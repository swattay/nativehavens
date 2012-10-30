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

ob_start();
session_start();
include_once("../../../program/includes/product_gui.php");

ini_set("memory_limit","256M");
if ($id == "") {
	header("Location: premium_album.php?=SID");
	exit;
}
  $s1=substr(get_cfg_var('post_max_size'), 0, -1);
  $s2=substr(get_cfg_var('upload_max_filesize'), 0, -1);
  if ($s1>$s2)
    $max_s=$s1;
  else
    $max_s=$s2;

$display='none';

if ($_GET['upload']=='true')
  $display='';
$thumb_width = 100;
$result = mysql_query("SELECT * FROM premium_album WHERE PRIKEY = '$id'");
while ($mike = mysql_fetch_array($result)) {
	$MOD_TITLE = lang("Edit Album").": $mike[ALBUM_NAME]";
  $album_name = $mike[ALBUM_NAME];
  $thumb_width = $mike["THUMB_WIDTH"];
  $columns = $mike["columns"];
  $rows = $mike["rows"];
  $RESIZE_TYPE = $mike["RESIZE_TYPE"];
  switch ($RESIZE_TYPE)
  {
    case 'no': $ARESIZE=1; break;
    case 'width': $ARESIZE=2; break;
    case 'biggest': $ARESIZE=3; break;
    default:
      $ARESIZE=1;
    break;
  }

  $ABIG_SIZE = $mike["BIG_SIZE"];
}

$BG = "photo_bg.jpg";

if ($action=='save_image')
{
  if (is_numeric($image_id))
  {
  	$title = stripslashes($title);
    $title = addslashes($title);

  	$desc = stripslashes($desc);
    $desc=addslashes($desc);

	$html = stripslashes($_POST['ihtml']);
	echo "<!-- ".$html." -->";
	if ( ! get_magic_quotes_gpc()) {
		$html = addslashes($_POST['ihtml']);
	}
	else {
		$html = $_POST['ihtml'];
	}
//	$html = str_ireplace('<script','' ,$html );
	mysql_query("UPDATE premium_album_images SET `IMAGE_TITLE`='$title', `IMAGE_DESC`='$desc', `user_html`='$html' WHERE `PRIKEY`='$image_id'");
  }
}

if ($action=='update')
{
  	// Check for duplicates and don't allow
    $message='';
    if (!((is_numeric($a_method)) && ($a_method>=1) && ($a_method<=3)))
      $a_method=1;

    if (!((is_numeric($RESIZE)) && ($RESIZE>=1) && ($RESIZE<=3)))
      $RESIZE=1;
    switch ($RESIZE)
    {
      case '1': $RESIZE='no'; break;
      case '2': $RESIZE='width'; break;
      case '3': $RESIZE='biggest'; break;
    }

    if (!((is_numeric($BIG_SIZE)) && ($BIG_SIZE>=200) && ($BIG_SIZE<=1600)))
      $BIG_SIZE=800;

    $ABIG_SIZE=$BIG_SIZE;

  	$NEWGROUP = ucwords($NEWGROUP);
  	$NEWGROUP = stripslashes($NEWGROUP);
  	$NEWGROUP = addslashes($NEWGROUP);

  	$result = mysql_query("SELECT * FROM premium_album");
  	$num_groups = mysql_num_rows($result);

    if (!((is_numeric($THUMB_SIZE)) && ($THUMB_SIZE>=25) && ($THUMB_SIZE<=500)))
      $THUMB_SIZE=$thumb_width;
	if (!((is_numeric($COL_COUNT)) && ($COL_COUNT>=1) && ($COL_COUNT<=150)))
		$COL_COUNT = $columns;

	if (!((is_numeric($ROW_COUNT)) && ($ROW_COUNT>=1) && ($ROW_COUNT<=150)))
		$ROW_COUNT = $rows;
	$columns = $COL_COUNT;
	$rows = $ROW_COUNT;
		if ($NEWGROUP != "") {
			if (mysql_query("UPDATE premium_album SET `ALBUM_NAME`='$NEWGROUP', `THUMB_WIDTH`='$THUMB_SIZE', `RESIZE_TYPE`='$RESIZE', `BIG_SIZE`='$BIG_SIZE',
					 `columns`='$COL_COUNT', `rows`='$ROW_COUNT', `resize_flag`='$resize_flag' WHERE `PRIKEY`='$id'"))
      {
//        echo "UPDATE premium_album SET `ALBUM_NAME`='$NEWGROUP', `THUMB_WIDTH`='$THUMB_SIZE', `RESIZE_TYPE`='$RESIZE', `BIG_SIZE`='$BIG_SIZE' WHERE `PRIKEY`='$id'";
        $album_name=stripslashes($NEWGROUP);
        $a_file = $_FILES['THUMB']['tmp_name'];
        $image_name='';

        if (is_uploaded_file($a_file))
        {
          preg_match("/\\.(.*)$/", $_FILES['THUMB']['name'], $m);
          $thumb_name='../images/premium/'.$id."_thumb.jpg";
          $db_name='images/premium/'.$id."_thumb.jpg";
          $image_name='../images/premium/'.$id.$m[0];
          $image_name2='../images/premium/'.$id.".jpg";

          if (!move_uploaded_file($_FILES['THUMB']['tmp_name'], $image_name) || !chmod($image_name, 0644))
            $message="Error while loading image";
          else
          {
            if (create_thumbnail($image_name, $thumb_name, $THUMB_SIZE))
            {
              if (!ToJpeg($image_name, $image_name2))
                $message="Error while loading image#".$matches[1]." (must be in jpeg, gif or png format)";
              if ($image_name!=$image_name2)
                unlink($image_name);

              mysql_query("UPDATE premium_album SET ALBUM_THUMB='$db_name' WHERE PRIKEY='$id'");
            }
            else
            {
              unlink($image_name);
              $thumb_name='';
              $message="Error while loading image (must be in jpeg, gif or png format)";
            }
          }
        }
        elseif ($thumb_width!=$THUMB_SIZE)
        {
          $thumb_name='../images/premium/'.$id."_thumb.jpg";
          $image_name2='../images/premium/'.$id.".jpg";
          create_thumbnail($image_name2, $thumb_name, $THUMB_SIZE);
        }
        if ($thumb_width!=$THUMB_SIZE)
        {
          $result = mysql_query("SELECT * FROM premium_album_images WHERE ALBUM_ID = '$id' ORDER BY `position`");
          while ($row = mysql_fetch_array($result))
          {
            $ext   = extract_ext($row['IMAGE_NAME']);
            $fname = basename($row['IMAGE_NAME'], '.'.$ext);
            $fname = '../images/premium/content/'.$fname.'_small.'.$ext;
            create_thumbnail('../'.$row['IMAGE_NAME'], $fname, $THUMB_SIZE);
          }
        }

        $thumb_width=$THUMB_SIZE;


        switch ($a_method)
        {
          case '1':
            $up_files = array();
            foreach ($_FILES as $key=>$value)
            {
              if ((preg_match('/pict_(\d+)/', $key, $matches))&&($_FILES[$key]['name']!=''))
              {
                $up_files[$matches[1]]=array($_FILES[$key]['name'], $_FILES[$key]['tmp_name']);
              }
            }

            ksort($up_files);

            foreach ($up_files as $key=>$value)
            {
              $a_file = $value[1];
              $image_name='';
              if (is_uploaded_file($a_file))
              {
                preg_match("/\\.(.*)$/", $value[0], $m);

			  	$title = stripslashes($_POST['title_'.$key]);
			    $title = addslashes($title);

			  	$desc = stripslashes($_POST['desc_'.$key]);
			    $desc=addslashes($desc);
                $pos = mysql_fetch_array(mysql_query("SELECT max(`position`) FROM `premium_album_images` WHERE `ALBUM_ID`='$id'"));
                $pos = $pos[0]+1;
                mysql_query("INSERT INTO `premium_album_images` (`ALBUM_ID`, `IMAGE_NAME`, `IMAGE_TITLE`, `IMAGE_DESC`,`position`) VALUES ('$id', '', '$title', '$desc','$pos')");
                $image_id=mysql_insert_id();

                $m[0]=strtolower($m[0]);
                $thumb_name='../images/premium/content/'.$image_id."_small.jpg";
                $image_name='../images/premium/content/'.$image_id.$m[0];
                $image_name2='../images/premium/content/'.$image_id.".jpg";

                if (!move_uploaded_file($value[1], $image_name) || !chmod($image_name, 0644))
                  $message="Error while loading image #".$key;
                else
                {

                  if (!create_thumbnail($image_name, $thumb_name, $THUMB_SIZE))
                  {
                    $thumb_name='';
                    $message="Error while loading image#$key (must be in jpeg, gif or png format)";
                    mysql_query("DELETE FROM `premium_album_images` WHERE `PRIKEY`='$image_id'");
                  }
                  else
                  {
                    if (!ToJpeg($image_name, $image_name2, $RESIZE, $BIG_SIZE))
                    {
                      $message="Error while loading image#$key (must be in jpeg, gif or png format)";
                      mysql_query("DELETE FROM `premium_album_images` WHERE `PRIKEY`='$image_id'");
                    }
                    else
                    {
                      if ($image_name!=$image_name2)
                        unlink($image_name);
                      $info = get_image_info($image_name2);
                      mysql_query("UPDATE
                                     `premium_album_images`
                                   SET
                                     `IMAGE_NAME`='images/premium/content/".$image_id.".jpg"."',
                                     `SIZE_X`=".$info['width'].",
                                     `SIZE_Y`=".$info['height'].",
                                     `FILE_SIZE`=".$info['size']."
                                   WHERE
                                     `PRIKEY`='$image_id'");
                    }
                  }
                }
              }
            }
          break;
          case '2':
            $IISupload = $doc_root."/sohoadmin/plugins/viastepphotogallery/unzips/";
            $uploadDir = $doc_root."/sohoadmin/plugins/viastepphotogallery/temp/";
            $uploadFile = $uploadDir . $_FILES['zip']['name'];
            $this_zip = $_FILES['zip']['name'];
            my_rmdirr($uploadDir);

            if (move_uploaded_file($_FILES['zip']['tmp_name'], $uploadFile))
            {
              $go = 0;
              $mess='';
              if (!chmod($uploadFile, 0777))
                $mess.="Cannot set 777 chmode for $uploadFile";

              $odir = getcwd();
              if (!chdir($uploadDir))
                $mess.="Cannot change dir to $uploadDir";

              $fdrName = eregi_replace(".zip",  "", $this_zip);

              if($go == 0 && $mess==''){
                 if (eregi("IIS", $SERVER_SOFTWARE)||eregi("Win32", $SERVER_SOFTWARE))
                 {
                    if(!is_dir($IISupload))
                    {
                       if (!mkdir('../unzips'))
                         $mess.='Cannot create a dir unzips. Please set permission 0777 for '.$IISupload;
                       $sshRez .= shell_exec ("expand -r -F:*.* ..\unzip.cab ..\unzips\ ");
                    }
                    $sshRez = shell_exec ("..\unzips\unzip -o -L $this_zip");
                 } else {
                    $sshRez = shell_exec("unzip $this_zip");
                 }
                 if(is_dir($uploadDir)){
                    unlink($this_zip);
                    $sshRez .= shell_exec("chmod -R 0755 $fdrName");
                    //echo "File unzipped and chmoded!";
                 }else{
                    echo "Could not delete uploaded zip file!<br>Folder (".$fdrName.") not Chmoded!";
                 }
              }
            }
            else
              $mess.='Cannot rename uploaded file. Try with smaller file or try to set permission 0777 for '.$uploadDir;

            echo $mess;
            $dir = dir($uploadDir);
            $key=0;
            $arr = array();
            while (false !== $entry = $dir->read()) {
                // Skip pointers
                if ($entry == '.' || $entry == '..') {
                    continue;
                }
                array_push($arr, $entry);
            }
            natsort($arr);
       		$arr = array_reverse($arr);
//            while (false !== $entry = $dir->read()) {
	         while ( true ) {
	         	$entry = array_pop($arr);
	         	if ((!isset($entry)) || ($entry == NULL)) {
	         		break;
	         	}
                // Skip pointers
                if ($entry == '.' || $entry == '..') {
                    continue;
                }
                $key++;
                $a_file = $entry;
                $image_name='';
                preg_match("/\\.(.*)$/", $a_file, $m);

                $pos = mysql_fetch_array(mysql_query("SELECT max(`position`) FROM `premium_album_images` WHERE `ALBUM_ID`='$id'"));
                $pos = $pos[0]+1;
                mysql_query("INSERT INTO `premium_album_images` (`ALBUM_ID`, `IMAGE_NAME`, `IMAGE_TITLE`, `IMAGE_DESC`,`position`) VALUES ('$id', '', '$title', '$desc','$pos')");
                $image_id=mysql_insert_id();

                $m[0]=strtolower($m[0]);
                $thumb_name='../images/premium/content/'.$image_id."_small.jpg";
                $image_name='../images/premium/content/'.$image_id.$m[0];
                $image_name2='../images/premium/content/'.$image_id.".jpg";

                rename($uploadDir.$a_file, $image_name);

                if (!create_thumbnail($image_name, $thumb_name, $THUMB_SIZE))
                {
                  $thumb_name='';
                  $message="Error while loading image#$key (must be in jpeg, gif or png format)";
                  mysql_query("DELETE FROM `premium_album_images` WHERE `PRIKEY`='$image_id'");
                }
                else
                {
                  if (!ToJpeg($image_name, $image_name2, $RESIZE, $BIG_SIZE))
                  {
                    $message="Error while loading image#$key (must be in jpeg, gif or png format)";
                    mysql_query("DELETE FROM `premium_album_images` WHERE `PRIKEY`='$image_id'");
                  }
                  else
                  {
                    if ($image_name!=$image_name2)
                      unlink($image_name);
                    $info = get_image_info($image_name2);
                    mysql_query("UPDATE
                                   `premium_album_images`
                                 SET
                                   `IMAGE_NAME`='images/premium/content/".$image_id.".jpg"."',
                                   `SIZE_X`=".$info['width'].",
                                   `SIZE_Y`=".$info['height'].",
                                   `FILE_SIZE`=".$info['size']."
                                 WHERE
                                   `PRIKEY`='$image_id'");
                  }
                }
             }

            chdir($odir);

          break;

          case '3':
            $dir = dir($folder);
            $key=0;
            while (false !== $entry = $dir->read()) {
                // Skip pointers
                if ($entry == '.' || $entry == '..') {
                    continue;
                }

                $key++;
                $a_file = $entry;
                $image_name='';
                preg_match("/\\.(.*)$/", $a_file, $m);

                $pos = mysql_fetch_array(mysql_query("SELECT max(`position`) FROM `premium_album_images` WHERE `ALBUM_ID`='$id'"));
                $pos = $pos[0]+1;
                mysql_query("INSERT INTO `premium_album_images` (`ALBUM_ID`, `IMAGE_NAME`, `IMAGE_TITLE`, `IMAGE_DESC`,`position`) VALUES ('$id', '', '$title', '$desc','$pos')");
                $image_id=mysql_insert_id();

                $m[0]=strtolower($m[0]);
                $thumb_name='../images/premium/content/'.$image_id."_small.jpg";
                $image_name='../images/premium/content/'.$image_id.$m[0];
                $image_name2='../images/premium/content/'.$image_id.".jpg";

                copy($folder.'/'.$a_file, $image_name);

                if (!create_thumbnail($image_name, $thumb_name, $THUMB_SIZE))
                {
                  $thumb_name='';
                  $message="Error while loading image#$key (must be in jpeg, gif or png format)";
                  mysql_query("DELETE FROM `premium_album_images` WHERE `PRIKEY`='$image_id'");
                }
                else
                {
                  if (!ToJpeg($image_name, $image_name2, $RESIZE, $BIG_SIZE))
                  {
                    $message="Error while loading image#$key (must be in jpeg, gif or png format)";
                    mysql_query("DELETE FROM `premium_album_images` WHERE `PRIKEY`='$image_id'");
                  }
                  else
                  {
                    if ($image_name!=$image_name2)
                      unlink($image_name);
                    $info = get_image_info($image_name2);
                    mysql_query("UPDATE
                                   `premium_album_images`
                                 SET
                                   `IMAGE_NAME`='images/premium/content/".$image_id.".jpg"."',
                                   `SIZE_X`=".$info['width'].",
                                   `SIZE_Y`=".$info['height'].",
                                   `FILE_SIZE`=".$info['size']."
                                 WHERE
                                   `PRIKEY`='$image_id'");
                  }
                }
             }

            chdir($odir);

          break;
        }

        if ($message<>'')
          echo $message."<br>";
      }
      else
			  echo mysql_error();
		}

  switch ($RESIZE)
  {
    case 'no': $ARESIZE=1; break;
    case 'width': $ARESIZE=2; break;
    case 'biggest': $ARESIZE=3; break;
    default:
      $ARESIZE=1;
    break;
  }
}

if ($action=='up')
{
  if (is_numeric($image_id))
  {
    $preserve_row='';
    $finded=false;
    $sql="SELECT `PRIKEY`, `IMAGE_NAME`, `SIZE_X`, `SIZE_Y`, `FILE_SIZE`, `IMAGE_TITLE`, `IMAGE_DESC` FROM `premium_album_images` WHERE `ALBUM_ID`='$id' ORDER BY `position`";
    if ($sql_result=mysql_query($sql))
      while($curr_row=mysql_fetch_array($sql_result))
      {
        if ($curr_row[0]==$image_id)
        {
          $finded='true';
          break;
        }
        else
          $preserve_row=$curr_row;
      }
    if (($preserve_row!='')&&($finded))
    {
      if (!mysql_query("UPDATE
                          `premium_album_images`
                        SET
                          `IMAGE_NAME`='$curr_row[1]',
                          `SIZE_X`='$curr_row[2]',
                          `SIZE_Y`='$curr_row[3]',
                          `FILE_SIZE`='$curr_row[4]',
                          `IMAGE_TITLE`='$curr_row[5]',
                          `IMAGE_DESC`='$curr_row[6]'
                        WHERE
                          `PRIKEY`='$preserve_row[0]'"))
        $message="Error when changing images order";

      if (!mysql_query("UPDATE
                          `premium_album_images`
                        SET
                          `IMAGE_NAME`='$preserve_row[1]',
                          `SIZE_X`='$preserve_row[2]',
                          `SIZE_Y`='$preserve_row[3]',
                          `FILE_SIZE`='$preserve_row[4]',
                          `IMAGE_TITLE`='$preserve_row[5]',
                          `IMAGE_DESC`='$preserve_row[6]'
                        WHERE
                          `PRIKEY`='$curr_row[0]'"))
        $message="Error when changing images order";
    }
  }
}

if ($action=='down')
{
  if (is_numeric($image_id))
  {
    $preserve_row='';
    $finded=false;
    $sql="SELECT `PRIKEY`, `IMAGE_NAME`, `SIZE_X`, `SIZE_Y`, `FILE_SIZE`, `IMAGE_TITLE`, `IMAGE_DESC` FROM `premium_album_images` WHERE `ALBUM_ID`='$id' ORDER BY `position`";
    if ($sql_result=mysql_query($sql))
      while($curr_row=mysql_fetch_array($sql_result))
      {
        if ($preserve_row[0]==$image_id)
        {
          $finded='true';
          break;
        }
        else
          $preserve_row=$curr_row;
      }

    if (($preserve_row!='')&&($finded))
    {
      if (!mysql_query("UPDATE
                          `premium_album_images`
                        SET
                          `IMAGE_NAME`='$curr_row[1]',
                          `SIZE_X`='$curr_row[2]',
                          `SIZE_Y`='$curr_row[3]',
                          `FILE_SIZE`='$curr_row[4]',
                          `IMAGE_TITLE`='$curr_row[5]',
                          `IMAGE_DESC`='$curr_row[6]'
                        WHERE
                          `PRIKEY`='$preserve_row[0]'"))
        $message="Error when changing images order";

      if (!mysql_query("UPDATE
                          `premium_album_images`
                        SET
                          `IMAGE_NAME`='$preserve_row[1]',
                          `SIZE_X`='$preserve_row[2]',
                          `SIZE_Y`='$preserve_row[3]',
                          `FILE_SIZE`='$preserve_row[4]',
                          `IMAGE_TITLE`='$preserve_row[5]',
                          `IMAGE_DESC`='$preserve_row[6]'
                        WHERE
                          `PRIKEY`='$curr_row[0]'"))
        $message="Error when changing images order";
    }
  }
}

if ($action=='moveto')
{
	$pos = $_GET['pos'];
	$topos = $_GET['topos'];
	if($pos==$topos)
	{
		$message = 'Error when changing images order';
	}
	else
	{
		$desc = false;
		if($pos > $topos)
		{
			$start = $topos;
			$finish = $pos;
			$desc = true;
		}
		else
		{
			$start = $pos;
			$finish = $topos;
		}
		$sql = "SELECT `position`,`PRIKEY` from `premium_album_images` WHERE `ALBUM_ID`='$id'
				ORDER BY `position`
				LIMIT ".($start-1).','.($finish-$start+1);
		$result = mysql_query($sql);
		$count = mysql_num_rows($result)-1;
  		if($desc)
  		{
  			mysql_data_seek($result,$count);
  			$count--;
  		}
  		$row = mysql_fetch_array($result);
  		$newpos = $row['position'];
  		$i_key = $row['PRIKEY'];
  		if($desc)
  		{
  			mysql_data_seek($result,$count);
  			$count--;
  		}
  		while(($row = mysql_fetch_array($result))&&($count>=-1))
  		{
  			$t = $row['position'];
  			$key = $row['PRIKEY'];
  			mysql_query("UPDATE `premium_album_images` SET `position`='$newpos' WHERE `PRIKEY`='$key'");
			$newpos = $t;
	  		if($desc)
	  		{
	  			mysql_data_seek($result,$count);
	  			$count--;
	  		}
  		}
  		mysql_query("UPDATE `premium_album_images` SET `position`='$newpos' WHERE `PRIKEY`='$i_key'");
	}
}

if ($action=='del')
{
  if (is_numeric($image_id))
  {
    $res=mysql_query("SELECT * FROM premium_album_images WHERE `PRIKEY`='$image_id'");
    if ($row=mysql_fetch_array($res))
    {
      @unlink('../'.$row['IMAGE_NAME']);

      $ext   = extract_ext($row['IMAGE_NAME']);
      $fname = basename($row['IMAGE_NAME'], '.'.$ext);
      $fname = 'images/premium/content/'.$fname.'_small.'.$ext;
      @unlink('../'.$fname);
    }
    $res=mysql_query("DELETE FROM premium_album_images WHERE `PRIKEY`='$image_id'");
  }
}
$messages=ob_get_contents();
ob_end_clean();
?>

<HTML>
<HEAD>
<TITLE>EDIT PREMIUM ALBUM</TITLE>

<META HTTP-EQUIV="Content-Type" content="text/html; charset=iso-8859-1">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<META HTTP-EQUIV="Expires" CONTENT="-1">

<link rel="stylesheet" href="../../../program/product_gui.css">
<script language="JavaScript" src="../../../program/includes/display_elements/js_functions.php"></script>


<script language="JavaScript">
<!--
function SV2_findObj(n, d) { //v3.0
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=SV2_findObj(n,d.layers[i].document); return x;
}

function SV2_popupMsg(msg) { //v1.0
  alert(msg);
}
function SV2_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
function popupWindow(url) {
  window.top.open(url,'Preview','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,screenX=150,screenY=150,top=150,left=150')
}

function delete_image(id, sel) {

	// What album is selected?
//	var sel = DELFORM.id.value;

	if (sel != "") {

		var tiny = window.confirm('Are you sure you wish to delete this Image?');
		if (tiny != false) {
			window.location = 'edit_album.php?id='+id+'&image_id='+sel+'&action=del';
		}

	} // End If ""

}

var p = "Premium Album";
parent.frames.footer.setPage(p);


//-->
</script>

</head>

<body bgcolor=white text=black link=darkblue vlink=darkblue alink=darkblue leftmargin=0 topmargin=0 marginwidth=0 marginheight=0>

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
$sql = "SELECT `resize_flag` FROM `premium_album` WHERE PRIKEY = '$id'";
$res = mysql_query($sql);
$row = mysql_fetch_array($res);
$resOpYes = '<option value="1" selected="true">Yes</option>
					<option value="0">No</option>';
$resOpNo = '<option value="1">Yes</option>
					<option value="0" selected="true">No</option>';
if ($row[0] == '') {
	$resizeOptions = $resOpYes;
} else {
	if ($row[0] == '1') {
		$resizeOptions = $resOpYes;
	} else {
		$resizeOptions = $resOpNo;
	}
}

$THIS_DISPLAY = "$messages";
$THIS_DISPLAY .= "<center><b>[ <a href=\"premium_album.php\">Back</a> ]</b>&nbsp;<b>[ <a href=\"preview_album.php?id=$id\">Preview</a> ]</b></center>";

	$THIS_DISPLAY .= "<form method=\"POST\" action=\"edit_album.php?id=$id\" enctype=\"multipart/form-data\">\n";
	$THIS_DISPLAY .= "<input type=\"hidden\" name=\"action\" value=\"update\">\n";

	$THIS_DISPLAY .= "<BR><BR><table border=\"0\" cellpadding=\"8\" cellspacing=\"0\" width=\"80%\" align=\"center\" style=\"border: 1px inset black;\">\n";

	$THIS_DISPLAY .= "<tr><td align=\"left\" valign=\"top\" class=\"text\" bgcolor=\"#EFEFEF\" style=\"color: #000099;\">\n";

		$THIS_DISPLAY .= "<div style=\"background-color: #CCCCCC; border: 1px solid #000000;\">
                        <center><B><font size=\"3\">".lang("Edit Premium Album")."</font></b></center></div><br>
                        <table border=\"0\" align=\"center\" class=\"text\" >
                          <tr>
                            <td>
                              <b><u>".lang("Enter Premium Album Name").":</u></b>
                            </td>
                            <td>
                              <INPUT TYPE=TEXT NAME=NEWGROUP CLASS=text STYLE='width: 184px;' VALUE=\"$album_name\">&nbsp;\n
                            </td>
                            <td rowspan=\"2\">
                              <input type=\"SUBMIT\" value=\"Update Album\" style=\"height: 50px\" class=\"btn_save\" onMouseover=\"this.className='btn_saveon';\" onMouseout=\"this.className='btn_save';\">
                            </td>
                          </tr>
                          <tr>
                            <td width=\"150\">
                              <b><u>".lang("Select Image For Album").":</u></b>
                            </td>
                            <td>
                              <INPUT TYPE=FILE NAME=THUMB CLASS=text>
                            </td>
                          </tr>
                          <tr>
                            <td width=\"150\">
                              <b><u>".lang("Select Thumbnail Width").":</u></b>
                            </td>
                            <td>
                              <INPUT TYPE=TEXT NAME=THUMB_SIZE STYLE='width: 80px;' CLASS=text VALUE=\"$thumb_width\">
                            </td>
                          </tr>
                                                    <tr>
                            <td width=\"150\">
                              <b><u>".lang("Columns").":</u></b>
                            </td>
                            <td>
                              <INPUT TYPE=TEXT NAME=COL_COUNT STYLE='width: 80px;' CLASS=text VALUE=\"$columns\">
                            </td>
                          </tr>
                          <tr>
                            <td width=\"150\">
                              <b><u>".lang("Rows").":</u></b>
                            </td>
                            <td>
                              <INPUT TYPE=TEXT NAME=ROW_COUNT STYLE='width: 80px;' CLASS=text VALUE=\"$rows\">
                            </td>
                          </tr>

                          <tr>
                            <td width=\"150\">
                              <b><u>".lang("Resize images").":</u></b>
                            </td>
                            <td>
								<select name=\"resize_flag\">
								" . $resizeOptions . "
								</select>
                            </td>
                          </tr>

                          <tr id=\"resize_row\" style=\"display: none\">
                            <td width=\"150\">
                              <b><u>".lang("Resize uploaded images (eg. from camera)?").":</u></b>
                            </td>
                            <td>
                                <select name=\"RESIZE\" style=\"width: 110px\" onChange=\"javascript:
                                    switch(this.value)
                                    {
                                      case '1':
                                        document.getElementById('big_size_span').style.display='none';
                                      break;
                                      case '2':
                                        document.getElementById('big_size_span').style.display='';
                                      break;
                                      case '3':
                                        document.getElementById('big_size_span').style.display='';
                                      break;
                                    }
                                  \">
                                  <option value=\"1\">".lang("No")."</option>
                                  <option value=\"2\">".lang("Set width to")."</option>
                                  <option value=\"3\">".lang("Set biggest side to")."</option>
                                </select>
                                <span id=\"big_size_span\" style=\"display:none\"><input style=\"width: 35px\" type=\"text\" class=\"text\" name=\"BIG_SIZE\" value=\"$ABIG_SIZE\"> px.</span>
                            </td>
                          </tr>

                          <tr>
                            <td colspan=\"3\">
                              <b><a href=\"#\" onClick=\"javascript:if (document.all.show_more.style.display=='none'){document.all.show_more1.style.display=''}; document.all.show_more.style.display=''; document.all.RESIZE.value=$ARESIZE; if ($ARESIZE!=1) {document.getElementById('big_size_span').style.display=''}; document.all.a_method.value=1; document.all.resize_row.style.display=''\">".lang("More...")."</a></b>
                            </td>
                          </tr>
                          <tr id=\"show_more\" style=\"display: $display\">
                              <td colspan=\"3\" align=\"center\">
                                <br>
                                <font color=\"red\">".lang('Due your server settings you can upload ').'<b>'.$max_s.lang('mb</b> at once')."</font>
                                <br><br>
                                <select name=\"a_method\" onChange=\"javascript:
                                    document.all.show_more1.style.display='none';
                                    document.all.show_more2.style.display='none';
                                    document.all.show_more3.style.display='none';
                                    switch(this.value)
                                    {
                                      case '1':
                                        document.all.show_more1.style.display='';
                                      break;
                                      case '2':
                                        document.all.show_more2.style.display='';
                                      break;
                                      case '3':
                                        document.all.show_more3.style.display='';
                                      break;
                                    }
                                  \">
                                  <option value=\"1\" selected>".lang("Upload files")."</option>
                                  <option value=\"2\">".lang("Upload .zip")."</option>
                                  <option value=\"3\">".lang("Add from server dir")."</option>
                                </select>
                              </td>
                          </tr>
                          <tr id=\"show_more2\" style=\"display: none\">
                            <td colspan=\"3\">
                              <center><b><u>".lang("Select a .zip file with images").":</u></b><br>
                              <input style=\"margin: 5px 0px\" CLASS=text name=\"zip\" value=\"\" type=\"file\">
                              <br>
                              <input type=\"SUBMIT\" value=\"Update Album\" class=\"btn_save\" onMouseover=\"this.className='btn_saveon';\" onMouseout=\"this.className='btn_save';\">
                            </td>
                          </tr>
                          <tr id=\"show_more3\" style=\"display: none\">
                            <td colspan=\"3\">
                              <center><b><u>".lang("Select a server folder").":</u></b><br>
                              <input style=\"margin: 5px 0px\" CLASS=text name=\"folder\" value=\"".$_SERVER['DOCUMENT_ROOT'].'/'."\" type=\"text\" size=\"45\">
                              <br>
                              <input type=\"SUBMIT\" value=\"Update Album\" class=\"btn_save\" onMouseover=\"this.className='btn_saveon';\" onMouseout=\"this.className='btn_save';\">
                            </td>
                          </tr>
                          <tr id=\"show_more1\" style=\"display: $display\">
                            <td colspan=\"3\">
                              <center><b><u>".lang("Add Images To The Album").":</u></b>

                              <br>
                              <table border=\"0\" class=\"text\">
                                <tr>
                                  <td width=\"100\"><b><u>".lang("Picture #")."1:</u></b></td>
                                  <td>
                                    <input CLASS=text name=\"pict_1\" value=\"\" type=\"file\">
                                  </td>
                                 </tr>
                                <tr valign=\"top\">
                                  <td><b><u>".lang("Title #")."1:</u></b></td>
                                  <td>
                                    <input CLASS=text STYLE='width: 184px;' name=\"title_1\" value=\"\" type=\"text\">
                                  </td>
                                </tr>
                                <tr valign=\"top\">
                                  <td><b><u>".lang("Description #")."1:</u></b></td>
                                  <td>
                                    <input CLASS=text STYLE='width: 184px;' name=\"desc_1\" value=\"\" type=\"text\"><br>&nbsp;
                                  </td>
                                </tr>

                                 <tr>
                                  <td width=\"100\"><b><u>".lang("Picture #")."2:</u></b></td>
                                  <td>
                                    <input CLASS=text name=\"pict_2\" value=\"\" type=\"file\">
                                  </td>
                                 </tr>
                                <tr valign=\"top\">
                                  <td><b><u>".lang("Title #")."2:</u></b></td>
                                  <td>
                                    <input CLASS=text STYLE='width: 184px;' name=\"title_2\" value=\"\" type=\"text\">
                                  </td>
                                </tr>
                                <tr valign=\"top\">
                                  <td><b><u>".lang("Description #")."2:</u></b></td>
                                  <td>
                                    <input CLASS=text STYLE='width: 184px;' name=\"desc_2\" value=\"\" type=\"text\"><br>&nbsp;
                                  </td>
                                </tr>

                                <tr>
                                  <td width=\"100\"><b><u>".lang("Picture #")."3:</u></b></td>
                                  <td>
                                    <input CLASS=text name=\"pict_3\" value=\"\" type=\"file\">
                                  </td>
                                 </tr>
                                <tr valign=\"top\">
                                  <td><b><u>".lang("Title #")."3:</u></b></td>
                                  <td>
                                    <input CLASS=text STYLE='width: 184px;' name=\"title_3\" value=\"\" type=\"text\">
                                  </td>
                                </tr>
                                <tr valign=\"top\">
                                  <td><b><u>".lang("Description #")."3:</u></b></td>
                                  <td>
                                    <input CLASS=text STYLE='width: 184px;' name=\"desc_3\" value=\"\" type=\"text\"><br>&nbsp;
                                  </td>
                                </tr>

                                <tr>
                                  <td width=\"100\"><b><u>".lang("Picture #")."4:</u></b></td>
                                  <td>
                                    <input CLASS=text name=\"pict_4\" value=\"\" type=\"file\">
                                  </td>
                                 </tr>
                                <tr valign=\"top\">
                                  <td><b><u>".lang("Title #")."4:</u></b></td>
                                  <td>
                                    <input CLASS=text STYLE='width: 184px;' name=\"title_4\" value=\"\" type=\"text\">
                                  </td>
                                </tr>
                                <tr valign=\"top\">
                                  <td><b><u>".lang("Description #")."4:</u></b></td>
                                  <td>
                                    <input CLASS=text STYLE='width: 184px;' name=\"desc_4\" value=\"\" type=\"text\"><br>&nbsp;
                                  </td>
                                </tr>

                                <tr>
                                  <td width=\"100\"><b><u>".lang("Picture #")."5:</u></b></td>
                                  <td>
                                    <input CLASS=text name=\"pict_5\" value=\"\" type=\"file\">
                                  </td>
                                 </tr>
                                <tr valign=\"top\">
                                  <td><b><u>".lang("Title #")."5:</u></b></td>
                                  <td>
                                    <input CLASS=text STYLE='width: 184px;' name=\"title_5\" value=\"\" type=\"text\">
                                  </td>
                                </tr>
                                <tr valign=\"top\">
                                  <td><b><u>".lang("Description #")."5:</u></b></td>
                                  <td>
                                    <input CLASS=text STYLE='width: 184px;' name=\"desc_5\" value=\"\" type=\"text\"><br>&nbsp;
                                  </td>
                                </tr>
                              </table>
                              <input type=\"SUBMIT\" value=\"Update Album\" class=\"btn_save\" onMouseover=\"this.className='btn_saveon';\" onMouseout=\"this.className='btn_save';\">
                              </center>
                            </td>
                          </tr>
                        </table>&nbsp;\n";

		$THIS_DISPLAY .= "</FORM><BR><table border=\"0\" class=\"text\" align=\"center\">
                      <tr>\n";

  $on_page=$rows*$columns;
  $cnt=0;
  $result = mysql_query("SELECT * FROM premium_album_images WHERE ALBUM_ID = '$id' ORDER BY `position`");
  $i=0;
  $count = mysql_num_rows($result);
  while ($row = mysql_fetch_array($result))
  {
	$i++;
    $ext   = extract_ext($row['IMAGE_NAME']);
    $fname = basename($row['IMAGE_NAME'], '.'.$ext);
    $fname = 'images/premium/content/'.$fname.'_small.'.$ext;
    $row['FILE_SIZE']=ceil($row['FILE_SIZE']/1024);
    $THIS_DISPLAY .= '<form method=GET action="edit_album.php">';
    $THIS_DISPLAY .= '<td>'.$i.'</td><td align="center" valign="top" width="'.$thumb_width.'"><a name="'.$row['PRIKEY'].'"><a href="#" onClick="popupWindow(\'../'.$row['IMAGE_NAME'].'\')" title="'.addslashes($row['IMAGE_TITLE']).'; '.$row['SIZE_X'].'x'.$row['SIZE_Y'].'; '.$row['FILE_SIZE'].' kb"><img width="'.$thumb_width.'" border="0" src="../'.$fname.'?rand='.mt_rand().'" alt="'.addslashes($row['IMAGE_TITLE']).'; '.$row['SIZE_X'].'x'.$row['SIZE_Y'].'; '.$row['FILE_SIZE'].' kb" hspace="8"></a><br>&nbsp</td>';
  	$THIS_DISPLAY .= '       <td>Move To&nbsp;
  	<input name=id value='.$id.' type=hidden>
	<input name=action value=moveto type=hidden>
	<input name=pos value='.$i.' type=hidden>
  	<select name=topos onchange="form.submit();">';//onchange="javascript:alert(1);"

  	for($j=1;$j<=$count;$j++)
  	{
  		if($j==$i){
  			$THIS_DISPLAY .= '<option selected="selected">---</option>';
  			continue;
  		}
  		else{
  			$THIS_DISPLAY .= '<option value='.$j.'>'.$j.'</option>';
//  			$THIS_DISPLAY .= '<option onclick="javascript:window.location.href=\'edit_album.php?id='.$id.'&pos='.$i.'&topos='.$j.'&action=moveto&image_id='.$row['PRIKEY'].'&rand='.mt_rand().'#'.$row['PRIKEY'].'\'" >'.$j.'</option>';
  		}
  	}
    $THIS_DISPLAY .= ' </select><p></form>';
	$THIS_DISPLAY .= '<input value="'.lang("Edit").'" class="btn_edit" onclick="javascript:if (document.all.block_'.$row['PRIKEY'].'.style.display==\'none\'){document.all.block_'.$row['PRIKEY'].'.style.display=\'\'}else{document.all.block_'.$row['PRIKEY'].'.style.display=\'none\'}" style="width: 80px;margin-bottom:4px;" type="button" onMouseover="this.className=\'btn_editon\';" onMouseout="this.className=\'btn_edit\';"><br>
                                   <input value="'.lang("Delete").'" class="btn_delete" onclick="javascript:delete_image('.$id.', '.$row['PRIKEY'].');" style="width: 80px;" type="button" onMouseover="this.className=\'btn_deleteon\';" onMouseout="this.className=\'btn_delete\';"><br>&nbsp;
                             </td></tr>';
    $THIS_DISPLAY .= '<tr id="block_'.$row['PRIKEY'].'" style="display: none">
                       <td align="center" valign="top" colspan="2"><form method="POST" action="edit_album.php?&action=save_image&id='.$id.'&image_id='.$row['PRIKEY'].'">
                                    <table border="0" width="100%" style="font-size: 10px">
                                      <tr><td>Title: </td><td><input CLASS="text" STYLE="width: 184px;" name="title" value="'.$row['IMAGE_TITLE'].'" type="text"></td></tr>
                                      <tr><td>Desc: </td><td><input CLASS="text" STYLE="width: 184px;" name="desc" value="'.$row['IMAGE_DESC'].'" type="text"><td></tr>
                                      <tr><td>HTML: </td><td><textarea name="ihtml">'.htmlentities($row['user_html']).'</textarea><td></tr>
                                      <tr><td>Test: </td><td>'.($row['user_html']).'<td></tr>
                                    </table>
                                    <input value="'.lang("Update").'" class="btn_blue" style="width: 80px;margin-bottom:4px" type="submit"><br>&nbsp;
    </form></td>';
    $cnt++;
    if ($cnt>=1)
    {
      $cnt=0;
      $THIS_DISPLAY.= "</tr><tr>";
    }
  }
  $THIS_DISPLAY.= "</tr></table><br>$scroll";


####################################################################
//ini_restore("memory_limit");
include("shared/html_build.php");

####################################################################

?>

</div>

</body>
<HEAD>
<META HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE"></HEAD>
</html>
