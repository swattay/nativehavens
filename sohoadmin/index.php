<?php
error_reporting(E_PARSE);
if($_GET['_SESSION'] != '' || $_POST['_SESSION'] != '' || $_COOKIE['_SESSION'] != '') { exit; }



// ======================================================================
// Note: IF YOU ARE SEEING THIS IN A BROWSER WINDOW WHEN TRYING TO ACCESS
// THE PRODUCT; YOU DO NOT HAVE PHP RUNNING! PLEASE INSTALL PHP AND TRY
// THIS PROGRAM AGAIN.
// =======================================================================
//
//
//
//
//
//
//
//
//
//
//
//

//
//
//
//
//
//
//
//
//
//

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


error_reporting(E_PARSE);

function iniTest($setting, $setval, $color = "") {
   $oldval = ini_get($setting);
   ini_set($setting, $setval);
   $newval = ini_get($setting);

   echo "<span style=\"color: #8b8b8b;\">".$setting." [<span style=\"color: #999;\">".$oldval."</span>]</span><br>";
   echo "<span style=\"color: #000;\">".$setting."</span> [<span style=\"color: red;\">".$newval."</span>]<br><br>";
}

session_start();

if (!file_exists("config/isp.conf.php")) {
	header("Location: setup.php");
	exit;
}


# Include core interface files!
if ( !include("program/includes/product_gui.php") ) {
   echo "\n\n\n\n <!---Could not include this file:<br>[".$product_gui."]----> \n\n\n\n";
}


error_reporting(E_PARSE);
#########################################################################################
###### DISPLAY LOADING SCREEN AND OPEN APPLICATION WINDOW.							      #####
#########################################################################################

if ($loginloop != "active") {
if(isset($_POST)){
	$getstring = '';
	foreach($_POST as $sesvar=>$sesval){
		if($sesvar != 'PHPSESSID'){
	    		$getstring .= $sesvar.'='.base64_encode($sesval).'&';
		}
	}
	$getstring = eregi_replace('&$', '', $getstring);
}
	echo "<html><head><title>SMT Login</title>\n";
	echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">\n";
	echo "<style>";
	echo " a:link { color: #339959; text-decoration: underline; }\n";
	echo " a:visited { color: #339959; text-decoration: underline; }\n";
	echo " a:hover { color: #66cc91; text-decoration: underline; }\n";
	echo " a:active { color: #66cc91; text-decoration: underline; }\n";
	echo "</style>";
	echo "</head>\n";



	echo "<script language=\"Javascript\">\n";
   echo "function SV2_openBrWindow(theURL,winName,features) {\n";
   echo "   window.open(theURL,winName,features);\n";
   echo "	}\n";

         echo "function loaderprg() {";
         # Allow mulitiple product windows!
         echo "   var winNum=Math.floor(Math.random()*11);\n";

         echo "   if(window.name == 'admin_dialog_content'){\n";
			echo "      window.location = 'version.php?loginloop=active".$getstring."' \n";
         echo "   }else{\n";
         # Open interface window

         echo "      window.open(\"http://".$_SESSION['this_ip']."/sohoadmin/version.php?$getstring\",\"interface\"+winNum+\"0\",\"location=no,status=no,menubar=no,scrollbars=no\");\n";
			echo "      var width = (screen.width);\n";
			echo "      var height = (screen.height - 25);\n";
			echo "      window.moveTo(0,0);\n";
			echo "      window.resizeTo(width, height);\n";
			echo "      var width = (screen.width);\n";
         echo "   }\n";

			if ($keystroke_login == "on") {
				echo "self.close();";
			}
	      echo "}\n";


	   echo "

		document.write(\"<body bgcolor=white text=black leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 onload='loaderprg();'>\");

		document.write(\"<table border=0 cellpadding=0 cellspacing=0 width='550' align='center' style='margin-top: 25%;font-family: Trebuchet MS, Tahoma, arial, helvetica, sans-serif;'>\");
		document.write(\" <tr>\");
		document.write(\"  <td colspan='2' align='center'><h2 style='font-weight: normal;color: #336699;'>Program Should Open in Separate Window</h2>\");
		document.write(\" </tr>\");
		document.write(\" <tr>\");
//		document.write(\"  <td><img src='icons/popup_blocker.gif'></td>\");
		document.write(\"  <td align=center valign=middle>\");
		document.write(\"   <font style='font-family: Tahoma, Verdana, Arial, Helvetica, Sans-serif; font-size: 14px;'>\");
		document.write(\"   <span style='color: #2E2E2E; font-size: 13px;'>\");
		document.write(\"   <b>Using a PopUp Blocker?</b>\");
		document.write(\"   Certain types of popup-blocker software may prevent program window from opening successfully.\");
		document.write(\"   If you are using a popup blocker, you may need to set it to allow popups from this website.</span><br>\");

		document.write(\"   <BR Clear=ALL>[ <a href='javascript:loaderprg();'>Click here to Re-launch the Program Window.</a> ]\");

		document.write(\"   </font>\");
		document.write(\"  </td>\");
		document.write(\" </tr>\");
		document.write(\"</table>\");
	";

	echo "</script>\n";
	echo "</body></html>\n";
	exit;

} // End Loader Sequence

########################################################################
### LOGIN ACCEPTED, PULL USER OPTIONS MENU 				  			 ###
########################################################################
//$SSL_CHECKOUT_LINK = 'http://".$_SESSION['this_ip']."/sohoadmin/version.php?PHPSESSID=".session_id();
//$SSL_VARIABLE."pgm-checkout.php?sid=".session_id();
header ("Location: http://".$_SESSION['this_ip']."/sohoadmin/version.php");
exit;

?>