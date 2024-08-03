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
 * $Id: AmazonCheckinSubmit.php 961 2016-08-23 08:23:00Z tim.neumann $
 *
 * (c) 2010 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */

defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');
require_once(DIR_MAGNALISTER_INCLUDES.'lib/classes/CheckinSubmit.php');
require_once(DIR_MAGNALISTER_MODULES.'amazon/amazonFunctions.php');
require_once(DIR_MAGNALISTER_MODULES.'amazon/AmazonHelper.php');

class AmazonCheckinSubmit extends CheckinSubmit {
	private $checkinDetails = array();
	
	public function __construct($settings = array()) {
		global $_MagnaSession;
		/* Setzen der Currency nicht noetig, da Preisberechnungen bereits in 
		   der AmazonSummaryView Klasse gemacht wurden.
		 */
		$settings = array_merge(array(
			'mlProductsUseLegacy' => !defined('MAGNA_VEYTON_MULTIVARIANTS') || MAGNA_VEYTON_MULTIVARIANTS !== true,
			'language' => getDBConfigValue($_MagnaSession['currentPlatform'].'.lang', $_MagnaSession['mpID']),
			'itemsPerBatch' => 100,
			'keytype' => getDBConfigValue('general.keytype', '0'),
			'skuAsMfrPartNo' => getDBConfigValue(array('amazon.checkin.SkuAsMfrPartNo', 'val'), $_MagnaSession['mpID'], false),
		), $settings);
		
		parent::__construct($settings);
	}
	
	public function makeSelectionFromErrorLog() {}
	
	protected function generateRequestHeader() {
		return array(
			'ACTION' => 'AddItems',
			'MODE' => $this->submitSession['mode']
		);
	}
	
	protected function markAsFailed($productId) {
		MagnaDB::gi()->insert(
			TABLE_MAGNA_AMAZON_ERRORLOG,
			array (
				'mpID' => $this->_magnasession['mpID'],
				'batchid' => '-',
				'errormessage' => ML_GENERIC_ERROR_UNABLE_TO_LOAD_PREPARE_DATA,
				'dateadded' => gmdate('Y-m-d H:i:s'),
				'additionaldata' => serialize(array(
					'SKU' => magnaPID2SKU($productId)
				))
			)
		);
		$this->badItems[] = $productId;
		unset($this->selection[$productId]);
	}

	protected function getProduct($pID) {
		if (!$this->settings['mlProductsUseLegacy']) {
			MLProduct::gi()
				->setPriceConfig(AmazonHelper::loadPriceSettings($this->mpID))
				->setQuantityConfig(AmazonHelper::loadQuantitySettings($this->mpID))
				->setOptions(array('sameVariationsToAttributes' => true))
			;
		}
		return parent::getProduct($pID);
	}
	
	protected function appendMatchingData($pID, $product, &$data) {
		$productMatching = MagnaDB::gi()->fetchRow("
			SELECT * 
			FROM `".TABLE_MAGNA_AMAZON_PROPERTIES."`
			WHERE 
				asin<>'' 
				AND ".(
					($this->settings['keytype'] == 'artNr')
			            ? 'products_model="'.MagnaDB::gi()->escape($product['ProductsModel']).'"'
			            : 'products_id="'.$pID.'"'
			    )."
				AND mpID='".$this->_magnasession['mpID']."'
			LIMIT 1
		");
		if ($productMatching === false) {
			return false;
		}
        # if shipping templates active use selected, else default template

        if (isset($productMatching['ShippingTemplate'])) {
            $defaultTemplateIndex = $productMatching['ShippingTemplate'];
        } else {
            $aDefaultTemplate = getDBConfigValue(array('amazon.shipping.template', 'defaults'), $this->mpID);
            $defaultTemplateIndex = array_search('1', $aDefaultTemplate);
        }


		$data['submit']['ASIN'] = $productMatching['asin'];
		$data['submit']['ConditionType'] = empty($productMatching['item_condition']) ? $data['submit']['ConditionType'] : $productMatching['item_condition'];
		$data['submit']['ConditionNote'] = sanitizeProductDescription($productMatching['item_note']);
        $data['submit']['ShippingTemplate'] = isset($defaultTemplateIndex)?:"";
		if ($productMatching['leadtimeToShip'] > 0) {
			$data['submit']['LeadtimeToShip'] = $productMatching['leadtimeToShip'];
		}
		$tax = $product['TaxPercent'];
		if(($tax - (int)$tax) > 0) {
			$decimalPlaces = 2;
		} else {
			$decimalPlaces = 0;
		}
		$taxStr = number_format($tax, $decimalPlaces, '.', '');
		$finalMWSTstr = str_replace('##', $taxStr, getDBConfigValue('amazon.mwstplaceholder', $this->_magnasession['mpID']));
		
		$data['submit']['ConditionNote'] = trim(substr(
			$data['submit']['ConditionNote'], 0, 
			(2000 - strlen($finalMWSTstr) - 1)
		).' '.$finalMWSTstr);
		$productVariations = isset($product['Variations']) && is_array($product['Variations'])? $product['Variations'] : array();
		$preparedVariations = array();

        // if the reduced price is available here it has been enabled in the module configuration and should be used.
        if (isset($product['PriceReduced'])) {
            $data['submit']['Price'] = $product['PriceReduced'];
        }

		foreach ($productVariations as $variation) {
			$variationProduct = array(
				'ProductsModel' => $variation['MarketplaceSku'], 
				'TaxPercent' => $product['TaxPercent']
			);
			$variationData = $data;
			$variationData['submit']['SKU'] = ($this->settings['keytype'] == 'artNr')
				? $variation['MarketplaceSku']
				: $variation['MarketplaceId'];
			$variationData['submit']['Quantity'] = $variation['Quantity'];
			$variationData['submit']['Price'] = $variation['Price'];
			if ($this->appendMatchingData($variation['VariationId'], $variationProduct, $variationData)) {
				unset($variationData['submit']['Variations']);
				$preparedVariations[] = $variationData['submit'];
			}
		}
		$data['submit']['Variations'] = empty($preparedVariations) ? array() : $preparedVariations;
		return true;
	}
	
	protected function appendApplyData($pID, $product, &$data) {
		$productApply = MagnaDB::gi()->fetchRow("
			SELECT data, leadtimeToShip, ShippingTemplate, variation_theme
			  FROM `".TABLE_MAGNA_AMAZON_APPLY."`
			 WHERE data<>''
			       AND ".(($this->settings['keytype'] == 'artNr')
			            ? 'products_model="'.MagnaDB::gi()->escape($product['ProductsModel']).'"'
			            : 'products_id="'.$pID.'"'
			       )."
			       AND is_incomplete='false'
			       AND mpID='".$this->_magnasession['mpID']."'
			 LIMIT 1
		");
		if ($productApply === false) {
			return false;
		}
		$productApply['data'] = @unserialize(@base64_decode($productApply['data']));
		if (empty($productApply['data']) || !is_array($productApply['data'])) {
			$productApply['data'] = array();
		}
		$productApply['category'] = @unserialize(@base64_decode($productApply['category']));
		if (empty($productApply['category']) || !is_array($productApply['category'])) {
			$productApply['category'] = array();
		}
		
		$productApply['data'] = array_merge($productApply['category'], $productApply['data']);
		if (empty($productApply['data'])) {
			return false;
		}
        if (array_key_exists('Keywords', $productApply['data']) && $productApply['data']['Keywords'] === null) {
            $productApply['data']['Keywords'] = $product['Keywords'];
        }

		$categoryAttributes = (!empty($productApply['data']['ShopVariation'])) ? $this->fixCategoryAttributes(json_decode($productApply['data']['ShopVariation'], true), $product) : '';

		// ConditionType should go as regular data attribute, not product attribute
		if (isset($categoryAttributes['ConditionType'])) {
			$productApply['ConditionType'] = $categoryAttributes['ConditionType'];
			unset($categoryAttributes['ConditionType']);
		}

		// ConditionNote should go as regular data attribute, not product attribute
		if (isset($categoryAttributes['ConditionNote'])) {
			$productApply['ConditionNote'] = $categoryAttributes['ConditionNote'];
			unset($categoryAttributes['ConditionNote']);
		}

		if (!empty($categoryAttributes)) {
			$data['submit']['Attributes'] = $categoryAttributes;
			unset($productApply['data']['Attributes']);
		}

		$data['submit'] = array_merge($data['submit'], $productApply['data']);

		//EAN for USA is UPC
		if (getDBConfigValue('amazon.site', $this->mpID) === 'US' && isset($categoryAttributes['UPC'])) {
			$data['submit']['EAN'] = $categoryAttributes['UPC'];
			unset($data['submit']['Attributes']['UPC']);
		}

		$data['submit']['SKU'] = ($this->settings['keytype'] == 'artNr')
			? $product['MarketplaceSku']
			: $product['MarketplaceId'];

		$data['submit']['ConditionType'] = empty($productApply['ConditionType']) ? $data['submit']['ConditionType'] : $productApply['ConditionType'];
		$data['submit']['ConditionNote'] = empty($productApply['ConditionNote']) ? sanitizeProductDescription($data['submit']['ConditionNote']) : sanitizeProductDescription($productApply['ConditionNote']);

		if (!empty($data['submit']['BrowseNodes'])) {
			foreach ($data['submit']['BrowseNodes'] as $i => $bn) {
				if ($bn == 'null') {
					unset($data['submit']['BrowseNodes'][$i]);
				} elseif (preg_match("/([0-9]*)_?/", $bn, $aOutput)) {
					$data['submit']['BrowseNodes'][$i] = $aOutput[1];
				}
			}
		}
		
		if (!empty($product['Attributes'])) {
			$data['submit']['CustomAttributes'] = array();
			foreach ($product['Attributes'] as $attribSet) {
				// need to convert field name to utf8 for json if its not utf8 json_encode will set it to null
				$data['submit']['CustomAttributes'][stringToUTF8($attribSet['Name'])] = $attribSet['Value'];
			}
		}
		
		$imagePath = getDBConfigValue('amazon.imagepath', $this->_magnasession['mpID'], SHOP_URL_POPUP_IMAGES);
		$imagePath = trim($imagePath, '/ ').'/';
		$images = array();
		if (!empty($data['submit']['Images'])) {
			foreach ($data['submit']['Images'] as $image => $use) {
				if ($use == 'true') {
					$images[] = $imagePath.$image;
				}
			}
			$data['submit']['Images'] = $images;
		}

		if ($productApply['leadtimeToShip'] > 0) {
			$data['submit']['LeadtimeToShip'] = $productApply['leadtimeToShip'];
		}
			
		if (isset($product['Weight']) && is_array($product['Weight'])) {
			$data['submit']['Weight'] = $product['Weight'];
		}

		// B2B
		// if B2B is globally disabled, ignore prepared values
		if (getDBConfigValue('amazon.b2b.active', $this->mpID, 'false') !== 'true') {
			$productApply['data']['B2BActive'] = 'false';
			$data['submit']['B2BActive'] = 'false';
		}

		$b2bActive = $this->getB2BValue($productApply['data'], 'B2BActive', 'active', 'false') === 'true';
		$b2bOnly = $this->getB2BValue($productApply['data'], 'B2BSellTo', 'sell_to', 'b2b_b2c') === 'b2b_only';

		if ($b2bActive) {
			$this->setB2BData($data, $pID, $product, $b2bOnly);
		} else {
			$this->unsetB2BData($data['submit']);
		}

		$data['submit']['Variations'] = (isset($product['Variations']) && is_array($product['Variations'])) ? $product['Variations'] : array();
		$preparedAttributes = $this->getPreparedAttributes($productApply);
		foreach ($data['submit']['Variations'] as &$vItem) {
			$vItem['SKU'] = ($this->settings['keytype'] == 'artNr')
				? $vItem['MarketplaceSku']
				: $vItem['MarketplaceId'];

			if ($productApply['leadtimeToShip'] > 0) {
				$vItem['LeadtimeToShip'] = $productApply['leadtimeToShip'];
			}
			if (
				(!isset($vItem['ManufacturerPartNumber']) || empty($vItem['ManufacturerPartNumber']))
				&& $this->settings['skuAsMfrPartNo']
			) {
				$vItem['ManufacturerPartNumber'] = $vItem['SKU'];
			}

			$vItem['Attributes'] = $this->fixVariationCategoryAttributes($preparedAttributes, $product, $vItem);

			if (isset($vItem['Attributes']['ConditionType'])) {
				unset($vItem['Attributes']['ConditionType']);
			}

			if (isset($vItem['Attributes']['ConditionNote'])) {
				unset($vItem['Attributes']['ConditionNote']);
			}

			if (getDBConfigValue('amazon.site', $this->mpID) === 'US' && isset($vItem['Attributes']['UPC'])) {
				$vItem['EAN'] = $vItem['Attributes']['UPC'];
				unset($vItem['Attributes']['UPC']);
			}

			if (isset($vItem['Images']) && !empty($vItem['Images'])) {
				foreach ($vItem['Images'] as $imgKey => $imgVal) {
					$vItem['Images'][$imgKey] = $imagePath.$imgVal;
				}
			} else {
				unset($vItem['Images']);
			}

			// if the reduced price is available here it has been enabled in the module configuration and should be used.
			if (isset($vItem['PriceReduced'])) {
				$vItem['Price'] = $vItem['PriceReduced'];
			}

			// B2B
			if ($b2bActive) {
				$this->setB2BVariationData($vItem, $data, $b2bOnly);
			} else {
				$this->unsetB2BData($vItem);
			}
		}

		if (
			(!isset($data['submit']['ManufacturerPartNumber']) || empty($data['submit']['ManufacturerPartNumber']))
			&& $this->settings['skuAsMfrPartNo']
		) {
			$data['submit']['ManufacturerPartNumber'] = $data['submit']['SKU'];
		}

		unset($data['submit']['ShopVariation']);

        if (isset($productApply['variation_theme'])) {
            $data['submit']['variation_theme'] = json_decode($productApply['variation_theme'], true);
            unset($data['submit']['variationTheme']);
        }


        if (!isset($data['submit']['Attributes'])) {
            $data['submit']['Attributes'] = array();
        }
        $aTemplates = getDBConfigValue(array('amazon.shipping.template', 'values'), $this->mpID);
        if (isset($productApply['ShippingTemplate'])) {
            $defaultTemplateIndex = $productApply['ShippingTemplate'];
        } else {
            $aDefaultTemplate = getDBConfigValue(array('amazon.shipping.template', 'defaults'), $this->mpID);
            $defaultTemplateIndex = array_search('1', $aDefaultTemplate);
        }
        if (isset($aTemplates[$defaultTemplateIndex])) {
            $data['submit']['Attributes']['MerchantShippingGroupName'] = $aTemplates[$defaultTemplateIndex];
            // Set MerchantShippingGroup also for Variations!
            foreach ($data['submit']['Variations'] as &$varItem) {
                $varItem['Attributes']['MerchantShippingGroupName'] = $aTemplates[$defaultTemplateIndex];
            }
        }

		return true;
	}

	private function setB2BData(&$data, $pID, $product, $b2bOnly) {
		$quantityDiscountType = $this->getB2BValue($data['submit'], 'QuantityPriceType', 'discount_type', '');
		$data['submit']['QuantityPriceType'] = $quantityDiscountType;
		$useTiers = $quantityDiscountType !== '';
		for ($i = 1; $i < 6; $i++) {
			$data['submit']['QuantityLowerBound'.$i] = $useTiers ?
				$this->getB2BValue($data['submit'], 'QuantityLowerBound'.$i, "discount_tier$i.quantity", '0') : '';
			$data['submit']['QuantityPrice'.$i] = $useTiers ?
				$this->getB2BValue($data['submit'], 'QuantityPrice'.$i, "discount_tier$i.discount", '0') : '';
		}

		$this->setB2BPrice($pID, $data, $product, $b2bOnly);
	}

	private function setB2BVariationData(&$vItem, $data, $b2bOnly)
	{
		$vItem['BusinessPrice'] = $this->simpleprice->setFinalPriceFromDB($vItem['VariationId'], $this->mpID, 'b2b.')
			->getPrice();

		$vItem['ProductTaxCode'] = $data['submit']['ProductTaxCode'];
		$vItem['QuantityPriceType'] = $data['submit']['QuantityPriceType'];
		for ($i = 1; $i < 6; $i++) {
			$vItem['QuantityLowerBound'.$i] = $data['submit']['QuantityLowerBound'.$i];
			$vItem['QuantityPrice'.$i] = $data['submit']['QuantityPrice'.$i];
		}

		if ($b2bOnly) {
			unset($vItem['Price']);
		}
	}

	private function unsetB2BData(&$data) {
		unset($data['B2BSellTo']);
		unset($data['QuantityPriceType']);
		for ($i = 1; $i < 6; $i++) {
			unset($data['QuantityLowerBound'.$i]);
			unset($data['QuantityPrice'.$i]);
		}
	}

	private function getB2BValue($data, $key, $configKey, $default)
	{
		if (isset($data[$key])) {
			return $data[$key];
		}

		// for backward compatibility, there might be items prepared before B2B so we need values from config
		return getDBConfigValue('amazon.b2b.'.$configKey, $this->mpID, $default);
	}

	protected function setB2BPrice($pID, &$data, $product, $b2bOnly) {
		$this->simpleprice->setCurrency(getCurrencyFromMarketplace($this->mpID));
		// calculate business price
		$businessPrice = $this->simpleprice->setFinalPriceFromDB($pID, $this->mpID, 'b2b.')->getPrice();
		$data['submit']['BusinessPrice'] = $businessPrice;
		if ($b2bOnly) {
			unset($data['submit']['Price']);
		}

		$taxMatch = getDBConfigValue('amazon.b2b.tax_code_specific', $this->mpID, array());
		if (isset($taxMatch[$data['submit']['MainCategory']])) {
			$taxMatch = $taxMatch[$data['submit']['MainCategory']];
		} else {
			$taxMatch = getDBConfigValue('amazon.b2b.tax_code', $this->mpID, array());
		}

		if (is_array($taxMatch) && array_key_exists($product['TaxClass'], $taxMatch)) {
			$data['submit']['ProductTaxCode'] = $taxMatch[$product['TaxClass']];
		} else {
			$data['submit']['ProductTaxCode'] = '';
		}

		unset($data['submit']['B2BSellTo']);
		unset($data['submit']['B2BActive']);
	}

	private function getPreparedAttributes($product) {
        $preparedAttributes = array();
        if (empty($product['data']['ShopVariation'])) {
            // product has been prepared before A-M is applied, so attributes are in Attributes array
            if (!empty($product['data']['Attributes'])) {
                $oldAttributes = $product['data']['Attributes'];
                foreach($oldAttributes as $attributeKey => $attributeValue) {
                    $preparedAttributes[$attributeKey] = array(
                        'Code' => 'attribute_value',
                        'Values' => $attributeValue,
                    );
                }
            }
        } else {
            $preparedAttributes = json_decode($product['data']['ShopVariation'], true);
        }

        return $preparedAttributes;
    }

	protected function appendAdditionalData($pID, $product, &$data) {
		if ($this->settings['mlProductsUseLegacy']) {
			$this->appendAdditionalDataOld($pID, $product, $data);
			return;
		}

		if ($data['quantity'] < 0) {
			$data['quantity'] = 0;
		}
		
		unset($data['submit']['optional_selector']);
		$data['submit']['Quantity'] = $data['quantity'];
		$data['submit']['SKU'] = magnaPID2SKU($pID);

		if (!empty($data['price']) && $data['price'] != 0) {
			$data['submit']['Price'] = $data['price'];
		} elseif (isset($product['PriceReduced'])) {
			// if the reduced price is available here it has been enabled in the module configuration and should be used.
			$data['submit']['Price'] = $product['PriceReduced'];
		}
		
		#VPE
		if ((isset($product['BasePrice']['Value'])) && ($product['BasePrice']['Value'] > 0)) {
			$data['submit']['BasePrice'] = $product['BasePrice'];
		}
		
		$data['submit']['ConditionType'] = getDBConfigValue('amazon.itemCondition', $this->_magnasession['mpID']);

		if (false === $this->appendMatchingData($pID, $product, $data)) {
			if (false === $this->appendApplyData($pID, $product, $data)) {
				$data['submit'] = array();
				$this->markAsFailed($pID);
			}
		}
	}
	
	protected function appendAdditionalDataOld($pID, $product, &$data) {
		#echo print_m(func_get_args(), __METHOD__);
		$conditionType = getDBConfigValue('amazon.itemCondition', $this->_magnasession['mpID']);
		
		$productMatching = $productApply = false;
		
		if ($data['quantity'] < 0) {
			$data['quantity'] = 0;
		}

		if (($productMatching = MagnaDB::gi()->fetchRow('
			SELECT * FROM `'.TABLE_MAGNA_AMAZON_PROPERTIES.'`
			 WHERE asin<>"" AND 
			      '.(($this->settings['keytype'] == 'artNr')
			            ? 'products_model="'.MagnaDB::gi()->escape($product['products_model']).'"'
			            : 'products_id="'.$pID.'"'
			        ).' AND
			       mpID="'.$this->_magnasession['mpID'].'"
			 LIMIT 1
		')) !== false) {
			$data['submit']['SKU'] = magnaPID2SKU($pID);
			$data['submit']['ASIN'] = $productMatching['asin'];
			$data['submit']['ConditionType'] = empty($productMatching['item_condition']) ? $conditionType : $productMatching['item_condition'];
			$data['submit']['Price'] = $data['price'];
			$data['submit']['Quantity'] = $data['quantity'];
			$data['submit']['WillShipInternationally'] = $productMatching['will_ship_internationally'];
			$data['submit']['ConditionNote'] = sanitizeProductDescription($productMatching['item_note']);
			if ($productMatching['leadtimeToShip'] > 0) {
				$data['submit']['LeadtimeToShip'] = $productMatching['leadtimeToShip'];
			}

		} else if (($productApply = MagnaDB::gi()->fetchRow('
			SELECT data, leadtimeToShip
			 FROM `'.TABLE_MAGNA_AMAZON_APPLY.'`
			 WHERE data<>""
			       AND '.(($this->settings['keytype'] == 'artNr')
			            ? 'products_model="'.MagnaDB::gi()->escape($product['products_model']).'"'
			            : 'products_id="'.$pID.'"'
			       ).'
			       AND is_incomplete="false"
			       AND mpID="'.$this->_magnasession['mpID'].'"
			 LIMIT 1
		')) !== false) {
			$productApply['data'] = @unserialize(@base64_decode($productApply['data']));
			if (empty($productApply['data']) || !is_array($productApply['data'])) {
				$productApply['data'] = array();
			}
			$productApply['category'] = @unserialize(@base64_decode($productApply['category']));
			if (empty($productApply['category']) || !is_array($productApply['category'])) {
				$productApply['category'] = array();
			}
			
			$productApply['data'] = array_merge($productApply['category'], $productApply['data']);
			
			if (empty($productApply['data'])) {
				$this->markAsFailed($pID);
				return;
			}

			##########################################################################################
			#echo print_m($productApply['data']); die();
			if ($productApply['Attributes']['Color'] == 'Keine Auswahl') $productApply['Attributes']['Color'] = '';
			if ($productApply['Attributes']['BB_Material'] == 'Keine Auswahl') $productApply['Attributes']['BB_Material'] = '';
			###########################################################################################

			$data['submit'] = array_merge(
				array(
					'SKU' => magnaPID2SKU($pID),
					'Price' => $data['price'],
					'Quantity' => $data['quantity'],
					'ConditionType' => $conditionType,
				),
				$productApply['data']
			);
			if (!empty($data['submit']['BrowseNodes'])) {
				foreach ($data['submit']['BrowseNodes'] as $i => $bn) {
					if ($bn == 'null') {
						unset($data['submit']['BrowseNodes'][$i]);
					}
				}
			}
			$imagePath = getDBConfigValue('amazon.imagepath', $this->_magnasession['mpID'], SHOP_URL_POPUP_IMAGES);
			$imagePath = trim($imagePath, '/ ').'/';
			$images = array();
			if (!empty($data['submit']['Images'])) {
				foreach ($data['submit']['Images'] as $image => $use) {
					if ($use == 'true') {
						$images[] = $imagePath.$image;
					}
				}
				$data['submit']['Images'] = $images;
			}

			if ($productApply['leadtimeToShip'] > 0) {
				$data['submit']['LeadtimeToShip'] = $productApply['leadtimeToShip'];
			}
			
			if ((float)$product['products_weight'] > 0) {
				$data['submit']['Weight'] = array (
					'Unit' => 'kg',
					'Value' => $product['products_weight'],
				);
			}
		} else {
			/**
			 * @todo check if ((masterArticle && haveVariants)||normalArticle)
			 */
			$this->markAsFailed($pID);
			return;
		}
		
		#VPE
		if ((isset($product['products_vpe_name'])) && ($product['products_vpe_value'] > 0)) {
			$data['submit']['BasePrice'] = array (
				'Unit'  => htmlspecialchars(trim($product['products_vpe_name'])),
				'Value' => $product['products_vpe_value'],
			);
		}

		if ($productApply === false) {
			return;
		}
		
		$tax = $this->simpleprice->getTaxByClassID($product['products_tax_class_id']);

		if(($tax - (int)$tax) > 0) {
			$decimalPlaces = 2;
		} else {
			$decimalPlaces = 0;
		}
		$taxStr = number_format($tax, $decimalPlaces, '.', '');
		$finalMWSTstr = str_replace('##', $taxStr, getDBConfigValue('amazon.mwstplaceholder', $this->_magnasession['mpID']));
		
		$data['submit']['ConditionNote'] = trim(substr(
				$data['submit']['ConditionNote'], 0, 
				(2000 - strlen($finalMWSTstr) - 1)
			).' '.$finalMWSTstr);

		if ($productApply !== false) {
			$variationTheme = array();
			// No variations support right now.
			if (false && defined('MAGNA_FIELD_ATTRIBUTES_EAN') 
				&& MagnaDB::gi()->columnExistsInTable('attributes_stock', TABLE_PRODUCTS_ATTRIBUTES)
			) {
				$variationTheme = MagnaDB::gi()->fetchArray('
				    SELECT po.products_options_name AS VariationTitle,
				           pov.products_options_values_name AS VariationValue,
				           pa.products_attributes_id AS aID,
				           pa.options_values_price AS aPrice,
				           pa.price_prefix AS aPricePrefix,
				           pa.attributes_stock AS Quantity,
				           '.MAGNA_FIELD_ATTRIBUTES_EAN.' AS EAN
				      FROM '.TABLE_PRODUCTS_ATTRIBUTES.' pa,
				           '.TABLE_PRODUCTS_OPTIONS.' po, 
				           '.TABLE_PRODUCTS_OPTIONS_VALUES.' pov
				     WHERE pa.products_id = "'.$pID.'"
				           AND po.language_code = "'.$this->settings['languagecode'].'"
				           AND po.products_options_id = pa.options_id
				           AND po.products_options_name<>""
				           AND pov.language_code = l.languages_code
				           AND pov.products_options_values_id = pa.options_values_id
				           AND pov.products_options_values_name<>""
				           AND pa.attributes_stock IS NOT NULL
				');
				arrayEntitiesToUTF8($variationTheme);
				#print_r($variationTheme);
				$quantityType = getDBConfigValue(
					$this->_magnasession['currentPlatform'].'.quantity.type',
					$this->_magnasession['mpID']
				);
				$quantityValue = getDBConfigValue(
					$this->_magnasession['currentPlatform'].'.quantity.value',
					$this->_magnasession['mpID'],
					0
				);
			}
	
			if (!empty($variationTheme)) {
				foreach ($variationTheme as &$item) {
					$item['SKU'] = magnaAID2SKU($item['aID']);
					unset($item['aID']);
					switch ($quantityType) {
						case 'stock': {
							# Already set.
							break;
						}
						case 'stocksub': {
							$item['Quantity'] = (int)$item['Quantity'] - $quantityValue;
							break;
						}
						default: {
							$item['Quantity'] = $quantityValue;
						}
					}
					if ($item['Quantity'] < 0) {
						$item['Quantity'] = 0;
					}
					$this->simpleprice->setPrice($data['price'])->removeTax($tax);

					if ($item['aPricePrefix'] == '+') {
						$this->simpleprice->addLump($item['aPrice']);
					} else {
						$this->simpleprice->subLump($item['aPrice']);
					}

					$this->simpleprice->addTax($tax);
					if (getDBConfigValue(
							$this->_magnasession['currentPlatform'].'.price.addkind',
							$this->_magnasession['mpID']
						) == 'percent'
					) {
						$this->simpleprice->addTax((float)getDBConfigValue(
							$this->_magnasession['currentPlatform'].'.price.factor',
							$this->_magnasession['mpID']
						));
					} else if (getDBConfigValue(
							$this->_magnasession['currentPlatform'].'.price.addkind',
							$this->_magnasession['mpID']
						) == 'addition'
					) {
						$this->simpleprice->addLump((float)getDBConfigValue(
							$this->_magnasession['currentPlatform'].'.price.factor',
							$this->_magnasession['mpID']
						));
					}

					$item['Price'] = $this->simpleprice->roundPrice()->makeSignalPrice(
							getDBConfigValue($this->_magnasession['currentPlatform'].'.price.signal', $this->_magnasession['mpID'], '')
					    )->getPrice();
					unset($item['aPrice']);
					unset($item['aPricePrefix']);
				}
			}
			$data['submit']['Variations'] = $variationTheme;
			#echo print_m($variationTheme);
		}
	}

	protected function processSubmitResult($result) { }

	protected function filterSelection() {
		#echo print_m($this->selection, __METHOD__.'{L:'.__LINE__.'}');
		/*
		foreach ($this->selection as $pID => &$data) {
			if ((int)$data['submit']['Quantity'] == 0) {
				unset($this->selection[$pID]);
				$this->disabledItems[] = $pID;
			}
		}
		*/
	}

	protected function postSubmit() {
		try {
			//*
			$result = MagnaConnector::gi()->submitRequest(array(
				'ACTION' => 'UploadItems',
			));
			//*/
		} catch (MagnaException $e) {
			$this->submitSession['api']['exception'] = $e;
			$this->submitSession['api']['html'] = MagnaError::gi()->exceptionsToHTML();
		}
	}

	protected function generateRedirectURL($state) {
		return toURL(array(
			'mp' => $this->realUrl['mp'],
			'mode' => 'listings',
		), true);
	}

	private function fixCategoryAttributes($aCatAttributes, $product) {
		$fixCatAttributes = array();
		if (isset($aCatAttributes) && is_array($aCatAttributes)) {
			foreach ($aCatAttributes as $key => &$aCatAttribute) {
				$sCode = $aCatAttribute['Code'];
				switch ($sCode) {
					case 'freetext':
					case 'attribute_value': {
						if (isset($aCatAttribute['Values']) && !empty($aCatAttribute['Values'])) {
							$fixCatAttributes[$key] = $aCatAttribute['Values'];
						}
						break;
					}
					case 'database_value': {
						if (isset($aCatAttribute['Values']) && !empty($aCatAttribute['Values'])) {
							$databaseValue = $this->runDbMatching(array(
								'Table' => array(
									'table' => $aCatAttribute['Values']['Table'],
									'column' => $aCatAttribute['Values']['Column'],
								),
								'Alias' => $aCatAttribute['Values']['Alias']
							), 'products_id', $product['ProductId']);

							if ($databaseValue != '') {
								$fixCatAttributes[$key] = $databaseValue;
							}
						}
						break;
					}
					case 'category': {
						if (isset($aCatAttribute['Values']) && !empty($aCatAttribute['Values'])) {
							if (!empty($aCatAttribute['Values']['Value'])) {
								$fixCatAttributes[$key] = $this->getCategoryNameById($aCatAttribute['Values']['Value']);
							}
						}
						break;
					}
					case 'title': {
						if (isset($aCatAttribute['Values']) && !empty($aCatAttribute['Values'])) {
							$fixCatAttributes[$key] = $product['Title'];
						}
						break;
					}
					case 'description': {
						if (isset($aCatAttribute['Values']) && !empty($aCatAttribute['Values'])) {
							$fixCatAttributes[$key] = $product['Description'];
						}
						break;
					}
					case 'ean': {
						if (isset($aCatAttribute['Values']) && !empty($aCatAttribute['Values'])) {
							$fixCatAttributes[$key] = $product['EAN'];
						}

						break;
					}
					case 'weight': {
						if (isset($aCatAttribute['Values']) && !empty($aCatAttribute['Values'])) {
							$fixCatAttributes[$key] = $product['Weight']['Value'].$product['Weight']['Unit'];
						}
						break;
					}
					case 'contentvolume': {
						if (isset($aCatAttribute['Values']) && !empty($aCatAttribute['Values'])) {
							$fixCatAttributes[$key] = $product['BasePrice']['Value'].$product['BasePrice']['Unit'];
						}
						break;
					}
					default:
						break;
				}

				if (empty($fixCatAttributes[$key])) {
					unset($aCatAttributes[$key]);
				}

				if (!isset($fixCatAttributes[$key])) {
					continue;
				}

				if ($this->stringStartsWith($key, 'additional_attribute')) {
					$sNewKey = ucfirst($sCode);
					$fixCatAttributes[$sNewKey] = $fixCatAttributes[$key];
					unset($fixCatAttributes[$key]);
				}
			}
		}

		return $fixCatAttributes;
	}

	private function fixVariationCategoryAttributes($aCatAttributes, $product, &$variationDB) {
		$fixCatAttributes = array();
		if (isset($aCatAttributes) && is_array($aCatAttributes)) {
			foreach ($aCatAttributes as $key => &$aCatAttribute) {
				$sCode = $aCatAttribute['Code'];
				switch ($sCode) {
					case 'freetext':
					case 'attribute_value': {
						if (isset($aCatAttribute['Values']) && !empty($aCatAttribute['Values'])) {
							$fixCatAttributes[$key] = $aCatAttribute['Values'];
						}
						break;
					}
					case 'database_value': {
						if (isset($aCatAttribute['Values']) && !empty($aCatAttribute['Values'])) {
							$databaseValue = $this->runDbMatching(array(
								'Table' => array(
									'table' => $aCatAttribute['Values']['Table'],
									'column' => $aCatAttribute['Values']['Column'],
								),
								'Alias' => $aCatAttribute['Values']['Alias']
							), 'products_id', $variationDB['VariationId']);

							if ($databaseValue) {
								$fixCatAttributes[$key] = $databaseValue;
							}
						}
						break;
					}
					case 'category': {
						if (isset($aCatAttribute['Values']) && !empty($aCatAttribute['Values'])) {
							if (!empty($aCatAttribute['Values']['Value'])) {
								$fixCatAttributes[$key] = $this->getCategoryNameById($aCatAttribute['Values']['Value']);
							}
						}
						break;
					}
					case 'title': {
						if (isset($aCatAttribute['Values']) && !empty($aCatAttribute['Values'])) {
							$fixCatAttributes[$key] = $product['Title'];
						}
						break;
					}
					case 'description': {
						if (isset($aCatAttribute['Values']) && !empty($aCatAttribute['Values'])) {
							$fixCatAttributes[$key] = $product['Description'];
						}
						break;
					}
					case 'ean': {
						if (isset($aCatAttribute['Values']) && !empty($aCatAttribute['Values'])) {
							$fixCatAttributes[$key] = isset($variationDB['EAN']) ? $variationDB['EAN'] : $product['EAN'];
						}

						break;
					}
					case 'weight': {
						if (isset($aCatAttribute['Values']) && !empty($aCatAttribute['Values'])) {
							if (isset($variationDB['Weight']['Value'])) {
								$fixCatAttributes[$key] = $variationDB['Weight']['Value'].$variationDB['Weight']['Unit'];
							} else {
								$fixCatAttributes[$key] = $product['Weight']['Value'].$product['Weight']['Unit'];
							}
						}
						break;
					}
					case 'contentvolume': {
						if (isset($aCatAttribute['Values']) && !empty($aCatAttribute['Values'])) {
							if (isset($variationDB['BasePrice']['Value'])) {
								$fixCatAttributes[$key] = $variationDB['BasePrice']['Value'].$variationDB['BasePrice']['Unit'];
							} else {
								$fixCatAttributes[$key] = $product['BasePrice']['Value'].$product['BasePrice']['Unit'];
							}
						}
						break;
					}
					default:
						foreach ($variationDB['Variation'] as &$variationAttribute) {
							if ($sCode == $variationAttribute['NameId']) {
								foreach ($aCatAttribute['Values'] as $value) {
									if (fixHTMLUTF8Entities($variationAttribute['Value']) === fixHTMLUTF8Entities($value['Shop']['Value'])) {
										if ($value['Marketplace']['Key'] === 'manual') {
											$fixCatAttributes[$key] = $value['Marketplace']['Value'];
										} else {
											$fixCatAttributes[$key] = $value['Marketplace']['Key'];
										}

										$sCode = $variationAttribute['Name'];
										$variationAttribute['VariantSetFromAM'] = true;

										$variationDB['Variation'][] = array(
											'Name' => $key,
											'Value' => $fixCatAttributes[$key]
										);
									}
								}
							}
						}
				}

				if (empty($fixCatAttributes[$key])) {
					unset($fixCatAttributes[$key]);
				}

				if (!isset($fixCatAttributes[$key])) {
					continue;
				}

				if ($this->stringStartsWith($key, 'additional_attribute')) {
					$sNewKey = ucfirst($sCode);
					$fixCatAttributes[$sNewKey] = $fixCatAttributes[$key];
					unset($fixCatAttributes[$key]);
				}
			}

			// Remove all variation definitions that are already set from MP
			foreach ($variationDB['Variation'] as $variationAttributeKey => $variationAttributeValue) {
				if (isset($variationAttributeValue['VariantSetFromAM']) && $variationAttributeValue['VariantSetFromAM']) {
					unset($variationDB['Variation'][$variationAttributeKey]);
				}
			}
		}

		return $fixCatAttributes;
	}

	/**
	 * Helper method to execute a db matching query.
	 * @return mixed
	 *   A string or false if the matching config is empty.
	 */
	protected function runDbMatching($tableSettings, $defaultAlias, $where) {
		if (!isset($tableSettings['Table']['table'])
			|| empty($tableSettings['Table']['table'])
			|| empty($tableSettings['Table']['column'])
		) {
			return false;
		}
		if (empty($tableSettings['Alias'])) {
			$tableSettings['Alias'] = $defaultAlias;
		}

		if (!is_numeric($where)) {
			$where = '"'.MagnaDB::gi()->escape($where).'"';
		}

		return (string)MagnaDB::gi()->fetchOne('
			SELECT `' . $tableSettings['Table']['column'] . '`
			FROM `' . $tableSettings['Table']['table'] . '`
			WHERE `' . $tableSettings['Alias'] . '` = ' . $where . '
				AND `' . $tableSettings['Table']['column'] . '` <> \'\'
		');
	}

	private function stringStartsWith($haystack, $needle) {
		$length = strlen($needle);
		return (substr($haystack, 0, $length) === $needle);
	}

	private function getCategoryNameById($categoryID) {
		try {
			$aRequest = array(
				'ACTION' => 'GetCategoryDetails',
				'DATA' => array(
					'CategoryID' => $categoryID
				)
			);

			$aResponse = MagnaConnector::gi()->submitRequest($aRequest);
			if ($aResponse['STATUS'] == 'SUCCESS' && isset($aResponse['DATA']) && is_array($aResponse['DATA'])) {
				return $aResponse['DATA']['title_plural'];
			} else {
				return $categoryID;
			}

		} catch (MagnaException $e) {
			return $categoryID;
		}
	}
}
