<?php

/**
 * This document defines Action_Helper class
 *
 * @package Zend_View_Helper_Abstract
 * @copyright Copyright 2010-2011 Vivahaayojan
 */

/**
 * This is My_Action_Helper_FormatDate class. This class will
 * execute all the request to change date format.
 *
 * @author Neeraj Chandola
 * @package Zend_Controller_Action_Helper_Abstract
 * @subpackage My_Action_Helper_FormatDate
 * Calling action helper in controller: $this->_helper->FormatDate();
 */
class My_Action_Helper_FormatDate extends Zend_Controller_Action_Helper_Abstract
{

    /**
     * This function will be use to format date given by user into output format specified.
     *
     * @param string $input_date Input date string given by user
     * @param string $input_date_format Input date string format given by user. Note: Format parameter wiil based on Zend_Date defined constants.
     * @param string $ouput_date_format Output date format specified by user. Note: Format parameter wiil based on Zend_Date defined constants.
     *
     * @return string Date string in output format.
     */
    public function direct($input_date = '', $input_date_format = '', $output_date_format)
    {
        $date = new Zend_Date();
        if ($input_date != '0000-00-00 00:00:00' && $input_date != '') {
            $date->set($input_date, $input_date_format); // To set the given date in format given by user

            return $date->get($output_date_format); //to get the string in output format.
        } else {
            return $date->get($output_date_format); //to get the string in output format.
        }
    }

    public function dateDiff($start_date, $end_date = '', $output_format = '')
    {
        $date = new Zend_Date();
        if ($input_date != '0000-00-00 00:00:00' && $input_date != '') {
            $date->set($input_date, $input_date_format); // To set the given date in format given by user

            return $date->get($output_date_format); //to get the string in output format.
        } else {
            return $date->get($output_date_format); //to get the string in output format.
        }
    }

}
