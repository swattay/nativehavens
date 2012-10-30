<?


# PLUGIN INFO
$plugin_folder = "embed_audio";
$plugin_title = "Embed Page Audio";
$plugin_version = "1.1";
$plugin_description = "Add background music to your pages!  In the page editor, drag the 'Embed Audio' object to the page and select your audio file!";
$plugin_author = "Joe Lain";
$plugin_homepage = "http://soholaunch.com";
$plugin_icon = "embed_icon.gif";
$plugin_options_link = "";


# Page Editor
$data = array();
$data['draggable_object_image'] = "eng-icon_embed.gif";
$data['draggable_object_id'] = "embed_obj";
$data['properties_dialog_id'] = "embedlayer";
$data['mod_folder'] = $plugin_folder;
hook_special("page_editor_object", $data);

hook_attach("embed_js.php", "pe-place_object_js");
hook_attach("embed_props.php", "pe-properties_dialog_layer");

# Misc
hook_attach("embed_write.php", "pe-confile_object_data");


?>