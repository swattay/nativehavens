<SCRIPT LANGUAGE=Javascript>

function start_recur(a) {

	no_recur();
	RSECONDARY.style.display = '';
	eval(a+".style.display = '';");

}

function no_recur() {

	RSECONDARY.style.display = 'none';
	RECUR1.style.display = 'none';
	RECUR2.style.display = 'none';
	RECUR3.style.display = 'none';
	RECUR4.style.display = 'none';

}

</SCRIPT>

<style>

form {
   margin:0;
}

</style>

<?

$display_dow = date("l", mktime(0,0,0,$am,$ad,$ay));
$string_month = date("F", mktime(0,0,0,$am,$ad,$ay));
$event_date = $string_month." $ad, $ay";

// Pre-build Mouseover script for new v4.7 buttons (because nobody likes side-scrolling)
$editOn = "class=\"btn_edit\" onMouseover=\"this.className='btn_editon';\" onMouseout=\"this.className='btn_edit';\"";
$saveOn = "class=\"btn_save\" onMouseover=\"this.className='btn_saveon';\" onMouseout=\"this.className='btn_save';\"";
?>

<FORM METHOD=POST ACTION="add_event.php">
<INPUT TYPE=HIDDEN NAME="ACTION" VALUE="SAVE_EVENT">
<INPUT TYPE=HIDDEN NAME="EVENT_DATE" VALUE="<? echo "$ay-$am-$ad"; ?>">

<TABLE WIDTH="700" BORDER="0" ALIGN="CENTER" CELLPADDING="5" CELLSPACING="0" class="feature_sub">
 <TR>
  <TD WIDTH="50%" ALIGN="LEFT" VALIGN="TOP" class="fsub_title">
   <? echo lang("Apply To"); ?>:
	<SELECT NAME="APLLY_SAVE_ACTION" CLASS=text style='width: 200px; background: #EFEFEF;'>
    <OPTION VALUE="A" SELECTED><? echo lang("THIS EVENT ONLY"); ?></OPTION>
    <!-- <OPTION VALUE="B"><? echo lang("All occurrences of this event"); ?></OPTION> -->
   </SELECT>
  </TD>
  <TD WIDTH="50%" ALIGN=RIGHT class="fsub_title" style="padding-right: 5px;">
	<INPUT TYPE="SUBMIT" VALUE=" <? echo lang("Save Event"); ?> " <? echo $saveOn; ?>>
  </td>
 </tr>
 <tr align="right">
  <td width="50%" align="left"><? echo lang("Event Date"); ?>: <B><? echo $display_dow.", ".$event_date; ?></B>
   &nbsp;&nbsp;</TD>
  <td width="50%"> <? echo lang("Start Time"); ?>:
   <select name="START_TIME" class="text" style='width: 75px;'>
	 <option value="" selected>n/a</option>

		<?

		$clock_flag = "am";
		for ($z=1;$z<=24;$z++) {

			$v = $z;
			$v2 = $z;

			if ($z > 12) { $v = $z-12; }
			if ($v2 < 10) { $v2 = "0".$v2; }

			if ($v == 12) { $clock_flag = "pm"; }
			if ($z == 24) { $clock_flag = "am"; }

			$d = "$v:00 $clock_flag";
			// if ($d == "9:00 am") { $SEL = "SELECTED"; } else { $SEL = ""; }
			echo "<OPTION VALUE=\"$v2:00\" STYLE='background: #EFEFEF; color: black;' $SEL>$d</OPTION>\n";
			echo "<OPTION VALUE=\"$v2:15\" STYLE='color: #999999;'>$v:15 $clock_flag</OPTION>\n";
			echo "<OPTION VALUE=\"$v2:30\" STYLE='color: #999999;'>$v:30 $clock_flag</OPTION>\n";
			echo "<OPTION VALUE=\"$v2:45\" STYLE='color: #999999;'>$v:45 $clock_flag</OPTION>\n";

		}

		?>
      </SELECT> <? echo lang("End Time"); ?>:
      <SELECT NAME="END_TIME" CLASS="text" STYLE='width: 75px;'>
      <OPTION VALUE="[none]">[none]</OPTION>
      <OPTION VALUE="" SELECTED>N/A</OPTION>

		<?

		$clock_flag = "am";
		for ($z=1;$z<=24;$z++) {

			$v = $z;
			$v2 = $z;

			if ($z > 12) { $v = $z-12; }
			if ($v2 < 10) { $v2 = "0".$v2; }

			if ($v == 12) { $clock_flag = "pm"; }
			if ($z == 24) { $clock_flag = "am"; }

			$d = "$v:00 $clock_flag";
			echo "<OPTION VALUE=\"$v2:00\" STYLE='background: #EFEFEF; color: black;' $SEL>$d</OPTION>\n";
			echo "<OPTION VALUE=\"$v2:15\" STYLE='color: #999999;'>$v:15 $clock_flag</OPTION>\n";
			echo "<OPTION VALUE=\"$v2:30\" STYLE='color: #999999;'>$v:30 $clock_flag</OPTION>\n";
			echo "<OPTION VALUE=\"$v2:45\" STYLE='color: #999999;'>$v:45 $clock_flag</OPTION>\n";

		}

		?>

      </SELECT> </TD>
  </TR>
  <TR>
    <TD COLSPAN="2"><? echo lang("Event Title"); ?>:<BR> <INPUT TYPE="text" NAME="EVENT_TITLE" CLASS="text" STYLE='width: 100%;'>
    </TD>
  </TR>
  <TR>
    <TD><? echo lang("Event Details (Description)"); ?>:<BR> <TEXTAREA NAME="EVENT_DETAILS" CLASS="text" STYLE="width: 100%; HEIGHT: 115px;" WRAP=VIRTUAL></TEXTAREA>
    </TD>
    <TD ALIGN="LEFT" VALIGN="TOP"><? echo lang("Event Category"); ?>:<BR> <SELECT NAME="EVENT_CATEGORY" CLASS="text" STYLE='width: 200px;'>
        <OPTION VALUE="ALL" SELECTED><? echo lang("All"); ?></OPTION>
        <?

		$result = mysql_query("SELECT * FROM calendar_category ORDER BY Category_Name");
		while ($row = mysql_fetch_array($result)) {
			echo "<OPTION VALUE=\"$row[PRIKEY]\">$row[Category_Name]</OPTION>\n";
		}

		?>
      </SELECT>
      <BR>
      <BR>
      <? echo lang("Security Code (Group)"); ?>:<BR> <SELECT NAME="EVENT_SECURITYCODE" CLASS="text" ID="EVENT_SECURITYCODE" STYLE='width: 200px;'>
        <OPTION VALUE="Public" SELECTED><? echo lang("Public"); ?></OPTION>
		<?

		$result = mysql_query("SELECT * FROM sec_codes ORDER BY security_code");
		while ($row = mysql_fetch_array($result)) {
			echo "<OPTION VALUE=\"$row[security_code]\">$row[security_code]</OPTION>\n";
		}

		?>
      </SELECT>
      <BR>
      <BR>
      Detail Page:<BR> <SELECT NAME="EVENT_DETAILPAGE" CLASS="text" ID="EVENT_DETAILPAGE" STYLE='width: 200px;'>
        <OPTION VALUE="" SELECTED>N/A</OPTION>
		<?

		// Removed reliance upon "type" pages in V4.6 (Still works for upgrades)
		$result = mysql_query("SELECT page_name FROM site_pages ORDER BY page_name");
		while ($row = mysql_fetch_array($result)) {
			echo "<OPTION VALUE=\"$row[page_name]\">$row[page_name]</OPTION>\n";
		}

		?>
      </SELECT> </TD>
  </TR>
  <TR>
    <TD><? echo lang("When saving or changing this event, email a notice to the following email addresses"); ?>:<BR>
      <INPUT TYPE="text" NAME="EVENT_EMAIL_CC" CLASS="text" STYLE='width: 100%;'>
    </TD>
    <TD ALIGN="LEFT" VALIGN="TOP">&nbsp; </TD>
  </TR>

<!-- RECURRENCE SECTION START --------------------------------- -->

 <TR>
  <TD WIDTH="50%" COLSPAN="2" ALIGN="LEFT" VALIGN="TOP" class="fsub_title">
   <? echo lang("Event Recurrence"); ?>
  </TD>
 </TR>
 <TR ALIGN="RIGHT">
  <TD WIDTH="50%" COLSPAN="2" ALIGN="LEFT">
	<INPUT TYPE="radio" NAME="RECUR_FREQUENCY"VALUE="NONE" CHECKED onclick="no_recur();">
      <? echo lang("No Recurrence"); ?> &nbsp;&nbsp;&nbsp;
      <INPUT TYPE="radio" NAME="RECUR_FREQUENCY" VALUE="DAILY" onclick="start_recur('RECUR1');"> <? echo lang("Daily"); ?> &nbsp;&nbsp;&nbsp;&nbsp;
	<INPUT TYPE="radio" NAME="RECUR_FREQUENCY" VALUE="WEEKLY" onclick="start_recur('RECUR2');"> <? echo lang("Weekly"); ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<INPUT TYPE="radio" NAME="RECUR_FREQUENCY" VALUE="MONTHLY" onclick="start_recur('RECUR3');"> <? echo lang("Monthly"); ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<INPUT TYPE="radio" NAME="RECUR_FREQUENCY" VALUE="YEARLY" onclick="start_recur('RECUR4');"> <? echo lang("Yearly"); ?>
	</TD>
  </TR>

  <!-- DAILY RECUR OPTIONS -->

  <TR ID="RECUR1" STYLE='DISPLAY: NONE';>
    <TD COLSPAN="2"><B><u><? echo lang("Daily Pattern"); ?></u>:</B><BR>
      <BR>
      <? echo lang("This event should re-occur every"); ?>
      <INPUT NAME="DAILY_NUMDAYS" CLASS=text TYPE="text" SIZE="5">
      <? echo lang("days"); ?>.<BR>
      &nbsp; &nbsp;&nbsp;</TD>
  </TR>

  <!-- WEEKLY RECUR OPTIONS -->

  <TR ID="RECUR2" STYLE='DISPLAY: NONE';>
    <TD COLSPAN="2"><B><u><? echo lang("Weekly Pattern"); ?></u>:</B><BR>
      <BR>
      <? echo lang("This event should re-occur every"); ?>
      <INPUT NAME="WEEKLY_NUMWEEKS" CLASS=text TYPE="text" SIZE="5">
      <? echo lang("weeks on"); ?>:<BR>
      <BR>
      <INPUT TYPE="checkbox" NAME="WEEKLY1" VALUE="SUNDAY">
      <? echo lang("Sunday"); ?>&nbsp;&nbsp;&nbsp;&nbsp;
      <INPUT TYPE="checkbox" NAME="WEEKLY2" VALUE="MONDAY">
      <? echo lang("Monday"); ?>&nbsp;&nbsp;&nbsp;&nbsp;
      <INPUT TYPE="checkbox" NAME="WEEKLY3" VALUE="TUESDAY">
      <? echo lang("Tuesday"); ?>&nbsp;&nbsp;&nbsp;&nbsp;
      <INPUT TYPE="checkbox" NAME="WEEKLY4" VALUE="WEDNESDAY">
      <? echo lang("Wednesday"); ?>&nbsp;&nbsp;&nbsp;&nbsp;
      <INPUT TYPE="checkbox" NAME="WEEKLY5" VALUE="THURSDAY">
      <? echo lang("Thursday"); ?>&nbsp;&nbsp;&nbsp;&nbsp;
      <INPUT TYPE="checkbox" NAME="WEEKLY6" VALUE="FRIDAY">
      <? echo lang("Friday"); ?>&nbsp;&nbsp;&nbsp;&nbsp;
      <INPUT TYPE="checkbox" NAME="WEEKLY7" VALUE="SATURDAY">
      <? echo lang("Saturday"); ?> <BR>
      &nbsp;&nbsp;&nbsp;</TD>
  </TR>

  <!-- MONTHLY RECUR OPTIONS -->

  <TR ID="RECUR3" STYLE='DISPLAY: NONE';>
    <TD COLSPAN="2"><B><u><? echo lang("Monthly Pattern"); ?></u>:</B><BR>
      <BR>
      <? echo lang("This event should re-occur on the"); ?>
      <SELECT NAME="MONTHLY_NUM">
        <OPTION VALUE="1" SELECTED>1st</OPTION>
        <OPTION VALUE="2">2nd</OPTION>
        <OPTION VALUE="3">3rd</OPTION>
        <OPTION VALUE="4">4th</OPTION>
      </SELECT>
      <SELECT NAME="MONTHLY_DOW">
	  	<OPTION VALUE="Sunday"><? echo lang("Sunday"); ?></OPTION>
        <OPTION VALUE="Monday" SELECTED><? echo lang("Monday"); ?></OPTION>
        <OPTION VALUE="Tuesday"><? echo lang("Tuesday"); ?></OPTION>
        <OPTION VALUE="Wednesday"><? echo lang("Wednesday"); ?></OPTION>
        <OPTION VALUE="Thursday"><? echo lang("Thursday"); ?></OPTION>
        <OPTION VALUE="Friday"><? echo lang("Friday"); ?></OPTION>
        <OPTION VALUE="Saturday"><? echo lang("Saturday"); ?></OPTION>

      </SELECT>
      <? echo lang("of each month"); ?>. <BR>
      &nbsp;&nbsp;&nbsp;&nbsp;</TD>
  </TR>

    <!-- YEARLY RECUR OPTIONS -->

  <TR ID="RECUR4" STYLE='DISPLAY: NONE';>
    <TD COLSPAN="2"><B><u><? echo lang("Yearly Pattern"); ?></u>:</B><BR>
      <BR>
      * <? echo lang("You have selected for this event to occurr every year on"); ?> <U><? echo $string_month . " " . $ad; ?></U>.<BR>
      &nbsp;&nbsp;</TD>
  </TR>

  <TR ID="RSECONDARY" STYLE='DISPLAY: NONE;'>
    <TD>
     <? echo lang("This event will start on the date of the selected 'Event Date' and continue for how long"); ?>?<BR> <INPUT TYPE="radio" NAME="RECUR_LENGTH" VALUE="UNLIMITED">
     <? echo lang("No End Date"); ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <INPUT NAME="RECUR_LENGTH" TYPE="radio" VALUE="LIMIT" CHECKED>
     <? echo lang("End after"); ?>
      <INPUT NAME="RECUR_LIMIT_NUMBER" TYPE="text" class=text VALUE="4" SIZE="5">
      <? echo lang("occurrences"); ?> </TD>
    <TD WIDTH="50%">&nbsp; </TD>
  </TR>
</TABLE>
</FORM>
