<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS . 'xt_master_slave/classes/config.php';

global $db;

// fix #201 Kein Update der attributes_parent_id in xt_plg_products_to_attributes
// beim Verschieben eines Attributes in anderes Eltern-Attribute
$sql = 'UPDATE '.TABLE_PRODUCTS_TO_ATTRIBUTES.' pta
JOIN '.TABLE_PRODUCTS_ATTRIBUTES.' pa
ON pa.attributes_id = pta.attributes_id
SET pta.attributes_parent_id = pa.attributes_parent';
$db->Execute($sql);
