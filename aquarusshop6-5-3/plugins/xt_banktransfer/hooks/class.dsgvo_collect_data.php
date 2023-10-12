<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

include_once _SRV_WEBROOT._SRV_WEB_PLUGINS.'xt_banktransfer/classes/class.xt_banktransfer_accounts.php';

unset($data_dsgvo);
$data_dsgvo = xt_banktransfer_accounts::getAccountsForCustomer($customers_id);
$data['xt_banktransfer'] = $data_dsgvo;