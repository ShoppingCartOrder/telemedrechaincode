<?php

class Doctor_InvestigationController extends Mylib_Controller_DoctorbaseController {

    //protected $_dashboardResource;

    public function init() {
        parent::init();
        $this->_doctorResource = new Application_Model_DbTable_Doctors();
        $this->_userResource = new Application_Model_DbTable_Users();
        $this->_specialityResource = new Application_Model_DbTable_Speciality();
        $this->_vitalDetailsResource = new Application_Model_DbTable_PatientVitalDetails();
        $this->_prescribeResource = new Application_Model_DbTable_Prescribe();

        $this->_patientResource = new Application_Model_DbTable_Patients();
        $this->_laboratoryResource = new Application_Model_DbTable_Laboratory();
        $this->_laboratoryRequestResource = new Application_Model_DbTable_LaboratoryRequest();
        $this->_investigationResource = new Application_Model_DbTable_Investigation();
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
        global $statusType;
        //global $appointmentType;
        $request = $this->getRequest();
        global $noRecord;
        $this->view->pid = $pid = $this->getRequest()->getParam('patient_id');
        $this->view->appntId = $appntId = $this->getRequest()->getParam('appnt_id');
        //echo $appntId; die;
        if ($request->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $data = $this->getRequest()->getPost();
            $params = $this->getSearchParams($data);



            $params['fields']['main'] = array('*');
            $params['sidx'] = 'id';
            $params['sord'] = 'DESC';
            //$params['rows'] = $data['rows'];
            //$params['start'] = 0;
            //if ($data['page'] > 1) {
            //  $params['start'] = ($data['page'] - 1) * $params['rows'];
            //}
            //dd($params);
            $params['condition']['patient_id'] = $pid;
            $params['condition']['appnt_id'] = $appntId;
            $params['userRoleType'] = DOCTOR_ROLE;
            $result = $this->_prescriptionResource->allPrescriptions($params);
            //dd($result);
            $responce = new stdClass();
            $responce->page = $result['page'];
            $responce->total = $result['total'];
            $responce->records = $result['records'];


            foreach ($result['result'] as $k => $val) {

                //$statusUpdate = '';
                //$actionUpdate = '';
                $id = $val['id'];
                $pid = $val['patient_id'];
                $appntId = $val['appnt_id'];
                //$apIDs = $val['appointment_type'].'_'.$id.'_'.$pid;
                $selectStatus = '';


                $responce->rows[$k]['id'] = $id;
                $actionUpdate = "<a title = 'Edit Prescription' onclick='return prescriptionEdit($pid,$appntId,$id);' href='javascript:void(0);'><span class='far fa-edit' aria-hidden='true'></span></a>";



                $responce->rows[$k]['cell'] = array(
                    $appntId,
                    $val['name'],
                    //$val['email'],
                    $val['medication_name'] . ' (' . $val['form_of_medicine'] . ' ' . $val['strength'] . ' ' . $val['unit'] . ')',
                    //$val['form_of_medicine'].' '.$val['unit'],
                    $val['quantity'] . ' ' . $val['how_often'] . ' ' . $val['whichtime'],
                    $this->changeDateFormat($val['start_datetime'], DATETIMEFORMAT, FRONT_DATE_FORMAT) . ' TO ' . $this->changeDateFormat($val['end_datetime'], DATETIMEFORMAT, FRONT_DATE_FORMAT),
                    $val['side_effects'],
                    //$this->changeDateFormat($val['appoinment_datetime'], DATETIMEFORMAT,DATE_TIME_FORMAT),                   
                    $this->changeDateFormat($val['created_at'], DATETIMEFORMAT, DATE_TIME_FORMAT),
                    $actionUpdate
                );
            }
            echo $this->jsonEncode($responce);
            exit;
        }
    }

    public function addeditAction() {

        $pid = $this->getRequest()->getParam('patient_id');
        $registrationNo = $this->getRequest()->getParam('rid');
        $appntId = $this->getRequest()->getParam('appntid');
        $prescribeId = $this->getRequest()->getParam('prescribe_id');
        $id = $this->getRequest()->getParam('id');
        //dd($registrationNo); die;
        $this->view->patient_id = $pid;
        $this->view->rid = $registrationNo;
        $this->view->appntId = $appntId;
        $this->view->prescribe_id = $prescribeId;
        $this->view->id = $id;


        //$patientData = $this->_patientResource->getPatientById($pid);
        //  $this->view->patientData = $patientData;
        $vitalRecordsCnt = 0;

        if (!empty($pid) && !empty($registrationNo) && !empty($appntId)) {
            $doctorDetails = $this->_patientResource->fetchPatientDoctorData($pid, $registrationNo,$appntId);
            $this->view->specialization_id = $doctorDetails['specialization_id'];
            $this->view->doctor_id = $doctorDetails['doctor_id'];
            //dd($doctorDetails);
            $vitalData = $this->_vitalDetailsResource->getPatientAllVitalDetails($pid);
            if ($id) {
                $investigationDetails = $this->_investigationResource->getInvestigationById($id);
                $this->view->investigationDetails = $investigationDetails;
            }
            $this->view->vitaldata = $vitalData;
            $vitalRecordsCnt = count($vitalData);
        }
        $this->view->vitalRecordsCnt = $vitalRecordsCnt;
    }

    public function saveinvetigationAction() {

        // global $status;
        $request = $this->getRequest();
        if ($data = $request->getPost()) {

            $params = $this->__inputPostData;

            $pid = $params['patient_id'];
            $registrationNo = $params['registration_no'];
            $appointmentId = $params['appointment_id'];
            $prescribeId = $params['prescribe_id'];
            $id = $params['id'];


            //$response = array('status' => 0, 'msg' => '');
            $this->validateInvetigationData($params, $this->errMsg);
            $inputData = new Zend_Filter_Input($this->filters, $this->validators);
            $inputData->setData($params);

            // print_r($params); die;

            if ($inputData->isValid()) {


                //dd($params);
                if (!empty($pid) && !empty($registrationNo) && !empty($appointmentId)  && (!empty($prescribeId)) && !empty($id)) {

                    $investigationData = array(
                     
                        'final_diagnosis' => $params['final_diagnosis'],
                        'present_history' => $params['present_history'],
                        'past_history' => $params['past_history'],
                        'examination' => $params['examination'],
                        'investigation' => $params['investigation'],
                        'hosp_course' => $params['hosp_course'],
                        'follow_up' => $params['follow_up']
                        
                    );
                    //dd($patientData);
                    $updateP = $this->_investigationResource->updateInvestigationDetail($investigationData, $id);
                    $this->_helper->FlashMessenger()->setNamespace('success')->addMessage('Investigation data data successfully updated.');
                            $this->_redirect(DOCTOR_BASE_URL . 'patient');
                    
                    /* if ($update) {

                            $this->_helper->FlashMessenger()->setNamespace('success')->addMessage('Investigation data data successfully updated.');
                            $this->_redirect(DOCTOR_BASE_URL . 'patient');
                        } else {
                            //$this->view->msg = 'Patient record not updated.';
                            $this->_helper->FlashMessenger()->setNamespace('error')->addMessage('Investigation data not updated.');
                             $this->_redirect(DOCTOR_BASE_URL . 'investigation/addedit/patient_id/'.$params['patient_id'].'/rid/'.$params['registration_no'].'/id/'.$params['id']);
                        }*/
                } else if (!empty($pid) && !empty($registrationNo) && (!empty($prescribeId))) {


                    $investigationData = array(
                        'patient_id' => $params['patient_id'],
                        'registration_no' => $params['registration_no'],
                        'appointment_id' => $params['appointment_id'],
                        'prescribe_id' => $params['prescribe_id'],
                        'final_diagnosis' => $params['final_diagnosis'],
                        'present_history' => $params['present_history'],
                        'past_history' => $params['past_history'],
                        'examination' => $params['examination'],
                        'investigation' => $params['investigation'],
                        'hosp_course' => $params['hosp_course'],
                        'follow_up' => $params['follow_up'],
                        'investigated_by' => $this->doctorId
                    );
                    //dd($patientData);
                    $insertedId = $this->_investigationResource->addInvestigation($investigationData);

                    if ($insertedId) {

                            $this->_helper->FlashMessenger()->setNamespace('success')->addMessage('Investigation record successfully saved.');
                            //$this->_redirect(HOSPITAL_BASE_URL . 'patient/index');
                            $this->_redirect(DOCTOR_BASE_URL . 'patient');
                            
                        } else {
                            $this->_helper->FlashMessenger()->setNamespace('error')->addMessage('Investigation record is not inserted.Please try again.');

                             $this->_redirect(DOCTOR_BASE_URL . 'investigation/addedit/patient_id/'.$params['patient_id'].'/rid/'.$params['registration_no'].'/prescribe_id/'.$params['prescribe_id']);
                        }

                   
                }
            } else {

                $msg = $this->getValidatorErrors($inputData->getMessages(), 'admin');
                $this->_helper->FlashMessenger()->setNamespace('error')->addMessage($msg);

                if ($params['id']) {
                     $this->_redirect(DOCTOR_BASE_URL . 'investigation/addedit/patient_id/'.$params['patient_id'].'/rid/'.$params['registration_no'].'/prescribe_id/'.$params['prescribe_id'].'/id/'.$params['id']);
                } else {
                    $this->_redirect(DOCTOR_BASE_URL . 'investigation/addedit/patient_id/'.$params['patient_id'].'/rid/'.$params['registration_no'].'/prescribe_id/'.$params['prescribe_id']);
                }
            }
        }

    
    }

    protected function validateInvetigationData($data, $errMsg) {

        if (isset($data['final_diagnosis'])) {
            $this->validators['final_diagnosis'] = array(
                'NotEmpty',
                'messages' => array('Please enter final diagnosis.'));
        }
    }

}

?>