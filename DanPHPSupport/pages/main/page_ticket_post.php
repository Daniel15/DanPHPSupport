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

//PAGE_TICKET_POST.PHP: Post a new message in a ticket

if (!defined('IN_SUPPORT') || eregi("page_ticket_submit.php",$_SERVER['PHP_SELF'])) {
    die("You can't run this directly!");
}

session_start();

if (!isset($_SESSION['support_in']) || $_SESSION['support_in'] !== true) {
	header("Location: http://{$_SERVER['HTTP_HOST']}{$_SERVER['PHP_SELF']}?page=login&return=ticket_main");
}


if (!isset($_POST['post'])) {
	header("Location: http://{$_SERVER['HTTP_HOST']}{$_SERVER['PHP_SELF']}?page=ticket_main");
}

$results = $database->safe_query("SELECT userID, status
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

$_POST['message'] = nl2br($_POST['message']);

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

pageHeader("Ticket Updated");
echo <<<EOT
You added a new message to your ticket<br>
<a href='index.php?page=ticket_view&amp;id={$_POST['post']}'>back to your ticket</a> 
EOT;
					  
?>
