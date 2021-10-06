<?php

class Patient_AppointmentController extends Mylib_Controller_PatientbaseController {

    //protected $_dashboardResource;

    public function init() {
        parent::init();
        $this->_patientResource = new Application_Model_DbTable_Patients();
        $this->_appointmentResource = new Application_Model_DbTable_Appointments();
        $this->_userResource = new Application_Model_DbTable_Users();
        $this->_doctorResource = new Application_Model_DbTable_Doctors();
        $this->_departmentResource = new Application_Model_DbTable_Departments();
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
       global $appointmentType;
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
            $params['userRoleType'] = PATIENT_ROLE;  
            $result = $this->_appointmentResource->allAppointments($params);
            
            $responce = new stdClass();
            $responce->page = $result['page'];
            $responce->total = $result['total'];
            $responce->records = $result['records'];


            foreach ($result['result'] as $k => $val) {
                
                //$statusUpdate = '';
                //$actionUpdate = '';
                $id = $val['id'];
                $pid = $val['patient_id'];
                $apIDs = $val['appointment_type'].'_'.$id.'_'.$pid;
                $selectStatus = '';
                $selectStatus .= "<select id='status_$id' name='$apIDs'>";
                foreach ($statusType as $key => $statusVal) {
                   
                    $selected = '';

                    if ($key == $val['status']) {
                        $selected = 'selected = selected';
                    }
                    $selectStatus .= "<option value='$key' $selected> $statusVal </option>";
                }
                $selectStatus .="</select>";
                
                $responce->rows[$k]['id'] = $id;
                //$responce->rows[$k]['appointment_type'] = $val['appointment_type'];
//                $actionUpdate = "<a onclick='return roleResource($id);' href='javascript:void(0);'><img width='15' height='15' src='".ADMIN_IMAGE_URL."related_cat.png' alt='Role Resources' title='Role Resources'></a>";               
//                if($val['status'] == 1){
//                    $statusUpdate = "<a onclick='return roleStatus($id,0);' href='javascript:void(0);'><img width='15' height='15' src='".ADMIN_IMAGE_URL."icon_activate.gif' alt='Role status' title='Role status'></a>";
//                } else {
//                    $statusUpdate = "<a onclick='return roleStatus($id,1);' href='javascript:void(0);'><img width='15' height='15' src='".ADMIN_IMAGE_URL."icon_deactivate.gif' alt='Role status' title='Role status'></a>";
//                }
                
                
                $responce->rows[$k]['cell'] = array(
                    //$id,                    
                    $val['doctor_name'],
                    $val['doctor_contact_no'],
                    $val['doctor_email'],
                    $val['dprt_name'],
                    $appointmentType[$val['appointment_type']],
                    $this->changeDateFormat($val['appoinment_datetime'], DATETIMEFORMAT,DATE_TIME_FORMAT),                   
                    $this->changeDateFormat($val['created_at'], DATETIMEFORMAT,DATE_TIME_FORMAT),                   
                    $selectStatus
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

   


    public function saveappoinmentAction() {
        // global $status;
        //dd($this->patientNamespace->patient_id);
        $request = $this->getRequest();
        if ($data = $request->getPost()) {
                                
            $params = $this->__inputPostData;
            
            $pid = $params['patient_id'];
            $appntId = $params['appnt_id'];
            
            //print_r($params); die;
            //$response = array('status' => 0, 'msg' => '');
            $this->validateAppoinmentData($params, $this->errMsg);
            $inputData = new Zend_Filter_Input($this->filters, $this->validators);
            $inputData->setData($params);
            
            $appointmentType = $params['appointment_type'];
            $actionName = $params['action_name'];
            
            if ($inputData->isValid()) {

                
                //dd($params);
                if (!empty($pid) && !empty($appntId)) {
                     
                    
                    $appDateTime = '';
                    
                    if($appointmentType !=3){
                       $appDateTime = date(DATETIMEFORMAT,strtotime($params['date_time']));                    
                    }
                    $appntData = array(
                                
                                'department_id' => $params['department'], 
                                'healthcare_provider_id' => $params['doctor'],
                                'appoinment_datetime' => $appDateTime,
                                'reason_for_appointment' => $params['note']
                            );
                        
                    $updateA = $this->_appointmentResource->updateAppointmentDetail($appntData,$appntId);

                        
                    if (($updateA)) {
                        
                           
                        $this->_helper->FlashMessenger()->setNamespace('success')->addMessage('Appointment record successfully updated.');
                        
                        $this->_redirect(PATIENT_BASE_URL . 'appointment/index');
                     
                        //$this->_redirect(PATIENT_BASE_URL . 'patient/addedit/id/'.$params['id']);
                        
                    } else {
                       
                        $this->_helper->FlashMessenger()->setNamespace('error')->addMessage('Appointment record not updated.');                  
                        $this->_redirect(PATIENT_BASE_URL . 'appointment/'.$actionName.'/patient_id/'.$pid.'/appnt_id/'.$appntId);
                        
                        
                    }
                    //$this->_redirect(PATIENT_BASE_URL . 'appointment/video-consultation-addedit/patient_id/'.$pid.'/appnt_id/'.$appntId);
                                   
                    } else { //Save new Patient

                           
                                $appDateTime ='';
                                 if($appointmentType !=3){
                                    $appDateTime = date(DATETIMEFORMAT,strtotime($params['date_time']));                    
                                 }
                                $appntData = array(
                                'patient_id' => $this->patientNamespace->patient_id,
                                'appointment_type' => $params['appointment_type'],
                                'department_id' => $params['department'], 
                                'healthcare_provider_id' => $params['doctor'],
                                'appoinment_datetime' => $appDateTime,
                                'reason_for_appointment' => $params['note']
                            );
                                //dd($appntData);
                                $insertedId = $this->_appointmentResource->addAppointment($appntData);
                                $this->_helper->FlashMessenger()->setNamespace('success')->addMessage('Appointment successfully sent.');
                                $this->_redirect(PATIENT_BASE_URL . 'appointment/index');
                        
                     
                        
                         
                    
                }
                } else {

                $msg = $this->getValidatorErrors($inputData->getMessages(), 'admin');
                
                $this->_helper->FlashMessenger()->setNamespace('error')->addMessage($msg); 
                
              
                if (!empty($pid) && !empty($appntId)) {
                    //dd($actionName);
                    $this->_redirect(PATIENT_BASE_URL . 'appointment/'.$actionName.'/appnt_id/'.$appntId);
                }else{
                    $this->_redirect(PATIENT_BASE_URL . 'appointment/'.$actionName);
                }
                
                //$this->view->msg = $msg;
            }
        }
        
       // die;

    }

  

    protected function validateAppoinmentData($data, $errMsg) {

        if (isset($data['department'])) {
            $this->validators['department'] = array(
                'NotEmpty',
                'messages' => array('Please enter department name.'));
        }
        
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
        }
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
    
    public function videocallAction() {
        
        
        
    }
    
     public function clinicConsultationAddeditAction() {
      
        $loginId = $this->patientNamespace->loginId;
        $formdata = array();
        //dd($this->loggedInPatientrDetails);
        if($loginId){
          
           $formdata = array(
                'patient_id' => $this->loggedInPatientrDetails['id'],
                'name' => $this->loggedInPatientrDetails['name'],
                'email' => $this->loginEmailId,
                'contact_no' => $this->loggedInPatientrDetails['contact_no'],
                'address' => $this->loggedInPatientrDetails['address']
            );
           
           $this->view->pid = $pid = $this->loggedInPatientrDetails['id'];
        }
        
        $appntId = $this->getRequest()->getParam('appnt_id');
        $this->view->appntId = $appntId;
        
        $this->view->dertData = $this->_departmentResource->fetchAllDepartments();
        
        if (!empty($pid) && !empty($appntId)) {

            $appntData = $this->_appointmentResource->fetchAppointmentData($pid,$appntId);
            //$formdata = processApmtData($appntData);
            $formdata = array(
                'id'=> $appntData['id'],
                //'patient_id'=> $appntData['patient_id'],
                'appointment_type'=> $appntData['appointment_type'],
                'department_id'=> $appntData['department_id'],
                'healthcare_provider_id'=> $appntData['healthcare_provider_id'],
                'appoinment_datetime'=> $this->changeDateFormat($appntData['appoinment_datetime'], DATETIMEFORMAT,DATE_TIME_FORMAT),
                'reason_for_appointment'=> $appntData['reason_for_appointment']
                //'name'=> $appntData['name'],
                //'contact_no'=> $appntData['contact_no'],
                //'email'=> $appntData['email'],
                //'address'=> $appntData['address']
                
            );
//dd($formdata);
            
           
            
        $this->view->doctorData = $this->_doctorResource->getDoctorByDepartmentId($appntData['department_id']);
            
        }
        $this->view->formdata = $formdata;
       
    }

   
   
    
     public function textConsultationAddeditAction() {
        
         $loginId = $this->patientNamespace->loginId;
        //$formdata = array();
        //dd($this->loggedInPatientrDetails);
        if($loginId){
          $formdata = $this->processApmtData();
          
           $this->view->pid = $pid = $this->loggedInPatientrDetails['id'];
        }
         
         
         
         
         
        //$pid = $this->getRequest()->getParam('patient_id');
        $appntId = $this->getRequest()->getParam('appnt_id');
        
        $this->view->pid = $pid;
        $this->view->appntId = $appntId;
        
        //$this->view->doctorData = $this->_doctorResource->fetchAllDoctorData();
         //   dd($this->view->doctorData);
        $this->view->dertData = $this->_departmentResource->fetchAllDepartments();
        
        if (!empty($pid) && !empty($appntId)) {

            $appntData = $this->_appointmentResource->fetchAppointmentData($pid,$appntId);
            //dd($appntData,true);
            $formdata = $this->processApmtData($appntData);
          
       
            $this->view->doctorData = $this->_doctorResource->getDoctorByDepartmentId($appntData['department_id']);
         
            
        }
         $this->view->formdata = $formdata;
       
    }
    
    public function videoConsultationAddeditAction() {
        
       $loginId = $this->patientNamespace->loginId;
       
       if($loginId){
          $formdata = $this->processApmtData();
          
           $this->view->pid = $pid = $this->loggedInPatientrDetails['id'];
        }
        
        //$pid = $this->getRequest()->getParam('patient_id');
        $appntId = $this->getRequest()->getParam('appnt_id');
        
        $this->view->pid = $pid;
        $this->view->appntId = $appntId;
        
        //$this->view->doctorData = $this->_doctorResource->fetchAllDoctorData();
         //   dd($this->view->doctorData);
        $this->view->dertData = $this->_departmentResource->fetchAllDepartments();
        if (!empty($pid) && !empty($appntId)) {

            $appntData = $this->_appointmentResource->fetchAppointmentData($pid,$appntId);
            $formdata = $this->processApmtData($appntData);
            $this->view->doctorData = $this->_doctorResource->getDoctorByDepartmentId($appntData['department_id']);
         
            
            
        }
        $this->view->formdata = $formdata;
       
    }
    
    
    protected function processApmtData($appntData = array()) {
        
      
           
            
             $formdata = array(
                'id'=> (isset($appntData['id'])?$appntData['id']:''),
                'department_id'=> (isset($appntData['department_id'])?$appntData['department_id']:''),
                'healthcare_provider_id'=> (isset($appntData['healthcare_provider_id'])?$appntData['healthcare_provider_id']:''),
                'appoinment_datetime'=> (isset($appntData['appoinment_datetime'])?$this->changeDateFormat($appntData['appoinment_datetime'], DATETIMEFORMAT,DATE_TIME_FORMAT):''),
                'reason_for_appointment'=> (isset($appntData['reason_for_appointment'])?$appntData['reason_for_appointment']:''),
                'patient_id'=> (isset($this->loggedInPatientrDetails['id'])?$this->loggedInPatientrDetails['id']:$appntData['patient_id']),                
                'name'=> (isset($this->loggedInPatientrDetails['name'])?$this->loggedInPatientrDetails['name']: $appntData['name']),
                'contact_no'=> (isset($this->loggedInPatientrDetails['contact_no'])?$this->loggedInPatientrDetails['contact_no']:$appntData['contact_no']),
                'email'=> (isset($this->loggedInPatientrDetails['email'])?$this->loggedInPatientrDetails['email']:$appntData['email']), 
                'address'=> (isset($this->loggedInPatientrDetails['address'])?$this->loggedInPatientrDetails['address']:$appntData['address'])
                
            );
             
          
            // dd($formdata);
            return $formdata;
            
      
        
        
        
    }
    
    
    
    

}

?>