<?php

class Application_Form_Captcha extends Zend_Form
{
    public function init()
    {
               
        // Add a captcha
        $this->addElement('captcha', 'captcha', array(
            'label'      => 'Please enter the 5 letters displayed below:',
            'required'   => true,
            'captcha'    => array(
                'captcha' => 'Figlet',
                'wordLen' => 5,
                'timeout' => 300
            )
        ));
        
         $this->addElement('hidden', 'redirect_url', array(    
            'required'   => false,
                        
        ));
         
        // Add the submit button
        $this->addElement('submit', 'submit', array(
            'ignore'   => true,
            'class'=>'btn-pink marg-top2',
            'label'    => 'Submit',
        ));
      
    }
}