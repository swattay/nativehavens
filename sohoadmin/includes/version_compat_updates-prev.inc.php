<?php
error_reporting(E_PARSE);
if($_GET['_SESSION'] != '' || $_POST['_SESSION'] != '' || $_COOKIE['_SESSION'] != '') { exit; }

#====================================================================================================
# Old version compatibility updates (before v4.9 BETA 2)
# This was all ripped straight from update_client.php for v4.8 BETA 2 release
# Some routines have been refined for efficiency, others are as they were originally
#====================================================================================================
/// v4.7 (RC 3) -- Check for copyright field in site_specs
### -------------------------------------------------------------------------------
$selSpx = mysql_query("SELECT * FROM site_specs");

if ( mysql_field_name($selSpx, 15) != "copyright" ) {
   # Add copyright column
   mysql_query("ALTER TABLE site_specs ADD COLUMN copyright BLOB");
   mysql_query("ALTER TABLE site_specs ADD COLUMN df_misc1 BLOB");
   mysql_query("ALTER TABLE site_specs ADD COLUMN df_misc2 BLOB");
}

/// v4.7 (RC 4) -- Check for df_hdrtxt, df_slogan, dev_mode fields in site_specs
### ---------------------------------------------------------------------------------
$selSpx = mysql_query("SELECT * FROM site_specs");

if ( mysql_field_name($selSpx, 18) != "df_hdrtxt" ) {

   // Read HDRTXT and SLOGAN text if available
   // ============================================
   $logoconf = "$cgi_bin/logo.conf";

   if (file_exists("$logoconf")) {
    	$file = fopen("$logoconf", "r");
   	$body = fread($file,filesize($logoconf));
   	fclose($file);
   	$lines = split("\n", $body);
   	$numLines = count($lines);
   	for ($x=0;$x<=$numLines;$x++) {
   		$temp = split("=", $lines[$x]);
   		$variable = $temp[0];
   		$value = $temp[1];
   		${$variable} = $value;
   	}
   } else {
   	$headertext = "";
   	$subheadertext = "";
   }

   $sql_prob = 0;

   // df_hdrtxt
   // ---------------------
   if ( !mysql_query("ALTER TABLE site_specs ADD COLUMN df_hdrtxt VARCHAR(255)") ) { $sql_prob++; }
   if ( !mysql_query("UPDATE site_specs SET df_hdrtxt = '$headertext'") ) { $sql_prob++; }

   // df_slogan
   // ---------------------
   if ( !mysql_query("ALTER TABLE site_specs ADD COLUMN df_slogan VARCHAR(255)") ) { $sql_prob++; }
   if ( !mysql_query("UPDATE site_specs SET df_slogan = '$subheadertext'") ) { $sql_prob++; }

   // dev_mode
   // ---------------------
   if ( !mysql_query("ALTER TABLE site_specs ADD COLUMN dev_mode VARCHAR(255)") ) { $sql_prob++; }

   // Goodbye logo.conf!
   // ---------------------------
   if ( $sql_prob == 0 ) { unlink($logoconf); }

} // End if df_hdrtxt field exists in site_specs

/// v4.7 (Beta 2.7) -- Check for SEO fields in site_pages
### ------------------------------------------------------------
$selTbl = mysql_query("SELECT * FROM site_pages");
if ( mysql_field_len($selTbl, 8) != 10 ) {
   //Add SEO columns
   mysql_query("ALTER TABLE site_pages ADD COLUMN bgcolor VARCHAR(10)");
   mysql_query("ALTER TABLE site_pages ADD COLUMN title VARCHAR(255)");
   mysql_query("ALTER TABLE site_pages ADD COLUMN description BLOB");
}

//----------------------------------------------------------
//      Make link BLOB if not already
//----------------------------------------------------------

$result = mysql_query("SELECT link FROM site_pages");
if ( mysql_field_type($result, 0) != "BLOB" ) {
   if ( !mysql_query("ALTER TABLE site_pages MODIFY link BLOB") ) {
   	mysql_query("drop index link on site_pages");
   	mysql_query("ALTER TABLE site_pages change link link BLOB");
   }
}

//----------------------------------------------------------
//      Make bgcolor BLOB if not already
//----------------------------------------------------------

$result = mysql_query("SELECT bgcolor FROM site_pages");
$fields = mysql_num_fields($result);
$rows  = mysql_num_rows($result);
$table  = mysql_field_table($result, 0);
for ($i=0; $i < $fields; $i++) {
   $type  = mysql_field_type($result, $i);
   $name  = mysql_field_name($result, $i);
   $len  = mysql_field_len($result, $i);
   $flags = mysql_field_flags($result, $i);
}
if ( $type != "BLOB" )
{
   if ( !mysql_query("ALTER TABLE site_pages MODIFY bgcolor BLOB") )
   {
      echo mysql_error(); exit;
   }
}

//----------------------------------------------------------
//      Make page_name 255 char long if not already
//----------------------------------------------------------
$result = mysql_query("SELECT page_name FROM site_pages");
$fields = mysql_num_fields($result);
$rows  = mysql_num_rows($result);
$table  = mysql_field_table($result, 0);
for ($i=0; $i < $fields; $i++) {
   $type  = mysql_field_type($result, $i);
   $name  = mysql_field_name($result, $i);
   $len  = mysql_field_len($result, $i);
   $flags = mysql_field_flags($result, $i);
}
if ( $len != 255 )
{
   if ( !mysql_query("ALTER TABLE site_pages MODIFY page_name VARCHAR(255) NOT NULL") )
   {
      echo mysql_error();
   }
}
//----------------------------------------------------------
//      Make sub_page_of 255 char long if not already
//----------------------------------------------------------
$sub_res = mysql_query("SELECT sub_page_of FROM site_pages");
$fields = mysql_num_fields($sub_res);
$rows  = mysql_num_rows($sub_res);
$table  = mysql_field_table($sub_res, 0);
for ($i=0; $i < $fields; $i++) {
   $type  = mysql_field_type($sub_res, $i);
   $name  = mysql_field_name($sub_res, $i);
   $len  = mysql_field_len($sub_res, $i);
   $flags = mysql_field_flags($sub_res, $i);
}
if ( $len != 255 )
{
   if ( !mysql_query("ALTER TABLE site_pages MODIFY sub_page_of VARCHAR(255)") )
   {
      echo mysql_error(); exit;
   }
}

$borez = mysql_query("SELECT startpage FROM site_specs");
while($bobo = mysql_fetch_array($borez)){
	$true_start_page = $bobo['startpage'];
}

if(strlen($true_start_page) >= 1){
	// v4.7 r2004_11c - Patch faulty Fantastico installations.
	$selHome = mysql_query("SELECT * FROM site_pages WHERE page_name = '".$true_start_page."'");
	$chkHome = mysql_num_rows($selHome);
} else {
	// v4.7 r2004_11c - Patch faulty Fantastico installations.
	$selHome = mysql_query("SELECT * FROM site_pages WHERE page_name = 'Home Page'");
	$chkHome = mysql_num_rows($selHome);
} 


if ( $chkHome < 1 ) {
   mysql_query("INSERT INTO site_pages VALUES('Home Page','main',' ',' ',' ','2459444338',' ',' ',' ','Welcome to $this_ip','$this_ip','')");
}


// Read Current Individual Page Templates into memory
// ----------------------------------------------------------

$filename = "$doc_root/media/page_templates.txt";
if (file_exists($filename)) {
	$file = fopen("$filename", "r");
		$template_vars = fread($file,filesize($filename));
	fclose($file);

	$tmp = split("\n", $template_vars);
	$tmp_cnt = count($tmp);

	for ($x=0;$x<=$tmp_cnt;$x++) {
		if ($tmp[$x] != "") {
			$this_var = split("=", $tmp[$x]);
			$PGname[$x] = $this_var[0];
			$PGtmplet[$x] = $this_var[1];
		} // End If
	} // End For
} // End If File

$tmpletCnt = count($tmp);
//foreach($PGname as $var=>$val){
//   //echo "var = (".$var.") val = (".$val.")<br>";
//}
//foreach($PGtmplet as $var=>$val){
//   //echo "var = (".$var.") val = (".$val.")<br>";
//}

######################################################################
### HAS THE SITE_PAGES TABLE BEEN UPDATED WITH TEMPLATE COLUMN?    ###
######################################################################

$isUp = 0;
$SPfields = mysql_num_fields($selTbl);
if($SPfields == 12){
   $isUp = 1;
}

// DOES NOT EXIST; INSERT TEMPLATE COLUMN NOW
## ====================================================

if($isUp == 0){
   //echo "No template field<br>"; exit;
   if ( !mysql_query("alter table site_pages add column template VARCHAR(100)") ) {
      echo "Cannot add template column to site_pages table!<br>";
      echo "Mysql says: ".mysql_error()."";
   }

   $tmpletCnt--;
   for ( $j=0; $j < $tmpletCnt; $j++ ) {
      if($PGname[$j] != "Latest_News" && $PGname[$j] != "Shopping_Cart"){
         $PGtmplet[$j] = eregi_replace($doc_root."/", "", $PGtmplet[$j]);
         if(!mysql_query("UPDATE site_pages SET template = '".$PGtmplet[$j]."' WHERE page_name = '".eregi_replace("_", " ", $PGname[$j])."'")){
         	echo "\n\n\n\n\n<!----------------------------------------------------------ERROR------------------------------------------------------------>\n";
         	echo "<!---Cannot save page template assignment for page '".$PGname[$j]."' to newly-created template field in site_pages table.--->\n";
         	echo "<!--- Mysql says: ".mysql_error()." --->\n";
         	echo "<!----------------------------------------------------------ERROR------------------------------------------------------------>\n\n\n\n\n\n";
         	//exit;
         }
      }
   }

} // End if isUp == 0

if ( table_exists("PROMO_BOXES") ) {
   $sub_res = mysql_query("SELECT * FROM PROMO_BOXES");
   $rows  = mysql_num_rows($sub_res);
   if($rows == 25){
      $news_cat_pull = "'','newsbox', '1', '', '', '', '', '', '', '', ''";
      if(!mysql_query("INSERT INTO PROMO_BOXES VALUES(".$news_cat_pull.")")){
         echo mysql_error();
      }
      $promo_cat_pull = "'','promobox', '2', '', '', '', '', '', '', '', ''";
      if(!mysql_query("INSERT INTO PROMO_BOXES VALUES(".$promo_cat_pull.")")){
         echo mysql_error();
      }
   }
}

?>