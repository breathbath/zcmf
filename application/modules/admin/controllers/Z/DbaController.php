<?php
class Admin_Z_DbaController extends Zend_Controller_Action
{
	public function init(){
		if (Z_Auth::getInstance()->getUser()->getRole()!='root')
			$this->_redirect('/admin');

	}
	public function indexAction()
	{
	Zend_Layout::getMvcInstance()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
     function adminer_object() {
    // required to run any plugin
    //http://cimb.me/sys/adminer/db.php?server=localhost&username=root&db=cimb
    include_once "Z/Adminer/plugin.php";
    include_once "Z/Adminer/plugins/Dbadmin.php";
    $plugins = array(
        // specify enabled plugins here
        new Dbadmin
    );
    
    /* It is possible to combine customization and plugins:
    class AdminerCustomization extends AdminerPlugin {
    }
    return new AdminerCustomization($plugins);
    */
    
    return new AdminerPlugin($plugins);
	}    
	  include "Z/Adminer/adminer-3.3.4.php";
	  //phpinfo();	
	}	
}
?>