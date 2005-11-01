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

//LIBRARY.PHP: General functions used by most code
//             Also see templates.php
$start_time = microtime();

require "settings.php";
require "database.php";
require "templates.php";

error_reporting(E_ALL);

$database = new class_database();
$database->connect($INFO['mysql_host'], $INFO['mysql_user'], $INFO['mysql_pass'], $INFO['mysql_db']);

error_reporting(E_ALL);
set_error_handler("errormsg");

define("DANPHPSUPPORT_DATE", "1/November/2005");
define("DANPHPSUPPORT_VER", "0.2 Beta");
define("DANPHPSUPPORT_BUILD", "2");

//Load all settings from 'settings table
$SETTINGS = "";
$results = $database->query("SELECT * FROM settings", __FILE__, __LINE__);
for ($x=0; $x < $database->get_num_rows(); $x++) {
	$row = $database->fetch_row();
	$SETTINGS[$row['field']] = $row['value'];
}


/* -------------------------
    Error handling functions
   ------------------------- */
$preverr = 0;
//Our error handler!
function errormsg($errno, $errmsg, $filename, $linenum) {
	global $preverr;
	//Get the date
    $dt = date("d/m/Y h:i:s A");
    $errortype = array (
                1   =>  "Error",
                2   =>  "Warning",
                4   =>  "Parsing Error",
                8   =>  "Notice",
                16  =>  "Core Error",
                32  =>  "Core Warning",
                64  =>  "Compile Error",
                128 =>  "Compile Warning",
                256 =>  "User Error",
                512 =>  "User Warning",
                1024=>  "User Notice"
                );
	//If there wasn't a previous error...
	if ($preverr == 0) {
		//...write the error header...
	    echo "DanPHPSupport has encountered an error. It is recommended that you send these details to the website administrator:<br><br>";
		//...and make sure it doesn't appear again.
		$preverr = 1;
	}
	//echo error details
	echo "Date/Time: $dt<br>";
	echo "<b>".$errortype[$errno]."</b> ";
	echo "in file <i>'$filename'</i>, line <b>$linenum</b>: <br>";
	echo "<font color='#FF0000' size='+1'>$errmsg</font><br><br>";
	//if error is fatal...
	if ($errno == E_ERROR) {
		//...then exit
	    exit;
	//if we raised the error, and it is fatal...
	} elseif ($errno == E_USER_ERROR) {
		//...exit
		exit;
	}
} //end error handler

function footer() {
	global $start_time;
	global $database;
	$duration = microtime_diff($start_time, microtime());
	$duration = sprintf("%0.3f", $duration);
	echo "<p align='right'><font size='-2'>Generation time: $duration seconds. Database queries: ".$database->get_query_cnt()."<br> Powered by <a href='http://danphpsupport.dansoftaustralia.net/'>DanPHPSupport</a> version ".DANPHPSUPPORT_VER." (".DANPHPSUPPORT_DATE.") by <a href='http://www.dansoftaustralia.net/'>DanSoft Australia</a></font></p>";
}

//timer function
function microtime_diff($a, $b) {
   list($a_dec, $a_sec) = explode(" ", $a);
   list($b_dec, $b_sec) = explode(" ", $b);
   return $b_sec - $a_sec + $b_dec - $a_dec;
}

function showTicketCategories($printout = false, $order = "ID ASC", $selected_id = 0) {
	global $database;
	$ticket_list = "";
	
	$results = $database->query("SELECT ID, name
								   FROM ticket_categories
								   ORDER BY {$order}", __FILE__, __LINE__);
	
	for ($x=0; $x < $database->get_num_rows($results); $x++) {
		$row = $database->fetch_row($results);
		$edit_link = "<a href='admin.php?do=page&amp;cat=2&amp;page=5&amp;edit={$row['ID']}'>edit</a>";//adminLink("edit", "&amp;edit={$row['ID']}");
		
		if ($printout == true) echo <<<EOT
<tr>
 <td>{$row['ID']}</td>
 <td>{$row['name']}</td>
 <td>{$edit_link}</td>
</tr>
EOT;
		if ($selected_id == 0 || $selected_id != $row['ID']) {
			$ticket_list .= "<option value='{$row['ID']}'>{$row['name']}</option>";
		} elseif ($selected_id == $row['ID']) {
			$ticket_list .= "<option value='{$row['ID']}' selected>{$row['name']}</option>";
		}
	}
	
	return $ticket_list;
}

function writeError($error) {
	echo "<font color='red'><b>{$error}</b></font><br>";
}

function getStatusName($statusID = 0) {
	switch($statusID) {
		case 0:
			return "Open";
			break;
		case 1:
			return "Pending";
			break;
		case 2:
			return "Closed";
			break;
		default:
			return "Unknown";
			break;	
	}
}

function showTicketSeverities($printout) {
	global $database;
	
	$severities = "<select name='severity'>";
	$results = $database->query("SELECT *
									FROM ticket_severities", __FILE__, __LINE__);
									
	for ($x=0; $x < $database->get_num_rows(); $x++) {
		$row = $database->fetch_row();

		if ($printout == true) {
			$edit_link = adminLink("edit", "&amp;edit={$row['ID']}");//." or ".adminLink("delete", "&amp;delete={$row['ID']}");
			
			echo <<<EOT
<tr>
 <td>{$row['ID']}</td>
 <td>{$row['name']}</td>
 <td align='right'  width='100'>{$edit_link}</td>
</tr>
EOT;
		}
		$severities .= "<option style='color:{$row['colour']}' value='{$row['ID']}'>{$row['name']}</option>";
	}
	$severities .= "</select>";
	
	return $severities;
}
?>
