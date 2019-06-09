/**
 * Product Grid class, primary used to align elements
 * @constructor
 * @param config.elementsSelector - The class used for each child item to be paged, defaults to ".product-grid li"
 * @param config.parentSelector - The parents of the content to align defaults to ".products-grid li",
 * @param config.alignParentElements - Also align the parent items (as selected by config.parentSelector)
 * @param config.selectorsToAlign - The selectors of the items to align defaults to ['.product-name a', '.ratings', '.item-content']
 */

;'use strict';

(function () {

    if (!window.Meanbee) {
        window.Meanbee = {};
    }

    var ProductGrid = Class.create({
        initialize: function(options) {
            this.config = Object.extend({
                elementsSelector: ".products-grid",
                parentSelector: ".products-grid li",
                alignParentElements: true,
                offsetTopAttribute: "data-offset-top",
                selectorsToAlign: ['.product-name a', '.ratings', '.item-content']
            }, options || {} );

            this.elements = $$(this.config.elementsSelector);

            this.resizeTimeout = 1000;
            this.resizeTimeoutId = null;

            // Align elements on initialisation and then each time the window is resized.
            this.alignElements();
            this.numberElementColumns();

            Event.observe(window, 'resize', this.handleResize.bind(this));
            Event.observe(document, 'meanbee:infinite_scroll_load_complete', this.alignElements.bind(this));
            Event.observe(document, 'meanbee:infinite_scroll_load_complete', this.numberElementColumns.bind(this));
        },

        handleResize: function() {
            var self = this;
            self.resetStyles();
            if (self.resizeTimeoutId) {
                clearTimeout(self.resizeTimeoutId);
            }
            self.resizeTimeoutId = setTimeout(function() {
                // clear out the cached values
                $$(self.config.parentSelector).each(function(el) {
                    el.removeAttribute(self.config.offsetTopAttribute);
                });

                self.alignElements();
                self.resizeTimeoutId = null;
            }, self.resizeTimeout);
        },

        /**
         * Put column identifiers on the elements.
         */
        numberElementColumns: function () {
            if (Meanbee.DecorateColumns) {
                Meanbee.DecorateColumns($$(this.config.parentSelector), 3, 3);
            }
        },

        /**
         * Align product name links in their rows
         */
        alignElements: function() {
            var self = this;
            self.resetStyles();

            self.elements.each(function(el) {
                self.config.selectorsToAlign.each(function(selector) {
                    self.alignChildElements(el, selector);
                });

                if (self.config.alignParentElements) {
                    self.alignParentElements();
                }
            });

        },

        alignParentElements: function() {
            var self = this;
            var elements = $$(self.config.parentSelector);

            var max_height = 0;
            var rowStart = 0;
            var offsetTop = elements[0].offsetTop;

            if (elements.length == 1) {
                max_height = elements[0].getHeight();
            }

            elements.each(function(el, index) {
                var isLast = (index == (elements.length - 1));
                if (!el.getAttribute(self.config.offsetTopAttribute)) {
                    el.setAttribute(self.config.offsetTopAttribute, el.offsetTop);
                }
                var elOffsetTop = el.getAttribute(self.config.offsetTopAttribute);
                // If we're on the same row, keep check of the tallest
                if (elOffsetTop == offsetTop && !isLast) {
                    var height = el.getHeight();
                    if (height > max_height) {
                        max_height = height;
                    }

                    // If we're on the last item in the grid, handle setting the heights
                }  else if (isLast) {


                    /* There is an edge case where our row can look like this
                     * 1 2 3
                     * 4
                     *
                     * Now this logic will set all four elements to the same size, despite the fact that 4 is on a different
                     * row. We add a check in here to see if it is.
                     */
                    var rowEnd = index;
                    if (elOffsetTop != offsetTop) {
                        // el is on a different row to the others.
                        rowEnd = rowEnd - 1;
                        el.setStyle({height: el.getHeight() + 'px'});
                    }

                    // Make sure we check if the last item is the tallest.
                    var height = el.getHeight();
                    if (height > max_height) {
                        max_height = height;
                    }

                    // Loop through row and set the max height.
                    for (var j = rowStart; j <= rowEnd; j++) {
                        $(elements[j]).setStyle({height: max_height + "px"});
                    }

                    // Otherwise when we start a new row, set the heights on the previous row.
                } else {

                    // Loop through all elements on a row and set the max height
                    for (var j = rowStart; j < index; j++) {
                        $(elements[j]).setStyle({height: max_height + "px"});
                    }

                    // Change offset top to match new row
                    offsetTop = el.offsetTop;

                    // Set the maximum height of the row as this element's height as it's the first
                    max_height = el.getHeight();

                    // Reset the rowstart so we only alter heights of one row when looping through
                    rowStart = index;
                }
            });
        },

        alignChildElements: function(el, selector) {
            var self = this;
            var children = el.select(selector);

            /*
             * This javascript iterates over the products at the same top offset
             * and ensure that they have the same height.
             */
            var max_height = 0;
            var rowStart = 0;
            var offsetTop = children[0].up(self.config.parentSelector).offsetTop;

            if (children.length == 1) {
                max_height = children[0].getHeight();
            }

            children.each(function(child, index) {
                var isLast = (index == (children.length - 1));

                // If we're on the same row, keep check of the tallest
                var parentItem = child.up(self.config.parentSelector);
                if (!parentItem.getAttribute(self.config.offsetTopAttribute)) {
                    parentItem.setAttribute(self.config.offsetTopAttribute, parentItem.offsetTop);
                }
                var parentItemOffsetTop = parentItem.getAttribute(self.config.offsetTopAttribute);

                if (parentItemOffsetTop == offsetTop && !isLast) {
                    var height = $(child).getHeight();
                    if (height > max_height) {
                        max_height = height;
                    }

                    // If we're on the last item in the grid, handle setting the heights
                }  else if (isLast) {

                    /* There is an edge case where our row can look like this
                     * 1 2 3
                     * 4
                     *
                     * Now this logic will set all four elements to the same size, despite the fact that 4 is on a different
                     * row. We add a check in here to see if it is.
                     */
                    var rowEnd = index;
                    if (parentItemOffsetTop != offsetTop) {
                        // el is on a different row to the others.
                        rowEnd = rowEnd - 1;
                        child.setStyle({height: child.getHeight() + 'px'});
                    }

                    // Make sure we check if the last item is the tallest.
                    var height = $(child).getHeight();
                    if (height > max_height) {
                        max_height = height;
                    }

                    // Loop through row and set the max height.
                    for (var j = rowStart; j <= rowEnd; j++) {
                        $(children[j]).setStyle({height: max_height + "px"});
                    }

                    // Otherwise when we start a new row, set the heights on the previous row.
                } else {

                    // Loop through all elements on a row and set the max height
                    for (var j = rowStart; j < index; j++) {
                        $(children[j]).setStyle({height: max_height + "px"});
                    }

                    // Change offset top to match new row
                    offsetTop = parentItemOffsetTop;

                    // Set the maximum height of the row as this element's height as it's the first
                    max_height = $(child).getHeight();

                    // Reset the rowstart so we only alter heights of one row when looping through
                    rowStart = index;
                }

            });
        },

        /**
         * Remove all inline styles.
         */
        resetStyles: function() {
            var self = this;

            var parents = $$(self.config.parentSelector);
            parents.each(function(el) {
                el.removeAttribute('style');
            })

            self.elements.each(function(el) {
                self.config.selectorsToAlign.each(function(selector) {
                    var children = el.select(selector);

                    children.each(function(child) {
                        $(child).removeAttribute('style');
                    });
                });
            });
        }
    });

    window.Meanbee.ProductGrid = ProductGrid;
})();