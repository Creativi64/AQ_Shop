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

function smarty_function_retail_products_price($params, & $smarty)
{
    global $price, $customers_status, $language;

    static $cgroup_allowed = null;
    static $force_gross = null; // show gross price even is net customer

    if ($cgroup_allowed == null)
    {
        if (!empty($params['cgroup']))
        {
            $cgroups = explode(',', $params['cgroup']);
            $cgroups = array_map('trim', $cgroups);

            if (!in_array($customers_status->customers_status_id, $cgroups))
            {
                $cgroup_allowed = false;
            }
            else
                $cgroup_allowed = true;
        }
        else
            $cgroup_allowed = true;
    }

    if ($force_gross == null)
    {
        if (!empty($params['force_gross']))
        {
            if($params['force_gross'] == '*')
            {
                $force_gross = true;
            }
            else {
                $cgroups = explode(',', $params['force_gross']);
                $cgroups = array_map('trim', $cgroups);

                if (!in_array($customers_status->customers_status_id, $cgroups))
                {
                    $force_gross = false;
                }
                else
                {
                    $force_gross = true;
                }
            }
        }
        else
            $force_gross = false;
    }

    if($cgroup_allowed)
    {
        $product = null;

        if ($params['position'] == 'listing')
        {
            $product = $smarty->getTemplateVars('module_data');
        }
        if ($params['position'] == 'product')
        {
            $product = $smarty->getTemplateVars('product');
        }

        if (is_array($product) && !empty($product['products_price']["original_price"]) && !empty($product['products_price']["plain"])
            && round($product['products_price']['plain'], 2) < round($product['products_price']["original_price"], 2))
        {
            $price_plain = $product['products_price']['original_price'];
            $price_otax = isset($product['products_price']['original_price_otax']) ? $product['products_price']['original_price_otax'] : 0;

            $price_plain = (string)$price_plain;
            $price_plain = (float)$price_plain;

            $is_net = $customers_status->customers_status_show_price_tax == 0;
            if($force_gross && $customers_status->customers_status_show_price_tax == 0)
            {
                $tax_rate = $product["products_tax_info"]["tax"];
                $price_plain = $price_plain * (1 + $tax_rate/100);
                $is_net = false;
            }

            $Fprice = $price->_StyleFormat($price_plain);

            $price_otax = (string)$price_otax;
            $price_otax = (float)$price_otax;
            $Fprice_otax = $price->_StyleFormat($price_otax);

            $tpl_data = array('is_net' => $is_net, 'PRICE' => array('formated' => $Fprice, 'plain' => $price_plain), 'PRICE_OTAX' => array('formated' => $Fprice_otax, 'plain' => $price_otax, 'date_available' => '', 'date_expired' => ''));
            $tpl = 'retail_products_price.html';

            $template = new Template();

            if(!defined('TEXT_TAX_EXC'))
            {
                if($language->content_language == 'de') define('TEXT_TAX_EXC', 'Netto');
                else define('TEXT_TAX_EXC', 'net');
            }
            if(!defined('TEXT_TAX_INC'))
            {
                if($language->content_language == 'de') define('TEXT_TAX_INC', 'Brutto');
                else define('TEXT_TAX_INC', 'gross');
            }
            if(!defined('TEXT_RETAIL_PRICE'))
            {
                if($language->content_language == 'de') define('TEXT_RETAIL_PRICE', 'VK Endkunden');
                else define('TEXT_RETAIL_PRICE', 'Retail price');
            }
            $tpl_price = $template->getTemplate('price_smarty', '/' . _SRV_WEB_CORE . 'pages/price/' . $tpl, $tpl_data);

            echo $tpl_price;
        }
    }
}

