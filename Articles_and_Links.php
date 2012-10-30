<?php
error_reporting(E_PARSE);
session_start();
if($_GET['_SESSION'] != '' || $_POST['_SESSION'] != '' || $_REQUEST['_SESSION'] != '' || $_COOKIE['_SESSION'] != '') { exit; }
include("pgm-site_config.php");
$pagetitle = eregi_replace( "_", " ", "Articles_and_Links" );
$secure_setting = mysql_query("select username from site_pages where page_name = '$pagetitle'");
$secure_name = mysql_fetch_array($secure_setting);
	if (!isset($secure_name['username']) or ($secure_name['username'] == "")) {
    $pr = "Articles_and_Links";
    $_REQUEST['pr'] = "Articles_and_Links";
    $_GET['pr'] = "Articles_and_Links";
    $_POST['pr'] = "Articles_and_Links";
    $pageRequest = "Articles_and_Links";
    include("index.php");
	} else { $destination = "Articles_and_Links.php";
	  header("Location:$destination");
   }
exit;
?>
