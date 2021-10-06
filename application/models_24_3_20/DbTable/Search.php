<?php

/**
 * This document defines Application_Model_DbTable_Users class
 * This is an Users model class. It defines all attributes and behaviour that is used for Users section.
 * @author Bidhan Chandra
 * @package Vivahaayojan Models
 * @subpackage Application_Model_DbTable_Users
 */
class Application_Model_DbTable_Search extends Mylib_Model_BaseModel {

    /**
     * Defining class variable for users table name.
     */
    protected $_name = 'departments';

    /**
     * This function is used to create new user.
     *
     * Created By : Bidhan Chandra 
     * Date : 10 July,2019	
     * @param $params array by reference
     *
     * @return string.
     */
   public function searchBySpecialization($params = array()) {
       
        if (empty($params['condition']['speciality'])) return false;
        $fields = array('*');
        if (!empty($params['fields']['main'])) {
            $fields = $params['fields']['main'];
        }


        $select = $this->getAdapter()->select()
                ->from(array('dr' => 'doctors'), $fields)
                //->useIndex('dr.department') 
                ->joinLeft(array('d' => 'departments'),
                        'dr.department = d.id', array('d.name as dprt_name'));
        
       

        $selectCount = $this->getAdapter()->select()
                ->from(array('dr' => 'doctors'), array("dr.id"))               
                ->joinInner(array('d' => 'departments'),
                'dr.department = d.id', null);              
        
        $where = "1 ";
        $genCond = " AND dr.status = 1 AND d.status = 1  ";
        
        if(!empty($params['condition']['qualification'])) {
            $genCond .= " AND dr.qualification = ".$params['condition']['qualification'];
        }
        if (!empty($params['condition']['speciality'])) {
            $where .= " AND d.name = '" . $params['condition']['speciality'] . "'";
        }
       
    
        $where .= $genCond;

        if (!empty($where)) {
            $selectCount->where($where);
            $select->where($where);
        }

        $selectCount->group('dr.id');
        $select->group('dr.id');
        //echo "<pre>";
        //echo $selectCount; die;
        $resultCount = $this->getAdapter()->fetchCol($selectCount);


        $params['count'] = count($resultCount);
        if (!$params['count']) {
            return $this->blankResult();
        }
        $this->getPagingSorting($params);

        if ($params['sidx'] != 1) {
            
            $select->order($params['sidx'] . " " . $params['sord']);
            
        } else {
                       
            $select->order('dr.name');
            
        }
 //echo $select; die;
        $select->limit($params['limit'], $params['start']);
        
        //echo "<pre>";
        //echo $select; die;
        $result = $this->getAdapter()->fetchAll($select);
        return array(
            'result' => $result,
            'total' => $params['totalPages'],
            'records' => $params['count'],
            'page' => $params['page']
        );
    }
    
    
   

    
}

?>