<?php
session_start();
error_reporting(E_PARSE);
if($_GET['_SESSION'] != '' || $_POST['_SESSION'] != '') { exit; }

include_once("../../includes/product_gui.php");

$webmaster_pref = new userdata("webmaster_pref");

$ftp_server = $_SESSION['this_ip'];
$ftp_user_name = $webmaster_pref->get("ftp_username");
$ftp_user_pass = $webmaster_pref->get("ftp_password");


if (!function_exists('ftp_connect')) {
	   return false;
	} else {

	$conn_id = ftp_connect($ftp_server);	   
	$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);	   
	// set up basic connection 
	if ((!$conn_id) || (!$login_result)) {
		echo "FTP connection has failed!";
		echo "Attempted to connect to $ftp_server for user $ftp_user_name";
	} else {
		$ftpdisp = "FTP connection successful!";
		if(!function_exists('ftp_chmod')) {
		    function ftp_chmod($ftp_stream, $themode, $filename)
		    {
		        return ftp_site($ftp_stream, sprintf('CHMOD %o %s', $themode, $filename));
		    }
		}	

		$_SESSION['filearray'] = '';
		$_SESSION['dirarrayz']='';
		function chmod_list_R($path, $orig_docroot){
			foreach (glob($path) as $filename) {		
				if(!is_dir($filename)) {
					if ( !eregi('\.htaccess', $filename)) {
						$_SESSION['filearray'][] = eregi_replace($orig_docroot.'/', '', $filename);
					}
				} else {
					if(eregi('sohoadmin', $filename) || eregi('media', $filename) || eregi('images', $filename) || eregi('shopping', $filename) || eregi('tCustom', $filename) || eregi('template', $filename) || eregi('import', $filename) || eregi('subscription', $filename)) {
						$_SESSION['dirarrayz'][] = eregi_replace($orig_docroot.'/', '', $filename);
						chmod_list_R($filename.'/*', $orig_docroot);
					}
				}
			}
		}	
		$odir = getcwd();
		chdir($_SESSION['doc_root']);
		chmod_list_R($_SESSION['doc_root'].'/*', $_SESSION['doc_root']);
		chdir($odir);
		natcasesort($_SESSION['dirarrayz']);
		natcasesort($_SESSION['filearray']);
		$curdir = $_SESSION['doc_root'];
		$dirarray = preg_split('/(\\\|\/)/', $_SESSION['doc_root'], -1, PREG_SPLIT_NO_EMPTY);
	   $php_suexec = strtoupper(php_sapi_name());
	   if(!eregi("CGI",$php_suexec)){
	   	$whosami = eregi_replace("\n", '', exec('whoami'));
	   	if($whosami != 'nobody' && $whosami != 'apache' && $whosami != ''){
	   		$php_suexec = 'CGI';
	   	}
		}
	   if(eregi("CGI",$php_suexec)){
	      $mode = 0755;
	      $mode2 = "0755";
	   }  else {
	      $mode = 0777;
	      $mode2 = "0777";
	   }
	   if(!ftp_chdir($conn_id, $curdir)) {
	      $ftpcwd = ftp_pwd($conn_id);
	      $lsarray = ftp_rawlist($conn_id, $ftpcwd);
	      $cccount = count($dirarray);
	      $zc = 0;
	      while($zc < $cccount) {
	         ftp_chdir($conn_id, $dirarray[$zc]);
	        // echo "Current dir is: [".ftp_pwd($conn_id)."]<br/>";
	         $lastfolder = $dirarray[$zc];
	         $zc++;
	      }
	   }
	   $ftpcwd2 = ftp_pwd($conn_id);
	   ftp_chdir($conn_id, '..');
	   ftp_chmod($conn_id, $mode, $ftpcwd2);
	   ftp_chdir($conn_id, $lastfolder);
	   $ftpcwd = ftp_pwd($conn_id);
	   $goodtogo = 0;
	
	   foreach(ftp_nlist($conn_id, '') as $filenames) {
	      if($filenames == 'sohoadmin') {
	         $goodtogo = 1;
	      }
	   }
	   if($goodtogo == 1) {
	      chdir($_SESSION['doc_root']);    
	      if(eregi("CGI",$php_suexec)){
	         foreach($_SESSION['dirarrayz'] as $dirnamz){
	            if(ftp_chmod($conn_id, 0755, $dirnamz) !== false) {
	               $chmodeddirs[] = $dirnamz;
	            } else {
	               exec("chmod 0755 ".$dirnamz);
	              // echo "2 $filename chmoded successfully to ".$pmode2." <br/>\n";
	            }
	         }
	         foreach($_SESSION['filearray'] as $filenamez){
	            if(ftp_chmod($conn_id, 0755, $filenamez) !== false) {
	               $chmodedfiless[] = $filenamez;
	            } else {
	               exec("chmod 0755 ".$filenamez);
	              // echo "2 $filename chmoded successfully to ".$pmode2." <br/>\n";
	            }
	         }
	      } else {
	         foreach($_SESSION['dirarrayz'] as $dirnamz){
	            if(ftp_chmod($conn_id, 0777, $dirnamz) !== false) {
	               $chmodeddirs[] = $dirnamz;
	            } else {
	               exec("chmod 0777 ".$dirnamz);
	              // echo "2 $filename chmoded successfully to ".$pmode2." <br/>\n";
	            }
	
	         }  
	         foreach($_SESSION['filearray'] as $filenamez){
	            if(ftp_chmod($conn_id, 0777, $filenamez) !== false) {
	               $chmodedfiless[] = $filenamez;
	            } else {
	               exec("chmod 0777 ".$filenamez);
	              // echo "2 $filename chmoded successfully to ".$pmode2." <br/>\n";
	            }
	         }
	      }
	   }
	   ftp_close($conn_id);
	   chdir($curdir);
	}
	
	$_SESSION['dirarray'] = '';
	$_SESSION['filearray'] = '';

}