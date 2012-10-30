<?php
//error_reporting(E_PARSE);
if($_GET['_SESSION'] != '' || $_POST['_SESSION'] != '' || $_COOKIE['_SESSION'] != '') { exit; }

##########################################################################################################################################
## Soholaunch(R) Site Management Tool
## Version 4.7
##
## Homepage:	 	http://www.soholaunch.com
## Bug Reports: 	http://bugz.soholaunch.com
## Community:     http://forum.soholaunch.com
##########################################################################################################################################

##########################################################################################################################################
## COPYRIGHT NOTICE
## Copyright 1999-2005 Soholaunch.com, Inc.  All Rights Reserved.
##
## This script may be used and modified in accordance to the license
## agreement attached (license.txt) except where expressly noted within
## commented areas of the code body. This copyright notice and the comments
## comments above and below must remain intact at all times.  By using this
## code you agree to indemnify Soholaunch.com, Inc, its coporate agents
## and affiliates from any liability that might arise from its use.
##
## Selling the code for this program without prior written consent is
## expressly forbidden and in violation of Domestic and International
## copyright laws.
##########################################################################################################################################

# Methinks I went a bit to OOP-crazy with these
# -MM


/*=============================================================================================*
 _____               _    _           _____   _
|  __ \             | |  | |         |  __ \ (_)
| |__) |___   _ __  | |  | | _ __    | |  | | _ __   __
|  ___// _ \ | '_ \ | |  | || '_ \   | |  | || |\ \ / /
| |   | (_) || |_) || |__| || |_) |  | |__| || | \ V /
|_|    \___/ | .__/  \____/ | .__/   |_____/ |_|  \_/
             | |            | |
             |_|            |_|
- Creates hidden div layer for progress imgs, help txt, etc. (cleaner than new browser win)
/*=============================================================================================*/
class pop_div {
   var $divid;
   var $boxcap = array();
   var $boxkill = array();
   var $boximg;
   var $classname;
   var $css = array();

   function pop_div($id = "doingstuff", $width = "300", $height = "225") {
      $this->divid = $id;
      $this->boximg = "&nbsp;";
      $this->classname = "text";
      $this->css['display'] = "none";
      $this->css['z-index'] = "50";
      $this->css['position'] = "absolute";
      $this->css['top'] = (480 - $height) / 2;
      $this->css['left'] = (790 - $width) / 2;
      $this->css['width'] = $width;
      $this->css['height'] = $height;
      $this->css['overflow'] = "none";
      $this->css['padding-top'] = "0px";
      $this->css['background'] = "#F8F9FD";
      $this->css['border'] = "1px solid #2E2E2E";

      // Set various default data and strings
      //---------------------------------------------------
      # Big Icon/Logo
      $this->boximg = "&nbsp;";

      # Close Window link
//      $this->boxkill['text'] = lang("Close Window");
//      $this->boxkill['aclass'] = "del";
//      $this->boxkill['onClose'] = "";


   }


   /// Build title cap for inner div table
   ###=======================================================================
   function boxtitle($title, $class = "gray_gel") {
      $text = lang($title);
      $this->boxcap['title'] = $text;
      $this->boxcap['class'] = $class;
   }


   /// Build close/hide link for bottom of inner div table
   ###=======================================================================
   function boxclose($text = "Close Window", $tdclass, $aclass = "del", $onClose = "") {
      $text = lang($text);
      $this->boxkill['text'] = $text;
      $this->boxkill['aclass'] = $aclass;
      $this->boxkill['onClose'] = $onClose;

   }


   /// Compile settings and output div layer!
   ###========================================================================
   function mkpop($contents = "&nbsp;", $tdStyle = "padding: 10px; text-align: left;") {

      // Build style tag attributes
      //---------------------------------
      $styletag = "";
      foreach ( $this->css as $prop=>$val ) {
         $styletag .= $prop.": ".$val."; ";
      }

      // Build div html and inner content table
      //===============================================
      $disdiv = "\n\n<!----~~~~~~~~~~~~~~~~~~~~~~~ Begin Div Layer: ".$this->divid." ~~~~~~~~~~~~~~~~~~~~~~~---->\n";
      $disdiv .= "<div id=\"".$this->divid."\" align=\"center\" style=\"".$styletag."\">\n";
      $disdiv .= " <table width=\"100%\" align=\"center\" border=\"0\" cellpadding=\"3\" cellspacing=\"0\" class=\"".$this->classname."\">\n";

      if ( $this->boxcap['title'] != "" ) {
         # Title header
         $disdiv .= "  <tr>\n";
         $disdiv .= "   <td align=\"left\" class=\"".$this->boxcap['class']."\">\n";
         $disdiv .= "    ".$this->boxcap['title']."\n";
         $disdiv .= "   </td>\n";

         # [ Close Window ]
         if ( $this->boxkill['text'] != "" ) {
            $disdiv .= "   <td align=\"right\" class=\"".$this->boxcap['class']."\">\n";
            $disdiv .= "    <span class=\"text\" style=\"font-weight: normal;\">\n";
            $disdiv .= "    [ <a href=\"#\" onclick=\"".$this->boxkill['onClose']."document.getElementById('".$this->divid."').style.display='none'\" class=\"".$this->boxkill['aclass']."\">".$this->boxkill['text']."</a> ]\n";
            $disdiv .= "    </span>\n";
            $disdiv .= "   </td>\n";
         } else {
            $disdiv .= "   <td align=\"left\" class=\"".$this->boxcap['class']."\">&nbsp;</td>\n";
         }
         $disdiv .= "  </tr>\n";

      } // End if boxtitle != ""

      # Passed HTML contents
      $disdiv .= "  <tr>\n";
      $disdiv .= "   <td colspan=\"2\" width=\"100%\" style=\"".$tdStyle."\">".$contents."</td>\n";
      $disdiv .= "  </tr>\n";

//      # [ Close Window ] link at bottom(ish) center
//      if ( $this->boxkill['text'] != "" ) {
//         $disdiv .= "  <tr>\n";
//         $disdiv .= "   <td colspan=\"2\" align=\"center\">\n";
//         $disdiv .= "    <span class=\"text\" style=\"font-weight: normal;\">\n";
//         $disdiv .= "    [ <a href=\"#\" onclick=\"".$this->boxkill['onClose']."document.getElementById('".$this->divid."').style.display='none'\" class=\"".$this->boxkill['aclass']."\">".$this->boxkill['text']."</a> ]\n";
//         $disdiv .= "    </span>\n";
//         $disdiv .= "   </td>\n";
//         $disdiv .= "  </tr>\n";
//      }


      $disdiv .= " </table>\n";
      $disdiv .= "</div>\n";
      $disdiv .= "<!----~~~~~~~~~~~~~~~~~~~~~~~ End Div Layer: ".$this->divid." ~~~~~~~~~~~~~~~~~~~~~~~---->\n\n\n";

      return $disdiv;
   }
}


/*=============================================================================================*
 _____               _    _          _______      _      _
|  __ \             | |  | |        |__   __|    | |    | |
| |__) |___   _ __  | |  | | _ __      | |  __ _ | |__  | |  ___
|  ___// _ \ | '_ \ | |  | || '_ \     | | / _` || '_ \ | | / _ \
| |   | (_) || |_) || |__| || |_) |    | || (_| || |_) || ||  __/
|_|    \___/ | .__/  \____/ | .__/     |_| \__,_||_.__/ |_| \___|
             | |            | |
             |_|            |_|
- Creates hidden table for progress imgs, help txt, etc. (cleaner than new browser win)
/*=============================================================================================*/
class pop_table extends pop_div {

   function pop_table($id = "doingstuff", $width = "300") {
      pop_div::pop_div($id, $width, "");
      $this->css['top'] = "45px";

   }

   /// Compile settings and output table
   ###========================================================================
   function mkpop($contents = "&nbsp;", $tdStyle = "padding: 10px;") {

      // Build style tag attributes
      //---------------------------------
      $styletag = "";
      foreach ( $this->css as $prop=>$val ) {
            $styletag .= $prop.": ".$val."; ";
      }

      // Build div html and inner content table
      //===============================================
      $disdiv = "\n\n<!----~~~~~~~~~~~~~~~~~~~~~~~ Begin Div Layer: ".$this->divid." ~~~~~~~~~~~~~~~~~~~~~~~---->\n";
      $disdiv .= "<div style=\"height: %100;\">\n";
      $disdiv .= " <table width=\"100%\" id=\"".$this->divid."\" align=\"center\" style=\"".$styletag."\" border=\"0\" cellpadding=\"3\" cellspacing=\"0\" class=\"".$this->classname."\">\n";

      # Title header
      $disdiv .= "  <tr>\n";
      $disdiv .= "   <td align=\"left\" class=\"".$this->boxcap['class']."\">\n";
      $disdiv .= "    ".$this->boxcap['title']."\n";
      $disdiv .= "   </td>\n";
      $disdiv .= "   <td align=\"right\" class=\"".$this->boxcap['class']."\">\n";
      $disdiv .= "    <span class=\"text\" style=\"font-weight: normal;\">\n";
      $disdiv .= "    [ <a href=\"#\" onclick=\"".$this->boxkill['onClose']."document.getElementById('".$this->divid."').style.display='none'\" class=\"".$this->boxkill['aclass']."\">".$this->boxkill['text']."</a> ]\n";
      $disdiv .= "    </span>\n";
      $disdiv .= "   </td>\n";
      $disdiv .= "  </tr>\n";

      # Passed HTML contents
      $disdiv .= "  <tr>\n";
      $disdiv .= "   <td colspan=\"2\" width=\"100%\" align=\"left\" style=\"".$tdStyle."\">".$contents."</td>\n";
      $disdiv .= "  </tr>\n";

      $disdiv .= " </table>\n";
      $disdiv .= "</div>\n";
      $disdiv .= "<!----~~~~~~~~~~~~~~~~~~~~~~~ End Div Layer: ".$this->divid." ~~~~~~~~~~~~~~~~~~~~~~~---->\n\n\n";

      return $disdiv;
   }

}





/*=============================================================================================*/

?>