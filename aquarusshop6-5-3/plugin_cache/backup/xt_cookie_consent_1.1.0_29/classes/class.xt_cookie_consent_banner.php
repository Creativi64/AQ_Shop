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

defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_cookie_consent/classes/constants.php';

class xt_cookie_consent_banner extends CookieBanner
{
    public static function getBanner()
    {
        if (constant('XT_COC_ACTIVATED') == 1)
        {
            global $_content;

            $content_array_coc = $_content->getHookContent((int)constant('XT_COC_CONTENT_CONSENT'), true);
            $tpl_data['content_body'] = $content_array_coc['content_body'];

            $content_array_privacy = $_content->getHookContent(2);

            $lifetime_days = str_replace(',','.', constant('XT_COC_COOKIE_LIFETIME'));
            $lifetime_days = floatval($lifetime_days);
            $lifetime_days = !empty($lifetime_days) ? $lifetime_days : 30;

            $tpl_data = [
                'content_title' => $content_array_coc['content_title'],
                'content_body'  => $content_array_coc['content_body'],
                'more_link'     => '<a href="'.$content_array_privacy['content_link'].'" target="_blank" class="">'.constant('TEXT_XT_COC_MORE_INFO').'</a>',
                'theme_id'      => constant('XT_COC_THEME'),
                'lifetime'      => intval($lifetime_days * 24 * 60 * 60).'',
                'theme'         => !empty((int) constant('XT_COC_THEME')) ? XT_COC_THEME : 3,
                'open'          => !CookieRegistry::hasConsentCookie(),
                'secure'        => checkHTTPS() ? true : false
            ];

            $tpl = 'cookie_consent.tpl.html';
            $template = new Template();
            $template->getTemplatePath($tpl, 'xt_cookie_consent', '', 'plugin');

            $tpl_html = $template->getTemplate('', $tpl, $tpl_data);

            echo $tpl_html;
        }

    }
}