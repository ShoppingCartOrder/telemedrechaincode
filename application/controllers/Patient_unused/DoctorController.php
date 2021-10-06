<?php

class Patient_DoctorController extends Mylib_Controller_PatientbaseController {

    //protected $_dashboardResource;

    public function init() {
        parent::init();
        $this->_doctorResource = new Application_Model_DbTable_Doctors();
        $this->_userResource = new Application_Model_DbTable_Users();
        $this->_departmentResource = new Application_Model_DbTable_Departments();
    }

  

    public function viewprofileAction() {

        $request = $this->getRequest();

        if ($request->getParam('s') == 1) {
            $this->view->savemsg = 1;
        }
        require_once 'My/Acl.php';
        $session = new Zend_Session_Namespace('mydoctor');
        $this->view->userAccountInfo = $this->_dashboardResource->fetchAdminUserInfo($session->loginId);
        $roleResourceObj = new Application_Model_DbTable_Role();
        $this->view->roleData = $roleResourceObj->fetchRoleData($this->view->userAccountInfo['0']['role']);
    }
    
     public function getdoctorAction() {
         $request = $this->getRequest();
         $dprtId = $request->getParam('dprtId');
         $doctorsArr = $this->_doctorResource->getDoctorByDepartmentId($dprtId);
         //dd($doctorsArr);
         echo json_encode($doctorsArr);
         exit;
         //$id = $this->getRequest()->getParam('id');
     }


}

?>