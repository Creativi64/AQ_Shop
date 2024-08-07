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
 * $Id: singlematching.php 4961 2014-12-09 14:10:12Z tim.neumann $
 *
 * (c) 2010 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */

defined('_VALID_XTC') or die('Direct Access to this location is not allowed.'); 
require_once (DIR_MAGNALISTER_INCLUDES.'lib/classes/SimplePrice.php');

define ('SHOWALLDEBUG', false);

$current_product_id = MagnaDB::gi()->fetchOne(eecho('
	SELECT pID FROM '.TABLE_MAGNA_SELECTION.'
	 WHERE mpID=\''.$_MagnaSession['mpID'].'\' AND
	       selectionname=\''.$matchingSetting['selectionName'].'\' AND
	       session_id=\''.session_id().'\'
	 LIMIT 1
', SHOWALLDEBUG));




MLProduct::gi()->setLanguage(getDBConfigValue('ebay.lang', $_MagnaSession['mpID']));
$productsData = MLProduct::gi()->getProductById($current_product_id);
$ebayProperties = MagnaDB::gi()->fetchRow(eecho('
	SELECT * FROM '.TABLE_MAGNA_EBAY_PROPERTIES.' 
	 WHERE mpID=\''.$_MagnaSession['mpID'].'\' AND
	       '.((getDBConfigValue('general.keytype', '0') == 'artNr')
				? 'products_model=\''.MagnaDB::gi()->escape($productsData['ProductsModel']).'\''
				: 'products_id = '.$current_product_id
			).'
	 LIMIT 1
', SHOWALLDEBUG));


$sprice = new SimplePrice(
$productsData['Price'], 
getCurrencyFromMarketplace($_MagnaSession['mpID'])
);
$tax = SimplePrice::getTaxByClassID($productsData['TaxClass']);
$sprice->addTax($tax)->calculateCurr()->roundPrice();

if (!empty($ebayProperties) && !empty($ebayProperties['ePID'])) {
	$productDetails = $ebayProperties;

} else {
	$productDetails['products_id'] = $current_product_id;
	$productDetails['products_model'] = $productsData['ProductsModel'];
	$productDetails['ePID'] = '';
	$productDetails['ePID_type'] = '';
	$productDetails['item_condition'] = getDBConfigValue('ebay.itemCondition', $_MagnaSession['mpID']);
	$productDetails['will_ship_internationally'] = getDBConfigValue('ebay.internationalShipping', $_MagnaSession['mpID']);
	$productDetails['item_note'] = '';
	
	if (defined('DEVELOPMENT_TEST')) {
		$productDetails['item_note'] = DEVELOPMENT_TEST;
	}

}

$searchResults = ebayPerformItemSearch(
	trim($productDetails['ePID']),
	trim($productsData[MAGNA_FIELD_PRODUCTS_EAN]),
	'',
	trim($productsData['Title']),
	''
);


$charLimit = 2000;

$productsData['Description'] = stripEvilBlockTags($productsData['Description']);
$productsData['Description'] = magnalisterIsUTF8($productsData['Description'])
	? $productsData['Description']
	: utf8_encode($productsData['Description']);
$productsData['ProductsModel'] = magnalisterIsUTF8($productsData['ProductsModel'])
	? $productsData['ProductsModel']
	: utf8_encode($productsData['ProductsModel']);
$manufacturerName = $productsData['Manufacturer'];

if (isset($productsData['Variations']) && !empty($productsData['Variations'])) {
    $products = array();
    $blKeytypeIsArtnr = (getDBConfigValue('general.keytype', '0') == 'artNr');
    $ePIDsForVariationsByKey = getEpidsForVariationsByKey($current_product_id, $productsData['ProductsModel']);
#echo print_m($ePIDsForVariationsByKey, '$ePIDsForVariationsByKey');
    if ($ePIDsForVariationsByKey != false) {
	foreach ($productsData['Variations'] as &$v) {
	    if ($blKeytypeIsArtnr) $v['ePID'] = $ePIDsForVariationsByKey[$v['MarketplaceSku']];
	    else $v['ePID'] = $ePIDsForVariationsByKey[$v['MarketplaceId']];
	}
    }
    foreach ($productsData['Variations'] as $var) {
	$attrs = ' (';
	foreach ($var['Variation'] as $attr) {
	    $attrs .= $attr['Name'].': '.$attr['Value'].', ';
	}
	$attrs = rtrim($attrs, ', ').')';
	if (isset($var['ePID']) && ($var['ePID'] != 'newproduct')) {
		$currSearchResults = ebayPerformItemSearch($var['ePID'], '', '', '', '');
	} else {
		$currSearchResults = $searchResults;
	}
	$products[] = array (
	    'product' => array(
		'products_id' => $var['MarketplaceId'],
		'products_name' => $productsData['Title'].$attrs,
		'products_details' => array (
			'desc' => $productsData['Description'],
			'images' => '', //$productsData['products_allimages'],
			'manufacturer' => $manufacturerName,
			'model' => $var['MarketplaceSku'],
			'ean' => $var['EAN'],
			'price' => $sprice->format(),
		),
		'products_description' => json_encode($productsData['Description']),
		'products_ePID' => isset($var['ePID']) ? $var['ePID'] : ''
	    ),
	    'result' => $currSearchResults,
	);
	$attrs = '';
    }
} else {
    $products = array(array(
	'product' => array(
		'products_id' => $current_product_id,
		'products_name' => $productsData['Title'],
		'products_details' => array (
			'desc' => $productsData['Description'],
			'images' => '', //$productsData['products_allimages'],
			'manufacturer' => $manufacturerName,
			'model' => $productsData['ProductsModel'],
			'ean' => $productsData['EAN'],
			'price' => $sprice->format(),
		),
		'products_description' => json_encode($productsData['Description']),
		'products_ePID' => $productDetails['ePID']
	),
	'result' => $searchResults,
    ));
}

echo '
<h2>Single Matching</h2>
<form name="singleMatching" id="singleMatching" action="'.toURL($_url).'" method="POST" enctype="multipart/form-data" >';
	renderMatchingTable($products, getCurrencyFromMarketplace($_MagnaSession['mpID']));
echo
'<input type="hidden" name="ebayProperties[products_id]" value="'.$productsData['ProductId'].'">
<input type="hidden" name="action" value="singlematching">

<table class="actions">
	<thead><tr><th>Aktionen</th></tr></thead>
	<tbody><tr><td>
		<table><tbody><tr>
			<td class="first_child"><a href="'.toURL(array('mp' => $_MagnaSession['mpID'], 'mode' => 'prepare', 'view' => 'match')).'" title="'.ML_BUTTON_LABEL_BACK.'" class="ml-button">
				'.ML_BUTTON_LABEL_BACK.
			'</a></td>
			<td class="last_child"><input type="submit" class="ml-button" value="'.ML_EBAY_BUTTON_MATCH_PREPARE_PRODUCT.'" /></td>
		</tr></tbody></table>
	</td></tr></tbody>
</table>

</form>';
?>
