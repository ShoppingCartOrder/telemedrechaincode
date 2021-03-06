<?php

/**
 * This is Mylib_Controller_BaseController class. This class will
 * execute all the request to setup the application  .
 * @author Tech Lead
 * @package Mylib_Controller_BaseController
 * @subpackage Zend_Controller_Action
 */

class Mylib_Controller_HospitalbaseController extends Mylib_Controller_BaseController
{

    /**
     * This method is used to initialize the application 
     * Created By : Tech Lead
     * Date : 19 Dec,2013
     * @param void
     * @return void
     */
    public function init(){
         /*Added by Umesh 15 - 5  14*/
		 $session = $this->hospitalNamespace = new Zend_Session_Namespace('myhospital');
                 //dd($session->storage);
		 $this->view->hospitalUsers = $session->hospitalUsers;
                 $this->view->loginId = $session->loginId;
                 $this->view->hospitaluserId = $this->hospitaluserId = $session->loginId;
                 //dd($session->doctor_id);
         parent::init();         
         //$this->allCities();
         //$this->allcity = $this->view->allcity = $this->allAvailablecities; 
         //$this->jsCatCityStrFilter();
         //$this->citiesDropDown();
         //$this->activeCitiesDropDown();
         
         //$this->allCitiesDropDown();
         
          $cntrlr = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
          $action = Zend_Controller_Front::getInstance()->getRequest()->getActionName();

        if(($cntrlr != 'Hospital_Index') && (($action != 'index')||($action != 'login'))){

               if(!$session->loginId){
                       $this->_redirect(HOSPITAL_BASE_URL.'login/');
               }
        }
    }
    

    
    
}

?>