tinyMCE.importPluginLanguagePack('Addfontz');var TinyMCE_AddfontzPlugin={getInfo:function(){return{longname:'Addfontz',author:'Moxiecode Systems AB',authorurl:'http://tinymce.moxiecode.com',infourl:'http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/Addfontz',version:tinyMCE.majorVersion+"."+tinyMCE.minorVersion}},getControlHTML:function(cn){switch(cn){case"Addfontz":return tinyMCE.getButtonHTML(cn,'lang_Addfontz_desc','{$pluginurl}/images/addfontz.gif','mceAddFontz')}return""},execCommand:function(editor_id,element,command,user_interface,value){switch(command){case"mceAddFontz":var template=new Array();template['file']='../../plugins/Addfontz/camsuperfonts.php';template['width']=790;template['height']=470;template['width']+=tinyMCE.getLang('lang_Addfontz_delta_width',0);template['height']+=tinyMCE.getLang('lang_Addfontz_delta_height',0);tinyMCE.openWindow(template,{editor_id:editor_id,inline:"yes"});return true}return false}};tinyMCE.addPlugin('Addfontz',TinyMCE_AddfontzPlugin);