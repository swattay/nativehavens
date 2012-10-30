<?php
error_reporting(E_PARSE);
if($_GET['_SESSION'] != '' || $_POST['_SESSION'] != '' || $_COOKIE['_SESSION'] != '') { exit; }


###############################################################################
## Soholaunch(R) Site Management Tool
## Version 4.5
##
## Author: 			Mike Johnston [mike.johnston@soholaunch.com]
## Homepage:	 	http://www.soholaunch.com
## Bug Reports: 	http://bugzilla.soholaunch.com
## Release Notes:	sohoadmin/build.dat.php
###############################################################################

##############################################################################
## COPYRIGHT NOTICE
## Copyright 1999-2003 Soholaunch.com, Inc. and Mike Johnston
## Copyright 2003-2007 Soholaunch.com, Inc.
## All Rights Reserved.
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
###############################################################################

error_reporting(0);
session_cache_limiter('none');
session_start();
track_vars;

$THIS_DISPLAY = "";	// Make Display Variable Blank in Case of Session Memory

#################################################################################
### WE WILL NEED TO KNOW THE DATABASE NAME; UN; PW; ETC TO OPERATE THE
### REAL-TIME EXECUTION.  THIS IS CONFIGURED IN THE isp.conf FILE
#################################################################################

include("pgm-cart_config.php");
$dot_com = $this_ip;	// Assign dot_com variable to configured ip address

include_once("../sohoadmin/program/includes/shared_functions.php");

if(isset($_GET['inv_error'])){
	echo "\n\n<SCRIPT LANGUAGE=Javascript>\n";
	echo "	alert('".lang("Were sorry, there are only")." ".$inv_error." ".lang("left of this product, please enter a new amount").".');\n";
	echo "</script>\n\n";
}

#################################################################################
### Check Security
#################################################################################
if($_REQUEST['id'] != ''){
	$secc = mysql_query("SELECT OPTION_SECURITYCODE FROM cart_products WHERE PRIKEY = '".$_REQUEST['id']."'");
	$sec_check = mysql_fetch_array($secc);
	if($sec_check['OPTION_SECURITYCODE'] != 'Public'){
		$groups_ar = explode(';', $_SESSION['GROUPS']);
		if(in_array($sec_check['OPTION_SECURITYCODE'], $groups_ar)){
			//echo 'found '.$sec_check['OPTION_SECURITYCODE'];
			// Let them stay, authorized to see this product
		} else {
			header("location: start.php?browse=1"); exit;
		}
	}
}

#################################################################################
### READ DATABASED OPTIONS INTO MEMORY NOW
#################################################################################
$result = mysql_query("SELECT * FROM cart_options");
$OPTIONS = mysql_fetch_array($result);

# Redirect now if set to go directly to checkout
if ($OPTIONS['GOTO_CHECKOUT'] == "skip") {
   header("location: pgm-add_cart.php?id=".$_REQUEST['id']."&qty=1&goto_checkout=yes"); exit;
}

# Misc cart preferences (set defaults too)
$cartprefs = new userdata("cart");
$cartpref = &$cartprefs; // Use $cartpref
if ( $cartprefs->get("fullimg_maxwidth") == "" ) { $cartprefs->set("fullimg_maxwidth", "650"); } // has to be something...650px was limit mentioned in note text b4 v4.9 r54

//040406 - Pull Currency Info
$dSign = $OPTIONS['PAYMENT_CURRENCY_SIGN'];
$dType = $OPTIONS['PAYMENT_CURRENCY_TYPE'];

# Restore css styles array
$getCss = unserialize($OPTIONS['CSS']);

// GET ALL INFO ABOUT "THIS" PRODUCT

$result = mysql_query("SELECT * FROM cart_products WHERE PRIKEY = '$id'");
$PROD = mysql_fetch_array($result);
#################################################################################

$PROD['PROD_NAME'] = eregi_replace("&quot;", "\"", $PROD['PROD_NAME']);
$PROD['PROD_DESC'] = eregi_replace("&quot;", "\"", $PROD['PROD_DESC']);

# Restore price variation arrays
$PROD['sub_cats'] = unserialize($PROD['sub_cats']);
$PROD['variant_names'] = unserialize($PROD['variant_names']);
$PROD['variant_prices'] = unserialize($PROD['variant_prices']);

eval(hook("pgm-more_information.php:initial_data"));

eval(hook("pgm-more_information.php:accordionincludes"));

// Log this sku as a view in statistics
// -----------------------------------------------------------------
if (file_exists("../pgm-site_stats.inc.php")) {    // Check; this mod N/A in Lite Version
   $statpage = "Product: ".$PROD[PROD_NAME];
   include ("../pgm-site_stats.inc.php");
}


/*---------------------------------------------------------------------------------------------------------*
 ___  _
/ __|| |__ _  _   ___ _  _  _ __   _ __   __ _  _ _  _  _
\__ \| / /| || | (_-<| || || '  \ | '  \ / _` || '_|| || |
|___/|_\_\ \_,_| /__/ \_,_||_|_|_||_|_|_|\__,_||_|   \_, |
                                                     |__/

# PARAGRAPH ONE : NORMAL DISPLAY (CONDENSED)
/*---------------------------------------------------------------------------------------------------------*/
$THIS_DISPLAY .= "<a name=\"MOREINFO\"></a>\n";
$THIS_DISPLAY .= "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" id=\"moreinfo-summary\">\n";
$THIS_DISPLAY .= " <tr>\n";
$THIS_DISPLAY .= "  <th>\n";
$THIS_DISPLAY .= "   ".$PROD['PROD_NAME']."\n";
$THIS_DISPLAY .= "  </th>\n";
$THIS_DISPLAY .= " </tr>\n";

$THIS_DISPLAY .= " <tr>\n";
$THIS_DISPLAY .= "  <td class=text>\n";

$THIS_DISPLAY .= "   <table border=\"0\" cellpadding=\"2\" cellspacing=\"0\" align=\"center\">\n";
$THIS_DISPLAY .= "    <tr>\n";
$THIS_DISPLAY .= "     <td class=text align=\"center\" valign=\"top\">\n";

// -------------------------------------------------------------------------------
// If a thumbnail is present, use it, otherwise, scale the fullimage to a width
// of 150px width max.  This keeps our cool display consistant.
// -------------------------------------------------------------------------------
$THIS_IMAGE = "";

if (strlen($PROD[PROD_THUMBNAIL]) > 2) {
   $THIS_IMAGE = "../images/".$PROD['PROD_THUMBNAIL'];
   $TEST_IMAGE = "$doc_root/images/".$PROD['PROD_THUMBNAIL'];
} else {
   $THIS_IMAGE = "../images/".$PROD['PROD_FULLIMAGENAME'];
   $TEST_IMAGE = $doc_root."/images/".$PROD['PROD_FULLIMAGENAME'];
}

$WH = "";   // Set width/height var to nothing

if ( file_exists($TEST_IMAGE) ) {
   $tempArray = getImageSize($TEST_IMAGE);
   $origW = $tempArray[0];
   $origH = $tempArray[1];
   $oW = $origW;        // Set new W/H to real image size
   $oH = $origH;

   $WH = "width=\"".$origW."\" height=\"".$origH."\"";

   if ( $origW > 114 ) {    // If width > 114px; scale to 114px proportionally
      $calc = 114 / $origW;
      $hcalc = $origH * $calc;
      $nheight = round($hcalc);
      $WH = " width=\"115\" height=\"".$nheight."\"";
   }

   //$THIS_IMAGE .= $WH;     // Add W/H calculation to image tag
}

// -------------------------------------------------------------------------------
// If the imagename listed in the database is not there, don't show an image
// -------------------------------------------------------------------------------

if (!file_exists($TEST_IMAGE) || $THIS_IMAGE == "../images/") {
   $THIS_IMAGE = "../spacer.gif";
}


$THIS_DISPLAY .= "      <img src=\"".$THIS_IMAGE."\" ".$WH." vspace=\"2\" hspace=\"5\" alt=\"".$PROD[PROD_NAME]."\" border=\"0\">\n";

$THIS_DISPLAY .= "     </td>\n";
$THIS_DISPLAY .= "     <td align=\"left\" valign=\"top\" class=\"text\">\n";

$PROD['PROD_DESC'] = chop($PROD['PROD_DESC']);
$PROD['PROD_DESC'] = rtrim($PROD['PROD_DESC']);  // Trim extra CR and spaces from description text

$tmpDisplayDesc = eregi_replace("\n", "<br/>", $PROD['PROD_DESC']);
$tmpDisplayDesc = str_replace("&quot;", "\"", $tmpDisplayDesc);

# Allow for full html description option
if ( trim($PROD['full_desc']) != "" ) {
   $description_text = nl2br(base64_decode($PROD['full_desc']));
} else {
   $description_text = $tmpDisplayDesc;
}

$THIS_DISPLAY .= $description_text."\n";

if ( eregi("Y", $OPTIONS['DISPLAY_EMAILFRIEND']) ) {
   $THIS_DISPLAY .= "      <br/><br/><div align=\"center\" class=\"text\">[ <a href=\"pgm-email_friend.php?id=$PROD[PRIKEY]\">".lang("Email To A Friend")."</a> ]</div>\n";
}

$THIS_DISPLAY .= "      <br/><div align=\"center\" class=\"smtext\">".lang("Add this product to your cart below")."<br/>".lang("under 'ordering options'.")."</div>\n";

$THIS_DISPLAY .= "     </td>\n";
$THIS_DISPLAY .= "    </tr>\n";
$THIS_DISPLAY .= "   </table>\n";

$THIS_DISPLAY .= "  </td>\n";

$THIS_DISPLAY .= " </tr>\n";
$THIS_DISPLAY .= "</table>\n";




/*---------------------------------------------------------------------------------------------------------*
   _       _     _   _                         _
  /_\   __| | __| | | |_  ___   __  __ _  _ _ | |_
 / _ \ / _` |/ _` | |  _|/ _ \ / _|/ _` || '_||  _|
/_/ \_\\__,_|\__,_|  \__|\___/ \__|\__,_||_|   \__|

# TABLE TWO : ADD TO CART (ONLY IF NOT CATALOG ONLY)
/*---------------------------------------------------------------------------------------------------------*/
if ( !eregi("Y", $OPTIONS['PAYMENT_CATALOG_ONLY']) ) {       // This covers the entire PARAGRAPH TWO OUTPUT
	$varprice_count = 0;
	foreach($PROD['variant_prices'] as $camzountz=>$camz){
		if($camz != '' && $camz != " " && $PROD['variant_names'][$camzountz] != '' && $PROD['variant_names'][$camzountz] != ' '){
			++$varprice_count;
		}
	}

	$cartprefs = new userdata("cart");
	if ( $cartprefs->get("more_information_display") == "extended" && $varprice_count > 0) {
		include('pgm-more_information_extended.php');
	} else {
eval(hook("pgm-more_information.php:above_moreinfo-pricing_table"));
eval(hook("pgm-more_information.php:accordioncontainer"));
	   $THIS_DISPLAY .= "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\" id=\"moreinfo-pricing\">\n";
	   $THIS_DISPLAY .= " <tr>\n";

	   $THIS_DISPLAY .= "  <th class=\"smtext\" align=\"center\">".lang("Product")."</td>\n";
	   $THIS_DISPLAY .= "  <th class=\"smtext\" align=\"center\">".lang("Price")."</td>\n";
	   $THIS_DISPLAY .= "  <th class=\"smtext\" align=\"center\">".lang("Qty")."</td>\n";
	   $THIS_DISPLAY .= "  <th class=\"smtext\" align=\"center\">".lang("Add To Cart")."</td>\n";

	   $THIS_DISPLAY .= " </tr>\n";


	   // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	   // Start Calculation of display based on single unit price or multiple variants
	   // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
eval(hook("pgm-more_information.php:addninfocontent"));
	   if ( $PROD['variant_prices'][1] != "" && $PROD['variant_names'][1] != "" ) {

	      $PRICE_OPTIONS .= "<SELECT NAME=price CLASS=smtext>\n";

	      for ( $z = 1; $z <= $PROD['num_variants']; $z++ ) {    // Loop through 6 variants and see what we have

	         if ( $PROD['variant_prices'][$z] != "" ) {

	            $PRICE_OPTIONS .= "     <OPTION VALUE=\"".$PROD['variant_names'][$z].";".$PROD['variant_prices'][$z]."\">".$PROD['variant_names'][$z]." - ".$dSign.$PROD['variant_prices'][$z]."</OPTION>\n";

	         }

	      }

	      $PRICE_OPTIONS .= "</SELECT>\n";

	   } else {

	      $PRICE_OPTIONS .= "".$dSign."".$PROD['PROD_UNITPRICE']."\n<input type=hidden name=\"price\" value=\"".$PROD['PROD_UNITPRICE']."\">\n";   // If not variant; this is a single unitprice

	   } // Finished Calculating Price Variants

	   // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	   // Now Let's calculate the different sub-categories or single product category
	   // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	   if ($PROD['sub_cats'][1] != "" ) {  // This must be a multiple category selection

	      # Restore price variation

	      for ( $z = 1; $z <= $PROD['num_variants']; $z++ ) { // Loop through sub-cats and create display HTML this way

	         $tmp_cat = "SUB_CAT".$z;

	         if ( $PROD['sub_cats'][$z] != "" ) {
eval(hook("pgm-more_information.php:multicatbox"));
	            //if ($multi_bg == "white") { $multi_bg = "#EFEFEF"; } else { $multi_bg = "white"; }

	            $THIS_DISPLAY .= "\n\n<form method=\"post\" action=\"pgm-add_cart.php\">\n";
	            $THIS_DISPLAY .= "<input type=\"hidden\" name=\"id\" value=\"".$PROD['PRIKEY']."\">\n";
	            $THIS_DISPLAY .= "<input type=\"hidden\" name=\"goto_checkout\" value=\"".$OPTIONS['GOTO_CHECKOUT']."\">\n";
	            $THIS_DISPLAY .= "<input type=\"hidden\" name=\"subcat\" value=\"".$PROD['sub_cats'][$z]."\">\n";
	            $THIS_DISPLAY .= " <tr>\n";
	            $THIS_DISPLAY .= "  <td class=\"smtext\" align=\"left\">".$PROD['PROD_NAME']." - ".$PROD['sub_cats'][$z]."</td>\n";
	            $THIS_DISPLAY .= "  <td class=\"smtext\" align=\"center\">".$PRICE_OPTIONS."</td>\n";
	            $THIS_DISPLAY .= "  <td class=\"smtext\" align=\"center\"><input type=\"text\" SIZE=\"2\" class=smtext name=\"qty\" value=\"1\"></td>\n";
	            if ( $PROD['OPTION_INVENTORY_NUM'] <= 0 ) {
	               $THIS_DISPLAY .= "  <td class=\"smtext\" align=\"center\">Out of Stock</td>\n";
	            } else {
	               $THIS_DISPLAY .= "  <td class=\"smtext\" align=\"center\"><input TYPE=\"SUBMIT\" value=\"".lang("Add")."\" class=\"FormLt1\"></td>\n";
	            }
	            $THIS_DISPLAY .= " </tr>\n";

eval(hook("pgm-more_information.php:dispmulticat"));

	            $THIS_DISPLAY .= "</form>\n\n";

	         }

	      } // End For Loop

	   } else { // This is a single cat product

	      $THIS_DISPLAY .= "\n\n<form method=\"post\" action=\"pgm-add_cart.php\">\n";
	      $THIS_DISPLAY .= "<input type=\"hidden\" name=\"id\" value=\"".$PROD['PRIKEY']."\">\n";
	      $THIS_DISPLAY .= "<input type=\"hidden\" name=\"goto_checkout\" value=\"".$OPTIONS['GOTO_CHECKOUT']."\">\n";
	      $THIS_DISPLAY .= "<input type=\"hidden\" name=\"subcat\" value=\"\">\n";
	      $THIS_DISPLAY .= "<tr>\n";
	      $THIS_DISPLAY .= "<td class=smtext align=\"left\">$PROD[PROD_NAME]</td>\n";
	      $THIS_DISPLAY .= "<td class=smtext align=\"center\">$PRICE_OPTIONS</td>\n";
	      $THIS_DISPLAY .= "<td class=smtext align=\"center\"><input type=\"text\" size=\"2\" class=smtext name=\"qty\" value=\"1\"></td>\n";
	         //testing for inventory
	         //echo "inv--->".$PROD[OPTION_INVENTORY_NUM]."<br/>";
	         if ($PROD['OPTION_INVENTORY_NUM'] <= 0)
	         {
	         $THIS_DISPLAY .= "<td class=smtext align=\"center\">".lang("Out of Stock")."</td>\n";
	         } else {
	         $THIS_DISPLAY .= "<td class=smtext align=\"center\"><input type=\"submit\" value=\"".lang("Add")."\" class=\"FormLt1\"></td>\n";
	         }
	      $THIS_DISPLAY .= "</tr>\n";

eval(hook("pgm-more_information.php:dispsinglecat"));

	      $THIS_DISPLAY .= "</form>\n\n";

	   } // End Sub Cat Check

	   // If this sku has a custom form attached, notify client that additional info
	   // will be gathered when user "adds" product to thier shopping cart.

	   if (eregi(".FORM", $PROD[OPTION_FORMDATA])) {

	      $THIS_DISPLAY .= "<tr>\n";
	      $THIS_DISPLAY .= "<td colspan=\"4\" class=\"smtext\" align=\"center\"><font color=\"maroon\">\n";
	      $THIS_DISPLAY .= "<i>".lang("Details specific to this item will be asked when you add this product to your cart.")."\n";
	      $THIS_DISPLAY .= "</td>\n";
	      $THIS_DISPLAY .= "</tr>\n";

	   } // End if Custom Form Function

	   $THIS_DISPLAY .= "</table>\n";

eval(hook("pgm-more_information.php:closecat"));

	}
} // End if NOT catalog only


   /*---------------------------------------------------------------------------------------------------------*
    __  __                  ___         __
   |  \/  | ___  _ _  ___  |_ _| _ _   / _| ___
   | |\/| |/ _ \| '_|/ -_)  | | | ' \ |  _|/ _ \
   |_|  |_|\___/|_|  \___| |___||_||_||_|  \___/

	# PARAGRAPH THREE : MORE INFO DISPLAY
   /*---------------------------------------------------------------------------------------------------------*/
	if ( strlen($PROD['other_images']) > 2 || strlen($PROD['PROD_FULLIMAGENAME']) > 2 || $PROD[OPTION_DETAILPAGE] != "") {	// If more info exists

	   # DEFAULT: 650px maxwidth, 95px thumbs
	   if ( $cartprefs->get("fullimg_maxwidth") == "" ) { $cartprefs->set("fullimg_maxwidth", "650"); }
	   if ( $cartprefs->get("thumb_width") == "" ) { $cartprefs->set("thumb_width", "95"); }
	   $fullsize_maxwidth = $cartprefs->get("fullimg_maxwidth");

		$THIS_DISPLAY .= "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\" id=\"moreinfo-details\">\n";
		$THIS_DISPLAY .= " <tr>\n";
		$THIS_DISPLAY .= "  <td bgcolor=\"#".$OPTIONS['DISPLAY_HEADERBG']."\">\n";
		$THIS_DISPLAY .= "   <font color=\"#".$OPTIONS['DISPLAY_HEADERTXT']."\"><b><font face=\"verdana, Arial, Helvetica, sans-serif\">".lang("More Information")."...</font></b></font>\n";
		$THIS_DISPLAY .= "  </td>\n";
		$THIS_DISPLAY .= " </tr>\n";

		$THIS_DISPLAY .= " <tr>\n";
		$THIS_DISPLAY .= "  <td class=text>\n";

      # Full size image?
      if ( strlen($PROD['PROD_FULLIMAGENAME']) > 2 ) {
         # Show full-size image
         $fullsize_url = "../images/".$PROD['PROD_FULLIMAGENAME'];

         # Force width resize by default?
         function maxwidth($fullsize_url) {
            global $cartprefs;
            $fullsize_info = getImageSize($fullsize_url);
            if ( $fullsize_info[0] > $cartprefs->get("fullimg_maxwidth") ) {
               return $cartprefs->get("fullimg_maxwidth");
            } else {
               return $fullsize_info[0];
            }
         }

         # Force width resize by default?
         $fullsize_info = getImageSize($fullsize_url);
         if ( $fullsize_info[0] > $cartprefs->get("fullimg_maxwidth") ) {
            $dfwidth = " width=\"".$cartprefs->get("fullimg_maxwidth")."\"";
         } else {
            $dfwidth = ""; // No width value...show full size image
         }

         $THIS_DISPLAY .= "  <h3>".lang("Pictures")."</h3>\n";
         $THIS_DISPLAY .= "  <div id=\"additional_images-container\">\n";

         # Additional img gallery?
         if ( strlen($PROD['other_images']) > 2 && $PROD['other_images'] != 'NULL') {
            # YES - Show mouseover image thumbnails
            $THIS_DISPLAY .= "<script type=\"text/javascript\" src=\"imgtrail.js\"></script>\n";

            $other_images = explode(";", $PROD['other_images']);

            $THIS_DISPLAY .= "     <h4>".lang("Place your mouse over a picture to see the full-size image...")."</h4>\n";

            # Start thumb list with default fullsize
            $img_url = "../images/".$PROD['PROD_FULLIMAGENAME'];
            $img_attrib = " onmouseover=\"showtrail('".$img_url."','My Image','My Image description','5.0000','16','1', 253, 1, ".maxwidth($img_url).");\" onmouseout=\"hidetrail();\"";
            $THIS_DISPLAY .= "     <div class=\"additional_images-thumb\">";
            $THIS_DISPLAY .= "      <img src=\"".$img_url."\"".$img_attrib.">";
            $THIS_DISPLAY .= "     </div>";

            # Display addition image thumbnails
            for ( $i=0; $i < count($other_images); $i++ ) {
               if ( $other_images[$i] != "" && $other_images[$i] != 'NULL' ) {
                  $img_url = "../images/".$other_images[$i];
                  $img_attrib = " onmouseover=\"showtrail('".$img_url."','My Image','My Image description','5.0000','16','1', 253, 1, ".maxwidth($img_url).");\" onmouseout=\"hidetrail();\"";
                  $THIS_DISPLAY .= "     <div class=\"additional_images-thumb\" style=\"margin: 5px;\">";
                  $THIS_DISPLAY .= "      <img src=\"".$img_url."\"".$img_attrib.">";
                  $THIS_DISPLAY .= "     </div>";
               }
            }
         } else {
            # NO - Show fullsize only
            $img_url = "../images/".$PROD['PROD_FULLIMAGENAME'];
            $img_attrib = " width=\"".maxwidth($img_url)."\"";
            $THIS_DISPLAY .= "   <img src=\"".$img_url."\"".$img_attrib." class=\"fullsize_image\">";
         }

         $THIS_DISPLAY .= "   <div style=\"clear: both;\"></div>\n"; // Float border hack
         $THIS_DISPLAY .= "  </div>\n";



      } // End If Full Image Exists



      // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
      // Now, Pull in and format "Detail Page" if exists
      // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
      if ($PROD[OPTION_DETAILPAGE] != "") {

         $filename = "$cgi_bin/$PROD[OPTION_DETAILPAGE].con";  // .con ext is important for this
         $filename = eregi_replace(" ", "_", $filename);       // Added 2003-03-03: Bug Fix for "two word" page names

         if (file_exists("$filename")) {

            $file = fopen("$filename", "r");
               $DETAILS = fread($file,filesize($filename));
            fclose($file);

            // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
            // Reformat content area of HTML to compensate for new 462 width
            // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

            $DETAILS = eregi_replace("<P>", "<div>", $DETAILS);
            $DETAILS = eregi_replace("<P ", "<div ", $DETAILS);
            $DETAILS = eregi_replace("width=612", "width=447", $DETAILS); // Overall Content Area

            $DETAILS = eregi_replace("width=199 height=21>", "width=144 height=15>", $DETAILS); // Clears Built-In Obj

            $DETAILS = eregi_replace("ID=thisflashobj WIDTH=\"597\" height=\"302\"", "ID=thisflashobj WIDTH=\"432\" height=\"220\"", $DETAILS); // Setup Flash Obj
            $DETAILS = eregi_replace("ID=thisflashobj WIDTH=\"398\" height=\"202\"", "ID=thisflashobj WIDTH=\"288\" height=\"144\"", $DETAILS); // Setup Flash Obj
            $DETAILS = eregi_replace("ID=thisflashobj WIDTH=\"199\" height=\"101\"", "ID=thisflashobj WIDTH=\"144\" height=\"72\"", $DETAILS); // Setup Flash Obj

            $DETAILS = eregi_replace("<img src=\"spacer.gif\" height=3 width=199 border=0>", "<img src=\"spacer.gif\" height=3 width=120 border=0>", $DETAILS); // Clears Built-In Obj

            $DETAILS = eregi_replace("width=597 height=", "width=432 name=", $DETAILS);
            $DETAILS = eregi_replace("width=398 height=", "width=288 name=", $DETAILS);
            $DETAILS = eregi_replace("width=199 height=", "width=144 name=", $DETAILS);

            $DETAILS = eregi_replace("width=597", "width=432", $DETAILS);
            $DETAILS = eregi_replace("width=398", "width=288", $DETAILS);
            $DETAILS = eregi_replace("width=199", "width=144", $DETAILS);
            $DETAILS = eregi_replace("width=298", "width=216", $DETAILS);

            $DETAILS = eregi_replace("width=185", "width=100", $DETAILS); // For Liquid Expansion; Only need to worry about spacer images

            // Get Links Corrected (We're in a different folder now)


            $DETAILS = eregi_replace("pgm-email_friend.php\?", "pgm-email_friend.php?id=$PROD[PRIKEY]&", $DETAILS);
            $DETAILS = eregi_replace("pgm-print_page.php\?", "../pgm-print_page.php?", $DETAILS);

            $DETAILS = eregi_replace("src=\"", "src=\"../", $DETAILS);           // Format all "local" image tags (Images NOT in images folder)
            $DETAILS = eregi_replace("src=\"../http", "src=\"http", $DETAILS);      // Now Correct Screw ups that WILL happen if this line is not here

            $DETAILS = eregi_replace("href=\"media/", "href=\"../media/", $DETAILS);   // Format all media folder links

            // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
            // IF A CUSTOM PHP INCLUDE HAS BEEN PLACED ON THIS PAGE, PROCESS IT
            // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

            if (eregi("##MIKEINC;", $DETAILS)) {

               $temp = eregi("<!-- ##MIKEINC;(.*)## -->", $DETAILS, $out);
               $INCLUDE_FILE = $out[1];

               $filename = "$doc_root/media/$INCLUDE_FILE";

               ob_start();
                  include("$filename");
                  $output = ob_get_contents();
               ob_end_clean();

               $replicate = "<!-- ##MIKEINC;$INCLUDE_FILE## -->";

               $DETAILS = eregi_replace($replicate, "<!-- START -->\n\n$output\n\n<!-- END -->", $DETAILS);

            }

            $THIS_DISPLAY .= "<div align=left><font face=Verdana, Arial, Helvetica size=2>\n";
            $THIS_DISPLAY .= "$DETAILS</div>\n";

         } // End if File Exists

      } // End if Detail Page Exists

      $THIS_DISPLAY .= "</td>\n";

		$THIS_DISPLAY .= "</tr>\n";
		$THIS_DISPLAY .= "</table>\n";

	} // End if More Info


   /*---------------------------------------------------------------------------------------------------------*
     ___           _                              ___                               _
    / __|_  _  ___| |_  ___  _ __   ___  _ _     / __| ___  _ __   _ __   ___  _ _ | |_  ___
   | (__| || |(_-<|  _|/ _ \| '  \ / -_)| '_|   | (__ / _ \| '  \ | '  \ / -_)| ' \|  _|(_-<
    \___|\_,_|/__/ \__|\___/|_|_|_|\___||_|      \___|\___/|_|_|_||_|_|_|\___||_||_|\__|/__/

	# PARAGRAPH FOUR : CUSTOMER COMMENTS (IF AVAILABLE/ACTIVE)
   /*---------------------------------------------------------------------------------------------------------*/
	if (eregi("Y", $OPTIONS['DISPLAY_COMMENTS'])) {

		$THIS_DISPLAY .= "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"4\" id=\"moreinfo-comments\">\n";
		$THIS_DISPLAY .= " <tr>\n";
		$THIS_DISPLAY .= "  <th>\n";
		$THIS_DISPLAY .= "   ".lang("Customer Comments")."\n";
		$THIS_DISPLAY .= "  </td>\n";
		$THIS_DISPLAY .= " </tr>\n";

		$THIS_DISPLAY .= " <tr>\n";
      $THIS_DISPLAY .= "  <td class=text>\n";

      // First, Are there any existing Commments?

      $comment_file = "CART_".$PROD['PRIKEY'].".REVIEW";
			$reviews = 0;
			$find_revs = mysql_query("select * from cart_comments where PROD_ID='".$PROD['PRIKEY']."' AND STATUS='approved' ORDER BY PRIKEY");
			if(mysql_num_rows($find_revs) > 0){
				$reviews = 1;
				$db_reviews = '';
				while($db_comments = mysql_fetch_assoc($find_revs)){
					$db_reviews .= $db_comments['COMMENT_HTML'];
				}
			}
      if (!file_exists("$comment_file")&& $reviews == 0) {

         $THIS_DISPLAY .= " <br/><b>".lang("Be the first to")." <a href=\"pgm-write_review.php?id=$PROD[PRIKEY]\">".lang("write a review")."</a> ".lang("of this product for other customers")."!</b><br/>&nbsp;&nbsp;[ <I><a href=\"pgm-write_review.php?id=$PROD[PRIKEY]\">".lang("Click Here")."</a></I> ]<br/><br/>\n\n";

      } else {

         $THIS_DISPLAY .= " <br/><b><a href=\"pgm-write_review.php?id=$PROD[PRIKEY]\">".lang("Write an online review")."</a> ".lang("and share your thoughts about this product with other customers.")."</b>&nbsp;&nbsp;&nbsp;&nbsp;[ <I><a href=\"pgm-write_review.php?id=$PROD[PRIKEY]\">".lang("Click Here")."</a></I> ]<br/><br/>\n\n";

         $file = fopen("$comment_file", "r");
            $product_comments = fread($file,filesize($comment_file));
         fclose($file);
					$product_comments .= $db_reviews;
         $product_comments = chop($product_comments);
         $product_comments = rtrim($product_comments);

         $THIS_DISPLAY .= " <div align=\"left\"><font face=verdana, Arial, Helvetica size=\"2\">\n";
         $THIS_DISPLAY .= " $product_comments</div>\n";

      }

      $THIS_DISPLAY .= " </td>\n";

		$THIS_DISPLAY .= " </tr>\n";
		$THIS_DISPLAY .= "</table>\n";

	} // End If Customer Comments Active

	// -----------------------------------------------------------------
	// PARAGRAPH FIVE : RECOMMENDED/RELATED PRODUCTS
	// -----------------------------------------------------------------

	if ($PROD['OPTION_RECOMMENDSKU'] != "" && $PROD['OPTION_RECOMMENDSKU'] != " ") {

	$THIS_DISPLAY .= "<BR><TABLE WIDTH=100% BORDER=0 CELLSPACING=0 CELLPADDING=2 class=text STYLE='border: inset BLACK 1px;'>\n";
	$THIS_DISPLAY .= "<TR>\n";
	$THIS_DISPLAY .= "<TD BGCOLOR=\"#".$OPTIONS[DISPLAY_HEADERBG]."\"><FONT COLOR=\"#".$OPTIONS[DISPLAY_HEADERTXT]."\"><B><FONT FACE=\"Verdana, Arial, Helvetica, sans-serif\">".lang("If you like this, you may also like").":</FONT></B></FONT>\n";
	$THIS_DISPLAY .= "</TD>\n";
	$THIS_DISPLAY .= "</TR>\n";
	$THIS_DISPLAY .= "<TR>\n";

		$THIS_DISPLAY .= "<TD>\n";

			$other_skus = split(",", $PROD[OPTION_RECOMMENDSKU]);
			$other_skus_count = count($other_skus);

			for ($z=0;$z<=$other_skus_count;$z++) {

				if ($other_skus[$z] != "") {

					$other_skus[$z] = ltrim($other_skus[$z]);
					$other_skus[$z] = rtrim($other_skus[$z]);

					$tsku = mysql_query("SELECT PRIKEY, PROD_NAME, PROD_DESC FROM cart_products WHERE PROD_SKU = '$other_skus[$z]'");
					$skuname = mysql_fetch_array($tsku);

					$skuname[PROD_DESC] = chop($skuname[PROD_DESC]);
					$skuname[PROD_DESC] = rtrim($skuname[PROD_DESC]);
					$skuname[PROD_DESC] = eregi_replace("\n", "<BR>", $skuname[PROD_DESC]);

					$THIS_DISPLAY .= "<B>&gt;&nbsp;<A HREF=\"pgm-more_information.php?id=$skuname[PRIKEY]&=SID\">$skuname[PROD_NAME]</A></B><BR><DIV CLASS=smtext>$skuname[PROD_DESC]</DIV><BR><BR>";

				}

			} // End For Loop

		$THIS_DISPLAY .= "</TD>\n";

	$THIS_DISPLAY .= "</TR>\n";
	$THIS_DISPLAY .= "</TABLE>\n";


} // End if Recommend Additional Skus is selected

#################################################################################
### SETUP SEARCH COLUMN HTML FOR DISPLAY (REGARDLESS OF FUNCTION CALL)
#################################################################################

$SEARCH_COLUMN = "";

ob_start();
	include("prod_search_column.inc");
	$SEARCH_COLUMN .= ob_get_contents();
ob_end_clean();


#################################################################################
### BUILD OVERALL TABLE TO PLACE SEARCH COLUMN TO THE LEFT OR RIGHT OF
### SEARCH RESULT DISPLAY AS DEFINED IN DISPLAY OPTIONS
#################################################################################

$FINAL_DISPLAY = "<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\" align=\"center\">\n";

$FINAL_DISPLAY .= " <tr>\n";

# Where should the search column be placed?
if ( eregi("L", $OPTIONS['DISPLAY_COLPLACEMENT'] ) ) {
   $FINAL_DISPLAY .= "  <td class=text width=\"150\" align=\"center\" valign=\"top\">\n";
   $FINAL_DISPLAY .= "   ".$SEARCH_COLUMN."\n";
   $FINAL_DISPLAY .= "  </td>\n";
   $FINAL_DISPLAY .= "  <td class=text align=\"center\" valign=\"top\">\n";
   $FINAL_DISPLAY .= "   ".$THIS_DISPLAY."\n";
   $FINAL_DISPLAY .= "  </td>\n";

} else {
   $FINAL_DISPLAY .= "  <td class=text align=\"center\" valign=\"top\">\n";
   $FINAL_DISPLAY .= "   ".$THIS_DISPLAY."\n";
   $FINAL_DISPLAY .= "  </td>\n";
   $FINAL_DISPLAY .= "  <td class=text width=\"150\" align=\"center\" valign=\"top\" id=\"searchcolumn\">\n";
   $FINAL_DISPLAY .= "   ".$SEARCH_COLUMN."\n";
   $FINAL_DISPLAY .= "  </td>\n";
}

$FINAL_DISPLAY .= " </tr>\n\n";
$FINAL_DISPLAY .= "</table>";


#################################################################################
### THE pgm-template_builder.php FILE COMPILES THE TEMPLATE DATA AND PAGE
### CONTENT DATA TOGETHER AND PUTS IT OUT AS THE $template_header AND
### $template_footer VARS RESPECTIVELY.
#################################################################################

$module_active = "yes";
include ("pgm-template_builder.php");

#################################################################################



$template_footer = eregi_replace("#CONTENT#", $FINAL_DISPLAY, $template_footer);
# UDT_CONTENT_SEARCH_REPLACE - Pull Global Search and Replace Vars and process now
$tResult = mysql_query("SELECT * FROM UDT_CONTENT_SEARCH_REPLACE");
while ($srRow = mysql_fetch_array($tResult)) {
	$repString = $srRow[REPLACE_WITH];
	if ($srRow[AUTO_IMAGE] != "NULL") { $repString = "<img src=\"images/$srRow[AUTO_IMAGE]\" align=absmiddle border=0>"; }
	if (strlen($srRow[SEARCH_FOR]) > 3) {
		$template_header = eregi_replace($srRow[SEARCH_FOR], $repString, $template_header);
		$template_footer = eregi_replace($srRow[SEARCH_FOR], $repString, $template_footer);
	}
} // End While
echo $template_header;
echo $template_footer;

echo "\n\n<SCRIPT language=Javascript>\n     window.focus();\n</SCRIPT>\n\n";

exit;

?>