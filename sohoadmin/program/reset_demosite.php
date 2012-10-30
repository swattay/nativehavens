<?php
error_reporting(E_PARSE);
if($_GET['_SESSION'] != '' || $_POST['_SESSION'] != '' || $_COOKIE['_SESSION'] != '') { exit; }

session_start();
error_reporting(E_PARSE);


# Include core interface files!
include("includes/config.php");
include("includes/db_connect.php");


# Double check demo site status
if ( $_SESSION['demo_site'] == "yes" ) {

   /// Kill dB tables (except login and demo timer)
   ###---------------------------------------------------------------------------------------------------------
   $i = 0;
   $result = mysql_list_tables("$db_name");
   while ($i < mysql_num_rows ($result)) {
      $tb_names[$i] = mysql_tablename ($result, $i);
      if ( $tb_names[$i] != "demo_timer" && $tb_names[$i] != "demo_track" && $tb_names[$i] != "demo_clickpath" && $tb_names[$i] != "demo_archive" ) {
         //echo "tables (".$tb_names[$i].")<br>";
         mysql_query("DROP TABLE $tb_names[$i]");
      }
      $i++;
   }


   /// Reset quickstart wizard
   ###--------------------------------------------------------------------------------------
   $wizfile = $_SESSION['docroot_path']."/sohoadmin/filebin/nowiz.txt";
   if ( file_exists($wizfile) ) {
      unlink($wizfile);
      //echo "<script language=\"javascript\">\n"; echo "alert(\"wizfile dead!\");\n";  echo "</script>";   exit;
   } else {
      //echo "<script language=\"javascript\">\n";  echo "alert(\"wizfile not dead!\");\n";  echo "</script>";   exit;
   }


   /// Kill page content files
   ###--------------------------------------------------------------------------------------
   $condir = $_SESSION['docroot_path']."/sohoadmin/tmp_content";
   $opcondir = opendir($condir);
   while ( $confile = readdir($opcondir) ) {
      if ( strlen($confile) > 2 ) {
         $killit = $condir."/".$confile;
         @unlink($killit);
      }
   }

   # images/
   $condir = $_SESSION['docroot_path']."/images";
   $opcondir = opendir($condir);
   while ( $confile = readdir($opcondir) ) {
      if ( strlen($confile) > 2 ) {
         $killit = $condir."/".$confile;
         @unlink($killit);
      }
   }

   # media/
   $condir = $_SESSION['docroot_path']."/media";
   $opcondir = opendir($condir);
   while ( $confile = readdir($opcondir) ) {
      if ( strlen($confile) > 2 ) {
         $killit = $condir."/".$confile;
         @unlink($killit);
      }
   }

   /// Do we need to create the demo_timer table?
   ###--------------------------------------------------------------------------------------
   $match = 0;
   $result = mysql_list_tables("$db_name");
   $i = 0;
   while ($i < mysql_num_rows ($result)) {
   	$tb_names[$i] = mysql_tablename ($result, $i);
   	if ($tb_names[$i] == "demo_timer") { $match = 1; }
   	$i++;
   }

   if ($match != 1) {
      // Yes, create demo_timer table
   	if ( !mysql_db_query("$db_name","CREATE TABLE demo_timer (PriKey INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT, last_demo VARCHAR(255), site_active VARCHAR(255))") ) {
   	   //echo "could not create demo_timer table on $db_name!";
   	}

   	mysql_query("INSERT INTO demo_timer VALUES('','','')");
   }

   /// Update demo_timer table with current timestamp and status
   ###--------------------------------------------------------------------------------------
   $tStamp = time();
   mysql_query("UPDATE demo_timer SET last_demo = '$tStamp', site_active = 'no'");


} // End if demo_site










?>