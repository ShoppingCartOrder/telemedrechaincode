<?php

/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage Helper
 */
class Mylib_Helper_View_IdeabookDataCount extends Zend_View_Helper_Abstract {

    /**
     * Check user favorite vendors
     * @return extension
     */
    public function IdeabookDataCount($uid,$section) {
        if (!empty($uid)) {
            $this->_ideabookResource = new Application_Model_DbTable_Ideabook();
            $seleted = '';
            $boardText = '';
            $inspirationText = '';
            $inspirationCount = '';
            $inspirationImages = $this->_ideabookResource->fetchInspirationImagesCount($uid);
            $userIdeabookCount = $this->_ideabookResource->fetchUserIdeaBookCount($uid);
            if (isset($inspirationImages) && !empty($inspirationImages)) {
                $inspirationCount = count($inspirationImages);
            }else{
                $inspirationCount = '0';
            }
            if(count($inspirationImages) > 1){
                $inspirationText = 'Inspirations';
            }else{
                $inspirationText = 'Inspiration';
            }
            if($userIdeabookCount[0] > 1){
                $boardText = 'Boards';
            }else{
                $boardText = 'Board';
            }
            if($section == 1){ $seleted= "active" ;}else{$seleted= "" ;}
            if($section == 2){$seletedIns= "active" ;}else{$seletedIns= "" ;}
            $html = '';
            $html.= "<div class='insp-detail-right'><div class='div-both insp-detail-right2'><a href='".WEBSITE_URL . "idea-book/index/id/" . base64_encode($uid)."'><h1 class='insp-detail-right3 $seleted'><span> <font style='font-size:32px;'>".$userIdeabookCount[0]."</font> ".$boardText."</span></h1></a></div>
            <div class='insp-detail-right4'><a href='".WEBSITE_URL . "idea-book/inspirations/id/" . base64_encode($uid)."'><h1 class='insp-detail-right3 $seletedIns'><span> <font style='font-size:32px;'> ".$inspirationCount."</font> ".$inspirationText."</span></h1></a></div><div style='clear: both;'></div>
           </div>";
            
            return $html;
        }
    }

}
