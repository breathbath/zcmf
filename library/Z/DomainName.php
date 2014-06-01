<?php

class Z_DomainName {
/*
	Функция возвращает имя домена указанного уровня
*/
	public static function getName($level=1)
	{
		if($level<=0)
			$level=1;
		$view = Zend_Layout::getMvcInstance()->getView();
        $url_array = $view->serverUrl();
        $url_array= explode('.', $url_array);  
        $url_array=array_reverse($url_array);
        $domain=null;
        if(isset($url_array[$level-1]))
          	$domain = str_replace('http://', '', $url_array[$level-1]);
		return $domain; 
	}
        
}    
?>