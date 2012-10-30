<?

//[ADD START] PREMIUM ALBUMS. ADDED BY D. CHAPLINSKY, VIASTEP 2:21 14.11.2005
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
// INSERT CODE FOR PREMIUM ALBUM
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if (eregi("##PREMIUM_ALBUM", $content_line[$sohocontent])) {
   $tmp = eregi("<!-- ##PREMIUM_ALBUM;(.*)## -->", $content_line[$sohocontent], $out);
   $BLOG_CATEGORY_NAME = $out[1];


   $filename = "sohoadmin/plugins/viastepphotogallery/pgm-premium_album.php";
//          echo $BLOG_CATEGORY_NAME;
   ob_start();
   include("$filename");
   $content_line[$sohocontent] = ob_get_contents();
   ob_end_clean();

} // PREMIUM ALBUM
//[ADD END] PREMIUM ALBUMS. ADDED BY D. CHAPLINSKY, VIASTEP 2:21 14.11.2005


?>