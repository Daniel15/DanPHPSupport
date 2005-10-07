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

//PAGES.PHP: The pages in the admin panel

/* Basically, format is:
   $CATS = array(
   				 0 => "",
				 1 => "category 1",
				 2 => "category 2",
				 ...
				 ...
				 ...
				 );
				  
   $PAGES = array(
   				  0 => array(
				  			 0 => array("Control Panel Index", "index"),
				             ),
				  1 => array(
				  			 0 => array("Item 1 in category 1", "filename"),
				  			 1 => array("Item 2 in category 1", "filename"),
							 ),
				  2 => array(
				  			 0=> array("Item in category 2", "filename"),
							 ),
				  ...
				  ...
				  ...
				  ...
				  );
				  
	filename is the file in admin/ folder, prefixed by 'page_'
	eg. if file is called 'page_mypage.php', enter 'mypage' here
   */
$CATS = array(
			  0 => "",
			  1 => "General Settings",
			  2 => "Support Tickets",
			  3 => "Knowledge Base",
			  4 => "Statistics",
			  );

$PAGES = array(
			   0 => array(
			   			  0 => array("Control panel index", "index"),
			   			  ),
			   1 => array(
			   			  0 => array("General Configuration", "not_imp"),
						  1 => array("Users", "not_imp"),
						  ),
			   2 => array(
			   			  0 => array("Open Tickets", "ticket_open"),
						  1 => array("Pending Tickets", "ticket_pending"),
						  2 => array("Closed Tickets", "ticket_closed"),
						  3 => array("Search Tickets", "not_imp"),
						  4 => array("", "ticket_view"), //used for blank line in menu, it also has the use of viewing a ticket
						  5 => array("Categories", "ticket_cats"),
						  6 => array("Severities", "ticket_sevs"),
						  ),
			   3 => array(
			   			  0 => array("Add Article", "kb_add"),
			   			  1 => array("Edit/Delete Article", "not_imp"),
						  2 => array("Search Knowledgebase", "kb_search"),
						  3 => array("Categories", "kb_cats"),
						  ),
			   4 => array(
			   			  ),
			   );
?>