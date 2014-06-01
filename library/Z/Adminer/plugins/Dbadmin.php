<?php
class Dbadmin {
protected $disabled;
protected $_dbhost;
protected $_dbuser;
protected $_dbbase;
protected $_dbpass;
	public function __construct(){
		$config = Zend_Registry::get('config')->resources->db->params;
		$this->_dbhost = $config->get('host','localhost');
		$this->_dbuser = $config->get('username','root');
		$this->_dbbase = $config->get('dbname','');
		$this->_dbpass=$config->get('password','');
	}
	//http://cimb.me/sys/adminer/db.php?server=localhost&username=root&db=cimb
	function credentials() {
            // server, username and password for connecting to database
            return array($this->_dbhost, $this->_dbuser, $this->_dbpass);
        }
    function name() {
		return 'ZCMF_DB';
	}    
	
	function databases() {
		return array($this->_dbbase);
	}
}        
?>