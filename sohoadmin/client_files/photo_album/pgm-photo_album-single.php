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


if ( !function_exists('sterilize_albumname') ) {
	function sterilize_albumname($curAlbum) {
	   $curAlbum = eregi_replace('[<>]', $curAlbum);
	   $curAlbum = stripslashes($curAlbum);
	   return $curAlbum;
	}
}
########################################################################
### MAKE SURE THIS SCRIPT WILL WORK ON ANY PAGE THAT IT IS PLACED ON ###
########################################################################


	$tmp = $PHP_SELF;
	$tmp_root = split("/", $tmp);
	$tmp_cnt = count($tmp_root);
	$tmp_cnt--;
	$link_page = $tmp_root[$tmp_cnt];

###################################################################
#### DEFINE CURRENT ALBUMS FOR DROP DOWN SELECTION ON WEB SITE ####
###################################################################

$curAlbum = sterilize_albumname($_REQUEST['curAlbum']);

#####################################################################
#### PULL ALL IMAGE FILENAMES TO DISPLAY FOR CURRENT PHOTO ALBUM ####
#####################################################################

if ($THIS_ID != "") {

	$result = mysql_query("SELECT * FROM photo_album WHERE ALBUM_NAME = '$THIS_ID'");
	
	while ($row = mysql_fetch_array($result)) {
		$IMAGE_NAME = $row[IMAGE_NAMES];
		$CAPTION = $row[CAPTION];
		$LINK = $row[LINK];
	}
	 
	$tIMG = split(";", $IMAGE_NAME);
	$tCAP = split(";", $CAPTION);
	$tLIN = split(";", $LINK);
	
	$tmp = count($tIMG);
	$num_photos = -1;
	
	for ($ze=0;$ze<=$tmp;$ze++) {
		if (strlen($tIMG[$ze]) > 2) {
			$num_photos++;
		}
	}

} // End If Album Selected

#############################################################
#### CREATE DISPLAY OF CURRENT IMAGE AND ALBUM SELECTION ####
#############################################################
if ( $_REQUEST['PREVBTN'] == "<< ".$lang["Prev"] ) { $curPhoto = $_REQUEST['prephoto']; }
if ( $_REQUEST['NEXTBTN'] == $lang["Next"]." >>" ) { $curPhoto = $_REQUEST['nxtphoto']; }
if ( $curPhoto == "" ) { $curPhoto = 0; }

//$curAlbum
?>

<FORM NAME=PHOTOA METHOD=POST ACTION="<? echo $link_page; ?>">

<?
	echo "<INPUT TYPE=HIDDEN NAME=pr VALUE=\"$pr\">\n";
	echo "<INPUT TYPE=HIDDEN NAME=\"curAlbum\" VALUE=\"".$curAlbum."\">\n";
?>

<TABLE BORDER=0 CELLPADDING=10 CELLSPACING=0 STYLE='border: 0px #CCCCCC solid;'>

<TR>
<TD ALIGN=CENTER VALIGN=TOP STYLE='font-family: Arial; font-size: 9pt;'><a name="album"></a>

<?
	
	
	//$thisIMG = 'images/'.$tIMG[$curPhoto];
	$thisIMG = "$doc_root/images/".$tIMG[$curPhoto];
	
	// if (file_exists("$thisIMG") && $tIMG[$curPhoto] != "") {

	if ($num_photos > -1) {
	
		$tmparray = getImageSize("$thisIMG");
		$origw = $tmparray[0];
		$origh = $tmparray[1];

		if ($origw > 500) {
			$wh_var = "width=500";
		} else {
			$wh_var = "width=$origw height=$origh";
		}
		
//		if (eregi("win", $_SERVER['SERVER_SOFTWARE'])) {
//			$doc_root = addslashes($_SESSION['doc_root']);
//		}
//		
		// FIX: was breaking img paths on winnt
		// http://E:\this\that.com\www/images/image_global.gif
		//$thisIMG = eregi_replace($doc_root, $_SESSION['this_ip'], $thisIMG);
		$thisIMG = "$this_ip/images/".$tIMG[$curPhoto];

		if (strlen($tLIN[$curPhoto]) > 4) {
			echo "<A HREF=\"$tLIN[$curPhoto]\" TARGET=\"_BLANK\">";
		}
		
		echo ("<DIV STYLE='background: white; padding: 10px;'><IMG SRC=\"http://$thisIMG\" ALT=\"$tLIN[$curPhoto]\" BORDER=0 $wh_var VSPACE=2 HSPACE=2 style=\"border: 1px solid black;\"><BR CLEAR=ALL>\n");
		
		if (strlen($tLIN[$curPhoto]) > 4) {
			echo "</A>";
		}
		
		echo ("<font style='font-family: Verdana; font-size: 12pt;'><BR><B>".$tCAP[$curPhoto]."</B><BR>&nbsp;</font></DIV>\n");

	} else {

		echo ("<font color=#999999>".lang("There are currently no images in this album.")."</font>\n");

	}

	echo ("</TD></TR>\n");

	$nxtphoto = $curPhoto+1;
	if ($nxtphoto > $num_photos) { $nxtphoto = 0; }

	$prephoto = $curPhoto-1;
	if ($prephoto < 0) { $prephoto = $num_photos; }

	echo ("<INPUT TYPE=HIDDEN NAME=nxtphoto VALUE=\"$nxtphoto\">\n");
	echo ("<INPUT TYPE=HIDDEN NAME=prephoto VALUE=\"$prephoto\">\n");

	echo ("<TR><TD ALIGN=CENTER VALIGN=MIDDLE STYLE='font-family: Arial; font-size: 9pt; background-color: #EFEFEF; border: 1px solid black;'>\n");


	echo "<table border=0 cellpadding=2 cellspacing=0 width=100% ><tr><td align=center valign=middle>\n";

	echo ("<INPUT TYPE=SUBMIT CLASS=FormLt1 NAME=PREVBTN VALUE=\"<< ".lang("Prev")."\" align=absmiddle>&nbsp;&nbsp;&nbsp;&nbsp;\n");

	echo "</td><td align=center valign=middle style='font-family: Arial; font-size: 8pt;'>\n";

	$counter = 0;

	for ($a=0;$a<=$num_photos;$a++) {
		$dNum = $a + 1;
		$counter++;
		if ($counter == 11) { echo "<BR CLEAR=ALL><BR>"; $counter = 0; }

		if ($a != $num_photos) { $plus = "&nbsp;"; } else { $plus = ""; }

		if ($dNum < 10) {
			$PADDING = "padding: 1px;";
			$dNum = "&nbsp;".$dNum;
		} else {
			$PADDING = "padding: 1px;";
		}

		if ($a != $curPhoto) {
			echo ("<SPAN align=absmiddle style='border: 1px #000000 inset; background-color: white; $PADDING'>&nbsp;<A HREF=\"$link_page?pr=$pr&curPhoto=$a&curAlbum=$curAlbum#album\">$dNum</A>&nbsp;</SPAN>".$plus."\n");
		} else {
			echo ("<SPAN align=absmiddle style='border: 1px #000000 inset; background-color: darkblue; color: white; $PADDING'>&nbsp;$dNum&nbsp;</SPAN>".$plus."\n");
		}
	}

	echo "</td><td align=center valign=middle>\n";

	echo ("&nbsp;&nbsp;&nbsp;&nbsp;<INPUT TYPE=SUBMIT NAME=NEXTBTN CLASS=FormLt1 VALUE=\"".lang("Next")." >>\" align=absmiddle>\n");

	echo "</td></tr></table>\n";

if($curPhoto != "" || $_POST['GO']){
   echo "<script language=\"javascript\">\n";
   echo "   document.location='#album'\n";
   echo "</script>\n";
}

?>

		</TD>
	</TR>
</TABLE>
</FORM>