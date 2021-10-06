<?php

class Hospital_UserController extends Mylib_Controller_HospitalbaseController {

    
    protected $_userResource;

    public function init() {
        parent::init();
        $this->_userResource = new Application_Model_DbTable_Users();
        //$this->session = new Zend_Session_Namespace('mypatient');
    }

    public function indexAction() {
       $loginId = $this->loginId;
       //echo $loginId; die;//
        //$id = $this->getRequest()->getParam('id');
        if (!empty($loginId)) {

            $patientData = $this->_laboratoriesResource->fetchLaboratoryProfileData($loginId);
            //dd($patientData);
            $formdata = array(
                'id' => $patientData['id'],
                'name' => $patientData['name'],
                'email' => $patientData['email'],
                'contact_no' => $patientData['contact_no'],
                'address' => $patientData['address']
            );
//dd($formdata);
            $this->view->formdata = $patientData;
        }
       // die;
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
                $labId = $this->lab_id;

                //dd($params);
                if (!empty($labId)) {
                    
                    $dbdata = array(                                               
                            'name' => $data['p_name'],                           
                            'contact_no' => $data['p_mno'], 
                           
                            'address' => $data['p_add'],
                            
                            'qualification' => $data['qualification'],                           
                            'experience' => $data['experience'], 
                           
                            'about' => $data['about']
               
       
                            );
                    

                    $update = $this->_laboratoriesResource->updateLaboratoryData($dbdata,$labId);

                    if ($update) {
                        $this->_helper->FlashMessenger()->setNamespace('success')->addMessage('Laboratory record successfully updated.');
                        $this->_redirect(LABORATORY_BASE_URL . 'user');
                        //$this->_redirect(LABORATORY_BASE_URL . 'patient/addedit/id/'.$params['id']);
                        
                    } else {
                        //$this->view->msg = 'Patient record not updated.';
                        $this->_helper->FlashMessenger()->setNamespace('error')->addMessage('Laboratory record not updated.');                  
            
                    }
                    $this->_redirect(LABORATORY_BASE_URL . 'user');
                } 
                
                } else {

                $msg = $this->getValidatorErrors($inputData->getMessages(), 'admin');
                
                $this->_helper->FlashMessenger()->setNamespace('error')->addMessage($msg);                  
                
                if($params['id']){
                   // $this->_redirect(LABORATORY_BASE_URL . 'laboratory/addedit/id/'.$params['id']);
                }else{
                  //  $this->_redirect(LABORATORY_BASE_URL . 'laboratory/addedit');
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
    


}
