<?php
class Application_Model_DbTable_LoginLogoutDetails extends Zend_Db_Table_Abstract
{
    protected $_name = 'vy_login_logout_details ';
	public function addUser($data)
    {   
		$this->_name = 'vy_login_logout_details';
		return $this->getAdapter()->insert('vy_login_logout_details', $data);
    }
	
	
	
	
	   
	   
    public function updateLogoutTimeUser($sessionId=NULL)
    {   
	   
		$this->_name = 'vy_login_logout_details';
		$date = new Zend_Date();
        $logout_time = array('logout_time'=>$date->toString('YYYY-MM-dd HH:mm:ss'));
		$where['session_id =?'] = $sessionId;
		return $this->getAdapter()->update($this->_name,$logout_time, $where);
	}
        
     public function addAdminloginDetails($data)
    {   
		
		return $this->getAdapter()->insert('vy_admin_login_log', $data);
    }
    
     public function updateLogoutTimeAdminUser($arrLogOut = array())
    {   
	   //$date = new Zend_Date();
           //$logout_time = array('logout_time'=>$date->toString('yyyy-MM-dd HH:mm:ss'));
	   $where['session_id =?'] = session_id();
	   return $this->getAdapter()->update('vy_admin_login_log',$arrLogOut, $where);
	}
	
  
}     
?>
