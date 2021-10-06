<?php

class Hospital_DoctorPrescribeController extends Mylib_Controller_HospitalbaseController {

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
    }

    public function indexAction() {
        
    }

    public function viewprofileAction() {
        
    }

    public function addeditAction() {

        $pid = $this->getRequest()->getParam('patient_id');
        $registrationNo = $this->getRequest()->getParam('rid');
        $id = $this->getRequest()->getParam('id');
        //dd($this->hospitaluserId); die;
        $this->view->patient_id = $pid;
        $this->view->rid = $registrationNo;
        $this->view->id = $id;


        //$patientData = $this->_patientResource->getPatientById($pid);
        //  $this->view->patientData = $patientData;
        $vitalRecordsCnt = 0;

        if (!empty($pid) && !empty($registrationNo)) {
            $doctorDetails = $this->_patientResource->fetchPatientDoctorData($pid, $registrationNo);
            $this->view->specialization_id = $doctorDetails['specialization_id'];
            $this->view->doctor_id = $doctorDetails['doctor_id'];
            //dd($doctorDetails);
            $vitalData = $this->_vitalDetailsResource->getPatientAllVitalDetails($pid);
            if ($id) {
                $prescriptionDetails = $this->_prescribeResource->getPrescriptionById($id);
                $this->view->prescriptionDetails = $prescriptionDetails;
            }
            $this->view->vitaldata = $vitalData;
            $vitalRecordsCnt = count($vitalData);
        }
        $this->view->vitalRecordsCnt = $vitalRecordsCnt;
    }

    public function savedoctorprescribeAction() {

        // global $status;
        $request = $this->getRequest();
        if ($data = $request->getPost()) {

            $params = $this->__inputPostData;

            $pid = $params['patient_id'];
            $registrationNo = $params['registration_no'];
            $id = $params['id'];


            //$response = array('status' => 0, 'msg' => '');
            $this->validatePrescribeData($params, $this->errMsg);
            $inputData = new Zend_Filter_Input($this->filters, $this->validators);
            $inputData->setData($params);

            //print_r($params); die;

            if ($inputData->isValid()) {


                //dd($params);
                if (!empty($pid) && !empty($registrationNo) && !empty($id)) {


                    $rowNos = $params['rowno'];

                    if ($rowNos) {

                        $arrRowNos = explode(',', $rowNos);

                        foreach ($arrRowNos as $rowNo) {

                            $vitalData = array(
                                'patient_id' => $params['patient_id'],
                                'registration_no' => $params['registration_no'],
                                'blood_pressure' => $params['blood-pressure_' . $rowNo],
                                'sugar' => $params['sugar_' . $rowNo],
                                'heart_beat' => $params['heart-beat_' . $rowNo],
                                'bmi' => $params['bmi_' . $rowNo],
                                'temperature' => $params['temprature_' . $rowNo],
                                'pulse' => $params['pulse_' . $rowNo],
                                'spo2' => $params['spo2_' . $rowNo],
                                'created_by' => $this->hospitaluserId,
                            );

                            $insertedId = $this->_vitalDetailsResource->addpatientVitalDetails($vitalData);
                        }
                    }

                    $prescribeData = array(
                        'patient_id' => $params['patient_id'],
                        'registration_no' => $params['registration_no'],
                        'consultation_notes' => $params['consultation_notes'],
                        'dcotor_prescibe_notes' => $params['dcotor_notes'],
                        'laboratery_test_notes' => $params['laboratery_test_notes'],
                        'medicine_advices' => $params['medicine_advices'],
                        'dressing_note' => $params['dressing_note'],
                        'xray' => $params['xray'],
                        'remark' => $params['remark'],
                        'prescribed_by' => $params['doctor_id'],
                        'specialization_id' => $params['specialization_id'],
                        'created_by' => $this->hospitaluserId,
                    );
                    //dd($patientData);
                    $updateP = $this->_prescribeResource->updatePrescriptionDetail($prescribeData, $id);
                    $deleteCond = array('patient_id = ?' => $params['patient_id'],'registration_no = ?' => $params['registration_no']);
                    $laborateryTestNotesIds = $this->_laboratoryRequestResource->deleteLaboratoryTestRequest($deleteCond);

                    if (!empty($params['laboratery_test_notes'])) {

                        $laborateryTestNotesName = "'" . $params['laboratery_test_notes'] . "'";

                        $laborateryTestNotesName = str_replace(", ", "','", $laborateryTestNotesName);

                        $laborateryTestNotesIds = $this->_laboratoryResource->fetchLaboratoryTestByNames($laborateryTestNotesName);

                        if (!empty($laborateryTestNotesIds)) {
                            foreach ($laborateryTestNotesIds as $key => $laborateryTestNotesVal) {
                                $laborateryTestRequest = array(
                                    'prescribe_id' => $id,
                                    'patient_id' => $params['patient_id'],
                                    'registration_no' => $params['registration_no'],
                                    'lab_test_id' => $laborateryTestNotesVal['id'],
                                    'lab_test_name' => $laborateryTestNotesVal['laboratory_test_name'],
                                    'doctor_id' => $params['doctor_id'],
                                    'specialization_id' => $params['specialization_id'],
                                    'created_by' => $this->hospitaluserId,
                                );
                                
                                $laborateryTestNotesIds = $this->_laboratoryRequestResource->addLaboratoryTestRequest($laborateryTestRequest);

                            }
                        }
                    }
                    
                    echo '2';
                    die;
                    //$this->_redirect(HOSPITAL_BASE_URL . 'appointment/video-consultation-addedit/patient_id/'.$pid.'/appnt_id/'.$appntId);
                } else if (!empty($pid) && !empty($registrationNo)) {

                    $rowNos = $params['rowno'];

                    if ($rowNos) {

                        $arrRowNos = explode(',', $rowNos);

                        foreach ($arrRowNos as $rowNo) {

                            $vitalData = array(
                                'patient_id' => $params['patient_id'],
                                'registration_no' => $params['registration_no'],
                                'blood_pressure' => $params['blood-pressure_' . $rowNo],
                                'sugar' => $params['sugar_' . $rowNo],
                                'heart_beat' => $params['heart-beat_' . $rowNo],
                                'bmi' => $params['bmi_' . $rowNo],
                                'temperature' => $params['temprature_' . $rowNo],
                                'pulse' => $params['pulse_' . $rowNo],
                                'spo2' => $params['spo2_' . $rowNo],
                                'created_by' => $this->hospitaluserId,
                            );

                            $insertedId = $this->_vitalDetailsResource->addpatientVitalDetails($vitalData);
                        }
                    }


                    $prescribeData = array(
                        'patient_id' => $params['patient_id'],
                        'registration_no' => $params['registration_no'],
                        'consultation_notes' => $params['consultation_notes'],
                        'dcotor_prescibe_notes' => $params['dcotor_notes'],
                        'laboratery_test_notes' => $params['laboratery_test_notes'],
                        'medicine_advices' => $params['medicine_advices'],
                        'dressing_note' => $params['dressing_note'],
                        'xray' => $params['xray'],
                        'remark' => $params['remark'],
                        'prescribed_by' => $params['doctor_id'],
                        'specialization_id' => $params['specialization_id'],
                        'created_by' => $this->hospitaluserId,
                    );
                    //dd($patientData);
                    $insertedId = $this->_prescribeResource->addPrescribe($prescribeData);


                    if (!empty($params['laboratery_test_notes'])) {

                        $laborateryTestNotesName = "'" . $params['laboratery_test_notes'] . "'";

                        $laborateryTestNotesName = str_replace(", ", "','", $laborateryTestNotesName);

                        $laborateryTestNotesIds = $this->_laboratoryResource->fetchLaboratoryTestByNames($laborateryTestNotesName);

                        if (!empty($laborateryTestNotesIds)) {
                            foreach ($laborateryTestNotesIds as $key => $laborateryTestNotesVal) {
                                $laborateryTestRequest = array(
                                    'prescribe_id' => $insertedId,
                                    'patient_id' => $params['patient_id'],
                                    'registration_no' => $params['registration_no'],
                                    'lab_test_id' => $laborateryTestNotesVal['id'],
                                    'lab_test_name' => $laborateryTestNotesVal['laboratory_test_name'],
                                    'doctor_id' => $params['doctor_id'],
                                    'specialization_id' => $params['specialization_id'],
                                    'created_by' => $this->hospitaluserId,
                                );
                                
                                $laborateryTestNotesIds = $this->_laboratoryRequestResource->addLaboratoryTestRequest($laborateryTestRequest);

                            }
                        }
                    }

                    echo '1';
                    die;
                }
            } else {

                $msg = $this->getValidatorErrors($inputData->getMessages(), 'admin');
                echo $msg;
                die;
            }
        }

        die;
    }

    protected function validatePrescribeData($data, $errMsg) {

        if (isset($data['medicine_advices'])) {
            $this->validators['medicine_advices'] = array(
                'NotEmpty',
                'messages' => array('Please enter medicine name.'));
        }
    }

}

?>