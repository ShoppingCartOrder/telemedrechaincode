<?php

class Admin_StaffController extends Mylib_Controller_AdminbaseController {

    //protected $_dashboardResource;

    public function init() {
        parent::init();
        $this->_staffResource = new Application_Model_DbTable_StaffDetails();
        $this->_userResource = new Application_Model_DbTable_Users();
       // $this->_specialityResource = new Application_Model_DbTable_Speciality();
        global $status;
        $this->view->status = $status;
    }

    /**
     * This function is used to show all function in dashboard 
     * Created By :  Bidhan
     * Date : 20 May,2020
     * @param void
     * @return void
     */
    public function indexAction() {

       global $nowDateTime;
        global $monthArray;
        $request = $this->getRequest();
        global $noRecord;
        if ($request->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $data = $this->getRequest()->getPost();
            $params = $this->getSearchParams($data);
            
            
            $params['fields']['main'] = array('*');
            $params['sidx'] = 'id';
            $params['sord'] = 'DESC';
          
            
            $result = $this->_staffResource->allStaffs($params);
            
            $responce = new stdClass();
            $responce->page = $result['page'];
            $responce->total = $result['total'];
            $responce->records = $result['records'];


            foreach ($result['result'] as $k => $val) {
                $statusUpdate = '';
                $actionUpdate = '';
                $id = $val['id'];
                $responce->rows[$k]['id'] = $val['id'];

                $responce->rows[$k]['cell'] = array(
                    $val['id'],
                    $val['name'],
                    $val['contact_no'],
                   // $val['dob'],
                    $this->changeDateFormat($val['dob'], DATETIMEFORMAT, FRONT_DATE_FORMAT),
                    
                   // $val['speciality_name'],
                    $val['created_at']
                   
                );
            }
            echo $this->jsonEncode($responce);
            exit;
        }
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

    public function addeditAction() {
        global $status;

        $this->view->status = $status;
        $id = $this->getRequest()->getParam('id');
       // $this->view->dertData = $this->_specialityResource->fetchAllSpeciality();
        if (!empty($id)) {

            $patientData = $this->_staffResource->getStaffDetails($id);
            //dd($patientData);
            $formdata = array(
                'id' => $patientData['id'],
                'name' => $patientData['name'],
                'email' => $patientData['email'],
                'contact_no' => $patientData['contact_no'],
                'designation' => $patientData['designation'],
               'department' => $patientData['department'],
                'address' => $patientData['address'],
                'dob' => $patientData['dob'],
                'qualification' => $patientData['qualification'],
                'experience' => $patientData['experience'],
                'salary' => $patientData['salary'],
               
                'about' => $patientData['about']
            );

                    
            $this->view->formdata = $formdata;
            
        }
    }


    public function savestaffAction() {
        
        // global $status;
        $request = $this->getRequest();
        if ($data = $request->getPost()) {
            //dd($data);
            $params = $this->__inputPostData;
            //print_r($params); die;
            //$response = array('status' => 0, 'msg' => '');
            $this->validateStaffData($params, $this->errMsg);
            $inputData = new Zend_Filter_Input($this->filters, $this->validators);
            $inputData->setData($params);
            if ($inputData->isValid()) {


               // dd($params);
                if (!empty($params['id'])) {
                    
                    $staffData = array(
                                                       
                               'name' => $params['s_name'],
                                'email' => $params['s_email'],
                                'contact_no' => $params['contact_no'],
                                'address' => $params['s_add'],
                                'dob' => $params['dob'],
                                'qualification' => $params['qualification'],
                                'experience' => $params['experience'], 
                                'department' => $params['department'],                                 
                                'designation' => $params['designation'],                                 
                                'salary' => $params['salary'],
                                'about' => $params['about']
                            );
                    
                    $update = $this->_staffResource->updateStaffDetail($staffData,$params['id']);

                    if ($update) {
                        $this->_helper->FlashMessenger()->setNamespace('success')->addMessage('Staff record successfully updated.');
                         $this->_redirect(ADMIN_BASE_URL . 'staff/index');
                    } else {
                        $this->_helper->FlashMessenger()->setNamespace('error')->addMessage('Staff record not updated.');                  
            
                    }
                   
                } else { //Save new Patient
                    
                         
                            $staffData = array(
                                                     
                              'name' => $params['s_name'],
                                'email' => $params['s_email'],
                                'contact_no' => $params['contact_no'],
                                'address' => $params['s_add'],
                                'dob' => $params['dob'],
                                'qualification' => $params['qualification'],
                                'experience' => $params['experience'], 
                                'department' => $params['department'],                                 
                                'designation' => $params['designation'],                                 
                                'salary' => $params['salary'],
                                'about' => $params['about']
                            );

                            $insertedId = $this->_staffResource->addStaff($staffData);
//dd($insertedId);
                            if ($insertedId) {

                                $this->_helper->FlashMessenger()->setNamespace('success')->addMessage('Staff record successfully saved.');
                            
                                 $this->_redirect(ADMIN_BASE_URL . 'staff/index');
                            } else {
                                $this->_helper->FlashMessenger()->setNamespace('error')->addMessage('Staff record is not inserted.Please try again.');                  
            
                               /// $this->view->msg = 'Patient record is not inserted.Please try again.';
                            }
                       
                   

                    $this->_redirect(ADMIN_BASE_URL . 'staff/addedit');
                }
              
                } else {

                $msg = $this->getValidatorErrors($inputData->getMessages(), 'admin');
                
                $this->_helper->FlashMessenger()->setNamespace('error')->addMessage($msg);                  
                
                if($params['id']){
                    $this->_redirect(ADMIN_BASE_URL . 'staff/addedit/id/'.$params['id']);
                }else{
                    $this->_redirect(ADMIN_BASE_URL . 'staff/addedit');
                }
                
                //$this->view->msg = $msg;
            }
        }
        
        die;

    }

  

    protected function validateStaffData($data, $errMsg) {

        if (isset($data['s_name'])) {
            $this->validators['s_name'] = array(
                'NotEmpty',
                'messages' => array('Please enter doctor name.'));
        }

        if (isset($data['contact_no'])) {
            $this->validators['contact_no'] = array(
                'NotEmpty',
                array('StringLength', 10),
                'messages' => array(
                    'Please enter mobile no.', 'Mobile no must be 10 digit'));
        }
        
         if (isset($data['address'])) {
            $this->validators['address'] = array(
                'NotEmpty',
                'messages' => 'Please enter address.');
        }
        
        if (isset($data['dob'])) {
            $this->validators['dob'] = array(
                'NotEmpty',
                'messages' => 'Please select dob name.');
        }
        
         if (isset($data['qualification'])) {
            $this->validators['qualification'] = array(
                'NotEmpty',
                'messages' => array('Please enter qualification.'));
        }
         if (isset($data['designation'])) {
            $this->validators['designation'] = array(
                'NotEmpty',
                'messages' => array('Please enter designation.'));
        }

        
        
        
    }
    
    
    
    
    
    public function deletenurseAction() {
        global $nowDateTime;
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $request = $this->getRequest();
        if ($request->getPost()) {
            $data = $this->__inputPostData;
            
            $userID = $this->_staffResource->getStaffLoginIdByStaffId($data['id']);
            
            //dd($userID);
            $params['status'] = 0;
            $params['updated_at'] = $nowDateTime;
            $deleteRole = $this->_staffResource->updateStaffDetail($params,$data['id']);
            $udata['status'] = 0;
            $udata['updated_at'] = $nowDateTime;
            $deleteUser = $this->_userResource->updateUserDetail($udata,$userID);
            
            
            echo $deleteRole;
        }
        die;
    }

}

?>