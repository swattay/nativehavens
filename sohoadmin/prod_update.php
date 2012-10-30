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


// If user chooses not to update at this time, redirect back to webmaster page
// ----------------------------------------------------------------------------
if (isset($updateno)) {
	header("Location: program/webmaster/webmaster.php?=SID");
	exit;
}

?>

<HTML>
<HEAD>

<script language="JavaScript">
<!--

function killErrors() { 
		return true; 
	}
 
window.onerror = killErrors;

function MM_findObj(n, d) { //v3.0
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document); return x;
}

function MM_showHideLayers() { //v3.0
  var i,p,v,obj,args=MM_showHideLayers.arguments;
  for (i=0; i<(args.length-2); i+=3) if ((obj=MM_findObj(args[i]))!=null) { v=args[i+2];
    if (obj.style) { obj=obj.style; v=(v=='show')?'visible':(v='hide')?'hidden':v; }
    obj.visibility=v; }
}

function show_working() {
	MM_showHideLayers('working','','show');
}

//-->
</script>

</HEAD>
<BODY bgcolor=white color=black link=red alink=red vlink=red>

<!-- ============================================================ -->
<!-- ============= WORKING LAYER DISPLAY DIV ==================== -->
<!-- ============================================================ -->

<DIV ID="working" style="position:absolute; left:0px; top:opx; width:100%; height:100%; z-index:50; border: 2px none #000000; visibility: hidden; overflow: hidden"> 
  <table border=0 cellpadding=0 width=100% height=100% bgcolor=WHITE>
    <tr> 
      <td align=center valign=middle class=text> 
		<img src="icons/loading.gif" width=137 height=30 border=0>
		<BR clear=all><font color=blud face=Tahoma size=2><B>Getting Data from Soholaunch Server.<BR>This may take two to three minutes, please be patient.</b></font>
      </td>
    </tr>
  </table>
</DIV>

<!-- ============================================================ -->

<?php

// Setup Soholaunch Update Server Data
// ----------------------------------------------------------------------------
$ftp_server = "ftp.myadminlogin.com";
$ftp_user_name = "produpdate";
$ftp_user_pass = "95qwJC56df";


// Setup connection to FTP server
// ----------------------------------------------------------------------------
$conn_id = ftp_connect($ftp_server); 

// Login to Product Update using Username and Password
// ----------------------------------------------------------------------------
$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass); 

// Confirm FTP connection
// ----------------------------------------------------------------------------
if ((!$conn_id) || (!$login_result)) { 
	echo "<center><BR><h2>Connection to Soholaunch update server has failed.</H2>";
    echo "Please try again later.<CENTER>"; 
    echo "</body></html>";
	exit;
}

// Determine the OS that this product is running with
// ----------------------------------------------------------------------------
if ($WINDIR != "") { $OSTYPE = "Windows"; } else { $OSTYPE = "Linux"; }

// Determine if current running product is PE or OBS
// ----------------------------------------------------------------------------
if (file_exists("program/modules/mods_full/shopping_cart/products.php")) {
	$current_dir = "obs/sohoadmin";
} else {
	$current_dir = "pe/sohoadmin";
}

// Get Server Build Number and Compare to Update Server Build
// ----------------------------------------------------------------------------

$file = $current_dir."/build.dat.php";
$buff = ftp_mdtm($conn_id, $file);
if ($buff != -1) {
    $UPDATE_SERVER_BUILD = date ("ymd", $buff);
} else {
    echo "<center><BR><h2>Couldn't get needed build information from Soholaunch update server.<BR>Please try again later.</h2></center>";
	echo "</body></html>";
	exit;
}

// Challenge user for wholesale update or advanced programmer update
// ----------------------------------------------------------------------------

if (isset($updateyes)) {

	echo "<table border=0 cellpadding=0 cellspacing=0 width=100% height=100%><tr><td align=center valign=middle>\n";

	echo "<form method=post action=\"prod_update.php\">";
	echo "<h1><font face=Tahoma color=darkblue>Update Preference</font></h1><font face=Tahoma size=2 color=darkblue>Please select the type of update you wish to perform. A <U>wholesale update</U><BR>will update all program code to be current with Soholaunch servers, otherwise<BR>you may use the <U>advanced update</U> feature to select individual filenames to update.</b></font><BR><BR><font face=Tahoma size=2>Choose your update preference:<BR><BR>";
	echo "<input type=submit name=wholesaleUpdate value=\"Wholesale Update\" style='cursor: hand; font-family: Tahoma; font-size: 8pt;'>\n";
	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	echo "<input type=submit name=advancedUpdate value=\"Advanced Update\" onclick=\"show_working();\" style='cursor: hand; font-family: Tahoma; font-size: 8pt;'>\n";
	echo "</form>\n";

	echo "</td></tr></table>\n\n";

	echo "</body></html>";
	exit;	// Wait for next move
	
}

// Do Wholesale product update now
// ----------------------------------------------------------------------------

if (isset($wholesaleUpdate) || isset($advancedUpdate)) {

			// Build Directory Structure Array
			// ----------------------------------------------------------------------------
			
			if (eregi("obs", $current_dir)) {
			
				$display_remove = "obs/";
				
				$DIR_STRUCTURE .= "";

				$DIR_STRUCTURE .= "client_files;";
				$DIR_STRUCTURE .= "client_files/base_files;";
				$DIR_STRUCTURE .= "client_files/calendar;";
				$DIR_STRUCTURE .= "client_files/demo_includes;";
				$DIR_STRUCTURE .= "client_files/newsletter;";
				$DIR_STRUCTURE .= "client_files/photo_album;";
				$DIR_STRUCTURE .= "client_files/secure_login;";
				$DIR_STRUCTURE .= "client_files/shopping_cart;";
				$DIR_STRUCTURE .= "client_files/statistics;";
				
				$DIR_STRUCTURE .= "icons;";
				$DIR_STRUCTURE .= "includes;";
				$DIR_STRUCTURE .= "language;";
				
				$DIR_STRUCTURE .= "program;";
				$DIR_STRUCTURE .= "program/includes;";
				
				$DIR_STRUCTURE .= "program/modules;";
				$DIR_STRUCTURE .= "program/modules/data_files;";
				$DIR_STRUCTURE .= "program/modules/includes;";
				$DIR_STRUCTURE .= "program/modules/menu_system;";
				$DIR_STRUCTURE .= "program/modules/menu_system/includes;";
				
				$DIR_STRUCTURE .= "program/webmaster;";
				$DIR_STRUCTURE .= "program/webmaster/includes;";
				$DIR_STRUCTURE .= "program/webmaster/shared;";
				
				$DIR_STRUCTURE .= "program/wizard;";
				$DIR_STRUCTURE .= "program/wizard/content;";
				$DIR_STRUCTURE .= "program/wizard/includes;";
				$DIR_STRUCTURE .= "program/wizard/shared;";
				
				$DIR_STRUCTURE .= "program/modules/page_editor;";
				$DIR_STRUCTURE .= "program/modules/page_editor/client;";
				$DIR_STRUCTURE .= "program/modules/page_editor/data;";				
				$DIR_STRUCTURE .= "program/modules/page_editor/formlib;";
				$DIR_STRUCTURE .= "program/modules/page_editor/formlib/builder;";
				$DIR_STRUCTURE .= "program/modules/page_editor/formlib/forms;";
				$DIR_STRUCTURE .= "program/modules/page_editor/formlib/includes;";
				$DIR_STRUCTURE .= "program/modules/page_editor/formlib/newsletter;";				
				$DIR_STRUCTURE .= "program/modules/page_editor/images;";
				$DIR_STRUCTURE .= "program/modules/page_editor/includes;";
				$DIR_STRUCTURE .= "program/modules/page_editor/layers;";
				$DIR_STRUCTURE .= "program/modules/page_editor/obj;";
				$DIR_STRUCTURE .= "program/modules/page_editor/obj_bar_icons;";

				$DIR_STRUCTURE .= "program/modules/mods_full;";
								
				$DIR_STRUCTURE .= "program/modules/mods_full/database_manager;";
				$DIR_STRUCTURE .= "program/modules/mods_full/database_manager/includes;";
				$DIR_STRUCTURE .= "program/modules/mods_full/database_manager/shared;";

				$DIR_STRUCTURE .= "program/modules/mods_full/enewsletter;";
				$DIR_STRUCTURE .= "program/modules/mods_full/enewsletter/includes;";
				$DIR_STRUCTURE .= "program/modules/mods_full/enewsletter/shared;";
				
				$DIR_STRUCTURE .= "program/modules/mods_full/event_calendar;";
				$DIR_STRUCTURE .= "program/modules/mods_full/event_calendar/includes;";
				$DIR_STRUCTURE .= "program/modules/mods_full/event_calendar/shared;";
				
				$DIR_STRUCTURE .= "program/modules/mods_full/includes;";
				
				$DIR_STRUCTURE .= "program/modules/mods_full/photo_album;";
				$DIR_STRUCTURE .= "program/modules/mods_full/photo_album/includes;";
				$DIR_STRUCTURE .= "program/modules/mods_full/photo_album/shared;";
				
				$DIR_STRUCTURE .= "program/modules/mods_full/shared;";
				
				$DIR_STRUCTURE .= "program/modules/mods_full/shopping_cart;";
				$DIR_STRUCTURE .= "program/modules/mods_full/shopping_cart/images;";
				$DIR_STRUCTURE .= "program/modules/mods_full/shopping_cart/includes;";
				$DIR_STRUCTURE .= "program/modules/mods_full/shopping_cart/shared;";
				
				$DIR_STRUCTURE .= "program/modules/mods_full/statistics;";
				$DIR_STRUCTURE .= "program/modules/mods_full/statistics/includes;";
				$DIR_STRUCTURE .= "program/modules/mods_full/statistics/shared;";
				
			}
					
					
			// Build Filename Array's
			// ----------------------------------------------------------------------------
			
			$ADV_DISPLAY = "";
			$ADV_DISPLAY .= "<DIV ID=\"display_layer\" style=\"position:absolute; left:0px; top:8%; width:100%; height:92%; z-index:1; overflow: auto; border-top: 1px solid black\">\n\n";

			$ADV_DISPLAY .= "<table border=0 cellpadding=8 cellspacing=0 width=100% bgcolor=white><tr><td align=left valign=top>\n";
			$ADV_DISPLAY .= "<font color=darkblue face=Tahoma size=3>Select the individual files that you wish to update and then click the \"Update Now\" button above.</font><BR><BR>\n";
	
			$ADV_DISPLAY .= "\n\n<font color=red><TT><B>$current_dir</B></TT></font><BR>\n";
			
			// Create Individual File Download Array for End-User to Select
			// ----------------------------------------------------------------------------
			$contents = ftp_nlist($conn_id, $current_dir);
			$r = 0;
			foreach ($contents as $entry) {
				$r++;
				$PROD_FILE[$r] = $entry;
				$ADV_DISPLAY .= "<input type=checkbox name=\"$PROD_FILE[$r]\">&nbsp;<TT>$PROD_FILE[$r]</TT><BR>\n";
			}

			// Calculate rest of sub-directory structure and add to array
			// ----------------------------------------------------------------------------
			
			$DIRS = split(";", $DIR_STRUCTURE);
			$num_dirs = count($DIRS);
			$base_ftp_dir = $current_dir;
						
			for ($i=0;$i<=$num_dirs;$i++) {			
				if ($DIRS[$i] != "") {
					$current_dir = $base_ftp_dir."/".$DIRS[$i];
					$ADV_DISPLAY .= "\n\n<font color=red><TT><B>$current_dir</b></TT></font><BR>\n";
					$contents = ftp_nlist($conn_id, $current_dir);
					foreach ($contents as $entry) {
						$r++;
						$PROD_FILE[$r] = $entry;
						$highlight="black";
						$ADV_DISPLAY .= "<input type=checkbox name=\"$PROD_FILE[$r]\">&nbsp;<font color=$highlight><TT>$PROD_FILE[$r]</TT></font><BR>\n";
					}
				} // End If			
			} // End For

			$ADV_DISPLAY .= "</td></tr></table>\n\n";
			
			$usr_btn = "<input type=button value=\"Update Now\" style='cursor: hand; font-family: Tahoma; font-size: 9pt; width: 100px;'>&nbsp;&nbsp;&nbsp;&nbsp;<input type=button value=\"Cancel\" style='cursor: hand; font-family: Tahoma; font-size: 9pt; width: 100px;'>";
			$ADV_DISPLAY = "<table border=0 cellpadding=2 cellspacing=0 width=100% height=38 bgcolor=#e6e6e6><tr><td align=center valign=middle><font face=Tahoma size=2>Total Number of Files: $r</B></font></td><td align=center valign=middle>$usr_btn</td></tr></table>".$ADV_DISPLAY."<BR><BR>";
			$ADV_DISPLAY .= "</DIV>";
			
			
			// Determine Update Preference and Respond Accordingly
			// ---------------------------------------------------------------------------- 
			
			if (isset($advancedUpdate)) {
				$ADV_DISPLAY = eregi_replace("$display_remove", "", $ADV_DISPLAY);
				echo $ADV_DISPLAY;
			}
			
			// Download File
			// ----------------------------------------------------------------------------
			// if (ftp_get($conn_id, $local_file, $server_file, FTP_BINARY)) {
			//     echo "Successfully written to $local_file\n";
			// } else {
			//    echo "<font color=red>There was a problem</font>\n";
			// }
			
			
			echo "</body></html>";
			exit;
			
} // End Update Files (Step 1)

// Does this build need to be updated or not?
// ----------------------------------------------------------------------------

echo "<table border=0 cellpadding=0 cellspacing=0 width=100% height=100%><tr><td align=center valign=middle>\n";

if ($GLOBAL_BUILD_NUM == $UPDATE_SERVER_BUILD) {
	echo "<font face=Tahoma color=darkblue><h2>You have the latest version of<BR>the Soholaunch Software installed.</h2><BR><font size=2>Current Build Number: $UPDATE_SERVER_BUILD";
} else {
	echo "<form method=post action=\"prod_update.php\">";
	echo "<h1><font face=Tahoma color=darkblue>Soholaunch Update</font></h1><font face=Tahoma size=2 color=red><B>You are currently using build $GLOBAL_BUILD_NUM.<BR>There is a version $UPDATE_SERVER_BUILD available.</b></font><BR><BR><font face=Tahoma size=2>Would you like to update your product now?<BR><BR>";
	echo "<input type=submit name=updateyes value=\"Yes, Update Now\" style='cursor: hand; font-family: Tahoma; font-size: 8pt;'>\n";
	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	echo "<input type=submit name=updateno value=\"No, I will update later\" style='cursor: hand; font-family: Tahoma; font-size: 8pt;'>\n";
	echo "</form>\n";
}

echo "</td></tr></table>\n\n";
echo "</body></html>";
	
// Close FTP connection
// ----------------------------------------------------------------------------
ftp_close($conn_id); 
		
			
?>