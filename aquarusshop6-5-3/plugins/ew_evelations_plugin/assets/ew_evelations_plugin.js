/**
* Function to Convert RGB to Hex
*/
function rgb2hex(rgb) {
    rgb = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
    return "#" + hex(rgb[1]) + hex(rgb[2]) + hex(rgb[3]);
}
var hexDigits = new Array("0","1","2","3","4","5","6","7","8","9","a","b","c","d","e","f");
function hex(x) {
    return isNaN(x) ? "00" : hexDigits[(x - x % 16) / 16] + hexDigits[x % 16];
}

/*
    Name: jquery.textCounter-min.js
    Author: Andy Matthews
    Website: http://www.andyMatthews.net

    @modified	2012-05-09
*/
(function(e){e.fn.textCounter=function(t){t=e.extend({},e.fn.textCounter.defaults,t);return this.each(function(n,r){var i=e(r);i.html(t.count);e(t.target).keyup(function(){var e=this.value.length;if(e<=t.count-t.alertAt){i.removeClass("tcAlert tcWarn")}else if(e>t.count-t.alertAt&&e<=t.count-t.warnAt){i.removeClass("tcAlert tcWarn").addClass("tcAlert")}else{i.removeClass("tcAlert tcWarn").addClass("tcWarn");if(t.stopAtLimit)this.value=this.value.substring(0,t.count)}if(t.count-this.value.length==0){i.html("0")}else{i.html(t.count-this.value.length)}}).trigger("keyup");e(t.target).bind("blur",function(){var e=this.value.length;if(e<=t.count-t.alertAt){i.removeClass("tcAlert tcWarn")}else if(e>t.count-t.alertAt&&e<=t.count-t.warnAt){i.removeClass("tcAlert tcWarn").addClass("tcAlert")}else{i.removeClass("tcAlert tcWarn").addClass("tcWarn");if(t.stopAtLimit)this.value=this.value.substring(0,t.count)}if(t.count-this.value.length==0){i.html("0")}else{i.html(t.count-this.value.length)}})})};e.fn.textCounter.defaults={count:140,alertAt:20,warnAt:0,target:"",stopAtLimit:false}})(jQuery)

/**
 * Get IE Version; supports IE11
 *
 * @see http://stackoverflow.com/a/17907562
 * @returns {number|bool} Returns IE Version Number; On other browsers boolean false
 */
var MSIE = (function() {
    var rv = 4, ua, re;
    if (navigator.appName == 'Microsoft Internet Explorer') {
        ua = navigator.userAgent;
        re  = new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");
        if (re.exec(ua) != null) {
            rv = parseFloat(RegExp.$1);
        }
    } else if (navigator.appName == 'Netscape') {
        ua = navigator.userAgent;
        re  = new RegExp("Trident/.*rv:([0-9]{1,}[\.0-9]{0,})");
        if (re.exec(ua) != null) {
            rv = parseFloat(RegExp.$1);
        }
    }
    return (rv > 4) ? rv : false;
}());


/**
 * Backe Keks (Create cookie)
 *
 * @param e {String} Unique name for each cookie
 * @param t {String} Cookie content
 * @param n {Int} Lifetime in days
 * @example backeKeks('myLovelyCookie', 'Lorem ipsum dolor sit amet...', 365);
 * @returns {Boolean} False if no cookie is found after creation
 */
function backeKeks(e,t,n){objNow=new Date;strExp=new Date(objNow.getTime()+n*864e5);document.cookie=e+"="+t+";expires="+strExp.toGMTString()+";path=/";return (esseKeks(e)!==false)?true:false}
/**
* Esse Keks (Get cookie content or check its existence)
*
* @param e string Unique name for each cookie
* @example esseKeks('myLovelyCookie');
* @returns {Boolean|String} False if no cookie is found, otherwise the cookie content
*/
function esseKeks(e){if(strCookie=document.cookie)return(arrCookie=strCookie.match(new RegExp(e+"=([^;]*)","g")))?RegExp.$1:false}
/**
* Esse Keks auf (Get cookie content or check its existence and delete the cookie)
*
* @param e string Unique name for each cookie
* @example esseKeksAuf('myLovelyCookie');
* @returns {Boolean|String} False if no cookie is found or cookie could not be deleted, otherwise the cookie content
*/
function esseKeksAuf(e){if((c=esseKeks(e))!=false){return (backeKeks(e,'',-1)==false)?c:false}else{return false}}

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

/*******************************************************************************
 * FIXES
 */
/* Internet Explorer 10 auf Windows 8 und Windows Phone 8 */
if (navigator.userAgent.match(/IEMobile\/10\.0/)) {
    var msViewportStyle = document.createElement('style')
    msViewportStyle.appendChild(
        document.createTextNode(
            '@-ms-viewport{width:auto!important}'
        )
    );
    document.querySelector('head').appendChild(msViewportStyle)
}
/* Android Stock Browser - Der Android Stock Browser wird bei <select>-Elementen die Symbole an der Seite ansonsten nicht richtig anzeigen, wenn ein border-radius und/oder border vorhanden ist. */
var nua = navigator.userAgent;
var isAndroid = (nua.indexOf('Mozilla/5.0') > -1 && nua.indexOf('Android ') > -1 && nua.indexOf('AppleWebKit') > -1 && nua.indexOf('Chrome') === -1);
if (isAndroid) {
    $('select.form-control').removeClass('form-control').css('width', '100%');
}


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
