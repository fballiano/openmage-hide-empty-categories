<?php
class Fballiano_HideEmptyCategories_Model_Catalog_Resource_Category_Flat extends Mage_Catalog_Model_Resource_Category_Flat
{
    protected function _loadNodes($parentNode = null, $recursionLevel = 0, $storeId = 0)
    {
        $nodes = parent::_loadNodes($parentNode, $recursionLevel, $storeId);
        foreach ($nodes as $node) {
            if ($node->getChildrenCategories()) continue;
            if ($node->getProductCount()) continue;
            unset($nodes[$node->getId()]);
        }
        return $nodes;
    }
}
