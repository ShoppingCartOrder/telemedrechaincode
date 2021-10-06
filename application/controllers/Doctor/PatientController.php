<?php

class Doctor_PatientController extends Mylib_Controller_DoctorbaseController {

    //protected $_dashboardResource;

    public function init() {
        parent::init();
        $this->_patientResource = new Application_Model_DbTable_Patients();
        $this->_userResource = new Application_Model_DbTable_Users();


        $this->_doctorResource = new Application_Model_DbTable_Doctors();

        $this->_specialityResource = new Application_Model_DbTable_Speciality();
        $this->_chargeItemResource = new Application_Model_DbTable_ChargeItemName();

        global $arrGender;
        $this->view->arrGender = $this->arrGender = $arrGender;
        global $arrBloodGroup;
        $this->view->arrBloodGroup = $this->arrBloodGroup = $arrBloodGroup;
        global $arrRelation;
        $this->view->arrRelation = $this->arrRelation =$arrRelation;
        global $arrMatrialStatus;
        $this->view->arrMatrialStatus = $this->arrMatrialStatus = $arrMatrialStatus;
        global $arrPaymentMethod;
        $this->view->arrPaymentMethod = $this->arrPaymentMethod = $arrPaymentMethod;
        //Appointments
        $this->_appointmentsResource = new Application_Model_DbTable_Appointments();
        
        
    }
    
    public function indexAction() {
        global $nowDateTime;

        global $healthCondition;
        //dd($this->gender);
        $request = $this->getRequest();
        global $noRecord;
        if ($request->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $data = $this->getRequest()->getPost();
            $params = $this->getSearchParams($data);


            $params['fields']['main'] = array('*');
            $params['sidx'] = 'a.id';
            $params['sord'] = 'DESC';


            $result = $this->_patientResource->allPatientsByDoctorId($params);
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
                $appntId = $val['appointment_id'];
                $prescriptionId = $val['dpid'];
                $investigationId = $val['hiid'];
                $laborateryTestNotes = $val['laboratery_test_notes'];
                
                if($val['test_count'] == 0){
                    
                    $testStatusLink = '';
                    
                }else if($val['test_count'] > 0){
                    $testURL = DOCTOR_BASE_URL.'labtest-requests/view/patient_id/'.$id.'/rid/'.$rId.'/appntid/'.$appntId.'/id/'.$prescriptionId;
                    
                    $testStatusLink = '<a  href="'.$testURL.'" title = "View Report"><i class="fa fa-list" aria-hidden="true"></i></a>';
                }else{
                    $testStatusLink = '';
                }
                
                if($val['uploaded_test_report_count'] == 0){
                    
                    $upoadedtestStatusLink = '';
                    
                }else if($val['uploaded_test_report_count'] > 0){
                    $uploadedtestURL = DOCTOR_BASE_URL.'labtest-requests/view/patient_id/'.$id.'/rid/'.$rId.'/appntid/'.$appntId.'/id/'.$prescriptionId;
                    
                    $upoadedtestStatusLink = '<a  href="'.$uploadedtestURL.'" title = "View uploaded Report"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                }else{
                    $upoadedtestStatusLink = '';
                }
                
                //$pid = $val['patient_id'];

                
                $responce->rows[$k]['id'] = $id;
                $actionUpdate = '';
                
                if($prescriptionId){
                    
                    $dpURL = DOCTOR_BASE_URL.'doctor-prescribe/addedit/patient_id/'.$id.'/rid/'.$rId.'/appntid/'.$appntId.'/id/'.$prescriptionId;
                    $dpname = 'Edit Prescription';
                }else{
                    $dpURL = DOCTOR_BASE_URL.'doctor-prescribe/addedit/patient_id/'.$id.'/rid/'.$rId.'/appntid/'.$appntId;
                    $dpname = 'Add Prescription';
                }
                $investigationUpdate = '';    
                
                if($investigationId){
                    
                    $hiURL = DOCTOR_BASE_URL.'investigation/addedit/patient_id/'.$id.'/rid/'.$rId.'/appntid/'.$appntId.'/prescribe_id/'.$prescriptionId.'/id/'.$investigationId;
                    $hiname = 'Edit Investigation';
                    $investigationUpdate = '<a  href="'.$hiURL.'" >'.$hiname.'</a>';
                    
                }else if($prescriptionId != '' && $investigationId == ''){
                    
                    if(($testStatusLink) && ($laborateryTestNotes)){
                        $hiURL = DOCTOR_BASE_URL.'investigation/addedit/patient_id/'.$id.'/rid/'.$rId.'/appntid/'.$appntId.'/prescribe_id/'.$prescriptionId;
                        $hiname = 'Add Investigation';
                        $investigationUpdate = '<a  href="'.$hiURL.'" >'.$hiname.'</a>';
                    }else if((!$testStatusLink) && (!$laborateryTestNotes)){
                        $hiURL = DOCTOR_BASE_URL.'investigation/addedit/patient_id/'.$id.'/rid/'.$rId.'/appntid/'.$appntId.'/prescribe_id/'.$prescriptionId;
                        $hiname = 'Add Investigation';
                        $investigationUpdate = '<a  href="'.$hiURL.'" >'.$hiname.'</a>';
                    }
                    
                }
                
                $prescribeUpdate = '<a  href="'.$dpURL.'" >'.$dpname.'</a>';
                
                
//                

                $responce->rows[$k]['cell'] = array(
                    $appntId,
                    $val['registration_no'],
                    $val['name'],
                    $val['mobile_no'],
                    $this->arrGender[$val['sex']],
                   // $val['sex'],
                    //$val['email'],
                    $this->changeDateFormat($val['date_of_admission'], DATETIMEFORMAT, FRONT_DATE_FORMAT),
                   
                    $prescribeUpdate,
                    $testStatusLink.'  '.$upoadedtestStatusLink,
                    $investigationUpdate
                );
            }
            echo $this->jsonEncode($responce);
            exit;
        }
    
    }

 

 
     

}

?>