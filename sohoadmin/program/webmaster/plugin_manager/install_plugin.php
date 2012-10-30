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
include_once("../../includes/product_gui.php");
error_reporting(E_PARSE);

# Plugin install/misc functions (hook_attach, hook_special, etc)
include_once("plugin_functions.php");

$updateprefs = new userdata("updateprefs");

if ( $_GET['todo'] == "finish_install" ) {
   # Bomb if no plugin name passed
   if ( $_GET['plugin'] == "" ) {
      echo "Developer note: Please specify name of plugin folder as plugin= in redirect URL."; exit;
   }

   $plugin_folder_name = $_GET['plugin'];

   # Preserve original working dir and switch to /plugins folder
   $plugins_dir_path = $_SESSION['docroot_path']."/sohoadmin/plugins/";
   $odir = getcwd();
   chdir($plugins_dir_path);
}

if ( $_GET['todo'] != "finish_install" ) { // Normal upload & install process, not coming back from custom install script
   # For zip extract on WIN/IIS servers
   $IISupload = $doc_root."/sohoadmin/unzips/";
   $plugins_dir_path = $_SESSION['docroot_path']."/sohoadmin/plugins/";

   # Where should the zip file be extracted?
   # Was plugin zip uploaded (install new plugin), or downloaded (update plugin)?
   if ( isset($_GET['downloaded_zipfile']) ) {
      $uploadFile = $plugins_dir_path . "temp/" . $_GET['downloaded_zipfile'];
      $zipfile_name = $_GET['downloaded_zipfile'];
      $OS = strtoupper(PHP_OS);

   } else {
      $uploadFile = $plugins_dir_path . "temp/" . $_FILES['FILE1']['name'];
      $zipfile_name = $_FILES['FILE1']['name'];
      $OS = strtoupper(PHP_OS);
   }

   /* Display file upload info for testing */
   /*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*
   //echo $TempfilE = $_FILES['FILE1']['tmp_name'];
   //echo $uploadDir." is uploadDir <br>";
   //echo $uploadFile." is uploadFile <br>";
   //echo $this_zip." is this_zip <br>";
   //print_r($_FILES);
   //
   //$shellenlikemagellen = shell_exec("cp $TempfilE $uploadFile");
   //echo "<pre>.$shellenlikemagellen.</pre>";
   //exit;
   /*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/

   /*---------------------------------------------------------------------------------------------------------*
       ______       __                      __     _____    _
      / ____/_  __ / /_ _____ ____ _ _____ / /_   /__  /   (_)____
     / __/  | |/_// __// ___// __ `// ___// __/     / /   / // __ \
    / /___ _>  < / /_ / /   / /_/ // /__ / /_      / /__ / // /_/ /
   /_____//_/|_| \__//_/    \__,_/ \___/ \__/     /____//_// .___/
                                                          /_/
   /*---------------------------------------------------------------------------------------------------------*/
   //error_reporting(E_ALL);

   if ( !is_dir($plugins_dir_path."temp") ) {
   	mkdir($plugins_dir_path."temp", 0755);
   }

   //echo getcwd(); exit;
   if ( isset($_GET['downloaded_zipfile']) || move_uploaded_file($_FILES['FILE1']['tmp_name'], $uploadFile) ) {

      $go = 0;
      chmod($uploadFile, 0755);

      # Preserve original working dir and switch to /plugins folder
      $odir = getcwd();
      chdir($plugins_dir_path);

      if ( $go == 0 ) {
   		chdir($plugins_dir_path."temp");
		
         $unzip_lib_folder = $_SESSION['docroot_path']."\sohoadmin\program\includes\untar";

   		copy($zipfile_name, $plugins_dir_path.$zipfile_name);

         # Extract zip file
			if ( eregi("WIN", $OS) ) {    
				chdir($plugins_dir_path);
				
				$sshRez = exec('"'.$unzip_lib_folder.'\unzip" -o -q -L '.$zipfile_name);
				$sshRez = shell_exec('"'.$unzip_lib_folder.'\unzip" -o -q -L '.$zipfile_name);
				chdir($plugins_dir_path."temp");
				sleep('5');
				$sshRez = exec('"'.$unzip_lib_folder.'\unzip" -o -L '.$zipfile_name);
				$sshRez .= shell_exec('"'.$unzip_lib_folder.'\unzip" -o -L '.$zipfile_name);
				chdir($plugins_dir_path);
			} else { // Linux
				$sshRez = exec("unzip -o ".$zipfile_name);
				chdir($plugins_dir_path);
				$sshRez = exec("unzip -o ".$zipfile_name);
			}


         if ( !unlink("temp/".$zipfile_name) ) {
            echo "Could not delete uploaded zip file (temp/".$zipfile_name."). Check permissions on sohoadmin/plugins folder.<br>"; exit;
         }

   			# Get plugin_folder_name
   			foreach (GLOB($plugins_dir_path."temp/*") as $file) {
//   			   echo "file = [".$file."]<br/>";
   				$file = str_replace($plugins_dir_path.'temp/', '', $file);
   				$file = str_replace($plugins_dir_path.'temp\\', '', $file);
   				$plugin_folder_name = $file;
   				$sshRez = $file;
   			}

   			# Kill temporary folder
   			rmdirr($plugins_dir_path."temp");

         # Throw error if extract failed
         if ( $sshRez == "" ) {
            echo "<div style=\"padding: 10px;font: 12px Trebuchet MS;background-color: #f8f9fd;margin: 15px;border: 1px solid #336699;\">\n";
            echo " <p><b>Error:</b> Could not successfully extract zip file (".$zipfile_name.").</p>\n";
            echo " <p><b>Possible causes/fixes...</b></p>\n";
            echo " <p><b>1. PHP's exec() function disabled on your server -</b> Chances are if this is the cause then you'll probably have \n";
            echo " problems with the Software Updates and Backup/Restore features as well. If that's the case, you may want to ask your web hosting company about \n";
            echo " whether \"the php function called shell exec is disabled\" on your server.</p>\n";

            echo " <p><b>2. Damaged archive file -</b> This rarely happens (#1 is usually the culprit), but you may want to check whether the .zip archive file itself is incomplete/damaged.\n";
            echo " To do this, use WinZip or similiar utility on your local PC to open up the plugin .zip file you downloaded...\n";
            echo " If the .zip opens in WinZip with no error message, and you can see all the files and folders within the .zip, then\n";
            echo " the archive is probably OK. Otherwise, if you do get an error when you try to open the plugin .zip with WinZip, then try re-downloading the plugin .zip,\n";
            echo " or contacting the plugin author if that doesn't work.</p>\n";
            echo "</div>\n";
         }

         # Delete zip file
         if ( !unlink($zipfile_name) ) {
            echo "Could not delete uploaded zip file (".$zipfile_name."). Check permissions on sohoadmin/plugins folder.<br>";
         }

         # Set permissions on sohoadmin to something aggreeable?
         if ( $updateprefs->get("chmod_after") == "yes" && !php_suexec() ) {
            exec("chmod -R 0777 ".$_SESSION['docroot_path']."/sohoadmin");
         } else {
            exec("chmod -R 0755 ".$_SESSION['docroot_path']."/sohoadmin");
         }

      }

      # Display shell output for testing
      /*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*
      echo "<br><br>\n";
      echo "<b>Shell output from tar command:</b> <br>";
      echo "<textarea style=\"width: 400px; height: 150px;\">".$sshRez."</textarea>\n";
      echo "<br><br>\n";
      /*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/

   } else {
      # Upload error
      if ( $_FILES['FILE1']['tmp_name'] == "" ) {
         header("location: plugin_manager.php?errorcode=blankuploadfield"); exit;
      } else {
         echo "<font color=\"red\"><strong>Error:</strong> Unable to install plugin files... <br>\n";
         echo "<p>Details: There was a problem moving the file you uploaded from it's temporary location on the server (where all uploaded files go initially) to\n";
         echo "the sohoadmin/plugins folder. This may mean that the plugins/ folder either does not exist, or that permissions on it are too tight to allow\n";
         echo "the plugin file to be copied into it.</p>\n";

         echo "<p><b>First thing to try:</b> Go to the Help Center feature, under the 'Diagnostic' button, look under 'Special Tools', and click the link that says something like\n";
         echo "\"Make sure all system-created folders exist\". Then try to install the plugin again.</p>\n";

         echo "<p>If that doesn't work go in through FTP and make sure that the sohoadmin/plugins folder exists. If it doesn't exist, create it.\n";
         echo "If it does exist (including if you just created it), look at the permissions on it (right click > properties in most FTP clients). \n";
         echo "Try opening permissions up to 777 to just see if it fixes the problem, then if you want you can tighten permission back down bit by bit until it breaks again \n";
         echo "(then go back one step).</p>\n";
      }
      exit;
   }

} // End if todo != finish_install



/*---------------------------------------------------------------------------------------------------------*
    ____              __          __ __
   /  _/____   _____ / /_ ____ _ / // /
   / / / __ \ / ___// __// __ `// // /
 _/ / / / / /(__  )/ /_ / /_/ // // /
/___//_/ /_//____/ \__/ \__,_//_//_/

-Read install_manifest
-Insert entry into addon table
/*---------------------------------------------------------------------------------------------------------*/

# Get name of extracted plugin folder
//eregi("inflating: ([a-zA-Z_0-9]+)", $sshRez, $regs);

//$plugin_folder_name = $regs[1];

# TESTING: Uncomment to troubleshoot folder name regex
/*-----------------------------------------------------*
echo "sshRez: [".$sshRez."]<br>";
//echo "0: ".$regs[0]."<br>";
//echo "1: ".$regs[1]."<br>";
//echo "2: ".$regs[2]."<br>";
echo "foldername: [".$plugin_folder_name."]<br>"; exit;
/*-----------------------------------------------------*/

# Bomb if can't get a folder name
if ( $plugin_folder_name == "" ) {
   echo "Error: Unable to detect name of extracted folder.<br>"; exit;
}

if ( !file_exists($plugin_folder_name."/install_manifest.php") ) {
   echo "Error: Cannot locate install_manifest.php for this plugin.<br>";
   echo "Looking for it here: [".$plugin_folder_name."/install_manifest.php]<br>"; exit;
} else {
   # Read in plugin's install manifest
   include($plugin_folder_name."/install_manifest.php");
}


# Check for this first: If plugin wants to collect user data before processing file mods, go to form now
# Only do this once, not when coming back from install script
if ( $plugin_install_form != "" && $_GET['todo'] != "finish_install" ) {
   header("location:../../../plugins/".$plugin_folder_name."/".$plugin_install_form); exit;
}


//# For testing
//echo "getcwd(): [".getcwd()."]<br>";
//echo "Plugin Folder: [".$plugin_folder_name."]<br>";
//exit;

# Kill folder and redirict to plugin manager with error message if not licensed
if ( !addon_licensed($plugin_folder_name) ) {
   rmdirr($plugin_folder_name);
   header("location: plugin_manager.php?errorcode=notlicensed"); exit;
}

# Build insert query for plugin table
$data = array();
$data['PLUGIN_FOLDER'] = $plugin_folder;
$data['TITLE'] = $plugin_title;
$data['VERSION'] = $plugin_version;
$data['DESCRIPTION'] = base64_encode($plugin_description);
$data['AUTHOR'] = $plugin_author;
$data['HOMEPAGE'] = $plugin_homepage;
$data['ICON'] = $plugin_icon;
$data['OPTIONS_LINK'] = $plugin_options_link;
if ( isset($_GET['downloaded_zipfile']) ) {
   $addons_api = addons_api($plugin_folder);
   $data['changelog'] = $addons_api['changelog'];
   $data['release_date'] = $addons_api['release_date'];
}

# Bomb if no plugin folder to work with
if ( $data['PLUGIN_FOLDER'] == "" ) {
   header("location: plugin_manager.php?errorcode=nopluginfolder"); exit;
}

# Make sure plugin does not already exist
$updating_plugin = false; // default
$selqry = "select * from system_plugins where PLUGIN_FOLDER = '".$plugin_folder."'";
$result = mysql_query($selqry);
if ( mysql_num_rows($result) > 0 ) {
   # Kill any exisiting records found in plugin/hook tables
   mysql_query("delete from system_plugins where PLUGIN_FOLDER = '$plugin_folder'");
   mysql_query("delete from system_hook_attachments where PLUGIN_FOLDER = '$plugin_folder'");
   $updating_plugin = true;
}

# Insert plugin record into db table
$qry = new mysql_insert("system_plugins", $data);
$qry->insert();

# Insert hook attachment records now
attach_hooks();

# Process hook overwrites now
process_overwrites();

# Uninstall replacements first if updating plugin itself
if ( $updating_plugin ) {
   uninstall_replacements();
}

# Process hook replacements
install_replacements();

# Make double-sure sure we're operating from the right folder
chdir($plugins_dir_path);

# Create plugin db tables?
$dbtable_script = $plugin_folder."/".$file_that_creates_plugin_dbtables;
if ( $file_that_creates_plugin_dbtables != "" && file_exists($dbtable_script) ) {
   include($dbtable_script);
} else {
   //echo "Plugin db table create script [".$dbtable_script."] does not exist!"; exit;
}

# Add plugin to session so it can be marked as 'restart to complete installation'
# The $_SESSION['new_plugins'] array is cleared at login (update_client.php)
$_SESSION['new_plugins'][] = $data['PLUGIN_FOLDER'];

# Switch back to original working directory
chdir($odir);

# Go back to plugin manager
# Installing or updating?
if ( isset($_GET['downloaded_zipfile']) ) {
   header("Location: plugin_manager.php?todo=plugin_updated&plugin_folder=".$plugin_folder); exit;
} else {
   header("Location: plugin_manager.php?todo=install_complete"); exit;
}



//echo "New Plugin installed sucessfully!<br><br>";


?>