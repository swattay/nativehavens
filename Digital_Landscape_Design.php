<?
error_reporting(E_PARSE);
session_start();
if($_GET['_SESSION'] != '' || $_POST['_SESSION'] != '' || $_REQUEST['_SESSION'] != '' || $_COOKIE['_SESSION'] != '') { exit; }
include("pgm-site_config.php");
$pagetitle = eregi_replace( "_", " ", "Digital_Landscape_Design" );
$secure_setting = mysql_query("select username from site_pages where page_name = '$pagetitle'");
$secure_name = mysql_fetch_array($secure_setting);
	if (!isset($secure_name['username']) or ($secure_name['username'] == "")) {
    $pr = "Digital_Landscape_Design";
    $_REQUEST['pr'] = "Digital_Landscape_Design";
    $_GET['pr'] = "Digital_Landscape_Design";
    $_POST['pr'] = "Digital_Landscape_Design";
    $pageRequest = "Digital_Landscape_Design";
    include("index.php");
	} else { $destination = "index.php?pr=Digital_Landscape_Design";
	  header("Location:$destination");
   }
exit;
?>
