$j(document).ready(function() {
    (function(updateImage) {
        ConfigurableMediaImages.updateImage = function (el) {
            var select = $j(el);
            var label = select.find('option:selected').attr('data-label');
            var productId = optionsPrice.productId;

            //find all selected labels
            var selectedLabels = new Array();

            $j('.product-options .super-attribute-select').each(function() {
                var $option = $j(this);

                if ($option.val() != '') {
                    selectedLabels.push($option.find('option:selected').attr('data-label'));
                }
            });


            var swatchImageUrl = ConfigurableMediaImages.getSwatchImage(productId, label, selectedLabels);
            if (ConfigurableMediaImages.isValidImage(swatchImageUrl)) {
                var swatchImage = ConfigurableMediaImages.getImageObject(productId, swatchImageUrl);

                ProductMediaManager.swapImage(swatchImage);
            }

            var pid = ConfigurableMediaImages.getSwatchProdId(productId, label, selectedLabels);

            if (!pid) {
                selectedLabels = new Array(selectedLabels[0]);
                var pid = ConfigurableMediaImages.getSwatchProdId(productId, label, selectedLabels);
            }

            if (!pid) {
                return false;
            }

            var additionalInfo = '';
            if ($j('#product-attribute-specs-table').length > 0) {
                    additionalInfo = '&additional_info=1';
            }

            $j.ajax({
                url: posturl + 'ajaxswatches/ajax/update',
                dataType: 'json',
                type : 'post',
                data: 'pid=' + pid + additionalInfo,
                success: function(data) {
                    if (data) {
                        ConfigurableMediaImages.showProductData(data);
                    } else {
                        return true;
                    }
                }
            });
        };
    }(ConfigurableMediaImages.updateImage));

    // extending the default getSwatchImage() function to get a fall-back PID when
    // more then 1 attribute is clicked and no match is found
    (function(getSwatchImage) {
        ConfigurableMediaImages.getSwatchImage = function(productId, optionLabel, selectedLabels) {
            var fallback = ConfigurableMediaImages.productImages[productId];
            if (!fallback) {
                return null;
            }
            //console.log(selectedLabels);

            //first, try to get label-matching image on config product for this option's label
            var currentLabelImage = fallback['option_labels'][optionLabel];
            if (currentLabelImage && fallback['option_labels'][optionLabel]['configurable_product'][ConfigurableMediaImages.imageType]) {
                //found label image on configurable product
                return fallback['option_labels'][optionLabel]['configurable_product'][ConfigurableMediaImages.imageType];
            }

            var compatibleProducts = ConfigurableMediaImages.getCompatibleProductImages(fallback, selectedLabels);

            //Wigman: try to get a fallback PID when no match found
            if (compatibleProducts.length == 0) { //no compatible products
                selectedLabels = new Array(selectedLabels[0]);
                var compatibleProducts = ConfigurableMediaImages.getCompatibleProductImages(fallback, selectedLabels);
            }

            //Wigman: this is the original 'bail' when no PIDs found
            if (compatibleProducts.length == 0) { //no compatible products
                return null; //bail
            }

            //second, get any product which is compatible with currently selected option(s)
            $j.each(fallback['option_labels'], function(key, value) {
                var image = value['configurable_product'][ConfigurableMediaImages.imageType];
                var products = value['products'];

                if (image) { //configurable product has image in the first place
                    //if intersection between compatible products and this label's products, we found a match
                    var isCompatibleProduct = ConfigurableMediaImages.arrayIntersect(products, compatibleProducts).length > 0;
                    if (isCompatibleProduct) {
                        return image;
                    }
                }
            });

            //third, get image off of child product which is compatible
            var childSwatchImage = null;
            var childProductImages = fallback[ConfigurableMediaImages.imageType];
            compatibleProducts.each(function(productId) {
                if (childProductImages[productId] && ConfigurableMediaImages.isValidImage(childProductImages[productId])) {
                    childSwatchImage = childProductImages[productId];
                    return false; //break "loop"
                }
            });
            if (childSwatchImage) {
                return childSwatchImage;
            }

            //fourth, get base image off parent product
            if (childProductImages[productId] && ConfigurableMediaImages.isValidImage(childProductImages[productId])) {
                return childProductImages[productId];
            }

            //no fallback image found
            return null;
        };
    }(ConfigurableMediaImages.getSwatchImage));
});

ConfigurableMediaImages.ajaxInit = function(jsons) {
    ConfigurableMediaImages.init('small_image');

    for (var key in jsons) {
        ConfigurableMediaImages.setImageFallback(key, $j.parseJSON(jsons[key]));
    }

    $j(document).trigger('configurable-media-images-init', ConfigurableMediaImages);
}

ConfigurableMediaImages.showProductData = function(data) {
    if (data.name) {
        $j('.product-name > h1').html(data.name);
    }
    if (data.short_description) {
        $j('.short-description > div.std').html(data.short_description);
    }
    if (data.description) {
        $j('#product_tabs_description_tabbed_contents > div.std').html(data.description);
    }
    if (data.additional_info && $j('#product-attribute-specs-table').length > 0) {
        $j('#product-attribute-specs-table tbody').html(data.additional_info);
        decorateTable('product-attribute-specs-table');
    }
    if (data.images) {
        ConfigurableMediaImages.setMoreImages(data.images);
    }
}

ConfigurableMediaImages.setMoreImages = function(data) {
    var newImages = Array();
    var maxId = 0;

    var thumblist = $j('.product-image-thumbs');
    var gallery   = $j('.product-image-gallery');

    thumblist.find('li').each(function() { //removing current thumbs and large images
        $j('#image-'+$j(this).find('a').data('image-index')).remove();
        $j(this).remove();
    });

    $j.each(data, function(key, value) { //adding new images
        maxId++;

        thumblist.append('<li><a class="thumb-link" href="#" title data-image-index="'+maxId+'"><img src="'+value['thumb']+'" width="75" height="75" alt=""></a></li>');
        gallery.append('<img id="image-'+maxId+'" class="gallery-image" src="'+value['image']+'" data-zoom-image="'+value['image']+'">');
    });
    ProductMediaManager.wireThumbnails();
}

ConfigurableMediaImages.getSwatchProdId = function(productId, optionLabel, selectedLabels) {
    var fallback = ConfigurableMediaImages.productImages[productId];
    if (!fallback) {
        return null;
    }

    var compatibleProducts = ConfigurableMediaImages.getCompatibleProductImages(fallback, selectedLabels);

    if (compatibleProducts.length == 0) { //no compatible products
        return null; //bail
    }

    var childSwatchProdId = null;
    var childProductImages = fallback[ConfigurableMediaImages.imageType];
    compatibleProducts.each(function(productId) {
        if (childProductImages[productId] && ConfigurableMediaImages.isValidImage(childProductImages[productId])) {
            childSwatchProdId = productId;
            return false; //break "loop"
        }
    });
    if (childSwatchProdId) {
        return childSwatchProdId;
    }

    //fourth, get base image off parent product
    if (childProductImages[productId] && ConfigurableMediaImages.isValidImage(childProductImages[productId])) {
        return productId;
    }

    //no prodId found
    return null;
}
