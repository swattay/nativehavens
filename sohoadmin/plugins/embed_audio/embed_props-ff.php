

<!-- ############################################################# -->
<!-- #### BEGIN EMBED AUDIO FILE										  #### -->
<!-- ############################################################# -->

<DIV ID="embedlayer" style="position:absolute; left:0px; top:0px; width:100%; height:15%; z-index:4; overflow: none; background-color: oldlace; layer-background-color: oldlace; visibility: hidden;">

	<table border=1 cellpadding=0 cellspacing=0 width=100% height=100% style='border: 1px inset black;'>
	<tr>
	<td align=left valign=middle width=100%>

	<table border=0 cellpadding=4 cellspacing=0 width=100%>
		<tr><td align=CENTER valign=top class=ctable>
		<FONT COLOR=DARKBLUE>Choose an audio file to play as background music for your site visitors.</FONT><BR><BR>

		Filename: &nbsp;
		<SELECT id="embedname" NAME="embedname" STYLE='font-family: Arial; font-size: 8pt; width: 350px;'>
		<option value="NONE" style='color: #999999;'>Audio Files:</option>

		<?

		for ($a=1;$a<=$mp3media;$a++) {
			if ($tmp == "#EFEFEF") { $tmp = "WHITE"; } else { $tmp = "#EFEFEF"; }
			echo "<option value=\"$mp3file[$a]\" style='background: $tmp;'>$mp3file[$a]</option>\n";
		}

		?>

		</select>

		</td><td align=center valign=middle>

		<input type=button class=mikebut onMouseOver="this.className='mikebutOn';" onMouseOut="this.className='mikebut';" value=" OK " onClick="OkEmbedData();show_hide_layer('objectbar_mods','','show','embedlayer','','hide');">
		&nbsp;&nbsp;<input type=button class=mikebut onMouseOver="this.className='mikebutOn';" onMouseOut="this.className='mikebut';" value=" Cancel " onClick="show_hide_layer('objectbar_mods','','show','embedlayer','','hide');replaceImageData();makeUnScroll(ColRowID);">
		</td></tr></table>
	</td>
	</tr>
	</table>

</DIV>

