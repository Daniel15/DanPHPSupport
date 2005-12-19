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

//PAGE_USERS.PHP: Admin page - User Administration

if (!defined('IN_ADMIN') || eregi("page_users.php",$_SERVER['PHP_SELF'])) {
    die("You can't run this directly!");
}

if (isset($_GET['edit']) && $_GET['edit'] != "") {

	$database->safe_query("SELECT *
                             FROM users
							 WHERE ID = %i",
							array($_GET['edit']), __FILE__, __LINE__);
	$row = $database->fetch_row();
	echo <<<EOT
<h2><center>Editing user ID {$_GET['edit']}</center></h2>
 <input type='hidden' name='edit2' value='{$_GET['edit']}'>
 Username: <input type='text' name='username' value='{$row['username']}' size='50'><br>
 First Name: <input type='text' name='firstname' value='{$row['firstName']}' size='50'><br>
 Last Name: <input type='text' name='lastname' value='{$row['lastName']}' size='50'><br>
 E-Mail Address: <input type='text' name='email' value='{$row['email']}' size='50'><br>
 <br>
 Change Password: <input type='password' name='password' size='50'><br>
 Confirm Change: <input type='password' name='confirm' size='50'><br>
 <input type='submit' value='Save Changes'>
EOT;
} elseif (isset($_POST['edit2']) && $_POST['edit2'] != "") {
	if ($_POST['confirm'] !== $_POST['password']) {
		writeError("ERROR: Passwords do not match!");
		die();
	}
	
	$database->safe_query("UPDATE users
							 SET userName = '%s',
							     firstName = '%s',
							     lastName = '%s',
								 email = '%s'
							   [, password = '%S']
							   WHERE id = %i
							   LIMIT 1",
							  array($_POST['username'], $_POST['firstname'], $_POST['lastname'],
							        $_POST['email'], md5($_POST['password']), $_POST['edit2']),
							   __FILE__, __LINE__);
	
	echo "Saved Changes!<br>".adminLink("Back to User Administration");
} else {

	$users = "";
	$database->query("SELECT firstName, lastName, username, ID, 
							 UNIX_TIMESTAMP(lastLogin) AS lastLogin_UNIX
						FROM users", __FILE__, __LINE__);
						
	for ($x=0; $x < $database->get_num_rows(); $x++) {
		$row = $database->fetch_row();
		$users .= "
	{$row['username']} ({$row['firstName']} {$row['lastName']}) - 
	  Last Logged in on ".formatDate("jS F Y", $row['lastLogin_UNIX'])." - 
	  ".adminLink("edit", "&amp;edit={$row['ID']}")."<!-- or 
	  ".adminLink("delete", "&amp;delete={$row['ID']}")."-->
	  <br>
	";
	}
	
	echo <<<EOT
	<h2>Current Users:</h2>
	{$users}
	
EOT;
}
?>