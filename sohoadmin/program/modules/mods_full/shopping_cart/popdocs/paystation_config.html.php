<?php
error_reporting(E_PARSE);
if($_GET['_SESSION'] != '' || $_POST['_SESSION'] != '' || $_COOKIE['_SESSION'] != '') { exit; }

require_once("../../../../includes/product_gui.php");

$result = mysql_query("SELECT * FROM cart_options");
$PAYMENT = mysql_fetch_assoc($result);

if(strlen($PAYMENT['PAYMENT_SSL']) > 4){
	$pgateway_link = $PAYMENT['PAYMENT_SSL'];
} else {
	$pgateway_link = "http://".$_SESSION['this_ip'];
}


?>
<html>
<head>
 <title>| ? | - Paystation Configuration</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" type="text/css" href="../../../../product_gui.css">
<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 10px;
	margin-right: 0px;
	margin-bottom: 0px;
}
.style5 {
	font-size: 11px;
	font-style: italic;
	border-style: solid none none none;
	font-weight: bold;
	color: #565656;
}
.style9 {border-style: solid none none none; font-weight: bold; color: #565656; font-size: 10px;}
.style10 {color: #980000}
.style11 {color: #565656}
.style17 {color: #339959; font-weight: bold; }
-->
</style>
</head>
<body>
<table width="450" border=0 align="center" cellpadding="8" cellspacing=0 class="feature_grn" celpadding="0" style="background: #FFFFFF; border: 1px solid #565656;">
 <tr>
  <td colspan="2" class="fgrn_title" style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; letter-spacing: 2px;">How to configure Paystation for your site</td>
 </tr>
 <tr>
  <td colspan="2" class="feature_sub" style="border-style: solid none solid none; border-color: #B5B5B5; background: #DFF6EA;">
	You will need to contact <a href="http://www.paystation.co.nz" target="_blank">Paystation</a> and have them set your return URL <br/> to the link below</td>
 </tr>
 <tr>
  <td align="right" bgcolor="#F8FDFB">&nbsp;</td>
  <td align="left" bgcolor="#F8FDFB" style="text-align: left; padding-top:0px;"><span class="style11"><?php echo $pgateway_link; ?>/shopping/pgm-show_invoice.php?meth=paystation</span></td>
 </tr>
 <tr align="center">
  <td colspan="2" class="feature_grn style5" style="padding-bottom:0px; border-bottom: 0px;">Once you have set up your return URL, Paystation is configured!</td>
 </tr>
 <tr align="center">
  <td colspan="2" class="feature_grn  style9" style="border-top: 0px;">[ <a href="javascript:window.close();" class="del">Close Window</a> ]</td>
 </tr>
</table>
</body>
</html>