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
 * (c) 2010 - 2022 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */

defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');
require_once(DIR_MAGNALISTER_MODULES.'magnacompatible/listings/MagnaCompatibleInventoryView.php');

class MetroInventoryView extends MagnaCompatibleInventoryView {

    public function __construct() {
        parent::__construct();
        $this->saveDeletedLocally = false;
    }

    public function prepareInventoryData() {
        global $magnaConfig;

        $result = $this->getInventory();
        if (($result !== false) && !empty($result['DATA'])) {
            $this->renderableData = $result['DATA'];
            foreach ($this->renderableData as &$item) {
                $aProductData = unserialize($item['ProductData']);
                if (isset($aProductData['Title'])) {
                    $item['MarketplaceTitle'] = $aProductData['Title'];
                    $item['MarketplaceTitleShort'] = (mb_strlen($item['MarketplaceTitle'],
                            'UTF-8') > $this->settings['maxTitleChars'] + 2)
                        ? (fixHTMLUTF8Entities(mb_substr($item['MarketplaceTitle'], 0, $this->settings['maxTitleChars'],
                                'UTF-8')).'&hellip;')
                        : fixHTMLUTF8Entities($item['MarketplaceTitle']);
                    unset($aProductData['Title']);
                } else {
                    $item['MarketplaceTitle'] = '&mdash;';
                    $item['MarketplaceTitleShort'] = '&mdash;';
                }

                if (is_array($this->settings['language'])) {
                    $sLanguageId = current($this->settings['language']);
                } else {
                    $sLanguageId = $this->settings['language'];
                }
                $item['ProductsID'] = $pID = magnaSKU2pID($item['SKU']);

                // When product is found
                if ($pID !== 0) {
                    $sTitle = (string)MagnaDB::gi()->fetchOne(eecho("
                        SELECT products_name 
                          FROM ".TABLE_PRODUCTS_DESCRIPTION."
                         WHERE     products_id = '".$pID."'
                               AND language_code = '".mlGetLanguageCodeFromID($sLanguageId)."'
                         LIMIT 1
                    ", false));
                    if (!empty($sTitle)) {
                        $item['Title'] = $sTitle;
                    }
                    $item['TitleShort'] = (mb_strlen($item['Title'], 'UTF-8') > $this->settings['maxTitleChars'] + 2)
                        ? (fixHTMLUTF8Entities(mb_substr($item['Title'], 0, $this->settings['maxTitleChars'],
                                'UTF-8')).'&hellip;')
                        : fixHTMLUTF8Entities($item['Title']);

                    // Price and Quantity Shop
                    $aPriceAndQuantity = MagnaDB::gi()->fetchRow(eecho("
                        SELECT products_price, products_quantity
                          FROM ".TABLE_PRODUCTS."
                         WHERE products_id = ".$pID."
                        LIMIT 1
                    ", false));
                    $item['ShopPrice'] = $aPriceAndQuantity['products_price'];
                    $item['ShopQuantity'] = (int)$aPriceAndQuantity['products_quantity'];
                } else {
                    $item['Title'] = '&mdash;';
                    $item['TitleShort'] = '&mdash;';
                    $item['ShopPrice'] = '&mdash;';
                    $item['ShopQuantity'] = '&mdash;';
                }

                $item['DateAdded'] = ((isset($item['DateAdded'])) ? strtotime($item['DateAdded']) : '&mdash;');

                // Extend Metro Inventory View (Offer API v2 Changes)
                $this->prepareInventoryItemData($item);
            }
            unset($result);
        }
    }

    /**
     * Extend Metro Inventory View (Offer API v2 Changes)
     *
     * @param $item
     * @return void
     */
    protected function prepareInventoryItemData(&$item) {
        // Pull Prepare Data
        if (getDBConfigValue('general.keytype', '0') == 'artNr') {
            $checkSKU = $item['SKU'];
            if (array_key_exists('MasterSKU', $item)) {
                $checkSKU = $item['MasterSKU'];
            }
            $sPropertiesWhere = "products_model = '".MagnaDB::gi()->escape($checkSKU)."'";
        } else {
            $sPropertiesWhere = "products_id = '".$item['ProductsID']."'";
        }
        $prepareData = MagnaDB::gi()->fetchRow(eecho("
            SELECT *
              FROM ".TABLE_MAGNA_METRO_PREPARE."
             WHERE     ".$sPropertiesWhere."
                   AND mpID = '".$this->mpID."'
        ", false));

        // Set SimplePrice to current Product
        $this->simplePrice->setFinalPriceFromDB($item['ProductsID'], $this->mpID);
        $item['ShopNetPrice'] = $this->simplePrice->removeTaxByPID($item['ProductsID'])->getPrice();
        $item['Tax'] = SimplePrice::getTaxByPID($item['ProductsID']);

        //Shipping costs (Gross + Net)
        $shippingPriceConfigValue = getDBConfigValue('metro.shippingprofile.cost', $this->mpID);
        if (!empty($prepareData['ShippingProfile']) && array_key_exists($prepareData['ShippingProfile'], $shippingPriceConfigValue)) {
            $shippingProfilePrice = $shippingPriceConfigValue[$prepareData['ShippingProfile']];
            $item['ShippingCost'] = (float)$shippingProfilePrice;
            $item['NetShippingCost'] = round(($item['ShippingCost'] / ((100 + (float)$item['Tax']) / 100)), 2);
        } else {
            $item['ShippingCost'] = $item['NetShippingCost'] = null;
        }
    }

    protected function getFields() {
        return array(
            'SKU' => array(
                'Label' => ML_LABEL_SKU,
                'Sorter' => 'sku',
                'Getter' => 'getSKU', /** @uses getSKU */
                'Field' => null
            ),
            'ShopTitle' => array(
                'Label' => ML_LABEL_SHOP_TITLE,
                'Sorter' => null,
                'Getter' => 'getTitle', /** @uses getTitle */
                'Field' => null,
            ),
            'Title' => array(
                'Label' => ML_METRO_LABEL_TITLE,
                'Sorter' => 'marketplacetitle',
                'Getter' => 'getMpTitle', /** @uses getMpTitle */
                'Field' => null,
            ),
            'ListingId' => array(
                'Label' => ML_METRO_LISTING_ID,
                'Sorter' => null,
                'Getter' => 'getLinkedListingId', /** @uses getLinkedListingId */
                'Field' => null,
            ),
            'Price' => array(
                'Label' => ML_METRO_PRICE_SHOP_METRO,
                'Sorter' => 'NetPrice',
                'Getter' => 'getItemPrice', /** @uses getItemPrice */
                'Field' => null
            ),
            'Quantity' => array(
                'Label' => ML_METRO_STOCK_SHOP_METRO,
                'Sorter' => 'quantity',
                'Getter' => 'getItemQuantity', /** @uses getItemQuantity */
                'Field' => null,
            ),
            'DateAdded' => array(
                'Label' => ML_GENERIC_CHECKINDATE,
                'Sorter' => 'dateadded',
                'Getter' => 'getItemDateAdded', /** @uses getItemDateAdded */
                'Field' => null
            ),
            'DateUpdated' => array(
                'Label' => ML_LAST_SYNC,
                'Sorter' => 'lastsync',
                'Getter' => 'getItemLastSync', /** @uses getItemLastSync */
                'Field' => null
            ),
            'Status' => array(
                'Label' => ML_GENERIC_STATUS,
                'Sorter' => null,
                'Getter' => 'getItemStatus', /** @uses getItemStatus */
                'Field' => null
            ),
        );
    }

    protected function getSKU($item) {
        return '<td>'.fixHTMLUTF8Entities($item['SKU'], ENT_COMPAT).'</td>';
    }

    protected function getMpTitle($item) {
        return '<td title="'.fixHTMLUTF8Entities($item['MarketplaceTitle'], ENT_COMPAT).'">'.$item['MarketplaceTitleShort'].'</td>';
    }

    protected function getLinkedListingId($item) {
        $blIsLinked = false;
        while (!empty($item['Data'])) {
            $aData = json_decode($item['Data'], true);
            if (!is_array($aData)) {
                break;
            }
            if (!isset($aData['Url'])) {
                break;
            }
            $blIsLinked = true;
            break;
        }
        if ($blIsLinked) {
            return '<td title="'.$item['MetroId'].'"><a href="'.$aData['Url'].'" target="_blank" >'.$item['MetroId'].'</a></td>';
        } else {
            return '<td title="'.$item['MetroId'].'">'.$item['MetroId'].'</td>';
        }
    }

    protected function getItemPrice($item) {
        if ($item['ShopPrice'] > 0) {
            $sShopPrice = $this->simplePrice->setPriceAndCurrency($item['ShopPrice'], $this->mpCurrency)->addTaxByPID($item['ProductsID'])->format();
        } else {
            $sShopPrice = '&mdash;';
        }
        $item['Currency'] = isset($item['Currency']) ? $item['Currency'] : $this->mpCurrency;

        $shippingCost = '';
        $shippingNetCost = '';
        if ($item['ShippingCost'] !== null && $item['NetShippingCost'] !== null) {
            $shippingCost = $this->simplePrice->setPriceAndCurrency($item['ShippingCost'], $item['Currency'])->format();
            $shippingNetCost = $this->simplePrice->setPriceAndCurrency($item['NetShippingCost'], $item['Currency'])->format();
        }

        $sMetroPrice = (isset($item['Price']) && 0 != $item['Price'])
            ? $this->simplePrice->setPriceAndCurrency($item['Price'], $item['Currency'])->format()
            .'<span class="small">('.ML_LABEL_INCL.' '.$shippingCost.' '.ML_GENERIC_SHIPPING.')</span>'
            : '&mdash';

        $renderedShopNetPrice = (isset($item['ShopNetPrice']) && 0 != $item['ShopNetPrice'])
            ? $this->simplePrice->setPriceAndCurrency($item['ShopNetPrice'], $item['Currency'])->format()
            : '&mdash;';
        $renderedMpNetPrice = ((isset($item['NetPrice']) && 0 != $item['NetPrice'])
            ? $this->simplePrice->setPriceAndCurrency($item['NetPrice'], $item['Currency'])->format()
            .'<span class="small">('.ML_LABEL_INCL.' '.$shippingNetCost.' '.ML_GENERIC_SHIPPING.')</span>'
            : '&mdash;'
        );

        return '<td>'.$sShopPrice.' / '.$sMetroPrice.'<br>'.$renderedShopNetPrice.' / '.$renderedMpNetPrice.'</td>';
    }

    protected function getItemQuantity($item) {
        return '<td>'.$item['ShopQuantity'].' / '.$item['Quantity'].'</td>';
    }

    protected function getItemLastSync($item) {
        if (isset($item['DateUpdated']) && $item['DateUpdated'] != '0000-00-00 00:00:00') {
            $time = strtotime($item['DateUpdated']);
            return '<td>'.date("d.m.Y", $time).' &nbsp;&nbsp;<span class="small">'.date("H:i", $time).'</span>'.'</td>';
        }

        return '<td>&mdash;</td>';
    }

    protected function getItemStatus($item) {
        if ('active' == $item['StatusProduct'] && 'active' == $item['StatusOffer']) {
            return '<td>'.ML_GENERIC_INVENTORY_STATUS_ACTIVE.'</td>';
        } elseif ('waiting' == $item['StatusProduct'] && ('waiting' == $item['StatusOffer'] || 'creating' == $item['StatusOffer'])) {
            return '<td>'.ML_GENERIC_STATUS_PRODUCT_IS_CREATED.'</td>';
        } elseif ('waiting' == $item['StatusProduct'] && 'active' == $item['StatusOffer']) {
            return '<td>'.ML_GENERIC_STATUS_PRODUCT_IS_UPDATED.'</td>';
        } elseif ('active' == $item['StatusProduct'] && 'waiting' == $item['StatusOffer']) {
            return '<td>'.ML_GENERIC_STATUS_OFFER_IS_UPDATED.'</td>';
        } elseif ('active' == $item['StatusProduct'] && 'creating' == $item['StatusOffer']) {
            return '<td>'.ML_GENERIC_STATUS_OFFER_IS_CREATED.'</td>';
        } elseif ('pending_delete' == $item['StatusOffer']) {
            return '<td>'.ML_METRO_STATUS_PRODUCT_IS_PENDING_DELETE.'</td>';
        } else {
            return '<td>&mdash;</td>';
        }
    }
}
