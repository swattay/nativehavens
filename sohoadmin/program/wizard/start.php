<?php
error_reporting('E_ALL');
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

# Plugin install/misc functions (hook_attach, hook_special, etc)
include_once($_SESSION['docroot_path']."/sohoadmin/program/webmaster/plugin_manager/plugin_functions.php");


//error_reporting(E_ALL);

# PROCESS: Install template
if ( $_REQUEST['todo'] == "install_template" ) {
   if(!include("browse_templates/install_template.inc.php")){
      echo "<b>".lang("Cannot include")." install_template.inc.php!</b><br/>\n";
   }
   
//   foreach($report as $var=>$val){
//      echo "var = (".$var.") val = (".$val.")<br>\n";
//   } 
}


# INSTALL TEMPLATE -- orig in-file attempt...since moved to install_template.php
if ( $_REQUEST['install_template'] != "" ) {
   $addons_api = addons_api($_GET['install_template']);
   $plugins_dir_path = $_SESSION['docroot_path']."/sohoadmin/program/modules/site_templates/pages/";
   $zipfile_name = $addons_api['zipfile_name'];
   $download_url = $addons_api['zipfile_url']."&update_domain=".$_SESSION['this_ip'];
   $downloaded_buildfile = $plugins_dir_path.$zipfile_name;

   # Download update file now!
   if ( $errorcode == "" ) {
      $dlUpdate = new file_download($download_url, $downloaded_buildfile);

      if ( !file_exists($downloaded_buildfile) ) {
         if ( ini_get('allow_url_fopen') != "1" ) {
            $errorcode = "cannotupdate-url_fopen";

         } else {
            $errorcode = "cannotupdate-nowrite";
         }
      }
   }
}
error_reporting(0);

//error_reporting(E_ALL);

/*###########################################################################*
  _____                    _           _____  _  _         _  _
 / ____|                  | |         / ____|(_)| |       | || |
| |      _ __  ___   __ _ | |_  ___  | (___   _ | |_  ___ | || |
| |     | '__|/ _ \ / _` || __|/ _ \  \___ \ | || __|/ _ \| || |
| |____ | |  |  __/| (_| || |_|  __/  ____) || || |_|  __/|_||_|
 \_____||_|   \___| \__,_| \__|\___| |_____/ |_| \__|\___|(_)(_)

/*###########################################################################*/

if ($_REQUEST['WIZSTEP'] == "Finish Wizard") {

	$HIDDEN_VARS = "";
	$s = 0;	// Active Var Flag

	// Clear all existing pages except Home Page
	// ---------------------------------------------------------------------------------
	$result = mysql_query("DELETE FROM site_pages WHERE page_name != 'Home Page'");

	# Make sure home page appears at top of auto menu
	mysql_query("UPDATE site_pages SET main_menu = '1' WHERE page_name = 'Home Page'");

	$pageCount = 2;	// Reset Menu Page Counter

	// Loop Through all passed variable data and "Get her done"
	// ---------------------------------------------------------------------------------

   $site_title = stripslashes(trim($_REQUEST['WEBSITE_TITLE']));

   // Update Template Header in site_specs
   mysql_query("UPDATE site_specs SET df_hdrtxt = '$site_title'");

	// Site Title
	$filename = "$cgi_bin/meta.conf";
	$file = fopen("$filename", "w");
		fwrite($file, "site_description=".$site_title."\n");
		fwrite($file, "site_keywords=".$site_title."\n");
		fwrite($file, "site_title=".$site_title." Website\n");
		fwrite($file, "splash_bg=FFFFFF\n");
	fclose($file);

	// Menu Conf
	$filename = "$cgi_bin/menu.conf";
	$file = fopen("$filename", "w");
		fwrite($file, "mainmenu=vertical\n");
		fwrite($file, "submenu=veritical\n");
		fwrite($file, "locationbar=\n");
		fwrite($file, "textmenu=on\n");
		fwrite($file, "MENUTYPE=textlink\n");
	fclose($file);

   $site_email = stripslashes(trim($_REQUEST['WEBSITE_EMAIL']));
   
	// Save Email Address to site_specs table
   mysql_query("UPDATE site_specs SET df_email = '$site_email'");

   $site_template = stripslashes(trim($_REQUEST['TEMPLATE']));

   // Set Base Template File Now

	// Kill current page-specific template assignments
	$pg_tmps = $_SESSION['docroot_path']."/media/page_templates.txt";
	if ( file_exists($pg_tmps) ) { @unlink($pg_tmps); }

	// Kill current base template assignment
	$template_dir = $_SESSION['docroot_path']."/template";
	$directory = $template_dir;
	if (is_dir($directory)) {
		$handle = opendir("$directory");
		while ($files = readdir($handle)) {
			if (strlen($files) > 2) {
				$deleteit = $directory."/".$files;
				@unlink($deleteit);
			}
		}
		closedir($handle);
	}

	// Now right template.conf file so that we can load the current
	// template from memory next time user enters "Site Templates" option

	$filename = $template_dir . "/template.conf";
	$file = fopen("$filename", "w");
		fwrite($file, "$site_template");
	fclose($file);
	
	## Business Info
   $address1 = stripslashes(trim($_REQUEST['df_address1']));
   $city = stripslashes(trim($_REQUEST['df_city']));
   $state = stripslashes(trim($_REQUEST['df_state']));
   $zip = stripslashes(trim($_REQUEST['df_zip']));
   $country = stripslashes(trim($_REQUEST['df_country']));
   $phone = stripslashes(trim($_REQUEST['df_phone']));
   $copyright = stripslashes(trim($_REQUEST['copyright']));
   $company = stripslashes(trim($_REQUEST['df_company']));
   $insThis = "df_address1 = '$address1', df_city = '$city', df_state = '$state', df_zip = '$zip',";
   $insThis .= "df_country = '$country', df_phone = '$phone', copyright = '$copyright', df_company = '$company'";
   mysql_query("UPDATE site_specs SET $insThis");


	####################################################################################
	// Publish New "Page" Now!
	####################################################################################
	
   reset($_POST);
   while (list($name, $value) = each($_POST)) {
      
      $value = stripslashes(trim($value));
      
   	if ($value == 1) {
         $CONTENT = "";
   
         ## Create "Content" text file for display on site
         ###########################################################
         $from_file = "content/".$name.".con";
         $to_file = $cgi_bin."/".$name.".con";
   
         if ( eregi("IIS", $_SERVER['SERVER_SOFTWARE']) ) {  // Correct File Path For Win IIS Systems
            $from_file = eregi_replace("/", "\\", $from_file);
            $to_file = eregi_replace("/", "\\", $to_file);
         }
   
         // Read generic content file into memory
         // -------------------------------------------------
         $file = fopen("$from_file", "r");
            $dContent = fread($file,filesize($from_file));
         fclose($file);
   
         // Personalize generic content
         // ----------------------------------
         $dContent = eregi_replace("#COMPANY#", "$site_title", $dContent);
         $dContent = eregi_replace("#WIZDOMAIN#", "$this_ip", $dContent);
         $dContent = eregi_replace("#WIZEMAIL#", "$site_email", $dContent);
         $dContent = eregi_replace("#WIZROOT#", $_SESSION['docroot_path'], $dContent);
   
         // Write personalized content file
         $file = fopen("$to_file", "w");
            fwrite($file, "$dContent");
         fclose($file);
   
   
         ## Create Page Editor regen file
         ###########################################################
         $CONTENT = "";
         $from_file = "content/".$name.".regen";
         $to_file = $cgi_bin."/".$name.".regen";
   
         if (eregi("IIS", $_SERVER['SERVER_SOFTWARE'])) {  // Correct File Path For Win IIS Systems
            $from_file = eregi_replace("/", "\\", $from_file);
            $to_file = eregi_replace("/", "\\", $to_file);
         }
   
         // Read generic content file into memory
         // -------------------------------------------------
         $file = fopen("$from_file", "r");
            $dContent = fread($file,filesize($from_file));
         fclose($file);
   
         // Personalize generic content
         // ----------------------------------
         $dContent = eregi_replace("#COMPANY#", "$site_title", $dContent);
         $dContent = eregi_replace("#WIZDOMAIN#", "$this_ip", $dContent);
         $dContent = eregi_replace("#WIZEMAIL#", "$site_title", $dContent);
         $dContent = eregi_replace("#WIZROOT#", $_SESSION['docroot_path'], $dContent);
   
         // Write personalized content file
         $file = fopen("$to_file", "w");
            fwrite($file, "$dContent");
         fclose($file);
   
   
         ## Now Database Page Names and Place them on Menu
         ####################################################################################
         $pageName = eregi_replace("_", " ", $name);
         $pageCount++;
         $link = md5($name);
         $SQL_INSERT = "VALUES('$pageName','Main','', '', '$pageCount','$link','','','','','','')";
         mysql_query("INSERT INTO site_pages $SQL_INSERT");
   
         $pageCount++;  // Make Home Page appear on menu system as "Last" in order
   
   	} // End Publish Page
   }

	$home_from = "content/Home_Page.con";
	$home_to = $cgi_bin."/Home_Page.con";
	copy($home_from, $home_to);

	$home_from = "content/Home_Page.regen";
	$home_to = $cgi_bin."/Home_Page.regen";
	copy($home_from, $home_to);

	# Turn off Wizard for next login (make sure filebin is writeable)
	if ( !is_writeable("../../filebin") ) { cam_perm_fix("../../filebin"); }
	$filename = "../../filebin/nowiz.txt";
	$file = fopen($filename, "w");
		fwrite($file, "wizardoff");
	fclose($file);

	header("Location: ../main_menu.php?wiz=1");
	exit;

} // End Final Step

###############################################################################
## Bi-Pass Wizard and Go Directly to Main Menu
###############################################################################

if ($WIZSTEP == "Skip Wizard (Advanced)") {

	// Write nowiz.txt to sohoadmin root
	// ---------------------------------------
		$filename = "../../filebin/nowiz.txt";
		$file = fopen("$filename", "w");
			fwrite($file, "wizardoff");
		fclose($file);

	// redirect to main menu now and exit wizard
	// ---------------------------------------
		header("Location: ../main_menu.php?=SID");
		exit;
} // End Main Menu Step

###############################################################################
## Start Wizard Step Through
###############################################################################

# Start buffering output
ob_start();
?>


<script language="JavaScript">
<!--
function SV2_findObj(n, d) {
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=SV2_findObj(n,d.layers[i].document); return x;
}
function SV2_showHideLayers() {
  var i,p,v,obj,args=SV2_showHideLayers.arguments;
  for (i=0; i<(args.length-2); i+=3) if ((obj=SV2_findObj(args[i]))!=null) { v=args[i+2];
    if (obj.style) { obj=obj.style; v=(v=='show')?'visible':(v='hide')?'hidden':v; }
    obj.visibility=v; }
}
function SV2_popupMsg(msg) {
  alert(msg);
}
function SV2_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}

SV2_showHideLayers('MAIN_MENU_LAYER?header','','hide');		// HIDE MAIN MENU FOR WIZARD
SV2_showHideLayers('PAGE_EDITOR_LAYER?header','','hide');	// HIDE ALL OTHER HEADER MENUS FOR NOW
SV2_showHideLayers('CART_MENU_LAYER?header','','hide');
SV2_showHideLayers('DATABASE_LAYER?header','','hide');
SV2_showHideLayers('NEWSLETTER_LAYER?header','','hide');
SV2_showHideLayers('CALENDAR_MENU_LAYER?header','','hide');

//-->
</script>


<?
###############################################################################
/* __          __  _
   \ \        / / | |
    \ \  /\  / /__| | ___ ___  _ __ ___   ___
     \ \/  \/ / _ \ |/ __/ _ \| '_ ` _ \ / _ \
      \  /\  /  __/ | (_| (_) | | | | | |  __/
       \/  \/ \___|_|\___\___/|_| |_| |_|\___|

*/## START UP SCREEN - WIZSTEP HAS NOT BEEN DEFINED YET
###############################################################################

if ($WIZSTEP == "" || isset($CANCEL)) {
   
   # Define module heading text and instructions for this step
   $heading_text = lang("Web Site Wizard");
   $instructions = lang("The Web Site Wizard helps you choose the right web site design template, content pages, and other specific details relating to the look, feel, and basic operation of your web site.");

   $WIZSTEP = ""; // Just in case

   # Alert if running in live demo site mode
   if ( $_SESSION['demo_site'] == "yes" ) {
      # Build popup
      $popup = "<p><b>".lang("Note").":</b> This is only a one-time demo site. \n";
      $popup .= "<b>All site data will be erased</b> once you logout or close this window.</p>\n";

      $popup .= "<h2>Saving your progress</h2>\n";
      $popup .= "<p style=\"margin-top: 0;\">If at any point you'd like to save your progress, go to the <b>Site Backup/Restore</b> feature \n";
      $popup .= "(click the icon on the Main Menu...which you're about to see after you run through the QuickStart Wizard),\n";
      $popup .= "<b>create a backup</b>, and <b>download it</b> to your PC (or Mac).</p>\n";

      $popup .= "<p>Return later, <b>upload your backup, and your site is back</b>! \n";
      $popup .= "Same goes for when you get a copy of ".$_SESSION['hostco']['sitebuilder_name']." running on your own website (domain name) ---\n";
      $popup .= "just upload the backup file you created here and <b>pick up where you left off.</b></p>\n";

      $popup .= "<h2>Click the big red bar below to close this popup and start building your website!</h2><br/>\n";

      echo help_popup("popup-demo_site", "Welcome to your private demo site", $popup, "top: 15%;left: 15%;width: 550px;background-color: #f8f9fd;");

      # Show demo site reset warning popup now
      echo "<script language=\"javascript\">\n";
      echo "showid('popup-demo_site');\n";
//      echo "alert('NOTE: ".lang("This is only a one-time demo site.")."\\n\\n".lang("All site data will be erased once you logout or close this window.")."');\n";
      echo "</script>\n";
   }

?>
	<form name="STARTWIZ" method="post" action="start.php">
      <table width="95%" border="0" align="center" cellpadding="5" cellspacing="0">

         <tr align="center" valign="middle">
            <td colspan="2">

      		  <table width="95%" border=0 align=center cellpadding=4 cellspacing=0 class=text style="font-size: 12px;">
      		   <tr>
      		    <td>
                  <b><? echo lang("Follow these simple steps to build your website"); ?>:</b><br>
      
                  <table border="0" cellpadding="8" cellspacing="2" width="100%" class="text" style="font-size: 12px;">
                   <tr>
                    <td align="right"><img src="wiz-step_1.gif" border="0"></td>
                    <td><? echo lang("Choose a template for your website"); ?>.</td>
                   </tr>
                   <tr>
                    <td align="right"><img src="wiz-step_2.gif" border="0"></td>
                    <td><? echo lang("Choose some pages that you would like on your website"); ?>.</td>
                   </tr>
                   <tr>
                    <td align="right"><img src="wiz-step_3.gif" border="0"></td>
                    <td><? echo lang("Enter your site title and email address and business information") ?>.</td>
                   </tr>
                   <tr>
                    <td align="right"><img src="wiz-step_4.gif" border="0"></td>
                    <td><? echo lang("Preview &amp; Edit your page content"); ?>!</td>
                   </tr>
                  </table>
      
      			   </td>
      			   <td>
      		         <img src="wizard_start.gif" width="150" height="150" align="right" border="0">
      		      </td>
      			   </tr>
      			</table>

			   </td>
         </tr>
        <tr>
          <td align="center" valign="middle">&nbsp;</td>
          <td align="center" valign="middle">&nbsp;</td>
        </tr>
        <tr>
          <td align="center" valign="middle"><input type="submit" name="WIZSTEP" value="<? echo lang("Skip Wizard")." (Advanced)"; ?>" class="btn_delete" onMouseover="this.className='btn_deleteon';" onMouseout="this.className='btn_delete';" style="width: 200px;"></td>
          <td align="center" valign="middle"><input type="submit" name="WIZSTEP" value="<? echo lang("Start Wizard"); ?>" class="btn_save" onMouseover="this.className='btn_saveon';" onMouseout="this.className='btn_save';" style="width: 200px;"></td>
        </tr>
		<tr>
          <td align="center" valign="middle">&nbsp;</td>
          <td align="center" valign="middle">&nbsp;</td>
        </tr>
      </table>
    </form>

<?
} // End WizStep = ""


/*---------------------------------------------------------------------------------------------------------*
     _____ _               __
    / ____| |             /_ |
   | (___ | |_ ___ _ __    | |
    \___ \| __/ _ \ '_ \   | |
    ____) | ||  __/ |_) |  | |
   |_____/ \__\___| .__/   |_|
                  | |
                  |_|
# Select a Web Site Template
/*---------------------------------------------------------------------------------------------------------*/
if ($WIZSTEP == "Start Wizard") {
   
	// Clear all existing pages except Home Page
	// ---------------------------------------------------------------------------------
	$result = mysql_query("DELETE FROM site_pages WHERE page_name != 'Home Page'");

	## Make sure home page appears at top of auto menu
	mysql_query("UPDATE site_pages SET main_menu = '1' WHERE page_name = 'Home Page'");
   
   // Update Template Header in site_specs
   mysql_query("UPDATE site_specs SET df_hdrtxt = 'My Website'");
   
   ## Create pages for viewing through wizard
   ## We will kill these once the wizard is complete
   
   $pages_to_add = array(2 => "About", 3 => "Products", 4 => "Shopping", 5 => "Company Info");
   foreach($pages_to_add as $num => $name){
      
      $link = md5($name);
      //echo "num(".$num.") = name(".$name.")<br/>\n";
      $SQL_INSERT = "VALUES('$name','Main','', '', '$num','$link','','','','','','')";
      mysql_query("INSERT INTO site_pages $SQL_INSERT");
      
      $name = eregi_replace(" ", "_", $name);
      
      $CONTENT = "";

      ## Create "Content" text file for display on site
      ###########################################################
      $from_file = "content/".$name.".con";
      $to_file = $cgi_bin."/".$name.".con";

      if ( eregi("IIS", $_SERVER['SERVER_SOFTWARE']) ) {  // Correct File Path For Win IIS Systems
         $from_file = eregi_replace("/", "\\", $from_file);
         $to_file = eregi_replace("/", "\\", $to_file);
      }

      // Read generic content file into memory
      // -------------------------------------------------
      $file = fopen("$from_file", "r");
         $dContent = fread($file,filesize($from_file));
      fclose($file);

      // Personalize generic content
      // ----------------------------------
      $dContent = eregi_replace("#COMPANY#", "My Company", $dContent);
      $dContent = eregi_replace("#WIZDOMAIN#", "$this_ip", $dContent);
      $dContent = eregi_replace("#WIZEMAIL#", "wiz@$this_ip", $dContent);
      $dContent = eregi_replace("#WIZROOT#", $_SESSION['docroot_path'], $dContent);

      // Write personalized content file
      $file = fopen("$to_file", "w");
         fwrite($file, "$dContent");
      fclose($file);


      ## Create Page Editor regen file
      ###########################################################
      $CONTENT = "";
      $from_file = "content/".$name.".regen";
      $to_file = $cgi_bin."/".$name.".regen";

      if (eregi("IIS", $_SERVER['SERVER_SOFTWARE'])) {  // Correct File Path For Win IIS Systems
         $from_file = eregi_replace("/", "\\", $from_file);
         $to_file = eregi_replace("/", "\\", $to_file);
      }

      // Read generic content file into memory
      // -------------------------------------------------
      $file = fopen("$from_file", "r");
         $dContent = fread($file,filesize($from_file));
      fclose($file);

      // Personalize generic content
      // ----------------------------------
      $dContent = eregi_replace("#COMPANY#", "$site_title", $dContent);
      $dContent = eregi_replace("#WIZDOMAIN#", "$this_ip", $dContent);
      $dContent = eregi_replace("#WIZEMAIL#", "$site_title", $dContent);
      $dContent = eregi_replace("#WIZROOT#", $_SESSION['docroot_path'], $dContent);

      // Write personalized content file
      $file = fopen("$to_file", "w");
         fwrite($file, "$dContent");
      fclose($file);
     
   }
   
	$home_from = "content/Home_Page.con";
	$home_to = $cgi_bin."/Home_Page.con";
	copy($home_from, $home_to);

	$home_from = "content/Home_Page.regen";
	$home_to = $cgi_bin."/Home_Page.regen";
	copy($home_from, $home_to);
   
   
   # Define module heading text and instructions for this step
   $heading_text = lang("Step 1: Select a template");
   $instructions = lang("Below is a list of website layouts for you to choose from.  Browse through the layout options and click on the template that fits your site best.  Clicking on a template will display more information about it and allow you to preview it.");

   include("browse_templates/browse_templates.php");

   //echo "<input type=\"submit\" name=\"CANCEL\" value=\"Cancel Wizard\" CLASS=\"FormLt1\" STYLE=\"width: 150px; background: maroon; border: inset darkred 2px;\">\n";

} // End "Start Wizard"


###############################################################################
/*   _____ _               ___
    / ____| |             |__ \
   | (___ | |_ ___ _ __      ) |
    \___ \| __/ _ \ '_ \    / /
    ____) | ||  __/ |_) |  / /_
   |_____/ \__\___| .__/  |____|
                  | |
                  |_|
*/## Select Website Pages
###############################################################################
if ($_REQUEST['WIZSTEP'] == "step2") {
   
   # Define module heading text and instructions for this step
   $heading_text = lang("Step 2: Select Site Pages");
   $instructions = lang("Now choose the pages you wish to have within your website by placing a check next to your choices. Please note that your &quot;Home Page&quot; is automatically created for you. When done, proceed to the next step below.");

?>

	  <FORM NAME="step2form" METHOD="post" ACTION="start.php">
	  <INPUT TYPE=HIDDEN NAME=TEMPLATE VALUE="<? echo $_REQUEST['TEMPLATE']; ?>">
	  <input type="hidden" name="Home_Page" value="1">
	  <INPUT TYPE=HIDDEN NAME="START_FLAG" VALUE="1">

      <table width="100%" border="0" align="center" cellpadding="5" cellspacing="0" class="text" style="">
        <tr>
          <td colspan="2" align="center" valign="middle"><table width="100%" border="0" align="center" cellpadding="4" cellspacing="0" class="text" style="font-size: 11px;">
              <tr>
                <td align="center" valign="top" style="padding-top: 10px;"><? echo "<img src=\"../modules/site_templates/pages/".$_REQUEST['TEMPLATE']."/screenshot.jpg\" WIDTH=200 HEIGHT=137 BORDER=0 ALIGN=ABSMIDDLE style=\"border: 2px solid black;\">"; ?><br>
                  <em><strong><? echo lang("Selected Template"); ?></strong></em></td>

                <td align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="3" class="text" style="font-size: 11px;">
                    <tr>
                      <td><input name="About" type="checkbox" id="About" value="1">
                        <label for="About"><? echo lang("About"); ?></label></td>
                      <td><input name="About_Us" type="checkbox" id="About_Us" value="1">
                        <label for="About_Us"><? echo lang("About Us"); ?></label></td>
                      <td><input name="Careers" type="checkbox" id="Careers" value="1">
                        <label for="Careers"><? echo lang("Careers"); ?></label></td>
                    </tr>
                    <tr>
                      <td><input name="Case_Studies" type="checkbox" id="Case_Studies" value="1">
                        <label for="Case_Studies"><? echo lang("Case Studies"); ?></label></td>
                      <td><input name="Clients" type="checkbox" id="Clients" value="1">
                        <label for="Clients"><? echo lang("Clients"); ?></label></td>
                      <td><input name="Company" type="checkbox" id="Company" value="1">
                        <label for="Company"><? echo lang("Company"); ?></label></td>
                    </tr>
                    <tr>
                      <td><input name="Company_Info" type="checkbox" id="Company_Info" value="1">
                        <label for="Company_Info"><? echo lang("Company Info"); ?></label></td>
                      <td><input name="Contact" type="checkbox" id="Contact" value="1">
                        <label for="Contact"><? echo lang("Contact"); ?></label></td>
                      <td><input name="Contact_Us" type="checkbox" id="Contact_Us" value="1">
                        <label for="Contact_Us"><? echo lang("Contact Us"); ?></label></td>
                    </tr>
                    <tr>
                      <td><input name="Customers" type="checkbox" id="Customers" value="1">
                        <label for="Customers"><? echo lang("Customers"); ?></label></td>
                      <td><input name="Customers_List" type="checkbox" id="Customers_List" value="1">
                        <label for="Customers_List"><? echo lang("Customers List"); ?></label></td>
                      <td><input name="Directions" type="checkbox" id="Directions" value="1">
                        <label for="Directions"><? echo lang("Directions"); ?></label></td>
                    </tr>
                    <tr>
                      <td><input name="Documents" type="checkbox" id="Documents" value="1">
                        <label for="Documents"><? echo lang("Documents"); ?></label></td>
                      <td><input name="Downloads" type="checkbox" id="Downloads" value="1">
                        <label for="Downloads"><? echo lang("Downloads"); ?></label></td>
                      <td><input name="Events" type="checkbox" id="Events" value="1">
                        <label for="Events"><? echo lang("Events"); ?></label></td>
                    </tr>
                    <tr>
                      <td><input name="Jobs" type="checkbox" id="Jobs" value="1">
                        <label for="Jobs"><? echo lang("Jobs"); ?></label></td>
                      <td><input name="News" type="checkbox" id="News" value="1">
                        <label for="News"><? echo lang("News"); ?></label></td>
                      <td><input name="Newsletter" type="checkbox" id="Newsletter" value="1">
                        <label for="Newsletter"><? echo lang("Newsletter"); ?></label></td>
                    </tr>
                    <tr>
                      <td><input name="Our_Mission" type="checkbox" id="Our_Mission" value="1">
                        <label for="Our_Mission"><? echo lang("Our Mission"); ?></label></td>
                      <td><input name="Partners" type="checkbox" id="Partners" value="1">
                        <label for="Partners"><? echo lang("Partners"); ?></label></td>
                      <td><input name="Products" type="checkbox" id="Products" value="1">
                        <label for="Products"><? echo lang("Products"); ?></label></td>
                    </tr>
                    <tr>
                      <td><input name="Services" type="checkbox" id="Services" value="1">
                        <label for="Services"><? echo lang("Services"); ?></label></td>
                      <td><input name="Shop_Now" type="checkbox" id="Shop_Now" value="1">
                        <label for="Shop_Now"><? echo lang("Shop Now"); ?></label></td>
                      <td><input name="Support" type="checkbox" id="Support" value="1">
                        <label for="Support"><? echo lang("Support"); ?></label></td>
                    </tr>
                    <tr>
                      <td>
                       <input name="Thank_You" type="checkbox" id="Thank_You" value="1">
                       <label for="Thank_You"><? echo lang("Thank You"); ?></label>
                      </td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                    </tr>
                    <tr align="center">
                      <td colspan="3"><font color="#666666">
                        <em>* <? echo lang("You can rename these pages once created or"); ?> <br>
                        <? echo lang("add as many pages as you wish from within the product"); ?>.</em></font></td>
                    </tr>
                  </table></td>
              </tr>
            </table>

			<INPUT TYPE=HIDDEN NAME="END_FLAG" VALUE="1">

			</td>
        </tr>
        <tr>
          <td align="center" valign="middle"><input type="submit" name="CANCEL" value="<? echo lang("Cancel Wizard"); ?>" CLASS="FormLt1" STYLE="width: 150px; background: maroon; border: inset darkred 2px;"></td>
          <td align="center" valign="middle"><input type="submit" name="WIZSTEP" value="<? echo lang("Step 3")." : ".lang("Next")." >>"; ?>" CLASS="FormLt1" STYLE="width: 150px; background: darkgreen; border: inset lightgreen 2px;"></td>
        </tr>
        <tr>
          <td align="center" valign="middle">&nbsp;</td>
          <td align="center" valign="middle">&nbsp;</td>
        </tr>
      </table>
    </FORM>


<?

} // End "Step 2"

###############################################################################
/*   _____ _               ____
    / ____| |             |___ \
   | (___ | |_ ___ _ __     __) |
    \___ \| __/ _ \ '_ \   |__ <
    ____) | ||  __/ |_) |  ___) |
   |_____/ \__\___| .__/  |____/
                  | |
                  |_|

*/## Enter your Web Site Information
###############################################################################


if ($WIZSTEP == "Step 3 : Next >>") {
   
   # Define module heading text and instructions for this step
   $heading_text = lang("Step 3: Enter Site Information");
   $instructions = lang("This page lets you add important details to your website. The main site title and e-mail address are important because that &quot;brands&quot; your site as well as allows for the wizard to redirect the submission data of any forms that may be placed on your pages.");

	$HIDDEN_VARS = "";
	$HIDDEN_VARS .= "\n\n<INPUT TYPE=HIDDEN NAME=\"TEMPLATE\" VALUE=\"$TEMPLATE\">\n";
	$s = 0;	// Active Var Flag

	reset($HTTP_POST_VARS);
	while (list($name, $value) = each($HTTP_POST_VARS)) {
		if ($name == "END_FLAG") { $s = 0; }
		if ($s == 1 && $name != "WIZSTEP") {
			$HIDDEN_VARS .= "<INPUT TYPE=HIDDEN NAME=\"$name\" VALUE=\"$value\">\n";
		}
		if ($name == "START_FLAG") { $s = 1; }

	} // End While


   #######################################################
   ### GET COUNTRY DATA FROM FLAT FILE
   $filename = "../webmaster/shared/countries.dat";
   $file = fopen("$filename", "r") or DIE("".lang("Error").": ".lang("Could not open country data")." (../webmaster/shared/contries.dat).");
      $tmp_data = fread($file,filesize($filename));
   fclose($file);
   
   $natDat = split("\n", $tmp_data);
   $numNats = count($natDat);
   
   //natDat: T.M.I (for now) format for proper display and usage
   $natNam = "";
   for ($f=0; $f < $numNats; $f++) {
      $tmpSplt = split("::", $natDat[$f]);
      $natNam[$f] = "$tmpSplt[0] - $tmpSplt[1]";
      $natNam[$f] = strtoupper($natNam[$f]);
   }


?>


	<FORM NAME="bizwiz" METHOD="post" ACTION="start.php">

	<? echo $HIDDEN_VARS; ?>
	
	<style>
	
	#step3contain {
	   /*border: 1px dashed #000000;*/
	   font-size: 11px;
   }
   
	#left_side {
	   /*border: 1px dashed red;*/
	   width: 40%;
	   float: left;
	   margin-left: 65px;
   }
   
	#right_side {
	   /*border: 1px dashed green;*/
	   width: 40%;
	   float: left;
	   margin-left: 60px;
   }
   
   #footer_side {
      /*border: 1px dashed #000000;*/
      /*margin: 5px;*/
   }
   
   #footer_side input {
      /*border: 1px dashed #000000;*/
      margin: 15px;
   }
   
   .field_text {
      padding: 2px;
   }
   
   .input_area {
      padding: 2px;
   }
	
	</style>
	
	<div id="step3contain">
	
	   <div id="left_side">
	      <h3><? echo lang("Site Settings"); ?> <br/><span class="blue_31" style="font-size: 10px;">(<? echo lang("Not required but recommended"); ?>)</span></h3>
	      
	      <div class="field_text"><? echo lang("Web Site Title"); ?>:</div>
	      <div class="input_area"><input name="WEBSITE_TITLE" type="text" id="WEBSITE_TITLE" style="font-family: Tahoma; font-size: 8pt; width: 220px;" value="My Website" size="45"></div>
	      
	      <div class="field_text"><? echo lang("Contact Email Address"); ?>:</div>
	      <div class="input_area"><input name="WEBSITE_EMAIL" type="text" id="WEBSITE_EMAIL" style="font-family: Tahoma; font-size: 8pt; width: 220px;" value="webmaster@<? echo eregi_replace('^www\.', '', $_SESSION['this_ip']); ?>" size="45"></div>
	   </div>
	   
	   <div id="right_side">
	      <h3><? echo lang("Business Information"); ?> <br/><span class="blue_31" style="font-size: 10px;">(<? echo lang("Not required but recommended"); ?>)</span></h3>
	      
	      <div class="field_text"><? echo lang("Company Name"); ?>:</div>
	      <div class="input_area"><input name="df_company" type="text" class="tfield" style="width: 220px;" value=""></div>
	      
	      <div class="field_text"><? echo lang("Phone Number"); ?>:</div>
	      <div class="input_area"><input name="df_phone" type="text" class="tfield" style="width: 220px;" value=""></div>
	      
	      <div class="field_text"><? echo lang("Street Address"); ?>:</div>
	      <div class="input_area"><input name="df_address1" type="text" class="tfield" style="width: 220px;" value=""></div>
	      
	      <div class="field_text"><? echo lang("City / Region"); ?>:</div>
	      <div class="input_area"><input name="df_city" type="text" class="tfield" style="width: 220px;" value=""></div>
	      
	      <div class="field_text"><? echo lang("State / Province"); ?>:</div>
	      <div class="input_area"><input name="df_state" type="text" class="tfield" style="width: 220px;" value=""></div>
	      
	      <div class="field_text"><? echo lang("Zip / Postal"); ?>:</div>
	      <div class="input_area"><input name="df_zip" type="text" class="tfield" style="width: 75px;" value=""></div>
	      
	      <div class="field_text"><? echo lang("Country"); ?>:</div>
	      <div class="input_area">
            <select name="df_country" style='font-family: Arial; font-size: 10px; width: 220px;'>
            <?
            //Build country list and select current
            for ($n=0;$n < $numNats;$n++) {
               $sel = "";
               if ($natNam[$n] == $df_country) { $sel = "selected"; }
               echo "    <option value=\"$natNam[$n]\" $sel>$natNam[$n]</option>\n";
            }
            ?>
            </select>
	      </div>
	      
	      <div class="field_text"><? echo lang("Copyright Text"); ?>:</div>
	      <div class="input_area">&copy; <input name="copyright" type="text" class="tfield" style="width: 205px;" value="2009 "></div>
	   </div>
	   
	   <div id="footer_side">
	      <input type="submit" name="CANCEL" value="<? echo lang("Cancel Wizard"); ?>" CLASS="FormLt1" STYLE="float: left; width: 150px; background: maroon; border: inset darkred 2px;">
	      <input type="submit" name="WIZSTEP" value="<? echo lang("Finish Wizard"); ?>" CLASS="FormLt1" STYLE="float: right; width: 150px; background: darkgreen; border: inset lightgreen 2px;">
	   </div>
	
	</div>


    </FORM>



<?
} // End Step 3


# Grab module html into container var
$module_html = ob_get_contents();
ob_end_clean();



# Build into standard module template
$module = new smt_module($module_html);
$module->meta_title = lang("Web Site Wizard");
$module->add_breadcrumb_link(lang("Web Site Wizard"), "program/wizard/start.php");
$module->icon_img = "skins/".$_SESSION['skin']."/icons/full_size/create_pages-enabled.gif";
$module->heading_text = $heading_text;
$module->description_text = $instructions;
$module->good_to_go();

?>