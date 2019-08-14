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

class Morningtime_TextfieldLayeredNav_Model_Eav_Entity_Attribute extends Mage_Eav_Model_Entity_Attribute
{

    public function usesSource()
    {
    	/* MT start
        return $this->getFrontendInput()==='select' || $this->getFrontendInput()==='multiselect';
        */
        return $this->getFrontendInput()==='select' || $this->getFrontendInput()==='multiselect' || $this->getFrontendInput()==='textfilter';
		/* MT end */
    }

}
