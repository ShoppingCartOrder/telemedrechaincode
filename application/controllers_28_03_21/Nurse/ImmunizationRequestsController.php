<?php

class Nurse_ImmunizationRequestsController extends Mylib_Controller_NursebaseController {

    //protected $_dashboardResource;

    public function init() {
        parent::init();
        $this->_patientResource = new Application_Model_DbTable_Patients();
        $this->_userResource = new Application_Model_DbTable_Users();


        $this->_doctorResource = new Application_Model_DbTable_Doctors();

        $this->_specialityResource = new Application_Model_DbTable_Speciality();
        $this->_chargeItemResource = new Application_Model_DbTable_ChargeItemName();
        $this->_immunizationResource = new Application_Model_DbTable_Immunization();
        $this->_immunizationRequestResource = new Application_Model_DbTable_ImmunizationRequest();
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


            $result = $this->_immunizationRequestResource->allImmunizationRequest($params);
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

                  $dpURL = NURSE_BASE_URL.'doctor-prescribe/addedit/patient_id/'.$id.'/rid/'.$rId.'/id/'.$prescriptionId;
                  $dpname = 'Edit Prescription';
                  }else{
                  $dpURL = NURSE_BASE_URL.'doctor-prescribe/addedit/patient_id/'.$id.'/rid/'.$rId;
                  $dpname = 'Add Prescription';
                  }


                  //$responce->rows[$k]['appointment_type'] = $val['appointment_type'];
                  $actionUpdate = "<a onclick='return patientProfileEdit($id);' href='javascript:void(0);'><span class='far fa-edit' aria-hidden='true'></span></a>";
                  $chargeUpdate = "<a onclick='return addPayment($id,$rId);' href='javascript:void(0);'>Add Charges</a>";
                  $prescribeUpdate = '<a  href="'.$dpURL.'" target = "_blank">'.$dpname.'</a>';
                 */
                $printReport = '';


                $imzDoneImgPath = WEBSITE_IMAGE . 'lab.png';
                
                 if ($val['done_by']) {
                  $imzDetailsTitle = "View Immunization Details";
                  $imzDetailsImg = "<img width = '20px' src = '" . $imzDoneImgPath . "' >";
                  $addViewReportUrl = NURSE_BASE_URL . 'immunization-requests/immunization-details-addedit/reqid/' . $id . '/patient_id/' . $val['patient_id'] . '/rid/' . $val['registration_no'].'/prescribe_id/'.$val['prescribe_id'].'/view/1';
                
                  
                 } else {

                  $imzDetailsTitle = "View Immunization Details Form";
                  $imzDetailsImg = " <i class='fa fa-plus' aria-hidden='true'></i>";
                  $addViewReportUrl = NURSE_BASE_URL . 'immunization-requests/record-addedit/reqid/' . $id . '/patient_id/' . $val['patient_id'] . '/rid/' . $val['registration_no'].'/prescribe_id/'.$val['prescribe_id'];
                
                  }

                 
                //$imzResultURL = NURSE_BASE_URL . 'immunization-requests/record-addedit/reqid/' . $id . '/patient_id/' . $val['patient_id'] . '/rid/' . $val['registration_no'].'/prescribe_id/'.$val['prescribe_id'];
                    
                if ($val['status'] == 1) {
                    //$printReportUrl = NURSE_BASE_URL . 'immunization-requests/printlabtestreport/reqid/' . $id;
                    $printReportUrl = NURSE_BASE_URL . 'immunization-requests/immunization-details-addedit/reqid/' . $id . '/patient_id/' . $val['patient_id'] . '/rid/' . $val['registration_no'].'/prescribe_id/'.$val['prescribe_id'].'/print/1';
                    
                    $printReport = "<a title = 'Print Test Report' href='" . $printReportUrl . "'><i class='fa fa-print' aria-hidden='true'></i></a>";
                }
                $actionUpdate = "<a title = 'Save Status' onclick='return updateStatus($id,$pId);' href='javascript:void(0);'><span class='far fa-save' aria-hidden='true'></span></a>&nbsp;"
                  . "<a title = '" . $imzDetailsTitle . "' href='" . $addViewReportUrl . "'>$imzDetailsImg</a>&nbsp;"
                  . $printReport; 
               

                $responce->rows[$k]['cell'] = array(
                    $id,
                    $val['registration_no'],
                    $val['patient_name'],
                    //$val['vaccine_name'],
                    '<a href = "' . $addViewReportUrl . '">' . $val['vaccine_name'] . "</a>",
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

   

    public function updatestatusAction() {
        $this->_helper->viewRenderer->setNoRender(true);
        $data = $this->getRequest()->getPost();

        if ($data['status'] != '') {
            $id = $data['id'];
            $pid = $data['pid'];
            //$rparam['remarks'] = $data['remarks'];
            $rparam['status'] = $data['status'];

            if ($rparam['status'] == 1) {
                
                
                    $this->_immunizationRequestResource->updateImmunizationRequestStatus($rparam, $id);
                    $patientData = $this->_patientResource->getPatientById($pid);
                    //dd($appnTData);
                    if (!empty($patientData['mobile_no'])) {
                        $arrMno[] = '91' . $patientData['mobile_no'];
                        $PatientAppntSMSTemp = "Immunization is completed";
                        $content = $PatientAppntSMSTemp;

                        $this->sendMessage($arrMno, $content);
                    }
                    echo '1';
                
            } else {
                echo '3';
            }
        }
        die;
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

    public function recordAddeditAction() {

        global $status;
        $this->view->status = $status;
        $this->view->id = $id = $this->getRequest()->getParam('id');
        $this->view->reqid =  $reqid = $this->getRequest()->getParam('reqid');
        $this->view->patientId = $patientId = $this->getRequest()->getParam('patient_id');
        $this->view->registrationNo = $registrationNo = $this->getRequest()->getParam('rid');
        $this->view->prescribeId = $prescribeId = $this->getRequest()->getParam('prescribe_id');
        $this->view->imuzBirthDetails = $imuzBirthDetails = $this->_immunizationResource->fetchImmunizationChildBirthDetails($patientId,$registrationNo);
        //dd();
        
        /*
        $imuzDetails = $this->_immunizationResource->fetchImmunizationDetails();
        $imuzData = array();
        if (!empty($imuzDetails)) {

            foreach ($imuzDetails as $key => $imzval) {

                $imuzData[$imzval['age']][$key] = $imzval;
            }
        }
        $this->view->imuzData = $imuzData; */
        // dd($imuzData);
//        $labTestResults = $this->_laboratoryTestResultResource->fetchLabTestResultByReqId($reqid);
//       
//        $arrLabTestResult = array();
//        if (!empty($labTestResults)) {
//
//            foreach ($labTestResults as $labTestResult) {
//                $arrLabTestResult[$labTestResult['laboratory_test_details_id']] = $labTestResult['result'];
//            }
//        }
//        //dd($arrLabTestResult);
//        $this->view->labTestResults = $arrLabTestResult;
    }

      public function savebirthdetailsAction() {

        // global $status;
        $request = $this->getRequest();
        if ($data = $request->getPost()) {
            //dd($data);
            $params = $this->__inputPostData;
//dd($params);
            if (!empty($params['reqid'])) {


                $reqid = $params['reqid'];
                $this->validateBirthData($params, $this->errMsg);
                $inputData = new Zend_Filter_Input($this->filters, $this->validators);
                $inputData->setData($params);
                if ($inputData->isValid()) {


                    //dd($params);
                    if (!empty($params['id'])) {
                        $id = $params['id'];

                        $birthData = array(
                            //'req_id' => $reqid,
                            //'patient_id' => $params['patient_id'],
                            //'registration_no' => $params['registration_no'],
                            //'prescription_id' => $params['prescribe_id'],
                            'delivery_date' => $params['delivery_date'],
                            'neonatal_status' => $params['neonatal_status'],
                            'length' => $params['length'],
                            'birth_weight' => $params['birth_weight'],
                            'head_circumference' => $params['head_circumference'],
                            'blood_group' => $params['blood_group'],
                            'remarks' => $params['remarks'],                            
                            //'created_by' => $this->nurseId,
                        );

                        $update = $this->_immunizationResource->updateBirthDetails($birthData,$id);

                        if ($update) {

                            $this->_helper->FlashMessenger()->setNamespace('success')->addMessage('Birth Details successfully updated.');
                            $this->_redirect(NURSE_BASE_URL . 'immunization-requests/index');
                        } else {
                            //$this->view->msg = 'Patient record not updated.';
                            $this->_helper->FlashMessenger()->setNamespace('error')->addMessage('Birth Details not updated.');
                            $this->_redirect(NURSE_BASE_URL . 'immunization-requests/record-addedit/reqid/' . $reqid.'/patient_id/'.$params['patient_id'].'/rid/'.$params['registration_no'].'/prescribe_id/'.$params['prescribe_id']);
                        }
                    } else { //Save new Patient
                        $birthData = array(
                            'req_id' => $reqid,
                            'patient_id' => $params['patient_id'],
                            'registration_no' => $params['registration_no'],
                            'prescription_id' => $params['prescribe_id'],
                            'delivery_date' => $params['delivery_date'],
                            'neonatal_status' => $params['neonatal_status'],
                            'length' => $params['length'],
                            'birth_weight' => $params['birth_weight'],
                            'head_circumference' => $params['head_circumference'],
                            'blood_group' => $params['blood_group'],
                            'remarks' => $params['remarks'],                            
                            'created_by' => $this->nurseId,
                        );
                        //dd($labTestResultData);
                        $insertedId = $this->_immunizationResource->addBirthDetails($birthData);

//dd($insertedId);
                        if ($insertedId) {
                            $insertId = $this->createImmunizationRecords($params['patient_id'],$params['registration_no']);
                            $this->_helper->FlashMessenger()->setNamespace('success')->addMessage('Birth Details successfully saved.');
                            $this->_redirect(NURSE_BASE_URL . 'immunization-requests/index');
                        } else {
                            $this->_helper->FlashMessenger()->setNamespace('error')->addMessage('Birth Details is not inserted.Please try again.');
                            $this->_redirect(NURSE_BASE_URL . 'immunization-requests/record-addedit/reqid/' . $reqid.'/patient_id/'.$params['patient_id'].'/rid/'.$params['registration_no'].'/prescribe_id/'.$params['prescribe_id']);
                        }
                    }
                } else {

                    $msg = $this->getValidatorErrors($inputData->getMessages(), 'admin');

                    $this->_helper->FlashMessenger()->setNamespace('error')->addMessage($msg);

                    if ($params['id']) {
                        $this->_redirect(NURSE_BASE_URL . 'immunization-requests/lab-test-report-addedit/reqid/' . $reqid . '/id/' . $id);
                    } else {
                        $this->_redirect(NURSE_BASE_URL . 'immunization-requests/record-addedit/reqid/' . $reqid.'/patient_id/'.$params['patient_id'].'/rid/'.$params['registration_no'].'/prescribe_id/'.$params['prescribe_id']);
                    }

                    //$this->view->msg = $msg;
                }
            }
        }
    }
    
    protected function validateBirthData($data, $errMsg) {

        if (isset($data['delivery_date'])) {
            $this->validators['delivery_date'] = array(
                'NotEmpty',
                'messages' => array('Please enter date.'));
        }
        if (isset($data['neonatal_status'])) {
            $this->validators['neonatal_status'] = array(
                'NotEmpty',
                'messages' => array('Please enter neonatal status.'));
        }
        if (isset($data['length'])) {
            $this->validators['length'] = array(
                'NotEmpty',
                'messages' => array('Please enter length.'));
        }
        if (isset($data['birth_weight'])) {
            $this->validators['birth_weight'] = array(
                'NotEmpty',
                'messages' => array('Please enter birth weight.'));
        }
        if (isset($data['head_circumference'])) {
            $this->validators['head_circumference'] = array(
                'NotEmpty',
                'messages' => array('Please enter head circumference.'));
        }
        if (isset($data['blood_group'])) {
            $this->validators['blood_group'] = array(
                'NotEmpty',
                'messages' => array('Please enter blood group.'));
        }
    }
    
 
     public function immunizationDetailsAddeditAction() {

        $this->view->id = $id = $this->getRequest()->getParam('id');
        $this->view->reqid =  $reqid = $this->getRequest()->getParam('reqid');
        $this->view->patientId = $patientId = $this->getRequest()->getParam('patient_id');
        $this->view->registrationNo = $registrationNo = $this->getRequest()->getParam('rid');
        $this->view->prescribeId = $prescribeId = $this->getRequest()->getParam('prescribe_id');
        $this->view->print = $print = $this->getRequest()->getParam('print');
        $this->view->view =  $this->getRequest()->getParam('view');
       
        $reqdVacDetails = $this->_immunizationRequestResource->fetchImmunizationRequestByReqId($reqid);                    
        
        if(empty($reqdVacDetails)){
            $this->_redirect(NURSE_BASE_URL . 'immunization-requests/index');
        }
        
        $this->view->selectedVaccineId = $reqdVacDetails['vaccine_id'];
        $this->view->reqCompletedDate = $reqdVacDetails['completed_date'];
        $this->view->selectedVaccineAge = $reqdVacDetails['age'];
        //dd($reqdVacDetails);
        $imuzChildDetails = $this->_immunizationResource->fetchChildImmunizationDetails($patientId,$registrationNo);                    

        if(!empty($imuzChildDetails)){
            foreach ($imuzChildDetails as $key=>$val) {
                $age=$val['age'];
                $imuzData[$age][$key]=$val;
                
            }
        }
       //dd($imuzData);
        $this->view->imuzData = $imuzData; 
   
    }
    
    public function saveimmunizationdetailsAction() {

         global $nowDateTime;
        $request = $this->getRequest();
        if ($data = $request->getPost()) {

            $params = $this->__inputPostData;

            $pid = $params['patient_id'];
            $registrationNo = $params['registration_no'];
            $birthDetailsId = $params['birth_details_id'];
            $reqid = $params['reqid'];
            $vaccineId = $params['vaccineId'];


            //$response = array('status' => 0, 'msg' => '');
            $this->validateImmunizationData($params, $this->errMsg);
            $inputData = new Zend_Filter_Input($this->filters, $this->validators);
            $inputData->setData($params);

            //print_r($params); die;

            if ($inputData->isValid()) {


                //dd($params);
                if (!empty($pid) && !empty($registrationNo) && !empty($birthDetailsId) && !empty($reqid)) {
                    //dd($params);

                    $allSelectedIds = rtrim($params['allselectedids'],',');
                    $allSelectedDates = rtrim($params['allselecteddates'],',');

                    if (($allSelectedIds) && ($allSelectedDates)) {

                        $arrSelectedIds = explode(',', $allSelectedIds);
                        $arrSelectedDates = explode(',', $allSelectedDates);
                        //dd($arrSelectedDates);
                        foreach ($arrSelectedIds as $arrSelectedId) {

                            $ImmunizationDetailsData1 = array(
                                'prescribe_id' => $params['prescribe_id'],                                
                                'request_id' => $reqid,                                
                                'batch' => $params['batch_'.$arrSelectedId],                                
                                'updated_by' => $this->nurseId
                            );
                            $cond1['id =?'] = $arrSelectedId;
                            $updated = $this->_immunizationResource->updateImmunizationDetails($ImmunizationDetailsData1,$cond1);
                        }
                        
                        foreach ($arrSelectedDates as $arrSelectedDate) {

                            $ImmunizationDetailsData2 = array(
                                'weight' => $params['weight_'.$arrSelectedDate],                                
                                'height' => $params['height_'.$arrSelectedDate],                                
                                'head_circumference' => $params['Headcir_'.$arrSelectedDate],                                
                                'updated_by' => $this->nurseId
                            );
                            $cond2['due_on =?'] = $arrSelectedDate;
                            $updated = $this->_immunizationResource->updateImmunizationDetails($ImmunizationDetailsData2,$cond2);
                        
                           
                        }
                     $ImmunizationCompleted['completed_date'] = $nowDateTime;
                     $ImmunizationCompleted['done_by'] = $this->nurseId;
                     
                    $updateP = $this->_immunizationRequestResource->updateImmunizationRequestStatus($ImmunizationCompleted, $reqid);
                    echo '1'; die;    
                    }//selected value

                    
                    echo '2'; die;
                   
                   
        
        }//check patient ID
        echo '3'; die;
    }else {

                    $msg = $this->getValidatorErrors($inputData->getMessages(), 'admin');

                    $this->_helper->FlashMessenger()->setNamespace('error')->addMessage($msg);
echo '4'; die;
                   
                    //$this->_redirect(NURSE_BASE_URL . 'immunization-requests/record-addedit/reqid/' . $reqid.'/patient_id/'.$params['patient_id'].'/rid/'.$params['registration_no'].'/prescribe_id/'.$params['prescribe_id'].'/id/'.$birthDetailsId);
                  

                    //$this->view->msg = $msg;
                }
        }
    }
    
    public function createImmunizationRecords($patientId,$registrationNo){
        
        $imuzBirthDetails = $this->_immunizationResource->fetchImmunizationChildBirthDetails($patientId,$registrationNo);
      
        if(!empty($imuzBirthDetails)){
        
        $imuzDetails = $this->_immunizationResource->fetchImmunizationDetails();
        $imuzData = array();
        if (!empty($imuzDetails)) {
            
            $cnt = 0;
            //dd($imuzDetails);
            foreach ($imuzDetails as $key => $imzval) {
                $bulkVaccineDetails = array();
                $bulkVaccineDetails['patient_id'] = $patientId;
                $bulkVaccineDetails['registration_no'] = $registrationNo;
                $bulkVaccineDetails['birth_details_id'] = $imuzBirthDetails['id'];
                $bulkVaccineDetails['vaccine_id'] = $imzval['id'];
                $bulkVaccineDetails['vaccine_name'] = $imzval['vaccine_name'];
                $bulkVaccineDetails['age'] = $imzval['age'];
                $deliveryDate = $imuzBirthDetails['delivery_date'];
                $vaccinationStartAge = $imzval['starting_age'];
                $strVaccinationDate = strtotime("$deliveryDate $vaccinationStartAge");
                $vaccinationDate =  date(DATEFORMAT,$strVaccinationDate); 
                    
                $bulkVaccineDetails['due_on'] = $vaccinationDate;
                $bulkVaccineDetails['created_by'] = $this->nurseId;
                
                $insertId = $this->_immunizationResource->InsertChildVaccineDetails($bulkVaccineDetails);
                
               
            }
          
           return $insertId;
            
    }
        }
        
    }
    
    protected function validateImmunizationData($data, $errMsg) {

        if (isset($data['patient_id'])) {
            $this->validators['patient_id'] = array(
                'NotEmpty',
                'messages' => array('Patient id not avaliable.'));
        }
    }

}

?>