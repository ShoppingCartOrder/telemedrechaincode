<?php

class Hospital_OtController extends Mylib_Controller_HospitalbaseController {

    //protected $_dashboardResource;

    public function init() {
        parent::init();
        $this->_patientResource = new Application_Model_DbTable_Patients();
        $this->_userResource = new Application_Model_DbTable_Users();


        $this->_doctorResource = new Application_Model_DbTable_Doctors();

        $this->_specialityResource = new Application_Model_DbTable_Speciality();
        $this->_chargeItemResource = new Application_Model_DbTable_ChargeItemName();
        $this->_wardsResource = new Application_Model_DbTable_Wards();

        $this->_helper->layout->setLayout('hospital');
        global $arrGender;
        $this->view->arrGender = $this->arrGender = $arrGender;
        global $arrBloodGroup;
    
        
        
        
    }
    
    public function indexAction() {
        global $nowDateTime;
        global $arrPatientType;
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


            $result = $this->_patientResource->allPatients($params);
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
                $pType = $val['patient_type'];
                $prescriptionId = $val['dpid'];
                //$pid = $val['patient_id'];


                $responce->rows[$k]['id'] = $id;
                $actionUpdate = '';
                if($prescriptionId){
                    
                    $dpURL = HOSPITAL_BASE_URL.'doctor-prescribe/addedit/patient_id/'.$id.'/rid/'.$rId.'/id/'.$prescriptionId;
                    //$dpname = 'Edit Prescription';
                    $dpname = 'View Prescription';
                }else{
                   // $dpURL = HOSPITAL_BASE_URL.'doctor-prescribe/addedit/patient_id/'.$id.'/rid/'.$rId;
                    //$dpname = 'Add Prescription';
                    $dpURL = '#';
                    $dpname = 'Not Prescribed';
                }
                        
                        
                //$responce->rows[$k]['appointment_type'] = $val['appointment_type'];
                $actionUpdate = "<input class = 'bed-cls' type = 'radio' value = '$id' onchange='return bedAllocation($id,$pType);' name = 'patientChk[]' id = 'p_$id'>";
                $dischargedDate = "<input class = 'date-cls' type = 'date' name = 'date' id = 'discharged-date_$id' disabled>";
                $prescribeUpdate = '<a  href="'.$dpURL.'" >'.$dpname.'</a>';
//                

                $responce->rows[$k]['cell'] = array(
                    $val['registration_no'],
                    $arrPatientType[$val['patient_type']],
                    $val['name'],
                    $val['mobile_no'],
                    $this->arrGender[$val['sex']],                 
                    $this->changeDateFormat($val['date_of_admission'], DATETIMEFORMAT, FRONT_DATE_FORMAT),
                    $dischargedDate,
                   // $prescribeUpdate,
                    $actionUpdate
                );
            }
            echo $this->jsonEncode($responce);
            exit;
        }
    
    }

    public function allocationAction() {
        $this->view->wardsData = $this->_wardsResource->fetchAllWards();
    }
    
   
     

}

?>