
// Places sitepal scene in dropzone
// Accepts: baseurl (pulled from sitepal api)
// Gets: Value of 'scene_thumb' select box --- thumbnail filename of selected scene (from which scene # is extracted)
function place_sitepal(baseurl) {
//   alert(baseurl);

   var finalObj,RandNum;
   var tmplt = objTemplate('SITEPALOBJ', true, 100);

	doOperation = 0;
   var dataTrue = dataData.search("pixel.gif");

	// Get select value from scene dropdown box
   disOne = $('scene_dd').selectedIndex;
	var dd_val = eval("$('scene_dd').options["+disOne+"].value");
	var width = document.getElementById('sitepal_width').value;
	var height = document.getElementById('sitepal_height').value;

	// Split thumbnail from scene name
	var scene_info = dd_val.split("~~~");

	// Extract scene thumbnail file and name
	var scene_thumb = scene_info[0];
	var scene_name = scene_info[1];
	var account_id = scene_info[2];

	// Extract scene number
	var scene_num = scene_thumb.replace(/[0-9a-z\/]*\/thumbs\/show_/, "");
	//alert(scene_num);
	scene_num = scene_num.replace("\.jpg", "");
	//alert(scene_num);

   //MoveObject_Graphic = ;
	sitePalObj = "<table width=\"199\" border=\"0\" cellpadding=\"2\" cellspacing=\"0\" style=\"border: 1px inset black; background: #EFEFEF;\">\n";

	sitePalObj += " <tr>\n";

	// Thumbnail Image
	sitePalObj += "  <td valign=\"top\" width=\"64\">\n";
	sitePalObj += "   <img src=\""+baseurl+scene_thumb+"\" width=\"60\" height=\"60\" border=\"0\" style=\"cursor: move;\">";
   sitePalObj += "  </td>\n";

	// SitePal Scene #
	sitePalObj += "  <td valign=\"top\" width=\"135\">\n";
	sitePalObj += "   <b>"+scene_name+"</b><br/>\n";
	sitePalObj += "   Account #"+account_id+"<br/>\n";
	sitePalObj += "   SitePal Scene #"+scene_num+"<br/>\n";
	sitePalObj += "   Dimensions: "+width+"x"+height+"<br/>\n";
	sitePalObj += "   <!-- ##SITEPAL;"+scene_num+";"+width+";"+height+";"+account_id+"## -->";
   sitePalObj += "  </td>\n";

   sitePalObj += " </tr>\n";
   sitePalObj += "</table>\n";

   finalObj = tmplt[1].replace("##OBJ_DISPLAY##", sitePalObj);
   
   if (dd_val != "") {
      document.getElementById(ColRowID).innerHTML= finalObj;
      show_hide_layer('objectbar','','show','sitepal_dialog','','hide');
   }else{
      alert('Please select a scene from the list and click Place on Page.')
   }
   
  	document.getElementById('scene_dd').selectedIndex = 0;	// Reset Selection to Nothing(Null)
	checkRow(ColRowID)
}
