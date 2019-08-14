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

include_once("Mage/Adminhtml/controllers/Catalog/Product/AttributeController.php");
class Morningtime_TextfieldLayeredNav_Adminhtml_Catalog_Product_AttributeController extends Mage_Adminhtml_Catalog_Product_AttributeController
{

    public function editAction()
    {
        $id = $this->getRequest()->getParam('attribute_id');
        $model = Mage::getModel('catalog/entity_attribute');

        if ($id) {
            $model->load($id);

            if (! $model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('catalog')->__('This attribute no longer exists'));
                $this->_redirect('*/*/');
                return;
            }

            // entity type check
            if ($model->getEntityTypeId() != $this->_entityTypeId) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('catalog')->__('You cannot edit this attribute'));
                $this->_redirect('*/*/');
                return;
            }
        }

        // set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getAttributeData(true);
        if (! empty($data)) {
            $model->setData($data);
        }

        Mage::register('entity_attribute', $model);

        $this->_initAction()
            ->_addBreadcrumb($id ? Mage::helper('catalog')->__('Edit Product Attribute') : Mage::helper('catalog')->__('New Product Attribute'), $id ? Mage::helper('catalog')->__('Edit Product Attribute') : Mage::helper('catalog')->__('New Product Attribute'))
            ->_addContent($this->getLayout()->createBlock('adminhtml/catalog_product_attribute_edit')->setData('action', $this->getUrl('*/catalog_product_attribute/save')))
            ->_addLeft($this->getLayout()->createBlock('adminhtml/catalog_product_attribute_edit_tabs'))
            ->_addJs(
                $this->getLayout()->createBlock('adminhtml/template')
                    ->setIsPopup((bool)$this->getRequest()->getParam('popup'))
					/* MT
                    ->setTemplate('catalog/product/attribute/js.phtml')
                    */
                    ->setTemplate('morningtime/textfieldlayerednav/catalog/product/attribute/js.phtml')
					/* MT: end */
            )
            ->renderLayout();
    }

}
