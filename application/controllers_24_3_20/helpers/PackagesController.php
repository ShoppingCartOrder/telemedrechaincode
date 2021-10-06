<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class PackagesController extends Mylib_Controller_BaseController {
    
    /**
     * This method is used to initialize the contactus action 
     * Created By : Praveen Kumar
     * Date : 27th May 2015
     * @param void
     * @return void
     */
    public function init() {
        parent::init();       
        $this->initMsg('ABOUTUS');
        $this->_contactUsResource = new Application_Model_DbTable_Contactus;
        $this->view->currenturl =  HOSTPATH.ltrim($this->getRequest()->getRequestUri(),'/');
        $this->view->doctype('XHTML1_RDFA');
    }
    
    public function indexAction() {
        $this->_helper->layout->setLayout('layout_online_packages');
        
        $this->view->keywords = $this->frmMsg['ABOUTUSKEYWORD'];
        $this->view->title = $title = $this->frmMsg['ABOUTUSTITLE'];
        $this->view->headTitle()->set($title);
        $this->view->description = $this->frmMsg['ABOUTUSDESCRIPTION'];
        
     }
}