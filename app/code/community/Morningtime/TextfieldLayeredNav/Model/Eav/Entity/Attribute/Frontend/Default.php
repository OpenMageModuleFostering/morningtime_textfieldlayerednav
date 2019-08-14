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

class Morningtime_TextfieldLayeredNav_Model_Eav_Entity_Attribute_Frontend_Default extends Mage_Eav_Model_Entity_Attribute_Frontend_Default
{

    public function getValue(Varien_Object $object)
    {
        $value = $object->getData($this->getAttribute()->getAttributeCode());
		/* MT start 
        if (in_array($this->getConfigField('input'), array('select','boolean'))) {
        */
        if (in_array($this->getConfigField('input'), array('select','textfilter','boolean'))) {
        /* MT end */
            $valueOption = $this->getOption($value);
            if (!$valueOption) {
                $opt = new Mage_Eav_Model_Entity_Attribute_Source_Boolean();
                if ($options = $opt->getAllOptions()) {
                    foreach ($options as $option) {
                        if ($option['value'] == $value) {
                            $valueOption = $option['label'];
                        }
                    }
                }
            }
            $value = $valueOption;
        }
        elseif ($this->getConfigField('input')=='multiselect') {
            $value = $this->getOption($value);
            if (is_array($value)) {
                $value = implode(', ', $value);
            }
        }
        return $value;
    }

}
