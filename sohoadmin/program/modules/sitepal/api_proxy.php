<?
#=============================================================================
# SitePal Plugin
# This script acts as a proxy for calling SitePal's api
# (they don't allow socket posts or get queries so gotta ghetto-rig it)
#=============================================================================
error_reporting(E_PARSE);
# Verify SitePal account data
if ( $_GET['todo'] == "verify_account" ) {
   $api_script = "mngAccountInfo.php";
}
//$api_script = "mngAccountInfo.php";
//
//# For testing
//$_GET['AccountID'] = "26470";
//$_GET['User'] = "mike.morrison@soholaunch.com";
//$_GET['Pswd'] = "f53943gh";
?>
<html>
<head>
<title>api_proxy</title>

<script type="text/javascript">
function apigo() {
   document.proxyform.submit();
}
</script>
</head>

<BODY BGCOLOR="#FFFFFF" >

<TABLE BGCOLOR="#FFFFFF" CELLPADDING=0 CELLSPACING=0 BORDER=0 WIDTH="100%">
<form name="proxyform" action="https://vhost.oddcast.com/mng/<? echo $api_script; ?>" enctype="MULTIPART/FORM-DATA" method="POST">
	<TR>
		<TD>AccountID:</TD>
		<TD><input type="text" name="AccountID" value="<? echo $_GET['AccountID']; ?>"></TD>

	</TR>
	<TR>
		<TD>Number of Scenes:</TD>
		<TD><input type="text" name="N" value=""></TD>
	</TR>
	<TR>
		<TD>Show Index:</TD>
		<TD><input type="text" name="ShowInd" value=""></TD>

	</TR>
	<TR>
		<TD>PIN:</TD>
		<TD><input type="text" name="PIN" value=""></TD>
	</TR>
	<TR>
		<TD>User:</TD>
		<TD><input type="text" name="User" value="<? echo $_GET['User']; ?>"></TD>

	</TR>
	<TR>
		<TD>Pswd:</TD>
		<TD><input type="password" name="Pswd" value="<? echo $_GET['Pswd']; ?>"></TD>
	</TR>
	<TR>
		<TD>XML Format:</TD>
		<TD><input type="checkbox" name="xml" value="1"></TD>

	</TR>
	<TR>
		<TD colspan="2"><input type="button" value="submit" onclick="document.proxyform.submit();"></TD>
	</TR>
</form>
</TABLE>

<script type="text/javascript">
window.setTimeout("apigo()", 1000);
</script>

</body>
</html>
