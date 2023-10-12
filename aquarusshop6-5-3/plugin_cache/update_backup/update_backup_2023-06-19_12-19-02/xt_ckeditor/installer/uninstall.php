<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $db;

$db->Execute("DELETE FROM " . TABLE_CONFIGURATION . " WHERE config_key='_SYSTEM_CKEDITOR_CONFIG'");
