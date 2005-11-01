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
// DATE: 31st October 2005

//PAGE_KB_ADD.PHP: Admin page - Add KB article

if (!defined('IN_ADMIN') || eregi("page_kb_add.php",$_SERVER['PHP_SELF'])) {
    die("You can't run this directly!");
}

if (isset($_GET['submit'])) {
	if (!isset($_POST['title']) || $_POST['title'] == "") {
		writeError("ERROR: No title entered!");
	} elseif (!isset($_POST['body']) || $_POST['body'] == "") {
		writeError("ERROR: No body text entered!");
	} else {
		$_POST['title'] = stripslashes($_POST['title']);
		$_POST['body'] = stripslashes($_POST['body']);
		
		$database->safe_query("INSERT INTO kb_articles
								  (title, body, categoryID)
							       VALUES ('%s', '%s', %i)",
							   array($_POST['title'], $_POST['body'], $_POST['category']),
							   __FILE__, __LINE__);
		
		updateKBCatCount($_POST['category']);
		writeError("Added article '{$_POST['title']}'");
		
		
								 
	}
}

$cat_list = showKBCategories(false, 0, 0, 0);
echo <<<EOT
Title: <input type='text' name='title'><br>
Category: <select name='category'>{$cat_list}</select><br>
Contents: <textarea name='body' cols='50' rows='20'></textarea><br>
<input type='submit' value='Add Article'>
EOT;
?>