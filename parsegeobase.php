<?php

exit;

include 'defines.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
	APPLICATION_ENV,
	APPLICATION_PATH . '/configs/application.ini'
);

$application->bootstrap();
set_time_limit(0);
//-------------------Парсинг файла с блоками адресов и заполнение БД-------------------//


$geoPath = APPLICATION_PATH.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'geoip';
$downloadFileName = $geoPath.DIRECTORY_SEPARATOR.'base.tar.gz';
$fileName = $geoPath.DIRECTORY_SEPARATOR.'cidr_ru_block.txt';

//if (file_exists($downloadFileName))
//  unlink($downloadFileName);
//if (file_exists($fileName))
//  unlink($fileName);
//
//
//$downloadFileContent = file_get_contents('http://ipgeobase.ru/files/db/Main/db_files.tar.gz');
//file_put_contents($downloadFileName, $downloadFileContent);
//
//$extractor = new Z_Archive_Extractor();
//$extractor->extractArchive($downloadFileName,$geoPath);


define('IP_BLOCK_START',0);
define('IP_BLOCK_STOP',1);
define('IP_BLOCK_CITY',4);
define('IP_BLOCK_AREA',5);
define('IP_BLOCK_DISTRICT',6);

$blocksModel	=	new Z_Model_Geo_Blocks();
$cityesModel	=	new Z_Model_Geo_Cityes();
$areasModel		=	new Z_Model_Geo_Areas();
$districtsModel	=	new Z_Model_Geo_Districts();

$districts = $districtsModel->getAdapter()->fetchPairs($districtsModel->select(true)
	->reset(Zend_Db_Select::COLUMNS)
	->columns(array('district','id')));

$areas = $areasModel->getAdapter()->fetchPairs($areasModel->select(true)
	->reset(Zend_Db_Select::COLUMNS)
	->columns(array('area','id')));

$cityes = $cityesModel->getAdapter()->fetchPairs($cityesModel->select(true)
	->reset(Zend_Db_Select::COLUMNS)
	->columns(array('city','id')));

$blocksModel->getAdapter()->query('TRUNCATE TABLE  `'.$blocksModel->info('name').'`');


if (($handle = fopen($fileName,'r')) !== false)
{
  $lastBlockStart	= 0;
  $lastBlockStop	= 0;
  $lastBlockCity	= '';

  $i=0;
  while (($data = Z_Csv::fgetcsv($handle, 10000, "\t")) !== FALSE)
  {
    $data[IP_BLOCK_AREA]      = iconv('WINDOWS-1251','UTF-8',$data[IP_BLOCK_AREA]);
    $data[IP_BLOCK_CITY]      = iconv('WINDOWS-1251','UTF-8',$data[IP_BLOCK_CITY]);
    $data[IP_BLOCK_DISTRICT]  = iconv('WINDOWS-1251','UTF-8',$data[IP_BLOCK_DISTRICT]);
    $data[IP_BLOCK_START]     =	(int)$data[IP_BLOCK_START];
    $data[IP_BLOCK_STOP]     =	(int)$data[IP_BLOCK_STOP];

    //добавление округа
    if (!array_key_exists($data[IP_BLOCK_DISTRICT],$districts))
    {
      $districtRow = $districtsModel->createRow(array(
	      'district'	=>	$data[IP_BLOCK_DISTRICT]
      ));
      $districtRow->save();
      $districts[$districtRow->district] = $districtRow->id;
      echo "Added district ".$districtRow->district."\n";
    }
    //добавление области
    if (!array_key_exists($data[IP_BLOCK_AREA],$areas))
    {
      $areaRow = $areasModel->createRow(array(
	      'z_geo_districts_id'	=>	$districts[$data[IP_BLOCK_DISTRICT]],
	      'area'					=>	$data[IP_BLOCK_AREA]
      ));
      $areaRow->save();
      $areas[$areaRow->area] = $areaRow->id;
      echo "Added area ".$areaRow->area."\n";
    }
    //добавление города
    if (!array_key_exists($data[IP_BLOCK_CITY],$cityes))
    {
      $cityRow = $cityesModel->createRow(array(
	      'z_geo_areas_id'	=>	$areas[$data[IP_BLOCK_AREA]],
	      'city'					=>	$data[IP_BLOCK_CITY]
      ));
      $cityRow->save();
      $cityes[$cityRow->city] = $cityRow->id;
      echo "Added city ".$cityRow->city."\n";
    }
    //добавление блока
    if ($lastBlockStart <= $data[IP_BLOCK_START] && $lastBlockStop >= $data[IP_BLOCK_STOP])
    {
      //если блок внутри существующего, но с другим городом, то добавляем, иначе ничего не делаем
      if ($lastBlockCity != $data[IP_BLOCK_CITY])
      {
	$blocksModel->insert(array(
		'z_geo_cityes_id'	=>	$cityes[$data[IP_BLOCK_CITY]],
		'start'					=>	$data[IP_BLOCK_START],
		'stop'					=>	$data[IP_BLOCK_STOP]
	));
	$i++;
	if ($i%1000 == 0)
	{

	  echo $i."\n";
	  sleep(2);
	}
//	echo  "Added block ".$blockRow->start." ".$blockRow->stop."\n";
      }
    }
    else
    {
      //если этот блок не является подблоком, то добавляем
      $blocksModel->insert(array(
	      'z_geo_cityes_id'	=>	$cityes[$data[IP_BLOCK_CITY]],
	      'start'					=>	$data[IP_BLOCK_START],
	      'stop'					=>	$data[IP_BLOCK_STOP]
      ));
      $i++;
      if ($i%1000 == 0)
      {

	echo $i."\n";
	sleep(2);
      }
//      echo  "Added block ".$blockRow->start." ".$blockRow->stop."\n";

      $lastBlockStart = $data[IP_BLOCK_START];
      $lastBlockStop  = $data[IP_BLOCK_STOP];
      $lastBlockCity  = $data[IP_BLOCK_CITY];
    }
    


  }
}
echo "\n";
