<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * Adds the possiblity to list a specific category in the frontend
 *
 * @category  Fballiano
 * @package   Fballiano_HideEmptyCategories
 * @author    Fabrizio Balliano
 * @author    Andreas Emer <emer AT mothership.de>
 * @copyright Copyright (c) 2014 Mothership GmbH
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.mothership.de/
 */

class Fballiano_HideEmptyCategories_Model_Catalog_Resource_Category_Flat extends
    Mage_Catalog_Model_Resource_Category_Flat
{
    protected function _loadNodes($parentNode = null, $recursionLevel = 0, $storeId = 0)
    {
        $nodes = parent::_loadNodes($parentNode, $recursionLevel, $storeId);

        foreach ($nodes as $node) {
            $category = Mage::getModel('catalog/category')->load($node->getId());

            if ($category->getDisplayMode() == Mage_Catalog_Model_Category::DM_PAGE) continue;
            if ($node->getChildrenCategories()) continue;
            if ($node->getProductCount()) continue;
            unset($nodes[$node->getId()]);
        }
        return $nodes;
    }
}
