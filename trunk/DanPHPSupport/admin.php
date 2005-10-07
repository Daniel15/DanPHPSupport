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
// DATE: 26th September 2005

//ADMIN.PHP: The administration interface file

include "includes/Library.php";
include "includes/settings.php";

session_start();

if(!isset($_GET['do']) || $_GET['do'] == "" || $_GET['do'] == "home") {
	adminLoginForm();
	die();
	
} elseif ($_GET['do'] == "login") {
	if (!isset($_POST['username'])) {
		adminLoginForm("ERROR: You must enter a username!");
		die();
	}
	$result = $database->safe_query("SELECT username, password, admin, ID, firstName, lastName, UNIX_TIMESTAMP(lastLogin) as lastLogin_UNIX
								       FROM users
                                       WHERE username = '%s'",
								array($_POST['username']), __FILE__, __LINE__);
	
	$row = $database->fetch_row();
	
	if ($database->get_num_rows() == 0) {
		adminLoginForm("ERROR: That username doesn't exist!", $_POST['username']);
	} elseif ($row['password'] !== md5($_POST['password'])) {
		adminLoginForm("ERROR: The password you typed was incorrect!", $_POST['username']);
	} elseif ($row['admin'] != 1) {
		adminLoginForm("ERROR: You aren't an admin ({$row['admin']})!", $_POST['username']);
	} else {
		$result = $database->query("UPDATE users
									   SET lastLoginOld = `lastLogin`,
									       lastLogin = NOW( ) 
									   WHERE ID = {$row['ID']}",
								   __FILE__, __LINE__);
		$_SESSION['admin_in'] = true;
		$_SESSION['admin_id'] = $row['ID'];
		$_SESSION['admin_user'] = $row['username'];
		$_SESSION['admin_name'] = $row['firstName']." ".$row['lastName'];
		$_SESSION['admin_lastlogin'] = $row['lastLogin_UNIX'];
		
		adminPanel();
	}
} elseif ($_GET['do'] == "menu") {
	include "pages/admin/pages.php";
	$menuText = "";
	
	for($x=0; $x<count($CATS); $x++) {
		$menuText .= "<h2>{$CATS[$x]}</h2>";
		for ($y=0; $y<count($PAGES[$x]); $y++) {
			$menuText .= "<a target='body' href='admin.php?do=page&amp;cat={$x}&amp;page={$y}'>{$PAGES[$x][$y][0]}</a><br>";
		}
	}
	
	adminMenu($menuText);
} elseif ($_GET['do'] == "page") {

	if (!isset($_SESSION['admin_in']) || $_SESSION['admin_in'] !== true) {
		header("Location: http://{$_SERVER['HTTP_HOST']}{$_SERVER['PHP_SELF']}");
	}
	
	include "pages/admin/pages.php";
	define("IN_ADMIN", true);
	
	adminPageHeader($PAGES[$_GET['cat']][$_GET['page']][0], $_GET['cat'], $_GET['page']);
	include "pages/admin/page_".$PAGES[$_GET['cat']][$_GET['page']][1].".php";
	adminPageFooter();
} else {
	die("Invalid 'do' action: {$_GET['do']}");
}

function adminLink($title, $extra = "") {
	return "<a href='admin.php?do=page&amp;cat={$_GET['cat']}&amp;page={$_GET['page']}{$extra}'>{$title}</a>";
}

function showKBCategories($printout = false, $selected_id = 0, $tabLevel = 0, $parentID = 0) {
	global $database;
	$cat_list = "";

	if ($tabLevel != 0) {
		$spacing = str_repeat("&nbsp;", ($tabLevel * 3) - 2)."|- ";
	} else {
		$spacing = "";
	}
	
	$results = $database->query("SELECT ID, name
								   FROM kb_categories
								   WHERE parentID = {$parentID}
								   ORDER BY ID ASC", __FILE__, __LINE__);
	
	for ($x=0; $x < $database->get_num_rows($results); $x++) {
		$row = $database->fetch_row($results);
		$edit_link = adminLink("edit", "&amp;edit={$row['ID']}");
		
		if ($printout == true) echo <<<EOT
<tr>
 <td>{$row['ID']}</td>
 <td>{$spacing}{$row['name']}</td>
 <td>{$edit_link}</td>
</tr>
EOT;
		if ($selected_id == 0 || $selected_id != $row['ID']) {
			$cat_list .= "<option value='{$row['ID']}'>{$spacing}{$row['name']}</option>";
		} elseif ($selected_id == $row['ID']) {
			$cat_list .= "<option value='{$row['ID']}' selected>{$spacing}{$row['name']}</option>";
		}
	
	$cat_list .= showKBCategories($printout, $selected_id, $tabLevel + 1, $row['ID']);
	}
	
	return $cat_list;
}
?>
