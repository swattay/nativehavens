<?
error_reporting(E_PARSE);
if($_GET['_SESSION'] != '' || $_POST['_SESSION'] != '' || $_COOKIE['_SESSION'] != '') { exit; }
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
   # VERIFIED!
   echo "   <h1>Quick Links</h1>\n";
   echo "   <ul style=\"margin-left: 0;margin-right: 15px;margin-top: 0; font-size: 11px; padding-left: 18px;list-style-type: square;\">\n";
   echo "    <li style=\"padding-top: 0px;\"><a href=\"#\" onclick=\"window.open('https://vhost.oddcast.com/admin/index.php');\">Log-in edit/manage your scenes at SitePal's website</li>\n";
   echo "    <li style=\"padding-top: 15px;\"><a href=\"http://".$_SESSION['docroot_url']."/sohoadmin/program/modules/open_page.php\">Open/Edit Pages</a></li>\n";
   $userid = "billytest";
   $checksum = md5($userid."XXXX");
   $gurl = urlencode("http://".$_SESSION['docroot_url']."/sohoadmin/program/modules/sitepal/management_module.php?todo=saveback");
//   echo "    <li style=\"padding-top: 15px;\"><a href=\"#\" onclick=\"window.open('http://host.oddcast.com/partner?uid=".$userid."&cs=".$checksum."&gurl=".$gurl."');\">Create Scene</a></li>\n";
   echo "   </ul>\n";
}

?>