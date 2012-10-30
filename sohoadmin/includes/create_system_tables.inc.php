<?php
error_reporting(E_PARSE);
if($_GET['_SESSION'] != '' || $_POST['_SESSION'] != '' || $_COOKIE['_SESSION'] != '') { exit; }

session_start();
#================================================================================
# CREATE SYSTEM TABLES
# Checks for and creates (if not found) the various system database tables
# and inserts default data
#================================================================================


# smt_userdata
# The place for all random data used by smaller features and plugins
# Cuts down on the need for one-row tables.
# Works with:
# userdata_mode(), get_userdata(), set_userdata(), delete_userdata
#--------------------------------------------------------------------
if ( !table_exists("smt_userdata") ) {
	$fields = "prikey INT(10) NOT NULL PRIMARY KEY AUTO_INCREMENT, plugin VARCHAR(75), fieldname VARCHAR(255), data BLOB";

	mysql_db_query($_SESSION['db_name'],"CREATE TABLE smt_userdata ($fields)");
}


# site_specs
#---------------------------------------------------------------------
$match = 0;
$blogMatch = 0; // May have to read news and promo cats from blog table
if ( table_exists("site_specs") ) { $match = 1; }
if ( table_exists("BLOG_CATEGORY") ) { $blogMatch = 1; }

if ( $match != 1 ) {
   ################################################################################
   ## DOES NOT EXIST; CREATE TABLE NOW!
   ################################################################################

   ##============================================================
   // Collect and Assemble Default Info
   ##============================================================

   // Read user.conf.php for generic info
   // =============================================
   $filename = "config/user.conf.php";

   if ($file = fopen("$filename", "r")) {
   	$body = fread($file,filesize($filename));
   	$lines = split("\n", $body);
   	$numLines = count($lines);

   	for ($x=0;$x<=$numLines;$x++) {

   		// Register all variables inside user.conf file
   		// --------------------------------------------------------------
   		if (!eregi("#", $lines[$x])) {
   			$variable = strtok($lines[$x], "=");
   			$value = strtok("\n");
   			$value = rtrim($value);

   			session_register("$variable");
   			${$variable} = $value;
   		}
   	}

   	fclose($file);

   } // End If File Open

   // Set language to english if empty
   // =========================================
   if ( $lang_set == "" ) {
      $lang_set = "english.php";
   }


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


   if ( $blogMatch != 0 ) {
      // Try to pull news_cat and promo_cat from BLOG_CATEGORY table
      // ==============================================================
      $newscat = 1;
      $promocat = 2;
      $blogrez = mysql_query("SELECT * FROM BLOG_CATEGORY");

      while ( $getBlog = mysql_fetch_array($blogrez) ) {
         if ( $getBlog['CATEGORY_NAME'] == "Latest News" ) {
            $newscat = $getBlog['PRIKEY'];
         }
         if ( $getBlog['CATEGORY_NAME'] == "Special Promotions" ) {
            $promocat = $getBlog['PRIKEY'];
         }
      }

   } // End if blog table exists


   ##============================================================
   // Construct and execute database queries
   ##============================================================
   $sql_prob = 0; // Error counter

   # Build site_specs field structure
   $wDeez = "df_company CHAR(255), df_address1 CHAR(255), df_address2 CHAR(255), df_city CHAR(100), ";
   $wDeez .= "df_state CHAR(100), df_zip CHAR(25), df_country CHAR(50), ";
   $wDeez .= "df_phone CHAR(50), df_email CHAR(255), df_domain CHAR(255), ";
   $wDeez .= "df_page CHAR(50), df_logo VARCHAR(255), df_lang CHAR(50), ";
   $wDeez .= "news_cat VARCHAR(10), promo_cat VARCHAR(10),";
   $wDeez .= "copyright BLOB,df_misc1 BLOB,df_misc2 BLOB,";
   $wDeez .= "df_hdrtxt VARCHAR(255),df_slogan VARCHAR(255),dev_mode VARCHAR(255),startpage VARCHAR(255), df_fax VARCHAR(50)";


//   $nowww = preg_replace('/www\./', '', $this_ip);
   $nowww = eregi_replace("^www\.", "", $this_ip);
   if ( $headertext == "" ) { $headertext = "Welcome"; }

   $dfemail = "webmaster@".$nowww;

   # Build INSERT data
   $nDis = "'$dfuser_company','$dfuser_address','$dfuser_aptnum','$dfuser_city',";
   $nDis .= "'$dfuser_state','$dfuser_zip','$dfuser_country',";
   $nDis .= "'$dfuser_phone','".$dfemail."','$this_ip',";
   $nDis .= "'Home Page','','$lang_set',";
   $nDis .= "'$newscat','$promocat',";
   $nDis .= "'".date('Y')." $dfuser_company','','',"; // Clear through df_misc2
   $nDis .= "'$headertext','$subheadertext','', 'Home Page', ''"; // Clear through df_fax

   # CREATE TABLE site_specs
   if ( !mysql_db_query("$db_name","CREATE TABLE site_specs ($wDeez)") ) { $sql_prob++; }

   # INSERT INTO site_specs
   if ( !mysql_query("INSERT INTO site_specs VALUES($nDis)") ) { $sql_prob++; }

   // Goodbye logo.conf!
   // ---------------------------
   if ( $sql_prob == 0 ) { unlink($logoconf); }

} // End if site_specs table exists


# site_pages
#---------------------------------------------------------------------
if ( !table_exists("site_pages") ) {

	// DEVNOTE: IF PAGES ARE NOT BEING CREATED; CHECK TO MAKE SURE YOUR TABLE
	//          IS BEING CREATED BEFORE MODIFICATION OF THIS CODE.

	$page_fields = "page_name VARCHAR(35) NOT NULL PRIMARY KEY, type VARCHAR(75), sub_page_of VARCHAR(35), password BLOB, ";
	$page_fields .= "main_menu INT(25), link BLOB, username VARCHAR(35), splash CHAR(1), bgcolor VARCHAR(10), title VARCHAR(255), description BLOB, template VARCHAR(100)";

	mysql_db_query("$db_name","CREATE TABLE site_pages ($page_fields)");

	# Insert home page record for default
	mysql_query("INSERT INTO site_pages VALUES('Home Page','main',' ',' ',' ','2459444338',' ',' ',' ','Welcome to $this_ip','$this_ip','')");
}


# sec_codes
#---------------------------------------------------------------------
$match = 0;
$result = mysql_list_tables("$db_name");
$i = 0;
while ($i < mysql_num_rows ($result)) {
	$tb_names[$i] = mysql_tablename ($result, $i);
	if ($tb_names[$i] == "sec_codes") { $match = 1; }
	$i++;
}
if ($match != 1) {
	mysql_db_query("$db_name","CREATE TABLE sec_codes (PriKey INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT, security_code CHAR(15))");
}


# UDT_CONTENT_SEARCH_REPLACE
#---------------------------------------------------------------------
$match = 0;

$result = mysql_list_tables("$db_name");
$i = 0;
while ($i < mysql_num_rows ($result)) {
	$tb_names[$i] = mysql_tablename ($result, $i);
	if ($tb_names[$i] == "UDT_CONTENT_SEARCH_REPLACE") { $match = 1; }
	$i++;
}
if ($match != 1) {
	mysql_db_query("$db_name","CREATE TABLE UDT_CONTENT_SEARCH_REPLACE (PRIKEY INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT, SEARCH_FOR CHAR(150), REPLACE_WITH BLOB, AUTO_IMAGE CHAR(100), AUTO_SECURITY_AUTH CHAR(255))");
}


# PROMO_BOXES
#--------------------------------------------------------------------
$match = 0;

$result = mysql_list_tables("$db_name");
$i = 0;
while ($i < mysql_num_rows ($result)) {
	$tb_names[$i] = mysql_tablename ($result, $i);
	if (strtoupper($tb_names[$i]) == "PROMO_BOXES") { $match = 1; }
	$i++;
}

if ( $match < 1 ) {

	$qry = "CREATE TABLE PROMO_BOXES (";
	$qry .= " PRIKEY INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,";
	$qry .= " BOX CHAR(255),";
	$qry .= " CONTENT CHAR(255),";         // holds blog category and display type(latest or multiple)
	$qry .= " NUM_DISPLAY CHAR(255),";     // holds number of blogs to display and number of characters
	$qry .= " DISP_TITLE CHAR(255),";      // title settings
	$qry .= " DISP_CONTENT CHAR(255),";    // content settings
	$qry .= " DISP_DATE CHAR(255),";       // date settings
	$qry .= " DISP_MORE CHAR(255),";       // read more settings
   $qry .= " FILE CHAR(255),";            // file name
	$qry .= " SETTINGS CHAR(255),";
	$qry .= " FUTURE1 CHAR(255),";
	$qry .= " content_type VARCHAR(50),";   // i.e. "blog" or "sitepal"
	$qry .= " content_src VARCHAR(255),";    // i.e. blog category or sitepal scene
	$qry .= " style BLOB,";    // i.e. blog category or sitepal scene

	# Strip trailing ","
	$qry = substr($qry, 0, -1);

   $qry .= ")";

	if (!mysql_db_query("$db_name",$qry)){
		//echo "Could not create table PROMO_BOXES!<br>";
		//echo "Mysql says (".mysql_error().")";
		//exit;
	}

   for ( $x=1; $x<=25; $x++ ) {
      # DEFAULT: Insert default box records
      $data = array();
      $box = "box".$x;
      $content = "a:3:{s:7:\"content\";s:11:\"Latest News\";s:7:\"display\";s:2:\"on\";s:4:\"type\";s:6:\"latest\";}";

      $data['BOX'] = $box;
      $data['CONTENT'] = $content;
      $data['NUM_DISPLAY'] = "a:2:{s:4:\"blog\";s:0:\"\";s:5:\"chars\";s:0:\"\";}";
      $data['DISP_TITLE'] = "a:3:{s:7:\"display\";s:2:\"on\";s:5:\"align\";s:4:\"left\";s:6:\"weight\";s:4:\"bold\";}";
      $data['DISP_CONTENT'] = "a:1:{s:7:\"display\";s:2:\"on\";}";
      $data['DISP_DATE'] = "a:6:{s:7:\"display\";s:2:\"on\";s:9:\"fontStyle\";s:2:\"on\";s:6:\"weight\";s:6:\"normal\";s:6:\"format\";s:4:\"full\";s:8:\"position\";s:8:\"dateLast\";s:5:\"align\";s:4:\"left\";}";
      $data['DISP_MORE'] = "a:4:{s:7:\"display\";s:2:\"on\";s:4:\"text\";s:12:\"Read more...\";s:5:\"align\";s:4:\"left\";s:6:\"weight\";s:6:\"normal\";}";
      $data['SETTINGS'] = "a:1:{s:8:\"template\";s:2:\"on\";}";
      $data['content_type'] = "blog";
      $data['content_src'] = "Latest News";

      $myqry = new mysql_insert("PROMO_BOXES", $data);
      if( !$myqry->insert() ) {
//         echo mysql_error();
      }
   }

   # DEFAULT: Oldschool News/promo box default category id's
   $data = array();
   $data['BOX'] = "newsbox";
   $data['CONTENT'] = "1";
   $myqry = new mysql_insert("PROMO_BOXES", $data);
   if( !$myqry->insert() ) {
//      echo mysql_error();
   }

   $data = array();
   $data['BOX'] = "promobox";
   $data['CONTENT'] = "2";
   $myqry = new mysql_insert("PROMO_BOXES", $data);
   if( !$myqry->insert() ){
//      echo mysql_error();
   }
}

# Create blog tables
include_once("program/modules/blog-dbtable_check.inc.php");
?>