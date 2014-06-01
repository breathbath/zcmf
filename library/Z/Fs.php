<?php

class Z_Fs
{
  private $rootdir;

  protected function __construct()
  {
  }
  
  public static function rscandir($base = '', &$data = array()) {
	
	if(substr($base, -1, 1)!=DIRECTORY_SEPARATOR)
		$base=$base.DIRECTORY_SEPARATOR;
	$array = array_diff(scandir($base), array('.', '..', '.DS_Store'));

    foreach ($array as $value) {
        if (is_dir($base . $value)) {
            $data[] = $base . $value . DIRECTORY_SEPARATOR;
            $data = Z_Fs::rscandir($base . $value . DIRECTORY_SEPARATOR, $data);
		}
        elseif (is_file($base . $value)) { 
            $data[] = $base . $value;
		}
    }
    return $data;
 }
 
 public static function getFormattedFileSize($filename){
 	$result='';
 	if(file_exists($filename)&&is_file($filename)&&is_readable($filename)){
 		$bytes=filesize($filename);
 		$units = array('B', 'KB', 'MB', 'GB', 'TB');
 	    $bytes = max($bytes, 0);
    	$pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    	$pow = min($pow, count($units) - 1);
	    $bytes /= pow(1024, $pow);
	    return round($bytes, 2) . ' ' . $units[$pow];
 	}
 	return $result;
 }


  public static function create_file($filename,$content="",$rewrite=false,$rights=0777)
  {
    global $m;
	
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
		  chmod($filename,$rights);
		}
	}
	else
	{
		return false;
	}
	
	/*
    $i=0;
    $fileparts_count = count($fileparts);
    foreach ($fileparts as $part)
    {
      $i++;
      if ($i<$fileparts_count)
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
      }
      $curdir .= $part.DIRECTORY_SEPARATOR;
    }
    $curdir = rtrim($curdir,DIRECTORY_SEPARATOR);
    if (file_exists($curdir) && !$rewrite)
    {
      return false;
    }
    else
    {
      file_put_contents($curdir,$content);
      chmod($curdir,$rights);
    }
	*/
    return true;
  }

  public static function create_folder($filename,$rights=0777)
  {
    global $m;
    $sp = SITE_PATH;  //  /home/brb/domains/gadget-hater.ru/public_html
	// $filename = /home/brb/domains/gadget-hater.ru/public_html/application/../library/Z/Fs.php
	$filename = str_replace($sp, '', $filename);
    $fileparts = explode(DIRECTORY_SEPARATOR,rtrim($filename,DIRECTORY_SEPARATOR));
    $curdir = $fileparts[0].DIRECTORY_SEPARATOR;
	unset($fileparts[0]);
    foreach ($fileparts as $part)
    {
      if (file_exists($sp.DIRECTORY_SEPARATOR.$curdir.$part))
      {
      }
      else
      {
		if (is_writable($sp.DIRECTORY_SEPARATOR.$curdir))
		{
		  mkdir($sp.DIRECTORY_SEPARATOR.$curdir.$part);
		  chmod($sp.DIRECTORY_SEPARATOR.$curdir.$part,$rights);
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
  public static function recursive_remove_directory($directory, $empty = FALSE) {
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

}