<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_cookie_consent/classes/constants.php';

switch ($request['get'])
{
    case 'url_XT_COC_THEME':
        $data=array();

        $data[] =  array(
            'id' => '1',
            'name' => __define('TEXT_XT_COC_THEME_1'));
        $data[] =  array(
            'id' => '2',
            'name' => __define('TEXT_XT_COC_THEME_2'));
        $data[] =  array(
            'id' => '3',
            'name' => __define('TEXT_XT_COC_THEME_3'));

        $result=$data;
        break;

}