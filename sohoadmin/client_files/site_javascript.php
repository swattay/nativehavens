<?php
error_reporting(E_PARSE);
if($_GET['_SESSION'] != '' || $_POST['_SESSION'] != '' || $_COOKIE['_SESSION'] != '') { exit; }


# This script is included in pgm-realtime_builder and pgm-template_builder...
# <script language="javascript" src="/sohoadmin/client_files/site_javascript.php"></script>
error_reporting(E_PARSE);
session_start();

# Restore webmaster prefs
include_once($_SESSION['docroot_path']."/pgm-site_config.php");
include_once($_SESSION['docroot_path']."/sohoadmin/program/includes/shared_functions.php");
//error_reporting(E_ALL);
error_reporting(E_PARSE);
$webmaster_pref = new userdata('webmaster_pref');
?>

<!--

function killErrors() {
     return true;
}
window.onerror = killErrors;

function MM_reloadPage(init) {  // reloads the window if Nav4 resized
     if (init==true) with (navigator) {if ((appName=="Netscape")&&(parseInt(appVersion)==4)) {
          document.MM_pgW=innerWidth; document.MM_pgH=innerHeight; onresize=MM_reloadPage; }}
     else if (innerWidth!=document.MM_pgW || innerHeight!=document.MM_pgH) location.reload();
}
MM_reloadPage(true);

function MM_swapImgRestore() { //v3.0
     var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

function MM_preloadImages() { //v3.0
     var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
     var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
     if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_findObj(n, d) { //v3.0
     var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
     d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
     if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
     for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document); return x;
}

function MM_swapImage() { //v3.0
     var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
     if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}

function MM_openBrWindow(theURL,winName,features) { //v2.0
     window.open(theURL,winName,features);
}

if (document.styleSheets) { // Fix Netscape Style Sheet Problems
     if (document.styleSheets.length > 0) {
	        var siteStyles = document.styleSheets[0];
 	        siteStyles.addRule("cinput", "font-size:9pt; height:18px; width:100px;");
     }
}



function toggleid(targetid) {
  var isnow = document.getElementById(targetid).style.display;
  if ( isnow == 'block' ) {
     document.getElementById(targetid).style.display='none';
  } else {
     document.getElementById(targetid).style.display='block';
  }
}

function showid(thingid) {
   document.getElementById(thingid).style.display = 'block';
}

function hideid(thingid) {
   document.getElementById(thingid).style.display = 'none';
}

function setClass(thingid, new_classname) {
   document.getElementById(thingid).className = new_classname;
}

//---------------------------------------------
// Replaces document.getElementById :)
//---------------------------------------------
if ( !$ && Function != $.constructor ) {
   function $() {
     var elements = new Array();

     for (var i = 0; i < arguments.length; i++) {
       var element = arguments[i];
       if (typeof element == 'string')
         element = document.getElementById(element);

       if (arguments.length == 1)
         return element;

       elements.push(element);
     }

     return elements;
   }
}

function mkObject() {
   var x;
   var browser = navigator.appName;

   if ( browser == "Microsoft Internet Explorer" ) {
      x = new ActiveXObject("Microsoft.XMLHTTP");
   } else {
      x = new XMLHttpRequest();
   }

   return x;
}

var request = mkObject();

function ajaxGet(qryString, boxid) {
   rezBox = boxid; // Make global so parseInfo can get it

   request.open('get', qryString);
   request.onreadystatechange = placeInfo;
   request.send('');
}

function placeInfo() {
   if ( request.readyState == 1 ) {
      document.getElementById(rezBox).innerHTML = 'Loading...';
   }
   if ( request.readyState == 4 ) {
      var answer = request.responseText;
      document.getElementById(rezBox).innerHTML = answer;
   }
}


function windowResize(h, w){
   //alert('ok resize')
   var daWindow = Windows.getWindow("admin_dialog")
   daWindow.setSize(h, w)
   daWindow.showCenter(true);
}

var isShowing = null;

function minEme(winId){
   var daWindow = Windows.getWindow(winId)
   if(daWindow.isMinimized){
      $('admin_dialog').style.height='25px'
   }else{
      $('admin_dialog').style.height='555px'
   }
   daWindow.minimize()
   daWindow._checkIEOverlapping()
}

// F2 shortcut login
function mouse_capture( event ) {
		// This is here to make it easier to login to admin section

		var key = event.which;
		var key = event.keyCode;
		if (key == 113){

<?
# New window or layer?
if ( $webmaster_pref->get("f2login") == "layer" ) {
?>
   	   this.win = new Window('admin_dialog', {className: "onscreen_edit", width:795, height:520, showProgress: false, resizable: false, draggable: true, url: "http://<? echo $_SESSION['this_ip']; ?>/sohoadmin/index.php"});
   	   this.win.showCenter(true);
   	   //this.win.maximize()
   	   this.win.setDestroyOnClose();
   	   this.win.setTitle('<? echo $_SESSION['this_ip']; ?>/sohoadmin')
   	   WindowUtilities.enableScreen();
   	   //$('admin_dialog').style.border = '1px solid red;';
   	   //this.win.setZIndex(0);
      	//debugWindow.showCenter()

<?
} else { // Launch new window
?>
      window.open("http://<? echo $_SESSION['docroot_url']; ?>/sohoadmin/index.php?keystroke=on","adminlogin","width=560,height=331,resizable=no,scrollbars=yes");
<?
} // End if f2login == layer
?>
      }
		return;
} // End Func


// Workaround for IE's infinite z-index issue
// Hide all dropdown boxes
function hide_dropdowns() {
   dropdowns = document.getElementsByTagName("select");
   for ( i = 0; i < dropdowns.length; i++ ) {
      dropdowns[i].style.display = 'none';
   }
}
// Show all dropdown boxes
function show_dropdowns() {
   dropdowns = document.getElementsByTagName("select");
   for ( i = 0; i < dropdowns.length; i++ ) {
      dropdowns[i].style.display = 'inline';
   }
}

/*
    Written by Jonathan Snook, http://www.snook.ca/jonathan
    Add-ons by Robert Nyman, http://www.robertnyman.com
*/
function getElementsByClassName(oElm, strTagName, oClassNames){
    var arrElements = (strTagName == "*" && oElm.all)? oElm.all : oElm.getElementsByTagName(strTagName);
    var arrReturnElements = new Array();
    var arrRegExpClassNames = new Array();
    if(typeof oClassNames == "object"){
        for(var i=0; i<oClassNames.length; i++){
            arrRegExpClassNames.push(new RegExp("(^|\\s)" + oClassNames[i].replace(/\-/g, "\\-") + "(\\s|$)"));
        }
    }
    else{
        arrRegExpClassNames.push(new RegExp("(^|\\s)" + oClassNames.replace(/\-/g, "\\-") + "(\\s|$)"));
    }
    var oElement;
    var bMatchesAll;
    for(var j=0; j<arrElements.length; j++){
        oElement = arrElements[j];
        bMatchesAll = true;
        for(var k=0; k<arrRegExpClassNames.length; k++){
            if(!arrRegExpClassNames[k].test(oElement.className)){
                bMatchesAll = false;
                break;
            }
        }
        if(bMatchesAll){
            arrReturnElements.push(oElement);
        }
    }
    return (arrReturnElements)
}

// Checks/unchecks a form checkbox field
// Optional: Pass true/false as second checkuncheck arg
function toggle_checkbox(targetid, checkuncheck) {

   if ( checkuncheck == "check" ) {
      // Set: CHECK
      document.getElementById(targetid).checked = true;
      return true;

   } else if ( checkuncheck == "uncheck" ) {
      // Set: UNCHECK
      document.getElementById(targetid).checked = false;
      return true;

   } else {
      // TOGGLE: Set to opposite of whatever it is now
      var isnow = document.getElementById(targetid).checked;
      if ( isnow == true ) {
         document.getElementById(targetid).checked = false;
         return true;
      } else {
         document.getElementById(targetid).checked = true;
         return true;
      }
   }
}
-->