<?php
//GLOBAL VARiABLES
error_reporting(1);
global $sql,$session,$config;
define( '_PAYSLIP','INDEX');
define("JPATH_ROOT",dirname(__FILE__));
define("DS",DIRECTORY_SEPARATOR);
define( 'JPATH_CONFIGURATION', 	JPATH_ROOT );
define( 'JPATH_LIBRARIES',	 	JPATH_ROOT.DS.'library' );
define( 'JPATH_PLUGINS',		JPATH_ROOT.DS.'plugins'   );
define( 'JPATH_PUBLIC'	   ,	JPATH_ROOT.DS.'public' );
define( 'JPATH_TMP'	   ,	JPATH_ROOT.DS.'public' );

define( 'JPATH_PLUGINS_FANCYBOX',		JPATH_BOOT.DS.'plugins'.DS.'fancybox'   );
define( 'JPATH_PUBLIC_PV_JS'	   ,	JPATH_BOOT.DS.'public'.DS.'pvs'.DS.'js' );
//ON THE REAL SERVER THE pvproject part must be removed.
define('START_YEAR','2012');


//Post Keeper
if($_REQUEST){
	foreach($_REQUEST as $key => $value){
		$$key = @trim($value);
	}
}

if($_FILES){
	
		foreach($_FILES as $keyimg => $values){
			//$$keyimg = $values;
		
			foreach($values as $key => $value){
				$$key = $value;
				}
			
			if($name !=""){
					if(is_uploaded_file($tmp_name)) {
						$$keyimg = file_get_contents($tmp_name);
						}
				}
		}
	
	}
	

class JConfig {
	public $dbtype = 'mysqli';
	public $server = 'localhost';
	public $user = 'root';
	public $database = 'srms';
	public $password = '';
	public $secret='TTU';
	public $debug = false;
	public $autoRollback= true;
	public $ADODB_COUNTRECS = false;
	private static $_instance;
	 
	public function __construct(){
	}
	
	private function __clone(){}
	
	public static function getInstance(){
	if(!self::$_instance instanceof self){
	     self::$_instance = new self();
	 }
	    return self::$_instance;
	}

}


$config = JConfig::getInstance();

//included classes
include dirname(__FILE__)."/library/Session.class.php";
include dirname(__FILE__)."/plugins/adodb/adodb.inc.php";
include dirname(__FILE__)."/library/sql.php";
include dirname(__FILE__)."/library/applicant.class.php";
include dirname(__FILE__)."/library/bank.class.php";
include dirname(__FILE__)."/library/calender.php";
include dirname(__FILE__)."/library/hall.class.php";


?>