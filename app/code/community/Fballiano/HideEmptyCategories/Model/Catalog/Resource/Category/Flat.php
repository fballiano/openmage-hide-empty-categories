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
class Fballiano_HideEmptyCategories_Model_Catalog_Resource_Category_Flat extends Mage_Catalog_Model_Resource_Category_Flat
{

    protected function _loadNodes($parentNode = null, $recursionLevel = 0, $storeId = 0, $onlyActive = true)
    {
        $nodes = parent::_loadNodes($parentNode, $recursionLevel, $storeId, $onlyActive);

        $category_collection = Mage::getResourceModel('catalog/category_collection');
        $category_collection->loadProductCount($nodes, true, true);

        $minimum_items = Mage::getStoreConfig('hideemptycategories_options/hideemptycategories_group/hideemptycategories_input', Mage::app()->getStore());

        //retrieve sellable products foreach category
        $helper = new Fballiano_HideEmptyCategories_Helper_Data();
        $categories_products = $helper->getNotSellableCategories($minimum_items);
        $categories_to_hide = array_map(function ($v, $k) {
            return $v['category_id'];
        }, $categories_products, array_keys($categories_products));
        foreach ($nodes as $node) {
            if ($node->getDisplayMode() == "PAGE") continue;
            if (strlen($node->getChildren()) > 0) continue;
            if (in_array($node->getId(), $categories_to_hide)) {
                unset($nodes[$node->getId()]);
            }
        }
        return $nodes;
    }

}