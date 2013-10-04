<?php
///http://localhost/dineout-newdesign2/model_g/model_generator.php?table_name=admin&pk=id

/***
*parameters: table_name
*pk=primary key of table 'table_name'
**/

ini_set('display_errors', '1');


include_once('../config.php');
include_once(SITE.'utils/utils.php');
include_once(SITE.'databse/db.php');


$obj_db=new db_handler();

$pk=$_REQUEST['pk'];

$table_name=$_REQUEST['table_name'];

echo $sql="select * from $table_name where 1=1 limit 1";

$data=$obj_db->mysql_read_prep($sql);

$arr=$data[0];


$fields=array();
foreach ($arr as $key => $value) {
	# code...
	if(!is_numeric($key)){
		$fields[]=$key;	
	}
	
}





/*
$fields=array(
	'id',
	'first_name',
	'last_name',
	'added_date',
	'email',
	'password',
	'phone',
	'status',
	'ip',
	'city',
	'user_type'

	);
*/
//$pk="id";


$class='';

$class.="<?php\n\r".
	"//ini_set('display_errors', '1');\n\r".
		"include_once('../config.php');\n\r".
		"include_once(SITE.'databse/db.php');\n\r".
		"include_once(SITE.'utils/utils.php');\n\r";

$class.="\n\rclass ".ucfirst($table_name)."{";
$class.="\n\r\tprivate \$table_name; //table name";
$class.="\n\r\tprivate \$pk; ////primary key of the table";
$class.="\n\r\tprivate \$obj_db; //db object";


$class.="\n\r\t//parameter lists";


foreach ($fields as $value) {
	# code...

	$class.="\n\r\tprivate \$$value;";
}

$class.="\n";

/**starting constructor function**/
$class.="\n\t".'function __construct($param)'."\n\t{";
$class.="\n\r\t\t\$this->obj_db = new db_handler();";			//db initialization
$class.="\n\r\t\t\$this->table_name = '$table_name';";
$class.="\n\r\t\t\$this->pk = '$pk';";


$class.="\n\r\t\t\$param = filter_array_for_null(\$param); //filtering the array and removing null entries";
$class.="\n\r\t\tif(array_key_exists(\$this->pk, \$param) && \$param[\$this->pk]!='')\n\t\t{";
$class.="\n\r\t\t\t\$sql=\"select * from \$this->table_name where \$this->pk=?\";";
$class.="\n\r\t\t\t\$arr = array(\$param[\$this->pk]);";

$class.="\n\r\t\t\t\$data = \$this->obj_db->mysql_read_prep(\$sql,\$arr); //getting the data of the corresponding primary key";


$class.="\n\r\t\t\tif(count(\$data)>0)\n\t\t\t{";
$class.="\n\r\t\t\t\t //set only if some data was found";
$class.="\n\r\t\t\t\t\$this->set_parameters(\$data[0]);//initializing the parameters with the db values";
$class.="\n\r\t\t\t}";
$class.="\n\r\t	} ";
$class.="\n\r\t\t//as this is new data,so setting the variables with the new values";

$class.="\n\r\t\t\$this->set_parameters(\$param); //calling the set parameter function";
/*
foreach ($fields as $value) {
	# code...

	$class.="\n\r\t\t\$this->$value=\$param['".$value."'];";
}

*/			//constructor closing
			$class.="\n\r 	} ";




/**ending constructor function**/





/**making insert function
**/


$class.="\n\n\r\t"."function insert()\n\r\t{";
$class.="\n\r\t\t\$param = array();";
foreach ($fields as $value) {
	# code...
	if($value==$pk){continue;}
	$class.="\n\r\t\t\$param['".$value."'] = \$this->$value;";
}
$class.="\n\r\t\t\$id = \$this->obj_db->mysql_insert_prep(\$this->table_name,\$param);\t//inserting into db";
$class.="\n\r\t\treturn \$id;";
			//insert function  closing
			$class.="\n\r 	} ";


/**ending insert function**/





/**making update function
**/


$class.="\n\n\r\t"."function update()\n\r\t{";
$class.="\n\r\t\t\$param = array();";
foreach ($fields as $value) {
	# code...
	if($value==$pk){continue;}
	$class.="\n\r\t\t\$param['".$value."'] = \$this->$value;";
}

$class.="\n\r\t\t\$val = \$this->pk;";
$class.="\n\r\t\t\$where = \" where \$this->pk=?\";";

$class.="\n\r\t\t\$where_param = array(\$this->\$val);";



$class.="\n\r\t\t\$ret = \$this->obj_db->update_associative_prep(\$this->table_name,\$param,\$where,\$where_param);\t//updating into db files";
$class.="\n\r\t\treturn \$ret;";
			//insert function  closing
			$class.="\n\r 	} ";


/**ending update function**/












/**making set_parameter function
**/


$class.="\n\n\r\t"."function set_parameters(\$param)\n\r\t{";

foreach ($fields as $value) {
	# code...

	$class.="\n\r\t\tif(\$param['".$value."'] != ''){ \$this->$value = \$param['".$value."']; }";
}

			//set_parameter function  closing
			$class.="\n\r 	} ";


/**ending insert function**/



/**making ftech_data function
**/


$class.="\n\n\r\t"."function fetch_data()\n\r\t{";

foreach ($fields as $value) {
	# code...

	$class.="\n\r\t\t\$param['".$value."'] = \$this->$value;";
}

$class.="\n\r\t\treturn \$param;";
			//set_parameter function  closing
			$class.="\n\r 	} ";


/**ending ftech_data function**/




//ending of class
$class.="\n\r }  //class ends \n\r?>";


$path=$table_name.'.php';
$file=fopen($path,'w+');
fwrite($file, $class);
fclose($file);


//echo $class;
?>