<?php

class Z_Translate
{
	protected $_data = null;
	protected $_type = null;
	protected $_language =null;
	
	public function __construct($language)
	{
		$this->_language=$language;
		
	}
	
	public function _($data)
	{
		if($this->_language=='ru') //это язык по умолчанию
		 return $data;
		$this->_data = $data;
		if (is_array($data)) {
                    $this->_type = 'Array';
                } else if ($data instanceof Zend_Db_Table_Rowset) {
                    $this->_type = 'Rowset';
                } else if ($data instanceof Zend_Db_Table_Row) {
                    $this->_type = 'Row';
                } else if($data instanceof Zend_Paginator){
                    $this->_type='Paginator';
                } else {
                    $type = (is_object($data)) ? get_class($data) : gettype($data);
                    throw new Exception ('Неизвестный тип данных для перевода: "'.$type.'"');
                } 
        if($this->_type=='Row')
        {
        	//echo('<pre>');
        	//die(var_dump($data));
        	$das = $this->translateArray($this->_data->toArray());
        	$data->setFromArray($das);
        }	
        else if ($this->_type=='Rowset'||$this->_type=='Paginator')
        {
        	foreach ($this->_data as $d)
        	{
        		$das = $this->translateArray($d->toArray());
        		$d->setFromArray($das);	
        	}
        }   
        else if($this->_type=='Array')
        {
        	foreach ($this->_data as $key=> $d)
        	{
        		$this->_data[$key]=$this->translateArray($d);
        	}
        }        
        return $data;

	}
	
	private function translateArray($input)
	{
		foreach ($input as $key=> $da)
        	{
        		if(isset($input[$this->_language.'_'.$key])&& trim($input[$this->_language.'_'.$key]))
        			$input[$key]=$input[$this->_language.'_'.$key];
        	}
        	return $input;
	}
}

?>