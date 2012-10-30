<?php
error_reporting(E_PARSE);
if($_GET['_SESSION'] != '' || $_POST['_SESSION'] != '' || $_COOKIE['_SESSION'] != '') { exit; }

session_start();
error_reporting(E_PARSE);

// Testing passed url vars
//foreach($_GET as $var=>$val){
//   echo "var = (".$var.") val = (".$val.")<br>";
//}

$template_name = $_GET['template_name'];
$template_name = eregi_replace(" ", "_", $template_name);

// Get style BG img name
if($tmp = eregi("/BG/(.*).jpg", $_GET['style'], $out)){
	$bgName = $out[1];
}

// Get parts of img name (imgname_color-style)
if($tmp = eregi("/main/(.*).jpg", $_GET['img'], $out)){
	$imgName = $out[1];
	$imgPath = split("_", $imgName);
	$imgType = $imgPath[0];
	$imgColor_BG = split("-", $imgPath[1]);
	$imgColor = $imgColor_BG[0];
	$imgBG = $imgColor_BG[1];
}

// Testing for parts of img name
//echo "(".$imgName.")";
//echo "(".$imgType.")";
//echo "(".$imgColor.")";
//echo "(".$imgBG.")";

// Get base css file
$filename = "styles/full_base_css.css";

ob_start(); 
	include("$filename");
	$CSS_CONTENT = ob_get_contents(); 
ob_end_clean(); 

// Get base html file
$filename = "styles/full_base.html";

ob_start(); 
	include("$filename");
	$HTML_CONTENT = ob_get_contents(); 
ob_end_clean(); 

// Start replacing values with selected values

$HTML_CONTENT = eregi_replace("#MAINIMG#", $imgName, $HTML_CONTENT);
$HTML_CONTENT = eregi_replace("#BGIMG#", $bgName, $HTML_CONTENT);

if($_GET['menu_main'] == "side"){
	// Kill un-needed menu pound var
	$HTML_CONTENT = eregi_replace("#MENUTOP#", "", $HTML_CONTENT);
	
	// Add div for Side Menu
	$HTML_CONTENT = eregi_replace("#MENUSIDE#", "\n<td valign=\"top\" id=\"side_menu_contain\">\n<div id=\"side_menu\">\n#VMENU#\n</div>\n</td>\n<td id=\"content\" width=\"100%\">\n#CONTENT#\n</td>\n", $HTML_CONTENT);
	
	// Set display for side menu
	$CSS_CONTENT = eregi_replace("#MENU-SIDE-DISPLAY#", "block", $CSS_CONTENT);
	$CSS_CONTENT = eregi_replace("#MENU-SIDESUB-DISPLAY#", "none", $CSS_CONTENT);
}

if($_GET['menu_main'] == "top"){
	// Add row for top menu
	$HTML_CONTENT = eregi_replace("#MENUTOP#", "<tr>\n<td width=\"100%\" valign=\"top\" colspan=\"2\" class=\"navigation\">\n#HMAINS#\n</td>\n</tr>\n<tr>\n<td class=\"navigation_sub\" width=\"100%\" valign=\"top\" colspan=\"2\">\n#HSUBS#\n</td>\n</tr>\n", $HTML_CONTENT);
	$HTML_CONTENT = eregi_replace("#MENUSIDE#", "<td id=\"content\" width=\"100%\" height=\"100\" colspan=\"2\">\n#CONTENT#\n</td>\n", $HTML_CONTENT);
	
	// Set display for side menu
	$CSS_CONTENT = eregi_replace("#MENU-SIDE-DISPLAY#", "none", $CSS_CONTENT);
	$CSS_CONTENT = eregi_replace("#MENU-SIDESUB-DISPLAY#", "none", $CSS_CONTENT);
}

if($_GET['menu_main'] == "both"){
	// Add row for mains and col for subs (top and side)
	$HTML_CONTENT = eregi_replace("#MENUTOP#", "<tr>\n<td width=\"100%\" valign=\"top\" colspan=\"2\" class=\"navigation\">#HMAINS#</td>\n</tr>", $HTML_CONTENT);
	$HTML_CONTENT = eregi_replace("#MENUSIDE#", "<td valign=\"top\" id=\"side_menu_light\">\n<div id=\"side_menu_display\">\n#VSUBS#\n</div>\n</td>\n<td id=\"content\" width=\"100%\">\n#CONTENT#\n</td>\n", $HTML_CONTENT);
	
	//<td id="content" width="100%" height="100" colspan="2">
	// Set display for side menu
	$CSS_CONTENT = eregi_replace("#MENU-SIDE-DISPLAY#", "none", $CSS_CONTENT);
	$CSS_CONTENT = eregi_replace("#MENU-SIDESUB-DISPLAY#", "block", $CSS_CONTENT);
}

//echo "<br/><br/>\n\n<textarea style=\"width: 600px; height: 500px;\">".$HTML_CONTENT."</textarea><br/><br/>\n\n\n";
if($_GET['menu_footer'] == "off"){
	$HTML_CONTENT = eregi_replace("#TMENU#", "", $HTML_CONTENT);
}
//echo "<textarea style=\"width: 600px; height: 500px;\">".$HTML_CONTENT."</textarea>\n";


$customStyles = array($_GET['custom_1'], $_GET['custom_2'], $_GET['custom_3'], $_GET['custom_4'], $_GET['custom_5']);

$greenStyles = array("green", "#004d24", "1px solid #5acc8f", "#1d603d", "#5acc8f", "#1d603d");
$blueStyles = array("blue", "#325884", "1px solid #D1E4FA", "#2D5179", "#D1E4FA", "#2D5179");
$redStyles = array("red", "#b9151c", "1px solid #e59da0", "#9e3f43", "#e59da0", "#9e3f43");
$greyStyles = array("grey", "#333333", "1px solid #CCCCCC", "#333333", "#CCCCCC", "#666666");

$allStyles = array($greenStyles, $blueStyles, $redStyles, $greyStyles);

$greenFont = array("green", "#FFFFFF", "#CCCCCC");
$blueFont = array("blue", "#FFFFFF", "#FFFFFF");
$redFont = array("red", "#FFFFFF", "#FFFF00");
$greyFont = array("red", "#FFFFFF", "#FFFFFF");

$colorStyles = array($greenFont, $blueFont, $redFont, $greyFont);

//Define header font position and size
//areaEffected = Array("style name", "title size", "slogan size", "title padding top", "title padding left", "slogan padding top", "slogan padding left")

$stripeFont = array("stripe", "21px", "15px", "50px", "35px", "10px", "35px");
$gradFont = array("grad", "21px", "15px", "10px", "0px", "20px", "0px");
$roundFont = array("round", "21px", "15px", "10px", "110px", "20px", "120px");
$sunFont = array("sun", "21px", "15px", "10px", "0px", "20px", "0px");
$ringsFont = array("rings", "21px", "15px", "10px", "0px", "20px", "0px");

$headerStyles = array($stripeFont, $gradFont, $roundFont, $sunFont, $ringsFont);

$allLength = count($allStyles);
//echo "<br/><br/><br/>";
for ($x=0;$x<=$allLength;$x++) {
	//echo "(".$allStyles[$x][0].")<br/>";
	
	// TEMPLATE COLOR / TITLE AND SLOGAN COLOR
	if($allStyles[$x][0] == $imgColor){
		$CSS_CONTENT = eregi_replace("STYLE1#", $customStyles[0], $CSS_CONTENT);
		$CSS_CONTENT = eregi_replace("STYLE2#", $customStyles[1], $CSS_CONTENT);
		$CSS_CONTENT = eregi_replace("STYLE3#", $customStyles[2], $CSS_CONTENT);
		$CSS_CONTENT = eregi_replace("STYLE4#", $customStyles[3], $CSS_CONTENT);
		$CSS_CONTENT = eregi_replace("STYLE5#", $customStyles[4], $CSS_CONTENT);

		$CSS_CONTENT = eregi_replace("MENU-SIDE-COLOR#", $customStyles[2], $CSS_CONTENT);
		$CSS_CONTENT = eregi_replace("MENU-SIDESUB-COLOR#", $customStyles[3], $CSS_CONTENT);
		
		$CSS_CONTENT = eregi_replace("#STYLE-TITLE#", $colorStyles[$x][1], $CSS_CONTENT);
		$CSS_CONTENT = eregi_replace("#STYLE-SLOGAN#", $colorStyles[$x][2], $CSS_CONTENT);
	}
	
	// HEAD TITLE AND SLOGAN SIZE AND POSITION
	if($headerStyles[$x][0] == $imgBG){
		$CSS_CONTENT = eregi_replace("#TITLE-SIZE#", $headerStyles[$x][1], $CSS_CONTENT);
		$CSS_CONTENT = eregi_replace("#SLOGAN-SIZE#", $headerStyles[$x][2], $CSS_CONTENT);
		$CSS_CONTENT = eregi_replace("#TITLE-PADDING-TOP#", $headerStyles[$x][3], $CSS_CONTENT);
		$CSS_CONTENT = eregi_replace("#TITLE-PADDING-LEFT#", $headerStyles[$x][4], $CSS_CONTENT);
		$CSS_CONTENT = eregi_replace("#SLOGAN-PADDING-TOP#", $headerStyles[$x][5], $CSS_CONTENT);
		$CSS_CONTENT = eregi_replace("#SLOGAN-PADDING-LEFT#", $headerStyles[$x][6], $CSS_CONTENT);
	}
}

// End replacing values with selected values


// Create folder for template in pages dir
if(!mkdir("../site_templates/pages/".$template_name, 0755)){
	echo "cant create folder!<br/>";
}

// Write index.html file ($HTML_CONTENT)
$nfilename = "../site_templates/pages/".$template_name."/index.html";

if(!$nfile = fopen("$nfilename", "w")){
	echo "cant open!<br/>";
}
	$template_html = "<!-- This file created with the Soholaunch Template Creator -->\n\n\n\n\n\n".$HTML_CONTENT;
	if(!fwrite($nfile, "$template_html\n")){
		echo "cant write!<br/>";
	}
fclose($nfile);

// Write index.html file ($CSS_CONTENT)
$nfilename = "../site_templates/pages/".$template_name."/custom.css";

if(!$nfile = fopen("$nfilename", "w")){
	echo "cant open!<br/>";
}
	$template_html = "\n".$CSS_CONTENT;
	if(!fwrite($nfile, "$template_html\n")){
		echo "cant write!<br/>";
	}
fclose($nfile);

// Copy images needed to template folder
$file = "images/head/main/".$imgName.".jpg";
$newfile = "../site_templates/pages/".$template_name."/".$imgName.".jpg";

if (!copy($file, $newfile)) {
   echo "failed to copy $file...\n";
}

$file = "images/head/BG/".$bgName.".jpg";
$newfile = "../site_templates/pages/".$template_name."/".$bgName.".jpg";

if (!copy($file, $newfile)) {
   echo "failed to copy $file...\n";
}

$file = "../../../client_files/base_files/pgm-auto_menu.php";
$newfile = "../site_templates/pages/".$template_name."/pgm-auto_menu.php";

if (!copy($file, $newfile)) {
   echo "failed to copy $file...\n";
}

$file = "images/screenshot.jpg";
$newfile = "../site_templates/pages/".$template_name."/screenshot.jpg";

if (!copy($file, $newfile)) {
   echo "failed to copy $file...\n";
}

header("location:template_builder.php?done=1");
exit;
//echo "<br/><br/>".$HTML_CONTENT;

?>