<?php

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
include($_SESSION['product_gui']);

ob_start(); 
	include("shared/base.html");
	$template_html .= ob_get_contents(); 
ob_end_clean(); 

// Build Section Colors
// ----------------------------------------------------------------------------

if ($TEMP_STYLE == "BLANK") {
	$SECTION1 = $BCOLOR;
	$SECTION2 = $BCOLOR;
	$SECTION3 = $BCOLOR;
	$SECTION4 = $BCOLOR;
	$SECTION5 = $BCOLOR;
	$SECTION6 = $BCOLOR;
}

if ($TEMP_STYLE == "LEFTBAR") {
	$SECTION1 = $FCOLOR;
	$SECTION2 = $BCOLOR;
	$SECTION3 = $FCOLOR;
	$SECTION4 = $BCOLOR;
	$SECTION5 = $FCOLOR;
	$SECTION6 = $BCOLOR;
}

if ($TEMP_STYLE == "LSHAPE") {
	$SECTION1 = $FCOLOR;
	$SECTION2 = $FCOLOR;
	$SECTION3 = $FCOLOR;
	$SECTION4 = $BCOLOR;
	$SECTION5 = $FCOLOR;
	$SECTION6 = $BCOLOR;
}

if ($TEMP_STYLE == "USHAPE") {
	$SECTION1 = $FCOLOR;
	$SECTION2 = $FCOLOR;
	$SECTION3 = $FCOLOR;
	$SECTION4 = $BCOLOR;
	$SECTION5 = $FCOLOR;
	$SECTION6 = $FCOLOR;
}

if ($TEMP_STYLE == "PRO") {
	$SECTION1 = $FCOLOR;
	$SECTION2 = $FCOLOR;
	$SECTION3 = $BCOLOR;
	$SECTION4 = $BCOLOR;
	$SECTION5 = $FCOLOR;
	$SECTION6 = $FCOLOR;
}
	
// -------------------------------------------------------------------
// Setup Menu Example (Based on current auto-menu settings
// --------------------------------------------------------------------

$menu_example = "";
$menu_example .= "<TABLE BORDER=0 CELLPADDING=2 CELLSPACING=0>\n";
	$menu_example .= "<TR><TD ALIGN=LEFT VALIGN=TOP CLASS=menusys><a href=\"#\">About Us</a></TD></TR>\n";
	$menu_example .= "<TR><TD ALIGN=LEFT VALIGN=TOP CLASS=menusys><a href=\"#\">Products</a></TD></TR>\n";
	$menu_example .= "<TR><TD ALIGN=LEFT VALIGN=TOP CLASS=menusys><a href=\"#\">Services</a></TD></TR>\n";
	$menu_example .= "<TR><TD ALIGN=LEFT VALIGN=TOP CLASS=menusys><a href=\"#\">Contact Us</a></TD></TR>\n";
	$menu_example .= "<TR><TD ALIGN=LEFT VALIGN=TOP CLASS=menusys><a href=\"#\">Home Page</a></TD></TR>\n";
$menu_example .= "</TABLE>\n\n";

// -------------------------------------------------------------------

// -------------------------------------------------------------------
// Setup Content Example (Read Real Home Page Data)
// --------------------------------------------------------------------

$content_example = "<BR><BR><TABLE BORDER=0 CELLPADDING=2 CELLSPACING=0 ALIGN=LEFT><TR><TD ALIGN=LEFT VALIGN=TOP CLASS=contentsys>     ";

ob_start(); 
	$filename = "content.txt";
	include("$filename");
	$stuff = ob_get_contents(); 
ob_end_clean(); 

for ($x=1;$x<=4;$x++) {
	$content_example .= $stuff;
}

$content_example .= "<BR><BR>\n";

for ($x=1;$x<=9;$x++) {
	$content_example .= $stuff;
}

$content_example .= " <a href=\"#\">Simulated Link...</a><BR><BR>\n";

for ($x=1;$x<=6;$x++) {
	$content_example .= $stuff;
}

$content_example .= " <a href=\"#\">Simulated Link...</a></td></tr></table>\n";

// -------------------------------------------------------------------

if ($IMAGE != "NOIMAGE") { 
	$IMAGE = "http://$this_ip/images/$IMAGE";
} else {
	$IMAGE = "library/noimage.gif";
}

// -------------------------------------------------------------------


// READ HEADER TEXT IF AVAILABLE
// -------------------------------------------------------------------	

$filename = "$cgi_bin/logo.conf";
if (file_exists("$filename")) {
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
} else {
	$headertext = "Website Title";
}

// -------------------------------------------------------------------

$template_html = eregi_replace("#SEC1#", "#$SECTION1", $template_html);
$template_html = eregi_replace("#SEC2#", "#$SECTION2", $template_html);
$template_html = eregi_replace("#SEC3#", "#$SECTION3", $template_html);
$template_html = eregi_replace("#SEC4#", "#$SECTION4", $template_html);
$template_html = eregi_replace("#SEC5#", "#$SECTION5", $template_html);
$template_html = eregi_replace("#SEC6#", "#$SECTION6", $template_html);

$template_html = eregi_replace("#FCOLOR#", "#$FCOLOR", $template_html);
$template_html = eregi_replace("#BCOLOR#", "#$BCOLOR", $template_html);
$template_html = eregi_replace("#TCOLOR#", "#$TCOLOR", $template_html);
$template_html = eregi_replace("#TXTCOLOR#", "#$TXTCOLOR", $template_html);
$template_html = eregi_replace("#LCOLOR#", "#$LCOLOR", $template_html);
$template_html = eregi_replace("#IMAGE#", "$IMAGE", $template_html);


$template_html = eregi_replace("#LOGO#", "$headertext", $template_html);
$template_html = eregi_replace("#VMENU#", "$menu_example", $template_html);
$template_html = eregi_replace("#CONTENT#", "$content_example", $template_html);
$template_html = eregi_replace("#TMENU#", "", $template_html);
$template_html = eregi_replace("#TITLE#", "Template Preview", $template_html);

echo $template_html;
			
####################################################################

echo "<SCRIPT LANGUAGE=Javascript> window.focus(); </SCRIPT>\n";

?>