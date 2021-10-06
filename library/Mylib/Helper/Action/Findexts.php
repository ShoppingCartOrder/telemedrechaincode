<?php
/**
 * Calling action helper in controller: $this->_helper->Findexts();
 */
class My_Action_Helper_Findexts extends Zend_Controller_Action_Helper_Abstract
{
	public function direct($filename)
	{
		$filename = strtolower($filename) ;
		$exts = split("[/\\.]", $filename) ;
		$n = count($exts)-1;
		$exts = $exts[$n];
		return $exts;
	}
}