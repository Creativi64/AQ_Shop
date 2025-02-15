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

class CdiscountImportOrders extends MagnaCompatibleImportOrders {
	public function __construct($mpID, $marketplace) {
		parent::__construct($mpID, $marketplace);
	}

	protected function getMarketplaceOrderID() {
		return $this->o['orderInfo']['MOrderID'];
	}

	protected function getOrdersStatus() {
		return $this->config['OrderStatusOpen'];
	}

	protected function generateOrderComment($blForce = false) {
		if (!$blForce && !getDBConfigValue(array('general.order.information', 'val'), 0, true)) {
			return ''; 
		}
		return trim(
			sprintf(ML_GENERIC_AUTOMATIC_ORDER_MP_SHORT, $this->marketplaceTitle)."\n".
			ML_LABEL_MARKETPLACE_ORDER_ID.': '.$this->getMarketplaceOrderID()."\n\n".
			$this->comment
		);
	}

	protected function additionalProductsIdentification() {
		$sEAN = $this->p['products_ean'];
		unset($this->p['products_ean']);
		if ($this->p['products_id'] == 0 && !empty($sEAN)) {
			$pim = MagnaDB::gi()->fetchRow('
				SELECT products_id, products_model
				  FROM '.TABLE_PRODUCTS.'
				 WHERE products_ean = "'.$sEAN.'"
			');
			if (false !== $pim) {
				$this->p['products_id'] = $pim['products_id'];
				$this->p['products_model'] = $pim['products_model'];
			}
		}
		if ((!isset($this->p['products_name']) || empty($this->p['products_name'])) && ($this->p['products_id'] != 0)) {
			$this->p['products_name'] = MagnaDB::gi()->fetchOne('
				SELECT pd.products_name
				  FROM '.TABLE_PRODUCTS_DESCRIPTION.'pd, '.TABLE_LANGUAGES.' l
				 WHERE     pd.products_id = "'.$this->p['products_id'].'"
					   AND pd.language_code = l.code
					   AND l.code = "'.strtolower($this->o['orderInfo']['BuyerCountryISO']).'"
			');
			if ($this->p['products_name'] == false) {
				# Fallback for default language
				$sLanguageCode = MagnaDB::gi()->fetchOne('
					SELECT code
					  FROM '.TABLE_LANGUAGES.'
				  ORDER BY sort_order ASC
					 LIMIT 1
				');
				$this->p['products_name'] = MagnaDB::gi()->fetchOne("
					SELECT pd.products_name
					  FROM ".TABLE_PRODUCTS_DESCRIPTION." pd
					 WHERE pd.products_id = '".$this->p['products_id']."'
						   AND pd.language_code = '".$sLanguageCode."'
				");
			}
		}
		if (empty($this->p['products_name'])) {
			$this->p['products_name'] = $sEAN;
		}
	}

	protected function generateOrdersStatusComment() {
		return $this->generateOrderComment(true);
	}
}
