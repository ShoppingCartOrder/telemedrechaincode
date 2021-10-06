<?php

// Define path to application directory
defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define public directory
defined('PUBLIC_PATH') || define('PUBLIC_PATH', realpath(dirname(__FILE__)));
defined('LIB_PATH') || define('LIB_PATH', realpath(dirname(__FILE__) . '/../library'));


// Define application environment
defined('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

define('SYSTEM_PATH', dirname(__FILE__));
// Ensure library/ is on include_path
//set_include_path(implode(PATH_SEPARATOR, array(
//    realpath(APPLICATION_PATH . '/../library'),
//    get_include_path(),
//)));

set_include_path(get_include_path() . PATH_SEPARATOR . SYSTEM_PATH . DIRECTORY_SEPARATOR . '../library' . PATH_SEPARATOR . DIRECTORY_SEPARATOR . '../library/Mylib' );

require_once 'Zend/Loader/Autoloader.php';

$loader = Zend_Loader_Autoloader::getInstance();
$loader->registerNamespace('Mylib_');



/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
        APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini'
);

$options = $application->getOptions();
defined('HOSTPATH') || define('HOSTPATH', $options['site']['HOSTPATH']);
include_once("constants.php");
if ($options['exception']['notice']) {
    error_reporting(E_ALL );
} else {
    error_reporting(E_ALL && ~E_STRICT && ~E_NOTICE);
}

function dd($data, $dontDie=false)
{
    echo"<pre>";
    print_r($data);
    if(!$dontDie) {
        die;
    }
}
if(isset($_SERVER['HTTPS'])){
    define('HTTP','https://');
}else{
    define('HTTP','http://');    
}


function imgUrl($url, $arr = null)
{
    return $url;
}

$application->bootstrap()->run();
/*
try {
   $application->bootstrap()->run();
} catch(Exception $e){            
            echo 'Caught exception: ',$e->getMessage(),'\n';
         //   include('db_error.php');
    //header("location:http://local.vivahaayojan-ui.com/db_error.php");
        }*/

