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

require_once(DIR_MAGNALISTER_INCLUDES.'lib/classes/ComparisonShopping/ComparisonShoppingCheckinSubmit.php');

class IdealoCheckinSubmit extends ComparisonShoppingCheckinSubmit {

	protected function setUpMLProduct()
	{
		parent::setUpMLProduct();
		MLProduct::gi()->setOptions(array(
			'sameVariationsToAttributes' => false,
			'purgeVariations' => true
		));
	}
		
	protected function appendAdditionalData($pID, $product, &$data) {
		parent::appendAdditionalData($pID, $product, $data);

		$aPropertiesRow = MagnaDB::gi()->fetchRow('
				SELECT * FROM ' . TABLE_MAGNA_IDEALO_PROPERTIES . '
				 WHERE ' . (getDBConfigValue('general.keytype', '0') == 'artNr'
				? 'products_model = "' . MagnaDB::gi()->escape($product['ProductsModel']) . '"'
				: 'products_id = "' . $pID . '"'
			) . '
					   AND mpID = ' . $this->_magnasession['mpID']
		);

		if (!empty($aPropertiesRow['Title'])) {
			$data['submit']['ItemTitle'] = $aPropertiesRow['Title'];
		}

		if (!empty($aPropertiesRow['Description'])) {
			$data['submit']['Description'] = $aPropertiesRow['Description'];
		}

		if (!empty($aPropertiesRow['PictureUrl'])) {
			$imagePath = getDBConfigValue($this->marketplace . '.imagepath', $this->_magnasession['mpID'], SHOP_URL_POPUP_IMAGES);
			$imagePath = empty($imagePath) ? SHOP_URL_POPUP_IMAGES : $imagePath;
			$imagePath = trim($imagePath, '/ ').'/';
			$data['submit']['Image'] = array();
			$pictureUrls = json_decode($aPropertiesRow['PictureUrl']);
			foreach ($pictureUrls as $image => $use) {
				if ($use == 'true') {
					$data['submit']['Image'][] = array(
						'URL' => $imagePath . $image
					);
				}
			}
		} else {
			$data['submit']['Image'][] = array(
				'URL' => $data['submit']['Image']
			);
		}

		if (!empty($aPropertiesRow['Checkout'])) {
			$checkout = json_decode($aPropertiesRow['Checkout'], true);
			$data['submit']['Checkout'] = $checkout['val'];
		} else {
			$data['submit']['Checkout'] = false;
		}

        // payment methods are independent of idealo checkout
        if (!empty($aPropertiesRow['PaymentMethod']) && $aPropertiesRow['PaymentMethod'] !== 'noselection') {
            $aPaymentMethods = json_decode($aPropertiesRow['PaymentMethod'], true);
            $data['submit']['PaymentMethod'] = is_array($aPaymentMethods) ? $aPaymentMethods : (array)$aPropertiesRow['PaymentMethod'];
        }

        if ($data['submit']['Checkout']) {
			if (!empty($aPropertiesRow['ShippingMethod']) && $aPropertiesRow['ShippingMethod'] !== 'noselection') {
				$data['submit']['ShippingMethod'] = $aPropertiesRow['ShippingMethod'];
			}

			if (!empty($aPropertiesRow['ShippingCountry'])) {
				$data['submit']['ShippingCountry'] = $aPropertiesRow['ShippingCountry'];
			}
		}

		if (!empty($aPropertiesRow['ShippingCostMethod'])) {
			if (is_numeric($aPropertiesRow['ShippingCost'])
				&& $aPropertiesRow['ShippingCostMethod'] === '__ml_lump') {
				$data['submit']['ShippingCost'] = $aPropertiesRow['ShippingCost'];
			} else if ($aPropertiesRow['ShippingCostMethod'] === '__ml_weight') {
				$data['submit']['ShippingCost'] = $data['submit']['ItemWeight'];
			}
		}
		
        $data['submit']['ShippingTime'] = $aPropertiesRow['DeliveryTimeSource'] === 'shop' ? $data['submit']['ShippingTime'] : $aPropertiesRow['DeliveryTime'];
        if (empty($data['submit']['ShippingTime'])) {
            $data['submit']['ShippingTime'] = getDBConfigValue($this->marketplace.'.deliverytime', $this->_magnasession['mpID'], '');
        }
		$data['submit']['Quantity'] = $product['Quantity'];
		$catname = $this->getcategoriesname($product['ProductId']);
		if (!empty($catname)) {
			$data['submit']['MerchantCategory'] = $catname;
		}

		if (!$this->getIdealoVariations($product, $data, $imagePath)) {
			return;
		}
		$format = $this->simpleprice->getFormatOptions();
        if ($data['submit']['Checkout']) {
            $data['submit']['FulFillmentType'] = $aPropertiesRow['FulFillmentType'];
            if ($data['submit']['FulFillmentType'] == 'Spedition') {
                $TwoManHandlingFee = priceToFloat($aPropertiesRow['TwoManHandlingFee'], $format);
                if (!empty($TwoManHandlingFee)) {
                    $data['submit']['TwoManHandlingFee'] = $TwoManHandlingFee;
                }
                $DisposalFee = priceToFloat($aPropertiesRow['DisposalFee'], $format);
                if (!empty($DisposalFee)) {
                    $data['submit']['DisposalFee'] = $DisposalFee;
                }
            }
        }

	}

	protected function getIdealoVariations($product, &$data, $imagePath) {
		$variations = array();
		foreach ($product['Variations'] as $v) {
			$vProduct = MLProduct::gi()->getProductById($v['VariationId']);
			$this->simpleprice->setPrice($v['Price']);
			$price = $this->simpleprice->roundPrice()->makeSignalPrice(
				getDBConfigValue($this->marketplace.'.price.signal', $this->mpID, '')
			)->getPrice();

			$vi = array(
				'SKU' => (getDBConfigValue('general.keytype', '0') == 'artNr') ? $v['MarketplaceSku'] : $v['MarketplaceId'],
				'Price' => $price,
				'Quantity' => ($this->quantityLumb === false)
					? max(0, $v['Quantity'] - (int)$this->quantitySub)
					: $this->quantityLumb,
				'EAN' => $v['EAN']
			);

			$vi['ItemWeight'] = $v['Weight'] ;
			$vi['ItemTitle'] = $data['submit']['ItemTitle'];
			foreach ($v['Variation'] as $varAttribute) {
				$vi['ItemTitle'] .= ', ' . $varAttribute['Name'] . ': ' . $varAttribute['Value'];
			}

			$vi['Description'] = sanitizeProductDescription($vProduct['Description']);
			$vi['ItemUrl'] = $this->getItemUrl($vProduct);

			if (    empty($v['Images'])
			     && !empty($vProduct['Images'])) {
				$v['Images'] = $vProduct['Images'];
			}

			if (empty($v['Images'])) {
				$vi['Image'] = $data['submit']['Images'];
			} else {
				foreach ($v['Images'] as $image) {
					$vi['Image'][] = array(
						'URL' => $imagePath . $image
					);
				}
			}

			if( isset( $v['BasePrice']) && empty( $v['BasePrice']) === false ){
				$vi['BasePrice']['Unit'] = $v['BasePrice']['Unit'];
				$vi['BasePrice']['Value'] = number_format((float)$v['BasePrice']['Value'], 2, '.','');
			}

			$variations[] = $vi;
		}

		if (!empty($variations)) {
			$data['submit']['Variations'] = $variations;
		}

		return true;
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
    
}
