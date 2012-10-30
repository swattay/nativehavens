<?php
error_reporting(E_PARSE);
if($_GET['_SESSION'] != '' || $_POST['_SESSION'] != '' || $_COOKIE['_SESSION'] != '') { exit; }


##############################################################################
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
include("../../../includes/product_gui.php");

error_reporting(E_PARSE | E_USER_ERROR);

$cartpref = new userdata("cart");

# DEFAULT: "Complete Order >>"
if ( $cartpref->get("paypal_btn_text") == "" ) { $cartpref->set("paypal_btn_text", "Complete Order &gt;&gt;"); }

#######################################################
### PERFORM SAVE ACTION : UPDATE DATA TABLE TO REFLECT
### CHANGES MADE BY USER
#######################################################

$update_complete = 0;

# Newschool
$gateway = new userdata("gateway");

if ($ACTION == "savepaymentopts") {

   # paypal_btn_text
   if ( $_POST['paypal_btn_text'] != "" ) { $cartpref->set("paypal_btn_text", $_POST['paypal_btn_text']); }
   if ( $_POST['paypal_testmode'] != "" ) { $cartpref->set("paypal_testmode", $_POST['paypal_testmode']); }
   if ( $_POST['checkorcheque'] != "" ) { $cartpref->set("checkorcheque", $_POST['checkorcheque']); }
   if ( $_POST['check-sendemail'] != "" ) { $cartpref->set("check-sendemail", $_POST['check-sendemail']); }
   if ( $_POST['dps-logo-display'] != "" ) { $cartpref->set("dps-logo-display", $_POST['dps-logo-display']); }
   if ( $_POST['sandbox-email'] != "" ) { $cartpref->set("sandbox-email", $_POST['sandbox-email']); }

eval(hook("payment_options.php:save_gateway_config_info"));

   $pay_methods = "";

   $pay_types = "use_paystation;use_dps;use_paypro;use_paypal;use_check;use_worldpay;use_offline;use_innovgate;use_verisign;use_paypoint;use_authorize;use_internetsecure;use_eway";
eval(hook("payment_options.php:pay_types"));
   $payMeth = split(";",$pay_types);
   $ptypes = count($payMeth);

   for ( $p=0; $p < $ptypes; $p++ ) {
      if ( ${$payMeth[$p]} == "yes" || $live_cc == $payMeth[$p] ) {
         $pay_methods .= $payMeth[$p].";";
      }
   }

	if ($pay_methods == "use_check;") {
		mysql_query("UPDATE cart_options SET PAYMENT_PROCESSING_TYPE = '$pay_methods', PAYMENT_CHECK_ONLY = 'y', PAYMENT_CATALOG_ONLY = ' '");
	} elseif ($pay_methods == "") {
		mysql_query("UPDATE cart_options SET PAYMENT_PROCESSING_TYPE = ' ', PAYMENT_CHECK_ONLY = ' ', PAYMENT_CATALOG_ONLY = 'y'");
	} else {
	   mysql_query("UPDATE cart_options SET PAYMENT_PROCESSING_TYPE = '$pay_methods', PAYMENT_CHECK_ONLY = ' ', PAYMENT_CATALOG_ONLY = ' '");
	}

	$ccard_str = "";

	if ($visa == 1) { $ccard_str .= "Visa;"; }
	if ($mastercard == 1) { $ccard_str .= "Mastercard;"; }
	if ($amex == 1) { $ccard_str .= "Amex;"; }
	if ($discover == 1) { $ccard_str .= "Discover;"; }

	mysql_query("UPDATE cart_options SET PAYMENT_CREDIT_CARDS = '$ccard_str'");
	mysql_query("UPDATE cart_options SET PAYMENT_VPARTNERID = '$vpartnerid'");
	mysql_query("UPDATE cart_options SET PAYMENT_VLOGINID = '$vloginid'");
	mysql_query("UPDATE cart_options SET PAYMENT_INCLUDE = '$GATEWAY_INCLUDE'");
	mysql_query("UPDATE cart_options SET PAYMENT_CURRENCY_TYPE = '$cash_type'");
	mysql_query("UPDATE cart_options SET PAYMENT_CURRENCY_SIGN = '$cash_sign'");

	mysql_query("UPDATE cart_options SET PAYMENT_SSL = '$PAYMENT_SSL'");
	mysql_query("UPDATE cart_options SET INVOICE_INCLUDE = '$INVOICE_INCLUDE'");

		// ###########################################
		// Check for Paystation info
		// ###########################################

		$match = 0;
		$tablename = "cart_paystation";

		$result = mysql_list_tables("$db_name");
		$i = 0;
		while ($i < mysql_num_rows ($result)) {
			$tb_names[$i] = mysql_tablename ($result, $i);
			if ($tb_names[$i] == $tablename) {
				$match = 1;
			}
			$i++;
		}

		if ($match != 1) {

			mysql_db_query("$db_name","CREATE TABLE cart_paystation (

				PAYSTATION_ID CHAR(255),
				PAYSTATION_FUTURE CHAR(255)

			)");

			mysql_query("INSERT INTO cart_paystation VALUES(' ',' ')");

		} // End If no db exists

		//echo "this is the id (".$PX_ID.")<br>";

		mysql_query("UPDATE cart_paystation SET PAYSTATION_ID = '$PAYSTATION_ID'");

		// ###########################################
		// Check for payment express info
		// ###########################################

		$match = 0;
		$tablename = "cart_dps";

		$result = mysql_list_tables("$db_name");
		$i = 0;
		while ($i < mysql_num_rows ($result)) {
			$tb_names[$i] = mysql_tablename ($result, $i);
			if ($tb_names[$i] == $tablename) {
				$match = 1;
			}
			$i++;
		}

		if ($match != 1) {

			mysql_db_query("$db_name","CREATE TABLE cart_dps (

				DPS_USERNAME CHAR(255),
				DPS_PASSWORD CHAR(255),
				DPS_USER1 CHAR(255),
				DPS_USER2 CHAR(255)

			)");

			mysql_query("INSERT INTO cart_dps VALUES(' ',' ',' ',' ')");

		} // End If no db exists

		//echo "this is the id (".$PX_ID.")<br>";

		mysql_query("UPDATE cart_dps SET DPS_USERNAME = '$DPS_USERNAME'");
	   mysql_query("UPDATE cart_dps SET DPS_PASSWORD = '$DPS_PASSWORD'");

		// ###########################################
		// Check for Paypro info
		// ###########################################

		$match = 0;
		$tablename = "cart_paypro";

		$result = mysql_list_tables("$db_name");
		$i = 0;
		while ($i < mysql_num_rows ($result)) {
			$tb_names[$i] = mysql_tablename ($result, $i);
			if ($tb_names[$i] == $tablename) {
				$match = 1;
			}
			$i++;
		}

		if ($match != 1) {

			mysql_db_query("$db_name","CREATE TABLE cart_paypro (

				PAYPRO_ID CHAR(255),
				PAYPRO_TEST CHAR(255),
				PAYPRO_FUTURE1 CHAR(255)

			)");

			mysql_query("INSERT INTO cart_paypro VALUES(' ',' ',' ')");

		} // End If no db exists

		mysql_query("UPDATE cart_paypro SET PAYPRO_ID = '$PAYPRO'");


		// ###########################################
		// Check for eway info
		// ###########################################

		$match = 0;
		$tablename = "cart_eway";

		$result = mysql_list_tables("$db_name");
		$i = 0;
		while ($i < mysql_num_rows ($result)) {
			$tb_names[$i] = mysql_tablename ($result, $i);
			if ($tb_names[$i] == $tablename) {
				$match = 1;
			}
			$i++;
		}

		// if ($match == 1) { mysql_query("DROP TABLE cart_paypal"); }

		if ($match != 1) {

			mysql_db_query("$db_name","CREATE TABLE cart_eway (

				EWAY_ID CHAR(255),
				EWAY_USER1 CHAR(255),
				EWAY_USER2 CHAR(255),
				EWAY_USER3 CHAR(255)

			)");

			mysql_query("INSERT INTO cart_eway VALUES(' ',' ',' ',' ')");

		} // End If no db exists

		mysql_query("UPDATE cart_eway SET EWAY_ID = '$EWAY'");

	   mysql_query("UPDATE cart_eway SET EWAY_USER1 = '$EWAYONSITE'");
	   mysql_query("UPDATE cart_eway SET EWAY_USER2 = '$EWAYATEWAY'");

//		 elseif($EWAYATEWAY == "checked"){
//		   mysql_query("UPDATE cart_eway SET EWAY_USER1 = 'ateway'");
//		}


		// ###########################################
		// Check for paypal info (add on Dec 12 2002)
		// ###########################################

		$match = 0;
		$tablename = "cart_paypal";

		$result = mysql_list_tables("$db_name");
		$i = 0;
		while ($i < mysql_num_rows ($result)) {
			$tb_names[$i] = mysql_tablename ($result, $i);
			if ($tb_names[$i] == $tablename) {
				$match = 1;
			}
			$i++;
		}

		// if ($match == 1) { mysql_query("DROP TABLE cart_paypal"); }

		if ($match != 1) {

			mysql_db_query("$db_name","CREATE TABLE cart_paypal (

				PAYPAL_EMAIL CHAR(255),
				PAYPAL_USER1 CHAR(255),
				PAYPAL_USER2 CHAR(255),
				PAYPAL_USER3 CHAR(255)

			)");

			mysql_query("INSERT INTO cart_paypal VALUES(' ',' ',' ',' ')");

		} // End If no db exists

		mysql_query("UPDATE cart_paypal SET PAYPAL_EMAIL = '$PAYPAL'");


		// ###########################################
		// Check for Worldpay info (added April 2004)
		// ###########################################

		$match = 0;
		$tablename = "cart_worldpay";

		$result = mysql_list_tables("$db_name");
		$i = 0;
		while ($i < mysql_num_rows ($result)) {
			$tb_names[$i] = mysql_tablename ($result, $i);
			if ($tb_names[$i] == $tablename) {
				$match = 1;
			}
			$i++;
		}

		// if ($match == 1) { mysql_query("DROP TABLE cart_worldpay"); }

		if ($match != 1) {

			mysql_db_query("$db_name","CREATE TABLE cart_worldpay (

				WP_INSTALL_ID CHAR(255),
				WP_LATER CHAR(255),
				WP_LATER2 CHAR(255),
				WP_LATER3 CHAR(255)

			)");

			mysql_query("INSERT INTO cart_worldpay VALUES(' ',' ',' ',' ')");

		} // End If no db exists

		mysql_query("UPDATE cart_worldpay SET WP_INSTALL_ID = '$WP_instId', WP_LATER2 = '$WP_fixCurr', WP_LATER3 = '$WP_testMode'");


		##############################################################
		## Check for Innovative Gateway info (v4.7 RC5 - Sep 2004)
		##############################################################
		$match = 0;
		$tablename = "cart_innovgate";

		$result = mysql_list_tables("$db_name");
		$i = 0;
		while ($i < mysql_num_rows ($result)) {
			$tb_names[$i] = mysql_tablename ($result, $i);
			if ($tb_names[$i] == $tablename) {
				$match = 1;
			}
			$i++;
		}

		// if ($match == 1) { mysql_query("DROP TABLE cart_innovgate"); }

		if ($match != 1) {

			mysql_db_query("$db_name","CREATE TABLE cart_innovgate (

				IG_USER VARCHAR(255),
				IG_PASS VARCHAR(255),
				IG_TMODE VARCHAR(5),
				IG_LATER VARCHAR(255)

			)");

			mysql_query("INSERT INTO cart_innovgate VALUES(' ',' ',' ',' ')");

		} // End If no db exists

		mysql_query("UPDATE cart_innovgate SET IG_USER = '$IG_user', IG_PASS = '$IG_pass', IG_TMODE = '$IG_testMode'");


		##############################################################
		## Check for Store Center USA info
		##############################################################
		$match = 0;
		$tablename = "cart_paypoint";

		$result = mysql_list_tables("$db_name");
		$i = 0;
		while ($i < mysql_num_rows ($result)) {
			$tb_names[$i] = mysql_tablename ($result, $i);
			if ($tb_names[$i] == $tablename) {
				$match = 1;
			}
			$i++;
		}

		// if ($match == 1) { mysql_query("DROP TABLE cart_innovgate"); }

		if ($match != 1) {

			mysql_db_query("$db_name","CREATE TABLE cart_paypoint (
				SC_ACCTID VARCHAR(255),
				SC_SUBID VARCHAR(255),
				SC_PASSCODE VARCHAR(255),
				SC_TMODE VARCHAR(255)

			)");

			mysql_query("INSERT INTO cart_paypoint VALUES(' ',' ',' ',' ')");

		} // End If no db exists

		if ( !$upscdb = mysql_query("UPDATE cart_paypoint SET SC_ACCTID = '$SC_acctid', SC_PASSCODE = '$SC_passcode', SC_TMODE = '$SC_tmode'") ) {
		   echo "unable to update cart_paypoint because:<br>".mysql_error().")\n"; exit;
		}

		##############################################################
		## Check for Authorize.net info
		##############################################################
		$match = 0;
		$tablename = "cart_authorize";

		$result = mysql_list_tables("$db_name");
		$i = 0;
		while ($i < mysql_num_rows ($result)) {
			$tb_names[$i] = mysql_tablename ($result, $i);
			if ($tb_names[$i] == $tablename) {
				$match = 1;
			}
			$i++;
		}

		if ($match != 1) {

			mysql_db_query("$db_name","CREATE TABLE cart_authorize (
				AN_ACCTID VARCHAR(255),
				AN_ACCTKEY VARCHAR(255),
				AN_TMODE VARCHAR(255)

			)");

			mysql_query("INSERT INTO cart_authorize VALUES(' ',' ',' ')");

		} // End If no db exists

		if ( !$upscdb = mysql_query("UPDATE cart_authorize SET AN_ACCTID = '$AN_acctid', AN_ACCTKEY = '$AN_acctkey', AN_TMODE = '$AN_tmode'") ) {
		   echo "unable to update cart_authorize because:<br>".mysql_error().")\n"; exit;
		}
		
	$internetsecures = new userdata("internetsecure");
	$internetsecures->set("acctid", $IS_acctid);
	$internetsecures->set("acctkey", $IS_acctkey);

	$update_complete = 1;

}

###########################################################################################
##=========================================================================================
## READ CURRENT SETTINGS INTO MEMORY NOW
##=========================================================================================
###########################################################################################

// Current Payment Options
// -------------------------
$result = mysql_query("SELECT * FROM cart_options");
$PAYMENT = mysql_fetch_array($result);

// Paystation
// -------------------------
$result = mysql_query("SELECT * FROM cart_paystation");
$getPaystation = mysql_fetch_array($result);
$PAYSTATION_ID = $getPaystation['PAYSTATION_ID'];

// DPS
// -------------------------
$result = mysql_query("SELECT * FROM cart_dps");
$getpx = mysql_fetch_array($result);
$DPS_USERNAME = $getpx[DPS_USERNAME];
$DPS_PASSWORD = $getpx[DPS_PASSWORD];

// PayPro
// -------------------------
$result = mysql_query("SELECT * FROM cart_paypro");
$getpaypro = mysql_fetch_array($result);
$PAYPRO = $getpaypro[PAYPRO_ID];

// eWAY
// -------------------------
$result = mysql_query("SELECT * FROM cart_eway");
$geteway = mysql_fetch_array($result);
$EWAY = $geteway[EWAY_ID];

// Paypal
// -------------------------
$result = mysql_query("SELECT * FROM cart_paypal");
$getPay = mysql_fetch_array($result);
$PAYPAL = $getPay[PAYPAL_EMAIL];

// Worldpay
// -------------------------
$result = mysql_query("SELECT * FROM cart_worldpay");
$getWorld = mysql_fetch_array($result);
$WP_instId = $getWorld[WP_INSTALL_ID];
$WP_testMode = $getWorld[WP_LATER3];
// v4.7 RC5
if ( $WP_testMode == "Yes" ) { $WP_testMode = "ON"; }
if ( $WP_testMode == "No" ) { $WP_testMode = "OFF"; }

// Innovative Gateway
// -------------------------
$result = mysql_query("SELECT * FROM cart_innovgate");
$getInnov = mysql_fetch_array($result);
$IG_user = $getInnov[IG_USER];
$IG_pass = $getInnov[IG_PASS];
$IG_testMode = $getInnov[IG_TMODE];

// PayPoint USA
// -------------------------
$result = mysql_query("SELECT * FROM cart_paypoint");
$getInnov = mysql_fetch_array($result);
$SC_acctid = $getInnov['SC_ACCTID'];

// Authorize.net
// -------------------------
$result = mysql_query("SELECT * FROM cart_authorize");
$getAuth = mysql_fetch_array($result);
$AN_acctid = $getAuth['AN_ACCTID'];
$AN_acctkey = $getAuth['AN_ACCTKEY'];

// InternetSecure
// -------------------------
$internetsecure = new userdata("internetsecure");
$IS_acctid = $internetsecure->get("acctid");
$IS_acctkey = $internetsecure->get("acctkey");

#######################################################
### READ CURRENCIES FROM DAT FILE INTO MEMORY
#######################################################
$filename = "shared/money.dat";
$file = fopen("$filename", "r") or DIE("Error: Could not open us states data (shared/us_states.dat).");
	$tmp_data = fread($file,filesize($filename));
fclose($file);

$cMoney[0] = "Numeric Value Only::none::none";
$cTmp = split("\n", $tmp_data);
$cNum = count($cTmp) + 1;

for ($t=1; $t <= $cNum; $t++) {
   $i = $t - 1;
   $cMoney[$t] = $cTmp[$i];
}

#######################################################
### START HTML/JAVASCRIPT CODE			             ###
#######################################################

ob_start();

?>

<script language="JavaScript">
<!--
function config_verisign() {

	var str = "How to configure VeriSign Payflow Link for your site\n";
	var str = str + "====================================================\n\n";
	var str = str + "First, login to the Verisign Manager via the link provided to\n";
	var str = str + "you by Verisign. Should be 'https://manager.paypal.com'\n\n";
	var str = str + "1. Select the 'Account Info' menu item at the top of the screen.\n\n";
	var str = str + "2. Select the 'Payflow Link Info' option.\n\n";
	var str = str + "3. Set the 'Return URL Method' to 'POST'.\n\n";
	var str = str + "4. Set the 'Return URL' to equal:\n   'http://www.[your_website_url.com]/shopping/pgm-show_invoice.php'.\n\n";
	var str = str + "5. Check the 'Silent POST URL' box and set the location value to:\n";
	var str = str + "   'http://www.[your_website_url.com]/shopping/pgm-silent_post.php'.\n\n";
	var str = str + "6. Scroll to the bottom of the screen and click 'Save Changes'.\n\n";
	var str = str + "Your Payflow Link system is now configured!.\n\n";

	alert(str);

}

function config_worldpay() {

	var str = "How to configure WorldPay for your site\n";
	var str = str + "====================================================\n\n";
	var str = str + "First, login to the WorldPay Account Manager via the link provided to\n";
	var str = str + "you by WorldPay. Should be 'https://support.worldpay.com/admin'\n\n";
	var str = str + "1. Select the 'Configure Options' link item at the bottom-left.\n\n";
	var str = str + "2. Select the 'Payflow Link Info' option.\n\n";
	var str = str + "3. Set the 'Callback URL' to equal:\n   'http://www.[your_website_url.com]/shopping/pgm-silent_post.php'.\n\n";
	var str = str + "4. Check the 'Callback enabled' box.\n";
	var str = str + "Your WorldPay system is now configured!.\n\n";

	alert(str);

}

function config_paypal() {

	var str = "How to configure WorldPay for your site\n";
	var str = str + "====================================================\n\n";
	var str = str + "First, login to the WorldPay Account Manager via the link provided to\n";
	var str = str + "you by WorldPay. Should be 'https://support.worldpay.com/admin'\n\n";
	var str = str + "1. Select the 'Configure Options' link item at the bottom-left.\n\n";
	var str = str + "2. Select the 'Payflow Link Info' option.\n\n";
	var str = str + "3. Set the 'Callback URL' to equal:\n   'http://www.[your_website_url.com]/shopping/pgm-silent_post.php'.\n\n";
	var str = str + "4. Check the 'Callback enabled' box.\n";
	var str = str + "Your WorldPay system is now configured!.\n\n";

	alert(str);

}


show_hide_layer('addCartMenu?header','','hide');
show_hide_layer('blankLayer?header','','hide');
show_hide_layer('linkLayer?header','','hide');
show_hide_layer('newsletterLayer?header','','hide');
show_hide_layer('cartMenu?header','','show');
show_hide_layer('menuLayer?header','','hide');
show_hide_layer('editCartMenu?header','','hide');

function ifshow(ifthisid, equalsthisvalue, showthisid) {
	if ( $(ifthisid).value == equalsthisvalue ) {
		showid(showthisid);
	} else {
		hideid(showthisid);
	}
}
<?

if ($update_complete == 1) {

	echo ("alert('Your payment options have been updated.');\n");

}

?>

//-->

// Spawns pop-up window with requested help file
//-----------------------------------------------------
function popdoc(fnam) {
   var ddoc = "popdocs/"+fnam+".html.php";
   window.open(ddoc,'docpop','width=500,height=380,screenX=400,screenY=400,top=400,left=400,location=no,menubar=no,scrollbars=no,status=no,toolbar=no,resizable=no');
}

function checkMe() {
   document.getElementById('EWAYATEWAY').checked = false;
//   if(document.getElementById('EWAYATEWAY').checked){
//      alert('Please select only one eWAY processing option');
//      document.getElementById('EWAYONSITE').checked = false;
//   }
}
function checkMeTo() {
   document.getElementById('EWAYONSITE').checked = false;
//   if(document.getElementById('EWAYONSITE').checked){
//      alert('Please select only one eWAY processing option');
//      document.getElementById('EWAYATEWAY').checked = false;
//   }
}

// Swaps preview image based on whether they want "Check" or "Cheque"
function checkorcheque_preview() {
   var isnow = $('checkorcheque').value;
   if ( isnow == '' ) { isnow = 'check'; }
   $('checkorcheque-preview').src = 'http://<?php echo $_SESSION['this_ip']; ?>/sohoadmin/client_files/shopping_cart/pay-'+isnow+'.gif';
}

</script>

<link rel="stylesheet" href="shopping_cart.css">

<style>
h3, h4 {
   margin-bottom: 0;
}

#toc {
   float: left;
}
</style>

<?

$THIS_DISPLAY = "";

$THIS_DISPLAY .= "<FORM NAME=PAY METHOD=POST ACTION=\"payment_options.php\">\n";
$THIS_DISPLAY .= "<input type=hidden name=ACTION value=savepaymentopts>\n\n";



 #
##=====================================================================================================
 #  What type of payment processing will you utilize?
 #=====================================================================================================
###

// Format options based on current settings
// ==================================================
$chkpaystation = ""; $chkdps = ""; $chkpaypro = ""; $chkpp = ""; $chkchk = ""; $chkvs = ""; $chkwp = ""; $chkol = ""; $chknp = ""; $chkig = ""; $chksc = ""; $chkan = ""; $chkew = "";

// Must specify account to utilize these
// -------------------------------------------
//if ( $PAYMENT[PAYMENT_VPARTNERID] == "" || $PAYMENT[PAYMENT_VLOGINID] == "" ) { $chkvs = " disabled"; }
//if ( $WP_instId == "" || $WP_testMode == "" ) { $chkwp = " disabled"; }
//if ( $PAYPAL == "" ) { $chkpp = " disabled"; }


# Pull current payment type settings
function is_checked($gateway, $yes = "checked" ) {
   global $PAYMENT;
   if ( eregi( $gateway, $PAYMENT['PAYMENT_PROCESSING_TYPE']) ) {
      $output = $yes;
   } else {
      $output = "";
   }
   return $output;
//   echo "[".$gateway.", ".$PAYMENT['PAYMENT_PROCESSING_TYPE']." ===> ".$output."]"; exit;
}

if ( eregi( "paypro", $PAYMENT['PAYMENT_PROCESSING_TYPE']) ) { $chkpaypro = " checked"; }
if ( eregi( "paypal", $PAYMENT['PAYMENT_PROCESSING_TYPE']) ) { $chkpp = " checked"; }
if ( eregi( "check", $PAYMENT['PAYMENT_PROCESSING_TYPE']) ) { $chkchk = " checked"; }
if ( eregi( "worldpay", $PAYMENT['PAYMENT_PROCESSING_TYPE']) ) { $chkwp = " checked"; }
if ( eregi( "verisign", $PAYMENT['PAYMENT_PROCESSING_TYPE']) ) { $chkvs = " checked"; }
if ( eregi( "paystation", $PAYMENT['PAYMENT_PROCESSING_TYPE']) ) { $chkpaystation = " checked"; }
if ( eregi( "offline", $PAYMENT['PAYMENT_PROCESSING_TYPE']) ) { $chkol = " checked"; }
if ( eregi( "innovgate", $PAYMENT['PAYMENT_PROCESSING_TYPE']) ) { $chkig = " checked"; }
if ( eregi( "paypoint", $PAYMENT['PAYMENT_PROCESSING_TYPE']) ) { $chksc = " checked"; }
if ( eregi( "authorize", $PAYMENT['PAYMENT_PROCESSING_TYPE']) ) { $chkan = " checked"; }
if ( eregi( "internetsecure", $PAYMENT['PAYMENT_PROCESSING_TYPE']) ) { $chkis = " checked"; }
if ( eregi( "eway", $PAYMENT['PAYMENT_PROCESSING_TYPE']) ) { $chkew = " checked"; }
if ( eregi( "dps", $PAYMENT['PAYMENT_PROCESSING_TYPE']) ) { $chkdps = " checked"; }

if ( $chkol.$chkig.$chkvs == "" ) { $chknp = " checked"; } // No live processing


# toc
$THIS_DISPLAY .= "<ol id=\"toc\">\n";
$THIS_DISPLAY .= " <li><a href=\"#processing_options\">".lang("Choose Processing Method(s)")."</a></li>\n";
$THIS_DISPLAY .= " <li><a href=\"#gateway_info\">".lang("Configure Processing Method(s)")."</a></li>\n";
$THIS_DISPLAY .= " <li><a href=\"#card_types\">".lang("Available Card Types")."</a></li>\n";
$THIS_DISPLAY .= " <li><a href=\"#currency\">".lang("Choose Currency Type/Symbol")."</a></li>\n";
$THIS_DISPLAY .= " <li><a href=\"#ssl_cert\">".lang("SSL Certificate information")."</a></li>\n";
$THIS_DISPLAY .= " <li><a href=\"#gateway_include\">".lang("Custom gateway include")."</a></li>\n";
$THIS_DISPLAY .= " <li><a href=\"#custom_invoice_include\">".lang("Custom invoice include script")."</a></li>\n";
$THIS_DISPLAY .= "</ol>\n";

# Save now
$THIS_DISPLAY .= "<div style=\"text-align: right;\">\n";
$THIS_DISPLAY .= " <INPUT class=\"btn_save\" TYPE=SUBMIT VALUE=\"".lang("Save Payment Options")."\" onmouseover=\"this.className='btn_saveon';\" onmouseout=\"this.className='btn_save';\">\n";
$THIS_DISPLAY .= " <p class=\"nomar_top note font90\">Note: Has the same effect as the Save button at the bottom</p>\n";
$THIS_DISPLAY .= "</div>\n";

$THIS_DISPLAY .= "<div class=\"id_cleardiv\"></div>\n";

# 1. processing_options
$THIS_DISPLAY .= "<a name=\"processing_options\"></a>\n";
$THIS_DISPLAY .= "<h2 class=\"nomar_btm\">1. ".lang("Choose Processing Method(s)")."</h2>";
$THIS_DISPLAY .= "<p class=\"nomar_top\">".lang("What type of payment processing options will you offer to your customers")."? \n";
$THIS_DISPLAY .= " ".lang("Pick the payment options you want to offer in step #1, then go to step #2 to fill-in the neccessary info for the payment gateways you select in step #1.")."<br/>\n";
$THIS_DISPLAY .= "<p><b>Note:</b> If you don't select any payment option your cart will default to\n";
$THIS_DISPLAY .= "operating in \"catalog only\" mode, which means all of your products will display but no purchase options will be offered.</p></p>\n";


$THIS_DISPLAY .= "<table border=\"0\" cellpadding=\"4\" cellspacing=\"0\" width=\"100%\">\n";
$THIS_DISPLAY .= " <tr>\n";
$THIS_DISPLAY .= "  <td width=\"50%\" align=\"left\" valign=\"middle\">\n";
$THIS_DISPLAY .= "   <h3>".lang("Send them to a third-party gateway for payment.")."</h3>\n";
$THIS_DISPLAY .= "  </td>\n";

$THIS_DISPLAY .= "  <td align=\"left\" valign=\"middle\">\n";
$THIS_DISPLAY .= "   <h3>".lang("Process their credit card directly on <i>your</i> website.")."</h3>\n";
$THIS_DISPLAY .= "  </td>\n";
$THIS_DISPLAY .= " </tr>\n";


$THIS_DISPLAY .= " <tr>\n";

# Third-party payment processing
#-------------------------------------------
$THIS_DISPLAY .= "  <td valign=\"top\">\n";

$THIS_DISPLAY .= "   <table border=\"0\" cellpadding=\"3\" cellspacing=\"0\">\n";

# Check or Money Order
$THIS_DISPLAY .= "    <tr>\n";
$THIS_DISPLAY .= "     <td><input type=checkbox name=\"use_check\" value=\"yes\"".$chkchk."></td>\n";
//$THIS_DISPLAY .= "     <td><img src=\"pay-check.gif\"></td>\n";
$THIS_DISPLAY .= "     <td valign=\"top\"><a href=\"#check_moneyorder\">".lang("Check / Money Order")."</a></td>\n";
$THIS_DISPLAY .= "    </tr>\n";

# PayPal
$THIS_DISPLAY .= "    <tr>\n";
$THIS_DISPLAY .= "     <td><input type=checkbox name=\"use_paypal\" value=\"yes\"".$chkpp."></td>\n";
//$THIS_DISPLAY .= "     <td><a href=\"http://www.paypal.com\" target=\"_blank\"><img src=logo-paypal.gif border=0></a></td>\n";
$THIS_DISPLAY .= "     <td valign=\"top\"><a href=\"#paypal\">".lang("PayPal Website Payments")."</a></td>\n";
$THIS_DISPLAY .= "    </tr>\n";

# WorldPay
$THIS_DISPLAY .= "    <tr>\n";
$THIS_DISPLAY .= "     <td><input type=checkbox name=use_worldpay value=\"yes\"".$chkwp."></td>\n";
//$THIS_DISPLAY .= "     <td><a href=\"https://secure.worldpay.com/app/splash.pl?Pid=$wpay_pid\" target=\"_blank\"><img src=\"logo-worldpay-ani.gif\" border=\"0\"></a></td>\n";
$THIS_DISPLAY .= "     <td valign=\"top\">".lang("WorldPay Payment System")."</td>\n";
$THIS_DISPLAY .= "    </tr>\n";

# PayPro
$THIS_DISPLAY .= "    <tr>\n";
$THIS_DISPLAY .= "     <td><input type=checkbox name=\"use_paypro\" value=\"yes\"".$chkpaypro."></td>\n";
//$THIS_DISPLAY .= "     <td><a href=\"http://www.paypro.co.nz\" target=\"_blank\"><img src=\"logo-paypro.gif\" border=\"1\"></a></td>\n";
$THIS_DISPLAY .= "     <td valign=\"top\">".lang("PayPro")."</td>\n";
$THIS_DISPLAY .= "    </tr>\n";

# VeriSign
$THIS_DISPLAY .= "    <tr>\n";
$THIS_DISPLAY .= "     <td><input type=\"checkbox\" name=\"use_verisign\" value=\"yes\"".$chkvs."></td>\n";
//$THIS_DISPLAY .= "     <td><a href=\"http://www.verisign.com\" target=\"_blank\"><img src=logo-verisign.gif border=0></a></td>\n";
$THIS_DISPLAY .= "     <td valign=\"top\">".lang("VeriSign")."</td>\n";
$THIS_DISPLAY .= "    </tr>\n";

# Paystation
$THIS_DISPLAY .= "    <tr>\n";
$THIS_DISPLAY .= "     <td><input type=checkbox name=\"use_paystation\" value=\"yes\"".$chkpaystation."></td>\n";
//$THIS_DISPLAY .= "     <td><a href=\"https://www.paystation.com\" target=\"_blank\"><img src=\"images/logo-paystation.jpg\" border=\"0\"></a></td>\n";
$THIS_DISPLAY .= "     <td valign=\"top\">".lang("Paystation")."</td>\n";
$THIS_DISPLAY .= "    </tr>\n";

eval(hook("payment_options.php:gateway_checkbox"));

$THIS_DISPLAY .= "   </table>\n";
$THIS_DISPLAY .= "  </td>\n";


# Live Credit Card Processing
#--------------------------------
$THIS_DISPLAY .= "  <td valign=\"top\">\n";

$THIS_DISPLAY .= "   <table border=\"0\" cellpadding=\"3\" cellspacing=\"0\">\n";

# None - catalog only
$THIS_DISPLAY .= "    <tr>\n";
$THIS_DISPLAY .= "     <td width=\"25px\"><input type=\"radio\" name=\"live_cc\" value=\"\"".$chknp."></td>\n";
//$THIS_DISPLAY .= "     <td><a href=\"https://www.paystation.com\" target=\"_blank\"><img src=\"images/logo-paystation.jpg\" border=\"0\"></a></td>\n";
$THIS_DISPLAY .= "     <td valign=\"top\">".lang("None")."</td>\n";
$THIS_DISPLAY .= "    </tr>\n";

# Offline credit card
$THIS_DISPLAY .= "    <tr>\n";
$THIS_DISPLAY .= "     <td><input type=\"radio\" name=\"live_cc\" value=\"use_offline\"".$chkol."></td>\n";
//$THIS_DISPLAY .= "     <td><a href=\"https://www.paystation.com\" target=\"_blank\"><img src=\"images/logo-paystation.jpg\" border=\"0\"></a></td>\n";
$THIS_DISPLAY .= "     <td valign=\"top\">".lang("Offline Credit Card")."</td>\n";
$THIS_DISPLAY .= "    </tr>\n";

# Offline credit card notice
$THIS_DISPLAY .= "    <tr>\n";
$THIS_DISPLAY .= "     <td colspan=\"2\" valign=\"top\" class=\"dred\">".lang("<b>NOTE:</b> For security purposes, when a order is processed using offline credit card, the first half of the card number along with the security code is sent via email to the notification address set in Shopping Cart > Business Information.  The last half is stored in the invoice display.")."</td>\n";
$THIS_DISPLAY .= "    </tr>\n";

# Innovative
$THIS_DISPLAY .= "    <tr>\n";
$THIS_DISPLAY .= "     <td><input type=\"radio\" name=\"live_cc\" value=\"use_innovgate\"".$chkig."></td>\n";
//$THIS_DISPLAY .= "     <td><a href=\"http://www.innovativegateway.com\" target=\"_blank\"><img src=\"logo-innovative.gif\" border=\"0\"></a></td>\n";
$THIS_DISPLAY .= "     <td valign=\"top\">".lang("Innovative Gateway")."</td>\n";
$THIS_DISPLAY .= "    </tr>\n";

# PayPoint USA
$THIS_DISPLAY .= "    <tr>\n";
$THIS_DISPLAY .= "     <td><input type=\"radio\" name=\"live_cc\" value=\"use_paypoint\"".$chksc."></td>\n";
//$THIS_DISPLAY .= "     <td><a href=\"http://www.paypointusa.com\" target=\"_blank\"><img src=\"images/logo-paypoint_usa.gif\" border=\"0\"></a></td>\n";
$THIS_DISPLAY .= "     <td valign=\"top\">".lang("PayPoint USA")."</td>\n";
$THIS_DISPLAY .= "    </tr>\n";;

# Authorize.net
$THIS_DISPLAY .= "    <tr>\n";
$THIS_DISPLAY .= "     <td><input type=\"radio\" name=\"live_cc\" value=\"use_authorize\"".$chkan."></td>\n";
//$THIS_DISPLAY .= "     <td><a href=\"http://www.authorize.net\" target=\"_blank\"><img src=\"images/logo-authorize_net.gif\" border=\"0\"></a></td>\n";
$THIS_DISPLAY .= "     <td valign=\"top\">".lang("Authorize.net")."</td>\n";
$THIS_DISPLAY .= "    </tr>\n";

# InternetSecure
$THIS_DISPLAY .= "    <tr>\n";
$THIS_DISPLAY .= "     <td><input type=\"radio\" name=\"live_cc\" value=\"use_internetsecure\"".$chkis."></td>\n";
$THIS_DISPLAY .= "     <td valign=\"top\">".lang("InternetSecure")."</td>\n";
$THIS_DISPLAY .= "    </tr>\n";

# eWay
$THIS_DISPLAY .= "    <tr>\n";
$THIS_DISPLAY .= "     <td><input type=\"radio\" name=\"live_cc\" value=\"use_eway\"".$chkew."></td>\n";
//$THIS_DISPLAY .= "     <td><a href=\"http://www.eway.com.au\" target=\"_blank\"><img src=\"images/logo-eway.gif\" border=\"0\"></a></td>\n";
$THIS_DISPLAY .= "     <td valign=\"top\">".lang("eWay")."</td>\n";
$THIS_DISPLAY .= "    </tr>\n";

# Payments Express
$THIS_DISPLAY .= "    <tr>\n";
$THIS_DISPLAY .= "     <td><input type=\"radio\" name=\"live_cc\" value=\"use_dps\"".is_checked("dps")."></td>\n";
//$THIS_DISPLAY .= "     <td><a href=\"https://www.paymentexpress.com\" target=\"_blank\"><img src=\"images/logo-dps.gif\" border=\"0\"></a></td>\n";
$THIS_DISPLAY .= "     <td valign=\"top\"><a href=\"#payments-express\">".lang("Payments Express")."</a></td>\n";
$THIS_DISPLAY .= "    </tr>\n";

$THIS_DISPLAY .= "   </table>\n";
$THIS_DISPLAY .= "  </td>\n";

$THIS_DISPLAY .= " </tr>\n";

$THIS_DISPLAY .= "</table>\n\n";



###
  #===================================================================================================
###   Select Payment System (Online Processing)
#=====================================================================================================
###

# 2. gateway_info
$THIS_DISPLAY .= "<a name=\"gateway_info\"></a>\n";
$THIS_DISPLAY .= "<h2 class=\"nomar_btm\">2. ".lang("Configure Processing Method(s)")."</h2>";
$THIS_DISPLAY .= "<p class=\"nomar_top\">Fill-in the neccessary info for the payment gateways\n";
$THIS_DISPLAY .= "you selected in step #1.</p>\n";
$THIS_DISPLAY .= "<table border=\"0\" cellpadding=\"5\" cellspacing=\"0\">\n";

# Check/Money Order
$THIS_DISPLAY .= " <tr>\n";
$THIS_DISPLAY .= "  <td align=\"center\" valign=\"middle\" rowspan=\"3\">\n";
$THIS_DISPLAY .= "   <a name=\"check_moneyorder\"></a>\n";
$THIS_DISPLAY .= "   <img id=\"checkorcheque-preview\" src=\"http://".$_SESSION['this_ip']."/sohoadmin/client_files/shopping_cart/pay-check.gif\" border=\"0\">\n";
$THIS_DISPLAY .= "  </td>\n";
$THIS_DISPLAY .= "  <td align=\"left\" valign=\"top\" colspan=\"2\" style=\"padding-bottom:0px;\">\n";
$THIS_DISPLAY .= "   <strong>Check/Money Order Options</strong>\n";
$THIS_DISPLAY .= "  </td>\n";
$THIS_DISPLAY .= " </tr>\n";

# Check or cheque?
$THIS_DISPLAY .= " <tr>\n";
$THIS_DISPLAY .= "  <td align=\"left\" valign=\"middle\" style=\"padding-top:0px;\">\n";
$THIS_DISPLAY .= "      ".lang("Check or Checque?")."<br>\n";
$THIS_DISPLAY .= "  </td>\n";
$THIS_DISPLAY .= "  <td align=\"left\" valign=\"top\" style=\"padding-top:0px;\">\n";
if ( $cartpref->get("checkorcheque") == "" ) { $cartpref->set("checkorcheque", "check"); }
$THIS_DISPLAY .= "   <select id=\"checkorcheque\" name=\"checkorcheque\" onchange=\"checkorcheque_preview();\">\n";
$THIS_DISPLAY .= "    <option value=\"check\" selected>check</option>\n";
$THIS_DISPLAY .= "    <option value=\"cheque\">cheque</option>\n";
$THIS_DISPLAY .= "   </select>\n";
$THIS_DISPLAY .= "   <script type=\"text/javascript\">$('checkorcheque').value = '".$cartpref->get("checkorcheque")."';</script>\n";
$THIS_DISPLAY .= "  </td>\n";
$THIS_DISPLAY .= " </tr>\n";

# Send email notifications?
$THIS_DISPLAY .= " <tr>\n";
$THIS_DISPLAY .= "  <td align=\"left\" valign=\"middle\" style=\"padding-top:0px;\">\n";
$THIS_DISPLAY .= "   ".lang("Email notify?")."<br>\n";
$THIS_DISPLAY .= "  </td>\n";
$THIS_DISPLAY .= "  <td align=\"left\" valign=\"top\" style=\"padding-top:0px;\">\n";
if ( $cartpref->get("check-sendemail") == "" ) { $cartpref->set("check-sendemail", "yes"); }
$THIS_DISPLAY .= "   <select id=\"check-sendemail\" name=\"check-sendemail\">\n";
$THIS_DISPLAY .= "    <option value=\"yes\" selected>yes</option>\n";
$THIS_DISPLAY .= "    <option value=\"no\">no</option>\n";
$THIS_DISPLAY .= "   </select>\n";
$THIS_DISPLAY .= "   <script type=\"text/javascript\">$('check-sendemail').value = '".$cartpref->get("check-sendemail")."';</script>\n";
$THIS_DISPLAY .= "  </td>\n";
$THIS_DISPLAY .= " </tr>\n";

$THIS_DISPLAY .= "<tr><td colspan=3>&nbsp;</td></tr>\n\n";

##-----------------------------------------------------
//----------------PayPoint USA----------------------
##-----------------------------------------------------
$THIS_DISPLAY .= " <tr>\n";
$THIS_DISPLAY .= "  <td align=\"center\" valign=\"middle\" rowspan=\"2\">\n";
$THIS_DISPLAY .= "   <a href=\"http://www.paypointusa.com\" target=\"_blank\"><img src=\"images/logo-paypoint_usa.gif\" border=\"0\"></a>\n";
$THIS_DISPLAY .= "  </td>\n";
$THIS_DISPLAY .= "  <td align=\"left\" valign=\"top\" colspan=\"2\" style=\"padding-bottom:0px;\">\n";
$THIS_DISPLAY .= "   <font color=\"#336699\"><b>".lang("PayPoint USA Quicksale")."</b></font><br>\n";
$THIS_DISPLAY .= "  </td>\n";
$THIS_DISPLAY .= " </tr>\n";
$THIS_DISPLAY .= " <tr>\n";
$THIS_DISPLAY .= "  <td align=\"left\" valign=\"middle\" style=\"padding-top:0px;\">\n";
$THIS_DISPLAY .= "   ".lang("Account ID").":\n";
$THIS_DISPLAY .= "  </td>\n";
$THIS_DISPLAY .= "  <td align=\"left\" valign=\"top\" style=\"padding-top:0px;\">\n";
$THIS_DISPLAY .= "   <input type=\"text\" name=\"SC_acctid\" class=\"text\" value=\"".$SC_acctid."\">\n";
$THIS_DISPLAY .= "  </td>\n";
$THIS_DISPLAY .= " </tr>\n";
$THIS_DISPLAY .= "<tr><td colspan=3>&nbsp;</td></tr>\n\n";


##-----------------------------------------------------
//---------------------WorldPay------------------------
##-----------------------------------------------------
# WorldPay Payment System
$THIS_DISPLAY .= " <tr>\n";
$THIS_DISPLAY .= "  <td align=\"center\" valign=\"top\" rowspan=\"4\">\n";
$THIS_DISPLAY .= "   <a href=\"http://worldpay.com\" target=\"_blank\"><img src=\"logo-worldpay-ani.gif\" border=\"0\"></a><br>\n";
$THIS_DISPLAY .= "   <font size=\"1\"><a href=\"#\" onclick=\"popdoc('worldpay_config');\" class=\"sav nounderline\">".lang("How to configure WorldPay")."</a></font>\n";
$THIS_DISPLAY .= "  </td>\n";

# How to configure WorldPay
$THIS_DISPLAY .= "  <td align=\"left\" valign=\"top\" colspan=\"2\" style=\"padding-bottom:0px;\">\n";
$THIS_DISPLAY .= "   <font color=\"#336699\"><b>".lang("WorldPay Payment System")."</b></font>\n";
$THIS_DISPLAY .= "  </td>\n";
$THIS_DISPLAY .= " </tr>\n";

# Installation ID
#===============================================
$THIS_DISPLAY .= " <tr>\n";
$THIS_DISPLAY .= "  <td align=\"left\" valign=\"middle\" style=\"padding-top:0px;\">\n";
$THIS_DISPLAY .= "   ".lang("Installation ID:")."\n";
$THIS_DISPLAY .= "  </td>\n";
$THIS_DISPLAY .= "  <td align=\"left\" valign=\"top\" style=\"padding-top:0px;\">\n";
$THIS_DISPLAY .= "   <input type=text name=\"WP_instId\" class=text value=\"".$WP_instId."\">\n";
$THIS_DISPLAY .= "  </td>\n";
$THIS_DISPLAY .= " </tr>\n\n";


# Fix currency type?
#===============================================
$THIS_DISPLAY .= " <tr>\n";
$THIS_DISPLAY .= "  <td align=\"left\" valign=\"middle\" style='padding-top:0px;'>".lang("Fix Currency Type")."?</td>\n";
$THIS_DISPLAY .= "  <td align=left valign=top style='padding-top:0px;'>\n";
$THIS_DISPLAY .= "   <select name='WP_fixCurr'>\n";

// Select current setting
//----------------------------
$wpFixed = "Yes;No";
$wpOpts = split(";", $wpFixed);

for ( $t=0; $t < count($wpOpts); $t++ ) {
   $sel = "";
   if ( $wpOpts[$t] == $WP_fixCurr ) { $sel = " selected"; }
   $THIS_DISPLAY .= "    <option value='$wpOpts[$t]'$sel>$wpOpts[$t]</option>\n";
}

$THIS_DISPLAY .= "   </select>\n";
$THIS_DISPLAY .= "  </td>\n";
$THIS_DISPLAY .= " </tr>\n\n";

# Test Mode:
#==============================================
$THIS_DISPLAY .= " <tr>\n";
$THIS_DISPLAY .= "  <td align=\"left\" valign=\"middle\" style=\"padding-top:0px;\">\n";
$THIS_DISPLAY .= "   ".lang("Test Mode:")."</td>\n";
$THIS_DISPLAY .= "  <td align=\"left\" valign=\"top\" style=\"padding-top:0px;\">\n";
$THIS_DISPLAY .= "   <select name=\"WP_testMode\"  style=\"color: #d70000;\">\n";

// Select current test mode setting
//-------------------------------------
$wpTest = "OFF;ACCEPT;DECLINE";
$wpOpt = split(";",$wpTest);

for ( $t=0; $t < count($wpOpt); $t++ ) {
   $sel = "";
   if ( $wpOpt[$t] == $WP_testMode ) { $sel = " selected"; }
   $THIS_DISPLAY .= "   <option value='$wpOpt[$t]'$sel>$wpOpt[$t]</option>\n";
}

$THIS_DISPLAY .= "</td></tr>\n\n";

$THIS_DISPLAY .= "<tr><td colspan=3>&nbsp;</td></tr>\n\n";

##-----------------------------------------------------
//-------------------PayPal----------------------------
##-----------------------------------------------------
$THIS_DISPLAY .= " <tr>\n";
$THIS_DISPLAY .= "  <td align=\"center\" valign=\"top\" rowspan=\"4\">\n";
$THIS_DISPLAY .= "   <a name=\"paypal\"></a>\n";
$THIS_DISPLAY .= "   <a href=\"http://www.paypal.com\" target=\"_blank\"><img src=logo-paypal.gif border=0></a>\n";
$THIS_DISPLAY .= "  </td>\n";
$THIS_DISPLAY .= "  <td align=left valign=top colspan=2 style='padding-bottom:0px;'>\n";
$THIS_DISPLAY .= "   <font color=#336699><b>PayPal Payments</b></font>\n";
$THIS_DISPLAY .= "  </td>\n";
$THIS_DISPLAY .= " </TR>\n";

$THIS_DISPLAY .= " <tr>\n";
$THIS_DISPLAY .= "  <td align=left valign=middle width=125 style='padding-top:0px;'>\n";
$THIS_DISPLAY .= "   ".lang("PayPal Email:")."\n";
$THIS_DISPLAY .= "  </td>\n";
$THIS_DISPLAY .= "  <td align=left valign=top style='padding-top:0px;'>\n";
$THIS_DISPLAY .= "   <input type=text name=\"PAYPAL\" class=text value=\"".$PAYPAL."\" style='width: 200px;'>\n";
$THIS_DISPLAY .= "  </td>\n";
$THIS_DISPLAY .= " </tr>\n";

# paypal_btn_text
$THIS_DISPLAY .= " <tr>\n";
$THIS_DISPLAY .= "  <td align=left valign=middle width=125 style='padding-top:0px;'>\n";
$THIS_DISPLAY .= "   ".lang("Continue Btn Text:")."\n";
$THIS_DISPLAY .= "  </td>\n";
$THIS_DISPLAY .= "  <td align=left valign=top style='padding-top:0px;'>\n";
$THIS_DISPLAY .= "   <input type=text name=\"paypal_btn_text\" class=text value=\"".$cartpref->get("paypal_btn_text")."\" style='width: 200px;'>\n";
$THIS_DISPLAY .= "  </td>\n";
$THIS_DISPLAY .= " </tr>\n";

$THIS_DISPLAY .= "    <tr>\n";
$THIS_DISPLAY .= "     <td align=left valign=\"top\" width=\"75\" style='padding-top:0px;'>\n";
$THIS_DISPLAY .= "      ".lang("Test Mode:")."\n";
$THIS_DISPLAY .= "     </td>\n";
$THIS_DISPLAY .= "     <td align=left style='padding-top:0px;'>\n";
if ( $cartpref->get("paypal_testmode") == "" ) { $cartpref->set("paypal_testmode", "off"); }
$THIS_DISPLAY .= "      <select id=\"paypal_testmode\" name=\"paypal_testmode\" onchange=\"ifshow('paypal_testmode', 'on', 'sandbox-email');\">\n";
$THIS_DISPLAY .= "       <option value=\"off\">OFF</option>\n";
$THIS_DISPLAY .= "       <option value=\"on\" style=\"color: #d70000;\">ON</option>\n";
$THIS_DISPLAY .= "      </select>\n";

$THIS_DISPLAY .= "      <div id=\"sandbox-email\" style=\"display: none;\">\n";
$THIS_DISPLAY .= "       <label><em>Sandbox Merchant Email:</em></label>\n";
$THIS_DISPLAY .= "       <input type=\"text\" name=\"sandbox-email\" value=\"".$cartpref->get("sandbox-email")."\"/>\n";
$THIS_DISPLAY .= "      </div>\n";
$THIS_DISPLAY .= "      <script type=\"text/javascript\">$('paypal_testmode').value = '".$cartpref->get("paypal_testmode")."';ifshow('paypal_testmode', 'on', 'sandbox-email');</script>\n";
$THIS_DISPLAY .= "     </td>\n";
$THIS_DISPLAY .= "    </tr>\n";

// Spacer Row
$THIS_DISPLAY .= " <tr><td colspan=3>&nbsp;</td></tr>\n\n";

##---------------------------------------------------------------
//---------------------Innovative Gateway------------------------
##---------------------------------------------------------------
$THIS_DISPLAY .= " <tr>\n";
$THIS_DISPLAY .= "  <td align=\"center\" valign=\"top\">\n";
$THIS_DISPLAY .= "   <a href=\"http://www.innovativegateway.com\" target=\"_blank\"><img src=\"logo-innovative.gif\" border=\"0\"></a>\n";
$THIS_DISPLAY .= "  </td>\n";
$THIS_DISPLAY .= "  <td colspan=\"2\" align=\"left\" style=\"padding: 0px;\">\n";
$THIS_DISPLAY .= "   <table border=\"0\" cellpadding=\"4\" cellspacing=\"0\" width=\"100%\" class=\"text\">\n";
$THIS_DISPLAY .= "    <tr>\n";
$THIS_DISPLAY .= "     <td align=left colspan=\"2\" style='padding-bottom:0px;'>\n";
$THIS_DISPLAY .= "      <font color=#336699><b>".lang("Innovate Gateway Solutions")."</b></font>\n";
$THIS_DISPLAY .= "     </td>\n";
$THIS_DISPLAY .= "    </tr>\n";

// IG_user
// --------------
$THIS_DISPLAY .= "    <tr>\n";
$THIS_DISPLAY .= "     <td align=left valign=middle width=\"75\" style='padding-top:0px;'>\n";
$THIS_DISPLAY .= "      ".lang("Username").":\n";
$THIS_DISPLAY .= "     </td>\n";
$THIS_DISPLAY .= "     <td align=left style='padding-top:0px;'>\n";
$THIS_DISPLAY .= "      <input type=text name=\"IG_user\" class=\"text\" value=\"$IG_user\">\n";
$THIS_DISPLAY .= "     </td>\n";
$THIS_DISPLAY .= "    </tr>\n";

// IG_pass
// --------------
$THIS_DISPLAY .= "    <tr>\n";
$THIS_DISPLAY .= "     <td align=left valign=middle width=\"75\" style='padding-top:0px;'>\n";
$THIS_DISPLAY .= "      ".lang("Password").":\n";
$THIS_DISPLAY .= "     </td>\n";
$THIS_DISPLAY .= "     <td align=left style='padding-top:0px;'>\n";
$THIS_DISPLAY .= "      <input type=text name=\"IG_pass\" class=\"text\" value=\"$IG_pass\">\n";
$THIS_DISPLAY .= "     </td>\n";
$THIS_DISPLAY .= "    </tr>\n";

// IG_testMode
// --------------
$THIS_DISPLAY .= "    <tr>\n";
$THIS_DISPLAY .= "     <td align=left valign=middle width=\"75\" style='padding-top:0px;'>\n";
$THIS_DISPLAY .= "      ".lang("Test Mode:")."\n";
$THIS_DISPLAY .= "     </td>\n";
$THIS_DISPLAY .= "     <td align=left style='padding-top:0px;'>\n";
$THIS_DISPLAY .= "      <select name=\"IG_testMode\" style=\"color: #d70000;\">\n";

// Select current test mode setting
$igTest = "OFF;ON";
$igOpts = split(";", $igTest);

for ( $t=0; $t < 2; $t++ ) {
   $sel = "";
   if ( $igOpts[$t] == $IG_testMode ) { $sel = " selected"; }
   $THIS_DISPLAY .= "   <option value='$igOpts[$t]'$sel>$igOpts[$t]</option>\n";
}

$THIS_DISPLAY .= "      </select>\n";
$THIS_DISPLAY .= "     </td>\n";
$THIS_DISPLAY .= "    </tr>\n";

$THIS_DISPLAY .= "   </table>\n";
$THIS_DISPLAY .= "  </td>\n";
$THIS_DISPLAY .= " </tr>\n";

// Spacer Row
$THIS_DISPLAY .= " <tr><td colspan=3>&nbsp;</td></tr>\n\n";


##---------------------------------------------------------------
//---------------------VeriSign----------------------------------
##---------------------------------------------------------------
$THIS_DISPLAY .= " <tr>\n";
$THIS_DISPLAY .= "  <td align=\"center\" valign=\"top\" rowspan=\"3\">\n";
$THIS_DISPLAY .= "   <a href=\"http://www.verisign.com\" target=\"_blank\"><img src=logo-verisign.gif border=0></a>\n";
$THIS_DISPLAY .= "  </td>\n";
$THIS_DISPLAY .= "  <td align=left valign=top colspan=2 style='padding-bottom:2px;'>\n";
$THIS_DISPLAY .= "   <font color=#336699><b>VeriSign Payflow Link</b></font><br>";
$THIS_DISPLAY .= "   <font size=1>( <a href=\"#\" onclick=\"popdoc('verisign_config');\">".lang("How to configure VeriSign Payflow Link for use with your site")."</a> )</font>\n";
$THIS_DISPLAY .= "  </td>\n";
$THIS_DISPLAY .= " </tr>\n";

$THIS_DISPLAY .= " <tr>\n";
$THIS_DISPLAY .= "  <td align=left valign=middle style='padding:0px 3px 0px 5px;'>\n";
$THIS_DISPLAY .= "   ".lang("VeriSign Partner ID:")."\n";
$THIS_DISPLAY .= "  </td>\n";
$THIS_DISPLAY .= "  <td align=left valign=top style='padding:0px 5px 0px 5px;'>\n";
$THIS_DISPLAY .= "   <input type=text name=\"vpartnerid\" class=text value=\"".$PAYMENT[PAYMENT_VPARTNERID]."\">\n";
$THIS_DISPLAY .= "  </td>\n";
$THIS_DISPLAY .= " </tr>\n";

$THIS_DISPLAY .= " <tr>\n";
$THIS_DISPLAY .= "  <td align=left valign=middle style='padding:0px 5px 0px 5px;'>\n";
$THIS_DISPLAY .= "   ".lang("VeriSign Login ID:")."</td>\n";
$THIS_DISPLAY .= "  <td align=left valign=top style='padding:0px 5px 5px 5px;'>\n";
$THIS_DISPLAY .= "   <input type=text name=\"vloginid\" class=text value=\"".$PAYMENT[PAYMENT_VLOGINID]."\">\n";
$THIS_DISPLAY .= "  </td>\n";
$THIS_DISPLAY .= " </tr>\n";

//spacer row
$THIS_DISPLAY .= " <tr><td colspan=3>&nbsp;</td></tr>\n\n";

##-----------------------------------------------------
//----------------Authorize.net----------------------
##-----------------------------------------------------
$THIS_DISPLAY .= " <tr>\n";

# Gateway Logo
#-------------------------
$THIS_DISPLAY .= "  <td align=\"center\" valign=\"top\" rowspan=\"3\">\n";
$THIS_DISPLAY .= "   <a href=\"http://www.authorize.net\" target=\"_blank\"><img src=\"images/logo-authorize_net.gif\" border=\"0\"></a>\n";
$THIS_DISPLAY .= "  </td>\n";

# Authorize.Net
#-------------------------------
$THIS_DISPLAY .= "  <td align=\"left\" valign=\"top\" colspan=\"2\" style=\"padding-bottom:0px;\">\n";
$THIS_DISPLAY .= "   <font color=\"#336699\"><b>".lang("Authorize.net")."</b></font><br>\n";
$THIS_DISPLAY .= "  </td>\n";
$THIS_DISPLAY .= " </tr>\n";

$THIS_DISPLAY .= " <tr>\n";
$THIS_DISPLAY .= "  <td align=\"left\" valign=\"middle\" style=\"padding-top:0px;\">\n";
$THIS_DISPLAY .= "   ".lang("Login ID").":\n";
$THIS_DISPLAY .= "  </td>\n";
$THIS_DISPLAY .= "  <td align=\"left\" valign=\"top\" style=\"padding-top:0px;\">\n";
$THIS_DISPLAY .= "   <input type=\"text\" name=\"AN_acctid\" class=\"text\" value=\"".$AN_acctid."\">\n";
$THIS_DISPLAY .= "  </td>\n";
$THIS_DISPLAY .= " </tr>\n";

$THIS_DISPLAY .= " <tr>\n";
$THIS_DISPLAY .= "  <td align=\"left\" valign=\"middle\" style=\"padding-top:0px;\">\n";
$THIS_DISPLAY .= "   ".lang("Transaction Key").":\n";
$THIS_DISPLAY .= "  </td>\n";
$THIS_DISPLAY .= "  <td align=\"left\" valign=\"top\" style=\"padding-top:0px;\">\n";
$THIS_DISPLAY .= "   <input type=\"text\" name=\"AN_acctkey\" class=\"text\" value=\"".$AN_acctkey."\">\n";
$THIS_DISPLAY .= "  </td>\n";
$THIS_DISPLAY .= " </tr>\n";

//spacer row
$THIS_DISPLAY .= " <tr><td colspan=3>&nbsp;</td></tr>\n\n";

##-----------------------------------------------------
//----------------InternetSecure----------------------
##-----------------------------------------------------
$THIS_DISPLAY .= " <tr>\n";
# Gateway Logo
#-------------------------
$THIS_DISPLAY .= "  <td align=\"center\" valign=\"top\" rowspan=\"3\">\n";
$THIS_DISPLAY .= "   <a href=\"https://www.internetsecure.com\" target=\"_blank\"><img src=\"images/logo-internet_secure_logo.gif\" style=\"border: 1px solid #336699;\"></a>\n";
$THIS_DISPLAY .= "  </td>\n";

# Authorize.Net
#-------------------------------
$THIS_DISPLAY .= "  <td align=\"left\" valign=\"top\" colspan=\"2\" style=\"padding-bottom:0px;\">\n";
$THIS_DISPLAY .= "   <font color=\"#336699\"><b>".lang("InternetSecure")."</b></font><br>\n";
$THIS_DISPLAY .= "  </td>\n";
$THIS_DISPLAY .= " </tr>\n";

$THIS_DISPLAY .= " <tr>\n";
$THIS_DISPLAY .= "  <td align=\"left\" valign=\"middle\" style=\"padding-top:0px;\">\n";
$THIS_DISPLAY .= "   ".lang("Merchant ID").":\n";
$THIS_DISPLAY .= "  </td>\n";
$THIS_DISPLAY .= "  <td align=\"left\" valign=\"top\" style=\"padding-top:0px;\">\n";
$THIS_DISPLAY .= "   <input type=\"text\" name=\"IS_acctid\" class=\"text\" value=\"".$IS_acctid."\">\n";
$THIS_DISPLAY .= "  </td>\n";
$THIS_DISPLAY .= " </tr>\n";

$THIS_DISPLAY .= " <tr>\n";
$THIS_DISPLAY .= "  <td align=\"left\" valign=\"middle\" style=\"padding-top:0px;\">\n";
$THIS_DISPLAY .= "   ".lang("Gateway ID").":\n";
$THIS_DISPLAY .= "  </td>\n";
$THIS_DISPLAY .= "  <td align=\"left\" valign=\"top\" style=\"padding-top:0px;\">\n";
$THIS_DISPLAY .= "   <input type=\"text\" name=\"IS_acctkey\" class=\"text\" value=\"".$IS_acctkey."\">\n";
$THIS_DISPLAY .= "  </td>\n";
$THIS_DISPLAY .= " </tr>\n";

//spacer row
$THIS_DISPLAY .= " <tr><td colspan=3>&nbsp;</td></tr>\n\n";

##-----------------------------------------------------
//-------------------PayPro----------------------------
##-----------------------------------------------------
$THIS_DISPLAY .= " <tr>\n";
$THIS_DISPLAY .= "  <td align=\"center\" valign=\"top\" rowspan=2>\n";
$THIS_DISPLAY .= "   <a href=\"http://www.paypro.co.nz\" target=\"_blank\"><img src=\"logo-paypro.gif\" border=\"1\"></a>\n";
$THIS_DISPLAY .= "  </td>\n";
$THIS_DISPLAY .= "  <td align=left valign=top colspan=2 style='padding-bottom:0px;'>\n";
$THIS_DISPLAY .= "   <font color=#336699><b>PayPro Payments</b></font>\n";
$THIS_DISPLAY .= "  </td>\n";
$THIS_DISPLAY .= " </TR>\n";

$THIS_DISPLAY .= " <tr>\n";
$THIS_DISPLAY .= "  <td align=left valign=middle width=125 style='padding-top:0px;'>\n";
$THIS_DISPLAY .= "   ".lang("PayPro Merchant ID:")."\n";
$THIS_DISPLAY .= "  </td>\n";
$THIS_DISPLAY .= "  <td align=left valign=top style='padding-top:0px;'>\n";
$THIS_DISPLAY .= "   <input type=text name=PAYPRO class=text value=\"".$PAYPRO."\" style='width: 200px;'>\n";
$THIS_DISPLAY .= "  </td>\n";
$THIS_DISPLAY .= " </tr>\n";

// Spacer Row
$THIS_DISPLAY .= " <tr><td colspan=3>&nbsp;</td></tr>\n\n";

$EWAYCK1 = "";
$EWAYCK2 = "";
if (eregi("EWAYONSITE", $geteway[EWAY_USER1])) { $EWAYCK1 = "checked"; }
if (eregi("EWAYATEWAY", $geteway[EWAY_USER2])) { $EWAYCK2 = "checked"; }

##-----------------------------------------------------
//---------------- eWAY Payments ----------------------
##-----------------------------------------------------
$THIS_DISPLAY .= " <tr>\n";

# Gateway Logo
#-------------------------
$THIS_DISPLAY .= "  <td align=\"center\" valign=\"top\" rowspan=\"4\">\n";
$THIS_DISPLAY .= "   <a href=\"http://www.eway.com.au\" target=\"_blank\"><img src=\"images/logo-eway.gif\" border=\"0\"></a>\n";
$THIS_DISPLAY .= "  </td>\n";

# eWay Payments
#-------------------------------
$THIS_DISPLAY .= "  <td align=\"left\" valign=\"top\" colspan=\"2\" style=\"padding-bottom:0px;\">\n";
$THIS_DISPLAY .= "   <font color=\"#336699\"><b>".lang("eWay Payments (Australian businesses only)")."</b></font><br>\n";
$THIS_DISPLAY .= "  </td>\n";
$THIS_DISPLAY .= " </tr>\n";

$THIS_DISPLAY .= " <tr>\n";
$THIS_DISPLAY .= "  <td align=\"left\" valign=\"middle\" style=\"padding-top:0px;\">\n";
$THIS_DISPLAY .= "   ".lang("Account ID").":\n";
$THIS_DISPLAY .= "  </td>\n";
$THIS_DISPLAY .= "  <td align=\"left\" valign=\"top\" style=\"padding-top:0px;\">\n";
$THIS_DISPLAY .= "   <input type=\"text\" name=\"EWAY\" class=\"text\" value=\"".$EWAY."\">\n";
$THIS_DISPLAY .= "  </td>\n";
$THIS_DISPLAY .= " </tr>\n";

$THIS_DISPLAY .= " <tr>\n";
$THIS_DISPLAY .= "  <td colspan=\"2\" align=\"left\" valign=\"middle\" style=\"padding-top:0px;\">\n";
$THIS_DISPLAY .= "   <table border=\"0\" cellspacing=\"0\" cellpadding=\"2\" class=\"text\">\n";
$THIS_DISPLAY .= "    <tr>\n";
$THIS_DISPLAY .= "     <td><input type=\"radio\" id=\"EWAYONSITE\" name=\"EWAYONSITE\" class=\"text\" value=\"EWAYONSITE\" onClick=\"checkMe();\" $EWAYCK1></td>\n";
$THIS_DISPLAY .= "     <td>Process payment live on website (SSL certificate required) </td>\n";
$THIS_DISPLAY .= "    </tr>\n";
$THIS_DISPLAY .= "    <tr>\n";
$THIS_DISPLAY .= "     <td><input type=\"radio\" id=\"EWAYATEWAY\" name=\"EWAYATEWAY\" class=\"text\" value=\"EWAYATEWAY\" onClick=\"checkMeTo();\" $EWAYCK2></td>\n";
$THIS_DISPLAY .= "     <td>Direct customer to eWay for payment.</td>\n";
$THIS_DISPLAY .= "    </tr>\n";
$THIS_DISPLAY .= "   </table>\n";
$THIS_DISPLAY .= "  </td>\n";
$THIS_DISPLAY .= " </tr>\n";

//spacer row
$THIS_DISPLAY .= " <tr><td colspan=3>&nbsp;</td></tr>\n\n";

##-----------------------------------------------------
//----------------Payment Express----------------------
##-----------------------------------------------------
$THIS_DISPLAY .= " <tr>\n";

# Gateway Logo
#-------------------------
$THIS_DISPLAY .= "  <td align=\"center\" valign=\"top\" rowspan=\"4\">\n";
$THIS_DISPLAY .= "   <a name=\"payments-express\"></a>\n";
$THIS_DISPLAY .= "   <a href=\"https://www.paymentexpress.com\" target=\"_blank\"><img src=\"images/logo-dps.gif\" border=\"0\"></a>\n";
$THIS_DISPLAY .= "  </td>\n";

# Payment Express
#-------------------------------
$THIS_DISPLAY .= "  <td align=\"left\" valign=\"top\" colspan=\"2\" style=\"padding-bottom:0px;\">\n";
$THIS_DISPLAY .= "   <font color=\"#336699\"><b>".lang("Payments Express")."</b></font><br>\n";
$THIS_DISPLAY .= "  </td>\n";
$THIS_DISPLAY .= " </tr>\n";

$THIS_DISPLAY .= " <tr>\n";
$THIS_DISPLAY .= "  <td align=\"left\" valign=\"middle\" style=\"padding-top:0px;\">\n";
$THIS_DISPLAY .= "   ".lang("Username").":\n";
$THIS_DISPLAY .= "  </td>\n";
$THIS_DISPLAY .= "  <td align=\"left\" valign=\"top\" style=\"padding-top:0px;\">\n";
$THIS_DISPLAY .= "   <input type=\"text\" name=\"DPS_USERNAME\" class=\"text\" value=\"".$DPS_USERNAME."\">\n";
$THIS_DISPLAY .= "  </td>\n";
$THIS_DISPLAY .= " </tr>\n";

$THIS_DISPLAY .= " <tr>\n";
$THIS_DISPLAY .= "  <td align=\"left\" valign=\"middle\" style=\"padding-top:0px;\">\n";
$THIS_DISPLAY .= "   ".lang("Password").":\n";
$THIS_DISPLAY .= "  </td>\n";
$THIS_DISPLAY .= "  <td align=\"left\" valign=\"top\" style=\"padding-top:0px;\">\n";
$THIS_DISPLAY .= "   <input type=\"text\" name=\"DPS_PASSWORD\" class=\"text\" value=\"".$DPS_PASSWORD."\">\n";
$THIS_DISPLAY .= "  </td>\n";
$THIS_DISPLAY .= " </tr>\n";

$THIS_DISPLAY .= " <tr>\n";
$THIS_DISPLAY .= "  <td align=\"left\" valign=\"middle\" style=\"padding-top:0px;\">\n";
$THIS_DISPLAY .= "   ".lang("DPS Logo Display").":\n";
$THIS_DISPLAY .= "  </td>\n";
$THIS_DISPLAY .= "  <td align=\"left\" valign=\"top\" style=\"padding-top:0px;\">\n";
if ( $cartpref->get("dps-logo-display") == "" ) { $cartpref->set("dps-logo-display", "check"); }
$THIS_DISPLAY .= "   <select name=\"dps-logo-display\" id=\"dps-logo-display\">\n";
$THIS_DISPLAY .= "    <option value=\"white\">White Background (default)</option>\n";
$THIS_DISPLAY .= "    <option value=\"transparent\">Transparent Background</option>\n";
$THIS_DISPLAY .= "   </select>\n";
$THIS_DISPLAY .= "   <script type=\"text/javascript\">$('dps-logo-display').value = '".$cartpref->get("dps-logo-display")."';</script>\n";
$THIS_DISPLAY .= "  </td>\n";
$THIS_DISPLAY .= " </tr>\n";

# spacer row
$THIS_DISPLAY .= " <tr><td colspan=3>&nbsp;</td></tr>\n\n";

##-----------------------------------------------------
//----------------Paystation----------------------
##-----------------------------------------------------
$THIS_DISPLAY .= " <tr>\n";

# Gateway Logo
$THIS_DISPLAY .= "  <td align=\"center\" valign=\"top\" rowspan=\"2\">\n";
$THIS_DISPLAY .= "   <a href=\"https://www.paystation.com\" target=\"_blank\"><img src=\"images/logo-paystation.jpg\" border=\"0\"></a>\n";
$THIS_DISPLAY .= "  </td>\n";

# Paystation
$THIS_DISPLAY .= "  <td align=\"left\" valign=\"top\" colspan=\"2\" style=\"padding-bottom:0px;\">\n";
$THIS_DISPLAY .= "   <font color=\"#336699\"><b>".lang("Paystation")."</b></font><br>\n";
$THIS_DISPLAY .= "   <font size=1>( <a href=\"#\" onclick=\"popdoc('paystation_config');\">".lang("How to configure Paystation for use with your site")."</a> )</font>\n";
$THIS_DISPLAY .= "  </td>\n";
$THIS_DISPLAY .= " </tr>\n";

$THIS_DISPLAY .= " <tr>\n";
$THIS_DISPLAY .= "  <td align=\"left\" valign=\"middle\" style=\"padding-top:0px;\">\n";
$THIS_DISPLAY .= "   ".lang("Paystation ID:").":\n";
$THIS_DISPLAY .= "  </td>\n";
$THIS_DISPLAY .= "  <td align=\"left\" valign=\"top\" style=\"padding-top:0px;\">\n";
$THIS_DISPLAY .= "   <input type=\"text\" name=\"PAYSTATION_ID\" class=\"text\" value=\"".$PAYSTATION_ID."\">\n";
$THIS_DISPLAY .= "  </td>\n";
$THIS_DISPLAY .= " </tr>\n";


# spacer row
$THIS_DISPLAY .= " <tr><td colspan=3>&nbsp;</td></tr>\n\n";


eval(hook("payment_options.php:config_form"));

$THIS_DISPLAY .= "</table>\n\n";


###
  #=================================================================================================
 ##  If using credit card processing, select which cards you will accept:
  #=================================================================================================
###

// Select current settings
$one = ""; $two = ""; $three = ""; $four = "";
if (eregi("Visa", $PAYMENT[PAYMENT_CREDIT_CARDS])) { $one = "checked"; }
if (eregi("Mastercard", $PAYMENT[PAYMENT_CREDIT_CARDS])) { $two = "checked"; }
if (eregi("Amex", $PAYMENT[PAYMENT_CREDIT_CARDS])) { $three = "checked"; }
if (eregi("Discover", $PAYMENT[PAYMENT_CREDIT_CARDS])) { $four = "checked"; }

# help-accepted_cards
$popup = "";
$popup .= "<h2>On-site credit card processing (i.e. Authorize.net)</h2>\n";
$popup .= "<p>If you are going to process credit cards directly on <i>your</i> website (i.e. via Authorize.net, Innovative Gateway, Offline Credit, etc. &mdash; see step #1 above),\n";
$popup .= "then make sure you check <i>at least one</i> of these.</p>\n";

$popup .= "<h2>Off-site credit card processing (i.e. PayPal)</h2>\n";
$popup .= "<p>If you are <i>not</i> processing credit cards directly on your website, \n";
$popup .= "then this option is really more for display purposes. It has little to no technical affect on your checkout process.</p>\n";
$popup .= "<p>As in, you could be using PayPal to proccess all payments, but still check these boxes\n";
$popup .= "to make the credit card images display on your site under an \"accepted credit cards\" headline (i.e. in the search column on checkout pages).\n";
$popup .= "Even though technically <i>you</i> accept \n";
$popup .= "<i>PayPal</i> and <i>PayPal</i> accepts the credit cards, the bottom line is that people can still pay for your products using those credit cards (albiet indirectly through PayPal),\n";
$popup .= " and displaying that you accept the major cards can help reassure those expecting to pay with credit card even if they might be new to PayPal.</p>\n";
$THIS_DISPLAY .= help_popup("help-accepted_cards", "Accepted Credit Cards", $popup, "top: 250%;width: 550px;");

#card_types
$THIS_DISPLAY .= "<a name=\"card_types\"></a>\n";
$THIS_DISPLAY .= "<h2>3. ".lang("If using credit card processing, select which cards you will accept")."\n";
$THIS_DISPLAY .= "<span class=\"help_link\" onclick=\"toggleid('help-accepted_cards');\">[?]</span></h2>\n";
$THIS_DISPLAY .= "<table border=\"0\" cellpadding=\"4\" cellspacing=\"0\" class=\"feature_sub\">\n";
$THIS_DISPLAY .= " <tr>\n";
$THIS_DISPLAY .= "  <td align=\"center\" valign=\"middle\">\n";
$THIS_DISPLAY .= "   <input type=checkbox name=visa value=1 $one><img src='images/visa.gif' hspace=2 vspace=2 border=0 align=absmiddle>&nbsp;&nbsp;&nbsp;&nbsp;\n";
$THIS_DISPLAY .= "   <input type=checkbox name=mastercard value=1 $two><img src='images/mastercard.gif' hspace=2 vspace=2 border=0 align=absmiddle>&nbsp;&nbsp;&nbsp;&nbsp;\n";
$THIS_DISPLAY .= "   <input type=checkbox name=amex value=1 $three><img src='images/amex.gif' hspace=2 vspace=2 border=0 align=absmiddle>&nbsp;&nbsp;&nbsp;&nbsp;\n";
$THIS_DISPLAY .= "   <input type=checkbox name=discover value=1 $four><img src='images/discover.gif' hspace=2 vspace=2 border=0 align=absmiddle>\n";
$THIS_DISPLAY .= "  </td>\n";
$THIS_DISPLAY .= " </tr>\n";
$THIS_DISPLAY .= "</table>\n\n";


# #
# #=================================================================================================
###   Choose Currency Type and Symbol
  #=================================================================================================
  #

#currency
$THIS_DISPLAY .= "<a name=\"currency\"></a>\n";
$THIS_DISPLAY .= "<h2>4. ".lang("Choose Currency Type and Symbol")."</h2>\n";
$THIS_DISPLAY .= "<table border=\"0\" cellpadding=\"4\" cellspacing=\"0\" class=\"feature_sub\" width=\"75%\">\n";

# Currency Type
$THIS_DISPLAY .= "<tr>\n";
$THIS_DISPLAY .= " <td align=left class=text>\n";
$THIS_DISPLAY .= "  ".lang("Currency Type:")."\n";
$THIS_DISPLAY .= " </td>\n";
$THIS_DISPLAY .= " <td align=left class=text>\n";
$THIS_DISPLAY .= "  <select name='cash_type'>\n";

//Build Currency Type Options
//--------------------------------
$dSign[0] = "$";
$dAbrv[0] = "----";
$n = 1;
for ($d=0; $d < $cNum; $d++) {
   $sel = "";
   $dTmp = split("::", $cMoney[$d]);

   //Select current setting
   if ($dTmp[0] == $PAYMENT[PAYMENT_CURRENCY_TYPE]) {$sel = "selected";}

   if ($dTmp[0] != "altsym") {
      $THIS_DISPLAY .= "   <option value=\"".$dTmp[0]."\" ".$sel.">".$dTmp[1]." (".$dTmp[0].")</option>\n";

   }

   if ($dTmp[2] != "$" && $dTmp[2] != "none") {
      $dSign[$n] = $dTmp[2]." ";
      $dAbrv[$n] = $dTmp[0]." ";
      $n++;
   }
}
$THIS_DISPLAY .= "  </select>\n";
$THIS_DISPLAY .= " </td>\n";

// Spacer Cell
$THIS_DISPLAY .= " <td width=\"5%\">&nbsp;</td>\n";

// Currency Symbol
$THIS_DISPLAY .= " <td align=left class=\"text\">\n";
$THIS_DISPLAY .= "  ".lang("Currency Symbol:")."\n";
$THIS_DISPLAY .= " </td>\n";
$THIS_DISPLAY .= " <td align=left class=\"text\">\n";
$THIS_DISPLAY .= "  <select name='cash_sign'>\n";


//Build Currency Symbols
//--------------------------------
$numSigns = count($dSign);
for ($s=0; $s < $numSigns; $s++) {
   $sel = "";
   $dSign[$s] = eregi_replace(" ", "", $dSign[$s]);
   $dSign[$s] = eregi_replace("\n", "", $dSign[$s]);

   if ( trim($dSign[$s]) == trim($PAYMENT['PAYMENT_CURRENCY_SIGN']) ) {$sel = " selected";}

   if (trim($dSign[$s]) != "\$") {
      $THIS_DISPLAY .= "   <option value=\"".trim($dSign[$s])."\"".$sel.">".trim($dSign[$s])."</option>\n";

   } elseif ($dSign[$s] == "\$") {
      $THIS_DISPLAY .= "   <option value=\"\$\"".$sel.">".trim($dSign[$s])."</option>\n";
   }
}

for ($s=0; $s < $numSigns; $s++) {
   $sel = "";
   if (trim($dAbrv[$s]) == trim($PAYMENT[PAYMENT_CURRENCY_SIGN])) {$sel = " selected";}

   if (trim($dAbrv[$s]) != "----" && trim($dAbrv[$s]) != "altsym") {
      $THIS_DISPLAY .= "   <option value=\"".trim($dAbrv[$s])."\"".$sel.">".trim($dAbrv[$s])."</option>\n";
   } elseif (trim($dAbrv[$s]) == "----") {
      $THIS_DISPLAY .= "   <option value='\$'".$sel.">".trim($dAbrv[$s])."</option>\n";
   }
}

$THIS_DISPLAY .= "   </select>\n";
$THIS_DISPLAY .= "  </td>\n";

// Spacer Cell
$THIS_DISPLAY .= " <td width=\"7%\">&nbsp;</td>\n";

$THIS_DISPLAY .= " </tr>\n";


$THIS_DISPLAY .= "</TABLE>\n\n";



######################################################
### READ ANY .INC or .PHP FILES INTO MEMORY; AT THIS
### POINT THERE IS NO SECURITY ON THESE FILES BECAUSE
### THEY SIMPLY EXIST ON THE SERVER IN A STANDARD
### PORT :80 ACCESSIBLE DIRECTORY.  IF SOMEONE WANTED
### TO MODIFY UPLOAD FILES TO PLACE FILES INTO A
### DATABASE; THAT WOULD SOLVE THAT PROBLEM.
### I JUST DID NOT DO IT IN THE INITIAL DESIGN.
###
### WHILE WE'RE HERE, MIGHT AS WELL POPULATE THE
### SELECTION BOX FOR CUSTOM FORM ATTACHMENT AS WELL.
######################################################

$inc_file = "     <OPTION VALUE=\"\">N/A</OPTION>\n";

$count = 0;
$directory = "$doc_root/media";
if (is_dir($directory)) {
$handle = opendir("$directory");
	while ($files = readdir($handle)) {
		if (strlen($files) > 2) {
			if (eregi(".inc", $files) || eregi(".php", $files)) {
				$count++;
				$tmp = "$directory/$files";
				$tmp_space = filesize($tmp);
				$tmp_srt = ucwords($files);
				$site_file[$count] = $tmp_srt . "~~~media~~~$tmp_space~~~" . $files;
			}
		}
	}
closedir($handle);
}

if ($count > 1) { sort($site_file); };
$file_count = count($site_file);

for ($x=0;$x<=$file_count;$x++) {

		$tmp = split("~~~", $site_file[$x]);
		$filename = $tmp[3];
		$filesize = $tmp[2];
		$filedir = $tmp[1];

		if (strlen($filename) > 2) {

			// -----------------------------------------
			// Calculate "Human" Filesize for display
			// -----------------------------------------

			if ($filesize >= 1048576) {
				$filesize = round($filesize/1048576*100)/100;
				$filesize = $filesize . "&nbsp;Mb";
			 } elseif ($filesize >= 1024) {
				$filesize = round($filesize/1024*100)/100;
				$filesize = $filesize . "&nbsp;K";
			 } else {
				$filesize = $filesize . "&nbsp;Bytes";
			 }

			$inc_file .= "     <OPTION VALUE=\"$filename\">$filename [$filesize]</OPTION>\n";

		}

}

# ssl_cert
$THIS_DISPLAY .= "<BR><BR>\n\n";
$THIS_DISPLAY .= "<a name=\"ssl_cert\"></a>\n";
$THIS_DISPLAY .= "<TABLE BORDER=0 CELLPADDING=5 CELLSPACING=0 class=\"feature_sub\" width=\"75%\">\n";
$THIS_DISPLAY .= "<TR><TD align=left valign=middle>\n\n";
$THIS_DISPLAY .= "<B>5. ".lang("I am using an SSL Certificate with my web site and when going to the checkout");
$THIS_DISPLAY .= " ".lang("the following https:// call should be made to the scripts");
$THIS_DISPLAY .= " ".lang("to invoke the SSL Cert.")."<BR>";
$THIS_DISPLAY .= "</TD></TR><TR><TD align=center valign=middle>\n";
$THIS_DISPLAY .= "Full HTTPS call: <input type=text name=PAYMENT_SSL class=text value=\"$PAYMENT[PAYMENT_SSL]\" style='width: 300px;'><BR><BR>\n";
$THIS_DISPLAY .= "<p class=\"note\" style=\"margin-top: 0;font-size: 90%;color: #888c8e;text-align: left;\">\n";
$THIS_DISPLAY .= "".lang("For most certificates, this is simply your domain name with 'https://' instead of 'http://' at the beginning")."\n";
$THIS_DISPLAY .= " (i.e. https://".$_SESSION['docroot_url'].").\n";
$THIS_DISPLAY .= "Or in other cases your certificate url might run off of another shared domain, like 'https://secure.example.com/~".$_SESSION['docroot_url']."'.\n";
$THIS_DISPLAY .= "If you are unsure what to put in this field, check with the company/person/website that you got the SSL certificate from.";
$THIS_DISPLAY .= lang("Note: DO NOT ADD ANY TRAILING FORWARD SLASHES to the url that you put in this box.")."\n";
$THIS_DISPLAY .= "</p>\n";
$THIS_DISPLAY .= "</TD></TR></TABLE>\n\n";
$THIS_DISPLAY .= "<BR><BR>\n\n";


# gateway_include
$THIS_DISPLAY .= "<br/><br/>\n";
$THIS_DISPLAY .= "<a name=\"gateway_include\"></a>\n";
$THIS_DISPLAY .= "<table border=\"0\" cellpadding=\"5\" cellspacing=\"0\" class=\"feature_sub\" width=\"75%\">\n";
$THIS_DISPLAY .= "<TR><TD align=left valign=middle>\n\n";
$THIS_DISPLAY .= "<b>6. ".lang("I want to use online processing but I have a custom PHP include payment gateway ");
$THIS_DISPLAY .= lang("system that I want to use in place of the others listed").".</b>\n";
$THIS_DISPLAY .= "</TD></TR><TR><TD align=center valign=middle>\n";
$THIS_DISPLAY .= "Custom Include File: <SELECT NAME=\"GATEWAY_INCLUDE\" CLASS=text>$inc_file</SELECT><BR><BR>\n";
$THIS_DISPLAY .= "<CENTER><FONT COLOR=#999999>".lang("This will over-ride all processing for credit cards.")."\n";
$THIS_DISPLAY .= "</TD></TR></TABLE>\n\n";
$THIS_DISPLAY .= "<BR><BR>\n\n";

# invoice_include
$THIS_DISPLAY .= "<BR><BR>\n\n";
$THIS_DISPLAY .= "<a name=\"custom_invoice_include\"></a>\n";
$THIS_DISPLAY .= "<TABLE BORDER=0 CELLPADDING=5 CELLSPACING=0 class=\"feature_sub\" width=75%>\n";
$THIS_DISPLAY .= "<TR><TD align=left valign=middle>\n\n";
$THIS_DISPLAY .= "<B>7. ".lang("When displaying the final invoice to my customer, I want to execute a custom PHP include");
$THIS_DISPLAY .= " ".lang("that processes data when the invoice is displayed.");
$THIS_DISPLAY .= "</TD></TR><TR><TD align=center valign=middle>\n";
$THIS_DISPLAY .= lang("Custom Include File:")." <SELECT NAME=\"INVOICE_INCLUDE\" CLASS=text>$inc_file</SELECT><BR><BR>\n";
$THIS_DISPLAY .= "<CENTER><FONT COLOR=#999999>".lang("This include can be used to create custom processes that execute after products have been purchased from your system.")." \n";
$THIS_DISPLAY .= "".lang("For example, you may wish to assign a new user automatically with a generated username and password to the Secure Users table after a membership payment.")."\n";
$THIS_DISPLAY .= "</TD></TR></TABLE>\n\n";
$THIS_DISPLAY .= "<BR><BR>\n\n";

####################################################################

$THIS_DISPLAY .= "<div class=\"center\"><INPUT class=\"btn_save\" TYPE=SUBMIT VALUE=\"".lang("Save Payment Options")."\" onmouseover=\"this.className='btn_saveon';\" onmouseout=\"this.className='btn_save';\"></div>\n";
$THIS_DISPLAY .= "</FORM>\n\n";

echo $THIS_DISPLAY;

echo "<script language=\"javascript\">\n";
echo "document.PAY.cash_sign.value = '".$PAYMENT['PAYMENT_CURRENCY_SIGN']."';\n";
echo "</script>\n";

if ($PAYMENT[PAYMENT_INCLUDE] != "" ) {

	echo "<SCRIPT LANGUAGE=\"javascript\">\n\n";
	echo "      document.PAY.GATEWAY_INCLUDE.value = '$PAYMENT[PAYMENT_INCLUDE]';\n";
	echo "      document.PAY.INVOICE_INCLUDE.value = '$PAYMENT[INVOICE_INCLUDE]';\n";
	echo "</SCRIPT>\n\n";

}

if ($PAYMENT[INVOICE_INCLUDE] != "" ) {

	echo "<SCRIPT LANGUAGE=\"javascript\">\n\n";
	echo "      document.PAY.INVOICE_INCLUDE.value = '$PAYMENT[INVOICE_INCLUDE]';\n";
	echo "</SCRIPT>\n\n";

}

####################################################################

# Grab module html into container var
$module_html = ob_get_contents();
ob_end_clean();

$module = new smt_module($module_html);
$module->add_breadcrumb_link("Shopping Cart Menu", "program/modules/mods_full/shopping_cart.php");
$module->add_breadcrumb_link("Payment Options", "program/modules/mods_full/shopping_cart/payment_options.php");
$module->icon_img = "skins/".$_SESSION['skin']."/icons/shopping_cart-enabled.gif";
$module->heading_text = "Payment Options";
$module->description_text = "How will your customers pay for items they purchase from your online store?";
$module->good_to_go();

?>