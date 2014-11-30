<?php
class Fballiano_HideEmptyCategories_Model_Observer
{
    public function catalogCategoryCollectionLoadAfter(Varien_Event_Observer $observer)
    {
    	if (Mage::app()->getStore()->isAdmin()) return;
    	
        $collection = $observer->getEvent()->getCategoryCollection();
        foreach ($collection as $key => $item) {
            if ($item->getEntityTypeId() == 3) {
                if ($item->getDisplayMode() == "PAGE") continue;
                if ($item->getChildrenCount()) continue;
                if ($item->getProductCount()) continue;
                $collection->removeItemByKey($key);
            }
        }
    }
}
