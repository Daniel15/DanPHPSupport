<?php
/***********************************\
|           DanPHPSupport           |
|  A Support System written in PHP  |
|-----------------------------------|
|   Written by Daniel Lo Nigro of   |
|         DanSoft Australia         |
| http://www.dansoftaustralia.net/  |
|-----------------------------------|
|   Please feel free to distribute  |
|    this script, as long as this   |
|       header stays attached.      |
\***********************************/

// VERSION: 0.1
// DATE: 26th September 2005

//DATABASE.PHP: Database access functions
//              Utilises SafeSQL (c)2001-2004 Monte Ohrt <monte@ispi.net>

//The general rule with the database access is, call $database->query if there is no user input
//involved, or $database->safe_query if user input is involved. safe_query will use SafeSQL
//fuctions to sanitise the data (make it safe) before entering it into the database.

require 'SafeSQL.class.php';
$SafeSQL = new SafeSQL_MySQL;

class class_database {
	var $connection = "";
	var $query = "";
	var $query_count = 0;
	var $record_row = array();
	
	function connect($host, $user, $pass, $database){
	
		$this->connection = mysql_connect($host, $user, $pass);
		mysql_select_db($database, $this->connection) or trigger_error("Database not found: $database", E_USER_WARNING);
		return $this->connection;
	}
	
	function disconnect() { 
        return mysql_close($this->connection);
    }
	
	function safe_query($query_string, $query_vars, $file = "Unknown", $line = "Unknown") {
		global $SafeSQL;
		
		$query_fixed = $SafeSQL->query($query_string, $query_vars);
		return $this->query($query_fixed, $file, $line);
	}
	
	function query($query_string, $file = "Unknown", $line = "Unknown") {
		$this->query = mysql_query($query_string, $this->connection) or trigger_error("Error while perfoming query '$query_string': ".mysql_error($this->connection)."<br><br>File: $file<br>Line: $line");
		$this->query_count++;
		return $this->query;
	}
	
	function fetch_row($query_id = "") {
		if ($query_id == "") $query_id = $this->query;
		$this->record_row = mysql_fetch_array($query_id, MYSQL_ASSOC);
		return $this->record_row;
	}
	
   function get_affected_rows() {
        return mysql_affected_rows($this->connection);
    }
    
    function get_num_rows($query_id = "") {
		if ($query_id == "") $query_id = $this->query;
        return mysql_num_rows($query_id);
    }
    
    function get_insert_id() {
        return mysql_insert_id($this->connection);
    } 

    function get_query_cnt() {
        return $this->query_count;
    }
}
?>