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

// VERSION: 0.2
// DATE: 1st November 2005

//PAGE_KB_EDIT.PHP: Admin page-KB article editor

if (!defined('IN_ADMIN') || eregi("page_kb_edit.php",$_SERVER['PHP_SELF'])) {
    die("You can't run this directly!");
}

if (isset($_GET['del']) && $_GET['del'] != "") {
	$database->safe_query("SELECT categoryID
							  FROM kb_articles
							  WHERE ID = %i",
							array($_GET['del']), __FILE__, __LINE__);
	
	$row = $database->fetch_row();

	$database->safe_query("DELETE FROM kb_articles
							 WHERE ID = %i
							 LIMIT 1", 
							array($_GET['del']), __FILE__, __LINE__);
	
	updateKBCatCount($row['categoryID'], "-1");
	
	writeError("Deleted article ID #{$_GET['del']}");
	echo "<br>".adminLink("back");
} elseif (isset($_GET['edit']) && $_GET['edit'] != "") {
	$database->safe_query("SELECT title, body, categoryID
							  FROM kb_articles
							  WHERE ID = %i",
							array($_GET['edit']), __FILE__, __LINE__);
	
	$row = $database->fetch_row(); 
	
	$cat_list = showKBCategories(false, $row['categoryID'], 0, 0);
	
	echo <<<EOT
<input type='hidden' name='edit2' value='{$_GET['edit']}'>
Title: <input type='text' name='title' value='{$row['title']}'><br>
Category: <select name='category'>{$cat_list}</select><br>
Contents: <textarea name='body' cols='50' rows='20'>{$row['body']}</textarea><br>
<input type='submit' value='Save Changes'>
EOT;

} elseif (isset($_POST['edit2']) && $_POST['edit2'] != "") {

	$_POST['title'] = stripslashes($_POST['title']);
	$_POST['body'] = stripslashes($_POST['body']);
		
	$database->safe_query("UPDATE kb_articles
							 SET title = '%s',
							     categoryID = %i,
								 body = '%s'
							 WHERE ID = %i",
						   array($_POST['title'], $_POST['category'], $_POST['body'], 
						         $_POST['edit2']), __FILE__, __LINE__);
	
	writeError("Saved changes to article #{$_POST['edit2']}");
	echo "<br>".adminLink("back");

} else {

	$articles = "";
	
	$database->query("SELECT k.title, c.name, k.ID
						FROM kb_articles AS k, kb_categories AS c
						WHERE k.categoryID = c.ID
						ORDER by k.title ASC",
					   __FILE__, __LINE__);
	
	for ($x = 0; $x < $database->get_num_rows(); $x++) {
		$row = $database->fetch_row();
		$articles .= "
 <tr>
  <td>{$row['title']}</td>
  <td>{$row['name']}</td>
  <td>".adminLink("edit", "&amp;edit={$row['ID']}")."
	or ".adminLink("delete", "&amp;del={$row['ID']}")."
 </tr>";
	}
	
	echo <<<EOT
 Current Knowledgebase articles:<br>
 <table width='100%'>
  <tr>
   <td><b>Title</b></td>
   <td><b>Category</b></td>
   <td align='right'>&nbsp;</td>
  </tr>
 {$articles} 
 </table>
EOT;
}
 
?>