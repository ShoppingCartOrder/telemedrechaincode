<?php

/**
 * This is MyAcl class. This class will
 * execute all the request for permission .
 * @author dev3
 * @package 
 * @subpackage
 */
//require_once(APPLICATION_PATH.'/modules/default/models/Permission.php');
class MyAcl extends Zend_Acl
{

    protected static $_instance = null;
    public $ACL = null;

    private function __construct()
    {
        
    }

    private function __clone()
    {
        
    }

    /**
     * This method is used to initialize the acl object and runs the permission query
     * Created By : Dev3
     * Date : 26 april,2010
     * @param an modulename as str
     * @return an acl object
     */
    protected function _initialize($userData)
    {
        if (isset($userData->ACL)) {
            return $userData->ACL;
        }

        $objPermission = new Application_Model_DbTable_Acl();
        $acl = new Zend_Acl();
        $resources = $objPermission->allResourcesOnly();
        if (!empty($resources)) {
            foreach ($resources as $resource) {
                $acl->add(new Zend_Acl_Resource($resource['id']));
            }
        }
        //$roles = $objPermission->getRoleList(array('RL_ID' => $userData->RL_ID,'is_superAdmin'=>"'0','1'"));

        //dd($userData);
        $userData->roleId = $userData->role;
        $acl->addRole(new Zend_Acl_Role($userData->roleId));

        $permissions = $objPermission->allPrivileges();
        $resPermissionType = array();
        $permissionVals = array();
        $permissionType = array();
        foreach ($permissions as $permission) {
            $permissionVals[] = $permission['priv_id'];
        }

        if (!empty($userData->rolePermissions['is_super_admin'])) {
            foreach ($resources as $resource) {
                $permissionType[$resource['id']] = $permissionVals;
                $acl->allow($userData->roleId, $resource['id'], $permissionVals);
            }
        } else {
            if(!empty($userData->rolePermissions['permissions'])) {
                $permissionType = $permissions = $userData->rolePermissions['permissions'];
                foreach($permissions as $kprm=>$prm) {
                    $acl->allow($userData->roleId, $kprm, $prm);
                }
            }
            
        }

        $userData->permissionType = $permissionType;
        $userData->ACL = $acl;

        return $acl;
    }

    /**
     * This method is used to initialize the acl object and runs the permission query
     * Created By : Dev3
     * Date : 26 april,2010
     * @param an modulename as str
     * @return an acl object
     */
    public static function getInstance($userData)
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
            self::$_instance->ACL = self::$_instance->_initialize($userData);
        }
        return self::$_instance;
    }

}

?>