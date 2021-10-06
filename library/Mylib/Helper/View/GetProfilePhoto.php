<?php

/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage Helper
 */
class Mylib_Helper_View_GetProfilePhoto extends Zend_View_Helper_Abstract
{
    /**
     * Check user favorite vendors
     * @return extension
     */
    
    public function GetProfilePhoto($id)
    {	
        try{ 
        if(isset($id) && !empty($id)){
			$this->_profileResource = new Application_Model_DbTable_Profile();
			$profilephoto = $this->_profileResource->getprofilePhoto($id);
			return $profilephoto;
			
		}
        }catch(Exception $e){            
            echo 'Caught exception: ',$e->getMessage(),'\n';
        } 
    }

}