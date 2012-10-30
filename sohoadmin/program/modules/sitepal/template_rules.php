<?php
//apd_set_pprof_trace(); $report[] = "ProcessID: [".getmypid()."]";
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

# Redirect to accounts if not verified
if ( !sitepal_verified(true) ) {
   header("location: accounts.php");
}

# Delete template rules
if ( $_GET['killrule'] != "" ) {
   $qry = "delete from smt_sitepal_rules where prikey = '".$_GET['killrule']."'";
   $rez = mysql_query($qry);
   $report[] = "Template rule for \"".$_GET['forpage']."\" page deleted!";
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
   if ( thumb == "" ) {
      // No thumb avail
      $('scene_preview').src = 'images/no_thumb-100.gif';
   } else {
      // Scene thumbnail
      $('scene_preview').src = '<? echo $_SESSION['sitepal_BaseURL']; ?>'+thumb;
   }

   // Thumb size by width/height vals
//   $('scene_preview').style.width = $('default_width').value;
//   $('scene_preview').style.height = $('default_height').value;
}
</script>

<?
# header_nav
include("header_nav.inc.php");

# Grr
if ( eregi("MSIE", $_SERVER['HTTP_USER_AGENT']) ) {
   $ihateIE = "margin-top: 0;";
}
echo "<div id=\"content_container\" style=\"".$ihateIE."\">\n";

# Pull all scenes for all accounts
$sp_scenes = sitepal_get_scenes();
$scene_options = "";
$numscenes = count($sp_scenes);
foreach ( $sp_scenes as $account_id=>$scenes ) {
   $numscenes = count($scenes);
   for ( $s = 0; $s < $numscenes; $s++ ) {
      if ($tmp == "#EFEFEF") { $tmp = "WHITE"; } else { $tmp = "#EFEFEF"; }
      if ( $getDefault['scene_id'] == $scenes[$s]['number'] ) { $selected = " selected"; } else { $selected = ""; }
      $scene_options .= "        <option value=\"".$scenes[$s]['thumb']."~~~".$scenes[$s]['number']."~~~".$account_id."\" style=\"background: ".$tmp.";\"".$selected.">".$scenes[$s]['name']."</option>\n";
   }
}

# Templates with SitePal assigned to one of their boxes?
$qry = "select * from PROMO_BOXES where content_type = 'sitepal'";
$rez = mysql_query($qry);
if ( mysql_num_rows($rez) < 1 ) {

   /*---------------------------------------------------------------------------------------------------------*
    _  _         ___  _                          _
   | \| | ___   / __|| |_   __ _  _ _  __ _  __ | |_  ___  _ _  ___
   | .` |/ _ \ | (__ | ' \ / _` || '_|/ _` |/ _||  _|/ -_)| '_|(_-<
   |_|\_|\___/  \___||_||_|\__,_||_|  \__,_|\__| \__|\___||_|  /__/

   # No characters assigned to a template box
   /*---------------------------------------------------------------------------------------------------------*/
   echo "<div class=\"nomar\">\n";
   echo " <h1>No character assigned to template</h1>\n";
   echo " <p class=\"subheading_explination_txt\">This feature comes into play when you you are using a template that contains special \"Template Box\" areas, \n";
   echo " and have designated (via Template Boxes feature) one of those areas to contain a SitePal character.\n";


   # Build info about assinged template
   $tpl_name = file_get_contents($_SESSION['docroot_path']."/template/template.conf");
   $tpl_path = $_SESSION['docroot_path']."/sohoadmin/program/modules/site_templates/pages/".$tpl_name;
   $tplfiles = array("index.html", "home.html", "cart.html");
   $tplvars = array("vmenu", "vmains", "promobox", "newsbox");
   $placement_options = array();

   # Search template html for #box#'s
   $hasboxes = false;
   $hasvmenu = false;
   foreach ( $tplfiles as $key=>$htmlfile ) {
      $bulk_html = file_get_contents($tpl_path."/".$htmlfile);

      #box#
      if ( eregi("#box", $bulk_html) !== false ) {
         $hasboxes = true;
         break;
      }

      # No boxes...any vars to hook it around?
      foreach ( $tplvars as $key=>$poundvar ) {
         if ( eregi($poundvar, $bulk_html) ) {
            $placement_options[] = $poundvar;
         }
      }
   } // End foreach template file

   # Boxes in template?
   if ( $hasboxes ) {
      # YES: Assign now?
      echo "<h1>Assign SitePal character to one of your <a href=\"../promo_boxes/promo_boxes.php\">template boxes</a> to activate this feature...</h1>\n";

   } else {
      # N/A
      echo "<h2>Current site template does not contain any template boxes</h2>\n";
      echo "<p>Please choose a template via Template Manager that incorporates template box's (if it does you'll see the Edit Template Boxes link appear under \n";
      echo " Template Features when you select it), then click the Edit Template Boxes link and choose \"SitePal virtual character\" as the content for one of your template boxes. \n";
      echo " Then come back here and this feature will be enabled. Note that currently not many templates incorporate these template boxes, so you may be better off\n";
      echo " using exactly the template you want and dragging-and-dropping your SitePal characters onto each site page via the Page Editor.</p>\n";

   }

   echo " <p>Note that you don't <i>have</i> to put your SitePal in your <i>template</i> &mdash; \n";
   echo " You can drag-and-drop your SitePal scenes onto your various <a href=\"../open_page.php\">site pages</a> via the Page Editor.</p> \n";





   $tpl_indexhtml = $_SESSION['docroot_path']."/sohoadmin/program/modules/site_templates/pages/".$tpl_name."/index.html";
   $tpl_homehtml = $_SESSION['docroot_path']."/sohoadmin/program/modules/site_templates/pages/".$tpl_name."/index.html";

//   # No boxes in template html
//   echo " <h2>Here are your options...</h2>\n";
//
//
//
//
//   echo " <h3>Option A - Pick a different template</h3>\n";
//   echo " <p>Choose a template via <a href=\"../site_templates.php\">Template Manager</a> that incorporates template box's \n";
//   echo " (if it does you'll see the Edit Template Boxes link appear under Template Features when you select it), \n";
//   echo " then click the Edit Template Boxes link and choose &quot;SitePal virtual character&quot; \n";
//   echo " as the content for one of your template boxes. Then come back here and this feature will be enabled.</p>";
//   echo "</div>\n";
//
//   echo "<h1>This is your site template</h1>\n";
//
//   echo "[".$tpl_name."]<br/>";
//   $tpl_ss_filepath = $_SESSION['docroot_path']."/sohoadmin/program/modules/site_templates/pages/".$tpl_name."/screenshot.jpg";
//   $tpl_ss_fileurl = "http://".$_SESSION['docroot_url']."/sohoadmin/program/modules/site_templates/pages/".$tpl_name."/screenshot.jpg";
//
//   # Screenshot or no image
//   if ( file_exists($tpl_ss_filepath) ) {
//      $screenshot_src = $tpl_ss_fileurl;
//   } else {
//      # "No screen shot" img
//      $screenshot_src = "http://".$_SESSION['docroot_url']."/sohoadmin/program/modules/site_templates/no_screenshot.gif";
//   }
//
//   echo "<img src=\"".$screenshot_src."\"/>";


} else {
   /*---------------------------------------------------------------------------------------------------------*
    _____                   _        _          ___        _
   |_   _|___  _ __   _ __ | | __ _ | |_  ___  | _ \ _  _ | | ___  ___
     | | / -_)| '  \ | '_ \| |/ _` ||  _|/ -_) |   /| || || |/ -_)(_-<
     |_| \___||_|_|_|| .__/|_|\__,_| \__|\___| |_|_\ \_,_||_|\___|/__/
                     |_|
   # YES - Show template character options
   /*---------------------------------------------------------------------------------------------------------*/

   # pophelp-default
   $popup = "";
   $popup .= " <p class=\"subheading_explination_txt\">What should display by default if no special rules are defined? \n";
   $popup .= "  Example: When a visitor views a site page that doesn't have a page-specific rule defined for it here.<p>\n";
   echo help_popup("pophelp-default", "Default behavior", $popup);

   # [+] Add Page-Specific Rule
   echo "  <p id=\"container-add_account_btn\"><input id=\"btn-add_account\" type=\"button\" value=\"[+] Add Page-Specific Rule\" onclick=\"document.location.href='add_edit_rule.php';\" ".$_SESSION['btn_build']."></p>\n";

   # Default template scene
   $qry = "select * from smt_sitepal_rules";
   $rez = mysql_query($qry);

   while ( $getRule = mysql_fetch_assoc($rez) ) {

      echo "<div class=\"account_box\" style=\"width: auto;float: left;\">\n";

      if ( $getRule['page_name'] == "default" ) {
         # Default
         echo " <h3><i>Default behaviour</i>\n";
         echo " <span class=\"help_link\" onclick=\"toggleid('pophelp-default');\">[?]</span>\n";
         echo " </h3>\n";
      } else {
         # Page rule
         echo " <h3>\"".$getRule['page_name']."\" page</h3>\n";
      }

      # Scene thumb defined?
      if ( $getRule['scene_thumb'] != "" ) {
         # YES - show thumbnail
         $thumbfile = $_SESSION['sp_BaseURL'][$getRule['account_id']].$getRule['scene_thumb'];
      } else {
         # NO - show default spacer image
         $thumbfile = "images/no_thumb-100.gif";
      }

      if ( $getRule['page_name'] == "default" && $getRule['account_id'] == "" ) {
         echo " <p>Not yet configured. Click the Edit button to set a default scene.</p>";
      } else {
         echo " <table border=\"0\" cellpadding=\"2\" cellspacing=\"0\" class=\"scene_properties\" style=\"background-color: #".$getRule['bgcolor']."\">\n";
         echo "  <tr>\n";
         echo "   <td class=\"thumb_cell\"><img id=\"scene_preview\" src=\"".$thumbfile."\" width=\"".ceil($getRule['width'] / 2)."\" height=\"".ceil($getRule['height'] / 2)."\"/></td>\n";

         echo "   <td>\n";
//         # TESTING
//         echo "    <p>sp_BaseURL: [".$_SESSION['sp_BaseURL'][$getRule['account_id']]."]</p>";
//         echo testArray($_SESSION['sp_BaseURL']);

         echo "    <p>Show Scene:<br/>\n";
         echo "     <b>".$getRule['scene_name']."</b></p>";

         echo "    <p>Deminsions:<br/>\n";
         echo "     <b>".$getRule['width']."w X ".$getRule['height']."h</b></p>";

         echo "    <p>BG Color:<br/>\n";
         echo "     <span style=\"background-color: #".$getRule['bgcolor']."\">#".$getRule['bgcolor']."</span></p>";
         echo "   </td>\n";
         echo "  </tr>\n";
         echo " </table>\n";
      }



      echo "  <div class=\"savebtn-container\">\n";
      if ( $getRule['page_name'] != "default" ) {
         # [ Delete Rule ]
         echo "   <span class=\"red uline hand\" onclick=\"document.location.href='template_rules.php?killrule=".$getRule['prikey']."&forpage=".$getRule['page_name']."';\" style=\"float: left;\">Delete Rule</span>\n";
      }

      # [ Edit >> ]
      echo "   <input id=\"btn-save_verify\" type=\"button\" ".$_SESSION['btn_edit']." value=\"Edit &gt;&gt;\" onclick=\"document.location.href='add_edit_rule.php?editrule=".$getRule['prikey']."';\">\n";
      echo "  </div>\n";
      echo " </form>\n"; // End <form id=default_scene_form>
      echo "</div>\n";
   }

   # Call preview function onload to show
//   echo "<script type=\"text/javascript\">df_scene_dropdown();</script>\n";

} // End else boxes found with sitepal assigned to them

echo " <div class=\"ie_cleardiv\"></div>\n";
echo "</div>\n";
//--- END: content_container

# Grab module html into container var
$module_html = ob_get_contents();
ob_end_clean();

$module = new smt_module($module_html);
$module->add_breadcrumb_link("SitePal", "program/modules/sitepal/accounts.php");
$module->add_breadcrumb_link("Template Character Behavior", "program/modules/sitepal/template_rules.php");
$module->icon_img = "program/modules/sitepal/images/sitepal_logo.gif";
$module->heading_text = "Template Character Behavior";
//$module->module_table_css = "margin: 0;width: 100%;border: 0px;";
$module->container_css = "padding: 0px;margin: 0px;margin-bottom: 25px;";

# Which tab to show by default?
//$module->bodyid = "accounts";
$module->bodyid = "template"; // testing

//$intro_text = "You have set up your Template Boxes settings so that one of your template boxes contains a SitePal character.";
//$intro_text .= " Here you can define \"rules\" that will determine how that SitePal character behaves on your various site pages.";

$intro_text = "If you are using a template that contains special \"Template Box\" areas, and have designated (via Template Boxes feature) one of those areas\n";
$intro_text .= "to contain a SitePal character, you can use this feature to create \"rules\" for what that character should say and do different site pages.\n";

$module->description_text = $intro_text;

$module->good_to_go();
?>