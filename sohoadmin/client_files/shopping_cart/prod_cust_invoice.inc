<?php
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

//Grr. No me gusta grimlens.
error_reporting(E_PARSE);

$cartprefs = new userdata("cart");
$taxpref = new userdata("tax_rate_options");

$BCOUNTRY = strtoupper($BCOUNTRY);
$SCOUNTRY = strtoupper($SCOUNTRY);
$BSTATE = strtoupper($BSTATE);
$SSTATE = strtoupper($SSTATE);

$HTML = "<TABLE BORDER=0 CELLPADDING=5 CELLSPACING=0 WIDTH=100% BORDER=0>\n";
$HTML .= "<TR>\n";
$HTML .= "<TD ALIGN=LEFT VALIGN=TOP CLASS=text>\n";
$HTML .= "<B><U>".lang("BILLING INFORMATION")."</U></B>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<!-- EDIT --><BR><BR>\n";

$HTML .= "$BFIRSTNAME $BLASTNAME<BR>";

	if ($BCOMPANY != "") { $HTML .= "$BCOMPANY<BR>\n"; }

$HTML .= "$BADDRESS1<BR>\n";

	if ($BADDRESS2 != "") { $HTML .= "$BADDRESS2<BR>\n"; }

$HTML .= "$BCITY, $BSTATE &nbsp;&nbsp; $BZIPCODE<br>\n";

$HTML .= "$BCOUNTRY<BR><BR>\n";

$HTML .= "$BPHONE<BR>\n";
$HTML .= "$BEMAILADDRESS\n";

$HTML .= "</TD>\n";

# Show Shipping Info on Invoice?
if ( $cartprefs->get("disable_shipping") == "yes" ) {
   $show_shipping = "hidden";
}else{
   $show_shipping = "visible";
}

$HTML .= "<TD ALIGN=LEFT VALIGN=TOP CLASS=text style=\"visibility: ".$show_shipping.";\">\n";
$HTML .= "<B><U>".lang("SHIPPING INFORMATION")."</U></B>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<!-- EDIT --><BR><BR>\n";

$HTML .= "$SFIRSTNAME $SLASTNAME<BR>";

	if ($SCOMPANY != "") { $HTML .= "$SCOMPANY<BR>\n"; }

$HTML .= "$SADDRESS1<BR>\n";

	if ($SADDRESS2 != "") { $HTML .= "$SADDRESS2<BR>\n"; }

$HTML .= "$SCITY, $SSTATE &nbsp;&nbsp; $SZIPCODE<br>\n";

$tmpSC = split(" - ",$SCOUNTRY);
$HTML .= "$tmpSC[0] - $tmpSC[1]<BR><BR>\n";

$HTML .= "$SPHONE<BR>\n";
$HTML .= "</TD>\n";



$HTML .= "</TR>\n";
$HTML .= "</TABLE><BR>\n\n";

# Show view/edit cart link?
$cartpref = new userdata("cart");
if ( $cartpref->get("invoice_viewedit_link") == "yes" ) {
   $HTML .= "\n<!-- EDITCART -->\n";
}

if ( $taxpref->get('vat-or-gst') == "" ) {
	$taxpref->set("vat-or-gst", 'VAT');
}

############################################################################################
// Show invoice notice for offline shipping calculation
############################################################################################
if ( $SHIPPING_OPTS[SHIP_METHOD] == "Offline" ) {
   $HTML .= "<table bgcolor=\"$OPTIONS[DISPLAY_HEADERBG]\" width=\"100%\" border=\"0\" cellpadding=\"6\" cellspacing=\"0\" class=\"text\" style=\"border: 1px solid #000000;\">\n";
   $HTML .= " <tr>\n";
   $HTML .= "  <td align=\"left\">\n";
   $HTML .= "   <font color=\"$OPTIONS[DISPLAY_HEADERTXT]\">\n";
   $HTML .= "   ".$SHIPPING_OPTS[NOTICE];
   $HTML .= "   </font>\n";
   $HTML .= "  </td>\n";
   $HTML .= " </tr>\n";
   $HTML .= "</table>\n";
   $HTML .= "<br>\n";
}


############################################################################################
// -----------------------------------------------------------------------------------------
// NOW DISPLAY ACTUAL INVOICE LINE ITEMS
// -----------------------------------------------------------------------------------------
############################################################################################

$HTML .= "<TABLE BORDER=0 CELLPADDING=5 CELLSPACING=0 CLASS=text ALIGN=CENTER WIDTH=100% STYLE='border: 1px solid black;' id=\"invoice-parent\">\n";

$tmp_formdata = split(":p:", $CART_FORMDATA);
$tmp_keyid = split(";", $CART_KEYID);
$tmp_qty = split(";", $CART_QTY);
$tmp_skuno = split(";", $CART_SKUNO);
$tmp_catno = split(";", $CART_CATNO);

$CART_PRODNAME = str_replace('&lt;', '<', $CART_PRODNAME);
$CART_PRODNAME = str_replace('&gt;', '>', $CART_PRODNAME);

$tmp_name = split(";", $CART_PRODNAME);

foreach($tmp_name as $var=>$val){
	$tmp_name[$var] = str_replace('>', '&gt;', $tmp_name[$var]);
	$tmp_name[$var] = str_replace('<', '&lt;', $tmp_name[$var]);
}


$tmp_subcat = split(";", $CART_SUBCAT);
$tmp_varname = split(";", $CART_VARNAME);

$tmp_price = split(";", $CART_UNITPRICE);
$tmp_sub = split(";", $CART_UNITSUBTOTAL);

$display_subtotal = 0;	// Reset Display sub_total; we'll calculate that on the fly
$line_items = count($tmp_qty);	// Count the number of array vars we have after split
$line_items = $line_items - 2;	// Subtract 2 because (a)we start count at 0 (b) we always have a trailing semi-colon;

# Alternate row bg color
if ($sep == "row-altbg") { $sep = "row-normalbg"; } else { $sep = "row-altbg"; }

/*--Test session data---*
$HTML .= " <tr>\n";
$HTML .= "  <td colspan=\"4\" align=\"left\" class=\"text\">\n";
$HTML .= "   <b>Order Number: </b>".$ORDER_NUMBER."\n";
$HTML .= "  </td>\n";
$HTML .= " </tr>\n";
$HTML .= " <tr>\n";
$HTML .= "  <td colspan=\"4\" align=\"left\" class=\"text\">\n";
$HTML .= "   <b>Order Total: </b>".$ORDER_TOTAL."\n";
$HTML .= "  </td>\n";
$HTML .= " </tr>\n";
/*--Test session data---*/

$HTML .= "<TR>\n";
$HTML .= "<TD ALIGN=LEFT VALIGN=TOP CLASS=text BGCOLOR=$OPTIONS[DISPLAY_HEADERBG] WIDTH=40%><FONT COLOR=$OPTIONS[DISPLAY_HEADERTXT]>\n";
$HTML .= "<B>".lang("Product Name")."\n";
$HTML .= "</TD>\n";
$HTML .= "<TD ALIGN=LEFT VALIGN=TOP CLASS=text BGCOLOR=$OPTIONS[DISPLAY_HEADERBG] WIDTH=100><FONT COLOR=$OPTIONS[DISPLAY_HEADERTXT]>\n";
$HTML .= "<B>".lang("Unit Price")."\n";
$HTML .= "</TD>\n";
$HTML .= "<TD ALIGN=LEFT VALIGN=TOP CLASS=text BGCOLOR=$OPTIONS[DISPLAY_HEADERBG] WIDTH=100><FONT COLOR=$OPTIONS[DISPLAY_HEADERTXT]>\n";
$HTML .= "<B>".lang("Quantity")."\n";
$HTML .= "</TD>\n";
$HTML .= "<TD ALIGN=LEFT VALIGN=TOP CLASS=text BGCOLOR=$OPTIONS[DISPLAY_HEADERBG] WIDTH=100><FONT COLOR=$OPTIONS[DISPLAY_HEADERTXT]>\n";
$HTML .= "<B>".lang("Sub-Total")."\n";
$HTML .= "</TD>\n";
$HTML .= "</TR>\n";

for ($z=0;$z<=$line_items;$z++) {

	$display_subtotal = $display_subtotal + $tmp_sub[$z];	// Add Unit Sub Total to Total Sub Total ??? Confused yet?

	if ($sep == "row-altbg") { $sep = "row-normalbg"; } else { $sep = "row-altbg"; }

	if (strlen($tmp_subcat[$z]) > 2) { $tmp_varname[$z] = "- " . $tmp_varname[$z]; } // Format for proper display

	// -----------------------------------------------------------
	// Build display for sku number and catalog number in invoice
	// -----------------------------------------------------------

		$ref_id_display = "<div class=\"invoice-sku_name\">";
		if ($tmp_skuno[$z] != "") { $ref_id_display .= "[<U>Sku</U>: $tmp_skuno[$z]]  "; }
		if ($tmp_catno[$z] != "") { $ref_id_display .= "[<U>Cat</U>: $tmp_catno[$z]]  "; }
		$ref_id_display .= "</div>";
		$ref_id_display = strtoupper($ref_id_display);

	// -----------------------------------------------------------

	$tmp_name[$z] = str_replace("#Q#", "&quot;", $tmp_name[$z]);	// Fix for quotes not appearing in product name

	$HTML .= "<TR>\n";
	$HTML .= "<TD ALIGN=LEFT VALIGN=TOP class=\"text ".$sep."\">\n";

	//Do we need to display special VAT rate?
	$result = mysql_query("SELECT * FROM cart_products WHERE PROD_SKU = '$tmp_skuno[$z]'");
   $getVat = mysql_fetch_array($result);
   $specVat = "";
   if ($getVat['SPECIAL_TAX'] != "" && $OPTIONS['CHARGE_VAT'] == "yes" && $OPTIONS['VAT_REG'] != "vatnum") {
      $specVat = "<font color='#7A0000'>(".$taxpref->get('vat-or-gst')." ".$getVat['SPECIAL_TAX']."%)</font>";
   }

	$HTML .= "$ref_id_display<U>".$tmp_name[$z]."</U> ".$specVat."<BR><DIV CLASS=smtext>".$tmp_subcat[$z]." ".$tmp_varname[$z]."\n";
	$HTML .= "<font style=\"font-family:arial,helvetica,sans-serif; font-size:9px; color:darkgreen; font-weight:bold;\">";

	//format form data into line-items
   $form_line = split(":br:", $tmp_formdata[$z]);
   $num_fl = count($form_line);
   for ($f=0;$f<=$num_fl;$f++) {
      $HTML .= $form_line[$f] . "<br>\n";
      }

eval(hook("pgm-more_information.php:dispinv"));

	$HTML .= "</font>";
	$HTML .= "</TD>\n";
	$HTML .= "<TD ALIGN=LEFT VALIGN=TOP class=\"text ".$sep."\">\n";
	$HTML .= $dSign."".$tmp_price[$z]."\n";
	$HTML .= "</TD>\n";
	$HTML .= "<TD ALIGN=LEFT VALIGN=TOP class=\"text ".$sep."\">\n";
	$HTML .= "[".$tmp_qty[$z]."]\n";
	$HTML .= "</TD>\n";
	$HTML .= "<TD ALIGN=LEFT VALIGN=TOP class=\"text ".$sep."\">\n";

	$tmp_sub[$z] = sprintf ("%01.2f", $tmp_sub[$z]);

	$HTML .= $dSign."$tmp_sub[$z]\n";
	$HTML .= "</TD>\n";

	$HTML .= "</TR>\n";

}	// End of $z Loop


// ========================================================================================
// DISPLAY ORDER SUB-TOTAL
// ========================================================================================

if ($sep == "row-altbg") { $sep = "row-normalbg"; } else { $sep = "row-altbg"; }

$HTML .= "<TR>\n";
$HTML .= "<TD ALIGN=LEFT VALIGN=MIDDLE class=\"text ".$sep."\">\n";
$HTML .= "<FONT SIZE=1 FACE=ARIAL></FONT>\n";
$HTML .= "</TD>\n";

$HTML .= "<TD ALIGN=RIGHT VALIGN=MIDDLE class=\"text ".$sep."\" COLSPAN=2>\n";
$HTML .= "Sub-Total</B>:\n";
$HTML .= "</TD>\n";

$HTML .= "<TD ALIGN=LEFT VALIGN=MIDDLE class=\"text ".$sep."\">\n";

$display_subtotal = sprintf ("%01.2f", $display_subtotal);

$HTML .= $dSign."<U>$display_subtotal</U>\n";
$HTML .= "</TD>\n";

$HTML .= "</TR>\n";

// ========================================================================================
// DISPLAY TAX TOTAL
// ========================================================================================

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
// Read Tax Table to determine if current ship-to state/country needs
// to be charged sales tax
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//Make sure tax variables are upper case
$BSTATE = strtoupper($BSTATE);
$SSTATE = strtoupper($SSTATE);
$BCOUNTRY = strtoupper($BCOUNTRY);
$SCOUNTRY = strtoupper($SCOUNTRY);

$taxpref = new userdata("tax_rate_options");

$stax_perc = "$stax_perc";
$result = mysql_query("SELECT * FROM cart_tax");
while ($TAX = mysql_fetch_array($result)) {
//	if ($BSTATE == trim($TAX['state'])) {
//		$stax_perc = $TAX['rate'];
//	}
	// BUG FIX #656 Provided by nsna_andrew
   if ($taxpref->get("taxby") == "SCOUNTRY") {
      if ($SSTATE == trim($TAX['state'])) {
         $stax_perc = $TAX['rate'];
      }
   } else {
      if ($BSTATE == trim($TAX['state'])) {
         $stax_perc = $TAX['rate'];
      }
   }
}

//======================
//Check for VAT tax
//======================

# Make sure there's a VAT number
if ($OPTIONS[CHARGE_VAT] == "yes" && $OPTIONS[VAT_REG] != "vatnum") {
   $ctax_perc = "$ctax_perc";
   $result = mysql_query("SELECT * FROM cart_vat");
   while ( $VAT = mysql_fetch_array($result) ) {

      # Set default if no value
      if ( $taxpref->get("taxby") == "" ) { $taxpref->set("taxby", "BCOUNTRY"); }

     	if ( ${$taxpref->get("taxby")} == $VAT['country'] ) {
       	$ctax_perc = $VAT['rate'];
      }
   }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
// If so, calculate tax
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
if ( ($stax_perc != "") || ($ctax_perc != "") ) {
	$staxrate = $stax_perc;
	$ctaxrate = $ctax_perc;
	$scalcTax = $staxrate / 100;
	$ccalcTax = $ctaxrate / 100;
	$exempt = 0;                     // Bugzilla #38
	$vatSub = 0;                     // Euro Stuff - 040408
	$numSkus = count($tmp_skuno);    // Bugzilla #38

// Account for "tax exempt" items in calculation - Bugzilla #38
// 2004-04-08: Also account for items with special VAT rate
// 2004-08-02: Check new "SPECIAL_TAX" field and leave SHIPC alone

	for ($m=0;$m<=$numSkus;$m++) {
	   $result = mysql_query("SELECT * FROM cart_products WHERE PROD_SKU = '$tmp_skuno[$m]'");
      $getProd = mysql_fetch_array($result);
	   if ($getProd[OPTION_CHARGETAX] == "N" ) {
	      $exempt += $tmp_sub[$m];
	   }
	   if ($getProd[SPECIAL_TAX] != "") {
	      $vatRate = $getProd[SPECIAL_TAX];
	      $calcVat = $vatRate / 100;
	      $vatSub += $calcVat * $tmp_sub[$m];

	   }
	}

	$tax_subtotal = $display_subtotal - $exempt;  // Bugzilla #38

	$sthisTax = $tax_subtotal * $scalcTax;  // Bugzilla #38
	$cthisTax = $tax_subtotal * $ccalcTax;  // Bugzilla #38

	$stax = number_format($sthisTax,2);
	$ctax = number_format($cthisTax,2);

	$staxrate = "( $stax_perc% )";

	if ( $ctax_perc == "" ){
	   $ctaxrate = "( 0% )";
	} else {
	   $ctaxrate = "( $ctax_perc% )";
	}


} else {
	$staxrate = "";
	$ctaxrate = "";

// Account for Special Tax items when no tax is charged - Bugzilla #38
	$numSkus = count($tmp_skuno);    // Bugzilla #38
	for ($s=0;$s<=$numSkus;$s++) {
	   $result = mysql_query("SELECT * FROM cart_products WHERE PROD_SKU = '$tmp_skuno[$s]'");
      $getSpecial = mysql_fetch_array($result);
	   if ($getSpecial[SPECIAL_TAX] != "") {
	      $vatRate = $getSpecial[SPECIAL_TAX];
	      $calcVat = $vatRate / 100;
	      $vatSub += $calcVat * $tmp_sub[$s];
	   }
	}

	//$sdisplay_tax = "0.00";
	//$cdisplay_tax = "0.00";
}

# Country tax: Do not display here if option to charge tax after shipping is set
if ( $cthisTax != "" && $cthisTax > 0 && $taxpref->get("taxwhen") != "aftershipping" ) {
   $HTML .= "<TR>\n";
   $HTML .= "<TD ALIGN=LEFT VALIGN=MIDDLE class=\"text ".$sep."\">\n";
   $HTML .= "<FONT SIZE=1 FACE=ARIAL></FONT>\n";
   $HTML .= "</TD>\n";

   //$HTML .= "<TD ALIGN=RIGHT VALIGN=MIDDLE class=\"text ".$sep."\" COLSPAN=2>\n";

   $HTML .= "<TD ALIGN=RIGHT VALIGN=MIDDLE class=\"text ".$sep."\" COLSPAN=2>\n";
   $HTML .= " ".$BCOUNTRY." Tax ".$ctaxrate."</b>:\n";
   $HTML .= "</TD>\n";


   $HTML .= "<TD ALIGN=LEFT VALIGN=MIDDLE class=\"text ".$sep."\">\n";

   $cthisTax = sprintf ("%01.2f", $cthisTax);

   $HTML .= $dSign.$cthisTax."\n";
   $HTML .= "</TD>\n";
   $HTML .= "</TR>\n";
}

# Vat item tax
if ( $vatSub != "" && $vatSub != "0.00" ){
   $HTML .= "<TR>\n";
   $HTML .= "<TD ALIGN=LEFT VALIGN=MIDDLE class=\"text ".$sep."\">\n";
   $HTML .= "<FONT SIZE=1 FACE=ARIAL></FONT>\n";
   $HTML .= "</TD>\n";


   $HTML .= "<TD ALIGN=RIGHT VALIGN=MIDDLE class=\"text ".$sep."\" COLSPAN=2>\n";
   $HTML .= lang("Special Tax")." ".$getVat[SPECIAL_TAX]."%</B>: \n";
   $HTML .= "</TD>\n";

   $HTML .= "<TD ALIGN=LEFT VALIGN=MIDDLE class=\"text ".$sep."\">\n";

   $vatSub = sprintf ("%01.2f", $vatSub);


   $HTML .= $dSign."$vatSub\n";
   $HTML .= "</TD>\n";
   $HTML .= "</TR>\n";
}


# State tax: Do not display here if option to charge tax after shipping is set
if ( $sthisTax != "" && $sthisTax != "0.00" && $taxpref->get("taxwhen") != "aftershipping" ) {
   $HTML .= "<TR>\n";
   $HTML .= "<TD ALIGN=LEFT VALIGN=MIDDLE class=\"text ".$sep."\">\n";
   $HTML .= "<FONT SIZE=1 FACE=ARIAL></FONT>\n";
   $HTML .= "</TD>\n";
   $HTML .= "<TD ALIGN=RIGHT VALIGN=MIDDLE class=\"text ".$sep."\" COLSPAN=2>\n";
   // BUG FIX #656 Provided by nsna_andrew
   $HTML .= ($taxpref->get("taxby") == "SCOUNTRY" ? $SSTATE : $BSTATE);
   $HTML .= " ".lang("Tax")." ".$staxrate.": \n";
//   $HTML .= $BSTATE." ".lang("Tax")." ".$staxrate."</B>: \n";
   $HTML .= "</TD>\n";
   $HTML .= "<TD ALIGN=LEFT VALIGN=MIDDLE class=\"text ".$sep."\">\n";

   $sthisTax = sprintf ("%01.2f", $sthisTax);

   $HTML .= $dSign."<span id=\"state_tax-total\">".$sthisTax."</span>\n";
   $HTML .= "</TD>\n";
   $HTML .= "</TR>\n";
}

###########################################################################################
// DISPLAY SHIPPING TOTAL
###########################################################################################
$HTML .= " <TR>\n";
$HTML .= "  <TD ALIGN=RIGHT VALIGN=MIDDLE class=\"text ".$sep."\" COLSPAN=3>\n";
$HTML .= "   $SHIPPING_INSTRUCTIONS &nbsp;&nbsp; ".lang("Shipping & Handling")."</B>:\n";
$HTML .= "  </TD>\n";

$HTML .= "  <TD ALIGN=LEFT VALIGN=MIDDLE class=\"text ".$sep."\">\n";
$sshiptax = 0;
$cshiptax = 0;

// Do not echo price value if using Offline Shipping
// ===================================================
if ( $SHIPPING_OPTS[SHIP_METHOD] == "Offline" ) {
   // Pending Calculation
   $HTML .= "   <font color=\"#d70000\">".lang("Pending Calculation")."</font>\n";
} else {

   # Account for shipping option to disable charging shipping for individual products
   # Note: Only for non-subtotal shipping charge method. SubTotal-based shipping charges calculated in pgm-checkout.php:825
   if ( $SHIPPING_OPTS['SHIP_METHOD'] != "SubTotal" ) {
   	for ($m=0;$m<=$numSkus;$m++) {

   	   $result = mysql_query("SELECT * FROM cart_products WHERE PROD_SKU = '$tmp_skuno[$m]'");
         $getProd = mysql_fetch_array($result);

   				if ($getProd['OPTION_CHARGESHIPPING'] == "N") {
                  $amoutToDel = $getProd['PROD_SHIPA']*$tmp_qty[$m];
                  $SHIPPING_TOTAL = $SHIPPING_TOTAL - $amoutToDel;

//                  # Testing - Items with disabled shipping
//                  echo "shipping for sku num(".$getProd[PROD_SKU].") and prikey (".$getProd[PRIKEY].") is (".$getProd[PROD_SHIPA].")<br>";
//                  echo "The current row is (".$m.")<br>";
//                  echo "qty in session (".$tmp_qty[$m].")<br>";
//                  echo "This is the amount to delete (".$amoutToDel.")<br>";

   				}

   		}
   }
   $SHIPPING_TOTAL = sprintf ("%01.2f", $SHIPPING_TOTAL);
   $HTML .= "   ".$dSign.$SHIPPING_TOTAL."\n";
}

$HTML .= "  </TD>\n";
$HTML .= " </TR>\n";


# STATE TAX: Update tax total to include shipping?
# Include shipping charges in tax calculation?
if ( $taxpref->get("taxwhen") == "aftershipping" && $sthisTax > 0 ) {

	// Cameron Fix, ( make sure to not include shipping total twice if using country and state tax);
	$state_tax = 'included';
	$state_sub_total_with_shipping = $tax_subtotal + $SHIPPING_TOTAL;
	// end cam fix

   $tax_subtotal = $tax_subtotal + $SHIPPING_TOTAL;
   $sthisTax = $scalcTax * $tax_subtotal;
//   $HTML .= " <br/>New taxed total: [".$tax_subtotal."]<br/>";
//   $HTML .= " <br/>New tax amount: [".$taxed_shipping."]<br/>";

   # Display tax total after shipping charges
   $HTML .= " <tr>\n";
   $HTML .= "  <td align=\"left\" valign=\"middle\" class=\"text ".$sep."\">\n";
   $HTML .= "   &nbsp;\n";
   $HTML .= "  </td>\n";
   $HTML .= "  <td align=\"right\" valign=\"middle\" class=\"text ".$sep."\" colspan=2>\n";
   // BUG FIX #656 Provided by nsna_andrew
   $HTML .= ($taxpref->get("taxby") == "SCOUNTRY" ? $SSTATE : $BSTATE);
   $HTML .= " ".lang("Tax")." ".$staxrate.": \n";
//   $HTML .= $BSTATE." ".lang("Tax")." ".$staxrate."</b>: \n";
   $HTML .= "  </td>\n";
   $HTML .= "  <td align=\"left\" valign=\"middle\" class=\"text ".$sep."\">\n";
   $sthisTax = sprintf ("%01.2f", $sthisTax);
   $HTML .= $dSign."<span id=\"state_tax-total\">".$sthisTax."</span>\n";
   $HTML .= "  </td>\n";
   $HTML .= " </tr>\n";
}

# COUNTRY TAX: Update tax total to include shipping?
# Include shipping charges in tax calculation?
if ( $taxpref->get("taxwhen") == "aftershipping" && $cthisTax > 0 ) {

	// Cameron Fix, ( make sure to not include shipping total twice if using country and state tax);
	if($state_tax == 'included'){
	   $tax_subtotal = $state_sub_total_with_shipping;
	   $cthisTax = $ccalcTax * $tax_subtotal;
	} else {
	   $tax_subtotal = $tax_subtotal + $SHIPPING_TOTAL;
	   $cthisTax = $ccalcTax * $tax_subtotal;
	}
	// end cam fix

//   $HTML .= " <br/>New taxed total: [".$tax_subtotal."]<br/>";
//   $HTML .= " <br/>New tax amount: [".$taxed_shipping."]<br/>";

   # Display tax total after shipping charges
   $HTML .= " <tr>\n";
   $HTML .= "  <td align=\"left\" valign=\"middle\" class=\"text ".$sep."\">\n";
   $HTML .= "   &nbsp;\n";
   $HTML .= "  </td>\n";
   $HTML .= "  <td align=\"right\" valign=\"middle\" class=\"text ".$sep."\" colspan=2>\n";
   $HTML .= ${$taxpref->get("taxby")}." ".lang("Tax")." ".$ctaxrate."</b>: \n";
   $HTML .= "  </td>\n";
   $HTML .= "  <td align=\"left\" valign=\"middle\" class=\"text ".$sep."\">\n";
   $cthisTax = sprintf ("%01.2f", $cthisTax);
   $HTML .= $dSign."<span id=\"state_tax-total\">".$cthisTax."</span>\n";
   $HTML .= "  </td>\n";
   $HTML .= " </tr>\n";
}


###########################################################################################
// DISPLAY TOTAL SALE COST
###########################################################################################

//more testing
//echo "<B>SHIPPING_TOTAL >> ($SHIPPING_TOTAL)</B><BR>\n";

$ORDER_TOTAL = $display_subtotal + $sthisTax + $cthisTax + $vatSub + $SHIPPING_TOTAL;

//echo "ORDER_TOTAL = display_subtotal($display_subtotal) + display_tax($display_tax) + SHIPPING_TOTAL($SHIPPING_TOTAL)<BR>\n";

$ORDER_TOTAL = sprintf ("%01.2f", $ORDER_TOTAL);

//echo "FINAL ORDER_TOTAL ----->> $ORDER_TOTAL\n";

$HTML .= "<TR>\n";
$HTML .= "<TD ALIGN=LEFT VALIGN=MIDDLE class=\"text ".$sep."\">\n";
$HTML .= "<FONT SIZE=1 FACE=ARIAL></FONT>\n";
$HTML .= "</TD>\n";

$HTML .= "<TD ALIGN=RIGHT VALIGN=MIDDLE class=\"text ".$sep."\" COLSPAN=2>\n";
$HTML .= "<B>".lang("Total")."</B>:\n";
$HTML .= "</TD>\n";

$HTML .= "<TD ALIGN=LEFT VALIGN=MIDDLE class=\"text ".$sep."\">\n";

$HTML .= "<FONT COLOR=MAROON><U>".$dSign.$ORDER_TOTAL."</U></FONT>\n";
$HTML .= "</TD>\n";

$HTML .= "</TR>\n";

// Register ORDER_TOTAL with our session for databasing and payment processing
// -------------------------------------------------------------------------------------

if (!session_is_registered("ORDER_TOTAL")) {
	session_register("ORDER_TOTAL");
}

$_SESSION['ORDER_TOTAL'] = $ORDER_TOTAL;


$HTML .= "</TABLE>\n\n";

echo $HTML;

?>