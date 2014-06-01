<?php
/**
 *
 * @author cramen
 * @version 
 */
require_once 'Zend/View/Interface.php';


/**
 * Menu helper
 *
 * @uses viewHelper Zend_View_Helper
 */
class Zend_View_Helper_Buildurl
{

	/**
	 * @var Zend_View_Interface 
	 */
	public $view;
        
	public function buildurl ($str)
	{	
          $result = parse_url($str);
          if($result===false)
              return '#';
          if(isset($result['scheme']))
              return $str;
          $str='http://'.$str;
          return $str;
	}

	/**
	 * Sets the view field 
	 * @param $view Zend_View_Interface
	 */
	public function setView (Zend_View_Interface $view)
	{
		$this->view = $view;
	}
	
        
     
          
            
}

