<?

// [ADD START] PREMIUM ALBUMS. ADDED BY D. CHAPLINSKY, VIASTEP 12:50 10.11.2005
if ($CUR_USER_ACCESS == "WEBMASTER" || eregi(";MOD_PHOTO_ALBUM;", $CUR_USER_ACCESS)) {
   $prem_album['link'] = eregi_replace("#LOC#", "../extensions/viastepphotogallery/premium_album_module/premium_album.php", $is_active);
   $prem_album['icon'] = "<img src=\"../extensions/viastepphotogallery/photo_new-on.gif\" ".$prem_album['link']." style=\"cursor: pointer;\"><br>\n";
} else {
   $prem_album['link'] = $no_access;
   $prem_album['icon'] = "<img src=\"../extensions/viastepphotogallery/photo_albums-off.gif\" ".$prem_album['link']." style=\"cursor: pointer;\"><br>\n";
}
// [ADD END] PREMIUM ALBUMS. ADDED BY D. CHAPLINSKY, VIASTEP 12:50 10.11.2005

?>