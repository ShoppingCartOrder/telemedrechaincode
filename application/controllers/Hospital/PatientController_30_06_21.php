<?php

class Hospital_PatientController extends Mylib_Controller_HospitalbaseController {

    //protected $_dashboardResource;

    public function init() {
        parent::init();
        $this->_patientResource = new Application_Model_DbTable_Patients();
        $this->_userResource = new Application_Model_DbTable_Users();


        $this->_doctorResource = new Application_Model_DbTable_Doctors();

        $this->_specialityResource = new Application_Model_DbTable_Speciality();
        $this->_chargeItemResource = new Application_Model_DbTable_ChargeItemName();
        $this->_wardsResource = new Application_Model_DbTable_Wards();
        $this->_appointmentsResource = new Application_Model_DbTable_Appointments();

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
        global $titleType;
        $this->view->arrTitleType = $this->arrPaymentMethod = $titleType;
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


            $result = $this->_patientResource->allRegisteredPatients($params);
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
                //$prescriptionId = $val['dpid'];
                //$pid = $val['patient_id'];


                $responce->rows[$k]['id'] = $id;
                $actionUpdate = '';
                /*if ($prescriptionId) {

                    $dpURL = HOSPITAL_BASE_URL . 'doctor-prescribe/addedit/patient_id/' . $id . '/rid/' . $rId . '/id/' . $prescriptionId;
                    //$dpname = 'Edit Prescription';
                    $dpname = 'View Prescription';
                } else {
                    // $dpURL = HOSPITAL_BASE_URL.'doctor-prescribe/addedit/patient_id/'.$id.'/rid/'.$rId;
                    //$dpname = 'Add Prescription';
                    $dpURL = '#';
                    $dpname = 'Not Prescribed';
                }
                */
                $apntURL = HOSPITAL_BASE_URL . 'appointment/patient-appointment-addedit/id/'.$id.'/rid/'.$rId;
                
                //$responce->rows[$k]['appointment_type'] = $val['appointment_type'];
                $actionUpdate = "<a onclick='return patientEdit($id,$pType);' href='javascript:void(0);'><span class='far fa-edit' aria-hidden='true'></span></a>"
                        . '&nbsp;&nbsp;<a  href="' . $apntURL . '" ><i class="far fa-calendar-alt"></i></a>';
                $chargeUpdate = "<a onclick='return viewPayment($id,$rId);' href='javascript:void(0);'>View Charges</a>";
                //$prescribeUpdate = '<a  href="' . $dpURL . '" >' . $dpname . '</a>';
//                

                $responce->rows[$k]['cell'] = array(
                    $val['registration_no'],
                    $arrPatientType[$val['patient_type']],
                    $val['name'],
                    $val['mobile_no'],
                    $this->arrGender[$val['sex']],
                    $this->changeDateFormat($val['created_at'], DATETIMEFORMAT, FRONT_DATE_FORMAT),
                    $chargeUpdate,
                    $this->changeDateFormat($val['a_date_of_admission'], DATETIMEFORMAT, FRONT_DATE_FORMAT),
                    $actionUpdate
                );
            }
            echo $this->jsonEncode($responce);
            exit;
        }
    }

    public function registerAction() {

        global $nowDateTime;
        $this->view->spcData = $this->_specialityResource->fetchAllSpeciality();
        $request = $this->getRequest();
        if ($data = $request->getPost()) {
            //dd($data);
            $params = $this->__inputPostData;
            //print_r($params); die;
            //$response = array('status' => 0, 'msg' => '');
            $this->validatePatientData($params, $this->errMsg);
            $inputData = new Zend_Filter_Input($this->filters, $this->validators);
            $inputData->setData($params);
            if ($inputData->isValid()) {

                $rid = strtotime($nowDateTime);


                $rid = $params['patient_type'] . $rid;

                $patientData = array(
                    //'panal' => $insertedId,
                    'registered_by' => $this->hospitaluserId,
                    'patient_type' => $params['patient_type'],
                    'name' => $params['title'].' '.$params['fname'].' '.$params['lname'],
                    'registration_no' => $rid,
                    //'date_of_admission' => $params['doa'],
                    'relative_id' => '',
                    'mobile_no' => $params['mobile_no'],
                    'email_id' => $params['email_id'],
                    'sex' => $params['sex'],
                    'birth_date' => $params['dob'],
                   // 'specialization_id' => $params['specialization'],
                    //'doctor_id' => $params['doctor'],
                    'tokan_no' => '',
                   // 'referred_by' => $params['referred_by'],
                   // 'relation' => $params['relation'],
                   // 'relative_name' => $params['relative_name'],
                    'address' => $params['address'],
                    'city' => $params['city'],
                    //'state' => $params['state'],
                    'pin_code' => $params['pin'],
                    //'vilage' => '',
                    'religion' => $params['religion'],
                    'matrial_status' => $params['matrial_status'],
                    'occupation' => $params['occupation'],
                    'drug_allergy' => $params['drug_allergy'],
                    'dignosis' => '',
                    'age' => $params['age'],
                    'blood_group' => $params['blood_group'],
                    //'follow_up_date' => $params['address'],
                    //'reminder_date' => $params['email_id'],
                    'weight' => $params['weight'],
                    'height' => $params['height'],
                        //'dob' => $this->changeDateFormat($params['dob'], DATE_TIME_FORMAT, DATETIMEFORMAT),
                );
                /*
                if (isset($params['ward_id']) && !empty($params['ward_id'])) {
                    $patientData['ward_id'] = $params['ward_id'];
                }
                if (isset($params['bed_no']) && !empty($params['bed_no'])) {
                    $patientData['bed_no'] = $params['bed_no'];
                }*/

                $email = $params['email_id'];
                //dd($email);
                $getUser = $this->_userResource->checkuser($email);
//dd($getUser);
                if ($getUser) {

                    $this->_helper->FlashMessenger()->setNamespace('error')->addMessage('Email already exist.');
                } else {
                    $userdata = array('name' => $params['name'],
                        'email' => $email,
                        'password' => md5(DEFAULT_PASSWORD),
                        'status' => 1,
                        'user_role_type' => PATIENT_ROLE,
                        'activation_code' => Zend_Session::getId());
//dd($userdata);
                    $uinsertedId = $this->_userResource->addUser($userdata);

                    if ($uinsertedId) {
                        $patientData['user_id'] = $uinsertedId;
                        $insertedPatientId = $this->_patientResource->addPatient($patientData);
                        
                        if(!empty($params['doa']) && !empty($params['doctor'])){
                            
                            $patientApntData = array(
                                'patient_type' => $params['patient_type'],
                                'patient_id' => $insertedPatientId,
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
                            try{
                                
                                $insertedAppntId = $this->_patientResource->addPatientAppntData($patientApntData);
                                
                            }catch(Exception $e){
                                echo $e->getMessage();
                                die;
                            }
                            
                            
                        }
//dd($insertedId);
                        if ($insertedPatientId) {

                            $this->_helper->FlashMessenger()->setNamespace('success')->addMessage('Patient record successfully saved.');
                            //$this->_redirect(HOSPITAL_BASE_URL . 'patient/index');
                            
                            if($insertedAppntId){
                                $this->_redirect(HOSPITAL_BASE_URL . 'patient/charges/id/' . $insertedPatientId . '/rid/' . $rid.'/appntid/'.$insertedAppntId.'/reg/1');
                            }else{
                                $this->_redirect(HOSPITAL_BASE_URL . 'patient/charges/id/' . $insertedPatientId . '/rid/' . $rid.'/reg/1');
                            }
                            
                        } else {
                            $this->_helper->FlashMessenger()->setNamespace('error')->addMessage('Patient record is not inserted.Please try again.');

                            /// $this->view->msg = 'Patient record is not inserted.Please try again.';
                        }
                    }
                }

                //$this->_redirect(HOSPITAL_BASE_URL . 'patient/register');
                $this->_redirect(HOSPITAL_BASE_URL . 'patient/patient-profile-edit');
                // }
            } else {

                $msg = $this->getValidatorErrors($inputData->getMessages(), 'admin');

                $this->_helper->FlashMessenger()->setNamespace('error')->addMessage($msg);

                if ($params['id']) {
                    $this->_redirect(HOSPITAL_BASE_URL . 'patient/addedit/id/' . $params['id']);
                } else {
                    $this->_redirect(HOSPITAL_BASE_URL . 'patient/patient-profile-edit');
                }

                //$this->view->msg = $msg;
            }
        }
    }// END //

    protected function validatePatientData($data, $errMsg) {

        if (isset($data['fname'])) {
            $this->validators['fname'] = array(
                'NotEmpty',
                'messages' => array('Please enter patient first name.'));
        }



        if (isset($data['email'])) {
            $this->validators['email'] = array(
                'NotEmpty',
                array('StringLength', 5),
                array('EmailAddress'),
                'messages' => array(
                    'Please enter email id.', 'Email must be atleast 5 characters', 'Plz enter valid email id'
            ));
        }
       

        if (isset($data['dob'])) {
            $this->validators['dob'] = array(
                'NotEmpty',
                'messages' => array('Please enter date of birth.'));
        }
        if (isset($data['sex'])) {
            $this->validators['sex'] = array(
                'NotEmpty',
                'messages' => array('Please enter gender.'));
        }

//        if (isset($data['password'])) {
//            $this->validators['password'] = array(
//                'NotEmpty',
//                'messages' => array('Please enter password.'));
//        }
        if (isset($data['mobile_no'])) {
            $this->validators['mobile_no'] = array(
                'NotEmpty',
                array('StringLength', 10),
                'messages' => array(
                    'Please enter mobile no.', 'Mobile no must be 10 digit'));
        }

        if (isset($data['address'])) {
            $this->validators['address'] = array(
                'NotEmpty',
                'messages' => 'Please enter address.');
        }
        if (isset($data['doa'])) {
            $this->validators['doa'] = array(
                'NotEmpty',
                'messages' => 'Please enter date of Appointment.');
        }
        if (isset($data['specialization'])) {
            $this->validators['specialization'] = array(
                'NotEmpty',
                'messages' => 'Please select specialization.');
        }
        if (isset($data['doctor'])) {
            $this->validators['address'] = array(
                'NotEmpty',
                'messages' => 'Please select doctor.');
        }

//        if (isset($data['termservice'])) {
//            $this->validators['termservice'] = array(
//                'NotEmpty',
//                'messages' => 'Please check term and conditions check box.');
//        }
    }

    public function deletepatientAction() {
        global $nowDateTime;
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $request = $this->getRequest();
        if ($request->getPost()) {
            $data = $this->__inputPostData;

            $userID = $this->_patientResource->getPatientLoginIdByPatientId($data['id']);

            //dd($userID);
            $params['status'] = 0;
            $params['updated_at'] = $nowDateTime;
            $deleteRole = $this->_patientResource->updatePatientDetail($params, $data['id']);
            $data['satus'] = 'inactive';
            $data['updated_at'] = $nowDateTime;
            $deleteUser = $this->_userResource->updateUserDetail($data, $userID);


            echo $deleteRole;
        }
        die;
    }

    public function loginAction() {
        
    }

    public function patientProfileEditAction() {

        $id = $this->getRequest()->getParam('id');
        $this->view->spcData = $this->_specialityResource->fetchAllSpeciality();
        if (!empty($id)) {

            $itemData = $this->_chargeItemResource->fetchAllItemCharge($id);
            $patientData = $this->_patientResource->fetchPatientData($id);
            //dd($patientData);
            $patientFormData = array(
                //'panal' => $insertedId,
                'id' => $patientData['id'],
                'a_id' => $patientData['a_id'],
                'title' => $patientData['title'],
                'fname' => $patientData['fname'],
                'lname' => $patientData['lname'],
                'registration_no' => $patientData['registration_no'],
                'date_of_admission' => $patientData['a_date_of_admission'],
                'mobile_no' => $patientData['mobile_no'],
                'email_id' => $patientData['email_id'],
                'sex' => $patientData['sex'],
                'birth_date' => $patientData['birth_date'],
                'specialization_id' => $patientData['a_specialization_id'],
                'doctor_id' => $patientData['a_doctor_id'],
                'referred_by' => $patientData['a_referred_by'],
                'relation' => $patientData['a_relation'],
                'relative_name' => $patientData['a_relative_name'],
                'address' => $patientData['address'],
                'city' => $patientData['city'],
                'pin_code' => $patientData['pin_code'],
                'religion' => $patientData['religion'],
                'matrial_status' => $patientData['matrial_status'],
                'occupation' => $patientData['occupation'],
                'drug_allergy' => $patientData['drug_allergy'],
                'dignosis' => '',
                'age' => $patientData['age'],
                'blood_group' => $patientData['blood_group'],
                'weight' => $patientData['weight'],
                'height' => $patientData['height'],
            );
//dd($patientFormData);
            $this->view->formdata = $patientFormData;
            $this->view->doctorData = $this->_doctorResource->getDoctorBySpecialityId($patientFormData['specialization_id']);
        }
    }

    public function savePatientProfileAction() {

        global $nowDateTime;

        $request = $this->getRequest();
        if ($data = $request->getPost()) {
            //dd($data);
            $params = $this->__inputPostData;

            $pid = $params['id'];


            //print_r($params); die;
            //$response = array('status' => 0, 'msg' => '');
            $this->validatePatientData($params, $this->errMsg);
            $inputData = new Zend_Filter_Input($this->filters, $this->validators);
            $inputData->setData($params);
            if ($inputData->isValid()) {
                //dd($params);

                $patientData = array(
                    //'user_id' => $this->hospitaluserId,
                    'name' => $params['title'].' '.$params['fname'].' '.$params['lname'],
                    //'registration_no' => strtotime($nowDateTime),
                   // 'date_of_admission' => $params['doa'],
                    'relative_id' => '',
                    'mobile_no' => $params['mobile_no'],
                    //'email_id' => $params['email_id'],
                    'sex' => $params['sex'],
                    'birth_date' => $params['dob'],
                    //'specialization_id' => $params['specialization'],
                    //'doctor_id' => $params['doctor'],
                    'tokan_no' => '',
                    //'referred_by' => $params['referred_by'],
                    //'relation' => $params['relation'],
                    //'relative_name' => $params['relative_name'],
                    'address' => $params['address'],
                    'city' => $params['city'],
                    //'state' => $params['state'],
                    'pin_code' => $params['pin'],
                    //'vilage' => '',
                    'religion' => $params['religion'],
                    'matrial_status' => $params['matrial_status'],
                    'occupation' => $params['occupation'],
                    'drug_allergy' => $params['drug_allergy'],
                    'dignosis' => '',
                    'age' => $params['age'],
                    'blood_group' => $params['blood_group'],
                    //'follow_up_date' => $params['address'],
                    //'reminder_date' => $params['email_id'],
                    'weight' => $params['weight'],
                    'height' => $params['height'],
                        //'dob' => $this->changeDateFormat($params['dob'], DATE_TIME_FORMAT, DATETIMEFORMAT),
                );
//dd($patientData);
               /* if (isset($params['ward_id']) && !empty($params['ward_id'])) {
                    $patientData['ward_id'] = $params['ward_id'];
                }
                if (isset($params['bed_no']) && !empty($params['bed_no'])) {
                    $patientData['bed_no'] = $params['bed_no'];
                }*/
                $insertedId = $this->_patientResource->updateProfileDetail($patientData, $pid);
//dd($insertedId);
                //if ($insertedId) {
                    
                    if(!empty($params['doa']) && !empty($params['doctor'])){
                        
                        $patientApntData = array(
                                //'patient_type' => $params['patient_type'],
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
                            //dd($params['a_id']);
                            try{
                                
                                $insertedId = $this->_patientResource->updatePatientAppntData($patientApntData,$params['a_id']);
                                                            
                            }catch(Exception $e){
                                echo $e->getMessage();
                                die;
                            }
                        
                        }
//dd($insertedId);
                        
                    $this->_helper->FlashMessenger()->setNamespace('success')->addMessage('Patient record successfully saved.');
                    $this->_redirect(HOSPITAL_BASE_URL . 'patient/index');
              /*  } else {
                    $this->_helper->FlashMessenger()->setNamespace('error')->addMessage('Patient record is not inserted.Please try again.');
                    $this->_redirect(HOSPITAL_BASE_URL . 'patient/patient-profile-edit/id/' . $pid);
                    
                }*/




                // }
            } else {

                $msg = $this->getValidatorErrors($inputData->getMessages(), 'admin');
//dd($msg);
                $this->_helper->FlashMessenger()->setNamespace('error')->addMessage($msg);

                if ($params['id']) {
                    $this->_redirect(HOSPITAL_BASE_URL . 'patient/patient-profile-edit/id/' . $pid);
                } else {
                    $this->_redirect(HOSPITAL_BASE_URL . 'patient/register');
                }

                //$this->view->msg = $msg;
            }
        }
    }

    public function chargesAction() {


        $id = $this->getRequest()->getParam('id');
        $this->view->regisNo = $regisNo = $this->getRequest()->getParam('rid');
        $this->view->appntid = $appntId = $this->getRequest()->getParam('appntid');
        $this->view->reg = $this->getRequest()->getParam('reg');
        $items = array('' => 'Select Item');
        $this->view->index = 1;
        $this->view->patientId = $id;
        $this->view->items = $items;
        if (!empty($id)) {
            $this->view->spcData = $this->_specialityResource->fetchAllSpeciality();
            $catData = $this->_chargeItemResource->fetchAllChargeCategory();
            $chargeCat = array('' => 'Please Select');
            foreach ($catData as $catVal) {
                $chargeCat[$catVal['id']] = $catVal['category_name'];
            }

            $this->view->catData = $chargeCat;

            $this->view->patientCharge = $patientCharge = $this->_chargeItemResource->fetchPatientCharges($id, $regisNo);

            if (!empty($patientCharge)) {
                $this->view->index = count($patientCharge);
            }
            //dd($patientCharge);
            //$this->view->itemsData = $itemsData = $this->_chargeItemResource->getItemsChargeByItemId($patientCharge['charge_category_id']);
            //dd($itemsData);
        }
    }

    public function getitemsAction() {
        $request = $this->getRequest();
        $catId = $request->getParam('cat_id');
        $itemsData = $this->_chargeItemResource->getItemsByCatId($catId);
        //dd($itemsData);
        echo json_encode($itemsData);
        exit;
        //$id = $this->getRequest()->getParam('id');
    }

    public function getitemchargeAction() {
        $request = $this->getRequest();
        $catId = $request->getParam('item_id');
        $itemsData = $this->_chargeItemResource->getItemsChargeByItemId($catId);
        $itemCharge['charge'] = $itemsData['charge'];
        $itemCharge['flag'] = $itemsData['flag'];
        
        echo json_encode($itemCharge);
        //echo $itemsData['charge'];
        exit;
        //$id = $this->getRequest()->getParam('id');
    }

    public function addpatientchargesAction() {

        $patientId = $this->getRequest()->getParam('patientId');
        $rId = $this->getRequest()->getParam('rid');
        $appntId = $this->getRequest()->getParam('appntid');
        $reg = $this->getRequest()->getParam('reg');

        if ($patientId && $rId) {

            $this->view->patientId = $patientId;
            $this->view->rId = $rId;
        }
        
        if($appntId){
            $this->view->appntId = $appntId;
        }
        $request = $this->getRequest();
        $params = $this->__inputPostData;

        if ($request->isPost()) {
            $appntid = 0;
            $data = $request->getPost();
            //dd($data);

            if (isset($data['patientId'])) {
                $patientId = $data['patientId'];
            }
            if (isset($data['rId'])) {
                $rId = $data['rId'];
            }
            if (isset($data['appntid'])) {
                $appntid = $data['appntid'];
            }

            if ((!$patientId) && (!$rId)) {

                $this->_redirect(HOSPITAL_BASE_URL . 'patient/index');
            }

            $this->view->patientId = $patientId;
            $this->view->rId = $rId;
            $this->view->appntid = $appntid;

            $billNo = $this->_chargeItemResource->getLastId() . '-' . $patientId;
            $totalCharges = array();

            $totalCharges['patient_id'] = $patientId;
            $totalCharges['registration_no'] = $rId;
            $totalCharges['appointment_id'] = $appntid;
            
            $appntURL = '';
            
            if($appntid){
               $appntURL .='/appntid/'.$appntid;
            }

            //$totalCharges[] = '';
            /* $totalCharges[] = $patientId;
              $totalCharges[] = $rId;
              $totalCharges[] = $billNo; */


            //dd($data);
            if (empty($data['total_amt'])) {

                $this->_helper->FlashMessenger()->setNamespace('error')->addMessage("Field can not be empty.");
                
                
                $this->_redirect(HOSPITAL_BASE_URL . 'patient/charges/id/' . $params['patientId'] . '/rid/' . $rId.$appntURL);
                
                
            } else {

                $totalCharges['total_amt'] = $data['total_charges'];
                $totalCharges['total_paid_amount'] = $data['total_amt'];
                //$totalCharges[] = $data['total_amt'];
            }
            if (empty($data['payment_method'])) {

                $this->_helper->FlashMessenger()->setNamespace('error')->addMessage("Field can not be empty.");
                
                
                $this->_redirect(HOSPITAL_BASE_URL . 'patient/charges/id/' . $params['patientId'] . '/rid/' . $rId.$appntURL);
               
            } else {
                $totalCharges['payment_method'] = $data['payment_method'];
                //$totalCharges[] = $data['payment_method'];
            }

            $totalCharges['remark'] = $data['remark'];
            //$totalCharges[] = $data['remark'];

            if (!empty($data['charge_category_id'])) {

                $str_data = '';
                $cnt = count($data['charge_category_id']);
                $first_key = key($data['charge_category_id']);
                $total_count = $first_key + $cnt;
                $cnt_update = 0;
                $cnt_insert = 0;


                for ($i = $first_key; $i < $total_count; $i++) {
                    if (isset($data['id'][$i])) {
                        //dd($data);
                        $arr_data_update[] = $data['id'][$i];
                        $arr_data_update[] = $patientId;

                        $arr_data_update[] = $rId;
                        
                        if(!$data['flag'][$i]){
                            $arr_data_update[] = $appntid;
                        }else{
                            $arr_data_update[] = 0;
                        }
                        
                        //$arr_data_update[] = $billNo;

                        if (!empty($data['bill_no'][$i])) {
                            $arr_data_update[] = $data['bill_no'][$i];
                        } else {
                            $this->_helper->FlashMessenger()->setNamespace('error')->addMessage("Field can not be empty.");
                            $this->_redirect(HOSPITAL_BASE_URL . 'patient/charges/id/' . $params['patientId'] . '/rid/' . $rId.$appntURL);
                        }

                        if (!empty($data['charge_category_id'][$i])) {
                            $arr_data_update[] = $data['charge_category_id'][$i];
                        } else {
                            $this->_helper->FlashMessenger()->setNamespace('error')->addMessage("Field can not be empty.");
                            $this->_redirect(HOSPITAL_BASE_URL . 'patient/charges/id/' . $params['patientId'] . '/rid/' . $rId.$appntURL);
                        }

                        if (!empty($data['charge_item_id'][$i])) {
                            $arr_data_update[] = $data['charge_item_id'][$i];
                        } else {
                            $this->_helper->FlashMessenger()->setNamespace('error')->addMessage("Field can not be empty.");
                            $this->_redirect(HOSPITAL_BASE_URL . 'patient/charges/id/' . $params['patientId'] . '/rid/' . $rId.$appntURL);
                        }



                        if (!empty($data['quantity'][$i])) {
                            $arr_data_update[] = $data['quantity'][$i];
                        } else {
                            $this->_helper->FlashMessenger()->setNamespace('error')->addMessage("Field can not be empty.");
                            $this->_redirect(HOSPITAL_BASE_URL . 'patient/charges/id/' . $params['patientId'] . '/rid/' . $rId.$appntURL);
                        }

                        if (!empty($data['amount'][$i])) {
                            $arr_data_update[] = $data['amount'][$i];
                        } else {
                            $this->_helper->FlashMessenger()->setNamespace('error')->addMessage("Field can not be empty.");
                            $this->_redirect(HOSPITAL_BASE_URL . 'patient/charges/id/' . $params['patientId'] . '/rid/' . $rId.$appntURL);
                        }


                        if (!empty($data['discount'][$i])) {
                            $arr_data_update[] = $data['discount'][$i];
                        } else {
                            $arr_data_update[] = 0;
                        }

                        if (!empty($data['net_amount'][$i])) {
                            $arr_data_update[] = $data['net_amount'][$i];
                        } else {
                            $this->_helper->FlashMessenger()->setNamespace('error')->addMessage("Field can not be empty.");
                            $this->_redirect(HOSPITAL_BASE_URL . 'patient/charges/id/' . $params['patientId'] . '/rid/' . $rId.$appntURL);
                        }
                        $cnt_update++;
                    } else {

                        $cnt_insert++;
                        $arr_data[] = $patientId;
                        $arr_data[] = $rId;
                        
                        
                        if(!$data['flag'][$i]){
                            $arr_data[] = $appntid;
                        }else{
                            $arr_data[] = 0;
                        }
                        //$billNo = $patientId.$rId;
                        $arr_data[] = $billNo;

                        $totalCharges['bill_no'] = $billNo;

                        if (!empty($data['charge_category_id'][$i])) {
                            $arr_data[] = $data['charge_category_id'][$i];
                        } else {
                            $this->_helper->FlashMessenger()->setNamespace('error')->addMessage("Field can not be empty.");
                            $this->_redirect(HOSPITAL_BASE_URL . 'patient/charges/id/' . $params['patientId'] . '/rid/' . $rId.$appntURL);
                        }

                        if (!empty($data['charge_item_id'][$i])) {
                            $arr_data[] = $data['charge_item_id'][$i];
                        } else {
                            $this->_helper->FlashMessenger()->setNamespace('error')->addMessage("Field can not be empty.");
                            $this->_redirect(HOSPITAL_BASE_URL . 'patient/charges/id/' . $params['patientId'] . '/rid/' . $rId.$appntURL);
                        }



                        if (!empty($data['quantity'][$i])) {
                            $arr_data[] = $data['quantity'][$i];
                        } else {
                            $this->_helper->FlashMessenger()->setNamespace('error')->addMessage("Field can not be empty.");
                            $this->_redirect(HOSPITAL_BASE_URL . 'patient/charges/id/' . $params['patientId'] . '/rid/' . $rId.$appntURL);
                        }

                        if (!empty($data['amount'][$i])) {
                            $arr_data[] = $data['amount'][$i];
                        } else {
                            $this->_helper->FlashMessenger()->setNamespace('error')->addMessage("Field can not be empty.");
                            $this->_redirect(HOSPITAL_BASE_URL . 'patient/charges/id/' . $params['patientId'] . '/rid/' . $rId.$appntURL);
                        }


                        if (!empty($data['discount'][$i])) {
                            $arr_data[] = $data['discount'][$i];
                        } else {
                            $arr_data[] = 0;
                        }

                        if (!empty($data['net_amount'][$i])) {
                            $arr_data[] = $data['net_amount'][$i];
                        } else {
                            $this->_helper->FlashMessenger()->setNamespace('error')->addMessage("Field can not be empty.");
                            $this->_redirect(HOSPITAL_BASE_URL . 'patient/charges/id/' . $params['patientId'] . '/rid/' . $rId.$appntURL);
                        }
                    }
                }
                if (!empty($arr_data) || !empty($arr_data_update) || !empty($cat_data)) {


                    if (!empty($arr_data)) {

                        //$totalCharges['bill_no'] = $totalCharges['bill_no'];
                        $this->_chargeItemResource->insertChargesDetails($arr_data, $cnt_insert);

                        $this->_helper->FlashMessenger()->setNamespace('success')->addMessage("Charges added successfully.");

                        $this->_chargeItemResource->updateTotalChargeAmountDetails($totalCharges);
                        
                        if($appntId){
                            $this->_redirect(HOSPITAL_BASE_URL . 'patient/printinvoice/patient_id/' . $params['patientId'] . '/rid/' . $rId .'/appntid/'.$appntId. '/bill_no/' . $billNo);
                        }else{
                            $this->_redirect(HOSPITAL_BASE_URL . 'patient/printinvoice/patient_id/' . $params['patientId'] . '/rid/' . $rId . '/bill_no/' . $billNo);
                        }
                        
                    }

                    if (!empty($arr_data_update)) {
                        //dd($arr_data_update);
                        $this->_chargeItemResource->updateChargeDetails($arr_data_update, $cnt_update);
                        //$this->_chargeItemResource->updateTotalChargeAmount($totalCharges);
                        $this->_helper->FlashMessenger()->setNamespace('success')->addMessage("Charges updated successfully.");
                        $this->_chargeItemResource->updateTotalChargeAmountDetails($totalCharges);
                    }

                    $this->_redirect(HOSPITAL_BASE_URL . 'patient/index');
                }
            } else {
                //dd(222);
                $this->_helper->FlashMessenger()->setNamespace('error')->addMessage("Please add charges.");
                $this->_redirect(HOSPITAL_BASE_URL . 'patient/charges/id/' . $params['patientId'] . '/rid/' . $rId.$appntURL);
            }
        }
    }

    //public function deleteitemchargeAction() {
    public function deleteitemchargeAction() {


        if ($data = $this->getRequest()->getPost()) {

            $totalCharge = array();

            $arr_charge_id[] = $data['charge_id'];
            $totalCharge['patient_id'] = $data['patient_id'];
            $totalCharge['registration_no'] = $data['r_id'];
            $totalCharge['bill_no'] = $data['bill_no'];

            //print_r($arr_dlt_extr_fld);die;
            if ($this->_chargeItemResource->deleteChargedDetails($arr_charge_id)) {

                $totalAmount = $this->_chargeItemResource->fetchPatientBillCharges($totalCharge);

                //dd($totalAmount);
                if (!empty($totalAmount)) {
                    $this->_chargeItemResource->updateTotalChargeNetAmount($totalAmount, $totalCharge);
                } else {
                    $this->_chargeItemResource->deleteTotalBillChargedDetails($totalCharge);
                }

                echo "1";
                exit;
            } else {
                echo "0";
                exit;
            }
        }
    }

    public function printinvoiceAction() {
        global $arrGender;
        $this->_helper->layout->disableLayout();
        $id = $this->getRequest()->getParam('patient_id');
        $this->view->patient_id = $id;

        $rid = $this->getRequest()->getParam('rid');
        $this->view->rid = $rid;
        $billNo = $this->getRequest()->getParam('bill_no');
        $appntId = $this->getRequest()->getParam('appntid');
        $this->view->bill_no = $billNo;

        $patientRegisteredData = $this->_patientResource->fetchRegisteredPatientData($id, $rid);
        //dd($patientRegisteredData);
        $this->view->patientRegisteredData = $patientRegisteredData;
        $this->view->arrGender = $arrGender;
        $this->view->patientCharge = $patientCharge = $this->_chargeItemResource->fetchPatientChargesByBillNo($id, $rid, $billNo);
        $this->view->patientTotalCharge = $patientTotalCharge = $this->_chargeItemResource->fetchPatientTotalChargesByBillNo($id, $rid, $billNo);
        if($appntId){
            $this->view->patientAppointmenetDetails = $this->_appointmentsResource->fetchAppointmentWithDoctorInfo($appntId,$id);
        }
    }

   public function patientchargesAction() {
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
                $prescriptionId = $val['dpid'];
                //$pid = $val['patient_id'];


                $responce->rows[$k]['id'] = $id;
                $actionUpdate = '';



                //$responce->rows[$k]['appointment_type'] = $val['appointment_type'];
                $chargeUpdate = "<a onclick='return addPayment($id,$rId);' href='javascript:void(0);'>Add Charges</a>";


                $responce->rows[$k]['cell'] = array(
                    $val['registration_no'],
                    $val['name'],
                    $this->arrGender[$val['sex']],
                    $val['mobile_no'],
                    $this->changeDateFormat($val['date_of_admission'], DATETIMEFORMAT, FRONT_DATE_FORMAT),
                    $chargeUpdate
                );
            }
            echo $this->jsonEncode($responce);
            exit;
        }
    }

    public function viewchargesAction() {


        $id = $this->getRequest()->getParam('id');
        $this->view->regisNo = $regisNo = $this->getRequest()->getParam('rid');
        $items = array('' => 'Select Item');
        $this->view->index = 1;
        $this->view->patientId = $id;
        $this->view->items = $items;
        if (!empty($id)) {
            $this->view->spcData = $this->_specialityResource->fetchAllSpeciality();
            $catData = $this->_chargeItemResource->fetchAllChargeCategory();
            $chargeCat = array('' => 'Please Select');
            foreach ($catData as $catVal) {
                $chargeCat[$catVal['id']] = $catVal['category_name'];
            }

            $this->view->catData = $chargeCat;

            $this->view->patientCharge = $patientCharge = $this->_chargeItemResource->fetchPatientCharges($id, $regisNo);

            if (!empty($patientCharge)) {
                $this->view->index = count($patientCharge);
            }
            //dd($patientCharge);
            //$this->view->itemsData = $itemsData = $this->_chargeItemResource->getItemsChargeByItemId($patientCharge['charge_category_id']);
            //dd($itemsData);
        }
    }

    public function patientRegistrationIpdAction() {
        $id = $this->getRequest()->getParam('id');
        $this->view->spcData = $this->_specialityResource->fetchAllSpeciality();
        $this->view->wardList = $wardList = $this->_wardsResource->fetchAllWards();
        // dd($wardList);
        if (!empty($id)) {

            $itemData = $this->_chargeItemResource->fetchAllItemCharge($id);
            $patientData = $this->_patientResource->fetchPatientData($id);
            //dd($itemData);
            $patientFormData = array(
                //'panal' => $insertedId,
                'id' => $patientData['id'],
                'a_id' => $patientData['a_id'],
                'name' => $patientData['name'],
                'registration_no' => $patientData['registration_no'],
                'date_of_admission' => $patientData['a_date_of_admission'],
                'mobile_no' => $patientData['mobile_no'],
                'email_id' => $patientData['email_id'],
                'sex' => $patientData['sex'],
                'birth_date' => $patientData['birth_date'],
                'specialization_id' => $patientData['a_specialization_id'],
                'doctor_id' => $patientData['a_doctor_id'],
                'referred_by' => $patientData['a_referred_by'],
                'relation' => $patientData['a_relation'],
                'relative_name' => $patientData['a_relative_name'],
                'address' => $patientData['address'],
                'city' => $patientData['city'],
                'pin_code' => $patientData['pin_code'],
                'religion' => $patientData['religion'],
                'matrial_status' => $patientData['matrial_status'],
                'occupation' => $patientData['occupation'],
                'drug_allergy' => $patientData['drug_allergy'],
                'dignosis' => '',
                'age' => $patientData['age'],
                'blood_group' => $patientData['blood_group'],
                'weight' => $patientData['weight'],
                'height' => $patientData['height'],
                'ward_id' => $patientData['a_ward_id'],
                'bed_no' => $patientData['a_bed_no'],
            );

//dd($patientFormData);
            $this->view->formdata = $patientFormData;
            $this->view->doctorData = $this->_doctorResource->getDoctorBySpecialityId($patientFormData['specialization_id']);
        }
    }
    
   /* public function patientchargesAction() {
        global $nowDateTime;

        global $healthCondition;
        //dd($this->gender);
        $request = $this->getRequest();
        $this->view->appntid = $request->getParam('appntid');
        //dd($this->view->appntid);
        global $noRecord;
        if ($request->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $data = $this->getRequest()->getPost();
            $params = $this->getSearchParams($data);


            $params['fields']['main'] = array('*');
            $params['sidx'] = 'id';
            $params['sord'] = 'DESC';
            $params['appntid'] = $request->getParam('appntid');
           


            $result = $this->_chargeItemResource->allCharges($params);
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
                //$prescriptionId = $val['dpid'];
                //$pid = $val['patient_id'];


                $responce->rows[$k]['id'] = $id;
                $actionUpdate = '';



                //$responce->rows[$k]['appointment_type'] = $val['appointment_type'];
                //$chargeUpdate = "<a onclick='return addPayment($id,$rId);' href='javascript:void(0);'>Add Charges</a>";


                $responce->rows[$k]['cell'] = array(
                    $val['registration_no'],
                    $val['name'],
                    $val['appointment_id'],
                    $val['bill_no'],
                    $val['category_name'],
                    $val['sub_category_name'],
                    $val['net_amount'],
                    
                    $this->changeDateFormat($val['bill_date'], DATETIMEFORMAT, FRONT_DATE_FORMAT),
                    //$chargeUpdate
                );
            }
            echo $this->jsonEncode($responce);
            exit;
        }
    }*/

}

?>