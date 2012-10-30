<?php
//error_reporting(E_PARSE);
if($_GET['_SESSION'] != '' || $_POST['_SESSION'] != '' || $_COOKIE['_SESSION'] != '') { exit; }

/*---------------------------------------------------------------------------------------------------------*
$module = new smt_module($module_html);
$module->title_tag = "Create New Pages";
$module->add_breadcrumb_link("Create New Pages", "create_pages.php");

# Path from sohoadmin/
$module->icon = "skins/".$_SESSION['skin']."/icons/full_size/create_pages-enabled.gif";

$module->heading_text = "Create New Pages";

$module->description = "You may create up to 10 new pages at a time. Please only use Alpha Numerical characters and Underscores.";

$module->goodtogo();
/*---------------------------------------------------------------------------------------------------------*/

$globalprefObj = new userdata('global');
$_SESSION['utf8value'] = $globalprefObj->get('utf8');

class smt_module {
	

   var $meta_title;
   var $breadcrumb_links = array();
   var $heading_text;
   var $icon_img;
   var $output;
   var $container_css;
   var $module_table_css;

   # Constructor
   function smt_module($module_html) {
      # Populate module html container var with passed data
      $this->module_html = $module_html;

      # Read-in module template html to work with
      # Populates $this->output
      ob_start();
      include($_SESSION['docroot_path']."/sohoadmin/program/includes/smt_module_template.php");
      $this->output = ob_get_contents();
      ob_end_clean();
   }

   # add_breadcrumb_link()
   # Add link to path-to-module breadcrumb
   function add_breadcrumb_link($display_text, $link_href) {
      $this->breadcrumb_links[] = array('display_text' => $display_text, 'link_href' => $link_href);
   }

   # add_cssfile()
   # Hook in a dedicated external css file for this module
   function add_cssfile($filepath) {
      # Hash code in module template to hook this file link in at
      $hooktag = "<!---#add_cssfile#-->";

      # Build link html
      $csslink = "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$filepath."\"/>\n";

      # Add to output html
      $this->output = str_replace($hooktag, $csslink.$hooktag, $this->output);
   }

   # Compile final module html for display
   function good_to_go() {

      # REPORT_MESSAGES?
      if ( count($GLOBALS['report']) > 0 ) {
         $report_bullets = "<ul>\n";
         for ( $r = 0; $r < count($GLOBALS['report']); $r++ ) {
            $report_bullets .= " <li>".$GLOBALS['report'][$r]."</li>\n";
         }
         $report_bullets .= "</ul>\n";
         $this->output = eregi_replace("#REPORT_DISPLAY#", "block", $this->output);
         $this->output = eregi_replace("#REPORT_MESSAGES#", $report_bullets, $this->output);
      } else {
         $this->output = eregi_replace("#REPORT_DISPLAY#", "none", $this->output);
      }


      # META_TITLE - default to heading_text if empty
      if ( $this->meta_title == "" ) { $this->meta_title = $this->heading_text; }

      # Build breadcrumb links
      $breadcrumb_html = "";
      for ( $x = 0; $x < count($this->breadcrumb_links); $x++ ) {
         if ( $x == (count($this->breadcrumb_links) - 1) ) { $self = " class=\"bold uline\""; } else { $self = ""; }
         $breadcrumb_html .= " <a href=\"http://".$_SESSION['docroot_url']."/sohoadmin/".$this->breadcrumb_links[$x]['link_href']."\"".$self.">".$this->breadcrumb_links[$x]['display_text']."</a> &gt;";
      }
      $breadcrumb_html = substr($breadcrumb_html, 0, -4);

      # container_css #
      # containercss (cellpadding for <div> element in module template that contains module html) - 10px pad UOD
      # 0px will screw up recently-updated modules like template manager and template manager, but is needed by some new mods like forms manager 2.0
      if ( $this->container_css == "" ) { $this->container_css = "margin: 0;padding: 10px;"; }

      # module_table_css #
      # Default or special css for outer module table? Use sparingly. All should look the same ideally.
      if ( $this->module_table_css == "" ) { $this->module_table_css = "margin-top: 10px;width: 97%;"; }

      # bodyid
      # DEFAULT: id of body element is "body", set to something diff for css-based tab switching
      if ( $this->bodyid == "" ) { $this->bodid = "body"; }

      # Misc Text/Element replacements
      $this->output = eregi_replace("#bodyid#", $this->bodyid, $this->output);
      $this->output = eregi_replace("#module_table_css#", $this->module_table_css, $this->output);
      $this->output = eregi_replace("#container_css#", $this->container_css, $this->output);
      $this->output = eregi_replace("#META_TITLE#", $this->meta_title, $this->output);
      $this->output = eregi_replace("#META_TITLE#", $this->meta_title, $this->output);
      $this->output = eregi_replace("#BREADCRUMB_LINKS#", $breadcrumb_html, $this->output);
      $this->output = eregi_replace("#ICON_IMG#", $this->icon_img, $this->output);
      $this->output = eregi_replace("#HEADING_TEXT#", $this->heading_text, $this->output);
      $this->output = eregi_replace("#DESCRIPTION_TEXT#", $this->description_text, $this->output);
      $this->output = eregi_replace("#MODULE_HTML#", $this->module_html, $this->output);
      
      if ( $_SESSION['utf8value'] == 'on' ) {
      	$this->output = eregi_replace('iso-8859-1', 'utf-8', $this->output);
      }

      echo $this->output;
   }

}

?>