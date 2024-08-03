<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $language;

if(!defined('MAIN_SLIM'))
{
    $logo_url = 'https://cdn.klarna.com/1.0/shared/image/generic/';

    switch (constant('_KLARNA_CONFIG_KP_LOGO_DESIGN'))
    {
        case 'badge-short-white':
        case 'badge-short-blue':
        case 'badge-long-white':
        case 'badge-long-blue':
            $logo_url .= 'badge/';
            break;
        default:
            $logo_url .= 'logo/';
    }

    switch ($language->code)
    {
        case 'de':
            $country_code = 'de_de';
            break;
        case 'fr':
            $country_code = 'fr_fr';
            break;
        default:
            $country_code = 'en_gb';
    }
    $logo_url .= $country_code . '/';


    switch (constant('_KLARNA_CONFIG_KP_LOGO_DESIGN'))
    {
        case 'badge-short-white':
        case 'badge-short-blue':
        case 'badge-long-white':
        case 'badge-long-blue':
            $logo_url .= 'checkout/';
            break;
        default:
            $logo_url .= 'basic/';
    }


    switch (constant('_KLARNA_CONFIG_KP_LOGO_DESIGN'))
    {
        case 'badge-short-white':
            $logo_url .= 'short-white.png';
            break;
        case 'badge-short-blue':
            $logo_url .= 'short-blue.png';
            break;
        case 'badge-long-white':
            $logo_url .= 'long-white.png';
            break;
        case 'badge-long-blue':
            $logo_url .= 'long-blue.png';
            break;
        case 'logo-black':
            $logo_url .= 'logo_black.png';
            break;
        default:
            $logo_url .= 'logo_white.png';
    }

    $logo_url = 'https://x.klarnacdn.net/payment-method/assets/badges/generic/klarna.svg';
    define('KLARNA_LOGO_URL', $logo_url);

}
