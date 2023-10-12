<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $db;

$db->Execute("DELETE FROM ".TABLE_EASY_CREDIT_FINANCING." WHERE orders_id=?", array($orders_id));