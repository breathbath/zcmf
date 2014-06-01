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
class Zend_View_Helper_Datformat
{

	/**
	 * @var Zend_View_Interface 
	 */
	public $view;

	/**
	 * 
	 */
	public function datformat ($input,$format='short')
	{
		$vDate = new Zend_Date($input);
		if ($format=='short')
  			return $vDate->get(Zend_Date::DATE_MEDIUM);
  		elseif ($format=='long') 
  			return $vDate->get(Zend_Date::DATE_LONG);
                elseif ($format=='timeshort') 
  			return $vDate->get(Zend_Date::DATETIME_SHORT);
                elseif($format=='timelong')
                        return $vDate->get(Zend_Date::DATETIME_LONG);
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
