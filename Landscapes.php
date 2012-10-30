<?
error_reporting(E_PARSE);
session_start();
if($_GET['_SESSION'] != '' || $_POST['_SESSION'] != '' || $_REQUEST['_SESSION'] != '' || $_COOKIE['_SESSION'] != '') { exit; }
include("pgm-site_config.php");
$pagetitle = eregi_replace( "_", " ", "Landscapes" );
$secure_setting = mysql_query("select username from site_pages where page_name = '$pagetitle'");
$secure_name = mysql_fetch_array($secure_setting);
	if (!isset($secure_name['username']) or ($secure_name['username'] == "")) {
    $pr = "Landscapes";
    $_REQUEST['pr'] = "Landscapes";
    $_GET['pr'] = "Landscapes";
    $_POST['pr'] = "Landscapes";
    $pageRequest = "Landscapes";
    include("index.php");
	} else { $destination = "index.php?pr=Landscapes";
	  header("Location:$destination");
   }
exit;
?>
