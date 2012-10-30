<?php
error_reporting(E_PARSE);
if($_GET['_SESSION'] != '' || $_POST['_SESSION'] != '' || $_COOKIE['_SESSION'] != '') { exit; }

error_reporting(E_PARSE);
if($_GET['_SESSION'] != '' || $_POST['_SESSION'] != '' || $_COOKIE['_SESSION'] != '') { exit; }


###############################################################################
## Soholaunch SMT                   Version 4.5.1
##
## Copyright 1999-2003 Soholaunch.com, Inc.
## Author: Mike Johnston [mike.johnston@soholaunch.com]
##
## Created: 12/12/1999           Last Modified: 2/2003
## Homepage:     			    http://www.soholaunch.com
## Support:						http://devnet.soholaunch.com
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

// STEP 1: Load Menu Display Settings into Memory
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

$filename = "$cgi_bin/menu.conf";
$file = fopen("$filename", "r");
	$body = fread($file,filesize($filename));
fclose($file);
$lines = split("\n", $body);
$numLines = count($lines);
for ($xedusvar=0;$xedusvar<=$numLines;$xedusvar++) {
	$temp = split("=", $lines[$xedusvar]);
	$variable = $temp[0];
	$value = $temp[1];
	${$variable} = $value;
}

$filename = "$cgi_bin/menucolor.conf";
$file = fopen("$filename", "r");
	$body = fread($file,filesize($filename));
fclose($file);
$lines = split("\n", $body);
$numLines = count($lines);
for ($xedusvar=0;$xedusvar<=$numLines;$xedusvar++) {
	$temp = split("=", $lines[$xedusvar]);
	$variable = $temp[0];
	$value = $temp[1];
	${$variable} = $value;
}


// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

$mainmenu = "vertical"; // Since 4.5 Release 2, this is the default for all nav systems

// STEP 2: Define all Sub-Pages of Current PageRequest
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	$thispage = eregi_replace("_", " ", $pageRequest);

	$result = mysql_query("SELECT page_name, sub_page_of FROM site_pages WHERE sub_page_of LIKE '$thispage%'");
	$active_subs = mysql_num_rows($result);

	if ($active_subs != 0) {

		$a=0;
		while ($row = mysql_fetch_array ($result)) {
			$a++;
			$tmp = split("~~~", $row[sub_page_of]);

			if (strlen($tmp[1]) == 1) { $tmp[1] = "0".$tmp[1]; } // Make single digit order nums a two digit for sort

			$subpage_order[$a] = $tmp[1]."~~~".$row[page_name];
			$subpage_name[$a] = $row["page_name"];
		}

		$numsubpages = $a;
		if ($numsubpages > 1) { sort($subpage_order); }

	} else {

		$numsubpages = 0;

	}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
// What if we are currently viewing a sub-page.  We should still show the "sub" menu
// options.  Lets Check for sure
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~


	if ($numsubpages == 0) {

		$result = mysql_query("SELECT sub_page_of FROM site_pages WHERE page_name = '$thispage'");
		$tmp = mysql_fetch_array($result);

		$dup = $tmp["sub_page_of"];
		$tmp = split("~~~", $dup);
		$dup = $tmp[0];

		$a=0;

		if ($dup != "") {
			$result = mysql_query("SELECT * FROM site_pages WHERE sub_page_of LIKE '$dup%'");
			while ($row = mysql_fetch_array ($result)) {
				$a++;
				$tmp = split("~~~", $row[sub_page_of]);
				if (strlen($tmp[1]) == 1) { $tmp[1] = "0".$tmp[1]; } // Make single digit order nums a two digit for sort
				$subpage_order[$a] = $tmp[1]."~~~".$row[page_name];
				$subpage_name[$a] = $row["page_name"];
			}
			$dup = eregi_replace(" ", "_", $dup);

		}

		$numsubpages = $a;
		if ($numsubpages > 1) { sort($subpage_order); }

	}



// STEP 3: Define Sub Page Text & Button Links
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if ($mainmenu != "vertical") {
	$finishLine = "<BR clear=all><img src=spacer.gif width=10 height=2><BR>\n";
	$vertOption = "";
} else {
	$vertOption = "<BR clear=all><img src=spacer.gif width=100 height=4 border=0><BR>\n";
	$finishLine = "<BR clear=all><img src=spacer.gif width=10 height=4><BR>\n";
}


	$sub_textmenu = "";
	$sub_quicknav = "";

	if ($MENUTYPE == "buttons") {
		$sub_buttons = "";
	} else {
		$sub_buttons = "\n\n<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0 width=154>\n";
	}

if ($numsubpages != 0) {
	if ($numsubpages > 1) { $substart = 0; } else { $substart = 1; }
	for ($allsubs_added=$substart;$allsubs_added<=$numsubpages;$allsubs_added++) {
		for ($xedusvar=1;$xedusvar<=$numsubpages;$xedusvar++) {
			if (eregi("$subpage_name[$xedusvar]", "$subpage_order[$allsubs_added]")) {
				$tmp = eregi_replace(" ", "_", $subpage_name[$xedusvar]);

            // Define the bullets
		      if ($tmp == $pageRequest || $tmp == $dup) {
		         $subClass = "submenu_on";
		      } else {
		         $subClass = "submenu_off";
		      }

				// Create SUB MENU options
				// ----------------------------------------------------------------------------------------
				$sub_textmenu .= "<a href=\"index.php?pr=$tmp\" class=text_menu>$subpage_name[$xedusvar]</a> | ";
				if ($MENUTYPE == "buttons") {
					$sub_buttons .= "<INPUT TYPE=BUTTON VALUE=\"$subpage_name[$xedusvar]\" STYLE=\"font-family: Verdana; font-size: 7pt; width: 100px; height: 20px; background: #$menubg; color: #$linkc; cursor: hand;\" onclick=\"navto('$tmp');\">\n$finishLine\n";
				} else {
					$sub_buttons .= "<TR><TD class=$subClass><a href=\"index.php?pr=$tmp\"> >> $subpage_name[$xedusvar]</a></TD></TR>";
				}
				// ----------------------------------------------------------------------------------------

			}
		} // End For Loop
	} // End Outer Loop
} // End if numsubpages greater than zero

	if ($MENUTYPE != "buttons") {
		$sub_buttons .= "\n</TABLE>\n\n";
	}

// STEP 4: Define Main Menu Text, Buttons and Quick Nav Links
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

$result = mysql_query("SELECT page_name FROM site_pages WHERE main_menu <> ' ' AND (type = 'Main' OR type = 'newsletter') ORDER BY main_menu");
$a=0;


$main_textmenu = "";

if ($MENUTYPE == "buttons") {
	$main_buttons = "<form name=netscapebs>\n\n<SCRIPT LANGUAGE=JAVASCRIPT>\n\n";
	$main_buttons .= "function navto(where) {\n";
	$main_buttons .= "     window.location = 'index.php?pr='+where+'&=SID';\n";
	$main_buttons .= "}\n\n</SCRIPT>\n\n";
} else {
	$main_buttons = "<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0 width=154>\n";
}

while ($row = mysql_fetch_array ($result)) {

		$a++;
		$thispage = $row["page_name"];
		$pagelink = eregi_replace(" ", "_", $thispage);
		$pageRequest = eregi_replace(" ", "_", $pageRequest);

		//Define the bullets
		if ($pagelink == $pageRequest || $pagelink == $dup) {
		   $mmClass = "vmenu_on";
		} else {
		   $mmClass = "vmenu_off";
		}

		// Create MAIN MENU option
		// ----------------------------------------------------------------------------------------
		if ($MENUTYPE == "buttons") {
			$main_buttons .= "<INPUT TYPE=BUTTON VALUE=\"$thispage\" STYLE=\"font-family: Verdana; font-size: 8pt; width: 120px; height: 20px; background: #$menubg; color: #$linkc; cursor: hand;\" onclick=\"navto('$pagelink');\">\n$vertOption\n";
		} else {
			$main_buttons .= "<TR><TD CLASS=$mmClass><a href=\"index.php?pr=$pagelink\">$thispage</a></TD></TR>\n";
		}

		$main_textmenu .= "<a href=\"index.php?pr=$pagelink\" class=text_menu>$thispage</a> | ";

		// ----------------------------------------------------------------------------------------



		if ($pagelink == $pageRequest || $pagelink == $dup) {

			if ($numsubpages != 0 ) {

				if ($MENUTYPE == "buttons") {
					$main_buttons .= $sub_buttons;
				} else {
					$main_buttons .= "<TR><TD ALIGN=LEFT>".$sub_buttons."</TD></TR>\n";
				}

				$main_textmenu .= $sub_textmenu;

			} else {

				$main_textmenu .= $sub_textmenu;
			}
		}
}

if ($MENUTYPE == "buttons") {
	$main_buttons .= "</form>";
} else {
	$main_buttons .= "</TABLE>\n\n";
}


?>