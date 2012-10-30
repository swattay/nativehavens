<?php
error_reporting(E_PARSE);
if($_GET['_SESSION'] != '' || $_POST['_SESSION'] != '' || $_COOKIE['_SESSION'] != '') { exit; }

###############################################################################
## Soholaunch(R) Site Management Tool
## Version 4.9
##      
## Author: 			Joe Lain [joe.lain@soholaunch.com]                 
## Homepage:	 	http://www.soholaunch.com
## Bug Reports: 	http://bugz.soholaunch.com
###############################################################################

##############################################################################
## COPYRIGHT NOTICE                                                     
## Copyright 1999-2006 Soholaunch.com, Inc.  All Rights Reserved.       
##                                                                        
## This script may be used and modified in accordance to the license      
## agreement attached (license.txt) except where expressly noted within      
## commented areas of the code body. This copyright notice and the comments  
## comments above and below must remain intact at all times.  By using this 
## code you agree to indemnify Soholaunch.com, Inc, its coporate agents   
## and affiliates from any liability that might arise from its use.                                                        
##                                                                           
## Selling the code for this program without prior written consent is       
## expressly forbidden and in violation of Domestic and International 
## copyright laws.  		                                           
###############################################################################

session_start();
include("../../includes/product_gui.php");


##########################################################################
### Download Images
##########################################################################

$remotefile = "http://update.securexfer.net/template_builder_images/images.zip";
$saveto = "images.zip";

if(!is_dir("images")){
	if(!$dlUpdate = new file_download($remotefile, $saveto)){
		//echo "Cant get file!";
	}else{
		//echo "Got file!";
		shell_exec("unzip images.zip");
	}
}

##########################################################################
### Pull Site Title and Slogan from site specs
##########################################################################

if ( !$result = mysql_query("SELECT df_hdrtxt, df_slogan FROM site_specs") ){
   echo "Cannot select from faq table<br>";
	echo "Mysql says: ".mysql_error();
	exit;
}
$siteSpecs = mysql_fetch_array($result);
$df_hdrtxt = $siteSpecs['df_hdrtxt'];
$df_slogan = $siteSpecs['df_slogan'];

if(strlen($df_hdrtxt) < 2){ $df_hdrtxt = "Site Title"; }
if(strlen($df_slogan) < 2){ $df_slogan = "Site Slogan"; }

##########################################################################
### INSERT FUNCTION TO KILL ALL NON ALPHA/NUMERIC CHARACTERS FROM DATA
### FOR DATABASE STORAGE
##########################################################################

function sterilize_char ($sterile_var) {

	$sterile_var = stripslashes($sterile_var);
	$sterile_var = eregi_replace(" ", "_", $sterile_var);
	$sterile_var = eregi_replace("#", "", $sterile_var);

	$st_l = strlen($sterile_var);
	$st_a = 0;
	$tmp = "";

	while($st_a != $st_l) {
		$temp = substr($sterile_var, $st_a, 1);
		if (eregi("[0-9a-z_]", $temp)) { $tmp .= $temp; }
		$st_a++;
	}

	$sterile_var = $tmp;
	return $sterile_var;

}

##########################################################################


#######################################################
### GET COLOR TABLE						 		    ###	
#######################################################


$filename = "color_picker/demo.htm";
ob_start();
	include($filename);
	$picker = ob_get_contents();
ob_end_clean();

$picker_line = split("\n", $picker);
$numPlines = count($picker_line);



##################################################################################
### READ IMAGE FILES INTO MEMORY			     
##################################################################################

//$img_selection = "     <OPTION VALUE=\"NOIMAGE\">[No Image]</OPTION>\n";

// Uploaded Images
// -------------------------------------------------------------------------------

$count = 0;
$directory = "$doc_root/sohoadmin/program/modules/template_builder/images/head/preview/images";
$handle = opendir("$directory");
	while ($files = readdir($handle)) {
		if (strlen($files) > 2) {
			$count++;
			$imageFile[$count] = ucwords($files) . "~~~" . $files;
		}
	}
$numImages = $count;
closedir($handle);

$countBG = 0;
$directory = "$doc_root/sohoadmin/program/modules/template_builder/images/head/preview/styles";
$handle = opendir("$directory");
	while ($files = readdir($handle)) {
		if (strlen($files) > 2) {
			$countBG++;
			$imageFileBG[$countBG] = ucwords($files) . "~~~" . $files;
			//echo "(".$files.")<br/>";
		}
	}
$numBG = $countBG;
closedir($handle);

$countLayout = 0;
$directory = "$doc_root/sohoadmin/program/modules/template_builder/images/head/layout";
$handle = opendir("$directory");
	while ($files = readdir($handle)) {
		if (strlen($files) > 2) {
			$countLayout++;
			$imageFileLayout[$countLayout] = ucwords($files) . "~~~" . $files;
		}
	}
$numLayout = $countLayout;
closedir($handle);

if ($count != 0) {
	sort($imageFile);
	$numImages--;	
}
if ($countBG != 0) {
	sort($imageFileBG);
	$numBG--;	
}
if ($countLayout != 0) {
	sort($imageFileLayout);
	if ($countLayout == 1) {
		$imageFileLayout[0] = $imageFileLayout[1];
	}
	$numLayout--;	
}


for ($x=0;$x<=$numImages;$x++) {
	$thisImage = split("~~~", $imageFile[$x]);
	$img_selection .= "     <img id=\"".$thisImage[1]."\" src=\"images/head/preview/images/".$thisImage[1]."\" onClick=\"changeMain(this.id);this.className='ind_img_on';\" class=\"ind_img\" onMouseOver=\"this.className='ind_img_on'\" onMouseOut=\"this.className='ind_img'\" width=\"40\" height=\"30\">\n";
}

for ($x=0;$x<=$numBG;$x++) {

	$thisImageBG = split("~~~", $imageFileBG[$x]);
	$img_selectionBG .= "     <img id=\"".$thisImageBG[1]."\" src=\"images/head/preview/styles/".$thisImageBG[1]."\" onClick=\"changeMainBG(this.id)\" class=\"ind_img\" onMouseOver=\"this.className='ind_img_on'\" onMouseOut=\"this.className='ind_img'\" width=\"40\" height=\"30\">\n";
}

for ($x=0;$x<=$numLayout;$x++) {

	$thisImageLayout = split("~~~", $imageFileLayout[$x]);
	$img_selectionLayout .= "     <img id=\"prevImg".$x."\" src=\"images/head/layout/".$thisImageLayout[1]."\" onClick=\"changeScheme(this.src)\" class=\"ind_img\" onMouseOver=\"this.className='ind_img_on'\" onMouseOut=\"this.className='ind_img'\" width=\"40\" height=\"30\">\n";
}

#######################################################
### START HTML/JAVASCRIPT CODE			 		    ###	
#######################################################

?>

<HTML>
<HEAD>
<TITLE>Template Builder</TITLE>
<META HTTP-EQUIV="Content-Type" content="text/html; charset=iso-8859-1">

<link rel="stylesheet" href="../../product_gui.css">
<link rel="stylesheet" href="template_builder.css">
<link rel="stylesheet" type="text/css" href="../promo_boxes/box_manager.css">
<link href="../../includes/display_elements/window/default.css" rel="stylesheet" type="text/css"></link>
<link href="../../includes/display_elements/window/alert_lite.css" rel="stylesheet" type="text/css"></link>
<link rel="stylesheet" type="text/css" href="color_picker/css/screen.css">

<script language="JavaScript" src="../../includes/display_elements/js_functions.php"></script>
<script type="text/javascript" src="../../includes/display_elements/window/prototype.js"></script>
<script type="text/javascript" src="includes/selector-addon-v1.js"></script>
<script type="text/javascript" src="../../includes/display_elements/window/window.js"></script>
<script type="text/javascript" src="../../includes/display_elements/window/effects.js"></script>
<script type="text/javascript" src="dropdown.js"></script>


<script type="text/javascript" src="color_picker/js/ddcolorposter.js"></script>
<script type="text/javascript" src="color_picker/js/YAHOO.js" ></script>
<script type="text/javascript2" src="color_picker/js/log.js" ></script>
<script type="text/javascript" src="color_picker/js/color.js" ></script>

<script type="text/javascript" src="color_picker/js/event.js" ></script>
<script type="text/javascript" src="color_picker/js/dom.js" ></script>
<script type="text/javascript" src="color_picker/js/animation.js" ></script>
<script type="text/javascript" src="color_picker/js/dragdrop.js" ></script>
<script type="text/javascript" src="color_picker/js/slider.js" ></script>
<script type="text/javascript" src="template_builder.js" ></script>

<script language="javascript">

function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

// Resize window
if(parent.window.name != 'admin_dialog_content'){
   //alert('not dhtml win')
   parent.window.resizeTo(1024,768);
}else{
   //alert('this is dhtml')
   parent.windowOptions(1024, 768)
}



//parent.window.resizeTo(1024,768);

// Define color sets
//var classNames = new Array("color scheme", "BGDark", "secondarynav", "secondarynav", "secondarynav_sub", "footerNav")

var greenStyles = new Array("green", "#004d24", "0px solid #FFFFFF", "#1d603d", "#5acc8f", "#1d603d")
var blueStyles = new Array("blue", "#325884", "0px solid #FFFFFF", "#638CBB", "#D1E4FA", "#2D5179")
var redStyles = new Array("red", "#b9151c", "0px solid #FFFFFF", "#9e3f43", "#e59da0", "#9e3f43")
var greyStyles = new Array("grey", "#333333", "0px solid #FFFFFF", "#333333", "#CCCCCC", "#666666")

//Place all color sets into array for later use
var allStyles = new Array(greenStyles, blueStyles, redStyles, greyStyles)


//Define header font styles
//var fontStyles = new Array("color scheme", "title color", "slogan color")

var greenFont = new Array("green", "#FFFFFF", "#CCCCCC")
var blueFont = new Array("blue", "#FFFFFF", "#FFFFFF")
var redFont = new Array("red", "#FFFFFF", "#FFFF00")
var greyFont = new Array("grey", "#FFFFFF", "#FFFFFF")
var ringsMod = new Array("rings", "#000000", "#666666")

//Place all header sets into array for later use
var colorStyles = new Array(greenFont, blueFont, redFont, greyFont, ringsMod)


//Define header font position and size
//var areaEffected = new Array("style name", "title size", "slogan size", "title padding top", "title padding left", "slogan padding top", "slogan padding left")

var stripeFont = new Array("stripe", "21px", "15px", "50px", "35px", "10px", "35px")
var gradFont = new Array("grad", "21px", "15px", "10px", "0px", "20px", "0px")
var roundFont = new Array("round", "21px", "15px", "10px", "110px", "20px", "120px")
var sunFont = new Array("sun", "21px", "15px", "10px", "0px", "20px", "0px")
var ringsFont = new Array("rings", "21px", "15px", "10px", "0px", "20px", "0px")

//Place all header sets into array for later use
var headerStyles = new Array(stripeFont, gradFont, roundFont, sunFont, ringsFont)


var menuStyles = new Array("top_menu", "top_sub_menu", "side_menu")

var customStyles = new Array("custom")

this_ip = "<? echo $this_ip; ?>";



function changeMain(new_img){		//stripes_prev.jpg
	openWorkingDialog()
	var imgSplit = new_img.split('_')
	var imgType = imgSplit[0]
	
	var hidImg = $('imgSel').value
	
	// Remove domain name
	// Had issue with colors in domain name (redsite.com)
	var hidLen = this_ip.length + 7
	hidImg = hidImg.substr(hidLen)
	//alert(hidImg)
	
	var availColors = new Array("red", "green", "blue", "grey")
	
	for (i=0; i<availColors.length; i++){
		var is_flow = hidImg.search(availColors[i])
		if(is_flow > 0){
			var imgColor = availColors[i]
			var imgSplit = hidImg.split('_'+imgColor+'-')
			var imgNameIndex = imgSplit[0].lastIndexOf('/') + 1
			var imgName = imgSplit[0].substr(imgNameIndex)
			var bgSplit = imgSplit[1].split('.')
			var bgName = bgSplit[0]
		}
	}
	
	$('main_img').src = 'http://<? echo $this_ip; ?>/sohoadmin/program/modules/template_builder/images/head/main/'+imgType+'_'+imgColor+'-'+bgName+'.jpg'
	$('imgSel').value = 'http://<? echo $this_ip; ?>/sohoadmin/program/modules/template_builder/images/head/main/'+imgType+'_'+imgColor+'-'+bgName+'.jpg'
	$('styleSel').value = 'http://<? echo $this_ip; ?>/sohoadmin/program/modules/template_builder/images/head/BG/'+bgName+'-'+imgColor+'.jpg'
	$('schemeSel').value = imgColor
	Dialog.closeInfo()
}

function changeMainBG(new_BG){
	openWorkingDialog()
	//alert(new_BG)
	
	var bgSplit = new_BG.split('_')
	var bgType = bgSplit[0]
	//alert(bgType)
	
	var hidImg = $('imgSel').value
	
	// Remove domain name
	// Had issue with colors in domain name (redsite.com)
	var hidLen = this_ip.length + 7
	hidImg = hidImg.substr(hidLen)
	//alert(hidImg)
	
	var availColors = new Array("red", "green", "blue", "grey")
	
	for (i=0; i<availColors.length; i++){
		var is_flow = hidImg.search(availColors[i])
		if(is_flow > 0){
			var imgColor = availColors[i]
			var imgSplit = hidImg.split('_'+imgColor+'-')
			var imgNameIndex = imgSplit[0].lastIndexOf('/') + 1
			var imgName = imgSplit[0].substr(imgNameIndex)
			var bgSplit = imgSplit[1].split('.')
			var bgName = bgSplit[0]
			
			for (j=0; j<headerStyles.length; j++){
				var is_flow = headerStyles[j][0].search(bgType)
				if(is_flow >= 0){
					if(nn6){
//						if(bgType == 'rings'){
//							document.styleSheets[6].cssRules[4].style.color=colorStyles[4][1]
//							document.styleSheets[6].cssRules[5].style.color=colorStyles[4][2]
//						}else{
//							document.styleSheets[6].cssRules[4].style.color=colorStyles[j][1]
//							document.styleSheets[6].cssRules[5].style.color=colorStyles[j][2]
//						}
						document.styleSheets[6].cssRules[4].style.fontSize=headerStyles[j][1]
						document.styleSheets[6].cssRules[5].style.fontSize=headerStyles[j][2]
						document.styleSheets[6].cssRules[4].style.paddingTop=headerStyles[j][3]
						document.styleSheets[6].cssRules[4].style.paddingLeft=headerStyles[j][4]
						document.styleSheets[6].cssRules[5].style.paddingTop=headerStyles[j][5]
						document.styleSheets[6].cssRules[5].style.paddingLeft=headerStyles[j][6]
					}else{
						document.styleSheets[6].rules[4].style.fontSize=headerStyles[j][1]
						document.styleSheets[6].rules[5].style.fontSize=headerStyles[j][2]
						document.styleSheets[6].rules[4].style.paddingTop=headerStyles[j][3]
						document.styleSheets[6].rules[4].style.paddingLeft=headerStyles[j][4]
						document.styleSheets[6].rules[5].style.paddingTop=headerStyles[j][5]
						document.styleSheets[6].rules[5].style.paddingLeft=headerStyles[j][6]
					}
				}else{
					//alert('NO-'+headerStyles[j][0])
				}
			}
			
		}
	}
	//none_blue-grad.jpg
	$('main_img').src = 'http://<? echo $this_ip; ?>/sohoadmin/program/modules/template_builder/images/head/main/'+imgName+'_'+imgColor+'-'+bgType+'.jpg'
	$('main_imgBG').style.background = 'url(http://<? echo $this_ip; ?>/sohoadmin/program/modules/template_builder/images/head/BG/'+bgType+'-'+imgColor+'.jpg) no-repeat'
	$('imgSel').value = 'http://<? echo $this_ip; ?>/sohoadmin/program/modules/template_builder/images/head/main/'+imgName+'_'+imgColor+'-'+bgType+'.jpg'
	$('styleSel').value = 'http://<? echo $this_ip; ?>/sohoadmin/program/modules/template_builder/images/head/BG/'+bgType+'-'+imgColor+'.jpg'
	$('schemeSel').value = imgColor
	
	Dialog.closeInfo()
}

function changeScheme(new_layout){
	openWorkingDialog()
	//setTimeout(Dialog.closeInfo(), 5000);
	var hidImg = $('imgSel').value
	
	// Remove domain name
	// Had issue with colors in domain name (redsite.com)
	var hidLen = this_ip.length + 7
	hidImg = hidImg.substr(hidLen)
	//alert(hidImg)
	
	var availColors = new Array("red", "green", "blue", "grey")
	
	for (i=0; i<availColors.length; i++){
		var is_flow = hidImg.search(availColors[i])
		if(is_flow > 0){
			var imgColor = availColors[i]
			//alert(imgColor)
			var imgSplit = hidImg.split('_'+imgColor+'-')
			var imgNameIndex = imgSplit[0].lastIndexOf('/') + 1
			var imgName = imgSplit[0].substr(imgNameIndex)
			var bgSplit = imgSplit[1].split('.')
			var bgName = bgSplit[0]
		}
	}
	
	for (i=0; i<availColors.length; i++){
		var is_flow = new_layout.search(availColors[i])
		if(is_flow > 0){
			$('main_img').src = 'http://<? echo $this_ip; ?>/sohoadmin/program/modules/template_builder/images/head/main/'+imgName+'_'+availColors[i]+'-'+bgName+'.jpg'
			$('main_imgBG').style.background = 'url(http://<? echo $this_ip; ?>/sohoadmin/program/modules/template_builder/images/head/BG/'+bgName+'-'+availColors[i]+'.jpg) no-repeat'
			$('imgSel').value = 'http://<? echo $this_ip; ?>/sohoadmin/program/modules/template_builder/images/head/main/'+imgName+'_'+availColors[i]+'-'+bgName+'.jpg'
			$('styleSel').value = 'http://<? echo $this_ip; ?>/sohoadmin/program/modules/template_builder/images/head/BG/'+bgName+'-'+availColors[i]+'.jpg'
			$('schemeSel').value = availColors[i]
			
			// Clear custom colors
			var numAssigned = customStyles.length
			customStyles.splice(1, numAssigned -1)
			//alert(customStyles.length)
			
			for (j=0; j<allStyles.length; j++){
				var is_flow = allStyles[j][0].search(availColors[i])
				if(is_flow >= 0){
					//Set template styles
					if(nn6){
						document.styleSheets[6].cssRules[0].style.backgroundColor=allStyles[j][1]
						document.styleSheets[6].cssRules[1].style.borderBottom=allStyles[j][2]
						document.styleSheets[6].cssRules[1].style.background=allStyles[j][3]
						document.styleSheets[6].cssRules[2].style.background=allStyles[j][4]
						document.styleSheets[6].cssRules[3].style.background=allStyles[j][5]
						
						document.styleSheets[6].cssRules[6].style.background=allStyles[j][3]
						document.styleSheets[6].cssRules[7].style.background=allStyles[j][4]
						
						// Set font colors
						document.styleSheets[6].cssRules[4].style.color=colorStyles[j][1]
						document.styleSheets[6].cssRules[5].style.color=colorStyles[j][2]

					}else{
						document.styleSheets[6].rules[0].style.backgroundColor=allStyles[j][1]
						document.styleSheets[6].rules[1].style.borderBottom=allStyles[j][2]
						document.styleSheets[6].rules[1].style.background=allStyles[j][3]
						document.styleSheets[6].rules[2].style.background=allStyles[j][4]
						document.styleSheets[6].rules[3].style.background=allStyles[j][5]
						
						document.styleSheets[6].rules[6].style.background=allStyles[j][3]
						document.styleSheets[6].rules[7].style.background=allStyles[j][4]
						
						// Set font colors
						document.styleSheets[6].rules[4].style.color=colorStyles[j][1]
						document.styleSheets[6].rules[5].style.color=colorStyles[j][2]
						//$('title_slogan').value=allStyles[j]
						//$('color_set').value=colorStyles[j]
					}
					
					
				}else{
					//alert('NO-'+allStyles[j][0])
				}
			}
			
//			var is_flow_mod = hidImg.search('rings')
//			if(is_flow_mod >= 0){
//				document.styleSheets[6].cssRules[4].style.color=colorStyles[4][1]
//				document.styleSheets[6].cssRules[5].style.color=colorStyles[4][2]
//			}
		}
	}
	Dialog.closeInfo()
}

function changeFont(newFont){
	//alert(newFont)
	//alert(document.styleSheets[6].cssRules[6].cssText)
	document.styleSheets[6].cssRules[6].fontSize='32px'
	alert(document.styleSheets[6].cssRules[6].cssText)
}

function changeMenu(disMenu){
	if(disMenu == 'top'){
		$('top_menu').style.display='block'
		$('top_sub_menu').style.display='inline'
		$('side_menu').style.display='none'
		$('side_menu_light').style.display='none'
	}
	if(disMenu == 'side'){
		$('side_menu').style.display='block'
		$('top_sub_menu').style.display='none'
		$('top_menu').style.display='none'
		$('side_menu_light').style.display='none'
	}
	if(disMenu == 'both'){
		$('top_menu').style.display='block'
		$('side_menu_light').style.display='block'
		$('side_menu').style.display='none'
		$('top_sub_menu').style.display='none'
	}
	$('menu_main').value=disMenu
}
	

//---------------------------------------------------------------------------------------------------------
//      _      _   _   __  __
//     /_\  _ | | /_\  \ \/ /
//    / _ \| || |/ _ \  >  <
//   /_/ \_\\__//_/ \_\/_/\_\
//
//---------------------------------------------------------------------------------------------------------
// The following script (as commonly seen in other AJAX javascripts) is used to detect which browser the client is using.
// If the browser is Internet Explorer we make the object with ActiveX.
// (note that ActiveX must be enabled for it to work in IE)
function makeObject() {
   var x;
   var browser = navigator.appName;

   if ( browser == "Microsoft Internet Explorer" ) {
      x = new ActiveXObject("Microsoft.XMLHTTP");
   } else {
      x = new XMLHttpRequest();
   }

   return x;
}

// The javascript variable 'request' now holds our request object.
// Without this, there's no need to continue reading because it won't work ;)
var request = makeObject();

function ajaxDo(qryString, boxid) {
   //alert(qryString+', '+boxid);

   rezBox = boxid; // Make global so parseInfo can get it

   // The function open() is used to open a connection. Parameters are 'method' and 'url'. For this tutorial we use GET.
   request.open('get', qryString);

   // This tells the script to call parseInfo() when the ready state is changed
   request.onreadystatechange = parseInfo;

   // This sends whatever we need to send. Unless you're using POST as method, the parameter is to remain empty.
   request.send('');

}

function parseInfo() {
   // Loading
   if ( request.readyState == 1 ) {
      document.getElementById(rezBox).innerHTML = 'Loading...';
   }

   // Finished
   if ( request.readyState == 4 ) {
      var answer = request.responseText;
      document.getElementById(rezBox).innerHTML = answer;
   }
}


function openInfoDialog() {
	Dialog.info("Creating Template...", {windowParameters: {className: "alert_lite",width:250, height:50}, showProgress: true});
}

function openWorkingDialog() {
	Dialog.info("Making Changes...", {windowParameters: {className: "alert_lite",width:250, height:50}, showProgress: false});
}

function findPosX(obj)
{
	var curleft = 0;
	if (obj.offsetParent)
	{
		while (obj.offsetParent)
		{
			curleft += obj.offsetLeft
			obj = obj.offsetParent;
		}
	}
	else if (obj.x)
		curleft += obj.x;
	return curleft;
}

function findPosY(obj)
{
	var curtop = 0;
	if (obj.offsetParent)
	{
		while (obj.offsetParent)
		{
			curtop += obj.offsetTop
			obj = obj.offsetParent;
		}
	}
	else if (obj.y)
		curtop += obj.y;
	return curtop;
}

function show_image(what) {
	var daArea = $('curArea').value
	if (what != "NOIMAGE") {
		$(daArea).style.backgroundImage='url(http://<? echo $this_ip; ?>/images/'+what+')'
	} else {
		$(daArea).style.backgroundImage=''
	}
	
	
	//IMG_PREVIEW.innerHTML = value;
}

function resizeWin(){
	parent.window.resizeTo(1024,768);
}

// Resize window bak :)
function resizeWinBak(){
   if(parent.window.name != 'admin_dialog_content'){
      //alert('not dhtml win')
      parent.window.resizeTo(800,600);
   }else{
      //alert('this is dhtml')
      parent.windowOptions(800, 600)
   }
   //parent.windowOptions(800, 600)
}

function updateTemplate(area, newText){
	$(area).innerHTML = newText
}

function setFooter(){
	if($('footer_setting').checked){
		$('menu_footer').value='on'
	}else{
		$('menu_footer').value='off'
	}
}

function bgRevert(bgType) {
   //alert(bgType)
   var daCurArea = $('curArea').value
   //alert(daCurArea)
	var hidImg = $('imgSel').value
	
	var availColors = new Array("green", "blue", "red", "grey")
	for (i=0; i<availColors.length; i++){
		var is_flow = hidImg.search(availColors[i])
		if(is_flow > 0){
			if(bgType == '0'){
				if(nn6){
					document.styleSheets[6].cssRules[0].style.background=allStyles[i][1]
				}else{
					document.styleSheets[6].rules[0].style.background=allStyles[i][1]
				}
			}
			if(bgType == '2'){
				if(nn6){
					document.styleSheets[6].cssRules[1].style.background=allStyles[i][3]
					$('side_menu').style.background=allStyles[i][3]
				}else{
					document.styleSheets[6].rules[1].style.background=allStyles[i][1]
				}
			}
			if(bgType == '3'){
				if(nn6){
					document.styleSheets[6].cssRules[2].style.background=allStyles[i][4]
				}else{
					document.styleSheets[6].rules[2].style.background=allStyles[i][1]
				}
			}
			if(bgType == '4'){
				if(nn6){
					document.styleSheets[6].cssRules[3].style.background=allStyles[i][5]
				}else{
					document.styleSheets[6].rules[3].style.background=allStyles[i][1]
				}
			}
		}
	}
}
	

function saveTemplate() {
	//openInfoDialog()
	var customFields = ''
	var styleSel = $('styleSel').value
	
	var availColors = new Array("green", "blue", "red", "grey")
	
	for (i=0; i<availColors.length; i++){
		var is_flow = styleSel.search(availColors[i])
		if(is_flow > 0){
			var imgColor = availColors[i]
			for (j=0; j<allStyles[i].length; j++){
				if(!customStyles[j]){
					//alert('NO('+customStyles[j]+')')
					customStyles[j] = allStyles[i][j]
					var repPound = allStyles[i][j].replace('#', '')
					customFields += '&custom_'+j+'='+repPound
				}else{
					//alert('YES('+customStyles[j]+')')
					var repPound = customStyles[j].replace('#', '')
					customFields += '&custom_'+j+'='+repPound
				}
				//alert('CURRENT('+repPound+')')
			}
		}
	}
	//alert(customFields)
		
	var imgSel = $('imgSel').value
	var schemeSel = $('schemeSel').value
	var site_title = $('site_title').value
	var site_slogan = $('site_slogan').value
	var menu_main = $('menu_main').value
	var menu_footer = $('menu_footer').value
	var template_name = $('template_name').value
	
	if(template_name.search(/[^a-zA-Z0-9_\-]/) < 0){
	   window.location = 'build_template.php?template_name='+template_name+'&img='+imgSel+'&style='+styleSel+'&scheme='+schemeSel+'&title='+site_title+'&slogan='+site_slogan+'&menu_main='+menu_main+'&menu_footer='+menu_footer+customFields
	   //alert('Yay')
	}else{
	   alert('The template name you entered is not valid.  Please only use alphanumeric characters including underscores and dashes.')
	}
}

function sendUpdateHex(){
   var daCurArea = $('curArea').value
   var hexvalue = $('pickerInput').value
   //alert(daCurArea+'-'+hexvalue)
   updateDisplay(daCurArea, hexvalue)
}

function updateDisplay(classToChange, newValue){
	var disClass = parseInt(classToChange)
	//alert(disClass)
	var hidImg = $('imgSel').value
	var availColors = new Array("green", "blue", "red", "grey")
	
	for (i=0; i<availColors.length; i++){
		var is_flow = hidImg.search(availColors[i])
		if(is_flow > 0){
			var imgColor = availColors[i]
			for (j=0; j<allStyles.length; j++){
				
				var is_flow = allStyles[j][0].search(imgColor)
				//alert(headerStyles[j][0])
				if(is_flow >= 0){
					var arrVal = disClass
					if(disClass == 0){
						arrVal++
					}else{
						arrVal = disClass + 2;
					}
					//alert(customStyles[arrVal])
					//alert(arrVal)
					customStyles[arrVal] = '#'+newValue
					//alert(customStyles[arrVal])
				}
			}
		}
	}
	if(disClass == 1 || disClass == 2){
		var changeBGtoo = disClass + 5
		//alert(changeBGtoo)
		if(nn6){
			document.styleSheets[6].cssRules[changeBGtoo].style.backgroundColor='#'+newValue
		}else{
			document.styleSheets[6].rules[changeBGtoo].style.backgroundColor='#'+newValue
		}
	}
	//alert(disClass)
	if(nn6){
		document.styleSheets[6].cssRules[disClass].style.backgroundColor='#'+newValue
	}else{
		document.styleSheets[6].rules[disClass].style.backgroundColor='#'+newValue
	}
	
}

function giveBorder(toDis){
	//alert(toDis)
	var availAreas = new Array("tmplt_display", "top_menu", "top_sub_menu", "footer_td")
	for (i=0; i<availAreas.length; i++){
		var is_flow = toDis.search(availAreas[i])
		//alert(is_flow)
		if(is_flow >= 0){
			$(toDis).style.border='2px solid red';
		}else{
			$(availAreas[i]).style.border='0px solid #FFFF00';
		}
	}
	if(toDis == 'top_sub_menu'){
		$('side_menu_light').style.border='2px solid red';
		$('side_sub_menu').style.border='2px solid red';
	}
	if(toDis == 'top_menu'){
		$('side_menu').style.border='2px solid red';
	}
	if(toDis == 'none' || toDis != 'top_sub_menu'){
		$('side_menu_light').style.border='0px solid red';
		$('side_sub_menu').style.border='0px solid red';
		$('side_menu').style.border='0px solid red';
	}else if(toDis != 'top_menu'){
		$('side_menu').style.border='0px solid red';
	}
}
	

function runStartup() {

   //alert($('imgSel').value+'---'+$('styleSel').value+'---'+$('schemeSel').value)
   
   if($('imgSel').value == '' && $('styleSel').value == '' && $('schemeSel').value == 'blue'){
   	$('main_img').src = 'http://<? echo $this_ip; ?>/sohoadmin/program/modules/template_builder/images/head/main/none_blue-stripe.jpg'
   	$('main_imgBG').style.background = 'url(http://<? echo $this_ip; ?>/sohoadmin/program/modules/template_builder/images/head/BG/stripe-blue.jpg) no-repeat'
   	$('imgSel').value='http://<? echo $this_ip; ?>/sohoadmin/program/modules/template_builder/images/head/main/none_blue-stripe.jpg'
   	$('styleSel').value='http://<? echo $this_ip; ?>/sohoadmin/program/modules/template_builder/images/head/BG/stripe-blue.jpg'
   	//$('schemeSel').value=new_img
   }
   
   $('title').innerHTML = '<? echo addslashes($df_hdrtxt); ?>'
   $('slogan').innerHTML = '<? echo addslashes($df_slogan); ?>'
}



</script>



</head>

<body onload="MM_preloadImages('images/head/main/none_blue-stripe.jpg','images/head/BG/stripe-blue.jpg')" onunload="resizeWinBak()">
<!---  -->
<script type="text/javascript" src="../../includes/display_elements/wz_dragdrop.js"></script>

<input type="hidden" name="curArea" id="curArea" />
<input type="hidden" name="imgSel" id="imgSel" />
<input type="hidden" name="styleSel" id="styleSel" />
<input type="hidden" name="schemeSel" id="schemeSel" value="blue" />

<input type="hidden" name="menu_main" id="menu_main" value="top" />
<input type="hidden" name="menu_footer" id="menu_footer" value="off" />

<div id="picker" style="display: none;">
<? echo $picker; ?>
</div>



<div class="head_text" onClick="addMenu()"><b>Welcome to the template builder<i>!</i></b> Here you can create a unique template for your website by choosing image, color, style,
menu, website title and slogan.  Step 5 will allow you to name your template and save<i>!</i>.
These templates will be added to the list of avalible site templates.</div>


<!---<div id="reload" style="cursor: pointer; border: 0px solid red;float:left;width:100px;" onClick="resizeWin()">Big</div>
<div id="reload" style="cursor: pointer; border: 0px solid red;float:left;width:100px;" onClick="resizeWinBak()">Small</div>
<div id="menu_child2" style="position: relative; width: 150px; visibility: hidden;">
<div id="VMENU" class="vmenu_sub_off" onmouseover="this.className='vmenu_sub_over';" onmouseout="this.className='vmenu_sub_off';" style="display: block; width: 140px; height:15px;">Vertical Menu</div>
<div id="HMENU" class="vmenu_sub_off" onmouseover="this.className='vmenu_sub_over';" onmouseout="this.className='vmenu_sub_off';" style="display: block; width: 140px; height:15px;">Horizontal Menu</div>
</div> -->
<!--- at_attach('menus', 'menu_child2', 'hover', 'y', 'pointer');at_attach('news', 'menu_child3', 'hover', 'y', 'pointer');at_attach('general', 'menu_child4', 'hover', 'y', 'pointer');;if (window.dd &amp;&amp; dd.elements &amp;&amp; !dd.elements.VMENU) { ADD_DHTML('VMENU'+CLONE); ADD_DHTML('HMENU'+CLONE); ADD_DHTML('NEWSBOX'+CLONE); ADD_DHTML('PROMOBOX'+CLONE); ADD_DHTML('LOGO'+CLONE); ADD_DHTML('SLOGAN'+CLONE); ADD_DHTML('COPYRIGHT'+CLONE); } return false;"> -->
<!--- dd.elements.VMENU.show(); dd.elements.HMENU.show(); -->

<!--- <div id="joe" style="position: relative; background: #336699;">LALA</div> -->
	<!--- <p>Template manager explanation.</p> -->

    <!---Preferences block-->
    <div id="pref" style="border: 0px solid red;"></div>

    <!---Container div-->
    <div id="tab_interface_container" style="display: block; width: 98%;margin: 40px 5px 20px 5px;position: relative; border: 0px solid red;">
    

     <!---================== Tabs - START ==================
     <div id="layout_tab1" class="tab-on" onclick="showid('tab1-content');hideid('tab2-content');hideid('tab3-content');setClass('layout_tab1', 'tab-on');setClass('layout_tab2', 'tab-off');setClass('layout_tab3', 'tab-off');">
      Layout
     </div>

     <div id="layout_tab2" class="tab-off" onclick="showid('tab2-content');hideid('tab1-content');hideid('tab3-content');setClass('layout_tab2', 'tab-on');setClass('layout_tab1', 'tab-off');setClass('layout_tab3', 'tab-off');">
      Header
     </div>

     <div id="layout_tab3" class="tab-off" onclick="showid('tab3-content');hideid('tab1-content');hideid('tab2-content');setClass('layout_tab1', 'tab-off');setClass('layout_tab2', 'tab-off');setClass('layout_tab3', 'tab-on');">
      Navigation
     </div>
     
     <div id="layout_tab4" class="tab-off" onclick="saveTemplate()">
      Finish
     </div>-->

		<table id="tab1-content" width="100%" border="0" cellspacing="0" cellpadding="0" class="feature_sub tab_content" style="display: table;">
			<tr>
				<td width="100%" style="padding: 10px;" align="left">
					<div class="head_nav">
						<span class="img_head"><img src="images/applications-graphics.png" width="48" height="48"></span>
						<span class="desc_text"><? echo lang("<h3>Step 1:</h3> Choose an image, style and color scheme for your template"); ?>.
						<?
						if($_GET['done'] == 1){
							echo "<h4>Template Created!</h4>";
						}
						?>
						</span>
						<span class="btn_next"><img src="images/go-last.png" width="24" height="24" onclick="showid('tab2-content');hideid('tab1-content');hideid('tab3-content');hideid('tab4-content');" /><br/>Next</span>
					</div>
				
         <table border="0" cellspacing="0" cellpadding="0" class="feature_sub" style="margin: 20px 5px 0px 5px; width:950px;">
			      <tr> 
			       <!--- <td align="center" valign="middle" class="header_text">Industry</td> -->
			       <td align="center" valign="middle" class="header_text">Image</td>
			       <td align="center" valign="middle" class="header_text">Style</td>
			       <td align="center" valign="middle" class="header_text">Color scheme</td>
			      </tr>
			      <tr> 
			       <!--- <td align="center" valign="middle" class="layout_industry_image">

			      	<div class="layout_industry">
							<select onchange="show_image(this.value);" style="width: 200px;" class="text" size="1" name="IMAGE">
								<option value="General">General</option>
								<option value="Animals">Animals</option>
								<option value="Corporate">Corporate</option>
								<option value="Neutral">Neutral</option>
							</select>
						</div>

			       </td> -->
			       <?
			      if ( eregi("MSIE", $_SERVER['HTTP_USER_AGENT']) ) {
			       	echo "<td align=\"center\" valign=\"middle\" class=\"layout_industry_image_ie\">\n";
			       	echo"		<div class=\"layout_image_ie\">\n";
						echo $img_selection;
						echo "	</div>\n";
						echo "</td>\n";
						
			       	echo "<td align=\"center\" valign=\"middle\" class=\"layout_industry_image_ie\">\n";
			       	echo"		<div class=\"layout_image_ie\">\n";
						echo $img_selectionBG;
						echo "	</div>\n";
						echo "</td>\n";
						
			       	echo "<td align=\"center\" valign=\"middle\" class=\"layout_industry_image_ie\">\n";
			       	echo"		<div class=\"layout_image_ie\">\n";
						echo $img_selectionLayout;
						echo "	</div>\n";
						echo "</td>\n";
						
			      }else{
			      	echo "<td align=\"center\" valign=\"middle\" class=\"layout_industry_image\">\n";
			       	echo"		<div class=\"layout_image\">\n";
						echo $img_selection;
						echo "	</div>\n";
						echo "</td>\n";
						
			       	echo "<td align=\"center\" valign=\"middle\" class=\"layout_industry_image\">\n";
			       	echo"		<div class=\"layout_image\">\n";
						echo $img_selectionBG;
						echo "	</div>\n";
						echo "</td>\n";
						
			       	echo "<td align=\"center\" valign=\"middle\" class=\"layout_industry_image\">\n";
			       	echo"		<div class=\"layout_image\">\n";
						echo $img_selectionLayout;
						echo "	</div>\n";
						echo "</td>\n";
			      }
			      
			       ?>
			      </TR>
         </table>
         
			
				<td>
			<tr>
		</table><!---End Tab1--->
		
		<table id="tab2-content" width="100%" border="0" cellspacing="0" cellpadding="5" class="feature_sub tab_content" style="display: none;">
			<tr>
				<td width="100%" align="left" style="padding: 10px;">
				<div class="head_nav">
					<span class="img_head"><img src="images/preferences-system-windows.png" width="48" height="48"></span>
					<span class="desc_text"><? echo lang("<h3>Step 2:</h3> Choose a navigation layout and setting for the footer"); ?>.</span>
					<span class="btn_next"><img src="images/go-last.png" width="24" height="24" onclick="showid('tab3-content');hideid('tab2-content');" /><br/>Next</span>
					<span class="btn_next"><img src="images/go-first.png" width="24" height="24" onclick="showid('tab1-content');hideid('tab2-content');" /><br/>Back</span>
				</div>
				
         <table border="0" cellspacing="0" cellpadding="0" class="feature_sub" style="margin: 20px 5px 0px 5px; width:950px;">
			      <tr> 
			       <td align="center" valign="middle" class="header_text">Main Navigation</td>
			       <td align="center" valign="middle" class="header_text">Footer Menu</td>
			      </tr>
			      <tr> 
			       <td align="center" valign="middle" class="layout_industry_image_nav">

							<span class="nav_choice" onclick="changeMenu('side');">
								<IMG SRC='images/menu_left.jpg'><br/><span>Left</span>
							</span>
						
							<span class="nav_choice" onclick="changeMenu('both');">
								<IMG SRC='images/menu_both.jpg'><br/><span>Top & Left</span>
							</span>
						
							<span class="nav_choice" onclick="changeMenu('top');" >
								<IMG SRC='images/menu_top.jpg'><br/><span>Top</span>
							</span>
						
			       </td>
			       <td align="center" valign="middle" class="layout_industry_image">

					Display footer menu? <input type="checkbox" id="footer_setting" name="footer_setting" onClick="toggleid('footer_menu');setFooter();">
						
			       </td>
			      </tr>
         </table>
				
				<td>
			<tr>
		</table><!---End Tab2--->
		
		
		<table id="tab3-content" width="100%" border="0" cellspacing="0" cellpadding="5" class="feature_sub tab_content" style="display: none;">
			<tr>
				<td width="100%" align="left" style="padding: 10px;">
				<div class="head_nav">
					<span class="img_head"><img src="images/folder-new.png" width="48" height="48"></span>
					<span class="desc_text"><? echo lang("<h3>Step 3:</h3> Customize template background colors"); ?><i>!</i></span>
					<span class="btn_next"><img src="images/go-last.png" width="24" height="24" onclick="hideid('picker');showid('tab4-content');hideid('tab3-content');giveBorder('none')" /><br/>Next</span>
					<span class="btn_next"><img src="images/go-first.png" width="24" height="24" onclick="hideid('picker');showid('tab2-content');hideid('tab3-content');giveBorder('none')" /><br/>Back</span>
				</div>
				<?
		      if ( eregi("MSIE", $_SERVER['HTTP_USER_AGENT']) ) {
		      	echo "		<div class=\"noteBG\" style=\"position: relative; float: left; top: 48px; left: 6px;\">\n";
					echo "			<b>Note:</b> Choose a background to modify.\n";
					echo "		</div>\n";
		      }
		      ?>
         <table border="0" cellspacing="0" cellpadding="0" class="feature_sub" style="margin: 20px 5px 0px 5px; width:950px;">
			      <tr> 
			       <td align="center" valign="middle" class="header_text_BG">Background Options</td>
			       <td align="center" valign="middle" class="header_text_BG_wide">Selected Color</td>
			      </tr>
			      <tr> 
			      <?
			      if ( eregi("MSIE", $_SERVER['HTTP_USER_AGENT']) ) {
			      	echo "		<td align=\"left\" valign=\"middle\" class=\"layout_industry_image_ie\" style=\"padding-left: 20px;\">\n";
			      	echo "		<div class=\"layout_industry\" style=\"margin-top: 15px;\">\n";
			      }else{
			      	echo "		<td align=\"center\" valign=\"middle\" class=\"layout_industry_image\">\n";
			      	echo "		<div class=\"noteBG\" style=\"margin-top: -5px;\">\n";
						echo "			<b>Note:</b> Choose a background to modify.\n";
						echo "		</div>\n";
						echo "		<div class=\"layout_industry\" style=\"padding-top: -15px;\">\n";
						
			      }
			      ?>
			      	
				      	<table border="0" cellspacing="0" cellpadding="0">
								<tr>
							   	<td align="left" valign="middle" onClick="showid('picker');$('curArea').value='0';$('BGcolorArea').innerHTML='Background Color';giveBorder('tmplt_display');" class="menuColorOpts">Background Color</td>
							   	<td align="center" valign="middle" class="menuColorRevert"><img src="images/edit-undo.png" width="18" height="18" onClick="bgRevert('0')" title="Original Color"></td>
							   </tr>
								<tr>
							   	<td align="left" valign="middle" onClick="showid('picker');$('curArea').value='1';$('BGcolorArea').innerHTML='Menu Background';giveBorder('top_menu');" class="menuColorOpts">Menu Background</td>
							   	<td align="center" valign="middle" class="menuColorRevert"><img src="images/edit-undo.png" width="18" height="18" onClick="bgRevert('2')" title="Original Color"></td>
							   </tr>
								<tr>
							   	<td align="left" valign="middle" onClick="showid('picker');$('curArea').value='2';$('BGcolorArea').innerHTML='Menu Sub Background';giveBorder('top_sub_menu');" class="menuColorOpts">Menu Sub Background</td>
							   	<td align="center" valign="middle" class="menuColorRevert"><img src="images/edit-undo.png" width="18" height="18" onClick="bgRevert('3')" title="Original Color"></td>
							   </tr>
								<tr>
							   	<td align="left" valign="middle" onClick="showid('picker');$('curArea').value='3';$('BGcolorArea').innerHTML='Footer Background';giveBorder('footer_td');" class="menuColorOpts">Footer Background</td>
							   	<td align="center" valign="middle" class="menuColorRevert"><img src="images/edit-undo.png" width="18" height="18" onClick="bgRevert('4')" title="Original Color"></td>
							   </tr>
							</table>
							       
			      	
			      	
			      		<!--- <span class="menuColorOpts" onClick="showid('picker');$('curArea').value='0';$('BGcolorArea').innerHTML='Background Color';" style="cursor: pointer;">Background Color<span class="menuColorRevert" onClick="bgRevert('0')"><img src="images/edit-undo.png" width="18" height="18" onClick="window.location='template_builder.php'"></span></span>
			      		<span class="menuColorOpts" onClick="showid('picker');$('curArea').value='1';$('BGcolorArea').innerHTML='Menu Background';" style="cursor: pointer;">Menu Background<span class="menuColorRevert" onClick="bgRevert('2')"><img src="images/edit-undo.png" width="18" height="18" onClick="window.location='template_builder.php'"></span></span>
			      		<span class="menuColorOpts" onClick="showid('picker');$('curArea').value='2';$('BGcolorArea').innerHTML='Menu Sub Background';" style="cursor: pointer;">Menu Sub Background<span class="menuColorRevert" onClick="bgRevert('3')"><img src="images/edit-undo.png" width="18" height="18" onClick="window.location='template_builder.php'"></span></span>
			      		<span class="menuColorOpts" onClick="showid('picker');$('curArea').value='3';$('BGcolorArea').innerHTML='Footer Background';" style="cursor: pointer;">Footer Background<span class="menuColorRevert" onClick="bgRevert('4')"><img src="images/edit-undo.png" width="18" height="18" onClick="window.location='template_builder.php'"></span></span> -->
			      		
							<!--- <input type="button" id="menu_top_picker" onClick="toggleid('picker');$('curArea').value='0';" value="Background Color" />
							<input type="button" id="menu_top_picker" onClick="toggleid('picker');$('curArea').value='1';" value="Top Menu Color" />
							<input type="button" id="menu_top_picker" onClick="toggleid('picker');$('curArea').value='2';" value="Top Sub Menu Color" />
							<input type="button" id="menu_top_picker" onClick="toggleid('picker');$('curArea').value='0';" value="Side Menu Color" />
							<input type="button" id="menu_top_picker" onClick="toggleid('picker');$('curArea').value='0';" value="Side Sub Menu Color" />
							<input type="button" id="menu_top_picker" onClick="toggleid('picker');$('curArea').value='3';" value="Footer Color" />
							<input type="button" id="menu_top_picker" onClick="toggleid('picker');$('curArea').value='0';" value="Side Menu Color" /> -->
						</div>

			       </td>

			      <?
			      if ( eregi("MSIE", $_SERVER['HTTP_USER_AGENT']) ) {
			      	echo "		<td align=\"center\" valign=\"middle\" class=\"layout_industry_image_ie\">\n";
			      }else{
			      	echo "		<td align=\"center\" valign=\"middle\" class=\"layout_industry_image\">\n";
			      }
			      ?>

			      	<div class="layout_industry" style="margin-top: 10px;">
							<div id="pickerSwatch">&nbsp;</div>
							<div id="pickerInputDiv">
							   #<input type="text" id="pickerInput" name="pickerInput" />
							   <input type="button" id="pickerInputButton" name="pickerInputButton" value="Set" onClick="sendUpdateHex();" />
							</div>
						</div>

			       </td>
			      </tr>
         </table>
				
				<td>
			<tr>
		</table><!---End Tab3--->
		
		<table id="tab4-content" width="100%" border="0" cellspacing="0" cellpadding="5" class="feature_sub tab_content" style="display: none;">
			<tr>
				<td width="100%" style="padding: 10px;" align="left">
				<div class="head_nav">
					<span class="img_head"><img src="images/accessories-text-editor.png" width="48" height="48"></span>
					<span class="desc_text"><? echo lang("<h3>Step 4:</h3> Enter a site title and site slogan"); ?>.</span>
					<span class="btn_next"><img src="images/go-last.png" width="24" height="24" onclick="showid('tab5-content');hideid('tab4-content');" /><br/>Next</span>
					<span class="btn_next"><img src="images/go-first.png" width="24" height="24" onclick="showid('tab3-content');hideid('tab4-content');" /><br/>Back</span>
				</div>
				
         <table border="0" cellspacing="0" cellpadding="0" class="feature_sub" style="margin: 20px 5px 0px 5px; width:950px;">
			      <tr> 
			       <td align="center" valign="middle" class="header_text">Site Title</td>
			       <td align="center" valign="middle" class="header_text">Site Slogan</td>
			      </tr>
			      <tr> 
			      <?
			      if ( eregi("MSIE", $_SERVER['HTTP_USER_AGENT']) ) {
			      	echo "		<td align=\"center\" valign=\"middle\" class=\"layout_industry_image_ie\">\n";
			      }else{
			      	echo "		<td align=\"center\" valign=\"middle\" class=\"layout_industry_image\">\n";
			      }
			      ?>

			      	<div class="layout_industry" style="margin-top: 10px;">
							<input type="text" id="site_title" name="site_title" size="50" onKeyUp="updateTemplate('title', this.value)" value="<? echo $df_hdrtxt; ?>" />
						</div>
						
			      	<div class="note" style="margin-top: 0px;">
							<b>Note:</b> Site title and slogan will not be saved with the template.  This step is only for display purposes.
						</div>

			       </td>
			      <?
			      if ( eregi("MSIE", $_SERVER['HTTP_USER_AGENT']) ) {
			      	echo "		<td align=\"center\" valign=\"middle\" class=\"layout_industry_image_ie\">\n";
			      }else{
			      	echo "		<td align=\"center\" valign=\"middle\" class=\"layout_industry_image\">\n";
			      }
			      ?>

						<div class="layout_industry" style="margin-top: 10px;">
						<textarea id="site_slogan" name="site_slogan" cols="25" rows="2" style="overflow: auto;" onKeyUp="updateTemplate('slogan', this.value)"><? echo $df_slogan; ?></textarea>
						</div>

			       </td>
			      </tr>
         </table>
		
				<td>
			<tr>
		</table><!---End Tab4--->
		
		
		<table id="tab5-content" width="100%" border="0" cellspacing="0" cellpadding="5" class="feature_sub tab_content" style="display: none;">
			<tr>
				<td width="100%" align="left" style="padding: 10px;">
				<div class="head_nav">
					<span class="img_head"><img src="images/folder-new.png" width="48" height="48"></span>
					<span class="desc_text"><? echo lang("<h3>Step 5:</h3> Enter a name for this template and save"); ?><i>!</i></span>
					<span class="btn_next"><img src="images/go-first.png" width="24" height="24" onclick="showid('tab4-content');hideid('tab5-content');" /><br/>Back</span>
				</div>
				
         <table border="0" cellspacing="0" cellpadding="0" class="feature_sub" style="margin: 20px 5px 0px 5px; width:950px;">
			      <tr> 
			       <td align="center" valign="middle" class="header_text">Template Name</td>
			       <td align="center" valign="middle" class="header_text">Save</td>
			      </tr>
			      <tr> 
			      <?
			      if ( eregi("MSIE", $_SERVER['HTTP_USER_AGENT']) ) {
			      	echo "		<td align=\"center\" valign=\"middle\" class=\"layout_industry_image_ie\">\n";
			      }else{
			      	echo "		<td align=\"center\" valign=\"middle\" class=\"layout_industry_image\">\n";
			      }
			      ?>
			      	<div class="layout_industry" style="margin-top: 10px;">
							<input type="text" id="template_name" name="template_name" size="50" value="Untitled" />
						</div>
						
			      	<div class="note" style="margin-top: 0px;">
							Please only use alphanumeric characters including underscores and dashes in template names.
							The correct template name format would be <b>Category-Sub_Category-Color</b>.
						</div>

			       </td>

			      <?
			      if ( eregi("MSIE", $_SERVER['HTTP_USER_AGENT']) ) {
			      	echo "		<td align=\"center\" valign=\"middle\" class=\"layout_industry_image_ie\">\n";
			      }else{
			      	echo "		<td align=\"center\" valign=\"middle\" class=\"layout_industry_image\">\n";
			      }
			      ?>

			      	<div class="layout_industry" style="margin-top: 10px;">
							<img src="images/document-save.png" width="48" height="48" style="cursor: pointer;" onclick="saveTemplate()">
						</div>

			       </td>
			      </tr>
         </table>
				
				<td>
			<tr>
		</table><!---End Tab5--->


</div>

<div id="tmplt_display">
	<?
	$filename = "styles/full.html";
	include("$filename");
	?>
</div>

<script language="javascript">

setTimeout('runStartup();', 500);
	
</script>

</body>
</html>