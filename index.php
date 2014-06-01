<?php
include 'defines.php';
date_default_timezone_set('Europe/Moscow');
 

// Create application, bootstrap, and run

$application = new Zend_Application(
	APPLICATION_ENV,
	APPLICATION_PATH . '/configs/application.ini'
);

include_once 'jQuery.php';

$application->bootstrap();
$frontController = Zend_Controller_Front::getInstance();
$config = Zend_Registry::get('config');
$languages = $config->languages->toArray();
/*$zl = new Zend_Locale();
$lang = in_array($zl->getLanguage(),$languages)?$zl->getLanguage():'ru';

$route = new Zend_Controller_Router_Route (':lang/:controller/:action/*',array('controller'=>'index','action'=>'index', 'module'=>'default','lang'=>$lang));
$router = $frontController->getRouter();
$router->addRoute ('default',$route);
$frontController->setRouter($router);
$router->addRoute ("secreturl", 
	new Zend_Controller_Router_Route('admin/:controller/:action/*', array ("module"=>"admin",'controller'=>'index','action'=>'index')));  */
$languageHelper = new Z_Controller_Action_Helper_Language ($languages);
Zend_Controller_Action_HelperBroker::addHelper($languageHelper);
$application->run();
  
//$pf = $application->getBootstrap()->getResource('db')->getProfiler();
//$pfs = $pf->getQueryProfiles();
//if ($pfs)
//foreach ($pfs as $pfel)
//{
//	echo $pfel->getElapsedSecs()."\t".$pfel->getQuery().'<br />------------------<br />';
//}
//echo $pf->getTotalElapsedSecs();
