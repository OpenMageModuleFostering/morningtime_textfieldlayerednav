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

class Morningtime_TextfieldLayeredNav_Model_Catalog_Product extends Mage_Catalog_Model_Product
{
/**
     * Create duplicate
     *
     * @return Mage_Catalog_Model_Product
     */
    public function duplicate()
    {
        $this->getWebsiteIds();
        $this->getCategoryIds();

        $newProduct = Mage::getModel('catalog/product')
			->setData($this->getData())
            ->setIsDuplicate(true)
            ->setOriginalId($this->getId())
            ->setSku(null)
            ->setStatus(Mage_Catalog_Model_Product_Status::STATUS_DISABLED)
            ->setCreatedAt(null)
            ->setUpdatedAt(null)
            ->setId(null)
            ->setStoreId(Mage::app()->getStore()->getId());

		// MT: override for textfilter
		$data = $this->getData();
		$db = Mage::getSingleton('core/resource')->getConnection('core_write');
		$eav_attribute = Mage::getSingleton('core/resource')->getTableName('eav_attribute');
		$eav_attribute_option = Mage::getSingleton('core/resource')->getTableName('eav_attribute_option');
		$eav_attribute_option_value = Mage::getSingleton('core/resource')->getTableName('eav_attribute_option_value');
		foreach ($this->getData() as $attr) {
			$result = $db->query('select a.attribute_code, v.value from '.$eav_attribute.' a, '.$eav_attribute_option_value.' v, '.$eav_attribute_option.' o where a.frontend_input = "textfilter" and a.attribute_id = o.attribute_id and o.option_id = v.option_id and v.store_id = '.Mage::app()->getStore()->getId());
			while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
				$setVar = 'set'.ucfirst($row['attribute_code']);
				$newProduct->$setVar($row['value']);
			}
		}
		// MT: end

        Mage::dispatchEvent('catalog_model_product_duplicate', array('current_product'=>$this, 'new_product'=>$newProduct));

        /* @var $newProduct Mage_Catalog_Model_Product */

//        $newOptionsArray = array();
//        $newProduct->setCanSaveCustomOptions(true);
//        foreach ($this->getOptions() as $_option) {
//            /* @var $_option Mage_Catalog_Model_Product_Option */
//            $newOptionsArray[] = $_option->prepareOptionForDuplicate();
//        }
//        $newProduct->setProductOptions($newOptionsArray);

        /* Prepare Related*/
        $data = array();
        $this->getLinkInstance()->useRelatedLinks();
        $attributes = array();
        foreach ($this->getLinkInstance()->getAttributes() as $_attribute) {
            if (isset($_attribute['code'])) {
                $attributes[]=$_attribute['code'];
            }
        }
        foreach ($this->getRelatedLinkCollection() as $_link) {
            $data[$_link->getLinkedProductId()] = $_link->toArray($attributes);
        }
        $newProduct->setRelatedLinkData($data);

        /* Prepare UpSell*/
        $data = array();
        $this->getLinkInstance()->useUpSellLinks();
        $attributes = array();
        foreach ($this->getLinkInstance()->getAttributes() as $_attribute) {
            if (isset($_attribute['code'])) {
                $attributes[]=$_attribute['code'];
            }
        }
        foreach ($this->getUpSellLinkCollection() as $_link) {
            $data[$_link->getLinkedProductId()] = $_link->toArray($attributes);
        }
        $newProduct->setUpSellLinkData($data);

        /* Prepare Cross Sell */
        $data = array();
        $this->getLinkInstance()->useCrossSellLinks();
        $attributes = array();
        foreach ($this->getLinkInstance()->getAttributes() as $_attribute) {
            if (isset($_attribute['code'])) {
                $attributes[]=$_attribute['code'];
            }
        }
        foreach ($this->getCrossSellLinkCollection() as $_link) {
            $data[$_link->getLinkedProductId()] = $_link->toArray($attributes);
        }
        $newProduct->setCrossSellLinkData($data);

        /* Prepare Grouped */
        $data = array();
        $this->getLinkInstance()->useGroupedLinks();
        $attributes = array();
        foreach ($this->getLinkInstance()->getAttributes() as $_attribute) {
            if (isset($_attribute['code'])) {
                $attributes[]=$_attribute['code'];
            }
        }
        foreach ($this->getGroupedLinkCollection() as $_link) {
            $data[$_link->getLinkedProductId()] = $_link->toArray($attributes);
        }
        $newProduct->setGroupedLinkData($data);

        $newProduct->save();

        $this->getOptionInstance()->duplicate($this->getId(), $newProduct->getId());
        $this->getResource()->duplicate($this->getId(), $newProduct->getId());

        // TODO - duplicate product on all stores of the websites it is associated with
        /*if ($storeIds = $this->getWebsiteIds()) {
            foreach ($storeIds as $storeId) {
                $this->setStoreId($storeId)
                   ->load($this->getId());

                $newProduct->setData($this->getData())
                    ->setSku(null)
                    ->setStatus(Mage_Catalog_Model_Product_Status::STATUS_DISABLED)
                    ->setId($newId)
                    ->save();
            }
        }*/
        return $newProduct;
    }
}
