<?php

require_once ('library/Zend/Controller/Action.php');

class My_MyController extends Zend_Controller_Action {

 public function __construct() {
     
        $this->_auth = Zend_Auth::getInstance();
         if ($this->_auth->hasIdentity()) {
            $authData = $this->_auth->getIdentity();
            print_r($authData); exit;
         }
        require_once('My/Acl.php');
        $acl = new My_Acl();
        $this->_acl = $acl;

    }
}

?>