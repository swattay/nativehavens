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

if(!function_exists('db_string_format')){
	function db_string_format($string) {
   	if ( !get_magic_quotes_gpc() ) {
      	return mysql_real_escape_string($string);
   	} else {
      	return $string;
   	}
	}
}


# Pull blog style settings
$blog_style_settings = new userdata("blog_styles");

if($_REQUEST['writeit'] == "1"){
   $saved = 0;
   $filename = "pgm-blog_styles.php";
//   $style_content = stripslashes(db_string_format(htmlentities($_REQUEST['file_content'], ENT_QUOTES))); // Causing problems on some servers
   $style_content = db_string_format($_REQUEST['file_content']);
   //$blog_comments = stripslashes(htmlentities($blog_comments, ENT_QUOTES));
   $current_dir = getcwd();

   # Save styles to DB
   $blog_style_settings->set("styles", $style_content);
   $saved = 1;

}


######################################################
##    get contents of file if not saved to db yet
######################################################

$current_dir = getcwd();
chdir($_SESSION['doc_root']);

$filename = "pgm-blog_styles.php";
ob_start();
	include("$filename");
	$BLOG_CSS_ORIG = ob_get_contents();
ob_end_clean();

chdir($current_dir);

if($blog_style_settings->get("styles")){
   # Already saved in db, pull it now!
   $BLOG_CSS = stripslashes(html_entity_decode($blog_style_settings->get("styles"), ENT_QUOTES));
}else{
   $BLOG_CSS = $BLOG_CSS_ORIG;
}

#######################################################
### START HTML/JAVASCRIPT CODE             ###
#######################################################

# Start buffering output
ob_start();


?>

<style>

h1 {
   padding: 0;
   margin: 0;
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

var p = "<? echo lang("Manage Blog Styles"); ?>";
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

function delete_it() {
   var really_revert = confirm("This will revert any changes you have made to the blog styles.\n\nAre you sure?");
   if(really_revert){
      ajaxDo('comment_result.php?process=delete_styles', 'delete_result');
      var orig_styles = $('orig_styles').innerHTML
      $('file_content').value = orig_styles
   }
}

</script>


<?php

# Restore default styles
echo "<div style=\"text-align:right; float: right; margin-right: 10px;\">\n";
echo "   <div id=\"delete_result\" style=\"float: left; margin-right: 20px; font-size: 15px; color: green; font-weight: bold;\">&nbsp;</div>\n";
echo "   <a href=\"#\" class=\"sav\" onclick=\"delete_it()\">Restore default styles</a>\n";
echo "</div>\n";

# Header + save result display
echo "<h1 style=\"margin: 0; padding: 0;\">Edit Blog Styles\n";
if($saved && $saved == 0 ){
   echo "<b class=\"red\" style=\"padding-left: 20px;\">Unable to save content! \n";
   echo "Please check permissions on pgm-blog_styles.php in the doc root and sohoadmin/client_files/base_files/</b>\n";
}elseif($saved && $saved == 1){
   echo "<b class=\"green\" style=\"padding-left: 20px;\">Styles saved!</b>\n";
}
echo "</h1>\n";

# Textarea with styles
echo "<div style=\"width: 100%;\">\n";
echo "<form method=\"post\" action=\"blog_styles.php\" name=\"writeFile\" style=\"margin: 0; padding: 0;\">\n";
echo "   <input type=\"hidden\" name=\"writeit\" value=\"1\" />\n";
echo "   <textarea name=\"file_content\" id=\"file_content\"  style=\"width: 100%; height: 300px; _height: 270px;\">".$BLOG_CSS."</textarea>\n";
echo "   <input type=\"submit\" name=\"submit\" value=\"Save >>\" />\n";
echo "</form>\n";
echo "</div>\n";

echo "<div id=\"orig_styles\" style=\"display: none;\">".$BLOG_CSS_ORIG."</div>\n";


# Grab module html into container var
$module_html = ob_get_contents();
ob_end_clean();

$instructions = lang("Edit the styles that define your blog display.");

# Build into standard module template
$module = new smt_module($module_html);
$module->meta_title = "Blog Styles";
$module->add_breadcrumb_link("Blog Manager", "program/modules/blog.php");
$module->add_breadcrumb_link("Blog Styles", "program/modules/blog/blog_styles.php");
$module->icon_img = "skins/".$_SESSION['skin']."/icons/full_size/blog_manager-enabled.gif";
$module->heading_text = "Blog Styles";
$module->description_text = $instructions;
$module->good_to_go();
?>