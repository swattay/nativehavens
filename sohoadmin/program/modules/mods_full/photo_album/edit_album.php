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
include("../../../includes/product_gui.php");

if ($id == "") {
	header("Location: photo_album.php?=SID");
	exit;
}

######################################################################
## SET GLOBALLY, THE NUMBER OF IMAGES BY DEFAULT THAT EACH ALBUM 
## CREATED CAN HAVE... THIS WILL TAKE CARE OF EVERYTHING.
######################################################################

$IMAGES_IN_ALBUM = 25;

#######################################################
### PROCESS "SAVE ALBUM" ACTION					    ###	
#######################################################

if ($action == "update") {

	// Update Our Album Table
	
	for ($x=1;$x<=$IMAGES_IN_ALBUM;$x++) {
	
		$tmp = "IMAGE_NAME" . $x;
		$IMAGE_NAME .= ${$tmp}.";";
		
		$tmp = "CAPTION" . $x;
		$daCap = stripslashes(${$tmp});
   	$daCap = eregi_replace("'", "", $daCap);
   	$daCap = str_replace("\"", "", $daCap);
		$CAPTION .= $daCap.";";
		
		$tmp = "LINK" . $x;
		$daLink = stripslashes(${$tmp});
   	$daLink = eregi_replace("'", "", $daLink);
   	$daLink = str_replace("\"", "", $daLink);
		$LINK .= $daLink.";";

	} // End For Loop

	
	mysql_query("UPDATE photo_album SET IMAGE_NAMES = '$IMAGE_NAME',
				CAPTION = '$CAPTION',
				LINK = '$LINK' WHERE PRIKEY = '$id'");
				
	header("Location: photo_album.php?=SID");
	exit;
	
}

#######################################################
### READ CURRENT SETUP INTO MEMORY				    ###	
#######################################################

$result = mysql_query("SELECT * FROM photo_album WHERE PRIKEY = '$id'");
$row = mysql_fetch_array($result);

$imgg = split(";", $row[IMAGE_NAMES]);
$cap = split(";", $row[CAPTION]);
$lin = split(";", $row[LINK]);

##################################################################################
### READ IMAGE FILES INTO MEMORY			    
##################################################################################

$img_selection = "     <OPTION VALUE=\" \">[No Image]</OPTION>\n";

$count = 0;
$directory = "$doc_root/images";
$handle = opendir("$directory");
	while ($files = readdir($handle)) {
		if (strlen($files) > 2) {
			$count++;
			$imageFile[$count] = ucwords($files) . "~~~" . $files;
		}
	}
$numImages = $count;
closedir($handle);

if ($count != 0) {
	sort($imageFile);
	if ($count == 1) {
		$imageFile[0] = $imageFile[1];
	}
	$numImages--;	
}


for ($x=0;$x<=$numImages;$x++) {

	$thisImage = split("~~~", $imageFile[$x]);
	$img_selection .= "     <OPTION VALUE=\"$thisImage[1]\">$thisImage[0]</OPTION>\n";
}


#######################################################
### START HTML/JAVASCRIPT CODE					    ###	
#######################################################

$result = mysql_query("SELECT * FROM photo_album WHERE PRIKEY = '$id'");
while ($mike = mysql_fetch_array($result)) {
	$MOD_TITLE = lang("Edit Album").": $mike[ALBUM_NAME]";
}


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

function cancel_edit() {
	window.location = 'photo_album.php?<?=SID?>';
}

SV2_showHideLayers('NEWSLETTER_LAYER?header','','hide');
SV2_showHideLayers('MAIN_MENU_LAYER?header','','show');
SV2_showHideLayers('CART_MENU_LAYER?header','','hide');
SV2_showHideLayers('DATABASE_LAYER?header','','hide');

//-->
</script>


<style>

form {
   margin:0;
}

.feature_contain {
	padding: 0;
	margin: 0;
	border-left: 1px solid #A2ADBC;
	border-bottom: 1px solid #A2ADBC;
	font: 12px "Trebuchet MS", Verdana, Arial, Helvetica, sans-serif;
	color: #33393F;
	text-align: center;
	background-color: #fff;
}

.feature_contain th {
	font: bold 12px "Trebuchet MS", Verdana, Arial, Helvetica, sans-serif;
	background: #D9E2E1;
	border-right: 1px solid #A2ADBC;
	border-bottom: 1px solid #A2ADBC;
	border-top: 1px solid #A2ADBC;
	padding:2;
}

.feature_contain td {
   padding-bottom:7px;
}

.cal_btn {
   margin:0;
   text-align: center;
   border: 2px outset #CFCFCF;
   cursor: pointer;
   background: #A7DFAF;
}

.cal_btn_over {
   text-align: center;
   border: 2px outset #AFFFBA;
   cursor: pointer;
   background: #6FDF7E;
}

.cal_del_btn {
   margin:0;
   text-align: center;
   border: 2px outset #CFCFCF;
   cursor: pointer;
   background: #FF0000;
   color: #FFFFFF;
}

.cal_del_btn_over {
   text-align: center;
   border: 2px outset #CCCCCC;
   cursor: pointer;
   background: #FF4F4F;
   color: #FFFFFF;
}


</style>


<?

$THIS_DISPLAY = "<form name=albumform method=post action=\"edit_album.php\">\n";
$THIS_DISPLAY .= "<input type=hidden name=id value=\"$id\">\n";
$THIS_DISPLAY .= "<input type=hidden name=action value=\"update\">\n";

$THIS_DISPLAY .= "<table border=0 cellpadding=5 cellspacing=0 width=\"750\" class=\"feature_contain\">";

	$THIS_DISPLAY .= "<TR>\n";
	$THIS_DISPLAY .= "<th align=\"center\" valign=\"top\" style=\"border-right: 0;\">\n";
	$THIS_DISPLAY .= " ".lang("Image Preview")."\n";
	$THIS_DISPLAY .= "</th>\n";
	

	$THIS_DISPLAY .= "<th align=\"center\" valign=\"top\">\n";
	$THIS_DISPLAY .= " ".lang("Details")."\n";
	$THIS_DISPLAY .= "</th>\n";
	$THIS_DISPLAY .= "</TR>\n";

$THIS_DISPLAY .= "<SCRIPT LANGUAGE=JAVASCRIPT>\n";
$THIS_DISPLAY .= "function update_demo(value,what) {\n";
$THIS_DISPLAY .= "	eval(\"DEMO\"+what+\".innerHTML='<img src=http://$this_ip/images/\"+value+\" width=50 border=1 align=absmiddle>';\");\n";
$THIS_DISPLAY .= "} // End Func\n\n";
$THIS_DISPLAY .= "</SCRIPT>\n\n";


$bgcolor = "#EFEFEF";

/// Build each slide - New School MM way (finish later)
/*==========================================================================================*
$img_limit = 25; // Total number of slides allowed in album

// Pull data from photo_album table
//----------------------------------------
$selph = mysql_query("SELECT * FROM photo_album WHERE PRIKEY = '$id'");
$getAl = mysql_fetch_array($selph);

// Split up db data into mini-arrays
//----------------------------------------
$pa_img = explode(";", $getAl[IMAGE_NAMES]);
$pa_cap = explode(";", $getAl[CAPTION]);
$pa_link = explode(";", $getAl[LINK]);

// Compile mini-arrays into per-slide array
//-------------------------------------------
for ( $p = 0; $p < $img_limit; $p++ ) {
   $i = $p + 1; // Index array by actual slide number
   $slide[$i] = array( img=>$pa_img[$p], caption=>$pa_cap[$p], link=>$pa_link[$p] );
}

for ( $s = 1; $s <= $img_limit; $s++ ) {
   $THIS_DISPLAY .= " <tr>\n";
   $THIS_DISPLAY .= "  <td class=text align=center valign=middle bgcolor=$bgcolor style=\"border-bottom: 1px solid black;\">\n";
   $THIS_DISPLAY .= "   <b><span id=DEMO".$s.">".$s."</span></b>\n";
	$THIS_DISPLAY .= "  </td>\n";
	
	$THIS_DISPLAY .= "  <td class=text align=left valign=top bgcolor=$bgcolor style=\"border-bottom: 1px solid black;\">\n";
	$THIS_DISPLAY .= "   <table border=0 cellpadding=2 cellspacing=0 align=left class=text>\n";
	$THIS_DISPLAY .= "    <tr>\n";
	$THIS_DISPLAY .= "     <td align=right valign=top>";
	$THIS_DISPLAY .= "      ".lang("Image").":\n";
	
	// Show image drop-down list
	//--------------------------------------
	$THIS_DISPLAY .= "      <select name=\"IMAGE_NAME".$x."\" style=\"font-size: 10px; width: 400px; background: #efefef; color: darkblue;\" onchange=\"update_demo(this.value,'".$x."');\">\n";
	$THIS_DISPLAY .= "       <option value=\"\">(".$s.")Total[".$img_limit."]</option>\n";
	$THIS_DISPLAY .= "       ".$img_selection."\n";
	$THIS_DISPLAY .= "      </select>\n";

	$THIS_DISPLAY .= "     </td>\n";
	$THIS_DISPLAY .= "    </tr>\n";

	## Caption:
	##--------------------
	$THIS_DISPLAY .= "    <tr>\n";
	$THIS_DISPLAY .= "     <td align=\"right\" valign=\"top\">";
	$THIS_DISPLAY .= "      ".lang("Caption").":\n";
	$THIS_DISPLAY .= "      <input type=\"text\" name=\"CAPTION".$s."\" value=\"".$slide[$s]['caption']."\" class=\"text\" style=\"width: 400px;\">\n";
	$THIS_DISPLAY .= "     </td>\n";
	$THIS_DISPLAY .= "    </tr>\n";
	
	$THIS_DISPLAY .= "    <tr>\n";
	$THIS_DISPLAY .= "     <td align=\"right\" valign=\"top\">";
	
	$THIS_DISPLAY .= "      ".lang("Link").":\n";
	$THIS_DISPLAY .= "      <input type=\"text\" name=\"LINK".$x."\"  VALUE=\"".$slide[$s]['link']."\" class=\"text\" STYLE=\"WIDTH: 400px;\">";

	$this_display .= "     </td>\n";
	$this_display .= "    </tr>\n";
	$this_display .= "   </table>\n";

	$this_display .= "  </td>\n";
	$this_display .= " </tr>\n";

}
/*==========================================================================================*/

###################################################################################################
/// Loop through photo slides and output stored info for each
###================================================================================================
for ($x = 1; $x <= $IMAGES_IN_ALBUM; $x++) {

	$y = $x - 1;
	
	if ($bgcolor == "#efefef") { $bgcolor = "WHITE"; } else { $bgcolor = "#efefef"; }
	
	$THIS_DISPLAY .= " <TR>\n";
	$THIS_DISPLAY .= "  <td class=text align=center valign=middle bgcolor=".$bgcolor." style='border-bottom: 1px solid #A2ADBC;'>\n";
	$THIS_DISPLAY .= "   <B><SPAN id=DEMO".$x.">".$x."</SPAN></B>\n";
	$THIS_DISPLAY .= "  </td>\n";

	$THIS_DISPLAY .= "  <td class=text align=left valign=top bgcolor=".$bgcolor." style='border-bottom: 1px solid #A2ADBC; border-right: 1px solid #A2ADBC;'>\n";

	$THIS_DISPLAY .= "   <TABLE BORDER=0 CELLPADDING=2 CELLSPACING=0 ALIGN=center CLASS=text>\n";
	$THIS_DISPLAY .= "    <TR>\n";
	$THIS_DISPLAY .= "     <td align=right valign=top>";
	$THIS_DISPLAY .= "      <b>".lang("Image").":</b>\n";
	
	// Show image drop-down list
	//--------------------------------------
	$THIS_DISPLAY .= "      <SELECT NAME=\"IMAGE_NAME".$x."\" STYLE=\"font-size: 10px; WIDTH: 400px; background: #EFEFEF; color: darkblue;\" ONCHANGE=\"update_demo(this.value,'".$x."');\">\n";
	//$THIS_DISPLAY .= "       <option value=\"\">($x - $y)Total[".$IMAGES_IN_ALBUM."]</option>\n";
	$THIS_DISPLAY .= "       ".$img_selection."\n";
	$THIS_DISPLAY .= "      </SELECT>\n";

	$THIS_DISPLAY .= "     </TD>\n";
	$THIS_DISPLAY .= "    </TR>\n";
	$THIS_DISPLAY .= "    <TR>\n";
	$THIS_DISPLAY .= "     <td align=right valign=top>";

	$THIS_DISPLAY .= "      <b>".lang("Caption").":</b>\n";
	$THIS_DISPLAY .= "      <INPUT TYPE=TEXT NAME=\"CAPTION$x\" VALUE=\"".$cap[$y]."\" class=text  STYLE='WIDTH: 400px;'>\n";

	$THIS_DISPLAY .= "     </TD>\n";
	$THIS_DISPLAY .= "    </TR>\n";
	$THIS_DISPLAY .= "    <TR>\n";
	$THIS_DISPLAY .= "     <td align=right valign=top>";

	$THIS_DISPLAY .= "      <b>".lang("Link").":</b>\n";
	$THIS_DISPLAY .= "      <INPUT TYPE=TEXT NAME=\"LINK$x\"  VALUE=\"".$lin[$y]."\" class=text STYLE='WIDTH: 400px;'>";

	$THIS_DISPLAY .= "     </TD>\n";
	$THIS_DISPLAY .= "    </TR>\n";
	$THIS_DISPLAY .= "   </TABLE>\n";

	$THIS_DISPLAY .= "  </td>\n";
	$THIS_DISPLAY .= " </TR>\n";

}
/*==========================================================================================*/

$THIS_DISPLAY .= "</TABLE>\n\n";

			$THIS_DISPLAY .= "<BR><BR><div align=center>\n";
			$THIS_DISPLAY .= "<INPUT TYPE=SUBMIT VALUE=\" ".lang("Save Album")." \" style='width: 100px;' class=\"btn_green\">\n";
			$THIS_DISPLAY .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n";
			$THIS_DISPLAY .= "<INPUT TYPE=BUTTON VALUE=\" ".lang("Cancel Edit")." \"  style='width: 100px;' ONCLICK=\"cancel_edit();\" class=\"btn_red\"></div>\n";
			
$THIS_DISPLAY .= "</FORM>\n";

	$THIS_DISPLAY .= "<SCRIPT LANGUAGE=JAVASCRIPT>\n\n";
	
	for ($x=1;$x<=$numImages;$x++) {
		$y = $x - 1;
		if (strlen($imgg[$y]) > 3) {
			$THIS_DISPLAY .= "albumform.IMAGE_NAME$x.value = '".$imgg[$y]."';\n";	
			$THIS_DISPLAY .= "DEMO$x.innerHTML = '<IMG SRC=http://$this_ip/images/$imgg[$y] width=50 border=1 align=absmiddle>';";
		}
	}
	
	$THIS_DISPLAY .= "</SCRIPT>\n";



$THIS_DISPLAY .= "  </td>\n";
$THIS_DISPLAY .= " </tr>\n";
$THIS_DISPLAY .= "</table>\n";
####################################################################

echo $THIS_DISPLAY;

####################################################################

# Grab module html into container var
$module_html = ob_get_contents();
ob_end_clean();

$instructions = lang("Edit this albums images, text and link.");

# Build into standard module template
$module = new smt_module($module_html);
$module->meta_title = lang("Photo Album");
$module->add_breadcrumb_link(lang("Photo Album"), "program/modules/mods_full/photo_album/photo_album.php");
$module->icon_img = "skins/".$_SESSION['skin']."/icons/full_size/photo_albums-enabled.gif";
$module->heading_text = lang("Photo Album");
$module->description_text = $instructions;
$module->good_to_go();
?>