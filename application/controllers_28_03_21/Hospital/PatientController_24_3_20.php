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

        $this->_helper->layout->setLayout('hospital');
        global $gender;
        $this->view->gender = $gender;
    }
    
    public function indexAction() {
         global $nowDateTime;

        global $healthCondition;
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
                //$pid = $val['patient_id'];


                $responce->rows[$k]['id'] = $id;
                $actionUpdate = '';
                //$responce->rows[$k]['appointment_type'] = $val['appointment_type'];
                $actionUpdate = "<a onclick='return patientProfileEdit($id);' href='javascript:void(0);'><span class='far fa-edit' aria-hidden='true'></span></a>";
                $chargeUpdate = "<a onclick='return addPayment($id,$rId);' href='javascript:void(0);'>Add Charges</a>";
//                

                $responce->rows[$k]['cell'] = array(
                    $val['registration_no'],
                    $val['name'],
                    $val['mobile_no'],
       
                    $val['sex'],
                    //$val['email'],
                    $this->changeDateFormat($val['date_of_admission'], DATETIMEFORMAT, DATE_TIME_FORMAT),
                    $chargeUpdate,
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
                   
                

                $this->_redirect(HOSPITAL_BASE_URL . 'patient/register');
                // }
            } else {

                $msg = $this->getValidatorErrors($inputData->getMessages(), 'admin');

                $this->_helper->FlashMessenger()->setNamespace('error')->addMessage($msg);

                if ($params['id']) {
                    $this->_redirect(HOSPITAL_BASE_URL . 'patients/addedit/id/' . $params['id']);
                } else {
                    $this->_redirect(HOSPITAL_BASE_URL . 'patients/register');
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

   

     public function patientProfileEditAction() {
       
         
        $id = $this->getRequest()->getParam('id');
        if (!empty($id)) {
            $this->view->spcData = $this->_specialityResource->fetchAllSpeciality();
            $itemData = $this->_chargeItemResource->fetchAllItemCharge($id);
            dd($itemData);
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
                   
                

                $this->_redirect(HOSPITAL_BASE_URL . 'patients/register');
                // }
            } else {

                $msg = $this->getValidatorErrors($inputData->getMessages(), 'admin');
//dd($msg);
                $this->_helper->FlashMessenger()->setNamespace('error')->addMessage($msg);

                if ($params['id']) {
                    $this->_redirect(HOSPITAL_BASE_URL . 'patients/addedit/id/' . $params['id']);
                } else {
                    $this->_redirect(HOSPITAL_BASE_URL . 'patients/register');
                }

                //$this->view->msg = $msg;
            }
        }
    }
    
    public function chargesAction() {
       
         
        $id = $this->getRequest()->getParam('id');
        $this->view->regisNo = $regisNo = $this->getRequest()->getParam('rid');
        $items = array(''=>'Select Item');
        $this->view->index = 1;
        $this->view->patientId = $id;
        $this->view->items = $items;
        if (!empty($id)) {
            $this->view->spcData = $this->_specialityResource->fetchAllSpeciality();
            $catData = $this->_chargeItemResource->fetchAllChargeCategory();
            $chargeCat = array(''=>'Please Select');
            foreach($catData as $catVal){
              $chargeCat[$catVal['id']] =  $catVal['category_name']; 
            }
            
            $this->view->catData = $chargeCat;
            
            $this->view->patientCharge = $patientCharge =  $this->_chargeItemResource->fetchPatientCharges($id,$regisNo);
            
            if(!empty($patientCharge)){
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
         //dd($itemsData);
         //echo json_encode($itemsData);
         echo $itemsData['charge'];
         exit;
         //$id = $this->getRequest()->getParam('id');
     }
     
     public function addpatientchargesAction() {
         
         $patientId = $this->getRequest()->getParam('patientId');
         $rId = $this->getRequest()->getParam('rid');
            
            if($patientId && $rId){
                
               $this->view->patientId = $patientId; 
               $this->view->rId = $rId; 
                
            }
            $request = $this->getRequest();
            $params = $this->__inputPostData;
            
            if ($request->isPost()) {
                                                
                $data = $request->getPost(); 
                //dd($data);
                
                if(isset($data['patientId'])){
                        $patientId = $data['patientId'];
                }
                if(isset($data['rId'])){
                        $rId = $data['rId'];
                }
                 
                 if((!$patientId) && (!$rId)){
                
               $this->_redirect(HOSPITAL_BASE_URL . 'patient/index');
                
            }
            
                $this->view->patientId = $patientId; 
                $this->view->rId = $rId; 

                if(!empty($data['charge_category_id'])){
                    
                    $str_data = '';                            
                    $cnt  = count($data['charge_category_id']);
                    $first_key = key($data['charge_category_id']);
                    $total_count = $first_key+$cnt;
                    $cnt_update =0;
                    $cnt_insert =0;

                    for($i = $first_key;$i<$total_count;$i++){  
                        if(isset($data['id'][$i])){
                            $arr_data_update[] = $data['id'][$i];
                            $arr_data_update[] = $patientId;
                            
                            $arr_data_update[] = $rId;
                            $arr_data_update[] = $patientId.$rId;

                            if(!empty($data['charge_category_id'][$i])){
                                    $arr_data_update[] = $data['charge_category_id'][$i];  
                            }else{
                                $this->_helper->FlashMessenger()->setNamespace('error')->addMessage("Field can not be empty."); 
                                $this->_redirect(HOSPITAL_BASE_URL . 'patient/charges/id/' . $params['patientId'].'/rid/'.$rId);

                            }

                            if(!empty($data['charge_item_id'][$i])){
                                    $arr_data_update[] = $data['charge_item_id'][$i];  
                            }else{
                                $this->_helper->FlashMessenger()->setNamespace('error')->addMessage("Field can not be empty."); 
                                $this->_redirect(HOSPITAL_BASE_URL . 'patient/charges/id/' . $params['patientId'].'/rid/'.$rId);

                            }



                            if(!empty($data['quantity'][$i])){
                                    $arr_data_update[] = $data['quantity'][$i];  
                            }else{
                                $this->_helper->FlashMessenger()->setNamespace('error')->addMessage("Field can not be empty."); 
                                $this->_redirect(HOSPITAL_BASE_URL . 'patient/charges/id/' . $params['patientId'].'/rid/'.$rId);

                            }

                            if(!empty($data['amount'][$i])){
                                    $arr_data_update[] = $data['amount'][$i];  
                            }else{
                                $this->_helper->FlashMessenger()->setNamespace('error')->addMessage("Field can not be empty."); 
                                $this->_redirect(HOSPITAL_BASE_URL . 'patient/charges/id/' . $params['patientId'].'/rid/'.$rId);

                            }


                            if(!empty($data['discount'][$i])){
                                    $arr_data_update[] = $data['discount'][$i];  
                            }else{
                                $arr_data_update[] = 0;  
                            }

                            if(!empty($data['net_amount'][$i])){
                                    $arr_data_update[] = $data['net_amount'][$i];  
                            }else{
                                $this->_helper->FlashMessenger()->setNamespace('error')->addMessage("Field can not be empty."); 
                                $this->_redirect(HOSPITAL_BASE_URL . 'patient/charges/id/' . $params['patientId'].'/rid/'.$rId);

                            }
                            $cnt_update++;
                            
                        }else{
                            
                        $cnt_insert++;
                        $arr_data[] = $patientId;
                        $arr_data[] = $rId;
                        $arr_data[] = $patientId.$rId;

                        if(!empty($data['charge_category_id'][$i])){
                                $arr_data[] = $data['charge_category_id'][$i];  
                        }else{
                            $this->_helper->FlashMessenger()->setNamespace('error')->addMessage("Field can not be empty."); 
                            $this->_redirect(HOSPITAL_BASE_URL . 'patient/charges/id/' . $params['patientId'].'/rid/'.$rId);

                        }

                        if(!empty($data['charge_item_id'][$i])){
                                $arr_data[] = $data['charge_item_id'][$i];  
                        }else{
                            $this->_helper->FlashMessenger()->setNamespace('error')->addMessage("Field can not be empty."); 
                            $this->_redirect(HOSPITAL_BASE_URL . 'patient/charges/id/' . $params['patientId'].'/rid/'.$rId);

                        }



                        if(!empty($data['quantity'][$i])){
                                $arr_data[] = $data['quantity'][$i];  
                        }else{
                            $this->_helper->FlashMessenger()->setNamespace('error')->addMessage("Field can not be empty."); 
                            $this->_redirect(HOSPITAL_BASE_URL . 'patient/charges/id/' . $params['patientId'].'/rid/'.$rId);

                        }

                        if(!empty($data['amount'][$i])){
                                $arr_data[] = $data['amount'][$i];  
                        }else{
                            $this->_helper->FlashMessenger()->setNamespace('error')->addMessage("Field can not be empty."); 
                            $this->_redirect(HOSPITAL_BASE_URL . 'patient/charges/id/' . $params['patientId'].'/rid/'.$rId);

                        }


                        if(!empty($data['discount'][$i])){
                                $arr_data[] = $data['discount'][$i];  
                        }else{
                            $arr_data[] = 0;  
                        }

                        if(!empty($data['net_amount'][$i])){
                                $arr_data[] = $data['net_amount'][$i];  
                        }else{
                            $this->_helper->FlashMessenger()->setNamespace('error')->addMessage("Field can not be empty."); 
                            $this->_redirect(HOSPITAL_BASE_URL . 'patient/charges/id/' . $params['patientId'].'/rid/'.$rId);

                        }
                                    
                    }     

                    }
                    if(!empty($arr_data) || !empty($arr_data_update)||!empty($cat_data)){
                           
                            
                            if(!empty($arr_data)){
                                $this->_chargeItemResource->insertChargesDetails($arr_data,$cnt_insert);
                                $this->_helper->FlashMessenger()->setNamespace('success')->addMessage("Charges added successfully."); 
                            
                            }
                            
                            if(!empty($arr_data_update)){
                                //dd($arr_data_update);
                                $this->_chargeItemResource->updateChargeDetails($arr_data_update,$cnt_update);
                                $this->_helper->FlashMessenger()->setNamespace('success')->addMessage("Charges updated successfully."); 
                            
                                
                            }
                            
                            $this->_redirect(HOSPITAL_BASE_URL . 'patient/index');

                            
                        }
                    
                    
                                 
                }else{
                    //dd(222);
                    $this->_helper->FlashMessenger()->setNamespace('error')->addMessage("Please add charges."); 
                    $this->_redirect(HOSPITAL_BASE_URL . 'patient/charges/id/' . $params['patientId'].'/rid/'.$rId);

                }
                
                
            }
         
     }
     
     //public function deleteitemchargeAction() {
      public function deleteitemchargeAction(){
                
                
                if($data = $this->getRequest()->getPost()){
                    
                    $arr_charge_id[] = $data['charge_id'];
                    //print_r($arr_dlt_extr_fld);die;
                    if($this->_chargeItemResource->deleteChargedDetails($arr_charge_id)){
                        echo "1";
                        exit;
                    }else{
                        echo "0";
                        exit;
                    }
                }
                
            }
     
     

}

?>