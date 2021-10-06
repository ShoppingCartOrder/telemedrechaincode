<?php

/**
 * This document defines Application_Model_DbTable_Users class
 * This is an Users model class. It defines all attributes and behaviour that is used for Users section.
 * @author Bidhan Chandra
 * @package Vivahaayojan Models
 * @subpackage Application_Model_DbTable_Users
 */
class Application_Model_DbTable_Laboratories extends Mylib_Model_BaseModel {

    /**
     * Defining class variable for users table name.
     */
    protected $_name = 'hms_laboratories';

   

    /**
     * This function is used to get user data by matching email.
     *
     * Created By : Bidhan Chandra
     * Date : 10 Jan,2020	
     * @param $params array by reference
     *
     * @return array.
     */
    public function addLaboratory($data) {
        try {
            $instered = $this->insert($data);
        $lastInsertId = $this->getAdapter()->lastInsertId();
        return $lastInsertId;
        
        } catch (Exception $ex) {
            echo $ex->getMessage(); die;
        }
        
    }
    
    public function getLaboratoryByEmail($email) {
        $db = $this->getAdapter();
        $select = $db->select()
                ->from($this->_name, '*')
                ->where('email = ?', $email);
        $value = $db->fetchAll($select);
        return $value;
    }

    
     public function fetchLaboratoryById($id) {

        if (!empty($id)) {

            $db = $this->getAdapter();
            $query = $db->select()
                    ->from(array('d' => $this->_name), array('*'))
                    //->joinInner(array('d' => 'doctors'), 'u.id = d.user_id', array('d.id', 'd.department', 'd.name', 'd.contact_no', 'd.address'))
                    ->where('d.id = ?', $id);
            //echo $query; die;

            return $db->fetchRow($query);
        } else {
            return Array('name' => 'NA');
        }
    }
    
public function updateLaboratoryData($dbdata, $id) {
        $where['id =?'] = $id;
        return $this->getAdapter()->update($this->_name, $dbdata, $where);
    }
    
     public function allLaboratory($params = null)
    {
        if (empty($params))
            return false;

        $fields = array('*');

        if (!empty($params['fields']['main'])) {
            $fields = $params['fields']['main'];
        }

        $select = $this->getAdapter()->select()
                ->from(array('p' => $this->_name), $fields)
                ->joinInner(array('u' => 'users'), 'u.id = p.user_id', array('email as email'));
               // ->joinLeft(array('d' => 'departments'), 'd.id = p.department', array('d.name as department_name'));

        $selectCount = $this->getAdapter()->select()
                ->from(array('p' => $this->_name), array(new Zend_Db_Expr("count(p.id)  AS count")))
                ->joinInner(array('u' => 'users'), 'u.id = p.user_id', null);
                //->joinLeft(array('d' => 'departments'), 'd.id = p.department', null);

        $where = "1 ";

        $genCond = " AND p.status = 1 AND u.status =1";

        if (!empty($params['condition']['name'])) {
            $where .= " AND p.name LIKE '%".$params['condition']['name']."%'";
        }
        //dd($params);
        if (!empty($params['condition']['department'])) {
            $where .= " AND d.name LIKE '%".$params['condition']['department']."%'";
        }
        if (!empty($params['condition']['email'])) {
            $where .= " AND u.email = '" . $params['condition']['email'] . "'";
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
    
    public function getUserDetails($id) {
        try{
        if($id){
            $db = $this->getAdapter();
            $select = $db->select()
                    ->from($this->_name, 'id')
                    ->where('user_id = ?', $id);
            $value = $db->fetchRow($select);
            return $value;
        }else{
            return false;
        }
        }  catch (Exception $e){
            echo $e->getMessage();
        }
    }
    
     public function getLaboratoryLoginIdByLabId($id) {
        try{
            
        if($id){
            $db = $this->getAdapter();
            $select = $db->select()
                    ->from($this->_name, array('user_id'))
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
    
     public function updateLaboratoryDetail($dbdata, $data) {
        $where['id =?'] = $data;
        return $this->getAdapter()->update($this->_name, $dbdata, $where);
    }
    
     public function fetchLaboratoryProfileData($id) {

        if (!empty($id)) {

            $db = $this->getAdapter();
            $query = $db->select()
                    ->from(array('u' => 'users'), array('u.id AS login_id', 'u.email'))
                    ->joinInner(array('p' => $this->_name), 'u.id = p.user_id', array('*'))
                    ->where('u.id = ?', $id);
            //echo $query; die;

            return $db->fetchRow($query);
        } else {
            return Array('name' => 'NA');
        }
    }
    

}

?>