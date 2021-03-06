<?php

/**
 * This document defines Application_Model_DbTable_Users class
 * This is an Users model class. It defines all attributes and behaviour that is used for Users section.
 * @author Bidhan Chandra
 * @package Vivahaayojan Models
 * @subpackage Application_Model_DbTable_Users
 */
class Application_Model_DbTable_Users extends Mylib_Model_BaseModel {

    /**
     * Defining class variable for users table name.
     */
    protected $_name = 'users';

    /**
     * This function is used to create new user.
     *
     * Created By : Bidhan Chandra
     * Date : 10 Jan,2014	
     * @param $params array by reference
     *
     * @return string.
     */
    public function addUser($data) {
        $instered = $this->insert($data);
        $lastInsertId = $this->getAdapter()->lastInsertId();
        return $lastInsertId;
    }

    /**
     * This function is used to get user data by matching email.
     *
     * Created By : Bidhan Chandra
     * Date : 10 Jan,2014	
     * @param $params array by reference
     *
     * @return array.
     */
    public function getUser($email) {
        $db = $this->getAdapter();
        $select = $db->select()
                ->from('users', '*')
                ->where('email = ?', $email);
        $value = $db->fetchAll($select);
        return $value;
    }

    /**
     * This function is used to verify user for changing password.
     *
     * Created By : Bidhan Chandra
     * Date : 10 Jan,2014	
     * @param $params array by reference
     *
     * @return array.
     */
    public function verifyuser($token, $email) {
        $db = $this->getAdapter();
        $select = $db->select()
                ->from('users', '*')
                ->where('token = ?', $token)
                ->where('email = ?', $email);
        $value = $db->fetchAll($select);
    }

    /**
     * This function is used to send forgot password email to user.
     *
     * Created By : Bidhan Chandra
     * Date : 10 Jan,2014	
     * @param $params array by reference
     *
     * @return bool.
     */
    function sendForgotPassMail($email, $token) {
        $dbdata = array('token' => $token, 'status' => 'active');
        $where['email =?'] = $email;
        return $this->getAdapter()->update($this->_name, $dbdata, $where);
    }

    /**
     * This function is used to get user deatil by matching token for forgot password.
     *
     * Created By : Bidhan Chandra
     * Date : 10 Jan,2014	
     * @param $params array by reference
     *
     * @return array.
     */
    public function checkToken($link) {
        $db = $this->getAdapter();
        $query = $db->select()
                ->from('users', '*')
                ->Where('token=?', $link);
        $select = $db->fetchAll($query);
        return $select;
    }

    /**
     * This function is used to get feedback from user.
     *
     * Created By : Bidhan Chandra
     * Date : 10 Jan,2014	
     * @param $params array by reference
     *
     * @return string.
     */
    public function sendfeedback($name, $email, $mobile, $comments, $date) {
        $db = $this->getAdapter();
        $data['name'] = $name;
        $data['email'] = $email;
        $data['mobile'] = $mobile;
        $data['comments'] = $comments;
        $data['date'] = $date;
        $data['times'] = "";
        $insert = $db->insert('vy_feedback_master', $data);
        if ($insert) {
            $sucess = 'submitted';
            return $sucess;
        }
    }

    /**
     * This function is used to get user information using id.
     *
     * Created By : Bidhan Chandra
     * Date : 10 Jan,2014	
     * @param $params array by reference
     *
     * @return array.
     */
    public function getAccountInformation($userId) {
        $db = $this->getAdapter();
        $query = $db->select()
                ->from('ww_site', '*')
                ->Where('user_id=?', $userId);
        $select = $db->fetchAll($query);
        return $select;
    }
    
    public function newPassword($userId,$userNewPassword,$email) {
        $db = $this->getAdapter();
	$statuses ='active';
	$tokens = '';
 	$changepassword = array('password' =>md5($userNewPassword),'status' =>$statuses,'token' =>$tokens);            
     	$result = $db->update('users', $changepassword,'id = '.$userId);
	return 1;	
    }
    
    public function updateUserDetail($dbdata, $data) {
        $where['id =?'] = $data;
        return $this->getAdapter()->update($this->_name, $dbdata, $where);
    }
    
    /**
     * This function is used to get user data by matching email.
     *
     * Created By : Bidhan Chandra
     * Date : 10 Jan,2014	
     * @param $params array by reference
     *
     * @return array.
     */
    public function getUserDetails($id) {
        try{
        if($id){
            $db = $this->getAdapter();
            $select = $db->select()
                    ->from('users', '*')
                    ->where('id = ?', $id);
            $value = $db->fetchRow($select);
            return $value;
        }else{
            return false;
        }
        }  catch (Exception $e){
            echo $e->getMessage();
        }
    }
    
     /**
     * This function is used to verify Email .
     *
     * Created By : Bidhan Chandra
     * Date : 25 Feb,2017	
     * @param $params array by reference
     *
     * @return array.
     */
    public function verifyEmail($uid, $token) {
        
        try{
            
            $db = $this->getAdapter();
            $select = $db->select()
                    ->from('users', array(new Zend_Db_Expr("count(id) AS count")))
                    ->where('id = ?', $uid)
                    ->where('activation_code = ?', $token);
            //echo $select;
            return $db->fetchRow($select);
            
        }catch(Exception $e){
            $e->getMessage();
        }
    }
    
    public function activateUserEmail($uid) {
        
        try{
            $db = $this->getAdapter();
            if($uid){
                
               // $data['activation_code'] = '';
                $data['status'] = 'active';
                $where['id =?'] = $uid;
                return $db->update('users',$data,$where); 
                
            }else{
                return false;
            }
            
        }catch(Exception $e){
            
        }
        
    }
    
     /**
     * This function is used to get user data by matching email.
     *
     * Created By : Bidhan Chandra
     * Date : 10 Jan,2014	
     * @param $params array by reference
     *
     * @return array.
     */
    public function adminUsersList() {
        try{
       
            $db = $this->getAdapter();
            $select = $db->select()
                    ->from(array('u'=>'users'), array('*'))
                    ->joinLeft(array('all'=>'vy_admin_login_log'),'all.user_id = u.id',array('login_date','logout_time'))
                    ->where('u.user_role_type = ?','b')
                    ->where('u.id !=?',$this->modelAdminId)
                    ->where('status = ?', 'active');
            $select->group('all.user_id');
            $select->order('all.id DESC');
            //echo $select;
            $value = $db->fetchAll($select);
            return $value;
        
        }  catch (Exception $e){
            echo $e->getMessage();
        }
    }
    
  public function checkuser($email) {
//echo "Hi"; die;
        $where = "where email = '" . $email. "'";
        $sql = "SELECT email FROM users $where";
        $result = $this->getAdapter()->fetchAll($sql);

        if (count($result) > 0) {
            return $result;
        }else{
            return false;
        }
        
    }
  
    
}

?>