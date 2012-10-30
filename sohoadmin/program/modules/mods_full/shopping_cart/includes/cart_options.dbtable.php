<?php
error_reporting(E_PARSE);
if($_GET['_SESSION'] != '' || $_POST['_SESSION'] != '' || $_COOKIE['_SESSION'] != '') { exit; }


#=====================================================================================
# CREATE DB TABLE: cart_options
# This script creates the cart_options table and inserts the default data
# It is included by shopping_cart.php if cart_options table does not exist
#=====================================================================================

// =======================================================================
// No Reason for a PriKey Field. This is simply a single record table for
// storing setup options for the shopping cart display; etc. These are
// converted to actual variables in the client side runtime files. If you
// notice, we will read this table into memory and convert the field names
// into vars and that's how we do the if/then testing on the client side.
// =======================================================================

if ( !table_exists("cart_options") ) {
   mysql_db_query($_SESSION['db_name'], "CREATE TABLE cart_options (

      PAYMENT_CREDIT_CARDS CHAR(30),
      PAYMENT_CHECK_ONLY CHAR(1),
      PAYMENT_CATALOG_ONLY CHAR(1),
      PAYMENT_PROCESSING_TYPE CHAR(7),
      PAYMENT_CURRENCY_TYPE CHAR(5),
      PAYMENT_CURRENCY_SIGN CHAR(12),
      PAYMENT_VPARTNERID CHAR(50),
      PAYMENT_VLOGINID CHAR(50),
      PAYMENT_INCLUDE CHAR(150),
      PAYMENT_SSL CHAR(255),

      BIZ_PAYABLE CHAR(100),
      BIZ_ADDRESS_1 CHAR(100),
      BIZ_ADDRESS_2 CHAR(100),
      BIZ_CITY CHAR(100),
      BIZ_STATE CHAR(100),
      BIZ_POSTALCODE CHAR(100),
      BIZ_COUNTRY CHAR(100),
      BIZ_PHONE CHAR(100),
      BIZ_VERIFY_COMMENTS BLOB,
      BIZ_EMAIL_NOTICE BLOB,
      BIZ_INVOICE_HEADER BLOB,

      DISPLAY_HEADERBG CHAR(7),
      DISPLAY_HEADERTXT CHAR(7),
      DISPLAY_CARTBG CHAR(7),
      DISPLAY_CARTTXT CHAR(7),
      DISPLAY_WELCOME BLOB,
      DISPLAY_RESULTS CHAR(2),
      DISPLAY_RESULTSORT CHAR(50),
      DISPLAY_COLPLACEMENT CHAR(1),
      DISPLAY_SEARCH CHAR(1),
      DISPLAY_USERBUTTON CHAR(55),
      DISPLAY_ADDCARTBUTTON CHAR(1),
      DISPLAY_LOGINBUTTON CHAR(1),
      DISPLAY_CATEGORIES CHAR(1),
      DISPLAY_COMMENTS CHAR(1),
      DISPLAY_EMAILFRIEND CHAR(1),
      DISPLAY_REMEMBERME CHAR(1),
      DISPLAY_STATE CHAR(25),
      DISPLAY_ZIP VARCHAR(150),
      DISPLAY_REQUIRED BLOB,

      INVOICE_INCLUDE CHAR(150),

      LOCAL_COUNTRY BLOB,
      CHARGE_VAT CHAR(4),
      VAT_REG CHAR(100),
      CSS BLOB,
      GOTO_CHECKOUT VARCHAR(20)

      )") || die("Could not create options table in $db_name");


   // ----------------------------------------------------------
   // Since this is apparently the first time this module has
   // been access, let's populate the options table with our
   // recommended defaults for easier setup and usage
   // ----------------------------------------------------------

   // Should be 45 fields (starting at 1) to CSS

   # Build default data for css field
   $cartcss = array('table_bgcolor'=>"FFFFFF", 'table_textcolor'=>"000000");
   $cartcss = serialize($cartcss);

   mysql_query("INSERT INTO cart_options VALUES(

      'Visa;Mastercard;Amex;Discover;',
      '',
      '',
      'offline','USD','\$',
      '','','','',
      '$getSpec[df_company]',
      '$getSpec[df_address1]',
      '$getSpec[df_address2]',
      '$getSpec[df_city]',
      '$getSpec[df_state]',
      '$getSpec[df_zip]',
      '$getSpec[df_country]',
      '$getSpec[df_phone]',
      '$getSpec[df_email]',
      '$getSpec[df_email]',
      'Thank you for your order!',
      '708090','F5F5F5','EFEFEF','000000','',
      '6','PROD_NAME','R','Y','','N',
      'N','Y','Y','Y','Y','usmenu','zippostal','yes',
      '','No Default Country','no','vatnum','$cartcss', 'no')");

} // End if !table_exists

?>