/* Blog layout styles */
/* Changes must be made to doc root file and its duplicate in sohoadmin/client_files/base_files **OR** from admin Blog Styles */
/* Image paths must be relative to doc root (http://domain.com) */


/* GENERAL */

.blog_outer_table {

}

.blog_category_name {
   font-family: Verdana, Arial, Helvetica, sans-serif;
   font-size: 16px;
   font-weight: bold;
}

.entry_separator {
   width: 100%;
   text-align: center;
   color: black;
   background-color: black;
   height: 2px;
}

.entry_separator hr {
   display: none;
}

.blog_date {
   
}

.blog_title {
   font-weight: bold;
   face: arial;
   font-size: 9pt;
}

.blog_text {

}

.archive_container {          /* Right side column */

}

.archive_text {
   text-decoration: underline;
   margin: 0;
   padding: 0;
}

.archive_link_container {
   padding-top: 4px;
   font-family: Arial;
   font-size: 8pt;
}


/* COMMENTS */

.show_hide_comments {
   text-align: left;
   font-size: 12px;
   margin-top: 0;
   margin-left: 2px;
   margin-bottom: 5px;
}

.comment_container {
   display: none;             /* Change to display: block; to show comments by default */
}

.a_comment {                  /* Individual comment */
   border: 1px solid #000;
   text-align: left;
   margin: 5px;
   padding: 5px;
}

.a_comment h3 {               /* Commenter's name */
   margin-top: 0;
   margin-bottom: 0;
   font-size: 14px;
}

.a_comment span {             /* Comment date */
   margin-bottom: 0;
   font-size: 12px;
   font-style: italic;
}

.a_comment p {                /* Comment text */
   margin-bottom: 0;
   font-size: 13px;
}

.blog_comment {               /* Add new comment text */
   text-align: left;
   font-size: 12px;
   margin-top: 10;
   margin-bottom: 5px;
   margin-left: 2px;
}

.add_blog_comment {           /* New comment form container */
   text-align: left;
   display: none;             /* Change to display: block; to show form by default */
}

.no_comments {                /* No comment text */
   text-align: left;
   font-size: 12px;
   padding: 0;
   margin-top: 5px;
   margin-left: 2px;
   margin-bottom: 5px;
}


/* ERROR-SUCCESS MESSAGES */

.comment_status_error {       /* Error posting comment text */
   /*border: 1px solid red;*/
   color: red;
   font-weight: bold;
   font-size: 17px;
   margin: 5px;
   /*background-color: #efefef;*/
}

.comment_status_success {     /* Successful comment text */
   /*border: 1px solid green;*/
   color: green;
   font-weight: bold;
   font-size: 17px;
   margin: 5px;
   /*background-color: #efefef;*/
}

 
/* FORMS */

/* Fix border on floated elements in IE */
.ie_cleardiv {
   display: block;
   clear: both;
   float: none;
   margin: 0;
   /*border: 1px dotted red;*/
}

.field-container {
   display: block;
   clear: both;
   margin-bottom: 6px;
   vertical-align: top;
}
.asterisk {
   color: red;
}

.instructions {
   margin-top: 0;
   color: #2e2e2e;
   font-family: Arial, helvetica, sans-serif;
   font-size: 13px;
   line-height: 1.1em !important;
   display: none;             /* Change to display: block; to display required text */
}

.myform-field_title-top,
.myform-field_title-left {
   font-size: 12px;
   font-weight: bold;
   font-family: Arial, helvetica, sans-serif;
   margin-bottom: 0;
   color: #000000;
   border-width: 1px;
   border-color: #ccc;
   border-style: hidden;
   width: 120px;
}
.myform-field_title-left {
   display: block;
   float: left;
   margin-right: 15px;
   /*margin-top: 12px;*/
   margin-top: 2px;
   text-align: left;
   /*border: 1px solid red;*/
}

.myform-field_title-hidden {
   display: none;
}

.myform-input_container, .myform-formfield_container {
   display: block;
   float: left;
   margin-top: 0;
   font-size: 11px;
}

.form_body_container {
   text-align: left;
   background-color: transparent;
   margin: 0;
   padding: 5;
   width: ;
   border-style: solid;
   border-width: 0px;
   border-color: F0F8FF;
   font-family: Arial, helvetica, sans-serif;
}

.userform-submit_btn-container {
   text-align: left;
}

.submit_btn {
   font-size: 13px;
   font-weight: bold;
}
