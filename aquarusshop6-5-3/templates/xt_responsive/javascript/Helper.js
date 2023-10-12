/**
 * EQUALIZE LISTING HEIGHTS
 * apply your matchHeight on DOM ready (they will be automatically re-applied on load or resize)
 * @see http://brm.io/jquery-match-height/
 */
var equalizeListingHeights = function (rowClass) {
    if ((!MSIE || MSIE > 8) && typeof window.opera == 'undefined') {
        jQuery(function () {
            var listingAutoPanels, listingNoPanels;

            jQuery.fn.matchHeight._throttle = 100;

            var equalize = function (wrap, panelize) {
                panelize = panelize || false;

                wrap.find(".image-link").matchHeight(panelize);
                setTimeout(function () {
                    wrap.find(".title").matchHeight(panelize);
                    setTimeout(function () {
                        wrap.find(".section-body").matchHeight(panelize);
                        wrap.find(".section-footer").matchHeight(panelize);
                        setTimeout(function () {
                            wrap.find(".section").matchHeight(panelize);
                        }, 0)
                    }, 0)
                }, 0)
            };

            // with panels
            if (typeof rowClass === 'undefined') {
                listingAutoPanels = jQuery(".listing:not(.equalize-no-panels, .equalize-nothing)");
            } else {
                listingAutoPanels = jQuery(".listing:not(.equalize-no-panels, .equalize-nothing)" + rowClass);
            }
            if (listingAutoPanels.length != 0) {
                listingAutoPanels.each(function () {
                    equalize(jQuery(this), true)
                });
            }

            // no panels
            if (typeof rowClass === 'undefined') {
                listingNoPanels = jQuery(".listing.equalize-no-panels:not(.equalize-nothing)");
            } else {
                listingNoPanels = jQuery(".listing.equalize-no-panels:not(.equalize-nothing)" + rowClass);
            }
            if (listingNoPanels.length != 0) {
                listingNoPanels.each(function () {
                    equalize(jQuery(this), false)
                });
            }
        });
    }
};

/**
 * Get IE Version; supports IE11
 *
 * @see http://stackoverflow.com/a/17907562
 * @returns {number|bool} Returns IE Version Number; On other browsers boolean false
 */
var MSIE = (function () {
    var rv = 4, ua, re;
    if (navigator.appName == 'Microsoft Internet Explorer') {
        ua = navigator.userAgent;
        re = new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");
        if (re.exec(ua) != null) {
            rv = parseFloat(RegExp.$1);
        }
    } else if (navigator.appName == 'Netscape') {
        ua = navigator.userAgent;
        re = new RegExp("Trident/.*rv:([0-9]{1,}[\.0-9]{0,})");
        if (re.exec(ua) != null) {
            rv = parseFloat(RegExp.$1);
        }
    }
    return (rv > 4) ? rv : false;
}());

/**
 * Detect apple mobile devices
 *
 * @returns {boolean}
 */
var isAppleMobileDevice = function () {
    return (
        (navigator.platform.indexOf("iPad") != -1) ||
        (navigator.platform.indexOf("iPhone") != -1) ||
        (navigator.platform.indexOf("iPod") != -1)
    );
};

/**
 * Detect mobile devices
 *
 * @returns {boolean}
 */
var isMobileDevice = function () {
    var userAgent = navigator.userAgent,
        platform = navigator.platform;

    return (
        (userAgent.indexOf("Android") != -1) ||
        (userAgent.indexOf("AppleWebKit") != -1 && userAgent.indexOf("Android") != -1) ||
        (userAgent.indexOf("IEMobile") != -1) ||
        (userAgent.indexOf("Trident") != -1 && userAgent.indexOf("Tablet") != -1) ||
        (userAgent.indexOf("webOS") != -1) ||
        (platform.indexOf("iPhone") != -1) ||
        (platform.indexOf("iPad") != -1) ||
        (platform.indexOf("iPod") != -1) ||
        (userAgent.indexOf("BlackBerry") != -1)
    );
};

/**
 * Detect Safari Browser
 *
 * @returns {boolean}
 */
var isSafari = function () {
    return !!(navigator.userAgent.indexOf('Safari') != -1 && navigator.userAgent.indexOf('Chrome') == -1);
};

/**
 * Backe Keks (Create cookie)
 *
 * @param e {String} Unique name for each cookie
 * @param t {String} Cookie content
 * @param n {Number} Lifetime in days
 * @example backeKeks('myLovelyCookie', 'Lorem ipsum dolor sit amet...', 365);
 * @returns {Boolean} False if no cookie is found after creation
 */
function backeKeks(e, t, n) {
    objNow = new Date;
    strExp = new Date(objNow.getTime() + n * 864e5);
    document.cookie = e + "=" + t + ";expires=" + strExp.toGMTString() + ";";
    return (esseKeks(e) !== false) ? true : false
}

/**
 * Esse Keks (Get cookie content or check its existence)
 *
 * @param e string Unique name for each cookie
 * @example esseKeks('myLovelyCookie');
 * @returns {Boolean|String} False if no cookie is found, otherwise the cookie content
 */
function esseKeks(e) {
    if (strCookie = document.cookie)return (arrCookie = strCookie.match(new RegExp(e + "=([^;]*)", "g"))) ? RegExp.$1 : false
}

/**
 * Esse Keks auf (Get cookie content or check its existence and delete the cookie)
 *
 * @param e string Unique name for each cookie
 * @example esseKeksAuf('myLovelyCookie');
 * @returns {Boolean|String} False if no cookie is found or cookie could not be deleted, otherwise the cookie content
 */
function esseKeksAuf(e) {
    if ((c = esseKeks(e)) != false) {
        return (backeKeks(e, '', -1) == false) ? c : false
    } else {
        return false
    }
}
