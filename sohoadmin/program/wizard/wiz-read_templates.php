<?php
error_reporting(E_PARSE);
if($_GET['_SESSION'] != '' || $_POST['_SESSION'] != '' || $_COOKIE['_SESSION'] != '') { exit; }
 
###############################################################################
## Soholaunch(R) Studio Edition
## Version 1.0
##      
## Author: 			Mike Johnston [mike.johnston@soholaunch.com]                 
## Homepage:	 	http://www.soholaunch.com
## Bug Reports: 	http://bugzilla.soholaunch.com
###############################################################################

##############################################################################
## COPYRIGHT NOTICE                                                     
## Copyright 1999-2004 Soholaunch.com, Inc.  All Rights Reserved.       
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


// Read Template Directory and Setup the "Select Option Values"

$directory = "../modules/site_templates/pages";	// Set Template Directory

$TEMPLATE_OPTIONS = "";		// Clear Global
$CATEGORY_LIST = "";
$cat_list = "";
$checkit = "";

if ($this_template == "default") { $sel = "SELECTED"; } else { $sel = ""; }
$TEMPLATE_OPTIONS = "<option value=\"default\" $sel>[site base template]</option>\n";
$CATEGORY_LIST = "<option value=\"default\">".lang("Select a Category")."...</option>\n";

$handle = opendir("$directory");

$a=0;

while ($files = readdir($handle)) {
	if (strlen($files) > 2 && $files != "default") {
	
		$template_name = eregi_replace("_", " ", $files);
		$template_name = strtolower($template_name);
		
		$tmp = split("-", $template_name);		
		if (!eregi($tmp[0], $checkit)) { $cat_list[$a] .= $files."~~~".ucwords($tmp[0]); $checkit .= "$tmp[0];"; }
		
		$tmp_display = ucwords($tmp[0])." > ".ucwords($tmp[1]);		
		if (!eregi("none", $tmp[2])) { $tmp_display .= "  (".ucwords($tmp[2]).")"; }		
		$tmp_value[$a] = $files."~~~".$tmp_display;
		
		$a++;
	}
	
}
closedir($handle);

sort($tmp_value);
sort($cat_list);
$default_category = "";

for ($z=0;$z<=$a;$z++) {
	if ($tmp_value[$z] != "") {
			if ($cat_list[$z] != "") {
				$cd = split("~~~", $cat_list[$z]);
				$tcat = strtolower($cd[1]);
				$tcat = ucwords($tcat);
				$cd[1] = eregi_replace(" ", "_", $cd[1]);
				$CATEGORY_LIST .= "<option value=\"$cd[1]\">$tcat</option>\n";
				if ($default_category == "") { $default_category = "$cd[1]"; }
			}
		$td = split("~~~", $tmp_value[$z]);
		$counter = $z+1;
		if ($this_template == "$td[0]") { $sel = "SELECTED"; } else { $sel = ""; }
		$TEMPLATE_OPTIONS .= "<option value=\"".$td[0]."\" $sel>".$td[1]."</option>\n";
	} // End If
} // End $z

if ($CATONLY == "on") {
	echo $CATEGORY_LIST;
} else {
	echo $TEMPLATE_OPTIONS;
}

?>