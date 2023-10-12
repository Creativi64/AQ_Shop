<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

$db->Execute("DELETE FROM " . TABLE_PRODUCTS_SERIAL . " WHERE products_id = ? and orders_id='0' and orders_products_id='0'", array($id));
