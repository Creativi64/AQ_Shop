<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_it_recht_kanzlei/classes/constants.php';

if (ITRK_ACTIVATED == 1)
{
    define('PAGE_ITRK', _SRV_WEB_PLUGINS.'xt_it_recht_kanzlei/pages/itrk_request_processor.php');
}