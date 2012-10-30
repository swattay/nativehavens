<?
#===================================================================================================================================
# Soholaunch v4.91 > SitePal > Header nav buttons include
#===================================================================================================================================
error_reporting(E_PARSE);
if($_GET['_SESSION'] != '' || $_POST['_SESSION'] != '' || $_COOKIE['_SESSION'] != '') { exit; }
session_start();
include_once($_SESSION['docroot_path']."/sohoadmin/program/includes/product_gui.php");
error_reporting(E_PARSE);
?>

<!---header_nav-->
<div id="header_nav">
 <ul id="nav">
  <!--- <li id="nav-setup"><a href="setup.php">Set-up SitePal</a></li> -->
  <li id="nav-accounts"><a href="accounts.php">Manage Accounts</a></li>

<?
# Disable char behavior button if account data goes bad
if ( sitepal_verified(true) ) {
   # ENABLED
   echo "<li id=\"nav-template\"><a href=\"template_rules.php\">Template Character Behavior</a></li>\n";
} else {
   # DISABLED
   echo "<li id=\"nav-template\" class=\"disabled\"><a href=\"#\">Template Character Behavior</a></li>\n";
}
?>

  <li id="nav-sitepal"><a href="#" onclick="window.open('<? echo $edit_scene_url; ?>');">Launch SitePal Partner Area</a></li>
 </ul>
</div>