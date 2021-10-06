<?php

/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage Helper
 */
class Mylib_Helper_View_CalculateRemainTime extends Zend_View_Helper_Abstract
{
    /**
     * Calculate remain time
     * @return extension
     */
    
    public function calculateRemainTime($postedTime)
    {
        if(!empty($postedTime)){
			$currenttime = date('Y-m-d H:i:s');
			$todaytime = strtotime($currenttime);
			$reviews_time = $postedTime;
			if($reviews_time != "0000-00-00 00:00:00"){
				strtotime($reviews_time);  
				$delta_time = $todaytime - strtotime($reviews_time);
				$hours =  floor(($delta_time)/3600);
				$msg = 'hours ago';
				$msg1 = 'Days ago';
				$msg2 = 'Few minutes ago';
				if($hours < 1){
					echo $msg2;
				} else if($hours <= 24){
					echo $hours . " " . $msg;
				} else {
					$days = round($hours/24);
					echo $days . " " . $msg1;
				}
			} else {
				echo '1' . " Day ago";
			}
		}
    }

}