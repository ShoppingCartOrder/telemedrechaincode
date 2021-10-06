<?php

/**
 * This document defines Application_Model_DbTable_Users class
 * This is an Users model class. It defines all attributes and behaviour that is used for Users section.
 * @author Bidhan Chandra
 * @package Vivahaayojan Models
 * @subpackage Application_Model_DbTable_Users
 */
class Application_Model_DbTable_Feedback extends Mylib_Model_BaseModel {

    /**
     * Defining class variable for users table name.
     */
    protected $_name = 'feedback';

    /**
     * This function is used to create new user.
     *
     * Created By : Bidhan Chandra 
     * Date : 10 July,2019	
     * @param $params array by reference
     *
     * @return string.
     */
    public function addFeedback($data) {
        try {
            $instered = $this->insert($data);
        $lastInsertId = $this->getAdapter()->lastInsertId();
        return $lastInsertId;
        
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
        
    }

    /**
     * This function is used to get user data by matching email.
     *
     * Created By : Bidhan Chandra
     * Date : 10 Jan,2019	
     * @param $params array by reference
     *
     * @return array.
     */
  

    
    	public function fetchFeedbackData($pid,$appntId) {
        
        if(!empty($pid) && !empty($appntId)){
            
            $db = $this->getAdapter();
		$query = $db->select()
					 ->from(array('a'=>'appointments'),array('*'))
					 ->joinInner(array('p'=>'patients'),'a.patient_id = p.id',array('p.name','p.contact_no','p.email','p.address'))
                                         ->where('p.id = ?', $pid)
                                         ->where('a.id = ?', $appntId);
                //echo $query; die;
        
                return $db->fetchRow($query);
        } else if(!empty($appntId)){
            
            $db = $this->getAdapter();
		$query = $db->select()
					 ->from(array('a'=>'appointments'),array('*'))
					 ->joinInner(array('p'=>'patients'),'a.patient_id = p.id',array('p.name','p.contact_no','p.email','p.address'))
                                         //->where('p.id = ?', $pid)
                                         ->where('a.id = ?', $appntId);
                //echo $query; die;
        
                return $db->fetchRow($query);
            
        }else{
			return Array('name'=>'NA');
		}
    }
    
 


    
    public function updateHealthProfileDetail($dbdata, $id) {
        $where['id =?'] = $id;
        return $this->getAdapter()->update($this->_name, $dbdata, $where);
    }

    public function getFeedbackDetails($id) {
        try{
        if($id){
            $db = $this->getAdapter();
            $select = $db->select()
                    ->from('feedback', '*')
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
    
   

   

    public function deleteAppointmentData($id) {

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
    
    public function allHealthProfiles($params = null)
    {
        if (empty($params))
            return false;

        $fields = array('*');

        if (!empty($params['fields']['main'])) {
            $fields = $params['fields']['main'];
        }

        $select = $this->getAdapter()->select()
                ->from(array('hp' => 'patient_health_profiles'), $fields);

        $selectCount = $this->getAdapter()->select()
                ->from(array('hp' => 'patient_health_profiles'), array(new Zend_Db_Expr("count(hp.id)  AS count")));
        
        
        $where = "1 ";
        
        

        $genCond = " ";
        
        $genCond = " AND hp.patient_id = ".$this->modelPatientId;
        
        if (!empty($params['condition']['medicine_names'])) {
            $where .= " AND hp.name LIKE '%".$params['condition']['medicine_names']."%'";
        }
        if (!empty($params['condition']['doctor_name'])) {
            $where .= " AND hp.doctor_name LIKE '%".$params['condition']['doctor_name']."%'";
        }
        if (!empty($params['condition']['diagnosed_in'])) {
            $where .= " AND hp.diagnosed_in = '" . $params['condition']['diagnosed_in'] . "'";
        }
        if (!empty($params['condition']['health_condition'])) {
            $where .= " AND hp.health_condition = '" . $params['condition']['health_condition'] . "'";
        }
        if (!empty($params['condition']['id'])) {
            $where .= " AND hp.id = '" . $params['condition']['id'] . "'";
        }
        
        if (!empty($params['condition']['created_at'])) {
        	$where .= " AND date(hp.created_at) = '" . date(DB_DATE_FORMAT,strtotime($params['condition']['created_at'])) . "'";
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