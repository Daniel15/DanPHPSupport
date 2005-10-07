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

//PAGE_TICKET_VIEW.PHP: Page to View a ticket

if (!defined('IN_SUPPORT') || eregi("page_ticket_view.php",$_SERVER['PHP_SELF'])) {
    die("You can't run this directly!");
}

session_start();

if (!isset($_SESSION['support_in']) || $_SESSION['support_in'] !== true) {
	header("Location: http://{$_SERVER['HTTP_HOST']}{$_SERVER['PHP_SELF']}?page=login&return=ticket_main");
}


if (!isset($_GET['id'])) {
	header("Location: http://{$_SERVER['HTTP_HOST']}{$_SERVER['PHP_SELF']}?page=ticket_main");
}

$results = $database->safe_query("SELECT t.ID, t.userID, UNIX_TIMESTAMP(t.date) AS dateUNIX,
										 t.subject, t.status, t.staffid, t.replyCount,
										 c.name AS catName, s.name AS sevName, s.colour,
										 u.firstName, u.lastName, t.staffID
									FROM tickets AS t, ticket_categories AS c,
									ticket_severities AS s, users AS u
								    WHERE t.ID=%i AND t.category = c.ID AND t.severity = s.ID AND
									      t.lastPost = u.ID",
								   array($_GET['id']), __FILE__, __LINE__);
								   
$row = $database->fetch_row();

if ($database->get_num_rows() == 0) {
	die("ERROR: Ticket not found!");
} elseif ($row['userID'] != $_SESSION['support_id']) {
	header("Location: http://{$_SERVER['HTTP_HOST']}{$_SERVER['PHP_SELF']}?page=ticket_main");
} else {
	if ($row['staffID'] == 0) {
		$owner = "nobody";
	} else {
		
		$results_own = $database->query("SELECT firstName, lastName
										 FROM users
										 WHERE ID={$row['staffID']}",
									   __FILE__, __LINE__);
		$row_own = $database->fetch_row();
		$owner = $row_own['firstName']." ".$row_own['lastName'];
	}
	
	$results_user = $database->query("SELECT firstName, lastName
									FROM users
									WHERE ID={$row['userID']}",
									__FILE__, __LINE__);
	$row_user = $database->fetch_row();
	$user = $row_user['firstName']." ".$row_user['lastName'];
		
	$status = getStatusName($row['status']);
	$createdDate = date("l, jS F Y h:i:s A", $row['dateUNIX']+$INFO['time_offset']);
	pageHeader("View a Ticket");
	
	echo <<<EOT
<h2><center>{$row['subject']}</center></h2>
<b>Ticket Information:</b>
<table width='100%' border=2>
 <tr>
  <td width='50%'>
   ID: {$row['ID']}<br>
   Created on: {$createdDate}<br>
   Created by: {$user}<br>
   Category: {$row['catName']}<br>
   Subject: {$row['subject']}<br>
   Severity: <font color='{$row['colour']}'>{$row['sevName']}</font><br>
   </td>
  <td width='50%'>
   Status: {$status}<br>
   Staff Assigned: {$owner}<br>
   Last Reply by: {$row['firstName']} {$row['lastName']}<br>
   Replies: {$row['replyCount']}<br>
  </td>
 </tr>
</table>
EOT;

	$results = $database->safe_query("SELECT UNIX_TIMESTAMP(m.date) as dateUNIX, m.message,
										u.firstName, u.lastName
										FROM ticket_messages AS m, users AS u
										WHERE u.ID = m.userID AND m.ticketID={$row['ID']}
										ORDER BY m.date ASC",
										__FILE__, __LINE__);
	
	for ($x=1; $x <= $database->get_num_rows(); $x++) {
		$row_msg = $database->fetch_row();
		$row_msg['message'] = stripslashes($row_msg['message']);
		$time = date("l, jS F Y h:i:s A", $row_msg['dateUNIX']+$INFO['time_offset']);
		echo <<<EOT
<br><br><b>Message {$x}, posted by {$row_msg['firstName']} {$row_msg['lastName']} at {$time}</b><br>
{$row_msg['message']}
EOT;
	}
	if ($row['status'] == 2) {
		echo "<br><br><br><b>You cannot reply to this ticket, it is closed</b>";
	} else {
		echo <<<EOT
<br><br><br>
<form action='index.php?page=ticket_post' method='POST'>
 Post a new message:<br>
 <input type='hidden' name='post' value='{$row['ID']}'>
 <textarea name='message' cols='70' rows='10'>Type your message here</textarea><br>
 <input type='submit' value='Post Message'>
</form>
EOT;
	}
}
 
?>
