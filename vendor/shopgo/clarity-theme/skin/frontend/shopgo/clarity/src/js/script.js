jQuery.noConflict();
;(function($) {
    'use strict';

    function Site(settings) {

        this.windowLoaded = false;

    }

    Site.prototype = {
        constructor: Site,

        start: function() {
            var me = this;

            $(window).load(function() {
                me.windowLoaded = true;
            });

            this.attach();
        },

        attach: function() {
            this.attachBootstrapPrototypeCompatibility();
            this.attachMedia();
        },

        attachBootstrapPrototypeCompatibility: function() {

            // Bootstrap and Prototype don't play nice, in the sense that
            // prototype is a really wacky horrible library. It'll
            // hard-code CSS to hide an element when a hide() event
            // is fired. See http://stackoverflow.com/q/19139063
            // To overcome this with dropdowns that are both
            // toggle style and hover style, we'll add a CSS
            // class which has "display: block !important"
            $('*').on('show.bs.dropdown show.bs.collapse', function(e) {
                $(e.target).addClass('bs-prototype-override');
            });

            $('*').on('hidden.bs.collapse', function(e) {
                $(e.target).removeClass('bs-prototype-override');
            });
        },

        attachMedia: function() {
            var $links = $('[data-toggle="media"]');
            if ( ! $links.length) return;

            // When somebody clicks on a link, slide the
            // carousel to the slide which matches the
            // image index and show the modal
            $links.on('click', function(e) {
                e.preventDefault();

                var $link = $(this),
                   $modal = $($link.attr('href')),
                $carousel = $modal.find('.carousel'),
                    index = parseInt($link.data('index'));

                $carousel.carousel(index);
                $modal.modal('show');

                return false;
            });
        }
    };

    jQuery(document).ready(function($) {
        var site = new Site();
        site.start();
    });

    jQuery(document).ready(function() {  
        var stickyNavTop = jQuery('.nav-container').offset().top;  
          
        var stickyNav = function(){  
        var scrollTop = jQuery(window).scrollTop();  
               
        if (scrollTop > stickyNavTop && jQuery("body").height() > 1200) {   
            jQuery('.nav-container').addClass('sticky-nav');  
            jQuery('.logo-sticky').addClass('logo-sticky-visible');
            jQuery('.logo-nohome-sticky').addClass('logo-sticky-visible');
        } else {  
            jQuery('.nav-container').removeClass('sticky-nav');   
            jQuery('.logo-sticky').removeClass('logo-sticky-visible');
            jQuery('.logo-nohome-sticky').removeClass('logo-sticky-visible');
        }  
        };  
        stickyNav();  
          
        jQuery(window).scroll(function() {  
            stickyNav();  
        });  
    });  

    jQuery(document).ready(function() {  
        
        // Product page quantity increase/decrease 
        jQuery(".quantity").append('<i id="add1" class="plus fa fa-plus" />').prepend('<i id="minus1" class="minus fa fa-minus" />');
        
        jQuery('<i id="add1" class="plus fa fa-plus" />').insertAfter(".grouped-items-table input.qty");
        jQuery('<i id="minus1" class="minus fa fa-minus" />').insertBefore(".grouped-items-table input.qty");

        jQuery(".plus").click(function()
        {
           var currentVal = parseInt(jQuery(this).parent().find(".qty").val());
           if (!currentVal || currentVal=="" || currentVal == "NaN") currentVal = 0;
           jQuery(this).parent().find(".qty").val(currentVal + 1);
        });

        jQuery(".minus").click(function()
        {
           var currentVal = parseInt(jQuery(this).parent().find(".qty").val());
           if (currentVal == "NaN") currentVal = 0;
           if (currentVal > 0)
           {
               jQuery(this).parent().find(".qty").val(currentVal - 1);
           }
        });


        //Top search
        
            jQuery("#search_mini_form .search-wrapper").click(function(event){
                event.stopPropagation();
                if(!jQuery(".menu-search .form-search").hasClass('active')){
                    jQuery(".menu-search .form-search").addClass('active');
                } 
                jQuery(document).click(function(e) {
                    if(jQuery(".menu-search .form-search").hasClass('active')){
                        jQuery(".menu-search .form-search").removeClass('active');
                    }
                });
            })
            jQuery(".menu-search").find("button").click(function(e){
                if(jQuery(".menu-search .form-search").hasClass('active')){
                } else {
                    e.preventDefault();
                }
            })


        if (!!navigator.userAgent.match(/Trident\/7\./)) {
            jQuery('body').addClass('ie-target');
        }
        if (navigator.userAgent.indexOf('Safari') != -1 && navigator.userAgent.indexOf('Chrome') == -1) {
            jQuery('body').addClass('safari-target');
        }

        if (navigator.userAgent.toLowerCase().indexOf('chrome') > -1) {
            jQuery('body').addClass('chrome-target');
        }


        // Shopbybrands block
        if (jQuery('.shopbybrands-block .brands-item a').find('span').length) {
            jQuery(".brands-list").addClass('aw-brandname');
        }    
        if (!jQuery('.shopbybrands-block .brands-item a').find('img').length) {
            jQuery(".brands-list").addClass('aw-brandname');
        }

        if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
            jQuery(".quickview-wrap").addClass('mobile-hide');
        }

    });

    jQuery(document).bind('m-ajax-after', function (e, selectors, url, action) {
        Shopgo.ProductFlipper.init();
    });


})(jQuery);
