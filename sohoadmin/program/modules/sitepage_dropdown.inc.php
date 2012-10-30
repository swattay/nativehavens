<?php
error_reporting(E_PARSE);
if($_GET['_SESSION'] != '' || $_POST['_SESSION'] != '' || $_COOKIE['_SESSION'] != '') { exit; }

/*************************************************************************************************
 ___                     _   ___   _        _         ___
/ __| _ __  ___  ___  __| | |   \ (_) __ _ | |  ___  | _ \ __ _  __ _  ___  ___
\__ \| '_ \/ -_)/ -_)/ _` | | |) || |/ _` || | |___| |  _// _` |/ _` |/ -_)(_-<
|___/| .__/\___|\___|\__,_| |___/ |_|\__,_||_|       |_|  \__,_|\__, |\___|/__/
     |_|                                                        |___/

# Developer 'Open Page' drop-downs
# Include this where ever you want to have a dropdown box with all the site pages in it
/*************************************************************************************************/

# Build page arrays (based on menu status) for jump menus
# Loop all and split into diff arrays by menu status
$pgrez = mysql_query("SELECT * FROM site_pages ORDER BY page_name");

$main_pages = array();
$sub_pages = array();
$offmenu_pages = array();
$dropdown_options = "";

# Build sortable page name array
#-----------------------------------------------
while ( $getPage = mysql_fetch_array($pgrez) ) {
   # Main menu pages
   if ( $getPage['main_menu'] > 0 ) {
      $main_pages[] = $getPage['page_name'];

   # Sub-menu pages
   } elseif ( strlen($getPage['sub_page_of']) > 4 ) {
      $tmppg = split("~~~", $getPage['sub_page_of']);
      $sub_pages[$tmppg[0]][] = array('sort'=>$tmppg[1], 'name'=>$getPage['page_name']);

   # Off-menu pages
   } else {
      $offmenu_pages[] = $getPage['page_name'];
   }
}

# Build dropdown options
#-----------------------------------------------
# [ On-menu pages ]
$dropdown_options .= "       <option value=\"\" style=\"background-color: #ccc;\">[".lang("On-Menu Pages")."]</option>\n";
foreach ( $main_pages as $key=>$mp ) {
   $dropdown_options .= "       <option value=\"".$mp."\">".$mp."</option>\n";

   # Pull sub-pages for this page
   foreach ( $sub_pages[$mp] as $sp ) {
      $dropdown_options .= "       <option value=\"".$sp[name]."\">&gt;&gt; ".$sp[name]."</option>\n";
   }
}

# [ Off-menu pages ]
$dropdown_options .= "       <option value=\"\" style=\"background-color: #ccc;\">[".lang("Off-Menu Pages")."]</option>\n";

foreach ( $offmenu_pages as $key=>$op ) {
   $dropdown_options .= "       <option value=\"".$op."\">".$op."</option>\n";
}



?>