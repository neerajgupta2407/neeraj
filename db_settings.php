<?php
include_once('../config.php');
//ini_set('display_errors',1);
//echo error_log("You messed up! \r\n", 3, SITE."logs/db_error.txt");

$db_config = array();

$db_config['main_db'] =array("host" => "localhost",
							 "user" => "root",
							 "pass" => "",
							 "db_name" => "dl"	
							 );


?>
