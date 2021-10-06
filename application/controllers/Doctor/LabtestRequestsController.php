<?php

class Doctor_LabtestRequestsController extends Mylib_Controller_DoctorbaseController {

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
        //dd($this->loginId);
        
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
    public function viewAction() {
        global $nowDateTime;

        global $arrLabRequestStatus;
        //dd($this->gender);
        $request = $this->getRequest();
        $urlParams = $request->getParams();
        
        $this->view->urldata = $urlParams;
        //dd($urlParams);
        global $noRecord;
        if ($request->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $data = $this->getRequest()->getPost();
            
            $params = $this->getSearchParams($data);


            $params['fields']['main'] = array('*');
            $params['sidx'] = 'id';
            $params['sord'] = 'DESC';

            $dataParams = $this->getRequest()->getParams();
            $params['condition']['patient_id'] = $dataParams['patient_id'];
            $params['condition']['rid'] = $dataParams['rid'];
            $params['condition']['prescribe_id'] = $dataParams['prescribe_id'];
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
                $rId = $val['registration_no'];
                $pId = $val['patient_id'];
                //$prescriptionId = $val['dpid'];
                //$pid = $val['patient_id'];


                $responce->rows[$k]['id'] = $id;

                $selectStatus = $arrLabRequestStatus[$val['status']];
                
                $printReport = '';
                

                if($val['status'] == 1){
                  $printReportUrl = DOCTOR_BASE_URL . 'labtest-requests/printlabtestreport/patient_id/'.$pId.'/rid/'.$rId.'/reqid/' . $id.'/prescribe_id/'.$dataParams['prescribe_id'];  
                  $printReport =  "<a title = 'Print Test Report' href='" . $printReportUrl . "'><i class='fa fa-print' aria-hidden='true'></i></a>";
                }
                
                $actionUpdate = $printReport;
                
                $reportCompletedDate = '';
                if($val['report_completed_date']){
                  $reportCompletedDate =   $this->changeDateFormat($val['report_completed_date'], DATETIMEFORMAT, FRONT_DATE_FORMAT);
                }

                $responce->rows[$k]['cell'] = array(
                    $id,
                    $val['registration_no'],
                    $val['patient_name'],
                     $val['lab_test_name'],
                   // $val['doctor_name'],
                    $this->changeDateFormat($val['created_at'], DATETIMEFORMAT, FRONT_DATE_FORMAT),
                     $reportCompletedDate,
                    $selectStatus,                    
                    "<a target = '_blank' href = '".UPLOADED_LAB_TEST_REPORT_FILE_PATH.$val['uploaded_report_filename']."'".'>'.$val['uploaded_report_filename'].'</a>',
                    $this->changeDateFormat($val['report_file_uploaded_date'], DATETIMEFORMAT, FRONT_DATE_FORMAT),
                    $actionUpdate,
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
                             'created_by' => $this->loginId,
                        );

                        $update = $this->_laboratoryTestReportResource->updateLaboratoryTestReport($labTestResultData, $id);

                        if ($update) {

                            $this->_helper->FlashMessenger()->setNamespace('success')->addMessage('Lab Result data successfully updated.');
                            $this->_redirect(DOCTOR_BASE_URL . 'requests/index');
                        } else {
                            //$this->view->msg = 'Patient record not updated.';
                            $this->_helper->FlashMessenger()->setNamespace('error')->addMessage('Lab Result data not updated.');
                            $this->_redirect(DOCTOR_BASE_URL . 'requests/lab-test-report-addedit/reqid/' . $reqid . '/id/' . $id);
                        }
                    } else { //Save new Patient
                        $labTestResultData = array(
                            'lab_test_request_id' => $reqid,
                            'measure_unit' => $params['measure_unit'],
                            'normal_value' => $params['normal_value'],
                            'result' => $params['result'],
                            'created_by' => $this->loginId,
                        );
                        //dd($labTestResultData);
                        $insertedId = $this->_laboratoryTestReportResource->addLaboratoryTestReport($labTestResultData);
//dd($insertedId);
                        if ($insertedId) {

                            $this->_helper->FlashMessenger()->setNamespace('success')->addMessage('Lab Result data successfully saved.');
                            $this->_redirect(DOCTOR_BASE_URL . 'requests/index');
                        } else {
                            $this->_helper->FlashMessenger()->setNamespace('error')->addMessage('Lab Result data is not inserted.Please try again.');
                            $this->_redirect(DOCTOR_BASE_URL . 'requests/lab-test-report-addedit/reqid/' . $reqid);
                        }
                    }
                } else {

                    $msg = $this->getValidatorErrors($inputData->getMessages(), 'admin');

                    $this->_helper->FlashMessenger()->setNamespace('error')->addMessage($msg);

                    if ($params['id']) {
                        $this->_redirect(DOCTOR_BASE_URL . 'requests/lab-test-report-addedit/reqid/' . $reqid . '/id/' . $id);
                    } else {
                        $this->_redirect(DOCTOR_BASE_URL . 'requests/lab-test-report-addedit/reqid/' . $reqid);
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
        $this->view->patientId = $this->getRequest()->getParam('patient_id');
        $this->view->rid = $this->getRequest()->getParam('rid');
        $this->view->prescribeId = $this->getRequest()->getParam('prescribe_id');
        
        $this->view->labRequestData = $labRequestData = $this->_laboratoryRequestResource->fetchLabRequestByReqId($reqid);
        //dd($labRequestData);
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
                    $cond['lab_test_request_id'] = $reqid;
                    $this->_laboratoryTestResultResource->deleteLaboratoryTestResults($cond);

                    $totalResults = array();
                    $rsltCnt = 0;
                    foreach ($params['result'] as $key => $result) {

                        if ($result) {
                            $totalResults[] = $reqid;
                            $totalResults[] = $key;
                            $totalResults[] = $result;
                            $totalResults[] = $this->loginId;
                            $rsltCnt++;
                        }
                    }
                }
                //dd($labTestResultData);
                $insertedId = $this->_laboratoryTestResultResource->insertLaboratoryTestResults($totalResults, $rsltCnt);
//dd($insertedId);
                if ($insertedId) {

                    $this->_helper->FlashMessenger()->setNamespace('success')->addMessage('Lab Result data successfully saved.');
                    $this->_redirect(DOCTOR_BASE_URL . 'requests/index');
                } else {
                    $this->_helper->FlashMessenger()->setNamespace('error')->addMessage('Lab Result data is not inserted.Please try again.');
                    $this->_redirect(DOCTOR_BASE_URL . 'requests/lab-test-result-addedit/reqid/' . $reqid);
                }
            }
        }
    }

}

?>