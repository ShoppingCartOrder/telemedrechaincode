<?php

/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage Helper
 */
class Mylib_Helper_View_TagRelated extends Zend_View_Helper_Abstract
{
    /**
     * Check user favorite vendors
     * @return extension
     */
    
    public function TagRelated($tagids)
    {
        if(!empty($tagids)){
			$this->_tagResource = new Application_Model_DbTable_Tag();
			$listCount = $this->_tagResource->getMultiTagDetails($tagids);
			if(count($listCount) > 0){
				return $listCount;
			} else {
				return false;
			}
		}
        
    }

}