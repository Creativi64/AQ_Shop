/*!
 * jQuery ListingSwitch
 *
 * @version 1.0.0
 *
 */
jQuery(document).ready(function () {
    var listing = jQuery(".product-listing-switch"),
        buttons = {},
        switcher,
        keks = {
            key: 'ListingSwitch',
            value: false
        },
        switchTo;

    if (listing.length) {
        switcher = jQuery(".listing-switch");
        if (switcher.length) {

            // init
            switcher.removeClass('hidden');
            keks.value = esseKeks(keks.key);
            switchTo = function (from, to) {
                listing.removeClass('product-listing-' + from);
                listing.addClass('product-listing-' + to).addClass('switch-active');
                keks.value = to;
            };
            buttons = {
                'v1': switcher.find(".v1"),
                'v2': switcher.find(".v2"),
                'all': switcher.find(".btn")
            };

            // switch to listing by cookie setting
            if (keks.value) {
                if (keks.value == 'v1') {
                    switchTo('v2', 'v1');
                } else if (keks.value == 'v2') {
                    switchTo('v1', 'v2');
                }
            }

            // highlight active button
            if (listing.hasClass('product-listing-v1')) {
                buttons.all.removeClass('active');
                buttons.v1.addClass('active');
            } else if (listing.hasClass('product-listing-v2')) {
                buttons.all.removeClass('active');
                buttons.v2.addClass('active');
            }

            // switch to listing by button press
            buttons.all.click(function () {
                var self = jQuery(this);
                if (!self.hasClass('active')) {
                    buttons.all.removeClass('active');
                    self.addClass('active');
                    if (self.hasClass('v1')) {
                        switchTo('v2', 'v1');
                    } else if (self.hasClass('v2')) {
                        switchTo('v1', 'v2');
                    }
                    equalizeListingHeights(".products");
                    backeKeks(keks.key, keks.value, 365);
                } else {
                    self.blur();
                }
            });
        }
    }
});
