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

// All this garbage is here to screw up hack jobs that may try
// to crack the encryption script.
//
// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! WARNING !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
//
// REMEMBER: THIS DOES NOT MAKE THE CREDIT CARD INFORMATION SAFE AND IS A BASIC ENCRYPTION
// ROUTINE THAT IS EASILY BROKEN. SOHOLAUNCH IN NO WAY GUARANTEES THE SECURITY USING THIS
// SYSTEM. IT IS SIMPLY HERE AS A VISUAL ASSURANCE MEASURE FOR YOUR CUSTOMER.  THE ONLY WAY
// TO INSURE TOTAL SECURITY IS TO USE A 3RD PARTY CREDIT CARD PROCESSING GATEWAY SYSTEM WITH
// SSL CERTS INSTALLED. THE CODE JUST BELOW THIS PARAGRAPH AND THE JAVASCRIPT CODE SHOULD NOT
// BE MODIFIED.  THE ECHO STATEMENTS ARE THERE TO SIMPLY KEEP "SOURCE CODE VIEWERS" FROM
// TAKING A QUICK PEAK AT THE JAVASCRIPT ROUTINE AND IS DESIGNED TO THROW THEM OFF TRACK.
// HOWEVER, IF THEY DO FIND THE ROUTINE, YOUR CREDIT CARD DATA IS STILL STORED IN YOUR
// DATABASE TABLE IN ENCRYPTED MODE -- MAKE SURE THAT THING IS SECURE!
//

// AGAIN, THIS IS NOTHING MORE THAN VERY, VERY, VERY BASIC ENCRYPTION AND IT IS NOT GUARANTEED
// TO BE EVEN 50% FOOL-PROOF.
//
// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

if(!function_exists('hascurl')) {
   function hascurl() {
      if ( !function_exists("curl_setopt") ) {
         return false;
      } else {
         return true;
      }
   }
}


/*---------------------------------------------------------------------------------------------------------*
    ____                                          ______                  __
   / __ \ _____ ____   _____ ___   _____ _____   / ____/____ _ _____ ____/ /
  / /_/ // ___// __ \ / ___// _ \ / ___// ___/  / /    / __ `// ___// __  /
 / ____// /   / /_/ // /__ /  __/(__  )(__  )  / /___ / /_/ // /   / /_/ /
/_/    /_/    \____/ \___/ \___//____//____/   \____/ \__,_//_/    \__,_/

/*---------------------------------------------------------------------------------------------------------*/
if ( $do == "chargeit" ) {

   $result = mysql_query("SELECT * FROM cart_paypoint");
   $PAYPOINT = mysql_fetch_array($result);

   # Collect and format involved data
   #===================================================================
   # Target url and script
   $scHost = "trans.atsbank.com";
   $scPath = "/cgi-bin/process.cgi";

   # Data to pass to gateway
   $scData = array();
   $scData['action'] = "ns_quicksale_cc";
   //$scData['acctid'] = "test0";
   $scData['acctid'] = $PAYPOINT['SC_ACCTID'];
   $scData['amount'] = $TOTAL_SALE;
   $scData['ccname'] = $CC_NAME;
   $scData['ccnum'] = trim($CC_NUM);
   $scData['expmon'] = $CC_MON;
   $scData['expyear'] = $CC_YEAR;
   $scData['cvv2'] = $CC_AVS;
   $scData['ci_billaddr1'] = $_POST['caddy1'];
   $scData['ci_billzip'] = $_POST['czip'];

   # Test mode values
//   $scData['ccnum'] = "5454545454545454";
//   $scData['acctid'] = "invalidacc";
//   $scData['expyear'] = "2003";

   // Include socket connection class and instantiate object
   //::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
   $fsock_class = $_SESSION['docroot_path']."/sohoadmin/program/includes/remote_actions/class-fsockit.php";
   if ( !include($fsock_class) ) {
      echo lang("Error").": ".lang("Unable to include fsock gateway connect routine")."!";
      exit;
   }

   # New socket connection object
   $scSocket = new fsockit($scHost, $scPath, $scData);

   // Connect and grab result array
   if ( hascurl() ) {
      $scRez = $scSocket->curlput();
   } else {
      $scRez = $scSocket->sockput();
   }

   //echo $scRez['raw']; exit;

   # Format result array returned from sockput() method
   #=====================================================================
   # Result line needle (trans result line starts with this)
   $rezLine = "<html><body><plaintext>";

   # Split response into line-by-line array
   $scLines = split("\n", $scRez['raw']);

   // Loop through lines and find the actual transaction result
   //=================================================================
   foreach ( $scLines as $line=>$txt ) {
      if ( eregi($rezLine, $txt) ) {
         $goodline = $line + 1;
      }

      if ( $goodline != "" ) {
         $scResult = $scLines[$goodline];
         $scResult = trim($scResult); // Declined=Invalid Expiration Date
         $scResult = split("=", $scResult); // [0]='Declined' | [1]='Invalid Expiration Date'
      }
   }

   # Was transaction declined?
   if ( strtoupper($scResult[0]) != "ACCEPTED" ) {

      # Declined: show error message a cc form
      echo "<div align=\"center\" style=\"border: 1px solid red; background-color: #F7DFDF;\" class=\"text\"><br>\n";
      echo " ".lang("Unable to complete transaction").". ".lang("Your credit card has not been charged").".<br>";
      echo " ".lang("Error").": <b>".$scResult[1]."</b><br><br>\n";
      echo "</div><br>\n";

   } else {
      # Accepted: show final invoice & 'thank you'
      include("pgm-show_invoice.php");
      exit;
   }

}


echo "<!-- END SYSTEM / START FRAME PULL -->\n";

for ($x=1;$x<=2500;$x++) {
	echo "\n\n";
}

echo "<!-- END SYSTEM -->\n";

?>

<script language="javascript">

	var astring=":aAb`BcVCd/eXDfEYg FZhi?jGk|HlmI,nJo@TKpqL.WMrsNt!uvwOx<yPz>0QR12~3S4;^567U89%$#*()-_=+��������������������ܢ��������Ѫ���������'";

	function encrypt(lstring){
     		retstr=""
     		for (var i=0;i<lstring.length;i++){
         		aNum=astring.indexOf(lstring.substring(i,i+1),0)
         		aNum=aNum^25
         		retstr=retstr+astring.substring(aNum,aNum+1)}
     		return retstr
	}

	function onClick(){

		var check = 0;

		if (document.INNOVCARD.CC_NAME.value == "") { check = 1; }
		if (document.INNOVCARD.CC_TYPE.value == "") { check = 1; }
		if (document.INNOVCARD.CC_NUM.value == "") { check = 1; }
		if (document.INNOVCARD.CC_AVS.value == "") { check = 1; }

		if (check != 1) {

			document.INNOVCARD.submit();

		} else {

			alert("".lang("YOU DID NOT COMPLETE ALL REQUIRED FIELDS").".\n".lang("PLEASE MAKE CORRECTIONS BEFORE CONTINUING").".");

		}

    	}

</script>
<style>
.cctext {
   font-family: Courier New, Courier, mono;
   /* font-family: Verdna, Arial, Helvetica, Sans-serif; */
   font-size: 12px;
   color: #2E2E2E;
   letter-spacing: 2px;
   padding-left: 2px;
}
</style>
<form name="pay_paypoint" method="post" action="pgm-payment_gateway.php">
<input type="hidden" name="PAYPOINT_FLAG" value="1">
<input type="hidden" name="PAY_TYPE" value="PAYPOINT">
<input type="hidden" name="do" value="chargeit">
<input type="hidden" name="ORDER_NUMBER" value="<? echo $ORDER_NUMBER; ?>">

<!---#####################################################--->
<!---        Required Info for Gateway Function           --->
<!---#####################################################--->

<!---TOTAL_SALE--->
<input type="hidden" name="TOTAL_SALE" value="<? echo $ORDER_TOTAL; ?>">
<!---caddy1--->
<input type="hidden" name="caddy1" value="<? echo $BADDRESS1; ?>">
<!---caddy2--->
<input type="hidden" name="caddy2" value="<? echo $BADDRESS2; ?>">
<!---ccity--->
<input type="hidden" name="ccity" value="<? echo $BCITY; ?>">
<!---cstate--->
<input type="hidden" name="cstate" value="<? echo $BSTATE; ?>">
<!---czip--->
<input type="hidden" name="czip" value="<? echo $BZIPCODE; ?>">
<!---ccountry--->
<input type="hidden" name="ccountry" value="<? echo $BCOUNTRY; ?>">
<!---cphone--->
<input type="hidden" name="cphone" value="<? echo $BPHONE; ?>">
<!---cemail--->
<input type="hidden" name="cemail" value="<? echo $BEMAILADDRESS; ?>">

<table border="0" cellpadding="0" cellspacing="0" width="100%">
 <tr>
  <td align="center" valign="top">
   <table border="0" cellspacing="0" cellpadding="5"  style='border: 1px solid black;' align="center" bgcolor="#<? echo $OPTIONS[DISPLAY_CARTBG]; ?>">
    <tr>
     <td colspan="2" class="text" align="left" bgcolor="#<? echo $OPTIONS[DISPLAY_HEADERBG]; ?>">
      &nbsp;
     </td>
    </tr>

    <tr>
     <td colspan="2" class="text" align="left">
      <font color="red">
      <? echo lang("The total amount of your purchase"); ?>,
      $<? echo $ORDER_TOTAL; ?>,
      <? echo lang("will be charged to your credit card."); ?>
      </font>
     </td>
    </tr>

    <!---CC_NAME--->
    <tr>
     <td align="left" valign="top" class="text" width="30%">
      <? echo lang("Name as it appears on card"); ?>:
     </td>
     <td align="left" valign="top" class="text" width="70%">
      <input type="text" name="CC_NAME" class="cctext" style='width: 250px;' value="<? echo $BFIRSTNAME." ".$BLASTNAME; ?>">
     </td>
    </tr>

    <!--- CC_TYPE --->
    <tr>
     <td class="text">
      <? echo lang("Credit Card Type"); ?>:
     </td>
     <td class="text">
      <select name="CC_TYPE" class="cctext">

   	<?

   	$tmp = split(";", $OPTIONS[PAYMENT_CREDIT_CARDS]);
   	$tmp_cnt = count($tmp);

   	for ($x=0;$x<=$tmp_cnt;$x++) {
   		if ($tmp[$x] != "") {
   			echo "<OPTION VALUE=\"$tmp[$x]\">$tmp[$x]</OPTION>\n";
   		}
   	}

   	?>
      </select>
     </td>
    </tr>

    <!---CC_NUM--->
    <tr>
     <td class="text">
      <? echo lang("Credit Card Number"); ?>:
     </td>
     <td class="text">
      <input type="text" name="CC_NUM" class="cctext" style='width: 250px;'>
     </td>
    </tr>

    <tr>
     <td class="text">
      <? echo lang("Credit Card Expiration Date"); ?>:
     </td>
     <td class="text">
      <? echo lang("Month"); ?>:

      <!---CC_MON--->
      <select name="CC_MON" class="cctext">
   	<?

   	$this_month = date("m");

   	for ($x=1;$x<=12;$x++) {
   		$show = $x;
   		if ($x < 10) { $show = "0".$x; }
   		if ($show == $this_month) { $SEL = "SELECTED"; } else { $SEL = ""; }
   		echo "<OPTION VALUE=\"$show\" $SEL>$show</OPTION>\n";
   	}

   	?>
      </select>

      <!---CC_YEAR--->
      &nbsp;&nbsp;Year:
      <select name="CC_YEAR" class="cctext">
   	<?

   	$this_year = date("Y");			// Start from current year and go 10 years forward.
   	$last_year = $this_year + 10;

   	for ($x=$this_year;$x<=$last_year;$x++) {
   		echo "<OPTION VALUE=\"$x\">$x</OPTION>\n";
   	}

   	?>
      </select>
     </td>
    </tr>
    <tr>
     <td align="left" class="text">
      3-Digit <? echo lang("Security Code"); ?>:
     </td>

     <!---CC_AVS--->
     <td align="left" valign="middle">
      <input type="text" name="CC_AVS" class="cctext" style='WIDTH: 50px;'>
     </td>
    </tr>
    <tr>
     <td colspan="2" class="text" align="center" bgcolor="#<? echo $OPTIONS[DISPLAY_HEADERBG]; ?>">
      <input type="button" value=" Process Order &gt;&gt;" class="FormLt1" name="button" onClick="document.pay_paypoint.submit()">
     </td>
    </tr>
   </table>
   <br><br>
  </td>
 </tr>
 <tr>
  <td align="center" class="text">
   <? echo lang("How to find your security code"); ?>:<br>
   <img src="avs_graphic.gif" width="516" height="130">
  </td>
 </tr>
</table>
</form>