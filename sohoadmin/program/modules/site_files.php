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
include("../includes/product_gui.php");



# Start buffering output
ob_start();
?>

<style type="text/css">
tr.row_bgcolor1 td,
tr.row_bgcolor2 td {
   border-bottom: 1px dashed #ccc;
}
tr.row_bgcolor1 td { background-color: #fff; }
tr.row_bgcolor2 td { background-color: #f8f9fd; }
</style>

<script language="JavaScript">
<!--

function killErrors() {
		return true;
	}

window.onerror = killErrors;

function MM_popupMsg(msg) { //v1.0
  alert(msg);
}

function MM_findObj(n, d) { //v3.0
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document); return x;
}

//-->
</script>

<script language="javascript">

function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}

function preview (image) {
	eval("MM_openBrWindow('site_files/prev_image.php?image="+image+"&dot_com=<?echo $dot_com; ?>','imagepreview','width=450,height=300,scrollbars=no,resizable=no');");
}

function download(image,lname) {
	var str = 'site_files/dlfile.php?name='+image+'&localname='+lname+'&dot_com=<? echo $dot_com; ?>';
	// eval("window.location(str);");
	eval("MM_openBrWindow('"+str+"','downloadfile','width=450,height=150');");

}


function update_status(text) {
	// STATUS UPDATE
}

function verify_delete(prikey) {
	//update_status('Verify Delete!');
	var r = document.getElementById('file_delete'+prikey).checked;
	if (r) {
	   //alert('soemthing');
		var tiny = window.confirm('<? echo lang("You have selected for this file to be deleted"); ?>.\n\n<? echo lang("Once you click \"Update Files\" you can not undo this process"); ?>.\n\n<? echo lang("Are you sure you wish to select this file for deletion"); ?>?');
	   	if (tiny != false) {
			// OK With client
	 	  } else {
			document.getElementById('file_delete'+prikey).checked=false;
			//update_status('Idle');
	  	 }
	}
}

function saving_updates() {
	show_hide_layer('Layer2','','hide','Layer1','','hide','Layer3','','show');
}

function upload_now() {
	window.location="upload_files.php";
}

function toggle(targetid) {
  var isnow = document.getElementById(targetid).style.display;
  if ( isnow == 'block' ) {
     document.getElementById(targetid).style.display='none';
  } else {
     document.getElementById(targetid).style.display='block';
  }
}
var p = "File Manager";
parent.frames.footer.setPage(p);

parent.header.flip_header_nav('MAIN_MENU_LAYER');

</script>



<?

#######################################################
####### Read Current Site Files Into Memory ###########
#######################################################

$count = 0;

# MEDIA/ -- Documents and custom includes
$directory = $_SESSION['docroot_path']."/media";
if (is_dir($directory)) {
$handle = opendir("$directory");
	while ($files = readdir($handle)) {
		if (strlen($files) > 2) {
			$count++;
			$tmp = "$directory/$files";
			$tmp_space = filesize($tmp);
			$tmp_srt = $files;
			$site_File[strtolower($files)] = $tmp_srt . "~~~media~~~$tmp_space~~~" . $files;

			# Custom include scripts
			if ( eregi("\.inc", $files) || eregi("\.php", $files) ) {
   			$incFile[$files]['size'] = $tmp_space;
   			$incFile[$files]['name'] = $tmp_srt;
   			$incFile[$files]['dir'] = "media";

   		# Custom HTML
   		} elseif ( eregi("\.html", $files) || eregi("\.htm", $files) ) {
   			$htmlFile[$files]['size'] = $tmp_space;
   			$htmlFile[$files]['name'] = $tmp_srt;
   			$htmlFile[$files]['dir'] = "media";

   		# Custom web forms and text files
   		} elseif ( eregi("\.form", $files) || eregi("\.txt", $files) ) {
   			$formFile[$files]['size'] = $tmp_space;
   			$formFile[$files]['name'] = $tmp_srt;
   			$formFile[$files]['dir'] = "media";

   		# Video files
   		} elseif ( eregi("\.avi", $files) || eregi("\.mov", $files) || eregi("\.mpg", $files) || eregi("\.wmv", $files) || eregi("\.mpeg", $files)) {
   			$vidFile[$files]['size'] = $tmp_space;
   			$vidFile[$files]['name'] = $tmp_srt;
   			$vidFile[$files]['dir'] = "media";

   		# Spreadsheets
   		} elseif ( eregi("\.csv", $files) || eregi("\.xls", $files) ) {
   			$csvFile[$files]['size'] = $tmp_space;
   			$csvFile[$files]['name'] = $tmp_srt;
   			$csvFile[$files]['dir'] = "media";

   		# Documents, PowerPoint Presentations, & Adobe PDF files
   		} elseif ( eregi("\.doc", $files) || eregi("\.pdf", $files) || eregi("\.ppt", $files) ) {
   			$docFile[$files]['size'] = $tmp_space;
   			$docFile[$files]['name'] = $files;
   			$docFile[$files]['dir'] = "media";

   		# Unclassified files
   		} else {
   			$wtfFile[$files]['size'] = $tmp_space;
   			$wtfFile[$files]['name'] = $tmp_srt;
   			$wtfFile[$files]['dir'] = "media";
   		}

		}
	}
closedir($handle);
}


$num = 1;
# Images
$imgFile = array();
$directory = $doc_root."/images";
if (is_dir($directory)) {
   $handle = opendir("$directory");
	while ( $files = readdir($handle) ) {
		if ( strlen($files) > 2 && !is_dir($directory."/".$files) ) {
			$count++;
			$tmp = "$directory/$files";
			$tmp_space = filesize($tmp);
			$tmp_srt = $files;
			$site_File[$files] = $tmp_srt . "~~~images~~~$tmp_space~~~" . $files;
			$imgFile[$files]['size'] = $tmp_space;
			$imgFile[$files]['name'] = $tmp_srt;
//			$SRT_IMG[$num] = $imgFile[$files]['name'];
			$imgFile[$files]['dir'] = "images";
			$num++;
		}
	}
closedir($handle);
//sort($SRT_IMG);
}
//echo testArray($imgFile); exit;


# Custom template HTML files
$directory = "$doc_root/tCustom";
if (is_dir($directory)) {
$handle = opendir("$directory");
	while ($files = readdir($handle)) {
		if (strlen($files) > 2) {
			$count++;
			$tmp = "$directory/$files";
			$tmp_space = filesize($tmp);
			$tmp_srt = $files;
			$site_File[$files] = $tmp_srt . "~~~tCustom~~~$tmp_space~~~" . $files;
			$tplFile[$files]['size'] = $tmp_space;
			$tplFile[$files]['name'] = $tmp_srt;
			$tplFile[$files]['dir'] = "tCustom";
		}
	}
closedir($handle);
}

# CSV files
$directory = "$doc_root/import";
if (is_dir($directory)) {
$handle = opendir("$directory");
	while ($files = readdir($handle)) {
		if (strlen($files) > 2) {
			$count++;
			$tmp = "$directory/$files";
			$tmp_space = filesize($tmp);
			$tmp_srt = $files;
			$site_File[$files] = $tmp_srt . "~~~import~~~$tmp_space~~~" . $files;
         $wtfFile[$files]['size'] = $tmp_space;
         $wtfFile[$files]['name'] = $tmp_srt;
         $wtfFile[$files]['dir'] = "import";
		}
	}
closedir($handle);
}

#######################################################

if ($count > 1) { sort($site_file); };
$file_count = count($site_file);

?>

<!-- ======================================================================================================== -->
<!-- ======================================================================================================== -->
<!-- ======================================================================================================== -->

<DIV ID="Layer3" style="position:absolute; left:0px; top:40%; width:100%; height:110px; z-index:100; border: 2px none #000000; visibility: hidden; overflow: hidden">

  <table border=0 cellpadding=0 width=100% height=100% bgcolor=WHITE>
    <tr>
      <td align=center valign=middle class=text>
		<img src="site_files/upload.gif" width=156 height=30 border=0>

      </td>
    </tr>
  </table>

</DIV>

<!-- ======================================================================================================== -->
<!-- ======================================================================================================== -->
<!-- ======================================================================================================== -->


<form method=post action="site_files/update_files.php" name="CURRENT">
<img src="spacer.gif" height=5 width=500>
<table border=0 cellpadding=0 cellspacing=0 width=100%>
 <tr>
  <td align=center valign=top>
  <?
//   foreach($_SESSION as $var=>$val){
//      echo "var = (".$var.") val = (".$val.")<br>\n";
//   }
  ?>
	<table border="0" cellspacing="0" cellpadding="5" width="100%">
    <tr>
     <td align="center" valign="top" class=text>
	   <table width="95%" border="0" cellspacing="0" cellpadding="3" align="center" class=text>
		 <tr>
		  <td align="right" valign="middle" class="text">
		   <INPUT TYPE=BUTTON CLASS="btn_build" VALUE=" <? echo lang("Upload New Files"); ?> " onclick="upload_now();"  STYLE='width: 150px;' onMouseover="this.className='btn_buildon';" onMouseout="this.className='btn_build';">
		  </td>
		 </tr>
		</table>
		<br>
		<table cellpadding="0" cellspacing="0" border="0" width="98%" align="center" class="feature_group">
		 <tr>
		  <td valign="top">
		   <table cellpadding="3" cellspacing="0" border="0" width="100%">
    		 <tr>
      	  <td NOWRAP width=100% class="fgroup_title">
		      <img src="arrow.gif" width="17" height="13" align="absmiddle"><? echo lang("Current Site Files"); ?>
			  </td>
			 </tr>
			</table>
		  </td>
		 </tr>
		 <tr>
		  <td valign="top" align="center">

			<! -- ## HEADER SETUP ## -->
			<table cellpadding="2" cellspacing="0" width="100%">

<?
#--------------------------------------------------------------------------------
# CALCULATE USER-READABLE FILESIZE
# Accepts: size value, size/flag
# Returns: formatted size value ('size') | flag image if too big ('flag')
#--------------------------------------------------------------------------------
function sizeInfo($filesize, $gimme) {

   # Will contain tag for 'too big' image (if it's too big)
   $red_flag = "";

   # MB
   if ( $filesize >= 1048576 ) {
      $filesize = round($filesize / 1048576 * 100) / 100;
      if ( $filesize > 1 ) {
         $red_flag = "<img src=\"site_files/red_flag.gif\" width=10 height=10 border=0 hspace=5 align=absmiddle>";
      }
      $filesize = $filesize . "&nbsp;MB";

   # KB
   } elseif ( $filesize >= 1024 ) {
      $filesize = round($filesize / 1024 * 100) / 100;
      if ( $filesize > 40 ) {
         $red_flag = "<img src=\"site_files/red_flag.gif\" width=10 height=10 border=0 hspace=5 align=absmiddle>";
      }
      $filesize = $filesize . "&nbsp;KB";

   # Bytes
   } else {
      $filesize = $filesize . "&nbsp;Bytes";
   }

   if ( $gimme == "size" ) {
      return $filesize;
   } elseif ( $gimme == "flag" ) {
      return $red_flag;
   }
}

#--------------------------------------------------------------------------------
# DEFINE IMAGE PREVIEW OPTION
#--------------------------------------------------------------------------------
function prevImg($filedir, $filename) {

   if (eregi("\.gif", $filename) || eregi("\.jpg", $filename) || eregi("\.jpeg", $filename) || eregi("\.bmp", $filename) || eregi("\.tif", $filename) || eregi("\.tiff", $filename)) {
      $preview = "<img src=\"site_files/preview.gif\" width=21 height=21 border=0 alt=\"Preview Image\" onclick=\"preview('$filedir/$filename');\" style=\"cursor: pointer;\">";
   } else {
      $preview = "<img src=\"site_files/dl.gif\" width=21 height=21 border=0 alt=\"Download File\" onclick=\"download('$filedir/$filename','$filename');\" style=\"cursor: pointer;\">";

      if (eregi("\.swf", $filename)) { $preview = ""; $mouseit = ""; }
   }

   return $preview;
}

# Total file count
$c = 0;

#--------------------------------------------------------------------------------
# SHOW FOLDERS FOR EACH FILE TYPE
#--------------------------------------------------------------------------------
function mkFolder($title, $filearray) {
   global $c;

   uksort($filearray, "strnatcasecmp");

   # For js show/hide folder div
   $toggle_id = str_replace(" ", "_", strtolower($title));

   # File group folder
   echo " <tr>\n";
   echo "  <td class=\"text\">\n";
   echo "   <div width=\"100%\" class=\"col_title\">\n";
   echo "    <table cellpadding=\"0\" cellspacing=\"0\" class=\"text\">\n";
   echo "     <tr>\n";
   echo "      <td><img src=\"../includes/display_elements/graphics/icon-folder-col_title.gif\"></td>\n";
   echo "      <td style=\"padding-left: 10px;\"><a href=\"#\" onClick=\"toggle('".$toggle_id."')\" class=\"darkbg\">".lang($title)."</a></td>\n";
   echo "     </tr>\n";
   echo "    </table>\n";
   echo "   </div>\n";
   echo "   <table id=\"".$toggle_id."\" cellpadding=\"4\" cellspacing=\"0\" width=\"100%\" class=\"text\" style=\"display: none; bacground-color: #FFF;\">\n";
   echo "    <tr>\n";
   echo "     <td class=\"col_sub\" style=\"padding-left: 15px; text-align: center;\">&nbsp;</td>\n";
   echo "     <td width=\"320\" class=\"col_sub\">".lang("File Name")."</td>\n";
   echo "     <td class=\"col_sub\">".lang("Rename")."</td>\n";
   echo "     <td width=\"50\" class=\"col_sub\">".lang("Delete")."</td>\n";
   echo "     <td width=\"165\" class=\"col_sub\">".lang("File Size")."</td>\n";
   echo "     <td width=\"60\" class=\"col_sub\">&nbsp;</td>\n";
   echo "    </tr>\n";

   foreach ( $filearray as $key=>$file ) {
      if ( $trbgclass == "row_bgcolor1" ) { $trbgclass = "row_bgcolor2"; } else { $trbgclass = "row_bgcolor1"; }
      echo "    <tr class=\"$trbgclass\">\n";

      # Preview icon
      echo "     <td style=\"padding-left: 15px; text-align: center; cursor: hand;\">\n";
      echo "      ".prevImg($file['dir'], $file['name'])."\n";
      echo "     </td>\n";

      # File name
      echo "     <td style=\"color: #000099;\">\n";
      echo "      ".$file['name']."<input type=hidden name=\"file".$c."\" value=\"".$file['dir']."/".$file['name']."\">\n";
      echo "     </td>\n";
      
      # If demo site, dont allow rename or file delete
      if($_SESSION['demo_site'] == "yes") $demo_disabled = "disabled=\"true\"";

      # Rename field
      echo "     <td>\n";
      echo "      <input class=\"text\" type=\"text\" name=\"new_name".$c ."\" size=\"22\" ".$demo_disabled.">\n";
      echo "     </td>\n";

      # Delete checkbox
      echo "     <td>\n";
      echo "      <input type=\"checkbox\" id=\"file_delete".$c."\" name=\"file_delete".$c."\" value=\"yes\" onclick=\"verify_delete('".$c."');\" ".$demo_disabled.">\n";
      echo "     </td>\n";

      # File size & too big flag
      echo "     <td width=\"85\" style=\"color: #000099;\">\n";
      echo "      ".sizeInfo($file['size'], "size")."\n";
      echo "     </td>\n";

      echo "     <td width=\"40\">\n";
      echo "      ".sizeInfo($file['size'], "flag")."&nbsp;\n";
      echo "     </td>\n";

      echo "    </tr>\n";

      # Increment file counter
      $c++;
   }
   echo "   </table>\n";
   echo "  </td>\n";
   echo " </tr>\n";

}

# Now compile each folder listing
//sort($imgFile);
//echo testArray($imgFile);

mkFolder(lang("Images"), $imgFile);
mkFolder(lang("Documents, Presentations, and Adobe PDFs"), $docFile);
mkFolder(lang("Video Files"), $vidFile);
mkFolder(lang("Spreadsheets and CSV files"), $csvFile);
mkFolder(lang("Custom web forms and text files"), $formFile);
mkFolder(lang("Custom HTML includes"), $htmlFile);
mkFolder(lang("Custom HTML template files"), $tplFile);
mkFolder(lang("Custom PHP scripts"), $incFile);
mkFolder(lang("Unclassified files"), $wtfFile);

echo "</table>\n";
echo "<input type=hidden name=true_count value=\"".$c."\">\n";
echo "<table width=\"85%\" border=\"0\" cellspacing=\"0\" cellpadding=\"5\" align=\"center\">\n";
echo " <tr> \n";
echo "  <td class=text align=\"right\" valign=\"middle\">\n";

if($_SESSION['demo_site'] == "yes"){
   echo "   <font color=\"#FF0000\"><U>".lang("Files cannot be changed in the demo version")."</U></font>\n";
}else{
   echo "   <font color=\"#FF0000\"><U>".lang("Remember")."</U>: ".lang("Changes and deletions are final and can not be undone.")."</font>\n";
   echo "   <BR>\n";
   echo "   <input type=\"submit\" value=\"".lang("Update File Changes")."\" class=\"btn_save\" onclick=\"saving_updates();\" onMouseover=\"this.className='btn_saveon';\" onMouseout=\"this.className='btn_save';\">\n";
}

echo "  </td>\n";
echo " </tr>\n";
echo "</table>\n";
echo "</form>\n";

?>


<br><br>

</DIV>


<?
# Grab module html into container var
$module_html = ob_get_contents();
ob_end_clean();

//$instructions = "<img src=\"site_files/dl.gif\" width=\"21\" height=\"21\" border=\"0\" alt=\"Download File\" align=\"absmiddle\"> ".lang("Media, document, and code files may be downloaded by clicking on the arrow next to the filename.<br/>");
//$instructions .= "<img src=\"site_files/preview.gif\" width=\"21\" height=\"21\" border=\"0\" alt=\"Preview Image\" align=\"absmiddle\"> ".lang("Image files can be viewed and saved by clicking the preview icon next to the filename.<br/>");
//$instructions .= "<img src=\"site_files/red_flag.gif\" width=\"10\" height=\"10\" border=\"0\" hspace=\"5\" align=\"absmiddle\"> ".lang("Indicates image should be reduced in filesize. This file causes slow load-times when viewing your web site.<br/>");

$instructions = "<img src=\"site_files/dl.gif\" width=\"21\" height=\"21\" border=\"0\" alt=\"Download File\" align=\"absmiddle\">".lang("Media, document, and code files may be downloaded by clicking on the arrow next to the filename").".<br/>";
$instructions .= "<img src=\"site_files/preview.gif\" width=\"21\" height=\"21\" border=\"0\" alt=\"Preview Image\" align=\"absmiddle\"> ".lang("Image files can be viewed and saved by clicking the preview icon next to the filename").".<br/>";
$instructions .= "<img src=\"site_files/red_flag.gif\" width=\"10\" height=\"10\" border=\"0\" hspace=\"5\" align=\"absmiddle\"> ".lang("Indicates image should be reduced in filesize. This file causes slow load-times when viewing your web site").".<br/>";


# Build into standard module template
$module = new smt_module($module_html);
$module->meta_title = lang("File Manager");
$module->add_breadcrumb_link(lang("File Manager"), "program/modules/site_files.php");
$module->icon_img = "skins/".$_SESSION['skin']."/icons/full_size/file_manager-enabled.gif";
$module->heading_text = lang("File Manager");
$module->description_text = $instructions;
$module->good_to_go();
?>