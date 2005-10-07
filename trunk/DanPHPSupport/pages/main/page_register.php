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

//PAGE_TICKET_MAIN.PHP: Main ticket page.

if (!defined('IN_SUPPORT') || eregi("page_ticket_submit.php",$_SERVER['PHP_SELF'])) {
    die("You can't run this directly!");
}

if (!isset($_REQUEST['return'])) $_REQUEST['return'] = "ticket_main";

if (isset($_POST['register2'])) {
	if (!isset($_POST['username']) || $_POST['username'] == "") {
		callRegisterForm("ERROR: You must enter a username!");
	} elseif (!isset($_POST['firstname']) || $_POST['firstname'] == "") {
		callRegisterForm("ERROR: You must enter a firstname!");
	} elseif (!isset($_POST['lastname']) || $_POST['lastname'] == "") {
		callRegisterForm("ERROR: You must enter a lastname!");
	} elseif (!isset($_POST['email']) || $_POST['email'] == "") {
		callRegisterForm("ERROR: You must enter an email address!");
	} elseif (!isset($_POST['password']) || $_POST['password'] == "") {
		callRegisterForm("ERROR: Your password cannot be blank!");
	} elseif ($_POST['password'] !== $_POST['confirm']) {
		callRegisterForm("ERROR: The two password boxes didn't match! Please re-type your password.");
	} else {
		$database->safe_query("SELECT username
								  FROM users
								  WHERE username='%s'",
								array($_POST['username']), __FILE__, __LINE__);
		if($database->get_num_rows() != 0) {
			callRegisterForm("ERROR: The username '{$_POST['username']}' is taken. Please choose another.");
		} else {
			$database->safe_query("INSERT INTO users
									  (firstName, lastName, email, username, password)
									  VALUES ('%s', '%s', '%s', '%s', '%s')",
								    array($_POST['firstname'], $_POST['lastname'], $_POST['email'],
									      $_POST['username'], md5($_POST['password'])),
								     __FILE__, __LINE__);
				 
			echo "Your account was created successfully. You can <a href='index.php?page=login&amp;return={$_REQUEST['return']}'>login here</a>";
			
		}
	}
} else {
	registerForm($_REQUEST['return']);
}

function callRegisterForm($error) {
	$_POST['username'] = isset($_POST['username']) ? $_POST['username'] : "";
	$_POST['firstname'] = isset($_POST['firstname']) ? $_POST['firstname'] : "";
	$_POST['lastname'] = isset($_POST['lastname']) ? $_POST['lastname'] : "";
	$_POST['email'] = isset($_POST['email']) ? $_POST['email'] : "";
	registerForm($_REQUEST['return'], $error, $_POST['username'], $_POST['firstname'], $_POST['lastname'], $_POST['email']);
}
?>
