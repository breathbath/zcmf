<?php

class Z_Controller_Action_Helper_Language extends Zend_Controller_Action_Helper_Abstract
{
	protected $_languages;
	//protected $_data;

	
	public function __construct($languages)
	{
		//$this->_data = $data;
		$this->_languages = $languages;
	}
	
	public function init()
	{
		$lang_sess = new Zend_Session_Namespace('Language');
        if (!isset($lang_sess->language)){
            $config = Zend_Registry::get('config');
			$languages = $config->languages->toArray();
			$zl = new Zend_Locale();
			$lang_sess->language = $language= in_array($zl->getLanguage(),$languages)?$zl->getLanguage():'ru';
        }    
        else{
        	$language=$lang_sess->language;
        }
		if(!in_array($language,array_keys($this->_languages))) {
			$language='ru';
		}
		$localeString = $this->_languages[$language];
		$translate =new Z_Translate ($language);
		$this->_actionController->_localeString = $localeString;
		$this->_actionController->t = $translate;
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
		$viewRenderer->view->localeString=$localeString;
		$viewRenderer->view->t = $translate;
	}
	
}


