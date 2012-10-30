<?php
error_reporting(E_PARSE);
if($_GET['_SESSION'] != '' || $_POST['_SESSION'] != '' || $_COOKIE['_SESSION'] != '') { exit; }

# Load time to beat: .672s
###############################################################################
## Soholaunch(R) Site Management Tool
## Version 4.6
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
error_reporting(E_PARSE);
include("../includes/product_gui.php");

# Clear recent page list
if ( $_GET['todo'] == "clear_recent" ) {
   $_SESSION['recent_pages'] = null;
}

# Delete page action
if ( $_POST['todo'] == "delete_page" && count($_POST['killthis_page']) > 0 ) {
   $delete_report = "";
   $delete_report .= "<div id=\"dead_pages\" class=\"hand bg_gray_df\" style=\"width: 650px;height: 50px;overflow: auto;\">\n";
   foreach ( $_POST['killthis_page'] as $key=>$pagename ) {

      $this_del_page = eregi_replace("_", " ", $pagename);
      mysql_query("DELETE FROM site_pages WHERE page_name = '$this_del_page'");
      $directory = $_SESSION['docroot_path']."/sohoadmin/tmp_content";
      $old_file = eregi_replace(" ", "_", $pagename);
      if($old_file != 'index'){
				unlink($_SESSION['doc_root'].'/'.$old_file.'.php');
			}
      $handle = opendir($directory);
      while ($files = readdir($handle)) {
      	if ( eregi($old_file, $files) ) {
      		if ($old_file.".con" == $files || $old_file.".regen" == $files) {
      			$this_file = $directory . "/".$files;
      			@unlink($this_file);
      		}
      	}
      }
      closedir($handle);

      $qry = "select page_name from site_pages where page_name = '".$this_del_page."'";
      $trez = mysql_query($qry);
      if ( mysql_num_rows($trez) < 1 ) {
         $delete_report .= $this_del_page." <span class=\"red\">- DELETED!</span><br/>";
      }
   }

   $delete_report .= "</div>\n";
   $delete_report .= "<div id=\"hide-dead_pages\" class=\"hand right white\" onclick=\"hideid('dead_pages');hideid('hide-dead_pages');\" style=\"width: 650px;background-color: red;\"><b>Click to close this report</b></div>\n";
}

#######################################################
### Process Single Page Template Update 		       ###
#######################################################
if ($_POST['process'] == "updatetemplates") {
	$docroot = $_SESSION['doc_root'];
	$doc_root = $_SESSION['doc_root'];
	$filename = $_SESSION['docroot_path']."/template/template.conf"; // Mantis 413
	$file = fopen("$filename", "r");
		$what_template = fread($file,filesize($filename));
	fclose($file);

	$page_templates = "";

	foreach ( $_POST as $name=>$value ) {  // Mantis 413
		$value = stripslashes($value);
		if ($name != "process") {
			if ($value == "default") { $value = ""; }
			$page_templates .= $name."=".$value."\n";
		}
	}

	$filename = "$doc_root/media/page_templates.txt";
	$file = fopen("$filename", "w");
	fwrite($file, "$page_templates");
	fclose($file);
	chmod($filename, 0755);
} // End single page template update


// Read Current Individual Page Templates into memory
// ----------------------------------------------------------
$filename = "$doc_root/media/page_templates.txt";
if (file_exists($filename)) {
	$file = fopen("$filename", "r");
		$template_vars = fread($file,filesize($filename));
	fclose($file);

	$tmp = split("\n", $template_vars);
	$tmp_cnt = count($tmp);

	for ($x=0;$x<=$tmp_cnt;$x++) {
		if ($tmp[$x] != "") {
			$this_var = split("=", $tmp[$x]);
			${$this_var[0]} = $this_var[1];
		} // End If
	} // End For
} // End If File

# CHECK FOR CUSTOM TEMPLATE EXISTANCE
$directory = "$doc_root/tCustom";
if (is_dir($directory)) {
$handle = opendir("$directory");
	while ($files = readdir($handle)) {
		if (strlen($files) > 2) {
			if (eregi(".html", $files) || eregi(".htm", $files)) {
				$templatecount++;
				$templateFile[$templatecount] = "[CUSTOM]~~~".$files;
			}
		}
	}

$numTemplates = $templatecount;
$newsTemplates = $newscount;
closedir($handle);

}

if ($templatecount > 1) {
	sort($templateFile);
	$numTemplates--;
}

//get site base template
$baseT = "../../../template/template.conf";

$file = fopen("$baseT", "r");
	$what_template = fread($file,filesize($baseT));
fclose($file);
$base_template = $what_template;	// In case of individual page definitions
$my_base_template = eregi_replace($doc_root."/", "", $base_template);
$my_base_template = eregi_replace("tCustom/", "", $my_base_template);


#######################################################
### START HTML/JAVASCRIPT CODE
#######################################################

# Start buffering output
ob_start();
?>


<style type="text/css">
/* Styles specific to the open pages menu */

/* BUTTON: What can I do from here? */
.wheredoistart  {
   border: 0px solid red;
   padding: 3px;
   padding-top: 1px;
   padding-left: 19px;
   text-align: left;
   font-size: 10px;
   font-weight: bold;
   color: #306fae;
   background-image: url('../../skins/<? echo $_SESSION['skin']; ?>/icons/help_icon-gray.gif');
   background-position: 0px 0;
   background-repeat: no-repeat;
   cursor: pointer;
}
/*.ltable {border-color: menutext;border-style: solid;border-left-style: solid;border-left-width: 3px;border-top-style: solid;border-top-width: 3px;border-right-style: solid;border-right-width: 3px;border-bottom-style: solid;border-bottom-width: 3px}*/

.recentpage-off {
   color: #336699;
   background-color: #dfecf6;
   font-weight: bold;
   cursor: pointer;
   border: 1px dashed #ccc;
}
.recentpage-on {
   color: #336699;
   background-color: #dfecf6;
   font-weight: bold;
   cursor: pointer;
   border: 1px solid #ffc417;
}
#recent_pages td {
   /*border: 1px dashed #ccc;*/
}
#recent_pages th {
   font-weight: normal;
}
</style>

<script type="text/javascript">
parent.header.flip_header_nav('EDIT_PAGES_LAYER');

 	function killErrors() {
		return true;
	}
	window.onerror = killErrors;
<?
echo "function delete_this_page(c) {\n";
echo "   var tiny = window.confirm('".lang("Are you sure you wish to delete this page")."');\n";
echo "	if (tiny != false) {\n";
echo "	   loadIt();\n";
echo "	   window.location = \"page_editor/delete_page.php?currentPage=\"+c;\n";
echo "   } else {\n";
echo "		// OK With client\n";
echo "	}\n";
echo "}\n";
?>

	function MM_openBrWindow(theURL,winName,features) { //v2.0
	  window.open(theURL,winName,features);
	}

	function SV2_findObj(n, d) { //v3.0
	  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
	    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
	  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
	  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=SV2_findObj(n,d.layers[i].document); return x;
	}

	function SV2_popupMsg(msg) { //v1.0
	  alert(msg);
	}
	function SV2_openBrWindow(theURL,winName,features) { //v2.0
	  window.open(theURL,winName,features);
	}

//	show_hide_layer('PAGE_EDITOR_LAYER?header','','hide');
//	show_hide_layer('NEWSLETTER_LAYER?header','','hide');
//	show_hide_layer('MAIN_MENU_LAYER?header','','show');
//	show_hide_layer('CART_MENU_LAYER?header','','hide');
//	show_hide_layer('DATABASE_LAYER?header','','hide');
//	show_hide_layer('WEBMASTER_MENU_LAYER?header','','hide');
	parent.frames.footer.CURPAGENAME.innerHTML = "Loading...";

	function loadIt(v) {
		show_hide_layer('loadingLayer','','show');
		show_hide_layer('userOpsLayer','','hide');
		var p = 'Editing Page : '+v;
		parent.frames.footer.setPage(p);
	}

	function load_screen() {
	   show_hide_layer('loadingLayer','','hide');
		show_hide_layer('userOpsLayer','','show');
      var v = "<? echo lang("Edit Pages"); ?>";
		parent.frames.footer.setPage(v);
	}

	function edit_page(v) {
	   loadIt(v);
	   //alert(v);
<?php
	echo "	   var nocache = '".microtime()."';\n";
?>
	   if(document.all){
	      window.location = 'page_editor/page_editor.php?currentPage='+v+'&nocache='+nocache;
	   }else{
	      window.location = 'page_editor/page_editor-ff.php?currentPage='+v+'&nocache='+nocache;
	   }
	}

   function closeit() {
      window.browsetemplates.close();
   }

	function show_template(v) {
		update_templates();
	}

	function update_templates() {
		loadIt();
		ctemplate.submit();
	}

	function set_default_page() {
		loadIt();
		document.main_page_select.submit();
	}

	function setDrop(change,templ){
	   //alert(change);
	   //alert(templ);

	   document.getElementById(change).value=templ;
	   //change.value=templ;
      //disOne = oSel.selectedIndex;
	   //tImage = eval("oSel.options["+disOne+"].value");
	}

	function browse_templates(v) {
		eval("MM_openBrWindow('site_templates/pgm-browse_templates.php?change="+v+"&cMode=win','browsetemplates','width=650,height=400,scrollbars=yes,resizable=yes');");
	}

   function toggle(targetid) {
     var isnow = document.getElementById(targetid).style.display;
     if ( isnow == 'block' ) {
        document.getElementById(targetid).style.display='none';
        return true;
     } else {
        document.getElementById(targetid).style.display='block';
        return true;
     }
   }


</SCRIPT>



<!-- ============================================================ -->
<!-- ============================================================ -->



<?
# popup-delete_pages
$popup = "";
$popup .= "  <form name=\"killpage_form\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n";
$popup .= "  <input type=\"hidden\" name=\"todo\" value=\"delete_page\">\n";
$popup .= "  <p><b class=\"red\">".lang("WARNING").":</b> ".lang("This action is permanent and cannot be un-done").".\n";
$popup .= "  ".lang("It can also be used to clean out all the pages you don't want just to get them out of your way").".\n";
$popup .= "  <p>".lang("You may select multiple pages").".</p>\n";
$popup .= "  <p><b>".lang("Choose page(s) to delete").":</b></p>\n";
$popup .= "  <select name=\"killthis_page[]\" style=\"font-family: Tahoma; font-size: 8pt; visibility: visible;height: 140px;\" multiple>\n";
$result = mysql_query("SELECT page_name FROM site_pages ORDER BY page_name");
while ( $row = mysql_fetch_array($result) ) {
   $popup .= "  <option value=\"".$row['page_name']."\">".$row['page_name']."</option>\n";
}
$popup .= "  </select>\n";
$popup .= "  <input type=\"button\" ".$btn_delete." value=\"".lang("Delete selected page(s)")."\" onclick=\"document.killpage_form.submit();\">\n";
$popup .= "  </form>\n";
echo help_popup("popup-delete_pages", "".lang("Advanced: Quick page delete")."", $popup, "left: 25px;");
echo $delete_report;


# popup-page_templates
# Page template assignments
$popup = "";
$popup .= "<p>".lang("This column shows you which of your site templates is assigned to each individiual page").".\n";
$popup .= "<p>".lang("In most cases, every page will have your Site Base Template assigned to it, so the information is kind of inconsequential,")." \n";
$popup .= "".lang("but it can be quite helpful if you're using different templates for different pages instead of one template for every page.")."</p>";
if ( $CUR_USER_ACCESS == "WEBMASTER" ) {
   $instructions = str_replace("#LINK#", "\"site_templates.php\"", lang("Note: You may assign a single Site Base Template that applies to your entire website via the <a href=#LINK#>Template Manager</a> feature."));
   $instructions .= lang("To change the template for a specific page, edit the page, select page properties, and select the template from the drop down box.");
} else {
   $instructions = lang("Note: You may assign a single Site Base Template that applies to your entire website via the Template Manager feature.");
   $instructions .= " ".lang("To change the template for a specific page, edit the page, select page properties, and select the template from the drop down box.");
}
$popup .= "<p>".$instructions."</p>";
echo help_popup("popup-page_templates", "".lang("Assigned template")."", $popup, "left: 25px;");
?>


<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="white">

 <!---HELP: What can I do here?--->
 <tr>
  <td width="80%" class="text gray_33" style="padding: 15px 0px 10px 15px;">

<?
if ( $CUR_USER_ACCESS == "WEBMASTER" || eregi(";MOD_CREATE_PAGES;", $CUR_USER_ACCESS) ) {
?>
	<table border="0" cellpadding="0" cellspacing="0" class="text">
	<tr><td bgcolor="#f8f9fd">
   <div class="wheredoistart" onClick="toggle('main_help');">
    <? echo lang("Where do I start"); ?>?&nbsp;
   </div>
  </td><td>
		&nbsp;&nbsp;Default "home" page:
  	<?php
		if($_POST['default_homepage'] != ''){
			$the_new_startpage = $_POST['default_homepage'];			
			mysql_query("update site_specs set startpage='".$the_new_startpage."'");
		}
		$main_page_select = "  <form name=\"main_page_select\" method=post action=\"open_page.php\" style=\"display:inline;\">\n\n";
		$main_page_select .= "<select id=\"default_page_select\" name=\"default_homepage\" onchange=\"set_default_page();\" style=\"background: white none repeat scroll 0%; width: 200px; font-family: Arial; font-size: 8pt; -moz-background-clip: -moz-initial; -moz-background-origin: -moz-initial; -moz-background-inline-policy: -moz-initial;\">\n\n";
		
		$borez = mysql_query("SELECT startpage FROM site_specs");
		while($bobo = mysql_fetch_array($borez)){
			$true_start_page = $bobo['startpage'];
		}

		$start_page_q = mysql_query("SELECT page_name,link FROM site_pages order by page_name ASC");
		while($start_page_q_result = mysql_fetch_array($start_page_q)){
			$pagez = $start_page_q_result['page_name'];
			$linkz = $start_page_q_result['link'];
			if(eregi('^((mailto)|(http://)|(https://))', $linkz)){
			} else {
				if($pagez == $true_start_page){
					$defaultselected = ' SELECTED';
				} else {
					$defaultselected = '';
				}
				$main_page_select .= "<option value=\"".$pagez."\"".$defaultselected.">".$pagez."</option>\n";
			}
		}

		$main_page_select .= "</select>\n\n";
		$main_page_select .= "</form>\n\n";
		
		echo $main_page_select;
		?>
		<div class="wheredoistart" onClick="toggle('default_page');" style="display:inline;">
		&nbsp;
		</div>
  </td></tr></table>
<?
} // End if logged-in user has permission to create pages
?>
   <div id="default_page" style="display: none;">
    <table border="0" cellpadding="0" cellspacing="0" bgcolor="#f8f9fd" class="text">
			<tr class="gray_33"><td>
				<?php echo lang("This page will be the first page that pulls up when a visitor goes to http://").$_SESSION['this_ip'].". ".lang("Also known as: start page, index page, default page")."."; ?>
			</td></tr>
    </table>
	</div>


   <div id="main_help" style="display: none;">
    <table border="0" cellpadding="0" cellspacing="0" bgcolor="#f8f9fd" class="text">
     <tr class="gray_33">
      <td colspan="2" width="28%" style="padding-left: 3px; font-size: 12px; font-weight: bold; border: 1px solid #999999; border-style: solid none none solid;"><? echo lang("Edit Pages"); ?></td>
      <td colspan="2" width="35%" style="font-size: 12px; font-weight: bold; border: 1px solid #999999; border-style: solid none none none;"><? echo lang("Create Pages"); ?></td>
      <td colspan="2" style="font-size: 12px; font-weight: bold; border: 1px solid #999999; border-style: solid solid none none;"><? echo lang("Delete Pages"); ?></td>
     </tr>
     <tr valign="top">
      <td height="50" style="padding-left: 3px; border: 1px solid #999999; border-style: none none solid solid;"><img src="../../skins/default/icons/edit_pages-enabled.gif" width="31" height="31"></td>
      <td height="50" style="border: 1px solid #999999; border-style: none none solid none; padding-left:2px;"><? echo lang("Click the Edit button next to any page to begin editing that page"); ?>.</td>
      <td height="50" style="border: 1px solid #999999; border-style: none none solid none;"><img src="../../skins/default/icons/create_pages-enabled.gif" width="34" height="35"></td>
      <td height="50" style="border: 1px solid #999999; border-style: none none solid none; padding-left:2px;"><? echo lang("Need to create another page? Click the 'Create New Page(s)' button at the bottom of the screen"); ?>.</td>
      <td height="50" style="border: 1px solid #999999; border-style: none none solid none;"><img src="../../skins/default/icons/edit_pages-enabled.gif" width="31" height="31"></td>
      <td height="50" style="border: 1px solid #999999; border-style: none solid solid none; padding-left:2px; padding-right:2px;"><? echo lang("Click the Delete button next to any page to delete that page"); ?>.</td>
     </tr>
    </table>
	</div>

  </td>

  <!---create pages button-->
  <td align="right" style="padding: 10px;">
<?
if ($CUR_USER_ACCESS == "WEBMASTER" || eregi(";MOD_CREATE_PAGES;", $CUR_USER_ACCESS)) {
   echo "<input type=\"button\" class=\"btn_build\" value=\"".lang("Create New Pages")." &gt;&gt;\" onclick=\"document.location.href='create_pages.php';\" onMouseover=\"this.className='btn_buildon';\" onMouseout=\"this.className='btn_build';\">\n";
} else {
   echo "&nbsp;\n";
}

?>
  </td>

 </tr>

 <!--- Contains main pages table -->
 <tr>
  <td colspan="2" align="center" valign="top">
<?

//$THIS_DISPLAY .= "  <div style=\"border: 1px solid red;\">".$CUR_USER_ACCESS."</div>\n";

$THIS_DISPLAY .= "  <form name=ctemplate method=post action=\"open_page.php\">\n\n";
$THIS_DISPLAY .= "  <input type=hidden name=process value=updatetemplates>\n\n";
###############################################################
### START OPEN PAGE VISUAL FOR MENU PAGES
###############################################################

$THIS_DISPLAY .= "  <table cellpadding=\"5\" cellspacing=\"0\" border=\"0\" width=\"100%\">\n";

$THIS_DISPLAY .= "   <tr>\n";
$THIS_DISPLAY .= "    <td align=\"center\">";

# Recent pages at top
if ( count($_SESSION['recent_pages']) > 0 ) {
   ksort($_SESSION['recent_pages']);
   $THIS_DISPLAY .= "     <div style=\"padding-left: 5px; text-align: left;\">\n";
   $THIS_DISPLAY .= "      <table cellspacing=\"4\" cellpadding=\"5\" id=\"recent_pages\">\n";
   $THIS_DISPLAY .= "       <tr>\n";
   $THIS_DISPLAY .= "        <th colspan=\"".count($_SESSION['recent_pages'])."\" align=\"left\" style=\"padding: 0;\">\n";
   $THIS_DISPLAY .= "         <b>".lang("Recently Created/Modified").":</b>\n";
   $THIS_DISPLAY .= "         (<a href=\"open_page.php?todo=clear_recent\" class=\"del\">clear</a>)\n";
   $THIS_DISPLAY .= "        </th>\n";
   $THIS_DISPLAY .= "       </tr>\n";

   $THIS_DISPLAY .= "       <tr>\n";
   $idcounter = 1;
   foreach ( $_SESSION['recent_pages'] as $pagename=>$time ) {
      $pagename_spaces = eregi_replace("_", " ", $pagename);
      $idname = "recentpage-".$idcounter;
      $mouseover = "onmouseover=\"setClass('".$idname."', 'recentpage-on');\"";
      $mouseover .= " onmouseout=\"setClass('".$idname."', 'recentpage-off');\"";
      $THIS_DISPLAY .= "         <td id=\"recentpage-".$idcounter."\" class=\"recentpage-off\" onclick=\"edit_page('".$pagename_spaces."');\" ".$mouseover.">\n";
      $THIS_DISPLAY .= "          ".$pagename_spaces."";
      $THIS_DISPLAY .= "         </td>\n";
      $idcounter++;
   }
   $recent_page_links = substr($recent_page_links, 0, -3);
   $THIS_DISPLAY .= $recent_page_links;
   $THIS_DISPLAY .= "       </tr>\n";
   $THIS_DISPLAY .= "      </table>\n";
   $THIS_DISPLAY .= "     </div>\n";
} // end if recent_pages > 0


# Main page list table
$THIS_DISPLAY .= "     <table cellpadding=\"4\" cellspacing=\"1\" border=\"0\" style=\"font-family: Tahoma; font-size: 8pt;\" width=\"98%\" bgcolor=\"#000000\">\n";
$THIS_DISPLAY .= "      <tr>\n";
$THIS_DISPLAY .= "       <td align=\"center\" valign=\"middle\" class=\"col_title\">".lang("Edit Content")."</td>";
$THIS_DISPLAY .= "       <td align=\"left\" valign=\"top\" class=\"col_title\"><font COLOR=WHITE><b>".lang("Page Name")."</b> [".lang("Parent Page")."]</td>";
$THIS_DISPLAY .= "       <td align=\"center\" valign=\"middle\" class=\"col_title\"><font COLOR=WHITE><b>".lang("Menu status")."</b></td>";
$THIS_DISPLAY .= "       <td align=\"center\" valign=\"middle\" class=\"col_title\">\n";
$THIS_DISPLAY .= "        <b>".lang("Assigned template")."</b> <span class=\"unbold help_link\" onclick=\"toggleid('popup-page_templates');\">[?]</span>\n";
$THIS_DISPLAY .= "       </td>";
//$THIS_DISPLAY .= "       <td align=\"center\" valign=\"middle\" bgcolor=#336699><b>Rename Page</b></td>";
$THIS_DISPLAY .= "       <td align=\"center\" valign=\"middle\" class=\"col_title\"><font COLOR=WHITE><b>".lang("Delete Page")."</b></td>";


# Loop through site pages and spit out table rows
//$result = mysql_query("SELECT * FROM site_pages WHERE type != 'menu' ORDER BY page_name"); // Do not pull external link pages
$result = mysql_query("SELECT * FROM site_pages ORDER BY page_name");
$tNumPages = mysql_num_rows($result);
$start_bg = "#EFEFEF";

while ( $row = mysql_fetch_array($result) ) {

   # What template is assigned to this page?
   $Ptemplate = $row['template'];
   $Ptemplate = eregi_replace("tCustom/","<b>".lang("CUSTOM TEMPLATE").":</b> ",$Ptemplate);
   if ( strlen($Ptemplate) < 2 ) {
      $Ptemplate = "<b>(".lang("Site Base Template").")</b> ".$my_base_template;
   }

   # Is the currently-logged-in admin user authorized to edit this page?
   $TMP_CHK = eregi_replace(" ", "_", $row['page_name']);

   if ($CUR_USER_ACCESS == "WEBMASTER" || strpos($CUR_USER_ACCESS, ";".$TMP_CHK.";") !== false || eregi(";MOD_ALLPAGES;", $CUR_USER_ACCESS) ) { // Admin is authorized, build page row now

      if ( $bgclass == "bg_white" ) { $bgclass = "bg_gray_ef"; } else { $bgclass = "bg_white"; }

      # Show [Parent page] if this is a sub-page on the menu
      $this_sub = "";
      if ($row[sub_page_of] != "") {
         $dis = split("~~~", $row['sub_page_of']);
         $this_sub = "<font color=#999999>[ $dis[0] ]</font>";
      }

      $this_page = eregi_replace(" ", "_", $row['page_name']); // Page_name
      $dPageName = $row['page_name']; // Page Name

      # Off Menu/On Menu?
      $status = "<b><font color=maroon>".lang("Off Menu")."</font></b>";
      if ( $row['main_menu'] > 0 || $this_sub != "" ) { $status = "<font color=darkgreen>".lang("On Menu")."</font>"; }


      # Begin building table row
      #---------------------------------------------------
		if ( $row["type"] != "menu" ) {
	      # Highlight home/default page and recently created/modified pages
	      if ( $this_page == startpage() ) {
	         $THIS_DISPLAY .= "      <tr class=\"bg_green_df\">\n";
	      } elseif ( array_key_exists($dPageName, $_SESSION['recent_pages']) ) {
	         $THIS_DISPLAY .= "      <tr class=\"bg_blue_df italic\">\n";
	      } else {
	         $THIS_DISPLAY .= "      <tr class=\"".$bgclass."\">\n";
	      }
	
	//      # EDIT
	//      if ( $row["type"] != "menu" ) {
	         # Normal menu page
	         $THIS_DISPLAY .= "       <td align=\"center\" valign=\"middle\">\n";
	         $THIS_DISPLAY .= "        <input type=\"button\" ".$btn_edit." value=\" ".lang("Edit")." \" onMouseover=\"this.className='btn_editon';\" onMouseout=\"this.className='btn_edit';\" onClick=\"edit_page('$this_page');\" style='width: 50px;'>\n";
	         $THIS_DISPLAY .= "       </td>\n";
	//      } else {
	//         # External link page (can't edit)
	//         $THIS_DISPLAY .= "       <td align=\"center\" valign=\"middle\">N/A</td>\n";
	//      }
	
	      # Page Name [Parent Page]
	      $THIS_DISPLAY .= "       <td align=\"left\" valign=\"middle\" class=\"text\"><b>".$dPageName."</b> ".$this_sub."</td>\n";
	
	      # Menu Status
	      $THIS_DISPLAY .= "       <td align=\"center\" valign=\"middle\">".$status."</td>\n";
	
	      # Assigned Template
	      if ( $row["type"] != "menu" ) {
	         # Normal page
	         $THIS_DISPLAY .= "       <td align=\"left\" valign=\"middle\" class=\"gray_33\">".$Ptemplate."</td>\n";
	      } else {
	         # External link
	         $THIS_DISPLAY .= "       <td align=\"left\" valign=\"middle\" class=\"gray_33\">External Link (".$row["link"].")</td>\n";
	      }
	
	      # DELETE
	      $THIS_DISPLAY .= "       <td align=\"center\" valign=\"middle\">\n";
	      if ( $this_page == startpage() ) {
	         $THIS_DISPLAY .= "        N/A\n";
	      } else {
	         $THIS_DISPLAY .= "        <input type=\"button\" ".$btn_delete." value=\" ".lang("Delete")." \" style='width: 50px;' onclick=\"delete_this_page('$this_page');\">\n";
	      }
	      $THIS_DISPLAY .= "       </td>\n";
	      $THIS_DISPLAY .= "      </tr>\n";
		}
   } // End Multi-User Access Check

} // End While Loop


###############################################################################################
### Allow definition of News Article Page Template if "Latest News" entry exists
###############################################################################################

$testArt = mysql_query("SELECT PRIKEY FROM BLOG_CONTENT WHERE BLOG_SUBJECT = '$getSpec[news_cat]'");
$test_articles = mysql_num_rows($testArt);

if ( $test_articles > 0 && ($CUR_USER_ACCESS == "WEBMASTER" || eregi(";MOD_TEMPLATES;", $CUR_USER_ACCESS)) ) {

      if ($start_bg == "#EFEFEF") { $start_bg = "white"; } else { $start_bg = "#EFEFEF"; }
      $this_sub = "";

      $status = "<font color=maroon>".lang("Articles").": ".$test_articles."</font>";

      $THIS_DISPLAY .= "\n<tr>\n";
      $THIS_DISPLAY .= "<td align=\"center\" valign=\"middle\" bgcolor=$start_bg><font color=#999999>N/A</font></td>\n";
      $THIS_DISPLAY .= "<td align=\"center\" valign=\"middle\" bgcolor=$start_bg>$status</td><td align=\"left\" valign=\"middle\" class=\"text\" bgcolor=$start_bg><b>".lang("Latest News")."</b></td>\n";
      $THIS_DISPLAY .= "<td align=\"center\" valign=\"middle\" bgcolor=$start_bg>";

         $THIS_DISPLAY .= '<select id="Latest_News" name="Latest_News" onchange="update_templates();" style="width: 250px; font-family: Arial; font-size: 8pt; background: '.$start_bg.'">';
         $USEDIR = "site_templates/";
            $this_page = "Latest_News";
            $this_template = ${$this_page};  // Have current template selected in case of other page change :)
         ob_start();
            include("site_templates/pgm-read_templates.php");
            $THIS_DISPLAY .= ob_get_contents();
         ob_end_clean();
         $THIS_DISPLAY .= '</select>';

      $THIS_DISPLAY .= "<font FACE=ARIAL SIZE=1>&nbsp;<a href=\"#\" onclick=\"browse_templates('Latest_News');\">[".lang("Browse")."]</a></font>";
      $THIS_DISPLAY .= "</td>\n";
            // $THIS_DISPLAY .= "<td align=\"center\" valign=\"middle\" bgcolor=$start_bg><input type=\"button\" class=FormLt1 value=\" Rename \" style='width: 50px;'></td>\n";
      $THIS_DISPLAY .= "<td align=\"center\" valign=\"middle\" bgcolor=$start_bg><font color=#999999>N/A</font></td>\n";
      $THIS_DISPLAY .= "</tr>\n";

} else {

      $THIS_DISPLAY .= "<input type=\"hidden\" name=\"Latest_News\" value=\"\">"; // Behind the scenes (default template)

}


###############################################################################################
### Allow definition of Shopping Cart Template if product exist
###############################################################################################

$test = mysql_query("SELECT PRIKEY FROM cart_products");
$test_products = mysql_num_rows($test);

if ( $test_products > 0  && ($CUR_USER_ACCESS == "WEBMASTER" || eregi(";MOD_TEMPLATES;", $CUR_USER_ACCESS)) ) {

      if ($start_bg == "#EFEFEF") { $start_bg = "white"; } else { $start_bg = "#EFEFEF"; }
      $this_sub = "";

      $status = "<font color=maroon>".lang("Number Skus").": ".$test_products."</font>";

      $THIS_DISPLAY .= "\n<tr>\n";
      $THIS_DISPLAY .= "<td align=\"center\" valign=\"middle\" bgcolor=$start_bg><font color=#999999>N/A</font></td>\n";
      $THIS_DISPLAY .= "<td align=\"center\" valign=\"middle\" bgcolor=$start_bg>$status</td><td align=\"left\" valign=\"middle\" class=\"text\" bgcolor=$start_bg><b>".lang("Shopping Cart")."</b></td>\n";
      $THIS_DISPLAY .= "<td align=\"center\" valign=\"middle\" bgcolor=$start_bg>";

         $THIS_DISPLAY .= '<select id="Shopping_Cart" name="Shopping_Cart" onchange="update_templates();" style="width: 250px; font-family: Arial; font-size: 8pt; background: '.$start_bg.'">';
         $USEDIR = "site_templates/";
            $this_page = "Shopping_Cart";
            $this_template = ${$this_page};  // Have current template selected in case of other page change :)
         ob_start();
            include("site_templates/pgm-read_templates.php");
            $THIS_DISPLAY .= ob_get_contents();
         ob_end_clean();
         $THIS_DISPLAY .= '</select>';

      $THIS_DISPLAY .= "<font FACE=ARIAL SIZE=1>&nbsp;<a href=\"#\" onclick=\"browse_templates('Shopping_Cart');\">[".lang("Browse")."]</a></font>";
      $THIS_DISPLAY .= "</td>\n";
            // $THIS_DISPLAY .= "<td align=\"center\" valign=\"middle\" bgcolor=$start_bg><input type=\"button\" class=FormLt1 value=\" Rename \" style='width: 50px;'></td>\n";
      $THIS_DISPLAY .= "<td align=\"center\" valign=\"middle\" bgcolor=$start_bg><font color=#999999>N/A</font></td>\n";
      $THIS_DISPLAY .= "</tr>\n";

} else {

      $THIS_DISPLAY .= "<input type=\"hidden\" name=\"Shopping_Cart\" value=\"\">";  // Behind the scenes (default template)

}

###############################################################################################
###############################################################################################

$THIS_DISPLAY .= "</table>\n\n";

$THIS_DISPLAY .= "\n</td></tr>\n";
$THIS_DISPLAY .= "</table>\n";

########################################################################
########################################################################

echo $THIS_DISPLAY;

########################################################################
########################################################################

	$display_file = "../useropts.txt";
	$file = fopen("$display_file", "r");
		$display_template = fread($file,filesize($display_file));
	fclose($file);
	$OPF_SELECTION = eregi_replace("##TITLE##", lang("Edit Pages")." <SPAN ID=WHAT>: ".lang("Main Menu Pages")."</SPAN>", $display_template);
	$OPF_SELECTION = eregi_replace("#MESSAGE#", "$THIS_DISPLAY", $OPF_SELECTION);
	$OPF_SELECTION = eregi_replace("<IMG SRC=\"spacer.gif\" HEIGHT=5 width=650 border=\"0\"><BR>", "", $OPF_SELECTION);
	//echo $OPF_SELECTION;
	if ($cnew == 1) { $btnValue = lang("Create More Pages")." &gt;&gt;"; } else { $btnValue = lang("Create New Pages")." &gt;&gt;"; }
?>
</td>
</tr>
</table>
</form>


<table width="100%" border="0" cellpadding=0 cellspacing="0">
 <tr>
<?
# Show quick delete option for WEBMASTER
if ( $_SESSION['CUR_USER_ACCESS'] == "WEBMASTER" ) {
   echo "<td align=\"left\" style=\"padding-left: 20px;\">\n";
   echo " <span class=\"red uline hand font90\" onclick=\"showid('popup-delete_pages');\">".lang("Advanced: Force delete pages")."&hellip;</span>\n";
   echo "</td>\n";
}
?>

  <td align="right" valign="top" style="padding-right: 15px;">
<?
// Added for V4 Multi-User Access Security
// ------------------------------------------------------------------------------------
if ($CUR_USER_ACCESS == "WEBMASTER" || eregi(";MOD_CREATE_PAGES;", $CUR_USER_ACCESS)) {
         echo '<form method=post action="create_pages.php">
            <input type=submit class="btn_build" value="'.$btnValue.'" onMouseover="this.className=\'btn_buildon\';" onMouseout="this.className=\'btn_build\';">
         </form>';
}
?>
  </td>
 </tr>
</table>

<SCRIPT Language="javascript">
	<? echo ("	var newStatus = \"\";\n"); ?>
	parent.frames.footer.CURPAGENAME.innerHTML = newStatus;
	<?
	if ($wiz == 1) {
		echo " alert('".lang("Congratulations! Your website setup is complete").".\\n\\n".lang("You can now VIEW your new site by clicking the View Website")."\\n".lang("button on the top of your screen or begin editing the")."\\n".lang("page content now").".');\n";
	}
	?>
</SCRIPT>
<?
	if ($cnew == 1) {
		echo ("<SCRIPT LANGUAGE=Javascript>\n");
		echo ("	alert('".lang("Your new pages have been created!")."');\n");

		# Error pages
		if ( $_GET['problems'] != "" ) {
		   echo "alert('".lang("There were problems creating the following pages")."...\\n\\n".base64_decode($_GET['problems'])."');\n";
		}

		echo ("</SCRIPT>\n");
	}

$instructions = lang("All site pages are listed here.  Click edit next to a page to begin editing!");

# Grab module html into container var
$module_html = ob_get_contents();
ob_end_clean();

$module = new smt_module($module_html);
$module->meta_title = "Open/Edit Pages";
$module->add_breadcrumb_link(lang("Open/Edit Pages"), "program/modules/open_page.php");
$module->icon_img = "skins/".$_SESSION['skin']."/icons/edit_pages-enabled.gif";
$module->heading_text = lang("Open/Edit Pages");
$module->description_text = $instructions;
$module->good_to_go();
?>