<?php

/**
 * This document defines Mylib_Auth_Validate class
 * This is BaseController class. It defines all attributes and behaviour that is common to whole application.
 * @author Tech Lead
 * @package Mylib_Library Models
 * @subpackage Mylib_Auth_Validate
 */
class Mylib_Auth_Validate extends Zend_Validate_Abstract {

    /**
     * Defining class constant for error message
     */
    const NOT_AUTHORISED = 'Invalid username or password';

    /**
     * Defining class variables for 
     */
    protected $_authAdapter;
    protected $_messageTemplates = array(
        self::NOT_AUTHORISED => 'No users with those details exist'
    );
    protected $_tableName = null;
    protected $_identityColumn = null;
    protected $_credentialColumn = null;

    /**
     *  Initializing zend auth adapter
     * Created By : Tech Lead
     * Date : 19 Dec,2013
     * @param	 void
     * @return  authAdapter
     */
    public function getAuthAdapter() {
        return $this->_authAdapter;
    }

    /**
     * Initializing zend database 
     * Created By : Tech Lead
     * Date : 19 Dec,2013
     * @param	 $tableName,$identityColumn, $credentialColumn
     * @return void
     */
    public function __construct($tableName, $identityColumn, $credentialColumn) {
        $this->_tableName = $tableName;
        $this->_identityColumn = $identityColumn;
        $this->_credentialColumn = $credentialColumn;
    }

    /**
     * This method is used to check for valid user 
     * Created By : Tech Lead
     * Date : 19 Dec,2013
     * @param	$validationArr array
     * @return boolen
     */
    public function isValid($authArr = null, $objRequest = null) {
        require_once 'Mylib/Acl.php';
        global $application;
        if (empty($authArr['identity']) || empty($authArr['identity'])) {
            return false;
        }

        if ($objRequest !== NULL) {
            $params = $objRequest->getParams();
        }
        $identity = (string) $authArr['identity'];
        $credential = (string) $authArr['credential'];
        $this->_setValue($identity);

        $dbAdapter = $application->getBootstrap()->getResource('db');


        $this->_authAdapter = new Mylib_Auth_Adapter_DbTable($dbAdapter);

        $this->_authAdapter->setTableName($this->_tableName)
                ->setIdentityColumn($this->_identityColumn)
                ->setCredentialColumn($this->_credentialColumn);

        $this->_authAdapter->setIdentity($identity);
        $this->_authAdapter->setCredential(md5($credential));
        $this->_authAdapter->setUserRoleType($params['userRoleType']);
        $auth = Zend_Auth::getInstance();


        $result = $auth->authenticate($this->_authAdapter);


        if (!$result->isValid()) {

            $this->_error(self::NOT_AUTHORISED);
            return false;
        } else {

            $data = $this->_authAdapter->getResultRowObject();

//dd($data);
            if (!empty($params['admin'])) {
                if (empty($data->user_role_type) || $data->user_role_type != ADMIN_ROLE) {
                    dd(3);
                    return false;
                }
                $namespace = 'my';
                $session = new Zend_Session_Namespace($namespace);
//dd($session);
                $data->loginId = $session->loginId = $data->id;

                $data->roleId = $session->roleId = $data->user_role_type;
                
                $data->adminUsers = $session->adminUsers = $data->name;
                

                /* * ***For Last Visit *** */

                $data->lastlogin = $session->lastlogin = $data->last_login;
                $data->loginip = $session->loginip = $data->ip;
                /*                 * **End Here*** */

                //require_once 'My/Acl.php';
                //require_once 'Vivahaayojan/Acl.php';

                //$rolePermObj = new Application_Model_DbTable_Acl();
                //$rolePerm['condition']['role_id'] = $data->role;
                //$data->rolePermissions = $this->processRolePermissions($rolePermObj->getRolePermissions($rolePerm));


                //$data->aclObj = $session->aclObj = new My_Acl();

                //$obj = new Application_Model_DbTable_City();

                //$cityData = $obj->fetchAllCity();

                //$data->city = $cityData[0]['id'];

                //$auth->getStorage()->write($data);

                /*                 * *****For Update Details******* */
                //$adminobj = new Application_Model_DbTable_Dashboard();

                $udata['ip'] = $_SERVER['REMOTE_ADDR'];
                $date = new Zend_Date();
                $udata['last_login'] = $date->toString('YYYY-MM-dd HH:mm:ss');
                //dd($udata);
                //$adminobj->update($udata, 'id = ' . $data->id);
            }else if (!empty($params['doctor'])) {
                if (empty($data->user_role_type) || $data->user_role_type != DOCTOR_ROLE) {
                    return false;
                }
                $namespace = 'mydoctor';
                $session = new Zend_Session_Namespace($namespace);

                $data->loginId = $session->loginId = $data->id;

                $data->roleId = $session->roleId = $data->user_role_type;
                
                $data->doctorUsers = $session->doctorUsers = $data->name;
                

                /* * ***For Last Visit *** */

                //$data->lastlogin = $session->lastlogin = $data->last_login;
                //$data->loginip = $session->loginip = $data->ip;
                /*                 * **End Here*** */

                //require_once 'My/Acl.php';
                //require_once 'Vivahaayojan/Acl.php';
//dd($data);
                //$rolePermObj = new Application_Model_DbTable_Acl();
                //$rolePerm['condition']['role_id'] = $data->role;
                //$data->rolePermissions = $this->processRolePermissions($rolePermObj->getRolePermissions($rolePerm));

//dd($rolePermObj);
                //$data->aclObj = $session->aclObj = new My_Acl();

                //$obj = new Application_Model_DbTable_City();

                //$cityData = $obj->fetchAllCity();

                //$data->city = $cityData[0]['id'];

                //$auth->getStorage()->write($data);

                /*                 * *****For Update Details******* */
                $doctorobj = new Application_Model_DbTable_Doctors();

                
                $arrDoctor = $doctorobj->getUserDetails($data->id);
                
                $data->doctorUsers = $session->doctor_id = $arrDoctor['id'];
                //$data->doctor_id = $arrDoctorId['id'];
                $session->doctorDetails = $arrDoctor;
                
                
                
            }else if (!empty($params['hospital'])) {
                
                if (empty($data->user_role_type) || $data->user_role_type != HOSPITAL_ROLE) {
                    return false;
                }
                $namespace = 'myhospital';
                $session = new Zend_Session_Namespace($namespace);

                $data->loginId = $session->loginId = $data->id;

                $data->roleId = $session->roleId = $data->user_role_type;
                
                $data->hospitalUsers = $session->hospitalUsers = $data->name;
                             
                //$data->lastlogin = $session->lastlogin = $data->last_login;
                $data->loginip = $session->loginip = $data->ip;
                /*                 * **End Here*** */

                $userobj = new Application_Model_DbTable_Users();
                
                $arrUser = $userobj->getUserDetails($data->id);
                //dd($arrPatientId);
                $session->hospital_user_id = $arrUser['id'];
                $session->hospital_user_details = $arrUser;
                //dd($udata);
                //$adminobj->update($udata, 'id = ' . $data->id);
                
            } else if (!empty($params['patient'])) {
                
                if (empty($data->user_role_type) || $data->user_role_type != PATIENT_ROLE) {
                    return false;
                }
                $namespace = 'mypatient';
                $session = new Zend_Session_Namespace($namespace);

                $data->loginId = $session->loginId = $data->id;

                $data->roleId = $session->roleId = $data->user_role_type;
                
                $data->patientUsers = $session->patientUsers = $data->name;
                             
                $data->lastlogin = $session->lastlogin = $data->last_login;
                $data->loginip = $session->loginip = $data->ip;
                /*                 * **End Here*** */

                $patientobj = new Application_Model_DbTable_Patients();
                
                $arrPatient = $patientobj->getUserDetails($data->id);
                //dd($arrPatientId);
                $session->patient_id = $arrPatient['id'];
                $session->patient_details = $arrPatient;
                //dd($udata);
                //$adminobj->update($udata, 'id = ' . $data->id);
                
            }else if (!empty($params['laboratory'])) {
                
                if (empty($data->user_role_type) || $data->user_role_type != LABORATORY_ROLE) {
                    return false;
                }
                
                $namespace = 'mylaboratory';
                $session = new Zend_Session_Namespace($namespace);

                $data->loginId = $session->loginId = $data->id;

                $data->roleId = $session->roleId = $data->user_role_type;
                
                $data->laboratoryUsers = $session->laboratoryUsers = $data->name;
                             
                $data->lastlogin = $session->lastlogin = $data->last_login;
                $data->loginip = $session->loginip = $data->ip;
                /*                 * **End Here*** */

                $labobj = new Application_Model_DbTable_Laboratories();
                
                $arrLab = $labobj->getUserDetails($data->id);
                //dd($arrPatientId);
                $session->lab_id = $arrLab['id'];
                $session->lab_details = $arrLab;
                //dd($udata);
                //$adminobj->update($udata, 'id = ' . $data->id);
                
            }else if (!empty($params['nurse'])) {
                
                if (empty($data->user_role_type) || $data->user_role_type != NURSE_ROLE) {
                    return false;
                }
                
                $namespace = 'mynurse';
                $session = new Zend_Session_Namespace($namespace);

                $data->loginId = $session->loginId = $data->id;

                $data->roleId = $session->roleId = $data->user_role_type;
                
                $data->nurseUsers = $session->nurseUsers = $data->name;
                             
                $data->lastlogin = $session->lastlogin = $data->last_login;
                $data->loginip = $session->loginip = $data->ip;
                /*                 * **End Here*** */

                $nurseobj = new Application_Model_DbTable_Nurses();
                
                $arrNurse = $nurseobj->getUserDetails($data->id);
                //dd($arrPatientId);
                $session->nurse_id = $arrNurse['id'];
                $session->nurse_details = $arrNurse;
                //dd($udata);
                //$adminobj->update($udata, 'id = ' . $data->id);
                
            }else {
                $namespace = 'userNamespace';
                $claimedObj = new Application_Model_DbTable_Claimbusiness();
                $lastClaimedId = $claimedObj->getLastBusiness($data->id);
                if(isset($lastClaimedId['businessid'])){
                    $data->businessId = $lastClaimedId['businessid'];                
                }else{
                    $data->businessId = 0;
                }
                if (empty($data->user_role_type)) {
                    return false;
                }
            }

            //require_once 'Vivahaayojan/Acl.php';
            //$objAcl = MyAcl::getInstance($data);
            //dd($objAcl);
            $auth->getStorage()->write($namespace);
            $storageObj = new Zend_Auth_Storage_Session($namespace);
            $auth->setStorage($storageObj);
            $auth->getStorage()->write($data);
        }

        return true;
    }

    /**
     * process RolePermissions 
     * Created By : Tech Lead
     * Date : 19 Dec,2013
     * @param	 array
     * @return array
     */
    public function processRolePermissions($rolePermissions) {

        $permArr = array();
        foreach ($rolePermissions as $prm) {
            $permArr['role_id'] = $prm['id'];
            $permArr['role_name'] = $prm['name'];
            $permArr['role_status'] = $prm['status'];
            $permArr['created'] = $prm['created'];
            $permArr['is_super_admin'] = $prm['is_super_admin'];
            if (!empty($prm['resource_id'])) {
                $permArr['permissions'][$prm['resource_id']][] = $prm['privillege_id'];
            }
        }

        return $permArr;
    }

}
