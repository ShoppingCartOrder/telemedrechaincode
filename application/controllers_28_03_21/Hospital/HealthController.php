<?php

class Doctor_HealthController extends Mylib_Controller_DoctorbaseController {

    //protected $_dashboardResource;

    public function init() {
        parent::init();
        $this->_patientResource = new Application_Model_DbTable_Patients();
        $this->_userResource = new Application_Model_DbTable_Users();
        $this->_healthProfileResource = new Application_Model_DbTable_HealthProfile();
        $this->_medicalTestReportResource = new Application_Model_DbTable_PatientMedicalTestReport();

        $this->_vitalDetailsResource = new Application_Model_DbTable_PatientVitalDetails();
        $this->_hospitalDetailsResource = new Application_Model_DbTable_PatientHospitalDetails();
        $this->_prescriptionDetailsResource = new Application_Model_DbTable_PatientPrescriptionDetails();
        $this->_journalResource = new Application_Model_DbTable_HealthJournal();
    }

    public function healthProfileAction() {

        global $nowDateTime;

        global $healthCondition;
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


            $result = $this->_healthProfileResource->allHealthProfiles($params);

            $responce = new stdClass();
            $responce->page = $result['page'];
            $responce->total = $result['total'];
            $responce->records = $result['records'];


            foreach ($result['result'] as $k => $val) {

                //$statusUpdate = '';
                //$actionUpdate = '';
                $id = $val['id'];
                $pid = $val['patient_id'];


                $responce->rows[$k]['id'] = $id;
                $actionUpdate = '';
                //$responce->rows[$k]['appointment_type'] = $val['appointment_type'];
                $actionUpdate = "<a onclick='return healthProfileEdit($id);' href='javascript:void(0);'><span class='far fa-edit' aria-hidden='true'></span></a>";
//                

                $responce->rows[$k]['cell'] = array(
                    $id,
                    $val['medicine_names'],
                    $val['diagnosed_in'],
                    $val['note'],
                    $val['doctor_name'],
                    $healthCondition[$val['health_condition']],
                    $this->changeDateFormat($val['created_at'], DATETIMEFORMAT, DATE_TIME_FORMAT),
                    $actionUpdate
                );
            }
            echo $this->jsonEncode($responce);
            exit;
        }
    }

    public function healthProfileAddEditAction() {
        global $healthCondition;

        $this->view->healthCondition = $healthCondition;
        $id = $this->getRequest()->getParam('id');
        if (!empty($id)) {

            $patientData = $this->_patientResource->fetchHealthProfileData($id);
            //dd($patientData);
            $formdata = array(
                'id' => $patientData['id'],
                'patient_id' => $patientData['patient_id'],
                'health_condition' => $patientData['health_condition'],
                'diagnosed_in' => $patientData['diagnosed_in'],
                'medicine_names' => $patientData['medicine_names'],
                'doctor_name' => $patientData['doctor_name'],
                'note' => $patientData['note']
            );

            $this->view->formdata = $formdata;
        }
    }

   

  

    public function medicalReportDetailsAddeditAction() {
        $loginId = $this->doctorNamespace->loginId;
        //echo $loginId; die;//
        $id = $this->getRequest()->getParam('id');
        if (!empty($loginId)) {

            $prescriptionData = $this->_medicalTestReportResource->getPatientMedicalTestReport($id);
            //dd($hospitalData);
            $formdata = array(
                'id' => $prescriptionData['id'],
                'patient_id' => $prescriptionData['patient_id'],
                'test_name' => $prescriptionData['test_name'],
                'test_date' => $this->changeDateFormat($prescriptionData['test_date'], DATETIMEFORMAT, DATE_TIME_FORMAT),
                'test_result' => $prescriptionData['test_result'],
                'medical_report_filename' => $prescriptionData['medical_report_filename'],
                'note' => $prescriptionData['note'],
                'prescribed_by' => $prescriptionData['prescribed_by'],
                'test_center' => $prescriptionData['test_center']
            );
//dd($formdata);
            $this->view->formdata = $formdata;
        }
        // die;
    }

  

    public function vitalDetailsAction() {

        global $nowDateTime;


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


            $result = $this->_vitalDetailsResource->allViralDetails($params);

            $responce = new stdClass();
            $responce->page = $result['page'];
            $responce->total = $result['total'];
            $responce->records = $result['records'];


            foreach ($result['result'] as $k => $val) {

                //$statusUpdate = '';
                //$actionUpdate = '';
                $id = $val['id'];
                $pid = $val['patient_id'];


                $responce->rows[$k]['id'] = $id;
                $actionUpdate = '';
                //$responce->rows[$k]['appointment_type'] = $val['appointment_type'];
                $actionUpdate = "<a onclick='return viralDetailsEdit($id);' href='javascript:void(0);'><span class='far fa-edit' aria-hidden='true'></span></a>";
//                

                $responce->rows[$k]['cell'] = array(
                    $id,
                    $val['blood_pressure'],
                    $val['sugar'],
                    $val['heart_beat'],
                    $val['bmi'],
                    $val['temperature'],
                    $this->changeDateFormat($val['created_at'], DATETIMEFORMAT, DATE_TIME_FORMAT),
                    $actionUpdate
                );
            }
            echo $this->jsonEncode($responce);
            exit;
        }
    }

    public function hospitalDetailsAction() {

        global $nowDateTime;


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


            $result = $this->_hospitalDetailsResource->allHospitalDetails($params);

            $responce = new stdClass();
            $responce->page = $result['page'];
            $responce->total = $result['total'];
            $responce->records = $result['records'];


            foreach ($result['result'] as $k => $val) {

                //$statusUpdate = '';
                //$actionUpdate = '';
                $id = $val['id'];
                $pid = $val['patient_id'];


                $responce->rows[$k]['id'] = $id;
                $actionUpdate = '';
                //$responce->rows[$k]['appointment_type'] = $val['appointment_type'];
                $actionUpdate = "<a onclick='return hospitalDetailsEdit($id);' href='javascript:void(0);'><span class='far fa-edit' aria-hidden='true'></span></a>";
//                

                $responce->rows[$k]['cell'] = array(
                    $id,
                    $val['hospital_name'],
                    $val['doctor_name'],
                    $val['desease_name'],
                    $val['note'],
                    $this->changeDateFormat($val['created_at'], DATETIMEFORMAT, DATE_TIME_FORMAT),
                    $actionUpdate
                );
            }
            echo $this->jsonEncode($responce);
            exit;
        }
    }

    public function prescriptionDetailsAction() {

        global $nowDateTime;


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


            $result = $this->_prescriptionDetailsResource->allPrescriptionDetails($params);

            $responce = new stdClass();
            $responce->page = $result['page'];
            $responce->total = $result['total'];
            $responce->records = $result['records'];


            foreach ($result['result'] as $k => $val) {

                //$statusUpdate = '';
                //$actionUpdate = '';
                $id = $val['id'];
                $pid = $val['patient_id'];


                $responce->rows[$k]['id'] = $id;
                $actionUpdate = '';
                //$responce->rows[$k]['appointment_type'] = $val['appointment_type'];
                $actionUpdate = "<a onclick='return prescriptionDetailsEdit($id);' href='javascript:void(0);'><span class='far fa-edit' aria-hidden='true'></span></a>";
//                

                $responce->rows[$k]['cell'] = array(
                    $id,
                    $val['medicine_name'],
                    $val['dosage'],
                    $val['duration'],
                    $val['side_effects'],
                    $this->changeDateFormat($val['created_at'], DATETIMEFORMAT, DATE_TIME_FORMAT),
                    $actionUpdate
                );
            }
            echo $this->jsonEncode($responce);
            exit;
        }
    }

    public function medicalReportDetailsAction() {

        global $nowDateTime;


        $request = $this->getRequest();
        $request = $this->getRequest();
        $this->view->patientId = $patientId = $request->getParam('patient_id');
        
        global $noRecord;
        if ($request->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $data = $this->getRequest()->getPost();
            $params = $this->getSearchParams($data);
            
            $params['fields']['main'] = array('*');
            $params['sidx'] = 'id';
            $params['sord'] = 'DESC';
            $params['userRoleType'] = DOCTOR_ROLE; 
            $params['condition']['patient_id'] = $patientId;

            $result = $this->_medicalTestReportResource->allMedicalTestReportDetails($params);

            $responce = new stdClass();
            $responce->page = $result['page'];
            $responce->total = $result['total'];
            $responce->records = $result['records'];


            foreach ($result['result'] as $k => $val) {

                //$statusUpdate = '';
                //$actionUpdate = '';
                $id = $val['id'];
                $pid = $val['patient_id'];


                $responce->rows[$k]['id'] = $id;
                $actionUpdate = '';
                
                $downloadTestReportUrl = DOCTOR_BASE_URL.'health/medical-report-download/patient_id/'.$val['patient_id'].'/id/'.$val['id'];
                
                //$responce->rows[$k]['appointment_type'] = $val['appointment_type'];
                $actionUpdate = "<a title = 'Download medical Test Report' target = '_blank' href='$downloadTestReportUrl'>Download</a>";
//                

                $responce->rows[$k]['cell'] = array(
                    $id,
                    $val['test_name'],
                    $this->changeDateFormat($val['test_date'], DATETIMEFORMAT, DATE_TIME_FORMAT),
                    $val['test_result'],
                    //$val['medical_report_filename'],
                    $val['note'],
                    $val['prescribed_by'],
                    $val['test_center'],
                    $this->changeDateFormat($val['created_at'], DATETIMEFORMAT, DATE_TIME_FORMAT),
                    $actionUpdate
                );
            }
            echo $this->jsonEncode($responce);
            exit;
        }
    }

    public function vitalDetailsAddeditAction() {

        $id = $this->getRequest()->getParam('id');
        if (!empty($loginId)) {

            $vitalData = $this->_vitalDetailsResource->getPatientVitalDetails($id);
            //dd($patientData);
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

   

    public function journalAddeditAction() {
        $loginId = $this->patientNamespace->loginId;
        //echo $loginId; die;//
        $id = $this->getRequest()->getParam('id');
        if (!empty($loginId)) {

            $journalData = $this->_journalResource->getHealthJournal($id);
            //dd($journalData);
            $formdata = array(
                'id' => $journalData['id'],
                'patient_id' => $journalData['patient_id'],
                'journal' => $journalData['journal'],
                'file_name' => $journalData['file_name']
            );
//dd($formdata);
            $this->view->formdata = $formdata;
        }
        // die;
    }



    
    public function journalAction() {

        global $nowDateTime;


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


            $result = $this->_journalResource->allHealthJournal($params);

            $responce = new stdClass();
            $responce->page = $result['page'];
            $responce->total = $result['total'];
            $responce->records = $result['records'];


            foreach ($result['result'] as $k => $val) {

                //$statusUpdate = '';
                //$actionUpdate = '';
                $id = $val['id'];
                $pid = $val['patient_id'];


                $responce->rows[$k]['id'] = $id;
                $actionUpdate = '';
                $fileName = $val['file_name'];
                $atagUrl = UPLOADED_REPORT_FILE_PATH.$fileName;
                //$responce->rows[$k]['appointment_type'] = $val['appointment_type'];
                $aFileName = "<a target = '_blank' href='$atagUrl'>$fileName</a>";
                $actionUpdate = "<a onclick='return journalEdit($id);' href='javascript:void(0);'><span class='far fa-edit' aria-hidden='true'></span></a>";
//                

                $responce->rows[$k]['cell'] = array(
                    $id,
                    $val['journal'],
                    $aFileName,                                       
                    $this->changeDateFormat($val['created_at'], DATETIMEFORMAT, DATE_TIME_FORMAT),
                    $actionUpdate
                );
            }
            echo $this->jsonEncode($responce);
            exit;
        }
    }
    
     public function medicalReportDownloadAction() {
         
        $request = $this->getRequest();
        $this->view->patientId = $patientId = $request->getParam('patient_id'); 
        $this->view->id = $id = $request->getParam('id');
        if ($data = $request->getPost()) {
                                
            $params = $this->__inputPostData;
            
            $this->view->hashId = $params['hash_id'];
            $this->view->ipfsHashURL = IPFS_BASE_URL.$params['hash_id'];
            
        }
         
     }
     public function downloadTestReportAction() {
         
         $request = $this->getRequest();
         $this->view->hashId = $hashId = $request->getParam('hash_id');
         $this->view->ipfsHashURL = IPFS_BASE_URL.$hashId;
       
         
     }
     
     public function viewTestReportAction() {
         
         $request = $this->getRequest();
         $this->view->hashId = $hashId = $request->getParam('hash_id');
         $this->view->ipfsHashURL = IPFS_BASE_URL.$hashId;
         
     }
     
     
     

}

?>