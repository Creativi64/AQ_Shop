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
 * (c) 2010 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */

defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');
require_once(DIR_MAGNALISTER_INCLUDES.'lib/classes/CheckinSubmit.php');

class ComparisonShoppingCheckinSubmit extends CheckinSubmit {
	protected $firstRun = false;
	protected $priceConfig = array();

	protected $quantitySub = false;
	protected $quantityLumb = false;
	
	public function __construct($settings = array()) {
		global $_MagnaSession, $_modules;

		$settings = array_merge(array(
			'language' => getDBConfigValue($_MagnaSession['currentPlatform'].'.lang', $_MagnaSession['mpID']),
			'currency' => getCurrencyFromMarketplace($_MagnaSession['mpID']),
			'keytype' => getDBConfigValue('general.keytype', '0'),
			'itemsPerBatch' => 100,
			'mlProductsUseLegacy' => false,
		), $settings);

		$this->priceConfig = self::loadPriceSettings($_MagnaSession['mpID']);

		$stockSetting = getDBConfigValue($this->marketplace.'.quantity.type', $this->mpID);
		if ($stockSetting == 'stocksub') {
			$this->quantitySub = -getDBConfigValue(
				$this->marketplace.'.quantity.value',
				$this->mpID,
				0
			);
			$this->quantityLumb = false;
		} else if ($stockSetting == 'lump') {
			$this->quantitySub = false;
			$this->quantityLumb = getDBConfigValue(
				$this->marketplace.'.quantity.value',
				$this->mpID,
				0
			);
		}
		parent::__construct($settings);
	}
	
	public static function loadPriceSettings($mpId) {
		$mp = magnaGetMarketplaceByID($mpId);

		$config = array(
			'AddKind' => 'percent', // hard coded because not allowed for ComparisonShopping
			'Factor'  => 0,
			'Signal'  => getDBConfigValue($mp.'.price.signal', $mpId, ''),
			'Group'   => getDBConfigValue($mp.'.price.group', $mpId, ''),
			'UseSpecialOffer' => getDBConfigValue(array($mp.'.price.usespecialoffer', 'val'), $mpId, false),
			'Currency' => getCurrencyFromMarketplace($mpId),
			'ConvertCurrency' => getDBConfigValue(array($mp.'.exchangerate', 'update'), $mpId, false),
		);

		return $config;
	}
	
	private function initUploadInfo() {
		if (MagnaConnector::gi()->getSubsystem() == 'ComparisonShopping') {
			$path = '';
			try {
				$result = MagnaConnector::gi()->submitRequest(array(
					'ACTION' => 'GetCSInfo',
				));
				$path = $result['DATA']['CSVPath'];
				$this->initSession['upload'] = $result['DATA']['HasUpload'] != 'no';
			} catch (MagnaException $e) {
				$path = '';
			}
			$this->firstRun = empty($path);
		}
	}

	private function initRequiredFileds() {
		$this->initSession['RequiredFileds'] = array();
		try {
			$requiredFileds = MagnaConnector::gi()->submitRequest(array(
				'ACTION' => 'GetRequiredKeys',
			));
			if (!empty($requiredFileds['DATA'])) {
				foreach ($requiredFileds['DATA'] as $key) {
					$this->initSession['RequiredFileds'][$key] = true;
				}
			}
		} catch (MagnaException $e) { }
	}

	public function init($mode, $items = -1) {
		parent::init($mode, $items);
		$this->initUploadInfo();
		$this->initRequiredFileds();
	}

	public function makeSelectionFromErrorLog() {
		$madeSelection = false;
		
		$sanitizedIDs = array();
		foreach ($_POST['errIDs'] as $errID) {
			if (ctype_digit($errID)) {
				$sanitizedIDs[] = $errID;
			}
		}
		$res = MagnaDB::gi()->fetchArray('
			SELECT id, products_id, products_model, product_details
			  FROM '.TABLE_MAGNA_CS_ERRORLOG.' 
			 WHERE id IN ('.implode(', ', $sanitizedIDs).')
		');	
	
		if (!empty($res)) {
			$pIDs = array();
			$errorIDs = array();
			$data = array();
			
			foreach ($res as $item) {
				if (getDBConfigValue('general.keytype', '0') == 'artNr') {
					$pID = MagnaDB::gi()->fetchOne('
						SELECT products_id FROM '.TABLE_PRODUCTS.' WHERE products_model=\''.MagnaDB::gi()->escape($item['products_model']).'\'
					');
					if ($pID !== false) {
						$pIDs[] = $pID;
					}
				} else {
					if (MagnaDB::gi()->recordExists(TABLE_PRODUCTS, array('products_id' => $item['products_id']))) {
						$pIDs[] = $pID = $item['products_id'];
					} else {
						$pID = false;
					}
				}
				if ($pID !== false) {
					$errorIDs[$item['id']] = $pID;
					$data[$pID] = $item['product_details'];
				}
			}

			$pIDs = array_unique($pIDs);
			$batch = array();
			foreach ($pIDs as $pID) {
				$selection[$pID] = $data[$pID];
				$batch[] = array(
					'pID' => $pID,
					'data' => $data[$pID],
					'mpID' => $this->_magnasession['mpID'],
					'selectionname' => $this->settings['selectionName'],
					'session_id' => session_id(),
					'expires' => gmdate('Y-m-d H:i:s')
				);
			}
			MagnaDB::gi()->batchinsert(TABLE_MAGNA_SELECTION, $batch, true);
			unset($data);
			unset($pIDs);
			unset($batch);
			
			$this->initSession['selectionFromErrorLog'] = $errorIDs;
			$madeSelection = true;
		}

		return $madeSelection;
	}

	protected function generateRequestHeader() {
		return array(
			'ACTION' => 'AddItems',
			'MODE' => $this->submitSession['mode']
		);
	}

	public function getcategoriesname($pID) {
		$catnames = array();
		$i = 0;

		// Maximale Kategorientiefe, bis zu der der Name der Ueberkategorie geholt wird. Kein von Billiger vorgegebener Wert, kann nach
		// persoenlichem Ermessen geaendert werden (aber nicht weglassen wg. moeglichem infinite loop!)
		$maxcatlevel = 4;

		$catdata = MagnaDB::gi()->fetchRow('
			SELECT p.categories_id, c.parent_id
			  FROM '.TABLE_PRODUCTS_TO_CATEGORIES.' p JOIN '.TABLE_CATEGORIES.' c ON p.categories_id = c.categories_id
			 WHERE products_id = '.$pID.'
			 LIMIT 1
		');
		$parentid = $catdata['parent_id'];
		$catnames[] = $catdata['categories_id'];

		while (($parentid != 0) && ($i < $maxcatlevel)) {
			$catdata = MagnaDB::gi()->fetchRow('
				SELECT categories_id, parent_id
				  FROM '.TABLE_CATEGORIES.'
				 WHERE categories_id = '.$parentid.'
				 LIMIT 1
			');
			$catnames[] = $catdata['categories_id'];
			$parentid = $catdata['parent_id'];
			++$i;
		}
		$catstring = '';
		$catnames = array_reverse($catnames);

        $categoryStoreIdQuery = (MagnaDB::gi()->columnExistsInTable('categories_store_id', TABLE_CATEGORIES_DESCRIPTION)
            ? ' AND categories_store_id = '.getDBConfigValue($this->marketplace.'.shopmandant', $this->mpID, '0').' '
            : '');

		foreach ($catnames as $value) {
			if (!empty($value)) {
				$cName = MagnaDB::gi()->fetchOne('
					SELECT categories_name
					  FROM '.TABLE_CATEGORIES_DESCRIPTION.'
					 WHERE categories_id = '.$value.'
					       AND language_code = "'.$this->settings['languagecode'].'"
					       '.$categoryStoreIdQuery.'
					 LIMIT 1
				');
				if (empty($catstring)) {
					$catstring = $cName;
				} else {
					$catstring .= ' > '.$cName;
				}
			}
		}
		return $catstring;
	}
    
	protected function setUpMLProduct() {
        parent::setUpMLProduct();
        $mp = magnaGetMarketplaceByID($this->mpID);
        $config = array(
            'Type'  => getDBConfigValue($mp.'.quantity.type', $this->mpID, 'stock'),
            'Value' => (int)getDBConfigValue($mp.'.quantity.value', $this->mpID, 0),
        );
        $iMaxQuantity = (int)getDBConfigValue($mp.'.quantity.maxquantity', $this->mpID, 0);
        if ($iMaxQuantity > 0) {
            $config['MaxQuantity'] = $iMaxQuantity;
        }
        MLProduct::gi()->setQuantityConfig($config);
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

	protected function appendAdditionalData($pID, $product, &$data) {
		#echo print_m(func_get_args(), __METHOD__);
		$finalPrice = $this->simpleprice->setFinalPriceFromDB($pID, $this->mpID, $this->priceConfig)->roundPrice()->getPrice();
		$shippingTime = MagnaDB::gi()->fetchOne(eecho('
		     SELECT status_name 
		       FROM '.TABLE_SYSTEM_STATUS_DESCRIPTION.' 
		      WHERE status_id=\''.$product['ShippingTimeId'].'\'
		            AND language_code=\''.$this->settings['languagecode'].'\'
		      LIMIT 1
		', false));
		if (empty($shippingTime)) {
			$shippingTime = (string)getDBConfigValue($this->settings['marketplace'].'.shipping.time', $this->_magnasession['mpID']);
		}
		if ($product['ManufacturerId'] > 0) {
			$manufacturerName = MagnaDB::gi()->fetchOne(
				'SELECT manufacturers_name FROM '.TABLE_MANUFACTURERS.' WHERE manufacturers_id=\''.$product['ManufacturerId'].'\''
			);
		} else {
			$manufacturerName = '';
		}

		$mfrmd = getDBConfigValue('comparisonshopping.checkin.manufacturerpartnumber.table', $this->mpID, false);

		if (is_array($mfrmd) && !empty($mfrmd['column']) && !empty($mfrmd['table'])) {
			$pIDAlias = getDBConfigValue('comparisonshopping.checkin.manufacturerpartnumber.alias', $this->mpID);
			if (empty($pIDAlias)) {
				$pIDAlias = 'products_id';
			}
			$data['submit']['ManufacturerPartNumber'] = MagnaDB::gi()->fetchOne('
				SELECT `'.$mfrmd['column'].'`
				  FROM `'.$mfrmd['table'].'`
				 WHERE `'.$pIDAlias.'`=\''.MagnaDB::gi()->escape($pID).'\'
				 LIMIT 1
			');
		}
		
		$imageURLPath = getDBConfigValue($this->marketplace.'.imagepath', $this->_magnasession['mpID'], SHOP_URL_POPUP_IMAGES);
		$imageURLPath = empty($imageURLPath) ? SHOP_URL_POPUP_IMAGES : $imageURLPath;
		if (!empty($product['Images'][0])) {
			$imageUrl = $imageURLPath. $product['Images'][0];
		} else {
			$imageUrl = '';
		}
        
		$data['submit']['SKU']				= magnaPID2SKU($product['ProductId']);
		$data['submit']['ItemTitle']		= $product['Title'];
		$data['submit']['Price']			= $finalPrice;
		$data['submit']['Currency']			= $this->settings['currency'];
		$data['submit']['Description']		= sanitizeProductDescription($product['Description']);
		$data['submit']['ItemUrl']			= $this->getItemUrl($product); # Vollstaendiger Link zum Artikel im Shop.
		$data['submit']['Manufacturer']		= $manufacturerName;
		$data['submit']['Image']			= $imageUrl;					# Vollstaendiger Pfad zum Bild im Shop.
		$data['submit']['ShippingCost']		= (string)$data['shippingcost'];
		$data['submit']['ShippingTime']		= $shippingTime;				# in Tagen
		$data['submit']['ItemWeight']		= ($product['Weight']['Value'] != 'null') ? $product['Weight']['Value'] : '';	# in kg
		$data['submit']['Quantity']			= $product['Quantity'];
		if (defined('MAGNA_FIELD_PRODUCTS_EAN') && array_key_exists('EAN', $product)) {
			$data['submit']['EAN']			= ($product['EAN'] != 'null') ? $product['EAN'] : '';
		}
		// BasePrice
		if (isset($product['BasePrice']) && isset($product['BasePrice']['Value']) && (0 <> $product['BasePrice']['Value'])) {
			$data['submit']['BasePrice'] = array (
				'Unit' => $product['BasePrice']['Unit'],
				'Value' => $product['BasePrice']['Value'],
			);
		}
	}
	
	protected function generateErrorSaveArray(&$data) {
		return array (
			'shippingcost' => $data['submit']['ShippingCost'],
		);
	}
	
	protected function filterItem($pID, $data) {
		return array();
	}

	protected function filterSelection() {
		if (!is_array($this->initSession['RequiredFileds'])) {
			$this->initRequiredFileds();
		}

		$shitHappend = false;
		$missingFields = array();
		foreach ($this->selection as $pID => &$data) {
			if ($data['submit']['Price'] <= 0) {
				// Loesche das Feld, um eine Fehlermeldung zu erhalten
				unset($data['submit']['Price']);
			}
			
			$mfC = array();
			
			$this->requirementsMet($data['submit'], $this->initSession['RequiredFileds'], $mfC);
			$mfC = array_merge($mfC, $this->filterItem($pID, $data['submit']));

			if (!empty($mfC)) {
				foreach ($mfC as $key => $field) {
					$mfC[$key] = ltrim(strtoupper(preg_replace('/([A-Z][a-z])/', '_${1}', $field)), '_');
				}
				MagnaDB::gi()->insert(
					TABLE_MAGNA_CS_ERRORLOG,
					array (
						'mpID' => $this->_magnasession['mpID'],
						'products_id' => $pID,
						'products_model' => MagnaDB::gi()->fetchOne('SELECT products_model FROM '.TABLE_PRODUCTS.' WHERE products_id=\''.$pID.'\''),
						'product_details' => serialize($this->generateErrorSaveArray($data)),
						'errormessage' => json_encode($mfC),
						'timestamp' => gmdate('Y-m-d H:i:s')
					)
				);
				$shitHappend = true;
				$this->badItems[] = $pID;
				unset($this->selection[$pID]);
				$this->ajaxReply['ignoreErrors'] = true;
			}
		}
		return $shitHappend;
	}
	
	protected function processSubmitResult($result) {}
	
	protected function postSubmit() {
		#echo 'postSubmit';
		if (isset($this->initSession['selectionFromErrorLog']) && !empty($this->initSession['selectionFromErrorLog'])) {
			foreach ($this->initSession['selectionFromErrorLog'] as $errID => $pID) {
				MagnaDB::gi()->delete(
					TABLE_MAGNA_CS_ERRORLOG,
					array(
						'id' => (int)$errID
					)
				);
			}
		}
		if ($this->initSession['upload'] === null) {
			$this->initUploadInfo();
		}
		#echo var_dump_pre($this->initSession['upload']);
		if ($this->initSession['upload']) {
			try {
				$result = MagnaConnector::gi()->submitRequest(array(
					'ACTION' => 'UploadItems',
					'MODE' => $this->submitSession['initialmode']
				));
				#echo print_m($result, true);
			} catch (MagnaException $e) {
				#echo print_m($e, 'Exception', true);
				$this->submitSession['api']['exception'] = $e->getErrorArray();
			}
		}
	}

	protected function generateRedirectURL($state) {
		return toURL(array(
			'mp' => $this->realUrl['mp'],
			'mode'   => 'listings',
			'view'   => ($state == 'fail') ? 'failed' : 'inventory'
		), true);
	}

	protected function getFinalDialogs() {
		global $_modules;
		if ($this->firstRun && ($this->submitSession['state']['success'] > 0)) {
			$path = '';
			try {
				$result = MagnaConnector::gi()->submitRequest(array(
					'ACTION' => 'GetCSInfo',
				));
				$path = $result['DATA']['CSVPath'];
			} catch (MagnaException $e) { }
			return array (
				array (
					'headline' => ML_LABEL_INFORMATION,
					'message' => sprintf(ML_CSHOPPING_TEXT_FIRST_CHECKIN, $_modules[$this->settings['marketplace']]['title'], $path)
				),
			);
		}
		return array();
	}
}
