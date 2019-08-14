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

class Morningtime_TextfieldLayeredNav_Model_Catalog_Resource_Eav_Attribute extends Mage_Catalog_Model_Resource_Eav_Attribute
{

    /**
     * Retrieve source model
     *
     * @return Mage_Eav_Model_Entity_Attribute_Source_Abstract
     */
    public function getSourceModel()
    {
        $model = $this->getData('source_model');
        if (empty($model)) {
        	/* MT start 
            if ($this->getBackendType() == 'int' && $this->getFrontendInput() == 'select') {
            */
            if ($this->getBackendType() == 'int' && ($this->getFrontendInput() == 'select' || $this->getFrontendInput() == 'textfilter')) {
            /* MT end */
                return 'eav/entity_attribute_source_table';
            }
        }
        return $model;
    }

    /**
     * Check is allow for rule condition
     *
     * @return bool
     */
    public function isAllowedForRuleCondition()
    {
    	/* MT start 
        $allowedInputTypes = array('text', 'multiselect', 'textarea', 'date', 'datetime', 'select', 'boolean', 'price');
        */
        $allowedInputTypes = array('text', 'multiselect', 'textarea', 'date', 'datetime', 'select', 'textfilter', 'boolean', 'price');
		/* MT end */
        return $this->getIsVisible() && in_array($this->getFrontendInput(), $allowedInputTypes);
    }

}
