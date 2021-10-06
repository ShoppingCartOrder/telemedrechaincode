<?php

class Hospital_AppointmentController extends Mylib_Controller_HospitalbaseController {

    //protected $_dashboardResource;

    public function init() {
        parent::init();
        $this->_patientResource = new Application_Model_DbTable_Patients();
        $this->_userResource = new Application_Model_DbTable_Users();


        $this->_doctorResource = new Application_Model_DbTable_Doctors();

        $this->_specialityResource = new Application_Model_DbTable_Speciality();
        $this->_chargeItemResource = new Application_Model_DbTable_ChargeItemName();
        $this->_wardsResource = new Application_Model_DbTable_Wards();
        $this->_appointmentResource = new Application_Model_DbTable_Appointments();

        $this->_helper->layout->setLayout('hospital');
        global $arrGender;
        $this->view->arrGender = $this->arrGender = $arrGender;
        global $arrBloodGroup;
        $this->view->arrBloodGroup = $this->arrBloodGroup = $arrBloodGroup;
        global $arrRelation;
        $this->view->arrRelation = $this->arrRelation = $arrRelation;
        global $arrMatrialStatus;
        $this->view->arrMatrialStatus = $this->arrMatrialStatus = $arrMatrialStatus;
        global $arrPaymentMethod;
        $this->view->arrPaymentMethod = $this->arrPaymentMethod = $arrPaymentMethod;
    }

    /**
     * This function is used to show all function in dashboard 
     * Created By :  bidhan
     * Date : 20 May,2019
     * @param void
     * @return void
     */
    public function indexAction() {

        global $nowDateTime;
        global $noRecord;
        global $arrPatientType;

        $request = $this->getRequest();
        $this->view->rid = $rid = $this->getRequest()->getParam('rid');
        $this->view->id = $id = $this->getRequest()->getParam('id');
        $this->view->ptype = $ptype = $this->getRequest()->getParam('ptype');

        if ($request->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $getParams = $this->getRequest()->getParams();
            $data = $this->getRequest()->getPost();
            $params = $this->getSearchParams($data);


            $params['fields']['main'] = array('*');
            $params['sidx'] = 'id';
            $params['sord'] = 'DESC';
            if(isset($getParams['id'])){
                $params['id'] = $getParams['id'];
            }
            
            if(isset($getParams['rid'])){
                $params['rid'] = $getParams['rid'];
            }
            
            


            $result = $this->_appointmentResource->allAppointments($params);
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
                
                //$pid = $val['patient_id'];


                $responce->rows[$k]['id'] = $id;
                $actionUpdate = '';
                $appntChrgLstUrl = HOSPITAL_BASE_URL.'patient/patientcharges/appntid/'.$val['id'];


                //$responce->rows[$k]['appointment_type'] = $val['appointment_type'];
                $chargeUpdate = "<a title = 'Add payment' onclick='return addPayment($pId,$rId,$id);' href='javascript:void(0);'><i class='fa fa-plus' aria-hidden='true'></i></a>&nbsp;<a href='$appntChrgLstUrl' title = 'View payment details'><i class='fa fa-eye' aria-hidden='true'></i></a>";


                $responce->rows[$k]['cell'] = array(
                    $val['id'],
                    $arrPatientType[$val['patient_type']],
                    $val['registration_no'],
                    $val['p_name'],
                    $this->arrGender[$val['sex']],
                    $val['mobile_no'],
                    $val['dprt_name'],
                    $val['doctor_name'],
                    $this->changeDateFormat($val['date_of_admission'], DATETIMEFORMAT, FRONT_DATE_FORMAT),
                    $val['paid_amount'],
                    $chargeUpdate
                );
            }
            echo $this->jsonEncode($responce);
            exit;
        }
    }

  

    public function patientAppointmentAddeditAction() {


        $this->view->rid = $rid = $this->getRequest()->getParam('rid');
        $this->view->id = $id = $this->getRequest()->getParam('id');
        $this->view->spcData = $this->_specialityResource->fetchAllSpeciality();
        if (!empty($id) && !empty($rid)) {


            $patientData = $this->_patientResource->fetchPatientData($id);
            $prevoiusPaidAppointmentDetails = $this->_chargeItemResource->fetchPrevoiusPaidAppointmentDetails($id,$rid);
            //dd($patientData);
            $this->view->registration_date = $this->changeDateFormat($patientData['created_at'], DATETIMEFORMAT, FRONT_DATE_FORMAT);
            $this->view->last_appnt_date = $this->changeDateFormat($patientData['a_date_of_admission'], DATETIMEFORMAT, FRONT_DATE_FORMAT);
            $this->view->prevoiusPaidAppointmentDate = $this->changeDateFormat($prevoiusPaidAppointmentDetails['bill_date'], DATETIMEFORMAT, FRONT_DATE_FORMAT);
            //dd($patientData);
            
//dd($patientFormData);
            //$this->view->formdata = $patientFormData;
            //$this->view->doctorData = $this->_doctorResource->getDoctorBySpecialityId($patientFormData['specialization_id']);
        }else{
            $this->_redirect(HOSPITAL_BASE_URL.'patient/patient-profile-edit');
             
        }
    }

    public function saveappoinmentAction() {

        global $nowDateTime;
        $this->view->spcData = $this->_specialityResource->fetchAllSpeciality();
        $request = $this->getRequest();
        //$this->view->id = $id = $request->getParam('id');
        //$this->view->rid = $rid = $request->getParam('rid');

        if ($data = $request->getPost()) {
            //dd($data);
            $params = $this->__inputPostData;

            $this->validateAppoinmentData($params, $this->errMsg);

            $inputData = new Zend_Filter_Input($this->filters, $this->validators);
            $inputData->setData($params);
            if ($inputData->isValid()) {

                $rid = $params['rid'];
                $id = $params['id'];

                $patientApntData = array(
                    'patient_type' => $params['apnt_patient_type'],
                    'patient_id' => $id,
                    'registration_no' => $rid,
                    'date_of_admission' => $params['doa'],
                    'specialization_id' => $params['specialization'],
                    'doctor_id' => $params['doctor'],
                    'relation' => $params['relation'],
                    'relative_name' => $params['relative_name'],
                    'referred_by' => $params['referred_by'],
                );

                if (isset($params['ward_id']) && !empty($params['ward_id'])) {
                    $patientApntData['ward_id'] = $params['ward_id'];
                }
                if (isset($params['bed_no']) && !empty($params['bed_no'])) {
                    $patientApntData['bed_no'] = $params['bed_no'];
                }
                $insertedId = $this->_patientResource->addPatientAppntData($patientApntData);
                //$insertedId =1;
                if ($insertedId) {
                    
                    $billStatus = $this->_chargeItemResource->fetchLastAppointmentCharge($id,$rid,$params['doa']);
                    
                    if(!empty($billStatus)){
                        $this->_helper->FlashMessenger()->setNamespace('success')->addMessage('Patient appointment successfully saved.');

                    }else{
                        $this->_redirect(HOSPITAL_BASE_URL . 'patient/charges/id/' . $id . '/rid/' . $rid.'/appntid/'.$insertedId);
                    }
                    
                    $this->_redirect(HOSPITAL_BASE_URL . 'appointment/patient-appointment-addedit/id/' . $id . '/rid/' . $rid);
                } else {
                    $this->_helper->FlashMessenger()->setNamespace('error')->addMessage('Patient record is not inserted.Please try again.');
                }


                if ($params['patient_type'] == 1) {
                    $this->_redirect(HOSPITAL_BASE_URL . 'appointment/patient-appointment-addedit/id/' . $id . '/rid/' . $rid);
                } else {
                    $this->_redirect(HOSPITAL_BASE_URL . 'appointment/patient-appointment-ipd-addedit/id/' . $id . '/rid/' . $rid);
                }
            } else {

                $msg = $this->getValidatorErrors($inputData->getMessages(), 'admin');

                $this->_helper->FlashMessenger()->setNamespace('error')->addMessage($msg);

                if ($params['patient_type'] == 1) {
                    $this->_redirect(HOSPITAL_BASE_URL . 'appointment/patient-appointment-addedit/id/' . $id . '/rid/' . $rid);
                } else {
                    $this->_redirect(HOSPITAL_BASE_URL . 'appointment/patient-appointment-ipd-addedit/id/' . $id . '/rid/' . $rid);
                }
            }
        }
    }

    protected function validateAppoinmentData($data, $errMsg) {

        if (isset($data['specialization'])) {
            $this->validators['specialization'] = array(
                'NotEmpty',
                'messages' => array('Please enter specialization name.'));
        }

        if (isset($data['doctor'])) {
            $this->validators['doctor'] = array(
                'NotEmpty',
                'messages' => array('Please enter doctor name.'));
        }

        if (isset($data['doa'])) {

            $this->validators['doa'] = array(
                'NotEmpty',
                'messages' => array('Please enter date and time.'));
        }
    }

    public function deleteappointmentAction() {
        global $nowDateTime;
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $request = $this->getRequest();
        if ($request->getPost()) {
            $data = $this->__inputPostData;
            //dd($data);
            $status = $this->_appointmentResource->deleteAppointmentData($data['id']);

            echo 1;
        }
        die;
    }

    public function patientAppointmentIpdAddeditAction() {


        $this->view->rid = $rid = $this->getRequest()->getParam('rid');
        $this->view->id = $id = $this->getRequest()->getParam('id');
        $this->view->spcData = $this->_specialityResource->fetchAllSpeciality();
        $this->view->wardList = $wardList = $this->_wardsResource->fetchAllWards();
        
        if (!empty($id)) {


            $patientData = $this->_patientResource->fetchPatientData($id);
            $this->view->registration_date = $this->changeDateFormat($patientData['created_at'], DATETIMEFORMAT, FRONT_DATE_FORMAT);
            $this->view->last_appnt_date = $this->changeDateFormat($patientData['a_date_of_admission'], DATETIMEFORMAT, FRONT_DATE_FORMAT);
            //dd($patientData);
            
//dd($patientFormData);
            //$this->view->formdata = $patientFormData;
            //$this->view->doctorData = $this->_doctorResource->getDoctorBySpecialityId($patientFormData['specialization_id']);
        }
        
    }

}

?>