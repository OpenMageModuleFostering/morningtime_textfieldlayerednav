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

class Morningtime_TextfieldLayeredNav_Model_Catalog_Resource_Eav_Mysql4_Product extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product
{

    /**
     * Save object collected data
     *
     * @param   array $saveData array('newObject', 'entityRow', 'insert', 'update', 'delete')
     * @return  Mage_Eav_Model_Entity_Abstract
     */
    protected function _processSaveData($saveData)
    {
        extract($saveData);
        $insertEntity   = true;
        $entityIdField  = $this->getEntityIdField();
        $entityId       = $newObject->getId();
        $condition      = $this->_getWriteAdapter()->quoteInto("$entityIdField=?", $entityId);

        if (!empty($entityId)) {
            $select = $this->_getWriteAdapter()->select()
                ->from($this->getEntityTable(), $entityIdField)
                ->where($condition);
            if ($this->_getWriteAdapter()->fetchOne($select)) {
                $insertEntity = false;
            }
        }

        /**
         * Process base row
         */
        if ($insertEntity) {
            $this->_getWriteAdapter()->insert($this->getEntityTable(), $entityRow);
            $entityId = $this->_getWriteAdapter()->lastInsertId();
            $newObject->setId($entityId);
        } else {
            $this->_getWriteAdapter()->update($this->getEntityTable(), $entityRow, $condition);
        }

        /**
         * insert attribute values
         */
        if (!empty($insert)) {
            foreach ($insert as $attrId=>$value) {
                $attribute = $this->getAttribute($attrId);
				
				/* MT start */				
				if ($attribute->frontend_input == 'textfilter') {
					$value = $this->_mtGetValue($attribute, $value);
				}
				/* MT end */
				
                $this->_insertAttribute($newObject, $attribute, $value);
            }
        }

        /**
         * update attribute values
         */
        if (!empty($update)) {
            foreach ($update as $attrId=>$v) {
                $attribute = $this->getAttribute($attrId);
				
				/* MT start */				
				if ($attribute->frontend_input == 'textfilter') {
					$v['value'] = $this->_mtGetValue($attribute, $v['value']);
				}
				/* MT end */
				
                $this->_updateAttribute($newObject, $attribute, $v['value_id'], $v['value']);
            }
        }

        /**
         * delete empty attribute values
         */
        if (!empty($delete)) {
            foreach ($delete as $table=>$values) {
                $this->_deleteAttributes($newObject, $table, $values);
            }
        }

        return $this;
    }
	
    protected function _mtGetValue($attribute, $value) 
	{				
		
		$value = addslashes($value);
									
		$writeFilter 	  			= Mage::getSingleton('core/resource')->getConnection('core_write');
		$eav_attribute	  			= Mage::getSingleton('core/resource')->getTableName('eav_attribute');
		$eav_attribute_option_value = Mage::getSingleton('core/resource')->getTableName('eav_attribute_option_value');
		$eav_attribute_option       = Mage::getSingleton('core/resource')->getTableName('eav_attribute_option');
		$textFilterResult 			= $writeFilter->query("select o.option_id from ".$eav_attribute." a, ".$eav_attribute_option_value." v, ".$eav_attribute_option." o
										where a.attribute_id='".$attribute->attribute_id."'
										and a.attribute_id=o.attribute_id
										and o.option_id=v.option_id
										and v.value='".$value."'");					
			
		$filterOptionId = 0; $i=0;
		while ($rowFilter = $textFilterResult->fetch()) {
			$filterOptionId=$rowFilter['option_id']; 
			$i++;
		}

		if ($i==0) { // new option: add & return option_id as $filterOptionId

			$stores = Mage::getModel('core/store')
		                ->getResourceCollection()
		                ->setLoadDefault(true)
		                ->load();

			$write = $this->_getWriteAdapter();
			$data = array(
                    'attribute_id'  => $attribute->attribute_id,
                    'sort_order'    => 0,
                    );
            $write->insert($eav_attribute_option, $data);
												
			$filterOptionId = $write->lastInsertId();
								
            foreach ($stores as $store) {
                     $data = array(
                             'option_id' => $filterOptionId,
                             'store_id'  => $store->getId(),
                             'value'     => $value,
                            );
                     $write->insert($eav_attribute_option_value, $data);
	               }

		}
														
		// set option ID
		return $filterOptionId;					
	}

}
