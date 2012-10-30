<?php
error_reporting(E_PARSE);
if($_GET['_SESSION'] != '' || $_POST['_SESSION'] != '' || $_COOKIE['_SESSION'] != '') { exit; }

#=====================================================================================
# Soholaunch(R) Site Management Tool
#
# Author:        Mike Morrison
# Homepage:      http://www.soholaunch.com
# Release Notes: http://wiki.soholaunch.com
#
# This Script: Simple example module to illustrate how to create a new
# module and keep it's look consistent with the rest of the product
#=====================================================================================

error_reporting(E_PARSE);
session_start();

# Include core files
include_once($_SESSION['docroot_path']."/sohoadmin/program/includes/product_gui.php");
include_once($_SESSION['docroot_path']."/sohoadmin/program/includes/smt_module.class.php");

# So you can write straight HTML without having to build every line into a container var (i.e. $disHTML .= "another line of html")
ob_start();

?>


<!---Module html goes here-->
<script type

<div>
 <h1 onclick="ajaxDo(>


<?
# Grab module html into container var
$module_html = ob_get_contents();
ob_end_clean();

$module = new smt_module($module_html);
$module->add_breadcrumb_link("Create New Pages", "program/modules/create_pages.php");
$module->icon_img = "skins/".$_SESSION['skin']."/icons/full_size/create_pages-enabled.gif";
$module->heading_text = "Create New Pages";
$module->description_text = "You may create up to 10 new pages at a time. Please only use Alpha Numerical characters and Underscores.";
$module->good_to_go();
?>