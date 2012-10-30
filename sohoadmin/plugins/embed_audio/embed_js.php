

function OkEmbedData() {

	doOperation = 0;

   var dataTrue = dataData.search("pixel.gif");
   
	disOne = document.getElementById('embedname').selectedIndex;
	embedStuff = eval("document.getElementById('embedname').options["+disOne+"].value");

   TextHeader = "<img src=images/text_header.gif width=199 height=15 border=0 align=left vspace=0 hspace=0 style='cursor: move;'><BR CLEAR=ALL>";
	TableStart = "<table border=0 cellpadding=2 cellspacing=0 style='border: 1px inset black; background: #EFEFEF;'><tr><td width=199 align=center valign=top>";
   TableEnd = "</td></tr></table><!-- ~~~ -->";

	if (embedStuff != "NONE") {
		if (dataTrue > 0) {
			sText = TableStart+TextHeader+"<font style='font-family: Arial; font-size: 8pt;'><U><font color=darkblue>Page Audio : "+embedStuff+"</font></U></FONT><!-- ##EMBEDME;"+embedStuff+"## -->"+TableEnd;
			doOperation = 1;
		} else {
			sText = dataData+"<BR>"+TableStart+TextHeader+"<font style='font-family: Arial; font-size: 8pt;'><U><font color=darkblue>Page Audio : "+embedStuff+"</font></U></FONT><!-- ##EMBEDME;"+embedStuff+"## -->"+TableEnd;
			doOperation = 1;
		}
	} else {
		doOperation = 0;
	}

	if (doOperation == 1) {
		document.getElementById(ColRowID).innerHTML= sText;
		document.getElementById(ColRowID).style.backgroundColor= "#FFFFFF";
	}
  	document.getElementById('embedname').selectedIndex = 0;	// Reset Selection to Nothing(Null)
	
   if ( window.checkPageAreas ){
      checkPageAreas('start');
   }
   
}

