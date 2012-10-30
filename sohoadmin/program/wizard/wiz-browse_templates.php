<?php
error_reporting(E_PARSE);
if($_GET['_SESSION'] != '' || $_POST['_SESSION'] != '' || $_COOKIE['_SESSION'] != '') { exit; }

###############################################################################
## Soholaunch(R) Studio Edition
## Version 1.0
##
## Author: 			Mike Johnston [mike.johnston@soholaunch.com]
## Homepage:	 	http://www.soholaunch.com
## Bug Reports: 	http://bugzilla.soholaunch.com
###############################################################################

##############################################################################
## COPYRIGHT NOTICE
## Copyright 1999-2004 Soholaunch.com, Inc.  All Rights Reserved.
##
## This script may be used and modified in accordance to the license
## agreement attached (license.txt) except where expressly noted within
## commented areas of the code body. This copyright notice and the comments
## comments above and below must remain intact at all times.  By using this
## code you agree to indemnify Soholaunch.com, Inc, its coporate agents
## and affiliates from any liability that might arise from its use.
##
## Selling the code for this program without prior written consent is
## expressly forbidden and in violation of Domestic and International
## copyright laws.
###############################################################################

if ($change == "") { $change = "site"; }

?>

<SCRIPT LANGUAGE=Javascript>

	function change_category(v) {
		cat.CURRENT_CATEGORY.value = v;
		cat.submit();
	}

	function choose_temp(v) {
		cat.WIZSTEP.value = 'step2';
		cat.TEMPLATE.value = v;
		cat.submit();
	}

</SCRIPT>

<STYLE>
	a.title:link { color: darkblue;font-size: 9pt;cursor: hand;font-weight:bold; text-decoration: none; }
	a.title:visited { color: darkblue;font-size: 9pt;cursor: hand;font-weight:bold; text-decoration: none; }
	a.title:hover { color: #336699; cursor: hand; font-size: 9pt;font-weight:bold; text-decoration: none; }
</STYLE>

		<FORM NAME=cat METHOD=POST ACTION="start.php">
		<INPUT TYPE=HIDDEN NAME=CURRENT_CATEGORY VALUE="">
		<INPUT TYPE=HIDDEN NAME=WIZSTEP VALUE="Start Wizard">
		<INPUT TYPE=HIDDEN NAME=TEMPLATE VALUE="">
		<INPUT TYPE=HIDDEN NAME=change VALUE="<? echo $change; ?>">

		<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0 WIDTH=100%>
  		<TR>
    	<TD ALIGN=CENTER VALIGN=TOP><table width="100%" border="0" cellspacing="0" cellpadding="5" align=center class=text>
        <tr>
		<td align="left" valign="middle" class="text" bgcolor="#90C8FF">
		<B><font color=black size=2>Browse Website Templates</font></B>
		</td><td align="right" valign="top" class="text" bgcolor="#90C8FF"><B>Browse:</B>

		<?
		echo '<select name="pTemplate" onchange="change_category(this.value);" style="width: 150px; font-family: Tahoma; font-size: 8pt; background: #EFEFEF;">';
		$CATONLY = "on";
		include("wiz-read_templates.php");
		echo '</select>';
		?>
		</td>
		</tr>
		<tr>
		<td colspan=2 align="center" valign="middle" class="text">

		<?

		// Is there a default category defined in isp.conf?
		if ( strlen($df_template_cat) > 2 ) {
		   $zCat = $df_template_cat;
		} else {
		   $zCat = "Neutral";
		}

		if ( $CURRENT_CATEGORY == "" ) { $CURRENT_CATEGORY = "_"; } // Pull all by default

		$directory = "../modules/site_templates/pages";	// Set Template Directory
		$handle = opendir("$directory");

		$a=0;

		echo "<table border=0 cellpadding=8 cellspacing=0 align=center width=75%>";
		$dCat = eregi_replace("_", " ", $CURRENT_CATEGORY);
		echo "<tr><td align=left valign=middle class=text colspan=2><font color=darkblue size=4>Browsing Category: <B>".ucwords($dCat)."</font></B><BR>";

		echo "<font color=#999999>Select a category to browse from the drop down box above. When your find a template you like, simply <font color=#000000>click the template image to continue to Step 2</font>.</font></td>\n";

		while ($files = readdir($handle)) {
			if (strlen($files) > 2 && $files != "default" && eregi("$CURRENT_CATEGORY", $files)) {
				$a++;
				if ($a == 1) { echo "<tr>\n"; }

				echo "<td align=center valign=top class=text><a href=\"#\" class=title onclick=\"choose_temp('$files');\"><img src=\"../modules/site_templates/pages/$files/screenshot.jpg\" WIDTH=200 HEIGHT=137 BORDER=0 ALIGN=ABSMIDDLE style=\"border: 1px solid black;\" hspace=2 vspace=2>";
				echo "<BR CLEAR=all>";

					$template_name = eregi_replace("_", " ", $files);
					$template_name = strtolower($template_name);

					$tmp = split("-", $template_name);
					$tmp_display = ucwords($tmp[1]);
					if (!eregi("none", $tmp[2])) { $tmp_display .= "  (".ucwords($tmp[2]).")"; }

					echo "$tmp_display</a><BR>&nbsp;</td>\n";

				if ($a == 2) { echo "</tr>\n"; $a=0; }
			} // End If
		} // End While
		closedir($handle);

		if ($a != 0) {
			while($a != 2) {
				$a++;
				echo "<td class=text align=left valign=top>&nbsp;</td>";
			}
			echo "</tr>";
		}

		echo "</table>\n\n";

		?>

		</td>
		</tr>
		</table></td>
		</tr>
		</table>
