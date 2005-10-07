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
// DATE: 27th September 2005

//PAGE_TICKET_SEVS.PHP: Admin Page - ticket severity editor

if (!defined('IN_ADMIN') || eregi("page_kb_cats.php",$_SERVER['PHP_SELF'])) {
    die("You can't run this directly!");
}

if (isset($_POST['add'])) {
	if (!isset($_POST['sev_name']) || $_POST['sev_name'] == "") {
		writeError("ERROR: No severity name entered!");
	} elseif (!isset($_POST['colour']) || $_POST['colour'] == "") {
		writeError("ERROR: No colour entered!");
	} else {
		$database->safe_query("INSERT INTO ticket_severities (name, colour)
							      VALUES ('%s', '%s')", 
							   array($_POST['sev_name'], $_POST['colour']),
							   __FILE__, __LINE__);
		writeError("Added category '{$_POST['sev_name']}' with color '{$_POST['colour']}'");
		echo adminLink("back");
	}
} elseif (isset($_GET['edit'])) {
	$results = $database->safe_query("SELECT *
										FROM ticket_severities
										WHERE ID = %s",
									  array($_GET['edit']), __FILE__, __LINE__);
	$row = $database->fetch_row();

	echo <<<EOT
<h2>Editing severity #{$row['ID']}</h2>
 <input type='hidden' name='edit2' value='{$row['ID']}'>
 Name: <input type='text' name='cat_name' value='{$row['name']}'><br>
 Colour: <input type='text' name='colour' value='{$row['colour']}'><br>
 <input type='submit' value='edit'> 
EOT;
} elseif (isset($_POST['edit2'])) {
	if (!isset($_POST['cat_name'])) {
		writeError("ERROR: No severity name entered!");
	} else {
		$database->safe_query("UPDATE ticket_severities
								 SET name='%s', colour='%s'
								  WHERE ID=%i",
							   array($_POST['cat_name'], $_POST['colour'], $_POST['edit2']),
							   __FILE__, __LINE__);
		writeError("Edited severity #{$_POST['edit2']} ({$_POST['cat_name']})");
		echo adminLink("back");
	}
} else {
	echo <<<EOT
	<table border='1' cellspacing='0' cellpadding='0' width='100%'>
	 <tr>
	  <td><b>ID</b></td>
	  <td><b>Name</b></td>
	  <td align='right' width='100'><b>Options</b></td>
	 </tr>
EOT;
	 $cat_list = showTicketSeverities(true);
	 
	echo <<<EOT
	</table>
	<br><b>Add a new category:</b><br>
	 <input type='hidden' name='add' value='true'>
	 Name: <input type='text' name='cat_name'><br>
	 Colour: <input type='text' name='colour'> <font size='-2'>- Enter colour name (eg. 'red') or HTML colour code (eg. #FF0000)</font><br>
	 <input type='submit' value='Add!'> 
EOT;
}
?>