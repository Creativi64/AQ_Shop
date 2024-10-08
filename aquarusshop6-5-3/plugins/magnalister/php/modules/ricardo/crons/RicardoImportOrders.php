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

require_once(DIR_MAGNALISTER_MODULES.'magnacompatible/crons/MagnaCompatibleImportOrders.php');

class RicardoImportOrders extends MagnaCompatibleImportOrders {
	public function __construct($mpID, $marketplace) {
		parent::__construct($mpID, $marketplace);
	}

	protected function getConfigKeys() {
		$aConfigKeys = parent::getConfigKeys();
		$aConfigKeys['OrderStatusOpen'] = array (
			'key' => 'orderstatus.open',
			'default' => '2',
		);
		$aConfigKeys['PaymentMethod']['default'] = 'marketplace';
		return $aConfigKeys;
	}

	protected function getOrdersStatus() {
		return $this->config['OrderStatusOpen'];
	}

	protected function getPaymentMethod() {
		if ($this->config['PaymentMethod'] == 'matching') {
			return $this->o['order']['payment_code'];
		}

		return $this->config['PaymentMethod'];
	}

	/**
	 * Converts the tax value to an ID
	 *
	 * @parameter mixed $tax	Something that represents a tax value
	 * @return float			The actual tax value
	 * @TODO: Save the ID2Tax Array somewhere more globally or ask the allmigty API for it.
	 */
	protected function getTaxValue($tax) {
		if ($tax < 0) {
			return (float)$this->config['MwStFallback'];
		}

		return $tax;
	}
}
