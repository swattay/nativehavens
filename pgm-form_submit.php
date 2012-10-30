<?php
error_reporting(E_PARSE);
if($_GET['_SESSION'] != '' || $_POST['_SESSION'] != '' || $_COOKIE['_SESSION'] != '') { exit; }


################################################################################
## Soholaunch(R) Site Management Tool
## Version 4.6
##
## Author: 			Mike Johnston [mike.johnston@soholaunch.com]
## Homepage:	 	http://www.soholaunch.com
## Bug Reports: 	http://bugzilla.soholaunch.com
## Release Notes:	sohoadmin/build.dat.php
################################################################################

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
error_reporting(0);
track_vars;

##########################################################################
### WE WILL NEED TO KNOW THE DATABASE NAME; UN; PW; ETC TO OPERATE THE ###
### REAL-TIME EXECUTION.  THIS IS CONFIGURED IN THE isp.conf FILE      ###
##########################################################################

# Double-check session data (Maintis 412)
//if ( !isset($_SESSION['db_name']) && !isset($_SESSION['this_ip']) ) {
//   echo "Your session has expired. Please go back to the form page, refresh, and try again.";
//   exit;
//}

include("sohoadmin/program/includes/shared_functions.php");
include("pgm-site_config.php");

$globalprefObj = new userdata('global');
$formpref = new userdata('forms');

# Check referrer and session data

$refer1 = eregi_replace('http://','',$_SERVER['HTTP_REFERER']);
$refer = eregi_replace('www.','',$refer1);
$check_ip = eregi_replace("pgm-form_submit.php","",$this_ip);
$check_ip1 = eregi_replace("www.","",$check_ip);

//if ( !eregi($check_ip1,$refer) ) {
//   echo "This request to submit this form needs to be made by ".$check_ip1.".  Please contact the webmaster if this is an error.";
//   exit;
//}


if(count($_POST) < 1){ exit; }


if(count($_FILES) >= 1){
	$filesuploaded = '';
	foreach($_FILES['fileupload']['tmp_name'] as $filnum=>$fildat) {
		$filesuploaded .= $_FILES['fileupload']['name'][$filnum];
	}
	$_POST['files_uploaded'] = $filesuploaded;
}

$dot_com = $this_ip;

$REL12FIX = $RESPONSEFILE;			// Instant Fix after release
$ERROR_READ_FILE = $CUST_FILENAME;		// Leave this value untainted by processing

# Mantis 412: Uber-secure but not backwards-compatible
//if ( $_POST['UNIQUE_TOKEN'] == $_SESSION['UNIQUE_TOKEN'] ) {
//   echo "They match!<br>";
//   echo "_POST['UNIQUE_TOKEN']: [".$_POST['UNIQUE_TOKEN'].")<br>";
//   echo "_SESSION['UNIQUE_TOKEN']: [".$_SESSION['UNIQUE_TOKEN'].")<br>";
//   exit;
//} else {
//   echo "They DO NOT match!<br>";
//   echo "_POST['UNIQUE_TOKEN']: [".$_POST['UNIQUE_TOKEN'].")<br>";
//   echo "_SESSION['UNIQUE_TOKEN']: [".$_SESSION['UNIQUE_TOKEN'].")<br>";
//   exit;
//}


##########################################################################
### INSERT FUNCTION TO KILL ALL NON ALPHA/NUMERIC CHARACTERS FROM DATA
### FOR DATABASE STORAGE
##########################################################################

function sterilize_char ($sterile_var) {

	$sterile_var = stripslashes($sterile_var);
	$sterile_var = eregi_replace(";", ",", $sterile_var);
	$sterile_var = eregi_replace(" ", "_", $sterile_var);

	$st_l = strlen($sterile_var);
	$st_a = 0;
	$tmp = "";

	while($st_a != $st_l) {
		$temp = substr($sterile_var, $st_a, 1);
		if (eregi("[0-9a-z_]", $temp)) { $tmp .= $temp; }
		$st_a++;
	}

	$sterile_var = $tmp;
	return $sterile_var;

}

##########################################################################
### INSERT VALIDATE EMAIL FUNCTION (Bugzilla #26)
##########################################################################
function email_is_valid ($email) { // Allows for 4 char domains (Bugzilla #15)
   if (eregi("^[0-9a-z]([+-_.]?[0-9a-z])*@[0-9a-z]([-.]?[0-9a-z])*\\.[a-z]{2,4}$", $email, $check)) {
      return TRUE;
   }
   return FALSE;
}	// END VALIDATE EMAIL FUNCTION


##########################################################################
### SETUP KNOWN VARIABLE ARRAY; UNKNOWS ARE FORM GENERATED
##########################################################################

$SOHO_VAR = ";EMAILTO;PAGEREQUEST;DATABASE;PAGEGO;RESPONSEFROM;SUBJECTLINE;RESPONSEFILE;";
$SOHO_VAR .= "REQUIRED FIELDS;SELFCLOSE;CUST FILENAME;CUSTOMERNUMBER;UNIQUETOKEN;";

// ----------------------------------------------------------------------------------------------
// Expected Variable Listing and what they tell this process
// ----------------------------------------------------------------------------------------------
//
//   var emailto			Who (Site Owner) to email results of this form to (Email Address)
//   var pageRequest		Page from which this form was submitted
//   var database			Name of data table for this data to create (un parsed w/space, etc.)
//   var pagego				Page to redirect site visitor after form is processed
//   var RESPONSEFROM		Email address to send auto-email FROM
//   var subjectline		Subject Line of auto-email
//   var RESPONSEFILE		Text file containing content of auto-email (DEFAULT2020202024452345.TXT)
//   var required_fields	array (; delimited) containing required field names from form
//   var selfclose			if (yes) send javascript close window command and exit;
//   var cust_filename      filename of form file
//   var customernumber     system assigned customer id
//   var UNIQUETOKEN       uber-secure form validation method for Mantis #412 for future utilization
//
//   THE FOLLOWING VARS MUST BE THE "NAME" OF THE INPUT FIELDS IN THE FORM FOR THE PROPER
//   PROCESSING TO WORK:
//
//   var emailaddr			Customers Email Address to send confirmation email
// -------------------------------------------------------------------------------------------------

//echo "file name -->(".$CUST_FILENAME.")<br><br>";
$PAGEREQUEST = eregi_replace(' ', '_', $PAGEREQUEST);
$filename = "$cgi_bin/".$PAGEREQUEST.".con";

chdir($cgi_bin);

$handle = fopen( $filename, "r");
if ( $handle ){
   while (!feof($handle)){
      $buffer = fgets($handle, 4096);
      $daForm .= $buffer;
      }
   fclose($handle);
}else{
   die("fopen failed for $filename. You may be pointing your form to a page that does not exist.");
}

$tOutput = explode("name=EMAILTO value=\"", $daForm);

$firstForm = $tOutput[1];
$secondForm = $tOutput[2];
//Mikes new form placement fix/ added by cameron
if($tOutput['1'] == ''){
	$tOutput = explode("Email To: ", $daForm);
	$firstForm = $tOutput[1];
	$secondForm = $tOutput[2];
	if(!eregi($CUST_FILENAME,$secondForm)){
	   $tOutput = explode("</font>", $firstForm);
		$tmpFinal = $tOutput[0];
	}else{
	   $tOutput = explode("</font>",$secondForm);
		$tmpFinal = $tOutput[0];
	}

} else {
	if(!eregi($CUST_FILENAME,$secondForm)){
	   $tOutput = explode("\"\>", $firstForm);
		$tmpFinal = "1".$tOutput[0];
	}else{
	   $tOutput = explode("\">", $secondForm);
		$tmpFinal = "2".$tOutput[0];
	}
}



######################################################################################################
### ONE: FORMAT ALL PASSED VARIABLES FOR DATA MANIPULATION
######################################################################################################

# Clear hackable vars
$EMAILTO = "";
$EMAILADDR = "";
$spamflagBool = false;

foreach ( $_POST as $name=>$value ) {

   # Convert array to list
   if ( is_array($value) ) {
      $value = implode(", ", $value);
   }

	$value = stripslashes($value);
	//$value = strtolower($value);
	$value = eregi_replace("\n", " ", $value); 	// Windows Line Feed Replaced with a Space
	$value = eregi_replace("\r", "", $value);	// Unix Line Feed

	$name = stripslashes($name);
	$name = strtoupper($name);
	$name = sterilize_char($name);

	$value = htmlspecialchars($value);		// Make sure no HTML code is sent to form processor : bugzilla #13
	
	if ( $formpref->get('block-links') == 'on' && eregi('http.*http', $value) ) {
		$spamflagBool = true;
	}

	${$name} = $value;						// Place passed values into NEW all UpperCase Var Names

	// echo "$name = ${$name};<BR>";
}

# Spammer rejection message goes here
if ( $spamflagBool == true ) {
     echo '<div style="width: 500px;background: #efefef;font: 12px Trebuchet MS, verdana, arial, sans-serif;padding: 15px;position: absolute; left:30%; top: 40%; border: 1px dotted red;">'."\n";
     echo $formpref->get('spam-trap-message');
     echo "&nbsp;&nbsp;&nbsp;\n<a href=\"#\" onClick=\"history.go(-1)\">".lang('Return to Previous Page')."</a>\n";
     echo '</div>'."\n";
     exit;
}
$sendto_email_orig = $EMAILTO;

# Constrict EMAILTO and EMAILADDR to 40 chars - Mantis 412
$sendto_email = str_replace(" ", "", $EMAILTO);
$sendto_email = split(",", $sendto_email);
$good_emailto = "";

# Limit to one email address for now (10 later?)
for ( $e = 0; $e < 10; $e++ ) {
   if ( strlen($sendto_email[$e]) < 50 ) {
      $good_emailto .= $sendto_email[$e];
   }
}

$EMAILTO = $good_emailto;

# Limit visitor email to 1 email address
$EMAILADDR = eregi_replace(",.*", "", $EMAILADDR);



######################################################################################################
### TWO: CHECK FOR REQUIRED FIELDS FIRST THING
######################################################################################################

// -------- Added fix for Bugzilla #26 ---------------------
$BUGZILLA26 = 0;
if ($EMAILADDR != "" && !email_is_valid($EMAILADDR)) {
	$BUGZILLA26 = 1;
	$EMAILADDR = "";
	if (!eregi("emailaddr;", $REQUIRED_FIELDS)) { $REQUIRED_FIELDS .= ";emailaddr"; }
}

if ($EMAIL_ADDRESS != "" && !email_is_valid($EMAIL_ADDRESS)) {	// Newsletter Sign-Up Form
	$BUGZILLA26 = 1;
	$EMAIL_ADDRESS = "";
	if (!eregi("EMAIL_ADDRESS;", $REQUIRED_FIELDS)) { $REQUIRED_FIELDS .= ";EMAIL_ADDRESS"; }
}

// ---------------------------------------------------------

if ($REQUIRED_FIELDS != "") {				// Script calls for required field data; check it now

	$REQUIRED_FIELDS = eregi_replace(" ", "_", $REQUIRED_FIELDS);

	// We already checked to make sure required file fields were populated in camcheckrequired.
	// Remove required file check.
	// BUZ #704
	$REQUIRED_FIELDS = str_replace("fileupload[];", "", $REQUIRED_FIELDS);

	$r_fields = split(";", $REQUIRED_FIELDS);
	$r_count = count($r_fields) - 1;
	$err_field = "";

	$i=0;
	$err=0;

	while($i <= $r_count) {

		$r_fields[$i] = strtoupper($r_fields[$i]);	// We changed all passed vars to upper case

		if ( $r_fields[$i] != "" && ${$r_fields[$i]} == "" ) {
			$err_field .= strtolower($r_fields[$i]).", ";
			$err = 1;
		}

		$i++;
	}

	if ($err == 1) {

		$tmp = strlen($err_field);
		$new = $tmp - 2;
		$err_field = substr($err_field, 0, $new);
		//$err_field = ucwords($err_field);
		$err_field = eregi_replace(", ", ",", $err_field);

		$err_field_data = split(",", $err_field);
		$err_count = count($err_field_data);

		// -------------------------------------------
		// 1. Open Form File and Read into Memory
		// -------------------------------------------

		$filename = $ERROR_READ_FILE;
		$fp = fopen("$filename", "r");
			$FORM_CONTENT = fread($fp,filesize($filename));
		fclose($fp);

		// --------------------------------------------
		// 2. Split the data up into managable parts
		// --------------------------------------------

		$work_html = $FORM_CONTENT;
		$work_html = eregi_replace(">", ">\n", $work_html);		// Make sure that all image calls are on a single line by themselves
		$work_html = eregi_replace("<", "\n<", $work_html);

      # Make sure <select> elements are on same line with all their options (v4.9.2 r14 bugfix: dropdowns are empty on required field missing page)
		$work_html = eregi_replace("\n<option ([A-Za-z_ =\"]*)>\n([A-Za-z_ =\"]*)\n</option>\n", "<option \\1>\\2</option>", $work_html);
		$work_html = eregi_replace("\n<option", "<option", $work_html);
		$work_html = eregi_replace("\n</select>", "</select>", $work_html);

//		# For testing formatted $work_html
//		echo "<div style=\"width: 800px;height: 400px;border: 1px dashed #ccc;overflow: auto;\">".$work_html."</div>\n";

		$html_line = split("\n", $work_html);
		$lc = count($html_line);

		$NEW_FORM_DATA = "";

		for ($x=0;$x<=$lc;$x++) {	// Start loop thru each html line

			$reset = 0;

			for ($z=0;$z<=$err_count;$z++) {	// Cycle through all error fields

			$alt_data_format = eregi_replace("_", " ", $err_field_data[$z]);	// In case custom form intended on having _ between field names

			if (strlen($err_field_data[$z]) > 1) { // Added for V4.6 -- > Stop wierd instance of "blank" required fields
					if (eregi("\"$err_field_data[$z]\"", $html_line[$x]) || eregi("\"$alt_data_format\"", $html_line[$x])) { 	// This line contains the input box for the req field missed

						if ($reset == 0) {

							$d = eregi_replace("_", " ", $err_field_data[$z]);
							//$d = ucwords($d);
							if ($d == "Emailaddr") { $d = lang("Email Address"); }

							$NEW_FORM_DATA .= "<TR><TD ALIGN=RIGHT VALIGN=TOP><FONT FACE=Verdana SIZE=2 COLOR=BLACK><B>$d:</B></TD>\n";

							if (eregi("TEXTAREA", $html_line[$x])) {	// Fix Text Area Screw up -- Thanks Kenny H!
								$NEW_FORM_DATA .= "<TD ALIGN=LEFT VALIGN=TOP>$html_line[$x]</TEXTAREA></TD></TR>\n";
							} else {
								$NEW_FORM_DATA .= "<TD ALIGN=LEFT VALIGN=TOP>$html_line[$x]</TD></TR>\n";
							}

							$reset = 1;

						} // End if Reset

					} // End if input line is found

			} // End if strlen is approved

			} // End Loop Through Each Err Field

		} // End Each Line Loop

		echo "<HTML><HEAD>\n";
		echo "<TITLE>".lang("Form Input Error")."</TITLE></HEAD>\n";
		echo "<BODY BGCOLOR=#ffffff TEXT=#000000>\n\n";

			echo "<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0 HEIGHT=100% WIDTH=100%><TR><TD ALIGN=CENTER VALIGN=MIDDLE>\n";

			echo "\n\n<FORM METHOD=POST ACTION=\"pgm-form_submit.php\">\n\n";

			reset($HTTP_POST_VARS);
			while (list($name, $value) = each($HTTP_POST_VARS)) {
				$value = stripslashes($value);
				echo "     <INPUT TYPE=HIDDEN NAME=\"$name\" VALUE=\"$value\">\n";
			}

			echo "\n<TABLE BORDER=0 CELLPADDING=10 CELLSPACING=0 BGCOLOR=WHITE ALIGN=CENTER WIDTH=450 STYLE='border: 5px inset black;'>\n";
			echo "<TR><TD ALIGN=CENTER VALIGN=MIDDLE COLSPAN=2><FONT COLOR=RED FACE=VERDANA SIZE=2>";
			echo "<B>";

			if ($BUGZILLA26 == 1) {
				echo lang("The email address you entered is invalid or")." ";
			}

			echo lang("You left a required field or fields blank.")."  ".lang("Please enter the following data before continuing").":</B></FONT></TD></TR>\n";
			echo $NEW_FORM_DATA;
			echo "<TR><TD ALIGN=CENTER VALIGN=MIDDLE COLSPAN=2><INPUT TYPE=SUBMIT VALUE=\"Re-Submit\" STYLE='cursor: hand; font-family: Arial; font-size: 8pt;'></TD></TR>\n";
			echo "</TABLE>\n\n</FORM>\n\n";

			echo "</TD></TR></TABLE>\n\n";

		echo "</BODY></HTML>\n";

		exit;

	} // End Err = 1

} // End Req Field Check

#######################################################################################################
####### THREE: SETUP EMAIL PROCESS TO WEB OWNER IF REQUESTED						 ######
#######################################################################################################

if ($EMAILTO != "" && !eregi("NEWSLETTER_SIGNUP_PROCESS", $EMAILTO)) {				// Setup send to site owner
   //echo "Sending email to site owner!<br>";
   //exit;

		$soho_email = "********* ".lang("Auto Generated Form Email")." **********\n\n";

		reset($_POST);
		while (list($name, $value) = each($_POST)) {

         # Convert array to list
         if ( is_array($value) ) {
            $value = implode(", ", $value);
         }

			$value = stripslashes($value);
			//$value = strtolower($value);
			$value = eregi_replace("\n", "", $value); // Windows Line Feed
			$value = eregi_replace("\r", "", $value);	// Unix Line Feed
			if (!eregi("emailaddr", $name) && !eregi("EMAILTO", $name) && !eregi("RESPONSEFROM", $name)){
				$value = eregi_replace("_", " ", $value);	// Replace underscores with spaces Bug #0000619
			}
			///echo __LINE__." ".$EMAILTO." ".$val." ".$EMAILADDR.' '.$visitor_email." ".$emailfrom." ".$RESPONSEFROM." ".$soho_email." ".$name.'='.$value."<br><br>";
			//$value = ucwords($value);

			$name = stripslashes($name);

			if ( $globalprefObj->get('utf8') != 'on' ) {
				$name = sterilize_char($name);
			}
			$name = eregi_replace("_", " ", $name);

			if (!eregi(";$name;", $SOHO_VAR)) {

				// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
				// Replace any required field names that appear in form with proper
				// "reading" format
				// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

				if (eregi("emailaddr", $name)) { $name = lang("Email Address"); $value = strtolower($value); $visitor_email = strtolower($value); }

				// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

				$soho_email .= "> $name: $value\n";
			}

		} // End While Loop

		$tmp = split("/", $CUST_FILENAME);
		$tmp_cnt = count($tmp) - 1;
		$form_name = $tmp[$tmp_cnt];

		$soho_sub_page = ucwords($PAGEREQUEST);

		$soho_email .= lang("This message is auto-generated by your web site ")."(".$_SESSION['this_ip'].") ".lang("when the ")." ";
		$soho_email .= lang("form is submitted by a site visitor on page")." \"$soho_sub_page\". ".lang("No need to reply").".\n";

		if ($DATABASE != "") {

			$DATABASE = sterilize_char($DATABASE);
			$tmp = split("/", $DATABASE);
			$tmp_cnt = count($tmp) - 1;
			$tname = $tmp[$tmp_cnt];
			$tname = strtoupper($tname);		// All UDT are to be built in upper case only

			$soho_email .= "\nFYI:\n".lang("This data has been saved to the")." \"$tname\" ".lang("database table").".";

		}

		if (!eregi("default2020202024452345", $RESPONSEFILE)) {
		   $resp_file_name = eregi_replace($_SESSION['doc_root'], "", $RESPONSEFILE);
			$soho_email .= "\n".lang("Your site visitor received the custom response file")." \"".$resp_file_name."\".";
		}

		$soho_email .= "\n\n\n\n";

		if ($SUBJECTLINE == "") { $SUBJECTLINE = "".lang("Website Form Submission").""; }
		//$SUBJECTLINE = ucwords($SUBJECTLINE);



      # Constrict EMAILTO and EMAILADDR to 40 chars - Mantis 412
      $sendto_email = str_replace(" ", "", $sendto_email_orig);
      $sendto_email = split(",", $sendto_email);
      $good_emailto = "";
      $good_emailto_admin = "";

      # Limit to one email address for now (10 later?)
      for ( $e = 0; $e < 10; $e++ ) {
         if ( strlen($sendto_email[$e]) < 50 ) {
            $good_emailto .= $sendto_email[$e];
            $good_emailto_admin[$e] = $sendto_email[$e];
         }
      }

      $EMAILTO_ADMIN = $good_emailto_admin;
      $EMAILTO = $good_emailto;

      # Limit visitor email to 1 email address
      $EMAILADDR = eregi_replace(",.*", "", $EMAILADDR);

//      $EMAILTO = $tmpFinal;
      //echo "sending to (".$EMAILTO.")<br>";
      //exit;



##//////////////////////////////////////////////////////////////////////////////////////////////////////////

	if ( count($_FILES) > 0 ) {

	//	echo testArray($_POST); exit;
		include_once($_SESSION['doc_root'].'/sohoadmin/program/includes/class-send_file.php');

	   // NEED TO ADD LOOP FOR MULTIPLE ADMIN EMAIL
	   // LIKE FOREACH MAIL SEND BELOW

		//$SUBJECTLINE = "".lang("Website Form Submission")."";
		$test = new attach_mailer($name = "", $from = "$RESPONSEFROM", $to = "$EMAILTO", $cc = "", $bcc = "", $subject = "".lang("Website Form Submission")."");

		foreach($_FILES['fileupload']['tmp_name'] as $filnum=>$fildat) {

			if(!eregi('\.con$', $_FILES['fileupload']['name'][$filnum]) && !eregi('\.regen$', $_FILES['fileupload']['name'][$filnum]) && !eregi('\.htaccess', $_FILES['fileupload']['name'][$filnum])&& !eregi('\.\.', $_FILES['fileupload']['name'][$filnum])){
				if (move_uploaded_file($_FILES['fileupload']['tmp_name'][$filnum], $_SESSION['doc_root'].'/sohoadmin/filebin/'.$_FILES['fileupload']['name'][$filnum])) {
					if(file_exists($_SESSION['doc_root'].'/sohoadmin/filebin/'.$_FILES['fileupload']['name'][$filnum])) {
						//file uploaded

						if(eregi('*.\.gif$', $_FILES['fileupload']['name'][$filnum]) || eregi('*.\.jpg$', $_FILES['fileupload']['name'][$filnum]) || eregi('*.\.jpeg$', $_FILES['fileupload']['name'][$filnum]) || eregi('*.\.png$', $_FILES['fileupload']['name'][$filnum]) || eregi('*.\.bmp$', $_FILES['fileupload']['name'][$filnum])) {
							$test->add_html_image($_SESSION['doc_root'].'/sohoadmin/filebin/'.$_FILES['fileupload']['name'][$filnum]);
						} else {
							$SLASH = DIRECTORY_SEPARATOR;
							$zipped_file = eregi_replace('\.[^\.]*$', '.zip', $_FILES['fileupload']['name'][$filnum]);
							$b4zip = getcwd();
							chdir('../filebin');
							if(eregi('WIN', PHP_OS)){
								$zippy = $_SESSION['doc_root'].$SLASH."sohoadmin".$SLASH."program".$SLASH."includes".$SLASH."untar".$SLASH."zip.exe";
								exec($zippy." ".$zipped_file." ".$_FILES['fileupload']['name'][$filnum]);
							} else {
								exec("zip ".$zipped_file." ".$_FILES['fileupload']['name'][$filnum]);
							}
							chdir($b4zip);
							if(file_exists($_SESSION['doc_root'].$SLASH."sohoadmin".$SLASH."filebin".$SLASH.$zipped_file)){
								$test->add_attach_file($_SESSION['doc_root'].$SLASH."sohoadmin".$SLASH."filebin".$SLASH.$zipped_file);
								unlink($_SESSION['doc_root'].$SLASH."sohoadmin".$SLASH."filebin".$SLASH.$zipped_file);
							} else {
								$test->add_attach_file($_SESSION['doc_root'].'/sohoadmin/filebin/'.$_FILES['fileupload']['name'][$filnum]);
							}
						unlink($_SESSION['doc_root'].$SLASH."sohoadmin".$SLASH."filebin".$SLASH.$_FILES['fileupload']['name'][$filnum]);
						}
					}
				}
			}
		}

		$test->html_body = "<html><pre>$soho_email</pre></html>";
		$test->text_body = strip_tags($test->html_body, "<a>");
		if($test->process_mail() == true ) {
			foreach($_FILES['fileupload']['tmp_name'] as $filnum=>$fildat) {
				if(!eregi('\.con$', $_FILES['fileupload']['name'][$filnum]) && !eregi('\.regen$', $_FILES['fileupload']['name'][$filnum]) && !eregi('\.htaccess', $_FILES['fileupload']['name'][$filnum])&& !eregi('\.\.', $_FILES['fileupload']['name'][$filnum])){
					if(file_exists($_SESSION['doc_root'].'/sohoadmin/filebin/'.$_FILES['fileupload']['name'][$filnum])) {
						unlink($_SESSION['doc_root'].'/sohoadmin/filebin/'.$_FILES['fileupload']['name'][$filnum]);
					}
				}
			}
		} else {

			$subject = "".lang("Website Form Submission")."";
			$bodytext = "$soho_email"."\n".lang("There was an error mailing the uploaded file").". \n".lang("Error").":".$test->msg['0']."";

			if ( $visitor_email != "" ) {
				$emailfrom = $visitor_email;
				$headers  = "From: $emailfrom" . "\r\n";
				$headers .= "Content-type: text/html; charset=utf-8" . "\r\n";
				$headers .= "MIME-Version: 1.0" . "\r\n";

			} else {
				$emailfrom = $RESPONSEFROM;
				$headers  = "From: $emailfrom" . "\r\n";
				$headers .= "Content-type: text/html; charset=utf-8" . "\r\n";
				$headers .= "MIME-Version: 1.0" . "\r\n";
			}
			//			mail("$EMAILTO", "$SUBJECTLINE", $soho_email, "FROM: ".$emailfrom) || DIE("Not able to send email.");

			foreach($EMAILTO_ADMIN as $var=>$val){
				if(strlen($val)>5){
					mail("$val", "$subject", $bodytext, $headers);
				}
			}
		}
	} else {
		##//////////////////////////////////////////////////////////////////////////////
		# Show email as from visitor's email if possible

		if ( $visitor_email != "" ) { $emailfrom = $visitor_email; } else { $emailfrom = $RESPONSEFROM; }
		//			mail("$EMAILTO", "$SUBJECTLINE", $soho_email, "FROM: ".$emailfrom) || DIE("Not able to send email.");
		foreach($EMAILTO_ADMIN as $var=>$val){
			if(strlen($val)>5){
				mail("$val", "$SUBJECTLINE", $soho_email, $headers);
			}
		}
	}
} // End Send Email to Site Owner


#######################################################################################################
####### FOUR: SETUP AUTO-EMAIL CONFIRMATION TO SITE VISITOR (IF NECESSARY)				 ######
######################################################################################################

if (($EMAILADDR != "" || $EMAIL_ADDRESS != "") && !eregi("NEWSLETTER_SIGNUP_PROCESS", $EMAILTO)) {				// Setup send to visitor

	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	// Build "custom file" display format for the auto-email.  This way,
	// if we do utilize a custom response to over-ride this one, we still
	// use the "search & replace variable" method for both responses.
	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	$soho_email = lang("Thank you for your form submission today! This email is to confirm the reception")." ";
	$soho_email .= lang("of your recently submitted data.")."\n\n".lang("We received the following:")." \n\n";

	reset($HTTP_POST_VARS);
	while (list($name, $value) = each($HTTP_POST_VARS)) {

      # Convert array to list
      if ( is_array($value) ) {
         $value = implode(", ", $value);
      }

		$name = stripslashes($name);
		//$name = strtolower($name);
		$name = sterilize_char($name);
		//$name = ucwords($name);
		$name = eregi_replace("_", " ", $name);

		if (!eregi(";$name;", $SOHO_VAR) && $name!='capval' && $name!='cap') {
			$soho_email .= $name.": [".$value."]\n";
		}

	} // End While Loop

	$soho_email .= lang("Thank You")."!\n\n ** ".lang("This message is auto-generated by our web site.")." ";
	$soho_email .= lang("Please do not reply to this email.")." **\n";

	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	// Do we wish to replace the default "soho_email" with a custom
	// response file? If so, replace current $soho_email with txt (html)
	// from the custom response file specified.
	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	if (!eregi("default2020202024452345.txt", $RESPONSEFILE)) {

		$this_filename = eregi_replace("$doc_root/", "", $REL12FIX);	// Bombing on custom include .txt files (fixed 2002-10-07)

		$this_filename = ltrim($this_filename);
		$this_filename = rtrim($this_filename);

		$file = fopen("$RESPONSEFILE", "r");
			$soho_email = fread($file,filesize($RESPONSEFILE));
		fclose($file);

	} // End Response File

	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	// Let's fill in the "value" data for the $soho_email variable since
	// we have now set what the variable will be.  We will replace
	// instances of [FIELDNAME] with the value of that fieldname.
	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	reset($HTTP_POST_VARS);
	while (list($name, $value) = each($HTTP_POST_VARS)) {

      # Convert array to list
      if ( is_array($value) ) {
         $value = implode(", ", $value);
      }

		$value = stripslashes($value);
		//$value = strtolower($value);
		$value = eregi_replace("\n", "", $value); // Windows Line Feed
		$value = eregi_replace("\r", "", $value);	// Unix Line Feed
		if (!eregi("emailaddr", $name) && !eregi("EMAILTO", $name) && !eregi("RESPONSEFROM", $name) && !eregi("EMAIL_ADDRESS", $name)){
			$value = eregi_replace("_", " ", $value);	// Replace underscores with spaces Bug #0000619
		}
		//$value = ucwords($value);

		$name = stripslashes($name);
		//$name = strtolower($name);
		if ( $globalprefObj->get('utf8') != 'on' ) {
			$name = sterilize_char($name);
		//$name = ucwords($name);
			$name = eregi_replace("_", " ", $name);
		}

		if (eregi("emailaddr", $name)) { $value = strtolower($value); }

		$soho_email = eregi_replace("\[$name\]", $value, $soho_email);		// This part should be case sensitive!

	} // End While Loop

	if($EMAILADDR == '' && $EMAIL_ADDRESS != ''){
		$soho_email = eregi_replace("EMAIL_ADDRESS", lang("Email Address"), $soho_email); 	// Modify Display of EmailAddr req field
	} else {
		$soho_email = eregi_replace("EMAILADDR", lang("Email Address"), $soho_email); 	// Modify Display of EmailAddr req field
	}
	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	// Now send auto-email to site visitor
	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	if ($SUBJECTLINE == "") { $SUBJECTLINE = "Website Form Submission"; }
	if ($EMAILTO == "") { $EMAILTO = "$dot_com <webmaster@$dot_com>"; }

	//$SUBJECTLINE = ucwords($SUBJECTLINE);

   # Constrict EMAILTO and EMAILADDR to 40 chars - Mantis 412
   $sendto_email = str_replace(" ", "", $EMAILTO);
   $sendto_email = split(",", $sendto_email);
   $good_emailto = "";

   # Limit to one email address for now (10 later?)
   for ( $e = 0; $e < 10; $e++ ) {
      if ( strlen($sendto_email[$e]) < 50 ) {
         $good_emailto .= $sendto_email[$e];
      }
   }

   $EMAILTO = $good_emailto;

		if($EMAILADDR == ''){
			$EMAILADDR = $EMAIL_ADDRESS;
		}

   # Limit visitor email to 1 email address
   $EMAILADDR = eregi_replace(",.*", "", $EMAILADDR);




	if (email_is_valid($EMAILADDR)) {
		mail("$EMAILADDR", "$SUBJECTLINE", "$soho_email", "FROM: $RESPONSEFROM") || Die (lang("Not able to send client email"));
	} // Only send if valid email address is returned (Bugzilla #26)







} // End auto-email IF

#######################################################################################################
####### FIVE: SETUP DATABASE PROCESS IF REQUESTED								 ######
#######################################################################################################

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
// This function addresses Bugzilla #27 which in iteself is trivial, however
// the age verification routine was never in original release for some
// reason.  Let's make it so now; but only for newsletter signup forms.
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if (eregi("NEWSLETTER_SIGNUP_PROCESS", $EMAILTO)) {
	$thisYear = date("Y");
	if ($Year_Born < 1900) { $Year_Born = $thisYear; }	// No one this freakin' old is using the Internet I assure you
	$verifyAge = $thisYear - $Year_Born;
	if ($verifyAge < 14) { $DATABASE = "";	}
} // End if this is Newsletter Form

if (strlen($DATABASE) > 3) {			// Require at least a three char database name (some people use spaces, etc.)

	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	// Build UDT name string for this database table.  All form submissions
	// force the creation of a UDT_ table so that users can easily manipulate
	// the data within the "Database Table Manager".
	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	$TABLE_NAME = sterilize_char($DATABASE);
	$TABLE_NAME = strtoupper($TABLE_NAME);
	$TABLE_NAME = "UDT_".$TABLE_NAME;

	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	// Does this user data table already exist on the server?
	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	$tbl_exist = 0;	// Set flag to NO on start of this function

	$result = mysql_list_tables("$db_name");

	$i = 0;

	while ($i < mysql_num_rows ($result)) {
		$tb_names[$i] = mysql_tablename($result, $i);
		if ($tb_names[$i] == $TABLE_NAME) { $tbl_exist = 1; }
		$i++;
	}

	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	// Process Data (A) : Table does NOT exist.  Create it giving it an auto
	// PriKey Field and field names that correspond to the UPPERCASE of variable
	// names passed to this script.
	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	if ($tbl_exist == 0) {
	   //echo "about to create table ($TABLE_NAME)!"; exit;     //testing to see if table gets created

		$SQL_CREATE = "CREATE TABLE $TABLE_NAME (PRIKEY INT NOT NULL AUTO_INCREMENT PRIMARY KEY, ";

		reset($_POST);
		while (list($name, $value) = each($_POST)) {

			$name = stripslashes($name);
			//$name = strtolower($name);
			if ( $globalprefObj->get('utf8') != 'on' ) {
				$name = sterilize_char($name);
				$name = strtoupper($name);
			}

			$tmp_chk = eregi_replace(" ", "_", $SOHO_VAR);	// Replace spaces with underscores for form names

			if (!eregi(";$name;", $tmp_chk)) {

				$SQL_CREATE .= "$name BLOB, ";			// Create all fields as CHAR(255) by default.
															// You can change this in the "Database Table Manager"
			}

		} // End While Loop

		$SQL_CREATE .= "AUTO_IMAGE CHAR(100), AUTO_SECURITY_AUTH CHAR(255))";				// Make sure we add the auto_image field to UDT

		// $tmp = strlen($SQL_CREATE);
		// $new = $tmp - 2;
		// $SQL_CREATE = substr($SQL_CREATE, 0, $new);
		// $SQL_CREATE .= ")";


		mysql_query("$SQL_CREATE");

		sleep(1);		// Wait for mySQL, sometimes she's a bear about creating!

	} // End Create Table Function

	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	// Process Data (B) : Now we are sure that the user data table exists. Let's
	// populate a record with the new submitted data.  Anyone can just create
	// a table from a form and populate it with the data, but we decided to go
	// one step further.  We decided to do a Fonzy.  You know what Fonzy's like
	// don't you? That's right, he's cool.  So now we're going to be real cool.
	//
	// We are going to cross reference the "existing" field names with the named
	// values passed to us from the form.
	//
	// Theoretically they should be the same and in the same order even -- in
	// past versions we counted on that or at minimum required it.  What if a
	// custom form is being used and your client decides he wants to add one
	// more field to the form to get a visitors shoe size or something.
	// TRUST ME, they always want just one more thing!
	//
	// Once we cross reference and match form names to field names, we will take
	// any "left-over" or remaining form names and simply ADD NEW FIELD NAMES
	// to the data table before we do the INSERT.  This insures that we can
	// change the form as many times as we like without ever having to screw
	// around with the table structure manually.
	//
	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~


		// ----------------------------------------------------------
		// FONZY STEP 1: BUILD TEMP HOUSING FOR "FORM NAMES"
		// ----------------------------------------------------------

		reset($_POST);
		$i=0;
		while (list($name, $value) = each($_POST)) {
		$value = mb_convert_encoding($value, 'HTML-ENTITIES', 'UTF-8');
		$name = mb_convert_encoding($name, 'HTML-ENTITIES', 'UTF-8');
			$name = stripslashes($name);
			//$name = strtolower($name);
			if ( $globalprefObj->get('utf8') != 'on' ) {
				$name = sterilize_char($name);
				$name = strtoupper($name);
			}

			$tmp_chk = eregi_replace(" ", "_", $SOHO_VAR);

			if (!eregi(";$name;", $tmp_chk)) {
				$i++;
				$PASSED_FORM_NAMES[$i] = $name;
			}

		} // End While Loop

		$NUM_FORM_NAMES = $i;

		// ----------------------------------------------------------
		// FONZY STEP 2: READ THE CURRENT FIELD NAMES FROM THIS TABLE
		// ----------------------------------------------------------

		$result = mysql_query("SELECT * FROM $TABLE_NAME");
		$numberFields = mysql_num_fields($result);
		$numberFields--;

		$CURRENT_FIELD_NAMES = "";

		for ($i=0;$i<=$numberFields;$i++) {
			$tmp = mysql_field_name($result, $i);
			$CURRENT_FIELD_NAMES .= strtoupper($tmp).";";		// Total Fonzy: What if this table was created manually via table manager? Format!
		}

		$NUM_FIELD_NAMES = $i;		// Get total existing fields

		// ----------------------------------------------------------
		// FONZY STEP 3: IF NUM_FIELD_NAMES == NUM_FORM_NAMES WE SHOULD
		// BE OK RIGHT? WRONG! WHAT IF WE SIMPLY RENAMED A FIELD IN THE
		// FORM BECAUSE OF A MISPELLED WORD.  KEEP GOING SIMPLE SIMON...
		// ----------------------------------------------------------

		$NEW_FIELDS = "";						// We hope this stays clear throught the next function

		$i = 0;
		while ($i <= $NUM_FORM_NAMES) {

			$found = 0;

			if (eregi("$PASSED_FORM_NAMES[$i];", $CURRENT_FIELD_NAMES)) {
				$found = 1;	// This Form Name Exists in Table
			} else {
				$NEW_FIELDS .= "$PASSED_FORM_NAMES[$i];";
			}

			$i++;

		} // End While Loop


		// ----------------------------------------------------------
		// FONZY STEP 4: THE MOMENT OF TRUTH.  IF THE FORM HAS NOT
		// BEEN TOUCHED SINCE THE LAST INSERT THE VAR $NEW_FIELDS
		// SHOULD NOT CONTAIN ANYTHING.  IF IT DOES CONTAIN SOMETHING
		// WE NEED TO SPLIT THE VALUE BY ; AND ADD THOSE NEW FIELDS.
		//
		// IT'S NOT IMPORTANT IF THEY ARE USING LESS FIELDS, THAN
		// BEFORE, WE ARE GOING TO MAKE A MOVE THAT COMPLETES THE
		// WHOLE FONZY CONCEPT WHEN WE INSERT THIS THING.
		// ----------------------------------------------------------

		if ($NEW_FIELDS != "") {		// We need to add more fields to the table

			$tmp = split(";", $NEW_FIELDS);
			$tmp_cnt = count($tmp) - 2;
//			echo testArray($tmp);

			for ($x=0;$x<=$tmp_cnt;$x++) {
				mysql_query("ALTER TABLE $TABLE_NAME ADD $tmp[$x] BLOB");  // Again, Stick to our standard CHAR(255) default
//				mysql_query("ALTER TABLE $TABLE_NAME ADD $tmp[$x] BLOB") || die(mysql_error());  // Again, Stick to our standard CHAR(255) default
				$CURRENT_FIELD_NAMES .= $tmp[$x] . ";";				    // Add this to our current db table counter.  Not done with it just yet
			}

		} // End Add New Fields


		// --------------------------------------------------------------
		// FONZY STEP 5: ADD LEATHER TUSKIDARO NOW, HERE'S THE TRICK
		// INSTEAD OF INSERTING ALL DATA FIRST, WE REALLY WANT TO UPDATE
		// A DATA RECORD, SO, WE WILL INSERT A "BLANK" RECORD AND THEN
		// UPDATE A RECORD WITH THE FORM DATA "WHERE PRIKEY" IS EQ TO
		// THE LAST ADDED KEY... :)
		// --------------------------------------------------------------

		$tmp = split(";", $CURRENT_FIELD_NAMES);
		$tmp_cnt = count($tmp) - 2;


		$SQL_INSERT = "INSERT INTO $TABLE_NAME VALUES(";
		for ($x=0;$x<=$tmp_cnt;$x++) {
			if ($x != $tmp_cnt) {
				$SQL_INSERT .= "'NULL', ";
			} else {
				$SQL_INSERT .= "'NULL'";
			}
		}

		$SQL_INSERT .= ")";



			mysql_query("$SQL_INSERT");					// Insert NULL Record
			$NEW_ID = mysql_insert_id();				// What dit the PRIKEY AUTO_INCREMENT set the primary key as?



		$SQL_UPDATE = "UPDATE $TABLE_NAME SET ";

		for ($x=1;$x<=$tmp_cnt;$x++) {					// Start with 1 instead of 0.  No need to UPDATE the PriKey :)

			$tValue = ${$tmp[$x]};

//			if (!eregi("email", $tmp[$x])) {			// Don't CAP email addresses
//				$tValue = ucwords($tValue);
//			}

			$tValue = addslashes($tValue);				// For goodness sake, make sure quotes and appostrophies can be stored in field


			if ($x != $tmp_cnt) {
				$SQL_UPDATE .= "$tmp[$x] = '$tValue', ";
			} else {
				$SQL_UPDATE .= "$tmp[$x] = '$tValue'";
			}
		}


			mysql_query("$SQL_UPDATE WHERE PRIKEY = '$NEW_ID'");


		// --------------------------------------------------------------
		// FONZY FUNCTION IS COMPLETED!
		// --------------------------------------------------------------


} // End Database Process

#######################################################################################################
####### SIX: FINAL STEP, TAKE REDIRECT OR CLOSE ACTION BASED ON SETTINGS FOR THIS FORM		 ######
#######################################################################################################

if (eregi("yes", $SELFCLOSE)) {

	echo "<HTML><HEAD><TITLE>".lang("Form Submitted").". ".lang("Thank You")."!</TITLE></HEAD>\n";
	echo "<BODY BGCOLOR=DARKBLUE TEXT=WHITE>\n\n";

	echo "<SCRIPT language=Javascript>\n";
	echo "     alert('".lang("Thank You")."! ".lang("Your information has been submitted").".');\n";
	echo "     self.close();\n";
	echo "</SCRIPT>\n\n";

	echo "</BODY></HTML>\n";
	exit;

} // End Self Close Ending

// -----------------------------------------------------
// Only other option is to redirect to a specified page
// -----------------------------------------------------
$pgqry = mysql_query("select page_name from site_pages where link='".$PAGEGO."'");
$cntit = mysql_num_rows($pgqry);
if($cntit > 0) {
	while($woo = mysql_fetch_array($pgqry)) {
		$PAGEGO = $woo['page_name'];
		$PAGEGO = eregi_replace(' ', '_', $PAGEGO);
	}
}

$PAGEGO = eregi_replace(" ", "_", $PAGEGO);

header("Location: ".pagename($PAGEGO));
exit;

?>