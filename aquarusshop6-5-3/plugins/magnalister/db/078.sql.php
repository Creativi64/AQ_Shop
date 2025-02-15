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
 * (c) 2010 - 2015 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */

$queries = array();
$functions = array();

function updateOldAmazonConfigRemoveTrigger_78() {
    $aAmazonMpIds = MagnaDB::gi()->fetchArray("
        SELECT DISTINCT mpID
          FROM ".TABLE_MAGNA_CONFIG."
         WHERE     mkey = 'amazon.merchantid'
               AND value <> ''
    ", true);

    foreach ($aAmazonMpIds as $sMpId) {
        if (MagnaDB::gi()->recordExists(TABLE_MAGNA_CONFIG, array('mkey' => 'amazon.orderstatus.sync', 'value' => 'trigger'))) {
            MagnaDB::gi()->delete(TABLE_MAGNA_CONFIG, array(
                'mpID' => $sMpId,
                'mkey' => 'amazon.orderstatus.sync',
                'value' => 'trigger'
            ));
        }
    }
    }
$functions[] = 'updateOldAmazonConfigRemoveTrigger_78';