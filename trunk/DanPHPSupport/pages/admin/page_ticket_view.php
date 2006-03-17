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

//PAGE_TICKET_VIEW.PHP: Admin Page - View a ticket

if (!defined('IN_ADMIN') || eregi("page_ticket_view.php",$_SERVER['PHP_SELF'])) {
    die("You can't run this directly!");
}

if (isset($_POST['post']) && $_POST['post'] != "") {

	$results = $database->safe_query("SELECT staffID
									   FROM tickets
									   WHERE ID = %i",
									 array($_POST['post']), __FILE__, __LINE__);
									 
	$row = $database->fetch_row();
	
	if ((($row['staffID'] != 0) && $row['staffID'] != $_SESSION['admin_id'])) {
		die("ERROR: Another staff member has claimed ownership of this ticket, therefore you can't post in it.");
	} elseif (!isset($_POST['message']) || $_POST['message'] == "") {
		die("ERROR: You can't post a blank message!");
	}
	
	$database->safe_query("INSERT INTO ticket_messages
							  (date, message, ticketID, userID)
							  VALUES (NOW(), '%s', %i, %i)",
						   array(nl2br(htmlentities($_POST['message'])), $_POST['post'], $_SESSION['admin_id']),
						   __FILE__, __LINE__);
	
	$database->safe_query("UPDATE tickets
							 SET replyCount=replyCount+1,
								 lastPost=%i, status=%i
							 WHERE ID=%i",
						  array($_SESSION['admin_id'], $_POST['status'], $_POST['post']),
						  __FILE__, __LINE__);
	
	$database->safe_query("SELECT t.userID, t.subject, u.email
							 FROM tickets AS t, users AS u
							 WHERE t.ID = %i AND t.userID = u.ID",
							array($_POST['post']), __FILE__, __LINE__);
	$row = $database->fetch_row();
	
	$message = updateMail($_POST['post'], stripslashes($row['subject']), stripslashes($_POST['message']), getStatusName($_POST['status']), "http://{$_SERVER['HTTP_HOST']}".dirname($_SERVER['PHP_SELF']));
	
	mail($row['email'], "Support Ticket {$_POST['post']} updated", $message, "From: {$SETTINGS['fromEmail']}");
	writeError("New message added to ticket");
	$_GET['id'] = $_POST['post'];
}

if (!isset($_GET['id'])) {
	die("ERROR: No ticket ID passed!");
}

if (isset($_GET['own']) && $_GET['own'] == "true") {
	$results = $database->safe_query("SELECT staffID
										FROM tickets
										WHERE ID=%i",
									  array($_GET['id']), __FILE__, __LINE__);
	$row = $database->fetch_row();
	if ($row['staffID'] != 0) die("ERROR: This ticket already has an owner!");
	
	$database->safe_query("UPDATE tickets
							 SET staffID = '%s'
							 WHERE ID=%i",
						  array($_SESSION['admin_id'], $_GET['id']),
						  __FILE__, __LINE__);
	
	writeError("You have taken ownership of this ticket.");
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
} elseif ((($row['staffid'] != 0) && $row['staffid'] != $_SESSION['admin_id'])) {
	die("ERROR: Another staff member has claimed ownership of this ticket, therefore you can't view it.");
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
	$createdDate = formatDate("l, jS F Y h:i:s A", $row['dateUNIX']);
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
<br><br><br>
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
		$time = formatDate("l, jS F Y h:i:s A", $row_msg['dateUNIX']);
		echo <<<EOT
<br>
<div class="messageBox">
<b>Message {$x}, posted by {$row_msg['firstName']} {$row_msg['lastName']} at {$time}</b><br>
{$row_msg['message']}
</div>
EOT;
	}
	if ($owner == "nobody") {
		echo adminLink("Take Ownership", "&amp;id={$row['ID']}&amp;own=true");
	}
	
	if (isset($_GET['canned']) && $_GET['canned'] != "") {
		$database->safe_query("SELECT text
		                         FROM ticket_canned
								 WHERE ID = %i",
								array($_GET['canned']), __FILE__, __LINE__);
		$row_canned = $database->fetch_row();
		$text = stripslashes($row_canned['text']);
	} else {
		$text = "";
	}
	
	$canned_list = "";
	$results_canned = $database->query("SELECT ID, name FROM ticket_canned");
	for ($x=0; $x < $database->get_num_rows(); $x++) {
		$row_canned = $database->fetch_row();
		$row_canned['name'] = stripslashes($row_canned['name']);
		
		$canned_list .= "<option value='{$row_canned['ID']}'>{$row_canned['name']}</option>";
	}
		
		
	echo <<<EOT
<br><br><br>
 <b>Post a new message:</b><br>
 <input type='hidden' name='post' value='{$row['ID']}'>
 <textarea name='message' cols='70' rows='10'>{$text}</textarea><br>
 Change status to: 
  <select name='status'>
   <option value='0'>Open</option>
   <option value='1' selected>Pending</option>
   <option value='2'>Closed</option>
  </select><br>
  
  <script language='JavaScript'>
	function doCanned(){	
		var canned = document.adminForm.canned;
		var url = canned.options[canned.selectedIndex].value;

		window.location.href = "admin.php?do=page&cat={$_GET['cat']}&page={$_GET['page']}&id={$_GET['id']}&canned="+url; 
	}
  </script>
  Insert canned response into 'New Message' box:
    <select name='canned'>
	 {$canned_list}
	</select>
	<input type="button" onClick="javascript:doCanned()" value="go"><br>
 <input type='submit' value='Post Message'>


EOT;

}
 
?>