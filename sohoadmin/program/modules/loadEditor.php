<?php
error_reporting(E_PARSE);
if($_GET['_SESSION'] != '' || $_POST['_SESSION'] != '' || $_COOKIE['_SESSION'] != '') { exit; }

session_start();
error_reporting(E_PARSE);
# Include core interface files!
include("../includes/product_gui.php");
$type = $_GET['type'];
$curtext = $_GET['curtext'];
?>
<link rel="stylesheet" type="text/css" href="../product_gui.css">
<script language="javascript">

<?


//-----------------------------------------------
// SAVE CONTENT TO SPECIFIC DIV ID
//-----------------------------------------------

echo "function DoIt(stuff){\n";

if ( $curtext == "" ) {

   //echo "alert(stuff)\n";
   echo ("  var textArr = stuff.split('src=\"images/')\n");
   echo ("  var textLen = textArr.length\n");
   echo ("  for(var x=0; x<textLen; x++){\n");
   echo ("	   stuff = stuff.replace('src=\"images/', 'src=\"http://".$this_ip."/images/');\n");
   echo ("  }\n");
   //echo "alert(stuff)\n";

   echo "   var joez = window.opener.setHtmlfirst(stuff);\n";
   //echo "   alert(\"Content saved!\");\n";
   echo "   window.close();\n";
} else {

   //echo "alert(stuff)\n";
   echo ("  var textArr = stuff.split('src=\"images/')\n");
   echo ("  var textLen = textArr.length\n");
   echo ("  for(var x=0; x<textLen; x++){\n");
   echo ("	   stuff = stuff.replace('src=\"images/', 'src=\"http://".$this_ip."/images/');\n");
   echo ("  }\n");
   //echo "alert(stuff)\n";

   echo "   var joez = window.opener.setHtml('".$curtext."',stuff);\n";
   //echo "   alert(\"Content saved!\");\n";
   echo "   window.close();\n";
}

echo "}\n";

//-----------------------------------------------
// END SAVE CONTENT TO SPECIFIC DIV ID
//-----------------------------------------------
?>

function onSaveFileSOHO(){
      var NewFinal = editor.editGetHtml();
      var re = new RegExp("<blink>","gi");
      var NewFinal = NewFinal.replace(re, " ");
      var re = new RegExp("</blink>","gi");
      var NewFinal = NewFinal.replace(re, " ");
      var re = new RegExp("<BLINK>","gi");
      var NewFinal = NewFinal.replace(re, " ");
      var re = new RegExp("</BLINK>","gi");
      var NewFinal = NewFinal.replace(re, " ");
      var billy = DoIt(NewFinal);
}

<?

//-----------------------------------------------
// GET CONTENT FROM SPECIFIC DIV ID
//-----------------------------------------------

echo "var editor = null;\n";
echo "var html = '';\n";
echo "function editOnEditorLoaded(objEditor,id){\n";
echo "   editor = objEditor;\n";
echo "   var html = window.opener.getHtml('".$curtext."');\n";
//echo "   alert(html);\n";
echo "   html = html.replace('<blink>','');\n";
echo "   html = html.replace('</blink>','');\n";
echo "   html = html.replace('<BLINK>','');\n";
echo "   html = html.replace('</BLINK>','');\n";
//echo "   alert(html);\n";
//echo "   var style = \"<LINK href=\"../../../runtime.css\" type=text/css rel=stylesheet>\";\n";
//echo "   alert(style);\n";
echo "   editor.editFullSize();\n";
echo "   document.getElementById('loadEdit').style.display='none'; \n";
echo "   document.getElementById('saveIt').style.display='block'; \n";
echo "   editor.editWrite(html);\n";
echo "   editor.editSetFocus();\n";
echo "}";
// write style and content to the editor
//echo "   editor.editWrite(style + html);\n";

//echo "   parent.maximize();\n";




echo "window.onresize=editFullNow\n";

echo "function editFullNow(){\n";
echo "   editor.editFullSize();\n";
echo "}\n";

echo "</script>\n";

//-----------------------------------------------
// END GET CONTENT FROM SPECIFIC DIV ID
//-----------------------------------------------

$tb = "T091006070861562021361113123522232427282930313299;B535455";
$mod = "none";
$doc_root_fix = str_replace("\\","/",$doc_root);

if($type=="basic"){
   $options = "editor/pinEdit.php?hb=1&modInUse=".$modInUse."&cu=http://".$this_ip."/&cp=".$doc_root_fix."/&dpa=".$doc_root_fix."/media/&dua=http://".$this_ip."/media/&iua=http://".$this_ip."/images/&ipa=".$doc_root_fix."/images/&rpl=http://".$this_ip."/&rpi=http://".$this_ip."/&eu=http://".$this_ip."/sohoadmin/program/modules/editor/&tb=".$tb."&trh=0&pfo=1";
   echo "<iframe id=\"editor\" WIDTH=100% HEIGHT=450 src=\"".$options."\" frameborder=0></iframe>\n";
}else{
   $options = "editor/pinEdit.php?hb=1&modInUse=".$modInUse."&cu=http://".$this_ip."/&cp=".$doc_root_fix."/&dpa=".$doc_root_fix."/media/&dua=http://".$this_ip."/media/&iua=http://".$this_ip."/images/&ipa=".$doc_root_fix."/images/&rpl=http://".$this_ip."/&rpi=http://".$this_ip."/&eu=http://".$this_ip."/sohoadmin/program/modules/editor/&trh=0&pfo=1";
   echo "<iframe id=\"editor\" WIDTH=100% HEIGHT=450 src=\"".$options."\" frameborder=0></iframe>\n";
}

?>

<div id="loadEdit" style="position:absolute; top:200; left:260; z-index:1000; display:none;">
   <table width="275"  border="0" cellspacing="0" cellpadding="0" class="feature_sub">
     <tr>
       <td class="fgroup_title">Loading</td>
     </tr>
     <tr>
       <td class="fprev_note" align="center" height="20" style="padding: 10px;">
        <b>Please wait...</b><br>
        <p>This will typically load a bit faster after you've loaded it once.</p>
        <!--- <p>The Text Editor also runs faster in FireFox/Mozilla browsers than in Internet Explorer.</p> -->
       </td>
     </tr>
   </table>
</div>

<div id="saveIt" style="position:absolute; bottom: 5px; right: 15px; z-index:1000; display:block;">
 <input onClick="onSaveFileSOHO();" type="button" id="openpage" value="  Save  " style="width: 150px;padding: 1px;" <? echo $_SESSION['btn_build']; ?> >
</div>

