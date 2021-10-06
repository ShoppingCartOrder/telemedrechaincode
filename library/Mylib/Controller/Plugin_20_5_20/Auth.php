<?php

/**
 * This is Mylib_Controller_Plugin_Auth class. This class is a plugin
 *  Class plugin is used to authorize the users
 * @author Tech Lead
 * @package Zend_Controller_Plugin_Abstract
 * @subpackage Mylib_Controller_Plugin_Auth
 */
//require_once 'Vivahaayojan/Acl.php';

class Mylib_Controller_Plugin_Auth extends Mylib_Controller_Plugin_Base
{

    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {


        $params = $request->getParams();
        
        //dd($request->getUserParams());
        //dd(get_class_methods(get_class($router)));
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);

        if (isset($params['error_handler']->exception) && $config->exception->display == 1) {
            return true;
        }
        $authObject = Zend_Auth::getInstance();

        if ($this->isRoute('admin')) {
            $userNamespace = new Zend_Session_Namespace('my');
            $request->setParam('admin', true);
            
        }else if ($this->isRoute('doctor')) {
            $userNamespace = new Zend_Session_Namespace('mydoctor');
            $request->setParam('doctor', true);
            
        }else if ($this->isRoute('hospital')) {
            $userNamespace = new Zend_Session_Namespace('myhospital');
            $request->setParam('hospital', true);
            
        }else if ($this->isRoute('patient')) {
            $userNamespace = new Zend_Session_Namespace('mypatient');
            $request->setParam('patient', true);
        } else {
            $userNamespace = new Zend_Session_Namespace('userNamespace');
        }

        $userData = $userNamespace->storage;
        //dd($userData);

        $objRedirector = new Zend_Controller_Action_Helper_Redirector();
        $objCntAct = new Application_Model_DbTable_Acl();
        
        $alpha = new Zend_Filter_Alpha();
        //dd($alpha -> filter($request->getControllerName()));
        $contActparams['condition']['module'] = $alpha -> filter($request->getModuleName());
        $contActparams['condition']['controller'] = $request->getControllerName();
        $contActparams['condition']['action'] = $request->getActionName();
        $contActparams['condition']['extra_params'] = $alpha -> filter($this->getExtraParams($request->getUserParams()));
        //dd($params);
        
        //$controllerActions = $objCntAct->fetchControllerAction($contActparams);
        /*try{
                $controllerActions = $objCntAct->fetchControllerAction($contActparams);
        }catch (Zend_Db_Adapter_Exception $e) {
                // perhaps a failed login credential, or perhaps the RDBMS is not running
                echo 'Caught exception: ',$e->getMessage(),'\n';
        } 	catch(Exception $e){
                echo 'Caught exception: ',$e->getMessage(),'\n';
        }
        
        if (!empty($controllerActions[0])) {
            $controllerActions = $controllerActions[0];
        }
            
        
        if (empty($controllerActions))
            return true;
        if (!empty($controllerActions['guest_access']) || empty($controllerActions['check_permission']))
            return true;
        */    
        if ($this->isRoute('admin')) {
            return true;
            if ($this->isAllowedAdminWithOutLogin($contActparams)) {

                if ($authObject->hasIdentity() && !empty($userData) && $userData->user_role_type == BACK_END_ROLE) {
                    $objRedirector->gotoUrl($config->site->HOSTPATH . 'admin/dashboard');
                }
                return true;
            }
            if (!$authObject->hasIdentity() || $userData->user_role_type != BACK_END_ROLE) {
                $objRedirector->gotoUrl($config->site->HOSTPATH . 'admin');
            }
            //echo $userData->rolePermissions['is_super_admin']; die;
            if (!empty($userData->rolePermissions['is_super_admin'])) {
                return true;
            }
            if (!$userData->ACL->isAllowed($userData->roleId, $controllerActions['resource_id'], $controllerActions['privilleges_id'])) {

                $objRedirector->gotoUrl($config->site->HOSTPATH . 'admin/index/unauthorize');
                exit();
            } 
            return true;
            
        } else if ($this->isRoute('doctor')) {
            return true;
            if ($this->isAllowedDoctorWithOutLogin($contActparams)) {

                if ($authObject->hasIdentity() && !empty($userData) && $userData->user_role_type == BACK_END_ROLE) {
                    $objRedirector->gotoUrl($config->site->HOSTPATH . 'doctor/dashboard');
                }
                return true;
            }
            
            if (!$authObject->hasIdentity() || $userData->user_role_type != BACK_END_ROLE) {
                $objRedirector->gotoUrl($config->site->HOSTPATH . 'doctor');
            }
            //echo $userData->rolePermissions['is_super_admin']; die;
           /* if (!empty($userData->rolePermissions['is_super_admin'])) {
                return true;
            } 
            if (!$userData->ACL->isAllowed($userData->roleId, $controllerActions['resource_id'], $controllerActions['privilleges_id'])) {

                $objRedirector->gotoUrl($config->site->HOSTPATH . 'admin/index/unauthorize');
                exit();
            } */
            return true;
        } else if ($this->isRoute('hospital')) {
            return true;            
        }else if ($this->isRoute('patient')) {
            return true;           
        } else {
            return true;
        }
    }

    protected function getExtraParams($params = array())
    {
        $extraPrm = "";

        $ignoreParams = array('module', 'controller', 'action');
        foreach ($params as $k => $prm) {
            if (in_array($k, $ignoreParams))
                continue;
            $extraPrm .= $k . ",";
        }
        $extraPrm = trim($extraPrm, ",");
        if($extraPrm == 'admin'){
            $extraPrm = '';
        }
        return $extraPrm;
    }

}

?>