<?php

class Hospital_DoctorController extends Mylib_Controller_HospitalbaseController {

    //protected $_dashboardResource;

    public function init() {
        parent::init();
        $this->_doctorResource = new Application_Model_DbTable_Doctors();
        $this->_userResource = new Application_Model_DbTable_Users();
        $this->_specialityResource = new Application_Model_DbTable_Speciality();
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
          
            
            $result = $this->_doctorResource->allDoctors($params);
            
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
                    $val['email'],
                    $val['speciality_name'],
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
        $this->view->dertData = $this->_specialityResource->fetchAllSpeciality();
        if (!empty($id)) {

            $patientData = $this->_doctorResource->fetchDoctorById($id);
            //dd($patientData);
            $formdata = array(
                'id' => $patientData['id'],
                'name' => $patientData['name'],
                'email' => $patientData['email'],
                'contact_no' => $patientData['contact_no'],
                'speciality_id' => $patientData['speciality_id'],
                'address' => $patientData['address'],
                
                'qualification' => $patientData['qualification'],
                'experience' => $patientData['experience'],
                'fee' => $patientData['fee'],
                'about' => $patientData['about']
            );

                    
            $this->view->formdata = $formdata;
            
        }
    }


    public function savedoctorAction() {
        
        // global $status;
        $request = $this->getRequest();
        if ($data = $request->getPost()) {
            //dd($data);
            $params = $this->__inputPostData;
            //print_r($params); die;
            //$response = array('status' => 0, 'msg' => '');
            $this->validateDoctorData($params, $this->errMsg);
            $inputData = new Zend_Filter_Input($this->filters, $this->validators);
            $inputData->setData($params);
            if ($inputData->isValid()) {


                //dd($params);
                if (!empty($params['id'])) {

                    $update = $this->_doctorResource->updateDoctorData($params);

                    if ($update) {
                        $this->_helper->FlashMessenger()->setNamespace('success')->addMessage('Doctor record successfully updated.');
                        
                        //$this->_redirect(ADMIN_BASE_URL . 'patient/addedit/id/'.$params['id']);
                        
                    } else {
                        //$this->view->msg = 'Patient record not updated.';
                        $this->_helper->FlashMessenger()->setNamespace('error')->addMessage('Doctor record not updated.');                  
            
                    }
                    $this->_redirect(ADMIN_BASE_URL . 'doctor/index');
                } else { //Save new Patient
                    //dd($params);
                    $email = $params['p_email'];
                    //dd($email);
                    $getUser = $this->_doctorResource->getDoctorsByEmail($email);
//dd($getUser);
                    if ($getUser) {
                    
                        $this->_helper->FlashMessenger()->setNamespace('error')->addMessage('Doctor already exist.');                  
            
                    } else {
                     
                            $doctortData = array(
                               // 'user_id' => $insertedId,
                                'speciality_id' => $params['speciality'],
                                'name' => $params['p_name'],
                                'email' => $email, 
                                'contact_no' => $params['p_mno'],
                                'address' => $params['p_add'],
                                'qualification' => $params['qualification'],
                                'experience' => $params['experience'],
                                'fee' => $params['fee'],
                                'about' => $params['about']
                            );

                            $insertedId = $this->_doctorResource->addDoctor($doctortData);
//dd($insertedId);
                            if ($insertedId) {

                                $this->_helper->FlashMessenger()->setNamespace('success')->addMessage('Doctor record successfully saved.');
                            } else {
                                $this->_helper->FlashMessenger()->setNamespace('error')->addMessage('Doctor record is not inserted.Please try again.');                  
            
                               /// $this->view->msg = 'Patient record is not inserted.Please try again.';
                            }
                       
                    }

                    $this->_redirect(ADMIN_BASE_URL . 'doctor/addedit');
                }
                } else {

                $msg = $this->getValidatorErrors($inputData->getMessages(), 'admin');
                
                $this->_helper->FlashMessenger()->setNamespace('error')->addMessage($msg);                  
                
                if($params['id']){
                    $this->_redirect(ADMIN_BASE_URL . 'doctor/addedit/id/'.$params['id']);
                }else{
                    $this->_redirect(ADMIN_BASE_URL . 'doctor/addedit');
                }
                
                //$this->view->msg = $msg;
            }
        }
        
        die;

    }

  

    protected function validateDoctorData($data, $errMsg) {

        if (isset($data['p_name'])) {
            $this->validators['p_name'] = array(
                'NotEmpty',
                'messages' => array('Please enter doctor name.'));
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
        
        if (isset($data['department'])) {
            $this->validators['department'] = array(
                'NotEmpty',
                'messages' => 'Please select department name.');
        }
    }
    
    
    
    
    
    public function deletedoctorAction() {
        global $nowDateTime;
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $request = $this->getRequest();
        if ($request->getPost()) {
            $data = $this->__inputPostData;
            
            $userID = $this->_doctorResource->getDoctorLoginIdByDoctorId($data['id']);
            
            //dd($userID);
            $params['status'] = 0;
            $params['updated_at'] = $nowDateTime;
            $deleteRole = $this->_doctorResource->updateDoctorDetail($params,$data['id']);
            $udata['status'] = 0;
            $udata['updated_at'] = $nowDateTime;
            $deleteUser = $this->_userResource->updateUserDetail($udata,$userID);
            
            
            echo $deleteRole;
        }
        die;
    }
    
    public function getdoctorAction() {
         $request = $this->getRequest();
         $dprtId = $request->getParam('dprtId');
         $doctorsArr = $this->_doctorResource->getDoctorBySpecialityId($dprtId);
         //dd($doctorsArr);
         echo json_encode($doctorsArr);
         exit;
         //$id = $this->getRequest()->getParam('id');
     }
    public function prescribeAction() {
         $request = $this->getRequest();
        //die;
     }

}

?>