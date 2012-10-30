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

$THIS_DISPLAY = "";
$updated_text = "";

include("pgm-cart_config.php");
include_once("sohoadmin/includes/emulate_globals.php");

/////Custom Form Session Store
if($_POST['special_var_form'] == 'yes'){
	$N_qty_cnt = 0;
	foreach($_POST as $va=>$vu){
		$Mypost_ar[$va]=$vu;
		if(eregi('^qty~', $va)){
			if($vu > 0){
				$N_qty_cnt = $N_qty_cnt + $vu;
				$Mypost_ar_items[]=array($va,$vu);
			} else {
				unset($_POST[$va]);
			}
		}
	}

	$_REQUEST['id'] = $Mypost_ar['id'];
	$_REQUEST['goto_checkout'] = $Mypost_ar['goto_checkout'];
	$_POST['special_var_form'] = 'yes';
	$_REQUEST['special_var_form'] = 'yes';
	$qty = $N_qty_cnt;

}

if($_POST['FORMLOOP'] != '') {
	$IDsku = $_POST['id'];
	if(count($_FILES) >= 1){
	  $fcount = count($_FILES['fileupload']['tmp_name']);
	  $f2count = 1;

	  foreach($_FILES['fileupload']['tmp_name'] as $filnum=>$fildat) {
	  	$_POST['Item_'.$f2count.'_fileuploaded'] = $_FILES['fileupload']['name'][$filnum];
	    ++$f2count;
	  }
	}

	$_SESSION['formdata'][$IDsku] = '';
	foreach($_POST as $xi=>$xii) {
		//echo $xi."<br/>";
		if(eregi('Item_', $xi)) {
			$replace = 'Item_[0-9]+_';
			$xi = eregi_replace('Item_[0-9]+_', '', $xi);
			$_SESSION['formdata'][$IDsku][$xi][]=$xii;
		}
	}
}

include_once('pull-policies.inc.php');

/////End Custom Form Session Store
##########################################################################
### INSERT FUNCTION TO KILL ALL NON NUMERIC CHARACTERS FROM QTY
### Bugzilla #25
##########################################################################

function alpha_only ($sterile_var) {

	$sterile_var = stripslashes($sterile_var);
	$st_l = strlen($sterile_var);
	$st_a = 0;
	$tmp = "";
	while($st_a != $st_l) {
		$temp = substr($sterile_var, $st_a, 1);
		if (eregi("[0-9]", $temp)) { $tmp .= $temp; }
		$st_a++;
	}
	$sterile_var = $tmp;
	return $sterile_var;
}


if($_REQUEST['subcat'] != ''){
	$subcat = str_replace('<', '&lt;', $_REQUEST['subcat']);
	$subcat = str_replace('>', '&gt;', $subcat);
	$_POST['subcat'] = $subcat;
	$_GET['subcat'] = $subcat;
	$_REQUEST['subcat'] = $subcat;
}

if($_REQUEST['price'] != ''){
	$price = str_replace('<', '&lt;', $_REQUEST['price']);
	$price = str_replace('>', '&gt;', $price);
	$_POST['price'] = $price;
	$_GET['price'] = $price;
	$_REQUEST['price'] = $price;
}

if($_REQUEST['qty'] != ''){
	$qty = str_replace('<', '&lt;', $_REQUEST['qty']);
	$qty = str_replace('>', '&gt;', $qty);
	$_POST['qty'] = $qty;
	$_GET['qty'] = $qty;
	$_REQUEST['qty'] = $qty;
}

eval(hook("pgm-add_cart.php:miscfunctions"));

##########################################################################
### WE WILL NEED TO KNOW THE DATABASE NAME; UN; PW; ETC TO OPERATE THE
### REAL-TIME EXECUTION.  THIS IS CONFIGURED IN THE isp.conf FILE
##########################################################################


$dot_com = $this_ip;	// Assign dot_com variable to configured ip address

$cartpref = new userdata("cart"); // Emergency justincase ducttape...should be able to kill this

##########################################################################
### READ SHOPPING CART SETUP OPTIONS
##########################################################################

$result = mysql_query("SELECT * FROM cart_options");
$OPTIONS = mysql_fetch_array($result);

//040406 - Pull Currency Info
$dSign = $OPTIONS['PAYMENT_CURRENCY_SIGN'];
$dType = $OPTIONS['PAYMENT_CURRENCY_TYPE'];

# Restore css styles array
$getCss = unserialize($OPTIONS['CSS']);

##########################################################################
### READ PRODUCT DATA BASED ON PRODUCT ID TO DEAL WITH
##########################################################################

$result = mysql_query("SELECT * FROM cart_products WHERE PRIKEY = '$id'");
$PROD = mysql_fetch_array($result);

# Restore price variation arrays
$PROD['sub_cats'] = unserialize($PROD['sub_cats']);
$PROD['variant_names'] = unserialize($PROD['variant_names']);
$PROD['variant_prices'] = unserialize($PROD['variant_prices']);

eval(hook("pgm-add_cart.php:initial_data"));

##########################################################################
### PROCESS CUSTOM FORM INFORMATION IF THIS PRODUCT REQUIRES IT
##########################################################################
if($_POST['special_var_form'] == 'yes'){
	$N_qty_cnt = 0;
	foreach($_POST as $va=>$vu){
		$Mypost_ar[$va]=$vu;
		if(eregi('^qty~', $va)){
			if($vu > 0){
				$N_qty_cnt = $N_qty_cnt + $vu;
				$Mypost_ar_items[]=array($va,$vu);
			}
		}
	}
	//unset($_REQUEST);
	//$_REQUEST = '';
	$_REQUEST['id'] = $Mypost_ar['id'];
	$_REQUEST['goto_checkout'] = $Mypost_ar['goto_checkout'];
//	$_REQUEST['qty'] = $N_qty_cnt;
	$_POST['special_var_form'] = 'yes';
	$_REQUEST['special_var_form'] = 'yes';
	$qty = $N_qty_cnt;

}
if (eregi(".FORM", $PROD['OPTION_FORMDATA']) && $FORMFLAG != "ON") {

	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	// Step 1: Build our own FORM submission
	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	$THIS_DISPLAY .= "<form enctype=\"multipart/form-data\" method=post action=\"pgm-add_cart.php\" name=\"attachment_form\">\n\n";

	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	// Step 2: Pass all variables sent to this script as hidden vars first
	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	reset($_REQUEST);
	while (list($name, $value) = each($_REQUEST)) {
		$value = stripslashes($value);
		$value = htmlspecialchars($value);	// Bugzilla #13
		$THIS_DISPLAY .= "<INPUT TYPE=HIDDEN NAME=\"".$name."\" VALUE=\"".$value."\">\n";
	}

	$THIS_DISPLAY .= "\n\n";

	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	// Step 3: Read custom form (.form) html into memory and process display
	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	$filename = chop($PROD['OPTION_FORMDATA']);
	$filename = "../media/$filename";

	$file = fopen("$filename", "r");
	$body = fread($file,filesize($filename));
	fclose($file);

	$body = eregi_replace(">>\"", "&gt;&gt;\"", $body);
	$body = eregi_replace(">", ">\n", $body);		// Make sure that each HTML TAG has a line feed after it
	$lines = split("\n", $body);				// Split each line into array for easy manipulation
	$numLines = count($lines);				// How many lines do we have?


	if ($PROD['OPTION_FORMDISPLAY'] == "PERQTY") {
		$num_times = $qty;
		$form_title = lang("Item")." N ".lang("Details");
		$instructions = lang("Please fill out the following information needed for this individual item").":";
	} else {
		$num_times = 1;
		$form_title = lang("Details");
		$instructions = lang("Please fill out the following information regarding this product").":";
	}

	// Send variable flags to "add routine" so that we can handle these variables as an add-on to
	// the VAR_NAME field of the cart

	$THIS_DISPLAY .= "<INPUT TYPE=HIDDEN NAME=FORMFLAG VALUE=\"ON\">\n";
	$THIS_DISPLAY .= "<INPUT TYPE=HIDDEN NAME=FORMLOOP VALUE=\"".$num_times."\">\n\n";

	for ($y=1;$y<=$num_times;$y++) {			// Show Form Based on Per Qty or Per Item

		$tmp = ereg_replace(" N ", " $y ", $form_title);

		$THIS_DISPLAY .= "<TABLE BORDER=0 CELLPADDING=4 CELLSPACING=0 WIDTH=100% CLASS=text STYLE='border: 1px inset black;'>\n";
		$THIS_DISPLAY .= "<TR>\n";
		$THIS_DISPLAY .= "<th WIDTH=95%><font color=\"".$OPTIONS['DISPLAY_HEADERTXT']."\">\n";
		$THIS_DISPLAY .= "<B>$tmp : $PROD[PROD_NAME]<BR>".$instructions."\n";
		$THIS_DISPLAY .= "</th></TR><TR><TD ALIGN=LEFT VALIGN=TOP>\n";


		for ($x=0;$x<=$numLines;$x++) {			// Loop through lines and parse unwanted "display" HTML

			# Include line in out put as long as they don't contain <form, <title, <meta, <html, <body, SUBMIT in them
			if ($lines[$x] != "\n\n" && !eregi("SUBMIT", $lines[$x]) && !eregi("</FORM", $lines[$x]) && !eregi("<FORM", $lines[$x]) && !eregi("<META", $lines[$x]) && !eregi("</TITLE", $lines[$x]) && !eregi("<HTML", $lines[$x]) && !eregi("<TITLE", $lines[$x]) && !eregi("<BODY", $lines[$x]) && !eregi("<HEAD", $lines[$x]) && !eregi("</HEAD", $lines[$x]) && !eregi("</BODY", $lines[$x]) && !eregi("</HTML", $lines[$x]) && !eregi("<HTML", $lines[$x]) && !eregi("required_fields", $lines[$x]) ) {
				if(!eregi("fileupload", $lines[$x])){
					$this_line = eregi_replace("name=\"", "name=\"Item_".$y."_", $lines[$x]);	// Change name variables so that product can interpret them based on use
				} else {
					$this_line = $lines[$x];
				}
				$THIS_DISPLAY .= $this_line . "\n";

			} elseif ( eregi("required_fields", $lines[$x]) ) {
			   # Replace required_fields fieldname and add to output lines
			   $THIS_DISPLAY .= eregi_replace("name=\"required_fields\"", "id=\"required_fields".$y."\"", $lines[$x]) . "\n";
			   $reqfield_js = "<script type=\"text/javascript\">\n";
			   $reqfield_js .= "function req_fields() {\n";
			   $reqfield_js .= "   var prod_form = document.attachment_form\n";
			   $reqfield_js .= "   var err_msg = '';\n";
			   $reqfield_js .= "   for ( y = 1; y <= ".$num_times."; y++ ) {\n";
			   $reqfield_js .= "      fname = 'required_fields'+y;\n";
			   $reqfield_js .= "      rfields = document.getElementById(''+fname+'').value;\n";
//			   $reqfield_js .= "      alert('['+rfields+']');\n";
			   $reqfield_js .= "      rfld = rfields.split(';')\n";
			   $reqfield_js .= "      for ( f = 0; f < rfld.length; f++ ) {\n";
			   $reqfield_js .= "         tcode = String.fromCharCode('8211');\n"; // check for hyphen character
			   $reqfield_js .= "         if ( rfld[f] != '' && rfld[f].indexOf('.') < 0 && rfld[f].indexOf(tcode) < 0 && rfld[f] != \"fileupload[]\") {\n";
//			   $reqfield_js .= "            alert('check: ['+rfld[f]+']('+rfld[f].charCodeAt(14)+')');\n";
			   $reqfield_js .= "            rfieldname = 'Item_'+y+'_'+rfld[f];\n";
			   $reqfield_js .= "            reqfield = eval(\"document.attachment_form.\"+rfieldname+\".value\");\n";
			   $reqfield_js .= "            if ( reqfield == '' || reqfield == false ) {\n";
			   $reqfield_js .= "               err_msg += '- The '+rfld[f]+' field for item #'+y+' (Item_'+y+'_'+rfld[f]+')\\n';\n";
			   $reqfield_js .= "            }\n";
			   $reqfield_js .= "         }else if(rfld[f] == \"fileupload[]\" && prod_form[rfld[f]].value.length < 5){\n";
			   $reqfield_js .= "            err_msg += '- The file upload field \\n';\n";
			   $reqfield_js .= "         }\n";
			   $reqfield_js .= "      }\n";
			   $reqfield_js .= "   }\n";
			   $reqfield_js .= "   if ( err_msg == '' ) {\n";
			   $reqfield_js .= "      document.attachment_form.submit();\n";
			   $reqfield_js .= "   } else {\n";
			   $reqfield_js .= "      alert('".lang("You have left the following required fields blank").":\\n'+err_msg+'\\n\\n".lang("Please complete these fields and re-submit the form").".');\n";
			   $reqfield_js .= "   }\n";
			   $reqfield_js .= "}\n";
			   $reqfield_js .= "</script>\n";
		      $THIS_DISPLAY .= $reqfield_js;
			}

		}	// End Line Loop

		$THIS_DISPLAY .= "</TD></TR></TABLE><BR>\n";

	} // End Number of Times Loop

	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	// Step 4: Display Submit Button and Close out form
	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	$THIS_DISPLAY .= "<BR><CENTER><input type=\"button\" value=\"".lang("Continue")."...\" CLASS=FormLt1 STYLE='cursor: hand;' onclick=\"req_fields();\"></CENTER></FORM>\n";

	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	// Step 5: Display and wait for customer input
	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	$ACTION = "form";			// Skip all other actions for the moment

} // End Custom Form Attachment

##########################################################################
### START PROCESSING FOR ACTUAL ADD CART FUNCTION
##########################################################################

if ($ACTION != "view" && $ACTION != "update" && $ACTION != "delete" && $ACTION != "form") {

if($_POST['special_var_form'] == 'yes'){
	foreach($_POST as $va=>$vu){
		$Mypost_ar[$va]=$vu;
		if(eregi('^qty~', $va)){
			if($vu > 0){
				++$crzy_cnt;
				$Mypost_ar_items[]=array($va,$vu);
				//echo $va." ".$vu."<br/>";
			}
		} else {
			$restore_post[$va]=$vu;
		}
	}

	unset($_POST);
	$_POST = '';

	$_POST['goto_checkout'] = $Mypost_ar['goto_checkout'];
	$_POST['id'] = $Mypost_ar['id'];
	foreach($restore_post as $o1=>$o2){
		$_POST[$o1]=$o2;
	}
//	echo testArray($Mypost_ar); exit;
} else {
	$crzy_cnt = 1;
}

$postloop = 0;

while($postloop < $crzy_cnt){
	if($Mypost_ar['special_var_form'] == 'yes'){

		$va = $Mypost_ar_items[$postloop]['0'];
    $vu = $Mypost_ar_items[$postloop]['1'];
    $new_valz = explode('~', $va);
    $subcat = str_replace('_', ' ', $new_valz['1']);
    $_POST['subcat'] = $subcat;
    $tttprice = explode(';', $new_valz['2']);
    $tttprice['1'];
    $daprice = str_replace('_', ' ', $tttprice['0']).';'.str_replace('_', '.', $tttprice['1']);
    $_POST['price'] = $daprice;
    $price = $_POST['price'];
    $_POST['qty'] = $vu;
    $qty = $vu;
	}

	// -----------------------------------------------------------------------------------------------
	// Verify that this is not a hacker attempt to distort cart pricing
	// Bugzilla #11
	// -----------------------------------------------------------------------------------------------

	$newSkuTest = mysql_query("SELECT PROD_NAME, PROD_SKU, PROD_UNITPRICE,VARIANT_NAME1,VARIANT_PRICE1,VARIANT_NAME2,VARIANT_PRICE2,VARIANT_NAME3,VARIANT_PRICE3,VARIANT_NAME4,VARIANT_PRICE4,VARIANT_NAME5,VARIANT_PRICE5,VARIANT_NAME6,VARIANT_PRICE6,OPTION_INVENTORY_NUM, sub_cats, variant_names, variant_prices FROM cart_products WHERE PRIKEY = '$id'");
	$dbValue = mysql_fetch_array($newSkuTest);
	$var_name = "";

	# Re-restore price variation data for double checking
   # Restore price variation arrays
   $dbValue['sub_cats'] = unserialize($dbValue['sub_cats']);
   $dbValue['variant_names'] = unserialize($dbValue['variant_names']);
   $dbValue['variant_prices'] = unserialize($dbValue['variant_prices']);

eval(hook("pgm-add_cart.php:poparrays"));

   // Start Fixing Bugzilla #35

	$price = htmlspecialchars_decode($price);
	if (eregi(";", $price)) {			// If a variant is being used, find out the variant name and individual price
		$tmp = split(";", $price);
		$var_name = $tmp[0];
		$price = $tmp[1];
		$prtmp = "";

		for ( $v=1; $v <= $PROD['num_variants']; $v++) {
			$varNameField = "VARIANT_NAME".$v;
			$varPriceField = "VARIANT_PRICE".$v;

			if ( ($var_name == str_replace(' ', '.', htmlspecialchars_decode($dbValue['variant_names'][$v])) || $var_name == htmlspecialchars_decode($dbValue['variant_names'][$v]) || str_replace(' ', '.', $var_name) == str_replace(' ', '.', htmlspecialchars_decode($dbValue['variant_names'][$v]))) && $price == htmlspecialchars_decode($dbValue['variant_prices'][$v]) ) {
				$prtmp = $price;
			}
		}
      $price = $prtmp;

      if ($price == "") { // Hacker Attempt Detected
         echo "<CENTER><H1><FONT COLOR=RED>".lang("Illegal product addition detected.")."</FONT></H1>";
         exit;
      }

	} elseif ($price != $dbValue['PROD_UNITPRICE']) {	  // Hacker Attempt Detected
	   $price = $dbValue['PROD_UNITPRICE'];
//		echo "<CENTER><H1><FONT COLOR=RED>".lang("ILLEGAL PRODUCT ADDITION DETECTED.")."(".$id.") = [".$price." == ".$dbValue['PROD_UNITPRICE']."]</FONT></H1>";
//		exit;
	}

   //END FIX - Bugzilla #35

	// -----------------------------------------------------------------------------------------------


	$prod_name = $dbValue['PROD_NAME'];	// Get the actual product name text

	$qty = alpha_only($qty);			// Bugzilla #25
	if ($qty < 1) { $qty = 1; }			// Bugzilla #28
	if ($qty > 10000) { $qty = 10000; } // Bugzilla #23

	if($dbValue['OPTION_INVENTORY_NUM'] < $qty){
		$inv_num = $dbValue['OPTION_INVENTORY_NUM'];
		header("Location: pgm-more_information.php?id=".$id."&inv_error=".$inv_num."&=SID");
		exit;
	}

	$prod_name = eregi_replace("&quot;", "\"", $prod_name);

	$sku_num = $PROD['PROD_SKU'];			// Get this products sku number
	$cat_num = $PROD['PROD_CATNO'];		// If there is a catalog number, get that for invoice reference

eval(hook("pgm-add_cart.php:calcaddnprice"));

	$sub_total = $qty*$price;		// Go ahead and calcualte sub-total for this line item (makes checkout easier)

	// --------------------------------------------------
	// DISPLAY VARIABLES FOR TESTING
	// --------------------------------------------------

/*
	 echo "<DIV ALIGN=LEFT CLASS=text>\n";
	 echo "<BR>$PROD[PROD_NAME]<BR><BR>";
	 echo "(ID$id) | $sku_num ($cat_num) | $prod_name | $subcat | $var_name | $price | $qty | $sub_total <BR><BR>\n";
	 echo "<DIV>\n";
	 exit;
*/

	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	// Add to cart as "new" item if product does not already exist in cart
	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

		$tmp = split(";", $_SESSION['CART_KEYID']);
		$tmp_cnt = count($tmp);
		$tmp_subcat = split(";", $_SESSION['CART_SUBCAT']);
		$tmp_varname = split(";", $_SESSION['CART_VARNAME']);

		$DUP = 0;

		$tmp_cnt = $tmp_cnt - 2;

		for ($z=0;$z<=$tmp_cnt;$z++) {

			if ($tmp[$z] == $id && $tmp_subcat[$z] == $subcat && $tmp_varname[$z] == $var_name) {
				$DUP = 1;
			}

		}

	if ($DUP != 1) {		// Add as new Product to cart

		// Check for custom form data

		$additional_data = "";
		$form_data = "";

		if ($FORMFLAG == "ON") {

			for ( $y = 1; $y <= $FORMLOOP; $y++ ) {

				reset($_POST);
				while (list($name, $value) = each($_POST)) {

				$tmp_lookfor = "Item_".$y."_";

					if (eregi("$tmp_lookfor", $name)) {
						$tmp_dat = "";
						$tmp_dat = eregi_replace("$tmp_lookfor", "", $name);
						$tmp_dat = ucwords($tmp_dat);
						$tmp_val = ucwords($value);
						$tmp_val = stripslashes($value);
						if ( is_array($value) ) { $tmp_val = implode(",&nbsp;", $value); }
						$tmp_dat = "-> (Item $y) ".$tmp_dat.": $tmp_val:br:";
						$additional_data .= $tmp_dat;
					}

				} // End While Loop

			} // End For $y Loop


			$form_data .= $additional_data;
//			echo $additional_data;

		} // End Form Flag

		$_SESSION['CART_KEYID'] .= $id . ";";
		$_SESSION['CART_SKUNO'] .= $sku_num . ";";
		$_SESSION['CART_CATNO'] .= $cat_num . ";";
		$_SESSION['CART_PRODNAME'] .= $prod_name . ";";
		$_SESSION['CART_SUBCAT'] .= $subcat . ";";
		$_SESSION['CART_FORMDATA'] .= $form_data . ":p:";
		$_SESSION['CART_VARNAME'] .= $var_name . ";";
		$_SESSION['CART_UNITPRICE'] .= $price . ";";
		$_SESSION['CART_QTY'] .= $qty . ";";
		$_SESSION['CART_UNITSUBTOTAL'] .= $sub_total . ";";

eval(hook("pgm-add_cart.php:addtosession"));

	} else {

	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	// If product already exists, update qty with + new qty
	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

		$tmp = split(";", $_SESSION['CART_KEYID']);
		$tmp_cnt = count($tmp);
		$tmp_qty = split(";", $_SESSION['CART_QTY']);
		$tmp_subcat = split(";", $_SESSION['CART_SUBCAT']);
		$tmp_formdata = split(":p:", $_SESSION['CART_FORMDATA']);
		$tmp_varname = split(";", $_SESSION['CART_VARNAME']);
		$tmp_price = split(";", $_SESSION['CART_UNITPRICE']);

eval(hook("pgm-add_cart.php:getattap"));

		$UPDATE_QTY = "";
		$UPDATE_SUBTOTAL = "";

		$tmp_cnt = $tmp_cnt - 2;

		for ($z=0;$z<=$tmp_cnt;$z++) {

			if ($tmp[$z] == $id && $tmp_subcat[$z] == $subcat && $tmp_varname[$z] == $var_name) {

				# This is the product being added to (the one this passed $qty should be added to)
				$new_subtotal = $tmp_subtotal + $sub_total;
				$new_qty = $tmp_qty[$z] + $qty;
				if($PROD['OPTION_INVENTORY_NUM'] < $new_qty){
					$err_inv_update = 1;
					if($PROD['OPTION_INVENTORY_NUM'] >= $tmp_qty[$z]){
						$new_qty = $tmp_qty[$z];
					}else{
						$new_qty = $PROD['OPTION_INVENTORY_NUM'];
					}
				}
				$UPDATE_QTY .= $new_qty . ";";
				$UPDATE_SUBTOTAL .= $tmp_price[$z] * $new_qty.";";

			} else {
			   # This is a different product in the basket, unaffected by this addition
				$UPDATE_QTY .= $tmp_qty[$z] . ";";
				$UPDATE_SUBTOTAL .= $tmp_price[$z] * $tmp_qty[$z].";";
			}

		}

		if($err_inv_update == 1){
   		$inv_num = $PROD['OPTION_INVENTORY_NUM'];
   		header("Location: pgm-more_information.php?id=".$id."&inv_error=".$inv_num."&=SID");
   		exit;
		}

		$_SESSION['CART_UNITSUBTOTAL'] = $UPDATE_SUBTOTAL;
		$_SESSION['CART_QTY'] = $UPDATE_QTY;

	} // End Else Clause
			///////////////////////////////////
			/////////////check if form has attached file
			if(count($_FILES) >= 1){
//				echo $form_data;
//				echo testArray($tmp_formdata);
//				echo testArray($_POST);
				$filesuploaded = '';
				$fcount = count($_FILES['fileupload']['tmp_name']);
				$f2count = 1;
				foreach($_FILES['fileupload']['tmp_name'] as $filnum=>$fildat) {
					$filesuploaded .= $_FILES['fileupload']['name'][$filnum];
					if($fcount > 1){
						$filesuploaded .= ", ";
					}
					$_POST['Item_'.$f2count.'_fileuploaded'] = $_FILES['fileupload']['name'][$filnum];
					++$f2count;
					--$fcount;
				}
				$_POST['files_uploaded'] = $filesuploaded;
			}

			if(count($_FILES) >= 1){

				$result = mysql_query("SELECT * FROM cart_options");
				$BIZ = mysql_fetch_array($result);

				$email_notice_flag = "off";

				if ($BIZ['BIZ_EMAIL_NOTICE'] != "") {
					$from_name = $BIZ['BIZ_PAYABLE'];
					// Parse Items that cause wierd email display issues for customer
					$from_name = eregi_replace("\.", " ", $from_name);
					$from_name = eregi_replace(",", "", $from_name);
					$from_email = $BIZ['BIZ_EMAIL_NOTICE'];
					//	echo testArray($_POST); exit;
					include_once('../sohoadmin/program/includes/class-send_file.php');

				   // NEED TO ADD LOOP FOR MULTIPLE ADMIN EMAIL
				   // LIKE FOREACH MAIL SEND BELOW

					$getnamequery = mysql_query("select PROD_NAME from cart_products where PRIKEY='".$_POST['id']."'");
					$getnameq = mysql_fetch_array($getnamequery);
						$prodname = $getnameq['PROD_NAME'];

					$SUBJECTLINE = "Website Shopping Cart ".$prodname." Submission";

					$soho_email = lang("The following have been submitted for the ").$prodname." ".lang("shopping cart product.")."\n".lang("This form IS submitted even if the customer did not complete the checkout process and submit a payment").". \n\r";
					$soho_email .= "\n\n";
					$soho_email .= lang("Product Information")." \n";
					$soho_email .= lang("Product").": ".$prodname." \n";
					$soho_email .= lang("qty").": ".$_POST['qty']." \n";
					$formstuff = '';
					foreach($_POST as $pvar=>$pval){
						if(eregi('Item_', $pvar)){
							if(is_array($pval)){
								$pval = $pval['0'];
							}
							$formstuff .= $pvar.": ".$pval."\n";
						}
					}

					$soho_email .= "\n\n";
					$soho_email .= lang("Form Information").": \n";
					$soho_email .= $formstuff."\n";
					if(($f2count - 1) > 1){
						$soho_email .= lang("Uploaded files").":".$filesuploaded;
					} else {
						$soho_email .= lang("Uploaded file").":".$filesuploaded;
					}
					$test = new attach_mailer($name = "$from_email", $from = "$from_email", $to = "$from_email", $cc = "", $bcc = "", $subject = "".$SUBJECTLINE."");

					foreach($_FILES['fileupload']['tmp_name'] as $filnum=>$fildat) {
						if (move_uploaded_file($_FILES['fileupload']['tmp_name'][$filnum], $_SESSION['doc_root'].'/sohoadmin/filebin/'.$_FILES['fileupload']['name'][$filnum])) {
							if(file_exists($_SESSION['doc_root'].'/sohoadmin/filebin/'.$_FILES['fileupload']['name'][$filnum])) {
								//file uploaded

								//if(eregi('\.gif$', $_FILES['fileupload']['name'][$filnum]) || eregi('\.jpg$', $_FILES['fileupload']['name'][$filnum]) || eregi('\.jpeg$', $_FILES['fileupload']['name'][$filnum]) || eregi('\.png$', $_FILES['fileupload']['name'][$filnum]) || eregi('\.bmp$', $_FILES['fileupload']['name'][$filnum])) {
								//	$test->add_html_image($_SESSION['doc_root'].'/sohoadmin/filebin/'.$_FILES['fileupload']['name'][$filnum]);
								//} else {
									if(eregi('\.gz$', $_FILES['fileupload']['name'][$filnum]) || eregi('\.zip$', $_FILES['fileupload']['name'][$filnum])){
										$test->add_attach_file($_SESSION['doc_root'].'/sohoadmin/filebin/'.$_FILES['fileupload']['name'][$filnum]);
									} else {
										$SLASH = DIRECTORY_SEPARATOR;
										$zipped_file = eregi_replace('\.[^\.]*$', '.zip', $_FILES['fileupload']['name'][$filnum]);
										$b4zip = getcwd();
										chdir($_SESSION['doc_root'].$SLASH."sohoadmin".$SLASH."filebin");
										if(eregi('WIN', PHP_OS)){
											$zippy = $_SESSION['doc_root'].$SLASH."sohoadmin".$SLASH."program".$SLASH."includes".$SLASH."untar".$SLASH."zip.exe";
											exec($zippy." ".$zipped_file." ".$_FILES['fileupload']['name'][$filnum]);
										} else {
											exec("zip ".$zipped_file." ".$_FILES['fileupload']['name'][$filnum]);
										}
										chdir($b4zip);
										if(file_exists($_SESSION['doc_root'].$SLASH."sohoadmin".$SLASH."filebin".$SLASH.$zipped_file)){
											$test->add_attach_file($_SESSION['doc_root'].$SLASH."sohoadmin".$SLASH."filebin".$SLASH.$zipped_file);
											unlink($_SESSION['doc_root'].$SLASH."sohoadmin".$SLASH."filebin".$SLASH.$zipped_file);
										} else {
											$test->add_attach_file($_SESSION['doc_root'].'/sohoadmin/filebin/'.$_FILES['fileupload']['name'][$filnum]);
										}
								//	}
								unlink($_SESSION['doc_root'].$SLASH."sohoadmin".$SLASH."filebin".$SLASH.$_FILES['fileupload']['name'][$filnum]);
								}
							}
						}
					}

					$test->html_body = "<html><pre>$soho_email</pre></html>";
					$test->text_body = strip_tags($test->html_body, "<a>");
					if($test->process_mail() == true) {
						foreach($_FILES['fileupload']['tmp_name'] as $filnum=>$fildat) {
							if(file_exists($_SESSION['doc_root'].'/sohoadmin/filebin/'.$_FILES['fileupload']['name'][$filnum])) {
								unlink($_SESSION['doc_root'].'/sohoadmin/filebin/'.$_FILES['fileupload']['name'][$filnum]);
							}
						}
					} else {
			//couldnotsend
					}
				}
			}
			//////////////// end form	attached file
++$postloop;
}
	// -------------------------------------------------------------------------------
	// OK, Product has been added to the session cart.  Now, lets view the current
	// shopping cart contents and show any recommended items
	// -------------------------------------------------------------------------------

	$ACTION = "view";

	# Go to Cart Contents page or directly to checkout?
	if ( $_REQUEST['goto_checkout'] != "yes" ) {
	   header("Location: pgm-add_cart.php?ACTION=view&=SID"); exit;
	}

} // END ADD TO CART FUNCTION

##########################################################################
### START PROCESSING DELETE FUNCTION
##########################################################################

if ($ACTION == "delete") {

		$delvar = str_replace("+", " ", $delvar);
		$delvar = str_replace(":amp:", "&", $delvar);
		$delsub = str_replace("+", " ", $delsub);
		$delsub = str_replace(":amp:", "&", $delsub);

		$updated_text = "<font color=red> [UPDATED]</font>";

		$delsub = str_replace("\%20", " ", $delsub);
		$delvar = str_replace("\%20", " ", $delvar);
		$delvar = str_replace("- ", "", $delvar);		// Remove Visual Formating for correct var matching

eval(hook("pgm-add_cart.php:replacegetparam"));

		$tmp_id = split(";", $_SESSION['CART_KEYID']);
		$tmp_cnt = count($tmp_id);

		$tmp_sku = split(";", $_SESSION['CART_SKUNO']);
		$tmp_cat = split(";", $_SESSION['CART_CATNO']);
		$tmp_name = split(";", $_SESSION['CART_PRODNAME']);
		$tmp_subcat = split(";", $_SESSION['CART_SUBCAT']);
		$tmp_formdata = split(":p:", $_SESSION['CART_FORMDATA']);
		$tmp_varname = split(";", $_SESSION['CART_VARNAME']);
		$tmp_price = split(";", $_SESSION['CART_UNITPRICE']);
		$tmp_qty = split(";", $_SESSION['CART_QTY']);
		$tmp_subtotal = split(";", $_SESSION['CART_UNITSUBTOTAL']);

eval(hook("pgm-add_cart.php:fetchtmparray"));

		$_SESSION['CART_KEYID'] = "";
		$_SESSION['CART_SKUNO'] = "";
		$_SESSION['CART_CATNO'] = "";
		$_SESSION['CART_PRODNAME'] = "";
		$_SESSION['CART_SUBCAT'] = "";
		$_SESSION['CART_FORMDATA'] = "";
		$_SESSION['CART_VARNAME'] = "";
		$_SESSION['CART_UNITPRICE'] = "";
		$_SESSION['CART_QTY'] = "";
		$_SESSION['CART_UNITSUBTOTAL'] = "";

eval(hook("pgm-add_cart.php:clearsessionattrib"));

		$tmp_cnt = $tmp_cnt - 2;

		for ($z=0;$z<=$tmp_cnt;$z++) {

			if ($tmp_id[$z] == $delkey && $tmp_subcat[$z] == $delsub && $tmp_varname[$z] == $delvar) {

				// Do not add this back to current cart; we are deleteing

			} else {

				$_SESSION['CART_KEYID'] .= $tmp_id[$z] . ";";
				$_SESSION['CART_SKUNO'] .= $tmp_sku[$z] . ";";
				$_SESSION['CART_CATNO'] .= $tmp_cat[$z] . ";";
				$_SESSION['CART_PRODNAME'] .= $tmp_name[$z] . ";";
				$_SESSION['CART_SUBCAT'] .= $tmp_subcat[$z] . ";";
				$_SESSION['CART_FORMDATA'] .= $tmp_formdata[$z] . ":p:";
				$_SESSION['CART_VARNAME'] .= $tmp_varname[$z] . ";";
				$_SESSION['CART_UNITPRICE'] .= $tmp_price[$z] . ";";
				$_SESSION['CART_QTY'] .= $tmp_qty[$z] . ";";
				$_SESSION['CART_UNITSUBTOTAL'] .= $tmp_subtotal[$z] . ";";

eval(hook("pgm-add_cart.php:addfrmtmp"));

			}

		}

//	echo "Error from ".basename(__FILE__)." : ".__LINE__."<br>";
//	echo "ACTION = [".$ACTION."]<br>";
//	echo "KEYID's = [".$_SESSION['CART_KEYID']."]<br>";

	$ACTION = "view";

}  // End Delete Function


##########################################################################
### START PROCESSING UPDATE FUNCTION
##########################################################################

if ($ACTION == "update") {

		$updated_text = "<font color=red> [".lang("UPDATED")."]</font>";

		$tmp_id = split(";", $_SESSION['CART_KEYID']);
		$tmp_cnt = count($tmp_id);					// Find the number of current line items

		$tmp_sku = split(";", $_SESSION['CART_SKUNO']);			// Place all current line items into arrays
		$tmp_cat = split(";", $_SESSION['CART_CATNO']);
		$tmp_name = split(";", $_SESSION['CART_PRODNAME']);
		$tmp_subcat = split(";", $_SESSION['CART_SUBCAT']);
		$tmp_formdata = split(":p:", $_SESSION['CART_FORMDATA']);
		$tmp_varname = split(";", $_SESSION['CART_VARNAME']);
		$tmp_price = split(";", $_SESSION['CART_UNITPRICE']);
		$tmp_qty = split(";", $_SESSION['CART_QTY']);
		$tmp_subtotal = split(";", $_SESSION['CART_UNITSUBTOTAL']);

eval(hook("pgm-add_cart.php:fetchtmparray"));

		$_SESSION['CART_KEYID'] = "";						// Empty current cart from memory
		$_SESSION['CART_SKUNO'] = "";
		$_SESSION['CART_CATNO'] = "";
		$_SESSION['CART_PRODNAME'] = "";
		$_SESSION['CART_SUBCAT'] = "";
		$_SESSION['CART_FORMDATA'] = "";
		$_SESSION['CART_VARNAME'] = "";
		$_SESSION['CART_UNITPRICE'] = "";
		$_SESSION['CART_QTY'] = "";
		$_SESSION['CART_UNITSUBTOTAL'] = "";

eval(hook("pgm-add_cart.php:clearsessionattrib"));

		$tmp_cnt = $tmp_cnt - 2;				// Remove blank items created by "the way" we do it
//foreach($_POST as $var=>$val){
//   echo "var = (".$var.") val = (".$val.")<br>";
//   //$doms[$var] = "<option name=\"".$val."\" value=\"".$val."\">".$val."</option>\n";
//}
		for ($z=0;$z<=$tmp_cnt;$z++) {			// Loop through line items and add back each with new qty

				$tmpvar = "QTYUPDATE" . $z;
				$newQTY = ${$tmpvar};			// Create variable for update and get value


				// Check to see if enough inv
				// New qty
				//echo "(".$newQTY.")<br>";
				// Old qty
				//echo "(".$tmp_qty[$z].")<br>";

				$result2 = mysql_query("SELECT * FROM cart_products WHERE PRIKEY = '$tmp_id[$z]'");
				$PROD2 = mysql_fetch_array($result2);

				$err_inv_update = 0;

				# Not enough left in inventory?
				if($PROD2['OPTION_INVENTORY_NUM'] < $newQTY){
					$err_inv_update = 1;
					if($PROD2['OPTION_INVENTORY_NUM'] >= $tmp_qty[$z]){
						$newQTY = $tmp_qty[$z];
					}else{
						$newQTY = $PROD2['OPTION_INVENTORY_NUM'];
					}
				}

				// *******************************************************************
				// *** DEVNOTE: THIS IS A SECURITY PRECAUTION.  ONLY UPDATE THE QTY
				// *** IF THE NEW QTY SELECTION IS GREATER THAN ZERO FROM THE END USER.
				// *** OTHERWISE, JUST DELETE THE ITEM.  IF NOT, THIS WOULD ALLOW
				// *** DISHONEST PEOPLE TO ENTER A NEGATIVE QTY AND ACTUALLY CAUSE
				// *** A CHARGEBACK (CREDIT) VIA AN ONLINE PROCESSING SYSTEM!
				// *******************************************************************

				if ($newQTY > 0) {

					$newQTY = alpha_only($newQTY);				// Bugzilla #25
					if ($newQTY > 10000) { $newQTY = 10000; }	// Bugzilla #23

					$_SESSION['CART_KEYID'] .= $tmp_id[$z] . ";";
					$_SESSION['CART_SKUNO'] .= $tmp_sku[$z] . ";";
					$_SESSION['CART_CATNO'] .= $tmp_cat[$z] . ";";
					$_SESSION['CART_PRODNAME'] .= $tmp_name[$z] . ";";
					$_SESSION['CART_SUBCAT'] .= $tmp_subcat[$z] . ";";
               $_SESSION['CART_FORMDATA'] .= $tmp_formdata[$z] . ":p:";
					$_SESSION['CART_VARNAME'] .= $tmp_varname[$z] . ";";

					$_SESSION['CART_UNITPRICE'] .= $tmp_price[$z] . ";";

					$_SESSION['CART_QTY'] .= $newQTY . ";";				// Insert new qty -- remember entire line item is form posted here
					$tsub_total = $newQTY*$tmp_price[$z];	// Calcualte new sub-total for this line item
					$_SESSION['CART_UNITSUBTOTAL'] .= $tsub_total . ";";

eval(hook("pgm-add_cart.php:addfrmtmp"));

				} // End Zero Check

		}

	$ACTION = "view";

}  // End Update Function


##########################################################################
### START PROCESSING VIEW/EDIT FUNCTION
##########################################################################

if ($ACTION == "view") {
//   echo testArray($tmp_formdata);

//	echo "Error from ".basename(__FILE__)." : ".__LINE__."<br>";
//	echo "ACTION = [".$ACTION."]<br>";
//	echo "KEYID's = [".$_SESSION['CART_KEYID']."]<br>";
//	error_reporting(E_ALL);
//echo "(".$err_inv_update.")<br>";

	$THIS_DISPLAY .= "\n\n<SCRIPT LANGUAGE=Javascript>\n\n";

   // 2004-08-02: Prevent bomb and/or empty item results when "continue shopping" is clicked
   if ( $_SESSION['cont_shopping_string'] == "" ) {
      $continue_str = "?browse=1";
   } else {
      $continue_str = $_SESSION['cont_shopping_string'];
   }

   if($err_inv_update == 1){
   	$THIS_DISPLAY .= "	alert('".lang("Could not update all products becuase of current inventory").".');\n";
   }

	$THIS_DISPLAY .= "     function continue_shopping() {\n";
	$THIS_DISPLAY .= "          window.location = \"start.php".$continue_str."\";\n";
	$THIS_DISPLAY .= "     }\n\n";

	$THIS_DISPLAY .= "     function delete_cart(tid,tsub,tvar) {\n";
	$THIS_DISPLAY .= "          strLink = \"pgm-add_cart.php?ACTION=delete&delkey=\"+tid+\"&delsub=\"+tsub+\"&delvar=\"+tvar+\"&=SID\";\n";
	$THIS_DISPLAY .= "          window.location = strLink;\n";
	$THIS_DISPLAY .= "     }\n\n";

	$THIS_DISPLAY .= "</script>\n\n";

	$THIS_DISPLAY .= "<form name=\"UPDATE\" method=\"post\" action=\"pgm-add_cart.php\">\n";
	$THIS_DISPLAY .= " <input type=\"hidden\" name=\"ACTION\" value=\"update\">\n";

	$THIS_DISPLAY .= " <table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\">\n";
	$THIS_DISPLAY .= "  <tr>\n";
	$THIS_DISPLAY .= "   <td class=\"text\"><font SIZE=\"4\" face=verdana><b>".lang("Current Shopping Cart Contents")."</b> $updated_text</font><br/>&nbsp;<br/><div align=\"center\">\n";

	// ----------------------------------------------------------
	// SHOW LINKS TO PRIVACY AND OTHER POLICIES HERE
	// ----------------------------------------------------------

	if (!eregi("Y", $OPTIONS[PAYMENT_CATALOG_ONLY])) {	// Only display shipping/returns if using cc processing

		if ( $cartpref->get("disable_shipping") != "yes" ) {
			$THIS_DISPLAY .= "<a href=\"start.php?policy=shipping&=SID\">".lang("Shipping Information")."</a>";
			$THIS_DISPLAY .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n";
		}
		
		if ( $return_policy_definedBool ) {
			$THIS_DISPLAY .= "<a href=\"start.php?policy=returns&=SID\">".lang("Returns & Exchanges")."</a>";
			$THIS_DISPLAY .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n";
		}

	}

	$THIS_DISPLAY .= "<a href=\"start.php?policy=privacy&=SID\">".lang("Privacy Policy")."</a>";
		
	if ( $other_policy_definedBool ) {
		$THIS_DISPLAY .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n";
		$THIS_DISPLAY .= "<a href=\"start.php?policy=other&=SID\">".lang("Other Policies")."</a>";
	}

	// ----------------------------------------------------------

	$THIS_DISPLAY .= "    <br/>&nbsp;</div>\n";
	$THIS_DISPLAY .= "   </td>\n";
	$THIS_DISPLAY .= "  </tr>\n";
	$THIS_DISPLAY .= "  <tr>\n";
	$THIS_DISPLAY .= "   <td align=\"left\" valign=\"top\">\n";

   $tmp_keyid = split(";", $_SESSION['CART_KEYID']);
   $tmp_qty = split(";", $_SESSION['CART_QTY']);
   $tmp_skuno = split(";", $_SESSION['CART_SKUNO']);
   $tmp_catno = split(";", $_SESSION['CART_CATNO']);

   $tmp_name = split(";", $_SESSION['CART_PRODNAME']);

   $tmp_subcat = split(";", $_SESSION['CART_SUBCAT']);
   $tmp_formdata = split(":p:", $_SESSION['CART_FORMDATA']);
   $tmp_varname = split(";", $_SESSION['CART_VARNAME']);

   $tmp_price = split(";", $_SESSION['CART_UNITPRICE']);
   $tmp_sub = split(";", $_SESSION['CART_UNITSUBTOTAL']);

eval(hook("pgm-add_cart.php:fetchtmparray"));

   $display_subtotal = 0;        // Reset Display sub_total; we'll calculate that on the fly
   $line_items = count($tmp_qty);   // Count the number of array vars we have after split
   $line_items = $line_items - 2;   // Subtract 2 because (a)we start count at 0 (b) we always have a trailing semi-colon;

   if ($sep == "#EFEFEF") { $sep = "white"; } else { $sep = "#EFEFEF"; }

   eval(hook("pgm-add_cart.php:above_current_cart_contents"));

   $THIS_DISPLAY .= "    <table border=\"0\" cellpadding=\"5\" cellspacing=\"0\" align=\"center\" width=\"100%\" id=\"addcart-current_cart_contents\">\n";

   $THIS_DISPLAY .= " <tr>\n";
   $THIS_DISPLAY .= "  <th width=\"40%\">\n";
   $THIS_DISPLAY .= "   ".lang("Product Name")."\n";
   $THIS_DISPLAY .= "  </th>\n";
   $THIS_DISPLAY .= "  <th width=\"100\">\n";
   $THIS_DISPLAY .= "   ".lang("Unit Price")."\n";
   $THIS_DISPLAY .= "  </th>\n";
   $THIS_DISPLAY .= "  <th width=\"100\">\n";
   $THIS_DISPLAY .= "   ".lang("Quantity")."\n";
   $THIS_DISPLAY .= "  </th>\n";
   $THIS_DISPLAY .= "  <th width=\"100\">\n";
   $THIS_DISPLAY .= "   ".lang("Sub-Total")."\n";
   $THIS_DISPLAY .= "  </th>\n";
   $THIS_DISPLAY .= "  <th>\n";
   $THIS_DISPLAY .= "   &nbsp;\n";
   $THIS_DISPLAY .= "  </th>\n";
   $THIS_DISPLAY .= " </tr>\n";

   if ($line_items != -1) {

      for ($z=0;$z<=$line_items;$z++) {

         $display_subtotal = $display_subtotal + $tmp_sub[$z]; // Add Unit Sub Total to Total Sub Total ??? Confused yet?

         if ($sep == "#EFEFEF") { $sep = "white"; } else { $sep = "#EFEFEF"; }

         if (strlen($tmp_subcat[$z]) > 2) { $tmp_varname[$z] = "- " . $tmp_varname[$z]; } // Format for proper display

         $THIS_DISPLAY .= "<TR>\n";
         $THIS_DISPLAY .= "<TD ALIGN=LEFT VALIGN=TOP CLASS=text>\n";
         $THIS_DISPLAY .= "<A HREF=\"pgm-more_information.php?id=$tmp_keyid[$z]&=SID\">$tmp_name[$z]</A><BR><DIV CLASS=smtext>$tmp_subcat[$z] $tmp_varname[$z]\n";
eval(hook("pgm-add_cart.php:disp"));
         $THIS_DISPLAY .= "<font style=\"font-family:arial,helvetica,sans-serif; font-size:11px; color:darkgreen; font-weight:bold;\">";

         //write $formdata in itemized list
         $form_line = split(":br:", $tmp_formdata[$z]);
         $num_fl = count($form_line);
         for ($f=0;$f<=$num_fl;$f++) {
            $THIS_DISPLAY .= $form_line[$f] . "<br>\n";
         }
         $THIS_DISPLAY .= "</font>";
         $THIS_DISPLAY .= "</TD>\n";
         $THIS_DISPLAY .= "<TD ALIGN=LEFT VALIGN=TOP CLASS=text>\n";
         $THIS_DISPLAY .= " ".$dSign . $tmp_price[$z]."\n";
         $THIS_DISPLAY .= "</TD>\n";
         $THIS_DISPLAY .= "<TD ALIGN=CENTER VALIGN=TOP CLASS=text>\n";
         $THIS_DISPLAY .= "<INPUT TYPE=TEXT NAME=\"QTYUPDATE$z\" VALUE=\"$tmp_qty[$z]\" class=\"textfield\" SIZE=2>\n";
         $THIS_DISPLAY .= "</TD>\n";
         $THIS_DISPLAY .= "<TD ALIGN=LEFT VALIGN=TOP CLASS=text>\n";

         $tmp_sub[$z] = sprintf ("%01.2f", $tmp_sub[$z]);

         $THIS_DISPLAY .= "$dSign".$tmp_sub[$z]."\n";
         $THIS_DISPLAY .= "</TD>\n";
         $THIS_DISPLAY .= "<TD ALIGN=CENTER VALIGN=TOP CLASS=text>\n";
         $jscript_passvar = eregi_replace(" ", "+", $tmp_varname[$z]);
         $jscript_passvar = str_replace("&amp;", ":amp:", $jscript_passvar);
         $jscript_passvar = str_replace("&", ":amp:", $jscript_passvar);

         $jscript_passsub = eregi_replace(" ", "+", $tmp_subcat[$z]);
         $jscript_passsub = str_replace("&amp;", ":amp:", $jscript_passsub);
         $jscript_passsub = str_replace("&", ":amp:", $jscript_passsub);

eval(hook("pgm-add_cart.php:jsvars"));

         $THIS_DISPLAY .= "<A HREF=\"#\" onclick=\"delete_cart('".$tmp_keyid[$z]."','".$jscript_passsub."','".$jscript_passvar."');\"><IMG SRC=\"delete_button.gif\" width=44 height=14 align=absmiddle border=0></A><BR>&nbsp;<BR>&nbsp;\n";
         $THIS_DISPLAY .= "</TD>\n";
         $THIS_DISPLAY .= "</TR>\n";

      }  // End of $z Loop

      if ($sep == "#EFEFEF") { $sep = "white"; } else { $sep = "#EFEFEF"; }

      $THIS_DISPLAY .= "<TR>\n";
      $THIS_DISPLAY .= "<TD ALIGN=LEFT VALIGN=MIDDLE CLASS=text>\n";
      $THIS_DISPLAY .= "<FONT SIZE=1 FACE=ARIAL><B><I>".lang("Sub-total does not include tax")."<br>".lang("and shipping charges, if applicable.")."</I></B></FONT>\n";
      $THIS_DISPLAY .= "</TD>\n";

      $THIS_DISPLAY .= "<TD ALIGN=RIGHT VALIGN=MIDDLE CLASS=text COLSPAN=2>\n";
      $THIS_DISPLAY .= "<B>".lang("Sub-Total")."</B>:\n";
      $THIS_DISPLAY .= "</TD>\n";

      $THIS_DISPLAY .= "<TD ALIGN=LEFT VALIGN=MIDDLE CLASS=text>\n";

      $display_subtotal = sprintf ("%01.2f", $display_subtotal);

      $THIS_DISPLAY .= "<U>".$dSign."$display_subtotal</U>\n";
      $THIS_DISPLAY .= "</TD>\n";
      $THIS_DISPLAY .= "<TD ALIGN=CENTER VALIGN=MIDDLE CLASS=text>\n";
      $THIS_DISPLAY .= "<INPUT TYPE=SUBMIT VALUE=\"".lang("Update Qty Changes")."\" STYLE='font-size: 9px;width: 110px; cursor: hand;'>\n";
      $THIS_DISPLAY .= "</TD>\n";
      $THIS_DISPLAY .= "</TR>\n";

      $THIS_CART_IS_EMPTY = "NO";

   } else { // If no line items no

      $THIS_DISPLAY .= "<TR><TD ALIGN=CENTER VALIGN=MIDDLE COLSPAN=5><BR><B>".lang("Your shopping cart is currently empty.")."<BR>&nbsp;</B></TD></TR>\n";
      $THIS_CART_IS_EMPTY = "YES";

   }

   $THIS_DISPLAY .= "</TABLE>\n\n";

   # Got to https url for checkout?
   if (strlen($OPTIONS[PAYMENT_SSL]) > 4) {
      $OPTIONS['PAYMENT_SSL'] = chop($OPTIONS['PAYMENT_SSL']);
      $OPTIONS[PAYMENT_SSL] = ltrim($OPTIONS[PAYMENT_SSL]);
      $OPTIONS[PAYMENT_SSL] = rtrim($OPTIONS[PAYMENT_SSL]);
      $SSL_VARIABLE = $OPTIONS[PAYMENT_SSL] . "/shopping/";
      $SSL_CHECKOUT_LINK = $SSL_VARIABLE."pgm-checkout.php?sid=".session_id();

   } else {
      $SSL_VARIABLE = "";
      $SSL_CHECKOUT_LINK = "pgm-checkout.php";
   }

   $THIS_DISPLAY .= "</FORM>\n\n";

   $THIS_DISPLAY .= "<table border=\"0\" cellpadding=\"5\" cellspacing=\"0\" align=\"center\">\n";
   $THIS_DISPLAY .= " <tr>\n";
   $THIS_DISPLAY .= "  <td align=\"center\" valign=\"top\">\n";
   $THIS_DISPLAY .= "   <a href=\"#\" onclick=\"continue_shopping();\"><img src=\"continue_button.gif\" width=\"161\" height=\"25\" border=\"0\"></a>\n\n";
   $THIS_DISPLAY .= "  </td>\n";


   // ----------------------------------------------------------------
   // Only display checkout button if cart contains items to purchase
   // ----------------------------------------------------------------

   if ($THIS_CART_IS_EMPTY != "YES") {

      $checkout_button = "<form name=\"checkout_button\" method=\"post\" action=\"".$SSL_CHECKOUT_LINK."\">\n";

      $pass_prod = eregi_replace("\"", "#Q#", $_SESSION['CART_PRODNAME']); // Make sure quotes get passed to SSL
      $pass_varname = eregi_replace("&amp;", ":amp:", $_SESSION['CART_VARNAME']); // Make sure quotes get passed to SSL

      $checkout_button .= "<input type=\"hidden\" name=\"CART_KEYID\" value=\"".$_SESSION['CART_KEYID']."\">\n";
      $checkout_button .= "<input type=\"hidden\" name=\"CART_SKUNO\" value=\"".$_SESSION['CART_SKUNO']."\">\n";
      $checkout_button .= "<input type=\"hidden\" name=\"CART_CATNO\" value=\"".$_SESSION['CART_CATNO']."\">\n";
      $checkout_button .= "<input type=\"hidden\" name=\"CART_PRODNAME\" value=\"".$pass_prod."\">\n";
      $checkout_button .= "<input type=\"hidden\" name=\"CART_SUBCAT\" value=\"".$_SESSION['CART_SUBCAT']."\">\n";
      $checkout_button .= "<input type=\"hidden\" name=\"CART_FORMDATA\" value=\"".$_SESSION['CART_FORMDATA']."\">\n";
      $checkout_button .= "<input type=\"hidden\" name=\"CART_VARNAME\" value=\"".$_SESSION['CART_VARNAME']."\">\n";
      $checkout_button .= "<input type=\"hidden\" name=\"CART_UNITPRICE\" value=\"".$_SESSION['CART_UNITPRICE']."\">\n";
      $checkout_button .= "<input type=\"hidden\" name=\"CART_QTY\" value=\"".$_SESSION['CART_QTY']."\">\n";
      $checkout_button .= "<input type=\"hidden\" name=\"CART_UNITSUBTOTAL\" value=\"".$_SESSION['CART_UNITSUBTOTAL']."\">\n";
      $checkout_button .= "<input type=\"hidden\" name=\"WIN_FULL_PATH\" value=\"".$WIN_FULL_PATH."\">\n";



      # Auto-redirect to checkout? Or actually display cart contents and button?
      if ( $_REQUEST['goto_checkout'] == "yes" ) {
         $checkout_button .= "</form>\n";
         $checkout_button .= "<span style=\"display: none;\">".lang("Going to checkout")."..</span>\n";
         $checkout_button .= "<script type=\"text/javascript\">\n";
         $checkout_button .= "document.checkout_button.submit();\n";
         $checkout_button .= "</script>\n";

         echo $checkout_button; exit;

      } else {
         $checkout_button .= "<input type=\"image\" src=\"checkout_button.gif\" width=\"106\" height=\"25\" border=\"0\" style=\"cursor: hand;\">\n";
         $checkout_button .= "</form>\n";
         $THIS_DISPLAY .= " <td align=\"center\" valign=\"top\">\n";
         $THIS_DISPLAY .= $checkout_button;
         $THIS_DISPLAY .= " </td>\n";
      }

   }

   // ----------------------------------------------------------------

   $THIS_DISPLAY .= " </tr>\n";
   $THIS_DISPLAY .= "</table>\n";

	$THIS_DISPLAY .= "</TD></TR></TABLE>\n\n";




   /*---------------------------------------------------------------------------------------------------------*
    ___                                            _   ___  _
   | _ \ ___  __  ___  _ __   _ __   ___  _ _   __| | / __|| |__ _  _
   |   // -_)/ _|/ _ \| '  \ | '  \ / -_)| ' \ / _` | \__ \| / /| || |
   |_|_\\___|\__|\___/|_|_|_||_|_|_|\___||_||_|\__,_| |___/|_\_\ \_,_|

	# DISPLAY ANY PRODUCTS THAT ARE MARKED AS RECOMMEND WHILE VIEWING CURRENT SHOPPING CART ITEM
   /*---------------------------------------------------------------------------------------------------------*/

	// ----------------------------------------------------------------------------------
	// ADD SECURITY CODE [GROUPS] CONTROL OVER SEARCH RESULTS
	if (isset($GROUPS)) {

		$grp_check = " HAVING OPTION_SECURITYCODE IN (";

		$grp_tmp = split(";", $GROUPS);	// Split this user's sec code groups into individual array
		$grp_cnt = count($grp_tmp);		// How Many sec groups does this user have access to?

		for ($gl=0;$gl<=$grp_cnt;$gl++) {	// Start to build SQL "IN" cluster
			if ($grp_tmp[$gl] != "") {
				$grp_check .= "'$grp_tmp[$gl]', ";
			}
		}

		$grp_check .= "'Public')";

	} else {

		$grp_check = " HAVING OPTION_SECURITYCODE IN('Public')";

	}
	// -----------------------------------------------------------------------------------

	$result = mysql_query("SELECT * FROM cart_products WHERE OPTION_SHOWATEDIT = 'Y' $grp_check");
	$success = 0;

	while ($PROD = mysql_fetch_array($result)) {

		if (!eregi("$PROD[PROD_SKU];", $_SESSION['CART_SKUNO'])) {	// Don't promote something that user already has in cart
			$success++;
			ob_start();
			include("prod_search_template.inc");
				$TMP_OUTPUT .= ob_get_contents();
			ob_end_clean();
		}

	} // End recommend loop

	if ($success > 0) {

		// -----------------------------------------------------------------
		// Set up button click Javascript action for linking to the
		// "More Info" page. (Used with the User defined Button and the
		// Buy Now! button. (Buy Now button is changed to "Add to Cart"
		// if that option is on.
		// -----------------------------------------------------------------

		$THIS_DISPLAY .= "<SCRIPT LANGUAGE=Javascript>\n\n";
		$THIS_DISPLAY .= "     function userbutton(prikey) {\n";
		$THIS_DISPLAY .= "          var strlink = \"pgm-more_information.php?id=\"+prikey+\"&".SID."#MOREINFO\";\n";
		$THIS_DISPLAY .= "          window.location = strlink;\n";
		$THIS_DISPLAY .= "     }\n\n";
		$THIS_DISPLAY .= "\n</SCRIPT>\n\n";

		$THIS_DISPLAY .= "<hr width=\"100%\">\n";
		$THIS_DISPLAY .= "<div align=\"left\" class=\"text\">\n";
		$THIS_DISPLAY .= " <b>".lang("We also recommend the following product(s)").":</b><br/><br/>\n";
		$THIS_DISPLAY .= "</div>" . $TMP_OUTPUT;

	}

} // End View/Edit Cart Func


##########################################################################
### BUILD OVERALL TABLE TO PLACE FINAL OUTPUT WITHIN
##########################################################################

$FINAL_DISPLAY = "<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\" align=\"center\" class=\"parent_table\">\n";
$FINAL_DISPLAY .= " <tr>\n";
$FINAL_DISPLAY .= "  <td align=\"center\" valign=\"top\">\n\n".$THIS_DISPLAY."\n\n</td>\n";
$FINAL_DISPLAY .= " </tr>\n\n";

// ----------------------------------------------------------------------------------
// If a business address has been supplied, display at the footer of each shopping
// cart page.  This can be removed if you wish, but studies have shown it instills
// trust among consumers that wish to buy from this web site
// ----------------------------------------------------------------------------------

if ($OPTIONS[BIZ_ADDRESS_1] != "" && $OPTIONS[BIZ_POSTALCODE] != "") {

	$FINAL_DISPLAY .= " <tr>\n";
	$FINAL_DISPLAY .= "  <td align=\"center\" valign=\"middle\" class=\"smtext\">\n";
	$FINAL_DISPLAY .= "  <hr width=\"100%\">\nMailing Address: ".$OPTIONS['BIZ_ADDRESS_1'].", ";

		if ($OPTIONS[BIZ_ADDRESS2] != "") {
			$FINAL_DISPLAY .= "  $OPTIONS[BIZ_ADDRESS_2], ";
		}

	$FINAL_DISPLAY .= "  $OPTIONS[BIZ_CITY], $OPTIONS[BIZ_STATE], $OPTIONS[BIZ_POSTALCODE]\n<HR width=\"100%\" style='height: 1px; color: $OPTIONS[DISPLAY_HEADERBG];'>";
	$FINAL_DISPLAY .= "  </td>\n";
	$FINAL_DISPLAY .= " </tr>\n\n";

}


// ----------------------------------------------------------------------------------

$FINAL_DISPLAY .= "</table>";


###########################################################################
### THE pgm-realtime_builder.php FILE COMPILES THE TEMPLATE DATA AND PAGE
### CONTENT DATA TOGETHER AND PUTS IT OUT AS THE $template_header AND
### $template_footer VARS RESPECTIVELY.  ANY MODIFICATION TO CHANGE THE
### WAY PAGES ARE OUTPUT TO THE SITE VISITOR SHOULD BE MADE WITHIN THE
### realtime_builder.php FILE
###########################################################################

$module_active = "yes";
include("pgm-template_builder.php");

#######################################################

echo ("$template_header\n");

//$template_footer = eregi_replace("#CONTENT#", testArray($_SESSION), $template_footer);
$template_footer = eregi_replace("#CONTENT#", $FINAL_DISPLAY, $template_footer);

echo ("$template_footer\n\n");

echo ("\n\n<SCRIPT language=Javascript>\n     window.focus();\n</SCRIPT>\n\n");

exit;

?>