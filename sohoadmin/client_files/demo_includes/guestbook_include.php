<?php
error_reporting(E_PARSE);
if($_GET['_SESSION'] != '' || $_POST['_SESSION'] != '' || $_COOKIE['_SESSION'] != '') { exit; }


#######################################################################
##
##  Guestbook Custom Include Script V1.0
##  Author: Mike Johnston
##
#######################################################################
##
## To use this custom include script:
##
## Upload the script to your site via "Upload Files".  Place on a page
## via the "Custom Code" object.  The database table will be created
## automatically.  To edit any entries, access the database table
## manager.
##
## You may wish to modify the following variable data to customize your
## guestbook script.
##
#######################################################################


$gb_tablename = "UDT_GUESTBOOK";	// Name of database table


$TEXT_COLOR = "#306FAE"; // Font Color
$GUEST_COLOR = "#58595A"; // Guest info font color
$FILL_COLOR = "#FFFFFF"; // Guest Book Background Color
$BORD_COLOR = "#888C8E"; // Guestbook entry border color







#######################################################################
##
## !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
## !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
##
## -- DO NOT MODIFY ANYTHING BELOW THIS LINE FOR PROPER OPERATION --
## -- WITH THE SOHOLAUNCH PLATFORM --
##
## !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
## !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
##
#######################################################################

$tmp = $_SERVER['PHP_SELF'];
$tmp_root = split("/", $tmp);
$tmp_cnt = count($tmp_root);
$tmp_cnt--;
$link_page = $tmp_root[$tmp_cnt];	// In case placed in a calendar or sku section

//echo "Link page: ($link_page)<br>";

#########################################################
### IF THE GUESTBOOK DB TABLE DOES NOT EXIST; CREATE IT
#########################################################

		$match = 0;

		$result = mysql_list_tables("$db_name");
		$i = 0;
		while ($i < mysql_num_rows ($result)) {
			$tb_names[$i] = mysql_tablename ($result, $i);
			if ($tb_names[$i] == $gb_tablename) {
				$match = 1;
			}
			$i++;
		}

		if ($match != 1) {

			if (!eregi("UDT_", $gb_tablename)) {
				$gb_tablename = "UDT_"+$gb_tablename;	// Tablename must start with UDT_ to be editable within DB Table Manager
			}

			mysql_db_query("$db_name","CREATE TABLE $gb_tablename (

				PRIKEY INT NOT NULL AUTO_INCREMENT PRIMARY KEY,

				NAME CHAR(255),
				EMAIL CHAR(255),
				LOCATION CHAR(255),
				COMMENTS BLOB,
				FUTURE BLOB,

				AUTO_IMAGE CHAR(100),
				AUTO_SECURITY_AUTH CHAR(255)

				)");

		} // End if Match != 1


// ----------------------------------------------------------------------------------------------
// -------------- Save the actual post to the data table
// ----------------------------------------------------------------------------------------------

if ($EDIT_GUESTBOOK == "SAVE") {

	if (strlen($GUESTNAME) > 2 && strlen($GUESTEMAIL) > 2 && $GUESTCOMMENTS != "") {

      # Add slashes is magic quotes not on
      if( !get_magic_quotes_gpc() ) {
      	$_POST['GUESTNAME'] = addslashes($_POST['GUESTNAME']);
      	$_POST['GUESTEMAIL'] = addslashes($_POST['GUESTEMAIL']);
      	$_POST['GUESTLOCATION'] = addslashes($_POST['GUESTLOCATION']);
      	$_POST['GUESTCOMMENTS'] = addslashes($_POST['GUESTCOMMENTS']);
      }

		$today = date("F j,Y");
		$insQry = "INSERT INTO ".$gb_tablename." VALUES('NULL','".$_POST['GUESTNAME']."','".$_POST['GUESTEMAIL']."','".$_POST['GUESTLOCATION']."','".$_POST['GUESTCOMMENTS']."','".$today."','NULL','NULL')";

		# Run query and show specific error on failure
		if ( !mysql_query($insQry) ) {
		   echo "MySQL Insert Failed!! -- <br>".mysql_error();
		}

		$EDIT_GUESTBOOK = "";	// Show Guestbook Now
	} else {
		$entry_err = 1;
		$EDIT_GUESTBOOK = "1"; // Redo Comments
	}

} // End Save Routine

// ----------------------------------------------------------------------------------------------
// -------------- Step 1: This is a normal entry to view the current guestbook ------------------
// ----------------------------------------------------------------------------------------------

if ($EDIT_GUESTBOOK == "") {

	echo "<STYLE> .GBCOMMENT { font-family: Verdana; font-size: 12px; } \n .GBWHO { font-size: 10px; font-family: Arial; } </STYLE>\n";

	echo "<table border=0 cellpadding=3 cellspacing=0 width=95% align=center>\n";
	echo " <tr>\n";
	echo "  <td align=\"left\" valign=\"top\">\n";

	//echo "<font size=\"4\" face=\"verdana\" color=\"".$FILL_COLOR."\"><B>Guestbook</B></FONT>";

	echo "  <td align=\"right\" valign=\"middle\">\n";

		echo "   <form method=\"post\" action=\"".$link_page."\">\n";

		if ($pr != "") {
			echo "    <input type=\"hidden\" name=\"pr\" value=\"".$pr."\">\n";
		} else {
			echo "    <input type=\"hidden\" name=\"pr\" value=\"".$pageRequest."\">\n";
		}

		echo "    <input type=\"hidden\" name=\"EDIT_GUESTBOOK\" value=\"1\">\n";
		echo "    <input type=\"submit\" class=\"FormLt1\" VALUE=\"Add Comments\">\n";
		echo "   </form>\n";

	echo "  </td>\n";
	echo " </tr>\n";
	echo " <tr>\n";
	echo "  <td align=\"center\" valign=\"top\" colspan=\"2\">\n";

		$result = mysql_query("SELECT * FROM $gb_tablename ORDER BY PRIKEY DESC");
		$tcheck = mysql_num_rows($result);

		if ($tcheck > 0) {

			while ($row = mysql_fetch_array($result)) {
				echo "<table border=\"0\" cellpadding=\"5\" cellspacing=\"0\" align=\"center\" width=\"100%\" style='border: 1px solid ".$BORD_COLOR.";' bgcolor=\"".$FILL_COLOR."\">\n";
				echo " <tr>\n";
				echo "  <td align=\"left\" valign=\"top\" class=\"GBCOMMENT\">\n";
				echo "   <font color=\"".$TEXT_COLOR."\">".$row['COMMENTS']."</font>\n";
				echo "  </td>\n";
				echo " </tr>\n";
				echo " <tr>\n";
				echo "  <td align=\"right\" valign=\"top\" class=\"GBWHO\">\n";
				echo "   <font color=\"".$GUEST_COLOR."\">".$row['NAME']."<br>".$row['FUTURE']."<br>".$row['LOCATION']."\n";
				echo "  </td>\n";
				echo " </tr>\n";
				echo "</table>\n";
				echo "<br clear=\"all\">";
			}

		} else {

				echo "<TABLE BORDER=0 CELLPADDING=10 CELLSPACING=0 ALIGN=CENTER WIDTH=100% STYLE='border: 1px solid black;' bgcolor=$FILL_COLOR>\n";
				echo "<TR><TD ALIGN=CENTER VALIGN=TOP CLASS=GBCOMMENT><FONT COLOR=$TEXT_COLOR><B>There are currently no comments in the guestbook.<BR><BR>Please add your comments now!</TD></TR>\n";
				echo "</TABLE>\n";

		} // End Make sure comments exist

	echo "</TD></TR></TABLE>\n";

} // End Display Only

// --------------------------------------------------------------------------------
// -------------- Step 2: Get other users comments now with form ------------------
// --------------------------------------------------------------------------------

if ($EDIT_GUESTBOOK == "1") {

	echo "<STYLE> .GBCOMMENT { font-family: Verdana; font-size: 9pt; } \n .GBWHO { font-size: 8pt; font-family: Arial; } </STYLE>\n";

	echo "<TABLE BORDER=0 CELLPADDING=3 CELLSPACING=0 WIDTH=95% ALIGN=CENTER>\n";
	echo "<TR><TD ALIGN=CENTER VALIGN=TOP CLASS=GBCOMMENT>\n";

	$err_show = "";
	if ($entry_err == 1) {
		echo "<FONT COLOR=RED>You did not fill our all required fields.  Please complete the form</FONT><BR><BR>\n";
		$err_show = "<FONT COLOR=RED>*</FONT>";
	}

	echo "<B>Enter you comments for the Guestbook below.  Please fill out all fields.</B><BR><I>Email addresses will not be shared with the public.</I><BR><BR>\n";

		echo "<FORM METHOD=POST ACTION=\"$link_page\">\n";

		if ($pr != "") {
			echo "<INPUT TYPE=HIDDEN NAME=pr VALUE=\"$pr\">\n";
		} else {
			echo "<INPUT TYPE=HIDDEN NAME=pr VALUE=\"$pageRequest\">\n";
		}

		echo "<INPUT TYPE=HIDDEN NAME=\"EDIT_GUESTBOOK\" VALUE=\"SAVE\">\n";

		echo "<TABLE BORDER=0 CELLPADDING=8 CELLSPACING=0 ALIGN=CENTER WIDTH=95%>\n";
		echo "<TR><TD ALIGN=RIGHT VALIGN=MIDDLE CLASS=GBCOMMENT>$err_show Your Full Name:</TD><TD ALIGN=LEFT VALIGN=MIDDLE><INPUT TYPE=TEXT SIZE=40 CLASS=text NAME=\"GUESTNAME\" VALUE=\"$GUESTNAME\" style='width: 300px;'></TD></TR>\n";
		echo "<TR><TD ALIGN=RIGHT VALIGN=MIDDLE CLASS=GBCOMMENT>$err_show Your Email Address:</TD><TD ALIGN=LEFT VALIGN=MIDDLE><INPUT TYPE=TEXT SIZE=40 CLASS=text NAME=\"GUESTEMAIL\" VALUE=\"$GUESTEMAIL\" style='width: 300px;'></TD></TR>\n";
		echo "<TR><TD ALIGN=RIGHT VALIGN=MIDDLE CLASS=GBCOMMENT>Your Location (ie: Atlanta, Ga):</TD><TD ALIGN=LEFT VALIGN=MIDDLE><INPUT TYPE=TEXT SIZE=40 CLASS=text NAME=\"GUESTLOCATION\" VALUE=\"$GUESTLOCATION\" style='width: 300px;'></TD></TR>\n";
		echo "<TR><TD ALIGN=RIGHT VALIGN=TOP CLASS=GBCOMMENT>$err_show Your Comments:</TD><TD ALIGN=LEFT VALIGN=MIDDLE><TEXTAREA CLASS=text style='width: 300px;' COLS=40 ROWS=10 NAME=\"GUESTCOMMENTS\" WRAP=VIRTUAL>$GUESTCOMMENTS</TEXTAREA></TD></TR>\n";

		echo "<TR><TD ALIGN=CENTER VALIGN=MIDDLE COLSPAN=2><BR><INPUT TYPE=SUBMIT CLASS=FormLt1 VALUE=\"Submit Comments\"></TD></TR>\n";
		echo "</TABLE>\n";

		echo "</FORM>\n";

	echo "</TD></TR></TABLE>\n";

} // End Edit Guestbook

?>


