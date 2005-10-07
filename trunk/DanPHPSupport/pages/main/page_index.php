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

//PAGE_INDEX.PHP: The main page

if (!defined('IN_SUPPORT') || eregi("page_index.php",$_SERVER['PHP_SELF'])) {
    die("You can't run this directly!");
}

pageHeader("Support Home");

$top_kb = "";

$results = $database->query("SELECT kb_articles.ID, kb_articles.title, 
									kb_articles.views, kb_categories.name AS catName
							  FROM kb_articles, kb_categories
							  WHERE kb_categories.ID = kb_articles.categoryID
							  ORDER BY views DESC
							  LIMIT 10", __FILE__, __LINE__);
							  
for ($x=1; $x <= $database->get_num_rows(); $x++) {
	$row = $database->fetch_row();
	$row['title'] = stripslashes($row['title']);
	$top_kb .= "
<tr>
<td>{$x}. <a href='index.php?page=kb_view&amp;id={$row['ID']}'>{$row['title']}</a> ({$row['catName']})</td>
<td align='right'>{$row['views']}</td>
</tr>";
}

echo <<<EOT
Welcome to our online support help desk! Please choose the option that you are interested in:<br><br>
 <a href="index.php?page=kb_index"><b>Knowledgebase</b></a> - Browse through the knowledgebase for the most frequently asked questions<br>
 <a href="index.php?page=ticket_submit"><b>Submit a Support Ticket</b></a> - If your question wasn't answered in the Knowledgebase, please feel free to submit a Support Ticket. You'll need to create signup for a free account.<br>
 <a href="index.php?page=ticket_main"><b>Ticket Status</b></a> - Check your support ticket's status. You'll need to sign in using your username and password<br><br>
 
 <table width="100%">
  <tr>
   <td><h2>Popular Knowledgebase Topics</h2></td>
   <td align="right"><h2>Views</h2></td>
  </tr>
  {$top_kb}
 </table>
 
EOT;
?>