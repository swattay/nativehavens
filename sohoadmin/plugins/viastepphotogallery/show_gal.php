<?php
//echo "this is the cwd(".getcwd().")<br>";
  include("../../../pgm-site_config.php");
  include_once("../../program/includes/shared_functions.php");
  //include_once("gallery/pgm-db_config.php");


	echo "<script language=\"javascript\">
	<!--
	window.document.body.scroll = 'no';
	writeCookie();
	eraseCookie(\"users_resolution\");

	function eraseCookie(name) {
		createCookie(name,\"\",-1);
	}
	function writeCookie()
	{
		var today = new Date();
		var the_date = new Date(\"December 31, 2023\");
		var the_cookie_date = the_date.toGMTString();
		var the_cookie = \"users_resolution=\"+ screen.width +\"x\"+ screen.height;
		var the_cookie = the_cookie + \";expires=\" + the_cookie_date;
		document.cookie=the_cookie;
	}
	//-->
	</script>";

	if(isset($HTTP_COOKIE_VARS["users_resolution"]))
	$screen_res = $HTTP_COOKIE_VARS["users_resolution"];
  if(!isset($screen_res)) {  	 $screen_res = "1280x1024";
  }

  eregi("(.*)x(.*)", $screen_res, $tmp);
  $screen_width = $tmp[1];
  $screen_height = $tmp[2];
  if ($screen_height < $screen_width) {  	  $screen_min = $screen_height;
  } else {      $screen_min = $screen_width;
  }
  $max_ratio = 2.0;
  $fit_ratio = 0.5;

  if (!((is_numeric($id)) && ($id>=0)))
    $id=0;

  if (!((is_numeric($m_id)) && ($m_id>=0)))
    $m_id=0;

  if ($slideshow!='run')
    $on_load='';
  else
  {
    $on_load='javascript:timerID=setTimeout(\'displayAlert()\', sec);';
  }


  if ($m_id=='0' || $id=='0')
    die ("Request error");

  $preserve_row='';
  $first_row='';
  $finded=false;
  if (!((is_numeric($layout)) && ($layout>=1) && ($layout<=4)))
    $layout=1;

  $sql="SELECT * FROM `premium_album_images` WHERE `ALBUM_ID` = '$id' ORDER BY `position`";
  if ($sql_result=mysql_query($sql))
    while($curr_row=mysql_fetch_array($sql_result))
    {
      if ($first_row=='')
        $first_row=$curr_row;
      if ($curr_row[0]==$m_id)
      {
        $finded='true';
        if (!$next_row=mysql_fetch_array($sql_result))
          $next_row='';
        break;
      }
      else
        $preserve_row=$curr_row;
    }
  $hrefs='';
  if ($preserve_row!='' && $finded)
    $hrefs.= '<td><a href="show_gal.php?id='.$id.'&m_id='.$preserve_row['PRIKEY']."&layout=$layout".'"><img src="images/prev.png" width="35" height="20" border="0" alt="back"></a></td>';
  else
    $hrefs.= '<td>&nbsp;</td>';


  if ($slideshow!='run')
    $hrefs.= '<td><a id="timeout_href" href="" onClick="javascript:timerID=setTimeout(\'displayAlert()\', sec); document.getElementById(\'timeout_href\').innerHTML=\'<img src=\\\'images/started.png\\\' width=\\\'35\\\' height=\\\'20\\\' border=\\\'0\\\' alt=\\\'slideshow started\\\'>\'; return false;"><img src="images/start.png" width="35" height="20" border="0" alt="start slideshow"></a></td>';
  else
    $hrefs.= '<td><a id="timeout_href" href="" onClick="javascript:if (time_out==\'\'){clearTimeout(timerID); time_out=\'1\'; document.getElementById(\'timeout_href\').innerHTML=\'<img src=\\\'images/resume.png\\\' width=\\\'35\\\' height=\\\'20\\\' border=\\\'0\\\' alt=\\\'resume slideshow\\\'>\'} else {timerID=setTimeout(\'displayAlert()\', sec); document.getElementById(\'timeout_href\').innerHTML=\'<img src=\\\'images/pause.png\\\' width=\\\'35\\\' height=\\\'20\\\' border=\\\'0\\\' alt=\\\'pause slideshow\\\'>\';  time_out=\'\'; }; return false;"><img src="images/pause.png" width="35" height="20" border="0" alt="pause"></a></td>';

  if ($next_row!='' && $finded)
  {
    $hrefs.= '<td><a href="show_gal.php?id='.$id.'&m_id='.$next_row['PRIKEY']."&layout=$layout".'"><img src="images/next.png" width="35" height="20" border="0" alt="next"></a></td>';
    $next_href='show_gal.php?id='.$id.'&slideshow=run&m_id='.$next_row['PRIKEY']."&layout=$layout";
  }
  else
  {
    $hrefs.= '<td>&nbsp;</td>';
    $next_href='show_gal.php?id='.$id.'&slideshow=run&m_id='.$first_row['PRIKEY']."&layout=$layout";
  }
//  $hrefs.= '<td><a href="" onClick="javascript:window.close(); return false;">close</a></td>';
  $curr_row['FILE_SIZE']=ceil($curr_row['FILE_SIZE']/1024);

  $ext   = extract_ext($curr_row['IMAGE_NAME']);
  $fname = basename($curr_row['IMAGE_NAME'], '.'.$ext);
  $fname = 'sohoadmin/plugins/viastepphotogallery/images/premium/content/'.$fname.'.'.$ext;

  if ($curr_row['SIZE_X'] > $curr_row['SIZE_Y']) {
  	$size_x = 1.0;
	$size_y = $curr_row['SIZE_Y'] / $curr_row['SIZE_X'];
  } else {
  	$size_y = 1.0;
	$size_x = $curr_row['SIZE_X'] / $curr_row['SIZE_Y'];
  }

  $sql = "SELECT `resize_flag` FROM `premium_album` WHERE `PRIKEY` = '$id'";
  $res = mysql_query($sql);
  $row = mysql_fetch_array($res);
  if ($row[0] == '') {  	 $resizeFlag = 0;
  } else { 	 $resizeFlag = $row[0];
  }
  if ($resizeFlag == 1) {
	  $size_x = $size_x * $screen_min * $fit_ratio;
	  $size_y = $size_y * $screen_min * $fit_ratio;
  } else {  	  $size_x = $curr_row['SIZE_X'];
  	  $size_y = $curr_row['SIZE_Y'];
  }
//    echo floor($size_x).':'.floor($size_y).'<br />';
  $image='<img width="'.$size_x.'" height="'.$size_y.'" border="0" src="../../../'.$fname.'" alt="'.$row['IMAGE_TITLE'].'" hspace="8"  onLoad="'.$on_load.'">';

//  $size_x = $curr_row['SIZE_X'] + 55;
//  $size_y = $curr_row['SIZE_Y'] + 105;
  if ((strlen($curr_row['IMAGE_TITLE']) == 0) && (strlen($curr_row['IMAGE_DESC']) == 0) && (strlen($curr_row['user_html'] == 0))) {
    $size_x = $size_x - 50;
  } else {  	$size_x = $size_x + 30;
  }
  $size_y = $size_y + 120;
  $title = $curr_row['IMAGE_TITLE'];
  $agent = getenv( 'HTTP_USER_AGENT' );
  $agent = strtoupper($agent);
  if ((strstr($agent, 'FIREFOX') || (strstr($agent, 'MSIE'))) != false) {  	 $size_y = $size_y * 1.095;//(35000 / $size_y);
  }
//  echo floor($size_x).':'.floor($size_y);

  switch ($layout)
  {
    case '1':
      $image='<table border="0" width="100%"><tr valign="top"><td>'.$image.'</td><td width="100"><strong>'.$curr_row['IMAGE_TITLE'].'</strong><br>'.$curr_row['IMAGE_DESC'].'<br />'.$curr_row['user_html'].'</td></tr></table>';
      $size_x=$size_x+100;
    break;
    case '2':
      $image='<strong>'.$curr_row['IMAGE_TITLE'].'</strong><br>'.$image.'<br>'.$curr_row['IMAGE_DESC'].'<br />'.'<br />'.$curr_row['user_html'];
      $size_y=$size_y+40;
    break;
    case '3':
      $image='<strong>'.$curr_row['IMAGE_TITLE'].'</strong><br>'.$image.'<br />'.'<br />'.$curr_row['user_html'];
      $size_y=$size_y+20;
    break;
  }
?>
<HTML>
<HEAD>
<TITLE>Preview: <?php echo $title;?></TITLE>
      <style type="text/css">
      body{
        font-size: 12px;
        margin: 0px 0px 0px 0px;
        padding: 0px 0px 0px 0px;
        font-family: Arial, Verdana;
        line-height: 14px;
      }
      p, td{
        font-size: 12px;
        font-family: Arial, Verdana;
        line-height: 14px;
      }
      </style>


<META HTTP-EQUIV="Content-Type" content="text/html; charset=iso-8859-1">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<META HTTP-EQUIV="Expires" CONTENT="-1">
<meta http-equiv="Page-Enter" content="progid:DXImageTransform.Microsoft.Fade(Duration=2)">
<meta http-equiv="Page-Exit" content="progid:DXImageTransform.Microsoft.Fade(Duration=2)">
    <script language="JavaScript">
     <!--
      i=0;
      sec=10*1000;
      time_out='';

      function displayAlert()
      {
        i++;
        if ('<?php echo $next_href ?>'!='')
          window.location.href='<?php echo $next_href ?>';
        timerID=setTimeout('displayAlert()', sec);
      }
       //-->
    </script>
</HEAD>

<BODY SCROLL="no" bgcolor="white" text="black" link="darkblue" vlink="darkblue" alink="darkblue" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
  <script language="javascript">
    window.resizeTo(<?php echo "$size_x, $size_y"; ?>);
  </script>
  <TABLE WIDTH="100%" BORDER="0">
    <TR>
      <TD align="center"><?php echo $image;?></TD>
    </TR>
    <TR>
      <TD>
        <TABLE ALIGN="CENTER" BORDER="0">
          <TR>
            <?php echo $hrefs;?>
          </TR>
        </TABLE>
      </TD>
    </TR>
  </TABLE>
</BODY>
</HTML>
