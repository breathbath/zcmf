<?php

class Z_View_Helper_FormHtml extends Zend_View_Helper_FormElement
{
 
    public function FormHtml($name, $value = null, $attribs = null)
    {
        $info = $this->_getInfo($name, $value, $attribs);
        extract($info); // name, value, attribs, options, listsep, disable

        // build the element
        $xhtml = '<div name="' . $this->view->escape($name) . '"'
                . ' id="' . $this->view->escape($id) . '"'
                . $this->_htmlAttribs($attribs) . '>'
                . $value . '</div>';
        return $xhtml;
    }
}
