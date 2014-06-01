<?php

class Admin_Z_UpdatesController extends Z_Admin_Controller_Action
{
	public function indexAction()
	{

		$items=null;
		$updates = new Z_Updates();
		$arr = $updates->getUninstalled();
		//var_dump($arr);
		if(is_array($arr)&&count($arr))
		{
			foreach ($arr as $key=> $ar)
			{
				$date = null;
				preg_match('#([0-9]{1,2})_([0-9]{1,2})_([0-9]{4}).*#i', $key, $date);
				$date_str = '';
				if(count($date))
				{
					$date_part=@array_slice($date, 1, 3);
					if(count($date_part))
						$date_str=implode('.',$date_part);
				}
				$items[]=array('when'=>$date_str,'text'=>$ar,'file'=>$key);
			}	
		}
		$this->view->items = $items;
	}

	public function installAction() {


		$this->disableRenderView();
        
        $data = $this->_request->getPost();
        
        
        if (!empty($data) && isset($data['updates'])) {
        	$updates = new Z_Updates();
        //	var_dump($data);
        
        	foreach ($data['updates'] as $update){
        		//echo $update;
        		echo $updates->install($update);
        		//break;
        		//	$updates->addEl('','');
        	}
        	
        
        }

    }



}

