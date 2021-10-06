<?php

class Doctor_LaboratoryController extends Mylib_Controller_DoctorbaseController {

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
    }

    /**
     * This function is used to show all function in dashboard 
     * Created By :  Bidhan
     * Date : 3 May,2020
     * @param void
     * @return void
     */
    
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

   

}

?>