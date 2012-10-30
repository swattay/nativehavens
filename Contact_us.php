<?
error_reporting(E_PARSE);
session_start();
if($_GET['_SESSION'] != '' || $_POST['_SESSION'] != '' || $_REQUEST['_SESSION'] != '' || $_COOKIE['_SESSION'] != '') { exit; }
include("pgm-site_config.php");
$pagetitle = eregi_replace( "_", " ", "Contact_us" );
$secure_setting = mysql_query("select username from site_pages where page_name = '$pagetitle'");
$secure_name = mysql_fetch_array($secure_setting);
	if (!isset($secure_name['username']) or ($secure_name['username'] == "")) {
    $pr = "Contact_us";
    $_REQUEST['pr'] = "Contact_us";
    $_GET['pr'] = "Contact_us";
    $_POST['pr'] = "Contact_us";
    $pageRequest = "Contact_us";
    include("index.php");
	} else { $destination = "index.php?pr=Contact_us";
	  header("Location:$destination");
   }
exit;
?>
