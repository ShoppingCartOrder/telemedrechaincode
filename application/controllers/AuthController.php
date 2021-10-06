<?php

/**
 * This is Application_Controller_AuthController class. This class will
 * execute all the request related to authentication.
 * @author Bidhan Chandra
 * @package Application_Controller_AuthController
 * @subpackage Mylib_Controller_BaseController
 */
class AuthController extends Mylib_Controller_BaseController {

    protected $_type;
    protected $_auth;
    protected $_adapter;

    /**
     * This method is used to initialize the auth action 
     * Created By : Bidhan Chandra
     * Date : 27 Dec,2013
     * @param void
     * @return void
     */
    public function init() {
		parent::init();
        // Disable the main layout renderer
        $this->_helper->layout->disableLayout();
        // Do not even attempt to render a view
        $this->_helper->viewRenderer->setNoRender(true);
        if ($this->getRequest()->getParam('type')) {
            $this->_type = $this->getRequest()->getParam('type');

            $this->_auth = Zend_Auth::getInstance();

            $this->_adapter = Josh_Auth_Adapter_Factory::factory($this->_type, $this->getRequest()->getParams());
        }
    }
    
    /**
     * This method is used to encoded the request and send response 
     * Created By : Bidhan Chandra
     * Date : 27 Dec,2013
     * @param void
     * @return void
     */
    protected function jsonResponse($status, $code, $html, $message = null) {
        $this->getResponse()
                ->setHeader('Content-Type', 'application/json')
                ->setHttpResponseCode($code)
                ->setBody(Zend_Json::encode(array("status" => $status, "html" => $html, "message" => $message)))
                ->sendResponse();
        exit;
    }
    
    /**
     * This method is used to validate the login 
     * Created By : Bidhan Chandra
     * Date : 27 Dec,2013
     * @param void
     * @return void
     */
    public function oauthAction() {
        $oauthNS = new Zend_Session_Namespace('oauthNS');
        if ($this->getRequest()->getParam('method') && $this->getRequest()->getParam('method') == 'popup') {
            $oauthNS->popup = true;
        }
        $result = $this->_auth->authenticate($this->_adapter);
        if ($result->isValid()) {
            $this->_helper->flashMessenger->addMessage(array('success' => 'Login was successful'));
            $this->_auth->getStorage()->write(array("identity" => $result->getIdentity(), "user" => new Josh_Auth_User($this->_type, $result->getMessages())));
        } else {
            $errorMessage = $result->getMessages();
            $this->_helper->flashMessenger->addMessage(array('error' => $errorMessage['error']));
        }

        if (isset($oauthNS->popup) && $oauthNS->popup == true) {
            unset($oauthNS->popup);
            echo $this->view->partial('partials/oauthClose.phtml');
        } else {
            $this->_redirect('/');
        }
    }
    
    /**
     * This method is used to show popup box after login
     * Created By : Bidhan Chandra
     * Date : 27 Dec,2013
     * @param void
     * @return void
     */
    public function ajaxAction() {
        $result = $this->_auth->authenticate($this->_adapter);
        if ($result->isValid()) {
            $this->_auth->getStorage()->write(array("identity" => $result->getIdentity(), "user" => new Josh_Auth_User($this->_type, $result->getMessages())));

            $ident = $this->_auth->getIdentity();

            $loggedIn = $this->view->partial('partials/userLoggedIn.phtml', array('userObj' => $ident['user']));
            $alert = $this->view->partial('partials/alert.phtml', array('alert' => 'Successful Login', 'alertClass' => 'alert-success'));

            $html = array("#userButton" => $loggedIn, "alert" => $alert);
            $this->jsonResponse('success', 200, $html);
        } else {
            $errorMessage = $result->getMessages();
            $alert = $this->view->partial('partials/alert.phtml', array('alert' => $errorMessage['error'], 'alertClass' => 'alert-error'));

            $html = array("alert" => $alert);
            $this->jsonResponse('error', 401, $html, $errorMessage['error']);
        }
    }

}
