<?php

class PatientsController extends Mylib_Controller_BaseController {

    //protected $_dashboardResource;

    public function init() {
        parent::init();
        $this->_patientResource = new Application_Model_DbTable_Patients();
        $this->_userResource = new Application_Model_DbTable_Users();


        $this->_doctorResource = new Application_Model_DbTable_Doctors();

        $this->_departmentResource = new Application_Model_DbTable_Departments();

        $this->_helper->layout->setLayout('hospital_inner_layout');
        global $gender;
        $this->view->gender = $gender;
    }
    
    public function indexAction() {
        
    }

    public function registerAction() {

        global $nowDateTime;
        
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


                        $patientData = array(
                            //'panal' => $insertedId,
                            'name' => $params['name'],
                            'registration_no' => strtotime($nowDateTime),
                            'date_of_admission' => $params['doa'],
                            'relative_id' => '',
                            'mobile_no' => $params['mobile_no'],
                            'email_id' => $params['email_id'],
                            'sex' => $params['sex'],
                            'birth_date' => $params['dob'],
                            'specialization_id' => $params['specialization'],
                            'doctor_id' => $params['doctor'],
                            'tokan_no' => '',
                            'referred_by' => $params['referred_by'],
                            'relation' => $params['relation'],
                            'relative_name' => $params['relative_name'],
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
                        $insertedId = $this->_patientResource->addPatient($patientData);
//dd($insertedId);
                        if ($insertedId) {

                            $this->_helper->FlashMessenger()->setNamespace('success')->addMessage('Patient record successfully saved.');
                        } else {
                            $this->_helper->FlashMessenger()->setNamespace('error')->addMessage('Patient record is not inserted.Please try again.');

                            /// $this->view->msg = 'Patient record is not inserted.Please try again.';
                        }
                   
                

                $this->_redirect(WWW_ROOT . 'patients/register');
                // }
            } else {

                $msg = $this->getValidatorErrors($inputData->getMessages(), 'admin');

                $this->_helper->FlashMessenger()->setNamespace('error')->addMessage($msg);

                if ($params['id']) {
                    $this->_redirect(WWW_ROOT . 'patients/addedit/id/' . $params['id']);
                } else {
                    $this->_redirect(WWW_ROOT . 'patients/register');
                }

                //$this->view->msg = $msg;
            }
        }
    }

    protected function validatePatientData($data, $errMsg) {

        if (isset($data['name'])) {
            $this->validators['name'] = array(
                'NotEmpty',
                'messages' => array('Please enter patient name.'));
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

//        if (isset($data['password'])) {
//            $this->validators['password'] = array(
//                'NotEmpty',
//                'messages' => array('Please enter password.'));
//        }

//        if (isset($data['mno'])) {
//            $this->validators['mno'] = array(
//                'NotEmpty',
//                array('StringLength', 10),
//                'messages' => array(
//                    'Please enter mobile no.', 'Mobile no must be 10 digit'));
//        }

        if (isset($data['address'])) {
            $this->validators['address'] = array(
                'NotEmpty',
                'messages' => 'Please enter address.');
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

    public function bookAppointmentAction() {

        $id = $this->getRequest()->getParam('doctorid');

        if (!empty($id)) {
            $doctorDetails = $this->_doctorResource->getDoctorDetailsById($id);
            $this->view->doctorDetails = $doctorDetails;
            dd($doctorDetails);
            // global $status;
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
                    
                    $patientData = array(
                           // 'user_id' => $insertedId,
                            'name' => $params['p_name'],
                            'email' => $params['p_email'],
                            'contact_no' => $params['p_mno'],
                            //'dob' => $nowDateTime
                            //'gender' => $params['gender'],
                            //'address' => $params['p_add']
                        );
                    
                    $insertedId = $this->_patientResource->addPatient($patientData);
//dd($insertedId);
                        if ($insertedId) {
                            
                            $appntData = array(
                                'patient_id' => $insertedId,
                                //'appointment_type' => $params['appointment_type'],
                                'department_id' => $params['department_id'], 
                                'healthcare_provider_id' => $params['doctor_id'],
                                'appoinment_datetime' => $nowDateTime,
                               // 'reason_for_appointment' => $params['note']
                            );
                                //dd($appntData);
                                $insertedId = $this->_appointmentResource->addAppointment($appntData);
                                //$this->_helper->FlashMessenger()->setNamespace('success')->addMessage('Appointment successfully sent.',FlashMessenger::NAMESPACE_SUCCESS, 1);
                                $this->_flashMessenger->addMessage(array('success' => 'Appointment successfully Confirm.'));
	
                                

                           // $this->_helper->FlashMessenger()->setNamespace('success')->addMessage('Patient record successfully saved.');
                        } else {
                            $this->_helper->FlashMessenger()->setNamespace('error')->addMessage('Appointment successfully Confirm.Please try again.');

                            /// $this->view->msg = 'Patient record is not inserted.Please try again.';
                        }
                        
                } else {

                $msg = $this->getValidatorErrors($inputData->getMessages(), 'admin');

                $this->_helper->FlashMessenger()->setNamespace('error')->addMessage($msg);

                //$this->view->msg = $msg;
            }
            }
        }
    }

     public function patientProfileEditAction() {
       
         
        $id = $this->getRequest()->getParam('id');
        if (!empty($id)) {

            $patientData = $this->_patientResource->fetchPatientData($id);
            //dd($patientData,true);
             $patientFormData = array(
                            //'panal' => $insertedId,
                            'id' => $patientData['id'],
                            'name' => $patientData['name'],
                            'registration_no' => $patientData['registration_no'],
                            'date_of_admission' => $patientData['date_of_admission'],
                            
                            'mobile_no' => $patientData['mobile_no'],
                            'email_id' => $patientData['email_id'],
                            'sex' => $patientData['sex'],
                            'birth_date' => $patientData['birth_date'],
                            'specialization_id' => $patientData['specialization_id'],
                            'doctor_id' => $patientData['doctor_id'],
                            
                            'referred_by' => $patientData['referred_by'],
                            'relation' => $patientData['relation'],
                            'relative_name' => $patientData['relative_name'],
                            'address' => $patientData['address'],
                            'city' => $patientData['city'],
                            'pin_code' => $patientData['pin'],
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


                        $patientData = array(

                            'name' => $params['name'],
                            'registration_no' => strtotime($nowDateTime),
                            'date_of_admission' => $params['doa'],
                            'relative_id' => '',
                            'mobile_no' => $params['mobile_no'],
                            'email_id' => $params['email_id'],
                            'sex' => $params['sex'],
                            'birth_date' => $params['dob'],
                            'specialization_id' => $params['specialization'],
                            'doctor_id' => $params['doctor'],
                            'tokan_no' => '',
                            'referred_by' => $params['referred_by'],
                            'relation' => $params['relation'],
                            'relative_name' => $params['relative_name'],
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
                        $insertedId = $this->_patientResource->updateProfileDetail($patientData,$pid);
//dd($insertedId);
                        if ($insertedId) {

                            $this->_helper->FlashMessenger()->setNamespace('success')->addMessage('Patient record successfully saved.');
                        } else {
                            $this->_helper->FlashMessenger()->setNamespace('error')->addMessage('Patient record is not inserted.Please try again.');

                            /// $this->view->msg = 'Patient record is not inserted.Please try again.';
                        }
                   
                

                $this->_redirect(WWW_ROOT . 'patients/register');
                // }
            } else {

                $msg = $this->getValidatorErrors($inputData->getMessages(), 'admin');
dd($msg);
                $this->_helper->FlashMessenger()->setNamespace('error')->addMessage($msg);

                if ($params['id']) {
                    $this->_redirect(WWW_ROOT . 'patients/addedit/id/' . $params['id']);
                } else {
                    $this->_redirect(WWW_ROOT . 'patients/register');
                }

                //$this->view->msg = $msg;
            }
        }
    }

}

?>