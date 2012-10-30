<?php
error_reporting(E_PARSE);
if($_GET['_SESSION'] != '' || $_POST['_SESSION'] != '' || $_COOKIE['_SESSION'] != '') { exit; }


###############################################################################
## Soholaunch(R) Site Management Tool
## Version 4.5
##
## Author: 			Mike Johnston [mike.johnston@soholaunch.com]
## Homepage:	 	http://www.soholaunch.com
## Bug Reports: 	http://bugzilla.soholaunch.com
## Release Notes:	sohoadmin/build.dat.php
###############################################################################

##############################################################################
## COPYRIGHT NOTICE
## Copyright 1999-2003 Soholaunch.com, Inc. and Mike Johnston 
## Copyright 2003-2007 Soholaunch.com, Inc.
## All Rights Reserved.
##
## This script may be used and modified in accordance to the license
## agreement attached (license.txt) except where expressly noted within
## commented areas of the code body. This copyright notice and the comments
## comments above and below must remain intact at all times.  By using this
## code you agree to indemnify Soholaunch.com, Inc, its coporate agents
## and affiliates from any liability that might arise from its use.
##
## Selling the code for this program without prior written consent is
## expressly forbidden and in violation of Domestic and International
## copyright laws.
###############################################################################

session_start();

# Include core files
include_once($_SESSION['docroot_path']."/sohoadmin/program/includes/product_gui.php");
include_once($_SESSION['docroot_path']."/sohoadmin/program/includes/smt_module.class.php");

#######################################################
### START HTML/JAVASCRIPT CODE
#######################################################

$MOD_TITLE = lang("Site Statistics");
$BG = "shared/stats_bg.jpg";


#######################################################
### CREATE ARCHIVE TABLES IF NEEDED
#######################################################

$st_db_result = mysql_list_tables($db_name);
$st_db_match = 0;
$stdb_i = 0;
while ($stdb_i <= mysql_num_rows ($st_db_result)) {
	$tb_names[$stdb_i] = mysql_tablename($st_db_result, $stdb_i);
	if ($tb_names[$stdb_i] == "STATS_TOP25_ARCHIVE") { $st_db_match++; }
	if ($tb_names[$stdb_i] == "STATS_BYDAY_ARCHIVE") { $st_db_match++; }
	if ($tb_names[$stdb_i] == "STATS_BYHOUR_ARCHIVE") { $st_db_match++; }
	if ($tb_names[$stdb_i] == "STATS_REFER_ARCHIVE") { $st_db_match++; }
	if ($tb_names[$stdb_i] == "STATS_UNIQUE_ARCHIVE") { $st_db_match++; }
	if ($tb_names[$stdb_i] == "STATS_BROWSER_ARCHIVE") { $st_db_match++; }
	$stdb_i++;
}

if ($st_db_match != 6) {
   mysql_db_query("$db_name","CREATE TABLE STATS_TOP25_ARCHIVE (PriKey INT NOT NULL AUTO_INCREMENT PRIMARY KEY,Month CHAR(25),Year INT(4),Page CHAR(25), Hits INT(25), SESSION CHAR(255), Real_Date DATE)");
   mysql_db_query("$db_name","CREATE TABLE STATS_BYDAY_ARCHIVE (PriKey INT NOT NULL AUTO_INCREMENT PRIMARY KEY,Month CHAR(25),Year INT(4),Day CHAR(25), Hits INT(25), SESSION CHAR(255), Real_Date DATE)");
   mysql_db_query("$db_name","CREATE TABLE STATS_BYHOUR_ARCHIVE (PriKey INT NOT NULL AUTO_INCREMENT PRIMARY KEY,Month CHAR(25),Year INT(4),Hour CHAR(25), Hits INT(25), SESSION CHAR(255), Real_Date DATE)");
   mysql_db_query("$db_name","CREATE TABLE STATS_REFER_ARCHIVE (PriKey INT NOT NULL AUTO_INCREMENT PRIMARY KEY,Month CHAR(25),Year INT(4),Refer CHAR(255), Hits INT(25), SESSION CHAR(255), Real_Date DATE)");
   mysql_db_query("$db_name","CREATE TABLE STATS_UNIQUE_ARCHIVE (PriKey INT NOT NULL AUTO_INCREMENT PRIMARY KEY,Month CHAR(25),Year INT(4), IP CHAR(25),Hour CHAR(25), Hits INT(25), Browser CHAR(100), SESSION CHAR(255), Real_Date DATE)");
   mysql_db_query("$db_name","CREATE TABLE STATS_BROWSER_ARCHIVE (PriKey INT NOT NULL AUTO_INCREMENT PRIMARY KEY,Month CHAR(25),Year INT(4),Browser CHAR(255), Hits INT(25), SESSION CHAR(255), Real_Date DATE)");
}


//$result = mysql_query("SELECT * FROM STATS_BROWSER");
//echo "(".mysql_num_rows($result).")<br/>\n";


if($_REQUEST['action'] == "archive_reports"){
   
   $match_not = array("action", "archive_all", "archive_btn", "PHPSESSID");
   
   //mysql_query("INSERT INTO STATS_BROWSER_ARCHIVE SELECT PriKey, Month, Year, Browser, Hits, SESSION, Real_Date FROM STATS_BROWSER") or die (mysql_error());
   
   foreach($_REQUEST as $var=>$val){
      if(!in_array($var, $match_not)){
         
         echo "var = (".$var.") val = (".$val.")<br>\n";
      }
   }
   
}


# So you can write straight HTML without having to build every line into a container var (i.e. $disHTML .= "another line of html")
ob_start();

?>

<script language="javascript">

function killErrors() {
	return true;
}
window.onerror = killErrors;

function SV2_findObj(n, d) { //v3.0
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=SV2_findObj(n,d.layers[i].document); return x;
}
function SV2_showHideLayers() { //v3.0
  var i,p,v,obj,args=SV2_showHideLayers.arguments;
  for (i=0; i<(args.length-2); i+=3) if ((obj=SV2_findObj(args[i]))!=null) { v=args[i+2];
    if (obj.style) { obj=obj.style; v=(v=='show')?'visible':(v='hide')?'hidden':v; }
    obj.visibility=v; }
}
function SV2_popupMsg(msg) { //v1.0
  alert(msg);
}
function SV2_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}

var p = "Site Statistics";
parent.frames.footer.setPage(p);


function check_all(){
   if($('archive_all').checked){
      $('unique_archive').checked=true;
      $('top25_archive').checked=true;
      $('day_archive').checked=true;
      $('hour_archive').checked=true;
      $('referrer_archive').checked=true;
      $('browser_archive').checked=true;
      //$('crawlers_archive').checked=true;
   }else{
      $('unique_archive').checked=false;
      $('top25_archive').checked=false;
      $('day_archive').checked=false;
      $('hour_archive').checked=false;
      $('referrer_archive').checked=false;
      $('browser_archive').checked=false;
      //$('crawlers_archive').checked=false;
   }
}


</script>

<?


// Setup Sub-Mod Navigation to be consistant with V4 GUI
// --------------------------------------------------------------------------

$THIS_DISPLAY = "<form method=\"POST\" action=\"../statistics.php\">\n";

$THIS_DISPLAY .= "<table border=\"0\" cellpadding=\"3\" cellspacing=\"0\" width=\"100%\" align=\"center\" class=\"smtext\">\n";
$THIS_DISPLAY .= "<tr>\n";

$THIS_DISPLAY .= "<td><input type=\"SUBMIT\" name=\"UNIQUE\" value=\"".lang("Unique Visitors")."\" class=\"FormLt1\"></TD>\n";
$THIS_DISPLAY .= "<td><input type=\"SUBMIT\" name=\"TOP25\" value=\"".lang("Top 25 Pages")."\" class=\"FormLt1\"></TD>\n";
$THIS_DISPLAY .= "<td><input type=\"SUBMIT\" name=\"BYDAY\" value=\"".lang("Views By Day")."\" class=\"FormLt1\"></TD>\n";
$THIS_DISPLAY .= "<td><input type=\"SUBMIT\" name=\"BYHOUR\" value=\"".lang("Views By Hour")."\" class=\"FormLt1\"></TD>\n";
$THIS_DISPLAY .= "<td><input type=\"SUBMIT\" name=\"REFERER\" value=\"".lang("Referrer Sites")."\" class=\"FormLt1\"></TD>\n";
$THIS_DISPLAY .= "<td><input type=\"SUBMIT\" name=\"BROWSERS\" value=\"".lang("Browser/OS")."\" class=\"FormLt1\"></TD>\n";
$THIS_DISPLAY .= "<td><input type=\"SUBMIT\" name=\"SPIDERS\" value=\"".lang("Web Crawlers")."\" class=\"FormLt1\"></TD>\n";
$THIS_DISPLAY .= "<td><input type=\"SUBMIT\" name=\"ARCHIVE\" value=\"".lang("Archive Stats")."\" class=\"FormLt1\"></TD>\n";

$THIS_DISPLAY .= "</tr>\n";
$THIS_DISPLAY .= "</table>\n";

$THIS_DISPLAY .= "</form>\n";

$THIS_DISPLAY .= "<style>\n";
$THIS_DISPLAY .= "   ul{\n";
$THIS_DISPLAY .= "      line-height: 2em;\n";
$THIS_DISPLAY .= "   }\n";
$THIS_DISPLAY .= "   ul li{\n";
$THIS_DISPLAY .= "      list-style-type: none;\n";
$THIS_DISPLAY .= "      vertical-align: middle;\n";
$THIS_DISPLAY .= "   }\n";
$THIS_DISPLAY .= "   input{\n";
$THIS_DISPLAY .= "      vertical-align: middle;\n";
$THIS_DISPLAY .= "   }\n";
$THIS_DISPLAY .= "</style>\n";


$THIS_DISPLAY .= "<form method=\"post\" action=\"statistics_archive.php\">\n";
$THIS_DISPLAY .= "<input type=\"hidden\" name=\"action\" value=\"archive_reports\" />\n";

$THIS_DISPLAY .= "<div id=\"outer_archive\">\n";
$THIS_DISPLAY .= "   <h3>Please select the reports you wish to archive.  All selected reports will be sent to archive and cleared out for new data recording.</h3>\n";
$THIS_DISPLAY .= "   <ul>\n";
$THIS_DISPLAY .= "      <li><input type=\"checkbox\" id=\"archive_all\" name=\"archive_all\" onclick=\"check_all();\" />Archive All</li>\n";
$THIS_DISPLAY .= "      <li><input type=\"checkbox\" id=\"unique_archive\" name=\"unique_archive\" />Unique Visitors</li>\n";
$THIS_DISPLAY .= "      <li><input type=\"checkbox\" id=\"top25_archive\" name=\"top25_archive\" />Top 25 Pages</li>\n";
$THIS_DISPLAY .= "      <li><input type=\"checkbox\" id=\"day_archive\" name=\"byday_archive\" />Views By Day</li>\n";
$THIS_DISPLAY .= "      <li><input type=\"checkbox\" id=\"hour_archive\" name=\"byhour_archive\" />Views By Hour</li>\n";
$THIS_DISPLAY .= "      <li><input type=\"checkbox\" id=\"referrer_archive\" name=\"refer_archive\" />Referrer Sites</li>\n";
$THIS_DISPLAY .= "      <li><input type=\"checkbox\" id=\"browser_archive\" name=\"browser_archive\" />Browser/OS</li>\n";
//$THIS_DISPLAY .= "      <li><input type=\"checkbox\" id=\"crawlers_archive\" name=\"crawlers_archive\" />Web Crawlers</li>\n";
$THIS_DISPLAY .= "   </ul>\n";

$THIS_DISPLAY .= "   <input type=\"submit\" name=\"archive_btn\" value=\"Archive Selected Reports\" />\n";

$THIS_DISPLAY .= "</div>\n";
$THIS_DISPLAY .= "</form>\n";





echo $THIS_DISPLAY;

# Grab module html into container var
$module_html = ob_get_contents();
ob_end_clean();

$module = new smt_module($module_html);
$module->meta_title = "Traffic Statistics";
$module->add_breadcrumb_link("Traffic Statistics", "program/modules/mods_full/statistics.php");
$module->icon_img = "skins/".$_SESSION['skin']."/icons/full_size/site_statistics-enabled.gif";
$module->heading_text = "Site Traffic Statistics";
$module->description_text = "Review various reports on the browsing behaviour of your site visitors.";
$module->good_to_go();
?>