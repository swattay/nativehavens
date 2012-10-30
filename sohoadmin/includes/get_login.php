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

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
// By License you may not modify any portion of this script. This particular
// script has dependancies and programming that can not be modified.
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

error_reporting(E_PARSE);
session_start();

include("includes/autoconfig.php");

if (!session_is_registered("keystroke_login")) { session_register("keystroke_login"); }
if ($keystroke == "on") {
	$keystroke_login = $keystroke;
}

# Pull webmaster preferences
$webmaster_prefs = new userdata('webmaster_pref');

############################################################################
### DETERMINE WHAT DIRECTORY THE APPLICATION IS RUNNING IN ON THE SERVER ###
############################################################################

$directory = $_SESSION['docroot_path'];

if (is_dir($directory)) {
   $handle = opendir("$directory");
   while ($files = readdir($handle)) {
      $this_file = $files;
      $files = $directory . "/$files";
      if (is_dir($files) && strlen($files) > 2) {
         $tmp = opendir("$files");
         while ($test_files = readdir($tmp)) {
            if ($test_files == "version.php") {
               $INSTALL_DIR = $this_file;
            }
         }
      }
   }

   closedir($handle);

//	if (eregi($_SERVER["HTTP_HOST"], $_SESSION['this_ip']) || eregi($_SESSION['this_ip'], $_SERVER["HTTP_HOST"])) {
//		$_SESSION['this_ip'] = $_SERVER["HTTP_HOST"];
//		$this_ip = $_SERVER["HTTP_HOST"];
//	}


   if (isset($_SERVER['HTTPS']) & $_SERVER['HTTPS'] == 'on' ) {
      $https = "on";
      } else { $https = "off";
   }

   if ( $https == "on" ) {
      $submit_form = "https://".$_SESSION['this_ip']."/sohoadmin/".basename($_SERVER['PHP_SELF']);
   } else {
      $submit_form = "http://".$_SESSION['this_ip']."/sohoadmin/".basename($_SERVER['PHP_SELF']);
   }
}

if ( $_POST['jubjub'] == "DownloadVBS" ) {
   $docroot = str_replace(basename(__FILE__), "", __FILE__);
	 $docfolder = eregi_replace("sohoadmin/includes/", '', $docroot);
   $VBSfilepath = $_SESSION['docroot_path']."/sohoadmin/config/ResolveDomain.vbs";
   $filePathvbs = $docroot."ResolveDomain.vbs";
   $fileSize = filesize($VBSfilepath);
   if ( strstr($HTTP_USER_AGENT, "MSIE") ) {
      $attachment = "";
   } else {
      $attachment = "attachment;";
   }

	header("Content-Description: File Download");
	header("Content-Length: $fileSize");
	header("Content-Type: application/force-download");
	header("Content-Disposition: $attachment; filename=\"resolvedomain.vbs\"");
	echo file_get_contents($VBSfilepath); exit;

}

# Email password to default email address
if ( $_GET['todo'] == "send_password" && $webmaster_prefs->get("forgotpw") != "no" ) {

   # Pull password from db
   $qry = "SELECT username, password FROM login WHERE last_name = 'WEBMASTER'";
   $rez = mysql_query($qry);
   $getLogin = mysql_fetch_array($rez);
   $my_email = db_fetch("site_specs", "df_email");

   # Build forgot my password email

   $from_addr = "From: noreply@".eregi_replace('^www\.', '', $_SESSION['this_ip'])."\n";
   $email_header = "";
   $email_header .= "Content-Type: text/html; charset=us-ascii;\n";
   $email_header .= "Content-Transfer-Encoding: 7bit\n";
   $email_header .= "Content-Disposition: inline;\n\n";

   $email_msg = "".lang("You are receiving this message because somebody (presumably yourself) clicked the 'Email my login info to me' link")."\n";
   $email_msg .= "".lang("on the sitebuilder login screen for your website")." (".$_SESSION['this_ip'].").<br/><br/>";

   $email_msg .= "".lang("Your login information is")."...<br/>\n";
   $email_msg .= "".lang("USERNAME").": <b>".$getLogin['username']."</b><br/>\n";
   $email_msg .= "".lang("PASSWORD").": <b>".$getLogin['password']."</b><br/>\n";


   # Send email now
   if ( mail($my_email, lang("Site builder login info"), $email_msg, $from_addr.$email_header) ) {
      $msg = lang("Your login info has been emailed to you.")."\n";
      $msg .= "<p><b>".lang("Note").":</b> ".lang("The email was to the email address specified in Webmaster > Global Settings,")." \n";
      $msg .= "".lang("which you can get to (after you log-in) by clicking on the 'Webmaster' button in the upper nav bar.")."</p>\n";
   }

} // End if todo = send_password

?>
<html>
<head>
<title><? echo $_SESSION['this_ip']; ?>: Log-in to manage your website</title>
<meta http-equiv="Content-Type" CONTENT="text/html; charset=iso-8859-1"/>
<script language="javascript">
<?

if ($keystroke_login == "on") {

	echo "	var width = (screen.width);
			var height = (screen.height - 25);
			var centerleft = 0;
			var centertop = 0;
			var centerleft = (width/2) - (560/2);
			var centertop = (height/2) - (300/2);
			var width=560;
			var height=300;
			window.moveTo(centerleft,centertop);
			window.resizeTo(width, height);
			window.focus();\n\n";

} // End If

?>
</script>

<link rel="stylesheet" type="text/css" href="program/product_gui.css"/>

<style type="text/css">
body {
   background-image: url('skins/default/getlogin_bg_gradient.jpg');
}
ul {
	list-style: none;
	margin: -3px 2px 0px 0px;
	padding: 10px;
	padding: 0px;
}
ul li {
	padding: 3px 0px 6px 15px;
	background: url('skins/default/getlogin_bullet.gif') no-repeat left top;
}
</style>
</HEAD>
<?
if ( $_POST['resolve'] == "domain" )  {
	echo "<div style=\"z-index: 6; position: absolute; top: 6%; left: 29%; width: 300px; height: 180px; visibility: visible;\">";
	include('includes/modify_host.php');
	echo "</div>";
}

$DOMAIN = $_SESSION['this_ip'];
$IP = $_SERVER['SERVER_ADDR'];
$HOST = $IP."     ".$DOMAIN;
$host = php_uname(n);
$hostip = gethostbyname($host);
$TIP = gethostbyname($_SERVER['HTTP_HOST']);
$addr = gethostbyaddr($TIP);
$gethost = gethostbyname($addr);
$HostIPs = gethostbyname($_SERVER['HTTP_HOST']);

if ( $IP == $HostIPs) {
	$resolved = "yes";
} else {
	if ( $gethost == $hostip ) {
		$resolved = "yes";
	} else {
		$resolved = "no";
	}

}
?>
<body onload="document.login.PHP_AUTH_USER.focus()" >

<form name="resolvedomain" method="post" action="index.php">
 <input type="hidden" name="resolve" value="domain">
</form>

<form name="login" method="post" action="<?php echo $submit_form; ?>">
 <input type="hidden" name="process" value="1">
 <!---parent table-->
 <table cellpadding="0" cellspacing="0" border="0" style="margin-top: 20%;" align="center">
<?
# Any messages to display? -- (i.e. 'password has been emailed to you')
if ( isset($msg) ) {
   echo "  <tr>\n";
   echo "   <td class=\"center\">\n";
   echo "    <div class=\"bg_yellow\" style=\"padding: 10px;width: 429px; text-align: left; border: 1px solid #ccc; margin: 10px;\">\n";
   echo "     ".$msg."\n";
   echo "    </div>";;
   echo "   </td>\n";
   echo "  </tr>\n";
}

# ERROR: Invalid username/password
if ( $error == 1 ) {
   echo "  <tr>\n";
   echo "   <td class=\"red center\" style=\"padding: 10px;\">".lang("Invalid Username/Password. Please Try Again.")."</td>";
   echo "  </tr>\n";
}

?>
  <tr>
   <td align="center" valign="top">
<?
# Warning: it appears that this domain has not yet resolved to this server
//if ( $_POST['resolve'] != "domain" && $resolved == "no" ) {
//   echo "<span style=\"font-family: Arial; font-size: 8pt; color: red; text-decoration: blink;\">Warning: </span>it appears as though this Domain has not resolved to this server!<br><a href=javascript:document.resolvedomain.submit();>Click Here for details</a></font><br><br>";
//}
?>
    <!---actual login table-->
    <table border="0" cellspacing="0" cellpadding="8" align="center" bgcolor="#FFFFFF" style="border: 1px solid #2e2e2e;">

     <!---Header row-->
     <tr>
      <td colspan="2" class="fsub_title">
       <b><? echo lang("Please log-in to manage your website"); ?></b>
      </td>
     </tr>

     <tr>
      <!---cell: help/info links-->
      <td valign="top" width="170" style="background-color: #efefef; border-right: 1px dotted #ccc;">
       <ul id="getlogin_links">
        <li><a href="system_requirements.html" rel="nofollow"><? echo lang("System Requirements"); ?></a></li>
        <li><a href="help.html" rel="nofollow"><? echo lang("Browser settings"); ?></a></li>
        <!--- <li><a href="<?php echo $submit_form; ?>?todo=send_password"><? echo lang("Email my login info to me"); ?></a></li> -->
<?
# Show link for Add/ Remove DNS Entry?
if ( $resolved == "yes" ) {
   echo "       <li><a href=\"javascript:document.resolvedomain.submit();\" rel=\"nofollow\">".lang("Add/Remove DNS Entry")."</a></li>";
}

# Show "Email my login info to me" link?
if ( $webmaster_prefs->get("forgotpw") != "no" ) {
   echo "<li><a href=\"".$submit_form."?todo=send_password\" rel=\"nofollow\" class=\"red\">".lang("Email my login info to me")."</a></li>\n";
}
?>

       </ul>
      </td>

      <!---cell: login form-->
      <td align="center" valign="top">
       <table width="250" align="center" border="0" cellspacing="0" cellpadding="5">
        <tr>
         <td width="20%"><? echo lang("Username"); ?>:</td>
         <td valign="top"><input type="text" name="PHP_AUTH_USER" style="width: 150px;"/></td>
        </tr>

        <tr>
         <td><? echo lang("Password"); ?>:</td>
         <td valign="top"><input type="password" name="PHP_AUTH_PW" style="width: 150px;"/></td>
        </tr>

        <!---login button-->
        <tr>
         <td colspan="2" class="right"><input type="submit" style="cursor: hand;" value="<? echo lang("Login"); ?> &gt;&gt;"/></td>
        </tr>
       </table>
      </td>
     </tr>
    </table>

   </td>
  </tr>
 </table>
</form>
</body>
</html>