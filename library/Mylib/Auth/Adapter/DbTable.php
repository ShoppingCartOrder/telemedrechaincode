<?php
/**
* This document defines Mylib_Auth_Adapter_DbTable class
* This is BaseController class. It defines all attributes and behaviour that is common to whole application.
* @author Tech Lead
* @package Mylib_Library Models
* @subpackage Zend_Auth_Adapter_DbTable
*/

class Mylib_Auth_Adapter_DbTable extends Zend_Auth_Adapter_DbTable
{
   
	/**
	 * create select query using zend authenticateCreateSelect function
	  * Created By : Tech Lead
	 * Date : 19 Dec,2013
	 * @param	$value string, $context as array
	 * @return boolen
	 */
    
    protected $_userRoleType = null;
    
    protected function _authenticateCreateSelect()
    {
    	$dbSelect = parent::_authenticateCreateSelect();

    	$dbSelect->where('status = ?',1);
    	$dbSelect->where('user_role_type = ?',$this->_userRoleType);
        		
        return $dbSelect;
    }
    
    /**
     * setCredentialColumn() - set the column name to be used as the user_role_type column
     *
     * @param  string $userRoleType
     * @return Zend_Auth_Adapter_DbTable Provides a fluent interface
     */
    public function setUserRoleType($userRoleType)
    {
        $this->_userRoleType = $userRoleType;
        return $this;
    }
}
