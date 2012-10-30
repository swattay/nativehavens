<?php

############################################################################################
## Soholaunch(R) Site Management Tool
## Version 4.8
##
## Author: 			Mike Johnston & Mike Morrison
## Homepage:	 	http://www.soholaunch.com
## Bug Reports: 	http://bugz.soholaunch.com
############################################################################################

############################################################################################
## COPYRIGHT NOTICE
## Copyright 1999-2005 Soholaunch.com, Inc. and Mike Johnston.  All Rights Reserved.
##
## This script may be used and modified in accordance to the license
## agreement attached (license.txt) except where expressly noted within
## commented areas of the code body. This copyright notice and the comments
## comments above and below must remain intact at all times.  By using this
## code you agree to indemnify Soholaunch.com, Inc, its coporate agents
## and affiliates from any liability that might arise from its use.
##
## Selling the code for this program without prior written consent is
## expressly forbidden and in violation of Domestic and International
## copyright laws.
#############################################################################################

# Maintis #250
if ( !function_exists("lang") ) {
   include("sohoadmin/program/includes/shared_functions.php");
}
  list($id, $layout, $columns, $rows, $show_thumbs)=split(' ', $BLOG_CATEGORY_NAME);
  $exec = true;

  if ($_GET['m_id' . $id] == '' && $id != -1) {
	  foreach($_GET as $key => $value) {
	  	  if ($value != '') {
	  	  	 if (strstr($key, 'm_id') == (bool)true) {
	  	  	 	$exec = false;
	  	  	 	break;
	  	  	 }
	  	  }
	  }
  }

if ($exec) {  //do not show anything if some other album will drow images

  if(isset($_GET['m_id'.$id]))
  	$m_id=$_GET['m_id'.$id];
  else
  	$m_id=$_GET['m_id'];
  $show_thumbs=$show_thumbs=='true';
  $let_user_select=$id==-1;

  $m_id_posfix = $id;
  if(($m_id != $id)/*||($id == -1)*/)
  {
  	$m_id = '';
  	$m_id_posfix = '';
  }
  if (($id != -1)&&($m_id=='')&($show_thumbs==false)) {
  	$m_id = $id;
  }
  if($id == -1)
  {
   	foreach($_GET as $keyname=>$val)
  	{
  		if($val != '')
	  		if (ereg('m_id', $keyname))
	  		{
	  			$id = $val;
	  			$m_id = $id;
	  			break;
	  		}
	}
  }
  if((!$let_user_select)||($m_id!=''))
  {
	  $result = mysql_query("SELECT * FROM premium_album WHERE PRIKEY = '$id'");
	  $dim = mysql_fetch_array($result);

	  $rows = $dim['rows'];
	  $columns = $dim['columns'];
  }

  if (!((is_numeric($columns)) && ($columns>=1) && ($columns<=250)))
  		$columns = 5;

  if (!((is_numeric($rows)) && ($rows>=1) && ($rows<=250)))
  		$rows = 5;

  if (!((is_numeric($layout)) && ($layout>=1) && ($layout<=8)))
    $layout=1;

  if (!((is_numeric($offset)) && ($offset>=0)))
    $offset=0;

$result = mysql_query("SELECT * FROM premium_album WHERE PRIKEY = '$id'");
if ($mike = mysql_fetch_array($result)) {
  $thumb_width = $mike["THUMB_WIDTH"];
}

	$THIS_DISPLAY = "<!-- 9dce3395e2df2b1fdc28cef945f650a1 -->
	<link rel=\"stylesheet\" type=\"text/css\" href=\"sohoadmin/plugins/viastepphotogallery/highslide/highslide.css\" />
<script language=\"JavaScript\">
<!--

function file_get_contents( url, flags, context, offset, maxLen ) {
    // Read the entire file into a string
    //
    // version: 906.111
    // discuss at: http://phpjs.org/functions/file_get_contents
    // *     example 1: file_get_contents('http://kevin.vanzonneveld.net/pj_test_supportfile_1.htm');
    // *     returns 1: '123'
    // Note: could also be made to optionally add to global $http_response_header as per http://php.net/manual/en/reserved.variables.httpresponseheader.php
    var tmp, headers = [], newTmp = [], k=0, i=0, href = '', pathPos = -1, flagNames = '', content = null, http_stream = false;
    var func = function (value) { return value.substring(1) !== ''; };

    // BEGIN REDUNDANT
    this.php_js = this.php_js || {};
    this.php_js.ini = this.php_js.ini || {};
    // END REDUNDANT
    context = context || this.php_js.default_streams_context || null;

    if (!flags) {flags = 0;}
    var OPTS = {
        PHP_FILE_USE_INCLUDE_PATH : 1,
        PHP_FILE_TEXT : 32,
        PHP_FILE_BINARY : 64
    };
    if (typeof flags === 'number') { // Allow for a single string or an array of string flags
        flagNames = flags;
    }
    else {
        flags = [].concat(flags);
        for (i=0; i < flags.length; i++) {
            if (OPTS[flags[i]]) {
                flagNames = flagNames | OPTS[flags[i]];
            }
        }
    }

    if ((flagNames & OPTS.PHP_FILE_USE_INCLUDE_PATH) && this.php_js.ini.include_path &&
            this.php_js.ini.include_path.local_value) {
        var slash = this.php_js.ini.include_path.local_value.indexOf('/') !== -1 ? '/' : '\\\\';
        url = this.php_js.ini.include_path.local_value+slash+url;
    }
    else if (!/^(https?|file):/.test(url)) { // Allow references within or below the same directory (should fix to allow other relative references or root reference; could make dependent on parse_url())
        href = this.window.location.href;
        pathPos = url.indexOf('/') === 0 ? href.indexOf('/', 8)-1 : href.lastIndexOf('/');
        url = href.slice(0, pathPos+1)+url;
    }

    if (context) {
        var http_options = context.stream_options && context.stream_options.http;
        http_stream = !!http_options;
    }

    if (!context || http_stream) {
        var req = this.window.ActiveXObject ? new ActiveXObject('Microsoft.XMLHTTP') : new XMLHttpRequest();
        if (!req) {throw new Error('XMLHttpRequest not supported');}

        var method = http_stream ? http_options.method : 'GET';
        var async = !!(context && context.stream_params && context.stream_params['phpjs.async']);
        req.open(method, url, async);
        if (async) {
            var notification = context.stream_params.notification;
            if (typeof notification === 'function') {
                req.onreadystatechange = function (aEvt) { // aEvt has stopPropagation(), preventDefault(); see https://developer.mozilla.org/en/NsIDOMEvent

                    var objContext = {}; // properties are not available in PHP, but offered on notification via 'this' for convenience
                    objContext.responseText = req.responseText;
                    objContext.responseXML = req.responseXML;
                    objContext.status = req.status;
                    objContext.statusText = req.statusText;
                    objContext.readyState = req.readyState;
                    objContext.evt = aEvt;

                    // notification args: notification_code, severity, message, message_code, bytes_transferred, bytes_max (all int's except string 'message')
                    // Need to add message, etc.
                    var bytes_transferred;
                    switch(req.readyState) {
                        case 0: // 	UNINITIALIZED 	open() has not been called yet.
                            notification.call(objContext, 0, 0, '', 0, 0, 0);
                            break;
                        case 1: // 	LOADING 	send() has not been called yet.
                            notification.call(objContext, 0, 0, '', 0, 0, 0);
                            break;
                        case 2: // 	LOADED 	send() has been called, and headers and status are available.
                            notification.call(objContext, 0, 0, '', 0, 0, 0);
                            break;
                        case 3: // 	INTERACTIVE 	Downloading; responseText holds partial data.
                            bytes_transferred = Math.floor(req.responseText.length/2); // Two characters for each byte
                            notification.call(objContext, 7, 0, '', 0, bytes_transferred, 0);
                            break;
                        case 4: // 	COMPLETED 	The operation is complete.
                            if (req.status >= 200 && req.status < 400) {
                                bytes_transferred = Math.floor(req.responseText.length/2); // Two characters for each byte
                                notification.call(objContext, 8, 0, '', req.status, bytes_transferred, 0);
                            }
                            else if (req.status === 403) { // Fix: These two are finished except for message
                                notification.call(objContext, 10, 2, '', req.status, 0, 0);
                            }
                            else { // Errors
                                notification.call(objContext, 9, 2, '', req.status, 0, 0);
                            }
                            break;
                        default:
                            throw 'Unrecognized ready state for file_get_contents()';
                    }
                }
            }
        }

        if (http_stream) {
            var sendHeaders = http_options.header && http_options.header.split(/\\r?\\n/);
            var userAgentSent = false;
            for (i=0; i < sendHeaders.length; i++) {
                var sendHeader = sendHeaders[i];
                var breakPos = sendHeader.search(/:\\s*/);
                var sendHeaderName = sendHeader.substring(0, breakPos);
                req.setRequestHeader(sendHeaderName, sendHeader.substring(breakPos+1));
                if (sendHeaderName === 'User-Agent') {
                    userAgentSent = true;
                }
            }
            if (!userAgentSent) {
                var user_agent = http_options.user_agent ||
                                                                    (this.php_js.ini.user_agent && this.php_js.ini.user_agent.local_value);
                if (user_agent) {
                    req.setRequestHeader('User-Agent', user_agent);
                }
            }
            content = http_options.content || null;
        }
        // We should probably change to an || \"or\", in order to have binary as the default (as it is in PHP), but this method might not be well-supported; check for its existence instead or will this be to much trouble?
        if (flagNames & OPTS.PHP_FILE_BINARY && !(flagNames & OPTS.PHP_FILE_TEXT)) { // These flags shouldn't be together
            req.sendAsBinary(content); // In Firefox, only available FF3+
        }
        else {
            req.send(content);
        }

        tmp = req.getAllResponseHeaders();
        if (tmp) {
            tmp = tmp.split('\\n');
            for (k=0; k < tmp.length; k++) {
                if (func(tmp[k])) {
                    newTmp.push(tmp[k]);
                }
            }
            tmp = newTmp;
            for (i=0; i < tmp.length; i++) {
                headers[i] = tmp[i];
            }
            this.\$http_response_header = headers; // see http://php.net/manual/en/reserved.variables.httpresponseheader.php
        }

        if (offset || maxLen) {
            if (maxLen) {
                return req.responseText.substr(offset || 0, maxLen);
            }
            return req.responseText.substr(offset);
        }
        return req.responseText;
    }
    return false;
}

	if (!window.scriptsLoaded) {
	  	window.scriptsLoaded = 'true';

		var ss = document.createElement('script');
		ss.text = file_get_contents('http://" . $_SERVER['HTTP_HOST'] . "/sohoadmin/plugins/viastepphotogallery/highslide/highslide-full.js');
		var hh = document.getElementsByTagName('head')[0];
		hh.appendChild(ss);

		hs.graphicsDir = 'sohoadmin/plugins/viastepphotogallery/highslide/graphics/';
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
				offsetX: '0',
				offsetY: '-10',
				hideOnMouseOut: true
			}
		});
	}
//-->
</script>
<style type=\"text/css\">
	.album_link {
		font-family:Arial,Geneva,Helvetica,sans-serif;
		font-size:12px;font-weight:bold;
	}
	.album_title {
		font-family:Arial,Geneva,Helvetica,sans-serif;
		font-size:14px;font-weight:normal;
	}
</style>
";

  if ($id!=-1 && !($show_thumbs && $m_id=='') )
  {
    if (($id==$m_id))
    {
	  $link_prefix=(preg_replace("/m_id".$id."\=[^\&]*\&/i", "", $_SERVER['REQUEST_URI']));
      $link_prefix=(preg_replace("/m_id".$id."\=[^\&]*/i", "", $link_prefix));
      $link_prefix=(preg_replace("/[\?|\&]$/", "", $link_prefix));

      if (ereg('\?', $link_prefix))
        $link_prefix=$link_prefix.'&m_id'.$id.'=';
      else
        $link_prefix=$link_prefix.'?m_id'.$id.'=';
      reset($_GET);
      $link='';
      while (list($key, $value) = each($_GET)) {
        if (!is_array($_GET[$key])) {
          if ($key!='offset'.$m_id)
            $link.=$key.'='.$value.'&';
        }
      }
      if ($link!='')
        $link='?'.$link;
      if ($let_user_select || $show_thumbs)
        $THIS_DISPLAY .= "<div class=\"album_title\">".$mike['ALBUM_NAME']." &nbsp;<a href=\"$link_prefix\"><img src=\"/sohoadmin/plugins/viastepphotogallery/images/up.png\" border=\"0\" alt=\"back\" width=\"26\" height=\"15\"></a></div><br>
                        <table border=\"0\">
                          <tr>";//class=\"album_table\"
      else
        $THIS_DISPLAY .= "<div class=\"album_title\">".$mike['ALBUM_NAME']."</div><br>
                        <table border=\"0\" >
                          <tr>"; //class=\"album_table\"
      	$THIS_DISPLAY .= "<td>";
      $on_page=$rows*$columns;
      $cnt=0;
    // function make_scroll($LinkPrefix, $Count, $Offset, $ItemsOnPage, $html_class = 'small')

     $result = mysql_query("SELECT count(*) as cnt FROM premium_album_images WHERE ALBUM_ID = '$id'");
     if ($row=mysql_fetch_array($result))
        $cnt = $row['cnt'];
      $scroll = make_scroll($link."offset$m_id=", $cnt, ${'offset'.$m_id}, $on_page, 'album_link');

      $cnt=0;
      $result = mysql_query("SELECT * FROM premium_album_images WHERE ALBUM_ID = '$id' ORDER BY `position` LIMIT ".${'offset'.$m_id}*$on_page.", $on_page");
  $THIS_DISPLAY .= '<table cellpadding=3" cellspacing="3" border="0">
  <tr style="font-family:Arial,Geneva,Helvetica,sans-serif;font-size:12px;font-weight:normal;">';
      while ($row = mysql_fetch_array($result))
      {
        $ext   = extract_ext($row['IMAGE_NAME']);
        $fname = basename($row['IMAGE_NAME'], '.'.$ext);
        $fname = 'sohoadmin/plugins/viastepphotogallery/images/premium/content/'.$fname.'_small.'.$ext;
        $row['FILE_SIZE']=ceil($row['FILE_SIZE']/1024);

        switch ($layout){
          case 1:
            $THIS_DISPLAY .= '<td align="center" class="album_item" valign="top" width="'.$thumb_width.'"><a href="sohoadmin/plugins/viastepphotogallery/' . $row['IMAGE_NAME'] . '" title=" '.$row['IMAGE_TITLE'].'" class="highslide" onclick="return hs.expand(this, { slideshowGroup: \'' . $row['ALBUM_ID'] . '\' } )"><img width="'.$thumb_width.'" border="0" src="'.$fname.'" alt="'.$row['IMAGE_TITLE'].'" hspace="4"></a><div class="highslide-caption">' . $row['IMAGE_TITLE'] . '<br />' . $row['IMAGE_DESC'] .'</div></td><td width="'.$thumb_width.'" align="center" valign="top"><b>'.$row['IMAGE_TITLE'].'</b><br><small>'.$row['IMAGE_DESC'].'</small></td>'."\n";
          break;
          case 2:
            $THIS_DISPLAY .= '<td align="center" class="album_item" valign="top" width="'.$thumb_width.'"><b>'.$row['IMAGE_TITLE'].'</b><br><a href="sohoadmin/plugins/viastepphotogallery/' . $row['IMAGE_NAME'] . '" title=" '.$row['IMAGE_TITLE'].'" class="highslide" onclick="return hs.expand(this, { slideshowGroup: \'' . $row['ALBUM_ID'] . '\' } )"><img width="'.$thumb_width.'" border="0" src="'.$fname.'" alt="'.$row['IMAGE_TITLE'].'" hspace="4"></a><div class="highslide-caption">' . $row['IMAGE_TITLE'] . '<br />' . $row['IMAGE_DESC'] . '</div><br><small>'.$row['IMAGE_DESC'].'</small><br>&nbsp</td>'."\n";
          break;
          case 3:
            $THIS_DISPLAY .= '<td align="center" class="album_item" valign="top" width="'.$thumb_width.'"><b>'.$row['IMAGE_TITLE'].'</b><br><a href="sohoadmin/plugins/viastepphotogallery/' . $row['IMAGE_NAME'] . '" title=" '.$row['IMAGE_TITLE'].'" class="highslide" onclick="return hs.expand(this, { slideshowGroup: \'' . $row['ALBUM_ID'] . '\' } )"><img width="'.$thumb_width.'" border="0" src="'.$fname.'" alt="'.$row['IMAGE_TITLE'].'" hspace="4"></a><div class="highslide-caption">' . $row['IMAGE_TITLE'] . '</div><br>&nbsp</td>'."\n";
          break;
          case 4:
            $THIS_DISPLAY .= '<td align="center" class="album_item" valign="top" width="'.$thumb_width.'"><a href="sohoadmin/plugins/viastepphotogallery/' . $row['IMAGE_NAME'] . '" title=" '.$row['IMAGE_TITLE'].'" class="highslide" onclick="return hs.expand(this, { slideshowGroup: \'' . $row['ALBUM_ID'] . '\' } )"><img width="'.$thumb_width.'" border="0" src="'.$fname.'" alt="'.$row['IMAGE_TITLE'].'" hspace="4"></a><br>&nbsp</td>'."\n";
          break;
          case 5:
            $THIS_DISPLAY .= '<td align="center" class="album_item" valign="top" width="'.$thumb_width.'"><a href="sohoadmin/plugins/viastepphotogallery/' . $row['IMAGE_NAME'] . '" title=" '.$row['IMAGE_TITLE'].'" class="highslide" onclick="return hs.expand(this, { slideshowGroup: \'' . $row['ALBUM_ID'] . '\' } )"><img width="'.$thumb_width.'" border="0" src="'.$fname.'" alt="'.$row['IMAGE_TITLE'].'" hspace="4"></a><div class="highslide-caption">' . $row['IMAGE_TITLE'] . '<br />' . $row['IMAGE_DESC'] . '</div><br><b>'.$row['IMAGE_TITLE'].'</b><br><small>'.$row['IMAGE_DESC'].'</small><br>&nbsp</td>'."\n";
          break;
          case 6:
            $THIS_DISPLAY .= '<td align="center" class="album_item" valign="top" width="'.$thumb_width.'"><a href="sohoadmin/plugins/viastepphotogallery/' . $row['IMAGE_NAME'] . '" title=" '.$row['IMAGE_TITLE'].'" class="highslide" onclick="return hs.expand(this, { slideshowGroup: \'' . $row['ALBUM_ID'] . '\' } )"><img width="'.$thumb_width.'" border="0" src="'.$fname.'" alt="'.$row['IMAGE_TITLE'].'" hspace="4"></a><div class="highslide-caption">' . $row['IMAGE_TITLE'] . '</div><br><b>'.$row['IMAGE_TITLE'].'</b><br>&nbsp</td>'."\n";
          break;
          case 7:
            $THIS_DISPLAY .= '<td align="center" class="album_item" valign="top" width="'.$thumb_width.'"><b>'.$row['IMAGE_TITLE'].'</b><br><a href="sohoadmin/plugins/viastepphotogallery/' . $row['IMAGE_NAME'] . '" title="'.$row['IMAGE_TITLE'].'" class="highslide" onclick="return hs.expand(this, { slideshowGroup: \'' . $row['ALBUM_ID'] . '\' } )"><img width="'.$thumb_width.'" border="0" src="'.$fname.'" alt="' . $row['IMAGE_TITLE'] . '" hspace="8"></a><div class="highslide-caption"><b>' . $row['IMAGE_TITLE'] . '</b><br />' . $row['IMAGE_DESC'] . '<br />' . $row['user_html'] . '</div><br /><small>'.$row['user_html'].'&nbsp</small></td>'."\n";
          break;
          case 8:
            $THIS_DISPLAY .= '<td align="center" class="album_item" valign="top" width="'.$thumb_width.'"><a href="sohoadmin/plugins/viastepphotogallery/' . $row['IMAGE_NAME'] . '" title=" '.$row['IMAGE_TITLE'].'" class="highslide" onclick="return hs.expand(this, { slideshowGroup: \'' . $row['ALBUM_ID'] . '\' } )"><img width="'.$thumb_width.'" border="0" src="'.$fname.'" alt="' . $row['IMAGE_TITLE'] . '" hspace="8"></a><div class="highslide-caption">' . $row['user_html'] . '</div><br /><small>'.$row['user_html'].'&nbsp</small></td>'."\n";
          break;
        }

        $cnt++;
        if ($cnt>=$columns)
        {
          $cnt=0;
          $THIS_DISPLAY .= "</tr>\n<tr style=\"font-family:Arial,Geneva,Helvetica,sans-serif;font-size:12px;font-weight:normal;\">";
        }
      }
  $THIS_DISPLAY .= '</tr></table>';
      $THIS_DISPLAY.= "\n</td></tr></table><br>$scroll";
    }
    else
    {
      $THIS_DISPLAY='';
    }
  }
  else
  {
    $THIS_DISPLAY .= "<table border=\"0\" class=\"album_table\">
                      <tr style=\"font-family:Arial,Geneva,Helvetica,sans-serif;font-size:12px;font-weight:normal;\">";

      $link_prefix=(preg_replace("/m_id"."\=[^\&]*\&/i", "", $_SERVER['REQUEST_URI']));
      $link_prefix=(preg_replace("/m_id"."\=[^\&]*/i", "", $link_prefix));
      $link_prefix=(preg_replace("/[\?|\&]$/", "", $link_prefix));

      if (ereg('\?', $link_prefix))
        $link_prefix = $link_prefix.'&';
      else
        $link_prefix = $link_prefix.'?';

      reset($_GET);
      $link='';
      while (list($key, $value) = each($_GET)) {
        if (!is_array($_GET[$key])) {
          if ($key!='aoffset')
            $link.=$key.'='.$value.'&';
        }
      }
      if ($link!='')
        $link='?'.$link;

	if (!ereg('\?', $link))
        $link='?'.$link;

    $on_page=$rows*$columns;
    $cnt=0;
    $result = mysql_query("SELECT count(*) as cnt FROM premium_album");
    if ($row=mysql_fetch_array($result))
        $cnt = $row['cnt'];
    if($show_thumbs != true)
	    $scroll = make_scroll($link."aoffset=", $cnt, $aoffset, $on_page, 'album_link');
    $cnt=0;
    $onlyone = '';
    $limit = '';
    if($show_thumbs == true)
    	$onlyone = " WHERE `PRIKEY` = $id ";
    else
    	$limit = " LIMIT ".$aoffset*$on_page.", $on_page";
    $result = mysql_query("SELECT * FROM premium_album $onlyone ORDER BY `ALBUM_NAME`" . $limit);
    while ($row = mysql_fetch_array($result))
    {
      $ext   = extract_ext($row['ALBUM_THUMB']);
      $fname = basename($row['ALBUM_THUMB'], '.'.$ext);
      $fname = 'sohoadmin/plugins/viastepphotogallery/images/premium/'.$fname.'.'.$ext;
      $res = mysql_query("SELECT count(*) as image_count, sum(FILE_SIZE) as album_size FROM premium_album_images WHERE ALBUM_ID='".$row['PRIKEY']."'");
      $thumb_width=$row['THUMB_WIDTH'];
	  $m_id_posfix = $row['PRIKEY'];
      $link_prefix=(preg_replace("/m_id".$m_id_posfix."\=[^\&]*\&/i", "", $_SERVER['REQUEST_URI']));
      $link_prefix=(preg_replace("/m_id".$m_id_posfix."\=[^\&]*/i", "", $link_prefix));
      $link_prefix=(preg_replace("/[\?|\&]$/", "", $link_prefix));
      $imgStyle = ' style="padding-top:15px;" ';
      if (ereg('\?', $link_prefix))
        $link_prefix=$link_prefix.'&m_id'.$m_id_posfix.'=';
      else
        $link_prefix=$link_prefix.'?m_id'.$m_id_posfix.'=';
      if ($in_row = mysql_fetch_array($res))
      {
        switch ($layout){
          case 1:
            $THIS_DISPLAY .= '<td align="center" class="album_item" valign="top" width="'.$thumb_width.'"><a href="'.$link_prefix.$row['PRIKEY'].'" title="Album: '.$row['ALBUM_NAME'].', photos:'.$in_row['image_count'].'"><img width="'.$thumb_width.'" border="0" src="'.$fname.'" alt="Album: '.$row['ALBUM_NAME'].', photos:'.$in_row['image_count'].'" hspace="8"></a></td><td width="'.$thumb_width.'" align="center" valign="top"><b>'.$row['ALBUM_NAME'].'</b><br>'.$in_row['image_count'].' photos</td>'."\n";
          break;
          case 2:
            $THIS_DISPLAY .= '<td align="center" class="album_item" valign="top" width="'.$thumb_width.'"><b>'.$row['ALBUM_NAME'].'</b><br><a href="'.$link_prefix.$row['PRIKEY'].'" title="Album: '.$row['ALBUM_NAME'].', photos:'.$in_row['image_count'].'"><img width="'.$thumb_width.'" border="0" src="'.$fname.'" alt="Album: '.$row['ALBUM_NAME'].', photos:'.$in_row['image_count'].'" hspace="8"></a><br>'.$in_row['image_count'].' photos<br>&nbsp</td>'."\n";
          break;
          case 3:
            $THIS_DISPLAY .= '<td align="center" class="album_item" valign="top" width="'.$thumb_width.'"><b>'.$row['ALBUM_NAME'].'</b><br><a href="'.$link_prefix.$row['PRIKEY'].'" title="Album: '.$row['ALBUM_NAME'].', photos:'.$in_row['image_count'].'"><img width="'.$thumb_width.'" border="0" src="'.$fname.'" alt="Album: '.$row['ALBUM_NAME'].', photos:'.$in_row['image_count'].'" hspace="8"></a><br>&nbsp</td>'."\n";
          break;
          case 4:
            $THIS_DISPLAY .= '<td align="center" class="album_item" valign="top" width="'.$thumb_width.'"><a href="'.$link_prefix.$row['PRIKEY'].'" title="Album: '.$row['ALBUM_NAME'].', photos:'.$in_row['image_count'].'"><img width="'.$thumb_width.'" border="0" src="'.$fname.'" alt="Album: '.$row['ALBUM_NAME'].', photos:'.$in_row['image_count'].'" hspace="8"></a><br>&nbsp</td>'."\n";
          break;
          case 5:
            $THIS_DISPLAY .= '<td align="center" class="album_item" valign="top" width="'.$thumb_width.'"><a href="'.$link_prefix.$row['PRIKEY'].'" title="Album: '.$row['ALBUM_NAME'].', photos:'.$in_row['image_count'].'"><img width="'.$thumb_width.'" border="0" src="'.$fname.'" alt="Album: '.$row['ALBUM_NAME'].', photos:'.$in_row['image_count'].'" hspace="8"></a><br><b>'.$row['ALBUM_NAME'].'</b><br>'.$in_row['image_count'].' photos<br>&nbsp</td>'."\n";
          break;
          case 6:
            $THIS_DISPLAY .= '<td align="center" class="album_item" valign="top" width="'.$thumb_width.'"><a href="'.$link_prefix.$row['PRIKEY'].'" title="Album: '.$row['ALBUM_NAME'].', photos:'.$in_row['image_count'].'"><img width="'.$thumb_width.'" border="0" src="'.$fname.'" alt="Album: '.$row['ALBUM_NAME'].', photos:'.$in_row['image_count'].'" hspace="8"></a><br><b>'.$row['ALBUM_NAME'].'</b><br>&nbsp</td>'."\n";
          break;
          case 7:
            $THIS_DISPLAY .= '<td align="center" class="album_item" valign="top" width="'.$thumb_width.'"><a href="'.$link_prefix.$row['PRIKEY'].'" title="Album: '.$row['ALBUM_NAME'].', photos:'.$in_row['image_count'].'"><img width="'.$thumb_width.'" border="0" src="'.$fname.'" alt="Album: '.$row['ALBUM_NAME'].', photos:'.$in_row['image_count'].'" hspace="8" '.$imgStyle.'></a><br><b>'.$row['ALBUM_NAME'].'</b><br>&nbsp</td>'."\n";
          break;
          case 8:
            $THIS_DISPLAY .= '<td align="center" class="album_item" valign="top" width="'.$thumb_width.'"><a href="'.$link_prefix.$row['PRIKEY'].'" title="Album: '.$row['ALBUM_NAME'].', photos:'.$in_row['image_count'].'"><img width="'.$thumb_width.'" border="0" src="'.$fname.'" alt="Album: '.$row['ALBUM_NAME'].', photos:'.$in_row['image_count'].'" hspace="8"></a><br><b>'.$row['ALBUM_NAME'].'</b><br>&nbsp</td>'."\n";
          break;
        }
        $cnt++;
      }

      if ($cnt>=$columns)
      {
        $cnt=0;
        $THIS_DISPLAY.= "</tr>\n<tr style=\"font-family:Arial,Geneva,Helvetica,sans-serif;font-size:12px;font-weight:normal;\">";
      }
    }
    $THIS_DISPLAY.= "\n</tr></table>$scroll";
  }
  echo $THIS_DISPLAY;
}
?>