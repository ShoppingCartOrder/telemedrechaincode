<?php



class My_Acl extends Zend_Acl

{



    public function __construct()

    

    {

        

       $aclModel = new Application_Model_DbTable_Role();

        

        $resourcesArray = $aclModel->fetchAllResources();

        foreach($resourcesArray as $resource) {

           $this->add(new Zend_Acl_Resource($resource['name'])); 

        }

        

        // find all role and add with zend acl.

        

         $rolesArray = $aclModel->fetchAllRole();

         

         foreach($rolesArray as $role) {

            

           $this->addRole(new Zend_Acl_Role($role['name']));

         }

         

         // find user role access

                   $session = new Zend_Session_Namespace('my');

 

                    $userRolesArray = $aclModel->getUserRoles($session->loginId);



         foreach($userRolesArray as $userRole) {

             

            $this->allow($userRole['role'], $userRole['resource']); 

         }

         

    }

    

    public function isAllowed($resource = null ){



        $aclModel = new Application_Model_DbTable_Role();

        $session = new Zend_Session_Namespace('my');

        $role = $aclModel->getRoleByRoleId($session->roleId);

                 return parent::isAllowed($role[0]['role'], $resource);  



     

    }



}  