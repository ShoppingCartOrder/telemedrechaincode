<?php

/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage Helper
 */
class Mylib_Helper_View_SetVendorCategory extends Zend_View_Helper_Abstract
{
    /**
     * Calculate remain time
     * @return extension
     */
    
    public function setVendorCategory(){
        $vendorCategoryRecord = '';
        $vendorCategoryRecord = array(
            array("id" => 3, "category" => "Banquets"), 
            array("id" => 6, "category" => "Caterers"), 
            array("id" => 45, "category" => "Makeup Artists"), 
            array("id" => 27, "category" => "Photographers and Videographers"), 
            array("id" => 36, "category" => "Wedding Planners")
            );
        return json_encode($vendorCategoryRecord);
    }

}