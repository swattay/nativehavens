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

//error_reporting(E_PARSE);

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
// By License you may not modify any portion of this script. This particular
// script has dependancies and programming that can not be modified.
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
$thirty_mins = 30;
$match = 0;
$tablename = "login_attempts";
$result = mysql_list_tables("$db_name");
$i = 0;
while ($i < mysql_num_rows ($result)) {
  $tb_names[$i] = mysql_tablename ($result, $i);
  if ($tb_names[$i] == $tablename) {
    $match = 1;
  }
  $i++;
}

if ($match != 1) {  
  $SQL_CREATE = "CREATE TABLE $tablename (PRIKEY INT NOT NULL AUTO_INCREMENT PRIMARY KEY, ip_address CHAR(50), date CHAR(50), time CHAR(25), username CHAR(50), password CHAR(50))";
  mysql_query($SQL_CREATE);
}

$match = 0;
$tablename = "login_history";
$result = mysql_list_tables("$db_name");
$i = 0;
while ($i < mysql_num_rows ($result)) {
  $tb_names[$i] = mysql_tablename ($result, $i);
  if ($tb_names[$i] == $tablename) {
    $match = 1;
  }
  $i++;
}

if ($match != 1) {  
  $SQL_CREATE = "CREATE TABLE $tablename (prikey INT NOT NULL AUTO_INCREMENT PRIMARY KEY, ip_address CHAR(50), username VARCHAR(255), date CHAR(50), time CHAR(25))";
  mysql_query($SQL_CREATE);
}

$match = 0;
$tablename = "login_bans";
$result = mysql_list_tables("$db_name");
$i = 0;
while ($i < mysql_num_rows ($result)) {
  $tb_names[$i] = mysql_tablename ($result, $i);
  if ($tb_names[$i] == $tablename) {
    $match = 1;
  }
  $i++;
}

if ($match != 1) {  
  $SQL_CREATE = "CREATE TABLE $tablename (PRIKEY INT NOT NULL AUTO_INCREMENT PRIMARY KEY, ip_address CHAR(50), date CHAR(50), time CHAR(25))";
  mysql_query($SQL_CREATE);
}


if ( !isset($_SESSION['PHP_AUTH_USER']) && !isset($_POST['PHP_AUTH_USER']) ) {
	/////////////////////////////////////////
	/////////////camerons check login attempts
	if(!isset($_POST['PHP_AUTH_USER'])) {
	  include_once("db_connect.php");    
	  $thirty_mins_ago = strtotime('-'.$thirty_mins.' minutes');
	  $daipquery = mysql_query("select * from login_bans where ip_address='".$_SERVER['REMOTE_ADDR']."' and time > '".$thirty_mins_ago."'");
	  $bancount = mysql_num_rows($daipquery);
	  if($bancount >= 1){
	    $banned = mysql_fetch_array($daipquery);
	    $bantime = $banned['time'];
	    $unbantime = time() - $bantime;
	    $timewaited = date('i', $unbantime);
	    $timetillrelease = $thirty_mins - $timewaited;    
	    echo "<font color=white>You have been banned due too many failed login attempts!<br/>\n You must wait ".$timetillrelease." minutes before attempting to login again.</font>";
	    session_destroy();
	    include("includes/get_login.php");
	    exit;
	    
	  }
	}
	/////////////////////////////////////////
	/////////////end camerons check login attempts

	include($_SESSION['docroot_path']."/sohoadmin/includes/get_login.php");
	exit;

} elseif ( isset($_SESSION['PHP_AUTH_USER']) || isset($_POST['PHP_AUTH_USER']) ) {		
	if (isset($_POST['PHP_AUTH_USER']) ) {		
		/////////////////////////////////////////
		/////////////camerons check login attempts   
		include_once("db_connect.php");
		$thirty_mins_ago = strtotime('-'.$thirty_mins.' minutes');
		$daipquery = mysql_query("select * from login_bans where ip_address='".$_SERVER['REMOTE_ADDR']."' and time > '".$thirty_mins_ago."'");
		$bancount = mysql_num_rows($daipquery);
		
		if($bancount >= 1){
			$bancountstuff = mysql_fetch_array($daipquery);   
			unset($infoarray);
			$infoarray['ip_address'] = $_SERVER['REMOTE_ADDR'];
			$infoarray['date'] = date("m/d/Y");
			$infoarray['time'] = time();
			$frminsrt = new mysql_insert($tablename, $infoarray);
			$frminsrt->insert();
			
			mysql_query("update login_bans set time='".time()."' where PRIKEY='".$bancountstuff['PRIKEY']."'");
			echo "<font color=white>You have been banned due too many failed login attempts!<br/>\n You must wait ".$thirty_mins." minute(s) before attempting to login again.</font>";
			     session_destroy();
			include("includes/get_login.php");
			exit;
		}
		/////////////////////////////////////////
		/////////////end camerons check login attempts
		
		$_SESSION['PHP_AUTH_USER'] =  $_POST['PHP_AUTH_USER'];
		$_SESSION['PHP_AUTH_PW'] =  $_POST['PHP_AUTH_PW'];
		
		# Live demo sites - Register demo user info in session for logging and click path tracking
		if ( $_SESSION['demo_site'] == "yes" ) {
		     $_SESSION['demo_user'] =   $_POST['demo_user'];
		     $_SESSION['demo_num'] =  $_POST['demo_num'];
		     $_SESSION['demo_email'] = $_POST['demo_email'];
		     
		     # Save start time in session so we don't have to pull from db for "ping for more time if 30min is up and they're still going"
		     $_SESSION['demostart_timestamp'] = time();
		}
	}

   $PHP_AUTH_USER = strtoupper($_SESSION['PHP_AUTH_USER']);
   $PHP_AUTH_PW = strtoupper($_SESSION['PHP_AUTH_PW']);

	$result = mysql_query("SELECT * FROM $user_table");

	while ($row = mysql_fetch_array ($result)) {

		$un = $row["Username"];
		$pw = $row["Password"];
		$pk = $row["PriKey"];
		$dot_com = $this_ip;

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
			
			$ten_mins_ago = strtotime('-'.$thirty_mins.' minutes');
			mysql_query("DELETE from login_attempts where ip_address='".$_SERVER['REMOTE_ADDR']."' and time > '".$ten_mins_ago."'");
			session_register("CUR_USER_ACCESS");	// Register Access Rights String
			
			if (isset($_POST['PHP_AUTH_USER']) ) {	
				unset($loginiparray);
				$loginiparray['ip_address'] = $_SERVER['REMOTE_ADDR'];
				$loginiparray['username'] = $_POST['PHP_AUTH_USER'];
				$loginiparray['date'] = date("m/d/Y");
				$loginiparray['time'] = time();
				$frminsrt = new mysql_insert('login_history', $loginiparray);
				$frminsrt->insert();
			}
		}	// End If Authorized User

	}

}

if ($auth != 1) {
	$error = 1;
	/////////////////////////////////////////
	/////////////camerons check login attempts
	$time = time();
	$ten_mins_ago = strtotime('-'.$thirty_mins.' minutes');

	$ipquery = mysql_query("select * from login_attempts where ip_address='".$_SERVER['REMOTE_ADDR']."' and time > '".$ten_mins_ago."'");
	$badcount = mysql_num_rows($ipquery) +1;
	unset($infoarray);
	$infoarray['ip_address'] = $_SERVER['REMOTE_ADDR'];
	$infoarray['date'] = date("m/d/Y");
	$infoarray['time'] = time();
	$infoarray['username'] = $_POST['PHP_AUTH_USER'];
	$infoarray['password'] = $_POST['PHP_AUTH_PW'];

	$frminsrt = new mysql_insert('login_attempts', $infoarray);
	$frminsrt->insert();

	if($badcount >= 6){
		//30 minute ban
		unset($infoarray);
		$infoarray['ip_address'] = $_SERVER['REMOTE_ADDR'];
		$infoarray['date'] = date("m/d/Y");
		$infoarray['time'] = time();

		$frminsrt = new mysql_insert($tablename, $infoarray);
		//$frminsrt->test();
		$frminsrt->insert();
		echo "<font color=white>You have been banned due too many failed login attempts!<br/>\n You must wait ".$thirty_mins." minute(s) before attempting to login again.</font>";
		session_destroy();
	}
	/////////end camerons check login attempts
	/////////////////////////////////////////
	include("includes/get_login.php");
	exit;
}

// USER AUTHENTICATED :: LET THEM PASS


?>