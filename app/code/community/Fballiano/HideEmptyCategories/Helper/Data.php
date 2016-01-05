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
class Fballiano_HideEmptyCategories_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Get categories_id and number of sellable products having less than $minimumItemsNumber
     * @param int $minimumItemsNumber
     * @param Mage_Catalog_Model_Category $category to filter for
     * @return array representing category_id and numbers of products
     */
    public function getNotSellableCategories($minimumItemsNumber = 1, $category = null)
    {
        $query = "select ccp.category_id,count(csi.product_id) as products_count from `catalog_product_super_link` cpsl
                    JOIN catalog_category_product ccp ON cpsl.`parent_id`=ccp.product_id
                    JOIN cataloginventory_stock_item csi ON csi.`product_id` = cpsl.product_id";
        if (!is_null($category)) {
            $query .= " WHERE ccp.category_id=" . $category->getEntityId();
        }

        $query .= " GROUP BY ccp.category_id HAVING products_count<" . $minimumItemsNumber;

        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $results = $readConnection->fetchAll($query);

        return $results;
    }

    /**
     * Return true if category has products
     * @param $category_id
     * @param int $minimumItemsNumber
     * @return bool
     */
    public function _hasProducts($category_id, $minimumItemsNumber = 1)
    {
        $products = Mage::getModel('catalog/category')->load($category_id)
            ->getProductCollection()
            ->addAttributeToSelect('entity_id')
            ->addAttributeToFilter('status', 1)
            ->addAttributeToFilter('visibility', 4);

        // Add filter to exclude products that are not on stock
        Mage::getModel('cataloginventory/stock_status')->addIsInStockFilterToCollection($products);

        return ($products->count() > $minimumItemsNumber) ? true : false;

    }
}