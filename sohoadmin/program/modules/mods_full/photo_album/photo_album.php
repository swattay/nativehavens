<?php
error_reporting(E_PARSE);
if($_GET['_SESSION'] != '' || $_POST['_SESSION'] != '' || $_COOKIE['_SESSION'] != '') { exit; }


###############################################################################
## Soholaunch(R) Site Management Tool
## Version 4.5
##      
## Author: 			Mike Johnston [mike.johnston@soholaunch.com]                 
## Homepage:	 	http://www.soholaunch.com
## Bug Reports: 	http://bugzilla.soholaunch.com
## Release Notes:	sohoadmin/build.dat.php
###############################################################################

##############################################################################
## COPYRIGHT NOTICE                                                     
## Copyright 1999-2003 Soholaunch.com, Inc. and Mike Johnston 
## Copyright 2003-2007 Soholaunch.com, Inc.
## All Rights Reserved.  
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

session_start();
include("../../../includes/product_gui.php");

#######################################################
### PROCESS "DELETE ALBUM" ACTION				    ###	
#######################################################

if ($ACTION == "DEL") {
	mysql_query("DELETE FROM photo_album WHERE PRIKEY = '$id'");
}

#######################################################
### PROCESS "ADD NEW ALBUM" ACTION				    ###	
#######################################################

if ($ACTION == "NG") {

		// Check for duplicates and don't allow

		$ef = 0;
		$NEWGROUP = ucwords($NEWGROUP);		
		$NEWGROUP = stripslashes($NEWGROUP);
		$NEWGROUP = addslashes($NEWGROUP);

		$result = mysql_query("SELECT * FROM photo_album");
		$num_groups = mysql_num_rows($result); 

		if ($num_groups > 0) {
			while($GROUP = mysql_fetch_array($result)) {
				if ($GROUP[ALBUM_NAME] == $NEWGROUP) { $ef = 1; }
			}
		}

		if ($NEWGROUP != "" && $ef != 1) {			
			mysql_query("INSERT INTO photo_album VALUES('NULL', '$NEWGROUP',' ',' ',' ')");
			echo mysql_error(); 
		}

}


#######################################################
### IF THE 'photo_album' TABLE DOES NOT EXIST; 
### CREATE NOW 
#######################################################

		$match = 0;		
		$tablename = "photo_album";

		$result = mysql_list_tables("$db_name");
		$i = 0; 
		while ($i < mysql_num_rows ($result)) { 
			$tb_names[$i] = mysql_tablename ($result, $i); 
			if ($tb_names[$i] == $tablename) {
				$match = 1;
			}
			$i++;
		} 

		// if ($match == 1) { mysql_query("DROP TABLE photo_album"); }
		
		if ($match != 1) {
			
			mysql_db_query("$db_name","CREATE TABLE photo_album (
				PRIKEY INT NOT NULL AUTO_INCREMENT PRIMARY KEY, 
				ALBUM_NAME CHAR(255), IMAGE_NAMES BLOB, CAPTION BLOB, LINK BLOB)");
				
		} // End if Match != 1


#######################################################
### START HTML/JAVASCRIPT CODE					    ###	
#######################################################

# Start buffering output
ob_start();
?>



<script language="JavaScript">
<!--
function SV2_findObj(n, d) { //v3.0
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=SV2_findObj(n,d.layers[i].document); return x;
}

function SV2_popupMsg(msg) { //v1.0
  alert(msg);
}
function SV2_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}

function delete_album() {

	// What album is selected?
	var sel = DELFORM.id.value;

	if (sel != "") {
	
		var tiny = window.confirm('Are you sure you wish to delete this Album?');
		if (tiny != false) { 
			window.location = 'photo_album.php?id='+sel+'&ACTION=DEL&<?=SID?>';
		}
	
	} // End If ""

}

var p = "<?php echo lang("Photo Album"); ?>";
parent.frames.footer.setPage(p);


//-->
</script>



<style>

form {
   margin:0;
}

.feature_contain {
	padding: 0;
	margin: 0;
	border-left: 1px solid #A2ADBC;
	border-bottom: 1px solid #A2ADBC;
	font: 12px "Trebuchet MS", Verdana, Arial, Helvetica, sans-serif;
	color: #33393F;
	text-align: center;
	background-color: #fff;
}

.feature_contain th {
	font: bold 12px "Trebuchet MS", Verdana, Arial, Helvetica, sans-serif;
	background: #D9E2E1;
	border-right: 1px solid #A2ADBC;
	border-bottom: 1px solid #A2ADBC;
	border-top: 1px solid #A2ADBC;
	padding:2;
}

.feature_contain td {
   padding-bottom:7px;
}

.cal_btn {
   margin:0;
   text-align: center;
   border: 2px outset #CFCFCF;
   cursor: pointer;
   background: #A7DFAF;
}

.cal_btn_over {
   text-align: center;
   border: 2px outset #AFFFBA;
   cursor: pointer;
   background: #6FDF7E;
}

.cal_del_btn {
   margin:0;
   text-align: center;
   border: 2px outset #CFCFCF;
   cursor: pointer;
   background: #FF0000;
   color: #FFFFFF;
}

.cal_del_btn_over {
   text-align: center;
   border: 2px outset #CCCCCC;
   cursor: pointer;
   background: #FF4F4F;
   color: #FFFFFF;
}


</style>

<?
$THIS_DISPLAY = "";

	$THIS_DISPLAY .= "<form method=\"POST\" action=\"photo_album.php\">\n";
	$THIS_DISPLAY .= "<input type=\"hidden\" name=\"ACTION\" value=\"NG\">\n";

	$THIS_DISPLAY .= "<table border=\"0\" cellpadding=\"8\" cellspacing=\"0\" width=\"750\" align=\"center\" class=\"feature_contain\">\n";
   $THIS_DISPLAY .= "<tr><th align=\"left\" valign=\"top\">".lang("Create New Album")."</th></tr>\n";

	$THIS_DISPLAY .= "<tr><td align=\"left\" valign=\"top\" style=\"border-right: 1px solid #A2ADBC;\">\n";

		$THIS_DISPLAY .= "<b>".lang("Enter Album Name")."</b>:<br/><INPUT TYPE=TEXT NAME=NEWGROUP CLASS=text STYLE='width: 400px;'>&nbsp;\n";
		$THIS_DISPLAY .= "<input type=\"SUBMIT\" value=\"".lang("Create Album")."\" class=\"btn_save\" onMouseover=\"this.className='btn_saveon';\" onMouseout=\"this.className='btn_save';\"></FORM>\n";
		$THIS_DISPLAY .= "</td></tr>\n";


		// Pull any data from "sec_codes" table for display use

		$result = mysql_query("SELECT * FROM photo_album ORDER BY ALBUM_NAME");
		$num_groups = mysql_num_rows($result); 

		if ($num_groups > 0) {
		   $THIS_DISPLAY .= "<tr><td align=\"left\" valign=\"top\" style=\"border-right: 1px solid #A2ADBC;\">\n";

			$THIS_DISPLAY .= "<DIV ALIGN=LEFT><FORM NAME=DELFORM METHOD=POST ACTION=\"edit_album.php\">\n";

			$THIS_DISPLAY .= "<B>".lang("Current Photo Albums")."</B>:<br/>\n\n";
			$THIS_DISPLAY .= "<SELECT NAME=id CLASS=text STYLE='width: 325px;'>\n";
			$THIS_DISPLAY .= "     <OPTION VALUE=\"\">".lang("Select Album")."...</OPTION>\n";

			while($MIKE = mysql_fetch_array($result)) {
				$THIS_DISPLAY .= "     <OPTION VALUE=\"$MIKE[PRIKEY]\">$MIKE[ALBUM_NAME]</OPTION>\n";
			}

			$THIS_DISPLAY .= "\n</SELECT>&nbsp;\n";
			$THIS_DISPLAY .= "<INPUT TYPE=SUBMIT VALUE=\" ".lang("Edit")." \" style='width: 75px;' CLASS=\"btn_edit\" onMouseover=\"this.className='btn_editon';\" onMouseout=\"this.className='btn_edit';\">\n";
			$THIS_DISPLAY .= "&nbsp;&nbsp;&nbsp;&nbsp;\n";
			$THIS_DISPLAY .= "<INPUT TYPE=BUTTON VALUE=\" ".lang("Delete")." \"  style='width: 75px;'  ONCLICK=\"delete_album();\" CLASS=\"btn_delete\" onMouseover=\"this.className='btn_deleteon';\" onMouseout=\"this.className='btn_delete';\">\n";
			
			$THIS_DISPLAY .= "</FORM></DIV>\n";

         $THIS_DISPLAY .= "</td></tr>\n";

		}

	$THIS_DISPLAY .= "</TABLE><BR><BR>\n";



$THIS_DISPLAY .= "  </td>\n";
$THIS_DISPLAY .= " </tr>\n";
$THIS_DISPLAY .= "</table>\n";


echo $THIS_DISPLAY;

# Grab module html into container var
$module_html = ob_get_contents();
ob_end_clean();

$instructions = lang("Create and manage your site photo albums here.  After you create the album, select it from the current photo albums list and click edit.");
//$instructions .= lang("Please only use alpha-numerical characters and spaces.");

# Build into standard module template
$module = new smt_module($module_html);
$module->meta_title = lang("Photo Album");
$module->add_breadcrumb_link(lang("Photo Album"), "program/modules/mods_full/photo_album/photo_album.php");
$module->icon_img = "skins/".$_SESSION['skin']."/icons/full_size/photo_albums-enabled.gif";
$module->heading_text = lang("Photo Album");
$module->description_text = $instructions;
$module->good_to_go();
?>