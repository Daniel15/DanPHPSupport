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

//PAGE_TICKET_LIST.PHP: Admin Page - List of tickets (Open/Closed/Pending)

if (!defined('IN_ADMIN') || eregi("page_ticket_list.php",$_SERVER['PHP_SELF'])) {
    die("You can't run this directly!");
}

if (!isset($action) || $action == "") {
	die("Error: no action!");
}

//pretty messy code here, this will be rewritten soon.
// --daniel15
if ($action == "open") {
	$where_clause = "status = 0";
} elseif ($action == "pending") {
	$where_clause = "status = 1";
} elseif ($action == "closed") {
	$where_clause = "status = 2";
} else {
	die("Error: {$action} is an invalid action!");
}

$tickets = "";
$alternating ="#DDDDDD";

//that's a BIG query!
$results = $database->query("SELECT t.ID, UNIX_TIMESTAMP(t.date) AS dateUNIX, t.subject, t.status,
							        c.name AS catName, s.name AS sevName, s.colour, u.firstName,
									u.lastName, t.replyCount
							    FROM tickets AS t, ticket_categories AS c, ticket_severities AS s,
								     users AS u
								WHERE {$where_clause} AND t.category = c.ID AND t.severity = s.ID
								      AND t.lastPost = u.ID
								ORDER BY t.severity DESC, t.ID DESC",
								__FILE__, __LINE__);


for ($x=0; $x < $database->get_num_rows(); $x++) {
	$row = $database->fetch_row();
	$tickets .= "
<tr bgcolor='{$alternating}'>
 <td width='7%'>{$row['ID']}</td>
 <td width='100'>".formatDate("Y-m-d h:i:s A", $row['dateUNIX'])."</td>
<!-- <td>".getStatusName($row['status'])."</td> -->
 <td><a href='admin.php?do=page&amp;cat=2&amp;page=4&amp;id={$row['ID']}'>{$row['subject']}</a></td>
 <td>{$row['catName']}</td>
 <td><font color='{$row['colour']}'><b>{$row['sevName']}</b></font></td>
 <td>{$row['replyCount']}</td>
 <td>{$row['firstName']} {$row['lastName']}</td>";
 
 	$alternating = ($alternating == "#DDDDDD") ? "#EEEEEE" : "#DDDDDD";
}

echo <<<EOT
<table width='100%' cellpadding='0' cellspacing='0' border='0'>
 <tr>
  <td width='7%'><b>ID</b></td>
  <td width='100'><b>Opened on</b></td>
<!--  <td width='45'><b>Status</b></td> -->
  <td><b>Subject</b></td>
  <td><b>Category</b></td>
  <td><b>Severity</b></td>
  <td><b>Replies</b></td>
  <td><b>Last reply by</b></td>
 </tr>
 {$tickets}
</table>

<br><br>
<a href='index.php?page=index'>back</a>
EOT;

?>
