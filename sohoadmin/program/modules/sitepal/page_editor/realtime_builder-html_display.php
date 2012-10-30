<?

$sitepal = new userdata("sitepal");
$getSp = $sitepal->get();

# SitePal Plugin: pgm-realtime_builder.php include
# Reads confile data written by object_write.php and builds final display html
# Displays SitePal scene/character on website
if ( eregi("##SITEPAL", $content_line[$sohocontent]) ) {
//   echo "here"; exit;
   # Get scene number
   $tmp = eregi("<!-- ##SITEPAL;(.*)## -->", $content_line[$sohocontent], $out);
   $spal_info = split(";", $out[1]);
   $spal_scene = $spal_info[0];
   $spal_width = $spal_info[1];
   $spal_height = $spal_info[2];
   $spal_account = $spal_info[3];


   # Append HTML to this line of content under comment tag trigger
//   $content_line[$sohocontent] = "billy";
   $content_line[$sohocontent] = "<script language=\"JavaScript\" type=\"text/javascript\" src=\"http://vhost.oddcast.com/vhost_embed_functions.php?acc=".$spal_account."&followCursor=1&js=1\"></script>\n";
   $content_line[$sohocontent] .= "<script language=\"JavaScript\" type=\"text/javascript\">\n";
   $content_line[$sohocontent] .= " AC_VHost_Embed_".$spal_account."(".$spal_height.",".$spal_width.",'FFFFFF',1,1,".$spal_scene.",0,0,0,'917d80ee584ab23dc9acd94cc1ddae0e',6);\n";
//   $content_line[$sohocontent] .= " AC_VHost_Embed_".$spal_account."(".$spal_width.",".$spal_height.",'FFFFFF',1,1,".$spal_scene.",0,0,0,'917d80ee584ab23dc9acd94cc1ddae0e',6);\n";
   $content_line[$sohocontent] .= "</script>\n";

}


?>