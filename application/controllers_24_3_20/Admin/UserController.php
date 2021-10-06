<?php

class Admin_UserController extends Mylib_Controller_AdminbaseController
{

    protected $_userResource;

    public function init()
    {
        parent::init();
        $this->_userResource = new Application_Model_DbTable_User();
        $this->_claimbusinessModal = new Application_Model_DbTable_Claimbusiness();
        $this->_aclModal = new Application_Model_DbTable_Acl();
        require_once 'My/Acl.php';
        $this->session = new Zend_Session_Namespace('my');
    }
    
    
    public function indexAction()
    {

        $request = $this->getRequest();

        if ($request->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $data = $this->getRequest()->getPost();
            
            $params = $this->getSearchParams($data);
            $params['fields']['main'] = array('id', 'name','email','created_date');
            $params['sidx'] = 'id';
            $params['sord'] =  'DESC';
            $result = $this->_userResource->fetchAllUsers($params);
            $responce = new stdClass();
            $responce->page = $result['page'];
            $responce->total = $result['total'];
            $responce->records = $result['records'];

            foreach ($result['result'] as $k => $val) {

                $responce->rows[$k]['id'] = $val['id'];
                $responce->rows[$k]['cell'] = array(
                    $val['name'],
                    $val['email'],
                    $val['created_date']                   
                );
            }

            echo $this->jsonEncode($responce);
            exit;
        }
    }

    public function addeditAction(){
        $id = $this->getRequest()->getParam('id');
        $this->view->allcity = $this->_claimbusinessModal->allcity();
        $params['sidx'] = 'id';
        $allRoles = $this->_aclModal->allroles($params);
        $this->view->allroles = $allRoles['result'];
        if (!empty($id)) {
            $userData = $this->_userResource->fetchUserData($id);
            $name = explode(" ", $userData['name']);

            if (count($name) > 1) {
                $lastname = $name[1];
            } else {
                $lastname = '';
            }

            $formdata = array(
                'id' => $userData['id'],
                'fname' => $name[0],
                'lname' => $lastname,
                'email' => $userData['email'],
                'password' => $userData['password'],
                //'phone' => $userData['phone'],
                //'dob' => $userData['dob'],
                //'address' => $userData['address'],
                //'locality' => $userData['locality'],
                //'city' => $userData['city'],
                //'wedding_date' => $userData['wedding_date'],
                //'self_status' => $userData['self_status'],
                //'fiance_name' => $userData['fiance_name'],
                //'fiance_email' => $userData['fiance_email'],
                //'usertype' => $userData['usertype'],
                'photo' => $userData['photo'],
                //'weddingwebsite' => $userData['wedding_website'],
                //'weddingplanning' => $userData['wedding_planning'],
                //'source' => $userData['source'],
                'business_name' => $userData['business'],
                'username' => $userData['username'],
                'role' => $userData['role']
            );
            $this->view->formdata = $formdata;
        }
    }

    public function addnewuserAction(){
        define("MAX_SIZE", "20000000");
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $request = $this->getRequest();
        $data = $request->getPost();
        $name = str_replace(" ","-",$data['fname']).'-'.str_replace(" ","-",$data['lname']);
        $UserId = $data['id'];
        
        $params['fields']['main'] = array('role_type');
        $params['condition']['id'] = $data['role'];
        $roleType = $this->_userResource->getUserRoleType($params);
                    
        $data['user_role_type'] = $roleType['role_type'];
        if(empty($data['username'])){
                $data['username'] = $data['email'];
        }
        if (empty($data['id'])) {
            $checkuser = $this->_userResource->checkuser($data);
            if($checkuser){
                $this->_helper->FlashMessenger()->setNamespace('error')->addMessage('User already exist.');                
                $this->_redirect(ADMIN_BASE_URL . 'user/addedit');
            }
            $errors = 0;
            $image = $_FILES['userphoto']['name'];
            if ($image) {
                $filename = stripslashes($_FILES['userphoto']['name']);
                $extension = $this->getExtension($filename);
                $extension = strtolower($extension);
                if (($extension != "jpg") && ($extension != "jpeg") && ($extension != "png") && ($extension != "gif") && ($extension != "JPG") && ($extension != "JPEG") && ($extension != "PNG") && ($extension != "GIF")) {
                    $errors = 1;
                } else {
                    $size = filesize($_FILES['userphoto']['tmp_name']);
                    if ($size > MAX_SIZE * 1024) {
                        echo '<h4>You have exceeded the size limit!</h4>';
                        $errors = 1;
                    } else {
                        $rndCode = $this->randomcode();
                        $image_base_name = $name .'-'.$rndCode;                          
                    	$image_name = $image_base_name . '.' . $extension;
                        $newname = UPLOAD_FILE_PATH . '/images/userimages/main/' . $image_name;
                        $copied = copy($_FILES['userphoto']['tmp_name'], $newname);
                        $cloudPublicId = $this->appOptions['cloudnary']['main_dir'].CLOUD_USER_PROFILE.$image_base_name;                            
                        $res = \Cloudinary\Uploader::upload($_FILES['userphoto']['tmp_name'], array("public_id" => $cloudPublicId,"invalidate" => TRUE));    
                        $inserbanner = $this->_userResource->saveUserData($image_name, $data);
                    }
                }
            } else {
                $image_name = '';
                $inserbanner = $this->_userResource->saveUserData($image_name, $data);
                
            }
        } else {
            $checkuser = $this->_userResource->checkuser($data);
            $userExist = $this->_userResource->checkUserExist($data);
            if($userExist){
                $this->_helper->FlashMessenger()->setNamespace('error')->addMessage('User already exist.');                
                $this->_redirect(ADMIN_BASE_URL . 'user/addedit/'.$data['id']);
            }
            $errors = 0;
            $image = $_FILES['userphoto']['name'];
            if ($image) {
                $filename = stripslashes($_FILES['userphoto']['name']);
                $extension = $this->getExtension($filename);
                $extension = strtolower($extension);
                if (($extension != "jpg") && ($extension != "jpeg") && ($extension != "png") && ($extension != "gif") && ($extension != "JPG") && ($extension != "JPEG") && ($extension != "PNG") && ($extension != "GIF")) {
                    $errors = 1;
                } else {
                    $size = filesize($_FILES['userphoto']['tmp_name']);
                    if ($size > MAX_SIZE * 1024) {
                        echo '<h4>You have exceeded the size limit!</h4>';
                        $errors = 1;
                    } else {
                        $newname = UPLOAD_FILE_PATH . '/images/userimages/main/' . $checkuser[0]['photo'];
                        unlink($newname);
                        $rndCode = $this->randomcode();
                        $image_base_name = $name .'-'.$rndCode;                          
                    	$image_name = $image_base_name . '.' . $extension;
                        $newname = UPLOAD_FILE_PATH . '/images/userimages/main/' . $image_name;
                        $copied = copy($_FILES['userphoto']['tmp_name'], $newname);
                        $this->createthumb($extension, $image_name, $_FILES['userphoto']['tmp_name']);
                        $preExt = '.'.$this->getExtension($checkuser[0]['photo']);
                        $prvUsrImg = $this->appOptions['cloudnary']['main_dir'].CLOUD_USER_PROFILE.str_replace($preExt,'',$checkuser[0]['photo']);                                                                                        
                        $cloudPublicId = $this->appOptions['cloudnary']['main_dir'].CLOUD_USER_PROFILE.$image_base_name;                            
                        \Cloudinary\Uploader::destroy($prvUsrImg, array('invalidate' => true));                            
                        $res = \Cloudinary\Uploader::upload($_FILES['userphoto']['tmp_name'], array("public_id" => $cloudPublicId,"invalidate" => TRUE));    
                        $updateUserData = $this->_userResource->updateUserData($image_name, $data);
                   }
                }
            } else {
                $image_name = '';
                $inserbanner = $this->_userResource->updateUserData($image_name, $data);
            }
        } // if closed for update user
        $this->_redirect(ADMIN_BASE_URL.'user/index/s/1');
    }
    
    public function viewinfoAction()
    {
        $id = $this->getRequest()->getParam('id');
        if (!empty($id)) {

            $userData = $this->_userResource->fetchUserData($id);
            $name = explode(" ", $userData['name']);

            if ($this->_userResource->weddingwebsitestats($id) > 0) {
                $wedwebstats = 1;
            } else {
                $wedwebstats = 0;
            }

            if ($this->_userResource->weddingplanstats($id) > 0) {
                $wedplanstats = 1;
            } else {
                $wedplanstats = 0;
            }

            $formdata = array(
                'id' => $userData['id'],
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => $userData['password'],
                'phone' => $userData['phone'],
                'dob' => $userData['dob'],
                'address' => $userData['address'],
                'location' => $userData['locality'],
                'city' => $userData['city'],
                'wedding_date' => $userData['wedding_date'],
                'self_status' => $userData['self_status'],
                'fiance_name' => $userData['fiance_name'],
                'fiance_email' => $userData['fiance_email'],
                'type' => $userData['usertype'],
                'photo' => $userData['photo'],
                'weddingwebsite' => $wedwebstats,
                'weddingplanning' => $wedplanstats,
                'created_date' => $userData['created_date'],
                'source' => $userData['source'],
            );
            //print_r($formdata); exit;
            $this->view->formdata = $formdata;
            $this->view->counteinvites = $this->_userResource->counteinvites($id);
            $this->view->countsiteshared = $this->_userResource->countsiteshared($id);
        }
    }
    
    public function deleteAction(){
        $id = $this->getRequest()->getPost('id');        
        $userdetail = $this->_userResource->fetchUserData($id);        
        $photo = $userdetail->photo;        
        
        if (isset($id)) {
            $newname = UPLOAD_FILE_PATH . '/images/userimages/main/'.$photo;
            $newnamethumb = UPLOAD_FILE_PATH . '/images/userimages/thumb/'.$photo;
            unlink($newname);
            unlink($newnamethumb);
            if ($this->_userResource->deleteUserData($id)) {
                $this->_redirect(ADMIN_BASE_URL.'user/index');
            } else {
                $this->_redirect(ADMIN_BASE_URL.'user/index');
            }
        }
    }

    public function deleteuserpicAction()
    {
        $id = $this->getRequest()->getParam('id');
        $userdetail = $this->_userResource->getuseremail($id);
        $useremail = $userdetail[0]['email'];
        $photo = $userdetail[0]['photo'];
        $photo = explode(".", $photo);
        $ext = $photo[2];

        if (isset($id)) {
            if ($this->_userResource->deleteUserImage($id)) {
                $newname = UPLOAD_FILE_PATH . '/images/userimages/main/' . $useremail . $ext;
		$newname1 = UPLOAD_FILE_PATH . '/images/userimages/thumb/' . $useremail . $ext;
                unlink($newname);
		unlink($newname1);
                $this->_redirect(ADMIN_BASE_URL.'user/addedit/?id=' . $id);
            } else {
                $this->_redirect(ADMIN_BASE_URL.'user/addedit/?id=' . $id);
            }
        }
    }

    public function getuserdetailAction()
    {
        $userid = $this->getRequest()->getParam('userid');
        $tabval = $this->getRequest()->getParam('tabval');

        if ($tabval == 1) {
            $userData = $this->_userResource->fetchUserData($userid);
            $this->view->formdata = $userData;
            $this->view->userid = $userid;
            $this->view->tabval = $tabval;
        } elseif ($tabval == 2) {
            $userData = $this->_userResource->fetchUserData($userid);
            $this->view->formdata = $userData;
            $this->view->userid = $userid;
            $this->view->tabval = $tabval;
            $usergallary = $this->_userResource->fetchUsergallary($userid);

            $this->view->usergallary = $usergallary;

            $uservideo = $this->_userResource->fetchVendorVideo($userid);
            $this->view->uservideo = $uservideo;
        }
    }

    public function updateportfolioAction()
    {

        $request = $this->getRequest();

        $pp_userid = $this->getRequest()->getParam('pp_userid');
        $pp_image_count = $this->getRequest()->getParam('pp_image_count');




        if (count($_FILES['pp_import_file_1']['name']) > 0)
            $pp_fileupload_count = 1;
        else
            $pp_fileupload_count = $pp_fileupload_count;

        $get_lastimage_id = $this->_userResource->pp_image_no($pp_userid);

        if ($pp_image_count == 0) {
            $imageno = 0;
        } else {
            $imageno = explode('.', $get_lastimage_id[0]['image']);
            $imageno = explode('_', $imageno[0]);
            $imageno = $imageno[1];
        }

        foreach ($_FILES['pp_import_file_1']['name'] as $key => $name) {

            define("MAX_SIZE", "20000000");

            $errors = 0;
            $image = $name;
            if ($image) {
                $filename = stripslashes($name);
                $extension = $this->getExtension($filename);
                $extension = strtolower($extension);
                if (($extension != "jpg") && ($extension != "jpeg") && ($extension != "png") && ($extension != "gif") && ($extension != "JPG") && ($extension != "JPEG") && ($extension != "PNG") && ($extension != "GIF")) {
                    //echo '<h3>Unknown extension!</h3>';
                    $errors = 1;
                    // $this->_redirect('/advertisement/addbanner/$');
                } else {

                    $size = filesize($_FILES['tmp_name'][$key]);

                    if ($size > MAX_SIZE * 1024) {
                        echo '<h4>You have exceeded the size limit!</h4>';
                        $errors = 1;
                    } else {


                        $imageno = $imageno + 1;
                        $image_name = $pp_userid . '_' . $imageno . '' . '.' . $extension;

                        $orignalImage = USER_PORTFOLIO . $image_name;
                        copy($_FILES['tmp_name'][$key], $orignalImage);

                        $insertUserPhoto = $this->_userResource->insertporfolio($pp_userid, $image_name);
                    }
                }
            }
        } // End for loop
        //$this->_redirect('/vendor/getvendordetail/vendorid/$pp_vendorid/tabval/2');
        $this->_redirect(ADMIN_BASE_URL.'user/getuserdetail/tabval/2/?userid=' . $pp_userid);
        // } //End main if
    }

    public function deleteportfolioAction()
    {
        $request = $this->getRequest();
        $imageid = $request->getParam('imageid');
        $userid = $request->getParam('userid');
        $getImageName = $this->_userResource->imageName($imageid);
        $imageName = $getImageName[0]['image'];
        $newname = USER_PORTFOLIO . $imageName;
        unlink($newname);

        $deleteimage = $this->_userResource->deleteimage($imageid);
        $this->_redirect(ADMIN_BASE_URL.'user/getuserdetail/tabval/2/?userid=' . $userid);
    }

    public function getimagepopupAction()
    {
        $this->_helper->layout->disableLayout();
        $this->view->image = $this->getRequest()->getParam('image');
    }

    public function getExtension($str)
    {
        $i = strrpos($str, ".");
        if (!$i) {
            return "";
        }
        $l = strlen($str) - $i;
        $ext = substr($str, $i + 1, $l);
        return $ext;
    }

    public function updatevideoAction()
    {
        $userid = $this->getRequest()->getParam('userid');
        $video_link = $this->getRequest()->getParam('video_link');
        $updatevideo = $this->_userResource->updatevideo($userid, $video_link);
        echo $userid;
        exit;
        $this->_redirect(ADMIN_BASE_URL.'user/getuserdetail/tabval/2/?userid=' . $userid);
    }

    public function deletevideoAction()
    {
        $userid = $this->getRequest()->getParam('userid');
        $link = $this->getRequest()->getParam('link');
        $deletevideo = $this->_userResource->deletevideo($userid, $link);
    }

    public function checkoldpassAction()
    {
        $this->_helper->layout->disableLayout();
        $request = $this->getRequest();
        $pass = $request->getParam('pass');
        $user = $request->getParam('user');
        $resultpass = $this->_userResource->checkpassword($user, $pass);
        echo $resultpass;
        die;
    }

    function createthumb($extension, $image, $image_tmpname)
    {
        //$image_filePath = VENDOR_PORTFOLIO_THUMB . $image;

        $image_filePath = UPLOAD_FILE_PATH . "/images/userimages/thumb/" . $image;


        if (in_array($extension, array('jpg', 'jpeg', 'gif', 'png'))) {

            list($gotwidth, $gotheight, $gottype, $gotattr) = getimagesize($image_tmpname);
            if ($extension == "jpg" || $extension == "jpeg") {
                $src = imagecreatefromjpeg($image_tmpname);
            } else if ($extension == "png") {
                $src = imagecreatefrompng($image_tmpname);
            } else if ($extension == "gif") {
                $src = imagecreatefromgif($image_tmpname);
            }

            list($width, $height) = getimagesize($image_tmpname);
            if ($gotwidth >= 55) {
                $newwidth = 55;
            } else {
                $newwidth = $gotwidth;
            }
            $newheight = 55;
            //$newheight = round(($gotheight * $newwidth) / $gotwidth);
            $tmp = imagecreatetruecolor($newwidth, $newheight);
            imagecopyresampled($tmp, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
            $createImageSave = imagejpeg($tmp, $image_filePath, 70);
        }
        return true;
    }
    
    public function getusernameAction() {
         $request = $this->getRequest();
        $this->_helper->layout->disableLayout();
        $term = $request->getParam('q');
        $users = $this->_userResource->getUsername($term);
        //dd($allcategory);
        $userName = '';
        for ($i = 0; $i < count($users); $i++) {                      
            echo $users[$i]['email'].'|'.$users[$i]['id'];
            echo "\n";
        }
        exit;
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
        if($request->isPost()){
            $params = $this->__inputPostData;
            $response = array('status' => 0, 'msg' => '');
            $this->validateSendWelcomePostData($params, $this->errMsg);
            
            $inputData = new Zend_Filter_Input($this->filters,$this->validators);
            $inputData->setData($params);
            
            if($inputData->isValid()){
            
                $params['created_by'] = $this->session->loginId;
                $insert = $this->_userResource->insrtsendUserDetails($params);
                
                /* * ****************Code to send SMS to welcome user************ */
                $content1 = sprintf($this->frmMsg['ADMIN_WELCOME_USER_SMS']);
                $search1 = array("{NAME}","{CONTACT_NO}", "{SITEURL}", "{NEWLINE}");
                $replace1 = array($params['name'], PRIMARY_VENDOR_NO, WEBSITE_NAME,"\n");
                $content1 = urlencode(str_replace($search1, $replace1, $content1));
                $mno = trim($params['mobile']);
                //$mno = '8800353404';           
                $this->sendMessage($mno, $content1, API_TIME);
                
                /** ****************End of code to send SMS to welcome user************ */
                $subject = 'Welcome to Vivahaayojan !';
                $content = 'Welcome';
                $sendto = $params['email'];
                $templateid = 3905;
                $content = $this->sendMail($sendto, $subject, $content, 'true', $templateid);
            
                
                if($insert){
                    $this->_helper->FlashMessenger()->setNamespace('success')->addMessage('Message is successfully sent.');                  
                    $this->_redirect(ADMIN_BASE_URL.'user/welcome-msg-list');
                }else{
                  
                    $this->_helper->FlashMessenger()->setNamespace('error')->addMessage('Message is not sent.');                  
                }
             
            
            
            }else{
                $msg = $this->getValidatorErrors($inputData->getMessages());
                $this->_helper->FlashMessenger()->setNamespace('error')->addMessage($msg); 
                $this->_redirect(ADMIN_BASE_URL.'user/welcome-msg');
            }
            
        }
        
        
        
    }
    
    
    public function validateSendWelcomePostData($data, $errMsg) {
        
        if(isset($data['name'])){
            $this->validators['name'] = array('NotEmpty','messages'=>array('Name can not be left empty.'));            
        }
        
        if(isset($data['email'])){
            $this->validators['email'] = array('NotEmpty',
                array('StringLength', 5),
                array('EmailAddress'),
                'messages'=>array(
                    'Email can not be left empty.',
                    'Email must be atleast 5 characters',
                    'Please enter valid email id.'
                    ));
        }

        
        if(isset($data['mobile'])){
            
            $this->validators['mobile'] = array('NotEmpty',array('StringLength', array('max'=>10,'min'=>10)),'messages'=>array('Mobile can not be left empty.',
                    'Invalid mobile no.'));
            
        }
        
    }
    
    
    public function welcomeMsgListAction()
    {

        $request = $this->getRequest();

        if ($request->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $data = $this->getRequest()->getPost();
            
            $params = $this->getSearchParams($data);
            $params['fields']['main'] = array('id', 'name','email','mobile','created_at');
            $params['sidx'] = 'id';
            $params['sord'] =  'DESC';
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
    
    
    public function welcomeUserDeleteAction(){
        $id = $this->getRequest()->getPost('id');        
        
        
        if (isset($id)) {           
            if ($this->_userResource->deleteWelcomeUserDetailsData($id)) {
                $this->_redirect(ADMIN_BASE_URL.'user/welcome-msg-list');
            } else {
                $this->_redirect(ADMIN_BASE_URL.'user/welcome-msg-list');
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
        if($request->isPost()){
            $params =  $this->__inputPostData;            
            $data['message'] =  $request->getPost('message');
            
            
            $response = array('status' => 0, 'msg' => '');
            $this->validateSendUserEmailMsgPostData($params, $this->errMsg);
            
            $inputData = new Zend_Filter_Input($this->filters,$this->validators);
            $inputData->setData($params);

            if($inputData->isValid()){
                $params['message'] = $data['message'];
                $params['created_by'] = $this->session->loginId;
                $insert = $this->_userResource->insrtsendUserMsgDetails($params);
               
                
                
                $subject = $params['email_subject'];
                
                $contentAdminEmail = sprintf($this->frmMsg['ADMIN_SEND_EMAIL_TEMPLATE']);
                
                $searchAdminEmail = array("{WP_LOGO_IMAGE_LINK}","{HEADING}","{MESSAGE}","{SITEURL}","{CURRENT_YEAR}","{WEBSITE_NAME}");
                $replaceAdminEmail = array(WP_LOGO_IMAGE_LINK,'Email',$data['message'],HOSTPATH, CURRENT_YEAR,WEBSITE_NAME);
                
                $contentAdminEmail = str_replace($searchAdminEmail, $replaceAdminEmail, $contentAdminEmail);
                
               
                
                $sendto = $params['email'];
               
                $content = $this->sendLargeMail($sendto, $subject, $contentAdminEmail, 'true','',ADVERTISE_US);
                
                if($insert){
                    $this->_helper->FlashMessenger()->setNamespace('success')->addMessage('Message is successfully sent.');                  
                    $this->_redirect(ADMIN_BASE_URL.'user/all-sent-email-mobile-list/message-section/1');
                }else{
                  
                    $this->_helper->FlashMessenger()->setNamespace('error')->addMessage('Message is not sent.');                  
                }
             
           
            
            }else{
                $msg = $this->getValidatorErrors($inputData->getMessages());
                $this->_helper->FlashMessenger()->setNamespace('error')->addMessage($msg); 
                $this->_redirect(ADMIN_BASE_URL.'user/msg-email');
            }
            
        }
        
        
        
    }
    
    
    public function validateSendUserEmailMsgPostData($data, $errMsg) {
       
        if(isset($data['name'])){
            $this->validators['name'] = array('NotEmpty','messages'=>array('Name can not be left empty.'));            
        }
        
        if(isset($data['email'])){
            $this->validators['email'] = array('NotEmpty',
                array('StringLength', 5),
                array('EmailAddress'),
                'messages'=>array(
                    'Email can not be left empty.',
                    'Email must be atleast 5 characters',
                    'Please enter valid email id.'
                    ));
        }
        
        if(isset($data['email_subject'])){
            $this->validators['email_subject'] = array('NotEmpty','message'=>array('Email subject can not be left empty.'));            
        }
        
        if(isset($data['message'])){
            $this->validators['message'] = array('NotEmpty','message'=>array('Message can not be left empty.'));            
        }

        
    }
    
    public function allSentEmailMobileListAction()
    {
        global $userType;
        
        $request = $this->getRequest();
        $this->view->messageSection = $messageSection = $request->getParam('message-section');
        if ($request->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $data = $this->getRequest()->getPost();
            
            $params = $this->getSearchParams($data);
            $params['fields']['main'] = array('mum.id', 'mum.name','mum.email','mum.email_subject','mum.mobile','mum.created_at','mum.user_type');
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
                if(array_key_exists($val['user_type'],$userType)){                 
                  $userTypeVal = $userType[$val['user_type']];                  
                }else{
                    $userTypeVal = 0;
                } 
              
                $aid = "<a class = 'a-tag' href = '/admin/user/view-msg/message-section/$messageSection/id/$id'>" . $id . "</a>";
                
                $responce->rows[$k]['id'] = $val['id'];
                if($messageSection == 1){                    
                    $responce->rows[$k]['cell'] = array(
                        $aid,
                        $userTypeVal,
                        $val['username'],                        
                        $val['name'],                       
                        $val['email'],
                        $val['email_subject'],
                        $this->changeDateFormat($val['created_at'], DATETIMEFORMAT, DATE_TIME_FORMAT),
                                        
                    );
                    
                }else if($messageSection == 2){
                    
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
    
    public function msgDeleteAction(){
        $data = $this->getRequest()->getParams();        
        
        
        if(!empty($data['id']) && !empty($data['message-section'])) { 
            $arrIds = explode(',',$data['id']);
            if ($this->_userResource->deleteSentEmailUserData($arrIds)) {
                $this->_redirect(ADMIN_BASE_URL.'user/all-sent-email-mobile-list/message-section/'.$data['message-section']);
            } else {
                $this->_redirect(ADMIN_BASE_URL.'user/view-msg/message-section/'.$data['message-section'].'/id/'.$data['id']);
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
        if($request->isPost()){
            $params =  $this->__inputPostData;            
            $data['message'] =  $request->getPost('message');
            
            
            $response = array('status' => 0, 'msg' => '');
            $this->validateSendUsermobileMsgPostData($params, $this->errMsg);
            
            $inputData = new Zend_Filter_Input($this->filters,$this->validators);
            $inputData->setData($params);

            if($inputData->isValid()){
                $params['message'] = $data['message'];
                $params['created_by'] = $this->session->loginId;
                $params['message_section'] = 2;
                $insert = $this->_userResource->insrtsendUserMsgDetails($params);
                
                
                if(array_key_exists($params['user_type'],$userType)){
                 $userTypeStr = strtoupper($userType[$params['user_type']]);   
                 $contentUSR = sprintf($this->frmMsg['ADMIN_SEND_'.$userTypeStr.'_SMS']);                                   
                }
              
                
                $usrThanks = array("{NAME}", "{NEWLINE}","{MESSAGE}","{WEBSITENAME}","{SITEURL}", "{ADVERTISE_US}");
                $replaceUsrThanks = array(ucfirst($params['name']),"\n",$params['message'],WEBSITENAME,SITEURL, ADVERTISE_US);
                $usrMsgContent = urlencode(str_replace($usrThanks, $replaceUsrThanks, $contentUSR));
                //dd($usrMsgContent);
                $this->sendMessage($params['mobile'], $usrMsgContent, API_TIME);
                
                                
                if($insert){
                    $this->_helper->FlashMessenger()->setNamespace('success')->addMessage('Message is successfully sent.');                  
                    $this->_redirect(ADMIN_BASE_URL.'user/all-sent-email-mobile-list/message-section/'.$params['message_section']);
                }else{
                  
                    $this->_helper->FlashMessenger()->setNamespace('error')->addMessage('Message is not sent.');                  
                }
             
           
            
            }else{
                $msg = $this->getValidatorErrors($inputData->getErrors());  
                //print_r($msg);
                if($msg == 'emailAddressInvalidFormat') {                    
                    $msg = 'Plz enter valid email id.';
                }else if ($msg == 'emailAddressInvalidHostnamehostnameInvalidHostnamehostnameLocalNameNotAllowed') {
                    $msg = 'Plz enter valid email id.';
                }else if($msg == 'emailAddressInvalidHostnamehostnameInvalidHostnamehostnameLocalNameNotAllowedstringLengthTooShort'){
                     $msg = 'Plz enter valid email id.';
                }else if($msg == 'isEmptyemailAddressInvalidFormat'){
                     $msg = 'Plz enter valid email id.';
                }else if($msg == 'emailAddressInvalidFormatstringLengthTooShort'){
                     $msg = 'Plz enter valid email id.';
                }else {
                    $msg = $this->getValidatorErrors($inputData->getMessages());                    
                    
                }
                
                $this->_helper->FlashMessenger()->setNamespace('error')->addMessage($msg); 
                $this->_redirect(ADMIN_BASE_URL.'user/msg-mobile');
            }
            
        }
        
        
        
    }
    
    
    public function validateSendUsermobileMsgPostData($data, $errMsg) {
       
        if(isset($data['name'])){
            $this->validators['name'] = array('NotEmpty','messages'=>array('Name can not be left empty.'));            
        }
        
        if(isset($data['mobile'])){
            
            $this->validators['mobile'] = array('NotEmpty',array('StringLength', array('max'=>10,'min'=>10)),'messages'=>array('Plz enter valid mobile no.',
                    'Invalid mobile no.'));
            
        }
        
        
        
        if(isset($data['message'])){
            $this->validators['message'] = array('NotEmpty','message'=>array('Message can not be left empty.'));            
        }

        
    }

}
