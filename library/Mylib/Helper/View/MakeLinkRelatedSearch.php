<?php

/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage Helper
 */
class Mylib_Helper_View_MakeLinkRelatedSearch extends Zend_View_Helper_Abstract {

    protected $_base_url = '';
    protected $_controller_name = '';
    protected $_city_name = '';

    /**
     * Check user favorite vendors
     * @return extension
     */
    public function makeLinkRelatedSearch($data, $class) {

        $lists = array_chunk($data, 3);
        $number = 1;
        $link = '';
        foreach ($lists as $items) {
            $link .= "<ul class='content-listing-column-one list-reset'>";
            foreach ($items as $item) {
                $link .="<li><a href=\"" . $item['url'] . "\">" . $item['name'] . "</a></li>";
            }
            $link .="</ul>";
        }
        return $link;
    }

}
