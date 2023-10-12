<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

if($this->order_data["shop_id"])
{
    global $db;

    require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS . 'xt_orders_invoices/classes/constants.php';

    $sql = "SELECT config_value FROM " . TABLE_PLUGIN_CONFIGURATION . " WHERE config_key=? and shop_id=?";
    $trigger_status_generate = $db->GetOne($sql, array('XT_ORDERS_INVOICE_STATUS_GENERATE', $this->order_data["shop_id"]));

    $exists = $db->GetOne("SELECT 1 FROM ".TABLE_ORDERS_STATUS_HISTORY." WHERE orders_id = ? AND orders_status_id = ?",[$this->oID, $trigger_status_generate]);

    /** @var $status string */
    if ($status == $trigger_status_generate && !$exists)
    {

        require_once _SRV_WEBROOT . 'plugins/xt_orders_invoices/classes/class.xt_orders_invoices.php';
        $invoice = new xt_orders_invoices();

        $invoice->autoGenerate($this->oID, '');

        $sql = "SELECT config_value FROM " . TABLE_PLUGIN_CONFIGURATION . " WHERE config_key=? and shop_id=?";
        $trigger_status_send = $db->GetOne($sql, array('XT_ORDERS_INVOICE_AUTO_SEND_ORDERS', $this->order_data["shop_id"]));

        if ($trigger_status_send == 'true')
        {
            global $db;
            $sql = "SELECT o.customers_email_address FROM " . TABLE_ORDERS . " o LEFT JOIN " . TABLE_ORDERS_INVOICES . " oi on o.orders_id = oi.orders_id WHERE o.`orders_id`=?";
            $email = $db->GetOne($sql, array($this->oID));
            $sql = "SELECT oi.invoice_id FROM " . TABLE_ORDERS . " o LEFT JOIN " . TABLE_ORDERS_INVOICES . " oi on o.orders_id = oi.orders_id WHERE o.`orders_id`=?";
            $invoice_id = $db->GetOne($sql, array($this->oID));

            $invoice->sendPdf($invoice_id, $email);
        }
    }
}
