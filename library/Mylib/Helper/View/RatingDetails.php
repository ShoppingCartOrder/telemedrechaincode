<?php

/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage Helper
 */
class Mylib_Helper_View_RatingDetails extends Zend_View_Helper_Abstract
{
    /**
     * Calculate remain time
     * @return extension
     */
    
    public function ratingDetails($rating)
    {
        $ratingsArr = array(0,1,2,3,4,5);
        $ratingData = array();
        if(!empty($rating)){
            $rating = ceil($rating);
	    if (in_array($rating, array(0,1,2,3,4,5))) {
                
                if ($rating == 1){
                    $ratingData['rating_cls'] = 'rating1';
                   $ratingData['rating_icon'] = 'sprite-icon-67';
                    //$ratingData['rating_icon'] = 'sprite-icon-69';
                }else if ($rating == 2){
                    $ratingData['rating_cls'] = 'rating2';
                    $ratingData['rating_icon'] = 'sprite-icon-672';
                    //$ratingData['rating_icon'] = 'sprite-icon-69';
                }else if ($rating == 3){
                    $ratingData['rating_cls'] = 'rating3';
                    $ratingData['rating_icon'] = 'sprite-icon-69';
                }else if ($rating == 4){
                    $ratingData['rating_cls'] = 'rating4';
                    $ratingData['rating_icon'] = 'sprite-icon-66';
                }else if ($rating == 5){
                    $ratingData['rating_cls'] = 'rating5';
                    $ratingData['rating_icon'] = 'sprite-icon-68';
                }else {
                    $ratingData['rating_cls'] = '0';
                    $ratingData['rating_icon'] = '';
                    
                }
                return $ratingData;
        }
		}
    }

}