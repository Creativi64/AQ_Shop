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
 * (c) 2010 - 2014 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */

defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');


require_once(DIR_MAGNALISTER_MODULES.'magnacompatible/crons/MagnaCompatibleSyncOrderStatus.php');
require_once(DIR_MAGNALISTER_MODULES.'amazon/amazonFunctions.php');

class AmazonSyncOrderStatus extends MagnaCompatibleSyncOrderStatus {

    public function __construct($mpID, $marketplace) {
        global $_MagnaSession;
        $_MagnaSession['mpID'] = $mpID;
        upgradeOrderSyncSettings();
    parent::__construct($mpID, $marketplace);
    }
    /*
       Each status has its own address
       Carrier and shipmethod don't epend on status
     */
    protected function getConfigKeys() {
        $keys = parent::getConfigKeys();
        $keys['TrackingCodeMatchingTable'] = array (
            'key' => 'orderstatus.carrier.trackingcode.table',
            'default' => 'false',
        );
        $keys['TrackingCodeMatchingAlias'] = array (
            'key' => 'orderstatus.carrier.trackingcode.alias',
            'default' => 'false',
        );
        $keys['CarrierMatch'] = array (
            'key' => 'orderstatus.carrier.default',
            'default' => 'false',
        );
        $keys['CarrierDBMatchingTable'] = array (
            'key' => 'orderstatus.carrier.carrierDBMatching.table',
            'default' => 'false',
        );
        $keys['CarrierDBMatchingAlias'] = array (
            'key' => 'orderstatus.carrier.carrierDBMatching.alias',
            'default' => 'false',
        );
        $keys['CarrierTextfield'] = array (
            'key' => 'orderstatus.carrier.textfield',
            'default' => '',
        );
        $keys['ShipMethodMatch'] = array (
            'key' => 'orderstatus.shipmethod.default',
            'default' => 'false',
        );
        $keys['ShipMethodDBMatchingTable'] = array (
            'key' => 'orderstatus.shipmethod.shipmethodDBMatching.table',
            'default' => 'false',
        );
        $keys['ShipMethodDBMatchingAlias'] = array (
            'key' => 'orderstatus.shipmethod.shipmethodDBMatching.alias',
            'default' => 'false',
        );
        $keys['ShipMethodTextfield'] = array (
            'key' => 'orderstatus.shipmethod.textfield',
            'default' => '',
        );
        $keys['StatusShipped'] = array (
            'key' => 'orderstatus.shipped.shipped',
            'default' => '',
        );
        $keys['ShippedNames'] = array (
            'key' => 'orderstatus.shipped.name',
            'default' => '',
        );
        $keys['ShippedLines1'] = array (
            'key' => 'orderstatus.shipped.line1',
            'default' => '',
        );
        $keys['ShippedLines2'] = array (
            'key' => 'orderstatus.shipped.line2',
            'default' => '',
        );
        $keys['ShippedLines3'] = array (
            'key' => 'orderstatus.shipped.line3',
            'default' => '',
        );
        $keys['ShippedCities'] = array (
            'key' => 'orderstatus.shipped.city',
            'default' => '',
        );
        $keys['ShippedCounties'] = array (
            'key' => 'orderstatus.shipped.county',
            'default' => '',
        );
        $keys['ShippedStates'] = array (
            'key' => 'orderstatus.shipped.state',
            'default' => '',
        );
        $keys['ShippedPostalcodes'] = array (
            'key' => 'orderstatus.shipped.postalcode',
            'default' => '',
        );
        $keys['ShippedCountries'] = array (
            'key' => 'orderstatus.shipped.country',
            'default' => '',
        );
        return $keys;
   }

    /**
     * Fetches a carrier if supported by the marketplace.
     *   more priority on matching
     * @return string
     *   The carrier
     */
    protected function getCarrier($orderId) {
        /* {Hook} "AmazonSyncOrderStatus_getCarrier": Enables you to replace the Carrier identification by your own functionality
           Variables that can be used:
           <ul><li>$orderId: The ID of the current order (read only)</li>
               <li>$sCarrier: Your calculated Carrier</li>
           </ul>
        */
        if (($hp = magnaContribVerify('AmazonSyncOrderStatus_getCarrier', 1)) !== false) {
            require($hp);
            if (isset($sCarrier) && !empty($sCarrier)) {
                return $sCarrier;
            }
        }
        if ($this->config['CarrierMatch'] == 'dbmatch') {
            $this->config['CarrierMatchingTable'] = $this->config['CarrierDBMatchingTable'];
            $this->config['CarrierMatchingAlias'] = $this->config['CarrierDBMatchingAlias'];
            return parent::getCarrier($orderId);
        } else if($this->config['CarrierMatch'] == 'textfield') {
            return $this->config['CarrierTextfield'];
        } else {
            return $this->config['CarrierMatch'];
        }
    }

    /**
     * Fetches a carrier if supported by the marketplace.
     *   more priority on matching
     * @return string
     *   The carrier
     */
    protected function getShipMethod($orderId) {
        /* {Hook} "AmazonSyncOrderStatus_getShipMethod": Enables you to replace the Shipping Method identification by your own functionality
           Variables that can be used:
           <ul><li>$orderId: The ID of the current order (read only)</li>
               <li>$sShippingMethod: Your calculated Shipping Method</li>
           </ul>
        */
        if (($hp = magnaContribVerify('AmazonSyncOrderStatus_getShipMethod', 1)) !== false) {
            require($hp);
            if (isset($sShippingMethod) && !empty($sShippingMethod)) {
                return $sShippingMethod;
            }
        }
        if ($this->config['ShipMethodMatch'] == 'dbmatch') {
            $this->config['CarrierMatchingTable'] = $this->config['ShipMethodDBMatchingTable'];
            $this->config['CarrierMatchingAlias'] = $this->config['ShipMethodDBMatchingAlias'];
            return parent::getCarrier($orderId);
        } else if($this->config['ShipMethodMatch'] == 'textfield') {
            return $this->config['ShipMethodTextfield'];
        } else {
            return $this->config['ShipMethodMatch'];
        }
    }
    
    protected function prepareSingleOrder($date) {
        if (in_array($this->oOrder['orders_status_shop'], $this->config['StatusShipped'])) {
            $this->confirmations[] = $this->confirmShipment($date);
        } else if ($this->oOrder['orders_status_shop'] == $this->config['StatusCancelled']) {
            $this->cancellations[] = $this->cancelOrder($date);
        }
        // this marketplace returns orders if successful
        $this->oOrder['__dirty'] = false;
    }

    /**
     * Checks whether the status of the current order should be synchronized with
     * the marketplace.
     * + Amazon AFN order should not be synced
     * @return bool
     */
    protected function isProcessable() {
        if (   is_array($this->oOrder['internaldata'])
            && array_key_exists('FulfillmentChannel', $this->oOrder['internaldata'])
            && $this->oOrder['internaldata']['FulfillmentChannel'] == 'AFN'
        ) {
            return false;
        }
        return (in_array($this->oOrder['orders_status_shop'], $this->config['StatusShipped'])
            || ($this->oOrder['orders_status_shop'] == $this->config['StatusCancelled']));
    }
    
    /**
     * Builds an element for the ConfirmShipment request.
     * @return array
     */
    protected function confirmShipment($date) {
        $cfirm = array (
            'MOrderID' => $this->oOrder['special'],
            'ShippingDate' => localTimeToMagnaTime($date),
        );
        $this->oOrder['data']['ML_LABEL_SHIPPING_DATE'] = $cfirm['ShippingDate'];
        
        $trackercode = $this->getTrackingCode($this->oOrder['orders_id']);
        $carrier     = $this->getCarrier($this->oOrder['orders_id']);
        $shipmethod   = $this->getShipMethod($this->oOrder['orders_id']);
        if (false != $carrier) {
            $this->oOrder['data']['ML_LABEL_CARRIER'] = $cfirm['Carrier'] = $carrier;
        }
        if (false != $shipmethod) {
            $this->oOrder['data']['ML_LABEL_SHIPMETHOD'] = $cfirm['ShipMethod'] = $shipmethod;
        }
        if (false != $trackercode) {
            $this->oOrder['data']['ML_LABEL_TRACKINGCODE'] = $cfirm['TrackingCode'] = $trackercode;
        }

        $iAddressDataPosition = array_search($this->oOrder['orders_status_shop'], $this->config['StatusShipped']);
        $aAddressData = array (
            'Name' => $this->config['ShippedNames'][$iAddressDataPosition],
            'Line1' => $this->config['ShippedLines1'][$iAddressDataPosition],
            'Line2' => $this->config['ShippedLines2'][$iAddressDataPosition],
            'Line3' => $this->config['ShippedLines3'][$iAddressDataPosition],
            'City' => $this->config['ShippedCities'][$iAddressDataPosition],
            'County' => $this->config['ShippedCounties'][$iAddressDataPosition],
            'State' => $this->config['ShippedStates'][$iAddressDataPosition],
            'Postalcode' => $this->config['ShippedPostalcodes'][$iAddressDataPosition],
            'Country' => $this->config['ShippedCountries'][$iAddressDataPosition],
        );
        foreach ($aAddressData as $sAddrKey => $sAddValue) {
            if (empty($sAddValue)) unset($aAddressData[$sAddrKey]);
        }

        if (!empty($aAddressData)) {
            $this->oOrder['data'] = array_merge($this->oOrder['data'], $aAddressData);
            $cfirm = array_merge($cfirm, $aAddressData);
        }
        
        // flag order as dirty, meaning that it has to be saved.
        $this->oOrder['__dirty'] = true;
        return $cfirm;
    }

    protected function getTrackingCode($orderId) {
        $sCode = magnaAmazonFetchTrackingCode3rdParty($orderId, $this->mpID);
        if (empty($sCode)) {
            $sCode = parent::getTrackingCode($orderId);
        }
        return $sCode;
    }

    /**
     * Processes the confirmations send from the API.
     * Can be overwritten from subclasses if required.
     *
     * @param array $result
     *   The entire API result.
     * @return void
     */
    protected function processResponseConfirmations($result) {
        if (!isset($result[$this->confirmationResponseField][0])) {
            return;
        }
        foreach ($result[$this->confirmationResponseField] as $cData) {
            if (!isset($cData['AmazonOrderID'])) {
                continue;
            }
            $oOrder = &$this->getFromLookupTable($cData['AmazonOrderID']);
            if ($oOrder !== null) {
                $this->storeConfirmation($oOrder, $cData);
            }
        }
    }

    /**
     * Adds confirmation information to the order item
     * @param array &$oOrder
     *   The order item
     * @param array $cData
     *   The confirmation element specific to this order
     * @return void
     */
    protected function storeConfirmation(&$oOrder, $cData) {
        // Flag as dirty. Probably is already flagged as dirty.
        $oOrder['__dirty'] = true;

        if (isset($cData['BatchID'])) {
            $this->aOrder['BatchID'] = $cData['BatchID'];
        }

    }

}
