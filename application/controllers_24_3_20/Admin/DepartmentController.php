<?php

class Admin_DepartmentController extends Mylib_Controller_AdminbaseController {

    //protected $_dashboardResource;

    public function init() {
        parent::init();
        $this->_departmentResource = new Application_Model_DbTable_Departments();
        $this->_userResource = new Application_Model_DbTable_Users();
    }

    /**
     * This function is used to show all function in dashboard 
     * Created By :  Umesh
     * Date : 20 May,2014
     * @param void
     * @return void
     */
    public function indexAction() {

       global $nowDateTime;
        global $monthArray;
        $request = $this->getRequest();
        global $noRecord;
        if ($request->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $data = $this->getRequest()->getPost();
            $params = $this->getSearchParams($data);
            
            
            $params['fields']['main'] = array('*');
            $params['sidx'] = 'id';
            $params['sord'] = 'DESC';
          
            $result = $this->_departmentResource->allDepartments($params);
            
            $responce = new stdClass();
            $responce->page = $result['page'];
            $responce->total = $result['total'];
            $responce->records = $result['records'];


            foreach ($result['result'] as $k => $val) {
                $statusUpdate = '';
                $actionUpdate = '';
                $id = $val['id'];
                $responce->rows[$k]['id'] = $val['id'];

                $responce->rows[$k]['cell'] = array(
                    $val['id'],
                    $val['name'],                   
                    $val['created_at']
                   
                );
            }
            echo $this->jsonEncode($responce);
            exit;
        }
    }

    public function viewprofileAction() {

        $request = $this->getRequest();

        if ($request->getParam('s') == 1) {
            $this->view->savemsg = 1;
        }
        require_once 'My/Acl.php';
        $session = new Zend_Session_Namespace('mydoctor');
        $this->view->userAccountInfo = $this->_dashboardResource->fetchAdminUserInfo($session->loginId);
        $roleResourceObj = new Application_Model_DbTable_Role();
        $this->view->roleData = $roleResourceObj->fetchRoleData($this->view->userAccountInfo['0']['role']);
    }

    public function addeditAction() {
        global $status;

        $this->view->status = $status;
        $id = $this->getRequest()->getParam('id');
        if (!empty($id)) {

            $departmentData = $this->_departmentResource->getDepartmentById($id);
            //dd($departmentData);
            $formdata = array(
                'id' => $departmentData['id'],
                'name' => $departmentData['name']
                
            );

            $this->view->formdata = $formdata;
            
        }
    }


    public function savedepartmentAction() {
        
        // global $status;
        $request = $this->getRequest();
        if ($data = $request->getPost()) {
            //dd($data);
            $params = $this->__inputPostData;
            //print_r($params); die;
            //$response = array('status' => 0, 'msg' => '');
            $this->validateDepartmentData($params, $this->errMsg);
            $inputData = new Zend_Filter_Input($this->filters, $this->validators);
            $inputData->setData($params);
            if ($inputData->isValid()) {


                //dd($params);
                if (!empty($params['id'])) {

                    $update = $this->_departmentResource->updateDepartmentData($params);

                    if ($update) {
                        $this->_helper->FlashMessenger()->setNamespace('success')->addMessage('Department record successfully updated.');
                        
                        //$this->_redirect(ADMIN_BASE_URL . 'department/addedit/id/'.$params['id']);
                        
                    } else {
                        //$this->view->msg = 'Patient record not updated.';
                        $this->_helper->FlashMessenger()->setNamespace('error')->addMessage('Department record not updated.');                  
            
                    }
                    $this->_redirect(ADMIN_BASE_URL . 'department/index');
                } else { //Save new Patient


                    

                            $dprtData = array(
                                
                                'name' => $params['p_name'],
                                
                            );

                            $insertedId = $this->_departmentResource->addDepartment($dprtData);
//dd($insertedId);
                            if ($insertedId) {

                                $this->_helper->FlashMessenger()->setNamespace('success')->addMessage('Department record successfully saved.');
                            } else {
                                $this->_helper->FlashMessenger()->setNamespace('error')->addMessage('Department record is not inserted.Please try again.');                  
            
                               /// $this->view->msg = 'Patient record is not inserted.Please try again.';
                            }
                       
                   // }

                    $this->_redirect(ADMIN_BASE_URL . 'department/addedit');
                }
                } else {

                $msg = $this->getValidatorErrors($inputData->getMessages(), 'admin');
                
                $this->_helper->FlashMessenger()->setNamespace('error')->addMessage($msg);                  
                
                if($params['id']){
                    $this->_redirect(ADMIN_BASE_URL . 'department/addedit/id/'.$params['id']);
                }else{
                    $this->_redirect(ADMIN_BASE_URL . 'department/addedit');
                }
                
                //$this->view->msg = $msg;
            }
        }
        
        die;

    }

  

    protected function validateDepartmentData($data, $errMsg) {

        if (isset($data['p_name'])) {
            $this->validators['p_name'] = array(
                'NotEmpty',
                'messages' => array('Please enter department name.'));
        }



 
    }
    
    
    
    
    
    public function deletedepartmentAction() {
        global $nowDateTime;
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $request = $this->getRequest();
        if ($request->getPost()) {
            $data = $this->__inputPostData;
            
            
            $params['status'] = 0;
            $params['updated_at'] = $nowDateTime;
            $deleteRole = $this->_departmentResource->updateDepartmentDetail($params,$data['id']);

            
            
            echo $deleteRole;
        }
        die;
    }

}

?>