<?php
error_reporting(E_PARSE);
if($_GET['_SESSION'] != '' || $_POST['_SESSION'] != '' || $_COOKIE['_SESSION'] != '') { exit; }


############################################################################################
## Soholaunch(R) Site Management Tool
## Version 4.9
##
## Author: 			Mike Johnston & Mike Morrison & Joe Lain
## Homepage:	 	http://www.soholaunch.com
## Bug Reports: 	http://bugz.soholaunch.com
############################################################################################

############################################################################################
## COPYRIGHT NOTICE
## Copyright 1999-2005 Soholaunch.com, Inc. and Mike Johnston.  All Rights Reserved.
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
#############################################################################################

# Maintis #250
if ( !function_exists("lang") ) {
   include("sohoadmin/program/includes/shared_functions.php");
}

if(!function_exists('db_string_format')){
	function db_string_format($string) {
   	if ( !get_magic_quotes_gpc() ) {
      	return mysql_real_escape_string($string);
   	} else {
      	return $string;
   	}
	}
}

if(!function_exists('myCheckDNSRR')){
	function myCheckDNSRR($hostName, $recType = ''){
		if(!empty($hostName)) {
			if( $recType == '' ) $recType = "MX";
			// Fix added by cameron 08/01/08
				/////exec("nslookup -type=$recType $hostName", $result);
				//      // check each line to find the one that starts with the host
				//      // name. If it exists then the function succeeded.
				//      foreach ($result as $line) {
				//        if(eregi("^$hostName",$line)) {
				//          return true;
				//        }
				//      }
				//      // otherwise there was no mail handler for the domain
				//      return false;			
			getmxrr($hostName, $mx_arr);			
			if(count($mx_arr) > 0){
				return true;
			} else {
				if(gethostbyname($hostName) != $hostName){
					return true;
				} else {
					return false;
				}
			}
		}
	}
}


# Clean all non alphanumeric characters and strip extra space
if(!function_exists('cleanString')){
   function cleanString($wild) {
      $wild = ereg_replace("[^[:alnum:]+]"," ",$wild);
      return ereg_replace("^[ \t\r\n]+|[ \t\r\n]+$","",$wild);
   }
}

# Pull blog styles from db
$blog_style_settings = new userdata("blog_styles");

if(!$blog_style_settings->get("styles")){
   # No saved styles... Include blog styles
   echo "<link href=\"pgm-blog_styles.php\" rel=\"stylesheet\" type=\"text/css\"></link>\n";
}else{
   # Spit out saved styles
   echo "<style>\n";
   echo stripslashes(html_entity_decode($blog_style_settings->get("styles"), ENT_QUOTES));
   echo "</style>\n";
}

# Include captcha js
echo "<script type=\"text/javascript\" src=\"sohoadmin/client_files/captcha/captcha.js\"></script>\n";


# Pull blog comment settings
$blog_comment_settings = new userdata("blog_comment");

if(!$blog_comment_settings->get("allow_comments") || $blog_comment_settings->get("allow_comments") == "no"){
   $is_allowed = "no";
}else{
   $is_allowed = "yes";
}

if( !$blog_comment_settings->get("require_approval") ){
   $blog_comment_settings->set("require_approval", "yes");
}

# Process blog comment
if($_REQUEST['process'] == "blog_comment"){


	if($blog_comment_settings->get("captcha") == "yes"){
	
	$bkey = $_REQUEST['blog_key'];
	$ccapval = $_REQUEST['capval_'.$bkey];
	$ccapans = $_REQUEST['cap_'.$bkey];
	
	
	$form_verificationk = '';
	$form_verificationk = $_SESSION['form_verification'][$bkey];
	unset($_SESSION['form_verification']);
	if($form_verificationk != md5(strtoupper($ccapans)) || $form_verificationk == '') {
	  header("Location: http://".$_SESSION['this_ip']."/".pagename($_POST['pr']));
	  echo "<script type=\"text/javascript\"> \n";
	  Echo "document.location='http://".$_SESSION['this_ip']."/".pagename($_POST['pr'])."'; \n";
	  Echo "</script> \n";
	  exit;
	}
	
	if($ccapval != '' && $ccapans != '' && $ccapval == md5(strtoupper($ccapans)) && $form_verificationk == md5(strtoupper($ccapans))) {
	  $_SESSION['form_verification'] = $form_verificationk;     
	} else {
	  header("Location: http://".$_SESSION['this_ip']."/".pagename($_POST['pr']));
	  echo "<script type=\"text/javascript\"> \n";
	  Echo "document.location='http://".$_SESSION['this_ip']."/".pagename($_POST['pr'])."'; \n";
	  Echo "</script> \n";
	  exit;
	}
	unset($_POST['capval']);
	unset($_POST['cap']);
	unset($_SESSION['form_verification']);
	
	}

   ## ====================================================
   ## DOES BLOG_COMMENTS TABLE EXSIST?
   ## ====================================================
   
   $match = 0;
   
   $result = mysql_list_tables("$db_name");
   $i = 0;
   while ($i < mysql_num_rows ($result)) {
   	$tb_names[$i] = mysql_tablename ($result, $i);
   	if (strtolower($tb_names[$i]) == "blog_comments") { $match = 1; }
   	$i++;
   }
   
   // DOES NOT EXIST; CREATE TABLE NOW
   ## ====================================================
   if ($match != 1) {
   	$qry = "CREATE TABLE BLOG_COMMENTS (";
   	$qry .= " PRIKEY INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,";
   	$qry .= " BLOG_KEY INT,";
   	$qry .= " NAME VARCHAR(255),";
   	$qry .= " EMAIL BLOB,";
   	$qry .= " COMMENTS BLOB,";
   	$qry .= " COMMENT_DATE DATETIME,";
   	$qry .= " STATUS VARCHAR(255)";
      $qry .= ")";
      //ECHO $qry."<br/>";
   	if (!mysql_db_query("$db_name",$qry)){
   		echo lang("Could not create table")." BLOG_COMMENTS!<br>";
   		echo lang("Mysql says")." (".mysql_error().")";
   		exit;
   	}
   }
   
   # Loop through REQUEST vars
   $blog_key = $_REQUEST['blog_key'];
   $find_em = array("comment_name", "emailaddr", "blog_comments");
   foreach($_REQUEST as $var=>$val){
      foreach($find_em as $name){
         //echo "name = (".$name.") var = (".$var.")<br/>\n";
         if(eregi($name, $var)){
            ${$name} = db_string_format($val);
            //echo "(".$name.")=(".${$name}.")<br/>\n";
         }
      }
   }
   $comment_name = stripslashes(cleanString($comment_name));
   $blog_comments = stripslashes(htmlentities($blog_comments, ENT_QUOTES));
   
	#CHECK EMAIL ADDRESS BEFORE INSERT
	list($userName, $mailDomain) = split("@", $emailaddr);
	if (!myCheckDNSRR($mailDomain,"MX")){
	   # CANNOT VERIFY EMAIL... ERROR
      $comment_error = lang("The Email Address you specified could not be verified").".\n";
	}else{
	   # EMAIL VERIFIED... CONTINUE WITH INSERT AND EMAIL
	   
	   # BUT FIRST MAKE SURE THIS IS NOT A RE-POST WITHIN 60 SEC
	   $find_recent = "SELECT PRIKEY, BLOG_KEY, NAME, EMAIL, COMMENT_DATE FROM BLOG_COMMENTS WHERE NAME = '".$comment_name."' AND EMAIL = '".$emailaddr."' ORDER BY COMMENT_DATE DESC LIMIT 1";
      $result = mysql_query($find_recent);
      $PREV_COMMENT = mysql_fetch_array($result);
      
      $timestamp = strtotime($PREV_COMMENT['COMMENT_DATE']) + 60;
      if(mysql_num_rows($result) > 0 && $timestamp > time()){
         $time_error = lang("Were sorry, for security purposes you must wait at least 60 seconds before you post another comment.");
      }else{
         # Test: compare times
         //echo "(".$PREV_COMMENT['PRIKEY'].")(".date('D-j-Y g:ia', $timestamp).") < (".date('D-j-Y g:ia', time()).")<br/>\n";
         //exit;
         
         # NOT A RE-POST... CONTINUE WITH INSERT AND EMAIL
      	$comment_date = time();
      	
      	# Require approval or auto accept comment?
         if( !$blog_comment_settings->get("require_approval") || $blog_comment_settings->get("require_approval") == "yes" ){
            $status = "new";
         }else{
            $status = "approved";
         }
      	
         $blogQry = "INSERT INTO BLOG_COMMENTS VALUES('NULL', '".$blog_key."', '".$comment_name."', '".$emailaddr."', '".$blog_comments."', NOW(), '".$status."')";
         //echo "(".$blogQry.")";
         if ( !mysql_query($blogQry) ) {
            $comment_error = lang("Unable to post blog comment").".  ".lang("Please contact the site webmaster").".";
         }else{
            
            # Approval text displayed depending on status
            if($status == "new"){
               $comment_result_text = lang("Your comment has been posted but will not be displayed until the webmaster has approved it").". ".lang("Thank you").".";
            }else{
               $comment_result_text = lang("Your comment has been posted")."! ".lang("Thank you").".";
            }
            
            //echo "Emailing to (".$SITE_SPECS['df_email'].")<br/>\n";
            
            $result = mysql_query("SELECT df_email FROM site_specs LIMIT 1");
            $SITE_SPECS = mysql_fetch_array($result);
            
            $admin_email = $SITE_SPECS['df_email'];
            
            if(!$blog_comment_settings->get("emailto")){
               $blog_comment_settings->set("emailto", $admin_email);
            }else{
               $admin_email = $blog_comment_settings->get("emailto");
            }
            
            $to_email = $admin_email;
            $boundary = md5(uniqid(time()));
            
            # Thank you email to buyer
            $email_header = "";
            $email_header .= "From: noreply@".$_SESSION['this_ip']."\n";
            $email_header .= "MIME-Version: 1.0\n";
            $email_header .= "Content-Type: multipart/alternative;\n";
            $email_header .= "	boundary=\"=".$boundary."\"\n";

            $email_content = "";
            $email_content .= "--=".$boundary."\n";
            $email_content .= "Content-Type: text/plain; charset=\"ISO-8859-1\"\n";
            $email_content .= "Content-Transfer-Encoding: 7bit\n";
            $email_content .= "\n";
            $email_content .= $_SESSION['this_ip']." - ".lang("New blog comment")."\n";
            $email_content .= "---------------------------------------------------------------------------------\n\n";
            $email_content .= lang("A user on your site")." ".$_SESSION['this_ip'].", ".lang("has posted a comment for blog subject")." ".$BLOG_CATEGORY_NAME."\n\n";
            $email_content .= lang("Details of this comment")."...\n";
            $email_content .= "> ".lang("Name").": ".stripslashes($comment_name)."\n";
            $email_content .= "> ".lang("Email").": ".stripslashes($emailaddr)."\n";
            $email_content .= "> ".lang("Comments").": ".nl2br(stripslashes(html_entity_decode($blog_comments, ENT_QUOTES)))."\n";
            $email_content .= "> ".lang("Comment Time").": ".date('D-j-Y g:ia', $comment_date)."\n\n";
            # Instructions on comment status
            if($status == "new"){
               $email_content .= lang("Please login to your site and navigate to")." ".lang("Main Menu")." > ".lang("Blog Manager")." > ".lang("Blog Comments")." ".lang("to approve or deny this comment").".\n\n";
            }else{
               $email_content .= lang("This comment has been automatically approved and will display in the blog comments").".  ".lang("If you wish to require approval before displaying comments, please login to your site and navigate to")." ".lang("Main Menu")." > ".lang("Blog Manager")." > ".lang("Blog Comments")." > ".lang("Settings and check the 'Require webmaster approval' box").".\n\n";
            }
            $email_content .= lang("Thank you")."!\n\n";
            
            $email_content .= "--=".$boundary."\n";
            $email_content .= "Content-Type: text/html; charset=\"ISO-8859-1\"\n";
            $email_content .= "Content-Transfer-Encoding: quoted-printable\n";
            
            $email_content .= "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n";
            $email_content .= "<html xmlns=\"http://www.w3.org/1999/xhtml\">\n";
            $email_content .= "<head>\n";
            $email_content .= "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />\n";
            $email_content .= "<title>Untitled Document</title>\n";
            $email_content .= "</head>\n";
            $email_content .= "<style type=\"text/css\">\n";
            $email_content .= "a {\n";
            $email_content .= "   color: #6699CC;\n";
            $email_content .= "   text-decoration:none;\n";
            $email_content .= "}\n";
            $email_content .= "h2 {\n";
            $email_content .= "	font-size: 15px;\n";
            $email_content .= "}\n";
            $email_content .= ".style1 {color: #990000}\n";
            $email_content .= "</style>\n";
            $email_content .= "<body>\n";
            $email_content .= "<div align=\"center\" style=\"padding: 10px;\">\n";
            $email_content .= " <div align=\"left\" style=\"width: 600px; padding: 10px; border: 1px solid #666666; font-family: 'Trebuchet MS', Arial; font-size:12px;\">\n";
            $email_content .= "  <h2>".lang("New blog comment")."</h2>\n";
            $email_content .= "  <p>".lang("A user on your site")." ".$_SESSION['this_ip'].", ".lang("has posted a comment for blog subject")." ".$BLOG_CATEGORY_NAME.".\n";
            $email_content .= "  </p>\n";
            $email_content .= "  <p>".lang("Details of this comment")."...</p>\n";
            $email_content .= " <ul>\n";
            $email_content .= "  <li><b>".lang("Name").":</b> ".stripslashes($comment_name)."</li>\n"; 
            $email_content .= "  <li><b>".lang("Email").":</b> ".stripslashes($emailaddr)."</li>\n";
            $email_content .= "  <li><b>".lang("Comments").":</b> ".nl2br(stripslashes(html_entity_decode($blog_comments, ENT_QUOTES)))."</li>\n";
            $email_content .= "  <li><b>".lang("Comment Time").":</b> ".date('D-j-Y g:ia', $comment_date)."</li>\n";
            $email_content .= " </ul>\n";
            
            # Instructions on comment status
            if($status == "new"){
               $email_content .= "  <p>".lang("Please login to your site and navigate to")." ".lang("Main Menu")." > ".lang("Blog Manager")." > ".lang("Blog Comments to approve or deny this comment").".</p>\n";
            }else{
               $email_content .= "  <p>".lang("This comment has been automatically approved and will display in the blog comments").".  ".lang("If you wish to require approval before displaying comments, please login to your site and navigate to")." ".lang("Main Menu")." > ".lang("Blog Manager")." > ".lang("Blog Comments")." > ".lang("Settings and check the 'Require webmaster approval' box").".</p>\n";
            }
            
            $email_content .= "  <p>Thank you!</p>\n";
            $email_content .= " </div>\n";
            $email_content .= "</div>\n";
            $email_content .= "</body>\n";
            $email_content .= "</html>\n";
            
            mail("$to_email", "New Blog Comment", "$email_content", $email_header);
         }
      }
   }
}

if($comment_error || $comment_result_text){
   if($comment_error){
      echo "<div class=\"comment_status_error\">\n";
      echo $comment_error;
      echo "</div>\n";
   }else{
      echo "<div class=\"comment_status_success\">\n";
      echo $comment_result_text;
      echo "</div>\n";
   }
}else if($time_error){
   echo "<div class=\"comment_status_error\">\n";
   echo $time_error;
   echo "</div>\n";
}

echo "<script language=\"javascript\">\n";

?>
if(typeof window.$ == 'function') {
   function $() {
     var elements = new Array();

     for (var i = 0; i < arguments.length; i++) {
       var element = arguments[i];
       if (typeof element == 'string')
         element = document.getElementById(element);

       if (arguments.length == 1)
         return element;

       elements.push(element);
     }

     return elements;
   }
}
<?php

echo "function chk_n_send(form_key){\n";
echo "   var err = '';\n";
echo "   if($('comment_name_'+form_key).value.length < 1){\n";
echo "      err += 'Name,'\n";
echo "      $('comment_name_display_'+form_key).style.color='red';\n";
echo "   }\n";
echo "   if($('emailaddr_'+form_key).value.length < 1){\n";
echo "      err += 'Email,'\n";
echo "      $('emailaddr_display_'+form_key).style.color='red';\n";
echo "   }\n";
echo "   if($('blog_comments_'+form_key).value.length < 1){\n";
echo "      err += 'Comments'\n";
echo "      $('blog_comments_display_'+form_key).style.color='red';\n";
echo "   }\n";
echo "   if(err == ''){\n";
echo "      var form_name = 'add_blog_comment_form_'+form_key;\n";
echo "      eval('document.'+form_name+'.submit();')\n";
echo "   }else{\n";
echo "      alert('".lang("Please complete the following fields").": '+err+'.')\n";
echo "   }\n";
echo "}\n";

echo "function chk_n_send_captcha(form_key){\n";
echo "   if(zulucrypt(form_key)){\n";
echo "   var err = '';\n";
echo "   if($('comment_name_'+form_key).value.length < 1){\n";
echo "      err += 'Name,'\n";
echo "      $('comment_name_display_'+form_key).style.color='red';\n";
echo "   }\n";
echo "   if($('emailaddr_'+form_key).value.length < 1){\n";
echo "      err += 'Email,'\n";
echo "      $('emailaddr_display_'+form_key).style.color='red';\n";
echo "   }\n";
echo "   if($('blog_comments_'+form_key).value.length < 1){\n";
echo "      err += 'Comments'\n";
echo "      $('blog_comments_display_'+form_key).style.color='red';\n";
echo "   }\n";
echo "   if(err == ''){\n";
echo "      var form_name = 'add_blog_comment_form_'+form_key;\n";
echo "      eval('document.'+form_name+'.submit();')\n";
echo "   }else{\n";
echo "      alert('".lang("Please complete the following fields").": '+err+'.')\n";
echo "   }\n";
echo "   }\n";
echo "}\n";

echo "</script>\n";





// Pull entries for choosen archive month
//------------------------------------------
if ($blog_archive != "") {
	$tmp = split("-", $blog_archive);
	$this_year = $tmp[0];
	$current_month = $tmp[1];
	$blog_qry = "AND BLOG_DATE LIKE '%$blog_archive-%' ORDER BY BLOG_DATE DESC"; // Limit by month

// Pull most recent 15 entries
//------------------------------------------
} else {
	$this_year = date("Y");
	$current_month = date("m");
	$blog_qry = "ORDER BY BLOG_DATE DESC, PRIKEY DESC LIMIT 15"; // Limit by 15
}


$n = $current_month + 1;
$p = $current_month - 1;

$prev_month = date("Y-m-d", mktime(0,0,0,$p,31,$this_year));
$next_month = date("Y-m-d", mktime(0,0,0,$n,1,$this_year));

// Build two column table --> content on the left archives on right

echo "<table class=\"blog_outer_table\" border=0 cellpadding=4 cellspacing=0 width=100% align=center>\n";
echo " <tr>\n";
echo "  <td align=center valign=top width=80%>\n";

// First Get the Key Number for the "Named" subject (category)

$BLOG_CATEGORY_NAME = db_string_format($BLOG_CATEGORY_NAME);

$result = mysql_query("SELECT PRIKEY FROM BLOG_CATEGORY WHERE CATEGORY_NAME = '$BLOG_CATEGORY_NAME'");
$SUBJ_KEY = mysql_fetch_array($result);

// Display Blog Subject

$BLOG_CATEGORY_NAME = stripslashes($BLOG_CATEGORY_NAME);

echo "   <table align=center cellpadding=\"5\" cellspacing=\"0\" border=\"0\" width=\"100%\">\n";
echo "    <tr>\n";
echo "     <td valign=top width=100% class=\"blog_category_name\">\n";
echo "      ".$BLOG_CATEGORY_NAME."\n";
echo "     </td>\n";
echo "    </tr>\n";
echo "   </table>\n";
echo "   <br>\n";

// Pull all entries for appropriate month from DB - Mantis #0000032
//==========================================================================
$blogQry = "SELECT * FROM BLOG_CONTENT WHERE BLOG_SUBJECT = '".$SUBJ_KEY['PRIKEY']."' ".$blog_qry;
if ( !$result = mysql_query($blogQry) ) {
   //echo "Unable to select content from BLOG_CONTENT table where BLOG_SUBJECT = [".$SUBJ_KEY[PRIKEY]."] ($blog_qry)<br>".mysql_error()."<br>"; exit;
} else {
   //echo "Num found: [".mysql_num_rows($result)."]<br>";
}

while ($row = mysql_fetch_array($result)) {
   //echo "<b>".$row['BLOG_TITLE']."</b><br>".$row['BLOG_DATA']."<HR>";

   // MM fix 030922 to allow links to display in blog content :: Bugzilla #34
   $row['BLOG_DATA'] = eregi_replace("SOHOLINK", "href", $row[BLOG_DATA]);
	$blog_title = stripslashes($row['BLOG_TITLE']);
	$blog_data = stripslashes($row['BLOG_DATA']);

   # Format entry date so month name is translateable (Mantis #198)
   $tmp = split("-", $row[BLOG_DATE]);
   $display_date = lang(date("F", mktime(0,0,0,$tmp[1],$tmp[2],$tmp[0])));
   $display_date .= " ".date("j, Y", mktime(0,0,0,$tmp[1],$tmp[2],$tmp[0]));

   echo "   <table border=\"0\" cellpadding=\"2\" cellspacing=\"0\" width=\"100%\" align=\"center\">\n";
   echo "    <tr>\n";
   echo "     <td align=\"left\" valign=\"top\" class=\"text\">\n";
   echo "      <div class=\"entry_separator\"><hr /></div>\n";
   echo "      <span class=\"blog_date\"><tt>".$display_date."</tt></span><br>\n";
   echo "      <span class=\"blog_title\">".lang($blog_title)."</span></b>\n";
   echo "     </td>\n";
   echo "    </tr>\n";
   echo "    <tr>\n";
   echo "     <td align=left valign=top class=\"text blog_text\">".$blog_data."</td>\n";
   echo "    </tr>\n";
   echo "   </table>";
   
   if($is_allowed == "yes" && eregi($BLOG_CATEGORY_NAME, $blog_comment_settings->get("allowed_categorys"))){
      
      # Make sure page name is set
      $pr = $_REQUEST['pr'];
      if(strlen($pr) < 1){
         $pr = "index";
      }
      
      # Add blog comment
      echo "   <p class=\"blog_comment\"><a href=\"javascript: return false;\" onClick=\"toggleid('add_blog_comment_".$row['PRIKEY']."');\">".lang("Add new comment")."</a></p>\n";
      echo "   <div class=\"add_blog_comment\" id=\"add_blog_comment_".$row['PRIKEY']."\">\n";
      echo "      <form name=\"add_blog_comment_form_".$row['PRIKEY']."\" method=\"POST\" action=\"".$pr.".php\">\n";
      echo "         <input type=\"hidden\" name=\"process\" value=\"blog_comment\" />\n";
      echo "         <input type=\"hidden\" name=\"blog_key\" value=\"".$row['PRIKEY']."\" />\n";
      
      echo "          <!---Begin form display-->\n";
      echo "          <div class=\"form_body_container\" style=\"\">\n";
      
      echo "          <div class=\"field-container\">\n";
      echo "          <p class=\"instructions\">All fields are required.</p>\n";
      echo "          <div class=\"ie_cleardiv\">\n";
      echo "          </div>\n";
      echo "          </div>\n";
      
      # Name
      echo "          <div class=\"field-container\">\n";
      echo "          <p class=\"myform-field_title-left\" id=\"comment_name_display_".$row['PRIKEY']."\"><label for=\"comment_name_".$row['PRIKEY']."\">Name:</label><span class=\"asterisk\">*</span>\n";
      echo "          </p>\n";
      echo "          <p class=\"myform-input_container\"><input type=\"text\" name=\"comment_name_".$row['PRIKEY']."\" id=\"comment_name_".$row['PRIKEY']."\" style=\"width: 220px; color: #999999;\" onclick=\"if(this.value=='Alphanumeric characters only')this.value='';this.style.color='';\" value=\"Alphanumeric characters only\" /></p>\n";
      echo "          <div class=\"ie_cleardiv\">\n";
      echo "          </div>\n";
      echo "          </div>\n";
      
      # Email
      echo "          <div class=\"field-container\">\n";
      echo "          <p class=\"myform-field_title-left\" id=\"emailaddr_display_".$row['PRIKEY']."\"><label for=\"emailaddr_".$row['PRIKEY']."\">Email Address:</label><span class=\"asterisk\">*</span>\n";
      echo "          </p>\n";
      echo "          <p class=\"myform-input_container\"><input type=\"text\" name=\"emailaddr_".$row['PRIKEY']."\" id=\"emailaddr_".$row['PRIKEY']."\" style=\"width: 220px;\"/></p>\n";
      echo "          <div class=\"ie_cleardiv\">\n";
      echo "          </div>\n";
      echo "          </div>\n";
      
      # Comments
      echo "          <div class=\"field-container\">\n";
      echo "          <p class=\"myform-field_title-left\" id=\"blog_comments_display_".$row['PRIKEY']."\"><label for=\"blog_comments_".$row['PRIKEY']."\">Comments</label><span class=\"asterisk\">*</span>\n";
      echo "          </p>\n";
      echo "          <p class=\"myform-formfield_container\"><textarea name=\"blog_comments_".$row['PRIKEY']."\" id=\"blog_comments_".$row['PRIKEY']."\" style=\"width: 250px;height: 85px;\"></textarea></p>\n";
      echo "          <div class=\"ie_cleardiv\">\n";
      echo "          </div>\n";
      echo "          </div>\n";
      
      echo "          <div class=\"userform-submit_btn-container\">\n";
      
      if($blog_comment_settings->get("captcha") == "yes"){
      
         echo "            ".$captcha."\n";
         //echo "            <input id=\"userform-submit_btn\" onclick=\"return zulucrypt();\" />\n";
         
      	$pattern = "abdehklmprsuwx123456789";
      	$key = "<table cellpadding=\"0\" cellspacing=\"0\">\n";	
      	$key .= "<tr>\n";
      	$synckey = '';
      	$key .= "<td height=\"34\" cellpadding=\"0\"><table align=left height=\"34\" cellpadding=\"0\" width=\"216\" cellspacing=\"0\"><tr>\n";
      	for($i=0;$i<6;$i++){  	
      			$key .= "<td  width=\"36px\" height=\"34px\" align=\"left\"><image src=\"sohoadmin/client_files/captcha/";
      			$keyz = $pattern{rand(0,22)};
      			$key .= $keyz.".gif\" width=\"36px\" height=\"34px\" style=\"border:1px solid black;\"></td>\n";
      			$synckey .= $keyz;
      	}
      	$synckey = strtoupper($synckey);
      //	$key .= "</form>\n";
      	
      	$key .= "</tr></table></td></tr>\n";
      	$key .= "<tr><td align=\"left\" valign=top style=\"padding:0px;\">\n";
      	$key .= "<input name=\"capval_".$row['PRIKEY']."\" id=\"capval_".$row['PRIKEY']."\" type=\"hidden\" value=\"".md5($synckey)."\">\n";
      	$key .= "<label for=\"cap_".$row['PRIKEY']."\">Please&nbsp;enter&nbsp;the&nbsp;phrase&nbsp;as&nbsp;it&nbsp;is&nbsp;shown&nbsp;in&nbsp;the&nbsp;box&nbsp;above.&nbsp;&nbsp;&nbsp;</label>\n";
      	//$key .= "<td align=\"left\">\n";
      	$key .= "<input name=\"cap_".$row['PRIKEY']."\" id=\"cap_".$row['PRIKEY']."\" type=\"text\" size=\"6\" maxlength=\"6\" style=\"border:1px solid black; text-align:left; font-size:18px;\"></td></tr>\n";
      	$key .= "</table>";
      	$_SESSION['form_verification'][$row['PRIKEY']] = md5($synckey);
      	echo $key;
      	//echo $ckey;
      //	echo "<br/>";
      //	echo $synckey;
      //	echo "<br/>";
      	//echo md5($synckey);   
         echo "          <input class=\"submit_btn\" type=\"button\" onclick=\"chk_n_send_captcha('".$row['PRIKEY']."')\" value=\"".lang("Submit")." >>\">\n";
      }else{
         echo "          <input class=\"submit_btn\" type=\"button\" onclick=\"chk_n_send('".$row['PRIKEY']."')\" value=\"".lang("Submit")." >>\">\n";
      }
      
      echo "          </div>\n";
       
      echo "          </div>\n";
      
      echo "      </form>\n";
      echo "   </div>\n";
      
      
      # Blog comments
      $comment_qry = "SELECT PRIKEY, NAME, COMMENTS, COMMENT_DATE FROM BLOG_COMMENTS WHERE BLOG_KEY = '".$row['PRIKEY']."' AND STATUS = 'approved' ORDER BY COMMENT_DATE ASC";
      $comment_result = mysql_query($comment_qry);
      
      if(mysql_num_rows($comment_result) > 0){
         echo "<p class=\"show_hide_comments\"><a href=\"javascript: return false;\" onClick=\"toggleid('comment_container_".$row['PRIKEY']."')\">Show/Hide Comments (".mysql_num_rows($comment_result).")</a></p>\n";
         echo "<div class=\"comment_container\" id=\"comment_container_".$row['PRIKEY']."\">\n";
         
         while($COMMENTS = mysql_fetch_array($comment_result)){
            echo "<div class=\"a_comment\">\n";
            echo "   <h3>".stripslashes($COMMENTS['NAME'])."</h3>\n";
            echo "   <span>".date("M-j g:ia",strtotime($COMMENTS['COMMENT_DATE']))."</span>\n";
            echo "   <p>".nl2br(stripslashes(html_entity_decode($COMMENTS['COMMENTS'], ENT_QUOTES)))."</p>\n";
            echo "</div>\n";
         }
         
         echo "</div>\n";
      }else{
         echo "<p class=\"no_comments\">".lang("No Comments")."</p>\n";
      }
   }
   
}

echo "  </td>\n";
echo "  <td align=\"left\" valign=\"top\" class=\"text archive_container\">";

	$result = mysql_query("SELECT * FROM BLOG_CATEGORY WHERE PRIKEY = '$SUBJ_KEY[PRIKEY]' ORDER BY CATEGORY_NAME");
	while ($row = mysql_fetch_array($result)) {
		echo "<BR><BR><BR><B><div class=\"archive_text\">".lang("Archives")."</div></B>";

		# Will prevent duplicates
		$process_watch = ";";
		
		# Begin archive month list
		$DISPLAY_HISTORY = "<div class=\"archive_link_container\">\n";

		# Pull dates of past entries
		$thistory = mysql_query("SELECT BLOG_DATE FROM BLOG_CONTENT WHERE BLOG_SUBJECT = '$SUBJ_KEY[PRIKEY]' ORDER BY BLOG_DATE");

		# Loop through dates and build unique by-month archive
		while($tmp = mysql_fetch_array($thistory)) {
			$dbNUM = split("-", $tmp['BLOG_DATE']);

			# Split up month and year so we can translate (Mantis #198)
			$ARCHIVE_MONTH = date("F", mktime(0,0,0,$dbNUM[1],$dbNUM[2],$dbNUM[0]));
			$ARCHIVE_YEAR = date("Y", mktime(0,0,0,$dbNUM[1],$dbNUM[2],$dbNUM[0]));
			$ARCHIVE_DATE = lang("$ARCHIVE_MONTH")." ".$ARCHIVE_YEAR;

			# Format date to pass in link
			$archive_data = date("Y-m", mktime(0,0,0,$dbNUM[1],$dbNUM[2],$dbNUM[0])); // Swapped m-Y to Y-m for easier dB searching (Mantis #32)

			# Display archive month links
			if (!eregi(";$archive_data;", $process_watch)) {
			 	$process_watch .= "$archive_data;";
				$DISPLAY_HISTORY .= "<a href=\"".pagename($_REQUEST['pr'], "&")."blog_archive=".$archive_data."\">".$ARCHIVE_DATE."</a><br>";
			}

		} // End while

		# End archive month list
		$DISPLAY_HISTORY .= "</div>\n";

		# Strip off trailing <br> tag
		$histLength = strlen($DISPLAY_HISTORY);
		$histDispLength = $histLength - 4;
		$DISPLAY_HISTORY = substr($DISPLAY_HISTORY, 0, $histDispLength);

		echo $DISPLAY_HISTORY;

	} // End While (outer)

echo '</td></tr></table>';


?>