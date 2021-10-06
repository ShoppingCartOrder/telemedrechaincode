<?php
/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage Helper
 */
class Mylib_Helper_View_getBudgetSummary extends Zend_View_Helper_Abstract
{
    /**
     * Calculate budget summary
     * @return array
     */
    
    public function getBudgetSummary($userId = null)
    {
        
	try{   
            if($userId){
                $this->_budgetResource = new Application_Model_DbTable_Budget();            
                $summaryResult = $this->_budgetResource->getBudgetSummary();
                return $summaryResult;
            }
        }catch(Exception $e){            
            echo 'Caught exception: ',$e->getMessage(),'\n';
        } 
    }
}

?>