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

class Morningtime_TextfieldLayeredNav_Model_Eav_Mysql4_Entity_Attribute extends Mage_Eav_Model_Mysql4_Entity_Attribute
{

    /**
     * Enter description here...
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Eav_Model_Mysql4_Entity_Attribute
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        $frontendLabel = $object->getFrontendLabel();
        if (is_array($frontendLabel)) {
            if (!isset($frontendLabel[0]) || is_null($frontendLabel[0]) || $frontendLabel[0]=='') {
                Mage::throwException(Mage::helper('eav')->__('Frontend label is not defined'));
            }
            $object->setFrontendLabel($frontendLabel[0]);

            if ($object->getData('modulePrefix')) {
                $str = $object->getData('modulePrefix') . Mage_Core_Model_Translate::SCOPE_SEPARATOR . $frontendLabel[0];
            }
            else {
                $str = $frontendLabel[0];
            }
            Mage::getModel('core/translate_string')
                ->setString($str)
                ->setTranslate($frontendLabel[0])
                ->setStoreTranslations($frontendLabel)
                ->save();
        }
        $applyTo = $object->getApplyTo();

        if (is_array($applyTo)) {
            $object->setApplyTo(implode(',', $applyTo));
        }

        /**
         * @todo need use default source model of entity type !!!
         */
        if (!$object->getId()) {
			/* MT start 
            if ($object->getFrontendInput()=='select') {
			*/
            if ($object->getFrontendInput()=='select' || $object->getFrontendInput()=='textfilter') {
			/* MT end */
                $object->setSourceModel('eav/entity_attribute_source_table');
            }
        }
		
		/* MT
        return parent::_beforeSave($object);
        */
    }
	
    /**
     * Enter description here...
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Eav_Model_Mysql4_Entity_Attribute
     */
    protected function _saveOption(Mage_Core_Model_Abstract $object)
    {
        $option = $object->getOption();
        if (is_array($option)) {
            $write = $this->_getWriteAdapter();
            $optionTable        = $this->getTable('attribute_option');
            $optionValueTable   = $this->getTable('attribute_option_value');
            $stores = Mage::getModel('core/store')
                ->getResourceCollection()
                ->setLoadDefault(true)
                ->load();

            if (isset($option['value'])) {
                $attributeDefaultValue = array();
                if (!is_array($object->getDefault())) {
                    $object->setDefault(array());
                }

                foreach ($option['value'] as $optionId => $values) {
                    $intOptionId = (int) $optionId;
                    if (!empty($option['delete'][$optionId])) {
                        if ($intOptionId) {
                            $condition = $write->quoteInto('option_id=?', $intOptionId);
                            $write->delete($optionTable, $condition);
                        }

                        continue;
                    }

                    if (!$intOptionId) {
                        $data = array(
                           'attribute_id'  => $object->getId(),
                           'sort_order'    => isset($option['order'][$optionId]) ? $option['order'][$optionId] : 0,
                        );
                        $write->insert($optionTable, $data);
                        $intOptionId = $write->lastInsertId();
                    }
                    else {
                        $data = array(
                           'sort_order'    => isset($option['order'][$optionId]) ? $option['order'][$optionId] : 0,
                        );
                        /* $write->update($optionTable, $data, $write->quoteInto('option_id=?', $intOptionId)); */
                    }

                    if (in_array($optionId, $object->getDefault())) {
                        if ($object->getFrontendInput() == 'multiselect') {
                            $attributeDefaultValue[] = $intOptionId;
						/* MT start 
                        } else if ($object->getFrontendInput() == 'select') {
						*/
                        } else if ($object->getFrontendInput() == 'select' || $object->getFrontendInput() == 'textfilter') {
						/* MT end */
                            $attributeDefaultValue = array($intOptionId);
                        }
                    }


                    // Default value
                    if (!isset($values[0])) {
                        Mage::throwException(Mage::helper('eav')->__('Default option value is not defined'));
                    }

                    $write->delete($optionValueTable, $write->quoteInto('option_id=?', $intOptionId));
                    foreach ($stores as $store) {
                        if (isset($values[$store->getId()]) && (!empty($values[$store->getId()]) || $values[$store->getId()] == "0")) {
                            $data = array(
                                'option_id' => $intOptionId,
                                'store_id'  => $store->getId(),
                                'value'     => $values[$store->getId()],
                            );
                            // $write->insert($optionValueTable, $data);
                        }
                    }
                }

                /*$write->update($this->getMainTable(), array(
                    'default_value' => implode(',', $attributeDefaultValue)
                ), $write->quoteInto($this->getIdFieldName() . '=?', $object->getId())); */
            }
        }
        return $this;
    }

}
