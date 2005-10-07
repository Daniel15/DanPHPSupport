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

//PAGE_KB_CAT.PHP: Browse a Knowledgebase category

if (!defined('IN_SUPPORT') || eregi("page_kb_cat.php",$_SERVER['PHP_SELF'])) {
    die("You can't run this directly!");
}

if (!isset($_GET['id']) || $_GET['id'] == 0) {
	header("Location: http://{$_SERVER['HTTP_HOST']}{$_SERVER['PHP_SELF']}?page=kb_index");
	die();
}

$categories = "";
$articles = "";

$results = $database->safe_query("SELECT ID, name, count
									FROM kb_categories
									WHERE parentID = %i
									ORDER by name ASC",
								  array($_GET['id']), __FILE__, __LINE__);

if ($database->get_num_rows() == 0) {
	$categories = "<i>[NONE]</i><br>";
} else {
	for ($x=0; $x < $database->get_num_rows(); $x++) {
		$row = $database->fetch_row();
		$categories .= "<a href='index.php?page=kb_cat&amp;id={$row['ID']}'>".stripslashes($row['name'])."</a> ({$row['count']})<br>";
	}
}




$results_articles = $database->safe_query("SELECT ID, title, views
							                  FROM kb_articles
							                  WHERE categoryID = %i
							                  ORDER BY title ASC", 
							                array($_GET['id']), __FILE__, __LINE__);
							  
for ($x=1; $x <= $database->get_num_rows(); $x++) {
	$row = $database->fetch_row();
	$articles .= "
<tr>
 <td><a href='index.php?page=kb_view&amp;id={$row['ID']}'>".stripslashes($row['title'])."</a></td>
 <td align='right'>{$row['views']}</td>
</tr>";
}

pageHeader("Knowledgebase - Browsing Category");

echo <<<EOT
<h2>Sub-categories</h2>
{$categories}
<h2>Articles</h2>
 <table width='100%' cellspacing='0' cellpadding='0' border='0'>
  <tr>
   <td><b>Title</b></td>
   <td align="right"><b>Views</b></td>
  </tr>
  {$articles}
 </table>
 <br>
 <a href='index.php?page=kb_index'>back</a>
EOT;

?>