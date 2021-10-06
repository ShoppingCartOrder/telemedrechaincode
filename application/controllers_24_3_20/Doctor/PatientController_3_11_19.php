<?php

class Doctor_PatientController extends Mylib_Controller_DoctorbaseController {

    //protected $_dashboardResource;

    public function init() {
        parent::init();
        $this->_patientResource = new Application_Model_DbTable_Patients();
        $this->_userResource = new Application_Model_DbTable_Users();
    }

    /**
     * This function is used to show all function in dashboard 
     * Created By :  Umesh
     * Date : 20 May,2014
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
            //$params['rows'] = $data['rows'];
            //$params['start'] = 0;
            //if ($data['page'] > 1) {
              //  $params['start'] = ($data['page'] - 1) * $params['rows'];
            //}
            //dd($params);
            
            $result = $this->_patientResource->allDoctorPatients($params);
            
            $responce = new stdClass();
            $responce->page = $result['page'];
            $responce->total = $result['total'];
            $responce->records = $result['records'];


            foreach ($result['result'] as $k => $val) {
                $statusUpdate = '';
                $actionUpdate = '';
                $id = $val['id'];
                $responce->rows[$k]['id'] = $val['id'];
//                $actionUpdate = "<a onclick='return roleResource($id);' href='javascript:void(0);'><img width='15' height='15' src='".ADMIN_IMAGE_URL."related_cat.png' alt='Role Resources' title='Role Resources'></a>";               
//                if($val['status'] == 1){
//                    $statusUpdate = "<a onclick='return roleStatus($id,0);' href='javascript:void(0);'><img width='15' height='15' src='".ADMIN_IMAGE_URL."icon_activate.gif' alt='Role status' title='Role status'></a>";
//                } else {
//                    $statusUpdate = "<a onclick='return roleStatus($id,1);' href='javascript:void(0);'><img width='15' height='15' src='".ADMIN_IMAGE_URL."icon_deactivate.gif' alt='Role status' title='Role status'></a>";
//                }
                $responce->rows[$k]['cell'] = array(
                    $val['id'],
                    $val['name'],
                    $val['contact_no'],
                    $val['email'],
                    $val['created_at']
                   // $statusUpdate,
                    //$actionUpdate
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
        if (!empty($id)) {

            $patientData = $this->_patientResource->fetchPatientData($id);
            $email = '';
            if(!empty($patientData['email'])){
               $email =  $patientData['email'];
            }else{
                $email =  $patientData['p_email'];
                
            }
            //dd($patientData);
            $formdata = array(
                'id' => $patientData['id'],
                'name' => $patientData['name'],
                'email' => $email,
                'contact_no' => $patientData['contact_no'],
                'address' => $patientData['address']
            );

            $this->view->formdata = $formdata;
            
        }
    }


    public function savepatientAction() {
        
        // global $status;
        $request = $this->getRequest();
        if ($data = $request->getPost()) {
            //dd($data);
            $params = $this->__inputPostData;
            //print_r($params); die;
            //$response = array('status' => 0, 'msg' => '');
            $this->validatePatientData($params, $this->errMsg);
            $inputData = new Zend_Filter_Input($this->filters, $this->validators);
            $inputData->setData($params);
            if ($inputData->isValid()) {


                //dd($params);
                if (!empty($params['id'])) {

                    $update = $this->_patientResource->updatePatientData($params);

                    if ($update) {
                        $this->_helper->FlashMessenger()->setNamespace('success')->addMessage('Patient record successfully updated.');
                        
                        //$this->_redirect(DOCTOR_BASE_URL . 'patient/addedit/id/'.$params['id']);
                        
                    } else {
                        //$this->view->msg = 'Patient record not updated.';
                        $this->_helper->FlashMessenger()->setNamespace('error')->addMessage('Patient record not updated.');                  
            
                    }
                    $this->_redirect(DOCTOR_BASE_URL . 'patient/addedit/id/'.$params['id']);
                } else { //Save new Patient

                    $email = $params['p_email'];
                    //dd($email);
                    $getUser = $this->_userResource->checkuser($email);
//dd($getUser);
                    if ($getUser) {
                    
                        $this->_helper->FlashMessenger()->setNamespace('error')->addMessage('Patient already exist.');                  
            
                    } else {

                      //  $date = $nowDate;
                        $userdata = array('name' => $params['p_name'],
                            'email' => $email,
                            'status' => 'inactive',
                            'user_role_type' => 1,
                            'activation_code' => Zend_Session::getId());
//dd($userdata);
                         $insertedId = $this->_userResource->addUser($userdata);

                        if ($insertedId) {

                            $patientData = array(
                                'user_id' => $insertedId,
                                'name' => $params['p_name'],
                                //'email' => $email, 
                                'contact_no' => $params['p_mno'],
                                'address' => $params['p_add']
                            );

                            $insertedId = $this->_patientResource->addPatient($patientData);
//dd($insertedId);
                            if ($insertedId) {

                                $this->_helper->FlashMessenger()->setNamespace('success')->addMessage('Patient record successfully saved.');
                            } else {
                                $this->_helper->FlashMessenger()->setNamespace('error')->addMessage('Patient record is not inserted.Please try again.');                  
            
                               /// $this->view->msg = 'Patient record is not inserted.Please try again.';
                            }
                        } else {
                            $this->_helper->FlashMessenger()->setNamespace('error')->addMessage('Patient record is not inserted.Please try again.');                  
            
                            //$this->view->msg = 'Patient record is not inserted.Please try again.';
                        }
                    }

                    $this->_redirect(DOCTOR_BASE_URL . 'patient/addedit');
                }
                } else {

                $msg = $this->getValidatorErrors($inputData->getMessages(), 'admin');
                
                $this->_helper->FlashMessenger()->setNamespace('error')->addMessage($msg);                  
                
                if($params['id']){
                    $this->_redirect(DOCTOR_BASE_URL . 'patient/addedit/id/'.$params['id']);
                }else{
                    $this->_redirect(DOCTOR_BASE_URL . 'patient/addedit');
                }
                
                //$this->view->msg = $msg;
            }
        }
        
        die;

    }

  

    protected function validatePatientData($data, $errMsg) {

        if (isset($data['p_name'])) {
            $this->validators['p_name'] = array(
                'NotEmpty',
                'messages' => array('Please enter patient name.'));
        }



        if (isset($data['p_email'])) {
            $this->validators['email'] = array(
                'NotEmpty',
                array('StringLength', 5),
                array('EmailAddress'),
                'messages' => array(
                    'Please enter email id.', 'Email must be atleast 5 characters', 'Plz enter valid email id'
            ));
        }

        if (isset($data['p_mno'])) {
            $this->validators['p_mno'] = array(
                'NotEmpty',
                array('StringLength', 10),
                'messages' => array(
                    'Please enter mobile no.', 'Mobile no must be 10 digit'));
        }

        if (isset($data['p_add'])) {
            $this->validators['p_add'] = array(
                'NotEmpty',
                'messages' => 'Please enter address.');
        }
    }
    
    
    
    
    
    public function deletepatientAction() {
        global $nowDateTime;
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $request = $this->getRequest();
        if ($request->getPost()) {
            $data = $this->__inputPostData;
            
            $userID = $this->_patientResource->getPatientLoginIdByPatientId($data['id']);
            
            //dd($userID);
            $params['status'] = 0;
            $params['updated_at'] = $nowDateTime;
            $deleteRole = $this->_patientResource->updatePatientDetail($params,$data['id']);
            $data['satus'] = 'inactive';
            $data['updated_at'] = $nowDateTime;
            $deleteUser = $this->_userResource->updateUserDetail($data,$userID);
            
            
            echo $deleteRole;
        }
        die;
    }
    
    public function viewAction() {
        global $status;
        global $gender;
        $this->view->status = $status;
        $id = $this->getRequest()->getParam('id');
        if (!empty($id)) {

            $patientData = $this->_patientResource->fetchPatientData($id);
            $email = '';
            if(!empty($patientData['email'])){
               $email =  $patientData['email'];
            }else{
                $email =  $patientData['p_email'];
                
            }
            //dd($patientData);
            $formdata = array(
                'id' => $patientData['id'],
                'name' => $patientData['name'],
                'email' => $email,
                'contact_no' => $patientData['contact_no'],
                'address' => $patientData['address'],
                'dob' => $this->changeDateFormat($patientData['dob'], DATETIMEFORMAT,DATE_TIME_FORMAT),                                      
                'gender' => $gender[$patientData['gender']]
            );

            $this->view->formdata = $formdata;
            
        }
    }

}

?>