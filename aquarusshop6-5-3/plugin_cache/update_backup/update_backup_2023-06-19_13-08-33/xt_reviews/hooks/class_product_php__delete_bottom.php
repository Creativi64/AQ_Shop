<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $db;

/** @var $id int */

$db->Execute("DELETE FROM " . TABLE_PRODUCTS_REVIEWS . " WHERE products_id = '" .$id . "'");
