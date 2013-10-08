<?php
include_once('../config.php');
//ini_set('display_errors',1);
//echo error_log("You messed up! \r\n", 3, SITE."logs/db_error.txt");

$db_config = array();
/*Test */
/*
$db_config['main_db'] =array("host" => "localhost",
							 "user" => "dineout_test",
							 "pass" => "Dine@test449",
							 "db_name" => "dineout_test"	
							 );
*/
/*Live*/
$db_config['main_db'] =array("host" => "localhost",
							 "user" => "root",
							 "pass" => "",
							 "db_name" => "dl"	
							 );

/*
$db_config['main_db'] =array("host" => "localhost",
							 "user" => "root",
							 "pass" => "",
							 "db_name" => "dineout_live"	
							 );
*/
//echo $db_config['main_db']['host'];

?>