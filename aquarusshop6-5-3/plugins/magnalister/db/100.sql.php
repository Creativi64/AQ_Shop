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
 * (c) 2010 - 2021 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */

$queries = array();
$functions = array();

function md_db_update_100() {
	if (!MagnaDB::gi()->columnExistsInTable('ShippingTimeMin', TABLE_MAGNA_HITMEISTER_PREPARE)) {
		MagnaDB::gi()->query("ALTER TABLE `".TABLE_MAGNA_HITMEISTER_PREPARE."` ADD COLUMN `ShippingTimeMin` INT(4) DEFAULT 1 AFTER `ShippingTime`");
		MagnaDB::gi()->query("ALTER TABLE `".TABLE_MAGNA_HITMEISTER_PREPARE."` ADD COLUMN `ShippingTimeMax` INT(4) DEFAULT 999 AFTER `ShippingTimeMin`");
                $aCodeToMinMax = array (
                    'a' => array('ShippingTimeMin' => 1, 'ShippingTimeMax' => 1),
                    'b' => array('ShippingTimeMin' => 1, 'ShippingTimeMax' => 3),
                    'c' => array('ShippingTimeMin' => 4, 'ShippingTimeMax' => 6),
                    'd' => array('ShippingTimeMin' => 7, 'ShippingTimeMax' => 10),
                    'e' => array('ShippingTimeMin' => 11, 'ShippingTimeMax' => 14),
                    'f' => array('ShippingTimeMin' => 15, 'ShippingTimeMax' => 28),
                    'g' => array('ShippingTimeMin' => 29, 'ShippingTimeMax' => 49),
                    'h' => array('ShippingTimeMin' => 21, 'ShippingTimeMax' => 70),
                    'i' => array('ShippingTimeMin' => 50, 'ShippingTimeMax' => 70)
                );
                foreach ($aCodeToMinMax as $sCode => $aMinMax) {
		    MagnaDB::gi()->update(TABLE_MAGNA_HITMEISTER_PREPARE,
                        $aMinMax,
                        array (
                            'ShippingTime' => $sCode
                    ));
                } 
                // not needed anymore
                MagnaDB::gi()->query("ALTER TABLE `".TABLE_MAGNA_HITMEISTER_PREPARE."` CHANGE COLUMN `ShippingTime` `ShippingTime` CHAR(1) DEFAULT NULL");
                // we may have multiple accounts
                $aDefaultShippingTimes = MagnaDB::gi()->fetchArray("SELECT * FROM ".TABLE_MAGNA_CONFIG." WHERE mkey = 'hitmeister.shippingtime'");
                foreach ($aDefaultShippingTimes as $aDefaultShippingTime) {
                    MagnaDB::gi()->query("INSERT INTO ".TABLE_MAGNA_CONFIG." (mpID, mkey, value) VALUES (".$aDefaultShippingTime['mpID'].", 'hitmeister.shippingtime.min', ".$aCodeToMinMax[$aDefaultShippingTime['value']]['ShippingTimeMin'].")");
                    MagnaDB::gi()->query("INSERT INTO ".TABLE_MAGNA_CONFIG." (mpID, mkey, value) VALUES (".$aDefaultShippingTime['mpID'].", 'hitmeister.shippingtime.max', ".$aCodeToMinMax[$aDefaultShippingTime['value']]['ShippingTimeMax'].")");
                }
	}
}

$functions[] = 'md_db_update_100';
