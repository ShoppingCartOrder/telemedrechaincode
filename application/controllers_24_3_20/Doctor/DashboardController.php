<?php

class Doctor_DashboardController extends Mylib_Controller_DoctorbaseController {

    //protected $_dashboardResource;

    public function init() {
        parent::init();


    }

    /**
     * This function is used to show all function in dashboard 
     * Created By :  Umesh
     * Date : 20 May,2014
     * @param void
     * @return void
     */
    public function indexAction() {
        
        $request = $this->getRequest();
        global $preDate;
        global $nowDate;
        // echo $preDate; die;
        if ($request->getParam('s') == 1) {
            $this->view->savemsg = 1;
        }
        $session = new Zend_Session_Namespace('mydoctor');
        
        $this->view->lastlogin = $session->lastlogin;
        $this->view->loginIp = $session->loginip;
     
    }

    public function viewprofileAction() {

        $request = $this->getRequest();

        if ($request->getParam('s') == 1) {
            $this->view->savemsg = 1;
        }
        require_once 'My/Acl.php';
        $session = new Zend_Session_Namespace('my');
        $this->view->userAccountInfo = $this->_dashboardResource->fetchAdminUserInfo($session->loginId);
        $roleResourceObj = new Application_Model_DbTable_Role();
        $this->view->roleData = $roleResourceObj->fetchRoleData($this->view->userAccountInfo['0']['role']);
    }

    public function dashboarddetailAction() {
        $request = $this->getRequest();
        $recognize = $request->getParam('recognize');
        if ($recognize == 'login') {
            $getDetail = $this->_dashboardResource->getLoggedInDetail();
        }

        if ($recognize == 'register') {
            $getDetail = $this->_dashboardResource->getRegisterDetail();
        }

        if ($recognize == 'enquiry') {
            $getDetail = $this->_dashboardResource->getEnquiryDetail();
        }

        $this->view->recognize = $recognize;
        $this->view->detail = $getDetail;
    }

    public function alertAction() {
        global $status_type;
        global $no_record;
        global $addedBusiness;
        $request = $this->getRequest();
        $this->view->alert_type = $alert_type = $request->getParam('type');

        $updateType = $request->getParam('u');
        if (isset($updateType))
            $this->view->update = $updateType;

        if ($alert_type == '1') {
            $this->view->claim_business = $this->_dashboardResource->getClaimRecord();
        }
        if ($alert_type == '2') {
            $this->view->ad_detail = $this->_dashboardResource->getAdRecord();
        }
        if ($alert_type == '3') {
            $this->view->recent_events = $this->_dashboardResource->getEventRecord();
        }
        if ($alert_type == '4') {
            if ($request->getParam('d')) {
                $this->view->removemsg = 'yes';
            }
            $this->view->advertises = $this->_dashboardResource->getAdvertiseRecord();
        }
        if ($alert_type == '5') {
            $request = $this->getRequest();
            $this->view->wrong_records = $allrecord = $this->_dashboardResource->fetchWrongRecord();
            $pageno = $request->getParam('page');
            if (empty($pageno)) {
                $pageno = 1;
                $this->view->page = $pageno;
            }
            $paginator = Zend_Paginator::factory($allrecord);
            $paginator->setCurrentPageNumber($pageno);
            $paginator->setItemCountPerPage(NO_OF_RECORDS_PER_PAGE);
            $this->view->wrongRecord = $paginator;
        }
        if ($alert_type == '6') {
            $this->view->missing_records = $missingrecords = $this->_dashboardResource->fetchMissingRecord();
            $pageno = $request->getParam('page');
            if (empty($pageno)) {
                $pageno = 1;
                $this->view->page = $pageno;
            }
            $paginator = Zend_Paginator::factory($missingrecords);
            $paginator->setCurrentPageNumber($pageno);
            $paginator->setItemCountPerPage(NO_OF_RECORDS_PER_PAGE);
            $this->view->missingRecords = $paginator;
        }
        if ($alert_type == '7') {
            $this->view->vendor_records = $this->_dashboardResource->fetchVendorCreatedRecord();
            $this->view->addedBusiness = $addedBusiness;
        }
        if ($alert_type == '8') {
            $this->view->wrongcat_records = $notfindrecord = $this->_dashboardResource->fetchWrongCatRecord();
            $pageno = $request->getParam('page');
            if (empty($pageno)) {
                $pageno = 1;
                $this->view->page = $pageno;
            }
            $paginator = Zend_Paginator::factory($notfindrecord);
            $paginator->setCurrentPageNumber($pageno);
            $paginator->setItemCountPerPage(NO_OF_RECORDS_PER_PAGE);
            $this->view->notFindRecord = $paginator;
            $this->view->no_record = $no_record;
        }
        if ($alert_type == '9') {
            $this->view->something_mind = $this->_dashboardResource->fetchSomethingMindRecord();
        }
        $this->view->status_type = $status_type;
    }

    public function dailyupdateAction() {

        $request = $this->getRequest();
        $this->view->alert_type = $alert_type = $request->getParam('type');

        if ($alert_type == '1') {
            $this->view->newsletter_subscribed = $this->_dashboardResource->getNewsLettter();
        }
        if ($alert_type == '2') {
            $this->view->srch_cat = $this->_dashboardResource->fetchSearchCategory();
        }

        if ($request->getParam('cat') != '') {
            $cat = $request->getParam('cat');
            $this->view->alert_type = '3';
            $this->view->srch_cat_vendor = $this->_dashboardResource->fetchSearchCategoryVendor($cat);
        }

        if ($alert_type == '4') {
            $this->view->weddingweb = $this->_dashboardResource->fetchWeddingWebsite();
        }

        if ($alert_type == '5') {
            $this->view->loggedin_users = $this->_dashboardResource->fetchLoggedinUsers();
        }




        if ($alert_type == '6') {
            $this->view->Registered_users = $this->_dashboardResource->fetchregisterusers();
        }

        if ($alert_type == '7') {
            $this->view->Einvites_users = $this->_dashboardResource->fetcheinvitesusers();
        }

        if ($alert_type == '8') {
            $this->view->Smsemail_users = $this->_dashboardResource->fetchSmsEmailusers();
        }

        if ($alert_type == '9') {
            $this->view->Availability_record = $this->_dashboardResource->availabilityrecord();
        }

        if ($alert_type == '10') {
            $this->view->QuickQuote_record = $quoutescats = $this->_dashboardResource->quickquoterecord();
            /* foreach($quoutescats as $key=>$val){
              $categories[$key] = $this->_dashboardResource->categorydata($val['category']);
              } */
        }

        if ($alert_type == '11') {
            $this->view->weddingplan = $this->_dashboardResource->fetchWeddingPlanningRecord();
        }
    }

    public function deleteadvertiseAction() {

        $request = $this->getRequest();
        $adid = $request->getParam('delid');

        if ($adid != '') {

            $this->_dashboardResource->updateAdvertiseRecord($adid);
            $this->_redirect('/admin/dashboard/alert/type/4/d/1');
        }
    }

    public function deletewrongcatAction() {

        $request = $this->getRequest();
        $id = $request->getParam('id');

        if ($id != '') {

            $this->_dashboardResource->deletewrongcatRecord($id);
            $this->_redirect('/admin/dashboard/alert/type/8/d/1');
        }
    }

    public function alertactionAction() {

        $id = $this->getRequest()->getParam('bid');
        $business_type = $this->getRequest()->getParam('business_type');
        if (isset($business_type)) {
            $this->view->business_type = $business_type = $this->getRequest()->getParam('business_type');
            $this->view->businessTypeUrl = 'business_type/' . $business_type;
        } else {
            $this->view->businessEdited = $businessEdited = $this->getRequest()->getParam('businessEdited');
            $this->view->businessTypeUrl = 'businessEdited/' . $businessEdited;
        }
        $this->view->return_type = $this->getRequest()->getParam('returnType');
        if (!empty($id)) {
            global $vendorsFieldArr;
            global $paymentMode;
            $this->view->allcity = $this->_claimbusinessModal->allcity();
            $updateVendorDetails = $claim_business_details = $this->_claimbusinessModal->claimbusinessdetail($id);
            $VendorDetails = $this->_claimbusinessModal->businessdetail($claim_business_details[0]['vendor_id']);
            $this->view->updateVendorDetails = $updateVendorDetails[0];
            $this->view->VendorDetails = $VendorDetails[0];
            $this->view->showFields = $vendorsFieldArr;
            $claimedVendorDeals = $this->_claimbusinessModal->getClaimedVendorDeals($id);
            /*******************************Extra field *******************************/
            $claimedVendorExtras = $this->_claimbusinessModal->getClaimedExtraFieldValues($id);
            $arrClaimedVendorExtras = array();
            foreach($claimedVendorExtras as $key=>$claimedVendorExtra){
                $arrClaimedVendorExtras[$claimedVendorExtra['field_id']] = $claimedVendorExtra;
            }           
            $this->view->claimedVendorExtra = $arrClaimedVendorExtras;
            
            
            $VendorExtras = $this->_claimbusinessModal->getExtraFieldValues($claim_business_details[0]['vendor_id']);
            $arrVendorExtras = array();            
            foreach($VendorExtras as $key=>$VendorExtra){
                $arrVendorExtras[$VendorExtra['field_id']] = $VendorExtra;
            }
            $this->view->VendorExtra = $arrVendorExtras;
            
            
            
            $allExtraFlds = $this->_categoryResource->categoryExtraFields($VendorDetails[0]['category']);            
            $arrAllExtraFlds = array();            
            foreach($allExtraFlds as $key=>$allExtraFld){
                $arrAllExtraFlds[$allExtraFld['id']] = $allExtraFld;
            }
            $this->view->allExtraFlds = $arrAllExtraFlds;
            
            /***************************[END]Extra field*****************************/
            
            
            $VendorDeals = $this->_claimbusinessModal->getVendorDeals($claim_business_details[0]['vendor_id']);            
            $this->view->getService = $this->_claimbusinessModal->getCategoryServiceTags($claim_business_details[0]['category']);
            $this->view->claimedVendorDeals = $claimedVendorDeals;
            $this->view->VendorDeals = $VendorDeals;
            $this->view->claimedVendorDeals = $claimedVendorDeals;
            
            
            $this->view->id = $id;
            $this->view->paymentMode = $paymentMode;
        }

        $this->view->categories = $this->_vendorResource->fetchCategories();
        $this->view->location = $this->_vendorResource->fetchLocation();
    }

    public function approvebusinessAction() {
        $this->_helper->layout->disableLayout('innerlayout');
        $request = $this->getRequest();
        if ($request->getPost()) {
            global $nowDateTime;
            global $vendorsFieldArr;
            $params = $this->__inputPostData;
            $extraArray = preg_grep('/^extra~/', array_keys($params));
            $response = array('status' => 0, 'msg' => '');
            $inputData = new Zend_Filter_Input($this->filters, $this->validators);
            $inputData->setData($params);
            if ($inputData->isValid()) {

                $VendorDetails = $this->_claimbusinessModal->businessdetail($params['business_origid']);
                $newData = array();
                foreach ($vendorsFieldArr as $key => $val) {
                    //if(isset($VendorDetails[0][$key])){
                    if ($VendorDetails[0][$key] == 'NA')
                        $VendorDetails[0][$key] = '';
                    if ($params[$key] == 'NA')
                        $params[$key] = '';
                    if ($key == 'lname') {
                        if (trim($params['location']) != trim($VendorDetails[0][$key])) {
                            $newData['location_approved'] = $params[$key];
                            $newData['location_status'] = 2;
                        }
                    } else if (trim($params[$key]) != trim($VendorDetails[0][$key])) {
                        $newData[$key . '_approved'] = $params[$key];
                        $newData[$key . '_status'] = 2;
                    }
                    //}
                }
            
                if (isset($params['deal_count'])) {
                    for ($dk = 1; $dk <= $params['deal_count']; $dk++) {
                        $dealData['vendorid'] = $params['business_copyid'];
                        $dealData['deals_approved'] = $params['deals_' . $dk];
                        $dealData['status'] = 1;
                        $dealData['updated_at'] = $nowDateTime;
                        if (empty($params['dealid_' . $dk])) {
                            if(!empty($params['deals_' . $dk])){
                                $dealData['deals'] = $params['deals_' . $dk];
                                unset($dealData['id']);
                                $this->_claimbusinessModal->insertDeal($dealData);
                            }
                            
                        } else {
                            $dealData['id'] = $params['dealid_' . $dk];
                            $this->_claimbusinessModal->updateDeal($dealData);
                        }
                    }
                }

                if (count($extraArray) > 0) {
                    foreach ($extraArray as $extraVal) {
                        $origExtraVal = str_replace('extra~', '', $extraVal);
                        $extraData['field_id'] = $origExtraVal;
                        $extraData['values_approved'] = rtrim($params[$extraVal], ',');
                        $extraData['status'] = 1;
                        $extraData['vendor_id'] = $params['business_copyid'];
                        $this->_claimbusinessModal->insertExtraFields($extraData);
                    }
                }
                if (!empty($newData)) {
                    $newData['id'] = $params['business_copyid'];
                    $this->_claimbusinessModal->updateApprovedFields($newData);
                }
                $this->UpdateVendorURL('1', $params['business_origid'], $params['name'], $params['location']);
                //$this->_redirect(ADMIN_BASE_URL . 'dashboard/vendorlist/' . $params['business_type'] . '/returnType/' . $params['return_type'] . '/u');
                $this->_redirect(ADMIN_BASE_URL . 'dashboard/admin-check-image-edit/bid/'.$params['business_copyid'].'/'.$params['business_type'] . '/returnType/' . $params['return_type']);
            }
        }
    }

    /*     * *******Vendor URL Update******* */

    public function UpdateVendorURL($exist = '', $id = '', $vendor = '', $location = '') {
        if (isset($id)) {
            if (isset($vendor)) {
                $vendor = $this->removeSpaceSpecial($vendor);
            }
            if (isset($location)) {
                $location = $this->removeSpaceSpecial($location);
            }
            $vendor_url['0'] = $vendor . '-' . $location;
            $vendor_url['0'] = $this->removeRepeatChar($vendor_url['0']);

            $vendor_url['1'] = $vendor . '-' . $location . '-' . $id;
            $vendor_url['1'] = $this->removeRepeatChar($vendor_url['1']);

            $checkAvailVendorURL = $this->_vendorResource->checkVendorURL($vendor_url['0'], $id);
            if ($checkAvailVendorURL == 1) {
                $avail_url = $vendor_url['0'];
            } else {
                $avail_url = $vendor_url['1'];
            }
            $vendor_url_old = $this->_vendorResource->existVendorURL($id);
            if ($exist == 1) {
                $modifyurl = $this->_vendorResource->modifyVendorURL($avail_url, $id, $vendor_url_old, $exist);
            } else {
                $addurl = $this->_vendorResource->modifyVendorURL($avail_url, $id, $vendor_url_old);
            }
        }
    }

    public function removeSpaceSpecial($vendorUrlValue) {
        $vendorUrlValue = preg_replace('!\s+!', '-', $vendorUrlValue);
        $vendorUrlValue = preg_replace('/[^a-zA-Z0-9_ -]/s', '', $vendorUrlValue);
        return str_replace(" ", "-", $vendorUrlValue);
    }

    public function removeRepeatChar($vendorUrl) {
        $vendorUrl = preg_replace('/_+/', '_', preg_replace('/-+/', '-', $vendorUrl));
        return $vendorUrl;
    }

    public function categorywisefieldsAction() {
        $this->_helper->layout->disableLayout('innerlayout');
        $request = $this->getRequest();
        $this->view->cat = $category = $request->getParam('category');
        $this->view->duplicate_vendorid = $request->getParam('vendorid');
        $this->view->vendorid = $request->getParam('businessid');

        $businessid = $this->view->vendorid;
        if ($businessid) {
            $business_details = $this->_claimbusinessModal->Duplicatebusinessdetail($businessid);
            $this->view->allcategory = $this->_claimbusinessModal->allcategory();
            $this->view->getTags = $this->_claimbusinessModal->getTags($business_details[0]['category']);
            $this->view->getService = $this->_claimbusinessModal->getService($business_details[0]['category']);
            $this->view->allcity = $this->_claimbusinessModal->allcity();
            $this->view->business_details = $business_details;
            $this->view->businessid = $businessid;
            $this->view->businessname = $business_details[0]['name'];
            $this->view->category = $business_details[0]['categoryname'];
            $this->view->categoryid = $business_details[0]['category'];
            $claimedStatus = $this->_vendorResource->fetchVendorClaimedStatus($businessid);
        } else {

            $this->view->allcategory = $this->_claimbusinessModal->allcategory();
            $categorname = $this->_claimbusinessModal->getcategorname($category);
            $this->view->getTags = $this->_claimbusinessModal->getTags($category);
            $this->view->getService = $this->_claimbusinessModal->getService($category);
            $this->view->allcity = $this->_claimbusinessModal->allcity();
            $this->view->category = $categorname[0]['name'];
            $this->view->categoryid = $categorname[0]['id'];
        }

        /*         * ********For filling icon suggest 9/27/2013******** */

        if ($category == '11') {
            $this->view->iconvals = 'Diet And Wellness Programs|Personalized Fitness Plans|Corporate Programs';
        }

        if ($category == '12') {
            $this->view->iconvals = 'Happy Hours|Special Nights|Alcohol Served|Dj And Music|Stag Entry|Private Party Arrangemnt';
        }

        if ($category == '14') {
            $this->view->iconvals = 'Wedding Planning|International Events|Destination Wedding|Honeymoon Planning|PR And Media Coverage|Celebrity Invites';
        }

        if ($category == '15') {
            $this->view->iconvals = 'Wedding Fireworks|Firework Professional Provided';
        }

        if ($category == '16') {
            $this->view->iconvals = 'Artificial Flowers And Vases|Cakes And Chocolates|Fancy Gift Box Envelops|Delivery Outside India|Pan India Delivery|Wedding Decoration';
        }

        if ($category == '17') {
            $this->view->iconvals = 'Aerobics|Changing Room|Locker Facility|Personal Trainer For Home|Shower Facility|Spa Facility';
        }

        if ($category == '18') {
            $this->view->iconvals = 'Sherwani|Readymade Suits|Suit Piece|Men Suit Stitching';
        }

        if ($category == '20') {
            $this->view->iconvals = 'Different Language Option|Fancy Gift Box And Envelops|Handmade Cards And Scrolls';
        }

        if ($category == '21') {
            $this->view->iconvals = '';
        }

        if ($category == '23') {
            $this->view->iconvals = '';
        }

        if ($category == '26') {
            $this->view->iconvals = 'Couple Courses Offered|Personality Development Classes|Grooming Courses|English Speaking Classes|Hobby Classes';
        }

        if ($category == '28') {
            $this->view->iconvals = 'Vaastu Service|House Visit|Ceremony N Wedding Service|Hawan And Pooja|Horoscope And Match Making';
        }

        if ($category == '30') {
            $this->view->iconvals = 'Outstation Travel|24x7|Driver Service Provided|Luxury Cars|Packaged Tours';
        }

        if ($category == '31') {
            $this->view->iconvals = 'Audio Visual Aids|Catering Service|Revolving Stage|Designer Tents|Waterproof Pandals|Lighting And Decoration';
        }

        if ($category == '32') {
            $this->view->iconvals = 'Abroad Tours|Honeymoon Packages|Visa And Passport Services|Air Ticket Booking|Domestic Tours|Hotel Booking';
        }

        if ($category == '33') {
            $this->view->iconvals = 'Sehra And Pagdi|Groom Kirpan|Clutches And Handbags|Chooda And Kaleere|Bangles And Bindis|Artificial Jewellery';
        }

        if ($category == '34') {
            $this->view->iconvals = 'Crystalware Items|Items For Home Decor|Packaging Service|Personalised Gifts|Religious Range|Silverware Items';
        }

        if ($category == '35') {
            $this->view->iconvals = 'Saree Bridal|Heavy Suit Bridal|Lehnga Bridal|Blous Stitching For Brides';
        }

        /*         * *********End********* */
    }

    public function savevendorduplicateAction() {

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $request = $this->getRequest();

        if ($request->isPost()) {
            $data = $request->getPost();

            define("MAX_SIZE", "20000000");

            function getExtension($str) {
                $i = strrpos($str, ".");
                if (!$i) {
                    return "";
                }
                $l = strlen($str) - $i;
                $ext = substr($str, $i + 1, $l);
                return $ext;
            }

            /*             * *****Check rest fields 9/25/2013***** */
            if (!array_key_exists('claim_album', $data)) {
                $data = array_merge($data, array('claim_album' => ''));
            }
            if (!array_key_exists('claim_community', $data)) {
                $data = array_merge($data, array('claim_community' => ''));
            }
            if (!array_key_exists('claim_cuisine', $data)) {
                $data = array_merge($data, array('claim_cuisine' => ''));
            }
            if (!array_key_exists('claim_dancestyle', $data)) {
                $data = array_merge($data, array('claim_dancestyle' => ''));
            }
            if (!array_key_exists('claim_ladiesgents', $data)) {
                $data = array_merge($data, array('claim_ladiesgents' => ''));
            }
            if (!array_key_exists('claim_institute', $data)) {
                $data = array_merge($data, array('claim_institute' => ''));
            }
            if (!array_key_exists('claim_rooms', $data)) {
                $data = array_merge($data, array('claim_rooms' => ''));
            }
            if (!array_key_exists('claim_cateredto', $data)) {
                $data = array_merge($data, array('claim_cateredto' => ''));
            }
            if (!array_key_exists('claim_nohalls', $data)) {
                $data = array_merge($data, array('claim_nohalls' => ''));
            }
            if (!array_key_exists('claim_performsin', $data)) {
                $data = array_merge($data, array('claim_performsin' => ''));
            }
            if (!array_key_exists('claim_specialitydish', $data)) {
                $data = array_merge($data, array('claim_specialitydish' => ''));
            }
            if (!array_key_exists('claim_therapy', $data)) {
                $data = array_merge($data, array('claim_therapy' => ''));
            }
            if (!array_key_exists('claim_dealsin', $data)) {
                $data = array_merge($data, array('claim_dealsin' => ''));
            }
            if (!array_key_exists('servers_guests_ratio', $data)) {
                $data = array_merge($data, array('servers_guests_ratio' => ''));
            }

            $data['search_services'] = '';

            /*             * *****For original data**** */
            $orexhiddenicons = explode("|", $data['hiddenicons']);
            $orexhiddenservices = explode(",", $data['hiddeservices']);
            /*             * *****End here ******* */

            $exhiddenicons = explode("|", str_replace(" ", "", strtolower($data['hiddenicons'])));
            $exhiddenservices = explode(",", str_replace(" ", "", strtolower($data['hiddeservices'])));
            for ($iconic = 0; $iconic < count($exhiddenicons); $iconic++) {

                if (in_array($exhiddenicons[$iconic], $exhiddenservices)) {
                    $data['search_services'] .= $orexhiddenicons[$iconic] . '_Available, ';
                } else {
                    $data['search_services'] .= $orexhiddenicons[$iconic] . '_Not Available, ';
                }
            }

            $data['search_services'] = substr($data['search_services'], 0, -2);

            $data['claim_location'] = $this->_locationResource->fetchLocationId($data['claim_location']);

            /*             * ****End**** */

            $errors = 0;
            $image = $_FILES['vendorimage']['name'];
            // if image is not empty
            if ($image) {
                $filename = stripslashes($_FILES['vendorimage']['name']);
                $extension = getExtension($filename);
                $extension = strtolower($extension);
                if (($extension != "jpg") && ($extension != "jpeg") && ($extension !=
                        "png") && ($extension != "gif") && ($extension != "JPG") &&
                        ($extension != "JPEG") && ($extension != "PNG") && ($extension !=
                        "GIF")) {
                    //echo '<h3>Unknown extension!</h3>';
                    $errors = 1;
                    // $this->_redirect('/advertisement/addbanner/$');
                } else {

                    $size = filesize($_FILES['vendorimage']['tmp_name']);

                    if ($size > MAX_SIZE * 1024) {
                        echo '<h4>You have exceeded the size limit!</h4>';
                        $errors = 1;
                    } else {

                        //$newname ="www.azurroware.com/mybb/images/vendorsImage/main/".$isVendorIdExist['vendorsImage'];
                        //unlink($newname);

                        $image_name = $data['id'] . '.' . $extension;
                        $newname = $_SERVER['DOCUMENT_ROOT'] . '/Admin/public/images/vendor/' . $image_name;
                        $copied = copy($_FILES['vendorimage']['tmp_name'], $newname);
                    }
                }
            } else {
                $image_name = $data['old_image'];
            }

            if ($data['status'] == '0') {
                $this->_dashboardResource->updateDuplicateVendorData($image_name, $data);
            } else if ($data['status'] == '2') {
                $this->_dashboardResource->removeDuplicateVendorData($data['id']);
            } else {
                $this->_dashboardResource->updateVendorData($image_name, $data);
            }
            $this->_redirect('/admin/dashboard/alert/type/1');
        }
    }

    /**
     * This function is used delete record in alert page
     * Created By :  Umesh
     * Date : 24 May,2014
     * @param void
     * @return void
     */
    public function deletewrongAction() {
        if ($id = $this->getRequest()->getParam('id')) {
            if ($this->_dashboardResource->deletewrong($id)) {
                $this->_redirect(ADMIN_BASE_URL . 'dashboard/alert/type/5');
            }
        }
    }

    /**
     * This function is used delete record in alert page
     * Created By :  Umesh
     * Date : 24 May,2014
     * @param void
     * @return void
     */
    public function missingbusinessAction() {
        if ($id = $this->getRequest()->getParam('id')) {
            if ($this->_dashboardResource->missingbusiness($id)) {
                $this->_redirect(ADMIN_BASE_URL . 'dashboard/alert/type/6');
            }
        }
    }

    /**
     * This function is used  update status
     * Created By :  Umesh
     * Date : 25 May,2014
     * @param void
     * @return void
     */
    public function setstatusAction() {
        $id = $this->getRequest()->getParam('id');
        $remarks = $this->getRequest()->getParam('remarks');
        $status = $this->getRequest()->getParam('status');
        if (($status != '0') && ($remarks != '') && ($id != '')) {
            $return = $this->_dashboardResource->updateStatus($id, $remarks, $status);
            echo '1';
            die;
        } else {
            echo '0';
            die;
        }

        // echo $return; die;
    }

    /**
     * This function is used  to add remark
     * Created By :  Umesh
     * Date : 25 May,2014
     * @param void
     * @return void
     */
    public function remarksupdateAction() {
        $value = $this->getRequest()->getParam('remarckvalue');
        $bid = $this->getRequest()->getParam('bid');
        $update = $this->_dashboardResource->upDateRemark($value, $bid);
        //echo '1'; die;
    }

    /**
     * This function is used  to set status in missing page
     * Created By :  Umesh
     * Date : 25 May,2014
     * @param void
     * @return void
     */
    public function missingstatusAction() {
        $id = $this->getRequest()->getParam('id');
        $remarks = $this->getRequest()->getParam('remarks');
        $status = $this->getRequest()->getParam('status');
        if (($status != '0') && ($remarks != '') && ($id != '')) {
            $return = $this->_dashboardResource->missingStatusUpdate($id, $remarks, $status);
            echo '1';
            die;
        } else {
            echo '0';
            die;
        }
    }

    /**
     * This function is used  to delete record	
     * Created By :  Umesh
     * Date : 25 May,2014
     * @param void
     * @return void
     */
    public function delanythingAction() {
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->getRequest()->getParam('anything_id');
        //echo $id; die;
        if (!empty($id)) {
            $this->_dashboardResource->deleteMultiAnything($id);
        }
        $this->_redirect("admin/dashboard/whatswrong");
    }

    /**
     * This function is used  to set status in vendor search
     * Created By :  Umesh
     * Date : 3 Jun,2014
     * @param void
     * @return void
     */
    public function norecordsfoundAction() {
        $id = $this->getRequest()->getParam('id');
        $remarks = $this->getRequest()->getParam('remarks');
        $status = $this->getRequest()->getParam('status');
        if (($status != '0') && ($remarks != '') && ($id != '')) {
            $return = $this->_dashboardResource->noRecordsFound($id, $remarks, $status);
            echo '1';
            die;
        } else {
            echo '0';
            die;
        }
    }

    /**
     * This function is used delete search recodes
     * Created By :  Umesh
     * Date : 3 june,2014
     * @param void
     * @return void
     */
    public function delsearchAction() {
        if ($ids = $this->getRequest()->getParam('notfound_id')) {
            //echo $id; die;
            $arrIds = explode(',', $ids);
            if ($del = $this->_dashboardResource->deleteNoRecordsFound($arrIds)) {
                $this->_helper->FlashMessenger()->setNamespace('success')->addMessage("Records successfully deleted.");
                $this->_redirect(ADMIN_BASE_URL . 'dashboard/noresult');
            }
        }
    }

    /**
     * This function is used delete search recodes
     * Created By :  Vaibhav
     * Date : 02 Dec,2014
     * @param void
     * @return void
     */
    public function deltagsearchAction() {
        if ($ids = $this->getRequest()->getParam('allfound_id')) {
            $arrIds = explode(',', $ids);
            if ($del = $this->_dashboardResource->deleteNoRecordsFound($arrIds)) {
                $this->_helper->FlashMessenger()->setNamespace('success')->addMessage("Records successfully deleted.");
                $this->_redirect(ADMIN_BASE_URL . 'dashboard/allresult');
            }
        }
    }

    /**
     * This function is used  to set status in vendor search
     * Created By :  Umesh
     * Date : 3 Jun,2014
     * @param void
     * @return void
     */
    public function updatestatusAction() {
        $id = $this->getRequest()->getParam('id');
        $remarks = $this->getRequest()->getParam('remarks');
        $status = $this->getRequest()->getParam('status');
        if (($status != '0') && ($remarks != '') && ($id != '')) {
            $return = $this->_dashboardResource->updateStatusSomething($id, $remarks, $status);
            echo '1';
            die;
        } else {
            echo '0';
            die;
        }
    }

    /**
     * This function is used  to delete record	
     * Created By :  Umesh
     * Date : 4 Jun,2014
     * @param void
     * @return void
     */
    public function delsomthingAction() {
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->getRequest()->getParam('something_id');
        if (!empty($id)) {
            $this->_dashboardResource->delSomethingMind($id);
        }
        $this->_redirect("admin/dashboard/something");
    }

    /**
     * This function is used  to delete record	
     * Created By :  Umesh
     * Date : 6 Jun,2014
     * @param void
     * @return void
     */
    public function deletedealAction() {
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->getRequest()->getParam('del_id');
        if (!empty($id)) {
            $this->_dashboardResource->deleteDealRecords($id);
        }
        $this->_redirect("admin/dashboard/deal");
    }

    /**
     * This function is used  to set status in vendor search
     * Created By :  Umesh
     * Date : 3 Jun,2014
     * @param void
     * @return void
     */
    public function dealsupdateAction() {
        $id = $this->getRequest()->getParam('id');
        $remarks = $this->getRequest()->getParam('remarks');
        $status = $this->getRequest()->getParam('status');
        if (($status != '0') && ($remarks != '') && ($id != '')) {
            $return = $this->_dashboardResource->updateDealsRecords($id, $remarks, $status);
            echo '1';
            die;
        } else {
            echo '0';
            die;
        }
    }

    public function updatevendorstatusAction() {
        $this->_helper->viewRenderer->setNoRender(true);
        $data = $this->getRequest()->getPost();
        if(empty($data['userEmail'])){
            echo "User does not exist.";
            exit;
        }
        $userEmail = $data['userEmail'];
        
        global $vendorsFieldArr;
        global $nowDateTime;

        if ($data['remarks'] != '') {
            $rparam['id'] = $data['id'];
            $rparam['remarks'] = $data['remarks'];
            $rparam['status'] = $data['status'];
            if (isset($data['businessType'])) {
                $rparam['businessType'] = $data['businessType'];
            } else {
                $rparam['businessEdited'] = $data['business_edited'];
            }
            
            
            $this->_claimbusinessModal->updateRemark($rparam);
            
            $newData = array();
            $claimedVendorDetails = $this->_claimbusinessModal->claimbusinessdetail($data['id']);
            if ($data['status'] == '1') {
                

                $newData['id'] = $data['vendor_id'];
                $newData['updated_at'] = $nowDateTime;
                $newData['updated_by'] = $this->session->loginId;
                $newData['claimed_at'] = $claimedVendorDetails[0]['claimed_at'];
                $newData['claimed_by'] = $claimedVendorDetails[0]['claimed_by'];
                $newData['claimed_mobile_number'] = $claimedVendorDetails[0]['claimed_mobile_number'];
                $newData['verification_code'] = $claimedVendorDetails[0]['verification_code'];
                foreach ($vendorsFieldArr as $key => $val) {
                    if ($key == 'lname')
                        $key = 'location';
                    if (isset($claimedVendorDetails[0][$key . '_approved']) && $claimedVendorDetails[0][$key . '_approved'] != 'NA' && $claimedVendorDetails[0][$key . '_status'] == 2) {
                        $newData[$key] = $claimedVendorDetails[0][$key . '_approved'];
                    }
                }
                $newData['status'] = '1';
                $this->_claimbusinessModal->approvedvendorsdata($newData);
                /********************Code to set new vendor URL***********************/
                if(isset($newData['name']) || isset($newData['location'])){
                    
                    if(isset($newData['location'])){
                       $lName = $this->_claimbusinessModal->getlocationname($newData['location']);
                       $lName = $lName[0]['name']; 
                    }else{
                        $locationname = $claimedVendorDetails[0]['lname'];
                    }
                    
                    if(isset($newData['name'])){
                       $vName = $newData['name'];                       
                    }else{
                        $vName = $claimedVendorDetails[0]['name'];
                    }
                                       
                    $this->UpdateVendorURL('1', $data['vendor_id'], $vName, $lName);
                }
                
                /********************[END]Code to set new vendor URL***********************/
                
                

                $claimedVendorDeals = $this->_claimbusinessModal->getClaimedVendorDeals($data['id']);
                
                if (count($claimedVendorDeals) > 0) {
                    $claimData = array();
                    $claimData['vendorid'] = $data['vendor_id'];
                    $claimData['status'] = '1';
                    $claimData['updated_at'] = $nowDateTime;
                    $claimData['updated_by'] = $this->session->loginId;
                    $claimData['count'] = 0;
                    foreach ($claimedVendorDeals as $ckey => $claimVal) {
                        if (!empty($claimVal['deals_approved'])){
                            $claimData[$ckey]['deals'] = $claimVal['deals_approved'];
                        }
                        $claimData[$ckey]['claimed_deal_id'] = $claimVal['id'];    
                        $claimData['count'] = $claimData['count'] + 1;
                    }
                    $this->_claimbusinessModal->approvedDealsdata($claimData);
                 
                    //$this->_claimbusinessModal->deleteClaimedVendorDeals($data['id']);
                }

                $claimedVendorExtra = $this->_claimbusinessModal->getClaimedExtraFieldValues($data['id']);
                if (count($claimedVendorExtra) > 0) {
                    $claimExtraData = array();
                    $claimExtraData['vendor_id'] = $data['vendor_id'];
                    $claimExtraData['status'] = '1';
                    foreach ($claimedVendorExtra as $ckey => $claimExtraVal) {
                        $claimExtraData['values'] = $claimExtraVal['values_approved'];
                        $claimExtraData['field_id'] = $claimExtraVal['field_id'];
                        $this->_claimbusinessModal->approvedExtradata($claimExtraData);
                    }
                    $this->_claimbusinessModal->deleteClaimedVendorExtraFields($data['vendor_id']);
                }
                $this->_dashboardResource->updatevendorstatus($data);
                
                /***********************Code to update portfolio images***************/
                $pwhere = "approved_status = 1";
                $allApprovedPortfolioImages = $this->_claimbusinessModal->claimBusinessPortfolioDetail($data['id'],$pwhere);
                
                if(!empty($allApprovedPortfolioImages)){
                    $arrIamges = array();
                    foreach($allApprovedPortfolioImages as $key=>$approvedPortfolioImage){
                        
                        $arrIamges['businessid'] = $approvedPortfolioImage['businessid'];
                        $arrIamges['image'] = $approvedPortfolioImage['image'];
                        
                        $this->_claimbusinessModal->insertApporvedVendorImages($arrIamges);
                        
                        
                    }
                    
                }
                
                
                
                /***********************[END]Code to update portfolio images***************/
                $vendorDetails = $this->_claimbusinessModal->businessdetail($claimedVendorDetails[0]['vendor_id']);
                
                if(!empty($vendorDetails[0])){
                    
                    if(isset($data['businessType']) && $data['businessType'] == 2){
                        
                        $catUrl = $this->createTextIndex(' ','-',$vendorDetails[0]['categoryname']);
                        $vendorDlpUrl = '<a href="'.WEBSITE_URL.$vendorDetails[0]['city_url'].'/'.$catUrl.'/'.$vendorDetails[0]['vendor_url'].'">'. $vendorDetails[0]['name'].'</a>';
                        
                        $catLPUrl = '<a href="'.WEBSITE_URL.$vendorDetails[0]['city_url'].'/'.$catUrl.'">'. ucwords($vendorDetails[0]['categoryname']).'</a>';
                        //$businessAdded = sprintf($this->frmMsg['BUSINESSADDED']);
                        //$searchBusinessAdded = array("{NAME}","{VENDORNAME}", "{SUPPORTMAIL}", "{CONTACTWEDDINGPLZ}", "{VENDORDLPURL}", "{WEDDINGPLZ_FB_LINK}", "{FB_IMAGE_LINK}", "{WEDDINGPLZ_TWITTER_LINK}", "{TWITTER_IMAGE_LINK}", "{WEDDINGPLZ_GP_LINK}", "{GP_IMAGE_LINK}");
                        //$replaceBusinessAdded = array($vendorDetails[0]['person_name'],$vendorDetails[0]['name'], SUPPORT_EMAIL, WEDDING_PLZ_CONTACT_NO, $vendorDlpUrl, WEDDINGPLZ_FB_LINK, FB_IMAGE_LINK, WEDDINGPLZ_TWITTER_LINK, TWITTER_IMAGE_LINK, WEDDINGPLZ_GP_LINK, GP_IMAGE_LINK);
                        
                        $subject = 'Welcome to Weddingplz.com: Your Business Is Now Listed';
                        
                        $businessAdded = sprintf($this->frmMsg['BUSINESSADDED_NEW']);
                                               
                        $searchBusinessAdded = array("{WP_LOGO_IMAGE_LINK}","{SUBJECT}","{CAT_NAME}","{VENDOR_NAME_LINK}","{VENDOR_LOGIN_EMAIL}", "{SITEURL}", "{WEDDINGPLZ_FB_LINK}", "{FB_IMAGE_LINK}", "{WEDDINGPLZ_TWITTER_LINK}", "{TWITTER_IMAGE_LINK}", "{WEDDINGPLZ_GP_LINK}", "{GP_IMAGE_LINK}","{WEDDININGPLZ_INSTAGRAM}","{INSTAGRAM_IMAGE_LINK}","{CURRENT_YEAR}","{WEBSITE_NAME}","{DATA_EMAIL}");
                        $replaceBusinessAdded = array(WP_LOGO_IMAGE_LINK,$subject,$catLPUrl,$vendorDlpUrl,$userEmail,SITEURL, WEDDINGPLZ_FB_LINK, FB_IMAGE_LINK, WEDDINGPLZ_TWITTER_LINK,TWITTER_IMAGE_LINK,WEDDINGPLZ_GP_LINK,GP_IMAGE_LINK,WEDDININGPLZ_INSTAGRAM,INSTAGRAM_IMAGE_LINK,CURRENT_YEAR,WEBSITE_NAME,DATA_EMAIL);
                        
                        $contentBusineddAdded = str_replace($searchBusinessAdded, $replaceBusinessAdded, $businessAdded);
                       // $mailToBusinessAdded = $params['claim_email'];
                        
                        
                        //$vEmail = $vendorDetails[0]['email'];                        
                        //$vEmail = 'parullwadhwa00@gmail.com';                                                     
                        //$this->sendMail($vEmail, $subject, $contentBusineddAdded, $api = true);
                         //$this->sendMail($userEmail, $subject, $contentBusineddAdded, $api = true);  
                        //$userEmail = 'bidhan@weddingplz.com';
                        
                        $this->sendLargeMail($userEmail, $subject, $contentBusineddAdded, $api = true);  
                        $this->sendLargeMail(BUSINESS_EDIT_EMAIL, $subject, $contentBusineddAdded, $api = true);
                    }else if(isset($data['businessType']) && $data['businessType'] ==1){
                        
                        $catUrl = $this->createTextIndex(' ','-',$vendorDetails[0]['categoryname']);
                        $vendorDlpUrl = '<a href="'.WEBSITE_URL.$vendorDetails[0]['city_url'].'/'.$catUrl.'/'.$vendorDetails[0]['vendor_url'].'">'. $vendorDetails[0]['name'].'</a>';
                        
                        /*$businessAdded = sprintf($this->frmMsg['BUSINESSCLAIMEDAPPROVED']);
                        $searchBusinessAdded = array("{NAME}","{VENDORNAME}", "{SUPPORTMAIL}", "{CONTACTWEDDINGPLZ}", "{VENDORDLPURL}", "{WEDDINGPLZ_FB_LINK}", "{FB_IMAGE_LINK}", "{WEDDINGPLZ_TWITTER_LINK}", "{TWITTER_IMAGE_LINK}", "{WEDDINGPLZ_GP_LINK}", "{GP_IMAGE_LINK}");
                        $replaceBusinessAdded = array($vendorDetails[0]['person_name'],$vendorDetails[0]['name'], SUPPORT_EMAIL, WEDDING_PLZ_CONTACT_NO, $vendorDlpUrl, WEDDINGPLZ_FB_LINK, FB_IMAGE_LINK, WEDDINGPLZ_TWITTER_LINK, TWITTER_IMAGE_LINK, WEDDINGPLZ_GP_LINK, GP_IMAGE_LINK);
                        */
                        $subjectHd = ucwords($vendorDetails[0]['name']). '- Business Claim Approved';
                        $subjectEmail = 'Business Claim Approved - '.ucwords($vendorDetails[0]['name']);
                        $businessAdded = sprintf($this->frmMsg['BUSINESSCLAIMEDAPPROVED_NEW']);
                        $searchBusinessAdded = array("{WP_LOGO_IMAGE_LINK}","{SUBJECT}","{NAME}","{VENDORNAME}","{VENDOR_LOGIN_EMAIL}", "{VENDORDLPURL}","{DATA_EMAIL}","{WEBSITENAME}","{SITEURL}","{WEDDING_PLZ_CONTACT_NO}", "{WEDDINGPLZ_FB_LINK}", "{FB_IMAGE_LINK}", "{WEDDINGPLZ_TWITTER_LINK}", "{TWITTER_IMAGE_LINK}", "{WEDDINGPLZ_GP_LINK}", "{GP_IMAGE_LINK}","{WEDDININGPLZ_INSTAGRAM}","{INSTAGRAM_IMAGE_LINK}","{CURRENT_YEAR}","{WEBSITE_NAME}","{SUPPORT_EMAIL}");
                        $replaceBusinessAdded = array(WP_LOGO_IMAGE_LINK,$subjectHd,ucwords($vendorDetails[0]['person_name']),ucwords($vendorDetails[0]['name']),$userEmail,$vendorDlpUrl,DATA_EMAIL,WEBSITENAME,SITEURL, WEDDING_PLZ_CONTACT_NO, WEDDINGPLZ_FB_LINK, FB_IMAGE_LINK, WEDDINGPLZ_TWITTER_LINK,TWITTER_IMAGE_LINK,WEDDINGPLZ_GP_LINK,GP_IMAGE_LINK,WEDDININGPLZ_INSTAGRAM,INSTAGRAM_IMAGE_LINK,CURRENT_YEAR,WEBSITE_NAME,SUPPORT_EMAIL);
                        
                        
                        $contentBusineddAdded = str_replace($searchBusinessAdded, $replaceBusinessAdded, $businessAdded);
                       // $mailToBusinessAdded = $params['claim_email'];
                        
                        //$vEmail = $vendorDetails[0]['email'];
                        //$vEmail = 'parullwadhwa00@gmail.com';                        
                        //$this->sendMail($vEmail, $subject, $contentBusineddAdded, $api = true);
                        //$this->sendMail($userEmail, $subject, $contentBusineddAdded, $api = true);
                        //$userEmail = 'bcb.passion@gmail.com';
                        
                        $this->sendLargeMail($userEmail, $subjectEmail, $contentBusineddAdded, $api = true);
                        $this->sendLargeMail(BUSINESS_EDIT_EMAIL, $subjectEmail, $contentBusineddAdded, $api = true);
                    }else if(!isset($data['businessType']) && $data['business_edited'] ==1){
                        
                        $catUrl = $this->createTextIndex(' ','-',$vendorDetails[0]['categoryname']);
                        $vURL = WEBSITE_URL.$vendorDetails[0]['city_url'].'/'.$catUrl.'/'.$vendorDetails[0]['vendor_url'];
                        $vendorDlpUrl = '<a href="'.$vURL.'">'. $vURL.'</a>';
                        
                        $findBusinessUrl = WEBSITE_URL.'claimbusiness/findbusiness';
                        $findBusinessUrlA = '<a href="'.$findBusinessUrl.'">'. $findBusinessUrl.'</a>';
                        
                        $businessEdited = sprintf($this->frmMsg['EDITBUSINESSAPPROVED']);
                        $searchBusinessEdited = array("{NAME}","{VENDORNAME}", "{VENDORDLPURL}", "{ADD_BUSINESS_URL}","{DATA_EMAIL}", "{CONTACTWEDDINGPLZ}", "{WEDDINGPLZ_FB_LINK}", "{WEDDINGPLZ_TWITTER_LINK}", "{WEDDINGPLZ_GP_LINK}");
                        $replaceBusinessEdited = array($vendorDetails[0]['person_name'],$vendorDetails[0]['name'],$vendorDlpUrl,$findBusinessUrlA, DATA_EMAIL, WEDDING_PLZ_CONTACT_NO, WEDDINGPLZ_FB_LINK, WEDDINGPLZ_TWITTER_LINK, WEDDINGPLZ_GP_LINK);
                        $contentBusineddEdited = str_replace($searchBusinessEdited, $replaceBusinessEdited, $businessEdited);
                       // $mailToBusinessAdded = $params['claim_email'];
                        $subject = 'Business Listing Edited - '.$vendorDetails[0]['name'];
                        
                        //$userEmail = 'bidhan@weddingplz.com';
                        $this->sendMail($userEmail, $subject, $contentBusineddEdited, $api = true);
                        
                    }
                }
            }else if ($data['status'] == '2') {
                //$this->_dashboardResource->updatevendorstatus($data);

                /*                 * ******send mail to vendor on rejected the business****** */

                /* $claimedVendorDetails = $this->_claimbusinessModal->claimbusinessdetail($data['id']);
                  $businessrejected = sprintf($this->frmMsg['BUSINESSCLAIMEDREJECTED']);
                  $searchrBusinessRejected = array("{VENDORNAME}","{SUPPORTMAIL}","{CONTACTWEDDINGPLZ}","{WEDDINGPLZ_FB_LINK}","{FB_IMAGE_LINK}","{WEDDINGPLZ_TWITTER_LINK}","{TWITTER_IMAGE_LINK}","{WEDDINGPLZ_GP_LINK}","{GP_IMAGE_LINK}");
                  $replaceBusinessRejected = array($claimedVendorDetails[0]['name'],SUPPORT_EMAIL,WEDDING_PLZ_CONTACT_NO,WEDDINGPLZ_FB_LINK,FB_IMAGE_LINK,WEDDINGPLZ_TWITTER_LINK,TWITTER_IMAGE_LINK,WEDDINGPLZ_GP_LINK,GP_IMAGE_LINK);
                  $contentclaimedRejected = str_replace($searchrBusinessRejected, $replaceBusinessRejected,$businessrejected);
                  $mailToBusinessClaimRejected =  $claimedVendorDetails[0]['email'];
                  $subject = $claimedVendorDetails[0]['name']. '- Business Claim Rejected';
                  $this->sendMail($claimedVendorDetails[0]['email'], $subject, $contentclaimedRejected, $api = true); */
                //$this->sendMail('jkmishra89@gmail.com', $subject, $contentclaimedRejected, $api = true);

                /*                 * ******send mail to vendor on rejected the business****** */
                
                if(isset($data['businessType']) && $data['businessType'] == 2){
                    /*
                    $businessAdded = sprintf($this->frmMsg['BUSINESSREJECTED']);
                    $searchBusinessAdded = array("{NAME}","{VENDORNAME}", "{SUPPORTMAIL}", "{CONTACTWEDDINGPLZ}", "{WEDDINGPLZ_FB_LINK}", "{FB_IMAGE_LINK}", "{WEDDINGPLZ_TWITTER_LINK}", "{TWITTER_IMAGE_LINK}", "{WEDDINGPLZ_GP_LINK}", "{GP_IMAGE_LINK}");
                    $replaceBusinessAdded = array($claimedVendorDetails[0]['person_name'],$claimedVendorDetails[0]['name'], DATA_EMAIL, WEDDING_PLZ_CONTACT_NO, WEDDINGPLZ_FB_LINK, FB_IMAGE_LINK, WEDDINGPLZ_TWITTER_LINK, TWITTER_IMAGE_LINK, WEDDINGPLZ_GP_LINK, GP_IMAGE_LINK);
                    $contentBusineddAdded = str_replace($searchBusinessAdded, $replaceBusinessAdded, $businessAdded);
                   
                    $subject = $claimedVendorDetails[0]['name']. '- Add a Business Request Rejected';
                    $vEmail = $claimedVendorDetails[0]['email'];
                    
                    $this->sendMail($vEmail, $subject, $contentBusineddAdded, $api = true);*/
                }else if(isset($data['businessType']) && $data['businessType'] ==1){
                    
                    $businessAdded = sprintf($this->frmMsg['BUSINESSCLAIMEDREJECTED']);
                    $searchBusinessAdded = array("{NAME}","{VENDORNAME}", "{SUPPORTMAIL}", "{CONTACTWEDDINGPLZ}", "{WEDDINGPLZ_FB_LINK}", "{FB_IMAGE_LINK}", "{WEDDINGPLZ_TWITTER_LINK}", "{TWITTER_IMAGE_LINK}", "{WEDDINGPLZ_GP_LINK}", "{GP_IMAGE_LINK}");
                    $replaceBusinessAdded = array($claimedVendorDetails[0]['person_name'],$claimedVendorDetails[0]['name'], DATA_EMAIL, WEDDING_PLZ_CONTACT_NO, WEDDINGPLZ_FB_LINK, FB_IMAGE_LINK, WEDDINGPLZ_TWITTER_LINK, TWITTER_IMAGE_LINK, WEDDINGPLZ_GP_LINK, GP_IMAGE_LINK);
                    $contentBusineddAdded = str_replace($searchBusinessAdded, $replaceBusinessAdded, $businessAdded);
                   // $mailToBusinessAdded = $params['claim_email'];
                    $subject = $claimedVendorDetails[0]['name']. '- Business Claim Rejected';
                    //$vEmail = $claimedVendorDetails[0]['email'];
                    //$vEmail = 'parullwadhwa00@gmail.com';
                    //$this->sendMail($vEmail, $subject, $contentBusineddAdded, $api = true);
                    //$this->sendMail($userEmail, $subject, $contentBusineddAdded, $api = true);
                    
                }
            }
            echo '1';
            die;
        }
    }

    /*     * ********************Functions to display data using jQgrid for Admin[13 Aug 2014]*********************** */

    public function noresultAction() {

        $request = $this->getRequest();
        global $noRecord;
        if ($request->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $data = $this->getRequest()->getPost();
            //dd($data);
            $params = $this->getSearchParams($data);
            $params['fields']['main'] = array('*');

            $result = $this->_dashboardResource->fetchNotFoundSearch($params);

            //dd($result);

            $responce = new stdClass();
            $responce->page = $result['page'];
            $responce->total = $result['total'];
            $responce->records = $result['records'];



            foreach ($result['result'] as $k => $val) {
                $selectStatus = '';

                $id = $val['id'];

                $remarks = $val['remarks'];
                $remarkInputType = "<input type='text' placeholder='Plz write your remark' value='$remarks' id='norecord_$id' name='norecord_$id'>";
                $selectStatus .= "<select id='added_$id' name='added_$id'>";
                foreach ($noRecord as $key => $statusVal) {
                    $selected = '';

                    if ($key == $val['status']) {
                        $selected = 'selected = selected';
                    }
                    $selectStatus .= "<option value='$key' $selected> $statusVal </option>";
                }
                $selectStatus .="</select>";
                $statusUpdate = "<a onclick='return norecordsfound($id);' href='javascript:void(0);'><img width='25' height='20' src='" . ADMIN_IMAGE_URL . "update.png' alt='Status Update' title='Status Update'></a>";
                $responce->rows[$k]['id'] = $val['id'];
                if ($remarks == '' && $val['status'] == 0) {
                    $responce->rows[$k]['cell'] = array(
                        //$chkInputType,

                        '<strong>' . $this->changeDateFormat($val['date'], DATETIMEFORMAT, "") . '</strong>',
                        '<strong>' . $val['ip'] . '</strong>',
                        '<strong>' . $val['category'] . '</strong>',
                        '<strong>' . $val['location_name'] . '</strong>',
                        '<strong>' . $val['zone_name'] . '</strong>',
                        '<strong>' . $val['city_name'] . '</strong>',
                        $remarkInputType,
                        $selectStatus,
                        $statusUpdate
                    );
                } else {
                    $responce->rows[$k]['cell'] = array(
                        //$chkInputType,                        
                        $this->changeDateFormat($val['date'], DATETIMEFORMAT, ""),
                        $val['ip'],
                        $val['category'],
                        $val['location_name'],
                        $val['zone_name'],
                        $val['city_name'],
                        $remarkInputType,
                        $selectStatus,
                        $statusUpdate
                    );
                }
            }

            echo $this->jsonEncode($responce);
            exit;
        }
    }

    /*     * ********************Functions to display all search data using jQgrid for Admin[02 Dec 2014]*********************** */

    public function allresultAction() {

        $request = $this->getRequest();
        global $noRecord;
        if ($request->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $data = $this->getRequest()->getPost();
            $params = $this->getSearchParams($data);
            $params['fields']['main'] = array('*');
            $params['condition']['searchCount'] = 'searchTag';

            $result = $this->_dashboardResource->fetchNotFoundSearch($params);
            $responce = new stdClass();
            $responce->page = $result['page'];
            $responce->total = $result['total'];
            $responce->records = $result['records'];

            foreach ($result['result'] as $k => $val) {
                $id = $val['id'];
                foreach ($noRecord as $key => $statusVal) {
                    $responce->rows[$k]['id'] = $val['id'];
                    $responce->rows[$k]['cell'] = array(
                        $this->changeDateFormat($val['date'], DATETIMEFORMAT, ""), $val['ip'],
                        $val['category'],
                        $val['location_name'],
                        $val['zone_name'],
                        $val['city_name'],
                    );
                }
            }

            echo $this->jsonEncode($responce);
            exit;
        }
    }

    public function whatswrongAction() {

        $request = $this->getRequest();
        global $status_type;
        if ($request->isXmlHttpRequest()) {

            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $data = $this->getRequest()->getPost();
            //dd($data);
            $params = $this->getSearchParams($data);
            $params['fields']['main'] = array('*');

            $result = $this->_dashboardResource->fetchWhatsWrongData($params);
            //dd($result);
            $responce = new stdClass();
            $responce->page = $result['page'];
            $responce->total = $result['total'];
            $responce->records = $result['records'];
            foreach ($result['result'] as $k => $val) {
                $selectStatus = '';
                $id = $val['id'];
                $vendorid = $val['vendorid'];
                $remarks = $val['remarks'];
                $remarkInputType = "<input type='text' placeholder='Plz write your remark' value='$remarks' id='whatsWrongRmk_$id' name='whatsWrongRmk_$id'>";
                $selectStatus .= "<select id='whatsWrongSts_$id' name='whatsWrongSts_$id'>";
                foreach ($status_type as $key => $statusVal) {
                    $selected = '';

                    if ($key == $val['status']) {
                        $selected = 'selected = selected';
                    }
                    $selectStatus .= "<option value='$key' $selected> $statusVal </option>";
                }
                $selectStatus .="</select>";
                $statusUpdate = "<a onclick='return updateAnythingWrongstatus($id);' href='javascript:void(0);'><img width='25' height='20' src='" . ADMIN_IMAGE_URL . "update.png' alt='Status Update' title='Status Update'></a>";
                $responce->rows[$k]['id'] = $val['id'];
                if ($remarks == '' && $val['status'] == 0) {
                    $responce->rows[$k]['cell'] = array(
                        '<strong>' . $this->changeDateFormat($val['date'], DATEFORMAT, "") . '</strong>',
                        "<a href = '/admin/vendor/viewinfo/id/$vendorid'><strong>" . $val['name'] . "</strong></a>",
                        '<strong>' . $val['email'] . '</strong>',
                        '<strong>' . $val['whatswrong'] . '</strong>',
                        '<strong>' . $val['wronginfo'] . '</strong>',
                        $remarkInputType,
                        $selectStatus,
                        $statusUpdate
                    );
                } else {
                    $responce->rows[$k]['cell'] = array(
                        $this->changeDateFormat($val['date'], DATEFORMAT, ""),
                        "<a href = '/admin/vendor/viewinfo/id/$vendorid'>" . $val['name'] . '</a>',
                        $val['email'],
                        $val['whatswrong'],
                        $val['wronginfo'],
                        $remarkInputType,
                        $selectStatus,
                        $statusUpdate
                    );
                }
            }

            echo $this->jsonEncode($responce);
            exit;
        }
    }

    public function vendorlistAction() {

        $params['count'] = '';
        $request = $this->getRequest();
        global $addedBusiness;
        $vendorEditUrl = '';
        if ($request->getParam('business_type')) {
            $businessType = $request->getParam('business_type');
            $vendorEditUrl = '/business_type/' . $businessType;
        } else {
            $businessType = 0;
        }

        if ($request->getParam('businessEdited')) {
            $businessEdited = $request->getParam('businessEdited');
            $vendorEditUrl = '/businessEdited/' . $businessEdited;
            
        } else {
            $businessEdited = '';
        }
        $returnType = $request->getParam('returnType');
        $this->view->returnType = $returnType;

        $this->view->business_type = $businessType;

        $this->view->businessEdited = $businessEdited;
        //echo $businessEdited; die;
        if ($request->isXmlHttpRequest()) {

            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $data = $this->getRequest()->getPost();
            //dd($data);
            $params = $this->getSearchParams($data);
     
            $params['fields']['main'] = array('id', 'name');
            
            if ($businessEdited) { //Business edited
                $params['sidx'] = 'updated_date';
                $params['sord'] = 'DESC';       
                $params['fields']['claimed_vendors'] = array('mcv.id as claim_business_id', 'mcv.email','mcv.city', 'mcv.claimed_mobile_number', 'mcv.edit_remarks AS remarks', 'mcv.edit_status AS status', 'mcv.business_type', 'mcv.claimed_at', 'mcv.updated_date');
            } else if ($businessType == 1) { //Claimed vendors
                $params['fields']['claimed_vendors'] = array('mcv.id as claim_business_id', 'mcv.email','mcv.city', 'mcv.claimed_mobile_number', 'mcv.claimed_remarks AS remarks', 'mcv.claimed_status AS status', 'mcv.business_type', 'mcv.claimed_at');
            } else if ($businessType == 2) {//Addedd vendors
                $params['fields']['claimed_vendors'] = array('mcv.id as claim_business_id', 'mcv.email','mcv.city', 'mcv.claimed_mobile_number', 'mcv.remarks', 'mcv.status', 'mcv.business_type', 'mcv.claimed_at');
            }

            $params['condition']['business_type'] = $businessType;
            if($businessEdited){
                $params['condition']['has_been_updated'] = $businessEdited;
                //$params['condition']['business_type'] = 2;
            }
            //$params['sidx'] = 'claim_business_id';
            //echo $businessEdited; die;
            //$params['condition']['status'] = 0;
            //dd($params);
            $result = $this->_dashboardResource->fetchNewVendorCreatedRecord($params);
            //dd($result['result']);
            $responce = new stdClass();
            $responce->page = $result['page'];
            $responce->total = $result['total'];
            $responce->records = $result['records'];
            foreach ($result['result'] as $k => $val) {
                $selectStatus = '';
                $id = $val['id'];
                $aid = "<a class = 'a-tag' href='/admin/vendor/addedit/id/$id'>".$id."</a>";              
               /*if ($businessType == 2 || $businessType == 0) {
                    $aid = "<a class = 'a-tag' href='/admin/vendor/addedit/id/$id'>".$id."</a>";              
                }else{
                    $aid = $id;
                }*/
                $userEmailId = '"'.trim($val['user_email']).'"';
                $claimBusinessId = $val['claim_business_id'];
                $remarks = $val['remarks'];
                if(!empty($vendorEditUrl)){
                    $vendorName = "<a href='/admin/dashboard/admin-check-edit/bid/$claimBusinessId$vendorEditUrl/returnType/$returnType'>" . $val['name'] . "</a>";
                }else{
                    $vendorName = "<a href='/admin/dashboard/alertaction/bid/$claimBusinessId$vendorEditUrl/returnType/$returnType'>" . $val['name'] . "</a>";
                }
                //$vendorName = $val['name'];
                if ($businessType != 0) {
                $remarkInputType = "<input type='text' placeholder='Plz write your remark' value='$remarks' id='remarks_$claimBusinessId' name='remarks_$claimBusinessId'>";
                }else{
                    $remarkInputType ="";
                }
                if ($businessType != 0) {
                $selectStatus .= "<select id='status_$claimBusinessId' name='status_$claimBusinessId'>";
                foreach ($addedBusiness as $key => $statusVal) {
                    $selected = '';

                    if ($key == $val['status']) {
                        $selected = 'selected = selected';

                        if ($val['status'] > 0) {
                            $selectStatus .= "<option value='$key' $selected> $statusVal </option>";
                            break;
                        }
                    }

                    if ($val['status'] == 0) {
                        $selectStatus .= "<option value='$key' $selected> $statusVal </option>";
                    }
                }
                $selectStatus .="</select>";
                } else{
                     $selectStatus ="";
                }
                if ($businessType != 0) {
                if (isset($params['condition']['has_been_updated'])) {
                    $statusUpdate = "<a onclick='return updateEditBusinessStatus($claimBusinessId,$id,$userEmailId);' href='javascript:void(0);'><img width='25' height='20' src='" . ADMIN_IMAGE_URL . "update.png' alt='Status Update' title='Status Update'></a>";
                } else {
                    $statusUpdate = "<a onclick='return updateVendorStatus($claimBusinessId,$id,$businessType,$userEmailId);' href='javascript:void(0);'><img width='25' height='20' src='" . ADMIN_IMAGE_URL . "update.png' alt='Status Update' title='Status Update'></a>";
                }
                } else{
                    $statusUpdate ="";
                }

                $responce->rows[$k]['id'] = $val['id'];
                $createdModifiedDate = '';
                if ($businessEdited) { //Business edited
                    if($val['updated_date'] != '0000-00-00 00:00:00'){
                        $createdModifiedDate = $this->changeDateFormat($val['updated_date'], DATEFORMAT, DATE_TIME_FORMAT);
                    }else{
                        $createdModifiedDate = $this->changeDateFormat($val['claimed_at'], DATEFORMAT, DATE_TIME_FORMAT);
                    }
                    
                }else{
                    $createdModifiedDate = $this->changeDateFormat($val['claimed_at'], DATEFORMAT, DATE_TIME_FORMAT);
                }
                
                
                if ($val['remarks'] == '' && $val['status'] == 0) {
                    $responce->rows[$k]['cell'] = array(
                        //$chkInputType,
                        "<strong>" . $aid. "</strong>",
                        "<strong>" . $createdModifiedDate . "</strong>",
                        "<strong>" . $val['user_email'] . "</strong>",
                        "<strong>" . $val['claimed_mobile_number'] . "</strong>",
                        "<strong>" . $vendorName . "</strong>",
                        "<strong>" . $val['category_name'] . "</strong>",
                        "<strong>" . $val['city_name'] . "</strong>",                        
                        $remarkInputType,
                        $selectStatus,
                        $statusUpdate
                    );
                } else {
                    $responce->rows[$k]['cell'] = array(
                        //$chkInputType,
                        $aid,
                        $createdModifiedDate,
                        $val['user_email'],
                        $val['claimed_mobile_number'],
                        $vendorName,
                        $val['category_name'],
                        $val['city_name'],
                        $remarkInputType,
                        $selectStatus,
                        $statusUpdate
                    );
                }
            }

            echo $this->jsonEncode($responce);
            exit;
        }
    }

    public function allmissingbusinessAction() {

        $request = $this->getRequest();
        global $status_type;

        if ($request->isXmlHttpRequest()) {

            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $data = $this->getRequest()->getPost();
            //dd($data);
            $params = $this->getSearchParams($data);
            $params['fields']['main'] = array('*');
            //$params['condition']['status'] = 0;
            //dd($params);
            $result = $this->_dashboardResource->fetchMissingBusinessRecords($params);
            //dd($result);
            $responce = new stdClass();
            $responce->page = $result['page'];
            $responce->total = $result['total'];
            $responce->records = $result['records'];
            foreach ($result['result'] as $k => $val) {
                $selectStatus = '';
                $id = $val['id'];
                $remarks = $val['remarks'];
                $remarkInputType = "<input type='text' placeholder='Plz write your remark' value='$remarks' id='missingSomethingRmk_$id' name='missingSomethingRmk_$id'>";
                $selectStatus .= "<select id='missingSomethingSts_$id' name='missingSomethingSts_$id'>";
                foreach ($status_type as $key => $statusVal) {
                    $selected = '';

                    if ($key == $val['status']) {
                        $selected = 'selected = selected';
                    }
                    $selectStatus .= "<option value='$key' $selected> $statusVal </option>";
                }
                $selectStatus .="</select>";
                $statusUpdate = "<a onclick='return updateMissingSomethingStatus($id);' href='javascript:void(0);'><img width='25' height='20' src='" . ADMIN_IMAGE_URL . "update.png' alt='Status Update' title='Status Update'></a>";
                $responce->rows[$k]['id'] = $val['id'];
                if ($val['remarks'] == '' && $val['status'] == 0) {
                    $responce->rows[$k]['cell'] = array(
                        //$chkInputType,
                        "<strong>" . $this->changeDateFormat($val['date'], DATEFORMAT, "") . "</strong>",
                        "<strong>" . $val['email'] . "</strong>",
                        "<strong>" . $val['detail'] . "</strong>",
                        $remarkInputType,
                        $selectStatus,
                        $statusUpdate
                    );
                } else {
                    $responce->rows[$k]['cell'] = array(
                        //$chkInputType,
                        $this->changeDateFormat($val['date'], DATEFORMAT, ""),
                        $val['email'],
                        $val['detail'],
                        $remarkInputType,
                        $selectStatus,
                        $statusUpdate
                    );
                }
            }

            echo $this->jsonEncode($responce);
            exit;
        }
    }

    public function delmissingstatusAction() {
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->getRequest()->getParam('missingBusinessIds');
        if (!empty($id)) {
            $this->_dashboardResource->deleteMultibusiness($id);
        }
        $this->_redirect("admin/dashboard/allmissingbusiness");
    }

    /**
     * This function is used  something wrong page
     * * Updated By :  Umesh
     * Created By :  Umesh
     * Date : 3 Jun,2014
     * @param void
     * @return void
     */
    public function somethingAction() {



        $request = $this->getRequest();
        global $something_mind;

        if ($request->isXmlHttpRequest()) {

            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $data = $this->getRequest()->getPost();
            //dd($data);
            $params = $this->getSearchParams($data);
            $params['fields']['main'] = array('*');
            //$params['condition']['status'] = 0;
            //dd($params);
            $result = $this->_dashboardResource->SomethingOnYourMind($params);
            //dd($result);
            $responce = new stdClass();
            $responce->page = $result['page'];
            $responce->total = $result['total'];
            $responce->records = $result['records'];
            foreach ($result['result'] as $k => $val) {
                $selectStatus = '';
                $id = $val['id'];
                $remarks = $val['remarks'];
                $remarkInputType = "<input type='text' placeholder='Plz write your remark' value='$remarks' id='somethingRmk_$id' name='somethingRmk_$id'>";
                $selectStatus .= "<select id='somethingSts_$id' name='somethingSts_$id'>";
                foreach ($something_mind as $key => $statusVal) {
                    $selected = '';

                    if ($key == $val['status']) {
                        $selected = 'selected = selected';
                    }
                    $selectStatus .= "<option value='$key' $selected> $statusVal </option>";
                }
                $selectStatus .="</select>";
                $statusUpdate = "<a onclick='return updatestatus($id);' href='javascript:void(0);'><img width='25' height='20' src='" . ADMIN_IMAGE_URL . "update.png' alt='Status Update' title='Status Update'></a>";
                $responce->rows[$k]['id'] = $val['id'];
                
                if(strtotime($val['date']) > 0){
                    $dateTime = $this->changeDateFormat($val['date'], DATEFORMAT, "");
                }else{
                    $dateTime = '';
                }
                
                
                
                if ($val['remarks'] == '' && $val['status'] == 0) {
                    $responce->rows[$k]['cell'] = array(
                        //$chkInputType,
                        "<strong>" . $dateTime . "</strong>",                        
                        "<strong>" . $val['email'] . "</strong>",
                        "<strong>" . $val['comments'] . "</strong>",
                        "<strong>" . $val['city_name'] . "</strong>",
                        $remarkInputType,
                        $selectStatus,
                        $statusUpdate
                    );
                } else {
                    $responce->rows[$k]['cell'] = array(
                        //$chkInputType,
                        $dateTime,                        
                        $val['email'],
                        $val['comments'],
                        $val['city_name'],
                        $remarkInputType,
                        $selectStatus,
                        $statusUpdate
                    );
                }
            }

            echo $this->jsonEncode($responce);
            exit;
        }
    }

    /**
     * This function is used  something wrong page
     * Created By :  Umesh
     * Date : 3 Jun,2014
     * @param void
     * @return void
     */
    public function dealAction() {


        $request = $this->getRequest();
        global $dealsBusiness;

        if ($request->isXmlHttpRequest()) {

            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $data = $this->getRequest()->getPost();
            //dd($data);
            $params = $this->getSearchParams($data);
            $params['fields']['main'] = array('*');
            //$params['condition']['status'] = 0;
            //dd($params);
            $result = $this->_dashboardResource->dealRequested($params);
            //dd($result);
            $responce = new stdClass();
            $responce->page = $result['page'];
            $responce->total = $result['total'];
            $responce->records = $result['records'];
            foreach ($result['result'] as $k => $val) {
                $selectStatus = '';
                $id = $val['id'];
                $remarks = $val['remarks'];
                $remarkInputType = "<input type='text' placeholder='Plz write your remark' value='$remarks' id='dealRmk_$id' name='dealRmk_$id'>";
                $selectStatus .= "<select id='dealSts_$id' name='dealSts_$id'>";
                foreach ($dealsBusiness as $key => $statusVal) {
                    $selected = '';

                    if ($key == $val['status']) {
                        $selected = 'selected = selected';
                    }
                    $selectStatus .= "<option value='$key' $selected> $statusVal </option>";
                }
                $selectStatus .="</select>";
                $statusUpdate = "<a onclick='return updateDealstatus($id);' href='javascript:void(0);'><img width='25' height='20' src='" . ADMIN_IMAGE_URL . "update.png' alt='Status Update' title='Status Update'></a>";
                $responce->rows[$k]['id'] = $val['id'];
                if ($val['remarks'] == '' && $val['status'] == 0) {
                    $responce->rows[$k]['cell'] = array(
                        "<strong>" . $this->changeDateFormat($val['date'], DATEFORMAT, "") . "</strong>",
                        "<strong>" . $val['email'] . "</strong>",
                        "<strong>" . $val['businessname'] . "</strong>",
                        "<strong>" . $val['city_name'] . "</strong>",
                        "<strong>" . $val['catname'] . "</strong>",
                        $remarkInputType,
                        $selectStatus,
                        $statusUpdate
                    );
                } else {
                    $responce->rows[$k]['cell'] = array(
                        $this->changeDateFormat($val['date'], DATEFORMAT, ""),
                        $val['email'],
                        $val['businessname'],
                        $val['city_name'],
                        $val['catname'],
                        $remarkInputType,
                        $selectStatus,
                        $statusUpdate
                    );
                }
            }

            echo $this->jsonEncode($responce);
            exit;
        }
    }

    public function latestreviewsAction() {


        $request = $this->getRequest();
        global $addedReviews;

        if ($request->isXmlHttpRequest()) {

            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $data = $this->getRequest()->getPost();
            
            $params = $this->getSearchParams($data);
            //dd($params);
            $params['fields']['main'] = array('id', 'user_id', 'vendor_id','city','rating', 'review', 'status', 'remarks', 'position', 'created_at');
            //$params['condition']['status'] = 0;
            //dd($params);
            $result = $this->_dashboardResource->latestReview($params); 
            //dd($result);
            $responce = new stdClass();
            $responce->page = $result['page'];
            $responce->total = $result['total'];
            $responce->records = $result['records'];
            $editReviewLink = '';
            $editReviewLinkUrl = '';
            foreach ($result['result'] as $k => $val) {
                $selectStatus = '';
                $id = $val['id'];
                $editReviewLinkUrl = ADMIN_BASE_URL.'review/addedit/id/'.$id.'/section/d';
                $editReviewLink = "<a href = '$editReviewLinkUrl' class='a-tag'>".$id."</a>";
                $vendorid = $val['vendor_id'];
                $userId = $val['user_id'];                
                $remarks = $val['remarks'];
                $remarkInputType = "<input type='text' placeholder='Plz write your remark' value='$remarks' id='reviewRmk_$id' name='reviewRmk_$id' width = '10px'>";
                $selectStatus .= "<select id='reviewSts_$id' name='reviewSts_$id'>";
                foreach ($addedReviews as $key => $statusVal) {
                    $selected = '';

                    if ($key == $val['status']) {
                        $selected = 'selected = selected';
                    }
                    $selectStatus .= "<option value='$key' $selected> $statusVal </option>";
                }
                $selectStatus .="</select>";
                $statusUpdate = "<a onclick='return updateReviewStatus($id,$vendorid);' href='javascript:void(0);'><img width='25' height='20' src='" . ADMIN_IMAGE_URL . "update.png' alt='Status Update' title='Status Update'></a>";
                $responce->rows[$k]['id'] = $val['id'];
                if ($val['remarks'] == '' && $val['status'] == 0) {
                    $responce->rows[$k]['cell'] = array(
                        "<strong>" . $editReviewLink . "</strong>",
                        "<strong>" . $this->changeDateFormat($val['created_at'], DATEFORMAT, "") . "</strong>",
                        "<strong><a href = '/admin/vendor/viewinfo/id/$vendorid' target = '_blank'>" . $val['vendor_name'] . "</a></strong>",
                        "<strong><a href = '/admin/user/addedit/id/$userId' target = '_blank'>" . $val['user_name'] . "</a></strong>",
                        "<strong>" . $val['email'] . "</strong>",
                        "<strong>" . $val['city_name'] . "</strong>",
                        "<strong>" . $val['catname'] . "</strong>",
                        "<strong>" . $val['rating'] . "</strong>",
                        "<strong>" . $val['review'] . "</strong>",
                        "<strong>" . $val['position'] . "</strong>",
                        $remarkInputType,
                        $selectStatus,
                        $statusUpdate
                    );
                } else {
                    $responce->rows[$k]['cell'] = array(
                        $editReviewLink,
                        $this->changeDateFormat($val['created_at'], DATEFORMAT, ""),
                        "<a href = '/admin/vendor/viewinfo/id/$vendorid'>" . $val['vendor_name'] . "</a>",
                        "<a href = '/admin/user/addedit/id/$userId' target = '_blank'>" . $val['user_name'] . "</a>",
                        $val['email'],
                        $val['city_name'],
                        $val['catname'],
                        $val['rating'],
                        $val['review'],
                        $val['position'],
                        $remarkInputType,
                        $selectStatus,
                        $statusUpdate
                    );
                }
            }

            echo $this->jsonEncode($responce);
            exit;
        }
    }

    /**
     * This function is used  to set status in vendor search
     * Created By :  Bidhan
     * Date : 22 Aug,2014
     * @param void
     * @return void
     */
    public function reviewupdatestatusAction() {

        $vendorId = $this->getRequest()->getParam('vendorId');






        $id = $this->getRequest()->getParam('id');

        $getReview = $this->_dashboardResource->fetchFirstReviewData($id);

        $userEmail = $this->_dashboardResource->fetchUserEmail($getReview[0]['user_id']);
        $userEmail = $userEmail[0]['email'];


        $review = $getReview[0]['review'];

        $remarks = $this->getRequest()->getParam('remarks');
        $status = $this->getRequest()->getParam('status');
        if (($status != '0') && ($remarks != '') && ($id != '')) {
            $return = $this->_dashboardResource->updateReviewStatusRecords($id, $remarks, $status);

            /*

            if ($status == '1') {
                // send mail start by jkm 	30dec 14

                $getVender = $this->_detailResource->getvendor($vendorId);
                //dd($getVender);
                $vendorEmail = $getVender[0]['vendor_email'];
                if ($getVender[0]['business_type'] == 1 && $getVender[0]['business_edited'] == 1 && $getVender[0]['status'] == 1) {

                    $vendorNameClaim = $getVender[0]['vendor_name'];
                    $businessNameClaim = $getVender[0]['vendor_name'];
                    $reviewTextClaim = $review;
                    $senderNameClaim = $userEmail;
                    $WEDDINGPLZ_CLAIMBUSINESS_LINK = WEBSITE_URL . 'Claimbusiness/findbusiness';

                    
                    
                    $contentVendorClaim = sprintf($this->frmMsg['SENDREVIEWINFOVENDORCLAIM_NEW']); 
                    $searchVendorClaim = array("{WP_LOGO_IMAGE_LINK}","{SUBJECT}","{VENDORNAME}","{BUSINESSNAME}","{REVIEW_TEXT}", "{SENDERNAME}","{SITEURL}","{WEDDING_PLZ_CONTACT_NO}", "{WEDDINGPLZ_FB_LINK}", "{FB_IMAGE_LINK}", "{WEDDINGPLZ_TWITTER_LINK}", "{TWITTER_IMAGE_LINK}", "{WEDDINGPLZ_GP_LINK}", "{GP_IMAGE_LINK}","{WEDDININGPLZ_INSTAGRAM}","{INSTAGRAM_IMAGE_LINK}","{CURRENT_YEAR}","{WEBSITE_NAME}","{SUPPORT_EMAIL}");
                    $replaceVendorClaim = array(WP_LOGO_IMAGE_LINK,$subject,$vendorNameClaim,$vendorNameClaim, $reviewTextClaim,$senderNameClaim,SITEURL, WEDDING_PLZ_CONTACT_NO, WEDDINGPLZ_FB_LINK, FB_IMAGE_LINK, WEDDINGPLZ_TWITTER_LINK,TWITTER_IMAGE_LINK,WEDDINGPLZ_GP_LINK,GP_IMAGE_LINK,WEDDININGPLZ_INSTAGRAM,INSTAGRAM_IMAGE_LINK,CURRENT_YEAR,WEBSITE_NAME,SUPPORT_EMAIL);
                        
                    
                    $contentVendorClaim = str_replace($searchVendorClaim, $replaceVendorClaim, $contentVendorClaim);
                    $mailToVendorClaim = $this->getUserEmailId($vendorEmail); 
                    
                    die('yes');
                } else {



                    
                    $siteUrl = WEBSITE_NAME;
                    $supportmail = SUPPORT_EMAIL;

                    $fbLink = WEDDINGPLZ_FB_LINK;
                    $twitterLink = WEDDINGPLZ_TWITTER_LINK;
                    $gpLink = WEDDINGPLZ_GP_LINK;

                    $fbImageLink = FB_IMAGE_LINK;
                    $twitterImageLink = TWITTER_IMAGE_LINK;
                    $gpImageLink = GP_IMAGE_LINK;
                    $weddingplzcontact = WEDDING_PLZ_CONTACT_NO;

                    

                    $vendorNameNotClaim = $getVender[0]['vendor_name'];
                    $businessNameNotClaim = $getVender[0]['vendor_name'];
                    $reviewTextNotClaim = $review;
                    $senderNameNotClaim = $userEmail;
                    $WEDDINGPLZ_CLAIMBUSINESS_LINK = WEBSITE_URL . 'Claimbusiness/findbusiness';

                    
                    $contentVendorNotClaim = sprintf($this->frmMsg['SENDREVIEWINFOVENDORNOTCLAIM_NEW']);
                    $searchVendorNotClaim = array("{WP_LOGO_IMAGE_LINK}","{SUBJECT}","{VENDORNAME}", "{BUSINESSNAME}","{REVIEWTEXT}", "{SENDERNAME}","{WEDDINGPLZ_CLAIMBUSINESS_LINK}","{SITEURL}","{WEDDING_PLZ_CONTACT_NO}", "{WEDDINGPLZ_FB_LINK}", "{FB_IMAGE_LINK}", "{WEDDINGPLZ_TWITTER_LINK}", "{TWITTER_IMAGE_LINK}", "{WEDDINGPLZ_GP_LINK}", "{GP_IMAGE_LINK}","{WEDDININGPLZ_INSTAGRAM}","{INSTAGRAM_IMAGE_LINK}","{CURRENT_YEAR}","{WEBSITE_NAME}","{SUPPORT_EMAIL}");
                    $replaceVendorNotClaim = array(WP_LOGO_IMAGE_LINK,'User reviewed',$vendorNameNotClaim,$vendorNameNotClaim, $reviewTextNotClaim,$senderNameNotClaim,$WEDDINGPLZ_CLAIMBUSINESS_LINK,SITEURL, WEDDING_PLZ_CONTACT_NO, WEDDINGPLZ_FB_LINK, FB_IMAGE_LINK, WEDDINGPLZ_TWITTER_LINK,TWITTER_IMAGE_LINK,WEDDINGPLZ_GP_LINK,GP_IMAGE_LINK,WEDDININGPLZ_INSTAGRAM,INSTAGRAM_IMAGE_LINK,CURRENT_YEAR,WEBSITE_NAME,SUPPORT_EMAIL);
                       
                    
                    $contentVendorNotClaim = str_replace($searchVendorNotClaim, $replaceVendorNotClaim, $contentVendorNotClaim);
                    
                    $mailToVendorNotClaim = $this->getUserEmailId($vendorEmail);
                    
                }
            }*/
            // send mail end by jkm		







            echo '1';
            die;
        } else {
            echo '0';
            die;
        }
    }

    /**
     * This function is used delete search recodes
     * Created By :  Bidhan
     * Date : 22 Aug,2014
     * @param void
     * @return void
     */
    public function delreviewsAction() {
        //dd($this->getRequest()->getParams());
        if ($ids = $this->getRequest()->getParam('reviewDel_ids')) {
            //echo $id; die;
            $arrIds = explode(',', $ids);
            if ($del = $this->_dashboardResource->deleteReviews($arrIds)) {
                $this->_helper->FlashMessenger()->setNamespace('success')->addMessage("Records successfully deleted.");
                $this->_redirect(ADMIN_BASE_URL . 'dashboard/latestreviews');
            }
        }
    }

    public function eventsAction() {


        $request = $this->getRequest();
        global $eventStatus;
        global $nowDate;
        if ($request->getParam('source')) {
            $source = $request->getParam('source');
            $this->view->source = $source;
        }

        if ($request->isXmlHttpRequest()) {

            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $data = $this->getRequest()->getPost();
            //dd($data);
            $params = $this->getSearchParams($data);
            //dd($params);
            $params['fields']['main'] = array('id', 'event_name', 'event_date', 'event_end_date', 'event_time', 'email', 'event_type','phone', 'date', 'status', 'remark');

            if ($source == 'front') {
                $params['condition']['source'] = $source;
            }
            //dd($params);
            $result = $this->_dashboardResource->fetchAllEvents($params);
            //dd($result);
            $responce = new stdClass();
            $responce->page = $result['page'];
            $responce->total = $result['total'];
            $responce->records = $result['records'];
            foreach ($result['result'] as $k => $val) {
                $selectStatus = '';
                $id = $val['id'];
                $remarks = $val['remark'];
                $remarkInputType = "<input type='text' placeholder='Plz write your remark' value='$remarks' id='eventRmk_$id' name='eventRmk_$id'>";
                $selectStatus .= "<select id='eventSts_$id' name='eventSts_$id'>";
                foreach ($eventStatus as $key => $statusVal) {
                    $selected = '';

                    if ($key == $val['status']) {
                        $selected = 'selected = selected';
                    } 
                    $selectStatus .= "<option value='$key' $selected> $statusVal </option>";
                }
                $selectStatus .="</select>";
                $statusUpdate = "<a onclick='return updateEventStatus($id);' href='javascript:void(0);'><img width='25' height='20' src='" . ADMIN_IMAGE_URL . "update.png' alt='Status Update' title='Status Update'></a>";
                $responce->rows[$k]['id'] = $val['id'];
                if ($remarks == '' && $val['status'] == 0) {
                    $responce->rows[$k]['cell'] = array(
                        '<strong>' . $this->changeDateFormat($val['date'], DATEFORMAT, "") . '</strong>',
                        '<strong>' . $val['event_name'] . '</strong>',
                        '<strong>' . $val['phone'] . '</strong>',
                        '<strong>' . $val['event_date'] . '</strong>',
                        '<strong>' . $val['event_end_date'] . '</strong>',
                        '<strong>' . $val['event_time'] . '</strong>',
                        '<strong>' . $val['event_type'] . '</strong>',
                        '<strong>' . $val['email'] . '</strong>',
                        $remarkInputType,
                        $selectStatus,
                        $statusUpdate
                    );
                } else {
                    $responce->rows[$k]['cell'] = array(
                        $this->changeDateFormat($val['date'], DATEFORMAT, ""),
                        $val['event_name'],
                        $val['phone'],
                        $val['event_date'],
                        $val['event_end_date'],
                        $val['event_time'],
                        $val['event_type'],
                        $val['email'],
                        $remarkInputType,
                        $selectStatus,
                        $statusUpdate
                    );
                }
            }

            echo $this->jsonEncode($responce);
            exit;
        }
    }

    /**
     * This function is used  to set status in vendor search
     * Created By :  Bidhan
     * Date : 22 Aug,2014
     * @param void
     * @return void
     */
    public function eventupdatestatusAction() {
        $id = $this->getRequest()->getParam('id');
        $remarks = $this->getRequest()->getParam('remarks');
        $status = $this->getRequest()->getParam('status');
        if (($status != '0') && ($remarks != '') && ($id != '')) {
            $return = $this->_dashboardResource->updateEventStatusRecords($id, $remarks, $status);
            echo '1';
            die;
        } else {
            echo '0';
            die;
        }
    }

    /**
     * This function is used delete Events
     * Created By :  Bidhan
     * Date : 23 Aug,2014
     * @param void
     * @return void
     */
    public function deleventsAction() {
        //dd($this->getRequest()->getParams());
        if ($ids = $this->getRequest()->getParam('eventDel_ids')) {
            //echo $id; die;
            $arrIds = explode(',', $ids);
            if ($del = $this->_dashboardResource->deleteEvents($arrIds)) {
                $this->_helper->FlashMessenger()->setNamespace('success')->addMessage("Records successfully deleted.");
                $this->_redirect(ADMIN_BASE_URL . 'dashboard/events');
            }
        }
    }

    public function advertiseAction() {


        $request = $this->getRequest();
        global $advertiseStatus;

        if ($request->isXmlHttpRequest()) {

            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $data = $this->getRequest()->getPost();
            //dd($data);
            $params = $this->getSearchParams($data);
            //dd($params);
            $params['fields']['main'] = array('*');

            //dd($params);
            $result = $this->_dashboardResource->fetchAllAdvertise($params);

            $responce = new stdClass();
            $responce->page = $result['page'];
            $responce->total = $result['total'];
            $responce->records = $result['records'];
            foreach ($result['result'] as $k => $val) {
                $selectStatus = '';
                $id = $val['id'];
                $remarks = $val['remarks'];
                $remarkInputType = "<input type='text' placeholder='Plz write your remark' value='$remarks' id='advertiseRmk_$id' name='advertiseRmk_$id'>";
                $selectStatus .= "<select id='advertiseSts_$id' name='advertiseSts_$id'>";
                foreach ($advertiseStatus as $key => $statusVal) {
                    $selected = '';

                    if ($key == $val['status']) {
                        $selected = 'selected = selected';
                    }
                    $selectStatus .= "<option value='$key' $selected> $statusVal </option>";
                }
                $selectStatus .="</select>";
                $statusUpdate = "<a onclick='return updateAdvertiseStatus($id);' href='javascript:void(0);'><img width='25' height='20' src='" . ADMIN_IMAGE_URL . "update.png' alt='Status Update' title='Status Update'></a>";
                $responce->rows[$k]['id'] = $val['id'];
                if ($val['remarks'] == '' && $val['status'] == 0) {
                    $responce->rows[$k]['cell'] = array(
                        "<strong>" . $this->changeDateFormat($val['date'], DATEFORMAT, "") . "</strong>",
                        "<strong>" . $val['name'] . "</strong>",
                        "<strong>" . $val['email'] . "</strong>",
                        "<strong>" . $val['phone'] . "</strong>",
                        "<strong>" . $val['comments'] . "</strong>",
                        "<strong>" . $val['prefer_time'] . "</strong>",
                        $remarkInputType,
                        $selectStatus,
                        $statusUpdate
                    );
                } else {
                    $responce->rows[$k]['cell'] = array(
                        $this->changeDateFormat($val['date'], DATEFORMAT, ""),
                        $val['name'],
                        $val['email'],
                        $val['phone'],
                        $val['comments'],
                        $val['prefer_time'],
                        $remarkInputType,
                        $selectStatus,
                        $statusUpdate
                    );
                }
            }

            echo $this->jsonEncode($responce);
            exit;
        }
    }

    /**
     * This function is used  to update status of Advertise record 
     * Created By :  Bidhan
     * Date : 22 Aug,2014
     * @param void
     * @return void
     */
    public function advertiseupdatestatusAction() {
        $id = $this->getRequest()->getParam('id');
        $remarks = $this->getRequest()->getParam('remarks');
        $status = $this->getRequest()->getParam('status');
        if (($status != '0') && ($remarks != '') && ($id != '')) {
            $return = $this->_dashboardResource->updateAdvertiseStatusRecords($id, $remarks, $status);
            echo '1';
            die;
        } else {
            echo '0';
            die;
        }
    }

    /**
     * This function is used delete Advertise records
     * Created By :  Bidhan
     * Date : 23 Aug,2014
     * @param void
     * @return void
     */
    public function deladvertiseAction() {
        //dd($this->getRequest()->getParams());
        if ($ids = $this->getRequest()->getParam('advertiseDel_ids')) {
            //echo $id; die;
            $arrIds = explode(',', $ids);
            if ($del = $this->_dashboardResource->deleteAdvertiseRecords($arrIds)) {
                $this->_helper->FlashMessenger()->setNamespace('success')->addMessage("Records successfully deleted.");
                $this->_redirect(ADMIN_BASE_URL . 'dashboard/advertise');
            }
        }
    }

    /**
     * This function is used delete Newsletter records
     * Created By :  Bidhan
     * Date : 23 Aug,2014
     * @param void
     * @return void
     */
    public function delnewsletterAction() {
        if ($ids = $this->getRequest()->getParam('newsletterDel_ids')) {
            $arrIds = explode(',', $ids);
            if ($del = $this->_dashboardResource->deleteNewsletterRecords($arrIds)) {
                $this->_helper->FlashMessenger()->setNamespace('success')->addMessage("Records successfully deleted.");
                $this->_redirect(ADMIN_BASE_URL . 'dashboard/newsletter');
            }
        }
    }

    /**
     * This function is used  to fetch all Contact us record 
     * Created By :  Bidhan
     * Date : 27 Aug,2014
     * @param void
     * @return void
     */
    public function newsletterAction() {

        $request = $this->getRequest();
        global $preDate;

        if ($request->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $data = $this->getRequest()->getPost();
            $params = $this->getSearchParams($data);
            $params['fields']['main'] = array('*');
            $result = $this->_dashboardResource->fetchNewsletterRecords($params);
            $yesterDaydate = date(YESTERDAY_DATE_FORMAT, strtotime("-1 days"));
            $responce = new stdClass();
            $responce->page = $result['page'];
            $responce->total = $result['total'];
            $responce->records = $result['records'];
            $smsDate = '';
            foreach ($result['result'] as $k => $val) {
                $id = $val['id'];
                $responce->rows[$k]['id'] = $val['id'];

                $smsDate = $this->changeDateFormat($val['date'], DATEFORMAT, '');
                if (strtotime($smsDate) >= strtotime($yesterDaydate)) {
                    $responce->rows[$k]['cell'] = array(
                        "<strong>" . $smsDate . "</strong>",
                        "<strong>" . $val['email'] . "</strong>",
                    );
                } else {
                    $responce->rows[$k]['cell'] = array(
                        $smsDate,
                        $val['email'],
                    );
                }
            }
            echo $this->jsonEncode($responce);
            exit;
        }
    }

    /**
     * This function is used  to fetch all Contact us record 
     * Created By :  Bidhan
     * Date : 27 Aug,2014
     * @param void
     * @return void
     */
    public function contactusAction() {


        $request = $this->getRequest();
        global $contactUsStatus;

        if ($request->isXmlHttpRequest()) {

            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $data = $this->getRequest()->getPost();
            //dd($data);
            $params = $this->getSearchParams($data);
            //dd($params);
            $params['fields']['main'] = array('*');

            //dd($params);
            $result = $this->_dashboardResource->fetchAllContactUs($params);

            $responce = new stdClass();
            $responce->page = $result['page'];
            $responce->total = $result['total'];
            $responce->records = $result['records'];
            foreach ($result['result'] as $k => $val) {
                $selectStatus = '';
                $id = $val['id'];
                $remarks = $val['remarks'];
                $remarkInputType = "<input type='text' placeholder='Plz write your remark' value='$remarks' id='contactusRmk_$id' name='contactusRmk_$id'>";
                $selectStatus .= "<select id='contactusSts_$id' name='contactusSts_$id'>";
                foreach ($contactUsStatus as $key => $statusVal) {
                    $selected = '';

                    if ($key == $val['status']) {
                        $selected = 'selected = selected';
                    }
                    $selectStatus .= "<option value='$key' $selected> $statusVal </option>";
                }
                $selectStatus .="</select>";
                $statusUpdate = "<a onclick='return updateContactUsStatus($id);' href='javascript:void(0);'><img width='25' height='20' src='" . ADMIN_IMAGE_URL . "update.png' alt='Status Update' title='Status Update'></a>";
                $responce->rows[$k]['id'] = $val['id'];
                if ($val['remarks'] == '' && $val['status'] == 0) {
                    $responce->rows[$k]['cell'] = array(
                        "<strong>" . $this->changeDateFormat($val['created_at'], DATEFORMAT, "") . "</strong>",
                        "<strong>" . utf8_encode($val['name']) . "</strong>",
                        "<strong>" . $val['email'] . "</strong>",
                        "<strong>" . utf8_encode($val['message']) . "</strong>",
                        $remarkInputType,
                        $selectStatus,
                        $statusUpdate
                    );
                } else {
                    $responce->rows[$k]['cell'] = array(
                        $this->changeDateFormat($val['created_at'], DATEFORMAT, ""),
                        utf8_encode($val['name']),
                        $val['email'],
                        utf8_encode($val['message']),
                        $remarkInputType,
                        $selectStatus,
                        $statusUpdate
                    );
                }
            }
//dd($responce,true);

            echo json_encode($responce,JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
           // echo json_last_error();
            exit;
        }
    }

    /**
     * This function is used  to update status of Advertise record 
     * Created By :  Bidhan
     * Date : 22 Aug,2014
     * @param void
     * @return void
     */
    public function contactusupdatestatusAction() {
        $id = $this->getRequest()->getParam('id');
        $remarks = $this->getRequest()->getParam('remarks');
        $status = $this->getRequest()->getParam('status');
        if (($status != '0') && ($remarks != '') && ($id != '')) {
            $return = $this->_dashboardResource->updateContactUsStatusRecords($id, $remarks, $status);
            echo '1';
            die;
        } else {
            echo '0';
            die;
        }
    }

    /**
     * This function is used delete Advertise records
     * Created By :  Bidhan
     * Date : 23 Aug,2014
     * @param void
     * @return void
     */
    public function delcontactusAction() {
        //dd($this->getRequest()->getParams());
        if ($ids = $this->getRequest()->getParam('contactUsDel_ids')) {
            //echo $id; die;
            $arrIds = explode(',', $ids);
            if ($del = $this->_dashboardResource->deleteContactusRecords($arrIds)) {
                $this->_helper->FlashMessenger()->setNamespace('success')->addMessage("Records successfully deleted.");
                $this->_redirect(ADMIN_BASE_URL . 'dashboard/contactus');
            }
        }
    }

    /**
     * This function is used  to fetch all SMS/Email me records
     * Created By :  Bidhan
     * Modified By :  Bidhan
     * Date : 28 Aug,2014
     * Modified Date : 11 Oct,2014
     * @param void
     * @return void
     */
    public function smsemailmeAction() {

        $request = $this->getRequest();
        global $instaBookingStatus;;
        global $preDate;
        
        if ($request->getParam('sectionType')) {
            $this->view->sectionType = $sectionType = $request->getParam('sectionType');
        } 
        
        
        if ($request->isXmlHttpRequest()) {

            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $data = $this->getRequest()->getPost();
            $params = $this->getSearchParams($data);
            $params['fields']['main'] = array('*');
            
            
            
            $result = $this->_dashboardResource->fetchSmsEmailMeRecords($params);
            $yesterDaydate = date(YESTERDAY_DATE_FORMAT, strtotime("-1 days"));
            //$yesterDaydateObj = new Zend_Date($preDate, 'd-m-Y');             
            //echo $yesterDaydate = $yesterDaydateObj->get('d-m-Y'); die;                                    
            //echo date('d-m-Y',strtotime("-1 days")); die;            
            $responce = new stdClass();
            $responce->page = $result['page'];
            $responce->total = $result['total'];
            $responce->records = $result['records'];
            $smsDate = '';
            foreach ($result['result'] as $k => $val) {
                $selectStatus = '';
                $id = $val['id'];
                $vendorid = $val['vendorid'];
                /*
                  $userId = '';
                  $userName = '';
                  if($val['userid'] > 0){
                  $userId = $val['userid'];
                  $userName = "<a href = '/admin/user/viewinfo/id/$userId'>".$val['user_name']."</a>";
                  }else{
                  $userName = "";
                  }
                 */
                
                if ($val['vendor_email_status'] == 1) {
                    $vendorEmailStatus = 'Activated';
                } else {
                    $vendorEmailStatus = "Deactivated";
                }
                $remarks = $val['remarks'];
                
                $remarkInputType = "<input type='text' placeholder='Plz write your remark' value='$remarks' id='ieRmk_$id' name='ieRmk_$id' style = 'width:100px;'>";
                $selectStatus .= "<select id='ieSts_$id' name='ieSts_$id' style = 'width:96px;'>";
                foreach ($instaBookingStatus as $key => $statusVal) {
                    $selected = '';

                    if ($key == $val['status']) {
                        $selected = 'selected = selected';
                    }
                    $selectStatus .= "<option value='$key' $selected> $statusVal </option>";
                }
                $selectStatus .="</select>";
                $statusUpdate = "<a onclick='return updateInstaEnquiryStatus($id);' href='javascript:void(0);'><img width='25' height='20' src='" . ADMIN_IMAGE_URL . "update.png' alt='Status Update' title='Status Update'></a>";
                
                
                
                if ($val['vendor_email_status'] == 1) {
                    $vendorEmailStatus = 'Activated';
                } else {
                    $vendorEmailStatus = "Deactivated";
                }

                $responce->rows[$k]['id'] = $val['id'];
                
                $smsDateTime = $this->changeDateFormat($val['date'],DATEFORMAT, DATE_TIME_FORMAT);
                
                $smsDate = $this->changeDateFormat($val['date'], DATEFORMAT, '');
                if (strtotime($smsDate) >= strtotime($yesterDaydate)) {
                    
                    
                    $responce->rows[$k]['cell'] = array(
                        "<strong>" . $smsDateTime . "</strong>",
                        "<strong>" . $val['name'] . "</strong>",
                        "<strong>" . $val['email'] . "</strong>",
                        "<strong>" . $val['mobile'] . "</strong>",
                        "<strong><a href = '/admin/vendor/viewinfo/id/$vendorid'>" . $val['vendor_name'] . "</a></strong>",
                        "<strong>".$val['city_name'] ."</strong>",
                       "<strong>" . $vendorEmailStatus . "</strong>"
                    );
                } else {
                   
                    $responce->rows[$k]['cell'] = array(
                        $smsDateTime,
                        $val['name'],
                        $val['email'],
                        $val['mobile'],
                        "<a href = '/admin/vendor/viewinfo/id/$vendorid'>" . $val['vendor_name'] . '</a>',
                        $val['city_name'],                        
                        $vendorEmailStatus
                    );
                }
            }

            echo $this->jsonEncode($responce);
            exit;
        }
    }

    /**
     * This function is used  to fetch all event SMS/Email me records
     * Created By :  Bidhan
     * Modified By :  Bidhan
     * Date : 28 Aug,2014
     * Modified Date : 11 Oct,2014
     * @param void
     * @return void
     */
    public function eventsmsemailmeAction() {

        $request = $this->getRequest();
        global $contactUsStatus;
        global $preDate;
        if ($request->isXmlHttpRequest()) {

            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $data = $this->getRequest()->getPost();
            $params = $this->getSearchParams($data);
            $params['fields']['main'] = array('*');
            $result = $this->_dashboardResource->fetchEventSmsEmailMeRecords($params);
            $yesterDaydate = date(YESTERDAY_DATE_FORMAT, strtotime("-1 days"));
            $responce = new stdClass();
            $responce->page = $result['page'];
            $responce->total = $result['total'];
            $responce->records = $result['records'];
            $smsDate = '';
            foreach ($result['result'] as $k => $val) {
                $selectStatus = '';
                $eventid = $val['event_id'];
                $id = $val['id'];
                $responce->rows[$k]['id'] = $val['id'];

                $smsDate = $this->changeDateFormat($val['date'], DATEFORMAT, '');
                if (strtotime($smsDate) >= strtotime($yesterDaydate)) {
                    $responce->rows[$k]['cell'] = array(
                        "<strong>" . $smsDate . "</strong>",
                        "<strong><a href = '/admin/event/viewinfo/id/$eventid'>" . $val['event_name'] . "</a></strong>",
                        "<strong>" . $val['name'] . "</strong>",
                        "<strong>" . $val['email'] . "</strong>",
                        "<strong>" . $val['mobile'] . "</strong>",
                        "<strong>" . $val['user_name'] . "</strong>"
                    );
                } else {
                    $responce->rows[$k]['cell'] = array(
                        $smsDate,
                        "<a href = '/admin/event/viewinfo/id/$eventid'>" . $val['event_name'] . "</a>",
                        $val['name'],
                        $val['email'],
                        $val['mobile'],
                        $val['user_name']
                    );
                }
            }

            echo $this->jsonEncode($responce);
            exit;
        }
    }

    /**
     * This function is used  to update status of SMS/Email me records 
     * Created By :  Bidhan
     * Date : 28 Aug,2014
     * @param void
     * @return void
     */
    public function smsemailmestatusAction() {
        $id = $this->getRequest()->getParam('id');
        $remarks = $this->getRequest()->getParam('remarks');
        $status = $this->getRequest()->getParam('status');
        if (($status != '0') && ($remarks != '') && ($id != '')) {
            $return = $this->_dashboardResource->updateSmsEmailMeStatusRecords($id, $remarks, $status);
            echo '1';
            die;
        } else {
            echo '0';
            die;
        }
    }

    /**
     * This function is used delete SMS/Email me records
     * Created By :  Bidhan
     * Date : 28 Aug,2014
     * @param void
     * @return void
     */
    public function delsmsemailmeAction() {
        //dd($this->getRequest()->getParams());
        if ($ids = $this->getRequest()->getParam('smsEmailMeDel_ids')) {
            //echo $id; die;
            $arrIds = explode(',', $ids);
            if ($del = $this->_dashboardResource->deleteSmsEmailMeRecords($arrIds)) {
                $this->_helper->FlashMessenger()->setNamespace('success')->addMessage("Records successfully deleted.");
                $this->_redirect(ADMIN_BASE_URL . 'dashboard/smsemailme');
            }
        }
    }

    /**
     * This function is used delete event SMS/Email me records
     * Created By :  Bidhan
     * Date : 28 Aug,2014
     * @param void
     * @return void
     */
    public function deleventsmsemailmeAction() {
        if ($ids = $this->getRequest()->getParam('eventsmsEmailMeDel_ids')) {
            $arrIds = explode(',', $ids);
            if ($del = $this->_dashboardResource->deleteEventSmsEmailMeRecords($arrIds)) {
                $this->_helper->FlashMessenger()->setNamespace('success')->addMessage("Records successfully deleted.");
                $this->_redirect(ADMIN_BASE_URL . 'dashboard/eventsmsemailme');
            }
        }
    }

    /**
     * This function is used  to fetch all Quick Quote records
     * Created By :  Bidhan
     * Modified By :  
     * Created Date : 11 Nov,2014
     * Modified Date : 
     * @param void
     * @return void
     */
    public function quickquotesendenquiryAction() {
        // die;
        $request = $this->getRequest();
        global $sendEnquiryStatus;
        global $preDate;

        $this->view->sectionType = $request->getParam('sectionType');
        if ($request->isXmlHttpRequest()) {

            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $data = $request->getPost();
            $params = $this->getSearchParams($data);
            $params['fields']['main'] = array('id','vendorid','name','mobile','email','date','service_names','status','remarks');
            //$params['fields']['main'] = array('*');
            $params['condition']['section_type'] = $request->getParam('sectionType');
            $params['condition']['serviceCount'] = true;
            $params['sidx'] = 'id';
            $params['sord'] = 'DESC';
            $result = $this->_dashboardResource->fetchQuickQuoteSendEnqRecords($params);
            $yesterDaydate = date(YESTERDAY_DATE_FORMAT, strtotime("-1 days"));
            //dd($result);
            //$yesterDaydateObj = new Zend_Date($preDate, 'd-m-Y');             
            //echo $yesterDaydate = $yesterDaydateObj->get('d-m-Y'); die;                                    
            //echo date('d-m-Y',strtotime("-1 days")); die;            
            $responce = new stdClass();
            $responce->page = $result['page'];
            if(!isset($result['totalRecordsCount'])){
                $result['totalRecordsCount'] = 0;
            }
            $responce->total = ceil($result['totalRecordsCount'] / NO_OF_RECORDS_PER_PAGE);
            $responce->records = $result['totalRecordsCount'];
            $smsDate = '';
            //$enquiredParams['fields']['main'] = array('vendorid','service_id','section_type');
            //$enquiredParams['sidx'] = 'mv.name';
            //$enquiredParams['sord'] = 'DESC';
            foreach ($result['result'] as $k => $val) {
                $selectStatus = '';
                $id = $val['id'];
                $vendorid = $val['vendorid'];
                $userId = '';

                $responce->rows[$k]['id'] = $val['id'];

                $smsDate = $this->changeDateFormat($val['date'], DATEFORMAT, '');
                
                $remarks = $val['remarks'];
                
                $remarkInputType = "<input type='text' placeholder='Plz write your remark' value='$remarks' id='seRmk_$id' name='seRmk_$id' style = 'width:100px;'>";
                $selectStatus .= "<select id='seSts_$id' name='seSts_$id' style = 'width:96px;'>";
                foreach ($sendEnquiryStatus as $key => $statusVal) {
                    $selected = '';

                    if ($key == $val['status']) {
                        $selected = 'selected = selected';
                    }
                    $selectStatus .= "<option value='$key' $selected> $statusVal </option>";
                }
                $selectStatus .="</select>";
                $statusUpdate = "<a onclick='return updateSEStatus($id);' href='javascript:void(0);'><img width='25' height='20' src='" . ADMIN_IMAGE_URL . "update.png' alt='Status Update' title='Status Update'></a>";

                if (strtotime($smsDate) >= strtotime($yesterDaydate)) {

                    if ($params['condition']['section_type'] == 2) {

                        $responce->rows[$k]['cell'] = array(
                            
                            "<strong>" . $smsDate . "</strong>",
                            "<strong>" . $val['name'] . "</strong>",
                            "<strong>" . $val['email'] . "</strong>",
                            "<strong>" . $val['mobile'] . "</strong>",
                            "<strong>" . $val['vendor_name'] . "</strong>",
                            "<strong>" . $val['category_name'] . "</strong>",
                            "<strong>" . $val['city_name'] . "</strong>",
                            "<strong>" . $val['zone_name'] . "</strong>",
                            $remarkInputType,
                            $selectStatus,
                            $statusUpdate,
                        );
                    } else {

                        $responce->rows[$k]['cell'] = array(
                            
                            "<strong>" . $smsDate . "</strong>",
                            "<strong>" . $val['name'] . "</strong>",
                            "<strong>" . $val['email'] . "</strong>",
                            "<strong>" . $val['mobile'] . "</strong>",
                            "<strong>" . $val['vendor_name'] . "</strong>",
                            "<strong>" . $val['category_name'] . "</strong>",
                            "<strong>" . $val['service_names'] . "</strong>",
                            "<strong>" . $val['city_name'] . "</strong>",
                            "<strong>" . $val['zone_name'] . "</strong>",
                            $remarkInputType,
                            $selectStatus,
                            $statusUpdate,
                        );
                    }
                } else {

                    if ($params['condition']['section_type'] == 2) {
                        $responce->rows[$k]['cell'] = array(
                            
                            $smsDate,
                            $val['name'],
                            $val['email'],
                            $val['mobile'],
                            $val['vendor_name'],
                            $val['category_name'],
                            $val['city_name'],
                            $val['zone_name'],
                            $remarkInputType,
                            $selectStatus,
                            $statusUpdate,
                        );
                    } else {
                        $responce->rows[$k]['cell'] = array(
                            
                            $smsDate,
                            $val['name'],
                            $val['email'],
                            $val['mobile'],
                            $val['vendor_name'],
                            $val['category_name'],
                            $val['service_names'],
                            $val['city_name'],
                            $val['zone_name'],
                            $remarkInputType,
                            $selectStatus,
                            $statusUpdate,
                        );
                    }
                }
            }

            echo $this->jsonEncode($responce);
            exit;
        }
    }

    /**
     * This function is used delete SMS/Email me records
     * Created By :  Bidhan
     * Date : 28 Aug,2014
     * @param void
     * @return void
     */
    public function delquickquoteAction() {
        //dd($this->getRequest()->getParams());
        if ($ids = $this->getRequest()->getParam('quickQuoteDel_ids')) {
            //echo $id; die;
            $arrIds = explode(',', $ids);
            $sectionType = $this->getRequest()->getParam('sectionType');
            $params['ids'] = $arrIds;
            $params['section_type'] = $sectionType;
            if ($del = $this->_dashboardResource->deleteQuickQuoteRecords($params)) {
                $this->_helper->FlashMessenger()->setNamespace('success')->addMessage("Records successfully deleted.");
                $this->_redirect(ADMIN_BASE_URL . 'dashboard/quickquotesendenquiry/sectionType/' . $sectionType);
            }
        }
    }

    /**
     * This function is used to find loged in user records 
     * Created By :  JItendra
     * Date :07 Nov,2014
     * @param void
     * @return void
     */
    public function loginrecordAction() {




        $request = $this->getRequest();
        global $contactUsStatus;

        if ($request->isXmlHttpRequest()) {

            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $data = $this->getRequest()->getPost();
            //dd($data);
            $params = $this->getSearchParams($data);
            //dd($params);
            $params['fields']['main'] = array('*');

            //dd($params);
            $result = $this->_dashboardResource->fetchLoginLogoutRecords($params);

            //  echo '<pre>'; print_r($result); die;			

            $responce = new stdClass();
            $responce->page = $result['page'];
            $responce->total = $result['total'];
            $responce->records = $result['records'];
            foreach ($result['result'] as $k => $val) {
                $responce->rows[$k]['id'] = $val['id'];

                /* $responce->rows[$k]['cell'] = array(                  
                  $this->changeDateFormat($val['login_time'],DATEFORMAT,"")
                  ); */

                $responce->rows[$k]['cell'] = array(
                    $this->changeDateFormat($val['login_time'], DATEFORMAT, ""),
                    $val['name'],
                    $val['email']
                );
            }

            echo $this->jsonEncode($responce);
            exit;
        }
    }

    /**
     * This function is used delete loged in user 
     * Created By :  JItendra
     * Date :07 Nov,2014
     * @param void
     * @return void
     */
    public function delloginrecordsAction() {
        // dd($this->getRequest()->getParams());
        if ($ids = $this->getRequest()->getParam('loginRecordsDel_ids')) {
            //echo $id; die;
            $arrIds = explode(',', $ids);
            if ($del = $this->_dashboardResource->deleteLogedinRecords($arrIds)) {
                $this->_helper->FlashMessenger()->setNamespace('success')->addMessage("Records successfully deleted.");
                $this->_redirect(ADMIN_BASE_URL . 'dashboard/loginrecord');
            }
        }
    }

    /**
     * This function is used to find new sign up user records 
     * Created By :  JItendra
     * Date :08 Nov,2014
     * @param void
     * @return void
     */
    public function registerrecordAction() {

        $request = $this->getRequest();
        global $contactUsStatus;
        if ($request->isXmlHttpRequest()) {

            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $data = $this->getRequest()->getPost();
            $params = $this->getSearchParams($data);
            $params['fields']['main'] = array('*');
            $result = $this->_dashboardResource->fetchRegisteredUserRecords($params);
            //echo '<pre>'; print_r($result); die;
            $responce = new stdClass();
            $responce->page = $result['page'];
            $responce->total = $result['total'];
            $responce->records = $result['records'];
            foreach ($result['result'] as $k => $val) {
                $responce->rows[$k]['id'] = $val['id'];
                if ($val['created_date'] == '') {
                    $val['created_date'] = DEFAULT_DATE;
                }
                $responce->rows[$k]['cell'] = array(
                    $this->changeDateFormat($val['created_date'], DATEFORMAT, ""),
                    $val['name'],
                    $val['email'],
                    $val['registered_from_page']
                );
            }
            echo $this->jsonEncode($responce);
            exit;
        }
    }

    /**
     * This function is used delete register user 
     * Created By :  JItendra
     * Date :07 Nov,2014
     * @param void
     * @return void
     */
    public function delregrecordsAction() {
        // dd($this->getRequest()->getParams());
        if ($ids = $this->getRequest()->getParam('registerRecordsDel_ids')) {
            //echo $id; die;
            $arrIds = explode(',', $ids);
            if ($del = $this->_dashboardResource->deleteUserRegisterRecords($arrIds)) {
                $this->_helper->FlashMessenger()->setNamespace('success')->addMessage("Records successfully deleted.");
                $this->_redirect(ADMIN_BASE_URL . 'dashboard/registerrecord');
            }
        }
    }

    protected function convertArrayString($data = array(), $fieldName, $linkfieldName) {

        $vendorStr = '';
        if (!empty($data)) {

            foreach ($data as $key => $val) {
                $vendorid = $val[$linkfieldName];
                $vendorStr.= "<a class = 'a-underline' href = '/admin/vendor/viewinfo/id/$vendorid'>" . $val[$fieldName] . "</a>";
                $vendorStr.= ', ';
            }
            return rtrim($vendorStr, ', ');
        }
    }

    /**
     * This function is used to find website activated records 
     * Created By :  JItendra
     * Date :28 Nov,2014
     * @param void
     * @return void
     */
    public function websiteactivatedAction() {

        $request = $this->getRequest();
        global $contactUsStatus;
        if ($request->isXmlHttpRequest()) {

            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $data = $this->getRequest()->getPost();
            $params = $this->getSearchParams($data);
            $params['fields']['main'] = array('*');
            $result = $this->_dashboardResource->fetchWebsiteActivatedRecords($params);

            //echo '<pre>'; print_r($result); die;
            $responce = new stdClass();
            $responce->page = $result['page'];
            $responce->total = $result['total'];
            $responce->records = $result['records'];
            foreach ($result['result'] as $k => $val) {
                $responce->rows[$k]['id'] = $val['id'];
                if ($val['log_time'] == '') {
                    $val['log_time'] = DEFAULT_DATE;
                }
                $responce->rows[$k]['cell'] = array(
                    $this->changeDateFormat($val['log_time'], DATEFORMAT, ""),
                    $val['name'],
                    $val['email'],
                    $val['log_page']
                );
            }
            echo $this->jsonEncode($responce);
            exit;
        }
    }

    /**
     * This function is used delete website activated 
     * Created By :  JItendra
     * Date :28 Nov,2014
     * @param void
     * @return void
     */
    public function delwebsiteactivatedAction() {
        // dd($this->getRequest()->getParams());
        if ($ids = $this->getRequest()->getParam('websitecreateddel_ids')) {
            //echo $id; die;
            $arrIds = explode(',', $ids);
            if ($del = $this->_dashboardResource->deleteWebsiteCreatedRecords($arrIds)) {
                $this->_helper->FlashMessenger()->setNamespace('success')->addMessage("Records successfully deleted.");
                $this->_redirect(ADMIN_BASE_URL . 'dashboard/websiteactivated');
            }
        }
    }

    /**
     * This function is used to find planning activated records 
     * Created By :  JItendra
     * Date :28 Nov,2014
     * @param void
     * @return void
     */
    public function planningactivatedAction() {

        $request = $this->getRequest();
        global $contactUsStatus;
        if ($request->isXmlHttpRequest()) {

            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $data = $this->getRequest()->getPost();
            $params = $this->getSearchParams($data);
            $params['fields']['main'] = array('*');
            $result = $this->_dashboardResource->fetchPlanningActivatedRecords($params);

            //echo '<pre>'; print_r($result); die;
            $responce = new stdClass();
            $responce->page = $result['page'];
            $responce->total = $result['total'];
            $responce->records = $result['records'];
            foreach ($result['result'] as $k => $val) {
                $responce->rows[$k]['id'] = $val['id'];
                if ($val['log_time'] == '') {
                    $val['log_time'] = DEFAULT_DATE;
                }
                $responce->rows[$k]['cell'] = array(
                    $this->changeDateFormat($val['log_time'], DATEFORMAT, ""),
                    $val['name'],
                    $val['email'],
                    $val['log_page']
                );
            }
            echo $this->jsonEncode($responce);
            exit;
        }
    }

    /**
     * This function is used delete Planning activated 
     * Created By :  JItendra
     * Date :28 Nov,2014
     * @param void
     * @return void
     */
    public function delplanningactivatedAction() {
        // dd($this->getRequest()->getParams());
        if ($ids = $this->getRequest()->getParam('planningcreateddel_ids')) {
            //echo $id; die;
            $arrIds = explode(',', $ids);
            if ($del = $this->_dashboardResource->deletePlanningCreatedRecords($arrIds)) {
                $this->_helper->FlashMessenger()->setNamespace('success')->addMessage("Records successfully deleted.");
                $this->_redirect(ADMIN_BASE_URL . 'dashboard/planningactivated');
            }
        }
    }
    
    
    
     /**
     * This function is used to display downloaded deals
     * Created By :  Bidhan
     * Date : 9 Dec,2015
     * @param void
     * @return void
     */
    
    public function dealDownloadAction() {


        $request = $this->getRequest();
        global $dealsBusiness;

        if ($request->isXmlHttpRequest()) {

            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $data = $this->getRequest()->getPost();
            //dd($data);
            $params = $this->getSearchParams($data);
            $params['fields']['main'] = array('*');
            //$params['condition']['status'] = 0;
            //dd($params);
            $result = $this->_dashboardResource->dealDownloaded($params);
            //dd($result);
            $responce = new stdClass();
            $responce->page = $result['page'];
            $responce->total = $result['total'];
            $responce->records = $result['records'];
            $yesterDaydate = date(YESTERDAY_DATE_FORMAT, strtotime("-1 days"));
            foreach ($result['result'] as $k => $val) {
                $selectStatus = '';
                $id = $val['id'];
                //$remarks = $val['remarks'];
                //$remarkInputType = "<input type='text' placeholder='Plz write your remark' value='$remarks' id='dealRmk_$id' name='dealRmk_$id'>";
                /*$selectStatus .= "<select id='dealSts_$id' name='dealSts_$id'>";
                foreach ($dealsBusiness as $key => $statusVal) {
                    $selected = '';

                    if ($key == $val['status']) {
                        $selected = 'selected = selected';
                    }
                    $selectStatus .= "<option value='$key' $selected> $statusVal </option>";
                }
                $selectStatus .="</select>";*/
                //$statusUpdate = "<a onclick='return updateDealstatus($id);' href='javascript:void(0);'><img width='25' height='20' src='" . ADMIN_IMAGE_URL . "update.png' alt='Status Update' title='Status Update'></a>";
                $dealDate = $this->changeDateFormat($val['created_at'], DATEFORMAT, '');
                
                $responce->rows[$k]['id'] = $val['id'];
                
                if (strtotime($dealDate) >= strtotime($yesterDaydate)) {
                    $responce->rows[$k]['cell'] = array(
                        "<strong>" . $this->changeDateFormat($val['created_at'], DATEFORMAT, "") . "</strong>",
                        "<strong>" . $val['name'] . "</strong>",
                        "<strong>" . $val['mobile_no'] . "</strong>",
                        "<strong>" . $val['email'] . "</strong>",
                        "<strong>" . $val['deals'] . "</strong>",
                        "<strong>" . $val['businessname'] . "</strong>",
                        "<strong>" . $val['city_name']."</strong>",
                        "<strong>" . $val['catname'] . "</strong>"
                    );
                } else {
                    $responce->rows[$k]['cell'] = array(
                        $this->changeDateFormat($val['created_at'], DATEFORMAT, ""),
                        $val['name'],
                        $val['mobile_no'],
                        $val['email'],
                        $val['deals'],
                        $val['businessname'],
                        $val['city_name'],
                        $val['catname']
                    );
                }
            }

            echo $this->jsonEncode($responce);
            exit;
        }
    }
    
    /**
     * This function is used  to delete downloaded deals	
     * Created By :  Bidhan
     * Date : 10 Dec,2015
     * @param void
     * @return void
     */
    public function deletedowloadeddealAction() {
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->getRequest()->getParam('del_d_id');
        if (!empty($id)) {           
            $this->_dashboardResource->deleteDownloadedDealRecords($id);
        }
        $this->_helper->FlashMessenger()->setNamespace('success')->addMessage("Records successfully deleted.");
        $this->_redirect("admin/dashboard/deal-download");
    }
    
    /**
     * This function is used  to fetch all Quick Quote records
     * Created By :  Bidhan
     * Modified By :  
     * Created Date : 11 Nov,2014
     * Modified Date : 
     * @param void
     * @return void
     */
    public function sendenquiryphotogallaryAction() {
        // die;
        $request = $this->getRequest();
        global $contactUsStatus;
        global $preDate;

        $this->view->sectionType = $request->getParam('sectionType');
        if ($request->isXmlHttpRequest()) {

            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $data = $request->getPost();
            $params = $this->getSearchParams($data);
            $sectionType = $request->getParam('sectionType');
            $params['fields']['main'] = array('*');
            $params['condition']['section_type'] = $sectionType;
             $params['condition']['serviceCount'] = true;           
            $params['sidx'] = 'id';
            $params['sord'] = 'DESC';
            $result = $this->_dashboardResource->fetchQuickQuoteSendEnqRecords($params);
            
            $yesterDaydate = date(YESTERDAY_DATE_FORMAT, strtotime("-1 days"));
            //dd($result);
            //$yesterDaydateObj = new Zend_Date($preDate, 'd-m-Y');             
            //echo $yesterDaydate = $yesterDaydateObj->get('d-m-Y'); die;                                    
            //echo date('d-m-Y',strtotime("-1 days")); die;            
            $responce = new stdClass();
            $responce->page = $result['page'];
            if(!isset($result['totalRecordsCount'])){
                $result['totalRecordsCount'] = 0;
            }
            $responce->total = ceil($result['totalRecordsCount'] / NO_OF_RECORDS_PER_PAGE);
            $responce->records = $result['totalRecordsCount'];
            $smsDate = '';
            //$enquiredParams['fields']['main'] = array('vendorid','service_id','section_type');
            //$enquiredParams['sidx'] = 'mv.name';
            //$enquiredParams['sord'] = 'DESC';
            foreach ($result['result'] as $k => $val) {
                $selectStatus = '';
                $id = $val['id'];
                $vendorid = $val['vendorid'];
                $userId = '';

                $responce->rows[$k]['id'] = $val['id'];
                
                $smsDate = $this->changeDateFormat($val['date'], DATEFORMAT, '');
                //$smsDate = $val['date'];

                //$enquiredParams['condition']['enquiry_no'] = $val['enquiry_no'];
                /*
                  if(!empty($val['service_id'])){
                  $enquiredParams['condition']['service_id'] = $val['service_id'];
                  }
                 */
                //$enquiredVendors = $this->_dashboardResource->getEnquiredVendors($enquiredParams);
                //$enquiredVendorNames = $this->convertArrayString($enquiredVendors,'vendor_name','vendorid');
                
                
                $secName = '';
                 
                if(strpos($val['enquiry_no'], 'ins_') === 0){
                    
                    $secName = 'Wedding Inspiration';
                }else if(strpos($val['enquiry_no'], 'real_') === 0){
                    
                  $secName = 'Real Wedding';
                }
              
                if (strtotime($smsDate) >= strtotime($yesterDaydate)) {

                    if ($params['condition']['section_type'] == 3) {

                        $responce->rows[$k]['cell'] = array(
                            "<strong>" . $id . "</strong>",
                            "<strong>" . $smsDate . "</strong>",
                            "<strong>" . $val['name'] . "</strong>",
                            "<strong>" . $val['email'] . "</strong>",
                            "<strong>" . $val['mobile'] . "</strong>",
                            "<strong>" . $val['vendor_name'] . "</strong>",
                            "<strong>" . $val['category_name'] . "</strong>",
                            "<strong>" . $val['city_name'] . "</strong>",
                            "<strong>" . $secName . "</strong>",
                        );
                    }
                } else {

                    if ($params['condition']['section_type'] == 3) {
                        $responce->rows[$k]['cell'] = array(
                            $id,
                            $smsDate,
                            $val['name'],
                            $val['email'],
                            $val['mobile'],
                            $val['vendor_name'],
                            $val['category_name'],
                            $val['city_name'],
                            $secName,
                        );
                    } 
                }
            }
           
            echo $this->jsonEncode($responce);
            exit;
        }
    }
    
    public function instabookingAction() {
        global $instaBookingStatus;
        $params['count'] = '';
        $request = $this->getRequest();
        
        if ($request->isXmlHttpRequest()) {

            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $data = $this->getRequest()->getPost();
            //dd($data);
            $params = $this->getSearchParams($data);
     
            $params['fields']['main'] = array('id','insta_booking_no', 'name','email', 'contact_no','status','remarks','created_at');
            
            $result = $this->_dashboardResource->instaBookingUserData($params);
            //dd($result['result']);
            $responce = new stdClass();
            $responce->page = $result['page'];
            $responce->total = $result['total'];
            $responce->records = $result['records'];
            foreach ($result['result'] as $k => $val) {
                $selectStatus = '';
                $userName = '';
                $userEmail = '';
                $userContactNo = '';
                
                
                if (!empty($val['user_id'])){
                    
                    $userId = $val['user_id'];
                    
                    $userName = "<a class = 'a-tag' href='/admin/user/addedit/id/$userId'>".$val['user_name']."</a>";              
                    $userEmail = $val['user_email'];
                    
                    if(!empty($val['user_phone'])){
                        $userContactNo = $val['user_phone'];                        
                    }else{
                        $userContactNo = $val['contact_no'];
                    }
                    
                    
                    
                }else{
                    $userName = $val['name'];                    
                    $userEmail = $val['email'];                    
                    if(!empty($val['contact_no'])){
                        $userContactNo = $val['contact_no'];                        
                    }                    
                }
                
                $id = $val['id'];                
                $vendorId = $val['vendor_id'];                
                $vendorName = "<a href='/admin/vendor/viewinfo/id/$vendorId'>" . $val['vendor_name'] . "</a>";
                
                $responce->rows[$k]['id'] = $val['id'];
                $remarks = $val['remarks'];
                
                $remarkInputType = "<input type='text' placeholder='Plz write your remark' value='$remarks' id='ibRmk_$id' name='ibRmk_$id' style = 'width:100px;'>";
                $selectStatus .= "<select id='ibSts_$id' name='ibSts_$id' style = 'width:96px;'>";
                foreach ($instaBookingStatus as $key => $statusVal) {
                    $selected = '';

                    if ($key == $val['status']) {
                        $selected = 'selected = selected';
                    }
                    $selectStatus .= "<option value='$key' $selected> $statusVal </option>";
                }
                $selectStatus .="</select>";
                $statusUpdate = "<a onclick='return updateInstabookingStatus($id);' href='javascript:void(0);'><img width='25' height='20' src='" . ADMIN_IMAGE_URL . "update.png' alt='Status Update' title='Status Update'></a>";
                
                $createdModifiedDate = '';
                
                //$createdModifiedDate = $this->changeDateFormat($val['created_at'], DATEFORMAT, "");
                $responce->rows[$k]['cell'] = array(
                        
                        //$id,
                        "<a class = 'a-tag' href='/admin/vendor/viewinstabookinginfo/id/".$val['insta_booking_no']."'>" . $val['insta_booking_no'] . "</a>",
                        
                        $val['created_at'],
                        $userEmail,
                        $userContactNo,   
                        $userName,                                                                  
                        $vendorName,
                        $val['cat_name'],
                        $val['city_name'],
                        $remarkInputType,
                        $selectStatus,
                        $statusUpdate
                    );
            }

            echo $this->jsonEncode($responce);
            exit;
        }
    }
    
    /**
     * This function is used  something wrong page
     * Created By :  Umesh
     * Date : 3 Jun,2014
     * @param void
     * @return void
     */
    public function dealexpiryalertAction() {


        $request = $this->getRequest();
        global $dealsExpire;
        global $nowDate;
        if ($request->isXmlHttpRequest()) {

            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $data = $this->getRequest()->getPost();
            //dd($data);
            $params = $this->getSearchParams($data);
            $params['fields']['main'] = array('*');
            //$params['condition']['status'] = 0;
            //dd($params);
            //$result = $this->_dashboardResource->dealRequested($params);
            $result = $this->_dashboardResource->allvendordeals($params);
            //dd($result);
            $responce = new stdClass();
            $responce->page = $result['page'];
            $responce->total = $result['total'];
            $responce->records = $result['records'];
            foreach ($result['result'] as $k => $val) {
                $selectStatus = '';
                $id = $val['id'];
                $remarks = $val['remarks'];
                $remarkInputType = "<input type='text' placeholder='Plz write your remark' value='$remarks' id='dealExRmk_$id' name='dealExRmk_$id'>";
                $selectStatus .= "<select id='dealExSts_$id' name='dealExSts_$id'>";
                foreach ($dealsExpire as $key => $statusVal) {
                    $selected = '';

                    if ($key == $val['status']) {
                        $selected = 'selected = selected';
                    }
                    $selectStatus .= "<option value='$key' $selected> $statusVal </option>";
                }
                $selectStatus .="</select>";
                $statusUpdate = "<a onclick='return updateDealExpirystatus($id);' href='javascript:void(0);'><img width='25' height='20' src='" . ADMIN_IMAGE_URL . "update.png' alt='Status Update' title='Status Update'></a>";
                $responce->rows[$k]['id'] = $val['id'];
                
                $startDate = '';
                if($val['start_date'] != '0000-00-00'){
                    $startDate = $this->changeDateFormat($val['start_date'], DATEFORMAT, '');
                }
                
                $endDate = '';
                if($val['end_date'] != '0000-00-00'){
                    $endDate = $this->changeDateFormat($val['end_date'], DATEFORMAT, '');
                }
                
                
                if ((strtotime($nowDate) >= strtotime($endDate)) && ($val['status'] == 1) && ($endDate!='')) {                
                
                    $responce->rows[$k]['cell'] = array(
                        
                        "<strong>" . $val['businessname'] . "</strong>",
                        "<strong>" . $val['city_name'] . "</strong>",
                        "<strong>" . $val['catname'] . "</strong>",
                        "<strong>" . $val['deals'] . "</strong>",
                        "<strong>" . $startDate. "</strong>",
                        "<strong>" . $endDate . "</strong>",
                        $remarkInputType,
                        $selectStatus,
                        $statusUpdate
                    );
                } else {
                    $responce->rows[$k]['cell'] = array(
                        
                        $val['businessname'],
                        $val['city_name'],
                        $val['catname'],
                        $val['deals'],
                        $startDate,
                        $endDate,                        
                        $remarkInputType,
                        $selectStatus,
                        $statusUpdate
                    );
                }
            }

            echo $this->jsonEncode($responce);
            exit;
        }
    }
    
    /**
     * This function is used  to set status in vendor search
     * Created By :  Umesh
     * Date : 3 Jun,2014
     * @param void
     * @return void
     */
    public function dealsexpiryupdateAction() {
        $id = $this->getRequest()->getParam('id');
        $remarks = $this->getRequest()->getParam('remarks');
        $status = $this->getRequest()->getParam('status');
        if (($status == 0 || $status == 1) && ($remarks != '') && ($id != '')) {
            $return = $this->_dashboardResource->updateVendorDealsStatus($id, $remarks, $status);
            echo '1';
            die;
        } else {
            echo '0';
            die;
        }
    }
    
    
    /**
     * This function is used  to update status of Send enquiry record 
     * Created By :  Bidhan
     * Date : 30 Jun,2016
     * @param void
     * @return void
     */
    public function sendenquiryupdatestatusAction() {
        $id = $this->getRequest()->getParam('id');
        $remarks = $this->getRequest()->getParam('remarks');
        $status = $this->getRequest()->getParam('status');
        if (($status != '0') && ($remarks != '') && ($id != '')) {
            global $nowDateTime;
            $toBeUpdated['id'] = $id;
            $toBeUpdated['status'] = $status;
            $toBeUpdated['remarks'] = $remarks;
            $toBeUpdated['updated_at'] = $nowDateTime;
            $toBeUpdated['updated_by'] = $this->session->loginId;            
            $return = $this->_dashboardResource->updateSEStatusRecords($toBeUpdated);
            echo '1';
            die;
        } else {
            echo '0';
            die;
        }
    }
    
    
    public function adminCheckEditAction() {

        $id = $this->getRequest()->getParam('bid');
        $business_type = $this->getRequest()->getParam('business_type');
        if (isset($business_type)) {
            $this->view->business_type = $business_type = $this->getRequest()->getParam('business_type');
            $this->view->businessTypeUrl = 'business_type/' . $business_type;
        } else {
            $this->view->businessEdited = $businessEdited = $this->getRequest()->getParam('businessEdited');
            $this->view->businessTypeUrl = 'businessEdited/' . $businessEdited;
        }
        $this->view->return_type = $this->getRequest()->getParam('returnType');
        if (!empty($id)) {
            global $vendorsFieldArr;
            global $paymentMode;
            $this->view->allcity = $this->_claimbusinessModal->allcity();
            $updateVendorDetails = $claim_business_details = $this->_claimbusinessModal->claimbusinessdetail($id);
            $VendorDetails = $this->_claimbusinessModal->businessdetail($claim_business_details[0]['vendor_id']);
            $this->view->updateVendorDetails = $updateVendorDetails[0];
            $this->view->VendorDetails = $VendorDetails[0];
            $this->view->showFields = $vendorsFieldArr;
            $claimedVendorDeals = $this->_claimbusinessModal->getClaimedVendorDeals($id);
            /*******************************Extra field *******************************/
            $claimedVendorExtras = $this->_claimbusinessModal->getClaimedExtraFieldValues($id);
            $arrClaimedVendorExtras = array();
            foreach($claimedVendorExtras as $key=>$claimedVendorExtra){
                $arrClaimedVendorExtras[$claimedVendorExtra['field_id']] = $claimedVendorExtra;
            }           
            $this->view->claimedVendorExtra = $arrClaimedVendorExtras;
            
            
            $VendorExtras = $this->_claimbusinessModal->getExtraFieldValues($claim_business_details[0]['vendor_id']);
            $arrVendorExtras = array();            
            foreach($VendorExtras as $key=>$VendorExtra){
                $arrVendorExtras[$VendorExtra['field_id']] = $VendorExtra;
            }
            $this->view->VendorExtra = $arrVendorExtras;
            
            
            
            $allExtraFlds = $this->_categoryResource->categoryExtraFields($VendorDetails[0]['category']);            
            $arrAllExtraFlds = array();            
            foreach($allExtraFlds as $key=>$allExtraFld){
                $arrAllExtraFlds[$allExtraFld['id']] = $allExtraFld;
            }
            $this->view->allExtraFlds = $arrAllExtraFlds;
            
            /***************************[END]Extra field*****************************/
            
            
            $VendorDeals = $this->_claimbusinessModal->getVendorDeals($claim_business_details[0]['vendor_id']);            
            $this->view->getService = $this->_claimbusinessModal->getCategoryServiceTags($claim_business_details[0]['category']);
            $this->view->claimedVendorDeals = $claimedVendorDeals;
            $this->view->VendorDeals = $VendorDeals;
            $this->view->claimedVendorDeals = $claimedVendorDeals;
            
            
            $this->view->id = $id;
            $this->view->paymentMode = $paymentMode;
            
            $params['remarks'] = 'Seen by admin';
            $params['status'] = 1;
            $params['businessEdited'] = 1;
            $params['id'] = $id;
            
            $this->_claimbusinessModal->updateRemark($params);
        }

        $this->view->categories = $this->_vendorResource->fetchCategories();
        $this->view->location = $this->_vendorResource->fetchLocation();
    }
    
    
    public function priceenquiryAction() {

        $params['count'] = '';
        $request = $this->getRequest();
        
        global $instaBookingStatus;;
        global $preDate;
        if ($request->isXmlHttpRequest()) {

            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $data = $this->getRequest()->getPost();
            $params = $this->getSearchParams($data);
            $params['fields']['main'] = array('*');
            $params['condition']['section'] = 1;
            
            
            $result = $this->_dashboardResource->fetchSmsEmailMeRecords($params);
            $yesterDaydate = date(YESTERDAY_DATE_FORMAT, strtotime("-1 days"));
            //$yesterDaydateObj = new Zend_Date($preDate, 'd-m-Y');             
            //echo $yesterDaydate = $yesterDaydateObj->get('d-m-Y'); die;                                    
            //echo date('d-m-Y',strtotime("-1 days")); die;            
            $responce = new stdClass();
            $responce->page = $result['page'];
            $responce->total = $result['total'];
            $responce->records = $result['records'];
            $smsDate = '';
            foreach ($result['result'] as $k => $val) {
                $selectStatus = '';
                $id = $val['id'];
                $vendorid = $val['vendorid'];
                /*
                  $userId = '';
                  $userName = '';
                  if($val['userid'] > 0){
                  $userId = $val['userid'];
                  $userName = "<a href = '/admin/user/viewinfo/id/$userId'>".$val['user_name']."</a>";
                  }else{
                  $userName = "";
                  }
                 */
                
                
                $remarks = $val['remarks'];
                
                $remarkInputType = "<input type='text' placeholder='Plz write your remark' value='$remarks' id='ieRmk_$id' name='ieRmk_$id' style = 'width:100px;'>";
                $selectStatus .= "<select id='ieSts_$id' name='ieSts_$id' style = 'width:96px;'>";
                foreach ($instaBookingStatus as $key => $statusVal) {
                    $selected = '';

                    if ($key == $val['status']) {
                        $selected = 'selected = selected';
                    }
                    $selectStatus .= "<option value='$key' $selected> $statusVal </option>";
                }
                $selectStatus .="</select>";
                $statusUpdate = "<a onclick='return updateInstaEnquiryStatus($id);' href='javascript:void(0);'><img width='25' height='20' src='" . ADMIN_IMAGE_URL . "update.png' alt='Status Update' title='Status Update'></a>";
                
                
                
                if ($val['vendor_email_status'] == 1) {
                    $vendorEmailStatus = 'Activated';
                } else {
                    $vendorEmailStatus = "Deactivated";
                }

                $responce->rows[$k]['id'] = $val['id'];
                
                $smsDateTime = $this->changeDateFormat($val['date'],DATEFORMAT, DATE_TIME_FORMAT);
                
                $smsDate = $this->changeDateFormat($val['date'], DATEFORMAT, '');
                if (strtotime($smsDate) >= strtotime($yesterDaydate)) {
                    
                    
                    $responce->rows[$k]['cell'] = array(
                        "<strong>" . $smsDateTime . "</strong>",
                        "<strong>" . $val['name'] . "</strong>",
                        "<strong>" . $val['email'] . "</strong>",
                        "<strong>" . $val['mobile'] . "</strong>",
                        "<strong><a href = '/admin/vendor/viewinfo/id/$vendorid'>" . $val['vendor_name'] . "</a></strong>",
                        "<strong>".$val['city_name'] ."</strong>",
                        $remarkInputType,
                        $selectStatus,
                        $statusUpdate
                    );
                } else {
                   
                    $responce->rows[$k]['cell'] = array(
                        $smsDateTime,
                        $val['name'],
                        $val['email'],
                        $val['mobile'],
                        "<a href = '/admin/vendor/viewinfo/id/$vendorid'>" . $val['vendor_name'] . '</a>',
                        $val['city_name'],                        
                        $remarkInputType,
                        $selectStatus,
                        $statusUpdate
                    );
                }
            }

            echo $this->jsonEncode($responce);
            exit;
        }
        
        
    }
    
    public function instabookingupdatestatusAction() {
        $id = $this->getRequest()->getParam('id');
        $remarks = $this->getRequest()->getParam('remarks');
        $status = $this->getRequest()->getParam('status');
        if (($status != '0') && ($remarks != '') && ($id != '')) {
            global $nowDateTime;
            $toBeUpdated['id'] = $id;
            $toBeUpdated['status'] = $status;
            $toBeUpdated['remarks'] = $remarks;
            $toBeUpdated['updated_at'] = $nowDateTime;
            $toBeUpdated['updated_by'] = $this->session->loginId;            
            $return = $this->_dashboardResource->updateInstabookingStatusRecords($toBeUpdated);
            echo '1';
            die;
        } else {
            echo '0';
            die;
        }
    }
    
     public function instaenquiryupdatestatusAction() {
        $id = $this->getRequest()->getParam('id');
        $remarks = $this->getRequest()->getParam('remarks');
        $status = $this->getRequest()->getParam('status');
        if (($status != '0') && ($remarks != '') && ($id != '')) {
            global $nowDateTime;
            $toBeUpdated['id'] = $id;
            $toBeUpdated['status'] = $status;
            $toBeUpdated['remarks'] = $remarks;
            $toBeUpdated['updated_at'] = $nowDateTime;
            $toBeUpdated['updated_by'] = $this->session->loginId;            
            $return = $this->_dashboardResource->updateInstaEnquiryStatusRecords($toBeUpdated);
            echo '1';
            die;
        } else {
            echo '0';
            die;
        }
    }
    
    public function contactexpertAction() {
        global $contactExpertStatus;
        $params['count'] = '';
        $request = $this->getRequest();
        
        if ($request->isXmlHttpRequest()) {

            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $data = $this->getRequest()->getPost();
            //dd($data);
            $params = $this->getSearchParams($data);
     
            $params['fields']['main'] = array('id','name', 'phone','event_name', 'event_date','status','remarks','created_at');
            
            $result = $this->_dashboardResource->contactExpertData($params);
            //dd($result['result']);
            $responce = new stdClass();
            $responce->page = $result['page'];
            $responce->total = $result['total'];
            $responce->records = $result['records'];
            foreach ($result['result'] as $k => $val) {
                $selectStatus = '';
                
                $id = $val['id'];                
                
                $responce->rows[$k]['id'] = $val['id'];
                $remarks = $val['remarks'];
                
                $remarkInputType = "<input type='text' placeholder='Plz write your remark' value='$remarks' id='ceRmk_$id' name='ceRmk_$id' style = 'width:100px;'>";
                $selectStatus .= "<select id='ceSts_$id' name='ceSts_$id' style = 'width:96px;'>";
                foreach ($contactExpertStatus as $key => $statusVal) {
                    $selected = '';

                    if ($key == $val['status']) {
                        $selected = 'selected = selected';
                    }
                    $selectStatus .= "<option value='$key' $selected> $statusVal </option>";
                }
                $selectStatus .="</select>";
                $statusUpdate = "<a onclick='return updateContactExpertStatus($id);' href='javascript:void(0);'><img width='25' height='20' src='" . ADMIN_IMAGE_URL . "update.png' alt='Status Update' title='Status Update'></a>";
                
                $createdModifiedDate = '';
                
                //$createdModifiedDate = $this->changeDateFormat($val['created_at'], DATEFORMAT, "");
                $responce->rows[$k]['cell'] = array(
                        $val['created_at'],
                        $val['name'],
                        $val['phone'],
                        $val['event_name'],  
                        $val['event_date'],
                        $val['city_name'],
                        $remarkInputType,
                        $selectStatus,
                        $statusUpdate
                    );
            }

            echo $this->jsonEncode($responce);
            exit;
        }
    }
    
    public function contactexpertupdatestatusAction() {
        $id = $this->getRequest()->getParam('id');
        $remarks = $this->getRequest()->getParam('remarks');
        $status = $this->getRequest()->getParam('status');
        if (($status != '0') && ($remarks != '') && ($id != '')) {
            global $nowDateTime;
            $toBeUpdated['id'] = $id;
            $toBeUpdated['status'] = $status;
            $toBeUpdated['remarks'] = $remarks;
            $toBeUpdated['updated_at'] = $nowDateTime;
            $toBeUpdated['updated_by'] = $this->session->loginId;            
            $return = $this->_dashboardResource->updateContactExpertStatusRecords($toBeUpdated);
            echo '1';
            die;
        } else {
            echo '0';
            die;
        }
    }
    
    
    public function adminCheckImageEditAction() {

        $id = $this->getRequest()->getParam('bid');
        $business_type = $this->getRequest()->getParam('business_type');
        
        if (isset($business_type)) {
            $this->view->business_type = $business_type = $this->getRequest()->getParam('business_type');
            $this->view->businessTypeUrl = 'business_type/' . $business_type;
        }
        $this->view->return_type = $this->getRequest()->getParam('returnType');
        $orignalVendorId = '';
        
        if (!empty($id)) {
            
            $claimBusinessPortfolioDetails = $this->_claimbusinessModal->getClaimBusinessPortfolioDetail($id);
            //dd($claimBusinessPortfolioDetails);
            $this->view->claimVendorPortfolioImageList =  $claimBusinessPortfolioDetails;
            if(($business_type == 1) && (!empty($claimBusinessPortfolioDetails))){
                $orignalVendorId = $claimBusinessPortfolioDetails[0]['businessid'];
                $VendorDetails = $this->_claimbusinessModal->businessPortfolioDetail($orignalVendorId);
               // dd($VendorDetails);
                $this->view->vendorPortfolioImageList = $VendorDetails;
                $this->view->orignalVendorId = $orignalVendorId;
                
            }          
            $this->view->id = $id;            
            $params['remarks'] = 'Seen by admin';            
            $params['claimed_business_id'] = $id;            
            $this->_claimbusinessModal->updateBusinessPortfolioRemark($params);
        }

        
    }
    
    /**
     * This function is used to delete Add business portfolio images.
     * Created By : Bidhan Chandra
     * Date : 18 Oct, 2016
     * @param void
     * @return void
     */
    
    public function deleteBulkAddClaimedBusinessImageAction() {

        $request = $this->getRequest();

        $imageids = $request->getParam('imageids');
        $category = $request->getParam('category');
        $businessid = $request->getParam('businessid');
        //$arrImageids = explode(',',$imageids);
        $arrImageids = explode(',',$imageids);
        $getImageNames = $this->_claimbusinessModal->getAddBusinessImageNameByIds($arrImageids,$businessid);
        
        if(!empty($getImageNames)){
            
        
            foreach($getImageNames as $key=>$arrImg){

                $imageName = $arrImg['image'];
               // $imageName = $arrImg;

                $newname1 = UPLOAD_FILE_PATH."/images/portfolio/main/$category/$businessid/".$imageName;

                $newname2 = UPLOAD_FILE_PATH."/images/portfolio/thumb/$category/$businessid/".$imageName;

                $newname3 = UPLOAD_FILE_PATH."/images/portfolio/orignal/$category/$businessid/".$imageName;


                /*         * *************Code to delete image from cloudinary server********************* */

                $imgInfo = pathinfo($newname3);
                $cloudFolderName = $this->appOptions['cloudnary']['main_dir'] . CLOUD_VENDOR_PORTFOLIO;
                $prvInsImg = $this->appOptions['cloudnary']['main_dir'] . CLOUD_VENDOR_PORTFOLIO . $category.'/'.$businessid.'/'.$imgInfo['filename'];
                \Cloudinary\Uploader::destroy($prvInsImg, array('invalidate' => true));
                /*         * *************Code to delete image from cloudinary server********************* */

                @unlink($newname1);

                @unlink($newname2);

                @unlink($newname3);
                
            }
           
            $deleteimage = $this->_claimbusinessModal->deleteBulkAddBusinessImage($arrImageids,$businessid);
            $response = array('status' => 1);
            echo Zend_Json::encode($response);
            }
        //echo $deleteimage;
        exit;
    }
    
    /**
     * This function is used to delete portfolio images.
     * Created By : Bidhan Chandra
     * Date : 26 Dec,2013
     * @param void
     * @return void
     */
    public function deletebulkimageAction() {

        $this->_helper->layout->disableLayout('innerlayout');

        $request = $this->getRequest();

        $imageids = $request->getParam('imageids');
        $category = $request->getParam('category');
        $businessid = $request->getParam('businessid');
        $arrImageids = explode(',',$imageids);
       // dd($arrImageids);
        $getImageNames = $this->_claimbusinessModal->getImageNameByIds($arrImageids,$businessid);
        
        if(!empty($getImageNames)){
            
        
            foreach($getImageNames as $key=>$arrImg){

                $imageName = $arrImg['image'];


                $newname1 = UPLOAD_FILE_PATH."/images/portfolio/main/$category/$businessid/".$imageName;

                $newname2 = UPLOAD_FILE_PATH."/images/portfolio/thumb/$category/$businessid/".$imageName;

                $newname3 = UPLOAD_FILE_PATH."/images/portfolio/orignal/$category/$businessid/".$imageName;


                /*         * *************Code to delete image from cloudinary server********************* */

                $imgInfo = pathinfo($newname3);
                $cloudFolderName = $this->appOptions['cloudnary']['main_dir'] . CLOUD_VENDOR_PORTFOLIO;
                $prvInsImg = $this->appOptions['cloudnary']['main_dir'] . CLOUD_VENDOR_PORTFOLIO . $category.'/'.$businessid.'/'.$imgInfo['filename'];
                \Cloudinary\Uploader::destroy($prvInsImg, array('invalidate' => true));
                /*         * *************Code to delete image from cloudinary server********************* */

                @unlink($newname1);

                @unlink($newname2);

                @unlink($newname3);
            }
           
            $deleteimage = $this->_claimbusinessModal->deleteBulkImage($arrImageids,$businessid);

                
            $response = array('status' => 1);
                
           
            echo Zend_Json::encode($response);
            }
        //echo $deleteimage;
        exit;
    }
    
    public function approveBusinessImageAction() {
        
        $request = $this->getRequest();
        
        if($request->isPost()){
            $params = $this->__inputPostData;
            $response = array('status' => 0, 'msg' => '');
            
            $inputData = new Zend_Filter_Input($this->filters, $this->validators);
            
            $inputData->setData($params);
            
            if($inputData->isValid()) {
            
                if(!empty($params['delPortfolioChkClaim'])){
                    
                    
                    $rparams['ids'] = $params['delPortfolioChkClaim'];
                    $rparams['approved_status'] = 1;
                    $updateImageStatus = $this->_claimbusinessModal->updateClaimedVendorImages($rparams);
                    
                }
               $this->_redirect(ADMIN_BASE_URL . 'dashboard/vendorlist/' . $params['business_type'] . '/returnType/' . $params['return_type'] . '/u'); 
               
            }
            
        }
        
        
        
        
        
        
        
    }
    
    
 }

?>