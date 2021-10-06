<?php

class Patient_PrescriptionController extends Mylib_Controller_PatientbaseController {

    //protected $_dashboardResource;

    public function init() {
        parent::init();
        $this->_patientResource = new Application_Model_DbTable_Patients();
        $this->_appointmentResource = new Application_Model_DbTable_Appointments();
        $this->_userResource = new Application_Model_DbTable_Users();
        $this->_doctorResource = new Application_Model_DbTable_Doctors();
        $this->_departmentResource = new Application_Model_DbTable_Departments();
        $this->_prescriptionResource = new Application_Model_DbTable_Prescription();
    }

    /**
     * This function is used to show all function in dashboard 
     * Created By :  bidhan
     * Date : 20 May,2019
     * @param void
     * @return void
     */
    public function indexAction() {

       global $nowDateTime;
       global $statusType;
       //global $appointmentType;
        $request = $this->getRequest();
        global $noRecord;
        $this->view->pid = $pid = $this->getRequest()->getParam('patient_id');
        $this->view->appntId = $appntId = $this->getRequest()->getParam('appnt_id');
        //echo $appntId; die;
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
            $params['condition']['patient_id'] = $pid;           
            $params['condition']['appnt_id'] = $appntId;           
            $params['userRoleType'] = PATIENT_ROLE;           
            $result = $this->_prescriptionResource->allPrescriptions($params);
            //dd($result);
            $responce = new stdClass();
            $responce->page = $result['page'];
            $responce->total = $result['total'];
            $responce->records = $result['records'];


            foreach ($result['result'] as $k => $val) {
                
                //$statusUpdate = '';
                //$actionUpdate = '';
                $id = $val['id'];
                $did = $val['prescribed_by'];
                $appntId = $val['appnt_id'];
                //$apIDs = $val['appointment_type'].'_'.$id.'_'.$pid;
                $selectStatus = '';
               
                
                $responce->rows[$k]['id'] = $id;
               $actionUpdate = "<a title = 'View Prescription' onclick='return prescriptionEdit($did,$appntId,$id);' href='javascript:void(0);'><span class='far fa-edit' aria-hidden='true'></span></a>";
                        
                
                
                $responce->rows[$k]['cell'] = array(
                    $appntId,                                                        
                    //$val['email'],
                    $val['medication_name'].' ('.$val['form_of_medicine'].' '.$val['strength'].' '.$val['unit'].')',
                    //$val['form_of_medicine'].' '.$val['unit'],
                    $val['quantity'].' '.$val['how_often'].' '.$val['whichtime'],
                    $this->changeDateFormat($val['start_datetime'], DATETIMEFORMAT,FRONT_DATE_FORMAT).' TO '.$this->changeDateFormat($val['end_datetime'], DATETIMEFORMAT,FRONT_DATE_FORMAT),
                    $val['side_effects'],
                    //$this->changeDateFormat($val['appoinment_datetime'], DATETIMEFORMAT,DATE_TIME_FORMAT),                   
                    $this->changeDateFormat($val['created_at'], DATETIMEFORMAT,DATE_TIME_FORMAT),                 
                    $val['doctor_name'],  
                    $val['dprt_name'],  
                    $actionUpdate
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
        
       // global $status;

        //$this->view->status = $status;
        $did = $this->getRequest()->getParam('doctor_id');
        $appntId = $this->getRequest()->getParam('appnt_id');
        $id = $this->getRequest()->getParam('id');
        
        $this->view->doctor_id = $did;
        $this->view->appntId = $appntId;
        $this->view->id = $id;
        
        $this->view->doctorData = $this->_doctorResource->fetchDoctorById($did);
         //   dd($this->view->doctorData);
        $this->view->dertData = $this->_departmentResource->fetchAllDepartments();
        //$patientData = $this->_patientResource->getPatientById($pid);
          //  $this->view->patientData = $patientData;
            //dd($patientData);
        if (!empty($did) && !empty($appntId) && !empty($id)) {

            
            
            
            $prescriptionData = $this->_prescriptionResource->getPrescriptionById($id);
            
           //dd($prescriptionData);
       
             $formdata = array(                         
                        'id'=> $prescriptionData['id'], 
                        'patient_id'=> $prescriptionData['patient_id'], 
                        'appnt_id'=> $prescriptionData['appnt_id'],
                        'med_name'=> $prescriptionData['medication_name'], 
                        'form_of_medicine'=> $prescriptionData['form_of_medicine'],
                        'strn'=> $prescriptionData['strength'], 
                        'unit'=> $prescriptionData['unit'], 
                        'quantity'=> $prescriptionData['quantity'], 
                         'how_often'=> $prescriptionData['how_often'],
                        'whichtime'=> $prescriptionData['whichtime'],
                        'other_instruction'=> $prescriptionData['other_instructions'], 
                        'start_time'=> date(DATE_TIME_FORMAT,strtotime($prescriptionData['start_datetime'])), 
                        'end_time'=> date(DATE_TIME_FORMAT,strtotime($prescriptionData['end_datetime'])),             
                        'm_taken_for'=> $prescriptionData['medicine_taken_for'], 
                        'prescribed_by'=> $prescriptionData['prescribed_by'], 
                        'side_effects'=> $prescriptionData['side_effects']
                        
                            );
             
//dd($formdata);
            $this->view->formdata = $formdata;
            
        }
       
    }


    public function saveprescriptionAction() {
        // global $status;
        $request = $this->getRequest();
        if ($data = $request->getPost()) {
                                
            $params = $this->__inputPostData;
            
            $pid = $params['patient_id'];
            $appntId = $params['appnt_id'];
            $id = $params['id']; 
            
            
            //$response = array('status' => 0, 'msg' => '');
            $this->validatePrescriptionData($params, $this->errMsg);
            $inputData = new Zend_Filter_Input($this->filters, $this->validators);
            $inputData->setData($params);
            
      //print_r($params); die;
            
            if ($inputData->isValid()) {

                
                //dd($params);
                if (!empty($pid) && !empty($appntId) && !empty($id)) {
                    
                
                   $patientData = array(                         
                        'patient_id'=> $params['patient_id'], 
                        'appnt_id'=> $params['appnt_id'],
                        'medication_name'=> $params['med_name'], 
                        'form_of_medicine'=> $params['form_of_medicine'],
                        'strength'=> $params['strn'], 
                        'unit'=> $params['unit'], 
                        'quantity'=> $params['quantity'], 
                         'how_often'=> $params['how_often'],
                        'whichtime'=> $params['whichtime'],
                        'other_instructions'=> $params['other_instruction'], 
                        'start_datetime'=> date(DATETIMEFORMAT,strtotime($params['start_time'])), 
                        'end_datetime'=> date(DATETIMEFORMAT,strtotime($params['end_time'])), 
                        
                        'medicine_taken_for'=> $params['m_taken_for'], 
                        'prescribed_by'=> $this->doctorId, 
                        'side_effects'=> $params['side_effects']
                        
                            );
                    //dd($patientData);
                    $updateP = $this->_prescriptionResource->updatePrescriptionDetail($patientData,$id);
                    
                    
                        
                    if (($updateP)) {
                        
                           
                        $this->_helper->FlashMessenger()->setNamespace('success')->addMessage('Prescription record successfully updated.');
                        
                        $this->_redirect(PATIENT_BASE_URL . 'prescription/index');
                     
                        //$this->_redirect(PATIENT_BASE_URL . 'patient/addedit/id/'.$params['id']);
                        
                    } else {
                       
                        $this->_helper->FlashMessenger()->setNamespace('error')->addMessage('Prescription record not updated.');                  
                        $this->_redirect(PATIENT_BASE_URL . 'prescription/addedit/patient_id/'.$pid.'/appnt_id/'.$appntId.'/id/'.$id);
                        
                        
                    }
                    //$this->_redirect(PATIENT_BASE_URL . 'appointment/video-consultation-addedit/patient_id/'.$pid.'/appnt_id/'.$appntId);
                                   
                    } else if (!empty($pid) && !empty($appntId)) {
                        
                    
                   $patientData = array(                         
                        'patient_id'=> $params['patient_id'], 
                        'appnt_id'=> $params['appnt_id'],
                        'medication_name'=> $params['med_name'], 
                        'form_of_medicine'=> $params['form_of_medicine'],
                        'strength'=> $params['strn'], 
                        'unit'=> $params['unit'], 
                        'quantity'=> $params['quantity'], 
                         'how_often'=> $params['how_often'],
                        'whichtime'=> $params['whichtime'],
                        'other_instructions'=> $params['other_instruction'], 
                        'start_datetime'=> date(DATETIMEFORMAT,strtotime($params['start_time'])), 
                        'end_datetime'=> date(DATETIMEFORMAT,strtotime($params['end_time'])),                         
                        'medicine_taken_for'=> $params['m_taken_for'], 
                        'prescribed_by'=> $this->doctorId, 
                        'side_effects'=> $params['side_effects']
                        
                            );
                    //dd($patientData);
                    $insertedId = $this->_prescriptionResource->addPrescription($patientData);
                    
                    
                            if ($insertedId) {
                            
                                $this->_helper->FlashMessenger()->setNamespace('success')->addMessage('Prescription successfully sent.');
                                 $this->_redirect(PATIENT_BASE_URL . 'prescription/index');
                        
                            } else {
                                $this->_helper->FlashMessenger()->setNamespace('error')->addMessage('Appointment is not sent.Please try again.');                  
                                $this->_redirect(PATIENT_BASE_URL . 'prescription/addedit/patient_id/'.$pid.'/appnt_id/'.$appntId);
                               /// $this->view->msg = 'Patient record is not inserted.Please try again.';
                            }
                       
                   
                        
                         
                    
                }
                } else {
echo "ddd"; die;
                $msg = $this->getValidatorErrors($inputData->getMessages(), 'admin');
                
                $this->_helper->FlashMessenger()->setNamespace('error')->addMessage($msg); 
                
              
                if (!empty($pid) && !empty($appntId)) {
                    //dd($actionName);
                    $this->_redirect(PATIENT_BASE_URL . 'prescription/addedit/patient_id/'.$pid.'/appnt_id/'.$appntId);
                }else{
                    $this->_redirect(PATIENT_BASE_URL . 'prescription/index');
                }
                
                //$this->view->msg = $msg;
            }
        }
        
        die;

    }

  

    protected function validatePrescriptionData($data, $errMsg) {

        if (isset($data['med_name'])) {
            $this->validators['med_name'] = array(
                'NotEmpty',
                'messages' => array('Please enter medicine name.'));
        }
       /* 
        if (isset($data['doctor'])) {
            $this->validators['doctor'] = array(
                'NotEmpty',
                'messages' => array('Please enter doctor name.'));
        }
        
        if (isset($data['date_time']) && ($data['appointment_type'] !=0)) {
            
            $this->validators['date_time'] = array(
                'NotEmpty',
                'messages' => array('Please enter date and time.'));
        }
        if (isset($data['p_name'])) {
            $this->validators['p_name'] = array(
                'NotEmpty',
                'messages' => array('Please enter name.'));
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
                'messages' => array('Please enter address.'));
        }
        
        if (isset($data['note'])) {
            $this->validators['note'] = array(
                'NotEmpty',
                'messages' => 'Please enter note.');
        }*/
    }
    
    
    
    
    
    public function deleteappointmentAction() {
        global $nowDateTime;
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $request = $this->getRequest();
        if ($request->getPost()) {
            $data = $this->__inputPostData;
            //dd($data);
            $status= $this->_appointmentResource->deleteAppointmentData($data['id']);
            
            echo 1;
        }
        die;
    }
    
     public function updatestatusAction() {
        $this->_helper->viewRenderer->setNoRender(true);
        $data = $this->getRequest()->getPost();
        
        global $nowDateTime;

        if ($data['status'] != '') {
            $id = $data['id'];
            //$rparam['remarks'] = $data['remarks'];
            $rparam['status'] = $data['status'];
            
            
            $this->_appointmentResource->updateAppointmentDetail($rparam,$id);
            
            echo '1';
            die;
        }
    }
    
    public function textConsultationAddeditAction() {
        
       // global $status;

        //$this->view->status = $status;
        $pid = $this->getRequest()->getParam('patient_id');
        $appntId = $this->getRequest()->getParam('appnt_id');
        
        $this->view->pid = $pid;
        $this->view->appntId = $appntId;
        
        $this->view->doctorData = $this->_doctorResource->fetchAllDoctorData();
         //   dd($this->view->doctorData);
        $this->view->dertData = $this->_departmentResource->fetchAllDepartments();
        if (!empty($pid) && !empty($appntId)) {

            $appntData = $this->_appointmentResource->fetchAppointmentData($pid,$appntId);
            //dd($appntData);
            $formdata = array(
                'id'=> $appntData['id'],
                'patient_id'=> $appntData['patient_id'],
                //'appointment_type'=> $appntData['appointment_type'],
                'department_id'=> $appntData['department_id'],
                'healthcare_provider_id'=> $appntData['healthcare_provider_id'],
                'appoinment_datetime'=> $this->changeDateFormat($appntData['appoinment_datetime'], DATETIMEFORMAT,DATE_TIME_FORMAT),
                'reason_for_appointment'=> $appntData['reason_for_appointment'],
                'name'=> $appntData['name'],
                'contact_no'=> $appntData['contact_no'],
                'email'=> $appntData['email'],
                'address'=> $appntData['address']
                
            );
//dd($formdata);
            $this->view->formdata = $formdata;
            
        }
       
    }
    
    public function clinicConsultationAddeditAction() {
        
       // global $status;

        //$this->view->status = $status;
        $pid = $this->getRequest()->getParam('patient_id');
        $appntId = $this->getRequest()->getParam('appnt_id');
        
        $this->view->pid = $pid;
        $this->view->appntId = $appntId;
        
        $this->view->doctorData = $this->_doctorResource->fetchAllDoctorData();
         //   dd($this->view->doctorData);
        $this->view->dertData = $this->_departmentResource->fetchAllDepartments();
        if (!empty($pid) && !empty($appntId)) {

            $appntData = $this->_appointmentResource->fetchAppointmentData($pid,$appntId);
            //dd($appntData);
            $formdata = array(
                'id'=> $appntData['id'],
                'patient_id'=> $appntData['patient_id'],
                'appointment_type'=> $appntData['appointment_type'],
                'department_id'=> $appntData['department_id'],
                'healthcare_provider_id'=> $appntData['healthcare_provider_id'],
                'appoinment_datetime'=> $this->changeDateFormat($appntData['appoinment_datetime'], DATETIMEFORMAT,DATE_TIME_FORMAT),
                'reason_for_appointment'=> $appntData['reason_for_appointment'],
                'name'=> $appntData['name'],
                'contact_no'=> $appntData['contact_no'],
                'email'=> $appntData['email'],
                'address'=> $appntData['address']
                
            );
//dd($formdata);
            $this->view->formdata = $formdata;
            
        }
       
    }

    
    public function consultationNoteAction() {
        
       // global $status;

        //$this->view->status = $status;
        //$pid = $this->getRequest()->getParam('patient_id');
        $appntId = $this->getRequest()->getParam('appnt_id');
        $this->view->pf = $this->getRequest()->getParam('pf');
        
        //$this->view->pid = $pid;
        $this->view->appntId = $appntId;
        
        if (!empty($appntId)) {

            $appntData = $this->_appointmentResource->fetchAppointmentData('',$appntId);
            //dd($appntData);
            $formdata = array(
                'id'=> $appntData['id'],
                'patient_id'=> $appntData['patient_id'],
                //'appointment_type'=> $appntData['appointment_type'],
                'department_id'=> $appntData['department_id'],
                'healthcare_provider_id'=> $appntData['healthcare_provider_id'],
                'appoinment_datetime'=> $this->changeDateFormat($appntData['appoinment_datetime'], DATETIMEFORMAT,DATE_TIME_FORMAT),
                'consultancy_note'=> $appntData['consultancy_note'],
                'name'=> $appntData['name'],
                'contact_no'=> $appntData['contact_no'],
                'email'=> $appntData['email'],
                'address'=> $appntData['address']
                
            );
//dd($formdata);
            $this->view->formdata = $formdata;
            
        }
       
    }
    
    public function saveappoinmentnoteAction() {
        
        
    
        $request = $this->getRequest();
        
        if ($data = $request->getPost()) {
                                
            $params = $this->__inputPostData;
            
            //$pid = $params['patient_id'];
            $appntId = $params['appnt_id'];
            $pf = $params['pf'];
            
            if (!empty($appntId)) {
                $this->validateAppoinmentNoteData($params, $this->errMsg);
                $inputData = new Zend_Filter_Input($this->filters, $this->validators);
                $inputData->setData($params);
                
                if ($inputData->isValid()) {
                                    
                    $appntData = array(
                                
                                'consultancy_note' => $params['note']
                            );
                    // dd($appntId);   
                    $updateA = $this->_appointmentResource->updateAppointmentDetail($appntData,$appntId);

                        
                    if ($updateA) {
                        
                           
                        $this->_helper->FlashMessenger()->setNamespace('success')->addMessage('Consultancy note successfully saved.');                       
                        
                        if($pf == 1){
                            $this->_redirect(PATIENT_BASE_URL . 'appointment/index');
                     
                        }elseif ($pf == 2) {
                            $this->_redirect(PATIENT_BASE_URL . 'appointment/consultancy-note-list');
                     
                        }
                        
                       
                    } else {
                       
                        $this->_helper->FlashMessenger()->setNamespace('error')->addMessage('Consultancy note not saved.');                  
                        $this->_redirect(PATIENT_BASE_URL . 'appointment/consultation-note/patient_id/'.$pid.'/appnt_id/'.$appntId);
                        
                    }
                  
                    
                }else{
                    
                    $msg = $this->getValidatorErrors($inputData->getMessages(), 'doctor');               
                    $this->_helper->FlashMessenger()->setNamespace('error')->addMessage($msg); 
                    $this->_redirect(PATIENT_BASE_URL . 'appointment/consultation-note/patient_id/'.$pid.'/appnt_id/'.$appntId);

                    
                
                }
                  
            }
       
        }
    }
    
    protected function validateAppoinmentNoteData($data, $errMsg) {

       
        if (isset($data['note'])) {
            $this->validators['note'] = array(
                'NotEmpty',
                'messages' => 'Please enter consultation note.');
        }
    }
    
    public function consultancyNoteListAction() {

       global $nowDateTime;
       global $statusType;
       global $appointmentType;
        $request = $this->getRequest();
        global $noRecord;
        if ($request->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $data = $this->getRequest()->getPost();
            $params = $this->getSearchParams($data);
            
            
            $params['fields']['main'] = array('id','patient_id','appointment_type','appoinment_datetime','reason_for_appointment','consultancy_note','status');
            $params['sidx'] = 'id';
            $params['sord'] = 'DESC';
           
            
            $result = $this->_appointmentResource->allAppointments($params);
            
            $responce = new stdClass();
            $responce->page = $result['page'];
            $responce->total = $result['total'];
            $responce->records = $result['records'];


            foreach ($result['result'] as $k => $val) {
                
                $id = $val['id'];
                $pid = $val['patient_id'];                           
                $responce->rows[$k]['id'] = $id;               
                $responce->rows[$k]['cell'] = array(
                    $id,                    
                    $val['patient_id'],
                    $val['name'],
                    $appointmentType[$val['appointment_type']],
                    //$this->changeDateFormat($val['appoinment_datetime'], DATETIMEFORMAT,DATE_TIME_FORMAT),                                      
                    //$val['reason_for_appointment'],
                    $val['consultancy_note'],
                    //$this->changeDateFormat($val['created_at'], DATETIMEFORMAT,DATE_TIME_FORMAT),                   
                    $statusType[$val['status']]
                );
            }
            echo $this->jsonEncode($responce);
            exit;
        }
    }
    
      public function deleteappointmentnoteAction() {
        $this->_helper->viewRenderer->setNoRender(true);
        $data = $this->getRequest()->getPost();
        
        global $nowDateTime;

        //if ($data['status'] != '') {
            $id = $data['id'];
            //$rparam['remarks'] = $data['remarks'];
            $rparam['consultancy_note'] = '';
            
            
            $this->_appointmentResource->updateAppointmentDetail($rparam,$id);
            
            echo '1';
            die;
       // }
    }
    
    public function videocallAction() {
        
        
        
    }

}

?>