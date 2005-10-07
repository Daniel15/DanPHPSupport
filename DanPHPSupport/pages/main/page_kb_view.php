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

//PAGE_KB_VIEW.PHP: View a Knowledgebase page.

if (!defined('IN_SUPPORT') || eregi("page_kb_view.php",$_SERVER['PHP_SELF'])) {
    die("You can't run this directly!");
}

if (!isset($_GET['id']) || $_GET['id'] == 0) {
	header("Location: http://{$_SERVER['HTTP_HOST']}{$_SERVER['PHP_SELF']}?page=kb_index");
	die();
}

$database->safe_query("UPDATE kb_articles
						 SET views=views+1
						 WHERE ID = %i",
					   array($_GET['id']), __FILE__, __LINE__);
					   
$results = $database->safe_query("SELECT title, body, views, categoryID
									FROM kb_articles
									WHERE ID = %i",
								  array($_GET['id']), __FILE__, __LINE__);

$row = $database->fetch_row();
$row['title'] = stripslashes($row['title']);
$row['body'] = nl2br(stripslashes($row['body']));
								  
pageHeader("Knowledgebase - Viewing Article");
echo <<<EOT
<center><h2>{$row['title']}</h2></center>
{$row['body']}<br><br><br>
<font size='-2'>This article has been viewed {$row['views']} times.</font><br>
<a href='index.php?page=kb_cat&amp;id={$row['categoryID']}'>back</a>
EOT;
								  

?>