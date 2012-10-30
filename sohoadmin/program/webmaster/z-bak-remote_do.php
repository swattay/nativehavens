<?php
error_reporting(E_PARSE);
if($_GET['_SESSION'] != '' || $_POST['_SESSION'] != '' || $_COOKIE['_SESSION'] != '') { exit; }
 



##########################################################################################################################################
## Soholaunch(R) Site Management Tool
## Version 4.7
##
## Homepage:	 	http://www.soholaunch.com
## Bug Reports: 	http://bugz.soholaunch.com
## Community:     http://forum.soholaunch.com
##########################################################################################################################################

##########################################################################################################################################
## COPYRIGHT NOTICE                                                     
## Copyright 1999-2005 Soholaunch.com, Inc.  All Rights Reserved.       
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
##########################################################################################################################################

# Primary interface include
$product_gui = $_SESSION['docroot_path']."/sohoadmin/program/includes/product_gui.php";
if ( !include($product_gui) ) {
   echo lang("Could not include this file").":<br>$product_gui";
   exit;
}

###:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
### NEW MODULE OBJECT    
###:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
$dMod = new feature_module("", "webmaster");


# Declare main html var
$disHTML = "";


if ( $do == "grabdb" ) {
   
   /// Open socket connection and send request
   #::--------------------------------------------------------------------------::
   $efit = new fsockit();
   $efit->dosoho($bakfile, $dotgz);
   $fsRez = $efit->sockput();
   
   echo "<b>Raw response....</b><br>";
   echo $fsRez['raw']."<br><br>";
   
   echo "<b>Line breaks....</b><br>";
   echo $fsRez['br']."<br><br>";
   
   echo "<b>Array of lines....</b><br>";
   foreach ( $fsRez['lines'] as $key=>$line ) {
      echo "$key. $line<br>";
   }
   exit;
   
   
   /// Show server response in popup div
   #::--------------------------------------------------------------------------::
   $nPop = new pop_div("drez");
   $nPop->boxtitle("Response from remote server");
   $nPop->boxclose("close window", "text");
   echo $nPop->mkpop("something");
   #::--------------------------------------------------------------------------::
   
   //echo js_alert("fsock probs in box??");
   
   // Show div layer now
   echo jscall("document.getelementbyid('drez').style.display='block';");
   exit;
   
}

/*---------------------------------------------------------------------------------------------*
 ___                   _           ___            _              _ 
| _ \ ___  _ __   ___ | |_  ___   / __| ___  _ _ | |_  _ _  ___ | |
|   // -_)| '  \ / _ \|  _|/ -_) | (__ / _ \| ' \|  _|| '_|/ _ \| |
|_|_\\___||_|_|_|\___/ \__|\___|  \___|\___/|_||_|\__||_|  \___/|_|
/*---------------------------------------------------------------------------------------------*/

//// Build hidden vars and form header
////-----------------------------------------
//$hVars = array( 'do'=>'grabdb' );
//$disHTML .= formtop("damntest", $_SERVER['PHP_SELF'], $hVars);
//
//$disHTML .= "<table width=\"100%\"  border=\"0\" cellpadding=\"4\" cellspacing=\"0\" class=\"feature_sub\">\n";
//$disHTML .= " <tr>\n";
//$disHTML .= "  <td colspan=\"4\" class=\"fsub_title\">Select target site to extract db dump</td>\n";
//$disHTML .= " </tr>\n";
//$disHTML .= " <tr>\n";
//$disHTML .= "  <td width=\"12%\">Target Site:</td>\n";
//$disHTML .= "  <td>\n";
//$disHTML .= "   <input name=\"bakfile\" type=\"text\" class=\"tfield\" style=\"width: 250px;\">\n";
//$disHTML .= "  </td>\n";
//$disHTML .= "  <td>\n";
//$disHTML .= "   TGZ?\n";
//$disHTML .= "   <input name=\"dotgz\" value=\"yes\" type=\"checkbox\" class=\"tfield\">\n";
//$disHTML .= "  </td>\n";
//$disHTML .= "  <td align=\"center\">\n";
//$disHTML .= "   <input type=\"button\" onclick=\"document.damntest.submit()\" value=\"Crazy Go Nuts Updates\" ".$nav_save.">\n";
//$disHTML .= "  </td>\n";
//$disHTML .= " </tr>\n";
//$disHTML .= "</table>\n";
//$disHTML .= "</form>\n";
//
//
//// Wrap form html in feature group table
//#:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
//$ttl = lang("Webmaster: Product Updates and Remote Controls");
//$dMod->add_fgroup($ttl, $disHTML);


/*---------------------------------------------------------------------------------------------*
 ___   _                _                      ___            _              _       
|   \ (_) _ _  ___  __ | |_  ___  _ _  _  _   / __| ___  _ _ | |_  ___  _ _ | |_  ___
| |) || || '_|/ -_)/ _||  _|/ _ \| '_|| || | | (__ / _ \| ' \|  _|/ -_)| ' \|  _|(_-<
|___/ |_||_|  \___|\__| \__|\___/|_|   \_, |  \___|\___/|_||_|\__|\___||_||_|\__|/__/                                       
                                       |__/  
/*---------------------------------------------------------------------------------------------*/

// Define local directory for product update files
//-------------------------------------------------------------------
$prod_local = $_SESSION['docroot_path']."/sohoadmin/program/webmaster/update_files";
if ( !is_dir($prod_local) ) { mkdir("update_files"); }

if ( $do == "download" ) {
   
   /// File download object
   ###:::::::::::::::::::::::::::::::::::::::::::::
   new remote_file($dlfile, 

if ( is_dir($prod_local) ) { $locFiles = dirlist($prod_local); }

/// List contents of LOCAL updates folder (downloaded files)
###====================================================================================
$disHTML = "<table width=\"100%\"  border=\"0\" cellpadding=\"4\" cellspacing=\"0\" class=\"feature_sub\">\n";
$disHTML .= " <tr>\n";
$disHTML .= "  <td colspan=\"4\" class=\"fsub_title\">Downloaded product files</td>\n";
$disHTML .= " </tr>\n";
$disHTML .= " <tr>\n";
$disHTML .= "  <td><b>".lang("File Name")."</b></td>\n";
$disHTML .= "  <td><b>".lang("File Size")."</b></td>\n";
$disHTML .= "  <td><b>".lang("Date Modified")."</b></td>\n";
$disHTML .= "  <td colspan=\"2\"><b>".lang("Permissions")."</b></td>\n";
$disHTML .= " </tr>\n";

foreach ( $locFiles['files'] as $dFile ) {
   $disHTML .= " <tr>\n";
   $disHTML .= "  <td>".$dFile['name']."</td>\n";
   $disHTML .= "  <td>".$dFile['size']."</td>\n";
   $disHTML .= "  <td>".$dFile['date']['mon']."-".$dFile['date']['mday']."-".$dFile['date']['year']."</td>\n";
   $disHTML .= "  <td>".$dFile['perms']."</td>\n";
   $disHTML .= " </tr>\n";
}

$disHTML .= "</table>\n";

$disHTML .= "<br><br>\n";


/// Call helper script to get contents of REMOTE folder (available updates)
###====================================================================================
$remAction = array( 'surfto'=>'dirlist', 'relative_path'=>'sohotemplates' );
$remLink = new fsockit("update.securexfer.net", "/process_remote.php", $remAction);
$remRez = $remLink->sockput();

//echo $remRez['br']."<hr>";

$remFiles = $remRez['restored'];


# Break apart server response by delimiters, put back in array, then output
#----------------------------------------------------------------------------------------
$disHTML .= "<table width=\"100%\"  border=\"0\" cellpadding=\"4\" cellspacing=\"0\" class=\"feature_sub\">\n";
$disHTML .= " <tr>\n";
$disHTML .= "  <td colspan=\"5\" class=\"fsub_title\">".lang("Available update files")."</td>\n";
$disHTML .= " </tr>\n";
$disHTML .= " <tr>\n";
$disHTML .= "  <td><b>".lang("File Name")."</b></td>\n";
$disHTML .= "  <td><b>".lang("File Size")."</b></td>\n";
$disHTML .= "  <td><b>".lang("Date Modified")."</b></td>\n";
$disHTML .= "  <td colspan=\"2\"><b>".lang("Perms")."</b></td>\n";
$disHTML .= " </tr>\n";

foreach ( $remFiles['files'] as $dFile ) {
   $disHTML .= " <tr>\n";
   $disHTML .= "  <td>".$dFile['name']."</td>\n";
   $disHTML .= "  <td>".$dFile['size']."</td>\n";
   $disHTML .= "  <td>".$dFile['date']['mon']."-".$dFile['date']['mday']."-".$dFile['date']['year']."</td>\n";
   $disHTML .= "  <td>".$dFile['perms']."</td>\n";
   $disHTML .= "  <td><a href=\"product_update.php?do=download&dlfile=".$dFile['path']."\">Download</a></td>\n";
   $disHTML .= " </tr>\n";
}

$disHTML .= "</table>\n";

// Wrap in feature group table
#:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
$ttl = lang("Webmaster: Update File Scan");
$dMod->add_fgroup($ttl, $disHTML);




###::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
### Output compiled module html!
###::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
echo $dMod->make_module();



?>