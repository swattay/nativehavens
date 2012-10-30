<?php
//apd_set_pprof_trace();

error_reporting(E_PARSE);
if($_GET['_SESSION'] != '' || $_POST['_SESSION'] != '' || $_COOKIE['_SESSION'] != '') { exit; }


session_start();
include("../../includes/product_gui.php");

//echo "Sever Name: (".$_SERVER[HTTP_HOST].")\n"; exit;
$dis_site = $_SESSION['docroot_url'];

//=========================================================================
//			 ___     _               _
//			| __|_ _| |_ ___ _ _  __(_)___ _ _   __ ____ _ _ _ ___
//			| _|\ \ /  _/ -_) ' \(_-< / _ \ ' \  \ V / _` | '_(_-<
//			|___/_\_\\__\___|_||_/__/_\___/_||_|  \_/\__,_|_| /__/
//
//=========================================================================

//============================================================
//================= Pull All Extension Vars ==================
//============================================================
$mod_props = special_hook('page_editor_object');

# UNCOMMENT TO TEST ADDON VALUES PULLED FROM INSTALL MANIFEST / system_hook_attachments TABLE
//foreach($mod_props as $var=>$val){
//   //echo "var = (".$var.") val = (".$val.")<br>";
//   foreach($val as $var1=>$val1){
//
//   	if($var1 == "draggable_object_image"){
//   		$varText[] = "<b>draggable_object_image = (".$val1.")</b><br/>Draggable image shown on the Addons menu.";
//   		$varDisplay[] = "<img align=\"absmiddle\" src=\"../../../plugins/#PLUGIN_FOLDER#/".$val1."\" width=\"80\" height=\"18\" vspace=\"1\" hspace=\"1\" style=\"cursor: hand;\">";
//   		$folder_name[] = "";
//   	}
//   	if($var1 == "draggable_object_id"){
//   		$varText[] = "<b>draggable_object_id  = (".$val1.")</b><br/>Unique id for your draggable image.  When your plugin is dropped on the page, this is how we know its yours.";
//   		$varDisplay[] = $val1;
//   		$folder_name[] = "";
//   	}
//   	if($var1 == "properties_dialog_id"){
//   		$varText[] = "<b>properties_dialog_id = (".$val1.")</b><br/>Unique id for this plugins properites layer.  When this plugin is dropped on the page, we 'show' (".$val1.").";
//   		$varDisplay[] = $val1;
//   		$folder_name[] = "";
//   	}
//   	if($var1 == "plugin_folder"){
//   		$varText[] = "<b>plugin_folder / mod_folder = (".$val1.")</b><br/>Name of the plugin folder.";
//   		$varDisplay[] = $val1;
//   		$folder_name[] = $val1;
//   	}
//   }
//	$varText[] = "<b>-----------------------------------------------------------------------------------------</b>";
//	$varDisplay[] = "<b>-------------------------------------------------</b>";
//}
//
//$num_mods = count($varText);
//for($x=0;$x<$num_mods;$x++){
//	if($folder_name[$x] != ""){ $folder = $folder_name[$x]; }
//	//if($folder_name[$x] != ""){ echo "(".$folder_name[$x].")<br/>"; }
//	//echo "(".$folder_name[$x].")";
//	$testHTML .= "  <tr>\n";
//	$testHTML .= "    <td bgcolor=\"#336699\" style=\"width:60%; color:#FFFFFF; border: 1px solid #CCCCCC;\">".$varText[$x]."</td>\n";
//	$testHTML .= "    <td bgcolor=\"#CCCCCC\">".$varDisplay[$x]."</td>\n";
//	$testHTML .= "  </tr>\n";
//	if($folder != ""){
//		$testHTML = eregi_replace("#PLUGIN_FOLDER#", $folder, $testHTML);
//		$folder = "";
//	}
//}
//echo "<h3>ADDON VALUES PULLED FROM INSTALL MANIFEST / system_hook_attachments TABLE</h3>\n";
//echo "<div style=\"width:100%; height:80%; overflow: scroll;\">\n";
//echo "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" style=\"overflow: scroll;\">\n";
//echo $testHTML;
//echo "</table>\n";
//echo "</div>\n";
//exit;

// Count extensions
$extend_count = count($mod_props);


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


// *********************************************************************
// ** The page editor module is the most complex module of the system.**
// ** It utilizes PHP to create the Microsoft(tm) JScript functions   **
// ** needed to allow the drag 'n' drop operation of the editing GUI. **
// ** 									       						  **
// ** It is advised that you do not edit this script unless you are   **
// ** VERY familiar with Microsoft's JScript language and operation.  **
// **                                                                 **
// *********************************************************************

track_vars;

// Control Initial Background Colors, Etc.
// ---------------------------------------------------------------------

$header_background="menu";
$header_text="menutext";
$background="oldlace";

$option_background="white";
$text="black";
$link="lightskyblue";
$mouseover="buttonhighlight";

##########################################################################################
## If this script is opened with out a current page to edit, redirect
## user to open page script to select a page to edit.
##
## DEVNOTE: All redirects must occur before any headers are sent to browser
##########################################################################################

if ( $_GET['currentPage'] == "" ) {
	header ("Location: ../open_page.php?=SID");
	exit;
}



##########################################################################################
## INCLUDE: initialize.php
##
## The initialize.php script handles reading of the current setup into memory and
## the setting up of the current page HTML as the editor will see it.
##########################################################################################

include("initialize.php");

##########################################################################################
## Do not remove Comment tags in the HTML HEAD of this document.  It is restricted by
## your license agreement.  Please read in full before using.
##########################################################################################

?>

<html>
<head>
<title>Page Editor</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<META HTTP-EQUIV="Expires" CONTENT="-1">
<link rel="stylesheet" type="text/css" href="../../product_gui.css">
<link rel="stylesheet" type="text/css" href="includes/page_editor.css">
<script language="JavaScript" src="../../includes/display_elements/js_functions.php"></script>
<script language="javascript" type="text/javascript" src="../tiny_mce/tiny_mce.js"></script>
<script language="javascript" type="text/javascript" src="includes/drop_cell.js"></script>
<script language="javascript" type="text/javascript" src="includes/general.js"></script>
<script type="text/javascript" src="../tiny_mce/plugins/media/jscripts/embed.js"></script>

<!--
************************************************************************
** PAGE EDITING SCRIPT AND WORD PROCESSOR BY MIKE JOHNSTON            **
** Email: mike@soholaunch.com;mike@mikejsolutions.com                 **
**                                                                    **
** Used under licensed for individual site use only. (c)copyright     **
** Soholaunch.com, Inc. & Mike Johnston respectivley                  **
************************************************************************
Modified by Joe Lain for Firefox Compatibility and PinEdit text editor and then again for TinyMCE editor
-->


<script language="javascript">

<?php

##########################################################################################
## If preview page was selected from the main menu, open another browser window
## and call this specific page to view
##########################################################################################
if ($previewWindow == 1) {
	$previewWindow = 0;
	$thisPage = eregi_replace(" ", "_", $_GET['currentPage']);
	echo ("MM_openBrWindow('http://$this_ip/index.php?pr=$thisPage&nosessionkill=1','prevwindow','width=790,height=450, status=yes, scrollbars=yes,resizable=yes,toolbar=yes');\n\n");
}

?>

function show_screen() {
	show_hide_layer('NOSAVE_LAYER','','hide');
	show_hide_layer('userOpsLayer','','show');

	var p = 'Editing Page: <? echo $_GET['currentPage']; ?>';
	parent.frames.footer.setPage(p);

	disable_links();
}

// This is the main javascript workhorse
// object_drops defines all routines that
// run when something is dropped into a cell
<?
include("object_drops.php");

# sitepal?
if ( sitepal_allowed() ) {
   $sitepal_objs = "\"sitepal\", ";
   $sitepal_element_show = "dd.elements.MYsitepal.show();\n";
   $sitepal_element_hide = "dd.elements.MYsitepal.hide(true);\n";
}
?>
function show_hide_dfobjects(showorhide) {
   var default_objs = ["images", "editor", "forms", "docs", <? echo $sitepal_objs; ?>"auth", "cust", "shop", "search", "signup", "calendar", "date", "print"];
   var dfobjs = default_objs.concat("email", "counter", "popup", "audio", "video", "plugin", "photo", "blog", "faq", "directions", "delete");

   var maxstop = dfobjs.length;

   for ( x = 0; x < maxstop; x++ ) {
      // Put object name back together
      var obj_name = "MY"+dfobjs[x];

      // Show or hide?
      if ( showorhide == "hide" ) {
         // Hide
         eval("dd.elements."+obj_name+".hide()");

      } else {
         // Show
         eval("dd.elements."+obj_name+".show()");
      }
   }
} // End show_hide_dfobjects()


function show_hide_icons(){
	// HIDE OBJECT BAR DIV
	if($('objectbar').style.display=='none'){
		//alert('something');
		$('objectbar').style.display='block';
		dd.elements.MYimages.show();
		dd.elements.MYeditor.show();
		dd.elements.MYforms.show();
		dd.elements.MYdocs.show();
		dd.elements.MYpdfs.show();
		dd.elements.MYauth.show();
		dd.elements.MYcust.show();
		dd.elements.MYshop.show();
		dd.elements.MYsearch.show();
		dd.elements.MYsignup.show();
		dd.elements.MYcalendar.show();
		dd.elements.MYdate.show();
		dd.elements.MYprint.show();
		dd.elements.MYemail.show();
<?
if ( sitepal_allowed() ) {
   echo "		dd.elements.MYsitepal.show();\n";
}
?>
		dd.elements.MYcounter.show();
		dd.elements.MYpopup.show();
		dd.elements.MYaudio.show();
		dd.elements.MYvideo.show();
		dd.elements.MYplugin.show();
		dd.elements.MYphoto.show();
		dd.elements.MYblog.show();
		dd.elements.MYfaq.show();
		dd.elements.MYdirections.show();
		dd.elements.MYdelete.show();
	}else{
		//alert('something else');
		$('objectbar').style.display='none';
		// HIDE DRAGGABLE IMAGES
		dd.elements.MYimages.hide(true);
		dd.elements.MYeditor.hide(true);
		dd.elements.MYforms.hide(true);
		dd.elements.MYdocs.hide(true);
		dd.elements.MYpdfs.hide(true);
		dd.elements.MYauth.hide(true);
		dd.elements.MYcust.hide(true);
		dd.elements.MYshop.hide(true);
		dd.elements.MYsearch.hide(true);
		dd.elements.MYsignup.hide(true);
		dd.elements.MYcalendar.hide(true);
		dd.elements.MYdate.hide(true);
		dd.elements.MYprint.hide(true);
		dd.elements.MYemail.hide(true);
<?
if ( sitepal_allowed() ) {
   echo "		dd.elements.MYsitepal.hide(true);\n";
}
?>
		dd.elements.MYcounter.hide(true);
		dd.elements.MYpopup.hide(true);
		dd.elements.MYaudio.hide(true);
		dd.elements.MYvideo.hide(true);
		dd.elements.MYplugin.hide(true);
		dd.elements.MYphoto.hide(true);
		dd.elements.MYblog.hide(true);
		dd.elements.MYfaq.hide(true);
		dd.elements.MYdirections.hide(true);
		dd.elements.MYdelete.hide(true);
	}
}

function show_mods(){
	// HIDE OBJECT BAR DIV
	if($('objectbar').style.display=='none'){
		//alert('something');
		$('objectbar').style.display='block';
		$('objectbar_mods').style.display='none';
		dd.elements.MYimages.show();
		dd.elements.MYeditor.show();
		dd.elements.MYforms.show();
		dd.elements.MYdocs.show();
		dd.elements.MYpdfs.show();
		dd.elements.MYauth.show();
		dd.elements.MYcust.show();
		dd.elements.MYshop.show();
		dd.elements.MYsearch.show();
		dd.elements.MYsignup.show();
		dd.elements.MYcalendar.show();
		dd.elements.MYdate.show();
		dd.elements.MYprint.show();
		dd.elements.MYemail.show();
<?
if ( sitepal_allowed() ) {
   echo "		dd.elements.MYsitepal.show();\n";
}
?>
		dd.elements.MYcounter.show();
		dd.elements.MYpopup.show();
		dd.elements.MYaudio.show();
		dd.elements.MYvideo.show();
		dd.elements.MYplugin.show();
		dd.elements.MYphoto.show();
		dd.elements.MYblog.show();
		dd.elements.MYfaq.show();
		dd.elements.MYdirections.show();
		dd.elements.MYdelete.show();


	}else{
		//alert('something else');
		$('objectbar').style.display='none';
		// HIDE DRAGGABLE IMAGES
		dd.elements.MYimages.hide(true);
		dd.elements.MYeditor.hide(true);
		dd.elements.MYforms.hide(true);
		dd.elements.MYdocs.hide(true);
		dd.elements.MYpdfs.hide(true);
		dd.elements.MYauth.hide(true);
		dd.elements.MYcust.hide(true);
		dd.elements.MYshop.hide(true);
		dd.elements.MYsearch.hide(true);
		dd.elements.MYsignup.hide(true);
		dd.elements.MYcalendar.hide(true);
		dd.elements.MYdate.hide(true);
		dd.elements.MYprint.hide(true);
		dd.elements.MYemail.hide(true);
<?
if ( sitepal_allowed() ) {
   echo "		dd.elements.MYsitepal.hide(true);\n";
}
?>
		dd.elements.MYcounter.hide(true);
		dd.elements.MYpopup.hide(true);
		dd.elements.MYaudio.hide(true);
		dd.elements.MYvideo.hide(true);
		dd.elements.MYplugin.hide(true);
		dd.elements.MYphoto.hide(true);
		dd.elements.MYblog.hide(true);
		dd.elements.MYfaq.hide(true);
		dd.elements.MYdirections.hide(true);
		dd.elements.MYdelete.hide(true);

		$('objectbar_mods').style.display='block';

		// ADD DRAGGABLE IMAGES (ADDONS/MODS)
		<?
		//echo "alert('".$extend_count."');";

		for($x=0;$x<$extend_count;$x++){
			echo "var mod".$x."_id = '".$mod_props[$x]['draggable_object_id']."';\n";
			echo "ADD_DHTML('".$mod_props[$x]['draggable_object_id']."'+CLONE);\n";
			//echo "dd.elements.".$mod_props[$x]['draggable_object_id'].".show();\n";
		}
		?>
	}


}

//      valid_elements : ""
//      +"a[accesskey|charset|class|coords|dir<ltr?rtl|href|hreflang|id|lang|name"
//        +"|onblur|onclick|ondblclick|onfocus|onkeydown|onkeypress|onkeyup"
//        +"|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|rel|rev"
//        +"|shape<circle?default?poly?rect|style|tabindex|title|target|type],"
//      +"abbr[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
//        +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
//        +"|title],"
//      +"acronym[class|dir<ltr?rtl|id|id|lang|onclick|ondblclick|onkeydown|onkeypress"
//        +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
//        +"|title],"
//      +"address[class|align|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown"
//        +"|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover"
//        +"|onmouseup|style|title],"
//      +"applet[align<bottom?left?middle?right?top|alt|archive|class|code|codebase"
//        +"|height|hspace|id|name|object|style|title|vspace|width],"
//      +"area[accesskey|alt|class|coords|dir<ltr?rtl|href|id|lang|nohref<nohref"
//        +"|onblur|onclick|ondblclick|onfocus|onkeydown|onkeypress|onkeyup"
//        +"|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup"
//        +"|shape<circle?default?poly?rect|style|tabindex|title|target],"
//      +"base[href|target],"
//      +"basefont[color|face|id|size],"
//      +"bdo[class|dir<ltr?rtl|id|lang|style|title],"
//      +"big[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
//        +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
//        +"|title],"
//      +"blockquote[dir|style|cite|class|dir<ltr?rtl|id|lang|onclick|ondblclick"
//        +"|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout"
//        +"|onmouseover|onmouseup|style|title],"
//      +"body[alink|background|bgcolor|class|dir<ltr?rtl|id|lang|link|onclick"
//        +"|ondblclick|onkeydown|onkeypress|onkeyup|onload|onmousedown|onmousemove"
//        +"|onmouseout|onmouseover|onmouseup|onunload|style|title|text|vlink],"
//      +"br[class|clear<all?left?none?right|id|style|title],"
//      +"button[accesskey|class|dir<ltr?rtl|disabled<disabled|id|lang|name|onblur"
//        +"|onclick|ondblclick|onfocus|onkeydown|onkeypress|onkeyup|onmousedown"
//        +"|onmousemove|onmouseout|onmouseover|onmouseup|style|tabindex|title|type"
//        +"|value],"
//      +"caption[align<bottom?left?right?top|class|dir<ltr?rtl|id|lang|onclick"
//        +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
//        +"|onmouseout|onmouseover|onmouseup|style|title],"
//      +"center[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
//        +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
//        +"|title],"
//      +"cite[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
//        +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
//        +"|title],"
//      +"code[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
//        +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
//        +"|title],"
//      +"col[align<center?char?justify?left?right|char|charoff|class|dir<ltr?rtl|id"
//        +"|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown"
//        +"|onmousemove|onmouseout|onmouseover|onmouseup|span|style|title"
//        +"|valign<baseline?bottom?middle?top|width],"
//      +"colgroup[align<center?char?justify?left?right|char|charoff|class|dir<ltr?rtl"
//        +"|id|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown"
//        +"|onmousemove|onmouseout|onmouseover|onmouseup|span|style|title"
//        +"|valign<baseline?bottom?middle?top|width],"
//      +"dd[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup"
//        +"|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style|title],"
//      +"del[cite|class|datetime|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown"
//        +"|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover"
//        +"|onmouseup|style|title],"
//      +"dfn[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
//        +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
//        +"|title],"
//      +"dir[class|compact<compact|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown"
//        +"|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover"
//        +"|onmouseup|style|title],"
//      +"div[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|onclick"
//        +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
//        +"|onmouseout|onmouseover|onmouseup|style|title],"
//      +"dl[class|compact<compact|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown"
//        +"|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover"
//        +"|onmouseup|style|title],"
//      +"dt[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup"
//        +"|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style|title],"
//      +"embed[src|quality|type|wmode|pluginspage|width|height|align],"
//      +"em/i[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
//        +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
//        +"|title],"
//      +"fieldset[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
//        +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
//        +"|title],"
//      +"font[class|color|dir<ltr?rtl|face|id|lang|size|style|title],"
//      +"form[accept|accept-charset|action|class|dir<ltr?rtl|enctype|id|lang"
//        +"|method<get?post|name|onclick|ondblclick|onkeydown|onkeypress|onkeyup"
//        +"|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|onreset|onsubmit"
//        +"|style|title|target],"
//      +"frame[class|frameborder|id|longdesc|marginheight|marginwidth|name"
//        +"|noresize<noresize|scrolling<auto?no?yes|src|style|title],"
//      +"frameset[class|cols|id|onload|onunload|rows|style|title],"
//      +"h1[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|onclick"
//        +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
//        +"|onmouseout|onmouseover|onmouseup|style|title],"
//      +"h2[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|onclick"
//        +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
//        +"|onmouseout|onmouseover|onmouseup|style|title],"
//      +"h3[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|onclick"
//        +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
//        +"|onmouseout|onmouseover|onmouseup|style|title],"
//      +"h4[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|onclick"
//        +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
//        +"|onmouseout|onmouseover|onmouseup|style|title],"
//      +"h5[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|onclick"
//        +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
//        +"|onmouseout|onmouseover|onmouseup|style|title],"
//      +"h6[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|onclick"
//        +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
//        +"|onmouseout|onmouseover|onmouseup|style|title],"
//      +"head[dir<ltr?rtl|lang|profile],"
//      +"hr[align<center?left?right|class|dir<ltr?rtl|id|lang|noshade<noshade|onclick"
//        +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
//        +"|onmouseout|onmouseover|onmouseup|size|style|title|width],"
//      +"html[dir<ltr?rtl|lang|version],"
//      +"iframe[align<bottom?left?middle?right?top|class|frameborder|height|id"
//        +"|longdesc|marginheight|marginwidth|name|scrolling<auto?no?yes|src|style"
//        +"|title|width],"
//      +"img[align<bottom?left?middle?right?top|alt|border|class|dir<ltr?rtl|height"
//        +"|hspace|id|ismap<ismap|lang|longdesc|name|onclick|ondblclick|onkeydown"
//        +"|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover"
//        +"|onmouseup|src|style|title|usemap|vspace|width],"
//      +"input[accept|accesskey|align<bottom?left?middle?right?top|alt"
//        +"|checked<checked|class|dir<ltr?rtl|disabled<disabled|id|ismap<ismap|lang"
//        +"|maxlength|name|onblur|onclick|ondblclick|onfocus|onkeydown|onkeypress"
//        +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|onselect"
//        +"|readonly<readonly|size|src|style|tabindex|title"
//        +"|type<button?checkbox?file?hidden?image?password?radio?reset?submit?text"
//        +"|usemap|value],"
//      +"ins[cite|class|datetime|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown"
//        +"|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover"
//        +"|onmouseup|style|title],"
//      +"isindex[class|dir<ltr?rtl|id|lang|prompt|style|title],"
//      +"kbd[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
//        +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
//        +"|title],"
//      +"label[accesskey|class|dir<ltr?rtl|for|id|lang|onblur|onclick|ondblclick"
//        +"|onfocus|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout"
//        +"|onmouseover|onmouseup|style|title],"
//      +"legend[align<bottom?left?right?top|accesskey|class|dir<ltr?rtl|id|lang"
//        +"|onclick|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
//        +"|onmouseout|onmouseover|onmouseup|style|title],"
//      +"li[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup"
//        +"|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style|title|type"
//        +"|value],"
//      +"link[charset|class|dir<ltr?rtl|href|hreflang|id|lang|media|onclick"
//        +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
//        +"|onmouseout|onmouseover|onmouseup|rel|rev|style|title|target|type],"
//      +"map[class|dir<ltr?rtl|id|lang|name|onclick|ondblclick|onkeydown|onkeypress"
//        +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
//        +"|title],"
//      +"menu[class|compact<compact|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown"
//        +"|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover"
//        +"|onmouseup|style|title],"
//      +"meta[content|dir<ltr?rtl|http-equiv|lang|name|scheme],"
//      +"noframes[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
//        +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
//        +"|title],"
//      +"noscript[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
//        +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
//        +"|title],"
//      +"object[align<bottom?left?middle?right?top|archive|border|class|classid"
//        +"|codebase|codetype|data|declare|dir<ltr?rtl|height|hspace|id|lang|name"
//        +"|onclick|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
//        +"|onmouseout|onmouseover|onmouseup|standby|style|tabindex|title|type|usemap"
//        +"|vspace|width],"
//      +"ol[class|compact<compact|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown"
//        +"|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover"
//        +"|onmouseup|start|style|title|type],"
//      +"optgroup[class|dir<ltr?rtl|disabled<disabled|id|label|lang|onclick"
//        +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
//        +"|onmouseout|onmouseover|onmouseup|style|title],"
//      +"option[class|dir<ltr?rtl|disabled<disabled|id|label|lang|onclick|ondblclick"
//        +"|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout"
//        +"|onmouseover|onmouseup|selected<selected|style|title|value],"
//      +"p[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|onclick"
//        +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
//        +"|onmouseout|onmouseover|onmouseup|style|title],"
//      +"param[id|name|type|value|valuetype<DATA?OBJECT?REF],"
//      +"pre/listing/plaintext/xmp[align|class|dir<ltr?rtl|id|lang|onclick|ondblclick"
//        +"|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout"
//        +"|onmouseover|onmouseup|style|title|width],"
//      +"q[cite|class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
//        +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
//        +"|title],"
//      +"s[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup"
//        +"|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style|title],"
//      +"samp[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
//        +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
//        +"|title],"
//      +"script[charset|defer|language|src|type],"
//      +"select[class|dir<ltr?rtl|disabled<disabled|id|lang|multiple<multiple|name"
//        +"|onblur|onclick|ondblclick|onfocus|onkeydown|onkeypress|onkeyup"
//        +"|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|size|style"
//        +"|tabindex|title],"
//      +"small[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
//        +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
//        +"|title],"
//      +"span[align|class|class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown"
//        +"|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover"
//        +"|onmouseup|style|title],"
//      +"strike[class|class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown"
//        +"|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover"
//        +"|onmouseup|style|title],"
//      +"strong/b[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
//        +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
//        +"|title],"
//      +"style[dir<ltr?rtl|lang|media|title|type],"
//      +"sub[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
//        +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
//        +"|title],"
//      +"sup[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
//        +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
//        +"|title],"
//      +"table[align<center?left?right|bgcolor|border|cellpadding|cellspacing|class"
//        +"|dir<ltr?rtl|frame|height|id|lang|onclick|ondblclick|onkeydown|onkeypress"
//        +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|rules"
//        +"|style|summary|title|width],"
//      +"tbody[align<center?char?justify?left?right|char|class|charoff|dir<ltr?rtl|id"
//        +"|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown"
//        +"|onmousemove|onmouseout|onmouseover|onmouseup|style|title"
//        +"|valign<baseline?bottom?middle?top],"
//      +"td[abbr|align<center?char?justify?left?right|axis|bgcolor|char|charoff|class"
//        +"|colspan|dir<ltr?rtl|headers|height|id|lang|nowrap<nowrap|onclick"
//        +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
//        +"|onmouseout|onmouseover|onmouseup|rowspan|scope<col?colgroup?row?rowgroup"
//        +"|style|title|valign<baseline?bottom?middle?top|width],"
//      +"textarea[accesskey|class|cols|dir<ltr?rtl|disabled<disabled|id|lang|name"
//        +"|onblur|onclick|ondblclick|onfocus|onkeydown|onkeypress|onkeyup"
//        +"|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|onselect"
//        +"|readonly<readonly|rows|style|tabindex|title],"
//      +"tfoot[align<center?char?justify?left?right|char|charoff|class|dir<ltr?rtl|id"
//        +"|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown"
//        +"|onmousemove|onmouseout|onmouseover|onmouseup|style|title"
//        +"|valign<baseline?bottom?middle?top],"
//      +"th[abbr|align<center?char?justify?left?right|axis|bgcolor|char|charoff|class"
//        +"|colspan|dir<ltr?rtl|headers|height|id|lang|nowrap<nowrap|onclick"
//        +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
//        +"|onmouseout|onmouseover|onmouseup|rowspan|scope<col?colgroup?row?rowgroup"
//        +"|style|title|valign<baseline?bottom?middle?top|width],"
//      +"thead[align<center?char?justify?left?right|char|charoff|class|dir<ltr?rtl|id"
//        +"|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown"
//        +"|onmousemove|onmouseout|onmouseover|onmouseup|style|title"
//        +"|valign<baseline?bottom?middle?top],"
//      +"title[dir<ltr?rtl|lang],"
//      +"tr[abbr|align<center?char?justify?left?right|bgcolor|char|charoff|class"
//        +"|rowspan|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
//        +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
//        +"|title|valign<baseline?bottom?middle?top],"
//      +"tt[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup"
//        +"|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style|title],"
//      +"u[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup"
//        +"|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style|title],"
//      +"ul[class|compact<compact|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown"
//        +"|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover"
//        +"|onmouseup|style|title|type],"
//      +"var[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
//        +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
//        +"|title]",


   //################################################
   //       _____ _          __  __  ___ ___
   //      |_   _(_)_ _ _  _|  \/  |/ __| __|
   //        | | | | ' \ || | |\/| | (__| _|
   //        |_| |_|_||_\_, |_|  |_|\___|___|
   //                   |__/                 Stuff
   //################################################
   
var Base64 = {

    // private property
    _keyStr : "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",

    // public method for encoding
    encode : function (input) {
        var output = "";
        var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
        var i = 0;

        input = Base64._utf8_encode(input);

        while (i < input.length) {

            chr1 = input.charCodeAt(i++);
            chr2 = input.charCodeAt(i++);
            chr3 = input.charCodeAt(i++);

            enc1 = chr1 >> 2;
            enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
            enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
            enc4 = chr3 & 63;

            if (isNaN(chr2)) {
                enc3 = enc4 = 64;
            } else if (isNaN(chr3)) {
                enc4 = 64;
            }

            output = output +
            this._keyStr.charAt(enc1) + this._keyStr.charAt(enc2) +
            this._keyStr.charAt(enc3) + this._keyStr.charAt(enc4);

        }

        return output;
    },

    // public method for decoding
    decode : function (input) {
        var output = "";
        var chr1, chr2, chr3;
        var enc1, enc2, enc3, enc4;
        var i = 0;

        input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");

        while (i < input.length) {

            enc1 = this._keyStr.indexOf(input.charAt(i++));
            enc2 = this._keyStr.indexOf(input.charAt(i++));
            enc3 = this._keyStr.indexOf(input.charAt(i++));
            enc4 = this._keyStr.indexOf(input.charAt(i++));

            chr1 = (enc1 << 2) | (enc2 >> 4);
            chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
            chr3 = ((enc3 & 3) << 6) | enc4;

            output = output + String.fromCharCode(chr1);

            if (enc3 != 64) {
                output = output + String.fromCharCode(chr2);
            }
            if (enc4 != 64) {
                output = output + String.fromCharCode(chr3);
            }

        }

        output = Base64._utf8_decode(output);

        return output;

    },

    // private method for UTF-8 encoding
    _utf8_encode : function (string) {
        string = string.replace(/\r\n/g,"\n");
        var utftext = "";

        for (var n = 0; n < string.length; n++) {

            var c = string.charCodeAt(n);

            if (c < 128) {
                utftext += String.fromCharCode(c);
            }
            else if((c > 127) && (c < 2048)) {
                utftext += String.fromCharCode((c >> 6) | 192);
                utftext += String.fromCharCode((c & 63) | 128);
            }
            else {
                utftext += String.fromCharCode((c >> 12) | 224);
                utftext += String.fromCharCode(((c >> 6) & 63) | 128);
                utftext += String.fromCharCode((c & 63) | 128);
            }

        }

        return utftext;
    },

    // private method for UTF-8 decoding
    _utf8_decode : function (utftext) {
        var string = "";
        var i = 0;
        var c = c1 = c2 = 0;

        while ( i < utftext.length ) {

            c = utftext.charCodeAt(i);

            if (c < 128) {
                string += String.fromCharCode(c);
                i++;
            }
            else if((c > 191) && (c < 224)) {
                c2 = utftext.charCodeAt(i+1);
                string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
                i += 2;
            }
            else {
                c2 = utftext.charCodeAt(i+1);
                c3 = utftext.charCodeAt(i+2);
                string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
                i += 3;
            }

        }

        return string;
    }

}
</script>
<?php
if (function_exists('curl_init')) {
	$spellchecker = ',spellchecker';
} else {
	$spellchecker = '';
}
$spellchecker = '';
/////////////////////
//Cam Fix them styles!
$startpage = startpage();
ob_start();

	if(eregi('tCustom', $CUR_TEMPLATE)){
		include('../../../../'.$CUR_TEMPLATE);
	} else {
		if($_GET['currentPage'] == $startpage  && is_file('../site_templates/pages/'.$CUR_TEMPLATE.'/home.html')){
			include('../site_templates/pages/'.$CUR_TEMPLATE.'/home.html');
		} else {
			include('../site_templates/pages/'.$CUR_TEMPLATE.'/index.html');
		}
	}
	$cap_display = ob_get_contents();
ob_end_clean();

preg_match_all('/[^\'" :]+(\.gif|\.jpg|\.jpeg|\.bmp|\.png|\.tiff|\.tif)/i', $cap_display, $matches);
	foreach($matches['0'] as $imgname){
		if(file_exists($_SESSION['doc_root'].'/sohoadmin/program/modules/site_templates/pages/'.$CUR_TEMPLATE.'/'.$imgname)){
			$cap_display = eregi_replace($imgname, 'http://'.$_SESSION['this_ip'].'/sohoadmin/program/modules/site_templates/pages/'.$CUR_TEMPLATE.'/'.$imgname, $cap_display);
		}	
	} 
	//$cap_display = eregi_replace('<head>', "<head>\n<link rel=\"stylesheet\" type=\"text/css\" href=\"http://provx.jambuildit.com/sohoadmin/program/modules/site_templates/pages/".$CUR_TEMPLATE."/custom.css\">", $cap_display);
	if($cap_display = eregi_replace('#CONTENT#', "<div  id=\"contentz\">#CONTENT#</div>", $cap_display)){
		$cap_display = eregi_replace('(z\-index\:)( )?[0-9]+;', '', $cap_display);
		echo "<body background=\"pixel.gif\" style=\"background-color:white;\"></body>";
		echo $cap_display = "<body style=\"background-color:white;\"><div id=\"thatsallfolks\" style=\"display:none;\">".$cap_display."</div></body>";
	}

	echo "<script language=\"javascript\">\n";
	if(eregi("contentz", $cap_display)){
?>
function isDefined(variable)
{
return (!(!( variable||false )))
}
	
ss = document.getElementById("contentz");

//alert(ss.nodeName); //alerts 7 (includes text nodes)
var stylevar=new Array();
var keeptrack=new Array();
var keeptrackstyle=new Array();
var tagarray=new Array();
var trackclass=new Array();
var tagtypes = '';
var classes = '';
var csid = '';
vx = 0;
stylem = '';
//while(ss.id != 'thatsallfolks') {
while(ss.nodeName != 'BODY') {
	ss = ss.parentNode;
	if(ss.id != 'thatsallfolks' && ss.id != 'contentz') {
		for(z=0;z<ss.attributes.length;z++){
	   	if(ss.attributes[z].value&&ss.attributes[z].specified&&ss.attributes[z].nodeValue) {
				styleatt = ss.attributes[z].name;
				//if(keeptrack[styleatt] == undefined) {			
					if(ss.attributes[z].name == 'class') {
						tclass = ss.nodeName + '.' + ss.attributes[z].value;
						
						if(trackclass[tclass] != ss.nodeName + '.' + ss.attributes[z].value){
							classes = classes + ss.attributes[z].value + ';';
							//alert(classes);
							trackclass[tclass] = ss.nodeName + '.' + ss.attributes[z].value;
						}
						
					} else if(ss.attributes[z].name == 'style') {
						stylem = stylem + ss.attributes[z].value;
						keeptrack[styleatt] = ss.attributes[z].value;
					} else if(ss.attributes[z].name == 'id') {
						csid = csid + ss.attributes[z].value +';';
						
					} else {					
						keeptrack[styleatt] = ss.attributes[z].name;
						if(ss.attributes[z].name != 'onload'){
							stylem = stylem + ss.attributes[z].name +':'+ ss.attributes[z].value +';';					
						}
					}
					
					ttype = ss.nodeName;				
					if(tagarray[ttype] != ss.nodeName) {
						tagarray[ttype] = ss.nodeName;
				 		tagtypes = tagtypes + ss.nodeName + ';';
					}
			
			 		vx++;
				//}
	   	}
	   }
	}
}
document.getElementById('thatsallfolks').innerHTML = '';
<?php
} else {
?>
var stylevar=new Array();
var keeptrack=new Array();
var keeptrackstyle=new Array();
var tagarray=new Array();
var trackclass=new Array();
var tagtypes = '';
var classes = '';
var csid = '';
vx = 0;
stylem = '';
<?php	
}
$cfonts = new userdata("customfonts");
if($cfonts->get("fontfams") == "") {
	$customfonts = "Andale Mono=andale mono,times;Arial=arial,helvetica,sans-serif;Arial Black=arial black,avant garde;Book Antiqua=book antiqua,palatino;Comic Sans MS=comic sans ms,sand;Courier New=courier new,courier;Georgia=georgia,palatino;Helvetica=helvetica;Impact=impact,chicago;Symbol=symbol;Tahoma=tahoma,arial,helvetica,sans-serif;Terminal=terminal,monaco;Times New Roman=times new roman,times;Trebuchet MS=trebuchet ms,geneva;Verdana=verdana,geneva;Webdings=webdings;Wingdings=wingdings,zapf dingbats";
	$cfonts->set("fontfams", $customfonts);		
}
$cfonts = $cfonts->get("fontfams");
$cfonts = explode(';', $cfonts);
usort($cfonts, "strnatcasecmp");
$finalfonts = '';
foreach($cfonts as $fvals){
	$finalfonts .= $fvals.';';
}
$finalfonts = eregi_replace(';$', '', $finalfonts);
?>



   var current_editing_area = '';

   tinyMCE.init({
   	mode : "none",
   	theme : "advanced",
   	plugins : "inlinepopups,style,table,advhr,advimage,advlink,emotions,insertdatetime,preview,Addfontz,media,searchreplace,print,contextmenu,paste,visualchars,xhtmlxtras<?php echo $spellchecker; ?>",
   	theme_advanced_buttons1_add : "fontselect,separator,Addfontz,separator,fontsizeselect",
   	theme_advanced_buttons2_add : "separator,insertdate,inserttime,separator,forecolor,backcolor",
   	theme_advanced_buttons2_add_before: "cut,copy,paste,pastetext,pasteword,separator,search,replace,separator",
   	theme_advanced_buttons3_add_before : "tablecontrols,separator",
   	theme_advanced_buttons3_add : "media,emotions,print,separator,styleprops,preview<?php echo $spellchecker; ?>",
   	theme_advanced_toolbar_location : "top",
   	theme_advanced_toolbar_align : "center",
   	theme_advanced_path_location : "bottom",
   	content_css : "../../../../sohoadmin/program/modules/tiny_mce/custom-css.php?cust_temp=<?php echo base64_encode($CUR_TEMPLATE); ?>&style="+ Base64.encode(stylem) +"&tags="+ Base64.encode(tagtypes) +"&ids="+ Base64.encode(csid) +"&classes="+ Base64.encode(classes) +"&pr=<?php echo base64_encode($_GET['currentPage']); ?>", 
		plugin_insertdate_dateFormat : "%Y-%m-%d",
		plugin_insertdate_timeFormat : "%H:%M:%S",
		paste_strip_class_attributes : "mso",
      verify_html : false,
   	convert_urls : false,
   	relative_urls : true,
   	document_base_url : "http://<?php echo $this_ip; ?>/",
   	theme_advanced_resize_horizontal : false,
   	theme_advanced_resizing : false,
   	nonbreaking_force_tab : true,
   	apply_source_formatting : true,
   	fix_content_duplication : false,
   	theme_advanced_fonts : "<?php echo $finalfonts; ?>",
   	trim_span_elements : false,
   	verify_css_classes : true,
   	visual : true,
   	media_use_script : true,
   	setupcontent_callback : "pullHTML",
   	theme_advanced_blockformats : "address,p,pre,h1,h2,h3,h4,h5,h6",
   	external_image_list_url : "../../../../sohoadmin/program/modules/tiny_mce/imagelist.php",
   	media_external_list_url : "../../../../sohoadmin/program/modules/tiny_mce/medialist.php",
   	external_link_list_url : "../../../../sohoadmin/program/modules/tiny_mce/linklist.php"
   });
   
   // updates tiny's font dropdown
   // font_num - index of font posistion
   // font_text - option display text
   // font_value - option value

   function resetFontsNow(){
      var inst = tinyMCE.selectedInstance;
      var editorId = inst.editorId;
      var formElementName = editorId+"_fontNameSelect";
		document.getElementById(formElementName).length = 0
   }
   function updateFontsNow(font_num, font_text, font_value){
      var inst = tinyMCE.selectedInstance;
      var editorId = inst.editorId;
      var formElementName = editorId+"_fontNameSelect";
      document.getElementById(formElementName).options[font_num] = new Option(font_text,font_value);
   }

   // Defines what happends when file specific buttons are clicked
   function fileBrowserCallBack(field_name, url, type, win) {
      //alert(type)
   	var connector = "../../../tiny_mce/file_manager.php";
   	var linkconnector = "../../../tiny_mce/link_manager.php";

   	my_field = field_name;
   	my_win = win;
   	wins_vars = "width=450,height=600";

   	switch (type) {
   		case "image":
   			connector += "?type=img&dot_com="+dot_com;
   			break;
   		case "media":
   			connector += "?type=flash&dot_com="+dot_com;
   			break;
   		case "file":
   			connector = linkconnector+"?type=files&dot_com="+dot_com;
   			wins_vars = "width=550,height=200";
   			break;
   	}
   	window.open(connector, "link_manager", wins_vars);
   	//alert('4test-'+connector+'---'+wins_vars)
   }

   // Gets content from editor and places it in editor
   // Called by setupcontent_callback within tinyMCE.init
   function pullHTML(editor_id, body, doc){
      //alert(current_editing_area)
      var html = document.getElementById(current_editing_area).innerHTML;
      //alert(html);
      
      // Fix for <table align=center> bug
      var alignArr = html.split('align=sohocenter')
      var alignLen = alignArr.length
      for(var x=0; x<alignLen; x++){
         html = html.replace('align=sohocenter', 'align=center');
      }
      var alignQutArr = html.split('align="sohocenter"')
      var alignQutLen = alignQutArr.length
      for(var x=0; x<alignQutLen; x++){
         html = html.replace('align="sohocenter"', 'align="center"');
      }
      // End Fix
      
      //alert(html);
      html = html.replace('<blink>','');
      html = html.replace('</blink>','');
      html = html.replace('<BLINK>','');
      html = html.replace('</BLINK>','');


      // Convert media objects to editor readable images
      var inst = tinyMCE.getInstanceById(tinyMCE.selectedInstance.editorId);
      var newHtml = TinyMCE_MediaPlugin.cleanup('insert_to_editor',html,inst)

      body.innerHTML = newHtml;
   }

   // Hide / show / load / unload editor within spcified id (div or textarea)
   function toggleEditor(id) {
      //alert('ok')

   	var elm = document.getElementById(id);
      var html = document.getElementById(current_editing_area).innerHTML;

   	if (tinyMCE.getInstanceById(id) == null){
   	   //$('tiny_editor_loading').style.display='block'
   		tinyMCE.execCommand('mceAddControl', false, id);
   	   $('tiny_editor_container').style.display='block';

   	   // Fix table border display
   	   setTimeout("tinyMCE.execInstanceCommand(tinyMCE.selectedInstance.editorId,'mceToggleVisualAid',false);tinyMCE.execInstanceCommand(tinyMCE.selectedInstance.editorId,'mceToggleVisualAid',false);",1000);
   	   //alert('ok2')
   	}else{
   	   //alert('ok3')
   		tinyMCE.execCommand('mceRemoveControl', false, id);
   	   $('tiny_editor_container').style.display='none';
   	   //alert('ok2')
   	}
   }

//   function setLink(newLink){
//      alert('setting...')
//      tinyMCEPopup.execCommand("mceBeginUndoLevel");
//      tinyMCE.execCommand('mceReplaceContent',false,'<a href="'+newLink+'">{$selection}</a>');
//   	tinyMCEPopup.execCommand("mceEndUndoLevel");
//   	alert('done setting...')
//      return false;
//   }

   function testtable(){
      alert('converting tables now')
      tinyMCE.execInstanceCommand('mce_editor_0','mceToggleVisualAid',false);
      tinyMCE.execInstanceCommand('mce_editor_0','mceToggleVisualAid',false);
   }

//alert('Changes are being made right now!  You are welcome to continue testing but please excuse the debuging stuff :)');

</script>

</head>



<!-- ############################################################# --
          ___  _         _             ___ _            _
         |   \(_)____ __| |__ _ _  _  / __| |_ __ _ _ _| |_
         | |) | (_-< '_ \ / _` | || | \__ \  _/ _` | '_|  _|
         |___/|_/__/ .__/_\__,_|\_, | |___/\__\__,_|_|  \__|
                   |_|          |__/
<!-- ############################################################# -->

<body onload="show_screen();">
<script type="text/javascript" src="../../includes/display_elements/wz_dragdrop.js"></script>

<!--- <a href="javascript:toggleEditor('tiny_editor');" style="position: absolute; top: 0px; left: 0px; border: 1px solid red; z-index:100000;">click it</a> -->


<!-- ############################################################# --
             ___    _ _ _             ___ _         __  __
            | __|__| (_) |_ ___ _ _  / __| |_ _  _ / _|/ _|
            | _|/ _` | |  _/ _ \ '_| \__ \  _| || |  _|  _|
            |___\__,_|_|\__\___/_|   |___/\__|\_,_|_| |_|
<!-- ############################################################# -->

<?php
if ( eregi("MSIE", $_SERVER['HTTP_USER_AGENT']) || eregi("opera", $_SERVER['HTTP_USER_AGENT']) ) {
   $editorHeight = "450px";
}else{
   $editorHeight = "475px";
}
?>

<div id="tiny_editor_container" style="position: absolute; height: <? echo $editorHeight; ?>; top: 0px; left: 0px; bottom: 0px; right: 0px; border: 0px solid green; z-index:1000; display: none;">
   <!--- Editor Textarea -->
   <textarea id="tiny_editor" name="tiny_editor" rows="15" cols="80" style="height: 100%; width: 100%;">editor content</textarea>

   <!--- Cancel / Done buttons -->
   <div id="saveIt" style="position:absolute; bottom: 1px; right: 15px; z-index:1000; display:block;">
      <!--- Test button
      <input onClick="testtable();" type="button" id="debug_edit" value="  Table Debug  " style="width: 150px;padding: 1px;" /> -->
      <!--- Cancel -->
      <input onClick="toggleEditor('tiny_editor');show_hide_icons();parent.header.flip_header_nav('PAGE_EDITOR_LAYER');" type="button" id="cancel_edit" value="  Cancel  " style="width: 150px;padding: 1px;" <? echo $_SESSION['btn_build']; ?> >
      <!--- Done -->
      <input onClick="onSaveFileSOHO();show_hide_icons();" type="button" id="save_content" value="  Done  " style="width: 150px;padding: 1px;" <? echo $_SESSION['btn_build']; ?> >
   </div>
</div>

<!--- EDITOR LOADING -->
<div id="overlay_edit">&nbsp;</div>
<div id="tiny_editor_loading">
   Working...
</div>

<!-- Display: none; will fix scroll problem -->

<FORM id="save" name="save" method="post" action="save_page.php" STYLE='Display: NONE;'>

<!-- ============================================================ -->
<!-- ============= PAGE PROPERTIES LAYER ======================== -->
<!-- ============================================================ -->

<div id="pageproperties" style="position:absolute; left:0px; top:1%; width:100%; height:525px; z-index:200; border: 0px inset black; overflow: auto; visibility: hidden;">
<table border="0" cellpadding="0" width="100%"><tr><td align="center" valign="middle">

	<?

	// For easier editing, the page properties layer was created as an include
	// In the last updates before release, this was still being modified heavily
	// and I did not want to keep updating this script. The page_editor script
	// is very sensitive
	// --------------------------------------------------------------------------

	include("layers/page_prop_layer.php");

	?>

</td></tr></table>

</div>

<!-- ============================================================ -->
<!-- ============= SAVE PAGE AS... LAYER ======================== -->
<!-- ============================================================ -->

<div id="saveaslayer" style="position:absolute; left:0px; top:40%; width:100%; height:350px; z-index:200; border: 0px inset black; overflow: auto; visibility: hidden;">
<table border="0" cellpadding="0" width="100%"><tr><td align="center" valign="middle">
	<?

	include("layers/save_as_layer.php");

	?>
</td></tr></table>

</div>

<!-- ############################################################# -->
<!-- #### START SAVE LAYER HIDDEN					  #### -->
<!-- ############################################################# -->

<div id="HiddenSaveLayer" style="position:absolute; width:100%; height:50%; z-index:1; left: 0px; top: 15%; overflow: none; visibility: hidden;">

<?

if ($totalHidden != 0) {
	for ($x=1;$x<=$totalHidden;$x++) {
		echo ("$hiddenValue[$x]");
	}
}
echo ("<SPAN id=\"saveForm\" class=\"hidden\"><input type=\"hidden\" id=\"currentPage\" name=\"currentPage\" value=\"".$_GET['currentPage']."\"><input type=\"hidden\" name=\"serial_number\" value=\"".$serial_number."\"><input type=\"hidden\" id=\"dot_com\" name=\"dot_com\" value=\"".$dis_site."\"></SPAN>\n");
?>

<span id="desctext"><? echo lang("Click on an object above and drag it onto a drop zone for page placement."); ?></span>


</div>
</FORM>



<?

// Determine which icons to pull based on language
//======================================================
if ( $getSpec['df_lang'] == "norwegian.php" ) {
   $ilng = "nor";
} else {
   $ilng = "eng";
}

echo div_window();

// Build icon filename
//----------------------------------------
$ipre = "obj_bar_icons/".$ilng."-icon_";
$engdash = "obj_bar_icons/".$ilng."-"; // newschool, use this

if ( eregi("MSIE", $_SERVER['HTTP_USER_AGENT']) || eregi("opera", $_SERVER['HTTP_USER_AGENT']) ) {
   $barWidth = "773";
}else{
   $barWidth = "781";
}
?>


<div id="objectbar">


<!---###################################################################################################--->
<!-----            ___   _      _           _     ___                                                   --->
<!-----           / _ \ | |__  (_) ___  __ | |_  |_ _| __  ___  _ _   ___                               --->
<!-----          | (_) || '_ \ | |/ -_)/ _||  _|  | | / _|/ _ \| ' \ (_-<                               --->
<!-----           \___/ |_.__/_/ |\___|\__| \__| |___|\__|\___/|_||_|/__/                               --->
<!-----                      |__/                                                                       --->
<!------------------------------------ Drag and Drop Icons: ROW ONE --------------------------------------->
<!---###################################################################################################--->

<table border="0" cellpadding="0" cellspacing="0" align="left" width="<? echo $barWidth; ?>" height="100%" style="display: block; border: 0px dotted green;">
 <tr>
  <td id="imgdesc" colspan="9" class="fprev_note" style="border: 0px; padding: 3px; text-align: center; font-style: italic;">
  <!--- <a href="#" onClick="tinyMCE.setContent('go Joe');">INSERT</a> -->
   <? echo lang("Click on an object below and drag it onto a drop zone for page placement."); ?></td>
 </tr>
 <tr>
  <td align="center" valign="top" colspan="9">
   <table border="0" cellpadding="0" cellspacing="0" align="center" width="<? echo $barWidth; ?>" height="100%" style="border: 1px solid #245981;">

    <!---###################################################################################################--->
    <!------------------------------------ Drag and Drop Icons: ROW ONE --------------------------------------->
    <!---###################################################################################################--->
    <tr>

     <!-- My Images -->
     <td width="81" height="20" align="center" valign="middle" class="ob2" onmouseover="this.className='ob1';" onmouseout="this.className='ob2';">
     <img name="MYimages" align="absmiddle" id="oImage" value="oImage" src="<? echo $ipre; ?>images.gif" width="80" height="18" style="cursor: hand;"></td>

     <!-- Text Editor -->
     <td width="81" height="20" align="center" valign="middle" class="ob2" onmouseover="this.className='ob1';" onmouseout="this.className='ob2';">
     <img name="MYeditor" align="absmiddle" id="oText" value="oText" src="<? echo $ipre; ?>texteditor.gif" width="80" height="18" style="cursor: hand;"></td>

     <!-- Forms -->
     <td width="81" height="20" align="center" valign="middle" class="ob2" onmouseover="this.className='ob1';" onmouseout="this.className='ob2';">
     <img name="MYforms" align="absmiddle" id="oForms" value="oForms" src="<? echo $ipre; ?>forms.gif" width="80" height="18" style='cursor: hand;'></td>

     <!-- Documents -->
     <td width="81" height="20" align="center" valign="middle" class="ob2" onmouseover="this.className='ob1';" onmouseout="this.className='ob2';">
     <img name="MYdocs" align="absmiddle" id="oMSWORD" value="oMSWORD" src="<? echo $ipre; ?>docs.gif" width="80" height="18" style='cursor: hand;'></td>

     <!--- Hit Counter --->
     <td width="81" height="20" align="center" valign="middle" class="ob2" onmouseover="this.className='ob1';" onmouseout="this.className='ob2';">
      <img name="MYcounter" align="absmiddle" id="oCounter" value="oCounter" src="<? echo $ipre; ?>counter.gif" width="80" height="18" style="cursor: hand;">
     </td>

     <!-- Auth Login -->
     <td width="81" height="20" align="center" valign="middle" class="ob2" onmouseover="this.className='ob1';" onmouseout="this.className='ob2';">
     <img name="MYauth" align="absmiddle" id="oSecureLogin" value="oSecureLogin" src="<? echo $ipre; ?>login.gif" width="80" height="18" style='cursor: hand;'></td>

     <!-- Custom Code -->
     <td width="81" height="20" align="center" valign="middle" class="ob2" onmouseover="this.className='ob1';" onmouseout="this.className='ob2';">
     <img name="MYcust" align="absmiddle" id="oCustom" value="oCustom" src="<? echo $ipre; ?>customcode.gif" width="80" height="18" style='cursor: hand;'></td>

     <!-- Shopping -->
     <td width="81" height="20" align="center" valign="middle" class="ob2" onmouseover="this.className='ob1';" onmouseout="this.className='ob2';">
     <img name="MYshop" align="absmiddle" id="oCart" value="oCart" src="<? echo $ipre; ?>shopping.gif" width="80" height="18" style='cursor: hand;'></td>

     <!-- Delete Object -->
     <td width="81" rowspan="3" height="60" align="center" valign="middle" class="ob2" onmouseover="this.className='ob1';" onmouseout="this.className='ob2';">
     <img name="MYdelete" align="absmiddle" id="oDelete" value="oDelete" src="<? echo $ipre; ?>deleteobj.gif" style='cursor: hand;'></td>

     </tr>

    <!---###################################################################################################--->
    <!------------------------------------ Drag and Drop Icons: ROW TWO --------------------------------------->
    <!---###################################################################################################--->
    <tr>

     <!-- Table Search -->
     <td width="81" height="20" align="center" valign="middle" class="ob2" onmouseover="this.className='ob1';" onmouseout="this.className='ob2';">
     <img name="MYsearch" align="absmiddle" id="oTableSearch" value="oTableSearch" src="<? echo $ipre; ?>searchdb.gif" width="80" height="18" style='cursor: hand;'></td>

     <!-- Sign-Up -->
     <td width="81" height="20" align="center" valign="middle" class="ob2" onmouseover="this.className='ob1';" onmouseout="this.className='ob2';">
     <img name="MYsignup" align="absmiddle" id="oNewsletter" value="oNewsletter" src="<? echo $ipre; ?>newsletter.gif" width="80" height="18" style='cursor: hand;'></td>

     <!-- Calendar -->
     <td width="81" height="20" align="center" valign="middle" class="ob2" onmouseover="this.className='ob1';" onmouseout="this.className='ob2';">
     <img name="MYcalendar" align="absmiddle" id="oCalendar" value="oCalendar" src="<? echo $ipre; ?>calendar.gif" width="80" height="18" style='cursor: hand;'></td>

     <!-- Directions -->
<?

// Disable map object? (intl. request)
//==========================================
if ( $map_obj != "disabled" ) {
   echo "     <td width=\"81\" height=\"20\" align=\"center\" valign=\"middle\" class=\"ob2\" onmouseover=\"this.className='ob1';\" onmouseout=\"this.className='ob2';\"><img id=\"MYdirections\" align=\"absmiddle\" id=oDirections value=\"oDirections\" src=\"".$ipre."directions.gif\" width=\"80\" height=\"18\" vspace=\"1\" hspace=\"1\" style='cursor: hand'></td>\n";
} else {
   echo "     <td width=\"81\" height=\"20\" align=\"center\" valign=\"middle\" class=\"ob2\">&nbsp;</td>\n";
}


// Shared properties for icon td's and img's
//--------------------------------------------
$tdProps = "width=\"81\" height=\"20\" align=\"center\" valign=\"middle\" class=\"ob2\"";
$iconProps = "width=\"80\" height=\"18\" vspace=\"1\" hspace=\"1\" align=\"absmiddle\" style=\"cursor: hand;\"";

?>

     <!--- Date Stamp --->
     <td <? echo $tdProps; ?> onmouseover="this.className='ob1';" onmouseout="this.className='ob2';">
      <img <? echo $iconProps; ?> name="MYdate" id="oDate" value="oDate" src="<? echo $ipre; ?>datestamp.gif">
     </td>

     <!--- Print Page --->
     <td <? echo $tdProps; ?> onmouseover="this.className='ob1';" onmouseout="this.className='ob2';">
      <img <? echo $iconProps; ?> name="MYprint" id="oPrint" value="oPrint" src="<? echo $ipre; ?>printpage.gif">
     </td>


     <!--- Email Friend --->
     <td <? echo $tdProps; ?> onmouseover="this.className='ob1';" onmouseout="this.className='ob2';">
      <img <? echo $iconProps; ?> name="MYemail" id="oEmailTo" value="oEmailTo" src="<? echo $ipre; ?>emailfriend.gif">
     </td>


<?
# SitePal object?
if ( sitepal_allowed() ) {
   # yes
   echo "     <!-- SitePal -->\n";
   echo "     <td width=\"81\" height=\"20\" align=\"center\" valign=\"middle\" class=\"ob2\" onmouseover=\"this.className='ob1';\" onmouseout=\"this.className='ob2';\">\n";
   echo "     <img name=\"MYsitepal\" border=\"0\" align=\"absmiddle\" id=\"sitepal_obj\" value=\"sitepal_obj\" src=\"".$engdash."sitepal.gif\" width=\"80\" height=\"18\" vspace=\"1\" hspace=\"1\" style='cursor: hand;'></td>\n";
} else {
   # no
   echo "     <td width=\"81\" height=\"20\" align=\"center\" valign=\"middle\" class=\"ob2\" style=\"cursor: default;\">&nbsp;</td>\n";
}
?>

    </tr>

    <!---###################################################################################################--->
    <!------------------------------------ Drag and Drop Icons: ROW THREE ------------------------------------->
    <!---###################################################################################################--->
    <tr>

     <!--- PopUp Win --->
     <td width="81" height="20" align="center" valign="middle" class="ob2" onmouseover="this.className='ob1';" onmouseout="this.className='ob2';">
     <img name="MYpopup" align="absmiddle" id="oPopup" value="oPopup" src="<? echo $ipre; ?>popup.gif" width="80" height="18" style='cursor: hand;'></td>

     <!--- Audio Files --->
     <td width="81" height="20" align="center" valign="middle" class="ob2" onmouseover="this.className='ob1';" onmouseout="this.className='ob2';">
     <img name="MYaudio" align="absmiddle" id="oMP3" value="oMP3" src="<? echo $ipre; ?>audio.gif" width="80" height="18" style='cursor: hand;'></td>

     <!--- Video Files --->
     <td width="81" height="20" align="center" valign="middle" class="ob2" onmouseover="this.className='ob1';" onmouseout="this.className='ob2';">
     <img name="MYvideo" align="absmiddle" id="oVideo" value="oVideo" src="<? echo $ipre; ?>video.gif" width="80" height="18" style='cursor: hand;'></td>

     <!--- PlugIn Links --->
     <td width="81" height="20" align="center" valign="middle" class="ob2" onmouseover="this.className='ob1';" onmouseout="this.className='ob2';">
     <img name="MYplugin" align="absmiddle" id="oAdobelink" value="oAdobelink" src="<? echo $ipre; ?>link.gif" width="80" height="18" style='cursor: hand;'></td>

     <!--- Photo Album --->
     <td width="81" height="20" align="center" valign="middle" class="ob2" onmouseover="this.className='ob1';" onmouseout="this.className='ob2';">
     <img name="MYphoto" align="absmiddle" id="oPhotoAlbum" value="oPhotoAlbum" src="<? echo $ipre; ?>photoalbum.gif" width="80" height="18" style='cursor: hand;'></td>

     <!--- Blogs --->
     <td width="81" height="20" align="center" valign="middle" class="ob2" onmouseover="this.className='ob1';" onmouseout="this.className='ob2';">
     <img name="MYblog" align="absmiddle" id="oBlog" value="oBlog" src="<? echo $ipre; ?>blog.gif" width="80" height="18" style='cursor: hand;'></td>

     <!--- Faqs --->
     <td width="81" height="20" align="center" valign="middle" class="ob2" onmouseover="this.className='ob1';" onmouseout="this.className='ob2';">
     <img name="MYfaq" align="absmiddle" id="oFaq" value="oFaq" src="<? echo $ipre; ?>faq.gif" width="80" height="18" style='cursor: hand;'></td>

     <!--- Addons/Mods --->
     <td width="81" height="20" align="center" valign="middle" class="ob2" onmouseover="this.className='ob1';" onmouseout="this.className='ob2';" onClick="show_mods();">
     <img name="addons" align="absmiddle" id="addons" value="addons" src="<? echo $ipre; ?>addons.gif" width="80" height="18" style='cursor: hand;'>
     </td>

<?
//eval(hook("pe_ff-draggable_page_object", basename(__FILE__)));
?>

     <!--- Blank Object Spacers --->
     <!--- <td width="81" height="20" align="center" valign="middle" class="ob2">&nbsp;</td> --->
     <!--- <td width="81" height="20" align="center" valign="middle" class="ob2">&nbsp;</td> --->
    </tr>

   </table>
  </td>
 </tr>
</table>
</div>


    <!---###################################################################################################--->
    <!----------------------------------------- ADDONS / MODS VIEW -------------------------------------------->
    <!---###################################################################################################--->

<div id="objectbar_mods">



<!---###################################################################################################---
						             _     _                            __  __           _
						    /\      | |   | |                          |  \/  |         | |
						   /  \   __| | __| | ___  _ __  ___   ______  | \  / | ___   __| |___
						  / /\ \ / _` |/ _` |/ _ \| '_ \/ __| |______| | |\/| |/ _ \ / _` / __|
						 / ____ \ (_| | (_| | (_) | | | \__ \          | |  | | (_) | (_| \__ \
						/_/    \_\__,_|\__,_|\___/|_| |_|___/          |_|  |_|\___/ \__,_|___/

<!---###################################################################################################--->

<table border="0" cellpadding="0" cellspacing="0" align="left" width="780" height="100%" style="display: block;">
 <tr>
  <td id="imgdesc2" colspan="9" class="fprev_note" style="width: 100%; border: 0px; padding: 3px; text-align: center; font-style: italic;">
   <? echo lang("Click on an object below and drag it onto a drop zone for page placement."); ?></td>
 </tr>
 <tr>
  <td align="center" valign="top" colspan="9">
   <table border="0" cellpadding="0" cellspacing="0" align="center" width="780" style="border: 1px solid #245981;">

    <!---###################################################################################################---
    <!------------------------------------ Drag and Drop Icons: ROW ONE --------------------------------------->
    <!---###################################################################################################--->
    <tr>

     <!-- My Mods -->
     	<td align="left" valign="top" class="ob2" style="width: 100%; height: 70px;">

     	<?
     		$top = 20;
     		$left = 3;
     		$second_row = 0;
     		for($x=0;$x<$extend_count;$x++){
     			if($x > 2 && $second_row != 1){
     				$top = 20;
     				$left = 88;
     				$second_row = 1;
     			}
	     		echo "<div id=\"".$mod_props[$x]['draggable_object_id']."\" name=\"".$mod_props[$x]['draggable_object_id']."\" class=\"ob2\" onmouseover=\"this.className='ob1';\" onmouseout=\"this.className='ob2';\" style=\"position:absolute;left:".$left."px;top:".$top."px;width:81px;height:20px;\">\n";
	     		echo "	<img align=\"absmiddle\" src=\"../../../plugins/".$mod_props[$x]['plugin_folder']."/".$mod_props[$x]['draggable_object_image']."\" width=\"80\" height=\"18\" vspace=\"1\" hspace=\"1\" style=\"cursor: hand;\">\n";
	     		echo "</div>\n";
	     		$top += 24;
			}

     		echo "<div id=\"MOD1\" name=\"MOD1\" class=\"ob2\" onmouseover=\"this.className='ob1';\" onmouseout=\"this.className='ob2';\" onClick=\"show_mods();\" style=\"float: right;width:81px;height:20px;\">\n";
     		echo "	<img align=\"absmiddle\" src=\"".$ipre."back.gif\" width=\"80\" height=\"18\" vspace=\"1\" hspace=\"1\" style=\"cursor: hand;\">\n";
     		echo "</div>\n";




     	?>


     	</td>

    </tr>
   </table>
  </td>
 </tr>
</table>
</div>



<!---###################################################################################################--->
<!-----           ___                       ____                                                        --->
<!-----          |   \  _ _  ___  _ __     |_  / ___  _ _   ___  ___                                    --->
<!-----          | |) || '_|/ _ \| '_ \     / / / _ \| ' \ / -_)(_-<                                    --->
<!-----          |___/ |_|  \___/| .__/    /___|\___/|_||_|\___|/__/                                    --->
<!-----                          |_|                                                                    --->
<!---###################################################################################################--->

<div id="editor">
	<?

	/**********************************************************************
      New Page Editor Drag and Drop in ie and Firefox- (Joe Lain 10-12-05)
    ----------------------------------------------------------------------
    The Page Editor's obj bar icons are draggable images that are identified
    by their name.  As soon as an icon is dragged my_PickFunc() makes the
    name of the icon available.  When the icon is dropped my_DropFunc()
    gets the coordinates of the icon and finds the cell that it was dropped
    in.
	/**********************************************************************/

   $leftPX = 50;
   $rightPX = 275;
   $topPX = 120;
   $botPX = 205;
   $c = 0;


   # Will contain comma-sep list of box ids
   $box_ids = "";

   # Holds scroll-up / scroll-down buttons
   $scrollers = "";

	// Ouput exactly 10 rows of drop zones
	//===========================================
	for ($x=1;$x<=10;$x++) {

		// Ouput each cell with correct pre-existing content (if any)
		//---------------------------------------------------------------
		for ($y=1;$y<=3;$y++) {
		   $c++;
			$areaId = "R" . $x . "C" . $y; // Used to pull existing cell content from loaded var data of same name (Ends up as 'R2C3' for 'ROW 2, COL 3')
			$tdid = "TD".$areaId; // Used to identify the cell in js
			$contentVar = ${$areaId}; // Pull existing zone content??

			// Format cell properties (b/c nobody likes to side scroll, least of all us dev types)
			//-------------------------------------------------------------------------------------------
			$zProps = " id=\"".$tdid."\" value=\"".$tdid."\"";
			$zProps .= " style=\"position:absolute; width:225; height:85; z-index:50; left: ".$leftPX."px; right: ".$rightPX."px; top: ".$topPX."px; bottom: ".$botPX."px; overflow: hidden;\"";

			// Now echo drop zone cell
			// --------------------------------

			echo "     <div class=\"editTable\" valign=\"top\" align=\"center\" bgcolor=\"".$option_background."\"".$zProps.">\n";

//       Testing for cell position
//			echo "      left= (".$leftPX.") right= (".$rightPX.")<br>";
//			echo "      top= (".$topPX.") bottom= (".$botPX.")<br>";

   		$SUleftPX = $leftPX + 210;
         $SUtopPX = $topPX + 1;
   		$SDleftPX = $leftPX + 210;
         $SDtopPX = $topPX + 70;
         // Make pixel.gif smaller in empty cells for better display

         //echo "      <div onmouseover=\"return escape('Scroll Up');\" style=\"border: 2px solid green; position:absolute; display:block; z-index:600; left: 1px; top: 1px; cursor: pointer;\"><img src=../../includes/display_elements/graphics/up-scroll.gif height=15 width=15>here</div>\n";

         $findThis = '[a-zA-Z0-9]';

         if ( eregi("pixel.gif",$contentVar) || !eregi($findThis, $contentVar) )
         {
            $contentVar = "<IMG height=\"50%\" src=\"pixel.gif\" width=\"199\" border=\"0\">";
      	   $scrollers .= "      <div onmouseover=\"return escape('Scroll Up');\" id='SU".$tdid."' style=\"border: 0px solid red; position:absolute; display:none; z-index:600; left: ".$SUleftPX."px; top: ".$SUtopPX."px; cursor: pointer;\" onClick=\"scroll_up('".$tdid."')\"><img src=\"../../includes/display_elements/graphics/up-scroll.gif\" height=\"15\" width=\"15\" /></div>\n";
      	   $scrollers .= "      <div onmouseover=\"return escape('Scroll Down');\" id='SD".$tdid."' style=\"border: 0px solid red; position:absolute; display:none; z-index:600; left: ".$SDleftPX."px; top: ".$SDtopPX."px; cursor: pointer;\" onClick=\"scroll_down('".$tdid."')\"><img src=../../includes/display_elements/graphics/down-scroll.gif height=15 width=15></div>\n";
         }else{
      	   $scrollers .= "      <div onmouseover=\"return escape('Scroll Up');\" id='SU".$tdid."' style=\"border: 0px solid red; position:absolute; display:block; z-index:600; left: ".$SUleftPX."px; top: ".$SUtopPX."px; cursor: pointer;\" onClick=\"scroll_up('".$tdid."')\"><img src=\"../../includes/display_elements/graphics/up-scroll.gif\" height=\"15\" width=\"15\" /></div>\n";
      	   $scrollers .= "      <div onmouseover=\"return escape('Scroll Down');\" id='SD".$tdid."' style=\"border: 0px solid red; position:absolute; display:block; z-index:600; left: ".$SDleftPX."px; top: ".$SDtopPX."px; cursor: pointer;\" onClick=\"scroll_down('".$tdid."')\"><img src=../../includes/display_elements/graphics/down-scroll.gif height=15 width=15></div>\n";
      	}

         // Fix for <table align=center> bug
         $contentVar = eregi_replace("align=center", "align=sohocenter", $contentVar);
         $contentVar = eregi_replace("align=\"center\"", "align=sohocenter", $contentVar);
         // End Fix
         
         echo "      ".$contentVar."\n";

			// Show contents in textarea for testing
		   //echo "      <br>ContentVar = <br><textarea name=\"mmtestt$y\">".$contentVar."</textarea>\n";


			echo "     </div>\n";

		$leftPX = $leftPX + 230;
      $rightPX = $rightPX + 230;

		# Store all box ids in array for later looping in js
		$box_ids .= ",\"".$tdid."\"";

		}
	$leftPX = 50;
   $rightPX = 275;

   $topPX = $topPX + 90;
   $botPX = $botPX + 90;
	}

   # Strip leading comma
   $box_ids = substr($box_ids, 1);

   # output scroll buttons
   echo "<!--- Start scroll buttons -->\n";
   echo $scrollers;
   echo "<!--- End scroll buttons -->\n";
	?>

</div>

<?




###########################################################################################################
### ALL LAYERS BELOW THIS, ARE LAYERS THAT APPEAR ON TOP OF THE OBJECT LAYER WHEN AN OBJECT IS DROPPED ON
### A DROP AREA. FOR INSTANCE: THESE LAYERS ARE WHERE USERS WOULD SELECT IMAGES, DATABASE TABLES, ETC.
###########################################################################################################

include("includes/layer_props.php");


# COMMENTED OUT: For some reason this must go below my_PickFunc and such or move cursor breaks for all objects
if ( sitepal_allowed() ) {
   # sitepal_dialog
   include_once("../sitepal/page_editor/props_dialog-html-ff.php");
}

?>




<!-- ############################################################# -->
<!-- #### Progress Bar Layer:Save							  #### -->
<!-- ############################################################# -->

<div id="ProgressBarSave" style="position:absolute; width:100%; height:100%; overflow: none;ground: white; z-index:150; left: 0px; top: 0px; visibility: hidden">
	<table border=0 cellpadding="0" cellspacing=0 width=100% height="100%" bgcolor=white><tr><td align=center valign=center>
	<img src="images/save_data.gif" WIDTH=200 HEIGHT=30 BORDER=0></td></tr></table>
</div>

<!-- ############################################################# -->
<!-- #### Progress Bar Layer: NO SAVE				 		 #### -->
<!-- ############################################################# -->

<DIV ID="NOSAVE_LAYER" style="position:absolute; background: white; left:0px; top:0px; width:100%; height: 100%; z-index:150; border: 1px inset black; visibility: hidden; overflow: none;">

  <table border=0 cellpadding=0 width=100% height=100% bgcolor=WHITE>
    <tr>
      <td align=center valign=middle class=text><FONT COLOR=#999999 STYLE='font-size: 8pt; font-family: Arial;'>
		<img src="../../../icons/loading.gif" width=137 height=30 border=0><br clear=all>No modifications have been made.
      </FONT></td>
    </tr>
  </table>

</DIV>

<!-- ############################################################# -->
<!-- #### Progress Bar Layer:DELETE					  #### -->
<!-- ############################################################# -->

<div id="DELETEPAGE" style="position:absolute; width:100%; height:100%; z-index:160; left: 0px; top: 0px; overflow: none; visibility: hidden">
<?
echo ("<table border=0 cellpadding=0 cellspacing=0 width=100% height=100% bgcolor=maroon><tr><td align=center valign=center>\n");
echo ("<font face=Arial size=4 color=white><B>DELETING \"".$_GET['currentPage']."\" PAGE NOW...</font></b></td></tr></table>\n");
?>
</div>


<?
//###################################################################################################
//						             _     _                            __  __           _
//						    /\      | |   | |                          |  \/  |         | |
//						   /  \   __| | __| | ___  _ __  ___   ______  | \  / | ___   __| |___
//						  / /\ \ / _` |/ _` |/ _ \| '_ \/ __| |______| | |\/| |/ _ \ / _` / __|
//						 / ____ \ (_| | (_| | (_) | | | \__ \          | |  | | (_) | (_| \__ \
//						/_/    \_\__,_|\__,_|\___/|_| |_|___/          |_|  |_|\___/ \__,_|___/
//
//###################################################################################################
//include($MYmod1_display);
eval(hook("pe_ff-properties_dialog_layer", basename(__FILE__)));
?>


<script language="javascript">

<?

if ($theNewsFlag == 1) {
	$cpDis = "Editing Newsletter: \"<U>".$_GET['currentPage']."</U>\"";
} else {
	$cpDis = "Editing: \"<U>".$_GET['currentPage']."</U>\"";
}
echo "var newStatus = '$cpDis';\n";

?>

//show_hide_layer('MAIN_MENU_LAYER?header','','hide');
//show_hide_layer('PAGE_EDITOR_LAYER?header','','show');

show_hide_layer('NOSAVE_LAYER','','hide');


//Define global variables
var dot_com = $('dot_com').value
//alert(dot_com)

</script>

<script type="text/javascript">

// All images that are draggable are sent to SET_DHTML to allow them
// to be dragged and interact with the editor.
// Joe Lain 10/10/05
<?
# SitePal?
if ( sitepal_allowed() ) {
   $sitepal_dhtml = ", \"MYsitepal\"+CLONE";
}
?>

SET_DHTML(RESET_Z, SCROLL, CURSOR_MOVE, TRANSPARENT<? echo $sitepal_dhtml; ?>, "MYeditor"+CLONE, "MYimages"+CLONE, "MYforms"+CLONE, "MYdocs"+CLONE, "MYpdfs"+CLONE, "MYauth"+CLONE, "MYcust"+CLONE, "MYshop"+CLONE, "MYsearch"+CLONE, "MYsignup"+CLONE, "MYcalendar"+CLONE, "MYdirections"+CLONE, "MYdate"+CLONE, "MYprint"+CLONE, "MYemail"+CLONE, "MYcounter"+CLONE, "MYpopup"+CLONE, "MYaudio"+CLONE, "MYvideo"+CLONE, "MYplugin"+CLONE, "MYphoto"+CLONE, "MYblog"+CLONE, "MYfaq"+CLONE, "MYdelete"+CLONE);


// The following two functions override their empty namesakes predefined in wz_dragdrop.js.
// They are automatically invoked from wz_dragdrop.js when a drag operation starts
// and ends, respectively.

/* my_PickFunc IS AUTOMATICALLY CALLED WHEN AN ITEM STARTS TO BE DRAGGED.
The following objects/properties are accessible from here:

- dd.e: current mouse event
- dd.e.property: access to a property of the current mouse event.
  Mostly requested properties:
  - dd.e.x: document-related x co-ordinate
  - dd.e.y: document-related y co-ord
  - dd.e.src: target of mouse event (not identical with the drag drop object itself).
  - dd.e.button: currently pressed mouse button. Left button: dd.e.button <= 1

- dd.obj: reference to currently dragged item.
- dd.obj.property: access to any property of that item.
- dd.obj.method(): for example dd.obj.resizeTo() or dd.obj.swapImage() .
  Mostly requested properties:
	- dd.obj.name: image name or layer ID passed to SET_DHTML();
	- dd.obj.x and dd.obj.y: co-ordinates;
	- dd.obj.w and dd.obj.h: size;
	- dd.obj.is_dragged: 1 while item is dragged, else 0;
	- dd.obj.is_resized: 1 while item is resized, i.e. if <ctrl> or <shift> is pressed, else 0*/

function my_PickFunc()
{
   // Get the name of the dragged image, happends onMouseDown
   thisObj = dd.obj.name;
   //alert(thisObj);
}


function my_DropFunc()
{
   //alert(thisObj);
   // Get how far the page has been scrolled to account for absolute pos.
   offsetVal = document.getElementById('editor').scrollTop;

   // Write the coordinates of the dropped item into vars
   var Xval = dd.obj.x;
   var Yval = dd.obj.y;

   // Array of box ids
   var boxId = new Array(<? echo $box_ids; ?>);
   //alert('num boxes: ['+boxId.length+']');

   // Loop through boxes
   for ( b = 0; b < 30; b++ ) {
      Xleft = document.getElementById(boxId[b]).style.left;
      Xleft = Xleft.replace('px','');

      Xright = document.getElementById(boxId[b]).style.right;
      Xright = Xright.replace('px','');

      Ytop = document.getElementById(boxId[b]).style.top;
      Ytop = Ytop.replace('px','');
      Ytop = Ytop - offsetVal;

      Ybottom = document.getElementById(boxId[b]).style.bottom;
      Ybottom = Ybottom.replace('px','');
      Ybottom = Ybottom - offsetVal;

// Test pos. dropped and box values
//      alert('['+Xval+'] > ['+Xleft+']');
//      alert('['+Xval+'] < ['+Xright+']');
//      alert('['+Yval+'] > ['+Ytop+']');
//      alert('['+Yval+'] < ['+Ybottom+']');

      if (Xval > Xleft && Xval < Xright && Yval > Ytop && Yval < Ybottom) {

        // When img is dropped places it back in its original possition
        var my_item = dd.elements[thisObj];
        my_item.moveTo(my_item.defx, my_item.defy);

        ColRowID = ''
        ColRowID = boxId[b];
        makeScroll(ColRowID);

        document.getElementById(ColRowID).style.backgroundColor= "#999999";



        // Set Global Var's for use in Object Drops
        TableStart = "<table border=0 cellpadding=0 cellspacing=1><tr><td width=199 height=75 align=center valign=middle>";
        dataData = document.getElementById(ColRowID).innerHTML;
        //alert("The pos is : ("+ColRowID+")");

        // decide which cell is being dragged and get contents
				<?
	     		for($x=0;$x<$extend_count;$x++){
					//echo "dd.elements.".$mod_props[$x]['draggable_object_id'].".hide();\n";

					${"MYmod".$x."_image"} = $mod_props[$x]['draggable_object_image'];
					${"MYmod".$x."_display"}=$mod_props[$x]['properties_dialog_file'];
					${'MYmod'.$x.'_drag_id'}=$mod_props[$x]['draggable_object_id'];
					${"MYmod".$x."_props_id"}=$mod_props[$x]['properties_dialog_id'];
					${"MYmod".$x."_folder"}="sohoadmin/plugins/".$mod_props[$x]['plugin_folder'];

	        	   echo "if(thisObj == \"".${'MYmod'.$x.'_drag_id'}."\"){\n";
	        	  	echo "	show_hide_layer('objectbar_mods','','hide','".$mod_props[$x]['properties_dialog_id']."','','show');\n";
	        	  	echo "}\n";
				}
				?>

           if (thisObj == "MYeditor") {
               //show_hide_icons();
               loadEditor();
            }

           if (thisObj == "MYimages") {
              show_hide_layer('objectbar','','hide','imageDrop','','show');
           }
           if (thisObj == "MYforms") {
              loadwindow('formlib/forms.php?dropArea='+ColRowID+'&selkey=Forms&=SID',600,500,'that');
           }
           if (thisObj == "MYdocs") {
              show_hide_layer('objectbar','','hide','ULDOCLAYER','','show');
           }
           if (thisObj == "MYpdfs") {
              show_hide_layer('objectbar','','hide','pdflayer','','show');
           }
           if (thisObj == "MYauth") {
              show_hide_layer('objectbar','','hide','securelayer','','show');
           }
           if (thisObj == "MYcust") {
              show_hide_layer('objectbar','','hide','customlayer','','show');
           }
           if (thisObj == "MYshop") {
              show_hide_layer('objectbar','','hide','shoppingCartLayer','','show');
           }
           if (thisObj == "MYsearch") {
              show_hide_layer('objectbar','','hide','memDatabase','','show');
           }
           if (thisObj == "MYsignup") {
              loadwindow('formlib/forms.php?dropArea='+ColRowID+'&selkey=Newsletter&=SID',600,500,'that');
           }
           if (thisObj == "MYcalendar") {
              show_hide_layer('objectbar','','hide','calendarlayer','','show');
           }
           if (thisObj == "MYdirections") {
              show_hide_layer('objectbar','','hide','mapquest','','show');
           }
           if (thisObj == "MYdate") {
              OKdateStamp();
           }
           if (thisObj == "MYprint") {
              printButton();
           }
           if (thisObj == "MYemail") {
              emailfriend();
           }
           if (thisObj == "MYcounter") {
              pageCounter();
           }
           if (thisObj == "MYpopup") {
              show_hide_layer('objectbar','','hide','popupwin','','show');
           }
           if (thisObj == "MYaudio") {
              show_hide_layer('objectbar','','hide','mp3layer','','show');
           }
           if (thisObj == "MYvideo") {
              show_hide_layer('objectbar','','hide','videolayer','','show');
           }
           if (thisObj == "MYplugin") {
              show_hide_layer('objectbar','','hide','sitelinks','','show');
           }
           if (thisObj == "MYphoto") {
              //photoalbum();
              show_hide_layer('objectbar','','hide','photoLayer','','show');
           }
           if (thisObj == "MYblog") {
              show_hide_layer('objectbar','','hide','blogLayer','','show');
           }
           if (thisObj == "MYfaq") {
              show_hide_layer('objectbar','','hide','faqLayer','','show');
           }
           if (thisObj == "MYdelete") {
              var DelMe = confirm("This will delete all data in this cell, would you like to continue?");
              if ( DelMe == true ) {
               clearCell();
               makeUnScroll(ColRowID);
               } else {
               document.getElementById(ColRowID).style.backgroundColor= "#FFFFFF";
            }
         }
<?
if ( sitepal_allowed() ) {
?>
           if (thisObj == "MYsitepal") {
              show_hide_icons();
              show_hide_layer('objectbar','','hide','sitepal_dialog','','show');
           }
<?
} // End if sitepal_allowed()
?>
      } else { // End 'within box' check
        // When img is dropped places it back in its original possition
        var my_item = dd.elements[thisObj];
        my_item.moveTo(my_item.defx, my_item.defy);
      }

   } // End loop through boxes

} // End my_DropFunc

objects = document.getElementsByTagName("object");
for (var i = 0; i < objects.length; i++)
{
    objects[i].outerHTML = objects[i].outerHTML;
}

</script>

<script language="JavaScript" type="text/javascript" src="../../includes/display_elements/wz_tooltip.js"></script>

<?
if ( sitepal_allowed() ) {
   # sitepal_dialog
//   include_once("../sitepal/page_editor/props_dialog-html-ff.php");
}
?>
</body>
</html>