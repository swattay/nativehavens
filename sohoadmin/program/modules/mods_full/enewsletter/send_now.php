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

set_time_limit(0);
error_reporting(0);
session_start();

include("../../../includes/product_gui.php");

error_reporting(0);

#############################################################################################
### Get this Campaign from the Manager Data Table
#############################################################################################

$result = mysql_query("SELECT * FROM CAMPAIGN_MANAGER WHERE PRIKEY = '$id'");
$row = mysql_fetch_array($result);
$email_names = $row['TEXT_EMAIL_ADDR'];
// -----------------------------------------------------
// Go ahead and get HTML content read and set send date
// -----------------------------------------------------

$SEND_DATE = date("Y-m-d");
$THIS_HTML = $row[HTML_CONTENT];
//echo testArray($row);
$THIS_HTML = str_replace("[FIRST_NAME]", "First name goes here", $row['HTML_CONTENT']);

//echo "<div style=\"width: 800px;height: 300px;overflow: auto;border: 1px solid red;\">".$THIS_HTML."</div>";
// -----------------------------------------------------
// Include HTML MIME MAIL class & register class
// -----------------------------------------------------

include("htmlMimeMail.php");
error_reporting(0);
$mail = new htmlMimeMail();

// ---------------------------------------------------
// Load image files into mimemail object
// ---------------------------------------------------
$images = split(";", $row[IMAGE_ARRAY]);
$img_cnt = count($images);
//$this_ip = eregi_replace("www.", "", $this_ip);

for ($x=0;$x<=$img_cnt;$x++) {
	if ($images[$x] != "") {		// Just in case -> bi-product of array build
		$tmp = split("/", $images[$x]);
		$tmpc = count($tmp) - 1;
		$this_image = $tmp[$tmpc];

		//echo testArray($images);
		//echo $images[$x];
    $images[$x] = eregi_replace($doc_root."/", "", $images[$x]);
    $html_label = eregi_replace("images/$this_image", "http://$this_ip/images/$this_image", $images[$x]);
		$real_one = $this_ip . $html_label;
		$filename = $images[$x];
		$fp = fopen($filename, 'r');
			$img_data = fread($fp, filesize($filename));
		fclose($fp);

		if (eregi("\.jpg", $this_image)) { $img_mime = "image/jpg"; }
		if (eregi("\.gif", $this_image)) { $img_mime = "image/gif"; }

//		$mail->add_html_image($img_data, $this_image, $img_mime);
	} // End If NO image name found

} //http://newsletter.aboutfabric.comhttp//newsletter.aboutfabric.com/images/MartiniComposite.jpg
//http://newhttp//newsletter.aboutfabric.com/images/BWcomposite_01.jpg

//echo "1(".$THIS_HTML.")END1<br/>\n";

//echo "<textarea style=\"width: 550px; height: 250px; font-size: 12px; font-family: arial;\">".$THIS_HTML."</textarea>";
//echo $THIS_HTML."<br><br><br>";
//echo "1)<textarea style=\"width: 550px; height: 250px; font-size: 12px; font-family: arial;\">".$THIS_HTML."</textarea>";
$THIS_HTML = eregi_replace("href\=\"index\.php", "href=\"http://$this_ip/index.php", $THIS_HTML);
//$THIS_HTML = eregi_replace("/images", "http://$this_ip/images", $THIS_HTML);
//echo $html_label;
//$THIS_HTML = eregi_replace("$html_label", "$this_image", $THIS_HTML);	// Make sending HTML match MIME encoded images
$THIS_HTML = eregi_replace("<img src\=\"sohoadmin", "<img src=\"http://".$this_ip."/sohoadmin", $THIS_HTML);
$THIS_HTML = eregi_replace("src\=\"sohoadmin", "src=\"http://".$this_ip."/sohoadmin", $THIS_HTML);
$THIS_HTML = eregi_replace("background\=\"sohoadmin", "background=\"http://".$this_ip."/sohoadmin", $THIS_HTML);
//echo "2)<textarea style=\"width: 550px; height: 250px; font-size: 12px; font-family: arial;\">".$THIS_HTML."</textarea>";

$THIS_HTML = eregi_replace("src\=\"images", "src=\"http://$this_ip/images", $THIS_HTML);
$THIS_HTML = eregi_replace("background\=\"images", "background=\"http://$this_ip/images", $THIS_HTML);
$THIS_HTML = eregi_replace("http://$this_ip/http://$this_ip/", "http://$this_ip/", $THIS_HTML);	// Text Editor Work around for images
//$THIS_HTML = eregi_replace("http//$this_ip/", "", $THIS_HTML);	// Text Editor Work around for images // Commented-out because it was breaking absolute links back to sending website
$THIS_HTML = eregi_replace("pgm\-download", "http://$this_ip/pgm-download", $THIS_HTML);
$THIS_HTML = eregi_replace("href\=\"media/", "href=\"http://$this_ip/media/", $THIS_HTML);
$THIS_HTML = str_replace("href=\"sohoadmin", "href=\"http://".$this_ip."/sohoadmin", $THIS_HTML);
$THIS_HTML = eregi_replace("src\=\"download_icon\.gif", "src=\"http://$this_ip/sohoadmin/program/modules/page_editor/client/download_icon.gif", $THIS_HTML);
$THIS_HTML = eregi_replace("http://".$this_ip."http://".$this_ip, "http://".$this_ip, $THIS_HTML);



//echo "<textarea style=\"width: 550px; height: 250px; font-size: 12px; font-family: arial;\">".$THIS_HTML."</textarea>";

//echo $THIS_HTML = eregi_replace("http://$this_ip/http//$this_ip/", "http://$this_ip/", $THIS_HTML);
//http//newsletter.aboutfabric.com
//echo "<textarea style=\"width: 550px; height: 250px; font-size: 12px; font-family: arial;\">".$THIS_HTML."</textarea>";
//echo $THIS_HTML;
//exit;
// ---------------------------------------------------
// MAKE SURE ALL LINKS WORK FROM EMAIL
// ---------------------------------------------------

//$THIS_HTML = eregi_replace("href=\"media/", "href=\"http://$this_ip/media/", $THIS_HTML);
//$THIS_HTML = eregi_replace("runtime.css", "http://$this_ip/runtime.css", $THIS_HTML);
$THIS_HTML = eregi_replace("\"shopping/", "\"http://$this_ip/shopping/", $THIS_HTML);
//$THIS_HTML = eregi_replace("pgm-secure", "http://$this_ip/pgm-secure", $THIS_HTML);
//$THIS_HTML = eregi_replace("pgm-form", "http://$this_ip/pgm-form", $THIS_HTML);
//
//$THIS_HTML = eregi_replace("pgm-download", "http://$this_ip/pgm-download", $THIS_HTML);
//$THIS_HTML = eregi_replace("pgm-email", "http://$this_ip/pgm-email", $THIS_HTML);
//$THIS_HTML = eregi_replace("pgm-print", "http://$this_ip/pgm-print", $THIS_HTML);
//$THIS_HTML = eregi_replace("pgm-view", "http://$this_ip/pgm-view", $THIS_HTML);
//
//$THIS_HTML = eregi_replace("pgm-print", "http://$this_ip/pgm-print", $THIS_HTML);
//$THIS_HTML = eregi_replace("pgm-view_video", "http://$this_ip/pgm-view_video", $THIS_HTML);
//
//$THIS_HTML = eregi_replace("VALUE=\"media/", "VALUE=\"http://$this_ip/media/", $THIS_HTML);
//$THIS_HTML = eregi_replace("index.php", "http://$this_ip/index.php", $THIS_HTML);



$THIS_HTML = eregi_replace('(title|alt|mce_src)\=("|\'| )[^"\' ]*("|\'| )', '', $THIS_HTML);
//echo "<div style=\"width: 800px;height: 300px;overflow: auto;border: 1px solid red;\">".$THIS_HTML."</div>";
//echo "<textarea style=\"width: 550px; height: 250px; font-size: 12px; font-family: arial;\">".$THIS_HTML."</textarea>";
// exit;


//	$THIS_HTML = eregi_replace("<img src=\"sohoadmin", "<img src=\"http://".$this_ip."/sohoadmin", $THIS_HTML);
//	$THIS_HTML = eregi_replace("background=\"sohoadmin", "background=\"http://".$this_ip."/sohoadmin", $THIS_HTML);

// -----------------------------------------------------------------
// Add Automated "Opt-Out" Feature to each newsletter
// -----------------------------------------------------------------
$opt_out = "<DIV ALIGN=CENTER><FONT STYLE='font-family: Arial; font-size: 7pt;'>If you do not wish to receive this email, unsubscribe to this service now. <A HREF=\"http://$this_ip/subscription/index.php?id=unsubscribe\">Click Here</A></FONT></DIV>\n";
$THIS_HTML = eregi_replace("</BODY>{1}", $opt_out."\n</BODY>\n", $THIS_HTML);

// -----------------------------------------------------------
// Finally, Add Hidden Tracking Image to HTML Before Send
// -----------------------------------------------------------
$THIS_HTML = $THIS_HTML . "<CENTER><img src=\"http://$this_ip/pgm-tracking.php?id=$id\" border=0 width=1 height=1></center><BR>";



// ---------------------------------------------
// SEND HTML NEWSLETTER NOW!
// ---------------------------------------------
// Split send into "chunks" for SENDMAIL to operate properly
$uresult = mysql_query("select DISTINCT upper(UNSUB_EMAIL_ADDR) as uemail from UNSUBSCRIBE");
$unotin = "'";
$numu = mysql_num_rows($uresult);
$ucount = 1;
while($urow = mysql_fetch_array($uresult)) {
   $unotin .= $urow[uemail];
	 $noemail[]=$urow[uemail];
   if ( $ucount < $numu ) {
      $unotin .= "','";
   }

   $ucount++;
} // end while
$unotin .= "'";


//echo "2(".$THIS_HTML.")END2<br/>\n";
//exit;

////////////////////////////////////////////
////if sending non-personalized newsletters//
////////////////////////////////////////////
if($email_names == ''){
	// ---------------------------------------------------
	// Load html and text newsletter into mimemail object
	// and build the message (encoding the text is cool)
	// ---------------------------------------------------
	$ipath = $doc_root . "/images/";
	$mail->setHtml($THIS_HTML, $row[TEXT_CONTENT], $ipath);
	$to_address = "list@".str_replace('www.', '', $this_ip);
	$from_address = "\"$row[FROM_ADDR]\" <$row[FROM_ADDR]>";
	$return_address = $row[FROM_ADDR];
	$mailer = "HTML Mime mail class (http://$this_ip)";
	$subject = "$row[SUBJECT_LINE]";
	$mail->setReturnPath($return_address);
	$mail->setFrom($from_address);
	$mail->setSubject($subject);
	$mail->setHeader('X-Mailer', $mailer);
	$mail->setHeader('Date', date("r")); // Exprimental...John @ Smile NZ says Yahoo flags messages as spam that don't have date headers
	$mresult = mysql_query("select DISTINCT upper($row[HTML_EMAIL_ADDR]) as semail from $row[TABLE_NAME] where upper($row[HTML_EMAIL_ADDR]) NOT IN ($unotin)");
	$counter = "1";
	while ( $mrow = mysql_fetch_array($mresult) ) {
		$theaddress = $mrow[semail];
		if(!in_array(strtoupper($theaddress), $noemail)){
			if ( eregi("@+.+\.[a-zA-Z]", $theaddress) && !eregi(" ", $theaddress) && !eregi("'", $theaddress) && !eregi("/", $theaddress) && !eregi("nobody", $theaddress) && !eregi("nowhere", $theaddress) && !eregi("\"", $theaddress) && !eregi("\.@", $theaddress) && !eregi("@\.", $theaddress) ) {
				$emailarray[] = $theaddress;
				$namearray[$theaddress] = $firstname;
				echo "$counter: $theaddress<br>\n";
			   $counter++;

		   } // end if
		}
	} // end while
	$nummail = count($emailarray);


	$loopnum = $nummail / 200;
	$loopnum = ceil($loopnum);
	$multiple = 200;
	$start = 0;
	$end = 200;
	$length = 200;
	for($z=1;$z<=$loopnum;$z++) {
	   $end = 200 * $z;
	   $output = array_slice($emailarray,$start,$length);
	   $bccmail = implode(",",$output);
	   $mail->setBcc($bccmail);
	   $sresult = $mail->send(array($to_address), 'smtp');
	   $start = $end;
	} // for each loop




} else {  ////////////////////////////////////////////////
	///////////////if sending personalized emails///////////
	///////////////////////////////////////////////////////

	$mresult = mysql_query("select DISTINCT upper($row[HTML_EMAIL_ADDR]) as semail, $email_names as emailname from $row[TABLE_NAME] where upper($row[HTML_EMAIL_ADDR]) NOT IN ($unotin)");
	$counter = "1";
	while ( $mrow = mysql_fetch_array($mresult) ) {

	   $theaddress = $mrow['semail'];
	   $firstname_arr = explode(' ', $mrow['emailname']);
	   $firstname = ucfirst(strtolower($firstname_arr['0']));
	   //echo "(".$firstname.")\n";
	   if(!in_array(strtoupper($theaddress), $noemail)){

			if ( eregi("@+.+\.[a-zA-Z]", $theaddress) && !eregi(" ", $theaddress) && !eregi("'", $theaddress) && !eregi("/", $theaddress) && !eregi("nobody", $theaddress) && !eregi("nowhere", $theaddress) && !eregi("\"", $theaddress) && !eregi("\.@", $theaddress) && !eregi("@\.", $theaddress) ) {

			// ---------------------------------------------------
			// Load html and text newsletter into mimemail object
			// and build the message (encoding the text is cool)
			// ---------------------------------------------------
			$ipath = $doc_root . "/images/";
			$FINAL_HTML = eregi_replace('#name#', $firstname, $THIS_HTML);
			$mail = new htmlMimeMail();
			$mail->setHtml($FINAL_HTML, $row[TEXT_CONTENT], $ipath);
			$to_address = "list@".str_replace('www.', '', $this_ip);
			$from_address = "\"$row[FROM_ADDR]\" <$row[FROM_ADDR]>";
			$return_address = $row[FROM_ADDR];
			$mailer = "HTML Mime mail class (http://$this_ip)";
			$subject = "$row[SUBJECT_LINE]";
			$mail->setReturnPath($return_address);
			$mail->setFrom($from_address);
			$mail->setSubject($subject);
			$mail->setHeader('X-Mailer', $mailer);
			$mail->setHeader('Date', date("r")); // Exprimental...John @ Smile NZ says Yahoo flags messages as spam that don't have date headers

			$sresult = $mail->send(array($theaddress), 'smtp');

		   } // end if
		}
	} // end while
	//exit;
//	$nummail = count($emailarray);
//
//
//	$loopnum = $nummail / 200;
//	$loopnum = ceil($loopnum);
//	$multiple = 200;
//	$start = 0;
//	$end = 200;
//	$length = 200;
//	for($z=1;$z<=$loopnum;$z++) {
//	   $end = 200 * $z;

//	   echo $output = array_slice($emailarray,$start,$length);
//	   echo "<br/>";
//	   echo $bccmail = implode(",",$output);
//	   exit;
//	   //$mail->setBcc($bccmail);
//	   $sresult = $mail->send(array($to_address), 'smtp');
	 //  $start = $end;
//	} // for each loop

}


///////////////////////////////////////////////////////
///////////////END sending personalized emails///////////
///////////////////////////////////////////////////////




// ---------------
// MODIFY CAMPAIGN MANAGER DATA TABLE FOR THIS
// CAMPAIGN AND REDIRECT TO MANAGER PAGE
// ---------------------------------------------
mysql_query("UPDATE CAMPAIGN_MANAGER SET STATUS = 'Sent', SEND_DATE = '$SEND_DATE' WHERE PRIKEY = '$id'");
echo "<script launguage=\"javascript\">";
echo "window.location.href='../enewsletter.php?sendflag=1&=SID'";
echo "</script>";
//header ("Location: ../enewsletter.php?sendflag=1&=SID");
exit;
?>