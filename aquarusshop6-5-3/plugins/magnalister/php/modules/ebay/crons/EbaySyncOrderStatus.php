<?php
/*
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
 * (c) 2010 - 2022 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */

defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');

require_once(DIR_MAGNALISTER_MODULES.'magnacompatible/crons/MagnaCompatibleSyncOrderStatus.php');
#require_once(DIR_MAGNALISTER_MODULES.'ebay/ebayFunctions.php');


class EbaySyncOrderStatus extends MagnaCompatibleSyncOrderStatus {
    protected $blIsPaymentProgramAvailable;

    protected $sizeOfBatch = 100;
    protected $confirmationResponseField = 'CONFIRMATIONS';

	public function __construct($mpID, $marketplace) {
		parent::__construct($mpID, $marketplace);
        $this->blIsPaymentProgramAvailable = false;
        try {
            $aResponse = MagnaConnector::gi()->submitRequest(array(
                'SUBSYSTEM' => $marketplace,
                'MARKETPLACEID' => $mpID,
                'ACTION' => 'CheckPaymentProgramAvailability',
            ));
            $this->blIsPaymentProgramAvailable = isset($aResponse['IsAvailable']) ? $aResponse['IsAvailable'] : false;
        } catch (MagnaException $oEx) {
        }

    }

	protected function prepareSingleOrder($date) {
		parent::prepareSingleOrder($date);

		$this->oOrder['__dirty'] = false;
	}
	
	/**
	 * Adds the current order's index to a lookup table where the key is
	 * the MOrderID. Joined orders are split.
	 * @return void
	 */
	protected function addToLookupTable() {
		$mOrderIds = explode("\n", $this->oOrder['special']);
		foreach ($mOrderIds as $mOrderId) {
			$this->aMOrderID2Order[$mOrderId] = $this->iOrderIndex;
		}
	}

    protected function getConfigKeys() {
        $aReturn = parent::getConfigKeys();

        $aReturn['OrderRefundStatus'] = array (
            'key' => 'refundstatus',
            'default' => '--',
        );
        $aReturn['OrderRefundReason'] = array (
            'key' => 'refundreason',
            'default' => false,
        );
        $aReturn['OrderRefundComment'] = array (
            'key' => 'refundcomment',
            'default' => false,
        );
        return $aReturn;
    }


    /**
     * @return bool
     */
    protected function isProcessable() {
        if ($this->blIsPaymentProgramAvailable && $this->config['OrderRefundStatus'] !== '--' && $this->oOrder['orders_status_shop'] === $this->config['OrderRefundStatus']) {
            $request = $this->getBaseRequest();
            $request = array_merge($request, array(
                'ACTION' => 'DoRefund',
                'MagnalisterOrderId' => $this->oOrder['special'],
                'ReasonOfRefund' => $this->config['OrderRefundReason'],
                'Comment' => $this->config['OrderRefundComment'],
            ));

            try {
                // in osc this $this->oOrder['data'] is at this point serialized but not in veyton
                if (!isset($this->oOrder['data']['refund'])) {
                    MagnaConnector::gi()->submitRequest($request);
                    $this->oOrder['data']['refund'] = 'requested';
                    $data = serialize($this->oOrder['data']);
                    MagnaDB::gi()->update(TABLE_MAGNA_ORDERS,
                        array(
                            'data' => $data
                        )
                        , array(
                            'orders_id' => $this->oOrder['orders_id']
                        )
                    );
                }
            } catch (MagnaException $oEx) {
                echo print_m($oEx->getMessage(), 'DoRefund ('.$this->oOrder['special'].')');
                $aErrorData = array(
                    'MOrderID' => $this->oOrder['special'],
                );

                if (is_numeric(substr($oEx->getMessage(), 0, 5))) {
                    $sOrigin = 'eBay';
                } else {
                    $sOrigin = 'magnalister';
                }

                MagnaDB::gi()->insert(
                    TABLE_MAGNA_COMPAT_ERRORLOG,
                    array(
                        'mpID' => $this->mpID,
                        'errormessage' => $oEx->getMessage(),
                        'dateadded' => date('Y-m-d H:i:s'),
                        'additionaldata' => serialize($aErrorData),
                        'origin' => $sOrigin
                    )
                );
            }
        }
        return parent::isProcessable();
    }

}

