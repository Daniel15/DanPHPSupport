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

//PAGE_INDEX.PHP: The administration index page

if (!defined('IN_ADMIN') || eregi("page_index.php",$_SERVER['PHP_SELF'])) {
    die("You can't run this directly!");
}
$last_time = date("jS F Y h:i:s A", $_SESSION['admin_lastlogin']+$INFO['time_offset']);

//various statistics queries:

//new tickets since last visit
$database->query("SELECT date
				    FROM tickets
					WHERE date > FROM_UNIXTIME({$_SESSION['admin_lastlogin']})",
				__FILE__, __LINE__);
$newcount = $database->get_num_rows();

//new replies since last visit
$database->query("SELECT date
					FROM ticket_messages
					WHERE date > FROM_UNIXTIME({$_SESSION['admin_lastlogin']})",
				__FILE__, __LINE__);
$replycount = $database->get_num_rows();

//total open tickets
$database->query("SELECT status
					FROM tickets
					WHERE status=0",
				__FILE__, __LINE__);
$totalopen = $database->get_num_rows();

//total pending tickets
$database->query("SELECT status
					FROM tickets
					WHERE status=1",
				__FILE__, __LINE__);
$totalpending = $database->get_num_rows();

//total closed tickets
$database->query("SELECT status
					FROM tickets
					WHERE status=2",
				__FILE__, __LINE__);
$totalclosed = $database->get_num_rows();


echo <<<EOT
Welcome to your administration panel, {$_SESSION['admin_name']}! From here, you can administer your online support desk.<br><br>
<b>You last visited at: </b> {$last_time}<br><br>
<b>Number of new tickets</b> since your last visit: {$newcount}<br>
<b>Number of new replies</b> since your last visit: {$replycount}<br><br>
<b>Total open (new) tickets:</b> {$totalopen}<br>
<b>Total pending tickets:</b> {$totalpending}<br>
<b>Total closed tickets:</b> {$totalclosed}<br>
EOT;
?>