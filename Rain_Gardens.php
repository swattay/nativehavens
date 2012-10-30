<?
error_reporting(E_PARSE);
session_start();
if($_GET['_SESSION'] != '' || $_POST['_SESSION'] != '' || $_REQUEST['_SESSION'] != '' || $_COOKIE['_SESSION'] != '') { exit; }
include("pgm-site_config.php");
$pagetitle = eregi_replace( "_", " ", "Rain_Gardens" );
$secure_setting = mysql_query("select username from site_pages where page_name = '$pagetitle'");
$secure_name = mysql_fetch_array($secure_setting);
	if (!isset($secure_name['username']) or ($secure_name['username'] == "")) {
    $pr = "Rain_Gardens";
    $_REQUEST['pr'] = "Rain_Gardens";
    $_GET['pr'] = "Rain_Gardens";
    $_POST['pr'] = "Rain_Gardens";
    $pageRequest = "Rain_Gardens";
    include("index.php");
	} else { $destination = "index.php?pr=Rain_Gardens";
	  header("Location:$destination");
   }
exit;
?>
