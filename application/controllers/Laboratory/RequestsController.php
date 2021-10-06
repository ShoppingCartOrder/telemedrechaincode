<?php

class Laboratory_RequestsController extends Mylib_Controller_LaboratorybaseController {

    //protected $_dashboardResource;

    public function init() {
        parent::init();
        $this->_patientResource = new Application_Model_DbTable_Patients();
        $this->_userResource = new Application_Model_DbTable_Users();


        $this->_doctorResource = new Application_Model_DbTable_Doctors();

        $this->_specialityResource = new Application_Model_DbTable_Speciality();
        $this->_chargeItemResource = new Application_Model_DbTable_ChargeItemName();
        $this->_laboratoryResource = new Application_Model_DbTable_Laboratory();
        $this->_laboratoryRequestResource = new Application_Model_DbTable_LaboratoryRequest();
        $this->_laboratoryTestReportResource = new Application_Model_DbTable_LaboratoryTestReport();
        $this->_laboratoryTestResultResource = new Application_Model_DbTable_LaboratoryTestResults();
        //dd($this->laboratoryuserId);
        
        //Appointments
        $this->_appointmentsResource = new Application_Model_DbTable_Appointments();
        
    }

    /**
     * This function is used to show all function in dashboard 
     * Created By :  Bidhan
     * Date : 3 May,2020
     * @param void
     * @return void
     */
    public function indexAction() {
        global $nowDateTime;

        global $arrLabRequestStatus;
        //dd($this->gender);
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


            $result = $this->_laboratoryRequestResource->allLabTestRequest($params);
            //dd($result);
            $responce = new stdClass();
            $responce->page = $result['page'];
            $responce->total = $result['total'];
            $responce->records = $result['records'];


            foreach ($result['result'] as $k => $val) {

                //$statusUpdate = '';
                //$actionUpdate = '';
                $id = $val['id'];
                $appntID = $val['appointment_id'];
                $rId = $val['registration_no'];
                $pId = $val['patient_id'];
                //$prescriptionId = $val['dpid'];
                //$pid = $val['patient_id'];


                $responce->rows[$k]['id'] = $id;

                $selectStatus = '';
                $selectStatus .= "<select id='status_$id' name='status_$id'>";
                foreach ($arrLabRequestStatus as $key => $statusVal) {

                    $selected = '';

                    if ($key == $val['status']) {
                        $selected = 'selected = selected';
                    }
                    $selectStatus .= "<option value='$key' $selected> $statusVal </option>";
                }
                $selectStatus .="</select>";

                /* $actionUpdate = '';
                  if($prescriptionId){

                  $dpURL = LABORATORY_BASE_URL.'doctor-prescribe/addedit/patient_id/'.$id.'/rid/'.$rId.'/id/'.$prescriptionId;
                  $dpname = 'Edit Prescription';
                  }else{
                  $dpURL = LABORATORY_BASE_URL.'doctor-prescribe/addedit/patient_id/'.$id.'/rid/'.$rId;
                  $dpname = 'Add Prescription';
                  }


                  //$responce->rows[$k]['appointment_type'] = $val['appointment_type'];
                  $actionUpdate = "<a onclick='return patientProfileEdit($id);' href='javascript:void(0);'><span class='far fa-edit' aria-hidden='true'></span></a>";
                  $chargeUpdate = "<a onclick='return addPayment($id,$rId);' href='javascript:void(0);'>Add Charges</a>";
                  $prescribeUpdate = '<a  href="'.$dpURL.'" target = "_blank">'.$dpname.'</a>';
                 */
                $printReport = '';
                
                
                $labResultImgPath = WEBSITE_IMAGE . 'lab.png';
                if ($val['test_report_ids']) {
                    $labResultTitle = "View Test Result";
                    $labResultImg = "<img width = '20px' src = '" . $labResultImgPath . "' >";
                } else {

                    $labResultTitle = "Generate Test Result";
                    $labResultImg = " <i class='fa fa-plus' aria-hidden='true'></i>";
                }
                $labResultURL = LABORATORY_BASE_URL . 'requests/lab-test-result-addedit/reqid/' . $id;
                
                if($val['status'] == 1){
                  $printReportUrl = LABORATORY_BASE_URL . 'requests/printlabtestreport/reqid/' . $id;  
                  $printReport =  " <a title = 'Print Test Report' href='" . $printReportUrl . "'><i class='fa fa-print' aria-hidden='true'></i></a>";
                }
                
                
                
                if($val['uploaded_report_filename']){
                  $target = '_blank';  
                  $labUploadTestReportTitle = "View Uploaded Test Result";
                  $labUploadTestReportIcon = " <i class='fa fa-eye' aria-hidden='true'></i>";
                  $labTestReportUploadtURL = UPLOADED_LAB_TEST_REPORT_FILE_PATH .$val['uploaded_report_filename'];
                  
                }else{
                    $target = '';
                     $labUploadTestReportTitle = "Upload Test Report";
                     $labUploadTestReportIcon = " <i class='fa fa-upload' aria-hidden='true'></i>";
                     $labTestReportUploadtURL = LABORATORY_BASE_URL . 'requests/lab-test-report-upload/reqid/' . $id;
                }
                
                $actionUpdate = "<a title = 'Save Status' onclick='return updateStatus($id,$pId);' href='javascript:void(0);'><span class='far fa-save' aria-hidden='true'></span></a>&nbsp;"
                        . "<a title = '" . $labResultTitle . "' href='" . $labResultURL . "'>$labResultImg</a>"
                        . $printReport
                        . " <a target = '" . $target . "' title = '" . $labUploadTestReportTitle . "' href='" . $labTestReportUploadtURL . "'>$labUploadTestReportIcon</a>";


                $responce->rows[$k]['cell'] = array(
                    $id,
                    $appntID,
                    $val['registration_no'],
                    $val['patient_name'],
                    '<a href = "' . $labResultURL . '">' . $val['lab_test_name'] . "</a>",
                    $val['doctor_name'],
                    $this->changeDateFormat($val['created_at'], DATETIMEFORMAT, FRONT_DATE_FORMAT),
                    $selectStatus,
                    $actionUpdate
                );
            }
            echo $this->jsonEncode($responce);
            exit;
        }
    }

    public function searchLabNameAction() {


        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $request = $this->getRequest();
        $term = $request->getParam('query');
        $labTestValue = $this->_laboratoryResource->getLaboratoryTestByName($term);
        $result = array();
        //$alterResult = array();
        foreach ($labTestValue as $key => $val) {


            $result[$key]['label'] = ucwords($val['laboratory_test_name']);

            $result[$key]['id'] = $val['id'];
        }

        //$resultTotal = array_merge($alterResult,$result);
        echo json_encode($result);
        exit;
    }

    public function updatestatusAction() {
        $this->_helper->viewRenderer->setNoRender(true);
        $data = $this->getRequest()->getPost();

        global $nowDateTime;

        if ($data['status'] != '') {
            $id = $data['id'];
            $pid = $data['pid'];
            //$rparam['remarks'] = $data['remarks'];
            $rparam['status'] = $data['status'];

            if ($rparam['status'] == 1) {
                $labTestResults = $this->_laboratoryTestResultResource->fetchLabTestResultByReqId($id);

                if (!empty($labTestResults)) {
                    $rparam['report_completed_date'] = $nowDateTime;
                    $rparam['tested_by'] = $this->lab_id;
                    $this->_laboratoryRequestResource->updateLabRequestStatus($rparam, $id);
                    $patientData = $this->_patientResource->getPatientById($pid);
                    //dd($appnTData);
                    if (!empty($patientData['mobile_no'])) {
                        $arrMno[] = '91' . $patientData['mobile_no'];
                        $PatientAppntSMSTemp = "Your report is ready";
                        $content = $PatientAppntSMSTemp;

                        $this->sendMessage($arrMno, $content);
                    }
                    echo '1';
                    
                }else{
                    
                    echo '2';
                   
                    
                }
            }else{
                echo '3';
                
            }
        }
        die;
    }

    public function labTestReportAddeditAction() {

        global $status;
        $this->view->status = $status;
        $id = $this->getRequest()->getParam('id');
        $reqid = $this->getRequest()->getParam('reqid');
        $this->view->labRequestData = $this->_laboratoryRequestResource->fetchLabRequestByReqId($reqid);
        if (!empty($id)) {

            $labTestResultData = $this->_laboratoryTestReportResource->fetchLabTestResultById($id);
            //dd($patientData);
            $formdata = array(
                'id' => $labTestResultData['id'],
                'measure_unit' => $labTestResultData['measure_unit'],
                'normal_value' => $labTestResultData['normal_value'],
                'result' => $labTestResultData['result'],
            );

            $this->view->formdata = $formdata;
        }
    }

    public function savelabtestreportAction() {

        // global $status;
        $request = $this->getRequest();
        if ($data = $request->getPost()) {
            //dd($data);
            $params = $this->__inputPostData;

            if (!empty($params['reqid'])) {


                $reqid = $params['reqid'];
                $this->validateLabTestReportData($params, $this->errMsg);
                $inputData = new Zend_Filter_Input($this->filters, $this->validators);
                $inputData->setData($params);
                if ($inputData->isValid()) {


                    //dd($params);
                    if (!empty($params['id'])) {
                        $id = $params['id'];

                        $labTestResultData = array(
                            'lab_test_request_id' => $reqid,
                            'measure_unit' => $params['measure_unit'],
                            'normal_value' => $params['normal_value'],
                            'result' => $params['result'],
                             'created_by' => $this->lab_id,
                        );

                        $update = $this->_laboratoryTestReportResource->updateLaboratoryTestReport($labTestResultData, $id);

                        if ($update) {

                            $this->_helper->FlashMessenger()->setNamespace('success')->addMessage('Lab Result data successfully updated.');
                            $this->_redirect(LABORATORY_BASE_URL . 'requests/index');
                        } else {
                            //$this->view->msg = 'Patient record not updated.';
                            $this->_helper->FlashMessenger()->setNamespace('error')->addMessage('Lab Result data not updated.');
                            $this->_redirect(LABORATORY_BASE_URL . 'requests/lab-test-report-addedit/reqid/' . $reqid . '/id/' . $id);
                        }
                    } else { //Save new Patient
                        $labTestResultData = array(
                            'lab_test_request_id' => $reqid,
                            'measure_unit' => $params['measure_unit'],
                            'normal_value' => $params['normal_value'],
                            'result' => $params['result'],
                            'created_by' => $this->lab_id,
                        );
                        //dd($labTestResultData);
                        $insertedId = $this->_laboratoryTestReportResource->addLaboratoryTestReport($labTestResultData);
//dd($insertedId);
                        if ($insertedId) {

                            $this->_helper->FlashMessenger()->setNamespace('success')->addMessage('Lab Result data successfully saved.');
                            $this->_redirect(LABORATORY_BASE_URL . 'requests/index');
                        } else {
                            $this->_helper->FlashMessenger()->setNamespace('error')->addMessage('Lab Result data is not inserted.Please try again.');
                            $this->_redirect(LABORATORY_BASE_URL . 'requests/lab-test-report-addedit/reqid/' . $reqid);
                        }
                    }
                } else {

                    $msg = $this->getValidatorErrors($inputData->getMessages(), 'admin');

                    $this->_helper->FlashMessenger()->setNamespace('error')->addMessage($msg);

                    if ($params['id']) {
                        $this->_redirect(LABORATORY_BASE_URL . 'requests/lab-test-report-addedit/reqid/' . $reqid . '/id/' . $id);
                    } else {
                        $this->_redirect(LABORATORY_BASE_URL . 'requests/lab-test-report-addedit/reqid/' . $reqid);
                    }

                    //$this->view->msg = $msg;
                }
            }
        }
    }

    protected function validateLabTestReportData($data, $errMsg) {

        if (isset($data['measure_unit'])) {
            $this->validators['measure_unit'] = array(
                'NotEmpty',
                'messages' => array('Please enter measure unit.'));
        }
        if (isset($data['normal_value'])) {
            $this->validators['normal_value'] = array(
                'NotEmpty',
                'messages' => array('Please enter normal value.'));
        }
        if (isset($data['result'])) {
            $this->validators['result'] = array(
                'NotEmpty',
                'messages' => array('Please enter result.'));
        }
    }

    public function printlabtestreportAction() {
        global $arrGender;
        $this->_helper->layout->disableLayout();


        //$id = $this->getRequest()->getParam('id');
        $reqid = $this->getRequest()->getParam('reqid');
        $this->view->labRequestData = $labRequestData = $this->_laboratoryRequestResource->fetchLabRequestByReqId($reqid);

        if (!empty($labRequestData)) {
            $pid = $labRequestData['patient_id'];
            $rid = $labRequestData['registration_no'];
            $patientRegisteredData = $this->_patientResource->fetchRegisteredPatientData($pid, $rid);
            $this->view->patientRegisteredData = $patientRegisteredData;
        }


        if (!empty($reqid)) {

            $labTestResultData = $this->_laboratoryTestResultResource->fetchLabTestDetailedResultByReqId($reqid);
        }
//dd($labTestResultData);
        $this->view->labTestResultData = $labTestResultData;

        $this->view->arrGender = $arrGender;
    }

    public function labTestResultAddeditAction() {

        global $status;
        $this->view->status = $status;
        $id = $this->getRequest()->getParam('id');
        $reqid = $this->getRequest()->getParam('reqid');
        $this->view->labRequestData = $this->_laboratoryRequestResource->fetchLabRequestByReqId($reqid);
        $this->view->labTestDetails = $this->_laboratoryTestResultResource->fetchLabTestDetails();
        $labTestResults = $this->_laboratoryTestResultResource->fetchLabTestResultByReqId($reqid);
        //dd($labTestResults);
        $arrLabTestResult = array();
        if (!empty($labTestResults)) {

            foreach ($labTestResults as $labTestResult) {
                $arrLabTestResult[$labTestResult['laboratory_test_details_id']] = $labTestResult['result'];
            }
        }
        //dd($arrLabTestResult);
        $this->view->labTestResults = $arrLabTestResult;
    }

    public function savelabtestresultAction() {

        // global $status;
        $request = $this->getRequest();
        if ($data = $request->getPost()) {
            //dd($data);
            $params = $this->__inputPostData;

            if (!empty($params['reqid'])) {


                $reqid = $params['reqid'];
                //$this->validateLabTestReportData($params, $this->errMsg);
                $inputData = new Zend_Filter_Input($this->filters, $this->validators);
                $inputData->setData($params);


                if (!empty($params['result'])) {
                    
                    $cond = array('lab_test_request_id = ?' => $reqid);
                    $this->_laboratoryTestResultResource->deleteLaboratoryTestResults($cond);

                    $totalResults = array();
                    $rsltCnt = 0;
                    foreach ($params['result'] as $key => $result) {

                        if ($result) {
                            $totalResults[] = $reqid;
                            $totalResults[] = $key;
                            $totalResults[] = $result;
                            $totalResults[] = $this->laboratoryuserId;
                            $rsltCnt++;
                        }
                    }
                }
                //dd($labTestResultData);
                $insertedId = $this->_laboratoryTestResultResource->insertLaboratoryTestResults($totalResults, $rsltCnt);
//dd($insertedId);
                if ($insertedId) {

                    $this->_helper->FlashMessenger()->setNamespace('success')->addMessage('Lab Result data successfully saved.');
                    $this->_redirect(LABORATORY_BASE_URL . 'requests/index');
                } else {
                    $this->_helper->FlashMessenger()->setNamespace('error')->addMessage('Lab Result data is not inserted.Please try again.');
                    $this->_redirect(LABORATORY_BASE_URL . 'requests/lab-test-result-addedit/reqid/' . $reqid);
                }
            }
        }
    }
    
public function labTestReportUploadAction() {

        global $status;
        $this->view->status = $status;
  
        $reqid = $this->getRequest()->getParam('reqid');
        $this->view->labRequestData = $this->_laboratoryRequestResource->fetchLabRequestByReqId($reqid);
        
       
    }
    
    public function savelabtestreportuploadAction() {


        global $nowDateTime;
        $request = $this->getRequest();
        if ($data = $request->getPost()) {

            $params = $this->__inputPostData;
           // dd($params);
            $id = $params['reqid'];


            //$response = array('status' => 0, 'msg' => '');
            $this->validateLabtestReporUploadtData($params, $this->errMsg);
            $inputData = new Zend_Filter_Input($this->filters, $this->validators);
            $inputData->setData($params);

            

            if ($inputData->isValid()) {
                
                
                $patientId = $params['patient_id'];
                $reqid = $params['reqid'];
                $laborateryTestName = $params['laboratery_test_name'];

                $newfilename = '';

                $labTestReportfile = $_FILES["report_filename"]["name"];
                
                
                if ((!empty($_FILES["report_filename"])) && ($_FILES['report_filename']['error'] == 0)) {

                    $filename = basename($_FILES['report_filename']['name']);
                    
                    $ext = substr($filename, strrpos($filename, '.') + 1);
                    
                    if ((($ext == "pdf") || ($ext == "jpg") || ($ext == "jpg")) && ($_FILES["report_filename"]["size"] < 500000000)) {
                        
                        $randString = $this->randomWithLength(2);
                        
                        $newfilename = $patientId.'-'.$reqid.'-'.str_replace(' ','-',$laborateryTestName).$randString.'.'.$ext;
                        
                        $newname = UPLOAD_LAB_TEST_REPORT_FILE_PATH . $newfilename;
                        
                        
                        if (!move_uploaded_file($_FILES['report_filename']['tmp_name'], $newname)) {

                            $this->_helper->FlashMessenger()->setNamespace('error')->addMessage('Error occured while uploading file.');
                            $this->_redirect(LABORATORY_BASE_URL . 'requests/lab-test-report-upload/reqid/' . $reqid);
                        }
                    } else {

                        $this->_helper->FlashMessenger()->setNamespace('error')->addMessage('Invalid file.');
                         $this->_redirect(LABORATORY_BASE_URL . 'requests/lab-test-report-upload/reqid/' . $reqid);
                    }
                }





                //dd($params);
                if (!empty($reqid)) {




                    
                    $labreportfilename['report_file_uploaded_date'] = $nowDateTime;
                    
                    
                    

                    if ($newfilename != '') {
                        $labreportfilename['uploaded_report_filename'] = $newfilename;
                        
                        
                    }

                   // dd($labreportfilename);
                   $updateP = $this->_laboratoryRequestResource->updateLabRequestStatus($labreportfilename, $reqid);



                    if (($updateP)) {


                        $this->_helper->FlashMessenger()->setNamespace('success')->addMessage('Test report successfully uploaded.');

                        $this->_redirect(LABORATORY_BASE_URL . 'requests/index');

                        
                    } else {

                        $this->_helper->FlashMessenger()->setNamespace('error')->addMessage('Test report not updated.');
                         $this->_redirect(LABORATORY_BASE_URL . 'requests/lab-test-report-upload/reqid/' . $reqid);
                    }
                    //$this->_redirect(PATIENT_BASE_URL . 'appointment/video-consultation-addedit/patient_id/'.$pid.'/appnt_id/'.$id);
                } 
            } else {

                $msg = $this->getValidatorErrors($inputData->getMessages(), 'admin');

                $this->_helper->FlashMessenger()->setNamespace('error')->addMessage($msg);


                 $this->_redirect(LABORATORY_BASE_URL . 'requests/lab-test-report-upload/reqid/' . $reqid);

                //$this->view->msg = $msg;
            }
        }




        die;
    }
    
     protected function validateLabtestReporUploadtData($data, $errMsg) {

        if (isset($data['laboratery_test_name'])) {
            $this->validators['laboratery_test_name'] = array(
                'NotEmpty',
                'messages' => array('Please enter test name.'));
        }
        if (isset($data['report_filename'])) {
            $this->validators['report_filename'] = array(
                'NotEmpty',
                'messages' => array('Please select file.'));
        }
       
    }
}

?>