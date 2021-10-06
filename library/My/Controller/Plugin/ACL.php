<?php

 	class My_Controller_Plugin_ACL extends Zend_Controller_Plugin_Abstract {
 	
 		protected $strDefaultRole = 'guest';
	
 		public function preDispatch(Zend_Controller_Request_Abstract $objRequest) {
 			
 			$objAuth = Zend_Auth::getInstance();
 			$bolClearACL = false;

	        if( $objAuth->hasIdentity() ){
	        	
	            $arrUser = $objAuth->getIdentity();
				
				define('CURRENT_ACCOUNT_ID',$arrUser['accountId']);
				define('CURRENT_ACCOUNT_NAME',$arrUser['account']);
				
	            $objSession = new Zend_Session_Namespace('My_Control_Panel');
	            
	            if($objSession->clearACL) {
	            	
	            	$bolClearACL = true;
	            	
	            	unset($objSession->clearACL);
	            }
	            
	            $objAcl = My_ACL_Factory::get($objAuth,$bolClearACL);

	            if( ! $objAcl->isAllowed($arrUser['role'], $objRequest->getModuleName() .'::' .$objRequest->getControllerName() .'::' .$objRequest->getActionName())) {
	            	
	            	return Zend_Controller_Action_HelperBroker::getStaticHelper('redirector')->setGotoRoute(array(),"noauth");	
	            
	            }
	
	        } else {
	        	
	            $objAcl = My_ACL_Factory::get($objAuth,$bolClearACL);
	            
	            if( ! $objAcl->isAllowed($this->strDefaultRole, $objRequest->getModuleName() .'::' .$objRequest->getControllerName() .'::' .$objRequest->getActionName())) {
	            	
	            	return Zend_Controller_Action_HelperBroker::getStaticHelper('redirector')->setGotoRoute(array(),"login");	
	            
	            } 
	        }
 		}
 	}
