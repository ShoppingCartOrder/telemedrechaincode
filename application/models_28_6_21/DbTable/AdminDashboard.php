<?php

class Application_Model_DbTable_AdminDashboard extends Weddingplz_Model_BaseModel {

    protected $_name = 'mybb_users';
    protected $_vendors = 'mybb_vendors';
    protected $_claimed_vendors = 'mybb_claimed_vendors';
    protected $_missing_business = 'mybb_missingsumthing';
    protected $_searchNotFound = 'mybb_search_notfound';
    protected $_whats_wrong = 'mybb_whats_wrong';
    protected $_help_us_improve = 'mybb_help_us_improve';
    protected $_deal_alert = 'mybb_deal_alert';
    protected $_review_master = 'mybb_review_master';
    protected $_category = 'mybb_category';
    protected $_users = 'mybb_users';
    protected $_events = 'mybb_events';
    protected $_advertise = 'mybb_advertise';
    protected $_contactus = 'mybb_contact_us';
    protected $_smsemailme = 'mybb_smsemailme';
    protected $_eventsmsemailme = 'mybb_eventsmsemailme';
    protected $_quickquote = 'mybb_send_enquiry';
    protected $_tags = 'mybb_tags';
    protected $_zone = 'mybb_zones';
    protected $_login_logout_details = 'mybb_login_logout_details';
    protected $_website_created = 'mybb_planning_site_log';
    protected $_deals_request = 'mybb_deals_request';
    protected $_instabooking_answer = 'mybb_insta_booking_answer';
    protected $_contact_expert = 'mybb_contact_an_expert';

    public function fetchAdminUserInfo($id) {

        $sql = "SELECT * FROM $this->_name where id = $id";
        return $this->getAdapter()->fetchAll($sql);
    }

    public function fetchLatestReview() {

        $where = 'where rm.status = 0';

        $sql = "SELECT rm.*,v.city FROM mybb_review_master as rm inner join mybb_vendors as v on v.id = rm.vendor_id $where";

        return $this->getAdapter()->fetchAll($sql);
    }

    public function fetchVendorCreatedRecord() {

        $sql = "SELECT mv.*,mc.name as cat_name FROM mybb_vendors mv inner join mybb_category mc on mv.category = mc.id where mv.status= '0' and mv.updated_date = '0000-00-00'";
        return $this->getAdapter()->fetchAll($sql);
    }

    /* public function getlogincount()
      {
      $yesterdayDate =  date("Y-m-j", strtotime("yesterday"));
      $sql = "SELECT * FROM mybb_login_count where date = '$yesterdayDate'";
      return $this->getAdapter()->fetchAll($sql);
      } */

    public function fetchLoggedinUsers() {


        // echo  date("Y-m-j", strtotime("yesterday")); die;
        /* $yesterdayDate = date("Y-m-j", strtotime("yesterday"));
          $sql = "SELECT lc.email,lc.date,lu.name,lu.id FROM mybb_login_count lc inner join mybb_users lu on lu.email = lc.email where lc.date ='$yesterdayDate'";
          $result = $this->getAdapter()->fetchAll($sql);
          return $result; */

        $yesterdayDate = date("Y-m-d", strtotime("yesterday"));
        $sql = "SELECT * FROM mybb_login_logout_details lc inner join mybb_users lu on lu.id = lc.userid  where date(lc.login_time) ='$yesterdayDate'";
        $result = $this->getAdapter()->fetchAll($sql);
        return $result;
    }

    public function fetchregisterusers() {
        $yesterdayDate = date("Y-m-j", strtotime("yesterday"));
        $sql = "SELECT * FROM mybb_users where created_date = '$yesterdayDate'";
        return $this->getAdapter()->fetchAll($sql);
    }

    public function fetchSomethingMindRecord() {
        $sql = "SELECT * FROM mybb_help_us_improve order by date desc";
        return $this->getAdapter()->fetchAll($sql);
    }

    public function getfeedbackcount() {
        $yesterdayDate = date("Y-m-j", strtotime("yesterday"));
        $sql = "SELECT * FROM mybb_feedback_master where date = '$yesterdayDate'";
        return $this->getAdapter()->fetchAll($sql);
    }

    public function getenquirycount() {
        $yesterdayDate = date("Y-m-j", strtotime("yesterday"));
        $sql = "SELECT * FROM mybb_send_enquiry where date = '$yesterdayDate'";
        return $this->getAdapter()->fetchAll($sql);
    }

    /* public function user_online()
      {
      $sql = "SELECT * FROM mybb_user_online WHERE datetime >= DATE_SUB(now(), INTERVAL 5 MINUTE)";
      return $result =  $this->getAdapter()->fetchAll($sql);

      } */

    public function quickquotecount() {
        $sql = "SELECT * FROM mybb_user_quickquote";
        return $result = $this->getAdapter()->fetchAll($sql);
    }

    public function quickquoterecord() {
        $sql = "SELECT qq.*,ml.name as area FROM mybb_user_quickquote qq left join mybb_location ml on qq.location = ml.id";
        return $result = $this->getAdapter()->fetchAll($sql);
    }

    public function availabilitycount() {
        $yesterdayDate = date("Y-m-j", strtotime("yesterday"));
        $sql = "SELECT id FROM mybb_checkavailability where date = '$yesterdayDate'";
        return $result = $this->getAdapter()->fetchAll($sql);
    }

    public function availabilityrecord() {
        $yesterdayDate = date("Y-m-j", strtotime("yesterday"));
        $sql = "SELECT ca.*,ml.name as area FROM mybb_checkavailability ca left join mybb_location ml on ca.location = ml.id where ca.date = '$yesterdayDate'";
        return $result = $this->getAdapter()->fetchAll($sql);
    }

    public function newslettercount() {
        $yesterdayDate = date("Y-m-j", strtotime("yesterday"));
        $sql = "SELECT id FROM mybb_newletter where date = '$yesterdayDate'";
        return $result = $this->getAdapter()->fetchAll($sql);
    }

    public function getNewsLettter() {
        $yesterdayDate = date("Y-m-j", strtotime("yesterday"));
        $sql = "SELECT * FROM mybb_newletter where date = '$yesterdayDate'";
        return $result = $this->getAdapter()->fetchAll($sql);
    }

    public function fetchSearchCatCnt() {

        $yesterdayDate = date("Y-m-j", strtotime("yesterday"));
        $sql = "SELECT id FROM mybb_mysearch where date = '$yesterdayDate' group by category";
        return $result = $this->getAdapter()->fetchAll($sql);
    }

    public function fetchSearchCategory() {

        $yesterdayDate = date("Y-m-j", strtotime("yesterday"));
        $sql = "SELECT *,count(category) as total FROM mybb_mysearch where date = '$yesterdayDate' group by category order by total desc";
        return $result = $this->getAdapter()->fetchAll($sql);
    }

    public function fetchSearchCategoryVendor($cat) {

        $yesterdayDay = date("j", strtotime("yesterday"));
        $yesterdayMonth = date("m", strtotime("yesterday"));
        $yesterdayYear = date("Y", strtotime("yesterday"));
        $sql = "SELECT bp.*,count(bp.businessid) as total,bv.name FROM mybb_business_popularity bp 
		inner join mybb_vendors bv on bv.id = bp.businessid where bp.date = '$yesterdayDay' and bp.month = '$yesterdayMonth' and bp.year = '$yesterdayYear' and bp.category = '" . $cat . "' group by bp.businessid order by total desc";
        return $result = $this->getAdapter()->fetchAll($sql);
    }

    public function fetchWeddingWebsite() {

        $sql = "select ws.*,mu.name from ww_site ws inner join mybb_users mu on mu.id = ws.user_id";
        $result = $this->getAdapter()->fetchAll($sql);
        return $result;
    }

    public function fetchWeddingPlanningRecord() {

        $sql = "select * from mybb_users where wedding_planning = '1'";
        $result = $this->getAdapter()->fetchAll($sql);
        return $result;
    }

    public function fetchEinvitesCnt() {

        $sql = "select id from wp_invites_details";
        $result = $this->getAdapter()->fetchAll($sql);
        return $result;
    }

    public function fetcheinvitesusers() {

        $sql = "select wid.*,mu.name from wp_invites_details wid inner join mybb_users mu on mu.id = wid.user_id";
        $result = $this->getAdapter()->fetchAll($sql);
        return $result;
    }

    public function fetchSmsEmailCnt() {

        $date = date("Y-m-j", strtotime("yesterday"));
        $sql = "select id from mybb_smsemailme where date = '$date'";
        $result = $this->getAdapter()->fetchAll($sql);
        return $result;
    }

    public function fetchSmsEmailusers() {

        $date = date("Y-m-j", strtotime("yesterday"));
        $sql = "select * from mybb_smsemailme where date = '$date'";
        $result = $this->getAdapter()->fetchAll($sql);
        return $result;
    }

    public function getLoggedInDetail() {
        $date = date("Y-m-j", strtotime("yesterday"));
        //	$date = '2013-03-15';
        $sql = "select lc.date,u.name,u.email,u.locality from mybb_login_count as lc inner join mybb_users as u on u.email=lc.email where date = '$date'";
        return $result = $this->getAdapter()->fetchAll($sql);
    }

    public function getRegisterDetail() {
        $date = date("Y-m-j", strtotime("yesterday"));
        //	$date = '2013-03-15';
        $sql = "select rc.date,u.name,u.email,u.locality from mybb_register_count as rc inner join mybb_users as u on u.email=rc.email where date = '$date'";
        return $result = $this->getAdapter()->fetchAll($sql);
    }

    public function getEnquiryDetail() {
        $date = date("Y-m-j", strtotime("yesterday"));
        //	$date = '2013-03-15';
        $sql = "select v.name as businessname,se.name,se.email,se.locality,se.date from mybb_send_enquiry as se inner join mybb_vendors as v on v.id=se.vendorid where date = '$date'";
        return $result = $this->getAdapter()->fetchAll($sql);
    }

    public function weddingwebcount() {
        $sql = "SELECT * FROM mybb_newletter";
        return $result = $this->getAdapter()->fetchAll($sql);
    }

    public function fetchClaimBusiness_update() {

        $sql = "SELECT cb.*,cat.name,ccb.userid,mv.name as bus_name FROM mybb_claimed_vendors cb
		inner join mybb_vendors mv on cb.vendor_id = mv.id
		inner join mybb_category cat on cat.id = cb.category
		inner join mybb_claimbusiness ccb on ccb.businessid = cb.vendor_id
		inner join mybb_users cu on cu.id = ccb.userid where ccb.status = '0'";
        return $result = $this->getAdapter()->fetchAll($sql);
    }

    public function getClaimRecord() {

        $sql = "SELECT cb.*,cat.name,ccb.userid,mv.name as bus_name FROM mybb_claimed_vendors cb
		inner join mybb_vendors mv on cb.vendor_id = mv.id
		inner join mybb_category cat on cat.id = cb.category
		inner join mybb_claimbusiness ccb on ccb.businessid = cb.vendor_id
		inner join mybb_users cu on cu.id = ccb.userid where ccb.status = '0' group by cb.id";
        return $result = $this->getAdapter()->fetchAll($sql);
    }

    public function getAdRecord() {

        $select = "select ab.*,aw.website,ap.page,ac.name as category from mybb_ad_banner ab
		inner join mybb_ad_website aw on aw.id = ab.website
		left join mybb_category ac on ac.id = ab.cat_id
		inner join mybb_ad_page ap on ap.id = ab.page where ab.expiry_date between now() and date_add(now(),interval 30 day) order by ab.expiry_date";

        return $this->getAdapter()->fetchAll($select);
    }

    public function getEventRecord() {

        $select = "select * from mybb_events where status = '0' and (remark = '' or remark IS NULL)";

        return $this->getAdapter()->fetchAll($select);
    }

    public function getAdvertiseRecord() {

        $select = "select * from mybb_advertise where status = '1'";

        return $this->getAdapter()->fetchAll($select);
    }

    public function fetchVendorDuplicateData($id) {

        $where = "where v.id = $id";
        $select = "SELECT v.* , l.name as location,l.id as locationid FROM mybb_vendors_duplicate as v inner join mybb_location as l on l.id = v.location $where ";

        return $this->getAdapter()->fetchRow($select);
    }

    public function fetchAdvertisecount() {

        $sql = "SELECT id FROM mybb_advertise where status = '1'";
        return $result = $this->getAdapter()->fetchAll($sql);
    }

    public function fetchBannerExpiring_count() {

        $sql = "SELECT id FROM mybb_ad_banner where expiry_date between now() and date_add(now(),interval 30 day)";
        return $result = $this->getAdapter()->fetchAll($sql);
    }

    public function fetchEvents_count() {

        $sql = "SELECT id FROM mybb_events where status = '0' and (remark = '' or remark IS NULL)";
        return $result = $this->getAdapter()->fetchAll($sql);
    }

    public function updateAdvertiseRecord($adid) {

        $where['id =?'] = $adid;
        $data['status'] = '0';
        $this->getAdapter()->update('mybb_advertise', $data, $where);
        return true;
    }

    public function categorydata($cats) {

        $sql = "select name from mybb_category where id in (" . $cats . ")";
        $result = $this->getAdapter()->fetchAll($sql);
        $cat_record = '';
        foreach ($result as $val) {
            $cat_record .= $val['name'] . ", ";
        }
        return $cat_record = substr($cat_record, 0, -2);
    }

    public function removeDuplicateVendorData($dupid) {

        if (!empty($dupid)) {

            $condition = array('id = ?' => $dupid);
            $this->getAdapter()->delete('mybb_vendors_duplicate', $condition);
        }
    }

    public function fetchWrongCatCnt() {
        $sql = "SELECT id FROM mybb_search_notfound";
        return $result = $this->getAdapter()->fetchAll($sql);
    }

    public function deletewrongcatRecord($id) {
        $condition = array('id = ?' => $id);
        $this->getAdapter()->delete('mybb_search_notfound', $condition);
    }

    public function updateDuplicateVendorData($image_name, $data) {

        $dbdata = array(
            'name' => $data['claim_businessname'],
            'person_name' => $data['claim_pname'],
            'person_name2' => $data['claim_pname2'],
            'number' => $data['claim_number'],
            'email' => $data['claim_email'],
            'address' => $data['claim_address'],
            'category' => $data['category'],
            'location' => $data['claim_location'],
            'other_location' => $data['claim_other_location'],
            'gmap_address' => $data['claim_gmap_address'],
            'longitude' => $data['claim_longitude'],
            'latitude' => $data['claim_latitude'],
            'television_media' => $data['claim_media_coverage'],
            'days_hours' => $data['claim_timing'],
            'city' => $data['claim_city'],
            'website' => $data['claim_website'],
            'status' => $data['status'],
            'remarks' => $data['remarks'],
            'vendor_pic' => $image_name,
            //'paid_not_paid' => $data['paid_not_paid'],
            'budget_amount' => $data['claim_budget'],
            'payment_mode' => $data['hiddepaymode'],
            'established_in' => $data['claim_establisin'],
            'facebook_link' => $data['claim_facebooklink'],
            'about_us' => $data['claim_aboutus'],
            'video_link' => $data['claim_videolink'],
            'search_tags' => $data['hiddentags'],
            'other_service' => $data['claim_otherservice'],
            'other_tags' => $data['claim_othertags'],
            'services' => $data['hiddeservices'],
            'album_types' => $data['claim_album'],
            //'brands' => $data['claim_album'],
            'wedding_community' => $data['claim_community'],
            'cuisine' => $data['claim_cuisine'],
            'dance_style' => $data['claim_dancestyle'],
            'ladies_gents' => $data['claim_ladiesgents'],
            'name_of_institute' => $data['claim_institute'],
            'no_rooms' => $data['claim_rooms'],
            'no_of_people_catered' => $data['claim_cateredto'],
            'number_of_halls' => $data['claim_nohalls'],
            'performs_in' => $data['claim_performsin'],
            //'segment'  => $data['claim_album'],
            'guests_ratio' => $data['servers_guests_ratio'],
            'speciality_dish' => $data['claim_specialitydish'],
            //'style'  => $data['claim_album'],
            'therapy' => $data['claim_therapy'],
            //'type'  => $data['claim_album'],
            'deals_in' => $data['claim_dealsin'],
            //'menu'  => $data['claim_album']
            //'source'  => $data['source'],
            //'updated_date' => date('Y-m-d')
            'action_date' => $data['action_date']
        );

        //print_r($data); die;

        $where['id =?'] = $data['id'];

        return $this->getAdapter()->update('mybb_vendors_duplicate', $dbdata, $where);
    }

    public function updateVendorData($image_name, $data) {
        $dbdata = array(
            'name' => $data['claim_businessname'],
            'person_name' => $data['claim_pname'],
            'person_name2' => $data['claim_pname2'],
            'number' => $data['claim_number'],
            'email' => $data['claim_email'],
            'address' => $data['claim_address'],
            'category' => $data['category'],
            'location' => $data['claim_location'],
            'other_location' => $data['claim_other_location'],
            'gmap_address' => $data['claim_gmap_address'],
            'longitude' => $data['claim_longitude'],
            'latitude' => $data['claim_latitude'],
            'television_media' => $data['claim_media_coverage'],
            'days_hours' => $data['claim_timing'],
            'city' => $data['claim_city'],
            'website' => $data['claim_website'],
            'status' => $data['status'],
            'remarks' => $data['remarks'],
            'vendor_pic' => $image_name,
            //'paid_not_paid' => $data['paid_not_paid'],
            'budget_amount' => $data['claim_budget'],
            'payment_mode' => $data['hiddepaymode'],
            'established_in' => $data['claim_establisin'],
            'facebook_link' => $data['claim_facebooklink'],
            'about_us' => $data['claim_aboutus'],
            'video_link' => $data['claim_videolink'],
            'search_tags' => $data['hiddentags'] . "," . $data['hiddeservices'],
            'available_services' => $data['hiddeservices'],
            'available_tags' => $data['hiddentags'],
            'services' => $data['search_services'],
            'album_types' => $data['claim_album'],
            //'brands' => $data['claim_album'],
            'wedding_community' => $data['claim_community'],
            'cuisine' => $data['claim_cuisine'],
            'dance_style' => $data['claim_dancestyle'],
            'ladies_gents' => $data['claim_ladiesgents'],
            'name_of_institute' => $data['claim_institute'],
            'no_rooms' => $data['claim_rooms'],
            'no_of_people_catered' => $data['claim_cateredto'],
            'number_of_halls' => $data['claim_nohalls'],
            'performs_in' => $data['claim_performsin'],
            //'segment'  => $data['claim_album'],
            'guests_ratio' => $data['servers_guests_ratio'],
            'speciality_dish' => $data['claim_specialitydish'],
            //'style'  => $data['claim_album'],
            'therapy' => $data['claim_therapy'],
            //'type'  => $data['claim_album'],
            'deals_in' => $data['claim_dealsin'],
                //'menu'  => $data['claim_album']
                //'source'  => $data['source'],
                //'updated_date' => date('Y-m-d')
        );

        if ($data['claim_othertags'] != '' && $data['claim_othertags'] != 'NA') {
            $dbdata['available_tags'] = $dbdata['available_tags'] . "," . $data['claim_othertags'];
        }

        if ($data['claim_otherservice'] != '' && $data['claim_otherservice'] != 'NA') {
            $dbdata['available_services'] = $dbdata['available_services'] . "," . $data['claim_otherservice'];
        }

        //print_r($data); die;

        $where['id =?'] = $data['buss_id'];

        $this->getAdapter()->update('mybb_vendors', $dbdata, $where);

        $condition = array('id = ?' => $data['id']);
        $this->getAdapter()->delete('mybb_vendors_duplicate', $condition);
    }

    /* Added by umesh here  old function */
    /* public function getregistercount()
      {
      $yesterdayDate =  date("Y-m-j", strtotime("yesterday"));
      $sql = "SELECT id FROM mybb_users where created_date = '$yesterdayDate'";
      return $this->getAdapter()->fetchAll($sql);
      }
      public function fetchWeddingPlanningCnt(){

      $sql = "select id from mybb_users where wedding_planning = '1'";
      $result = $this->getAdapter()->fetchAll($sql);
      return $result;
      }
      public function fetchWrongRecord(){

      $sql = "SELECT ww.*,mv.name FROM mybb_whats_wrong ww inner join mybb_vendors mv on mv.id = ww.vendorid";
      return $result =  $this->getAdapter()->fetchAll($sql);
      }

      public function fetchMissingRecord(){

      $sql = "SELECT * FROM mybb_missingsumthing";
      return $result =  $this->getAdapter()->fetchAll($sql);
      }


     */

    /* Added by Umesh  */

    /**
     * This function is count all user login today.
     *
     * Created By : Umesh
     * Date : 21 May,2014	
     * @return void.
     */
    public function userOnline() {
        $startTime = date('Y-m-d') . ' ' . '00:00:00';
        $endTime = date('Y-m-d') . ' ' . '24:59:59';
        $db = $this->getAdapter();
        $query = $db->select()
                ->from(array('on' => 'mybb_user_online'), array('on.*'))
                ->where("on.datetime >= '$startTime' and on.datetime <= '$endTime'");
        $select = count($db->fetchAll($query));
        return $select;
    }

    /**
     * This function is count all user login yesterday.
     *
     * Created By : Umesh
     * Date : 21 May,2014	
     * modified By : jitendra
     * Date : 05 Nov,2014	
     * @return void.
     */
    public function getloginCount() {
        /*
          commented by jkm on 5 november 14
          $startTime = date('Y-m-d', strtotime("yesterday")) . ' ' . '00:00:00';
          $endTime = date('Y-m-d', strtotime("yesterday")) . ' ' . '24:59:59';
          $db = $this->getAdapter();
          $query = $db->select()
          ->from(array('on' => 'mybb_user_online'), array('on.*'))
          ->where("on.datetime >= '$startTime' and on.datetime <= '$endTime'");
          $select = count($db->fetchAll($query));
          return $select; */

        /* @ **********added by jkm on 5 november********** */
        $startTime = date('Y-m-d', strtotime("yesterday")) . ' ' . '00:00:00';
        $endTime = date('Y-m-d', strtotime("yesterday")) . ' ' . '24:59:59';
        $db = $this->getAdapter();
        $query = $db->select()
                ->from(array('on' => 'mybb_login_logout_details'), array('on.*'))
                ->where("on.login_time >= '$startTime' and on.login_time <= '$endTime'");
        $select = count($db->fetchAll($query));
        return $select;
    }

    /**
     * This function is count all user Registered Yesterday.
     *
     * Created By : Umesh
     * Date : 21 May,2014	
     * Updated By : Jitendra
     * Date : 08 november,2014	
     * @return void.
     */
    public function getRegisterCount() {

        /*
          $startTime = date('Y-m-d', strtotime("yesterday"));
          $db = $this->getAdapter();
          $query = $db->select()
          ->from(array('rg' => 'mybb_users'), array('rg.*'))
          ->Where('rg.created_date=?', $startTime);
          $select = count($db->fetchAll($query));
          return $select; */

        $startTime = date('Y-m-d', strtotime("yesterday"));
        $db = $this->getAdapter();
        $query = $db->select()
                ->from(array('rg' => 'mybb_users'), array('rg.*'))
                ->Where('rg.created_date=?', $startTime);
        $select = count($db->fetchAll($query));
        return $select;
    }

    /**
     * This function is count all user active wedding website.
     *
     * Created By : Umesh
     * Date : 21 May,2014	
     * @return void.
     */
    public function fetchWeddingWebsiteCnt() {
        $startTime = date('Y-m-d', strtotime("yesterday")) . ' ' . '00:00:00';
        $endTime = date('Y-m-d', strtotime("yesterday")) . ' ' . '24:59:59';
        $db = $this->getAdapter();
        $query = $db->select()
                ->from(array('ws' => 'ww_site'), array('ws.*'))
                ->where("ws.created_date >= '$startTime' and ws.created_date <= '$endTime'");
        $select = count($db->fetchAll($query));
        return $select;
    }

    /**
     * This function is count all user active wedding website.
     *
     * Created By : Umesh
     * Date : 21 May,2014	
     * @return void.
     */
    public function fetchWeddingPlanningCnt() {
        $sql = "select id from mybb_users where wedding_planning = '1'";
        $result = $this->getAdapter()->fetchAll($sql);
        return $result;
    }

    /**
     * This function is count all user active wedding website.
     *
     * Created By : Umesh
     * Date : 21 May,2014	
     * @return void.
     */
    public function smsemailcount() {
        $yesterdayDate = date("Y-m-j", strtotime("yesterday"));
        $sql = "SELECT * FROM mybb_smsemailme where date = '$yesterdayDate'";
        return $result = $this->getAdapter()->fetchAll($sql);
    }

    /**
     * This function is use to get vendor id then featch all record.
     *
     * Created By : Umesh
     * Date : 24 May,2014	
     * @return void.
     */
    public function fetchWrongRecord() {
        $db = $this->getAdapter();
        $query = $db->select()
                ->from(array('ww' => 'mybb_whats_wrong'), array('ww.*'))
                ->joinLeft(array('mv' => 'mybb_vendors'), 'ww.vendorid = mv.id', array('mv.name as name'))
                ->order("ww.id DESC");
        $result = $db->fetchAll($query);
        return $result;
    }

    /**
     * This function is use to Missing A Business.
     *
     * Created By : Umesh
     * Date : 24 May,2014	
     * @return void.
     */
    public function fetchMissingRecord() {
        $db = $this->getAdapter();
        $query = $db->select()
                ->from('mybb_missingsumthing', '*')
                ->order("id DESC");
        $result = $db->fetchAll($query);
        return $result;
    }

    /**
     * This function is delete note with added path note.
     *
     * Created By : Umesh
     * Date : 28 May,2014	
     * @return void.
     */
    public function deletewrong($id) {
        if ($id != '') {
            $db = $this->getAdapter();
            $condition = array('id = ?' => $id);
            return $result = $db->delete('mybb_whats_wrong', $condition);
        }
    }

    /**
     * This function is delete note with added path note.
     *
     * Created By : Umesh
     * Date : 28 May,2014		
     * @return void.
     */
    public function missingbusiness($id) {
        if ($id != '') {
            $db = $this->getAdapter();
            $condition = array('id = ?' => $id);
            return $result = $db->delete('mybb_missingsumthing', $condition);
        }
    }

    public function fetchWrongCnt() {

        $sql = "SELECT ww.id FROM mybb_whats_wrong ww inner join mybb_vendors mv on mv.id = ww.vendorid";
        return $result = $this->getAdapter()->fetchAll($sql);
    }

    ######################################################################################  

    /**
     * This function is count anything missing.
     *
     * Created By : Umesh
     * Date : 28 May,2014		
     * @return void.
     */
    public function fetchWrongCntDb() {
        $db = $this->getAdapter();
        $query = $db->select()
                ->from('mybb_whats_wrong', '*')
                ->Where('status=?', '0');
        //->order("id DESC");
        $result = count($db->fetchAll($query));
        return $result;
    }

    /**
     * This function is count anything missing.
     *
     * Created By : Umesh
     * Date :28 May,2014	
     * @return void.
     */
    public function fetchMissingCnt() {
        $db = $this->getAdapter();
        $query = $db->select()
                ->from('mybb_missingsumthing', '*')
                ->Where('status=?', '0');
        //->order("id DESC");
        $result = count($db->fetchAll($query));
        return $result;
    }

    /**
     * This function is used to update status.
     *
     * Created By : Umesh
     * Date :28 May,2014		
     * @return void.
     */
    public function updateStatus($id, $remarks, $status) {
        $db = $this->getAdapter();
        if ($remarks != '') {
            $updatestatus = array('status' => $status, 'remarks' => $remarks);
            $db->update('mybb_whats_wrong', $updatestatus, 'id = ' . $id);
        }
    }

    /**
     * This function is used to missing Status update.
     *
     * Created By : Umesh
     * Date : 28 May,2014	
     * @return void.
     */
    public function missingStatusUpdate($id, $remarks, $status) {
        $db = $this->getAdapter();
        if ($remarks != '') {
            $updatestatus = array('status' => $status, 'remarks' => $remarks);
            $db->update('mybb_missingsumthing', $updatestatus, 'id = ' . $id);
        }
    }

    /**
     * This function is used to missing Status update.
     *
     * Created By : Umesh
     * Date : 28 May,2014	
     * @return void.
     */
    public function upDateRemark($value, $bid) {
        $db = $this->getAdapter();
        $update_note = array('remarks' => $value);
        $db->update('mybb_whats_wrong', $update_note, 'id = ' . $bid);
    }

    /**
     * This function is delete Multi business.
     *
     * Created By : Umesh
     * Date : 28 May,2014	
     * @return void.
     */
    public function deleteMultibusiness($id) {
        $db = $this->getAdapter();
        $condition = array('id IN (?)' => split(',', $id));
        $delete = $db->delete('mybb_missingsumthing', $condition);
    }

    /**
     * This function is delete Multi Anything.
     *
     * Created By : Umesh
     * Date : 28 May,2014	
     * @return void.
     */
    public function deleteMultiAnything($id) {
        $db = $this->getAdapter();
        $condition = array('id IN (?)' => split(',', $id));
        return $delete = $db->delete('mybb_whats_wrong', $condition);
    }

    /**
     * This function is  use to featch all not found category record.
     *
     * Created By : Umesh
     * Date : 3 jun,2014	
     * @return void.       check live
     */
    public function fetchWrongCatRecord() {
        $db = $this->getAdapter();
        $query = $db->select()
                ->from('mybb_search_notfound', '*')
                ->order("id DESC");
        $result = $db->fetchAll($query);
        return $result;
    }

    /**
     * This function is used to missing Status update.
     *
     * Created By : Umesh
     * Date : 3 jun,2014	
     * @return void.
     */
    public function noRecordsFound($id, $remarks, $status) {
        $db = $this->getAdapter();
        if ($remarks != '') {
            $updatestatus = array('status' => $status, 'remarks' => $remarks);
            $db->update('mybb_search_notfound', $updatestatus, 'id = ' . $id);
        }
    }

    /**
     * This function is delete search.
     *
     * Created By : Umesh
     * Updated By : Bidhan
     * Date : 12 Aug,2014	
     * @return void.
     */
    public function deleteNoRecordsFound($ids = array()) {
        if (!empty($ids)) {
            $db = $this->getAdapter();
            $condition = array('id IN(?)' => $ids);
            return $result = $db->delete('mybb_search_notfound', $condition);
        }
    }

    /**
     * This function is  use to featch all not found category record.
     *
     * Created By : Umesh
     * Date : 3 jun,2014	
     * @return void.       check live
     */
    /*
      public function SomethingOnYourMind() {
      $db = $this->getAdapter();
      $query = $db->select()
      ->from('mybb_help_us_improve', '*')
      ->order("id DESC");
      $result = $db->fetchAll($query);
      return $result;
      }
     */

    /**
     * This function is used to missing Status update.
     *
     * Created By : Umesh
     * Date : 3 jun,2014	
     * @return void.
     */
    public function updateStatusSomething($id, $remarks, $status) {
        $db = $this->getAdapter();
        if ($remarks != '') {
            $updatestatus = array('status' => $status, 'remarks' => $remarks);
            $db->update('mybb_help_us_improve', $updatestatus, 'id = ' . $id);
        }
    }

    /**
     * This function is delete Multi Anything.
     *
     * Created By : Umesh
     * Date : 28 May,2014	
     * @return void.
     */
    public function delSomethingMind($id) {
        $db = $this->getAdapter();
        $condition = array('id IN (?)' => split(',', $id));
        $delete = $db->delete('mybb_help_us_improve', $condition);
    }

    /**
     * This function is count something count.
     *
     * Created By : Umesh
     * Date :4 jun,2014	
     * @return void.
     */
    public function something_Cnt() {
        $db = $this->getAdapter();
        $query = $db->select()
                ->from('mybb_help_us_improve', '*')
                ->Where('status=?', '0');
        $result = count($db->fetchAll($query));
        return $result;
    }

    /**
     * This function is  use to featch all not found category record.
     *
     * Created By : Umesh
     * Date : 6 jun,2014	
     * @return void.  check live
     */
    /*
      public function dealRequested() {
      $db = $this->getAdapter();
      $query = $db->select()
      ->from(array('vd' => 'mybb_vendor_deals'), array('vd.*'))
      ->joinLeft(array('mv' => 'mybb_vendors'), 'vd.vendorid = mv.id', array('mv.name as businessname'))
      ->joinLeft(array('mc' => 'mybb_category'), 'mv.category = mc.id', array('mc.name as catname'))
      ->order("vd.id DESC");
      $result = $db->fetchAll($query);
      return $result;
      }
     */

    /**
     * This function is count Deal Requested
     *
     * Created By : Umesh
     * Date :6 jun,2014	
     * @return void.
     */
    public function dealRequestedCount() {
        $db = $this->getAdapter();
        $query = $db->select()
                ->from('mybb_vendor_deals', '*')
                ->Where('status=?', '0');
        echo $query;
        die;
        $result = count($db->fetchAll($query));
        return $result;
    }

    /**
     * This function is delete this selected id details.
     *
     * Created By : Umesh
     * Date : 6 jun,2014	
     * @return void.
     */
    public function deleteDealRecords($id) {
        $db = $this->getAdapter();
        $condition = array('id IN (?)' => split(',', $id));
        $delete = $db->delete($this->_deal_alert, $condition);
    }

    /**
     * This function is used to status remarks.
     *
     * Created By : Umesh
     * Date : 6 jun,2014	
     * @return void.
     */
    public function updateDealsRecords($id, $remarks, $status) {
        $db = $this->getAdapter();
        if ($remarks != '') {

            $updatestatus = array('status' => $status, 'remarks' => $remarks);
            //$db->update('mybb_vendor_deals', $updatestatus, 'id = ' . $id);
            return $db->update($this->_deal_alert, $updatestatus, 'id = ' . $id);
        }
    }

    /**
     * This function is count Deal Requested
     *
     * Created By : Umesh
     * Date :6 jun,2014	
     * @return void.
     */
    public function noResultFoundCount() {
        $db = $this->getAdapter();
        $query = $db->select()
                ->from('mybb_search_notfound', '*')
                ->Where('status=?', '0');
        $result = count($db->fetchAll($query));
        return $result;
    }

    /*     * *******************function to count new added vendor records************************ */

    public function fetchLatestVendors_update($action) {
        //$date = new Zend_Date();
        //$date = $date->get('YYYY-MM-dd');
        //$sql = "SELECT mv.id FROM mybb_vendors mv inner join mybb_category mc on mv.category = mc.id where mv.status= '0' and mv.updated_date = '0000-00-00'";
        $db = $this->getAdapter();
        $sql = $db->select()
                ->from(array('mcv' => 'mybb_claimed_vendors'), array(new Zend_Db_Expr("count(mcv.id)  AS vendorCount")));

        if ($action == 'add_business') {
            $sql->where('mcv.status = 0 AND mcv.business_type = 2');
        } else if ($action == 'claim_business') {
            $sql->where('mcv.status = 0 AND mcv.business_type = 1');
        }

        //echo $sql;  die;
        return $db->fetchRow($sql);


        return $this->getAdapter()->fetchAll($sql);
    }

    /*     * ******************function to fetch new added vendor records************************** */

    public function fetchNewVendorCreatedRecord($params = array()) {



        //if(empty($params['condition']['business_edited']) && empty($params['condition']['business_type'])) return false;
        //$fields = array('*');
        $status = '';
        $remarks = '';
        if (isset($params['fields']['claimed_vendors'])) {
            $claimedVendorFields = $params['fields']['claimed_vendors'];
        } else {
            return false;
        }

        if (!empty($params['fields']['main'])) {
            $fields = $params['fields']['main'];
        }

        $select = $this->getAdapter()->select()
                ->from(array('mv' => $this->_vendors), $fields)
                ->joinInner(array('mcv' => $this->_claimed_vendors), 'mv.id = mcv.vendor_id', $claimedVendorFields)
                ->joinLeft(array('mu' => 'mybb_users'), 'mu.id = mcv.claimed_by', array('mu.email as user_email'))
                ->joinInner(array('mc' => 'mybb_category'), 'mc.id = mcv.category', array('mc.name as category_name'))
                ->joinInner(array('mcty' => 'mybb_city_master'), 'mcty.id = mcv.city', array('mcty.name as city_name'));

        $selectCount = $this->getAdapter()->select()
                ->from(array('mv' => $this->_vendors), array(new Zend_Db_Expr("count(mcv.id)  AS count")))
                ->joinInner(array('mcv' => $this->_claimed_vendors), 'mv.id = mcv.vendor_id', null)
                ->joinLeft(array('mu' => 'mybb_users'), 'mu.id = mcv.claimed_by', null)
                ->joinInner(array('mc' => 'mybb_category'), 'mc.id = mcv.category', null)
                ->joinInner(array('mcty' => 'mybb_city_master'), 'mcty.id = mcv.city', null);

        $where = "1 ";

        $genCond = "";

        if (!empty($params['condition']['name'])) {
            $where .= " AND mv.name LIKE '%" . $params['condition']['name'] . "%'";
        }
        //dd($params);
//        if (!empty($params['condition']['email'])) {
//            $where .= " AND mv.email = '" . $params['condition']['email'] . "'";
//        }
        
        if (!empty($params['condition']['claimed_mobile_number'])) {
            $where .= " AND mcv.claimed_mobile_number = '" . $params['condition']['claimed_mobile_number'] . "'";
        }

        if (!empty($params['condition']['business_type'])) {
            $where .= " AND mcv.business_type = " . $params['condition']['business_type'];
        }

        if (isset($params['condition']['business_edited'])) {
            $where .= " AND mcv.business_edited = " . $params['condition']['business_edited'];
        }

        if (isset($params['condition']['has_been_updated'])) {
            $where .= " AND mcv.has_been_updated = " . $params['condition']['has_been_updated'];
        }
        
        if (isset($params['condition']['city'])) {
            $where .= " AND mcv.city = " . $params['condition']['city'];
        }
        
        if (!empty($params['condition']['email'])) {
            $where .= " AND mu.email = '" . $params['condition']['email'] . "'";
        }
        // echo $params['condition']['status']; die;

        if(!empty($params['count'])){
            //if (isset($params['condition']['business_type']) && $params['condition']['business_type'] == 2) {
            if($params['section'] == 'claimed'){
                $where .= " AND mcv.claimed_status = " . $params['condition']['claimed_status'];
                $where .= " AND (mcv.claimed_remarks is null OR mcv.claimed_remarks = '')";

            }else if ($params['section'] == 'edit') {
                
                $where .= " AND mcv.edit_status = " . $params['condition']['edit_status'];
                $where .= " AND (mcv.edit_remarks is null OR mcv.edit_remarks = '')";
                                      
            }else if($params['section'] == 'add'){
                $where .= " AND mcv.status = " . $params['condition']['status'];
                $where .= " AND (mcv.remarks is null OR mcv.remarks = '')";
            }
        }
        
        
        if (isset($params['condition']['status']) && isset($params['condition']['has_been_updated']) && !isset($params['count'])) {

            $where .= " AND mcv.edit_status = " . $params['condition']['status'];
        }else if(isset($params['condition']['status']) && !isset($params['condition']['has_been_updated']) && ($params['condition']['business_type'] == 2)){
            $where .= " AND mcv.status = " . $params['condition']['status'];
        }else if(isset($params['condition']['status']) && ($params['condition']['business_type'] == 1)){
            $where .= " AND mcv.claimed_status = " . $params['condition']['status'];
        }
        //echo $where; die;

        $where .= $genCond;

        if (!empty($where)) {
            $selectCount->where($where);
            $select->where($where);
        }

        //echo $selectCount; die;
        $resultCount = $this->getAdapter()->fetchCol($selectCount);

        $params['count'] = $resultCount[0];

        if (!$params['count']) {
            return $this->blankResult();
        }
        $this->getPagingSorting($params);

        $select->order($params['sidx'] . " " . $params['sord']);

        $select->limit($params['limit'], $params['start']);
        //echo $select; die; 
        $result = $this->getAdapter()->fetchAll($select);
        //dd($params);
        return array(
            'result' => $result,
            'total' => $params['totalPages'],
            'records' => $params['count'],
            'page' => $params['page'],
            'resultCount' => count($result)
        );
    }

    public function updatevendorstatus($data = array()) {

        if (empty($data)) {
            return false;
        }
        $db = $this->getAdapter();

        $updatestatus['remarks'] = $data['remarks'];
        $updatestatus['status'] = $data['status'];

        if (isset($data['business_edited'])) {
            if ($data['business_edited'] == 0) {
                $updatestatus['business_edited'] = $data['business_edited'];
            }
        }
        return $db->update($this->_claimed_vendors, $updatestatus, 'id = ' . $data['id']);
    }

    public function fetchMissingBusinessRecords($params = array()) {


        //dd($params);

        if (!empty($params['fields']['main'])) {
            $fields = $params['fields']['main'];
        }

        if (empty($fields)) {
            $fields = array('*');
        }
        $select = $this->getAdapter()->select()
                ->from(array('mmb' => $this->_missing_business), $fields);

        $selectCount = $this->getAdapter()->select()
                ->from(array('mmb' => $this->_missing_business), array(new Zend_Db_Expr("count(mmb.id)  AS count")));

        $where = "1 ";

        $genCond = "";

        if (!empty($params['condition']['email'])) {
            $where .= " AND mmb.email = '" . $params['condition']['email'] . "'";
        }

        if (isset($params['condition']['status'])) {
            $where .= " AND mmb.status = " . $params['condition']['status'];
        }

        // echo $params['condition']['status']; die;
        if (!empty($params['condition']['detail'])) {
            $where .= " AND mcv.detail LIKE '%" . $params['condition']['detail'] . "%'";
        }


        $where .= $genCond;

        if (!empty($where)) {
            $selectCount->where($where);
            $select->where($where);
        }

        //echo $select; die;
        $resultCount = $this->getAdapter()->fetchCol($selectCount);


        $params['count'] = $resultCount[0];

        if (!$params['count']) {
            return $this->blankResult();
        }
        $this->getPagingSorting($params);

        $select->order($params['sidx'] . " " . $params['sord']);

        $select->limit($params['limit'], $params['start']);
        //echo $select; die; 
        $result = $this->getAdapter()->fetchAll($select);
        //dd($params);
        return array(
            'result' => $result,
            'total' => $params['totalPages'],
            'records' => $params['count'],
            'page' => $params['page'],
            'resultCount' => count($result)
        );





        $db = $this->getAdapter();
        $query = $db->select()
                ->from('mybb_missingsumthing', '*')
                ->order("id DESC");
        $result = $db->fetchAll($query);
        return $result;
    }

    public function fetchNotFoundSearch($params = array()) {

        $fields = array('*');

        if (!empty($params['fields']['main'])) {
            $fields = $params['fields']['main'];
        }

        $select = $this->getAdapter()->select()
                ->from(array('ms' => $this->_searchNotFound), $fields)
                ->joinLeft(array('mc' => 'mybb_city_master'), 'mc.id = ms.city_id', array('mc.name as city_name'))
                ->joinLeft(array('mz' => 'mybb_zones'), 'mz.id = ms.zone_id', array('mz.name as zone_name'))
                ->joinLeft(array('ml' => 'mybb_location'), 'ml.id = ms.location_id', array('ml.name as location_name'));


        $selectCount = $this->getAdapter()->select()
                ->from(array('ms' => $this->_searchNotFound), array(new Zend_Db_Expr("count(ms.id)  AS count")))
                ->joinLeft(array('mc' => 'mybb_city_master'), 'mc.id = ms.city_id', null)
                ->joinLeft(array('mz' => 'mybb_zones'), 'mz.id = ms.zone_id', null)
                ->joinLeft(array('ml' => 'mybb_location'), 'ml.id = ms.location_id', null);


        $where = "1 ";

        if (!empty($params['condition']['searchCount'])) {
            $genCond = " AND number_of_records > 0 ";
        } else {
            $genCond = " AND number_of_records = 0 ";
        }

        if (!empty($params['condition']['category'])) {
            $where .= " AND category LIKE '%" . $params['condition']['category'] . "%'";
        }

        if (!empty($params['condition']['ip'])) {
            $where .= " AND ip LIKE '%" . $params['condition']['ip'] . "%'";
        }

        if (!empty($params['condition']['id'])) {
            $where .= " AND id = " . $params['condition']['id'] . "";
        }

        if (isset($params['condition']['status'])) {
            $where .= " AND ms.status = " . $params['condition']['status'];
        }

        $where .= $genCond;

        if (!empty($where)) {
            $selectCount->where($where);
            $select->where($where);
        }

        //echo $select; die;
        $resultCount = $this->getAdapter()->fetchCol($selectCount);


        $params['count'] = $resultCount[0];

        if (!$params['count']) {
            return $this->blankResult();
        }
        $this->getPagingSorting($params);

        $select->order($params['sidx'] . " " . $params['sord']);

        $select->limit($params['limit'], $params['start']);
        //echo $select; die; 
        $result = $this->getAdapter()->fetchAll($select);
        //dd($params);
        return array(
            'result' => $result,
            'total' => $params['totalPages'],
            'records' => $params['count'],
            'page' => $params['page'],
            'resultCount' => count($result)
        );
    }

    public function fetchWhatsWrongData($params = array()) {

        $fields = array('*');

        if (!empty($params['fields']['main'])) {
            $fields = $params['fields']['main'];
        }

        $select = $this->getAdapter()->select()
                ->from(array('mww' => $this->_whats_wrong), $fields)
                ->joinInner(array('mv' => $this->_vendors), 'mww.vendorid = mv.id', array('mv.name'));


        $selectCount = $this->getAdapter()->select()
                ->from(array('mww' => $this->_whats_wrong), array(new Zend_Db_Expr("count(mww.id)  AS count")))
                ->joinInner(array('mv' => $this->_vendors), 'mww.vendorid = mv.id', array('name'));


        $where = "1 ";

        $genCond = "";
        //$genCond = " AND mww.status = 0";

        if (!empty($params['condition']['whatswrong'])) {
            $where .= " AND whatswrong LIKE '%" . $params['condition']['whatswrong'] . "%'";
        }

        if (!empty($params['condition']['wronginfo'])) {
            $where .= " AND wronginfo LIKE '%" . $params['condition']['wronginfo'] . "%'";
        }

        if (!empty($params['condition']['email'])) {
            $where .= " AND mww.email = '" . $params['condition']['email'] . "'";
        }

        if (!empty($params['condition']['id'])) {
            $where .= " AND id = " . $params['condition']['id'] . "";
        }

        if (isset($params['condition']['status'])) {
            $where .= " AND mww.status = " . $params['condition']['status'];
        }

        $where .= $genCond;

        if (!empty($where)) {
            $selectCount->where($where);
            $select->where($where);
        }

        //echo $selectCount; die;
        $resultCount = $this->getAdapter()->fetchCol($selectCount);


        $params['count'] = $resultCount[0];

        if (!$params['count']) {
            return $this->blankResult();
        }
        $this->getPagingSorting($params);

        $select->order($params['sidx'] . " " . $params['sord']);

        $select->limit($params['limit'], $params['start']);
        //echo $select; die; 
        $result = $this->getAdapter()->fetchAll($select);
        //dd($params);
        return array(
            'result' => $result,
            'total' => $params['totalPages'],
            'records' => $params['count'],
            'page' => $params['page'],
            'resultCount' => count($result)
        );
    }

    /**
     * This function is  use to featch all not found category record.
     *
     * Created By : Umesh
     * Date : 3 jun,2014	
     * @return void.       check live
     */
    public function SomethingOnYourMind($params = array()) {


        $fields = array('*');

        if (!empty($params['fields']['main'])) {
            $fields = $params['fields']['main'];
        }

        $select = $this->getAdapter()->select()
                ->from(array('mww' => $this->_help_us_improve), $fields)
                ->joinLeft(array('mc'=>'mybb_city_master'),'mc.id = mww.city_id',array('mc.name AS city_name'));


        $selectCount = $this->getAdapter()->select()
                ->from(array('mww' => $this->_help_us_improve), array(new Zend_Db_Expr("count(mww.id)  AS count")))
                ->joinLeft(array('mc'=>'mybb_city_master'),'mc.id = mww.city_id',null);


        $where = "1 ";

        $genCond = "";

        if (!empty($params['condition']['id'])) {
            $where .= " AND id = " . $params['condition']['id'] . "";
        }

        if (!empty($params['condition']['name'])) {
            $where .= " AND name LIKE '%" . $params['condition']['name'] . "%'";
        }

        if (!empty($params['condition']['email'])) {
            $where .= " AND mww.email = '" . $params['condition']['email'] . "'";
        }
        
        if (!empty($params['condition']['comments'])) {
            $where .= " AND comments LIKE '%" . $params['condition']['comments'] . "%'";
        }


        if (isset($params['condition']['status'])) {
            $where .= " AND mww.status = " . $params['condition']['status'];
        }
        
        if (!empty($params['condition']['city'])) {
            $where .= " AND mww.city_id = '" . $params['condition']['city'] . "'";
        }
        
        $where .= $genCond;

        if (!empty($where)) {
            $selectCount->where($where);
            $select->where($where);
        }

        //echo $selectCount; die;
        $resultCount = $this->getAdapter()->fetchCol($selectCount);


        $params['count'] = $resultCount[0];

        if (!$params['count']) {
            return $this->blankResult();
        }
        $this->getPagingSorting($params);

        $select->order($params['sidx'] . " " . $params['sord']);

        $select->limit($params['limit'], $params['start']);
        //echo $select; die; 
        $result = $this->getAdapter()->fetchAll($select);
        //dd($params);
        return array(
            'result' => $result,
            'total' => $params['totalPages'],
            'records' => $params['count'],
            'page' => $params['page'],
            'resultCount' => count($result)
        );
    }

    /**
     * This function is  use to featch all not found category record.
     *
     * Created By : Bidhan
     * Date : 14 Aug,2014	
     * @return void.  check live
     */
    public function dealRequested($params = array()) {


        $fields = array('*');

        if (!empty($params['fields']['main'])) {
            $fields = $params['fields']['main'];
        }

        $select = $this->getAdapter()->select()
                ->from(array('mda' => 'mybb_deal_alert'), $fields)
                ->joinLeft(array('mv' => 'mybb_vendors'), 'mda.vendorid = mv.id', array('mv.name as businessname'))
                ->joinLeft(array('mcm' => 'mybb_city_master'), 'mcm.id = mv.city', array('mcm.name as city_name'))
                ->joinLeft(array('mc' => 'mybb_category'), 'mv.category = mc.id', array('mc.name as catname'));


        $selectCount = $this->getAdapter()->select()
                ->from(array('mda' => 'mybb_deal_alert'), array(new Zend_Db_Expr("count(mda.id)  AS count")))
                ->joinLeft(array('mv' => 'mybb_vendors'), 'mda.vendorid = mv.id', null)
                ->joinLeft(array('mcm' => 'mybb_city_master'), 'mcm.id = mv.city', null)
                ->joinLeft(array('mc' => 'mybb_category'), 'mv.category = mc.id', null);



        $where = "1 ";

        $genCond = "";

        if (!empty($params['condition']['id'])) {
            $where .= " AND mda.id = " . $params['condition']['id'] . "";
        }



        if (!empty($params['condition']['email'])) {
            $where .= " AND mda.email = '" . $params['condition']['email'] . "'";
        }

        if (!empty($params['condition']['city'])) {
            $where .= " AND mv.city = '" . $params['condition']['city'] . "'";
        }
        
        if (isset($params['condition']['status'])) {
            $where .= " AND mda.status = " . $params['condition']['status'];
        }

        $where .= $genCond;

        if (!empty($where)) {
            $selectCount->where($where);
            $select->where($where);
        }

        //echo $selectCount; die;
        $resultCount = $this->getAdapter()->fetchCol($selectCount);


        $params['count'] = $resultCount[0];

        if (!$params['count']) {
            return $this->blankResult();
        }
        $this->getPagingSorting($params);

        $select->order($params['sidx'] . " " . $params['sord']);

        $select->limit($params['limit'], $params['start']);
        //echo $select; die; 
        $result = $this->getAdapter()->fetchAll($select);
        //dd($params);
        return array(
            'result' => $result,
            'total' => $params['totalPages'],
            'records' => $params['count'],
            'page' => $params['page'],
            'resultCount' => count($result)
        );
    }

    /**
     * This function is  use to featch all Reviews record.
     *
     * Created By : Bidhan
     * Date : 14 Aug,2014	
     * @return void.  check live
     */
    public function latestReview($params = array()) {


        $fields = array('*');

        if (!empty($params['fields']['main'])) {
            $fields = $params['fields']['main'];
        }

        $select = $this->getAdapter()->select()
                ->from(array('mrm' => $this->_review_master), $fields)
                ->joinInner(array('mv' => $this->_vendors), 'mrm.vendor_id = mv.id', array('mv.name as vendor_name'))
                ->joinInner(array('mc' => $this->_category), 'mv.category = mc.id', array('mc.name as catname'))
                ->joinInner(array('mu' => $this->_users), 'mu.id = mrm.user_id', array('mu.id as user_id','mu.email as email','mu.name as user_name'))
                ->joinInner(array('mcm' => 'mybb_city_master'), 'mrm.city = mcm.id', array('mcm.name as city_name'));


        $selectCount = $this->getAdapter()->select()
                ->from(array('mrm' => $this->_review_master), array(new Zend_Db_Expr("count(mrm.id)  AS count")))
                ->joinInner(array('mv' => $this->_vendors), 'mrm.vendor_id = mv.id', null)
                ->joinInner(array('mc' => $this->_category), 'mv.category = mc.id', null)
                ->joinInner(array('mu' => $this->_users), 'mu.id = mrm.user_id', null)
                ->joinInner(array('mcm' => 'mybb_city_master'), 'mrm.city = mcm.id', null);


        $where = "1 ";

        $genCond = "";

        if (!empty($params['condition']['id'])) {
            $where .= " AND mrm.id = " . $params['condition']['id'] . "";
        }



        if (!empty($params['condition']['user_name'])) {
            $where .= " AND mu.name LIKE '%" . $params['condition']['user_name'] . "%'";
        }
        
        if (!empty($params['condition']['email'])) {
            $where .= " AND mu.email = '" . $params['condition']['email'] . "'";
        }
        
        if (!empty($params['condition']['vendor_name'])) {
            $where .= " AND mv.name LIKE '%" . $params['condition']['vendor_name'] . "%'";
        }

        if (!empty($params['condition']['review'])) {
            $where .= " AND mrm.review LIKE '%" . $params['condition']['review'] . "%'";
        }

        if (isset($params['condition']['status'])) {
            $where .= " AND mrm.status = " . $params['condition']['status'];
        }
        
        if (!empty($params['condition']['city'])) {
            $where .= " AND mrm.city = " . $params['condition']['city'];
        }
        
        if (!empty($params['condition']['catname'])) {
            $where .= " AND mv.category = " . $params['condition']['catname'];
        }
        
        if (!empty($params['condition']['remarks'])) {
            $where .= " AND mrm.remarks LIKE '%" . $params['condition']['remarks'] . "%'";
        }
        
        $where .= $genCond;

        if (!empty($where)) {
            $selectCount->where($where);
            $select->where($where);
        }

        //echo $selectCount; die;
        $resultCount = $this->getAdapter()->fetchCol($selectCount);


        $params['count'] = $resultCount[0];


        if (!$params['count']) {
            return $this->blankResult();
        }
        $this->getPagingSorting($params);

        $select->order($params['sidx'] . " " . $params['sord']);

        $select->limit($params['limit'], $params['start']);
        //echo $select; die; 
        $result = $this->getAdapter()->fetchAll($select);
        //dd($params);
        return array(
            'result' => $result,
            'total' => $params['totalPages'],
            'records' => $params['count'],
            'page' => $params['page'],
            'resultCount' => count($result)
        );
    }

    /**
     * This function is used to status remarks.
     *
     * Created By : Bidhan
     * Date : 6 jun,2014	
     * @return void.
     */
    public function updateReviewStatusRecords($id, $remarks, $status) {
        $db = $this->getAdapter();
        if ($remarks != '') {

            $updatestatus = array('status' => $status, 'remarks' => $remarks);
            //$db->update('mybb_vendor_deals', $updatestatus, 'id = ' . $id);
            return $db->update($this->_review_master, $updatestatus, 'id = ' . $id);
        }
    }

    /**
     * This function is delete search.
     *
     * Created By : Bidhan
     * 
     * Date : 22 Aug,2014	
     * @return void.
     */
    public function deleteReviews($ids = array()) {
        if (!empty($ids)) {
            $db = $this->getAdapter();
            $condition = array('id IN(?)' => $ids);
            return $result = $db->delete($this->_review_master, $condition);
        }
    }

    /**
     * This function is  use to featch all Events record.
     *
     * Created By : Bidhan
     * Date : 23 Aug,2014	
     * @return void.  check live
     */
    public function fetchAllEvents($params = array()) {


        $fields = array('*');
        global $nowDate;
        if (!empty($params['fields']['main'])) {
            $fields = $params['fields']['main'];
        }

        $select = $this->getAdapter()->select()
                ->from(array('me' => 'mybb_events'), $fields);


        $selectCount = $this->getAdapter()->select()
                ->from(array('me' => 'mybb_events'), array(new Zend_Db_Expr("count(me.id)  AS count")));



        $where = "1 ";

        $genCond = "";

        if (!empty($params['condition']['id'])) {
            $where .= " AND id = " . $params['condition']['id'] . "";
        }



        if (!empty($params['condition']['email'])) {
            $where .= " AND email = '" . $params['condition']['email'] . "'";
        }

        if (!empty($params['condition']['event_type'])) {
            $where .= " AND event_type = '" . $params['condition']['event_type'] . "'";
        }

        if (!empty($params['condition']['event_name'])) {
            $where .= " AND event_name LIKE '%" . $params['condition']['event_name'] . "%'";
        }
        
        if (!empty($params['condition']['phone'])) {
            $where .= " AND phone LIKE '%" . $params['condition']['phone'] . "%'";
        }
        
        if (!empty($params['condition']['position'])) {
            $where .= " AND position = '" . $params['condition']['position'] . "'";
        }

        if (isset($params['condition']['status'])) {
            if ($params['condition']['status'] < 3) {
                $where .= " AND status = " . $params['condition']['status'];
                $where .= " AND event_end_date >= '" . $nowDate . "'";
            } else {
                $where .= " AND event_end_date < '" . $nowDate . "'";
            }
        }

        if (!empty($params['condition']['source'])) {
            $where .= " AND source = '" . $params['condition']['source'] . "'";
        }

        $where .= $genCond;

        if (!empty($where)) {
            $selectCount->where($where);
            $select->where($where);
        }

        //echo $selectCount; die;
        $resultCount = $this->getAdapter()->fetchCol($selectCount);


        $params['count'] = $resultCount[0];


        if (!$params['count']) {
            return $this->blankResult();
        }
        $this->getPagingSorting($params);

        $select->order($params['sidx'] . " " . $params['sord']);

        $select->limit($params['limit'], $params['start']);
        //echo $select; die; 
        $result = $this->getAdapter()->fetchAll($select);
        //dd($params);
        return array(
            'result' => $result,
            'total' => $params['totalPages'],
            'records' => $params['count'],
            'page' => $params['page'],
            'resultCount' => count($result)
        );
    }

    /**
     * This function is used to status remarks.
     *
     * Created By : Bidhan
     * Date : 23 Aug,2014	
     * @return void.
     */
    public function updateEventStatusRecords($id, $remarks, $status) {
        $db = $this->getAdapter();
        if ($remarks != '') {

            $updatestatus = array('status' => $status, 'remark' => $remarks);
            //$db->update('mybb_vendor_deals', $updatestatus, 'id = ' . $id);
            return $db->update($this->_events, $updatestatus, 'id = ' . $id);
        }
    }

    /**
     * This function is delete search.
     * Created By : Bidhan
     * Date : 23 Aug,2014	
     * @return void.
     */
    public function deleteEvents($ids = array()) {
        if (!empty($ids)) {
            $db = $this->getAdapter();
            $condition = array('id IN(?)' => $ids);
            return $result = $db->delete($this->_events, $condition);
        }
    }

    /**
     * This function is  use to featch all Advertise with us record.
     *
     * Created By : Bidhan
     * Date : 27 Aug,2014	
     * @return void.  check live
     */
    public function fetchAllAdvertise($params = array()) {


        $fields = array('*');
        global $nowDate;
        if (!empty($params['fields']['main'])) {
            $fields = $params['fields']['main'];
        }

        $select = $this->getAdapter()->select()
                ->from(array('ma' => $this->_advertise), $fields);


        $selectCount = $this->getAdapter()->select()
                ->from(array('ma' => $this->_advertise), array(new Zend_Db_Expr("count(ma.id)  AS count")));

        $where = "1 ";
        $genCond = "";

        if (!empty($params['condition']['id'])) {
            $where .= " AND id = " . $params['condition']['id'] . "";
        }

        if (!empty($params['condition']['name'])) {
            $where .= " AND name LIKE '%" . $params['condition']['name'] . "%'";
        }

        if (!empty($params['condition']['email'])) {
            $where .= " AND email = '" . $params['condition']['email'] . "'";
        }

        if (!empty($params['condition']['phone'])) {
            $where .= " AND phone = '" . $params['condition']['phone'] . "'";
        }

        if (!empty($params['condition']['comments'])) {
            $where .= " AND comments LIKE '%" . $params['condition']['comments'] . "%'";
        }

        if (!empty($params['condition']['prefer_time'])) {
            $where .= " AND prefer_time = '" . $params['condition']['prefer_time'] . "'";
        }

        if (isset($params['condition']['status'])) {

            $where .= " AND status = " . $params['condition']['status'];
        }

        $where .= $genCond;

        if (!empty($where)) {
            $selectCount->where($where);
            $select->where($where);
        }

        //echo $selectCount; die;
        $resultCount = $this->getAdapter()->fetchCol($selectCount);


        $params['count'] = $resultCount[0];


        if (!$params['count']) {
            return $this->blankResult();
        }
        $this->getPagingSorting($params);

        $select->order($params['sidx'] . " " . $params['sord']);

        $select->limit($params['limit'], $params['start']);
        //echo $select; die; 
        $result = $this->getAdapter()->fetchAll($select);
        //dd($params);
        return array(
            'result' => $result,
            'total' => $params['totalPages'],
            'records' => $params['count'],
            'page' => $params['page'],
            'resultCount' => count($result)
        );
    }

    /**
     * This function is used to status remarks.
     *
     * Created By : Bidhan
     * Date : 23 Aug,2014	
     * @return void.
     */
    public function updateAdvertiseStatusRecords($id, $remarks, $status) {
        $db = $this->getAdapter();
        if ($remarks != '') {

            $updatestatus = array('status' => $status, 'remarks' => $remarks);
            //$db->update('mybb_vendor_deals', $updatestatus, 'id = ' . $id);
            return $db->update($this->_advertise, $updatestatus, 'id = ' . $id);
        }
    }

    /**
     * This function is delete search.
     * Created By : Bidhan
     * Date : 23 Aug,2014	
     * @return void.
     */
    public function deleteAdvertiseRecords($ids = array()) {
        if (!empty($ids)) {
            $db = $this->getAdapter();
            $condition = array('id IN(?)' => $ids);
            return $result = $db->delete($this->_advertise, $condition);
        }
    }

    /**
     * This function is  use to featch all Contact us records.
     *
     * Created By : Bidhan
     * Date : 27 Aug,2014	
     * @return void.  check live
     */
    public function fetchAllContactUs($params = array()) {


        $fields = array('*');
        global $nowDate;
        if (!empty($params['fields']['main'])) {
            $fields = $params['fields']['main'];
        }

        $select = $this->getAdapter()->select()
                ->from(array('mc' => $this->_contactus), $fields);


        $selectCount = $this->getAdapter()->select()
                ->from(array('mc' => $this->_contactus), array(new Zend_Db_Expr("count(mc.id)  AS count")));

        $where = "1 ";
        $genCond = "";

        if (!empty($params['condition']['id'])) {
            $where .= " AND id = " . $params['condition']['id'] . "";
        }

        if (!empty($params['condition']['name'])) {
            $where .= " AND name LIKE '%" . $params['condition']['name'] . "%'";
        }

        if (!empty($params['condition']['email'])) {
            $where .= " AND email = '" . $params['condition']['email'] . "'";
        }



        if (!empty($params['condition']['message'])) {
            $where .= " AND message LIKE '%" . $params['condition']['message'] . "%'";
        }


        if (isset($params['condition']['status'])) {

            $where .= " AND status = " . $params['condition']['status'];
        }

        $where .= $genCond;

        if (!empty($where)) {
            $selectCount->where($where);
            $select->where($where);
        }

        //echo $selectCount; die;
        $resultCount = $this->getAdapter()->fetchCol($selectCount);


        $params['count'] = $resultCount[0];


        if (!$params['count']) {
            return $this->blankResult();
        }
        $this->getPagingSorting($params);

        $select->order($params['sidx'] . " " . $params['sord']);

        $select->limit($params['limit'], $params['start']);
        //echo $select; die; 
        $result = $this->getAdapter()->fetchAll($select);
        //dd($params);
        return array(
            'result' => $result,
            'total' => $params['totalPages'],
            'records' => $params['count'],
            'page' => $params['page'],
            'resultCount' => count($result)
        );
    }

    /**
     * This function is used to status remarks.
     *
     * Created By : Bidhan
     * Date : 27 Aug,2014	
     * @return void.
     */
    public function updateContactUsStatusRecords($id, $remarks, $status) {
        $db = $this->getAdapter();
        if ($remarks != '') {

            $updatestatus = array('status' => $status, 'remarks' => $remarks);
            //$db->update('mybb_vendor_deals', $updatestatus, 'id = ' . $id);
            return $db->update($this->_contactus, $updatestatus, 'id = ' . $id);
        }
    }

    /**
     * This function is delete contact us records.
     * Created By : Bidhan
     * Date : 27 Aug,2014	
     * @return void.
     */
    public function deleteContactusRecords($ids = array()) {
        if (!empty($ids)) {
            $db = $this->getAdapter();
            $condition = array('id IN(?)' => $ids);
            return $result = $db->delete($this->_contactus, $condition);
        }
    }

    /**
     * This function is  use to featch all SMS/Email me records.     
     * Created By : Bidhan
     * Date : 28 Aug,2014	
     * @return void.  check live
     */
    public function fetchSmsEmailMeRecords($params = array()) {


        $fields = array('*');
        global $nowDate;
        if (!empty($params['fields']['main'])) {
            $fields = $params['fields']['main'];
        }

        $select = $this->getAdapter()->select()
                ->from(array('mse' => $this->_smsemailme), $fields)
                ->joinInner(array('mv' => $this->_vendors), 'mv.id = mse.vendorid', array('mv.name as vendor_name'))
                ->joinInner(array('mcm' => 'mybb_city_master'), 'mv.city = mcm.id', array('mcm.name as city_name'))
                ->joinLeft(array('mu' => $this->_users), 'mu.id = mse.userid', array('mu.name as user_name'));



        $selectCount = $this->getAdapter()->select()
                ->from(array('mse' => $this->_smsemailme), array(new Zend_Db_Expr("count(mse.id)  AS count")))
                ->joinInner(array('mv' => $this->_vendors), 'mv.id = mse.vendorid', null)
                ->joinInner(array('mcm' => 'mybb_city_master'), 'mv.city = mcm.id', null)
                ->joinLeft(array('mu' => $this->_users), 'mu.id = mse.userid', null);

        $where = "1 ";
        $genCond = "";

        if (!empty($params['condition']['id'])) {
            $where .= " AND mse.id = " . $params['condition']['id'] . "";
        }

        if (!empty($params['condition']['name'])) {
            $where .= " AND mse.name LIKE '%" . $params['condition']['name'] . "%'";
        }

        if (!empty($params['condition']['user_name'])) {
            $where .= " AND mu.name LIKE '%" . $params['condition']['user_name'] . "%'";
        }

        if (!empty($params['condition']['vendor_name'])) {
            $where .= " AND mv.name LIKE '%" . $params['condition']['vendor_name'] . "%'";
        }

        if (!empty($params['condition']['email'])) {
            $where .= " AND mse.email = '" . $params['condition']['email'] . "'";
        }

        if (!empty($params['condition']['mobile'])) {
            $where .= " AND mse.mobile = '" . $params['condition']['mobile'] . "'";
        }

        if (isset($params['condition']['status'])) {
            $where .= " AND mse.status = " . $params['condition']['status'];
        }

        if (isset($params['condition']['date'])) {
            $where .= " AND mse.date >= DATE(DATE_SUB(NOW(), INTERVAL 1 DAY))";
        }
        
        if (isset($params['condition']['city'])) {
            $where .= " AND mv.city = " . $params['condition']['city'];
        }
        
        if (isset($params['condition']['section'])) {
            $where .= " AND mse.section = " . $params['condition']['section'];
        }else{
            $where .= " AND mse.section != 1";
        }
        
        $where .= $genCond;

        if (!empty($where)) {
            $selectCount->where($where);
            $select->where($where);
        }

        //echo $selectCount; die;
        $resultCount = $this->getAdapter()->fetchCol($selectCount);


        $params['count'] = $resultCount[0];


        if (!$params['count']) {
            return $this->blankResult();
        }

        $this->getPagingSorting($params);

        $select->order($params['sidx'] . " " . $params['sord']);

        $select->limit($params['limit'], $params['start']);

        //echo $select; die; 

        $result = $this->getAdapter()->fetchAll($select);
        //dd($params);
        return array(
            'result' => $result,
            'total' => $params['totalPages'],
            'records' => $params['count'],
            'page' => $params['page'],
            'resultCount' => count($result)
        );
    }

    /**
     * This function is  use to featch all SMS/Email me records.     
     * Created By : Bidhan
     * Date : 28 Aug,2014	
     * @return void.  check live
     */
    public function fetchEventSmsEmailMeRecords($params = array()) {

        $fields = array('*');
        global $nowDate;
        if (!empty($params['fields']['main'])) {
            $fields = $params['fields']['main'];
        }

        $select = $this->getAdapter()->select()
                ->from(array('mse' => 'mybb_eventsmsemailme'), $fields)
                ->joinInner(array('me' => $this->_events), 'me.id = mse.eventid', array('me.event_name', 'me.id as event_id'))
                ->joinLeft(array('mu' => $this->_users), 'mu.id = mse.userid', array('mu.name as user_name'));

        $selectCount = $this->getAdapter()->select()
                ->from(array('mse' => 'mybb_eventsmsemailme'), array(new Zend_Db_Expr("count(mse.id)  AS count")))
                ->joinInner(array('me' => $this->_events), 'me.id = mse.eventid', array('me.event_name', 'me.id as event_id'))
                ->joinLeft(array('mu' => $this->_users), 'mu.id = mse.userid', null);

        $where = "1 ";
        $genCond = "";

        if (!empty($params['condition']['id'])) {
            $where .= " AND mse.id = " . $params['condition']['id'] . "";
        }

        if (!empty($params['condition']['name'])) {
            $where .= " AND mse.name LIKE '%" . $params['condition']['name'] . "%'";
        }

        if (!empty($params['condition']['event_name'])) {
            $where .= " AND me.event_name LIKE '%" . $params['condition']['event_name'] . "%'";
        }

        if (!empty($params['condition']['user_name'])) {
            $where .= " AND mu.name LIKE '%" . $params['condition']['user_name'] . "%'";
        }

        if (!empty($params['condition']['email'])) {
            $where .= " AND mse.email = '" . $params['condition']['email'] . "'";
        }

        if (!empty($params['condition']['mobile'])) {
            $where .= " AND mse.mobile = '" . $params['condition']['mobile'] . "'";
        }

        if (isset($params['condition']['date'])) {
            $where .= " AND mse.date >= DATE(DATE_SUB(NOW(), INTERVAL 1 DAY))";
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
     * This function is  use to featch all SMS/Email me records.     
     * Created By : Bidhan
     * Date : 28 Aug,2014	
     * @return void.  check live
     */
    public function fetchNewsletterRecords($params = array()) {

        $fields = array('*');
        global $nowDate;
        if (!empty($params['fields']['main'])) {
            $fields = $params['fields']['main'];
        }

        $select = $this->getAdapter()->select()
                ->from(array('mn' => 'mybb_newletter'), $fields);

        $selectCount = $this->getAdapter()->select()
                ->from(array('mn' => 'mybb_newletter'), array(new Zend_Db_Expr("count(mn.id)  AS count")));

        $where = "1 ";
        $genCond = "";

        if (!empty($params['condition']['id'])) {
            $where .= " AND mn.id = " . $params['condition']['id'] . "";
        }

        if (!empty($params['condition']['email'])) {
            $where .= " AND mn.email = '" . $params['condition']['email'] . "'";
        }

        if (isset($params['condition']['date'])) {
            $where .= " AND mn.date >= DATE(DATE_SUB(NOW(), INTERVAL 1 DAY))";
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
     * This function is used to status remarks.
     *
     * Created By : Bidhan
     * Date : 27 Aug,2014	
     * @return void.
     */
    public function updateSmsEmailMeStatusRecords($id, $remarks, $status) {
        $db = $this->getAdapter();
        if ($remarks != '') {

            $updatestatus = array('status' => $status, 'remarks' => $remarks);
            //$db->update('mybb_vendor_deals', $updatestatus, 'id = ' . $id);
            return $db->update($this->_smsemailme, $updatestatus, 'id = ' . $id);
        }
    }

    /**
     * This function is delete contact us records.
     * Created By : Bidhan
     * Date : 27 Aug,2014	
     * @return void.
     */
    public function deleteSmsEmailMeRecords($ids = array()) {
        if (!empty($ids)) {
            $db = $this->getAdapter();
            $condition = array('id IN(?)' => $ids);
            return $result = $db->delete($this->_smsemailme, $condition);
        }
    }

    /**
     * This function is delete event sms records.
     * Created By : Bidhan
     * Date : 27 Aug,2014	
     * @return void.
     */
    public function deleteEventSmsEmailMeRecords($ids = array()) {
        if (!empty($ids)) {
            $db = $this->getAdapter();
            $condition = array('id IN(?)' => $ids);
            return $result = $db->delete($this->_eventsmsemailme, $condition);
        }
    }

    /**
     * This function is delete newsletter records.
     * Created By : Bidhan
     * Date : 27 Aug,2014	
     * @return void.
     */
    public function deleteNewsletterRecords($ids = array()) {
        if (!empty($ids)) {
            $db = $this->getAdapter();
            $condition = array('id IN(?)' => $ids);
            return $result = $db->delete('mybb_newletter', $condition);
        }
    }

    /**
     * This function is  use to featch all Quick Quote records.     
     * Created By : Bidhan
     * Date : 4 Nov,2014	
     * @return void.  check live
     */
    public function fetchQuickQuoteSendEnqRecords($params = array()) {


        $fields = array('*');
        global $nowDate;
        if (!empty($params['fields']['main'])) {
            $fields = $params['fields']['main'];
        }
       
        $select = $this->getAdapter()->select()
                ->from(array('mqq' => $this->_quickquote), $fields)
                ->joinInner(array('mcm' =>'mybb_city_master'),'mcm.id = mqq.city_id',array('mcm.name as city_name'))
                // ->joinInner(array('mv' =>$this->_vendors),'mv.id = mqq.vendorid',array('mv.name as vendor_name'))
                ->joinInner(array('mv' => $this->_vendors), 'mv.id = mqq.vendorid', array('GROUP_CONCAT(distinct mv.name) AS vendor_name'))
                ->joinInner(array('mc' => $this->_category), 'mc.id = mqq.category_id', array('mc.name as category_name'))
                ->joinLeft(array('mz' => $this->_zone), 'mz.id = mqq.zone_id', array('mz.name AS zone_name'));


        $selectCount = $this->getAdapter()->select()
                ->from(array('mqq' => $this->_quickquote), array(new Zend_Db_Expr("count(mqq.id)  AS count")))
                ->joinInner(array('mcm' =>'mybb_city_master'),'mcm.id = mqq.city_id',null)
                ->joinInner(array('mv' => $this->_vendors), 'mv.id = mqq.vendorid', null)
                ->joinInner(array('mc' => $this->_category), 'mc.id = mqq.category_id', null)
                ->joinLeft(array('mz' => $this->_zone), 'mz.id = mqq.zone_id', null);
        /*
        if ($params['condition']['section_type'] == 1) {
            $select->joinLeft(array('mt' => $this->_tags), 'mt.id = mqq.service_id', array('mt.tag_name AS service_name'));
            $selectCount->joinLeft(array('mt' => $this->_tags), 'mt.id = mqq.service_id', null);
        }*/

        $where = "1 ";
        $genCond = "";

        if (!empty($params['condition']['id'])) {
            $where .= " AND mqq.id = " . $params['condition']['id'] . "";
        }

        if (!empty($params['condition']['name'])) {
            $where .= " AND mqq.name LIKE '%" . $params['condition']['name'] . "%'";
        }

        if (!empty($params['condition']['vendor_name'])) {
            $where .= " AND mv.name LIKE '%" . $params['condition']['vendor_name'] . "%'";
        }

        if (!empty($params['condition']['email'])) {
            $where .= " AND mqq.email = '" . $params['condition']['email'] . "'";
        }

        if (!empty($params['condition']['mobile'])) {
            $where .= " AND mqq.mobile = '" . $params['condition']['mobile'] . "'";
        }


        if (isset($params['condition']['date'])) {
            $where .= " AND mqq.date >= DATE(DATE_SUB(NOW(), INTERVAL 1 DAY))";
        }

        if (isset($params['condition']['section_type'])) {
            $where .= " AND mqq.section_type = " . $params['condition']['section_type'] . "";
        }

        if (isset($params['condition']['city'])) {
            $where .= " AND mqq.city_id = " . $params['condition']['city'] . "";
        }
        
        if ($params['condition']['section_type'] == 1) {
            /*
            if (!empty($params['condition']['service_name'])) {
                $where .= " AND mt.tag_name LIKE '%" . $params['condition']['service_name'] . "%'";
            }*/

            if (!empty($params['condition']['zone_name'])) {
                $where .= " AND mz.name LIKE '%" . $params['condition']['zone_name'] . "%'";
            }
            
            
        }

        if (!empty($params['condition']['category_name'])) {
            $where .= " AND mc.name LIKE '%" . $params['condition']['category_name'] . "%'";
        }
        
        if (!empty($params['condition']['status'])) {
                $where .= " AND mqq.status = ".$params['condition']['status'];
        }
            
        $where .= $genCond;

        if (!empty($where)) {
            $selectCount->where($where);
            $select->where($where);
        }
        //echo $params['condition']['serviceCount']; die;
        if (isset($params['condition']['serviceCount'])) {
            if ($params['condition']['section_type'] == 1) {
                $selectCount->group('mqq.service_id');
                $selectCount->group('mqq.enquiry_no');
                $select->group('mqq.service_id');
                $select->group('mqq.enquiry_no');
            } else if ($params['condition']['section_type'] == 2) {
                $selectCount->group('mqq.category_id');
                $selectCount->group('mqq.enquiry_no');
                $select->group('mqq.category_id');
                $select->group('mqq.enquiry_no');
            }else if ($params['condition']['section_type'] == 3) {
                $selectCount->group('mqq.category_id');
                $selectCount->group('mqq.enquiry_no');
                $select->group('mqq.category_id');
                $select->group('mqq.enquiry_no');
            }
        }
        //echo $selectCount; die;
        $resultCount = $this->getAdapter()->fetchCol($selectCount);
        if (isset($params['condition']['serviceCount'])) {
            $params['totalRecordsCount'] = count($resultCount);
            
            
        } else {
            $params['totalRecordsCount'] = 0;
        }

        if (isset($resultCount[0])) {
            $params['count'] = count($resultCount);
        } else {
            $params['count'] = 0;
        }
        
        if (!$params['count']) {
            return $this->blankResult();
        }
        
        $this->getPagingSorting($params);

        $select->order($params['sidx'] . " " . $params['sord']);

        $select->limit($params['limit'], $params['start']);

       // echo $select; die; 

        $result = $this->getAdapter()->fetchAll($select);
        //dd($result);
        return array(
            'result' => $result,
            'total' => $params['totalPages'],
            'records' => $params['count'],
            'page' => $params['page'],
            'resultCount' => count($result),
            'totalRecordsCount' => $params['totalRecordsCount']
        );
    }

    /**
     * This function is delete Quick Quote records.
     * Created By : Bidhan
     * Date : 4 Nov,2014	
     * @return void.
     */
    public function deleteQuickQuoteRecords($params = array()) {
        if (!empty($params)) {
            $db = $this->getAdapter();
            $condition = array('id IN(?)' => $params['ids'], 'section_type = ?' => $params['section_type']);
            //dd($condition);
            return $result = $db->delete($this->_quickquote, $condition);
        }
    }

    /**
     * This function is fetchLoginLogoutRecords.
     * Created By : JItendra	
     * Date : 07, Nov,2014	
     * @return void.
     */
    public function fetchLoginLogoutRecords($params = array()) {


        $fields = array('*');
        global $nowDate;
        if (!empty($params['fields']['main'])) {
            $fields = $params['fields']['main'];
        }

        $select = $this->getAdapter()->select()
                ->from(array('mse' => $this->_login_logout_details), $fields)
                ->joinInner(array('mu' => $this->_users), 'mu.id = mse.userid', array('mu.name as name', 'mu.email as email'));

        $selectCount = $this->getAdapter()->select()
                ->from(array('mse' => $this->_login_logout_details), array(new Zend_Db_Expr("count(mse.id)  AS count")))
                ->joinInner(array('mu' => $this->_users), 'mu.id = mse.userid', null);

        $where = "1 ";
        $genCond = "";

        if (!empty($params['condition']['name'])) {
            $where .= " AND name LIKE '%" . $params['condition']['name'] . "%'";
        }

        if (!empty($params['condition']['email'])) {
            $where .= " AND email = '" . $params['condition']['email'] . "'";
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
        //echo $select; die; 
        $result = $this->getAdapter()->fetchAll($select);
        //dd($params);
        return array(
            'result' => $result,
            'total' => $params['totalPages'],
            'records' => $params['count'],
            'page' => $params['page'],
            'resultCount' => count($result)
        );
    }

    /**
     * This function is delete loged in user records records.
     * Created By : JItendra	
     * Date : 07, Nov,2014	
     * @return void.
     */
    public function deleteLogedinRecords($ids = array()) {
        if (!empty($ids)) {
            $db = $this->getAdapter();
            $condition = array('id IN(?)' => $ids);
            return $result = $db->delete($this->_login_logout_details, $condition);
        }
    }

    /**
     * This function is delete user register records records.
     * Created By : JItendra	
     * Date : 08, Nov,2014	
     * @return void.
     */
    public function deleteUserRegisterRecords($ids = array()) {
        if (!empty($ids)) {
            $db = $this->getAdapter();
            $condition = array('id IN(?)' => $ids);
            return $result = $db->delete($this->_name, $condition);
        }
    }

    /**
     * This function is fetch registered user Records.
     * Created By : JItendra	
     * Date : 08, Nov,2014	
     * @return void.
     */
    public function fetchRegisteredUserRecords($params = array()) {


        $fields = array('*');
        global $nowDate;
        if (!empty($params['fields']['main'])) {
            $fields = $params['fields']['main'];
        }

        $select = $this->getAdapter()->select()
                ->from(array('mse' => $this->_name));

        $selectCount = $this->getAdapter()->select()
                ->from(array('mse' => $this->_name), array(new Zend_Db_Expr("count(mse.id)  AS count")));

        $where = "1 ";
        $genCond = "";

        if (!empty($params['condition']['name'])) {
            $where .= " AND name LIKE '%" . $params['condition']['name'] . "%'";
        }

        if (!empty($params['condition']['email'])) {
            $where .= " AND email = '" . $params['condition']['email'] . "'";
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
        //echo $select; die; 
        $result = $this->getAdapter()->fetchAll($select);
        //dd($params);
        return array(
            'result' => $result,
            'total' => $params['totalPages'],
            'records' => $params['count'],
            'page' => $params['page'],
            'resultCount' => count($result)
        );
    }

    public function getEnquiredVendors($params = array()) {

        $fields = array('*');
        if (!empty($params['fields']['main'])) {
            $fields = $params['fields']['main'];
        }

        $select = $this->getAdapter()->select()
                ->from(array('mqq' => $this->_quickquote), $fields)
                ->joinInner(array('mv' => $this->_vendors), 'mv.id = mqq.vendorid', array('mv.name as vendor_name'));

        $where = "1 ";
        $genCond = "";

        if (!empty($params['condition']['enquiry_no'])) {
            $where .= " AND mqq.enquiry_no = '" . $params['condition']['enquiry_no'] . "'";
        }

        if (!empty($params['condition']['service_id'])) {
            $where .= " AND mqq.service_id = " . $params['condition']['service_id'] . "";
        }

        if (isset($params['condition']['section_type'])) {
            $where .= " AND mqq.section_type = " . $params['condition']['section_type'] . "";
        }

        $where .= $genCond;

        if (!empty($where)) {
            $select->where($where);
        }

        $select->order($params['sidx'] . " " . $params['sord']);
        echo $select;
        die;
        $result = $this->getAdapter()->fetchAll($select);
        return $result;
        //dd($result);
    }

    /**
     * This function is Fetch website created date.
     * Created By : JItendra	
     * Date : 28, Nov,2014	
     * @return void.
     */
    public function fetchWebsiteActivatedRecords($params = array()) {


        $fields = array('*');
        global $nowDate;
        if (!empty($params['fields']['main'])) {
            $fields = $params['fields']['main'];
        }


        $select = $this->getAdapter()->select()
                ->from(array('mse' => $this->_website_created), $fields)
                ->joinInner(array('mu' => $this->_users), 'mu.id = mse.user_id', array('mu.name as name', 'mu.email as email'));

        $selectCount = $this->getAdapter()->select()
                ->from(array('mse' => $this->_website_created), array(new Zend_Db_Expr("count(mse.id)  AS count")))
                ->joinInner(array('mu' => $this->_users), 'mu.id = mse.user_id', null);

        $where = "1 ";
        $genCond = "";

        if (!empty($params['condition']['name'])) {
            $where .= " AND name LIKE '%" . $params['condition']['name'] . "%'";
        }

        if (!empty($params['condition']['email'])) {
            $where .= " AND email = '" . $params['condition']['email'] . "'";
        }

        $where .= " AND log_section = '1'";

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
        //echo $select; die; 
        $result = $this->getAdapter()->fetchAll($select);
        //dd($params);
        return array(
            'result' => $result,
            'total' => $params['totalPages'],
            'records' => $params['count'],
            'page' => $params['page'],
            'resultCount' => count($result)
        );
    }

    /**
     * This function is count website activated yesterday.
     *
     * Created By : Jitendra
     * Date : 28 May,2014	
     * @return void.
     */
    public function getWebsiteActivatedCount() {


        /* @ **********added by jkm on 28 november********** */
        /* $startTime = date('Y-m-d', strtotime("yesterday")) . ' ' . '00:00:00';
          $endTime = date('Y-m-d', strtotime("yesterday")) . ' ' . '24:59:59';
          $db = $this->getAdapter();
          $query = $db->select()
          ->from(array('on' => 'mybb_planning_site_log'), array('on.*'))
          ->where("on.log_time >= '$startTime' and on.log_time <= '$endTime' and on.log_section = '1'");
          $select = count($db->fetchAll($query));
          return $select; */

        $yesterdayDate = date("Y-m-j", strtotime("yesterday"));
        //echo $yesterdayDate ; die;
        $sql = "SELECT id FROM mybb_planning_site_log where log_time LIKE '%" . $yesterdayDate . "%' and log_section = '1' ";
        $select = $this->getAdapter()->fetchAll($sql);
        return $select;
    }

    /**
     * This function is delete website created records.
     * Created By : JItendra	
     * Date : 28, Nov,2014	
     * @return void.
     */
    public function deleteWebsiteCreatedRecords($ids = array()) {
        if (!empty($ids)) {
            $db = $this->getAdapter();
            $condition = array('id IN(?)' => $ids);
            return $result = $db->delete('mybb_planning_site_log', $condition);
        }
    }

    // planning activation code

    /**
     * This function is Fetch website created date.
     * Created By : JItendra	
     * Date : 07, Nov,2014	
     * @return void.
     */
    public function fetchPlanningActivatedRecords($params = array()) {


        $fields = array('*');
        global $nowDate;
        if (!empty($params['fields']['main'])) {
            $fields = $params['fields']['main'];
        }


        $select = $this->getAdapter()->select()
                ->from(array('mse' => $this->_website_created), $fields)
                ->joinInner(array('mu' => $this->_users), 'mu.id = mse.user_id', array('mu.name as name', 'mu.email as email'));

        $selectCount = $this->getAdapter()->select()
                ->from(array('mse' => $this->_website_created), array(new Zend_Db_Expr("count(mse.id)  AS count")))
                ->joinInner(array('mu' => $this->_users), 'mu.id = mse.user_id', null);

        $where = "1 ";
        $genCond = "";

        if (!empty($params['condition']['name'])) {
            $where .= " AND name LIKE '%" . $params['condition']['name'] . "%'";
        }

        if (!empty($params['condition']['email'])) {
            $where .= " AND email = '" . $params['condition']['email'] . "'";
        }

        $where .= " AND log_section = '2'";

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
        //echo $select; die; 
        $result = $this->getAdapter()->fetchAll($select);
        //dd($params);
        return array(
            'result' => $result,
            'total' => $params['totalPages'],
            'records' => $params['count'],
            'page' => $params['page'],
            'resultCount' => count($result)
        );
    }

    /**
     * This function is count website activated yesterday.
     *
     * Created By : Jitendra
     * Date : 28 May,2014	
     * @return void.
     */
    public function getPlanningActivatedCount() {


        /* @ **********added by jkm on 5 november********** */
        /* $startTime = date('Y-m-d', strtotime("yesterday")) . ' ' . '00:00:00';
          $endTime = date('Y-m-d', strtotime("yesterday")) . ' ' . '24:59:59';
          $db = $this->getAdapter();
          $query = $db->select()
          ->from(array('on' => 'mybb_planning_site_log'), array('on.*'))
          ->where("on.log_time >= '$startTime' and on.log_time <= '$endTime' and on.log_section = '2'");
          $select = count($db->fetchAll($query));
          return $select; */

        $yesterdayDate = date("Y-m-j", strtotime("yesterday"));
        //echo $yesterdayDate ; die;
        $sql = "SELECT id FROM mybb_planning_site_log where log_time LIKE '%" . $yesterdayDate . "%' and log_section = '2' ";
        $select = $this->getAdapter()->fetchAll($sql);
        return $select;
    }

    /**
     * This function is delete website created records.
     * Created By : JItendra	
     * Date : 28, Nov,2014	
     * @return void.
     */
    public function deletePlanningCreatedRecords($ids = array()) {
        if (!empty($ids)) {
            $db = $this->getAdapter();
            $condition = array('id IN(?)' => $ids);
            return $result = $db->delete('mybb_planning_site_log', $condition);
        }
    }

    public function fetchFirstReviewData($id = NULL) {
        $id = $id;
        $sql = "SELECT review,user_id FROM mybb_review_master where id = '$id'";
        return $this->getAdapter()->fetchAll($sql);
    }

    public function fetchUserEmail($id = NULL) {
        $id = $id;
        $sql = "SELECT email FROM mybb_users where id = '$id'";
        return $this->getAdapter()->fetchAll($sql);
    }
    
    
    /**
     * This function is  use to featch all not found deal downloaded record.
     *
     * Created By : Bidhan
     * Date : 12 Dec,2015	
     * @return void.  check live
     */
    public function dealDownloaded($params = array()) {


        $fields = array('*');

        if (!empty($params['fields']['main'])) {
            $fields = $params['fields']['main'];
        }

        $select = $this->getAdapter()->select()
                ->from(array('mdr' => 'mybb_deals_request'), $fields)
                ->joinInner(array('mvd' => 'mybb_vendor_deals'), 'mvd.id = mdr.deal_id', array('mvd.deals'))
                ->joinLeft(array('mv' => 'mybb_vendors'), 'mdr.vendor_id = mv.id', array('mv.name as businessname'))
                ->joinLeft(array('mcm' => 'mybb_city_master'), 'mcm.id = mv.city', array('mcm.name as city_name'))
                ->joinLeft(array('mc' => 'mybb_category'), 'mv.category = mc.id', array('mc.name as catname'));


        $selectCount = $this->getAdapter()->select()
                ->from(array('mdr' => 'mybb_deals_request'), array(new Zend_Db_Expr("count(mdr.id)  AS count")))
                ->joinInner(array('mvd' => 'mybb_vendor_deals'), 'mvd.id = mdr.deal_id', null)
                ->joinLeft(array('mv' => 'mybb_vendors'), 'mdr.vendor_id = mv.id', null)
                ->joinLeft(array('mcm' => 'mybb_city_master'), 'mcm.id = mv.city', null)
                ->joinLeft(array('mc' => 'mybb_category'), 'mv.category = mc.id', null);



        $where = "1 ";

        $genCond = "";

        if (!empty($params['condition']['id'])) {
            $where .= " AND mdr.id LIKE '%" . $params['condition']['id'] . "";
        }
        if (!empty($params['condition']['name'])) {
            $where .= " AND mdr.name = '" . $params['condition']['name'] . "%'";
        }

         if (!empty($params['condition']['mobile_no'])) {
            $where .= " AND mdr.mobile_no = '" . $params['condition']['mobile_no'] . "'";
        }

       
        if (!empty($params['condition']['email'])) {
            $where .= " AND mdr.email LIKE '%" . $params['condition']['email'] . "%'";
        }
        
        if (!empty($params['condition']['deals'])) {
            $where .= " AND mvd.deals LIKE '%" . $params['condition']['deals'] . "%'";
           // $where .= " AND name LIKE '%" . $params['condition']['name'] . "%'";
        }

        if (isset($params['condition']['status'])) {
            $where .= " AND mdr.status = " . $params['condition']['status'];
        }
        
        if (isset($params['condition']['city'])) {
            $where .= " AND mv.city = " . $params['condition']['city'];
        }
        
        $where .= $genCond;

        if (!empty($where)) {
            $selectCount->where($where);
            $select->where($where);
        }

        //echo $selectCount; die;
        $resultCount = $this->getAdapter()->fetchCol($selectCount);


        $params['count'] = $resultCount[0];

        if (!$params['count']) {
            return $this->blankResult();
        }
        $this->getPagingSorting($params);

        $select->order($params['sidx'] . " " . $params['sord']);

        $select->limit($params['limit'], $params['start']);
        //echo $select; die; 
        $result = $this->getAdapter()->fetchAll($select);
        //dd($params);
        return array(
            'result' => $result,
            'total' => $params['totalPages'],
            'records' => $params['count'],
            'page' => $params['page'],
            'resultCount' => count($result)
        );
    }
    
     /**
     * This function is delete this selected id downloaded deals.
     *
     * Created By : Bidhan
     * Date : 10 Dec,2015
     * @return void.
     */
    public function deleteDownloadedDealRecords($id) {
        $db = $this->getAdapter();
        $condition = array('id IN (?)' => split(',', $id));
        $delete = $db->delete($this->_deals_request, $condition);
    }
    
    
     /**
     * This function is  use to featch all not found deal downloaded record.
     *
     * Created By : Bidhan
     * Date : 12 Dec,2015	
     * @return void.  check live
     */
    public function instaBookingUserData($params = array()) {


        $fields = array('*');

        if (!empty($params['fields']['main'])) {
            $fields = $params['fields']['main'];
        }

        $select = $this->getAdapter()->select()
                ->from(array('miba' => 'mybb_insta_booking_answer'), $fields)
                ->joinLeft(array('mu' => 'mybb_users'), 'mu.id = miba.user_id', array('mu.id AS user_id','mu.name AS user_name','mu.email AS user_email','mu.phone AS user_phone'))
                ->joinInner(array('mv' => 'mybb_vendors'), 'miba.vendor_id = mv.id', array('mv.name as vendor_name','mv.id as vendor_id'))
                ->joinInner(array('mcm' => 'mybb_city_master'), 'mcm.id = miba.city_id', array('mcm.name as city_name'))
                ->joinInner(array('mc' => 'mybb_category'), 'miba.category_id = mc.id', array('mc.name as cat_name'));


        $selectCount = $this->getAdapter()->select()
                ->from(array('miba' => 'mybb_insta_booking_answer'), array(new Zend_Db_Expr("count(miba.id)  AS count")))
                ->joinLeft(array('mu' => 'mybb_users'), 'mu.id = miba.user_id', null)
                ->joinInner(array('mv' => 'mybb_vendors'), 'miba.vendor_id = mv.id', null)
                ->joinInner(array('mcm' => 'mybb_city_master'), 'mcm.id = miba.city_id', null)
                ->joinInner(array('mc' => 'mybb_category'), 'miba.category_id = mc.id', null);



        $where = "1 ";

        $genCond = "";

        if (!empty($params['condition']['id'])) {
            $where .= " AND miba.id = '" . $params['condition']['id'] . "";
        }
        if (!empty($params['condition']['name'])) {
            $where .= " AND miba.name LIKE '%" . $params['condition']['name'] . "%' OR mu.name = '%" . $params['condition']['name'] . "%'"; 
        }

         if (!empty($params['condition']['contact_no'])) {
            $where .= " AND miba.contact_no LIKE '%" . $params['condition']['contact_no'] . "%' OR mu.phone = '%" . $params['condition']['contact_no'] . "%'"; 
            
        }

       
        if (!empty($params['condition']['email'])) {
            $where .= " AND miba.email LIKE '%" . $params['condition']['email'] . "%' OR mu.email = '%" . $params['condition']['email'] . "%'"; 
            
        }
        
        if (!empty($params['condition']['vendor_name'])) {
            $where .= " AND mv.name LIKE '%" . $params['condition']['vendor_name'] . "%'";
           // $where .= " AND name LIKE '%" . $params['condition']['name'] . "%'";
        }

        if (isset($params['condition']['status'])) {
            $where .= " AND miba.status = " . $params['condition']['status'];
        }
        
        if (isset($params['condition']['city'])) {
            $where .= " AND miba.city_id = " . $params['condition']['city'];
        }
        
        if (isset($params['condition']['category'])) {
            $where .= " AND miba.category_id = " . $params['condition']['category'];
        }
        
        $where .= $genCond;

        if (!empty($where)) {
            $selectCount->where($where);
            $select->where($where);
        }
        $selectCount->group('miba.insta_booking_no');
        
        $select->group('miba.insta_booking_no');
        
        //echo $select; die;
        $resultCount = $this->getAdapter()->fetchCol($selectCount);


//        $params['count'] = $resultCount[0];
//
//        if (!$params['count']) {
//            return $this->blankResult();
//        }
        
        
        if (isset($resultCount[0])) {
            $params['count'] = count($resultCount);
        } else {
            $params['count'] = 0;
        }
        
        if (!$params['count']) {
            return $this->blankResult();
        }
        
        
        
        $this->getPagingSorting($params);

        $select->order($params['sidx'] . " " . $params['sord']);

        $select->limit($params['limit'], $params['start']);
        //echo $select; die; 
        $result = $this->getAdapter()->fetchAll($select);
        //dd($params);
        return array(
            'result' => $result,
            'total' => $params['totalPages'],
            'records' => $params['count'],
            'page' => $params['page'],
            'resultCount' => count($result)
        );
        
        
    }
    
    /**
     * This function is  use to featch all not found category record.
     *
     * Created By : Bidhan
     * Date : 14 Aug,2014	
     * @return void.  check live
     */
    public function allvendordeals($params = array()) {


        $fields = array('*');

        if (!empty($params['fields']['main'])) {
            $fields = $params['fields']['main'];
        }

        $select = $this->getAdapter()->select()
                ->from(array('mda' => 'mybb_vendor_deals'), $fields)
                ->joinInner(array('mv' => 'mybb_vendors'), 'mda.vendorid = mv.id', array('mv.name as businessname'))
                ->joinInner(array('mcm' => 'mybb_city_master'), 'mcm.id = mv.city', array('mcm.name as city_name'))
                ->joinInner(array('mc' => 'mybb_category'), 'mv.category = mc.id', array('mc.name as catname'));


        $selectCount = $this->getAdapter()->select()
                ->from(array('mda' => 'mybb_vendor_deals'), array(new Zend_Db_Expr("count(mda.id)  AS count")))
                ->joinInner(array('mv' => 'mybb_vendors'), 'mda.vendorid = mv.id', null)
                ->joinInner(array('mcm' => 'mybb_city_master'), 'mcm.id = mv.city', null)
                ->joinInner(array('mc' => 'mybb_category'), 'mv.category = mc.id', null);



        $where = "1 ";

        $genCond = "";

        if (!empty($params['condition']['id'])) {
            $where .= " AND mda.id = " . $params['condition']['id'] . "";
        }



        if (!empty($params['condition']['businessname'])) {
            $where .= " AND mv.name LIKE '%" . $params['condition']['businessname'] . "%'";
        }

        if (!empty($params['condition']['city'])) {
            $where .= " AND mv.city = '" . $params['condition']['city'] . "'";
        }
        
        if (isset($params['condition']['status'])) {
            $where .= " AND mda.status = " . $params['condition']['status'];
        }
        
        if (isset($params['condition']['current_date'])) {
            $where .= " AND (mda.end_date <> '0000-00-00' AND mda.end_date <= '" . $params['condition']['current_date']."')";
        }
        
        $where .= $genCond;

        if (!empty($where)) {
            $selectCount->where($where);
            $select->where($where);
        }

        //echo $selectCount; die;
        $resultCount = $this->getAdapter()->fetchCol($selectCount);


        $params['count'] = $resultCount[0];

        if (!$params['count']) {
            return $this->blankResult();
        }
        $this->getPagingSorting($params);

        $select->order($params['sidx'] . " " . $params['sord']);

        $select->limit($params['limit'], $params['start']);
        //echo $select; die; 
        $result = $this->getAdapter()->fetchAll($select);
        //dd($params);
        return array(
            'result' => $result,
            'total' => $params['totalPages'],
            'records' => $params['count'],
            'page' => $params['page'],
            'resultCount' => count($result)
        );
    }
    
    
    public function contactExpertData($params = array()) {


        $fields = array('*');

        if (!empty($params['fields']['main'])) {
            $fields = $params['fields']['main'];
        }

        $select = $this->getAdapter()->select()
                ->from(array('mcae' => 'mybb_contact_an_expert'), $fields)
                ->joinInner(array('mcm' => 'mybb_city_master'), 'mcm.id = mcae.city_id', array('mcm.name as city_name'));
                

        $selectCount = $this->getAdapter()->select()
                ->from(array('mcae' => 'mybb_contact_an_expert'), array(new Zend_Db_Expr("count(mcae.id)  AS count")))
                ->joinInner(array('mcm' => 'mybb_city_master'), 'mcm.id = mcae.city_id', null);
                


        $where = "1 ";

        $genCond = "";

        if (!empty($params['condition']['id'])) {
            $where .= " AND mcae.id = '" . $params['condition']['id'] . "";
        }
        if (!empty($params['condition']['name'])) {
            $where .= " AND mcae.name LIKE '%" . $params['condition']['name'] . "%'"; 
        }

         if (!empty($params['condition']['phone'])) {
            $where .= " AND mcae.phone LIKE '%" . $params['condition']['phone'] . "%'"; 
            
        }

        if (!empty($params['condition']['event_name'])) {
            $where .= " AND mcae.event_name LIKE '%" . $params['condition']['event_name'] . "%'";
           // $where .= " AND name LIKE '%" . $params['condition']['name'] . "%'";
        }
        if (isset($params['condition']['date'])) {
            $where .= " AND mcae.created_at >= DATE(DATE_SUB(NOW(), INTERVAL 1 DAY))";
        }
        if (isset($params['condition']['status'])) {
            $where .= " AND mcae.status = " . $params['condition']['status'];
        }
        
        if (isset($params['condition']['city'])) {
            $where .= " AND mcae.city_id = " . $params['condition']['city'];
        }
        
        $where .= $genCond;

        if (!empty($where)) {
            $selectCount->where($where);
            $select->where($where);
        }
       
        
        //echo $select; die;
        $resultCount = $this->getAdapter()->fetchCol($selectCount);


//        $params['count'] = $resultCount[0];
//
//        if (!$params['count']) {
//            return $this->blankResult();
//        }
        
        
        if (isset($resultCount[0])) {
            $params['count'] = count($resultCount);
        } else {
            $params['count'] = 0;
        }
        
        if (!$params['count']) {
            return $this->blankResult();
        }
        
        
        
        $this->getPagingSorting($params);

        $select->order($params['sidx'] . " " . $params['sord']);

        $select->limit($params['limit'], $params['start']);
        //echo $select; die; 
        $result = $this->getAdapter()->fetchAll($select);
        //dd($params);
        return array(
            'result' => $result,
            'total' => $params['totalPages'],
            'records' => $params['count'],
            'page' => $params['page'],
            'resultCount' => count($result)
        );
        
        
    }
    
    
    
    /**
     * This function is used to status remarks to  expire deal.
     *
     * Created By : Bidhan
     * Date : 25 May,2016	
     * @return void.
     */
    public function updateVendorDealsStatus($id, $remarks, $status) {
        $db = $this->getAdapter();
        if ($remarks != '') {

            $updatestatus = array('status' => $status, 'remarks' => $remarks);
            //$db->update('mybb_vendor_deals', $updatestatus, 'id = ' . $id);
            return $db->update('mybb_vendor_deals', $updatestatus, 'id = ' . $id);
        }
    }
    
    /**
     * This function is used to update status and remarks of send enquiry.
     *
     * Created By : Bidhan
     * Date : 30 Jun, 2016	
     * @return void.
     */
    
    public function updateSEStatusRecords($toBeUpdated = array()) {
        try {
            $db = $this->getAdapter();
            if ($toBeUpdated['remarks'] != '') {
                $id = $toBeUpdated['id'];
                unset($toBeUpdated['id']);                        
                return $db->update($this->_quickquote, $toBeUpdated,'id = '.$id);
            }
        } catch (Exception $ex) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }
        
    }
  
    public function updateInstabookingStatusRecords($toBeUpdated = array()) {
        try {
            $db = $this->getAdapter();
            if ($toBeUpdated['remarks'] != '') {
                $id = $toBeUpdated['id'];
                unset($toBeUpdated['id']);                        
                return $db->update($this->_instabooking_answer, $toBeUpdated,'id = '.$id);
            }
        } catch (Exception $ex) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }
        
    }
    
    public function updateInstaEnquiryStatusRecords($toBeUpdated = array()) {
        try {
            $db = $this->getAdapter();
            if ($toBeUpdated['remarks'] != '') {
                $id = $toBeUpdated['id'];
                unset($toBeUpdated['id']);                        
                return $db->update($this->_smsemailme, $toBeUpdated,'id = '.$id);
            }
        } catch (Exception $ex) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }
        
    }
    
     public function updateContactExpertStatusRecords($toBeUpdated = array()) {
        try {
            $db = $this->getAdapter();
            if ($toBeUpdated['remarks'] != '') {
                $id = $toBeUpdated['id'];
                unset($toBeUpdated['id']);                        
                return $db->update($this->_contact_expert, $toBeUpdated,'id = '.$id);
            }
        } catch (Exception $ex) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }
        
    }

}

?>