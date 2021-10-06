<?php

/**
 * This document defines Application_Model_DbTable_Users class
 * This is an Users model class. It defines all attributes and behaviour that is used for Users section.
 * @author Bidhan Chandra
 * @package Vivahaayojan Models
 * @subpackage Application_Model_DbTable_Users
 */
class Application_Model_DbTable_Laboratory extends Mylib_Model_BaseModel {

    /**
     * Defining class variable for users table name.
     */
    protected $_name = 'hms_laboratory_test';

   

    /**
     * This function is used to get user data by matching email.
     *
     * Created By : Bidhan Chandra
     * Date : 10 Jan,2020	
     * @param $params array by reference
     *
     * @return array.
     */
    public function getLaboratoryTestByName($name = '') {
        $query = "select * from hms_laboratory_test where laboratory_test_name like '%$name%' LIMIT 10";
        $select = $this->getAdapter()->fetchAll($query);
        return $select;
    }
    
    public function fetchLaboratoryTestByNames($names) {
        $query = "select * from hms_laboratory_test where laboratory_test_name IN($names)";
        $select = $this->getAdapter()->fetchAll($query);
        return $select;
    }


}

?>