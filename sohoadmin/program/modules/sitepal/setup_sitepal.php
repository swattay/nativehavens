<?
#===============================================
# Manage module for SitePal plugin
/* Script Outline
-easy to get started

| Does account data exist in db?
| -> no: fist-time setup layout
| -> yes: attempt to verify
     ->
*/
#==============================================

error_reporting(E_PARSE);
session_start();
include_once($_SESSION['docroot_path']."/sohoadmin/program/includes/product_gui.php");
error_reporting(E_PARSE);

# Whose userdata fields will be manipulated by this plugin? [plugin folder name]'s userdata fields, that's whose.
$sitepal = new userdata("sitepal");

# Use Soholaunch Affiliate ID if none is specified by Web Host in Branding Controls
if ( $_SESSION['hostco']['sitepal_affiliate_id'] == "" ) {
   $_SESSION['hostco']['sitepal_affiliate_id'] = "27092";
}

# Centralize
$sitepal_signup_url = "http://www.oddcast.com/sitepal?&affId=".$_SESSION['hostco']['sitepal_affiliate_id']."&bannerId=0&promotionId=8243";
$edit_scene_url = "https://vhost.oddcast.com/admin/index.php";

ob_start();
/*--------------------------------------------------------------------------------------------------------*
 _   __           _  ___        ___                             __
| | / /___  ____ (_)/ _/__ __  / _ | ____ ____ ___  __ __ ___  / /_
| |/ // -_)/ __// // _// // / / __ |/ __// __// _ \/ // // _ \/ __/
|___/ \__//_/  /_//_/  \_, / /_/ |_|\__/ \__/ \___/\_,_//_//_/\__/
                      /___/
# verify_account_info
# Check/save submitted account info
# https://vhost.oddcast.com/mng/testMngAccountInfo.php
/*---------------------------------------------------------------------------------------------------------*/
if ( $_POST['todo'] == "verify_account_info" ) {

   # Socket-post account id, un, and pw to sitepal api
   # ...kicks back with "must use https" error -- gotta find a workaround
   /*----------------------------------------------------------------------*/
   # Target url and script
   $scHost = "vhost.oddcast.com";
   $scPath = "/mng/mngAccountInfo.php";

   # Format data to pass to SitePal API
   $sitepal_post_data = "AccountID=".$_POST['accountid']."&User=".$_POST['username']."&Pswd=".$_POST['password'];

   # If good, save in db; if error, kick back and show error msg
   if ( sitepal_verified($_POST['accountid'], $_POST['username'], $_POST['password']) ) {
      echo "<h2 style=\"color: #339959;\">Account data verified and saved!</h2>";

      # Store filepath in db for future re-access (beyond session)
      $sitepal->set("accountid", $_POST['accountid']);
      $sitepal->set("username", $_POST['username']);
      $sitepal->set("password", $_POST['password']);

   } else {
      echo "<h2 style=\"color: #980000;\">Invalid account data. Authentication failed.</h2>";

      # TESTING: Store bad acc info in db to test pre-verify display/functionality
      $sitepal->set("accountid", $_POST['accountid']);
      $sitepal->set("username", $_POST['username']);
      $sitepal->set("password", $_POST['password']);
   }

} // end if todo = verify_account_info"


# Pull stored settings from db
$getSp = $sitepal->get();

# Pre-populate form fields with data from db if they didn't just try a post
if ( !isset($_POST['accountid']) ) {
   $_POST['accountid'] = $getSp['accountid'];
   $_POST['username'] = $getSp['username'];
   $_POST['password'] = $getSp['password'];
}


?>

<!---Rules for this specific module-->
<link rel="stylesheet" type="text/css" href="module.css"/>

<style>
#accountid {
   font-family: verdana, arial, helvetica, sans-serif;
   font-size: 10px;
   width: 65px;
}

#username {
   font-family: verdana, arial, helvetica, sans-serif;
   font-size: 10px;
   width: 220px;
}

#password {
   font-family: verdana, arial, helvetica, sans-serif;
   font-size: 10px;
   width: 100px;
}

h1 { font-size: 13px; margin-bottom: 0; }

.help_link {
   color: #ff7900;
   text-decoration: underline;
   cursor: pointer;
}

#savebtn-container {
   text-align: right;
}
</style>

<script language="javascript">
// Gets confirmation before redirecting to passed url
function confirm_delete(url) {
   var usure = window.confirm("Are you sure you want to delete this link?");

   if ( usure == true ) {
      document.location.href=url;
   }
}

/*
    Written by Jonathan Snook, http://www.snook.ca/jonathan
    Add-ons by Robert Nyman, http://www.robertnyman.com
*/
// If this works it's SO going in js_functions.php
function getElementsByClassName(oElm, strTagName, oClassNames){
    var arrElements = (strTagName == "*" && oElm.all)? oElm.all : oElm.getElementsByTagName(strTagName);
    var arrReturnElements = new Array();
    var arrRegExpClassNames = new Array();
    if(typeof oClassNames == "object"){
        for(var i=0; i<oClassNames.length; i++){
            arrRegExpClassNames.push(new RegExp("(^|\\s)" + oClassNames[i].replace(/\-/g, "\\-") + "(\\s|$)"));
        }
    }
    else{
        arrRegExpClassNames.push(new RegExp("(^|\\s)" + oClassNames.replace(/\-/g, "\\-") + "(\\s|$)"));
    }
    var oElement;
    var bMatchesAll;
    for(var j=0; j<arrElements.length; j++){
        oElement = arrElements[j];
        bMatchesAll = true;
        for(var k=0; k<arrRegExpClassNames.length; k++){
            if(!arrRegExpClassNames[k].test(oElement.className)){
                bMatchesAll = false;
                break;
            }
        }
        if(bMatchesAll){
            arrReturnElements.push(oElement);
        }
    }
    return (arrReturnElements)
}


// Turn a particular tab 'on' and all others 'off'
// Note: content layer id and tab id should be exactly the same except prefix, like "tab-mylayer" and "container-mylayer"
function switch_tab(targettabid) {
   tabs = getElementsByClassName(document, "div", "tab");

   var numtabs = tabs.length;
   for ( t = 0; t < numtabs; t++ ) {
//      alert('id: ['+tabs[t].id+']');

      // Rebuild name of associated content layer
      contentid = tabs[t].id.replace("tab-", "container-");

      if ( tabs[t].id == targettabid ) {
         // Turn tab ON
         tabs[t].className = 'tab tab-on';

         // SHOW content layer
         $(contentid).style.display = 'block';

      } else {
         // Turn tab ON
         tabs[t].className = 'tab tab-off';

         // HIDE content layer
         $(contentid).style.display = 'none';
      }
   }
}

</script>

<?
# Verify stored accounts against api-returned info
sitepal_verify_accounts();

include("header_nav.html");

?>


<!--- content_container -->
<div id="content_container" style="border: 1px solid red;">

<!---Parent table--->
<table width="100%" border="0" align="center" cellpadding="5" cellspacing="0">
 <tr>
  <td valign="top">

   <!---account_form-->
   <div id="container-account_info">
    <h1>Please fill-in your SitePal account information</h1>
    <p class="nomar_top">You must have a SitePal account in order to use the SitePal features.
    Once you <b>sign-up for an account</b> on <a href="#" onclick="window.open('".$sitepal_signup_url."');">SitePal's website</a>,
    you'll be given an account id, username, and password.
    When you get that info from SitePal, <b>come back here, fill-in the fields</b> below, and hit 'Save Settings'.
    Doing that <b>will 'turn on' this feature</b>, and you'll be able to drag-and-drop your characters on to your site pages.
    </p>

   <!---sitepal account info form--->
   <form name="sitepal_form" method="post" action="<? echo $_SERVER['PHP_SELF']; ?>">
   <input type="hidden" name="todo" value="verify_account_info">
   <table border="0" cellspacing="0" cellpadding="3">
    <tr>
     <td><b>SitePal Account ID:</b></td>
     <td>
      <input id="accountid" type="text" name="accountid" value="<? echo $_POST['accountid']; ?>"<? echo $form_disabled; ?>>
     </td>
    </tr>

    <tr>
     <td><b>SitePal Username:</b></td>
     <td><input id="username" type="text" name="username" value="<? echo $_POST['username']; ?>"<? echo $form_disabled; ?>/></td>
    </tr>

    <tr>
     <td><b>SitePal Password:</b></td>
     <td><input id="password" type="text" name="password" value="<? echo $_POST['password']; ?>"<? echo $form_disabled; ?>/></td>
    </tr>
   </table>
   </form>

   <!---cURL error-->
   <div id="error-why_disabled" style="display: none;">
    <b>Problem:</b> cURL library not installed on this web server.<br/>
    This is why the fields are disabled.
    <span class="red uline hand" onclick="showid('error-no_curl');">See the detailed error message</span>.
   </div>

   <!--savebtn-container-->
   <div id="savebtn-container">
    <input id="cancelbtn" type="button" <? echo $_SESSION['btn_edit']; ?> value="[x] Cancel" onclick="document.location.href='management_module.php';" style="display: <? echo $cancelbtn_display; ?>;">
    <input id="savebtn" type="button" <? echo $_SESSION['btn_save']; ?> value="<? echo $savebtn_txt; ?>" onclick="document.sitepal_form.submit();" style="display: <? echo $savebtn_display; ?>;">
    <input id="editbtn" type="button" <? echo $_SESSION['btn_edit']; ?> value="Edit/Change Account Info" onclick="document.location.href='management_module.php?edit_info=yes';" style="display: <? echo $editbtn_display; ?>;">
   </div>

   </div>
   <!---END: account_form-container-->

  </td>

<?
/*---------------------------------------------------------------------------------------------------------*
 ___       _          ___        _
/ __| ___ | |_   ___ | _ \ __ _ | |
\__ \/ _ \| ' \ / _ \|  _// _` || |
|___/\___/|_||_|\___/|_|  \__,_||_|

# Sidebar with Soholaunch's SitePal character and quick links
/*---------------------------------------------------------------------------------------------------------*/

# POPUP: How to use the SitePal feature
$popup_content = "";
$popup_content .= "  <ol style=\"margin-top: 0; font-size: 11px; padding-left: 18px;\">\n";
$popup_content .= "   <li>Fill-in your SitePal account information and hit 'Save Settings'</li>\n";
$popup_content .= "   <li>Open a page in the Page Editor</li>\n";
$popup_content .= "   <li>Drag-and-drop the \"SitePal\" object into one of the grid boxes</li>\n";
$popup_content .= "   <li>Pick which of your SitePal scenes you'd like to use on that page</li>\n";
$popup_content .= "   <li>Save your page and take a look!</li>\n";
$popup_content .= "  </ol>\n";
echo help_popup("help-howto_use", "How to use the SitePal feature", $popup_content, "left: 5%;top: 10%;");
?>
  <td valign="top" width="30%">

<?
# Show special SitePal-Soholaunch scene!
include("sitepal_spokeswoman.php");

# Show diffent options for before/after filling in SitePal account information
if ( !sitepal_verified("", "", "", true) ) {
   # Not set up yet
   echo "   <ul style=\"margin-left: 0;margin-right: 15px;margin-top: 0; font-size: 11px; padding-left: 18px;list-style-type: square;\">\n";
   echo "    <li>Don't have a SitePal account? \n";
   echo "     <a href=\"#\" onclick=\"window.open('".$sitepal_signup_url."');\">Get one here</a>.\n";
   echo "    </li>\n";
   echo "    <li style=\"padding-top: 10px;\"><span class=\"help_link\" onclick=\"showid('help-howto_use');\">How do I use this feature?</span></li>\n";
   echo "   </ul>\n";

} else {
   # VERIFIED
   echo "   <h1>Quick Links</h1>\n";
   echo "   <ul style=\"margin-left: 0;margin-right: 15px;margin-top: 0; font-size: 11px; padding-left: 18px;list-style-type: square;\">\n";
   echo "    <li style=\"padding-top: 0px;\"><a href=\"#\" onclick=\"window.open('https://vhost.oddcast.com/admin/index.php');\">Log-in edit/manage your scenes at SitePal's website</li>\n";
   echo "    <li style=\"padding-top: 15px;\"><a href=\"<a href=\"http://".$_SESSION['docroot_url']."/sohoadmin/program/modules/open_page.php\">Open/Edit Pages</a></li>\n";
   echo "   </ul>\n";
}

?>
  </td>
 </tr>
 <tr>
  <td colspan="2" valign="top">&nbsp;</td>
 </tr>
</table>


<?
# Module requires cURL support
if ( !function_exists("curl_setopt") ) {
   # POPUP: Error - cURL not installed on server
   $popup_content = "<p>The SitePal feature depends upon certain php functions that do not appear to be available on the web server that your website is hosted on.</p>";

   $popup_content .= "<p><b>To fix:</b> \n";
   $popup_content .= "Contact your web host and ask them to install \"the curl library for php\" on your server.\n";
   $popup_content .= "They should know what you're talking about.</p>\n";

   $popup_content .= "<p style=\"margin-bottom: 35px;\">In case it's helpful: instructions on installing CURL for PHP can be found \n";
   $popup_content .= "<a href=\"http://us3.php.net/manual/en/ref.curl.php\">here</a>.\n";
   $popup_content .= "Fair warning though: These instructions are <i>not</i> begginner-friendly.\n";
   $popup_content .= "</p>\n";

   echo help_popup("error-no_curl", "Problem: CURL library not installed on this web server", $popup_content, "top: 15%;left:20%;");

   # Go ahead and display it (and disable all the fields)
   echo "<script type=\"text/javascript\">\n";
   echo "showid('error-no_curl');\n";
   echo "$('accountid').disabled='true';\n";
   echo "$('username').disabled='true';\n";
   echo "$('password').disabled='true';\n";
   echo "$('save_settings').style.display='none';\n";
   echo "$('error-why_disabled').style.display='block';\n";
   echo "</script>\n";
}

echo "</div>"; // End content_container div

# Grab module html into container var
$module_html = ob_get_contents();
ob_end_clean();

$module = new smt_module($module_html);
$module->add_breadcrumb_link("SitePal", "program/modules/sitepal/accounts.php");
$module->icon_img = "program/modules/sitepal/images/sitepal_logo.gif";
$module->heading_text = "SitePal";
$module->container_css = "padding: 0px;margin: 0px;";

$intro_text = "Manage your SitePal virtual characters. Drag-and-drop your scenes onto your website.";
$module->description_text = $intro_text;

$module->good_to_go();
?>