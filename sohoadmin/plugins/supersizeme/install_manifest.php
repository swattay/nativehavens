<?
# PLUGIN INFO
$plugin_folder = "SuperSizeMe";
$plugin_title = "Super Size Me";
$plugin_version = "1.0";
$plugin_author = "cica";
$plugin_homepage = "www.cicawebdesign.com";
$plugin_icon = "images/SSM.gif";

# Description text
$plugin_description = "Expand the SohoAdmin window to 1024x768.";

# Code replacement
hook_replace("sohoadmin/version.php", "version-repl.php");
?>