<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');


if ($form_grid->code == 'plugin_installed' && array_key_exists('conf_ITRK_API_URL_STORE_INDEPENDENT_shop_1', $form_grid->params['header']))
{
    global $store_handler, $xtLink;
    $stores = $store_handler->getStores();

    $link = $xtLink->_adminlink(array('page' => 'ITRK'));

    foreach ($stores as $store)
    {
        $form_grid->params['header']['conf_ITRK_API_URL_STORE_INDEPENDENT_shop_'.$store['id']]['value'] = $link;
        if ($store['id'] == 1) continue;
        $form_grid->params['header']['conf_ITRK_API_URL_STORE_INDEPENDENT_shop_'.$store['id']]['readonly'] = true;
    }


    echo '<script type="text/javascript">
            setTimeout(function(){
                $(\'input[name*="ITRK_API_URL"]\').attr("style", "width:80%");
                $(\'input[name*="ITRK_API_URL"]\').attr("readonly", "true");
            }, 400);</script>';
}
