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

class Morningtime_TextfieldLayeredNav_Model_CatalogIndex_Indexer_Eav extends Mage_CatalogIndex_Model_Indexer_Eav
{

    protected function _isAttributeIndexable(Mage_Eav_Model_Entity_Attribute_Abstract $attribute)
    {
        if ($attribute->getIsFilterable() == 0 && $attribute->getIsVisibleInAdvancedSearch() == 0) {
            return false;
        }
		/* MT start 
        if ($attribute->getFrontendInput() != 'select' && $attribute->getFrontendInput() != 'multiselect') {
        */
        if ($attribute->getFrontendInput() != 'select' && $attribute->getFrontendInput() != 'textfilter' && $attribute->getFrontendInput() != 'multiselect') {
		/* MT end */
            return false;
        }

        return true;
    }

    protected function _getIndexableAttributeConditions()
    {
    	/* MT start 
        $conditions = "frontend_input IN ('select', 'multiselect') AND (is_filterable IN (1, 2) OR is_visible_in_advanced_search = 1)";
        */
        $conditions = "frontend_input IN ('select', 'textfilter', 'multiselect') AND (is_filterable IN (1, 2) OR is_visible_in_advanced_search = 1)";
		/* MT end */
        return $conditions;

        $conditions = array();
		/* MT start 
        $conditions['frontend_input'] = array('select', 'multiselect');
        */
        $conditions['frontend_input'] = array('select', 'textfilter', 'multiselect');
		/* MT end */
        $conditions['or']['is_filterable'] = array(1, 2);
        $conditions['or']['is_visible_in_advanced_search'] = 1;
    }
}
