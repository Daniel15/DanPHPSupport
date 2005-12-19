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

// VERSION: 0.3
// DATE: 19th December 2005

//PAGE_TICKET_CANNED.PHP: Admin Page - canned responses for tickets

if (!defined('IN_ADMIN') || eregi("page_ticket_canned.php",$_SERVER['PHP_SELF'])) {
    die("You can't run this directly!");
}

if (isset($_GET['edit']) && $_GET['edit'] != "") {
	$database->safe_query("SELECT name, text
					     	FROM ticket_canned
						    WHERE ID = %i",
						   array($_GET['edit']), __FILE__, __LINE__);
	$row = $database->fetch_row();
	$row['name'] = stripslashes($row['name']);
	$row['text'] = stripslashes($row['text']);
	
	echo <<<EOT
 <input type='hidden' name='edit2' value='{$_GET['edit']}'>
 Name: <input type='text' name='canned_name' size='40' value='{$row['name']}'><br>
 Text: <textarea name='canned_text' rows='10' cols='50'>{$row['text']}</textarea><br>
 <input type='submit' value='Save Changes'> 
EOT;
	//echo "<br>".adminLink("back");
} elseif (isset($_POST['edit2']) && $_POST['edit2'] != "") {
	if (!isset($_POST['canned_name'])) {
		writeError("ERROR: You didn't enter a name for this canned response!");
	} elseif (!isset($_POST['canned_text'])) {
		writeError("ERROR: You didn't enter any text for this canned response!");
	} else {
		$database->safe_query("UPDATE ticket_canned
								  SET name = '%s',
								      text = '%s'
								  WHERE ID =  %i",
							   array($_POST['canned_name'], $_POST['canned_text'], $_POST['edit2']),
								__FILE__, __LINE__);
								
		writeError("Saved changes to canned response #{$_POST['edit2']} ({$_POST['canned_name']})");
		echo "<br>".adminLink("back");
	}
} else {
	if (isset($_GET['del']) && $_GET['del'] != "") {
		$database->safe_query("DELETE FROM ticket_canned
								 WHERE ID = %i
								 LIMIT 1",
								array($_GET['del']), __FILE__, __LINE__);
		
		writeError("Deleted canned response #{$_GET['del']}");
		//echo "<br>".adminLink("back");

	} elseif (isset($_POST['add'])) {
		if (!isset($_POST['canned_name'])) {
			writeError("ERROR: You didn't enter a name for this canned response!");
		} elseif (!isset($_POST['canned_text'])) {
			writeError("ERROR: You didn't enter any text for this canned response!");
		} else {
			$database->safe_query("INSERT INTO ticket_canned
									  (name, text)
									 VALUES
									  ('%s', '%s')",
									array($_POST['canned_name'], $_POST['canned_text']),
									__FILE__, __LINE__);
			writeError("Added canned response '{$_POST['canned_name']}'");
		}
	}
	
	$canned_list = "";
	
	$database->query("SELECT *
						FROM ticket_canned");
						
	for ($x=0; $x < $database->get_num_rows(); $x++) {
		$row = $database->fetch_row();
		$row['name'] = stripslashes($row['name']);
		$row['text'] = stripslashes(nl2br($row['text']));
		$options = adminLink("edit", "&amp;edit={$row['ID']}")." or ".adminLink("delete", "&amp;del={$row['ID']}");
		
		$canned_list .= <<<EOT
	 <tr>
	  <td>{$row['name']}</td>
	  <td>{$row['text']}</td>
	  <td>{$options}</td>
	 </tr>
EOT;
		
	}
	echo <<<EOT
	<table table border='1' cellspacing='0' cellpadding='0' width='100%'>
	  <tr>
		<td><b>Name</b></td>
		<td><b>Text</b></td>
		<td width="100"><b>Options</b></td>
	  </tr>
	  {$canned_list}
	</table>
	
	<br><br><b>Add a new canned response:</b><br>
	 <input type='hidden' name='add' value='true'>
	 Name: <input type='text' name='canned_name' size='40'><br>
	 Text: <textarea name='canned_text' rows='10' cols='50'></textarea><br>
	 <input type='submit' value='Add!'> 
EOT;
}
?>