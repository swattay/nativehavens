<?php

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
## Copyright 1999-2003 Soholaunch.com, Inc.  All Rights Reserved.       
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

if (!isset($PHP_AUTH_USER)) {

	include($_SESSION['docroot_path']."/sohoadmin/includes/get_login.php");
	exit;

} else if (isset($PHP_AUTH_USER)) {

	$PHP_AUTH_USER = strtoupper($PHP_AUTH_USER); 
	$PHP_AUTH_PW = strtoupper($PHP_AUTH_PW);	

	include ("includes/db_connect.php");

	$result = mysql_query("SELECT * FROM $user_table");

	while ($row = mysql_fetch_array ($result)) {
	
		$un = $row["Username"];
		$pw = $row["Password"];
		$pk = $row["PriKey"];
		$dot_com = $row["Owner"];

		$tmpun = strtoupper($un);
		$tmppw = strtoupper($pw);
		$thisCheck = "$tmpun:$tmppw";
		
		if ($thisCheck == "$PHP_AUTH_USER:$PHP_AUTH_PW") { 
		
			$auth=1; 
			session_register("PHP_AUTH_PW");
			session_register("PHP_AUTH_USER");
			session_register("dot_com");
			
			// Added for version 5 : Multi-User Access Rights
			// ----------------------------------------------
			
			$CUR_USER = $row["Email"];
			$CUR_USER_KEY = $row["PriKey"];
			
			session_register("CUR_USER_KEY");	// Register Current User PriKey
			session_register("CUR_USER");		// Register Current User Name
			
			if ($pk != 1) {
				$ares = mysql_query("SELECT ACCESS_STRING FROM USER_ACCESS_RIGHTS WHERE LOGIN_KEY = '$pk'");
				$this_access = mysql_fetch_array($ares);
				$CUR_USER_ACCESS = $this_access["ACCESS_STRING"];
			} else {
				$CUR_USER_ACCESS = "WEBMASTER";
			}
			
			session_register("CUR_USER_ACCESS");	// Register Access Rights String
					
		}	// End If Authorized User

	}
	
}

if ($auth != 1) {
	include($_SESSION['docroot_path']."/sohoadmin/includes/get_login.php");
	exit;
}

// USER AUTHENTICATED :: LET THEM PASS


?>