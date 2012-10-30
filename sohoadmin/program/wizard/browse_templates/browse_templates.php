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

?>

<style>

</style>

<script type="text/javascript" src="../includes/display_elements/window/prototype.js"></script>
<script type="text/javascript" src="../includes/display_elements/window/window.js"></script>
<script type="text/javascript" src="../includes/display_elements/window/effects.js"></script>
<script type="text/javascript" src="browse_templates/browse_templates.js"></script>

<link rel="stylesheet" type="text/css" href="browse_templates/browse_templates.css"/>
<link href="../includes/display_elements/window/default.css" rel="stylesheet" type="text/css"></link>
<link href="../includes/display_elements/window/alert_lite.css" rel="stylesheet" type="text/css"></link>

<!--- <div id="loading_overlay" style="">
   <div id="loading_box">
      <div id="loading_text"><? echo lang("Loading Templates"); ?>...</div>
   </div>
</div> -->

<!---Module html goes here-->
<div id="template_details">
 <h2><? echo lang("Template Details"); ?></h2>
 <? echo lang("No template selected").". ".lang("Select a template thumbnail to see details");?>.
</div>


<!--- START: search_tools -->
<fieldset id="search_tools" style="">
 <!--- browse_category -->
 <div id="browse_category" style="">
  <label for="category"><? echo lang("Browse Category"); ?>:</label>
  <select id="category" onchange="loadTemplates('category');">
   <option value="all" selected><? echo lang("All"); ?></option>
   <option value="animals_dog_cat_nature_pets"><? echo lang("Animals"); ?></option>
   <option value="beauty_health_fitness_hiking_active_garden_food"><? echo lang("Beauty and Health"); ?></option>
   <option value="business_tech_medical_college_industry_construction"><? echo lang("Business"); ?></option>
   <option value="outdoors_hiking_active_travel_nature"><? echo lang("Travel"); ?></option>
   <option value="business_art_dance_creative_music"><? echo lang("Art"); ?></option>
   <option value="education_college"><? echo lang("Education"); ?></option>
  </select>
 </div>

 <!--- color_container -->
 <div id="color_container" style="">
  <label for="show_colors"><? echo lang("Show colors"); ?>:</label>
  <select id="show_colors" onchange="loadTemplates('color');">
      <option value="all" selected><? echo lang("All"); ?></option>
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
  <label for="sort_by"><? echo lang("Sort by"); ?>:</label>
  <select id="sort_by" onchange="loadTemplates('sort');">
   <option value="name"><? echo lang("Template Name"); ?></option>
   <option value="updated"><? echo lang("Newest"); ?></option>
   <option value="downloaded" selected><? echo lang("Most Popular"); ?></option>
  </select>
 </div>
 
 <!--- limit_container -->
 <div id="limit_container" style="">
  <label for="limit_num"><? echo lang("Display Number"); ?>:</label>
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
         <img src="browse_templates/24-arrow-previous.png" border="0" title="Back" />
      </a>
   </div>
   
   <div id="next_btn" style="width: 100px; float:right; text-align: right; visibility: visible; ">
      <a href="#" onclick="loadTemplates('next')">
         <img src="browse_templates/24-arrow-next.png" border="0" align="center" title="Next" />
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


