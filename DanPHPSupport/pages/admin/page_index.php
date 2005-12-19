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
// DATE: 19th December 2005

//PAGE_INDEX.PHP: The administration index page

if (!defined('IN_ADMIN') || eregi("page_index.php",$_SERVER['PHP_SELF'])) {
    die("You can't run this directly!");
}
$last_time = formatDate("jS F Y h:i:s A", $_SESSION['admin_lastlogin']);

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

$version = DANPHPSUPPORT_VER;
$build = DANPHPSUPPORT_BUILD;
$rel_date = DANPHPSUPPORT_DATE;
echo <<<EOT
Welcome to your administration panel, {$_SESSION['admin_name']}! From here, you can administer your online support desk.<br><br>
<b>You last visited at: </b> {$last_time}<br><br>
<b>Number of new tickets</b> since your last visit: {$newcount}<br>
<b>Number of new replies</b> since your last visit: {$replycount}<br><br>
<b>Total open (new) tickets:</b> {$totalopen}<br>
<b>Total pending tickets:</b> {$totalpending}<br>
<b>Total closed tickets:</b> {$totalclosed}<br><br>
<b>Installed version: </b><span id='yourVersion'>{$version}</span> (Build <span id='yourBuild'>{$build}</span>) released on {$rel_date}<br>
<span id='currVersion'>Unable to connect to DanSoft Australia website to check version!</span>

<script language="JavaScript" src="http://danphpsupport.dansoftaustralia.net/updates/dps-version.js?build={$build}"></script>
EOT;
?>