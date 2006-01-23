<?php
require("includes/templates.php");
error_reporting(E_ALL);

if (!isset($_REQUEST['step']))
	$_REQUEST['step'] = 1;

pageHeader("DanPHPSupport Installer - Step {$_REQUEST['step']}");

switch($_REQUEST['step']) {
	case "1":
		echo <<<EOT
Welcome to the DanPHPSupport installer! This installer will guide you through the rest of the 
DanPHPSupport installation. In order to continue, you'll need to know the following details:<br><br>
<ul>
 <li>Database Host (usually 'localhost')</li>
 <li>Database Name (create one now if you haven't already)</li>
 <li>Database Username</li>
 <li>Database Password</li>
</ul>
Once you have all this information, please click <a href='installer.php?step=2'>here</a> to continue.
EOT;
		break;
	case "2":
		echo <<<EOT
<br><b>Please enter the following information:</b><br><br>
<form action='installer.php?step=3' method='POST'>
 <table>
EOT;
		//writeInputField($name, $caption = "", $type = "text", $default = "", $extra = "") {
		writeInputField("db_host", "Database Host Name", "text", "localhost", "(for most servers, this is 'localhost')");
		writeInputField("db_name", "Database Name", "text", "DanPHPSupport");
		writeInputField("db_user", "Database Username");
		writeInputField("db_pass", "Database Password", "password");
		writeInputField("support-email", "Support Email Address", "text", "support-noreply@example.com", "- This is where support notifications will appear to come from. Don't use a real email address here. Make it something like support-noreply@your_domain (obviously replacing your_domain with your website's domain)");
		
		echo "<tr><td colspan='2'><b>Administrator User Details:</b></td></tr>";
		
		writeInputField("user", "Support Username", "text", "admin", "-- Username you'll use to log in to the administration interface.");
		writeInputField("pass", "Support Password", "password");
		//writeInputField("confirm", "Confirm above Password", "password");
		writeInputField("firstname", "First Name");
		writeInputField("lastname", "Last Name");
		writeInputField("email", "Email Address");
		
		writeInputField("submit", "", "submit", "Install!");
		echo "</form>";
		break;
	case "3":
		require("includes/database.php");
		$database = new class_database();
		
		echo "Trying to connect to database... ";
		if ($database->connect($_POST['db_host'], $_POST['db_user'], $_POST['db_pass'], $_POST['db_name']) === FALSE) {
			die("<br><b><font color='red'>Error connecting to database! Please click 'Back' on your browser and check the settings!</font></b>");
		}
		echo "SUCCESS!<br><br><b>Creating Database Structure...</b><br>";
		
		echo "Creating kb_articles table...";
		$database->query("DROP TABLE IF EXISTS `kb_articles`", __FILE__, __LINE__);
		$database->query("CREATE TABLE `kb_articles` (
  							`ID` mediumint(8) unsigned NOT NULL auto_increment,
  							`title` varchar(255) NOT NULL default '',
  							`body` mediumtext NOT NULL,
  							`views` int(10) unsigned NOT NULL default '0',
  							`categoryID` smallint(5) unsigned NOT NULL default '0',
 							PRIMARY KEY  (`ID`),
  							FULLTEXT KEY `searchIndex` (`title`,`body`)
							) TYPE=MyISAM", __FILE__, __LINE__);
		$database->query("INSERT INTO `kb_articles` VALUES 
		                    (10, 'Test Article', 'Test Article -- Remove me if you want!', 0, 1);",
							__FILE__, __LINE__);
							
		echo "<br>Creating kb_categories table...";
		$database->query("DROP TABLE IF EXISTS `kb_categories`", __FILE__, __LINE__);
		$database->query("CREATE TABLE `kb_categories` (
							  `ID` smallint(5) unsigned NOT NULL auto_increment,
							  `name` varchar(50) NOT NULL default '',
							  `parentID` smallint(5) unsigned NOT NULL default '0',
							  `count` int(10) unsigned NOT NULL default '0',
							  PRIMARY KEY  (`ID`)
							) TYPE=MyISAM", __FILE__, __LINE__);
		$database->query("INSERT INTO `kb_categories` VALUES (1, 'Test KB Category', 0, 1);");
		
		echo "<br>Creatng settings table...";
		$database->query("DROP TABLE IF EXISTS `settings`", __FILE__, __LINE__);
		$database->query("CREATE TABLE `settings` (
							  `field` tinytext NOT NULL,
							  `value` text NOT NULL,
							  PRIMARY KEY  (`field`(30))
							) TYPE=MyISAM;", __FILE__, __LINE__);
		echo "<br>&nbsp;&nbsp;&nbsp;Adding settings to table...";
		$database->query("INSERT INTO `settings` VALUES ('adminEmail', '{$_POST['email']}');", __FILE__, __LINE__);
		$database->query("INSERT INTO `settings` VALUES ('emailNewTicket', '1');", __FILE__, __LINE__);
		$database->query("INSERT INTO `settings` VALUES ('fromEmail', '{$_POST['support-email']}');", __FILE__, __LINE__);
		$database->query("INSERT INTO `settings` VALUES ('timeZone', '10')", __FILE__, __LINE__);
		
		echo "<br>Creating ticket_categories table...";
		$database->query("DROP TABLE IF EXISTS `ticket_categories`", __FILE__, __LINE__);
		$database->query("CREATE TABLE `ticket_categories` (
							  `id` smallint(5) unsigned NOT NULL auto_increment,
							  `name` varchar(50) NOT NULL default '',
							  PRIMARY KEY  (`id`)
							) TYPE=MyISAM", __FILE__, __LINE__);
		$database->query("INSERT INTO `ticket_categories` VALUES (1, 'Test Ticket Category');", __FILE__, __LINE__);
		
		echo "<br>Creating table 'ticket_messages'...";
		$database->query("DROP TABLE IF EXISTS `ticket_messages`", __FILE__, __LINE__);
		$database->query("CREATE TABLE `ticket_messages` (
						  `ID` int(10) unsigned NOT NULL auto_increment,
						  `date` datetime NOT NULL default '0000-00-00 00:00:00',
						  `message` mediumtext NOT NULL,
						  `ticketID` int(10) unsigned NOT NULL default '0',
						  `userID` int(10) unsigned NOT NULL default '0',
						  PRIMARY KEY  (`ID`),
						  FULLTEXT KEY `message` (`message`)
						) TYPE=MyISAM", __FILE__, __LINE__);
						
		echo "<br>Creating table 'ticket_severities'...";
		$database->query("DROP TABLE IF EXISTS `ticket_severities`", __FILE__, __LINE__);
		$database->query("CREATE TABLE `ticket_severities` (
						  `ID` tinyint(3) unsigned NOT NULL auto_increment,
						  `name` varchar(25) NOT NULL default '',
						  `colour` varchar(10) NOT NULL default '',
						  PRIMARY KEY  (`ID`)
						) TYPE=MyISAM AUTO_INCREMENT=7", __FILE__, __LINE__);
		$database->query("INSERT INTO `ticket_severities` VALUES (2, 'Low', 'green');", __FILE__, __LINE__);
		$database->query("INSERT INTO `ticket_severities` VALUES (3, 'Medium', 'yellow');", __FILE__, __LINE__);
		$database->query("INSERT INTO `ticket_severities` VALUES (4, 'High', 'red');", __FILE__, __LINE__);
		$database->query("INSERT INTO `ticket_severities` VALUES (5, 'Critical', 'orange');", __FILE__, __LINE__);
		$database->query("INSERT INTO `ticket_severities` VALUES (6, 'Urgent', 'blue');", __FILE__, __LINE__);
		
		echo "<br>Creating table 'tickets'...";
		$database->query("DROP TABLE IF EXISTS `tickets`", __FILE__, __LINE__);
		$database->query("CREATE TABLE `tickets` (
							  `ID` int(10) unsigned NOT NULL auto_increment,
							  `userID` int(10) unsigned NOT NULL default '0',
							  `date` datetime NOT NULL default '0000-00-00 00:00:00',
							  `category` smallint(5) unsigned NOT NULL default '0',
							  `subject` varchar(100) NOT NULL default '',
							  `severity` tinyint(4) NOT NULL default '0',
							  `status` tinyint(1) NOT NULL default '0',
							  `staffID` int(10) unsigned NOT NULL default '0',
							  `lastPost` int(10) unsigned NOT NULL default '0',
							  `replyCount` tinyint(4) NOT NULL default '0',
							  PRIMARY KEY  (`ID`)
							) TYPE=MyISAM", __FILE__, __LINE__);
							
		
		echo "<br>Creating 'users' table...";
		$database->query("DROP TABLE IF EXISTS `users`", __FILE__, __LINE__);
		$database->query("CREATE TABLE `users` (
						  `ID` int(10) unsigned NOT NULL auto_increment,
						  `firstName` varchar(30) NOT NULL default '',
						  `lastName` varchar(30) NOT NULL default '',
						  `email` varchar(100) NOT NULL default '',
						  `username` varchar(50) NOT NULL default '',
						  `password` varchar(32) NOT NULL default '',
						  `admin` tinyint(1) unsigned NOT NULL default '0',
						  `lastLogin` datetime NOT NULL default '0000-00-00 00:00:00',
						  `lastLoginOld` datetime NOT NULL default '0000-00-00 00:00:00',
						  PRIMARY KEY  (`ID`)
						) TYPE=MyISAM", __FILE__, __LINE__);
		echo "<br>&nbsp;&nbsp;&nbsp;Creating '{$_POST['user']}' user...";
		
		$database->query("INSERT INTO `users` VALUES (
						   1, '{$_POST['firstname']}', '{$_POST['lastname']}', '{$_POST['email']}',
						   '{$_POST['user']}', '".md5($_POST['pass'])."', 1, NOW(), '');",
						    __FILE__, __LINE__);
							
		echo "<br>Creating ticket_canned table...";
		$database->query("DROP TABLE IF EXISTS `ticket_canned`", __FILE__, __LINE__);
		$database->query("CREATE TABLE `ticket_canned` (
						  `ID` TINYINT UNSIGNED NOT NULL AUTO_INCREMENT ,
						  `name` VARCHAR(80) NOT NULL ,
						  `text` TEXT NOT NULL ,
						  PRIMARY KEY (`ID`))", __FILE__, __LINE__);
						  
		//$database->query("", __FILE__, __LINE__);
		//$database->query("", __FILE__, __LINE__);
		//$database->query("", __FILE__, __LINE__);
		//$database->query("", __FILE__, __LINE__);
		//$database->query("", __FILE__, __LINE__);
		
		echo "<br>DONE!<br><br>Writing database settings to 'settings-new.php'...";
		
		$settings_file = <<<EOT
<?php
//Automatically generated by DanPHPSupport installer

\$INFO['mysql_host'] = "{$_POST['db_host']}";		//Your database host (usually localhost)
\$INFO['mysql_user'] = "{$_POST['db_user']}";		//Your MySQL username
\$INFO['mysql_pass'] = "{$_POST['db_pass']}";		//Your MySQL password
\$INFO['mysql_db'] = "{$_POST['db_name']}";	// The name of the database
?>
EOT;

		$file_handle = fopen("includes/settings-new.php","w");
		fwrite($file_handle, $settings_file) ;   
		fclose($file_handle);  
		
		echo "SUCCESS!<br><br>DanPHPSupport install completed!<br><h3><b><font color='red'>Please rename the 'settings-new.php' file to 'settings.php'(see the 'includes' directory), otherwise your DanPHPSupport installation <b>will not work</b>!</font>";
	}

function writeInputField($name, $caption = "", $type = "text", $default = "", $extra = "") {
	echo <<<EOT
<tr>
 <td width='130'>{$caption}:</td>
 <td><input type='{$type}' name='{$name}' value='{$default}' size='30'> {$extra}</td>
</tr>
EOT;
}
?>
 </body>
</html>