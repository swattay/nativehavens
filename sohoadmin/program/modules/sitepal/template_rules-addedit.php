<?php
#===================================================================================================================================
# Soholaunch v4.91 > SitePal > Template Scene layer content include
#===================================================================================================================================
error_reporting(E_PARSE);
if($_GET['_SESSION'] != '' || $_POST['_SESSION'] != '' || $_COOKIE['_SESSION'] != '') { exit; }
session_start();
include_once($_SESSION['docroot_path']."/sohoadmin/program/includes/product_gui.php");
error_reporting(E_PARSE);

# Make sure sitepal db tables exist
include_once("create_dbtables-sitepal.php");

# Use Soholaunch Affiliate ID if none is specified by Web Host in Branding Controls
if ( $_SESSION['hostco']['sitepal_affiliate_id'] == "" ) {
   $_SESSION['hostco']['sitepal_affiliate_id'] = "27092";
}

# Centralize
$sitepal_signup_url = "http://www.oddcast.com/sitepal?&affId=".$_SESSION['hostco']['sitepal_affiliate_id']."&bannerId=0&promotionId=8243";
$edit_scene_url = "https://vhost.oddcast.com/admin/index.php";


/*---------------------------------------------------------------------------------------------------------*
   ____                   ___        ___             __ __
  / __/___ _ _  __ ___   / _ \ ___  / _/___ _ __ __ / // /_
 _\ \ / _ `/| |/ // -_) / // // -_)/ _// _ `// // // // __/
/___/ \_,_/ |___/ \__/ /____/ \__//_/  \_,_/ \_,_//_/ \__/

# Save changes to default template scene
/*---------------------------------------------------------------------------------------------------------*/
if ( $_POST['default'] > 0 ) {
   $qry = "update smt_sitepal_rules set";
   $qry .= " account_id = '".$_POST['default']['account_id']."'";
   $qry .= ", scene_id = '".$_POST['default']['scene_id']."'";
   $qry .= ", width = '".$_POST['default']['width']."'";
   $qry .= ", height = '".$_POST['default']['height']."'";
   $qry .= ", bgcolor = '".$_POST['default']['bgcolor']."'";
   $qry .= " where page_name = 'default'";
   $rez = mysql_query($qry);
}


ob_start();
?>

<!---Rules for this specific module-->
<link rel="stylesheet" type="text/css" href="module.css"/>

<script type="text/javascript">
function df_scene_dropdown() {
   // Get current value of default scene dropdown
   rawval = $('default_scene_dd').value;
   valArr = rawval.split("~~~");
   thumb = valArr[0];
   sceneid = valArr[1];
   accountid = valArr[2];

//   alert('sceneid = ['+sceneid+']');

   // Update hidden field values
   $('default_scene_id').value = sceneid;
   $('default_account_id').value = accountid;

   // Thumb preview
   $('scene_preview').src = '<? echo $_SESSION['sitepal_BaseURL']; ?>'+thumb;

   // Thumb size by width/height vals
//   $('scene_preview').style.width = $('default_width').value;
//   $('scene_preview').style.height = $('default_height').value;
}
</script>

<?
# header_nav
include("header_nav.html");

echo "<div id=\"content_container\">\n";

# Templates with SitePal assigned to one of their boxes?
$qry = "select * from PROMO_BOXES where content_type = 'sitepal'";
$rez = mysql_query($qry);
if ( mysql_num_rows($rez) < 1 ) {

   # NO - Set one of your boxes to "SitePal Character"
   echo "<div>\n";
   echo " <h2>SitePal character not yet placed in template(s)</h2>\n";
   echo " You do not currently have a SitePal character in your template(s). \n";
   echo " Assign a template via Template Manager that incorporates template box's, \n";
   echo " then go to Template Box manager and choose &quot;SitePal Character&quot; \n";
   echo " for the content of one of the boxes.";
   echo "</div>\n";

} else {
   # YES - Show template character options

   # Default template scene
   $qry = "select * from smt_sitepal_rules where page_name = 'default'";
   $rez = mysql_query($qry);
   $getDefault = mysql_fetch_assoc($rez);

   echo "<div class=\"account_box\">\n";

   # <form>
   echo " <form id=\"default_scene_form\" method=\"post\" action=\"".$_SERVER['PHP_SELF']."\">\n";
   echo " <input type=\"hidden\" id=\"default_scene_id\" name=\"default[scene_id]\" value=\"".$getDefault['scene_id']."\">\n";
   echo " <input type=\"hidden\" id=\"default_account_id\" name=\"default[account_id]\" value=\"".$getDefault['account_id']."\">\n";
   echo " <h4>Default Character</h4>\n";
   echo " <p class=\"subheading_explination_txt\">What should display by default if no special rules are defined? \n";
   echo "  Example: When visitors view a site page that doesn't have a special SitePal scene/audio assigned to it.<p>\n";

   echo "  <label>Which scene?</label>\n";
   echo "  <select id=\"default_scene_dd\" class=\"sitepal_scene_dropdown\" onchange=\"df_scene_dropdown();\">\n";

   # Build scene dd options
   $sp_scenes = sitepal_get_scenes();
   $numscenes = count($sp_scenes);
   foreach ( $sp_scenes as $account_id=>$scenes ) {
      $numscenes = count($scenes);
      for ( $s = 0; $s < $numscenes; $s++ ) {
         if ($tmp == "#EFEFEF") { $tmp = "WHITE"; } else { $tmp = "#EFEFEF"; }
         if ( $getDefault['scene_id'] == $scenes[$s]['number'] ) { $selected = " selected"; } else { $selected = ""; }
         echo "        <option value=\"".$scenes[$s]['thumb']."~~~".$scenes[$s]['number']."~~~".$account_id."\" style=\"background: ".$tmp.";\"".$selected.">".$scenes[$s]['name']."</option>\n";
      }
   }
   echo "  </select>\n";

   echo "  <table border=\"0\" cellpadding=\"4\" cellspacing=\"0\" style=\"width: 100%;\">\n";
   echo "   <tr>\n";
   echo "    <td>\n";
   echo "     <fieldset class=\"scene_properties\">\n";
   echo "      <legend>Scene Properties</legend>\n";
   echo "      <p><label>Width:</label>\n";
   echo "      <input type=\"text\" id=\"default_width\" name=\"default[width]\" value=\"".$getDefault['width']."\" style=\"width: 60px;\" onkeyup=\"df_scene_dropdown();\"></p>\n";
   echo "      <p><label>Height:</label>\n";
   echo "      <input type=\"text\" id=\"default_height\" name=\"default[height]\" value=\"".$getDefault['height']."\" style=\"width: 60px;\" onkeyup=\"df_scene_dropdown();\"></p>\n";
   echo "      <p><label>Background Color:</label>\n";
   echo "      #<input type=\"text\" id=\"default_bgcolor\" name=\"default[bgcolor]\" value=\"".$getDefault['bgcolor']."\" style=\"width: 80px;\" onkeyup=\"df_scene_dropdown();\"></p>\n";
   echo "     </fieldset>\n";
   echo "    </td>\n";

   # thumbnail preview
   echo "    <td align=\"center\">\n";
//   echo "     <label>Thumbnail Preview...</label>\n";
//   echo "     <div style=\"width: 200px;height: 200px;overflow: auto;border: 1px dotted #ccc;\">\n";
   echo "      <img id=\"scene_preview\" src=\"".$_SESSION['sitepal_BaseURL'].$getDefault['account_id']."/thumbs/show_".$getDefault['scene_id'].".jpg\"/>\n";
//   echo "     </div>\n";
   echo "    </td>\n";
   echo "   </tr>\n";
   echo "  </table>\n";


   # [save]
   echo "  <div class=\"savebtn-container\">\n";
   echo "   <input id=\"btn-save_verify\" type=\"button\" ".$_SESSION['btn_save']." value=\"Save &gt;&gt;\" onclick=\"$('default_scene_form').submit();\">\n";
   echo "  </div>\n";
   echo " </form>\n"; // End <form id=default_scene_form>
   echo "</div>\n";

   # Call preview function onload to show
   echo "<script type=\"text/javascript\">df_scene_dropdown();</script>\n";

   # [+] Add SitePal Account
   echo "  <p id=\"container-add_account_btn\"><input id=\"btn-add_account\" type=\"button\" value=\"[+] Add Special Rule\" onclick=\"add_account();\" ".$_SESSION['btn_build']."></p>\n";

} // End else boxes found with sitepal assigned to them


echo "</div>\n";
//--- END: content_container


# Grab module html into container var
$module_html = ob_get_contents();
ob_end_clean();

$module = new smt_module($module_html);
$module->add_breadcrumb_link("SitePal", "program/modules/sitepal/management_module.php");
$module->add_breadcrumb_link("Template Characters", "program/modules/sitepal/template_rules.php");
$module->icon_img = "program/modules/sitepal/sitepal_logo-full.gif";
$module->heading_text = "Template Character";
$module->module_table_css = "margin: 0;width: 100%;border: 0px;background-color: #fff;";
$module->container_css = "padding: 0px;margin: 0px;";

# Which tab to show by default?
//$module->bodyid = "accounts";
$module->bodyid = "template"; // testing

$intro_text = "You have set up your Template Boxes settings so that one of your template boxes contains a SitePal character.";
$module->description_text = $intro_text;

$module->good_to_go();
?>