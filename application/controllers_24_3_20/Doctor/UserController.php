<?php

class Patient_UserController extends Mylib_Controller_PatientbaseController {

    protected $_patientResource;
    protected $_userResource;

    public function init() {
        parent::init();
        $this->_patientResource = new Application_Model_DbTable_Patients();
        $this->_userResource = new Application_Model_DbTable_Users();
        //$this->session = new Zend_Session_Namespace('mypatient');
    }

    public function indexAction() {
       $loginId = $this->patientNamespace->loginId;
       //echo $loginId; die;//
        //$id = $this->getRequest()->getParam('id');
        if (!empty($loginId)) {

            $patientData = $this->_patientResource->fetchPatientProfileData($loginId);
            //dd($patientData);
            $formdata = array(
                'id' => $patientData['id'],
                'name' => $patientData['name'],
                'email' => $patientData['email'],
                'contact_no' => $patientData['contact_no'],
                'address' => $patientData['address']
            );
//dd($formdata);
            $this->view->formdata = $formdata;
        }
       // die;
    }
     public function updateprofileAction() {
        $this->_helper->layout->disableLayout('planner/innerlayout');
        $request = $this->getRequest();
        $data['name'] = $request->getParam('name');
        $data['email'] = $request->getParam('email');
        $data['city'] = $request->getParam('city');
        //$data['location'] = $request->getParam('profile_location');
        $data['phone'] = $request->getParam('phone');
        //$data['date'] = $request->getParam('wedding_date');
        $data['youare'] = $request->getParam('youare');
        $data['fiancename'] = $request->getParam('fiancename');
        $data['fianceemail'] = $request->getParam('fianceemail');
        $data['userid'] = $request->getParam('userid');
      
        $updateUserProfile = $this->_profileResource->updateUserProfile($data);
       
        echo 1;
        exit;
    }
    public function savepatientAction() {

        // global $status;
        $request = $this->getRequest();
        if ($data = $request->getPost()) {
            //dd($data);
            $params = $this->__inputPostData;
            $this->validatePatientData($params, $this->errMsg);
            $inputData = new Zend_Filter_Input($this->filters, $this->validators);
            $inputData->setData($params);
            if ($inputData->isValid()) {

               // if (!empty($params['id'])) {
                    $params['id'] = $this->patientNamespace->loginId;
                    //dd($params);
                    $update = $this->_patientResource->updateProfileData($params);

                    if ($update) {
                        $this->_helper->FlashMessenger()->setNamespace('success')->addMessage('Patient record successfully updated.');
                    } else {

                        $this->_helper->FlashMessenger()->setNamespace('error')->addMessage('Patient record not updated.');
                    }
                    $this->_redirect(PATIENT_BASE_URL . 'user/index');
                //} else { //Save new Patient
                   
            } else {

                $msg = $this->getValidatorErrors($inputData->getMessages(), 'admin');

                $this->_helper->FlashMessenger()->setNamespace('error')->addMessage($msg);

                $this->_redirect(PATIENT_BASE_URL . 'patient/index');
            }
        }

        die;
    }

    protected function validatePatientData($data, $errMsg) {

        if (isset($data['p_name'])) {
            $this->validators['p_name'] = array(
                'NotEmpty',
                'messages' => array('Please enter patient name.'));
        }



        if (isset($data['p_email'])) {
            $this->validators['email'] = array(
                'NotEmpty',
                array('StringLength', 5),
                array('EmailAddress'),
                'messages' => array(
                    'Please enter email id.', 'Email must be atleast 5 characters', 'Plz enter valid email id'
            ));
        }

        if (isset($data['p_mno'])) {
            $this->validators['p_mno'] = array(
                'NotEmpty',
                array('StringLength', 10),
                'messages' => array(
                    'Please enter mobile no.', 'Mobile no must be 10 digit'));
        }

        if (isset($data['p_add'])) {
            $this->validators['p_add'] = array(
                'NotEmpty',
                'messages' => 'Please enter address.');
        }
    }

    public function deleteAction() {
        $id = $this->getRequest()->getPost('id');
        $userdetail = $this->_userResource->fetchUserData($id);
        $photo = $userdetail->photo;

        if (isset($id)) {
            $newname = UPLOAD_FILE_PATH . '/images/userimages/main/' . $photo;
            $newnamethumb = UPLOAD_FILE_PATH . '/images/userimages/thumb/' . $photo;
            unlink($newname);
            unlink($newnamethumb);
            if ($this->_userResource->deleteUserData($id)) {
                $this->_redirect(ADMIN_BASE_URL . 'user/index');
            } else {
                $this->_redirect(ADMIN_BASE_URL . 'user/index');
            }
        }
    }

    public function welcomeMsgAction() {


        $request = $this->getRequest();

        $id = $request->getParam('id');

        if (!empty($id)) {
            $userData = $this->_userResource->welcomeMailSentUserData($id);
            $name = $userData['name'];



            $formdata = array(
                'id' => $userData['id'],
                'name' => $name,
                'email' => $userData['email'],
                'mobile' => $userData['mobile'],
            );
            $this->view->formdata = $formdata;
        }
    }

    public function sendWelcomeMsgAction() {

        $request = $this->getRequest();
        $this->initMsg('USER_WELCOME_MESSAGE');
        if ($request->isPost()) {
            $params = $this->__inputPostData;
            $response = array('status' => 0, 'msg' => '');
            $this->validateSendWelcomePostData($params, $this->errMsg);

            $inputData = new Zend_Filter_Input($this->filters, $this->validators);
            $inputData->setData($params);

            if ($inputData->isValid()) {

                $params['created_by'] = $this->session->loginId;
                $insert = $this->_userResource->insrtsendUserDetails($params);

                /*                 * ****************Code to send SMS to welcome user************ */
                $content1 = sprintf($this->frmMsg['ADMIN_WELCOME_USER_SMS']);
                $search1 = array("{NAME}", "{CONTACT_NO}", "{SITEURL}", "{NEWLINE}");
                $replace1 = array($params['name'], PRIMARY_VENDOR_NO, WEBSITE_NAME, "\n");
                $content1 = urlencode(str_replace($search1, $replace1, $content1));
                $mno = trim($params['mobile']);
                //$mno = '8800353404';           
                $this->sendMessage($mno, $content1, API_TIME);

                /**                 * ***************End of code to send SMS to welcome user************ */
                $subject = 'Welcome to Vivahaayojan !';
                $content = 'Welcome';
                $sendto = $params['email'];
                $templateid = 3905;
                $content = $this->sendMail($sendto, $subject, $content, 'true', $templateid);


                if ($insert) {
                    $this->_helper->FlashMessenger()->setNamespace('success')->addMessage('Message is successfully sent.');
                    $this->_redirect(ADMIN_BASE_URL . 'user/welcome-msg-list');
                } else {

                    $this->_helper->FlashMessenger()->setNamespace('error')->addMessage('Message is not sent.');
                }
            } else {
                $msg = $this->getValidatorErrors($inputData->getMessages());
                $this->_helper->FlashMessenger()->setNamespace('error')->addMessage($msg);
                $this->_redirect(ADMIN_BASE_URL . 'user/welcome-msg');
            }
        }
    }

    public function validateSendWelcomePostData($data, $errMsg) {

        if (isset($data['name'])) {
            $this->validators['name'] = array('NotEmpty', 'messages' => array('Name can not be left empty.'));
        }

        if (isset($data['email'])) {
            $this->validators['email'] = array('NotEmpty',
                array('StringLength', 5),
                array('EmailAddress'),
                'messages' => array(
                    'Email can not be left empty.',
                    'Email must be atleast 5 characters',
                    'Please enter valid email id.'
            ));
        }


        if (isset($data['mobile'])) {

            $this->validators['mobile'] = array('NotEmpty', array('StringLength', array('max' => 10, 'min' => 10)), 'messages' => array('Mobile can not be left empty.',
                    'Invalid mobile no.'));
        }
    }

    public function welcomeMsgListAction() {

        $request = $this->getRequest();

        if ($request->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $data = $this->getRequest()->getPost();

            $params = $this->getSearchParams($data);
            $params['fields']['main'] = array('id', 'name', 'email', 'mobile', 'created_at');
            $params['sidx'] = 'id';
            $params['sord'] = 'DESC';
            $result = $this->_userResource->fetchAllWelcomeMessages($params);
            $responce = new stdClass();
            $responce->page = $result['page'];
            $responce->total = $result['total'];
            $responce->records = $result['records'];

            foreach ($result['result'] as $k => $val) {

                $responce->rows[$k]['id'] = $val['id'];
                $responce->rows[$k]['cell'] = array(
                    $val['name'],
                    $val['email'],
                    $val['mobile'],
                    $val['created_at']
                );
            }

            echo $this->jsonEncode($responce);
            exit;
        }
    }

    public function welcomeUserDeleteAction() {
        $id = $this->getRequest()->getPost('id');


        if (isset($id)) {
            if ($this->_userResource->deleteWelcomeUserDetailsData($id)) {
                $this->_redirect(ADMIN_BASE_URL . 'user/welcome-msg-list');
            } else {
                $this->_redirect(ADMIN_BASE_URL . 'user/welcome-msg-list');
            }
        }
    }

    public function msgEmailAction() {

        global $userType;

        $this->view->userType = $userType;
        $request = $this->getRequest();
    }

    public function sendEmailMsgAction() {

        $request = $this->getRequest();
        $this->initMsg('ADMIN_SEND_MESSAGE');
        if ($request->isPost()) {
            $params = $this->__inputPostData;
            $data['message'] = $request->getPost('message');


            $response = array('status' => 0, 'msg' => '');
            $this->validateSendUserEmailMsgPostData($params, $this->errMsg);

            $inputData = new Zend_Filter_Input($this->filters, $this->validators);
            $inputData->setData($params);

            if ($inputData->isValid()) {
                $params['message'] = $data['message'];
                $params['created_by'] = $this->session->loginId;
                $insert = $this->_userResource->insrtsendUserMsgDetails($params);



                $subject = $params['email_subject'];

                $contentAdminEmail = sprintf($this->frmMsg['ADMIN_SEND_EMAIL_TEMPLATE']);

                $searchAdminEmail = array("{WP_LOGO_IMAGE_LINK}", "{HEADING}", "{MESSAGE}", "{SITEURL}", "{CURRENT_YEAR}", "{WEBSITE_NAME}");
                $replaceAdminEmail = array(WP_LOGO_IMAGE_LINK, 'Email', $data['message'], HOSTPATH, CURRENT_YEAR, WEBSITE_NAME);

                $contentAdminEmail = str_replace($searchAdminEmail, $replaceAdminEmail, $contentAdminEmail);



                $sendto = $params['email'];

                $content = $this->sendLargeMail($sendto, $subject, $contentAdminEmail, 'true', '', ADVERTISE_US);

                if ($insert) {
                    $this->_helper->FlashMessenger()->setNamespace('success')->addMessage('Message is successfully sent.');
                    $this->_redirect(ADMIN_BASE_URL . 'user/all-sent-email-mobile-list/message-section/1');
                } else {

                    $this->_helper->FlashMessenger()->setNamespace('error')->addMessage('Message is not sent.');
                }
            } else {
                $msg = $this->getValidatorErrors($inputData->getMessages());
                $this->_helper->FlashMessenger()->setNamespace('error')->addMessage($msg);
                $this->_redirect(ADMIN_BASE_URL . 'user/msg-email');
            }
        }
    }

    public function validateSendUserEmailMsgPostData($data, $errMsg) {

        if (isset($data['name'])) {
            $this->validators['name'] = array('NotEmpty', 'messages' => array('Name can not be left empty.'));
        }

        if (isset($data['email'])) {
            $this->validators['email'] = array('NotEmpty',
                array('StringLength', 5),
                array('EmailAddress'),
                'messages' => array(
                    'Email can not be left empty.',
                    'Email must be atleast 5 characters',
                    'Please enter valid email id.'
            ));
        }

        if (isset($data['email_subject'])) {
            $this->validators['email_subject'] = array('NotEmpty', 'message' => array('Email subject can not be left empty.'));
        }

        if (isset($data['message'])) {
            $this->validators['message'] = array('NotEmpty', 'message' => array('Message can not be left empty.'));
        }
    }

    public function allSentEmailMobileListAction() {
        global $userType;

        $request = $this->getRequest();
        $this->view->messageSection = $messageSection = $request->getParam('message-section');
        if ($request->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $data = $this->getRequest()->getPost();

            $params = $this->getSearchParams($data);
            $params['fields']['main'] = array('mum.id', 'mum.name', 'mum.email', 'mum.email_subject', 'mum.mobile', 'mum.created_at', 'mum.user_type');
            //$params['sidx'] = 'mum.id';
            //$params['sord'] =  'DESC';
            $params['condition']['message_section'] = $messageSection;
            $result = $this->_userResource->fetchAllSentEmailUserData($params);
            $responce = new stdClass();
            $responce->page = $result['page'];
            $responce->total = $result['total'];
            $responce->records = $result['records'];

            foreach ($result['result'] as $k => $val) {
                $id = $val['id'];
                if (array_key_exists($val['user_type'], $userType)) {
                    $userTypeVal = $userType[$val['user_type']];
                } else {
                    $userTypeVal = 0;
                }

                $aid = "<a class = 'a-tag' href = '/admin/user/view-msg/message-section/$messageSection/id/$id'>" . $id . "</a>";

                $responce->rows[$k]['id'] = $val['id'];
                if ($messageSection == 1) {
                    $responce->rows[$k]['cell'] = array(
                        $aid,
                        $userTypeVal,
                        $val['username'],
                        $val['name'],
                        $val['email'],
                        $val['email_subject'],
                        $this->changeDateFormat($val['created_at'], DATETIMEFORMAT, DATE_TIME_FORMAT),
                    );
                } else if ($messageSection == 2) {

                    $responce->rows[$k]['cell'] = array(
                        $aid,
                        $userTypeVal,
                        $val['username'],
                        $val['name'],
                        $val['mobile'],
                        $this->changeDateFormat($val['created_at'], DATETIMEFORMAT, DATE_TIME_FORMAT),
                    );
                }
            }

            echo $this->jsonEncode($responce);
            exit;
        }
    }

    public function viewMsgAction() {

        global $userType;
        $request = $this->getRequest();
        $this->view->userType = $userType;
        $id = $request->getParam('id');
        $this->view->messageSection = $request->getParam('message-section');



        if (!empty($id)) {
            $userData = $this->_userResource->getEmailSentUserData($id);
            $this->view->formdata = $userData;
        }
    }

    public function msgDeleteAction() {
        $data = $this->getRequest()->getParams();


        if (!empty($data['id']) && !empty($data['message-section'])) {
            $arrIds = explode(',', $data['id']);
            if ($this->_userResource->deleteSentEmailUserData($arrIds)) {
                $this->_redirect(ADMIN_BASE_URL . 'user/all-sent-email-mobile-list/message-section/' . $data['message-section']);
            } else {
                $this->_redirect(ADMIN_BASE_URL . 'user/view-msg/message-section/' . $data['message-section'] . '/id/' . $data['id']);
            }
        }
    }

    public function msgMobileAction() {

        global $userType;
        $this->view->userType = $userType;
        $request = $this->getRequest();
    }

    public function sendMobileMsgAction() {
        global $userType;
        $request = $this->getRequest();
        $this->initMsg('ADMIN_SEND_MESSAGE');
        if ($request->isPost()) {
            $params = $this->__inputPostData;
            $data['message'] = $request->getPost('message');


            $response = array('status' => 0, 'msg' => '');
            $this->validateSendUsermobileMsgPostData($params, $this->errMsg);

            $inputData = new Zend_Filter_Input($this->filters, $this->validators);
            $inputData->setData($params);

            if ($inputData->isValid()) {
                $params['message'] = $data['message'];
                $params['created_by'] = $this->session->loginId;
                $params['message_section'] = 2;
                $insert = $this->_userResource->insrtsendUserMsgDetails($params);


                if (array_key_exists($params['user_type'], $userType)) {
                    $userTypeStr = strtoupper($userType[$params['user_type']]);
                    $contentUSR = sprintf($this->frmMsg['ADMIN_SEND_' . $userTypeStr . '_SMS']);
                }


                $usrThanks = array("{NAME}", "{NEWLINE}", "{MESSAGE}", "{WEBSITENAME}", "{SITEURL}", "{ADVERTISE_US}");
                $replaceUsrThanks = array(ucfirst($params['name']), "\n", $params['message'], WEBSITENAME, SITEURL, ADVERTISE_US);
                $usrMsgContent = urlencode(str_replace($usrThanks, $replaceUsrThanks, $contentUSR));
                //dd($usrMsgContent);
                $this->sendMessage($params['mobile'], $usrMsgContent, API_TIME);


                if ($insert) {
                    $this->_helper->FlashMessenger()->setNamespace('success')->addMessage('Message is successfully sent.');
                    $this->_redirect(ADMIN_BASE_URL . 'user/all-sent-email-mobile-list/message-section/' . $params['message_section']);
                } else {

                    $this->_helper->FlashMessenger()->setNamespace('error')->addMessage('Message is not sent.');
                }
            } else {
                $msg = $this->getValidatorErrors($inputData->getErrors());
                //print_r($msg);
                if ($msg == 'emailAddressInvalidFormat') {
                    $msg = 'Plz enter valid email id.';
                } else if ($msg == 'emailAddressInvalidHostnamehostnameInvalidHostnamehostnameLocalNameNotAllowed') {
                    $msg = 'Plz enter valid email id.';
                } else if ($msg == 'emailAddressInvalidHostnamehostnameInvalidHostnamehostnameLocalNameNotAllowedstringLengthTooShort') {
                    $msg = 'Plz enter valid email id.';
                } else if ($msg == 'isEmptyemailAddressInvalidFormat') {
                    $msg = 'Plz enter valid email id.';
                } else if ($msg == 'emailAddressInvalidFormatstringLengthTooShort') {
                    $msg = 'Plz enter valid email id.';
                } else {
                    $msg = $this->getValidatorErrors($inputData->getMessages());
                }

                $this->_helper->FlashMessenger()->setNamespace('error')->addMessage($msg);
                $this->_redirect(ADMIN_BASE_URL . 'user/msg-mobile');
            }
        }
    }

    public function validateSendUsermobileMsgPostData($data, $errMsg) {

        if (isset($data['name'])) {
            $this->validators['name'] = array('NotEmpty', 'messages' => array('Name can not be left empty.'));
        }

        if (isset($data['mobile'])) {

            $this->validators['mobile'] = array('NotEmpty', array('StringLength', array('max' => 10, 'min' => 10)), 'messages' => array('Plz enter valid mobile no.',
                    'Invalid mobile no.'));
        }



        if (isset($data['message'])) {
            $this->validators['message'] = array('NotEmpty', 'message' => array('Message can not be left empty.'));
        }
    }

}
