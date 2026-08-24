(function($) {
    'use strict';
    // DOM Ready
    $(function() {
        var $backToTop = $('#back_to_top');

        if ( $backToTop.length ) {
            // Scroll to top on click
            $backToTop.on('click', function(e) {
                e.preventDefault();
                $('html, body').animate({ scrollTop: 0 }, 200);
            });
            // Toggle active class on scroll
            $(window).on('scroll', function() {
                if ( $(this).scrollTop() > 100 ) {
                    $backToTop.addClass('active');
                } else {
                    $backToTop.removeClass('active');
                }
            });
        }
    });

})(jQuery);
