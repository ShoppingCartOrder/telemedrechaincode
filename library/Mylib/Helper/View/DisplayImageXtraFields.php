<?php

/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage Helper
 */
class Mylib_Helper_View_DisplayImageXtraFields extends Zend_View_Helper_Abstract {

    /**
     * Check file name
     * @return extension
     */
    public function DisplayImageXtraFields($allImageFileName) {


        $imgStr = '';
        //$extraImgPath = VENDOR_EXTRA_IMAGE_PATH;
        $extraImgPath = EXTRAMAIN;
        $extraImgThumbPath = EXTRATHUMB;
        if (!empty($allImageFileName)) {
            $allImageFileArr = explode(",", $allImageFileName);
            if (!empty($allImageFileArr[0])) {
                //$imgSrc =  $extraImgPath . $allImageFileArr[0];
                //$imgStr .= "<div style = \"\"><img id = 'xtraImg' src = \"$imgSrc\"></div>";
            }
            /* if(count($allImageFileArr)>0) {
              $imgStr .= "<div class='lp-portfolio'>";



              foreach($allImageFileArr as $img) {
              $imgStr .="<div class='portfolio logo menu-cateres' data-cat=\"$img\">";
              $nextImgSrc =  $extraImgPath.'/'.$img;
              //                    $nextImgThumbSrc =  $extraImgThumbPath.'/'.$img;
              //
              $imgStr .="<span class='popup-gallery-pop'>";
              //                    if($cloudnary['enable']){
              //                        $mainImgUrl = cloudinary_url($cloudnary['main_dir'].CLOUD_VENDOR_EXTRA_IMAGE.$img, array('overlay' => "watermark",
              //                                                'gravity' => 'south_east','x' => 5,'y' => 5,"transformation" =>
              //                                                   array( "crop" => "fill", "height" => 405, "width" => 540 )));
              //                        $thumbImg = cl_image_tag($cloudnary['main_dir'].CLOUD_VENDOR_EXTRA_IMAGE.$img, array("width" => 55, "height" => 55));
              //                        $imgStr .= "<a href =$mainImgUrl title = \"$img\">$thumbImg</a>";
              //                    }else{
              //                        $imgStr .= "<a href = \"$nextImgSrc\" title = \"$img\"><img height='133' width='182' src = \"$nextImgSrc\"></a>";
              //                    }
              //$i++;
              $imgStr .= "<img style='display:inline' src = \"$nextImgSrc\">";
              $imgStr .= "</span></div>";
              }

              $imgStr .= "</div>";
              } */
            if (count($allImageFileArr) > 0) {
                $imgStr .= "<div class='bg-slider'><div class='bxslider1'>";



                foreach ($allImageFileArr as $img) {

                    $nextImgSrc = $extraImgPath . '/' . $img;

                    $imgStr .= "<img style='display:inline' src = \"$nextImgSrc\">";
                }

                $imgStr .= "</div></div>";
            }
        }
        //echo $imgStr; die;
        return $imgStr;
    }

}
