<?php

/**
 * This is Mylib_Controller_BaseController class. This class will
 * execute all the request to setup the application  .
 * @author Tech Lead
 * @package Mylib_Controller_BaseController
 * @subpackage Zend_Controller_Action
 */
include("image-resize-class.php");

class Mylib_Controller_BaseController extends Zend_Controller_Action {

    /**
     * This variable defines the zend filters object to be used as standard throughout the project
     */
    public $filters = array('StringTrim', 'HtmlEntities');

    /**
     * This variable defines the global object for user session namespace  
     */
    public $namespace = null;

    /**
     * This variable defines the object  to be used for storing user session data to be used as standard throughout the project
     */
    public $userSession = null;

    /**
     * This variable defines the object  to be used for storing user profile data to be used as standard throughout the project
     */
    public $validators = array();

    /**
     * @var public to allow valid pattern in dhtml grid      
     */
    public $validGridStrPatern = '/[^a-zA-Z0-9+_|?:\[\]{}()=$@!^\\\*#\,\s*\"\'\.\/&amp;<>-\d]/i';

    /** 	
     * @var private instance for model object	
     */
    protected $modelObj;

    /** 		
     * @var protected instance for view object	
     */
    protected $viewObj;

    /**
     * @var protected instance for Filtered InputData 
     */
    protected $__inputPostData = array();

    /**
     * @var protected instance for Filtered getparams data 	
     */
    protected $__params = array();

    /**
     * @var protected tables array 
     */
    public $__tables = array();

    /**
     * @var protected tables array 
     */
    public $__tablespk = array();

    /**
     * This method is used to initialize the application 
     * Created By : Tech Lead
     * Date : 19 Dec,2019
     * @param void
     * @return void
     */
    public function init() {
        $this->filters[2] = new Zend_Filter_StripTags(array('<p>', '<span>', '<div>', '<ul>', '<li>', '<ol>', '<br>', '<br/>', '<b>', '<strong>', '<em>', '<table>', '<td>', '<tr>', '<s>', '<strike>'));

        $this->_initSetup();
        $this->_initSession();
        $this->_filterInputData();
        //$this->_initSearchCity();
        $this->initMsg('');
        $this->initCommonErrMsg();
        //$this->initCommonData();
       // $this->initImageCloud();
        $this->initBlockIps();
    }

    /**
     * This function is used to select common data.
     * Created By : Tech Lead
     * Date : 19 Dec,2019
     * @param void
     * @return void
     */
 

    /**
     * This function is used to select common data.
     * Created By : Tech Lead
     * Date : 19 Dec,2019
     * @param void
     * @return void
     */
    protected function initCommonData() {
        $cities = new Application_Model_DbTable_City();
        $categories = new Application_Model_DbTable_Category();
        $this->_claimbusinessModal = new Application_Model_DbTable_Claimbusiness();
        $this->allcategory = $this->view->allcategory_footer = $categories->fetchCategories();
        $this->allcity = $this->view->allcity = $this->processCity($cities->fetchAllCity());
        /* if (isset($this->userSession->id))
          $this->checkBussClaimedOrNot = $this->view->checkBussClaimedOrNot = $this->_claimbusinessModal->checkBussClaimedOrNot($this->userSession->id); */
    }

    /**
     * This function is used to filter input data.
     * Created By : Tech Lead
     * Date : 19 Dec,2019
     * @param void
     * @return void
     */
    protected function _filterInputData() {

        if ($this->getRequest()->isPost()) {
            $this->__inputPostData = $this->filterInput($this->getRequest()->getPost());
        }
        if ($this->getRequest()->isPost() || $this->getRequest()->isGet()) {
            $this->__params = $this->filterInput($this->getRequest()->getParams());
        }
    }

    /**
     * This function is used to inititalize application level session.
     * Created By : Tech Lead
     * Date : 19 Dec,2019
     * @param void
     * @return void
     */
    protected function _initSession() {
        Zend_Session::start();
        $params = $this->getRequest()->getParams();
        $this->processSearchNameSpace($params);
        if (!empty($params['admin'])) {
            $userNamespace = new Zend_Session_Namespace('my');
        }else if (!empty($params['doctor'])) {
            $userNamespace = new Zend_Session_Namespace('mydoctor');      
        }else if (!empty($params['patient'])) {
            $userNamespace = new Zend_Session_Namespace('mypatient');
        }else if (!empty($params['hospital'])) {
            $userNamespace = new Zend_Session_Namespace('myhospital');
        } else {
            $userNamespace = new Zend_Session_Namespace('userNamespace');
        }

        $this->view->userSession = $this->userSession = $userNamespace->storage;
        $authObject = Zend_Auth::getInstance();
        $this->namespace = $authObject->getStorage()->read();
    }

    /**
     * This function is used to inititalize application level session.
     * Created By : Tech Lead
     * Date : 19 Dec,2019
     * @param void
     * @return void
     */
    protected function _initSearchCity() {

        $this->processSearchNameSpace($this->__inputPostData);
        $searchSession = new Zend_Session_Namespace('searchSession');
        if (!empty($searchSession->cityId)) {
            $this->cityId = $this->view->city = $searchSession->cityId;
        } else {
            $this->cityId = $this->view->city = DEFAULT_CITY_ID;
        }
        
        /**************************set session for location City search ******************/
        if (!empty($searchSession->search_type)) {
            $this->search_type = $this->view->search_type = $searchSession->search_type;
        }else{
            $this->search_type = $this->view->search_type = DEFAULT_CITY_TYPE;
        
        }
        
        if (isset($searchSession->search_location_id)) {
            $this->search_location_id = $this->view->search_location_id = $searchSession->search_location_id;
        }
        
        if (isset($searchSession->search_location_name)) {
            $this->search_location_name = $this->view->search_location_name = $searchSession->search_location_name;
        }
        /***************************[END]set session for location City search *******************/
        
        if (!empty($this->SearchControllers['CITY' . $this->cityId])) {
            $this->searchController = $this->view->searchController = $this->SearchControllers['CITY' . $this->cityId];
        } else {
            $this->searchController = $this->view->searchController = $this->SearchControllers['CITY1'];
        }
    }

    /**
     * This function is used to inititalize application level session.
     * Created By : Tech Lead
     * Date : 19 Dec,2019
     * @param void
     * @return void
     */
    protected function processSearchNameSpace($params = array()) {
        /*         * *******Code to ger current URL********** */
        $request = $this->getRequest();
        $uri = $request->getRequestUri();
        /*         * *******End of code to ger current URL********** */
        $searchSession = new Zend_Session_Namespace('searchSession');
        if (!empty($params['search-city'])) {
            $searchSession->cityId = $params['search-city'];
            
            /**************************set session for location City search ******************/
            $searchSession->search_type = $params['hidden_search_type'];
            
            if(!empty($params['hidden_search_location_id'])){
                $searchSession->search_location_id = $params['hidden_search_location_id'];
            }
           if($params['hidden_search_type'] != 'city'){
                $searchSession->search_location_name  = $params['hidden_search_location_name'];
              }else{
                $searchSession->search_location_name = '';
                }
           /***************************[END]set session for location City search *******************/
            
              } else if ($cityId = $this->identifyCity($uri)) {
            $searchSession->cityId = $cityId;
            $dataParams = $request->getParams();
            
            /*********** identify locationzone and store in session **********/
            if(!isset($dataParams['citynamehome'])){
                $locationzone = $this->identifyLocationZone();
                if(isset($locationzone['type']) && isset($locationzone['value'])){
                    $searchSession->search_type = $locationzone['type'];
                    $searchSession->search_location_name = $locationzone['value'];
                   }
            }
            /***********[END]identify locationzone and store in session **********/
            
            } else if (empty($searchSession->cityId)) {
            $searchSession->cityId = DEFAULT_CITY_ID;
            $searchSession->search_type = DEFAULT_CITY_TYPE;
            }
    }

    /**
     * This function is used to update the .
     * Created By : Tech Lead
     * Date : 19 Dec,2019
     * @param void
     * @return void
     */

    /**
     * This function is used to process categorywise fields.
     * Created By : Bidhan Chandra
     * Date : 05 April,2019
     * @param void
     * @return void
     */
    function identifyCity($uri) {
        $cityId = 0;
        $urlArr = explode("/", $uri);
        $regionsObj = Zend_Registry::get('Regions_Controller');
        $regionsArr = $regionsObj->toArray();

        if (!empty($urlArr[1]) && in_array($urlArr[1], $regionsArr)) {
            $cityId = $this->getCityId(array_search($urlArr[1], $regionsArr));
        } else if (!empty($urlArr[2]) && in_array($urlArr[2], $regionsArr)) {
            $cityId = $this->getCityId(array_search($urlArr[1], $regionsArr));
        }
        return $cityId;
    }
    
    function getCityId($city) {
        return str_replace("CITY", "", $city);
    }
    
    
    
    function identifyLocationZone() {
        $request = $this->getRequest();
        $param = $request->getParams();
        $locationzoneValue= '';
        $locationzoneArr = array();
        if(is_array($param) && !empty($param)){
            if(isset($param['areazone'])){
                $locationzoneValue = explode("--", $param['areazone']);
                $locationzoneArr['type'] = $locationzoneValue[0];
                $locationzoneArr['value'] = ucwords(str_replace("-", " ", $locationzoneValue[1]));
               
            }else if(isset($param['arealoc'])){
                $locationzoneValue = explode("--", $param['arealoc']);
                $locationzoneArr['type'] = $locationzoneValue[0];
                $locationzoneArr['value'] = ucwords(str_replace("-", " ", $locationzoneValue[1]));
            }else{
                $locationzoneArr['type'] = DEFAULT_CITY_TYPE;
                $locationzoneArr['value'] = '';
           
            }
            return $locationzoneArr;
          }
    }

    /**
     * This function is used to process categorywise fields.
     * Created By : Bidhan Chandra
     * Date : 05 April,2019
     * @param void
     * @return void
     */
    function processCategorywiseFields($data = array()) {
        if (Empty($data)) {
            return $data;
        }
        $processedData = array();
        foreach ($data as $dt) {
            if (in_array($dt['field_type'], array(3, 4, 5))) {
                if (empty($dt['values']))
                    $dt['values'] = "";
                $dt['field_options'] = $this->getFieldOptions($dt);
                $dt['field_values'] = explode(",", strtolower($dt['values']));
            }
            $fieldIndex = CAT_EXTRA_FIELD_PREFIX . CAT_FIELD_SEPARATOR . $dt['field_type'] . CAT_FIELD_SEPARATOR . $dt['id'] . CAT_FIELD_SEPARATOR . $dt['field_name'];
            $processedData[$fieldIndex] = $dt;
        }
        return $processedData;
    }

    function getFieldOptions($data = array()) {
        if (!empty($data['values'])) {
            $vendorMergeVal = explode(",", strtolower(trim(str_replace("amp;", "", $data['values']))));
            $vendorVal = array_map('strtolower', explode(",", trim(str_replace("amp;", "", $data['values']))));
            $catOptions = explode(",", trim(str_replace("amp;", "", $data['field_options'])));
            foreach ($catOptions as $option) {
                $pos = array_search(strtolower(trim(str_replace("amp;", "", $option))), $vendorVal);
                if ($pos !== false)
                    unset($vendorMergeVal[$pos]);
            }
            return array_unique(array_merge($catOptions, $vendorMergeVal));
        } else {
            return explode(",", str_replace("amp;", "", $data['field_options']));
        }
    }

    /**
     * This function is used to inititalize application level setting for view path helper path etc.
     * Created By : Tech Lead
     * Date : 19 Dec,2019
     * @param void
     * @return void
     */
    protected function _initSetup() {
        global $options;

        $front = Zend_Controller_Front::getInstance();
        $request = $front->getRequest();

        //dd($front->getRouter()->getCurrentRouteName());
        $this->view->controllerName = $this->controllerName = $request->getControllerName();
        $this->view->actionName = $this->actionName = $request->getActionName();
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->view->tables = $this->__tables = Zend_Registry::get("tables");
        $this->view->SearchControllers = $this->SearchControllers = Zend_Registry::get("Regions_Controller")->toArray();
        $this->__tablespk = Zend_Registry::get("tablespk")->toArray();
        $this->view->appOptions = $this->appOptions = $options;

        $this->view->keywords = '';

        $this->view->description = '';

        $this->view->a_btn_go_bgimage = 'http://res.cloudinary.com/vivahaayojan/image/upload/v1422687091/live/static_images/btn-go.png';
    }

    /**
     * This function is used to inititalize model object for each controller
     * Created By : Tech Lead
     * Date : 19 Dec,2019
     * @param string $model as model name
     * @return void
     */
    protected function _getModel($model = null, $moduleName = null) {
        if (empty($moduleName)) {
            $moduleName = 'Application';
        }
        $modelName = $moduleName . '_Model_DbTable_' . $model;
        $this->modelObj = new $modelName;
    }

    /**
     * Method is used to make string dhtmlx grid readable
     * Created By : Tech Lead
     * Date : 19 Dec,2019
     * @param $str as string
     * @return void
     */
    public function gridStringCDATAFilter($str) {
        $str = preg_replace($this->validGridStrPatern, '', $str);
        $str = str_replace(".br/", '', $str);
        $str = str_replace(" br/br/", '', $str);
        if (get_magic_quotes_gpc() == '1') {
            $_str = stripcslashes(html_entity_decode(htmlspecialchars_decode($str)));
        } else {
            $_str = html_entity_decode(htmlspecialchars_decode($str));
        }
        $_str = "<![CDATA[" . $_str . "]]>";
        return $_str;
    }

    /**
     * Method is used to make string dhtmlx grid readable
     * Created By : Tech Lead
     * Date : 19 Dec,2019
     * @param $str as string
     * @return void
     */
    public function gridStringFilter($str) {
        $str = preg_replace($this->validGridStrPatern, '', $str);
        $str = str_replace(".br/", '', $str);
        $str = str_replace(" br/br/", '', $str);
        if (get_magic_quotes_gpc() == '1') {
            $_str = stripcslashes(html_entity_decode(htmlspecialchars_decode($str)));
        } else {
            $_str = html_entity_decode(htmlspecialchars_decode($str));
        }
        return $_str;
    }

    /**
     * This function is used to fetch global constants from constant.ini file
     * Created By : Tech Lead
     * Date : 19 Dec,2019
     * @param  $sect_name  as string,This parameter is used to specify section in constant.ini file from which 	
     * @return array This function will return constants in array (key => value) format
     */
    public function getConstants($sect_name = '') {
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/constants.ini', $sect_name, true);
        return $config->toArray();
    }

    /**
     *  This function is used to inititalize ini messages and errors as global use in the application
     * Created By : Tech Lead
     * Date : 19 Dec,2019
     * @params $section  as string,  This parameter is used to specify section in error.ini  or messages.ini file from which 	
     * @return array This function will return messages in array (key => value) format
     */
    public function initMsg($section = null) {
        $pageTitle = "";
        if ($section !== null) {
            $this->frmMsg = $this->formMessage($section);
            $this->view->frmMsg = $this->frmMsg;
            $this->view->frmMsgEncode = json_encode($this->frmMsg);
            $this->errMsg = $this->errorMessage($section);
            $this->view->errMsg = $this->errMsg;
            $this->view->errMsgEncode = json_encode($this->errMsg);
            if (isset($this->frmMsg['PAGE_TITLE']))
                $pageTitle = $this->frmMsg['PAGE_TITLE'];
        } else {
            $this->view->frmMsg = '';
            $this->view->frmMsgEncode = '';
            $this->view->errMsg = '';
            $this->view->errMsgEncode = '';
        }
        $this->view->headTitle()->set($pageTitle);
    }

    /**
     *  This function is used to fetch application level message error 
     * Created By : Tech Lead
     * Date : 19 Dec,2019
     * @params void
     * @return void
     */
    public function initCommonErrMsg() {
        $msgCommon = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application_messages.ini', 'Common', true);
        $errCommon = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application_error.ini', 'Common', true);
        $this->msgCommon = $this->view->msgCommon = $msgCommon->toArray();
        $this->errCommon = $this->view->errCommon = $errCommon->toArray();
        $this->view->msgCommonEncode = json_encode($this->msgCommon);
        $this->view->errCommonEncode = json_encode($this->errCommon);
    }

    /**
     * This function is used to json encode
     *
     * @param object
     *
     * @return json encode object
     */
    public function jsonEncode($data = '') {
        return json_encode($data);
    }

    /**
     *  This function is used to if the required helpers is available otherwise sytem dies 
     * Created By : Tech Lead
     * Date : 19 Dec,2019
     * @params $helpers as array
     * @return void
     */
    public function checkHelpers($helpers = array()) {
        if (is_array($helpers)) {
            foreach ($helpers as $h) {
                if (!$this->_helper->hasHelper($h)) {
                    die($h . " " . $this->msgCommon['HELPER']['NOEXISTS']);
                }
            }
        } else {
            if (!$this->_helper->hasHelper($helpers)) {
                die($helpers . " " . $this->msgCommon['helper']['noExists']);
            }
        }
    }

    /**
     *   This function is used to fetch form messages from form_message.ini file of a defined module
     * Created By : Tech Lead
     * Date : 19 Dec,2019
     * @param string $sectName This parameter is used to specify section in form_message.ini file from which form messages are to fetched
     * @return array This function will return form messages in array (key => value) format
     */
    protected function formMessage($sectName) {
        $msgCommon = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application_messages.ini', 'Common', true);
        if (!empty($sectName)) {
            $msg = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application_messages.ini', $sectName, true);
            $msg = $msg->toArray();
        } else {
            $msg = array();
        }

        return array_merge($msgCommon->toArray(), $msg);
    }

    /**
     *  This function is used to fetch error messages from error.ini file of a defined module
     * Created By : Tech Lead
     * Date : 19 Dec,2019
     * @param string $sect_name This parameter is used to specify section in error.ini file from which error messages are to fetched.
     * @return array This function will return error messages in array (key => value) format
     */
    protected function errorMessage($sectName) {
        if (empty($sectName))
            return array();
        $msgCommon = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application_error.ini', 'Common', true);
        $msg = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application_error.ini', $sectName, true);
        return array_merge($msgCommon->toArray(), $msg->toArray());
    }

    /**
     *  This function is used to fetch errors generated by zend validator on form submission
     * Created By : Tech Lead
     * Date : 19 Dec,2019
     * @param array $errors This parameter defines the error thrown by zend validator for submitted form fields in array format
     * @return string This function returns errors in formatted text.
     */
    public function getValidatorErrors($errors = array(), $section = null) {
        $strErrors = '';
        if (!empty($errors) && is_array($errors)) {
            foreach ($errors as $error) {
                if (is_array($error)) {
                    foreach ($error as $val) {
                        if (!empty($val)) {
                            if ($section == 'admin') {
                                $strErrors .= '<div class = "s-error">' . $val . '</div>';
                            }else if ($section == 'doctor') {
                                $strErrors .= '<div class = "s-error">' . $val . '</div>';                           
                            }else if ($section == 'patient') {
                                $strErrors .= '<div class = "s-error">' . $val . '</div>';
                            } else {
                                $strErrors .= $val;
                            }
                        }
                    }
                } else {
                    if (!empty($error))
                        $strErrors .= $error;
                }
            }
        }

        return $strErrors;
    }

    /**
     * This function is used to upload file.
     * Created By : Tech Lead
     * Date : 19 Dec,2019
     * @param array	 as (key => value) format
     * @return array ,This function will return array (key => value) format
     */
    public function uploadFile($params = array()) {
        $retArr = array();
        $filePath = '';
        $upload = new Zend_File_Transfer_Adapter_Http();
        if (isset($params['extension'])) {
            $upload->addValidator('Extension', false, $params['extension']);
        }
        if (isset($params['maxFileSize'])) {
            $upload->addValidator('FilesSize', false, array('max' => $params['maxFileSize'] . 'Mb'));
        }
        $fileInfo = $upload->getFileInfo();
        if (isset($params['path'])) {
            $filePath = $params['path'];
        } else {
            $filePath = $this->constant->VIDEOUPLOADPATH;
        }
        if (!empty($fileInfo[$params['fieldName']]['name'])) {
            $ext = $this->_helper->Findexts($fileInfo[$params['fieldName']]['name']);
        }
        $fileName = $params['namePrefix'] . '_' . time();
        $retArr['fileName'] = $fileName;
        $retArr['ext'] = $ext;
        $fileName = $fileName . "." . $ext;
        @chmod($filePath, 0777);
        $uploadFile = $filePath . $fileName;
        $upload->addFilter('Rename', array('target' => $uploadFile, 'overwrite' => true));
        if ($upload->isValid()) {
            $upload->receive();
            $retArr['status'] = true;
        } else {
            $retArr['status'] = false;
            $retArr['errorArr'] = $upload->getErrors();
        }
        return $retArr;
    }

    /**
     * This action is used to create thumbnail of the image
     * Created By : Tech Lead
     * Date : 19 Dec,2019	
     * @param include width, path, height,etc..
     * @return
     */
    public function createThumbnail($params) {
        $maxwidth = $params['width'];
        $maxheight = $params['height'];
        $new_width = $params['width'];
        $new_height = $params['height'];
        $image_file = $params['image'];
        $image_path = $params['path'] . $image_file;
        $target_path = isset($params['target_path']) ? $params['target_path'] : $image_path;
        $img = null;
        $ext = strtolower(end(explode('.', $image_path)));
        if ($ext == 'jpg' || $ext == 'jpeg') {
            $img = @imagecreatefromjpeg($image_path);
        } else if ($ext == 'png') {
            $img = @imagecreatefrompng($image_path);
        } else if ($ext == 'gif') {
            $img = @imagecreatefromgif($image_path);
        }
        if ($img) {
            $width = imagesx($img);
            $height = imagesy($img);
            $scale = min($maxwidth / $width, $maxheight / $height);
            $w = round($width * $maxheight / $height);
            $h = round($height * $maxwidth / $width);
            if (($maxheight - $h) < ($maxwidth - $w)) {
                $new_width = & $w;
            } else {
                $new_height = & $h;
            }
            $tmp_img = imagecreatetruecolor($new_width, $new_height);
            imagecopyresampled($tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

            if ($ext == 'jpg' || $ext == 'jpeg') {
                imagejpeg($tmp_img, $target_path);
            } else if ($ext == 'png') {
                imagepng($tmp_img, $target_path);
            } else if ($ext == 'gif') {
                imagegif($tmp_img, $target_path);
            }

            return;
        }
    }

    /**
     * This function is used to send emails used as global in application
     * Created By : Tech Lead
     * Date : 19 Dec,2019
     * @captcha array	
     * @return
     */
    public function sendMail($mailTo, $subject = '', $message = '', $api = null, $templateid = '',$emailVerificationLink = "") {
        if (SEND_SMS_EMAIL) {
            if ($api) {
                $api_key = API_KEY;
                $subject = urlencode($subject);
                $fromname = 'Vivahaayojan.com';
                $from = 'no-reply@vivahaayojan.com';
                $content = rawurlencode($message);
                $sendto = $mailTo;
                $url = API_EMAIL_URL;
                $replytoid = '';

                if ($templateid) {
                    $template = '&template=' . $templateid;
                } else {
                    $template = '';
                }
                
                if($emailVerificationLink){
                    $strEmailVerificationLink = '&ATT_VERIFICATIONURL='.rawurlencode($emailVerificationLink);
                }else{
                    $strEmailVerificationLink = '';
                }
                
                $handle = curl_init($url . '?api_key=' . $api_key . '&subject=' . $subject . '&fromname=' . $fromname . '&from=' . $from . '&replytoid=' . $replytoid . '&content=' . $content . '&recipients=' . $sendto . $template.$strEmailVerificationLink);
                curl_setopt($handle, CURLOPT_VERBOSE, true);
                curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($handle, CURLOPT_SSLVERSION, 4);
                $content = curl_exec($handle);
                //throw new Exception(curl_error($handle), curl_errno($handle)); die;
                curl_close($handle);
                return $content;
            } else {

                $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);

                $config_auh = null;
                if ($config->mail->smtp->auth) {
                    $config_auh = array('auth' => 'login',
                        'username' => $config->mail->smtp->user,
                        'password' => $config->mail->smtp->password,
                        'ssl' => $config->mail->smtp->ssl,
                        'port' => $config->mail->smtp->port
                    );
                    $transport = new Zend_Mail_Transport_Smtp($config->mail->smtp->host, $config_auh);
                } else {
                    $transport = new Zend_Mail_Transport_Smtp($config->mail->smtp->host);
                }

                $mail = new Zend_Mail();
                $mail->addTo($mailTo['mailTo'], $mailTo['name']);
                $mail->setFrom($config->mail->smtp->mailFrom, $config->mail->smtp->mailFromName);
                $mail->setSubject($subject);
                $mail->setBodyHtml(nl2br($message));
                $response = array('error' => 0, 'msg' => 'sucess');
                try {
                    $mail->send($transport);
                    return $response;
                } catch (Exception $ex) {
                    $this->view->mailException = $ex->getMessage();
                    $response = array('error' => 1, 'msg' => $ex->getMessage());
                    return $response;
                }
            }
        } else {
            return false;
        }
    }

    /**
     * This function is used to send messages used as global in application
     * Created By : Vaibhav 
     * Date : 28 Feb,2019
     * @captcha array	
     * @return
     */
    /*public function sendMessage($mobile, $text, $time) {
        if (SEND_SMS_EMAIL) {
            $feedid = FEED_ID;
            $username = API_USERNAME;
            $password = API_PASSWORD;
            $url = API_PHONE_URL;

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => $url . '?feedid=' . $feedid . '&username=' . $username . '&password=' . $password . '&To=' . $mobile . '&time=' . $time . '&Text=' . $text . '&senderid=testSenderID',
                CURLOPT_POST => 1,
                CURLOPT_POSTFIELDS => array()
            ));
            $resp = curl_exec($curl);
            curl_close($curl);
            return $resp;
        } else {
            return false;
        }
    }*/    
    public function sendMessage($arrMobileNos = array(), $textMsg) {
        
        if (SEND_SMS_EMAIL) {
            
            //dd($arrMobileNos);
            $apiKey = urlencode(API_KEY);
            $sender = urlencode(API_SENDER_ID);
            
                       
            $mobileNos = implode(',', $arrMobileNos);
            $message = rawurlencode($textMsg);

            //$username = API_USERNAME;
            //$password = API_PASSWORD;
            //$url = 'https://'.SMS_API_KEY.':'.SMS_API_TOKEN.SMS_API_SUB_DOMAIN.'/v1/Accounts/'.SMS_API_ACCOUNT_SID."/Sms/send";
            $url = SMS_API_URL;
            $curl = curl_init();
          
            //$postData =  'accesskey=' . ACCESS_KEY.'&from='.API_SENDER_ID .'&to=' . $mobile .'&text=' . $text;
            $postData = array(
                // 'From' doesn't matter; For transactional, this will be replaced with your SenderId;
                // For promotional, this will be ignored by the SMS gateway
                'apikey' =>$apiKey,
                'numbers' =>$mobileNos,
                'sender' =>$sender,
                'message' =>$message
                
            );
             
            $ch = curl_init();
            $agent = $_SERVER["HTTP_USER_AGENT"];
            curl_setopt($ch, CURLOPT_USERAGENT, $agent);
            curl_setopt($ch, CURLOPT_URL, $url );
            
            //This is a POST query
            curl_setopt($ch, CURLOPT_POST, 1 );
            //Set the post data
            //curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

            //We want the content after the query
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            //Follow Location redirects
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_VERBOSE, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            //curl_setopt($ch, CURLOPT_SSLVERSION, 4);

            $postResult = curl_exec($ch);   
            //dd($postResult);
            //throw new Exception(curl_error($handle), curl_errno($handle)); die;
            curl_close($ch);

            return $postResult;
                            
        } else {
            return false;
        }
    }

    /**
     * This function is used to convert date format as global in application
     * Created By : Vaibhav 
     * Date : 17 April,2019
     * @captcha array	
     * @return
     */
    public function convertDate($curDate, $seperator) {
        $convertDate = $curDate;
        if (!empty($curDate) && !empty($seperator)) {
            $convertDate = date('Y' . $seperator . 'm' . $seperator . 'd', strtotime($curDate));
        }
        return $convertDate;
    }

    /**
     * This function is used to login user as global in application
     * Created By : Vaibhav 
     * Date : 17 April,2019
     * @captcha array	
     * @return
     */
    public function authFrontUser($data) {
        if (!empty($data)) {
            $namespace = 'userNamespace';
            $session = new Zend_Session_Namespace($namespace);
            $session->data = $data;

            $auth = Zend_Auth::getInstance();
            $auth->getStorage()->write($namespace);
            $storageObj = new Zend_Auth_Storage_Session($namespace);
            $auth->setStorage($storageObj);
            $auth->getStorage()->write($data);
            return true;
        }
    }

    /**
     * This function is used to create user
     * Created By : Vaibhav 
     * Date : 28 Feb,2019
     * @captcha array	
     * @return
     */
    public function commonRegisterNewUser($data,$verificationURLStatus = null) {
        $userModel = new Application_Model_DbTable_Users();
        $instered = $userModel->addUser($data);
        if ($instered) {
            $subject = 'Welcome to Vivahaayojan !';
            $content = 'Welcome';
            
            if($verificationURLStatus){
                $strVerificationURL = WEBSITE_URL.'account-confirm/'.base64_encode($instered).'/'.$data['activation_code'];
                
            }else{
                $strVerificationURL = '';
            }
            
            
            $sendto = $data['email'];
            $templateid = 4717;            
            $content = $this->sendMail($sendto, $subject, $content, 'true', $templateid,$strVerificationURL);
            return $instered;
        } else {
            return '0';
        }
    }

    /**
     * This function will encode the html special characters such as adding the slashes to the code etc.
     * Created By : Tech Lead
     * Date : 19 Dec,2019	
     * @param iString $val This is the string which is to be encoded
     * @return  string the encoded string
     */
    function EncodeInputValue($val, $forceQuot = true) {
        if (!get_magic_quotes_gpc() || $forceQuot) {
            if (is_array($val)) {
                while (list($k2, $v2) = each($val)) {
                    if (is_array($v2)) {

                        self::EncodeInputValue($v2, $forceQuot);
                    } else {
                        $v2 = strip_tags($v2, "<br/>");
                        $val[$k2] = trim(addslashes(htmlspecialchars($v2)));
                    }
                }
            } else {
                $v2 = strip_tags($v2, "<br/>");
                $val = trim(addslashes(htmlspecialchars($val)));
            }
        } else {
            if (is_array($val)) {
                while (list($k2, $v2) = each($val)) {
                    if (is_array($v2)) {
                        self::EncodeInputValue($v2, $forceQuot);
                    } else {
                        $v2 = strip_tags($v2, "<br/>");
                        $val[$k2] = htmlspecialchars($v2);
                    }
                }
            } else {
                $v2 = strip_tags($v2, "<br/>");
                $val = htmlspecialchars($val);
            }
        }
        return $val;
    }

    public function dataEncrypt($data) {
        return base64_encode($data);
    }

    public function dataDecrypt($data) {
        return base64_decode($data);
    }

    /**
     * This function is used to filter user inputed data 
     * Created By : Tech Lead
     * Date : 19 Dec,2019	
     * @param $data as array,$isXss as boolen
     * @return array
     */
    public function filterInput($data, $isXss = true, $isAddslashes = false, $ignore = false) {
        if (is_array($data)) {
            array_walk($data, 'Mylib_Controller_BaseController::encodeArray', $isAddslashes);
        } else {

            $oF = new Zend_Filter_HtmlEntities();
            $oF2 = new Zend_Filter_StripTags();
            if ($isXss) {
                $data = $this->removeXSS($data);
            }
            $data = strip_tags($data, "<br/>");

            if (!get_magic_quotes_gpc()) {
                if ($ignore == 1) {
                    $data = trim(addslashes(htmlspecialchars($data, ENT_IGNORE)));
                } else {
                    $data = trim(addslashes(htmlspecialchars($data)));
                }
            } else {
                if ($isAddslashes) {
                    $data = trim(addslashes(htmlspecialchars($data, ENT_QUOTES)));
                } else {
                    $data = trim(htmlspecialchars($data));
                }
            }

            $data = $oF->filter($oF2->filter($data, ""));
        }

        return $data;
    }

    /**
     * This function is used to call recurcevily to filter  user inputed array 
     * Created By : Tech Lead
     * Date : 19 Dec,2019	
     * @param &$val as array or string,$key as boolen
     * @return array or string
     */
    public static function encodeArray(&$val, $key, $isAddslashes) {
        if (is_array($val)) {
            array_walk($val, 'Mylib_Controller_BaseController::encodeArray', $isAddslashes);
        } else if (is_string($val)) {
            $oF = new Zend_Filter_HtmlEntities();
            $oF2 = new Zend_Filter_StripTags();

            $val = strip_tags($val, "<br/>");
            if (isset($isXss) && $isXss) {
                $val = $this->removeXSS($val);
            }
            if (!get_magic_quotes_gpc()) {
                $val = trim(addslashes(htmlspecialchars($val)));
            } else {
                if ($isAddslashes) {
                    $val = trim(addslashes(htmlspecialchars($val, ENT_QUOTES)));
                } else {
                    $val = trim(htmlspecialchars($val));
                }
            }
            $val = $oF->filter($oF2->filter(strip_tags($val, "")));
        }
    }

    /**
     * This function will remove XSS attack.
     * Created By : Tech Lead
     * Date : 19 Dec,2019	
     * @param String $xssStr This is the string which is to be fixed
     * @return string the $xssStr string
     */
    function removeXSS($xssStr) {
        $patterns[0] = '/<script>[^<]+<\/script>/i';
        $patterns[1] = '/<script >[^<]+<\/script>/i';
        $patterns[2] = '/<script\stype=\"javascript\">[^<]+<\/script>/i';
        $patterns[4] = '/<script\stype=\'javascript\'>[^<]+<\/script>/i';
        $patterns[5] = '/<script\stype=\"javascript\/text\">[^<]+<\/script>/i';
        $patterns[6] = '/<script\stype=\'javascript\/text\'>[^<]+<\/script>/i';
        $patterns[7] = '/<script\stype=\'javascript\/text\'>[^<]+<\/script>/i';
        $patterns[8] = '/expression\([^<]+\)/i';
        $patterns[8] = '/expression[^<]+\)/i';
        $patterns[9] = '/background:expression\([^<]+\)/i';
        $patterns[10] = '/eval\([^<]+\)/i';
        $patterns[11] = '/style\=\"background:expression\([^<]+\)/i';

        $xssStr = preg_replace($patterns, '', $xssStr);
        return $xssStr;
    }

    /**
     * This function is used to filter outputed to display 
     * Created By : Tech Lead
     * Date : 19 Dec,2019	
     * @param array
     * @return array
     */
    public function displayFilter($data) {
        if (is_array($data)) {
            array_walk($data, 'Mylib_Controller_BaseController::decodeArray');
        } else {
            $data = stripcslashes(html_entity_decode(htmlspecialchars_decode($data)));
        }
        return $data;
    }

    /**
     * This function is used to call recurcevily to filter outputed to display 
     * Created By : Tech Lead
     * Date : 19 Dec,2019	
     * @param &val as array or string,$key as boolen
     * @return array or string
     */
    public static function decodeArray(&$val, $key) {
        if (is_array($val)) {
            array_walk($val, 'Mylib_Controller_BaseController::decodeArray', @$new);
        } else {
            $val = stripcslashes(html_entity_decode(htmlspecialchars_decode($val)));
        }
    }

    /**
     * This function is used to generate random code 
     * Created By : Tech Lead
     * Date : 19 Dec,2019	
     * @param $baseCode as string or int
     * @return string
     */
    function randomcode($baseCode = null, $codeType = '') {
        srand(time());
        $rndTimestamp = strtotime(date('Y-m-d H:i:s'));
        $str1 = '';
        $str2 = '';
        $i = 1;
        while ($i <= 2) {
            $str1 .= chr((rand() % 26) + 97);
            $str2 .= chr((rand() % 26) + 97);
            $i++;
        }
        return $codeType . $str1 . $rndTimestamp . $baseCode . $str2;
    }

    /**
     * This function is used to generate integer random code 
     * Created By : Bidhan
     * Date : 12 May,2019	
     * @param $baseCode as string or int
     * @return string
     */
    function randomWithLength($length) {

        $number = '';
        for ($i = 0; $i < $length; $i++) {
            $number .= rand(0, 9);
        }

        return (int) $number;
    }

    /**
     * This function is used to get user ip 
     * Created By : Bidhan
     * Date : 13 May,2019	
     * @param $baseCode as string or int
     * @return string
     */
    function getUserIp() {

        $request = $this->getRequest();
        $userip = $request->getServer('REMOTE_ADDR');
        return $userip;
    }

    function getTagMeata($params = array()) {
        $seoModel = new Application_Model_DbTable_Seo();
        $metaTags = $seoModel->getTagMeta($params);
    }

    /**
     * This function is used to control pagination and sorting.
     *
     * Created By : Tech Lead
     * Date : 19 Dec,2019	
     * @param $params array by reference
     *
     * @return void.
     */
    public function getSearchParams($data = array()) {
        $params = array();

        $params['page'] = !empty($data['page']) ? $data['page'] : 1;

        $params['rows'] = $params['limit'] = !empty($data['rows']) ? $data['rows'] : NO_OF_RECORDS_PER_PAGE;

        $params['sidx'] = !empty($data['sidx']) ? $data['sidx'] : 1;

        $params['sord'] = !empty($data['sord']) ? $data['sord'] : DEFAULTSORTDIR;

        if (!empty($data['count'])) {
            $params['totalPages'] = ceil($data['count'] / $params['limit']);
        } else {
            $params['totalPages'] = 1;
        }
        if ($params['totalPages'] > 1) { //Condition put by Bidhan[20-Aug -2019]
            if ($params['page'] > $params['totalPages']) {
                $params['page'] = $params['totalPages'];
            }
        }
        $params['start'] = $params['limit'] * $params['page'] - $params['limit'];

        $filters = isset($data['filters']) ? $data['filters'] : "";
        $params['condition'] = $this->decodeFilters($filters);

        return $params;
    }

    public function decodeFilters($filters = '', $format = 'json') {
        $conditionArr = array();

        if (empty($filters))
            return $conditionArr;

        switch ($format) {

            case 'json':
            default:
                $conditions = json_decode($filters);
        }

        if (!empty($conditions->rules) && is_array($conditions->rules)) {
            foreach ($conditions->rules as $rule) {
                $conditionArr[$rule->field] = trim($rule->data);
            }
        }
        return $conditionArr;
    }

    /**
     * This function is used to convert date format as global in application
     * Created By : Bidhan 
     * Date : 17 April,2019
     * @captcha array	
     * @return
     */
    /*
    public function changeDateFormat($input_date, $input_date_format, $output_date_format) {
        $date = new Zend_Date();
        if ($output_date_format == '') {
            $output_date_format = OUTPUT_DATE_FORMAT;
        }
        if ($input_date != '0000-00-00 00:00:00' && $input_date != '') {
            @$date->set($input_date, $input_date_format); // To set the given date in format given by user

            return @$date->get($output_date_format); //to get the string in output format.
        } else {
            return @$date->get($output_date_format); //to get the string in output format.
        }
    }*/
    
    
     /**
     * This function is used to convert date format as global in application
     * Created By : Bidhan 
     * Date : 17 April,2019
     * @captcha array	
     * @return
     */
    public function changeDateFormat($input_date, $input_date_format, $output_date_format) {
        
        if ($output_date_format == '') {
            $output_date_format = YESTERDAY_DATE_FORMAT;
        }        
        if ($input_date != '0000-00-00 00:00:00' && $input_date != '') {                
             $date = new DateTime($input_date);
             return   $date->format($output_date_format); // 31.07.2012        
        } else {            
            return date($output_date_format);
        }
    }
    

    /**
     * This function is used to custom replace from search string.
     * Created By : Tech Lead
     * Date : 16 Aug,2019	
     * @param void
     *
     * @return string.
     */
    public function customFilter($filter = "", $search = array(), $replace = array()) {
        $ret = "";
        if (empty($search)) {
            $search = array("-", "--");
        }
        if (empty($replace)) {
            $replace = array(" ", " ");
        }
        if (is_string($filter)) {
            $ret = trim(str_replace($search, $replace, $filter));
        }

        if (is_array($filter)) {
            foreach ($filter as $k => $fl) {
                $ret[$k] = trim(str_replace($search, $replace, $fl));
            }
        }
        return $ret;
    }

    /**
     * This function is used to get one contact from vendor contact nos.
     * Created By : Bidhan Chandra
     * Date : 26 Aug,2019	
     * @param void
     * 
     * @return string.
     * 1 = mobile
     * 2 = Basic phone no
     */
    public function getVendorContactNo($contactNos, $contactNoType = null) {
        //dd($contactNos);
        $totalContactNos = explode(',', $contactNos);
        $searchword = '-';
        $arrPhones = array_filter($totalContactNos, function($var) use ($searchword) {
            return preg_match("/\b$searchword\b/i", $var);
        });
        if ($contactNoType == 1) {

            $arrMobiles = array_diff($totalContactNos, $arrPhones);

            if (!empty($arrMobiles[0])) {
                return $arrMobiles[0];
            } else {
                return false;
            }
        } else if ($contactNoType == 2) {

            if (!empty($arrPhones[0])) {
                return $arrPhones[0];
            } else {
                return false;
            }
        }
    }

    /**
     * This function is used to remove & from string.
     * Created By : Bidhan Chandra
     * Date : 14 Sep,2019	
     * @param void
     * 
     * @return string.
     * 
     * 
     */
    public function filterMetaString($string) {
        $string = $this->displayFilter($string);
        return str_replace("&amp;", "&", $string);
    }

    /**
     * This function is used to display all the relevent data if someone search by either category or location or both or none with count, 
     * data limit and pages.
     * Created By : Prashant Kumar
     * Date : 26 Dec,2019
     * @param void
     * @return void
     */
    protected function filterUrl(&$uri) {
        $uri = urldecode($uri);
        return $uri = rtrim(trim($uri), "/");
    }

    public function getEventUrl($request) {

        $uri = $request->getRequestUri();
        $uri = $this->filterUrl($uri);
        $explodeUrl = explode("/", $uri);
        if (isset($explodeUrl[3])) {
            return $explodeUrl[3];
        } else {
            return false;
        }
    }

    /**
     * This function is used to redirect URL, 
     * data limit and pages.
     * Created By : Prashant Kumar
     * Date : 16 Oct,2019
     * @param void
     * @return void
     */
    protected function getRedirectURL($uri, $str) {
        //$uri = urldecode($uri);

        if (stristr($uri, " ") == false) {
            $uri = str_replace(" ", "-", $uri);
            $uriStr = WEBSITE_URL . trim($uri, '/') . '/' . trim($str);
            $this->_redirect($uriStr, array('code' => 301));
            Exit;
        }
    }

    /**
     * This function is used to check IP address, 
     * data limit and pages.
     * Created By : Bidhan Chandra
     * Date : 16 Oct,2019
     * @param void
     * @return void
     */
    protected function checkIPAddress($arrIPs = array()) {
        //$uri = urldecode($uri);
        //$currIp = $_SERVER['REMOTE_ADDR'];

        $currIp = $this->getUserIp();
        $currIp = '182.68.109.153';
        dd($arrIPs);
        if (in_array($currIp, $arrIPs)) {
            return false;
        } else {
            return true;
        }
    }

    protected function checkFileType($filePath, $fileType) {

        // 1 For image        
        if ($fileType == 1) {
            global $allowedImageType;
            $imageMimeType = image_type_to_mime_type(exif_imagetype($filePath));

            if (in_array($imageMimeType, $allowedImageType)) {
                return true;
            } else {
                return false;
            }
        }
    }

    protected function processCity($data) {
        $cityArr = array();
        foreach ($data as $city) {
            $cityArr[$city['id']] = $city;
        }

        return $cityArr;
    }

    /**
     * This function is used to convert date format 
     * Created By : Bidhan
     * Date : 6 Jan,2015
     * @param string $model as model name
     * @return void
     */
    public function convertDateFormat($curDate, $dateFormat) {
        $convertDate = '';
        if (!empty($curDate) && !empty($dateFormat)) {
            $convertDate = date($dateFormat, $curDate);
        }
        return $convertDate;
    }

    /**
     * This function is used to add http in url 
     * Created By : Vaibhav
     * Date : 7 Jan,2015
     * @param string $url as url name
     * @return void
     */
    function addScheme($url, $scheme = 'http://') {
        if (empty($url))
            return false;

        return parse_url($url, PHP_URL_SCHEME) === null ?
                $scheme . $url : $url;
    }

    function getExtension($str) {
        $i = strrpos($str, ".");
        if (!$i) {
            return "";
        }
        $l = strlen($str) - $i;
        $ext = substr($str, $i + 1, $l);
        return $ext;
    }

    /**
     * This function is used to create index using text 
     * Created By : Bidhan Chandra
     * Date : 13 Feb,2015
     * @param string $url as url name
     * @return void
     */
    function createTextIndex($srchTxt = '', $rplcText = '', $txt) {
        $fnlStr = '';
        if ($txt != '') {
            $fnlStr = strtolower(str_replace($srchTxt, $rplcText, trim($txt)));
        }
        return $fnlStr;
    }

    /**
     * This function is used to create and write content in file 
     * Created By : Bidhan Chandra
     * Date : 13 Feb,2015
     * @param string $url as url name
     * @return void
     */
    function createAndWriteFile($filePath, $content = '') {
        $fwrite = false;
        if ($filePath != '') {
            $fP = fopen($filePath, 'w+') or die("Can't open file");
            $fwrite = fwrite($fP, $content);
        }
        return $fwrite;
    }

    function createCityMetaTags($metaStr) {
        $cityName = $this->allcity[$this->cityId]['name'];
        $sMetaStr = sprintf($metaStr);
        $searchStr = array("{CITY}");
        $replaceStr = array($cityName);
        return str_replace($searchStr, $replaceStr, $sMetaStr);
    }

    function makeRunningCase($str) {
        return $str = ucfirst(strtolower($str));
    }

    function getUserEmailId($emails, $noOfEmails = 1) {

        $arrEmailIds = explode(',', $emails);
        $emailCnt = count($arrEmailIds);
        if ($noOfEmails > $emailCnt) {
            return false;
        }

        $arrEmails = array();

        if ($noOfEmails > 1) {
            for ($i = 0; $i < $noOfEmails; $i++) {
                $arrEmails[$i] = $arrEmailIds[$i];
            }
        } else {
            $arrEmails[] = $arrEmailIds[0];
        }

        return $arrEmails;
    }

    function addDaysToDate($date, $days, $outPutDateFormat) {
        return date($outPutDateFormat, strtotime($date . " + " . $days));
    }

    protected function categoriesByCityId($cityId = null) {

        if ($cityId) {
            $objCategory = new Application_Model_DbTable_Category();
            $params['condition']['cityId'] = $cityId;
            return $result = $objCategory->fetchCategoriesByCityId($params);
        } else {
            $objCategory = new Application_Model_DbTable_Category();
            $params['condition']['cityId'] = DEFAULT_CITY_ID;
            return $result = $objCategory->fetchCategoriesByCityId($params);
        }
    }

    protected function allCities() {
        $objCity = new Application_Model_DbTable_city();
        $this->allAvailablecities = $this->view->allAvailablecities = $this->processCity($objCity->getAllCities());
    }

    function getPercentageValue($totalValue, $percentage) {
        $prcntVal = $percentage / 100;
        return $finalValue = $totalValue * $prcntVal;
    }

    function generateUniqueId($prefix = null) {

        if ($prefix) {
            $uniqid = uniqid($prefix);
        } else {
            $uniqid = uniqid('vivahaayojan-');
        }
        return $uniqid;
    }

    protected function delete($path) {
        if (is_dir($path) === true) {
            $files = array_diff(scandir($path), array('.', '..'));
            foreach ($files as $file) {
                $this->delete(realpath($path) . '/' . $file);
            }
            return rmdir($path);
        } else if (is_file($path) === true) {
            return unlink($path);
        }

        return false;
    }

    /**
     * This function is used to convert date format 
     * Created By : Bidhan
     * Date : 6 Jan,2015
     * @param string $model as model name
     * @return void
     */
    public function changeDateFormatNew($date, $dateFormat) {
        $convertDate = '';
        if (!empty($date) && !empty($dateFormat)) {
            $convertDate = date($dateFormat, strtotime($date));
        }
        return $convertDate;
    }

    /*
      public function calculateWeddingBudget($totalBudget, $percentage)
      {
      if($totalBudget != '' && $percentage != ''){

      $budget = round($totalBudget*($percentage/100),2);

      }
      return $budget;
      }


      public function calculatePercentage($totalPecentage, $totCnt)
      {
      $percntage = '';
      if($totalPecentage != '' && $totCnt != ''){
      $percntage = round($totalPecentage/$totCnt,2);
      }
      return $percntage;
      }
     */

    /**
     * This function is used to select common data.
     * Created By : Tech Lead
     * Date : 19 Dec,2019
     * @param void
     * @return void
     */
    protected function gmailAddessIntegration() {

        require_once LIB_PATH . '/Oauthgmail/GmailOath.php';
        return $gmailKey = array(
            "client_id" => $this->appOptions['gmailAddressImportApi']['clientId'],
            "client_secret" => $this->appOptions['gmailAddressImportApi']['ClientSecret'],
            "max_results" => 25,
            "redirect_url" => HOSTPATH . 'guests/gmailcontact'
        );
    }

    public function imageCrop($imgFileName, $thumbPath, $imagePath, $width, $height) {

        if ($imgFileName != '' && $thumbPath != '' && $imagePath != '' && $width != '' && $height != '') {
            require_once 'Zend/Polycast/Filter/ImageSize.php';
            require_once 'Zend/Polycast/Filter/ImageSize/Strategy/Crop.php';
            require_once 'Zend/Polycast/Filter/ImageSize/PathBuilder/Standard.php';

            $filter = new Zend_Polycast_Filter_ImageSize();

            $filter->setOutputPathBuilder(new Zend_Polycast_Filter_ImageSize_PathBuilder_Standard($thumbPath));

            $filter->getConfig()
                    ->setHeight($height)
                    ->setWidth($width)
                    ->setQuality(100)
                    ->setOverwriteMode(Zend_Polycast_Filter_ImageSize::OVERWRITE_ALL)
                    //->setOutputImageType(Zend_Polycast_Filter_ImageSize::TYPE_JPEG)
                    ->setStrategy(new Zend_Polycast_Filter_ImageSize_Strategy_Crop());

            $output = $filter->filter($imagePath . $imgFileName);

            return true;
        } else {
            return false;
        }
    }

    /**
     * This function is used to replace text
     * Created By : Bidhan Chandra
     * Date : 24 Aug,2015
     * @param string $url as url name
     * @return void
     */
    function replaceString($findTxtArr = array(), $rplcTexArr = array(), $txt) {
        try {
            $fnlStr = '';
            if (empty($findTxtArr) || empty($rplcTexArr) || empty($txt)) {
                throw new Exception('Please enter proper value in the function.');
            }
            $fnlStr = strtolower(str_replace($findTxtArr, $rplcTexArr, trim($txt)));
            return $fnlStr;
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), '\n';
        }
    }

    function getJsonDataFromFile($filePath, $includePath) {
        try {
            $jsonData = '';
            $fnlData = '';
            if (empty($filePath) || empty($includePath)) {
                throw new Exception('Please specify correct filepath in the function.');
            }
            $jsonData = file_get_contents($filePath, $includePath);
            $fnlData = json_decode($jsonData, true);
            return $fnlData;
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), '\n';
        }
    }
    
    function makeUCWords ($str) {
        return $str = ucwords(strtolower($str));
    }
    
    function salezSharkSendLead($params = array()) {
        
        if(SEND_LEAD_SALEZ_SHARK_API){    
            $curl = curl_init();
            curl_setopt_array($curl, array(
              CURLOPT_URL => SALEZ_SHARK_API_URL,
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 30,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "POST",
              CURLOPT_HTTPHEADER => $params,
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            if ($err) {
              echo "cURL Error #:" . $err;
            } else {
               return json_decode($response); 
              //echo $response;
            }
            
         }else{
             return false;
         }
    }
    
     /**
     * This function is used to set array vendor's extra fields.
     * Created By : Bidhan Chandra
     * Date : 21 Feb,2019
     * @param void
     * @return void
     */
    
    function processXtraFields($xtraFieldsDetails = array(), $selectedDlpMenus = array())
    {
       
        $processedFields = array();
        foreach ($selectedDlpMenus as $menu) {
           if(!empty($xtraFieldsDetails)){               
                foreach ($xtraFieldsDetails as $k => $field) {
                    if ($field['display_in_menu'] == $menu['menu_order']) {
                        $processedFields[$menu['menu_type']][] = $field;
                        unset($xtraFieldsDetails[$k]);
                    }
                }
           }
        }        
        return $processedFields;
    }
    
    /**
     * This function is used to display 404 page.
     * Created By : Bidhan Chandra
     * Date : 21 Feb,2019
     * @param void
     * @return void
     */
    
    public function set404Response() {

        $this->getResponse()->setHttpResponseCode(404);
        $this->view->message = 'Page not found';

        $this->_forward('notfound', 'error');
    }
    /********************Search controller functions **********************/
    protected function makeBreadCrumbLink($search = '', $arrLocationDetails = array(), $type = 'tag', $linkInnerDisplay = '') {

        if (empty($search) || empty($this->cityId))
            return false;
        $urlSuffix = ($type == 'business') ? "/type-business" : "";
        $baseLink = HOSTPATH;
        $searchLink = $this->createTextIndex(' ', '-', $search);
        $linkInnerDisplay = $this->createTextIndex(' ', '-', $linkInnerDisplay);
        $linkInnerDisplay = empty($linkInnerDisplay) ? $searchLink : $linkInnerDisplay;
        $breadCrum = array(
            array(
                'link' => $baseLink,
                'title' => "Home",
            )
        );

        $regionsObj = Zend_Registry::get('Regions_Controller');
        $regionsArr = $regionsObj->toArray();
        $baseLink = empty($regionsArr['CITY' . $this->cityId]) ? '' : $regionsArr['CITY' . $this->cityId];
        if ($baseLink) {
            $baseLink = HOSTPATH . $baseLink . '/';
        }

        if (!empty($this->cityId) && !empty($baseLink)) {
            $cityLink = array(
                'link' => $baseLink . $linkInnerDisplay . $urlSuffix,
                'title' => $this->makeUCWords($this->allcity[$this->cityId]['name']),
            );
            array_push($breadCrum, $cityLink);
        }


        if (!empty($arrLocationDetails['zone_name']) && !empty($baseLink)) {

            $zoneLink = array(
                'link' => $baseLink . $linkInnerDisplay . $urlSuffix . "/zone--" . $this->createTextIndex(' ', '-', $arrLocationDetails['zone_name']),
                'title' => $this->makeUCWords($arrLocationDetails['zone_name']),
            );

            array_push($breadCrum, $zoneLink);
        }

        if (!empty($arrLocationDetails['id']) && !empty($baseLink)) {

            $locationLink = array(
                'link' => $baseLink . $linkInnerDisplay . $urlSuffix . "/location--" . $this->createTextIndex(' ', '-', $arrLocationDetails['name']),
                'title' => $this->makeUCWords($arrLocationDetails['name']),
            );
            array_push($breadCrum, $locationLink);
        }
        /*         * ************[Start 29-10-2015]Code to display category link on the DLP page***************** */
        if (!empty($arrLocationDetails['category_name']) && !empty($baseLink)) {

            $locationLink = array(
                'link' => $baseLink . $linkInnerDisplay . $urlSuffix . "/location--" . $this->createTextIndex(' ', '-', $arrLocationDetails['name']),
                'title' => $this->makeUCWords($arrLocationDetails['category_name']),
            );
            array_push($breadCrum, $locationLink);
        }
        /*         * ************[END 29-10-2015]Code to display category link on the DLP page***************** */

        if (!empty($search) && !empty($baseLink)) {
            $currentUrl = HOSTPATH . ltrim($this->getRequest()->getRequestUri(), '/');
            $dlpLink = substr($currentUrl, 0, strrpos($currentUrl, "/"));
            $searchLink = array(
                'link' => $dlpLink,
                'title' => $this->makeUCWords($search),
            );
            array_push($breadCrum, $searchLink);
        }

        /*         * ************[Start 29-10-2015]Code to display category link on the DLP page***************** */
        if (!empty($arrLocationDetails['portfolioImage']) && !empty($baseLink)) {

            $locationLink = array(
                'link' => HOSTPATH . ltrim($this->getRequest()->getRequestUri(), '/'),
                'title' => $this->makeUCWords($arrLocationDetails['portfolioImage']),
            );
            array_push($breadCrum, $locationLink);
        }
        
         /*         * ************[Start 28-7-2016]Code to display review link on the DLP Review page***************** */
        if (!empty($arrLocationDetails['reviews']) && !empty($baseLink)) {

            $locationLink = array(
                'link' => HOSTPATH . ltrim($this->getRequest()->getRequestUri(), '/'),
                'title' => $this->makeUCWords($arrLocationDetails['reviews']),
            );
            array_push($breadCrum, $locationLink);
        }
        
        
         /*         * ************[Start 28-7-2016]Code to display Map link on the DLP Map page***************** */
        if (!empty($arrLocationDetails['map-direction']) && !empty($baseLink)) {

            $locationLink = array(
                'link' => HOSTPATH . ltrim($this->getRequest()->getRequestUri(), '/'),
                'title' => $this->makeUCWords($arrLocationDetails['map-direction']),
            );
            array_push($breadCrum, $locationLink);
        }
        
         /*         * ************[Start 28-7-2016]Code to display Map link on the DLP Map page***************** */
        if (!empty($arrLocationDetails['deals']) && !empty($baseLink)) {

            $locationLink = array(
                'link' => HOSTPATH . ltrim($this->getRequest()->getRequestUri(), '/'),
                'title' => $this->makeUCWords($arrLocationDetails['deals']),
            );
            array_push($breadCrum, $locationLink);
        }
        
         /*         * ************[Start 28-7-2016]Code to display Virtual Tour on the DLP Virtual Tour page***************** */
        if (!empty($arrLocationDetails['menu-available']) && !empty($baseLink)) {

            $locationLink = array(
                'link' => HOSTPATH . ltrim($this->getRequest()->getRequestUri(), '/'),
                'title' => $this->makeUCWords($arrLocationDetails['menu-available']),
            );
            array_push($breadCrum, $locationLink);
        }
         /*         * ************[Start 28-7-2016]Code to display Menu link on the DLP Menu page***************** */
        if (!empty($arrLocationDetails['virtual-tour']) && !empty($baseLink)) {

            $locationLink = array(
                'link' => HOSTPATH . ltrim($this->getRequest()->getRequestUri(), '/'),
                'title' => $this->makeUCWords($arrLocationDetails['virtual-tour']),
            );
            array_push($breadCrum, $locationLink);
        }
        
        return $breadCrum;
    }
    
    
    
    public function relatedTagsNameDisplay($relatedSearch) {
        $relatedSearch['city'] = $this->cityId;
        $relatedSearch['base_url'] = HOSTPATH . $this->searchController . '/';
        $relatedSearch['city_name'] = $this->allcity[$this->cityId]['name'];
        $relatedSearchStr = "";
        switch ($relatedSearch['type']) {
            CASE 'LP_TAG_LOCATION_ZONE_SEARCH':
                $relatedSearchStr = $this->lpTagLocatinZoneCitySearch($relatedSearch);
                break;
            CASE 'LP_TAG_SEARCH':
                $relatedSearchStr = $this->lpTagSearch($relatedSearch);
                break;
            CASE 'LP_BUSINESS_SEARCH':
                $relatedSearchStr = $this->lpBusinessSearch($relatedSearch);
                break;
            CASE 'DLP_SEARCH':
                $relatedSearchStr = $this->dlpSearch($relatedSearch);
                break;
            CASE 'LP_TAG_LOCATION_SEARCH':
                $relatedSearchStr = $this->lpTagLocationSearch($relatedSearch);
                break;
        }

        return $relatedSearchStr;
    }

    /**
     * This function is used to call getLinkRelatedSearch and shuffle result for displaying related tag link.
     * Created By : Bidhan Chandra
     * Date : 26 Nov,2019
     * @param void
     * @return void
     */
    protected function lpTagSearch($relatedSearch = array()) {
        if ($relatedSearch['type'] == 'LP_TAG_SEARCH') {
            $strSearchArr = array();
            $zoneName = !empty($relatedSearch['zone_name']) ? $relatedSearch['zone_name'] : '';
            foreach ($relatedSearch['data'] as $data) {
                $this->getLinkRelatedSearch($data, $relatedSearch, $strSearchArr);
            }
            shuffle($strSearchArr);
            return $strSearchArr;
            // return $this->makeLinkRelatedSearch($strSearchArr);
        }
    }

    protected function lpTagLocatinZoneCitySearch($relatedSearch = array()) {
        if ($relatedSearch['type'] == 'LP_TAG_LOCATION_ZONE_SEARCH') {
            $strSearchArr = array();
            $zoneName = !empty($relatedSearch['zone_name']) ? $relatedSearch['zone_name'] : '';
            foreach ($relatedSearch['data'] as $data) {
                $this->getLinkRelatedLocatinZoneCitySearch($data, $relatedSearch, $strSearchArr);
            }
            shuffle($strSearchArr);
            return $strSearchArr;
            // return $this->makeLinkRelatedSearch($strSearchArr);
        }
    }

    protected function lpTagLocationSearch($relatedLocationSearch = array()) {
        if ($relatedLocationSearch['type'] == 'LP_TAG_LOCATION_SEARCH') {
            $strLocationSearchArr = array();
            $zoneName = !empty($relatedLocationSearch['zone_name']) ? $relatedLocationSearch['zone_name'] : '';
            foreach ($relatedLocationSearch['data'] as $data) {
                $this->getLinkRelatedLocationSearch($data, $relatedLocationSearch, $strLocationSearchArr);
            }
            shuffle($strLocationSearchArr);
            return $strLocationSearchArr;
            // return $this->makeLinkRelatedSearch($strSearchArr);
        }
    }

    /**
     * This function is used to set array for displaying related tag link.
     * Created By : Bidhan Chandra
     * Date : 26 Nov,2019
     * @param void
     * @return void
     */
    protected function getLinkRelatedLocatinZoneCitySearch($data, $relatedSearch, &$strSearchArr) {
        $url = $relatedSearch['base_url'] . str_replace(" ", "-", strtolower($data['tag_name']));

        if (empty($relatedSearch['zone_name']) && empty($relatedSearch['location'])) {
            $strSearchArr[] = array(
                'url' => $url,
                'name' => strtolower($data['tag_name'] . ' in ' . $relatedSearch['city_name']),
            );
        }

        if (!empty($relatedSearch['zone_name'])) {
            $url .= '/zone--' . str_replace(" ", "-", strtolower($relatedSearch['zone_name']));

            $strSearchArr[] = array(
                'url' => $url,
                'name' => strtolower($data['tag_name'] . ' in ' . $relatedSearch['zone_name']),
            );
        }
        if (!empty($relatedSearch['location'])) {
            $url .= '/location--' . str_replace(" ", "-", strtolower($relatedSearch['location']['name']));

            $strSearchArr[] = array(
                'url' => $url,
                'name' => strtolower($data['tag_name'] . ' in ' . $relatedSearch['location']['name']),
            );
        }
    }

    protected function getLinkRelatedSearch($data, $relatedSearch, &$strSearchArr) {
        $url = $relatedSearch['base_url'] . str_replace(" ", "-", strtolower($data['tag_name']));

        $strSearchArr[] = array(
            'url' => $url,
            'name' => strtolower($data['tag_name'] . ' in ' . $relatedSearch['city_name']),
        );

        if (!empty($relatedSearch['zone_name'])) {
            $url .= '/zone--' . str_replace(" ", "-", strtolower($relatedSearch['zone_name']));

            $strSearchArr[] = array(
                'url' => $url,
                'name' => strtolower($data['tag_name'] . ' in ' . $relatedSearch['zone_name']),
            );
        }
    }

    protected function getLinkRelatedLocationSearch($data, $relatedLocationSearch, &$strLocationSearchArr) {
        $url = $relatedLocationSearch['base_url'] . str_replace(" ", "-", strtolower($data['tag_name']));

        $strLocationSearchArr[] = array(
            'url' => $url,
            'name' => strtolower($data['tag_name'] . ' in ' . $relatedLocationSearch['location_name']),
        );

        if (!empty($relatedLocationSearch['zone_name']) && (isset($data['location_type'])) && (in_array($data['location_type'], array(2, 3)))) {
            $url .= '/zone--' . str_replace(" ", "-", strtolower($relatedLocationSearch['zone_name']));

            $strLocationSearchArr[] = array(
                'url' => $url,
                'name' => strtolower($data['tag_name'] . ' in ' . $relatedLocationSearch['zone_name']),
            );
        }
    }
    
    
     /**
     * This function is used to send Large email used as global in application
     * Created By : Bidhan Chandra
     * Date : 9 Nov,2016
     * @captcha array	
     * @return
     */
    public function sendLargeMail($mailTo, $subject = '', $message = '', $api = null, $templateid = '',$from = null) {
        if (SEND_SMS_EMAIL) {
            if ($api) {
                $api_key = API_KEY;
                $subject = urlencode($subject);
                $fromname = 'Vivahaayojan.com';
                if(!$from){
                    //$from = 'no-reply@vivahaayojan.com';
                    $from = SUPPORT_EMAIL;
                }
                
                $content = rawurlencode($message);
                $sendto = $mailTo;
                $url = API_EMAIL_URL;
                $replytoid = '';

                if ($templateid) {
                    $template = '&template=' . $templateid;
                } else {
                    $template = '';
                }
                
                $post_data ='api_key=' . $api_key . '&subject=' . $subject . '&fromname=' . $fromname . '&from=' . $from . '&replytoid=' . $replytoid . '&content=' . $content . '&recipients=' . $sendto . $template;
                
               // $handle = curl_init($url . '?api_key=' . $api_key . '&subject=' . $subject . '&fromname=' . $fromname . '&from=' . $from . '&replytoid=' . $replytoid . '&content=' . $content . '&recipients=' . $sendto . $template);
               

                //Create a curl object
                $ch = curl_init();
                $agent = $_SERVER["HTTP_USER_AGENT"];
                curl_setopt($ch, CURLOPT_USERAGENT, $agent);
                curl_setopt($ch, CURLOPT_URL, $url );
                //This is a POST query
                curl_setopt($ch, CURLOPT_POST, 1 );
                //Set the post data
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
                //We want the content after the query
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                //Follow Location redirects
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                curl_setopt($ch, CURLOPT_VERBOSE, true);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSLVERSION, 4);
                
                $postResult = curl_exec($ch);             
                //throw new Exception(curl_error($handle), curl_errno($handle)); die;
                curl_close($ch);
                
                return $content;
            }
        } else {
            return false;
        }
    }
    
     /**
     * This function is used to write user ip address in ip files
     * Created By : Bidhan Chandra
     * Date : 1 Feb,2017
     * @captcha array	
     * @return
     */
    protected function writeIpAddress($ipAddress){
        
         

            if (file_exists(IP_FILE)) {

                $ipJson = file_get_contents(IP_FILE, FILE_USE_INCLUDE_PATH);
                $ipArr = json_decode($ipJson, true);

                if(!empty($ipArr)){

                    if(!in_array($currentIp,$ipArr)){

                        array_push($ipArr,$currentIp);
                        $jsonTxt = json_encode($ipArr);                
                        $fwrite = $this->createAndWriteFile(IP_FILE,$jsonTxt);                    
                        if($fwrite === false) {
                            return 'There is an error while writing tags in file.';
                            
                        }

                    }


                }else{


                    $currentArr = array($currentIp);
                    $jsonTxt = json_encode($currentArr);

                    $fwrite = $this->createAndWriteFile(IP_FILE,$jsonTxt); 
                    
                    if($fwrite === false) {
                        return 'There is an error while writing tags in file.';
                        
                    }
                }
                return 1; 
            }else{
                return "File not found";
            }
        
    }
    
    /**
     * This function is used to block user ip address
     * Created By : Bidhan Chandra
     * Date : 1 Feb,2017
     * @captcha array	
     * @return
     */
    
    protected function initBlockIps(){
        
        if(IP_BLOCK){
                        
            $currentIp = $_SERVER['REMOTE_ADDR'];

            if (file_exists(IP_FILE)) {
        

                $ipJson = file_get_contents(IP_FILE, FILE_USE_INCLUDE_PATH);
                $ipArr = json_decode($ipJson, true);

                if(!empty($ipArr)){

                    if(in_array($currentIp,$ipArr)){
                        $this->_redirect(WEBSITE_URL.'access_denied.php');                        
                    }

                }

             }
        }else{
           // $objIndex = new Application_Model_DbTable_Index();
           // $objIndex->user_online();
        }
    }
    
    public function apiTest($arrMobileNos = array(), $textMsg) {
        
        
            
            //dd($arrMobileNos);
            $apiKey = urlencode(API_KEY);
            $sender = urlencode(API_SENDER_ID);
            
                       
            $mobileNos = implode(',', $arrMobileNos);
            $message = rawurlencode($textMsg);

            //$username = API_USERNAME;
            //$password = API_PASSWORD;
            //$url = 'https://'.SMS_API_KEY.':'.SMS_API_TOKEN.SMS_API_SUB_DOMAIN.'/v1/Accounts/'.SMS_API_ACCOUNT_SID."/Sms/send";
            $url = 'https://www.thyrocare.com/API_BETA/master.svc/asvnCqhd5Kgv@uOCAHDfGt)md5hBcNMfvPgYBeIXe3s=/OFFER/products';
            $curl = curl_init();
          
            //$postData =  'accesskey=' . ACCESS_KEY.'&from='.API_SENDER_ID .'&to=' . $mobile .'&text=' . $text;
            $postData = array(
                // 'From' doesn't matter; For transactional, this will be replaced with your SenderId;
                // For promotional, this will be ignored by the SMS gateway
                'apikey' =>$apiKey,
                'numbers' =>$mobileNos,
                'sender' =>$sender,
                'message' =>$message
                
            );
             
            $ch = curl_init();
            $agent = $_SERVER["HTTP_USER_AGENT"];
            curl_setopt($ch, CURLOPT_USERAGENT, $agent);
            curl_setopt($ch, CURLOPT_URL, $url );
            
            //This is a POST query
            curl_setopt($ch, CURLOPT_POST, 0 );
            //Set the post data
            //curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
            //curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

            //We want the content after the query
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            //Follow Location redirects
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_VERBOSE, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            //curl_setopt($ch, CURLOPT_SSLVERSION, 4);

            $postResult = curl_exec($ch);   
            dd($postResult);
            //throw new Exception(curl_error($handle), curl_errno($handle)); die;
            curl_close($ch);

            return $postResult;
                            
       
    }
    
    
}

?>
