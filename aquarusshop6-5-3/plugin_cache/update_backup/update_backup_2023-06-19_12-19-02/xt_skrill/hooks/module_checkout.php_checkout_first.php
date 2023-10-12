<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $xtPlugin, $page;

if(in_array($page->page_action, array('shipping', 'payment', 'confirmation')))
{
            unset($_SESSION['last_order_id']);
}

if(!empty($_POST['selected_payment_skrill']) && $_POST['selected_payment'] == 'xt_skrill' &&
    USER_POSITION == 'store' &&
    isset($xtPlugin->active_modules['xt_skrill']))
{
    include_once _SRV_WEBROOT . _SRV_WEB_PLUGINS . 'xt_skrill/classes/class.xt_skrill.php';

    if (xt_skrill::skrillActivated())
    {
        $_POST['selected_payment'] = $_POST['selected_payment_skrill'];
        $exp = explode(':', $_POST['selected_payment_skrill']);
        $_SESSION['skrill_selected_payment_sub'] = $exp[1];
    }

    if (isset($_SESSION['skrill_selected_payment_sub']))
    {
        $payment_name = xt_skrill::$payments[$_SESSION['skrill_selected_payment_sub']]['name'];
        define('TEXT_PAYMENT_' . strtoupper($_SESSION['skrill_selected_payment_sub']), $payment_name);
    }
}

if(isset($_SESSION['skrill_selected_payment_sub']) &&
    USER_POSITION == 'store' &&
    isset($xtPlugin->active_modules['xt_skrill']))
{
    include_once _SRV_WEBROOT . _SRV_WEB_PLUGINS . 'xt_skrill/classes/class.xt_skrill.php';

    $payment_name = xt_skrill::$payments[$_SESSION['skrill_selected_payment_sub']]['name'];
    define('TEXT_PAYMENT_' . strtoupper($_SESSION['skrill_selected_payment_sub']), $payment_name);
}
