<?php

class Doctor_UserController extends Mylib_Controller_DoctorbaseController {

    protected $_laboratoryResource;
    protected $_userResource;

    public function init() {
        parent::init();
        $this->_laboratoriesResource = new Application_Model_DbTable_Laboratories();
        $this->_userResource = new Application_Model_DbTable_Users();
        $this->_doctorResource = new Application_Model_DbTable_Doctors();
        
        $this->_specialityResource = new Application_Model_DbTable_Speciality();
    }

    public function indexAction() {
       $doctorId = $this->doctorId;
       //dd($doctorId);
        if (!empty($doctorId)) {
            $this->view->dertData = $this->_specialityResource->fetchAllSpeciality();
            $patientData = $this->_doctorResource->fetchDoctorById($doctorId);
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
//dd($formdata);
                    
            $this->view->formdata = $formdata;
        }
       // die;
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

                $params['id'] = $this->doctorId;
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
                    $update = $this->_doctorResource->updateDoctorData($dbdata,$params['id']);

                    if ($update) {
                        $this->_helper->FlashMessenger()->setNamespace('success')->addMessage('Doctor record successfully updated.');
                        
                        //$this->_redirect(DOCTOR_BASE_URL . 'patient/addedit/id/'.$params['id']);
                        
                    } else {
                        //$this->view->msg = 'Patient record not updated.';
                        $this->_helper->FlashMessenger()->setNamespace('error')->addMessage('Doctor record not updated.');                  
            
                    }
                    $this->_redirect(DOCTOR_BASE_URL . 'user/index');
                } 
                } else {

                $msg = $this->getValidatorErrors($inputData->getMessages(), 'admin');
                
                $this->_helper->FlashMessenger()->setNamespace('error')->addMessage($msg);                  
                
               $this->_redirect(DOCTOR_BASE_URL . 'user/index');
                
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
    

   

}
