<html>
<head>
<title>#META_TITLE#</title>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="-1">

<link rel="stylesheet" href="http://<? echo $_SESSION['docroot_url']; ?>/sohoadmin/program/product_gui.css">
<script type="text/javascript" src="http://<? echo $_SESSION['docroot_url']; ?>/sohoadmin/program/includes/display_elements/js_functions.php"></script>

<link rel="stylesheet" type="text/css" href="http://<? echo $_SESSION['docroot_url']; ?>/sohoadmin/program/smt_module.css">

<!---#add_cssfile#-->
</head>

<body id="#bodyid#" bgcolor="white" text="black" link="darkblue" vlink="darkblue" alink="darkblue" topmargin="0" marginwidth="0" marginheight="0" onLoad="show_hide_layer('Layer1','','hide','userOpsLayer','','show');">

<!-- ============================================================ -->
<!-- ============= LOAD MODULE DISPLAY LAYER ==================== -->
<!-- ============================================================ -->

<div id="Layer1" style="position:absolute; left:0px; top:40%; width:100%; height:110px; z-index:100; border: 2px none #000000; visibility: visible; overflow: hidden">

  <table border=0 cellpadding=0 width=100% height=100% bgcolor=WHITE>
    <tr>
      <td align=center valign=middle class=text>Loading...<br/>
		<img src="http://<? echo $_SESSION['docroot_url']; ?>/sohoadmin/icons/ajax-loader2.gif" width=60 height=30 border=0>
      </td>
    </tr>
  </table>

</div>

<div id="userOpsLayer" style="position:absolute; visibility:hidden; left:0px; top:0; width:100%; height:100%;z-index:; overflow: auto; border: 1px none #000;padding: 0;">

<!---Module heading--->
<table border="0" align="center" cellpadding="10" cellspacing="0" class="feature_sub" style="#module_table_css#">
 <tr>
  <td valign="top" class="nopad" style="padding: 0;">
   <table width="100%" border="0" cellspacing="0" cellpadding="0" class="feature_module_heading" style="margin: 0;border: 1px none #000;">
    <tr>
     <th colspan="2" class="fgroup_title">
      <a href="http://<? echo $_SESSION['docroot_url']; ?>/sohoadmin/program/main_menu.php">Main Menu</a> &gt;
      #BREADCRUMB_LINKS#
     </th>
     <th class="fgroup_title right" style="padding-right: 15px;">
      &nbsp;
     </th>
    </tr>

    <tr>
     <!---Module icon--->
     <td align="center">
      <img src="http://<? echo $_SESSION['docroot_url']; ?>/sohoadmin/#ICON_IMG#" border="0">
     </td>

     <!---Module title and description--->
     <td width="100%" valign="top">
      <h1>#HEADING_TEXT#</h1>
      <p id="module-description_text">#DESCRIPTION_TEXT#</p></td>

     <!---spacer-->
     <td class="nopad">&nbsp;</td>
    </tr>
   </table>
  </td>
 </tr>

 <tr>
  <td valign="top" style="padding: 0;">
   <!---Report messages-->
   <div id="report_messages" style="display: #REPORT_DISPLAY#;cursor: pointer;" onclick="hideid('report_messages');">
    <!--- <div style="position: absolute;right: 25px;top: 0px;cursor: pointer;color: red;margin: 0;" onclick="hideid('report_messages');">[x]</div> -->
    #REPORT_MESSAGES#
   </div>

   <div style="#container_css#vertical-align: top;">
   #MODULE_HTML#
   </div></td>
 </tr>

</table>
</div>

</body>
</html>