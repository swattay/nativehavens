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

set_time_limit(0);
error_reporting(E_PARSE);
session_start();
echo $_SESSION['docroot_path']."<br>"; 
/// Ensure 100% legit docroot var is available
###-------------------------------------------------------------------------------------
if ( is_dir($_SESSION['docroot_path']) ) {

   # Known aspects of path
   $clipknown = DIRECTORY_SEPARATOR."sohoadmin".DIRECTORY_SEPARATOR."includes".DIRECTORY_SEPARATOR.basename(__FILE__);
echo $_SESSION['docroot_path'];
   # Strip away fluff and presto: garaunteed-accurate docroot path
   $_SESSION['docroot_path'] = str_replace( $clipknown, "", __FILE__);
echo $_SESSION['docroot_path'];
   # Define domain root path (for html stuff)
   $_SESSION['docroot_url'] = $_SERVER['HTTP_HOST'].str_replace( $clipknown, "", $_SERVER['PHP_SELF'] );

   # Define path to interface graphics directory
   $_SESSION['icon_dir'] = $_SESSION['docroot_url'].DIRECTORY_SEPARATOR."program".DIRECTORY_SEPARATOR."includes".DIRECTORY_SEPARATOR."display_elements".DIRECTORY_SEPARATOR."graphics".DIRECTORY_SEPARATOR;
}
echo $_SESSION['docroot_path']."<br>";  exit;

# Emulate register_globals = 1!
include($_SESSION['docroot_path']."/sohoadmin/includes/emulate_globals.php");

########################################################
### READ ISP.CONF.PHP CONFIGURATION VARIABLE FILE	  ###
########################################################
$filename = $_SESSION['docroot_path']."/sohoadmin/config/isp.conf.php";		// This server should be setup; if not; let's set it up.

if ($file = fopen("$filename", "r")) {
	$body = fread($file,filesize($filename));
	$lines = split("\n", $body);
	$numLines = count($lines);

	for ($x=2;$x<=$numLines;$x++) {

		// --------------------------------------------------------------
		// Session Register all Variables contained inside isp.conf file
		// --------------------------------------------------------------

		if (!eregi("#", $lines[$x])) {
			$variable = strtok($lines[$x], "=");
			$value = strtok("\n");
			$value = rtrim($value);

			${$variable} = $value;
			session_register("$variable");
		}
	}

	fclose($file);

	$user_table = "login";
	$com_key = "pro";

	// -----------------------------------------------------------------------------
	// If the config file did not exist; let the user configure the setup via a GUI
	// -----------------------------------------------------------------------------

	if (eregi("IIS", $SERVER_SOFTWARE) && isset($windir)) {
		$WINDIR = $windir;
		if ( !session_is_registered("WINDIR") ) { session_register("WINDIR"); }
	}

} else {
   echo "\n\n\n\n\n\n\n\n";
   echo "<!===============================================>-\n";
   echo "<!-------Unable to read config file-------------->\n";
   echo "<!===============================================>\n";
   echo "\n\n\n\n\n\n\n\n";

} // End If File Open


########################################################
### READ HOST.CONF.PHP CONFIGURATION VARIABLE FILE	  ###
########################################################
$filename = "config/host.conf.php";		// This server should be setup; if not; let's set it up.

if ($file = fopen("$filename", "r")) {
	$body = fread($file,filesize($filename));
	$lines = split("\n", $body);
	$numLines = count($lines);

	for ($x=0;$x<=$numLines;$x++) {

		// --------------------------------------------------------------
		// Session Register all Variables contained inside host.conf file
		// --------------------------------------------------------------

		if (!eregi("#", $lines[$x])) {
			$variable = strtok($lines[$x], "=");
			$value = strtok("\n");
			$value = rtrim($value);

			${$variable} = $value;
			session_register("$variable");
		}
	}

	fclose($file);

} // End If File Open

if ( $custom_hostname != "yes" ) {
   $this_ip = $_SESSION['docroot_url'];
}

if ( $custom_docroot != "yes" ) {
   $doc_root = $_SESSION['docroot_path'];
}

if ( $_SESSION['skin'] == "" ) {
   $_SESSION['skin'] = "default";
}


?>