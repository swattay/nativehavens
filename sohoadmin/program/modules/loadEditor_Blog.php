<?php
error_reporting(E_PARSE);
if($_GET['_SESSION'] != '' || $_POST['_SESSION'] != '' || $_COOKIE['_SESSION'] != '') { exit; }

session_start();
error_reporting(E_PARSE);

# Let's try this
include("../../includes/emulate_globals.php");
$type = $_GET['type'];
$modInUse = $_GET['mod'];
$blogForm = $_GET['blogForm'];
$savebtn = $_GET['savebtn'];
$curtext = $_GET['curtext'];
$blogBox = $_GET['blogBox'];

?>
<link rel="stylesheet" type="text/css" href="../product_gui.css">
<script language="javascript">

<?

	echo "
	      var width = (screen.width);
			var height = (screen.height - 25);
			var centerleft = 0;
			var centertop = 0;
			var centerleft = (width/2) - (400);
			var centertop = (height/2) - (300);
			window.moveTo(centerleft,centertop);
			window.focus();\n\n";

//-----------------------------------------------
// SAVE CONTENT TO SPECIFIC DIV ID
//-----------------------------------------------

echo "function SendBlog(theCont) {\n";
//echo "   alert(theCont);\n";
//echo "   alert('\"$blogBox\"');\n";
//echo "   alert('\"$curtext\"');\n";
//echo "   alert('\"$savebtn\"');\n";
echo "   var hidBox = \"$blogBox\";\n";
echo "   var visBox = \"$curtext\";\n";
echo "   var frmBox = \"$blogForm\";\n";
echo "   var savebtn = \"$savebtn\";\n";
//echo "   alert(frmBox);\n";

//echo "alert(theCont)\n";
echo ("  var textArr = theCont.split('src=\"images/')\n");
echo ("  var textLen = textArr.length\n");
echo ("  for(var x=0; x<textLen; x++){\n");
echo ("	   theCont = theCont.replace('src=\"images/', 'src=\"http://".$this_ip."/images/');\n");
echo ("  }\n");
//echo "alert(theCont)\n";

echo "   window.opener.SetBlog(theCont,hidBox,visBox,frmBox,savebtn);\n";
echo "   window.close();\n";
echo "}\n";


//-----------------------------------------------
// END SAVE CONTENT TO SPECIFIC DIV ID
//-----------------------------------------------
?>

function onSaveFileSOHO(){
      var NewFinal = editor.editGetHtmlBody();
      SendBlog(NewFinal);
}

<?
//-----------------------------------------------
// GET CONTENT FROM SPECIFIC DIV ID
//-----------------------------------------------




echo "var editor = null;\n";
echo "var html = '';\n";
echo "function editOnEditorLoaded(objEditor,id){\n";
echo "   editor = objEditor;\n";
//echo "   alert('\"$blogBox\"');\n";
echo "   var html = window.opener.getHtml('".$blogBox."');\n";
//echo "   alert(html);\n";
//echo "var style = \"<style>.MyStyle{color:red}</style>\"\n";
//echo "   var style = '<LINK href=\"../site_templates/pages/LANDSCAPE-Mountains_Man-Blue/custom.css\" type=text/css rel=stylesheet>';\n";
//echo "   alert(style);\n";
echo "   editor.editFullSize();\n";
echo "   document.getElementById('loadEdit').style.display='none'; \n";
echo "   document.getElementById('saveIt').style.display='block'; \n";
echo "   editor.editWrite(html);\n";
echo "   editor.editSetFocus();\n";
echo "}";

//-----------------------------------------------
// END GET CONTENT FROM SPECIFIC DIV ID
//-----------------------------------------------

echo "window.onresize=editFullNow\n";
//echo "window.onfocus=editFullNow\n";

echo "function editFullNow(){\n";
echo "   editor.editFullSize();\n";
echo "}\n";

echo "</script>\n";

//echo "<iframe id=\"editor\" WIDTH=100% HEIGHT=450 src=\"editor/pinEdit.php?hb=1&modInUse=$mod&cu=http://$this_ip/&cp=$doc_root&dpa=$doc_root/media&dua=http://$this_ip/media/&iua=http://$this_ip/images/&ipa=$doc_root/images/&eu=http://$this_ip/sohoadmin/program/modules/editor/\" frameborder=0></iframe>\n";

$tb = "T091006070861562021361113123522232427282930313299;B535455";
$doc_root_fix = str_replace("\\","/",$doc_root);

if($type=="basic"){
   $options = "editor/pinEdit.php?hb=1&modInUse=".$modInUse."&cu=http://".$this_ip."/&cp=".$doc_root_fix."/&dpa=".$doc_root_fix."/media/&dua=http://".$this_ip."/media/&iua=http://".$this_ip."/images/&ipa=".$doc_root_fix."/images/&rpl=http://".$this_ip."/&rpi=http://".$this_ip."/&eu=http://".$this_ip."/sohoadmin/program/modules/editor/&tb=".$tb."&trh=0&pfo=1";
   echo "<iframe id=\"editor\" WIDTH=100% HEIGHT=450 src=\"".$options."\" frameborder=0></iframe>\n";
}else{
   $options = "editor/pinEdit.php?hb=1&modInUse=".$modInUse."&cu=http://".$this_ip."/&cp=".$doc_root_fix."/&dpa=".$doc_root_fix."/media/&dua=http://".$this_ip."/media/&iua=http://".$this_ip."/images/&ipa=".$doc_root_fix."/images/&rpl=http://".$this_ip."/&rpi=http://".$this_ip."/&eu=http://".$this_ip."/sohoadmin/program/modules/editor/&trh=0&pfo=1";
   echo "<iframe id=\"editor\" WIDTH=100% HEIGHT=450 src=\"".$options."\" frameborder=0></iframe>\n";
}

?>

<div id="loadEdit" style="position:absolute; top:200; left:260; z-index:1000; display:block;">
   <table width="275"  border="0" cellspacing="0" cellpadding="0" class="feature_sub">
     <tr>
       <td class="fgroup_title">Loading...</td>
     </tr>
     <tr>
       <td class="fprev_note" align="center" height="20">Please wait</td>
     </tr>
   </table>
</div>

<!--- <div id="saveIt" style="position:absolute; top:495; left:730; z-index:1000; display:none;">
   <table width="275"  border="0" cellspacing="0" cellpadding="0">
     <tr>
      <td><input onClick="onSaveFileSOHO();" type="button" id="openpage" value="  Save  " style="padding: 1px;" <? echo $_SESSION['btn_build']; ?> ></td>
     </tr>
   </table>
</div> -->

<div id="saveIt" style="position:absolute; bottom: 5px; right: 15px; z-index:1000; display:none;">
 <input onClick="onSaveFileSOHO();" type="button" id="openpage" value="  Save  " style="width: 150px;padding: 1px;" <? echo $_SESSION['btn_build']; ?> >
</div>
