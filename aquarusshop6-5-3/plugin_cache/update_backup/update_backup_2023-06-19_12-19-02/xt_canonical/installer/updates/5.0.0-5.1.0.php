<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $db;

$db->Execute('UPDATE '.TABLE_PLUGIN_CONFIGURATION.' SET config_value=1 WHERE config_key=? AND config_value=?',
    array('XT_CANONICAL_APPLY_TO_ALL_SLAVES', 'true'));
$db->Execute('UPDATE '.TABLE_PLUGIN_CONFIGURATION.' SET config_value=0 WHERE config_key=? AND config_value=?',
    array('XT_CANONICAL_APPLY_TO_ALL_SLAVES', 'false'));
