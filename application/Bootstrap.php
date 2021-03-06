<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    
    
    protected function _initZendPluginLoaderCache()
    {
       /*
        $cache = $this->bootstrap('cachemanager')->getResource('cachemanager')->getCache('default');
        if ($cache->getOption('caching')) {
            $classFileIncCache = APPLICATION_PATH . '/data/cache/pluginLoaderCache.php';
            if (file_exists($classFileIncCache)) {
                include_once $classFileIncCache;
            }
            Zend_Loader_PluginLoader::setIncludeFileCache($classFileIncCache);
        }*/
    }
    protected function _initResource()
    {
        $loader = new Zend_Loader_Autoloader_Resource(array(
            'basePath' => APPLICATION_PATH,
            'namespace' => 'Application',
        ));

        $loader->addResourceType('model', 'models', 'Model');
        //Zend_Session::start();
        require_once 'My/Acl.php';
        Zend_Controller_Action_HelperBroker::addPath(APPLICATION_PATH . '/controllers/helpers');
    }

    /**
     * Autoload for Controller to Specify Action Helper...
     * Created By : Tech Lead
     * Date : 19 Dec,2013
     * @param	 void
     * @return void
     */
    protected function _initRegistry()
    {

        $tables = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application_tables.ini', 'tables', true);

        Zend_Registry::set("tables", $tables);
        $tablespk = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application_tables.ini', 'tablespk', true);
        Zend_Registry::set("tablespk", $tablespk);

        $appRegions = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application_regions.ini', 'Regions_Controller', true);
        Zend_Registry::set("Regions_Controller", $appRegions);
    }

    /**
     * Autoload for Controller to load plugins
     * Created By : Tech Lead
     * Date : 19 Dec,2013
     * @param	 void
     * @return void
     */
    protected function _initPlugins()
    {
        $front = Zend_Controller_Front::getInstance();
        $front->registerPlugin(new Mylib_Controller_Plugin_Auth());
        $front->registerPlugin(new Mylib_Controller_Plugin_Routes());
    }

    /**
     * Autoload for Controller to load plugins
     * Created By : Tech Lead
     * Date : 19 Dec,2013
     * @param	 void
     * @return void
     */
//    protected function _initControllers()
//    {
//         
//        $front = Zend_Controller_Front::getInstance();
//        $front->throwExceptions(true)
//                ->setControllerDirectory(
//                        array(
//                            'admin'=>APPLICATION_PATH.'/controller/admin/',
//                            'default'=>APPLICATION_PATH.'/controller/'
//                        )
//                        );
//        
//        return $front;
//        
//    }

    protected function _initRoute()
    {
        $frontController = Zend_Controller_Front::getInstance();
        $router = $frontController->getRouter();

        $router->addRoute(
            'admin', new Zend_Controller_Router_Route('/admin/:action/*', array(
            'controller' => 'Admin_Index',
            'action' => 'index'
                ))
        );
        
        $router->addRoute(
            'doctor', new Zend_Controller_Router_Route('/doctor/:action/*', array(
            'controller' => 'Doctor_Index',
            'action' => 'index'
                ))
        );
        $router->addRoute(
            'patient', new Zend_Controller_Router_Route('/patient/:action/*', array(
            'controller' => 'Patient_Index',
            'action' => 'index'
                ))
        );
        $router->addRoute(
            'hospital', new Zend_Controller_Router_Route('/hospital/:action/*', array(
            'controller' => 'Hospital_Index',
            'action' => 'index'
                ))
        );
        
        $router->addRoute(
            'laboratory', new Zend_Controller_Router_Route('/laboratory/:action/*', array(
            'controller' => 'Laboratory_Index',
            'action' => 'index'
                ))
        );
        $router->addRoute(
            'nurse', new Zend_Controller_Router_Route('/nurse/:action/*', array(
            'controller' => 'Nurse_Index',
            'action' => 'index'
                ))
        );
        
        /*******************code for routing***************************/
        $configRoute = new Zend_Config_Ini(APPLICATION_PATH . '/configs/routes.ini', 'routes', true);
        $front = Zend_Controller_Front::getInstance();
        $router = Zend_Controller_Front::getInstance()->getRouter();        
        $router->addConfig($configRoute, 'routes');
        $front->setRouter($router);
        /*******************code for routing***************************/
                        
	/*$router->addRoute(
            'myWeddingVendors', new Zend_Controller_Router_Route_Static('wedding-vendors', array(
            'controller' => 'search',
            'action' => 'categories'
                ))
        );*/
        $adminCont = new Zend_Config_Ini(APPLICATION_PATH . '/configs/admin_controllers.ini', 'admin_controllers', true);
        $adminCont = $adminCont->toArray();

        foreach ($adminCont as $k => $v) {
            $rt = "/".str_replace("_", "/", strtolower($v))."/:action/*";
            
            $router->addRoute(
                    $k, new Zend_Controller_Router_Route($rt, array(
                'controller' => $v,
                'action' => 'index'
                    ))
            );
        }
        
        $doctorCont = new Zend_Config_Ini(APPLICATION_PATH . '/configs/doctor_controllers.ini', 'doctor_controllers', true);
        $doctorCont = $doctorCont->toArray();

        foreach ($doctorCont as $k => $v) {
            $rt = "/".str_replace("_", "/", strtolower($v))."/:action/*";
            
            $router->addRoute(
                    $k, new Zend_Controller_Router_Route($rt, array(
                'controller' => $v,
                'action' => 'index'
                    ))
            );
        }
        
        $patientCont = new Zend_Config_Ini(APPLICATION_PATH . '/configs/patient_controllers.ini', 'patient_controllers', true);
        $patientCont = $patientCont->toArray();

        foreach ($patientCont as $k => $v) {
            $rt = "/".str_replace("_", "/", strtolower($v))."/:action/*";
            
            $router->addRoute(
                    $k, new Zend_Controller_Router_Route($rt, array(
                'controller' => $v,
                'action' => 'index'
                    ))
            );
        }
        $hospitalCont = new Zend_Config_Ini(APPLICATION_PATH . '/configs/hospital_controllers.ini', 'hospital_controllers', true);
        $hospitalCont = $hospitalCont->toArray();

        foreach ($hospitalCont as $k => $v) {
            $rt = "/".str_replace("_", "/", strtolower($v))."/:action/*";
            
            $router->addRoute(
                    $k, new Zend_Controller_Router_Route($rt, array(
                'controller' => $v,
                'action' => 'index'
                    ))
            );
        }
        
        $laboratoryCont = new Zend_Config_Ini(APPLICATION_PATH . '/configs/laboratory_controllers.ini', 'laboratory_controllers', true);
        $laboratoryCont = $laboratoryCont->toArray();

        foreach ($laboratoryCont as $k => $v) {
            $rt = "/".str_replace("_", "/", strtolower($v))."/:action/*";
            
            $router->addRoute(
                    $k, new Zend_Controller_Router_Route($rt, array(
                'controller' => $v,
                'action' => 'index'
                    ))
            );
        }
        
        $nurseCont = new Zend_Config_Ini(APPLICATION_PATH . '/configs/nurse_controllers.ini', 'nurse_controllers', true);
        $nurseCont = $nurseCont->toArray();

        foreach ($nurseCont as $k => $v) {
            $rt = "/".str_replace("_", "/", strtolower($v))."/:action/*";
            
            $router->addRoute(
                    $k, new Zend_Controller_Router_Route($rt, array(
                'controller' => $v,
                'action' => 'index'
                    ))
            );
        }
        
        return $router;
    }

  
/*    
public function _initCache () {
 
    $frontendOptions = array('lifetime' => 60);

// backend options
$backendOptions = array(
    'cache_dir' => APPLICATION_PATH.'/cache' // Directory where to put the cache files
);

// make object
$cache = Zend_Cache::factory('Output',
                             'File',
                             $frontendOptions,
                             $backendOptions);

// make an id
$cacheID='user1';

// everything before this is not cached
if (!($cache->start($cacheID))) {
    //begin cache
    //end cache
    $cache->end();
}
}*/
	

}

?>
