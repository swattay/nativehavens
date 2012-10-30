//<script language="javascript">
// ========================================================= 
//[ADD START] PREMIUM ALBUMS. ADDED BY D. CHAPLINSKY, VIASTEP 1:22 14.11.2005
  
function OkPremiumData() {
  doOperation = 0;

  disOne = document.getElementById('albumname_id').selectedIndex;
  t_albumname = document.getElementById('albumname_id').options[disOne].value; //eval("albumname.options["+disOne+"].value");
  t_albumname1 = document.getElementById('albumname_id').options[disOne].innerHTML;

  t_show_thumb = document.getElementById('show_thumb_id').checked;
	
	my_l = document.getElementById('temp_id').my_layout;
  for (var i = 0; i<my_l.length; i++) {
     if (my_l[i].checked==1)
     {
       t_layout=my_l[i].value;
       break;
     }
  }


  switch (t_layout){
    case '1': t_layout1 = 'Layout: Left-right'; break;
    case '2': t_layout1 = 'Layout: Top-bottom'; break;
    case '3': t_layout1 = 'Layout: Top'; break;
    case '4': t_layout1 = 'Layout: Image only'; break;
    case '5': t_layout1 = 'Layout: Bottom'; break;
    case '6': t_layout1 = 'Layout: Bottom title'; break;
  }

  t_columns = document.getElementById('columns_id').value;
  t_rows = document.getElementById('rows_id').value;
  
  if (t_show_thumb==true && t_albumname!=-1)
  {
    t_show_thumb1=" "+t_show_thumb;
    t_show_thumb='; show thumbnail before album:'+t_show_thumb;
  }
  else
  {
    t_show_thumb1=" "+t_show_thumb;
    t_show_thumb='';
  }

  TextHeader = "<img src=images/text_header.gif width=199 height=15 border=0 align=left vspace=0 hspace=0 style='cursor: move;'><BR CLEAR=ALL>";
  TableStart = "<table border=0 width=199 cellpadding=3 cellspacing=0 style='background: #EFEFEF; border: 1px inset black;'><tr><td width=199 align=center valign=middle>";
  TableEnd = "</td></tr></table><!-- ~~~ -->";

  sText = TableStart+TextHeader+"<div ID=PREMIUMALBUM align=center><font style='font-family: Arial; font-size: 8pt; color: darkblue;'><U>"+t_albumname1+"<br>("+t_layout1+"; cols: "+t_columns+"; rows:"+t_rows+t_show_thumb+")</U></font></div><!-- ##PREMIUM_ALBUM;"+t_albumname+" "+t_layout+" "+t_columns+" "+t_rows+t_show_thumb1+"## -->"+TableEnd;

  document.getElementById(ColRowID).innerHTML= sText;
  document.getElementById(ColRowID).style.backgroundColor= "#FFFFFF";
}


//[ADD END] PREMIUM ALBUMS. ADDED BY D. CHAPLINSKY, VIASTEP 1:22 14.11.2005
// ========================================================= 
