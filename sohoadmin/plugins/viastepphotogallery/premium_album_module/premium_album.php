<?
###############################################################################
## Soholaunch(R) Site Management Tool
## Version 4.5
##
## Author: 			Dmitry Chaplinsky [soholaunch@viastep.com]
## Homepage:	 	http://www.sohotemplates.com
## Bug Reports: 	http://bugz.soholaunch.com
## Release Notes:	sohoadmin/build.dat.php
###############################################################################
ob_start();
session_start();
include_once("../../../program/includes/product_gui.php");
  $s1=substr(get_cfg_var('post_max_size'), 0, -1);
  $s2=substr(get_cfg_var('upload_max_filesize'), 0, -1);
  if ($s1>$s2)
    $max_s=$s1;
  else
    $max_s=$s2;

#######################################################
### IF THE 'photo_album' TABLE DOES NOT EXIST;
### CREATE NOW
#######################################################
    if (!file_exists('../images/premium/'))
      mkdir('../images/premium/');
    if (!file_exists('../images/premium/content/'))
      mkdir('../images/premium/content/');

    if (!file_exists('../temp/'))
      mkdir('../temp/');

		$match = 0;
		$match2 = 0;
		$match3 = 0;
		$match4 = 0;
		$match5 = 0;
		$match6 = 0;
		$tablename = "premium_album";
		$tablename2 = "premium_album_images";
		$position = "position";
		$cols = 'columns';

		$html_field = 'user_html';

		$resizeFlag = 'resize_flag';

		$cols_count = 4;
		$rows_count = 4;

		$result = mysql_list_tables("$db_name");
		$i = 0;
		while ($i < mysql_num_rows ($result)) {
			$tb_names[$i] = mysql_tablename ($result, $i);
			if ($tb_names[$i] == $tablename) {
				$match = 1;

				$res =  mysql_list_fields("$db_name","$tablename");
				while($field = mysql_fetch_field($res))
				{
					if($field->name==$cols)
					{
						$match4 = 1;
					}
					if($field->name == $resizeFlag)
					{
						$match6 = 1;
					}
				}
			}

			$tb_names[$i] = mysql_tablename ($result, $i);
			if ($tb_names[$i] == $tablename2) {
				$match2 = 1;

				$res =  mysql_list_fields("$db_name","$tablename2");
				while($field = mysql_fetch_field($res))
				{
					if($field->name==$html_field)
					{
						$match5 = 1;
					}
					if($field->name==$position)
					{
						$match3 = 1;
					}
				}
			}

			$i++;
		}

		// if ($match == 1) { mysql_query("DROP TABLE photo_album"); }

		if ($match != 1) {
			mysql_db_query("$db_name","CREATE TABLE premium_album (
				PRIKEY INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
				ALBUM_NAME VARCHAR(255),
        ALBUM_THUMB VARCHAR(255),
        THUMB_WIDTH INTEGER,
        `RESIZE_TYPE` ENUM( 'no', 'width', 'biggest' ) NOT NULL,
        `BIG_SIZE` INTEGER)");

		} // End if Match != 1
    else
    {
      $fields = mysql_list_fields("$db_name", $tablename);
      $columns = mysql_num_fields($fields);

      $alter_need=true;
      for ($i = 0; $i < $columns; $i++) {
        if (mysql_field_name($fields, $i)=='RESIZE_TYPE')
        {
          $alter_need='false';
          break;
        }
      }
    }

    if ($alter_need)
    {
			mysql_db_query("$db_name",
       "ALTER TABLE `premium_album` ADD
          `RESIZE_TYPE` ENUM( 'no', 'width', 'biggest' ) NOT NULL, ADD
          `BIG_SIZE` INTEGER;");

    }

		if ($match2 != 1) {

			mysql_db_query("$db_name","CREATE TABLE premium_album_images (
				PRIKEY INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
				ALBUM_ID INTEGER,
        IMAGE_NAME VARCHAR(255),
        SIZE_X INTEGER,
        SIZE_Y INTEGER,
        FILE_SIZE INTEGER,
        IMAGE_TITLE VARCHAR(255),
				IMAGE_DESC TEXT,
				position INTEGER)");
			$match3 = 0;
		} // End if Match2 != 1
		if($match3 != 1)
		{
			mysql_db_query("$db_name","ALTER TABLE `premium_album_images`
							ADD `position` INTEGER");
			$albums = mysql_db_query("$db_name","SELECT `PRIKEY` FROM `premium_album`");
			while($album = mysql_fetch_array($albums))
			{
				$id = $album['PRIKEY'];
				$res = mysql_db_query("$db_name","SELECT `PRIKEY` FROM `premium_album_images`
									   WHERE `ALBUM_ID`='$id'");
				$pos = 1;
				while($row = mysql_fetch_array($res))
				{
					$key = $row['PRIKEY'];
					mysql_db_query("$db_name","UPDATE `premium_album_images` SET
									`position`='$pos' WHERE `PRIKEY`='$key'");
					$pos++;
				}
			}
		}
		if($match4 != 1)
		{//`columns` and `rows` field needed
			mysql_db_query("$db_name","ALTER TABLE `premium_album`
							ADD `columns` INTEGER,
							ADD `rows` INTEGER");
			mysql_db_query("$db_name","UPDATE `premium_album` SET `columns`=$cols_count,
							 `rows`=$rows_count");
		}
		if($match5 != 1)
		{//`user_html` field needed
			mysql_db_query("$db_name","ALTER TABLE `premium_album_images`
							ADD `".$html_field."` VARCHAR( 500 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL");
		}
		if($match6 != 1)
		{//`resize_flag` field needed
			mysql_db_query("$db_name","ALTER TABLE `premium_album`
					ADD `" . $resizeFlag . "` INT( 1 ) NOT NULL DEFAULT '1'");
		}

#######################################################
### PROCESS "DELETE ALBUM" ACTION				    ###
#######################################################

if ($ACTION == "DEL") {
  $res = mysql_query("SELECT ALBUM_THUMB FROM premium_album WHERE PRIKEY = '$id'");
  if ($row=mysql_fetch_array($res)) {
    @unlink('../'.$row['ALBUM_THUMB']);
  }

  $res = mysql_query("SELECT IMAGE_NAME FROM premium_album_images WHERE ALBUM_ID = '$id'");
  while ($row=mysql_fetch_array($res)) {
    @unlink('../'.$row['IMAGE_NAME']);

    $ext   = extract_ext($row['IMAGE_NAME']);
    $fname = basename($row['IMAGE_NAME'], '.'.$ext);
    $fname = 'images/premium/content/'.$fname.'_small.'.$ext;
    @unlink('../'.$fname);
  }
	mysql_query("DELETE FROM premium_album_images WHERE ALBUM_ID = '$id'");
	mysql_query("DELETE FROM premium_album WHERE PRIKEY = '$id'");
}

#######################################################
### PROCESS "ADD NEW ALBUM" ACTION				    ###
#######################################################

if ($ACTION == "NG") {

		// Check for duplicates and don't allow
    $message='';

		$ef = 0;
		$NEWGROUP = ucwords($NEWGROUP);
		$NEWGROUP = stripslashes($NEWGROUP);
		$NEWGROUP = addslashes($NEWGROUP);

		$result = mysql_query("SELECT * FROM premium_album");
		$num_groups = mysql_num_rows($result);

    if (!((is_numeric($a_method)) && ($a_method>=1) && ($a_method<=3)))
      $a_method=1;

		if ($num_groups > 0) {
			while($GROUP = mysql_fetch_array($result)) {
				if ($GROUP[ALBUM_NAME] == $NEWGROUP) { $ef = 1; }
			}
		}
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

    if (!((is_numeric($THUMB_SIZE)) && ($THUMB_SIZE>=25) && ($THUMB_SIZE<=500)))
      $THUMB_SIZE=100;

		if ($NEWGROUP != "" && $ef != 1) {
			if (mysql_query("INSERT INTO premium_album VALUES('NULL', '$NEWGROUP', '', '$THUMB_SIZE', '$RESIZE',
			                 '$BIG_SIZE', '$cols_count', '$rows_count', '1')"))
      {
        $id = mysql_insert_id();
        $a_file = $_FILES['THUMB']['tmp_name'];
        $image_name='';
        if (is_uploaded_file($a_file))
        {
          preg_match("/\\.(.*)$/", $_FILES['THUMB']['name'], $m);
          $thumb_name='../images/premium/'.$id."_thumb.jpg";
          $db_name='images/premium/'.$id."_thumb.jpg";
          $image_name='../images/premium/'.$id.$m[0];
          $image_name2='../images/premium/'.$id.".jpg";

          if (!move_uploaded_file($_FILES['THUMB']['tmp_name'], $image_name) || !chmod($image_name, 0644) )
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

                $title=addslashes($_POST['title_'.$key]);
                $desc=addslashes($_POST['desc_'.$key]);

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
//                       $sshRez .= shell_exec ("del ..\unzip.cab");
                    }
                    $sshRez = shell_exec ("..\unzips\unzip -o -L $this_zip");
                 }else
                 {
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

            $key=0;
            $dir = dir($uploadDir);
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

          $key=0;
          case '3':
            $dir = dir($folder);
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

$messages=ob_get_contents();
ob_end_clean();
}




#######################################################
### START HTML/JAVASCRIPT CODE					    ###
#######################################################

$MOD_TITLE = lang("Premium Album");
$BG = "photo_bg.jpg";

?>

<HTML>
<HEAD>
<TITLE>PREMIUM ALBUM</TITLE>

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

function delete_album(sel) {

	// What album is selected?
//	var sel = DELFORM.id.value;

	if (sel != "") {

		var tiny = window.confirm('Are you sure you wish to delete this Album?');
		if (tiny != false) {
			window.location = 'premium_album.php?id='+sel+'&ACTION=DEL&<?=SID?>';
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
$THIS_DISPLAY = "$messages";

	$THIS_DISPLAY .= "<form method=\"POST\" action=\"premium_album.php\" enctype=\"multipart/form-data\">\n";
	$THIS_DISPLAY .= "<input type=\"hidden\" name=\"ACTION\" value=\"NG\">\n";

	$THIS_DISPLAY .= "<BR><BR><table border=\"0\" cellpadding=\"8\" cellspacing=\"0\" width=\"80%\" align=\"center\" style=\"border: 1px inset black;\">\n";

	$THIS_DISPLAY .= "<tr><td align=\"left\" valign=\"top\" class=\"text\" bgcolor=\"#EFEFEF\" style=\"color: #000099;\">\n";

		$THIS_DISPLAY .= "<div style=\"background-color: #CCCCCC; border: 1px solid #000000;\">
                        <center><B><font size=\"3\">".lang("Create New Premium Album")."</font></b></center></div><br>
                        <table border=\"0\" align=\"center\" class=\"text\" >
                          <tr>
                            <td>
                              <b><u>".lang("Enter Premium Album Name").":</u></b>
                            </td>
                            <td>
                              <INPUT TYPE=TEXT NAME=NEWGROUP CLASS=text STYLE='width: 184px;'>&nbsp;\n
                            </td>
                            <td rowspan=\"2\">
                              <input type=\"SUBMIT\" value=\"Create Album\" style=\"height: 50px\" class=\"btn_save\" onMouseover=\"this.className='btn_saveon';\" onMouseout=\"this.className='btn_save';\">
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
                          <tr id=\"thumb_size_row\" style=\"display: none\">
                            <td width=\"150\">
                              <b><u>".lang("Select Thumbnail Width").":</u></b>
                            </td>
                            <td>
                              <INPUT TYPE=TEXT NAME=THUMB_SIZE STYLE='width: 80px;' CLASS=text VALUE=\"100\">
                            </td>
                          </tr>

                          <tr id=\"resize_row\"  style=\"display: none\">
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
                                  <option value=\"1\" selected>".lang("No")."</option>
                                  <option value=\"2\">".lang("Set width to")."</option>
                                  <option value=\"3\">".lang("Set biggest side to")."</option>
                                </select>
                                <span id=\"big_size_span\" style=\"display:none\"><input style=\"width: 35px\" type=\"text\" class=\"text\" name=\"BIG_SIZE\" value=\"800\"> px.</span>
                            </td>
                          </tr>

                          <tr>
                            <td colspan=\"3\">
                              <b><a href=\"#\" onClick=\"javascript:if (document.all.show_more.style.display=='none'){document.all.show_more1.style.display=''}; document.all.show_more.style.display=''; document.all.RESIZE.value=1; document.all.a_method.value=1; document.all.thumb_size_row.style.display=''; document.all.resize_row.style.display=''\">".lang("More...")."</a></b>
                            </td>
                          </tr>
                          <tr id=\"show_more\" style=\"display: none\">
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
                               <center>
                              <b><u>".lang("Select a .zip file with images").":</u></b><br>
                              <input style=\"margin: 5px 0px\" CLASS=text name=\"zip\" value=\"\" type=\"file\">
                              <br>
                              <input type=\"SUBMIT\" value=\"Create Album\" class=\"btn_save\" onMouseover=\"this.className='btn_saveon';\" onMouseout=\"this.className='btn_save';\">
                            </td>
                          </tr>
                          <tr id=\"show_more3\" style=\"display: none\">
                            <td colspan=\"3\">
                              <center><b><u>".lang("Select a server folder").":</u></b><br>
                              <input style=\"margin: 5px 0px\" CLASS=text name=\"folder\" value=\"".$_SERVER['DOCUMENT_ROOT'].'/'."\" type=\"text\" size=\"45\">
                              <br>
                              <input type=\"SUBMIT\" value=\"Create Album\" class=\"btn_save\" onMouseover=\"this.className='btn_saveon';\" onMouseout=\"this.className='btn_save';\">
                            </td>
                          </tr>
                          <tr id=\"show_more1\" style=\"display: none\">
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
                              <input type=\"SUBMIT\" value=\"Create Album\" class=\"btn_save\" onMouseover=\"this.className='btn_saveon';\" onMouseout=\"this.className='btn_save';\">
                              </center>
                            </td>
                          </tr>
                        </table>&nbsp;\n";

		$THIS_DISPLAY .= "</FORM><BR>\n";


		// Pull any data from "sec_codes" table for display use

		$result = mysql_query("SELECT * FROM premium_album ORDER BY ALBUM_NAME");
		$num_groups = mysql_num_rows($result);

		if ($num_groups > 0) {
      $THIS_DISPLAY .= "<DIV ALIGN=LEFT>\n";
			$THIS_DISPLAY .= "<B><U>".lang("Current Photo Albums")."</U></B>:<BR><BR>\n\n";
      $THIS_DISPLAY .= '<table class="text" border="0" cellpadding="5" cellspacing="0" width="100%" bgcolor="#EFEFEF">
                         <tr>
                           <td align="center" bgcolor="#EFEFEF" valign="middle">
                             <table style="font-family: Tahoma; font-size: 8pt;" bgcolor="#000000" border="0" cellpadding="4" cellspacing="1" width="98%">
                               <tr>
                                 <td class="col_title" align="center">'.lang("ID").'</td>
                                 <td class="col_title" align="center">'.lang("Thumbnails&Title").'</td>
                                 <td class="col_title" align="center">'.lang("Operation").'</td>
                               </tr>';

			while($row = mysql_fetch_array($result)) {
        $res = mysql_query("SELECT count(*) as image_count, sum(FILE_SIZE) as album_size FROM premium_album_images WHERE ALBUM_ID='".$row['PRIKEY']."'");
        if ($in_row = mysql_fetch_array($res))
        {
          $in_row['album_size']=round($in_row['album_size']/1024);
        }
				$THIS_DISPLAY .= '     <tr align="center" bgcolor="#FFFFFF">';
				$THIS_DISPLAY .= "       <td>".$row['PRIKEY']."</td>\n";
				$THIS_DISPLAY .= '       <td><img width="100" alt="" src="../'.$row['ALBUM_THUMB'].'"><br>'.$row['ALBUM_NAME'].'</td>';
				$THIS_DISPLAY .= '       <td>
                                      '.lang("Images In Gallery").': <b>'.$in_row['image_count'].'</b><br>
                                      '.lang("Gallery size").': <b>'.$in_row['album_size'].'kb</b><br><br>
                                   <input value="'.lang("Preview").'" class="btn_blue" onclick="javascript:window.location.href=\'preview_album.php?id='.$row['PRIKEY'].SID.'\'" style="width: 80px;margin-bottom:4px" type="button"><br>
                                   <input value="'.lang("Edit").'" class="btn_edit" onclick="javascript:window.location.href=\'edit_album.php?id='.$row['PRIKEY'].SID.'\'" style="width: 80px;margin-bottom:4px;" type="button" onMouseover="this.className=\'btn_editon\';" onMouseout="this.className=\'btn_edit\';"><br>
                                   <input value="'.lang("Upload images").'" onclick="javascript:window.location.href=\'edit_album.php?&upload=true&id='.$row['PRIKEY'].SID.'#upload\'" style="width: 80px;margin-bottom:4px;" type="button" class="btn_save" onmouseover="this.className=\'btn_saveon\';" onmouseout="this.className=\'btn_save\';"><br>
                                   <input value="'.lang("Delete").'" class="btn_delete" onclick="javascript:delete_album('.$row['PRIKEY'].');" style="width: 80px;" type="button" onMouseover="this.className=\'btn_deleteon\';" onMouseout="this.className=\'btn_delete\';"><br>&nbsp;
                                 </td>';
				$THIS_DISPLAY .= '     </tr>';
			}

			$THIS_DISPLAY .= "    </table>
                          </tr>
                        </table>
                          </DIV>\n";
		}

	$THIS_DISPLAY .= "</TD></TR></TABLE><BR><BR>\n";




####################################################################

include("shared/html_build.php");

####################################################################

?>

</div>

</body>
<HEAD>
<META HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE"></HEAD>
</html>
