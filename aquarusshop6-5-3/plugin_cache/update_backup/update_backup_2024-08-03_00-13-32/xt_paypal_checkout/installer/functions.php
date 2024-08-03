<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

/**
 * @param $product_id int
 * @param $_plugin_code string
 */
function ppcInstallPaymentTypes($product_id, $_plugin_code, $mode, $payment_methods = [])
{
    global $db;

    $files = [];
    if(count($payment_methods))
    {
        foreach ($payment_methods as $pm)
        {
            $files[] = _SRV_WEBROOT._SRV_WEB_PLUGINS.'xt_paypal_checkout/installer/paymentTypes/'.$pm.'.xml';
        }
    }
    else
        $files = glob(_SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_paypal_checkout/installer/paymentTypes/*.xml');

    if (is_array($files)) {

        $plugin = new plugin();
        if( version_compare(_SYSTEM_VERSION, '6.5.4', '<') )
        {
            require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_paypal_checkout/installer/class.plugin_patch.php';
            $plugin = new plugin_patch();
        }
        foreach($files as $filename) {
            $data = $plugin->xmlToArray($filename);
            $paymentId = $plugin->installPayment($data['xtcommerceplugin'], $product_id, $_plugin_code, false);
            $db->Execute('UPDATE '.TABLE_PAYMENT. ' SET plugin_required = 0 WHERE payment_id = ?', [$paymentId]);
            if($mode == 'insert') ppcInstallPaymentCosts($paymentId, explode('.', basename($filename))[0]);
        }

    }
    $db->Execute('DELETE FROM '.TABLE_CONFIGURATION_PAYMENT." WHERE config_key = 'XT_PAYPAL_CHECKOUT_ORDER_STATUS_NEW'");
}

function ppcInstallPaymentCosts($payment_id, $payment_type)
{
    global $db;

    /** @var int $payment_id */
    if($payment_type == 'pui')
    {
        $db->Execute("INSERT INTO " . TABLE_PAYMENT_COST . " (`payment_id`, `payment_geo_zone`, `payment_country_code`, `payment_type_value_from`, `payment_type_value_to`, `payment_price`,`payment_allowed`) VALUES(" . $payment_id . ", 0, 'DE', 5, 2500.00, 0, 1);");
    }
    else if($payment_type == 'giropay')
    {
        $db->Execute("INSERT INTO " . TABLE_PAYMENT_COST . " (`payment_id`, `payment_geo_zone`, `payment_country_code`, `payment_type_value_from`, `payment_type_value_to`, `payment_price`,`payment_allowed`) VALUES(" . $payment_id . ", 0, 'DE', 1, 10000.00, 0, 1);");
    }
    else if($payment_type == 'eps')
    {
        $db->Execute("INSERT INTO " . TABLE_PAYMENT_COST . " (`payment_id`, `payment_geo_zone`, `payment_country_code`, `payment_type_value_from`, `payment_type_value_to`, `payment_price`,`payment_allowed`) VALUES(" . $payment_id . ", 0, 'AT', 1, 10000.00, 0, 1);");
    }
    else
    {
        $db->Execute("INSERT INTO " . TABLE_PAYMENT_COST . " (`payment_id`, `payment_geo_zone`, `payment_country_code`, `payment_type_value_from`, `payment_type_value_to`, `payment_price`,`payment_allowed`) VALUES(" . $payment_id . ", 24, '', 0.01, 10000.00, 0, 1);");
        $db->Execute("INSERT INTO " . TABLE_PAYMENT_COST . " (`payment_id`, `payment_geo_zone`, `payment_country_code`, `payment_type_value_from`, `payment_type_value_to`, `payment_price`,`payment_allowed`) VALUES(" . $payment_id . ", 25, '', 0.01, 10000.00, 0, 1);");
        $db->Execute("INSERT INTO " . TABLE_PAYMENT_COST . " (`payment_id`, `payment_geo_zone`, `payment_country_code`, `payment_type_value_from`, `payment_type_value_to`, `payment_price`,`payment_allowed`) VALUES(" . $payment_id . ", 26, '', 0.01, 10000.00, 0, 1);");
        $db->Execute("INSERT INTO " . TABLE_PAYMENT_COST . " (`payment_id`, `payment_geo_zone`, `payment_country_code`, `payment_type_value_from`, `payment_type_value_to`, `payment_price`,`payment_allowed`) VALUES(" . $payment_id . ", 27, '', 0.01, 10000.00, 0, 1);");
        $db->Execute("INSERT INTO " . TABLE_PAYMENT_COST . " (`payment_id`, `payment_geo_zone`, `payment_country_code`, `payment_type_value_from`, `payment_type_value_to`, `payment_price`,`payment_allowed`) VALUES(" . $payment_id . ", 28, '', 0.01, 10000.00, 0, 1);");
        $db->Execute("INSERT INTO " . TABLE_PAYMENT_COST . " (`payment_id`, `payment_geo_zone`, `payment_country_code`, `payment_type_value_from`, `payment_type_value_to`, `payment_price`,`payment_allowed`) VALUES(" . $payment_id . ", 29, '', 0.01, 10000.00, 0, 1);");
        $db->Execute("INSERT INTO " . TABLE_PAYMENT_COST . " (`payment_id`, `payment_geo_zone`, `payment_country_code`, `payment_type_value_from`, `payment_type_value_to`, `payment_price`,`payment_allowed`) VALUES(" . $payment_id . ", 30, '', 0.01, 10000.00, 0, 1);");
        $db->Execute("INSERT INTO " . TABLE_PAYMENT_COST . " (`payment_id`, `payment_geo_zone`, `payment_country_code`, `payment_type_value_from`, `payment_type_value_to`, `payment_price`,`payment_allowed`) VALUES(" . $payment_id . ", 31, '', 0.01, 10000.00, 0, 1);");
    }

}

function ppcInstallMailTemplates()
{
    global $db, $language;

    $db->Execute("INSERT INTO " . TABLE_MAIL_TEMPLATES . " (`tpl_type`) VALUES ('paypal_checkout_pui_bank_data');");
    $emailTemplateId = $db->GetOne("SELECT tpl_id FROM " . TABLE_MAIL_TEMPLATES . " WHERE tpl_type='paypal_checkout_pui_bank_data'");

    $languages = $language->_getLanguageList();
    if (count($languages)) {

        foreach ($languages as $key => $val)
        {
            $keyExists =  $db->GetOne("SELECT 1 FROM " . TABLE_MAIL_TEMPLATES_CONTENT . " WHERE tpl_id=? AND language_code=?", array( $emailTemplateId, $val['code']));
            if($keyExists) continue;

            $tplBodyHtml = file_get_contents(_SRV_WEBROOT._SRV_WEB_PLUGINS.'xt_paypal_checkout/installer/mail/'.$val['code'].'/tpl_'.$val['code'].'_mail_body_html.html', true);
            $tplBodyTxt =  file_get_contents(_SRV_WEBROOT._SRV_WEB_PLUGINS.'xt_paypal_checkout/installer/mail/'.$val['code'].'/tpl_'.$val['code'].'_mail_body_txt.html', true);
            $tplSubject =  file_get_contents(_SRV_WEBROOT._SRV_WEB_PLUGINS.'xt_paypal_checkout/installer/mail/'.$val['code'].'/tpl_'.$val['code'].'_mail_subject.html', true);

            $insert_array = array();
            $insert_array['tpl_id'] = $emailTemplateId;
            $insert_array['language_code'] = $val['code'];
            $insert_array['mail_body_html'] = $tplBodyHtml;
            $insert_array['mail_body_txt'] = $tplBodyTxt;
            $insert_array['mail_subject'] = $tplSubject;
            $db->AutoExecute(TABLE_MAIL_TEMPLATES_CONTENT, $insert_array);

        }
    }
}

function ppcInstallPaymentIcon()
{
    if(!file_exists(_SRV_WEBROOT.'media/payment/xt_paypal_checkout.png'))
        copy(_SRV_WEBROOT._SRV_WEB_PLUGINS.'xt_paypal_checkout/images/xt_paypal_checkout.png',
            _SRV_WEBROOT.'media/payment/xt_paypal_checkout.png');
    if(!file_exists(_SRV_WEBROOT.'media/payment/xt_paypal_checkout_applepay.png'))
        copy(_SRV_WEBROOT._SRV_WEB_PLUGINS.'xt_paypal_checkout/installer/paymentLogos/xt_paypal_checkout_applepay.png',
            _SRV_WEBROOT.'media/payment/xt_paypal_checkout_applepay.png');
    if(!file_exists(_SRV_WEBROOT.'media/payment/xt_paypal_checkout_googlepay.png'))
        copy(_SRV_WEBROOT._SRV_WEB_PLUGINS.'xt_paypal_checkout/installer/paymentLogos/xt_paypal_checkout_googlepay.png',
            _SRV_WEBROOT.'media/payment/xt_paypal_checkout_googlepay.png');
}

function ppcFixDependencies()
{
    // klarna KP  der form submit handler stört pp rechungskauf
    // wenn keine Tel# oder datum blockt kp das formular
    if(file_exists(_SRV_WEBROOT._SRV_WEB_PLUGINS.'xt_klarna_kp/javascript/klarna_kp.js'))
    {
        rename(_SRV_WEBROOT._SRV_WEB_PLUGINS.'xt_klarna_kp/javascript/klarna_kp.js',
                 _SRV_WEBROOT._SRV_WEB_PLUGINS.'xt_klarna_kp/javascript/_klarna_kp.js');
        copy(_SRV_WEBROOT._SRV_WEB_PLUGINS.'xt_paypal_checkout/installer/dependencies/xt_klarna_kp/javascript/klarna_kp.js',
               _SRV_WEBROOT._SRV_WEB_PLUGINS.'xt_klarna_kp/javascript/klarna_kp.js');
    }
}

function ppcUpdateRefundStatus()
{
    global $db;

    $fully_refunded_status = $db->GetOne("SELECT status_id FROM " . TABLE_SYSTEM_STATUS_DESCRIPTION . " WHERE status_name = 'Zurückgezahlt'");
    if (empty($fully_refunded_status))
    {
        $fully_refunded_status = 17;
    }
    $db->Execute("UPDATE ".TABLE_CONFIGURATION_PAYMENT." SET config_value = ? WHERE config_key LIKE 'XT_PAYPAL_CHECKOUT%FULLY_REFUNDED'", [$fully_refunded_status]);

    $partially_refunded_status = $db->GetOne("SELECT status_id FROM " . TABLE_SYSTEM_STATUS_DESCRIPTION . " WHERE status_name = 'Teilweise zurückgezahlt'");
    if (empty($partially_refunded_status))
    {
        $partially_refunded_status = 17;
    }
    $db->Execute("UPDATE ".TABLE_CONFIGURATION_PAYMENT." SET config_value = ? WHERE config_key LIKE 'XT_PAYPAL_CHECKOUT%PARTIALLY_REFUNDED'", [$partially_refunded_status]);
}

function ppcClearLanguageCache()
{
    // _cache_xt.language_content
    try
    {
        $files = glob(_SRV_WEBROOT . 'cache/_cache_xt.language_content*');
        array_map('unlink', $files);
    }
    catch(Exception $e){ error_log('ppcClearLanguageCache: '.$e->getMessage()); }
}
