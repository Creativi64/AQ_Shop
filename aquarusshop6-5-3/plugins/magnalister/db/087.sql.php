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
 * $Id: 020.sql.php 650 2011-01-08 22:30:52Z MaW $
 *
 * (c) 2012 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */

$queries = array();
$functions = array();

# eBay-Modul: 
function extend_ebay_properties_table_087() {
	// add eBay Product References
	// ErrorCode: if Error says lacking ePID, can be set to OK when ePID inserted
	if (!MagnaDB::gi()->columnExistsInTable('ErrorCode', TABLE_MAGNA_EBAY_PROPERTIES)){
		MagnaDB::gi()->query("ALTER TABLE `".TABLE_MAGNA_EBAY_PROPERTIES."` ADD COLUMN `ErrorCode` VARCHAR(10) NOT NULL DEFAULT '' AFTER `Verified`");
	}
	if (!MagnaDB::gi()->columnExistsInTable('productRequired', TABLE_MAGNA_EBAY_PROPERTIES)){
		MagnaDB::gi()->query("ALTER TABLE `".TABLE_MAGNA_EBAY_PROPERTIES."` ADD COLUMN `productRequired` ENUM('false', 'true') NOT NULL DEFAULT 'false' AFTER `ErrorCode`");
	}
	if (!MagnaDB::gi()->columnExistsInTable('ePID', TABLE_MAGNA_EBAY_PROPERTIES)){
		MagnaDB::gi()->query("ALTER TABLE `".TABLE_MAGNA_EBAY_PROPERTIES."` ADD COLUMN `ePID` VARCHAR(43) NOT NULL DEFAULT '' AFTER `productRequired`");
	}
	return;
}


# magnalister eBay Variations ePIDs

function create_magnalister_ebay_variations_epids() {
	MagnaDB::gi()->query("CREATE TABLE IF NOT EXISTS magnalister_ebay_variations_epids (
  `mpID` int(8) unsigned NOT NULL,
  `products_id` int(11) NOT NULL,
  `products_sku` varchar(150) NOT NULL DEFAULT '',
  `marketplace_id` varchar(32) NOT NULL DEFAULT '',
  `marketplace_sku` varchar(150) NOT NULL DEFAULT '',
  `ePID` varchar(43) NOT NULL DEFAULT '',
  KEY `mpID` (`mpID`),
  KEY `products_id` (`products_id`),
  KEY `products_sku` (`products_sku`),
  KEY `marketplace_id` (`marketplace_id`),
  KEY `marketplace_sku` (`marketplace_sku`)
)");
	return;
}

$functions[] = 'extend_ebay_properties_table_087';
$functions[] = 'create_magnalister_ebay_variations_epids';
