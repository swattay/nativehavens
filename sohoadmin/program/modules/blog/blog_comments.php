<?php
error_reporting(E_PARSE);
if($_GET['_SESSION'] != '' || $_POST['_SESSION'] != '' || $_COOKIE['_SESSION'] != '') { exit; }


###############################################################################
## Soholaunch(R) Site Management Tool
## Version 4.5
##
## Author:        Mike Johnston [mike.johnston@soholaunch.com]
## Homepage:      http://www.soholaunch.com
## Bug Reports:   http://bugzilla.soholaunch.com
## Release Notes: sohoadmin/build.dat.php
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

error_reporting(E_PARSE);

session_start();

# Include core files
include("../../includes/product_gui.php");

## ====================================================
## DOES BLOG_COMMENTS TABLE EXSIST?
## ====================================================

$match = 0;

$result = mysql_list_tables("$db_name");
$i = 0;
while ($i < mysql_num_rows ($result)) {
	$tb_names[$i] = mysql_tablename ($result, $i);
	if (strtolower($tb_names[$i]) == "blog_comments") { $match = 1; }
	$i++;
}

// DOES NOT EXIST; CREATE TABLE NOW
## ====================================================
if ($match != 1) {
	$qry = "CREATE TABLE BLOG_COMMENTS (";
	$qry .= " PRIKEY INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,";
	$qry .= " BLOG_KEY INT,";
	$qry .= " NAME VARCHAR(255),";
	$qry .= " EMAIL BLOB,";
	$qry .= " COMMENTS BLOB,";
	$qry .= " COMMENT_DATE DATETIME,";
	$qry .= " STATUS VARCHAR(255)";
   $qry .= ")";
   //ECHO $qry."<br/>";
	if (!mysql_db_query("$db_name",$qry)){
		echo "Could not create table BLOG_COMMENTS!<br>";
		echo "Mysql says (".mysql_error().")";
		exit;
	}
}

#######################################################
### START HTML/JAVASCRIPT CODE             ###
#######################################################

# Start buffering output
ob_start();

# Show display settings
if(!$_SESSION['display_type'] && !$_REQUEST['display_type']){
   //echo "here1";
   $_SESSION['display_type'] = "show_new";
   $display_new = "checked";
   $display_denied = "";
   $display_approved = "";
}else{
   //echo "here2";
   //echo "(".$_SESSION['display_type'].")<br/>\n";
   if($_REQUEST['display_type']){
      $_SESSION['display_type'] = $_REQUEST['display_type'];
   }

   if(eregi("show_new", $_SESSION['display_type'])){ $display_new = "checked"; }else{ $display_new = ""; }
   if(eregi("show_denied", $_SESSION['display_type'])){ $display_denied = "checked"; }else{ $display_denied = ""; }
   if(eregi("show_approved", $_SESSION['display_type'])){ $display_approved = "checked"; }else{ $display_approved = ""; }
}
//echo "(".$_SESSION['display_type'].")<br/>\n";

//   echo "(".$display_new.")<br/>\n";
//   echo "(".$display_denied.")<br/>\n";
//   echo "(".$display_approved.")<br/>\n";

# Pull default email for site
$result = mysql_query("SELECT df_email FROM site_specs LIMIT 1");
$SITE_SPECS = mysql_fetch_array($result);
$admin_email = $SITE_SPECS['df_email'];

# Pull blog comment settings
$blog_comment_settings = new userdata("blog_comment");

if(!$blog_comment_settings->get("allow_comments") || $blog_comment_settings->get("allow_comments") == "no"){
   $is_allowed = "";
   $display_category = "none";
}else{
   $is_allowed = "checked";
   $display_category = "block";
}

if(!$blog_comment_settings->get("emailto")){
   $blog_comment_settings->set("emailto", $admin_email);
}else{
   $admin_email = $blog_comment_settings->get("emailto");
}

if(!$blog_comment_settings->get("captcha") || $blog_comment_settings->get("captcha") == "no"){
   $is_captcha = "";
}else{
   $is_captcha = "checked";
}

if( !$blog_comment_settings->get("require_approval") ){
   $blog_comment_settings->set("require_approval", "yes");
   $is_required = "checked";
}elseif( $blog_comment_settings->get("require_approval") == "yes" ){
   $is_required = "checked";
}elseif( $blog_comment_settings->get("require_approval") == "no" ){
   $is_required = "";
}

?>

<style>

.comment_container {
   /*border: 1px dashed green;*/
}

.a_comment {
   /*width: 100%;*/
   margin: 10px;
   border: 1px solid #000000;
   background-color: #cccccc;
   font-family: Trebuchet MS, arial, helvetica, sans-serif;
   font-size: 11px;
}

.a_comment div.post_name {
   margin-left: 10px;
   font-weight: bold;
   font-size: 13px;
   /*color: blue;*/
}

.a_comment span {
   margin-left: 10px;
   font-style: italic;
}

.a_comment p {
   margin-left: 10px;
   margin-right: 10px;
   background-color: #efefef;
   padding: 5px;
   border: 1px dashed #999999;
}

.result_select {
   float: right;
   width: 75px;
   /*background-color: #dfdfdf;*/
   border: 1px solid #000000;
   border-style: none none solid solid;
   /*display: inline;*/
}

.result_select div {
   text-align: left;
   padding-left: 10px;
   font-size: 12px;
   /*display: inline;*/
}

.delete_spacer {
   height: 20px;
   /*float: right;*/
   /*width: 75px;*/
   /*text-align: right;*/
   /*background-color: #dfdfdf;*/
   /*border: 1px solid #000000;
   border-style: none none solid solid;*/
   /*display: inline;*/
}

.delete_select {
   float: right;
   width: 65px;
   height: 20px;
   text-align: left;
   padding-left: 10px;
   font-size: 12px;
   /*text-align: right;*/
   /*background-color: #dfdfdf;*/
   border: 1px solid #000000;
   border-style: solid none none solid;
   /*display: inline;*/
}



.comment_result {
   /*float: right;*/
   width: 150px;
   margin-left: 150px;
   display: inline;
   font-weight: bold;
   color: green;
}

.display_options {
   text-align: center;
   padding-top: 10px;
   /*border: 1px dashed #000000;*/
   width: 720px;
}

.tab-off, .tab-on {
   position: absolute;
   top: -25px;
   z-index: 2;
   text-align: center;
   width: 125px;
   /*height: 25px;*/
   vertical-align: top;
   font-weight: bold;
   padding-top: 5px;
   padding-bottom: 5px;
   margin-right: 15px;
   background-color: #efefef;
   border: 1px solid #ccc;
   border-top: 3px solid #ccc;
   color: #595959;
   cursor: pointer;
}

.tab-on {
   color: #000;
   background-color: #efefef;
   border-top: 3px solid #175aaa;
   font-weight: bold;
}

/* Table containing content for each tab */
table.tab_content {
   _margin: -20px 5px 20px 5px;
   border: 1px solid #ccc;
   margin-left: 5px;
   /*width: 100%;*/
   /*position: relative;*/
}

#layout_tab1 { left: 5px; }
#layout_tab2 { left: 140px; }
#layout_tab3 { left: 275px; }
#layout_tab4 { left: 610px; }
#layout_tab5 { left: 410px; }

/*  ___
   | __|__ _ _ _ __  ___
   | _/ _ \ '_| '  \(_-<
   |_|\___/_| |_|_|_/__/ */

/* Hack to fix border on floated elements in IE */
.ie_cleardiv {
   display: block;
   clear: both;
   float: none;
   margin: 0;
   /*border: 1px dotted red;*/
}

.field-container {
   display: block;
   clear: both;
   margin-bottom: 6px;
   vertical-align: top;
   /*border: 1px solid red;*/
}
.asterisk {
   color: red;
}

.instructions {
   margin-top: 0;
   color: #2e2e2e;
   font-family: Arial, helvetica, sans-serif;
   font-size: 13px;
   line-height: 1.1em !important;
}

.myform-field_title-top,
.myform-field_title-left {
   font-size: 12px;
   font-weight: bold;
   font-family: Arial, helvetica, sans-serif;
   margin-bottom: 0;
   color: #000000;
   border-width: 1px;
   border-color: #ccc;
   border-style: hidden;
   width: 130px;
}
.myform-field_title-left {
   display: block;
   float: left;
   margin-right: 15px;
   /*margin-top: 12px;*/
   margin-top: 2px;
   text-align: left;
   /*border: 1px solid red;*/
}

.myform-field_title-hidden {
   display: none;
}

.myform-input_container, .myform-formfield_container {
   display: block;
   float: left;
   margin-top: 0;
   font-size: 11px;
}

.form_body_container {
   text-align: left;
   background-color: transparent;
   margin: 0;
   padding: 5;
   width: ;
   border-style: solid;
   border-width: 0px;
   border-color: F0F8FF;
   font-family: Arial, helvetica, sans-serif;
}

.userform-submit_btn-container {
   text-align: left;
}

.submit_btn {
   font-size: 13px;
   font-weight: bold;
}

.instructions {
   margin-top: 0;
   color: #2e2e2e;
   font-family: Trebuchet MS, arial, helvetica, sans-serif;
   font-size: 12px;
   line-height: 1.1em !important;
}

#settings_result {
   color: green;
   font-size: 15px;
   font-weight: bold;
}


</style>


<script language="javascript">


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

var p = "<? echo lang("Manage Blog Comments"); ?>";
parent.frames.footer.setPage(p);


//---------------------------------------------------------------------------------------------------------
//      _      _   _   __  __
//     /_\  _ | | /_\  \ \/ /
//    / _ \| || |/ _ \  >  <
//   /_/ \_\\__//_/ \_\/_/\_\
//
//---------------------------------------------------------------------------------------------------------
// The following script (as commonly seen in other AJAX javascripts) is used to detect which browser the client is using.
// If the browser is Internet Explorer we make the object with ActiveX.
// (note that ActiveX must be enabled for it to work in IE)
function makeObject() {
   var x;
   var browser = navigator.appName;

   if ( browser == "Microsoft Internet Explorer" ) {
      x = new ActiveXObject("Microsoft.XMLHTTP");
   } else {
      x = new XMLHttpRequest();
   }

   return x;
}

// The javascript variable 'request' now holds our request object.
// Without this, there's no need to continue reading because it won't work ;)
var request = makeObject();

function ajaxDo(qryString, boxid) {
   //alert(qryString+', '+boxid);

   rezBox = boxid; // Make global so parseInfo can get it

   // The function open() is used to open a connection. Parameters are 'method' and 'url'. For this tutorial we use GET.
   request.open('get', qryString);

   // This tells the script to call parseInfo() when the ready state is changed
   request.onreadystatechange = parseInfo;

   // This sends whatever we need to send. Unless you're using POST as method, the parameter is to remain empty.
   request.send('');

}

function parseInfo() {
   // Loading
   if ( request.readyState == 1 ) {
      document.getElementById(rezBox).innerHTML = 'Loading...';
   }

   // Finished
   if ( request.readyState == 4 ) {
      var answer = request.responseText;
      document.getElementById(rezBox).innerHTML = answer;
   }
}

// End AJAX

function getTypes(){
   var display_string = '';
   if(document.getElementById('show_new').checked)
      display_string += 'show_new;';
   if(document.getElementById('show_denied').checked)
      display_string += 'show_denied;';
   if(document.getElementById('show_approved').checked)
      display_string += 'show_approved;';
   document.getElementById('display_type').value = display_string
   document.display_form.submit();
}

function check_n_send() {
   var email_to = $('email_to').value
   if($('captcha').checked){ var captcha = "yes"; }else{ var captcha = "no"; }
   if($('enable').checked){ var enable = "yes"; }else{ var enable = "no"; }
   if($('require_approval').checked){ var require_approval = "yes"; }else{ var require_approval = "no"; }

   var allowed_categorys = "";

   var num_ele = document.comment_settings_form.elements.length
   //alert(num_ele);
   for(var x = 0; x < num_ele; x++){
      if(!document.comment_settings_form.elements[x].name.search("category") && document.comment_settings_form.elements[x].checked){
         var names = document.comment_settings_form.elements[x].name.split("___")
         //alert(names[0]+'---'+names[1])

         allowed_categorys = allowed_categorys+names[1]+";";
         //alert(allowed_categorys)
      }
   }
   //alert(allowed_categorys)

   ajaxDo('comment_result.php?process=comment_settings&email_to='+email_to+'&captcha='+captcha+'&enable='+enable+'&require_approval='+require_approval+'&allowed_categorys='+allowed_categorys, 'settings_result');
}

function check_display(ele) {
   if(ele.checked)
      $('allow_individual').style.display='block'
   else
      $('allow_individual').style.display='none'
}


</script>

      <div style="border: 0px dotted red; height: 15px; text-align: right; float: right; padding-right: 20px;">
         <font size="1" color="#f7941d" style="font-weight: bold;"><sup><i>NEW!</i></sup></font>
         <a class="sav" href="blog_styles.php">Edit Blog Styles</a>
      </div>

   <!---Container div-->
   <div id="tab_interface_container" style="display: block; width: 100%;margin: 20px 5px 20px 5px;position: relative; border: 0px solid red;">

      <!---================== Tabs - START ==================-->
      <div id="layout_tab1" class="tab-on" onclick="showid('tab1-content');hideid('tab2-content');setClass('layout_tab1', 'tab-on');setClass('layout_tab2', 'tab-off');">
      Blog Comments
      </div>

      <div id="layout_tab2" class="tab-off" onclick="showid('tab2-content');hideid('tab1-content');setClass('layout_tab2', 'tab-on');setClass('layout_tab1', 'tab-off');">
      Settings
      </div>

   </div>

      <table id="tab1-content" border="0" cellspacing="0" cellpadding="0" class="feature_sub tab_content" style="display: table;">
         <tr>
            <td style="">

               <div class="display_options">

                  <form name="display_form" action="blog_comments.php">
                     <input type="hidden" name="process" value="display_settings" />
                     <input type="hidden" id="display_type" name="display_type" value="" />
                     <b>Display Options:</b>

                     <input type="checkbox" id="show_new" name="show_new" <? echo $display_new; ?> /> <label for="show_new">Show new</label>
                     <input type="checkbox" id="show_denied" name="show_denied" <? echo $display_denied; ?> /> <label for="show_denied">Show denied</label>
                     <input type="checkbox" id="show_approved" name="show_approved" <? echo $display_approved; ?> /> <label for="show_approved">Show approved</label>
                     <input type="button" onclick="getTypes()" value="Show Selected" class="btn_blue hand" style="margin-left: 5px;" />
                  </form>
               </div>

<?php

   $display_what = "";
   if($display_new == "checked"){ $display_what .= "STATUS = 'new' "; }

   if($display_denied == "checked"){
      if($display_what != ""){
         $display_what .= "OR ";
      }
      $display_what .= "STATUS = 'denied' ";
   }
   if($display_approved == "checked"){
      if($display_new == "checked" || $display_denied == "checked"){
         $display_what .= "OR ";
      }
      $display_what .= "STATUS = 'approved' ";
   }
   //echo "(".$display_what.")<br/>\n";

   $at_least_one = 0;

   $blogQry = "SELECT * FROM BLOG_CATEGORY";
   $result = mysql_query($blogQry);

   while ($CATEGORY = mysql_fetch_array($result)) {
      $found_comment = 0;


      $current_category = $CATEGORY['PRIKEY'];
      $blogQry2 = "SELECT PRIKEY, BLOG_SUBJECT, BLOG_TITLE FROM BLOG_CONTENT WHERE BLOG_SUBJECT = '".$current_category."'";
      $result2 = mysql_query($blogQry2);

      while ($CONTENT = mysql_fetch_array($result2)) {

         # Blog comments

         $comment_qry = "SELECT PRIKEY, NAME, COMMENTS, COMMENT_DATE, STATUS FROM BLOG_COMMENTS WHERE BLOG_KEY = '".$CONTENT['PRIKEY']."' AND (".$display_what.") ORDER BY COMMENT_DATE DESC";
         //echo "(".$comment_qry.")<br/>\n";

         $comment_result = mysql_query($comment_qry);

         //echo "Num comments (".mysql_num_rows($comment_result).")<br/>\n";

         if(mysql_num_rows($comment_result) > 0){

            if($found_comment == 0){
               $at_least_one = 1;
               echo "<h1 style=\"margin: 5px;\"><b>Blog posts with new comments in <span class=\"blue\">".$CATEGORY['CATEGORY_NAME']."</span>.</b> <span class=\"text green hand\" onclick=\"toggleid('comment_container_".$CATEGORY['PRIKEY']."');\">show/hide</span></h1>\n";
               echo "<div id=\"comment_container_".$CATEGORY['PRIKEY']."\" style=\"display: block;\">\n";
            }

            $found_comment = 1;

            echo "<div class=\"comment_container\" style=\"margin: 10px;\">\n";
            echo "<h2><span class=\"green\">".$CONTENT['BLOG_TITLE']."</span> has ".mysql_num_rows($comment_result)." new comments.</h2>\n";

            while($COMMENTS = mysql_fetch_array($comment_result)){
               echo "<div class=\"a_comment\" style=\"\">\n";

               echo "   <div class=\"result_select\">\n";
               echo "      <div class=\"green\" style=\"cursor: pointer;\" onclick=\"ajaxDo('comment_result.php?process=comment&comment=".$COMMENTS['PRIKEY']."&do=approved', 'comment_result_".$COMMENTS['PRIKEY']."');\">Approve</div>\n";
               echo "      <div class=\"red\" style=\"cursor: pointer;\" onclick=\"ajaxDo('comment_result.php?process=comment&comment=".$COMMENTS['PRIKEY']."&do=denied', 'comment_result_".$COMMENTS['PRIKEY']."');\">Deny</div>\n";
               echo "   </div>\n";

               echo "   <div class=\"post_name blue\">Posted by ".stripslashes(html_entity_decode($COMMENTS['NAME'], ENT_QUOTES))."\n";

//               echo "   <div class=\"comment_result\" id=\"comment_result_".$COMMENTS['PRIKEY']."\">\n";
//               if($COMMENTS['STATUS'] != "new"){ echo "Currently ".$COMMENTS['STATUS']; }else{ echo "&nbsp;"; }
//               echo "   </div>\n";

               echo "   </div>\n";
               echo "   <span>".date("M-j g:ia",strtotime($COMMENTS['COMMENT_DATE']))."</span>\n";

               echo "   <div class=\"comment_result\" id=\"comment_result_".$COMMENTS['PRIKEY']."\">\n";
               if($COMMENTS['STATUS'] != "new"){ echo "Currently ".$COMMENTS['STATUS']; }else{ echo "&nbsp;"; }
               echo "   </div>\n";

               echo "   <p>".nl2br(stripslashes(html_entity_decode($COMMENTS['COMMENTS'], ENT_QUOTES)))."</p>\n";

               echo "   <div class=\"red delete_select\" style=\"cursor: pointer;\" onclick=\"ajaxDo('comment_result.php?process=delete&comment=".$COMMENTS['PRIKEY']."', 'comment_result_".$COMMENTS['PRIKEY']."');\">Delete</div>\n";
               echo "   <div class=\"delete_spacer\">&nbsp;</div>\n";

               echo "</div>\n";



            }

            echo "</div>\n";
         }else{
            //echo "<p class=\"no_comments\">No Comments</p>\n";
         }
      }

      if($found_comment == 1){
         echo "</div>\n";
      }
   }

   if($at_least_one == 0){
      echo "<h1 style=\"margin: 5px;\"><b>There are no new blog comments.</b></h1>\n";
   }

?>
            <td>
         <tr>
      </table><!---End Tab1--->

      <table id="tab2-content" border="0" cellspacing="0" cellpadding="0" class="feature_sub tab_content" style="display: none;">
         <tr>
            <td style="padding-left: 15px;">

               <form name="comment_settings_form" id="comment_settings_form" method="post" action="blog_comments.php">

               <div id="form_body_container" style="text-align: left;background-color: transparent;margin: 10;padding: 0px;width: ;border-style: none;border-width: 0;border-color: 000;">

                  <!--- Title -->
                  <div class="field-container">
                  <h1 style="margin-top: 10px; margin-bottom: 5px;">Blog Comment Settings</h1>
                  <p class="instructions" style="color: #595959; margin-bottom: 15px;">These settings define different parts of the blog comment system.</p>
                  <div class="ie_cleardiv">
                  </div>
                  </div>

                  <!--- Allow blog comments? -->
                  <div class="field-container">
                  <p class="myform-field_title-left"><label for="enable">Allow blog comments</label> :
                  </p>
                  <p class="myform-input_container"><input onclick="check_display(this);" type="checkbox" name="enable" id="enable" style="margin-right: 10px;" <? echo $is_allowed; ?> /></p>
                  <p class="instructions" style="color: #595959; padding-top: 3px;">Should users be able to post comments about your blog posts?</p>
                  <div class="ie_cleardiv">
                  </div>
                  </div>

                  <!--- Allow blog comments? -->
                  <div class="field-container" id="allow_individual" style="background-color: #FFFFFF; border: 1px dashed #CCCCCC; padding: 5px; display: <? echo $display_category; ?>;">
                  <p class="myform-field_title-left">Allow comments for individual categorys :
                  </p>
                  <p class="myform-input_container">

                  <?php

                  if(!$blog_comment_settings->get("allowed_categorys")){
                     $allowed_categorys = "all";
                  }else{
                     $allowed_categorys = $blog_comment_settings->get("allowed_categorys");
                  }

                  $blogQry = "SELECT * FROM BLOG_CATEGORY";
                  $result = mysql_query($blogQry);

                  while ($CATEGORY = mysql_fetch_array($result)) {
                     $is_checked = "";
                     if($allowed_categorys == "all" || eregi($CATEGORY['CATEGORY_NAME'], $allowed_categorys)){
                        $is_checked = "checked";
                     }
                     echo "<input type=\"checkbox\" name=\"category___".$CATEGORY['CATEGORY_NAME']."\" id=\"category___".$CATEGORY['CATEGORY_NAME']."\" style=\"margin-right: 10px;\" ".$is_checked." /> <label for=\"category___".$CATEGORY['CATEGORY_NAME']."\">".$CATEGORY['CATEGORY_NAME']."</label><br/>\n";
                  }

                  ?>

                  </p>
                  <p class="instructions" style="color: #595959; padding-top: 3px;">If you wish to disable comments for a specific blog you can do that here.</p>
                  <div class="ie_cleardiv">
                  </div>
                  </div>

                  <!--- Send Confirmation Email? -->
                  <div class="field-container">
                     <p class="myform-field_title-left"><label for="captcha">Require webmaster approval:</label></p>
                     <p class="myform-input_container"><input type="checkbox" name="require_approval" id="require_approval" style="margin-right: 10px;" <? echo $is_required; ?> /></p>
                     <p class="instructions" style="color: #595959; padding-top: 3px;">If you disable this, all comments will be approved and displayed when submitted.</p>
                     <div class="ie_cleardiv"></div>
                  </div>

                  <!--- Email to? -->
                  <div class="field-container">
                  <p class="myform-field_title-left"><label for="email_to">Email comments to:</label>
                  </p>
                  <p class="myform-input_container"><input type="text" name="email_to" id="email_to" style="width: 220px; margin-right: 5px;" value="<? echo $admin_email; ?>"/></p>
                  <p class="instructions" style="color: #595959; padding-top: 5px;"> Who should get notified when a comment is posted?</p>
                  <div class="ie_cleardiv">
                  </div>
                  </div>

                  <!--- Captcha? -->
                  <div class="field-container">
                     <p class="myform-field_title-left"><label for="captcha">Display captcha:</label></p>
                     <p class="myform-input_container"><input type="checkbox" name="captcha" id="captcha" style="margin-right: 10px;" <? echo $is_captcha; ?> /></p>
                     <p class="instructions" style="color: #595959; padding-top: 3px;">This adds a form verification field to the end of comment forms.  Helps combat spammers.</p>
                     <div class="ie_cleardiv"></div>
                  </div>

                  <div class="userform-submit_btn-container" style="margin-bottom: 35px;">
                  <p class="myform-field_title-left">
                     <input onmouseout="this.className='btn_save';" onmouseover="this.className='btn_saveon';" class="btn_save" type="button" onclick="check_n_send();" value="Save >>">
                  </p>
                     <p class="myform-input_container"><div id="settings_result">&nbsp;</div></p>
                  </div>

               </div>
               </form>

            <td>
         <tr>
      </table><!---End Tab2--->

<?php

# Grab module html into container var
$module_html = ob_get_contents();
ob_end_clean();

$instructions = lang("Manage all comments posted about your blogs.");

# Build into standard module template
$module = new smt_module($module_html);
$module->meta_title = "Blog Comments";
$module->add_breadcrumb_link("Blog Manager", "program/modules/blog.php");
$module->add_breadcrumb_link("Blog Comments", "program/modules/blog/blog_comments.php");
$module->icon_img = "skins/".$_SESSION['skin']."/icons/full_size/blog_manager-enabled.gif";
$module->heading_text = "Blog Comments";
$module->description_text = $instructions;
$module->good_to_go();
?>