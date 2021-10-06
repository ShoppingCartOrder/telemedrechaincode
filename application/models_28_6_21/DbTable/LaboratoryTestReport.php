<?php

/**
 * This document defines Application_Model_DbTable_Users class
 * This is an Users model class. It defines all attributes and behaviour that is used for Users section.
 * @author Bidhan Chandra
 * @package Vivahaayojan Models
 * @subpackage Application_Model_DbTable_Users
 */
class Application_Model_DbTable_LaboratoryTestReport extends Mylib_Model_BaseModel {

    /**
     * Defining class variable for users table name.
     */
    protected $_name = 'hms_laboratory_test_report';

   
    
    public function addLaboratoryTestReport($data) {
        try {
            $instered = $this->insert($data);
        $lastInsertId = $this->getAdapter()->lastInsertId();
        return $lastInsertId;
        
        } catch (Exception $ex) {
            echo $ex->getMessage(); die;
        }
        
    }
    
    public function deleteLaboratoryTestReport($condition) {
        
        $this->getAdapter()->delete($this->_name, $condition);
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
   public function allLaboratoryTestReport($params = null) {
        if (empty($params))
            return false;

        $fields = array('*');

        if (!empty($params['fields']['main'])) {
            $fields = $params['fields']['main'];
        }

        $select = $this->getAdapter()->select()
                ->from(array('l' => $this->_name), $fields)
                ->joinInner(array('p' => 'hms_patient_registration'), 'l.patient_id = p.id AND l.registration_no = p.registration_no', array('p.name AS patient_name'))
                ->joinInner(array('d' => 'hms_doctors'), 'l.doctor_id = d.id', array('d.name AS doctor_name'));
           
        $selectCount = $this->getAdapter()->select()
                ->from(array('l' => $this->_name), array(new Zend_Db_Expr("count(p.id)  AS count")))
                ->joinInner(array('p' => 'hms_patient_registration'), 'l.patient_id = p.id AND l.registration_no = p.registration_no', null)
                ->joinInner(array('d' => 'hms_doctors'), 'l.doctor_id = d.id', null);
           

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


        $where .= $genCond;

        if (!empty($where)) {
            $selectCount->where($where);
            $select->where($where);
        }
        
        //$selectCount->group('p.id');
  
       // $select->group('p.id');
        
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
    
    public function updateLaboratoryTestReport($dbdata, $id) {
        $where['id =?'] = $id;
        return $this->getAdapter()->update($this->_name, $dbdata, $where);
    }
    
    public function fetchLabTestResultById($id) {

        if (!empty($id)) {

            $db = $this->getAdapter();
            $query = $db->select()
                    ->from(array('ltr' => $this->_name), array('*'))
                    ->joinInner(array('lr' => 'hms_laboratory_test_request'), 'lr.id = ltr.lab_test_request_id',null)
                    ->where('ltr.id = ?', $id);
            //echo $query; die;

            return $db->fetchRow($query);
        } else {
            return Array('name' => 'NA');
        }
    }

}

?>