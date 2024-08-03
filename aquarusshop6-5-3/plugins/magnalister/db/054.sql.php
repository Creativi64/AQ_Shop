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
 * $Id$
 *
 * (c) 2010 - 2014 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */

$queries = array();
$functions = array();

function md_db_update_54_1() {
	if (MagnaDB::gi()->columnExistsInTable('Title', TABLE_MAGNA_FYNDIQ_PROPERTIES)) {
		MagnaDB::gi()->query("ALTER TABLE `".TABLE_MAGNA_FYNDIQ_PROPERTIES."` CHANGE COLUMN `Title` `Title` VARCHAR(64) DEFAULT NULL ");
	}
}
$functions[] = 'md_db_update_54_1';

function md_db_update_54_2() {
	if (MagnaDB::gi()->columnExistsInTable('Description', TABLE_MAGNA_FYNDIQ_PROPERTIES)) {
		MagnaDB::gi()->query("ALTER TABLE `".TABLE_MAGNA_FYNDIQ_PROPERTIES."` CHANGE COLUMN `Description` `Description` TEXT DEFAULT NULL ");
	}
}
$functions[] = 'md_db_update_54_2';

function md_db_update_54_3() {
	if (MagnaDB::gi()->columnExistsInTable('Tax', TABLE_MAGNA_FYNDIQ_PROPERTIES)) {
		MagnaDB::gi()->query('ALTER TABLE `'.TABLE_MAGNA_FYNDIQ_PROPERTIES.'` DROP COLUMN `Tax`');
	}
}
$functions[] = 'md_db_update_54_3';

function md_db_update_54_4() {
	if (MagnaDB::gi()->columnExistsInTable('Brand', TABLE_MAGNA_FYNDIQ_PROPERTIES)) {
		MagnaDB::gi()->query('ALTER TABLE `'.TABLE_MAGNA_FYNDIQ_PROPERTIES.'` DROP COLUMN `Brand`');
	}
}
$functions[] = 'md_db_update_54_4';

function md_db_update_54_5() {
	if (MagnaDB::gi()->columnExistsInTable('IdentifierType', TABLE_MAGNA_FYNDIQ_PROPERTIES)) {
		MagnaDB::gi()->query('ALTER TABLE `'.TABLE_MAGNA_FYNDIQ_PROPERTIES.'` DROP COLUMN `IdentifierType`');
	}
}
$functions[] = 'md_db_update_54_5';

function md_db_update_54_6() {
	if (MagnaDB::gi()->columnExistsInTable('IdentifierValue', TABLE_MAGNA_FYNDIQ_PROPERTIES)) {
		MagnaDB::gi()->query('ALTER TABLE `'.TABLE_MAGNA_FYNDIQ_PROPERTIES.'` DROP COLUMN `IdentifierValue`');
	}
}
$functions[] = 'md_db_update_54_6';

function md_db_update_54_7() {
	if (MagnaDB::gi()->columnExistsInTable('Attributes', TABLE_MAGNA_FYNDIQ_PROPERTIES)) {
		MagnaDB::gi()->query('ALTER TABLE `'.TABLE_MAGNA_FYNDIQ_PROPERTIES.'` DROP COLUMN `Attributes`');
	}
}
$functions[] = 'md_db_update_54_7';

function md_db_update_54_8() {
	if (MagnaDB::gi()->columnExistsInTable('Subtitle', TABLE_MAGNA_FYNDIQ_PROPERTIES)) {
		MagnaDB::gi()->query('ALTER TABLE `'.TABLE_MAGNA_FYNDIQ_PROPERTIES.'` DROP COLUMN `Subtitle`');
	}
}
$functions[] = 'md_db_update_54_8';
