<?php
#===================================================================================================================================
# Soholaunch v4.91 > SitePal > My Accounts layer content include
#===================================================================================================================================
error_reporting(E_PARSE);
if($_GET['_SESSION'] != '' || $_POST['_SESSION'] != '' || $_COOKIE['_SESSION'] != '') { exit; }
session_start();
include_once($_SESSION['docroot_path']."/sohoadmin/program/includes/product_gui.php");
error_reporting(E_PARSE);


# Make sure sitepal db tables exist
include_once("create_dbtables-sitepal.php");

# Use Soholaunch Affiliate ID if none is specified by Web Host in Branding Controls
if ( $_SESSION['hostco']['sitepal_affiliate_id'] == "" ) {
   $_SESSION['hostco']['sitepal_affiliate_id'] = "27092";
}

# Centralize
$sitepal_signup_url = "http://www.oddcast.com/sitepal?&affId=".$_SESSION['hostco']['sitepal_affiliate_id']."&bannerId=0&promotionId=8243";
$edit_scene_url = "https://vhost.oddcast.com/admin/index.php";
ob_start();


# DEFAULT: Let sitepal_verified() check pass based on session flag (vs. actual cURL qrys)...saves load time
$force_verify = false;

# PROCESS: Delete account
if ( $_GET['delete_account'] != "" ) {
   $qry = "delete from smt_sitepal_accounts where account_id = '".$_GET['delete_account']."'";
   $rez = mysql_query($qry);
   $report[] = "Account #".$_GET['delete_account']." deleted!";

   # When page loads force "for real" sitepal_verified() check
   $force_verify = true;
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
      $data['date_created'] = strtotime($api_response['Creation']);
      $data['account_title'] = slashthis($_POST['account_title']);

      # Update or Insert?
      if ( $_POST['editkey'] != "" ) {
         # UPDATE
         $qry = "update smt_sitepal_accounts set";
         $qry .= " account_id = '".$_POST['accountid']."'";
         $qry .= ", username = '".$_POST['username']."'";
         $qry .= ", password = '".$_POST['password']."'";
         $qry .= ", status = '".$api_response['Active']."'";
         $qry .= ", account_info = '".$data['account_info']."'";
         $qry .= ", date_created = '".$data['date_created']."'";
         $qry .= ", account_title = '".$_POST['account_title']."'";
         $qry .= " where prikey = '".$_POST['editkey']."'";
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


ob_start();
?>


<!---Rules for this specific module-->
<link rel="stylesheet" type="text/css" href="module.css"/>

<script type="text/javascript">
// Pass account info to edit form and populate fields
function edit_account(account_id, username, password, account_title, prikey) {
   // Set popup style to edit mode
   $('popconfig-add_account-content').className = 'editmode';
   $('addoredit').value = 'edit';

   // Change title text to "Edit..."
   $('popconfig-add_account-title').innerHTML = 'Edit SitePal Account';

   // Populate fields in add/edit popup
   newtitle = decodeURIComponent(account_title);
   newtitle = newtitle.replace("\+", " ");
   $('account_title').value = newtitle;
//   $('account_title').value = account_title;
   $('accountid').value = account_id;
   $('username').value = username;
   $('password').value = password;
   $('editkey').value = prikey;

   // Show popup
   showid('popconfig-add_account');
}

// Clear account popup form fields and set to 'add' mode
function add_account() {
   $('popconfig-add_account-content').className = 'addmode';
   $('addoredit').value = 'add';
   $('popconfig-add_account-title').innerHTML = 'Add SitePal Account';
   $('accountid').value = '';
   $('username').value = '';
   $('password').value = '';
   $('editkey').value = '';
   showid('popconfig-add_account');
}

// Gets confirmation before doing something drastic (i.e. deleting account)
function usure_href(txt, gohref) {
   var usure = window.confirm(txt);
   if ( usure == true ) {
      document.location.href = gohref;
   }
}

</script>


<?
# Verify stored accounts against api-returned info
sitepal_verify_accounts();

# header_nav
include("header_nav.inc.php");

# Grr
if ( eregi("MSIE", $_SERVER['HTTP_USER_AGENT']) ) { $ihateIE = "margin-top: 0;"; }
# content_container
echo "<div id=\"content_container\" style=\"".$ihateIE."\">\n";

if ( !sitepal_accountdata_exists() ) {
   /*---------------------------------------------------------------------------------------------------------*
    _  _          _                            _
   | \| | ___    /_\   __  __  ___  _  _  _ _ | |_
   | .` |/ _ \  / _ \ / _|/ _|/ _ \| || || ' \|  _|
   |_|\_|\___/ /_/ \_\\__|\__|\___/ \_,_||_||_|\__|

   # No Account! Go to setup screen.
   /*---------------------------------------------------------------------------------------------------------*/
   header("location: setup.php?showreport=".base64_encode($report[0])); exit;


} else {
   /*---------------------------------------------------------------------------------------------------------*
    _     _      _       _                            _
   | |   (_) ___| |_    /_\   __  __  ___  _  _  _ _ | |_  ___
   | |__ | |(_-<|  _|  / _ \ / _|/ _|/ _ \| || || ' \|  _|(_-<
   |____||_|/__/ \__| /_/ \_\\__|\__|\___/ \_,_||_||_|\__|/__/
   /*---------------------------------------------------------------------------------------------------------*/
   if ( sitepal_verified(true) ) {
      # ALL GOOD: You're good to go!
      echo "<h1>Your SitePal account info was verified successfully!</h1>\n";
      echo "<p class=\"subheading_explination_txt\">The SitePal feature is now fully enabled. You're all set up to drag-and-drop SitePal scenes onto your site pages.</p>\n";
      echo "<ol style=\"margin-top: 0; font-size: 11px; padding-left: 18px;\">\n";
      echo " <li><a href=\"http://".$_SESSION['docroot_url']."/sohoadmin/program/modules/open_page.php\">Open a page</a> in the Page Editor</li>\n";
      echo " <li>Drag-and-drop the \"SitePal\" object into one of the grid boxes</li>\n";
      echo " <li>Pick which of your SitePal scenes you'd like to use on that page</li>\n";
      echo " <li>Save your page and take a look!</li>\n";
      echo "</ol>\n";

   } else {
      # PROBLEM:  Something's wrong with your (only) account info (i.e., this doesn't show if multiple accounts and one is still good)
      echo "<h1>Problem: Something appears to be wrong with your SitePal account info.</h1>\n";
      echo "<p class=\"subheading_explination_txt\">Please see below and make sure your SitePal account info is current. \n";
      echo " Until this is corrected you will not be able to drag-and-drop SitePal characters onto pages, etc.</p>\n";
   }


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

      # For edit link(s)
      $edit_onclick = "edit_account('".$getAcc['account_id']."', '".$getAcc['username']."', '".$getAcc['password']."', '".urlencode($getAcc['account_title'])."', '".$getAcc['prikey']."');";

      # "Account 124346 - Soholaunch Enterprise"
      echo "<div class=\"account_box\">\n";
      echo " <span class=\"account_box-heading\">\n";
      echo $account_title_display;
//      echo "  <h4></h4>\n";
      echo "  <div class=\"editlink\">\n";
      # [delete]
      echo "   [ <span onclick=\"usure_href('Are you sure you want to delete this account? Any character behaviors associated with this account will also be deleted, and scenes placed on site pages may cease to function!', 'accounts.php?delete_account=".$getAcc['account_id']."');\" class=\"red uline hand\">Delete</span> ]&nbsp;&nbsp;&nbsp;\n";
      # [edit]
      echo "   [ <span onclick=\"".$edit_onclick."\" class=\"bold blue uline hand\" style=\"text-transform: uppercase;letter-spacing: .02em;\">Edit</span> ]\n";
      echo "  </div>\n";
      echo "  <div class=\"ie_cleardiv\"></div>\n";
      echo " </span>\n";

      # Problem with account?
      if ( $getAcc['status'] != "0" ) {
         # ERROR: Account cannot verify
         echo " <h4 class=\"red nomar_btm\">Error: Could not verify account</h4>\n";
         echo " <p class=\"nomar_top\"><b>Reason:</b> ".sitepal_status("status", $getAcc['status'])."</p>\n";
         echo " <p>Please <span class=\"blue uline hand\" onclick=\"".$edit_onclick."\">edit this account</span> and make sure that the information is current.\n";

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
            # Loop through and display thumbnails for configured scenes (exclude undefined silhouette thumbs)
            for ( $s = 0; $s < $max; $s++ ) {
               if ( eregi("jpg", $getScene[$s]['thumb']) && !eregi("silhouette", $getScene[$s]['thumb']) ) {
                  echo "  <img src=\"".$_SESSION['sitepal_BaseURL'].$getScene[$s]['thumb']."\"/>\n";
               }
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

echo "</div>\n";
//--- END: content_container

# popconfig-add_account
ob_start();
include("popup-add_account.inc.php");
$popup = ob_get_contents();
ob_end_clean();
echo help_popup("popconfig-add_account", "Add SitePal Account", $popup, "z-index: 999;width: 600px;left: 10%;top:10%;".$errdisplay);

# Re-display popup on error
if ( $_POST['todo'] == "verify_account_info" && $addedit_acc_error != "" ) {
   if ( $_POST['addoredit'] == "edit" ) {
      echo "<script type=\"text/javascript\">edit_account('".$_POST['accountid']."', '".$_POST['username']."', '".$_POST['password']."', '".$_POST['account_title']."');</script>\n";
   } else {
      echo "<script type=\"text/javascript\">add_account();</script>\n";
   }
}


# Grab module html into container var
$module_html = ob_get_contents();
ob_end_clean();

$module = new smt_module($module_html);
$module->add_breadcrumb_link("SitePal", "program/modules/sitepal/accounts.php");
$module->icon_img = "program/modules/sitepal/images/sitepal_logo.gif";
$module->heading_text = "Manage Accounts";
//$module->module_table_css = "margin: 0;width: 100%;height: 100%;border: 0px;";
$module->container_css = "padding: 0px;margin: 0px;";

# Which tab to show by default?
$module->bodyid = "accounts";

//$intro_text = "You must add at least one valid SitePal account to enable the other SitePal-related features.";
$intro_text = "View a quick summary of your SitePal account(s). If you have more than one account, you can add them all here and then whenever you're prompted to choose a scene (i.e. to place on a page) you'll be able to choose from all your scenes accross all your accounts.";
$module->description_text = $intro_text;

$module->good_to_go();
?>