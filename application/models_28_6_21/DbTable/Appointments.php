<?php

/**
 * This document defines Application_Model_DbTable_Users class
 * This is an Users model class. It defines all attributes and behaviour that is used for Users section.
 * @author Bidhan Chandra
 * @package Vivahaayojan Models
 * @subpackage Application_Model_DbTable_Users
 */
class Application_Model_DbTable_Appointments extends Mylib_Model_BaseModel {

    /**
     * Defining class variable for users table name.
     */
    protected $_name = 'appointments';

    /**
     * This function is used to create new user.
     *
     * Created By : Bidhan Chandra 
     * Date : 10 July,2019	
     * @param $params array by reference
     *
     * @return string.
     */
    public function addAppointment($data) {
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
     * Date : 10 Jan,2014	
     * @param $params array by reference
     *
     * @return array.
     */
    public function getPatientByEmail($email) {
        $db = $this->getAdapter();
        $select = $db->select()
                ->from('patients', '*')
                ->where('email = ?', $email);
        $value = $db->fetchAll($select);
        return $value;
    }

    
    	public function fetchAppointmentData($pid,$appntId) {
        
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
    
 


    
    public function updateAppointmentDetail($dbdata, $data) {
        $where['id =?'] = $data;
        return $this->getAdapter()->update($this->_name, $dbdata, $where);
    }

    public function getAppointmentDetails($id) {
        try{
        if($id){
            $db = $this->getAdapter();
            $select = $db->select()
                    ->from('patients', '*')
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
    
     public function saveAppointmentData($image_name, $data) {
         
        $dbdata = array(
            'name' => $data['name'] . " " . $data['lname'],
            'email' => $data['email'],
            //'password' => md5($data['password']),
           // 'user_role_type' => $data['user_role_type'],
            'username' => $data['username'],
            'address' => $data['address'],
            'username' => $data['username'],
            'user_role_type'=>$data['user_role_type'],
            
        );
        $row = $this->createRow($dbdata);
        return $row->save();
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
    
    public function allAppointments($params = null)
    {
        if (empty($params))
            return false;

        $fields = array('*');

        if (!empty($params['fields']['main'])) {
            $fields = $params['fields']['main'];
        }

        $select = $this->getAdapter()->select()
                ->from(array('a' => 'appointments'), $fields)
                ->joinInner(array('p' => 'patients'), 'a.patient_id = p.id', array('name','email','contact_no','address'))
                ->joinLeft(array('pr' => 'prescription'), 'a.id = pr.appnt_id', array(new Zend_Db_Expr("count(pr.id)  AS Prescription_count")));

        $selectCount = $this->getAdapter()->select()
                ->from(array('a' => 'appointments'), array(new Zend_Db_Expr("count(a.id)  AS count")))
                ->joinInner(array('p' => 'patients'), 'a.patient_id = p.id', null)
                ->joinLeft(array('pr' => 'prescription'), 'a.id = pr.appnt_id', null);
        
        
        $where = "1 ";
        
        if (!empty($params['appointment_type']) || (isset($params['appointment_type']) && ($params['appointment_type'] == 0))) {
            $where .= " AND a.appointment_type = '" . $params['appointment_type'] . "'";
        }

        $genCond = " ";
        
        if (isset($params['userRoleType']) && $params['userRoleType'] == DOCTOR_ROLE) {
            $genCond = " AND a.healthcare_provider_id = ".$this->modelDoctorId;

        } else if (isset($params['userRoleType']) && $params['userRoleType'] == PATIENT_ROLE) {
            $select->joinLeft(array('d' => 'doctors'), 'd.id = a.healthcare_provider_id', array('d.name AS doctor_name','d.email AS doctor_email','d.contact_no AS doctor_contact_no'));
            $select->joinLeft(array('dp' => 'departments'), 'dp.id = d.department', array('dp.name AS dprt_name'));
            
            $selectCount->joinLeft(array('d' => 'doctors'), 'd.id = a.healthcare_provider_id', null);
            $selectCount->joinLeft(array('dp' => 'departments'), 'dp.id = d.department', null);
            
            
            $genCond = " AND a.patient_id = ".$this->modelPatientId;

        }
        
        
        if (!empty($params['condition']['name'])) {
            $where .= " AND p.name LIKE '%".$params['condition']['name']."%'";
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
        if (!empty($params['condition']['appnt_id'])) {
            $where .= " AND a.id = '" . $params['condition']['appnt_id'] . "'";
        }
               
        if (!empty($params['condition']['appointment_type']) || ((isset($params['condition']['appointment_type'])) && ($params['condition']['appointment_type'] == 0))) {
            $where .= " AND a.appointment_type = '" . $params['condition']['appointment_type'] . "'";
        }
        
        //dd($params['condition']['appointment_type']);
        
        if (!empty($params['condition']['status'])) {
            $where .= " AND a.status = '" . $params['condition']['status'] . "'";
        }
        
        if (!empty($params['condition']['created_at'])) {
        	$where .= " AND date(a.created_at) = '" . date(DB_DATE_FORMAT,strtotime($params['condition']['created_at'])) . "'";
        }
        
        if (!empty($params['condition']['appoinment_datetime'])) {
        	$where .= " AND date(a.appoinment_datetime) = '" . date(DB_DATE_FORMAT,strtotime($params['condition']['appoinment_datetime'])) . "'";
        }
        
        $where .= $genCond;

        if (!empty($where)) {
            $selectCount->where($where);
            $select->where($where);
        }

        $selectCount->group('a.id');
        $select->group('a.id');
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
    
    
    
   public function fetchAppointmentWithDoctorInfo($appntId) {
        
        if(!empty($appntId)){
            
            $db = $this->getAdapter();
		$query = $db->select()
					 ->from(array('a'=>'appointments'),array('*'))
					 ->joinInner(array('d'=>'doctors'),'a.healthcare_provider_id = d.id',array('d.name','d.contact_no','d.email','d.address'))
                                         ->where('a.patient_id = ?', $this->modelPatientId)
                                         ->where('a.id = ?', $appntId);
                //echo $query; die;
        
                return $db->fetchRow($query);
        } else{
			return Array('name'=>'NA');
		}
    }

    
}

?>