<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS . 'xt_master_slave/classes/class.xt_master_slave_products.php';
require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS . 'xt_master_slave/classes/class.xt_master_slave_functions.php';

global $p_info, $db, $msp;

if(!empty($msp))
{
    $variant = 0;
    if ($p_info->data["products_master_flag"] == 1)
    {
        $m_data = $p_info;
    }
    else
    {
        $variant = $p_info;
        $master_model = $p_info->data['products_master_model'];
        $m_data = xt_master_slave_functions::getMasterData($master_model);
    }

    $showList = xt_master_slave_functions::getOverrideSetting(_PLUGIN_MASTER_SLAVE_SHOW_SLAVE_LIST, 'ms_show_slave_list', $m_data);
    // ist das eine konkrete Variante?
    if ($p_info->data["products_master_flag"] == 0
        && xt_master_slave_functions::getOverrideSetting(XT_MASTER_SLAVE_FILTERLIST_HIDE_ON_PRODUCT, 'ms_filter_slave_list_hide_on_product', $m_data))
    {
        $showList = false;
    }

    if ($showList)
    {
        $showProductList = $msp->getProductList();

        echo $showProductList;
    }
}
