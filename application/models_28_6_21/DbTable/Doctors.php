<?php

/**
 * This document defines Application_Model_DbTable_Users class
 * This is an Users model class. It defines all attributes and behaviour that is used for Users section.
 * @author Bidhan Chandra
 * @package Vivahaayojan Models
 * @subpackage Application_Model_DbTable_Users
 */
class Application_Model_DbTable_Doctors extends Mylib_Model_BaseModel {

    /**
     * Defining class variable for users table name.
     */
    protected $_name = 'hms_doctors';

    /**
     * This function is used to create new user.
     *
     * Created By : Bidhan Chandra
     * Date : 10 Jan,2019	
     * @param $params array by reference
     *
     * @return string.
     */
    public function addDoctor($data) {
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
    public function getDoctorsByEmail($email) {
        $db = $this->getAdapter();
        $select = $db->select()
                ->from($this->_name, '*')
                ->where('email = ?', $email);
        $value = $db->fetchAll($select);
        return $value;
    }

    
     public function fetchDoctorById($id) {

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
    
    
    	public function fetchAllDoctorData() {
        
       
            try{
            $db = $this->getAdapter();
		$query = $db->select()
					 ->from(array('u'=>'users'),array('u.id AS login_id','u.email'))
					 ->joinInner(array('d'=>'doctors'),'u.id = d.user_id',array('d.id','d.name','d.contact_no','d.address'));
                                         
               // echo $query; die;
        
                return $db->fetchAll($query);
                
            }Catch(Exception $e){
                
                echo $e->getMessage();
            }
        
    }
    
    public function updateProfileDetail($dbdata, $id) {
        $where['id =?'] = $id;
        return $this->getAdapter()->update($this->_name, $dbdata, $where);
    }

    public function getUserDetails($id) {
        try{
        if($id){
            $db = $this->getAdapter();
            $select = $db->select()
                    ->from($this->_name, array('*'))
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
                    ->from('patients', array(new Zend_Db_Expr("count(id) AS count")))
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
                return $db->update('patients',$data,$where); 
                
            }else{
                return false;
            }
            
        }catch(Exception $e){
            
        }
        
    }
    
   
    
   public function checkuser($email) {
//echo "Hi"; die;
        $where = "where email = '" . $email. "'";
        $sql = "SELECT email FROM patients $where";
        $result = $this->getAdapter()->fetchAll($sql);

        if (count($result) > 0) {
            return $result;
        }else{
            return false;
        }
        
    }
    
     public function saveDoctorData($image_name, $data) {
         
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

   

    public function deletePatientData($id) {

        $where = $this->getadapter()->quoteInto('id = ?', $id);
        return $this->delete($where);
    }

    public function updateDoctorData($data) {
        
        if(!$data['id']){
            return false;
        }
           $dbdata = array(                                               
                            'name' => $data['p_name'],                           
                            'contact_no' => $data['p_mno'], 
                            'speciality_id' => $data['speciality'], 
                            'address' => $data['p_add'],
                            
                            'qualification' => $data['qualification'],                           
                            'experience' => $data['experience'], 
                            'fee' => $data['fee'], 
                            'about' => $data['about'],
               
       
                            );

    
        $where['id =?'] = $data['id'];

        return $this->getAdapter()->update($this->_name, $dbdata, $where);
    }
    
    public function allDoctors($params = null)
    {
        if (empty($params))
            return false;

        $fields = array('*');

        if (!empty($params['fields']['main'])) {
            $fields = $params['fields']['main'];
        }

        $select = $this->getAdapter()->select()
                ->from(array('p' => $this->_name), $fields)
                //->joinInner(array('u' => 'users'), 'u.id = p.user_id', array('email as email'))
                ->joinLeft(array('d' => 'hms_speciality'), 'd.id = p.speciality_id', array('d.name as speciality_name'));

        $selectCount = $this->getAdapter()->select()
                ->from(array('p' => $this->_name), array(new Zend_Db_Expr("count(p.id)  AS count")))
                //->joinInner(array('u' => 'users'), 'u.id = p.user_id', null)
                ->joinLeft(array('d' => 'hms_speciality'), 'd.id = p.speciality_id', null);

        $where = "1 ";

        $genCond = " AND p.status = 1";

        if (!empty($params['condition']['name'])) {
            $where .= " AND p.name LIKE '%".$params['condition']['name']."%'";
        }
        //dd($params);
        if (!empty($params['condition']['speciality'])) {
            $where .= " AND d.name LIKE '%".$params['condition']['speciality']."%'";
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
    
    public function getDoctorLoginIdByDoctorId($id) {
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
    
     public function updateDoctorDetail($dbdata, $data) {
        $where['id =?'] = $data;
        return $this->getAdapter()->update($this->_name, $dbdata, $where);
    }
    
    public function getDoctorBySpecialityId($id) {
        try{
            
        if($id){
            $db = $this->getAdapter();
            $select = $db->select()
                    ->from($this->_name, array('id','name'))
                    ->where('speciality_id = ?', $id);
            $value = $db->fetchAll($select);
            return $value;
        }else{
            return false;
        }
        }  catch (Exception $e){
            echo $e->getMessage();
        }
    }

    public function getDoctorDetailsById($id) {

        if (!empty($id)) {
            
            $select = $this->getAdapter()->select()
                ->from(array('dr' => 'doctors'), array('*'))
                //->useIndex('dr.department') 
                ->joinLeft(array('d' => 'departments'),
                        'dr.department = d.id', array('d.name as dprt_name'))
                    ->where('dr.id = ?', $id);
            
//echo $select;
            return $this->getAdapter()->fetchRow($select);
        } else {
            return Array('name' => 'NA');
        }
    }
}

?>