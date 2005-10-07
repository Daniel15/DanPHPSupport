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

//PAGE_KB_INDEX.PHP: Knowledgebase index

if (!defined('IN_SUPPORT') || eregi("page_index.php",$_SERVER['PHP_SELF'])) {
    die("You can't run this directly!");
}

pageHeader("Knowledgebase");

$kb_categories = "";
$alternating = 0;

$results = $database->query("SELECT *
							  FROM kb_categories
							  WHERE parentID = 0
							  ORDER BY Name ASC", __FILE__, __LINE__);

for ($x=0; $x < $database->get_num_rows($results); $x++) {
	$row = $database->fetch_row($results);
	if ($alternating == 0) {
		$kb_categories .= "<tr><td vAlign='top'><a href='index.php?page=kb_cat&amp;id={$row['ID']}'><b></a>{$row['name']}</b> ({$row['count']})<br>";
	} else {
		$kb_categories .= "<td vAlign='top'><a href='index.php?page=kb_cat&amp;id={$row['ID']}'><b></a>{$row['name']}</b> ({$row['count']})<br>";
	}
	
	$results2 = $database->query("SELECT title, ID
									FROM kb_articles
									WHERE categoryID = {$row['ID']}
									ORDER BY views DESC
									LIMIT 5", __FILE__, __LINE__);
	
	for ($y=0; $y < $database->get_num_rows($results2); $y++) {
		$row2 = $database->fetch_row($results2);
		$kb_categories .= "&raquo; <a href='index.php?page=kb_view&amp;id={$row2['ID']}'>".stripslashes($row2['title'])."</a><br>";
	}
	
	if ($alternating == 0) {
		$kb_categories .= "&nbsp;&nbsp;&nbsp;&nbsp;<a href='index.php?page=kb_cat&amp;id={$row['ID']}'>More &raquo;</a><br><br></td>";
		$alternating = 1;
	} else {
		$kb_categories .= "&nbsp;&nbsp;&nbsp;&nbsp;<a href='index.php?page=kb_cat&amp;id={$row['ID']}'>More &raquo;</a><br><br></td></tr>";
		$alternating = 0;
	}
									
}

echo <<<EOT
Our knowledgebase is organised into different categories. Please select the category you wish to browse. Otherwise, you can search the entire knowledgebase by entering in some keywords in the box below:<br><br>
EOT;

KBSearchForm();

echo <<<EOT
<br><br>
<table width="100%">
{$kb_categories}
</table>
<br><br>
<a href='index.php?page=index'>back</a>
EOT;

?>