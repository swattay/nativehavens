<?php
error_reporting(E_PARSE);
//apd_set_pprof_trace(); $report[] = "ProcessID: [".getmypid()."]";
#===================================================================================================================================
# Soholaunch v4.91 > SitePal > Template Scene layer content include
#===================================================================================================================================
//error_reporting(E_PARSE);
if($_GET['_SESSION'] != '' || $_POST['_SESSION'] != '' || $_COOKIE['_SESSION'] != '') { exit; }
session_start();
include_once($_SESSION['docroot_path']."/sohoadmin/program/includes/product_gui.php");


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
  _____                 __         ___
 / ___/____ ___  ___ _ / /_ ___   / _ \ ___ _ ___ _ ___
/ /__ / __// -_)/ _ `// __// -_) / ___// _ `// _ `// -_)
\___//_/   \__/ \_,_/ \__/ \__/ /_/    \_,_/ \_, / \__/
                                            /___/
# Create new site page (from quick create page popup spawned by Create New... option in dropdown)
/*---------------------------------------------------------------------------------------------------------*/
if ( $_POST['todo'] == "createpage" ) {

   while ( count($report) < 1 ) {
      # Page name blank?
      if ( $_POST['page_name'] == "" ) {
         $report[] = "Could not create page '".$_POST['page_name']."': Page name field blank. Please provide a name for the page you want to create.\n";
         continue;
      }

      # Page already exists?
      $qry = "select page_name from site_pages where page_name = '".$_POST['page_name']."'";
      $rez = mysql_query($qry);
      if ( mysql_num_rows($rez) > 0 ) {
         $report[] = "Could not create page '".$_POST['page_name']."': Page with that name already exists.\n";
         continue;
      }

      # Ok, create page!
      $data = array();
      $data['page_name'] = $_POST['page_name'];
      $data['type'] = "Main";
      $data['link'] = md5($data['page_name']);

      # Get next menu position?
      if ( $_POST['onmenu'] == "yes" ) {
         $qry = "select main_menu from site_pages order by main_menu desc limit 1";
         $rez = mysql_query($qry);
         $data['main_menu'] = (mysql_result($rez, 0) + 1);
      } else {
         $data['main_menu'] = 0;
      }
      $myqry = new mysql_insert("site_pages", $data);
      $myqry->insert();
      $report[] = "'".$_POST['page_name']."' created.";
      if ( $_POST['onmenu'] == "yes" ) {
         $report[] = "'".$_POST['page_name']."' added to site navigation menu.";
      }
      break;
   }
//   echo "done"; exit;
}


/*---------------------------------------------------------------------------------------------------------*
   ____
  / __/___ _ _  __ ___
 _\ \ / _ `/| |/ // -_)
/___/ \_,_/ |___/ \__/

# Add/Update template page rule
/*---------------------------------------------------------------------------------------------------------*/
if ( $_POST['todo'] == "addupdate" ) {
   # Build db qry array
   $data = array();
   $data['page_name'] = $_POST['page_name'];
   $data['account_id'] = $_POST['account_id'];
   $data['scene_id'] = $_POST['scene_id'];
   $data['width'] = $_POST['width'];
   $data['height'] = $_POST['height'];
   $data['bgcolor'] = $_POST['bgcolor'];
   $data['scene_name'] = $_POST['scene_name'];
   $data['scene_thumb'] = $_POST['scene_thumb'];


   # Adding new rule?
   if ( $_POST['editrule'] == "" ) {
      # ADD
      $myqry = new mysql_insert("smt_sitepal_rules", $data);
      $myqry->insert();

   } else {
      # UPDATE
      $qry = "update smt_sitepal_rules set";
      $qry .= " account_id = '".$data['account_id']."'";
      $qry .= ", page_name = '".$data['page_name']."'";
      $qry .= ", scene_id = '".$data['scene_id']."'";
      $qry .= ", width = '".$data['width']."'";
      $qry .= ", height = '".$data['height']."'";
      $qry .= ", bgcolor = '".$data['bgcolor']."'";
      $qry .= ", scene_name = '".$data['scene_name']."'";
      $qry .= ", scene_thumb = '".$data['scene_thumb']."'";
      $qry .= " where prikey = '".$_POST['editrule']."'";
      $rez = mysql_query($qry);
   }

   # Redirect to rule list with success message
   header("location: template_rules.php?newrule=".$data['page_name']); exit;
}


# COLORS - Pre-build options for color dropdowns (i.e. bg color for sitepal scenes)
$filename = "../mods_full/shopping_cart/shared/color_table.dat";
$colordat = file_get_contents($filename);
$colorLines = split("\n", $colordat);
$max = count($colorLines);
$colors = array();
$keynum = 0;
$color_options = "";
for ($x = 0; $x <= $max; $x++ ) {
   $temp = split(",", $colorLines[$x]); // Sample line: "Red,#ff0000"
   if ($temp[0] != "") {
      $colors[$keynum]['name'] = $temp[0];
      $colors[$keynum]['hex'] = $temp[1];

      # Build dropdown options
      $color_options .= "<option value=\"".$colors[$keynum]['hex']."\" alt=\"".$colors[$keynum]['name']."\" style=\"background-color: #".$colors[$keynum]['hex']."\">".$colors[$keynum]['name']."</option>\n";
   }
}
ob_start();
?>

<!---Rules for this specific module-->
<link rel="stylesheet" type="text/css" href="module.css"/>

<script type="text/javascript">
// Allows incrementing/decrimenting numeric value in text field with arrow keys
function sizeField(theEvent, boxid) {

   // Shift + up/down changes value by 25
   if ( theEvent.shiftKey ) {
      incrementby = 25;
   } else {
      incrementby = 1;
   }

   // Only if not empty
   if ( $(boxid).value != "" ) {
      switch(theEvent.keyCode) {
         // Arrow up
         case 38:
         $(boxid).value = parseInt($(boxid).value) + parseInt(incrementby);
         break;

         // Arrow down
         case 40:
         $(boxid).value = parseInt($(boxid).value) - parseInt(incrementby);
         break;
      }
   }
} // End sizeField

function df_scene_dropdown() {
   // Get current value of default scene dropdown
   rawval = $('default_scene_dd').value;
   valArr = rawval.split("~~~");
   thumb = valArr[0];
   sceneid = valArr[1];
   accountid = valArr[2];
   scenename = valArr[3];

//   alert('sceneid = ['+sceneid+']');

   // Update hidden field values
   $('default_scene_id').value = sceneid;
   $('default_account_id').value = accountid;
   $('scene_name').value = scenename;
   $('scene_thumb').value = thumb;

   // Thumb preview
   if ( thumb == "" ) {
      // No thumb avail
      $('scene_preview').src = 'images/no_thumb-100.gif';
   } else {
      // Scene thumbnail
      $('scene_preview').src = '<? echo $_SESSION['sitepal_BaseURL']; ?>'+thumb;
   }

   // Thumb size by width/height vals
   $('scene_preview').style.width = $('default_width').value;
   $('scene_preview').style.height = $('default_height').value;
}


// EXPERIMENTAL: Open create pages dialog if "Create" option choosen from page list drop-down
function pages_dropdown(ddid) {
   if ( $(ddid).value == 'createpage' ) {
      hide_dropdowns();
      showid('popconfig-create_pages');
   }
}
</script>
<style>
h4 {
   margin-top: 10px;
}

fieldset {
   margin-top: 7px;
}
</style>
<?
# header_nav
include("header_nav.inc.php");

# Grr
if ( eregi("MSIE", $_SERVER['HTTP_USER_AGENT']) ) {
   $ihateIE = "margin-top: 0;";
}
echo "<div id=\"content_container\" style=\"".$ihateIE."\">\n";

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

   if ( $_REQUEST['editrule'] != "" ) {
      # EDIT - pull saved settings
      $qry = "select * from smt_sitepal_rules where prikey = '".$_REQUEST['editrule']."'";
      $rez = mysql_query($qry);
      $getRule = mysql_fetch_assoc($rez);

      # Mimic post vaules
      $_POST['page_name'] = $getRule['page_name'];
      $_POST['width'] = $getRule['width'];
      $_POST['height'] = $getRule['height'];
      $_POST['bgcolor'] = $getRule['bgcolor'];
      $_POST['account_id'] = $getRule['account_id'];
      $_POST['scene_id'] = $getRule['scene_id'];

   } else {
      # ADD - pull default
      $qry = "select * from smt_sitepal_rules where page_name = 'default'";
      $rez = mysql_query($qry);
      $getRule = mysql_fetch_assoc($rez);

      # Mimic post vaules
//      $_POST['page_name'] = $getRule['page_name'];
      $_POST['width'] = $getRule['width'];
      $_POST['height'] = $getRule['height'];
      $_POST['bgcolor'] = $getRule['bgcolor'];
      $_POST['account_id'] = $getRule['account_id'];
      $_POST['scene_id'] = $getRule['scene_id'];
   }

   # popconfig-create_pages
   $popup = "";
   $popup .= "<form id=\"createpage_form\" method=\"post\" action=\"".basename($_SERVER['PHP_SELF'])."\">\n";
   $popup .= "<input type=\"hidden\" name=\"todo\" value=\"createpage\"/>\n";
   $popup .= "<input type=\"hidden\" name=\"editrule\" value=\"".$_REQUEST['editrule']."\">\n";
   $popup .= "<p class=\"nomar_btm\">Page Name...</p>\n";
   $popup .= "<p class=\"nomar_top\"><input type=\"text\" name=\"page_name\" value=\"\"/></p>\n";
   $popup .= "<p class=\"nomar_btm\">Place on menu now?</p>\n";
   $popup .= "<span onclick=\"toggle_checkbox('onmenuyes', 'check');\" class=\"hand\"><input type=\"radio\" id=\"onmenuyes\" name=\"onmenu\" value=\"yes\"/> Yes</span>\n";
   $popup .= "<span onclick=\"toggle_checkbox('onmenuyes', 'check');\" class=\"hand\"><input type=\"radio\" name=\"onmenu\" id=\"onmenuno\" value=\"no\" checked/> No<span>\n";
   $popup .= "<p class=\"savebtn-container\"><input type=\"submit\" value=\"".lang("Create Page")." &gt;&gt;\" ".$_SESSION['btn_save']."/></p>\n";
   $popup .= "</form>\n";
   $other['onclose'] = "show_dropdowns();";
   echo help_popup("popconfig-create_pages", "Create new site page", $popup, "width: 250px;", $other);


   # [x] Cancel Changes
   echo "<div style=\"text-align: right;\"><input type=\"button\" ".$_SESSION['btn_delete']." value=\"[x] Cancel\" onclick=\"document.location.href='template_rules.php';\"></div>\n";

   echo "<div class=\"account_box\">\n";

   # <form>
   echo " <form id=\"default_scene_form\" method=\"post\" action=\"".$_SERVER['PHP_SELF']."\">\n";
   echo " <input type=\"hidden\" name=\"todo\" value=\"addupdate\"/>\n";
   echo " <input type=\"hidden\" name=\"editrule\" value=\"".$_REQUEST['editrule']."\">\n";
   echo " <input type=\"hidden\" id=\"default_scene_id\" name=\"scene_id\" value=\"".$_POST['scene_id']."\">\n";
   echo " <input type=\"hidden\" id=\"default_account_id\" name=\"account_id\" value=\"".$_POST['account_id']."\">\n";
   echo " <input type=\"hidden\" id=\"scene_name\" name=\"scene_name\" value=\"".$_POST['scene_name']."\">\n";
   echo " <input type=\"hidden\" id=\"scene_thumb\" name=\"scene_thumb\" value=\"".$_POST['scene_thumb']."\">\n";

   echo "  <table border=\"0\" cellpadding=\"8\" cellspacing=\"0\">\n";
   echo "   <tr>\n";
   echo "    <td width=\"100%\">\n";

   # On this page...
   echo " <h4>On this page...</h4>\n";
   if ( $_POST['page_name'] == "default" ) {
      echo " <input type=\"hidden\" id=\"page_name\" name=\"page_name\" value=\"default\"/>\n";
      echo " <p><i><b>Default Behavior - </b></i><br/>This rule will apply to any page on your site that doesn't have a page-specific rule defined for it.</p>";
   } else {
      echo " <select id=\"page_name\" name=\"page_name\" onchange=\"pages_dropdown(this.id);\">\n";
      include("pages_dropdown.php");
      echo " </select>\n";
   }
   echo "<script type=\"text/javascript\">$('page_name').value = '".$_POST['page_name']."';</script>";

   # Which scene?
   echo "  <h4>Show this scene...</h4>\n";
   echo "  <select id=\"default_scene_dd\" class=\"sitepal_scene_dropdown\" onchange=\"df_scene_dropdown();\">\n";
   $sp_scenes = sitepal_get_scenes();
   $numscenes = count($sp_scenes);

   # Number of SitePal accounts that are set up
   $numaccounts = count($sp_scenes);

   # Single or multi-account behaviors?
   if ( $numaccounts > 1 ) {
      # MULTI
      $account_dividers = true;
      $default_selectedindex = 1;
   } else {
      # SINGLE
      $account_dividers = false;
      $default_selectedindex = 0;
   }

   # Error flag triggered if one or more accounts (but not all) fail to verify (technically: if their scene list is empty)
   $problem_account = false;

   # Split by account
   foreach ( $sp_scenes as $account_id=>$scenes ) {
      $numscenes = count($scenes);

      # Show dividers for multiple accounts?
      if ( $account_dividers ) {
         # Problem with verifying this account?
         if ( $numscenes < 1 ) {
            # ERROR - could not verify account, show error
            echo "       <option value=\"\" style=\"background: #ff0000;color: #fff;\">Unable to pull scenes for account #".$account_id."</option>\n";
            $problem_account = true;

         } else {
            # VERIFIED - show normal account divider
            echo "       <option value=\"\" style=\"background: #000;color: #fff;\">---Account ".$account_id."---</option>\n";
         }
      }

      for ( $s = 0; $s < $numscenes; $s++ ) {
         if ($tmp == "#EFEFEF") { $tmp = "WHITE"; } else { $tmp = "#EFEFEF"; }
         if ( trim($scenes[$s]['name']) != "" && !eregi("silhouette", $scenes[$s]['thumb']) ) {
//            echo "       <option value=\"".$scenes[$s]['thumb']."~~~".$scenes[$s]['id']."~~~".$account_id."~~~".$scene[$s]['name']."\" style=\"background: ".$tmp.";\">".$scenes[$s]['name']."</option>\n";
            echo "        <option value=\"".$scenes[$s]['thumb']."~~~".$scenes[$s]['number']."~~~".$account_id."~~~".$scenes[$s]['name']."\" style=\"background: ".$tmp.";\"".$selected.">".$scenes[$s]['name']."</option>\n";
         }
      }
   } // End foreach
   echo "  </select>\n";

   echo "     <fieldset class=\"scene_properties\">\n";
   echo "      <legend>Basic Scene Properties</legend>\n";
   echo "      <p class=\"nomar\"><label>Width:</label>\n";
   echo "      <input type=\"text\" id=\"default_width\" name=\"width\" value=\"".$_POST['width']."\" style=\"width: 60px;\" onkeyup=\"sizeField(event, 'default_width');df_scene_dropdown();\"></p>\n";
   echo "      <p class=\"nomar\"><label>Height:</label>\n";
   echo "      <input type=\"text\" id=\"default_height\" name=\"height\" value=\"".$_POST['height']."\" style=\"width: 60px;\" onkeyup=\"sizeField(event, 'default_height');df_scene_dropdown();\"></p>\n";

   # Background Color
   echo "      <p class=\"nomar\"><label>Background Color:</label>\n";
   echo "      <select id=\"bgcolor_dd\" onchange=\"$('default_bgcolor').value=this.value;$('default_bgcolor').style.backgroundColor='#'+this.value;\">\n";
   echo "      <option value=\"\">None (transparent)</option>\n";
   echo "      ".$color_options."\n";
   echo "      </select>\n";
   echo "      #<input type=\"text\" id=\"default_bgcolor\" name=\"bgcolor\" value=\"".$_POST['bgcolor']."\" style=\"width: 80px;\" onkeyup=\"df_scene_dropdown();\"></p>\n";
   echo "     </fieldset>\n";
   echo "    </td>\n";

   # thumbnail preview
   echo "    <td align=\"center\">\n";
   echo "     <label>Thumbnail Preview...</label>\n";
   echo "     <div style=\"text-align: center;\">\n";
   echo "      <img id=\"scene_preview\" src=\"".$_SESSION['sitepal_BaseURL'].$_POST['account_id']."/thumbs/show_".$_POST['scene_id'].".jpg\" style=\"border: 1px dotted #ccc;\"/>\n";
   echo "     </div>\n";
   echo "    </td>\n";
   echo "   </tr>\n";
   echo "  </table>\n";
   echo " </form>\n"; // End <form id=default_scene_form>
   echo "</div>\n";

   # [save]
   echo "<div style=\"text-align: right;\"><input id=\"btn-save_verify\" type=\"button\" ".$_SESSION['btn_save']." value=\"Save Changes &gt;&gt;\" onclick=\"$('default_scene_form').submit();\"/></div>\n";

   # Call preview function onload to show
   echo "<script type=\"text/javascript\">\n";
   echo "df_scene_dropdown();\n";
   echo "$('bgcolor_dd').value = '".$_POST['bgcolor']."';\n";
   echo "$('default_bgcolor').style.backgroundColor = '#".$_POST['bgcolor']."';\n";
   echo "</script>\n";

} // End else boxes found with sitepal assigned to them


echo "</div>\n";
//--- END: content_container


# Grab module html into container var
$module_html = ob_get_contents();
ob_end_clean();

$module = new smt_module($module_html);
$module->add_breadcrumb_link("SitePal", "program/modules/sitepal/management_module.php");
$module->add_breadcrumb_link("Template Characters", "program/modules/sitepal/template_rules.php");

if ( $_REQUEST['editrule'] != "" ) {
   # EDIT
   $module->add_breadcrumb_link("Edit Page Rule: ".$_POST['page_name'], "program/modules/sitepal/add_edit_rule.php?editrule=".$_REQUEST['editrule']);
} else {
   # ADD
   $module->add_breadcrumb_link("Add Page Rule", "program/modules/sitepal/add_edit_rule.php");
}

$module->icon_img = "program/modules/sitepal/images/sitepal_logo.gif";
$module->heading_text = "Add/Edit Page Rule";
//$module->module_table_css = "margin: 0;width: 100%;border: 0px;background-color: #fff;";
$module->container_css = "padding: 0px;margin: 0px;";

# Which tab to show by default?
//$module->bodyid = "accounts";
$module->bodyid = "template"; // testing

$intro_text = "These rules determine how the SitePal character in your template will behave based on which page of your site the visitor is viewing.";
$module->description_text = $intro_text;

$module->good_to_go();
?>