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
 * $Id: InventoryView.php 1224 2011-09-06 00:28:04Z derpapst $
 *
 * (c) 2011 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */

defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');
require_once (DIR_MAGNALISTER_MODULES.'magnacompatible/listings/MagnaCompatibleInventoryView.php');

class EtsyInventoryView extends MagnaCompatibleInventoryView {

	public function prepareInventoryData() {
		global $magnaConfig;

		$result = $this->getInventory();
		if (($result !== false) && !empty($result['DATA'])) {
			$this->renderableData = $result['DATA'];
			foreach ($this->renderableData as &$item) {
				if (isset($item['Title'])) {
					$item['MarketplaceTitle'] = $item['Title'];
					$item['MarketplaceTitleShort'] = (mb_strlen($item['MarketplaceTitle'], 'UTF-8') > $this->settings['maxTitleChars'] + 2)
						? (fixHTMLUTF8Entities(mb_substr($item['MarketplaceTitle'], 0, $this->settings['maxTitleChars'], 'UTF-8')).'&hellip;')
						: fixHTMLUTF8Entities($item['MarketplaceTitle']);
					unset($item['Title']);
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
					       AND language_code = '".mlGetLanguageCodeFromID($sLanguageId)."'
				");
				if (!empty($sTitle)) {
					$item['Title'] = $sTitle;
				}
				$item['TitleShort'] = (mb_strlen($item['Title'], 'UTF-8') > $this->settings['maxTitleChars'] + 2)
					? (fixHTMLUTF8Entities(mb_substr($item['Title'], 0, $this->settings['maxTitleChars'], 'UTF-8')).'&hellip;')
					: fixHTMLUTF8Entities($item['Title']);
				$item['DateAdded'] = ((isset($item['DateAdded'])) ? strtotime($item['DateAdded']) : '');
				$aPriceAndQuantity = MagnaDB::gi()->fetchRow("
					SELECT products_price, products_quantity
					  FROM ".TABLE_PRODUCTS."
					 WHERE     products_id = ".$pID."
					LIMIT 1
				");
				$item['ShopPrice']    = $aPriceAndQuantity['products_price'];
				$item['ShopQuantity'] = (int)$aPriceAndQuantity['products_quantity'];
			}
			unset($result);
		}

	}
	
/*
 SKU | Shop-Titel | Etsy-Titel | ListingId | Preis Shop/Etsy | Lager Shop/Etsy | DateAdded | LastSync | Status
 Bei v3 fehlen: Etsy-Titel, Lager Shop, steht nicht drin welcher Preis welcher ist
*/
	protected function getFields() {
		return array(
			'SKU' => array (
				'Label' => ML_LABEL_SKU,
				'Sorter' => 'sku',
				'Getter' => null,
				'Field' => 'SKU'
			),
			'ShopTitle' => array (
				'Label' => ML_LABEL_SHOP_TITLE,
				'Sorter' => null,
				'Getter' => 'getTitle',
				'Field' => null,
 			),
			'Title' => array (
				'Label' => ML_ETSY_LABEL_TITLE,
				'Sorter' => 'marketplacetitle',
				'Getter' => 'getMpTitle',
				'Field' => null,
 			),
			'ListingId' => array (
				'Label' => ML_ETSY_LISTING_ID,
				'Sorter' => null,
				'Getter' => 'getLinkedListingId',
				'Field' => null,
			),
 			'Price' => array (
 				'Label' => ML_ETSY_PRICE_SHOP_ETSY,
 				'Sorter' => 'price',
 				'Getter' => 'getItemPrice',
 				'Field' => null
 			),
 			'Quantity' => array (
				'Label' => ML_ETSY_STOCK_SHOP_ETSY,
				'Sorter' => 'quantity',
				'Getter' => 'getItemQuantity',
				'Field' => null,
			),
 			'DateAdded' => array (
 				'Label' => ML_GENERIC_CHECKINDATE,
 				'Sorter' => 'dateadded',
 				'Getter' => 'getItemDateAdded',
 				'Field' => null
 			),
 			'DateUpdated' => array (
 				'Label' => ML_LAST_SYNC,
 				'Sorter' => 'lastsync',
 				'Getter' => 'getItemLastSync',
 				'Field' => null
 			),
 			'Status' => array (
 				'Label' => ML_GENERIC_STATUS,
 				'Sorter' => null,
 				'Getter' => 'getItemStatus',
 				'Field' => null
 			),
		);
	}

	protected function getMpTitle($item) {
		return '<td title="'.fixHTMLUTF8Entities($item['MarketplaceTitle'], ENT_COMPAT).'">'.$item['MarketplaceTitleShort'].'</td>';
	}

	protected function getLinkedListingId($item) {
		$blIsLinked = false;
		while (!empty($item['Data'])) {
			$aData = json_decode($item['Data'], true);
			if (!is_array($aData)) break;
			if (!isset($aData['Url'])) break;
			$blIsLinked = true; break;
		}
		if ($blIsLinked) {
			return '<td title="'.$item['ListingId'].'"><a href="'.$aData['Url'].'" target="_blank" >'.$item['ListingId'].'</a></td>';
		} else {
			return '<td title="'.$item['ListingId'].'">'.$item['ListingId'].'</td>';
		}
	}

	protected function getItemPrice($item) {
		if ($item['ShopPrice'] > 0) {
			$sShopPrice = $this->simplePrice->setPriceAndCurrency($item['ShopPrice'], $this->mpCurrency)->addTaxByPID($item['ProductsID'])->format();
		} else {
			$sShopPrice = '&mdash;';
		}
		$item['Currency'] = isset($item['Currency']) ? $item['Currency'] : $this->mpCurrency;
		$sEtsyPrice = $this->simplePrice->setPriceAndCurrency($item['Price'], $item['Currency'])->format();
		return '<td>'.$sShopPrice.' / '.$sEtsyPrice.'</td>';
	}

	protected function getItemQuantity($item) {
		if (empty($item['ShopQuantity'])) $item['ShopQuantity'] = '&mdash;';
		return '<td>'.$item['ShopQuantity'].' / '.$item['Quantity'].'</td>';
	}

	protected function getItemLastSync($item) {
		$item['LastSync'] = ((isset($item['DateUpdated'])) ? strtotime($item['DateUpdated']) : '');
		return '<td>'.date("d.m.Y", $item['LastSync']).' &nbsp;&nbsp;<span class="small">'.date("H:i", $item['LastSync']).'</span>'.'</td>';	
	}

	protected function getItemStatus($item) {
		switch($item['Status']) {
            case 'add': {
                $sStatus = ML_GENERIC_STATUS_PRODUCT_IS_CREATED;
                break;
            }
            case 'active': {
                $sStatus = ML_GENERIC_INVENTORY_STATUS_ACTIVE;
                break;
            }
            case 'inactive': {
                $sStatus = ML_GENERIC_INVENTORY_STATUS_INACTIVE;
                break;
            }
            case 'expired': {
                $sStatus = ML_GENERIC_INVENTORY_STATUS_EXPIRED;
                break;
            }
            case 'draft': {
                $sStatus = ML_GENERIC_INVENTORY_STATUS_DRAFT;
                break;
            }
            case 'sold_out': {
                $sStatus = ML_GENERIC_INVENTORY_STATUS_SOLD_OUT;
                break;
            }
		}
		return '<td>'.$sStatus.'</td>';
	}
}
