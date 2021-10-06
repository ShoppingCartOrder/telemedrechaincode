<?php

/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage Helper
 */
class Mylib_Helper_View_instaBookingFields extends Zend_View_Helper_Abstract {

    /**
     * 
     * @return extension
     */
    public function instaBookingFields($type, $data) {//echo '<pre>'; print_r($type);
        switch ($type) {
            case '1':
                $field = null;
                
               
                    $field .= '<div class="form-group" style="margin:6px 0 19px; padding-left: 36px;"><span class="fl-lt field-box-pop fill-underline"><input id="' . $data['field_name'] . '" type="text" name="' . $data['field_name'] . '" placeholder="' . $data['placeholder'] . '" value="" /></span></div> <br>';
               

                return $field;
                break;

            case '2':
                //print_r($data); die;
                $field = null;
                
                    $field .= '<div class="fl-lt field-box-pop fill-underline-area" style="margin-top:30px; margin-left: 20px;"><textarea required data-msg="please fill text" class="pop-textarea ' . $data['field_name'] . '" id="' . $data['field_name'] .'" type="text" name="' . $data['field_name'] . '" placeholder="' . $data['placeholder'] . '" ></textarea></label></span></div> <br>';
                

                return $field;
                break;
            case '3':
                $field = null;
                foreach ($data as $key => $value) {
                    $field .= '<span id="checkbookArray"><div class="form-group" style="margin:6px 0 19px; padding-left: 36px;"><span class="check-box intacheck"><label class="checkbox font0">' . $value['question_option'] . ' <input type="checkbox" onClick = "setChecboxValue(this.value,this);" data-msg="please check checkbox" required checkbox-valid class="services-offered ' . $value['field_name'] . '" id="' . str_replace(' ','_',$value['question_option']) . '-' . $key . '" name="' . $value['field_name'] . '[]' . '" value="' . $value['option_id'] . '"  /><i></i></label></span><span class="instachek1">' . $value['question_option'] . '</span></div></span> <br>';
                }

                return $field;
                break;
            case '4':
                $field = null;
                foreach ($data as $key => $value) {
                    $field .= '<div class="form-group" style="margin:6px 0 19px; padding-left: 36px;"><span class="radio-box intacheck"><label class="radio font0"><input onClick = "setRadioValue(this.value,this);" type="radio" required data-msg="please check radio button" data-label ="'.$value['question_option'].'" id="' . $value['field_name'] . '_' . $value['option_id'] . '" class="search-extra-fields ' . $value['field_name'] . '" name="' . $value['field_name'] . '" value="' . $value['option_id'] . '"  /><i></i></label></span><span class="instachek1"> ' . $value['question_option'] . '</span></div> <br>';
                }

                return $field;
                break;
            case '5':
                $field = null;
                echo '<select name="' . $nameAtt . '">';
                foreach ($data as $key => $value) {
                    $field .= '<option value="' . $value['option_id'] . '">' . $value['question_option'] . '</option>';
                }

                return $field . '</select>';
                break;

            case '6':
                $field = null;
                
                $field .= '<div class="form-group" style="width:300px; margin:43px 144px 54px 40px; height:25px; border-bottom: 1px solid #ccc;"><span class="fl-lt field-box-pop fill-underline"><input class="input-txt ' . $data['field_name'] . '" id="contact_location" type="text" required data-msg="Please fill this field" name="' . $data['field_name'] . '" placeholder="' . $data['placeholder'] . '" value="" onblur="locationCheck()" /></span></div> <br>';
                

                return $field;
                break;

            case '7':
                $field = null;
                
                $field .= '<div class="form-group" style="margin:6px 0 19px; padding-left: 36px;"><span class="fl-lt field-box-pop fill-underline"><input class="input-txt-cal ' . $data['field_name'] . '" id="wedding-date" required readonly data-msg="Please fill this field" type="text" name="' . $data['field_name'] . '" placeholder="' . $data['placeholder'] . '" value="" /><div id="date-input"></div></span></div> <br>';
                

                return $field;
                break;
            default:
                echo 'No list has been specified';
                break;
        }
    }

}
