<!-- [ADD START] PREMIUM ALBUMS. ADDED BY D. CHAPLINSKY, VIASTEP 0:54 14.11.2005-->
<!-- ############################################################# -->
<!-- #### BEGIN PREMIUM ALBUM LAYER             						  #### -->
<!-- ############################################################# -->

<DIV ID="premiumlayer" style="position:absolute; left:0px; top:0px; width:100%; height:15%; z-index:4; overflow: none; background-color: oldlace; visibility: hidden" onMouseOver="HighDropZone();">

	<table border=1 cellpadding=5 cellspacing=0 width=100% height=100% style='border: 1px inset black;'>
	<tr>
	<td align=center valign=middle>

		<table border=0 cellpadding=2 cellspacing=0 align="center" class="ctable">
		<tr>
		<td align=left valign=middle class="ctable">

		Select album to show: &nbsp;</td>
    <td>
		<SELECT id="albumname" NAME="albumname" style='font-face: Arial; font-size: 8pt; width: 250px;'>
			<option value="-1" STYLE='color:#999999;'>Let user select</option>

			<?

   		$result = mysql_query("SELECT * FROM premium_album ORDER BY ALBUM_NAME");

      while ($row=mysql_fetch_array($result))
      {
					if ($tmp == "#EFEFEF") { $tmp = "WHITE"; } else { $tmp = "#EFEFEF"; }
					echo "<option value=\"".$row['PRIKEY']."\" STYLE='background: $tmp;'>".$row['ALBUM_NAME']."</option>\n";
			}

			?>

		</SELECT></td>
    <td align=center valign=middle rowspan="5">

	 	<input type=button class=mikebut style="margin-bottom: 4px" onMouseOver="this.className='mikebutOn';" onMouseOut="this.className='mikebut';" value=" OK " onClick="OkPremiumData();show_hide_layer('objectbar','','show','premiumlayer','','hide');"><br>
		<input type=button class=mikebut onMouseOver="this.className='mikebutOn';" onMouseOut="this.className='mikebut';" value=" Cancel " onClick="show_hide_layer('objectbar','','show','premiumlayer','','hide');replaceImageData();makeUnScroll(ColRowID);">
		</td>

    </tr>
    <?php
      $columns = 5;
      $rows = 5;
	    $THIS_DISPLAY .= "   <tr><td>".lang("Gallery Layout")."</td>\n";
	    $THIS_DISPLAY .= '   <td class="ctable" valign="middle">
                              <input type="radio" name="my_layout" id="my_layout" value="1" checked> &nbsp <img src="images/left-right.gif" alt="left-right layout" width="50" height="50" align="middle">
                              <input type="radio" name="my_layout" id="my_layout" value="2"> &nbsp <img src="images/top-bottom.gif" alt="top-bottom layout" width="50" height="50" align="middle">
                              <input type="radio" name="my_layout" id="my_layout" value="3"> &nbsp <img src="images/top.gif" alt="top layout" width="50" height="50" align="middle">
                              <input type="radio" name="my_layout" id="my_layout" value="5"> &nbsp <img src="images/bottom-all.gif" alt="bottom all layout" width="50" height="50" align="middle">
                              <input type="radio" name="my_layout" id="my_layout" value="6"> &nbsp <img src="images/bottom.gif" alt="bottom layout" width="50" height="50" align="middle">
                           </td></tr>';
	    $THIS_DISPLAY .= "   <tr><td>".lang("Columns")."</td>\n";
	    $THIS_DISPLAY .= '   <td><input type="text" style="width: 30px" class="text" value="'.$columns.'" name="columns" id="columns">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.lang("Rows")."\n";
	    $THIS_DISPLAY .= '   &nbsp;&nbsp;&nbsp;&nbsp;<input type="text" style="width: 30px" class="text" value="'.$rows.'" name="rows" id="rows"></tr>';
      echo $THIS_DISPLAY;
		?>
		</table>
	</td>
	</tr>
	</table>

</DIV>
<!-- [ADD END] PREMIUM ALBUMS. ADDED BY D. CHAPLINSKY, VIASTEP 0:54 14.11.2005-->


?>