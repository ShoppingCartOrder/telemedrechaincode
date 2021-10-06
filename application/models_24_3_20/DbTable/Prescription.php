<?php

/**
 * This document defines Application_Model_DbTable_Users class
 * This is an Users model class. It defines all attributes and behaviour that is used for Users section.
 * @author Bidhan Chandra
 * @package Vivahaayojan Models
 * @subpackage Application_Model_DbTable_Users
 */
class Application_Model_DbTable_Prescription extends Mylib_Model_BaseModel {

    /**
     * Defining class variable for users table name.
     */
    protected $_name = 'prescription';

    /**
     * This function is used to create new user.
     *
     * Created By : Bidhan Chandra 
     * Date : 10 July,2019	
     * @param $params array by reference
     *
     * @return string.
     */
    public function addPrescription($data) {
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
    public function getPrescriptionById($id) {
        $db = $this->getAdapter();
        $select = $db->select()
                ->from('prescription', '*')
                ->where('id = ?', $id);
        $value = $db->fetchRow($select);
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
    
 


    
    public function updatePrescriptionDetail($dbdata, $id) {
        $where['id =?'] = $id;
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
    
    public function allPrescriptions($params = null)
    {
        if (empty($params))
            return false;

        $fields = array('*');

        if (!empty($params['fields']['main'])) {
            $fields = $params['fields']['main'];
        }

        $select = $this->getAdapter()->select()
                ->from(array('pr' => 'prescription'), $fields)
                ->joinInner(array('p' => 'patients'), 'pr.patient_id = p.id', array('name','email','contact_no','address'));

        $selectCount = $this->getAdapter()->select()
                ->from(array('pr' => 'prescription'), array(new Zend_Db_Expr("count(pr.id)  AS count")))
                ->joinInner(array('p' => 'patients'), 'pr.patient_id = p.id', null);

        $where = "1 ";

        $genCond = " ";
        
        if (isset($params['userRoleType']) && $params['userRoleType'] == DOCTOR_ROLE) {
            $genCond = " AND pr.prescribed_by = ".$this->modelDoctorId;

        } else if (isset($params['userRoleType']) && $params['userRoleType'] == PATIENT_ROLE) {
            $select->joinLeft(array('d' => 'doctors'), 'd.id = pr.prescribed_by', array('d.name AS doctor_name','d.email AS doctor_email','d.contact_no AS doctor_contact_no'));
            $select->joinLeft(array('dp' => 'departments'), 'dp.id = d.department', array('dp.name AS dprt_name'));
            
            $selectCount->joinLeft(array('d' => 'doctors'), 'd.id = pr.prescribed_by', null);
            $selectCount->joinLeft(array('dp' => 'departments'), 'dp.id = d.department', null);
            
            
            $genCond = " AND pr.patient_id = ".$this->modelPatientId;

        }
        
        
        if (!empty($params['condition']['medication_name'])) {
            $where .= " AND pr.medication_name LIKE '%".$params['condition']['medication_name']."%'";
        }
        
        
        
        if (!empty($params['condition']['created_at'])) {
        	$where .= " AND date(pr.created_at) = '" . date(DB_DATE_FORMAT,strtotime($params['condition']['created_at'])) . "'";
        }
        
        
        if (!empty($params['condition']['patient_id'])) {
            $where .= " AND pr.patient_id = '" . $params['condition']['patient_id'] . "'";
        }
        
        if (!empty($params['condition']['appnt_id'])) {
            $where .= " AND pr.appnt_id = '" . $params['condition']['appnt_id'] . "'";
        }
         
         //dd($params);
        /*
        if (!empty($params['condition']['appointment_type'])) {
            $where .= " AND a.appointment_type = '" . $params['condition']['appointment_type'] . "'";
        }
        
        if (!empty($params['condition']['appoinment_datetime'])) {
        	$where .= " AND date(a.appoinment_datetime) = '" . date(DB_DATE_FORMAT,strtotime($params['condition']['appoinment_datetime'])) . "'";
        }
         * 
         */
        
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