<?php

/**
 * This is Mylib_Controller_BaseController class. This class will
 * execute all the request to setup the application  .
 * @author Tech Lead
 * @package Mylib_Controller_BaseController
 * @subpackage Zend_Controller_Action
 */

class Mylib_Controller_LaboratorybaseController extends Mylib_Controller_BaseController
{

    /**
     * This method is used to initialize the application 
     * Created By : Tech Lead
     * Date : 19 Dec,2013
     * @param void
     * @return void
     */
    public function init(){
         
		 $session = $this->labNamespace = new Zend_Session_Namespace('mylaboratory');
                 //dd($session->loginId);
		 $this->view->laboratoryUsers = $session->laboratoryUsers;
                 $this->view->loginId= $this->loginId = $session->loginId;
                 $this->view->laboratoryuserId = $this->laboratoryuserId = $session->lab_id;
                 $this->view->lab_id = $this->lab_id = $session->lab_id;
                // dd($this->hospitaluserId);
         parent::init();         
         //$this->allCities();
         //$this->allcity = $this->view->allcity = $this->allAvailablecities; 
         //$this->jsCatCityStrFilter();
         //$this->citiesDropDown();
         //$this->activeCitiesDropDown();
         
         //$this->allCitiesDropDown();
         
          $cntrlr = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
          $action = Zend_Controller_Front::getInstance()->getRequest()->getActionName();

        if(($cntrlr != 'Laboratory_Index') && (($action != 'index')||($action != 'login'))){

               if(!$session->loginId){
                       $this->_redirect(LABORATORY_BASE_URL.'login/');
               }
        }
    }
    

    
    
}

?>