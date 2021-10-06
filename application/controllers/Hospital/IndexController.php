<?php

class Hospital_IndexController extends Mylib_Controller_HospitalbaseController
{

    protected $_userType;

    public function init()
    {

        parent::init();
        //$this->_tagResource = new Application_Model_DbTable_Tag();
        $this->_LoginLogoutDetailsResource = new Application_Model_DbTable_LoginLogoutDetails();
        /* Initialize action controller here */
        //$this->captcha = 'RAMram098@#$';
        //$this->captcha = CAPTCHA;
       
    }

    public function indexAction()
    {
       
		require_once 'Zend/Session/Namespace.php';
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $request = $this->getRequest();
        if ($request->isPost()) {
           
            $session = new Zend_Session_Namespace('myhospital');
            //$session->setExpirationSeconds(3600);
          //  if($request->getParam('captchCode') == $this->captcha){
                if ($this->_process(array('username' => $request->getParam('username'), 'password' => $request->getParam('password')))) {
                    // We're authenticated! Redirect to the home page
                    
                    $loginDetails['user_id'] = $session->loginId;
                    $loginDetails['ip'] = $this->getUserIp();
                    $loginDetails['login_code'] = $request->getParam('captchCode');
                    $loginDetails['session_id'] = session_id();
                    //die('HHH');
                    //$this->_LoginLogoutDetailsResource->addAdminloginDetails($loginDetails);
                    
                    $cntrlr= $request->getPost('cntrlr');
                    $act = $request->getPost('act');
                    
                    if($cntrlr && $act){
                       $this->_redirect(HOSPITAL_BASE_URL.$cntrlr.'/'.$act); 
                    }else{
                        $this->_redirect(HOSPITAL_BASE_URL.'dashboard/index');
                    }
                    
                }
          //  }
            $this->_redirect(HOSPITAL_BASE_URL.'index/login/s/1');
        } else {
          
           // $this->render('captcha');
            $this->_redirect(HOSPITAL_BASE_URL.'login/');
            //$this->_redirect(WEBSITE_URL.'login/captcha.php');
        }
    }

     public function loginAction()
    { 
        // action body
     	$this->_helper->layout->disableLayout();
       // $form = new Application_Form_Captcha();
        
      /* if($this->getRequest()->getPost()){
        if (!$form->isValid($this->getRequest()->getPost())) {
            
                $postData = $this->getRequest()->getPost();
                //$captcha = 'RAMram098@#$';
                $input = isset($postData['captcha']['input']) ? $postData['captcha']['input'] : '';
               
                if(!empty($input) && $input == $this->captcha){    
                    $this->view->captchCode  = $this->captcha;
                    $this->render('login');
                }else{
                    $this->_helper->FlashMessenger()->setNamespace('error')->addMessage('Wrong Captcha');                
                    if($input){                       
                        $this->_redirect(ADMIN_BASE_URL.'login/');
                    }else{                       
                        $this->_redirect(ADMIN_BASE_URL.'login/');
                    }
                }
           }else{
                             
              $this->view->form = $form;
              //$this->_helper->flashMessenger->addMessage("You dont have permission to access admin panel.Plz contact to vivahaayojan Administrator to access.");
           //   $this->view->messages = 'You dont have permission to access admin panel.Plz contact to vivahaayojan Administrator to access.';
             /// $this->render('captcha');  
           }       
        }else{
                
            $this->view->form = $form;
          //  $this->render('captcha');                
               //  $this->_redirect(ADMIN_BASE_URL.'login/');
        }*/
        $this->view->redirectcntrlr = $this->getRequest()->getParam('cntrlr');
        $this->view->redirectaction = $this->getRequest()->getParam('act');
        
        if ($this->getRequest()->getParam('s') == '1') {
            $this->view->errmsg = "The username or password you entered is incorrect.";
        }
        
    }

    protected function _process($values)
    {
        // Get our authentication adapter and check credentials
        $objRequest = $this->getRequest();
        $objLoginValidate = new Mylib_Auth_Validate($this->__tables->ADMIN, 'email', 'password');
        $loginArr = array('identity' => $values['username'], 'credential' => $values['password']);
        $objRequest->setParam('hospital', true);
        $objRequest->setParam('userRoleType', HOSPITAL_ROLE);
        if ($objLoginValidate->isValid($loginArr, $objRequest)) {
            //echo "Hi"; die;
           // $this->_userType = $this->userSession->userType;
           /*******End here********/
           return true;
        }
        return false;
    }
    /*
    protected function _getAuthAdapter()
    {
        $dbAdapter = Zend_Db_Table::getDefaultAdapter();
        $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
        $authAdapter->setTableName('admin')
                ->setIdentityColumn('username')
                ->setCredentialColumn('password');
        return $authAdapter;
    }
    */
    public function logoutAction()
    {
        global $nowDateTime;
        
        $namespace = new \Zend_Session_Namespace('myhospital');
        $namespace->unsetAll();
        Zend_Auth::getInstance()->clearIdentity();        
        //$arrLogOut['logout_time'] = $nowDateTime;        
        //$this->_LoginLogoutDetailsResource->updateLogoutTimeAdminUser($arrLogOut);
        $this->_redirect(HOSPITAL_BASE_URL.'login'); // back to login page
        
       // $this->_helper->redirector('login'); 
    }
    
    
    /********************Function to remove special characters form string **************************/
       public function validatefieldnameAction(){
                $name = " Bidhan Chandra";   
                $validator = new Zend_Validate_Alnum(array('allowWhiteSpace' => true));
                                                 
                        // Condition to check special characters in the name field excepting spaces
                            
                            if(!$validator->isValid(trim($name))){
                                $str = preg_replace('/[^A-Za-z0-9\. -]/', '', $name);                                
                                return $str;                                 
                            }else{                                              
                               return $name;                                
                                
                            }
                
            }
    /***********************Action for uploading tag related data in excel format****************/        
    public function uploadcategoryexcelAction(){
               if (isset($_POST['import_file'])) {
				$user_file = $_FILES["data_import_file"]["name"];
				if ((!empty($_FILES["data_import_file"])) && ($_FILES['data_import_file']['error'] == 0)) {
                $filename = basename($_FILES['data_import_file']['name']);
                $ext = substr($filename, strrpos($filename, '.') + 1);
                if (($ext == "csv" ) && ($_FILES["data_import_file"]["size"] < 500000000)) {
                    $newname = UPLOAD_FILE_PATH.'/bulkdata/'.$filename; //VENDOR_BULK_DATA_FILE. $filename;
                    if (!file_exists($newname)) {
                        //Attempt to move the uploaded file to it's new place
                        if ((move_uploaded_file($_FILES['data_import_file']['tmp_name'], $newname))) {
                           // echo $newname; die;
                            $rslt = $this->fileRead($newname);
                            if (empty($rslt))
                                $this->view->msg = "<p style='color:green'>It's done! The file data has been insterted in database</p>";
                            else {
                                $this->view->errorRows = implode(',', $rslt);
                                $this->view->msg = "<p style='color:green'>It's partially done! The below mentioned rows data has some data format issue so please insert these data manually.</p>";
                            }
                        } else {
                            $this->view->msg = "<p style='color:red'>Error: A problem occurred during file upload!</p>";
                        }
                    } else {
                        $this->view->msg = "<p style='color:red'>Error: File " . $_FILES["data_import_file"]["name"] . " already exists</p>";
                    }
                } else {
                    $this->view->msg = "<p style='color:red'>Error: Only .XLS files under 200000Kb are accepted for upload</p>";
                }
            } else {
                $this->view->msg = "<p style='color:red'>Error: No file uploaded</p>";
            }
        }
        echo $this->view->msg;
        echo $this->view->errorRows;
    }
    /***********************Functions for uploading tag related data in excel format****************/
    function fileRead($filename) {
        $this->session = new Zend_Session_Namespace('my');
        $header = NULL;
        $data = array();
        $errorRows = array();
        $delimiter = ',';
        if (($handle = fopen($filename, 'r')) !== FALSE) {
            while (($row = fgetcsv($handle, 5000, $delimiter)) !== FALSE) {
                if (!$header)
                    $header = $row;
                else {
                    $data[] = array_combine($header, $row);
                }
            }
            fclose($handle);
        }
        $errorRows = array();
                //print_r($data); die;
		//$categoryId = $data[0]['Id'];
                $count = count($data);
                foreach($data as $key=>$val){
                        if(!empty($val['Availiable_Services'])){    
                            $total_availiable_Services = array_map('trim',explode(",",$val['Availiable_Services']));
                            //print_r($availiable_ServicesArray); die;
                            $availiable_ServicesArray = '';
                            foreach($total_availiable_Services as $total_availiable_Service){
                                $availiable_ServicesArray[] = $this->filterTag($total_availiable_Service);
                            }
                            //print_r($availiable_ServicesArray); die;
                            $tagsToEnter = array_unique($availiable_ServicesArray);
                            $str_tagsToEnter = "'".implode("','",$tagsToEnter)."'";
                            if($str_tagsToEnter){
                                $tag_ids = $this->_tagResource->getTagIds($str_tagsToEnter,$key);
                            }else{
                                echo "error"; die;
                            }
                            //print_r($tag_ids);

                            if($tag_ids)
                            {
                                $date = new Zend_Date();



                                $vendor_data['available_services'] = $val['Availiable_Services'];
                                $vendor_data['tag_ids'] = $tag_ids;  
                                $vendor_data['v_id'] = $val['Vendor_ID'];
                                $vendor_data['updated_by'] = $this->session->loginId;

                                $vendor_data['updated_at'] = $date->get('YYYY-MM-dd');

                                $db_data[] =$this->_tagResource->updateVendorTable($vendor_data);

                            }  

                        }
                }
                //print_r($db_data); die;
        //$this->deleteFile($filename);
        return $errorRows;
    }

	/********************Function to remove special characters form string **************************/
    public function filterTag($name){   
		$validator = new Zend_Validate_Alnum(array('allowWhiteSpace' => true));
		if(!$validator->isValid(trim($name))){
			$str = preg_replace('/[^A-Za-z0-9\. -]/', '', $name);                                
			return $str;                                 
		} else {                                              
			return $name;                                
		}
	}
        
        function deleteFile($filePath) {

        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }
    
    public function unauthorizeAction()
    {
        $this->view->errmsg = "Sorry!! You are unauthorized to view this page.";
    }
/***********************End of Functions for uploading tag related data in excel format****************/	
    
    
    /***********************End of Functions for uploading tag related data in excel format****************/	
    
    
      /***********************End of Functions for uploading tag related data in excel format****************/	
    
    
    public function createthumbimageAction() {
		//die;
        define("MAX_SIZE", "200000000000");
        ini_set("memory_limit", "-1");
        ini_set("upload_max_filesize", "100M");
        ini_set("post_max_size", "200M");
        
        $id = $this->getRequest()->getParam('id');
        
        $directory = UPLOAD_FILE_PATH.'/images/portfolio/main/'.$id.'/';
        
        //$directory = VENDOR_IMAGES;
        $allScanFolders = scandir($directory, 1);  
        dd($allScanFolders,true);
        array_pop($allScanFolders);
        array_pop($allScanFolders);
        //dd($allScanImages);
        $destFilePath = UPLOAD_FILE_PATH . '/images/test/thumb/'.$id.'/';
		$i = 1;
        foreach($allScanFolders as $key1=>$folderName){
            
            $imageFilePath = $directory.$folderName.'/';  
            $flag = mkdir($destFilePath.$folderName, 0777, TRUE);   
			//var_dump($flag); die;
            if($flag == true){
                $allScanImages = scandir($imageFilePath, 1);  
               //dd($allScanImages);
                array_pop($allScanImages);
                array_pop($allScanImages);

                foreach ($allScanImages as $key => $image) {
                   $image_filePath = $imageFilePath.$image; 
                   $image_filePathThumb = $destFilePath.$folderName.'/'.$image;
                      //list($width, $height) = getimagesize($image_filePath);
                      list($gotwidth, $gotheight, $gottype, $gotattr) = getimagesize($image_filePath);



                      //$info = getimagesize($_FILES[$fileToUpload]['tmp_name'][$i]);

                                   $ratio_orig = $gotwidth / $gotheight;
                                   /*
                                   if ($imageWidth / $imageHeight > $ratio_orig) {
                                       $imageWidth = $imageHeight * $ratio_orig;
                                   } else {
                                       $imageHeight = $imageWidth / $ratio_orig;
                                   }*/
                      //dd($gotwidth);
                       if ($gotwidth >= 200) {
                           $newwidth = 200;
                       } else {
                           $newwidth = $gotwidth;
                       }
                       //$newheight = 200;  
                       $newheight = $newwidth / $ratio_orig;
                       $extension = pathinfo($image_filePath, PATHINFO_EXTENSION); 
                                       echo '<pre>';
                       dd('('.$i.'--'.$key1.')'.$image_filePath,true);
                       if ($extension == "jpg" || $extension == "jpeg") {
                       $src = imagecreatefromjpeg($image_filePath);
                   } else if ($extension == "png") {
                       $src = imagecreatefrompng($image_filePath);
                   } else if ($extension == "gif") {
                       $src = imagecreatefromgif($image_filePath);
                   }

                       $tmp = imagecreatetruecolor($newwidth, $newheight);
                   imagecopyresampled($tmp, $src, 0, 0, 0, 0, $newwidth, $newheight, $gotwidth, $gotheight);
                   $createImageSave = imagejpeg($tmp, $image_filePathThumb, 100); 
                               $i++;

               }
            }else{
                echo "<br>skipped file=>".$key1;
            }
       }
        die;
        
    }
	
	/***********************End of Functions for uploading tag related data in excel format****************/	
    
    
    public function createeventthumbimageAction() {
		//die;
        define("MAX_SIZE", "200000000000");
        ini_set("memory_limit", "-1");
        ini_set("upload_max_filesize", "100M");
        ini_set("post_max_size", "200M");
        
        $id = $this->getRequest()->getParam('id');
        
        $directory = UPLOAD_FILE_PATH.'/images/eventportfolio/main/';
        
        //$directory = VENDOR_IMAGES;
        $allScanFolders = scandir($directory, 1);  
        dd($allScanFolders,true);
        array_pop($allScanFolders);
        array_pop($allScanFolders);
        //dd($allScanImages);
        $destFilePath = UPLOAD_FILE_PATH . '/images/eventportfolio/event_thumb/';
            $i = 1;
                foreach ($allScanFolders as $key => $image) {
                   $image_filePath = $directory.$image; 
                   $image_filePathThumb = $destFilePath.$image;
                      //list($width, $height) = getimagesize($image_filePath);
                      list($gotwidth, $gotheight, $gottype, $gotattr) = getimagesize($image_filePath);
                      $ratio_orig = $gotwidth / $gotheight;
                                   
                      //dd($gotwidth);
                       if ($gotwidth >= 200) {
                           $newwidth = 200;
                       } else {
                           $newwidth = $gotwidth;
                       }
                       //$newheight = 200;  
                       $newheight = $newwidth / $ratio_orig;
                       $extension = pathinfo($image_filePath, PATHINFO_EXTENSION); 
                                       echo '<pre>';
                       dd('('.$i.'--'.$key.')'.$image_filePath,true);
                       if ($extension == "jpg" || $extension == "jpeg") {
                       $src = imagecreatefromjpeg($image_filePath);
                   } else if ($extension == "png") {
                       $src = imagecreatefrompng($image_filePath);
                   } else if ($extension == "gif") {
                       $src = imagecreatefromgif($image_filePath);
                   }

                   $tmp = imagecreatetruecolor($newwidth, $newheight);
                   imagecopyresampled($tmp, $src, 0, 0, 0, 0, $newwidth, $newheight, $gotwidth, $gotheight);
                   $createImageSave = imagejpeg($tmp, $image_filePathThumb, 100); 
                    $i++;

               }
            
      
        die;
        
    }
    
    
       
    
}
