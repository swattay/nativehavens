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

#######################################################
#### Step 1: Confirm Existance of Security Table    ###
#### and if not, create and populate global admin   ###
#######################################################

$link = mysql_connect("$db_server", "$db_un","$db_pw") || die("Could not connect to database '$db_name' ($db_server). Check your database setup.");
mysql_query("SET SESSION SQL_MODE = ''");
$sel = mysql_select_db("$db_name"); 
$result = mysql_list_tables("$db_name");

	$i = 0; 
	$match = 0;
	while ($i < mysql_num_rows ($result)) { 
		$tb_names[$i] = mysql_tablename ($result, $i); 
		if ($tb_names[$i] == "$user_table") { $match = 1; }
		$i++;
	}

	if ($match != 1) {
	
		// Create user table for future use
	
		mysql_db_query("$db_name","CREATE TABLE $user_table (PriKey INT NOT NULL AUTO_INCREMENT PRIMARY KEY, Owner CHAR(150), First_Name CHAR(75), Last_Name CHAR(75), Email CHAR(150), Username CHAR(50), Password CHAR(50), Rank CHAR(255))");
		$tmp_pw = md5("admin");
		mysql_query("INSERT INTO $user_table VALUES('NULL','$SERVER_NAME','SOHOUSER','SOHOUSER','webmaster@$SERVER_NAME','admin','admin','$tmp_pw')");
	
	} else {
	
		$tmp_pw = md5($PHP_AUTH_PW);
		mysql_query("UPDATE $user_table SET Rank = '$tmp_pw' WHERE Username = '$PHP_AUTH_USER' AND Password = '$PHP_AUTH_PW'");
	
	}

?>