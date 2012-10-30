<?php
error_reporting(E_PARSE);
if($_GET['_SESSION'] != '' || $_POST['_SESSION'] != '' || $_COOKIE['_SESSION'] != '') { exit; }


###############################################################################
## Soholaunch(R) Site Management Tool
## Version 4.9
##
## Author: 			Mike Johnston [mike.johnston@soholaunch.com]
## Homepage:	 	http://www.soholaunch.com
## Bug Reports: 	http://bugzilla.soholaunch.com
## Release Notes:	sohoadmin/build.dat.php
###############################################################################

##############################################################################
## COPYRIGHT NOTICE
## Copyright 1999-2007 Soholaunch.com, Inc.  All Rights Reserved.
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
include("../includes/product_gui.php");

#######################################################
### START HTML/JAVASCRIPT CODE			    ###
#######################################################

$MOD_TITLE = "Meta Tag Data";


if ($action == "savemeta") {

	$filename = "$cgi_bin/meta.conf";

	$site_description = eregi_replace("\n", "", $site_description);
	$site_description = eregi_replace("\r", "", $site_description);

	$site_keywords = eregi_replace("\n", "", $site_keywords);
	$site_keywords = eregi_replace("\r", ",", $site_keywords);
	$site_keywords = eregi_replace(",,", ", ", $site_keywords);

	$site_title = eregi_replace("\n", "", $site_title);
	$site_title = eregi_replace("\r", "", $site_title);

	$file = fopen("$filename", "w");

	fwrite($file, "site_description=$site_description\n");
	fwrite($file, "site_keywords=$site_keywords\n");
	fwrite($file, "site_title=$site_title\n");
	fwrite($file, "splash_bg=$splash_bg\n");

	fclose($file);

//	header("Location: webmaster.php?=SID");
//	exit;

}

#######################################################
### READ META.CONF FILE FOR CURRENT SETTINGS	    ###
#######################################################

$filename = "$cgi_bin/meta.conf";

if (file_exists($filename)) {
	$file = fopen("$filename", "r");
		$body = fread($file,filesize($filename));
	fclose($file);
	$lines = split("\n", $body);
	$numLines = count($lines);
	for ($x=0;$x<=$numLines;$x++) {
		$temp = split("=", $lines[$x]);
		$variable = $temp[0];
		$value = $temp[1];
		${$variable} = $value;
	}
}


###############################################################################
###############################################################################
function enc($v) {
	$v = md5($v);
	return $v;
}
$SECURE_MOD_LICENSE = 0;
$tmp = eregi_replace("tmp_content", "", $cgi_bin);
$filename = $tmp."filebin/soholaunch.lic";
$file = fopen("$filename", "r");
	$data = fread($file,filesize($filename));
fclose($file);
$keydata = split("\n", $data);
// Security
$check_sum = enc("secure");
if (trim($keydata[7]) == $check_sum) {
	$SECURE_MOD_LICENSE = 1;
} else {
	$SECURE_MOD_LICENSE = 0;
}
###############################################################################
###############################################################################
# Start buffering output
ob_start();
?>



<script language="JavaScript">
<!--
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

function navto(where) {
	window.location = where+"?<?=SID?>";
}

SV2_showHideLayers('addCartMenu?header','','hide');
SV2_showHideLayers('blankLayer?header','','hide');
SV2_showHideLayers('linkLayer?header','','hide');
SV2_showHideLayers('newsletterLayer?header','','hide');
SV2_showHideLayers('cartMenu?header','','show');
SV2_showHideLayers('menuLayer?header','','hide');
SV2_showHideLayers('editCartMenu?header','','hide');

//-->
</script>

<style type="text/css">
legend {
   font-weight: bold;
   font-size: 125%;
}
</style>

<?

####################################################################
### FOR VISUAL CONSISTANCY; WE USE AN HTML TEMPLATE BUILDER FILE
### LOCATED IN THE /shared FOLDER.  THIS WAY ALL OF OUR MODULE
### INTERFACES LOOK THE SAME. YOU MUST SUPPLY THE VARIABLES:
###
### $MOD_TITLE		Title of this Module
### $THIS_DISPLAY		HTML Content to display to end user
### $BG 			Background Image for content table if used
###
### THIS SAME METHOD SHOULD BE USED WHEN BUILDING ANY OF YOUR OWN
### CUSTOM MODULES.  REMEMBER TO INCLUDE THE HEADER "INCLUDES"
### ABOVE FOR PROPER FUNCTIONALITY WITHIN THE APPLICAITON.
####################################################################

$THIS_DISPLAY = "";

# Webmaster nav button row
include("webmaster_nav_buttons.inc.php");

$THIS_DISPLAY .= "<fieldset>\n";
$THIS_DISPLAY .= " <legend>".lang("Default meta tag data for search engines")."</legend>\n";
$THIS_DISPLAY .= "<TABLE BORDER=0 CELLPADDING=5 CELLSPACING=0 CLASS=text WIDTH=100%>\n";
$THIS_DISPLAY .= " <tr>\n";
$THIS_DISPLAY .= "<TD ALIGN=CENTER VALIGN=TOP>\n";
$THIS_DISPLAY .= '				  <form method=post action="meta_data.php">
                                  <input type=hidden name=action value="savemeta">

                                  <table width="99%" border="0" cellspacing="0" cellpadding="10">
                                    <tr>
                                      <td align="left" valign="top" width="50%"><font style="font-family: Arial; font-size: 9pt;"><U>'.lang("Web Site Title").'</U>:</font><br>
                                        <font style="font-family: Arial; font-size: 8pt;">
                                        ('.lang("This will be displayed at the top of the browser window on all pages of your site.").')<br>
                                        <input style="width: 300px;" type="text" name="site_title" size="35" class=text value="'.$site_title.'">
                                        </font></td>
                                      <td align="left" valign="top"><font style="font-family: Arial; font-size: 9pt;">
                                       <U>'.lang("Web Site Description").'</U>:</font><br>
                                        <font style="font-family: Arial; font-size: 8pt;">
                                        ('.lang("This is a Meta Tag that helps search engines classify your web site.").'
                                        '.lang("This should be a small sentance that describes your site.").')<br>
                                        <input style="width: 300px;" class=text type="text" name="site_description" size="35" value="'.$site_description.'">
                                        </FONT></td>
                                    </tr>
                                    <tr>
                                      <td><font style="font-family: Arial; font-size: 9pt;">
                                       <U>'.lang("Web Site Keywords").'</U>:</font><br>
                                        <font style="font-family: Arial; font-size: 8pt;">
                                        ('.lang("This is a Meta Tag that some search engines use to search your site with.").'
                                        '.lang("Please enter each keyword separated by a comma.").'
                                        '.lang("There is no need to use line feeds or carriage returns in the field.").')</font><br>
                                        <textarea name="site_keywords" class=text cols="30" rows="8" style="width: 300px;">'.$site_keywords.'</textarea>
                                      </td>
  									  <td align=center valign=middle class=text>

									  <font color=red>[ '.lang("Note: Individual Meta Tag Data can be edited from Page Properties while editing the page.").' ]</font><BR><BR><BR>


									  <input type="submit" name="Submit" '.$btn_save.' value="'.lang("Save Meta Tag Data").'"></td>
                                          </tr>
                                        </table>

                                </form>
						';


		$THIS_DISPLAY .= "</TD></TR></TABLE>\n";
$THIS_DISPLAY .= "</fieldset>\n";
echo $THIS_DISPLAY;


# pophelp-replace_homelinks
$popup = "";
$popup .= "<p>".lang("If this option is set to 'yes' any links in your site's menu navigation system that point to your")." default/home/start page (".startpage().") \n";
$popup .= lang("will instead point to your root url")." (http://".$_SESSION['this_ip']."). ".lang("This helps prevent search engines from penalizing your site for having the same content")."\n";
$popup .= lang("on")." http://".$_SESSION['this_ip']."/".pagename(startpage())." ".lang("as you have on")." http://".$_SESSION['this_ip']."</p>";
echo help_popup("pophelp-replace_homelinks", lang("Replace home links with domain root url"), $popup);

# SEO preferences
$webmasterpref = new userdata("webmaster");
if ( $webmasterpref->get("replace_homelinks") == "" ) { $webmasterpref->set("replace_homelinks", "no"); }
if ( $_GET['replace_homelinks'] != "" ) { $webmasterpref->set("replace_homelinks", $_GET['replace_homelinks']); }
?>
<fieldset>
 <legend>Miscellaneous SEO-related Preferences</legend>
 <label><span class="help_link" onclick="toggleid('pophelp-replace_homelinks');">[?]</span>Replace menu links to "<?php echo startpage(); ?>" with domain root url?</label>
 <select id="replace_homelinks" name="prefs[replace_homelinks]" onchange="document.location.href='meta_data.php?replace_homelinks='+this.value;">
  <option value="yes"><? echo lang("Yes"); ?></option>
  <option value="no"><? echo lang("No"); ?></option>
 </select>
</fieldset>

<script type="text/javascript">
$('replace_homelinks').value = '<?php echo $webmasterpref->get("replace_homelinks"); ?>';
</script>

<?php

####################################################################

# Grab module html into container var
$module_html = ob_get_contents();
ob_end_clean();

$instructions = lang("This is one of the most important pieces of your website.  Enter your site title, description and keywords so that search engines can find your site!");

# Build into standard module template
$module = new smt_module($module_html);
$module->add_breadcrumb_link(lang("Webmaster"), "program/webmaster/webmaster.php");
$module->add_breadcrumb_link(lang("Search Engine Ranking"), "program/webmaster/meta_data.php");
$module->icon_img = "skins/".$_SESSION['skin']."/icons/webmaster-enabled.gif";
$module->heading_text = lang("Search Engine Ranking");
$module->description_text = $instructions;
$module->good_to_go();
?>