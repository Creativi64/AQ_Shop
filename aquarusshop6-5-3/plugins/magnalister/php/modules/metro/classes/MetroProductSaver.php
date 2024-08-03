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
 * (c) 2010 - 2021 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */

require_once(DIR_MAGNALISTER_MODULES.'metro/prepare/MetroCategoryMatching.php');
require_once(DIR_MAGNALISTER_MODULES.'metro/MetroHelper.php');
defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');

class MetroProductSaver {
    const DEBUG = false;
    public $aErrors = array();

    protected $aMagnaSession = array();
    protected $sMarketplace = '';
    protected $sMpId = 0;

    protected $aConfig = array();

    public function __construct($magnaSession) {
        $this->aMagnaSession = &$magnaSession;
        $this->sMarketplace = $this->aMagnaSession['currentPlatform'];
        $this->mpId = $this->aMagnaSession['mpID'];

        $this->aConfig['keytype'] = getDBConfigValue('general.keytype', '0');
    }

    public function saveSingleProductProperties($iProductId, $aItemDetails)
    {
        $this->saveMultipleProductProperties(array($iProductId), $aItemDetails);
    }

    public function saveMultipleProductProperties($aProductIds, $aItemDetails)
    {
        $isSinglePrepare = count($aProductIds) === 1;
        foreach ($aProductIds as $sProductId) {
            $aRow = $this->preparePropertiesRow($sProductId, $aItemDetails, $isSinglePrepare);
            $this->insertPrepareData($aRow);
        }
    }

    /**
     * Hilfsfunktion fuer SaveSingleProductProperties und SaveMultipleProductProperties
     * bereite die DB-Zeile vor mit allen Daten die sowohl fuer Single als auch Multiple inserts gelten
     */
    protected function preparePropertiesRow($iProductId, $aItemDetails, $singlePrepare = false)
    {
        $product = MLProduct::gi()->setLanguage(getDBConfigValue('metro.lang',
            $this->mpId))->getProductById($iProductId);

        $aRow = array();
        $aRow['mpID'] = $this->mpId;
        $aRow['products_id'] = $iProductId;
        $aRow['products_model'] = $product['ProductsModel'];

        $aRow['PreparedTS'] = date('Y-m-d H:i:s');
        $aRow['Verified'] = 'OK'; // MP & API provides no Verify Request
        // Title, Description -> depends if Single or Multi

        if (!isset($aItemDetails['PrimaryCategory']) || $aItemDetails['PrimaryCategory'] === '') {
            $this->aErrors['ML_RICARDO_ERROR_CATEGORY'] = ML_RICARDO_ERROR_CATEGORY;
        } else {
            $aRow['PrimaryCategory'] = $aItemDetails['PrimaryCategory'];
            $m = new MetroCategoryMatching();
            $aRow['PrimaryCategoryName'] = $m->getMetroCategoryPath($aItemDetails['PrimaryCategory']);
        }

        if (!$singlePrepare) {
            $aItemDetails['Title'] = $product['Title'];
            $aItemDetails['ShortDescription'] = $product['ShortDescription'];
            $aItemDetails['Description'] = $product['Description'];
            $aItemDetails['Manufacturer'] = $product['Manufacturer'];
            $aItemDetails['Brand'] = $product['Manufacturer'];
            $aItemDetails['ManufacturerPartNumber'] = $product['ManufacturerPartNumber'];
            $aMetaDescription = array_slice(explode(',', $product['BulletPoints']), 0, 5);
            $aItemDetails['Feature'] = array_map('trim', $aMetaDescription);
            $aItemDetails['GTIN'] = $product['EAN'];
        }

        if (isset($aItemDetails['Title']) && trim($aItemDetails['Title']) === '') {
            $this->aErrors['ML_RICARDO_ERROR_TITLE'] = ML_RICARDO_ERROR_TITLE;
        } else {
            $aRow['Title'] = $aItemDetails['Title'];
        }

        if (isset($aItemDetails['ShortDescription'])) {
            $aRow['ShortDescription'] = $aItemDetails['ShortDescription'];
        }

        if (isset($aItemDetails['Description'])) {
            $aRow['Description'] = $aItemDetails['Description'];
        }

        if (isset($aItemDetails['Manufacturer'])) {
            $aRow['Manufacturer'] = $aItemDetails['Manufacturer'];
        }

        if (isset($aItemDetails['ManufacturerPartNumber'])) {
            $aRow['ManufacturerPartNumber'] = $aItemDetails['ManufacturerPartNumber'];
        }

        if (isset($aItemDetails['Brand'])) {
            $aRow['Brand'] = $aItemDetails['Brand'];
        }

        if (isset($aItemDetails['Description']) && trim($aItemDetails['Description']) === '') {
            $this->aErrors['ML_RICARDO_ERROR_DESCRIPTION'] = ML_RICARDO_ERROR_DESCRIPTION;
        }

        if (isset($aItemDetails['Feature'])) {
            $aRow['Feature'] = serialize($aItemDetails['Feature']);
        } else {
            $aRow['Feature'] = '';
        }

        $imagePath = getDBConfigValue($this->sMarketplace . '.imagepath', $this->mpId, '');
        if (empty($imagePath)) {
            $imagePath = SHOP_URL_POPUP_IMAGES;
        }

        if (empty($aItemDetails['GalleryPictures'])
            || !isset($aItemDetails['GalleryPictures']['Images'])
            || empty($aItemDetails['GalleryPictures']['Images'])) {
            $aRow['Images'] = '';
            if (!$singlePrepare) {
                $aRow['Images'] = array();
                /**
                 * @var $product MLProduct
                 */
                foreach ($product['Images'] as $name) {
                    $aRow['Images'][] = $imagePath . $name;
                }
            } else {
                $this->aErrors['ML_RICARDO_ERROR_IMAGES'] = ML_RICARDO_ERROR_IMAGES;
            }
        } else {
            $aRow['Images'] = array();
            foreach ($aItemDetails['GalleryPictures']['Images'] as $name => $checked) {
                if ($checked === 'true') {
                    $aRow['Images'][] = $imagePath.$name;
                }
            }
        }
        if (empty($aRow['Images'])) {
            $this->aErrors['ML_RICARDO_ERROR_IMAGES'] = ML_RICARDO_ERROR_IMAGES;
        }
        $aRow['Images'] = json_encode($aRow['Images']);

        if (
            (
                !isset($aItemDetails['GTIN'])
                || $aItemDetails['GTIN'] === ''
                || !preg_match('/^\d+$/', $aItemDetails['GTIN'])
                || strlen($aItemDetails['GTIN']) > 14
            )
            && (empty($aItemDetails['Manufacturer']) || empty($aItemDetails['ManufacturerPartNumber']))
        ) {
            $this->aErrors['ML_METRO_ERROR_GTIN'] = '('.$product['ProductsModel'].') '.ML_METRO_ERROR_GTIN;
        } else {
            $aRow['GTIN'] = $aItemDetails['GTIN'];
        }

        if ($singlePrepare) {
            $msrp = (float)number_format(MetroHelper::str2float($aItemDetails['MSRP']), 2, '.', '');
            if ($msrp !== 0.0) {
                $aRow['MSRP'] = is_float($msrp) ? $msrp : null;
            }
        }

        $aRow['ProcessingTime'] = $aItemDetails['ProcessingTime'];
        $aRow['MaxProcessingTime'] = $aItemDetails['MaxProcessingTime'];
        $aRow['BusinessModel'] = $aItemDetails['BusinessModel'];
        $aRow['FreightForwarding'] = $aItemDetails['FreightForwarding'];
        $aRow['ShopVariation'] = $aItemDetails['ShopVariation'];
        $aRow['ShippingProfile'] = $aItemDetails['ShippingProfile'];
        $aRow['ShippingGroup'] = $aItemDetails['ShippingGroup'];

        if (!empty($this->aErrors)) {
            $aRow['Verified'] = 'ERROR';
        }
        return $aRow;
    }

    protected function insertPrepareData($aData)
    {
        /* {Hook} "MetroInsertPrepareData": Enables you to modify the prepared product data before it will be saved.<br>
            Variables that can be used:
            <ul>
             <li><code>$aData</code>: The data of a product.</li>
             <li>$this->mpID</code>: The ID of the marketplace.</li>
            </ul>
        */
        if (($hp = magnaContribVerify('MetroInsertPrepareData', 1)) !== false) {
            require($hp);
        }
        if (self::DEBUG) {
            echo print_m($aData, __METHOD__);
            die();
        }
        MagnaDB::gi()->insert(TABLE_MAGNA_METRO_PREPARE, $aData, true);
    }
}
