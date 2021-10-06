<?php

/**
 * This document defines Application_Model_DbTable_Acl class
 * This is an Acl model class. It defines all attributes and behaviour that is used for Acl section.
 * @author Vaibhav
 * @package Vivahaayojan Models
 * @subpackage Application_Model_DbTable_Acl
 */
class Application_Model_DbTable_Acl extends Mylib_Model_BaseModel
{

    protected $_name = 'vy_roles';

    /**
     * This function is used to get all roles for acl.
     * Created By : Vaibhav
     * Date : 22 Dec,2014	
     * @return void.
     */
    public function allroles($params = null)
    {
        if (empty($params))
            return false;

        $fields = array('*');

        if (!empty($params['fields']['main'])) {
            $fields = $params['fields']['main'];
        }

        $select = $this->getAdapter()->select()
                ->from(array('r' => 'vy_roles'), $fields);

        $selectCount = $this->getAdapter()->select()
                ->from(array('r' => 'vy_roles'), array(new Zend_Db_Expr("count(r.id)  AS count")));

        $where = "1 ";

        $genCond = " AND r.id != 1";

        if (!empty($params['condition']['role_type'])) {
            $where .= " AND r.role_type = " . $params['condition']['role_type'];
        }
        if (!empty($params['condition']['name'])) {
            $where .= " AND r.name = '" . $params['condition']['name'] . "'";
        }
        if (!empty($params['condition']['role_id'])) {
            $where .= " AND r.id = '" . $params['condition']['role_id'] . "'";
        }
        $where .= $genCond;

        if (!empty($where)) {
            $selectCount->where($where);
            $select->where($where);
        }

        $resultCount = $this->getAdapter()->fetchCol($selectCount);

        $params['count'] = $resultCount[0];
        if (!$params['count']) {
            return $this->blankResult();
        }
        $this->getPagingSorting($params);

        $select->order($params['sidx'] . " " . $params['sord']);

        $select->limit($params['limit'], $params['start']); 
        $result = $this->getAdapter()->fetchAll($select);
        return array(
            'result' => $result,
            'total' => $params['totalPages'],
            'records' => $params['count'],
            'page' => $params['page'],
            'resultCount' => count($result)
        );
    }

    /**
     * This function is used to get all resources for roles.
     * Created By : Vaibhav
     * Date : 24 Dec,2014	
     * @return void.
     */
    public function allResources($params = null)
    {
        $fields = array('*');

        if (!empty($params['fields']['main'])) {
            $fields = $params['fields']['main'];
        }

        $select = $this->getAdapter()->select()
                ->from(array('mre' => 'vy_resources'), $fields)
                ->joinLeft(array('mrr' => 'vy_role_resources'), 'mre.id = mrr.resource_id', array('role_id as commonRoleId', 'resource_id as commonResourceId'));

        $selectCount = $this->getAdapter()->select()
                ->from(array('mre' => 'vy_resources'), array(new Zend_Db_Expr("count(mre.id)  AS count")))
                ->joinLeft(array('mrr' => 'vy_role_resources'), 'mre.id = mrr.resource_id', null);

        $where = "1 ";

        $genCond = "";

        if (!empty($params['condition']['role_id'])) {
            $where .= " AND mr.id = " . $params['condition']['role_id'];
        }

        $where .= $genCond;

        if (!empty($where)) {
            $selectCount->where($where);
            $select->where($where);
        }
        $select = $select->group('mre.id');
        $selectCount = $selectCount->group('mre.id');

        $resultCount = $this->getAdapter()->fetchCol($selectCount);

        $params['count'] = $resultCount[0];
        if (!$params['count']) {
            return $this->blankResult();
        }
        $this->getPagingSorting($params);

        $select->order($params['sidx'] . " " . $params['sord']);

        $select->limit($params['limit'], $params['start']);
        $result = $this->getAdapter()->fetchAll($select);
        return array(
            'result' => $result,
            'total' => $params['totalPages'],
            'records' => $params['count'],
            'page' => $params['page'],
            'resultCount' => count($result)
        );
    }

    /**
     * This function is used to get update roles status for acl.
     * Created By : Vaibhav
     * Date : 22 Dec,2014	
     * @return void.
     */
    public function updateRoles($params = null)
    {
        if (empty($params))
            return false;

        if (isset($params['id'])) {
            $id = $params['id'];
            unset($params['id']);
        }
        if (isset($params['name']) && !isset($id)) {
            $this->getAdapter()->insert($this->_name, $params);
        } else {
            $this->getAdapter()->update($this->_name, $params, 'id = ' . $id);
        }
    }

    /**
     * This function is used to update roles status for acl.
     * Created By : Vaibhav
     * Date : 22 Dec,2014	
     * @return void.
     */
    public function allPrivileges($params = null)
    {
        if (isset($params['condition']['role_id'])) {
            $where = 'r.id = ' . $params['condition']['role_id'];
            $sql = $this->getAdapter()->select()
                    ->from(array('r' => 'vy_roles'), null)
                    ->joinInner(array('rp' => 'role_resource_privillege'), 'rp.role_id = r.id', array('resource_id', 'privillege_id'))
                    ->where($where);
        } else {
            $sql = $this->getAdapter()->select('*')->from('privilleges');
        }
        $result = $this->getAdapter()->fetchAll($sql);
        return $result;
    }
    
    /**
     * This function is used to update roles status for acl.
     * Created By : techlead
     * Date : 22 Dec,2014	
     * @return void.
     */
    public function allResourcesOnly($params = null)
    {
        $sql = $this->getAdapter()->select('*')->from('vy_resources');
        $result = $this->getAdapter()->fetchAll($sql);
        return $result;
    }

    /**
     * This function is used to fetch role and permission details.
     * Created By : TechLead
     * Date : 22 Dec,2014	
     * @return void.
     */
    public function getRolePermissions($params = null)
    {
        $where = "1 ";
        if(!empty($params['condition']['role_id'])) {
            $where .= ' AND r.id = ' . $params['condition']['role_id'];
        }
        
        $sql = $this->getAdapter()->select()
                ->from(array('r' => 'vy_roles'))
                ->joinLeft(array('rp' => 'role_resource_privillege'), 'rp.role_id = r.id', array('role_res_id','resource_id', 'privillege_id'))
                ->where($where);
        //echo $sql;exit;
        $result = $this->getAdapter()->fetchAll($sql);
        return $result;
    }
    
     /**
     * This function is used to fetch controller action details.
     * Created By : TechLead
     * Date : 22 Dec,2014	
     * @return void.
     */
    public function fetchControllerAction($params = null)
    {
        //dd('hi');
        $where = "1 ";
        
        
        $sql = $this->getAdapter()->select()
                ->from(array('cnt' => 'vy_controller_action'));
		
		if(!empty($params['condition']['controller'])) {
			$sql->where('cnt.controller_name = ?', $params['condition']['controller']);
            //$where .= " AND cnt.controller_name = '" . $params['condition']['controller']."'";
        }
        if(!empty($params['condition']['action'])) {
			$sql->where('cnt.action_name = ?', $params['condition']['action']);
            //$where .= " AND cnt.action_name = '" . $params['condition']['action']."'";
        }
        if(!empty($params['condition']['extra_params'])) {
			$sql->where('cnt.extra_params = ?', $params['condition']['extra_params']);
            //$where .= " AND cnt.extra_params = '" . $params['condition']['extra_params']."'";
        }
		
              //  $sql->where($where);
        //echo $sql;exit;
        $result = $this->getAdapter()->fetchAll($sql);
        return $result;
    }

    /**
     * This function is used to insert resources privileges for acl.
     * Created By : Vaibhav
     * Date : 26 Dec,2014	
     * @return void.
     */
    public function insertResourcesPrivileges($params = null)
    {
        if (empty($params))
            return false;
        $condition = array('role_id = ?' => $params['role_id']);
        $this->getAdapter()->delete('role_resource_privillege', $condition);
        $roleId = $params['role_id'];
        unset($params['role_id']);
        for ($key = 0; $key < count($params); $key++) {
            $expriveleges = explode('---', $params[$key]);
            $exprivillegeVal = explode(',', trim($expriveleges[1]));
            for ($ex = 0; $ex < count($exprivillegeVal); $ex++) {
                if (isset($exprivillegeVal[$ex]) && ($exprivillegeVal[$ex] > 0)) {
                    $exData = array();
                    $exData['role_id'] = $roleId;
                    $exData['resource_id'] = $expriveleges[0];
                    $exData['privillege_id'] = $exprivillegeVal[$ex];
                    $this->getAdapter()->insert('role_resource_privillege', $exData);
                }
            }
        }
    }

    /**
     * This function is used to delete role for acl.
     * Created By : Vaibhav
     * Date : 27 Dec,2014	
     * @return void.
     */
    public function deleteRole($params = null)
    {
        $roleCondition = array('id = ?' => $params['roleId']);
        $this->getAdapter()->delete('vy_roles', $roleCondition);
        $roleResourceCondition = array('role_id = ?' => $params['roleId']);
        $this->getAdapter()->delete('role_resource_privillege', $roleResourceCondition);
    }

    /**
     * This function is used to add/update resources for acl.
     * Created By : Vaibhav
     * Date : 29 Dec,2014	
     * @return void.
     */
    public function addUpdateResources($params = null)
    {
        if (empty($params))
            return false;

        $checkResource = count($this->checkResourceExist($params));
        if ($checkResource <= 0) {
            if (isset($params['id'])) {
                $id = $params['id'];
                unset($params['id']);
                $resourceCondition = array('id = ?' => $id);
                $resource = $this->getAdapter()->update('vy_resources', $params, $resourceCondition);
            } else {
                $resource = $this->getAdapter()->insert('vy_resources', $params);
            }
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * This function is used to check resources if already exist for acl.
     * Created By : Vaibhav
     * Date : 29 Dec,2014	
     * @return void.
     */
    protected function checkResourceExist($param = null)
    {
        if (empty($param))
            return false;

        $sql = $this->getAdapter()->select('id')->from('vy_resources');
        if (isset($param['name'])) {
            $sql = $sql->where("name = '" . $param['name'] . "'");
        }
        if (isset($param['id'])) {
            $sql = $sql->where("id != " . $param['id']);
        }
        $record = $this->getAdapter()->fetchAll($sql);
        return $record;
    }

}
