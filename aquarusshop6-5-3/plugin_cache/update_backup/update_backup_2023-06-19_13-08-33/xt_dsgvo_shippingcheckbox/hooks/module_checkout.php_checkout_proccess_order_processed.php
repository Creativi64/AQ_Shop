<?php
defined('_VALID_CALL') or die('Direct Access is not allowed.');
if(!array_key_exists('xt_klarna_kco', $xtPlugin->active_modules) && $_POST['dsgvo_shipping_optin'] == 'on'){
    $db->Execute("UPDATE ".TABLE_ORDERS." SET dsgvo_shipping_optin='1' WHERE orders_id=?",array($_SESSION['last_order_id']));
}
