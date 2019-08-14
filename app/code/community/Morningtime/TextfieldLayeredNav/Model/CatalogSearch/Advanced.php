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

class Morningtime_TextfieldLayeredNav_Model_CatalogSearch_Advanced extends Mage_CatalogSearch_Model_Advanced
{

    /**
     * Add data about search criteria to object state
     *
     * @param   Mage_Eav_Model_Entity_Attribute $attribute
     * @param   mixed $value
     * @return  Mage_CatalogSearch_Model_Advanced
     */
    protected function _addSearchCriteria($attribute, $value)
    {
        $name = $attribute->getFrontend()->getLabel();

        if (is_array($value) && (isset($value['from']) || isset($value['to']))){
            if (isset($value['currency'])) {
                $currencyModel = Mage::getModel('directory/currency')->load($value['currency']);
                $from = $currencyModel->format($value['from'], array(), false);
                $to = $currencyModel->format($value['to'], array(), false);
            } else {
                $currencyModel = null;
            }

            if (strlen($value['from']) > 0 && strlen($value['to']) > 0) {
                // -
                $value = sprintf('%s - %s', ($currencyModel ? $from : $value['from']), ($currencyModel ? $to : $value['to']));
            } elseif (strlen($value['from']) > 0) {
                // and more
                $value = Mage::helper('catalogsearch')->__('%s and greater', ($currencyModel ? $from : $value['from']));
            } elseif (strlen($value['to']) > 0) {
                // to
                $value = Mage::helper('catalogsearch')->__('up to %s', ($currencyModel ? $to : $value['to']));
            }
        }

		/* MT start 
        if (($attribute->getFrontendInput() == 'select' || $attribute->getFrontendInput() == 'multiselect') && is_array($value)) {
        */
        if (($attribute->getFrontendInput() == 'select' || $attribute->getFrontendInput() == 'textfilter' || $attribute->getFrontendInput() == 'multiselect') && is_array($value)) {
        /* MT end */
            foreach ($value as $k=>$v){
                $value[$k] = $attribute->getSource()->getOptionText($v);

                if (is_array($value[$k]))
                    $value[$k] = $value[$k]['label'];
            }
            $value = implode(', ', $value);
		/* MT start
        } else if ($attribute->getFrontendInput() == 'select' || $attribute->getFrontendInput() == 'multiselect') {
		*/
        } else if ($attribute->getFrontendInput() == 'select' || $attribute->getFrontendInput() == 'textfilter' || $attribute->getFrontendInput() == 'multiselect') {
        /* MT end */
            $value = $attribute->getSource()->getOptionText($value);
            if (is_array($value))
                $value = $value['label'];
        } else if ($attribute->getFrontendInput() == 'boolean') {
            $value = $value == 1
                ? Mage::helper('catalogsearch')->__('Yes')
                : Mage::helper('catalogsearch')->__('No');
        }

		/* MT Start: different overload */
        $searchCriterias = $this->_searchCriterias;
        $searchCriterias[] = array('name'=>$name, 'value'=>$value);
        return $searchCriterias;
		/* MT END */
    }

}
