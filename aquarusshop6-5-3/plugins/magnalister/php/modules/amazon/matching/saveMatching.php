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
 * (c) 2010 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */

defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');

/**
 * Auto Matching
 */
function amazonAutoMatching($mpID, $selectionName) {
	global $_MagnaSession;

	$_autoMatchingStats = array (
		'success' => 0,
		'almost' => 0,
		'nosuccess' => 0,
		'_timer' => microtime(true)
	);

	require_once(DIR_MAGNALISTER_MODULES.'amazon/amazonFunctions.php');
	require_once(DIR_MAGNALISTER_INCLUDES.'lib/classes/SimplePrice.php');
	//echo print_m($_POST, '$_POST');

	$allItems = MagnaDB::gi()->fetchArray('
		SELECT pID 
		  FROM '.TABLE_MAGNA_SELECTION.'
	     WHERE mpID=\''.$mpID.'\' AND
	           selectionname=\''.$selectionName.'\' AND
	           session_id=\''.session_id().'\'
	', true);

	$alreadyMatched = MagnaDB::gi()->fetchArray('
		SELECT products_id 
		  FROM `'.TABLE_MAGNA_AMAZON_PROPERTIES.'`
		 WHERE mpID=\''.$mpID.'\'
		       AND asin<>\'\'
	', true);
	if ((isset($_POST['match']) && ($_POST['match'] == 'notmatched')) 
		|| (!isset($_POST['match']) && !getDBConfigValue(array('amazon.multimatching', 'rematch'), $mpID, false))
	) {
		$allItems = array_diff($allItems, $alreadyMatched);
		MagnaDB::gi()->query('
			DELETE FROM '.TABLE_MAGNA_SELECTION.'
			 WHERE mpID=\''.$mpID.'\'
		           AND selectionname=\''.$selectionName.'\'
		           AND session_id=\''.session_id().'\' 
		           AND pID IN (\''.implode('\', \'', $alreadyMatched).'\')
		');
	}

	while (!empty($allItems)) {
		$result = array();
		try {
			$result = MagnaConnector::gi()->submitRequest(array(
				'ACTION' => 'GetInventory',
				'OFFSET' => 0,
				'LIMIT' => 0xFFFFFFFF,
			));
	
		} catch (MagnaException $e) {
			$e->setCriticalStatus(false);
			#echo print_m($e->getErrorArray());
		}
	
		if (array_key_exists('DATA', $result) && !empty($result['DATA'])) {
			$subResult = array();
			foreach ($result['DATA'] as $key => &$item) {
				$subResult[trim($item['SKU'])] = &$item; 
			}
			$result = $subResult;
		}
		//echo print_m($result, '$result');

		$pData = MagnaDB::gi()->fetchRow('SELECT * FROM '.TABLE_PRODUCTS.' LIMIT 1');
		$noEan = (!array_key_exists(MAGNA_FIELD_PRODUCTS_EAN, $pData));

		$price = new SimplePrice();
		$price->setCurrency(getCurrencyFromMarketplace($mpID));

		foreach ($allItems as $pID) {
			@set_time_limit(60);

			$pRow = MagnaDB::gi()->fetchRow('
				SELECT products_model'.($noEan ? '' : ', '.MAGNA_FIELD_PRODUCTS_EAN.' as products_ean').'
				  FROM '.TABLE_PRODUCTS.' 
				 WHERE products_id=\''.$pID.'\'
			');
			if (!array_key_exists('products_ean', $pRow)) {
				$pRow['products_ean'] = '';
			}

			$searchResult = array();
			$sku = magnaPID2SKU($pID);
			if (array_key_exists($sku, $result)) {
				$searchResult = performItemSearch($result[$sku]['ASIN'], '', '');
				//echo print_m($searchResult, '$searchResult(1)');
			}

			if (!$noEan && (count($searchResult) != 1) && !empty($pRow['products_ean'])) {
				$searchResult = performItemSearch('', $pRow['products_ean'], '');
				//echo print_m($searchResult, '$searchResult(2)');
			}

			if (count($searchResult) != 1) { 
				if (count($searchResult) > 0) {
					++$_autoMatchingStats['almost'];
				}
				++$_autoMatchingStats['nosuccess'];

				// continue;
				MagnaDB::gi()->delete(TABLE_MAGNA_SELECTION, array(
					'pID' => $pID,
					'mpID' => $mpID,
					'selectionname' => $selectionName,
					'session_id' => session_id()
				));
				continue;
			}
			//echo print_m($searchResult[0], $ean);
			if (!isset($searchResult[0]['LowestPrice'])) {
				$searchResult[0]['LowestPrice'] = '0.0';
			}
			$_MagnaSession['amazonLastPreparedTS'] = array_key_exists('amazonLastPreparedTS', $_MagnaSession) ? $_MagnaSession['amazonLastPreparedTS'] : date('Y-m-d H:i:s');

			$data = array(
				'PreparedTS' => $_MagnaSession['amazonLastPreparedTS'],
				'mpID' => $mpID,
				'products_id' => $pID,
				'products_model' => $pRow['products_model'],
				'asin' => $searchResult[0]['ASIN'],
				'item_condition' => getDBConfigValue('amazon.itemCondition', $mpID),
				'will_ship_internationally' => 0,
				'category_id' => $searchResult[0]['CategoryID'],
				'category_name' => $searchResult[0]['CategoryName'],
				'lowestprice' => $searchResult[0]['LowestPrice'],
			);
			
			$where = (getDBConfigValue('general.keytype', '0') == 'artNr')
				? array (
					'products_model' => $data['products_model']
				)
				: array (
					'products_id' => $data['products_id']
				);
			$where['mpID'] = $mpID;
			if (!empty($data['asin'])) {
				if (MagnaDB::gi()->recordExists(TABLE_MAGNA_AMAZON_PROPERTIES, $where)) {
					MagnaDB::gi()->update(TABLE_MAGNA_AMAZON_PROPERTIES, $data, $where);
				} else {
					$data['leadtimeToShip'] = getDBConfigValue('amazon.leadtimetoship', $_MagnaSession['mpID'], '0');
					MagnaDB::gi()->insert(TABLE_MAGNA_AMAZON_PROPERTIES, $data);
				}
				if (defined('MAGNA_DEV_PRODUCTLIST') && MAGNA_DEV_PRODUCTLIST === true && MLProduct::gi()->hasMasterItems()) {
					$sKeyType = 'products_'.((getDBConfigValue('general.keytype', '0') == 'artNr') ? 'model' : 'id');
					$aMaster = MagnaDb::gi()->fetchRow(eecho("
							SELECT m.products_id, m.products_model
							FROM ".TABLE_PRODUCTS." p
							INNER JOIN ".TABLE_PRODUCTS." m on p.products_master_model = m.products_model
							WHERE p.".$sKeyType." = '". $data[$sKeyType]."'
					", false));
					if (is_array($aMaster) && isset($aMaster['products_id'])) {
						MagnaDb::gi()->query("
							REPLACE INTO ".TABLE_MAGNA_AMAZON_PROPERTIES."
							SET
								products_id='".$aMaster['products_id']."',
								products_model='".MagnaDB::gi()->escape($aMaster['products_model'])."',
								mpID='".$_MagnaSession['mpID']."',
								asin='dummyMasterArticle',
								PreparedTS = '".$_MagnaSession['amazonLastPreparedTS']."',
								leadtimeToShip=".getDBConfigValue('amazon.leadtimetoship', $_MagnaSession['mpID'], '1')."
						");
					}
				}
			}
			
			//*
			MagnaDB::gi()->delete(TABLE_MAGNA_SELECTION, array(
				'pID' => $pID,
				'mpID' => $mpID,
				'selectionname' => $selectionName,
				'session_id' => session_id()
			));
			//*/
			++$_autoMatchingStats['success'];
		}
		
		break;
	}
	
	$_autoMatchingStats['_timer'] = microtime(true) - $_autoMatchingStats['_timer'];

	return $_autoMatchingStats;
}

/**
 * Single Matching
 */
if (array_key_exists('amazonProperties', $_POST)) {
	$data = $_POST['amazonProperties'];
	$data['mpID'] = $_MagnaSession['mpID'];

	$asin = array_first($_POST['match']);
	
	if ($asin != 'false') {
		$data['asin'] = $asin;

		$data['category_id'] = isset($_POST['catID'][$asin]) ? $_POST['catID'][$asin] : '';
		$data['category_name'] = isset($_POST['catName'][$asin]) ? $_POST['catName'][$asin] : '';
		$data['lowestprice'] = isset($_POST['lowprice'][$asin]) ? $_POST['lowprice'][$asin] : '0.0';
	} else {
		$data['asin'] = $data['item_note'] = $data['category_id'] = $data['category_name'] = $data['item_condition'] = '';
		$data['asin_type'] = $data['will_ship_internationally'] = $data['lowestprice'] = 0;
	}	
	$_MagnaSession['amazonLastPreparedTS'] = array_key_exists('amazonLastPreparedTS', $_MagnaSession) ? $_MagnaSession['amazonLastPreparedTS'] : date('Y-m-d H:i:s');
	$data['PreparedTS'] = $_MagnaSession['amazonLastPreparedTS'];
	$data['products_model'] = $_POST['model'][$data['products_id']];
	$where = (getDBConfigValue('general.keytype', '0') == 'artNr')
		? array (
			'products_model' => $data['products_model']
		)
		: array (
			'products_id' => $data['products_id']
		);
	$where['mpID'] = $_MagnaSession['mpID'];
	if (!empty($data['asin'])) {
		if (MagnaDB::gi()->recordExists(TABLE_MAGNA_AMAZON_PROPERTIES, $where)) {
			MagnaDB::gi()->update(TABLE_MAGNA_AMAZON_PROPERTIES, $data, $where);
		} else {
			MagnaDB::gi()->insert(TABLE_MAGNA_AMAZON_PROPERTIES, $data);
		}
		if (defined('MAGNA_DEV_PRODUCTLIST') && MAGNA_DEV_PRODUCTLIST === true && MLProduct::gi()->hasMasterItems()) {
			$sKeyType = 'products_'.((getDBConfigValue('general.keytype', '0') == 'artNr') ? 'model' : 'id');
			// if product has variants fetch id and model from master
			$aMaster = MagnaDb::gi()->fetchRow(eecho("
			    SELECT m.products_id, m.products_model
			      FROM ".TABLE_PRODUCTS." p
			INNER JOIN ".TABLE_PRODUCTS." m on p.products_master_model = m.products_model
		 	     WHERE     p.".$sKeyType." = '". $data[$sKeyType]."'
			           AND m.products_master_flag = '1'
			", false));
			// if a product has no variants
			if ($aMaster === false) {
				$aMaster = MagnaDb::gi()->fetchRow(eecho("
				    SELECT m.products_id, m.products_model
				      FROM ".TABLE_PRODUCTS." p
				INNER JOIN ".TABLE_PRODUCTS." m on p.products_master_model = m.products_model
				     WHERE p.".$sKeyType." = '". $data[$sKeyType]."'
				", false));
			}
			if (is_array($aMaster) && isset($aMaster['products_id'])) {
				MagnaDb::gi()->query("
					REPLACE INTO ".TABLE_MAGNA_AMAZON_PROPERTIES."
					SET
						products_id='".$aMaster['products_id']."',
						products_model='".MagnaDB::gi()->escape($aMaster['products_model'])."',
						mpID='".$_MagnaSession['mpID']."',
						asin='dummyMasterArticle',
						PreparedTS = '".$_MagnaSession['amazonLastPreparedTS']."',
						leadtimeToShip=".getDBConfigValue('amazon.leadtimetoship', $_MagnaSession['mpID'], '1')."
				");
			}
		}
	}
}

/**
 * Multi Matching
 */
if (array_key_exists('action', $_GET) && ($_GET['action'] == 'multimatching') && array_key_exists('match', $_POST)) {
	$items = $_POST['match'];

	foreach ($items as $productID => $asin) {
		if ($asin != 'false') {
			$product = MLProduct::gi()->getProductByIdOld($productID);

			$data = array('mpID' => $_MagnaSession['mpID']);
			$data['products_id'] = $product['products_id'];
			$data['products_model'] = $product['products_model'];
			$data['asin'] = $asin;
			$data['item_condition'] = getDBConfigValue('amazon.itemCondition', $_MagnaSession['mpID']);
			$data['will_ship_internationally'] = 0;

			$data['category_id'] = isset($_POST['catID'][$asin]) ? $_POST['catID'][$asin] : '';
			$data['category_name'] = isset($_POST['catName'][$asin]) ? $_POST['catName'][$asin] : '';
			$data['lowestprice'] = isset($_POST['lowprice'][$asin]) ? $_POST['lowprice'][$asin] : '0.0';

		} else {
			$data = array(
				'mpID' => $_MagnaSession['mpID'],
				'products_id' => $productID,
				'products_model' => $_POST['model'][$productID]
			);
			$data['asin'] = $data['item_note'] = $data['category_id'] = $data['category_name'] = $data['item_condition'] = '';
			$data['asin_type'] = $data['will_ship_internationally'] = $data['lowestprice'] = 0;
		}
		$_MagnaSession['amazonLastPreparedTS'] = array_key_exists('amazonLastPreparedTS', $_MagnaSession) ? $_MagnaSession['amazonLastPreparedTS'] : date('Y-m-d H:i:s');
		$data['PreparedTS'] = $_MagnaSession['amazonLastPreparedTS'];
		$where = (getDBConfigValue('general.keytype', '0') == 'artNr')
			? array (
				'products_model' => $data['products_model']
			)
			: array (
				'products_id' => $data['products_id']
			);
		$where['mpID'] = $_MagnaSession['mpID'];
		if (!empty($data['asin'])) {
			if (MagnaDB::gi()->recordExists(TABLE_MAGNA_AMAZON_PROPERTIES, $where)) {
				MagnaDB::gi()->update(TABLE_MAGNA_AMAZON_PROPERTIES, $data, $where);
			} else {
				$data['leadtimeToShip'] = getDBConfigValue('amazon.leadtimetoship', $_MagnaSession['mpID'], '0');
				MagnaDB::gi()->insert(TABLE_MAGNA_AMAZON_PROPERTIES, $data);
			}
			if (defined('MAGNA_DEV_PRODUCTLIST') && MAGNA_DEV_PRODUCTLIST === true && MLProduct::gi()->hasMasterItems()) {
				$sKeyType = 'products_'.((getDBConfigValue('general.keytype', '0') == 'artNr') ? 'model' : 'id');
				// if product has variants fetch id and model from master
				$aMaster = MagnaDb::gi()->fetchRow(eecho("
				    SELECT m.products_id, m.products_model
				      FROM ".TABLE_PRODUCTS." p
				INNER JOIN ".TABLE_PRODUCTS." m on p.products_master_model = m.products_model
				     WHERE     p.".$sKeyType." = '". $data[$sKeyType]."'
				           AND m.products_master_flag = '1'
				", false));
				// if a product has no variants
				if ($aMaster === false) {
					$aMaster = MagnaDb::gi()->fetchRow(eecho("
					    SELECT m.products_id, m.products_model
					      FROM ".TABLE_PRODUCTS." p
					INNER JOIN ".TABLE_PRODUCTS." m on p.products_master_model = m.products_model
					     WHERE p.".$sKeyType." = '". $data[$sKeyType]."'
					", false));
				}
				if (is_array($aMaster) && isset($aMaster['products_id'])) {
					MagnaDb::gi()->query("
						REPLACE INTO ".TABLE_MAGNA_AMAZON_PROPERTIES."
						SET
							products_id='".$aMaster['products_id']."',
							products_model='".MagnaDB::gi()->escape($aMaster['products_model'])."',
							mpID='".$_MagnaSession['mpID']."',
							asin='dummyMasterArticle',
							PreparedTS = '".$_MagnaSession['amazonLastPreparedTS']."',
							leadtimeToShip=".getDBConfigValue('amazon.leadtimetoship', $_MagnaSession['mpID'], '1')."
					");
				}
			}
		}
	}
}
