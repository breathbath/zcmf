<?php

class Z_Updates {

	/**
	 * 
	 * @var Z_Db_Table
	 */
	protected $_model = NULL;
        protected $_data = NULL;
        protected $_url=null;
	protected $_meta=null;
	protected $_files=null;
	/**
	 * 
	 * @var Zend_Db_Table_Row
	 */
	protected $_row = NULL;
	
	public function __construct()
	{
		$this->_model = new Z_Model_Updates();
		$config = Zend_Registry::get('config')->site;
		$this->_url = $config->get('update','http://www.made7.ru/update_server/');
	}

	/**
	 * @return string
	 */
	public function getUninstalled()
	{
	    try {
	    	
			if(!$this->_url)
				return null;	
	
			$http = new Zend_Http_Client($this->_url);
			$response = $http->request();
			$items = array();
			if ($response->isSuccessful())
			{
				$res=$response->getBody();
				$array = @unserialize($res);
				if($array&&is_array($array)&&count($array))
				{
		        	//var_dump($array);
		        	$files = array_keys($array);
        			$filter_data = trim(implode(',', $files));
        			$filter_data=trim(preg_replace('/\,\z|\A\,/','',$filter_data));
        			if($filter_data)
        			{		
        				//var_dump($files);
        				$filter['uid IN(?)']=$files;
        				$rows=$this->_model->fetchAll($filter);
        				$result = explode(',',$filter_data);
        				if(count($rows))
        				{
        					foreach ($rows as $row)
        					{
        						$found=array_search($row->uid, $result);
        						if($found!==false)
        						{
        							if(isset($array[$result[$found]]))
        								unset ($array[$result[$found]]);
        						}
        					}
        				}
        			}
        			
        			function cmp($a, $b)
					{
    					
    					$date=$dateA=$dateB = null;
						preg_match('#([0-9]{1,2})_([0-9]{1,2})_([0-9]{4}).*#i', $a, $date);
						$date_str = '';
						if(count($date))
						{
							$date_part=@array_slice($date, 1, 3);
							if(count($date_part))
							{	
								$date_str=implode('.',$date_part);
								$dateA=new Zend_Date($date_str);
							}	
								
						}
						$date=null;
						preg_match('#([0-9]{1,2})_([0-9]{1,2})_([0-9]{4}).*#i', $b, $date);
						$date_str = '';
						if(count($date))
						{
							$date_part=@array_slice($date, 1, 3);
							if(count($date_part))
							{	
								$date_str=implode('.',$date_part);
								$dateB=new Zend_Date($date_str);
							}	
								
						}
						if($dateA&&$dateB){
							if($dateA->equals($dateB,Zend_Date::DATES))
								return 0;
							if($dateA->isEarlier($dateB,Zend_Date::DATES))
								return -1;
							if($dateA->isLater($dateB,Zend_Date::DATES))
								return 1;		
						}
						else
						{
    						if ($a == $b) {
        						return 0;
    						}
    						return ($a < $b) ? -1 : 1;
    					}	
					}
					uksort($array, "cmp");
					//var_dump($array);
/*
        			var_dump($array);
        			ksort($array);
        			var_dump($array);
*/
        			return $array;
        		}
        	}
        }
        catch (Zend_Http_Client_Exception $e)
		{
			Zend_Debug::dump($e->getMessage());
			return null;
		}					
        	return null;
	}
     
  	public function addEl($uid,$descr)
	{
           //echo $descr;
            $row=$this->_model->createRow(array('uid'=>$uid,'descr'=>$descr));
            $row->save();
	} 
	
	private function parse($filedata)
	{
		if(is_array($filedata)&&count($filedata))
		{
			foreach ($filedata as $fd)
			{
				$matches = null;
				preg_match('#<n>(.*)</n>#Uis', $fd, $matches);
				if(isset($matches[1]))
					$filename = $matches[1];
				preg_match('#</n>(.*)#is', $fd, $matches);
				if(isset($matches[1]))
					$filedata = $matches[1];
				//echo $filename;	
				//echo '<br/>';
				if($filename=='meta.data')
					//echo '<br/>'.($filedata);
					$this->_meta=@unserialize($filedata);
					//$this->xml->loadXML(base64_decode($filedata));
				else	
					$this->_files[$filename]=$filedata;		
			}
		}
	}
	
	public function install($file)
	{
		ini_set('pcre.backtrack_limit', '5000000');				
        if(!$this->_url||!$file)
			return null;
		try {
			$http = new Zend_Http_Client($this->_url.'?up='.$file);
			//echo $this->_url.'?up='.$file;
			$response = $http->request();
			if ($response->isSuccessful()){
				$res=$response->getBody();
				if($res){
					$out=$descr =null;
					preg_match('#<global>(.*)</global>#i',$res,$out);
                    if(isset($out[1]))
                       	$descr=$out[1];   	
					preg_match_all('#<f>(.*)</f>#Uis', $res, $out);
					//echo '<pre>';
					//var_dump($out[1]);
					if(isset($out[1]) && count($out[1]))
					{
						$this->parse($out[1]);
						//echo '1111';
						if(is_array($this->_meta)&&count($this->_meta))
						{
							$errors=false;
							echo '<h2><b>'.$file.'</b></h2>';
							echo '<ul>';
												
							foreach ($this->_meta as $operation)
							{
								if(isset($operation['action'])&&method_exists($this, $operation['action']))
				           	 	{	
				           	 		$result = $this->$operation['action']($operation);
				           	 		if($result)
				           	 		{
				           	 			echo '<li> (Success) '.$operation['action'].'->'.$operation['file'].'</li>';
				           	 		}	
				           	 		else
				           	 		{
				           	 			$errors=true;
				           	 			echo '<li> (Error) '.$operation['action'].'->'.$operation['file'].'</li>';
				           	 		}		
				           	 	}	
							}
							if(!$errors)
								$this->addEl($file,$descr);
							echo '</ul><br/>';
						}
		
					}
				}
        	}
        	//echo '<pre>';
        	//var_dump($this->_meta);
        }
        catch (Zend_Http_Client_Exception $e){
			return null;
		}					
        return null;	
	}
	
	private function create($input){
    //	echo '<pre>';
       //	var_dump($this->_files);
    	if (isset($input['path'])&&isset($input['file'])&&isset($this->_files[$input['file']]))
    	{
    		$data = base64_decode($this->_files[$input['file']]);
    		$filename = $this->buildFileName($input['path']);
    		if($filename && $data)
    		{
    			$result = $this->create_file($filename, $data,true);
    			if($result!==false)
    				return true;
    		}	
    	}	
    	return false;
    }
    private function php($input){
    	if (!isset($input['data']))
    		return false;	
    	try
    	{
    	 if(eval($input['data']))
    	 return true;
    	 else
    	 return false;
    	}
    	catch (Exception $e)
        {
            return false;
        }
    	

    }
    private function db($input){
    	//echo '1';
    	if (!isset($input['file'])||!isset($this->_files[$input['file']]))
    		return false;
    	$query = base64_decode($this->_files[$input['file']]); 	
    	$queries= $this->splitQueries($query);
    	//var_dump($queries);
    	try
    	{
        	foreach($queries as $key => $q)
        	{
           		$this->_model->getAdapter()->query($q);
        	}
                return true;
        }    
        catch (Exception $e)
        {
                return false;
        }
    }
    
    private function delete($input){
    	if (isset($input['path']))
    	{
    		$filename = $this->buildFileName($input['path']);
    		try
    		{
    			if(is_dir($filename))
    			{
    				$this->recursive_remove_directory($filename);	
    			}
    			elseif(file_exists($filename))
    			{
    					if(unlink($filename)==false)
    						return false;
    			}		
    			return true;	
    		}
    		catch (Exception $e)
        	{
                return false;
        	}
    		
    	}	
    	return false;	
    }
    
    private function replace($input){
    	if (!isset($input['data'])||!isset($input['path']))
    		return false;
    	try
    	{
        	$filename = $this->buildFileName($input['path']);
        	if(!file_exists($filename))
        		return false;	
        	$results=null;
        	$to=str_replace('\n', PHP_EOL,$input['data']);
    		preg_match_all('#{replace}(.*){pattern}(.*){/pattern}(.*){to}(.*){/to}(.*){/replace}#Uis', $to, $results);
    		if(!isset($results[2])||!isset($results[4]))
    			return false;
    		//var_dump($results[4]);
    		//return false;	
    		$content = file_get_contents($filename); 	
    		$content = preg_replace($results[2],$results[4],$content);
    		if(file_put_contents($filename, $content)!==false)	
            	return true;
            else
            	return false;	
        }    
        catch (Exception $e)
        {
                return false;
        }
    }
    
    private function add($input){
    	if (!isset($input['data'])||!isset($input['path']))
    		return false;
    	try
    	{
    		$filename = $this->buildFileName($input['path']);
        	if(!file_exists($filename))
        		return false;	
        	$content = file_get_contents($filename); 
        	$content.=$input['data'];
        	if(file_put_contents($filename, $content)!==false)	
            	return true;
            else
            	return false;	
    	}
    	catch (Exception $e)
        {
            return false;
        }
    	
    }
     
    private function buildFileName($file){
		$file=preg_replace('/\AAPPLICATION_PATH|\AAPP|\AAPPLICATION|\AAP/', APPLICATION_PATH, $file); 
		$file=preg_replace('/\ASITE_PATH|\ASP|\ASITE/', SITE_PATH, $file); 
		$file=preg_replace('/\Alibrary/', APPLICATION_PATH.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'library', $file); 
				//$file=str_replace('SITE_PATH', SITE_PATH, $file); 
				$file=str_replace('/', DIRECTORY_SEPARATOR, $file); 
				return $file;
	}
	protected function splitQueries($sql)
	{
        $sql = trim($sql);

        $sql = preg_replace('/(--.*\n)+/','',$sql);
        $sql = preg_replace('/\/\*!.+\*\/;/','',$sql);
        $res = preg_split('/;\n/',$sql);

        foreach($res as $key=>$el)
        {
            $res[$key] = trim($el,"\n");
            if (substr($el,0,2)=='--') unset($res[$key]);

        }

        return $res;
        
	}
	
	private function create_file($filename,$content="",$rewrite=false,$rights=0777)
  {
    global $m;
	try{
    $fileparts = explode(DIRECTORY_SEPARATOR,rtrim($filename,DIRECTORY_SEPARATOR));
	$pathparts = $fileparts;
	unset($pathparts[count($pathparts)-1]);
	$path = rtrim(implode(DIRECTORY_SEPARATOR,$pathparts),DIRECTORY_SEPARATOR);

	if (Z_Fs::create_folder($path))
	{
		if (file_exists($filename) && !$rewrite)
		{
		  return false;
		}
		else
		{
		  file_put_contents($filename,$content);
		  @chmod($filename,$rights);
		}
	}
	else
	{
		return false;
	}
	
	    return true;
	} catch (Exception $exc) {
            return false;
        }     
  }
  
  private function recursive_remove_directory($directory, $empty = FALSE) {
        if (substr($directory, - 1) == '/') {
            $directory = substr($directory, 0, - 1);
        }
        if (!file_exists($directory) || !is_dir($directory)) {
            return FALSE;
        } elseif (is_readable($directory)) {
            $handle = opendir($directory);
            while (FALSE !== ($item = readdir($handle))) {
                if ($item != '.' && $item != '..') {
                    $path = $directory . '/' . $item;
                    if (is_dir($path)) {
                        $this->recursive_remove_directory($path);
                    } else {
                        unlink($path);
                    }
                }
            }
            closedir($handle);
            if ($empty == FALSE) {
                if (!rmdir($directory)) {
                    return FALSE;
                }
            }
        }
        return TRUE;
    }


  private function create_folder($filename,$rights=0777)
  {
    global $m;

    $fileparts = explode(DIRECTORY_SEPARATOR,rtrim($filename,DIRECTORY_SEPARATOR));
    $curdir = $fileparts[0].DIRECTORY_SEPARATOR;
	unset($fileparts[0]);
    foreach ($fileparts as $part)
    {
      if (file_exists($curdir.$part))
      {
      }
      else
      {
		if (is_writable($curdir))
		{
		  mkdir($curdir.$part);
		  chmod($curdir.$part,$rights);
		}
		else
		{
		  return false;
		}
      }
      $curdir .= $part.DIRECTORY_SEPARATOR;
    }
    return true;
  }
        
	
}

?>