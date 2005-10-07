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

//INDEX.PHP: The main file

include "includes/Library.php";

ob_start("ob_gzhandler");

if (!isset($_GET['page'])) {
	$_GET['page'] = "index";
}

define("IN_SUPPORT", true);

include "pages/main/page_{$_GET['page']}.php";
mainPageFooter();

?>
