<?php

class Admin_LeaveApplicationController extends Mylib_Controller_AdminbaseController {

    //protected $_dashboardResource;

    public function init() {
        parent::init();


    }

    /**
     * This function is used to show all function in dashboard 
     * Created By :  Umesh
     * Date : 20 May,2014
     * @param void
     * @return void
     */
    public function indexAction() {
        
        $request = $this->getRequest();
        global $preDate;
        global $nowDate;
        // echo $preDate; die;
        if ($request->getParam('s') == 1) {
            $this->view->savemsg = 1;
        }
        $session = new Zend_Session_Namespace('my');
        
        $this->view->lastlogin = $session->lastlogin;
        $this->view->loginIp = $session->loginip;
     
    }

    public function viewprofileAction() {

        $request = $this->getRequest();

        if ($request->getParam('s') == 1) {
            $this->view->savemsg = 1;
        }
        require_once 'My/Acl.php';
        $session = new Zend_Session_Namespace('my');
        $this->view->userAccountInfo = $this->_dashboardResource->fetchAdminUserInfo($session->loginId);
        $roleResourceObj = new Application_Model_DbTable_Role();
        $this->view->roleData = $roleResourceObj->fetchRoleData($this->view->userAccountInfo['0']['role']);
    }

    
 }

?>