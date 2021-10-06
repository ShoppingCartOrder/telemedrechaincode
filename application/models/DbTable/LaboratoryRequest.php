<?php

/**
 * This document defines Application_Model_DbTable_Users class
 * This is an Users model class. It defines all attributes and behaviour that is used for Users section.
 * @author Bidhan Chandra
 * @package Vivahaayojan Models
 * @subpackage Application_Model_DbTable_Users
 */
class Application_Model_DbTable_LaboratoryRequest extends Mylib_Model_BaseModel {

    /**
     * Defining class variable for users table name.
     */
    protected $_name = 'hms_laboratory_test_request';

   
    
    public function addLaboratoryTestRequest($data) {
        try {
            $instered = $this->insert($data);
        $lastInsertId = $this->getAdapter()->lastInsertId();
        return $lastInsertId;
        
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
        
    }
    
    public function deleteLaboratoryTestRequest($condition) {
        
        $this->getAdapter()->delete('hms_laboratory_test_request', $condition);
    }
    
    /**
     * This function is used to get user data by matching email.
     *
     * Created By : Bidhan Chandra
     * Date : 10 Jan,2020	
     * @param $params array by reference
     *
     * @return array.
     */
   public function allLabTestRequest($params = null) {
        if (empty($params))
            return false;

        $fields = array('*');

        if (!empty($params['fields']['main'])) {
            $fields = $params['fields']['main'];
        }

        $select = $this->getAdapter()->select()
                ->from(array('l' => $this->_name), $fields)
                ->joinInner(array('p' => 'hms_patient_registration'), 'l.patient_id = p.id AND l.registration_no = p.registration_no', array('p.name AS patient_name'))
                ->joinInner(array('d' => 'hms_doctors'), 'l.doctor_id = d.id', array('d.name AS doctor_name'))
                ->joinLeft(array('ltr' => 'hms_patient_laboratory_test_results'), 'ltr.lab_test_request_id = l.id', array('COUNT(ltr.id) AS test_report_ids'));
           
        $selectCount = $this->getAdapter()->select()
                ->from(array('l' => $this->_name), array(new Zend_Db_Expr("count(p.id)  AS count")))
                ->joinInner(array('p' => 'hms_patient_registration'), 'l.patient_id = p.id AND l.registration_no = p.registration_no', null)
                ->joinInner(array('d' => 'hms_doctors'), 'l.doctor_id = d.id', null)
                ->joinLeft(array('ltr' => 'hms_patient_laboratory_test_results'), 'ltr.lab_test_request_id = l.id', null);
           

        $where = "1 ";

        $genCond = "";

       
        if (!empty($params['condition']['lab_test_name'])) {
            $where .= " AND l.lab_test_name LIKE '%" . $params['condition']['lab_test_name'] . "%'";
        }
        
        if (!empty($params['condition']['id'])) {
            $where .= " AND l.id = '" . $params['condition']['id'] . "'";
        }
        if (isset($params['condition']['status'])) {
            $where .= " AND l.status = '" . $params['condition']['status'] . "'";
        }
        if (!empty($params['condition']['patient_id'])) {
            $where .= " AND l.patient_id = '" . $params['condition']['patient_id'] . "'";
        }
        if (isset($params['condition']['rid'])) {
            $where .= " AND l.registration_no = '" . $params['condition']['rid'] . "'";
        }
        
        if (!empty($params['condition']['prescribe_id'])) {
            $where .= " AND l.prescribe_id = '" . $params['condition']['prescribe_id'] . "'";
        }


        $where .= $genCond;

        if (!empty($where)) {
            $selectCount->where($where);
            $select->where($where);
        }
        $selectCount->group('l.id');
        $selectCount->group('ltr.lab_test_request_id');
  
       $select->group('l.id');
       $select->group('ltr.lab_test_request_id');
        
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
    
    public function updateLabRequestStatus($dbdata, $data) {
        $where['id =?'] = $data;
        return $this->getAdapter()->update($this->_name, $dbdata, $where);
    }
    
    public function fetchLabRequestByReqId($reqid) {

        if (!empty($reqid)) {

            $db = $this->getAdapter();
            $query = $db->select()
                    ->from(array('lr' => $this->_name), array('*'))
                     ->joinInner(array('d' => 'hms_doctors'), 'd.id = lr.doctor_id', array('d.name AS doctor_name'))
                     ->joinInner(array('s' => 'hms_speciality'), 's.id = lr.specialization_id', array('s.name AS speciality_name'))
                    ->where('lr.id = ?', $reqid);
            //echo $query; die;

            return $db->fetchRow($query);
        } else {
            return Array('name' => 'NA');
        }
    }


}

?>