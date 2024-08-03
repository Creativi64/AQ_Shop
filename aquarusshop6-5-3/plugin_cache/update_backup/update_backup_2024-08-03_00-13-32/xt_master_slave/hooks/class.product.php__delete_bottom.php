<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $db;

/** @var $id string|int */

$db->Execute("DELETE FROM " . TABLE_PRODUCTS_TO_ATTRIBUTES . " WHERE products_id =?",array((int)$id));
