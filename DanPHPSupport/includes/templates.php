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

//TEMPLATES.PHP: Templates for various stuff

//////////////////////////////////////////////////////////////////////////////////////////////////
// Main Templates
//////////////////////////////////////////////////////////////////////////////////////////////////
function adminPanel() {
?>
<html>
 <head>
  <title>DanPHPSupport - Administration</title>
  <script language='javascript'>
  //if this frame page is loaded within a frameset, 'break out' of the frameset.
  //eg. If admin login expires and user prompted for re-login
  if (top.location != location) {
    top.location.href = document.location.href ;
  }
  </script>
 </head>

 <frameset cols="185,*" framespacing="2">
  <frame name="menu" scrolling='auto' src="admin.php?do=menu">
  <frame name="body" scrolling='auto' src="admin.php?do=page&amp;cat=0&amp;page=0">
 </frameset>
 
 <noframes>Get a browser with frames support you loser :-)
 </noframes>
</html>
<?php 
}

function adminLoginForm($error = "", $username = "") {
	pageHeader("DanPHPSupport Administration Panel");
	echo <<<EOT
<p>
 <font color="#FF0000" size="+1">{$error}</font><br>
 Please enter your username and password to login to the administration panel:
</p>
<form action="admin.php?do=login" method="POST">
 Username: &nbsp;<input type="text" name="username" value="{$username}" size='50'><br>
 Password: &nbsp;<input type="password" name="password" size='50'><br>
 <input type="submit" value="Log in">
</form>
EOT;
	mainPageFooter();
}

function supportLoginForm($message = "", $error = "", $username = "", $return = "") {
	echo <<<EOT
<p>
 <font color="#FF0000" size="+1">{$error}</font><br>
 {$message}
</p>
<form action="index.php?page=login" method="POST">
 <input type='hidden' name='login2' value='true'>
 <input type='hidden' name='return' value='{$return}'>
 Username: &nbsp;<input type="text" name="username" value="{$username}" size='50'><br>
 Password: &nbsp;<input type="password" name="password" size='50'><br>
 <input type="submit" value="Log in">
</form>
EOT;
}

function adminMenu($menuText = "") {
?>
<html>
 <head>
  <style>@import url(style.css);</style>
 </head>
 <body>
 <?php echo $menuText ?>
 </body>
</html>
<?php 
}

function adminPageHeader($header = "", $cat = 0, $page = 0) {
	echo pageHeader($header);
	echo "<form action='admin.php?do=page&amp;cat={$cat}&amp;page={$page}&amp;submit=true' method='POST' name='adminForm'>";

}

function adminPageFooter() {
	echo mainPageFooter();
}

function pageHeader($header = "") {
	echo <<<EOT
<html>
 <head>
  <style>@import url(style.css);</style>
  <title>{$header}</title>
 </head>
 <body>
  <center><h1>{$header}</h1></center>	
EOT;
}

function mainPageFooter() {
	echo footer();
	echo <<<EOT
 </body>
</html>
EOT;
}

function KBSearchForm($query = "") {
	echo <<<EOT
<form action='index.php' method='GET'>
 <input type='hidden' name='page' value='kb_search'>
 Keywords: <input type='text' name='q' value='{$query}' size='50'>
 <input type='submit' value='search'>
</form>	
EOT;
}

function registerForm($return, $error = "", $username = "", $firstname = "", $lastname = "", $email = "") {
	pageHeader("Register");
	echo <<<EOT
<p>
 <font color="#FF0000" size="+1">{$error}</font><br>
 Please fill out the registration form below. Your account will be available immediately after registering.
</p>
<form action='index.php?page=register&return={$return}' method='POST'>
 <input type='hidden' name='register2' value='true'>
 Username: <input type='text' name='username' value='{$username}' size='50'><br>
 First Name: <input type='text' name='firstname' value='{$firstname}' size='50'><br>
 Last Name: <input type='text' name='lastname' value='{$lastname}' size='50'><br>
 E-Mail Address: <input type='text' name='email' value='{$email}' size='50'><br>
 Password: <input type='password' name='password' size='50'><br>
 Confirm Password: <input type='password' name='confirm' size='50'><br>
 <input type='submit' value='Sign Up'>
EOT;
}

//////////////////////////////////////////////////////////////////////////////////////////////////
// E-Mail Templates
//////////////////////////////////////////////////////////////////////////////////////////////////
function newTicketMail($id, $subject, $severity, $message) {
	return <<<EOT
Do not reply to this email, it was automatically generated by DanPHPSupport.
This message is to inform you that a new support ticket has been created. Details are below:

Subject: {$subject}
Severity: {$severity}
Message:
 {$message}

 --Support Mailer
EOT;
}

function updateMail($id, $subject, $message, $status, $url) {
	return <<<EOT
Do not reply to this email, it was automatically generated by DanPHPSupport.
This message is to inform you that there has been an update to your support ticket. Details are below:

Ticket subject: {$subject}
Current ticket status: {$status}
Message:
{$message}


To view your ticket, please login at {$url} (choose the 'Ticket Status' function) and click on it

--Support Mailer
EOT;
}

function updateAdminMail($id, $subject, $message, $status) {
	return <<<EOT
Do not reply to this email, it was automatically generated by DanPHPSupport.
This message is to inform you that there has been an update to a support ticket which you have taken ownership of. Details are below:

Ticket subject: {$subject}
Current ticket status: {$status}
Message:
{$message}

--Support Mailer
EOT;
}
