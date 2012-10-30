<?php
error_reporting(E_PARSE);
if($_GET['_SESSION'] != '' || $_POST['_SESSION'] != '' || $_COOKIE['_SESSION'] != '') { exit; }

if(!include_once("../includes/product_gui.php")){
	exit;	
}
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


#######################################################################
### STEP 1: PULL ALL CURRENT SITE PAGES FROM DATA TABLE
#######################################################################

$result = mysql_query("SELECT page_name FROM site_pages");
$t_chk = ";";
while ($row = mysql_fetch_array($result)) {
	$t_chk .= $row['page_name'].";";
}
$PAGE_ERR = "";

#######################################################################


function sterilize($sterile_var) {
	$sterile_var = stripslashes($sterile_var);
	$st_l = strlen($sterile_var);
	$st_a = 0;
	$tmp = "";
	while($st_a != $st_l) {
		$temp = substr($sterile_var, $st_a, 1);
		if (eregi("[0-9a-z_ !\-ÅÄÖåäö]", $temp)) { $tmp .= $temp; }
		$st_a++;
	}
	$sterile_var = $tmp;
	$sterile_var = trim($sterile_var);
	return $sterile_var;
}


$string = implode("~~~", $_POST);
$string = stripslashes($string);
$string = eregi_replace("'", "", $string);
$string = str_replace("&", "", $string);
$string = str_replace("#", "", $string);
$string = str_replace("\"", "", $string);
$string = str_replace("/", "", $string);
$string = str_replace("_", " ", $string);
$formValues = split("~~~", $string);
$numVars = count($formValues);

$c=0;
for ($d=1;$d<=$numVars;$d=$d+2) {
   $nxt = $d+1;
   if (strlen($formValues[$d]) >= 1) {
      $c++;
      $addPage[$c] = $formValues[$d];
      //$subPage[$c] = $formValues[$nxt];
   }
}


#######################################################################
### Loop through "pages to create" and make sure they do not already
### exist within the web site
#######################################################################
$errors = array();

for ($d=1;$d<=$c;$d++) {
   //$addPage[$d] = ucwords($addPage[$d]);
   $l = strlen($addPage[$d]);
   $a = 0;
   $tmpName = "";
   while($a != $l) {
      $temp = substr($addPage[$d], $a, 1);
      $tmpName .= $temp;
      $a++;
   }
   $addPage[$d] = $tmpName;
   $link = md5($tmpName);

   if ( $addPage[$d] != "php.ini" && $addPage[$d] != "php" && !eregi(";$addPage[$d];", $t_chk) ) {    // DO NOT REMOVE -- THIS IS FOR SECURITY

      $pagefile = eregi_replace(' ', '_', $addPage[$d]);
			$opentag = "<?php\n";
			$opentag .= 'error_reporting(E_PARSE);'."\n";
			$opentag .= 'session_start();'."\n";
			$opentag .= 'if($_GET[\'_SESSION\'] != \'\' || $_POST[\'_SESSION\'] != \'\' || $_REQUEST[\'_SESSION\'] != \'\' || $_COOKIE[\'_SESSION\'] != \'\') { exit; }'."\n";
			$indexphpurl = $opentag.'include("pgm-site_config.php");'."\n";
			$indexphpurl .= '$pagetitle = eregi_replace( "_", " ", "'.$pagefile.'" );'."\n";
			$indexphpurl .= '$secure_setting = mysql_query("select username from site_pages where page_name = \'$pagetitle\'");'."\n";
			$indexphpurl .= '$secure_name = mysql_fetch_array($secure_setting);'."\n";
			$indexphpurl .= '	if (!isset($secure_name[\'username\']) or ($secure_name[\'username\'] == "")) {'."\n";
			$indexphpurl .= '    $pr = "'.$pagefile.'";'."\n";
			$indexphpurl .= '    $_REQUEST[\'pr\'] = "'.$pagefile.'";'."\n";
			$indexphpurl .= '    $_GET[\'pr\'] = "'.$pagefile.'";'."\n";
			$indexphpurl .= '    $_POST[\'pr\'] = "'.$pagefile.'";'."\n";
			$indexphpurl .= '    $pageRequest = "'.$pagefile.'";'."\n";
			$indexphpurl .= '    include("index.php");'."\n";
			$indexphpurl .= '	} else { $destination = "'.pagename($pagefile)."\";\n";
			$indexphpurl .= '	  header("Location:$destination");'."\n";
			$indexphpurl .= '   }'."\n";
			$indexphpurl .= 'exit;'."\n";
			$indexphpurl .= "?>"."\n";
      $indexphpfilename = $_SESSION['doc_root']."/".$pagefile.'.php';
      $indexphpfile = fopen($indexphpfilename, "w");
      fwrite($indexphpfile, $indexphpurl);
      fwrite($indexphpfile);
      fclose($indexphpfile);
      chmod($indexphpfilename, 0755);

   } else {
      # Problem with this one
      $errors[] = $addPage[$d];
//       $PAGE_ERR .= "$addPage[$d] \\n";
   }
} // End For $d


//	#######################################################################
//	### If a page exists; bomb now and return to create pages
//	#######################################################################
//
//	if ($PAGE_ERR != "") {
//		echo "<SCRIPT LANGUAGE=Javascript>\n";
//		echo "	alert('".$lang["Could Not Create the Following Pages because they already exist on the system:"]."\\n\\n$PAGE_ERR');\n";
//		echo "  history.back();\n";
//		echo "</SCRIPT>\n";
//		exit;
//	}

	#######################################################################
	### No duplicates detected, proceed with creation of pages
	#######################################################################

// This checks for the main menu pages and adds
// the page to the menu if selected in Create Pages
// Joe Lain 7-25-05

//error_reporting(E_ALL);

$thisnum = 0;
$findOnmenu = mysql_query("SELECT * FROM site_pages WHERE main_menu>0");
$mainnum = "";
while ($OnMenu = mysql_fetch_array($findOnmenu))
{
	$mainnum = $OnMenu['main_menu'];
	if ( $mainnum > $thisnum )
	{
		$bigone = $mainnum;
		$thisnum = $bigone;
	}
}

$bigone++;

for ($d=1;$d<=$c;$d++) {

   //$addPage[$d] = ucwords($addPage[$d]);

   $l = strlen($addPage[$d]);
   $a = 0;
   $tmpName = "";
   while($a != $l) {
      $temp = substr($addPage[$d], $a, 1);
      $tmpName .= $temp;
      $a++;
   }
   $addPage[$d] = $tmpName;
   
   $link = md5($tmpName);

   // Fixed problem when creating pages with underscores from tool
   // Mike Morrison - 5/2003
   // created db_page_name var and removed slashes before adding
   // to db
   // ------------------------------------------------------------

   if ( !in_array($addPage[$d], $errors) ) {
      $addPage[$d] = sterilize($addPage[$d]);
      $db_page_name[$d] = eregi_replace("_", " ", $addPage[$d]);
      
      //$pageName = htmlentities($db_page_name[$d], ENT_QUOTES);

      if ( strtolower(${"ONMENU".$d}) != "yes" ) {
         # Do not add page to menu
         $SQL_INSERT = "VALUES('".$db_page_name[$d]."','main','$ts', '', '','".$link."','','','','','','')";
         $dataArr = array();
         $dataArr['page_name'] = $db_page_name[$d];
         $dataArr['type'] = 'main';
         $dataArr['sub_page_of'] = $ts;
         $dataArr['link'] = $link;
         $myQry = new mysql_insert('site_pages', $dataArr);
         $myQry->insert();
         

      } else {
         # Adds page to menu
         $SQL_INSERT = "VALUES('".$db_page_name[$d]."','main','$ts', '', ".$bigone.",'".$link."','','','','','','')";
         $dataArr = array();
         $dataArr['page_name'] = $db_page_name[$d];
         $dataArr['type'] = 'main';
         $dataArr['sub_page_of'] = $ts;
         $dataArr['main_menu'] = $bigone;
         $dataArr['link'] = $link;
         $myQry = new mysql_insert('site_pages', $dataArr);
         $myQry->insert();         
         
         $bigone++;
      }

//      if ( !$mkpages = mysql_query("INSERT INTO site_pages $SQL_INSERT") ) {
//          echo "Could not create new pages!<br>";
//          echo "MySQL Error: [".mysql_error()."]\n";
//          exit;
//      }

      # Update User Access Rights with created page if applicable.
      if ($CUR_USER_ACCESS != "WEBMASTER") {
         $this_page = eregi_replace(" ", "_", $addPage[$d]);
         $CUR_USER_ACCESS = $CUR_USER_ACCESS."$this_page;";
      }

   } // End if !in_array($addPage, $errors)

   # Add pages to "recently accessed" list for priority display on open/edit pages menu
//   error_reporting(E_ALL);
//   $arraykey = time();
   $_SESSION['recent_pages'][$addPage[$d]] = time();

} // End $d For Loop


# Update user access rights in db with updated $CUR_USER_ACCESS string (presumably added to in for loop)
if ($CUR_USER_ACCESS != "WEBMASTER") {
   mysql_query("UPDATE USER_ACCESS_RIGHTS SET ACCESS_STRING = '$CUR_USER_ACCESS' WHERE LOGIN_KEY = '$CUR_USER_KEY'");
}


$action = "DONE";	// TELL CREATE_PAGES SCRIPT THAT THE ADDITION IS COMPLETE

?>