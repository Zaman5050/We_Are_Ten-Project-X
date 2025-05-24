
(function($) {
    "use strict";

    /*================================
    Preloader
    ==================================*/

    var preloader = $('#preloader');
    $(window).on('load', function() {
        setTimeout(function() {
            preloader.fadeOut('slow', function() { $(this).remove(); });
        }, 300)
    });

    /*================================
    sidebar collapsing
    ==================================*/
    if (window.innerWidth <= 1364) {
        $('.page-container').addClass('sbar_collapsed');
        $('.header-area').addClass('sbar_collapsed');
    }
    $('.nav-btn').on('click', function() {
        $('.header-area').toggleClass('sbar_collapsed');
        $('.page-container').toggleClass('sbar_collapsed');
        
    });

    

   

    

   

   

    


    

   

})(jQuery);


