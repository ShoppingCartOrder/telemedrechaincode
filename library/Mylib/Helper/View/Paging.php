<?php

/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage Helper
 */
class Mylib_Helper_View_Paging extends Zend_View_Helper_Abstract
{
    /**
     * Check user favorite vendors
     * @return extension
     */
    
    public function Paging($page,$totalpage)
    {
        if(!empty($page) && !empty($totalpage)){
			if ($page <= 4) {
				$next = $page + 1;
				$prev = $page - 1;
				$startPaging = 1;
				$endPaging = 5;
				$first = 1;
			} else {
				$next = $page + 1;
				$prev = $page - 1;
				$startPaging = $page - 2;
				$endPaging = $page + 2;
				$first = 1;
			}
			if ($endPaging > $totalpage) {
				if ((int) $totalpage == $totalpage) {
					$endPaging = $totalpage;
				} else {
					$endPaging = (int) $totalpage;
					$endPaging = $endPaging + 1;
				}
			}
			
			$str = '';
			if ($page > 1) { 
				$str .= '<li class="pre"><a href="javascript:void(0);" onclick=\'javascript:filtersearch("page='.$prev.'")\'>&lt;</a></li>';
			}
			for ($i = $startPaging; $i <= $endPaging; $i++) {
				if ($i == $page) {
					$str .= '<li><a href="javascript:void(0);" class="active-lp pagination-lp">' . $i . '</a></li>';
				} else {
					$str .= '<li><a href="javascript:void(0);" onclick=\'javascript:filtersearch("page='.$i.'")\'>' . $i . '</a></li>';
				}
			}
			if ($page < $endPaging) {
				$str.= '<li class="next"><a href="javascript:void(0);" onclick=\'javascript:filtersearch("page='.$next.'")\'> &gt; </a></li>';
			}
			return $str;
		}
        
    }

}