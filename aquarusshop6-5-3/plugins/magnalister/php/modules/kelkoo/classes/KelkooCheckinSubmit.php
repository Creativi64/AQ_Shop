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
 * $Id: KelkooCheckinSubmit.php 652 2010-12-31 12:06:08Z derpapst $
 *
 * (c) 2010 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */

defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');
require_once(DIR_MAGNALISTER_INCLUDES.'lib/classes/ComparisonShopping/ComparisonShoppingCheckinSubmit.php');

class KelkooCheckinSubmit extends ComparisonShoppingCheckinSubmit {

	protected function appendAdditionalData($pID, $product, &$data) {
		parent::appendAdditionalData($pID, $product, $data);
		$catname = $this->getcategoriesname($product['ProductId']);
		if (!empty($catname)) {
			$data['submit']['MerchantCategory'] = $catname;
		}
	}

	protected function filterItem($pID, $data) {
		$failedFields = array();
		if ((STOCK_ALLOW_CHECKOUT == 'true') && ((int)$data['Quantity'] == 0)) {
			$failedFields[] = 'Quantity';
		}
		return $failedFields;
	}

}
