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

# Set comment status
if($_REQUEST['process'] == "comment"){

   $BLOG_QRY = "UPDATE BLOG_COMMENTS SET STATUS = '".$_REQUEST['do']."' WHERE PRIKEY = '".$_REQUEST['comment']."'";
   if(!mysql_query($BLOG_QRY)){
      echo "Error updating comment!";
   }else{
      echo "Comment ".$_REQUEST['do'];
   }
}

# Delete comment
if($_REQUEST['process'] == "delete"){
   if(!mysql_query("DELETE FROM BLOG_COMMENTS WHERE PRIKEY = '".$_REQUEST['comment']."'")){
      echo mysql_error();
   }else{
      echo "Comment deleted";
   }
}

# Update comment settings
if($_REQUEST['process'] == "comment_settings"){
   
   $blog_comment_settings = new userdata("blog_comment");
   
   $blog_comment_settings->set("allow_comments", $_REQUEST['enable']);
   $blog_comment_settings->set("emailto", $_REQUEST['email_to']);
   $blog_comment_settings->set("captcha", $_REQUEST['captcha']);
   $blog_comment_settings->set("require_approval", $_REQUEST['require_approval']);
   $blog_comment_settings->set("allowed_categorys", $_REQUEST['allowed_categorys']);

   echo "Settings Updated";
}

# Delete styles
if($_REQUEST['process'] == "delete_styles"){
   if(!mysql_query("DELETE FROM smt_userdata WHERE plugin = 'blog_styles' AND fieldname = 'styles'")){
      echo mysql_error();
   }else{
      echo "Default Styles Restored";
   }
}

?>