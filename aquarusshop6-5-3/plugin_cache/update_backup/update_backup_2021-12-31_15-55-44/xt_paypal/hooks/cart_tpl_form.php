<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

if (constant('XT_PAYPAL_EXPRESS') == 1) {

    $payment = new payment();

    $payment->_payment();
    $data_array = $payment->_getPossiblePayment();

    $show_paypalexpress = false;
    foreach ($data_array as $k => $v) {
        if ($v['payment_code'] == 'xt_paypal') {
            $show_paypalexpress = true;
        }
    }

    if ($show_paypalexpress) {
        global $xtPlugin;
        require_once _SRV_WEBROOT . 'plugins/xt_paypal/classes/class.paypal.php';
        $ppExpressButton = new paypal();

        $tpl_file = 'button_paypal_express.html';
        $tpl_data = [
            'pos'  => 'cart',
            'link' => $ppExpressButton->buildLink(),
            'img'  => $ppExpressButton->buildButton()
        ];

        $template = new Template();

        $template->getTemplatePath($tpl_file, 'xt_paypal', '', 'plugin');
        $button_str = $template->getTemplate('', $tpl_file, $tpl_data);

        ($plugin_code = $xtPlugin->PluginCode('paypal_hooks:cart_tpl_form_bottom')) ? eval($plugin_code) : false;
        echo $button_str;
    }
}
