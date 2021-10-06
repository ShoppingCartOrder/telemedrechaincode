<?php

/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage Helper
 */
class Mylib_Helper_View_GetCanonicalUrl extends Zend_View_Helper_Abstract
{
    /**
     * Check file name
     * @return extension
     */
    
    public function GetCanonicalUrl()
    {
			$request = Zend_Controller_Front::getInstance()->getRequest();
			$uri = rtrim(WWW_ROOT,'/').$request->getRequestUri(); 
			//$uri = $this->userAgent()->getServerValue('request_uri');		 
			$uri = rtrim($uri, "/") ; 
			if(strpos($uri,'?')>0){
				$uriExplode = explode('?',$uri);
				$uri = rtrim($uriExplode[0],'/');
			}
                        $params = $request->getParams();
                        if(!empty($params['page']) && $params['page'] > 1) {
                            $uri .="?page=" .$params['page'];
                        }
                        
			
       return '<link rel="canonical" href="'.strtolower($uri).'" />';
        
    }
}