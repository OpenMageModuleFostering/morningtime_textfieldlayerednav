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

class Morningtime_TextfieldLayeredNav_Block_Catalog_Product_Compare_List extends Mage_Catalog_Block_Product_Compare_List
{

    /**
     * Retrieve Product Attribute Value
     *
     * @param Mage_Catalog_Model_Product $product
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @return string
     */
    public function getProductAttributeValue($product, $attribute)
    {
        if (!$product->hasData($attribute->getAttributeCode())) {
            return '&nbsp;';
        }

		/* MT start 
        if ($attribute->getSourceModel() || in_array($attribute->getFrontendInput(), array('select','boolean','multiselect'))) {
        */ 
        if ($attribute->getSourceModel() || in_array($attribute->getFrontendInput(), array('select','textfilter','boolean','multiselect'))) {
       	/* MT end */
            //$value = $attribute->getSource()->getOptionText($product->getData($attribute->getAttributeCode()));
            $value = $attribute->getFrontend()->getValue($product);
        }
        else {
            $value = $product->getData($attribute->getAttributeCode());
        }
        return $value ? $value : '&nbsp;';
    }

}
