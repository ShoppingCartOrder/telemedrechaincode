<?php

/**
 * This is Mylib_Controller_Plugin_Auth class. This class is a plugin
 *  Class plugin is used to authorize the users
 * @author Tech Lead
 * @package Zend_Controller_Plugin_Abstract
 * @subpackage Mylib_Controller_Plugin_Auth
 */


class Mylib_Controller_Plugin_Base extends Zend_Controller_Plugin_Abstract
{

    public function isRoute($route = '')
    {
        $frontController = Zend_Controller_Front::getInstance();
        $requestUri = $frontController->getRequest()->getRequestUri();
        if(empty($route)) {
            return true;
        }
        if(strpos(strtolower($requestUri), '/'.strtolower($route))===0) {
            return true;
        } else {
            return false;
        }
    }
    
    public function isAllowedAdminWithOutLogin($contActparams = array(), $action = '', $controller = '')
    {
        global $withoutLoginAdmin;
        
        $controller = '';
        
        if(!empty($contActparams['params']['controller']) && strpos(strtolower($contActparams['params']['controller']), 'admin_')===0){
            dd(12);
            $controller = str_replace('admin_', "", strtolower($contActparams['params']['controller']));
        }
       dd($contActparams);
        if(!empty($withoutLoginAdmin[$controller]) && is_array($withoutLoginAdmin[$controller]) && in_array(strtolower($contActparams['params']['action']), $withoutLoginAdmin[$controller])) {
            
            return true;
        }
        
       
    }
    
    public function isAllowedDoctorWithOutLogin($contActparams = array(), $action = '', $controller = '')
    {
        global $withoutLoginAdmin;
        
        $controller = '';
        
        if(!empty($contActparams['params']['controller']) && strpos(strtolower($contActparams['params']['controller']), 'doctor_')===0){
            $controller = str_replace('doctor_', "", strtolower($contActparams['params']['controller']));
        }
       
        if(!empty($withoutLoginAdmin[$controller]) && is_array($withoutLoginAdmin[$controller]) && in_array(strtolower($contActparams['params']['action']), $withoutLoginAdmin[$controller])) {
            
            return true;
        }
        
       
    }
    
    public function isAllowedHospitalWithOutLogin($contActparams = array(), $action = '', $controller = '')
    {
        global $withoutLoginAdmin;
        
        $controller = '';
        
        if(!empty($contActparams['params']['controller']) && strpos(strtolower($contActparams['params']['controller']), 'hospital_')===0){
            $controller = str_replace('hospital_', "", strtolower($contActparams['params']['controller']));
        }
       
        if(!empty($withoutLoginAdmin[$controller]) && is_array($withoutLoginAdmin[$controller]) && in_array(strtolower($contActparams['params']['action']), $withoutLoginAdmin[$controller])) {
            
            return true;
        }
        
       
    }
    
    public function isAllowedLaboratoryWithOutLogin($contActparams = array(), $action = '', $controller = '')
    {
        global $withoutLoginAdmin;
        
        $controller = '';
        
        if(!empty($contActparams['params']['controller']) && strpos(strtolower($contActparams['params']['controller']), 'laboratory_')===0){
            $controller = str_replace('laboratory_', "", strtolower($contActparams['params']['controller']));
        }
       
        if(!empty($withoutLoginAdmin[$controller]) && is_array($withoutLoginAdmin[$controller]) && in_array(strtolower($contActparams['params']['action']), $withoutLoginAdmin[$controller])) {
            
            return true;
        }
        
       
    }
}

?>