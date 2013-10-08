<?php 
///ini_set('display_errors', '1');
												/*Database class for Read,Insert,Execute (Update/Delete) */
//include_once("../config.php");
include_once("db_settings.php");
//include_once(SITE.'logger/logger.php');



class db_handler
{
   var $connection;
   var $db_name;
   var $db_config;
   function __construct($db_name = "main_db")
   {
	   global $db_config;
	   $this->db_config = $db_config;
	   $this->db_name = $db_name;
   }
   
 //function begin_trans()
 //function commit_trans()
 //function rollback_trans()

   public function get_connection() // Function for opening a connection.
   {
	   //Check If Connection is Already On
		
		if(!is_resource($this->connection))
		{
			
			//Make Connection
	    	try
			{
					//print_r($this);
				$this->connection = new PDO("mysql:host=".$this->db_config[$this->db_name]['host'].";dbname=".$this->db_config[$this->db_name]['db_name'],$this->db_config[$this->db_name]['user'],$this->db_config[$this->db_name]['pass']);
				
			}
			catch (PDOException $e) 
			{
				
				return "Error!: " . $e->getMessage() . "<br/>";
			}
			
		}
		//var_dump($this->connection);
		return $this->connection;
   }
   
    function filter_array_for_null($arr_input){
   	//this function will take associative array ($arr_input) as input and return the array after removing the null elements


   		$arr_output=array();   //output array
   		//creating the output array
   		foreach($arr_input as $key=>$value){

   			if(strlen($value)>0){   //discardinf the null or empty values

   				$arr_output[$key]=$value;
   			}
   		}
   		
   		return $arr_output;	
	}


   public function mysql_read_prep($sql="",$params="") // For Reading from the database
   {
	   
	  $con = $this->get_connection();

	  $stmt = $con->prepare($sql);
	  $k=1;
	  for($i=0;$i<count($params);$i++)
	  {
		 $stmt->bindParam($k,$params[$i]);
		 $k++;
	  }

      if(!$stmt->execute())
	  {
           return $this->log_errors($sql,$params,$con);
		 
		  /*$str_data = serialize($params);
		  error_log("\r\n".$current_datetime." ".$sql,3,SITE."logs/db_error.txt");
		  error_log("\r\n".$str_data,3,SITE."logs/db_error.txt"); 	     */
	  }
	  	  
      $result = $stmt->fetchAll();
	  
	  return $result;

   }
   
  public function mysql_insert_prep($table,$data) // For Inserting into the database
   {

   	  $con = $this->get_connection();
	  
	  $data=$this->filter_array_for_null($data);  ////removing the null elements from array
	  
	  $params = array();
	  $str_keys ="";
	  $str_placeholder ="";
	  
	  foreach ($data as $key => $value) {
	  
		  $str_keys .= "`".$key."`".", ";
		  $str_placeholder .= '?, ';
		  $params[] = $value;
		 
	  }
	  
	  $str_keys = substr($str_keys, 0, -2);
	  $str_placeholder = substr($str_placeholder, 0 ,-2); 
	  $sql = "insert into $table ($str_keys) values ($str_placeholder)";
	  
	  $this->logger($table,$sql,$params,'insert') ;   ///logging the query into file
	  
	  
	  
	 // var_dump($params);
	  $stmt = $con->prepare($sql);
	  $k=1;
	  for($i=0;$i<count($params);$i++)
	  {
		 $stmt->bindParam($k,$params[$i]);
		 $k++;
	  }
	
      if(!$stmt->execute())
	  {
 		  return $this->log_errors($sql,$params,$con);
		  /*
		  $str_data = serialize($params);
		  error_log("\r\n".$current_datetime." ".$sql,3,SITE."logs/db_error.txt");
		  error_log("\r\n".$str_data,3,SITE."logs/db_error.txt"); 	     
		  error_log("\r\n".serialize($con->errorInfo()),3,SITE."logs/db_error.txt"); 	  
		  return 0;
		  */
	  }
	  else
	  {
	  	  return $con->lastInsertId();
	  }
	  
   }
   
   public function mysql_execute_prep($sql="",$params="") //Function for update and delete queries.
   {
	  
	  $this->logger('',$sql,$params,'') ;   ///logging the query into file
	  $con = $this->get_connection();
	  
	  $stmt = $con->prepare($sql);
	  $k=1;
	  for($i=0;$i<count($params);$i++)
	  {
	  $stmt->bindParam($k,$params[$i]);
	  $k++;
	  }
	  
	  if(!$stmt->execute())
	  {
		 /* $str_data = serialize($params);
		  error_log("\r\n".$current_datetime." ".$sql,3,SITE."logs/db_error.txt");
		  error_log("\r\n".$str_data,3,SITE."logs/db_error.txt");
		  return 0;*/
		  
		  return $this->log_errors($sql,$params,$con);
	  }
	  
	  return 1;
   }
   
   public function log_errors($sql,$params,$con)
   {
	  $str_data = json_encode($params);
	  
	  
	  		$string.="\n\r------Begin-----\n\r";
			$string.="TIME: ".date('d-m-Y H:i:s',time())."\n\r";
			$string.="Command:$sql \n\r";
			$string.="Data:$str_data \n\r";
			$string.="Connection info:".json_encode($con->errorInfo())." \n\r";
			$string.="\n\r------End-----\n\r";
	        error_log($string,3,SITE."logs/db_error.txt"); 
	  return 0;
   }
   
  
function update_associative_prep($table,$parameter,$where,$where_param)
	{
		
		$parameter=$this->filter_array_for_null($parameter);    //removing the null elements from the array
		$where_param=$this->filter_array_for_null($where_param); //removing the null elements from the array
		
		
		
		if($table=='' || !isset($parameter) || !isset($where) ||  strlen($where)<5|| !isset($where_param)){

			echo 'in if';
			return 0;    //will return of nothing was provided
		}

		$string="";    //this will contains update command string
		$params=array(); //Creating the array of db values
		foreach($parameter as $key=>$value)
		{
			$string.=$key."=? ,";
			$params[]=$value;
		}

		$string=substr($string,0,-1);   //removing the extra ','.

		foreach ($where_param as $key => $value)  ///appending the  where_param in main array
		{
			# code...
			$params[]=$value;

		}

		$string.=$where;
	
		

		$update_str=" update $table set $string ";   //final update command
		
		return  $this->mysql_execute_prep($update_str,$params);
	}
	
	
	public function logger($table,$query,$data,$type)
	{
		//This Stores the activity logs of the user
		////////*******************THIS SECTION LOGS THE UPDATE DATA OF ALL THE ACTIONS***********
		$new_data=$data;
		$user_name=$_SESSION['AdminInfo']['Name'];
		$user_type=$_SESSION['AdminInfo']['UserType'];
		
		//Preparing array for the logger class
		$logger_array=array(
	
			'user_name'=>$user_name,
			'user_type'=>$user_type,
			'new_data'=>$data,
			'query_string'=>$query,
			'table'=>$table,
			'type'=>$type
			);
		
		
				
		
		$obj_logger=new logger($logger_array);
		$obj_logger->logging();
		
	////////*******************THIS SECTION LOGS THE UPDATE DATA OF ALL THE ACTIONS***********
	
	
	}


	
	
   
}// Class End


?>
