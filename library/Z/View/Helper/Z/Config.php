<?php

class Z_View_Helper_Z_Config extends Zend_View_Helper_Abstract
{
	public function z_config($sid)
	{
		return Z_Config::val($sid);
	}
}

?>