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

session_start();
include($_SESSION['product_gui']);


#########################################################
### IF FORM SUBMITTED TO SAVE CATEGORY DATA, PERFORM
### SAVE/UPDATE ROUTINE AND RETURN WITH NOTIFICATION
#########################################################

# Save category name edit action
if ( $_POST['action'] == "savecat" && $_POST['cat_name'] != "" ) {
   //echo testArray($_POST); exit;
   $qry = "UPDATE cart_category SET category = '".$_POST['cat_name']."' WHERE keyfield = '".$_POST['cat_id']."'";
   mysql_query($qry);
}

# Delete action
if ($del == "yes") {
	mysql_query("DELETE FROM cart_category WHERE keyfield = '$key'");
}

if ($ACTION == "ADDCAT") {

	#######################################################
	### START DATABASE UPDATE/CREATE				    ###
	#######################################################

	$match = 0;
	$tablename = "cart_category";

		// ------------------------------------------------------
		// Make sure the table exists before adding new data
		// ------------------------------------------------------

		$result = mysql_list_tables("$db_name");
		$i = 0;
		while ($i < mysql_num_rows ($result)) {
			$tb_names[$i] = mysql_tablename ($result, $i);
			if ($tb_names[$i] == $tablename) {
				$match = 1;
			}
			$i++;
		}

		// ------------------------------------------------------
		// Perform Insert of New Category
		// ------------------------------------------------------
      # Increase length of category name field (v4.9 beta2 -- MM)
      $rez = mysql_query("SELECT * FROM cart_category");
      if ( mysql_fieldlen( $rez, 1 ) == 23 ) {
         $increase_length = "ALTER TABLE cart_category MODIFY category VARCHAR(255)";
         mysql_query($increase_length) || die("Could not increase length of category field in cart_category table!!");
      }

		if ($match == 1) {
			mysql_query("INSERT INTO cart_category VALUES('NULL','$ADDCATEGORY')");
		} else {
			mysql_db_query("$db_name","CREATE TABLE cart_category (keyfield INT NOT NULL AUTO_INCREMENT PRIMARY KEY,category VARCHAR(255))");
			mysql_query("INSERT INTO cart_category VALUES('NULL','$ADDCATEGORY')");
		}

} // End Category Add

#######################################################
### START HTML/JAVASCRIPT CODE			  			###
#######################################################

ob_start();

?>


<script language="JavaScript">
<!--
function SV2_findObj(n, d) { //v3.0
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=SV2_findObj(n,d.layers[i].document); return x;
}
function SV2_showHideLayers() { //v3.0
  var i,p,v,obj,args=SV2_showHideLayers.arguments;
  for (i=0; i<(args.length-2); i+=3) if ((obj=SV2_findObj(args[i]))!=null) { v=args[i+2];
    if (obj.style) { obj=obj.style; v=(v=='show')?'visible':(v='hide')?'hidden':v; }
    obj.visibility=v; }
}
function SV2_popupMsg(msg) { //v1.0
  alert(msg);
}
function SV2_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}

SV2_showHideLayers('addCartMenu?header','','hide');
SV2_showHideLayers('blankLayer?header','','hide');
SV2_showHideLayers('linkLayer?header','','hide');
SV2_showHideLayers('newsletterLayer?header','','hide');
SV2_showHideLayers('cartMenu?header','','show');
SV2_showHideLayers('menuLayer?header','','hide');
SV2_showHideLayers('editCartMenu?header','','hide');

//-->
</script>

<link rel="stylesheet" href="shopping_cart.css">

<?

$THIS_DISPLAY = "";

$THIS_DISPLAY .= "<table border=\"0\" cellpadding=5 cellspacing=\"0\" width=\"100%\">\n\n";
$THIS_DISPLAY .= " <tr>\n";
$THIS_DISPLAY .= "  <td align=\"center\" valign=\"top\" class=\"text\" width=\"50%\">\n";

	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	// Scale demo table to 150 width because this is the width displayed
	// on the client side when in actual operation
	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	# Edit cat form and cat list
   $THIS_DISPLAY .= "   <form name=\"editcat_form\" method=\"post\" action=\"categories.php\">\n";
   $THIS_DISPLAY .= "   <input type=\"hidden\" name=\"action\" value=\"savecat\">\n";
   $THIS_DISPLAY .= "   <input type=\"hidden\" name=\"cat_id\" value=\"".$_GET['edit_cat']."\">\n";
	$THIS_DISPLAY .= "   <table border=\"0\" cellpadding=\"5\" cellspacing=\"1\" width=\"100%\" class=\"feature_sub\">\n";
	$THIS_DISPLAY .= "    <tr>\n";
	$THIS_DISPLAY .= "     <td align=\"center\" valign=\"middle\" id=\"header2\" colspan=\"3\" class=\"fsub_title\">\n";
	$THIS_DISPLAY .= "      ".lang("Current Categories")."<BR>\n";
	$THIS_DISPLAY .= "     </td>\n";
	$THIS_DISPLAY .= "    </tr>\n";

	$result = mysql_query("SELECT * FROM cart_category ORDER BY category ASC");
	while ($row = mysql_fetch_array ($result)) {
		if ( strlen($row['category']) > 2 ) {

			# Static text or editing mode?
			if ( $_GET['edit_cat'] == $row['keyfield'] ) {
			   # EDIT MODE
            $THIS_DISPLAY .= "    <tr class=\"bg_green\">\n";

   			# [ cancel ]
   			$THIS_DISPLAY .= "     <td align=\"center\" valign=\"middle\" width=\"5%\">\n";
   			$THIS_DISPLAY .= "      [&nbsp;<a href=\"categories.php\" class=\"del\">Cancel</a>&nbsp;]</td>\n";

            # text field
   			$THIS_DISPLAY .= "     <td align=\"left\" valign=\"middle\">\n";
   			$THIS_DISPLAY .= "      <input type=\"text\" name=\"cat_name\" value=\"".$row['category']."\" style=\"width: 205px;\">\n";
   			$THIS_DISPLAY .= "     </td>\n";

   			# [ save ]
   			$THIS_DISPLAY .= "     <td align=\"center\" valign=\"middle\" width=\"5%\">\n";
   			$THIS_DISPLAY .= "      [&nbsp;<span class=\"hand green uline\" onclick=\"document.editcat_form.submit();\">Save</span>&nbsp;]</td>\n";

   		} elseif ( $_GET['edit_cat'] == "" ) { // Do not show the other cats other than the target if in edit mode
   		   # STATIC MODE
   		   $THIS_DISPLAY .= "    <tr>\n";

   			# [delete]
   			$THIS_DISPLAY .= "     <td align=\"center\" valign=\"middle\" bgcolor=WHITE width=5%>\n";
   			$THIS_DISPLAY .= "      [&nbsp;<a href=\"categories.php?del=yes&key=$row[keyfield]\" class=\"del\">Delete</a>&nbsp;]</td>\n";

   			# Static cat name
   			$THIS_DISPLAY .= "     <td align=\"left\" valign=\"middle\" bgcolor=WHITE><font FACE=Verdana SIZE=2 color=#000099>\n";
   			$THIS_DISPLAY .= "      ".$row['category']."</font></td>\n";

   			# [ edit ]
   			$THIS_DISPLAY .= "     <td align=\"center\" valign=\"middle\" bgcolor=WHITE width=5%>\n";
   			$THIS_DISPLAY .= "      [&nbsp;<a href=\"categories.php?edit_cat=".$row['keyfield']."\" class=\"edit\">Edit</a>&nbsp;]</td>\n";
   		}


			$THIS_DISPLAY .= "    </tr>\n";
		}
	}

	$THIS_DISPLAY .= "   </table>\n\n";
	$THIS_DISPLAY .= "   </form>\n\n"; // End edit category form


   $THIS_DISPLAY .= "  </td>\n";
   $THIS_DISPLAY .= "  <td align=\"left\" valign=\"top\" class=\"text\" width=\"50%\">\n";

	// Add new category Form
   $THIS_DISPLAY .= "   <form name=CATSAVE method=\"post\" ACTION=\"categories.php\">\n";
   $THIS_DISPLAY .= "   <input type=\"hidden\" name=\"ACTION\" value=\"ADDCAT\">\n";
	$THIS_DISPLAY .= "   <table border=\"0\" cellpadding=5 cellspacing=\"0\" class=\"feature_sub\" width=\"75%\">\n";
	$THIS_DISPLAY .= "    <tr>\n";
	$THIS_DISPLAY .= "     <td align=\"center\" valign=\"middle\" ID=header2 class=\"fsub_title\">\n";
	$THIS_DISPLAY .= "      ".lang("Add New Category")."<BR>\n";
	$THIS_DISPLAY .= "     </td>\n";
	$THIS_DISPLAY .= "    </tr>\n";

	$THIS_DISPLAY .= "    <tr>\n";
	$THIS_DISPLAY .= "     <td align=\"left\" valign=\"middle\" bgcolor=WHITE style=\"color: #000099;\">\n";
	$THIS_DISPLAY .= "     <b>".lang("New Category Name").":</b><BR><input class=\"text\" type=\"text\" name=\"ADDCATEGORY\" size=\"23\" maxlength=\"50\" value=\"\" style='width: 150px;'>\n";
	$THIS_DISPLAY .= "     <BR><BR>\n";
	$THIS_DISPLAY .= "     <input type=submit value=\"".lang("Add Category")."\" class=\"btn_save\" onmouseover=\"this.className='btn_saveon';\" onmouseout=\"this.className='btn_save';\">\n\n";

	$THIS_DISPLAY .= "     <BR><BR><div align=\"center\" style=\"background-color: #F5F5F5; border-top: 1px solid #999999;\"><font COLOR=#999999>".lang("To delete a category")."</font></div>";

	$THIS_DISPLAY .= "     </td>\n";
	$THIS_DISPLAY .= "    </tr>\n";
	$THIS_DISPLAY .= "   </table>\n";
	$THIS_DISPLAY .= "   </form>\n\n";


$THIS_DISPLAY .= "</td></tr></table>";

echo $THIS_DISPLAY;

# Grab module html into container var
$module_html = ob_get_contents();
ob_end_clean();

$module = new smt_module($module_html);
$module->add_breadcrumb_link("Shopping Cart Menu", "program/modules/mods_full/shopping_cart.php");
$module->add_breadcrumb_link("Product Categories", "program/modules/mods_full/shopping_cart/categories.php");
$module->icon_img = "skins/".$_SESSION['skin']."/icons/shopping_cart-enabled.gif";
$module->heading_text = "Product Categories";
$module->description_text = "Each of your shopping cart products (skus) must be associated with one or more product category. Examples: T-Shirts, Shoes, Hats, Widgets, Cogs, Sprockets...";
$module->good_to_go();
?>