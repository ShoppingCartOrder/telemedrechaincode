<?php

/**
 * This document defines Application_Model_DbTable_Users class
 * This is an Users model class. It defines all attributes and behaviour that is used for Users section.
 * @author Bidhan Chandra
 * @package Vivahaayojan Models
 * @subpackage Application_Model_DbTable_Users
 */
class Application_Model_DbTable_ChargeItemName extends Mylib_Model_BaseModel {

    /**
     * Defining class variable for users table name.
     */
    protected $_name = 'hms_charge_item_name';

    public function fetchAllChargeCategory() {


        try {
            $db = $this->getAdapter();
            $query = $db->select()
                    ->from(array('cc' => 'hms_charge_category'), array('*'));
            //->joinInner(array('ci'=>'hms_charge_item_name'),'ci.category_id = cc.id',array('ci.id AS item_id','ci.sub_category_name','ci.charge'));
            // echo $query; die;

            return $db->fetchAll($query);
        } Catch (Exception $e) {

            echo $e->getMessage();
        }
    }

    public function fetchAllItemCharge() {


        try {
            $db = $this->getAdapter();
            $query = $db->select()
                    ->from(array('cc' => 'hms_charge_category'), array('*'))
                    ->joinInner(array('ci' => 'hms_charge_item_name'), 'ci.category_id = cc.id', array('ci.id AS item_id', 'ci.sub_category_name', 'ci.charge'));

            // echo $query; die;

            return $db->fetchAll($query);
        } Catch (Exception $e) {

            echo $e->getMessage();
        }
    }

    public function getItemsByCatId($id) {

        if (!empty($id)) {

            $select = $this->getAdapter()->select()
                    //->from(array('cc' => 'hms_charge_category'), array('*'))
                    //->useIndex('dr.department') 
                    ->from(array('ci' => 'hms_charge_item_name'), array('ci.id AS item_id', 'ci.sub_category_name', 'ci.charge'))
                    ->where('ci.category_id = ?', $id);

//echo $select;
            return $this->getAdapter()->fetchAll($select);
        } else {
            return Array('name' => 'NA');
        }
    }

    public function fetchPatientCharges($patient_id, $rId) {

        if (!empty($patient_id)) {
            try {

                $db = $this->getAdapter();
                $query = $db->select()
                        ->from(array('pc' => 'hms_patient_charges'), array('*'))
                        ->joinInner(array('ci' => 'hms_charge_item_name'), 'pc.charge_item_id = ci.id', array('ci.id AS item_id', 'ci.sub_category_name'))
                        ->where('pc.patient_id = ?', $patient_id)
                        ->where('pc.registration_no = ?', $rId)
                        ->order('id DESC');
                //echo $query; die;

                return $db->fetchAll($query);
            } Catch (Exception $e) {

                echo $e->getMessage();
            }
        }
    }

    public function getItemsChargeByItemId($id) {

        if (!empty($id)) {

            $select = $this->getAdapter()->select()
                    //->from(array('cc' => 'hms_charge_category'), array('*'))
                    //->useIndex('dr.department') 
                    ->from(array('ci' => 'hms_charge_item_name'), array('ci.id AS item_id', 'ci.sub_category_name', 'ci.charge', 'ci.flag'))
                    ->where('ci.id = ?', $id);

//echo $select;
            return $this->getAdapter()->fetchRow($select);
        }
    }

    public function insertChargesDetails($chargessData, $total_extra_fields) {
        //print_r($extra_fields_data); die;
        if ($chargessData && $total_extra_fields) {
            $values = implode(',', array_fill(0, $total_extra_fields, '(?,?,?,?,?,?,?,?,?,?)'));
            $sql = "INSERT INTO hms_patient_charges (`patient_id` ,`registration_no`,`appointment_id`,`bill_no`,`charge_category_id` ,`charge_item_id` ,`quantity` ,`amount`,`discount`,`net_amount`) VALUES $values";

            $stmt = $this->getAdapter()->prepare($sql);

            return $stmt->execute($chargessData);
        } else {
            return false;
        }
    }

    /*
      public function updateChargeDetails($chargessData,$total_extra_fields){

      if($chargessData && $total_extra_fields){
      $values = implode(',',array_fill(0,$total_extra_fields,'(?,?,?,?,?,?,?,?,?,?)'));
      $sql = "INSERT INTO hms_patient_charges (`id`,`patient_id` ,`registration_no` ,`bill_no` ,`charge_category_id`,`charge_item_id` ,`quantity` ,`amount`,`discount`,`net_amount`)"
      . " VALUES $values"." ON DUPLICATE KEY UPDATE patient_id = VALUES(patient_id),registration_no = VALUES(registration_no),bill_no = VALUES(bill_no),charge_category_id = VALUES(charge_category_id)"
      . ",charge_item_id = VALUES(charge_item_id),quantity = VALUES(quantity),amount = VALUES(amount),discount = VALUES(discount),net_amount = VALUES(net_amount)";


      //echo $sql; die;
      $stmt = $this->getAdapter()->prepare($sql);
      //dd($extra_fields_data);
      $stmt->execute($chargessData);

      //die;

      }else{
      return false;
      }
      }
     */

    public function deleteChargedDetails($data) {

        if ($data) {

            $db = $this->getAdapter();
            $condition = array('id IN(?)' => $data);
            return $sucess = $db->delete('hms_patient_charges', $condition);
            //return true;
        } else {
            return false;
        }
    }

    public function insertTotalChargeAmount($data) {
        try {

            $this->getAdapter()->insert('hms_patient_total_charges', $data);
            $lastInsertId = $this->getAdapter()->lastInsertId();
            return $lastInsertId;
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }

    public function updateTotalChargeAmount($data) {

        if (!$data['id']) {
            return false;
        }
        $dbdata = array(
            'total_amount' => $data['total_amount'],
            'payment_method' => $data['payment_method'],
            'remark' => $data['remark']
        );


        $where['patient_id =?'] = $data['patient_id'];
        $where['registration_no =?'] = $data['registration_no'];
        $where['bill_no =?'] = $data['bill_no'];

        return $this->getAdapter()->update($this->_name, $dbdata, $where);
    }

    /*
      public function updateTotalChargeAmountDetails($totalAmountData,$total_extra_fields){

      if($totalAmountData && $total_extra_fields){
      $values = implode(',',array_fill(0,$total_extra_fields,'(?,?,?,?,?,?)'));
      $sql = "INSERT INTO hms_patient_total_charges (`patient_id` ,`registration_no` ,`bill_no` ,`total_amount`,`payment_method` ,`remark`)"
      . " VALUES $values"." ON DUPLICATE KEY UPDATE patient_id = VALUES(patient_id),registration_no = VALUES(registration_no),bill_no = VALUES(bill_no),total_amount = VALUES(total_amount)"
      . ",payment_method = VALUES(payment_method),remark = VALUES(remark)";


      //echo $sql; die;
      $stmt = $this->getAdapter()->prepare($sql);
      //dd($extra_fields_data);
      $stmt->execute($totalAmountData);

      //die;

      }else{
      return false;
      }
      } */

    public function updateTotalChargeAmountDetails($totalAmountData) {

        // dd($totalAmountData);
        if ($totalAmountData) {
            $db = $this->getAdapter();
            $colStr = '';
            $colVal = '';
            $i = 1;
            $cnt = count($totalAmountData);

            //dd($totalAmountData);

            $patientId = "'" . $totalAmountData['patient_id'] . "'";
            $registrationNo = "'" . $totalAmountData['registration_no'] . "'";
            $appointmentId = "'" . $totalAmountData['appointment_id'] . "'";
            $billNo = "'" . $totalAmountData['bill_no'] . "'";
            $totalAmount = "'" . $totalAmountData['total_amt'] . "'";
            $paymentMethod = "'" . $totalAmountData['payment_method'] . "'";
            $remark = "'" . $totalAmountData['remark'] . "'";
            $totalPaidAmount = "'" . $totalAmountData['total_paid_amount'] . "'";
            


            $sql = "INSERT INTO hms_patient_total_charges (`patient_id` ,`registration_no` ,`appointment_id` ,`bill_no`,`total_amount`,`total_paid_amount`,`payment_method` ,`remark`)"
                    . " VALUES ($patientId,$registrationNo,$appointmentId,$billNo,$totalAmount,$totalPaidAmount,$paymentMethod,$remark)" . " ON DUPLICATE KEY UPDATE total_amount = $totalAmount"
                    . ",total_paid_amount = $totalPaidAmount,payment_method = $paymentMethod,remark = $remark";


            //echo $sql; die;
            $stmt = $this->getAdapter()->prepare($sql);
            //dd($extra_fields_data); 
            $stmt->execute($totalAmountData);

            //die;
        } else {
            return false;
        }
    }

    public function fetchPatientChargeIds($patient_id, $rId) {

        if (!empty($patient_id)) {
            try {

                $db = $this->getAdapter();
                $query = $db->select()
                        ->from(array('pc' => 'hms_patient_charges'), array('id'))
                        //->joinInner(array('ci'=>'hms_charge_item_name'),'pc.charge_item_id = ci.id',array('ci.id AS item_id','ci.sub_category_name'))
                        ->where('pc.patient_id = ?', $patient_id)
                        ->where('pc.registration_no = ?', $rId);
                //echo $query; die;

                return $db->fetchAll($query);
            } Catch (Exception $e) {

                echo $e->getMessage();
            }
        }
    }

    public function updateChargeBillNo($data) {

        if (!$data['id']) {
            return false;
        }
        $dbdata = array(
            'bill_no' => $data['bill_no']
        );

        $condition = array('id IN(?)' => $data);

        return $this->getAdapter()->update('hms_patient_charges', $dbdata, $where);
    }

    public function updateChargeDetails($chargessData, $total_extra_fields) {
        //dd($chargessData); 
        if ($chargessData && $total_extra_fields) {
            $values = implode(',', array_fill(0, $total_extra_fields, '(?,?,?,?,?,?,?,?,?,?,?)'));
            $sql = "INSERT INTO hms_patient_charges (`id`,`patient_id` ,`registration_no` ,`appointment_id` ,`bill_no` ,`charge_category_id`,`charge_item_id` ,`quantity` ,`amount`,`discount`,`net_amount`)"
                    . " VALUES $values" . " ON DUPLICATE KEY UPDATE patient_id = VALUES(patient_id),registration_no = VALUES(registration_no),appointment_id = VALUES(appointment_id),bill_no = VALUES(bill_no),charge_category_id = VALUES(charge_category_id)"
                    . ",charge_item_id = VALUES(charge_item_id),quantity = VALUES(quantity),amount = VALUES(amount),discount = VALUES(discount),net_amount = VALUES(net_amount)";


            // echo $sql; die;
            $stmt = $this->getAdapter()->prepare($sql);
            //dd($extra_fields_data); 
            $stmt->execute($chargessData);

            //die;
        } else {
            return false;
        }
    }

    public function fetchPatientBillCharges($data = array()) {

        if (!empty($data)) {
            try {

                $db = $this->getAdapter();
                $query = $db->select()
                        ->from(array('pc' => 'hms_patient_charges'), array('patient_id', 'registration_no', 'bill_no', new Zend_Db_Expr("SUM(net_amount) AS total_amount")))
                        ->where('pc.patient_id = ?', $data['patient_id'])
                        ->where('pc.registration_no = ?', $data['registration_no'])
                        ->where('pc.bill_no = ?', $data['bill_no'])
                        ->group('pc.patient_id')
                        ->group('pc.registration_no')
                        ->group('pc.bill_no');

                //echo $query; die;

                return $db->fetchRow($query);
            } Catch (Exception $e) {

                echo $e->getMessage();
            }
        }
    }

    public function deleteTotalBillChargedDetails($data) {

        if ($data) {

            $db = $this->getAdapter();
            $condition = array('patient_id = ?' => $data['patient_id'], 'registration_no = ?' => $data['registration_no'], 'bill_no = ?' => $data['bill_no']);
            return $sucess = $db->delete('hms_patient_total_charges', $condition);
        } else {
            return false;
        }
    }

    public function updateTotalChargeNetAmount($data = array(), $cond = array()) {


        if (!empty($data) && !empty($cond)) {
            //dd($cond);
            $patient_id = $cond['patient_id'];
            $registration_no = $cond['registration_no'];
            $bill_no = $cond['bill_no'];
            
            $db = $this->getAdapter();
            $total_amount = $data['total_amount'];
            
            $sql = "UPDATE hms_patient_total_charges set total_amount = $total_amount where patient_id = $patient_id AND registration_no = $registration_no AND bill_no = '$bill_no'";
            //echo $sql; die;
            return $sucess = $db->query($sql);
            
            
            
            
        }else{
            return false;
        }
    }
    public function getLastId(){
        
        $db = $this->getAdapter();
                $query = $db->select()
                        ->from(array('pc' => 'hms_patient_charges'), array(new Zend_Db_Expr("MAX(id) AS last_id")));
                        
                        
       $lastId = $this->getAdapter()->fetchRow($query);
       return $lastId['last_id']+1; 
    }
    
     public function fetchPatientChargesByBillNo($patient_id, $rId,$billNo) {

        if (!empty($patient_id)) {
            try {

                $db = $this->getAdapter();
                $query = $db->select()
                        ->from(array('pc' => 'hms_patient_charges'), array('*'))
                        ->joinInner(array('ci' => 'hms_charge_item_name'), 'pc.charge_item_id = ci.id', array('ci.id AS item_id', 'ci.sub_category_name'))
                        ->where('pc.patient_id = ?', $patient_id)
                        ->where('pc.registration_no = ?', $rId)
                        ->where('pc.bill_no = ?', $billNo)
                        ->order('id DESC');
                //echo $query; die;

                return $db->fetchAll($query);
            } Catch (Exception $e) {

                echo $e->getMessage();
            }
        }
    }
    
     public function fetchPatientTotalChargesByBillNo($patient_id, $rId,$billNo) {

        if (!empty($patient_id)) {
            try {

                $db = $this->getAdapter();
                $query = $db->select()
                        ->from(array('pt' => 'hms_patient_total_charges'), array('*'))
                        ->where('pt.patient_id = ?', $patient_id)
                        ->where('pt.registration_no = ?', $rId)
                        ->where('pt.bill_no = ?', $billNo)
                        ->order('id DESC');
                //echo $query; die;

                return $db->fetchRow($query);
            } Catch (Exception $e) {

                echo $e->getMessage();
            }
        }
    }
    
    public function fetchLastAppointmentCharge($patientId,$rId,$appntDate) {

        if (!empty($patientId)) {
            
            try {
                
               
                $date = date('Y-m-d', strtotime($appntDate. ' -'.PAID_APPOINTMENT_VALID_DAYS));

                           
                $db = $this->getAdapter();
                $query = $db->select()
                        ->from(array('pc' => 'hms_patient_charges'), array('*'))
                        //->joinInner(array('ci'=>'hms_charge_item_name'),'pc.charge_item_id = ci.id',array('ci.id AS item_id','ci.sub_category_name'))
                        ->where('pc.patient_id = ?', $patientId)
                        ->where('pc.registration_no = ?', $rId)
                        ->where('pc.appointment_id > ?', 0)
                        ->where('pc.charge_item_id = ?', 2)
                        ->where('pc.bill_date >=?', $date)
                        ->order('bill_date DESC')
                        ->limit(1, 0);
                //echo $query; die;

                return $db->fetchRow($query);
            } Catch (Exception $e) {

                echo $e->getMessage();
            }
        }
    }
    
    public function fetchPrevoiusPaidAppointmentDetails($patientId,$rId) {

        if (!empty($patientId)) {
            
            try {
             
                $db = $this->getAdapter();
                $query = $db->select()
                        ->from(array('pc' => 'hms_patient_charges'), array('*'))
                        //->joinInner(array('ci'=>'hms_charge_item_name'),'pc.charge_item_id = ci.id',array('ci.id AS item_id','ci.sub_category_name'))
                        ->where('pc.patient_id = ?', $patientId)
                        ->where('pc.registration_no = ?', $rId)
                        ->where('pc.appointment_id > ?', 0)
                        ->where('pc.charge_item_id = ?', 2)
                        //->where('pc.bill_date >=?', $date)
                        ->order('bill_date DESC')
                        ->limit(1, 0);
                //echo $query; die;

                return $db->fetchRow($query);
            } Catch (Exception $e) {

                echo $e->getMessage();
            }
        }
    }
    
     public function allCharges($params = null)
    {
        if (empty($params))
            return false;

        $fields = array('*');

        if (!empty($params['fields']['main'])) {
            $fields = $params['fields']['main'];
        }

        $select = $this->getAdapter()->select()
                ->from(array('pc' => 'hms_patient_charges'), $fields)
                ->joinInner(array('p' => 'hms_patient_registration'), 'pc.patient_id = p.id AND pc.registration_no = p.registration_no', array('name as name'))
                ->joinLeft(array('a' => 'hms_appointments'), 'pc.appointment_id = a.id', array('patient_type'))
                ->joinInner(array('cc' => 'hms_charge_category'), 'pc.charge_category_id = cc.id', array('cc.category_name'))
                ->joinInner(array('ci' => 'hms_charge_item_name'), 'pc.charge_item_id = ci.id', array('ci.sub_category_name'));
                

                //->joinLeft(array('pr' => 'prescription'), 'a.id = pr.appnt_id', array(new Zend_Db_Expr("count(pr.id)  AS Prescription_count")));

        $selectCount = $this->getAdapter()->select()
                ->from(array('pc' => 'hms_patient_charges'), array(new Zend_Db_Expr("count(pc.id)  AS count")))
                ->joinInner(array('p' => 'hms_patient_registration'), 'pc.patient_id = p.id AND pc.registration_no = p.registration_no', null)
                ->joinLeft(array('a' => 'hms_appointments'), 'pc.appointment_id = a.id', null)
                ->joinInner(array('cc' => 'hms_charge_category'), 'pc.charge_category_id = cc.id', null)
                ->joinInner(array('ci' => 'hms_charge_item_name'), 'pc.charge_item_id = ci.id', null);
                
            


        
        
        $where = "1 ";
        
  

        $genCond = " ";
       
        
       
        
      if (!empty($params['appntid'])) {
            $where .= " AND a.id = '" . $params['appntid'] . "'";
        }
       
        if (!empty($params['condition']['appnt_id'])) {
            $where .= " AND a.id = '" . $params['condition']['appnt_id'] . "'";
        }
        
        if (!empty($params['condition']['registration_no'])) {
            $where .= " AND a.registration_no = '" . $params['condition']['registration_no'] . "'";
        }
               
     
    
        
      
       
        
        $where .= $genCond;

        if (!empty($where)) {
            $selectCount->where($where);
            $select->where($where);
        }

        //$selectCount->group('a.id');
        //$select->group('a.id');
        //echo $selectCount; die;
        $resultCount = $this->getAdapter()->fetchCol($selectCount);
        
        if(isset($resultCount[0])){
            $params['count'] = $resultCount[0];
            
        }else{
            $params['count'] = 0;
        }
        
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

}

?>