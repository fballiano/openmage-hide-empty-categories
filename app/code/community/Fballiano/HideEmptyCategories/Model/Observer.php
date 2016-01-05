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

class Fballiano_HideEmptyCategories_Model_Observer extends Mage_Core_Model_Abstract
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
        $collection->addAttributeToSelect("is_anchor");
    }

    public function catalogCategoryCollectionLoadAfter(Varien_Event_Observer $observer)
    {
        $this->_removeHiddenCollectionItems($observer->getEvent()->getCategoryCollection());
    }

    /**
     * Remove hidden items from a product or category collection
     *
     * @param Mage_Eav_Model_Entity_Collection_Abstract|Mage_Core_Model_Mysql4_Collection_Abstract $collection
     */
    protected function _removeHiddenCollectionItems($collection)
    {
        $minimum_items = Mage::getStoreConfig('hideemptycategories_options/hideemptycategories_group/hideemptycategories_input',Mage::app()->getStore());
        $helper = new Fballiano_HideEmptyCategories_Helper_Data();
        $categories_products = $helper->getNotSellableCategories($minimum_items);
        $categories_to_hide = array_map(function ($v, $k) { return $v['category_id']; }, $categories_products, array_keys($categories_products));
        // Loop through each category or product
        foreach ($collection as $key => $item) {
            // If it is a category
            if ($item->getEntityTypeId() == 3) {
                if ($item->getDisplayMode() == "PAGE") continue;
                if (strlen($item->getChildren()) > 0) continue;
                if (in_array($key,$categories_to_hide)) {
                    $collection->removeItemByKey($key);
                }

            }
        }
    }
}