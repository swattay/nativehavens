

<!-- ############################################################# -->
<!-- #### BEGIN EMBED AUDIO FILE										  #### -->
<!-- ############################################################# -->

<DIV ID="embedlayer" class="prop_layer" style="" >

   <div class="prop_head">Embed Page Audio</div>

	<table cellpadding="0" cellspacing="0" width="100%" class="prop_table">
		<tr>
		<td align=center valign=top class=ctable>
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
		&nbsp;&nbsp;<input type=button class=mikebut onMouseOver="this.className='mikebutOn';" onMouseOut="this.className='mikebut';" value=" Cancel " onClick="show_hide_layer('objectbar_mods','','show','embedlayer','','hide');">
	</td>

	</tr>
	</table>

</DIV>

