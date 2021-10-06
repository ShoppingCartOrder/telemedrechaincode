<?php

/**
 * This document defines Application_Model_DbTable_Dashboard class
 * This is a Dashboard model class. It defines all attributes and behaviour that is used for Dashboard section.
 * @author Bidhan Chandra
 * @package Weddingplz Models
 * @subpackage Application_Model_DbTable_Dashboard
 */

class Application_Model_DbTable_Dashboard extends Mylib_Model_BaseModel {
	
	/**
     * Defining class variable for checklist category table name.
    */
    protected $_name = 'wp_checklist_category';
	
	/**
     * This function is used to get budget amount of login user.
     *
     * Created By : Bidhan Chandra
     * Date : 04 Jan,2014	
     * @param $params array by reference
     *
     * @return array.
     */
	public function userBudget($userId)
	{
	   $db = $this->getAdapter();
	   $query = $db->select()
	             ->from('wp_budget_amount','*')
				 ->Where('login_user_id=?',$userId);  
	   $select = $db->fetchAll($query); 
 	   return $select;  
	}
	
	/**
     * This function is used to get checklist items of login user.
     *
     * Created By : Bidhan Chandra
     * Date : 04 Jan,2014	
     * @param $params array by reference
     *
     * @return array.
     */
    public function checkbox_countChecklist($userId)
	{
        //count total check box query here
        $db = $this->getAdapter();
        $query = $db->select()
                ->from('wp_checklist_items', array('count(check_item) AS check_item'))
                ->where('user_id = ?', $userId);
        $select = $db->fetchAll($query);
        return $select;
    }

	/**
     * This function is used to get active checklist items of login user.
     *
     * Created By : Bidhan Chandra
     * Date : 04 Jan,2014	
     * @param $params array by reference
     *
     * @return array.
     */
	public function dueInDaysChecklist($userId) {
        $db = $this->getAdapter();
		$date = date("Y-m-d");
        $query = $db->select()
                ->from('wp_checklist_items', array('count(check_item) AS check_item'))
                ->where('active = ?', 1)
                ->where('user_id = ?', $userId);
			     $select = $db->fetchAll($query);
	     return $select;
    }

	/**
     * This function is used to get non active checklist items of login user.
     *
     * Created By : Bidhan Chandra
     * Date : 04 Jan,2014	
     * @param $params array by reference
     *
     * @return array.
     */
	public function checkbox_count_valueChecklist($userId) {
        $db = $this->getAdapter();
        $query = $db->select()
                ->from('wp_checklist_items', array('count(check_item) AS check_item'))
                ->where('active = ?', 0)
                ->where('user_id = ?', $userId);
        $select = $db->fetchAll($query);
        return $select;
    }

	/**
     * This function is used to get active checklist items before due date of login user.
     *
     * Created By : Bidhan Chandra
     * Date : 04 Jan,2014	
     * @param $params array by reference
     *
     * @return array.
     */
	public function overduetask($userId) {
        $db = $this->getAdapter();
        $query = $db->select()
                ->from('wp_checklist_items', array('count(check_item) AS duedateitem'))
                ->where('user_id = ?', $userId)
		->where('active = ?', 1)
		->where('due_date <=?',date("Y-m-d"));
        $select = $db->fetchAll($query);
        return $select;
    }
	
	/**
     * This function is used to get budget amount of login user.
     *
     * Created By : Bidhan Chandra
     * Date : 04 Jan,2014	
     * @param $params array by reference
     *
     * @return array.
     */
	public function getnewupamountBudget($userid) 
	{
		$db = $this->getAdapter();
		$query = $db->select()
				->from('wp_budget_amount', '*')
				->where("login_user_id=?", $userid);
        $select = $db->fetchAll($query);
        return $select;
    }
	
	/**
     * This function is used to get budget details of login user.
     *
     * Created By : Bidhan Chandra
     * Date : 10 Jan,2014	
     * @param $params array by reference
     *
     * @return array.
     */
    public function sum_toatlBudget($userId) 
	{
		$db = $this->getAdapter();
		$query = $db->select()
				->from('wp_budget_detals', array('sum(suggested) AS suggested', 'sum(actual) AS actual', 'sum(deposit) AS deposit', 'sum(paid) AS paid', 'sum(due) AS due'))
				->where('user_id = ?', $userId);
        $select = $db->fetchAll($query);
		return $select;
    }
	
	/**
     * This function is used to get total guset count of login user.
     *
     * Created By : Bidhan Chandra
     * Date : 10 Jan,2014	
     * @param $params array by reference
     *
     * @return array.
     */
    public function totalGuestCount($userId)
	{
	 	$db = $this->getAdapter();
        $query = $db->select()
                ->from('wp_mywedding_guest', '*')
                ->Where('user_id=?', $userId);
        $select = $db->fetchAll($query);
	    return $select;
	}
	 
	/**
     * This function is used to get user details of login user.
     *
     * Created By : Bidhan Chandra
     * Date : 10 Jan,2014	
     * @param $params array by reference
     *
     * @return array.
     */
	public function myWeddingDate($userId)
	{
	 	$db = $this->getAdapter();
        $query = $db->select()
                ->from('mybb_users', '*')
                ->Where('id=?', $this->userSession->id);  
        $select = $db->fetchAll($query);
	    return $select;
	}

	/**
     * This function is used to get total guests invited of login user.
     *
     * Created By : Bidhan Chandra
     * Date : 10 Jan,2014	
     * @param $params array by reference
     *
     * @return array.
     */
	public function totalGuestInvited($userId)
	{
		$db = $this->getAdapter();
		$query = $db->select()
                ->from('wp_mywedding_guest', '*')
                ->Where('user_id=?', $userId)
				->Where('email_status=?', 1);
        $select = $db->fetchAll($query);
	    return $select;
	}

	/**
     * This function is used to get saved vendors of login user.
     *
     * Created By : Bidhan Chandra
     * Date : 10 Jan,2014	
     * @param $params array by reference
     *
     * @return array.
     */
	 public function totalSavedVendors($userId)
	 {
 	   $db = $this->getAdapter();
	   $query = $db->select()
	             ->from('wp_my_vendors','*')
				 ->Where('user_id=?',$userId);  
	   $select = $db->fetchAll($query); 
	   return $select; 
	}

	/**
     * This function is used to get reviewed vendors of login user.
     *
     * Created By : Bidhan Chandra
     * Date : 10 Jan,2014	
     * @param $params array by reference
     *
     * @return array.
     */
	 public function totalReviewedVendors($userId)
	 {
 	   $db = $this->getAdapter();
	   $q = $db->select()
			->from(array('r'=>'mybb_review_master'),array('r.vendor_id','count( * ) as reviewcount'))
			->group('r.vendor_id');
           //echo $q; die;
	   $query = $db->select()
				->from(array('mv'=>'wp_my_vendors'),array('mv.*'))
  				->joinleft(array('vv'=> $q),'mv.vendors_id = vv.vendor_id',array('vv.reviewcount'))
  				->Where('mv.user_id=?',$userId);
 				$select = $db->fetchAll($query);
                                
	   return $select; 
	}

	/**
     * This function is used to get wedding date of login user.
     *
     * Created By : Bidhan Chandra
     * Date : 10 Jan,2014	
     * @param $params array by reference
     *
     * @return array.
     */
	 public function getWeddingDate($userId)
	 {
	   $db = $this->getAdapter();
	   $query = $db->select()
	             ->from('mybb_users','*')
				 ->Where('id=?',$userId);  
	   $select = $db->fetchAll($query); 
 	   return $select;  
	}

	/**
     * This function is used to get account information of login user.
     *
     * Created By : Bidhan Chandra
     * Date : 10 Jan,2014	
     * @param $params array by reference
     *
     * @return array.
     */
	public function getAccountInformation($userId)
	{
	   $db = $this->getAdapter();
	   $query = $db->select()
	            ->from('mybb_users','*')
                    ->Where('id=?',$this->userSession->id);  
	   $select = $db->fetchRow($query); 
 	   return $select;  
	}
	
	/**
     * This function is used to get site themes of login user.
     *
     * Created By : Bidhan Chandra
     * Date : 10 Jan,2014	
     * @param $params array by reference
     *
     * @return array.
     */
	public function getThemes()
    {
		$db = $this->getAdapter();
		$query = $db->select()
					 ->from(array('s'=>'ww_site_themes_colors'),array('s.*'))
					 ->joinLeft(array('st'=>'ww_site_themes'),'s.theme_id = st.id',array('st.id as theme_id','st.theme','st.theme_name'))
					 ->order('theme')	;
		$select['0'] = $db->fetchAll($query);
 		$query1 = $db->select()
					 ->from(array('u'=>'ww_user_site'),array('u.*'))
					 ->joinLeft(array('s'=>'ww_site_themes_colors'),'u.site_id = s.id',array('s.*'))	
   					 ->joinLeft(array('st'=>'ww_site_themes'),'s.theme_id = st.id',array('st.*'))
					 ->where('u.user_id = ?',$this->userSession->id)	; 

		$select['1'] = $db->fetchAll($query1);
		return $select;   
    }

	/**
     * This function is used to get checklist items of next week of login user.
     *
     * Created By : Bidhan Chandra
     * Date : 10 Jan,2014	
     * @param $params array by reference
     *
     * @return array.
     */
 	public function dueTasksInNextWeek($userId) {
  	    $date = date('Y-m-d',strtotime("+7 days"));  
 	    $db = $this->getAdapter();
        $query = $db->select()
                ->from('wp_checklist_items', array('count(check_item) AS nextWeekTask'))
                ->where('user_id = ?', $userId)
			  	->where('due_date <=?',  $date);
        $select = $db->fetchAll($query);
        return $select;
    }
	
	/**
     * This function is used to get total events of login user.
     *
     * Created By : Bidhan Chandra
     * Date : 10 Jan,2014	
     * @param $params array by reference
     *
     * @return int.
     */
	public function totaleventCount($userId)
	{
	 	$db = $this->getAdapter();
		$query = $db->select()
                ->from('wp_mywedding_events', '*')
                ->Where('userid=?', $userId);
		$select = count($db->fetchAll($query));
	    return $select;
	}
  /**
     * This function is used to get website url.
     *
     * Created By : Umesh
     * Date : 8 Feb,2014	
     * @param $params array by reference
     *
     * @return int.
     */
	public function getWeddingWebsite($userId)
	{
	 	$db = $this->getAdapter();
		$query = $db->select()
                ->from('ww_site', '*')
                ->Where('user_id=?', $userId);
		 $select = $db->fetchRow($query);  
	    return $select;
	 }
	/**
     * This function is used to get last use e invite.
     *
     * Created By : Umesh
     * Date : 8 Feb,2014	
     * @param $params array by reference
     *
     * @return int.
     */
	public function getEinvitetemplate($userId)
	{
	 	$db = $this->getAdapter();
		$query = $db->select()
                ->from('wp_invites_details', '*')
                ->Where('user_id=?', $userId)
				->order('id DESC')
				->limit('1'); 
		$select = $db->fetchRow($query);   
	    return $select;
	 }
         
        
        public function getWeddingBudgetDetail() {
            $db = $this->getAdapter();
            $query = $db->select()->from('mybb_user_wedding_details', '*')
                    ->Where('user_id=?', $this->modelUserId); 
            $select = $db->fetchRow($query);   
	    return $select;
	}
        
        public function getUserChecklistItems() { 
            $db = $this->getAdapter();
            $query = $db->select()->from('mybb_wp_user_checklist_items', '*')
                    ->Where('user_id=?', $this->modelUserId); 
            $select = $db->fetchAll($query);   
	    return $select;
	}
	
}
?>