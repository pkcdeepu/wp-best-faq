/*
WP BEST FAQ JS
--------------------------

PLEASE DO NOT make modifications to this file directly as it will be overwritten on update.
Instead, save a copy of this file to your theme directory. It will then be loaded in place
of the plugin's version and will maintain your changes on upgrade
*/
jQuery(document).ready( function(){
    // This looks at the initial state of each content area, and hide content areas that are closed
    jQuery('.best-faq-content').each( function() {
        if( jQuery(this).hasClass('faq-closed')) {
            jQuery(this).hide();
        }
    });

    // This runs when a Toggle Title is clicked. It changes the CSS and then runs the animation
    jQuery('.best-faq-title').each( function() {
        jQuery(this).click(function() {
            var toggleContent = jQuery(this).next('.best-faq-content');

            jQuery(this).toggleClass('faq-open').toggleClass('faq-closed');
            toggleContent.toggleClass('faq-open').toggleClass('faq-closed');
            toggleContent.slideToggle();
        });
    });

    // If the user sets the style to "accordion"
  /*  jQuery('.best-faq-accordion-wrap').accordion( {
        collapsible: true,
        active: false,
        heightStyle: "content"
    });*/
});