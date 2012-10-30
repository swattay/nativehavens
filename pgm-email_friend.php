<?php
error_reporting(E_PARSE);
if($_GET['_SESSION'] != '' || $_POST['_SESSION'] != '' || $_COOKIE['_SESSION'] != '') { exit; }


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
## Copyright 1999-2003 Soholaunch.com, Inc. and Mike Johnston
## Copyright 2003-2007 Soholaunch.com, Inc.
## All Rights Reserved.
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

session_start();

track_vars;

reset($_POST);


include_once("pgm-site_config.php");
include_once("sohoadmin/program/includes/shared_functions.php");

$spamflagBool = false;
$formpref = new userdata('forms');
foreach ( $_POST as $name=>$value ) {

	$value = stripslashes($value);
	//$value = strtolower($value);
	$value = eregi_replace("\n", " ", $value); 	// Windows Line Feed Replaced with a Space
	$value = eregi_replace("\r", "", $value);	// Unix Line Feed

	$name = stripslashes($name);

	$value = htmlspecialchars($value);		// Make sure no HTML code is sent to form processor : bugzilla #13
	
	if ( $formpref->get('block-links') == 'on' && eregi('http://', $value) ) {
		$spamflagBool = true;
	}	
		
	${$name} = $value;
}



	# Spammer rejection message goes here
	if ( $spamflagBool == true ) {
		echo '<div style="width: 500px;background: #efefef;font: 12px Trebuchet MS, verdana, arial, sans-serif;padding: 15px;position: absolute; left:30%; top: 40%; border: 1px dotted red;">'."\n";
		echo $formpref->get('spam-trap-message');
		echo "&nbsp;&nbsp;&nbsp;\n<a href=\"#\" onClick=\"history.go(-1)\">".lang('Return to Previous Page')."</a>\n";
		echo '</div>'."\n";
		exit;
	}



$mailpage=$_GET['mailpage'];
if($mailpage != ''){
	$pr=$_GET['mailpage'];
} else {
	$mailpage=$_POST['mailpage'];
	$pr=$_POST['mailpage'];
}
##########################################################################
### WE WILL NEED TO KNOW THE DATABASE NAME; UN; PW; ETC TO OPERATE THE ###
### REAL-TIME EXECUTION.  THIS IS CONFIGURED IN THE isp.conf FILE      ###
##########################################################################

$dot_com = $this_ip;

#########################################################
### MAKE pageRequest VAR AND pr VAR MATCH			###
#########################################################

if ($pageRequest == "" && $pr != "") { $pageRequest = $pr; }

$site_title = strtoupper($SERVER_NAME);

#######################################################################################################
###### SEND EMAIL	(After the form has been processed and this script is then recalled)
#######################################################################################################

if ($emailcoming == 1) {

//	if($_POST['yourname'] == ''){
//		echo 'no name';
//	}
//	if($_POST['from'] == ''){
//		echo 'no email address';
//	}
//	if($_POST['sendto'] == ''){
//		echo 'no friend email address';
//	}
//
//	if($_POST['message'] == ''){
//		echo 'no message';
//	}
//exit;
	if($_POST['email_field'] != ''){
		header("location: index.php"); exit;
	}

	$message = stripslashes($message);
	$yourname = ucwords($yourname);
	$rrmessage = "";
	$custom_message = "media/emailfriend.txt";	// Upload this file via upload files to customize message

	if (!file_exists("$custom_message")) {

		$rrmessage .= lang("I found this web site that you might be interested in")."";
		$rrmessage .= " ".lang("so I thought I'd email it to you...")."\n\n".lang("Just click on the link to see it!")."\n";

	} else {

		$file = fopen("$custom_message", "r");
			$rrmessage = fread($file,filesize($custom_message));
		fclose($file);

	}

	$rrmessage .= "\n";

   if ( $mailpage == startpage() ) {
      $tmpExt = "";
   } else {
      $tmpExt = "/".pagename($mailpage);
   }

	$rrmessage .= "http://$this_ip".$tmpExt."\n";
	$rrmessage .= "\n";
	$rrmessage .= "$message\n\n";
	$rrmessage .= "$yourname\n";

	$custom_title = "media/emailtitle.txt";		// Upload this file via upload files to customize subject line
	if (!file_exists("$custom_title")) {

		$thistitle .= lang("I found something you might want to see...")."";

	} else {

		$file = fopen("$custom_title", "r");
			$thistitle = fread($file,filesize($custom_title));
			chop($thistitle);
			$thistitle = eregi_replace("\n", "", $thistitle);
			$thistitle = eregi_replace("\r", "", $thistitle);
		fclose($file);

	}

	$sendto = eregi_replace(',.*$', '', $sendto);
	mail("$sendto", "$thistitle", "$rrmessage", "FROM: $from");	// Utilize built-in Sendmail to send this email

	// *****************************************************************************************************
	// Dump user back to the page they emailed to a friend and send a "pagesent" flag to the builder system.
	// This will produce a javascript popup confirmation that the email was sent.
	// *****************************************************************************************************

	header("Location: ".pagename($mailpage, "&")."epagesent=1");
	exit;

}

###########################################################################################
##### CALL TEMPLATE BUILDER AND PRESENT INITIAL FORM TO WEB VISITOR
###########################################################################################

$module_active = "yes";				// Make sure to leave #CONTENT# variable intact when returning header/footer var
include ("pgm-realtime_builder.php");	// Generate Template header/footer vars

echo ("$template_header\n");			// Go ahead and display header now


#######################################################################################################
###### PRESENT EMAIL ITEM TO A FRIEND FORM
#######################################################################################################

if ($emailcoming == "") {

	$contentarea = "<center><table id=\"email_friend-outer\" border=0 align=center cellpadding=0 cellspacing=0 width=\"90%\"><tr><td><table border=0 cellpadding=4 cellspacing=0 width=100% bgcolor=slategray align=center><tr><td align=left valign=middle>\n";
	$contentarea .= "<font face=Verdana, Arial, sans-serif size=2 color=white><B>&nbsp;".lang("Email this page to a friend")."...</B></font></td></tr></table>\n";

	$contentarea .= "<form method=post action=pgm-email_friend.php>\n";
	$contentarea .= "<input type=hidden name=customernumber value=\"$customernumber\">\n";
	$contentarea .= "<input type=hidden name=emailcoming value=\"1\">\n";
	$contentarea .= "<input type=hidden name=mailpage value=\"$mailpage\">\n";

	$contentarea .= "<div style=\"display:none;\">\n";
	$contentarea .= "<input type=\"text\" name=\"email_field\" value=\"\">\n";
	$contentarea .= "</div>\n";

	$contentarea .= "<table id=\"email_friend-inner\" border=0 cellpadding=4 cellspacing=0 width=100%><tr>\n";
	$contentarea .= "<td align=right valign=middle><font face=Verdana, Arial, sans-serif size=2>".lang("Your Name").":</td><td align=left valign=middle><input type=text class=cinput size=22 name=yourname style='width: 300px;'></td></tr>\n";
	$contentarea .= "<tr><td align=right valign=middle><font face=Verdana, Arial, sans-serif size=2>".lang("Your Email Address").":</td><td align=left valign=middle><input type=text class=cinput size=22 name=from style='width: 300px;'></td></tr>\n";
	$contentarea .= "<tr><td align=right valign=middle><font face=Verdana, Arial, sans-serif size=2>".lang("Friends Email Address").":</td><td align=left valign=middle><input type=text class=cinput size=22 name=sendto style='width: 300px;'></td></tr>\n";
	$contentarea .= "<tr><td align=right valign=top><font face=Verdana, Arial, sans-serif size=2>".lang("Personal Message").":</td><td align=left valign=top><textarea name=message cols=25 rows=8 wrap=virtual class=\"textfield\" style='width: 300px;'></textarea></td></tr>\n";
	$contentarea .= "<tr><td align=center colspan=2><input type=submit class=FormLt1 value=\"".lang("Send Now")."!\"></td></tr></table></form></td></tr></table></center>\n";

	// Log this calendar view into stats
	// -----------------------------------------------------------------

	if (file_exists("pgm-site_stats.inc.php")) {		// Check; this mod N/A in Lite Version
		$statpage = lang("Email a Friend");
		include ("pgm-site_stats.inc.php");
	}

	// -----------------------------------------------------------------

}

#######################################################################################################

// **************************************************************************
// Replace intact #CONTENT# var with $contentarea created within this script
// **************************************************************************

$template_footer = eregi_replace("#CONTENT#", $contentarea, $template_footer);

// **************************************************************************
// Display template footer var from realtime_builder and close out this page
// **************************************************************************

echo ("$template_footer\n");

exit;

?>