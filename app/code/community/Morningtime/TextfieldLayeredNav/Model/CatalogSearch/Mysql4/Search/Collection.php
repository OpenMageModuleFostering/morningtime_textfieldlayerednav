<?php
/**
 * Morningtime TextfieldLayeredNav extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Morningtime
 * @package    Morningtime_TextfieldLayeredNav
 * @copyright  Copyright (c) 2009 Morningtime Internet, http://www.morningtime.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Morningtime_TextfieldLayeredNav_Model_CatalogSearch_Mysql4_Search_Collection
    extends Mage_CatalogSearch_Model_Mysql4_Search_Collection
{

    protected function _isAttributeTextAndSearchable($attribute)
    {
    	/* MT start
        if (($attribute->getIsSearchable() && !in_array($attribute->getFrontendInput(), array('select', 'multiselect')))
        */
        if (($attribute->getIsSearchable() && !in_array($attribute->getFrontendInput(), array('select', 'textfilter', 'multiselect')))
		/* MT end */
            && (in_array($attribute->getBackendType(), array('varchar', 'text')) || $attribute->getBackendType() == 'static')) {
            return true;
        }
        return false;
    }

    protected function _hasAttributeOptionsAndSearchable($attribute)
    {
    	/* MT start
        if ($attribute->getIsSearchable() && in_array($attribute->getFrontendInput(), array('select', 'multiselect'))) {
        */
        if ($attribute->getIsSearchable() && in_array($attribute->getFrontendInput(), array('select', 'textfilter', 'multiselect'))) {
        /* MT end */
            return true;
        }

        return false;
    }
	
    /**
     * Retrieve SQL for search entities by option
     *
     * @param unknown_type $query
     * @return string
     */
    protected function _getSearchInOptionSql($query)
    {
        $attributeIds    = array();
        $attributeTables = array();
        $storeId = (int)$this->getStoreId();

        /**
         * Collect attributes with options
         */
        foreach ($this->_getAttributesCollection() as $attribute) {
            if ($this->_hasAttributeOptionsAndSearchable($attribute)) {
                $attributeTables[$attribute->getFrontendInput()] = $attribute->getBackend()->getTable();
                $attributeIds[] = $attribute->getId();
            }
        }
        if (empty($attributeIds)) {
            return false;
        }

        $resource = Mage::getSingleton('core/resource');
        $optionTable      = $resource->getTableName('eav/attribute_option');
        $optionValueTable = $resource->getTableName('eav/attribute_option_value');
        $attributesTable  = $resource->getTableName('eav/attribute');

        /**
         * Select option Ids
         */
        $select = $this->getConnection()->select()
            ->from(array('default'=>$optionValueTable), array('option_id','option.attribute_id', 'store_id'=>'IFNULL(store.store_id, default.store_id)', 'a.frontend_input'))
            ->joinLeft(array('store'=>$optionValueTable),
                $this->getConnection()->quoteInto('store.option_id=default.option_id AND store.store_id=?', $storeId),
                array())
            ->join(array('option'=>$optionTable),
                'option.option_id=default.option_id',
                array())
            ->join(array('a' => $attributesTable), 'option.attribute_id=a.attribute_id', array())
            ->where('default.store_id=0')
            ->where('option.attribute_id IN (?)', $attributeIds)
            ->where('IFNULL(store.value, default.value) LIKE :search_query');
        $options = $this->getConnection()->fetchAll($select, array('search_query'=>$this->_searchQuery));
        if (empty($options)) {
            return false;
        }

        // build selects of entity ids for specified options ids by frontend input
        $select = array();
        foreach (array(
            'select'      => 'value = %d',
			/* MT start */
            'textfilter'      => 'value = %d',
			/* MT end */
            'multiselect' => 'FIND_IN_SET(%d, value)')
            as $frontendInput => $condition) {
            if (isset($attributeTables[$frontendInput])) {
                $where = array();
                foreach ($options as $option) {
                    if ($frontendInput === $option['frontend_input']) {
                        $where[] = sprintf("attribute_id=%d AND store_id=%d AND {$condition}", $option['attribute_id'], $option['store_id'], $option['option_id']);
                    }
                }
                if ($where) {
                    $select[$frontendInput] = (string)$this->getConnection()->select()
                        ->from($attributeTables[$frontendInput], 'entity_id')
                        ->where(implode(' OR ', $where));
                }
            }
        }

        // search in catalogindex for products as part of configurable/grouped/bundle products (current store)
        $where = array();
        foreach ($options as $option) {
            $where[] = sprintf('attribute_id=%d AND value=%d', $option['attribute_id'], $option['option_id']);
        }
        if ($where) {
            $select[] = (string)$this->getConnection()->select()
                ->from($resource->getTableName('catalogindex/eav'), 'entity_id')
                ->where(implode(' OR ', $where))
                ->where("store_id={$storeId}");
        }

        return implode(' UNION ', $select);
    }

}
