<?php
error_reporting(E_PARSE);
if($_GET['_SESSION'] != '' || $_POST['_SESSION'] != '' || $_COOKIE['_SESSION'] != '') { exit; }
session_start();
$filenames = '';
foreach (glob($_SESSION['doc_root'].'/images/*') as $filename) {
	$filenames .= basename($filename)."\n";
}
$filenames = eregi_replace("\n$", '', $filenames);
$imglist_AR = explode("\n", $filenames);

usort($imglist_AR, "strnatcasecmp");
$jsdisp = "var tinyMCEImageList = new Array( \n";
foreach($imglist_AR as $imgv=>$imgn) {
	$jsdisp .= "[\"".$imgn."\", \"images/".$imgn."\"],\n";
}
$jsdisp = eregi_replace("\,\n$", '', $jsdisp);
echo $jsdisp .= "); \n";
?>