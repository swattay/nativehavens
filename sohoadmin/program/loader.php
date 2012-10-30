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

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
// By License you may not modify any portion of this script. This particular
// script has dependancies and programming that can not be modified.
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

session_start();
error_reporting(E_PARSE);

## Pull 100% legit docroot from path to this script
//==============================================================
if ( !is_dir($_SESSION['docroot_path']) ) {
   # Build known aspects of path to clip
   $clipknown = "/sohoadmin/program/".basename(__FILE__);

   # Define full docroot path from root (for php stuff)
   $_SESSION['docroot_path'] = str_replace( $clipknown, "", __FILE__ );

   # Define domain root path (for html stuff)
   //$_SESSION['docroot_url'] = $_SERVER['HTTP_HOST'].str_replace( $clipknown, "", $_SERVER['PHP_SELF'] );

   # Define full path to core product include script
   $_SESSION['product_gui'] = $_SESSION['docroot_path']."/sohoadmin/program/includes/product_gui.php";

}

# Include core interface files!
if ( !include_once("includes/product_gui.php") ) {
   //echo "Could not include this file:<br>[<b><font color=\"#008080\">".$_SESSION['product_gui']."</font></b>]<br>"; exit;
   exit;
}


#########################################################################
## Start quickstart wizard or have a normal login?
#########################################################################

if ( !file_exists("../nowiz.txt") && !file_exists("../filebin/nowiz.txt") && $CUR_USER_ACCESS == "WEBMASTER") {
	header("Location: wizard/start.php?SID");
	exit;
}

?>

<HTML><HEAD><TITLE>MAIN MENU LOADER</TITLE></HEAD>
<BODY BGCOLOR=WHITE LINK=BLUE ALINK=BLUE VLINK=BLUE LEFTMARGIN=0 TOPMARGIN=0 MARGINWIDTH=0 MARGINHEIGHT=0>

<DIV ID="LOAD_LAYER" style="position:absolute; left:0px; top:40%; width:100%; height:110px; z-index:100; border: 2px none #000000; visibility: visible; overflow: hidden">
<table border=0 cellpadding=0 width=100% height=100% bgcolor=WHITE>
    <tr>
      <td align=center valign=middle>
		<img src="../icons/loading.gif" width=137 height=30 border=0>
      </td>
    </tr>
  </table>
</DIV>

	<?php

		echo "<SCRIPT LANGUAGE=Javascript>\n";
		//echo "window.location = \"user_options_46.php?SID\"; ";
		echo "window.location = \"main_menu.php?SID\"; ";
		echo "</SCRIPT>\n\n";

	?>

</BODY>
</HTML>
<HEAD><META HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE"></HEAD>