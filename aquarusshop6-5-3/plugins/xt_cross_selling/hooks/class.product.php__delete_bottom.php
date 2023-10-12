<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

if (is_int($id))
{
    $db->Execute("DELETE FROM " . TABLE_PRODUCTS_CROSS_SELL . " WHERE products_id = ?", [$id]);
    $db->Execute("DELETE FROM " . TABLE_PRODUCTS_CROSS_SELL . " WHERE products_id_cross_sell = ?", [$id]);
}