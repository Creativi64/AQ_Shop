<?php
/**
 * 888888ba                 dP  .88888.                    dP
 * 88    `8b                88 d8'   `88                   88
 * 88aaaa8P' .d8888b. .d888b88 88        .d8888b. .d8888b. 88  .dP  .d8888b.
 * 88   `8b. 88ooood8 88'  `88 88   YP88 88ooood8 88'  `"" 88888"   88'  `88
 * 88     88 88.  ... 88.  .88 Y8.   .88 88.  ... 88.  ... 88  `8b. 88.  .88
 * dP     dP `88888P' `88888P8  `88888'  `88888P' `88888P' dP   `YP `88888P'
 *
 *                          m a g n a l i s t e r
 *                                      boost your Online-Shop
 *
 * -----------------------------------------------------------------------------
 * $Id: 103.sql.php 650 2011-01-08 22:30:52Z edeja $
 *
 * (c) 2012 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */

$functions = array();

# eBay-Modul:
function remove_ebay_properties_table_column() {
    if (MagnaDB::gi()->columnExistsInTable('HitCounter', TABLE_MAGNA_EBAY_PROPERTIES)) {
        MagnaDB::gi()->query('ALTER TABLE `'.TABLE_MAGNA_EBAY_PROPERTIES.'` DROP COLUMN `HitCounter`');
    }
}

$functions[] = 'remove_ebay_properties_table_column';

