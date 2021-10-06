<?php
/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage Helper
 */

class Mylib_Helper_View_CalculateChecklistTaskDone extends Zend_View_Helper_Abstract
{
    
    
     /**
     * Calculate budget payment
     * @return extension
     */

    public function CalculateChecklistTaskDone($userCatId,$catId)
    {
        $this->_checklistResource = new Application_Model_DbTable_Checklist();
        $taskTotal = $this->_checklistResource->calculateChecklistCategoryTask($userCatId,$catId);
        return $taskTotal;
    }
}


?>