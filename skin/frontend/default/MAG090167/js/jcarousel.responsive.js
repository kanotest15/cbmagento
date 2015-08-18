
jQuery = jQuery.noConflict(); 

(function(jQuery) {
    jQuery(function() {
        var jcarousel = jQuery('.jcarousel');

        jcarousel
            .on('jcarousel:reload jcarousel:create', function () {
                var carousel = jQuery(this),
                    width = carousel.innerWidth();

                /* if (width >= 600) {
                    width = width / 3;
                } else if (width >= 350) {
                    width = width / 1;
                } */
				width = 100;
                carousel.jcarousel('items').css('width', Math.ceil(width) + '%');
            })
            .jcarousel({
                wrap: 'circular',
        hoverPause: true,
            });

        jQuery('.jcarousel-control-prev')
            .jcarouselControl({
                target: '-=1'
            });

       jQuery('.jcarousel-control-next')
            .jcarouselControl({
                target: '+=1'
            });
//            $('.jcarousel').jcarouselAutoscroll({
//    autostart: false
//});

                       jQuery('.jcarousel').jcarouselAutoscroll({
    target: '-=1'
});
jQuery('.jcarousel').jcarouselAutoscroll({
    interval: 1000
});
jQuery('.jcarousel').hover(function() {
    $(this).jcarouselAutoscroll('stop');
}, function() {
   jQuery(this).jcarouselAutoscroll('start');
});
        jQuery('.jcarousel-pagination')
            .on('jcarouselpagination:active', 'a', function() {
                $(this).addClass('active');
            })
            .on('jcarouselpagination:inactive', 'a', function() {
                $(this).removeClass('active');
            })
            .on('click', function(e) {
                e.preventDefault();
            })
            .jcarouselPagination({
                perPage: 1,
                item: function(page) {
                    return '<a href="#' + page + '">' + page + '</a>';
                }
            });
    });
})(jQuery);
