<?php
error_reporting(E_PARSE);
session_start();
if($_GET['_SESSION'] != '' || $_POST['_SESSION'] != '' || $_REQUEST['_SESSION'] != '' || $_COOKIE['_SESSION'] != '') { exit; }
include("pgm-site_config.php");
$pagetitle = eregi_replace( "_", " ", "Residential_Services" );
$secure_setting = mysql_query("select username from site_pages where page_name = '$pagetitle'");
$secure_name = mysql_fetch_array($secure_setting);
	if (!isset($secure_name['username']) or ($secure_name['username'] == "")) {
    $pr = "Residential_Services";
    $_REQUEST['pr'] = "Residential_Services";
    $_GET['pr'] = "Residential_Services";
    $_POST['pr'] = "Residential_Services";
    $pageRequest = "Residential_Services";
    include("index.php");
	} else { $destination = "Residential_Services.php";
	  header("Location:$destination");
   }
exit;
?>
