<?php
/**
 * This is CreateStringIndex class. This class will
 * replace space with '-' and convert into lower case.
 *
 * @author Bidhan
 * @package Zend_View_Helper_Abstract
 * @subpackage Mylib_Helper_View_CreateStringIndex
 */

class Mylib_Helper_View_CreateStringIndex extends Zend_View_Helper_Abstract{
    
    public function createStringIndex($srchTxt = '',$rplcText = '',$txt) {
        $fnlStr = '';           
        if($txt != ''){            
            $fnlStr =  strtolower(str_replace($srchTxt,$rplcText,trim($txt)));
        }
        return $fnlStr;
        
    }
    
}
?>