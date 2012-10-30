<?

// [ADD START] PREMIUM ALBUMS. ADDED BY D. CHAPLINSKY, VIASTEP 14:35 10.11.2005
function create_thumbnail($source, $res, $width=200, $quality=70)
{
  $path_parts = pathinfo(strtolower($source));
  switch ($path_parts["extension"])
  {
    case "jpg":
      $im = @imagecreatefromjpeg ($source);
    break;
    case "jpeg":
      $im = @imagecreatefromjpeg ($source);
    break;
    case "gif":
      $im = @imagecreatefromgif ($source);
    break;
    case "png":
      $im = @imagecreatefrompng ($source);
    break;
    default:
      return false;
    break;
  };
//  if (imagesx($im)>$width)
//  {
    $ratio = $width/imagesx($im);
    $x = $width;
    $y = (int) imagesy($im)*$ratio;
    $im_processed = imagecreatetruecolor ($x, $y);
    if (function_exists('imagecopyresampled'))
      imagecopyresampled($im_processed, $im, 0, 0, 0, 0, $x+1, $y+1, imagesx($im), imagesy($im));
    else
      imagecopyresized($im_processed, $im, 0, 0, 0, 0, $x+1, $y+1, imagesx($im), imagesy($im));
    touch($res);
    imagejpeg($im_processed, $res, $quality);
//  }
//  else
//    imagejpeg($im, $res, $quality);

  imagedestroy($im);
  imagedestroy($im_processed);
  return true;
}

function ToJpeg($source, $res, $method='no', $size=800, $quality=100)
{
//  error_reporting(E_WARNING);
  $path_parts = pathinfo(strtolower($source));
  switch ($path_parts["extension"])
  {
    case "jpg":
      $im = @imagecreatefromjpeg ($source);
    break;
    case "jpeg":
      $im = @imagecreatefromjpeg ($source);
    break;
    case "gif":
      $im = @imagecreatefromgif ($source);
    break;
    case "png":
      $im = @imagecreatefrompng ($source);
    break;
    default:
      return false;
    break;
  };

  switch ($method)
  {
    case 'no':
      touch($res);
      imagejpeg($im, $res, $quality);
    break;
    case 'width':
      $width=$size;
      $ratio = $width/imagesx($im);
      if ($ratio>1)
      {
        $ratio=1;
        $x=imagesx($im);
      }
      else
        $x = $width;

      $y = (int) imagesy($im)*$ratio;
      $im_processed = imagecreatetruecolor ($x, $y);
      if (function_exists('imagecopyresampled'))
        imagecopyresampled($im_processed, $im, 0, 0, 0, 0, $x+1, $y+1, imagesx($im), imagesy($im));
      else
        imagecopyresized($im_processed, $im, 0, 0, 0, 0, $x+1, $y+1, imagesx($im), imagesy($im));
      touch($res);
      imagejpeg($im_processed, $res, $quality);
      imagedestroy($im_processed);
    break;

    case 'biggest':
      if (imagesx($im)> imagesy($im))
      {
        $width=$size;
        $ratio = $width/imagesx($im);

        if ($ratio>1)
        {
          $ratio=1;
          $x=imagesx($im);
        }
        else
          $x = $width;

        $y = (int) imagesy($im)*$ratio;
        $im_processed = imagecreatetruecolor ($x, $y);
        if (function_exists('imagecopyresampled'))
          imagecopyresampled($im_processed, $im, 0, 0, 0, 0, $x+1, $y+1, imagesx($im), imagesy($im));
        else
          imagecopyresized($im_processed, $im, 0, 0, 0, 0, $x+1, $y+1, imagesx($im), imagesy($im));
        touch($res);
        imagejpeg($im_processed, $res, $quality);
        imagedestroy($im_processed);
      }
      else
      {
        $height=$size;
        $ratio = $height/imagesy($im);
        if ($ratio>1)
        {
          $ratio=1;
          $y=imagesy($im);
        }
        else
          $y = $height;

        $x = (int) imagesx($im)*$ratio;

        $im_processed = imagecreatetruecolor ($x, $y);
        if (function_exists('imagecopyresampled'))
          imagecopyresampled($im_processed, $im, 0, 0, 0, 0, $x+1, $y+1, imagesx($im), imagesy($im));
        else
          imagecopyresized($im_processed, $im, 0, 0, 0, 0, $x+1, $y+1, imagesx($im), imagesy($im));
        touch($res);
        imagejpeg($im_processed, $res, $quality);
        imagedestroy($im_processed);
      }
    break;
  }

  imagedestroy($im);
  return true;
}

function get_image_info ($source)
{
  $path_parts = pathinfo(strtolower($source));
  switch ($path_parts["extension"])
  {
    case "jpg":
      $im = @imagecreatefromjpeg ($source);
    break;
    case "jpeg":
      $im = @imagecreatefromjpeg ($source);
    break;
    case "gif":
      $im = @imagecreatefromgif ($source);
    break;
    case "png":
      $im = @imagecreatefrompng ($source);
    break;
    default:
      return false;
    break;
  };
  $w=imagesx($im);
  $h=imagesy($im);
  $s=filesize($source);

  imagedestroy($im);
  return array('width'=>$w, 'height'=>$h, 'size'=>$s);
}

function extract_filename($full)
{
  $path_parts = pathinfo($full);
  return $path_parts["basename"];
}
function extract_ext($full)
{
  $path_parts = pathinfo($full);
  return $path_parts["extension"];
}

function my_lang($string) {
   global $lang;
   if ( $lang[$string]!='' ) {
      echo $lang["Operation"];
      return $lang[$string];
   } else {
      return $string;
   }
}

function make_scroll($LinkPrefix, $Count, $Offset, $ItemsOnPage, $html_class = 'small')
{
  global $sid;
  $Ret='';
  if (($Count>$ItemsOnPage)&&($ItemsOnPage!=0))
  {
    // Добавляем ссылку <<
    if ($Offset>0)
    {
     $prev = $Offset - 1;
     if ($prev<0)
       $prev=0;

     $Ret .= "<a href=\"$LinkPrefix".'0'."\" href=$LinkPrefix&offset=0 class=\"$html_class\">&laquo;&laquo;</a> &nbsp;&nbsp;";
     $Ret .= "<a href=\"$LinkPrefix$prev\" class=\"$html_class\">&laquo;</a> &nbsp;&nbsp;";
    }
    // Вычисляем цифру, с которой начнутся ссылки
    if ($Offset>=4)
      $left=$Offset-4;
    else
      $left=0;

    // Вычисляем цифру, которой закончатся ссылки

    if (ceil($Count/$ItemsOnPage)>$left+10)
      $right=$left+10;
    else
    {
      $right=ceil($Count/$ItemsOnPage);
      if (($right-10)>0)
        $left=$right-10;
    }

    // Рисуем цифровые ссылки
    for ($i = $left+1; $i<=$right; $i++ ){
     if ($i-1==$Offset){
       $Ret .= "<B><span class=\"$html_class\">[$i]</span></B>&nbsp;&nbsp;";
     }
     else
     {
       $i_m_1=$i-1;
       $Ret .= "<a href=\"$LinkPrefix$i_m_1\" class=\"$html_class\">$i</a>&nbsp;&nbsp;";
     }
    }

    // Рисуем ссылку >>
    if (($Offset+1)*$ItemsOnPage<$Count)
    {
     $next = $Offset + 1;
     $last = floor(($Count-1)/$ItemsOnPage);
     $Ret .= "<a href=\"$LinkPrefix$next\" class=\"$html_class\">&raquo;</a> &nbsp;&nbsp;";
     $Ret .= "<a href=\"$LinkPrefix$last\" class=\"$html_class\">&raquo;&raquo;</a> &nbsp;&nbsp;";
    }
    $Ret = substr ($Ret , 0 , -7);
    $Ret = "<div align=\"center\" class=\"$html_class\">".$Ret."</div>";
  }
  return $Ret;
}
function my_rmdirr($dirname, $level=0)
{
    // Sanity check
    if (!file_exists($dirname)) {
        return false;
    }

    // Simple delete for a file
    if (is_file($dirname)) {
        return unlink($dirname);
    }

    // Loop through the folder
    $dir = dir($dirname);
    while (false !== $entry = $dir->read()) {
        // Skip pointers
        if ($entry == '.' || $entry == '..') {
            continue;
        }

        // Recurse
        rmdirr("$dirname/$entry", $level+1);
    }

    // Clean up
    $dir->close();
    if ($level!=0)
      return rmdir($dirname);
    else
      return true;
}

// [ADD END] PREMIUM ALBUMS. ADDED BY D. CHAPLINSKY, VIASTEP 14:35 10.11.2005

?>