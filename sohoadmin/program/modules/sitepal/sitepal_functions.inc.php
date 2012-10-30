<?
###############################################################################
## Soholaunch(R) Site Management Tool
## Version 4.9
##
## Author: 			Mike Morrison
## Homepage:	 	http://www.soholaunch.com
## Bug Reports: 	http://bugz.soholaunch.com
###############################################################################

################################################################################################################################
## COPYRIGHT NOTICE
## Copyright 1999-2007 Soholaunch.com, Inc.  All Rights Reserved.
##
## This script may be used and modified in accordance to the license agreement attached (license.txt)
## except where expressly noted within commented areas of the code body. This copyright notice and the comments
## comments above and below must remain intact at all times.  By using this code you agree to indemnify Soholaunch.com, Inc,
## its coporate agents and affiliates from any liability that might arise from its use.
##
## Selling the code for this program without prior written consent is expressly forbidden and in violation of Domestic and
## International copyright laws.
#################################################################################################################################

# Use Soholaunch Affiliate ID if none is specified by Web Host in Branding Controls
if ( $_SESSION['hostco']['sitepal_affiliate_id'] == "" ) {
   $_SESSION['hostco']['sitepal_affiliate_id'] = "27092";
}

$sitepal_signup_url = "http://www.oddcast.com/affiliates/entry/?affId=".$_SESSION['hostco']['sitepal_affiliate_id'];

# sitepal_allowed()
# Returns true/false based on whether sitepal features are enabled/disabled (i.e. according branding controls, etc)
function sitepal_allowed() {

   # Enabled until proven otherwise
   $allowed = true;

   # Specifically turned off in Branding Controls?
   if ( $_SESSION['hostco']['sitepal_features'] == "off" ) {
      $allowed = false;
   }

   # Not specifically disabled but product running in branded mode?
   if ( $_SESSION['hostco']['sitepal_features'] == "" && $_SESSION['hostco']['company_name'] != "Soholaunch" ) {
      $allowed = false;
   }

   return $allowed;

} // End sitepal_allowed()



# sitepal_verify_accounts()
# Checks all saved account data to make it's still valid
# Updates db with current status value from api for each account
function sitepal_verify_accounts() {
   $qry = "select * from smt_sitepal_accounts";
   $rez = mysql_query($qry);
   while ( $getAcc = mysql_fetch_assoc($rez) ) {
      $api_response = sitepal_verify($getAcc['account_id'], $getAcc['username'], $getAcc['password'], true);

      # UPDATE db table with current account status
      $qry = "update smt_sitepal_accounts set status = '".$api_response['Status']."'";

      # Only update account info if there is new accoutn info to update with (vs. blanking everything out by saving empty return)
      if ( $api_response['Status'] == "0" ) {
         $qry .= ", account_info = '".serialize($api_response)."'";
      }
      $qry .= " where account_id = '".$getAcc['account_id']."'";
      mysql_query($qry);

   } // end while

} // End sitepal_verify_accounts()


# sitepal_accountdata_exists()
# RETURNS: true/false based on whether SOMETHING exists in the smt_sitepal_accounts table
# USE: Is account data not set-up yet or setup but gone bad?
function sitepal_accountdata_exists() {
   $qry = "select prikey from smt_sitepal_accounts limit 1";
   $rez = mysql_query($qry);
   if ( mysql_num_rows($rez) > 0 ) {
      return true;
   } else {
      return false;
   }
}


# sitepal_verified()
# Checks to make sure at least one valid SitePal account is set up
function sitepal_verified($override_sessionflag = false) {

   if ( $_SESSION['sitepal_verified'] == true && $override_sessionflag !== true ) {
      # Already checked this session
      return true;

   } else {
      $qry = "select * from smt_sitepal_accounts";
      $rez = mysql_query($qry);
      while ( $getAcc = mysql_fetch_assoc($rez) ) {
         if ( sitepal_verify($getAcc['account_id'], $getAcc['username'], $getAcc['password']) ) {
            # VERIFIED! Set sitepal verified flag and skip the rest
            $_SESSION['sitepal_verified'] = true;
            return true;
         }
      }
   } // end if

   # No valid accounts on file
   $_SESSION['sitepal_verified'] = false;
   return false;
}


# sitepal_verify()
# Checks sitepal account info and verifys via API
# Pass it account id, username, password for verification check (otherwise pulls these from userdata db)
# RETURNS: true/false based on whether SitePal account info checks out (and sets $_SESSION['sitepal_verified'] accordingly)
function sitepal_verify($accountid, $username, $password, $arrayreturn = false) {

   # Ping SitePal api now
   # cURL-post account id, un, and pw to sitepal api
   /*----------------------------------------------------------------------*/
   # Target url and script
   $scHost = "vhost.oddcast.com";
   $scPath = "/mng/mngAccountInfo.php";

   # Format data to pass to SitePal API
   $sitepal_post_data = "AccountID=".$accountid."&User=".$username."&Pswd=".$password;

   $Url = "https://vhost.oddcast.com/mng/mngAccountInfo.php";
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $Url);
   curl_setopt($ch, CURLOPT_HEADER, 0); // set to 0 to eliminate header info from response
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
   curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // Returns response data instead of TRUE(1)
   curl_setopt($ch, CURLOPT_POSTFIELDS, $sitepal_post_data); // use HTTP POST to send form data
   $curl_output =  curl_exec($ch); //execute post and get results
   curl_close ($ch);

//      # TESTING: Output response data
//      echo "[".$curl_output."]"; exit;

   $response_string = $curl_output;

   # Split up var=value pairs and stick in array
   parse_str($response_string, $api_response);

//      echo testArray($api_response);

   # Return data array or just true/false?
   if ( $arrayreturn ) {
      return $api_response;

   } else {

      # If good, save in db; if error, kick back and show error msg
      if ( $api_response['Status'] == "0" ) {
         # VERIFIED!
         return true;
      } else {
         # NO DICE!
         return false;
      }
   }

} // End sitepal_verify()


# sitepal_account()
function sitepal_account($account_id) {
   $qry = "select * from smt_sitepal_accounts";
   $qry .= " where account_id = '".$account_id."'";
   $rez = mysql_query($qry);
   $getAcc = mysql_fetch_assoc($rez);

   # Get data from api for this account_id
   return sitepal_verify($getAcc['account_id'], $getAcc['username'], $getAcc['password'], true);
}


# sitepal_features()
# Returns true/false based on whether valid sitepal account data exists, sitepal features turned on, and cURL installed on server
# If true: Sets $_SESSION['sitepal_verified'] = true so only has to check once per session
# PURPOSE: Show SitePal options too or just normal options?
#          Called from various places within product that optionally have sitepal-related abilities (i.e. Template boxes)
function sitepal_features() {
   # Enable until proven otherwise
   $showfeatures = true;

   # cURL not installed?
   if ( !hascurl() ) {
      $showfeatures = false;
   }

   # Permitted by branding controls?
   if ( !sitepal_allowed() ) {
      $showfeatures = false;
   }

   # Bad/missing account info?
   if ( !sitepal_verified() ) {
      $showfeatures = false;
   }

   return $showfeatures;

} // End sitepal_verified()


# sitepal_get_scenes(account_id)
# RETURNS: Array of scene data, indexed by account_id --- for passed account or for all accounts if no specific id passed
# OPTIONAL: Pass allinone = true to get one array with bulk scenes from all accounts instead of indexed by account in multidem ar
function sitepal_get_scenes($account_id = "", $allinone = false) {
   $scene_data = array();

   $qry = "select * from smt_sitepal_accounts where status = '0'";

   # specific account id or all accounts?
   if ( $account_id != "" ) {
      $qry .= " and account_id = '".$account_id."'";
   }

   $rez = mysql_query($qry);

   while ( $getAcc = mysql_fetch_assoc($rez) ) {
      # Build account data array for sitepal_scenes function
      $getSp = array('account_id' => $getAcc['account_id'], 'username' => $getAcc['username'], 'password' => $getAcc['password']);

      if ( $allinone == false ) {
         # Index by account id
         $scene_data[$getAcc['account_id']] = sitepal_scenes($getSp);

      } else {
         $scene_data = array_merge(sitepal_scenes($getSp), $scene_data);
      }
   }

   # Return scene data array
   return $scene_data;

} // End sitepal_get_scenes()


# sitepal_scenes()
# ACCEPTS: Array of sitepal account_id, username, password
# RETURNS: Multi-dem array of scene data from sitepal api, nicely indexed
function sitepal_scenes($getSp, $skip_untitled = false) {

   $sitepal_post_data = "AccountID=".$getSp['account_id']."&User=".$getSp['username']."&Pswd=".$getSp['password'];

   $Url = "https://vhost.oddcast.com/mng/mngListScenes.php";
   $ch = curl_init(); // URL of gateway for cURL to post to
   curl_setopt($ch, CURLOPT_URL, $Url);
   curl_setopt($ch, CURLOPT_HEADER, 0); // set to 0 to eliminate header info from response
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
   curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // Returns response data instead of TRUE(1)
   curl_setopt($ch, CURLOPT_POSTFIELDS, $sitepal_post_data); // use HTTP POST to send form data
   $response_string =  curl_exec($ch); //execute post and get results
   curl_close ($ch);

   # Get scene list into nice, useable array
   parse_str($response_string, $sp_scenes);

//   echo testArray($sp_scenes, "500"); exit;

   # Store extra data like baseURL in session so it can be accessed separately from scene array
   $_SESSION['sp_BaseURL'][$getSp['account_id']] = $sp_scenes['BaseURL'];
   $_SESSION['sitepal_BaseURL'] = $sp_scenes['BaseURL'];

//   return $sp_scenes;

   # Because it's one big array with scene1name, scene1thumb, scene2name, etc. Each scene has 7 properties plus to extra at the end.
   $total_scenes = (count($sp_scenes) - 2) / 7;

   # Stick scene data into nicely indexed multi-dem array
   $sitepal_scenes = array();
   $a = 0; // for array index
   for ( $s = 1; $s <= $total_scenes; $s++ ) {
      # Names of only those properties that are actually used
      $id = "Id".$s;
      $thumb = "Thumb".$s;
      $name = "Name".$s;

      # Skip Untitled scenes?
      if ( $skip_untitled === false || (!eregi("Untitled", $sp_scenes[$name]) && $sp_scenes[$name] != "") ) {
         $sitepal_scenes[$a]['id'] = $sp_scenes[$id];
         $sitepal_scenes[$a]['thumb'] = $sp_scenes[$thumb];
         $sitepal_scenes[$a]['name'] = $sp_scenes[$name];

         # Extract scene number from thumbnail path
         $number = sitepal_scenenum($sitepal_scenes[$a]['thumb']);
         $sitepal_scenes[$a]['number'] = $number;

         $a++;
      }
   }

   return $sitepal_scenes;
}

# sitepal_scenenum()
# Returns scene number as extracted from passed thumbnail path
function sitepal_scenenum($thumb_path) {
   $number = $thumb_path;
   $number = eregi_replace("[0-9a-z/]*thumbs/show_", "", $number);
//   $number = str_replace("show_", "", $number);
   $number = str_replace(".jpg", "", $number);

//   echo "<br/><br/>(".$thumb_path.") --> num: [".$number."]<br/><br/>"; exit;

   return $number;
}


# sitepal_status
# ACCEPTS: $param (i.e. "status", "active"), $returncode from api respose (i.e. "1", "3")
# RETURNS: Human-readable text description of what $returncode means as a value of $param
function sitepal_status($param, $returncode) {

   switch ($param) {
      # active
      case "active":
         switch ($returncode) {
            case "1":
               return "Active";
               break;
            case "0":
               return "Inactive";
               break;
         }
         break;

      # status
      case "status":
         switch ($returncode) {
            case "0":
               return "Normal completion";
               break;
            case "1":
               return "Authentication failed";
               break;
         }
         break;
   } // End switch(param)
} // End sitepal_status()

?>