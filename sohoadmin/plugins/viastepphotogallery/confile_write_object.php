<?

########################################
#### PREMIUM ALBUM   (4.5 MOD)      ####
########################################

//[ADD START] PREMIUM ALBUMS. ADDED BY D. CHAPLINSKY, VIASTEP 2:34 14.11.2005
if (eregi("##PREMIUM_ALBUM", $thisobj)) {
   $tmp = eregi("<!-- ##PREMIUM_ALBUM;(.*)## -->", $thisobj, $out);
   $dataname = $out[1];
   $droparea .= "\n\n<!-- ##PREMIUM_ALBUM;$dataname## -->\n\n";
}
//[ADD END] PREMIUM ALBUMS. ADDED BY D. CHAPLINSKY, VIASTEP 2:34 14.11.2005


?>