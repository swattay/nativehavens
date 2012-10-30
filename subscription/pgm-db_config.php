<?php
error_reporting(E_PARSE);
if($_GET['_SESSION'] != '' || $_POST['_SESSION'] != '' || $_COOKIE['_SESSION'] != '') { exit; }




###############################################################################

## Soholaunch(R) Site Management Tool

## Version 4.6

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



############################################################################

### Define where the site configuration file is located for all OS's	 ###

############################################################################



// Even Windows doesn't know who the @#$! it is!

if (eregi("IIS", $SERVER_SOFTWARE)) {

	if (!session_is_registered("WINDIR")) { session_register("WINDIR"); } 

	if ($WINDIR == "") { $WINDIR = "WIN"; }

}



// Register as global for use elsewhere

if (!session_is_registered("WIN_FULL_PATH")) { session_register("WIN_FULL_PATH"); } 

$WIN_FULL_PATH = ""; // For Linux Servers

	

$filename = "sohoadmin/config/isp.conf.php";



// Let's Look at what global vars are available to us so that we can determine

// if this is a Windows Server, Windows Server w/Sphera or Linux Server



if (eregi("WIN", $WINDIR)) {	// Windows Server





	$WIN_FULL_PATH = $PATH_TRANSLATED;

	$tmp = eregi_replace("/","\\\\", $PHP_SELF);

	$WIN_FULL_PATH = eregi_replace("$tmp", "", $WIN_FULL_PATH);

			

	// Set the configuration file location using full path (Win)

	$filename = $WIN_FULL_PATH . "\\sohoadmin\\\\config\\\\isp.conf.php";

	

} // End Build Win Full Path	



// -----------------------------------------------	

// Added for newsletter subscription system

// -----------------------------------------------

$filename = "../".$filename;



#######################################################

### READ CONFIGURATION VARIABLE FILE		   		###

#######################################################	



$file = fopen("$filename", "r") or DIE(lang("Error").": ".lang("Could not open isp.conf in config folder")." ($filename)");



	$body = fread($file,filesize($filename));



	$lines = split("\n", $body);

	$numLines = count($lines);



	for ($x=0;$x<=$numLines;$x++) {



		// If not a comment line then register config variable

		// ====================================================



		if (!eregi("#", $lines[$x])) {

			$temp = split("=", $lines[$x]);

			$variable = $temp[0];

			$value = $temp[1];

			session_register("$variable");

			${$variable} = $value;

		}

	}



fclose($file);



###########################################################

#### GLOBAL CONNECT TO DEFINED DATABASE FOR THIS SITE   ###

###########################################################



$link = mysql_pconnect("$db_server", "$db_un","$db_pw");

$sel = mysql_select_db("$db_name");

$result = mysql_list_tables("$db_name");



###########################################################

#### SET GLOBAL SERVER AND WINDOWS DIR VARS FOR EASY ID ###

###########################################################



	if (eregi("IIS", $SERVER_SOFTWARE) && isset($windir)) {

		$WINDIR = $windir;

		session_register("WINDIR");

	}







?>