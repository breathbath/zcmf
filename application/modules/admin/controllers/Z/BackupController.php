<?php
class Admin_Z_BackupController extends Zend_Controller_Action
{
 	
public function init() {
 		if(Z_Auth::getInstance()->getUser()->getLogin()=='guest')
            {  
                $this->_redirect('/admin');
                return;
            }  
		$role = Z_Auth::getInstance()->getUser()->getRole();
			$acl = Z_Acl::getInstance();
			$allowed = true;
			try {
				$allowed = $acl->isAllowed($role,'z_backups','list');
			}
			catch (Exception $e)
			{
				$allowed = false;
			}
			if (!$allowed)
			{
				 $this->_redirect('/admin');
				 return;
			}
		if($this->_getParam('action')!='download'&&!$this->getRequest()->isXmlHttpRequest())
		{
			$this->_redirect('/admin');
			return;
		}	
        
		$this->_helper->viewRenderer->setNoRender(true);
  		Zend_Layout::getMvcInstance()->disableLayout();
        @ini_set("memory_limit", "-1");
        $this->session = new Zend_Session_Namespace("z_backup");
    }

    public function initAction() {
    	$backup_dir=APPLICATION_PATH.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'backups';

       $config = Zend_Registry::get('config');
  		$res = $config->get('site');
		if ($res)
		{
			$res = $res->get('backup');
			if ($res)
				$backup_dir = $res->get('directory');
		}	
		       
       $dn=$this->view->serverUrl();
       $dn = str_replace('http://www.', '',$dn);
       $dn = str_replace('https://www.', '',$dn);
       $dn = str_replace('http://', '',$dn);
       $dn = str_replace('https://', '',$dn);
       $dn = str_replace('.', '_',$dn);	
        $backup = new Z_Backup($backup_dir . "/backup_" . date("m-d-Y_H-i") .'_'.$dn. ".tar");
        $initInfo = $backup->init();
        $this->session->backup = $backup;        
        $this->_helper->json($initInfo);
    }

    public function filesAction() {

        $backup = $this->session->backup;
        $return = $backup->fileStep($this->_getParam("step"));              
        $this->session->backup = $backup;
                                
        $this->_helper->json($return);
    }

    public function mysqlTablesAction() {

        $backup = $this->session->backup;
        $return = $backup->mysqlTables();              
        $this->session->backup = $backup;
                                
        $this->_helper->json($return);
        
    }

    public function mysqlAction() {

        $name = $this->_getParam("name");
        $type = $this->_getParam("type");
        
        $backup = $this->session->backup;
        $return = $backup->mysqlData($name, $type);              
        $this->session->backup = $backup;
                                
        $this->_helper->json($return);
    }

    public function mysqlCompleteAction() {

        $backup = $this->session->backup;
        $return = $backup->mysqlComplete();              
        $this->session->backup = $backup;
                                
        $this->_helper->json($return);
    }

    public function completeAction() {

        $backup = $this->session->backup;
        $return = $backup->complete();              
        $this->session->backup = $backup;
                                
        $this->_helper->json($return);
    }    
	
	
	public function downloadAction() {
        $path = APPLICATION_PATH.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'backups'.DIRECTORY_SEPARATOR;
        $id=$this->_getParam('id');
        $fname='';
        if($id)
        {
        	$fname=base64_decode($id);
        }
        else
        {
        	 $this->_forward('z-backups','list');
	 		return;
        }
        if(file_exists($path.$fname))
        {
            //    header("Content-Disposition: inline; filename=\"" . $filename . ".xls\"");
        	//header("Content-Type: application/tar");
        	//header('Content-Disposition: attachment; filename="' . $fname . '"');
        	//readfile($path.$fname);
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename='.basename($path.$fname));
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($path.$fname));
    ob_clean();
    flush();
    readfile($path.$fname);
    exit;
 	    }   
 	    else
 	    {
 	    	 $this->_forward('z-backups','list');	
 	    }
    }   

}

