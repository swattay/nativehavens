<?php
//apd_set_pprof_trace();

error_reporting(E_PARSE);
if($_GET['_SESSION'] != '' || $_POST['_SESSION'] != '' || $_COOKIE['_SESSION'] != '') { exit; }


session_start();
include("../../includes/product_gui.php");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<title>Page Editor</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<META HTTP-EQUIV="Expires" CONTENT="-1">
<link rel="stylesheet" type="text/css" href="../../product_gui.css">
<link rel="stylesheet" type="text/css" href="includes/page_editor.css">
<script language="JavaScript" src="../../includes/display_elements/js_functions.php"></script>
<script language="javascript" type="text/javascript" src="../tiny_mce/tiny_mce.js"></script>
<script language="javascript" type="text/javascript" src="includes/drop_cell.js"></script>
<script language="javascript" type="text/javascript" src="includes/mootools.v1.1.js"></script>
<script language="javascript" type="text/javascript" src="includes/general.js"></script>
<script type="text/javascript" src="../tiny_mce/plugins/media/jscripts/embed.js"></script>

<script language="javascript">

		window.addEvent('domready', function(){
			var fx = [];

$$('.drag').each(function(drag){
	new Drag.Move(drag, {
		droppables: $$('.editTable'),
		limit: $('editor')
	});
	
	drag.addEvent('emptydrop', function(){
	   //alert(this.id);
		this.setStyle('background-color', '#F8F8FF');
	});
});

$$('.editTable').each(function(drop, index){
	fx[index] = drop.effects({transition:Fx.Transitions.Back.easeOut});
	drop.addEvents({
		'over': function(el, obj){
			this.setStyle('background-color', '#78ba91');
		},
		'leave': function(el, obj){
			this.setStyle('background-color', '#F8F8FF');
		},
		'drop': function(el, obj){
		   //alert(el.id+'-'+obj.id)
		   //alert(this.id.charAt(3))
			el.remove();
//			fx[index].start({
//				'height': this.getStyle('height').toInt() + 30,
//				'background-color' : ['#78ba91', '#F8F8FF']
//			});

         // This is the action cell
			var myEffects = new Fx.Styles(this.id, {duration: 500, transition: Fx.Transitions.Back.easeOut});
      	myEffects.start({
				'height': this.getStyle('height').toInt() + 50,
				'background-color' : ['#78ba91', '#F8F8FF']
      	});
      	if(this.id.charAt(5) != 1){
   			var myEffectsRow1 = new Fx.Styles('TDR'+this.id.charAt(3)+'C1', {duration: 500, transition: Fx.Transitions.Back.easeOut});
         	myEffectsRow1.start({
   				'height': this.getStyle('height').toInt() + 50,
   				'background-color' : ['#78ba91', '#F8F8FF']
         	});
         }
      	if(this.id.charAt(5) != 2){
   			var myEffectsRow2 = new Fx.Styles('TDR'+this.id.charAt(3)+'C2', {duration: 500, transition: Fx.Transitions.Back.easeOut});
         	myEffectsRow2.start({
   				'height': this.getStyle('height').toInt() + 50,
   				'background-color' : ['#78ba91', '#F8F8FF']
         	});
         }
         if(this.id.charAt(5) != 3){
   			var myEffectsRow3 = new Fx.Styles('TDR'+this.id.charAt(3)+'C3', {duration: 500, transition: Fx.Transitions.Back.easeOut});
         	myEffectsRow3.start({
   				'height': this.getStyle('height').toInt() + 50,
   				'background-color' : ['#78ba91', '#F8F8FF']
         	});
         }
      	objectAction(el.id, this.id);
			//$('TDR1C2').style.height = $('TDR1C2').getStyle('height').toInt() + 30
			//$('TDR1C3').style.height = $('TDR1C3').getStyle('height').toInt() + 30
		}
	});
});
		});

function objectAction(obj, area){

}


</script>

</head>
<body>

<?php
// Determine which icons to pull based on language
//======================================================
if ( $getSpec['df_lang'] == "norwegian.php" ) {
   $ilng = "nor";
} else {
   $ilng = "eng";
}
$ipre = "obj_bar_icons/".$ilng."-icon_";
$engdash = "obj_bar_icons/".$ilng."-"; // newschool, use this
?>

<div id="objectbar">

   <? echo lang("Click on an object below and drag it onto a drop zone for page placement."); ?>

   <table border="0" cellpadding="0" cellspacing="0" align="center" width="<? echo $barWidth; ?>" height="100%" style="border: 1px solid #245981;">

    <tr>

     <!-- My Images -->
     <td width="81" height="20" align="center" valign="middle" class="ob2" onmouseover="this.className='ob1';" onmouseout="this.className='ob2';">
     <img class="drag" name="MYimages" align="absmiddle" id="oImage" value="oImage" src="<? echo $ipre; ?>images.gif" width="80" height="18" style="cursor: hand;"></td>

     <!-- Text Editor -->
     <td width="81" height="20" align="center" valign="middle" class="ob2" onmouseover="this.className='ob1';" onmouseout="this.className='ob2';">
     <img class="drag" name="MYeditor" align="absmiddle" id="oText" value="oText" src="<? echo $ipre; ?>texteditor.gif" width="80" height="18" style="cursor: hand;"></td>

     <!-- Forms -->
     <td width="81" height="20" align="center" valign="middle" class="ob2" onmouseover="this.className='ob1';" onmouseout="this.className='ob2';">
     <img class="drag" name="MYforms" align="absmiddle" id="oForms" value="oForms" src="<? echo $ipre; ?>forms.gif" width="80" height="18" style='cursor: hand;'></td>

     <!-- Documents -->
     <td width="81" height="20" align="center" valign="middle" class="ob2" onmouseover="this.className='ob1';" onmouseout="this.className='ob2';">
     <img class="drag" name="MYdocs" align="absmiddle" id="oMSWORD" value="oMSWORD" src="<? echo $ipre; ?>docs.gif" width="80" height="18" style='cursor: hand;'></td>

     <!--- Hit Counter --->
     <td width="81" height="20" align="center" valign="middle" class="ob2" onmouseover="this.className='ob1';" onmouseout="this.className='ob2';">
      <img class="drag" name="MYcounter" align="absmiddle" id="oCounter" value="oCounter" src="<? echo $ipre; ?>counter.gif" width="80" height="18" style="cursor: hand;">
     </td>

     <!-- Auth Login -->
     <td width="81" height="20" align="center" valign="middle" class="ob2" onmouseover="this.className='ob1';" onmouseout="this.className='ob2';">
     <img class="drag" name="MYauth" align="absmiddle" id="oSecureLogin" value="oSecureLogin" src="<? echo $ipre; ?>login.gif" width="80" height="18" style='cursor: hand;'></td>

     <!-- Custom Code -->
     <td width="81" height="20" align="center" valign="middle" class="ob2" onmouseover="this.className='ob1';" onmouseout="this.className='ob2';">
     <img class="drag" name="MYcust" align="absmiddle" id="oCustom" value="oCustom" src="<? echo $ipre; ?>customcode.gif" width="80" height="18" style='cursor: hand;'></td>

     <!-- Shopping -->
     <td width="81" height="20" align="center" valign="middle" class="ob2" onmouseover="this.className='ob1';" onmouseout="this.className='ob2';">
     <img class="drag" name="MYshop" align="absmiddle" id="oCart" value="oCart" src="<? echo $ipre; ?>shopping.gif" width="80" height="18" style='cursor: hand;'></td>

     <!-- Delete Object -->
     <td width="81" rowspan="3" height="60" align="center" valign="middle" class="ob2" onmouseover="this.className='ob1';" onmouseout="this.className='ob2';">
     <img class="drag" name="MYdelete" align="absmiddle" id="oDelete" value="oDelete" src="<? echo $ipre; ?>deleteobj.gif" style='cursor: hand;'></td>

     </tr>


    <tr>

     <!-- Table Search -->
     <td width="81" height="20" align="center" valign="middle" class="ob2" onmouseover="this.className='ob1';" onmouseout="this.className='ob2';">
     <img class="drag" name="MYsearch" align="absmiddle" id="oTableSearch" value="oTableSearch" src="<? echo $ipre; ?>searchdb.gif" width="80" height="18" style='cursor: hand;'></td>

     <!-- Sign-Up -->
     <td width="81" height="20" align="center" valign="middle" class="ob2" onmouseover="this.className='ob1';" onmouseout="this.className='ob2';">
     <img class="drag" name="MYsignup" align="absmiddle" id="oNewsletter" value="oNewsletter" src="<? echo $ipre; ?>newsletter.gif" width="80" height="18" style='cursor: hand;'></td>

     <!-- Calendar -->
     <td width="81" height="20" align="center" valign="middle" class="ob2" onmouseover="this.className='ob1';" onmouseout="this.className='ob2';">
     <img class="drag" name="MYcalendar" align="absmiddle" id="oCalendar" value="oCalendar" src="<? echo $ipre; ?>calendar.gif" width="80" height="18" style='cursor: hand;'></td>

     <!-- Directions -->
<?

// Disable map object? (intl. request)
//==========================================
if ( $map_obj != "disabled" ) {
   echo "     <td width=\"81\" height=\"20\" align=\"center\" valign=\"middle\" class=\"ob2\" onmouseover=\"this.className='ob1';\" onmouseout=\"this.className='ob2';\"><img class=\"drag\" id=\"MYdirections\" align=\"absmiddle\" id=oDirections value=\"oDirections\" src=\"".$ipre."directions.gif\" width=\"80\" height=\"18\" vspace=\"1\" hspace=\"1\" style='cursor: hand'></td>\n";
} else {
   echo "     <td width=\"81\" height=\"20\" align=\"center\" valign=\"middle\" class=\"ob2\">&nbsp;</td>\n";
}


// Shared properties for icon td's and img's
//--------------------------------------------
$tdProps = "width=\"81\" height=\"20\" align=\"center\" valign=\"middle\" class=\"ob2\"";
$iconProps = "width=\"80\" height=\"18\" vspace=\"1\" hspace=\"1\" align=\"absmiddle\" style=\"cursor: hand;\"";

?>

     <!--- Date Stamp --->
     <td <? echo $tdProps; ?> onmouseover="this.className='ob1';" onmouseout="this.className='ob2';">
      <img class="drag" <? echo $iconProps; ?> name="MYdate" id="oDate" value="oDate" src="<? echo $ipre; ?>datestamp.gif">
     </td>

     <!--- Print Page --->
     <td <? echo $tdProps; ?> onmouseover="this.className='ob1';" onmouseout="this.className='ob2';">
      <img class="drag" <? echo $iconProps; ?> name="MYprint" id="oPrint" value="oPrint" src="<? echo $ipre; ?>printpage.gif">
     </td>


     <!--- Email Friend --->
     <td <? echo $tdProps; ?> onmouseover="this.className='ob1';" onmouseout="this.className='ob2';">
      <img class="drag" <? echo $iconProps; ?> name="MYemail" id="oEmailTo" value="oEmailTo" src="<? echo $ipre; ?>emailfriend.gif">
     </td>


<?
# SitePal object?
//if ( sitepal_allowed() ) {
   # yes
   echo "     <!-- SitePal -->\n";
   echo "     <td width=\"81\" height=\"20\" align=\"center\" valign=\"middle\" class=\"ob2\" onmouseover=\"this.className='ob1';\" onmouseout=\"this.className='ob2';\">\n";
   echo "     <img class=\"drag\" name=\"MYsitepal\" border=\"0\" align=\"absmiddle\" id=\"sitepal_obj\" value=\"sitepal_obj\" src=\"".$engdash."sitepal.gif\" width=\"80\" height=\"18\" vspace=\"1\" hspace=\"1\" style='cursor: hand;'></td>\n";

?>

    </tr>

    <!---###################################################################################################--->
    <!------------------------------------ Drag and Drop Icons: ROW THREE ------------------------------------->
    <!---###################################################################################################--->
    <tr>

     <!--- PopUp Win --->
     <td width="81" height="20" align="center" valign="middle" class="ob2" onmouseover="this.className='ob1';" onmouseout="this.className='ob2';">
     <img class="drag" name="MYpopup" align="absmiddle" id="oPopup" value="oPopup" src="<? echo $ipre; ?>popup.gif" width="80" height="18" style='cursor: hand;'></td>

     <!--- Audio Files --->
     <td width="81" height="20" align="center" valign="middle" class="ob2" onmouseover="this.className='ob1';" onmouseout="this.className='ob2';">
     <img class="drag" name="MYaudio" align="absmiddle" id="oMP3" value="oMP3" src="<? echo $ipre; ?>audio.gif" width="80" height="18" style='cursor: hand;'></td>

     <!--- Video Files --->
     <td width="81" height="20" align="center" valign="middle" class="ob2" onmouseover="this.className='ob1';" onmouseout="this.className='ob2';">
     <img class="drag" name="MYvideo" align="absmiddle" id="oVideo" value="oVideo" src="<? echo $ipre; ?>video.gif" width="80" height="18" style='cursor: hand;'></td>

     <!--- PlugIn Links --->
     <td width="81" height="20" align="center" valign="middle" class="ob2" onmouseover="this.className='ob1';" onmouseout="this.className='ob2';">
     <img class="drag" name="MYplugin" align="absmiddle" id="oAdobelink" value="oAdobelink" src="<? echo $ipre; ?>link.gif" width="80" height="18" style='cursor: hand;'></td>

     <!--- Photo Album --->
     <td width="81" height="20" align="center" valign="middle" class="ob2" onmouseover="this.className='ob1';" onmouseout="this.className='ob2';">
     <img class="drag" name="MYphoto" align="absmiddle" id="oPhotoAlbum" value="oPhotoAlbum" src="<? echo $ipre; ?>photoalbum.gif" width="80" height="18" style='cursor: hand;'></td>

     <!--- Blogs --->
     <td width="81" height="20" align="center" valign="middle" class="ob2" onmouseover="this.className='ob1';" onmouseout="this.className='ob2';">
     <img class="drag" name="MYblog" align="absmiddle" id="oBlog" value="oBlog" src="<? echo $ipre; ?>blog.gif" width="80" height="18" style='cursor: hand;'></td>

     <!--- Faqs --->
     <td width="81" height="20" align="center" valign="middle" class="ob2" onmouseover="this.className='ob1';" onmouseout="this.className='ob2';">
     <img class="drag" name="MYfaq" align="absmiddle" id="oFaq" value="oFaq" src="<? echo $ipre; ?>faq.gif" width="80" height="18" style='cursor: hand;'></td>

     <!--- Addons/Mods --->
     <td width="81" height="20" align="center" valign="middle" class="ob2" onmouseover="this.className='ob1';" onmouseout="this.className='ob2';" onClick="show_mods();">
     <img class="drag" name="addons" align="absmiddle" id="addons" value="addons" src="<? echo $ipre; ?>addons.gif" width="80" height="18" style='cursor: hand;'>
     </td>

<?
//eval(hook("pe_ff-draggable_page_object", basename(__FILE__)));
?>

     <!--- Blank Object Spacers --->
     <!--- <td width="81" height="20" align="center" valign="middle" class="ob2">&nbsp;</td> --->
     <!--- <td width="81" height="20" align="center" valign="middle" class="ob2">&nbsp;</td> --->
    </tr>

   </table>




</div>


<div id="nugget">nugg</div>







	<?

	/**********************************************************************
      New Page Editor Drag and Drop in ie and Firefox- (Joe Lain 10-12-05)
    ----------------------------------------------------------------------
    The Page Editor's obj bar icons are draggable images that are identified
    by their name.  As soon as an icon is dragged my_PickFunc() makes the
    name of the icon available.  When the icon is dropped my_DropFunc()
    gets the coordinates of the icon and finds the cell that it was dropped
    in.
	/**********************************************************************/

   $leftPX = 50;
   $rightPX = 275;
   $topPX = 120;
   $botPX = 205;
   $c = 0;


   # Will contain comma-sep list of box ids
   $box_ids = "";

   # Holds scroll-up / scroll-down buttons
   $scrollers = "";

	// Ouput exactly 10 rows of drop zones
	//===========================================
	for ($x=1;$x<=10;$x++) {

		// Ouput each cell with correct pre-existing content (if any)
		//---------------------------------------------------------------
		for ($y=1;$y<=3;$y++) {
		   $c++;
			$areaId = "R" . $x . "C" . $y; // Used to pull existing cell content from loaded var data of same name (Ends up as 'R2C3' for 'ROW 2, COL 3')
			$tdid = "TD".$areaId; // Used to identify the cell in js
			$contentVar = ${$areaId}; // Pull existing zone content??

			// Format cell properties (b/c nobody likes to side scroll, least of all us dev types)
			//-------------------------------------------------------------------------------------------
			$zProps = " id=\"".$tdid."\" value=\"".$tdid."\"";
			//$zProps .= " style=\"float: left; margin: 10px; width:225; height:85; z-index:50; overflow: hidden;\"";

			// Now echo drop zone cell
			// --------------------------------

			echo "     <div class=\"editTable\" valign=\"top\" align=\"center\" bgcolor=\"".$option_background."\"".$zProps.">joe\n";

//       Testing for cell position
//			echo "      left= (".$leftPX.") right= (".$rightPX.")<br>";
//			echo "      top= (".$topPX.") bottom= (".$botPX.")<br>";

   		$SUleftPX = $leftPX + 210;
         $SUtopPX = $topPX + 1;
   		$SDleftPX = $leftPX + 210;
         $SDtopPX = $topPX + 70;
         // Make pixel.gif smaller in empty cells for better display

         //echo "      <div onmouseover=\"return escape('Scroll Up');\" style=\"border: 2px solid green; position:absolute; display:block; z-index:600; left: 1px; top: 1px; cursor: pointer;\"><img src=../../includes/display_elements/graphics/up-scroll.gif height=15 width=15>here</div>\n";

         $findThis = '[a-zA-Z0-9]';

         if ( eregi("pixel.gif",$contentVar) || !eregi($findThis, $contentVar) )
         {
            $contentVar = "<IMG height=\"50%\" src=\"pixel.gif\" width=\"199\" border=\"0\">";
      	   //$scrollers .= "      <div onmouseover=\"return escape('Scroll Up');\" id='SU".$tdid."' style=\"border: 0px solid red; position:absolute; display:none; z-index:600; left: ".$SUleftPX."px; top: ".$SUtopPX."px; cursor: pointer;\" onClick=\"scroll_up('".$tdid."')\"><img src=\"../../includes/display_elements/graphics/up-scroll.gif\" height=\"15\" width=\"15\" /></div>\n";
      	   //$scrollers .= "      <div onmouseover=\"return escape('Scroll Down');\" id='SD".$tdid."' style=\"border: 0px solid red; position:absolute; display:none; z-index:600; left: ".$SDleftPX."px; top: ".$SDtopPX."px; cursor: pointer;\" onClick=\"scroll_down('".$tdid."')\"><img src=../../includes/display_elements/graphics/down-scroll.gif height=15 width=15></div>\n";
         }else{
      	   //$scrollers .= "      <div onmouseover=\"return escape('Scroll Up');\" id='SU".$tdid."' style=\"border: 0px solid red; position:absolute; display:block; z-index:600; left: ".$SUleftPX."px; top: ".$SUtopPX."px; cursor: pointer;\" onClick=\"scroll_up('".$tdid."')\"><img src=\"../../includes/display_elements/graphics/up-scroll.gif\" height=\"15\" width=\"15\" /></div>\n";
      	   //$scrollers .= "      <div onmouseover=\"return escape('Scroll Down');\" id='SD".$tdid."' style=\"border: 0px solid red; position:absolute; display:block; z-index:600; left: ".$SDleftPX."px; top: ".$SDtopPX."px; cursor: pointer;\" onClick=\"scroll_down('".$tdid."')\"><img src=../../includes/display_elements/graphics/down-scroll.gif height=15 width=15></div>\n";
      	}



         echo "      ".$contentVar."\n";

			// Show contents in textarea for testing
		   //echo "      <br>ContentVar = <br><textarea name=\"mmtestt$y\">".$contentVar."</textarea>\n";


			echo "     </div>\n";

		$leftPX = $leftPX + 230;
      $rightPX = $rightPX + 230;

		# Store all box ids in array for later looping in js
		$box_ids .= ",\"".$tdid."\"";

		}
	$leftPX = 50;
   $rightPX = 275;

   $topPX = $topPX + 90;
   $botPX = $botPX + 90;
	}

   # Strip leading comma
   $box_ids = substr($box_ids, 1);

	?>



<!--- <div id="draggables">
	<div class="drag"></div>
	<div class="drag"></div>
	<div class="drag"></div>
	<div class="drag"></div>
	<div class="drag"></div>
	<div class="drag"></div>
	<div class="drag"></div>
	<div class="drag"></div>
	<div class="drag"></div>
	<div class="drag"></div>
</div> -->

</body>
</html>