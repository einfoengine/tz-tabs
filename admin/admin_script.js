/**
* @version 1.0.0
* @package Tz Module Widget
* @copyright (C) 2012 www.themezart.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

jQuery.noConflict();
jQuery(document).ready(function(fn){

        // Apply jquery UI Radio element style
    // Turn radios into btn-group
    fn('#jform_showtitle').addClass('btn-group');

    fn('.radio.btn-group label').addClass('btn');
    fn(".btn-group label:not(.active)").click(function() {
        var label = fn(this);
        var input = fn('#' + label.attr('for'));

        if (!input.prop('checked')) {
            label.closest('.btn-group').find("label").removeClass('active btn-danger btn-primary');
            if(input.val()== '') {
                    label.addClass('active btn-primary');
             } else if(input.val()==0) {
                    label.addClass('active btn-danger');
             } else {
            label.addClass('active btn-primary');
             }
            input.prop('checked', true);
        }
    });

    fn(".btn-group input[checked=checked]").each(function() {
        if(fn(this).val()== '') {
           fn("label[for=" + fn(this).attr('id') + "]").addClass('active btn-primary');
        } else if(fn(this).val()==0) {
           fn("label[for=" + fn(this).attr('id') + "]").addClass('active btn-danger');
        } else {
            fn("label[for=" + fn(this).attr('id') + "]").addClass('active btn-primary');
        }
    });

    //Bootsrap button on Joomla2.5 toolbar button
    fn('#toolbar li a').addClass('btn');

    // Bootstrap button for position
    var pos = fn('#jform_position-lbl').closest('li');
    pos.addClass('input-append').find('a').addClass('btn');

    // Boostraped alert message
    fn('#system-message ul li').addClass('alert alert-info');

    //Chosen Multiple selector
    fn(".chzn-select").chosen();

    fn('.cs-list a').popover({
        placement : 'right'
    });	


    //remove label li and push it to previous element
    fn('div.remove-lbl').each(function(){
       var content = fn(this);
        //push it to previous li
        fn(this).closest('li')
            .prev()
            .append(content);
        //remove paren li
        fn(this).closest('li').next().remove();
    });

	
});
    
