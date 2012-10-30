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

################################################################################
### WE NEED TO DISCOVER WHETHER OR NOT THIS SPECIFIC PAGE HAS BEEN ASSIGNED
### IT'S ON SPECIAL TEMPLATE OR IF IT IS USING THE STANDARD BASE SITE TEMPLATE
################################################################################	

$filename = "media/page_templates.txt";
$foundvar=0;
$foundFlag=0;

// ************************************************************
// READ THE INDIVIDUAL PAGE TEMPLATE ASSIGNMENT CONFIG FILE
// IF IT EXISTS
// ************************************************************

if ($file = fopen("$filename", "r")) {
		$conf_file = fread($file,filesize($filename));
	fclose($file);

	$alines = split("\n", $conf_file);
	$anumLines = count($alines);

	for ($zz=0;$zz<=$anumLines;$zz++) {
		if (eregi("=", $alines[$zz])) {
			$foundvar++;
			$temp = split("=", $alines[$zz]);
			$ifval[$foundvar] = $temp[0];
			$thenval[$foundvar] = $temp[1];
		}
	}

	if ($module_active == "yes" && $templateVar != "") { $pageRequest=$templateVar; } 

	// ******************************************************************************
	// IF THIS IS NOT A MODULE CALL (ALL MODULES USE BASE TEMPLATE NO MATTER WHAT)
	// THEN LOOP THROUGH INDIVIDUAL ASSIGNMENTS AND FIND "THIS" PAGE
	// ******************************************************************************

	for ($zz=0;$zz<=$foundvar;$zz++) {

		$tmp = eregi_replace(" ", "_", $pageRequest);

		$thenval[$zz] = chop($thenval[$zz]);
		$ifval[$zz] = chop($ifval[$zz]);

		if ($ifval[$zz] == $tmp) { 
			$tVal[1] = $thenval[$zz]; 
			$tVal[1] = chop($tVal[1]); 
			$foundFlag=1; 
		}

	}

	if ($foundFlag == 0) { $body = $bombCode; }

} else {	// If file does not exist; no individual template could possibly be assigned!
	
	############################################
	### IF ALL ELSE FAILS, GO SAFE #############
	############################################

	$body = $bombCode;

}

?>