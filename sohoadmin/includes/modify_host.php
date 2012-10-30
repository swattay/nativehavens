<?php
error_reporting(E_PARSE);
if($_GET['_SESSION'] != '' || $_POST['_SESSION'] != '' || $_COOKIE['_SESSION'] != '') { exit; }

error_reporting(E_PARSE);
$docroot = str_replace(basename(__FILE__), "", __FILE__);

$docfolder = eregi_replace("sohoadmin/includes/", '', $docroot);

$DOMAIN = $_SESSION['this_ip'];
$IP = $_SERVER['SERVER_ADDR'];
if ( $IP == "" ) {
	$IP = $_SERVER['LOCAL_ADDR'];
}
$HOST = $IP."     ".$DOMAIN;
$host = php_uname(n);
$hostip = gethostbyname($host);
$TIP = gethostbyname($_SERVER['HTTP_HOST']);
$addr = gethostbyaddr($TIP);
$gethost = gethostbyname($addr);
$HostIPs = gethostbyname($_SERVER['HTTP_HOST']);
//include('D:\WEBSITES_SOHO\positivecashflowrentals\sohoadmin\program\includes\shared_functions.php');
//echo testArray($_SERVER);


if ( $IP == $HostIPs) {
	$resolved = "yes";
} else {
	if ( $gethost == $hostip ) {
		$resolved = "yes";
	} else {
		$resolved = "no";
	}

}



$VBS = "On Error Resume Next"."\n";
$VBS .= "Const Host1 = \"".$HOST."\""."\n";
$VBS .= "Const Domain1 = \"".$DOMAIN."\"\n";
$VBS .= "Const IP = \"".$IP."\"\n";
$VBS .= "Dim objFSO \n";
$VBS .= "Dim fle1	\n";
$VBS .= "Dim fle2	\n";
$VBS .= "Dim strPath	\n";
$VBS .= "Dim strFldr	\n";
$VBS .= "Dim strLine	\n";
$VBS .= "strPath = \"C:\WINDOWS\System32\Drivers\etc\Hosts\" \n";
$VBS .= "strFldr = \"C:\WINDOWS\System32\Drivers\etc\TempFile.txt\"\n";
$VBS .= "Main\n";
$VBS .= "Sub Main()  \n";
$VBS .= "Dim rtn \n";
$VBS .= "	rtn = CopyStuff() \n";
$VBS .= "if rtn = 1 Then \n";
$VBS .= "	MsgBox \"The entry for: \" & Domain1 & \" has been removed from your hostfile.\"\n";
$VBS .= "else\n";
$VBS .= "End if\n";
$VBS .= "if Not fle1 is nothing Then Set fle1 = nothing\n";
$VBS .= "if Not fle2 is nothing Then Set fle2 = nothing\n";
$VBS .= "if Not objFSO is nothing Then Set objFSO = nothing    \n";
$VBS .= "End Sub\n";
$VBS .= "function CopyStuff()\n";
$VBS .= "Set objFSO = CreateObject(\"Scripting.FileSystemObject\")\n";
$VBS .= "	if err.number <> 0 Then \n";
$VBS .= "		MsgBox \"Error In Creating Object: \" & err.number & \"; \" & err.description \n";
$VBS .= "		CopyStuff = 0\n";
$VBS .= "		Exit function \n";
$VBS .= "	End if\n";
$VBS .= "if Not objFSO.FileExists(strPath) Then \n";
$VBS .= "	MsgBox \"The \" & strPath & \" file was Not found On this computer\"\n";
$VBS .= "	CopyStuff = 2\n";
$VBS .= "	Exit function\n";
$VBS .= "End if\n";
$VBS .= "if objFSO.FileExists(strFldr) Then\n";
$VBS .= "	objFSO.DeleteFile(strFldr) \n";
$VBS .= "End If\n";
$VBS .= "	Set fle1 = objFSO.OpenTextFile(strPath) \n";
$VBS .= "		if err.number <> 0 Then 	\n";
$VBS .= "			MsgBox \"Error opening \" & strPath & \": \" & err.number & \"; \" & err.description\n";
$VBS .= "			CopyStuff = 3\n";
$VBS .= "			Exit function\n";
$VBS .= "		End if\n";
$VBS .= "	Set fle2 = objFSO.CreateTextFile(strFldr) \n";
$VBS .= "		if err.number <> 0 Then 	\n";
$VBS .= "			MsgBox \"Error creating temp ini: \" & err.number & \"; \" & err.description\n";
$VBS .= "			CopyStuff = 4\n";
$VBS .= "			Exit function\n";
$VBS .= "		End if\n";
$VBS .= "	Do While Not fle1.AtEndofStream \n";
$VBS .= "	strLine = fle1.ReadLine\n";
$VBS .= "		Select Case strLine    				\n";
$VBS .= "			Case Host1  				\n";
$VBS .= "			fle2.WriteLine \"\"			\n";
$VBS .= "			Case Else\n";
$VBS .= "				fle2.WriteLine strLine     		\n";
$VBS .= "		End Select\n";
$VBS .= "	Loop\n";
$VBS .= "    			If strLine = Host1 Then \n";
$VBS .= "    			Else\n";
$VBS .= "On Error Resume Next\n";
$VBS .= "Const ForReading = 1, ForWriting = 2, ForAppending = 8\n";
$VBS .= "If objFSO.FileExists(strFldr) Then\n";
$VBS .= "	objFSO.DeleteFile(strFldr) \n";
$VBS .= "End If\n";
$VBS .= "Set WshShell=CreateObject(\"WScript.Shell\")\n";
$VBS .= "WinDir =WshShell.ExpandEnvironmentStrings(\"%WinDir%\")\n";
$VBS .= "HostsFile = WinDir & \"\System32\Drivers\etc\Hosts\"\n";
$VBS .= "Set objFSO = CreateObject(\"Scripting.FileSystemObject\")\n";
$VBS .= "If objFSO.FileExists(HostsFile) Then\n";
$VBS .= "Set objTextStream = objFSO.OpenTextFile(HostsFile, ForReading, ForAppending)\n";
$VBS .= "Else\n";
$VBS .= "WScript.Echo \"Input file \" & HostsFile & \" not found.\"\n";
$VBS .= "End If\n";
$VBS .= "		Set fso = CreateObject(\"Scripting.FileSystemObject\")\n";
$VBS .= "		Set WshShell=CreateObject(\"WScript.Shell\")\n";
$VBS .= "		Set filetxt = fso.OpenTextFile(HostsFile, ForAppending)\n";
$VBS .= "		filetxt.WriteLine( \" \" ) \n";
$VBS .= "		filetxt.WriteLine(Host1)\n";
$VBS .= "		WScript.Echo \"The URL http://\" & Domain1 & \" will now resolve to the IP:\" & IP & \" .  Note that this will only effect your computer.  All other computers must run this script to access this domain before it Resolves.  You may need to restart your browser before these changes take effect.  Running this file again will remove the entry.  You may add and remove the entry as much as you would like.\"\n";
$VBS .= "		WScript.Echo \"Click OK to Launch your browser.\"\n";
$VBS .= "	Set objExplorer = WScript.CreateObject(\"InternetExplorer.Application\")\n";
$VBS .= "objExplorer.Navigate \"".$DOMAIN."/sohoadmin/index.php\"\n";  
$VBS .= "	objExplorer.ToolBar = 1\n";
$VBS .= "	objExplorer.StatusBar = 1\n";
$VBS .= "	objExplorer.Width=800\n";
$VBS .= "	objExplorer.Height = 600\n";
$VBS .= "	objExplorer.Left = 0\n";
$VBS .= "	objExplorer.Top = 0\n";
$VBS .= "	objExplorer.Visible = 1\n";
$VBS .= "		filetxt.Close\n";
$VBS .= "		WScript.quit\n";
$VBS .= "Exit Function\n";
$VBS .= "End If	\n";
$VBS .= "	if err.number <> 0 Then \n";
$VBS .= "		MsgBox \"Error transfering data: \" & err.number & \"; \" & err.description\n";
$VBS .= "		CopyStuff = 5\n";
$VBS .= "		fle1.close\n";
$VBS .= "		fle2.close\n";
$VBS .= "		Exit function\n";
$VBS .= "	End if\n";
$VBS .= "	fle1.close\n";
$VBS .= "	 Set fle1 = nothing\n";
$VBS .= "	fle2.close\n";
$VBS .= "	 Set fle2 = nothing\n";
$VBS .= "	objFSO.DeleteFile strPath, True	\n";
$VBS .= "	objFSO.MoveFile strFldr, strPath \n";
$VBS .= "	if err.number <> 0 Then \n";
$VBS .= "		MsgBox \"Error replacing \" & strPath & \" With new file: \" & err.number & \"; \" & err.description\n";
$VBS .= "		CopyStuff = 6\n";
$VBS .= "	Else\n";
$VBS .= "		CopyStuff = 1 \n";
$VBS .= "	End if\n";
$VBS .= "End Function \n";


$uri  = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
$extra = $_SERVER['HTTP_HOST'].$uri;
$VBSfilepath = $_SESSION['docroot_path']."/sohoadmin/config/ResolveDomain.vbs";
$VBSfile = fopen($VBSfilepath, "w");
	if ( !fwrite($VBSfile, "$VBS") ) { 
		//echo "<div style=\"z-index: 7; position: absolute; top: 25%; width: 300px; height: 180px; visibility: visible;\">";
		echo "<BODY BGCOLOR=\"#EFEFEF\" TEXT=\"#FFFFFF\" LINK=\"blue\" VLINK=\"blue\" ALINK=\"blue\" LEFTMARGIN=\"0\" TOPMARGIN=\"0\" MARGINWIDTH=\"0\" MARGINHEIGHT=\"0\">";
		echo "<TABLE WIDTH=\"525\" BORDER=\"1\" CELLSPACING=\"0\" ellpadding=\"7\" ALIGN=\"CENTER\">";
    echo "<tr>";
    echo "<td bgcolor=white valign=\"top\" align=left style=\"padding:5;\"><Font color=black size=2px>&nbsp;&nbsp;<font color=red><strong>Error:</strong> Can't write file to /sohoadmin/config/ folder!</font>&nbsp;Change the permissions on the <strong><font color=\"blue\">".$_SESSION['docroot_path']."/sohoadmin/config/</strong></font> folder so that php has write access.  You may need to contact your host inorder to do this.";
		echo "</td></tr></table>"; 
	//	echo "</div>";
		exit;
	} else {
		fclose($VBSfile);
		$writable = "yes";
	}

if (eregi("www\.", $DOMAIN)) {
	$DOMAIN3 = eregi_replace("^www\.", '', $DOMAIN);
	$DOMAIN2 = "- use the URL: <font color=blue>http://</font><font color=red><strong>www</strong></font><font color=blue>.".$DOMAIN3."</font></strong> <font color=red>(<strong> with www </strong>)</font> to access <font color=blue>this site</font>.<br>- use the URL: <font color=blue>http://".$DOMAIN3."</font><font color=red> ( <strong>no www</strong>) </font> to access the <font color=red>live site</font>.";
} ELSE {
	$DOMAIN2 = "- use the URL: <font color=blue>http://".$DOMAIN."</font><font color=red> ( no <strong>www</strong> ) </font> to access <font color=blue>this site</font>.<br>- use the URL: <font color=blue>http://</font><font color=red><strong>www</strong></font><font color=blue>.".$DOMAIN."</strong><font color=red> ( with <strong>www</strong> ) </font></font></strong> to access the <font color=red>live site</font>.";
}
$DOMAIN = eregi_replace("^www\.", '', $DOMAIN);



if ( $_POST['jubjub'] == "DownloadVBS" ) {

   //$filePathvbs = $docroot."ResolveDomain.vbs";
   $fileSize = filesize($VBSfilepath);
	   if ( strstr($HTTP_USER_AGENT, "MSIE") ) {
	      $attachment = "";
	   } else {
	      $attachment = "attachment;";
	   }
   
 
	header("Content-Description: File Download");
	header("Content-Length: $fileSize");
	header("Content-Type: application/force-download");
	header("Content-Disposition: $attachment; filename=\"resolvedomain.vbs\"");
	echo file_get_contents("$VBSfilepath"); exit;
	}

if ($resolved == "no") { $INTRO =  "<span style=\"font-family: Arial; font-size: 8pt; color: red; text-decoration: blink;\">Warning: </span>It appears that <font color=blue> ".$DOMAIN."</font> is not yet live on the internet or is pointing to a different server.  Additional steps must be taken before you can login to your website builder.";
} Else { $INTRO =  "It appears that <font color=blue>".$DOMAIN."</font><font color=#01C700> is resolving</font> properly to this server.  If you have already run the script below on your computer, you should download and run the script again to remove the dns entry because it is no longer necessary.  If you are experiencing \"page not found\" problems when logging in, you may need to run this script, otherwise you should not run this script.<br><br>";
	 }

?>

<BODY BGCOLOR="#EFEFEF" TEXT="#FFFFFF" LINK="blue" VLINK="blue" ALINK="blue" LEFTMARGIN="0" TOPMARGIN="0" MARGINWIDTH="0" MARGINHEIGHT="0">
<form name="backtologin" method=post action="index.php"><input type=HIDDEN name="resolve" value="login">
<TABLE WIDTH="525" HEIGHT="238" BORDER="1" CELLSPACING="0" CELLPADDING="0" ALIGN="CENTER">
	<tr>
  <td class=txt bgcolor="#8caae7" HEIGHT=25><font color="#FFFFFF" face=Verdana SIZE=2PX><b>Resolve <? echo $DOMAIN; ?></b></font></td>
</tr>
<tr align="center" valign="top">
  <td>
    <table align="center" border="0" cellspacing="0" cellpadding="7" class=txt>
      <tr>
    <td bgcolor=white valign="top" align=left><Font color=black size=2px>&nbsp;&nbsp;<? echo $INTRO; ?>&nbsp;&nbsp;To use this sitebuilder before this domain name resolves, download and run <font color=#FF8B00>resolvedomain.vbs</font>.  <font color=#FF8B00>resolvedomain.vbs</font> is a script that adds a DNS record to your local computer so that <font color=blue><? echo $DOMAIN; ?></font> resolves to <font color=red><? echo $IP; ?></font>, which is this server's I.P. address.
    	<br><br>&nbsp;&nbsp;After running the script you will be able to access <font color=blue>this site's</font> website builder.
        	  If <font color=blue><? echo $DOMAIN; ?></font> is a live site on a different server, you will be able to access both the <font color=red>live site</font> and <font color=blue>this site</font>. 
        	 <br><? echo $DOMAIN2; ?><br>
    			<br>&nbsp;&nbsp;<strong><font color=#01C700>To Use This Script:</font></strong><br><strong><font color=#01C700>1)</font></strong> Click the link below to download <font color=#FF8B00>resolvedomain.vbs</font>. <br><strong><font color=#01C700>2)</font></strong> Save the file anywhere on your computer. <br><strong><font color=#01C700>3)</font></strong> Double-click <font color=#FF8B00>resolvedomain.vbs</font> to run the file.  
          <br><br><strong><font color=red>To Remove Entry After Domain Resolves:</font><br><font color=red>1)</font></strong> Click the link below to download <font color=#FF8B00>resolvedomain.vbs</font>. <br><strong><font color=red>2)</font></strong> Save the file anywhere on your computer. <br><strong><font color=red>3)</font></strong> Running <font color=#FF8B00>resolvedomain.vbs</font> a second time, removes the added dns entry.<br><br>
          You can add and remove this entry as many times as you would like. After running <font color=#FF8B00>resolvedomain.vbs</font> you may need to close & re-open your Internet browser before the changes take affect.</font>
          </FORM><form name="VBSadd" method=post action="<? echo $_SERVER['PHP_SELF']; ?>">
					<input type=HIDDEN name="resolve" value="domain">
          <input type=HIDDEN name="jubjub" value="DownloadVBS">
          <br><strong><font size=2px><span style="text-decoration: blink;"><a href=javascript:document.VBSadd.submit();>CLICK HERE </span>TO DOWNLOAD RESOLVEDOMAIN.VBS</a></strong></font><br><font size=2 color=red> *<font color=black>Note that this script only affects the dns on your computer. You will need to run this script on each computer that accesses the website builder until the DNS resolves to this server.  Your computer must be using Windows to run this script.<br><br><font color=blue size=2px><code ><< </code><a href=javascript:document.backtologin.submit();>Click Here to return to Login Screen</a></font></font>
        </td>
      </tr>
    </table></form>
  </td>
</tr>
</table>

