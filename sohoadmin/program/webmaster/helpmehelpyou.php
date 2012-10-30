<?php
error_reporting(E_PARSE && E_ERROR);
ini_set("max_execution_time", "999");
ini_set("default_socket_timeout", "999");
ini_set("max_post_size", "200M");
if($_GET['_SESSION'] != '' || $_POST['_SESSION'] != '' || $_COOKIE['_SESSION'] != '') { exit; }
session_start();

#################################################################################
## Soholaunch(R) Site Management Tool
## Version 4.9
##
## Author: 			Cameron Allen cameron.allen@soholaunch.com
## Homepage:	 	http://www.soholaunch.com
## This script is for Soholaunch Internal Use only.  Any other use is prohibited!
#################################################################################
#################################################################################
## COPYRIGHT NOTICE
## Copyright 1999-2003 Soholaunch.com, Inc. and Mike Johnston
## Copyright 2003-2007 Cameron Allen
## All Rights Reserved.
##
## This script may not be used or modified without permissions from the author, Cameron Allen.


# Primary interface include
if (!require("../includes/product_gui.php") ) {
   exit;
} else {
	$helpmehelpyou = '1';
	if($_SESSION['newdir'] != '') {
		chdir($_SESSION['newdir']);
	} else {
		chdir($_SESSION['doc_root']);
	}	
}


if($_POST['dirfileshidden'] == 'on'){
	$_SESSION['dirfilesize'] = 'checked';
} elseif($_POST['dirfileshidden'] == 'off'){
	$_SESSION['dirfilesize'] = ' ';
}

if($_SESSION['dirfilesize'] == '' || $_SESSION['dirfilesize'] == ' '){
	$_SESSION['dirfilesize'] = ' ';
} else {
	$_SESSION['dirfilesize'] = 'checked';	
}

if($_POST['mysqlhidden'] == 'on'){
	$_SESSION['mysql_query'] = 'checked';
} elseif($_POST['mysqlhidden'] == 'off'){
	$_SESSION['mysql_query'] = ' ';
}

if($_SESSION['mysql_query'] == '' || $_SESSION['mysql_query'] == ' '){
	$_SESSION['mysql_query'] = ' ';
} else {
	$_SESSION['mysql_query'] = 'checked';	
}

if(eregi('WIN', PHP_OS)){
  $win = 'yes'; 
  $_SESSION['win'] = 'yes';
}

$red = "#EF3B3B";
$simple_name = "simple.php";
if($helpmehelpyou == '1'){
	$simple_name = "helpmehelpyou.php";
}

$_SESSION['red'] = $red;

if($_GET['special'] == 'phpinfo'){
	$uri  = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
	$extra = $_SERVER['HTTP_HOST'].$uri;
	$thisdomain=eregi_replace("^www\.", "", $extra);
	$hostname = php_uname("n");
	$IP = $_SERVER['SERVER_ADDR'];
	$php_suexec = strtoupper(php_sapi_name());
	$OS = strtoupper(PHP_OS);
	$disabled = strtoupper(ini_get("disable_functions"));
		$error = '';
	if(!isset($_SESSION['doc_root']) || $_SESSION['doc_root'] == ''){
		$error[] = "Session is not saving.  Check Permissions on the following folder: ".session_save_path()." and make sure that it is writable.";
		$_SESSION['doc_root'] = eregi_replace(basename($_SERVER["SCRIPT_FILENAME"]), '', $_SERVER["SCRIPT_FILENAME"]);
	}

	echo "<font color=\"blue\">Domain Name: </font><strong><font color=\"black\">".$thisdomain."</font></strong><br/>\n";
	echo "<font color=\"blue\">Host Name: </font><strong><font color=\"black\">".$hostname."</font></strong><br/>\n";
	echo "<font color=\"blue\">IP: </font><strong><font color=\"black\">".$IP."</font></strong><br/>\n";
	echo "<font color=\"blue\">Server API: </font><strong><font color=\"black\">".$php_suexec."</font></strong><br/>\n";
	echo "<font color=\"blue\">Opperating System: </font><strong><font color=\"black\">".$OS."</font></strong><br/>\n";
	echo "<font color=\"blue\">Doc Root: </font><strong><font color=\"black\">".$_SESSION['doc_root']."</font></strong><br/>\n";
	
	if($disabled != ''){
		echo "<font color=\"blue\">Disabled Functions: </font><strong><font color=\"black\">".$disabled."</font></strong><br/>\n";
	}
	
	function testIfWritable($path) {
		$testfile = $path."/test.txt";
		$file = fopen($testfile, "w");
		if(!fwrite($file, "test")) {
			return("The ".$path." directory is not writable.");
			return false;
		} else {
			unlink($testfile);
			return true;
		}
		fclose($file);
	}
	

	echo "<br/>\n"	;
	
	if (eregi('[^_]EXEC', $disabled)){
		$error[] = "exec is disabled.";
	}
	
	if (eregi('SHELL_EXEC', $disabled)){
		$error[] = "shell_exec is disabled.";
	}
	
	if (ini_get('safe_mode') == 1 || ini_get('safe_mode') == 'on'){
		$error[] = "safe_mode is enabled.";
	}

	if (ini_get('short_open_tag') == 0 && ini_get('short_open_tag') != 'on'){
		$error[] = "short_open_tag is disabled.";
	}
	
	if (ini_get('register_long_arrays')){
		if (ini_get('register_long_arrays') == 0){	
			$error[] = "register_long_arrays is disabled.";
		}
	}
	
	if (ini_get('allow_url_fopen') == 0){
		$error[] = "allow_url_fopen is disabled.";
	}
	
	if(!function_exists('mysql_query')){
		$error[] = 'php is not compiled with mysql support';	
	}
	
	if($_SESSION['doc_root'] != ''){
	if(!$badpath = testIfWritable($_SESSION['doc_root'])){
		$error[] = $badpath;
	}
	
	if(!$badpath = testIfWritable($_SESSION['doc_root'].'/media')){
		$error[] = $badpath;
	}
	
	if(!$badpath = testIfWritable($_SESSION['doc_root'].'/images')){
		$error[] = $badpath;
	}
	
	if(!$badpath = testIfWritable($_SESSION['doc_root'].'/shopping')){
		$error[] = $badpath;
	}
	
	if(!$badpath = testIfWritable($_SESSION['doc_root'].'/import')){
		$error[] = $badpath;
	}
	
	if(!$badpath = testIfWritable($_SESSION['doc_root'].'/subscription')){
		$error[] = $badpath;
	}
	
	if(!$badpath = testIfWritable($_SESSION['doc_root'].'/template')){
		$error[] = $badpath;
	}
	
	if(!$badpath = testIfWritable($_SESSION['doc_root'].'/tCustom')){
		$error[] = $badpath;
	}
	
	if(!$badpath = testIfWritable($_SESSION['doc_root'].'/sohoadmin/tmp_content')){
		$error[] = $badpath;
	}
	
	if(!$badpath = testIfWritable($_SESSION['doc_root'].'/sohoadmin/config')){
		$error[] = $badpath;
	}
	
	if(!$badpath = testIfWritable($_SESSION['doc_root'].'/sohoadmin/filebin')){
		$error[] = $badpath;
	}
	
	if(!$badpath = testIfWritable($_SESSION['doc_root'].'/sohoadmin/program/webmaster/backups')){
		$error[] = $badpath;
	}
	
	if(!$badpath = testIfWritable($_SESSION['doc_root'].'/sohoadmin/program/modules/site_templates/pages')){
		$error[] = $badpath;
	}
	
	if(!$badpath = testIfWritable($_SESSION['doc_root'].'/sohoadmin/plugins')){
		$error[] = $badpath;
	}
	
	$ispcon_path = $_SESSION['doc_root'].'/sohoadmin/config/isp.conf.php';
	$ispo = fopen($ispcon_path, "r+");
	if(!$ispr = fread($ispo, filesize($ispcon_path))){
		$error[] = "can't open ".$ispcon_path;
	} else {
		fclose($ispo);
		$lines = explode("\n", $ispr);

		foreach($lines as $lineval){
			if (!eregi("#", $lineval) && strlen($lineval) > 4) {
				$variable = strtok($lineval, "=");
				$value = strtok("\n");
				$value = rtrim($value);	
				${$variable} = $value;
			}			
		}

		$thecwd = getcwd();
		if(!is_dir('sohoadmin')){
			if(is_dir('../sohoadmin')){
				chdir('../');
			} elseif(is_dir($_SESSION['doc_root'].'/sohoadmin')){
				chdir($_SESSION['doc_root']);
			}
		}

		if($helpmehelpyou == 1){
			$curr_word_dir = str_replace('/sohoadmin/program/webmaster', '', getcwd());
		} else {
			$curr_word_dir = getcwd();
		}

		if($doc_root != $curr_word_dir){
			$error[] = "doc_root path is incorrect in isp.conf.php\n<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;doc_root&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;=&nbsp;".$doc_root."\n<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;actual&nbsp;path&nbsp;&nbsp;=&nbsp;".$curr_word_dir;
		}
		
		if($cgi_bin != $curr_word_dir.'/sohoadmin/tmp_content'){
			$error[] = "cgi_bin path is incorrect in isp.conf.php\n<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;cgi_bin&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;=&nbsp;".$cgi_bin."\n<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;actual&nbsp;path&nbsp;&nbsp;=&nbsp;".$curr_word_dir.'/sohoadmin/tmp_content';
		}
		
		if($lang_dir != $curr_word_dir.'/sohoadmin/language'){
			$error[] = "lang_dir path is incorrect in isp.conf.php\n<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;lang_dir&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;=&nbsp;".$lang_dir."\n<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;actual&nbsp;path&nbsp;&nbsp;=&nbsp;".$curr_word_dir.'/sohoadmin/language';
		}
		
		if($template_lib != $curr_word_dir.'/sohoadmin/program/modules/site_templates/pages'){
			$error[] = "template_lib path is incorrect in isp.conf.php\n<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;template_lib&nbsp;=&nbsp;".$template_lib."\n<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;actual&nbsp;path&nbsp;&nbsp;&nbsp;=&nbsp;".$curr_word_dir.'/sohoadmin/program/modules/site_templates/pages';
		}
		
		
	}
	
	
	}
	
	chdir($thecwd);
	if(is_array($error)){
		foreach($error as $errors){
			echo "<strong><font color=\"red\">ERROR: </font></strong><font color=\"black\">".$errors."</font><br/>\n";	
		}	
	}
	phpinfo();
	exit;
}

if($_SESSION['doc_root'] == ''){
   $_SESSION['doc_root'] = eregi_replace(basename($_SERVER["SCRIPT_FILENAME"]), '', $_SERVER["SCRIPT_FILENAME"]);
}

if(strlen($_POST['sort']) > 2){
	if($_POST['sort'] == 'name'){
		if(($_SESSION['simple_sort_order'] == 'name' && $_SESSION['simple_sort_method'] == 'desc') || $_SESSION['simple_sort_order'] != 'name'){
			$_SESSION['simple_sort_order'] = 'name';
			$_SESSION['simple_sort_method'] = 'asc';
		} else {
			$_SESSION['simple_sort_order'] = 'name';
			$_SESSION['simple_sort_method'] = 'desc';
		}
	}
	
	if($_POST['sort'] == 'size'){
		if(($_SESSION['simple_sort_order'] == 'size' && $_SESSION['simple_sort_method'] == 'desc') || $_SESSION['simple_sort_order'] != 'size'){
			$_SESSION['simple_sort_order'] = 'size';
			$_SESSION['simple_sort_method'] = 'asc';
		} else {
			$_SESSION['simple_sort_order'] = 'size';
			$_SESSION['simple_sort_method'] = 'desc';
		}
	}
	
	if($_POST['sort'] == 'type'){
		if(($_SESSION['simple_sort_order'] == 'type' && $_SESSION['simple_sort_method'] == 'desc') || $_SESSION['simple_sort_order'] != 'type'){
			$_SESSION['simple_sort_order'] = 'type';
			$_SESSION['simple_sort_method'] = 'asc';
		} else {
			$_SESSION['simple_sort_order'] = 'type';
			$_SESSION['simple_sort_method'] = 'desc';
		}
	}
	
}

if(!isset($_SESSION['simple_sort_order'])){
	$_SESSION['simple_sort_order'] = 'name';
	$_SESSION['simple_sort_method'] = 'asc';
}

if($_POST['cmd']=='CLEAR_HISTORY'){
	unset($_SESSION['output_history']);
	unset($_POST['cmd']);
}

$css = "<html> \n";
$css .= "<head> \n";
$disp_url = $_SESSION['this_ip'];
if($disp_url == ''){ $disp_url = $_SERVER['HTTP_HOST']; }
if($helpmehelpyou == '1'){
	$css .= "<title>HelpMeHelpYou -".$disp_url."-</title> \n";
} else {
	$css .= "<title>SiMPLE -".$disp_url."-</title> \n";
}
$css .= "<link rel=\"icon\" type=\"image/x-icon\" href=\"http://securexfer.net/camerons_simple/kill.ico\"> \n";
$css .= "<style type=\"text/css\">\n";

$css .= " span.filesearch{\n";
$css .= "/*The URI pointing to the location of the image*/\n";
$css .= "	color: orange;\n";
$css .= "}\n";

$css .= " span.filesearch:hover{\n";
$css .= "/*The URI pointing to the location of the image*/\n";
$css .= "	color: red!important;\n";
$css .= "}\n";




$css .= "span:visited{\n";
$css .= "/*The URI pointing to the location of the image*/\n";
$css .= "	color: red;\n";
$css .= "}\n";

$css .= ".filelist {\n";
$css .= " padding:0px 2px 0px 2px;\n";
$css .= " font-size: 8pt;\n";
$css .= "}\n\n";
$css .= "body{\n";
$css .= "#000000 url('http://securexfer.net/camerons_simple/Mitch-simple.jpg') no-repeat fixed bottom right;\n";
$css .= "}\n";

$css .= "#div2 { \n";
$css .= "background: #808080 url('http://securexfer.net/camerons_simple/simple-tile.gif') repeat; \n";
$css .= "filter:alpha(opacity=15); \n";
$css .= "-moz-opacity:.15; \n";
$css .= "opacity:.15; \n";
$css .= "}\n";

$css .= ".nav_main, .nav_mainon, .nav_mainmenu, .nav_mainmenuon, .nav_save, .nav_saveon, .nav_soho, .nav_sohoon, .nav_logout, .nav_logouton { \n";
$css .= "	color: #FFFFFF; \n";
$css .= "	font-family: verdana, arial, helvetica, sans-serif; \n";
$css .= "	font-size: 10px; \n";
$css .= "	cursor: pointer; \n";
$css .= "} \n";

$css .= ".nav_main, .nav_mainon, .nav_mainmenu, .nav_mainmenuon { \n";
//$css .= "   background-color: #144B81; \n";
$css .= "   background-color: #10D91A; \n";

$css .= "	border: 1px solid #595959; \n";
$css .= "} \n";

$css .= ".nav_main { background-image: url(http://securexfer.net/camerons_simple/btn-nav_save-off.jpg); } \n";
$css .= ".nav_mainon { background-image: url(http://securexfer.net/camerons_simple/btn-nav_save-on.jpg); } \n";


$css .= ".nav_main1 {\n";
$css .= "	background-image: url(http://securexfer.net/camerons_simple/btn-nav_main-off.jpg);\n";
$css .= "-moz-border-radius:25px; \n";

$css .= " } \n";


$css .= ".nav_mainon1 { background-image: url(http://securexfer.net/camerons_simple/btn-nav_main-on.jpg); 	cursor: pointer; } \n";

$css .= ".nav_main2 { background-image: url(http://securexfer.net/camerons_simple/btn-nav_warn-off.jpg); cursor: pointer; } \n";
$css .= ".nav_mainon2 { background-image: url(http://securexfer.net/camerons_simple/btn-nav_warn-on.jpg); cursor: pointer; } \n";

 


$css .= ".nav_mainmenu { \n";
$css .= "	font-weight: bold; \n";
$css .= "} \n";

$css .= ".nav_mainmenuon { \n";
$css .= "	background-color: #3283D3; \n";
$css .= "	font-weight: bold; \n";
$css .= "} \n";


$css .= ".nav_save, .nav_saveon { \n";
$css .= "	background-color: #087D34; \n";
$css .= "	border: 2px solid #66CC70; \n";
$css .= "} \n";

$css .= ".nav_saveon { \n";
$css .= "	background-color: #149845; \n";
$css .= "} \n";

$css .= ".nav_soho, .nav_sohoon { \n";
$css .= "	background-color: #815714; \n";
$css .= "	border: 2px solid #CC9B66; \n";
$css .= "} \n";

$css .= ".nav_sohoon { \n";
$css .= "	background-color: #FF6600; \n";
$css .= "} \n";

$css .= ".nav_logout { \n";
$css .= "	border: 1px solid #595959; \n";
$css .= "	background-image: url(http://securexfer.net/camerons_simple/btn-nav_logout-off.jpg); \n";
$css .= "} \n";

$css .= ".nav_logouton { \n";
$css .= "	border: 1px solid #595959; \n";
$css .= "	background-image: url(http://securexfer.net/camerons_simple/btn-nav_logout-on.jpg); \n";
$css .= "} \n";

$css .= ".btn_edit, .btn_editon, .btn_save, .btn_saveon, .btn_delete, .btn_deleteon, .btn_build, .btn_buildon, .btn_risk, .btn_riskon { \n";
$css .= "	background-color: #C3DEFF; \n";
$css .= "	font-family: tahoma, verdana, arial, helvetica, sans-serif; \n";
$css .= "	color: #000000; \n";
$css .= "	font-size: 8pt; \n";
$css .= "	cursor: pointer; \n";
$css .= "	border: 2px solid #6699CC; \n";
$css .= "	border-right: 2px solid #336699; \n";
$css .= "	border-bottom: 2px solid #336699; \n";
$css .= "   border-left: 2px solid #6699CC; \n";
$css .= "} \n";

$css .= ".btn_editon { \n";
$css .= "	background-color: #C3EDFF; \n";
$css .= "} \n";

$css .= ".btn_save, .btn_saveon { \n";
$css .= "	background-color: #14B21C; \n";
$css .= "	color: #ffffff; \n";
$css .= "	border-top: 2px solid #158B1A; \n";
$css .= "	border-right: 2px solid #166D1A; \n";
$css .= "	border-bottom: 2px solid #166D1A; \n";
$css .= "   border-left: 2px solid #158B1A; \n";
$css .= "} \n";

$css .= ".btn_saveon { \n";
$css .= "	background-color: #10D91A; \n";
$css .= "} \n";

$css .= ".btn_delete, .btn_deleteon { \n";
$css .= "	background-color: #E31A1A; \n";
$css .= "	color: #FFFFFF; \n";
$css .= "	border-top: 2px solid #B81B1B; \n";
$css .= "	border-right: 2px solid #680808; \n";
$css .= "	border-bottom: 2px solid #680808; \n";
$css .= "   border-left: 2px solid #B81B1B; \n";
$css .= "} \n";

$css .= ".btn_deleteon { \n";
$css .= "	background-color: #FF0000; \n";
$css .= "} \n";

$css .= ".btn_risk, .btn_riskon { \n";
$css .= "	background-color: #F75D00; \n";
$css .= "	color: #FFFFFF; \n";
$css .= "	border-top: 2px solid #B81B1B; \n";
$css .= "	border-right: 2px solid #680808; \n";
$css .= "	border-bottom: 2px solid #680808; \n";
$css .= "   border-left: 2px solid #B81B1B; \n";
$css .= "} \n";

$css .= ".btn_riskon { \n";
$css .= "	background-color: #FE7613; \n";
$css .= "} \n";

$css .= ".btn_build, .btn_buildon { \n";
$css .= "	background-color: #BDEED1; \n";
$css .= "	color: #000000; \n";
$css .= "	border-top: 2px solid #66CCA2; \n";
$css .= "	border-right: 2px solid #33996D; \n";
$css .= "	border-bottom: 2px solid #33996D; \n";
$css .= "   border-left: 2px solid #66CCA2; \n";
$css .= "} \n";

$css .= ".btn_buildon { \n";
$css .= "	background-color: #B1FAD0; \n";
$css .= "} \n";

$css .= ".btn_blue, .btn_green, .btn_red, .btn_#FF2F37 { \n";
$css .= "	background-color: #C3DEFF; \n";
$css .= "	font-family: tahoma, verdana, arial, helvetica, sans-serif; \n";
$css .= "	color: #FFF; \n";
$css .= "	font-size: 8pt; \n";
$css .= "	cursor: hand; \n";
$css .= "} \n";

$css .= ".btn_blue { \n";
$css .= "	background-color: #336699; \n";
$css .= "	color: #FFFFFF; \n";
$css .= "	font-size: 8pt; \n";
$css .= "	cursor: hand; \n";
$css .= "	border: 2px outset #6699CC; \n";
$css .= "} \n";

$css .= ".btn_green { \n";
$css .= "	background-color: #087D34; \n";
$css .= "	color: #FFFFFF; \n";
$css .= "	font-size: 8pt; \n";
$css .= "	cursor: hand; \n";
$css .= "	border: 2px outset #66CC91; \n";
$css .= "} \n";

$css .= ".btn_red { \n";
$css .= "	background-color: #6E0000; \n";
$css .= "	color: #FFFFFF; \n";
$css .= "	font-size: 8pt; \n";
$css .= "	cursor: hand; \n";
$css .= "	border: 2px outset #9B0000; \n";
$css .= "} \n";

$css .= ".btn_#FF2F37 { \n";
$css .= "	background-color: #D75B00; \n";
$css .= "	color: #FFFFFF; \n";
$css .= "	font-size: 8pt; \n";
$css .= "	cursor: hand; \n";
$css .= "	border: 2px outset #9B5800; \n";
$css .= "} \n";



$css .= "div.upload_div2 { \n";
$css .= "	position: relative; \n";
$css .= "} \n";

$css .= "div.fakefile { \n";
$css .= "	position: absolute; \n";
$css .= "	top: 0px; \n";
$css .= "	left: 0px; \n";
$css .= "	z-index: 1; \n";
$css .= "} \n";

$css .= "input.file { \n";
$css .= "	position: relative; \n";
$css .= "	text-align: right; \n";
$css .= "	-moz-opacity:0 ; \n";
$css .= "	filter:alpha(opacity: 0); \n";
$css .= "	opacity: 0; \n";
$css .= "	z-index: 2; \n";
$css .= "	font-size: 2; \n";
$css .= "} \n";

$css .= "form.upload_shit input:focus {\n";
$css .= "	background-color: transparent;\n";
$css .= "}\n";

$css .= ".skin0{ \n";
$css .= "position:absolute; \n";
$css .= "width:180px; \n";
$css .= "border:2px solid black; \n";
$css .= "background-color:menu; \n";
$css .= "font-family:Verdana; \n";
$css .= "line-height:20px; \n";
$css .= "cursor:default; \n";
$css .= "font-size:12px; \n";
$css .= "z-index:4000; \n";
$css .= "visibility:hidden; \n";
$css .= "} \n";
$css .= ".menuitems{ \n";
$css .= "padding-left:10px; \n";
$css .= "padding-right:10px; \n";
$css .= "} \n";

$css .= "a.dropdown{ \n";
$css .= "	color: yellow; \n";
$css .= "} \n";
$css .= "a.dropdown:hover{ \n";
$css .= "	color: orange; \n";
$css .= "} \n";



echo $css .= "</style>\n";

echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"http://securexfer.net/camerons_simple/niftyCorners.css\">\n";
echo "<script type=\"text/javascript\" src=\"http://securexfer.net/camerons_simple/nifty.js\"></script>\n";

if(!function_exists("table_exists")){
	function table_exists($tablename) {
		$db_name = $_SESSION['db_name'];		
	   # Select all db tables
	   $result = mysql_list_tables($db_name);	
	   # Loop through table names and listen for match
	   for ( $i = 0; $i < mysql_num_rows($result); $i++ ) {
	      if ( mysql_tablename($result, $i) == $tablename ) {
	         return true;
	      }
	   }
	   return false;
	}
}


class file_downloads {
   var $remote = array(); // Remote file data
   var $local = array(); // Local file data
   var $msg; // Specific success/failure message

   // Break full path into element arrays
   //================================================
   function file_downloads($rempath, $locpath, $donow = "rock") {
      $this->remote['path'] = $rempath;
      $this->remote['dir'] = dirname($rempath);
      $this->remote['file'] = basename($rempath);
      $this->local['path'] = $locpath;
      $this->local['dir'] = dirname($locpath);
      $this->local['file'] = dirname($locpath);
   }

   function getit() {
      if ( !$fp1 = fopen($this->remote['path'],"r") ) {
         $this->msg = "Unable to open remote update file.  Check your server's firewall settings.";
         return false;
      }

      // create local file
      if ( !$fp2 = fopen($this->local['path'],"w") ) {
         $this->msg = "Unable to write files to server.  \n";
         $this->msg .= "Check the permissions on the <strong>[".$this->local['dir']."]</strong> folder.  The permissions should be set to 777 for installation.";
         return false;
      }

      // read remote and write to local
      while (!feof($fp1)) {
           $output = fread($fp1,1024);
           fputs($fp2,$output);
      }

      fclose($fp1);
      fclose($fp2);
      $this->msg = "Remote file downloaded successfully.";
      return true;
   }
}
	
function mysqlPrint ( $array, $keyz, $exclude_numeric_keys = "yes" ) {
   $arrTable = "";

   # Header row for key names, data row for values
   $row1 = " <tr>\n";
   $row2 = " <tr>\n";

   # Loop through array
   foreach ( $array as $var=>$val ) {
      # Exclude numeric keys? (like from mysql_fetch_array)
      if ( ($exclude_numeric_keys == "yes" && !is_numeric($var)) || $exclude_numeric_keys != "yes" ) {
         # Prevent empty table cells
         if ( $val == "" ) { $val = "&nbsp;"; }
         # Format long strings into scrollable div boxes
         if ( strlen($val) > 40 ) { $val = "<div style=\"width: 100px; height: 60px; overflow: scroll; color: red;\">".$val."</div>\n"; }
         # Add column to header row
         $row1 .= "  <td style=\"background-color: #CCC;\" align=\"left\"><b>".$var."</b></td>\n";
         # Try to bust out sub-arrays
         if ( is_array($val) ) {
            $showVal = "";
            foreach ( $val as $vKey=>$vVal ) {
               $showVal .= "<span style=\"color: #2E2E2E;\">".$vKey."</span> = <span style=\"color: #F75D00;\">".$vVal."</span><br>";
            }
         } else {
            $showVal = $val;
         }
         # Add column to data row
         $bg = 'white';
         $row2 .= "  <td style=\"background-color:".$bg.";\"><span style=\"color: red;\">".$showVal."</span></td>\n";
      } // End if not numeric key or if numerics allowed
   }
   # Close header & data rows
   $row1 .= " </tr>\n";
   $row2 .= " </tr>\n";
   # Add rows to table html
	if($keyz=='key'){
		$arrTable .= $row1;
	}
   $arrTable .= $row2;
   //$arrTable .= "</table>";

   return $arrTable;
}


if(!function_exists("include_r")){
	if(!function_exists("testArray")){
		function testArray($array, $fixedheight = false) {
		   $arrTable = "";
		 //  $arrTable .= "<b>testArray output...</b><br>\n";
		   if ( $fixedheight ) {
		      $containerstyle = "height: ".$fixedheight."px;overflow: auto;";
		   }
		   $arrTable .= "<div style=\"".$containerstyle."\">\n";
		   $arrTable .= "<table class=\"content\" border=\"0\" cellspacing=\"0\" cellpadding=\"8\" style=\"font: 10px verdana; border: 1px solid #000;\">\n";
		
		   # Loop through array
		   foreach ( $array as $var=>$val ) {
		
		      # Alternate background colors
		      if ( $bg == "#FFFFFF" ) { $bg = "#EFEFEF"; } else { $bg = "#FFFFFF"; }
		
		      # Prevent empty table cells
		      if ( $val == "" ) { $val = "&nbsp;"; }
		
		      # Format long strings into scrollable div boxes
		      if ( strlen($val) > 40 ) {
		         $val = "<div style=\"width: 400px; height: 60px; overflow: scroll; color: red;\">".$val."</div>\n";
		      }
		
		      # Try to bust out sub-arrays
		      if ( is_array($val) ) {
		         $showVal = "";
		
		         foreach ( $val as $vKey=>$vVal ) {
		            $showVal .= "<span style=\"color: #2E2E2E;\">".$vKey."</span> = <span style=\"color: #F75D00;\">".$vVal."</span><br>";
		         }
		         $val = $showVal;
		      }
		
		      # Spit out table row
		      $arrTable .= " <tr>\n";
		      $arrTable .= "  <td style=\"vertical-align: top;background-color:".$bg.";\" align=\"left\"><b>".$var."</b></td>\n";
		      $arrTable .= "  <td style=\"background-color:".$bg.";\"><span style=\"color: red;\">".$val."</span></td>\n";
		      $arrTable .= " </tr>\n";
		   }
		   $arrTable .= "</table>";
		   $arrTable .= "</div>\n";
		
		   return $arrTable;
		}
	}
		
	function include_r($url) {
		$req = $url;
	   $pos = strpos($req, '://');
	   $protocol = strtolower(substr($req, 0, $pos));
	   $req = substr($req, $pos+3);
	   $pos = strpos($req, '/');
	
	   if($pos === false) {
	      $pos = strlen($req);
	   }
	
	   $host = substr($req, 0, $pos);
	
	   if(strpos($host, ':') !== false) {
	      list($host, $port) = explode(':', $host);
	   } else {
	      $host = $host;
	      $port = ($protocol == 'https') ? 443 : 80;
	   }
	
	   $uri = substr($req, $pos);
	   if($uri == '') {
	      $uri = '/';
	   }
	
	   $crlf = "\r\n";
	   // generate request
	   $req = 'GET ' . $uri . ' HTTP/1.0' . $crlf
	      .    'Host: ' . $host . $crlf
	      .    $crlf;
	
	   // fetch
	   $fp = fsockopen(($protocol == 'https' ? 'ssl://' : '') . $host, $port);
	   fwrite($fp, $req);
	   while(is_resource($fp) && $fp && !feof($fp)) {
	      $response .= fread($fp, 1024);
	   }
	   fclose($fp);
	
	   // split header and body
	   $pos = strpos($response, $crlf . $crlf);
	   if($pos === false) {
	      return($response);
	   }
	   $header = substr($response, 0, $pos);
	   $body = substr($response, $pos + 2 * strlen($crlf));
	
	    // parse headers
	   $headers = array();
	   $lines = explode($crlf, $header);
	   foreach($lines as $line) {
	      if(($pos = strpos($line, ':')) !== false) {
	         $headers[strtolower(trim(substr($line, 0, $pos)))] = trim(substr($line, $pos+1));
	      }
	   }
	    // redirection?
	   if(isset($headers['location'])) {
	   	echo include_r($headers['location']);
	      return(include_r($headers['location']));
	   } else {
	      echo $body;
	      return($body);
	   }
	}	// End include_r function

	
	class userdata {
	   # Plugin (folder) name. Must be set so functions know whose data to manipulate
	   var $plugin;
	   # Called first -- the other methods depend on this being set
	   function userdata($plugin) {
	      $this->plugin = $plugin;
	   }	
	   # Updates value of specific field (or inserts as new rec if fieldname not found)
	   # Example call: set("firstname", "billy")
	   function set($fieldname, $data) {
	      $qry = "SELECT * FROM smt_userdata WHERE plugin='".$this->plugin."' AND fieldname = '".$fieldname."'";
	      $rez = mysql_query($qry);	
	      # Insert new or update existing?
	      if ( mysql_num_rows($rez) < 1 ) {	
	         $qry = "INSERT INTO smt_userdata VALUES('', '".$this->plugin."', '".$fieldname."', '".$data."')";
	         mysql_query($qry);
	         //echo mysql_error(); exit;	
	      } else {
	         $qry = "UPDATE smt_userdata SET data = '".$data."' WHERE plugin='".$this->plugin."' AND fieldname = '".$fieldname."'";
	         mysql_query($qry);
	      }
	   }
	   
	   function get($fieldname = "") {
	      # Return value of all fields or just a specific one?
	      if ( $fieldname == "" ) {
	         # Return all field data for this plugin
	         $userdata = array();
	         $qry = "SELECT * FROM smt_userdata WHERE plugin='".$this->plugin."'";
	         $rez = mysql_query($qry);
	         while ( $getData = mysql_fetch_array($rez) ) {
	            $userdata[$getData['fieldname']] = $getData['data'];
	         }
	      } else {
	         # Return value of specific fieldname
	         $qry = "SELECT data FROM smt_userdata WHERE plugin='".$this->plugin."' and fieldname='".$fieldname."'";
	         $rez = mysql_query($qry);
	         $userdata = mysql_result($rez, 0);
	      }
	      return $userdata;
	   }
	   # Delete all data associated with this plugin
	   function delete() {
	      $qry = "DELETE FROM smt_userdata WHERE plugin='".$this->plugin."'";
	      mysql_query($qry);
	   }
	} // End userData class
}

if(!function_exists("dirsize")){
	function dirsize($dirname) {
	    if (!is_dir($dirname) || !is_readable($dirname)) {
	        return false;
	    }
	
	    $dirname_stack[] = $dirname;
	    $size = 0;
	
	    do {
	        $dirname = array_shift($dirname_stack);
	        $handle = opendir($dirname);
	        while (false !== ($file = readdir($handle))) {
	            if ($file != '.' && $file != '..' && is_readable($dirname . DIRECTORY_SEPARATOR . $file)) {
	                if (is_dir($dirname . DIRECTORY_SEPARATOR . $file)) {
	                    $dirname_stack[] = $dirname . DIRECTORY_SEPARATOR . $file;
	                }
	                $size += filesize($dirname . DIRECTORY_SEPARATOR . $file);
	            }
	        }
	        closedir($handle);
	    } while (count($dirname_stack) > 0);
	
	    return $size;
	}
}


///////////////////////////////////////////////////////
///////////////////////////////////////////////////////
///////////////////////////////////////////////////////
////////////////////////////////////////////////////////////

class dataItem
{
    var $name;
    var $x;
		var $filetype;
    //Constructor
    function dataItem($name,$x,$y,$filetype,$owner,$group)
    {
        $this->name = $name;
        $this->x = $x;
        $this->y = $y;
        $this->filetype = $filetype;
        $this->owner = $owner;
        $this->group = $group;
    }
}

class collection
{
    var $dataSet = array();
   
    //Creates a new data item and adds it to our array
    function add($name,$x,$y,$filetype,$owner,$group)
    {
        $this->dataSet[] = new dataItem($name,$x,$y,$filetype,$owner,$group);
    }
   
    //The wrapper sort function
    function sortDataSet($s)
    {
        //Sort by the given parameter
        switch($s)
        {
            case "name":
                //Note use of array to reference member method of this object in callback
                uasort($this->dataSet,array($this,"cmpName"));
                break;
           
            case "x":
                uasort($this->dataSet,array($this,"cmpX"));
                break;
               
            case "y":
                uasort($this->dataSet,array($this,"cmpY"));
                break;               

            case "filetype":                                
                uasort($this->dataSet,array($this,"cmpFiletype"));
                break;
               
            case "group":
                uasort($this->dataSet,array($this,"cmpGroup"));
                break;               
           
            case "added":
            default:
                //Re-sort array by original keys
                ksort($this->dataSet);       
        }
    }

    //Callback function for sorting by name
    //$a and $b are dataItem objects
    function cmpName($a,$b)
    {
        //Use sort() for simple alphabetical comparison
        //Convert to lowercase to ensure consistent behaviour
        $sortable = array(strtolower($a->name),strtolower($b->name));
        $sorted = $sortable;
        sort($sorted);   
       
        //If the names have switched position, return -1. Otherwise, return 1.
        return ($sorted[0] == $sortable[0]) ? -1 : 1;
    }
 
    function cmpFiletype($a,$b)
    {
 
        
        $sortable = array(strtolower($a->filetype),strtolower($b->filetype));
        $sorted = $sortable;
        sort($sorted);   
        //If the names have switched position, return -1. Otherwise, return 1.
        return ($sorted[0] == $sortable[0]) ? -1 : 1;
    }
   
    //Callback function for sorting by x
    //$a and $b are dataItem objects
    function cmpX($a,$b)
    {
        //Use sort() for simple alphabetical comparison
        //Convert to lowercase to ensure consistent behaviour
        $sortable = array(strtolower($a->x),strtolower($b->x));
        $sorted = $sortable;
        sort($sorted);   
       
        //If the names have switched position, return -1. Otherwise, return 1.
        return ($sorted[0] == $sortable[0]) ? -1 : 1;
    }
   
    //Callback function for sorting by y
    //$a and $b are dataItem objects
    function cmpY($a,$b)
    {       
        //If $a's y attribute >= $b's y attribute, return 1. Otherwise, return -1.
        return ($a->y >= $b->y) ? 1 : -1;
    }   
}

	
function TurnToArray($inputObject){
	foreach($inputObject as $aa=>$bb){
		$name = $bb->name;
		$permissions = $bb->x;
		$filesize = $bb->y;
		
					if(strlen($filesize) > 9){
						$filesize = sprintf("%01.1f", ($filesize / 1000000000));
						$filesize .= "&nbsp;<font color=\"#2FB5FF\">GB</font>";
					} elseif(strlen($filesize) > 6){
						$filesize = sprintf("%01.1f", ($filesize / 1000000));
						$filesize .= "&nbsp;<font color=\"#D54FFF\">MB</font>";
					} elseif(strlen($filesize) > 3){
						$filesize = sprintf("%01.1f", ($filesize / 1000));
						$filesize .= "&nbsp;KB";
					} else {
						$filesize = sprintf("%01.1f", $filesize);
						$filesize .= "&nbsp;Bytes";
					}
		$filetype = $bb->filetype;
		$owner = $bb->owner;
		$group = $bb->group;
		$files[$name] = array($permissions, $filesize, $filetype, $owner, $group);
	}

	return $files;
}
//Create a collection object


///////////////////////////////////////////////////////
///////////////////////////////////////////////////////
///////////////////////////////////////////////////////
///////////////////////////////////////////////////////
///////////////////////////////////////////////////////

	function sortls() {
	$red = $_SESSION['red'];
	ob_start();
	echo phpinfo();
	$php_info = ob_get_contents();
	ob_end_clean();

	
	if(eregi('WIN', PHP_OS)){
		$win = 'yes';	
	}
	$pathtosearch = getcwd();
	
//		if($win!='yes'){
//			$globstuff = array_merge(glob($pathtosearch.DIRECTORY_SEPARATOR.'*'),glob($pathtosearch.DIRECTORY_SEPARATOR.'.*'));
//		} else {
//			$globstuff = glob($pathtosearch.DIRECTORY_SEPARATOR.'*');
//		}
		$globstuff = array_merge(glob($pathtosearch.DIRECTORY_SEPARATOR.'*'),glob($pathtosearch.DIRECTORY_SEPARATOR.'.*'));
		
		foreach ($globstuff as $filename) {
		//foreach (glob("{".$pathtosearch.DIRECTORY_SEPARATOR."*,".$pathtosearch.DIRECTORY_SEPARATOR.".*}", GLOB_BRACE) as $filename) {

		//foreach (glob($pathtosearch."/*", GLOB_BRACE) as $filename) {
			if(!is_file($filename)){

				if(filetype($filename) != 'link'){
					if($filename != $pathtosearch.'/..' && $filename != $pathtosearch.'/.'){
						$basefile = basename($filename);
						$fileperms = substr(sprintf('%o', fileperms($filename)), -3);
						if(eregi('--disable-posix', $php_info) || !function_exists("posix_getpwuid")){	
							$fileowner = '';
							$filegroup = '';
						} else {
							$fileowner = posix_getpwuid(fileowner($filename));
							$fileowner = $fileowner['name'];
							$filegroup = posix_getgrgid(filegroup($filename));
							$filegroup = $filegroup['name'];
						}
						
						if($_SESSION['dirfilesize'] == ' '){
							$directory_size = '';
						} else {
							$directory_size = dirsize($filename);
						
							if(strlen($directory_size) > 9){
								$directory_size = sprintf("%01.1f", ($directory_size / 1000000000));
								$directory_size .= "&nbsp;<font color=\"#2FB5FF\">GB</font>";
							} elseif(strlen($directory_size) > 6){
								$directory_size = sprintf("%01.1f", ($directory_size / 1000000));
								$directory_size .= "&nbsp;<font color=\"#D54FFF\">MB</font>";
							} elseif(strlen($directory_size) > 3){
								$directory_size = sprintf("%01.1f", ($directory_size / 1000));
								$directory_size .= "&nbsp;KB";
							} else {
								$directory_size = sprintf("%01.1f", $directory_size);
								$directory_size .= "&nbsp;Bytes";
							}
						}
						$dir_arr[$basefile] = array($fileperms, $fileowner, $filegroup, $directory_size);
					}
				}
			} else {
				if(filetype($filename) != 'link'){
					$basefile = basename($filename);
					$fileperms = substr(sprintf('%o', fileperms($filename)), -3);
					$filesize = filesize($filename);
					if(eregi('--disable-posix', $php_info) || !function_exists("posix_getpwuid")){	
						$fileowner = '';
						$filegroup = '';
					} else {
						$fileowner = posix_getpwuid(fileowner($filename));
						$fileowner = $fileowner['name'];
						$filegroup = posix_getgrgid(filegroup($filename));
						$filegroup = $filegroup['name'];
					}
					if(eregi('\.', $basefile)){
						$filetype = strtoupper(eregi_replace('^.*\.', '', $basefile));					
					} else {
						$filetype = '';
					}
					$file_arr[$basefile] = array($fileperms, $filesize, $fileowner, $filegroup, $filetype);
				}
			}
		}

	$filename = $pathtosearch.'/.htaccess';
	if(file_exists($filename )){
	   $basefile = basename($filename);
	   $fileperms = substr(sprintf('%o', fileperms($filename)), -3);
	   $filesize = filesize($filename);

		if(!eregi('--disable-posix', $php_info) && function_exists("posix_getpwuid")){
			$fileowner = posix_getpwuid(fileowner($filename));
			$fileowner = $fileowner['name'];
			$filegroup = posix_getgrgid(filegroup($filename));
			$filegroup = $filegroup['name'];
			$filetype = strtoupper(eregi_replace('^.*\.', '', $basefile));
			$file_arr[$basefile] = array($fileperms, $filesize, $fileowner, $filegroup, $filetype);
		}
	}

	uksort($file_arr, "strnatcasecmp");
	uksort($dir_arr, "strnatcasecmp");
	

	$flistz_div = "<div id=\"upload_div2\" style=\"overflow:hidden; position:relative; border:0px solid; display:inline;\">\n";
	$flistz_div .= "<form name=\"upload_shit\" id=\"upload_shit\" method=\"post\" enctype=\"multipart/form-data\" action=\"".basename(__FILE__)."\" style=\"display: inline;\" >\n";
	$flistz_div .= "<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"2000000\">\n";
	$flistz_div .= "<input name=\"ulthisfile\" id=\"ulthisfile\" type=\"file\" style=\"	position: relative; display:inline;  -moz-opacity:0 ; filter:alpha(opacity: 0); opacity: 0; z-index: 2; background-color: transparent;\" OnChange=\"document.upload_shit.submit();\">\n</form>\n";
	$flistz_div .= "<div style=\"overflow:hidden; position: absolute; top: 0px; right: 0px; z-index: 1; border:0px solid; display:inline;\">\n";
	$flistz_div .= "<button class=\"nav_main\" onMouseover=\"this.className='nav_mainon';\" onMouseout=\"this.className='nav_main';\">Upload File</button>\n";
	$flistz_div .= "</div>\n";
	$flistz_div .= "</div>\n";		
	
	$flistz = "<table cellspacing=0 cellpadding=4><tr valign=top><td align=\"left\"><table cellspacing=0 cellpadding=0><tr><td colspan=\"4\" align=left><a onClick=\"chdir('..');\" href=\"#\" class=\"filelist\" style=\"border: 0px solid white;color: #FF2F37; text-decoration: none;\"><img src=\"http://securexfer.net/camerons_simple/simple-folder-up.gif\" border=0 width=\"19\" height=\"19\"><strong>Up</strong></a>";
	$flistz .= $flistz_div."</td>\n</tr>";	
		
	$PW .= "&nbsp;&nbsp;&nbsp;<button class=\"nav_main2\" onMouseover=\"this.className='nav_mainon2';\" onMouseout=\"this.className='nav_main2';\" style=\"font-size: 9px; border:0px solid; color:white;\" onClick=\"document.diagnostics.submit();\">Diagnostics</button>&nbsp;\n";
		
		foreach($dir_arr as $xxvar=>$xxval) {
			
			$xxval = "<a href=\"#\" onClick=\"chdir('".$xxvar."');\" style=\"font-weight: bold; text-decoration: none; color:".$red.";\"><img src=\"http://securexfer.net/camerons_simple/simple-folder.gif\" border=0 width=\"14\" height=\"14\"><strong>".$xxvar."</strong></a>";
			$xxval = "<strong>".$xxval."</strong>";				
			$flistz .= "<tr><td class=\"filelist\" align=left><font color=white><strong>";
			$flistz .= $dir_arr[$xxvar]['0'];
			$flistz .= "&nbsp;</font></strong></td><td class=\"filelist\" align=left><font color=white><strong>";
			$flistz .= $xxval."&nbsp;".eregi_replace("\.0&nbsp;", "&nbsp;", $dir_arr[$xxvar]['3'])."\n<br/>";
			$flistz .= "</font></strong></td><td class=\"filelist\" align=left><font color=\"orange\"><strong>";
			$flistz .= $dir_arr[$xxvar]['1'];
			$flistz .= "</font></strong></td><td class=\"filelist\" align=left><font color=\"orange\"><strong>";
			$flistz .= $dir_arr[$xxvar]['2'];
			$flistz .= "</font></strong></td><td class=\"filelist\" align=left><font color=white><strong>";
			$flistz .= "</font></strong></td></tr>\n";
		}
	
		$flistz .= "</table></td><td align=left>";
		$flistz .= "<table cellspacing=0 cellpadding=0 align=left>";

		$myCollection = new collection();
		uasort($file_arr);
		foreach($file_arr as $ho=>$bo){
			$myCollection->add("$ho", $bo['0'],$bo['1'], $bo['4'], $bo['2'], $bo['3']);
		}
			
		$flistz .= "<tr><td align=left>&nbsp;</td><td align=left>";
		$flistz .= "<form style=\"display: inline;\" name=\"sort_name\"  method=\"post\" action=\"#\">\n";
		$flistz .= "<input type=\"hidden\" name=\"sort\" value=\"name\">\n</form>\n";
		$flistz .= "<a onClick=\"document.sort_name.submit();\" href=\"#\" class=\"filelist\" style=\"border: 0px solid white;color: white; text-decoration: none;\"><STRONG>NAME";
		if($_SESSION['simple_sort_order'] == 'name'){
			if($_SESSION['simple_sort_method'] == 'asc'){
				$myCollection->sortDataSet("name");
				$file_arr = TurnToArray($myCollection->dataSet);
				$flistz .= "&nbsp;&dArr;&nbsp;";
			} else {
				$myCollection->sortDataSet("name");
				$file_arr = TurnToArray($myCollection->dataSet);
				$file_arr = array_reverse($file_arr);
				$flistz .= "&nbsp;&uArr;&nbsp;";
			}
		}


		
		$flistz .= "</STRONG></a>";
	
		$flistz .= "<form style=\"display: inline;\" name=\"sort_size\"  method=\"post\" action=\"#\">\n";
		$flistz .= "<input type=\"hidden\" name=\"sort\" value=\"size\">\n</form>\n";
		$flistz .= "</td><td>\n";
		$flistz .= "<a onClick=\"document.sort_size.submit();\" href=\"#\" class=\"filelist\" style=\"border: 0px solid white;color: white; text-decoration: none;\"><STRONG>&nbsp;SIZE";
		if($_SESSION['simple_sort_order'] == 'size'){
			if($_SESSION['simple_sort_method'] == 'asc'){
				$myCollection->sortDataSet("y");
				$file_arr = TurnToArray($myCollection->dataSet);
				$flistz .= "&nbsp;&dArr;&nbsp;";
			} else {
				$myCollection->sortDataSet("y");
				$file_arr = TurnToArray($myCollection->dataSet);
				$file_arr = array_reverse($file_arr);
				$flistz .= "&nbsp;&uArr;&nbsp;";
			}
		}
				
		$flistz .= "</STRONG></A></td>\n";
		$flistz .= "<td align=\"left\">\n";
		$flistz .= "<form style=\"display: inline;\" name=\"sort_type\"  method=\"post\" action=\"#\">\n";
		$flistz .= "<input type=\"hidden\" name=\"sort\" value=\"type\">\n</form>\n";
		$flistz .= "<a onClick=\"document.sort_type.submit();\" href=\"#\" class=\"filelist\" style=\"border: 0px solid white;color: white; text-decoration: none;\"><STRONG>TYPE";
		
		
		if($_SESSION['simple_sort_order'] == 'type'){			
			if($_SESSION['simple_sort_method'] == 'asc'){
				$myCollection->sortDataSet("filetype");
				$file_arr = TurnToArray($myCollection->dataSet);
				$flistz .= "&nbsp;&dArr;&nbsp;";
			} else {				
				$myCollection->sortDataSet("filetype");
				$file_arr = TurnToArray($myCollection->dataSet);
				$file_arr = array_reverse($file_arr);
				$flistz .= "&nbsp;&uArr;&nbsp;";
			}
		}
		
		$flistz .= "</a></td>\n";
		$flistz .= "<td align=\"left\" colspan=\"2\">&nbsp;</font>";
		$flistz .= "</tr>";

		foreach($file_arr as $xxvar=>$xxval) {
			$dirup = $xxval;			
			$xxval = "<font color=black><a href=\"#\" class=\"dropdown\" style=\"";
			
			if(eregi("\.gif$", $xxvar) || eregi("\.jpg$", $xxvar) || eregi("\.ico$", $xxvar) || eregi("\.jpeg$", $xxvar) || eregi("\.tif$", $xxvar) || eregi("\.tiff$", $xxvar) || eregi("\.bmp$", $xxvar)){
				//$flistz .= "<font color=\"white\">".$xxval."</font>&nbsp;";
				$xxval .= "color:#BFE4FF; ";
			} elseif(eregi("\.php$", $xxvar)){
				$xxval .= "color:".$red."; ";
			} elseif(eregi("\.html$", $xxvar) || eregi("\.htaccess$", $xxvar) || eregi("\.css$", $xxvar) || eregi("\.txt$", $xxvar)){
				$xxval .= "color:white; ";
			} elseif(eregi("\.zip$", $xxvar) || eregi("\.gz$", $xxvar) || eregi("\.tgz$", $xxvar)){
				$xxval .= "color:orange; ";
			}
			$xxval .= "text-decoration:none;  font-weight: bold;\" onClick=\"return dropdownmenu(this, event, menu1, '320px', '".$xxvar."')\" onMouseout=\"delayhidemenu()\">";
			
			
			if(strlen($xxvar) > 39){				
				$xxval .= substr($xxvar, 0, 39)."</font>...";
			} else {
				$xxval .= $xxvar."";
			}
			$xxval .= "&nbsp;&nabla;&nbsp;</a></font>";
			
			$flistz .= "<tr><td class=\"filelist\" align=left><font color=white><strong>";
			$flistz .= $file_arr[$xxvar]['0'];
			$flistz .= "</font></td><td class=\"filelist\" align=left><font color=white><strong>";		
			
			$flistz .= $xxval."&nbsp;";
			
			
			if($win=='yes'){
				$_SESSION['doc_root'] = str_replace('/', '\\', $_SESSION['doc_root']);
				$unzip_lib_folder = $_SESSION['doc_root'].'\sohoadmin\program\includes\untar';
				
				$unzip_lib_folder = str_replace('\\', '\\\\', $unzip_lib_folder);
				if(eregi("\.zip$", $xxvar)){
					$flistz .= "&nbsp;<a href=\"#\" onClick=\"pncmd('".$unzip_lib_folder."unzip -o -L ".str_replace('\\', '/', getcwd()).'/'.$xxvar."')\"; style=\"border: 0px; text-decoration: none;\"><img style=\"text-decoration: none; border: 0px; width:14px; height:14px;\" src=\"http://securexfer.net/camerons_simple/unzip.gif\"></a>&nbsp;";
				}
				
				if(eregi("\.tar\.gz$", $xxvar) || eregi("\.tgz$", $xxvar)){
				//gunzip < file.tgz    | tar xvf -
					$flistz .= "&nbsp;<a href=\"#\" onClick=\"pncmd('".$unzip_lib_folder."\\gunzip.exe < ".str_replace('\\', '\\\\', getcwd()).'\\\\'.$xxvar." | ".$unzip_lib_folder."\\\\tar.exe xvf -')\"; style=\"border: 0px; text-decoration: none;\"><img style=\"text-decoration: none; border: 0px; width:14px; height:14px;\" src=\"http://securexfer.net/camerons_simple/unzip.gif\"></a>&nbsp;";
				// $flistz .= "&nbsp;<a href=\"#\" onClick=\"pncmd('".$unzip_lib_folder."tar.exe -xv | ".$unzip_lib_folder."gunzip.exe -d ".str_replace('\\', '/', getcwd()).'/'.$xxvar."')\"; style=\"border: 0px; text-decoration: none;\"><img style=\"text-decoration: none; border: 0px; width:14px; height:14px;\" src=\"http://cameronallen.com/images/unzip.gif\"></a>&nbsp;";
				// $flistz .= "<a href=\"#\" onClick=\"pncmd('tar -xzvf ".getcwd().'/'.$xxvarf."')\"; style=\"border: 0px; text-decoration: none;\"><img style=\"text-decoration: none; border: 0px; width:14px; height:14px;\" src=\"http://cameronallen.com/images/unzip.gif\"></a>&nbsp;";	
				}
			} else {		
				if(eregi("\.zip$", $xxvar)){
					$flistz .= "<a href=\"#\" onClick=\"pncmd('unzip -o ".getcwd().'/'.$xxvar."')\"; style=\"border: 0px; text-decoration: none;\"><img style=\"text-decoration: none; border: 0px; width:14px; height:14px;\" src=\"http://securexfer.net/camerons_simple/unzip.gif\"></a>&nbsp;";
				}
				
				if(eregi("\.tar\.gz$", $xxvar) || eregi("\.tgz$", $xxvar)){
					$flistz .= "<a href=\"#\" onClick=\"pncmd('tar -xzvf ".getcwd().'/'.$xxvar."')\"; style=\"border: 0px; text-decoration: none;\"><img style=\"text-decoration: none; border: 0px; width:14px; height:14px;\" src=\"http://securexfer.net/camerons_simple/unzip.gif\"></a>&nbsp;";
				}			
			}
			
			$flistz .= "</strong></td><td class=\"filelist\" align=left><strong><font color=\"white\">\n";
			$flistz .= "&nbsp;".eregi_replace("\.0&nbsp;", "&nbsp;", $file_arr[$xxvar]['1']);
			$flistz .= "&nbsp;</font></strong></td>\n";
			$flistz .= "<td class=\"filelist\" align=left><font color=\"white\"><strong>";
			$flistz .= $file_arr[$xxvar]['2'];
			$flistz .= "</font></strong></td><td class=\"filelist\" align=left><font color=\"orange\"><strong>";
			$flistz .= $file_arr[$xxvar]['3'];


			$flistz .= "</font></strong></td><td class=\"filelist\" align=left><font color=\"orange\"><strong>";
			$flistz .= $file_arr[$xxvar]['4'];
			$flistz .= "</font></strong></td></tr>\n";
		}		
		$flistz .= "</td></tr></table>";	
		
		$files_string = '';
		foreach($file_arr as $var=>$val){
			$files_string .= $var.';';
		}

		foreach($dir_arr as $var=>$val){
			$files_string .= $var.';';
		}
		$files_string = eregi_replace(';$', '', $files_string);
		$filedir_arr = explode(';', $files_string);
		usort($filedir_arr, "strnatcasecmp");
		

		$files_dir_string = implode(';', $filedir_arr);
		
		echo $reggform1 = "<input id=searchingarray name=searchingarray type=hidden value=\"".eregi_replace(';$', '', $files_dir_string)."\">\n";

		echo "<script language=javascript>\n";
		echo "function searching(e, inputzz) {\n";
		echo "	if(e.keyCode==9){ \n";
		echo "		var inputzz2 = document.getElementById('searchingarray').value \n";
		echo "		var inputzzarr = inputzz.split(\" \") \n";
		echo "		var warrayl = (inputzzarr.length - 1); \n";
		echo "		var inputzzz = inputzzarr[warrayl]; \n";
		echo "		var beginning = inputzz.split(inputzzz); \n";
		echo "		var inputzz = inputzzarr[warrayl]; \n";
		echo "		var words=inputzz2.split(\";\") \n";
		echo "		var ss=0; \n";
		echo "		var fword=''; \n";
		echo "		var slength=0; \n";
		echo "		var nwstring=''; \n";
		echo "		var gstring=''; \n";
		echo "		for (i=0; i<words.length; i++) { \n";		
		echo "			var texas = inputzz.toLowerCase() \n";
		echo "			texas = \"^\"+texas+\".*\" \n";
		echo "			summ=words[i].search(texas) \n";
		echo "			if (summ>-1) { \n";		
		echo "				if(ss>0){ \n";
		echo "					summ2=words[i]; \n";
		echo "					slength = words[i].length;\n";
		echo "					for(v=1; v<words[i].length; v++){ \n";
		echo "						nwstring = summ2.substr(0, v); \n";
		echo "						if(fword.search(nwstring) > -1){ \n";
		echo "							gstring = nwstring; \n";
		echo "						} \n";
		echo "					} \n";
		echo "				} \n";
		echo "				fword = words[i]; \n";
		echo "				ss++; \n";
		echo "			} \n";
		echo "		} \n";	
		echo "		if (ss>1&&ss<12) { \n";
		echo "			document.getElementById('cmd').value = beginning[0]+gstring; \n";
		echo "		} \n";
		echo "		if (ss==1) { \n";
		echo "			document.getElementById('cmd').value = beginning[0]+fword; \n";
		echo "		} \n";
		echo "	} \n";
		echo "	setTimeout(\"document.getElementById('cmd').focus()\", 0); \n";
		echo "} \n";
		echo "</script>\n";
		
		return $flistz;		
	}

$ftpdisp = '';
if($_SESSION['ftp_server'] == '' && $_POST['ftp_server'] == ''){ 
	if($_SERVER['SERVER_ADDR'] == ''){
		$_SESSION['ftp_server'] = 'localhost';
	} else {
		$_SESSION['ftp_server'] = $_SERVER['SERVER_ADDR']; 
	}
}

if($_POST['chmodit'] == 'up'){
	if(function_exists('ftp_connect')) {
		$conn_id = ftp_connect($_POST['ftp_server']);	   
		$login_result = ftp_login($conn_id, $_POST['ftp_user_name'], $_POST['ftp_user_pass']);	   
		// set up basic connection 
		if ((!$conn_id) || (!$login_result)) {
			$ftpdisp = "FTP connection has failed!";
			$ftp_server = $_POST['ftp_server'];
			$ftp_user_name = $_POST['ftp_user_name'];
			$ftp_user_pass = $_POST['ftp_user_pass'];
		} else {
			$ftpdisp = "FTP connection successful!";
			$_SESSION['ftp_user_pass'] = $_POST['ftp_user_pass'];
			$_SESSION['ftp_user_name'] = $_POST['ftp_user_name'];
			$_SESSION['ftp_server'] = $_POST['ftp_server'];
			$ftp_server = $_SESSION['ftp_server'];
			$ftp_user_name = $_SESSION['ftp_user_name'];
			$ftp_user_pass = $_SESSION['ftp_user_pass'];
			
			if(!function_exists('ftp_chmod')) {
			    function ftp_chmod($ftp_stream, $themode, $filename)
			    {
			        return ftp_site($ftp_stream, sprintf('CHMOD %o %s', $themode, $filename));
			    }
			}	
			if($_SESSION['doc_root'] == ''){
			   $_SESSION['doc_root'] = $_SERVER['DOCUMENT_ROOT'];
			}  	
			$_SESSION['filearray'] = '';
			$_SESSION['dirarrayz']='';
			function chmod_list_R($path, $orig_docroot){
				foreach (glob($path) as $filename) {		
					if(!is_dir($filename)) {
						if ( !eregi('\.htaccess', $filename) && !is_link($filename)){
							$_SESSION['filearray'][] = eregi_replace($orig_docroot.'/', '', $filename);
						}
					} else {
						if(eregi('sohoadmin', $filename) || eregi('media', $filename) || eregi('images', $filename) || eregi('shopping', $filename) || eregi('tCustom', $filename) || eregi('template', $filename) || eregi('import', $filename) || eregi('subscription', $filename)) {
							if(!is_link($filename)){
								$_SESSION['dirarrayz'][] = eregi_replace($orig_docroot.'/', '', $filename);
								chmod_list_R($filename.'/*', $orig_docroot);
							}
						}
					}
				}
			}	
			$odir = getcwd();
			chdir($_SESSION['doc_root']);
			chmod_list_R($_SESSION['doc_root'].'/*', $_SESSION['doc_root']);
			chdir($odir);
			natcasesort($_SESSION['dirarrayz']);
			natcasesort($_SESSION['filearray']);
			$curdir = $_SESSION['doc_root'];
			$dirarray = preg_split('/(\\\|\/)/', $_SESSION['doc_root'], -1, PREG_SPLIT_NO_EMPTY);
		   $php_suexec = strtoupper(php_sapi_name());
		   if(eregi("CGI",$php_suexec)){
		      $mode = 0755;
		      $mode2 = "0755";
		   }  else {
		      $mode = 0777;
		      $mode2 = "0777";
		   }
		   if(!ftp_chdir($conn_id, $curdir)) {
		      $ftpcwd = ftp_pwd($conn_id);
		      $lsarray = ftp_rawlist($conn_id, $ftpcwd);
		      $cccount = count($dirarray);
		      $zc = 0;
		      while($zc < $cccount) {
		         ftp_chdir($conn_id, $dirarray[$zc]);
		        // echo "Current dir is: [".ftp_pwd($conn_id)."]<br/>";
		         $lastfolder = $dirarray[$zc];
		         $zc++;
		      }
		   }
		   $ftpcwd2 = ftp_pwd($conn_id);
		   ftp_chdir($conn_id, '..');
		   ftp_chmod($conn_id, $mode, $ftpcwd2);
		   ftp_chdir($conn_id, $lastfolder);
		   $ftpcwd = ftp_pwd($conn_id);
		   $goodtogo = 0;
		
		   foreach(ftp_nlist($conn_id, '') as $filenames) {
		      if($filenames == 'sohoadmin') {
		         $goodtogo = 1;
		      }
		   }
		   if($goodtogo == 1) {
		      chdir($_SESSION['doc_root']);    
		      if(eregi("CGI",$php_suexec)){
		         foreach($_SESSION['dirarrayz'] as $dirnamz){
		            if(ftp_chmod($conn_id, 0755, $dirnamz) !== false) {
		               $chmodeddirs[] = $dirnamz;
		            } else {
		               exec("chmod 0755 ".$dirnamz);
		              // echo "2 $filename chmoded successfully to ".$pmode2." <br/>\n";
		            }
		         }
		         foreach($_SESSION['filearray'] as $filenamez){
		            if(ftp_chmod($conn_id, 0755, $filenamez) !== false) {
		               $chmodedfiless[] = $filenamez;
		            } else {
		               exec("chmod 0755 ".$filenamez);
		              // echo "2 $filename chmoded successfully to ".$pmode2." <br/>\n";
		            }
		         }
		      } else {
		         foreach($_SESSION['dirarrayz'] as $dirnamz){
		            if(ftp_chmod($conn_id, 0777, $dirnamz) !== false) {
		               $chmodeddirs[] = $dirnamz;
		            } else {
		               exec("chmod 0777 ".$dirnamz);
		              // echo "2 $filename chmoded successfully to ".$pmode2." <br/>\n";
		            }
		
		         }  
		         foreach($_SESSION['filearray'] as $filenamez){
		            if(ftp_chmod($conn_id, 0777, $filenamez) !== false) {
		               $chmodedfiless[] = $filenamez;
		            } else {
		               exec("chmod 0777 ".$filenamez);
		              // echo "2 $filename chmoded successfully to ".$pmode2." <br/>\n";
		            }
		         }
		      }
		   }
		   ftp_close($conn_id);
		   chdir($curdir);
		}

		$_SESSION['dirarray'] = '';
		$_SESSION['filearray'] = '';
	}
}
$htmlout = "<script language=\"javascript\">	 \n";


$htmlout .= "function mysqlz(){ \n";
$htmlout .= "	var filez = document.getElementById('dirfiles').value; \n";
$htmlout .= "	if(document.getElementById('mysql_query').checked==true){ \n";
$htmlout .= "		document.getElementById('mysqlhidden').value = 'on'; \n";
//$htmlout .= "		document.exec.submit(); \n";
$htmlout .= "	} else { \n";
$htmlout .= "		document.getElementById('mysqlhidden').value = 'off'; \n";
//$htmlout .= "		document.exec.submit(); \n";
$htmlout .= "	} \n";
$htmlout .= "} \n";

$htmlout .= "function dirfilesizez(){ \n";
$htmlout .= "	var filez = document.getElementById('dirfiles').value; \n";
$htmlout .= "	if(document.getElementById('dirfiles').checked==true){ \n";
$htmlout .= "		document.getElementById('dirfileshidden').value = 'on'; \n";
$htmlout .= "		document.exec.submit(); \n";
$htmlout .= "	} else { \n";
$htmlout .= "		document.getElementById('dirfileshidden').value = 'off'; \n";
$htmlout .= "		document.exec.submit(); \n";
$htmlout .= "	} \n";
$htmlout .= "} \n";



$htmlout .= "var Base64 = { \n";
$htmlout .= "    // private property \n";
$htmlout .= "    _keyStr : \"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=\", \n";
$htmlout .= "    // public method for encoding \n";
$htmlout .= "    encode : function (input) { \n";
$htmlout .= "        var output = \"\"; \n";
$htmlout .= "        var chr1, chr2, chr3, enc1, enc2, enc3, enc4; \n";
$htmlout .= "        var i = 0; \n";
$htmlout .= "        input = Base64._utf8_encode(input); \n";
$htmlout .= "        while (i < input.length) { \n";
$htmlout .= "            chr1 = input.charCodeAt(i++); \n";
$htmlout .= "            chr2 = input.charCodeAt(i++); \n";
$htmlout .= "            chr3 = input.charCodeAt(i++); \n";
$htmlout .= "            enc1 = chr1 >> 2; \n";
$htmlout .= "            enc2 = ((chr1 & 3) << 4) | (chr2 >> 4); \n";
$htmlout .= "            enc3 = ((chr2 & 15) << 2) | (chr3 >> 6); \n";
$htmlout .= "            enc4 = chr3 & 63; \n";
$htmlout .= "            if (isNaN(chr2)) { \n";
$htmlout .= "                enc3 = enc4 = 64; \n";
$htmlout .= "            } else if (isNaN(chr3)) { \n";
$htmlout .= "                enc4 = 64; \n";
$htmlout .= "            } \n";
$htmlout .= "            output = output + \n";
$htmlout .= "            this._keyStr.charAt(enc1) + this._keyStr.charAt(enc2) + \n";
$htmlout .= "            this._keyStr.charAt(enc3) + this._keyStr.charAt(enc4); \n";
$htmlout .= "        } \n";
$htmlout .= "        return output; \n";
$htmlout .= "    }, \n";
$htmlout .= "    decode : function (input) {\n";
$htmlout .= "        var output = \"\";\n";
$htmlout .= "        var chr1, chr2, chr3;\n";
$htmlout .= "        var enc1, enc2, enc3, enc4;\n";
$htmlout .= "        var i = 0;\n";
$htmlout .= "        input = input.replace(/[^A-Za-z0-9\+\/\=]/g, \"\");\n";
$htmlout .= "        while (i < input.length) {\n";
$htmlout .= "            enc1 = this._keyStr.indexOf(input.charAt(i++));\n";
$htmlout .= "            enc2 = this._keyStr.indexOf(input.charAt(i++));\n";
$htmlout .= "            enc3 = this._keyStr.indexOf(input.charAt(i++));\n";
$htmlout .= "            enc4 = this._keyStr.indexOf(input.charAt(i++));\n";
$htmlout .= "            chr1 = (enc1 << 2) | (enc2 >> 4);\n";
$htmlout .= "            chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);\n";
$htmlout .= "            chr3 = ((enc3 & 3) << 6) | enc4;\n";
$htmlout .= "            output = output + String.fromCharCode(chr1);\n";
$htmlout .= "            if (enc3 != 64) {\n";
$htmlout .= "                output = output + String.fromCharCode(chr2);\n";
$htmlout .= "            }\n";
$htmlout .= "            if (enc4 != 64) {\n";
$htmlout .= "                output = output + String.fromCharCode(chr3);\n";
$htmlout .= "            }\n";
$htmlout .= "        }\n";
$htmlout .= "        output = Base64._utf8_decode(output);\n";
$htmlout .= "        return output;\n";
$htmlout .= "    },\n";
$htmlout .= "    _utf8_decode : function (utftext) {\n";
$htmlout .= "        var string = \"\";\n";
$htmlout .= "        var i = 0;\n";
$htmlout .= "        var c = c1 = c2 = 0;\n";
$htmlout .= "        while ( i < utftext.length ) {\n";
$htmlout .= "            c = utftext.charCodeAt(i);\n";
$htmlout .= "            if (c < 128) {\n";
$htmlout .= "                string += String.fromCharCode(c);\n";
$htmlout .= "                i++;\n";
$htmlout .= "            }\n";
$htmlout .= "            else if((c > 191) && (c < 224)) {\n";
$htmlout .= "                c2 = utftext.charCodeAt(i+1);\n";
$htmlout .= "                string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));\n";
$htmlout .= "                i += 2;\n";
$htmlout .= "            }\n";
$htmlout .= "            else {\n";
$htmlout .= "                c2 = utftext.charCodeAt(i+1);\n";
$htmlout .= "                c3 = utftext.charCodeAt(i+2);\n";
$htmlout .= "                string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));\n";
$htmlout .= "                i += 3;\n";
$htmlout .= "            }\n";
$htmlout .= "        }\n";
$htmlout .= "        return string;\n";
$htmlout .= "    },\n";

$htmlout .= "    // private method for UTF-8 encoding \n";
$htmlout .= "    _utf8_encode : function (string) { \n";
$htmlout .= '        string = string.replace(/\r\n/g,"\n");'." \n";
$htmlout .= "       var utftext = \"\"; \n";
$htmlout .= "        for (var n = 0; n < string.length; n++) { \n";
$htmlout .= "            var c = string.charCodeAt(n);      \n";   
$htmlout .= "            if (c < 128) { \n";
$htmlout .= "                utftext += String.fromCharCode(c); \n";
$htmlout .= "            } \n";
$htmlout .= "            else if((c > 127) && (c < 2048)) { \n";
$htmlout .= "                utftext += String.fromCharCode((c >> 6) | 192); \n";
$htmlout .= "                utftext += String.fromCharCode((c & 63) | 128); \n";
$htmlout .= "            } \n";
$htmlout .= "            else { \n";
$htmlout .= "                utftext += String.fromCharCode((c >> 12) | 224); \n";
$htmlout .= "                utftext += String.fromCharCode(((c >> 6) & 63) | 128); \n";
$htmlout .= "                utftext += String.fromCharCode((c & 63) | 128); \n";
$htmlout .= "            } \n";
$htmlout .= "        } \n";
$htmlout .= "        return utftext; \n";
$htmlout .= "    } \n";
$htmlout .= "} \n\n";



$htmlout .= "function camcancel() { \n";
$htmlout .= "	document.edit.cmd2.value = ''; \n";
$htmlout .= "	location.href='".$simple_name."'; \n";
$htmlout .= "} \n\n";

$htmlout .= "function camsmile() { \n";
$htmlout .= "	document.edit.cmd2.value = ''; \n";
$htmlout .= "	document.getElementById('realcontent').value = Base64.encode(document.getElementById('content').value); \n";
$htmlout .= "	document.edit.submit(); \n";
$htmlout .= "} \n\n";
$htmlout .= "function camsmiler() {	 \n";
$htmlout .= "document.getElementById('content').focus();\n";
$htmlout .= "	document.getElementById('getback').value = window.document.getElementById('content').scrollTop; \n";
$htmlout .= "	document.getElementById('realcontent').value = Base64.encode(document.getElementById('content').value); \n";
$htmlout .= "	document.edit.submit(); \n";
$htmlout .= "} \n\n";
$htmlout .= "function lcmd(command) { \n";
$htmlout .= "	document.getElementById('cmd').value = command; \n";
$htmlout .= "} \n\n";
$htmlout .= "function pncmd(command) { \n";
$htmlout .= "	document.getElementById('cmd').value = command; \n";
$htmlout .= "	document.exec.submit(); \n";
$htmlout .= "} \n\n";

$htmlout .= "function pncmd2(command) { \n";
$htmlout .= "	document.getElementById('cmd3').value = command; \n";
$htmlout .= "	document.exec2.submit(); \n";
$htmlout .= "} \n\n";

$htmlout .= "function chdir(newdir) { \n";
$htmlout .= "	var tnewdir = 'cs '+newdir; \n";
$htmlout .= "	document.getElementById('cmd').value = tnewdir; \n";
$htmlout .= "	document.exec.submit(); \n";
$htmlout .= "} \n\n";
$htmlout .= "function tab_to_tab(e,el) {\n";
$htmlout .= "    //A function to capture a tab keypress in a textarea and insert 4 spaces and NOT change focus.\n";
$htmlout .= "    //9 is the tab key, except maybe it's 25 in Safari? oh well for them ...\n";
$htmlout .= "    if(e.keyCode==9){\n";
$htmlout .= "        var oldscroll = el.scrollTop; //So the scroll won't move after a tabbing\n";
$htmlout .= "        e.returnValue=false;  //This doesn't seem to help anything, maybe it helps for IE\n";
$htmlout .= "        //Check if we're in a firefox deal\n";
$htmlout .= "      	if (el.setSelectionRange) {\n";
$htmlout .= "      	    var pos_to_leave_caret=el.selectionStart+1;\n";
$htmlout .= "      	    //Put in the tab\n";
$htmlout .= "     	    el.value = el.value.substring(0,el.selectionStart) + '	' + el.value.substring(el.selectionEnd,el.value.length);\n";
$htmlout .= "            //There's no easy way to have the focus stay in the textarea, below seems to work though\n";
$htmlout .= "            setTimeout(\"var t=document.getElementById('content'); t.focus(); t.setSelectionRange(\" + pos_to_leave_caret + \", \" + pos_to_leave_caret + \");\", 0);\n";
$htmlout .= "      	}\n";
$htmlout .= "      	//Handle IE\n";
$htmlout .= "      	else {\n";
$htmlout .= "      		// IE code, pretty simple really\n";
$htmlout .= "      		document.selection.createRange().text='	';\n";
$htmlout .= "      	}\n";
$htmlout .= "        el.scrollTop = oldscroll; //put back the scroll\n";
$htmlout .= "    }\n";
$htmlout .= "}\n";



$htmlout .= "function camenter(e,el) {\n";
$htmlout .= "	if(e.shiftKey==true&&e.keyCode==13){\n";
$htmlout .= "        var oldscroll = el.scrollTop; //So the scroll won't move after a tabbing\n";
$htmlout .= "        e.returnValue=false;  //This doesn't seem to help anything, maybe it helps for IE\n";
$htmlout .= "        //Check if we're in a firefox deal\n";
$htmlout .= "      	if (el.setSelectionRange) {\n";
$htmlout .= "      	    var pos_to_leave_caret=el.selectionStart+5;\n";
$htmlout .= "      	    //Put in the tab\n";
$htmlout .= "     	    el.value = el.value.substring(0,el.selectionStart) + '<br/>' + el.value.substring(el.selectionEnd,el.value.length);\n";
$htmlout .= "            //There's no easy way to have the focus stay in the textarea, below seems to work though\n";
$htmlout .= "            setTimeout(\"var t=document.getElementById('content'); t.focus(); t.setSelectionRange(\" + pos_to_leave_caret + \", \" + pos_to_leave_caret + \");\", 0);\n";
$htmlout .= "      	}\n";
$htmlout .= "      	//Handle IE\n";
$htmlout .= "      	else {\n";
$htmlout .= "      		// IE code, pretty simple really\n";
$htmlout .= "      		document.selection.createRange().text='	';\n";
$htmlout .= "      	}\n";
$htmlout .= "        el.scrollTop = oldscroll; //put back the scroll\n";
$htmlout .= "	}\n";
$htmlout .= "} \n";


$htmlout .= "function camsave(e,el) {\n";
$htmlout .= "    //A function to capture a tab keypress in a textarea and insert 4 spaces and NOT change focus.\n";
$htmlout .= "    //9 is the tab key, except maybe it's 25 in Safari? oh well for them ...\n";
$htmlout .= "	if(e.ctrlKey==true&&e.keyCode==83){\n   //83=s  17=ctrl";
$htmlout .= "        var oldscroll = el.scrollTop; //So the scroll won't move after a tabbing\n";
$htmlout .= "        e.returnValue=false;  //This doesn't seem to help anything, maybe it helps for IE\n";
$htmlout .= "        //Check if we're in a firefox deal\n";
$htmlout .= "      	if (el.setSelectionRange) {\n";
$htmlout .= "				camsmiler(); \n";
$htmlout .= "			} \n";
$htmlout .= "	} \n";
$htmlout .= "} \n";



if(isset($_POST['cmd'])) { 
	$_POST['cmd'] = stripslashes($_POST['cmd']);
	$SPLIT = explode(' ', $_POST['cmd']);
	if(strtoupper($SPLIT['0']) == strtoupper('edit')) {
		$htmlout .= "function CodePress() { \n";
		$htmlout .= "	var contentzz = document.getElementById('content').value; \n";
		$htmlout .= "	contentzz = Base64.decode(contentzz);\n";
		$htmlout .= "	document.getElementById('content').value = contentzz; \n";

    $htmlout .= " if(!NiftyCheck())\n";
    $htmlout .= " 	return;\n";
    $htmlout .= " Rounded(\"div#editingstuff\",\"#808080\",\"#000000\");\n";

		if(isset($_POST['getback'])) { 
			$htmlout .= "window.document.getElementById('content').scrollTop=".$_POST['getback']."; \n";
		}
			$htmlout .= "} \n\n";	
	}
}
$htmlout .= "</script> \n";
$htmlout .= "</head> \n";

$ispfile = 'sohoadmin/config/isp.conf.php';
if(!file_exists($ispfile)){
	$ispfile = $_SESSION['doc_root'].'/'.$ispfile;
}

if(file_exists($ispfile)){   
  $filenameisp = $ispfile;
  $fileisp = fopen($filenameisp, "r");
  $body = fread($fileisp,filesize($filenameisp));
  $lines = split("\n", $body);
  $numLines = count($lines);
  
    for ($x=2;$x<=$numLines;$x++) {
      if (!eregi("#", $lines[$x])) {
        $variable = strtok($lines[$x], "="); 
        $value = strtok("\n");
        $value = rtrim($value);
        ${$variable} = $value;
        
        }
      }
  $_SESSION['doc_root'] = $doc_root;
    fclose($fileisp);
  $link = mysql_connect("$db_server", "$db_un","$db_pw");

  $sel = mysql_select_db("$db_name");
  echo mysql_error();
  $result = mysql_list_tables("$db_name");
  echo mysql_error();
  
  $query = 'SELECT * FROM login';
  $result = mysql_query($query);
    echo mysql_error();
  $blue = mysql_fetch_array($result);
  $username = $blue['Username'];
  $password = $blue['Password'];
  $URL = $this_ip;
  if($_SESSION['ftp_user_name']==''){
    $webmaster_pref = new userdata("webmaster_pref");
    $_SESSION['ftp_user_name'] = $webmaster_pref->get("ftp_username");
    $_SESSION['ftp_user_pass'] = $webmaster_pref->get("ftp_password");
  }
}

echo $htmlout;
if($_SESSION['newdir'] != '') {
	chdir($_SESSION['newdir']);
}

if(eregi('^cd ', $_POST['cmd']) || eregi('^cs ', $_POST['cmd'])) {
	$newdir = eregi_replace('^cd ', '', $_POST['cmd']);
	$newdir = eregi_replace('^cs ', '', $newdir);
	chdir($newdir);
	unset($_POST['cmd']);
	$_SESSION['newdir'] = getcwd();
}

if(isset($_POST['cmd'])) { 
	$SPLIT = explode(' ', $_POST['cmd']);
	if(strtoupper($SPLIT['0']) == strtoupper('EDIT')) {
		$ncmd = eregi_replace('^edit ', '', $_POST['cmd']);
		echo "<body onload=\"CodePress();\" style=\"background: #808080;\">\n";

		$PW = "<div id=\"div1\" style=\"position:block; top: 0px; valign:top; width:1230px; overflow:hidden;\">\n";
		$PW .= "<table style=\"width:2000px; height:50px;\"><tr><td style=\"width:100%; height:50px;\">\n";
	} elseif(strtoupper($SPLIT['0']) == 'RENAME'){
		rename(getcwd().'/'.$SPLIT['1'], getcwd().'/'.$SPLIT['2']);
		echo "<body onload=\"document.exec.cmd.focus();\" style=\"background: #808080;\">\n";
		$PW = "<div id=\"div1\" style=\"width:100%; height:100%; overflow:hidden;\">\n";
		$_SESSION['lastcmd'] = $_POST['cmd'];
		$_POST['lastcmd'] = $_POST['cmd'];
		$_POST['cmd'] = '';
	} elseif(strtoupper($SPLIT['0']) == 'WGET' || strtoupper($SPLIT['0']) == 'GET'){
		$remotefile = $SPLIT['1'];
   	$savetodir = getcwd().'/'.basename($remotefile);
   	$getfilez = new file_downloads($remotefile, $savetodir);
		$getfilez->getit();		
		echo "<body onload=\"document.exec.cmd.focus();\" style=\"background: #808080;\">\n";
		$_SESSION['lastcmd'] = $_POST['cmd'];
		$_POST['lastcmd'] = $_POST['cmd'];
		$_POST['cmd'] = '';
		
	} elseif(eregi('^find "', $_POST['cmd'])){
		echo "<body onload=\"document.exec.cmd.focus();\" style=\"background: #808080;\">\n";
		$find_ar = explode('"', $_POST['cmd']);
		$find_orig = $_POST['cmd'];
		
		$_POST['cmd'] = 'find '.getcwd().'/ -name "*.*" -exec grep -ls "'.$find_ar['1'].'" "{}" \;';
		
	} elseif(strtoupper($SPLIT['0']) == strtoupper('rm') && strtoupper($SPLIT['1']) != strtoupper('-rf') && !eregi('\*', $_POST['cmd'])) {
		$PW = "<div id=\"div1\" style=\"width:100%; height:100%; overflow:hidden; \">\n";
		if($SPLIT['1'] == '-f'){
			$split = explode('rm -f ', $_POST['cmd']);
			unlink($split['1']);
			echo "<body onload=\"document.exec.cmd.focus();\" style=\"background: #808080;\">\n";
			if(file_exists($split['1'])){		
				echo "<font color=\"".$red."\"><blink>Could Not Delete ".$split['1']."</blink></font>";		
			
			} else {
				echo "<font color=\"".$red."\">".$split['1']." was deleted!</font>";
				if(eregi('simple.php', $split['1'])){ exit; }
			}
		} else {
			$split = explode('rm ', $_POST['cmd']);
			
			echo "<body onload=\"document.exec.cmd.focus();\" style=\"background: #808080;\">\n";

		}
	
		unlink($split['1']);
		if(file_exists($split['1'])){ 
		   if($_SESSION['ftp_user_name']!=''){
		      if(function_exists('ftp_connect')) {
		         $conn_id = ftp_connect($_SESSION['ftp_server']);      
		         $login_result = ftp_login($conn_id, $_SESSION['ftp_user_name'], $_SESSION['ftp_user_pass']);    
		         if (($conn_id) || ($login_result)) {
		            $ftp_server = $_SESSION['ftp_server'];
		            $ftp_user_name = $_SESSION['ftp_user_name'];
		            $ftp_user_pass = $_SESSION['ftp_user_pass'];		            
		            if(!function_exists('ftp_chmod')) {
		                function ftp_chmod($ftp_stream, $themode, $filename)
		                {
		                    return ftp_site($ftp_stream, sprintf('CHMOD %o %s', $themode, $filename));
		                }
		            }  
		            if($_SESSION['doc_root'] == ''){
		               $_SESSION['doc_root'] = $_SERVER['DOCUMENT_ROOT'];
		            }     
		            $_SESSION['filearray'] = '';
		            $_SESSION['dirarrayz']='';
		   
		            $odir = getcwd();
		            chdir($odir);
		            $curdir = $_SESSION['doc_root'];
		            $dirarray = preg_split('/(\\\|\/)/', $_SESSION['doc_root'], -1, PREG_SPLIT_NO_EMPTY);
		   
		            $mode = 0777;
		            $mode2 = "0777";
		   
		            if(!ftp_chdir($conn_id, $curdir)) {
		               $ftpcwd = ftp_pwd($conn_id);
		               $lsarray = ftp_rawlist($conn_id, $ftpcwd);
		               $cccount = count($dirarray);
		               $zc = 0;
		               while($zc < $cccount) {
		                  ftp_chdir($conn_id, $dirarray[$zc]);
		                  $lastfolder = $dirarray[$zc];
		                  $zc++;
		               }
		            }
		            $ftpcwd2 = ftp_pwd($conn_id);
		            ftp_chdir($conn_id, '..');
		            ftp_chmod($conn_id, $mode, $lastfolder);
		            ftp_chdir($conn_id, $lastfolder);
		            ftp_chmod($conn_id, $mode, $split['1']);
		            ftp_close($conn_id);
		            chdir($curdir);
		   
		         }
		      }
				unlink($split['1']);
			}
		
		
		}
		
		if(file_exists($split['1'])){    
			echo "<font color=\"red\"><blink>Could Not Delete ".$split['1']."</blink></font>";
		} else {
		   echo "<font color=\"red\">".$split['1']." was deleted!</font>\n";
			if(eregi(basename(__FILE__).'$', $split['1'])){
			
				echo "<script language=\"javascript\">\n";
		  	echo "setTimeout(\"window.location.href='".basename(__FILE__)."'\", 1000);\n";
		   	echo "</script>\n";
				exit;	
			}
		}	
		
		$_SESSION['lastcmd'] = $_POST['cmd'];
		$_POST['lastcmd'] = $_POST['cmd'];
		$_POST['cmd'] = '';
	} else {
		$PW = "<div id=\"div1\" style=\"width:100%; height:100%; overflow:hidden;\">\n";
		echo "<body onload=\"document.exec.cmd.focus();\" style=\"background: #808080;\">\n";	
	}	
} else {
	$PW = "<div id=\"div1\" style=\"width:100%; height:100%; overflow:hidden;\">\n";
	echo "<body onload=\"document.exec.cmd.focus();\" style=\"background: #808080;\">\n";		
}

?>

<SCRIPT LANGUAGE="JavaScript">
function insertTags(tagOpen, tagClose, sampleText, wpTextbox1) {
//   alert('hit function');
	if (document.editform)
	   var txtarea = eval('document.editform.'+wpTextbox1);
//	   alert('is editform');
	else {
	   // some alternate form? take the first one we can find
		var areas = document.getElementsByTagName('textarea');
		var txtarea = areas[0];
//		alert('not editform');
	}

	// IE
	if (document.selection  && !is_gecko) {
//		var theSelection = document.selection.createRange().text;
//		if (!theSelection)
//			theSelection=sampleText;
//		txtarea.focus();		
//		if (theSelection.charAt(theSelection.length - 1) == " ") { // exclude ending space char, if any
//			theSelection = theSelection.substring(0, theSelection.length - 1);
//			document.selection.createRange().text = tagOpen + theSelection + tagClose + " ";
//		} else {
//			document.selection.createRange().text = tagOpen + theSelection + tagClose;
//		}
//
//	// Mozilla
	} else if(txtarea.selectionStart || txtarea.selectionStart == '0') {
		var replaced = false;
		var startPos = txtarea.selectionStart;
		var endPos = txtarea.selectionEnd;
		if (endPos-startPos)
			replaced = true;
		var scrollTop = txtarea.scrollTop;
		var myText = (txtarea.value).substring(startPos, endPos);
		if (!myText)
			myText=sampleText;
		if(tagOpen=='remove'){
			tagOpen = '';
			myText=myText.replace(/^(\/\/)/mg, "");
		} else {
			myText=myText.replace(/[\n\r]/mg, "\n//");
		}	
				
		if (myText.charAt(myText.length - 1) == " ") { // exclude ending space char, if any
			subst = tagOpen + myText.substring(0, (myText.length - 1)) + tagClose + " ";
		} else {
			subst = tagOpen + myText + tagClose;
		}
		txtarea.value = txtarea.value.substring(0, startPos) + subst +
			txtarea.value.substring(endPos, txtarea.value.length);
		txtarea.focus();
		//set new selection
		if (replaced) {
			var cPos = startPos+(tagOpen.length+myText.length+tagClose.length);
			txtarea.selectionStart = cPos;
			txtarea.selectionEnd = cPos;
		} else {
			txtarea.selectionStart = startPos+tagOpen.length;
			txtarea.selectionEnd = startPos+tagOpen.length+myText.length;
		}
		txtarea.scrollTop = scrollTop;
	}

	if (txtarea.createTextRange)
		txtarea.caretPos = document.selection.createRange().duplicate();
}
</SCRIPT>

<?php
$SPLIT = explode(' ', $_POST['cmd']);

if(strtoupper($SPLIT['0']) != strtoupper('edit')) {
	echo "<div id=\"ie5menu\" class=\"skin0\" onMouseover=\"highlightie5(event)\" onMouseout=\"lowlightie5(event)\" onClick=\"jumptoie5(event)\" display:none> \n";
	echo "<div class=\"menuitems\" onclick=\"insertTags('//', '', '', 'message');\" url=\"#comment\">zzzzz Selected</div> \n";
	echo "<div class=\"menuitems\" onclick=\"insertTags('remove', '', '', 'message');\" url=\"#comment\">zzzz Selected</div> \n";
	echo "</div> \n";
} else {
	echo "<div id=\"ie5menu\" class=\"skin0\" style=\"\" onMouseover=\"highlightie5(event)\" onMouseout=\"lowlightie5(event)\" onClick=\"jumptoie5(event)\" display:none> \n";
	echo "<div class=\"menuitems\" onclick=\"insertTags('//', '', '', 'message');\" url=\"#comment\">Comment Out Selected</div> \n";
	echo "<div class=\"menuitems\" onclick=\"insertTags('remove', '', '', 'message');\" url=\"#comment\">Uncomment Selected</div> \n";
	echo "</div> \n";
}

?>

<script language="JavaScript1.2">

//set this variable to 1 if you wish the URLs of the highlighted menu to be displayed in the status bar
var display_url=0

var ie5=document.all&&document.getElementById
var ns6=document.getElementById&&!document.all
if (ie5||ns6)
var menuobj=document.getElementById("ie5menu")


function showmenuie5(e){
<?php
if(strtoupper($SPLIT['0']) == strtoupper('edit')) {
	echo "	var areas = document.getElementsByTagName('textarea'); \n";
	//echo "	var areas = document.getElementById('content'); \n";
	
	echo "	var txtarea = areas[0]; \n";
	//echo "	var txtarea = document.getElementById('content'); \n";
	echo "	if((txtarea.selectionEnd - txtarea.selectionStart) != 0) { \n";
		//alert(txtarea.selectionStart+"   "+txtarea.selectionEnd);	
}
?>
		//Find out how close the mouse is to the corner of the window
		var rightedge=ie5? document.body.clientWidth-event.clientX : window.innerWidth-e.clientX
		var bottomedge=ie5? document.body.clientHeight-event.clientY : window.innerHeight-e.clientY
		
		//if the horizontal distance isn't enough to accomodate the width of the context menu
		if (rightedge<menuobj.offsetWidth)
		//move the horizontal position of the menu to the left by it's width
		menuobj.style.left=ie5? document.body.scrollLeft+event.clientX-menuobj.offsetWidth : window.pageXOffset+e.clientX-menuobj.offsetWidth
		else
		//position the horizontal position of the menu where the mouse was clicked
		menuobj.style.left=ie5? document.body.scrollLeft+event.clientX : window.pageXOffset+e.clientX
		
		//same concept with the vertical position
		if (bottomedge<menuobj.offsetHeight)
		menuobj.style.top=ie5? document.body.scrollTop+event.clientY-menuobj.offsetHeight : window.pageYOffset+e.clientY-menuobj.offsetHeight
		else
		menuobj.style.top=ie5? document.body.scrollTop+event.clientY : window.pageYOffset+e.clientY
		
		menuobj.style.visibility="visible"
		return false
<?php

if(strtoupper($SPLIT['0']) == strtoupper('edit')) {
	echo "	} \n";
}
?>
}

function hidemenuie5(e){
menuobj.style.visibility="hidden"
}

function highlightie5(e){
var firingobj=ie5? event.srcElement : e.target
if (firingobj.className=="menuitems"||ns6&&firingobj.parentNode.className=="menuitems"){
if (ns6&&firingobj.parentNode.className=="menuitems") firingobj=firingobj.parentNode //up one node
firingobj.style.backgroundColor="highlight"
firingobj.style.color="white"
if (display_url==1)
window.status=event.srcElement.url
}
}

function lowlightie5(e){
var firingobj=ie5? event.srcElement : e.target
if (firingobj.className=="menuitems"||ns6&&firingobj.parentNode.className=="menuitems"){
if (ns6&&firingobj.parentNode.className=="menuitems") firingobj=firingobj.parentNode //up one node
firingobj.style.backgroundColor=""
firingobj.style.color="black"
window.status=''
}
}

function jumptoie5(e){
var firingobj=ie5? event.srcElement : e.target
if (firingobj.className=="menuitems"||ns6&&firingobj.parentNode.className=="menuitems"){
if (ns6&&firingobj.parentNode.className=="menuitems") firingobj=firingobj.parentNode
if (firingobj.getAttribute("target"))
window.open(firingobj.getAttribute("url"),firingobj.getAttribute("target"))
else
window.location=firingobj.getAttribute("url")
}
}

<?php
if(strtoupper($SPLIT['0']) == strtoupper('edit')) {

echo "	if (ie5||ns6){ \n";
echo "		menuobj.style.display='' \n";
echo "		document.oncontextmenu=showmenuie5 \n";
echo "		document.onclick=hidemenuie5 \n";
echo "	} \n";
}
?>

</script>

<style type="text/css">

#dropmenudiv{
position:absolute;
border:1px solid black;
//border-bottom-width: 0;
font:normal 12px Verdana;
line-height:18px;
z-index:100;
}

#dropmenudiv a{
width: 100%;
display: inline;
text-indent: 3px;
//border-bottom: 1px solid black;
padding: 1px 0;
text-decoration: none;
font-weight: bold;
color: white;
}


#dropmenudiv a:hover { /*hover background color*/
	color: orange;
}



#dropmenudiv a.strikeout {	
	color: white;
	text-decoration: none;
}

#dropmenudiv a.strikeout:hover{
	color: rgb(255, 47, 55);
	text-decoration: line-through;
}




</style>

<script type="text/javascript">
function hideit(obj){
	dropmenuobj.style.visibility='hidden';
}
/***********************************************
* AnyLink Drop Down Menu- © Dynamic Drive (www.dynamicdrive.com)
* This notice MUST stay intact for legal use
* Visit http://www.dynamicdrive.com/ for full source code
***********************************************/

//Contents for menu 1
var menu1=new Array()
menu1[0]=''
menu1[1]=''
menu1[2]=''
menu1[3]=''
menu1[4]=''

var menuwidth='65px' //default menu width
var menubgcolor='#808080'  //menu bgcolor
var disappeardelay=550  //menu disappear speed onMouseout (in miliseconds)
var hidemenu_onclick="no" //hide menu when user clicks within menu?

/////No further editting needed

var ie4=document.all
var ns6=document.getElementById&&!document.all

if (ie4||ns6)
document.write('<div id="dropmenudiv" style="visibility:hidden;width:'+menuwidth+';background-color:'+menubgcolor+'" onMouseover="clearhidemenu()" onMouseout="dynamichide(event)"></div>')

function getposOffset(what, offsettype){

	var totaloffset=(offsettype=="left")? what.offsetLeft : what.offsetTop;
	var parentEl=what.offsetParent;

	while (parentEl!=null){	
		//alert(what.parentNode+"\n"+parentEl+" \n"+parentEl.offsetTop+"\n"+totaloffset);
		totaloffset=(offsettype=="left")? totaloffset+parentEl.offsetLeft : parentEl.offsetTop+totaloffset;
		parentEl=parentEl.offsetParent;
	}
	
	if(offsettype == 'top'){
		totaloffset= totaloffset - document.getElementById('filezlistcon').scrollTop;
	}
	//alert(totaloffset);
	return totaloffset;
}


function showhide(obj, e, visible, hidden, menuwidth){
if (ie4||ns6)
dropmenuobj.style.left=dropmenuobj.style.top="-500px"
if (menuwidth!=""){
dropmenuobj.widthobj=dropmenuobj.style
dropmenuobj.widthobj.width=menuwidth
}
if (e.type=="click" && obj.visibility==hidden || e.type=="mouseover")
obj.visibility=visible
else if (e.type=="click")
obj.visibility=hidden
}

function iecompattest(){
return (document.compatMode && document.compatMode!="BackCompat")? document.documentElement : document.body
}

function clearbrowseredge(obj, whichedge){
var edgeoffset=0
if (whichedge=="rightedge"){
var windowedge=ie4 && !window.opera? iecompattest().scrollLeft+iecompattest().clientWidth-15 : window.pageXOffset+window.innerWidth-15
dropmenuobj.contentmeasure=dropmenuobj.offsetWidth
if (windowedge-dropmenuobj.x < dropmenuobj.contentmeasure)
edgeoffset=dropmenuobj.contentmeasure-obj.offsetWidth
}
else{
var topedge=ie4 && !window.opera? iecompattest().scrollTop : window.pageYOffset
var windowedge=ie4 && !window.opera? iecompattest().scrollTop+iecompattest().clientHeight-15 : window.pageYOffset+window.innerHeight-18

dropmenuobj.contentmeasure=dropmenuobj.offsetHeight
if (windowedge-dropmenuobj.y < dropmenuobj.contentmeasure){ //move up?
edgeoffset=dropmenuobj.contentmeasure+obj.offsetHeight
if ((dropmenuobj.y-topedge)<dropmenuobj.contentmeasure) //up no good either?
edgeoffset=dropmenuobj.y+obj.offsetHeight-topedge
}
}

return edgeoffset
}

function populatemenu(what){
if (ie4||ns6)
dropmenuobj.innerHTML=what.join("")
}

function renamefile(oldname){
	var newname = document.getElementById('rename'+oldname).value;	
	var command = 'rename '+oldname+' '+newname;
	document.getElementById('cmd').value = command;
	document.exec.submit();
}



function dropdownmenu(obj, e, menucontents, menuwidth, file_name){
<?php 
	echo "var lil_file_name = file_name;\n";
	if($win == 'yes'){
		$dird = '';
		$dirzw = explode('\\', getcwd());
		foreach($dirz as $d=>$e) {
			if($d != '0') {
				$dird .= '\\\\'.$e;
				//$dirdzw .= "<font color=\"white\">\\</font><a href=\"#\" onClick=\"chdir('".$dird."');\" style=\"text-decoration: none; color:#FF2F37;\">".$e."</a>";	
			} else {
				$dird .= $e;
				//$dirdz .= "<font color=\"white\"></font><a href=\"#\" onClick=\"chdir('".$dird."');\" style=\"text-decoration: none; color:#FF2F37;\">".$e."</a>";	
			}
		}
		echo $dird;
		
		echo "var file_name = \"".str_replace('\\', '/', getcwd())."/\"+file_name;\n";	
	} else {
		echo "var file_name = \"".getcwd()."/\"+file_name;\n";
	}
	
	$scripturl = $_SERVER['HTTP_HOST'].str_replace($_SESSION['doc_root'], '', getcwd());
	$scripturl = str_replace('\\', '/', $scripturl).'/';
	echo "var gotourl = '".$scripturl."';";
?>
var obj = obj;
menu1[0]='<a href="#Move"></a><br/>'
menu1[1]='<a href="#" onclick="pncmd(\'edit '+file_name+'\');">&nbsp;&nbsp;&nbsp;&nbsp;Edit</a><br/>'
menu1[2]='<a target="_blank" href="http://'+gotourl+lil_file_name+'" onclick="hideit(\''+obj+'\');">&nbsp;&nbsp;&nbsp;&nbsp;View</a><br/>'
menu1[3]='<a href="#" onclick="renamefile(\''+file_name+'\')">&nbsp;&nbsp;&nbsp;&nbsp;Rename</a>&nbsp;&nbsp;&nbsp;&nbsp;<input style="background-color: black; color: white;" id="rename'+file_name+'" type=text value="'+file_name+'" size="25"><br/>'

menu1[4]='<a href="#Move"></a><br/>'
menu1[5]='<font color="white">&nbsp;&nbsp;&nbsp;&nbsp;<a class="strikeout" href="#" onclick="pncmd(\'rm '+file_name+'\');">Delete</a></font><br/>'
menu1[6]='<a href="#Move"></a><br/>'
if (window.event) event.cancelBubble=true
else if (e.stopPropagation) e.stopPropagation()
clearhidemenu()
dropmenuobj=document.getElementById? document.getElementById("dropmenudiv") : dropmenudiv
populatemenu(menucontents)

if (ie4||ns6){
showhide(dropmenuobj.style, e, "visible", "hidden", menuwidth)
dropmenuobj.x=getposOffset(obj, "left")
dropmenuobj.y=getposOffset(obj, "top")
dropmenuobj.style.left=dropmenuobj.x-clearbrowseredge(obj, "rightedge")+"px"
dropmenuobj.style.top=(dropmenuobj.y-clearbrowseredge(obj, "bottomedge")+obj.offsetHeight)-document.getElementById('div1').scrollTop+"px"

}

return clickreturnvalue()
}

function clickreturnvalue(){
if (ie4||ns6) return false
else return true
}

function contains_ns6(a, b) {
while (b.parentNode)
if ((b = b.parentNode) == a)
return true;
return false;
}

function dynamichide(e){
if (ie4&&!dropmenuobj.contains(e.toElement))
delayhidemenu()
else if (ns6&&e.currentTarget!= e.relatedTarget&& !contains_ns6(e.currentTarget, e.relatedTarget))
delayhidemenu()
}

function hidemenu(e){
if (typeof dropmenuobj!="undefined"){
if (ie4||ns6)
dropmenuobj.style.visibility="hidden"
}
}

function delayhidemenu(){
if (ie4||ns6)
delayhide=setTimeout("hidemenu()",disappeardelay)
}

function clearhidemenu(){
if (typeof delayhide!="undefined")
clearTimeout(delayhide)
}

if (hidemenu_onclick=="yes")
document.onclick=hidemenu


   var lineObjOffsetTop = 2;
   function createTextAreaWithLines(id){
 //     var el = document.createElement('DIV');
      
      var ta = document.getElementById(id);
//      ta.parentNode.insertBefore(el,ta);
//      //el.appendChild(ta);
//      el.className='textAreaWithLines';
//      //el.id='nums';
//      el.style.width = (12) + 'px';
//      //el.style.width = (ta.offsetWidth + 30) + 'px';
//      ta.style.position = 'relative';
//      ta.style.left = '30px';
//      ta.style.zIndex = '900';
//      el.style.zIndex = '1800';
//      el.style.height = (ta.offsetHeight -20) + 'px';
//      el.style.overflow='hidden';
//      el.style.position = 'absolute';
//      el.style.width = (30) + 'px';
//      el.style.fontFamily = 'Courier New'; 
//      el.style.fontSize = '8pt';
      
      var lineObj = document.createElement('DIV');
      lineObj.style.position = 'relative';
      lineObj.style.top = lineObjOffsetTop + 'px';

      //lineObj.style.left = '-1px';
      lineObj.style.left = '0px';
      lineObj.style.width = '30px';
      //lineObj.style.zIndex = '500';
      lineObj.style.fontFamily = 'Courier New'; 
      lineObj.style.fontSize = '8pt';
     // lineObj.style.backgroundColor = '#ffffff';
      ta.parentNode.insertBefore(lineObj, ta);
      lineObj.style.height = (ta.offsetHeight) + 'px';
      lineObj.style.textAlign = 'right';
      lineObj.className='lineObj';
      var string = '<div style="overflow:hidden; width:29px; color:black; background:white; border-left: 4px solid black;">1';
      for(var no=2;no<2999;no++){
         if(string.length>0)string = string + '<br>\n';
         string = string + no;
      }
      string = string + '</div>';
      
      
      var el = document.createElement('DIV');
      lineObj.parentNode.insertBefore(el,lineObj);
      el.appendChild(lineObj);
      el.className='textAreaWithLines';
     // el.style.width = (12) + 'px';
      //el.style.width = (ta.offsetWidth + 30) + 'px';
      el.style.zIndex = '1800';
      el.style.height = (ta.offsetHeight -17) + 'px';
      el.style.overflow='hidden';
      el.style.position = 'absolute';
      el.style.width = (34) + 'px';
      el.style.fontFamily = 'Courier New'; 
      el.style.fontSize = '8pt';
      ta.style.position = 'relative';
      ta.style.left = '21px';
      
      
      
      
      //ta.onkeydown = function() { positionLineObj(lineObj,ta); };
      ta.onmousedown = function() { positionLineObj(lineObj,ta); };
      ta.onscroll = function() { positionLineObj(lineObj,ta); };
      ta.onblur = function() { positionLineObj(lineObj,ta); };
      ta.onfocus = function() { positionLineObj(lineObj,ta); };
      ta.onmouseover = function() { positionLineObj(lineObj,ta); };
      
		var myInterval = window.setInterval(function (a,b) {
			positionLineObj(lineObj,ta);
		},400);
      
      
      lineObj.innerHTML = string;      
   }
   
   function positionLineObj(obj,ta){
      obj.style.top = (ta.scrollTop * -1 + lineObjOffsetTop) + 'px';            
   }
</script>

<style type="text/css">
   #content{
      width:800px;
      z-Index: 802;
   }
	
.textAreaWithLines {
	color: white;
	z-Index: 800;
}
.filelist {
 padding:0px 2px 0px 2px;
 font-size: 8pt;
}
</style>
<?php


	if($_POST['cmd'] != ''){
		if(eregi('^cd ', $_POST['cmd']) || eregi('^cs ', $_POST['cmd'])){
			if($_POST['lastcmd'] != ''){
				$lastsesscmd = $_POST['lastcmd'];
			}
			
		} elseif(strtoupper($SPLIT['0']) == 'FIND'){
			$find_orig = str_replace("'", '"', $find_orig);
			$lastsesscmd = str_replace('"', "&quot;", $find_orig);
		} else {
			$lastsesscmd = $_POST['cmd'];
		}
	}
if($win=='yes'){
	$lastsesscmd = str_replace('\\\\', '\\', $_SESSION['lastcmd']);	
}
if($lastsesscmd != ''){
	if(!is_array($_SESSION['lastcmd'])){
		$_SESSION['lastcmd'] = array();
	}
	if(!in_array($lastsesscmd, $_SESSION['lastcmd'])){
		$_SESSION['lastcmd'][] = $lastsesscmd;
	}
}

if(is_array($_SESSION['lastcmd'])){
	$cmdcount = count($_SESSION['lastcmd']) - 1;
	$lstcmd = $_SESSION['lastcmd'][$cmdcount];
	if($cmdcount > 30){
		array_shift($_SESSION['lastcmd']);
	}

	$lastcmd_drop = array_reverse($_SESSION['lastcmd']);	
	$lastcmd_dropd = "<select style=\"color: gray; width: 300px; background:black; \" onChange=\"lcmd(document.getElementById('lastcmdsel').value);\" name=lastcmdsel id=lastcmdsel>\n";
	$lastcmd_dropd .= "<option style=\"color:gray;\">Last Command...</option>\n";	
	foreach($lastcmd_drop as $akey=>$aval){
		$lastcmd_dropd .= "<option style=\"color:white;\" value=\"".$aval."\">".eregi_replace("\n", '', $aval)."</option>\n";		
	}
	$lastcmd_dropd .= "</select>\n";
}



if($_SESSION['whoami'] == ''){
	if(shell_exec('echo hi') == '') { 
		$_SESSION['exectype'] = 'exec';
		$whoami = exec("whoami");			
	} else {
		$_SESSION['exectype'] = 'shell_exec';
		$whoami = shell_exec("whoami");
	} 
} else {
	$whoami = $_SESSION['whoami'];
}

if($whoami == ''){
  $_SESSION['doc_root'] = eregi_replace('[\\]{2}', DIRECTORY_SEPARATOR, $_SESSION['doc_root']);
  $testfile = $_SESSION['doc_root'].DIRECTORY_SEPARATOR."test.php";
  $file = fopen($testfile, "w");
  if(fwrite($file, "<?php\necho get_current_user();\n?>")) {
    //echo fileowner($testfile);
    ob_start();
      include_r("http://".$this_ip."/test.php");
      $system_owner = ob_get_contents();
    ob_end_clean();
    fclose($file);
    if($system_owner == ''){
      ob_start();
      echo phpinfo();
      $php_info = ob_get_contents();
      ob_end_clean();
      if(!eregi('--disable-posix', $php_info) && function_exists("posix_getpwuid")){  
        $fileowner = posix_getpwuid(fileowner('test'));
        $system_owner = $fileowner['name'];
      }
      unlink("test.php");       
    }             
  }
  $whoami = $system_owner;
}


if($whoami != ''){
	$_SESSION['whoami'] = $whoami;
}

if($win=='yes'){
	$dirz = explode('\\', getcwd());
} else {
	$dirz = explode('/', getcwd());
}
if($username != ''){
	$PW .= "<span class=button><button class=\"nav_main1\" onMouseover=\"this.className='nav_mainon1';\" onMouseout=\"this.className='nav_main1';\" style=\"font-size: 9px; border:0px solid; color:white;\" onClick=\"document.LOGIN.submit();\">login: ".$username."/".$password."</button></span>&nbsp;";
}

if ((eregi('[^_]EXEC', strtoupper(ini_get("disable_functions"))) && eregi('SHELL_EXEC', strtoupper(ini_get("disable_functions")))) ||  (exec('echo hi') != 'hi' && shell_exec('echo hi') != 'hi')){
  $PW .=  "<span style=\"border: 1px solid white; background:red\"><font size=2 color=white>&nbsp;exec disabled!&nbsp;</font></span>";
}
$PW .= "&nbsp;&nbsp;<form style=\"display: inline;\" name=\"diagnostics\"  method=\"GET\" target=\"_BLANK\" action=\"#\">";
$PW .= "&nbsp;&nbsp;&nbsp;<button class=\"nav_main2\" onMouseover=\"this.className='nav_mainon2';\" onMouseout=\"this.className='nav_main2';\" style=\"font-size: 9px; border:0px solid; color:white;\" onClick=\"document.diagnostics.submit();\">Diagnostics</button>&nbsp;\n";
$PW .= "<input type=\"hidden\" name=\"special\" value=\"phpinfo\">\n";
$PW .= "</form>\n";
//$PW .= "&nbsp;&nbsp;<a style=\"text-decoration: none; color:#DF2968;\" href=\"simple.php?special=phpinfo\" target=\"_BLANK\">Diagnostics</a>&nbsp;&nbsp;\n";  

$PW .= "<form style=\"display: inline;\" name=\"chmod\"  method=\"POST\" action=\"#\">";

$PW .= "&nbsp;&nbsp;<button class=\"nav_logout\" onMouseover=\"this.className='nav_logouton';\" onMouseout=\"this.className='nav_logout';\" style=\"font-size: 9px; border:0px solid; color:white;\" onClick=\"document.chmodit.submit();\">FTP Chmod</button>\n";

$PW .= "<span style=\"font-size: 9px; color:white;\"><input type=\"hidden\" name=\"chmodit\" value=\"up\"><input type=\"text\" style=\"background-color:black; color:white; width:120px; font-size: 9px;\" name=\"ftp_server\" value=\"".$_SESSION['ftp_server']."\">&nbsp;UN:<input type=\"text\" style=\"background-color:black; color:white; width:120px; font-size: 9px;\" name=\"ftp_user_name\" value=\"".$_SESSION['ftp_user_name']."\">&nbsp;PW:<input type=\"text\" style=\"font-size: 9px; background-color:black; color:white; width:120px;\" name=\"ftp_user_pass\" value=\"".$_SESSION['ftp_user_pass']."\"></span>\n";
$PW .= "</form>\n";


if($username != ''){
	$PW .= "<form style=\"display: inline;\" name=LOGIN method=post target=\"_blank\" action=\"http://".$this_ip."/sohoadmin/index.php\" target=\"_blank\" action=\"http://".$this_ip."/sohoadmin/includes/getlogin.php\" target=\"_blank\" action=\"http://".$this_ip."/sohoadmin/version.php\" target=\"_blank\">\n";
	$PW .= "<input type=HIDDEN name=PHP_AUTH_USER value=\"".$username."\">\n<input type=HIDDEN name=PHP_AUTH_PW value=\"".$password."\">\n<input type=hidden name=process value=\"1\">\n";
	$PW .= "</form>\n";	
}

if($helpmehelpyou != '1'){
	if($win == 'yes'){
		$killpath = str_replace('\\', '\\\\', dirname(__FILE__))."\\\\".basename(__FILE__);
	} else {
		$killpath = dirname(__FILE__)."/".basename(__FILE__);
	}
	$PW .=  "&nbsp;&nbsp;&nbsp;<font color=red><s><a href=\"#\" onClick=\"document.getElementById('cmd').value='rm ".$killpath."';document.exec.submit();\" style=\"text-decoration: none; color:red;\">KILL&nbsp;ME</a></s></font>";
}
$PW .= "&nbsp;&nbsp;&nbsp;".$lastcmd_dropd;
//$PW .= "&nbsp;&nbsp;&nbsp;<font size='2' color=white>Last Cmd:&nbsp;<a style=\"text-decoration: none; color:blue;\" href=\"#\" onClick=\"lcmd('".$lstcmd."');\">".eregi_replace("\n", '', $lstcmd)."</a></font>";
$PW .= "&nbsp;&nbsp;&nbsp;<font size='2' color=white>".$ftpdisp."</font>";
	

$dird = '';
$dirdz = '';
$tcount = count($dirz) - 1;
foreach($dirz as $d=>$e) {
	if($win=='yes'){
		if($d != '0') {
			$dird .= '\\\\'.$e;
			$dirdz .= "<font color=\"white\">\\</font><a href=\"#\" onClick=\"chdir('".$dird."');\" style=\"text-decoration: none; color:#FF2F37;\">".$e."</a>";	
		} else {
			$dird .= $e;
			$dirdz .= "<font color=\"white\"></font><a href=\"#\" onClick=\"chdir('".$dird."');\" style=\"text-decoration: none; color:#FF2F37;\">".$e."</a>";	
		}
	} else {
		if($d != '0') {
			$dird .= '/'.$e;
			$dirdz .= "<font color=\"white\">/</font><a href=\"#\" onClick=\"chdir('".$dird."');\" style=\"text-decoration: none; color:#FF2F37;\">".$e."</a>";	
		}
	}
}
//$PW .= $dirdz;




$PW .= "<table style=\"valign:top;\" cellpadding=\"0\" cellspacing=\"0\" width=100%><tr style=\"valign:top;\"><td style=\"valign:top;\" width=100%>\n";

$PW .= "<FORM style=\"display: inline;\" name=exec2 id=exec2 ACTION=\"#\" target=\"_BLANK\" METHOD=POST>\n";
$PW .= "<input id=cmd3 type=hidden name=cmd value=\"\">\n</form>";

$PW .= "\n<FORM style=\"display: inline;\" name=exec id=exec ACTION=\"#\" METHOD=POST>\n";
$show_searching = '';
if(strtoupper($SPLIT['0']) != strtoupper('EDIT')) {
	$show_searching = "onKeydown=\"searching(event, document.getElementById('cmd').value);\"";
}

echo $PW .= "</strong><font size='2' color=white>[<font color=orange>".eregi_replace("\n", '', $whoami)."</font>@<font color=\"blue\">".php_uname("n")."</font> ".$dirdz."]#&nbsp;</font><INPUT TYPE=TEXT ID=cmd NAME=cmd style=\"background-color:black; color:white; width:400px;\" value=\"\"".$show_searching.">\n";
$_SESSION['whoami'] = $whoami;



if($_POST['realcontent'] != '') {
	$savecontent = base64_decode($_POST['realcontent']);
	$filesave = fopen($_POST['filename'], "w+");		
	if(!fwrite($filesave, $savecontent)) {
		fclose($filesave);
		echo "<font color=".$red."> COULD NOT SAVE FILE!!!</font>";
	} else {
		echo "<font color=green> file saved!!!</font>";
	}
} 

if(function_exists('mysql_query')){
	if(mysql_connect("$db_server", "$db_un","$db_pw")){
		mysql_query("SET SESSION SQL_MODE = ''");
		if(!$sel = mysql_select_db("$db_name")) {
		} else {
			echo "<div style=\"overflow:hidden; display: inline;\"><font size='2' color=orange>MySQL</font><input id=\"mysqlhidden\" name=\"mysqlhidden\" type=\"hidden\" ".$_SESSION['mysql_query']."><input id=\"mysql_query\" name=\"mysql_query\" type=\"checkbox\" onClick=\"mysqlz();\" ".$_SESSION['mysql_query']."></div>";
		}
	}
}

echo "<div style=\"overflow:hidden; display: inline;\"><font size='2' color=white>&nbsp;Dir&nbsp;Filesize?</font>&nbsp;<input id=\"dirfileshidden\" name=\"dirfileshidden\" type=\"hidden\" ".$_SESSION['dirfilesize']."><input id=\"dirfiles\" name=\"dirfilesize\" type=\"checkbox\" onClick=\"dirfilesizez();\" ".$_SESSION['dirfilesize'].">";
if(eregi('^edit ', $_POST['cmd'])){
	$fname = eregi_replace('^edit ', '', $_POST['cmd']);
	$ffname = eregi_replace(basename($fname), "<font color=\"red\">".basename($fname)."</font>", $fname);
}
echo "</div>";

if(isset($_POST['MAX_FILE_SIZE']) && $_FILES['ulthisfile']['size'] > 0){
	$fileName = $_FILES['ulthisfile']['name'];
	$tmpName  = $_FILES['ulthisfile']['tmp_name'];
	$fileSize = $_FILES['ulthisfile']['size'];
	$fileType = $_FILES['ulthisfile']['type'];	
	$fp1 = fopen($tmpName, 'r');
	$upcontent = fread($fp1, filesize($tmpName));
	fclose($fp1);
	unlink($fileName);
	if(!file_exists($fileName)){		
		$newfiley = fopen($fileName, "w+");		
		if(!fwrite($newfiley, $upcontent)) {
			echo "<font color=red> COULD NOT SAVE FILE!!!</font>";
		} else {
			echo "<font color=green> File Uploaded!!!</font>";
		}	
		fclose($newfiley);
	}
}	




echo "</form></font>\n"; 
$SPLIT = explode(' ', $_POST['cmd']);
echo "</div>\n";
echo "</td></tr></table>\n";
echo "<span style=\"background:black; color:white;\"><br/></span>\n";

if(strtoupper($SPLIT['0']) == strtoupper('edit')) {
	if($_POST['realcontent'] != ''){
		$content = base64_decode($_POST['realcontent']);
	} else {
		$ncmd = eregi_replace('^edit ', '', $_POST['cmd']);
		$file = fopen($ncmd, "r"); 
      $content = fread($file, filesize($ncmd));      
      fclose($file);
	}
		$concnt = explode("\n", $content);
		$concnt = count($concnt);

		if(eregi("MSIE", $_SERVER['HTTP_USER_AGENT'])){
			$overflow = "hidden";
		} else {
			$overflow = "auto";
		}
	//	echo "<div id=\"content\" style=\"position:fixed; z-index: 2; valign:top; font-family:Courier New; font-size:8pt; width:99%; height:86%; background: #808080; border-top: 2px solid #ffffff; border-bottom: 0px solid #ffffff; border 1px solid #A6A498; overflow: ".$overflow.";\">\n";

//
//      echo "<script type=\"text/javascript\">\n";
//      echo "window.onload=function(){\n";
//      echo "if(!NiftyCheck())\n";
//      echo "    return;\n";
//      echo "Rounded(\"div#editingstuff\",\"#808080\",\"#ffffff\");\n";
//      echo "}\n";
//      echo "</script>\n";

    //  echo "<br/><div id=\"filezlist\" style=\"background: #000000 url('http://securexfer.net/camerons_simple/Mitch-simple.jpg') no-repeat fixed bottom right;\">";



			//echo "<div style=\"position:relative; height:602px; width: 1230px; color:white; background: #ffffff;\">\n";
			echo "<div id=\"editingstuff\" style=\"overflow:hidden; position:relative;  width: 1230px; color:white; background-color: black;\">\n";
      echo "\n<form id=edit name=edit method=POST action=\"#\">";
      echo "<textarea ID=\"content\" name=\"content\" spellcheck=\"false\" WRAP=\"OFF\"  style=\"padding-left: 19px; border:1px solid black; height:600px; width: 1200px; color:white; background: #000000 url('http://securexfer.net/camerons_simple/Mitch-simple.jpg') no-repeat fixed bottom right; overflow-y:scroll; overflow-x:scroll; overflow:scroll; font-family:Courier New; font-size:8pt;\" onkeydown=\"tab_to_tab(event,document.getElementById('content')); camsave(event,document.getElementById('content')); camenter(event,document.getElementById('content'));\">\n";
     // echo "<textarea spellcheck=\"false\" WRAP=\"OFF\"  style=\"height:100%; width:95%; padding-left:".(8.5 * strlen($concnt))."px; color:white; background: #000000 url('http://securexfer.net/camerons_simple/Mitch-simple.jpg') no-repeat fixed bottom right; overflow:scroll; font-family:Courier New; font-size:8pt;\" ID=\"content\" onkeydown=\"tab_to_tab(event,document.getElementById('content')); camsave(event,document.getElementById('content')); camenter(event,document.getElementById('content'));\" name=\"content\">\n";
      echo base64_encode($content);
      echo "</textarea>\n";    
      
      echo "<br><strong><font color=red>&nbsp;&nbsp;Editing: ".basename($fname)."</strong></font>&nbsp;&nbsp;<font size=2 color=white>".getcwd().'/'.basename($fname)."</font>";
      echo "<input type=hidden ID=\"realcontent\" name=realcontent value=\"\">\n"; 
      echo "<input type=hidden name=filename value=\"".$ncmd."\">\n"; 
      echo "<input type=hidden id=lastcmd name=lastcmd value=\"".$lstcmd."\">\n"; 
      echo "<input type=hidden id=cmd2 name=cmd value=\"".$_POST['cmd']."\">\n"; 
      echo "<input type=hidden ID=\"getback\" name=getback value=\"\">\n"; 

      echo "</form>\n"; 
			
			echo "</div>\n";
		
			echo "<script type=\"text/javascript\">\n";
			echo "createTextAreaWithLines('content');\n";
			echo "</script>\n";
			
			echo "<div style=\"overflow:hidden; position:absolute; bottom:3%;  z-index: 301; \">";			
      echo "<button class=\"nav_main\" onMouseover=\"this.className='nav_mainon';\" onMouseout=\"this.className='nav_main';\" onclick=\"camsmile();\">SAVE AND EXIT</button>\n";       
      echo "<button class=\"nav_main\" onMouseover=\"this.className='nav_mainon';\" onMouseout=\"this.className='nav_main';\"  onclick=\"camsmiler();\">SAVE AND RELOAD</button>\n";  
      echo "<button class=\"nav_logout\" onMouseover=\"this.className='nav_logouton';\" onMouseout=\"this.className='nav_logout';\" onclick=\"camcancel();\">Cancel</button>\n";  
			echo "</div>\n";

} elseif($_SESSION['mysql_query'] == 'checked') {
	$post_mysql = mysql_query($_POST['cmd']);
	$arrayTable = "<hr>\n";
	echo "<br><br><b><font color=\"white\">Mysql Mode</font></b><br>\n";
	if(eregi('^select ', $_POST['cmd']) || eregi('^show ', $_POST['cmd'])){
		
		$tablename = explode('from ', $_POST['cmd']);
		$table=explode(' ', $tablename['1']);
		$ttable = $table['0'];
		$arrayTable = "<hr>\n";
   	$arrayTable .= "<b><font color=\"white\">$ttable</font></b><br>\n";
		$arrayTable .= "<table class=\"content\" border=\"1\" cellspacing=\"0\" cellpadding=\"5\" style=\"font: 10px verdana; border: 1px solid #000;\">\n";
		while($msql_result = mysql_fetch_array($post_mysql)){
			$showkey = 'key';
			if($hidekey == 'yes'){ $showkey = ''; }
			$arrayTable .=  mysqlPrint($msql_result, $showkey);
			$hidekey = 'yes';
		}
		echo $arrayTable .= "</table>\n";
	}
	echo mysql_error();
} else {  
	echo "<font color=white>";
	if($_SESSION['exectype'] != 'exec') {

		if($_SESSION['win']=='yes' && strtoupper($SPLIT['0']) == 'FIND'){
			$win_explode = explode('"', $find_orig);
			$searchterm = $win_explode['1'];
			unset($_SESSION['found_files']);
			$_SESSION['found_files'] = array();
			function win_search($path, $searchterm){
			  $thisdirz = getcwd();
			 // echo $path;	
			//	foreach (glob($path.'/*') as $filez) {
				foreach (array_merge(glob($path.DIRECTORY_SEPARATOR.'*'),glob($path.DIRECTORY_SEPARATOR.'.*')) as $filez) {	
					$stripped = str_replace($thisdirz.'\\', '', $filez);		
					$stripped = eregi_replace('^\.', '', $stripped);		

					if(!is_dir($stripped)) {			
						if(!eregi('\.gif$', $stripped) && $stripped != '.' && $stripped != '..' && !eregi('\.gz$', $stripped) && !eregi('\.tar$', $stripped) && !eregi('\.zip$', $stripped) && !eregi('\.tgz$', $stripped) && !eregi('\.png$', $stripped) && !eregi('\.rm$', $stripped) && !eregi('\.avi$', $stripped) && !eregi('\.mpg$', $stripped) && !eregi('\.mpeg$', $stripped) && !eregi('\.mov$', $stripped) && !eregi('\.jpg$', $stripped) && !eregi('\.tif$', $stripped) && !eregi('\.sql$', $stripped) && !eregi('\.jpeg$', $stripped) && !eregi('\.css$', $stripped) && !eregi('\.psd$', $stripped) && !eregi('\.bmp$', $stripped) && !eregi('\.ttf$', $stripped) && !eregi('\.swf$', $stripped) && !eregi('\.flv$', $stripped) && !eregi('\.doc$', $stripped) && !eregi('\.csv$', $stripped) && !eregi('\.pdf$', $stripped)) {
							$phppage = $stripped;				
							$filesizephp = filesize($phppage);

							if($filesizephp < '300000') {				
								$fileophp = fopen($phppage, "r");
								$phpcontent = fread($fileophp, $filesizephp);
								fclose($fileophp);					


								if(eregi($searchterm, $phpcontent)){
									$_SESSION['found_files'][] = getcwd().'\\'.$phppage;
								}
																
							} 
						}			
					} else {					
						if(!eregi('\\\.$', $stripped) && !eregi('\\\.\.$', $stripped)){
							//echo $stripped."<br/>";
							win_search($filez, $searchterm);
						}
					}
				}
			}
			
			win_search(getcwd(), $searchterm);
			$output = 'Found '.$searchterm.' '.count($_SESSION['found_files'])." times.\n";
			$xo = 1;
			foreach($_SESSION['found_files'] as $val){
				//$output .= 'Found '.$searchterm.' in '.$val."\n";
				$val = str_replace('\\', '\\\\', $val);
				$output .= $xo.") <span class=\"filesearch\" style=\" cursor:pointer;\" onclick=\"pncmd2('edit ".$val."'); this.style.color='red;'\">".$val."</span>\n";
				++$xo;
			}
			
		} else {
			
			$output = shell_exec($_POST['cmd']);
			if(strtoupper($SPLIT['0']) == 'FIND'){
			  $_POST['cmd'] = $find_orig;
			  $findout_ar = explode("\n", $output);
			  $findout = "Found ".(count($findout_ar) - 1)." matching files.\n";
			  $ollyc = 0;
			  foreach($findout_ar as $var=>$val){
			    if($val != ""){               
			      ++$ollyc;
			      $findout .= $ollyc.") <span class=\"filesearch\" style=\" cursor:pointer;\" onclick=\"pncmd2('edit ".$val."'); this.style.color='red;'\">".$val."</span>\n";
			      //$findout .= $ollyc.") <span style=\"cursor:pointer; color:orange;\" onmouseover=\"this.style.color='red;'\" onMouseOut=\"this.style.color='orange;'\" onclick=\"pncmd2('edit ".$val."'); this.style.color='red;'\">".$val."</span>\n";
			    }
			  }
			  $findout .= "\n";
			  $output = $findout;
			}
		}

      
      if($output == '') {
         //$output = shell_exec('ls -Al');


					echo "<script type=\"text/javascript\">\n";
					echo "window.onload=function(){\n";
					echo "if(!NiftyCheck())\n";
					echo "    return;\n";
					echo "	Rounded(\"div#filezlist\",\"#808080\",\"#000000\");\n";
					echo "	document.exec.cmd.focus();\n";
					echo "}\n";
					echo "</script>\n";

         echo "<div id=\"filezlist\" style=\"background-color:black; width:98%; height:88%;\">\n";
         echo "<div id=\"filezlistcon\" style=\"background: #000000 url('http://securexfer.net/camerons_simple/Mitch-simple.jpg') no-repeat fixed bottom right; width:99%; height:98%; overflow:auto; padding:3px 0px 3px 3px;\">\n";
         echo sortls();        
         echo "</div></div>";
      } else {
         if($_POST['cmd']=='ls -Al') {
            //echo $output;
            //echo sortls();   
         } else {
            //echo $output;
         }
      }
   } else {
			$output = exec($_POST['cmd']); 
      if($output == '') {
         echo sortls();  
      }
   }
  

  $ootp = $_POST['cmd']."\n";
	if($output != ''){
		$ootp .= $output;
	}
	$ootp = explode("\n", $ootp);

	$oh_c = explode("\n", $_SESSION['output_history']);
	$oh_cx = 0;

	$oh_c = array_merge($oh_c,$ootp);
	krsort($oh_c);
	$oooo = '';
	foreach($oh_c as $val){
		if($oh_cx < 900){
			$oooo[] = $val;
		}
		$oh_cx++;
	}
	krsort($oooo);
	$_SESSION['output_history'] = implode("\n", $oooo);
	$_SESSION['output_history'] = eregi_replace("^(\n)+", '', $_SESSION['output_history']);
	$_SESSION['output_history'] = eregi_replace("\n$", '', $_SESSION['output_history']);
	$_SESSION['output_history'] = eregi_replace("\n\n\n", "\n\n", $_SESSION['output_history']);
if($_SESSION['output_history'] != '' && $output != ''){
	
	echo "<script type=\"text/javascript\">\n";
	echo "window.onload=function(){\n";
	echo "if(!NiftyCheck())\n";
	echo "    return;\n";
	echo "	Rounded(\"div#scrolly1\",\"#808080\",\"#000000\");\n";
	echo "	document.exec.cmd.focus();\n";
	echo "}\n";
	echo "</script>\n";
	echo "<div id=\"scrolly1\" style=\"background-color:black; width:98%; height:88%;\">\n";
	echo "<div id=\"scrolly\" style=\"font-size: 8pt; background: #000000 url('http://securexfer.net/camerons_simple/Mitch-simple.jpg') no-repeat fixed bottom right; width:99%; height:98%; overflow:auto; padding:3px 0px 3px 3px;\">\n";
	echo "<pre>";
	echo $_SESSION['output_history'];
	echo "</pre>\n";
	echo "</div>\n";

	echo "</div>\n";
	echo "<div style=\"overflow:hidden; padding: 4px 0px 0px 0px;\">\n";
	echo "<button class=\"nav_logout\" onMouseover=\"this.className='nav_logouton';\" onMouseout=\"this.className='nav_logout';\" style=\"font-size: 9px; border:0px solid; color:white;\" onClick=\"pncmd('CLEAR_HISTORY')\";>CLEAR HISTORY</button>\n";
	echo "</div>\n";
	echo "<script language=javascript>\n";
	echo "var mydiv = document.getElementById(\"scrolly\");\n";
	echo "mydiv.scrollTop = mydiv.scrollHeight - mydiv.clientHeight;\n";
	echo "</script>\n";
	echo "</font>";

}	else  {
	echo "</font>";
}


}

echo "</font> \n";
echo "</div>\n";
echo "</div>\n";
echo "</body> \n";
echo "</html> \n";
?>