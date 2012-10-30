<?php
error_reporting(E_PARSE);
if($_GET['_SESSION'] != '' || $_POST['_SESSION'] != '' || $_COOKIE['_SESSION'] != '') { exit; }

###############################################################################
## Soholaunch(R) Site Management Tool
## Version 4.9
##
## Author: 			Mike Johnston [mike.johnston@soholaunch.com]
## Homepage:	 	http://www.soholaunch.com
## Bug Reports: 	http://bugz.soholaunch.com
## Release Notes:	http://wiki.soholaunch.com
###############################################################################

##############################################################################
## COPYRIGHT NOTICE
## Copyright 1999-2007 Soholaunch.com, Inc. and Mike Johnston
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
$dot_com = $this_ip;	// Assign dot_com variable to configured ip address

#################################################################################
### READ DATABASED OPTIONS INTO MEMORY NOW
#################################################################################

$result = mysql_query("SELECT * FROM cart_options");
$OPTIONS = mysql_fetch_array($result);

if($OPTIONS['DISPLAY_COMMENTS'] != 'Y'){
	exit;
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
$PROD = mysql_fetch_array($result);

#################################################################################
### IF THE SEND FLAG IS ACTIVE, PROCESS INFORMATION AND SEND EMAIL(S)
#################################################################################

if ($SEND == 1) {

	// -------------------------------------------------------------
	// First, Check to make sure that all fields are filled out.
	// -------------------------------------------------------------

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

	$err = 0;

	if($_POST['rating']=='0'){ echo "<script language=\"javascript\">\n alert('".lang('Please rate this product')."');\n </script>\n"; $err = 1; }
	if (strlen($title) < 2) { $err = 1; }
	if (strlen($message) < 5) { $err = 1; }
	if (strlen($name) < 2) { $err = 1; }

	if ($err == 0) {

		//MAKE SURE CART_COMMENTS TABLE EXITS
		$match = 0;
		$result = mysql_list_tables($_SESSION['db_name']);
		$i = 0;
		while ($i < mysql_num_rows ($result)) {
			$tb_names[$i] = mysql_tablename ($result, $i);
			if (strtolower($tb_names[$i]) == "cart_comments") { $match = 1; }
			$i++;
		}

		// DOES NOT EXIST; CREATE TABLE NOW
		## ====================================================
		if ($match != 1) {
			$qry = "CREATE TABLE cart_comments (";
			$qry .= " PRIKEY INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,";
			$qry .= " PROD_ID INT,";
			$qry .= " COMMENT_TITLE VARCHAR(255),";
			$qry .= " COMMENT BLOB,";
			$qry .= " RATING VARCHAR(255),";
			$qry .= " NAME VARCHAR(255),";
			$qry .= " LOCATION VARCHAR(255),";
			$qry .= " COMMENT_DATE DATETIME,";
			$qry .= " STATUS VARCHAR(255),";
			$qry .= " COMMENT_HTML BLOB,";
			$qry .= " AUTH_KEY VARCHAR(255)";
			$qry .= ")";
			//ECHO $qry."<br/>";
			if (!mysql_db_query($_SESSION['db_name'],$qry)){
				echo lang("Could not create table cart_comments")."!<br>";
				echo lang("Mysql says")." (".mysql_error().")";
				exit;
			}
		}

	    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	    // Build HTML Email to send to webmaster for this comment
	    // submission
	    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

			$headers = "";
			$headers .= "From: $name <shoppingcart@$SERVER_NAME>\r\n";
			$headers .= "Content-Type: text/html; charset=us-ascii; name=\"review.html\"\r\n";
			$headers .= "Content-Transfer-Encoding: 7bit\r\n";
			$headers .= "Content-Disposition: inline;\n filename=\"review.html\"\r\n";

	    $EMAIL_HEAD = "<HTML><HEAD>\n\n";

	    $EMAIL_HEAD .= "<STYLE>\n\n";
	    $EMAIL_HEAD .= "     .text {  font-family: Arial, Helvetica, sans-serif; font-size: 9pt}\n";
	    $EMAIL_HEAD .= "     .SMtext {  font-family: Arial, Helvetica, sans-serif; font-size: 8pt}\n";
	    $EMAIL_HEAD .= "</STYLE>\n\n";

	    $EMAIL_HEAD .= "<TITLE>$PROD[PROD_NAME]</TITLE>\n\n</HEAD>\n";
	    $EMAIL_HEAD .= "<BODY>\n\n";

	    ######################################################################################

	    $EMAIL_CONTENT .= "<FONT FACE=ARIAL><B>";

	    if ($rating != 0) {
	      $EMAIL_CONTENT .= "<B><FONT SIZE=3 COLOR=DARKBLUE>";
	      for ($z=1;$z<=$rating;$z++) {
	        $EMAIL_CONTENT .= "*";
	      }
	      $EMAIL_CONTENT .= "</B></FONT>&nbsp;&nbsp;";
	    }

	    $message = stripslashes($message);
	    $title = stripslashes($title);
	    $message = chop($message);
	    $message = rtrim($message);
	    $message = eregi_replace("\n", "<BR>", $message);

	    $EMAIL_CONTENT .= "<FONT SIZE=2><U>$title</U></b>&nbsp;&nbsp;<I><FONT SIZE=1> - $name ($location)</FONT></I>\n<BR>\n$message\n<BR><BR>\n";

	    #####################################################################################

	    $EMAIL_FOOT = "</BODY>\n</HTML>\n";

	    // --------------------------------------------------------------------
	    // Build a temporary file ID for this comment before making it public
	    // --------------------------------------------------------------------


	     $time = time();
	      for ($uu=0;$uu<=4;$uu++) {
	        srand((double)microtime()*1000000);
	        $tempVar = rand(0,9);
	        $time = "$tempVar$time";
	      }
	    $temp_id = $time;

	    // -----------------------------------------------------------------
	    // Use SENDMAIL to zip this puppy out
	    // -----------------------------------------------------------------

	    $to = $OPTIONS[BIZ_VERIFY_COMMENTS];
	    if ($to == "") { $to = "webmaster@$SERVER_NAME"; }

			$mtime = explode(" ", microtime());
    	$unique_key = $mtime['1'].'.'.str_replace('.', '', $mtime['0']).$unique_key;
	    $VERIFY_HTML = $EMAIL_HEAD;
	    $VERIFY_HTML .= "<center><h2>".lang("A customer has submitted the following comments about")."<BR>".lang("the product").": $PROD[PROD_NAME].</h2></center><div style='background: #EFEFEF; border: 1px black inset;'>\n";
	    $VERIFY_HTML .= "$EMAIL_CONTENT</div><BR><BR>\n";
	    $VERIFY_HTML .= "<A HREF=\"http://$this_ip/shopping/pgm-ok_comment.php?id=".$PROD['PRIKEY']."&key=".$unique_key."\">".lang("CLICK HERE")."</a> ".lang("TO MAKE THIS POST LIVE.")." (".lang("If you do not want to display this comment, simply delete this email").")\n";
	    $VERIFY_HTML .= $EMAIL_FOOT;


			$qry = "INSERT INTO cart_comments (";
			$qry .= " PROD_ID,";
			$qry .= " COMMENT_TITLE,";
			$qry .= " COMMENT,";
			$qry .= " RATING,";
			$qry .= " NAME,";
			$qry .= " LOCATION,";
			$qry .= " COMMENT_DATE,";
			$qry .= " STATUS,";
			$qry .= " COMMENT_HTML,";
			$qry .= " AUTH_KEY) ";
			$qry .= " VALUES('".$PROD['PRIKEY']."','".$_POST['title']."','".$_POST['message']."','".$_POST['rating']."','".$_POST['name']."','".$_POST['location']."', NOW(),'not_approved','".$EMAIL_CONTENT."', '".$unique_key."')";
			mysql_query($qry);

	    $to = eregi_replace('[^a-zA-Z0-9\.@]', '', stripslashes($to));
	    mail("$to", "Product Comment :: $PROD[PROD_NAME]", "$VERIFY_HTML", $headers) || DIE (lang("There is a problem with our email server."));

	    // -----------------------------------------------------------------
	    // Write temporary comment file to server
	    // -----------------------------------------------------------------

//	    $filename = "CART_".$temp_id.".".$PROD[PRIKEY];
//	    $file = fopen("$filename", "w");
//	      fwrite($file, "$EMAIL_CONTENT\n");
//	    fclose($file);

	    // ----------------------------------------------------------------

	    $THIS_DISPLAY = "<FONT COLOR=DARKBLUE><B>".lang("Thanks")." $name!<BR><BR>".lang("Your comment has been submitted.")."<BR><BR>";
	    $THIS_DISPLAY .= "<DIV ALIGN=LEFT STYLE='background: #EFEFEF; border: 1px black inset;'></B><FONT COLOR=BLACK SIZE=2>$EMAIL_CONTENT</FONT></DIV>\n";
	    $THIS_DISPLAY .= "<BR><BR></b>[ <A HREF=\"pgm-more_information.php?id=$PROD[PRIKEY]\">".lang("Click Here to Return to")." $PROD[PROD_NAME]</a> ]\n";

	} else {

	  $SEND = 0; // Set Send Flag to Zero so we can repeat the user input to get it right

	}

} // End SEND EQ 1

#################################################################################
### IF THE SEND FLAG IS NOT ACTIVE, THEN DISPLAY THE INITIAL EMAIL FORM
#################################################################################

if ($SEND != 1) {

	if ($title == "") { $title = ""; }

	$THIS_DISPLAY .= "<br/><br/><form name=\"EMAILSKU\" method=\"post\" action=\"pgm-write_review.php\">\n\n";

	$THIS_DISPLAY .= "<input type=\"hidden\" name=\"SEND\" value=1>\n";
	$THIS_DISPLAY .= "<input type=\"hidden\" name=\"id\" value=\"$id\">\n\n";

   $THIS_DISPLAY .= "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\" id=\"write_review_form\">\n";
	$THIS_DISPLAY .= " <tr>\n";
	$THIS_DISPLAY .= "  <th colspan=\"2\" bgcolor=\"".$OPTIONS['DISPLAY_HEADERBG']."\">\n";
	$THIS_DISPLAY .= "   <b><font face=\"verdana, Arial, Helvetica, sans-serif\" color=\"".$OPTIONS['DISPLAY_HEADERTXT']."\">".lang("Write Review For").": ".$PROD['PROD_NAME']."</font></b>\n";
	$THIS_DISPLAY .= "  </th>\n";
	$THIS_DISPLAY .= " </tr>\n";

   if ($err == 1) {
      $THIS_DISPLAY .= "<tr>\n";
      $THIS_DISPLAY .= " <td align=\"center\" valign=\"middle\" class=\"text\" colspan=\"2\">\n";
      $THIS_DISPLAY .= " <font color=\"RED\">".lang("You have left one or more fields blank.")."<br/>".lang("Please correct and re-submit your review.")."</td>\n";
      $THIS_DISPLAY .= "</tr>\n";
   }

   # Start Form Input Boxes
   $RATE_OPTIONS .= " <option value=\"0\"> --  </option>\n";
   $RATE_OPTIONS .= " <option value=\"1\">1 ".lang("Star")." </option>\n";
   $RATE_OPTIONS .= " <option value=\"2\">2 ".lang("Stars")." </option>\n";
   $RATE_OPTIONS .= " <option value=\"3\">3 ".lang("Stars")." </option>\n";
   $RATE_OPTIONS .= " <option value=\"4\">4 ".lang("Stars")." </option>\n";
   $RATE_OPTIONS .= " <option value=\"5\">5 ".lang("Stars")." </option>\n";

   $THIS_DISPLAY .= " <tr>\n";
   $THIS_DISPLAY .= "  <td align=\"right\" valign=\"middle\" class=\"text\">".lang("Rate this Product").": </td>\n";
   $THIS_DISPLAY .= "  <td align=\"left\" valign=\"middle\" class=\"text\"><select name=\"rating\" class=\"text\" style='width: 75px; color: darkblue;'>$RATE_OPTIONS</select><br/>\n";
   $THIS_DISPLAY .= "   <font size=\"1\">".lang("On a scale of 1-5, with 5 being the best")."</font></td>\n";
   $THIS_DISPLAY .= " </tr>\n";

   $THIS_DISPLAY .= "<tr>\n";
   $THIS_DISPLAY .= "<td align=\"right\" valign=\"middle\" class=\"text\">".lang("Comment Title").": </td>\n";
   $THIS_DISPLAY .= "<td align=\"left\" valign=\"middle\" class=\"text\"><input type=\"text\" size=\"35\" class=\"text\" name=\"title\" value='$title' style='width: 250px; color: darkblue;'></td>\n";

   $THIS_DISPLAY .= "</tr>\n";
   $THIS_DISPLAY .= "<tr>\n";

   $THIS_DISPLAY .= "<TD ALIGN=RIGHT VALIGN=TOP CLASS=text>".lang("Your Review/Comments").": </TD>\n";
   $THIS_DISPLAY .= "<TD ALIGN=LEFT VALIGN=MIDDLE CLASS=text><TEXTAREA ROWS=15 CLASS=text NAME=message STYLE='width: 250px;'>$message</TEXTAREA></TD>\n";

   $THIS_DISPLAY .= "</TR>\n";
   $THIS_DISPLAY .= "<TR>\n";

   $THIS_DISPLAY .= "<TD ALIGN=RIGHT VALIGN=MIDDLE CLASS=text>".lang("Your Name").": </TD>\n";
   $THIS_DISPLAY .= "<TD ALIGN=LEFT VALIGN=MIDDLE CLASS=text><INPUT TYPE=TEXT SIZE=35 CLASS=text NAME=name value='$name' STYLE='width: 250px; color: darkblue;'></TD>\n";

   $THIS_DISPLAY .= "</TR>\n";
   $THIS_DISPLAY .= "<TR>\n";

   $THIS_DISPLAY .= "<TD ALIGN=RIGHT VALIGN=TOP CLASS=text>".lang("Where are you in the world")."? </TD>\n";
   $THIS_DISPLAY .= "<TD ALIGN=LEFT VALIGN=TOP CLASS=text><INPUT TYPE=TEXT SIZE=35 CLASS=text NAME=location value='$location' STYLE='width: 250px; color: darkblue;'><BR>\n";
   $THIS_DISPLAY .= "<FONT COLOR=#708090 size=1>(ex: Atlanta, GA USA)</FONT></TD>\n";

   $THIS_DISPLAY .= "</TR>\n";
   $THIS_DISPLAY .= "<TR>\n";

   $THIS_DISPLAY .= "<TD ALIGN=CENTER VALIGN=MIDDLE CLASS=text COLSPAN=2>\n";
   $THIS_DISPLAY .= "&nbsp;</TD>\n";

   $THIS_DISPLAY .= "</TR>\n";
   $THIS_DISPLAY .= "<TR>\n";

   $THIS_DISPLAY .= "<TD ALIGN=CENTER VALIGN=MIDDLE CLASS=text COLSPAN=2>\n";
   $THIS_DISPLAY .= "<INPUT TYPE=SUBMIT VALUE=\"".lang("Submit Review")."\" CLASS=FormLt1></TD>\n";

   $THIS_DISPLAY .= "</TR>\n";
   $THIS_DISPLAY .= "<TR>\n";

   $THIS_DISPLAY .= "<TD ALIGN=CENTER VALIGN=MIDDLE CLASS=text COLSPAN=2>\n";
   $THIS_DISPLAY .= "<FONT COLOR=#708090><I>(".lang("Your review will be submitted to our staff and should be posted within 2-3 business days.")."  ".lang("Thank you").".)</I></FONT></TD>\n";

   $THIS_DISPLAY .= "</TR>\n";
   $THIS_DISPLAY .= "</TABLE>\n";

   // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	$THIS_DISPLAY .= "<CENTER><BR><BR></b><font face=Arial size=2>[ <A HREF=\"pgm-more_information.php?id=$PROD[PRIKEY]\">".lang("Click Here to Return to")." $PROD[PROD_NAME]</a> ]</CENTER>\n";

	$THIS_DISPLAY .= "</FORM>\n\n";

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

$FINAL_DISPLAY = "<TABLE BORDER=0 CELLPADDING=2 CELLSPACING=0 WIDTH=612 ALIGN=CENTER>\n";

$FINAL_DISPLAY .= "<TR>\n";

	if (eregi("L", $OPTIONS[DISPLAY_COLPLACEMENT] )) {

		$FINAL_DISPLAY .= "<TD WIDTH=150 ALIGN=CENTER VALIGN=TOP>\n\n$SEARCH_COLUMN\n\n</TD>\n<TD ALIGN=CENTER VALIGN=TOP>\n\n$THIS_DISPLAY\n\n</TD>\n";

	} else {

		$FINAL_DISPLAY .= "<TD ALIGN=CENTER VALIGN=TOP>\n\n$THIS_DISPLAY\n\n</TD>\n<TD WIDTH=150 ALIGN=CENTER VALIGN=TOP>\n\n$SEARCH_COLUMN\n\n</TD>\n";

	}

$FINAL_DISPLAY .= "</TR>\n\n";

$FINAL_DISPLAY .= "</TABLE>";


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

echo ("\n\n<SCRIPT language=Javascript>\n     window.focus();\n</SCRIPT>\n\n");

exit;

?>