<?php

class Patient_FeedbackController extends Mylib_Controller_PatientbaseController
{

    protected $_userType;

    public function init()
    {

        parent::init();
        $this->_feedbackResource = new Application_Model_DbTable_Feedback();
        $this->_departmentResource = new Application_Model_DbTable_Departments();
        
       
    }

    public function indexAction()
    {
       
        


        // global $status;
        $request = $this->getRequest();
        
        $this->view->dertData = $this->_departmentResource->fetchAllDepartments();
        
        if ($data = $request->getPost()) {
            //dd($data);
            $params = $this->__inputPostData;
            //print_r($params); die;
            //$response = array('status' => 0, 'msg' => '');
            $this->validateFeedbackData($params, $this->errMsg);
            $inputData = new Zend_Filter_Input($this->filters, $this->validators);
            $inputData->setData($params);
            if ($inputData->isValid()) {


              
//SSdd($params);
                    $feedbackData = array(
                        'patient_id' => $this->patientNamespace->patient_id,
                        'department_id' => $params['department'],
                        'doctor_id' => $params['doctor'],
                        'rating' => $params['rating'],
                        'feedback' => $params['feedback']
                        
                    );
                    //dd($feedbackData);
                    $insertedId = $this->_feedbackResource->addFeedback($feedbackData);
//dd($insertedId);
                    if ($insertedId) {

                        $this->_helper->FlashMessenger()->setNamespace('success')->addMessage('Feedback successfully saved.');

                        //$this->_redirect(PATIENT_BASE_URL . 'health/health-profile');
                    } else {
                        $this->_helper->FlashMessenger()->setNamespace('error')->addMessage('Feedback is not inserted.Please try again.');
                        //$this->_redirect(PATIENT_BASE_URL . 'health/health-profile-add-edit');
                        /// $this->view->msg = 'Patient record is not inserted.Please try again.';
                    }
               
            } else {

                $msg = $this->getValidatorErrors($inputData->getMessages(), 'admin');

                $this->_helper->FlashMessenger()->setNamespace('error')->addMessage($msg);


                //$this->view->msg = $msg;
            }
        }

//        die;
  
    }
    
     protected function validateFeedbackData($data, $errMsg) {

        if (isset($data['department'])) {
            $this->validators['department'] = array(
                'NotEmpty',
                'messages' => array('Please select department.'));
        }
        if (isset($data['doctor'])) {
            $this->validators['doctor'] = array(
                'NotEmpty',
                'messages' => array('Please select doctor name.'));
        }
        if (isset($data['feedback'])) {
            $this->validators['feedback'] = array(
                'NotEmpty',
                'messages' => array('Please enter feedback.'));
        }


        if (isset($data['rating'])) {
            $this->validators['rating'] = array(
                'NotEmpty',
                'messages' => 'Please enter rating.');
        }
    }

     
    
       
    
}
