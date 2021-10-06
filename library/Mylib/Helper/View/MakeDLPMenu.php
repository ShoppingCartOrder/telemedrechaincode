<?php

/**
 * This document defines Action_Helper class
 *
 * @package Zend_View_Helper_Abstract
 * @copyright Copyright 2010-2011 Vivahaayojan
 */

/**
 * This is My_View_Helper_FormatDate class. This class will
 * execute all the request to change date format.
 *
 * @author Tech Lead
 * @package Zend_View_Helper_Abstract
 * @subpackage My_View_Helper_FormatDate
 */
class Mylib_Helper_View_MakeDLPMenu extends Zend_View_Helper_Abstract
{

    /**
     * This function will be use to format date given by user into output format specified.
     *
     * @param string $input_date Input date string given by user
     * @param string $input_date_format Input date string format given by user. Note: Format parameter wiil based on Zend_Date defined constants.
     * @param string $ouput_date_format Output date format specified by user. Note: Format parameter wiil based on Zend_Date defined constants.
     *
     * @return string Date string in output format.
     */
    public function makeDLPMenu($dlpMenus, $vendorFullUrl,$dlpMenusData,$tabUrl = null)
    {
        if($tabUrl!= ''){
            $tabMenuType = $tabUrl;
        }else{
            $tabMenuType = 'basic_info';
        }
              
        $strTab = '<ul class="cd-tabs-navigation">';
       
        $detailData = '';
        $detailDataValue = '';
        foreach ($dlpMenus as $key => $menu) {
            if (isset($dlpMenusData[$menu['menu_type']])) {
                $detailData = $dlpMenusData[$menu['menu_type']];
            }
        }
        if (isset($detailData['extra_fields'])) {
            foreach ($detailData['extra_fields'] as $key => $extraFields) { 
                $detailDataValue = $extraFields['values'];
            }
        }
       
       foreach ($dlpMenus as $k => $menu) {
           
          
            $menuName = strtolower($menu['menu_name']);
            $menuType = strtolower(str_replace('_','-',trim($menu['menu_type'])));
                                    
            $hrefUrl = ($k > 0) ? $vendorFullUrl."/".$menuType:$vendorFullUrl;
            
            //$class = ($k === 0) ? "class='selected'" : '';
            $class = ($tabMenuType === trim($menu['menu_type'])) ? "class='selected'" : '';
            
            if (strpos($menu['menu_type'], 'extra_menu_') !== false && !empty($detailDataValue)) {
                $xtraMenuURL = str_replace(' ','-',trim($menuName));                
                $hrefUrl = $vendorFullUrl."/".$xtraMenuURL;
            }else if(strpos($menu['menu_type'], 'extra_menu_') !== false && empty($detailDataValue)){
               
                $hrefUrl = '';
            }
            
            if($hrefUrl!= ''){
                $strTab .= '<li>';
                
                $strTab .= '<a '.$class.' data-content="'.$menu['menu_type'].'" href="'.$hrefUrl.'">'.$menu['menu_name'].'</a>';
                 
                $strTab .= '</li>';
            }
       }
       
        
       $strTab .= '</ul>';
       return $strTab;
    }

}
