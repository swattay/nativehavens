<?

########################################
#### AUDIO FILE EMBED					####
########################################

if (eregi("##EMBEDME", $thisobj)) {

		$tmp = eregi("<!-- ##EMBEDME;(.*)## -->", $thisobj, $out);
		$audio_file = $out[1];

		$droparea .= "<embed src=\"media/".$audio_file."\" autostart=\"true\" loop=\"true\" width=\"0\" height=\"0\">\n";
		$droparea .= "  <noembed>\n";
		$droparea .= "    <bgsound src=\"media/".$audio_file."\" loop=\"infinite\">\n";
		$droparea .= "  </noembed>\n";
		$droparea .= "</embed>\n";

}

?>