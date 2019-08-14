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

class Morningtime_TextfieldLayeredNav_Model_Catalog_Entity_Attribute extends Mage_Catalog_Model_Entity_Attribute
{
    /**
     * Detect backend storage type using frontend input type
     *
     * @return string backend_type field value
     * @param string $type frontend_input field value
     */
    public function getBackendTypeByInput($type)
    {
        switch ($type) {
            case 'text':
            case 'gallery':
            case 'media_image':
            case 'multiselect':
                return 'varchar';

            case 'image':
            case 'textarea':
                return 'text';

            case 'date':
                return 'datetime';

            case 'select':
			/* MT start */
            case 'textfilter':
			/* MT end */
            case 'boolean':
                return 'int';


            case 'price':
                return 'decimal';
/*
            default:
                Mage::dispatchEvent('eav_attribute_get_backend_type_by_input', array('model'=>$this, 'type'=>$type));
                if ($this->hasBackendTypeByInput()) {
                    return $this->getData('backend_type_by_input');
                }
                Mage::throwException('Unknown frontend input type');
*/
        }
    }

    /**
     * Detect default value using frontend input type
     *
     * @return string default_value field value
     * @param string $type frontend_input field name
     */
    public function getDefaultValueByInput($type)
    {
        $field = '';
        switch ($type) {
            case 'select':
			/* MT start */
            case 'textfilter':
			/* MT end */
            case 'gallery':
            case 'media_image':
            case 'multiselect':
                return '';

            case 'text':
            case 'price':
            case 'image':
                $field = 'default_value_text';
                break;

            case 'textarea':
                $field = 'default_value_textarea';
                break;

            case 'date':
                $field = 'default_value_date';
                break;

            case 'boolean':
                $field = 'default_value_yesno';
                break;
/*
            default:
                Mage::dispatchEvent('eav_attribute_get_default_value_by_input', array('model'=>$this, 'type'=>$type));
                if ($this->hasBackendTypeByInput()) {
                    return $this->getData('backend_type_by_input');
                }
                Mage::throwException('Unknown frontend input type');
*/
        }

        return $field;
    }
}
