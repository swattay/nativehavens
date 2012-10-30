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

# Make sure sitepal db tables exist
include_once("create_dbtables-sitepal.php");

# Centralize
$edit_scene_url = "https://vhost.oddcast.com/admin/index.php";

# Report msg passed?
if ( $_GET['showreport'] != "" ) {
   $report[] = base64_decode($_GET['showreport']);
}

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
      $report[] = "<b>Error:</b> The account information you submitted does not appear to be valid: ". $api_response['Error'];
      $addedit_acc_error = "<b>Error:</b>". $api_response['Error'];
   }

} // end if todo = verify_account_info"

# Verify stored accounts against api-returned info
sitepal_verify_accounts();

//include("header_nav.inc.php");

# Verified?
if ( sitepal_verified(true) ) {
   # YES - Go to accounts/rules

   # Convenience: Auto-Direct to accounts after first setup or template rules if some exist
   $qry = "select prikey from smt_sitepal_rules";
   $rez = mysql_query($qry);

//   echo "<h1>location: accounts.php/rules.php</h1>"; exit;
   if ( mysql_num_rows($rez) > 2 ) {
      header("location: template_rules.php"); exit;
   } else {
      header("location: accounts.php"); exit;
   }
} else {
   # NO - account data gone bad or show first time setup form?
   if ( sitepal_accountdata_exists() ) {
      # Goto Accounts
//      echo "<h1>location: accounts.php</h1>"; exit;
      header("location: accounts.php"); exit;
   }
}

?>

<!---Rules for this specific module-->
<link rel="stylesheet" type="text/css" href="module.css"/>

<style>
* {
   font-family: Trebuchet MS, arial, helvetica, sans-serif;
}
form {
   margin: 0;
}

#accountid {
   /*font-family: verdana, arial, helvetica, sans-serif;*/
   font-size: 10px;
   width: 65px;
}

#username {
   /*font-family: verdana, arial, helvetica, sans-serif;*/
   font-size: 10px;
   width: 220px;
}

#password {
   /*font-family: verdana, arial, helvetica, sans-serif;*/
   font-size: 10px;
   width: 100px;
}

h1 { font-size: 17px; margin: 0; }
h2 { font-size: 14px; margin: 0; }

.help_link {
   color: #ff7900;
   text-decoration: underline;
   cursor: pointer;
}

#savebtn-container {
   text-align: right;
}

#setup_form, #setup_instructions {
   font-size: 11px;
   padding: 6px;
   background-color: #fff;
   border: 1px dotted #ccc;
}

#setup_arrow-container {
   padding: 3px 0 0;
   text-align: center;
}

/*#setup_form {
   padding: 8px;
   background-color: #fffab2;
   border: 1px solid #ccc;
}*/

#content_container {
   margin: 5px 3px 5px;
}

#signup_now-container {
   margin: 0;
   margin-top: 5px;
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




<!--- content_container -->
<div id="content_container">

<!---Parent table--->
<table width="100%" border="0" align="center" cellpadding="5" cellspacing="0">
 <tr>
  <td valign="top">

   <!---setup_instructions-->
   <div id="setup_instructions">
    <h1>Go get a SitePal account!</h1>
    <p class="nomar">You must have a SitePal account in order to use the SitePal features.
    Once you <b>sign-up for an account</b> on <a href="#" onclick="popup_window('<? echo $sitepal_signup_url; ?>');">SitePal's website</a>,
    you'll be given an account id, username, and password.
    When you get that info from SitePal, <b>come back here, fill-in the fields</b> below, and click the Save & Verify button.
    Doing that <b>will 'turn on' this feature</b>, and you'll be able to drag-and-drop your characters on to your site pages, etc.
    </p>

    <!--signup_now-->
    <div id="signup_now-container">
     <input id="signup_now" type="button" <? echo $_SESSION['btn_save']; ?> value="Sign-Up for SitePal Account &gt;&gt;" onclick="popup_window('<? echo $sitepal_signup_url; ?>');">
    </div>
   </div>
   <!---END: setup_instructions-->


   <!---setup_arrow-container-->
   <div id="setup_arrow-container">
    <img src="images/setup_arrow-down.gif"/>
   </div>
   <!---END: setup_arrow-container-->


   <!---sitepal account info form--->
   <div id="setup_form">
    <h1>Fill-in your SitePal account info to unlock features...</h1>
    <form name="sitepal_form" method="post" action="setup.php">
    <input type="hidden" name="todo" value="verify_account_info">
    <input type="hidden" name="addoredit" value="add"/>
    <table border="0" cellspacing="0" cellpadding="3">
     <tr>
      <td><b>SitePal Account ID:</b></td>
      <td>
       <input id="accountid" type="text" name="accountid" value="<? echo $_POST['accountid']; ?>">
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

    <!---cURL error-->
    <div id="error-why_disabled" style="display: none;">
     <b>Problem:</b> cURL library not installed on this web server.<br/>
     This is why the fields are disabled.
     <span class="red uline hand" onclick="showid('error-no_curl');">See the detailed error message</span>.
    </div>

    <!--savebtn-container-->
    <div id="savebtn-container">
     <input id="savebtn" type="button" <? echo $_SESSION['btn_save']; ?> value="Save & Verify &gt;&gt;" onclick="document.sitepal_form.submit();">
    </div>
   </div>
   <!---END: setup_form-->


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

# Not set up yet
echo "   <ul style=\"margin-left: 0;margin-right: 15px;margin-top: 0; font-size: 11px; padding-left: 18px;list-style-type: square;\">\n";
echo "    <li>Don't have a SitePal account?<br/>\n";
echo "     <a href=\"#\" onclick=\"popup_window('".$sitepal_signup_url."');\">Get one here</a>.\n";
echo "    </li>\n";
echo "    <li style=\"padding-top: 10px;\"><span class=\"help_link\" onclick=\"showid('help-howto_use');\" style=\"font-size: 100%;\">How do I use this feature?</span></li>\n";
echo "    <li>Already have a SitePal account?<br/>\n";
echo "     <a href=\"#\" onclick=\"window.open('".$edit_scene_url."');\">Log-in to look up your info &gt;&gt;</a>.\n";
echo "    </li>\n";
//$edit_scene_url
echo "   </ul>\n";

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
   echo "alert('Error: cURL is not installed on this server.');\n";
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
$module->module_table_css = "margin: 0;width: 100%;border: 0px;height: 100%;";

$intro_text = "Manage your SitePal virtual characters. Drag-and-drop your scenes onto your website.";
$module->description_text = $intro_text;

$module->good_to_go();
?>