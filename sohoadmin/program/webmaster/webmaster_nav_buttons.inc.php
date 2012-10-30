<?php
error_reporting(E_PARSE);
if($_GET['_SESSION'] != '' || $_POST['_SESSION'] != '' || $_COOKIE['_SESSION'] != '') { exit; }

#======================================================================================
# This outputs the row of buttons displayed on each screen in the Webmaster feature
# Included by: webmaster.php, global_settings.php, etc.
#======================================================================================

# Buttons use this function (should eventually go in asmt_javascript.php or something like that)
$THIS_DISPLAY .= "<script type=\"text/javascript\">\n";
$THIS_DISPLAY .= "function navto(a) {\n";
$THIS_DISPLAY .= "   window.location = a+\"?=".SID."\"";
$THIS_DISPLAY .= "}\n";
$THIS_DISPLAY .= "</script>\n";


# This is the row of buttons displayed in Webmaster, Global Settings, and Meta Tag Data
$THIS_DISPLAY .= "<table border=\"0\" cellpadding=3 cellspacing=\"0\" width=\"700px\" align=\"center\">\n";

$THIS_DISPLAY .= " <tr>\n";

# Administrator Logins
$THIS_DISPLAY .= "  <td align=\"center\">\n";
$THIS_DISPLAY .= "   <input type=\"button\" value=\"".lang("Administrator Logins")."\" ".$nav_main." style=\"width: 145px;\" onClick=\"navto('webmaster.php');\"></td>\n";

# Global Settings
$THIS_DISPLAY .= "  <td align=\"center\">\n";
$THIS_DISPLAY .= "   <input type=\"button\" value=\"".lang("Global Settings")."\" ".$nav_main." onClick=\"navto('global_settings.php');\"></td>\n";

# Business info
$THIS_DISPLAY .= "  <td align=\"center\">\n";
$THIS_DISPLAY .= "   <input type=\"button\" value=\"".lang("Business Info")."\" ".$nav_main." onClick=\"navto('business_info.php');\"></td>\n";

# Search Engine Ranking
$THIS_DISPLAY .= "  <td align=\"center\">\n";
$THIS_DISPLAY .= "   <input type=\"button\" value=\"".lang("Search Engine Ranking")."\" ".$nav_main." style=\"width: 155px;\" onClick=\"navto('meta_data.php');\"></td>\n";

# software updates
if ( updates_allowed() ) {
   $THIS_DISPLAY .= "  <td align=\"center\">\n";
   $THIS_DISPLAY .= "   <input type=\"button\" value=\"".lang("Software Updates")."\" ".$nav_main." style=\"width: 125px;\" onClick=\"navto('software_updates.php');\"></td>\n";
}

# FAQ Manager
//$THIS_DISPLAY .= "  <td align=\"center\"><input type=\"button\" value=\"".lang("FAQ Manager")."\" ".$nav_main." onClick=\"navto('faq_manager.php');\" style='width: 150px;'></td>\n";

# Site Backup / Resotore
//if ( $SECURE_MOD_LICENSE == 1 ) { $THIS_DISPLAY .= "  <td align=\"center\"><input type=\"button\" value=\"Site Backup/Restore\" onClick=\"navto('backup_restore.php');\" ".$nav_main." style='width: 150px;'></td>\n"; }

$THIS_DISPLAY .= " </tr>\n";
$THIS_DISPLAY .= "</table>\n";




?>