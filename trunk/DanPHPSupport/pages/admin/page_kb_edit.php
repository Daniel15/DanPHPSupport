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

//PAGE_KB_EDIT.PHP: Admin page-KB article editor

if (!defined('IN_ADMIN') || eregi("page_kb_edit.php",$_SERVER['PHP_SELF'])) {
    die("You can't run this directly!");
}
?>