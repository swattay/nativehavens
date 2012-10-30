<?
error_reporting(E_PARSE);
session_start();
if($_GET['_SESSION'] != '' || $_POST['_SESSION'] != '' || $_REQUEST['_SESSION'] != '' || $_COOKIE['_SESSION'] != '') { exit; }
include("pgm-site_config.php");
$pagetitle = eregi_replace( "_", " ", "About_Us" );
$secure_setting = mysql_query("select username from site_pages where page_name = '$pagetitle'");
$secure_name = mysql_fetch_array($secure_setting);
	if (!isset($secure_name['username']) or ($secure_name['username'] == "")) {
    $pr = "About_Us";
    $_REQUEST['pr'] = "About_Us";
    $_GET['pr'] = "About_Us";
    $_POST['pr'] = "About_Us";
    $pageRequest = "About_Us";
    include("index.php");
	} else { $destination = "index.php?pr=About_Us";
	  header("Location:$destination");
   }
exit;
?>
