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
## Copyright 1999-2006 Soholaunch.com, Inc. and Mike Johnston  
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

?>

<SCRIPT language=javascript>
	function savenow() {
		parent.frames.header.savePage('page_editor.php');
	}
</SCRIPT>

<TABLE CELLPADDING="0" CELLSPACING="0" BORDER="0" WIDTH="350" ALIGN="center" BGCOLOR="#6699CC">
  <TR> 
    <TD BGCOLOR="#FFFFFF" HEIGHT="1"><IMG SRC="spacer.gif" WIDTH="1" HEIGHT="1"></TD>
  </TR>
  <TR> 
    <TD VALIGN="top"> 
	 <TABLE CELLPADDING="5" CELLSPACING="0" BORDER="0" WIDTH="100%">
	   <TR> 
		<TD NOWRAP BGCOLOR="#336699" VALIGN=TOP WIDTH=100%><FONT FACE="Verdana, Arial, Helvetica, sans-serif" SIZE="2" COLOR="#FFFFFF"><B><IMG SRC="arrow.gif" WIDTH="17" HEIGHT="13" ALIGN="absmiddle">Save Current Page As...</B></FONT><FONT COLOR=WHITE>&nbsp;</FONT></TD>
	   </TR>
	 </TABLE>
    </TD>
  </TR>
  <TR> 
    <TD VALIGN="top" ALIGN="center"> 
	 <TABLE WIDTH="100%" BORDER="0" CELLSPACING="1" CELLPADDING="0" HEIGHT="100%">
	   <TR> 
		<TD ALIGN="LEFT" BGCOLOR="#EFEFEF" class=catselect>
		  <TABLE WIDTH="400" BORDER="0" CELLSPACING="0" CELLPADDING="5">
		    <TR> 
			 <TD ALIGN="LEFT" VALIGN="TOP" class=catselect>Enter New Page Name:<BR><INPUT TYPE="text" class=dropdownbox NAME="SAVEAS_name" SIZE="22" MAXLENGTH="22" VALUE="">
			   <INPUT TYPE="button" VALUE="Save Now" CLASS="submitBtnOff" onMouseOver="this.className='submitBtnOn';" onMouseOut="this.className='submitBtnOff';" onclick="MM_showHideLayers('saveaslayer','','hide'); savenow();">
			   <INPUT TYPE="button" VALUE="Cancel" CLASS="submitBtnOff" onMouseOver="this.className='submitBtnOn';" onMouseOut="this.className='submitBtnOff';" onclick="MM_showHideLayers('saveaslayer','','hide'); document.save.style.display = 'none';">
			 </TD>
		    </TR>
		  </TABLE>
		</TD>
	   </TR>
	 </TABLE>
    </TD>
</TABLE>
