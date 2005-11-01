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

//PAGE_KB_SEEACH.PHP: Knowledgebase search page

if (!defined('IN_SUPPORT') || eregi("page_kb_search.php",$_SERVER['PHP_SELF'])) {
    die("You can't run this directly!");
}

pageHeader("Knowledgebase Search");

if (isset($_GET['q'])) {
	$results = $database->safe_query("SELECT kb_articles.ID, kb_articles.title, 
										     kb_articles.views, kb_articles.categoryID, 
											 kb_categories.name AS catName,
										     MATCH(kb_articles.title, kb_articles.body)
										           AGAINST('%s') AS score
								       FROM kb_articles, kb_categories
								       WHERE MATCH(kb_articles.title, kb_articles.body)
								             AGAINST('%s') and
								       kb_categories.ID = kb_articles.categoryID
								       ORDER BY score DESC, views DESC", 
									   array($_GET['q'], $_GET['q']), __FILE__, __LINE__);
	$results_count = $database->get_num_rows();
	
	if ($results_count == 0) {
		echo <<<EOT
<b><font color='red'>Your search for '{$_GET['q']}' produced no results. </font></b><br>
<b>Suggestions:</b>
 <ul>
  <li>Words that appear in more than 50% of articles are automatically filtered out. Try more specific words
  <li>Check your spelling
  <li>Try different words that have the same meaning
 </ul>
EOT;
	} else {
		echo <<<EOT
	Your query '{$_GET['q']}' returned {$results_count} results<br><br>
	<table width='100%' cellspacing='0' cellpadding='0' border='0'>
	 <tr>
	  <td width='60'><b>Score</b></td>
	  <td><b>Title</b></td>
	  <td><b>Category</b></td>
	  <td width='60'><b>Views</b></td>
	 </tr>
EOT;
	
		for ($x=0; $x < $results_count; $x++) {
			$row = $database->fetch_row();
			$row['score'] = round($row['score'] * 100, 2); 
			echo <<<EOT
	<tr>
	 <td>{$row['score']}</td>
	 <td><a href='index.php?page=kb_view&amp;id={$row['ID']}'>{$row['title']}</a></td>
	 <td><a href='index.php?page=kb_cat&amp;id={$row['categoryID']}'>{$row['catName']}</a></td>
	 <td>{$row['views']}</td>
	</tr>
EOT;
		}
	}
	echo "</table>";
} else {
	$_GET['q'] = "";
}

KBSearchForm($_GET['q']);

?>