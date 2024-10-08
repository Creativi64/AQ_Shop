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

defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');


require_once(DIR_MAGNALISTER_MODULES.'magnacompatible/crons/MagnaCompatibleSyncOrderStatus.php');

class CdiscountSyncOrderStatus extends MagnaCompatibleSyncOrderStatus {

    /**
     * Size of request batch.
     * @var int
     */
    protected $sizeOfBatch = 10;

    public function __construct($mpID, $marketplace) {
        parent::__construct($mpID, $marketplace);
        $this->confirmationResponseField = 'CONFIRMATIONS';
    }

    protected function prepareSingleOrder($date) {
        parent::prepareSingleOrder($date);

        // this marketplace returns orders if successful
        $this->oOrder['__dirty'] = false;
    }
	
	/**
	 * Adds an error to the cdiscount error log.
	 * 
	 * @param array $error
	 *   The entry for the error log.
	 * @return void
	 */
	protected function addToErrorLog($error) {
		$add = $error['DETAILS'];
		unset($add['ErrorCode']);
		unset($add['ErrorMessage']);
		$add['Action'] = $error['APIACTION'];
		
		MagnaDB::gi()->insert(
			TABLE_MAGNA_COMPAT_ERRORLOG,
			array (
				'mpID' => $this->mpID,
				'errormessage' => $error['ERRORMESSAGE'],
				'dateadded' => date('Y-m-d H:i:s'),
				'additionaldata' => serialize($add)
			)
		);
	}
	
}
