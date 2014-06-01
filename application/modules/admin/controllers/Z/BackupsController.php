<?php

class Admin_Z_BackupsController extends Z_Admin_Controller_Action
{
	public function listAction()
	{

		$path = APPLICATION_PATH.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'backups'.DIRECTORY_SEPARATOR;
		if(file_exists($path)){
			$filesIn = Z_Fs::rscandir($path);
			$result=null;
			foreach ($filesIn as $fileIn) {
				$pi=pathinfo($fileIn);
				//backup_" . date("m-d-Y_H-i") . ".tar")
				
				if(isset($pi['extension'])&&$pi['extension']=='tar'&&isset($pi['basename']))
				{
					$pattern = "#backup\_(\d+\-\d+\-\d{2,4}\_\d+\-\d+)#";
					$matches=array();
					preg_match($pattern, $pi['basename'], $matches);
					if(isset($matches[1]))
					{
						$name= base64_encode($pi['basename']);
						$date = new Zend_Date($matches[1], 'MM-dd-yyyy_h-m');
						$result[]=array('name'=>$name,'date'=>$date->get(Zend_Date::DATETIME_MEDIUM), 'size'=>Z_Fs::getFormattedFileSize($fileIn));
					}	

				}
            }
            $this->view->backups = $result;
        }    
	}
	


}

