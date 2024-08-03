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
 * (c) 2010 - 2019 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */

defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');
require_once(DIR_MAGNALISTER_MODULES.'magnacompatible/checkin/MagnaCompatibleCheckinSubmit.php');
require_once(DIR_MAGNALISTER_MODULES . 'hitmeister/HitmeisterHelper.php');

/**
 * TODO: Siehe appendAdditionalData()
 */
class HitmeisterCheckinSubmit extends MagnaCompatibleCheckinSubmit {

	protected $useShippingtimeMatching = false;
	protected $defaultHandlingtime = '';

	public function __construct($settings = array()) {
		global $_MagnaSession;

		$settings = array_merge(array(
			'language' => getDBConfigValue($settings['marketplace'] . '.lang', $_MagnaSession['mpID'], ''),
			'currency' => getCurrencyFromMarketplace($_MagnaSession['mpID']),
			'keytype' => getDBConfigValue('general.keytype', '0'),
			'itemsPerBatch' => 100,
			'mlProductsUseLegacy' => false,
		), $settings);

		parent::__construct($settings);
		$this->summaryAddText = "<br /><br />\n".ML_HITMEISTER_UPLOAD_EXPLANATION;

		$this->settings['SyncInventory'] = array(
			'Price' => getDBConfigValue($settings['marketplace'] . '.inventorysync.price', $this->mpID, '') == 'auto',
			'Quantity' => getDBConfigValue($settings['marketplace'] . '.stocksync.tomarketplace', $this->mpID, '') == 'auto',
		);
	}
	
	public function init($mode, $items = -1) {
		parent::init($mode, $items);
		$this->initSession['RequiredFields'] = array();
		try {
			$requiredFileds = MagnaConnector::gi()->submitRequest(array(
				'ACTION' => 'GetRequiredKeys',
			));
			if (!empty($requiredFileds['DATA'])) {
				foreach ($requiredFileds['DATA'] as $key) {
					$this->initSession['RequiredFields'][$key] = true;
				}
			}
		} catch (MagnaException $e) { }
		
		$this->defaultHandlingtime     = getDBConfigValue($this->marketplace.'.handlingtime', $this->mpID, 0); 
	}

	protected function setUpMLProduct()
	{
		parent::setUpMLProduct();

		// Set Price and Quantity settings
		MLProduct::gi()->setPriceConfig(HitmeisterHelper::loadPriceSettings($this->mpID));
		MLProduct::gi()->setQuantityConfig(HitmeisterHelper::loadQuantitySettings($this->mpID));
		MLProduct::gi()->setOptions(array(
			'sameVariationsToAttributes' => false,
			'purgeVariations' => true
		));
	}
	
	protected function appendAdditionalData($pID, $product, &$data) {
		$defaultLocation = getDBConfigValue($this->settings['marketplace'].'.itemcountry', $this->_magnasession['mpID']);
		$defaultTitle = isset($product['Title']) ? $product['Title'] : '';
		$defaultSubtitle = isset($product['ShortDescription']) ? $product['ShortDescription'] : '';
		$defaultDescription = isset($product['Description']) ? $product['Description'] : '';
		
		$prepare = MagnaDB::gi()->fetchRow('
			SELECT * FROM '.TABLE_MAGNA_HITMEISTER_PREPARE.'
			 WHERE '.((getDBConfigValue('general.keytype', '0') == 'artNr')
					     ? 'products_model=\''.MagnaDB::gi()->escape($product['ProductsModel']).'\''
					     : 'products_id=\''.$pID.'\''
					).' 
				   AND mpID = '.$this->_magnasession['mpID'].'
		');
		
		if (is_array($prepare)) {
			$categoryAttributes = isset($prepare['CategoryAttributes']) ? $this->fixCategoryAttributes(json_decode($prepare['CategoryAttributes'], true), $product) : '';
			$data['submit']['SKU'] = magnaPID2SKU($pID);
			$data['submit']['ParentSKU'] = magnaPID2SKU($pID);
			$data['submit']['EAN'] = isset($prepare['EAN']) ? $prepare['EAN'] : $product['EAN'];
			$data['submit']['MarketplaceCategory'] = isset($prepare['MarketplaceCategories']) ? $prepare['MarketplaceCategories'] : '';
			$data['submit']['MarketplaceCategoryName'] = isset($prepare['MarketplaceCategoriesName']) ? $prepare['MarketplaceCategoriesName'] : '';
			$data['submit']['CategoryAttributes'] = $categoryAttributes;
			$data['submit']['Title'] = isset($prepare['Title']) ? $prepare['Title'] : $defaultTitle;
			$data['submit']['Subtitle'] = isset($prepare['Subtitle']) ? $prepare['Subtitle'] : HitmeisterHelper::sanitizeDescription($defaultSubtitle);
			$data['submit']['Description'] = isset($prepare['Description']) ? $prepare['Description'] : $defaultDescription;
			
			$imagePath = getDBConfigValue($this->marketplace . '.imagepath', $this->_magnasession['mpID'], SHOP_URL_POPUP_IMAGES);
			$imagePath = trim($imagePath, '/ ').'/';
			if (empty($prepare['PictureUrl']) === false) {
				$pictureUrls = json_decode($prepare['PictureUrl']);

				foreach ($pictureUrls as $image => $use) {
					if ($use == 'true') {
						$data['submit']['Images'][] = array(
							'URL' => $imagePath . $image
						);
					}
				}
			} else if (isset($product['Images'])) {
				foreach($product['Images'] as $image) {
					$data['submit']['Images'][] = array(
							'URL' => $imagePath . $image
						);
				}
			}					
			
			$data['submit']['HandlingTime']     = $prepare['HandlingTime'];
			$data['submit']['ConditionType']    = $prepare['ConditionType'];
			if (isset($prepare['ShippingGroup']) && $prepare['ShippingGroup'] != 0) {
				$data['submit']['ShippingGroup'] = $prepare['ShippingGroup'];
			} else if (getDBConfigValue($this->marketplace.'.shippinggroup', $this->mpID, 0) != 0) {
				$data['submit']['ShippingGroup'] = getDBConfigValue($this->marketplace.'.shippinggroup', $this->mpID, 0);
			}
			$data['submit']['Location'] = isset($prepare['Location']) ? $prepare['Location'] : $defaultLocation;
			$data['submit']['Comment']  = isset($prepare['Comment']) ? $prepare['Comment'] : '';
			$data['submit']['Matched']  = $prepare['PrepareType'] === 'Match' ? true : false;
		} else {
			$data['submit']['HandlingTime']     = isset($data['handlingtime']) && !empty($data['handlingtime'])
				? $data['handlingtime']
				: $this->defaultHandlingtime;
			$data['submit']['ConditionType'] = getDBConfigValue($this->settings['marketplace'].'.itemcondition', $this->_magnasession['mpID']);
		}
		
		$data['submit']['Price'] = $data['price'];
		$data['submit']['Currency'] = $this->settings['currency'];
		$data['submit']['Quantity'] = $data['quantity'] < 0 ? 0 : $data['quantity'];

		$manufacturerName = $product['Manufacturer'];
		if (empty($manufacturerName)) {
			$manufacturerName = getDBConfigValue(
				$this->marketplace.'.checkin.manufacturerfallback',
				$this->mpID,
				''
			);
		}
		if (!empty($manufacturerName)) {
			$data['submit']['Manufacturer'] = $manufacturerName;
		}
		$mfrmd = getDBConfigValue($this->marketplace.'.checkin.manufacturerpartnumber.table', $this->mpID, false);
		if (is_array($mfrmd) && !empty($mfrmd['column']) && !empty($mfrmd['table'])) {
			$pIDAlias = getDBConfigValue($this->marketplace.'.checkin.manufacturerpartnumber.alias', $this->mpID);
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

		$data['submit']['ItemTax'] = $product['TaxPercent'];

		if (!$this->getHitmeisterVariations($product, $data, $imagePath, json_decode($prepare['CategoryAttributes'], true))) {
			return;
		}
	}

	protected function preSubmit(&$request) {
		$request['DATA'] = array();
		foreach ($this->selection as $iProductId => &$aProduct) {
			if (empty($aProduct['submit']['Variations'])) {
				$request['DATA'][] = $aProduct['submit'];
				continue;
			}

			foreach ($aProduct['submit']['Variations'] as $aVariation) {
				$aVariationData = $aProduct;
				unset($aVariationData['submit']['Variations']);
				foreach ($aVariation as $sParameter => $mParameterValue) {
					$aVariationData['submit'][$sParameter] = $mParameterValue;
				}

				$request['DATA'][] = $aVariationData['submit'];
			}
		}

		arrayEntitiesToUTF8($request['DATA']);
	}
	
	protected function filterItem($pID, $data) {
		return array();
	}
	
    protected function requirementsMet($product, $requirements, &$failed) {
        if (!is_array($product) || empty($product) || !is_array($requirements) || empty($requirements)) {
            $failed = true;
            return false;
        }
        $failed = array();
        foreach ($requirements as $req => $needed) {
            if (!$needed) continue;
            if (empty($product[$req]) && ($product[$req] !== '0')) {
                if (!empty($product['Variations'])) {
                    foreach ($product['Variations'] as $variation) {
                        if (empty($variation[$req]) && ($variation[$req] !== '0')) {
                            $failed[] = $req;
                        }
                    }
                } else {
                    $failed[] = $req;
                }
            }
        }
        return empty($failed);
    }

	protected function filterSelection() {
		$b = parent::filterSelection();

		$shitHappend = false;
		$missingFields = array();
		foreach ($this->selection as $pID => &$data) {
			if ($data['submit']['Price'] <= 0) {
				// Loesche das Feld, um eine Fehlermeldung zu erhalten
				unset($data['submit']['Price']);
			}
			
			$mfC = array();
			
			$this->requirementsMet($data['submit'], $this->initSession['RequiredFields'], $mfC);
			$mfC = array_merge($mfC, $this->filterItem($pID, $data['submit']));

			if (!empty($mfC)) {
				foreach ($mfC as $key => $field) {
					$mfC[$key] = $field;
				}
				$sku = magnaPID2SKU($pID);
				//echo print_m($mfC, $sku);
				//*
				MagnaDB::gi()->insert(
					TABLE_MAGNA_COMPAT_ERRORLOG,
					array (
						'mpID' => $this->mpID,
						'errormessage' => json_encode(array (
							'MissingFields' => $mfC
						)),
						'dateadded' => gmdate('Y-m-d H:i:s'),
						'additionaldata' => serialize(array(
							'SKU' => $sku
						))
					)
				);
				//*/
				$shitHappend = true;
				$this->badItems[] = $pID;
				unset($this->selection[$pID]);
			}
		}
		$this->badItems = array_unique($this->badItems);
		return $b || $shitHappend;
	}

	protected function generateRedirectURL($state) {
		return toURL(array(
			'mp' => $this->realUrl['mp'],
			'mode'   => ($state == 'fail') ? 'errorlog' : 'listings'
		), true);
	}

	protected function getHitmeisterVariations($product, &$data, $imagePath, $categoryAttributes) {
		if ($this->checkinSettings['Variations'] != 'yes') {
			return true;
		}

		$variations = array();
		foreach ($product['Variations'] as $v) {
			$this->simpleprice->setPrice($v['Price']['Price']);
			$price = $this->simpleprice->roundPrice()->makeSignalPrice(
				getDBConfigValue($this->marketplace.'.price.signal', $this->mpID, '')
			)->getPrice();

			$vi = array(
				'SKU' => ($this->settings['keytype'] == 'artNr') ? $v['MarketplaceSku'] : $v['MarketplaceId'],
				'Price' => $price,
				'Currency' => $this->settings['currency'],
				'Quantity' => ($this->quantityLumb === false)
					? max(0, $v['Quantity'] - (int)$this->quantitySub)
					: $this->quantityLumb,
				'EAN' => $v['EAN']
			);

//			$variation = MLProduct::gi()->getProductById($v['VariationId']);
//			$vi['Title'] = $variation['Title'];

			$vi['Title'] = $product['Title'];

			foreach ($v['Variation'] as $varAttribute) {
				$vi['Title'] .= ' ' . $varAttribute['Name'] . ' - ' . $varAttribute['Value'];
			}

			if (empty($product['ManufacturerPartNumber']) === false) {
				$vi['Mpn'] = $product['ManufacturerPartNumber'];
			}

			if (empty($v['Images'])) {
				$vi['Images'] = $data['submit']['Images'];
			} else {
				foreach ($v['Images'] as $image) {
					$vi['Images'][] = array(
						'URL' => $imagePath . $image,
						'id' => $image
					);
				}
			}

			$vi['CategoryAttributes'] = $this->fixVariationCategoryAttributes($categoryAttributes, $product, $v, $vi);

			$variations[] = $vi;
		}

		$data['submit']['Variations'] = $variations;
		return true;
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

	private function fixVariationCategoryAttributes($aCatAttributes, $product, $variationDB, $variation) {
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
						foreach ($variationDB['Variation'] as $variationAttribute) {
							if ($sCode == $variationAttribute['NameId']) {
								foreach ($aCatAttribute['Values'] as $value) {
									if ($variationAttribute['Value'] === $value['Shop']['Value']) {
										$fixCatAttributes[$key] = str_replace(array(ML_GENERAL_VARMATCH_MANUALY_MATCHED, ML_GENERAL_VARMATCH_AUTO_MATCHED, ML_GENERAL_VARMATCH_FREE_TEXT), '', $value['Marketplace']['Value']);
										$sCode = $variationAttribute['Name'];
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
		}

		return $fixCatAttributes;
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
