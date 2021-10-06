<?php

/**
 * This is Mylib_Controller_BaseController class. This class will
 * execute all the request to setup the application  .
 * @author Tech Lead
 * @package Mylib_Controller_BaseController
 * @subpackage Zend_Controller_Action
 */
class Mylib_Controller_NursebaseController extends Mylib_Controller_BaseController {

    /**
     * This method is used to initialize the application 
     * Created By : Tech Lead
     * Date : 19 Dec,2019
     * @param void
     * @return void
     */
    public function init() {

        $session = $this->mynurseNamespace = new Zend_Session_Namespace('mynurse');

        //dd($this->doctorNamespace->doctorDetails);
        $this->view->nurseUsers = $session->nurseUsers;
        $this->view->loginId = $this->loginId = $session->loginId;
        $this->view->nurseId = $this->nurseId = $session->nurse_id;
        //dd($session->doctor_id);
        parent::init();


        $cntrlr = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
        $action = Zend_Controller_Front::getInstance()->getRequest()->getActionName();

        if (($cntrlr != 'Nurse_Index') && (($action != 'index') || ($action != 'login'))) {

            if (!$session->loginId) {
                $this->_redirect(NURSE_BASE_URL . 'login/');
            }
        }
    }

}

?>