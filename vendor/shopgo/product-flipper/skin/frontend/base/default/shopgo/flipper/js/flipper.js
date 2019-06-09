window.hasOwnProperty = function (obj) {
    return (this[obj]) ? true : false;
};
if (!window.hasOwnProperty('Shopgo')) {
    Shopgo = {};
}

Shopgo.ProductFlipper = {
    getFlipperURL: "",
    imageContainer: "",
    effectType: "none",
    effectDuration: 500,
    preloader: 1,

    init: function () {
        Shopgo.ProductFlipper.initial();
        Shopgo.ProductFlipper.imageHover();
    },

    initial: function () {
        jQuery(Shopgo.ProductFlipper.imageContainer)
            .addClass('container_image')
            .attr('title', "");
        jQuery(".container_image img").each(
            function () {
                if (!jQuery(this).hasClass('flipper_image'))
                    jQuery(this).addClass('base_image');
            }
        );
    },

    imageHover: function () {
        jQuery(".container_image").on('mouseenter', function () {
            var product = jQuery(this);
            product.attr('data-flip', 'flip');

            if (product.children("img.flipper_image").length == 0) {
                var loading = product.attr('data-loading');
                if (loading != "loading" && loading != "loaded" && loading != "none") {
                    product.attr('data-loading', 'loading');
                    Shopgo.ProductFlipper.loadFlipperByProduct(product);
                }
                else if (product.attr('data-loading') == "loading") {
                    Shopgo.ProductFlipper.enablePreloader(product);
                }
            }
            else {
                Shopgo.ProductFlipper.flipper(product);
            }
        }).on('mouseleave', function () {
            var product = jQuery(this);
            product.attr('data-flip', 'base');
            Shopgo.ProductFlipper.disablePreloader(product);
            Shopgo.ProductFlipper.flipper(product);
        });
    },

    loadFlipperByProduct: function (product) {
        Shopgo.ProductFlipper.enablePreloader(product);

        var productId = Shopgo.ProductFlipper.getProductId(product);
        var width = product.children("img.base_image").width();
        var height = product.children("img.base_image").height();

        jQuery.ajax({
            url: Shopgo.ProductFlipper.getFlipperURL,
            type: "POST",
            dataType: 'json',
            data: "product_id=" + productId + "&width=" + width + "&height=" + height,
            success: function (result) {
                if (result.flipper) {
                    product.attr('data-flip-type', result.type);
                    Shopgo.ProductFlipper.loadImage(product, result.flipper);
                }
                else {
                    product.attr('data-loading', 'none');
                    Shopgo.ProductFlipper.disablePreloader(product);
                }
            },
            error: function () {
                Shopgo.ProductFlipper.disablePreloader(product);
            }
        });
    },

    loadImage: function (product, flipper) {
        var pic = new Image();
        pic.src = flipper;
        jQuery(pic).load(function () {
            product.append('<img class="flipper_image" style="display:none" src="' + flipper + '"/>');
            Shopgo.ProductFlipper.flipper(product);
            Shopgo.ProductFlipper.disablePreloader(product);
            jQuery(product).attr('data-loading', "loaded");
        });
    },

    getProductId: function (product) {
        var pid = 0;

        if (pid = jQuery(product).attr('data-product-id')) {
            if (parseInt(pid)) return pid;
        }

        var li = product.closest('li');
        var price = li.find("span[id*='product-price-']").attr("id");
        if (price)
            pid = price.replace("product-price-", "");
        if (!parseInt(pid)) {
            var cartUrl = li.find("button.btn-cart").attr("onclick");
            if (cartUrl)
                pid = cartUrl.replace(/.product\/(\d+)\/./g, '$1')
        }
        if (!parseInt(pid)) {
            var linkWishlist = li.find(".link-wishlist").attr("href");
            if (linkWishlist)
                pid = linkWishlist.replace(/.product\/(\d+)\/./g, '$1');
        }
        return pid;
    },

    flipper: function (product) {
        switch (product.attr('data-flip-type')) {
            case 'image':
                Shopgo.ProductFlipper.flipperImage(product);
                break;
        }
    },

    flipperImage: function (product) {
        switch (Shopgo.ProductFlipper.effectType) {
            case 'fade':
                Shopgo.ProductFlipper.effectFade(product);
                break;
            case 'none':
            default :
                Shopgo.ProductFlipper.effectNone(product);
        }
    },

    effectNone: function (product) {
        var flip = product.children("img.flipper_image");
        var base = product.children("img.base_image");
        if (base.attr('src') != "" && flip.attr('src') != "") {
            if (product.attr('data-flip') == 'flip') {
                flip.stop(true, true).fadeTo(0, 1);
                base.stop(true, true).fadeTo(0, 0);
            }
            else {
                base.stop(true, true).fadeTo(0, 1);
                flip.stop(true, true).fadeTo(0, 0);
            }
        }
    },

    effectFade: function (product) {
        var flip = product.children("img.flipper_image");
        var base = product.children("img.base_image");
        if (base.attr('src') != "" && flip.attr('src') != "") {
            if (product.attr('data-flip') == 'flip') {
                flip.stop(true, true).fadeTo(Shopgo.ProductFlipper.effectDuration, 1);
                base.stop(true, true).fadeTo(Shopgo.ProductFlipper.effectDuration, 0);
            }
            else {
                base.stop(true, true).fadeTo(Shopgo.ProductFlipper.effectDuration, 1);
                flip.stop(true, true).fadeTo(Shopgo.ProductFlipper.effectDuration, 0);
            }
        }
    },

    enablePreloader: function (product) {
        var preloader = jQuery(product).children(".shopgo_flipper_preloader");
        if (jQuery(preloader).length == 0) {
            product.append('<div class="shopgo_flipper_preloader" style="display:block;"><div>');
            Shopgo.ProductFlipper.centeringBlock(jQuery('.shopgo_flipper_preloader'), product);
        }
        jQuery(preloader).show();
    },

    disablePreloader: function (product) {
        if (Shopgo.ProductFlipper.preloader) {
            var preloader = jQuery(product).children(".shopgo_flipper_preloader");
            if (jQuery(preloader).length != 0) {
                jQuery(preloader).hide();
            }
        }
    },

    centeringBlock: function (block, parent) {
        if (Shopgo.ProductFlipper.preloader) {
            jQuery(block).css("top", jQuery(parent).height() / 2 - jQuery(block).height() / 2 + "px")
                .css("left", jQuery(parent).width() / 2 - jQuery(block).width() / 2 + "px");
        }
    }
};