<?

###############################################################################
## Soholaunch(R) Site Management Tool
## Version 4.5
##
## Author: 			Dmitry Chaplinsky [soholaunch@viastep.com]
## Homepage:	 	http://www.viastep.com
## Bug Reports: 	http://bugzilla.soholaunch.com
## Release Notes:	sohoadmin/build.dat.php
###############################################################################


session_start();
include_once("../../../program/includes/product_gui.php");

if ($id == "") {
	header("Location: premium_album.php?=SID");
	exit;
}

  if (((is_numeric($columns)) && ($columns>=1) && ($columns<=150)))
  	if (((is_numeric($rows)) && ($rows>=1) && ($rows<=150)))
  	{
	   mysql_query("UPDATE premium_album SET `columns`='$columns',
	   				`rows`='$rows' WHERE PRIKEY = '$id'");
  	}

$thumb_width = 100;
$result = mysql_query("SELECT * FROM premium_album WHERE PRIKEY = '$id'");
while ($mike = mysql_fetch_array($result)) {
	$MOD_TITLE = lang("Preview Album").": $mike[ALBUM_NAME]";
  $thumb_width = $mike["THUMB_WIDTH"];
  $columns = $mike["columns"];
  $rows = $mike["rows"];
}

$BG = "photo_bg.jpg";

//if (!((is_numeric($columns)) && ($columns>=1) && ($columns<=15)))
//  $columns=5;

if (!is_numeric($columns))
  $columns=15;

//if (!((is_numeric($rows)) && ($rows>=1) && ($rows<=50)))
//  $rows=5;

if (!is_numeric($rows))
  $rows=15;

if (!((is_numeric($layout)) && ($layout>=1) && ($layout<=5)))
  $layout=1;

if (!((is_numeric($offset)) && ($offset>=0)))
  $offset=0;

$selected1 = '';
$selected2 = '';
$selected3 = '';
$selected4 = '';
$selected5 = '';

switch ($layout){
  case 1:
    $selected1='selected';
  break;
  case 2:
    $selected2='selected';
  break;
  case 3:
    $selected3='selected';
  break;
  case 4:
    $selected4='selected';
  break;
  case 5:
    $selected5='selected';
  break;
}
?>

<HTML>
<HEAD>
<TITLE>PREMIUM ALBUM PREVIEW</TITLE>

<META HTTP-EQUIV="Content-Type" content="text/html; charset=iso-8859-1">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<META HTTP-EQUIV="Expires" CONTENT="-1">

<link rel="stylesheet" href="../../../program/product_gui.css">
<script type="text/javascript" src="../highslide/highslide-full.js"></script>
<link rel="stylesheet" type="text/css" href="../highslide/highslide.css" />
</head>

<body bgcolor=white text=black link=darkblue vlink=darkblue alink=darkblue leftmargin=0 topmargin=0 marginwidth=0 marginheight=0>
<script language="JavaScript" src="../../../program/includes/display_elements/js_functions.php"></script>
<script language="JavaScript">
<!--

		hs.graphicsDir = '../highslide/graphics/';
		hs.showCredits = false;
		hs.align = 'center';
		hs.transitions = ['expand', 'crossfade'];
		hs.outlineType = 'rounded-white';
		hs.fadeInOut = true;
		hs.dimmingOpacity = 0.75;

		// define the restraining box
		hs.useBox = true;
		hs.width = 640;
		hs.height = 480;

		// Add the controlbar
		hs.addSlideshow({
			//slideshowGroup: 'group1',
			interval: 5000,
			repeat: false,
			useControls: true,
			fixedControls: 'fit',
			overlayOptions: {
				opacity: 1,
				position: 'bottom center',
				hideOnMouseOut: true
			}
		});

//-->
</script>
<!-- 9dce3395e2df2b1fdc28cef945f650a1 -->
<DIV ID="userOpsLayer" style="position:absolute; visibility:visible; left:0px; top:0; width:100%; height:100%; z-index:1; overflow: auto; border: 1px none #000000">

<?

####################################################################
### FOR VISUAL CONSISTANCY; WE USE AN HTML TEMPLATE BUILDER FILE
### LOCATED IN THE /shared FOLDER.  THIS WAY ALL OF OUR MODULE
### INTERFACES LOOK THE SAME. YOU MUST SUPPLY THE VARIABLES:
###
### $MOD_TITLE		Title of this Module
### $THIS_DISPLAY		HTML Content to display to end user
### $BG 			Background Image for content table if used
###
### THIS SAME METHOD SHOULD BE USED WHEN BUILDING ANY OF YOUR OWN
### CUSTOM MODULES.  REMEMBER TO INCLUDE THE HEADER "INCLUDES"
### ABOVE FOR PROPER FUNCTIONALITY WITHIN THE APPLICAITON.
####################################################################

$THIS_DISPLAY .= "
   <FORM method=\"POST\" action=\"preview_album.php?id=".$id."\">
   <TABLE BORDER=0 CELLPADDING=5 CELLSPACING=0 class=\"feature_sub\" width=\"250\">";

	$THIS_DISPLAY .= "<TR>\n";
	$THIS_DISPLAY .= "<td align=center valign=top class=\"fsub_title2\" colspan=\"2\">\n";
	$THIS_DISPLAY .= " ".lang("Preview Settings")."&nbsp;<b>[ <a href=\"premium_album.php\">Back</a> ]</b>&nbsp;<b>[ <a href=\"edit_album.php?id=$id\">Edit</a> ]</b>\n";
	$THIS_DISPLAY .= "</td>\n";
	$THIS_DISPLAY .= "</tr>\n";

	$THIS_DISPLAY .= " <TR>\n";
	$THIS_DISPLAY .= "  <td class=text width=\"100\">\n";
	$THIS_DISPLAY .= "   <B>".lang("Gallery Layout")."</B>\n";
	$THIS_DISPLAY .= "  </td>\n";
	$THIS_DISPLAY .= "  <td class=text>\n";
	$THIS_DISPLAY .= '   <select name="layout" style="width: 130px"><option value="1" '.$selected1.'>Left-Right</option><option value="2" '.$selected2.'>Top-Bottom</option><option value="3" '.$selected3.'>Top</option><option value="4" '.$selected4.'>Image only</option><option value="5" '.$selected5.'>Top title-bottom html</option></select>';
	$THIS_DISPLAY .= "  </td>\n";
	$THIS_DISPLAY .= "</tr>\n";

	$THIS_DISPLAY .= " <TR>\n";
	$THIS_DISPLAY .= "  <td class=text>\n";
	$THIS_DISPLAY .= "   <B>".lang("Columns")."</B>\n";
	$THIS_DISPLAY .= "  </td>\n";
	$THIS_DISPLAY .= "  <td class=text>\n";
	$THIS_DISPLAY .= '   <input type="text" style="width: 30px" class="text" value="'.$columns.'" name="columns">';
	$THIS_DISPLAY .= "  </td>\n";
	$THIS_DISPLAY .= "</tr>\n";
	$THIS_DISPLAY .= " <TR>\n";
	$THIS_DISPLAY .= "  <td class=text>\n";
	$THIS_DISPLAY .= "   <B>".lang("Rows")."</B>\n";
	$THIS_DISPLAY .= "  </td>\n";
	$THIS_DISPLAY .= "  <td class=text>\n";
	$THIS_DISPLAY .= '   <input type="text" style="width: 30px" class="text" value="'.$rows.'" name="rows">';
	$THIS_DISPLAY .= "  </td>\n";
	$THIS_DISPLAY .= "</tr>\n";

	$THIS_DISPLAY .= " <TR>\n";
	$THIS_DISPLAY .= "  <td class=text colspan=\"2\" align=\"center\">\n";
	$THIS_DISPLAY .= '   <input value="'.lang("Update").'" class="btn_blue" style="width: 80px;margin-bottom:4px" type="submit">';
	$THIS_DISPLAY .= "  </td>\n";
	$THIS_DISPLAY .= "</tr>\n";

	$THIS_DISPLAY .= '</TABLE></FORM><br>
                    <table border="0" class="text">
                      <tr>';

  $on_page=$rows*$columns;
  $cnt=0;
// function make_scroll($LinkPrefix, $Count, $Offset, $ItemsOnPage, $html_class = 'small')

  $result = mysql_query("SELECT count(*) as cnt FROM premium_album_images WHERE ALBUM_ID = '$id'");
  if ($row=mysql_fetch_array($result))
    $cnt = $row['cnt'];
  $scroll = make_scroll("preview_album.php?id=$id&columns=$columns&rows=$rows&layout=$layout&offset=", $cnt, $offset, $on_page, 'text');

  $cnt=0;
  $result = mysql_query("SELECT * FROM premium_album_images WHERE ALBUM_ID = '$id' ORDER BY `position` LIMIT ".$offset*$on_page.", $on_page");
  while ($row = mysql_fetch_array($result))
  {
    $ext   = extract_ext($row['IMAGE_NAME']);
    $fname = basename($row['IMAGE_NAME'], '.'.$ext);
    $fname = 'images/premium/content/'.$fname.'_small.'.$ext;
    $row['FILE_SIZE']=ceil($row['FILE_SIZE']/1024);

    switch ($layout){
      case 1:
//        $THIS_DISPLAY .= '<td align="center" valign="top" width="'.$thumb_width.'"><a href="#"  title="'.$row['SIZE_X'].'x'.$row['SIZE_Y'].'; '.$row['FILE_SIZE'].' kb"><img width="'.$thumb_width.'" border="0" src="../'.$fname.'"        alt="'.$row['SIZE_X'].'x'.$row['SIZE_Y'].'; '.$row['FILE_SIZE'].' bytes" hspace="8"></a></td><td width="'.$thumb_width.'" align="center" valign="top"><b>'.$row['IMAGE_TITLE'].'</b><br>'.$row['IMAGE_DESC'].'</td>';
        $THIS_DISPLAY .= '<td align="center" valign="top" width="'.$thumb_width.'"><a href="../' . $row['IMAGE_NAME'] . '" class="highslide" onclick="return hs.expand(this)"><img width="'.$thumb_width.'" border="0" src="../'.$fname.'" alt="' . $row['IMAGE_TITLE'] . '" hspace="8"></a></td><td width="'.$thumb_width.'" align="center" valign="top"><div class="highslide-caption"><b>' . $row['IMAGE_TITLE'] . '</b><br />' . $row['IMAGE_DESC'] . '</div><b>'.$row['IMAGE_TITLE'].'</b><br>'.$row['IMAGE_DESC'].'</td>'."\n";
      break;
      case 2:
        $THIS_DISPLAY .= '<td align="center" valign="top" width="'.$thumb_width.'"><b>'.$row['IMAGE_TITLE'].'</b><br><a href="../' . $row['IMAGE_NAME'] . '" class="highslide" onclick="return hs.expand(this)"><img width="'.$thumb_width.'" border="0" src="../'.$fname.'" alt="' . $row['IMAGE_TITLE'] . '" hspace="8"></a><div class="highslide-caption"><b>' . $row['IMAGE_TITLE'] . '</b><br />' . $row['IMAGE_DESC'] . '</div><br /><small>'.$row['IMAGE_DESC'].'&nbsp</small></td>'."\n";
      break;
      case 3:
//        $THIS_DISPLAY .= '<td align="center" valign="top" width="'.$thumb_width.'"><b>'.$row['IMAGE_TITLE'].'</b><br><a href="#"  title="'.$row['SIZE_X'].'x'.$row['SIZE_Y'].'; '.$row['FILE_SIZE'].' kb"><img width="'.$thumb_width.'" border="0" src="../'.$fname.'" alt="'.$row['SIZE_X'].'x'.$row['SIZE_Y'].'; '.$row['FILE_SIZE'].' bytes" hspace="8"></a><br>&nbsp</td>';
	    $THIS_DISPLAY .= '<td align="center" valign="top" width="'.$thumb_width.'"><b>'.$row['IMAGE_TITLE'].'</b><br><a href="../' . $row['IMAGE_NAME'] . '" class="highslide" onclick="return hs.expand(this)"><img width="'.$thumb_width.'" border="0" src="../'.$fname.'" alt="' . $row['IMAGE_TITLE'] . '" hspace="8"></a><div class="highslide-caption"><b>' . $row['IMAGE_TITLE'] . '</b></div><br />&nbsp</td>'."\n";
      break;
      case 4:
//        $THIS_DISPLAY .= '<td align="center" valign="top" width="'.$thumb_width.'"><a href="#"  title="'.$row['SIZE_X'].'x'.$row['SIZE_Y'].'; '.$row['FILE_SIZE'].' kb"><img width="'.$thumb_width.'" border="0" src="../'.$fname.'" alt="'.$row['SIZE_X'].'x'.$row['SIZE_Y'].'; '.$row['FILE_SIZE'].' bytes" hspace="8"></a><br>&nbsp</td>';
	    $THIS_DISPLAY .= '<td align="center" valign="top" width="'.$thumb_width.'"><a href="../' . $row['IMAGE_NAME'] . '" class="highslide" onclick="return hs.expand(this)"><img width="'.$thumb_width.'" border="0" src="../'.$fname.'" alt="' . $row['IMAGE_TITLE'] . '" hspace="8"></a><br />&nbsp</td>'."\n";
      break;
      case 5:
        $THIS_DISPLAY .= '<td align="center" valign="top" width="'.$thumb_width.'"><b>'.$row['IMAGE_TITLE'].'</b><br><a href="../' . $row['IMAGE_NAME'] . '" class="highslide" onclick="return hs.expand(this)"><img width="'.$thumb_width.'" border="0" src="../'.$fname.'" alt="' . $row['IMAGE_TITLE'] . '" hspace="8"></a><div class="highslide-caption"><b>' . $row['IMAGE_TITLE'] . '</b><br />' . $row['IMAGE_DESC'] . '<br />' . $row['user_html'] . '</div><br /><small>'.$row['user_html'].'&nbsp</small></td>'."\n";
      break;
    }
    $cnt++;
    if ($cnt>=$columns)
    {
      $cnt=0;
      $THIS_DISPLAY.= "</tr><tr>";
    }
  }
  $THIS_DISPLAY.= "</tr></table><br>$scroll";



####################################################################

include("shared/html_build.php");

####################################################################

?>

</div>

</body>
<HEAD>
<META HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE"></HEAD>
</html>
