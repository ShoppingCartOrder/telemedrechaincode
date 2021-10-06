<?php
/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage Helper
 */

class Mylib_Helper_View_CalculateBudgetPayment extends Zend_View_Helper_Abstract
{
    
    
     /**
     * Calculate budget payment
     * @return extension
     */

    public function CalculateBudgetPayment($itemVal = array(),$userCatId)
    {
            
            $link = "";		
            $actualCost = $itemVal['budget_item_actual_cost'];
            $paidAmt = $itemVal['total_amount_paid'];           
            $itemId = $itemVal['user_item_id'];
            $displayTxt = 'Add Payment Details';
            if ($paidAmt > 0 && $actualCost >0) {
                $style ="float:right";                                 
                if ($paidAmt > $actualCost) {  
                    $style ="float:right;color:#FF0000;";
                    $displayTxt = '('.$paidAmt.')';                           
                }else if($paidAmt <= $actualCost){
                    $displayTxt = $paidAmt;
                }                  
                $link = '<a class="enqury small-link2" href="#" style="$style" onclick="wpBudgetPaymentModeForm('.$itemId.','.$userCatId.')">'.$displayTxt.'</a>';                                                                  
            } else if($paidAmt == 0 && $actualCost > 0){                                                                                                                                                        
                  $link = '<a class="enqury small-link2" href="#" style="$style" onclick="wpBudgetPaymentModeForm('.$itemId.','.$userCatId.')">'.$displayTxt.'</a>';                
            } else{
                $link = "--";    
            }        

       return $link;
        
    }
}


?>