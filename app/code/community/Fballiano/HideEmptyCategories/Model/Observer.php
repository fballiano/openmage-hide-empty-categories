<?php
/**
 * FBalliano
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this Module to
 * newer versions in the future.
 *
 * @category   FBalliano
 * @package    Fballiano_HideEmptyCategories
 * @copyright  Copyright (c) 2014 Fabrizio Balliano (http://fabrizioballiano.it)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Fballiano_HideEmptyCategories_Model_Observer
{
    public function catalogCategoryFlatLoadnodesBefore(Varien_Event_Observer $observer)
    {
        $select = $observer->getEvent()->getSelect();
        $select->columns("display_mode");
    }

    public function catalogCategoryCollectionLoadBefore(Varien_Event_Observer $observer)
    {
        $collection = $observer->getEvent()->getCategoryCollection();
        $collection->addAttributeToSelect("display_mode");
    }

    public function catalogCategoryCollectionLoadAfter(Varien_Event_Observer $observer)
    {
        $collection = $observer->getEvent()->getCategoryCollection();
        $core_resource = Mage::getSingleton('core/resource');
        $category_product_index_table = $core_resource->getTableName('catalog/category_product_index');
        $db = $core_resource->getConnection('core_read');
        $category_product_count = $db->fetchPairs("SELECT category_id, COUNT(*) FROM $category_product_index_table WHERE visibility IN (2,4) AND store_id=? GROUP BY category_id", array(
            Mage::app()->getStore()->getId()
        ));

        foreach ($collection as $key => $item) {
            if ($item->getEntityTypeId() == 3) {
                if ($item->getDisplayMode() == "PAGE") continue;
                if (@$category_product_count[$item->getId()]) continue;
                $collection->removeItemByKey($key);
            }
        }
    }
}
