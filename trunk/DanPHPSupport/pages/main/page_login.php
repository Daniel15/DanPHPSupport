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

//PAGE_LOGIN.PHP: Login

if (!defined('IN_SUPPORT') || eregi("page_ticket_submit.php",$_SERVER['PHP_SELF'])) {
    die("You can't run this directly!");
}

session_start();
if (!isset($_REQUEST['return'])) $_REQUEST['return'] = "ticket_main";

if (isset($_SESSION['support_in']) && $_SESSION['support_in'] == true) {
	header("Location: http://{$_SERVER['HTTP_HOST']}{$_SERVER['PHP_SELF']}?page={$_REQUEST['return']}");
}

if (isset($_POST['login2'])) {
	if (!isset($_POST['username'])) {
		supportLoginForm("", "ERROR: You must enter a username!", "", $_REQUEST['return']);
		die();
	}
	
	$result = $database->safe_query("SELECT username, password, admin, ID, firstName, lastName, UNIX_TIMESTAMP(lastLogin) as lastLogin_UNIX
								       FROM users
                                       WHERE username = '%s'",
								array($_POST['username']), __FILE__, __LINE__);
	
	$row = $database->fetch_row();
	
	if ($database->get_num_rows() == 0) {
		supportLoginForm("", "ERROR: That username doesn't exist! If you are trying to create a new account, please <a href='index.php?page=register&amp;return={$_REQUEST['return']}'>go here</a>", $_POST['username'], $_REQUEST['return']);
	} elseif ($row['password'] !== md5($_POST['password'])) {
		supportLoginForm("", "ERROR: The password you typed was incorrect!", $_POST['username'], $_REQUEST['return']);
	} else {
		$result = $database->query("UPDATE users
									   SET lastLoginOld = `lastLogin`,
									       lastLogin = NOW( ) 
									   WHERE ID = {$row['ID']}",
								   __FILE__, __LINE__);
		$_SESSION['support_in'] = true;
		$_SESSION['support_id'] = $row['ID'];
		$_SESSION['support_user'] = $row['username'];
		$_SESSION['support_name'] = $row['firstName']." ".$row['lastName'];
		$_SESSION['support_lastlogin'] = $row['lastLogin_UNIX'];
		
		header("Location: http://{$_SERVER['HTTP_HOST']}{$_SERVER['PHP_SELF']}?page={$_REQUEST['return']}");
	}
} else {
	pageHeader("Support Login");
	supportLoginForm("Please enter your username and password to log in. If you haven't registered yet, please <a href='index.php?page=register&amp;return={$_REQUEST['return']}'>do so here</a>", "", "", $_REQUEST['return']);
}
?>