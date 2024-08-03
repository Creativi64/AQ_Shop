<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $db;

$db->Execute('UPDATE '.TABLE_PLUGIN_CONFIGURATION.' SET config_value = 0 WHERE config_key = \'XT_GCT_DO_NOT_TRACK\'');
