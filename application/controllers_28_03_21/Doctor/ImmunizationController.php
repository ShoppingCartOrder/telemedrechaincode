<?php

class Doctor_ImmunizationController extends Mylib_Controller_DoctorbaseController {

    //protected $_dashboardResource;

    public function init() {
        parent::init();
        $this->_patientResource = new Application_Model_DbTable_Patients();
        $this->_userResource = new Application_Model_DbTable_Users();


        $this->_doctorResource = new Application_Model_DbTable_Doctors();

        $this->_specialityResource = new Application_Model_DbTable_Speciality();
        $this->_chargeItemResource = new Application_Model_DbTable_ChargeItemName();
        $this->_ImmunizationResource = new Application_Model_DbTable_Immunization();

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
    
    public function searchImmunizationNameAction() {


        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $request = $this->getRequest();
        $term = $request->getParam('query');
        $labTestValue = $this->_ImmunizationResource->getImmunizationDetailsByName($term);
        $result = array();
        //$alterResult = array();
        foreach ($labTestValue as $key => $val) {


            $result[$key]['label'] = ucwords($val['vaccine_name']);

            $result[$key]['id'] = $val['id'];
        }

        //$resultTotal = array_merge($alterResult,$result);
        echo json_encode($result);
        exit;
    }

 

 
     

}

?>