<?php

/**
 * This document defines Application_Model_DbTable_Users class
 * This is an Users model class. It defines all attributes and behaviour that is used for Users section.
 * @author Bidhan Chandra
 * @package Vivahaayojan Models
 * @subpackage Application_Model_DbTable_Users
 */
class Application_Model_DbTable_Patients extends Mylib_Model_BaseModel {

    /**
     * Defining class variable for users table name.
     */
    protected $_name = 'hms_patient_registration';

    /**
     * This function is used to create new user.
     *
     * Created By : Bidhan Chandra
     * Date : 10 Jan,2014	
     * @param $params array by reference
     *
     * @return string.
     */
    public function addPatient($data) {
        try {
            //dd($data);
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

    public function getPatientById($id) {
        $db = $this->getAdapter();
        $select = $db->select()
                ->from('patients', '*')
                ->where('id = ?', $id);
        $value = $db->fetchRow($select);
        return $value;
    }
    
    public function fetchPatientData($id) {

        if (!empty($id)) {

            $db = $this->getAdapter();
            $query = $db->select()
                    ->from(array('p' => $this->_name), array('*'))
                    //->joinLeft(array('p' => 'patients'), 'u.id = p.user_id', array('p.id','p.email AS p_email', 'p.name', 'p.contact_no', 'p.address'))
                    //->joinLeft(array('u' => 'users'), 'u.id = p.user_id', array('u.id AS login_id', 'u.email'))
                    ->where('p.id = ?', $id);
            //echo $query; die;

            return $db->fetchRow($query);
        } else {
            return Array('name' => 'NA');
        }
    }

    public function fetchPatientProfileData($id) {

        if (!empty($id)) {

            $db = $this->getAdapter();
            $query = $db->select()
                    ->from(array('u' => 'users'), array('u.id AS login_id', 'u.email'))
                    ->joinInner(array('p' => 'patients'), 'u.id = p.user_id', array('p.id', 'p.name', 'p.contact_no', 'p.address'))
                    ->where('u.id = ?', $id);
            //echo $query; die;

            return $db->fetchRow($query);
        } else {
            return Array('name' => 'NA');
        }
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
    public function verifyPatient($token, $email) {
        $db = $this->getAdapter();
        $select = $db->select()
                ->from('patients', '*')
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
                ->from('patients', '*')
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

    public function newPassword($userId, $userNewPassword, $email) {
        $db = $this->getAdapter();
        $statuses = 'active';
        $tokens = '';
        $changepassword = array('password' => md5($userNewPassword), 'status' => $statuses, 'token' => $tokens);
        $result = $db->update('patients', $changepassword, 'id = ' . $userId);
        return 1;
    }

    public function updateProfileDetail($dbdata, $data) {
        $where['id =?'] = $data;
        return $this->getAdapter()->update($this->_name, $dbdata, $where);
    }

public function getUserDetails($id) {
        try{
        if($id){
            $db = $this->getAdapter();
            $select = $db->select()
                    ->from($this->_name, '*')
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

        try {

            $db = $this->getAdapter();
            $select = $db->select()
                    ->from('patients', array(new Zend_Db_Expr("count(id) AS count")))
                    ->where('id = ?', $uid)
                    ->where('activation_code = ?', $token);
            //echo $select;
            return $db->fetchRow($select);
        } catch (Exception $e) {
            $e->getMessage();
        }
    }

    public function activateUserEmail($uid) {

        try {
            $db = $this->getAdapter();
            if ($uid) {

                // $data['activation_code'] = '';
                $data['status'] = 'active';
                $where['id =?'] = $uid;
                return $db->update('patients', $data, $where);
            } else {
                return false;
            }
        } catch (Exception $e) {
            
        }
    }

    public function checkuser($email) {
//echo "Hi"; die;
        $where = "where email = '" . $email . "'";
        $sql = "SELECT email FROM patients $where";
        $result = $this->getAdapter()->fetchAll($sql);

        if (count($result) > 0) {
            return $result;
        } else {
            return false;
        }
    }

    public function savePatientData($image_name, $data) {

        $dbdata = array(
            'name' => $data['name'] . " " . $data['lname'],
            'email' => $data['email'],
            //'password' => md5($data['password']),
            // 'user_role_type' => $data['user_role_type'],
            'username' => $data['username'],
            'address' => $data['address'],
            'username' => $data['username'],
            'user_role_type' => $data['user_role_type'],
        );
        $row = $this->createRow($dbdata);
        return $row->save();
    }

    public function deletePatientData($id) {

        $where = $this->getadapter()->quoteInto('id = ?', $id);
        return $this->delete($where);
    }

    public function updatePatientData($data) {

        if (!$data['id']) {
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

     public function updateProfileData($data) {

        if (!$data['id']) {
            return false;
        }
        $dbdata = array(
            'name' => $data['p_name'],
            //'email' => $email, 
            'contact_no' => $data['p_mno'],
            'address' => $data['p_add']
        );


        $where['user_id =?'] = $data['id'];

        return $this->getAdapter()->update($this->_name, $dbdata, $where);
    }
    
    public function allDoctorPatients($params = null) {
        if (empty($params))
            return false;

        $fields = array('*');

        if (!empty($params['fields']['main'])) {
            $fields = $params['fields']['main'];
        }

        $select = $this->getAdapter()->select()
                ->from(array('p' => 'patients'), $fields)
                ->joinInner(array('a' => 'appointments'), 'a.patient_id = p.id', null);

        $selectCount = $this->getAdapter()->select()
                ->from(array('p' => 'patients'), array(new Zend_Db_Expr("count(p.id)  AS count")))
                ->joinInner(array('a' => 'appointments'), 'a.patient_id = p.id', null);

        $where = "1 ";

        $genCond = " AND a.healthcare_provider_id = ".$this->modelDoctorId;

        if (!empty($params['condition']['name'])) {
            $where .= " AND p.name LIKE '%" . $params['condition']['name'] . "%'";
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

        if (!empty($params['condition']['created_at'])) {
            $where .= " AND date(p.created_at) = '" . date(DB_DATE_FORMAT, strtotime($params['condition']['created_at'])) . "'";
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
    
    public function allPatients($params = null) {
        if (empty($params))
            return false;

        $fields = array('*');

        if (!empty($params['fields']['main'])) {
            $fields = $params['fields']['main'];
        }

        $select = $this->getAdapter()->select()
                ->from(array('p' => $this->_name), $fields);
        
           
        $selectCount = $this->getAdapter()->select()
                ->from(array('p' => $this->_name), array(new Zend_Db_Expr("count(p.id)  AS count")));
        

        $where = "1 ";

        $genCond = "";

        if (!empty($params['condition']['registration_no'])) {
            $where .= " AND p.registration_no = '" . $params['condition']['registration_no'] . "'";
        }
        if (!empty($params['condition']['name'])) {
            $where .= " AND p.name LIKE '%" . $params['condition']['name'] . "%'";
        }
        if (!empty($params['condition']['email'])) {
            $where .= " AND u.email = '" . $params['condition']['email'] . "'";
        }
        if (!empty($params['condition']['mobile_no'])) {
            $where .= " AND p.mobile_no = '" . $params['condition']['mobile_no'] . "'";
        }
        if (!empty($params['condition']['id'])) {
            $where .= " AND p.id = '" . $params['condition']['id'] . "'";
        }

        if (!empty($params['condition']['date_of_admission'])) {
            $where .= " AND date(p.date_of_admission) = '" . date(DB_DATE_FORMAT, strtotime($params['condition']['date_of_admission'])) . "'";
                      
        }

        $where .= $genCond;

        if (!empty($where)) {
            $selectCount->where($where);
            $select->where($where);
        }
        
        //$selectCount->group('a.id');
        //$select->group('a.id');
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

    public function getPatientLoginIdByPatientId($id) {
        try {

            if ($id) {
                $db = $this->getAdapter();
                $select = $db->select()
                        ->from('patients', array('user_id'))
                        ->where('id = ?', $id);
                $value = $db->fetchRow($select);
                return $value;
            } else {
                return false;
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function updatePatientDetail($dbdata, $data) {
        $where['id =?'] = $data;
        return $this->getAdapter()->update($this->_name, $dbdata, $where);
    }
    
    
     public function fetchHealthProfileData($id) {

        if (!empty($id)) {

            $db = $this->getAdapter();
            $query = $db->select()
                    ->from(array('hp' => 'patient_health_profiles'), array('*'))
                    //->joinLeft(array('p' => 'patients'), 'u.id = p.user_id', array('p.id','p.email AS p_email', 'p.name', 'p.contact_no', 'p.address'))
                    //->joinLeft(array('u' => 'users'), 'u.id = p.user_id', array('u.id AS login_id', 'u.email'))
                    ->where('hp.id = ?', $id);
            //echo $query; die;

            return $db->fetchRow($query);
        } else {
            return Array('name' => 'NA');
        }
    }
    
    public function fetchHealthProfilesByPatient($id) {

         if (empty($params))
            return false;

        $fields = array('*');

        if (!empty($params['fields']['main'])) {
            $fields = $params['fields']['main'];
        }

        $select = $this->getAdapter()->select()
                ->from(array('hp' => 'patient_health_profiles'), $fields);
                //->joinLeft(array('u' => 'users'), 'u.id = p.user_id', array('email as uemail'))
                //->joinInner(array('a' => 'appointments'), 'a.patient_id = p.id', null)
                //->joinInner(array('d' => 'doctors'), 'd.id = a.healthcare_provider_id',array('GROUP_CONCAT(d.name) AS doctor_name') );
        
           
        $selectCount = $this->getAdapter()->select()
                ->from(array('hp' => 'patient_health_profiles'), array(new Zend_Db_Expr("count(p.id)  AS count")));
                //->joinLeft(array('u' => 'users'), 'u.id = p.user_id', null)
                //->joinInner(array('a' => 'appointments'), 'a.patient_id = p.id', null)
                //->joinInner(array('d' => 'doctors'), 'd.id = a.healthcare_provider_id',null);
        

        $where = "1 ";

        $genCond = " AND hp.patient_id = ".$this->modelPatientId;

        if (!empty($params['condition']['medicine_names'])) {
            $where .= " AND hp.medicine_names LIKE '%" . $params['condition']['medicine_names'] . "%'";
        }
        if (!empty($params['condition']['doctor_name'])) {
            $where .= " AND hp.doctor_name LIKE '%" . $params['condition']['doctor_name'] . "%'";
        }
        if (!empty($params['condition']['health_condition'])) {
            $where .= " AND hp.health_condition = '" . $params['condition']['health_condition'] . "'";
        }
        

        if (!empty($params['condition']['created_at'])) {
            $where .= " AND date(p.created_at) = '" . date(DB_DATE_FORMAT, strtotime($params['condition']['created_at'])) . "'";
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