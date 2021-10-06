<?php

/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage Helper
 */
class Mylib_Helper_View_GetLocationName extends Zend_View_Helper_Abstract
{
    /**
     * Check user favorite vendors
     * @return extension
     */
    
    public function GetLocationName($lid)
    {	
        if(!empty($lid)){
			$this->_locationResource = new Application_Model_DbTable_Location();
			$listCount = $this->_locationResource->fetchLocationData($lid);
			if(count($listCount) > 0){
				return $listCount['name'];
			} else {
				return false;
			}
		}
        
    }

}