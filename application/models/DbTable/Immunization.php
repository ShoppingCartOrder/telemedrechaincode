<?php

/**
 * This document defines Application_Model_DbTable_Users class
 * This is an Users model class. It defines all attributes and behaviour that is used for Users section.
 * @author Bidhan Chandra
 * @package Vivahaayojan Models
 * @subpackage Application_Model_DbTable_Users
 */
class Application_Model_DbTable_Immunization extends Mylib_Model_BaseModel {

    /**
     * Defining class variable for users table name.
     */
    protected $_name = 'hms_immunization_vaccine_details';

   

    /**
     * This function is used to get user data by matching email.
     *
     * Created By : Bidhan Chandra
     * Date : 10 Jan,2020	
     * @param $params array by reference
     *
     * @return array.
     */
    public function getImmunizationDetailsByName($name = '') {
        $query = "select * from $this->_name where vaccine_name like '%$name%' LIMIT 10";
        $select = $this->getAdapter()->fetchAll($query);
        return $select;
    }
    
    public function fetchImmunizationByNames($names) {
        $query = "select * from $this->_name where vaccine_name IN($names)";
        $select = $this->getAdapter()->fetchAll($query);
        return $select;
    }
    
    public function fetchImmunizationDetails() {


            $db = $this->getAdapter();
            $query = $db->select()
                    ->from(array('ltd' => $this->_name), array('*'));
            //echo $query; die;

            return $db->fetchAll($query);
        
    }
    
    public function fetchImmunizationChildBirthDetails($patientId,$rId) {

            if($patientId && $rId){
                $db = $this->getAdapter();
                $query = $db->select()
                        ->from(array('icd' => 'hms_immunization_child_birth_details'), array('*'))
                        ->where('patient_id = ?',$patientId)
                        ->where('registration_no =?',$rId);
//echo $query; die;
                return $db->fetchRow($query);
            }
        
    }
    
     public function addBirthDetails($data) {
        try {
            $instered =$this->getAdapter()->insert('hms_immunization_child_birth_details',$data);
        $lastInsertId = $this->getAdapter()->lastInsertId();
        return $lastInsertId;
        
        } catch (Exception $ex) {
            echo $ex->getMessage(); die;
        }
        
    }
    
    public function updateBirthDetails($dbdata, $data) {
        $where['id =?'] = $data;
        return $this->getAdapter()->update('hms_immunization_child_birth_details', $dbdata, $where);
    }
    
    public function bulkInsertVaccineDetails($vaccineData, $totalfields) {
        
        if ($vaccineData && $totalfields) {
            $values = implode(',', array_fill(0, $totalfields, '(?,?,?,?,?,?)'));
            //dd($values);
            $sql = "INSERT INTO hms_child_immunization_vaccine_details (`patient_id`,`registration_no`,`birth_details_id`,`vaccine_name`,`age`,`created_by`) VALUES $values";

            $stmt = $this->getAdapter()->prepare($sql);

            return $stmt->execute($vaccineData);
        } else {
            return false;
        }
    }
    
    public function InsertChildVaccineDetails($data) {
        try {
            $instered =$this->getAdapter()->insert('hms_child_immunization_vaccine_details',$data);
        $lastInsertId = $this->getAdapter()->lastInsertId();
        return $lastInsertId;
        
        } catch (Exception $ex) {
            echo $ex->getMessage(); die;
        }
        
    }
    
     public function fetchChildImmunizationDetails($patientId,$rId) {

          if($patientId && $rId){
            $db = $this->getAdapter();
            $query = $db->select()
                    ->from(array('ltd' => 'hms_child_immunization_vaccine_details'), array('*'))
                    ->where('patient_id = ?',$patientId)
                    ->where('registration_no =?',$rId);

            return $db->fetchAll($query);
          }
        
    }
    
      public function updateImmunizationDetails($dbdata, $cond) {
        
        try{
            //dd($cond,true);
             return $this->getAdapter()->update('hms_child_immunization_vaccine_details', $dbdata, $cond);
        }catch(Exception $e){
            echo $e->getMessage();
        }
        //$where['id =?'] = $data;
       
    }
    

}

?>