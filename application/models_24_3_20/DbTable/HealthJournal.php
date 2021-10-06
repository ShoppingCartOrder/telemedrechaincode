<?php

/**
 * This document defines Application_Model_DbTable_Users class
 * This is an Users model class. It defines all attributes and behaviour that is used for Users section.
 * @author Bidhan Chandra
 * @package Vivahaayojan Models
 * @subpackage Application_Model_DbTable_Users
 */
class Application_Model_DbTable_HealthJournal extends Mylib_Model_BaseModel {

    /**
     * Defining class variable for users table name.
     */
    protected $_name = 'health_journal';

    /**
     * This function is used to create new user.
     *
     * Created By : Bidhan Chandra 
     * Date : 10 July,2019	
     * @param $params array by reference
     *
     * @return string.
     */
    public function addHealthJournal($data) {
        try {
            $instered = $this->insert($data);
        $lastInsertId = $this->getAdapter()->lastInsertId();
        return $lastInsertId;
        
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
        
    }


 


    
    public function updateHealthJournal($dbdata, $id) {
        $where['id =?'] = $id;
        return $this->getAdapter()->update($this->_name, $dbdata, $where);
    }

    public function getHealthJournal($id) {
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
    
   

   

    public function deleteHealthJournal($id) {

        $where = $this->getadapter()->quoteInto('id = ?', $id);
        return $this->delete($where);
    }

    public function updateAppointmentData($data) {
        
        if(!$data['id']){
            return false;
        }
           $dbdata = array(                                               
                            'name' => $data['p_name'],
                            //'email' => $email, 
                            'contact_no' => $data['p_mno'], 
                            'address' => $data['p_add']
                            );

    
        $where['id =?'] = $data['id'];

        return $this->getAdapter()->update($this->_name, $dbdata, $where);
    }
    
    public function allHealthJournal($params = null)
    {
        if (empty($params))
            return false;

        $fields = array('*');

        if (!empty($params['fields']['main'])) {
            $fields = $params['fields']['main'];
        }

        $select = $this->getAdapter()->select()
                ->from(array('j' => $this->_name), $fields);

        $selectCount = $this->getAdapter()->select()
                ->from(array('j' => $this->_name), array(new Zend_Db_Expr("count(j.id)  AS count")));
                
        $where = "1 ";
               
        $genCond = " ";
     
        $genCond = " AND j.patient_id = ".$this->modelPatientId;
        
        if (!empty($params['condition']['journal'])) {
            $where .= " AND j.journal LIKE '%".$params['condition']['journal']."%'";
        }
       
               
        
        if (!empty($params['condition']['created_at'])) {
        	$where .= " AND date(j.created_at) = '" . date(DB_DATE_FORMAT,strtotime($params['condition']['created_at'])) . "'";
        }
        
       
        
        $where .= $genCond;

        if (!empty($where)) {
            $selectCount->where($where);
            $select->where($where);
        }

        //echo $selectCount; die;
        $resultCount = $this->getAdapter()->fetchCol($selectCount);
        
        if(isset($resultCount[0])){
            $params['count'] = $resultCount[0];
            
        }else{
            $params['count'] = 0;
        }
        
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