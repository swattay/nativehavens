<?

########################################
#### SITEPAL PLUGIN                 ####
########################################

if (eregi("##SITEPAL", $thisobj)) {
   $tmp = eregi("<!-- ##SITEPAL;(.*)## -->", $thisobj, $out);
   $dataname = $out[1];
   $droparea .= "\n\n<!-- ##SITEPAL;$dataname## -->\n\n";
}


?>