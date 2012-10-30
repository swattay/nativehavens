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

# Check for blog tables
include_once("../blog-dbtable_check.inc.php");

# Start buffering output
ob_start();
   
###############################################################################
## SAVE INDIVIDUAL BLOG POST
###############################################################################
if(!function_exists('db_string_format')){
	function db_string_format($string) {
   	if ( !get_magic_quotes_gpc() ) {
      	return mysql_real_escape_string($string);
   	} else {
      	return $string;
   	}
	}
}

if ( $_POST['do'] == "save_entry" ) {

   $newHTML = ${"hiddenbox".$_POST['cVal']};
	$newHTML = eregi_replace("<SOHOtextarea", "<textarea", $newHTML);
	$newHTML = eregi_replace("</SOHOtextarea", "</textarea", $newHTML);
	
	$newHTML = db_string_format($newHTML);
   $newTitle = ${"entry_title".$_POST['cVal']};
   $newTitle = addslashes($newTitle);
   $newStuff = "BLOG_DATA = '".$newHTML."', BLOG_TITLE = '".$newTitle."'";
   $blogQry = "UPDATE BLOG_CONTENT SET $newStuff WHERE PRIKEY = '".$_POST['entry_id']."'";
   if ( !mysql_query($blogQry) ) {
      echo "Unable to update blog entry because:<br>";
      echo mysql_error();
      exit;
   }

   # Return to 'View Subject' entry list
   $ACTION = "uSUBJ";
   $VIEW = "View";
   $del_subj = $_POST['display_subj'];


}

###############################################################################
## DELETE INDIVIDUAL BLOG POST
###############################################################################
if ($ACTION == "dREMOVE") {
	mysql_query("DELETE FROM BLOG_CONTENT WHERE PRIKEY = '$id'");
	$ACTION = "uSUBJ";
	$VIEW = lang("View");
	$del_subj = $subj;
}

###############################################################################
## ADD/DELETE SUBJECT ACTION
###############################################################################
$update_flag = 0;
$del_err = 0;
if ($ACTION == "uSUBJ") {
	if (isset($ADD)) {
		$new_blog_subj = stripslashes($new_blog_subj);
		$new_blog_subj = addslashes($new_blog_subj);
		if (strlen($new_blog_subj) > 2) {
			mysql_query("INSERT INTO BLOG_CATEGORY VALUES('NULL', '$new_blog_subj')");
			$update_flag = 1;
		}
	}
	if (isset($DEL) && $del_subj != "NULL") {
		$tmp = mysql_query("SELECT BLOG_SUBJECT FROM BLOG_CONTENT WHERE BLOG_SUBJECT = '$del_subj'");
		$tmp_cnt = mysql_num_rows($tmp);
		if ($tmp_cnt < 1) {
			mysql_query("DELETE FROM BLOG_CATEGORY WHERE PRIKEY = '$del_subj'");
			$update_flag = 1;
		} else {
			$del_err = 1;
		}
	}

	if (isset($VIEW) && $del_subj != "NULL") {

   ?>
         <style type="text/css">
         <!--
         .unnamed1 {
         	padding-top: 30px;
         }
         .unnamed2 {
         	padding-top: 15px;
         	font-size: 8pt;
         	font-family: Arial;
         }
         -->
         </style>
         <script language="javascript" type="text/javascript" src="tiny_mce/tiny_mce.js"></script>
         <script type="text/javascript" src="tiny_mce/plugins/media/jscripts/embed.js"></script>
			<script type="text/javascript">

         function createCookie(name,value,days)
         {
         	if (days)
         	{
         		var date = new Date();
         		date.setTime(date.getTime()+(days*24*60*60*1000));
         		var expires = "; expires="+date.toGMTString();
         	}
         	else var expires = "";
         	document.cookie = name+"="+value+expires+"; path=/";
         }

         function readCookie(name)
         {
         	var nameEQ = name + "=";
         	var ca = document.cookie.split(";");
         	for(var i=0;i < ca.length;i++)
         	{
         		var c = ca[i];
         		while (c.charAt(0)==" ") c = c.substring(1,c.length);
         		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
         	}
         	return null;
         }

         function disBlog(mode){
            var frmNm = AfrmNm;
            var textId = AtextId;
            var boxId = AboxId;
            var savebtn = Asavebtn;

            if(document.getElementById("remember").checked){
               createCookie("editorMode",mode,90);
               document.getElementById("chooseMode").style.display="none";
               alert("Setting saved!  To reset this option, go to webmaster and click Clear Editor Mode");
            }
            document.getElementById("chooseMode").style.display="none";
            document.getElementById("remember").checked=false;

            eval ("var result = MM_openBrWindow('loadEditor_Blog.php?mod=blog&type="+mode+"&savebtn="+savebtn+"&blogForm="+frmNm+"&curtext="+textId+"&blogBox="+boxId+"&dotcom=<? echo $dis_site; ?>&=SID','blogEdit','width=790, height=550, resizable=1');");

         }

			function SetBlog() {
			   
            var blogCont = tinyMCE.getContent();
            //alert(blogCont)
         	is_txtarea = blogCont.search("<textarea");
         	if(is_txtarea>0){
         		var textArr = blogCont.split("<textarea")
         		var textLen = textArr.length
         		for(var x=0; x<textLen; x++){
         			blogCont = blogCont.replace("<textarea","<SOHOtextarea");
         			blogCont = blogCont.replace("</textarea","</SOHOtextarea");
         		}
         	}
            var textImages = blogCont.split("src=\"images/")
            var textImagesLen = textImages.length
            for(var x=0; x<textImagesLen; x++){
               blogCont = blogCont.replace("src=\"images/", "src=\"http://"+dot_com+"/images/");
            }
			   
//			   alert(current_editing_area)
//			   alert(current_saving_area)
//			   alert(current_saving_button)

            img = tinyMCE.getParam("theme_href") + '/images/spacer.gif';
            NewFinal = blogCont.replace(/<script[^>]*>\s*write(Flash|ShockWave|WindowsMedia|QuickTime|RealMedia)\(\{([^\)]*)\}\);\s*<\/script>/gi, '<img class="mceItem$1" title="$2" src="'+img+'" />');
			   
			   //alert(blogCont)
			   //alert(NewFinal)
			   
			   document.getElementById(current_saving_area).innerHTML= NewFinal;
			   document.getElementById(current_editing_area).value= blogCont;
            document.getElementById(current_saving_button).style.display= "block";
            toggleEditor("tiny_editor");
			}

			function getHtml(thisBox) {
			   var boxHtml = document.getElementById(thisBox).value;
			   //alert(boxHtml)
				is_txtarea = boxHtml.search("<SOHOtextarea");
				if(is_txtarea>0){
					var textArr = boxHtml.split("<SOHOtextarea")
					var textLen = textArr.length
					for(var x=0; x<textLen; x++){
						boxHtml = boxHtml.replace("<SOHOtextarea","<textarea");
						boxHtml = boxHtml.replace("</SOHOtextarea","</textarea");
					}
				}
			return boxHtml; }

         function loadBlog(frmNm,textId,boxId,savebtn){
            current_editing_area = boxId;
            current_saving_area = textId;
            current_saving_button = savebtn;
            toggleEditor("tiny_editor");
            
         }

         function textEdit(frmNm,textId,boxId) {
            //alert("something");
            eval ("var result = MM_openBrWindow('page_editor/text_editor_45.php?blogForm="+frmNm+"&curtext="+textId+"&blogBox="+boxId+"&dotcom=<? echo $dis_site; ?>&=SID','textEditorWin','width=750,height=450');");
         }
			function save_blog(formNm,divId,boxId) {
				window.location = "blog.php?ACTION=dSave&subj="+subj+"&id="+key+"&='.SID.'";
			}
			function del_blog(key,subj) {
				window.location = "blog.php?ACTION=dREMOVE&subj="+subj+"&id="+key+"&='.SID.'";
			}


	     
         //################################################
         //       _____ _          __  __  ___ ___ 
         //      |_   _(_)_ _ _  _|  \/  |/ __| __|
         //        | | | | ' \ || | |\/| | (__| _| 
         //        |_| |_|_||_\_, |_|  |_|\___|___|
         //                   |__/                 Stuff
         //################################################
         
         //Define global variables
         var dot_com = '<? echo $_SESSION["docroot_url"]; ?>'
         
         var current_editing_area = '';
         var current_saving_area = '';
         var current_saving_button = '';

<?php
	$origdir = getcwd();
	chdir('page_editor');
	include('tiny_init.php');
	chdir($origdir);
?>

         // Gets content from editor and places it in editor
         // Called by setupcontent_callback within tinyMCE.init
         function pullHTML(editor_id, body, doc){
            //alert(current_editing_area)
            var html = getHtml(current_editing_area);
            //alert(html);
            
            var inst = tinyMCE.getInstanceById(tinyMCE.selectedInstance.editorId);
            var newHtml = TinyMCE_MediaPlugin.cleanup('insert_to_editor',html,inst);
            
            //alert(newHtml);
            
            body.innerHTML = newHtml;
         }
         
         // Hide / show / load / unload editor within spcified id (div or textarea)
         function toggleEditor(id) {
         	var elm = document.getElementById(id);
         
         	if (tinyMCE.getInstanceById(id) == null){
         		tinyMCE.execCommand('mceAddControl', false, id);
         		$('entry_container').style.display='none';
         	   $('tiny_editor_container').style.display='block';
         	   setTimeout("tinyMCE.execInstanceCommand(tinyMCE.selectedInstance.editorId,'mceToggleVisualAid',false);tinyMCE.execInstanceCommand(tinyMCE.selectedInstance.editorId,'mceToggleVisualAid',false);",1000);
         	}else{
         		tinyMCE.execCommand('mceRemoveControl', false, id);
         		$('entry_container').style.display='block';
         	   $('tiny_editor_container').style.display='none';
         	}
         }
	     
			</script>

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
         if(eregi("Firefox/3", $_SERVER['HTTP_USER_AGENT'])){
            $editorHeight = "435px";
         }else{
            $editorHeight = "475px";
         }
      }
      ?>
      
      <div id="tiny_editor_container" style="position: absolute; height: <? echo $editorHeight; ?>; top: 0px; left: 0px; bottom: 0px; right: 0px; _right: -25px; border: 0px solid green; z-index:1000; display: none;">
         <!--- Editor Textarea -->
				<?php
				echo "<textarea id=\"tiny_editor\" name=\"tiny_editor\" rows=\"15\" cols=\"80\" style=\"height: ".$editorHeight."; width: 100%; border: 1px dotted red;\">editor content</textarea>";
				?>
         <!--- Cancel / Done buttons -->
         <!---<div id="saveIt" style="position:absolute; bottom: 1px; right: 15px; z-index:1000; display:block;">-->
			<div id="saveIt" style="position:absolute; bottom: 1px; right: 15px; z-index:1002; display:block;">


      <input onClick="tinyMCE.execInstanceCommand('tiny_editor','mceCodeEditor',false);" type="button" id="html_view" value=" HTML View  " class="btn_edit" style="font-weight:bold; background-color: #F0F0EE;color: #88c8e;border: 1px solid #888c8e;width: 120px;padding: 0px;margin-right: 10em;" onmouseover="this.style.backgroundColor='#000';this.style.color='#fff';" onmouseout="this.style.backgroundColor='#F0F0EE';this.style.color='#000';">
      
      <!--- Cancel -->
      <input onClick="toggleEditor('tiny_editor');" type="button" id="cancel_edit" value=" [x] Cancel  " style="width: 100px; padding: 0px;" <? echo $_SESSION['btn_delete']; ?> >
   	
      <!--- Done -->
      <input onClick="SetBlog();" type="button" id="save_content" value="  Update  " style="font-weight: bold;width: 150px;padding: 0px;" <? echo $_SESSION['btn_save']; ?> >


            
         </div>
      </div>
      
      <div id="entry_container">
<?
		//echo "<BR><CENTER><font face=Verdana size=2><B>[ <a href=\"blog.php?=SID\">Back</A> ]</b></font></CENTER>\n";
		$result = mysql_query("SELECT * FROM BLOG_CATEGORY WHERE PRIKEY = '$del_subj'");
		$tmp = mysql_fetch_array($result);
		$this_subj_name = $tmp[CATEGORY_NAME];
		echo "<BR><TABLE ALIGN=CENTER CELLPADDING=\"5\" CELLSPACING=\"0\" BORDER=\"0\" WIDTH=\"725\">\n";
		echo "   <TR>\n";
		echo "      <TD VALIGN=\"absmiddle\" class=\"feature_module\" style=\"background: #D9E6EF; \" WIDTH=100%><B><IMG SRC=\"arrow.gif\" WIDTH=\"17\" HEIGHT=\"13\" ALIGN=\"absmiddle\">".$this_subj_name."</B></TD>\n";
		echo "      <TD align=\"right\" VALIGN=\"absmiddle\" class=\"feature_module\" style=\"background: #D9E6EF; \" WIDTH=100%><b><a href=\"blog.php?=SID\">Back</A></b></TD>\n";
		echo "   </TR>\n";
		echo "</TABLE>\n";
		
      echo "<br>\n\n";

		$c = 1; // Counter var
		$result = mysql_query("SELECT * FROM BLOG_CONTENT WHERE BLOG_SUBJECT = '$del_subj' ORDER BY BLOG_DATE DESC, PRIKEY ASC");
		while ($row = mysql_fetch_array($result)) {
			$blog_title = stripslashes($row['BLOG_TITLE']);
			//echo "(".$row['BLOG_DATA'].")<br/>";
			$blog_data = stripslashes($row['BLOG_DATA']);
			$blog_data_hid = eregi_replace("<textarea", "<SOHOtextarea", $blog_data);
			$blog_data_hid = eregi_replace("</textarea", "</SOHOtextarea", $blog_data_hid);
			$blog_data_hid = eregi_replace("<TEXTAREA", "<SOHOtextarea", $blog_data_hid);
			$blog_data_hid = eregi_replace("</TEXTAREA", "</SOHOtextarea", $blog_data_hid);
			//echo "(".$blog_data.")<br/>";

		   echo "<form name=\"blog_entry".$c."\" action=\"blog.php\" method=\"post\">\n";
		   echo "<input type=\"hidden\" name=\"do\" value=\"save_entry\">\n";
		   echo "<input type=\"hidden\" name=\"cVal\" value=\"".$c."\">\n"; // So we know which 'hiddenbox' to check
		   echo "<input type=\"hidden\" name=\"entry_id\" value=\"".$row['PRIKEY']."\">\n"; // Which db row to update
		   echo "<input type=\"hidden\" name=\"display_subj\" value=\"".$del_subj."\">\n"; // Which subject to display after save
			echo "<table border=0 cellpadding=4 cellspacing=1 width=725 align=center style='border: 1px solid #CCCCCC;'>\n";
			echo " <tr>\n";

			# [Delete Entry]
			echo "  <td align=\"right\" valign=top bgcolor=#EFEFEF class=text>\n";
			echo "   <input type=button value=\"".lang("Delete Entry")."\" onclick=\"del_blog('$row[PRIKEY]','$del_subj');\" ".$btn_delete.">&nbsp;";
			echo "  </td>\n";

			# Blog title
		   echo "  <td width=\"100%\" align=\"left\" valign=\"top\" bgcolor=\"#EFEFEF\" class=\"text\">\n";
			echo "   ".$row['BLOG_DATE'].":\n";
			echo "   <input type=\"text\" name=\"entry_title".$c."\" value=\"".$blog_title."\" class=\"tfield\" style=\"width: 400px;\" onfocus=\"document.blog_entry".$c.".save_btn".$c.".style.display = 'block'\">\n";
			echo "  </td>\n";

		   # [Edit Entry]
		   echo "  <td align=\"center\" valign=top bgcolor=#EFEFEF class=text>\n";
		   echo "   <input type=\"button\" name=\"edit_btn\" value=\"".lang("Edit Entry")."\" onclick=\"loadBlog('blog_entry".$c."','blogentry".$c."','hiddenbox".$c."','save_btn".$c."');\" ".$btn_edit.">&nbsp;";
		   echo "  </td>\n";

			echo " </tr>\n";

			# Blog Data
			echo " <tr>\n";
			echo "  <td colspan=\"3\" width=\"725\" align=left valign=top class=text bgcolor=white>\n";
			echo "   <div class=TXTCLASS id=\"blogentry".$c."\" align=\"left\">\n";
			//echo "    <b>wordz</b> go here.\n";
			echo "   ".$blog_data."\n";
			echo "   </div>\n";
			echo "  </td>\n";
			echo " </tr>\n";

			# Hidden textarea
			echo " <tr>\n";
			echo "  <td colspan=\"2\" align=left valign=top class=text bgcolor=white>\n";
         echo "   <TEXTAREA id=\"hiddenbox".$c."\" name=\"hiddenbox".$c."\" STYLE=\"display: none;\">".$blog_data_hid."</TEXTAREA>";
			echo "  </td>\n";

		   # [Save Entry]
		   echo "  <td align=\"right\" valign=\"top\" class=\"text\">\n";
		   echo "   <input type=\"submit\" id=\"save_btn".$c."\" name=\"save_btn".$c."\" value=\"".lang("Save Entry")."\" ".$btn_save." style=\"display: none;\">&nbsp;";
		   echo "  </td>\n";
			echo " </tr>\n";

			echo "</table>\n";
			echo "</form>\n";

			echo "\n\n<BR><BR>\n\n";

			$c++;
		}
		
		echo "</div>\n";
		
      # Grab module html into container var
      $module_html = ob_get_contents();
      ob_end_clean();
      
      $instructions = lang("Create blog subjects, add/edit blog content, and assign blog content.");
      //$instructions .= lang("Please only use alpha-numerical characters and spaces.");
      
      # Build into standard module template
      $module = new smt_module($module_html);
      $module->meta_title = "Blog Manager";
      $module->add_breadcrumb_link("Blog Manager", "program/modules/blog.php");
      $module->icon_img = "skins/".$_SESSION['skin']."/icons/full_size/blog_manager-enabled.gif";
      $module->heading_text = "Blog Manager";
      $module->description_text = $instructions;
      $module->good_to_go();
		exit;
	} // End if View

} // End Update Blog

###############################################################################
## Post New Blog Data
###############################################################################

if ($ACTION == "pBLOG") {
	$today = date("Y-m-d");
	$title = stripslashes($blog_title);
	$title = addslashes($title);

	$message = stripslashes($message);
	
//	if(eregi("editor/pinEdit.php", $message)){
//	   $daParts = split("#", $message);
//	   $arrLen = count($daParts);
//	   echo "(".$daParts[$arrLen].")<br/>";
//	   $arrMinOne = $arrLen-1;
//	   echo "(".$daParts[$arrMinOne].")<br/>";
//	   
//
//	   
//	   
//	   foreach($daParts as $var => $val){
//	      echo "var(".$var.") = (".$val.")<br/>";
//	   }
//	}
//	exit;
	
	$message = addslashes($message);
	
	########################################################################################################
	// Format blog content to remove Page Editor styles.
	###=====================================================================================================
   //$message = eregi_replace("<SPAN id=SOHOTEXTSTART>", "", $message);
   //$message = eregi_replace("</SPAN>", "", $message);

   $message = eregi_replace("SOHOLINK=", "href=", $message);

   // ----------------------------------------------------------------------------------
   // Replace Crap Inserted Automatically by Text Editor when hardcoding links and such
   // ----------------------------------------------------------------------------------
   $tmp = "href=\"http://$this_ip/sohoadmin/program/modules/page_editor/";
   $message = eregi_replace($tmp,"href=\"", $message);

   $nTmpFind = eregi("href=\"text_editor_obj_45.php\?curtext=(.*)&=SID#", $message, $out);
   $thisNewObj = $out[1];
   //$message = eregi_replace("href=\"text_editor_obj_45.php\?curtext=$thisNewObj&=SID#","href=\"#", $message);
   // ----------------------------------------------------------------------------------

   // Only target a blank browser window if link is off-site (2003-03-04)
   // ----------------------------------------------------------------------------------------
   if (eregi(" href=\"http:", $message) && !eregi(" href=\"http://$this_ip", $message)) {
      $message = eregi_replace(" href=\"http:", " target=\"_blank\" href=\"http:", $message);
   }
	$message = eregi_replace("<SOHOtextarea", "<textarea", $message);
	$message = eregi_replace("</SOHOtextarea", "</textarea", $message);

	/// Insert new entry into blog_content table!
	###vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv
	mysql_query("INSERT INTO BLOG_CONTENT VALUES('NULL','$pSUBJ','$title','$message','$today')");
	$update_flag = 1;
}


if ($ACTION == "pNEWSPROMO") {
	$new_promocat = $_POST['pPROMO'];
	//echo "(".$new_promocat.")";

   $blogQry = "UPDATE PROMO_BOXES SET CONTENT = '$new_promocat' WHERE BOX = 'promobox'";
   if ( !mysql_query($blogQry) ) {
      echo "Unable to update PROMO_BOXES :<br>";
      echo mysql_error();
      exit;
   }

	$new_newscat = $_POST['pNEWS'];
	//echo "(".$new_newscat.")";

   $blogQry = "UPDATE PROMO_BOXES SET CONTENT = '$new_newscat' WHERE BOX = 'newsbox'";
   if ( !mysql_query($blogQry) ) {
      echo "Unable to update PROMO_BOXES :<br>";
      echo mysql_error();
      exit;
   }
   $news_promo_flag = 1;
}






//$result = mysql_query("SELECT * FROM BLOG_CATEGORY");
//
//while ($row=mysql_fetch_array($result)) {
//	if($row['CATEGORY_NAME'] == $news_arr) { $news_sel = "selected"; } else { $news_sel = ""; }
//	if($row['CATEGORY_NAME'] == $promo_arr) { $promo_sel = "selected"; } else { $promo_sel = ""; }
//
//	$NEWS_SUBJECTS .= "<OPTION VALUE=\"".$row['PRIKEY']."\"".$news_sel.">".$row['CATEGORY_NAME']."</OPTION>\n";
//
//	$PROMO_SUBJECTS .= "<OPTION VALUE=\"".$row['PRIKEY']."\"".$promo_sel.">".$row['CATEGORY_NAME']."</OPTION>\n";
//}
//echo "<select id=\"pNEWS\" name=\"pNEWS\" class=text style='width: 250px;'>\n";
//echo "	<option value=\"NULL\"></option>\n";
//echo $NEWS_SUBJECTS;
//echo "</select>\n";
//
//echo "<select id=\"pNEWS\" name=\"pNEWS\" class=text style='width: 250px;'>\n";
//echo "	<option value=\"NULL\"></option>\n";
//echo $PROMO_SUBJECTS;
//echo "</select>\n";
//
//exit;

###############################################################################
## Read Blog Subjects into memory
###############################################################################
$promo_arr = "";
$news_arr = "";
$result1 = mysql_query("SELECT BOX, CONTENT FROM PROMO_BOXES WHERE prikey > 25");
while($CUR_NEWS_PROMO = mysql_fetch_array($result1)){
	if($CUR_NEWS_PROMO['BOX'] == "newsbox"){
		$news_arr = $CUR_NEWS_PROMO['CONTENT'];
	}
	if($CUR_NEWS_PROMO['BOX'] == "promobox"){
		$promo_arr = $CUR_NEWS_PROMO['CONTENT'];
	}
}

$NEWS_SUBJECTS = "";
$PROMO_SUBJECTS = "";
$SUBJECTS = "";
$result = mysql_query("SELECT * FROM BLOG_CATEGORY");

while ($row=mysql_fetch_array($result)) {

   # Select current subject when editing
   if ( $subj == $row['PRIKEY'] ) { $selSubj = " selected"; } else { $selSubj = ""; }
	if($row['PRIKEY'] == $news_arr) { $news_sel = "selected"; } else { $news_sel = ""; }
	if($row['PRIKEY'] == $promo_arr) { $promo_sel = "selected"; } else { $promo_sel = ""; }

   # Build drop-down options
	$SUBJECTS .= "<OPTION VALUE=\"".$row['PRIKEY']."\"".$selSubj.">".$row['CATEGORY_NAME']."</OPTION>\n";
	$NEWS_SUBJECTS .= "<OPTION VALUE=\"".$row['PRIKEY']."\"".$news_sel.">".$row['CATEGORY_NAME']."</OPTION>\n";
	$PROMO_SUBJECTS .= "<OPTION VALUE=\"".$row['PRIKEY']."\"".$promo_sel.">".$row['CATEGORY_NAME']."</OPTION>\n";
}

if ( $ACTION == "dEdit" ) {
   $blogID = $id;
   $rez = mysql_query("SELECT * FROM BLOG_CONTENT WHERE PRIKEY = '$blogID'");
   $getBlog = mysql_fetch_array($rez);
   //echo "subj: $subj"; exit;
   //echo $showBlog; exit;
}

?>


<style type="text/css">
<!--
.unnamed1 {
	padding-top: 30px;
}
.unnamed2 {
	padding-top: 15px;
	font-size: 8pt;
	font-family: Arial;
}
-->
</style>
<script language="javascript" type="text/javascript" src="tiny_mce/tiny_mce.js"></script>
<script type="text/javascript">
parent.header.flip_header_nav("MAIN_MENU_LAYER");

<!--

function postNcheck() {
   var finished = 0;
   var Btitle = document.SOHOEDITOR.blog_title.value;
   if (Btitle == "") {
      alert('Please enter a title for this blog.');
      finished = 1;
   }
   disOne = document.SOHOEDITOR.pSUBJ.selectedIndex;
	Bsubj = eval("document.SOHOEDITOR.pSUBJ.options["+disOne+"].value");
	if (Bsubj == "NULL") {
	   alert('Please select a subject to post this blog to.');
	   finished = 1;
	}
   Bcont = document.SOHOEDITOR.message.value;
   if (Bcont == "") {
      alert('Please click Launch Editor to enter content for this blog');
      finished = 1;
   }
   if (finished == 0) {
      document.SOHOEDITOR.submit();
   }

}

function getHtml() {
   var boxHtml = document.SOHOEDITOR.message.value;
return boxHtml; }

function SetBlog() {
   var blogCont = tinyMCE.getContent();
   //alert(blogCont)
	is_txtarea = blogCont.search("<textarea");
	if(is_txtarea>0){
		var textArr = blogCont.split("<textarea")
		var textLen = textArr.length
		for(var x=0; x<textLen; x++){
			blogCont = blogCont.replace("<textarea","<SOHOtextarea");
			blogCont = blogCont.replace("</textarea","</SOHOtextarea");
		}
	}
	//alert('ok1')
   var textImages = blogCont.split('src="images/')
   var textImagesLen = textImages.length
   for(var x=0; x<textImagesLen; x++){
      blogCont = blogCont.replace('src="images/', 'src="http://'+dot_com+'/images/');
   }
	//alert('ok2')
	
	//alert(blogCont)
	
   //img = tinyMCE.getParam("theme_href") + '/images/spacer.gif';
   //NewFinal = blogCont.replace(/<script[^>]*>\s*write(Flash|ShockWave|WindowsMedia|QuickTime|RealMedia)\(\{([^\)]*)\}\);\s*<\/script>/gi, '<img class="mceItem$1" title="$2" src="'+img+'" />');
   
   //alert(NewFinal)
	
   document.SOHOEDITOR.message.value = blogCont;
   document.getElementById('statusEdit').style.display= "block";
   document.getElementById('launch').value= " Edit Blog ";
   if(document.getElementById('status').style.display=="block" && document.getElementById('statusEdit').style.display=="block" && document.getElementById('statusPost').style.display=="block"){
      document.getElementById('statusGo').style.display= "block";
   }else{
      document.getElementById('statusGo').style.display= "none";
   }
   toggleEditor('tiny_editor');
}

function dispCat() {
   disOne = document.SOHOEDITOR.pSUBJ.selectedIndex;
	SelectedCat = eval("document.SOHOEDITOR.pSUBJ.options["+disOne+"].value");
   if(SelectedCat!="NULL") {
      document.getElementById('statusPost').style.display= "block";
   }else{
      document.getElementById('statusPost').style.display= "none";
   }
   if(document.getElementById('status').style.display=="block" && document.getElementById('statusEdit').style.display=="block" && document.getElementById('statusPost').style.display=="block"){
      document.getElementById('statusGo').style.display= "block";
   }else{
      document.getElementById('statusGo').style.display= "none";
   }
}

function dispStatus(){
   if(document.getElementById('blog_title').value==''){
      document.getElementById('status').style.display= "none";
   }else{
      document.getElementById('status').style.display= "block";
   }
   if(document.getElementById('status').style.display=="block" && document.getElementById('statusEdit').style.display=="block" && document.getElementById('statusPost').style.display=="block"){
      document.getElementById('statusGo').style.display= "block";
   }else{
      document.getElementById('statusGo').style.display= "none";
   }
}

function createCookie(name,value,days)
{
	if (days)
	{
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
	}
	else var expires = "";
	document.cookie = name+"="+value+expires+"; path=/";
}

function readCookie(name)
{
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for(var i=0;i < ca.length;i++)
	{
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1,c.length);
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
	}
	return null;
}

function disBlog(mode){
   if(document.getElementById('remember').checked){
      createCookie('editorMode',mode,90);
      alert('Setting saved!  To reset this option, go to webmaster and click Clear Editor Mode');
   }
   document.getElementById('chooseMode').style.display='none';
   document.getElementById('remember').checked=false;

   eval ("var result = MM_openBrWindow('loadEditor_Blog.php?mod=blog&type='+mode+'','blogEdit','width=790, height=550, resizable=1');");
}

function loadBlog(){
   var cook = readCookie('editorMode');
   if(cook){
      eval ("var result = MM_openBrWindow('loadEditor_Blog.php?mod=blog&type='+cook+'','blogEdit','width=790, height=550, resizable=1');");
   }else{
      document.getElementById('chooseMode').style.display='block';
   }
   //eval ("var result = MM_openBrWindow('loadEditor_Blog.php?mod=blog','blogEdit','width=830, height=550');");
}

function buttonOn() {
   //alert('something');
document.getElementById('chooseMode').style.filters.alpha.opacity=''; //IE4 syntax
document.getElementById('chooseMode').style.MozOpacity='';           //NS6 syntax
}


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

SV2_showHideLayers('NEWSLETTER_LAYER?header','','hide');
SV2_showHideLayers('MAIN_MENU_LAYER?header','','show');
SV2_showHideLayers('CART_MENU_LAYER?header','','hide');
SV2_showHideLayers('DATABASE_LAYER?header','','hide');
var p = "Blog Manager";
parent.frames.footer.setPage(p);

//-->

   //################################################
   //       _____ _          __  __  ___ ___ 
   //      |_   _(_)_ _ _  _|  \/  |/ __| __|
   //        | | | | ' \ || | |\/| | (__| _| 
   //        |_| |_|_||_\_, |_|  |_|\___|___|
   //                   |__/                 Stuff
   //################################################
   
   //Define global variables
   var dot_com = '<? echo $_SESSION['docroot_url']; ?>'
   
   var current_editing_area = '';
   <?php
	$origdir = getcwd();
	chdir('page_editor');
	include('tiny_init.php');
	chdir($origdir);
	?>
   // Gets content from editor and places it in editor
   // Called by setupcontent_callback within tinyMCE.init
   function pullHTML(editor_id, body, doc){
      //alert(current_editing_area)
      var html = document.SOHOEDITOR.message.value;
      //alert(html);
//      html = html.replace('<blink>','');
//      html = html.replace('</blink>','');
//      html = html.replace('<BLINK>','');
//      html = html.replace('</BLINK>','');

      var inst = tinyMCE.getInstanceById(tinyMCE.selectedInstance.editorId);
      var newHtml = TinyMCE_MediaPlugin.cleanup('insert_to_editor',html,inst);
      
      body.innerHTML = html;
      //alert('joe');
   }
   
   // Hide / show / load / unload editor within spcified id (div or textarea)
   function toggleEditor(id) {
      if($('userOpsLayer').style.overflow == "auto"){
         $('userOpsLayer').scrollTop=0
         $('userOpsLayer').style.overflow='hidden'
      }else{
         $('userOpsLayer').style.overflow='auto'
      }
   	var elm = document.getElementById(id);
      toggleDropdownz()   
   	if (tinyMCE.getInstanceById(id) == null){
   		tinyMCE.execCommand('mceAddControl', false, id);
   	   $('tiny_editor_container').style.display='block';
   	}else{
   		tinyMCE.execCommand('mceRemoveControl', false, id);
   	   $('tiny_editor_container').style.display='none';
   	}
   }
   
   function toggleDropdownz(){
      if($('del_subj').style.visibility!='hidden'){
         $('del_subj').style.visibility='hidden'
         $('pSUBJ').style.visibility='hidden'
         $('pNEWS').style.visibility='hidden'
         $('pPROMO').style.visibility='hidden'
      }else{
         $('del_subj').style.visibility='visible'
         $('pSUBJ').style.visibility='visible'
         $('pNEWS').style.visibility='visible'
         $('pPROMO').style.visibility='visible'
      }
   }

</script>


<!-- ############################################################## -->
<!-- #### Choose Editor Layer                                  #### -->
<!-- ############################################################## -->

         <div id="chooseMode" style="position:absolute; top:130; left:260; z-index:200; display:none;">
         <table width="275"  border="0" cellspacing="0" cellpadding="0" class="feature_sub">
           <tr>
             <td class="fgroup_title">Select Editor Mode</td>
           </tr>
           <tr>
             <td height="80">
               <table width="100%" border="0" cellspacing="0" cellpadding="0">
                 <tr>
                   <td align="center" width="135" class"unnamed1"><input style="width:70px;" onClick="disBlog('basic');" type="button" id="openpage" value="Basic" <? echo $_SESSION['btn_edit']; ?> ></td>
                   <td align="center" class"unnamed1"><input style="width:70px;" onClick="disBlog('advanced');" type="button" id="openpage" value="Advanced" <? echo $_SESSION['btn_edit']; ?> ></td>
                 </tr>
                 <tr>
                   <td colspan="2" align="center" class="unnamed2">
                     <input type="checkbox" id="remember" name="remember" value="1">
                     <i>Remember my answer</i></td>
                 </tr>
               </table>
             </td>
           </tr>
         </table>
         </div>




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
   if(eregi("Firefox/3", $_SERVER['HTTP_USER_AGENT'])){
      $editorHeight = "435px";
   }else{
      $editorHeight = "475px";
   }
}

?>

<div id="tiny_editor_container" style="position: absolute; height: <? echo $editorHeight; ?>; top: 0px; left: 0px; bottom: 0px; right: 0px; _right: -25px; border: 0px solid green; display: none;">
   <!--- Editor Textarea -->
   <textarea id="tiny_editor" name="tiny_editor" rows="15" cols="80" style="height: <? echo $editorHeight; ?>; width: 100%; border: 0px dotted red;">editor content</textarea>
   
   <!--- Cancel / Done buttons -->
   <div id="saveIt" style="position:absolute; bottom: 1px; right: 15px; z-index:1000; display:block;">
   
      <input onClick="tinyMCE.execInstanceCommand('tiny_editor','mceCodeEditor',false);" type="button" id="html_view" value=" HTML View  " class="btn_edit" style="font-weight:bold; background-color: #F0F0EE;color: #88c8e;border: 1px solid #888c8e;width: 120px;padding: 0px;margin-right: 10em;" onmouseover="this.style.backgroundColor='#000';this.style.color='#fff';" onmouseout="this.style.backgroundColor='#F0F0EE';this.style.color='#000';">
      <!--- Cancel -->
      <input onClick="toggleEditor('tiny_editor');" type="button" id="cancel_edit" value="  Cancel  " style="width: 100px;padding: 1px;" <? echo $_SESSION['btn_delete']; ?> >
      <!--- Done -->
      <input onClick="SetBlog();" type="button" id="save_content" value="  Done  " style="width: 150px;padding: 1px;" <? echo $_SESSION['btn_save']; ?> >
   </div>
</div>


<!-- ########################## Start HTML Output Now ################################## -->



                  <table border="0" cellspacing="0" cellpadding="10" width="725">
                    <tr>
                      <td align="center" valign="top" class="text">

					  <!-- #### Start Content Area of Mod Table -->

                  <div style="border: 0px dotted red; height: 15px; text-align: right; padding-right: 20px;">
                     <font size="1" color="#f7941d" style="font-weight: bold;"><sup><i>NEW!</i></sup></font>
                     <a href="blog/blog_comments.php" class="sav" style="padding-right: 10px;">Manage Blog Comments</a>
                     <font size="1" color="#f7941d" style="font-weight: bold;"><sup><i>NEW!</i></sup></font>
                     <a href="blog/blog_styles.php" class="sav">
                        
                        Edit Blog Styles
                     </a>
                  </div>

					  <table border=0 cellpadding=4 cellspacing=1 width=100%>
					  <tr>
					  <td align=left valign=top class=txt width=100%>

					  	  <form method=post action="blog.php">
						  <input type=hidden name=ACTION value="uSUBJ">

						  <table border=0 cellpadding=4 cellspacing=0 width=100% style='border: 1px solid black;'>
						  <tr>
						  <td colspan="2" align="left" valign="top" class="fsub_title" style='border-bottom: 1px solid black;'><B><? echo lang("Blog Subjects"); ?>:</B></td>
						  </tr><tr>
						  <td align="center" valign="top" class="text" bgcolor="white" style="color: #000099;">
								<? echo lang("New Subject"); ?>: <input type=text name=new_blog_subj value="" class=text style='width: 200px;'>
								<input type=submit name=ADD value="<? echo lang("Add New"); ?>" class="btn_save" onMouseover="this.className='btn_saveon';" onMouseout="this.className='btn_save';">

						  </td>
						  <td align=center valign=top class=txt bgcolor=white>
								<select id="del_subj" name="del_subj" class="text" style='width: 200px;'>
								<option value="NULL"><? echo lang("Existing Subjects"); ?>...</option>
								 <? echo $SUBJECTS; ?>
								</select>
								<input type=submit name=VIEW value="<? echo lang("View"); ?>" class="btn_edit" onMouseover="this.className='btn_editon';" onMouseout="this.className='btn_edit';" style="width: 60px;">
								&nbsp;
								<input type=submit name=DEL value="<? echo lang("Delete"); ?>" class="btn_delete" onMouseover="this.className='btn_deleteon';" onMouseout="this.className='btn_delete';">
						  </td>
						  </tr>
						  </table>
						  </FORM>
						</td>
						</tr><tr>
						<td valign="top" class="text" width="100%" style="color: #000099;"><center><b>
							<? echo lang("Create a new blog entry by entering your data in the text editor below"); ?>.<br>
							<? echo lang("Then choose the subject that this blog should be assigned to and click Post Blog to continue"); ?>.
							</center></b>
							<BR>

							<!-- Start Word Processor Call -->

							<TABLE BORDER=0 CELLPADDING=3 CELLSPACING=0 WIDTH=100% HEIGHT=100% ALIGN=CENTER STYLE='border:1px solid black;'>
						  <tr>
						  <td colspan="3" align="left" valign="top" class="fsub_title" style='border-bottom: 1px solid black;'><B><? echo lang("Add Blog"); ?>:</B></td>
						  </tr>
							<TR>
							   <td align=left valign=top class=text>
   								<FORM NAME="SOHOEDITOR" METHOD="post" action="blog.php">
   								<input type="hidden" name="ACTION" value="pBLOG">
   								<TEXTAREA NAME="message" STYLE="display: none;"></TEXTAREA>

   								<B><? echo lang("Step 1: Blog Title"); ?>:</B>
   						   </td>
							   <td align=left valign=top class=text>
   								<INPUT TYPE=TEXT id="blog_title" NAME="blog_title" onKeyUp="dispStatus();" class="text" STYLE="width: 400px;" value="<? echo $getBlog['BLOG_TITLE']; ?>"><BR>
							   </td>
							   <td width=50 align=left valign=top class=text>
							   <font color="green">
							   <div id="status" style="display: none;"><B><? echo lang("Done!"); ?></B></div>
							   </font>
                        </td>
							</tr>
							<tr>
							   <td align=left valign=top class=text>
                           <B><? echo lang("Step 2: Enter Content For Blog"); ?>:</B>
                        </td>
							   <td align=left valign=top class=text>
                           <INPUT id="launch" TYPE=button VALUE="  <? echo lang("Launch Editor"); ?>  " class="btn_save" onMouseover="this.className='btn_saveon';" onMouseout="this.className='btn_save';" onClick="toggleEditor('tiny_editor');"><br>
                        </td>
							   <td width=50 align=left valign=top class=text>
							   <font color="green">
							   <div id="statusEdit" style="display: none;"><B><? echo lang("Done!"); ?></B></div>
							   </font>
                        </td>
                     </tr>
                     <tr>
							   <td align=left valign=top class=text>
   								<B>
   								<? echo lang("Step 3: Post Blog to"); ?>:
   							</td>
							   <td align=left valign=top class=text>
   								<select id="pSUBJ" ONCHANGE="dispCat()" name="pSUBJ" class=text style='width: 250px;'>

   									<option value="NULL"><? echo lang("Choose Subject"); ?>...</option>
   									<? echo $SUBJECTS; ?>

   								</select></b>

                           </font>&nbsp;
                        </td>
							   <td width=50 align=left valign=top class=text>
							   <font color="green">
							   <div id="statusPost" style="display: none;"><B><? echo lang("Done!"); ?></B></div>
							   </font>
                        </td>
							</tr>
							<tr>
							   <td align=center colspan="3" valign=top class=text>
							   <div id="statusGo" style="display: none;">
   								<INPUT TYPE=button VALUE="  <? echo lang("Post"); ?>  " class="btn_save" onMouseover="this.className='btn_saveon';" onMouseout="this.className='btn_save';" onClick="postNcheck();">
							   </div>

							   </td>
							</tr>

								<?

                     	# Main Text Editor box
                     	//echo "<iframe id=\"editBox\" WIDTH=98% HEIGHT=250 src=\"page_editor/text_editor_obj_45.php?BLOGON=1&blogID=$blogID&=SID\"></iframe>\n";
								//echo "<OBJECT ID=editBox WIDTH=98% HEIGHT=250 DATA=\"page_editor/text_editor_obj_45.php?BLOGON=1&blogID=$blogID&=SID\" TYPE=\"text/x-scriptlet\"></OBJECT>\n";

								?>


								</FORM>

							</TD>
							</TR>
							</TABLE>

							<!-- End Word Processor Call -->

						</td>
						</tr>
						</table>
						<br/>

						<div class="text" style="color: #000099;"><center><b>
							<? echo lang("Some templates display content from blog categories.  There are 2 types of display, newsbox and promo box"); ?>.<br>
							<? echo lang("Please select a blog category to display content from for each"); ?>.
							</center></b></div>
							<BR>

<!-- ###########################################################################
//						 _  _                       ___
//						| \| |_____ __ _____  ___  | _ \_ _ ___ _ __  ___
//						| .` / -_) V  V (_-< |___| |  _/ '_/ _ \ '  \/ _ \
//						|_|\_\___|\_/\_//__/       |_| |_| \___/_|_|_\___/
    ############################################################################ -->

						<FORM NAME="newsPromo" METHOD="post" action="blog.php">
						<input type="hidden" name="ACTION" value="pNEWSPROMO">
							<table border="0" cellpadding="3" cellspacing="0" width="99%" height="100%" align="center" style='border:1px solid black;'>
						  <tr>
						  <td colspan="2" align="left" valign="top" class="fsub_title" style='border-bottom: 1px solid black;'><B><? echo lang("Assign Blog Category"); ?>:</B></td>
						  </tr>
								<tr>
								   <td align="left" valign="middle" class="text">
								   <b><? echo lang("Newsboxes should display content from which category?"); ?></b>
								   </td>
								   <td align="left" valign="top" class="text">
	   								<select id="pNEWS" name="pNEWS" class=text style='width: 250px;'>

	   									<option value="NULL"><? echo lang("Choose Subject"); ?>...</option>
	   									<? echo $NEWS_SUBJECTS; ?>

	   								</select>
								   </td>
								</tr>

								<tr>
								   <td align="left" valign="middle" class="text">
								   <b><? echo lang("Promo boxes should display content from which category?"); ?></b>
								   </td>
								   <td align="left" valign="top" class="text">
	   								<select id="pPROMO" name="pPROMO" class=text style='width: 250px;'>

	   									<option value="NULL"><? echo lang("Choose Subject"); ?>...</option>
	   									<? echo $PROMO_SUBJECTS; ?>

	   								</select>
								   </td>
								</tr>

							<tr>
							   <td align=center colspan="3" valign=top class=text>
   								<input type="submit" value="  <? echo lang("Save"); ?>  " class="btn_save" onMouseover="this.className='btn_saveon';" onMouseout="this.className='btn_save';">
							   </td>
							</tr>

							</table>
						</form>

					  <!-- #### End Content Area of Mod Table -->

                      </td>
                    </tr>
                  </table>


<!-- #################################### End HTML Output Now ######################################## -->


<?
if ($update_flag == 1) { echo "<script language=javascript>	alert('".lang("Update Complete")."!'); </script>\n"; }
if ($news_promo_flag == 1) { echo "<script language=javascript>	alert('".lang("Update Complete")."!'); </script>\n"; }
if ($del_err == 1) { echo "<script language=javascript>	alert('".lang("Can not delete this subject.  Blog data exists")."!'); </SCRIPT>\n"; }


# Grab module html into container var
$module_html = ob_get_contents();
ob_end_clean();

$instructions = lang("Create blog subjects, add/edit blog content, and assign blog content.");
//$instructions .= lang("Please only use alpha-numerical characters and spaces.");

# Build into standard module template
$module = new smt_module($module_html);
$module->meta_title = "Blog Manager";
$module->add_breadcrumb_link("Blog Manager", "program/modules/blog.php");
$module->icon_img = "skins/".$_SESSION['skin']."/icons/full_size/blog_manager-enabled.gif";
$module->heading_text = "Blog Manager";
$module->description_text = $instructions;
$module->good_to_go();
?>
