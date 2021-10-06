<?php

/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage Helper
 */
class Mylib_Helper_View_CityLocation extends Zend_View_Helper_Abstract
{
    /**
     * Check user favorite vendors
     * @return extension
     */
    
    public function CityLocation($cid,$limit)
    {	
        if(!empty($cid) && !empty($limit)){
			$this->_cityResource = new Application_Model_DbTable_Search();
			$locationvalue = $this->_cityResource->displaycitylocation($cid,$limit);
			if(count($locationvalue) > 0){
				return $locationvalue;
			} else {
				return false;
			}
		}
        
    }

}