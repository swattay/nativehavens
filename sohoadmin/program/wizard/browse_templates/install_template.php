<?php
#=========================================================================================================
# Download/Install selected template
#=========================================================================================================
# ACCEPTS: $_GET['template_folder']

/* ==================START Script Outline==================*
#Is template licensed for this site? (free or paid-for)
# Pass template folder name, domain, and server hostname to license check api
if yes licensed {
   # Get template file download url from api

   # Download remote template .zip file to /pages folder

   # Create /temp folder

   # Copy downloaded .zip file to /temp folder

   # Extract .zip file in /temp folder

   # Delete copy of .zip within /temp folder

   # Read /temp folder contents to get name of extracted template folder

   # Store name of extracted folder in $new_template_folder

   # Kill /temp folder

   # Extract downloaded .zip in pages folder

   # Verify that new template folder exists in pages

   # Delete copy of downloaded zip in pages folder

   # Redirect to Browse Templates page (preserve search filter settings so last-viewed templates still display)

   # Show success message (report at top or something, not popup that they have to close)
      # "Template Installed! Go to My Templates to view/manage it."

} elseif not licensed {
   # Redirect to Browse Templates (preserve search filter settings)

   # Show "not license-able" message popup

}
/* ==================END Script Outline==================*/

# Was template folder passed as expected?
if ( $_GET['template_folder'] != "" ) {
   echo "<p>".lang("Template folder").": [".$_GET['template_folder']."]</p>";

   # Licensed?
   if ( !addon_licensed($_GET['template_folder']) ) {
      $report[] = lang("You must purchase a license for this template before you can install it").".";
   }
}



?>