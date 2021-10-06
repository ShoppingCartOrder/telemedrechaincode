<?php

class Patient_IndexController extends Mylib_Controller_PatientbaseController
{

    protected $_userType;

    public function init()
    {

        parent::init();
        
        //$this->_tagResource = new Application_Model_DbTable_Tag();
        //$this->_LoginLogoutDetailsResource = new Application_Model_DbTable_LoginLogoutDetails();
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
           
            $session = new Zend_Session_Namespace('mypatient');
            //$session->setExpirationSeconds(3600);
          //  if($request->getParam('captchCode') == $this->captcha){
                if ($this->_process(array('username' => $request->getParam('username'), 'password' => $request->getParam('password')))) {
                    // We're authenticated! Redirect to the home page
                    
                    $loginDetails['user_id'] = $session->loginId;
                    $loginDetails['ip'] = $this->getUserIp();
                    $loginDetails['login_code'] = $request->getParam('captchCode');
                    $loginDetails['session_id'] = session_id();
                    
                    //$this->_LoginLogoutDetailsResource->addAdminloginDetails($loginDetails);
                    
                    $this->_redirect(PATIENT_BASE_URL.'dashboard/index');
                }else{
                    $this->_helper->FlashMessenger()->setNamespace('error')->addMessage('Invalid email or password. Please try again.');                  
            
                }
          //  }
            $this->_redirect(PATIENT_BASE_URL.'index/login/s/1');
        } else {
          
           // $this->render('captcha');
            $this->_redirect(PATIENT_BASE_URL.'login/');
            //$this->_redirect(WEBSITE_URL.'login/captcha.php');
        }
    }

     public function loginAction()
    { 
         $this->_helper->layout->setLayout('innerlayout');
        // action body
     	//$this->_helper->layout->disableLayout();
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
        $objRequest->setParam('patient', true);
        $objRequest->setParam('userRoleType', PATIENT_ROLE);
        
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
        
        $namespace = new \Zend_Session_Namespace('mydoctor');
        $namespace->unsetAll();
        Zend_Auth::getInstance()->clearIdentity();        
        //$arrLogOut['logout_time'] = $nowDateTime;        
        //$this->_LoginLogoutDetailsResource->updateLogoutTimeAdminUser($arrLogOut);
        $this->_redirect(PATIENT_BASE_URL.'login'); // back to login page
        //$this->_helper->redirector('login'); // back to login page
    }
    
    

    
    public function unauthorizeAction()
    {
        $this->view->errmsg = "Sorry!! You are unauthorized to view this page.";
    }

    
    
       
    
}
