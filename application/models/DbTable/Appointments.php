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
    protected $_name = 'hms_appointments';
    protected $_patient_registration = 'hms_patient_registration';
    protected $_doctors = 'hms_doctors';
    protected $_speciality = 'hms_speciality';

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
					 ->from(array('a'=>$this->_name),null)
					 ->joinInner(array('p'=>$this->_patient_registration),'a.patient_id = p.id AND a.registration_no = p.registration_no',array('id AS a_id','patient_type AS a_patient_type','referred_by AS a_referred_by', 'specialization_id AS a_specialization_id', 'doctor_id AS a_doctor_id', 'date_of_admission AS a_date_of_admission', 'reason_for_appointment AS a_reason_for_appointment','relation AS a_relation', 'relative_name AS a_relative_name', 'ward_id AS a_ward_id', 'bed_no AS a_bed_no'))
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
                ->from(array('a' => $this->_name), $fields)
                ->joinInner(array('p' => $this->_patient_registration), 'a.patient_id = p.id AND a.registration_no = p.registration_no', array('name as p_name','sex','mobile_no'))
                ->joinLeft(array('d' => $this->_doctors), 'd.id = a.doctor_id', array('d.name AS doctor_name','d.email AS doctor_email','d.contact_no AS doctor_contact_no'))
                ->joinLeft(array('dp' => $this->_speciality), 'dp.id = d.speciality_id', array('dp.name AS dprt_name'))
                ->joinLeft(array('pc' => 'hms_patient_charges'), 'a.patient_id = pc.patient_id AND a.registration_no = pc.registration_no AND a.id = pc.appointment_id', array('SUM(net_amount) AS paid_amount'));

                //->joinLeft(array('pr' => 'prescription'), 'a.id = pr.appnt_id', array(new Zend_Db_Expr("count(pr.id)  AS Prescription_count")));

        $selectCount = $this->getAdapter()->select()
                ->from(array('a' =>  $this->_name), array(new Zend_Db_Expr("count(a.id)  AS count")))
                ->joinInner(array('p' => $this->_patient_registration), 'a.patient_id = p.id AND a.registration_no = p.registration_no', null)
                ->joinLeft(array('d' => $this->_doctors), 'd.id = a.doctor_id', null)
                ->joinLeft(array('dp' => $this->_speciality), 'dp.id = d.speciality_id', null)
                ->joinLeft(array('pc' => 'hms_patient_charges'), 'a.patient_id = pc.patient_id AND a.registration_no = pc.registration_no AND a.id = pc.appointment_id', null);
            

//->joinLeft(array('pr' => 'prescription'), 'a.id = pr.appnt_id', null);
        
        
        $where = "1 ";
        
    /*    if (!empty($params['patient_type']) || (isset($params['patient_type']) && ($params['patient_type'] == 0))) {
            $where .= " AND a.patient_type = '" . $params['patient_type'] . "'";
        }
        if (!empty($params['rid'])) {
            $where .= " AND a.registration_no = '" . $params['rid'] . "'";
        }
        if (!empty($params['id'])) {
            $where .= " AND a.patient_id = '" . $params['id'] . "'";
        }
       */

        $genCond = " ";
        /*
        if (isset($params['userRoleType']) && $params['userRoleType'] == DOCTOR_ROLE) {
            $genCond = " AND a.doctor_id = ".$this->modelDoctorId;

        } else if (isset($params['userRoleType']) && $params['userRoleType'] == PATIENT_ROLE) {
            
            
            
            $genCond = " AND a.patient_id = ".$this->modelPatientId;

        }
        */
        
        if (!empty($params['condition']['name'])) {
            $where .= " AND p.name LIKE '%".$params['condition']['name']."%'";
        }
        
        if (!empty($params['condition']['contact_no'])) {
            $where .= " AND p.contact_no = '" . $params['condition']['contact_no'] . "'";
        }
        if (!empty($params['condition']['id'])) {
            $where .= " AND a.id = '" . $params['condition']['id'] . "'";
        }
        if (!empty($params['condition']['appnt_id'])) {
            $where .= " AND a.id = '" . $params['condition']['appnt_id'] . "'";
        }
        if (!empty($params['condition']['registration_no'])) {
            $where .= " AND a.registration_no = '" . $params['condition']['registration_no'] . "'";
        }
               
        if (!empty($params['condition']['patient_type']) || ((isset($params['condition']['patient_type'])) && ($params['condition']['appointment_type'] == 0))) {
            $where .= " AND a.patient_type = '" . $params['condition']['patient_type'] . "'";
        }
        
        //dd($params['condition']['appointment_type']);
        
      
        
        if (!empty($params['condition']['created_at'])) {
        	$where .= " AND date(a.created_at) = '" . date(DB_DATE_FORMAT,strtotime($params['condition']['created_at'])) . "'";
        }
        
        if (!empty($params['condition']['date_of_admission'])) {
        	$where .= " AND date(a.date_of_admission) = '" . date(DB_DATE_FORMAT,strtotime($params['condition']['date_of_admission'])) . "'";
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
    
    
    
   public function fetchAppointmentWithDoctorInfo($appntId,$pid) {
        
        if(!empty($appntId) && !empty($pid)){
            
            $db = $this->getAdapter();
		$query = $db->select()
					 ->from(array('a'=>'hms_appointments'),array('*'))
					 ->joinInner(array('d' => 'hms_doctors'), 'd.id = a.doctor_id', array('d.name AS doctor_name'))
                                         ->joinInner(array('s' => 'hms_speciality'), 's.id = a.specialization_id', array('s.name AS speciality_name'))
                    
                                         ->where('a.patient_id = ?', $pid)
                                         ->where('a.id = ?', $appntId);
                //echo $query; die;
        
                return $db->fetchRow($query);
        } else{
			return Array('name'=>'NA');
		}
    }

    
}

?>