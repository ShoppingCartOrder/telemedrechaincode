<?php

/**
 * This document defines Mylib_Model_BaseModel class
 * This is Base model class. It defines all attributes and behaviour that is common to whole application.
 * @author Tech Lead
 * @package Mylib_Library Models
 * @subpackage Mylib_Model_BaseModel
 */
class Mylib_Model_BaseModel extends Zend_Db_Table_Abstract
{

    /**
     * Defining class variable for zend database adapter.
     */
    public $_db;

    /**
     * Defining class variable for user session.
     */
    public $userSession;

    /**
     * Defining class variable for constant.
     */
    public $constant = null;

    /**
     * Defining class variable as object for database table
     */
    public $__tables = array();

    /**
     * Defining class variable as object for database table
     */
    public $__tablespk = array();

    /**
     * Initializing zend database adapter and initializing user session variable for use.
     * Created By : Tech Lead
     * Date : 19 Dec,2013	
     * @param	 void
     * @return void
     */
    public function __construct()
    {
        if (null === $this->_db) {
            $this->_db = self::getDBAdapter();
        }
        $userSession = new Zend_Session_Namespace('userNamespace');        
        $adminSession = new Zend_Session_Namespace('my');
        $mydoctorSession = new Zend_Session_Namespace('mydoctor');
        $mypatientSession = new Zend_Session_Namespace('mypatient');
        $myHospitalUserSession = new Zend_Session_Namespace('myhospital');
        $myLaboratoryUserSession = new Zend_Session_Namespace('mylaboratory');
        
        $this->adminSession = $adminSession->storage;        
        $this->doctorSession = $mydoctorSession->storage;        
        $this->patientSession = $mypatientSession->storage;        
        $this->hospitalUserSession = $myHospitalUserSession->storage;        
        $this->laboratoryUserSession = $myLaboratoryUserSession->storage;
        
        if(isset($this->adminSession->id)) $this->modelAdminId = $this->adminSession->id; 
        
        if(isset($this->doctorSession->id)){
            
            $this->modelDoctorLoginId = $this->doctorSession->id; 
            $this->modelDoctorId = $mydoctorSession->doctor_id; 
            
        }
        if(isset($this->hospitalUserSession->id)){
            
            $this->modelHospitalLoginId = $this->hospitalUserSession->id; 
            $this->modelHospitalUserId = $myHospitalUserSession->user_id; 
            
        }
        //dd($mydoctorSession->doctor_id);
        if(isset($this->patientSession->id)){
            
            $this->modelPatientLoginId = $this->patientSession->id; 
            $this->modelPatientId = $mypatientSession->patient_id; 
            
        }
        if(isset($this->laboratoryUserSession->id)){
            
            $this->modelLabLoginId = $this->laboratoryUserSession->id; 
            $this->modelLabId = $myLaboratoryUserSession->lab_id; 
            
        }
        
       // if(isset($this->patientSession->id)) $this->modelPatientId = $this->patientSession->id; 
        
        $this->userSession = $userSession->storage;        
        
	if(isset($this->userSession->id)) $this->modelUserId = $this->userSession->id; 
        /*
    	if(!empty($this->userSession->city)) {
            $this->cityId = $this->userSession->city;
        } else {
            $this->cityId = 1;
        }
        */
        
        $searchSession = new Zend_Session_Namespace('searchSession');
        if (!empty($searchSession->cityId)) {
            $this->cityId = $searchSession->cityId;
        } else {
            $this->cityId = DEFAULT_CITY_ID;
        }
        $this->__tables = Zend_Registry::get("tables");
        $this->__tablespk = Zend_Registry::get("tablespk")->toArray();
    }

    /**
     * This function is used to control pagination and sorting.
     *
     * Created By : Tech Lead
     * Date : 19 Dec,2013	
     * @param $params array by reference
     *
     * @return void.
     */
    public function getSearchParams($data = array())
    {
        $params = array();
        
        $params['page'] = !empty($data['page'])? $data['page'] : 1;
        
        $params['rows'] = $params['limit'] = !empty($data['rows'])? $data['rows'] : NO_OF_RECORDS_PER_PAGE;
        
        $params['sidx'] = !empty($data['sidx'])? $data['sidx'] : 1;
        
        $params['sord'] = !empty($data['sord'])? $data['sord'] : DEFAULTSORTDIR;
        
        if (!empty($data['count'])) {
            $params['totalPages'] = ceil($data['count'] / $params['limit']);
        } else {
            $params['totalPages'] = 0;
        }
        if ($params['page'] > $params['totalPages']) {
            $params['page'] = $params['totalPages'];
        }
        $params['start'] = $params['limit'] * $params['page'] - $params['limit'];
        
        $filters = isset($data['filters'])? $data['filters'] : "";
        $params['condition'] = $this->decodeFilters($filters);
        
        return $params;
    }
    
    
    /**
     * This function is used to control pagination and sorting.
     *
     * Created By : Tech Lead
     * Date : 19 Dec,2013	
     * @param $params array by reference
     *
     * @return void.
     */
    public function getPagingSorting(&$params = array())
    {
        if (!isset($params['page'])) {
            $params['page'] = 1;
        }
        if (!isset($params['rows'])) {
            $params['limit'] = NO_OF_RECORDS_PER_PAGE;
        } else {
            $params['limit'] = $params['rows'];
        }
        if (!isset($params['sidx'])) {
            $params['sidx'] = 1;
        }
        if (!isset($params['sord'])) {
            $params['sord'] = DEFAULTSORTDIR;
        }

        if ($params['count'] > 0 && strtolower($params['limit']) != 'all') {
            $params['totalPages'] = ceil($params['count'] / $params['limit']);
        } else {
            $params['totalPages'] = 1;
        }
//            if ($params['page'] > $params['totalPages']) {
//                $params['page'] = $params['totalPages'];
//            }
        if(!isset($params['result-notfound'])){
            if ($params['page'] > $params['totalPages']) {
                $params['page'] = $params['totalPages'];
            }
        }
        if(strtolower($params['limit']) != 'all'){
            $params['start'] = $params['limit'] * $params['page'] - $params['limit'];
        }else{
            $params['start'] = 0;
        }
    }
    
    
    public function decodeFilters($filters = '', $format = 'json') {
        $conditionArr = array();

        if (empty($filters))
            return $conditionArr;

        switch ($format) {

            case 'json':
            default:
                $conditions = json_decode($filters);
        }

        if (!empty($conditions->rules) && is_array($conditions->rules)) {
            foreach ($conditions->rules as $rule) {
                $conditionArr[$rule->field] = trim($rule->data);
            }
        }
        return $conditionArr;
    }


    /**
     * This function is used return blank array if no result is found.
     * Created By : Tech Lead
     * Date : 19 Dec,2013	
     * @param void
     *
     * @return array.
     */
    public function blankResult()
    {
        return array('result' => array(), 'total' => 0, 'records' => 0, 'page' => 0);
    }
    
    /**
     * This function is used to custom replace from search string.
     * Created By : Tech Lead
     * Date : 16 Aug,2014	
     * @param void
     *
     * @return string.
     */
    public function customFilter($filter = "", $search = array(), $replace = array())
    {
        $ret = "";
        if(empty($search)) {
            $search = array("-", "--");
        }
        if(empty($replace)) {
            $replace = array(" ", " ");
        }
        if(is_string($filter)) {
            $ret = trim(str_replace($search, $replace, $filter));
        }
        
        if(is_array($filter)) {
            foreach($filter as $k=>$fl) {
                $ret[$k] = trim(str_replace($search, $replace, $fl));
            }            
        }
        return $ret;
    }

    /**
     *  Initializing zend database adapter
     * Created By : Tech Lead
     * Date : 19 Dec,2013	
     * @param	 void
     * @return  OBJECT Returns tables.
     */
    protected function getDBAdapter()
    {
        global $application;
        return $application->getBootstrap()->getResource('db');
    }

    /**
     * Setting default database table name for db model instance externally through model object.
     * Created By : Tech Lead
     * Date : 19 Dec,2013	
     * @param	 
     * @return
     */
    public function setDBTableName($name)
    {
        $this->_name = $name;
    }

    /**
     * Getting default database current table name of db model instance externally through model object.
     * Created By : Tech Lead
     * Date : 19 Dec,2013	
     * @param	 
     * @return string Default db model instance current table name
     */
    public function getDBTableName()
    {
        return $this->_name;
    }

  
    public function randomcode($base = null)
    {
        srand(time());
        $str1 = '';
        $str2 = '';
        $i = 1;
        while ($i <= 3) {
            $str1 .= chr((rand() % 26) + 97);
            $str2 .= chr((rand() % 26) + 97);
            $i++;
        }

        return $str1 . $base . $str2;
    }
    
    

}

?>