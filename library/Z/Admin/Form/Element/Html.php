<?php

class Z_Admin_Form_Element_Html extends Zend_Form_Element_Xhtml {

    public $helper = 'formHtml';
public function init()
    {
        $this->clearDecorators()
             ->addDecorator('ViewHelper');
    }
    public function isValid($value, $context = null) {
        return TRUE;
    }

}
