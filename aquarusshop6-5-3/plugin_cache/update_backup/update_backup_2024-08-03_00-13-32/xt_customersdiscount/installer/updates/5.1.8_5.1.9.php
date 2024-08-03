<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $db;

$db->Execute("UPDATE ".TABLE_CUSTOMERS_STATUS." SET customers_discount = '' WHERE customers_discount like 'new'");
