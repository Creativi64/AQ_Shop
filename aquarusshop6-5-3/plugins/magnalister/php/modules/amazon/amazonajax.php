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
 * $Id: amazonajax.php 328 2014-05-15 12:32:25Z markus.bauer $
 *
 * (c) 2010 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */

defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');
require_once (DIR_MAGNALISTER_INCLUDES.'lib/classes/SimplePrice.php');

if (isset($_POST['request'])) {
	$r = $_POST['request'];

	if ($r == 'ItemSearch') {
		include_once(DIR_MAGNALISTER_MODULES.'amazon/matching/matchingViews.php');
		if (isset($_POST['search']) && !empty($_POST['search']) &&
			isset($_POST['productID']) && !empty($_POST['productID'])) {
			$search = $_POST['search'];
			$productID = $_POST['productID'];

			try {
				$result = MagnaConnector::gi()->submitRequest(array(
					'ACTION' => 'ItemSearch',
					'NAME'   => $search
				));
			} catch (MagnaException $e) {
				$result = array('DATA' => array());
			}
			if (!empty($result['DATA'])) {
				foreach ($result['DATA'] as &$data) {
					if (!empty($data['Author'])) {
						$data['Title'] .= ' ('.$data['Author'].')';
					}
					$price = new SimplePrice($data['LowestPrice']['Price'], $data['LowestPrice']['CurrencyCode']);
					$data['LowestPrice'] = $data['LowestPrice']['Price'];
					$data['LowestPriceFormated'] = $price->format();
				}
			}

			$dbProd = MLProduct::gi()->getProductByIdOld($productID);
			header('Content-Type: text/html; charset=ISO-8859-1');
			renderMathingResultTr($productID, $search, '', $result['DATA']);
		}
	}

	if ($r == 'ItemLookup') {
		include_once(DIR_MAGNALISTER_MODULES.'amazon/matching/matchingViews.php');
		if (isset($_POST['asin']) && !empty($_POST['asin']) &&
		    isset($_POST['productID']) && !empty($_POST['productID'])) {
			$asin = $_POST['asin'];
			$productID = $_POST['productID'];

			try {
				$result = MagnaConnector::gi()->submitRequest(array(
					'ACTION' => 'ItemLookup',
					'ASIN' => $asin
				));
			} catch (MagnaException $e) {
				$result = array('DATA' => array());
			}
			$dbProd = MLProduct::gi()->getProductByIdOld($productID);

			if (!empty($result['DATA'])) {
				foreach ($result['DATA'] as &$data) {
					if (array_key_exists('Author', $data) && !empty($data['Author'])) {
						$data['Title'] .= ' ('.$data['Author'].')';
					}
					$price = new SimplePrice($data['LowestPrice']['Price'], $data['LowestPrice']['CurrencyCode']);
					$data['LowestPrice'] = $data['LowestPrice']['Price'];
					$data['LowestPriceFormated'] = $price->format();
				}
			}
			header('Content-Type: text/html; charset=ISO-8859-1');
			renderMathingResultTr($productID, $dbProd['products_name'], '', $result['DATA']);
		}
	}

    if ($r === 'UpdatePrepareTable') {
        // parse data from `data` field and extract main category and save it to separate column
        $allItems = MagnaDB::gi()->fetchArray("
            SELECT `mpID`, `products_id`, `data` 
              FROM " . TABLE_MAGNA_AMAZON_APPLY . "
             WHERE mpID = '" . $_MagnaSession['mpID'] . "' AND
                   MainCategory = ''
             LIMIT 0, 20
        ", true);

        if (!empty($allItems)) {
            foreach ($allItems as $item) {
                $aData = unserialize(base64_decode($item['data']));
                MagnaDB::gi()->update(TABLE_MAGNA_AMAZON_APPLY, array(
                    'MainCategory' => !empty($aData['MainCategory']) ? $aData['MainCategory'] : 'null'
                ), array(
                    'mpID' => $item['mpID'],
                    'products_id' => $item['products_id'],
                ));
            }
        }

        $finished = MagnaDB::gi()->fetchOne("
            SELECT count(*) 
              FROM " . TABLE_MAGNA_AMAZON_APPLY . "
             WHERE mpID = '" . $_MagnaSession['mpID'] . "' AND
                   MainCategory != ''
        ");

        $count = MagnaDB::gi()->fetchOne("
            SELECT count(*) 
              FROM " . TABLE_MAGNA_AMAZON_APPLY . "
             WHERE mpID = '" . $_MagnaSession['mpID'] . "'
        ");

        header('Content-Type: application/JSON; charset=UTF8');
        echo json_encode(array(
			'success' => true,
            'finished' => $finished,
            'total' => $count,
        ));
    }
}
