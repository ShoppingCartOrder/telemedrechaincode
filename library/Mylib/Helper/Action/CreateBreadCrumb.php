<?php
/**
 * This document defines Action_Helper class
 *
 * @package Zend_View_Helper_Abstract
 * @copyright Copyright 2010-2011 Vivahaayojan 
 */

/**
 * This is My_Action_Helper_FormatDate class. This class will
 * execute all the request to change date format.
 *
 * @author Tech Lead
 * @package Zend_Controller_Action_Helper_Abstract
 * @subpackage My_Action_Helper_FormatDate
 * Calling action helper in controller: $this->_helper->FormatDate();
 */
class Mylib_Helper_Action_CreateBreadCrumb extends Zend_Controller_Action_Helper_Abstract
{
	/**
	 * This function will be use to format date given by user into output format specified.
	 *
	 * @param array $sourceArray Source array (in 2-D format) from which key => value array is to be created.
	 * @param string $keyColumn Key column name
	 * @param array $valueColumns Value columns name
	 * @param array $valueSeparator Separator in case of multiple value columns
	 *
	 * @return array Key => Value array
	 */
	public function getBreadCrumbDetails($vendorDet) {
            
                $breadCrum = array('id' => 0, 'name' => '', 'cityid' => 1, 'zoneid' => 0,
                    'metro_station_id' => 0, 'status' => 1, 'zone_name' => '');
                if (!empty($vendorDet['location'])) {
                    $breadCrum['id'] = $vendorDet['location'];
                }

                if (!empty($vendorDet['location_name'])) {
                    $breadCrum['name'] = $vendorDet['location_name'];
                }

                if (!empty($vendorDet['city'])) {
                    $breadCrum['cityid'] = $vendorDet['city'];
                }

                if (!empty($vendorDet['zone_id'])) {
                    $breadCrum['zoneid'] = $vendorDet['zone_id'];
                }

                if (!empty($vendorDet['zone_name'])) {
                    $breadCrum['zone_name'] = $vendorDet['zone_name'];
                }

                if (!empty($vendorDet['category_name'])) {
                    $breadCrum['category_name'] = $vendorDet['category_name'];
                }

                if (!empty($vendorDet['portfolioImage'])) {
                    $breadCrum['portfolioImage'] = 'portfolio';
                }

                if (isset($vendorDet['reviews'])) {
                    $breadCrum['reviews'] = $vendorDet['reviews'];
                }
                
                if (isset($vendorDet['map-direction'])) {
                    $breadCrum['map-direction'] = $vendorDet['map-direction'];
                }
                
                if (isset($vendorDet['deals'])) {
                    $breadCrum['deals'] = $vendorDet['deals'];
                }
                
                if (isset($vendorDet['menu-available'])) {
                    $breadCrum['menu-available'] = $vendorDet['menu-available'];
                }
                
                if (isset($vendorDet['virtual-tour'])) {
                    $breadCrum['virtual-tour'] = $vendorDet['virtual-tour'];
                }
                
                return $breadCrum;
    }
}