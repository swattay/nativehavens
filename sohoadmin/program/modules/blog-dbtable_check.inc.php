<?php
error_reporting(E_PARSE);
if($_GET['_SESSION'] != '' || $_POST['_SESSION'] != '' || $_COOKIE['_SESSION'] != '') { exit; }

#=====================================================================
# Check for blog tables, create and insert defaults if not found
# Included by various scripts related to blogs and promo boxes
#=====================================================================

######################################################################
### DOES THE BLOG_CATEGORY TABLE EXIST?
######################################################################

$match = 0;

$result = mysql_list_tables("$db_name");
$i = 0;
while ($i < mysql_num_rows ($result)) {
	$tb_names[$i] = mysql_tablename ($result, $i);
	if ($tb_names[$i] == "BLOG_CATEGORY") { $match = 1; }
	if ($tb_names[$i] == "blog_category") { $match = 1; }
	$i++;
}

### DOES NOT EXIST; CREATE TABLE NOW
///===================================================

if ($match != 1) {
	mysql_db_query("$db_name","CREATE TABLE BLOG_CATEGORY (PRIKEY INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT, CATEGORY_NAME CHAR(150))");

}

######################################################################
### DOES THE BLOG_CONTENT DATA TABLE EXIST?
######################################################################

$match = 0;

$result = mysql_list_tables("$db_name");
$i = 0;
while ($i < mysql_num_rows ($result)) {
	$tb_names[$i] = mysql_tablename ($result, $i);
	if ($tb_names[$i] == "BLOG_CONTENT") { $match = 1; }
	if ($tb_names[$i] == "blog_content") { $match = 1; }
	$i++;
}

### DOES NOT EXIST; CREATE TABLE NOW
///===================================================
if ($match != 1) {
	mysql_db_query("$db_name","CREATE TABLE BLOG_CONTENT (PRIKEY INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT, BLOG_SUBJECT INT(11), BLOG_TITLE CHAR(255), BLOG_DATA BLOB, BLOG_DATE DATE)");
}



######################################################################
### v4.7 RC4 - Check for promo and newsbox categories
/*##################################################################*/
$newscat = lang("Latest News");
$newscat = trim($newscat);
$promocat = lang("Special Promotions");
$promocat = trim($promocat);

$nboxcat = 0;
$pboxcat = 0;

$catRez = mysql_query("SELECT * FROM BLOG_CATEGORY");
while ( $catScan = mysql_fetch_array($catRez) ) {
   $disCat = trim($catScan[CATEGORY_NAME]);
   if ( $disCat == $newscat ) { $nboxcat++; }
   if ( $disCat == $promocat ) { $pboxcat++; }
}

if ( $nboxcat == 0 ) {
   // Insert news box category
   // --------------------------------------------
   mysql_query("INSERT INTO BLOG_CATEGORY VALUES('','$newscat')");
}

if ( $pboxcat == 0 ) {
   // Insert promo box category
   // --------------------------------------------
   mysql_query("INSERT INTO BLOG_CATEGORY VALUES('','$promocat')");
}

if ( ($nboxcat + $pboxcat) < 1 ) {

   // Scan again - Pull prikey values for site_specs table
   // -----------------------------------------------------
   $newsKey = "";
   $promoKey = "";
   $catRez = mysql_query("SELECT * FROM BLOG_CATEGORY WHERE CATEGORY_NAME = '$newscat' OR CATEGORY_NAME = '$promocat'");
   while ( $catScan = mysql_fetch_array($catRez) ) {
      $disCat = trim($catScan[CATEGORY_NAME]);
      if ( $disCat == $newscat ) { $newsKey = $catScan[PRIKEY]; }
      if ( $disCat == $promocat ) { $promoKey = $catScan[PRIKEY]; }
   }

   // Record promo and newsbox category IDs in site_specs
   // ===========================================================
   mysql_query("UPDATE site_specs SET news_cat = '$newsKey',promo_cat = '$promoKey'");

} // End if creating news and promo categories
/*##################################################################*/
?>