<?php
error_reporting(E_PARSE);
if($_GET['_SESSION'] != '' || $_POST['_SESSION'] != '' || $_COOKIE['_SESSION'] != '') { exit; }

session_start();
error_reporting(E_PARSE);

$demo_path = stripslashes($_GET['flash_demo_path']);
//echo "(".$demo_path.")<br/>";
//echo "(".$_SESSION['this_ip'].")<br/>";
//foreach($_SESSION as $var=>$val){
//   echo "var = (".$var.") val = (".$val.")<br>";
//}

function url_exists($url)
{
 $handle = @fopen($url, "r");
 if ($handle === false)
  return false;
 fclose($handle);
 return true;
}

if(eregi($_SESSION['this_ip'], $demo_path)){
	header("location: ../../../filebin/".$_GET['swffile'].".htm");
}else{
   if(url_exists("http://".$demo_path."/".$_GET['swffile'].".htm")){
   	$demo_path_final = eregi_replace("\\","/",$demo_path);
   	header("location: http://".$demo_path."/".$_GET['swffile'].".htm");
   }elseif(url_exists("http://".$demo_path."/".$_GET['swffile'].".html")){
   	$demo_path_final = eregi_replace("\\","/",$demo_path);
   	header("location: http://".$demo_path."/".$_GET['swffile'].".html");
   }else{
      echo "<h1>Were Sorry!</h1>, cant find file (http://".$demo_path."/".$_GET['swffile'].".html). Please contact your host to find out more about this error.";
   }
}
?>