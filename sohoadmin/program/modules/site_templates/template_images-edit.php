<?php
error_reporting(E_PARSE);
if($_GET['_SESSION'] != '' || $_POST['_SESSION'] != '' || $_COOKIE['_SESSION'] != '') { exit; }

#=======================================================================================
# Soholaunch(R) Site Management Tool
#
# Author:        Mike Morrison
# Homepage:      http://www.soholaunch.com
# Release Notes: http://wiki.soholaunch.com
#
# Script notes
# >> Depends on $_REQUEST['templatefolder'], $_REQUEST['edit_image'], $_REQUEST['layoutfile']
#=======================================================================================


error_reporting(E_PARSE);
session_start();

# Include core files
include_once($_SESSION['docroot_path']."/sohoadmin/program/includes/product_gui.php");
include_once($_SESSION['docroot_path']."/sohoadmin/program/includes/smt_module.class.php");

# Template-related functions
include_once($_SESSION['docroot_path']."/sohoadmin/program/modules/site_templates/template_functions.inc.php");


# Save user image change
if ( $_POST['edit_img'] != "" ) {
   # Kill any existing record for this template image
   $qry = "delete from smt_userimages where template_folder = '".$_POST['templatefolder']."'";
   $qry .= " and layout_file = '".$_POST['layoutfile']."'";
   $qry .= " and orig_image = '".$_POST['edit_img']."'";
   $rez = mysql_query($qry);

   # Insert new record (if they actually choose a replacement image)
   if ( $_POST['user_image'] != "" ) {
      $data = array();
      $data['template_folder'] = $_POST['templatefolder'];
      $data['layout_file'] = $_POST['layoutfile'];
      $data['orig_image'] = $_POST['edit_img'];
      $data['user_image'] = $_POST['user_image'];
      $myqry = new mysql_insert("smt_userimages", $data);
      $myqry->insert();
   }

   # Redirect to main Template Images page
//   header("location:template_images.php?templatefolder=".$_POST['templatefolder']."&code=editdone"); exit;
}


# Pull current user setting for this image from db if one exists
$qry = "select user_image from smt_userimages where template_folder = '".$_REQUEST['templatefolder']."'";
$qry .= " and layout_file = '".$_REQUEST['layoutfile']."'";
$qry .= " and orig_image = '".$_REQUEST['edit_img']."'";
$rez = mysql_query($qry);
$saved_userimg = mysql_result($rez, 0);

# So you can write straight HTML without having to build every line into a container var (i.e. $disHTML .= "another line of html")
ob_start();


/*---------------------------------------------------------------------------------------------------------*
 _   _                 ___
| | | | ___ ___  _ _  |_ _| _ __   __ _  __ _  ___  ___
| |_| |(_-</ -_)| '_|  | | | '  \ / _` |/ _` |/ -_)(_-<
 \___/ /__/\___||_|   |___||_|_|_|\__,_|\__, |\___|/__/
                                        |___/

/*---------------------------------------------------------------------------------------------------------*/

# Build site image file options for logo/userimage dropdowns
//$directory = "$doc_root/images";
//$handle = opendir("$directory");
// while ($files = readdir($handle)) {
//    if (strlen($files) > 2) {
//       if ( $files == $df_logo ) {
//          $imagefile_options .= "    <option value=\"$files\" selected>$files</option>\n";
//       } else {
//          $imagefile_options .= "    <option value=\"$files\">$files</option>\n";
//       }
//    }
// }
//closedir($handle);


##################################################################################
### READ IMAGE FILES INTO MEMORY
##################################################################################

//$img_selection = "     <OPTION VALUE=\" \">[".lang("No Image")."]</OPTION>\n";

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
	if (file_exists("$directory/$thisImage[1]")) {
//		$tempArray = getImageSize("$directory/$thisImage[1]");
		$origW = $tempArray[0];
		$origH = $tempArray[1];
		$oW = "";
		$oH = "";

		if ($origH > 300) {
				$oH = "HEIGHT=300 ";
		}

		if ($origW > 275) {
			$oW = "WIDTH=275";
		}

		$WH = "$oW $oH ";
	}

   if ( $thisImage[1] == $df_logo ) {
      $img_selection .= "     <option value=\"".$thisImage[1]."\" selected>".$thisImage[0]."</option>\n";
   } else {
      $img_selection .= "     <option value=\"".$thisImage[1]."\">".$thisImage[0]."</option>\n";
   }

	//$img_selection .= "     <option value=\"".$thisImage[0]."\">".$thisImage[0]."</option>\n";
}

?>
<style>
#involved_templates {
   position: relative;
}
h2 {
   font-size: 13px;
}
h3 {
   font-size: 11px;
   margin: 0;
}
#involved_templates h6 {
   margin: 0;
   font-size: 10px;
}

#working_tpl-container {
   display: none;
   float: right;
   padding: 5px;
   /*border: 1px solid #ccc;*/
}

#working_tpl-container h1 {
   font-size: 16px;
}

#working_tpl-container img {
   float: left;
   width: 100px;
   height: 69px;
}
#working_tpl-caption {
   float: left;
}

#userimg_form {
   clear: both;
}

#choose_other-container {
   position: absolute;
   top: -10px;
   right: -10px;
   width: 330px;
   height: 150px;
   overflow: auto;
   margin-left: 15px;
   padding: 5px 5px 0 0;
   border: 1px solid #ccc;
   border-top: none;
   text-align: right;
}

.choose_other-template {
   float: right;
   width: 90px;
   margin: 3px;
   padding: 2px;
   border: 1px dashed #ccc;
   text-align: center;
   opacity: .5;
   cursor: pointer;
}

.choose_other-template img {
   /*float: left;*/
}
p.choose_other-caption {
   /*float: left;*/
   font-size: 90%;
}

.floatbdrfix {
   margin: 0;
   clear: both;
}

div.userimage-container {
   position: relative;
   padding: 5px 2px;
   margin-bottom: 205px;
   border: 1px dotted #ccc;
}

/* Box that wraps around the <img> tag to allow for overflow: auto; */
div.userimage-img {
   float: left;
   /*width: 700px;*/
   /*overflow: auto;*/
   padding: 5px;
   /*border: 1px dotted #ccc;*/
   text-align: center;
}

p.userimage-info {
/*   position: absolute;
   bottom: 0px;
   right: 0;*/
   margin: 0 0 3px;
}

div.userimage-option_box {
/*   position: absolute;
   right: 5px;
   bottom: 10px;*/
   /*background-color: #336699;*/
   color: #fff;
   /*padding: 15px;*/
   margin: 5px;
}

.img_caption {
   font-size: 90%;
   color: #595959;
}

.imginfo-label {
   color: #7a7a7a;
}
</style>

<?
# help-resized
$popup = "";
$popup .= "<p>Depending on your template, the original image that you are replacing may (or may not) have a specific\n";
$popup .= "width and height defined for it (i.e. hard-coded by developer in template html).</p>\n";
$popup .= "<p>If this is the case and the original template image has a fixed (unchangeable unless you edit the raw template html) width and height\n";
$popup .= "set for it, then those dimensions will be applied to whatever image you choose to replace it (i.e. your image will be squeezed/stretched to fit).</p>\n";
$popup .= "<p>This resized preview shows what your image would look like in the case that it has to be resized to fit the dimensions of the original (default) template image.</p>\n";
$popup .= "<p>Ideally, it's best crop/size/etc your image file so that it matches the exact dimensions of the original template image it will be replacing. \n";
$popup .= "But that's not always neccessary; often times just having generally the same shape/proportions as the original (i.e. square for a square) \n";
$popup .= "is enough to keep it looking clean when squeezed/stretched.</p>\n";
$popup .= "<p>Take a look at the \"Resized preview\" of your image and decide for yourself whether it resizes gracefully as-is, \n";
$popup .= "or whether you might need to pick a different image or modify that one via your favorite image editor (i.e. Adobe Photoshop Elements).</p>\n";
$popup .= "<p>If the template image you're replacing does NOT have a fixed width and height hard-coded in the template html, \n";
$popup .= "then your replacement image will be swapped in at full size.</p>\n";
$extra['onclose'] = "show_dropdowns();";
echo help_popup("help-resized", "Resized preview", $popup, "top: 20%;left: 5%;width: 610px;", $extra);
?>


<div id="involved_templates">

 <div id="working_tpl-container">
  <h1>Working Template</h1>
  <img src="http://<? echo $tpl_base_url; ?>/<? echo $_REQUEST['templatefolder']; ?>/screenshot.jpg">
  <div id="working_tpl-caption">
<?
# Template name
echo "   ".format_templatename($_REQUEST['templatefolder']);
echo "   <p><b>Layout:</b> ".$layout_names[$_REQUEST['layoutfile']]."</p>\n";
?>
  </div>
  <div class="floatbdrfix">&nbsp;</div>
 </div>




<?
# Get available layouts used by this template
$layouts_used = layouts_used($_REQUEST['templatefolder']);
echo "</div>\n"; // End involved_templates


# Begin user img form for this template
echo "<div id=\"userimg_form\">\n";
echo " <form action=\"".$_SERVER['PHP_SELF']."\" method=\"POST\" name=\"userimgform\">\n";
echo " <input type=\"hidden\" name=\"todo\" value=\"save_userimg\">\n";
echo " <input type=\"hidden\" name=\"edit_img\" value=\"".$_REQUEST['edit_img']."\">\n";
echo " <input type=\"hidden\" name=\"templatefolder\" value=\"".$_REQUEST['templatefolder']."\">\n";
echo " <input type=\"hidden\" name=\"layoutfile\" value=\"".$_REQUEST['layoutfile']."\">\n";


/*---------------------------------------------------------------------------------------------------------*
  ___        _        _              _   ___
 / _ \  _ _ (_) __ _ (_) _ _   __ _ | | |_ _| _ __   __ _
| (_) || '_|| |/ _` || || ' \ / _` || |  | | | '  \ / _` |
 \___/ |_|  |_|\__, ||_||_||_|\__,_||_| |___||_|_|_|\__, |
               |___/                                |___/
/*---------------------------------------------------------------------------------------------------------*/
# Save some keystrokes...
$basesrc_orig = "http://".$tpl_base_url."/".$_REQUEST['templatefolder'];
$basepath = $tpl_base_path."/".$_REQUEST['templatefolder'];
$imgfile = $_REQUEST['edit_img'];
$orig_wh = getImageSize($basepath."/".$imgfile);
$imgid = "orig_image";

echo " <h1>Swap-out this original (default) template image...</h1>\n";
echo " <div class=\"userimage-container\">\n";
echo "  <p class=\"userimage-info\">\n";
echo "   <b>Current Setting:</b>\n";
echo "   <span class=\"imginfo-label\">File:</span> ".basename($imgfile)."\n";
echo "   | <span class=\"imginfo-label\">Size:</span> ".human_filesize($basepath."/".$imgfile)."\n";
echo "   | <span class=\"imginfo-label\">Width:</span> ".$orig_wh[0]."px\n";
echo "   | <span class=\"imginfo-label\">Height:</span> ".$orig_wh[1]."px</p>\n";
echo "  <div class=\"userimage-img\">\n";
echo "   <img src=\"".$basesrc_orig."/".$imgfile."\">\n";
echo "   <div class=\"floatbdrfix\"></div>\n";
echo "  </div>";
echo "  <div class=\"userimage-option_box\" id=\"".$optionboxid."\" style=\"display: block;\">\n";
$editlink = $_SERVER['PHP_SELF']."?edit_img=".$imgfile."&amp;templatefolder=".$_REQUEST['templatefolder']."&amp;layoutfile=".$htmlfile;
echo "  </div>\n";
echo "   <div class=\"floatbdrfix\"></div>\n";
echo " </div>";


/*---------------------------------------------------------------------------------------------------------*
 _   _                 ___
| | | | ___ ___  _ _  |_ _| _ __   __ _
| |_| |(_-</ -_)| '_|  | | | '  \ / _` |
 \___/ /__/\___||_|   |___||_|_|_|\__, |
                                  |___/
# User-set image
/*---------------------------------------------------------------------------------------------------------*/
# Save some keystrokes...
$basesrc_user = "http://".$_SESSION['docroot_url']."/images";
$basepath = $_SESSION['docroot_path']."/images";
$imgfile = $saved_userimg;
$user_wh = getImageSize($basepath."/".$imgfile);
echo " <h1>...and use this image instead...</h1>\n";

echo " <div class=\"userimage-container\">\n";
# Info
echo "  <p class=\"userimage-info\">\n";
echo "   <b>Current Setting:</b>\n";
if ( $saved_userimg != "" ) {
   echo "   <span class=\"imginfo-label\">File:</span> ".basename($imgfile)."\n";
   echo "   | <span class=\"imginfo-label\">Size:</span> ".human_filesize($basepath."/".$imgfile)."\n";
   echo "   | <span class=\"imginfo-label\">Width:</span> ".$user_wh[0]."px\n";
   echo "   | <span class=\"imginfo-label\">Height:</span> ".$user_wh[1]."px\n";
} else {
   echo "   [No replacement set - using original (default) template image]";
}
echo "  </p>\n";

?>
<script type="text/javascript">
function userimg_preview() {
   var imgfile = $('user_image').value;

   if ( imgfile != "" ) {
      // Set previews to USER-selected image
      var base_src = '<? echo $basesrc_user; ?>';
      $('userimg-preview-fullsize').src = base_src + '/' + imgfile;
      $('userimg-preview-resized').src = base_src + '/' + imgfile;

      // Show preview images
      showid('userimage_previews');

   } else {
      // Set previews to ORIGINAL template image
      var origimg = '<? echo $basesrc_orig."/".$_REQUEST['edit_img']; ?>';
      $('userimg-preview-fullsize').src = origimg;
      $('userimg-preview-resized').src = origimg;

      // Hide preview images
      hideid('userimage_previews');
   }
}
</script>
<?

# user_image
echo "   <select id=\"user_image\" name=\"user_image\" style=\"width: 300px;font-size: 11px;\" onchange=\"showid('save_btn');showid('cancel_btn');userimg_preview();\" onkeyup=\"userimg_preview();\">\n";
echo "    <option value=\"\" style=\"background-color: #FFFF99;font-size: 12px;font-weight: bold;\">".lang("Use original (default) template image")."</option>\n";
echo "  ".$img_selection."\n";
echo "   </select>\n";

# Reset to default
if ( $saved_userimg != "" ) {
//   echo "   <span class=\"blue hand uline\" onclick=\"\$('user_image').value = '';userimg_preview();\">Reset to default</span>\n";
}

echo "  <div id=\"userimage_previews\" style=\"display: none;\">\n"; // For show/hide

# userimg-preview-fullsize
echo "  <div class=\"userimage-img\">\n";
echo "   <img id=\"userimg-preview-fullsize\" src=\"http://".$_SESSION['docroot_url']."/images/".$imgfile."\"><br/>\n";
echo "   <span class=\"img_caption\">Full-size</span>\n";
echo "  </div>";

# userimg-preview-resized
echo "  <div class=\"userimage-img\" style=\"width: ".($orig_wh[0] + 10)."px;\">\n";
echo "   <img id=\"userimg-preview-resized\" src=\"http://".$_SESSION['docroot_url']."/images/".$imgfile."\" ".$orig_wh[3]."><br/>\n";
echo "   <span class=\"img_caption\">Resized preview.</span>\n";
echo "   <span class=\"help_link\" onclick=\"hide_dropdowns();showid('help-resized');\">[?]</span>\n";
echo "  </div>";
echo "   <div class=\"floatbdrfix\"></div>\n";
echo " </div>"; // End container

echo " </div>\n"; // End userimage_previews

# [<< back] [cancel] [save]
echo "  <input id=\"back_btn\" type=\"button\" value=\"&lt;&lt; Back\" ".$_SESSION['btn_edit']." onclick=\"document.location.href='template_images.php?templatefolder=".$_REQUEST['templatefolder']."';\" style=\"display: block;float: left;margin-right: 20px;\">\n";
echo "  <input id=\"cancel_btn\" type=\"button\" value=\"[x] Reset/Cancel\" ".$_SESSION['btn_edit']." onclick=\"\$('user_image').value = '".$saved_userimg."';userimg_preview();hideid('save_btn');\" style=\"display: none;float: left;margin-right: 20px;\">\n";
echo "  <input id=\"save_btn\" type=\"button\" value=\"Save Settings\" ".$_SESSION['btn_save']." onclick=\"document.userimgform.submit();\" style=\"display: none;float: left;\">\n";
echo " </form>\n";
?>
</div>

<script type="text/javascript">
// Re-select stored settings
//alert('[<? echo $saved_userimg; ?>]');
$('user_image').value = '<? echo $saved_userimg; ?>';
userimg_preview();
</script>



<?
# Grab module html into container var
$module_html = ob_get_contents();
ob_end_clean();

$module = new smt_module($module_html);
$module->meta_title = "Edit Template Images";
$module->add_breadcrumb_link("Template Manager", "program/modules/site_templates.php");
$module->add_breadcrumb_link("Template Images", "program/modules/site_templates/template_images.php?templatefolder=".$_REQUEST['templatefolder']);
$module->add_breadcrumb_link("Edit Image <span class=\"unbold\">(".basename($_REQUEST['edit_img']).")</span>", "program/modules/site_templates/template_images-edit.php?templatefolder=".$_REQUEST['templatefolder']."&amp;edit_img=".$_REQUEST['edit_img']."&amp;layoutfile=".$_REQUEST['layoutfile']);
$module->icon_img = "skins/".$_SESSION['skin']."/icons/template_manager-enabled.gif";
$module->heading_text = "Template Images";
$module->description_text = "Certain images within one or more of your templates can be swapped-out for other images of your choosing.";
$module->good_to_go();
?>