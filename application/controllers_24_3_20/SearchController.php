<?php

class SearchController extends Mylib_Controller_BaseController {

    //protected $_dashboardResource;

    public function init() {
        parent::init();
        $this->_patientResource = new Application_Model_DbTable_Patients();
        $this->_userResource = new Application_Model_DbTable_Users();
        $this->_searchResource = new Application_Model_DbTable_Search();
        $this->_helper->layout->setLayout('innerlayout');
    }

    public function indexAction() {

        // global $status;
        $request = $this->getRequest();
        $data = $request->getParams();
        //dd($data);
        if ($data = $request->getPost()) {



            $data = $this->_searchResource->searchByCategory($params);
        }
    }

    public function doctorListAction() {

        // global $status;
        $request = $this->getRequest();
        //$uri = $request->getRequestUri();

        $data = $request->getParams();
        //dd($data);
        if (isset($data['speciality'])) {
            $this->view->speciality = $speciality = $data['speciality'];
            $speciality = str_replace("-", " ", $speciality);
            
            if (isset($data['page'])) {
                $page = $data['page'];
                $this->view->currentPageNo = $page;
            } else {
                $this->view->currentPageNo = 1;
                $page = 1;
            }
            
            $limit = SEARCH_DEFAULT_COUNT;
            
            $params['fields']['main'] = array('*');
            
            $params['rows'] = $limit;
            $params['start'] = $page * $params['rows'];
            $params['page'] = $page;
            $params['condition']['speciality'] = $speciality;
            $data = $this->_searchResource->searchBySpecialization($params);
            //dd($data);
            $this->view->searchData = $data['result'];
            $this->view->searchCount = $count = $data['records'];
            $this->view->page = $page;
            $this->view->limit = $limit;
            $this->view->num_of_pages = $data['total'];
            $this->view->currenturl =  HOSTPATH.ltrim($request->getRequestUri(),'/');
        
        }
    }

}

?>