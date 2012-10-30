// ------------------------------------------------------------------
// -- START DRAG AND DROP ROUTINES
// ------------------------------------------------------------------

function createCookie(name,value,days)
{
	if (days)
	{
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
	}
	else var expires = "";
	document.cookie = name+"="+value+expires+"; path=/";
}

function readCookie(name)
{
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for(var i=0;i < ca.length;i++)
	{
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1,c.length);
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
	}
	return null;
}


<?
# place_sitepal()
include_once("../sitepal/page_editor/props_dialog-javascript-ff.php");
?>


// =========================================================
// =========== If Drop Object is a New Text Area ===========
// =========================================================

function loadEditor(mode) {

   if(document.getElementById('remember').checked){
      createCookie('editorMode',mode,90);
//      alert('Setting saved!  To reset this option, go to webmaster and click Clear Editor Mode');
   }
   document.getElementById('chooseMode').style.display='none';
   document.getElementById('remember').checked=false;

   d = new Date();
	RandNum = "NEWOBJ";
	RandNum += d.getUTCHours();
	RandNum += d.getUTCMinutes();
	RandNum += d.getUTCSeconds();
	RandNum += d.getUTCMilliseconds();
	RandNum = RandNum.toString();

   var dataTrue = dataData.search("pixel.gif");
   var TableStart = "<table border=0 cellpadding=1 cellspacing=0 style='border: 1px inset black; background: #EFEFEF;'><tr><td width=199 height=100% align=center valign=top>";
   var TableEnd = "</td></tr></table><!-- ~~~ -->";
   TextHeader = "<img src=images/text_header.gif width=199 height=15 border=0 align=center vspace=0 hspace=0 style='cursor: move;'><BR CLEAR=ALL>";

   if (dataTrue > 0) {
      //RandNumJoe = "12345";
   	sText = TableStart+TextHeader+"<div ID="+RandNum+" class=TXTCLASS align=left onclick=\"newEdit("+RandNum+",'"+RandNum+"');\"></div>"+TableEnd;
   	doOperation = 1;
      document.getElementById(ColRowID).innerHTML= sText;
   	<?
   	$joe_page = $this_ip."/media/";
   	//echo("eval (\"var result = windowload('../editor/pinEdit.php?url=http://".$joe_page."test.html?curtext=\"+RandNum+\"&dotcom=".$dis_site."&=SID','newEditor',780,455,this);\");\n");
   	//echo("eval (\"var result = loadwindow('../loadEditor.php?curtext=\"+RandNum+\"&dotcom=".$dis_site."&=SID','785','430','newEditor');\");\n");
   	echo("eval (\"var result = MM_openBrWindow('../loadEditor.php?curtext=\"+RandNum+\"&type=\"+mode+\"&dotcom=".$dis_site."&=SID','newEditor','width=790, height=520');\");\n");
   	?>
   } else {
   	sText = dataData+"<BR>"+TableStart+TextHeader+"<div ID="+RandNum+" class=TXTCLASS align=left onclick=\"newEdit("+RandNum+",'"+RandNum+"');\"></div>"+TableEnd;
   	doOperation = 1;
   	document.getElementById(ColRowID).innerHTML= sText;
      <?
      //echo("eval (\"var result = loadwindow('../loadEditor.php?curtext=\"+RandNum+\"&dotcom=".$dis_site."&=SID','780','450','newEditor');\");\n");
      echo("eval (\"var result = MM_openBrWindow('../loadEditor.php?curtext=\"+RandNum+\"&type=\"+mode+\"&dotcom=".$dis_site."&=SID','newEditor','width=790, height=520');\");\n");
      ?>
   }
}

<?

// =========================================================
echo "	function getHtml(curtext){\n";
//echo "      alert(curtext);\n";
echo "	   var html = document.getElementById(curtext).innerHTML;\n";
echo "	return html; }\n";

echo "	function setHtml(curtext,cont){\n";
//echo "      alert('('+cont+')');\n";
echo "	   document.getElementById(curtext).innerHTML=cont;\n";
echo "      var RawCode = document.getElementById(curtext).innerHTML;\n";
//echo "      alert(RawCode);\n";
echo "      var billy = document.getElementById(curtext).innerHTML='<blink>'+RawCode+'</blink>';\n";
echo "		disable_links()\n";
//echo "      alert(billy);\n";
echo "}\n";

echo "	function setHtmlfirst(cont){\n";
//echo "   alert(cont);\n";
echo "	   document.getElementById(random).innerHTML= cont\n";
echo "		disable_links()\n";
echo "}\n";

echo "div_window();\n";

echo "	function newEdit(curId,textId){\n";
echo "      AcurId=curId;\n";
echo "      AtextId=textId;\n";
echo "      var cook = readCookie('editorMode');\n";
echo "      if(cook){\n";
echo "		   eval (\"var result = MM_openBrWindow('../loadEditor.php?curtext=\"+textId+\"&type=\"+cook+\"&dotcom=".$dis_site."&=SID','newEditor','width=790, height=520');\");\n";
echo "      }else{\n";
echo "         document.getElementById('chooseMode2').style.display='block';\n";
echo "      }\n";
echo "	}\n";

echo "	function textEdit(curId,textId){\n";
echo "      AcurId=curId;\n";
echo "      AtextId=textId;\n";
echo "      var cook = readCookie('editorMode');\n";
echo "      if(cook){\n";
echo "		   eval (\"var result = MM_openBrWindow('../loadEditor.php?curtext=\"+textId+\"&type=\"+cook+\"&dotcom=".$dis_site."&=SID','newEditor','width=790, height=520');\");\n";
echo "      }else{\n";
echo "         document.getElementById('chooseMode2').style.display='block';\n";
echo "      }\n";
echo "	}\n";

echo "   function loadDis(disMode){\n";
echo "      if(document.getElementById('remember2').checked){\n";
echo "         createCookie('editorMode',disMode,90);\n";
echo "         alert('Setting saved!  To reset this option, go to webmaster and click Clear Editor Mode');\n";
echo "      }\n";
echo "      document.getElementById('chooseMode2').style.display='none';\n";
echo "      document.getElementById('remember2').checked=false;\n";
echo "      var curId = AcurId;\n";
echo "      var textId = AtextId;\n";
echo "		eval (\"var result = MM_openBrWindow('../loadEditor.php?curtext=\"+textId+\"&type=\"+disMode+\"&dotcom=".$dis_site."&=SID','newEditor','width=790, height=520');\");\n";
echo "	}\n";

//echo "	function textEdit(curId,textId){\n";
////echo "		eval (\"var result = loadwindow('../loadEditor.php?curtext=\"+textId+\"&dotcom=".$dis_site."&=SID','790','450','newEditor');\");\n";
//echo "		eval (\"var result = MM_openBrWindow('../loadEditor.php?curtext=\"+textId+\"&dotcom=".$dis_site."&=SID','newEditor','width=830, height=550');\");\n";
//echo "	}\n";

//echo "	function textEdit(curId,textId){\n";
////echo "		eval (\"var result = loadwindow('../loadEditor.php?curtext=\"+textId+\"&dotcom=".$dis_site."&=SID','790','450','newEditor');\");\n";
//echo "		eval (\"var result = MM_openBrWindow('../loadEditor.php?curtext=\"+textId+\"&dotcom=".$dis_site."&=SID','newEditor','width=830, height=550');\");\n";
//echo "	}\n";

?>

// =========================================================




// =========================================================

function moveThis() {
   var CurCellCont = document.getElementById(thisObj).innerHTML;
   document.getElementById(ColRowID).innerHTML= CurCellCont;
}

// =========================================================

function clearCell() {
   document.getElementById(ColRowID).innerHTML= '<IMG height=50% src=pixel.gif width=199 border=0>';
   document.getElementById(ColRowID).style.backgroundColor= "#FFFFFF";
}

	// =========================================================
	// =============== If Drop Object is Date Stamp ============
	// =========================================================

function OKdateStamp() {
   var dataTrue = dataData.search("pixel.gif");
	var TableStart = "<table border=0 cellpadding=1 cellspacing=0 style='border: 1px inset black; background: #EFEFEF;'><tr><td width=199 align=center valign=top>";
   var TableEnd = "</td></tr></table><!-- ~~~ -->";

   TextHeader = "<img src=images/text_header.gif width=199 height=15 border=0 align=left vspace=0 hspace=0 style='cursor: move;'><BR CLEAR=ALL>";
	if (dataTrue > 0) {

//		ColumnDrop = ColRow;
//		Remember = curId;

		var months=new Array(13);
		months[1]="January";
		months[2]="February";
		months[3]="March";
		months[4]="April";
		months[5]="May";
		months[6]="June";
		months[7]="July";
		months[8]="August";
		months[9]="September";
		months[10]="October";
		months[11]="November";
		months[12]="December";
		var time=new Date();
		var lmonth=months[time.getMonth() + 1];
		var date=time.getDate();
		var year=time.getYear();
		if (year < 2000) year = year + 1900;
		sText = TableStart+TextHeader+"<div ID=DATESTAMP align=center><font face=\"Verdana, Arial, Helvetica, Sans-serif\" style=\"font-size: 11px;\">"+lmonth+" "+date+", "+year+"</font></div><!-- ##DATESTAMP## -->"+TableEnd;
		doOperation = 1;
	} else {
//		ColumnDrop = ColRow;
//		Remember = curId;
		var months=new Array(13);
		months[1]="January";
		months[2]="February";
		months[3]="March";
		months[4]="April";
		months[5]="May";
		months[6]="June";
		months[7]="July";
		months[8]="August";
		months[9]="September";
		months[10]="October";
		months[11]="November";
		months[12]="December";
		var time=new Date();
		var lmonth=months[time.getMonth() + 1];
		var date=time.getDate();
		var year=time.getYear();
		if (year < 2000) year = year + 1900;
		sText = dataData+"<BR>"+TableStart+TextHeader+"<div ID=DATESTAMP align=center><font face=Arial style='font-size: 8pt;'>"+lmonth+" "+date+", "+year+"</font></div><!-- ##DATESTAMP## -->"+TableEnd;
		doOperation = 1;
	}
    if (doOperation == 1) {
	    document.getElementById(ColRowID).innerHTML= sText;
	    document.getElementById(ColRowID).style.backgroundColor= "#FFFFFF";
    }
}


// =========================================================

function getCurrentTrue() {
   var curTrue = document.getElementById(ColRowID).innerHTML.search("pixel.gif");
return curTrue }

function getCurrentCont() {
   var curCont = document.getElementById(ColRowID).innerHTML;
return curCont }


// =========================================================

function ckFunt(disTing) {
   document.getElementById(ColRowID).innerHTML= disTing;
   document.getElementById(ColRowID).style.backgroundColor= "#FFFFFF";
}

// =========================================================

function cellWhite() {
   document.getElementById(ColRowID).style.backgroundColor= "#FFFFFF";
}

function makeScroll(ColRowID) {
   document.getElementById('SU'+ColRowID).style.display='block';
   document.getElementById('SD'+ColRowID).style.display='block';
}

function makeUnScroll(ColRowID) {
   var stillFull = document.getElementById(ColRowID).innerHTML;
   var yesNo = stillFull.search("pixel.gif");
   if(yesNo > 0) {
      document.getElementById('SU'+ColRowID).style.display='none';
      document.getElementById('SD'+ColRowID).style.display='none';
   }
}

// =========================================================

function linkImage(imagePass) {
	tempImage = imagePass;
	show_hide_layer('objectbar','','hide','imageLink','','show');
}

// =========================================================

function inputImageLink() {
	var oTemp = saveForm.innerHTML;
   disOne = imagePageLink.selectedIndex;
	var sText = eval("imagePageLink.options["+disOne+"].value");

	if (sText == "NONE") {
		var sText = imageUrlLink.value;
	}
	if (sText == "http://") {
		var sText = "mailto:"+emailImageLink.value;
	}
	if (sText == "mailto:") {
		var sText = "#";
	}
	var oTemp = oTemp + "<input type=hidden name=PICLINK"+tempImage+" value=\""+tempImage+"PICLINK"+sText+"\">";

	if (sText != "#") {
		saveForm.innerHTML = oTemp;
	}

	imagePageLink.selectedIndex = 0;
	imageUrlLink.value = "http://";
	emailImageLink.value = "";
}

// =========================================================

function getImageData() {
	doOperation = 0;
   var sText = "";

   var dataTrue = dataData.search("pixel.gif");

   disOne = oSel.selectedIndex;
	tImage = eval("oSel.options["+disOne+"].value");

	var TableStart = "<table border=0 cellpadding=0 cellspacing=1><tr><td width=199 height=75 align=center valign=middle>";
   var TableEnd = "</td></tr></table>";


	if (tImage != "NONE") {
   	<? echo ("tImage = \"http://$this_ip/images/\" + tImage;\n"); ?>
   	if (dataTrue > 0) {
   		sText = "<img src=pixel.gif width=199 height=1 border=0>"+TableStart+"<img src="+tImage+" border=1 class=tHead>"+TableEnd;
   		doOperation = 1;
   	} else {
   		sText = dataData+"<img src=pixel.gif width=199 height=1 border=0>"+TableStart+"<img src="+tImage+" border=1 class=tHead>"+TableEnd;
   		doOperation = 1;
   	}
	}
	if (doOperation == 1) {
		document.getElementById(ColRowID).innerHTML= sText;
	}
}

// =========================================================

function OkImageData() {
	d = new Date();
	RandNum = "IMGOBJ";
	RandNum += d.getUTCHours();
	RandNum += d.getUTCMinutes();
	RandNum += d.getUTCSeconds();
	RandNum += d.getUTCMilliseconds();
	RandNum = RandNum.toString();

	doOperation = 0;
   disOne = oSel.selectedIndex;
	tImage = eval("oSel.options["+disOne+"].value");

	var TableStart = "<table border=0 cellpadding=1 cellspacing=0 style='border: 1px inset black; background: #EFEFEF;'><tr><td width=199 align=center valign=top>";
   var TableEnd = "</td></tr></table><!-- ~~~ -->";
   TextHeader = "<img name=MYmovingCell src=images/text_header.gif width=199 height=15 border=0 align=left vspace=0 hspace=0 style='cursor: move;'><BR CLEAR=ALL>";
   var dataTrue = dataData.search("pixel.gif");
   sText = "<img src=pixel.gif width=199 height=1 border=0>"

   var InObj = dataData;

	if (tImage != "NONE") {

	<? echo ("var myImgMan = \"http://$this_ip/images/\" + tImage;\n"); ?>
	tImage = myImgMan;
	if (dataTrue > 0) {
		sText = TableStart+TextHeader+"<!-- ##IMAGE;"+tImage+";"+RandNum+"## --><img src="+tImage+" border=0 id="+RandNum+" class=tHead onClick=\"linkImage('"+RandNum+"');\">"+TableEnd;
		doOperation = 1;
	} else {
		sText = dataData+"<BR>"+TableStart+TextHeader+"<!-- ##IMAGE;"+tImage+";"+RandNum+"## --><img src="+tImage+" border=0 id="+RandNum+" class=tHead onClick=\"linkImage('"+RandNum+"');\">"+TableEnd;
		doOperation = 1;
	}
	}

	if (doOperation == 1) {
		document.getElementById(ColRowID).innerHTML= sText;
		document.getElementById(ColRowID).style.backgroundColor= "#FFFFFF";
	}

	oSel.selectedIndex = 0;
}

// =========================================================

function getImageDataUP(tImage) {
	doOperation = 0;

	TableStart = "<table border=0 cellpadding=0 cellspacing=1><tr><td width=199 height=75 align=center valign=middle>";
  	TableEnd = "</td></tr></table>";
   var dataTrue = dataData.search("pixel.gif");
  	sText = "<img src=pixel.gif width=199 height=100% border=0>";

	if (tImage != "NONE") {
	<? echo ("tImage = \"http://$this_ip/images/\" + tImage;\n"); ?>
	if (dataTrue > 0) {
		sText = "<img src=pixel.gif width=199 height=1 border=0>"+TableStart+"<img src="+tImage+" border=1 class=tHead>"+TableEnd;
		doOperation = 1;
	} else {
		sText = dataData+"<img src=pixel.gif width=199 height=1 border=0>"+TableStart+"<img src="+tImage+" border=1 class=tHead>"+TableEnd;
		doOperation = 1;
	}
	}
	if (doOperation == 1) {
		document.getElementById(ColRowID).innerHTML= sText;
		document.getElementById(ColRowID).style.backgroundColor= "#FFFFFF";
	}
}

// =========================================================

function OkImageDataUP(tImage) {
	d = new Date();
	RandNum = "IMGOBJ";
	RandNum += d.getUTCHours();
	RandNum += d.getUTCMinutes();
	RandNum += d.getUTCSeconds();
	RandNum += d.getUTCMilliseconds();
	RandNum = RandNum.toString();

	doOperation = 0;
   var dataTrue = dataData.search("pixel.gif");
	var TableStart = "<table border=0 cellpadding=1 cellspacing=0 style='border: 1px inset black; background: #EFEFEF;'><tr><td width=199 align=center valign=top>";
   var TableEnd = "</td></tr></table><!-- ~~~ -->";
   TextHeader = "<img src=images/text_header.gif width=199 height=15 border=0 align=left vspace=0 hspace=0 style='cursor: move;'><BR CLEAR=ALL>";

   sText = "<img src=pixel.gif width=199 height=100% border=0>";
   var InObj = dataData;

	if (tImage != "NONE") {

	<? echo ("var myImgMan = \"http://$this_ip/images/\" + tImage;\n"); ?>
	tImage = myImgMan;
	if (dataTrue > 0) {
		sText = TableStart+TextHeader+"<!-- ##IMAGE;"+tImage+";"+RandNum+"## --><img src="+tImage+" border=0 id="+RandNum+" class=tHead onClick=\"linkImage('"+RandNum+"');\">"+TableEnd;
		doOperation = 1;
	} else {
		sText = dataData+"<BR>"+TableStart+TextHeader+"<!-- ##IMAGE;"+tImage+";"+RandNum+"## --><img src="+tImage+" border=0 id="+RandNum+" class=tHead onClick=\"linkImage('"+RandNum+"');\">"+TableEnd;
		doOperation = 1;
	}
	}

	if (doOperation == 1) {
		document.getElementById(ColRowID).innerHTML= sText;
		document.getElementById(ColRowID).style.backgroundColor= "#FFFFFF";
	}


}

// =========================================================

function OkMapquestData() {
   var dataTrue = dataData.search("pixel.gif");

	d = new Date();
	RandNum = "MAP";
	RandNum += d.getUTCHours();
	RandNum += d.getUTCMinutes();
	RandNum += d.getUTCSeconds();
	RandNum += d.getUTCMilliseconds();
	RandNum = RandNum.toString();
	doOperation = 0;

	tStreet = street.value;
	tCity = city.value;
	tState = state.value;
	tZip = zip.value;

	tStreet = tStreet.toString();
	tCity = tCity.toString();
	tState = tState.toString();
	tZip = tZip.toString();

	if (MAPLINKTOquest.checked) { var tMapLink = "MAPQUEST"; }
	if (MAPLINKTOyahoo.checked) { var tMapLink = "YAHOO"; }

	displayAddr = "<br><FONT COLOR=MAROON>("+tStreet+","+tCity+","+tState+","+tZip+")</FONT>";

   TextHeader = "<img src=images/text_header.gif width=199 height=15 border=0 align=left vspace=0 hspace=0 style='cursor: move;'><BR CLEAR=ALL>";
	TableStart = "<table border=0 cellpadding=2 cellspacing=0 style='border: 1px inset black; background: #EFEFEF;'><tr><td width=199 align=center valign=top>";
    TableEnd = "</td></tr></table><!-- ~~~ -->";

	if (tStreet != "") {
		if (dataTrue > 0) {
		sText = TableStart+TextHeader+"<input type=button value=' Get Directions ' id="+RandNum+" style='cursor: hand;'><BR><FONT STYLE='font-family: Arial; font-size: 7pt; color: #999999;'>Courtesy of: "+tMapLink+"<SUP>TM</SUP>"+displayAddr+"<!-- ##MAPQUEST;"+tStreet+";"+tCity+";"+tState+";"+tZip+";"+tMapLink+"## -->"+TableEnd;
		doOperation = 1;
		} else {
		sText = dataData+"<BR>"+TableStart+TextHeader+"<input type=button value=' Get Directions ' id="+RandNum+"  style='cursor: hand;'><FONT STYLE='font-family: Arial; font-size: 7pt; color: #999999;'>Courtesy of: "+tMapLink+"<SUP>TM</SUP>"+displayAddr+"<!-- ##MAPQUEST;"+tStreet+";"+tCity+";"+tState+";"+tZip+";"+tMapLink+"## -->"+TableEnd;
		doOperation = 1;
		}
	} else {
		LowerDropZone();
		doOperation = 0;
	}

	if (doOperation == 1) {
		document.getElementById(ColRowID).innerHTML= sText;
		document.getElementById(ColRowID).style.backgroundColor= "#FFFFFF";
	}
}

// =========================================================

function OkLoginData() {
	doOperation = 0;
	tloginbutton = loginbutton.value;
	tloginbutton = tloginbutton.toString();

   var dataTrue = dataData.search("pixel.gif");

   TextHeader = "<img src=images/text_header.gif width=199 height=15 border=0 align=left vspace=0 hspace=0 style='cursor: move;'><BR CLEAR=ALL>";
   TableStart = "<table border=0 cellpadding=2 cellspacing=0 style='border: 1px inset black; padding: 2px;'><tr><td width=199 align=center valign=top bgcolor=#EFEFEF>";
	TableStart = "<table border=0 cellpadding=2 cellspacing=0 style='border: 1px inset black; padding: 2px;'><tr><td width=199 align=center valign=top bgcolor=#EFEFEF>";
   TableEnd = "</td></tr></table><!-- ~~~ -->";

	if (tloginbutton != "") {
		if (dataTrue > 0) {
			sText = TableStart+TextHeader+"<input type=button value=\""+tloginbutton+"\" id=SECURELOGIN class=FormLt1><BR><BR><FONT FACE=ARIAL STYLE='FONT-SIZE: 7pt;'><b><?php echo lang("Forgotten your password?"); ?> <U><FONT COLOR=BLUE><?php echo lang("Click Here"); ?></FONT></U>.<!-- ##SECURELOGIN;"+tloginbutton+"## -->"+TableEnd;
			doOperation = 1;
		} else {
			sText = dataData+"<BR>"+TableStart+TextHeader+"<input type=button value=\""+tloginbutton+"\" id=SECURELOGIN class=FormLt1><BR><BR><FONT FACE=ARIAL STYLE='FONT-SIZE: 7pt;'><b><?php echo lang("Forgotten your password?"); ?> <U><FONT COLOR=BLUE><?php echo lang("Click Here"); ?></FONT></U>.<!-- ##SECURELOGIN;"+tloginbutton+"## -->"+TableEnd;
			doOperation = 1;
		}
	} else {
		alert("You did not fill in a name for the button.");
		doOperation = 0;
	}

	if (doOperation == 1) {
		document.getElementById(ColRowID).innerHTML= sText;
		document.getElementById(ColRowID).style.backgroundColor= "#FFFFFF";
	}
}

// =========================================================

function OkPDFData() {
	doOperation = 0;

   var dataTrue = dataData.search("pixel.gif");

   disOne = pdfname.selectedIndex;
	tPdf = eval("pdfname.options["+disOne+"].value");

   TextHeader = "<img src=images/text_header.gif width=199 height=15 border=0 align=left vspace=0 hspace=0 style='cursor: move;'><BR CLEAR=ALL>";
	TableStart = "<table border=0 width=199 cellpadding=3 cellspacing=0 style='background: #EFEFEF; border: 1px inset black;'><tr><td width=199 align=center valign=middle>";
   TableEnd = "</td></tr></table><!-- ~~~ -->";

	if (tPdf != "NONE") {

		if (dataTrue > 0) {
		sText = TableStart+TextHeader+"<div ID=PDFFILE align=left><img style='cursor: hand;' src=client/pdf_download.gif width=21 height=22 align=absmiddle hspace=2 border=0><font style='font-family: Arial; font-size: 8pt; color: darkblue;'><U>"+tPdf+"</U></font></div><!-- ##PDF;"+tPdf+"## -->"+TableEnd;
		doOperation = 1;
		} else {
		sText = dataData+"<BR>"+TableStart+TextHeader+"<div ID=PDFFILE align=left><img  style='cursor: hand;' src=client/pdf_download.gif width=21 height=22 align=absmiddle hspace=2 border=0><font style='font-family: Arial; font-size: 8pt; color: darkblue;'><U>"+tPdf+"</U></font></div><!-- ##PDF;"+tPdf+"## -->"+TableEnd;
		doOperation = 1;
		}
	} else {
		LowerDropZone();
		doOperation = 0;
	}
	if (doOperation == 1) {
		document.getElementById(ColRowID).innerHTML= sText;
		document.getElementById(ColRowID).style.backgroundColor= "#FFFFFF";
	}
  	pdfname.selectedIndex = 0;
}

// =========================================================

function OkPDFDataUP(tPdf) {
	doOperation = 0;

   var dataTrue = dataData.search("pixel.gif");

   TextHeader = "<img src=images/text_header.gif width=199 height=15 border=0 align=left vspace=0 hspace=0 style='cursor: move;'><BR CLEAR=ALL>";
	TableStart = "<table border=0 width=199 cellpadding=3 cellspacing=0 style='background: #EFEFEF; border: 1px inset black;'><tr><td width=199 align=center valign=middle>";
   TableEnd = "</td></tr></table><!-- ~~~ -->";

	if (tPdf != "NONE") {

		if (dataTrue > 0) {
		sText = TableStart+TextHeader+"<div ID=PDFFILE align=left><img style='cursor: hand;' src=client/pdf_download.gif width=21 height=22 align=absmiddle hspace=2 border=0><font style='font-family: Arial; font-size: 8pt; color: darkblue;'><U>"+tPdf+"</U></font></div><!-- ##PDF;"+tPdf+"## -->"+TableEnd;
		doOperation = 1;
		} else {
		sText = dataData+"<BR>"+TableStart+TextHeader+"<div ID=PDFFILE align=left><img  style='cursor: hand;' src=client/pdf_download.gif width=21 height=22 align=absmiddle hspace=2 border=0><font style='font-family: Arial; font-size: 8pt; color: darkblue;'><U>"+tPdf+"</U></font></div><!-- ##PDF;"+tPdf+"## -->"+TableEnd;
		doOperation = 1;
		}
	} else {
		LowerDropZone();
		doOperation = 0;
	}
	if (doOperation == 1) {
		document.getElementById(ColRowID).innerHTML= sText;
		document.getElementById(ColRowID).style.backgroundColor= "#FFFFFF";
	}
  	pdfname.selectedIndex = 0;
}

// =========================================================

function OkFlashData(tFlash,tFlashW,tFlashH) {

	var dataTrue = dataData.search("pixel.gif");
	doOperation = 0;

	var istVideo = tFlash.search(";;");
	if(istVideo > 0){
	   tFlash = tFlash.replace(';;',';'+tFlashW+';'+tFlashH);
	}else{
      var disTing = tFlash.indexOf(";");
      tFlash = tFlash.substring(0, disTing);
      tFlash = tFlash.concat(';'+tFlashW+';'+tFlashH);
   }

   TextHeader = "<img src=images/text_header.gif width=199 height=15 border=0 align=left vspace=0 hspace=0 style='cursor: move;'><BR CLEAR=ALL>";
	TableStart = "<table border=0 width=199 cellpadding=2 cellspacing=0 style='border: 1px inset black; bgcolor=#EFEFEF;'><tr><td width=199 align=center valign=top>";
    TableEnd = "</td></tr></table><!-- ~~~ -->";

	if (tFlash != "NONE") {
		if (dataTrue > 0) {
		sText = TableStart+TextHeader+"<div ID=FLASHFILE><font style='font-family: Arial; font-size: 7pt;'><U>FLASH MOVIE DISPLAY</U><BR>The flash movie \"<U>"+tFlash+"</U>\" will be displayed here.</font></div><!-- ##FLASH;"+tFlash+";"+tFlashW+";"+tFlashH+"## -->"+TableEnd;
		doOperation = 1;
		} else {
		sText = dataData+"<BR>"+TableStart+TextHeader+"<div ID=FLASHFILE><font style='font-family: Arial; font-size: 7pt;'><U>FLASH MOVIE DISPLAY</U><BR>The flash movie \"<U>"+tFlash+"</U>\" will play here.</font></div><!-- ##FLASH;"+tFlash+";"+tFlashW+";"+tFlashH+"## -->"+TableEnd;
		doOperation = 1;
		}
	} else {
		LowerDropZone();
		doOperation = 0;
	}

	if (doOperation == 1) {
		document.getElementById(ColRowID).innerHTML= sText;
		document.getElementById(ColRowID).style.backgroundColor= "#FFFFFF";
	}

  	videoname.selectedIndex = 0;
  	closeit();
}

// =========================================================

function OkWordData() {

	doOperation = 0;

   var dataTrue = dataData.search("pixel.gif");

	disOne = mswordname.selectedIndex;
	tWordFile = eval("mswordname.options["+disOne+"].value");

   TextHeader = "<img src=images/text_header.gif width=199 height=15 border=0 align=left vspace=0 hspace=0 style='cursor: move;'><BR CLEAR=ALL>";
	TableStart = "<table border=0 width=199 cellpadding=2 cellspacing=0 style='border: 1px inset black; background: #EFEFEF'><tr><td width=199 align=left valign=top>";
   TableEnd = "</td></tr></table><!-- ~~~ -->";

	if (tWordFile != "NONE") {
		if (dataTrue > 0) {
		sText = TableStart+TextHeader+"<div ID=WORDFILE><img src='client/download_icon.gif' align=absmiddle vspace=0 hspace=2 border=0 width=20 height=19> <font face=Arial style='font-size: 8pt;'><U>"+tWordFile+"</U><font color=#999999> [Size]</font></div><!-- ##MSWORD;"+tWordFile+"## -->"+TableEnd;
		doOperation = 1;
		} else {
		sText = dataData+"<BR>"+TableStart+TextHeader+"<div ID=WORDFILE><img src='client/download_icon.gif' align=absmiddle vspace=0 hspace=2 border=0 width=20 height=19> <font face=Arial style='font-size: 8pt;'><U>"+tWordFile+"</U><font color=#999999> [Size]</font></div><!-- ##MSWORD;"+tWordFile+"## -->"+TableEnd;
		doOperation = 1;
		}
	} else {
		LowerDropZone();
		doOperation = 0;
	}

	if (doOperation == 1) {
		document.getElementById(ColRowID).innerHTML= sText;
		document.getElementById(ColRowID).style.backgroundColor= "#FFFFFF";
	}
  	mswordname.selectedIndex = 0;
}

// =========================================================

function OkWordDataUP(tWordFile) {

	doOperation = 0;
	   var dataTrue = dataData.search("pixel.gif");
   TextHeader = "<img src=images/text_header.gif width=199 height=15 border=0 align=left vspace=0 hspace=0 style='cursor: move;'><BR CLEAR=ALL>";
	TableStart = "<table border=0 width=199 cellpadding=2 cellspacing=0 style='border: 1px inset black; background: #EFEFEF'><tr><td width=199 align=left valign=top>";
  	TableEnd = "</td></tr></table><!-- ~~~ -->";

	if (tWordFile != "NONE") {
		if (dataTrue > 0) {
		sText = TableStart+TextHeader+"<div ID=WORDFILE><img src='client/download_icon.gif' align=absmiddle vspace=0 hspace=2 border=0 width=20 height=19> <font face=Arial style='font-size: 8pt;'><U>"+tWordFile+"</U><font color=#999999> [Size]</font></div><!-- ##MSWORD;"+tWordFile+"## -->"+TableEnd;
		doOperation = 1;
		} else {
		sText = dataData+"<BR>"+TableStart+TextHeader+"<div ID=WORDFILE><img src='client/download_icon.gif' align=absmiddle vspace=0 hspace=2 border=0 width=20 height=19> <font face=Arial style='font-size: 8pt;'><U>"+tWordFile+"</U><font color=#999999> [Size]</font></div><!-- ##MSWORD;"+tWordFile+"## -->"+TableEnd;
		doOperation = 1;
		}
	} else {
		LowerDropZone();
		doOperation = 0;
	}

	if (doOperation == 1) {
		document.getElementById(ColRowID).innerHTML= sText;
		document.getElementById(ColRowID).style.backgroundColor= "#FFFFFF";
	}
  	mswordname.selectedIndex = 0;
}

// =========================================================

function okMembership() {

	doOperation = 0;

   var dataTrue = dataData.search("pixel.gif");

	disOne = dbaseSelect.selectedIndex;
	tdBase = eval("dbaseSelect.options["+disOne+"].value");

   TextHeader = "<img src=images/text_header.gif width=199 height=15 border=0 align=left vspace=0 hspace=0 style='cursor: move;'><BR CLEAR=ALL>";

	TableStart = "<table border=0 width=199 cellpadding=1 cellspacing=0 style='border: 1px inset black; background: #EFEFEF;'><tr><td width=199 align=center valign=top>";
    TableEnd = "</td></tr></table><!-- ~~~ -->";

	if (tdBase != "NONE") {
		if (dataTrue > 0) {
		sText = TableStart+TextHeader+"<div ID=MEMDATA><div align=center valign=middle><font face=Arial size=1><B>Search: "+tdBase+"</B></div></tt></div><!-- ##MEMBERSHIP;"+tdBase+"## -->"+TableEnd;
		doOperation = 1;
		} else {
		sText = dataData+"<BR>"+TableStart+TextHeader+"<div ID=MEMDATA class=datetext><div align=center valign=middle><font face=Arial size=1><B>Search: "+tdBase+"</B></div></tt></div><!-- ##MEMBERSHIP;"+tdBase+"## -->"+TableEnd;
		doOperation = 1;
		}
	} else {
		LowerDropZone();
		doOperation = 0;
	}

	if (doOperation == 1) {
		document.getElementById(ColRowID).innerHTML= sText;
		document.getElementById(ColRowID).style.backgroundColor= "#FFFFFF";
	}
  	dbaseSelect.selectedIndex = 0;
}

// =========================================================

function OkMP3Data() {

	doOperation = 0;

   var dataTrue = dataData.search("pixel.gif");

	disOne = mp3name.selectedIndex;
	tMP3 = eval("mp3name.options["+disOne+"].value");

   TextHeader = "<img src=images/text_header.gif width=199 height=15 border=0 align=left vspace=0 hspace=0 style='cursor: move;'><BR CLEAR=ALL>";

	TableStart = "<table border=0 width=199 cellpadding=2 cellspacing=0 style='border: 1px inset black; background: #EFEFEF;'><tr><td width=199 align=left valign=top>";
    TableEnd = "</td></tr></table><!-- ~~~ -->";

	if (tMP3 != "NONE") {
		if (dataTrue > 0) {
		sText = TableStart+TextHeader+"<div ID=MP3FILE><font style='font-family: Arial; font-size: 8pt;'><img src='client/download_icon.gif' align=absmiddle vspace=0 hspace=2 border=0 width=20 height=19><U><font color=blue>"+tMP3+"</font></u></div><!-- ##MP3;"+tMP3+"## -->"+TableEnd;
		doOperation = 1;
		} else {
		sText = dataData+"<BR>"+TableStart+TextHeader+"<div ID=MP3FILE><font style='font-family: Arial; font-size: 8pt;'><img src='client/download_icon.gif' align=absmiddle vspace=0 hspace=2 border=0 width=20 height=19><U><font color=blue>"+tMP3+"</font></U></div><!-- ##MP3;"+tMP3+"## -->"+TableEnd;
		doOperation = 1;
		}
	} else {
		LowerDropZone();
		doOperation = 0;
	}

	if (doOperation == 1) {
		document.getElementById(ColRowID).innerHTML= sText;
		document.getElementById(ColRowID).style.backgroundColor= "#FFFFFF";
	}
  	mp3name.selectedIndex = 0;
}

// =========================================================

function OkMP3DataUP(tMP3) {

	doOperation = 0;
   var dataTrue = dataData.search("pixel.gif");
   TextHeader = "<img src=images/text_header.gif width=199 height=15 border=0 align=left vspace=0 hspace=0 style='cursor: move;'><BR CLEAR=ALL>";
	TableStart = "<table border=0 width=199 cellpadding=2 cellspacing=0 style='border: 1px inset black; background: #EFEFEF;'><tr><td width=199 align=left valign=top>";
    TableEnd = "</td></tr></table><!-- ~~~ -->";

	if (tMP3 != "NONE") {
		if (dataTrue > 0) {
		sText = TableStart+TextHeader+"<div ID=MP3FILE><font style='font-family: Arial; font-size: 8pt;'><img src='client/download_icon.gif' align=absmiddle vspace=0 hspace=2 border=0 width=20 height=19><U><font color=blue>"+tMP3+"</font></u></div><!-- ##MP3;"+tMP3+"## -->"+TableEnd;
		doOperation = 1;
		} else {
		sText = dataData+"<BR>"+TableStart+TextHeader+"<div ID=MP3FILE><font style='font-family: Arial; font-size: 8pt;'><img src='client/download_icon.gif' align=absmiddle vspace=0 hspace=2 border=0 width=20 height=19><U><font color=blue>"+tMP3+"</font></U></div><!-- ##MP3;"+tMP3+"## -->"+TableEnd;
		doOperation = 1;
		}
	} else {
		LowerDropZone();
		doOperation = 0;
	}

	if (doOperation == 1) {
		document.getElementById(ColRowID).innerHTML= sText;
		document.getElementById(ColRowID).style.backgroundColor= "#FFFFFF";
	}
  	window.frames['cframe'].mp3name.selectedIndex = 0;
}

// =========================================================

function OkVideoData() {

	doOperation = 0;

   var dataTrue = dataData.search("pixel.gif");

	disOne = videoname.selectedIndex;
	tVideo = eval("videoname.options["+disOne+"].value");

	tVideoW = videow.value;
	tVideoW = tVideoW.toString();

	tVideoH = videoh.value;
	tVideoH = tVideoH.toString();

	var istVideo = tVideo.search(";;");
	if(istVideo > 0){
	   tVideo = tVideo.replace(';;',';'+tVideoW+';'+tVideoH);
	}else{
      var disTing = tVideo.indexOf(";");
      tVideo = tVideo.substring(0, disTing);
      tVideo = tVideo.concat(';'+tVideoW+';'+tVideoH);
   }

	//tVideoFull = tVideo+" "+tVideoW+"W X "+tVideoH+"H";
	var flash_file_test = tVideo.search(".swf");

	if (flash_file_test > 0) {		// This is a FLASH file
		OkFlashData(tVideo,tVideoW,tVideoH);
		tVideo = "NONE";
	}
   TextHeader = "<img src=images/text_header.gif width=199 height=15 border=0 align=left vspace=0 hspace=0 style='cursor: move;'><BR CLEAR=ALL>";
	TableStart = "<table border=0 width=199 cellpadding=2 cellspacing=0 style='border: 1px inset black; background: #EFEFEF;'><tr><td width=199 align=center valign=top>";
    TableEnd = "</td></tr></table><!-- ~~~ -->";

	if (tVideo != "NONE") {
		if (dataTrue > 0) {
		sText = TableStart+TextHeader+"<div ID=VIDEOFILE align=center><font style='font-family: Arial; font-size: 7pt;'><INPUT TYPE=BUTTON VALUE='Play Video' CLASS=FormLt1><BR><FONT COLOR=#999999>[ "+tVideo+" ]</FONT></div><!-- ##VIDEO;"+tVideo+";"+tVideoW+";"+tVideoH+"## -->"+TableEnd;
		doOperation = 1;
		} else {
		sText = dataData+"<BR>"+TableStart+TextHeader+"<div ID=VIDEOFILE align=center><font style='font-family: Arial; font-size: 7pt;'><INPUT TYPE=BUTTON VALUE='Play Video' CLASS=FormLt1><BR><FONT COLOR=#999999>[ "+tVideo+" ]</FONT></div><!-- ##VIDEO;"+tVideo+";"+tVideoW+";"+tVideoH+"## -->"+TableEnd;
		doOperation = 1;
		}
	} else {
		LowerDropZone();
		doOperation = 0;
	}

	if (doOperation == 1) {
		document.getElementById(ColRowID).innerHTML= sText;
		document.getElementById(ColRowID).style.backgroundColor= "#FFFFFF";
	}

  	videoname.selectedIndex = 0;
}

// =========================================================

function OkVideoDataUP(tVideo,tVideoW,tVideoH) {

	doOperation = 0;

	var dataTrue = dataData.search("pixel.gif");

	//tVideoFull = tVideo+" "+tVideoW+"W X "+tVideoH+"H";
	var flash_file_test = tVideo.search(".swf");

	var istVideo = tVideo.search(";;");
	if(istVideo > 0){
	   tVideo = tVideo.replace(';;',';'+tVideoW+';'+tVideoH);
	   tVideoFull = tVideo.replace(';;',';'+tVideoW+';'+tVideoH);
	}else{
      var disTing = tVideo.indexOf(";");
      tVideo = tVideo.substring(0, disTing);
      tVideo = tVideo.concat(';'+tVideoW+';'+tVideoH);
   }

	if (flash_file_test > 0) {		// This is a FLASH file
		OkFlashData(tVideo,tVideoW,tVideoH);
		tVideo = "NONE";
	}
   TextHeader = "<img src=images/text_header.gif width=199 height=15 border=0 align=left vspace=0 hspace=0 style='cursor: move;'><BR CLEAR=ALL>";
	TableStart = "<table border=0 width=199 cellpadding=2 cellspacing=0 style='border: 1px inset black; background: #EFEFEF;'><tr><td width=199 align=center valign=top>";
   TableEnd = "</td></tr></table><!-- ~~~ -->";

	if (tVideo != "NONE") {
		if (dataTrue > 0) {
		sText = TableStart+TextHeader+"<div ID=VIDEOFILE align=center><font style='font-family: Arial; font-size: 7pt;'><INPUT TYPE=BUTTON VALUE='Play Video' CLASS=FormLt1><BR><FONT COLOR=#999999>[ "+tVideo+" ]</FONT></div><!-- ##VIDEO;"+tVideo+";"+tVideoW+";"+tVideoH+"## -->"+TableEnd;
		doOperation = 1;
		} else {
		sText = dataData+"<BR>"+TableStart+TextHeader+"<div ID=VIDEOFILE align=center><font style='font-family: Arial; font-size: 7pt;'><INPUT TYPE=BUTTON VALUE='Play Video' CLASS=FormLt1><BR><FONT COLOR=#999999>[ "+tVideo+" ]</FONT></div><!-- ##VIDEO;"+tVideo+";"+tVideoW+";"+tVideoH+"## -->"+TableEnd;
		doOperation = 1;
		}
	} else {
		LowerDropZone();
		doOperation = 0;
	}

	if (doOperation == 1) {
		document.getElementById(ColRowID).innerHTML= sText;
		document.getElementById(ColRowID).style.backgroundColor= "#FFFFFF";
	}

  	window.frames['cframe'].videoname.selectedIndex = 0;
}


// =========================================================

function OkPopupData() {

	doOperation = 0;
   var dataTrue = dataData.search("pixel.gif");

	disOne = popname.selectedIndex;
	tPageName = eval("popname.options["+disOne+"].value");

	tWindowW = winw.value;
	tWindowW = tWindowW.toString();

	tWindowH = winh.value;
	tWindowH = tWindowH.toString();

   TextHeader = "<img src=images/text_header.gif width=199 height=15 border=0 align=left vspace=0 hspace=0 style='cursor: move;'><BR CLEAR=ALL>";
	TableStart = "<table border=0 width=199 cellpadding=2 cellspacing=0 style='border: 1px inset black; background: #EFEFEF;'><tr><td width=199 align=center valign=top>";
    TableEnd = "</td></tr></table><!-- ~~~ -->";

	if (tPageName != "NONE") {
		if (dataTrue > 0) {
		sText = TableStart+TextHeader+"<div ID=JAVAPOPUP align=center><font face=Arial style='font-size: 7pt;'><U>JAVASCRIPT POPUP WINDOW</U><BR>This is a <B>hidden</B> Javascript function that will automatically spawn a "+tWindowW+"x"+tWindowH+" browser window when this page is accessed.</div><!-- ##POPUP;"+tPageName+";"+tWindowW+";"+tWindowH+"## -->"+TableEnd;
		doOperation = 1;
		} else {
		sText = dataData+"<BR>"+TableStart+TextHeader+"<div ID=JAVAPOPUP align=center><font face=Arial style='font-size: 7pt;'><U>JAVASCRIPT POPUP WINDOW</U><BR>This is a <B>hidden</B> Javascript function that will automatically spawn a "+tWindowW+"x"+tWindowH+" browser window when this page is accessed.</div><!-- ##POPUP;"+tPageName+";"+tWindowW+";"+tWindowH+"## -->"+TableEnd;
		doOperation = 1;
		}
	} else {
		LowerDropZone();
		doOperation = 0;
	}

	if (doOperation == 1) {
		document.getElementById(ColRowID).innerHTML= sText;
		document.getElementById(ColRowID).style.backgroundColor= "#FFFFFF";
	}
  	popname.selectedIndex = 0;
	winw.value = '';
	winh.value = '';
}

// =========================================================

function OkCustomData() {

	doOperation = 0;

   var dataTrue = dataData.search("pixel.gif");

	disOne = customname.selectedIndex;
	tCustom = eval("customname.options["+disOne+"].value");

   TextHeader = "<img src=images/text_header.gif width=199 height=15 border=0 align=left vspace=0 hspace=0 style='cursor: move;'><BR CLEAR=ALL>";

	TableStart = "<table border=0 width=199 cellpadding=2 cellspacing=0 style='border: 1px inset black; background: #EFEFEF;'><tr><td width=199 align=center valign=top>";
    	TableEnd = "</td></tr></table><!-- ~~~ -->";

	if (tCustom != "NONE") {
		if (dataTrue > 0) {
		sText = TableStart+TextHeader+"<div ID=CUSTOMHTMLFILE align=center><font style='font-family: Arial; font-size: 8pt;'><B>Custom Code Include:</B><BR>[ "+tCustom+" ]</div><!-- ##CUSTOMHTML;"+tCustom+"## -->"+TableEnd;
		doOperation = 1;
		} else {
		sText = dataData+"<BR>"+TableStart+TextHeader+"<div ID=CUSTOMHTMLFILE align=center><font style='font-family: Arial; font-size: 8pt;'><B>Custom Code Include:</B><BR>[ "+tCustom+" ]</div><!-- ##CUSTOMHTML;"+tCustom+"## -->"+TableEnd;
		doOperation = 1;
		}
	} else {
		LowerDropZone();
		doOperation = 0;
	}

	if (doOperation == 1) {
		document.getElementById(ColRowID).innerHTML= sText;
		document.getElementById(ColRowID).style.backgroundColor= "#FFFFFF";
	}
  	customname.selectedIndex = 0;
}

// =========================================================

function OkCustomDataUP(tCustom) {
	doOperation = 0;
	   var dataTrue = dataData.search("pixel.gif");
   TextHeader = "<img src=images/text_header.gif width=199 height=15 border=0 align=left vspace=0 hspace=0 style='cursor: move;'><BR CLEAR=ALL>";
	TableStart = "<table border=0 width=199 cellpadding=2 cellspacing=0 style='border: 1px inset black; background: #EFEFEF;'><tr><td width=199 align=center valign=top>";
   TableEnd = "</td></tr></table><!-- ~~~ -->";

	if (tCustom != "NONE") {
		if (dataTrue > 0) {
		sText = TableStart+TextHeader+"<div ID=CUSTOMHTMLFILE align=center><font style='font-family: Arial; font-size: 8pt;'><B>Custom Code Include:</B><BR>[ "+tCustom+" ]</div><!-- ##CUSTOMHTML;"+tCustom+"## -->"+TableEnd;
		doOperation = 1;
		} else {
		sText = dataData+"<BR>"+TableStart+TextHeader+"<div ID=CUSTOMHTMLFILE align=center><font style='font-family: Arial; font-size: 8pt;'><B>Custom Code Include:</B><BR>[ "+tCustom+" ]</div><!-- ##CUSTOMHTML;"+tCustom+"## -->"+TableEnd;
		doOperation = 1;
		}
	} else {
		LowerDropZone();
		doOperation = 0;
	}

	if (doOperation == 1) {
		document.getElementById(ColRowID).innerHTML= sText;
		document.getElementById(ColRowID).style.backgroundColor= "#FFFFFF";
	}
}

// =========================================================
// SHOPPING CART SKU PLACEMENT
// =========================================================

function OkCartSku() {
	doOperation = 0;

   var dataTrue = dataData.search("pixel.gif");

	disOne = SINGLESKU.selectedIndex;
	tSingleSku = eval("SINGLESKU.options["+disOne+"].value");
	tSingleSku = tSingleSku.toString();

   TextHeader = "<img src=images/text_header.gif width=199 height=15 border=0 align=left vspace=0 hspace=0 style='cursor: move;'><BR CLEAR=ALL>";
	TableStart = "<table border=0 width=199 cellpadding=0 cellspacing=0 style='border: 1px inset black; background: #EFEFEF'><tr><td width=199 align=center valign=top>";
    TableEnd = "</td></tr></table><!-- ~~~ -->";

	if (tSingleSku != "NONE") {
		if (dataTrue > 0) {
		sText = TableStart+TextHeader+"<div ID=SKUPROMO align=center><img class=tHead src=client/singlesku.gif width=199 height=196 border=1><BR clear=all><font face=Arial size=1><B>Par No. (sku): <U>"+tSingleSku+"</U></div><!-- ##SINGLESKU;"+tSingleSku+"## -->"+TableEnd;
		doOperation = 1;
		} else {
		sText = dataData+"<BR>"+TableStart+TextHeader+"<div ID=SKUPROMO align=center valign=middle><img class=tHead src=client/singlesku.gif width=199 height=196 border=1><BR clear=all><font face=Arial size=1><B>Part No. (sku): <U>"+tSingleSku+"</U></div><!-- ##SINGLESKU;"+tSingleSku+"## -->"+TableEnd;
		doOperation = 1;
		}
	} else {
		LowerDropZone();
		doOperation = 0;
	}

	if (doOperation == 1) {
		document.getElementById(ColRowID).innerHTML= sText;
		document.getElementById(ColRowID).style.backgroundColor= "#FFFFFF";
	}
	SINGLESKU.value = "";
}

// =========================================================

function OkGrlinkData() {

	disOne = grlink.selectedIndex;
	tlinkBut = eval("grlink.options["+disOne+"].value");

	tlinkBut = tlinkBut.toString();

	eval(tlinkBut+"();");
}

// =========================================================

function OkNewsletterData() {
	doOperation = 0;

	tContestVar = "no";

	tNewsCat = newsSel.options(newsSel.selectedIndex).value;
	tNewsCat = tNewsCat.toString();

	TableStart = "<table border=0 width=199 cellpadding=0 cellspacing=0><tr><td width=199 align=center valign=middle><img class=tHead src=client/newsletter_tool.gif width=199 height=30 border=1><BR clear=all>";
    	TableEnd = "</td></tr></table><!-- ~~~ -->";

	if (tNewsCat != "NONE") {
		if (InObj.search("pixel.gif") > 0) {
		sText = TableStart+"<div ID=NEWSLETTER class=datetext><div align=center valign=middle><font face=Arial size=1><B>Category: "+tNewsCat+"</div></tt></div><!-- ##NEWSLETTER;"+tNewsCat+";"+tContestVar+"## -->"+TableEnd;
		doOperation = 1;
		} else {
		sText = oldImageData+"<BR>"+TableStart+"<div ID=NEWSLETTER class=datetext><div align=center valign=middle><font face=Arial size=1><B>Category: "+tNewsCat+"</div></tt></div><!-- ##NEWSLETTER;"+tNewsCat+";"+tContestVar+"## -->"+TableEnd;
		doOperation = 1;
		}
	} else {
		LowerDropZone();
		doOperation = 0;
	}

	if (doOperation == 1) {
		ColumnDrop.innerHTML = sText;
		document.getElementById(ColRowID).style.backgroundColor= "#FFFFFF";
	}
  	newsSel.selectedIndex = 0;
}

// =========================================================

function OkNewsArchive() {

	doOperation = 0;
	tloginbutton = "Newsletter Archives";

	TableStart = "<table border=0 cellpadding=0 cellspacing=0><tr><td width=199 height=75 align=center valign=middle>";
    	TableEnd = "</td></tr></table><!-- ~~~ -->";

	if (tloginbutton != "") { 		if (InObj.search("pixel.gif") > 0) {
			sText = TableStart+TextHeader+"<input type=button value=\""+tloginbutton+"\" id=NEWSARCH class=FormLt1 vspace=2><!-- ##NEWSARCHIVE;"+tloginbutton+"## -->"+TableEnd;
			doOperation = 1;
		} else {
			sText = oldImageData+"<BR>"+TableStart+TextHeader+"<input type=button value=\""+tloginbutton+"\" id=NEWSARCH class=FormLt1 vspace=2><!-- ##NEWSARCHIVE;"+tloginbutton+"## -->"+TableEnd;
			doOperation = 1;
		}
	} else {
		LowerDropZone();
		alert("You did not fill in a name for the button.");
		doOperation = 0;
	}

	if (doOperation == 1) {
		ColumnDrop.innerHTML = sText;
		document.getElementById(ColRowID).style.backgroundColor= "#FFFFFF";
	}
	LowerDropZone();


}

// =========================================================
function printButton() {
   var dataTrue = dataData.search("pixel.gif");

   TextHeader = "<img src=images/text_header.gif width=199 height=15 border=0 align=left vspace=0 hspace=0 style='cursor: move;'><BR CLEAR=ALL>";
	TableStart = "<table border=0 cellpadding=2 cellspacing=0 style='background: #EFEFEF; border: 1px inset black;'><tr><td width=199 align=center valign=top>";
    	TableEnd = "</td></tr></table><!-- ~~~ -->";

	if (dataTrue > 0) {
		sText = TableStart+TextHeader+"<input type=button style='cursor: hand;' value='<? echo lang("Printable Page"); ?>' class=FormLt1><!-- ##PRINTTHIS## -->"+TableEnd;
		doOperation = 1;
	} else {
		sText = dataData+"<BR>"+TableStart+TextHeader+"<input type=button style='cursor: hand;' value='<? echo lang("Printable Page"); ?>' class=FormLt1><!-- ##PRINTTHIS## -->"+TableEnd;
		doOperation = 1;
	}

	if (doOperation == 1) {
		document.getElementById(ColRowID).innerHTML= sText;
		document.getElementById(ColRowID).style.backgroundColor= "#FFFFFF";
	}
}

// =========================================================


function adobelink() {
   var dataTrue = dataData.search("pixel.gif");

   TextHeader = "<img src=images/text_header.gif width=199 height=15 border=0 align=left vspace=0 hspace=0 style='cursor: move;'><BR CLEAR=ALL>";
	TableStart = "<table border=0 cellpadding=0 cellspacing=1><tr><td width=199 height=75 align=center valign=middle>";
   TableEnd = "</td></tr></table><!-- ~~~ -->";

	if (dataTrue > 0) {
		sText = TableStart+"<img src=client/adobe_link.gif border=0 class=tHead><!-- ##ADOBELINK## -->"+TableEnd;
		doOperation = 1;
	} else {
		sText = dataData+"<BR>"+TableStart+"<img src=client/adobe_link.gif border=0 class=tHead><!-- ##ADOBELINK## -->"+TableEnd;
		doOperation = 1;
	}

	if (doOperation == 1) {
		document.getElementById(ColRowID).innerHTML= sText;
		document.getElementById(ColRowID).style.backgroundColor= "#FFFFFF";
	}
}

// =========================================================


function flashlink() {
   var dataTrue = dataData.search("pixel.gif");

   TextHeader = "<img src=images/text_header.gif width=199 height=15 border=0 align=left vspace=0 hspace=0 style='cursor: move;'><BR CLEAR=ALL>";
	TableStart = "<table border=0 cellpadding=0 cellspacing=1><tr><td width=199 height=75 align=center valign=middle>";
    	TableEnd = "</td></tr></table><!-- ~~~ -->";

	if (dataTrue > 0) {
		sText = TableStart+"<img src=client/flash_link.gif border=0 class=tHead><!-- ##FLASHLINK## -->"+TableEnd;
		doOperation = 1;
	} else {
		sText = dataData+"<BR>"+TableStart+"<img src=client/flash_link.gif border=0 class=tHead><!-- ##FLASHLINK## -->"+TableEnd;
		doOperation = 1;
	}

	if (doOperation == 1) {
		document.getElementById(ColRowID).innerHTML= sText;
		document.getElementById(ColRowID).style.backgroundColor= "#FFFFFF";
	}
}

// =========================================================


function winamplink() {
   var dataTrue = dataData.search("pixel.gif");

   TextHeader = "<img src=images/text_header.gif width=199 height=15 border=0 align=left vspace=0 hspace=0 style='cursor: move;'><BR CLEAR=ALL>";
	TableStart = "<table border=0 cellpadding=0 cellspacing=1><tr><td width=199 height=75 align=center valign=middle>";
    	TableEnd = "</td></tr></table><!-- ~~~ -->";

	if (dataTrue > 0) {
		sText = TableStart+"<img src=client/winamp_link.gif border=0 class=tHead><!-- ##WINAMPLINK## -->"+TableEnd;
		doOperation = 1;
	} else {
		sText = dataData+"<BR>"+TableStart+"<img src=client/winamp_link.gif border=0 class=tHead><!-- ##WINAMPLINK## -->"+TableEnd;
		doOperation = 1;
	}

	if (doOperation == 1) {
		document.getElementById(ColRowID).innerHTML= sText;
		document.getElementById(ColRowID).style.backgroundColor= "#FFFFFF";
	}
}

// =========================================================


function quicktimelink() {
   var dataTrue = dataData.search("pixel.gif");

   TextHeader = "<img src=images/text_header.gif width=199 height=15 border=0 align=left vspace=0 hspace=0 style='cursor: move;'><BR CLEAR=ALL>";
	TableStart = "<table border=0 cellpadding=0 cellspacing=1><tr><td width=199 height=75 align=center valign=middle>";
    	TableEnd = "</td></tr></table><!-- ~~~ -->";

	if (dataTrue > 0) {
		sText = TableStart+"<img src=client/quicktime_link.gif border=0 class=tHead><!-- ##QUICKTIMELINK## -->"+TableEnd;
		doOperation = 1;
	} else {
		sText = dataData+"<BR>"+TableStart+"<img src=client/quicktime_link.gif border=0 class=tHead><!-- ##QUICKTIMELINK## -->"+TableEnd;
		doOperation = 1;
	}

	if (doOperation == 1) {
		document.getElementById(ColRowID).innerHTML= sText;
		document.getElementById(ColRowID).style.backgroundColor= "#FFFFFF";
	}
}

// =========================================================


function emailfriend() {
   var dataTrue = dataData.search("pixel.gif");

   TextHeader = "<img src=images/text_header.gif width=199 height=15 border=0 align=left vspace=0 hspace=0 style='cursor: move;'><BR CLEAR=ALL>";
	TableStart = "<table border=0 cellpadding=2 cellspacing=0 style='border: 1px inset black; background: #EFEFEF;'><tr><td width=199 align=center valign=top>";
    	TableEnd = "</td></tr></table><!-- ~~~ -->";

	if (dataTrue > 0) {
		sText = TableStart+TextHeader+"<font style='font-family: Arial; font-size: 8pt;'>[ <U><font color=darkblue>Email this page to a friend</font></U> ]</FONT><!-- ##EFRIEND## -->"+TableEnd;
		doOperation = 1;
	} else {
		sText = dataData+"<BR>"+TableStart+TextHeader+"<font style='font-family: Arial; font-size: 8pt;'>[ <U><font color=darkblue>Email this page to a friend</font></U> ]</FONT><!-- ##EFRIEND## -->"+TableEnd;
		doOperation = 1;
	}

	if (doOperation == 1) {
		document.getElementById(ColRowID).innerHTML= sText;
		document.getElementById(ColRowID).style.backgroundColor= "#FFFFFF";
	}
}

// =========================================================


function do_calendar() {

   var dataTrue = dataData.search("pixel.gif");

   disOne = calcat.selectedIndex;
	var calcategory = eval("calcat.options["+disOne+"].value");
	calcategory = calcategory.toString();

   TextHeader = "<img src=images/text_header.gif width=199 height=15 border=0 align=left vspace=0 hspace=0 style='cursor: move;'><BR CLEAR=ALL>";

	TableStart = "<table border=0 cellpadding=2 cellspacing=0 style='border: 1px inset black; background: #EFEFEF;'><tr><td width=199 align=center valign=top>";
   TableEnd = "</td></tr></table><!-- ~~~ -->";

	CalCode = "<DIV ALIGN=CENTER STYLE='font-family: arial; font-size: 7pt; width: 199px;'>";

	if (caltypeW.checked) {
		var ctype = "W";
		CalCode = CalCode + "<B><FONT SIZE=2 FACE=VERDANA><U>THIS WEEK CALENDAR</U></FONT></B><BR><BR>When viewing the page, you will see";
		CalCode = CalCode + " events for the current week in the '<U>"+calcategory+"</U>'.<BR>(7 days; Sun-Sat).";
	}

	if (caltypeM.checked) {
		var ctype = "M";
		CalCode = CalCode + "<B><FONT SIZE=2 FACE=VERDANA><U>THIS MONTH CALENDAR</U></FONT></B><BR><BR>When viewing the page, you will see";
		CalCode = CalCode + " this month's events ONLY from the '<U>"+calcategory+"</U>' category, displayed in a monthly view display.<BR><BR>Please note: this will take up a<br>large majority of the page.";
	}


	if (caltypeS.checked) {
		var ctype = "SYS";
		CalCode = CalCode + "<B><FONT SIZE=2 FACE=VERDANA><U>CALENDAR SYSTEM OBJECT</U></FONT></B><BR><BR>When viewing the page, you will see";
		CalCode = CalCode + " the event calendar interface. All search options etc. will be available.<BR><BR>Please note: This should be on a page by itself for optimal operation.";
	}

	CalCode = CalCode + "</DIV>";

	if (dataTrue > 0) {
		sText = TableStart+TextHeader+CalCode+"<!-- ##CALENDAR;"+ctype+";"+calcategory+"## -->"+TableEnd;
		doOperation = 1;
	} else {
		sText = dataData+"<BR>"+TableStart+TextHeader+CalCode+"<!-- ##CALENDAR;"+ctype+";"+calcategory+"## -->"+TableEnd;
		doOperation = 1;
	}

	if (doOperation == 1) {
		document.getElementById(ColRowID).innerHTML= sText;
		document.getElementById(ColRowID).style.backgroundColor= "#FFFFFF";
	}
}

// =========================================================

function pageCounter() {
   var dataTrue = dataData.search("pixel.gif");

   TextHeader = "<img src=images/text_header.gif width=199 height=15 border=0 align=left vspace=0 hspace=0 style='cursor: move;'><BR CLEAR=ALL>";
	TableStart = "<table border=0 cellpadding=2 cellspacing=0 STYLE='border: 1px inset black; background: #EFEFEF;'><tr><td width=199 align=center valign=top>";
    TableEnd = "</td></tr></table><!-- ~~~ -->";

	if (dataTrue > 0) {
		sText = TableStart+TextHeader+"<img src='client/counter_tool.gif' width=90 height=20 border=0 alt=\"Hit Counter\"><!-- ##COUNTER## -->"+TableEnd;
		doOperation = 1;
	} else {
		sText = dataData+"<BR>"+TableStart+TextHeader+"<img src='client/counter_tool.gif' width=90 height=20 border=0 alt=\"Hit Counter\"><!-- ##COUNTER## -->"+TableEnd;
		doOperation = 1;
	}

	if (doOperation == 1) {
		document.getElementById(ColRowID).innerHTML= sText;
		document.getElementById(ColRowID).style.backgroundColor= "#FFFFFF";
	}
}


// =========================================================

function photoDisable(){
	if($('photoUser').checked){
		$('photocat').disabled=true
	}else{
		$('photocat').disabled=false
	}
}

function photoalbum() {
	if($('photoUser').checked){
	   var dataTrue = dataData.search("pixel.gif");

	   TextHeader = "<img src=images/text_header.gif width=199 height=15 border=0 align=left vspace=0 hspace=0 style='cursor: move;'><BR CLEAR=ALL>";
		TableStart = "<table border=0 cellpadding=2 cellspacing=0 style='border: 1px inset black; background: #EFEFEF;'><tr><td width=199 align=center valign=top>";
	   TableEnd = "</td></tr></table><!-- ~~~ -->";

		if (dataTrue > 0) {
			sText = TableStart+TextHeader+"<font style='font-family: Arial; font-size: 8pt;'><U><font color=darkblue>Photo Album</font></U></FONT><!-- ##CUSTOMHTML;pgm-photo_album.php## -->"+TableEnd;
			doOperation = 1;
		} else {
			sText = dataData+"<BR>"+TableStart+TextHeader+"<font style='font-family: Arial; font-size: 8pt;'><U><font color=darkblue>Photo Album</font></U></FONT><!-- ##CUSTOMHTML;pgm-photo_album.php## -->"+TableEnd;
			doOperation = 1;
		}

		if (doOperation == 1) {
			document.getElementById(ColRowID).innerHTML= sText;
			document.getElementById(ColRowID).style.backgroundColor= "#FFFFFF";
		}
	}else{

		doOperation = 0;

	   var dataTrue = dataData.search("pixel.gif");

		disOne = photocat.selectedIndex;
		tphotocat = eval("photocat.options["+disOne+"].value");

		if(tphotocat == "NULL"){
			alert('You have not selected a photo album, nothing will be added to the page.');
			replaceImageData();
			makeUnScroll(ColRowID);
		}else{

			tphotocat = tphotocat.toString();

		   TextHeader = "<img src=images/text_header.gif width=199 height=15 border=0 align=left vspace=0 hspace=0 style='cursor: move;'><BR CLEAR=ALL>";
			TableStart = "<table border=0 cellpadding=2 cellspacing=0 style='border: 1px inset black; background: #EFEFEF;'><tr><td width=199 align=center valign=top>";
		    TableEnd = "</td></tr></table><!-- ~~~ -->";

			if (dataTrue > 0) {
				sText = TableStart+TextHeader+"<font style='font-family: Arial; font-size: 8pt;'><U><font color=darkblue>Photo Album : "+tphotocat+"</font></U></FONT><!-- ##PHOTO;"+tphotocat+"## -->"+TableEnd;
				doOperation = 1;
			} else {
				sText = dataData+"<BR>"+TableStart+TextHeader+"<font style='font-family: Arial; font-size: 8pt;'><U><font color=darkblue>Photo Album : "+tphotocat+"</font></U></FONT><!-- ##PHOTO;"+tphotocat+"## -->"+TableEnd;
				doOperation = 1;
			}

			if (doOperation == 1) {
				document.getElementById(ColRowID).innerHTML= sText;
				document.getElementById(ColRowID).style.backgroundColor= "#FFFFFF";
			}
		  	photocat.selectedIndex = 0;	// Reset Selection to Nothing(Null)
		}
	}
}

// =========================================================

function placeCartSearch() {
   var dataTrue = dataData.search("pixel.gif");

   TextHeader = "<img src=images/text_header.gif width=199 height=15 border=0 align=left vspace=0 hspace=0 style='cursor: move;'><BR CLEAR=ALL>";
	TableStart = "<table border=0 cellpadding=0 cellspacing=0><tr><td width=199 align=center valign=top>";
   TableEnd = "</td></tr></table><!-- ~~~ -->";


	if (dataTrue > 0) {
		sText = TableStart+TextHeader+"<img src='client/cartsearch_tool.gif' width=199 height=71 border=0 class=tHead vspace=0 hspace=0><!-- ##CARTSEARCH## -->"+TableEnd;
		doOperation = 1;
	} else {
		sText = dataData+"<BR>"+TableStart+TextHeader+"<img src='client/cartsearch_tool.gif' width=199 height=71 border=0 class=tHead vspace=0 hspace=0><!-- ##CARTSEARCH## -->"+TableEnd;
		doOperation = 1;
	}

	if (doOperation == 1) {
		document.getElementById(ColRowID).innerHTML= sText;
		document.getElementById(ColRowID).style.backgroundColor= "#FFFFFF";
	}
}


// =========================================================

function OkBlog() {
	doOperation = 0;

   var dataTrue = dataData.search("pixel.gif");

	disOne = blogsubj.selectedIndex;
	tBlogSubject = eval("blogsubj.options["+disOne+"].value");
   tBlogSubject = tBlogSubject.toString();

   TextHeader = "<img src=images/text_header.gif width=199 height=15 border=0 align=left vspace=0 hspace=0 style='cursor: move;'><BR CLEAR=ALL>";
	TableStart = "<table border=0 cellpadding=2 cellspacing=0 style='border: 1px inset black; background: #EFEFEF;'><tr><td width=199 align=center valign=top>";
    TableEnd = "</td></tr></table><!-- ~~~ -->";

	if (dataTrue > 0) {
		sText = TableStart+TextHeader+"<font style='font-family: Arial; font-size: 8pt;'><U><font color=darkblue>Blog : "+tBlogSubject+"</font></U></FONT><!-- ##BLOG;"+tBlogSubject+"## -->"+TableEnd;
		doOperation = 1;
	} else {
		sText = dataData+"<BR>"+TableStart+TextHeader+"<font style='font-family: Arial; font-size: 8pt;'><U><font color=darkblue>Blog : "+tBlogSubject+"</font></U></FONT><!-- ##BLOG;"+tBlogSubject+"## -->"+TableEnd;
		doOperation = 1;
	}

	if (doOperation == 1) {
		document.getElementById(ColRowID).innerHTML= sText;
		document.getElementById(ColRowID).style.backgroundColor= "#FFFFFF";
	}
  	blogsubj.selectedIndex = 0;	// Reset Selection to Nothing(Null)
}

// =========================================================

function OkFaq() {
	doOperation = 0;

   var dataTrue = dataData.search("pixel.gif");

	disOne = faqcat.selectedIndex;
	tFaqCat = eval("faqcat.options["+disOne+"].value");

	tFaqCat = tFaqCat.toString();

   TextHeader = "<img src=images/text_header.gif width=199 height=15 border=0 align=left vspace=0 hspace=0 style='cursor: move;'><BR CLEAR=ALL>";
	TableStart = "<table border=0 cellpadding=2 cellspacing=0 style='border: 1px inset black; background: #EFEFEF;'><tr><td width=199 align=center valign=top>";
    TableEnd = "</td></tr></table><!-- ~~~ -->";

	if (dataTrue > 0) {
		sText = TableStart+TextHeader+"<font style='font-family: Arial; font-size: 8pt;'><U><font color=darkblue>Faq : "+tFaqCat+"</font></U></FONT><!-- ##FAQ;"+tFaqCat+"## -->"+TableEnd;
		doOperation = 1;
	} else {
		sText = dataData+"<BR>"+TableStart+TextHeader+"<font style='font-family: Arial; font-size: 8pt;'><U><font color=darkblue>Faq : "+tFaqCat+"</font></U></FONT><!-- ##FAQ;"+tFaqCat+"## -->"+TableEnd;
		doOperation = 1;
	}

	if (doOperation == 1) {
		document.getElementById(ColRowID).innerHTML= sText;
		document.getElementById(ColRowID).style.backgroundColor= "#FFFFFF";
	}
  	faqcat.selectedIndex = 0;	// Reset Selection to Nothing(Null)
}

// =========================================================


<?
eval(hook("pe_ff-place_object_js", basename(__FILE__)));
?>