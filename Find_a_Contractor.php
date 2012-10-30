<?php
error_reporting(E_PARSE);
session_start();
if($_GET['_SESSION'] != '' || $_POST['_SESSION'] != '' || $_REQUEST['_SESSION'] != '' || $_COOKIE['_SESSION'] != '') { exit; }
include("pgm-site_config.php");
$pagetitle = eregi_replace( "_", " ", "Find_a_Contractor" );
$secure_setting = mysql_query("select username from site_pages where page_name = '$pagetitle'");
$secure_name = mysql_fetch_array($secure_setting);
	if (!isset($secure_name['username']) or ($secure_name['username'] == "")) {
    $pr = "Find_a_Contractor";
    $_REQUEST['pr'] = "Find_a_Contractor";
    $_GET['pr'] = "Find_a_Contractor";
    $_POST['pr'] = "Find_a_Contractor";
    $pageRequest = "Find_a_Contractor";
    include("index.php");
	} else { $destination = "Find_a_Contractor.php";
	  header("Location:$destination");
   }
exit;
?>
