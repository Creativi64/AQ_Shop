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
require_once(DIR_MAGNALISTER_MODULES . 'magnacompatible/checkin/MagnaCompatibleCheckinSubmit.php');
require_once(DIR_MAGNALISTER_MODULES . 'priceminister/PriceministerHelper.php');

/**
 * TODO: Siehe appendAdditionalData()
 */
class PriceministerCheckinSubmit extends MagnaCompatibleCheckinSubmit
{

    protected $useShippingtimeMatching = false;
    protected $defaultShippingtime = '';
    protected $shippingtimeMatching = array();

    public function __construct($settings = array())
    {
        global $_MagnaSession;
        $this->summaryAddText = "<br /><br />\n" . ML_PRICEMINISTER_UPLOAD_EXPLANATION;

        $settings = array_merge(array(
            'language' => getDBConfigValue($settings['marketplace'] . '.lang', $_MagnaSession['mpID'], ''),
            'currency' => getCurrencyFromMarketplace($_MagnaSession['mpID']),
            'keytype' => getDBConfigValue('general.keytype', '0'),
            'itemsPerBatch' => 100,
            'mlProductsUseLegacy' => false,
        ), $settings);

        parent::__construct($settings);
        $this->summaryAddText = "<br /><br />\n" . ML_PRICEMINISTER_UPLOAD_EXPLANATION;

        $this->settings['SyncInventory'] = array(
            'Price' => getDBConfigValue($settings['marketplace'] . '.inventorysync.price', $this->mpID, '') == 'auto',
            'Quantity' => getDBConfigValue($settings['marketplace'] . '.stocksync.tomarketplace', $this->mpID, '') == 'auto',
        );
    }

    public function init($mode, $items = -1)
    {
        parent::init($mode, $items);
    }

    protected function setUpMLProduct()
    {
        parent::setUpMLProduct();

        // Set Price and Quantity settings
        MLProduct::gi()->setPriceConfig(PriceministerHelper::loadPriceSettings($this->mpID));
        MLProduct::gi()->setQuantityConfig(PriceministerHelper::loadQuantitySettings($this->mpID));
        MLProduct::gi()->setOptions(array(
            'sameVariationsToAttributes' => false,
            'purgeVariations' => true
        ));
    }

    protected function appendAdditionalData($pID, $product, &$data)
    {
        $defaultTitle = isset($product['Title']) ? $product['Title'] : '';
        $defaultDescription = isset($product['Description']) ? $product['Description'] : '';

        $prepare = MagnaDB::gi()->fetchRow('
			SELECT * FROM ' . TABLE_MAGNA_PRICEMINISTER_PREPARE . '
			 WHERE ' . ((getDBConfigValue('general.keytype', '0') == 'artNr')
                ? 'products_model=\'' . MagnaDB::gi()->escape($product['ProductsModel']) . '\''
                : 'products_id=\'' . $pID . '\''
            ) . ' 
				   AND mpID = ' . $this->_magnasession['mpID'] . '
		');

        if (is_array($prepare)){
            $categoryAttributes = isset($prepare['CategoryAttributes']) ? $this->fixCategoryAttributes(json_decode($prepare['CategoryAttributes'], true), $product) : '';
            $categoryAttributes = $this->prepareCategoryAttributesForRequest($categoryAttributes, $prepare['TopMarketplaceCategory']);
            $data['submit']['SKU'] = magnaPID2SKU($pID);
            $data['submit']['ParentSKU'] = $prepare['PrepareType'] === 'Apply' ? magnaPID2SKU($pID) : $prepare['MPProductId'];
            $data['submit']['Ean'] = isset($prepare['EAN']) ? $prepare['EAN'] : $product['EAN'];
            $data['submit']['MarketplaceCategory'] = isset($prepare['MarketplaceCategories']) ? $prepare['MarketplaceCategories'] : '';
            $data['submit']['MarketplaceCategoryName'] = isset($prepare['MarketplaceCategoriesName']) ? $prepare['MarketplaceCategoriesName'] : $data['submit']['MarketplaceCategory'];
            $data['submit']['CategoryAttributes'] = $categoryAttributes;
            $data['submit']['ItemTitle'] = isset($prepare['Title']) ? PriceministerHelper::sanitizeTitle($prepare['Title'], PriceministerHelper::$TITLE_MAX_LENGTH) :
                PriceministerHelper::sanitizeTitle($defaultTitle, PriceministerHelper::$TITLE_MAX_LENGTH);
            $data['submit']['Description'] = isset($prepare['Description']) ? PriceministerHelper::truncateString($prepare['Description'], PriceministerHelper::$DESC_MAX_LENGTH) :
                PriceministerHelper::truncateString($defaultDescription, PriceministerHelper::$DESC_MAX_LENGTH);
            $data['submit']['CategoryId'] = isset($prepare['TopMarketplaceCategory']) ? $prepare['TopMarketplaceCategory'] : '';

            $imagePath = getDBConfigValue($this->marketplace . '.imagepath', $this->_magnasession['mpID'], SHOP_URL_POPUP_IMAGES);
            $imagePath = trim($imagePath, '/ ') . '/';
            if (empty($prepare['PictureUrl']) === false){
                $pictureUrls = json_decode($prepare['PictureUrl']);

                foreach ($pictureUrls as $image => $use){
                    if ($use == 'true'){
                        $data['submit']['Images'][] = array(
                            'URL' => $imagePath . $image
                        );
                    }
                }
            } else if (isset($product['Images'])){
                foreach ($product['Images'] as $image){
                    $data['submit']['Images'][] = array(
                        'URL' => $imagePath . $image
                    );
                }
            }

            $data['submit']['Condition'] = $prepare['ConditionType'];
            $data['submit']['Matched'] = $prepare['PrepareType'] === 'Apply' ? 'false' : 'true';
        } else{
            /* TODO: Shippingtime aus selection oder aus matching oder der generelle wert. */
            $data['submit']['ConditionType'] = getDBConfigValue($this->settings['marketplace'] . '.itemcondition', $this->_magnasession['mpID']);
        }

        $data['submit']['Price'] = $data['price'];
        $data['submit']['Quantity'] = $data['quantity'] < 0 ? 0 : $data['quantity'];

        if (!$this->getPriceministerVariations($product, $data, $imagePath, json_decode($prepare['CategoryAttributes'], true))){
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

    protected function filterItem($pID, $data)
    {
        return array();
    }

    protected function filterSelection()
    {
        $b = parent::filterSelection();

        $shitHappend = false;
        $missingFields = array();
        foreach ($this->selection as $pID => &$data){
            if ($data['submit']['Price'] <= 0){
                // Loesche das Feld, um eine Fehlermeldung zu erhalten
                unset($data['submit']['Price']);
            }

            $mfC = array();

            $this->requirementsMet($data['submit'], $this->initSession['RequiredFileds'], $mfC);
            $mfC = array_merge($mfC, $this->filterItem($pID, $data['submit']));

            if (!empty($mfC)){
                foreach ($mfC as $key => $field){
                    $mfC[$key] = $field;
                }
                $sku = magnaPID2SKU($pID);
                //echo print_m($mfC, $sku);
                //*
                MagnaDB::gi()->insert(
                    TABLE_MAGNA_COMPAT_ERRORLOG,
                    array(
                        'mpID' => $this->mpID,
                        'errormessage' => json_encode(array(
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

    protected function generateRedirectURL($state)
    {
        return toURL(array(
            'mp' => $this->realUrl['mp'],
            'mode' => ($state == 'fail') ? 'errorlog' : 'listings'
        ), true);
    }

    protected function generateRequestHeader()
    {
        return array(
            'ACTION' => 'AddItems',
            'MODE' => $this->submitSession['mode']
        );
    }

    protected function getPriceministerVariations($product, &$data, $imagePath, $categoryAttributes)
    {
        if ($this->checkinSettings['Variations'] != 'yes'){
            return true;
        }

        $variations = array();
        foreach ($product['Variations'] as $v){
            $this->simpleprice->setPrice($v['Price']['Price']);
            $price = $this->simpleprice->roundPrice()->makeSignalPrice(
                getDBConfigValue($this->marketplace . '.price.signal', $this->mpID, '')
            )->getPrice();

            $vi = array(
                'SKU' => ($this->settings['keytype'] == 'artNr') ? $v['MarketplaceSku'] : $v['MarketplaceId'],
                'Price' => $price,
                'Currency' => $this->settings['currency'],
                'Quantity' => ($this->quantityLumb === false)
                    ? max(0, $v['Quantity'] - (int)$this->quantitySub)
                    : $this->quantityLumb,
                'Ean' => $v['EAN']
            );

            $vi['ItemTitle'] = $data['submit']['ItemTitle'];
            $vi['VariantTitle'] = $vi['ItemTitle'];

            foreach ($v['Variation'] as $varAttribute){
                $vi['VariantTitle'] .= ' ' . $varAttribute['Name'] . ' - ' . $varAttribute['Value'];
            }

            if (empty($v['Images'])){
                $vi['Images'] = $data['submit']['Images'];
            } else{
                foreach ($v['Images'] as $image){
                    $vi['Images'][] = array(
                        'URL' => $imagePath . $image,
                        'id' => $image
                    );
                }
            }

            $vi['CategoryAttributes'] = $this->fixVariationCategoryAttributes($categoryAttributes, $product, $v, $vi);
            $vi['CategoryAttributes'] = $this->prepareCategoryAttributesForRequest($vi['CategoryAttributes'], $data['submit']['MarketplaceCategory']);
            $variations[] = $vi;
        }

        $data['submit']['Variations'] = $variations;
        return true;
    }

    private function fixCategoryAttributes($aCatAttributes, $product)
    {
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

    private function fixVariationCategoryAttributes($aCatAttributes, $product, $variationDB, $variation)
    {
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

    private function prepareCategoryAttributesForRequest($categoryAttributes, $categoryId)
    {
        $aCategory = $this->getCategoryDetailsById($categoryId);
        if (empty($aCategory) || empty($aCategory['attributes'])){
            return $categoryAttributes;
        }

        $aCatAttributes = array(
            'Product' => array(),
            'Advert' => array()
        );

        foreach ($categoryAttributes as $attributeName => $attributeValue){
            if ($aCategory['attributes'][$attributeName]['product']){
                $aCatAttributes['Product'][$attributeName] = $attributeValue;
            } else{
                $aCatAttributes['Advert'][$attributeName] = $attributeValue;
            }
        }

        return $aCatAttributes;
    }

    private function stringStartsWith($haystack, $needle)
    {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }

    private function getCategoryDetailsById($categoryID)
    {
        try{
            $aRequest = array(
                'ACTION' => 'GetCategoryDetails',
                'DATA' => array(
                    'CategoryID' => $categoryID
                )
            );

            $aResponse = MagnaConnector::gi()->submitRequest($aRequest);
            if ($aResponse['STATUS'] == 'SUCCESS' && isset($aResponse['DATA']) && is_array($aResponse['DATA'])){
                return $aResponse['DATA'];
            } else{
                return false;
            }

        } catch (MagnaException $e){
            return false;
        }
    }

    protected function processSubmitResult($result) {
        if (array_key_exists('ERRORS', $result)
            && is_array($result['ERRORS'])
            && !empty($result['ERRORS'])
        ) {
            foreach ($result['ERRORS'] as $err) {
                if (isset($err['ERRORDATA']['SKU'])) {
                    $SKU = $err['ERRORDATA']['SKU'];
                    foreach ($this->selection as $pID => &$data) {
                        if ($data['submit']['SKU'] === $SKU) {
                            $this->badItems[] = $pID;
                            unset($this->selection[$pID]);
                            break;
                        }
                    }
                }
            }
        }
    }
    
}
