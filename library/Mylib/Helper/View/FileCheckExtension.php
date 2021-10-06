<?php

/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage Helper
 */
class Mylib_Helper_View_FileCheckExtension extends Zend_View_Helper_Abstract
{
    /**
     * Check file name
     * @return extension
     */
    
    public function FileCheckExtension($fileName)
    {
        global $IMAGE_FILE_EXTENSION; 
        global $DOC_FILE_EXTENSION; 
                
        $ext = pathinfo($fileName);   
        if(isset($ext['extension'])){
        if(in_array($ext['extension'],$IMAGE_FILE_EXTENSION)){
            return "image";
        }elseif (in_array($ext['extension'],$DOC_FILE_EXTENSION)) {
            return "doc";
        }
        }
        
    }
}