<?php

//All the function and query related to the database going to perform here......
class Application_Model_DbTable_Index extends Mylib_Model_BaseModel {

    protected $_name = 'vy_review_master';
    protected $_useronline = 'vy_user_online';

    public function checkBussClaimedOrNot($userid) {
        $query = $this->getAdapter()->select()
                ->from(array('claim' => 'vy_claimbusiness'), array('claim.userid'))
                ->joinInner(array('v' => 'vy_vendors'), 'v.claimed_by = claim.id', array('v.id as vendorid'))
                ->Where("claim.userid = " . $userid);
        $select = $this->getAdapter()->fetchAll($query);
        return $select;
    }

    public function latestReviews() {
        $qq = $this->getAdapter()->select()
                ->from(array('rr' => 'vy_review_master'), array('rr.vendor_id', 'avg(rr.rating) as rating'))
                ->group('rr.vendor_id');

        $query = $this->getAdapter()->select()
                ->from(array('v' => 'vy_review_master'), array('v.*'))
                ->joinLeft(array('ven' => 'vy_vendors'), 'v.vendor_id = ven.id', array('ven.name as name', 'ven.vendor_url', 'ven.average_rating as average_rating', 'ven.total_published_reviews as total_published_reviews'))
                //->joinLeft(array('ven' => 'vy_vendors'), 'v.vendor_id = ven.id', array('ven.name as name', 'ven.vendor_url'))
                //->joinleft(array('vvv' => $qq), 'vvv.vendor_id = v.vendor_id', null)
                ->joinLeft(array('u' => 'vy_users'), 'v.user_id = u.id', array('u.name as username', 'u.photo as userthumb'))
                ->joinLeft(array('c' => 'vy_category'), 'ven.category = c.id', array('c.name as cat_name'))
                ->joinLeft(array('l' => 'vy_location'), 'ven.location = l.id', array('l.name as location_name'))
                ->joinLeft(array('city' => 'vy_city_master'), 'city.id = v.city', array('city.name as city_name'))
                ->where("v.status = '1'")
                ->where("ven.status = '1'")
                ->where("v.featured = '1'")
                ->where('v.city=?', $this->cityId)
                //->where("v.position != 0")
                //->order('v.position ASC')
                ->order('v.approved_at DESC')
                ->limit(3, 0);
        //echo $query; die;
        $select = $this->getAdapter()->fetchAll($query);
        return $select;
    }

    public function indexpageWhathappening() {
        $date = new Zend_Date();
        $todaydate = $date->get('YYYY-MM-dd');
        $query = $this->getAdapter()->select()
                ->from(array('w' => 'vy_events'), array('w.*'))
                ->joinLeft(array('l' => 'vy_location'), 'w.location = l.id', array('l.name as location_name'))
                ->Where('w.city=?', $this->cityId)
                ->where("w.status='1'")
                //->where("w.position != 0")
                ->where("w.event_end_date >= CURRENT_DATE()")
                ->order("w.event_date ASC");

        $select = $this->getAdapter()->fetchAll($query);
        return $select;
    }

    public function user_online() {
        date_default_timezone_set('Asia/Calcutta');
        $date = date('Y-m-d H:i:s', time());
        $dbdata = array('datetime' => $date);
        $insert = $this->getAdapter()->insert($this->_useronline, $dbdata);
        return $insert;
    }

    public function checkWeddingDate() {
        $userId = $this->userSession->id;
        $query = $this->getAdapter()->select()
                ->from('vy_users', '*')
                ->Where('id=?', $userId);
        $select = $this->getAdapter()->fetchAll($query);
        return $select;
    }

    public function updateWeddingDate($date, $userId) {
        $update_items = array('wedding_date' => $date);
        $this->getAdapter()->update('vy_users', $update_items, 'id = ' . $userId);
    }

    public function allCity() {
        $query = $this->getAdapter()->select()
                ->from(array('c' => 'vy_city_master'), array('c.*'))
                ->where('c.id !=' . $this->cityId . '');
        $select = $this->getAdapter()->fetchAll($query);
        //print_r($select); exit; 
        return $select;
    }

    public function mostPopularArticles() {
        $query = $this->getAdapter()->select()
                ->from('wp_posts', '*')
                ->Where('post_type=?', 'post')
                ->Where('post_status=?', 'publish')
                ->order("post_date  DESC")
                ->limit(3, 3);
        $select = $this->getAdapter()->fetchAll($query);
        return $select;
    }

    public function inspiringStories() {
        $query = $this->getAdapter()->select()
                ->from(array('w' => 'wp_posts'), array('w.*'))
                ->joinLeft(array('l' => 'wp_postmeta'), 'w.ID = l.meta_id', array('l.*'))
                ->Where('w.post_type=?', 'post')
                ->Where('w.post_status=?', 'publish')
                //->Where('l.meta_key=?','_thumbnail_id')
                //->where("w.ID == l.post_id")
                ->order("post_date  DESC")
                ->limit(3);
        // echo "<pre>";
        $select = $this->getAdapter()->fetchAll($query);
        return $select;
    }

    public function indexResourceTitle() {
        $query = $this->getAdapter()->select()
                ->from('wp_posts', '*')
                ->Where('post_type=?', 'post')
                ->order("comment_count  DESC")
                ->limit(2);
        $select = $this->getAdapter()->fetchAll($query);
        return $select;
    }

    /*     * ***********************Added by bidhan for generating XML*********************** */

    public function xml() {
        $query = $this->getAdapter()->select()
                ->from(array('v' => 'vy_vendors'), array('id', 'category', 'vendor_url'))
                ->joinInner(array('c' => 'vy_category'), 'v.category = c.id', array('name'))
                ->where('status = 1')
                ->order('id ASC');
        // ->limit(2);
        $all_active_vendor = $this->getAdapter()->fetchAll($query);
        return $all_active_vendor;
        //print_r(count($select)); die;
    }
    
    public function homePageWhathappening() {
        $date = new Zend_Date();
        $todaydate = $date->get('YYYY-MM-dd');
        $query = $this->getAdapter()->select()
                ->from(array('w' => 'vy_events'), array('w.*'))
                ->joinLeft(array('l' => 'vy_location'), 'w.location = l.id', array('l.name as location_name'))
                ->Where('w.city=?', $this->cityId)
                ->where("w.status='1'")
                //->where("w.position != 0")
                ->where("w.event_end_date >= CURRENT_DATE()")
                ->order("w.event_date ASC")
                ->limit(3, 0);

        $select = $this->getAdapter()->fetchAll($query);
        return $select;
    }

}

?>