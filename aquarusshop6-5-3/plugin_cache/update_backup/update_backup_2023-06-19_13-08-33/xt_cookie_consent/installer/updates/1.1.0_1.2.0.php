<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

/** @var $output string */

if(version_compare(constant('_SYSTEM_VERSION'), '6.5.4', '<'))
{
    $f_source = _SRV_WEBROOT . _SRV_WEB_PLUGINS . 'xt_cookie_consent/installer/updates/class.CookieRegistry.php';
    $f_target = _SRV_WEBROOT . _SRV_WEB_FRAMEWORK. 'classes/class.CookieRegistry.php';

    $renamed = rename($f_target, $f_target. '.cookie_consent_updater');

    $copied = false;

    if($renamed)
    {
        $copied = copy($f_source, $f_target);
    }

    if (!$renamed || !$copied)
    {
        $hintPath = _SRV_WEB_CORE . _SRV_WEB_FRAMEWORK. 'classes/class.CookieRegistry.php';
        $hint = "Updater tried to copy new class file but failed.<br />You have to copy manually plugins/xt_cookie_consent/installer/updates/class.CookieRegistry.php to <br/> $hintPath";
        $output .= "<br /><div style='border:solid 1px #fecf43;background: #ffe086; padding:10px;'>";
        $output .= $hint;
        $output .= "</div>";
    }
}
