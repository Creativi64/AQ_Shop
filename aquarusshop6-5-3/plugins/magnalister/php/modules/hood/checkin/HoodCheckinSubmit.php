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
require_once(DIR_MAGNALISTER_MODULES.'hood/HoodHelper.php');
require_once(DIR_MAGNALISTER_MODULES.'hood/classes/HoodProductSaver.php');

class HoodCheckinSubmit extends MagnaCompatibleCheckinSubmit {
	const MAX_LEN_AUCTION_TITLE = 85;
	
	protected $priceConfig = array();
	protected $quantityConfig = array();
	
	protected $config = array();
	
	private $verify = false;
	private $lastException = null;
	protected $ignoreErrors = true;
	
	protected $hasAttributesSortOrder = false;
	
	public function __construct($settings = array()) {
		global $_MagnaSession;
		$settings = array_merge(array(
			'language' => getDBConfigValue($settings['marketplace'] . '.lang', $_MagnaSession['mpID']),
			'currency' => 'EUR',
			'itemsPerBatch' => 5
		), $settings);
		
		parent::__construct($settings);
		
		$this->priceConfig = HoodHelper::loadPriceSettings($this->_magnasession['mpID']);
		$this->quantityConfig = HoodHelper::loadQuantitySettings($this->_magnasession['mpID']);

		// do not in veyton - table does not exists in this shopsystem
		//$this->hasAttributesSortOrder = MagnaDB::gi()->columnExistsInTable('sortorder', TABLE_PRODUCTS_ATTRIBUTES);
		
		$this->config['maxImages'] = getDBConfigValue($this->marketplace.'.prepare.maximagecount', $this->_magnasession['mpID'], 'all');
		$this->config['maxImages'] = ($this->config['maxImages'] == 'all') ? true : (int)$this->config['maxImages'];
	}
	
	protected function generateRequestHeader() {
		# das Request braucht nur action, subsystem und data
		return array(
			'ACTION' => ($this->verify ? 'VerifyAddItems' : 'AddItems'),
			'SUBSYSTEM' => 'hood',
			'MODE' => isset($this->submitSession['mode']) ? $this->submitSession['mode'] : 'ADD',
		);
	}
	
	# Anders als im allg. Fall: Reihenfolge wie in der Auflistung
	protected function initSelection($offset, $limit) {
		$this->selection = array();
		if ($this->verify) {
			# fuer Verify nur Artikel mit gueltiger Menge und Preis nehmen, ausser man findet keine
			$verifySelectionResult = MagnaDB::gi()->query('
			    SELECT ms.pID pID, ms.data data
			      FROM '.TABLE_MAGNA_SELECTION.' ms, '.TABLE_PRODUCTS.' p, '.TABLE_PRODUCTS_DESCRIPTION.' pd
			     WHERE     mpID = "'.$this->_magnasession['mpID'].'"
			           AND selectionname = "'.$this->settings['selectionName'].'"
			           AND session_id = "'.session_id().'"
			           AND pd.language_code = "'.$this->settings['language'].'"
			           AND p.products_quantity > 0 AND p.products_price > 0.0
			           AND p.products_id = ms.pID
			           AND pd.products_id = ms.pID
			  ORDER BY pd.products_name ASC
			     LIMIT '.$offset.','.$limit.'
			');
			while ($row = MagnaDB::gi()->fetchNext($verifySelectionResult)) {
				$this->selection[$row['pID']] = unserialize($row['data']);
			}
			if (!empty($this->selection)) {
				return;
			}
		}
		parent::initSelection($offset, $limit);
	}
	
	protected function calcVariationPrice($price, $offset, $tax) {
		return $price;
	}
	
	protected function getVariations($pID, $product, &$data) {
		MLProduct::gi()->setPriceConfig($this->priceConfig['Fixed']);
		MLProduct::gi()->setQuantityConfig($this->quantityConfig['Fixed']);
		MLProduct::gi()->setLanguage($this->settings['language']);
		$p = MLProduct::gi()->getProductById($pID); // with variations
		$sSkuKey = (getDBConfigValue('general.keytype', '0') == 'artNr' ? 'MarketplaceSku' : 'MarketplaceId');
		if (!empty($p['Variations'])) {
			$data['submit']['Variations'] = array();
			foreach($p['Variations'] as $aVariation) {
				$aCurrVariation = array(
					'SKU'       => $aVariation[$sSkuKey],
					'Price'     => $aVariation['Price'],
					'Quantity'  => $aVariation['Quantity'],
					'Variation' => $aVariation['Variation']
				);
				if (array_key_exists('EAN', $aVariation)) {
					$aCurrVariation['EAN'] = $aVariation['EAN'];
				}
				$data['submit']['Variations'][] = $aCurrVariation;
				unset($aCurrVariation);
			}
		}
		
	}
	
	protected function appendOfferData($pID, $product, &$data) {
		$listingType = ($data['submit']['ListingType'] == 'classic') ? 'Auction' : 'Fixed';
		if ($data['submit']['ListingType'] == 'classic'){
			if (!isset($data['submit']['StartPrice'])) {
				$data['submit']['StartPrice'] = $this->simpleprice
					->setFinalPriceFromDB($pID, $this->_magnasession['mpID'], $this->priceConfig['Auction']['StartPrice'])
					->roundPrice()->getPrice();
			}
			
			if ($this->priceConfig['Auction']['BuyItNow']['UseBuyItNow']) {
				$data['submit']['Price'] = $this->simpleprice
					->setFinalPriceFromDB($pID, $this->_magnasession['mpID'], $this->priceConfig['Auction']['BuyItNow'])
					->roundPrice()->getPrice();
			}
		} else {
			$data['submit']['Price'] = $this->simpleprice
				->setFinalPriceFromDB($pID, $this->_magnasession['mpID'], 'fixed')
				->roundPrice()->getPrice();
		}
		$data['submit']['Tax'] = SimplePrice::getTaxByClassID($product['products_tax_class_id']);
		$data['submit']['Quantity'] = HoodHelper::calcQuantity($product['products_quantity'], $this->quantityConfig[$listingType]);
		
		if (('shopProduct' == $data['submit']['ListingType']) && getDBConfigValue(array(
				$this->_magnasession['currentPlatform'] . '.usevariations', 'val'
			), $this->_magnasession['mpID'], true)
		) {
			$this->getVariations($pID, $product, $data);
			if (    array_key_exists('Variations', $data['submit'])
			     && (!is_array($data['submit']['Variations'])
			          || (    is_array($data['submit']['Variations'])
			               && count($data['submit']['Variations']) == 0
			             ) 
			        ) 
			) {
				unset($data['submit']['Variations']);
			}
		}
	}
	
	/**
	 * Hilfsfunktion: Fuer den Fall dass am Ende des Titels ein #BASEPRICE# steht,
	 * das durch die 85-Zeichen-Beschraenkung abgeschnitten wurde
	 */	
	protected function buildTitle($pId, $title, $data, $maxlen) {
		$title = html_entity_decode(fixHTMLUTF8Entities($title), ENT_COMPAT, 'UTF-8');
		$bp = '';
		if (isset($data['#BASEPRICE#']) && !empty($data['#BASEPRICE#'])) {
			$bp = $data['#BASEPRICE#'];
			unset($data['#BASEPRICE#']);
		}
		$title = HoodHelper::substituteTemplate($this->_magnasession['mpID'], $pId, $title, $data);
		if (empty($bp)) {
			$title = str_replace('#BASEPRICE#', '', $title);
		}
		if (strpos($title, '#BASEPRICE#') !== false) {
			$len = strlen($title) - strlen('#BASEPRICE#') + strlen($bp);
			if ($len > $maxlen) {
				$aT = explode('#BASEPRICE#', $title);
				$reduce = $len - $maxlen;
				for ($i = count($aT) - 1; $i >= 0; --$i) {
					if ($reduce == 0) {
						break;
					}
					$iL = strlen($aT[$i]);
					if ($iL > $reduce) {
						$aT[$i] = substr($aT[$i], 0, $iL - $reduce);
						$reduce = 0;
					} else {
						$reduce -= $iL;
						$aT[$i] = '';
					}
				}
				$title = implode('#BASEPRICE#', $aT);
			}
			$title = HoodHelper::substituteTemplate($this->_magnasession['mpID'], $pId, $title, array(
				'#BASEPRICE#' => $bp
			));
		}
		
		return substr($title, 0, $maxlen);
	}
	
	protected function appendAdditionalData($pID, $product, &$data) {
		$propertiesRow = MagnaDB::gi()->fetchRow('
			SELECT * FROM ' . TABLE_MAGNA_HOOD_PROPERTIES . '
			 WHERE ' . ((getDBConfigValue('general.keytype', '0') == 'artNr') 
						? 'products_model="' . MagnaDB::gi()->escape($product['products_model']) . '"'
						: 'products_id="' . $pID . '"'
					) . ' 
			       AND mpID = ' . $this->_magnasession['mpID']
		);
		#echo var_dump_pre($propertiesRow, '$propertiesRow');
		// Will not happen in sumbit cycle but can happen in loadProductByPId.
		if (empty($propertiesRow)) {
			$data['submit'] = array();
			return;
		}
		
		foreach (array('ShippingServiceOptions', 'PaymentMethods', 'GalleryPictures', 'Features') as $jsonKey) {
			$propertiesRow[$jsonKey] = json_decode($propertiesRow[$jsonKey], true);
		}
		
		$data['submit']['SKU'] = magnaPID2SKU($pID);
		
		/*
		echo print_m(func_get_args(), __METHOD__);
		echo print_m($propertiesRow, 'propertiesRow');
		//*/
		
		if (!empty($propertiesRow['Subtitle'])) {
			$data['submit']['SubTitle'] = $propertiesRow['Subtitle'];
		}
		
		if (!empty($propertiesRow['StartTime'])) {
			$data['submit']['StartTime'] = $propertiesRow['StartTime'];
		}
		
		$data['submit']['Images'] = array();
		if (is_array($propertiesRow['GalleryPictures'])
			&& isset($propertiesRow['GalleryPictures']['BaseUrl']) && is_string($propertiesRow['GalleryPictures']['BaseUrl']) && !empty($propertiesRow['GalleryPictures']['BaseUrl'])
			&& isset($propertiesRow['GalleryPictures']['Images'])  && is_array($propertiesRow['GalleryPictures']['Images'])   && !empty($propertiesRow['GalleryPictures']['Images'])
		) {
			if ($propertiesRow['GalleryPictures']['BaseUrl'] == '/') {
				$propertiesRow['GalleryPictures']['BaseUrl'] = '';
			}
			$maxImages = $this->config['maxImages'];
			foreach ($propertiesRow['GalleryPictures']['Images'] as $img => $imgSubmit) {
				if (!$imgSubmit || ((int)$maxImages <= 0)) {
					continue;
				}
				$data['submit']['Images'][] = array(
					'URL' => $propertiesRow['GalleryPictures']['BaseUrl'].$img,
				);
				if ($maxImages !== false) {
					--$maxImages;
				}
			}
		}
		
		if (isset($product['products_ean']) && !empty($product['products_ean'])) {
			$data['submit']['EAN'] = $product['products_ean'];
		}
		
		if (isset($product['products_weight']) && ((float)$product['products_weight'] > 0)) {
			$data['submit']['Weight'] = array (
				'Unit' => 'kg',
				'Value' => $product['products_weight'],
			);
		}
		
		if ($propertiesRow['ConditionType']) {
			$data['submit']['ConditionType'] = $propertiesRow['ConditionType'];
		}

		if ($propertiesRow['noIdentifierFlag']) {
			$data['submit']['NoIdentifierFlag'] = $propertiesRow['noIdentifierFlag'];
		}
		
		foreach (array('FSK', 'USK') as $ageThingy) {
			if ($propertiesRow[$ageThingy] != '-1') {
				$data['submit'][$ageThingy] = $propertiesRow[$ageThingy];
			}
		}
		
		$data['submit']['Features'] = $propertiesRow['Features'];
		
		$data['submit']['Manufacturer'] = $propertiesRow['Manufacturer'];
		$data['submit']['ManufacturerPartNumber'] = $propertiesRow['ManufacturerPartNumber'];
		
		$data['submit']['MarketplaceCategories'][] = $propertiesRow['PrimaryCategory'];
		if (!empty($propertiesRow['SecondaryCategory'])) {
			$data['submit']['MarketplaceCategories'][] = $propertiesRow['SecondaryCategory'];
		}
		foreach (array('StoreCategory', 'StoreCategory2', 'StoreCategory3') as $kat) {
			if (!empty($propertiesRow[$kat])) {
				$data['submit']['StoreCategories'][] = $propertiesRow[$kat];
			}
		}
		
		$data['submit']['ListingType'] = $propertiesRow['ListingType'];
		$data['submit']['ListingDuration'] = $propertiesRow['ListingDuration'];
		
		$data['submit']['PaymentMethods'] = $propertiesRow['PaymentMethods'];
		
		$data['submit']['BasePrice'] = array (
			'Unit' => $product['products_vpe_name'],
			'Value' => $product['products_vpe_value'],
		);
		
		$data['submit']['ShippingTime'] = array();
		# send the stored data, validation happens on the API part
		$data['submit']['ShippingTime']['Min'] = trim(getDBConfigValue('hood.ShippingTime.Min', $this->_magnasession['mpID'], ''));
		$data['submit']['ShippingTime']['Max'] = trim(getDBConfigValue('hood.ShippingTime.Max', $this->_magnasession['mpID'], ''));
		
		$data['submit']['ShippingServices'] = $propertiesRow['ShippingServiceOptions'];
		
		if (($data['submit']['ListingType'] == 'classic') && ((float)$propertiesRow['StartPrice'] > 0)) {
			$data['submit']['StartPrice'] = $propertiesRow['StartPrice'];
		}
		
		$this->appendOfferData($pID, $product, $data);
		
		# The BasePrice string(!) for the title depends on the price. So this has to be created once the price is
		# known.
		if (isset($product['products_vpe_name']) && ($product['products_vpe_value'] != 0)) {
			$formatted_vpe = $this->simpleprice->setPrice($data['submit']['Price'] * (1.0 / $product['products_vpe_value']))->format()
				.'/'.fixHTMLUTF8Entities($product['products_vpe_name']);
		} else {
			$formatted_vpe = '';
		}
		# Titel: Entferne komische nicht-druckbare Zeichen wie &curren; & ggf VPE einsetzen
		$data['submit']['Title'] = $this->buildTitle($pID, $propertiesRow['Title'], array(
			'#BASEPRICE#' => $formatted_vpe
		), self::MAX_LEN_AUCTION_TITLE);
		
		$data['submit']['ShortDescription'] = $propertiesRow['ShortDescription'];
		
		# Wenn nicht in der Maske gefuellt
		if (empty($data['submit']['Description'])) {
			if (!empty($propertiesRow['Description'])) {
				# Beim Uebermitteln Preis einsetzen
				$data['submit']['Description'] = HoodHelper::substituteTemplate($this->_magnasession['mpID'], $pID, $propertiesRow['Description'], array(
					'#PRICE#' => $this->simpleprice->setPrice($data['submit']['Price'])->formatWOCurrency(),
					'#VPE#' => $formatted_vpe,
					'#BASEPRICE#' => $formatted_vpe
				));
			} else {
				$hoodTemplate = getDBConfigValue('hood.template.content', $this->_magnasession['mpID']);
				$imagePath = getDBConfigValue('hood.imagepath', $this->_magnasession['mpID']);
				$substitution = array(
					'#TITLE#' => fixHTMLUTF8Entities($product['products_name']),
					'#ARTNR#' => $product['products_model'],
					'#PID#' => $pID,
					'#SKU#' => $data['submit']['SKU'],
					'#SHORTDESCRIPTION#' => stripLocalWindowsLinks(stringToUTF8(isset($product['products_short_description']) ? $product['products_short_description'] : '')),
					'#DESCRIPTION#' => stripLocalWindowsLinks(stringToUTF8($product['products_description'])),
					'#PRICE#' => $this->simpleprice->setPrice($data['submit']['Price'])->formatWOCurrency(),
					'#VPE#' => $formatted_vpe,
					'#BASEPRICE#' => $formatted_vpe
				);
				$data['submit']['Description'] = stringToUTF8(HoodHelper::getSubstitutePictures(
					HoodHelper::substituteTemplate(
						$this->_magnasession['mpID'], $pID, $hoodTemplate, $substitution
					),
					$pID, $imagePath
				));
			}
		} else {
			$data['submit']['Description'] = stringToUTF8($data['submit']['Description']);
		}

		/* {Hook} "HoodCheckinSubmit_appendAdditionalData": Enables you to extend or modify the product data.<br>
		   Variables that can be used: <ul>
			   <li>$data: The data of a product.</li>
		   </ul>
		 */
		if (($hp = magnaContribVerify('HoodCheckinSubmit_appendAdditionalData', 1)) !== false) {
			require($hp);
		}
		#echo print_m($data['submit']);
	}
	
	public static function loadProductByPId($pId, $onlyActive = false) {
		global $_MagnaSession;
		
		$product = MLProduct::gi()->getProductByIdOld($pId);
		if ($onlyActive && ($product['products_status'] == '0')) {
			return array();
		}
		$data = array(
			'submit' => array (
				'SKU' => magnaPID2SKU($pId),
			),
		);
		$cs = new self(array (
			'marketplace' => $_MagnaSession['currentPlatform'],
		));
		$cs->appendAdditionalData($pId, $product, $data);
		
		arrayEntitiesToUTF8($data['submit']);
		
		return $data['submit'];
	}
	
	public static function loadOfferByPId($pId, $listingType, $onlyActive = false) {
		global $_MagnaSession;
		
		$product = MLProduct::gi()->getProductByIdOld($pId);
		if ($onlyActive && ($product['products_status'] == '0')) {
			return array();
		}
		$data = array (
			'submit' => array (
				'SKU' => magnaPID2SKU($pId),
				'ListingType' => $listingType,
			),
		);
		$cs = new self(array (
			'marketplace' => $_MagnaSession['currentPlatform'],
		));
		
		$cs->appendOfferData($pId, $product, $data);
		
		arrayEntitiesToUTF8($data['submit']);
		
		return $data['submit'];
	}
	
	protected function preSubmit(&$request) {
		//echo print_m(json_indent(json_encode($request)));
	}
	
	protected function postSubmit() {
		try {
			$result = MagnaConnector::gi()->submitRequest(array(
				'ACTION' => 'UploadItems'
			));
			MagnaConnector::gi()->resetTimeOut();
		}
		catch (MagnaException $e) {
			$this->submitSession['api']['exception'] = $e;
			$this->submitSession['api']['html'] = MagnaError::gi()->exceptionsToHTML();
		}
	}
	
	protected function processSubmitResult($result) {
		//echo print_m($result, '$result');
	}
	
	protected function filterSelection() {
		# Anzahlen <=0 wegfiltern, ausser es sind Shop-Produkte
		foreach ($this->selection as $pID => &$data) {
			if (    ((int) $data['submit']['Quantity'] <= 0) 
			     && ($data['submit']['ListingType']    != 'shopProduct')
			) {
				unset($this->selection[$pID]);
				$this->disabledItems[] = $pID;
			}
		}
	}
	
	protected function generateRedirectURL($state) {
		return toURL(array(
			'mp' => $this->realUrl['mp'],
			'mode' => 'listings',
			'view' => ($state == 'fail') ? 'failed' : 'inventory'
		), true);
	}
	
	protected function processException($e) {
		$this->lastException = $e;
	}
	
	public function getLastException() {
		return $this->lastException;
	}
	
	protected function generateCustomErrorHTML() {
		$exs = MagnaError::gi()->getExceptionCollection();
		$html = '';
		foreach ($exs as $ex) {
			if (!is_object($ex) || ($ex->getSubsystem() == 'PHP') || (($ex->getSubsystem() == 'Core'))) {
				continue;
			}
			$errors = $ex->getErrorArray();
			/* ... als unkrittisch markieren. */
			//$ex->setCriticalStatus(false);
			
			foreach ($errors['ERRORS'] as $err) {
				echo print_m($err, __METHOD__);
			}
		}
		return $html;
	}
	
	public function verifyOneItem($echoRequest = false) {
		$this->verify = true;
		MagnaDB::gi()->delete(TABLE_MAGNA_SELECTION, array(
			'mpID' => $this->_magnasession['mpID'],
			'selectionname' => $this->settings['selectionName'].'Verify',
			'session_id' => session_id()
		));
		$item = MagnaDB::gi()->fetchRow('
			SELECT * FROM '.TABLE_MAGNA_SELECTION.' 
			 WHERE mpID="'.$this->_magnasession['mpID'].'" AND
			       selectionname="'.$this->settings['selectionName'].'" AND
			       session_id="'.session_id().'"
			 LIMIT 1
		');
		if (empty($item)) {
			return false;
		}
		
		$oldSelectionName = $this->settings['selectionName'];
		$this->settings['selectionName'] = $this->settings['selectionName'].'Verify';
		$item['selectionname'] = $this->settings['selectionName'];
		MagnaDB::gi()->insert(TABLE_MAGNA_SELECTION, $item);
		
		//echo print_m($this->settings, '$this->settings');

		$this->initSelection(0, 1);
		$this->init('Verify', count($this->selection));

		//echo print_m($this->selection, '$this->selection[1]');
		$this->populateSelectionWithData();
		//echo print_m($this->selection, '$this->selection[2]');
		foreach ($this->selection as $pID => &$data) {
			if (!isset($data['submit']['Quantity']) || ((int)$data['submit']['Quantity'] == 0)) {
				$data['submit']['Quantity'] = 1; // hack to get verification of zero quantity items working
			}
		}
		
		$result = $this->sendRequest(false, $echoRequest);
		
		MagnaDB::gi()->delete(TABLE_MAGNA_SELECTION, array(
			'mpID' => $this->_magnasession['mpID'],
			'selectionname' => $this->settings['selectionName'],
			'session_id' => session_id()
		));
		
		// restore selection name
		$this->settings['selectionName'] = $oldSelectionName;
		
		# Liste der pIDs um die ebay_properties upzudaten
		$selectedPidsArray = MagnaDB::gi()->fetchArray('
			SELECT DISTINCT pID 
			  FROM '.TABLE_MAGNA_SELECTION.'
			 WHERE mpID="'.$this->_magnasession['mpID'].'"
			       AND selectionname="'.$this->settings['selectionName'].'"
			       AND session_id="'.session_id().'"
		');
		$selectedPidsList = '';
		foreach ($selectedPidsArray as $pIDsRow) {
			if (is_numeric($pIDsRow['pID'])) $selectedPidsList .= $pIDsRow['pID'].', ';
		}
		$selectedPidsList = trim($selectedPidsList, ', ');
		MagnaDB::gi()->query('
			UPDATE ' . TABLE_MAGNA_HOOD_PROPERTIES . '
			   SET Verified="'.(('SUCCESS' == $result['STATUS']) ? 'OK' : 'ERROR').'"
			 WHERE mpID = ' . $this->_magnasession['mpID'] . '
			      AND products_id IN (' . $selectedPidsList . ')
		');
		return $result;
	}
	
}
