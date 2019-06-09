(function($) {
    $(window).load(function() {
        var defaults = {
            easing: 'swing',
            slideshowSpeed: 7000,
            slideshow: true,
            animationSpeed: 600
        };

        if (typeof shopgoBannerslider != 'undefined' && shopgoBannerslider.enabled) {
                var timeout = shopgoBannerslider.timeout
                              && (shopgoBannerslider.timeout != defaults.slideshowSpeed);
                var speed   = shopgoBannerslider.speed
                              && (shopgoBannerslider.speed != defaults.animationSpeed);
                switch (shopgoBannerslider.type) {
                    case 'basic':
                        $('#banner-slider-b').flexslider({
                            animation: shopgoBannerslider.animation,
                            easing: shopgoBannerslider.easing,
                            animationLoop: shopgoBannerslider.loop,
                            slideshowSpeed: timeout ? shopgoBannerslider.timeout : defaults.slideshowSpeed,
                            slideshow: timeout ? false : true,
                            animationSpeed: speed ? shopgoBannerslider.speed : defaults.animationSpeed,
                            pauseOnHover: shopgoBannerslider.pause
                        });
                        break;
                    case 'thumb':
                        $('#banner-carousel-t').flexslider({
                            animation: shopgoBannerslider.animation,
                            easing: shopgoBannerslider.easing,
                            controlNav: false,
                            animationLoop: shopgoBannerslider.loop,
                            slideshowSpeed: timeout ? shopgoBannerslider.timeout : defaults.slideshowSpeed,
                            slideshow: timeout ? false : true,
                            animationSpeed: speed ? shopgoBannerslider.speed : defaults.animationSpeed,
                            pauseOnHover: shopgoBannerslider.pause,
                            itemWidth: 210,
                            itemMargin: 5,
                            asNavFor: '#banner-slider-t'
                        });
                        $('#banner-slider-t').flexslider({
                            animation: shopgoBannerslider.animation,
                            easing: shopgoBannerslider.easing,
                            animationLoop: shopgoBannerslider.loop,
                            slideshowSpeed: timeout ? shopgoBannerslider.timeout : defaults.slideshowSpeed,
                            slideshow: timeout ? false : true,
                            animationSpeed: speed ? shopgoBannerslider.speed : defaults.animationSpeed,
                            pauseOnHover: shopgoBannerslider.pause,
                            controlNav: false,
                            sync: '#banner-carousel-t'
                        });
                        break;
                    case 'thumb_cvp':
                        $('#banner-slider-tcvp').flexslider({
                            animation: shopgoBannerslider.animation,
                            easing: shopgoBannerslider.easing,
                            animationLoop: shopgoBannerslider.loop,
                            slideshowSpeed: timeout ? shopgoBannerslider.timeout : defaults.slideshowSpeed,
                            slideshow: timeout ? false : true,
                            animationSpeed: speed ? shopgoBannerslider.speed : defaults.animationSpeed,
                            pauseOnHover: shopgoBannerslider.pause,
                            controlNav: 'thumbnails'
                        });
                        break;
                }
        }
    });
})(jQuery);
