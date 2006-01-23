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

//PAGE_TICKET_SUBMIT.PHP: Page to submit a new ticket

if (!defined('IN_SUPPORT') || eregi("page_ticket_submit.php",$_SERVER['PHP_SELF'])) {
    die("You can't run this directly!");
}

session_start();

if (!isset($_SESSION['support_in']) || $_SESSION['support_in'] !== true) {
	header("Location: http://{$_SERVER['HTTP_HOST']}{$_SERVER['PHP_SELF']}?page=login&return=ticket_submit");
}

if (isset($_POST['submit2'])) {
	if (!isset($_POST['subject']) || $_POST['subject'] == "") {
		writeError("ERROR: You must enter a subject. Please click the 'Back' button in your browser to fix this.");
		die();
	} elseif (!isset($_POST['message']) || $_POST['message'] == "") {
		writeError("ERROR: You must enter a message (if you don't want to write a message, why are you filling in a support ticket?). Please click the 'Back' button in your browser to fix this.");
		die();
	} else {
		$database->safe_query("INSERT INTO tickets
								 (userID, date, category, subject, severity, lastPost)
							     VALUES (%i, NOW(), %i, '%s', %i, %i)",
							   array($_SESSION['support_id'], $_POST['category'], 
							         $_POST['subject'], $_POST['severity'], $_SESSION['support_id']),
							   __FILE__, __LINE__);
		$ticket_id = $database->get_insert_id();
		
		$database->safe_query("INSERT INTO ticket_messages
								  (date, message, ticketID, userID)
								  VALUES (NOW(), '%s', %i, %i)",
							  array(nl2br(htmlentities($_POST['message'])), $ticket_id, $_SESSION['support_id']),
							  __FILE__, __LINE__);
							  
		$message = newTicketMail($_SESSION['support_id'], stripslashes($_POST['subject']), $_POST['severity'],
								 stripslashes($_POST['message']));
		
		mail($SETTINGS['adminEmail'], "New support ticket (ID #{$ticket_id})", $message, "From: {$SETTINGS['fromEmail']}");
		pageHeader("Ticket Received");
		echo "Your ticket was received by our support team and we will reply as soon as possible.<br><br><a href='index.php?page=ticket_main'>back</a>";
	}
} else {
	pageHeader("Submit a Support Ticket");
	
	$cat_list = showTicketCategories(false, "name ASC", 0);
		
	$severities = showTicketSeverities(false);
	
	echo <<<EOT
	If you would like to submit a support ticket to our support team, please use the form below. We will try to reply as soon as possible.<br><br>
	<form action='index.php?page=ticket_submit' method="POST">
	 <input type='hidden' name='submit2' value='true'>
	 Subject: <input type='text' name='subject' size='40'><br>
	 Category: <select name='category'>{$cat_list}</select><br>
	 Severity: {$severities}<br>
	 Message: <textarea rows='20' cols='70' name='message'>Type your message here</textarea><br>
	 <input type='submit' value='Submit Ticket'>
EOT;
}
?>