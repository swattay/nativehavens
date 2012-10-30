<?php
error_reporting(E_PARSE);
if($_GET['_SESSION'] != '' || $_POST['_SESSION'] != '' || $_COOKIE['_SESSION'] != '') { exit; }

#====================================================================================
# VERSION COMPATIBILITY UPDATES
# This script is included by software updates after extracting a new build file.
# It should contain all checks/updates from its inception (v4.9 beta2) to current.
#
# Example: Originally created for v4.9 to move hitcounter.txt (if already exists)
#          from docroot to filebin.
#====================================================================================

$report = array();

# All compatibility updates prior to those in more recent releases --- just to keep this file more reference-friendly
include($_SESSION['docroot_path']."/sohoadmin/includes/version_compat_updates-prev.inc.php");

# Pull information about installed build version
$build = build_info();

#====================================================================================================
# v4.9 BETA 2
#====================================================================================================

# If hitcounter.txt exists in docroot, move it to filebin
$hitcounter_old = $_SESSION['docroot_path']."/hitcounter.txt";
$hitcounter_new = $_SESSION['docroot_path']."/sohoadmin/filebin/hitcounter.txt";
if ( file_exists($hitcounter_old) ) {
   copy($hitcounter_old, $hitcounter_new);
   $report[] = "hitcounter.txt copied to new location in sohoadmin/filebin";
}

# Add startpage column to site_specs table (set to Home Page by default)
$rez = mysql_query("SELECT * FROM site_specs");
if ( mysql_field_name($rez, 21) != "startpage" ) {
   mysql_query("ALTER TABLE site_specs ADD COLUMN startpage VARCHAR(255)");
   mysql_query("UPDATE site_specs SET startpage = 'Home Page'");
}

# Add CSS field to cart_options table
if ( table_exists("cart_options") && !$rez = mysql_query("SELECT CSS FROM cart_options") ) {
   mysql_query("ALTER TABLE cart_options ADD COLUMN CSS BLOB");

   # Build default value array
   $cartcss = array('table_bgcolor'=>"FFFFFF", 'table_textcolor'=>"000000");

   $qry = "UPDATE cart_options SET CSS = '".serialize($cartcss)."'";
   //if ( !mysql_query($qry) ) { echo "Unable to update cart_options CSS field with default style data...<br/>".mysql_error(); }
   mysql_query($qry);
}

# Add df_fax field to site_specs table
if ( !$rez = mysql_query("SELECT df_fax FROM site_specs") ) {
   mysql_query("ALTER TABLE site_specs ADD COLUMN df_fax VARCHAR(255)");
}

# Add box manager records for old-style PROMOTXT1, PROMOHDR1, NEWSBOX, etc.
if ( table_exists("PROMO_BOXES") ) {
   $sub_res = mysql_query("SELECT * FROM PROMO_BOXES");
   $rows  = mysql_num_rows($sub_res);
   if($rows == 25){
      $news_cat_pull = "'','newsbox', '1', '', '', '', '', '', '', '', ''";
      if(!mysql_query("INSERT INTO PROMO_BOXES VALUES(".$news_cat_pull.")")){
         //echo mysql_error();
      }
      $promo_cat_pull = "'','promobox', '2', '', '', '', '', '', '', '', ''";
      if(!mysql_query("INSERT INTO PROMO_BOXES VALUES(".$promo_cat_pull.")")){
         //echo mysql_error();
      }
   }
}

# Add LAST_UPDATED field to SYSTEM_HOOK_ATTACHMENTS table -- so hooks can reprocess in same order as when installed
if ( table_exists("SYSTEM_PLUGINS") && !$rez = mysql_query("SELECT LAST_UPDATED FROM SYSTEM_HOOK_ATTACHMENTS") ) {
   mysql_query("ALTER TABLE SYSTEM_HOOK_ATTACHMENTS ADD COLUMN LAST_UPDATED VARCHAR(30)");
}

# Add LAST_UPDATED field to SYSTEM_PLUGINS table -- may come in handy later for auto-updating plugins
if ( table_exists("SYSTEM_PLUGINS") && !$rez = mysql_query("SELECT LAST_UPDATED FROM SYSTEM_PLUGINS") ) {
   mysql_query("ALTER TABLE SYSTEM_PLUGINS ADD COLUMN LAST_UPDATED VARCHAR(30)");
}

# Convert plugin table names to lower case
if ( table_exists("SYSTEM_PLUGINS") ) {
   if ( !mysql_query("RENAME TABLE SYSTEM_PLUGINS TO system_plugins") ) { echo mysql_error(); exit; }
}
if ( table_exists("SYSTEM_HOOK_ATTACHMENTS") ) {
   if ( !mysql_query("RENAME TABLE SYSTEM_HOOK_ATTACHMENTS TO system_hook_attachments") ) { echo mysql_error(); exit; }
}

# Add GOTO_CHECKOUT field to cart_options table
if ( table_exists("cart_options") && !$rez = mysql_query("SELECT GOTO_CHECKOUT FROM cart_options") ) {
   mysql_query("ALTER TABLE cart_options ADD COLUMN GOTO_CHECKOUT VARCHAR(5)");

   $qry = "UPDATE cart_options SET GOTO_CHECKOUT = 'no'";
   //if ( !mysql_query($qry) ) { echo "Unable to update cart_options GOTO_CHECKOUT field with default 'no' value...<br/>".mysql_error(); }
   mysql_query($qry);
}


# Add changelog field to system_plugins table
if ( !$rez = mysql_query("SELECT changelog FROM system_plugins") ) {
   mysql_query("ALTER TABLE system_plugins ADD COLUMN changelog BLOB");
   mysql_query("ALTER TABLE system_plugins ADD COLUMN release_date VARCHAR(50)");
   $report[] = "changelog and release_date fields added to system_plugins table";
}

# Add order_type field to cart_shipping_opts table
if ( table_exists("cart_shipping_opts") && !$rez = mysql_query("SELECT order_type FROM cart_shipping_opts") ) {
   $qry = "alter table cart_shipping_opts add column order_type varchar(50)";
   mysql_query($qry);
   $report[] = "order_type field added to cart_shipping_opts";

   $qry = "alter table cart_shipping_opts add column local_country varchar(255)";
   mysql_query($qry);
   $report[] = "local_country field added to cart_shipping_opts";

   # Update with default settings
   $qry = "UPDATE cart_shipping_opts SET order_type = 'local'";
   mysql_query($qry);
   $report[] = "Default local value set for order_type in cart_shipping_opts";

   # Make sure intl row exists in table
   $qry = "SELECT NOTICE FROM cart_shipping_opts";
   $rez = mysql_query($qry);
   $offline_notice = mysql_result($rez, 0);
   $qry = "INSERT INTO cart_shipping_opts (SHIP_METHOD, ST_GTHAN1, NOTICE, order_type)";
   $qry_vals_intl = " VALUES('Standard', '0.01', '".$offline_notice."', 'intl')";
   mysql_query($qry.$qry_vals_intl);
   $report[] = "New row for international (intl) shipping charges added to cart_shipping_opts";
}


# Add price_variants field to cart_products table
if ( table_exists("cart_products") && !$rez = mysql_query("SELECT sub_cats FROM cart_products") ) {

   # Add newschool variation fields
   $qry = "ALTER TABLE cart_products ADD COLUMN sub_cats BLOB";
   mysql_query($qry);
   $qry = "ALTER TABLE cart_products ADD COLUMN variant_names BLOB";
   mysql_query($qry);
   $qry = "ALTER TABLE cart_products ADD COLUMN variant_prices BLOB";
   mysql_query($qry);
   $qry = "ALTER TABLE cart_products ADD COLUMN num_variants VARCHAR(20)";
   mysql_query($qry);
	 mysql_query("update cart_products set num_variants = '12'");

   // Bug fix #0000624
   // Allows use of UPPER(OPTION_KEYWORDS)
   // blob does not support
   $qry = "ALTER TABLE cart_products modify OPTION_KEYWORDS char(255)";
   mysql_query($qry);
   $report[] = "sub_cats, variant_names, variant_prices, num_variants fields added to cart_products";

} // End if select sub_cats field from cart_products

# Add LOCAL_COUNTRY field to cart_options table
if ( table_exists("cart_options") && !$rez = mysql_query("SELECT LOCAL_COUNTRY FROM cart_options") ) {

   # Add newschool variation fields
   $qry = "ALTER TABLE cart_options ADD COLUMN LOCAL_COUNTRY BLOB";
   mysql_query($qry);

   $report[] = "LOCAL_COUNTRY field added to cart_options";

} // End if select LOCAL_COUNTRY field from cart_options


# Port existing old-style variants and sub cats over to new system
$qry = "SELECT * FROM cart_products WHERE (SUB_CAT1 != '' || VARIANT_NAME1 != '')";
$rez = mysql_query($qry);

$sku_affected_count = 0;
if ( mysql_num_rows($rez) > 0 ) {
   while ( $getSku = mysql_fetch_array($rez) ) {
      # Only update if new variant_names field is empty
      # ...have to check here instead of in select where statement because mysql won't detect empty blob fields on some servers
      if ( $getSku['variant_names'] == "" && $getSku['sub_cats'] == "" ) {

         # Build newschool variation arrays
         # Put together array of price variations and sub categories
         $sub_cats = array();
         $variant_names = array();
         $variant_prices = array();
         for ( $v = 1; $v <= 6; $v++ ) {
            $sub_cats[$v] = $getSku['SUB_CAT'.$v];
            $variant_names[$v] = $getSku['VARIANT_NAME'.$v];
            $variant_prices[$v] = $getSku['VARIANT_PRICE'.$v];
         }
         $sub_cats = serialize($sub_cats);
         $variant_names = serialize($variant_names);
         $variant_prices = serialize($variant_prices);

         # Update this record with newschool variation data
         $qry = "UPDATE cart_products SET";
         $qry .= " sub_cats = '".$sub_cats."'";
         $qry .= ", variant_names = '".$variant_names."'";
         $qry .= ", variant_prices = '".$variant_prices."'";
         $qry .= " WHERE PRIKEY = '".$getSku['PRIKEY']."'";
         mysql_query($qry);
         $sku_affected_count++;
      }
   }

   $report[] = "[".$sku_affected_count."] product skus converted to new-style price variation data format.";

} else {
   $report[] = "No existing product skus found with old-style sub cats/variants setup for them (so nothing to fix).";
}

# Make sure num_variants field isn't empty (missed this in original r34 wrap...causes variants from not displaying on the client side)
//Removed 4.9.3 r11 cameronallen
//$test_buildnum = eregi_replace("v4.9 r", "", $build['build_name']);
//if ( $test_buildnum > "33" ) {
//   $qry = "update cart_products SET num_variants = '12'";
//   if ( !mysql_query($qry) ) {
//      $report[] = "Could not update num_variants field: ".mysql_error();
//   } else {
//      $report[] = "Corrected cart_products records that had empty num_variants fields";
//   }
//}

# Remove obsolete and vulnerable files (i.e. copies of login.php)
$badfile = array();
$badfile[] = $_SESSION['docroot_path']."/sohoadmin/program/modules/editor/dialogs/php/uploadProcess.php";
$badfile[] = $_SESSION['docroot_path']."/sohoadmin/program/wizard/includes/login.php";
$badfile[] = $_SESSION['docroot_path']."/sohoadmin/program/webmaster/includes/login.php";
$badfile[] = $_SESSION['docroot_path']."/sohoadmin/program/modules/template_builder/includes/login.php";
$badfile[] = $_SESSION['docroot_path']."/sohoadmin/program/modules/upload/includes/login.php";
$badfile[] = $_SESSION['docroot_path']."/sohoadmin/program/includes/login.php";
$badfile[] = $_SESSION['docroot_path']."/sohoadmin/program/modules/site_templates/includes/login.php";
$badfile[] = $_SESSION['docroot_path']."/sohoadmin/program/modules/site_files/includes/login.php";
$badfile[] = $_SESSION['docroot_path']."/sohoadmin/program/modules/page_editor/includes/login.php";
$badfile[] = $_SESSION['docroot_path']."/sohoadmin/program/modules/page_editor/formlib/includes/login.php";
$badfile[] = $_SESSION['docroot_path']."/sohoadmin/program/modules/mods_full/statistics/includes/login.php";
$badfile[] = $_SESSION['docroot_path']."/sohoadmin/program/modules/page_editor/formlib/includes/login.php";
$badfile[] = $_SESSION['docroot_path']."/sohoadmin/program/modules/mods_full/statistics/includes/login.php";
$badfile[] = $_SESSION['docroot_path']."/sohoadmin/program/modules/mods_full/shopping_cart/includes/login.php";
$badfile[] = $_SESSION['docroot_path']."/sohoadmin/program/modules/mods_full/photo_album/includes/login.php";
$badfile[] = $_SESSION['docroot_path']."/sohoadmin/program/modules/mods_full/includes/login.php";
$badfile[] = $_SESSION['docroot_path']."/sohoadmin/program/modules/mods_full/event_calendar/includes/login.php";
$badfile[] = $_SESSION['docroot_path']."/sohoadmin/program/modules/mods_full/enewsletter/includes/login.php";
$badfile[] = $_SESSION['docroot_path']."/sohoadmin/program/modules/mods_full/database_manager/includes/login.php";
$badfile[] = $_SESSION['docroot_path']."/sohoadmin/program/modules/menu_system/includes/login.php";
$badfile[] = $_SESSION['docroot_path']."/sohoadmin/program/modules/includes/login.php";
$badfile[] = $_SESSION['docroot_path']."/sohoadmin/program/modules/forms_manager/includes/login.php";
$badfile[] = $_SESSION['docroot_path']."/sohoadmin/build_installer.php";

# Remove PinEdit files
$badfile[] = $_SESSION['docroot_path']."/sohoadmin/program/modules/editor/dialogs/php/uploadProcess.php";

function remove_Thisdir($path){
	foreach (glob($path) as $filename) {
		if(!is_dir($filename)) {
			unlink($filename);
		} else {
			rmdir($filename);
			remove_Thisdir($filename.'/*');
			rmdir($filename);
		}
	}
}
remove_Thisdir($_SESSION['docroot_path'].'/sohoadmin/program/modules/editor/*');
rmdir($_SESSION['docroot_path'].'/sohoadmin/program/modules/editor');
# END Remove PinEdit files

$badfiles_searched = count($badfile);
$badfiles_found = 0;
$badfiles_removed = 0;
for ( $x = 0; $x < count($badfile); $x++ ) {
   if ( file_exists($badfile[$x]) ) {
      $badfiles_found++;
      unlink($badfile[$x]);

      if ( file_exists($badfile[$x]) ) {
         $report[] = "Unable to remove: ".eregi_replace($_SESSION['docroot_path']."/", "", $badfile[$x])."";
      } else {
         $badfiles_removed++;
      }
   }
}
if ( $badfiles_removed != $badfiles_found ) {
   $report[] = "WARNING: It appears some obsolete/vulnerable files found on your site could not be removed (they should be listed above), presumably due to permissions settings on the various involved folders/files. You may want to remove them manually via FTP. Or just fix the permissions issue then come back here and click the \"Re-apply latest version compatitbility updates\" link again.";
}
$report[] = "Obsolete/vulnerable files:\\n....Searched For: [".$badfiles_searched."]\\n....Found: [".$badfiles_found."]\\n....Removed: [".$badfiles_removed."]";


# Increase size of smt_userdata data field to a blob
$qry = "select data from smt_userdata limit 1";
$rez = mysql_query($qry);
if ( mysql_field_type($rez, 0) == "string" ) {
   $qry = "alter table smt_userdata modify data BLOB";
   mysql_query($qry);

   $qry = "select data from smt_userdata limit 1";
   $rez = mysql_query($qry);
   if ( mysql_field_type($rez, 0) == "blob" ) {
      $report[] = "smt_userdata [data] field expanded to BLOB";
   } else {
      $report[] = "unable to change [data] field in smt_userdata to a BLOB field";
   }
}


# full_desc - Add field to cart_products table
if ( table_exists("cart_products") && !$rez = mysql_query("SELECT full_desc FROM cart_products") ) {
   $qry = "ALTER TABLE cart_products ADD COLUMN full_desc BLOB";
   mysql_query($qry);
   $report[] = "full_desc field added to cart_products table";
}

# USER_ACCESS_rightS fix -- as in "rightS" -> "RIGHTS"
if ( table_exists("USER_ACCESS_rightS") ) {
   mysql_query("RENAME TABLE USER_ACCESS_rightS TO USER_ACCESS_RIGHTS");
}

# Kill old nowiz.txt to on demo sites
if ( $_SESSION['demo_site'] == "yes" && file_exists("nowiz.txt") ) {
   unlink("nowiz.txt");
}


# v4.9 r54


# other_images - Add field to cart_products table
if ( table_exists("cart_products") && !$rez = mysql_query("SELECT other_images FROM cart_products") ) {
   $qry = "ALTER TABLE cart_products ADD COLUMN other_images BLOB";
   mysql_query($qry);
   $report[] = "other_images field added to cart_products table";
}

# v4.91 - SitePal build
# PROMO_BOXES > content_type - Add field
if ( table_exists("PROMO_BOXES") && !$rez = mysql_query("SELECT content_type FROM PROMO_BOXES") ) {
   # content_type
   $qry = "ALTER TABLE PROMO_BOXES ADD COLUMN content_type VARCHAR(50)";
   mysql_query($qry);
   $report[] = "content_type field added to PROMO_BOXES table";

   # Set all content_type values to "blog" (because no other option existed until "sitepal" added in this build)
   $qry = "UPDATE PROMO_BOXES set content_type = 'blog'";
   $rez = mysql_query($qry);

   # content_src
   $qry = "ALTER TABLE PROMO_BOXES ADD COLUMN content_src VARCHAR(255)";
   mysql_query($qry);
   $report[] = "content_src field added to PROMO_BOXES table";

   # Pull content source values for existing boxes out of serialized CONTENT array
   $qry = "select PRIKEY, CONTENT from PROMO_BOXES";
   $rez = mysql_query($qry);
   while ( $getBox = mysql_fetch_assoc($rez) ) {
      # Restore CONTENT array
      $contentArr = unserialize($getBox['CONTENT']);

      # Pull assigned blog category and stick in content_src field
      $qry = "update PROMO_BOXES set content_src = '".$contentArr['content']."' WHERE PRIKEY = '".$getBox['PRIKEY']."'";
      mysql_query($qry);
   }
   $report[] = "existing settings ported to new content_src field";

   # style - add field
   $qry = "ALTER TABLE PROMO_BOXES ADD COLUMN style BLOB";
   mysql_query($qry);
   $report[] = "style field added to PROMO_BOXES table";

   # SitePal plugin installed and set up?
   # Port over account_id -> accountid and uninstall sitepal plugin hooks
   # Only do this if built-in sitepal feature will be enabled
   if ( function_exists("sitepal_allowed") ) { // In case new smt_functions.php doesn't get written due to permissions
      if ( sitepal_allowed() ) {
         $sitepal = new userdata("sitepal");
         if ( $sitepal->get("account_id") != "" ) {
            $sitepal->set("accountid", $sitepal->get("account_id"));
            $report[] = "Existing SitePal account ID from SitePal plugin ported to built-in SitePal features.";
         }

         # Remove SitePal plugin hook attachments
         $qry = "delete from system_hook_attachments where PLUGIN_FOLDER = 'sitepal'";
         $rez = mysql_query($qry);
         $report[] = "SitePal plugin hooks removed so as not to conflict with new built-in SitePal support.";
      }
   } else {
      $report[] = "WARNING: Premissions on sohoadmin/program/includes/smt_functions.php may be too tight because it did not appear to get updated and as a result new essential functions may be unavailable.";
   }
} // End if new PROMO_BOXES fields exist for sitepal build


# Patch session exploit in templates not already patched
$unpatched = array();
$unpatched[] = "AGRICULTURE-FarmSickle_Autumn-None";
$unpatched[] = "CORPORATE-Asthetic_Brevity-Green";
$unpatched[] = "DELICIOUSLY-Clean-Blue";
$unpatched[] = "MEDICAL-Scrubs-none";
$unpatched[] = "NEUTRAL-Red_White_and_Chrome-none";
$badlook = count($unpatched);
$badfound = 0;
$reportmsg = "";
for ( $t=0; $t < $badlook; $t++ ) {
   $pathtoautomenufile = $_SESSION['docroot_path']."/sohoadmin/program/modules/site_templates/pages/".$unpatched[$t]."/pgm-auto_menu.php";

   # template installed on site?
   if ( file_exists($pathtoautomenufile) ) {
      $phpcontent = file_get_contents($pathtoautomenufile);

      # patched?
      if ( preg_match("/^<\?php\nerror_reporting\(E_PARSE\);\nif\(".'\$_GET\[\'_SESSION'."/i", $phpcontent) < 1 ) {
         # NO - patch now
         $badfound++;

         # Replace long open tag with short open tag if found
         $soso = $phpcontent;
         $soso = preg_replace("/^<\?php/i", '<?', $soso, 1);

         # Replace short open tag with long open + patch code
         $soso = preg_replace("/^<\?/i", "<?php\nerror_reporting(E_PARSE);\n".'if($_GET[\'_SESSION\'] != \'\' || $_POST[\'_SESSION\'] != \'\' || $_COOKIE[\'_SESSION\'] != \'\') { exit; }'."\n", $soso, 1);

         # Write modified contents back to file
         $phpwrite = fopen($pathtoautomenufile, "w");
         fwrite($phpwrite, $soso);
         fclose($phpwrite);
         $reportmsg .= $unpatched[$t]." - PATCHED\\n";

      } else {
         $reportmsg .= $unpatched[$t]." - OK\\n";

      } // End else already patched

   } else {
      # Template not installed
      $reportmsg .= $unpatched[$t]." - NOT INSTALLED\\n";
   } // end else file does not exist

} // End for loop through vulnerable templates

# Report if any templates had to be patched
if ( $badfound > 0 ) { $report[] = "Searched for [".$badlook."] potentially-exploitable template files...\\n".$reportmsg; }


# status (smt_sitepal_accounts)
if ( table_exists("smt_sitepal_accounts") && !$rez = mysql_query("SELECT status FROM smt_sitepal_accounts") ) {
   $qry = "ALTER TABLE smt_sitepal_accounts ADD COLUMN status VARCHAR(50)";
   mysql_query($qry);
   $report[] = "status field added to smt_sitepal_accounts table";
}


# v4.91 r4
#-------------------
# FTP info?
if ( !check_ftp() ) {
   $webmaster_pref = new userdata("webmaster_pref");

   if ( $webmaster_pref->get("ftp_username") == "" ) {
      # Will isp.conf.php values work?
      if ( check_ftp($_SESSION['dflogin_user'], $_SESSION['dflogin_pass']) ) {
         $webmaster_pref->set("ftp_username", $_SESSION['dflogin_user']);
         $webmaster_pref->set("ftp_password", $_SESSION['dflogin_pass']);
         $report[] = "Default login info in site config file works for FTP too. Saved to Global Settings.";
      }
   }
} // end if !check_ftp()


# v4.9.2 r1
#-------------------
# Check for missing MD5CODE values in sec_users table
if ( table_exists("sec_users") ) {
   $qry = "select * from sec_users where CHAR_LENGTH(MD5CODE) < 10";
   $rez = mysql_query($qry);

   if ( mysql_num_rows($rez) > 0 ) {
      $updatedcount = 0;
      while ( $getUser = mysql_fetch_assoc($rez) ) {
         # Build md5code value
         $md5code = $getUser['OWNER_EMAIL'].$getUser['OWNER_NAME'];
         $md5code = eregi_replace(" ", "", $md5code);
         $md5code = md5($md5code);

         # Update record
         $qry = "update sec_users set MD5CODE = '".$md5code."' WHERE PRIKEY = '".$getUser['PRIKEY']."'";
         mysql_query($qry);
         $updatedcount++;
      } // End while

      $report[] = "Missing MD5CODE bug discovered and corrected for [".$updatedcount."] member records.";
   } // End if
} // End if table_exists("sec_users") update missing MD5CODE


# v4.9.2 r7
# Set default: "Other Policies"
$cartpref = new userdata("cart");
if ( $cartpref->get("other_policy_title") == "" ) {
   $cartpref->set("other_policy_title", lang("Other Policies"));
   $report[] = "Default policy title set: Other Policies";
}


# v4.9.3 r1
# Kill any bad sec_user entries (i.e., no username or no password)
$qry = "select PRIKEY from sec_users where USERNAME = '' OR PASSWORD = ''";
$rez = mysql_query($qry);
$countRez = mysql_num_rows($rez);
if ( $countRez > 0 ) {
   $qry = "delete from sec_users where USERNAME = '' OR PASSWORD = ''";
   mysql_query($qry);
   $report[] = '['.$countRez.'] Bad member login records (empty username/password) removed.';
}

# Make copy of tmp_content folder as tmp_content_rollback
# ...only if tmp_content_rollback doesn't exist already
$tmp_content = $_SESSION['docroot_path'].'/sohoadmin/tmp_content';
$tmp_content_rollback = $_SESSION['docroot_path'].'/sohoadmin/tmp_content_rollback';
if ( !is_dir($tmp_content_rollback) ) {
   copyr($tmp_content, $tmp_content_rollback);

   # Make sure new dir exists now
   if ( is_dir($tmp_content_rollback) ) {
      $report[] = "Page content files backed up in tmp_content_rollback dir.";
   } else {
      $report[] = "Unable to create tmp_content_rollback dir. Check permissions on sohoadmin folder.";
   }
}


# v4.9.3 r15
########################################################################
###Cameron Fix, Change to new CUSTOMER COMMENTS SYSTEM for Shopping Cart
########################################################################
		//MAKE SURE CART_COMMENTS TABLE EXITS	4.9.3 r15
		$match = 0;
		$result = mysql_list_tables($_SESSION['db_name']);
		$i = 0;
		while ($i < mysql_num_rows ($result)) {
			$tb_names[$i] = mysql_tablename ($result, $i);
			if (strtolower($tb_names[$i]) == "cart_comments") { $match = 1; }
			$i++;
		}

		// DOES NOT EXIST; CREATE TABLE NOW
		## ====================================================
		if ($match != 1) {
			$qry = "CREATE TABLE cart_comments (";
			$qry .= " PRIKEY INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,";
			$qry .= " PROD_ID INT,";
			$qry .= " COMMENT_TITLE VARCHAR(255),";
			$qry .= " COMMENT BLOB,";
			$qry .= " RATING VARCHAR(255),";
			$qry .= " NAME VARCHAR(255),";
			$qry .= " LOCATION VARCHAR(255),";
			$qry .= " COMMENT_DATE DATETIME,";
			$qry .= " STATUS VARCHAR(255),";
			$qry .= " COMMENT_HTML BLOB,";
			$qry .= " AUTH_KEY VARCHAR(255)";
			$qry .= ")";
			//ECHO $qry."<br/>";
			if (!mysql_db_query($_SESSION['db_name'],$qry)){
				//				echo "Could not create table cart_comments!<br>";
				//				echo "Mysql says (".mysql_error().")";
				//				exit;
			}
		}

	$importcount = 0;
	$appcomment_file = "CART_*";
	foreach (glob($_SESSION['doc_root'].'/shopping/'.$appcomment_file ) as $filename) {
		if(file_exists($filename)){
			$filear = explode('.', $filename);
			if($filear['1'] == 'REVIEW'){
				$approved[] = $filename;
			} else {
				$not_approved[] = $filename;
			}
		}
	}


	foreach($approved as $var=>$val){
		$comfilename = $val;
		$val = str_replace($_SESSION['doc_root'].'/shopping/CART_', '', $val);
		$prod_idar = explode('.', $val);
		$prod_id = $prod_idar['0'];
		$uniquekey = $prod_idar['1'];


		$file = fopen("$comfilename", "r");
		$OK_COMMENT = fread($file,filesize($comfilename));
		fclose($file);

		if($OK_COMMENT != ''){
			$OK_COMMENT = str_replace("'", "\'", $OK_COMMENT);
			$qry = "INSERT INTO cart_comments (";
			$qry .= " PROD_ID,";
			$qry .= " COMMENT_TITLE,";
			$qry .= " COMMENT,";
			$qry .= " RATING,";
			$qry .= " NAME,";
			$qry .= " LOCATION,";
			$qry .= " COMMENT_DATE,";
			$qry .= " STATUS,";
			$qry .= " COMMENT_HTML,";
			$qry .= " AUTH_KEY) ";
			$qry .= " VALUES('".$prod_id."','multiple','multiple','multiple','multiple','multiple', NOW(),'approved','".$OK_COMMENT."', 'multiple')";
			if(mysql_query($qry)){
				$importcount++;
				unlink($comfilename);
			}
		}
	}


	foreach($not_approved as $var=>$val){
		$comfilename = $val;
		$val = str_replace($_SESSION['doc_root'].'/shopping/CART_', '', $val);
		$prod_idar = explode('.', $val);

		$uniquekey = $prod_idar['0'];
		$prod_id = $prod_idar['1'];

		$file = fopen("$comfilename", "r");
		$OK_COMMENT = fread($file,filesize($comfilename));
		fclose($file);

		if($OK_COMMENT != ''){
			$OK_COMMENT = str_replace("'", "\'", $OK_COMMENT);
			$qry = "INSERT INTO cart_comments (";
			$qry .= " PROD_ID,";
			$qry .= " COMMENT_TITLE,";
			$qry .= " COMMENT,";
			$qry .= " RATING,";
			$qry .= " NAME,";
			$qry .= " LOCATION,";
			$qry .= " COMMENT_DATE,";
			$qry .= " STATUS,";
			$qry .= " COMMENT_HTML,";
			$qry .= " AUTH_KEY) ";
			$qry .= " VALUES('".$prod_id."','multiple','multiple','multiple','multiple','multiple', NOW(),'not_approved','".$OK_COMMENT."', '".$uniquekey ."')";
			if(mysql_query($qry)){
				unlink($comfilename);
				$importcount++;
			}
		}
	}

	if($importcount > 0){
		$report[]	= 'Imported '.$importcount.' customer comment files into new cart_comments database structure.';
	}
########################################################################
###END Cameron Fix, Change to new CUSTOMER COMMENTS SYSTEM for Shopping Cart
########################################################################


# v4.9.3 r18
# Add custom start/end time fields to calendar_events table
#=========================================================================================================================
# custom_start
$qry = "select custom_start from calendar_events";
if ( !$rez = mysql_query($qry) ) {
   $qryStr = 'alter table calendar_events change FUTURE1 custom_start varchar(255)';

   # custom_start
   if ( $resRes = mysql_query($qryStr) ) {
      $report[] = 'custom_start field added to calendar_events table.';
   } else {
      $report[] = 'ERROR: Could not add custom_start field to calendar_events table because ['.addslashes(mysql_error()).'].';
   }

} else {
//   $report[] = 'custom_start field already exists in calendar_events table.';
}

# custom_end
$qry = "select custom_end from calendar_events";
if ( !$rez = mysql_query($qry) ) {
   $qryStr = 'alter table calendar_events change FUTURE2 custom_end varchar(255)';

   # custom_end
   if ( $resRes = mysql_query($qryStr) ) {
      $report[] = 'custom_end field added to calendar_events table.';
   } else {
      $report[] = 'ERROR: Could not add custom_end field to calendar_events table because ['.addslashes(mysql_error()).'].';
   }

} else {
//   $report[] = 'custom_end field already exists in calendar_events table.';
}
# END adding custom_start/end fields to calendar_events table
#=========================================================================================================================


# v4.9.3 r21
# Increase size of OPTION_DETAILPAGE field to 255 (up from 30)
if ( table_exists('cart_products') ) {
   $qry = "select OPTION_DETAILPAGE from cart_products limit 1";
   $rez = mysql_query($qry);
   if ( mysql_field_len($rez, 0) == 30 ) {
      $qry = "alter table cart_products modify OPTION_DETAILPAGE VARCHAR(255)";
      mysql_query($qry);

      $qry = "select OPTION_DETAILPAGE from cart_products limit 1";
      $rez = mysql_query($qry);
      if ( mysql_field_len($rez, 0) == 255 ) {
         $report[] = "OPTION_DETAILPAGE field expanded to 255 characters";
      } else {
         $report[] = "unable to increase length of OPTION_DETAILPAGE field to a 255 characters";
      }
   }
}


# v4.9.3 r29
# Update bad 'link' column values in site_pages table. They all need to be md5(pagename), but because of a bug some of them were just the word 'link', screwing up forms library goto page
$qryStr = "select * from site_pages where link = 'link'";
$qryRez = mysql_query($qryStr);
$updatecountInt = 0;
while ( $getArr = mysql_fetch_assoc($qryRez) ) {
	$qryStr = "UPDATE site_pages set link = '".md5($getArr['page_name'])."' where page_name = '".$getArr['page_name']."'";
	mysql_query($qryStr);
	$updatecountInt++;
}
$report[] = "[".$updatecountInt."] bad page links corrected in site_pages table";


?>