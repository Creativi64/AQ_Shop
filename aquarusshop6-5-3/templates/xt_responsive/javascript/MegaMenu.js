/*!
 * Mega Menu
 *
 * @version 1.0.0
 *
 */
jQuery(document).ready(function () {
    var megaMenu = jQuery(".main-navigation");
    if (megaMenu.length) {
        var megaMenuContainer = jQuery(".navbar-mega"),
            withBackdropShadow = megaMenuContainer.hasClass('with-backdrop-shadow');

        if (megaMenuContainer.length) {

            // prevent dropdown toggle if container is clicked
            megaMenuContainer.click(function (e) {
                e.stopPropagation();
            });

            var megaMenuDropdown = megaMenu.find(".mega-dropdown");
            if (megaMenuDropdown.length) {
                var megaMenuContent,
                    body = jQuery("body");

                // prepare container
                megaMenuContainer
                    .addClass('row listing')
                    .empty();

                // add mega menu content
                megaMenuDropdown.on('show.bs.dropdown', function () {
                    var self = jQuery(this);

                    megaMenuContainer.empty();
                    megaMenuContent = self
                        .clone()
                        .find(".dropdown-menu > li");

                    if (typeof megaMenuContent != 'undefined' && megaMenuContent.length) {

                        megaMenuContent.each(function () {
                            var self = jQuery(this);

                            if (!self.hasClass('col')) {
                                self.addClass('col');

                                if (self.hasClass('static')) {
                                    self.addClass('col-md-12');
                                } else {
                                    self.not(":empty")
                                        .addClass('col-md-3 section');
                                }
                            }
                        });

                        megaMenuContainer
                            .removeClass('hidden')
                            .append(megaMenuContent);

                        equalizeListingHeights(".navbar-mega");
                        megaMenu.addClass('open');

                        body.addClass('navbar-mega-open');
                        if (withBackdropShadow) {
                            body.append('<div class="navbar-mega-overlay modal-backdrop fade in hidden-float-breakpoint"></div>');
                        }
                    }
                });

                // remove mega menu payload
                megaMenuDropdown.on('hidden.bs.dropdown', function () {
                    megaMenuContainer.addClass('hidden').empty();
                    megaMenu.removeClass('open');
                    body.removeClass('navbar-mega-open');
                    if (withBackdropShadow) {
                        jQuery(".navbar-mega-overlay").remove();
                    }
                    megaMenuContent = undefined;
                });
            }
        }
    }
});
