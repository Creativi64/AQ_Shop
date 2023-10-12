<?php


defined('_VALID_CALL') or die('Direct Access is not allowed.');

if (constant('XT_PAYPAL_EXPRESS') == 1 && $_SESSION['paypalExpressCheckout'] == true && $page->page_action != 'success' && $page->page_action != 'process') {
    global $template;

    $tpl = 'xt_paypal_checkout_page.html';

    if(version_compare(_SYSTEM_VERSION, '6.1.1', '>'))
    {
        $shipping_data = $checkout->_selectShipping();
        $tpl_data['shipping_data'] = $shipping_data;
    }

    $template->getTemplatePath($tpl, 'xt_paypal', '', 'plugin');

    // unset shipping if not virtual and NOSHIPPING and let customer select one
    if($_SESSION['cart']->type!='virtual' && $_SESSION['selected_shipping']=='NOSHIPPING'){
        unset($_SESSION['selected_shipping']);
    }

    // xtc4ppp1 versandland nicht erlaubt -> zurück per button zu pp
    require_once _SRV_WEBROOT . 'plugins/xt_paypal/classes/class.paypal.php';
    $ppExpressButton = new paypal();

    $tpl_file_btn = 'button_paypal_express.html';
    $tpl_data_btn = [
        'pos'  => 'cart',
        'link' => $ppExpressButton->buildLink(),
        'img'  => $ppExpressButton->buildButton()
    ];

    $template = new Template();
    $template->getTemplatePath($tpl_file, 'xt_paypal', '', 'plugin');
    $button_str = $template->getTemplate('', $tpl_file_btn, $tpl_data_btn);

    $tpl_data['pp_express_button'] = $button_str;

    $brotkrumen = new brotkrumen();
    $brotkrumen->_addItem($xtLink->_link(array('page' => 'index', 'exclude' => 'cat_info_coID_mnf')), TEXT_HOME);
    $brotkrumen->_addItem($xtLink->_link(array('page' => 'checkout', '')), TEXT_PAYPAL_EXPRESS);
}
