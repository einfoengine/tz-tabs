/*---------------------------------------------------------------
# Tz Tabs - Next generation tab module for joomla
# ---------------------------------------------------------------
# Author - ThemeZart http://www.themezart.Com
# Copyright (C) 2010 - 2012 ThemeZart.Com. All Rights Reserved.
# license - PHP files are licensed under  GNU/GPL V2
# license - CSS  - JS - IMAGE files  are Copyrighted material 
# Websites: http://www.themezart.Com
-----------------------------------------------------------------*/

jQuery.noConflict();

jQuery(document).ready(function(){
	showhide();	
	
	jQuery('#jform_params_content_source,#jform_params_transition_type,#jform_params_tabs_title_type').change(function() {showhide()});
	jQuery('#jform_params_content_source,#jform_params_transition_type,#jform_params_tabs_title_type').blur(function() {showhide()});
	
	function showhide(){
		if (jQuery("#jform_params_content_source").val()=="joomla") {
			jQuery("#jform_params_modules").parent().css("display", "none");
			jQuery("#jform_params_catid").parent().css("display", "block");
			jQuery("#jform_params_ordering").parent().css("display", "block");
			jQuery("#jform_params_user_id").parent().css("display", "block");
			jQuery("#jform_params_show_featured").parent().css("display", "block");
			
		} else {
			jQuery("#jform_params_modules").parent().css("display", "block");
			jQuery("#jform_params_catid").parent().css("display", "none");
			jQuery("#jform_params_ordering").parent().css("display", "none");
			jQuery("#jform_params_user_id").parent().css("display", "none");
			jQuery("#jform_params_show_featured").parent().css("display", "none");
			
		}
		if (jQuery("#jform_params_transition_type").val()=="fade") {
			jQuery("#jform_params_fadein_speed-lbl").parent().css("display", "block");
			jQuery("#jform_params_fadeout_speed-lbl").parent().css("display", "block");
		} else {
			jQuery("#jform_params_fadein_speed-lbl").parent().css("display", "none");
			jQuery("#jform_params_fadeout_speed-lbl").parent().css("display", "none");
			
		}
		if (jQuery("#jform_params_tabs_title_type").val()=="content") {
			jQuery("#jform_params_tabs_title_custom").parent().css("display", "none");
		} else {
			jQuery("#jform_params_tabs_title_custom").parent().css("display", "block");
			
		}
	}
	
	var empty =jQuery('#jform_params___field1-lbl');
	if (empty) empty.parent().remove();
});