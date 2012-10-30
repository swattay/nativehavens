<?


/*************************************************************************************************
 ___                     _   ___   _        _         ___
/ __| _ __  ___  ___  __| | |   \ (_) __ _ | |  ___  | _ \ __ _  __ _  ___  ___
\__ \| '_ \/ -_)/ -_)/ _` | | |) || |/ _` || | |___| |  _// _` |/ _` |/ -_)(_-<
|___/| .__/\___|\___|\__,_| |___/ |_|\__,_||_|       |_|  \__,_|\__, |\___|/__/
     |_|                                                        |___/

# Developer 'Open Page' drop-downs
/*************************************************************************************************/

# Override standard menu set for Page Editor
//$upnav['PAGE_EDITOR_LAYER'] = array();

# Build page arrays (based on menu status) for jump menus
# Loop all and split into diff arrays by menu status
$pgrez = mysql_query("SELECT * FROM site_pages ORDER BY page_name");

$main_pages = array();
$sub_pages = array();
$offmenu_pages = array();

while ( $getPage = mysql_fetch_array($pgrez) ) {

   # Main menu pages
   if ( $getPage['main_menu'] > 0 ) {
      $main_pages[] = $getPage['page_name'];

   # Sub-menu pages
   } elseif ( strlen($getPage['sub_page_of']) > 4 ) {
      $tmppg = split("~~~", $getPage['sub_page_of']);
      $sub_pages[$tmppg[0]][] = array('sort'=>$tmppg[1], 'name'=>$getPage['page_name']);

   # Off-menu pages
   } else {
      $offmenu_pages[] = $getPage['page_name'];
   }
}


# Build quick-edit drop-down box
if ( $CUR_USER_ACCESS != 'WEBMASTER' ) { // Show normal Open Pages button

   # Use standard Edit Pages button if not logged-in as webmaster
   $upnav['PAGE_EDITOR_LAYER'][0] = mkbutton( "openpage", "Open Page", "nav_main", "savePage('../open_page.php');" );

} else { // Show QuickEdit drop-down

   # [ On-menu pages ]
   $dropdown_box = "      <select name=\"jump_menupg\" style=\"width: 150px; position: absolute; top: 5px; left: 7px;\" onchange=\"quickEdit(this.value);\">\n";
   $dropdown_box .= "       <option value=\"\" style=\"background-color: #ccc;\">[".lang("On-Menu Pages")."]</option>\n";
   foreach ( $main_pages as $key=>$mp ) {
      $dropdown_box .= "       <option value=\"".$mp."\">".$mp."</option>\n";

      # Pull sub-pages for this page
      foreach ( $sub_pages[$mp] as $sp ) {
         $dropdown_box .= "       <option value=\"".$sp[name]."\">&gt;&gt; ".$sp[name]."</option>\n";
      }
   }

   # [ Off-menu pages ]
   $dropdown_box .= "       <option value=\"\" style=\"background-color: #ccc;\">[".lang("Off-Menu Pages")."]</option>\n";

   foreach ( $offmenu_pages as $key=>$op ) {
      $dropdown_box .= "       <option value=\"".$op."\">".$op."</option>\n";
   }

   $dropdown_box .= "      </select>\n";

   # Replace 'Open Pages' button with quickjump dd
   $upnav['PAGE_EDITOR_LAYER'][0] = $dropdown_box;

   # Change color of Save As button
   $upnav['PAGE_EDITOR_LAYER'][1] = mkbutton( "savepage", "Save Page", "nav_save", "savePage('page_editor.php');" );
   $upnav['PAGE_EDITOR_LAYER'][2] = mkbutton( "saveas", "Save As", "nav_main", "save_as_layer();" );


} // End if user has webmaster rights

?>