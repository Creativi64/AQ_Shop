<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $db;

$db->Execute('DELETE FROM  '.TABLE_PRODUCTS_CROSS_SELL.'
WHERE NOT EXISTS
(
    SELECT  NULL
        FROM    '.TABLE_PRODUCTS.' p
        WHERE   p.products_id = '.TABLE_PRODUCTS_CROSS_SELL.'.products_id
        )');

$db->Execute('DELETE FROM  '.TABLE_PRODUCTS_CROSS_SELL.'
WHERE NOT EXISTS
(
    SELECT  NULL
        FROM    '.TABLE_PRODUCTS.' p
        WHERE   p.products_id = '.TABLE_PRODUCTS_CROSS_SELL.'.products_id_cross_sell
        )');

/** test **/
/**
 *

SELECT  distinct cs.products_id
FROM    xt_products_cross_sell cs
WHERE   NOT EXISTS
(
SELECT  NULL
FROM    xt_products p
WHERE   p.products_id = cs.products_id
)

 *
 *

SELECT  distinct cs.products_id_cross_sell
FROM    xt_products_cross_sell cs
WHERE   NOT EXISTS
(
SELECT  NULL
FROM    xt_products p
WHERE   p.products_id = cs.products_id_cross_sell
)

 *
 */
