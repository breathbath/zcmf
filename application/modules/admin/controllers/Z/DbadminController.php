<?php
class Admin_Z_DbadminController extends Z_Admin_Controller_Action
{
	public function init(){
		if (Z_Auth::getInstance()->getUser()->getRole()!='root')
			$this->_redirect('/admin');

	}
	
	public function indexAction()
	{
	$this->disableRenderView();
	  echo $this->view->admin_Head('Управление базой данных');
	  echo $this->view->admin_Bodybegin();
	 /*
 echo '<a onClick="window.open(\'/admin/z_dba?server=localhost&username=root&db=cimb\',\'mywindow\',\'width=800,height=600\');">Open new window</a>';
	 
*/  
$config = Zend_Registry::get('config')->resources->db->params;
		$dbhost = $config->get('host','localhost');
		$dbuser = $config->get('username','root');
		$dbname=$config->get('dbname','');
echo '<a class="z-button z-button-top z-additional-button  ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false" href="/admin/z_dba?server='.$dbhost.'&username='.$dbuser.'&db='.$dbname.'" target="_blank"><span class="ui-button-text">Открыть редактор в новом окне</span></a><br/><br/><br/><br/><br/><br/><br/>'; 
	  echo $this->view->admin_Bodyend();	
	}
	

	
}
?>

