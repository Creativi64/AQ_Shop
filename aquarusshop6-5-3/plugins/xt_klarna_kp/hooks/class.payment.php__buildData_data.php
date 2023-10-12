<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $price, $page, $info, $language, $store_handler;

if($page->page_name == 'checkout' && $page->page_action == 'payment' && !empty($value) && !empty($plugin_payment_data))
{
    if ($value['payment_code'] == 'xt_klarna_kp' /*&& empty($_SESSION['kp_session'])*/)
    {
        unset($_SESSION['kp_session_coupon_code']); // kp_session_coupon_code set when coupon applied on confirmation page

        $payments_session = $_SESSION['kp_session'] = $plugin_payment_data->getPaymentsSession($_SESSION['cart'], $_SESSION['customer'], false);

        if(is_array($payments_session) &&
            array_key_exists('payment_method_categories', $payments_session) &&
            count($payments_session['payment_method_categories']))
        {
            static $kp_testmode_warning_outputted = false;
            if (constant('_KLARNA_CONFIG_KP_TESTMODE') == 1 && !$kp_testmode_warning_outputted)
            {
                $tplFile = 'warning-test-mode.tpl.html';
                $template = new Template();
                $template->getTemplatePath($tplFile, 'xt_klarna_kp', '', 'plugin');
                $tpl_data_warn = array(
                    'kp_session_id' => $payments_session['session_id']
                );
                $html = $template->getTemplate('', $tplFile, $tpl_data_warn);
                $info->_addInfo($html, 'error');
                $kp_testmode_warning_outputted = true;
            }

            $base_data = $data[$value['payment_code']];
            $payment_price = false; //$price->_getPrice(array('price' => $payment_price, 'tax_class' => $data['payment_tax_class'], 'curr' => true, 'format' => true, 'format_type' => 'default'));
            $payment_price['discount'] = 0;
            $payment_price['fee_percent'] = 0;
            $payment_price['percent'] = 0;
            $base_data['payment_price'] = $payment_price;

            unset($data[$value['payment_code']]);
            $pmcs = $payments_session['payment_method_categories'];
            $configured_pmcs = $plugin_payment_data->get_saved_klarna_kp_payment_methods(['store_id' => $store_handler->shop_id], true);

            $tmp_sub_data = [];

            foreach ($pmcs as $pmc)
            {
                if(!in_array($pmc['identifier'], $configured_pmcs)) continue;
                $tmp_sub_data[$pmc['identifier']] = $base_data;
                $tmp_sub_data[$pmc['identifier']]['klarna_payment_method'] = $pmc['identifier'];
                $tmp_sub_data[$pmc['identifier']]['payment_icon'] = $pmc["asset_urls"]["standard"];
                $tmp_sub_data[$pmc['identifier']]['payment_name'] = $pmc['name'];
            }

            foreach($configured_pmcs as $configured_pmc)
            {
                $data['xt_klarna_kp' . '_' . $configured_pmc] = $tmp_sub_data[$configured_pmc];
            }
        }
        else if(is_array($payments_session) &&
            array_key_exists('payment_method_categories', $payments_session) &&
            count($payments_session['payment_method_categories']) == 0)
        {
            unset($data['xt_klarna_kp']);
        }
        else if(empty($payments_session))
        {
            unset($data['xt_klarna_kp']);
        }
    }
}
