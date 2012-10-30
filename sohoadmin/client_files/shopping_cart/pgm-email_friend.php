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

error_reporting(0);
session_cache_limiter('none');
session_start();
track_vars;

$THIS_DISPLAY = "";	// Make Display Variable Blank in Case of Session Memory

#################################################################################
### WE WILL NEED TO KNOW THE DATABASE NAME; UN; PW; ETC TO OPERATE THE
### REAL-TIME EXECUTION.  THIS IS CONFIGURED IN THE isp.conf FILE
#################################################################################
include_once('sohoadmin/program/includes/shared_functions.php');
include("pgm-cart_config.php");

$refer = $_SERVER['HTTP_REFERER'];
$cthis_ip = eregi_replace('www\.', '', $this_ip);
//if(!eregi($cthis_ip, $refer)){echo "This request to email a friend needs to be made by ".$this_ip.".  Please contact the webmaster if this is an error."; exit;}

$dot_com = $_SESSION['this_ip'];	// Assign dot_com variable to configured ip address

#################################################################################
### READ DATABASED OPTIONS INTO MEMORY NOW
#################################################################################

$result = mysql_query("SELECT * FROM cart_options");
$OPTIONS = mysql_fetch_array($result);

if($OPTIONS['DISPLAY_EMAILFRIEND'] != 'Y'){
	header("location: start.php?browse=1"); exit;
}


#################################################################################
### Check Security
#################################################################################
if($_REQUEST['id'] != ''){
	$secc = mysql_query("SELECT OPTION_SECURITYCODE FROM cart_products WHERE PRIKEY = '".$_REQUEST['id']."'");
	$sec_check = mysql_fetch_array($secc);
	if($sec_check['OPTION_SECURITYCODE'] != 'Public'){
		$groups_ar = explode(';', $_SESSION['GROUPS']);
		if(in_array($sec_check['OPTION_SECURITYCODE'], $groups_ar)){
			//echo 'found '.$sec_check['OPTION_SECURITYCODE'];
			// Let them stay, authorized to see this product
		} else {
			header("location: start.php?browse=1"); exit;
		}
	}
}
// ------------------------------------
// Pull Product Information for Email
// ------------------------------------

$result = mysql_query("SELECT * FROM cart_products WHERE PRIKEY = '$id'");
if(mysql_num_rows($result) < 1){
	header("location: start.php?browse=1"); exit;
}
$PROD = mysql_fetch_array($result);

#################################################################################
### IF THE SEND FLAG IS ACTIVE, PROCESS INFORMATION AND SEND EMAIL(S)
#################################################################################

if ($SEND == 1) {
	if($_POST['email_field'] != ''){
		header("location: start.php?browse=1"); exit;
	}

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
	
	
	// -------------------------------------------------------------
	// First, Check to make sure that all fields are filled out.
	// -------------------------------------------------------------

	$err = 0;

	if (strlen($user_name) < 2) { $err = 1; }
	if (strlen($user_email) < 5) { $err = 1; }
	if (strlen($friend_name) < 2) { $err = 1; }
	if (strlen($friend_email) < 5) { $err = 1; }
	if ($subject_line == "") { $err = 1; }

	if ($err == 0) {

		// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		// Build HTML for this product.  We're going to display it back
		// to this user anyway, so lets just do it
		// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		$link_to_sku = "http://".$_SESSION['this_ip']."/shopping/pgm-more_information.php?id=".$PROD['PRIKEY'];
//		echo "[".$link_to_sku."]<br/>";

		ob_start();
		include("prod_search_template.inc");
		$SKUINFO_HTML = ob_get_contents();
		ob_end_clean();

      # Format Links For Off-Site Email Link
      $SKUINFO_HTML = eregi_replace("\[ \<A HREF=\"pgm-email_friend.php\?id=[0123456789]*&=SID\"\>".lang("Email To A Friend")."\</A\> \]", '', $SKUINFO_HTML);
      $SKUINFO_HTML = eregi_replace("../images", "http://".$_SESSION['this_ip']."/images", $SKUINFO_HTML);
      $SKUINFO_HTML = eregi_replace("start.php", "http://".$_SESSION['this_ip']."/shopping/start.php", $SKUINFO_HTML);

		if ($email_type == "HTML") {	// This is an HTML Email Request

			$message = stripslashes($message);

			$headers = "";
			$headers .= "From: $user_name <$user_email>\r\n";
			$headers .= "Content-Type: text/html; charset=us-ascii; name=\"product.html\"\r\n";
			$headers .= "Content-Transfer-Encoding: 7bit\r\n";
			$headers .= "Content-Disposition: inline;\n filename=\"product.html\"\r\n";

			# Custom email to friend template exist?
			$etf_template_file = $_SESSION['docroot_path']."/media/emailtofriend_template.html";
			if ( file_exists($etf_template_file) ) {
			   # YES - Use custom template
			   $etf_template_html = file_get_contents($etf_template_file);
			   $etf_message = "<font face=\"verdana\">$friend_name,<br/><br/>$message<br/><br/>- $user_name</font><br/><br/>\n";
			   $etf_message .= $SKUINFO_HTML;
			   $EMAIL_CONTENT = eregi_replace("#content#", $etf_message, $etf_template_html);

			} else {
			   # NO - Use default (blank) template
   			$EMAIL_CONTENT = "<html><head>\n\n";
   			$EMAIL_CONTENT .= "<style>\n\n";
   			$EMAIL_CONTENT .= "     .text {  font-family: Arial, Helvetica, sans-serif; font-size: 9pt}\n";
   			$EMAIL_CONTENT .= "     .SMtext {  font-family: Arial, Helvetica, sans-serif; font-size: 8pt}\n";
   			$EMAIL_CONTENT .= "</style>\n\n";

   			$EMAIL_CONTENT .= "<title>".$PROD['PROD_NAME']."</title>\n\n</head>\n";
   			$EMAIL_CONTENT .= "<body bgcolor=\"white\" color=black link=\"red\" alink=red vlink=\"red\">\n\n";

   			$EMAIL_CONTENT .= "<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\" width=\"400\" align=center><tr><td class=\"text\" align=\"left\" valign=\"top\">\n";

   			$EMAIL_CONTENT .= "<font face=\"verdana\">$friend_name,<br/><br/>$message<br/><br/>- $user_name</font><br/><br/>\n";

   			$EMAIL_CONTENT .= $SKUINFO_HTML;
            $EMAIL_CONTENT .= "</td></tr></table></body>\n</html>\n";
         }


		} else {
		   # TEXT-ONLY email format
			$headers = "FROM: $user_email";

			$EMAIL_CONTENT = "\n$friend_name,\n\n$message\n\n- $user_name\n\n";
			$EMAIL_CONTENT .= $link_to_sku."\n\n";
			$EMAIL_CONTENT .= "[".$this_ip."]\n\n";

		} // End Email Creation

//      # TESTING
//		echo "<h1>EMAIL_CONTENT</h1>\n";
//		echo $EMAIL_CONTENT; exit;


		// -----------------------------------------------------------------
		// Use SENDMAIL to zip this puppy out
		// -----------------------------------------------------------------

		$to = $friend_email;

		if (strlen($to) < 80) {
			$to = eregi_replace('[^a-zA-Z0-9\.@]', '', stripslashes($to));
		   mail("$to", "$subject_line", "$EMAIL_CONTENT", $headers) || DIE (lang("There is a problem with our email server."));
		}else{
		   $SEND = 0; // Set Send Flag to Zero so we can repeat the user input to get it right
		   $err = 1;
		}

		// ------------------------------------------------------------------
		// If user requests a copy, send that too.
		// ------------------------------------------------------------------

		if ($copy_me == 1 && $SEND != 0) {

			$to = $user_email;
		   if(strlen($to) < 80){
		   	$to = eregi_replace('[^a-zA-Z0-9\.@]', '', stripslashes($to));
			   mail("$to", "$subject_line", "$EMAIL_CONTENT", $headers) || DIE (lang("There is a problem with our email server."));
			}else{
		      $SEND = 0; // Set Send Flag to Zero so we can repeat the user input to get it right
		      $err = 1;
		   }

		}

		// -----------------------------------------------------------------
      if($SEND != 0){
		   $THIS_DISPLAY = "<font color=\"DARKBLUE\"><b>".lang("Thanks")." $user_name!<br/><br/>".lang("Your email has been sent").".<br/><br/>";
		   $THIS_DISPLAY .= $HTML_DISPLAY;
	   }

	} else {

		$SEND = 0; // Set Send Flag to Zero so we can repeat the user input to get it right

	}

} // End SEND EQ 1

#################################################################################
### IF THE SEND FLAG IS NOT ACTIVE, THEN DISPLAY THE INITIAL EMAIL FORM
#################################################################################

if ($SEND != 1) {

	if ($subject_line == "") { $subject_line = lang("A cool product I found..."); }

	$THIS_DISPLAY .= "<br/><br/>\n";
	$THIS_DISPLAY .= "<form name=\"EMAILSKU\" method=\"post\" action=\"pgm-email_friend.php\">\n\n";

	$THIS_DISPLAY .= "<input type=\"hidden\" name=\"SEND\" value=1>\n";
	$THIS_DISPLAY .= "<input type=\"hidden\" name=\"id\" value=\"$id\">\n";
	$THIS_DISPLAY .= "<input type=\"hidden\" name=\"customernumber\" value=\"$customernumber\">\n\n";

	$THIS_DISPLAY .= "<div style=\"display:none;\">\n";
	$THIS_DISPLAY .= "<input type=\"text\" name=\"email_field\" value=\"\">\n";
	$THIS_DISPLAY .= "</div>\n";

   $THIS_DISPLAY .= "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\" class=\"text\" id=\"email_friend_form\">\n";
	$THIS_DISPLAY .= " <tr>\n";
	$THIS_DISPLAY .= "  <th colspan=\"2\" bgcolor=\"".$OPTIONS['DISPLAY_HEADERBG']."\">\n";
	$THIS_DISPLAY .= "   <b><font color=\"".$OPTIONS['DISPLAY_HEADERTXT']."\" face=\"verdana, Arial, Helvetica, sans-serif\">".lang("Email Product").": ".$PROD['PROD_NAME']."</font></b>\n";
	$THIS_DISPLAY .= "  </th>\n";
	$THIS_DISPLAY .= " </tr>\n";

   if ($err == 1) {
      $THIS_DISPLAY .= " <tr>\n";
      $THIS_DISPLAY .= "  <td align=\"center\" valign=\"middle\" class=\"text\" colspan=\"2\">\n";
      $THIS_DISPLAY .= "  <font color=\"RED\">".lang("You have left one or more required fields blank").".<br/>".lang("Please correct and re-submit your email").".</td>\n";
      $THIS_DISPLAY .= " </tr>\n";
   }

   # Bugzilla #29
   $THIS_DISPLAY .= "<tr>\n";
   $THIS_DISPLAY .= " <td align=\"center\" valign=\"middle\" class=\"text\" colspan=\"2\">\n";
   $THIS_DISPLAY .= "  <font color=\"red\" size=1><b><i><Sup>*</sup></font> ".lang("Required Fields")."</b></i>\n";
   $THIS_DISPLAY .= " </td>\n";
   $THIS_DISPLAY .= "</tr>\n";


   // Start Form Input Boxes
   // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
   $THIS_DISPLAY .= "<tr>\n";
   $THIS_DISPLAY .= "<td align=\"left\" valign=\"middle\" class=\"text\"><font color=\"red\"><Sup>*</sup></font>".lang("Your <u>Full</u> Name").": </td>\n";
   $THIS_DISPLAY .= "<td align=\"left\" valign=\"middle\" class=\"text\"><input type=\"text\" size=\"35\" class=\"text\" name=user_name value='$user_name' style='width: 250px; color: darkblue;'></td>\n";

   $THIS_DISPLAY .= "</tr>\n";
   $THIS_DISPLAY .= "<tr>\n";

   $THIS_DISPLAY .= "<td align=\"left\" valign=\"middle\" class=\"text\"><font color=\"red\"><Sup>*</sup></font>".lang("Your Email Address").": </td>\n";
   $THIS_DISPLAY .= "<td align=\"left\" valign=\"middle\" class=\"text\"><input type=\"text\" size=\"35\" class=\"text\" name=user_email value='$user_email' style='width: 250px; color: darkblue;'></td>\n";

   $THIS_DISPLAY .= "</tr>\n";
   $THIS_DISPLAY .= "<tr>\n";

   $THIS_DISPLAY .= "<td align=\"left\" valign=\"middle\" class=\"text\"><font color=\"red\"><Sup>*</sup></font>".lang("Friend's <u>First</u> Name").": </td>\n";
   $THIS_DISPLAY .= "<td align=\"left\" valign=\"middle\" class=\"text\"><input type=\"text\" size=\"35\" class=\"text\" name=friend_name value='$friend_name' style='width: 250px; color: darkblue;'></td>\n";

   $THIS_DISPLAY .= "</tr>\n";
   $THIS_DISPLAY .= "<tr>\n";

   $THIS_DISPLAY .= "<td align=\"left\" valign=\"middle\" class=\"text\"><font color=\"red\"><Sup>*</sup></font>".lang("Friend's Email Address").": </td>\n";
   $THIS_DISPLAY .= "<td align=\"left\" valign=\"middle\" class=\"text\"><input type=\"text\" size=\"35\" class=\"text\" name=friend_email value='$friend_email' style='width: 250px; color: darkblue;'></td>\n";

   $THIS_DISPLAY .= "</tr>\n";
   $THIS_DISPLAY .= "<tr>\n";

   $THIS_DISPLAY .= "<td align=\"left\" valign=\"middle\" class=\"text\"><font color=\"red\"><Sup>*</sup></font>".lang("Subject Line of Email").": </td>\n";
   $THIS_DISPLAY .= "<td align=\"left\" valign=\"middle\" class=\"text\"><input type=\"text\" size=\"35\" class=\"text\" name=subject_line value='$subject_line' style='width: 250px; color: darkblue;'></td>\n";

   $THIS_DISPLAY .= "</tr>\n";
   $THIS_DISPLAY .= "<tr>\n";

   $THIS_DISPLAY .= "<td align=\"left\" valign=\"top\" class=\"text\">".lang("Personal Message").": </td>\n";
   $THIS_DISPLAY .= "<td align=\"left\" valign=\"middle\" class=\"text\"><TEXTAREA ROWS=\"15\" class=\"text\" name=\"message\" style='width: 250px; color: darkblue;'>$message</TEXTAREa></td>\n";

   $THIS_DISPLAY .= "</tr>\n";
   $THIS_DISPLAY .= "<tr>\n";

   $THIS_DISPLAY .= "<td align=\"right\" valign=\"middle\" class=\"text\">".lang("Email Type").": </td>\n";
   $THIS_DISPLAY .= "<td align=\"left\" valign=\"middle\" class=\"text\">\n";

   $THIS_DISPLAY .= "<input type=\"RADIO\" name=email_type value=\"HTML\" CHECKED> HTML \n";
   $THIS_DISPLAY .= "<input type=\"RADIO\" name=email_type value=\"TEXT\"> TEXT <font size=\"1\"><font color=#708090>&nbsp;<i>(".lang("AOL").", ".lang("Eudura").", ".lang("Etc.")." ".lang("Users").")</i></font>\n";

   $THIS_DISPLAY .= "</td>\n";

   $THIS_DISPLAY .= "</tr>\n";
   $THIS_DISPLAY .= "<tr>\n";

   $THIS_DISPLAY .= "<td align=\"center\" valign=\"middle\" class=\"text\" colspan=\"2\">\n";
   $THIS_DISPLAY .= "<input type=\"CHECKBOX\" name=copy_me value=\"1\" CHECKED>".lang("Yes, send me a copy of the email too.")."</td>\n";

   $THIS_DISPLAY .= "</tr>\n";
   $THIS_DISPLAY .= "<tr>\n";

   $THIS_DISPLAY .= "<td align=\"center\" valign=\"middle\" class=\"text\" colspan=\"2\">\n";
   $THIS_DISPLAY .= "&nbsp;</td>\n";

   $THIS_DISPLAY .= "</tr>\n";
   $THIS_DISPLAY .= "<tr>\n";

   $THIS_DISPLAY .= "<td align=\"center\" valign=\"middle\" class=\"text\" colspan=\"2\">\n";
   $THIS_DISPLAY .= "<input type=\"submit\" value=\"".lang("Send Email Now")."\" class=\"FormLt1\"></td>\n";

   $THIS_DISPLAY .= "</tr>\n";
   $THIS_DISPLAY .= "<tr>\n";

   $THIS_DISPLAY .= "<td align=\"center\" valign=\"middle\" class=\"text\" colspan=\"2\">\n";
   $THIS_DISPLAY .= "</td>\n";

   $THIS_DISPLAY .= "</tr>\n";
   $THIS_DISPLAY .= "</table>\n";

   // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	$THIS_DISPLAY .= "<CENTER><br/><br/></b><font face=\"Arial\" size=2>[ <a href=\"pgm-more_information.php?customernumber=$customernumber&id=".$PROD['PRIKEY']."\">".lang("Click Here to Return to")." $PROD[PROD_NAME]</a> ]</CENTER>\n";

	$THIS_DISPLAY .= "</form>\n\n";

} // END SEND FLAG NOT ON

#################################################################################
### SETUP SEARCH COLUMN HTML FOR DISPLAY (REGARDLESS OF FUNCTION CALL)
#################################################################################

$SEARCH_COLUMN = "";

ob_start();
	include("prod_search_column.inc");
	$SEARCH_COLUMN .= ob_get_contents();
ob_end_clean();


#################################################################################
### BUILD OVERALL TABLE TO PLACE SEARCH COLUMN TO THE LEFT OR RIGHT OF
### SEARCH RESULT DISPLAY AS DEFINED IN DISPLAY OPTIONS
#################################################################################

$FINAL_DISPLAY = "<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\" width=\"612\" align=\"center\">\n";

$FINAL_DISPLAY .= "<tr>\n";

	if (eregi("L", $OPTIONS[DISPLAY_COLPLACEMENT] )) {

		$FINAL_DISPLAY .= "<td width=\"150\" align=\"center\" valign=\"top\">\n\n$SEARCH_COLUMN\n\n</td>\n<td align=\"center\" valign=\"top\">\n\n$THIS_DISPLAY\n\n</td>\n";

	} else {

		$FINAL_DISPLAY .= "<td align=\"center\" valign=\"top\">\n\n$THIS_DISPLAY\n\n</td>\n<td width=\"150\" align=\"center\" valign=\"top\">\n\n$SEARCH_COLUMN\n\n</td>\n";

	}

$FINAL_DISPLAY .= "</tr>\n\n";

$FINAL_DISPLAY .= "</table>";


#################################################################################
### THE pgm-template_builder.php FILE COMPILES THE TEMPLATE DATA AND PAGE
### CONTENT DATA TOGETHER AND PUTS IT OUT AS THE $template_header AND
### $template_footer VARS RESPECTIVELY.
#################################################################################



$module_active = "yes";
include ("pgm-template_builder.php");

#################################################################################

echo ("$template_header\n");

	$template_footer = eregi_replace("#CONTENT#", $FINAL_DISPLAY, $template_footer);

echo ("$template_footer\n\n");

echo ("\n\n<SCRIPT language=\"Javascript\">\n     window.focus();\n</SCRIPT>\n\n");

exit;

?>