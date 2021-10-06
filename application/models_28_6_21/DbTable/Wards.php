<?php

/**
 * This document defines Application_Model_DbTable_Users class
 * This is an Users model class. It defines all attributes and behaviour that is used for Users section.
 * @author Bidhan Chandra
 * @package Vivahaayojan Models
 * @subpackage Application_Model_DbTable_Users
 */
class Application_Model_DbTable_Wards extends Mylib_Model_BaseModel {

    /**
     * Defining class variable for users table name.
     */
    protected $_name = 'hms_wards';

   

    /**
     * This function is used to get user data by matching email.
     *
     * Created By : Bidhan Chandra
     * Date : 10 Jan,2020	
     * @param $params array by reference
     *
     * @return array.
     */
  public function addWard($data) {
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
     * Date : 10 Jan,2020	
     * @param $params array by reference
     *
     * @return array.
     */
    public function getWardById($id) {
        $db = $this->getAdapter();
        $select = $db->select()
                ->from($this->_name, '*')
                ->where('id = ?', $id);
        $value = $db->fetchRow($select);
        return $value;
    }
    public function getAllocatedBedsDetails($id) {
        $db = $this->getAdapter();
        $select = $db->select()
                ->from('hms_patient_ward_bed_allocation', '*')
                ->where('ward_id = ?', $id)
                ->where('status = ?', 1);
        $value = $db->fetchAll($select);
        return $value;
    }

    
     
    
    	public function fetchAllWards() {
                   
            $db = $this->getAdapter();
            $query = $db->select()
		->from(array('d'=>$this->_name),array('*'));
					 
                //echo $query; die;
        
                return $db->fetchAll($query);
        
    }
    
 


    
    public function updateBedStatus($dbdata, $data) {
        $where['id =?'] = $data;
        return $this->getAdapter()->update('hms_patient_ward_bed_allocation', $dbdata, $where);
    }
    
    public function addBed($data) {
        try {
            $resource = $this->getAdapter()->insert('hms_patient_ward_bed_allocation', $data);
        $lastInsertId = $this->getAdapter()->lastInsertId();
        return $lastInsertId;
        
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
        
    }
    public function deleteDischargedBed($condition) {
        
        $this->getAdapter()->delete('hms_patient_ward_bed_allocation', $condition);
    }


}

?>