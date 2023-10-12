<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

// check if xt_banktransfer payment method
if ($this->order_data['payment_code']=='xt_banktransfer' && XT_BANKTRANSFER_SEND_MANDATE=='true') {
    include_once _SRV_WEBROOT._SRV_WEB_PLUGINS.'xt_banktransfer/classes/class.xt_banktransfer.php';
    $xt_banktransfer = new xt_banktransfer;
    $pdf_mandat=$xt_banktransfer->getMandate($this->order_data['orders_id']);
    if ($pdf_mandat!=false) $ordermail->AddStringAttachment($pdf_mandat, 'sepa_mandat_' . $this->order_data['orders_id'] . '.pdf');
}
