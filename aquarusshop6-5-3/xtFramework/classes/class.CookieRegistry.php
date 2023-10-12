<?php
/*
 #########################################################################
 #                       xt:Commerce Shopsoftware
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # Copyright 2021 xt:Commerce GmbH All Rights Reserved.
 # This file may not be redistributed in whole or significant part.
 # Content of this file is Protected By International Copyright Laws.
 #
 # ~~~~~~ xt:Commerce Shopsoftware IS NOT FREE SOFTWARE ~~~~~~~
 #
 # https://www.xt-commerce.com
 #
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # @copyright xt:Commerce GmbH, www.xt-commerce.com
 #
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # xt:Commerce GmbH, Maximilianstrasse 9, 6020 Innsbruck
 #
 # office@xt-commerce.com
 #
 #########################################################################
 */

use GuzzleHttp\Cookie\SetCookie;

defined('_VALID_CALL') or die('Direct Access is not allowed.');


class CookieRegistry
{
    public const COOKIE_CONSENT_COOKIE_NAME = '_cookie_consent';

    /** @var CookieRegistry  */
    private static $_instance;

    private $_request_cookies = [];

    /** @var  array */
    private $_cookie_jars = [];

    /** @var CookieBanner */
    private $_cookieBanner;

    private $_isDirty = false;

    private $_emitCookieSettingsJs = true;

    private $_initFunctions = [];

    /**
     * @return bool
     */
    public static function emitCookieSettingsJs(): bool
    {
        return self::_instance()->_emitCookieSettingsJs;
    }

    /**
     * @param bool $emit
     * @return CookieRegistry
     */
    public static function setEmitCookieSettingsJs(bool $emit)
    {
        self::_instance()->_emitCookieSettingsJs = $emit;
        return self::_instance();
    }

    /**
     * @return CookieBanner
     */
    public function getCookieBanner(): CookieBanner
    {
        return $this->_cookieBanner;
    }

    /**
     * @param CookieBanner $cookieBanner
     * @return CookieRegistry
     */
    public static function setCookieBanner(CookieBanner $cookieBanner)
    {
        self::_instance()->_cookieBanner = $cookieBanner;
        return self::_instance();
    }

    /**
     * CookieRegistry constructor.
     * @param array $cookies
     */
    private function __construct($cookies = [])
    {
        $this->_request_cookies = $cookies;
        foreach(CookieType::types() as $type)
        {
            $this->_cookie_jars[$type] = [];
        }
    }

    /**
     * @return CookieRegistry
     */
    private static function _instance()
    {
        if(empty(self::$_instance))
            self::$_instance = new CookieRegistry();
        return self::$_instance;
    }

    /**
     * @param null $cookies
     */
    public static function init($cookies = null)
    {
        if(!is_array($cookies)) $cookies = [];
        self::_instance()->_request_cookies = $cookies;
        if(self::hasConsentCookie())
        {
            $c = new SetCookie();
            $c->setName(self::COOKIE_CONSENT_COOKIE_NAME);
            $c->setValue(self::_instance()->_request_cookies[self::COOKIE_CONSENT_COOKIE_NAME]);
            $c->setDomain($_SERVER["SERVER_NAME"]);
            $c->setSecure(checkHTTPS());
            $c->setHttpOnly(false);
            $c->setPath(_SRV_WEB);
            $ci = new CookieInfo(CookieType::FUNCTIONAL, $_SERVER["SERVER_NAME"], $c,
                'Zustimmung zur Verwendung von Cookies / Cookie consent');
            self::registerCookie($ci);
            $_SESSION[self::COOKIE_CONSENT_COOKIE_NAME] = '1';
        }
    }

    public static function hasConsentCookie()
    {
        return (is_array(self::_instance()->_request_cookies) && array_key_exists(self::COOKIE_CONSENT_COOKIE_NAME, self::_instance()->_request_cookies))
            || (is_array($_SESSION) && array_key_exists(self::COOKIE_CONSENT_COOKIE_NAME, $_SESSION));
    }

    public static function registerCookieScript(CookieInfo $ci)
    {
        self::registerCookie($ci, false);
    }

    public static function registerCookie(CookieInfo $ci, $send = false)
    {
        if(!CookieType::valid($ci->getType()))
            throw new InvalidArgumentException('['.$ci->getType() .'] is not an accepted cookie type');
        self::_instance()->_cookie_jars[$ci->getType()][] = $ci;
    }


    public static function sendCookies()
    {

    }

    public static function checkGetBanner()
    {
        if(!self::hasConsentCookie()) return self::getBanner();
        return '';
    }

    public static function getCookieAllowed($type)
    {
        if($type === CookieType::FUNCTIONAL)
            return true;
        if(!self::hasConsentCookie())
            return false;

        static $_cookie_consent = 0;
        if($_cookie_consent === 0)
        {
            $_cookie_consent = self::_instance()->_request_cookies[self::COOKIE_CONSENT_COOKIE_NAME];
            $_cookie_consent = json_decode(base64_decode($_cookie_consent));
        }

        if(empty($_cookie_consent) || empty($_cookie_consent->$type))
            return false;
        if(!isset($_cookie_consent->$type->allowed))
            return false;

        return $_cookie_consent->$type->allowed;
    }

    public static function getCookieSettings()
    {
        $settings = ['topics' => []];
        foreach(self::_instance()->_cookie_jars as $type => $cookies)
        {
            $type_settings = new stdClass();
            $type_settings->allowed = self::getCookieAllowed($type);
            $type_settings->cookies = self::_instance()->_cookie_jars[$type];
            $settings['topics'][$type] = $type_settings;
        }
        return $settings;
    }

    public static function getCookieSettingsJs()
    {
        $settings = self::getCookieSettings();
        $js = self::$js;
        $js .= "\n<script>\nconst COOKIE_CONSENT_COOKIE_NAME = \"".self::COOKIE_CONSENT_COOKIE_NAME."\";\nconst cookie_settings = ".json_encode($settings,JSON_PRETTY_PRINT).";\n</script>\n";
        return $js;
    }

    public static function getBannerHtml()
    {
        if (empty(self::_instance()->_cookieBanner )) return '';
        return self::_instance()->_cookieBanner::getBanner();
    }

    public static function registerInitFunction($fnName)
    {
        self::_instance()->_initFunctions[] = $fnName;
    }

    public static function getInitFunctions()
    {
        return self::_instance()->_initFunctions;
    }

    private static $js = '
<script>    
// returns the cookie with the given name,
// or undefined if not found
function getCookie(name) {
    let matches = document.cookie.match(new RegExp(
        "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, \'\\$1\') + "=([^;]*)"
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
        \'max-age\': -1
    })
}
</script>  
    ';

}
