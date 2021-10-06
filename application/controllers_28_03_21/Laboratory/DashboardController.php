<?php

class Laboratory_DashboardController extends Mylib_Controller_LaboratorybaseController {

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
        $session = new Zend_Session_Namespace('mylaboratory');
        
        $this->view->lastlogin = $session->lastlogin;
        $this->view->loginIp = $session->loginip;

    }

 

    
    
 }

?>