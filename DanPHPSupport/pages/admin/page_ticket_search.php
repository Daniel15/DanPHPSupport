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
// DATE: 17th December 2005

//PAGE_TICKET_SEARCH.PHP: Admin Page - Search Support Tickets

if (!defined('IN_ADMIN') || eregi("page_ticket_search.php",$_SERVER['PHP_SELF'])) {
    die("You can't run this directly!");
}

if (isset($_POST['q']) && $_POST['q'] != "") {
	$database->safe_query("SELECT UNIX_TIMESTAMP(t.date) as dateUNIX, t.subject, t.ID, c.name,
							   MATCH (m.message) AGAINST ('%s') AS score
							   FROM tickets AS t, ticket_categories AS c, ticket_messages AS m
							   WHERE MATCH (m.message) AGAINST ('%s') AND
							         m.ticketID = t.ID AND
							         t.category = c.ID
							   ORDER BY score DESC, t.ID DESC",
							 array($_POST['q'], $_POST['q']), __FILE__, __LINE__);
	
	$results_count = $database->get_num_rows();
	
	if ($results_count == 0) {
		echo "<b><font color='red'>Your search for '{$_POST['q']}' produced no results. </font></b><br>";
	} else {
		echo <<<EOT
	Your query '{$_POST['q']}' returned {$results_count} results<br><br>
	<table width='100%' cellspacing='0' cellpadding='0' border='0'>
	 <tr>
	  <td width='60'><b>Score</b></td>
	  <td><b>Opened On</b></td>
	  <td><b>Subject</b></td>
	  <td><b>Category</b></td>
	 </tr>
EOT;
	
		for ($x=0; $x < $results_count; $x++) {
			$row = $database->fetch_row();
			$row['score'] = round($row['score'] * 100, 2); 
			$date = formatDate("Y-m-d h:i:s A", $row['dateUNIX']);
			
			echo <<<EOT
	<tr>
	 <td>{$row['score']}</td>
	 <td>$date</td>
	 <td><a href='admin.php?do=page&amp;cat=2&amp;page=4&amp;id={$row['ID']}'>{$row['subject']}</a></td>
	 <td>{$row['name']}</td>
	</tr>
EOT;
		}
	}
	echo "</table><br><br>";
									 
} else {
	$_POST['q'] = "";
}

echo <<<EOT
 Keywords: <input type='text' name='q' size='50' value='{$_POST['q']}'>
 <input type='submit' value='search'><br><br>
 Please note that only the messages are indexed. Ticket subjects are not indexed.
EOT;

?>
