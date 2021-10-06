<?php

/**
 * This is Mylib_Controller_BaseController class. This class will
 * execute all the request to setup the application  .
 * @author Tech Lead
 * @package Mylib_Controller_BaseController
 * @subpackage Zend_Controller_Action
 */

class Mylib_Controller_AdminbaseController extends Mylib_Controller_BaseController
{

    
    public function init(){
         
		 $adminSession = $this->adminSession = new Zend_Session_Namespace('my');
		 $this->view->adminUsers = $adminSession->adminUsers;
                 $this->view->adminId = $adminSession->loginId;
         parent::init();         
         //$this->activeCitiesDropDown();
         
         //$this->allCitiesDropDown();
         
          $cntrlr = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
          $action = Zend_Controller_Front::getInstance()->getRequest()->getActionName();

        if(($cntrlr != 'Admin_Index') && (($action != 'index')||($action != 'login'))){

               if(!$adminSession->loginId){
                       $this->_redirect(ADMIN_BASE_URL.'login/');
               }
        }
    }
    
    protected function jsCatCityStrFilter() {
        
        $citiesStr = '';
        $citiesStr.= ':'.$this->frmMsg['ALL'].';';
        $allCitiesStr = '';
        $allCitiesStr.= ':'.$this->frmMsg['ALL'].';';
        foreach ($this->allcity as $key => $city) {
            $allCitiesStr.= $city['id'].':'.trim($city['name']).';';
            if($city['status'] == 1){
                $citiesStr.= $city['id'].':'.trim($city['name']).';';            
            }
            
        }
        $this->view->allCitiesStr = rtrim($allCitiesStr,';'); 
        $this->view->citiesStr = rtrim($citiesStr,';');        
        $categoriesStr = '';
        $categoriesStr.= ':'.$this->frmMsg['ALL'].';';
        foreach ($this->allcategory as $key => $category) {
            $categoriesStr.= $category['id'].':'.trim($category['name']).';';            
        }         
        $this->view->categoriesStr = rtrim($categoriesStr,';');                
    }
    
    protected function citiesDropDown(){
        $citiesArr = array();
        $allCitiesArr = array();
        foreach ($this->allcity as $key => $city) {
            $allCitiesArr[$city['id']]= trim($city['name']); 
            if($city['status'] == 1){
                $citiesArr[$city['id']]= trim($city['name']);  
            }
            
        }
        $this->allCitiesDropDown = $this->view->allCitiesDropDown = $allCitiesArr;
        $this->allcityDD = $this->view->allcityDD = $citiesArr;
    }
    
    protected function allCitiesDropDown(){
        $allCitiesArr = array();           
        foreach ($this->allAvailablecities as $key => $city) {
            $allCitiesArr[$city['id']]= trim($city['name']);            
        } 
    
        $this->allCitiesDropDown = $this->view->allCitiesDropDown = $allCitiesArr;
}
  protected function activeCitiesDropDown(){
        $citiesArr = array();           
        foreach ($this->allcity as $key => $city) {
            $citiesArr[$city['id']]= trim($city['name']);            
        }              
        $this->allAcitivecitiesDD = $this->view->allAcitivecitiesDD = $citiesArr;
    }
    
}

?>