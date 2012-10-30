<?php
error_reporting(E_PARSE);
if($_GET['_SESSION'] != '' || $_POST['_SESSION'] != '' || $_COOKIE['_SESSION'] != '') { exit; }
#===================================================================================================================================
# Soholaunch v4.91 > SitePal > Add Account popup
#===================================================================================================================================
?>

<div id="add_account">
 <!---sitepal account info form--->
 <form name="sitepal_form" method="post" action="<? echo basename($_SERVER['PHP_SELF']); ?>">
 <input type="hidden" name="todo" value="verify_account_info"/>
 <input type="hidden" id="addoredit" name="addoredit" value="add"/>
 <input type="hidden" id="editkey" name="editkey" value=""/>

 <!--- text-add_account -->
 <div id="text-add_account">

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
 </div>
 <!---END: text-add_account-->

 <!--- text-edit_account -->
 <div id="text-edit_account">
  Warning: If you edit these account details and change them from something that works to something that doesn't you'll
  lose access to your scenes from this account until you've put the right account info in again.
 </div>
 <!---END: text-edit_account-->


 <!---account_title_option-->
 <div id="account_title_option">
  <p><b>Account Name</b> (optional)<br/>
   Give this account a name/title for your own reference (i.e. "My One and Only Account", "Client XYZ").</p>
  <input id="account_title" type="text" name="account_title" value="<? echo $_POST['account_title']; ?>"/>
 </div>


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
 <div class="savebtn-container">
  <input id="btn-save_verify" type="button" <? echo $_SESSION['btn_save']; ?> value="Save & Verify &gt;&gt;" onclick="document.sitepal_form.submit();">
 </div>

</div>
<!---END: add_account-->