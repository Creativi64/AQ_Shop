<?php
/*
 #########################################################################
 #                       xt:Commerce Shopsoftware
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # Copyright 2021 xt:Commerce GmbH All Rights Reserved.
 # This file may not be redistributed in whole or significant part.
 # Content of this file is Protected By International Copyright Laws.
 #
 # ~~~~~~ xt:Commerce Shopsoftware IS NOT FREE SOFTWARE ~~~~~~~
 #
 # https://www.xt-commerce.com
 #
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # @copyright xt:Commerce GmbH, www.xt-commerce.com
 #
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # xt:Commerce GmbH, Maximilianstrasse 9, 6020 Innsbruck
 #
 # office@xt-commerce.com
 #
 #########################################################################
 */

defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_ship_and_track/classes/constants.php';

/**
 * Logik, DB-Zugriff, Tools
 */
class tracking extends xt_backend_cls
{
    protected function setTracking($orders_id, $shipperId, $trackinCodes, $sendMail, $statusId = 0)
    {
        global $db;

        $result = new stdClass();
        $result->success = false;

        if (!$statusId)
        {
            $statusId = $db->GetOne('SELECT `'.COL_TRACKING_STATUS_ID_PK.'` FROM '.TABLE_TRACKING_STATUS.' WHERE `'.COL_TRACKING_STATUS_CODE."`=0 AND `".COL_TRACKING_SHIPPER_ID. "`=?", array($shipperId) );
        }

        $insertData = array();
        foreach($trackinCodes as $code)
        {
            $insertData[] = array(
                COL_TRACKING_CODE => $code,
                COL_TRACKING_ORDER_ID => $orders_id,
                COL_TRACKING_STATUS_ID => $statusId,
                COL_TRACKING_SHIPPER_ID => $shipperId
            );
            /*
             $trackingId = $db->GetOne('SELECT `'.COL_TRACKING_ID_PK.'` FROM '.TABLE_TRACKING.' WHERE `'.COL_TRACKING_CODE."`=?", array($code));
            if ($trackingId)
            {
                $insertData[count($insertData)-1][COL_TRACKING_ID_PK] = $trackingId;
            }
            */
            try {
                $db->AutoExecute(TABLE_TRACKING ,$insertData[count($insertData)-1]);
                $result->tracking_id = $db->insert_id();
            } catch (exception $e) {
                $result->msg = $e->msg;
                return $result;
            }
        }
        if ($sendMail)
        {
            $trackingCodes = array();
            foreach($insertData as $v)
            {
                $trackingCodes[] = $v[COL_TRACKING_CODE];
            }
            $this->sendTrackingMail($orders_id,$trackingCodes);
        }
        $result->success = true;
        $result->event = 'tracking_created';
        $result->eventData = array(
            'xt_orders_id' => $orders_id,
            'tracking_ids' => array($result->tracking_id)
        );

        return $result;
    }

    public static function getTracking($pk_tracking)
    {
        global $db;

        $sql = "
            SELECT
        t.".COL_TRACKING_ID_PK.",
        t.".COL_TRACKING_CODE.",
        t.".COL_TRACKING_ORDER_ID.",
        t.".COL_TRACKING_STATUS_ID.",
        ts.".COL_TRACKING_STATUS_CODE.",
        t.".COL_TRACKING_SHIPPER_ID.",
        s.".COL_SHIPPER_CODE.",
        s.".COL_SHIPPER_NAME.",
        s.".COL_SHIPPER_TRACKING_URL.",
        s.".COL_SHIPPER_API_ENABLED.",
        s.".COL_SHIPPER_ENABLED.",
        t.".COL_TRACKING_ADDED."
        FROM ".TABLE_TRACKING." t
        INNER JOIN ".TABLE_SHIPPER." s ON t.".COL_TRACKING_SHIPPER_ID." = s.".COL_SHIPPER_ID_PK."
        INNER JOIN ".TABLE_TRACKING_STATUS." ts ON t.".COL_TRACKING_STATUS_ID." = ts.".COL_TRACKING_STATUS_ID_PK
            ;

        $sql .= " WHERE t.".COL_TRACKING_ID_PK." = ?";

        $tracking = $db->GetArray($sql, array($pk_tracking));

        return $tracking;
    }

    public static function getTrackingForOrder($orders_id, $tracking_codes=array(), $beautyName = true, $email = false)
    {
        global $db;
        $trackings = array();

        if(is_string($tracking_codes))
        {
            $tracking_codes = explode(',', $tracking_codes);
        }

        $params = array($orders_id);
        $whereTracking = '';
        if (count($tracking_codes)>0)
        {
            $q = [];
            foreach($tracking_codes as $tc)
            {
                $q[] = '?';
                $params[] = $tc;
            }
            $q = implode(',', $q);
            $whereTracking = ' AND `'.COL_TRACKING_CODE."` IN ({$q})";
        }

        $sql = "
        SELECT
        t.".COL_TRACKING_ID_PK.",
        t.".COL_TRACKING_CODE.",
        t.".COL_TRACKING_ORDER_ID.",
        t.".COL_TRACKING_STATUS_ID.",
        ts.".COL_TRACKING_STATUS_CODE.",
        t.".COL_TRACKING_SHIPPER_ID.",
        s.".COL_SHIPPER_CODE.",
        s.".COL_SHIPPER_NAME.",
        s.".COL_SHIPPER_TRACKING_URL.",
        s.".COL_SHIPPER_API_ENABLED.",
        s.".COL_SHIPPER_ENABLED.",
        t.".COL_TRACKING_ADDED."
        FROM ".TABLE_TRACKING." t
        INNER JOIN ".TABLE_SHIPPER." s ON t.".COL_TRACKING_SHIPPER_ID." = s.".COL_SHIPPER_ID_PK."
        INNER JOIN ".TABLE_TRACKING_STATUS." ts ON t.".COL_TRACKING_STATUS_ID." = ts.".COL_TRACKING_STATUS_ID_PK
        ;

        $sql .= '
        WHERE t.'.COL_TRACKING_ORDER_ID.'=? ' . $whereTracking. ' GROUP BY t.'.COL_TRACKING_ID_PK.', t.'.COL_TRACKING_CODE;

        $rs = $db->Execute($sql, $params);
        if ($rs->RecordCount()>0)
        {
            while (!$rs->EOF) {

                if($beautyName && strpos($rs->fields[COL_SHIPPER_NAME], 'shipcloud-') === 0)
                {
                    $shipper_names = $shipper_code = str_replace('shipcloud-', '', $rs->fields[COL_SHIPPER_NAME]);
                    $shipper_names = str_replace('_', ' ', $shipper_names);
                    $parts = explode(' ',$shipper_names);
                    foreach($parts as &$v)
                    {
                        if (strlen($v)<4)
                        {
                            $v = strtoupper($v);
                        }
                        else
                        {
                            $v = ucfirst($v);
                        }
                    }

                    $name = implode(' ', $parts);
                    if(USER_POSITION=='admin' && !$email)
                    {
                        $name .= ' / shipcloud';

                    }
                    else {
                        $url = $db->GetOne("SELECT ".COL_SHIPPER_TRACKING_URL. " FROM ".TABLE_SHIPPER." WHERE ".COL_SHIPPER_CODE."=?", array($shipper_code));
                        $rs->fields[COL_SHIPPER_TRACKING_URL] = $url;
                        $code = $db->GetOne("SELECT ".COL_SHIPCLOUD_LABEL_CARRIER_TRACKING_NO. " FROM ".TABLE_SHIPCLOUD_LABELS." WHERE ".COL_SHIPCLOUD_LABEL_ID."=?", array($rs->fields[COL_TRACKING_CODE]));
                        $rs->fields[COL_TRACKING_CODE] = $code;
                    }
                    $rs->fields[COL_SHIPPER_NAME] = $name;

                }
                $rs->fields[COL_SHIPPER_TRACKING_URL] = str_replace('[TRACKING_CODE]',$rs->fields[COL_TRACKING_CODE], $rs->fields[COL_SHIPPER_TRACKING_URL]);
                $trackings[] = $rs->fields;
                $rs->MoveNext();
            }
        }
        $rs->Close();
        return $trackings;
    }

    public static function getShippers($onlyActive = true)
    {
        global $db;
        $shippers = array();

        $whereActive = '';
        if ($onlyActive)
        {
            $whereActive = ' WHERE s.'.COL_SHIPPER_ENABLED.'=1';
        }
        $rs = $db->Execute('SELECT * FROM '.TABLE_SHIPPER.' s '.$whereActive.' ORDER BY '.COL_SHIPPER_NAME);
        if ($rs->RecordCount()>0)
        {
            while (!$rs->EOF) {
                $shippers[] = $rs->fields;
                $rs->MoveNext();
            }
        }
        return $shippers;
    }

    function sendTrackingMail($orderId, $tracking_codes=array())
    {
        global $language, $db;

        $order = new order($orderId, -1);

        $customer = new customer($order->customer);
        $cGroup = $order->order_customer['customers_status'];

        $lang = $order->order_data["language_code"];
        $langs = $language->_getLanguageList();
        if(!array_key_exists($lang, $langs))
        {
            $lang = empty($customer->customer_info['customers_default_language']) ?
                $language->code : $customer->customer_info['customers_default_language'];
        }

        $shopId = $order->order_data['shop_id'];

        $tracking_codes = array_unique($tracking_codes);

        $tracking_infos = $this->getTrackingForOrder($orderId, $tracking_codes, true, true);

        $mailer = new xtMailer('tracking_links', $lang, $cGroup, -1, $shopId);
        $mailer->_addReceiver($order->order_customer['customers_email_address'],$order->order_customer['customers_email_address']);

        $shop_ssl_domain = $db->GetOne("SELECT shop_ssl_domain FROM " . TABLE_MANDANT_CONFIG . " where shop_id = ?", array($shopId));
        $is_ssl = $db->GetOne("SELECT shop_ssl FROM " . TABLE_MANDANT_CONFIG . " where shop_id = ?", array($shopId));
        $shop_url = 'http' . ($is_ssl ? 's' : '') . '://' . $shop_ssl_domain.'/';

        $mailer->_assign('_system_base_url',  $shop_url);

        $mailer->_assign('customer',  $customer);
        $mailer->_assign('lang',  $lang);
        $mailer->_assign('tracking_infos',  $tracking_infos);
        //$mailer->_assign('order',  $order);

        $mailer->_assign('order_customer',$order->order_customer);
        $mailer->_assign('order_data',$order->order_data);

        $mailer->_assign('order_products',$order->order_products);
        $mailer->_assign('order_total_data',$order->order_total_data);
        $mailer->_assign('total',$order->order_total);
        $mailer->_assign('order_count',$order->order_count);

        // get text for payment method
        $rs = $db->Execute(
            "SELECT pd.payment_email_desc_txt,pd.payment_email_desc_html FROM ".TABLE_PAYMENT_DESCRIPTION." pd, ".TABLE_PAYMENT." p WHERE pd.language_code=? and p.payment_id=pd.payment_id and p.payment_code=?",
            array($language->code, $order->order_data['payment_code'])
        );
        if ($rs->RecordCount()==1) {
            // old payment info
            $mailer->_assign('payment_info',$rs->fields['payment_email_desc_txt']);

            // new payment info
            $mailer->_assign('payment_info_html',$rs->fields['payment_email_desc_html']);
            $mailer->_assign('payment_info_txt',$rs->fields['payment_email_desc_txt']);
        }
        $shipping_d = $order->_getShippingInfo($this->order_data['shipping_code'],$language->code);
        if ( $shipping_d){
            $mailer->_assign('shipping_info_html',$shipping_d['shipping_email_desc_html']);
            $mailer->_assign('shipping_info_txt',$shipping_d['shipping_email_desc_txt']);
        }

        $result = new stdClass();
        $result->success = false;
        if ($mailer->_sendMail())
        {
            $result->success = true;
        }
        else{
            $result->errorMsg = $mailer->ErrorInfo ? $mailer->ErrorInfo : TEXT_FAILURE;
            $result->msg = $mailer->ErrorInfo ? $mailer->ErrorInfo : TEXT_FAILURE;
            error_log('Failed to send tracking codes for ' . $order->order_customer['customers_email_address']);
        }

        return $result;
    }
}