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

//PAGE_KB_CATS.PHP: Admin Page - KB category editor

if (!defined('IN_ADMIN') || eregi("page_kb_cats.php",$_SERVER['PHP_SELF'])) {
    die("You can't run this directly!");
}

if (isset($_POST['add'])) {
	if (!isset($_POST['cat_name'])) {
		writeError("ERROR: No category name entered!");
	} else {
		$database->safe_query("INSERT INTO kb_categories
						          (name, parentID)
							      VALUES ('%s', %i)", 
							   array($_POST['cat_name'], $_POST['parent']),
							   __FILE__, __LINE__);
		writeError("Added category '{$_POST['cat_name']}'");
		echo adminLink("back");
	}
} elseif (isset($_GET['edit'])) {
	$results = $database->safe_query("SELECT ID, name, parentID
										FROM kb_categories
										WHERE ID = %s",
									  array($_GET['edit']), __FILE__, __LINE__);
	$row = $database->fetch_row();
	
	$cat_list = showKBCategories(false, $row['parentID'], 0, 0);
	echo <<<EOT
<h2>Editing category #{$row['ID']}</h2>
 <input type='hidden' name='edit2' value='{$row['ID']}'>
 Name: <input type='text' name='cat_name' value='{$row['name']}'><br>
 Parent: <select name='parent'><option value='0'>[NONE]</option>{$cat_list}</select><br>
 <input type='submit' value='edit'> 
EOT;
} elseif (isset($_POST['edit2'])) {
	if (!isset($_POST['cat_name'])) {
		writeError("ERROR: No category name entered!");
	} else {
		$database->safe_query("UPDATE kb_categories
								 SET name='%s',
								     parentID=%i
								  WHERE ID=%i",
							   array($_POST['cat_name'], $_POST['parent'], $_POST['edit2']),
							   __FILE__, __LINE__);
		writeError("Edited category #{$_POST['edit2']} ({$_POST['cat_name']})");
		echo adminLink("back");
	}
} else {
	echo <<<EOT
	<table border='1' cellspacing='0' cellpadding='0' width='100%'>
	 <tr>
	  <td><b>ID</b></td>
	  <td><b>Name</b></td>
	  <td align='right' width='10'><b>Options</b></td>
	 </tr>
EOT;
	 $cat_list = showKBCategories(true, 0, 0, 0);
	 
	echo <<<EOT
	</table>
	<br><b>Add a new category:</b><br>
	 <input type='hidden' name='add' value='true'>
	 Name: <input type='text' name='cat_name'><br>
	 Parent: <select name='parent'><option value='0' selected>[NONE]</option>{$cat_list}</select><br>
	 <input type='submit' value='Add!'> 
EOT;
}
?>