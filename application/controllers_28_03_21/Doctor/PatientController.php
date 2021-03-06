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
            $params['sidx'] = 'id';
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
                $prescriptionId = $val['dpid'];
                $investigationId = $val['hiid'];
                $laborateryTestNotes = $val['laboratery_test_notes'];
                
                if($val['test_count'] == 0){
                    
                    $testStatusLink = '';
                    
                }else if($val['test_count'] > 0){
                    $testURL = DOCTOR_BASE_URL.'labtest-requests/view/patient_id/'.$id.'/rid/'.$rId.'/id/'.$prescriptionId;
                    
                    $testStatusLink = '<a  href="'.$testURL.'" >View Report</a>';
                }else{
                    $testStatusLink = '';
                }
                
                //$pid = $val['patient_id'];


                $responce->rows[$k]['id'] = $id;
                $actionUpdate = '';
                
                if($prescriptionId){
                    
                    $dpURL = DOCTOR_BASE_URL.'doctor-prescribe/addedit/patient_id/'.$id.'/rid/'.$rId.'/id/'.$prescriptionId;
                    $dpname = 'Edit Prescription';
                }else{
                    $dpURL = DOCTOR_BASE_URL.'doctor-prescribe/addedit/patient_id/'.$id.'/rid/'.$rId;
                    $dpname = 'Add Prescription';
                }
                $investigationUpdate = '';    
                
                if($investigationId){
                    
                    $hiURL = DOCTOR_BASE_URL.'investigation/addedit/patient_id/'.$id.'/rid/'.$rId.'/prescribe_id/'.$prescriptionId.'/id/'.$investigationId;
                    $hiname = 'Edit Investigation';
                    $investigationUpdate = '<a  href="'.$hiURL.'" >'.$hiname.'</a>';
                    
                }else if($prescriptionId != '' && $investigationId == ''){
                    
                    if(($testStatusLink) && ($laborateryTestNotes)){
                        $hiURL = DOCTOR_BASE_URL.'investigation/addedit/patient_id/'.$id.'/rid/'.$rId.'/prescribe_id/'.$prescriptionId;
                        $hiname = 'Add Investigation';
                        $investigationUpdate = '<a  href="'.$hiURL.'" >'.$hiname.'</a>';
                    }else if((!$testStatusLink) && (!$laborateryTestNotes)){
                        $hiURL = DOCTOR_BASE_URL.'investigation/addedit/patient_id/'.$id.'/rid/'.$rId.'/prescribe_id/'.$prescriptionId;
                        $hiname = 'Add Investigation';
                        $investigationUpdate = '<a  href="'.$hiURL.'" >'.$hiname.'</a>';
                    }
                    
                }
                
                $prescribeUpdate = '<a  href="'.$dpURL.'" >'.$dpname.'</a>';
                
                
//                

                $responce->rows[$k]['cell'] = array(
                    $val['registration_no'],
                    $val['name'],
                    $val['mobile_no'],
                    $this->arrGender[$val['sex']],
                   // $val['sex'],
                    //$val['email'],
                    $this->changeDateFormat($val['date_of_admission'], DATETIMEFORMAT, FRONT_DATE_FORMAT),
                   
                    $prescribeUpdate,
                    $testStatusLink,
                    $investigationUpdate
                );
            }
            echo $this->jsonEncode($responce);
            exit;
        }
    
    }

 

 
     

}

?>