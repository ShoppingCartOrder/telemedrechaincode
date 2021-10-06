<?php

/**
 * Frontcontroller plugin to assist the routes
 * Frontcontroller plugin to assist the routes
 * @author techlead
 * @package Zend_Controller_Plugin_Abstract
 * @subpackage Mylib_Controller_Plugin_Routes
 */
class Mylib_Controller_Plugin_Routes extends Mylib_Controller_Plugin_Base
{

    /**
     * Set the admin controller for the admin route
     * @param Zend_Controller_Request_Abstract $request
     * @todo Block all unauthorized access
     * Created By : techlead
     * Date : 17 jan,2014
     */
    public function routeShutdown(Zend_Controller_Request_Abstract $request)
    {
        global $allowedAdminIP;
        $params = $request->getParams();

        $controllerRegions = Zend_Registry::get("Regions_Controller")->toArray();
        $controller = $request->getControllerName();
        if (!$controller) {
            $request->setControllerName('index');
        }

        if ($this->isRoute('admin')) {
            $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
          
            if ($config->exception->applyAdminRestriction == 1 
                    && 
                    !in_array($request->getServer('REMOTE_ADDR'), $allowedAdminIP)) {
                die("Sorry you cannot access admin panel outside office");
            }
            Zend_Layout::startMvc(APPLICATION_PATH . "/layouts/scripts");
            $layout = Zend_Layout::getMvcInstance();
            $layout->setLayout('adminuser');
            
        }else if ($this->isRoute('doctor')) {
            
            $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
          //dd($config);
            if ($config->exception->applyAdminRestriction == 1 
                    && !in_array($request->getServer('REMOTE_ADDR'), $allowedAdminIP)) {
                die("Sorry you cannot access doctor panel outside office");
            }
            Zend_Layout::startMvc(APPLICATION_PATH . "/layouts/scripts");
            $layout = Zend_Layout::getMvcInstance();
            $layout->setLayout('doctor');
            
        }else if ($this->isRoute('hospital')) {
            
            $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
          //dd($config);
            if ($config->exception->applyAdminRestriction == 1 
                    && !in_array($request->getServer('REMOTE_ADDR'), $allowedAdminIP)) {
                die("Sorry you cannot access hospital panel outside office");
            }
            Zend_Layout::startMvc(APPLICATION_PATH . "/layouts/scripts");
            $layout = Zend_Layout::getMvcInstance();
            $layout->setLayout('hospital');
            
        }else if ($this->isRoute('patient')) {
            
            $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
          //dd($config);
            if ($config->exception->applyAdminRestriction == 1 
                    && !in_array($request->getServer('REMOTE_ADDR'), $allowedAdminIP)) {
                die("Sorry you cannot access doctor panel outside office");
            }
            Zend_Layout::startMvc(APPLICATION_PATH . "/layouts/scripts");
            $layout = Zend_Layout::getMvcInstance();
            $layout->setLayout('patient');
            
        }else if ($this->isRoute('laboratory')) {
            
            $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
          //dd($config);
            if ($config->exception->applyAdminRestriction == 1 
                    && !in_array($request->getServer('REMOTE_ADDR'), $allowedAdminIP)) {
                die("Sorry you cannot access doctor panel outside office");
            }
            Zend_Layout::startMvc(APPLICATION_PATH . "/layouts/scripts");
            $layout = Zend_Layout::getMvcInstance();
            $layout->setLayout('laboratory');
            
        } else {
            if (in_array($controller, $controllerRegions)) {
                $request->setControllerName('search');
                $request->setActionName('index');
            }
        }
    }

}

?>