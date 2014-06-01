<?php

class Z_Config {

	/**
	 * 
	 * @var Z_Db_Table
	 */
	protected $_model = NULL;
        protected $_data = NULL;
	
	/**
	 * 
	 * @var Zend_Db_Table_Row
	 */
	protected $_row = NULL;
	
	public function __construct($sid)
	{
		$this->_model = new Z_Model_Config();
		$this->_row = $this->_getRow($sid);
	}
	

	/**
	 * @return string
	 */
	public function getValue()
	{
		return $this->_row;
	}
        
        public static function val($sid)
        {
          $cache = Z_Cache::getInstance();
          $model = new Z_Model_Config();
            if (!$data=$cache->load('kap_config'))
            {
                $data = $model->fetchPairs(array('sid','value'));
                $cache->save($data,'kap_config');
            }
            if(isset($data[$sid]))
                return $data[$sid];
            else
                return '';

        }
	
	/**
	 * @return Zend_Db_Table_Row
	 */
	protected function _getRow($sid)
	{
            $cache = Z_Cache::getInstance();
            if (!$this->_data=$cache->load('kap_config'))
            {
                $this->_data = $this->_model->fetchPairs(array('sid','value'));
                $cache->save($this->_data,'kap_config');
            }
            if(isset($this->_data[$sid]))
                return $this->_data[$sid];
            else
                return false;
	} 
        
    public static function extractExtensions($input,$regime=null)
    {
        
        if (is_string($input)) {
            $extension = explode(',', $input);
        }

        foreach ($extension as $content) {
            if (empty($content) || !is_string($content)) {
                continue;
            }

            $extensions[] = trim($content);
        }
        $extensions = array_unique($extensions);

        // Sanity check to ensure no empty values
        foreach ($extensions as $key => $ext) {
            if (empty($ext)) {
                unset($extensions[$key]);
            }
            if($regime=='js_valid')
            {
                $extensions[$key]='*.'.$ext;
            }    
        }
        if($regime=='js_valid')
          return implode(';', $extensions);
        else
          return $extensions;  
    }
	
}

?>