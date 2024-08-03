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
 * (c) 2010 - 2013 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */

defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');
require_once(DIR_MAGNALISTER_MODULES.'magnacompatible/checkin/MagnaCompatibleCheckinSubmit.php');
require_once(DIR_MAGNALISTER_MODULES.'check24/Check24Helper.php');
require_once(DIR_MAGNALISTER_MODULES.'check24/classes/Check24ProductSaver.php');

// @ToDo: Why use MagnaCompat instead of ComparisonShopping?
class Check24CheckinSubmit extends MagnaCompatibleCheckinSubmit {
	private $oLastException = null;

	public function __construct($settings = array()) {
		global $_MagnaSession;

		$settings = array_merge(array(
			'language' => getDBConfigValue($settings['marketplace'] . '.lang', $_MagnaSession['mpID'], ''),
			'currency' => getCurrencyFromMarketplace($_MagnaSession['mpID']),
			'keytype' => getDBConfigValue('general.keytype', '0'),
			'itemsPerBatch' => 100,
			'mlProductsUseLegacy' => false,
		), $settings);

		$this->summaryAddText = ML_CHECK24_TEXT_AFTER_UPLOAD;
		
		parent::__construct($settings);
		
		$this->settings['SyncInventory'] = array (
			'Price' => getDBConfigValue($settings['marketplace'].'.inventorysync.price', $this->mpID, '') == 'auto',
			'Quantity' => getDBConfigValue($settings['marketplace'].'.stocksync.tomarketplace', $this->mpID, '') == 'auto',
		);
	}

	protected function processException($e) {
		$this->oLastException = $e;
	}

	public function getLastException() {
		return $this->oLastException;
	}
	
	protected function setUpMLProduct() {
		parent::setUpMLProduct();
		
		// Set Price and Quantity settings
		MLProduct::gi()->setPriceConfig(Check24Helper::loadPriceSettings($this->mpID));
		MLProduct::gi()->setQuantityConfig(Check24Helper::loadQuantitySettings($this->mpID));
	}

    protected function getItemUrl($product) {
        $shopUrl = MagnaDB::gi()->fetchOne('
            SELECT shop_http
              FROM '.TABLE_MANDANT_CONFIG.'
             WHERE shop_id = '.getDBConfigValue($this->settings['marketplace'].'.shopmandant', $this->_magnasession['mpID'], '1').'
        ');

        /** xt:Commerce 6 patch code */
        if (!$shopUrl) {
            $shopUrl = MagnaDB::gi()->fetchArray('
                SELECT shop_ssl_domain, shop_ssl
                  FROM '.TABLE_MANDANT_CONFIG.'
                 WHERE shop_id = '.getDBConfigValue($this->settings['marketplace'].'.shopmandant', $this->_magnasession['mpID'], '1').'
            ');
            if (isset($shopUrl[0]) && !empty($shopUrl[0])) {
                $shopUrl = $shopUrl[0];
                $shopUrl = $shopUrl['shop_ssl'] == '1' ? 'https://'.$shopUrl['shop_ssl_domain'] : 'http://'.$shopUrl['shop_ssl_domain'];
            }
        }

        $itemUrl = $shopUrl.DIR_WS_CATALOG;
        if ((_SYSTEM_MOD_REWRITE == 'true') && isset($product['ProductUrl']) && !empty($product['ProductUrl'])) {
            $itemUrl .= $product['ProductUrl'];
        } else {
            $itemUrl .= '?page=product&info='.$product['ProductId'];
        }

        if (($campaign = getDBConfigValue($this->settings['marketplace'].'', $this->_magnasession['mpID'], '')) != '') {

            $itemUrl .= ((strpos($itemUrl, '?') !== false) ? '&' : '?').'mlcampaign='.trim($campaign);
        }
        return $itemUrl;
    }

	protected function appendAdditionalData($iPID, $aProduct, &$aData) {
		$aPropertiesRow = MagnaDB::gi()->fetchRow('
			SELECT * FROM '.TABLE_MAGNA_CHECK24_PROPERTIES.'
			 WHERE ' . ((getDBConfigValue('general.keytype', '0') == 'artNr')
				? 'products_model = "'.MagnaDB::gi()->escape($aProduct['ProductsModel']).'"'
				: 'products_id = "'.$iPID.'"'
			) . '
			       AND mpID = '.$this->_magnasession['mpID']
		);
		
		// Will not happen in sumbit cycle but can happen in loadProductByPId.
		if (empty($aPropertiesRow)) {
			$aData['submit'] = array();
			return;
		}

        $aData['submit']['CategoryPath'] = renderCategoryPath($iPID, 'product', ' > ');

		$aData['submit']['SKU'] = $aData['submit']['MasterSKU'] = ($this->settings['keytype'] == 'artNr') ? $aProduct['MarketplaceSku'] : $aProduct['MarketplaceId'];
		$aData['submit']['Title'] = $aProduct['Title'];

		if (!empty($aProduct['Description'])) {
			$aData['submit']['Description'] = sanitizeProductDescription($aProduct['Description']);
		}

		if (empty($aProduct['Manufacturer']) === false) {
			$aData['submit']['Manufacturer'] = $aProduct['Manufacturer'];
		} else {
			$manufacturerName = getDBConfigValue($this->marketplace.'.checkin.manufacturerfallback', $this->mpID, '');
			if (empty($manufacturerName) === false) {
				$aData['submit']['Manufacturer'] = $manufacturerName;
			}
		}

		if (empty($aProduct['ManufacturerPartNumber']) === false) {
			$aData['submit']['ManufacturerPartNumber'] = $aProduct['ManufacturerPartNumber'];
		}

		if (empty($aProduct['EAN']) === false) {
			$aData['submit']['EAN'] = $aProduct['EAN'];
		}

		if (empty($aProduct['Images']) === false) {
            $imagePath = getDBConfigValue($this->marketplace.'.imagepath', $this->_magnasession['mpID'], SHOP_URL_POPUP_IMAGES);
            $imagePath = trim($imagePath, '/ ').'/';
			foreach($aProduct['Images'] as $sImg) {
				$aData['submit']['Images'][] = array('URL' => $imagePath.$sImg);
			}
		}

		if (isset($aProduct['Weight']) && !empty($aProduct['Weight'])) {
			$aData['submit']['Weight'] = $aProduct['Weight'];
		}
		$aData['submit']['ProductUrl'] = $this->getItemUrl($aProduct);
		$aData['submit']['Quantity'] = $aData['quantity'];
		$aData['submit']['Price'] = $aData['price'];
		$aData['submit']['BasePrice'] = $aProduct['BasePrice'];
		$aData['submit']['ShippingTime'] = $aPropertiesRow['ShippingTime'];
		$aData['submit']['ShippingCost'] = $aPropertiesRow['ShippingCost'];
		if (!empty($aPropertiesRow['ItemHandlingData'])) {
			$aItemHandlingData = json_decode($aPropertiesRow['ItemHandlingData'], true);
			foreach ($aItemHandlingData as $sIHKey => $sIHValue) {
				$aData['submit'][$sIHKey] = $sIHValue;
			}
			if (    array_key_exists('DeliveryMode', $aData['submit'])
			     && ($aData['submit']['DeliveryMode'] == 'EigeneAngaben')) {
				if (array_key_exists('DeliveryModeText', $aData['submit'])
			             && !empty($aData['submit']['DeliveryModeText'])) {
					$aData['submit']['DeliveryMode'] = $aData['submit']['DeliveryModeText'];
					unset($aData['submit']['DeliveryModeText']);
				}
			}
			if (!array_key_exists('CustomTariffsNumber', $aData['submit'])) {
			// use config value
				$aCustomTariffsNumberDBMatching = getDBConfigValue($this->marketplace.'.custom_tariffs_number.dbmatching.table', $this->mpID, '');
				if (    !empty($aCustomTariffsNumberDBMatching)
				     && isset($aCustomTariffsNumberDBMatching['column'])
				     && isset($aCustomTariffsNumberDBMatching['table'])) {
					$aData['submit']['CustomTariffsNumber'] = (string)MagnaDB::gi()->fetchOne('SELECT '.$aCustomTariffsNumberDBMatching['column'].' FROM '.$aCustomTariffsNumberDBMatching['table'].' WHERE products_id = '.$iPID.' LIMIT 1');
					if (empty($aData['submit']['CustomTariffsNumber'])) {
						unset($aData['submit']['CustomTariffsNumber']);
					}
				}
			}
		}
	}

	protected function markAsFailed($sku) {
		$iPID = magnaSKU2pID($sku);
		$this->badItems[] = $iPID;
		unset($this->selection[$iPID]);
	}

	/*protected function postSubmit() {
		try {
			$result = MagnaConnector::gi()->submitRequest(array(
				'ACTION' => 'UploadItems',
			));
		} catch (MagnaException $e) {
			$this->submitSession['api']['exception'] = $e;
			$this->submitSession['api']['html'] = MagnaError::gi()->exceptionsToHTML();
		}
	}*/

}
