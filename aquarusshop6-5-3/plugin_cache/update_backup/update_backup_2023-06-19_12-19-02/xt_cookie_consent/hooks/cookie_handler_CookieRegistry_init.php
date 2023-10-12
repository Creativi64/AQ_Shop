<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_cookie_consent/classes/class.xt_cookie_consent_banner.php';
CookieRegistry::setCookieBanner(new xt_cookie_consent_banner());
if(!CookieRegistry::hasConsentCookie()) CookieRegistry::setEmitCookieSettingsJs(true);