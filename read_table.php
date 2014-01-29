<?php
/**
 * A simple, clean and secure CSV to MySQL using PHP Script.
 * Available in First beta version : 0.1.
 *
 * CSV to MySQL using PHP First SIMPLE VERSION 0.1
 *
 * @author PANJABRAO SHELKE
 * @link https://github.com/panjabshelke/CSV-to-MySQL-using-PHP
 * 
 * @license http://opensource.org/licenses/MIT MIT License
 */
 
 
define('db_name', "PanjabPHPCSV"); // folder name which contains the csv files
define('db_path', getcwd() . "/" . db_name);
define('db_host', 'localhost'); // Your Server Name
define('db_user', 'root'); // User Name of MySQL DATABASE
define('db_password', ''); // Password for MySQL DATABASE

create_database();

//Created database function for creating database
function create_database(){
  $mysql_handle = mysql_connect(db_host, db_user, db_password);
  if (!$mysql_handle) { die('Could not connect: ' . mysql_error()); }
  //$sql = 'CREATE DATABASE ' . db_name;
  $sql = 'CREATE DATABASE IF NOT EXISTS ' . db_name;
  echo "<br/>***************************** DATA BASE ************************************<br/><br/>";
  if (mysql_query($sql, $mysql_handle)) {
      echo "Database " . db_name . " created successfully <br/>";
      read_database();
  } else {
      echo 'Error creating database: ' . mysql_error() . "\n <br/>";
  }
  
  mysql_close($mysql_handle);
}
//Database connection 
function connect_database(){
  return mysqli_connect(db_host, db_user, db_password, db_name);// or die("Error " . mysqli_error($link));
}
//Read database
function read_database(){
  $connection = connect_database();
  if (is_dir(db_path)) {
      if ($dh = opendir(db_path)) {
          while (($file = readdir($dh)) !== false) {
              if( is_csv( $file ) ) {
                setup_table( $file, $connection );
              }
          }
          closedir($dh);
      }
  }
  mysqli_close( $connection );
}

function setup_table( $table, $connection ){
  $table_name = str_replace(".csv", "", $table);
  echo "<br/>***************************** TABLE Creation Start *****************************<br/><br/>";
  echo "\nReading table: $table_name\n <br/>";
  $table_path = db_path . "/" . $table;
  if (($handle = fopen($table_path, "r")) !== FALSE) {
      $row_count = 1;
      while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
          $num = count($row); // number of columns (comma-seperated values)
          if($row_count == 1){
            // column names --> skip
            create_table( $table_name, $row, $connection );
            $row_count++; continue;
          }
          echo "\ninserting values from row " . ($row_count-1) . " into '$table_name' <br/>";
          insert_into_table( $table_name, $row, $connection );
          $row_count++;
      }
      fclose($handle);
  }
  echo "<br/>***************************** TABLE Creation END *****************************<br/>";
}


function create_table($table_name, $columns, $connection){
  echo "creating table '$table_name'\n <br/>";
  echo "adding columns to '$table_name' <br/>";
  //$query = "CREATE TABLE $table_name(" . implode(' TEXT,', $columns) . " TEXT)" or die("Error in the consult.." . mysqli_error($connection));
 $query = "CREATE TABLE IF NOT EXISTS $table_name(" . implode(' TEXT,', $columns) . " TEXT)" or die("Error in the consult.." . mysqli_error($connection));
  mysqli_query($connection, $query);
}

function insert_into_table($table_name, $values, $connection){
  $query = "INSERT INTO $table_name VALUES ('". implode("','", $values) . "')" or die("Error in the consult.." . mysqli_error($connection));
  mysqli_query($connection, $query);
}

function is_csv($file){

  return (pathinfo($file)["extension"] == "csv");
  /*
  	if it's not work use below code
	$file_path = pathinfo($file);
	return ($file_path["extension"] == "csv");
  */
}