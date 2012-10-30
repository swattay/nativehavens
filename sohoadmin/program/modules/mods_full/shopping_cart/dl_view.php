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
session_start();
# Make sure session is restored (Mantis #4)
if ( strlen($lang["Order Date"]) < 4 ) {
   include("includes/config-global.php"); // Re-registers all global & session info
}

include("../includes/login.php");
include("../includes/db_connect.php");

$CSVFILE = "";

########################################################
### CONNECT TO APPROPRIATE TABLE AND GET ALL DATA    ###
########################################################

	$result = mysql_query("SELECT * FROM cart_invoice $QUERY");
	$N_FIELDS = mysql_num_fields($result) - 1;

	// ----------------------------------------------
	// Insert Field Names as First Line of CSV data
	// ----------------------------------------------
		
	for ($x=0;$x<=$N_FIELDS;$x++) {
		if ($x != $N_FIELDS) {
			$CSVFILE .= mysql_field_name($result, $x);
			$CSVFILE .= ";";
		} else {
			$CSVFILE .= mysql_field_name($result, $x);
			$CSVFILE .= "\n";
		}
	} // End For Loop

	// ----------------------------------------------
	// Place each record into CSV file variable
	// ----------------------------------------------

	while ($row = mysql_fetch_array ($result)) {

		for ($x=0;$x<=$N_FIELDS;$x++) {

			$THIS_FIELD_NAME = mysql_field_name($result, $x);
			$THIS_DATA = $row[$THIS_FIELD_NAME];
			
			$THIS_DATA = eregi_replace(";", ",", $THIS_DATA);
			$THIS_DATA = eregi_replace("\n", "", $THIS_DATA);	// Kill Internal CR from Unix or Windows
			$THIS_DATA = eregi_replace("\r", ",", $THIS_DATA);
			// $THIS_DATA = addslashes($THIS_DATA);

			if ($x != $N_FIELDS) {
				$CSVFILE .= "\"".$THIS_DATA."\"";
				$CSVFILE .= ";";
			} else {
				$CSVFILE .= "\"".$THIS_DATA."\"\n";
			}
			
		} // End For Loop
		
	} // End While Loop
	
	// ----------------------------------------------
	// Clean up char that will cause spreadsheets to
	// "Over-React" and format the import
	// ----------------------------------------------	
	
	// ----------------------------------------------
	// Force Feed the Download Action Now
	// ----------------------------------------------
	
	$today = date("Y-m-d");
	$local_name = "CART_INVOICE_DATA($today).csv";
	$local_name = strtoupper($local_name);

	Header("Content-Type: application/x-octet-stream"); 
	Header( "Content-Disposition: attachment; filename=\"$local_name\""); 
	echo $CSVFILE; 
	
?>