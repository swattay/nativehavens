<?
#=================================================================================================================================
# Soholaunch v4.91 - SitePal Feature
# Check for/create sitepal-related db tables
# smt_sitepal_accounts - id, user, pass of verified SitePal account(s); any other info stored locally vs. pulled every time
#=================================================================================================================================

# smt_sitepal_accounts
# PURPOSE: Storing info neccessary to qry sitepal api on behalf of user's sitepal account(s)
$tablename = "smt_sitepal_accounts";
if ( !table_exists($tablename) ) {
   $wDeez = "prikey INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT";
   $wDeez .= ", account_id VARCHAR(255)";
   $wDeez .= ", username VARCHAR(255)";
   $wDeez .= ", password VARCHAR(255)";
   $wDeez .= ", status VARCHAR(50)";
   $wDeez .= ", account_info BLOB";
   $wDeez .= ", date_created VARCHAR(40)";
   $wDeez .= ", account_title VARCHAR(255)";

   $create_qry = "CREATE TABLE ".$tablename." (".$wDeez.")";
   if ( !mysql_db_query($db_name, $create_qry) ) { echo "Unable to create ".$tablename." table!<br>".mysql_error(); }
}

# smt_sitepal_rules
# PURPOSE: Contains various behavior "rules" defined by user like "on this page show this scene"
$tablename = "smt_sitepal_rules";
if ( !table_exists($tablename) ) {
   $wDeez = "prikey INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT";
   $wDeez .= ", page_name VARCHAR(255)";
   $wDeez .= ", account_id VARCHAR(20)";
   $wDeez .= ", scene_id VARCHAR(20)";
   $wDeez .= ", width VARCHAR(20)";
   $wDeez .= ", height VARCHAR(20)";
   $wDeez .= ", bgcolor VARCHAR(20)";
   $wDeez .= ", scene_name VARCHAR(255)";

   $create_qry = "CREATE TABLE ".$tablename." (".$wDeez.")";
   if ( !mysql_db_query($db_name, $create_qry) ) { echo "Unable to create ".$tablename." table!<br>".mysql_error(); }

   # Insert a row for the default record - set to "no display" by default
   $data = array();
   $data['page_name'] = "default";
   $data['scene_id'] = "none";
   $myqry = new mysql_insert($tablename, $data);
   $myqry->insert();
}

# smt_sitepal_rules
# Does default rule exist?
$qry = "select prikey from smt_sitepal_rules where page_name = 'default'";
$rez = mysql_query($qry);
if ( mysql_num_rows($rez) < 1 ) {
   # Insert a row for the default record - set to "no display" by default
   $data = array();
   $data['page_name'] = "default";
   $data['scene_id'] = "none";
   $myqry = new mysql_insert($tablename, $data);
   $myqry->insert();
}

# smt_sitepal_rules
# scene_thumb field exist?
$qry = "select scene_thumb from smt_sitepal_rules limit 1";
if ( !mysql_query($qry) ) {
   $qry = "alter table smt_sitepal_rules add column scene_thumb varchar(255)";
   mysql_query($qry);
}
?>