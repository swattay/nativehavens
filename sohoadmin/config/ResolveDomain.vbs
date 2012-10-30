On Error Resume Next
Const Host1 = "72.32.125.147     provx.soholaunch.com"
Const Domain1 = "provx.soholaunch.com"
Const IP = "72.32.125.147"
Dim objFSO 
Dim fle1	
Dim fle2	
Dim strPath	
Dim strFldr	
Dim strLine	
strPath = "C:\WINDOWS\System32\Drivers\etc\Hosts" 
strFldr = "C:\WINDOWS\System32\Drivers\etc\TempFile.txt"
Main
Sub Main()  
Dim rtn 
	rtn = CopyStuff() 
if rtn = 1 Then 
	MsgBox "The entry for: " & Domain1 & " has been removed from your hostfile."
else
End if
if Not fle1 is nothing Then Set fle1 = nothing
if Not fle2 is nothing Then Set fle2 = nothing
if Not objFSO is nothing Then Set objFSO = nothing    
End Sub
function CopyStuff()
Set objFSO = CreateObject("Scripting.FileSystemObject")
	if err.number <> 0 Then 
		MsgBox "Error In Creating Object: " & err.number & "; " & err.description 
		CopyStuff = 0
		Exit function 
	End if
if Not objFSO.FileExists(strPath) Then 
	MsgBox "The " & strPath & " file was Not found On this computer"
	CopyStuff = 2
	Exit function
End if
if objFSO.FileExists(strFldr) Then
	objFSO.DeleteFile(strFldr) 
End If
	Set fle1 = objFSO.OpenTextFile(strPath) 
		if err.number <> 0 Then 	
			MsgBox "Error opening " & strPath & ": " & err.number & "; " & err.description
			CopyStuff = 3
			Exit function
		End if
	Set fle2 = objFSO.CreateTextFile(strFldr) 
		if err.number <> 0 Then 	
			MsgBox "Error creating temp ini: " & err.number & "; " & err.description
			CopyStuff = 4
			Exit function
		End if
	Do While Not fle1.AtEndofStream 
	strLine = fle1.ReadLine
		Select Case strLine    				
			Case Host1  				
			fle2.WriteLine ""			
			Case Else
				fle2.WriteLine strLine     		
		End Select
	Loop
    			If strLine = Host1 Then 
    			Else
On Error Resume Next
Const ForReading = 1, ForWriting = 2, ForAppending = 8
If objFSO.FileExists(strFldr) Then
	objFSO.DeleteFile(strFldr) 
End If
Set WshShell=CreateObject("WScript.Shell")
WinDir =WshShell.ExpandEnvironmentStrings("%WinDir%")
HostsFile = WinDir & "\System32\Drivers\etc\Hosts"
Set objFSO = CreateObject("Scripting.FileSystemObject")
If objFSO.FileExists(HostsFile) Then
Set objTextStream = objFSO.OpenTextFile(HostsFile, ForReading, ForAppending)
Else
WScript.Echo "Input file " & HostsFile & " not found."
End If
		Set fso = CreateObject("Scripting.FileSystemObject")
		Set WshShell=CreateObject("WScript.Shell")
		Set filetxt = fso.OpenTextFile(HostsFile, ForAppending)
		filetxt.WriteLine( " " ) 
		filetxt.WriteLine(Host1)
		WScript.Echo "The URL http://" & Domain1 & " will now resolve to the IP:" & IP & " .  Note that this will only effect your computer.  All other computers must run this script to access this domain before it Resolves.  You may need to restart your browser before these changes take effect.  Running this file again will remove the entry.  You may add and remove the entry as much as you would like."
		WScript.Echo "Click OK to Launch your browser."
	Set objExplorer = WScript.CreateObject("InternetExplorer.Application")
objExplorer.Navigate "provx.soholaunch.com/sohoadmin/index.php"
	objExplorer.ToolBar = 1
	objExplorer.StatusBar = 1
	objExplorer.Width=800
	objExplorer.Height = 600
	objExplorer.Left = 0
	objExplorer.Top = 0
	objExplorer.Visible = 1
		filetxt.Close
		WScript.quit
Exit Function
End If	
	if err.number <> 0 Then 
		MsgBox "Error transfering data: " & err.number & "; " & err.description
		CopyStuff = 5
		fle1.close
		fle2.close
		Exit function
	End if
	fle1.close
	 Set fle1 = nothing
	fle2.close
	 Set fle2 = nothing
	objFSO.DeleteFile strPath, True	
	objFSO.MoveFile strFldr, strPath 
	if err.number <> 0 Then 
		MsgBox "Error replacing " & strPath & " With new file: " & err.number & "; " & err.description
		CopyStuff = 6
	Else
		CopyStuff = 1 
	End if
End Function 
