<?php

/**
 * This is Mylib_Controller_BaseController class. This class will
 * execute all the request to setup the application  .
 * @author Tech Lead
 * @package Mylib_Controller_BaseController
 * @subpackage Zend_Controller_Action
 */

class Mylib_Controller_PatientbaseController extends Mylib_Controller_BaseController
{

    /**
     * This method is used to initialize the application 
     * Created By : Tech Lead
     * Date : 19 Dec,2013
     * @param void
     * @return void
     */
    public function init(){
        
		 $patientNamespace = $this->patientNamespace  = new Zend_Session_Namespace('mypatient');
                 
		 $this->view->patientUsers =  $patientNamespace->patientUsers;
                 $this->view->loginId = $patientNamespace->loginId;
                 $this->view->patientId = $patientNamespace->patient_id;
                 $this->view->loggedInUserDetails = $this->loggedInUserDetails = $this->patientNamespace->storage;
                 
                 $this->view->loginEmailId = $this->loginEmailId = (isset($this->loggedInUserDetails->email)?$this->loggedInUserDetails->email:'');
                 
                 $this->view->loggedInPatientrDetails = $this->loggedInPatientrDetails = $patientNamespace->patient_details;
         parent::init();         
       
          $cntrlr = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
          $action = Zend_Controller_Front::getInstance()->getRequest()->getActionName();

        if(($cntrlr != 'Patient_Index') && (($action != 'index')||($action != 'login'))){

               if(!$patientNamespace->loginId){
                       $this->_redirect(PATIENT_BASE_URL.'login/');
               }
        }
    }
    

    
    
}

?>