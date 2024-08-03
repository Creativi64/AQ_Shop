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
 * $Id: ebayFunctions.php 167 2013-02-08 12:00:00Z tim.neumann $
 *
 * (c) 2010 - 2013 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */

defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');

require_once(DIR_MAGNALISTER_INCLUDES.'lib/classes/SimplePrice.php');
require_once(DIR_MAGNALISTER_INCLUDES.'lib/classes/ShopAddOns.php');
require_once(DIR_MAGNALISTER_MODULES.'ebay/EbayHelper.php');

# eBay gibt GMT-Zeit zurueck,
# Format wie '2011-01-07T22:07:23.174Z',
# mache universellen Unix Timestamp daraus
function eBayTimeToTs($eBayTime) {
    return gmmktime(
        substr($eBayTime, 11, 2), substr($eBayTime, 14, 2), substr($eBayTime, 17, 2),
        substr($eBayTime, 5, 2), substr($eBayTime, 8, 2), substr($eBayTime, 0, 4)
    );
}

function ebayPerformItemSearch($epid = '', $ean = '', $mpn = '', $productsName = '', $keywords = '') {
    $searchResults = array();
    if (!empty($epid) && ($epid != 'variations')) {
        try {
            $result = MagnaConnector::gi()->submitRequest(array(
                'ACTION' => 'ItemLookup',
                'EPID'   => $epid
            ));
            if (!empty($result['DATA'])) {
                $searchResults = array_merge($searchResults, $result['DATA']);
            }
        } catch (MagnaException $e) {
            $e->setCriticalStatus(false);
        }
        return $searchResults;
    }
    $ean = str_replace(array(' ', '-'), '', $ean);
    $aRequest = array(
        'ACTION' => 'ItemSearch'
    );
    if (!empty($ean))
        $aRequest['EAN'] = $ean;
    if (!empty($mpn))
        $aRequest['MPN'] = $mpn;
    if (!empty($productsName))
        $aRequest['NAME'] = $productsName;
    if (!empty($keywords))
        $aRequest['KEYWORDS'] = $keywords;
    if (count($aRequest) > 1) {
        try {
            $result = MagnaConnector::gi()->submitRequest($aRequest);
            if (!empty($result['DATA'])) {
                $searchResults = array_merge($searchResults, $result['DATA']);
            }
        } catch (MagnaException $e) {
            $e->setCriticalStatus(false);
        }
    }
    return $searchResults;
}

function updateEbayInventoryByEdit($mpID, $updateData) {
    $updateItem = genericInventoryUpdateByEdit($mpID, $updateData);
    if (!is_array($updateItem)) {
        return false;
    }
    $pID = array_first(array_keys($updateData));

    $requestMode = ('SET' == $updateItem['NewQuantity']['Mode']) ? 'NewQuantity' : 'AddQuantity';
    $QtyToSubmit = ('SUB' == $updateItem['NewQuantity']['Mode']) ? -1 * $updateItem['NewQuantity']['Value'] : $updateItem['NewQuantity']['Value'];
    $updateData = array(
        'SKU'               => $updateItem['SKU'],
        "$requestMode"      => (int)$QtyToSubmit,
        'fixed.stocksync'   => getDBConfigValue('ebay.stocksync.tomarketplace', $mpID, ''),
        'chinese.stocksync' => getDBConfigValue('ebay.chinese.stocksync.tomarketplace', $mpID, ''),
    );
    $request = array(
        'ACTION'        => 'UpdateQuantity',
        'SUBSYSTEM'     => 'eBay',
        'MARKETPLACEID' => $mpID,
        'DATA'          => $updateData
    );

    if (defined('MAGNA_ECHO_UPDATE') && MAGNA_ECHO_UPDATE) {
        echo print_m($request, __FUNCTION__);
        return true;
    }
    // we dont wait for answer
    MagnaConnector::gi()->setTimeOutInSeconds(1);
    try {
        $result = MagnaConnector::gi()->submitRequest($request);
        #echo print_m($result, '$result');
    } catch (MagnaException $e) {
        if ($e->getCode() == MagnaException::TIMEOUT) {
            //$e->saveRequest();//if there is a really problem cron shold make it
            $e->setCriticalStatus(false);
        }
        #echo print_m($e->getErrorArray(), '$error');
    }
    MagnaConnector::gi()->resetTimeOut();
}

function updateEbayInventoryByOrder($mpID, $boughtItems, $subRelQuant = true) {
    global $_MagnaSession;
    if (!isset($_MagnaSession) || !is_array($_MagnaSession)) {
        $_MagnaSession = array('mpID' => $mpID);
    } else if (!isset($_MagnaSession['mpID'])) {
        $_MagnaSession['mpID'] = $mpID;
    }
    $ess = getDBConfigValue('ebay.stocksync.tomarketplace', $mpID, 'no');
    $ecss = getDBConfigValue('ebay.chinese.stocksync.tomarketplace', $mpID, 'no');
    $syncPrice = getDBConfigValue('ebay.inventorysync.price', $mpID, null) == 'auto';
    $syncPriceC = getDBConfigValue('ebay.chinese.inventorysync.price', $mpID, null) == 'auto';

    if (($ess == 'no') && ($ecss == 'no')) {
        return;
    }
    $data = genericInventoryUpdateByOrder($mpID, $boughtItems, $subRelQuant);
    foreach ($data as $i => $updateItem) {
        $requestMode = ('SET' == $updateItem['NewQuantity']['Mode']) ? 'NewQuantity' : 'AddQuantity';
        $QtyToSubmit = ('SUB' == $updateItem['NewQuantity']['Mode']) ? -1 * $updateItem['NewQuantity']['Value'] : $updateItem['NewQuantity']['Value'];
        $updateData = array(
            'SKU'               => $updateItem['SKU'],
            "$requestMode"      => (int)$QtyToSubmit,
            'fixed.stocksync'   => $ess,
            'chinese.stocksync' => $ecss,
        );
        $pID = magnaSKU2pID($updateItem['SKU'], true);
        # schauen ob Preis eingefroren
        $priceFrozenQuery = 'SELECT Price FROM '.TABLE_MAGNA_EBAY_PROPERTIES
            .' WHERE mpID = '.$mpID.' AND ';
        if ('artNr' == getDBConfigValue('general.keytype', '0'))
            $priceFrozenQuery .= ' products_model = \''.MagnaDB::gi()->escape($updateItem['SKU']).'\'';
        else
            $priceFrozenQuery .= ' products_id = '.$pID;
        $priceFrozen = MagnaDB::gi()->fetchOne($priceFrozenQuery);
        if (0.0 == $priceFrozen)
            $priceFrozen = false;
        /*$variationMatrix = getVariations($pID, null, true, $syncPrice && !$priceFrozen);
        $totalQuantity = makeQuantity($pID);
        if (false != $variationMatrix) {
            # mode ist immer SUB (kommt so aus magnaInventoryUpdateByOrder)
            setVariationQuantity($variationMatrix, $pID, $updateItem['Attributes'], $boughtItems[$i]['NewQuantity']['Value'], 'SUB');
            # wenn Variantions da, hat Anzahl keine Bedeutung, entscheidend ist die variationMatrix
            # es darf dann aber nicht sein dass 0 uebergeben wird, denn das wuerde EndItem ausloesen
            unset($updateData["$requestMode"]);
            $updateData['NewQuantity'] = (int)$totalQuantity;
        }
        $updateData['Variations'] = $variationMatrix;*/
        try {
            $request = array(
                'ACTION'        => 'UpdateQuantity',
                'SUBSYSTEM'     => 'eBay',
                'MARKETPLACEID' => $mpID,
                'DATA'          => $updateData,
            );
            if (defined('MAGNA_ECHO_UPDATE') && MAGNA_ECHO_UPDATE) {
                echo print_m($request, __FUNCTION__);
            } else {
                $result = MagnaConnector::gi()->submitRequest($request);
                #echo print_m($result, '$result');
            }
        } catch (MagnaException $e) {
            /* don't show errors for now. should be saved in the errorlog instead. */
            $e->setCriticalStatus(false);
            /* Do NOT save the request incase of timeout. Since this is a synchronous
             * call to ebay it may take up to 9 seconds before we receive a reply.
             * The reply isn't important anyway (as it isn't processed anyway) so no need
             * to repeat the request later on.
             */
            /*
            if ($e->getCode() == MagnaException::TIMEOUT) {
                $e->saveRequest();
            }
            */
            #echo print_m($e->getErrorArray(), '$error');
        }
    }
}

function geteBayShippingDetails() {
    global $_MagnaSession;

    $mpID = $_MagnaSession['mpID'];
    $site = getDBConfigValue('ebay.site', $mpID);

    initArrayIfNecessary($_MagnaSession, array($mpID, $site, 'eBayShippingDetails'));

    if (!empty($_MagnaSession[$mpID][$site]['eBayShippingDetails'])) {
        return $_MagnaSession[$mpID][$site]['eBayShippingDetails'];
    }
    try {
        $shippingDetails = MagnaConnector::gi()->submitRequest(array(
            'ACTION' => 'GetShippingServiceDetails',
            'DATA'   => array('Site' => $site),
        ));
        $shippingDetails = $shippingDetails['DATA'];
    } catch (MagnaException $e) {
        return false;
    }
    unset($shippingDetails['Version']);
    unset($shippingDetails['Timestamp']);
    unset($shippingDetails['Site']);
    foreach ($shippingDetails['ShippingServices'] as &$service) {
        $service['Description'] = fixHTMLUTF8Entities($service['Description']);
    }
    foreach ($shippingDetails['ShippingLocations'] as &$location) {
        $location = fixHTMLUTF8Entities($location);
    }
    $_MagnaSession[$mpID][$site]['eBayShippingDetails'] = $shippingDetails;
    return $_MagnaSession[$mpID][$site]['eBayShippingDetails'];
}

function geteBayShippingServicesList() {
    $shippingDetails = geteBayShippingDetails();
    $servicesList = array();
    return $servicesList;
}

function geteBayLocalShippingServicesList() {
    $shippingDetails = geteBayShippingDetails();
    $servicesList = array();
    foreach ($shippingDetails['ShippingServices'] as $service => $serviceData) {
        if ('1' == $serviceData['InternationalService'])
            continue;
        #	$servicesList["$service"] = utf8_decode($serviceData['Description']);
        $servicesList["$service"] = $serviceData['Description'];
    }
    return $servicesList;
}

function geteBayInternationalShippingServicesList() {
    $shippingDetails = geteBayShippingDetails();
    $servicesList = array('' => ML_EBAY_LABEL_NO_INTL_SHIPPING);
    foreach ($shippingDetails['ShippingServices'] as $service => $serviceData) {
        if ('0' == $serviceData['InternationalService'])
            continue;
        #	$servicesList["$service"] = utf8_decode($serviceData['Description']);
        $servicesList["$service"] = $serviceData['Description'];
    }
    return $servicesList;
}

function geteBayShippingLocationsList() {
    $shippingDetails = geteBayShippingDetails();
    return $shippingDetails['ShippingLocations'];
}

function geteBayShippingDiscountProfiles($forceRefresh = false) {
    global $_MagnaSession;
    $mpID = $_MagnaSession['mpID'];
    initArrayIfNecessary($_MagnaSession, array($mpID, 'eBayShippingDiscountProfiles'));
    if ($forceRefresh)
        unset($_MagnaSession[$mpID]['eBayShippingDiscountProfiles']);

    $storedProfileData = getDBConfigValue('ebay.shippingprofiles', $mpID);
    if (!empty($storedProfileData)) {
        if (($storedProfileData['Timestamp'] < time() - 60) || $forceRefresh)
            unset($storedProfileData);
    }

    if (!empty($_MagnaSession[$mpID]['eBayShippingDiscountProfiles'])) {
        return $_MagnaSession[$mpID]['eBayShippingDiscountProfiles'];
    }
    if (empty($storedProfileData)) {
        try {
            $shippingDiscountProfiles = MagnaConnector::gi()->submitRequest(array(
                'ACTION' => 'GetShippingDiscountProfiles'
            ));
        } catch (MagnaException $e) {
            return false;
        }
        $shippingDiscountProfiles['DATA']['Timestamp'] = time();
        setDBConfigValue('ebay.shippingprofiles', $mpID, $shippingDiscountProfiles['DATA'], true);
        $storedProfileData = $shippingDiscountProfiles['DATA'];
    }
    $profiles = array(0 => '&nbsp; &mdash; &nbsp;');
    $myPrice = new SimplePrice(null,
        (getCurrencyFromMarketplace($_MagnaSession['mpID'])
            ? getCurrencyFromMarketplace($_MagnaSession['mpID'])
            : DEFAULT_CURRENCY)
    );
    # aufbereiten
    if (array_key_exists('Profiles', $storedProfileData)) {
        foreach ($storedProfileData['Profiles'] as $key => $profile) {
            if (empty($profile['ProfileName']))
                $profile['ProfileName'] = $key;
            $profiles[$key] = $profile['ProfileName'].' ('.$myPrice->setPrice($profile['EachAdditionalAmount'])->format().' '.ML_EBAY_LABEL_EACH_ONE_MORE.')';
        }
    }
    $_MagnaSession[$mpID]['eBayShippingDiscountProfiles'] = $profiles;
    return $_MagnaSession[$mpID]['eBayShippingDiscountProfiles'];
}

/* Business Policies / Rahmenbedingungen */
function geteBayBusinessPolicies($forceRefresh = false) {

    global $_MagnaSession;
    $mpID = $_MagnaSession['mpID'];
    initArrayIfNecessary($_MagnaSession, array($mpID, 'eBaySellerPaymentProfiles'));
    initArrayIfNecessary($_MagnaSession, array($mpID, 'eBaySellerShippingProfiles'));
    initArrayIfNecessary($_MagnaSession, array($mpID, 'eBaySellerReturnProfiles'));
    if ($forceRefresh) {
        unset($_MagnaSession[$mpID]['eBaySellerPaymentProfiles']);
        unset($_MagnaSession[$mpID]['eBaySellerShippingProfiles']);
        unset($_MagnaSession[$mpID]['eBaySellerReturnProfiles']);
    }

    $storedProfileData = getDBConfigValue('ebay.sellerprofiles', $mpID);
    if (!empty($storedProfileData)) {
        if (($storedProfileData['Timestamp'] < time() - 86400) || $forceRefresh)
            unset($storedProfileData);
    }
    if (empty($storedProfileData)) {
        try {
            $sellerProfiles = MagnaConnector::gi()->submitRequest(array(
                'ACTION' => 'GetSellerProfiles',
        	'SUBSYSTEM'     => 'eBay',
            ));
        } catch (MagnaException $e) {
            return false;
        }
        $sellerProfiles['DATA']['Timestamp'] = time();
        setDBConfigValue('ebay.sellerprofiles', $mpID, $sellerProfiles['DATA'], true);
        $storedProfileData = $sellerProfiles['DATA'];
    }
    if (count($storedProfileData) < 2)
        return false; // if only Timestamp is filled
    $storedProfileContentsData = getDBConfigValue('ebay.sellerprofile.contents', $mpID);
    if (!empty($storedProfileContentsData)) {
        if (($storedProfileContentsData['Timestamp'] < time() - 86400) || $forceRefresh)
            unset($storedProfileContentsData);
    }
    if (empty($storedProfileContentsData)) {
        try {
            $sellerProfileContents = MagnaConnector::gi()->submitRequest(array(
                'ACTION' => 'GetSellerProfileContents',
        	'SUBSYSTEM'     => 'eBay',
            ));
        } catch (MagnaException $e) {
            return false;
        }
        $sellerProfileContents['DATA']['Timestamp'] = time();
        MagnaDB::gi()->setEscapeStrings(false);// disabling magic-quotes-config...dont comes from request
        setDBConfigValue('ebay.sellerprofile.contents', $mpID, $sellerProfileContents['DATA'], true);
        MagnaDB::gi()->setEscapeStrings();// default behavior for escape strings
        $storedProfileContentsData = $sellerProfileContents['DATA'];
    }

    # aufbereiten
    if (array_key_exists('Profiles', $storedProfileData)) {
        # drop defaults, if outdated
        if (!array_key_exists(getDBConfigValue('ebay.default.paymentsellerprofile', $mpID, 0), $storedProfileData['Profiles'])) {
            setDBConfigValue('ebay.default.paymentsellerprofile', $mpID, 0, true);
        }
        if (!array_key_exists(getDBConfigValue('ebay.default.shippingsellerprofile', $mpID, 0), $storedProfileData['Profiles'])) {
            setDBConfigValue('ebay.default.shippingsellerprofile', $mpID, 0, true);
        } 
        if (!array_key_exists(getDBConfigValue('ebay.default.returnsellerprofile', $mpID, 0), $storedProfileData['Profiles'])) {
            setDBConfigValue('ebay.default.returnsellerprofile', $mpID, 0, true);
        }
        foreach ($storedProfileData['Profiles'] as $key => $profile) {
            switch ($profile['ProfileType']) {
                case ('PAYMENT'):
                {
                    $_MagnaSession[$mpID]['eBaySellerPaymentProfiles'][$key] = $profile['ProfileName'];
                    if ('true' === $profile['IsDefault']) {
                        $_MagnaSession[$mpID]['eBaySellerPaymentProfiles'][$key] .= ' '.ML_OPTION_ADDTEXT_DEFAULT;
                        if (0 == getDBConfigValue('ebay.default.paymentsellerprofile', $mpID, 0)) {
                            setDBConfigValue('ebay.default.paymentsellerprofile', $mpID, $key, true);
                        }
                    }
                    break;
                }
                case ('SHIPPING'):
                {
                    $_MagnaSession[$mpID]['eBaySellerShippingProfiles'][$key] = $profile['ProfileName'];
                    if ('true' === $profile['IsDefault']) {
                        $_MagnaSession[$mpID]['eBaySellerShippingProfiles'][$key] .= ' '.ML_OPTION_ADDTEXT_DEFAULT;
                        if (0 == getDBConfigValue('ebay.default.shippingsellerprofile', $mpID, 0)) {
                            setDBConfigValue('ebay.default.shippingsellerprofile', $mpID, $key, true);
                        }
                    }
                    break;
                }
                case ('RETURN_POLICY'):
                {
                    $_MagnaSession[$mpID]['eBaySellerReturnProfiles'][$key] = $profile['ProfileName'];
                    if ('true' === $profile['IsDefault']) {
                        $_MagnaSession[$mpID]['eBaySellerReturnProfiles'][$key] .= ' '.ML_OPTION_ADDTEXT_DEFAULT;
                        if (0 == getDBConfigValue('ebay.default.returnsellerprofile', $mpID, 0)) {
                            setDBConfigValue('ebay.default.returnsellerprofile', $mpID, $key, true);
                        }
                    }
                    break;
                }
                default:
                    break;
            }
        }
        # Fall: Kein Profil als default ausgewählt
        foreach (array(
                     'ebay.default.paymentsellerprofile' => 'PAYMENT',
                     'ebay.default.shippingsellerprofile' => 'SHIPPING',
                     'ebay.default.returnsellerprofile' => 'RETURN_POLICY',
                 ) as $mkey => $sProfileType) {
            if (0 == getDBConfigValue($mkey, $mpID, 0)) {
                foreach ($storedProfileData['Profiles'] as $key => $profile) {
                    if ($sProfileType == $profile['ProfileType']) {
                        setDBConfigValue($mkey, $mpID, $key, true);
                        break;
                    }
                }
            }
        }
    }
    return true;
}

function geteBaySellerProfiles($forceRefresh = false, $sKind) {
    global $_MagnaSession;
    $mpID = $_MagnaSession['mpID'];
    if (('Payment' != $sKind)
        && ('Shipping' != $sKind)
        && ('Return' != $sKind)
    ) {
        return '';
    }
    initArrayIfNecessary($_MagnaSession, array($mpID, 'eBaySeller'.$sKind.'Profiles'));
    if ($forceRefresh)
        geteBayBusinessPolicies($forceRefresh);
    return $_MagnaSession[$mpID]['eBaySeller'.$sKind.'Profiles'];
}

function geteBaySellerPaymentProfiles($forceRefresh = false) {
    return geteBaySellerProfiles($forceRefresh, 'Payment');
}

function geteBaySellerShippingProfiles($forceRefresh = false) {
    return geteBaySellerProfiles($forceRefresh, 'Shipping');
}

function geteBaySellerReturnProfiles($forceRefresh = false) {
    return geteBaySellerProfiles($forceRefresh, 'Return');
}

/* Helper function for config + prepare */
function eBayGetSellerProfileData($sProfile) {
    global $_MagnaSession;
    $aSellerProfiles = getDBConfigValue('ebay.sellerprofile.contents', $_MagnaSession['mpID'], false);
    if (empty($aSellerProfiles))
        return;
    if (array_key_exists($sProfile, $aSellerProfiles['Payment'])) {
        echo json_encode($aSellerProfiles['Payment'][$sProfile]);
        return;
    } else if (array_key_exists($sProfile, $aSellerProfiles['Return'])) {
        echo json_encode($aSellerProfiles['Return'][$sProfile]);
        return;
    } else if (array_key_exists($sProfile, $aSellerProfiles['Shipping'])) {
        $aSellerProfiles['Shipping'][$sProfile]['ebay_default_shipping_local'] = renderReadonlyShippingDetails($aSellerProfiles['Shipping'][$sProfile]['shipping.local'], false);
        $aSellerProfiles['Shipping'][$sProfile]['ebay_default_shipping_international'] = renderReadonlyShippingDetails($aSellerProfiles['Shipping'][$sProfile]['shipping.international'], true);
        echo json_encode($aSellerProfiles['Shipping'][$sProfile]);
        return;
    } else {
        echo "EMPTY\n"; // show something to prevent eternal waiting for the ajax response
        return;
    }
}

/* Helper function for config + prepare */
function renderReadonlyShippingDetails($aDetails, $blInt = false) {
    global $_MagnaSession;
    $sp = new SimplePrice(null, getDBConfigValue('ebay.currency', $_MagnaSession['mpID']));
    $html = '';
    $sLocalOrInt = ($blInt ? 'international' : 'local');
    if ($blInt) {
        $aServiceList = geteBayInternationalShippingServicesList();
        $aLocationList = geteBayShippingLocationsList();
    } else {
        $aServiceList = geteBayLocalShippingServicesList();
    }
    foreach ($aDetails as $i => $aDetail) {
        $html .= '  <tr class="row1">
    <td class="paddingRight"><input type="text" name="conf[ebay.default.shipping.'.$sLocalOrInt.']['.$i.'][service]" value="'.$aServiceList[$aDetail['service']].'" disabled="disabled" style="background-color:lightgray" />
    <input type="hidden" name="conf[ebay.default.shipping.'.$sLocalOrInt.']['.$i.'][service]" value="'.$aDetail['service'].'" /></td>';
        $html .= '
    <td class="textright">Versandkosten:&nbsp;</td>
    <td class="paddingRight"><input type="text" name="conf[ebay.default.shipping.'.$sLocalOrInt.']['.$i.'][cost]" value="'.$sp->setPrice($aDetail['cost'])->formatWOCurrency().'" disabled="disabled" style="background-color:lightgray" />
    <input type="hidden" name="conf[ebay.default.shipping.'.$sLocalOrInt.']['.$i.'][cost]" value="'.$aDetail['cost'].'" /></td>
  </tr>';
        if (array_key_exists('location', $aDetail)) {
            $html .= '  <tr class="row1">
    <td class="paddingRight" colspan="3"><table><tr><td>';
            foreach ($aDetail['location'] as $sLoc) {
                $html .= '
      <nobr><input type="checkbox" name="conf[ebay.default.shipping.international]['.$i.'][location][]" value="'.$sLoc.'" checked="checked" disabled="disabled" style="background-color:lightgray" />&nbsp;'.$aLocationList[$sLoc].'&nbsp;</nobr>
      <input type="hidden" name="conf[ebay.default.shipping.international]['.$i.'][location][]" value="'.$sLoc.'" /><br />';
            }
            $html .= '  </td></tr></table></tr>';
        }
    }
    return $html;
}

function geteBaySiteID() {
    global $_MagnaSession;
    $mpID = $_MagnaSession['mpID'];
    if (isset($_MagnaSession[$mpID]['SiteID']))
        return $_MagnaSession[$mpID]['SiteID'];
    try {
        $baseCall = MagnaConnector::gi()->submitRequest(array(
            'ACTION' => 'GeteBayOfficialTime'
        ));
    } catch (MagnaException $e) {
        return 77;
    }
    $_MagnaSession[$mpID]['SiteID'] = $baseCall['DATA']['SiteID'];
    return $_MagnaSession[$mpID]['SiteID'];
}

function geteBayPaymentOptions() {
    global $_MagnaSession;

    #echo print_m($_MagnaSession,'$_MagnaSession');

    $mpID = $_MagnaSession['mpID'];
    $site = getDBConfigValue('ebay.site', $mpID);

    #echo print_m($site,'$site');

    if (@isset($_MagnaSession[$mpID]['eBayPaymentOptions']['Site']) &&
        ($_MagnaSession[$mpID]['eBayPaymentOptions']['Site'] == getDBConfigValue('ebay.site', $mpID, '999'))
    ) { # 999 um keine falsche Gleichheit bei nicht gesetzten Werten zu bekommen
        return $_MagnaSession[$mpID][$site]['eBayPaymentOptions'];
    } else {
        try {
            $paymentOptions = MagnaConnector::gi()->submitRequest(array(
                'ACTION' => 'GetPaymentOptions',
                'DATA'   => array('Site' => $site),
            ));
        } catch (MagnaException $e) {
            $paymentOptions = array(
                'DATA' => false
            );
        }
        if (!is_array($paymentOptions) || @empty($paymentOptions['DATA'])) {
            return false;
        }
        foreach ($paymentOptions['DATA']['PaymentOptions'] as &$option) {
            $option = fixHTMLUTF8Entities($option);
        }
    }
    $_MagnaSession[$mpID]['eBayPaymentOptions'] = $paymentOptions['DATA']['PaymentOptions'];
    return $paymentOptions['DATA']['PaymentOptions'];
}

function geteBayReturnPolicyDetails() {
    global $_MagnaSession;

    #echo print_m($_MagnaSession,'$_MagnaSession');

    $mpID = $_MagnaSession['mpID'];
    $site = getDBConfigValue('ebay.site', $mpID);

    #echo print_m($site,'$site');
    if (@isset($_MagnaSession[$mpID]['eBayReturnPolicyDetails']['Site']) &&
        ($_MagnaSession[$mpID]['eBayReturnPolicyDetails']['Site'] == getDBConfigValue('ebay.site', $mpID, '999'))
    ) { # 999 um keine falsche Gleichheit bei nicht gesetzten Werten zu bekommen
        return $_MagnaSession[$mpID][$site]['eBayReturnPolicyDetails'];
    } else {
        try {
            $returnPolicyDetails = MagnaConnector::gi()->submitRequest(array(
                'ACTION' => 'GetReturnPolicyDetails',
                'DATA'   => array('Site' => $site),
            ));
        } catch (MagnaException $e) {
            $returnPolicyDetails = array(
                'DATA' => false
            );
        }
        if (!is_array($returnPolicyDetails) || @empty($returnPolicyDetails['DATA'])) {
            return false;
        }
        arrayEntitiesFixHTMLUTF8($returnPolicyDetails['DATA']['ReturnPolicyDetails']);
    }
    $_MagnaSession[$mpID]['eBayReturnPolicyDetails'] = $returnPolicyDetails['DATA']['ReturnPolicyDetails'];
    return $returnPolicyDetails['DATA']['ReturnPolicyDetails'];
}

# einzelnes Detail: ReturnsAccepted, ReturnsWithin, ShippingCostPaidBy oder RefundOption
# (letzteres noch nicht implementiert, gibts in Europa nicht)
function geteBaySingleReturnPolicyDetail($detailName) {
    global $_MagnaSession;
    $mpID = $_MagnaSession['mpID'];
    if ((!isset ($_MagnaSession[$mpID]['eBayReturnPolicyDetails']))
        || (!is_array($_MagnaSession[$mpID]['eBayReturnPolicyDetails']))) {
        $returnPolicyDetails = geteBayReturnPolicyDetails();
    } else {
        $returnPolicyDetails = $_MagnaSession[$mpID]['eBayReturnPolicyDetails'];
    }
    if (!isset($returnPolicyDetails[$detailName])) {
        return array('' => '-');
    }
    return $returnPolicyDetails[$detailName];
}

function geteBayPlusSettings() {
    global $_MagnaSession;
    $mpID = $_MagnaSession['mpID'];
    $site = getDBConfigValue('ebay.site', $mpID);
    initArrayIfNecessary($_MagnaSession, array($mpID, $site, 'eBayPlusSettings'));

    if (!empty($_MagnaSession[$mpID][$site]['eBayPlusSettings'])) {
        return $_MagnaSession[$mpID][$site]['eBayPlusSettings'];
    }
    try {
        $eBayPlusSettings = MagnaConnector::gi()->submitRequest(array(
            'ACTION' => 'GeteBayAccountSettings'
        ));
    } catch (MagnaException $e) {
        $eBayPlusSettings = array('DATA' => array('eBayPlus' => 'false', 'eBayPlusListingDefault' => 'false'));
    }
    $_MagnaSession[$mpID][$site]['eBayPlusSettings'] = $eBayPlusSettings['DATA'];
    return $_MagnaSession[$mpID][$site]['eBayPlusSettings'];
}

function getEBayAttributes($cID, $mode, $preselectedValues = array()) {
    global $_MagnaSession;
    # erst schauen obs ItemSpecifics gibt (sind neuer & das andere ist uU deprecated)
    $itemSpecs = getEBayItemSpecifics($cID, $mode, $preselectedValues);
    if (!empty($itemSpecs))
        return $itemSpecs;
    try {
        $attrOptions = MagnaConnector::gi()->submitRequest(array(
            'ACTION' => 'GetAttributes',
            'DATA'   => array(
                'CategoryID'    => $cID,
                'FormStructure' => true,
                'Site'          => getDBConfigValue('ebay.site', $_MagnaSession['mpID']),
            )
        ));
    } catch (MagnaException $e) {
        return '';
    }
    if (!array_key_exists('attributes', $attrOptions['DATA'])
        || empty($attrOptions['DATA']['attributes'])
    ) {
        #return getEBayItemSpecifics($cID, $mode, $preselectedValues);
        return '';
    }
    $attrOptions = $attrOptions['DATA'];
    $attrOptions['attributes']['key'] = array('Attributes', $mode);
    $attrOptions['attributes']['head'] = ML_EBAY_LABEL_ATTRIBUTES_FOR.' '.(($mode == 1)
            ? ML_LABEL_EBAY_PRIMARY_CATEGORY
            : ML_LABEL_EBAY_SECONDARY_CATEGORY
        );
    if (!is_array($preselectedValues)) {
        $preselectedValues = str_replace("%27", "'", $preselectedValues);
        $preselectedValues = json_decode($preselectedValues, true);
    }
    if (!empty($preselectedValues)) {
        if (!isset($preselectedValues[0])) {
            if (isset($preselectedValues[1]))
                $preselectedValues = $preselectedValues[1];
            else if (isset($preselectedValues[2]))
                $preselectedValues = $preselectedValues[2];
        }
    }
    //change key for form-fields an fill preselected values with new key
    labelToKey($attrOptions, $preselectedValues);

    require_once(DIR_MAGNALISTER_INCLUDES.'lib/classes/GenerateProductsDetailInput.php');
    if (!empty($preselectedValues)) {
        $gPDI = new GenerateProductsDetailInput($attrOptions, $preselectedValues);
    } else
        $gPDI = new GenerateProductsDetailInput($attrOptions);
    return $gPDI->render();
}

function getEBayItemSpecifics($cID, $mode, $preselectedValues = '') {
    global $_MagnaSession;
    try {
        $specsOptions = MagnaConnector::gi()->submitRequest(array(
            'ACTION' => 'GetItemSpecifics',
            'DATA'   => array(
                'CategoryID'    => $cID,
                'FormStructure' => true,
                'Site'          => getDBConfigValue('ebay.site', $_MagnaSession['mpID']),
            )
        ));
    } catch (MagnaException $e) {
        return '';
    }
    if (!array_key_exists('specifics', $specsOptions['DATA'])
        || empty($specsOptions['DATA']['specifics'])
    ) {
        return '';
    }
    $specsOptions = $specsOptions['DATA'];
    $specsOptions['specifics']['key'] = array('ItemSpecifics', $mode);
    $specsOptions['specifics']['head'] = ML_EBAY_LABEL_ATTRIBUTES_FOR.' '.(($mode == 1)
            ? ML_LABEL_EBAY_PRIMARY_CATEGORY
            : ML_LABEL_EBAY_SECONDARY_CATEGORY
        );
    if (!is_array($preselectedValues)) {
        $preselectedValues = str_replace("%27", "'", (fixBrokenJsonUmlauts($preselectedValues)));
        $preselectedValues = json_decode($preselectedValues, true);
    }
    if (!empty($preselectedValues)) {
        if (!isset($preselectedValues[0])) {
            if (isset($preselectedValues[1]) && ($mode == 1)) {
                $preselectedValues = $preselectedValues[1];
            } else if (isset($preselectedValues[2]) && ($mode == 2)) {
                $preselectedValues = $preselectedValues[2];
            }
        }
    }

    //change key for form-fields an fill preselected values with new key
    labelToKey($specsOptions, $preselectedValues);
    require_once(DIR_MAGNALISTER_INCLUDES.'lib/classes/GenerateProductsDetailInput.php');
    /* if EbayProductIdentifierSync booked, prefill product identifier fields */
    $prefilledReadonlyFields = array();
    if (getDBConfigValue('ebay.listingdetails.sync', $_MagnaSession['mpID'], false) != 'false'
        && ML_ShopAddOns::mlAddOnIsBooked('EbayProductIdentifierSync')
    ) {
        $productIds = MagnaDB::gi()->fetchArray("
			SELECT pID
			  FROM ".TABLE_MAGNA_SELECTION."
			WHERE     mpID = '".$_MagnaSession['mpID']."'
			      AND selectionname = 'prepare'
			      AND session_id = '".session_id()."'
		", true);
        if (count($productIds) > 1) {
            $prefilledReadonlyFields = array(
                'Brand' => '(matching)',
                'MPN'   => '(matching)',
                'EAN'   => '(matching)'
            );
            if (getDBConfigValue('ebay.listingdetails.mpn.dbmatching.table', $_MagnaSession['mpID'], false) == false) {
                unset($prefilledReadonlyFields['MPN']);
            }
            if (getDBConfigValue('ebay.listingdetails.ean.dbmatching.table', $_MagnaSession['mpID'], false) == false) {
                unset($prefilledReadonlyFields['EAN']);
            }
        } else {
            $productId = array_pop($productIds);
            $prefilledReadonlyFields = EbayHelper::getProductListingDetailsFromProduct($productId, getDBConfigValue('ebay.lang', $_MagnaSession['mpID']));
        }
    }

    if (!empty($preselectedValues)) {
        $gPDI = new GenerateProductsDetailInput($specsOptions, $preselectedValues, $prefilledReadonlyFields);
    } else
        $gPDI = new GenerateProductsDetailInput($specsOptions, array(), $prefilledReadonlyFields);
    return $gPDI->render();
}

# Helper function: change key for form-fields and fill preselected values with new key
function labelToKey(&$aAttrOptions, &$preselectedValues) {
    $aSpecKey2Spec = array();
    reset($aAttrOptions['specifics']['fields']);
    if (1 == key($aAttrOptions['specifics']['fields'])) {
        $aAttrOptions['specifics']['fields'] = array_values($aAttrOptions['specifics']['fields']);
    }
    $aPreselectedValues = array();
    if (empty($preselectedValues)) {
        $preselectedValues = array();
    }
    foreach ($aAttrOptions as $sAttrOptionsKey => $aAttrOptionsValue) {
        foreach ($aAttrOptionsValue['fields'] as $sAttrOptionsFieldKey => $aAttrOptionsFieldValue) {
            $sLabel = $aAttrOptionsFieldValue['label'];
            $blMulti = count($aAttrOptionsFieldValue['inputs']) > 1;
            foreach ($aAttrOptionsFieldValue['inputs'] as $sAttrOptionsInputKey => $aAttrOptionsInputValue) {
                foreach ($aAttrOptionsInputValue['cols'] as $sAttrOptionsColsKey => $aAttrOptionsColsValue) {
                    $sKey = $sLabel.($blMulti ? '_'.$aAttrOptionsColsValue['key'] : '');
                    $aPack = unpack('H*', $sKey);
                    $aAttrOptions[$sAttrOptionsKey]['fields']
                    [$sAttrOptionsFieldKey]['inputs']
                    [$sAttrOptionsInputKey]['cols']
                    [$sAttrOptionsColsKey]['key'] = $aPack[1];
                    if (array_key_exists($sKey, $preselectedValues)) {
                        $aPreselectedValues[$aPack[1]] = $preselectedValues[$sKey];
                    } elseif (array_key_exists($aPack[1], $preselectedValues)) {
                        $aPreselectedValues[$aPack[1]] = $preselectedValues[$aPack[1]];
                    } elseif (array_key_exists($aAttrOptionsColsValue['key'], $preselectedValues)) {
                        $aPreselectedValues[$aPack[1]] = $preselectedValues[$aAttrOptionsColsValue['key']];
                    }
                    if (array_key_exists('values', $aAttrOptionsColsValue)) {
                        $aRealValues = array();
                        foreach ($aAttrOptionsColsValue['values'] as $iSelectKey => $sSelectValue) {
                            if ($iSelectKey < 0) {
                                $aRealValues[$iSelectKey] = $sSelectValue;
                            } else {
                                $aRealValues[$sSelectValue] = $sSelectValue;
                                if (
                                    array_key_exists($aPack[1], $aPreselectedValues)
                                    && is_array($aPreselectedValues[$aPack[1]])
                                    && array_key_exists('select', $aPreselectedValues[$aPack[1]])
                                    && (
                                    (
                                        $aPreselectedValues[$aPack[1]]['select'] == $sSelectValue
                                    ) /*|| (
											is_numeric($aPreselectedValues[$aPack[1]]['select']) 
											&& $aPreselectedValues[$aPack[1]]['select'] == $iSelectKey
										)*/
                                    )
                                ) {
                                    $aPreselectedValues[$aPack[1]]['select'] = $sSelectValue;
                                    break;
                                } elseif (
                                    array_key_exists($aPack[1], $aPreselectedValues)
                                    && is_array($aPreselectedValues[$aPack[1]])
                                    && array_key_exists(0, $aPreselectedValues[$aPack[1]])
                                ) {//multiple
                                    foreach ($aPreselectedValues[$aPack[1]] as $sMultipleValue) {
                                        if (
                                            (!is_numeric($sMultipleValue) && $sSelectValue == $sMultipleValue)
                                            ||
                                            (is_numeric($sMultipleValue) && $iSelectKey == $sMultipleValue)
                                        ) {
                                            $aPreselectedValues[$aPack[1]][$sSelectValue] = $sSelectValue;
                                        }
                                    }
                                }
                            }
                        }
                        $aAttrOptions[$sAttrOptionsKey]['fields']
                        [$sAttrOptionsFieldKey]['inputs']
                        [$sAttrOptionsInputKey]['cols']
                        [$sAttrOptionsColsKey]['values'] = $aRealValues;
                    }
                }
            }
        }
    }
    $preselectedValues = $aPreselectedValues;
}

function VariationsEnabled($cID) {
    try {
        $VariationsEnabledResult = MagnaConnector::gi()->submitRequest(array(
            'ACTION' => 'VariationsEnabled',
            'DATA'   => array('CategoryID' => $cID,),
        ));
    } catch (MagnaException $e) {
        return false;
    }
    if (!array_key_exists('VariationsEnabled', $VariationsEnabledResult['DATA']))
        return false;
    if ('true' == (string)$VariationsEnabledResult['DATA']['VariationsEnabled'])
        return true;
    else return false;
}

function ProductRequired($cID) {
    global $_MagnaSession;
    if (empty($cID))
        return false;
    try {
        $ProductRequiredResult = MagnaConnector::gi()->submitRequest(array(
            'ACTION' => 'ProductRequired',
            'DATA'   => array(
                'CategoryID' => $cID,
                'Site'       => getDBConfigValue('ebay.site', $_MagnaSession['mpID'])
            ),
        ));
    } catch (MagnaException $e) {
        return false;
    }
    if (isset($ProductRequiredResult['DATA']['ProductRequiredEnabled'])
        && (('Required' == (string)$ProductRequiredResult['DATA']['ProductRequiredEnabled'])
            || ('Enabled' == (string)$ProductRequiredResult['DATA']['ProductRequiredEnabled']))
    ) {
        return true;
    }
    return false;
}

function GetConditionValues($cID) {
    global $_MagnaSession;
    if (empty($cID))
        return false;
    try {
        $GetConditionValuesResult = MagnaConnector::gi()->submitRequest(array(
            'ACTION' => 'GetConditionValues',
            'DATA'   => array(
                'CategoryID' => $cID,
                'Site'       => getDBConfigValue('ebay.site', $_MagnaSession['mpID'])
            ),
        ));
    } catch (MagnaException $e) {
        return false;
    }
    if (isset($GetConditionValuesResult['DATA']['ConditionValues'])
        && (is_array($GetConditionValuesResult['DATA']['ConditionValues']))
    ) {
        return $GetConditionValuesResult['DATA']['ConditionValues'];
    }
    return false;
}

function GetConditionPolicies($cID) {
    global $_MagnaSession;
    if (empty($cID))
        return false;
    try {
        $GetConditionPoliciesResult = MagnaConnector::gi()->submitRequest(array(
            'ACTION' => 'GetConditionPolicies',
            'DATA' => array('CategoryID' => $cID,
                'Site' => getDBConfigValue('ebay.site', $_MagnaSession['mpID'])),
        ));
    } catch (MagnaException $e) {
        return false;
    }
    if (isset($GetConditionPoliciesResult['DATA']['ConditionPolicies'])
        && (is_array($GetConditionPoliciesResult['DATA']['ConditionPolicies']))
    ) {
        if (!isset($_MagnaSession[$_MagnaSession['mpID']]['EbayApiConfigValues'])) {
            $_MagnaSession[$_MagnaSession['mpID']]['EbayApiConfigValues'] = array();
        }
        $_MagnaSession[$_MagnaSession['mpID']]['EbayApiConfigValues']['GetConditionPolicies{"DATA":{"CategoryID":"'.$cID.'"}}'] = $GetConditionPoliciesResult['DATA']['ConditionPolicies'];
        return $GetConditionPoliciesResult['DATA']['ConditionPolicies'];
    }
    return false;
}

function GetConditionDescriptors($iCategoryID, $iConditionID) {
    global $_MagnaSession;
    if (isset($_MagnaSession[$_MagnaSession['mpID']]['EbayApiConfigValues']['GetConditionPolicies{"DATA":{"CategoryID":"'.$iCategoryID.'"}}'])) { 
        $aConditionPolicies = $_MagnaSession[$_MagnaSession['mpID']]['EbayApiConfigValues']['GetConditionPolicies{"DATA":{"CategoryID":"'.$iCategoryID.'"}}'];
    } else {
        $aConditionPolicies = GetConditionPolicies($iCategoryID);
    }
    if (empty($aConditionPolicies)) {
        return false;
    }
    if ($iConditionID == 1000) { // nothing predefined, take the first (it's never < 1000)
        return current($aConditionPolicies);
    }
    if (!array_key_exists($iConditionID, $aConditionPolicies)) {
        return false;
    }
    return $aConditionPolicies[$iConditionID]; 
}

function substitutePictures($tmplStr, $pID, $imagePath) {
    if (version_compare(PHP_VERSION, '5.2.0', '>=')) {
        @ini_set('pcre.backtrack_limit', '10000000');
        @ini_set('pcre.recursion_limit', '10000000');
    }
    $undo = ml_extractBase64($tmplStr);
    # Tabelle nur bei xtCommerce- und Gambio- Shops vorhanden (nicht OsC)
    if (defined('TABLE_MEDIA') && MagnaDB::gi()->tableExists(TABLE_MEDIA)
        && defined('TABLE_MEDIA_LINK') && MagnaDB::gi()->tableExists(TABLE_MEDIA_LINK)
    ) {
        $pics = MagnaDB::gi()->fetchArray('SELECT
            id image_nr, file image_name
			FROM '.TABLE_MEDIA.' m, '.TABLE_MEDIA_LINK.' ml
            WHERE ml.type=\'images\' AND ml.class=\'product\' AND m.id=ml.m_id AND ml.link_id='.$pID);
        $i = 2;
        # Ersetze #PICTURE2# usw. (#PICTURE1# ist das Hauptbild und wird vorher ersetzt)
        foreach ($pics as $pic) {
            $tmplStr = str_replace('#PICTURE'.$i.'#', "<img src=\"".$imagePath.$pic['image_name']."\" style=\"border:0;\" alt=\"\" title=\"\" />",
                preg_replace('/(src|SRC|href|HREF|rev|REV)(\s*=\s*)(\'|")(#PICTURE'.$i.'#)/', '\1\2\3'.$imagePath.$pic['image_name'], $tmplStr));
            $i++;
        }
        # Uebriggebliebene #PICTUREx# loeschen
        $tmplStr = preg_replace('/<[^<]*(src|SRC|href|HREF|rev|REV)\s*=\s*(\'|")#PICTURE\d+#(\'|")[^>]*\/*>/', '', $tmplStr);
        $tmplStr = preg_replace('/#PICTURE\d+#/', '', $tmplStr);
        $str = ml_restoreBase64($tmplStr, $undo);
    } else {
        $tmplStr = preg_replace('/<[^<]*(src|SRC|href|HREF|rev|REV)\s*=\s*(\'|")#PICTURE\d+#(\'|")[^>]*\/*>/', '', $tmplStr);
        $tmplStr = preg_replace('/#PICTURE\d+#/', '', $tmplStr);
        $str = ml_restoreBase64($tmplStr, $undo);
    }
    # ggf. leere image tags loeschen
    $str = preg_replace('/<img[^>]*src=(""|\'\')[^>]*>/i', '', $str);
    return $str;
}

# Hilfsfunktion: Preis bestimmen
# priceType: == ListingType oder BuyItNowPrice
function makePrice($pID, $priceType, $takePrepared = false, $variationPrice = 0.0, $format = false) {
    global $_MagnaSession;
    if ($takePrepared) {
        $iBuyItNowPrice = magnalisterEbayGetPriceByType($pID, $priceType);
        if ($iBuyItNowPrice !== false) {
            return $iBuyItNowPrice;
        }
    }
    require_once(DIR_MAGNALISTER_INCLUDES.'lib/classes/SimplePrice.php');
    switch ($priceType) {
        case 'Chinese':
        {
            $which = 'chinese';
            break;
        }
        case 'BuyItNowPrice':
        {
            $which = 'chinese.buyitnow';
            break;
        }
        case 'strike':
        case 'StrikePrice':
        {
            $which = 'strike';
            break;
        }
        default:
        { # 'FixedPriceItem' oder 'StoresFixedPrice'
            $which = 'fixed';
            break;
        }
    }
    $myPrice = new SimplePrice(null, getCurrencyFromMarketplace($_MagnaSession['mpID']));
    if ($variationPrice) {
        $myPrice->setPriceFromDB($pID, $_MagnaSession['mpID'], $which)->addLump($variationPrice)->finalizePrice($pID, $_MagnaSession['mpID'], $which);
    } else {
        $myPrice->setFinalPriceFromDB($pID, $_MagnaSession['mpID'], $which);
    }

    if ($format) {
        return $myPrice->getPrice()->format();
    } else {
        return $myPrice->getPrice();
    }
}

# Hilfsfunktion: Variation-Preis zu einem Grundpreis berechnen
# (benoetigt wenn Grundpreis per Hand geaendert)
# Grundpreis ist brutto, Varianten-Aufschlaege netto, daher kann man nicht einfach addieren
function addVarPriceToPrice($pID, $mainPrice, $varPrice) {
    global $_MagnaSession;
    $myPrice = new SimplePrice($mainPrice, getCurrencyFromMarketplace($_MagnaSession['mpID']));
    $myPrice->removeTaxByPID($pID)->addLump($varPrice)->addTaxByPID($pID);
    return $myPrice->getPrice();
}

/*
 * Helper function for prepare view:
 * Determine main + strike price
 * depending on STP settings
 * @param int $pID
 * @param string $StrikePriceKind
 * @param int $StrikePriceGroup
 * @return array (string price, string strikePrice)
 */
function makePriceByStrikePriceSettings($pID, $StrikePriceKind, $StrikePriceGroup, $variationPrice = 0.0, $format = false) {
    global $_MagnaSession;
    switch($StrikePriceKind) {
        case('SpecialPrice'):
            $fixExtra = array (
                'AddKind'    => getDBConfigValue($mp.$extra.'.price.addkind', $_MagnaSession['mpID'], 'percent'),
                'Factor'     => (float)getDBConfigValue($mp.$extra.'.price.factor', $_MagnaSession['mpID'], 0),
                'Signal'     => getDBConfigValue($mp.$extra.'.price.signal', $_MagnaSession['mpID'], ''),
                'Group'      => getDBConfigValue('ebay.fixed.price.group', $_MagnaSession['mpID'], ''),
                'UseSpecialOffer' => true,
                'IncludeTax' => true
            );
            $stpExtra = array_merge($fixExtra, array (
                'UseSpecialOffer' => false,
            ));
            $myFixPrice = new SimplePrice(null, getCurrencyFromMarketplace($_MagnaSession['mpID']));
            if ($variationPrice) {
                $myFixPrice->setPriceFromDB($pID, $_MagnaSession['mpID'], $fixExtra)->addLump($variationPrice)->finalizePrice($pID, $_MagnaSession['mpID'], $fixExtra);
            } else {
                $myFixPrice->setFinalPriceFromDB($pID, $_MagnaSession['mpID'], $fixExtra);
            }
            $myStrikePrice = new SimplePrice(null, getCurrencyFromMarketplace($_MagnaSession['mpID']));
            if ($variationPrice) {
                $myStrikePrice->setPriceFromDB($pID, $_MagnaSession['mpID'], $stpExtra)->addLump($variationPrice)->finalizePrice($pID, $_MagnaSession['mpID'], $stpExtra);
            } else {
                $myStrikePrice->setFinalPriceFromDB($pID, $_MagnaSession['mpID'], $stpExtra);
            }
            $retVal = array (
                'price'       => $format ? $myFixPrice->format()    : $myFixPrice->getPrice(),
                'strikePrice' => $format ? $myStrikePrice->format() : $myStrikePrice->getPrice()
            );
            break;
        case('CustomerGroup'):
            $extra = array (
                'AddKind'    => 'percent',
                'Factor'     => '0',
                'Signal'     => '',
                'Group'      => $StrikePriceGroup,
                'UseSpecialOffer' => false,
                'IncludeTax' => true
            );
            $myStrikePrice = new SimplePrice(null, getCurrencyFromMarketplace($_MagnaSession['mpID']));
            if ($variationPrice) {
                $myStrikePrice->setPriceFromDB($pID, $_MagnaSession['mpID'], $extra)->addLump($variationPrice)->finalizePrice($pID, $_MagnaSession['mpID'], $extra);
            } else {
                $myStrikePrice->setFinalPriceFromDB($pID, $_MagnaSession['mpID'], $extra);
            }
            $retVal = array (
                'price'       => makePrice($pID, 'fixed', false, $variationPrice, $format),
                'strikePrice' => $format ? $myStrikePrice->format() : $myStrikePrice->getPrice()
            );
            break;
        case('DontUse'):
        default:
            $retVal = array (
                'price'       => makePrice($pID, 'fixed', false, $variationPrice, $format),
                'strikePrice' => 0
            );
            break;
    }
    if ($retVal['strikePrice'] <= $retVal['price']) {
        $retVal['strikePrice'] = 0;
    }
    return $retVal;
}

# Hilfsfunktion: Anzahl bestimmen
function makeQuantity($pID, $ListingType = 'StoresFixedPrice') {
    global $_MagnaSession;
    switch ($ListingType) {
        case 'Chinese':
        {
            $calc_method = 'lump';
            $qValue = 1;
            break;
        }
        default:
        { # 'FixedPriceItem' oder 'StoresFixedPrice'
            $calc_method = getDBConfigValue('ebay.fixed.quantity.type', $_MagnaSession['mpID']);
            $qValue = (int)getDBConfigValue('ebay.fixed.quantity.value', $_MagnaSession['mpID']);
            $maxQuantity = (int)getDBConfigValue('ebay.maxquantity', $_MagnaSession['mpID'], 0);
            if (0 == $maxQuantity)
                $maxQuantity = PHP_INT_MAX;
            break;
        }
    }
    if ('lump' == $calc_method) {
        return $qValue;
    }
    $shop_stock = 0;
    # Nehme Anzahl Varianten, soweit Varianten lt konfig aktiviert, und soweit solche existieren
    //if (('Chinese' != $ListingType) && getDBConfigValue(array($_MagnaSession['currentPlatform'].'.usevariations', 'val'), $_MagnaSession['mpID'], true) && variationsExist($pID)) {
    //    if ('stock' == $calc_method)
    //	    $shop_stock = min(getProductVariationsQuantity($pID), $maxQuantity);
    //    else if ('stocksub' == $calc_method)
    //	    $shop_stock = min(getProductVariationsQuantity($pID, $qValue), $maxQuantity);
    //        return $shop_stock;
    //}
    # Keine Varianten da, nehme Stammartikel
    $shop_stock = MagnaDB::gi()->fetchOne('SELECT products_quantity FROM '.TABLE_PRODUCTS.' WHERE products_id ='.$pID);
    if ('stock' == $calc_method) {
        return min($shop_stock, $maxQuantity);
    } else if ('stocksub' == $calc_method) {
        return min(max(0, $shop_stock - $qValue), $maxQuantity);
    } else {
        return 0;
    }
}

# Hilfsfunktion: Varianten-Matrix fuer die Einstellung aufbauen
function getVariations($pID, $otherMainPrice = null) {
    global $_MagnaSession;
    $variations = array();
    $namelist = array();
    $valuelist = array();
    return false;
}

/*
 * Hilfsfunktion: Soweit für den Artikel Varianten-ePIDs hinterlegt sind, gebe diese zurück
 * als Array. Keys sind, je nach general.keytype
 * MarketPlaceId oder MarketplaceSku
 */
function getEpidsForVariationsByKey($pID, $artNr) {
    // ePIDs for Variations (if stored)
    global $_MagnaSession;
    $blKeytypeIsArtnr = (getDBConfigValue('general.keytype', '0') == 'artNr');
    $blePIDsForVariationsStored = (boolean)MagnaDB::gi()->fetchOne(eecho('SELECT COUNT(*) FROM magnalister_ebay_variations_epids
        WHERE mpID = '.$_MagnaSession['mpID'].'
          AND '.($blKeytypeIsArtnr
            ? 'products_sku = \''.$artNr.'\''
            : 'products_id = '.$pID), false));
    if (!$blePIDsForVariationsStored)
        return false;
    $ePIDsForVariations = MagnaDB::gi()->fetchArray('SELECT * FROM magnalister_ebay_variations_epids
        WHERE mpID = '.$_MagnaSession['mpID'].'
            AND '.($blKeytypeIsArtnr
            ? 'products_sku = \''.$artNr.'\''
            : 'products_id = '.$pID));
    $ePIDsForVariationsByKey = array();
    foreach ($ePIDsForVariations as $ePidRow) {
        if ($blKeytypeIsArtnr)
            $ePIDsForVariationsByKey[$ePidRow['marketplace_sku']] = $ePidRow['ePID'];
        else $ePIDsForVariationsByKey[$ePidRow['marketplace_id']] = $ePidRow['ePID'];
    }
    return $ePIDsForVariationsByKey;
}

function geteBayCategoryPath($CategoryID, $StoreCategory = false, $justImported = false) {
    global $_MagnaSession;
    $appendedText = '&nbsp;<span class="cp_next">&gt;</span>&nbsp;';
    if ($StoreCategory) {
        $SiteID = $_MagnaSession['mpID'];
    } else {
        $SiteID = geteBaySiteID();
    }
    $StoreCategory = $StoreCategory ? '1' : '0';
    $catPath = '';
    do {
        # Ermittle Namen, CategoryID und ParentID,
        # dann das gleiche fuer die ParentCategory usw.
        # bis bei Top angelangt (CategoryID = ParentID)
        $yCP = MagnaDB::gi()->fetchRow('
			SELECT CategoryID, CategoryName , ParentID
			  FROM '.TABLE_MAGNA_EBAY_CATEGORIES.'
			 WHERE CategoryID=\''.$CategoryID.'\'
			 AND StoreCategory=\''.$StoreCategory.'\'
			 AND SiteID = \''.$SiteID.'\'
			 ORDER BY InsertTimestamp DESC LIMIT 1
		');
        if ($yCP === false)
            break;
        if (empty($catPath)) {
            $catPath = fixHTMLUTF8Entities($yCP['CategoryName']);
        } else {
            $catPath = fixHTMLUTF8Entities($yCP['CategoryName']).$appendedText.$catPath;
        }
        $CategoryID = $yCP['ParentID'];
    } while ($yCP['CategoryID'] != $yCP['ParentID']);

    if (($yCP === false) && ($justImported == true)) {
        return '<span class="invalid">'.ML_LABEL_INVALID.'</span>';
    }
    if (($yCP === false) && ($justImported == false)) {
        if ($StoreCategory) {
            require_once(DIR_MAGNALISTER_MODULES.'ebay/classes/eBayCategoryMatching.php');
            $cm = new eBayCategoryMatching();
            $cm->importeBayStoreCategories();
        } else {
            importeBayCategoryPath($CategoryID);
        }
        return geteBayCategoryPath($CategoryID, $StoreCategory, true);
    }
    return $catPath;
}

# Die Funktion wird verwendet beim Aufruf der Kategorie-Zuordnung, nicht vorher.
# Beim Aufruf werden die Hauptkategorien gezogen,
# und beim Anklicken der einzelnen Kategorie die Kind-Kategorien, falls noch nicht vorhanden.
function importeBayCategoryPath($CategoryID) {
    global $_MagnaSession;
    try {
        $categories = MagnaConnector::gi()->submitRequest(array(
            'ACTION' => 'GetCategoryWithAncestors',
            'DATA'   => array(
                'CategoryID' => $CategoryID,
                'Site'       => getDBConfigValue('ebay.site', $_MagnaSession['mpID'])
            ),
        ));
    } catch (MagnaException $e) {
        $categories = array(
            'DATA' => false
        );
    }
    if (!is_array($categories['DATA']) || empty($categories['DATA'])) {
        return false;
    }
    $now = time();
    foreach ($categories['DATA'] as &$curRow) {
        $curRow['InsertTimestamp'] = $now;
        $curRow['StoreCategory'] = '0';
    }
    #$delete_query = 'DELETE FROM '.TABLE_MAGNA_EBAY_CATEGORIES
    #	.' WHERE StoreCategory=\'0\'
    #	AND SiteID = '.$categories['DATA'][0]['SiteID'].'
    #	AND ParentID = ';
    # ganz oben ist CategoryID == ParentID
    #if (0 == $ParentID)	$delete_query .= 'CategoryID';
    #else			$delete_query .= $ParentID.' AND ParentID <> CategoryID';
    #MagnaDB::gi()->query($delete_query);
    #echo print_m($categories['DATA'], __FUNCTION__);
    MagnaDB::gi()->batchinsert(TABLE_MAGNA_EBAY_CATEGORIES, $categories['DATA'], true);
    return true;
}

# Streichpreis Felder für die Properties Table aufbereiten
function convertStrikePriceFields(&$itemDetails) {
    global $_MagnaSession;
    if ($itemDetails['UseStrikePrice'] !== 'true') {
        $itemDetails['StrikePriceKind'] = 'DontUse';
    }
    $aRes = array();
    $aRes['ebay.strike.price.kind'] = $itemDetails['StrikePriceKind'];
    switch($itemDetails['StrikePriceKind']) {
        case ('SpecialPrice'): {
            $aRes['ebay.strike.price.addkind'] = getDBConfigValue('ebay.fixed.price.addkind', $_MagnaSession['mpID']);
            $aRes['ebay.strike.price.factor']  = getDBConfigValue('ebay.fixed.price.factor', $_MagnaSession['mpID']);
            $aRes['ebay.strike.price.signal']  = getDBConfigValue('ebay.fixed.price.signal', $_MagnaSession['mpID']);
            $aRes['ebay.strike.price.group']   = getDBConfigValue('ebay.fixed.price.group', $_MagnaSession['mpID']);
            break;
        }
        case ('DontUse'): {
            $aRes['ebay.strike.price.addkind']    = 'percent';
            $aRes['ebay.strike.price.factor']     = '0';
            $aRes['ebay.strike.price.group']      = -1;
            $aRes['ebay.strike.price.isUVP'] = '{"val":false}';
            $aRes['ebay.strike.price.signal']     = '';
            break;
        }    
        default: {
            $aRes['ebay.strike.price.addkind']    = 'percent';
            $aRes['ebay.strike.price.factor']     = '0';
            $aRes['ebay.strike.price.group']      = $itemDetails['StrikePriceGroup'];
            $aRes['ebay.strike.price.isUVP']      = getDBConfigValue('ebay.strike.price.isUVP', $_MagnaSession['mpID'], '');
            #$aRes['ebay.strike.price.isUVP'] = ( $itemDetails['StrikePriceIsUvp']
            #    ?'{"val":true}'
            #    :'{"val":false}' );
            $aRes['ebay.strike.price.signal']     = '';
            break;
        }
    }
    $jRes = json_encode($aRes);
    $itemDetails['StrikePriceConf'] = $jRes;
}

# Hilfsfunktion fuer SaveEBaySingleProductProperties und SaveEBayMultipleProductProperties
# bereite die DB-Zeile vor mit allen Daten die sowohl fuer Single als auch Multiple inserts gelten
function prepareEBayPropertiesRow($pID, $itemDetails) {
    global $_MagnaSession;

    convertStrikePriceFields($itemDetails);
    $row = array();
    $row['mpID'] = $_MagnaSession['mpID'];
    $row['products_id'] = $pID;
    $row['products_model'] = MagnaDB::gi()->fetchOne('SELECT products_model FROM '.TABLE_PRODUCTS.' WHERE products_id ='.$pID);
    $row['Site'] = $itemDetails['Site'];
    $row['PrimaryCategory'] = $itemDetails['PrimaryCategory'];
    if (!empty($itemDetails['PrimaryCategory'])) {
        $row['PrimaryCategoryName'] = MagnaDB::gi()->fetchOne('SELECT CategoryName FROM '.TABLE_MAGNA_EBAY_CATEGORIES.' WHERE CategoryID ='.$itemDetails['PrimaryCategory'].' LIMIT 1');
    }
    if (!empty($itemDetails['SecondaryCategory'])) {
        $row['SecondaryCategory'] = $itemDetails['SecondaryCategory'];
        $row['SecondaryCategoryName'] = MagnaDB::gi()->fetchOne('SELECT CategoryName FROM '.TABLE_MAGNA_EBAY_CATEGORIES.' WHERE CategoryID ='.$itemDetails['SecondaryCategory'].' LIMIT 1');
    }
    if (!empty($itemDetails['StoreCategory'])) {
        $row['StoreCategory'] = $itemDetails['StoreCategory'];
    }
    if (!empty($itemDetails['StoreCategory2'])) {
        $row['StoreCategory2'] = $itemDetails['StoreCategory2'];
    }
    $row['ListingType'] = $itemDetails['ListingType'];
    $row['ListingDuration'] = $itemDetails['ListingDuration'];
    $row['StrikePriceConf'] = isset($itemDetails['StrikePriceConf']) ? $itemDetails['StrikePriceConf']:'';
    $row['PaymentMethods'] = json_encode($itemDetails['PaymentMethods']);
    if (!empty($itemDetails['Attributes'])) {
        $row['Attributes'] = json_encode($itemDetails['Attributes']);
    } elseif ($oldAttributes = MagnaDB::gi()->fetchOne('SELECT Attributes FROM '.TABLE_MAGNA_EBAY_PROPERTIES.' WHERE products_id ='.$pID.' AND mpID = '.$_MagnaSession['mpID'])) {
        $row['Attributes'] = $oldAttributes;
    }
    if (!empty($itemDetails['ItemSpecifics'])) {
        arrayEntitiesFixHTMLUTF8($itemDetails['ItemSpecifics']);
        $row['ItemSpecifics'] = json_encode($itemDetails['ItemSpecifics']);
    } elseif ($oldItemSpecifics = MagnaDB::gi()->fetchOne('SELECT ItemSpecifics FROM '.TABLE_MAGNA_EBAY_PROPERTIES.' WHERE products_id ='.$pID.' AND mpID = '.$_MagnaSession['mpID'])) {
        $row['ItemSpecifics'] = $oldItemSpecifics;
    }

    $row['ConditionID'] = $itemDetails['ConditionID'];
    $row['ConditionDescriptors'] = json_encode($itemDetails['ConditionDescriptors']);
    $row['ConditionDescription'] = $itemDetails['ConditionDescription'];
    // BusinessPolicies
    if (array_key_exists('shippingsellerprofile', $itemDetails)) {
        $row['SellerProfiles'] = json_encode(
            array(
                'Payment'  => $itemDetails['paymentsellerprofile'],
                'Shipping' => $itemDetails['shippingsellerprofile'],
                'Return'   => getDBConfigValue('ebay.default.returnsellerprofile', $_MagnaSession['mpID'])
            ));
        // Shipping a bit different with the simplified readonly stuff
        // We store everything in the preparation table like before,
        // so that the preparation stays valid even if the customer switches off BusinessPolicies on eBay.
        if (array_key_exists('conf', $itemDetails)) {
            if (array_key_exists('ebay.default.shipping.local', $itemDetails['conf'])) {
                $itemDetails['ebay_default_shipping_local'] = $itemDetails['conf']['ebay.default.shipping.local'];
            }
            if (array_key_exists('ebay.default.shipping.international', $itemDetails['conf'])) {
                $itemDetails['ebay_default_shipping_international'] = $itemDetails['conf']['ebay.default.shipping.international'];
            }
        }
    }
    $ShippingDetails = array();
    $ShippingDetails['ShippingServiceOptions'] = array();
    foreach ($itemDetails['ebay_default_shipping_local'] as $key => $localService) {
        $ShippingDetails['ShippingServiceOptions'][$key] = array(
            'ShippingService'     => $localService['service'],
            'ShippingServiceCost' => priceToFloat($localService['cost']),
        );
        if (array_key_exists('addcost', $localService)
            && isset($localService['addcost'])
        ) {
            $ShippingDetails['ShippingServiceOptions'][$key]['ShippingServiceAdditionalCost'] = priceToFloat($localService['addcost']);
        }
        if ('=GEWICHT' == strtoupper($localService['cost'])) {
            $ShippingDetails['ShippingServiceOptions'][$key]['ShippingServiceCost'] = '=GEWICHT';
            $ShippingDetails['ShippingServiceOptions'][$key]['ShippingServiceAdditionalCost'] = 0.0;
        }
        if (!isset($next_service)
            && (is_numeric($ShippingDetails['ShippingServiceOptions'][$key]['ShippingServiceCost']))
            && (0.0 == $ShippingDetails['ShippingServiceOptions'][$key]['ShippingServiceCost'])
            && (0.0 == $ShippingDetails['ShippingServiceOptions'][$key]['ShippingServiceAdditionalCost'])
        ) {
            $ShippingDetails['ShippingServiceOptions'][$key]['FreeShipping'] = 1;
        }
        $next_service = true; # FreeShipping darf nur beim 1ten Service gesetzt sein
    }
    $row['DispatchTimeMax'] = array_key_exists('dispatchTime', $itemDetails) ? $itemDetails['dispatchTime'] : getDBConfigValue('ebay.DispatchTimeMax', $_MagnaSession['mpID'], 30);
    if (isset($itemDetails['localProfile'])) {
        $ShippingDetails['LocalProfile'] = $itemDetails['localProfile'];
    }
    if ('on' == $itemDetails['localPromotionalDiscount']) {
        $ShippingDetails['LocalPromotionalDiscount'] = 'true';
    } else {
        $ShippingDetails['LocalPromotionalDiscount'] = 'false';
    }
    $ShippingDetails['InternationalShippingServiceOption'] = array();
    if (is_array($itemDetails['ebay_default_shipping_international'])) {
        foreach ($itemDetails['ebay_default_shipping_international'] as $key => $intlService) {
            if (empty($intlService['service']))
                break;
            $ShippingDetails['InternationalShippingServiceOption'][$key] = array(
                'ShippingService'     => $intlService['service'],
                'ShippingServiceCost' => priceToFloat($intlService['cost']),
                'ShipToLocation'      => $intlService['location']
            );
        }
        if (array_key_exists('addcost', $intlService)
            && isset($intlService['addcost'])
        ) {
            $ShippingDetails['InternationalShippingServiceOption'][$key]['ShippingServiceAdditionalCost'] = priceToFloat($intlService['addcost']);
        }
        if ('=GEWICHT' == strtoupper($intlService['cost'])) {
            $ShippingDetails['InternationalShippingServiceOption'][$key]['ShippingServiceCost'] = '=GEWICHT';
            $ShippingDetails['InternationalShippingServiceOption'][$key]['ShippingServiceAdditionalCost'] = 0.0;
        }
    }
    if (0 == count($ShippingDetails['InternationalShippingServiceOption'])) {
        unset($ShippingDetails['InternationalShippingServiceOption']);
    }
    if (isset($itemDetails['internationalProfile'])) {
        $ShippingDetails['InternationalProfile'] = $itemDetails['internationalProfile'];
    }
    if ('on' == $itemDetails['internationalPromotionalDiscount']) {
        $ShippingDetails['InternationalPromotionalDiscount'] = 'true';
    } else {
        $ShippingDetails['InternationalPromotionalDiscount'] = 'false';
    }
    $row['ShippingDetails'] = json_encode($ShippingDetails);
    # Noch nicht verifiziert:
    $row['Verified'] = 'OPEN';
    # ePID gespeichert?
    if (isset($itemDetails['ePID'.$pID])) {
        $row['ePID'] = $itemDetails['ePID'.$pID];
    }
    return $row;
}

function eBayInsertPrepareData($data) {
    foreach (array('ItemSpecifics', 'Attributes') as $sAttributeOrSpecific) {
        if (array_key_exists($sAttributeOrSpecific, $data)) {
            $aAttribute = json_decode(fixBrokenJsonUmlauts($data[$sAttributeOrSpecific]), true);
            $aAttribute = json_decode($data[$sAttributeOrSpecific], true);
            if (!empty($aAttribute) && is_array($aAttribute)) {
                $aMyAttribute = array();
                foreach ($aAttribute as $sKey => $aValue) {
                    foreach ($aValue as $sAttributeKey => $aAttributeValue) {
                        $aMyAttribute[$sKey][pack('H*', $sAttributeKey)] = $aAttributeValue;
                    }
                }
                $data[$sAttributeOrSpecific] = json_encode($aMyAttribute);
            }
        }
    }
    $data['topPrimaryCategory'] = $data['PrimaryCategory'] == NULL ? '' : $data['PrimaryCategory'];
    $data['topSecondaryCategory'] = $data['topSecondaryCategory'] == NULL ? '' : $data['SecondaryCategory'];
    $data['topStoreCategory1'] = $data['topStoreCategory1'] == NULL ? '' : $data['StoreCategory'];
    $data['topStoreCategory2'] = $data['topStoreCategory2'] == NULL ? '' : $data['StoreCategory2'];
    if (array_key_exists('MobileDescription', $data)
        && !empty($data['MobileDescription'])
        && (getDBConfigValue('ebay.template.usemobile', $_MagnaSession['mpID'], false) === 'true')) {
        $data['MobileDescription'] = strip_tags($data['MobileDescription'], '<ol></ol><ul></ul><li></li><br><br/><br />');
        $data['MobileDescription'] = trim(substr(strip_tags($data['MobileDescription'], '<ol></ol><ul></ul><li></li><br><br/><br />'), 0, 1021)); // eBay accepts 800, 1021 are for our table
    }
    /* {Hook} "eBayInsertPrepareData": Enables you to modify the prepared product data before it will be saved.<br>
       Variables that can be used:
       <ul>
        <li><code>$data</code>: The data of a product.</li>
        <li><code>$data['mpID']</code>: The ID of the marketplace.</li>
       </ul>
     */
    if (($hp = magnaContribVerify('eBayInsertPrepareData', 1)) !== false) {
        require($hp);
    }
    MagnaDB::gi()->insert(TABLE_MAGNA_EBAY_PROPERTIES, $data, true);
}

function SaveEBaySingleProductProperties($pID, $itemDetails) {
    global $_MagnaSession;
    $row = prepareEBayPropertiesRow($pID, $itemDetails);
    $row['Title'] = substr(trim(strip_tags(html_entity_decode($itemDetails['Title']))), 0, 80);
    if (('on' == $itemDetails['enableSubtitle']) && !empty($itemDetails['Subtitle'])) {
        $row['Subtitle'] = substr(trim(strip_tags($itemDetails['Subtitle'])), 0, 55);
    }
    if (!empty($itemDetails['PictureURL'])) {
        if (is_array($itemDetails['PictureURL'])) {
            $row['PictureURL'] = json_encode($itemDetails['PictureURL']);
        } else {
            $row['PictureURL'] = trim($itemDetails['PictureURL']);
        }
    }
    if (!empty($itemDetails['PictureURLVariation'])) {
        $row['VariationPictures'] = json_encode($itemDetails['PictureURLVariation']);
    }
    if (!empty($itemDetails['VariationDimensionForPictures'])) {
        $row['VariationDimensionForPictures'] = trim($itemDetails['VariationDimensionForPictures']);
    }
    //	if (array_key_exists('PicturePackPurge', $itemDetails) && ('on' == $itemDetails['PicturePackPurge'])){
    //  immer an
    $row['eBayPicturePackPurge'] = '1';
    //	}
    $row['GalleryType'] = $itemDetails['GalleryType'];
    if ('on' == $itemDetails['privateListing']) {
        $row['PrivateListing'] = '1';
    }
    if (('on' == $itemDetails['bestOfferEnabled']) && ('Chinese' != $itemDetails['ListingType'])) {
        $row['BestOfferEnabled'] = '1';
    }
    if (array_key_exists('plus', $itemDetails) && ('on' == $itemDetails['plus']) && ('Chinese' != $itemDetails['ListingType'])) {
        $row['eBayPlus'] = '1';
    }
    if (!empty($itemDetails['startTime'])) {
        $row['StartTime'] = $itemDetails['startTime'];
    }

    // only set price if a chinese auction otherwise set it to zero
    if (('true' == $itemDetails['isPriceFrozen']) && ('Chinese' == $itemDetails['ListingType'])) {
        if ($itemDetails['frozenPrice'] == (string)(float)$itemDetails['frozenPrice']) {
            $row['Price'] = $itemDetails['frozenPrice'];
        } else {
            $row['Price'] = priceToFloat($itemDetails['frozenPrice']);
        }
        # Einfrieren, aber nicht ausgefuellt => berechneten Preis einfrieren
        if (0.00 == $row['Price'])
            $row['Price'] = priceToFloat($itemDetails['Price']);
    } else {
        $row['Price'] = (float)0;
    }
    if (isset($itemDetails['isPriceFrozen'])
        && isset($itemDetails['enableBuyItNowPrice'])
        && !empty($itemDetails['BuyItNowPrice'])
        && ('Chinese' == $itemDetails['ListingType'])
    ) {
        if ($itemDetails['BuyItNowPrice'] == (string)(float)$itemDetails['BuyItNowPrice']) {
            $row['BuyItNowPrice'] = $itemDetails['BuyItNowPrice'];
        } else {
            $row['BuyItNowPrice'] = priceToFloat($itemDetails['BuyItNowPrice']);
        }
    }
    $row['Description'] = trim($itemDetails['Description']);
    if (getDBConfigValue('ebay.template.usemobile', $_MagnaSession['mpID'], false) === 'true') {
        $row['MobileDescription'] = trim(strip_tags($itemDetails['MobileDescription'], '<ol></ol><ul></ul><li></li><br><br/><br />'));
    } else {
        $row['MobileDescription'] = '';
    }
    #echo print_m($row, 'final');
    # doppelte Eintraege verhindern
    if ('artNr' == getDBConfigValue('general.keytype', '0')) {
        MagnaDB::gi()->delete(TABLE_MAGNA_EBAY_PROPERTIES, array(
            'mpID'           => $_MagnaSession['mpID'],
            'products_model' => $row['products_model']
        ));
    } else {
        MagnaDB::gi()->delete(TABLE_MAGNA_EBAY_PROPERTIES, array(
            'mpID'        => $_MagnaSession['mpID'],
            'products_id' => $pID
        ));
    }

    // reset frozen price if not chinese auction
    if ('Chinese' != $itemDetails['ListingType']) {
        $row['Price'] = (float)0;
    }
    $row['mwst'] = $itemDetails['mwst'];
    $row['PreparedTs'] = date('Y-m-d H:i:s');
    eBayInsertPrepareData($row);
}

function eBaySubstituteTemplate($mpID, $pID, $template, $substitution) {
    /* {Hook} "eBaySubstituteTemplate": Enables you to extend the eBay Template substitution (e.g. use your own placeholders).<br>
    Variables that can be used:
    <ul><li><code>$mpID</code>: The ID of the marketplace.</li>
    <li><code>$pID</code>: The ID of the product (Table <code>products.products_id</code>).</li>
    <li><code>$template</code>: The eBay product template.</li>
    <li><code>$substitution</code>: Associative array. Keys are placeholders, Values are their content.</li>
    </ul>
    */
    if (($hp = magnaContribVerify('eBaySubstituteTemplate', 1)) !== false) {
        require($hp);
    }

    return substituteTemplate($template, $substitution);
}

function SaveEBayMultipleProductProperties($pIDs, $itemDetails) {
    global $_MagnaSession;
    # Analog zu SaveEBaySingleProductProperties, aber
    # Title, (Subtitle), PictureURL aus der Datenbank
    # und Descriptions zusammenbauen
    if (!is_array($pIDs)) {
        if (!empty($pIDs))
            $pIDs = array($pIDs);
        else return false;
    }
    $more_data_select = 'SELECT p.products_id products_id, p.products_model products_model, pd.products_name Title, ';
    if (MagnaDB::gi()->columnExistsInTable('products_short_description', TABLE_PRODUCTS_DESCRIPTION)) {
        $more_data_select .= ' pd.products_short_description products_short_description, ';
    } else {
        $more_data_select .= ' \'\' products_short_description, ';
    }
    $language_code = MagnaDB::gi()->fetchOne('SELECT code FROM '.TABLE_LANGUAGES.' WHERE languages_id = '.getDBConfigValue('ebay.lang', $_MagnaSession['mpID'], 999));
    if (false === $language_code)
        $language_code = $_SESSION['magna']['selected_language'];
    $productsStoreIdConstraint = (MagnaDB::gi()->columnExistsInTable('products_store_id', TABLE_PRODUCTS_DESCRIPTION)
        ? '
				AND pd.products_store_id = '.getDBConfigValue('ebay.shopmandant', $_MagnaSession['mpID']).' '
        : '');
    $more_data_select .= ' pd.products_description description, 
				p.products_price Price,	p.products_image image 
				FROM '.TABLE_PRODUCTS.' p, '.TABLE_PRODUCTS_DESCRIPTION.' pd
				WHERE p.products_id = pd.products_id
				AND pd.language_code = \''.$language_code.'\''.$productsStoreIdConstraint.'
				AND p.products_id IN ('.implode(', ', $pIDs).')';

    $more_data = MagnaDB::gi()->fetchArray($more_data_select);
    $imagePath = getDBConfigValue('ebay.imagepath', $_MagnaSession['mpID']);
    $eBayTemplate = getDBConfigValue('ebay.template.content', $_MagnaSession['mpID']);
    $eBayTitleTemplate = getDBConfigValue('ebay.template.name', $_MagnaSession['mpID'], '#TITLE#');
    $preparedTs = date('Y-m-d H:i:s');
    foreach ($more_data as $dataRow) {
        $row = prepareEBayPropertiesRow($dataRow['products_id'], $itemDetails);
        $row['Title'] = substr(eBaySubstituteTemplate($_MagnaSession['mpID'], $dataRow['products_id'], $eBayTitleTemplate, array(
            '#TITLE#' => strip_tags($dataRow['products_name']),
            '#ARTNR#' => $dataRow['products_model']
        )), 0, 80);
        if ('on' == $itemDetails['enableSubtitle'] && !empty($dataRow['products_short_description'])) {
            $row['Subtitle'] = substr(trim(strip_tags($dataRow['products_short_description'])), 0, 55);
        }
        if ('on' == $itemDetails['privateListing']) {
            $row['PrivateListing'] = '1';
        }
        if (('on' == $itemDetails['bestOfferEnabled']) && ('Chinese' != $itemDetails['ListingType'])) {
            $row['BestOfferEnabled'] = '1';
        }
        if (('on' == $itemDetails['plus']) && ('Chinese' != $itemDetails['ListingType'])) {
            $row['eBayPlus'] = '1';
        }
        if (!empty($itemDetails['startTime'])) {
            $row['StartTime'] = $itemDetails['startTime'];
        }
        $row['Price'] = (float)0; # Preis nicht einfrieren
        if (('Chinese' == $itemDetails['ListingType']) && getDBConfigValue(array('ebay.chinese.buyitnow.price.active', 'val'), $_MagnaSession['mpID'])) {
            $row['BuyItNowPrice'] = makePrice($dataRow['products_id'], 'BuyItNowPrice');
        }
        //	if (array_key_exists('PicturePackPurge', $itemDetails) && ('on' == $itemDetails['PicturePackPurge'])){
        //  immer an
        $row['eBayPicturePackPurge'] = '1';
        //	}
        if (getDBConfigValue(array('ebay.picturepack', 'val'), $_MagnaSession['mpID'])) {
            $row['PictureURL'] = json_encode(MLProduct::gi()->setLanguage($_SESSION['languages_id'])->getAllImagesByProductsId($dataRow['products_id']));
        } else {
            $row['PictureURL'] = empty($dataRow['image']) ? '' : $imagePath.$dataRow['image'];
        }
        $row['GalleryType'] = $itemDetails['GalleryType'];
        $row['mwst'] = $itemDetails['mwst'];
        # Descriptions zusammenbauen
        $substitution = array(
            '#TITLE#'            => fixHTMLUTF8Entities($dataRow['Title']),
            '#ARTNR#'            => $dataRow['products_model'],
            '#PID#'              => $dataRow['products_id'],
            '#SKU#'              => magnaPID2SKU($dataRow['products_id']),
            '#SHORTDESCRIPTION#' => $dataRow['products_short_description'],
            '#DESCRIPTION#'      => stripLocalWindowsLinks($dataRow['description']),
            '#PICTURE1#'         => empty($dataRow['image']) ? '' : $imagePath.$dataRow['image'],
            '#WEIGHT#'           => ((float)$dataRow['products_weight'] > 0) ? $dataRow['products_weight'] : '',
        );
        $row['Description'] = substitutePictures(eBaySubstituteTemplate(
            $_MagnaSession['mpID'], $dataRow['products_id'], $eBayTemplate, $substitution
        ), $dataRow['products_id'], $imagePath);

        # Mobile Descriptions
        if (getDBConfigValue('ebay.template.usemobile', $_MagnaSession['mpID'], false) === 'true') {
            $msubstitution = array(
                '#TITLE#'            => fixHTMLUTF8Entities($dataRow['products_name']),
                '#ARTNR#'            => $dataRow['products_model'],
                '#PID#'              => $dataRow['products_id'],
                '#SKU#'              => magnaPID2SKU($dataRow['products_id']),
                '#SHORTDESCRIPTION#' => $dataRow['products_short_description'],
                '#DESCRIPTION#'      => stripLocalWindowsLinks($dataRow['description']),
                '#WEIGHT#'           => ((float)$dataRow['products_weight'] > 0) ? $dataRow['products_weight'] : '',
            );
            $row['MobileDescription'] = strip_tags(eBaySubstituteTemplate(
                $_MagnaSession['mpID'], $dataRow['products_id'], $eBayTemplate, $msubstitution
            ), '<ol></ol><ul></ul><li></li><br><br/><br />');
        } else {
            $row['MobileDescription'] = '';
        }


        # ggf vorher eingefrorene Preise nicht plattmachen &
        # doppelte Eintraege verhindern
        if ('artNr' == getDBConfigValue('general.keytype', '0')) {
            $row['Price'] = (float)MagnaDB::gi()->fetchOne('SELECT Price FROM '.TABLE_MAGNA_EBAY_PROPERTIES.' WHERE mpID = '.$_MagnaSession['mpID'].' AND products_model = \''.MagnaDB::gi()->escape($row['products_model']).'\'');
            MagnaDB::gi()->delete(TABLE_MAGNA_EBAY_PROPERTIES, array('mpID' => $_MagnaSession['mpID'], 'products_model' => $row['products_model']));
        } else {
            $row['Price'] = (float)MagnaDB::gi()->fetchOne('SELECT Price FROM '.TABLE_MAGNA_EBAY_PROPERTIES.' WHERE mpID = '.$_MagnaSession['mpID'].' AND products_id = '.$dataRow['products_id']);
            MagnaDB::gi()->delete(TABLE_MAGNA_EBAY_PROPERTIES, array('mpID' => $_MagnaSession['mpID'], 'products_id' => $dataRow['products_id']));
        }
        $row['PreparedTs'] = $preparedTs;
        eBayInsertPrepareData($row);
    }
}

function magnalisterEbayGetPriceByType($iProductsId, $sPriceType = false) {
    global $_MagnaSession;
    if ('artNr' == getDBConfigValue('general.keytype', '0')) {
        $preparedPriceQuery = "
			SELECT ".('BuyItNowPrice' == $sPriceType ? 'ep.BuyItNowPrice' : 'ep.Price')."
			  FROM ".TABLE_MAGNA_EBAY_PROPERTIES." ep, ".TABLE_PRODUCTS." p
			 WHERE     ep.products_model = p.products_model
			       AND p.products_id = ".$iProductsId."
			       AND ep.mpID = ".$_MagnaSession['mpID']."
			 LIMIT 1
		";
    } else {
        $preparedPriceQuery = "
			SELECT ".('BuyItNowPrice' == $sPriceType ? 'ep.BuyItNowPrice' : 'ep.Price')."
			  FROM ".TABLE_MAGNA_EBAY_PROPERTIES." ep
			 WHERE     ep.products_id = ".$iProductsId."
			       AND ep.mpID = ".$_MagnaSession['mpID']."
			LIMIT 1
		";
    }

    return MagnaDB::gi()->fetchOne($preparedPriceQuery);
}

/* Zahlungsarten eBay:
-AmEx
CashInPerson
-CashOnPickup
-CCAccepted
-COD
CODPrePayDelivery # reserved for future use
CustomCode        # reserved for future use
-Diners           # CC 
-Discover         # CC
-ELV              # Lastschrift 
Escrow            # reserved for future use 
-IntegratedMerchantCreditCard # CC
LoanCheck
-MOCC             # Money order/cashiers check 
-Moneybookers
-MoneyXferAccepted# Direct transfer of money
-MoneyXferAcceptedInCheckout
None
Other
OtherOnlinePayments
PaisaPayAccepted  # India only
PaisaPayEscrow    # India only
PaisaPayEscrowEMI # India only
Paymate           # US only
PaymentSeeDescription
-PayOnPickup
-PayPal
-PersonalCheck
PostalTransfer    # reserved for future use
PrePayDelivery    # reserved for future use
ProPay            # US only
StandardPayment
-VisaMC
*/
function getPaymentClassForEbayPaymentMethod($paymentMethod) {
    switch ($paymentMethod) {
        case ('MOCC'):
        case ('PersonalCheck'):
        case ('MoneyXferAccepted'):
        case ('MoneyXferAcceptedInCheckout'):
            $sRet = ML_EBAY_PAYMENT_MOCC;
            break;
        case ('COD'):
            $sRet = ML_EBAY_PAYMENT_COD;
            break;
        case ('AmEx'):
            $sRet = ML_EBAY_PAYMENT_AMEX;
            break;
        case ('VisaMC'):
            $sRet = ML_EBAY_PAYMENT_VISA;
            break;
        case ('CCAccepted'):
        case ('Diners'):
        case ('Discover'):
        case ('IntegratedMerchantCreditCard'):
            $sRet = ML_EBAY_PAYMENT_CC;
            break;
        case ('ELV'):
            $sRet = ML_EBAY_PAYMENT_ELV;
            break;
        case ('PayUponInvoice'):
            $sRet = ML_EBAY_PAYMENT_INVOICE;
            break;
        case ('CashOnPickup'):
        case ('PayOnPickup'):
            $sRet = ML_EBAY_PAYMENT_CASH;
            break;
        default:
            /*
                Monebookers, PayPal
             */
            $sRet = $paymentMethod;
            break;
    }
    return $sRet;
}

/*
 Datensatz in customers_addresses updaten
 Wenn $blBillinAddressToo, auch die Rechnungsadresse (anhand der Versandadresse)
 aufrufen nachdem orders Datensatz upgedatet
*/
function updateShippingAddressFromOrder($iOrdersId, $blBillinAddressToo = false) {
    $aOrder = MagnaDB::gi()->fetchRow('SELECT *
		 FROM '.TABLE_ORDERS.'
		WHERE orders_id = '.$iOrdersId);
    $oAddressBookIDs = array('delivery_address_book_id' => $aOrder['delivery_address_book_id']);
    if ($blBillinAddressToo) {
        $oAddressBookIDs['billing_address_book_id'] = $aOrder['billing_address_book_id'];
    }
    foreach ($oAddressBookIDs as $iAddressBookId) {
        MagnaDB::gi()->query('UPDATE '.TABLE_CUSTOMERS_ADDRESSES.'
			SET customers_firstname      = \''.$aOrder['delivery_firstname'].'\',
			    customers_lastname       = \''.$aOrder['delivery_lastname'].'\',
			    customers_street_address = \''.$aOrder['delivery_street_address'].'\','.
            ((MagnaDB::gi()->columnExistsInTable('customers_address_addition', TABLE_CUSTOMERS_ADDRESSES) && array_key_exists('delivery_address_addition', $aOrder)) ? '
		            customers_address_addition = \''.$aOrder['delivery_address_addition'].'\','
                : '').'
			    customers_suburb         = \''.$aOrder['delivery_suburb'].'\',
			    customers_postcode       = \''.$aOrder['delivery_postcode'].'\',
			    customers_city           = \''.$aOrder['delivery_city'].'\',
			    customers_country_code   = \''.$aOrder['delivery_country_code'].'\',
			    last_modified            = NOW()
			WHERE address_book_id = '.$iAddressBookId);

    }
    if ($blBillinAddressToo) {
        MagnaDB::gi()->query('UPDATE '.TABLE_ORDERS.'
			SET billing_firstname      = delivery_firstname,
			    billing_lastname       = delivery_lastname,
			    billing_company        = delivery_company,
			    billing_company_2      = delivery_company_2,
			    billing_company_3      = delivery_company_3,
			    billing_street_address = delivery_street_address,'.
            (MagnaDB::gi()->columnExistsInTable('delivery_address_addition', TABLE_ORDERS) ? '
		            billing_address_addition = delivery_address_addition,'
                : '').'
			    billing_suburb         = delivery_suburb,
			    billing_city           = delivery_city,
			    billing_postcode       = delivery_postcode,
			    billing_zone           = delivery_zone,
			    billing_zone_code      = delivery_zone_code,
			    billing_country        = delivery_country,
			    billing_country_code   = delivery_country_code
			WHERE orders_id = '.$iOrdersId);
    }
}

/*
 Es passiert, dass Datensätze in xt_customers_addresses nicht mit xt_orders übereinstimmen
 (offenbar passiert der Fehler beim eBay Bestell-Update).
 Der Fehler kann nicht reproduziert werden (wahrscheinlich abgebrochene DB-Verbindung),
 daher werden die Datensätze am Ende der Bestell-Update noch mal gecheckt.
 */
function checkAndCorrectWrongShippingAddresses() {
    $aOrderIDs = MagnaDB::gi()->fetchArray('SELECT o.orders_id
		FROM '.TABLE_ORDERS.' o, '.TABLE_CUSTOMERS_ADDRESSES.' a, '.TABLE_MAGNA_ORDERS.' mo
		WHERE mo.orders_id=o.orders_id AND o.delivery_address_book_id = a.address_book_id AND (
		    o.delivery_firstname<>a.customers_firstname
		 OR o.delivery_lastname<>a.customers_lastname
		 OR o.delivery_street_address<>a.customers_street_address
		 OR o.delivery_suburb<>a.customers_suburb
		 OR o.delivery_postcode<>a.customers_postcode
		 OR o.delivery_city<>a.customers_city
		 OR o.delivery_country_code<>a.customers_country_code
		) AND mo.platform=\''.'ebay'.'\' order by o.orders_id desc limit 5', true);
    foreach ($aOrderIDs as $iOrdersId) {
        updateShippingAddressFromOrder($iOrdersId, false);
    }
}
