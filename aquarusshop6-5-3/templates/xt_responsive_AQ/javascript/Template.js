/*******************************************************************************
 * ON DOCUMENT READY
 */
document.addEventListener("DOMContentLoaded",function(event){

    /**
     *  FIX ACCORDIONS AUTO COLLAPSE
     *  collapse only on load, then remove toggle class
     */
    $('.collapse-auto-toggle-xs').on('hidden.bs.collapse', function (e) {
        console.log(this);
        $(this).removeClass('collapse-auto-toggle-xs');
    })

    /**
     *  dont collapse on link in a data-toggle element
     */
    $('.dont-collapse').on('click', function (e) {
        e.stopPropagation();
    });


    /**
     * CHECK BREAKPOINT ON SCREEN RESIZE
     */
    /**
     * CHECK BREAKPOINT ON SCREEN RESIZE
     */
    jQuery(window).resize(function () {
        setTimeout(function () {
            var c = jQuery(".collapse.collapse-auto-toggle-xs");
            if (jQuery.isXs()) {
                c.collapse('hide')
            } else {
                c.collapse('show')
            }
        }, 0)
    }.debounce(500))
        .resize();

    /**
     * BOOTSTRAP SELECT
     * @see http://silviomoreto.github.io/bootstrap-select/
     */
    if (!MSIE || MSIE > 8) {
        jQuery("select")
            .addClass('selectpicker show-menu-arrow')
            .hide();
        jQuery(".ms-options select")
            .attr('data-style', 'btn-secondary')
            .each(function () {
                var self = jQuery(this);
                if (parseInt(self.val()) > 0) {
                    self.addClass('select_option_selected');
                }
            });
        if (isMobileDevice()) {
            jQuery(".selectpicker")
                .selectpicker('mobile');
        }
    }

    /**
     * ANCHOR ANIMATION
     * slide to id by hash
     */
    jQuery(".move").bind("click", function(e) {
        var z = jQuery(this).get(0).hash,
            o = jQuery(z);

        if (o.length == 0)
            return true;

        e.preventDefault();
        jQuery("html, body").animate({
            scrollTop : o.offset().top - 80
        }, 500);
    });

    /**
     * "Back to Top" LINK
     * Only enable if the document has a long scroll bar.
     * Note the window height + offset.
     */
    var backToTopSelector = jQuery("#back-to-top");
    var backToTopVisibilityOffset = 100;
    if ((jQuery(window).height() + backToTopVisibilityOffset) < jQuery(document).height()) {
        backToTopSelector.removeClass('hidden').affix({
            offset: {top: backToTopVisibilityOffset}
        });
        backToTopSelector.click(function (e) {
            e.preventDefault();
            jQuery("html, body").animate({scrollTop: 0}, 'slow');
        });
    }

    /**
     * SWITCHES
     */
    jQuery(".switch-area").each(function (area) {
        var thisSwitchArea = jQuery(this),
            visibleSwitchItems = parseInt(thisSwitchArea.data('visible-items'));
        if (visibleSwitchItems < 1) {
            visibleSwitchItems = 1;
        }
        thisSwitchArea.addClass('switch-area-' + area + ' switch-items-show-' + visibleSwitchItems);
        if (!thisSwitchArea.hasClass('switch-disabled')) {
            var thisSwitchChildren = thisSwitchArea.find(".switch-items").children();
            if (thisSwitchChildren.length > visibleSwitchItems) {
                thisSwitchArea.addClass('switch-enabled');
                thisSwitchChildren.each(function (item) {
                    var thisSwitchItem = jQuery(this);
                    if (item >= visibleSwitchItems) {
                        thisSwitchItem.addClass('switch-default-hidden switch-item switch-item-' + item);
                    } else {
                        thisSwitchItem.addClass('switch-default-visible switch-item switch-item-' + item);
                    }
                });
                thisSwitchChildren.siblings(".switch-default-hidden").wrapAll('<div class="switch-toggle" style="display:none;"></div>');
                thisSwitchArea.find(".switch-button").click(function () {
                    var self = jQuery(this);
                    thisSwitchArea.toggleClass('switch-bounce');
                    thisSwitchArea.find(".switch-toggle").slideToggle('fast', function () {
                        thisSwitchArea.toggleClass('switch-bounce-finish');
                        self.blur();
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
     * LOGIN PASSWORD TOGGLE
     */
    var accountSwitch = function (type) {
        type = type || 'register';
        jQuery('.visible-switch-account').show();
        switch (type) {
            case 'register':
                jQuery('.visible-guest-account').hide();
                break;
            default:
                jQuery('.visible-register-account').hide();
        }
    };
    jQuery('#guest-account')
        .change(function () {
            accountSwitch(jQuery(this).val());
        })
        .change();

    /**
     * FORM REQUIRED MARK
     */
    jQuery("label:contains('\*')").html(function (_, html) {
        return html.replace(/(\*)/g, '<span class="required">$1</span>');
    });

    /**
     * CHECKOUT
     */
    var listGroupItems = jQuery("#checkout .list-group-item");
    if (listGroupItems.length) {
        var setListGroupActive = function (t) {
            listGroupItems.removeClass('active');
            t.closest(".list-group-item").addClass('active');
        };

        // highlight by preselected
        setListGroupActive(listGroupItems.find(".selected:last"));

        // highlight by click
        listGroupItems.addClass('cursor-pointer').click(function () {
            var radio = jQuery(this).find("[name='selected_shipping'], [name='selected_payment']");
            
            radio.prop('checked', true);

            listGroupItems
                .not(this)
                .find(".collapse.in")
                .collapse('hide');

            $(this).find(".collapse").collapse('show');

            setListGroupActive(radio);
        });

        // highlight by change
        listGroupItems.find("[name='selected_shipping'], [name='selected_payment']").change(function () {
            setListGroupActive(jQuery(this));
        });
    }

    /**
     * TOOLTIPS
     */
    var isTouchDevice = true === ("ontouchstart" in window || window.DocumentTouch && document instanceof DocumentTouch);
    if (!isTouchDevice) {
        jQuery("#header .header-info [title]").tooltip({
            placement: 'bottom',
            trigger: "hover"
        });
        jQuery('[data-toggle="tooltip"]').tooltip({ trigger: "hover" });
        jQuery('[data-toggle="tooltip"]').on('click', function () {
            $(this).tooltip('hide')
        })
    }

    /**
     * VISUAL FORM VALIDATION
     *
     * Visual validation for required fields
     * @version 2.0
     */
    setTimeout(function () {
        jQuery(".form-group label:contains('*'), .form-group .label:contains('*'), .form-group .form-required")
            .each(function () {
                var label = jQuery(this),
                    formGroup = label.closest(".form-group"),
                    input = formGroup.find("input, textarea");

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
    initSliders();

    /**
     * LIGHT GALLERY
     */
    var lightGalleryElement = jQuery(".lightgallery");
    if (lightGalleryElement.length != 0) {
        lightGalleryElement.lightGallery({
            selector: 'a',
            download: false,
            hash: false,
            mode: 'lg-lollipop',
            hideBarsDelay: 99999999,
            mouseWheel: false
        });
    }

    /**
     * Prevent dropdown toggle on self click
     */
    jQuery(".dropdown-menu").click(function (e) {
        //e.stopPropagation();
    });

    /**
     *  prevent double form submission
     */
    window.form_being_submitted = false;
    jQuery(
        'form:not([data-ajax="true"])#checkout-form, ' +
        'form:not([data-ajax="true"])#create_account').submit(function(e)
    {
        try{
            var formChecked = xtSimpleCheckForm(this);
            if(window.form_being_submitted || !formChecked)
            {
                e.preventDefault();
                return false;
            }
            window.form_being_submitted = true;

            //jQuery('form#checkout-form button[type=submit]').removeClass('btn-success',true)
            var $btnSubmit = $(this).find('button[type=submit].btn-success,' +
                'button[type=submit].btn-primary');

            if ($btnSubmit.length > 0) {
                $btnSubmit.removeClass('btn-success');
                $btnSubmit.css('cursor','default');
                $btnSubmit.prop('disabled',true).attr('disabled',true);

                $btnSubmit.find('[class^=fa]').each(
                    function(idx, elem){
                        $(elem).removeClass(
                            function (index, className) {
                                return (className.match(/(^|\s)fa-\S+|/g) || []).join(' ');

                            });
                        $(elem).addClass('fa-spinner fa-spin');
                    });

                $btnSubmit.find('[class^=glyphicon]').each(
                    function(idx, elem){
                        $(elem).removeClass(
                            function (index, className) {
                                return (className.match(/(^|\s)glyphicon-\S+|/g) || []).join(' ');

                            });
                        $(elem).addClass('glyphicon-hourglass');
                    });
            }
        }catch(ex){
            console.log(ex);
        }
    });

    $(".xt-form-required").on('click', function(){
       $(this).removeClass('xt-form-error');
    });

});

function initSliders(selector = '.productCarousel')
{
    var itemsCustom = [
        [0, 1],
        [480, 2],
        [768, 3],
        [992, 4]
    ];
    try {
        if (window.XT.version.type == 'FREE' && window.XT.page.page_name == 'cart') {
            itemsCustom = [
                [992, 2]
            ];
        }
    }catch (e)
    {
        console.log(e);
    }

    var productSliderElement = jQuery(selector);
    if (productSliderElement.length != 0) {
        productSliderElement.owlCarousel({
            itemsCustom: itemsCustom,
            responsive: true,
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

}

const xtConnector = {

    translations: [],

    page: async (fncParams, options) => {
        let data = await xtConnector.fnc(fncParams, options);
        if(data && data.success === true) {
            replaceDomElement(data.callback_args.id, data.callback_args.html);
            initSliders('#' + data.callback_args.id + ' .productCarousel');
        }

    },

    fnc: async (fncParams, options) => {
        if(typeof fncParams != "undefined" && typeof fncParams.connector != "undefined") {
            const params = {...{connector: fncParams.connector}, ...fncParams};
            options = {...{silent: true, throw: false}, ...options};
            try {

                const result = await axios.request({
                    method: 'POST',
                    url: window.XT.baseUrl + 'connector.php',
                    headers: {
                        'Content-Type': 'application/json; utf-8',
                    },
                    data: JSON.stringify(params)
                })

                //console.log(result);
                if (!options.silent) {
                    xtConnector.msg(result.data.message);
                }
                return result.data;
            } catch (e) {
                xtConnector.error(e);
                if (options.throw === true) {
                    throw e;
                }
            }
        }
    },

    error: (e) => {
        console.log('error', e);
    },
    // main xt bridge / error / msg

}; // main xt connector / error / msg

function replaceDomElement(id, html)
{
    const oldEl = document.querySelector("#" + id);
    oldEl.innerHTML = html;
}


/**
 * EQUALIZE HEIGHTS
 */
(function () {
    equalizeListingHeights();

    //checkout progress bar
    if ((!MSIE || MSIE > 8) && typeof window.opera == 'undefined') {
        jQuery(function () {
            jQuery("#checkout .progress-bar").matchHeight(false);
        });
    }
})();

function showCartChanged()
{
    jQuery(".box-cart").addClass('animated rubberBand');
    setTimeout(function()
        {
            jQuery(".box-cart").removeClass('animated rubberBand');
            jQuery(".box-cart .btn-group").addClass('open');
            setTimeout(function()
                {
                    jQuery(".box-cart .btn-group").removeClass('open');
                }, 2000
            );
        }, 700
    );
}

// very simple form check
function xtSimpleCheckForm(form)
{
    var errorElements = [];


    $(form).find('input[type=checkbox].xt-form-required').each(function(idx, el){
        if( el.checked !== true) {
            errorElements.push(el);
        }
    });

    $(form).find('input[type=radio].xt-form-required').each(function(idx, el){
        //
    });


    $(form).find('input[type=text].xt-form-required').each(function (idx, el) {
        //
    });

    // ....

    if(errorElements.length)
    {
        errorElements.forEach(function(el){
            $(el).addClass('xt-form-error');
        });
        $(errorElements[0]).parents('form')[0].scrollIntoView({
            behavior: 'smooth'
        });
        return false;
    }

    return true;
}

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

/*
bindWithDelay jQuery plugin
Author: Brian Grinstead
MIT license: http://www.opensource.org/licenses/mit-license.php

http://github.com/bgrins/bindWithDelay
http://briangrinstead.com/files/bindWithDelay

Usage:
    See http://api.jquery.com/bind/
    .bindWithDelay( eventType, [ eventData ], handler(eventObject), timeout, throttle )

Examples:
    $("#foo").bindWithDelay("click", function(e) { }, 100);
    $(window).bindWithDelay("resize", { optional: "eventData" }, callback, 1000);
    $(window).bindWithDelay("resize", callback, 1000, true);
*/

(function($) {

    $.fn.bindWithDelay = function( type, data, fn, timeout, throttle ) {

        if ( $.isFunction( data ) ) {
            throttle = timeout;
            timeout = fn;
            fn = data;
            data = undefined;
        }

        // Allow delayed function to be removed with fn in unbind function
        fn.guid = fn.guid || ($.guid && $.guid++);

        // Bind each separately so that each element has its own delay
        return this.each(function() {

            var wait = null;

            function cb() {
                var e = $.extend(true, { }, arguments[0]);
                var ctx = this;
                var throttler = function() {
                    wait = null;
                    fn.apply(ctx, [e]);
                };

                if (!throttle) { clearTimeout(wait); wait = null; }
                if (!wait) { wait = setTimeout(throttler, timeout); }
            }

            cb.guid = fn.guid;

            $(this).bind(type, data, cb);
        });
    };

    $.fn.delayedKeyup = function( delay, callback, options ) {

        return this.each(function() {
            $(this).bindWithDelay('keyup', callback, delay);
        });
    };

    $.fn.delayedInput = function( delay, callback, options ) {

        return this.each(function() {
            $(this).bindWithDelay('input', callback, delay);
        });
    };

})(jQuery);


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

