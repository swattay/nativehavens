<?

session_start();
error_reporting(E_PARSE);

include("../includes/product_gui.php");


###############################################################################

$disHTML .= "<script language=\"JavaScript\" src=\"../../includes/display_elements/js_functions.php\"></script>\n";
$disHTML .= "<link href=\"../site_templates/pages/CORPORATE-A_Curvacious_Mark-Blue_Gray/custom.css\" rel=\"stylesheet\" type=\"text/css\"></link>\n";


$disHTML .= "<script language=\"JavaScript\" type=\"text/javascript\" src=\"../../webmaster/includes/faq_includes/core.js\"></script>\n";
$disHTML .= "<script language=\"JavaScript\" type=\"text/javascript\" src=\"../../webmaster/includes/faq_includes/events.js\"></script>\n";
$disHTML .= "<script language=\"JavaScript\" type=\"text/javascript\" src=\"../../webmaster/includes/faq_includes/css.js\"></script>\n";
$disHTML .= "<script language=\"JavaScript\" type=\"text/javascript\" src=\"../../webmaster/includes/faq_includes/coordinates.js\"></script>\n";
$disHTML .= "<script language=\"JavaScript\" type=\"text/javascript\" src=\"../../webmaster/includes/faq_includes/drag.js\"></script>\n";
$disHTML .= "<script language=\"JavaScript\" type=\"text/javascript\" src=\"../../webmaster/includes/faq_includes/dragsort.js\"></script>\n";

$disHTML .= "<script language=\"JavaScript\"><!--\n";
$disHTML .= "var ESCAPE = 27\n";
$disHTML .= "var ENTER = 13\n";
$disHTML .= "var TAB = 9\n";
$disHTML .= "\n";
$disHTML .= "var coordinates = ToolMan.coordinates()\n";
$disHTML .= "var dragsort = ToolMan.dragsort()\n";
$disHTML .= "\n";
$disHTML .= "window.onload = function() {\n";
$disHTML .= "\n";

$disHTML .= "  join(\"a3\")\n";
$disHTML .= "  join(\"b3\")\n";
$disHTML .= "  join(\"c3\")\n";
//$disHTML .= "  join(\"d3\")\n";
//$disHTML .= "  join(\"e3\")\n";

$disHTML .= "}\n";
$disHTML .= "\n";
$disHTML .= "function setHandle(item) {\n";
$disHTML .= "  item.toolManDragGroup.setHandle(findHandle(item))\n";
$disHTML .= "}\n";
$disHTML .= "\n";
$disHTML .= "function findHandle(item) {\n";
$disHTML .= "  var children = item.getElementsByTagName(\"div\")\n";
$disHTML .= "  for (var i = 0; i < children.length; i++) {\n";
$disHTML .= "     var child = children[i]\n";
$disHTML .= "\n";
$disHTML .= "     if (child.getAttribute(\"class\") == null) continue\n";
$disHTML .= "\n";
$disHTML .= "     if (child.getAttribute(\"class\").indexOf(\"handle\") >= 0)\n";
$disHTML .= "        return child\n";
$disHTML .= "  }\n";
$disHTML .= "  return item\n";
$disHTML .= "}\n";
$disHTML .= "\n";
$disHTML .= "function join(name, isDoubleClick) {\n";
$disHTML .= "  var view = document.getElementById(name + \"View\")\n";
//$disHTML .= "	alert(view);\n";
$disHTML .= "  view.editor = document.getElementById(name + \"Edit\")\n";
//$disHTML .= "	alert(view.editor);\n";
$disHTML .= "  var showEditor = function(event) {\n";
//$disHTML .= "		alert(event);\n";
//$disHTML .= "     document.getElementById('saveName').style.display='block';\n";
$disHTML .= "     event = fixEvent(event)\n";
$disHTML .= "     var view = this\n";
$disHTML .= "     var editor = view.editor\n";
$disHTML .= "     if (!editor) return true\n";
$disHTML .= "     if (editor.currentView != null) {\n";
$disHTML .= "        editor.blur()\n";
$disHTML .= "     }\n";
$disHTML .= "     editor.currentView = view\n";
$disHTML .= "     var topLeft = coordinates.topLeftOffset(view)\n";
$disHTML .= "     topLeft.reposition(editor)\n";
$disHTML .= "     if (editor.nodeName == 'TEXTAREA') {\n";
//$disHTML .= "			alert('1');\n";
$disHTML .= "        editor.style['width'] = view.offsetWidth + \"px\"\n";
$disHTML .= "        editor.style['height'] = \"30px\"\n";
//$disHTML .= "			alert(editor.style['height']);\n";
$disHTML .= "     }else{\n";
//$disHTML .= "			alert('11');\n";
$disHTML .= "		}\n";
$disHTML .= "     editor.value = view.innerHTML\n";
$disHTML .= "     editor.style['display'] = 'block'\n";
$disHTML .= "     view.style['display'] = 'none'\n";
$disHTML .= "     editor.focus()\n";
$disHTML .= "     return false\n";
$disHTML .= "  }\n";
$disHTML .= "  if (isDoubleClick) {\n";
$disHTML .= "     view.ondblclick = showEditor\n";
$disHTML .= "  } else {\n";
$disHTML .= "     view.onclick = showEditor;\n";
$disHTML .= "  }\n";

$disHTML .= "  view.editor.onblur = function(event) {\n";
//$disHTML .= "        document.getElementById('saveName').style.display='block';\n";

$disHTML .= "     event = fixEvent(event)\n";
$disHTML .= "     var editor = event.target\n";
$disHTML .= "     var view = editor.currentView\n";
$disHTML .= "     if (!editor.abandonChanges) view.innerHTML = editor.value\n";
$disHTML .= "     editor.abandonChanges = false\n";
$disHTML .= "     editor.style['display'] = 'none'\n";
//$disHTML .= "      editor.value = '' // fixes firefox 1.0 bug\n";
$disHTML .= "     view.style['display'] = 'block'\n";
$disHTML .= "     editor.currentView = null\n";
$disHTML .= "     return true\n";
$disHTML .= "  }\n";
$disHTML .= "  view.editor.onkeydown = function(event) {\n";
$disHTML .= "     event = fixEvent(event)\n";
$disHTML .= "     \n";
$disHTML .= "     var editor = event.target\n";
$disHTML .= "     if (event.keyCode == TAB) {\n";
$disHTML .= "        editor.blur()\n";
$disHTML .= "        return false\n";
$disHTML .= "     }\n";
$disHTML .= "  }\n";
$disHTML .= "  view.editor.onkeyup = function(event) {\n";
$disHTML .= "     event = fixEvent(event)\n";
$disHTML .= "     var editor = event.target\n";
$disHTML .= "     if (event.keyCode == ESCAPE) {\n";
$disHTML .= "        editor.abandonChanges = true\n";
$disHTML .= "        editor.blur()\n";
$disHTML .= "        return false\n";
$disHTML .= "     } else if (event.keyCode == TAB) {\n";
$disHTML .= "        return false\n";
$disHTML .= "     } else {\n";
$disHTML .= "        return true\n";
$disHTML .= "     }\n";
$disHTML .= "  }\n";
$disHTML .= "  // TODO: this method is duplicated elsewhere\n";
$disHTML .= "  function fixEvent(event) {\n";
$disHTML .= "     if (!event) event = window.event\n";
$disHTML .= "     if (event.target) {\n";
$disHTML .= "        if (event.target.nodeType == 3) event.target = event.target.parentNode\n";
$disHTML .= "     } else if (event.srcElement) {\n";
$disHTML .= "        event.target = event.srcElement\n";
$disHTML .= "     }\n";
$disHTML .= "     return event\n";
$disHTML .= "  }\n";
$disHTML .= "}\n";

$disHTML .= "function show_opts() {\n";
$disHTML .= "	toggleid('areas_opts')\n";
$disHTML .= "}\n";


$disHTML .= "//-->\n";
$disHTML .= "</script>\n";

$disHTML .= "<style type=\"text/css\">\n";
$disHTML .= "<!--\n";
$disHTML .= "\n";
$disHTML .= "#zclass { font-family: \"Courier New\", Courier, mono; font-size: 11px; font-weight: bold; text-align: right; }\n";
$disHTML .= ".style8 {color: #D70000}\n";
$disHTML .= ".tfieldlong {\n";
$disHTML .= "  font-family: verdana, arial, helvetica, sans-serif;\n";
$disHTML .= "  font-size: 10px;\n";
$disHTML .= "  height: 65px;\n";
$disHTML .= "  width: 500px;\n";
$disHTML .= "  border: thin solid #000000;\n";
$disHTML .= "}\n";
$disHTML .= ".fsub_col {\n";
$disHTML .= "font-family: verdana, arial, helvetica, sans-serif;\n";
$disHTML .= "font-size: 10px;\n";
$disHTML .= "font-weight: bold;\n";
$disHTML .= "padding: 2px;\n";
$disHTML .= "border: 1px solid #B5B5B5;\n";
$disHTML .= "border-style: solid solid solid solid;\n";
$disHTML .= "color: #000000;\n";
$disHTML .= "background: #E7EFF5;\n";
$disHTML .= "}\n";
$disHTML .= "td.fsub_border {\n";
$disHTML .= "border: 1px solid #B5B5B5;\n";
$disHTML .= "border-style: none none solid solid;\n";
$disHTML .= "}\n";
$disHTML .= "td.fsub_border1 {\n";
$disHTML .= "border: 1px solid #B5B5B5;\n";
$disHTML .= "border-style: none solid solid none;\n";
$disHTML .= "}\n";
$disHTML .= ".tboxjoe {\n";
$disHTML .= "  font-family: verdana, arial, helvetica, sans-serif;\n";
$disHTML .= "  font-size: 10px;\n";
$disHTML .= "  background-color: #CCCCCC;\n";
$disHTML .= "  border: #000000;\n";
$disHTML .= "  border-style: solid;\n";
$disHTML .= "  border-top-width: thin;\n";
$disHTML .= "  border-right-width: thin;\n";
$disHTML .= "  border-bottom-width: thin;\n";
$disHTML .= "  border-left-width: thin;\n";
$disHTML .= "  height: 100px;\n";
$disHTML .= "  width: 500px;\n";
$disHTML .= "}\n";
$disHTML .= ".style9 {\n";
$disHTML .= "  font-family: Arial;\n";
$disHTML .= "  font-weight: bold;\n";
$disHTML .= "  font-size: smaller; color: orange;\n";
$disHTML .= "}\n";
$disHTML .= ".style13 {font-size: 12px; font-weight: bold; }\n";
$disHTML .= ".style14 {font-size: 9pt}\n";

$disHTML .= ".slideshow {\n";
$disHTML .= "  list-style-type: none;\n";
$disHTML .= "  margin: 0px;\n";
$disHTML .= "  padding: 0px;\n";
$disHTML .= "}\n";
$disHTML .= "\n";
$disHTML .= ".slide {\n";
$disHTML .= "  position: relative;\n";
$disHTML .= "  float: left;\n";
$disHTML .= "  width: 172px;\n";
$disHTML .= "  margin-bottom: 10px;\n";
$disHTML .= "  margin-right: 10px;\n";
$disHTML .= "}\n";
$disHTML .= "\n";
$disHTML .= ".slide div.thumb {\n";
$disHTML .= "  background: #fff;\n";
$disHTML .= "  width: 170px;\n";
$disHTML .= "  height: 120px;\n";
$disHTML .= "  border: 1px solid #000;\n";
$disHTML .= "  font-size: 5px;\n";
$disHTML .= "  font-family: \"Times New Roman\", serif;\n";
$disHTML .= "  overflow: hidden;\n";
$disHTML .= "}\n";
$disHTML .= "\n";
$disHTML .= ".slide .view {\n";
$disHTML .= "  padding: 2px 2px;\n";
$disHTML .= "  margin: 2px 0px;\n";
$disHTML .= "  cursor: text;\n";
$disHTML .= "  border-width: 1px;\n";
$disHTML .= "  border-style: solid;\n";
$disHTML .= "  border-color: #ccc;\n";
$disHTML .= "  background-color: #eee;\n";
$disHTML .= "  height: 1em;\n";
$disHTML .= "}\n";
$disHTML .= ".view:hover {\n";
$disHTML .= "  background-color: #ffffcc;\n";
$disHTML .= "}\n";
$disHTML .= ".view, .inplace, #list5 input {\n";
$disHTML .= "  font-size: 14px;\n";
$disHTML .= "  font-family: sans-serif;\n";
$disHTML .= "}\n";
$disHTML .= "\n";
$disHTML .= ".inplace {\n";
$disHTML .= "  position: absolute;\n";
$disHTML .= "  height: 30px;\n";
$disHTML .= "  display: none;\n";
$disHTML .= "  z-index: 10000;\n";
$disHTML .= "  font: 10px verdana;\n";
$disHTML .= "  width: 150px;\n";
$disHTML .= "}\n";
$disHTML .= ".inplace, #list5 input:hover, #list5 input:focus {\n";
$disHTML .= "  background-color: #ffffcc;\n";
$disHTML .= "}\n";
$disHTML .= "#slideEditors input.inplace {\n";
$disHTML .= "  width: 12em;\n";
$disHTML .= "  max-width: 12em;\n";
$disHTML .= "  margin-left: 1px;\n";
$disHTML .= "}\n";
$disHTML .= "#slideEditors input.inplace, #slideshow .view {\n";
$disHTML .= "  text-align: center;\n";
$disHTML .= "}\n";
$disHTML .= "\n";
$disHTML .= "#paragraphView, #paragraphEdit, #markupView, #markupEdit {\n";
$disHTML .= "  font-family: \"Times New Roman\", serif;\n";
$disHTML .= "  font-size: 14px;\n";
$disHTML .= "}\n";
$disHTML .= "#paragraphView, #markupView {\n";
$disHTML .= "  border: 1px solid #fff;\n";
$disHTML .= "  padding: 8px;\n";
$disHTML .= "  width: 400px;\n";
$disHTML .= "  max-width: 400px;\n";
$disHTML .= "}\n";
$disHTML .= "#paragraphView:hover, #markupView:hover {\n";
$disHTML .= "  background-color: #ffffcc;\n";
$disHTML .= "  border-color: #ccc;\n";
$disHTML .= "}\n";
$disHTML .= "#paragraphEdit, #markupEdit {\n";
$disHTML .= "  width: 315px;\n";
$disHTML .= "  background-color: #ffffcc;\n";
$disHTML .= "}\n";
$disHTML .= "#paragraphEdit {\n";
$disHTML .= "  height: 5em;\n";
$disHTML .= "}\n";
$disHTML .= "#markupEdit {\n";
$disHTML .= "  height: 15em;\n";
$disHTML .= "}\n";
$disHTML .= "\n";
$disHTML .= "#listExamples td {\n";
$disHTML .= "  width: 9em;\n";
$disHTML .= "  margin-right: 20px; \n";
$disHTML .= "  padding: 0px 20px;\n";
$disHTML .= "  vertical-align: top;\n";
$disHTML .= "}\n";
$disHTML .= "#listExamples th {\n";
$disHTML .= "  vertical-align: bottom;\n";
$disHTML .= "  font-weight: normal;\n";
$disHTML .= "  font-size: 14px;\n";
$disHTML .= "  padding-top: 20px;\n";
$disHTML .= "}\n";
$disHTML .= "#listExamples td.caption {\n";
$disHTML .= "  font-size: 12px;\n";
$disHTML .= "  text-align: center;\n";
$disHTML .= "}\n";
$disHTML .= "#listExamples li {\n";
$disHTML .= "  padding: 0px;\n";
$disHTML .= "  height: 20px;\n";
$disHTML .= "  min-height: 1em;\n";
$disHTML .= "  width: 120px;\n";
$disHTML .= "}\n";
$disHTML .= "#listExamples li .view {\n";
$disHTML .= "  height: 16px;\n";
$disHTML .= "  vertical-align: middle;\n";
$disHTML .= "  padding: 2px;\n";
$disHTML .= "}\n";
$disHTML .= "#list1 li:hover {\n";
$disHTML .= "  background-color: #eee;\n";
$disHTML .= "}\n";
$disHTML .= "#listExamples input.inplace {\n";
$disHTML .= "  width: 220px;\n";
$disHTML .= "  max-width: 120px;\n";
$disHTML .= "}\n";
$disHTML .= "\n";
$disHTML .= "/* BugFix: Firefox: avoid bottom margin on draggable elements */\n";
$disHTML .= "#listExamples #list4, #listExamples #list5 { margin-top: -2px; }\n";
$disHTML .= "#listExamples #list4 li, #listExamples #list5 li { margin-top: 4px; }\n";
$disHTML .= "\n";
$disHTML .= "#listExamples #list4 li { cursor: default; }\n";
$disHTML .= "#listExamples #list4 .handle,\n";
$disHTML .= "#listExamples #list5 .handle {\n";
$disHTML .= "  float: right;\n";
$disHTML .= "  background-color: #ccc;\n";
$disHTML .= "  background-image: url(common/handle.png);\n";
$disHTML .= "  background-repeat: repeat-y;\n";
$disHTML .= "  width: 7px;\n";
$disHTML .= "  height: 20px;\n";
$disHTML .= "}\n";
$disHTML .= "#listExamples #list4 li .view {\n";
$disHTML .= "  cursor: text;\n";
$disHTML .= "}\n";
$disHTML .= "#listExamples #list4Editors input.inplace, #listExamples #list5 input {\n";
$disHTML .= "  width: 104px;\n";
$disHTML .= "  max-width: 104px;\n";
$disHTML .= "}\n";
$disHTML .= "#listExamples #list4Editors>input.inplace, #listExamples #list5>li>input {\n";
$disHTML .= "  width: 111px;\n";
$disHTML .= "  max-width: 111px;\n";
$disHTML .= "}\n";
$disHTML .= "#list5 input {\n";
$disHTML .= "  background-color: #eee;\n";
$disHTML .= "}\n";
$disHTML .= ".inplace, #list5 input {\n";
$disHTML .= "  background-color: #fff;\n";
$disHTML .= "  margin: 0px;\n";
$disHTML .= "  padding-left: 1px;\n";
$disHTML .= "}\n";
$disHTML .= ".handle {\n";
$disHTML .= "  cursor: move;\n";
$disHTML .= "}\n";

$disHTML .= "-->\n";
$disHTML .= "</style>\n";

//$filename = "http://joe.soholaunch.com/sohoadmin/program/modules/site_templates/pages/SPORTS-Snowboard1-486";
$filename = "http://".$this_ip."/sohoadmin/program/modules/site_templates/pages/CORPORATE-A_Curvacious_Mark-Blue_Gray/index.html";

ob_start(); 
	include("$filename");
	$THIS_HTML = ob_get_contents(); 
ob_end_clean();


$THIS_HTML = eregi_replace("src=\"", "src=\"../site_templates/pages/CORPORATE-A_Curvacious_Mark-Blue_Gray/", $THIS_HTML);
$THIS_HTML = eregi_replace("background=\"", "background=\"../site_templates/pages/CORPORATE-A_Curvacious_Mark-Blue_Gray/", $THIS_HTML);
$THIS_HTML = eregi_replace("href=\"", "href=\"../site_templates/pages/CORPORATE-A_Curvacious_Mark-Blue_Gray/", $THIS_HTML);

//$THIS_HTML = eregi_replace("#LOGO#", "<TEXTAREA name=\"a3EditORG\" value=\"#LOGO#\" style=\"display: none;\"></TEXTAREA><TEXTAREA id=\"a3Edit\" name=\"a3Edit\" class=\"inplace\"></TEXTAREA><div id=\"a3View\" class=\"view text\" style=\"border: 1px solid red;\" onMouseOut=\"toggleid('areas_opts')\" onMouseOver=\"toggleid('areas_opts')\">#LOGO#</div>", $THIS_HTML);
//$THIS_HTML = eregi_replace("#SLOGAN#", "<TEXTAREA name=\"b3EditORG\" value=\"#SLOGAN#\" style=\"display: none;\"></TEXTAREA><TEXTAREA id=\"b3Edit\" name=\"b3Edit\" class=\"inplace\"></TEXTAREA><div id=\"b3View\" class=\"view text\" style=\"width: 200px; border: 1px solid red;\">#SLOGAN#</div>", $THIS_HTML);
//$THIS_HTML = eregi_replace("#LALA#", "<input type=\"hidden\" name=\"b3EditORG\" value=\"#LOGO#\"><input id=\"b3Edit\" name=\"b3Edit\" class=\"inplace\"><div id=\"b3View\" class=\"view text\" style=\"border: 1px solid red;\">#LOGO#</div>", $THIS_HTML);
//$THIS_HTML = eregi_replace("#BOX3#", "<input type=\"hidden\" name=\"c3EditORG\" value=\"#BOX3#\"><input id=\"c3Edit\" name=\"c3Edit\" class=\"inplace\"><div id=\"c3View\" class=\"view text\" style=\"border: 1px solid red;\">#BOX3#</div>", $THIS_HTML);

$THIS_HTML = eregi_replace("#LOGO#", "<div onMouseOut=\"toggleid('areas_opts')\" onMouseOver=\"toggleid('areas_opts')\">#LOGO#</div>", $THIS_HTML);
$THIS_HTML = eregi_replace("#SLOGAN#", "<div onMouseOut=\"toggleid('areas_opts')\" onMouseOver=\"toggleid('areas_opts')\">#SLOGAN#</div>", $THIS_HTML);
$THIS_HTML = eregi_replace("#CONTENT#", "<div onMouseOut=\"toggleid('areas_opts')\" onMouseOver=\"toggleid('areas_opts')\">#CONTENT#</div>", $THIS_HTML);
$THIS_HTML = eregi_replace("#TMENU#", "<div onMouseOut=\"toggleid('areas_opts')\" onMouseOver=\"toggleid('areas_opts')\">#TMENU#</div>", $THIS_HTML);
$THIS_HTML = eregi_replace("#VMENU#", "<div onMouseOut=\"toggleid('areas_opts')\" onMouseOver=\"toggleid('areas_opts')\">#VMENU#</div>", $THIS_HTML);
$THIS_HTML = eregi_replace("#COPYRIGHT#", "<div onMouseOut=\"toggleid('areas_opts')\" onMouseOver=\"toggleid('areas_opts')\">#COPYRIGHT#</div>", $THIS_HTML);



echo $disHTML;

//$disHTML .= "               <input type=\"hidden\" name=\"".$k."3EditORG\" value=\"".$gbak['CAT_NAME']."\">\n";
//$disHTML .= "               <input id=\"".$k."3Edit\" name=\"".$k."3Edit\" class=\"inplace\">\n";
//$disHTML .= "               <td class=\"text\" align=\"left\" style=\"".$rowStyle."\"><div id=\"".$k."3View\" class=\"view text\">".$gbak['CAT_NAME']."</div></td>\n";

echo "Mouse over different areas of the template to see which areas are editable.  When you are ready to edit, click the area that you would like to modify<br/>";
?>

<script language="JavaScript">

//function show_opts() {
//	toggleid('areas_opts');
//}




</script>

<?

echo $THIS_HTML;

echo "<div id=\"areas_opts\" style=\"display: none; width: 250px; height: 75px; border: 1px solid grey;\">\n";

echo "<div id=\"something\">Joes Nugget</div>\n";

echo "</div>\n";


//include("index.html");

?>

<script language="javascript">

//alert(document.images[0].width);

var ie=document.all;
var nn6=document.getElementById&&!document.all;

var isdrag=false;
var x,y;
var dobj;

function selectmouse()
{
  endX='';
  endY='';
  var fobj       = nn6 ? document.getElementById("top") : alert('ie');
  var topelement = nn6 ? "HTML" : "BODY";
  
  alert(fobj)

  while (fobj.tagName = topelement && fobj.className != "editTable")
  {
  	alert('seomting');
    fobj = nn6 ? fobj.parentNode : fobj.parentElement;
  }

	//access some <ul> element
	var mylist=document.getElementsByTagName("DIV")
	alert(mylist.childNodes.length)
	for (i=0; i<mylist.childNodes.length; i++){
		//alert(mylist.childNodes[i].id)
		if (mylist.childNodes[i].nodeName=="DIV"){
			alert(mylist.childNodes[i].id)
		}
	}
}

function loadTemplate(){
	eval ("var result = MM_openBrWindow('loadEditor.php','blogEdit','width=790, height=520');");
}


</script>


<div id="top">
	<div id="mid1"></div>
	<div id="mid2"></div>
	<div id="mid3">
		<div id="bottom1"></div>
		<div id="bottom2"></div>
	</div>
</div>
<input id="joe" onClick="selectmouse()" type="button" value=" click me " />

