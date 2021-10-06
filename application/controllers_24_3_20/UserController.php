<?php

/**
 * This is Application_Controller_UserController class. This class will
 * execute all the request related to user.
 * @author Bidhan Chandra
 * @package Application_Controller_UserController
 * @subpackage Mylib_Controller_BaseController
 */
class UserController extends Mylib_Controller_BaseController {

    protected $_userType;

    /**
     * This method is used to initialize the user action 
     * Created By : Bidhan Chandra
     * Date : 27 Dec,2013
     * @param void
     * @return void
     */
    public function init() {
        parent::init();
        global $nowDate;
        global $nowDateTime, $options;

        $this->_getModel('Users');
        $this->_userModal = new Application_Model_DbTable_Users();
        $this->_searchResource = new Application_Model_DbTable_Search();
        $this->_claimbusinessModal = new Application_Model_DbTable_Claimbusiness();
        $this->view->gmailClientId = $options['gmailApi']['clientId'];
        $this->initMsg('Login');
        global $nowDate;
        global $nowDateTime;
    }

    /*
      public function indexAction() {

      }
     */

    /**
     * This function is used to set forgot password 
     * Created By : Bidhan Chandra
     * Date : 27 Dec,2013
     * @param void
     * @return void
     */
    public function forgotpasswordAction() {
        $this->_helper->layout->disableLayout();
        $request = $this->getRequest();

        if ($request->getPost()) {
            $params = $this->__inputPostData;
            $response = array('status' => 0, 'msg' => '', 'attemptCount' => 0);
            $this->validateLogin($params, $this->errMsg);
            $inputData = new Zend_Filter_Input($this->filters, $this->validators);
            $inputData->setData($params);
            if ($inputData->isValid()) {
                $email = $params['email'];
                $getCount = $this->_userModal->getUser($email);
                if ($getCount[0]['forgotpass_count'] < 5) {
                    $token = $this->randomcode();
                    $forgotpass = $this->_userModal->sendForgotPassMail($email, $token);
                    if ($forgotpass) {
                        $subject = $this->frmMsg['PASSREQUEST'];
                        
                        /*$content = sprintf($this->frmMsg['forgotMailContent']);
                        $search = array("{SITEURL}", "{TOKEN}");
                        $replace = array(HOSTPATH, $token);*/
                        
                        $content = sprintf($this->frmMsg['forgotMailContent_NEW']);
                        $search = array("{WP_LOGO_IMAGE_LINK}","{SUBJECT}", "{TOKEN}","{SITEURL}","{WEDDING_PLZ_CONTACT_NO}", "{Mylib_FB_LINK}", "{FB_IMAGE_LINK}", "{Mylib_TWITTER_LINK}", "{TWITTER_IMAGE_LINK}", "{Mylib_GP_LINK}", "{GP_IMAGE_LINK}","{WEDDININGPLZ_INSTAGRAM}","{INSTAGRAM_IMAGE_LINK}","{CURRENT_YEAR}","{WEBSITE_NAME}","{SUPPORT_EMAIL}");
                        $replace = array(WP_LOGO_IMAGE_LINK,'Password Reset Request',$token,SITEURL, WEDDING_PLZ_CONTACT_NO, Mylib_FB_LINK, FB_IMAGE_LINK, Mylib_TWITTER_LINK,TWITTER_IMAGE_LINK,Mylib_GP_LINK,GP_IMAGE_LINK,WEDDININGPLZ_INSTAGRAM,INSTAGRAM_IMAGE_LINK,CURRENT_YEAR,WEBSITE_NAME,SUPPORT_EMAIL);
                        
                        $content = str_replace($search, $replace, $content);
                        $sendto = $email;
                        
                        //$content = $this->sendMail($sendto, $subject, $content, 'true');
                        $content = $this->sendLargeMail($sendto, $subject, $content, 'true');
                        $response = array('status' => 1, 'msg' => 'success');
                    } else {
                        /* insert/update count */
                        /* select count */
                        $response['msg'] = $this->errMsg['invalid']['notregister'];
                        $response['attemptCount'] = 1;
                    }
                } else {
                    $response['msg'] = 'captcha';
                }
            } else {
                //$msg = $this->getValidatorErrors($inputData->getMessages());
                //$response['msg'] = $msg;
                $msg = $this->getValidatorErrors($inputData->getErrors());
                if ($msg = $this->errMsg['EMAIL_CUSTOM_VALIDATOR_CODE']) {
                    $response['msg'] = $this->errMsg['email']['invalid'];
                } else {
                    $msg = $this->getValidatorErrors($inputData->getMessages());
                    $response['msg'] = $msg;
                }
            }
            echo json_encode($response);
            exit;
        }
    }

    /**
     * This function is used to display feedback page 
     * Created By : Bidhan Chandra
     * Date : 27 Dec,2013
     * @param void
     * @return void
     */
    public function userfeedbackAction() {
        $this->_helper->layout->disableLayout();
    }

    /**
     * This function is used to register the user 
     * Created By : Bidhan Chandra
     * Date : 27 Dec,2013
     * @param void
     * modified by : JItendra
     * Date         : 10 Nov 14 
     * @return void
     */
    public function registerAction() {
        global $nowDate;
        $request = $this->getRequest();
        $this->_helper->layout->disableLayout();
        if ($request->isXmlHttpRequest()) {

            // dd($_SERVER);

            /* @ *******code for save user signup url***** */
            $refrer = str_replace(WWW_ROOT, "", $this->getRequest()->getHeader('referer'));
            if (empty($refrer)) {
                $refrer = 'Home Page';
            }



            if ($request->getPost()) {
                $params = $this->__inputPostData;
                $response = array('status' => 0, 'msg' => '');
                $this->validateRegister($params, $this->errMsg);
                $inputData = new Zend_Filter_Input($this->filters, $this->validators);
                $inputData->setData($params);
                if ($inputData->isValid()) {
                    $email = $params['email'];
                    $username = $params['username'];
                    $password = md5($params['password']);
                    $getUser = $this->_userModal->getUser($email);
                    if ($getUser) {
                        $response['msg'] = $this->errMsg['user']['exist'];
                    } else {
                        $date = $nowDate;
                        $data = array('name' => $username,
                            'email' => $email,
                            'password' => $password,
                            'status' => 'inactive',
                            'usertype' => '1',
                            'user_role_type' => 'b',
                            'registered_from_page' => $refrer,
                            'activation_code' => Zend_Session::getId());

                        $inserted = $this->commonRegisterNewUser($data,1);
                        if ($inserted) {
                            $response = array('status' => 1, 'msg' => 'success');
                        } else {
                            $response['msg'] = $this->errMsg['invalid']['user'];
                        }
                    }
                } else {
                    //$msg = $this->getValidatorErrors($inputData->getMessages());
                    //$response['msg'] = $msg;
                    $msg = $this->getValidatorErrors($inputData->getErrors());
                    if ($msg = $this->errMsg['EMAIL_CUSTOM_VALIDATOR_CODE']) {
                        $response['msg'] = $this->errMsg['email']['invalid'];
                    } else {
                        $msg = $this->getValidatorErrors($inputData->getMessages());
                        $response['msg'] = $msg;
                    }
                }
                echo json_encode($response);
                exit;
            }
        }/* else {
          $this->getResponse()->setHttpResponseCode(404);
          $this->view->message = 'Page not found';
          $this->_forward('notfound', 'error');
          } */
    }

    /**
     * This function is used to verify the user 
     * Created By : Bidhan Chandra
     * Date : 27 Dec,2013
     * @param void
     * @return void
     */
    public function verifyAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $token = $this->__params['token'];
        $email = $this->__params['email'];
        $users = $this->_userModal->verifyuser($token, $email);
    }

    /**
     * This function is used to process the login 
     * Created By : Bidhan Chandra
     * Date : 27 Dec,2013
     * @param void
     * @return void
     */
    public function loginAction() {

        $this->_helper->layout->disableLayout();

        $request = $this->getRequest();
        $this->view->type = (isset($this->__params['typeVal']) ? $this->__params['typeVal'] : '');
        if ($request->isXmlHttpRequest()) {
            if ($request->getPost()) {
                $params = $this->__inputPostData;
                $response = array('status' => 0, 'msg' => '');
                $this->validateLogin($params, $this->errMsg);
                $inputData = new Zend_Filter_Input($this->filters, $this->validators);
                $inputData->setData($params);
                if ($inputData->isValid()) {
                    $objLoginValidate = new Mylib_Auth_Validate($this->__tables->USERS, 'email', 'password');
                    $loginArr = array('identity' => $params['email'], 'credential' => $params['password']);
                    if ($objLoginValidate->isValid($loginArr, $request)) {
                        /* @ *****code for find last login user start******* */
                        $this->_auth = Zend_Auth::getInstance();
                        if ($this->_auth->hasIdentity()) {
                            $authData = $this->_auth->getIdentity();
                            //echo Zend_Session::getId(); die;
                            $this->_LoginLogoutDetailsResource = new Application_Model_DbTable_LoginLogoutDetails();
                            $lastLoginDetails = array();
                            $lastLoginDetails['userid'] = $authData->id;
                            $lastLoginDetails['session_id'] = Zend_Session::getId();
                            $lastlogin = $this->_LoginLogoutDetailsResource->addUser($lastLoginDetails);
                            $claimedBusiness = $this->_claimbusinessModal->getLastBusiness($authData->id);
                            $userSession = new Zend_Session_Namespace('userNamespace');
                            $userSession->businessId = $claimedBusiness;

                            //echo Zend_Session::getId(); die;
                        }
                        /* @ *****code for find last login user ends******* */
                        $this->rememberMe($params);
                        $response = array('status' => 1, 'msg' => $this->errMsg['loginSuccessful']);
                    } else {
                        $response['msg'] = $this->errMsg['invalid']['user'];
                    }
                } else {
                    //$msg = $this->getValidatorErrors($inputData->getMessages());
                    //$response['msg'] = $msg;
                    $msg = $this->getValidatorErrors($inputData->getErrors());
                    if ($msg = $this->errMsg['EMAIL_CUSTOM_VALIDATOR_CODE']) {
                        $response['msg'] = $this->errMsg['email']['invalid'];
                    } else {
                        $msg = $this->getValidatorErrors($inputData->getMessages());
                        $response['msg'] = $msg;
                    }
                }
                echo json_encode($response);
                exit;
            }
        } else {
            $this->getResponse()->setHttpResponseCode(404);
            $this->view->message = 'Page not found';
            $this->_forward('notfound', 'error');
        }
    }

    /**
     * This function is used to validate login process 
     * Created By : Bidhan Chandra
     * Date : 8 march,2014
     * @param void
     * @return void
     */
    protected function rememberMe($params = array()) {
        /*
          if ($params['remember'] == 1) {
          Zend_Session::rememberMe(REMEMBER_ME_TIME);
          } else {
          Zend_Session::forgetMe();
          } */
        Zend_Session::rememberMe(REMEMBER_ME_TIME);
    }

    /**
     * This function is used to validate login process 
     * Created By : Bidhan Chandra
     * Date : 8 march,2014
     * @param void
     * @return void
     */
    protected function validateLogin($data, $errMsg) {
        if (isset($data['email'])) {
            $this->validators['email'] = array(
                'NotEmpty',
                array('StringLength', 5),
                array('EmailAddress'),
                'messages' => array(
                    $errMsg['email']['no_empty'], $errMsg['email']['min_length'], $errMsg['email']['invalid']
            ));
        }

        if (isset($data['password'])) {
            $this->validators['password'] = array(
                'NotEmpty',
                'messages' => array(
                    $errMsg['password']['no_empty'],
                )
            );
        }
    }

    /**
     * This function is used to validate Register process 
     * Created By : Bidhan Chandra
     * Date : 8 march,2014
     * @param void
     * @return void
     */
    protected function validateRegister($data, $errMsg) {
        if (isset($data['email'])) {
            $this->validators['email'] = array(
                'NotEmpty',
                array('StringLength', 5),
                array('EmailAddress'),
                'messages' => array(
                    $errMsg['email']['no_empty'], $errMsg['email']['min_length'], $errMsg['email']['invalid']
            ));
        }

        if (isset($data['password'])) {
            $this->validators['password'] = array(
                'NotEmpty',
                'messages' => array(
                    $errMsg['password']['no_empty'],
                )
            );
        }

        if (isset($data['username'])) {
            $this->validators['username'] = array(
                'NotEmpty',
                'messages' => array(
                    $errMsg['username']['no_empty'],
                )
            );
        }
    }

    protected function _getAuthAdapter() {
        $dbAdapter = Zend_Db_Table::getDefaultAdapter();
        $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
        $authAdapter->setTableName('vy_users')
                ->setIdentityColumn('email')
                ->setCredentialColumn('password');
        return $authAdapter;
    }

    protected function _process($values) {
        $objRequest = $this->getRequest();
        $objLoginValidate = new Mylib_Auth_Validate($this->__tables->USERS, 'email', 'password');
        $loginArr = array('identity' => $values['email'], 'credential' => $values['password']);
        if ($objLoginValidate->isValid($loginArr, $objRequest)) {
            return true;
        }
        return false;
    }

    /**
     * This function is used to logout 
     * Created By : Bidhan Chandra
     * Date : 27 Dec,2013
     * @param void
     * @return void
     */
    public function logoutAction() {


        //echo Zend_Session::getId() ;die;
        $this->_LoginLogoutDetailsResource = new Application_Model_DbTable_LoginLogoutDetails();
        $lastlogin = $this->_LoginLogoutDetailsResource->updateLogoutTimeUser(Zend_Session::getId());
        // echo $lastlogin ; die;

        $objRequest = $this->getRequest();
        $isRedirect = $objRequest->getParam('isRedirect');
        $storage = new Zend_Auth_Storage_Session();



        $storage->clear();
        //session_destroy();
        Zend_Session::destroy(true);
        Zend_Session::forgetMe();
        if ($isRedirect == '1') {
            echo '1';
            exit;
        } else {
            //$this->_redirect(WEBSITE_URL);
            $this->_redirect($_SERVER['HTTP_REFERER']);
        }
    }

    /**
     * This function is used to login from facebook 
     * Created By : Bidhan Chandra
     * Date : 27 Dec,2013
     * @param void
     * @return void
     */
    public function fbloginAction() {
        global $nowDate;
        require 'src/facebook.php'; //include the facebook php sdk
        $facebook = new Facebook(array(
            'appId' => FACEBOOK_API_ID, //app id
            'secret' => FACEBOOK_SECRET, // app secret
            'cookie' => TRUE,
        ));
        $user = $facebook->getUser();
	//dd($user);
        if ($user != 0) {
            // check if current user is authenticated
            // Proceed knowing you have a logged in user who's authenticated.
            //$userProfile = $facebook->api('/me');
	    $userProfile =  $facebook->api('/'.$user);
            $service = 'facebook';
            $data = new stdClass;
            if (!empty($userProfile)) {
                $name = '' . $userProfile['first_name'] . ' ' . $userProfile['last_name'] . '';
                $data->name = $name;
                $data->email = $userProfile['email'];
                $data->fbid = $userProfile['id'];
                $date = $nowDate;
                $image_name = "fb_" . $userProfile['id'] . '-' . $this->randomcode() . ".jpg";
                $ifuserexit = $this->_userModal->getUser($userProfile['email']);
                if (count($ifuserexit) > 0) {
                    $data->id = $ifuserexit[0]['id'];
                    $data->photo = $ifuserexit[0]['photo'];
                    $data->google_id = $ifuserexit[0]['google_id'];
                    $dbdata = '';
                    if (empty($data->photo)) {
                        $dbdata = array('facebook_id' => $data->fbid, 'photo' => $image_name);
                        $updateFbInfo = $this->_userModal->updateProfileDetail($dbdata, $data->id);
                        $this->saveSocialMediaProfilePic($service, $data->fbid, $gmailImgUrl = null, $image_name);
                    } elseif (!empty($data->photo) && !empty($data->google_id)) {
                        $dbdata = array('facebook_id' => $data->fbid);
                        $updateFbInfo = $this->_userModal->updateProfileDetail($dbdata, $data->id);
                    }
                } else {
                    $this->saveSocialMediaProfilePic($service, $data->fbid, $gmailImgUrl = null, $image_name);
                    $dbdata = array('name' => $userProfile['first_name'] . " " . $userProfile['last_name'],
                        'email' => $userProfile['email'],
                        'city' => $this->userSession->city,
                        'self_status' => $userProfile['gender'],
                        'created_date' => $date,
                        'locality' => $userProfile['location']['name'],
                        'facebook_id' => $data->fbid,
                        'photo' => $image_name
                    );
                    $inserted = $this->commonRegisterNewUser($dbdata);
                    $data->id = $inserted;
                    $data->phone = '';
                    $data->user_role_type = 'f';
                }
                $claimedBusiness = $this->_claimbusinessModal->getLastBusiness($data->id);
                $data->businessId = $claimedBusiness['businessid'];
                $this->authFrontUser($data);
                //$this->_redirect('/');
                $this->_redirect($_SERVER['HTTP_REFERER']);
            } else {
                $this->_redirect('/');
            }
        } else {
            $this->_redirect('/');
        }
    }

    public function examplegoogleAction() {
        $this->view->value = 1;
    }

    /**
     * This function is used to login from gmail 
     * Created By : Bidhan Chandra
     * Date : 27 Dec,2013
     * @param void
     * @return void
     */
//    public function getgmailsessionAction() {
//
//        global $nowDate;
//        require_once 'src/openid.php';
//        $openid = new LightOpenID(HOSTPATH);
//        //dd($openid);
//        $user_details = $openid->getAttributes();
//        $ifuserexit = $this->_userModal->getUser($user_details['contact/email']);
//
//        if (count($ifuserexit) > 0) {
//            $name = $ifuserexit[0]['name'];
//            $data->name = $name;
//            $data->email = $user_details['contact/email'];
//            $data->id = $ifuserexit[0]['id'];
//            $data->phone = $ifuserexit[0]['phone'];
//            $data->user_role_type = $ifuserexit[0]['user_role_type'];
//        } else {
//            $date = $nowDate;
//            $dbdata = array('name' => $user_details['namePerson/first'] . " " . $user_details['namePerson/last'],
//                'email' => $user_details['contact/email'],
//                'city' => $this->userSession->city,
//                'self_status' => '',
//                'created_date' => $date
//            );
//            $createUser = $this->commonRegisterNewUser($dbdata);
//            if ($createUser) {
//                $data->name = $user_details['namePerson/first'] . ' ' . $user_details['namePerson/last'];
//                $data->email = $user_details['contact/email'];
//                $data->id = $createUser;
//                $data->phone = '';
//                $data->user_role_type = 'f';
//            }
//        }
//        $this->authFrontUser($data);
//        $this->_redirect('/');
//    }


    public function getgmailsessionAction() {


        //dd($openid);
        //$user_details = $openid->getAttributes();
        //$ifuserexit = $this->_userModal->getUser($user_details['contact/email']);
        global $nowDate;
        $request = $this->getRequest();
        $gmailData = $request->getPost();

        $userDetails = $this->_userModal->getUser($gmailData['email']);
        $service = 'google';
        $data = new stdClass;
        $data->gmailid = $gmailData['id'];
        $data->gmailimg = $gmailData['imgUrl'];
        $image_name = "gplus_" . $gmailData['id'] . '-' . $this->randomcode() . ".jpg";
        if (!empty($userDetails[0]) > 0) {
            $userDetails = $userDetails[0];
            $data->name = $userDetails['name'];
            $data->email = $userDetails['email'];
            $data->id = $userDetails['id'];
            $data->photo = $userDetails['photo'];
            $data->facebook_id = $userDetails['facebook_id'];
            $data->user_role_type = $userDetails['user_role_type'];
            $dbdata = '';
            if (empty($data->photo)) {
                $dbdata = array('google_id' => $data->gmailid, 'photo' => $image_name);
                $updateGPlusInfo = $this->_userModal->updateProfileDetail($dbdata, $data->id);
                $this->saveSocialMediaProfilePic($service, $data->gmailid, $data->gmailimg, $image_name);
            } elseif (!empty($data->photo) && !empty($data->facebook_id)) {
                $dbdata = array('google_id' => $data->gmailid);
                $updateGPlusInfo = $this->_userModal->updateProfileDetail($dbdata, $data->id);
            }
        } else {
            $this->saveSocialMediaProfilePic($service, $data->gmailid, $data->gmailimg, $image_name);
            $date = $nowDate;
            $dbdata = array('name' => $gmailData['name'],
                'email' => $gmailData['email'],
                'self_status' => '',
                'google_id' => $gmailData['id'],
                'user_role_type' => 'g',
                'photo' => $image_name,
            );
            $createUser = $this->commonRegisterNewUser($dbdata);
            if ($createUser) {
                $data->name = $gmailData['name'];
                $data->email = $gmailData['email'];
                $data->id = $createUser;
                $data->phone = '';
                $data->user_role_type = 'g';
            }
        }
        $claimedBusiness = $this->_claimbusinessModal->getLastBusiness($data->id);
        $data->businessId = $claimedBusiness['businessid'];
        $this->authFrontUser($data);
        echo json_encode(array('status' => 1));
        exit;
    }

    protected function saveSocialMediaProfilePic($service, $userid, $gmailImgUrl, $image_name) {
        switch ($service) {

            case "google":
                $image = file_get_contents($gmailImgUrl . "?sz=200");
                $imagePath = "images/userimages/main/" . $image_name;
                file_put_contents($imagePath, $image);
                /*                 * ********** for thumb *************** */
                $thumbPath = "images/userimages/thumb/" . $image_name;
                $copied = copy(WEBSITE_URL . "images/userimages/main/" . $image_name, $thumbPath);
                /*                 * *********************Code to upload images on local************************ */

                /*                 * *********************Code to upload images on cloudinary************************ */

                $cloudImageName = str_replace(".jpg", "", $image_name);
                $cloudFolderName = $this->appOptions['cloudnary']['main_dir'] . CLOUD_USER_PROFILE;
                $res = \Cloudinary\Uploader::upload($imagePath, array("public_id" => $cloudImageName, "folder" => $cloudFolderName));
                /*                 * *********************[END]Code to upload images on cloudinary************************ */
                break;

            case "facebook":
                /*                 * *********************Code to upload images on local************************ */
                $image = file_get_contents("https://graph.facebook.com/" . $userid . "/picture?type=large");
                $imagePath = "images/userimages/main/" . $image_name;
                file_put_contents($imagePath, $image);
                /*                 * ********** for thumb *************** */
                $thumbPath = "images/userimages/thumb/" . $image_name;
                $copied = copy(WEBSITE_URL . "images/userimages/main/" . $image_name, $thumbPath);
                /*                 * *********************Code to upload images on local************************ */

                /*                 * *********************Code to upload images on cloudinary************************ */
                $cloudImageName = str_replace(".jpg", "", $image_name);
                $cloudFolderName = $this->appOptions['cloudnary']['main_dir'] . CLOUD_USER_PROFILE;
                $res = \Cloudinary\Uploader::upload($imagePath, array("public_id" => $cloudImageName, "folder" => $cloudFolderName));
                /*                 * *********************[END]Code to upload images on cloudinary************************ */
                break;
        }
    }
    
    public function accountConfirmAction() {
        
        $this->_helper->layout->setLayout('layout_static');
        $this->view->keywords = '';
        $this->view->title = "";
        $this->view->headTitle()->set("");
        $this->view->description = "";
        $msg = '';
        $request = $this->getRequest();
        $params = $request->getParams();
        if(($params['uid']!= "") && ($params['activation_code']!= "")){            
            //echo base64_encode($params['uid']); die;
            $uId = base64_decode($params['uid']);
            $emailCount = $this->_userModal->verifyEmail($uId,$params['activation_code']);
            //dd($emailCount);
            if($emailCount['count'] == 1){                
                
                $emailCount = $this->_userModal->activateUserEmail($uId);                
                if($emailCount){
                    $msg = "Your account has been active now. Plz <a href = 'javascript:void(0);' onClick ='javascript:loginForm();' class = 'enqury'>login</a>.";
                }else{
                    $msg = "This account has already been confirmed.";
                    
                }
                
            }else if($emailCount['count'] == 0){
                $msg = "invalid link.";
            }
            
            //$this->view->msg = $msg;
            
        }else{
            
            $msg = "invalid link.";
            
        }
        
        $this->view->msg = $msg;
        //echo $msg; die;
    }

}

?>
