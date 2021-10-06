<?php
/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage Helper
 */
class Mylib_Helper_View_getGuestSummary extends Zend_View_Helper_Abstract
{
    /**
     * Calculate budget summary
     * @return array
     */
    
    public function getGuestSummary($userId = null)
    {
       
	try{ 
           
            if($userId){
                $this->_guestResource = new Application_Model_DbTable_Guests();            
                $summaryResults = $this->_guestResource->getGuestSummary();
                
                $resultsArr = array();
                if(!empty($summaryResults)){
                    $totalGuest = 0;
                    $totalEvents = $summaryResults['eventCnt'];
                    unset($summaryResults['eventCnt']);
                    $totalInvited = 0;                    
                    foreach($summaryResults as $key=>$summaryResult){
                        $totalGuest+= $summaryResult['total_guests']+$summaryResult['no_of_guest'];
                        //$totalEvents+= $summaryResult['total_events'];
                        if($summaryResult['email_status'] == 1){
                           if($summaryResult['total_guests'] > 0){ 
                                $totalInvited += $summaryResult['total_guests']; 
                           }
                        }
                    }
                    $resultsArr['total_guests'] = $totalGuest;
                    $resultsArr['total_events'] = $totalEvents;
                    $resultsArr['total_invited'] = $totalInvited;
                    
                }                
                return $resultsArr;
            }
        }catch(Exception $e){            
            echo 'Caught exception: ',$e->getMessage(),'\n';
        } 
    }
}

?>