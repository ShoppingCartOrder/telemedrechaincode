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
class My_Action_Helper_GetDateDiffrence extends Zend_Controller_Action_Helper_Abstract
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
	public function direct($t1,$t2='')
	{
		if(empty($t2))
		{	
			 $t2	=	date('Y-m-d H:i:s'); 
		}
		
		
		if (($t1 = strtotime($t1)) === false) die ("Input string 1 unrecognized");
		if (($t2 = strtotime($t2)) === false) die ("Input string 2 unrecognized");
		
		if($t1 < $t2) 
		    {
		    $d1 = getdate($t2);
		    $d2 = getdate($t1); 
		    }
		else
		    {
		    $d1 = getdate($t1);
		    $d2 = getdate($t2);
		    }
		        
		foreach ($d1 as $k => $v) 
		    {
		    $d1[$k]-=  $d2[$k] ;        
		     }
		
		
		if ($d1['seconds'] < 0 ) 
		    {
		    $d1['seconds'] +=60 ;
		    $d1['minutes'] -=1;
		    }
		
		if ($d1['minutes'] < 0 ) 
		    {
		    $d1['minutes'] +=60 ;
		    $d1['hours'] -=1;
		    }
		
		if ($d1['hours'] < 0 ) 
		    {
		    $d1['hours'] +=24 ;
		    $d1['yday'] -=1;
		    }
		
		if ($d1['yday'] < 0 ) 
		    {
		    $d1['yday'] +=365 ;
		    $d1['year'] -=1;
		    }	
		
		
		return ($d1);
	}
}