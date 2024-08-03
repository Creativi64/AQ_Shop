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

defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');
require_once(DIR_MAGNALISTER_MODULES.'magnacompatible/listings/MagnaCompatibleInventoryView.php');

class OttoInventoryView extends MagnaCompatibleInventoryView {

    public function __construct() {
        parent::__construct();
        $this->saveDeletedLocally = false;
    }

    /*
     * overwritten by eBay-like function
     * to get shop prices and quantities
     * + some performance by doing queries for the entire item list instead of for each item
     */
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
                }
                $this->prepareInventoryItemData($item);
                if (is_array($this->settings['language'])) {
                    $sLanguageId = current($this->settings['language']);
                } else {
                    $sLanguageId = $this->settings['language'];
                }
                $item['ProductsID'] = $pID = magnaSKU2pID($item['SKU']);
                $sTitle = (string)MagnaDB::gi()->fetchOne("
					SELECT products_name 
					  FROM ".TABLE_PRODUCTS_DESCRIPTION."
					 WHERE     products_id = '".$pID."'
					       AND language_code = '".$sLanguageId."'
				");
                if (!empty($sTitle)) {
                    $item['Title'] = $sTitle;
                }
                $item['TitleShort'] = (mb_strlen($item['Title'], 'UTF-8') > $this->settings['maxTitleChars'] + 2)
                    ? (fixHTMLUTF8Entities(mb_substr($item['Title'], 0, $this->settings['maxTitleChars'],
                            'UTF-8')).'&hellip;')
                    : fixHTMLUTF8Entities($item['Title']);
                $item['DateAdded'] = ((isset($item['DateAdded'])) ? strtotime($item['DateAdded']) : '');
                $aPriceAndQuantity = MagnaDB::gi()->fetchRow("
					SELECT products_price, products_quantity
					  FROM ".TABLE_PRODUCTS."
					 WHERE     products_id = ".$pID."
					LIMIT 1
				");
                $item['ShopPrice'] = $aPriceAndQuantity['products_price'];
                $item['ShopQuantity'] = (int)$aPriceAndQuantity['products_quantity'];
            }
            unset($result);
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

            'ProductUrl' => array(
                'Label' => 'URL',
                'Sorter' => 'itemtitle',
                'Getter' => 'getProductUrl',
                'Field' => null,
            ),
            'Price' => array(
                'Label' => ML_OTTO_PRICE_SHOP_OTTO,
                'Sorter' => 'price',
                'Getter' => 'getItemPrice', /** @uses getItemPrice */
                'Field' => null
            ),
            'Quantity' => array(
                'Label' => ML_OTTO_STOCK_SHOP_OTTO,
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

    protected function getProductUrl($item) {
        if (!empty($item['ProductUrl'])) {
            return '<td><a class="ml-js-noBlockUi" href="'.$item['ProductUrl'].'" target="_blank">LINK</a></td>';
        }
        return '<td>&mdash;</td>';
    }

    protected function getItemPrice($item) {
        if ($item['ShopPrice'] > 0) {
            $sShopPrice = $this->simplePrice->setPriceAndCurrency($item['ShopPrice'], $this->mpCurrency)->addTaxByPID($item['ProductsID'])->format();
        } else {
            $sShopPrice = '&mdash;';
        }
        $item['Currency'] = isset($item['Currency']) ? $item['Currency'] : $this->mpCurrency;
        $sOttoPrice = $this->simplePrice->setPriceAndCurrency($item['Price'], $item['Currency'])->format();
        return '<td>'.$sShopPrice.' / '.$sOttoPrice/*.'<br />'.print_m($item, '$item')*/.'</td>';
    }

    protected function getItemQuantity($item) {
        return '<td>' . $item['ShopQuantity'] . ' / ' . $item['Quantity'] . '<br />' . date("d.m.Y", $item['LastSync']) . ' &nbsp;&nbsp;<span class="small">' . date("H:i", $item['LastSync']) . '</span></td>';
    }

    protected function getItemLastSync($item) {
        $item['LastSync'] = ((isset($item['DateUpdated'])) ? strtotime($item['DateUpdated']) : '');
        return '<td>'.date("d.m.Y", $item['LastSync']).' &nbsp;&nbsp;<span class="small">'.date("H:i", $item['LastSync']).'</span>'.'</td>';
    }

    protected function getItemStatus($item) {
        if ('active' == $item['StatusProduct']) {
            return '<td>'.ML_GENERIC_INVENTORY_STATUS_ACTIVE.'</td>';
        } elseif ('waiting' == $item['StatusProduct']) {
            return '<td>'.ML_OTTO_STATUS_PRODUCT_IS_CREATED.'</td>';
        } elseif ('creating' == $item['StatusProduct']) {
            return '<td>'.ML_OTTO_STATUS_PRODUCT_IS_CREATING.'</td>';
        } elseif ('pending_delete' == $item['StatusProduct']) {
            return '<td>'.ML_OTTO_STATUS_PRODUCT_IS_PENDING_DELETE.'</td>';
        } elseif ('pending_creation' == $item['StatusProduct']) {
            return '<td>'.ML_OTTO_STATUS_PRODUCT_IS_PENDING_CREATION.'</td>';
        } elseif ('pending_update' == $item['StatusProduct']) {
            return '<td>'.ML_OTTO_STATUS_PRODUCT_IS_PENDING_UPDATE.'</td>';
        } else {
            return '<td>&mdash;</td>';
        }
    }
}
