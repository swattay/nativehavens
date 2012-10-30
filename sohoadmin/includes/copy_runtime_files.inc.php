<?php
error_reporting(E_PARSE);
if($_GET['_SESSION'] != '' || $_POST['_SESSION'] != '' || $_COOKIE['_SESSION'] != '') { exit; }
session_start();

##################################################
### COPY ALL BASE RUNTIME FILES TO USER        ###
### DIRECTORY FOR LATEST UPDATE OPERATION      ###
### v4.7 -- This now includes runtime.css      ###
##################################################

require_once('program/includes/product_gui.php');
# Preserve original working dir and force "sohoadmin" working dir
# b/c this the code in this file was originally cut form update_client.php and expects a working dir of "sohoadmin"
# but is now included in different places like software_updates.php and update_client.php)
$orig_dir = getcwd();
chdir($_SESSION['docroot_path'].'/sohoadmin');

$userdir = $_SESSION['docroot_path'];
$clientdir = "client_files/base_files";
$cn = 0; // counter
$handle = opendir("$clientdir");

while ($files = readdir($handle)) {

	// Un-comment echo lines to test file copy
	/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
	if (strlen($files) > 2) {
		if ( @copy("$clientdir/$files", "$userdir/$files") ) {
//		   echo "<font style=\"color: #339959;\">$cn) $clientdir/<b>$files</b></font><br>\n";
		} else {
//		   echo "<font style=\"color: #980000;\">$cn) $clientdir/<b>$files</b></font><br>\n";
		}
	}
	/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/

}

closedir($handle);


##################################################
### COPY INITIAL IMAGES TO IMAGES FOLDER	     ###
##################################################
$userdir = $_SESSION['docroot_path']."/images";
//@copy("icons/image_global.gif", "$userdir/image_global.gif");
@copy("icons/image_paperwork.gif", "$userdir/image_paperwork.gif");


####################################################
### COPY BASE TEMPLATE TO SITE IF NOT ONE IN USE ###
####################################################
$tmp_file = $_SESSION['docroot_path']."/template/template.conf";
$userdir = $_SESSION['docroot_path']."/template";

if (!file_exists("$tmp_file")) {

	$filename = "$userdir/template.conf";
	$file = fopen("$filename", "w");
		fwrite($file, "CORPORATE-A_Curvacious_Mark-Blue_Gray");
	fclose($file);


} // End If File Exists


##################################################
### DEFINE CONTENT AREA STATEGY				   ###
##################################################
$filename = $cgi_bin . "/contentarea.conf";
if (!file_exists($filename)) {
	$file = fopen("$filename", "w");
		fwrite($file, "LIQUID");
	fclose($file);
}


##########################################################################################
### SETUP PRO MODULES IF THEY EXIST
##########################################################################################

if ( file_exists($_SESSION['docroot_path']."/sohoadmin/filebin/soholaunch.lic") ) {

	// -----------------------------------------------------------
	// COPY SHOPPING CART RUNTIME FILES TO ROOT/SHOPPING
	// -----------------------------------------------------------
	if (is_dir($_SESSION['docroot_path']."/sohoadmin/client_files/shopping_cart")) {

		$DIR = $_SESSION['docroot_path']."/shopping";
		if (!is_dir($DIR)) {
			if (!mkdir($DIR, 0755)) { echo ("Dir Creation Error: Error Creating <B>$DIR</b> directory<BR>"); $err=1; };
			chmod ($DIR, 0755);
		}

		$userdir = $_SESSION['docroot_path']."/shopping";
		$clientdir = "client_files/shopping_cart";

		$handle = opendir("$clientdir");
		while ($files = readdir($handle)) {
		   $cn++;
			if (strlen($files) > 2) {

			   // Un-comment echo lines to test file copy
			   /*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
      		if ( @copy("$clientdir/$files", "$userdir/$files") ) {
      		   //echo "<font style=\"color: #339959;\">$cn) $clientdir/<b>$files</b></font><br>\n";
      		} else {
      		   //echo "<font style=\"color: #980000;\">$cn) $clientdir/<b>$files</b></font><br>\n";
      		}
      		/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/

			}
		}
		closedir($handle);

	} // end cart confirm


	// -----------------------------------------------------------
	// COPY DEMO INCLUDE FILES TO MEDIA FOLDER FOR USE BY CLIENT
	// -----------------------------------------------------------

	if (is_dir("client_files/demo_includes")) {

		$userdir = $_SESSION['docroot_path']."/media";
		$clientdir = "client_files/demo_includes";

		$handle = opendir("$clientdir");
		while ($files = readdir($handle)) {
			if (strlen($files) > 2) {
				@copy("$clientdir/$files", "$userdir/$files");
			}
		}
		closedir($handle);

	} // end demo_includes confirm


	// -----------------------------------------------------------
	// COPY SECURE LOGIN FILES TO ROOT
	// -----------------------------------------------------------

	if (is_dir("client_files/secure_login")) {

		$userdir = $_SESSION['docroot_path'];
		$clientdir = "client_files/secure_login";

		$handle = opendir("$clientdir");
		while ($files = readdir($handle)) {
			if (strlen($files) > 2) {
				@copy("$clientdir/$files", "$userdir/$files");
			}
		}
		closedir($handle);

	} // end secure confirm


	// -----------------------------------------------------------
	// COPY NEWSLETTER SUBSCRIPTION CENTER
	// -----------------------------------------------------------

	if (is_dir("client_files/newsletter")) {

		$DIR = $_SESSION['docroot_path']."/subscription";
		if (!is_dir($DIR)) {
			if (!mkdir ($DIR, 0755)) { echo ("Dir Creation Error: Error Creating <B>$DIR</b> directory<BR>"); $err=1; };
			chmod ($DIR, 0755);
		}

	 	$userdir = $_SESSION['docroot_path']."/subscription";
	 	$clientdir = "client_files/newsletter";

	 	$handle = opendir("$clientdir");
		while ($files = readdir($handle)) {
			if (strlen($files) > 2) {
				@copy("$clientdir/$files", "$userdir/$files");
			}
		}
		closedir($handle);

	 } // end secure confirm

	// -----------------------------------------------------------
	// COPY CALENDAR MODS TO ROOT IF EXIST
	// -----------------------------------------------------------

	if (is_dir("client_files/calendar")) {

	 	$userdir = $_SESSION['docroot_path'];
	 	$clientdir = "client_files/calendar";

	 	$handle = opendir("$clientdir");
		while ($files = readdir($handle)) {
			if (strlen($files) > 2) {
				@copy("$clientdir/$files", "$userdir/$files");
			}
		}
		closedir($handle);

	 } // end calendar client mod xfer

	// -----------------------------------------------------------
	// COPY PHOTO ALBUM MODS TO ROOT IF EXIST
	// -----------------------------------------------------------

	if (is_dir("client_files/photo_album")) {

	 	$userdir = $_SESSION['docroot_path'];
	 	$clientdir = "client_files/photo_album";

	 	$handle = opendir("$clientdir");
		while ($files = readdir($handle)) {
			if (strlen($files) > 2) {
				@copy("$clientdir/$files", "$userdir/$files");
			}
		}
		closedir($handle);

	 } // end photo album client mod xfer

	// -----------------------------------------------------------
	// COPY STATISTICS RUNTIME FILE TO ROOT IF EXISTS
	// -----------------------------------------------------------

	if (is_dir("client_files/statistics")) {

	 	$userdir = $_SESSION['docroot_path'];
	 	$clientdir = "client_files/statistics";

	 	$handle = opendir("$clientdir");
		while ($files = readdir($handle)) {
			if (strlen($files) > 2) {
				@copy("$clientdir/$files", "$userdir/$files");
			}
		}
		closedir($handle);

	 } // end stats mod xfer


} // End verify license exists

# Switch back to original working folder
chdir($orig_dir);

?>