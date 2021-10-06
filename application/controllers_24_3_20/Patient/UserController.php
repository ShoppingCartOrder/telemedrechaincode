<?php

class Patient_UserController extends Mylib_Controller_PatientbaseController {

    protected $_patientResource;
    protected $_userResource;

    public function init() {
        parent::init();
        $this->_patientResource = new Application_Model_DbTable_Patients();
        $this->_userResource = new Application_Model_DbTable_Users();
        $this->_vitalDetailsResource = new Application_Model_DbTable_PatientVitalDetails();
        $this->_hospitalDetailsResource = new Application_Model_DbTable_PatientHospitalDetails();
        $this->_prescriptionDetailsResource = new Application_Model_DbTable_PatientPrescriptionDetails();
        
        //$this->session = new Zend_Session_Namespace('mypatient');
    }

    public function indexAction() {
       $loginId = $this->patientNamespace->loginId;
       //echo $loginId; die;//
        //$id = $this->getRequest()->getParam('id');
        if (!empty($loginId)) {

            $patientData = $this->_patientResource->fetchPatientProfileData($loginId);
            //dd($patientData);
            $formdata = array(
                'id' => $patientData['id'],
                'name' => $patientData['name'],
                'email' => $patientData['email'],
                'contact_no' => $patientData['contact_no'],
                'address' => $patientData['address']
            );
//dd($formdata);
            $this->view->formdata = $formdata;
        }
       // die;
    }
   
    public function savepatientAction() {

        // global $status;
        $request = $this->getRequest();
        if ($data = $request->getPost()) {
            //dd($data);
            $params = $this->__inputPostData;
            $this->validatePatientData($params, $this->errMsg);
            $inputData = new Zend_Filter_Input($this->filters, $this->validators);
            $inputData->setData($params);
            if ($inputData->isValid()) {

               // if (!empty($params['id'])) {
                    $params['id'] = $this->patientNamespace->loginId;
                    //dd($params);
                    $update = $this->_patientResource->updateProfileData($params);

                    if ($update) {
                        $this->_helper->FlashMessenger()->setNamespace('success')->addMessage('Patient record successfully updated.');
                    } else {

                        $this->_helper->FlashMessenger()->setNamespace('error')->addMessage('Patient record not updated.');
                    }
                    $this->_redirect(PATIENT_BASE_URL . 'user/index');
                //} else { //Save new Patient
                   
            } else {

                $msg = $this->getValidatorErrors($inputData->getMessages(), 'admin');

                $this->_helper->FlashMessenger()->setNamespace('error')->addMessage($msg);

                $this->_redirect(PATIENT_BASE_URL . 'patient/index');
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

    public function deleteAction() {
        $id = $this->getRequest()->getPost('id');
        $userdetail = $this->_userResource->fetchUserData($id);
        $photo = $userdetail->photo;

        if (isset($id)) {
            $newname = UPLOAD_FILE_PATH . '/images/userimages/main/' . $photo;
            $newnamethumb = UPLOAD_FILE_PATH . '/images/userimages/thumb/' . $photo;
            unlink($newname);
            unlink($newnamethumb);
            if ($this->_userResource->deleteUserData($id)) {
                $this->_redirect(ADMIN_BASE_URL . 'user/index');
            } else {
                $this->_redirect(ADMIN_BASE_URL . 'user/index');
            }
        }
    }
    
    public function vitalDetailsAddeditAction() {
       $loginId = $this->patientNamespace->loginId;
        $id = $this->getRequest()->getParam('id');
        if (!empty($loginId)) {

            $vitalData = $this->_vitalDetailsResource->getPatientVitalDetails($id);
            //dd($vitalData);
            $formdata = array(
                'id' => $vitalData['id'],
                'patient_id' => $vitalData['patient_id'],
                'blood_pressure' => $vitalData['blood_pressure'],
                'sugar' => $vitalData['sugar'],
                'heart_beat' => $vitalData['heart_beat'],
                'bmi' => $vitalData['bmi'],
                'temperature' => $vitalData['temperature']
                
            );
//dd($formdata);
            $this->view->formdata = $formdata;
        }
       // die;
    }
    
     public function savevitaldetailsAction() {

     
        // global $status;
        $request = $this->getRequest();
        if ($data = $request->getPost()) {
                                
            $params = $this->__inputPostData;
            
            $id = $params['id']; 
            
            
            //$response = array('status' => 0, 'msg' => '');
            $this->validateVitalData($params, $this->errMsg);
            $inputData = new Zend_Filter_Input($this->filters, $this->validators);
            $inputData->setData($params);
            
      //print_r($params); die;
            
            if ($inputData->isValid()) {

                
                //dd($params);
                if (!empty($id)) {
                    
                
                   $vitalData = array(      
                       
                        'patient_id'=> $this->patientNamespace->patient_id, 
                        'blood_pressure'=> $params['blood_pressure'],
                        'sugar'=> $params['sugar'], 
                        'heart_beat'=> $params['heart_beat'],
                        'bmi'=> $params['bmi'], 
                        'temperature'=> $params['temperature']
                        
                            );
                    //dd($patientData);
                    $updateP = $this->_vitalDetailsResource->updatePatientVitalDetails($vitalData,$id);
                    
                    
                        
                    if (($updateP)) {
                        
                           
                        $this->_helper->FlashMessenger()->setNamespace('success')->addMessage('Vital record successfully updated.');
                        
                        $this->_redirect(PATIENT_BASE_URL . 'user/vital-details-addedit');
                     
                        //$this->_redirect(PATIENT_BASE_URL . 'patient/addedit/id/'.$params['id']);
                        
                    } else {
                       
                        $this->_helper->FlashMessenger()->setNamespace('error')->addMessage('Vital record not updated.');                  
                        $this->_redirect(PATIENT_BASE_URL . 'user/vital-details-addedit/id/'.$id);
                        
                        
                    }
                    //$this->_redirect(PATIENT_BASE_URL . 'appointment/video-consultation-addedit/patient_id/'.$pid.'/appnt_id/'.$id);
                                   
                    } else {
                        
                    
                    $vitalData = array(                         
                        'patient_id'=> $this->patientNamespace->patient_id, 
                        'blood_pressure'=> $params['blood_pressure'],
                        'sugar'=> $params['sugar'], 
                        'heart_beat'=> $params['heart_beat'],
                        'bmi'=> $params['bmi'], 
                        'temperature'=> $params['temperature']
                        
                            );
                    //dd($vitalData);
                    $insertedId = $this->_vitalDetailsResource->addpatientVitalDetails($vitalData);
                    
                    
                            if ($insertedId) {
                            
                                $this->_helper->FlashMessenger()->setNamespace('success')->addMessage('Vital Data successfully saved.');
                                 $this->_redirect(PATIENT_BASE_URL . 'user/vital-details-addedit');
                        
                            } else {
                                $this->_helper->FlashMessenger()->setNamespace('error')->addMessage('Vital Data not successfully saved.');                  
                                $this->_redirect(PATIENT_BASE_URL . 'user/vital-details-addedit/id/'.$id);
                               /// $this->view->msg = 'Patient record is not inserted.Please try again.';
                            }
                       
                   
                        
                         
                    
                }
                } else {

                $msg = $this->getValidatorErrors($inputData->getMessages(), 'admin');
                
                $this->_helper->FlashMessenger()->setNamespace('error')->addMessage($msg); 
                
              
                if (!empty($id)) {
                    //dd($actionName);
                    $this->_redirect(PATIENT_BASE_URL . 'user/vital-details-addedit/id/'.$id);
                }else{
                    $this->_redirect(PATIENT_BASE_URL . 'user/vital-details-addedit');
                }
                
                //$this->view->msg = $msg;
            }
        }
        
      

    
        die;
    }
    
    protected function validateVitalData($data, $errMsg) {

        if (isset($data['blood_pressure'])) {
            $this->validators['blood_pressure'] = array(
                'NotEmpty',
                'messages' => array('Please enter blood pressure.'));
        }
       
    }

     public function hospitalDetailsAddeditAction() {
       $loginId = $this->patientNamespace->loginId;
       //echo $loginId; die;//
        $id = $this->getRequest()->getParam('id');
        if (!empty($loginId)) {

            $hospitalData = $this->_hospitalDetailsResource->getPatientHospitalDetails($id);
            //dd($hospitalData);
            $formdata = array(
                'id' => $hospitalData['id'],
                'patient_id' => $hospitalData['patient_id'],
                'hospital_name' => $hospitalData['hospital_name'],
                'doctor_name' => $hospitalData['doctor_name'],
                'desease_name' => $hospitalData['desease_name'],
                'note' => $hospitalData['note']
                
                
            );
//dd($formdata);
            $this->view->formdata = $formdata;
        }
       // die;
    }
    
      public function savehospitaldetailsAction() {

     
        // global $status;
        $request = $this->getRequest();
        if ($data = $request->getPost()) {
                                
            $params = $this->__inputPostData;
            
            $id = $params['id']; 
            
            
            //$response = array('status' => 0, 'msg' => '');
            $this->validateHospitalData($params, $this->errMsg);
            $inputData = new Zend_Filter_Input($this->filters, $this->validators);
            $inputData->setData($params);
            
      //print_r($params); die;
            
            if ($inputData->isValid()) {

                
                //dd($params);
                if (!empty($id)) {
                    
                
                
                
                            
                   $hospitalData = array(                         
                        'patient_id'=> $this->patientNamespace->patient_id, 
                        'hospital_name'=> $params['hospital_name'],
                        'doctor_name'=> $params['doctor_name'], 
                        'desease_name'=> $params['desease_name'],
                        'note'=> $params['note']
                        
                            );
                   
                   
                    //dd($patientData);
                    $updateP = $this->_hospitalDetailsResource->updatePatientHospitalDetails($hospitalData,$id);
                    
                    
                        
                    if (($updateP)) {
                        
                           
                        $this->_helper->FlashMessenger()->setNamespace('success')->addMessage('Hospital record successfully updated.');
                        
                        $this->_redirect(PATIENT_BASE_URL . 'user/hospital-details-addedit');
                     
                        //$this->_redirect(PATIENT_BASE_URL . 'patient/addedit/id/'.$params['id']);
                        
                    } else {
                       
                        $this->_helper->FlashMessenger()->setNamespace('error')->addMessage('Hospital record not updated.');                  
                        $this->_redirect(PATIENT_BASE_URL . 'user/hospital-details-addedit/id/'.$id);
                        
                        
                    }
                    //$this->_redirect(PATIENT_BASE_URL . 'appointment/video-consultation-addedit/patient_id/'.$pid.'/appnt_id/'.$id);
                                   
                    } else {
                        
                    
                     $hospitalData = array(                         
                        'patient_id'=> $this->patientNamespace->patient_id, 
                        'hospital_name'=> $params['hospital_name'],
                        'doctor_name'=> $params['doctor_name'], 
                        'desease_name'=> $params['desease_name'],
                        'note'=> $params['note']
                        
                            );
                    //dd($hospitalData);
                    $insertedId = $this->_hospitalDetailsResource->addpatientHospitalDetails($hospitalData);
                    
                    
                            if ($insertedId) {
                            
                                $this->_helper->FlashMessenger()->setNamespace('success')->addMessage('Hospital Data successfully saved.');
                                 $this->_redirect(PATIENT_BASE_URL . 'user/hospital-details-addedit');
                        
                            } else {
                                $this->_helper->FlashMessenger()->setNamespace('error')->addMessage('Hospital Data not successfully saved.');                  
                                $this->_redirect(PATIENT_BASE_URL . 'user/hospital-details-addedit/id/'.$id);
                               /// $this->view->msg = 'Patient record is not inserted.Please try again.';
                            }
                       
                   
                        
                         
                    
                }
                } else {

                $msg = $this->getValidatorErrors($inputData->getMessages(), 'admin');
                
                $this->_helper->FlashMessenger()->setNamespace('error')->addMessage($msg); 
                
              
                if (!empty($id)) {
                    //dd($actionName);
                    $this->_redirect(PATIENT_BASE_URL . 'user/hospital-details-addedit/id/'.$id);
                }else{
                    $this->_redirect(PATIENT_BASE_URL . 'user/hospital-details-addedit');
                }
                
                //$this->view->msg = $msg;
            }
        }
        
      

    
        die;
    }
    
    protected function validateHospitalData($data, $errMsg) {

        if (isset($data['hospital_name'])) {
            $this->validators['hospital_name'] = array(
                'NotEmpty',
                'messages' => array('Please enter hospital name.'));
        }
       
    }

 public function prescriptionDetailsAddeditAction() {
       $loginId = $this->patientNamespace->loginId;
       //echo $loginId; die;//
        $id = $this->getRequest()->getParam('id');
        if (!empty($loginId)) {

            $prescriptionData = $this->_prescriptionDetailsResource->getPatientPrescriptionDetails($id);
            //dd($hospitalData);
            $formdata = array(
                'id' => $prescriptionData['id'],
                'patient_id' => $prescriptionData['patient_id'],
                'medicine_name' => $prescriptionData['medicine_name'],
                'dosage' => $prescriptionData['dosage'],
                'duration' => $prescriptionData['duration'],
                'side_effects' => $prescriptionData['side_effects']
                
                
            );
//dd($formdata);
            $this->view->formdata = $formdata;
        }
       // die;
    }
    
      public function saveprescriptiondetailsAction() {

     
        // global $status;
        $request = $this->getRequest();
        if ($data = $request->getPost()) {
                                
            $params = $this->__inputPostData;
            
            $id = $params['id']; 
            
            
            //$response = array('status' => 0, 'msg' => '');
            $this->validatePrescriptionData($params, $this->errMsg);
            $inputData = new Zend_Filter_Input($this->filters, $this->validators);
            $inputData->setData($params);
            
      //print_r($params); die;
            
            if ($inputData->isValid()) {

                
                //dd($params);
                if (!empty($id)) {
                 
                
                            
                   $prescriptionData = array(                         
                        'patient_id'=> $this->patientNamespace->patient_id, 
                        'medicine_name'=> $params['medicine_name'],
                        'dosage'=> $params['dosage'], 
                        'duration'=> $params['duration'],
                        'side_effects'=> $params['side_effects']
                        
                            );
                   
                   
                    //dd($patientData);
                    $updateP = $this->_prescriptionDetailsResource->updatePatientPrescriptionDetails($prescriptionData,$id);
                    
                    
                        
                    if (($updateP)) {
                        
                           
                        $this->_helper->FlashMessenger()->setNamespace('success')->addMessage('Prescription record successfully updated.');
                        
                        $this->_redirect(PATIENT_BASE_URL . 'user/prescription-details-addedit');
                     
                        //$this->_redirect(PATIENT_BASE_URL . 'patient/addedit/id/'.$params['id']);
                        
                    } else {
                       
                        $this->_helper->FlashMessenger()->setNamespace('error')->addMessage('Prescription record not updated.');                  
                        $this->_redirect(PATIENT_BASE_URL . 'user/prescription-details-addedit/id/'.$id);
                        
                        
                    }
                    //$this->_redirect(PATIENT_BASE_URL . 'appointment/video-consultation-addedit/patient_id/'.$pid.'/appnt_id/'.$id);
                                   
                    } else {
                        
                    
                    $prescriptionData = array(                         
                        'patient_id'=> $this->patientNamespace->patient_id, 
                        'medicine_name'=> $params['medicine_name'],
                        'dosage'=> $params['dosage'], 
                        'duration'=> $params['duration'],
                        'side_effects'=> $params['side_effects']
                        
                            );
                    //dd($hospitalData);
                    $insertedId = $this->_prescriptionDetailsResource->addpatientPrescriptionDetails($prescriptionData);
                    
                    
                            if ($insertedId) {
                            
                                $this->_helper->FlashMessenger()->setNamespace('success')->addMessage('Prescription Data successfully saved.');
                                 $this->_redirect(PATIENT_BASE_URL . 'user/prescription-details-addedit');
                        
                            } else {
                                $this->_helper->FlashMessenger()->setNamespace('error')->addMessage('Prescription Data not successfully saved.');                  
                                $this->_redirect(PATIENT_BASE_URL . 'user/prescription-details-addedit/id/'.$id);
                               /// $this->view->msg = 'Patient record is not inserted.Please try again.';
                            }
                       
                   
                        
                         
                    
                }
                } else {

                $msg = $this->getValidatorErrors($inputData->getMessages(), 'admin');
                
                $this->_helper->FlashMessenger()->setNamespace('error')->addMessage($msg); 
                
              
                if (!empty($id)) {
                    //dd($actionName);
                    $this->_redirect(PATIENT_BASE_URL . 'user/prescription-details-addedit/id/'.$id);
                }else{
                    $this->_redirect(PATIENT_BASE_URL . 'user/prescription-details-addedit');
                }
                
                //$this->view->msg = $msg;
            }
        }
        
      

    
        die;
    }
    
    protected function validatePrescriptionData($data, $errMsg) {

        if (isset($data['hospital_name'])) {
            $this->validators['hospital_name'] = array(
                'NotEmpty',
                'messages' => array('Please enter hospital name.'));
        }
     
    }

}
