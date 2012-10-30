<?
error_reporting(E_PARSE);
if($_GET['_SESSION'] != '' || $_POST['_SESSION'] != '' || $_COOKIE['_SESSION'] != '') { exit; }
#=====================================================================
# Soholaunch v4.91 > SitePal > Add Account popup
#====================================================================

error_reporting(E_PARSE);
session_start();
include_once($_SESSION['docroot_path']."/sohoadmin/program/includes/product_gui.php");
error_reporting(E_PARSE);

# Whose userdata fields will be manipulated by this plugin? [plugin folder name]'s userdata fields, that's whose.
$sitepal = new userdata("sitepal");

# Make sure sitepal db tables exist
include_once("create_dbtables-sitepal.php");

# Use Soholaunch Affiliate ID if none is specified by Web Host in Branding Controls
if ( $_SESSION['hostco']['sitepal_affiliate_id'] == "" ) {
   $_SESSION['hostco']['sitepal_affiliate_id'] = "27092";
}
?>

<link rel="stylesheet" type="text/css" href="../../smt_module.css"/>
<link rel="stylesheet" type="text/css" href="module.css"/>

<div id="add_account">
 <!---sitepal account info form--->
 <form name="sitepal_form" method="post" action="<? echo $_SERVER['PHP_SELF']; ?>">
 <input type="hidden" name="todo" value="verify_account_info">
<?
# You need an account!
echo "<h2>Please fill-in your SitePal account information</h2>\n";

# Only show new user instructions if adding first account
if ( !sitepal_verified() ) {
   # Sign-Up!
   echo "<p class=\"nomar_top\">You must have a valid SitePal account in order to use the SitePal features.\n";
   echo "Once you <b>sign-up for an account</b> on <a href=\"#\" onclick=\"window.open('".$sitepal_signup_url."');\">SitePal's website</a>, \n";
   echo "you'll be given an account id, username, and password.\n";
   echo "When you get that info from SitePal, <b>come back here, fill-in the fields</b> below, and hit 'Save Settings'. \n";
   echo "Doing that <b>will 'turn on' this feature</b>, and you'll be able to drag-and-drop your characters on to your site pages.\n";
   echo "</p>\n";
} else {
   # Add another account
   echo "<p class=\"nomar_top\">If you have more than one SitePal account, you can add them each here. \n";
   echo "Once you do this you'll be able to access all of your scene from all of your accounts without having to go back-and-forth.\n";
   echo "</p>\n";
}
?>
 <table border="0" cellspacing="0" cellpadding="3">
  <tr>
   <td><b>SitePal Account ID:</b></td>
   <td>
    <input id="accountid" type="text" name="accountid" value="<? echo $_POST['accountid']; ?>"/>
   </td>
  </tr>

  <tr>
   <td><b>SitePal Username:</b></td>
   <td><input id="username" type="text" name="username" value="<? echo $_POST['username']; ?>"/></td>
  </tr>

  <tr>
   <td><b>SitePal Password:</b></td>
   <td><input id="password" type="text" name="password" value="<? echo $_POST['password']; ?>"/></td>
  </tr>
 </table>
 </form>

 <!--savebtn-container-->
 <div id="savebtn-container">
  <input id="cancelbtn" type="button" <? echo $_SESSION['btn_edit']; ?> value="[x] Cancel" onclick="parent.killDialog();">
  <input id="savebtn" type="button" <? echo $_SESSION['btn_save']; ?> value="Save & Verify &gt;&gt;" onclick="document.sitepal_form.submit();">
  <!--- <input id="editbtn" type="button" <? echo $_SESSION['btn_edit']; ?> value="Edit/Change Account Info" onclick="document.location.href='management_module.php?edit_info=yes';" style="display: <? echo $editbtn_display; ?>;"> -->
 </div>

</div>
<!---END: add_account-->