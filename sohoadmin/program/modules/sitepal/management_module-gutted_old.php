<?
error_reporting(E_ALL);
//apd_set_pprof_trace();
error_reporting(E_PARSE);
if($_GET['_SESSION'] != '' || $_POST['_SESSION'] != '' || $_COOKIE['_SESSION'] != '') { exit; }
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

# Make sure sitepal db tables exist
include_once("create_dbtables-sitepal.php");

# Use Soholaunch Affiliate ID if none is specified by Web Host in Branding Controls
if ( $_SESSION['hostco']['sitepal_affiliate_id'] == "" ) {
   $_SESSION['hostco']['sitepal_affiliate_id'] = "27092";
}

# Get current status of any saved sitepal accounts
//sitepal_verify_accounts();

# Centralize
$sitepal_signup_url = "http://www.oddcast.com/sitepal?&affId=".$_SESSION['hostco']['sitepal_affiliate_id']."&bannerId=0&promotionId=8243";
$edit_scene_url = "https://vhost.oddcast.com/admin/index.php";

ob_start();
?>

<script type="text/javascript">
// Gets confirmation before redirecting to passed url
function confirm_delete(url) {
   var usure = window.confirm("Are you sure you want to delete this link?");

   if ( usure == true ) {
      document.location.href=url;
   }
}

// Changes id of body for sweet css-based tab layer switching
function setBodyid(idname) {
   document.body.id = idname;
   // Set focus to body to clear browser-induced dotted border around clicked element (I'm OCD like that)
   $('my_accounts').focus();
}
</script>


<?
# header_nav
include("header_nav.html");
?>

<div id="content_container" style="margin-top: 30px;">
<!---Parent table--->
<table width="100%" border="0" align="center" cellpadding="5" cellspacing="0">
 <tr>
  <td valign="top">

   <!---my_accounts-->
   <div id="my_accounts">
    <h1>My SitePal Account(s)</h1>
<?
# My Accounts content include
include("my_accounts.inc.php");
?>

   </div>
   <!---END: my_accounts-->

   <!---cURL error-->
   <div id="error-why_disabled" style="display: none;">
    <b>Problem:</b> cURL library not installed on this web server.<br/>
    This is why the fields are disabled.
    <span class="red uline hand" onclick="showid('error-no_curl');">See the detailed error message</span>.
   </div>

   <!---template_scene-->
   <div id="template_scene">
    <? include("template_scene.inc.php"); ?>
   </div>
   <!---END: template_template-->

   <!---my_scenes-->
   <div id="my_scenes">
    <h1>My Scenes</h1>
<?
# Pull scene list
$sitepal_scenes = sitepal_get_scenes("", true);
echo testArray($sitepal_scenes);
?>
   </div>
   <!---END: my_scenes-->

   <!---my_audio-->
   <div id="my_audio">
    <h1>My Audio</h1>
<?
# Pull scene list
$sitepal_scenes = sitepal_get_scenes("", true);
echo testArray($sitepal_scenes);
?>
   </div>
   <!---END: my_audio-->

  </td>
  <td valign="top" width="30%">
   SohoPal&hellip;
<?
# Soholaunch's SitePal character/links
//include("soho_sitepal_character.inc.php");
?>
  </td>
 </tr>
 <tr>
  <td colspan="2" valign="top">&nbsp;</td>
 </tr>
</table>

</div>


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

# Grab module html into container var
$module_html = ob_get_contents();
ob_end_clean();

$module = new smt_module($module_html);
$module->add_breadcrumb_link("SitePal", "program/modules/sitepal/management_module.php");
$module->icon_img = "program/modules/sitepal/sitepal_logo-full.gif";
$module->heading_text = "SitePal";
$module->module_table_css = "margin: 0;width: 100%;border: 0px;background-color: #fff;";
$module->container_css = "padding: 0px;margin: 0px;";

# Which tab to show by default?
$module->bodyid = "accounts";

$intro_text = "Manage your SitePal virtual characters. Drag-and-drop your scenes onto your website.";
$module->description_text = $intro_text;

$module->good_to_go();
?>