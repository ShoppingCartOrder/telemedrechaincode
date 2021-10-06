<?php
defined('HOSTPATH') || die("Illegal access of Page");
defined('PUBLIC_PATH')
    || define('PUBLIC_PATH', realpath(dirname(__FILE__)));
define('APPLICATION_UPLOADS_DIR', PUBLIC_PATH . '/uploads');
define('WEBSITE_URL', HOSTPATH);
define('WEBSITE_PUBLIC_URL', HOSTPATH);
define('WEBSITE_MIN_URL', HOSTPATH.'min/?f=');

define('WWW_ROOT',HOSTPATH);
define('newimage_URL',HOSTPATH);
define('SITEURL',HOSTPATH);

define('UPLOAD_FILE_PATH',PUBLIC_PATH);
define('BLOG_PATH',HOSTPATH.'/blog');
//define('ADMIN_BASE_URL',HOSTPATH.'admin/');

//define('ADMINEMAIL','parull@telemedrechaincode.com');
define('ADMINEMAIL','sendbidhan@gmail.com');

define('WEBSITE_Image', WEBSITE_PUBLIC_URL.'images');

define('userImage_main',WEBSITE_PUBLIC_URL.'images/userimages/main');
define('userImage_thumb',WEBSITE_PUBLIC_URL.'images/userimages/thumb');

define('USER_IMAGE_MAIN_PATH',PUBLIC_PATH.'images/userimages/main');
define('USER_IMAGE_THUMB_PATH',PUBLIC_PATH.'images/userimages/thumb');

define('ADVERTISE_IMAGE_PATH',PUBLIC_PATH.'/images/advertise/');
define('ADVERTISE_IMAGE_PATH_DEFAULT',WEBSITE_PUBLIC_URL.'images/advertise/default/');


define('REVIEW_IMAGE_DEFAULT_PATH',WEBSITE_PUBLIC_URL.'images/reviewimage/');

define('CLOUD_USER_PROFILE','user_profile/');

define('userPortfolio',WEBSITE_PUBLIC_URL.'images/portfolio');
define('userPortfolio_main',WEBSITE_PUBLIC_URL.'images/portfolio/main');
define('userPortfolio_thumb',WEBSITE_PUBLIC_URL.'images/portfolio/thumb');

define('userPortfolioMainDef',WEBSITE_PUBLIC_URL.'images/portfolio/main.jpg');
define('userPortfolioThumbDef',WEBSITE_PUBLIC_URL.'images/portfolio/thumb_def.jpg');
 
define('VENDOR_PORTFOLIO_WATERMARK', WEBSITE_PUBLIC_URL.'images/');
define('ERROR_SORRY_PAGE', 'error/sorry/');

define('ADMIN_IMAGE_URL',WEBSITE_PUBLIC_URL.'images/adminImages/');
define('eventImage',WEBSITE_PUBLIC_URL.'images/eventimage');



define('WEBSITE_PATH',WEBSITE_PUBLIC_URL); 

define('WEBSITE_PATH_THEME',WEBSITE_PUBLIC_URL.'themes/'); 

define('wedingPlzWeb',WEBSITE_PUBLIC_URL.'themes'); 

define('weddingMusic',WEBSITE_PUBLIC_URL.'uploads'); 

define('weddingMusicfile',WEBSITE_PUBLIC_URL.'images/albums'); 





define('WEBSITE_PATH_EDITER',WEBSITE_PUBLIC_URL.'myediter/'); 

define('WEBSITE_URL_THUMBS',WEBSITE_PUBLIC_URL);
define('WEBSITE_PATH_ALBUM',WEBSITE_PUBLIC_URL.'images/albums/'); 
define('WEBSITE_PATH_SAMPLE',WEBSITE_PUBLIC_URL.'myediter/samples');

define('ADVERTISEMENT_PHOTOS', WEBSITE_PUBLIC_URL.'images/advertise');
 
define('CAT_EXTRA_FIELD_PREFIX','extra');
define('CAT_FIELD_SEPARATOR','-');

define('WEBSITE_URL_JS',WEBSITE_PUBLIC_URL.'js/');
define('JSPATH',WEBSITE_PUBLIC_URL.'js/');
define('CSSPATH',WEBSITE_PUBLIC_URL.'css/');
define('JQPATH',WEBSITE_URL_JS.'jquery/js/');
define('JQCSSPATH',WEBSITE_URL_JS.'jquery/css/');
define('COLORBOXPATH',WEBSITE_PUBLIC_URL.'js/colorbox/');
//define('BACK_END_ROLE', 'b');
//define('FRONT_END_ROLE', 'f');
define('REMEMBER_ME_TIME', 1000000);

define('TOTALCATEGORYSEARCHLIMIT', 7);
define('CATEGORYSEARCHLIMIT', 4);
define('BUSINESS_SEARCH_LIMIT', 3);

define('TOTALCITYLOCATIONSEARCHLIMIT', 10);
define('CITYSEARCHLIMIT', 4);
define('ZONESEARCHLIMIT', 3);
define('LOCATIONSEARCHLIMIT', 5);
define('CITYLIMIT', 12);

$withoutLoginFront = array();
$withoutLoginAdmin = array(
    'index'=>array('index','login')
    );
$paymentMode = array(0=>"Select", 1=>"Cash", 2=>"Cheque", 3=>"Credit Card", 4=>"Debit Card");

define('CATEGORY_TAG_ICON_PATH',PUBLIC_PATH.'/uploads/icons/');
define('CATEGORY_TAG_ICON_PATH_THUMBS',PUBLIC_PATH.'/uploads/icons/thumbs/');
define('SERVICE_ICON_PATH',WEBSITE_PUBLIC_URL.'uploads/icons/thumbs/');
define('SERVICE_ICON_ROOT_PATH',WEBSITE_PUBLIC_URL.'uploads/icons/');

define('SEARCH_DEFAULT_COUNT',1);
define('LP_DISPLAY_ICON',6);
define('TAG_IMAGE_HEIGHT',18);
define('TAG_IMAGE_WIDTH',18);
define('SEARCH_PRIORITY_VALUES',7);
define('NO_OF_RECORDS_PER_PAGE',50);
define('DEFAULTSORTDIR','ASC');
//$additional_attribute_type = array(1 =>'Text',2=>'Text Area',3=>'Check box',4=>'Radio button',5=>'Drop down',6=>'Single Image upload',7=>'Multi Image upload',8=>'Virtual Tour');
$additional_attribute_type = array(1 =>'Text',2=>'Text Area',3=>'Check box',4=>'Radio button',5=>'Drop down',6=>'Image upload',8=>'Virtual Tour');
$menu_item_name = array(1 =>'Info',2=>'Portfolio',3=>'Reviews',4=>'Map',5=>'Deals');
$cities = array('1'=>'Delhi','2' =>'Mumbai');
$arr_providers = array('0' =>'Service','1'=>'Product');
$dlp_menu_name = array('Basic Info' =>'Basic Info','Portfolio'=>'Portfolio','Reviews'=>'Reviews','Map & Direction'=>'Map & Direction','Deals'=>'Deals');
$vendor_pending_status = array(0=>'Pending',1=>'Verified',2=>'Rejected');
$vendor_sources = array(0=>'Admin',1=>'Advt Us',2=>'Ad Business');
$search_page_count = array('25','50','75','100');

$date = new Zend_Date();
//$nowDate = $date->get('YYYY-MM-dd');
//$nowDateTime = $date->get('YYYY-MM-dd HH:mm:ss');
$nowDate = $date->get('yyyy-MM-dd');
$nowDateTime = $date->get('yyyy-MM-dd HH:mm:ss');
$monthArray = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec');
define('PER_PAGE_REVIEWS',5);
define('NO_OF_VENDORS_SEARCH_HISTORY',2);
define('NO_OF_RELATED_VENDORS',8);
define('REVIEW_IMAGE_UPLOAD_SIZE',16000000);


define('PER_PAGE_PORTFOLIO_IMAGE',10);
define('PER_PAGE_PORTFOLIOALBUM_IMAGE',2);
define('SERVICE_IMAGE_UPLOAD_SIZE',2097152);
define('NO_VENDOR_PORTFOLIO_IMAGE',4);
$IMAGE_FILE_EXTENSION = array('jpeg','jpg','png','gif');
 $DOC_FILE_EXTENSION = array('doc','docx','xls','pdf');
 
 define('INFO_EMAIL','info@telemedrechaincode.com');
 define('SUPPORT_EMAIL','support@telemedrechaincode.com');
 define('BUSINESS_EDIT_EMAIL','sendparull@gmail.com');
 define('NO_REPLY_EMAIL','no-reply@telemedrechaincode.com');
 define('WEBSITE_NAME','Vivahaayojan.com');
 
 define('SEARCH_FILTER_SEPARATOR','  in  ');
 
 define('ADVERTISE_US','sales@telemedrechaincode.com');
 define('MISSING_BUSINESS','info@telemedrechaincode.com');
 define('BUSINESS_ADD','info@telemedrechaincode.com');
 define('DEAL_ALERT','info@telemedrechaincode.com');
 define('REVIEW_INSERT','info@telemedrechaincode.com');
 define('EVENT_NEWSLETTER','events@telemedrechaincode.com');
 define('EVENT_CREATED','events@telemedrechaincode.com');
 define('CAREER_APPLY','career@telemedrechaincode.com');
 define('MAX_FORGOT_PASS_ATTEMPT', 10);
 
$status = array(0 =>'Deactive',1=>'Active');
//Added by umesh 
$statusType = array(0 =>'New',1=>'Respond',2=>'Confirmed');


//$no_record = array(0 =>'Open',1=>'Added');
$something_mind = array(0 =>'Added',1=>'Rejected',2=>'Reply sent');
$addedBusiness = array(0 =>'Pending',1=>'Verified',2=>'Rejected');
$dealsBusiness = array(0 =>'Pending',1=>'Accepted',2=>'Refused');
 define('NO_OF_CHARACTERS_LIMIT',50);
 define('NO_OF_CHARACTERS',60);
 define('CONTACT_US_EMAIL','contact@telemedrechaincode.com');
 define('HELP_US_IMPROVE','info@telemedrechaincode.com'); 
 //define('WEDDING_PLZ_CONTACT_NO','+91 9971257789');
 //define('WEDDING_PLZ_CONTACT_NO','+91-7862825566');
 define('WEDDING_PLZ_CONTACT_NO','+91-7834822228');
 $allowedAdminIP = array("182.68.139.114","127.0.0.1");
 $metaFormulaArr = array(
     'meta_title'=>"%s in %s, %s | Vivahaayojan",
     'meta_description'=>"%s in %s - Get addresses, phone numbers, best deal, customers rating & reviews and more for %s at Vivahaayojan."
 );
 
 $metaServiceFormulaArr = array(
     'meta_title_service'=>"%s | ",
     'meta_description_service'=>" List of %s."
 );
 
 $vendorMetaFormulaArr = array(
     'meta_title'=>"%s, %s, %s | %s | Vivahaayojan",
     'meta_description'=>"%s, %s, %s, India. Specialized in %s."
 );
 
 $vendorLpMetaFormulaArr = array(
     'meta_title'=>"%s in %s | Vivahaayojan",
     'meta_description'=>"%s in %s - Find %s in %s region with address, offered services, ratings, reviews, at Vivahaayojan.com."
 );
 
 $XMLFILECOUNT = array('1'=>1,'2'=>2,'3'=>3,'4'=>4,'5'=>5,'6'=>6,'7'=>7,'8'=>8,'9'=>9,'10'=>10,'11'=>11,'12'=>12,'13'=>13,'14'=>14,'15'=>15);
 $defaultLpMeta = array(
     'meta_title' => "Vivahaayojan",
     'meta_description' => "Search Wedding Vendors",
     'meta_keywords'=>"Wedding Vendors"
 );
 define('DEFAULT_DATE','25-08-2013');
 
 $changeFrequently  = array('Daily' => 'Daily','Monthly' => 'Monthly','Yearly' =>'Yearly');
 define('REVIEW_IMAGE_MAIN',WEBSITE_PUBLIC_URL.'/images/reviewimage/main/');
 define('REVIEW_IMAGE_THUMB',WEBSITE_PUBLIC_URL.'/images/reviewimage/thumb/');
 
 define('REVIEWLINK',HOSTPATH.'reviews');
 
 define('Mylib_FB_LINK','https://www.facebook.com/Vivahaayojan/');
 define('FB_IMAGE_LINK',WEBSITE_PUBLIC_URL.'images/fb.png');
 
 define('Mylib_TWITTER_LINK','https://twitter.com/telemedrechaincode');
 define('TWITTER_IMAGE_LINK',WEBSITE_PUBLIC_URL.'images/twt.png');
 
 define('Mylib_GP_LINK','https://plus.google.com/104608318393562333997');
 define('GP_IMAGE_LINK',WEBSITE_PUBLIC_URL.'images/gplus.png');
 define('Mylib_LINKEDIN','https://www.linkedin.com/company/telemedrechaincode');
 define('WEDDININGPLZ_INSTAGRAM','https://www.instagram.com/telemedrechaincode/');
 define('Mylib_PINTEREST','https://www.pinterest.com/Vivahaayojan/');
 
 define('INSTAGRAM_IMAGE_LINK',WEBSITE_PUBLIC_URL.'images/insta.png');
 
 $noRecord = array(0 =>'Open',1=>'Accepted',2=>'Rejected');
 
 define('ROWLIST',"50,100,150,200");
 define('RESULT_NOT_FOUND','Result is not found.');
 
 define('STATUSTYPESEARCHSTR',":All;0:Open;1:Updated;2:Pending;3:Rejected");
 define('STATUSNORECORDSTR',":All;0:Open;1:Accepted;2:Rejected");
 define('STATUSSOMETHINGMINDSTR',":All;0:Added;1:Rejected;2:Reply sent");
 define('STATUSDEALSTR',":All;0:Pending;1:Accepted;2:Refused");
 define('STATUSREVIEWSTR',":All;0:Pending;1:Verified;3:Rejected");
 
 $tagType = array('' =>'All',1=>'Main',2=>'Alternate');
 define('DATEFORMAT','Y-m-d');
 
 $eventStatus = array(0 =>'Pending',1=>'Live',2=>'Rejected');
 define('EVENTSTATUS',":All;0:Pending;1:Live;2:Rejected");

$advertiseStatus = array(0 =>'Open',1 =>'Handled',2=>'Rejected');
define('ADVERTISE_STATUS',":All;0:Open;1:Handled;2:Rejected");

$contactUsStatus = array(0 =>'Open',1 =>'Reply Sent',2=>'Rejected');
define('CONTACTUS_STATUS',":All;0:Open;1:Reply Sent;2:Rejected");
define('DATETIMEFORMAT','Y-m-d H:m:s');
define('BUSINESS_STATUS_STR',":All;0:Pending;1:Verified;2:Rejected");
$vendorType = array(0 =>'Not paid',1 =>'Paid');
define('VENDOR_TYPE_STR',":All;0:Not paid;1:Paid");

define('VENDOR_SEND_ENQUIRY',":All;0:Send enquiry;1:Not send enquiry");
define('SEND_ENQUIRY_PRIORITY',7);

define('Mylib_CLAIMBUSINESS_LINK','Claimbusiness/findbusiness');
define('OUTPUT_DATE_FORMAT','dd-mm-Y');

define('DB_DATE_FORMAT','Y-m-d');
define('OUTPUT_DATE_FORMAT_NOTE','d M Y');

$preDate = $date->subDay(1);
define('MAX_NO_VENDORS_SMS',6);

define('YESTERDAY_DATE_FORMAT','d-m-Y');
$resultNotFound = array('result' => array(),'total'=>0,'records'=>0,'page'=>0);

$graphArray = array(0=>array('popularity'=>'15','month'=>''));
define('BUSINESS_VISIT_COUNT',5);

define('BUSINESS_ENQUIRY_USER_CONF_SUBJECT','Your enquiry sent to vendors');

define('ALL_EVENT_URL_STR','upcoming-wedding-events-and-exhibitions-in-');


$bannerExpiryDuration = array('1_weeks' =>'1 Week','1_months' =>'1 Month','3_months' =>'3 Months','6_months' =>'6 Months','1_years' =>'1 Years');

$allowedImageType = array('image/jpeg','image/gif','image/png','image/bmp');

define('ADD_CLAIM_BUSINESS_ASSISTANCE_TEXT','We are here for you, for any assistance Call us on '.WEDDING_PLZ_CONTACT_NO.' or email us at <a href="mailto:'.INFO_EMAIL.'">'.INFO_EMAIL.'</a>');
define('MAX_NO_VENDORS_SMS_QUICK_QUOTE',7);
define('LOCATION_TYPE_STR',"1:City;2:Zone;3:Both");
$locationTypeArr = array('1'=>'City','2'=>'Zone','3'=>'Both');
define('Mylib_TAG_LINE',"Search The Best Wedding Vendors in Town");
define('RELATED_TAG_LP_LINK_NO',6);
define('RELATED_TAG_DLP_LINK_NO',12);
define('RELATED_TAG_DLP_LOCATION_LINK_NO',8);

define('RELATED_TAGS_ROW_LIST',"'All'");

$businessSearch = array("/", "&" , "'");
$businessReplace = array(" and ", "and", "");
$mediaArray = array(''=>'NA','print' =>'Print','television'=>'Television','website'=>'Website','radio'=>'Radio','other'=>'Other');

$vendorBranchMetaFormulaArr = array(
     'meta_title'=>"%s more Branches in %s | Vivahaayojan",
     'meta_description'=>"%s in %s - Find %s more Branches in %s region with address, offered services, ratings, reviews, at Vivahaayojan.com."
 );

define('NO_OF_KEYWORDS_SEO',5);
define('XML_FILE_EXTENSION','.xml.gz');

define('CLOUD_VENDOR_PORTFOLIO','vendor_portfolio/');
define('CLOUD_TAG_SERVICE_ICON','service_icon/');
define('CLOUD_VENDOR_EXTRA_IMAGE','vendorsImage/extra/');
define('VIEW_PRIVILEGE',1);
define('ADD_PRIVILEGE',2);
define('EDIT_PRIVILEGE',3);
define('DELETE_PRIVILEGE',4);
define('RES_VENDOR_MANAGEMENT',1);
define('CLOUD_STATIC_IMAGE','static_images/');


define('SEARCH_SERVICE_SEPARATOR','|');
define('APP_ROOT_PATH',dirname(dirname(__FILE__)));
define('DATA_FILE_PATH',APP_ROOT_PATH.'/data/recordFiles/');
define('TAG_FILE_PATH',DATA_FILE_PATH.'allTags.json');
define('EXTRA_FIELDS_FILE_PATH',DATA_FILE_PATH.'extraFields.json');
define('SEARCH_EX_CAT_SEPARATOR','~');
define('SEARCH_EX_FIELD_VALUE_SEPARATOR','--is--');
define('SEARCH_TXT_SEPARATOR','-');
define('LOCATION_FILE_PATH',DATA_FILE_PATH.'locations.json');
define('SEARCH_LOCATION_SEPARATOR',',');
define('COMMA_SEPARATOR',',');
define('SEARCH_XT_FIELD_NAMES_SEPARATOR','--and--');
//$xtrFldTypeArr = array(1=>'text', 2=>'textarea', 3=>'checkbox', 4=>'radio', 5=>'select', 6=>'Single Image Upload', 7=>'Multi Image Upload', 8=>'Virtual Tour');
$xtrFldTypeArr = array(1=>'text', 2=>'textarea', 3=>'checkbox', 4=>'radio', 5=>'select', 6=>'Image Upload', 8=>'Virtual Tour');
$allowedDocType = array('application/pdf','application/msword','application/vnd.openxmlformats-officedocument.wordprocessingml.document');
//$xtrFldTypeArrECR = array(1=>'text', 2=>'textarea',5=>'select', 6=>'Single Image Upload', 7=>'Multi Image Upload', 8=>'Virtual Tour');
$xtrFldTypeArrECR = array(1=>'text', 2=>'textarea',5=>'select', 6=>'Image Upload', 8=>'Virtual Tour');
define('ZONE_FILE_PATH',DATA_FILE_PATH.'zones.json');
//$xtrFldChkedTypeXfld = array(3=>'checkbox', 4=>'radio', 6=>'Single Image Upload', 7=>'Multi Image Upload', 8=>'Virtual Tour');
$xtrFldChkedTypeXfld = array(3=>'checkbox', 4=>'radio', 6=>'Image Upload', 8=>'Virtual Tour');
$xtrFldValueInsrtTypeXfld = array(1=>'text', 2=>'textarea',5=>'select');
define('DEFAULT_CITY_ID',1);
define('DEFAULT_CITY_TYPE','city');
define('CURRENT_MONTH',ltrim(date('m'),'0'));

define('CONFIG_FILE_PATH',APP_ROOT_PATH.'/application/configs/');
define('REGIONS_FILE_PATH',CONFIG_FILE_PATH.'application_regions.ini');

define('CITY_SPCL_CHAR_REPCE','-');
define('CATEGORY_DISPLAY_NO',3);
define('META_TAG_PAGE_NAME','Page');
$arrCityDisplay = array(1,2);
$DELHI_LOCATION_TEXT = 'e.g. Karol bagh etc.';
$MUMBAI_LOCATION_TEXT = 'e.g. Andheri etc.';
define('SS_URL',WEBSITE_URL.'getData.php');
define('FILTER_URL_SEPARATOR','--in--');
define('COUPON_VALID_DAYS','15 days');
define('USER_DATE_FORMAT','d-m-Y');
define('COUPON_PREFIX_CODE','WP');
define('DATA_EMAIL','data@telemedrechaincode.com');
define('PHOTO_GALLERY_INSPIRATION',UPLOAD_FILE_PATH.'/images/photo-gallery/inspiration/');
define('PHOTO_GALLERY_DISPLAY_INSPIRATION',WEBSITE_PUBLIC_URL.'images/photo-gallery/inspiration/');
define('PHOTO_GALLERY_DISPLAY_INSPIRATION_THUMB',WEBSITE_PUBLIC_URL.'images/photo-gallery/inspiration/thumbs/');
define('INSPIRATION_IMAGE_STATUS_STR',":All;0:Not Active;1:Active");
$inspirationalStatus = array(0 =>'Not Active',1=>'Active');
define('PHOTO_GALLERY_DISPLAY_REAL_WED',WEBSITE_PUBLIC_URL.'images/photo-gallery/real_wedding/');
define('PHOTO_GALLERY_DISPLAY_REAL_WED_THUMB',WEBSITE_PUBLIC_URL.'images/photo-gallery/real_wedding/thumbs/');
define('PHOTO_GALLERY_REAL_WED',UPLOAD_FILE_PATH.'/images/photo-gallery/real_wedding/');
define('PHOTO_GALLERY_INSPIRATION_THUMB',UPLOAD_FILE_PATH.'/images/photo-gallery/inspiration/thumbs/');
define('ALBUM_IMAGE_HEIGHT',100);
define('ALBUM_IMAGE_WIDTH',300);
define('PER_PAGE_ALBUM_IMAGE',15);
define('PHOTO_GALLERY_REAL_WED_THUMB',UPLOAD_FILE_PATH.'/images/photo-gallery/real_wedding/thumbs/');
define('PHOTO_GALLERY_THUMB_DISPLAY_INSPIRATION',WEBSITE_PUBLIC_URL.'images/photo-gallery/inspiration/thumbs/');
define('PER_PAGE_INSPIRATION_IMAGE',15);
define('PER_PAGE_REAL_WED_IMAGE',14);
define('PHOTO_GALLERY_TRENDINGNOW','trending');
define('PHOTO_GALLERY_LATEST','latest');
define('PHOTO_GALLERY_CATEGORY_FILE_PATH',DATA_FILE_PATH.'categories.json');
define('IDEA_BOOK_IMG_PATH',UPLOAD_FILE_PATH.'/images/ideabook/');
define('IDEA_BOOK_DISPLAY_IMG_PATH',WEBSITE_PUBLIC_URL.'/images/ideabook/');
define('CLOUD_IDEA_BOOK_IMG_PATH','ideabook/');
define('PER_PAGE_IDEABOOK_INSPIRATIONS_IMAGE',2);
define('PER_PAGE_IDEABOOK_BOARD',2);
define('PER_PAGE_IDEABOOK_BOARD_IMAGE',2);



/*define('facebook_appId', "725560674218955");
define('facebook_secret', "48801a84a277eb1ec89d2eafb0be02a5");
define('facebook_permissions', "email");
define('facebook_redirect_uri', 'http://local.telemedrechaincode.com/index/fblogin');*/
define('FACEBOOK_API_ID', '106423413030579');
define('FACEBOOK_SECRET', 'ba8a33da3169b49eeacf803c233791dc');

define('GMAIL_BASE_URL','https://www.google.com/m8/feeds/contacts/default/full?max-results=');
define('GMAIL_CURL_TOKEN','https://accounts.google.com/o/oauth2/token');
define('GMAIL_GD_NS','http://schemas.google.com/g/2005');
define('DISPLAY_RECORDS',4);

define('BUSINESS_ASSISTANCE_TEXT','Need Assistance ?');
define('CLAIMBUSINESS_DETAIL_FILE_PATH',DATA_FILE_PATH.'claimbusinessDetail.json');
define('CLAIMBUSINESS_DEAL_FILE_PATH',DATA_FILE_PATH.'claimbusinessDeal.json');
define('SEND_SMS_EMAIL',0);

$myVendorStatusArray = array('Not Available','Negotiation in Process','Shortlisted','Selected','Booking Done','Rejected');

//$backgroundImages = array(1 =>array('banner_home01.jpg', 'banner_home03.jpg','home_baner_delhi.jpg','banner_home_08_03.jpg'),2 => array('banner_home01.jpg', 'banner_home03.jpg'),5 => array('banner_home01.jpg', 'banner_home03.jpg','home_baner_banglore.jpg'),19 =>array('banner_home01.jpg', 'banner_home03.jpg','home_baner_ludhiyana.jpg'),38=>array('banner_home01.jpg', 'banner_home03.jpg','home_baner_chndigrh.jpg'));
$backgroundImages = array(1 =>array('slide1.jpg','slide2.jpg', 'slide3.jpg','slide4.jpg'),2 => array('banner_home01.jpg',  'banner_home_02_08_16.jpg','banner_home_mumbai_13_04.jpg'),5 => array('banner_home01.jpg', 'banner_home_02_08_16.jpg','home_baner_banglore.jpg'),19 =>array('banner_home01.jpg', 'banner_home_10_03_16.jpg','home_baner_ludhiyana.jpg'),38=>array('banner_home01.jpg', 'banner_home_10_03_16.jpg','home_baner_chndigrh.jpg'));

define('PHOTO_GALLERY_TEMP_IMAGES',UPLOAD_FILE_PATH.'/photoAlbumZipFile/'); 

define('PHOTO_GALLERY_IMAGES_EXCEL_FILE',UPLOAD_FILE_PATH.'/photoAlbumExcelFile/');

define('CLOUD_PHOTO_GALLARY_INSPIRATION','photo-gallery/inspiration/');

define('CLOUD_PUBLIC_PATH','public/');

define('CLOUD_PHOTO_GALLARY_REAL','photo-gallery/real_wedding/');

define('RELATED_READ',WEBSITE_Image.'/relatedRead/');

$albumSearch = array(" ", "/","&","'","amp;","\'");
$albumReplace = array("-","-","and","","","");

define('VENDOR_PORTFOLIO_THUMB_IMAGE_HEIGHT',200);
define('VENDOR_PORTFOLIO_THUMB_IMAGE_WIDTH',200);

$imageMetaFormulaArr = array(
     'meta_title'=>" %s - %s %s - %s | Vivahaayojan",
     'meta_description'=>"%s - %s Photos, %s pictures, images, vendor credits - %s - %s."
 );


$albumMetaFormulaArr = array(
     'meta_title'=>"%s %s photos album %s | Vivahaayojan",
     'meta_description'=>"%s %s photos, couple images, pictures, %s"
 );

$insImageMetaFormulaArr = array(
     'meta_title'=>" %s - %s - %s | Vivahaayojan",
     'meta_description'=>"%s - %s Photos, %s pictures, images, vendor credits - %s."
 );

define('CLOUD_EVENT_IMAGE','eventImage/');
$gallarySectionArr = array(4=>'Wedding inspiration',5=>'Real wedding'); 

$reviewMetaFormulaArr = array(
     'meta_title'=>"Reviews - %s Wedding Vendors | Vivahaayojan",
     'meta_description'=>"Read latest Customers Ratings, Reviews of Wedding Vendors in %s for better understanding of vendor’s reputation, services qualities and more.",
     'meta_keywords'=>"reviews, real reviews, genuine reviews, reviews from real couples, review your wedding vendors, wedding vendor reviews, marriage vendor reviews ,wedding vendor review websites, wedding vendor recommendations"
 );

define('CURRENT_YEAR',date('Y'));

$eventLPMetaFormulaArr = array(
     'meta_title'=>"Upcoming Wedding Events & Exhibitions in %s, Bridal Shows, Fairs | Vivahaayojan",
     'meta_description'=>"Upcoming Wedding Events in %s - Get detailed information about upcoming wedding exhibitions in %s %s, latest marriage events, wedding fairs, Bridal shows and more.",
     'meta_keywords'=>"wedding events in %s, upcoming wedding events in %s, wedding exhibitions in %s, wedding shows in %s, bridal exhibitions in %s %s, upcoming wedding exhibitions in %s %s"
 );

define('PHOTO_ALBUM_CAT_FILE_PATH',DATA_FILE_PATH.'categories.json');

define('EVENT_STATUS_STR',":All;1:Live;2:Expired");
define('EVENT_ACTION_STATUS_STR',":All;0:Pending;1:Accepted;2:Rejected"); 

//define('VENDOR_IMAGES',UPLOAD_FILE_PATH.'/images/portfolio/orignal/10/');

define('CLOUD_EVENT_PORTFOLIO_IMAGE','eventportfolio/');

$insSearchMetaFormulaArr = array(
     'meta_title'=>"%s Photography Ideas, Indian Wedding Inspiration Photos | Vivahaayojan",
     'meta_description'=>"%s Photography - Indian Wedding images on %s theme, explore unlimited wedding pictures online at Vivahaayojan.",
     'meta_keywords'=>"%s wedding photos, %s inspiration photos, %s wedding ideas, %s wedding pictures, %s wedding images"
 );

$realSearchMetaFormulaArr = array(
     'meta_title'=>"Indian Real Wedding Photography – %s | Vivahaayojan",
     'meta_description'=>"%s Indian Real Wedding Photo Albums – Explore unlimited %s real wedding images online at telemedrechaincode.",
     'meta_keywords'=>"%s real wedding photos, indian wedding image %s, indian wedding gallery %s, indian weddings pictures %s, indian wedding picture gallery"
 );

$vendorMenuMetaFormulaArr = array(
     'meta_title'=>"Menu of %s, %s, %s | %s | Vivahaayojan",
     'meta_description'=>"Menu of %s, %s, %s - View the Menu of %s, %s, %s."
 );


$vendorReviewMetaFormulaArr = array(
     'meta_title'=>"'%s' - Customer Ratings and Reviews in %s | Vivahaayojan",
     'meta_description'=>"Customer Ratings and Reviews of '%s' in %s - Find latest customer ratings, reviews of '%s' in %s online at telemedrechaincode.com.",
     'meta_keywords'=>"review %s, review %s in %s"
 );

$crawlers = array(
    array('Google', 'Google'),
    array('msnbot', 'MSN'),
    array('Rambler', 'Rambler'),
    array('Yahoo', 'Yahoo'),
    array('AbachoBOT', 'AbachoBOT'),
    array('accoona', 'Accoona'),
    array('AcoiRobot', 'AcoiRobot'),
    array('ASPSeek', 'ASPSeek'),
    array('CrocCrawler', 'CrocCrawler'),
    array('Dumbot', 'Dumbot'),
    array('FAST-WebCrawler', 'FAST-WebCrawler'),
    array('GeonaBot', 'GeonaBot'),
    array('Gigabot', 'Gigabot'),
    array('Lycos', 'Lycos spider'),
    array('MSRBOT', 'MSRBOT'),
    array('Scooter', 'Altavista robot'),
    array('AltaVista', 'Altavista robot'),
    array('IDBot', 'ID-Search Bot'),
    array('eStyle', 'eStyle Bot'),
    array('Scrubby', 'Scrubby robot')
    );

define('EVENT_BANNER_IMAGE_UPLOAD_SIZE',20971520);


define('EVENT_BANNER_IMAGE',UPLOAD_FILE_PATH.'/images/eventimage/eventBanner/');
define('EVENT_BANNER_IMAGE_THUMB',UPLOAD_FILE_PATH.'/images/eventimage/eventBanner/thumbs/');
define('EVENT_BANNER_IMAGE_WIDTH',332);
define('CLOUD_EVENT_BANNER_IMAGE','eventbanner/');
define('EVENT_BANNER_IMAGE_DISPLAY',WEBSITE_PUBLIC_URL.'images/eventimage/eventBanner/');
define('EVENT_BANNER_IMAGE_THUMB_DISPLAY',WEBSITE_PUBLIC_URL.'images/eventimage/eventBanner/thumbs/');

$eventBannerPosition = array(0 =>'Default',1 =>'First',2 =>'Second',3 =>'Third');
define('EVENT_BANNER_STATUS_STR',":All;0:Deactivated;1:Activated");
define('EVENT_BANNER_POSITION_STR',":All;0:Default;1:First;2:Second;3:Third");

define('HOME_PAGE_EVENTS_NUMBER',3);

$currentTime = $date->get('hh');

$instaBookingFieldType = array(1 =>'Text Box',2=>'Textarea',3=>'Checkbox',4=>'Radio Button',5=>'Drop Down',6=>'Location Drop Down',7=>'Date');
$instaBookingStatus = array(0 =>'Open',1 =>'Pending',2 =>'Not Relevant',3=>'Contacted');
define('INSTABOOKING_STATUS',":All;0:Open;1:Pending;2:Not Relevant;3:Contacted");

$contactExpertStatus = array(0 =>'Open',1 =>'Pending',2 =>'Not Relevant',3=>'Contacted');
define('CONTACTEXPERT_STATUS',":All;0:Open;1:Pending;2:Not Relevant;3:Contacted");

//define('INSTA_BOOKING_FILE_PATH',DATA_FILE_PATH.'allTags.json');

define('CLOUD_VENDOR_BANNER','vendorbanner/');
define('VENDOR_BANNER_MAIN',WEBSITE_PUBLIC_URL.'images/vendorbanners/main');

define('VENDOR_BANNER_MAIN_PATH',PUBLIC_PATH.'/images/vendorbanners/main/');
define('VENDOR_BANNER_ORIGINAL_PATH',PUBLIC_PATH.'/images/vendorbanners/orignal/');
$dealsExpire = array(0 =>'Deactive',1=>'Activate');
define('STATUSDEALEXPIRESTR',":All;0:Deactive;1:Activate");
define('HOME_CATEGORY_NO',12);
define('CAPTCHA','AD@CP#(8&DEC&16)');

define('MAX_NO_PORTFOLIO_VENDOR',30);
define('ADMIN_MOBILE_NO1','9873004147');
define('ADMIN_MOBILE_NO2','9971257789');
define('SALEZ_SHARK_API_URL','https://app.salezshark.com/ExternalUtilitiesForDG/rest/service/generateClientsLead');
define('SEND_LEAD_SALEZ_SHARK_API',1);
define('CLOUDINARY_BASE_URL','telemedrechaincode-res.cloudinary.com');
define('SEARCH_LP_BUSINESS_NOT_LOCATION_MSG','No result found for %s in %s. Displaying result of other available locations.');
define('SEND_ENQUIRY_STATUS',":All;0:Open;1:Pending;2:Not Relevant;3:Contacted");
$sendEnquiryStatus = array(0 =>'Open',1 =>'Pending',2 =>'Not Relevant',3=>'Contacted');
define('WEBSITENAME','telemedrechaincode');
#define('PRIMARY_VENDOR_NO','8882-77-99-33');
define('PRIMARY_VENDOR_NO','7834-82-22-28');
$eventMetaFormulaArr = array(
     'meta_title'=>"%s at %s, %s from %s to %s | Vivahaayojan",
     'meta_description'=>"%s at %s, %s, %s from %s to %s. Know more about this most awaiting wedding %s at Vivahaayojan with contact detail, website address, timings and more.",
     'meta_keywords'=>"%s %s, upcoming wedding exhibition in %s, %s %s"
 );

define('DATE_TIME_FORMAT','d-m-Y H:m:s');
define('WEBSITE_MAIN_URL',rtrim(HOSTPATH,'/'));

define('WP_LOGO_IMAGE_LINK',WEBSITE_PUBLIC_URL.'images/wp_email_logo_new.png');
define('USER_TYPE_STR',":All;1:User;2:Vendor");
$addedReviews = array(0 =>'Pending',1=>'Verified',3=>'Rejected');
define('IP_FILE',PUBLIC_PATH.'/ips.json');
define('IP_BLOCK',0);


$appointmentType = array(0 =>'Text consultation',1=>'Video Consultation',2=>'Clinic Consultation'); 
define('APPOINTMENT_TYPE_STR',":All;0:Text consultation;1:Video Consultation;2:Clinic Consultation");
define('DOCTOR_BASE_URL',HOSTPATH.'doctor/');
define('PATIENT_BASE_URL',HOSTPATH.'patient/');
define('ADMIN_BASE_URL',HOSTPATH.'admin/');
define('HOSPITAL_BASE_URL',HOSTPATH.'hospital/');

define('ADMIN_ROLE', 3);
define('DOCTOR_ROLE', 2);
define('PATIENT_ROLE', 1);
define('PHARMACY_ROLE', 4);
define('HOSPITAL_ROLE', 5);


$statusType = array(0 =>'New',1=>'Respond',2=>'Confirmed');
define('STATUS_TYPE_STR',":All;0:New;1:Respond;2:Confirmed");
define('STATUS_TYPE_STR2',"0:New;1:Respond;2:Confirmed");
define('DEFAULT_PASSWORD',"MED@1#");
$healthCondition = array('1'=>'Previous','2'=>'Current');
define('FRONT_DATE_FORMAT','d-m-Y');
define('UPLOAD_REPORT_FILE_PATH',PUBLIC_PATH.'/uploads/');
define('UPLOADED_REPORT_FILE_PATH',HOSTPATH.'uploads/');
//define('VIDEO_CHAT_URL',HOSTPATH.'video/chat/chat.php#');
//define('VIDEO_CHAT_URL',HOSTPATH.'video/chat/index.html#');
define('VIDEO_CHAT_URL','https://appr.tc/r/');
$gender = array(1=>'Male',2=>'Female',3=>'Others');
define('UPLOAD_LOG_FILE_PATH',UPLOAD_REPORT_FILE_PATH.'uploadLogfiles/');

define('IPFS_BASE_URL','https://ipfs.io/ipfs/');



define('SMS_API_URL','https://api.textlocal.in/send/');

define('API_KEY','AAeu7S9y5aE-WNm9yWRAaqc9I12XX53IhTQ7GtZv7t');

define('API_SENDER_ID','TXTLCL');
define('WEBSITE_STATIC_IMAGE_PATH',WEBSITE_PUBLIC_URL.'/images/');

define('TOTAL_RATING',5);

define('DATE_FORMAT_FRONT','d/m/Y');
$arrGender = array('m' =>'Male','f' =>'Female','o' => 'Others');
$arrBloodGroup = array('ap' =>'A+','an'=>'A-','bp'=>'B+','bn'=>'B-','abp'=>'AB+','abn'=>'AB-','op'=>'O+','on'=>'O-');
$arrRelation = array('so' =>'S/O','wo'=>'W/O','ho'=>'H/O','mo'=>'M/O');

?>
