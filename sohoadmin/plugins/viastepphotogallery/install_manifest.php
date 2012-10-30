<?


# PLUGIN INFO
$plugin_folder = "viastepphotogallery";
$plugin_title = "Premium Photo Gallery";
$plugin_version = "2.01";
$plugin_description = "Create Multiple galleries, slideshow, resize, upload single images, through zip or FTP location.";
$plugin_author = "ViaStep";
$plugin_homepage = "http://sohotemplates.com";
$plugin_icon = "plugin_icon2.png";
$plugin_options_link = "premium_album_module/premium_album.php";

# PLUGIN DATABASE TABLES
# If this plugin creates any database tables
# list them here so that Pro Edition knows to drop them
# during the un-installation process
$drop_tables[] = "premium_album_images";
$drop_tables[] = "premium_album";


# SPECIAL HOOK: Main menu button
# Use this if you want to place a button for your plugin on Pro Edition's Main Menu
# ...which links to some kind of config/managment module for your plugin.
# If your plugin doesn't involve a config/management module (as in, it runs exactly as installed with no further input needed from the user)
# then a main menu button probably isn't neccessary.
$data = array();
$data['enabled_button_image'] = "photo_new-on.png";
$data['disabled_button_image'] = "photo_new-off.png";
$data['enabled_button_link'] = "premium_album_module/premium_album.php";
$data['button_caption_text'] = "Photo Gallery";
$data['multiuser_access_code'] = ";MOD_PHOTO_ALBUM;";
hook_special("main_menu_button", $data);


# SPECIAL HOOK: Header nav buttons
$data = array();
$data['button1']['button_text'] = "Gallery Menu"; // This string will be passed through lang() for translation
$data['button1']['button_onclick'] = base64_encode("parent.body.location.href='../plugins/viastepphotogallery/premium_album_module/premium_album.php';"); // Do not use single quotes in this string

hook_special("header_nav_buttons", $data);


# Misc
hook_attach("realtime_builder_include.php", "rtb_contentloop");
hook_attach("thumbnail_image_functions.php", "global_function");
hook_attach("english_strings.php", "english_strings");


# Page Editor
$data = array();
$data['draggable_object_image'] = "eng-icon_premiumalbum.gif";
$data['draggable_object_id'] = "vsgallery_object";
$data['properties_dialog_id'] = "premiumlayer";
$data['mod_folder'] = $plugin_folder;
$data['properties_dialog_file'] = $plugin_folder.DIRECTORY_SEPARATOR."drag_object-props.php";
$data['place_object_js_function'] = $plugin_folder.DIRECTORY_SEPARATOR."place_album_js.php";
hook_special("page_editor_object", $data);


hook_attach("place_album_js-ff.php", "pe_ff-place_object_js");
hook_attach("drag_object-props-ff.php", "pe_ff-properties_dialog_layer");

hook_attach("place_album_js.php", "pe-place_object_js");
hook_attach("drag_object-props.php", "pe-properties_dialog_layer");

hook_attach("confile_write_object.php", "pe-confile_object_data");


?>