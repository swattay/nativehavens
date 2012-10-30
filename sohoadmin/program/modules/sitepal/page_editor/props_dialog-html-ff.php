<?
# SitePal plugin: Choose scene to place page
# Pull account un/pw from db and attemp to verify (prompt and direct to management module if problem)
# If account data is good, ping mngListScenes.php api to get scene list
# ...if this is too slow may have to store scense in db and re-update automatically when hitting manage module, for example

# For testing
function curl_support() {
   if ( function_exists("curl_init") ) {
      return true;
   } else {
      return false;
   }
}


?>

<DIV ID="sitepal_dialog" class="prop_layer">
<div class="prop_head">SitePal Selection</div>

 <table cellpadding="0" cellspacing="0" width="100%" class="prop_table">
  <tr>
	<td align="center" valign="middle">

<?
$closedialog_onclick = "";
$closedialog_onclick .= "show_hide_layer('objectbar','','show','sitepal_dialog','','hide');";
$closedialog_onclick .= "replaceImageData();";
$closedialog_onclick .= "makeUnScroll(ColRowID);";

# ERROR: cURL not installed
if ( !curl_support() ) {
   /*---------------------------------------------------------------------------------------------------------*
    _  _         ___            _
   | \| | ___   / __|_  _  _ _ | |
   | .` |/ _ \ | (__| || || '_|| |
   |_|\_|\___/  \___|\_,_||_|  |_|
   /*---------------------------------------------------------------------------------------------------------*/

   # POPUP: Error - cURL not installed on server
   $popup_content = "<p>The SitePal feature depends upon certain php functions that do not appear to be available on the web server that your website is hosted on.</p>";

   $popup_content .= "<p><b>To fix:</b> \n";
   $popup_content .= "Contact your web host and ask them to install \"the curl library for php\" on your server.\n";
   $popup_content .= "They should know what you're talking about.</p>\n";

   $popup_content .= "<p style=\"margin-bottom: 35px;\">In case it's helpful: instructions on installing CURL for PHP can be found \n";
   $popup_content .= "<a href=\"http://us3.php.net/manual/en/ref.curl.php\">here</a>.\n";
   $popup_content .= "Fair warning though: These instructions are <i>not</i> begginner-friendly.\n";
   $popup_content .= "</p>\n";

   echo help_popup("error-no_curl", "Problem: CURL library not installed on this web server", $popup_content, "top: 15%;left:20%;");

   echo "   <div style=\"width: 550px;padding:10px;border: 1px solid #980000;background-color: #efefef;text-align:left;\">\n";
   echo "    <b>Problem: cURL library is not installed on this server.</b> SitePal features require cURL functions to operate.</b>\n";
//   echo "    Tell your web host to install cURL.<br/>\n";
   echo "    <span class=\"red uline hand\" onclick=\"showid('error-no_curl');\">Click for more info about this error.</span>";
   echo "   </div>\n";

   # [cancel]
   echo "   <div nowrap=\"\" onClick=\"".$closedialog_onclick."\" style=\"margin-top: 5px;z-index: 99;border-style: none solid solid; border-color: rgb(0, 0, 0); border-width: 1px; margin: 0pt; padding: 0px 15px; background-image: url(http://mnm.soholaunch.com/sohoadmin/program/includes/display_elements/graphics/btn-nav_main-off.jpg); display: block; height: 16px;width: 100px;\" onclick=\"savePage('page_editor.php');\" onmouseout=\"this.style.backgroundImage='url(http://mnm.soholaunch.com/sohoadmin/program/includes/display_elements/graphics/btn-nav_main-off.jpg)';\" onmouseover=\"this.style.backgroundImage='url(http://mnm.soholaunch.com/sohoadmin/program/includes/display_elements/graphics/btn-nav_main-on.jpg)';\" class=\"nav_main\">\n";
   echo "    <div nowrap=\"\" style=\"margin: 0pt; display: block; vertical-align: top; padding-top: 2px;\">Cancel</div>\n";
   echo "   </div>\n";

} else {

   /*---------------------------------------------------------------------------------------------------------*
    _  _       _     ___       _     _   _
   | \| | ___ | |_  / __| ___ | |_  | | | | _ __
   | .` |/ _ \|  _| \__ \/ -_)|  _| | |_| || '_ \
   |_|\_|\___/ \__| |___/\___| \__|  \___/ | .__/
                                           |_|
   # Account info not available
   /*---------------------------------------------------------------------------------------------------------*/
   if ( !sitepal_verified() ) {
      echo "   <div style=\"width: 75%;height: 70px;overflow: auto;padding:5px;border: 1px solid #980000;background-color: #efefef;text-align:left;margin-top: 5px;\">\n";
//      echo "    <b>Drop-down box empty?</b>\n";

      # No account data or bad account data?
      $qry = "select prikey from smt_sitepal_accounts limit 1";
      $rez = mysql_query($qry);

      if ( mysql_num_rows($rez) < 1 ) {
         echo "     <p><b>SitePal not setup yet!</b> You have to fill-in your SitePal account info to unlock SitePal features like being able to drag-and-drop scenes onto pages.</p>\n";
         echo "     <p>Go to Main Menu > SitePal for more info/options.</p>\n";
      } else {
         echo "<p style=\"margin: 0;\"><b>There was a problem verifying your SitePal account information.</b><br/>\n";
         echo " Please go to <b>Main Menu > SitePal</b> and make sure your SitePal account info is current. \n";
         echo " Until this is corrected you will not be able to drag-and-drop SitePal characters onto pages.</p>\n";
         echo " <p style=\"margin-top: 5px;\">If the problem is that your saved account info is no longer current (e.g., due to password change), \n";
         echo " then any characters that you have already placed on your site pages should continue to function.\n";
         echo " However, if the problem is with your SitePal account itself (e.g., account suspended or cancelled), \n";
         echo " then characters already placed on your site pages may cease to function (until you resolve the account issue).</p>\n";
      }

      echo "   </div>\n";
      echo "   <div nowrap=\"\" onClick=\"".$closedialog_onclick."\" style=\"z-index: 99;border-style: none solid solid; border-color: #000; border-width: 1px; margin: 5px 0 0 0; padding: 0px 15px; background-image: url(http://mnm.soholaunch.com/sohoadmin/program/includes/display_elements/graphics/btn-nav_main-off.jpg); display: block; height: 16px;width: 200px;\" onclick=\"savePage('page_editor.php');\" onmouseout=\"this.style.backgroundImage='url(http://mnm.soholaunch.com/sohoadmin/program/includes/display_elements/graphics/btn-nav_main-off.jpg)';\" onmouseover=\"this.style.backgroundImage='url(http://".$_SESSION['this_ip']."/sohoadmin/program/includes/display_elements/graphics/btn-nav_main-on.jpg)';\" class=\"nav_main\">\n";
      echo "    <div nowrap=\"\" style=\"margin: 0pt; display: block; vertical-align: top; padding-top: 2px;\">OK, Close this error message</div>\n";
      echo "   </div>\n";

   } else {
      /*---------------------------------------------------------------------------------------------------------*
      __   __          _   __  _          _
      \ \ / /___  _ _ (_) / _|(_) ___  __| |
       \ V // -_)| '_|| ||  _|| |/ -_)/ _` |
        \_/ \___||_|  |_||_|  |_|\___|\__,_|

      # SitePal account info on file and verified!
      /*---------------------------------------------------------------------------------------------------------*/
?>
   <script type="text/javascript">
   // Swaps preview image to selected scene thumbnail
   // Value of scene dd passed as thumbfile~~~scenename~~~accountid
   function sitepal_thumb(combined, sitepal_baseURL) {
      // Skip to next if account divider selected (vs actual scene option)
      if ( combined == "" ) {
         var isnow = $('scene_dd').selectedIndex;
         var onedown = eval(isnow+"+1");
         var oneup = eval(isnow+"-1");
         var lastone = eval($('scene_dd').length+"-1");

         if ( onedown > lastone ) {
            // Move up one if at bottom already
            var newone = oneup;
         } else {
            // Move to next one down
            var newone = onedown;
         }

         $('scene_dd').selectedIndex = newone;
         var combined = $('scene_dd').value;
      }

      // Get thumbnail path from value and swap preview image now
      if ( combined != "" ) {
         var info = combined.split("~~~");
         var thumbnail = info[0];
         var accountid = info[2];
         document.getElementById('sitepal_thumbnail').src = sitepal_baseURL+thumbnail;
      }
   }
   </script>

	 <table border="0" cellpadding="2" cellspacing="0" align="center">
	  <tr>

		<td rowspan="2" align="center" valign="top" style="background-color: #ccc; border: 1px solid #2e2e2e;">
		 <img src="pixel.gif" id="sitepal_thumbnail" width="50" height="50">
		</td>

		<td valign="top">
		 Choose SitePal scene to place on page:<br/>
<?
//      include_once($_SESSION['docroot_path']."/sohoadmin/program/modules/sitepal/sitepal_functions.inc.php");
      $sp_scenes = sitepal_get_scenes();
      krsort($sp_scenes);
      //echo testArray($sp_scenes, "200px");
?>

       <select id="scene_dd" name="scene_dd" style="font-face: Arial; font-size: 8pt; width: 250px;" onchange="sitepal_thumb(this.value, '<? echo $_SESSION['sitepal_BaseURL']; ?>');">
        <!--- <option value="NULL" STYLE='color:#999999;'>[<? echo $total_scenes; ?>] SitePal scenes available...</option> -->

<?
      //$sp_scenes = sitepal_get_scenes();
      //echo testArrray($sp_scenes, "200px");

      # Number of SitePal accounts that are set up
      $numaccounts = count($sp_scenes);

      # Single or multi-account behaviors?
      if ( $numaccounts > 1 ) {
         # MULTI
         $account_dividers = true;
         $default_selectedindex = 1;
      } else {
         # SINGLE
         $account_dividers = false;
         $default_selectedindex = 0;
      }

      # Error flag triggered if one or more accounts (but not all) fail to verify (technically: if their scene list is empty)
      $problem_account = false;

      # Split by account
      foreach ( $sp_scenes as $account_id=>$scenes ) {
         $numscenes = count($scenes);

         # Show dividers for multiple accounts?
         if ( $account_dividers ) {
            # Problem with verifying this account?
            if ( $numscenes < 1 ) {
               # ERROR - could not verify account, show error
               echo "       <option value=\"\" style=\"background: #ff0000;color: #fff;\">Unable to pull scenes for account #".$account_id."</option>\n";
               $problem_account = true;

            } else {
               # VERIFIED - show normal account divider
               echo "       <option value=\"\" style=\"background: #000;color: #fff;\">---Account ".$account_id."---</option>\n";
            }
         }

         for ( $s = 0; $s < $numscenes; $s++ ) {
            if ($tmp == "#EFEFEF") { $tmp = "WHITE"; } else { $tmp = "#EFEFEF"; }
            if ( trim($scenes[$s]['name']) != "" && !eregi("silhouette", $scenes[$s]['thumb']) ) {
               echo "       <option value=\"".$scenes[$s]['thumb']."~~~".$scenes[$s]['name']."~~~".$account_id."\" style=\"background: ".$tmp.";\">".$scenes[$s]['name']."</option>\n";
            }
         }
      } // End foreach
?>
       </select>

       <script type="text/javascript">
       // Select and preview first option from scene dd by default
       $('scene_dd').selectedIndex = <? echo $default_selectedindex; ?>;
       sitepal_thumb($('scene_dd').value, '<? echo $_SESSION['sitepal_BaseURL']; ?>');
       </script>
		</td>

		<!---Place on Page button-->
		<td rowspan="2" align="center" valign="top" style="padding-top: 15px;">
       <div nowrap="" onclick="place_sitepal('<? echo $_SESSION['sitepal_BaseURL']; ?>');" style="z-index: 99;border-style: none solid solid; border-color: rgb(0, 0, 0); border-width: 1px; margin: 0pt; padding: 0px 15px; background-image: url(../../includes/display_elements/graphics/btn-nav_save-off.jpg); display: block; height: 16px;" onmouseout="this.style.backgroundImage='url(../../includes/display_elements/graphics/btn-nav_save-off.jpg)';" onmouseover="this.style.backgroundImage='url(../../includes/display_elements/graphics/btn-nav_save-on.jpg)';" class="nav_save">
        <div nowrap="" style="margin: 0pt; display: block; vertical-align: top; padding-top: 2px;">Place on Page</div>
       </div>
		</td>

		<!---Cancel button-->
		<td rowspan="2" align="center" valign="top" style="padding-top: 15px;">
       <div nowrap="" onClick="<? echo $closedialog_onclick; ?>" style="z-index: 99;border-style: none solid solid; border-color: rgb(0, 0, 0); border-width: 1px; margin: 0pt; padding: 0px 15px; background-image: url(../../includes/display_elements/graphics/btn-nav_main-off.jpg); display: block; height: 16px;" onmouseout="this.style.backgroundImage='url(../../includes/display_elements/graphics/btn-nav_main-off.jpg)';" onmouseover="this.style.backgroundImage='url(../../includes/display_elements/graphics/btn-nav_main-on.jpg)';" class="nav_main">
        <div nowrap="" style="margin: 0pt; display: block; vertical-align: top; padding-top: 2px;">Cancel</div>
       </div>
		</td>
	  </tr>

	  <tr>
	   <td>
	    Width: <input type="text" id="sitepal_width" name="sitepal_width" value="165" style="width: 55px;">
	    Height: <input type="text" id="sitepal_height" name="sitepal_height" value="155" style="width: 55px;">
	   </td>
	  </tr>
    </table>

<?
      # Account verify problem(s)?
      if ( $problem_account ) {
         echo "    <p style=\"text-align: center;\"><strong class=\"red\">Warning:</strong> There was a problem verifying one or more of your SitePal accounts.<br/>\n";
         echo "     Please go to <b>Main Menu > SitePal</b> and make sure your SitePal account info is current. \n";
      }

      # Show preview image for initially-selected scene
      if ( $total_scenes > 0 ) {
         echo " <script type=\"text/javascript\">\n";
         echo "  sitepal_thumb(document.getElementById('scene_dd').value);\n";
         echo " </script>\n";
      }

   } // End if sitepal_verified()

} // End if cURL enabled
?>

	</td>
  </tr>
 </table>

</div>
