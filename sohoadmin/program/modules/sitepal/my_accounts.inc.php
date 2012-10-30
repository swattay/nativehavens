<?php
error_reporting(E_PARSE);
if($_GET['_SESSION'] != '' || $_POST['_SESSION'] != '' || $_COOKIE['_SESSION'] != '') { exit; }
#===================================================================================================================================
# Soholaunch v4.91 > SitePal > My Accounts layer content include
#===================================================================================================================================

# PROCESS: Delete account
if ( $_GET['delete_account'] != "" ) {
   $qry = "delete from smt_sitepal_accounts where account_id = '".$_GET['delete_account']."'";
   $rez = mysql_query($qry);
   $report[] = "Account #".$_GET['delete_account']." deleted!";
}

/*--------------------------------------------------------------------------------------------------------*
   ___      __    __  ___                             __
  / _ | ___/ /___/ / / _ | ____ ____ ___  __ __ ___  / /_
 / __ |/ _  // _  / / __ |/ __// __// _ \/ // // _ \/ __/
/_/ |_|\_,_/ \_,_/ /_/ |_|\__/ \__/ \___/\_,_//_//_/\__/

# verify_account_info
# Check/Add/Update submitted account info
/*---------------------------------------------------------------------------------------------------------*/
if ( $_POST['todo'] == "verify_account_info" ) {
   $addedit_acc_error = "";

   # If good, save in db; if error, kick back and show error msg
   $api_response = sitepal_verify($_POST['accountid'], $_POST['username'], $_POST['password'], true);

   # If good, save in db; if error, kick back and show error msg
   if ( $api_response['Status'] == "0" ) {
      # VERIFIED!
      # Store verification info for this account in db
      $data = array();
      $data['account_id'] = $_POST['accountid'];
      $data['username'] = $_POST['username'];
      $data['password'] = $_POST['password'];
      $data['status'] = $api_response['Active'];
      $data['account_info'] = serialize($api_response);
//      $data['date_created'] = $api_response['Creation'];
      $data['date_created'] = strtotime($api_response['Creation']);

      # Account already exist?
      $qry = "select prikey from smt_sitepal_accounts where account_id = '".$_POST['accountid']."'";
      $rez = mysql_query($qry);
      if ( mysql_num_rows($rez) > 0 ) {
         # UPDATE
         $qry = "update smt_sitepal_accounts set";
         $qry .= " username = '".$_POST['username']."'";
         $qry .= ", password = '".$_POST['password']."'";
         $qry .= ", status = '".$api_response['Active']."'";
         $qry .= ", account_info = '".$data['account_info']."'";
         $qry .= ", date_created = '".$data['date_created']."'";
         $qry .= ", account_title = '".$_POST['account_title']."'";
         $qry .= " where account_id = '".$_POST['accountid']."'";
         $rez = mysql_query($qry);

         $report[] = "<b>Details for account #</b>".$_POST['accountid']." updated!";

      } else {
         # INSERT
         $myqry = new mysql_insert("smt_sitepal_accounts", $data);
         $myqry->insert();

         $report[] = "<b>Account #</b>".$_POST['accountid']." added!";
      }

   } else {
      # ERROR
      $report[] = "<b>Error:</b>". $api_response['Error'];
      $addedit_acc_error = "<b>Error:</b>". $api_response['Error'];
   }

} // end if todo = verify_account_info"


# Verify stored accounts against api-returned info
sitepal_verify_accounts();


/*---------------------------------------------------------------------------------------------------------*
 _     _      _       _                            _
| |   (_) ___| |_    /_\   __  __  ___  _  _  _ _ | |_  ___
| |__ | |(_-<|  _|  / _ \ / _|/ _|/ _ \| || || ' \|  _|(_-<
|____||_|/__/ \__| /_/ \_\\__|\__|\___/ \_,_||_||_|\__|/__/
/*---------------------------------------------------------------------------------------------------------*/
if ( !sitepal_verified(true) ) {
   # No Account!
   echo "<p>No valid SitePal account info on file.</p>\n";

} else {
   # Pull scene data for all accounts now
   $sitepal_scenes = sitepal_get_scenes();

//   echo testArray($sitepal_scenes);

   # Pull accounts
   $qry = "select * from smt_sitepal_accounts order by date_created desc";
   $rez = mysql_query($qry);

   while ( $getAcc = mysql_fetch_assoc($rez) ) {
      # Restore raw acccount info
      $account_info = unserialize($getAcc['account_info']);

      # Shorthand for this account's scene data
      $getScene = $sitepal_scenes[$getAcc['account_id']];

      # For toggle-able details
      $idname = $getAcc['account_id']."-details";

      # Account title?
      if ( $getAcc['account_title'] != "" ) { $account_title_display = "<h3>".$getAcc['account_title']."</h3>"; } else { $account_title_display = ""; }
      if ( $getAcc['account_title'] != "" ) {
         $account_title_display = "<h3>".$getAcc['account_title']." <span> | <label>Account ID:</label> ".$getAcc['account_id']."</span></h3>";

      } else {
         $account_title_display = "<h3>Account ".$getAcc['account_id']."</h3>";
      }

      # "Account 124346 - Soholaunch Enterprise"
      echo "<div class=\"account_box\">\n";
      echo " <span class=\"account_box-heading\">\n";
      echo $account_title_display;
//      echo "  <h4></h4>\n";
      echo "  <div class=\"editlink\">\n";
      # [delete]
      echo "   [ <span onclick=\"document.location.href='management_module.php?delete_account=".$getAcc['account_id']."';\" class=\"red uline hand\">Delete</span> ]&nbsp;&nbsp;&nbsp;\n";
      # [edit]
      echo "   [ <span onclick=\"edit_account('".$getAcc['account_id']."', '".$getAcc['username']."', '".$getAcc['password']."', '".$getAcc['account_title']."');\" class=\"bold blue uline hand\" style=\"text-transform: uppercase;letter-spacing: .02em;\">Edit</span> ]\n";
      echo "  </div>\n";
      echo "  <div class=\"ie_cleardiv\"></div>\n";
      echo " </span>\n";

      # Problem with account?
      if ( $getAcc['status'] != "0" ) {
         # ERROR: Account cannot verify
         echo " <h4 class=\"red nomar_btm\">Error: Could not verify account</h4>\n";
         echo " <p class=\"nomar_top\"><b>Reason:</b> ".sitepal_status("status", $getAcc['status'])."</p>\n";
         echo " <p>Please <span class=\"blue uline hand\" onclick=\"edit_account('".$getAcc['account_id']."', '".$getAcc['username']."', '".$getAcc['password']."', '".$getAcc['account_title']."');\">edit this account</span> and make sure that the information is current.\n";

      } else {
         # VALID: Account is still legit
         echo " <p class=\"nomar\">\n";
         echo "  <span onclick=\"toggleid('".$idname."');\" class=\"blue uline hand\">Show/Hide Details</span>\n";
         echo " </p>\n";
         echo " <span id=\"".$idname."\" style=\"display: none;\">\n";
         echo "  <label>User Name:</label> ".$getAcc['username']."<br/>";
         echo "  <label>Status:</label> ".sitepal_status("active", $account_info['Active'])." |";
         echo "  <label>Created:</label> ".$account_info['Creation']." |\n";
         echo "  <label>Expires:</label> ".$account_info['Expiration']."<br/>\n";
         echo "  <label>Licensed Domains:</label> ".$account_info['Domains']." |\n";
         echo "  <label>Audio Limit:</label> ".$account_info['AudioLimit']." |\n";
         echo "  <label>Telephone Updates Remaining:</label> ".$account_info['TelUpdate']."<br/>\n";
         echo " </span>\n";

         # Thumbnail previews for this account's scenes
         $max = count($getScene);
         if ( $max > 0 && is_array($getScene) ) {
            # Scene thumbnails
            echo " <p class=\"nomar_btm\">Preview of [<b>".$account_info['NumSS']."</b>] scenes under this account&hellip;</p>\n";
            echo " <div class=\"scene_thumbnail_gallery\">\n";
            for ( $s = 0; $s < $max; $s++ ) {
               echo "  <img src=\"".$_SESSION['sitepal_BaseURL'].$getScene[$s]['thumb']."\"/>\n";
            }
            echo " </div>\n";
         } else {
            # No thumbs avail
            echo "<p><i>No thumbnail previews found for this account. Usually this means that you haven't set up any scenes yet, but sometimes it happens when you just haven't made changes to your scenes in a while.</i></p>";
         }
      } // end else status = 0

      echo "</div>\n";
   }

} // End else sitepal_verified so pull account(s)


# [+] Add SitePal Account
echo "  <p id=\"container-add_account_btn\"><input id=\"btn-add_account\" type=\"button\" value=\"[+] Add SitePal Account\" onclick=\"add_account();\" ".$_SESSION['btn_build']."></p>\n";


# popconfig-add_account
ob_start();
include("popup-add_account.inc.php");
$popup = ob_get_contents();
ob_end_clean();
echo help_popup("popconfig-add_account", "Add SitePal Account", $popup, "z-index: 999;left: 5%;".$errdisplay);

# Re-display popup on error
if ( $_POST['todo'] == "verify_account_info" && $addedit_acc_error != "" ) {
   if ( $_POST['action'] == "edit" ) {
      echo "<script type=\"text/javascript\">edit_account('".$_POST['accountid']."', '".$_POST['username']."', '".$_POST['password']."');</script>\n";
   } else {
      echo "<script type=\"text/javascript\">add_account();</script>\n";
   }
}
?>