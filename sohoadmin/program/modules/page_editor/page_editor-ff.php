<?php
error_reporting(E_PARSE);
if($_GET['_SESSION'] != '' || $_POST['_SESSION'] != '' || $_COOKIE['_SESSION'] != '') { exit; }

session_start();

##########################################################################################
## This script is no longer used as the Firefox version of page editor.
## Redirect user to new page_editor.php
## 
##########################################################################################

$daPage = $_GET['currentPage'];
$no_cache = microtime();
header ("Location: page_editor.php?currentPage=".$daPage."&nocache=".$no_cache);
exit;

?>