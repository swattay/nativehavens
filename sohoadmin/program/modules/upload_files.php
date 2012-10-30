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

# Make sure images and media are writeable
testWrite("images", true);
testWrite("media", true);
?>



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

function update_status(text) {
	// STATUS UPDATE
}

function start_upload() {
	show_hide_layer('Layer2','','hide','Layer1','','hide','Layer3','','show');
}

var p = "<? echo lang("Upload Files"); ?>";
parent.frames.footer.setPage(p);

parent.header.flip_header_nav('MAIN_MENU_LAYER');

</script>



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



		<?

		if ($update == 1) {
			echo"<BR CLEAR=ALL><BR>".lang("File update completed.");
		}
		if ($success != "") {
			echo"<BR CLEAR=ALL><BR>".lang("Upload of files completed.");
		}

		?>

<form enctype="multipart/form-data" action="upload/upload_action.php" method="POST" name="FormUpload">

<table border="0" cellspacing="0" cellpadding="4" align="center" class="allBorder" >


<?

for ($x=0;$x<=9;$x++) {
	$number = $x + 1;
	if ($class == "class=controls") { $class = "class=text"; } else { $class = "class=text"; }

		$tmp = lang("Filename")." #";
		$tmp = eregi_replace("#", "$number", $tmp);	// Make sure number display's correctly for international language support

	echo " <tr><td align=\"right\" valign=\"middle\" style=\"color: #FF6633;\" $class>$tmp:</td><td align=left valign=middle $class><input class=\"FormLt2\" type=\"file\" size=\"50\" name=\"FILE$x\"></td></tr>\n";

}

echo "</table><BR clear=all>\n";
echo "<div align=center>\n";
echo "<input class=\"btn_save\" style='width: 150px;' type=submit value=\"".lang("Upload Files")."\" name=\"submit\"  onclick=\"start_upload();\" onMouseover=\"this.className='btn_saveon';\" onMouseout=\"this.className='btn_save';\">\n";
echo "</div>\n";

?>

</form>
</td></tr></table>

<?
# Grab module html into container var
$module_html = ob_get_contents();
ob_end_clean();


//// Added For V5 Multi-User Access Rights
//// -------------------------------------------------------------------------------
//		if ($CUR_USER_ACCESS == "WEBMASTER") {
//			echo "<a href=\"site_files.php?=SID\"><B>".lang("File Manager")."</B></a>";
//		}

$instructions = lang("Select the Browse button next to each filename to locate your local file for upload. When you are ready to start the upload operation, select Upload Files.")."<br/>";
$instructions .= "<b>".lang("Please Note").":</b> ".lang("Files should be optimized for web display.")." ".lang("Files over 1 MB may have difficulty uploading.")." ".lang("This depends on your server settings.");

# Build into standard module template
$module = new smt_module($module_html);
$module->meta_title = lang("Upload Files");
$module->add_breadcrumb_link(lang("Upload Files"), "program/modules/upload_files.php");
$module->icon_img = "skins/".$_SESSION['skin']."/icons/full_size/file_manager-enabled.gif";
$module->heading_text = lang("Upload Files");
$module->description_text = $instructions;
$module->good_to_go();
?>