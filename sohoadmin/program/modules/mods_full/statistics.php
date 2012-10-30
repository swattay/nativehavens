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
### START HTML/JAVASCRIPT CODE			    ###
#######################################################

$MOD_TITLE = lang("Site Statistics");
$BG = "shared/stats_bg.jpg";

# So you can write straight HTML without having to build every line into a container var (i.e. $disHTML .= "another line of html")
ob_start();

if (isset($ARCHIVE)) {
   header ("Location: statistics/statistics_archive.php");
   exit;   
}

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

</script>

<?

// Determine which stat mod to pull into IFRAME display.  This was treated
// with an IFRAME because we had just re-written the statistics routine
// weeks prior to the V4 project; therefore we simply ported over the stats
// routine from Version 3 with session and "real sql date" mods for V4.
// --------------------------------------------------------------------------

if ($STAT_SHOW == "" || isset($UNIQUE)) {
	$STAT_SHOW = "statistics/includes/unique.php";
	//$STAT_SHOW = "http://".$_SESSION['docroot_url']."/sohoadmin/program/modules/mods_full/".$STAT_SHOW;
	//$STAT_SHOW = "shopping_cart.php";
}

if (isset($TOP25)) { $STAT_SHOW = "statistics/includes/top25.php?SID=".SID; }
if (isset($BYDAY)) { $STAT_SHOW = "statistics/includes/byday.php?SID=".SID; }
if (isset($BYHOUR)) { $STAT_SHOW = "statistics/includes/byhour.php?SID=".SID; }
if (isset($REFERER)) { $STAT_SHOW = "statistics/includes/refer.php?SID=".SID; }
if (isset($BROWSERS)) { $STAT_SHOW = "statistics/includes/browser.php?SID=".SID; }
if (isset($SPIDERS)) { $STAT_SHOW = "statistics/includes/spiders.php?SID=".SID; }
// Setup Sub-Mod Navigation to be consistant with V4 GUI
// --------------------------------------------------------------------------

$THIS_DISPLAY = "<form method=\"POST\" action=\"statistics.php\">\n";

$THIS_DISPLAY .= "<table border=\"0\" cellpadding=\"3\" cellspacing=\"0\" width=\"100%\" align=\"center\" class=\"smtext\">\n";
$THIS_DISPLAY .= "<tr>\n";

$THIS_DISPLAY .= "<td><input type=\"SUBMIT\" name=\"UNIQUE\" value=\"".lang("Unique Visitors")."\" class=\"FormLt1\"></TD>\n";
$THIS_DISPLAY .= "<td><input type=\"SUBMIT\" name=\"TOP25\" value=\"".lang("Top 25 Pages")."\" class=\"FormLt1\"></TD>\n";
$THIS_DISPLAY .= "<td><input type=\"SUBMIT\" name=\"BYDAY\" value=\"".lang("Views By Day")."\" class=\"FormLt1\"></TD>\n";
$THIS_DISPLAY .= "<td><input type=\"SUBMIT\" name=\"BYHOUR\" value=\"".lang("Views By Hour")."\" class=\"FormLt1\"></TD>\n";
$THIS_DISPLAY .= "<td><input type=\"SUBMIT\" name=\"REFERER\" value=\"".lang("Referrer Sites")."\" class=\"FormLt1\"></TD>\n";
$THIS_DISPLAY .= "<td><input type=\"SUBMIT\" name=\"BROWSERS\" value=\"".lang("Browser/OS")."\" class=\"FormLt1\"></TD>\n";
$THIS_DISPLAY .= "<td><input type=\"SUBMIT\" name=\"SPIDERS\" value=\"".lang("Web Crawlers")."\" class=\"FormLt1\"></TD>\n";
//$THIS_DISPLAY .= "<td><input type=\"SUBMIT\" name=\"ARCHIVE\" value=\"".lang("Archive Stats")."\" class=\"FormLt1\"></TD>\n";

$THIS_DISPLAY .= "</tr>\n";

$THIS_DISPLAY .= "<tr><td colspan=\"7\" align=\"left\" valign=\"middle\" class=\"text\">\n";
$THIS_DISPLAY .= "<FONT COLOR=\"#999999\">".lang("You should empty your log tables at least every six months or so depending on traffic.");
$THIS_DISPLAY .= "&nbsp;".lang("If you experience slowness in loading reports, your log tables have probably gone unattended for some time.")."</FONT>\n";
$THIS_DISPLAY .= "</td></tr>\n";

$THIS_DISPLAY .= "</table>\n";

$THIS_DISPLAY .= "</form>\n";

//$THIS_DISPLAY .= "I-Frame src=\"".$STAT_SHOW."\"<br>";
$THIS_DISPLAY .= "<iframe width=\"740\" height=\"325\" border=\"0\" style=\"border: 2px solid #000000;\" src=\"".$STAT_SHOW."\" scroll=\"auto\" align=\"center\" valign=\"top\"></iframe>\n";

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