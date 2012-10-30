<?php
//error_reporting(E_PARSE);
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

/*=========================================================================================================================
 CLASS OVERVIEW: Description Of Available Methods
===========================================================================================================================

-----------------------------------
auto_update()
-----------------------------------
 + Pulls data from 'system_version' table

 + Collects info about installed build
   - $localbuild = "[release_time]"
   - $newinstall = "NO" / "YES" (based on existence of soholaunch.lic) // MAY BE UNCESSARY IF WE POST 'HOW-TO' ON UPDATING DEPOT FILE.

-----------------------------------
check()
-----------------------------------
 + Socket-posts release_time of currently installed build
   - update_request.php
   -> action = "check"
   -> localbuild = "[release_time]" / "newinstall"

 + Reads & cleans response from remote script
   - trim()
   - strtoupper()

 < Returns formatted response text
   - LATEST / AVAIL / FORCE

-----------------------------------
download()
-----------------------------------
 + new file_download() object
   - REMOTE: url from update_file field/array key
   - LOCAL: /docroot/sohoadmin/update/update_dl-[timestamp].tgz


-----------------------------------
install()
-----------------------------------
 + Extracts tgz update file from docroot

 + Deletes tgz update file

 + Reads & unserializes updated build info array
   - sohoadmin/build.dat.php

 + Updates local version info with restored array data
   - system_version

 + Turns off 'update avail' notification
   - setflag("OFF")

/*=========================================================================================================================*/


/*::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::*
    ___           __            __  __            __        __
   /   |  __  __ / /_ ____     / / / /____   ____/ /____ _ / /_ ___
  / /| | / / / // __// __ \   / / / // __ \ / __  // __ `// __// _ \
 / ___ |/ /_/ // /_ / /_/ /  / /_/ // /_/ // /_/ // /_/ // /_ /  __/
/_/  |_|\__,_/ \__/ \____/   \____// .___/ \__,_/ \__,_/ \__/ \___/
                                  /_/
/*:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::*/
class auto_update {

   # Version information
   var $local = array();
   var $remote = array();

   # Local update file
   var $updateTgz;

   # Report strings by method
   var $report = array();

   /// Collect available version info
   ###===================================================================
   function auto_update() {
      # Pull timestamp for installed version
      $this->local['release_time'] = db_fetch("system_version", "release_time");

      // Determine if it's a new installation
      //===================================================
      $chklic = $_SESSION['docroot_path']."/sohoadmin/soholaunch.lic";
      if ( !file_exists($chklic) ) {
         $this->local['licensed'] = "YES";
      } else {
         $this->local['licensed'] = "NO";
      }

   }


   /// CHECK remote server for new build and set local 'flag' accordingly
   ###================================================================================
   function check() {
      $local_dom = $_SERVER['HTTP_HOST'];
      $local_rtime = $this->local['release_time'];
      $sockdata = array( 'surfto'=>"check", 'local_rtime'=>$local_rtime, 'local_dom'=>$local_dom );

      // Open socket to update site and get any new build info
      //--------------------------------------------------------------------
      $upsock = new fsockit("update.securexfer.net", "/sohoadmin/update_request.php", $sockdata);
      $rez = $upsock->sockput();
      $response = $rez['delimited'];
      $this->remote = unserialize($response);

      if ( $this->remote['flag'] != "AVAIL" ) { $this->remote['flag'] = "LATEST"; }

      // Make appropriate updates to local version table
      //vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv
      $update_qry = "last_checked = '".addslashes(date("h:i s"))."', update_flag = '".addslashes($this->remote['flag'])."', ";
      $update_qry .= "update_info = '".addslashes($response)."', update_file = '".addslashes($this->remote['tgzfile'])."'";
      db_set("system_version", $update_qry);

      return $this->remote;

   }


   /// DOWNLOAD update .tgz file from remote server
   ###================================================================================
   function download() {
      # Pull remote filename from local db table
      $remfile = db_fetch("system_version", "update_file");

      # Define local filename for downloaded update
      $localfile = $_SESSION['docroot_path']."/sohoadmin/update/autoupdate-".time().".tgz";

      // Download now!
      $dLoad = new file_download($remfile, $localfile);
      $this->report['download'] = $dLoad->msg;

      # Check for local file existence
      if ( file_exists($localfile) ) {
         $this->updateTgz = $localfile;
         return true;
      } else {
         return false;
      }
   }


   /// INSTALL new product update (extract tgz, kill tgz, txt into db)
   ###================================================================================
   function install() {

      // 1. Extract tgz update file, then delete it
      //======================================================
      # Build untar command
      $untar = "tar -xzvf ".$this->updateTgz;

      # Stow current dir in tmp var
      $origdir = getcwd();

      # Switch to docroot perspective
      chdir($_SESSION['docroot_path']);

      # Extract update tar from docroot
      exec($untar);

      # Switch back to orig folder
      chdir($origdir);

      # Delete tgz update file
      if ( unlink($this->updateTgz) ) {
         $this->report['install'] = "Update file extracted and removed.";
      }

      // 2. Get new version info from build.dat.php
      //======================================================
      # Build path to file
      $datPath = $_SESSION['docroot_path']."/sohoadmin/build.dat.php";

      if ( file_exists($datPath) ) {
         # Read contents and close file
         $datfile = fopen($datPath, "r");
         $dat_info = fread($datfile, filesize($datPath));
         fclose($datfile);

         # Restore serailized array
         $datInfo = unserialize($dat_info);
      }

      // 3. Update db table with new build info
      //======================================================
      # Build DB update query
      $newstuff = "version = '".$datInfo['product_version']."', build = '".$datInfo['product_build']."', ";
      $newstuff .= "release_date = '".$datInfo['release_date']."', release_time = '".$datInfo['release_time']."', ";
      $newstuff .= "changelog = '".$datInfo['changelog']."', update_flag='LATEST'";

      if ( db_set("system_version", $newstuff) ) {
         $this->report['install'] = "Local version information updated.";
      }

      return true;

   }

   function errortest() {
      echo "<b>Local build:</b> (<span style=\"color: #ff6600;\">".$this->localbuild."</span>)<br>";

      echo "<br><br><u><b>check()</b></u><br>";
      echo "Raw Response: (<b>".$this->check()."</b>)<br>";
      exit;
   }


}











/*----------------------------------------------------*
class file_download {

   // Break full path into element arrays
   //================================================
   function remote_file($rempath, $locpath) {
      $this->remote['path'] = $rempath;
      $this->remote['dir'] = dirname($rempath);
      $this->remote['file'] = basename($rempath);

      $this->local['path'] = $locpath;
      $this->local['dir'] = dirname($locpath);
      $this->local['file'] = dirname($locpath);
   }


   function dlnow() {
      error_reporting(E_ALL);
      // open remote file handle
      $fp1 = fopen($this->remote['path'],"r");

      if ( !$fp1 ) {
         echo "Unable to open remote file!<br><br>\n";
         exit;
      } else {
         echo "Remote file seems ok.<br><br>\n";
         //exit;
      }

      // create local file
      $fp2 = fopen($this->local['path'],"w");

      // read remote and write to local
      while (!feof($fp1)) {
           $output = fread($fp1,1024);
           fputs($fp2,$output);
      }

      fclose($fp1);
      fclose($fp2);
   }


} // End remote file class
/*----------------------------------------------------*/

?>