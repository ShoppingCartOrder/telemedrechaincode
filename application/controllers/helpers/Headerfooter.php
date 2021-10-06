<?php
class Zend_Controller_Action_Helper_Headerfooter extends Zend_Controller_Action_Helper_Abstract
{
function allcategory()
{  
	$this->_searchResource = new Application_Model_DbTable_Search();
	return  $this->_searchResource->allCategory();
}
}