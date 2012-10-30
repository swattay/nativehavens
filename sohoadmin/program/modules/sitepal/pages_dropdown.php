<?php
error_reporting(E_PARSE);
if($_GET['_SESSION'] != '' || $_POST['_SESSION'] != '' || $_COOKIE['_SESSION'] != '') { exit; }
# Orignally built for QuickEdit Dropdown plugin
# Copy-pasted and tweaked to use for Click Counter
session_start();

# Build page arrays (based on menu status)
# Loop all and split into diff arrays by menu status
$pgrez = mysql_query("SELECT * FROM site_pages ORDER BY page_name");

$main_pages = array();
$sub_pages = array();
$offmenu_pages = array();

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

# [ On-menu pages ]
$dropdown_box = "";
$dropdown_box .= "<option value=\"\" style=\"background-color: #ccc;\">".lang("On-Menu Pages")."...</option>\n";
foreach ( $main_pages as $key=>$mp ) {
   $dropdown_box .= "<option value=\"".$mp."\">".$mp."</option>\n";

   # Pull sub-pages for this page
   foreach ( $sub_pages[$mp] as $sp ) {
      $dropdown_box .= "<option value=\"".$sp[name]."\">&gt;&gt; ".$sp[name]."</option>\n";
   }
}

# [ Off-menu pages ]
if ( count($offmenu_pages) > 0 ) {
   $dropdown_box .= "<option value=\"\" style=\"background-color: #ccc;\">".lang("Off-Menu Pages")."&hellip;</option>\n";

   # Pull off-menu pages
   foreach ( $offmenu_pages as $key=>$op ) {
      $dropdown_box .= "<option value=\"".$op."\">".$op."</option>\n";
   }
} // end if


# Create new...
$dropdown_box .= "<option value=\"createpage\" style=\"background-color: #66cc91;font-style: italic;\">".lang("Create New")."&hellip;</option>\n";

echo $dropdown_box;
?>