<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

if(DB_PREFIX!=''){
    $DB_PREFIX = DB_PREFIX . '_';
}else{
    define('DB_PREFIX','xt');
    $DB_PREFIX = DB_PREFIX . '_';
}

if (!defined('TABLE_PRODUCTS_TO_ATTRIBUTES')) define('TABLE_PRODUCTS_TO_ATTRIBUTES', $DB_PREFIX . 'plg_products_to_attributes');
if (!defined('TABLE_PRODUCTS_ATTRIBUTES')) define('TABLE_PRODUCTS_ATTRIBUTES', $DB_PREFIX . 'plg_products_attributes');
if (!defined('TABLE_PRODUCTS_ATTRIBUTES_DESCRIPTION')) define('TABLE_PRODUCTS_ATTRIBUTES_DESCRIPTION', $DB_PREFIX . 'plg_products_attributes_description');
if (!defined('TABLE_PRODUCTS_ATTRIBUTES_TEMPLATES')) define('TABLE_PRODUCTS_ATTRIBUTES_TEMPLATES', $DB_PREFIX.'plg_products_attributes_templates');

define('TABLE_TMP_PRODUCTS', $DB_PREFIX . 'tmp_products');
define('TABLE_TMP_PRODUCTS_TO_ATTRIBUTES', $DB_PREFIX . 'tmp_plg_products_to_attributes');

// Sortierung in der Ansicht 'Artikel Eigenschaften' bei Zuweisung von Eigenschaften zu Artikeln
// mgl Werte  name  model  sort_order
// @xt-dev sort_order funktioniert nicht richtig (durch extjs(json-encode?), int's werden als string's sortiert, zb: 1,11,100,2,201,3
// BACKEND_ATTRIBUTES_SORT_1 kann zb auch in config.php gesetzt werden um xt/plg-update-sicher zu sein, solange nicht klar ist,
// ob und wo/wie dieser Wert im Backend eingestellt werden kann
if (!defined('BACKEND_ATTRIBUTES_SORT_1')) define('BACKEND_ATTRIBUTES_SORT_1', 'name');
