<?php

class Hospital_WardController extends Mylib_Controller_HospitalbaseController {

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
        $paramsData = $request->getParams();
        $this->view->ward_no = $paramsData['wardno'];
        $this->view->bed_no = $paramsData['bedno'];
        $aid = '';
        if (isset($paramsData['id'])) {
            $this->view->id = $aid = $paramsData['id'];
        } else {
            $this->view->id = '';
        }

        global $noRecord;
        if ($request->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $data = $this->getRequest()->getPost();
            $params = $this->getSearchParams($data);
//dd($params);

            $params['fields']['main'] = array('*');
            $params['sidx'] = 'id';
            $params['sord'] = 'DESC';
            $params['condition']['patient_type'] = 2;
            //$params['condition']['ward_id'] = $paramsData['wardno'];
            //$params['condition']['bed_no'] = $paramsData['bedno'];
            $params['condition']['allocation_id'] = $aid;

            $result = $this->_patientResource->allPatientsWithWardBed($params);
            //dd($result);
            $responce = new stdClass();
            $responce->page = $result['page'];
            $responce->total = $result['total'];
            $responce->records = $result['records'];


            foreach ($result['result'] as $k => $val) {

                //$statusUpdate = '';
                $disabled = '';
                $checked = '';
                $id = $val['id'];
                $rId = $val['registration_no'];
                $pType = $val['patient_type'];
                $prescriptionId = $val['dpid'];
                $allocationId = $val['allocation_id'];
                $bedNo = '';
                //$pid = $val['patient_id'];
                
                if ($val['bed_no']) {
                    $disabled = 'disabled';
                    $bedNo = 'Bed No:' . $val['bed_no'];
                    // $checked = 'checked';
                }
                $disabledDis = 'disabled';
                if ($allocationId == $aid) {
                    $disabledDis = '';
                    $bedNo = 'Bed No:' . $val['bed_no'];
                    // $checked = 'checked';
                }
                

                $responce->rows[$k]['id'] = $id;
                $actionUpdate = '';
                if ($prescriptionId) {

                    $dpURL = HOSPITAL_BASE_URL . 'doctor-prescribe/addedit/patient_id/' . $id . '/rid/' . $rId . '/id/' . $prescriptionId;
                    //$dpname = 'Edit Prescription';
                    $dpname = 'View Prescription';
                } else {
                    // $dpURL = HOSPITAL_BASE_URL.'doctor-prescribe/addedit/patient_id/'.$id.'/rid/'.$rId;
                    //$dpname = 'Add Prescription';
                    $dpURL = '#';
                    $dpname = 'Not Prescribed';
                }


                //$responce->rows[$k]['appointment_type'] = $val['appointment_type'];
                $allocationUpdate = "<input class = 'bed-a-cls' type = 'radio' value = '1' onchange='return bedAllocation($id,$pType);' name = 'bsChk[]' id = 'ba_$id' $disabled>";

                $beDischargedUpdate = "<input class = 'bed-d-cls' type = 'radio' value = '2' onclick='return bedDischarge($id,$pType);' name = 'bsChk[]' id = 'bd_$id' $disabledDis>";


                $actionUpdate = "<a title = 'Save' onclick='return saveBedStatus($id,$rId,$allocationId);' href='javascript:void(0);'><span class='far fa-save' aria-hidden='true'></span></a>";

                $dischargedDate = "<input class = 'date-cls' type = 'date' name = 'date' id = 'discharged-date_$id'>";

                //$prescribeUpdate = '<a  href="'.$dpURL.'" >'.$dpname.'</a>';

                $responce->rows[$k]['cell'] = array(
                    $val['registration_no'],
                    $val['ward_name'] . ' <br> ' . $bedNo,
                    $val['name'],
                    $val['mobile_no'],
                    $this->arrGender[$val['sex']],
                    $this->changeDateFormat($val['date_of_admission'], DATETIMEFORMAT, FRONT_DATE_FORMAT),
                    $dischargedDate,
                    $allocationUpdate,
                    $beDischargedUpdate,
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

    public function getbedsAction() {
        $request = $this->getRequest();
        $wardId = $request->getParam('ward_id');
        $wardsArr = $this->_wardsResource->getWardById($wardId);
        $allocatedwardsArr = $this->_wardsResource->getAllocatedBedsDetails($wardId);
        $bedsAllocationDetails = array();
        if (!empty($allocatedwardsArr)) {
            foreach ($allocatedwardsArr as $key => $val) {
                $bedsAllocationDetails[$val['bed_no']] = $val;
            }
        }
        $arrAllBeds = array();
        $strAllBeds = '';
        //$prescribeUpdate = '<a  href="'.$dpURL.'" >'.$dpname.'</a>';
        if (!empty($wardsArr)) {
            $noOfBeds = $wardsArr['no_of_beds'];
            for ($i = 1; $i <= $noOfBeds; $i++) {
                if ((isset($bedsAllocationDetails[$i]))) {
                    $allocationId = $bedsAllocationDetails[$i]['id'];
                    $src = WEBSITE_IMAGE . 'bed_with_patient.png';
                    $strAllBeds.= "<a onclick='return toggleImage($i,$allocationId);'><img id='" . $i . "' src='" . $src . "' width='150' height='150'></a>";
                } else {

                    $src = WEBSITE_IMAGE . 'bed.png';
                    $strAllBeds.= "<a onclick='return toggleImage($i);'><img id='" . $i . "' src='" . $src . "'  width='150' height='150'></a>";
                }
            }
        }
        //dd($doctorsArr);
        echo $strAllBeds;
        exit;
        //$id = $this->getRequest()->getParam('id');
    }

    public function bedstatusupdateAction() {
        global $nowDateTime;
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $request = $this->getRequest();
        if ($request->getPost()) {
            $data = $this->__inputPostData;
            //dd($data);


            $paramsData['status'] = $data['bedstatus'];
            if($data['dischargeddate']){
                $paramsData['date_of_release'] = $data['dischargeddate'];
            }
            

            if ($paramsData['status'] == 1) {
                
                $paramsData['date_of_allocation'] = $nowDateTime;

                $paramsData['patient_id'] = $data['id'];
                $paramsData['registration_no'] = $data['registration_no'];
                $paramsData['ward_id'] = $data['ward_no'];
                $paramsData['bed_no'] = $data['bed_no'];
                $paramsData['created_by'] = $this->hospitaluserId;
                $deleteRole = $this->_wardsResource->addBed($paramsData);
                echo '1';
            } else if ($paramsData['status'] == 2) {

                if (($paramsData['date_of_release'] != '') && ($data['allocation_id'])) {

                    //$params['date_of_release'] = $data['date_of_release'];
                    $update = $this->_wardsResource->updateBedStatus($paramsData, $data['allocation_id']);
                    
                    $cond = array('id = ?' => $data['allocation_id']);
                    $delete = $this->_wardsResource->deleteDischargedBed($cond);
                    echo '2';
                    
                } else {
                    echo '3';
                }
            }



            
        } else {
            echo '4';
        }
        die;
    }

}

?>