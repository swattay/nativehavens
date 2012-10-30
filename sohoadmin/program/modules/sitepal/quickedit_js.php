<?

# This function gets added to the js functions defined at the top of header.php

?>

// Called on change by quickEdit drop-down in Page Editor
function quickEdit(page) {
   // Jump to selected page (for editing)
   if ( document.all ) {
      parent.body.location = "modules/page_editor/page_editor.php?currentPage="+page;
   } else {
      parent.body.location = "modules/page_editor/page_editor-ff.php?currentPage="+page;
   }
}