<?php
error_reporting(E_PARSE);
if($_GET['_SESSION'] != '' || $_POST['_SESSION'] != '' || $_COOKIE['_SESSION'] != '') { exit; }

if(!include_once('../../includes/product_gui.php')){
	exit;
}

###############################################################################
## Soholaunch(R) Site Management Tool
## Version 4.5
##
## Author: 			Mike Johnston [mike.johnston@soholaunch.com]
## Homepage:	 	http://www.soholaunch.com
## Bug Reports: 	http://bugzilla.soholaunch.com
## Release Notes:	sohoadmin/build.dat.php
###############################################################################

##############################################################################
## COPYRIGHT NOTICE
## Copyright 1999-2003 Soholaunch.com, Inc.  All Rights Reserved.
##
## This script may be used and modified in accordance to the license
## agreement attached (license.txt) except where expressly noted within
## commented areas of the code body. This copyright notice and the comments
## comments above and below must remain intact at all times.  By using this
## code you agree to indemnify Soholaunch.com, Inc, its coporate agents
## and affiliates from any liability that might arise from its use.
##
## Selling the code for this program without prior written consent is
## expressly forbidden and in violation of Domestic and International
## copyright laws.
###############################################################################

$sresult = mysql_query("select * from site_pages where page_name = '$currentPage'");
$srow = mysql_fetch_array($sresult);

if($srow['username'] != "" && $srow['username'] != "NULL") {
   $pgsec = "1";
} else {
   $pgsec = "";
}

if($new_name) {
   $pge_request = $new_name;
   $pge_request = str_replace("&", "", $pge_request);
   $pge_request = str_replace("'", "", $pge_request);
   $pge_request = str_replace("\"", "", $pge_request);
   $pge_request = eregi_replace(" .", "_", $pge_request);
//   $pge_request = htmlspecialchars($pge_request, ENT_QUOTES);
//   echo $pge_request; exit;

} else {
   $pge_request = eregi_replace(" ", "_", $currentPage);
}

//$pge_request = trim($pge_request);

$page_content = $pge_request . ".con";
$contentpath = $doc_root."/sohoadmin/tmp_content/".$page_content;
$GATEway = stripslashes($GATEway);

#######################################
### PULL GATEWAY BASE TEMPLATE FILE ###
#######################################

$filename = "data/GATEWAY_BASE.HTML";
$file = fopen("$filename", "r");
	$BASE_HTML = fread($file,filesize($filename));
fclose($file);

// Build Gateway Text Menu
// ----------------------------------------------

$thisMenu = "";
$result = mysql_query("SELECT * FROM site_pages WHERE type = 'main' AND main_menu <> '' ORDER BY main_menu");
while ($row = mysql_fetch_array ($result)) {

$htmlpgname = eregi_replace(" ", "_", $row['page_name']);
$htmlpgname .= ".html";
$htmlpath = $doc_root."/".$htmlpgname;
	if(!file_exists($htmlpath)) {
	$thisMenu .= "<a href=\"".pagename($row['page_name'])."\">".$row['page_name']."</a>&nbsp;&nbsp;|&nbsp;&nbsp;";
	} else {
	$thisMenu .= "<a href=\"".$htmlpgname."\">".$row['page_name']."</a>&nbsp;&nbsp;|&nbsp;&nbsp;";
	}

//	$thisMenu .= "<a href=\"index.php?pr=$row[page_name]\">$row[page_name]</a>&nbsp;&nbsp;|&nbsp;&nbsp;";
}

$mnu_len = strlen($thisMenu);
$mnu_subt = $mnu_len - 25;
$thisMenu = substr("$thisMenu", 0, $mnu_subt);


// ----------------------------------------------
// START RADIUS 3 ALTERATION
// ----------------------------------------------
$mfilename = "$cgi_bin/meta.conf";
if (file_exists($mfilename)) {
	$file = fopen("$mfilename", "r");
		$body = fread($file,filesize($mfilename));
	fclose($file);
	$lines = split("\n", $body);
	$numLines = count($lines);
	for ($x=0;$x<=$numLines;$x++) {
		$temp = split("=", $lines[$x]);
		$variable = $temp[0];
		$value = $temp[1];
		${$variable} = $value;
	}
}


// ----------------------------------------------
// TEMPLATE UPDATE
// ----------------------------------------------

$disTemplate = $_POST['pTemplate'];
$disTemplate = eregi_replace($doc_root."/","",$disTemplate);
//$disTemplate = eregi_replace("","",$disTemplate);

if($srow['template'] != $disTemplate && $_POST['pTemplate'] != ''){

   if($disTemplate == "default"){
      mysql_query("UPDATE site_pages SET template = '' WHERE page_name = '$PROP_KEYNAME'");
   }else{
      if(!mysql_query("UPDATE site_pages SET template = '$disTemplate' WHERE page_name = '$PROP_KEYNAME'")){
         //echo "Could not update template for (".$PROP_KEYNAME.")<br>";
      }else{
         //echo "Updated (".$PROP_KEYNAME.")<br>";
      }
   }
}

//echo "this template (".$_POST['pTemplate'].")<br>";
//exit;

// $thisTitle = strtoupper($dot_com);
if ( $prop_title != "" ) { $thisTitle = $prop_title; } else { $thisTitle = strtoupper($this_ip); }
$sitetitle = $site_title;
$conflag = "";
if ($GATEway != "CON" && $GATEway != "" && $pgsec != "1") {
$BASE_HTML = eregi_replace("#GATEWAY#", "$GATEway", $BASE_HTML);
$BASE_HTML = eregi_replace("#MENU#", "$thisMenu", $BASE_HTML);
} else if(($GATEway == "CON" || $GATEway == "") && file_exists($contentpath) && $pgsec != "1") {
$filename = "$contentpath";
$file = fopen("$filename", "r");
$CONtent = fread($file,filesize($filename));
fclose($file);
$BASE_HTML = eregi_replace("#GATEWAY#", "$CONtent", $BASE_HTML);
$BASE_HTML = eregi_replace("#MENU#", "$thisMenu", $BASE_HTML);
$conflag = "1";
} else {
$filename = "data/GATEWAY_DEFAULT.HTML";
$file = fopen("$filename", "r");
$GATEway = fread($file,filesize($filename));
fclose($file);

$GATEway = eregi_replace("#PAGE#", "$pge_request", $GATEway);
$GATEway = eregi_replace("#DOTCOM#", "$dot_com", $GATEway);
$BASE_HTML = eregi_replace("#GATEWAY#", "$GATEway", $BASE_HTML);
$BASE_HTML = eregi_replace("#MENU#", "If not connected within 5 seconds... <a href=\"".pagename($pge_request)."\">click here</a>", $BASE_HTML);
$GATEway = ""; // RESET FOR TOOL SAKE
}

// Build Gateway Text Menu
// ----------------------------------------------

$BASE_HTML = eregi_replace("#SITETITLE#", "$sitetitle", $BASE_HTML);
$BASE_HTML = eregi_replace("#HEADING#", "<a href='../index.php'>$thisTitle</a>", $BASE_HTML);
$BASE_HTML = eregi_replace("#TITLE#", "$thisTitle", $BASE_HTML);

if($KEYwords != "") {
   $BASE_HTML = eregi_replace("#KEYWORDS#", "$KEYwords", $BASE_HTML);
} else {
   $BASE_HTML = eregi_replace("#KEYWORDS#", "$site_keywords", $BASE_HTML);
}

if($prop_desc != "") {
   $BASE_HTML = eregi_replace("#DESCRIPTION#", "$prop_desc", $BASE_HTML);
} else {
   $BASE_HTML = eregi_replace("#DESCRIPTION#", "$site_description", $BASE_HTML);
}

$date = date("m/d/Y");
$BASE_HTML = eregi_replace("#DATE#", "$date", $BASE_HTML);

##########################################################################
##########################################################################

if (strlen($SAVEAS_name) < 2) {

	$string = stripslashes($string);
	$string = eregi_replace("'", "", $string);
	$string = str_replace("&", "", $string);

	$PROP_name = stripslashes($PROP_name);
	//$PROP_name = sterilize($PROP_name);
	//$PROP_name = ucwords($PROP_name);

	#########################################
	### UPDATE PROPERTIES NOW! ##############
	#########################################


	$GATEway = addslashes($GATEway);

   if($conflag != "1") {
   	$KEYwords = $KEYwords . "~~~SEP~~~" . $GATEway;
   } elseif($conflag == "1") {
      $KEYwords = $KEYwords . "~~~SEP~~~CON";
   }


   if( $PROP_splash == "y" && $PROP_splash_type == "y" ){		// bgcolor
   	$prop_bgcolor = eregi_replace("#", "", $prop_bgcolor);
   	$prop_bgcolor_image = strtoupper($prop_bgcolor);
   } elseif ($PROP_splash == "y" && $PROP_splash_type == "i") {		// bg image
   	$prop_bgcolor_image = addslashes($PROP_image_type);
   } else {
   	$prop_bgcolor_image = "";
   	$PROP_splash_type = "";
   }
  

	$disStuff = "password = '".slashthis($KEYwords)."', page_name = '$PROP_name', type = 'Main', username = '$PROP_sec_code', ";
	$disStuff .= "splash = '$PROP_splash_type',";
	$disStuff .= "bgcolor = '$prop_bgcolor_image', title = '".slashthis($prop_title)."', description = '".slashthis($prop_desc)."'";
	if ( !mysql_query("UPDATE site_pages SET $disStuff WHERE page_name = '$PROP_KEYNAME'") ) { echo "Could not save page properties because: ".mysql_error(); exit; }

	if ($PROP_KEYNAME != $PROP_name) { $currentPage = $PROP_name; }


# Save As...
} else {
	$time = time();
 	for ($i=0;$i<=10;$i++) {
		srand((double)microtime()*1000000);
		$tempVar = rand(0,9);
		$time = "$tempVar$time";
	}
	$link = substr("$time", 0, 10);

	$SAVEAS_name = stripslashes($SAVEAS_name);
	$SAVEAS_name = eregi_replace("'", "", $SAVEAS_name);
	$SAVEAS_name = str_replace("&", "", $SAVEAS_name);


////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////


	$PROP_name = $srow["page_name"];
	$PROP_splash = $srow["splash"];
	$prop_bgcolor = $srow["bgcolor"];
	$PROP_sec_code = $srow["username"];
	$PROP_pagetype = $srow["type"];
	$CUR_TEMPLATE = $srow["template"];
	$tmp = $srow["password"];
	$key_gate = split("~~~SEP~~~", $tmp);
	$prop_title = $srow[title];
	$prop_desc = $srow[description];

////////////////////////////////////////////////////////////////////////////////////////


	# Must be webmaster or have access to Create Pages to use Save As...
	if ( $_SESSION['CUR_USER_ACCESS'] == "WEBMASTER" || eregi(";MOD_CREATE_PAGES;", $_SESSION['CUR_USER_ACCESS']) ) {
	   mysql_query("INSERT INTO site_pages VALUES('$SAVEAS_name','$PROP_pagetype','', '$tmp', '','$link','$PROP_sec_code','$PROP_splash','$prop_bgcolor','$prop_title','$prop_desc','$CUR_TEMPLATE')");
	}

	# If this is a non-webmaster user, make sure they have access to create new pages, then update their access rights to include new page name
	if ( $_SESSION['CUR_USER_ACCESS'] != "WEBMASTER" && eregi(";MOD_CREATE_PAGES;", $_SESSION['CUR_USER_ACCESS']) ) {
      $pagename_underscored = eregi_replace(" ", "_", $SAVEAS_name);
      $_SESSION['CUR_USER_ACCESS'] = $_SESSION['CUR_USER_ACCESS'].$pagename_underscored.";";
      $qry = "UPDATE USER_ACCESS_RIGHTS SET ACCESS_STRING = '".$_SESSION['CUR_USER_ACCESS']."' WHERE LOGIN_KEY = '".$_SESSION['CUR_USER_KEY']."'";
      mysql_query($qry);
	}

	$currentPage = $SAVEAS_name;

}

$currentPage = trim($currentPage);
$pge_request = eregi_replace(" ", "_", $currentPage);

$search_engine = $BASE_HTML;

# Add to recent pages list for priority display on open/edit pages
$_SESSION['recent_pages'][$currentPage] = time();

//$filename = "$doc_root/$pge_request.html";
//$file = fopen("$filename", "w");
//fwrite($file, "$search_engine");
//fclose($file);
//
//$lowerpage = strtolower($pge_request);
//
//$filename = "$doc_root/$lowerpage.html";
//$file = fopen("$filename", "w");
//fwrite($file, "$search_engine");
//fclose($file);


?>