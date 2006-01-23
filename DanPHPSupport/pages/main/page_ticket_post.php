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

// VERSION: 0.4 BETA
// DATE: 22nd January 2006

//PAGE_TICKET_POST.PHP: Post a new message in a ticket

if (!defined('IN_SUPPORT') || eregi("page_ticket_post.php",$_SERVER['PHP_SELF'])) {
    die("You can't run this directly!");
}

session_start();

if (!isset($_SESSION['support_in']) || $_SESSION['support_in'] !== true) {
	header("Location: http://{$_SERVER['HTTP_HOST']}{$_SERVER['PHP_SELF']}?page=login&return=ticket_main");
}


if (!isset($_POST['post'])) {
	header("Location: http://{$_SERVER['HTTP_HOST']}{$_SERVER['PHP_SELF']}?page=ticket_main");
}

$results = $database->safe_query("SELECT userID, status, staffID, subject
						           FROM tickets
						           WHERE ID = %i",
						         array($_POST['post']), __FILE__, __LINE__);
								 
$row = $database->fetch_row();

if ($row['userID'] != $_SESSION['support_id']) {
	header("Location: http://{$_SERVER['HTTP_HOST']}{$_SERVER['PHP_SELF']}?page=ticket_main");
} elseif ($row['status'] == 2) {
	die("ERROR: This ticket is <b>CLOSED</b>, you can't post in it!");
} elseif (!isset($_POST['message']) || $_POST['message'] == "") {
	die("ERROR: You can't post a blank message!");
}

$_POST['message'] = nl2br(htmlentities($_POST['message']));

$database->safe_query("INSERT INTO ticket_messages
						  (date, message, ticketID, userID)
						  VALUES (NOW(), '%s', %i, %i)",
					   array($_POST['message'], $_POST['post'], $_SESSION['support_id']),
					   __FILE__, __LINE__);

$database->safe_query("UPDATE tickets
					     SET replyCount=replyCount+1,
						     lastPost=%i
					     WHERE ID=%i",
					  array($_SESSION['support_id'], $_POST['post']),
					  __FILE__, __LINE__);
if (isset($row['staffID']) && $row['staffID'] != 0) {
	$database->safe_query("SELECT email 
							FROM users
							WHERE id = %i
							LIMIT 1",
						   array($row['staffID']), __FILE__, __LINE__);
	$row_staff = $database->fetch_row();

//function updateAdminMail($id, $subject, $message, $status) {
	
	$message = updateAdminMail($_POST['post'], stripslashes($row['subject']), stripslashes($_POST['message']), getStatusName($row['status']));
		
	mail($row_staff['email'], "Reply to support Ticket {$_POST['post']}", $message, "From: {$SETTINGS['fromEmail']}");
	
}

pageHeader("Ticket Updated");
echo <<<EOT
You added a new message to your ticket<br>
<a href='index.php?page=ticket_view&amp;id={$_POST['post']}'>back to your ticket</a> 
EOT;
					  
?>
