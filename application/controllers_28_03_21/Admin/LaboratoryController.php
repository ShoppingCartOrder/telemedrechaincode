<?php
class Admin_LaboratoryController extends Mylib_Controller_AdminbaseController {

    //protected $_dashboardResource;

    public function init() {
        parent::init();
        
        $this->_userResource = new Application_Model_DbTable_Users();
      
        $this->_patientResource = new Application_Model_DbTable_Patients();
        $this->_userResource = new Application_Model_DbTable_Users();

        $this->_chargeItemResource = new Application_Model_DbTable_ChargeItemName();
        $this->_laboratoriesResource = new Application_Model_DbTable_Laboratories();
        $this->_laboratoryRequestResource = new Application_Model_DbTable_LaboratoryRequest();
        $this->_laboratoryTestReportResource = new Application_Model_DbTable_LaboratoryTestReport();
        $this->_laboratoryTestResultResource = new Application_Model_DbTable_LaboratoryTestResults();
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
          
            
            $result = $this->_laboratoriesResource->allLaboratory($params);
            
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
        $session = new Zend_Session_Namespace('mylaboratory');
        $this->view->userAccountInfo = $this->_dashboardResource->fetchAdminUserInfo($session->loginId);
        $roleResourceObj = new Application_Model_DbTable_Role();
        $this->view->roleData = $roleResourceObj->fetchRoleData($this->view->userAccountInfo['0']['role']);
    }

    public function addeditAction() {
        global $status;

        $this->view->status = $status;
        $id = $this->getRequest()->getParam('id');
       
        if (!empty($id)) {

            $patientData = $this->_laboratoriesResource->fetchLaboratoryById($id);
            //dd($patientData);
            $formdata = array(
                'id' => $patientData['id'],
                'name' => $patientData['name'],
                'email' => $patientData['email'],
                'contact_no' => $patientData['contact_no'],
                
                'address' => $patientData['address'],
                
                'qualification' => $patientData['qualification'],
                'experience' => $patientData['experience'],
               
                'about' => $patientData['about']
            );

                    
            $this->view->formdata = $formdata;
            
        }
    }


    public function savelaboratoryAction() {
        
        // global $status;
        $request = $this->getRequest();
        if ($data = $request->getPost()) {
            //dd($data);
            $params = $this->__inputPostData;
            //print_r($params); die;
            //$response = array('status' => 0, 'msg' => '');
            $this->validatelaboratoryData($params, $this->errMsg);
            $inputData = new Zend_Filter_Input($this->filters, $this->validators);
            $inputData->setData($params);
            if ($inputData->isValid()) {


                //dd($params);
                if (!empty($params['id'])) {
                    
                    $dbdata = array(                                               
                            'name' => $data['p_name'],                           
                            'contact_no' => $data['p_mno'], 
                           
                            'address' => $data['p_add'],
                            
                            'qualification' => $data['qualification'],                           
                            'experience' => $data['experience'], 
                           
                            'about' => $data['about']
               
       
                            );
                    

                    $update = $this->_laboratoriesResource->updateLaboratoryData($dbdata,$params['id']);

                    if ($update) {
                        $this->_helper->FlashMessenger()->setNamespace('success')->addMessage('Laboratory record successfully updated.');
                        
                        //$this->_redirect(ADMIN_BASE_URL . 'patient/addedit/id/'.$params['id']);
                        
                    } else {
                        //$this->view->msg = 'Patient record not updated.';
                        $this->_helper->FlashMessenger()->setNamespace('error')->addMessage('Laboratory record not updated.');                  
            
                    }
                    $this->_redirect(ADMIN_BASE_URL . 'laboratory/index');
                } else { //Save new Patient
                    //dd($params);
                    $email = $params['p_email'];
                    //dd($email);
                    $getUser = $this->_userResource->checkuser($email);
//dd($getUser);
                    if ($getUser) {
                    
                        $this->_helper->FlashMessenger()->setNamespace('error')->addMessage('Laboratory already exist.');                  
            
                    } else {
                     
                            //  $date = $nowDate;
                        $userdata = array('name' => $params['p_name'],
                            'email' => $email,
                            'password' => md5(DEFAULT_PASSWORD),
                            'status' => 1,
                            'user_role_type' => LABORATORY_ROLE,
                            'activation_code' => Zend_Session::getId());
//dd($userdata);
                         $insertedId = $this->_userResource->addUser($userdata);
                         if ($insertedId) {
                             
                             $laboratorytData = array(
                                'user_id' => $insertedId,                           
                                'name' => $params['p_name'],
                                'email' => $email, 
                                'contact_no' => $params['p_mno'],
                                'address' => $params['p_add'],
                                'qualification' => $params['qualification'],
                                'experience' => $params['experience'],                               
                                'about' => $params['about']
                            );

                            $insertedId = $this->_laboratoriesResource->addLaboratory($laboratorytData);
//dd($insertedId);
                            if ($insertedId) {

                                $this->_helper->FlashMessenger()->setNamespace('success')->addMessage('Laboratory record successfully saved.');
                            } else {
                                $this->_helper->FlashMessenger()->setNamespace('error')->addMessage('Laboratory record is not inserted.Please try again.');                  
            
                               /// $this->view->msg = 'Patient record is not inserted.Please try again.';
                            }
                            
                             
                         }else {
                            $this->_helper->FlashMessenger()->setNamespace('error')->addMessage('Laboratory record is not inserted.Please try again.');                  
            
                            //$this->view->msg = 'Patient record is not inserted.Please try again.';
                        }
                            
                       
                    }

                    $this->_redirect(ADMIN_BASE_URL . 'laboratory/addedit');
                }
                
                } else {

                $msg = $this->getValidatorErrors($inputData->getMessages(), 'admin');
                
                $this->_helper->FlashMessenger()->setNamespace('error')->addMessage($msg);                  
                
                if($params['id']){
                    $this->_redirect(ADMIN_BASE_URL . 'laboratory/addedit/id/'.$params['id']);
                }else{
                    $this->_redirect(ADMIN_BASE_URL . 'laboratory/addedit');
                }
                
                //$this->view->msg = $msg;
            }
        }
       

    }

  

    protected function validatelaboratoryData($data, $errMsg) {

        if (isset($data['p_name'])) {
            $this->validators['p_name'] = array(
                'NotEmpty',
                'messages' => array('Please enter laboratory name.'));
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
    
    
    
    
    
    public function deletelaboratoryAction() {
        global $nowDateTime;
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $request = $this->getRequest();
        if ($request->getPost()) {
            $data = $this->__inputPostData;
            
            $userID = $this->_laboratoriesResource->getLaboratoryLoginIdByLabId($data['id']);
            
            //dd($userID);
            $params['status'] = 0;
            $params['updated_at'] = $nowDateTime;
            $deleteRole = $this->_laboratoriesResource->updateLaboratoryDetail($params,$data['id']);
            $udata['status'] = 0;
            $udata['updated_at'] = $nowDateTime;
            $deleteUser = $this->_userResource->updateUserDetail($udata,$userID);
            
            
            echo $deleteRole;
        }
        die;
    }

}

?>