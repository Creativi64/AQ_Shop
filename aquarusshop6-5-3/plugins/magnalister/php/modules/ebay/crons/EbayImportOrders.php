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
 * (c) 2010 - 2023 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */

defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');

require_once(DIR_MAGNALISTER_MODULES.'magnacompatible/crons/MagnaCompatibleImportOrders.php');
require_once(DIR_MAGNALISTER_MODULES.'ebay/ebayFunctions.php');

class EbayImportOrders extends MagnaCompatibleImportOrders {

	public function __construct($mpID, $marketplace) {
		parent::__construct($mpID, $marketplace);
	}
	
	protected function initImport() {
		parent::initImport();
		MagnaConnector::gi()->setTimeOutInSeconds(10);
	}

	protected function completeImport() {
		MagnaConnector::gi()->resetTimeOut();
	}
	
	protected function getConfigKeys() {
		$keys = parent::getConfigKeys();
		
		$keys['OrderStatusClosed'] = array (
			'key' => 'orderstatus.closed',
			'default' => array(),
		);
		
		$keys['ImportOnlyPaid'] = array (
			'key' => 'order.importonlypaid',
			'default' => false,
		);
		
		$keys['ShippingProfiles'] = array (
			'key' => 'shippingprofiles',
			'default' => null,
		);
		$keys['ShippingProfileIdLocal'] = array (
			'key' => 'default.shippingprofile.local',
			'default' => 0,
		);
		$keys['ShippingProfileIdIternational'] = array (
			'key' => 'default.shippingprofile.international',
			'default' => 0,
		);
		$keys['ShippingProfileDiscountUseLocal'] = array (
			'key' => array('shippingdiscount.local', 'val'),
			'default' => true,
		);
		$keys['ShippingProfileDiscountUseIternational'] = array (
			'key' => array('shippingdiscount.international', 'val'),
			'default' => true,
		);
		return $keys;
	}
	
	protected function initConfig() {
		parent::initConfig();
		
		if (!is_array($this->config['OrderStatusClosed'])) {
			$this->config['OrderStatusClosed'] = array();
		}
	}
	
	protected function doBeforeInsertProduct() {
		if (isset($this->p['products_shipping_time'])
			|| ('0' == $this->p['products_shipping_time'])
		) {
			if ('de' == strtolower($this->config['StoreLanguage'])) {
				$this->p['products_shipping_time'] = getDBConfigValue('ebay.DispatchTimeMax', $this->mpID, 0).' Werktage';
			} else {
				$this->p['products_shipping_time'] = getDBConfigValue('ebay.DispatchTimeMax', $this->mpID, 0).' days';
			}
		}
	}
	
	/**
	 * How many hours, days, weeks or whatever we go back in time to request older orders?
	 * @return time in seconds
	 */ 
	protected function getPastTimeOffset() {
		return 60 * 60 * 24 * 14;
	}
	
	protected function getMarketplaceOrderID() {
		return $this->o['orderInfo']['eBayOrderID'];
	}
	
	protected function orderExists() {
		$mOID = $this->getMarketplaceOrderID();
		$aRow = MagnaDB::gi()->fetchRow('
		    SELECT orders_id, processed
		      FROM '.TABLE_MAGNA_ORDERS.'
		     WHERE platform = "'.MagnaDB::gi()->escape($this->marketplace).'"
		           AND special like "%'.MagnaDB::gi()->escape($mOID).'%"
		  ORDER BY orders_id DESC
		     LIMIT 1
		');
		if (empty($aRow)) {
			return false;
		} elseif ($aRow['processed'] == 0) {
			// order can be delted but perhaps shop-owner have already edited order
			return false;
		} else if (    $this->isExtendedOrderToAdd()
			    && (MagnaDB::gi()->fetchOne('SELECT COUNT(*)
				FROM '.TABLE_ORDERS_PRODUCTS.'
				WHERE orders_id = '.$aRow['orders_id']) < count($this->o['products']))) {
			if ($this->verbose) echo 'orderExists('.$mOID.') but has not all positions. Processing'."\n";
			return false;
		} else {
			if ($this->verbose) echo 'orderExists('.$mOID.')'."\n";
			/* Ack again */
			$this->processedOrders[] = array (
				'MOrderID' => $mOID,
				'ShopOrderID' => $aRow['orders_id'],
			);
			return true;
		}
	}

	protected function getOrdersStatus() {
		return $this->config['OrderStatusOpen'];
	}

    private function getEbayRefundUrl () {
        return (empty($this->o['orderInfo']['EbayRefundUrl']))
            ? ''
            : sprintf(ML_EBAY_ORDER_DETAIL_INFORMATION_TO_EBAY_SELLER_HUB, $this->o['orderInfo']['EbayRefundUrl'])
            ;
    }

    private function getEbayPlus () {
		if (empty($this->o['orderInfo']['eBayPlus']))
			return '';
		$sRet = "\neBayPlus";
echo print_m($this->o['magnaOrders'], '$this->o[magnaOrders]');
		if (   array_key_exists('ML_TEXT_WARNING',$this->o['magnaOrders'])
		    && defined($this->o['magnaOrders']['ML_TEXT_WARNING'])) {
			$sRet .= "\n".constant($this->o['magnaOrders']['ML_TEXT_WARNING']);
		}
		return $sRet;
	}

	protected function generateOrderComment($blForce = false) {
		if (!$blForce && !getDBConfigValue(array('general.order.information', 'val'), 0, true)) {
			return ''; 
		}
		if (isset($this->o['orderInfo']['ExtendedOrderID'])) {
			$ExtendedOrderID = "\n".'ExtendedOrderID:   '.$this->o['orderInfo']['ExtendedOrderID'];
		} else {
			$ExtendedOrderID = '';
		}

		if (!empty($this->o['orderInfo']['eBayBuyerUsername'])) {
			$buyer = "\n".'eBay User:   '.$this->o['orderInfo']['eBayBuyerUsername'];
		} else {
			$buyer = '';
		}
		
		return trim(
			sprintf(ML_GENERIC_AUTOMATIC_ORDER_MP_SHORT, $this->marketplaceTitle)."\n".
			'eBayOrderID: '.$this->getMarketplaceOrderID().$ExtendedOrderID.$buyer."\n".$this->getEbayPlus()."\n\n".
			$this->comment
		);
	}
	
	protected function generateOrdersStatusComment() {
		if (isset($this->o['orderInfo']['ExtendedOrderID'])) {
			$ExtendedOrderID = "\n".'ExtendedOrderID:   '.$this->o['orderInfo']['ExtendedOrderID'];
		} else {
			$ExtendedOrderID = '';
		}

		if (!empty($this->o['orderInfo']['eBayBuyerUsername'])) {
			$buyer = "\n".'eBay User:   '.$this->o['orderInfo']['eBayBuyerUsername'];
		} else {
			$buyer = '';
		}

		if ('true' === $this->config['ImportOnlyPaid']) {
			if (strpos($this->o['orderComment'], 'PayPal')) {
				$PUIcomment = ML_EBAY_PUI_MSG_TO_BUYER.
				$this->o['orderComment'];
			}
		}

        return trim(
            sprintf(ML_GENERIC_AUTOMATIC_ORDER_MP, $this->marketplaceTitle)."\n".
            'eBayOrderID: '.$this->getMarketplaceOrderID().$ExtendedOrderID.$buyer.$this->getEbayPlus()."\n\n".
            $this->comment.(isset($PUIcomment) ? $PUIcomment : '')."\n\n".
            $this->getEbayRefundUrl()
        );
	}
	
	/**
	 * Returs the shipping method for the current order.
	 * @return string
	 */
	protected function getShippingMethod() {
		if ($this->config['ShippingMethod'] == 'standart') {
			return $this->o['order']['shipping_code'];
		}
		return $this->config['ShippingMethod'];
	}

	protected function getPaymentMethod() {
		// The function returns a human-readable payment method name
		if ($this->config['PaymentMethod'] == 'matching') {
			return getPaymentClassForEbayPaymentMethod($this->o['order']['payment_code']);
		}
        if ($this->config['PaymentMethod'] == 'standart') {
            return $this->o['order']['payment_code'];
        }
		if ($this->config['PaymentMethod'] == '__ml_lump') {
			return trim($this->config['PaymentMethodName']);
		}
		return $this->config['PaymentMethod'];
	}
	
	// special case (bug):
	// ImportOnlyPaid = true, but an order with the same ExtendedOrderID has not been considered
	// so we should add it (if the already imported order has still the start status)
	private function isExtendedOrderToAdd() {
		if (!isset($this->o['orderInfo']['ExtendedOrderID'])) return false;
		if (!$this->config['ImportOnlyPaid']) return false;
		return (boolean)MagnaDB::gi()->fetchOne(eecho('SELECT COUNT(*)
			 FROM '.TABLE_ORDERS.'
			WHERE comments LIKE \'%ExtendedOrderID:   '.$this->o['orderInfo']['ExtendedOrderID'].'%\'
			  AND orders_status = \''.$this->config['OrderStatusOpen'].'\'', $this->verbose));
	}

	protected function doInsertOrder() {
		$this->doBeforeInsertOrder();
		
		$blIsExtendedOrderToAdd = $this->isExtendedOrderToAdd();
		if (   (   ( empty($this->config['OrderStatusClosed'])) 
		        || ('true' === $this->config['ImportOnlyPaid']))
		    && (!$blIsExtendedOrderToAdd)                       ) {
			# don't merge if "don't megre" array empty, or if we import only complete orders
			$existingOpenOrder = false;
		} else {
			if (!$blIsExtendedOrderToAdd) {
				$sAndOrdersStatus = 'AND o.orders_status NOT IN ("'.implode('", "', $this->config['OrderStatusClosed']).'")';
			} else {
				$sAndOrdersStatus = 'AND o.orders_status = \''.$this->config['OrderStatusOpen'].'\'
			           AND o.comments LIKE \'%ExtendedOrderID:   '.$this->o['orderInfo']['ExtendedOrderID'].'%\'';
			}
			$existingOpenOrder = MagnaDB::gi()->fetchRow(eecho('
			    SELECT o.orders_id, mo.special, mo.data
			      FROM '.TABLE_ORDERS.' o, '.TABLE_MAGNA_ORDERS.' mo
			     WHERE o.customers_id = '.$this->o['order']['customers_id'].'
			           AND o.customers_email_address = \''.$this->o['order']['customers_email_address'].'\' 
			           '.$sAndOrdersStatus.'
			           AND o.currency_code = \''.$this->o['order']['currency_code'].'\'
			           AND mo.mpID = '.$this->mpID.'
			           AND o.orders_id = mo.orders_id 
			           AND mo.processed = 1
			  ORDER BY o.orders_id DESC LIMIT 1
			', $this->verbose));
		}
		
		if ($this->verbose) echo var_dump_pre($existingOpenOrder, '$existingOpenOrder');

		# If magna order is found we add this order to it.
		if (false != $existingOpenOrder) {
			# We found the order to which we can add this order and make it merged.
			$this->cur['OrderID'] = (int)$existingOpenOrder['orders_id'];
			$magnaOrdersDataArr = unserialize($existingOpenOrder['data']);

			# Merge order to merged or single order.
			foreach (array('eBayOrderID', 'ExtendedOrderID', 'eBaySalesRecordNumber') as $sOrderKey) {
				if (!array_key_exists($sOrderKey, $this->o['magnaOrders'])) {
					continue;
				}
				if (!is_array($magnaOrdersDataArr[$sOrderKey])) {
					$magnaOrdersDataArr[$sOrderKey] = array(
						$magnaOrdersDataArr[$sOrderKey],
						$this->o['magnaOrders'][$sOrderKey]
					);
				} else {
					$magnaOrdersDataArr[$sOrderKey][] = $this->o['magnaOrders'][$sOrderKey];
				}
			}
			# eBayPlus or other additional info
			$aNewMagnaOrdersDataKeys = array_keys($this->o['magnaOrders']);
			foreach ($aNewMagnaOrdersDataKeys as $newKey) {
				if (!array_key_exists($newKey, $magnaOrdersDataArr)) {
					$magnaOrdersDataArr[$newKey] = $this->o['magnaOrders'][$newKey];
				}
			}
			$magnaOrdersData = serialize($magnaOrdersDataArr);
			$magnaOrdersSpecial = $existingOpenOrder['special']."\n".$this->getMarketplaceOrderID();
			
			# Update the shipping code
			MagnaDB::gi()->update(TABLE_ORDERS, array (
				'shipping_code' => $this->getShippingMethod(),
			), array (
				'orders_id' => $this->cur['OrderID'],
			));
			$this->o['internaldata'] = $this->calculateInternalData($this->cur['OrderID'], $this->o['orderTotal']['Shipping']['orders_total_price'], $existingOpenOrder['internaldata']);
			$iProcessed = 1;
		} else {
			# We didn't find an order to which we can add this order.
			$sOrderCountSql = "SELECT * FROM ".TABLE_ORDERS." WHERE customers_email_address = '".MagnaDB::gi()->escape($this->o['order']['customers_email_address'])."'";
			$iPrevOrdersCount = (int) $this->db->fetchRow($sOrderCountSql);
			// newer xt:Commerce Versions have a address_addition fields
			if ( /*  (stripos($this->o['order']['delivery_street_address'], 'Packstation') !== false)
			     &&  ctype_digit($this->o['order']['delivery_suburb'])
			     &&*/MagnaDB::gi()->columnExistsInTable('delivery_address_addition', TABLE_ORDERS)) {
			    $this->o['order']['delivery_address_addition'] = $this->o['order']['delivery_suburb'];
			    $this->o['order']['delivery_suburb'] = '';
			}
			if ( /*  (stripos($this->o['order']['billing_street_address'], 'Packstation') !== false)
			     &&  ctype_digit($this->o['order']['billing_suburb'])
			     &&*/MagnaDB::gi()->columnExistsInTable('billing_address_addition', TABLE_ORDERS)) {
			    $this->o['order']['billing_address_addition'] = $this->o['order']['billing_suburb'];
			    $this->o['order']['billing_suburb'] = '';
			}
            MagnaDB::gi()->validateDataLength($this->o['order'], TABLE_ORDERS);
            MagnaDB::gi()->addNonNullableEntries($this->o['order'], TABLE_ORDERS);
            $this->db->insert(TABLE_ORDERS, $this->o['order']);
			$this->cur['OrderID'] = $this->db->getLastInsertID();
			# for the case it gets lost (happened)
			if (empty($this->cur['OrderID'])) {
				$iInsertId = (int)$this->db->fetchOne("SELECT LAST_INSERT_ID()");
				$sOrderId = (int)$this->db->fetchOne("
					SELECT orders_id 
					FROM ".TABLE_ORDERS."
					WHERE customers_email_address = '".MagnaDB::gi()->escape($this->o['order']['customers_email_address'])."'
					ORDER BY orders_id DESC
					LIMIT 1
				");
				if ($iInsertId == $sOrderId) {
					$this->cur['OrderID'] = $iInsertId;
				} elseif((int) $this->db->fetchRow($sOrderCountSql) > $iPrevOrdersCount) {// count is now higher
					$this->cur['OrderID'] = $sOrderId;
				} else {// there is no order-id
					return;
				}
			}
			$magnaOrdersData = serialize($this->o['magnaOrders']);
			$magnaOrdersSpecial = $this->getMarketplaceOrderID();
			$this->o['internaldata'] = $this->calculateInternalData($this->cur['OrderID'], $this->o['orderTotal']['Shipping']['orders_total_price']);
			$iProcessed = 0;
		}
		
		$this->db->insert(TABLE_MAGNA_ORDERS, array(
			'mpID' => $this->mpID,
			'orders_id' => $this->cur['OrderID'],
			'orders_status' => $this->o['order']['orders_status'],
			'data' => $magnaOrdersData,
			'internaldata' => $this->o['internaldata'],
			'special' => $magnaOrdersSpecial,
			'platform' => $this->marketplace,
			'processed' => $iProcessed,
		), true);
	}

	protected function insertProduct() {
		parent::insertProduct();
		if(!$this->isExtendedOrderToAdd()) {
			return;
		}
		$aRepeatedProducts = MagnaDB::gi()->fetchArray(eecho("SELECT products_id, products_model, products_price, products_quantity, COUNT(*) cnt
			 FROM ".TABLE_ORDERS_PRODUCTS."
			WHERE orders_id = ".$this->cur['OrderID']."
			  AND (products_id <> 0 OR LENGTH(products_model) > 0)
			GROUP BY products_id, products_model, products_quantity, products_price
			HAVING cnt>1", $this->verbose));
		if (empty($aRepeatedProducts)) {
			// no repeated products, return
			return;
		}
		foreach($aRepeatedProducts as $row) {
			$this->db->query(eecho("DELETE FROM ".TABLE_ORDERS_PRODUCTS."
				WHERE orders_id = ".$this->cur['OrderID']."
				  AND products_id = ".$row['products_id']."
				  AND products_model = '".$row['products_model']."'
				  AND products_price = ".$row['products_price']."
				  AND products_quantity = ".$row['products_quantity']."
				  ORDER BY orders_products_id DESC LIMIT ".(int)($row['cnt'] - 1)
			, $this->verbose));
		}
			
	}
	
	protected function isDomestic($countryISO) {
		if (strtolower($countryISO) == $this->config['StoreCountry']) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Recalculates the shipping cost for orders that are going to be merged.
	 */
	protected function calculateShippingCost($existingShippingCost, $currItemShippingCost, $totalNumberOfItems, $totalPriceWOShipping, $currProductsCount) {
		
		if ((0 == $existingShippingCost) && (0 == $currItemShippingCost)) {
			return 0.0;
		}
		if ('true' === $this->config['ImportOnlyPaid']) {
		// special case (bug):
		// ImportOnlyPaid = true, but an order with the same ExtendedOrderID has not been considered:
		// in magnalister DB, one of the items has the shipping costs for all, the others 0
			return max($existingShippingCost, $currItemShippingCost);
		}
		$internaldataArray = unserialize($this->o['internaldata']);

		if (array_key_exists('addCost', $internaldataArray)) {# $addCost gesetzt
			$addCost = $internaldataArray['addCost'];
			# existingAddCost: ausser dem ersten Item und aktueller Bestellung
			$existingAddCost = ($totalNumberOfItems - 1 - $currProductsCount) * $addCost;
			$firstItemShippingCost = $existingShippingCost - $existingAddCost;
			# currSingleItemShippingCost: erstes Stueck der aktuellen Bestellung
			$currSingleItemShippingCost = $currItemShippingCost - (($currProductsCount - 1) * $addCost);
			$totalAddCost = $existingAddCost + ($currProductsCount * $addCost);
			if ($firstItemShippingCost > $currSingleItemShippingCost) {
				$totalShippingCost = $firstItemShippingCost + $totalAddCost;
			} else {
				$totalShippingCost = $currSingleItemShippingCost + $totalAddCost;
			}
		} else {# kein $addCost, alles voll berechnen
			$totalShippingCost = $existingShippingCost + $currItemShippingCost;
		}
		
		$minAmountForDiscount = (array_key_exists('minAmountForDiscount', $internaldataArray))
			? $internaldataArray['minAmountForDiscount']
			: 0
		;
		if (
			array_key_exists('maxCostPerOrder', $internaldataArray)
		    && ($totalPriceWOShipping >= $minAmountForDiscount)
		) {
			$totalShippingCost = min($totalShippingCost, $internaldataArray['maxCostPerOrder']);
		}
		return $totalShippingCost;
	}
	
	/**
	 * Calculates the shipping costs if an existing order will be merged
	 * before calculating the shipping tax.
	 */
	protected function proccessShippingTax() {
		if (!array_key_exists('Shipping', $this->o['orderTotal'])) {
			return;
		}
		
		$existingShippingCost = (float)MagnaDB::gi()->fetchOne(eecho('
		    SELECT ((orders_total_price * (100.0 + orders_total_tax)) / 100.0)
		      FROM '.TABLE_ORDERS_TOTAL.'
		     WHERE orders_id = '.$this->cur['OrderID'].'
		           AND orders_total_key = "shipping"
		  ORDER BY orders_total_price DESC 
		     LIMIT 1
		', $this->verbose));
		$productsCount = (int)MagnaDB::gi()->fetchOne(eecho('
			SELECT SUM(products_quantity)
			  FROM '.TABLE_ORDERS_PRODUCTS.'
			 WHERE orders_id = '.$this->cur['OrderID'].'
		', $this->verbose));
		$totalPriceWOShipping = (float)MagnaDB::gi()->fetchOne(eecho('
		    SELECT SUM(((products_price * (100 + products_tax))/100))
		      FROM '.TABLE_ORDERS_PRODUCTS.'
		     WHERE orders_id = '.$this->cur['OrderID'].'
		', $this->verbose));
		
		
		if (($existingShippingCost > 0) || ($productsCount > $this->o['_processingData']['ProductsCount'])) {
			/* Merged order */
			$this->o['orderTotal']['Shipping']['orders_total_price'] = $this->calculateShippingCost(
				$existingShippingCost,
				$this->o['orderTotal']['Shipping']['orders_total_price'],
				$productsCount,
				$totalPriceWOShipping,
				$this->o['_processingData']['ProductsCount']
				//$this->o['order']['billing_country_iso_code_2']
			);
			if ($this->verbose) {
				echo "\n".'Merged ShippingCost: '.$this->o['orderTotal']['Shipping']['orders_total_price']."\n";
			}
		}
		
		parent::proccessShippingTax();
	}

	protected function insertOrdersStats() {
		$existingOrdersStats = MagnaDB::gi()->fetchRow('
			SELECT *
			  FROM '.TABLE_ORDERS_STATS.'
			 WHERE orders_id = '.$this->cur['OrderID']
		);
		if (false == $existingOrdersStats) {
			parent::insertOrdersStats();
		} else {
		// when combining orders, add existing price and count
			if (!$this->config['AllowTax']) {
				$sum = 0;
				foreach ($this->taxValues as $tax => $value) {
					$sum += $value;
				}
				$this->o['ordersStats']['orders_stats_price'] = $sum;
			}
			// add existing price and count
			$this->o['ordersStats']['orders_stats_price'] += $existingOrdersStats['orders_stats_price'];
			$this->o['ordersStats']['products_count']     += $existingOrdersStats['products_count'];
			$this->o['ordersStats']['orders_id'] = $this->cur['OrderID'];
			$this->db->insert(TABLE_ORDERS_STATS, $this->o['ordersStats'], true);
		}
	}

	private function calculateInternalData($ordersId, $shippingCost, $existingInternalData = '') {
		$products_id    = magnaSKU2pID($this->o['products'][0]['products_id'], true);
		if ('artNr' == getDBConfigValue('general.keytype', '0')) {
			// API gives the main SKU in products_id field, and variation SKU in products_model (same if least not given)
			$products_model = $this->o['products'][0]['products_id'];
		} else {
			$products_model = trim(MagnaDB::gi()->fetchOne('SELECT products_model FROM '.TABLE_PRODUCTS.'
				WHERE products_id=\''.$products_id.'\' LIMIT 1'
			));
		}
		$domestic = $this->isDomestic($this->o['order']['delivery_country_code']);
		$shippingProfiles = getDBConfigValue('ebay.shippingprofiles', $this->mpID, null);
		if ($this->verbose) {
			echo print_m($shippingProfiles, '$shippingProfiles');
			echo print_m($this->mpID, '$this->mpID');
		}
		if (0 != $products_id) {
			$currProductsShippingDetails = MagnaDB::gi()->fetchOne(eecho('SELECT ShippingDetails
				FROM '.TABLE_MAGNA_EBAY_PROPERTIES .'
				WHERE '.('artNr' != getDBConfigValue('general.keytype', '0')
						?'products_id = '.$products_id
						:'products_model =\''.MagnaDB::gi()->escape($products_model)).'\''.'
				AND mpID = '.$this->mpID, 
				false
			));
			if (false != $currProductsShippingDetails) {
				$currProductsShippingDetailsArr = json_decode($currProductsShippingDetails, true);
				if ($domestic) {
					if (array_key_exists('LocalProfile', $currProductsShippingDetailsArr)) {
						$profileID = $currProductsShippingDetailsArr['LocalProfile'];
						$useDiscount = $currProductsShippingDetailsArr['LocalPromotionalDiscount'];
					} else {
						$profileID   = getDBConfigValue('ebay.default.shippingprofile.local',$this->mpID, 0);
						$useDiscount = getDBConfigValue(array('ebay.shippingdiscount.local', 'val'), $this->mpID, true);
					}
				} else {
					if (array_key_exists('InternationalProfile', $currProductsShippingDetailsArr)) {
						$profileID = $currProductsShippingDetailsArr['InternationalProfile'];
						$useDiscount = $currProductsShippingDetailsArr['InternationalPromotionalDiscount'];
					} else {
						$profileID   = getDBConfigValue('ebay.default.shippingprofile.international',$this->mpID, 0);
						$useDiscount = getDBConfigValue(array('ebay.shippingdiscount.international', 'val'), $this->mpID, true);
					}
				}
			}
		} 
		if ((0 == $products_id) || (false == $currProductsShippingDetails)) {
			if ($domestic) {
				$profileID = getDBConfigValue('ebay.default.shippingprofile.local',$this->mpID, 0);
				$useDiscount = getDBConfigValue(array('ebay.shippingdiscount.local', 'val'), $this->mpID, true);
			} else {
				$profileID = getDBConfigValue('ebay.default.shippingprofile.international',$this->mpID, 0);
				$useDiscount = getDBConfigValue(array('ebay.shippingdiscount.international', 'val'), $this->mpID, true);
			}
		}
		$newInternaldataArray = array (
			'singleShippingCost' => $shippingCost,
		);

		// add the additional costs to internal array
		if (
			isset($shippingProfiles) 
			&& (!empty($profileID))
            && (!empty($shippingProfiles['Profiles']))
			&& array_key_exists($profileID, $shippingProfiles['Profiles'])
			&& array_key_exists('EachAdditionalAmount', $shippingProfiles['Profiles']["$profileID"])
		) {
			if ($shippingProfiles['Profiles']["$profileID"]['EachAdditionalAmount'] >= 0) {
				$newInternaldataArray['addCost'] = trim($shippingProfiles['Profiles']["$profileID"]['EachAdditionalAmount']);
			} else {
				# negative EachAdditionalAmount (i.e. EachAdditionalAmountOff
				# => take my ShippingCost minus (plus the negative) EachAdditionalAmount
				$newInternaldataArray['addCost'] = max(0, ($shippingCost + trim($shippingProfiles['Profiles']["$profileID"]['EachAdditionalAmount'])));
			}
		}
		# singleShippingCost anpassen falls Quantity > 1
		if ($this->o['products'][0]['products_quantity'] > 1) {
			if (array_key_exists('addCost', $newInternaldataArray)) {
				$newInternaldataArray['singleShippingCost'] -=
					round((($this->o['products'][0]['products_quantity'] -1) * $newInternaldataArray['addCost']), 4);
			} else {
				$newInternaldataArray['singleShippingCost'] =
					round(($newInternaldataArray['singleShippingCost'] / $this->o['products'][0]['products_quantity']), 8);
			}
		}

		// useDiscount can be string, make it proper boolean if needed
		if ('false' === $useDiscount) {
			$useDiscount = false;
		}

		// if shippingProfiles are available, useDiscount is set, and a Discount is defined in eBay account
		if (    isset($shippingProfiles)
		     && $useDiscount
		     && array_key_exists('PromotionalShippingDiscount', $shippingProfiles)
		) {
			# hier switch zu newInternalData
			# addCost oder maxShippingCostAbSoUndSoViel
			switch ($shippingProfiles['PromotionalShippingDiscount']['DiscountName']) {
				case ('MaximumShippingCostPerOrder'): {
					$newInternaldataArray['maxCostPerOrder'] = trim($shippingProfiles['PromotionalShippingDiscount']['ShippingCost']);
					break;
				}
				case ('ShippingCostXForAmountY'): {
					$newInternaldataArray['minAmountForDiscount'] = trim($shippingProfiles['PromotionalShippingDiscount']['OrderAmount']);
					$newInternaldataArray['maxCostPerOrder'] = trim($shippingProfiles['PromotionalShippingDiscount']['ShippingCost']);
					break;
				}
				default: break;
			}
		}
		if (!empty($existingInternalData)) {
			// always use the rules of the Item with the biggest ShippingCost
			// (so, if the current one is smaller, use the old one)
			// if ShippingCosts are equal, use the old one
			$existingInternalDataArray = unserialize($existingInternalData);
			if (    
				is_array($existingInternalDataArray) 
			    && array_key_exists('singleShippingCost', $existingInternalDataArray)
			    && $existingInternalDataArray['singleShippingCost'] >= $newInternaldataArray['singleShippingCost']
			) {
				return $existingInternalData;
			}
		}
		// no existingInternalData or existing singleShippingCost < current singleShippingCost
		$newInternaldata = serialize($newInternaldataArray);
		return $newInternaldata;
	}
	protected function sendPromoMail() {
		if ($this->config['MailSend'] != 'true') return;
		sendSaleConfirmationMail(
			$this->mpID,
            str_replace('blacklisted-', '', $this->o['customer']['customers_email_address']),
			array (
				'#FIRSTNAME#' => $this->o['order']['billing_firstname'],
				'#LASTNAME#' => $this->o['order']['billing_lastname'],
				'#EMAIL#' => $this->o['customer']['customers_email_address'],
				'#PASSWORD#'  => $this->cur['customer']['Password'],
				'#MORDERID#' => $this->o['orderInfo']['eBayOrderID'],
				'#EORDERID#' => (isset($this->o['orderInfo']['ExtendedOrderID'])
					? $this->o['orderInfo']['ExtendedOrderID']
					: ''
				), 
				'#ORDERSUMMARY#' => $this->mailOrderSummary,
				'#MARKETPLACE#' => $this->marketplaceTitle,
				'#SHOPURL#' => '', // eBay doesn't allow it
			)
		);
	}

    /**
     * add 'blacklisted-' from customer's e-mail address
     *  if configured so (not recommended)
     *
     * @return array
     */
    protected function insertCustomer() {
        if (getDBConfigValue(array($this->marketplace . '.mailaddress.blacklist', 'val'), $this->mpID, false)) {
            if ($this->verbose) echo __FUNCTION__.": ebay.mailaddress.blacklist == true\n";
            $this->o['customer']['customers_email_address'] = 'blacklisted-'.$this->o['customer']['customers_email_address'];
        }

        return parent::insertCustomer();
    }


    /**
     * add 'blacklisted-' from customer's e-mail address
     *  if configured so (not recommended)
     */
    protected function doBeforeInsertOrder() {
        if (getDBConfigValue(array($this->marketplace . '.mailaddress.blacklist', 'val'), $this->mpID, false)) {
            if ($this->verbose) echo __FUNCTION__.": ebay.mailaddress.blacklist == true\n";
            $this->o['order']['customers_email_address'] = 'blacklisted-'.$this->o['order']['customers_email_address'];
        }
    }
}
