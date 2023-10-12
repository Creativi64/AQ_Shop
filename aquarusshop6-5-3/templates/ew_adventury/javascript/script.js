//
// For each shop client you could create its own javascript file
// script_[shop_id].js
// @example for shop client id 1 name it 'script_1.js'
//

/*******************************************************************************
 * ON DOCUMENT READY
 */
jQuery(document).ready(function () {
    setTimeout(function () {
        var isSafari = 0;
        if (navigator.userAgent.indexOf('Safari') != -1 && navigator.userAgent.indexOf('Chrome') == -1) {
            isSafari = 1;
        }

        /**
         * Edit address modal
         */
        (function ($) {
            var trigger = $('.edit-address-trigger');
            trigger.click(function (event) {
                event.preventDefault();

                var self = $(this);
                var link = self.attr('href');
                var modal = $('#edit-address-modal');
                var modalBody = modal.find('.modal-body');

                modalBody.find('.loader').removeClass('hidden');
                modalBody.find('.content').html('');
                modal.modal({ show: true });

                $.get(link + '#container', function (data) {
                    var dom = $(data);
                    dom.find('h1').remove();
                    modalBody
                        .find('.loader')
                        .addClass('hidden');
                    modalBody
                        .find('.content')
                        .html(dom.find('#edit-adress').html() + '<div class="clearfix"></div>');
                });
            });
        })(jQuery);

        /**
         * Listing Switch
         */
        var listingSwitch = {
            _cache: {},
            conf: {
                storageKey: 'listingSwitch'
            },
            selector: {
                buttons: '.listing-switch .btn',
                productListing: '.product-listing',
                listingSwitchArea: '.listing-switch-area'
            },
            cachedObj: function (type) {
                return typeof this._cache[type] === 'undefined' ?
                    jQuery(this.selector[type]) :
                    this._cache[type];
            },
            buttons: function () {
                return this.cachedObj('buttons');
            },
            productListing: function () {
                return this.cachedObj('productListing');
            },
            listingSwitchArea: function () {
                return this.cachedObj('listingSwitchArea');
            },
            setCurrent: function (version) {
                this.productListing()
                    .data('listing-switch-current', version)
                    .attr('data-listing-switch-current', version);

                if (typeof sessionStorage !== 'undefined') {
                    sessionStorage.setItem(this.conf.storageKey, version);
                }
            },
            current: function () {
                var stored = false;
                if (typeof sessionStorage !== 'undefined') {
                    stored = sessionStorage.getItem(this.conf.storageKey) || false;
                }
                return stored && (stored === 'v1' || stored === 'v2') ?
                    stored :
                    this.productListing().data('listing-switch-current') || false;
            },
            enabled: function () {
                return this.buttons().length &&
                    this.productListing().length &&
                    this.listingSwitchArea().length &&
                    this.current().length
            },
            activate: function (version, element, cb) {
                var self = this;
                cb = cb || function () {};
                setTimeout(function () {
                    // products
                    self.listingSwitchArea()
                        .hide()
                        .parent()
                        .find(self.selector.listingSwitchArea + '-' + version)
                        .show();

                    // listing
                    self.productListing()
                        .removeClass('product-listing-v1 product-listing-v2')
                        .addClass('product-listing-' + version);
                    var listing = self.productListing()
                        .find('.listing');
                    if (version === 'v2') {
                        listing.addClass('equalize-nothing no-image-height-helper')
                    } else {
                        listing.removeClass('equalize-nothing no-image-height-helper')
                    }

                    // button
                    element = element || self.buttons().parent().find('.btn-' + version);
                    self.buttons().removeClass('active');
                    element.addClass('active');

                    // storage
                    self.setCurrent(version);

                    // callback
                    cb();
                }, 0);
            },
            handler: function (cb) {
                var self = this;
                setTimeout(function () {
                    if (self.enabled()) {
                        self.activate(self.current(), null, function () {
                            self.buttons().click(function () {
                                var element = jQuery(this);
                                var wanted = element.data('listing-switch-wanted');
                                if (wanted) {
                                    self.activate(wanted, element, function () {
                                        equalizeListingHeights();
                                    });
                                }
                            });
                            cb(true);
                        });
                    } else {
                        cb(false);
                    }
                }, 0);
            }
        };
        listingSwitch.handler(function (success) {
            if (listingSwitch.buttons().length && !success) {
                listingSwitch.buttons().hide();
                console.error('Could not initialize listing switch');
            }
        });

        /**
         * MS OPTIONS
         */
        var msOptionsSelector = jQuery("#ms-options");
        if (msOptionsSelector.length != 0) {
            msOptionsSelector.find(".default_option").addClass('preloader');
            msOptionsSelector.find("select").addClass('form-control');
        }

        /**
         * BOOTSTRAP SELECT
         * @see http://silviomoreto.github.io/bootstrap-select/
         */
        if (!MSIE || MSIE > 8) {
            jQuery("select").addClass('show-menu-arrow');
            var bsSelctor = "select:not(#countries select):not(#default_address_customers_federal_state_code):not(#customers_federal_state_code)";
            jQuery(bsSelctor).selectpicker();
            if (isMobileDevice()) {
                jQuery(bsSelctor).selectpicker('mobile');
            }

            //click tracker
            jQuery(".bootstrap-select").click(function () {
                jQuery(this).prev("select").addClass('clicked');
            });
        }

        /**
         * PRINT DOCKED SIDENAVIGATION OBJECTS
         */
        if (typeof CONFIG_EW_ADVENTURY_PLUGIN_SIDEBUTTONS != 'undefined' &&
            CONFIG_EW_ADVENTURY_PLUGIN_SIDEBUTTONS === true) {

            var floatingBox = jQuery("#floating-box-container");
            var sideNavigationArea = floatingBox.find(".custom-floating-boxes");
            if (sideNavigationArea.length !== 0) {
                jQuery("[data-orientation-nav-url][data-orientation-nav-classes][data-orientation-nav-icon][data-orientation-nav-label]")
                    .each(function (i) {
                        sideNavigationArea.append('<a href="' + jQuery(this).data('orientation-nav-url') + '" title="' + jQuery(this).data('orientation-nav-label') + '" class="obj-' + i + ' ' + jQuery(this).data('orientation-nav-classes') + '"><span class="floating-box">' + jQuery(this).data('orientation-nav-icon') + '</span></a>');
                    });
                floatingBox.css({"margin-top": -(floatingBox.outerHeight() / 2) + "px"});
            }
        }

        /**
         * ANCHOR ANIMATION
         * back to top
         */
        jQuery(".backtotop").click(function(e){
            e.preventDefault();
            jQuery('html,body').animate({scrollTop:0}, 500);
        });

        /**
         * ANCHOR ANIMATION
         * slide to id by hash
         */
        jQuery("a[href*=\\#].move").bind("click", function(e) {
            z = jQuery(this).get(0).hash;
            o = jQuery(z);
            if (o.length != 0) {
                e.preventDefault();
                jQuery('html,body').animate({
                    scrollTop : o.offset().top - 80
                }, 500);
            }
        });

        /**
         * BUTTONS LOADING ANIMATION
         * @see http://msurguy.github.io/ladda-bootstrap/
         */
        if (!MSIE || MSIE > 7) {
            var LaddaElements = ".ladda-button, .preloader";
            var LaddaElementsSelector = jQuery(LaddaElements);
            if (LaddaElementsSelector.length != 0) {
                LaddaElementsSelector.each(function () {
                    jQuery(this).addClass("ladda-button").attr('data-style', 'zoom-in');
                    if (jQuery(this).hasClass('btn-default')) {
                        jQuery(this).attr('data-spinner-color', '#000');
                    } else {
                        jQuery(this).attr('data-spinner-color', '#fff');
                    }
                    jQuery(this).attr('data-style', 'slide-up');
                });
                Ladda.bind(LaddaElements);
            }
        }

        /**
         * LOGIN PASSWORD TOGGLE
         */
        jQuery("#account-button").click(function () {
            jQuery("#guest-account").show();
        });
        jQuery("#guest-button").click(function () {
            jQuery("#guest-account").hide();
        });

        /**
         * FORM REQUIRED MARK
         */
        jQuery("label:contains('\*')").html(function (_, html) {
            return html.replace(/(\*)/g, '<span class="required">$1</span>');
        });

        /**
         * CHECK SEARCH INPUT
         */
        var searchFormSelector = jQuery(".search-box-form");
        searchFormSelector.find(".submit-button").click(function (e) {
            if (jQuery.trim(searchFormSelector.find(".keywords").val()) == '') {
                searchFormSelector.find(".keywords").focus();
                e.preventDefault();
                Ladda.stopAll();
            }
        });

        /**
         * CHECKOUT
         */
        if (jQuery("#checkout").length) {
            jQuery("#checkout .progress .progress-bar").tooltip({
                placement: 'bottom'
            });
            jQuery("#checkout .list-group .list-group-item input[type=radio]:checked").closest(".list-group-item").addClass('active');
            jQuery("#checkout .list-group .list-group-item").click(function () {
                jQuery("#checkout .list-group .list-group-item .shipping-desc, #checkout .list-group .list-group-item .payment-desc").hide();
                jQuery("#checkout .list-group .list-group-item").removeClass('active').find("input[type=radio]").removeAttr('checked');
                jQuery(this).addClass('active').find("input[type=radio]").prop('checked', 'true');
                jQuery(this).find(".shipping-desc, .payment-desc").show();
            });
        }

        /**
         * REVIEWS
         */
        jQuery(".product-reviews").not('popover-trigger').tooltip({
            viewport: '#content',
            placement: 'right'
        });

        /**
         * TOOLTIPS
         */
        jQuery("#header .header-info [title], .mnf a, .dummy a").tooltip({
            placement: 'bottom'
        });
        jQuery("#floating-box-container [title]").tooltip({
            placement: 'left'
        });
        jQuery("#box-paymentlogos img").tooltip({
            placement: 'top'
        });

        /**
         * PRODUCT DETAIL PAGE CONTENT
         */
        var productContentSelector = jQuery("#product #pcontent");
        if (productContentSelector.length != 0) {
            var productContentSecondarySelector = productContentSelector.find(".secondary");
            if (jQuery.trim(productContentSecondarySelector.text()) == '') {
                productContentSelector.removeAttr('class');
                productContentSecondarySelector.hide();
                productContentSelector.find(".primary").removeAttr('class').addClass('primary');
            }
        }

        /**
         * SWITCHES
         */
        var visibleSwitchItems = 1;
        $(".switch-area").each(function (area) {
            var thisSwitchArea = $(this);
            thisSwitchArea.addClass('switch-area-' + area + ' switch-items-show-' + visibleSwitchItems);
            if (!thisSwitchArea.hasClass('switch-disabled')) {
                var thisSwitchChildren = thisSwitchArea.find(".switch-items").children();
                if (thisSwitchChildren.length > visibleSwitchItems) {
                    thisSwitchArea.addClass('switch-enabled');
                    thisSwitchChildren.each(function (item) {
                        var thisSwitchItem = $(this);
                        if (item >= visibleSwitchItems) {
                            thisSwitchItem.addClass('switch-default-hidden switch-item switch-item-' + item);
                        } else {
                            thisSwitchItem.addClass('switch-default-visible switch-item switch-item-' + item);
                        }
                    });
                    thisSwitchChildren.siblings(".switch-default-hidden").wrapAll('<div class="switch-toggle" style="display:none;"></div>');
                    thisSwitchArea.find(".switch-button").click(function () {
                        thisSwitchArea.toggleClass('switch-bounce');
                        thisSwitchArea.find(".switch-toggle").slideToggle('fast', function () {
                            thisSwitchArea.toggleClass('switch-bounce-finish');
                        });
                    });
                } else {
                    thisSwitchArea.addClass('switch-disabled');
                }
            } else {
                thisSwitchArea.addClass('switch-disabled-by-class');
            }
        });

        /**
         * VISUAL FORM VALIDATION
         *
         * Visual validation for required fields
         * @version 2.0.0
         */
        setTimeout(function () {
            jQuery(".form-group label:contains('*'), .form-group .label:contains('*'), .form-group .form-required")
                .each(function () {
                    var label = jQuery(this),
                        formGroup = label.closest(".form-group"),
                        input = formGroup.find("input:not([type='checkbox']), textarea");

                    if (!input.parent(".bs-searchbox").length) {
                        label.addClass('control-label');
                        input.addClass('form-control');

                        input.on('blur', function () {
                            var self = jQuery(this),
                                selfFormGroup = self.closest(".form-group"),
                                selfFormControl = selfFormGroup.find(".form-control");

                            if (!self.val().length) {
                                selfFormGroup.removeClass('has-success').addClass('has-error');
                                selfFormControl.addClass('animated shake');
                            } else {
                                selfFormGroup.removeClass('has-error').addClass('has-success');
                                selfFormControl.removeClass('animated shake');
                            }
                        });
                    }
                });
        }, 10);

        /**
         * TEXT COUNTER
         */
        jQuery(".form-counter [maxlength]").each(function (i) {
            var field = this;
            var maxlength = parseInt(jQuery(field).attr('maxlength'));
            if (maxlength && maxlength > 0) {
                jQuery(field).parent().append('<p class="help-block js-input-count count-' + i + '"><span class="txt">' + TEXT_EW_ADVENTURY_STILL + ' </span><span class="nr label label-default">' + maxlength + '</span><span class="txt"> ' + TEXT_EW_ADVENTURY_CHARACTERS_AVAILABLE + '</span></p>');
                var thisCounter = jQuery(field).parent().find(".js-input-count");

                thisCounter.find(".nr").textCounter({
                    target: field,
                    stopAtLimit: true,
                    count: maxlength,
                    alertAt: Math.floor(maxlength / 100 * 30), //30%
                    warnAt: Math.floor(maxlength / 100 * 10) //10%
                });

                thisCounter.hide();
                jQuery(field).focus(function () {
                    thisCounter.show();
                });
            }
        });

        /**
         * IMAGE RESPONSIVE HELPER
         */
        jQuery("img.img-responsive").removeAttr('width').removeAttr('height');

        /**
         * VERTICAL HELPER LINK HELPER
         * Removes link html whitespaces because of wrong underline by onmouseover
         */
        jQuery("a.vertical-helper").each(function () {
            jQuery(this).html(jQuery.trim(jQuery(this).html()));
        });

        /**
         * !Safari Browser
         */
        if (isSafari == 0) {
            jQuery(".image-link, .usp-container .section").not('no-ripple')
                .addClass('ripple');
            jQuery('.panel-shadow').addClass('transition500');
        }

        /**
         * Set ripple class
         */

        jQuery(".btn-default, .table-hover td a").not('no-ripple')
            .addClass('ripple');

        /**
         * RIPPLE click animation
         * Material Design Hover & Click Effects
         */
        jQuery(".ripple").click(function (e) {
            if (jQuery(this).find(".ink-ripple").length === 0) {
                jQuery(this).prepend('<span class="ink-ripple"></span>');
            }
            var ink = jQuery(this).find(".ink-ripple").removeClass("animate-ripple");
            if (!ink.height() && !ink.width()) {
                var d = Math.max(jQuery(this).outerWidth(), jQuery(this).outerHeight());
                ink.css({height: d, width: d});
            }
            var x = e.pageX - jQuery(this).offset().left - ink.width() / 2, y = e.pageY - jQuery(this).offset().top - ink.height() / 2;
            ink.css({top: y + 'px', left: x + 'px'}).addClass("animate-ripple");
        });

        /**
         * Search overlay
         *
         * @param element
         * @returns {Array}
         */
        jQuery(".open-overlay-search").click(function () {
            jQuery("#overlay-search").addClass('open animated fadeIn');
            jQuery("#overlay-search .form-group").addClass('open animated fadeInDown');
        });

        jQuery("#overlay-search, #overlay-search .close").click(function () {
            jQuery("#overlay-search").addClass('open animated fadeOut');
            jQuery("#overlay-search .form-group").addClass('open animated fadeOutUp');
            setTimeout(function() {
                jQuery('#overlay-search, #overlay-search .form-group').removeClass('open animated fadeOutUp fadeInDown fadeIn fadeOut');
            }, 800);
            return true;
        });

        jQuery("#overlay-search .form-group").click(function(e) {
            e.stopPropagation();
        });


        /**
         * Popovers
         */
        jQuery(".popover-trigger").popover({trigger: "hover", container: 'body'});

        /**
         * Mega Menu
         */
        if (typeof CONFIG_EW_ADVENTURY_PLUGIN_MEGANAV != 'undefined' && CONFIG_EW_ADVENTURY_PLUGIN_MEGANAV === true) {

            var megamenu = jQuery(".mega-menu");
            var megamenuVisible = 0;
            var megamenuCover = jQuery(".mega-menu-cover");
            var megamenuTrigger = jQuery(".mega-menu-trigger");
            var megamenuScrolls = 0;
            var mainNavigation = jQuery('#main-navigation');

            megamenuTrigger.click(function () {

                if (jQuery(this).hasClass("mega-visible")) {
                    hideMegaMenu();
                } else {
                    $('body').addClass("disableScroll");
                    if (megamenuVisible == 0 || megamenuVisible == 1) {
                        if (!mainNavigation.hasClass('affix')) {
                            megamenuScrolls = 1;
                            jQuery("html, body").stop().animate({ scrollTop: mainNavigation.offset().top - 1 }, 200 , function() {
                                setTimeout(function() {
                                    megamenuScrolls = 0;
                                }, 500);
                            });
                        }
                        megamenuVisible = 1;
                        megamenuTrigger.removeClass("mega-visible");
                        jQuery(this).addClass("mega-visible");
                        megamenu
                            .html("")
                            .html(jQuery("#" + jQuery(this).data("megamenu")).html())
                            .removeClass("hidden");
                        megamenu.find('.categoryCarousel').owlCarousel({
                            itemsCustom: [
                               [0, 2],
                               [480, 2],
                               [768, 3],
                               [992, 4]
                            ],
                            navigation: true,
                            lazyLoad: true,
                            slideSpeed: 800,
                            paginationSpeed: 800,
                            pagination: false,
                            scrollPerPage: false,
                            addClassActive: true,
                            navigationText: ['', '']
                        });
                        jQuery(megamenu).show();
                        megamenu.css({'top': mainNavigation.offset().top + mainNavigation.height()});
                        megamenuCover.stop().fadeIn(300);
                    }
                }
                jQuery(".mega-close").click(function () {
                    hideMegaMenu();
                });
                jQuery(".mega-menu-cover").click(function () {
                    hideMegaMenu();
                });
            });

            jQuery(window).on("resize", function () {
                hideMegaMenu();
            });

            var hideMegaMenu = function () {
                if (megamenuVisible == 1 && megamenuScrolls == 0) {
                    megamenuVisible = 2;
                    $('body').removeClass("disableScroll");
                    megamenuTrigger.removeClass("mega-visible");
                    jQuery(megamenu).hide();
                    megamenuCover.stop().fadeOut(300, function () {
                        megamenu
                            .html("")
                            .addClass("hidden");
                        megamenuVisible = 0;
                    });
                }
            };
        }

        var cartBottomFloating = jQuery('#cart-bottom-floating');
        var cartToolbarVisible = 0;
        var toggleCartToolbar = function() {
            if (cartToolbarVisible == 0 && $(window).scrollTop() > ($(window).height() / 100 * 25)) {
                cartBottomFloating.addClass("animated fadeInUp");
                cartBottomFloating.show();
                cartToolbarVisible = 1;
            }
            if (cartToolbarVisible == 1 && $(window).scrollTop() < ($(window).height() / 100 * 25)) {
                cartBottomFloating.removeClass("animated fadeInUp");
                cartBottomFloating.hide();
                cartToolbarVisible = 0;
            }
        }

        jQuery(window).on("resize", function () {
            toggleCartToolbar();
        });

        jQuery(window).on("scroll", function () {
            toggleCartToolbar();
        });


        /**
         * Removes hover css on apple devices
         * because of ipad click issues
         */
        if (isAppleMobileDevice()) {
            jQuery(".hvr-float")
                .removeClass('hvr-float')
                .addClass('hvr-float-removed-from-js');
        }

        /**
         * IE 9- placeholder fix
         */
        if (MSIE && MSIE <= 9) {
            jQuery("[placeholder]").each(function () {
                var form = jQuery(this),
                    placeholder = jQuery.trim(form.attr('placeholder')),
                    value = jQuery.trim(form.attr('value'));
                if (value == '' && placeholder != '') {
                    form.attr('value', placeholder);
                    form.focus(function () {
                        var form = jQuery(this);
                        if (jQuery.trim(form.attr('value')) == placeholder) {
                            form.attr('value', '');
                        }
                    });
                }
            });
        }

        /**
         * CAROUSEL PRODUCT SLIDER
         */
        var productSliderElement = jQuery('.productCarousel');
        if (productSliderElement.length != 0) {
            productSliderElement.owlCarousel({
                itemsCustom: [
                    [0, 1],
                    [480, 2],
                    [768, 3],
                    [992, 4]
                ],
                navigation: true,
                lazyLoad: true,
                slideSpeed: 800,
                paginationSpeed: 800,
                pagination: false,
                scrollPerPage: true,
                addClassActive: true,
                navigationText: ['', ''],
                afterUpdate: function (t) {
                    var target = jQuery(t),
                        id = target.attr('id');

                    if (typeof id != 'undefined' && id != '') {
                        equalizeListingHeights(' #' + id);
                    }
                }
            });
        }

        /**
         * CAROUSEL PRODUCT SLIDER (SINGLE)
         */
        var productSliderElementSingle = jQuery('.productCarouselSingle');
        if (productSliderElementSingle.length != 0) {
            productSliderElementSingle.owlCarousel({
                singleItem: true,
                navigation: true,
                pagination: false,
                slideSpeed: 800,
                paginationSpeed: 800,
                scrollPerPage: true,
                autoHeight: true,
                lazyLoad: true,
                addClassActive: true,
                navigationText: ['', '']
            });
        }

        /**
         * PRODUCT PICTURE SLIDER
         */
        var productPictureSliderElement = jQuery('.productPictureCarousel');
        if (productPictureSliderElement.length != 0) {
            productPictureSliderElement.owlCarousel({
                itemsCustom: [
                    [0, 2],
                    [480, 3],
                    [768, 4],
                    [992, 5]
                ],
                navigation: true,
                lazyLoad: true,
                slideSpeed: 800,
                paginationSpeed: 800,
                pagination: false,
                scrollPerPage: false,
                addClassActive: true,
                navigationText: ['', '']
            });
        }

        /**
         * TESTIMONIALS SLIDER
         */
        var testimonialsElement = jQuery('.testimonialsCarousel');
        if (testimonialsElement.length != 0) {
            testimonialsElement.owlCarousel({
                singleItem: true,
                navigation: false,
                pagination: true,
                slideSpeed: 800,
                paginationSpeed: 800,
                scrollPerPage: true,
                autoPlay: 7000,
                stopOnHover: true,
                autoHeight: true,
                lazyLoad: true,
                addClassActive: true,
                navigationText: ['', '']
            });
        }

        /**
         * CAROUSEL TEASER SLIDER
         */
        jQuery('.pictureCarousel').each(function () {
            var teaserElement = jQuery(this);

            teaserElement.children().removeClass('hidden');

            teaserElement.owlCarousel({
                singleItem: true,
                navigation: false,
                pagination: true,
                slideSpeed: teaserElement.data('slide-speed') || 800,
                paginationSpeed: teaserElement.data('pagination-speed') || 800,
                scrollPerPage: true,
                autoPlay: teaserElement.data('auto-play') || 7000,
                stopOnHover: true,
                autoHeight: true,
                lazyLoad: true,
                addClassActive: true,
                navigationText: ['', '']
            });
        });

        /**
         * CATEGORY TEASER SLIDER
         */
        var categoryElement = jQuery('#categoryCarousel-category');
        if (categoryElement.length != 0) {
            categoryElement.owlCarousel({
                itemsCustom: [
                    [0, 2],
                    [480, 2],
                    [768, 3],
                    [992, 4]
                ],
                navigation: true,
                lazyLoad: true,
                slideSpeed: 800,
                paginationSpeed: 800,
                pagination: false,
                scrollPerPage: false,
                addClassActive: true,
                navigationText: ['', '']
            });
        }

        /**
         * CATEGORY TEASER SLIDER
         */
        var categoryElementCompact= jQuery('#categoryCarousel-categoryCompact');
        if (categoryElementCompact.length != 0) {
            categoryElementCompact.owlCarousel({
                itemsCustom: [
                    [0, 2],
                    [480, 3],
                    [768, 4],
                    [992, 5]
                ],
                navigation: true,
                lazyLoad: true,
                slideSpeed: 800,
                paginationSpeed: 800,
                pagination: false,
                scrollPerPage: false,
                addClassActive: true,
                navigationText: ['', '']
            });
        }


        /**
         * LIGHT GALLERY
         */
        var lightGalleryElement = jQuery(".lightgallery");
        if (lightGalleryElement.length !== 0) {
            lightGalleryElement.lightGallery({
                selector: 'a',
                download: false,
                mode: 'lg-lollipop',
                hideBarsDelay: 99999999,
                mouseWheel: false,
                hash: false
            });
        }
    }, 10);
});

/**
 * EQUALIZE LISTING HEIGHTS
 * apply your matchHeight on DOM ready (they will be automatically re-applied on load or resize)
 * @see http://brm.io/jquery-match-height/
 */
var equalizeListingHeights = function (rowClass) {
    if ((!MSIE || MSIE > 8) && typeof window.opera == 'undefined') {
        jQuery(function () {
            setTimeout(function () {
                var listingAutoPanels, listingNoPanels;

                jQuery.fn.matchHeight._throttle = 100;
                jQuery.fn.matchHeight._resizeTimeOut = 100;

                var equalize = function (wrap, panelize) {
                    panelize = panelize || false;

                    wrap.find(".image-link:visible").matchHeight(panelize);
                    setTimeout(function () {
                        wrap.find(".title:visible").matchHeight(panelize);
                        setTimeout(function () {
                            wrap.find(".section:visible").matchHeight(panelize);
                        }, 0)
                    }, 0)
                };

                // with panels
                if (typeof rowClass === 'undefined') {
                    listingAutoPanels = jQuery(".listing:not(.equalize-no-panels, .equalize-nothing):visible");
                } else {
                    listingAutoPanels = jQuery(".listing:not(.equalize-no-panels, .equalize-nothing):visible" + rowClass);
                }
                if (listingAutoPanels.length != 0) {
                    listingAutoPanels.each(function () {
                        equalize(jQuery(this), true)
                    });
                }

                // no panels
                if (typeof rowClass === 'undefined') {
                    listingNoPanels = jQuery(".listing.equalize-no-panels:not(.equalize-nothing):visible");
                } else {
                    listingNoPanels = jQuery(".listing.equalize-no-panels:not(.equalize-nothing):visible" + rowClass);
                }
                if (listingNoPanels.length != 0) {
                    listingNoPanels.each(function () {
                        equalize(jQuery(this), false)
                    });
                }
            }, 10);
        });
    }
};

(function () {
    setTimeout(function () {
        equalizeListingHeights();

        //checkout progress bar
        if ((!MSIE || MSIE > 8) && typeof window.opera == 'undefined') {
            jQuery(function () {
                jQuery("#checkout .progress-bar").matchHeight(false);
            });
        }
    }, 10);
})();

/**
 * Scroll animations with wow.js
 */
if (typeof CONFIG_EW_ADVENTURY_PLUGIN_ANIMATIONS != 'undefined' &&
    CONFIG_EW_ADVENTURY_PLUGIN_ANIMATIONS === true) {
    new WOW().init();
}

/**
 * Spinner
 */
jQuery(".jq-spinner")
    .spinner('delay', 200) //delay in ms
    .spinner('changed', function (e, newVal, oldVal) {
        //trigger lazed, depend on delay option.
    })
    .spinner('changing', function (e, newVal, oldVal) {
        //trigger immediately
    });

/**
 *
 * @param msg
 * @param cssClass  success|info|warning|danger
 * @param title
 */
function xtAlertAndHide(msg, cssClass, title)
{
    if(msg === "" || msg === false || msg === null || typeof msg === 'undefined') msg = "Ok";

    if(cssClass === "" || cssClass === false || cssClass === null || typeof cssClass === 'undefined') cssClass = "success";

    if(title === "" || title === false || title === null || typeof title === 'undefined') title = "!";

    if (typeof msg === 'string')
        msg = [{msg: msg, cssClass: cssClass, title: title}];
    const acceptableCssClasses = ['success','info','warning','danger'];

    msg.forEach(function(el){
        try
        {
            if(el.msg == "" || el.msg == false || el.msg == null) el.msg = "Ok";

            if(el.cssClass === "error") el.cssClass = "danger";
            if(el.cssClass == "" || el.cssClass == false || el.cssClass == null) el.cssClass = "success";
            if(!acceptableCssClasses.includes(cssClass)) el.cssClass = "info";

            if(el.title == ""|| el.title == false || el.title == null) title = false;

            let delay = 4000;
            if(el.cssClass === 'success') delay = 2000;
            if(el.cssClass === 'danger') delay = 6000;

            let glyph_icon = "asterisk";
            if(el.cssClass === 'success') glyph_icon = "ok";
            else if(el.cssClass === 'info') glyph_icon = "info-sign";
            else if(el.cssClass === 'warning') glyph_icon = "warning-sign";
            else if(el.cssClass === 'danger') glyph_icon = "exclamation-sign";

            // http://bootstrap-notify.remabledesigns.com/
            $.notify({
                // options
                icon: 'glyphicon glyphicon-'+glyph_icon,
                title: el.title,
                message: el.msg
            },{
                // settings
                type: el.cssClass,
                newest_on_top: true,
                delay: delay,
                mouse_over: 'pause',
                animate: {
                    enter: 'animated fadeInDown',
                    exit: 'animated fadeOutUp'
                }
            });
        }
        catch(e){
            console.error(e);
        }
    });
}

function getCookie(name) {
    let matches = document.cookie.match(new RegExp(
        "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\/\+^])/g, '\$1') + "=([^;]*)"
    ));
    return matches ? decodeURIComponent(matches[1]) : undefined;
}

function xtSetCookie(name, value, options)
{
    if (typeof options != "object")
        options = {};

    let options_local = {
        path: baseUri
    };

    for (let attrname in options) { options_local[attrname] = options[attrname]; }

    if (options.expires instanceof Date) {
        options_local.expires = options.expires.toUTCString();
    }

    //console.log(options_local);

    let updatedCookie = encodeURIComponent(name) + "=" + encodeURIComponent(value);

    for (let optionKey in options_local) {
        updatedCookie += "; " + optionKey;
        let optionValue = options_local[optionKey];
        if (optionValue !== true) {
            updatedCookie += "=" + optionValue;
        }
    }

    //console.log(updatedCookie);

    document.cookie = updatedCookie;
}

function xtDeleteCookie(name) {
    setCookie(name, "", {
        'max-age': -1
    })
}