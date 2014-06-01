<?php
/**
 * Pimcore
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.pimcore.org/license
 *
 * @copyright  Copyright (c) 2009-2010 elements.at New Media Solutions GmbH (http://www.elements.at)
 * @license    http://www.pimcore.org/license     New BSD License
  
 
 *class Z_Backup
 library/Z/Backup.php
 
 * application.ini - backup section
 AP/configs/application.ini
{replace}
{pattern}(site\.backup\.directory.*){/pattern}
{to} {/to}
{/replace}

AP/configs/application.ini
{replace}
{pattern}(\[development.*production\]){/pattern}
{to}site.backup.directory = APPLICATION_PATH "/data/backups"\n\n[development : production]{/to}
{/replace}
 
 * Z_Fs - method rscandir
 library/Z/Fs.php
 
 * sys/js/backup.js
 SP/sys/js/backup.js
 
 * layout - ->appendFile("/sys/js/backup.js")
 AP/modules/admin/views/scripts/layout.phtml
 
 * Admin_Z_BackupController.php 
 AP/modules/admin/controllers/Z/BackupController.php
 
 * Admin_Z_BackupsController.php
 AP/modules/admin/controllers/Z/BackupsController.php
 
 * views/z/backups/list.phtml  
 AP/modules/admin/views/scripts/z/backups/list.phtml
 
 * Z_Archive_Tar.php 
 library/Z/Archive/Tar.php
 
 * library/PEAR  
 library/PEAR
 
 * library/PEAR.php 
 library/PEAR.php
 
 * library/PEAR5.php 
 library/PEAR5.php 
 
 
 *Ресурс:
 INSERT INTO `z_resources` (`resourceId`, `actionId`, `parentid`, `orderid`, `title`, `model`, `datatype`, `indexate`, `default_field`, `parent_field`, `order`, `group`, `paginate`, `can_delete`, `can_edit`, `can_add`, `delete_confirm`, `delete_on_have_child`, `sortable`, `sortable_position`, `visible`, `on_have_subcat`) SELECT
'z_backups', 'list',0 , 143, 'Резервные копии', '', 'band', '', 'title', '', 'id', '', 15, 1, 1, 1, 1, 0, 0, 'bottom', 1, 1 from `z_resources` WHERE NOT EXISTS (SELECT * FROM `z_resources` WHERE `resourceId`='z_backups') limit 1
 
 
 */

class Z_Backup {

    public $additionalExcludePatterns = array();

    public $filesToBackup;
    public $fileAmount;
    public $backupFile;
    public $backupDir;
    const PR_ROOT=''; 
    public function __construct ($backupFile) {
    	$this->backupFile=$backupFile.'.tmp';
        $config = Zend_Registry::get('config')->site->backup;
		$backup_dir=$config->get('directory',APPLICATION_PATH.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'backups');
        if (!is_dir($backup_dir)) {
            if (!mkdir($backup_dir)) {
                throw new Exception("Folder " . $backup_dir . " couldn't be created.");
                exit;
            }
        }
        $this->backupDir = $backup_dir;
        
        $sp = SITE_PATH;
        $root='';
        if(!file_exists($sp.DIRECTORY_SEPARATOR.'application'))
        {
        	$root=str_replace(basename($sp), '', $sp);
        }
        else
        {
        	$root=$sp.DIRECTORY_SEPARATOR;
        }
        
        $this->PR_ROOT=$root;
    }
    
    public function getFilesToBackup () {
        return $this->filesToBackup;
    }
    
    protected function setFilesToBackup ($files) {
        $this->filesToBackup = $files;
    }
    
    public function getFileAmount () {
        return $this->fileAmount;
    }
    
    protected function setFileAmount ($fileAmount) {
        $this->fileAmount = $fileAmount;
    }
    
    public function getBackupFile () {
        return $this->backupFile;
    }
    
    public function getBackupDir(){
    	return $this->backupDir;
    }

    public function getAdditionalExcludeFiles () {
        return $this->additionalExcludePatterns;
    }

    public function setAdditionalExcludePatterns ($additionalExcludePatterns) {
        $this->additionalExcludePatterns = $additionalExcludePatterns;
    }

    protected function getFormattedFilesize () {
        return $this->formatBytes(filesize($this->getBackupFile()));
    }
    
    
    protected function getArchive () {
    	$tmpFname = $this->getBackupFile();
        $obj = new Z_Archive_Tar($tmpFname);

        if (!is_file($tmpFname)) {

            $files = array();

            if (!$obj->create($files)) {
                echo "can't create archive";
            }
        }

        return $obj;
    }
    protected function formatBytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');

    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);

    $bytes /= pow(1024, $pow);

    return round($bytes, $precision) . ' ' . $units[$pow];
	}
	
    public function init () {

        // create backup directory if not exists
        $errors = array();
        $this->setFileAmount(0);
        $backup_dir = $this->getBackupDir();

        // cleanup old backups
        if (is_file($backup_dir . "/backup-dump.sql")) {
            unlink($backup_dir . "/backup-dump.sql");
        }

        // get steps
        $steps = array();

        // get available tables
        $model= new Z_Model_Config();
        $db =$model->getAdapter();
        $tables = $db->fetchAll("SHOW FULL TABLES");


        $steps[] = array("mysql-tables", null);
        
         // tables
        foreach ($tables as $table) {

            $name = current($table);
            $type = next($table);

            if ($type != "VIEW") {
                $steps[] = array("mysql", array(
                    "name" => $name,
                    "type" => $type
                ));
            }
        }

        // views
        foreach ($tables as $table) {

            reset($table);
            $name = current($table);
            $type = next($table);

            if ($type == "VIEW") {
                $steps[] = array("mysql", array(
                    "name" => $name,
                    "type" => $type
                ));
            }
        }
        
        



        $steps[] = array("mysql-complete", null);
                
        // check files
        $currentFileCount = 0;
        $currentFileSize = 0;
        $currentStepFiles = array();


        // check permissions
       
        $filesIn = Z_Fs::rscandir($this->PR_ROOT);
        /*
$filesIn2=$filesIn3=array();
        
        	$filesIn2=Z_Fs::rscandir(APPLICATION_PATH . DIRECTORY_SEPARATOR);
        if(!file_exists($sp.'library'))
        	$filesIn3=Z_Fs::rscandir(APPLICATION_PATH."/../library".DIRECTORY_SEPARATOR);	
        $filesIn=array_merge($filesIn1,$filesIn2,$filesIn3);
        
        
        $sp.='sys/';
        $filesIn = Z_Fs::rscandir($sp);	
*/
        clearstatcache();
        $filesToBackup=null;

        foreach ($filesIn as $fileIn) {
            if (!is_readable($fileIn)) {
                $errors[] = $fileIn . " is not readable.";
            }

            if ($currentFileCount > 300 || $currentFileSize > 20000000) {

                $currentFileCount = 0;
                $currentFileSize = 0;
                if (!empty($currentStepFiles)) {
                    $filesToBackup[] = $currentStepFiles;
                }
                $currentStepFiles = array();
            }

            if(file_exists($fileIn)) {
                $currentFileSize += filesize($fileIn);
                $currentFileCount++;
                $currentStepFiles[] = $fileIn;
            }
        }

        if (!empty($currentStepFiles)) {
            $filesToBackup[] = $currentStepFiles;
        }
        if(count($filesToBackup))
        {
        	$this->setFilesToBackup($filesToBackup);
        	$fileSteps = count($filesToBackup);

	        for ($i = 0; $i < $fileSteps; $i++) {	
    	        $steps[] = array("files", array(
        	        "step" => $i
            	));
        	}

	        $steps[] = array("complete", null);
	    }    

        if (!empty($errors)) {
            $steps = null;
        }
        return array(
            "steps" => $steps,
            "errors" => $errors
        );
    }
    
    public function fileStep ($step) {
        
        $filesContainer = $this->getFilesToBackup();
        $files = $filesContainer[$step];
		$ds=DIRECTORY_SEPARATOR;
        $excludePatterns = array(
            	"#application\\".$ds."data\\".$ds."backups\\".$ds.".*#"
        	);	
       
        if(!empty($this->additionalExcludePatterns) && is_array($this->additionalExcludePatterns)) {
            $excludePatterns = array_merge($excludePatterns, $this->additionalExcludePatterns);
        }

        clearstatcache();
        $logs = '';
        foreach ($files as $file) {
            if ($file) {
                if (file_exists($file) && is_readable($file)) {

                    $exclude = false;
                   
                    $relPath = str_replace($this->PR_ROOT, "", $file);
                    
                    foreach ($excludePatterns as $pattern) {
                        if (preg_match($pattern, $relPath)) {
                            $exclude = true;
                        }
                    }

                    if (!$exclude && is_file($file)) {
                        $this->getArchive()->addString($relPath, file_get_contents($file));
                    }
                }
                else {
                    $logs.="Backup: Can't read file: " . $file."/n";
                }
            }
        }
        if($logs)
          $this->getArchive()->addString('log.txt', $logs);

        $this->setFileAmount($this->getFileAmount()+count($files));

        return array(
            "success" => true,
            "filesize" => $this->getFormattedFilesize(),
            "fileAmount" => $this->getFileAmount()
        );
    }
    
    public function mysqlTables () {
        $model= new Z_Model_Config();
        $db =$model->getAdapter();        
        $tables = $db->fetchAll("SHOW FULL TABLES");

        $dumpData = "\nSET NAMES UTF8;\n\n";

        // tables
        foreach ($tables as $table) {

            $name = current($table);
            $type = next($table);

            if ($type != "VIEW") {
                $dumpData .= "\n\n";
                $dumpData .= "DROP TABLE IF EXISTS `" . $name . "`;";
                $dumpData .= "\n";

                $tableData = $db->fetchRow("SHOW CREATE TABLE `" . $name."`");

                $dumpData .= $tableData["Create Table"] . ";";

                $dumpData .= "\n\n";
            }

        }

        $dumpData .= "\n\n";
        $backup_dir = $this->getBackupDir();


        $h = fopen($backup_dir . "/backup-dump.sql", "a+");
        fwrite($h, $dumpData);
        fclose($h);

        return array(
            "success" => true
        );
    }
    
    public function mysqlData ($name, $type) {
        $model= new Z_Model_Config();
        $db =$model->getAdapter();      

        $dumpData = "\n\n";

        if ($type != "VIEW") {
            // backup tables
            $tableData = $db->fetchAll("SELECT * FROM `" . $name."`");

            foreach ($tableData as $row) {

                $cells = array();
                foreach ($row as $cell) {
                    $cells[] = $db->quote($cell);
                }

                $dumpData .= "INSERT INTO `" . $name . "` VALUES (" . implode(",", $cells) . ");";
                $dumpData .= "\n";

            }
        }
        else {
            // dump view structure
            $dumpData .= "\n\n";
            $dumpData .= "DROP VIEW IF EXISTS `" . $name . "`;";
            $dumpData .= "\n";

            try {
                $viewData = $db->fetchRow("SHOW CREATE VIEW `" . $name."`");
                $dumpData .= $viewData["Create View"] . ";";
            } catch (Exception $e) {
            }
        }

        $dumpData .= "\n\n";
         $backup_dir = $this->getBackupDir();

        $h = fopen($backup_dir . "/backup-dump.sql", "a+");
        fwrite($h, $dumpData);
        fclose($h);

        return array(
            "success" => true
        );
    }
    
    public function mysqlComplete() {
     	$backup_dir = $this->getBackupDir();
        $this->getArchive()->addString("application".DIRECTORY_SEPARATOR."data".DIRECTORY_SEPARATOR."dump".DIRECTORY_SEPARATOR."dump.sql", file_get_contents($backup_dir . "/backup-dump.sql"));

        // cleanup
        unlink($backup_dir . "/backup-dump.sql");

        return array(
            "success" => true,
            "filesize" => $this->getFormattedFilesize()
        );
    }
    
    public function complete () {
    	$backupFile=$this->getBackupFile();
		$fname = basename($backupFile,'.tmp');
    	$path  = str_replace($fname.'.tmp', '',$backupFile);
    	rename($backupFile, $path.$fname);
		$this->backupFile=$path.$fname;
		$pattern = "#backup\_(\d+\-\d+\-\d{2,4}\_\d+\-\d+)#";
		$matches=array();
		$filedate='';
		preg_match($pattern, $fname, $matches);
		if(isset($matches[1]))
		{
			$date = new Zend_Date($matches[1], 'MM-dd-yyyy_h-m');
			$filedate=$date->get(Zend_Date::DATETIME_MEDIUM);
		}
		
	return array(
            "success" => true,
            "filesize" => $this->getFormattedFilesize(),
            "fileid" => base64_encode($fname),
            "filedate"=>$filedate
        );
    }
}
