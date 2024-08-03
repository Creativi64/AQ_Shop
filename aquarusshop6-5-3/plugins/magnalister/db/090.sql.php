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
 * (c) 2010 - 2019 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */

$queries = array();
$functions = array();

function google_shopping_extend_tables_090() {
    if (!MagnaDB::gi()->columnExistsInTable('ShippingLabel', TABLE_MAGNA_GOOGLESHOPPING_PREPARE)){
        MagnaDB::gi()->query("ALTER TABLE `". TABLE_MAGNA_GOOGLESHOPPING_PREPARE ."` ADD COLUMN `ShippingLabel` VARCHAR(255) DEFAULT NULL ");
    }
    if (!MagnaDB::gi()->columnExistsInTable('WeightUnit', TABLE_MAGNA_GOOGLESHOPPING_PREPARE)){
        MagnaDB::gi()->query("ALTER TABLE `". TABLE_MAGNA_GOOGLESHOPPING_PREPARE ."` ADD COLUMN `WeightUnit` VARCHAR(8) DEFAULT NULL ");
    }
    if (!MagnaDB::gi()->columnExistsInTable('DimensionUnit', TABLE_MAGNA_GOOGLESHOPPING_PREPARE)){
        MagnaDB::gi()->query("ALTER TABLE `". TABLE_MAGNA_GOOGLESHOPPING_PREPARE ."` ADD COLUMN `DimensionUnit` VARCHAR(8) DEFAULT NULL ");
    }
    if (!MagnaDB::gi()->columnExistsInTable('Weight', TABLE_MAGNA_GOOGLESHOPPING_PREPARE)){
        MagnaDB::gi()->query("ALTER TABLE `". TABLE_MAGNA_GOOGLESHOPPING_PREPARE ."` ADD COLUMN `Weight` DECIMAL(15,2) DEFAULT NULL");
    }
    if (!MagnaDB::gi()->columnExistsInTable('Width', TABLE_MAGNA_GOOGLESHOPPING_PREPARE)){
        MagnaDB::gi()->query("ALTER TABLE `". TABLE_MAGNA_GOOGLESHOPPING_PREPARE ."` ADD COLUMN `Width` DECIMAL(6,2) DEFAULT NULL");
    }
    if (!MagnaDB::gi()->columnExistsInTable('Length', TABLE_MAGNA_GOOGLESHOPPING_PREPARE)){
        MagnaDB::gi()->query("ALTER TABLE `". TABLE_MAGNA_GOOGLESHOPPING_PREPARE ."` ADD COLUMN `Length` DECIMAL(6,2) DEFAULT NULL");
    }
    if (!MagnaDB::gi()->columnExistsInTable('Height', TABLE_MAGNA_GOOGLESHOPPING_PREPARE)){
        MagnaDB::gi()->query("ALTER TABLE `". TABLE_MAGNA_GOOGLESHOPPING_PREPARE ."` ADD COLUMN `Height` DECIMAL(6,2) DEFAULT NULL");
    }
    return;
}

$functions[] = 'google_shopping_extend_tables_090';
