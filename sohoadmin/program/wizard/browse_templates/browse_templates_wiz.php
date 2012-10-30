<?php
error_reporting(E_PARSE);
if($_GET['_SESSION'] != '' || $_POST['_SESSION'] != '' || $_COOKIE['_SESSION'] != '') { exit; }

#=====================================================================================
# Soholaunch(R) Site Management Tool
#
# Author:        Mike Morrison
# Homepage:      http://www.soholaunch.com
# Release Notes: http://wiki.soholaunch.com
#
# This Script: Simple example module to illustrate how to create a new
# module and keep it's look consistent with the rest of the product
#=====================================================================================

error_reporting(E_PARSE);
session_start();

# Plugin install/misc functions (hook_attach, hook_special, etc)
include_once($_SESSION['docroot_path']."/sohoadmin/program/webmaster/plugin_manager/plugin_functions.php");


# PROCESS: Install template
if ( $_GET['todo'] == "install_template" ) {
   include("install_template.inc.php");
}


# INSTALL TEMPLATE -- orig in-file attempt...since moved to install_template.php
if ( $_GET['install_template'] != "" ) {
   $addons_api = addons_api($_GET['install_template']);
   $plugins_dir_path = $_SESSION['docroot_path']."/sohoadmin/program/modules/site_templates/pages/";
   $zipfile_name = $addons_api['zipfile_name'];
   $download_url = $addons_api['zipfile_url']."&update_domain=".$_SESSION['this_ip'];
   $downloaded_buildfile = $plugins_dir_path.$zipfile_name;

   # Download update file now!
   if ( $errorcode == "" ) {
      $dlUpdate = new file_download($download_url, $downloaded_buildfile);

      if ( !file_exists($downloaded_buildfile) ) {
         if ( ini_get('allow_url_fopen') != "1" ) {
            $errorcode = "cannotupdate-url_fopen";

         } else {
            $errorcode = "cannotupdate-nowrite";
         }
      }
   }
}



?>


<script type="text/javascript" src="../includes/display_elements/window/prototype.js"></script>
<script type="text/javascript" src="../includes/display_elements/window/window.js"></script>
<script type="text/javascript" src="../includes/display_elements/window/effects.js"></script>
<script type="text/javascript" src="../modules/site_templates/browse_templates/browse_templates_wiz.js"></script>

<link rel="stylesheet" type="text/css" href="../modules/site_templates/browse_templates/browse_templates.css"/>
<link href="../includes/display_elements/window/default.css" rel="stylesheet" type="text/css"></link>
<link href="../includes/display_elements/window/alert_lite.css" rel="stylesheet" type="text/css"></link>

<!--- <div id="loading_overlay" style="">
   <div id="loading_box">
      <div id="loading_text">Loading ...</div>
   </div>
</div> -->

<!---Module html goes here-->
<div id="template_details">
 <h2>Template Details</h2>
 No template selected. Select a template thumbnail to see details.
</div>


<!--- START: search_tools -->
<fieldset id="search_tools" style="">
 <!--- browse_category -->
 <div id="browse_category" style="">
  <label for="category">Browse Category:</label>
  <select id="category" onchange="loadTemplates('category');">
   <option value="all" selected>All</option>
   <option value="animals_dog_cat_nature_pets">Animals</option>
   <option value="beauty_health_fitness_hiking_active_garden_food">Beauty and Health</option>
   <option value="business_tech_medical_college_industry_construction">Business</option>
   <option value="outdoors_hiking_active_travel_nature">Travel</option>
   <option value="business_art_dance_creative_music">Art</option>
   <option value="education_college">Education</option>
  </select>
 </div>

 <!--- color_container -->
 <div id="color_container" style="">
  <label for="show_colors">Show colors:</label>
  <select id="show_colors" onchange="loadTemplates('color');">
      <option value="all" selected>All</option>
      <?
      $category_colors = array("Blue", "Green", "Red", "Black", "Grey", "Purple", "Teal", "Orange", "Yellow", "Brown");
      foreach($category_colors as $var){
         echo "<option value=\"".$var."\">".$var."</option>\n";
      }
      ?>
  </select>
 </div>

 <!--- sort_container -->
 <div id="sort_container" style="">
  <label for="sort_by">Sort by:</label>
  <select id="sort_by" onchange="loadTemplates('sort');">
   <option value="name">Template Name</option>
   <option value="updated">Newest</option>
   <option value="downloaded" selected>Most Popular</option>
  </select>
 </div>
 
 <!--- limit_container -->
 <div id="limit_container" style="">
  <label for="limit_num">Display Number:</label>
  <select id="limit_num" onchange="loadTemplates('limit');">
   <option value="25">25</option>
   <option value="50" selected>50</option>
   <option value="75">75</option>
  </select>
 </div>

 <div class="ie_cleardiv"></div>
</fieldset>
<!--- END: filter_controls -->

<!--- template_results -->
<div id="template_results">

</div>

<!--- Next/Prev buttons -->
<div id="next_prev" style="position: relative; text-align: center; padding: 0 20; ">
   <div id="prev_btn" style="width: 100px; float:left; text-align: left; visibility: hidden; ">
      <a href="#" onclick="loadTemplates('prev')">
         <img src="24-arrow-previous.png" border="0" title="Back" />
      </a>
   </div>
   
   <div id="next_btn" style="width: 100px; float:right; text-align: right; visibility: visible; ">
      <a href="#" onclick="loadTemplates('next')">
         <img src="24-arrow-next.png" border="0" align="center" title="Next" />
      </a>
   </div>
   
   <div id="num_results_display" style="position: relative; width: 300px; margin: 0px auto 0px auto; text-align: center;">
      &nbsp;
   </div>
</div>

<script type="text/javascript">
//$('Layer1').style.visibility='hidden'
show_hide_layer('Layer1','','hide','userOpsLayer','','show');

//alert($('loading_overlay').style.width)

loadTemplates('all');
//ajaxDoBrowse('template_results.ajax.php?color=all', 'template_results');

</script>
