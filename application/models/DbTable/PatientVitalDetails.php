<?php

/**
 * This document defines Application_Model_DbTable_Users class
 * This is an Users model class. It defines all attributes and behaviour that is used for Users section.
 * @author Bidhan Chandra
 * @package Vivahaayojan Models
 * @subpackage Application_Model_DbTable_Users
 */
class Application_Model_DbTable_PatientVitalDetails extends Mylib_Model_BaseModel {

    /**
     * Defining class variable for users table name.
     */
    protected $_name = 'hms_patient_vital_details';

    /**
     * This function is used to create new user.
     *
     * Created By : Bidhan Chandra 
     * Date : 10 July,2019	
     * @param $params array by reference
     *
     * @return string.
     */
    public function addpatientVitalDetails($data) {
        try {
            $instered = $this->insert($data);
            $lastInsertId = $this->getAdapter()->lastInsertId();
            return $lastInsertId;
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }

    public function fetchAppointmentData($pid, $appntId) {

        if (!empty($pid) && !empty($appntId)) {

            $db = $this->getAdapter();
            $query = $db->select()
                    ->from(array('a' => 'appointments'), array('*'))
                    ->joinInner(array('p' => 'patients'), 'a.patient_id = p.id', array('p.name', 'p.contact_no', 'p.email', 'p.address'))
                    ->where('p.id = ?', $pid)
                    ->where('a.id = ?', $appntId);
            //echo $query; die;

            return $db->fetchRow($query);
        } else if (!empty($appntId)) {

            $db = $this->getAdapter();
            $query = $db->select()
                    ->from(array('a' => 'appointments'), array('*'))
                    ->joinInner(array('p' => 'patients'), 'a.patient_id = p.id', array('p.name', 'p.contact_no', 'p.email', 'p.address'))
                    //->where('p.id = ?', $pid)
                    ->where('a.id = ?', $appntId);
            //echo $query; die;

            return $db->fetchRow($query);
        } else {
            return Array('name' => 'NA');
        }
    }

    public function updatePatientVitalDetails($dbdata, $id) {
        $where['id =?'] = $id;
        return $this->getAdapter()->update($this->_name, $dbdata, $where);
    }

    public function getPatientVitalDetails($id) {
        try {
            if ($id) {
                $db = $this->getAdapter();
                $select = $db->select()
                        ->from('patient_vital_details', '*')
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
    
    public function getPatientAllVitalDetails($pid) {
        try {
            if ($pid) {
                $db = $this->getAdapter();
                $select = $db->select()
                        ->from($this->_name, '*')
                        ->where('patient_id = ?', $pid);
                $value = $db->fetchAll($select);
                return $value;
            } else {
                return false;
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
    
    public function deleteAppointmentData($id) {

        $where = $this->getadapter()->quoteInto('id = ?', $id);
        return $this->delete($where);
    }

    public function updateAppointmentData($data) {

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

    public function allViralDetails($params = null) {
        if (empty($params))
            return false;

        $fields = array('*');

        if (!empty($params['fields']['main'])) {
            $fields = $params['fields']['main'];
        }

        $select = $this->getAdapter()->select()
                ->from(array('vd' => 'patient_vital_details'), $fields);

        $selectCount = $this->getAdapter()->select()
                ->from(array('vd' => 'patient_vital_details'), array(new Zend_Db_Expr("count(vd.id)  AS count")));

        $where = "1 ";

        $genCond = " ";

        $genCond = " AND vd.patient_id = " . $this->modelPatientId;

        if (!empty($params['condition']['blood_pressure'])) {
            $where .= " AND vd.blood_pressure LIKE '%" . $params['condition']['blood_pressure'] . "%'";
        }
        if (!empty($params['condition']['sugar'])) {
            $where .= " AND vd.sugar LIKE '%" . $params['condition']['sugar'] . "%'";
        }
        if (!empty($params['condition']['heart_beat'])) {
            $where .= " AND vd.heart_beat LIKE '%" . $params['condition']['heart_beat'] . "%'";
        }
        if (!empty($params['condition']['bmi'])) {
            $where .= " AND vd.bmi LIKE '%" . $params['condition']['bmi'] . "%'";
        }
        if (!empty($params['condition']['temperature'])) {
            $where .= " AND vd.temperature LIKE '%" . $params['condition']['temperature'] . "%'";
        }


        if (!empty($params['condition']['created_at'])) {
            $where .= " AND date(vd.created_at) = '" . date(DB_DATE_FORMAT, strtotime($params['condition']['created_at'])) . "'";
        }



        $where .= $genCond;

        if (!empty($where)) {
            $selectCount->where($where);
            $select->where($where);
        }

        //echo $selectCount; die;
        $resultCount = $this->getAdapter()->fetchCol($selectCount);

        if (isset($resultCount[0])) {
            $params['count'] = $resultCount[0];
        } else {
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