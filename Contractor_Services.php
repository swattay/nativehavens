<?php
error_reporting(E_PARSE);
session_start();
if($_GET['_SESSION'] != '' || $_POST['_SESSION'] != '' || $_REQUEST['_SESSION'] != '' || $_COOKIE['_SESSION'] != '') { exit; }
include("pgm-site_config.php");
$pagetitle = eregi_replace( "_", " ", "Contractor_Services" );
$secure_setting = mysql_query("select username from site_pages where page_name = '$pagetitle'");
$secure_name = mysql_fetch_array($secure_setting);
	if (!isset($secure_name['username']) or ($secure_name['username'] == "")) {
    $pr = "Contractor_Services";
    $_REQUEST['pr'] = "Contractor_Services";
    $_GET['pr'] = "Contractor_Services";
    $_POST['pr'] = "Contractor_Services";
    $pageRequest = "Contractor_Services";
    include("index.php");
	} else { $destination = "Contractor_Services.php";
	  header("Location:$destination");
   }
exit;
?>
