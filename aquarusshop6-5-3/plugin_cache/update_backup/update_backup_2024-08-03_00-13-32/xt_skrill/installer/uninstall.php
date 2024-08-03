<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

include_once _SRV_WEBROOT . _SRV_WEB_PLUGINS . 'xt_skrill/hooks/page_registry.php_bottom.php';

global $db, $filter;

$db->Execute("DROP TABLE IF EXISTS " . TABLE_SKRILL_REFUNDS );

$db->Execute("DELETE FROM ".TABLE_ADMIN_NAVIGATION." WHERE text in ('skrill_transactions','skrill_refunds')");
