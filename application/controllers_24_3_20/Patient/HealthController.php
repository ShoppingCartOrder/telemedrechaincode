<?php

class Patient_HealthController extends Mylib_Controller_PatientbaseController {

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

    public function savehealthprofileAction() {

        // global $status;
        $request = $this->getRequest();
        if ($data = $request->getPost()) {
            //dd($data);
            $params = $this->__inputPostData;
            //print_r($params); die;
            //$response = array('status' => 0, 'msg' => '');
            $this->validateHealthProfileData($params, $this->errMsg);
            $inputData = new Zend_Filter_Input($this->filters, $this->validators);
            $inputData->setData($params);
            if ($inputData->isValid()) {


                //dd($params);
                if (!empty($params['id'])) {

                    $healthProfileData = array(
                        'patient_id' => $this->patientNamespace->patient_id,
                        'health_condition' => $params['health_condition'],
                        'diagnosed_in' => $params['diagnosed_in'],
                        'medicine_names' => $params['medicine_names'],
                        'doctor_name' => $params['doctor_name'],
                        'note' => $params['note']
                    );

                    $update = $this->_healthProfileResource->updateHealthProfileDetail($healthProfileData, $params['id']);

                    if ($update) {
                        $this->_helper->FlashMessenger()->setNamespace('success')->addMessage('Health Profile record successfully updated.');
                        $this->_redirect(PATIENT_BASE_URL . 'health/health-profile');
                    } else {
                        //$this->view->msg = 'Patient record not updated.';
                        $this->_helper->FlashMessenger()->setNamespace('error')->addMessage('Health Profile record not updated.');
                        $this->_redirect(PATIENT_BASE_URL . 'health/health-profile-add-edit/id/' . $params['id']);
                    }
                } else { //Save new Patient
                    //$email = $params['p_email'];
//SSdd($params);
                    $healthProfileData = array(
                        'patient_id' => $this->patientNamespace->patient_id,
                        'health_condition' => $params['health_condition'],
                        'diagnosed_in' => $params['diagnosed_in'],
                        'medicine_names' => $params['medicine_names'],
                        'doctor_name' => $params['doctor_name'],
                        'note' => $params['note']
                    );

                    $insertedId = $this->_healthProfileResource->addHealthProfile($healthProfileData);
//dd($insertedId);
                    if ($insertedId) {

                        $this->_helper->FlashMessenger()->setNamespace('success')->addMessage('Health Profile record successfully saved.');

                        $this->_redirect(PATIENT_BASE_URL . 'health/health-profile');
                    } else {
                        $this->_helper->FlashMessenger()->setNamespace('error')->addMessage('Health Profile record is not inserted.Please try again.');
                        $this->_redirect(PATIENT_BASE_URL . 'health/health-profile-add-edit');
                        /// $this->view->msg = 'Patient record is not inserted.Please try again.';
                    }
                }
            } else {

                $msg = $this->getValidatorErrors($inputData->getMessages(), 'admin');

                $this->_helper->FlashMessenger()->setNamespace('error')->addMessage($msg);

                if ($params['id']) {
                    $this->_redirect(PATIENT_BASE_URL . 'health/health-profile-add-edit/id/' . $params['id']);
                } else {
                    $this->_redirect(PATIENT_BASE_URL . 'health/health-profile-add-edit');
                }

                //$this->view->msg = $msg;
            }
        }

        die;
    }

    protected function validateHealthProfileData($data, $errMsg) {

        if (isset($data['health_condition'])) {
            $this->validators['health_condition'] = array(
                'NotEmpty',
                'messages' => array('Please enter health condition.'));
        }


        if (isset($data['diagnosed_in'])) {
            $this->validators['diagnosed_in'] = array(
                'NotEmpty',
                'messages' => 'Please enter diagnose.');
        }
    }

    public function medicalReportDetailsAddeditAction() {
        $loginId = $this->patientNamespace->loginId;
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

    public function savemedicalreportAction() {


        // global $status;
        $request = $this->getRequest();
        if ($data = $request->getPost()) {

            $params = $this->__inputPostData;

            $id = $params['id'];


            //$response = array('status' => 0, 'msg' => '');
            $this->validateMedicalReportData($params, $this->errMsg);
            $inputData = new Zend_Filter_Input($this->filters, $this->validators);
            $inputData->setData($params);

            //print_r($params); die;

            if ($inputData->isValid()) {

                $newfilename = '';

                $medicalReportfile = $_FILES["medical_report_filename"]["name"];
                if ((!empty($_FILES["medical_report_filename"])) && ($_FILES['medical_report_filename']['error'] == 0)) {

                    $filename = basename($_FILES['medical_report_filename']['name']);
                    $ext = substr($filename, strrpos($filename, '.') + 1);
                    if ((($ext == "pdf") || ($ext == "jpg") || ($ext == "jpg")) && ($_FILES["medical_report_filename"]["size"] < 500000000)) {
                        $randString = $this->randomWithLength(4);
                        $newfilename = $randString . '-' . $filename;
                        $newname = UPLOAD_REPORT_FILE_PATH . $newfilename; //VENDOR_BULK_DATA_FILE. $filename;
                        //echo $newname; die;
                        if (!move_uploaded_file($_FILES['medical_report_filename']['tmp_name'], $newname)) {

                            $this->_helper->FlashMessenger()->setNamespace('error')->addMessage('Error occured while uploading file.');
                            $this->_redirect(PATIENT_BASE_URL . 'health/medical-report-details-addedit/id/' . $id);
                        }else{
                            
                            
                            //chdir(UPLOAD_REPORT_FILE_PATH);
                            //$output = shell_exec('ls'); 
                            // Display the list of all file 
                            // and directory 
                            //echo "<pre>$output</pre>"; die;
                             $command = "ipfs add $newfilename"; 
                            echo $output = shell_exec("ipfs add cookie.txt 2>&1"); die;
                            $logFileName = str_replace('.'.$ext, '.txt', $newfilename);
                            $log_fo = fopen('uploadLogfiles/'.$logFileName, 'w+') or die("Can't open file");
                            $fwrite = fwrite($log_fo, $output);
                            if ($fwrite === false) {
                                echo "Not written in file";
                            }
                            chdir(PUBLIC_PATH);
                            
                        }
                    } else {

                        $this->_helper->FlashMessenger()->setNamespace('error')->addMessage('Invalid file.');
                        $this->_redirect(PATIENT_BASE_URL . 'health/medical-report-details-addedit/id/' . $id);
                    }
                }





                //dd($params);
                if (!empty($id)) {




                    $prescriptionData = array(
                        'patient_id' => $this->patientNamespace->patient_id,
                        'test_name' => $params['test_name'],
                        'test_date' => $this->changeDateFormat($params['test_date'], DATE_TIME_FORMAT, DATETIMEFORMAT),
                        'test_result' => $params['test_result'],
                        //'medical_report_filename'=> $newfilename,
                        'note' => $params['note'],
                        'prescribed_by' => $params['prescribed_by'],
                        'test_center' => $params['test_center']
                    );

                    if ($newfilename != '') {
                        $prescriptionData['medical_report_filename'] = $newfilename;
                    }

                    //dd($patientData);
                    $updateP = $this->_medicalTestReportResource->updatePatientMedicalTestReport($prescriptionData, $id);



                    if (($updateP)) {


                        $this->_helper->FlashMessenger()->setNamespace('success')->addMessage('Test report successfully updated.');

                        $this->_redirect(PATIENT_BASE_URL . 'health/medical-report-details');

                        //$this->_redirect(PATIENT_BASE_URL . 'patient/addedit/id/'.$params['id']);
                    } else {

                        $this->_helper->FlashMessenger()->setNamespace('error')->addMessage('Test report not updated.');
                        $this->_redirect(PATIENT_BASE_URL . 'health/medical-report-details-addedit/id/' . $id);
                    }
                    //$this->_redirect(PATIENT_BASE_URL . 'appointment/video-consultation-addedit/patient_id/'.$pid.'/appnt_id/'.$id);
                } else {


                    $prescriptionData = array(
                        'patient_id' => $this->patientNamespace->patient_id,
                        'test_name' => $params['test_name'],
                        'test_date' => $this->changeDateFormat($params['test_date'], DATE_TIME_FORMAT, DATETIMEFORMAT),
                        'test_result' => $params['test_result'],
                        //'medical_report_filename'=> $newfilename,
                        'note' => $params['note'],
                        'prescribed_by' => $params['prescribed_by'],
                        'test_center' => $params['test_center']
                    );

                    if ($newfilename != '') {
                        $prescriptionData['medical_report_filename'] = $newfilename;
                    }
                    //dd($prescriptionData);
                    $insertedId = $this->_medicalTestReportResource->addPatientMedicalTestReport($prescriptionData);


                    if ($insertedId) {

                        $this->_helper->FlashMessenger()->setNamespace('success')->addMessage('Test report successfully saved.');
                        $this->_redirect(PATIENT_BASE_URL . 'health/medical-report-details');
                    } else {
                        $this->_helper->FlashMessenger()->setNamespace('error')->addMessage('Test report not successfully saved.');
                        $this->_redirect(PATIENT_BASE_URL . 'health/medical-report-details-addedit/id/' . $id);
                        /// $this->view->msg = 'Patient record is not inserted.Please try again.';
                    }
                }
            } else {

                $msg = $this->getValidatorErrors($inputData->getMessages(), 'admin');

                $this->_helper->FlashMessenger()->setNamespace('error')->addMessage($msg);


                if (!empty($id)) {
                    //dd($actionName);
                    $this->_redirect(PATIENT_BASE_URL . 'health/medical-report-details-addedit/id/' . $id);
                } else {
                    $this->_redirect(PATIENT_BASE_URL . 'health/medical-report-details-addedit');
                }

                //$this->view->msg = $msg;
            }
        }




        die;
    }

    protected function validateMedicalReportData($data, $errMsg) {

        if (isset($data['hospital_name'])) {
            $this->validators['hospital_name'] = array(
                'NotEmpty',
                'messages' => array('Please enter hospital name.'));
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
          } */
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
        global $noRecord;
        if ($request->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $data = $this->getRequest()->getPost();
            $params = $this->getSearchParams($data);


            $params['fields']['main'] = array('*');
            $params['sidx'] = 'id';
            $params['sord'] = 'DESC';


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
                //$responce->rows[$k]['appointment_type'] = $val['appointment_type'];
                $actionUpdate = "<a onclick='return testReportEdit($id);' href='javascript:void(0);'><span class='far fa-edit' aria-hidden='true'></span></a>";
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
                        'patient_id' => $this->patientNamespace->patient_id,
                        'blood_pressure' => $params['blood_pressure'],
                        'sugar' => $params['sugar'],
                        'heart_beat' => $params['heart_beat'],
                        'bmi' => $params['bmi'],
                        'temperature' => $params['temperature']
                    );
                    //dd($patientData);
                    $updateP = $this->_vitalDetailsResource->updatePatientVitalDetails($vitalData, $id);



                    if (($updateP)) {


                        $this->_helper->FlashMessenger()->setNamespace('success')->addMessage('Vital record successfully updated.');

                        $this->_redirect(PATIENT_BASE_URL . 'health/vital-details');

                        //$this->_redirect(PATIENT_BASE_URL . 'patient/addedit/id/'.$params['id']);
                    } else {

                        $this->_helper->FlashMessenger()->setNamespace('error')->addMessage('Vital record not updated.');
                        $this->_redirect(PATIENT_BASE_URL . 'health/vital-details-addedit/id/' . $id);
                    }
                    //$this->_redirect(PATIENT_BASE_URL . 'appointment/video-consultation-addedit/patient_id/'.$pid.'/appnt_id/'.$id);
                } else {


                    $vitalData = array(
                        'patient_id' => $this->patientNamespace->patient_id,
                        'blood_pressure' => $params['blood_pressure'],
                        'sugar' => $params['sugar'],
                        'heart_beat' => $params['heart_beat'],
                        'bmi' => $params['bmi'],
                        'temperature' => $params['temperature']
                    );
                    //dd($vitalData);
                    $insertedId = $this->_vitalDetailsResource->addpatientVitalDetails($vitalData);


                    if ($insertedId) {

                        $this->_helper->FlashMessenger()->setNamespace('success')->addMessage('Vital Data successfully saved.');
                        $this->_redirect(PATIENT_BASE_URL . 'health/vital-details');
                    } else {
                        $this->_helper->FlashMessenger()->setNamespace('error')->addMessage('Vital Data not successfully saved.');
                        $this->_redirect(PATIENT_BASE_URL . 'health/vital-details-addedit/id/' . $id);
                        /// $this->view->msg = 'Patient record is not inserted.Please try again.';
                    }
                }
            } else {

                $msg = $this->getValidatorErrors($inputData->getMessages(), 'admin');

                $this->_helper->FlashMessenger()->setNamespace('error')->addMessage($msg);


                if (!empty($id)) {
                    //dd($actionName);
                    $this->_redirect(PATIENT_BASE_URL . 'health/vital-details-addedit/id/' . $id);
                } else {
                    $this->_redirect(PATIENT_BASE_URL . 'health/vital-details-addedit');
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
                        'patient_id' => $this->patientNamespace->patient_id,
                        'hospital_name' => $params['hospital_name'],
                        'doctor_name' => $params['doctor_name'],
                        'desease_name' => $params['desease_name'],
                        'note' => $params['note']
                    );


                    //dd($patientData);
                    $updateP = $this->_hospitalDetailsResource->updatePatientHospitalDetails($hospitalData, $id);



                    if (($updateP)) {


                        $this->_helper->FlashMessenger()->setNamespace('success')->addMessage('Hospital record successfully updated.');

                        $this->_redirect(PATIENT_BASE_URL . 'health/hospital-details');

                        //$this->_redirect(PATIENT_BASE_URL . 'patient/addedit/id/'.$params['id']);
                    } else {

                        $this->_helper->FlashMessenger()->setNamespace('error')->addMessage('Hospital record not updated.');
                        $this->_redirect(PATIENT_BASE_URL . 'health/hospital-details-addedit/id/' . $id);
                    }
                    //$this->_redirect(PATIENT_BASE_URL . 'appointment/video-consultation-addedit/patient_id/'.$pid.'/appnt_id/'.$id);
                } else {


                    $hospitalData = array(
                        'patient_id' => $this->patientNamespace->patient_id,
                        'hospital_name' => $params['hospital_name'],
                        'doctor_name' => $params['doctor_name'],
                        'desease_name' => $params['desease_name'],
                        'note' => $params['note']
                    );
                    //dd($hospitalData);
                    $insertedId = $this->_hospitalDetailsResource->addpatientHospitalDetails($hospitalData);


                    if ($insertedId) {

                        $this->_helper->FlashMessenger()->setNamespace('success')->addMessage('Hospital Data successfully saved.');
                        $this->_redirect(PATIENT_BASE_URL . 'health/hospital-details');
                    } else {
                        $this->_helper->FlashMessenger()->setNamespace('error')->addMessage('Hospital Data not successfully saved.');
                        $this->_redirect(PATIENT_BASE_URL . 'health/hospital-details-addedit/id/' . $id);
                        /// $this->view->msg = 'Patient record is not inserted.Please try again.';
                    }
                }
            } else {

                $msg = $this->getValidatorErrors($inputData->getMessages(), 'admin');

                $this->_helper->FlashMessenger()->setNamespace('error')->addMessage($msg);


                if (!empty($id)) {
                    //dd($actionName);
                    $this->_redirect(PATIENT_BASE_URL . 'health/hospital-details-addedit/id/' . $id);
                } else {
                    $this->_redirect(PATIENT_BASE_URL . 'health/hospital-details-addedit');
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
                        'patient_id' => $this->patientNamespace->patient_id,
                        'medicine_name' => $params['medicine_name'],
                        'dosage' => $params['dosage'],
                        'duration' => $params['duration'],
                        'side_effects' => $params['side_effects']
                    );


                    //dd($patientData);
                    $updateP = $this->_prescriptionDetailsResource->updatePatientPrescriptionDetails($prescriptionData, $id);



                    if (($updateP)) {


                        $this->_helper->FlashMessenger()->setNamespace('success')->addMessage('Prescription record successfully updated.');

                        $this->_redirect(PATIENT_BASE_URL . 'health/prescription-details');

                        //$this->_redirect(PATIENT_BASE_URL . 'patient/addedit/id/'.$params['id']);
                    } else {

                        $this->_helper->FlashMessenger()->setNamespace('error')->addMessage('Prescription record not updated.');
                        $this->_redirect(PATIENT_BASE_URL . 'health/prescription-details-addedit/id/' . $id);
                    }
                    //$this->_redirect(PATIENT_BASE_URL . 'appointment/video-consultation-addedit/patient_id/'.$pid.'/appnt_id/'.$id);
                } else {


                    $prescriptionData = array(
                        'patient_id' => $this->patientNamespace->patient_id,
                        'medicine_name' => $params['medicine_name'],
                        'dosage' => $params['dosage'],
                        'duration' => $params['duration'],
                        'side_effects' => $params['side_effects']
                    );
                    //dd($hospitalData);
                    $insertedId = $this->_prescriptionDetailsResource->addpatientPrescriptionDetails($prescriptionData);


                    if ($insertedId) {

                        $this->_helper->FlashMessenger()->setNamespace('success')->addMessage('Prescription Data successfully saved.');
                        $this->_redirect(PATIENT_BASE_URL . 'health/prescription-details');
                    } else {
                        $this->_helper->FlashMessenger()->setNamespace('error')->addMessage('Prescription Data not successfully saved.');
                        $this->_redirect(PATIENT_BASE_URL . 'health/prescription-details-addedit/id/' . $id);
                        /// $this->view->msg = 'Patient record is not inserted.Please try again.';
                    }
                }
            } else {

                $msg = $this->getValidatorErrors($inputData->getMessages(), 'admin');

                $this->_helper->FlashMessenger()->setNamespace('error')->addMessage($msg);


                if (!empty($id)) {
                    //dd($actionName);
                    $this->_redirect(PATIENT_BASE_URL . 'health/prescription-details-addedit/id/' . $id);
                } else {
                    $this->_redirect(PATIENT_BASE_URL . 'health/prescription-details-addedit');
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

    public function savejournalAction() {


        // global $status;
        $request = $this->getRequest();
        if ($data = $request->getPost()) {

            $params = $this->__inputPostData;

            $id = $params['id'];


            //$response = array('status' => 0, 'msg' => '');
            $this->validateJournalData($params, $this->errMsg);
            $inputData = new Zend_Filter_Input($this->filters, $this->validators);
            $inputData->setData($params);

            //print_r($params); die;

            if ($inputData->isValid()) {
                
                $newfilename = '';

                $medicalReportfile = $_FILES["file_name"]["name"];
                if ((!empty($_FILES["file_name"])) && ($_FILES['file_name']['error'] == 0)) {

                    $filename = basename($_FILES['file_name']['name']);
                    $ext = substr($filename, strrpos($filename, '.') + 1);
                    if ((($ext == "pdf") || ($ext == "jpg") || ($ext == "jpg")) && ($_FILES["file_name"]["size"] < 500000000)) {
                        $randString = $this->randomWithLength(5);
                        $newfilename = $randString . '--' . $filename;
                        $newname = UPLOAD_REPORT_FILE_PATH . $newfilename; //VENDOR_BULK_DATA_FILE. $filename;
                        //echo $newname; die;
                        if (!move_uploaded_file($_FILES['file_name']['tmp_name'], $newname)) {

                            $this->_helper->FlashMessenger()->setNamespace('error')->addMessage('Error occured while uploading file.');
                            $this->_redirect(PATIENT_BASE_URL . 'health/journal-addedit/id/' . $id);
                        }
                    } else {

                        $this->_helper->FlashMessenger()->setNamespace('error')->addMessage('Invalid file.');
                        $this->_redirect(PATIENT_BASE_URL . 'health/journal-addedit/id/' . $id);
                    }
                }
                if (!empty($id)) {



                    $journalData = array(
                        'patient_id' => $this->patientNamespace->patient_id,
                        'journal' => $params['journal']
                        //'file_name' => $newfilename
                    );

                    if ($newfilename != '') {
                        $journalData['file_name'] = $newfilename;
                    }
                    //dd($patientData);
                    $updateP = $this->_journalResource->updateHealthJournal($journalData, $id);



                    if (($updateP)) {


                        $this->_helper->FlashMessenger()->setNamespace('success')->addMessage('Journal successfully updated.');

                        $this->_redirect(PATIENT_BASE_URL . 'health/journal');

                        //$this->_redirect(PATIENT_BASE_URL . 'patient/addedit/id/'.$params['id']);
                    } else {

                        $this->_helper->FlashMessenger()->setNamespace('error')->addMessage('Journal not updated.');
                        $this->_redirect(PATIENT_BASE_URL . 'health/journal-addedit/id/' . $id);
                    }
                    //$this->_redirect(PATIENT_BASE_URL . 'appointment/video-consultation-addedit/patient_id/'.$pid.'/appnt_id/'.$id);
                } else {


                   $journalData = array(
                        'patient_id' => $this->patientNamespace->patient_id,
                       'journal' => $params['journal']
                       // 'file_name' => $newfilename
                    );
                    if ($newfilename != '') {
                        $journalData['file_name'] = $newfilename;
                    }
                    $insertedId = $this->_journalResource->addHealthJournal($journalData);


                    if ($insertedId) {

                        $this->_helper->FlashMessenger()->setNamespace('success')->addMessage('Journal successfully saved.');
                        $this->_redirect(PATIENT_BASE_URL . 'health/journal');
                    } else {
                        $this->_helper->FlashMessenger()->setNamespace('error')->addMessage('Journal not successfully saved.');
                        $this->_redirect(PATIENT_BASE_URL . 'health/journal-addedit/id/' . $id);
                        /// $this->view->msg = 'Patient record is not inserted.Please try again.';
                    }
                }
            } else {

                $msg = $this->getValidatorErrors($inputData->getMessages(), 'admin');

                $this->_helper->FlashMessenger()->setNamespace('error')->addMessage($msg);


                if (!empty($id)) {
                    //dd($actionName);
                    $this->_redirect(PATIENT_BASE_URL . 'health/journal-addedit/id/' . $id);
                } else {
                    $this->_redirect(PATIENT_BASE_URL . 'health/journal-addedit');
                }

                //$this->view->msg = $msg;
            }
        }




        die;
    }

    protected function validateJournalData($data, $errMsg) {

        if (isset($data['journal'])) {
            $this->validators['journal'] = array(
                'NotEmpty',
                'messages' => array('Please enter value.'));
        }

        if (isset($data['file_name'])) {
            $this->validators['file_name'] = array(
                'NotEmpty',
                'messages' => array('Please select file.'));
        }
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

}

?>