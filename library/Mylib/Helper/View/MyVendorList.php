<?php

/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage Helper
 */
class Mylib_Helper_View_MyVendorList extends Zend_View_Helper_Abstract
{
    /**
     * Check user favorite vendors
     * @return extension
     */
    
    public function MyVendorList($uid,$vid)
    {
        if(!empty($uid) && !empty($vid)){
			$this->_searchResource = new Application_Model_DbTable_Search();
			$params['fields']['main'] = array('id');
            $params['condition']['vendor_id'] = $vid;
            $params['condition']['user_id'] = $uid;
			$listCount = $this->_searchResource->myvendorlist($params);
			if(count($listCount) > 0){
				return true;
			} else {
				return false;
			}
		}
        
    }
    
    /**
     * Check vendors portfolio
     * @return images
     */
    
    public function VendorsPortfolio($vid,$limit)
    {
    	if(!empty($vid)){
    		$this->_detailResource = new Application_Model_DbTable_Detail();
    		$portfolioCount = $this->_detailResource->get_portfolio($vid,$limit);
    		if(count($portfolioCount) > 0){
    			return $portfolioCount;
    		} else {
    			return false;
    		}
    	}
    
    }

}