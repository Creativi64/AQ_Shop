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

function magnaHasOrderDetails($args) {
	return MagnaDB::gi()->fetchOne(
		'SELECT platform FROM '.TABLE_MAGNA_ORDERS.' WHERE orders_id=\''.MagnaDB::gi()->escape($args['oID']).'\' LIMIT 1'
	);
}

function magnaLoadOrder($oID, $mpID = false) {
	$order = MagnaDB::gi()->fetchRow('
		SELECT * FROM '.TABLE_MAGNA_ORDERS.'
		 WHERE orders_id=\''.$oID.'\'
		 	   '.(($mpID != false) 
		 	   		? 'AND mpID=\''.$mpID.'\'' 
		 	   		: ''
		 	   ).'
	');
	# Not ours.
	if ($order === false) return false;
	$order['data'] = @unserialize($order['data']);
	if (!is_array($order['data'])) {
		$order['data'] = array();
	}
	$order['internaldata'] = @unserialize($order['internaldata']);
	if (!is_array($order['internaldata'])) {
		$order['internaldata'] = array();
	}
	return $order;
}

function magnaSaveOrder($order) {
	$order['data'] = serialize($order['data']);
	$order['internaldata'] = serialize($order['internaldata']);
	MagnaDB::gi()->update(TABLE_MAGNA_ORDERS, $order, array(
		'orders_id' => $order['orders_id']
	));
}

function magnaRenderOrderPlatformIcon($args) {
	$order = magnaLoadOrder(MagnaDB::gi()->escape($args['oID']));
	if ($order === false) {
		return '';
	}
	if (($order['platform'] == 'amazon')) {
		$fulfillment = $order['internaldata']['FulfillmentChannel'];
		if ($fulfillment === 'MFN-Prime') {
			$order['platform'] .= '_prime';
			if (isset($order['internaldata']['ShipServiceLevel'])) {
                $sShipServiceLevel = $order['internaldata']['ShipServiceLevel'];
                if ($sShipServiceLevel === 'NextDay') {
                    $order['platform'] .= '_nextday';
                } else if ($sShipServiceLevel === 'SameDay') {
                    $order['platform'] .= '_sameday';
                } else if ($sShipServiceLevel === 'SecondDay') {
                    $order['platform'] .= '_secondday';
                }
            }
		} elseif ($fulfillment === 'Business') {
			$order['platform'] .= '_business';
		} elseif ($fulfillment !== 'MFN') {
			$order['platform'] .= '_fba';

            if (isset($order['internaldata']['IsBusinessOrder']) && $order['internaldata']['IsBusinessOrder'] == 'true') {
                $order['platform'] .= '_business';
            }
		}
	}
	if (($order['platform'] == 'ebay') && (array_key_exists('eBayPlus', $order['data']))){
		$order['platform'] .= '_plus';
	}
	return ' style="
		background-image: url(includes/magnalister/images/logos/'.$order['platform'].'_orderview.png);
		background-repeat: no-repeat;
		background-position: 99% 60%;"';
}

function magnaRenderOrderDetails($args) {
	/* Description of Modules */
	include(DIR_MAGNALISTER_INCLUDES.'modules.php');
	$details = magnaLoadOrder(MagnaDB::gi()->escape($args['oID']));
	if ($details === false) {
		return '';
	}
	
	$mpIcon = $details['platform'];
	if (($details['platform'] == 'amazon') && ($details['internaldata']['FulfillmentChannel'] != 'MFN')){
		$mpIcon .= '_fba';
	}
	
	$html = '
		<style type="text/css">
div.magnaOrderHeadline {
	color: #000000;
	font: 14px sans-serif;
	font-weight: bold;
}
div.magnaOrderHeadline span {
	color: #DC043D;
}
div.magnaOrderHeadline img {
	vertical-align: middle;
}
table.magnaOrderDetails {
	width: 100%;
	font: 11px sans-serif;
	border: 1px solid #999;
	margin: 0.5em 0 2em 0;
	border-collapse: separate;
	border-spacing: 1px;
}
table.magnaOrderDetails tbody tr td {
	padding: 4px 6px;
}
table.magnaOrderDetails tbody tr.odd td {
	background: #eaeaea;
	border-top: 1px solid #e0e0e0;
	border-bottom: 1px solid #e0e0e0;
}
table.magnaOrderDetails tbody tr.even td {
	background: #fff;
	border-top: 1px solid #fafafa;
	border-bottom: 1px solid #fafafa;
}
table.magnaOrderDetails tbody tr td.key {
	white-space: nowrap;
	width: 15em;
}
		</style>
		<div class="magnaOrderHeadline">
			<span>m</span>agnalister Details
			<img src="includes/magnalister/images/logos/'.$mpIcon.'_orderview.png" alt="'.$mpIcon.'">
		</div>
		<table class="magnaOrderDetails"><tbody>';
		$isOdd = true;
		foreach ($details['data'] as $key => $value) {
			if (defined($key)) $key = constant($key);
			if (defined($value)) {
				if ($value == 'ML_GENERIC_ORDER_THROUGH_COMPARISON_SHOPPING') {
					if (array_key_exists($details['platform'], $_modules)) {
						$title = $_modules[$details['platform']]['title'];
					} else {
						$title = ML_LABEL_UNKNOWN;
					}
					$value = sprintf(constant($value), $title);
				} else {
					$value = constant($value);
				}
			}
	
			$html .= '
			<tr class="'.(($isOdd = !$isOdd) ? 'odd' : 'even').'">
				<td class="key">'.$key.'</td>
				<td class="value">'.$value.'</td>
			</tr>';
		}
	$html .= '
		</tbody></table>';
	
	return $html;
}

function magnaInsertOrderDetails($args) {
	global $magnaConfig;
	
	$modules = array_unique(array_values($magnaConfig['maranon']['Marketplaces']));
	if (!array_key_exists('magnalister', $_SESSION) || !is_array($_SESSION['magnalister'])
		|| !array_key_exists('camefrom', $_SESSION['magnalister']) || !is_array($_SESSION['magnalister']['camefrom'])
	) {
		return;
	}
	$info = $_SESSION['magnalister']['camefrom'];
	$boughtItems = magnaExecute('magnaGetCartContents', array(), array('inventoryUpdate.php'));

	MagnaDB::gi()->insert(TABLE_MAGNA_ORDERS, array(
		'orders_id' => $args['oID'],
		'data' => serialize(array(
			'ML_LABEL_NOTE' => 'ML_GENERIC_ORDER_THROUGH_COMPARISON_SHOPPING',
		)),
		'platform' => $_SESSION['magnalister']['camefrom']['Marketplace'],
		'mpID' => $_SESSION['magnalister']['camefrom']['MarketplaceID']
	), true);
	
	$tmpBI = array();
	if (!empty($boughtItems)) {
		foreach ($boughtItems as $pID => $detail) {
			$tmpBI[] = array(
				'SKU' => magnaPID2SKU($pID),
				'Quantity' => $detail['Quantity'],
				'Price' => $detail['Price'],
				'Currency' => $detail['Currency'],
			);
		}
	}

	$data = array (
		'OrderID' => $args['oID'],
		'Marketplace' => $info['Marketplace'],
		'MarketplaceID' => $info['MarketplaceID'],
		'IP' => $info['IP'],
		'BoughtItems' => $tmpBI,
		'DateTime' => $info['DateTime'],
	);
	#echo print_m($data);
	try {
		$res = MagnaConnector::gi()->submitRequest(array(
			'ACTION' => 'CollectOrderStats',
			'SUBSYSTEM' => 'Core',
			'DATA' => $data
		));
	} catch (MagnaException $e) {
		if ($e->getCode() == MagnaException::TIMEOUT) {
			$e->saveRequest();
			$e->setCriticalStatus(false);
		}
		$res = $e->getErrorArray();
	}
	if (MAGNA_CALLBACK_MODE == 'STANDALONE') {
	#	echo print_m($res);
	}

}

function magnaSubmitOrderStatus($args) {
/* 
Bei Statusaenderung in Detailansicht:
$_POST = array (
    'comments' => '',
    'status' => '3',
    'notify' => 'on',
	'magna' => array (
		'trackingcode' => '',
		'carriercode' => 'Postbote',
	),
);
$_GET = array (
    'page' => '1',
    'oID' => '400289',
    'action' => 'update_order',
);

# Bei $args['action'] == 'gm_send_order'
$_POST = array();
$_GET = array (
    'oID' => '400289',
    'type' => 'order',
);

# Bei $argsv == array()
# InfoBox: "Bestellstatus f�r mehrere Bestellungen gleichzeitig �ndern"
$args = array ();
$_GET = array (
  'page' => '1',
  'oID' => '400289',
  'action' => 'gm_multi_status',
);
$_POST = array (
  'gm_multi_status' => array (
    0 => '400247',
    1 => '400246',
    2 => '400253',
    3 => '400262',
    4 => '400213',
  ),
  'XTCsid' => '87i0ivoimfm7ivt05n07p6jla6',
  'action' => 'gm_multi_status',
  'page' => '2',
  'gm_status' => '3',
  'magna' => array (
    'trackingcode' => '',
    'carriercode' => 'Postbote',
  ),
  'gm_comments' => '',
);
*/

/*
	echo var_export_pre($args, '$args');
	echo var_export_pre($_GET, '$_GET');
	echo var_export_pre($_POST, '$_POST');
*/
	if (isset($_POST['action']) && ($_POST['action'] == 'gm_multi_status') 
		&& isset($_GET['oID'])
	) {
		# MultiConfirm
		if (!array_key_exists('gm_multi_status', $_POST) 
			|| !is_array($_POST['gm_multi_status']) 
			|| empty($_POST['gm_multi_status'])
		) return '';

		if (!ctype_digit($_POST['gm_status'])) return '';

		$oID = MagnaDB::gi()->escape($_GET['oID']);
		$order = magnaLoadOrder($oID);
		if ($order === false) return '';

		$orderIDs = $_POST['gm_multi_status'];
		
		$incl = DIR_MAGNALISTER_MODULES.$order['platform'].'/'.$order['platform'].'Functions.php';
		if (!file_exists($incl)) return '';
		require_once($incl);

		$func = $order['platform'].'ProcessMultiOrderStatus';
		if (!function_exists($func)) return '';
		$doFunc = $order['platform'].'DoOrderStatusSyncByTigger';
		if (!function_exists($doFunc)) return '';

		loadDBConfig($order['mpID']);

		if (!$doFunc($order['mpID'])) {
			return '';
		}

		$orders = array($order['orders_id'] => $order);
		foreach ($orderIDs as $oID) {
			$oID = MagnaDB::gi()->escape($oID);
			$tO = magnaLoadOrder($oID, $order['mpID']);
			if ($tO === false) continue;
			$orders[$oID] = $tO;
		}

		$nuArgs = array(
			'orders' => $orders,
			'status' => $_POST['gm_status'],
		);
		return $func($nuArgs);
		#echo var_export_pre($nuArgs, $func.'($nuArgs)');
		#die();

	} else if (isset($_GET['oID']) && (isset($_POST['status']) || isset($_GET['type']))) {
		# SingleConfirm
		$oID = MagnaDB::gi()->escape($_GET['oID']);
		$order = magnaLoadOrder($oID);
		if ($order === false) return '';

		$incl = DIR_MAGNALISTER_MODULES.$order['platform'].'/'.$order['platform'].'Functions.php';
		if (!file_exists($incl)) return '';
		require_once($incl);

		$func = $order['platform'].'ProcessSingleOrderStatus';
		if (!function_exists($func)) return '';
		$doFunc = $order['platform'].'DoOrderStatusSyncByTigger';
		if (!function_exists($doFunc)) return '';

		loadDBConfig($order['mpID']);

		if (!$doFunc($order['mpID'])) {
			return '';
		}

		$status = false;
		if (isset($_POST['status'])) {
			$status = $_POST['status'];
		} else if (isset($_GET['type']) && (($_GET['type'] == 'cancel') || ($_GET['type'] == 'order'))) {
			$status = $_GET['type'];
		}
		if ($status === false) return '';

		$nuArgs = array(
			'order' => $order,
			'status' => $status,
		);
		return $func($nuArgs);
		#echo var_export_pre($nuArgs, $func.'($nuArgs)');
		#die();

	}

}

function magnaRenderOrderStatusSync($args) {
	$html = '';
	if (isset($_GET['oID'])) {
		$oID = MagnaDB::gi()->escape($_GET['oID']);
		$order = magnaLoadOrder($oID);
		#echo print_m($order, '$order');
		if ($order === false) return '';

		$incl = DIR_MAGNALISTER_MODULES.$order['platform'].'/'.$order['platform'].'Functions.php';
		if (!file_exists($incl)) return '';
		require_once($incl);

		$func = $order['platform'].'RenderOrderStatusSync';
		if (!function_exists($func)) return '';
		$doFunc = $order['platform'].'DoOrderStatusSyncByTigger';
		if (!function_exists($doFunc)) return '';

		loadDBConfig($order['mpID']);

		if (!$doFunc($order['mpID'])) {
			return '';
		}

		$view = 'orderDetail';
		if (array_key_exists('multi', $args) && (bool)$args['multi']) {
			$view = 'orderDetailMulti';
		}

		$html .= $func(array(
			'order' => $order,
			'view' => $view,
		));
	}
	echo $html.'';
}
