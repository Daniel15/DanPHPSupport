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

//PAGE_GENERAL.PHP: General Configuration options

if (!defined('IN_ADMIN') || eregi("page_general.php",$_SERVER['PHP_SELF'])) {
    die("You can't run this directly!");
}

//Time zone names are actually indexed using the GMT value times 2. This is because there are
//decimal values
$timeZoneNames['-24'] = "Enitwetok, Kwajalien";
$timeZoneNames['-22'] = "Midway Island, Samoa";
$timeZoneNames['-20'] = "Hawaii";
$timeZoneNames['-18'] = "Alaska";
$timeZoneNames['-16'] = "Pacific Time (US &amp; Canada)";
$timeZoneNames['-14'] = "Mountain Time (US &amp; Canada)";
$timeZoneNames['-12'] = "Central Time (US &amp; Canada), Mexico City";
$timeZoneNames['-10'] = "Eastern Time (US &amp; Canada), Bogota, Lima";
$timeZoneNames['-8'] = "Atlantic Time (Canada), Caracas, La Paz";
$timeZoneNames['-7'] = "Newfoundland";
$timeZoneNames['-6'] = "Brazil, Buenos Aires, Falkland Is.";
$timeZoneNames['-4'] = "Mid-Atlantic, Ascention Is., St Helena";
$timeZoneNames['-2'] = "Azores, Cape Verde Islands";
$timeZoneNames['0'] = "Casablanca, Dublin, London, Lisbon, Monrovia";
$timeZoneNames['2'] = "Brussels, Copenhagen, Madrid, Paris";
$timeZoneNames['4'] = "Kaliningrad, South Africa";
$timeZoneNames['6'] = "Baghdad, Riyadh, Moscow, Nairobi";
$timeZoneNames['7'] = "Tehran";
$timeZoneNames['8'] = "Abu Dhabi, Baku, Muscat, Tbilisi";
$timeZoneNames['9'] = "Kabul";
$timeZoneNames['10'] = "Ekaterinburg, Karachi, Tashkent";
$timeZoneNames['11'] = "Bombay, Calcutta, Madras, New Delhi";
$timeZoneNames['12'] = "Almaty, Colomba, Dhakra";
$timeZoneNames['14'] = "Bangkok, Hanoi, Jakarta";
$timeZoneNames['16'] = "Hong Kong, Perth, Singapore, Taipei";
$timeZoneNames['18'] = "Osaka, Sapporo, Seoul, Tokyo, Yakutsk";
$timeZoneNames['19'] = "Adelaide, Darwin";
$timeZoneNames['20'] = "Melbourne, Papua New Guinea, Sydney";
$timeZoneNames['22'] = "Magadan, New Caledonia, Solomon Is.";
$timeZoneNames['24'] = "Auckland, Fiji, Marshall Island";

if (isset($_GET['submit'])) {
	if (!isset($_POST['fromEmail'])) {
		writeError("ERROR: No 'From Email' address entered!");
	} elseif (!isset($_POST['timeZone'])) {
		writeError("ERROR: No time zone entered (how'd you manage to do that??)");
	} else {
		$SETTINGS['fromEmail'] = $_POST['fromEmail'];
		$SETTINGS['timeZone'] = $_POST['timeZone'];
		saveSettings();
		writeError("Saved Settings!");
	}
}

$timeZone_choose = "<select name='timeZone'>";
for ($x = -12; $x < 14; $x+=0.5) {
	$timeZone_choose .= "<option value='{$x}'";
	if ($x == $SETTINGS['timeZone']) $timeZone_choose .= " selected"; 
	$timeZone_choose .= ">(GMT ".($x >= 0 ? "+".$x : $x).") ";
	
	if (isset($timeZoneNames[$x*2])) $timeZone_choose .= $timeZoneNames[$x*2];
	
	$timeZone_choose .= "</option>\r\n";
}
$timeZone_choose .= "</select>";

$current_timeZone = ($SETTINGS['timeZone'] >= 0 ? "+".$SETTINGS['timeZone'] : $SETTINGS['timeZone']);
$current_time = formatDate("jS F Y h:i:s A", time());

echo <<<EOT
 From E-Mail: <input type='text' name='fromEmail' value='{$SETTINGS['fromEmail']}' size='50'><br>
 <font size='-2'>The 'From E-Mail' is where email notifications will appear to come from.</font><br><br>
 Time Zone: {$timeZone_choose}<br>
 <font size='-2'>The current time (at GMT{$current_timeZone}) is {$current_time}</font><br><br>
 <input type='submit' name='Save Changes'>
EOT;
?>