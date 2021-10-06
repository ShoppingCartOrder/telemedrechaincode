<?php

/**
 * This document defines Application_Model_DbTable_Users class
 * This is an Users model class. It defines all attributes and behaviour that is used for Users section.
 * @author Bidhan Chandra
 * @package Vivahaayojan Models
 * @subpackage Application_Model_DbTable_Users
 */
class Application_Model_DbTable_StaffDetails extends Mylib_Model_BaseModel {

    /**
     * Defining class variable for users table name.
     */
    protected $_name = 'hms_staff_details';

    /**
     * This function is used to create new user.
     *
     * Created By : Bidhan Chandra
     * Date : 10 Jan,2014	
     * @param $params array by reference
     *
     * @return string.
     */
    public function addStaff($data) {
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
                ->from($this->_name, '*')
                ->where('email = ?', $email);
        $value = $db->fetchAll($select);
        return $value;
    }

    

  
    public function updateStaffDetail($dbdata, $data) {
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
    public function getStaffDetails($id) {
        try{
        if($id){
            $db = $this->getAdapter();
            $select = $db->select()
                    ->from($this->_name, '*')
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
    
     
    
    public function activate_StaffEmail($uid) {
        
        try{
            $db = $this->getAdapter();
            if($uid){
                
               // $data['activation_code'] = '';
                $data['status'] = 1;
                $where['id =?'] = $uid;
                return $db->update($this->_name,$data,$where); 
                
            }else{
                return false;
            }
            
        }catch(Exception $e){
            
        }
        
    }
    
    
  public function check_StaffEmail($email) {
//echo "Hi"; die;
        $where = "where email = '" . $email. "'";
        $sql = "SELECT email FROM $this->_name $where";
        $result = $this->getAdapter()->fetchAll($sql);

        if (count($result) > 0) {
            return $result;
        }else{
            return false;
        }
        
    }
    
    public function allStaffs($params = null)
    {
        if (empty($params))
            return false;

        $fields = array('*');

        if (!empty($params['fields']['main'])) {
            $fields = $params['fields']['main'];
        }

        $select = $this->getAdapter()->select()
                ->from(array('p' => $this->_name), $fields);
                //->joinInner(array('u' => 'users'), 'u.id = p.user_id', array('email as email'));
               // ->joinLeft(array('d' => 'departments'), 'd.id = p.department', array('d.name as department_name'));

        $selectCount = $this->getAdapter()->select()
                ->from(array('p' => $this->_name), array(new Zend_Db_Expr("count(p.id)  AS count")));
                //->joinInner(array('u' => 'users'), 'u.id = p.user_id', null);
                //->joinLeft(array('d' => 'departments'), 'd.id = p.department', null);

        $where = "1 ";

        $genCond = " AND p.status = 1 AND status =1";

        if (!empty($params['condition']['name'])) {
            $where .= " AND p.name LIKE '%".$params['condition']['name']."%'";
        }
        //dd($params);
        if (!empty($params['condition']['department'])) {
            $where .= " AND p.name LIKE '%".$params['condition']['department']."%'";
        }
        if (!empty($params['condition']['email'])) {
            $where .= " AND p.email = '" . $params['condition']['email'] . "'";
        }
        if (!empty($params['condition']['contact_no'])) {
            $where .= " AND p.contact_no = '" . $params['condition']['contact_no'] . "'";
        }
        if (!empty($params['condition']['id'])) {
            $where .= " AND p.id = '" . $params['condition']['id'] . "'";
        }
        
        
        $where .= $genCond;

        if (!empty($where)) {
            $selectCount->where($where);
            $select->where($where);
        }
//echo $selectCount; die;
        $resultCount = $this->getAdapter()->fetchCol($selectCount);

        $params['count'] = $resultCount[0];
        
        if (!$params['count']) {
            return $this->blankResult();
        }
        $this->getPagingSorting($params);

        $select->order($params['sidx'] . " " . $params['sord']);

        $select->limit($params['limit'], $params['start']);
        
        try {
            //echo $select; die;
            $result = $this->getAdapter()->fetchAll($select);
            
        } catch (Exception $ex) {
            
            echo $ex->getMessage();
        }
        
        return array(
            'result' => $result,
            'total' => $params['totalPages'],
            'records' => $params['count'],
            'page' => $params['page'],
            'resultCount' => count($result)
        );
    }
  
    
}

?>