<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

$db->CacheExecute("DELETE FROM " . TABLE_PRODUCTS_TO_ATTRIBUTES . " WHERE products_id =?",array((int)$id));
