<?php
/*
 #########################################################################
 #                       xt:Commerce Shopsoftware
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # Copyright 2007-2018 xt:Commerce International Ltd. All Rights Reserved.
 # This file may not be redistributed in whole or significant part.
 # Content of this file is Protected By International Copyright Laws.
 #
 # ~~~~~~ xt:Commerce Shopsoftware IS NOT FREE SOFTWARE ~~~~~~~
 #
 # http://www.xt-commerce.com
 #
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # @copyright xt:Commerce International Ltd., www.xt-commerce.com
 #
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # xt:Commerce International Ltd., Kafkasou 9, Aglantzia, CY-2112 Nicosia
 #
 # office@xt-commerce.com
 #
 #########################################################################
 */

defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_ship_and_track/classes/constants.php';
require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_ship_and_track/classes/class.xt_tracking.php';
require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_ship_and_track/classes/api/hermes/Hermes.php';

require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_ship_and_track/classes/class.xt_ship_and_track_layoutPart.php';

if (defined('HERMES_DEV')) die (' - dont even try - ');
define('HERMES_DEV', 1);

if (defined('XT_HERMES_API_PARTNER_ID')) die (' - dont even try - ');

global $db;
$xt_sat_version = $db->GetOne("SELECT `version` FROM ".TABLE_PLUGIN_PRODUCTS." WHERE `code` = 'xt_ship_and_track'");
define('SHIP_AND_TRACK_PLUGIN_VERSION', $xt_sat_version);

if (HERMES_DEV) // digitalfabrik api daten
{
    define('XT_HERMES_API_PARTNER_ID',  'EXT000315');
    define('XT_HERMES_API_PARTNER_PWD', '70d0172475a9486a134b17f911332563');
}
else // XTC api daten
{
    define('XT_HERMES_API_PARTNER_ID',  'EXT000207');
    define('XT_HERMES_API_PARTNER_PWD', '42864422bb280c5d134b17f911332563');
}

//  HERMES
$sql = 'SELECT `value` FROM '.TABLE_HERMES_SETTINGS. ' WHERE `key` = ?';
$a = $db->GetOne($sql, array(KEY_HERMES_USER));
define('XT_HERMES_USER', $a ? $a:'');

$a = $db->GetOne($sql, array(KEY_HERMES_PWD));
define('XT_HERMES_PWD',  $a ? $a:'');

$a = $db->GetOne($sql, array(KEY_HERMES_SANDBOX));
define('XT_HERMES_SANDBOX',  $a==1 ? 1:0);

// TABLE_SHIPCLOUD_SETTINGS
$sql = 'SELECT `value` FROM '.TABLE_SHIPCLOUD_SETTINGS. ' WHERE `key` = ?';
$a = $db->GetOne($sql, array(KEY_SHIPCLOUD_SANDBOX));
define('XT_SHIPCLOUD_SANDBOX',  $a==1 ? 1:0);

$a = $db->GetOne($sql, array(KEY_SHIPCLOUD_API_KEY_LIVE));
define('XT_SHIPCLOUD_API_KEY_LIVE',  $a ? $a:'');

$a = $db->GetOne($sql, array(KEY_SHIPCLOUD_API_KEY_SANDBOX));
define('XT_SHIPCLOUD_API_KEY_SANDBOX',  $a ? $a:'');

$a = $db->GetOne($sql, array(KEY_SHIPCLOUD_SANDBOX));
if(XT_SHIPCLOUD_SANDBOX==1)
{
    define('XT_SHIPCLOUD_API_KEY', XT_SHIPCLOUD_API_KEY_SANDBOX);
}
else {
    define('XT_SHIPCLOUD_API_KEY', XT_SHIPCLOUD_API_KEY_LIVE);
}

$a = $db->GetOne($sql, array(KEY_SHIPCLOUD_BANK_ACCOUNT_HOLDER));
define('XT_SHIPCLOUD_BANK_ACCOUNT_HOLDER',  $a ? $a:'');

$a = $db->GetOne($sql, array(KEY_SHIPCLOUD_BANK_NAME));
define('XT_SHIPCLOUD_BANK_NAME',  $a ? $a:'');

$a = $db->GetOne($sql, array(KEY_SHIPCLOUD_BANK_ACCOUNT_NUMBER));
define('XT_SHIPCLOUD_BANK_ACCOUNT_NUMBER',  $a ? $a:'');

$a = $db->GetOne($sql, array(KEY_SHIPCLOUD_BANK_CODE));
define('XT_SHIPCLOUD_BANK_CODE',  $a ? $a:'');


class xt_ship_and_track extends xt_ship_and_track_layoutPart {

    private $_master_key = 'id';

    function setPosition($position)
    {
        $this->position = $position;
    }

    function _getParams()
    {
        $header = [];
        $params = [];

        $this->getdefaultParams($header, $params);

        //$params['PageSize'] = 2;

        if (!$this->url_data['edit_id'])
        {
            $rowActionsFunctions = array();
            // open item

            $js = "
                    Ext.MessageBox.show({
                   title:    'Druckposition wählen',
                   msg:      '<input checked=\"1\" value=\"1\" id=\"printPosition1\" name=\"printPosition\" type=\"radio\" /> oben links<br /><input value=\"2\" id=\"printPosition2\" name=\"printPosition\" type=\"radio\" /> oben rechts<br /><input value=\"3\" id=\"printPosition3\" name=\"printPosition\" type=\"radio\" /> unten links<br /><input value=\"4\" id=\"printPosition4\" name=\"printPosition\" type=\"radio\" /> unten rechts<br />',
                   buttons:  Ext.MessageBox.OKCANCEL,
                   fn: function(btn) {
                      if( btn == 'ok') {
                          var pos = $(\"input:radio[name ='printPosition']:checked\").val();
                          //alert(pos);
                          console.log(record.data);
                          window.open('adminHandler.php?plugin=xt_ship_and_track&load_section=xt_ship_and_track&pg=printLabel&type=pdf&orders_id='+record.data.xt_orders_id+'&tracking_code='+record.data.hermes_order_no+'&pos='+pos+'&sec='+csrf_key,'_blank');
                      }
                   }
                });";
            $rowActionsFunctions['PRINT_LABEL'] = $js;
            $rowActions[] = array('iconCls' => 'PRINT_LABEL', 'qtipIndex' => 'qtip1', 'tooltip' => TEXT_PRINT_LABEL);

            $js = "
                var lm = new Ext.LoadMask(Ext.getBody(),{msg:'".__define('TEXT_HERMES_DELETING_ORDER')."'});
                lm.show();
    
                var conn = new Ext.data.Connection();
                conn.request({
                    url: 'adminHandler.php',
                    method:'GET',
                    params: {
                        pg:             'deleteOrder',
                        load_section:   'xt_ship_and_track',
                        plugin:         'xt_ship_and_track',
                        tracking_code:      record.data.".COL_HERMES_ORDER_NO.",
                        orders_id:      record.data.".COL_HERMES_XT_ORDER_ID.",
                        cascade: 1,
                        sec:            csrf_key
                    },
                    waitMsg: 'Laden',
                    success: function(responseObject)
                    {
                        lm.hide();
                        var r = Ext.decode(responseObject.responseText);
                       
                        contentTabs.getActiveTab().getUpdater().refresh();
                        if (!r.success)
                        {
                            Ext.MessageBox.alert('".__define('TEXT_ALERT')."', r.errorMsg);
                        }
                        else
                        {
                            Ext.MessageBox.alert('".__define('TEXT_ALERT')."',r.msg);
                        }
                    },
                    failure: function(responseObject)
                    {
                        lm.hide();
                        var r = Ext.decode(responseObject.responseText);
                        //console.log('fail');
                        //console.log(r);
                        var title = responseObject.statusText ? '".__define('TEXT_ALERT')."'+responseObject.status : '".__define('TEXT_ALERT')."';
                        var msg = responseObject.statusText ? responseObject.statusText : 'No Details available';
                        Ext.MessageBox.alert(title,msg);
                        //console.log(responseObject)
                    }
                });
            \n";

            $rowActionsFunctions['DELETE_HERMES_ORDER'] = $js;
            $rowActions[] = array('iconCls' => 'DELETE_HERMES_ORDER', 'qtipIndex' => 'qtip1', 'tooltip' => TEXT_DELETE);

            if (count($rowActionsFunctions) > 0) {
                $params['rowActions'] = $rowActions;
                $params['rowActionsFunctions'] = $rowActionsFunctions;
            }

            if (count($rowActions) > 0) {
                $params['rowActions'] = $rowActions;
                $params['rowActionsFunctions'] = $rowActionsFunctions;
            }
        }

        $ext = new ExtFunctions();
        $ext->setMasterKey($this->_master_key);
        $js = $ext->_multiactionPopup('my33', 'adminHandler.php?plugin=xt_ship_and_track&load_section=xt_hermes_collect&edit_id=new&sec='.$_SESSION['admin_user']['admin_key'], TEXT_REQUEST_COLLECT);
        $UserButtons['my33'] = array('status'=>false, 'text'=>'TEXT_REQUEST_COLLECT', 'style'=>'HERMES_COLLECT', 'acl'=>'edit', 'icon'=>'lorry.png', 'stm'=>$js);
        $params['display_my33Btn'] = true;

        $js_refresh = "
            var lm = new Ext.LoadMask(Ext.getBody(),{msg:'".__define('TEXT_HERMES_REFRESHING')."'});
            lm.show();

            var conn = new Ext.data.Connection();
            conn.request({
                url: 'adminHandler.php',
                method:'GET',
                params: {
                    pg:             'updateStatus',
                    load_section:   'xt_ship_and_track',
                    plugin:         'xt_ship_and_track',
                    tracking_code: 'refresh_all',
                    sec:            csrf_key
                },
                success: function(responseObject)
                {
                    var r = Ext.decode(responseObject.responseText);
                    //console.log(r);
                    lm.hide();
                    contentTabs.getActiveTab().getUpdater().refresh();
                    if (!r.success)
                    {
                        Ext.MessageBox.alert('".__define('TEXT_ALERT')."', r.errorMsg);
                    }
                    else
                    {
                        Ext.MessageBox.alert('".__define('TEXT_ALERT')."',r.msg);
                    }
                },
                failure: function(responseObject)
                {
                    lm.hide();
                    var r = Ext.decode(responseObject.responseText);
                    //console.log('fail');
                    //console.log(r);
                    var title = responseObject.statusText ? '".__define('TEXT_ALERT')."'+responseObject.status : '".__define('TEXT_ALERT')."';
                    var msg = responseObject.statusText ? responseObject.statusText : 'No Details available';
                    Ext.MessageBox.alert(title,msg);
                    //console.log(responseObject)
                }
            });
        \n";

        $UserButtons['refresh_status'] = array('text'=>'TEXT_HERMES_REFRESH', 'style'=>'HERMES_REFRESH', 'icon'=>'arrow_refresh.png', 'acl'=>'edit', 'stm' => $js_refresh);
        $params['display_refresh_statusBtn'] = true;

        $js_print = $ext->_multiactionWindow('printSelectedLabels', 'adminHandler.php?plugin=xt_ship_and_track&load_section=xt_ship_and_track&pg=printLabelsPdfSelection&sec='.$_SESSION['admin_user']['admin_key'], TEXT_HERMES_PRINT_SELECTION);
        $UserButtons['printSelectedLabels'] = array('status'=>false, 'text'=>'TEXT_HERMES_PRINT_SELECTION', 'style'=>'HERMES_PRINT', 'acl'=>'edit', 'icon'=>'printer.png', 'stm'=>$js_print);
        $params['display_printSelectedLabelsBtn'] = true;

        $params['UserButtons']      = $UserButtons;

        return $params;
    }

    function _unset($id = 0)
    {
        return false;
    }

    function deleteOrder($data)
    {
        $cascadeTracking = $data['cascade'];

        $r = new stdClass();
        $r->success = false;
        $r->msg = false;
        $r->errorMsg = false;

        global $db;

        $tracking = tracking::getTrackingForOrder($data['orders_id'],$data['tracking_code']);

        if($tracking[0])
        {
            $shipper_code = $tracking[0]['shipper_code'];
            if(strpos($shipper_code, 'shipcloud-') === 0)
            {
                $shipper_code = 'shipcloud';
            }
            switch($shipper_code)
            {
                case 'hermes':
                    $hermesError = false;
                    try
                    {
                        $hermes = self::getHermesService();
                        if (!$hermes)
                        {
                            $r->errorMsg = TEXT_HERMES_CHECK_SETTINGS;
                            $r->msg = TEXT_HERMES_CHECK_SETTINGS;
                            return json_encode($r);
                        }
                        $hResult = $hermes->deleteOrder($data[COL_TRACKING_CODE]);
                    }
                    catch (HermesException $he)
                    {
                        $r->errorMsg = $he->getHermesMessage();
                        $r->msg = $he->getHermesMessage();
                        $r->code = $he->getCode();
                        $hermesError = $he->getCode();
                    }
                    catch (Exception $e)
                    {
                        $r->errorMsg = $e->getMessage();
                        $r->msg = $e->getMessage();
                        $hermesError = true;
                    }

                    if ($hermesError!='312311') // 312311 - Der Auftrag kann nicht gelöscht werden.
                    {
                        $db->Execute("DELETE FROM ".TABLE_HERMES_ORDER." where `".COL_HERMES_ORDER_NO."` = ?", array($data[COL_TRACKING_CODE]));

                        if ($cascadeTracking && $hermesError!='312311') // 312311 - Der Auftrag kann nicht gelöscht werden.
                        {
                            $xtTracking = new xt_tracking();
                            $data['cascade'] = false;
                            $xtTracking->deleteTracking($data);
                        }
                    }

                    $r->success = true;
                    $r->msg = $hermesError=='312311' ? $r->msg : __define('TEXT_SUCCESS');
                    return json_encode($r);

                    break;
                case 'shipcloud':
                    try
                    {
                        shipcloud_api::getInstance(XT_SHIPCLOUD_API_KEY)
                            ->shipmentDelete($data['tracking_code']);

                        $db->Execute("DELETE FROM ".TABLE_SHIPCLOUD_LABELS." where `".COL_SHIPCLOUD_LABEL_ID."` = ?", array($data[COL_TRACKING_CODE]));

                        if ($cascadeTracking) // 312311 - Der Auftrag kann nicht gelöscht werden.
                        {
                            $xtTracking = new xt_tracking();
                            $data['cascade'] = false;
                            $xtTracking->deleteTracking($data);
                        }
                    }
                    catch (Exception $e)
                    {
                        $r->errorMsg = $e->getMessage();
                        $r->msg = $e->getMessage();
                        $r->success = false;
                        return json_encode($r);
                    }

                    $r->success = true;
                    $r->msg =__define('TEXT_SUCCESS');
                    return json_encode($r);

                    break;
            }
        }
        $r->success = false;
        $r->errorMsg = 'Sendung nicht gefunden';
        $r->msg =__define('TEXT_ALERT');
        return json_encode($r);
    }

    public static function getParcelClassForUser()
    {
        if ($_SESSION['hermes_parcel_class_user'])
        {
            $data = $_SESSION['hermes_parcel_class_user'];
        }
        else
        {
            $data = array();
            $hermes = self::getHermesService();
            if ($hermes)
            {
                try
                {
                    $classes = $hermes->getUserProducts();
                    foreach ($classes as $pwp) {
                        $name = self::_buildParcelClassDesc($pwp);

                        $data[] =  array('id' => $pwp->productInfo->parcelFormat->parcelClass,
                            'name' => $name);
                    }
                    $_SESSION['hermes_parcel_class_user'] = $data;
                }
                catch(HermesException $he)
                {
                    $data[] =  array('id' => 0,
                        'name' => str_replace('.','.<br/>', $he->getHermesMessage()));
                }
            }
            else {
                $data = array();
                $data[] = array('id' => '', 'name' => TEXT_HERMES_CHECK_SETTINGS);
            }
        }

        return $data;
    }

    public function updateStatus($data)
    {
        global $db;

        $r = new stdClass();
        $r->success = false;
        $r->msg = false;
        $r->errorMsg = false;

        if($data['tracking_code'] == 'refresh_order')
        {
            return $this->updateStatusForOrder($data['orders_id']);
        }
        else if ($data['tracking_code'] == 'refresh_all')
        {
            return $this->updateStatusForAll();
        }

        try {

            $tracking = tracking::getTrackingForOrder($data['orders_id'],$data['tracking_code']);

            if($tracking[0])
            {
                $shipper_code = $tracking[0]['shipper_code'];
                if(strpos($shipper_code, 'shipcloud-') === 0)
                {
                    $shipper_code = 'shipcloud';
                }
                switch($shipper_code)
                {
                    case 'hermes':
                        $hermes =  $this->getHermesService();// new Hermes(XT_HERMES_API_PARTNER_ID, XT_HERMES_API_PARTNER_PWD);
                        if (!$hermes)
                        {
                            $r->errorMsg = TEXT_HERMES_CHECK_SETTINGS;
                            $r->msg = TEXT_HERMES_CHECK_SETTINGS;
                            return json_encode($r);
                        }
                        $loggedIn = $hermes->login(XT_HERMES_USER, XT_HERMES_PWD);
                        if ($loggedIn)
                        {
                            $status = $hermes->readShipmentStatus($data['tracking_code']);
                            $xt_tracking = new xt_tracking();
                            $xt_tracking->setStatus($data['tracking_code'], $status);

                            global $db;
                            $sql = "UPDATE ".TABLE_HERMES_ORDER." SET `".COL_HERMES_STATUS."`=? WHERE `".COL_HERMES_ORDER_NO."`=?";
                            $r = $db->Execute($sql, array($status, $data['tracking_code']));

                            $r->msg = __define('TEXT_HERMES_'.$status.'_LONG');
                        }
                        else
                        {
                            $r->errorMsg = 'TEXT_HERMES_NOT_LOGGED_IN';
                            return json_encode($r);
                        }
                        break;
                    case 'shipcloud':
                        //$sc_id = $db->GetOne("SELECT ".COL_SHIPCLOUD_LABEL_ID. " FROM ".TABLE_SHIPCLOUD_LABELS. " WHERE ".COL_SHIPCLOUD_LABEL_ID_PK."=?", array($tracking[0]['id']));
                        $responseRead = shipcloud_api::getInstance(XT_SHIPCLOUD_API_KEY)
                            ->shipmentRead($data['tracking_code']);
                        $packages = $responseRead->packages();
                        $latestEvent = $packages[0]->tracking_events[0];
                        foreach($packages[0]->tracking_events as $te)
                        {
                            $dtLatest = new DateTime($latestEvent->timestamp);
                            $dtCurrent = new DateTime($te->timestamp);
                            if($dtCurrent > $dtLatest)
                            {
                                $latestEvent = $te;
                            }
                        }
                        $r->msg = self::formatShipcloudEvent($latestEvent, '<br/>');

                        $xttr = new xt_tracking();
                        $statusCode = self::getXtStatusCodeFromShipcloudStatus($latestEvent->status);
                        // we need a correct shipperId for shipclud because it was mixed with shipcloud-dhl ... on insert tracking
                        $shipperId = $db->GetOne("SELECT ".COL_SHIPPER_ID_PK. " FROM ".TABLE_SHIPPER. " WHERE ".COL_SHIPPER_CODE."='shipcloud'");
                        $xttr->setStatus($tracking[0]['tracking_code'], $statusCode, $shipperId);

                        break;
                }
            }
        }
        catch (HermesException $he)
        {
            $r->errorMsg = $he->getHermesMessage();
            $r->msg = $he->getHermesMessage();
            return json_encode($r);
        }
        catch (Exception $e)
        {
            $r->errorMsg = $e->getMessage();
            $r->msg = $e->getMessage();
            return json_encode($r);
        }

        $r->success = true;
        return json_encode($r);

    }

    public function updateStatusForAll()
    {
        $r = new stdClass();
        $r->success = false;
        $r->msg = false;
        $r->errorMsg = false;

        try {
            $hermes = $this->getHermesService();// new Hermes(XT_HERMES_API_PARTNER_ID, XT_HERMES_API_PARTNER_PWD);
            if (!$hermes)
            {
                $r->errorMsg = TEXT_HERMES_CHECK_SETTINGS;
                $r->msg = TEXT_HERMES_CHECK_SETTINGS;
                return json_encode($r);
            }
            $loggedIn = $hermes->login(XT_HERMES_USER, XT_HERMES_PWD);
            if ($loggedIn)
            {
                global $db;
                $shipperId = $db->GetOne(" SELECT `".COL_SHIPPER_ID_PK."` FROM ". TABLE_SHIPPER ." WHERE `".COL_SHIPPER_CODE."`='hermes'");
                $rs = $db->Execute(
                    'SELECT `'.COL_TRACKING_STATUS_CODE.'` FROM '.TABLE_TRACKING_STATUS.' t WHERE t.'.COL_TRACKING_SHIPPER_ID.'=? AND `'.COL_TRACKING_STATUS_CODE.'`!=10120',
                    array($shipperId)
                );
                $codes = array();
                if ($rs->RecordCount()>0)
                {
                    while (!$rs->EOF) {
                        $codes[] = $rs->fields[COL_TRACKING_STATUS_CODE];
                        $rs->MoveNext();
                    }
                }
                $rs->Close();

                $endRow = 499;
                $startRow = 0;
                $maxRows = 500;

                $c = 0;
                do
                {
                    $limit = " {$startRow},{$endRow} ";
                    // einzelne abfrage an hermes wird eingeschränkt auf 500 datensätzen und sätze
                    // - nicht älter 90 tage seit einstellung
                    // - nicht im status 10120 (erhalt rv)
                    $sql = "SELECT * FROM `".TABLE_HERMES_ORDER."` WHERE ".
                        " `".COL_HERMES_STATUS."` != 10120 AND ".
                        " `".COL_HERMES_TS_CREATED."` >= DATE_SUB(CURRENT_TIMESTAMP , INTERVAL 90 DAY) ".
                        " ORDER BY ".COL_HERMES_TS_CREATED .
                        " LIMIT ".$limit;

                    $rs = $db->Execute($sql);
                    $c = $rs->RecordCount();
                    if ($c==0) break;

                    $rs->MoveFirst();
                    $from = new DateTime($rs->fields[COL_HERMES_TS_CREATED]);
                    $from = $from->format('Y-m-d').'T'.$from->format('H:i:s');

                    $rs->MoveLast();
                    $to = new DateTime($rs->fields[COL_HERMES_TS_CREATED]);
                    $to = $to->format('Y-m-d').'T'.$to->format('H:i:s');

                    $sc = new PropsOrderSearchCriteria(
                        null, //identNo
                        null, //orderNo
                        null, //lastname
                        null, //city
                        $from, //from xsdDate
                        $to, //to
                        null, //postcode
                        null, //coutryCode
                        null, //clientRefNumber
                        null, //ebay
                        $codes  //status array // TODO muss eigentlich null sein
                    );

                    $orders = $hermes->getOrders($sc);
                    $xt_tracking = new xt_tracking();
                    foreach($orders as $order)
                    {
                        $xt_tracking->setStatus($order->orderNo, $order->status);

                        global $db;
                        $sql = "UPDATE ".TABLE_HERMES_ORDER." SET ".
                            "`".COL_HERMES_STATUS."`=?,".
                            "`".COL_HERMES_SHIPPING_ID."`=?".
                            " WHERE `".COL_HERMES_ORDER_NO."`=?";
                        $r = $db->Execute($sql, array($order->status, $order->shippingId, $order->orderNo));
                    }

                    $startRow += $maxRows;
                    $endRow += $maxRows;
                }
                while($c>0);

                $r->msg = __define('TEXT_SUCCESS');
            }
            else
            {
                $r->errorMsg = 'TEXT_HERMES_NOT_LOGGED_IN';
                return json_encode($r);
            }
        }
        catch (HermesException $he)
        {
            $r->errorMsg = $he->getHermesMessage();
            $r->msg = $he->getHermesMessage();
            return json_encode($r);
        }
        catch (Exception $e)
        {
            $r->errorMsg = $e->getMessage();
            $r->msg = $e->getMessage();
            return json_encode($r);
        }

        $r->success = true;
        $r->msg = 'OK';
        return json_encode($r);

    }

    public function updateStatusForOrder($xt_orders_id)
    {
        $r = new stdClass();
        $r->success = false;
        $r->msg = false;
        $r->errorMsg = false;

        $trackings = tracking::getTrackingForOrder($xt_orders_id);
        $hasHermes = false;
        $hasShipcloud = false;
        foreach($trackings as $t)
        {
            if(strpos($t['shipper_code'],'shipcloud') === 0)
            {
                $hasShipcloud = true; continue;
            }
            if($t['shipper_code'] == 'hermes')
            {
                $hasHermes = true; continue;
            }
        }

        if($hasHermes)
        {
            $rHermes =  $this->updateStatusForOrderHermes($xt_orders_id);
        }

        if($hasShipcloud)
        {
            $rShipcloud =  $this->updateStatusForOrderShipcloud($xt_orders_id);
        }

        $r->success = $rHermes->success || $rShipcloud->success;
        if(!$r->success)
        {
            $r->errorMsg = $r->msg = $rHermes->errorMsg.'<br />'.$rShipcloud->errorMsg;
        }
        else {
            $r->msg = 'OK';
        }


        return json_encode($r);

    }

    public function updateStatusForOrderHermes($xt_orders_id)
    {
        global $db, $store_handler;

        $r = new stdClass();
        $r->success = false;
        $r->msg = false;
        $r->errorMsg = false;

        try {
            $hermes = $this->getHermesService();// new Hermes(XT_HERMES_API_PARTNER_ID, XT_HERMES_API_PARTNER_PWD);
            if (!$hermes)
            {
                $r->errorMsg = TEXT_HERMES_CHECK_SETTINGS;
                $r->msg = TEXT_HERMES_CHECK_SETTINGS;
                return $r;
            }
            $loggedIn = $hermes->login(XT_HERMES_USER, XT_HERMES_PWD);
            if ($loggedIn)
            {
                $refNo = $xt_orders_id .'#'. $store_handler->domain;
                // fallback to old format
                /*
                $sql = 'SELECT `value` FROM '.TABLE_HERMES_SETTINGS. ' WHERE `key` = ?';
                $a = $db->GetOne($sql, array(KEY_HERMES_LAST_ORDER_BEFORE_v6));
                if($a && $a<=$xt_orders_id)
                    $refNo = 'xtc'.$xt_orders_id;
                */

                $sc = new PropsOrderSearchCriteria(
                    null, //identNo
                    null, //orderNo
                    null, //lastname
                    null, //city
                    null, //date(DATE_RFC822,0), //from
                    null, //date(DATE_RFC822), //to
                    null, //postcode
                    null, //coutryCode
                    $refNo, //clientRefNumber
                    null, //ebay
                    null  //status array
                );

                $orders = $hermes->getOrders($sc);
                $xt_tracking = new xt_tracking();
                foreach($orders as $order)
                {
                    $xt_tracking->setStatus($order->orderNo, $order->status);

                    global $db;
                    $sql = "UPDATE ".TABLE_HERMES_ORDER." SET `".COL_HERMES_STATUS."`=? WHERE `".COL_HERMES_ORDER_NO."`=?";
                    $r = $db->Execute($sql, array($order->status, $order->orderNo));
                }

                $r->msg = __define('TEXT_SUCCESS');
            }
            else
            {
                $r->errorMsg = 'TEXT_HERMES_NOT_LOGGED_IN';
                return $r;
            }
        }
        catch (HermesException $he)
        {
            $r->errorMsg = $he->getHermesMessage();
            $r->msg = $he->getHermesMessage();
            return $r;
        }
        catch (Exception $e)
        {
            $r->errorMsg = $e->getMessage();
            $r->msg = $e->getMessage();
            return $r;
        }

        $r->success = true;
        return $r;

    }

    public function updateStatusForOrderShipcloud($xt_orders_id)
    {
        global $db, $store_handler;

        $r = new stdClass();
        $r->success = false;
        $r->msg = false;
        $r->errorMsg = false;

        try {
                $refNo = $store_handler->domain.'#'.$xt_orders_id;

                $responseFilter = shipcloud_api::getInstance(XT_SHIPCLOUD_API_KEY)
                ->shipmentIndex(false,false, array('reference_number' => $refNo));

                foreach($responseFilter as $resp)
                {
                    $packages = $resp->packages();
                    $latestEvent = $packages[0]->tracking_events[0];
                    foreach($packages[0]->tracking_events as $te)
                    {
                        $dtLatest = new DateTime($latestEvent->timestamp);
                        $dtCurrent = new DateTime($te->timestamp);
                        if($dtCurrent > $dtLatest)
                        {
                            $latestEvent = $te;
                        }
                    }

                    $xttr = new xt_tracking();
                    $statusCode = self::getXtStatusCodeFromShipcloudStatus($latestEvent->status);
                    // we need a correct shipperId for shipclud because it was mixed with shipcloud-dhl ... on insert tracking
                    $shipperId = $db->GetOne("SELECT ".COL_SHIPPER_ID_PK. " FROM ".TABLE_SHIPPER. " WHERE ".COL_SHIPPER_CODE."='shipcloud'");
                    $xttr->setStatus($resp->id(), $statusCode, $shipperId);
                }

                $r->msg = __define('TEXT_SUCCESS');
        }
        catch (Exception $e)
        {
            $r->errorMsg = $e->getMessage();
            $r->msg = $e->getMessage();
            return $r;
        }

        $r->success = true;
        return $r;

    }

    public function saveOrder($data, $cascadeTracking = true)
    {
        global $store_handler;
        $r = new stdClass();
        $r->success = false;
        $r->msg = false;
        $r->errorMsg = false;

        $xtOrder = new order($data['orders_id'], -1);

        try {
            $hermes = $this->getHermesService();// new Hermes(XT_HERMES_API_PARTNER_ID, XT_HERMES_API_PARTNER_PWD);
            if (!$hermes)
            {
                $r->errorMsg = TEXT_HERMES_CHECK_SETTINGS;
                $r->msg = TEXT_HERMES_CHECK_SETTINGS;
                return json_encode($r);
            }
            $loggedIn = $hermes->login(XT_HERMES_USER, XT_HERMES_PWD);
            if ($loggedIn)
            {
                global $db;
                $iso3countryCode = $db->GetOne(
                    "SELECT `countries_iso_code_3` FROM ". TABLE_COUNTRIES . " WHERE `countries_iso_code_2`=?",
                    array($xtOrder->order_data['delivery_country_code'])
                );

                $refNo = $data['orders_id'].'#'.$store_handler->domain;
                $lastName = !empty($xtOrder->order_data['delivery_company']) ?
                    $xtOrder->order_data['delivery_lastname'] . ' / '. $xtOrder->order_data['delivery_company'] :
                    $xtOrder->order_data['delivery_lastname'];

                $receiver = new HermesAddress(
                    trim($xtOrder->order_data['delivery_address_addition']), // adresszusatz zb hinterhaus
                    trim($xtOrder->order_data['customers_email_address']),
                    '', // hausnummer
                    trim($iso3countryCode),
                    trim($lastName),
                    trim($xtOrder->order_data['delivery_city']), trim($xtOrder->order_data['delivery_suburb']), trim($xtOrder->order_data['delivery_postcode']),
                    trim($xtOrder->order_data['delivery_street_address']),
                    trim($xtOrder->order_data['delivery_phone']),
                    trim($xtOrder->order_data['delivery_firstname']),
                    '' // phone prefix
                );
                $cod = (is_numeric($data['hermes_amount_cash_on_delivery']) && $data['hermes_amount_cash_on_delivery'] > 0) ? true : false;
                $codAmount = $data['hermes_amount_cash_on_delivery'] ;
                $codAmountCent = floatval($data['hermes_amount_cash_on_delivery'])  * 100;

                $bulk = $data['hermes_bulk_good'] == 'on' || $data['hermes_bulk_good'] == 1 ? true : false;
                $orderNo = !empty($data['orderNo']) ? $data['orderNo'] : '';
                $orderNo = $hermes->createOrder($receiver, $refNo, $data['parcel_class'],$cod,$codAmountCent, $bulk, $orderNo);

                $sc = new PropsOrderSearchCriteria(
                    null, //identNo
                    $orderNo, //orderNo
                    null, //lastname
                    null, //city
                    null, //date(DATE_RFC822,0), //from
                    null, //date(DATE_RFC822), //to
                    null, //postcode
                    null, //coutryCode
                    null, //clientRefNumber
                    null, //ebay
                    null  //status array
                );

                $orders = $hermes->getOrders($sc);
                $order = $orders[0];
                $r->msg = __define('TEXT_HERMES_'.$order->status.'_LONG');

                $saveData = array(
                    COL_HERMES_ORDER_NO => $orderNo,
                    COL_HERMES_SHIPPING_ID => '',
                    COL_HERMES_STATUS => $order->status,
                    COL_HERMES_AMOUNT_CASH_ON_DELIVERY => $codAmount,
                    COL_HERMES_PARCEL_CLASS => $data['parcel_class'],
                    COL_HERMES_XT_ORDER_ID => $xtOrder->oID,
                    COL_HERMES_BULK_GOOD => $bulk ? 1 :0,
                    COL_HERMES_COLLECT_DATE => null,
                    COL_HERMES_TS_CREATED => $order->creationDate
                );
                if (!empty($data['edit_id']))
                {
                    $saveData[COL_HERMES_ID_PK] = $data['edit_id'];
                }
                $o = new adminDB_DataSave(TABLE_HERMES_ORDER, $saveData, false, __CLASS__);
                try {
                    $o->saveDataSet();
                }
                catch(Exception $e){
                    $r->errorMsg = $e->getMessage();
                    $r->msg = $e->getMessage();
                    return json_encode($r);
                }

                if ($cascadeTracking)
                {
                    global $db;
                    $shipperId = $db->GetOne(" SELECT `".COL_SHIPPER_ID_PK."` FROM ". TABLE_SHIPPER ." WHERE `".COL_SHIPPER_CODE."`='hermes'");
                    $tracking = new xt_tracking();
                    $data = array(
                        'orders_id' => $xtOrder->oID,
                        'shipper' => $shipperId,
                        'tracking_codes' => $orderNo,
                        'send_email' => false
                    );
                    $addResult = $tracking->addTracking($data, false);
                    $tracking->setStatus($orderNo, $order->status);

                    $r->event = 'tracking_created';
                    $r->eventData = array(
                        'xt_orders_id' => $data['orders_id'],
                        'tracking_ids' => array($addResult->tracking_id)
                    );
                }

            }
            else
            {
                $r->errorMsg = TEXT_NOT_LOGGED_IN;
                $r->msg = TEXT_NOT_LOGGED_IN;
                return json_encode($r);
            }
        }
        catch (HermesException $he)
        {
            $r->errorMsg = $he->getHermesMessage();
            if($he->get_Code() == "312124")
            {
                $r->errorMsg .= "<br>Kombination aus Nachname+Firma darf nicht länger als 29 Zeichen sein.";
            }
            $r->msg = $he->getHermesMessage();
            if($he->get_Code() == "312124")
            {
                //$r->errorMsg .= "<br>Kombination aus Nachname+Firma darf nicht länger als 29 Zeichen sein.";
            }
            return json_encode($r);
        }
        catch (Exception $e)
        {
            $r->errorMsg = $e->getMessage();
            $r->msg = $e->getMessage();
            return json_encode($r);
        }


        $r->success = true;
        return json_encode($r);

    }


    public function printLabel($data)
    {
        global $db;
        $r = TEXT_FAILURE;
        try {

            $tracking = tracking::getTrackingForOrder($data['orders_id'],$data['tracking_code'], false);

            if($tracking[0])
            {
                $shipper_code = $tracking[0]['shipper_code'];
                if(strpos($shipper_code, 'shipcloud-') === 0)
                {
                    $shipper_code = 'shipcloud';
                }
                switch($shipper_code)
                {
                    case 'hermes':
                        $hermes = $this->getHermesService();// new Hermes(XT_HERMES_API_PARTNER_ID, XT_HERMES_API_PARTNER_PWD);
                        if (!$hermes)
                        {
                            return TEXT_HERMES_CHECK_SETTINGS;
                        }
                        $loggedIn = $hermes->login(XT_HERMES_USER, XT_HERMES_PWD);
                        if ($loggedIn)
                        {
                            if ($data['type'] == 'pdf')
                            {
                                $pdf = $hermes->printLabelPdf($data['tracking_code'], $data['pos']);
                            }
                            else if ($data['type'] == 'jpeg' || $data['type'] == 'jpg')
                            {
                                $pdf = $hermes->printLabelJpeg($data['tracking_code'], $data['pos']);
                            }
                            else {
                                echo 'Wrong type';
                                exit;
                            }
                        }
                        else
                        {
                            $r->errorMsg = 'TEXT_HERMES_NOT_LOGGED_IN';
                            return $r;
                        }
                        break;
                    case 'shipcloud':
                        $labelUrl =$db->GetOne("SELECT ".COL_SHIPCLOUD_LABEL_LABEL_URL." FROM ".TABLE_SHIPCLOUD_LABELS." WHERE ".COL_SHIPCLOUD_LABEL_ID."=?", array($tracking[0]['tracking_code']));
                        header("Location: ".$labelUrl);
                        die();

                        break;
                }
            }



        }
        catch (HermesException $he)
        {
            $r = $he->getHermesMessage();
            return $r;
        }
        catch (Exception $e)
        {
            $r->errorMsg = $e->getMessage();
            $r->msg = $e->getMessage();
            return $r;
        }

        header('Content-type: application/'.$data['type']);
        header('Content-Disposition:  filename=order-'.$data['orders_id'].'_'.$data['tracking_code'].'.'.$data['type']);
        echo $pdf;

        exit;

    }

    public function printLabelsPdf($data)
    {
        $r = TEXT_FAILURE;

        try {
            $hermes = $this->getHermesService();// new Hermes(XT_HERMES_API_PARTNER_ID, XT_HERMES_API_PARTNER_PWD);
            if (!$hermes)
            {
                return TEXT_HERMES_CHECK_SETTINGS;
            }
            $loggedIn = $hermes->login(XT_HERMES_USER, XT_HERMES_PWD);
            if ($loggedIn)
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
            INNER JOIN ".TABLE_TRACKING_STATUS." ts ON t.".COL_TRACKING_STATUS_ID." = ts.".COL_TRACKING_STATUS_ID_PK;

                $sql .= " WHERE s.".COL_SHIPPER_CODE."='hermes' AND t.".COL_TRACKING_ORDER_ID."=?";

                $dbResult = $db->Query($sql, array($data['orders_id']));
                if ($dbResult->RecordCount() > 0)
                {
                    $codes = array();
                    while(!$dbResult->EOF)
                    {
                        $code = $dbResult->fields[COL_TRACKING_CODE];
                        $codes[] = $code;
                        $dbResult->MoveNext();
                    }
                    $dbResult->Close();

                    $pdf = $hermes->printLabelsPdf($codes);
                }
            }
            else
            {
                $r->errorMsg = 'TEXT_HERMES_NOT_LOGGED_IN';
                return $r;
            }
        }
        catch (HermesException $he)
        {
            $r = $he->getHermesMessage();
            return $r;
        }
        catch (Exception $e)
        {
            $r->errorMsg = $e->getMessage();
            $r->msg = $e->getMessage();
            return $r;
        }

        header('Content-type: application/pdf');
        header('Content-Disposition:  filename=order-'.$data['orders_id'].'.'.$data['type']);
        echo $pdf;

        exit;

    }

    public function printLabelsPdfSelection($data)
    {
        $r = TEXT_FAILURE;

        $hermesIds = explode(',', $this->url_data['value_ids']);
        if (sizeof($hermesIds)==0)
        {
            $r->msg = $r->errorMsg = 'No id\'s found';
            return json_encode($r);
        }
        $orderNoIds = array();
        foreach($hermesIds as $hermesId)
        {
            if(!$hermesId) continue;
            $h = $this->_get($hermesId)->data[0];
            if ($h)
            {
                $orderNoIds[] = $h[COL_HERMES_ORDER_NO];
            }
        }

        if(sizeof($orderNoIds))
        {
            try {
                $hermes = $this->getHermesService();
                if (!$hermes)
                {
                    return TEXT_HERMES_CHECK_SETTINGS;
                }
                $pdf = $hermes->printLabelsPdf($orderNoIds);
            }
            catch (HermesException $he)
            {
                $r = $he->getMessage();
                echo $r;
                die();
            }
            catch (Exception $e)
            {
                $r = $e->getMessage();
                echo $r;
                die();
            }

            header('Content-type: application/pdf');
            header('Content-Disposition:  filename=hermes_'.date('Y-m-d_H-i-s').'.pdf');
            echo $pdf;

            exit;
        }
        echo $r;
        die();
    }

    public function requestCollect($data)
    {
        $r = new stdClass();
        $r->success = false;
        $r->msg = false;
        $r->errorMsg = false;
        $r->requestNo = false;

        try {
            $hermes = $this->getHermesService();
            if (!$hermes)
            {
                $r->errorMsg = TEXT_HERMES_CHECK_SETTINGS;
                $r->msg = TEXT_HERMES_CHECK_SETTINGS;
                return $r;
            }
            $loggedIn = $hermes->login(XT_HERMES_USER, XT_HERMES_PWD);
            if ($loggedIn)
            {
                $requestNo = $hermes->requestCollection($data['date'],$data['xs'],$data['s'],$data['m'],$data['l'],$data['xl'],$data['xl_bulk']);
                $r->requestNo = $requestNo;
            }
            else
            {
                $r->errorMsg = 'TEXT_HERMES_NOT_LOGGED_IN';
                return $r;
            }
        }
        catch (HermesException $he)
        {
            $r->errorMsg = $he->getHermesMessage();
            $r->msg = $he->getHermesMessage();
            return $r;
        }
        catch (Exception $e)
        {
            $r->errorMsg = $e->getMessage();
            $r->msg = $e->getMessage();
            $r;
        }

        $r->msg = __define('TEXT_SUCCESS');
        $r->errorMsg = __define('TEXT_SUCCESS');
        $r->success = true;
        return $r;
    }


    public function deleteCollect($date)
    {
        $r = new stdClass();
        $r->success = false;
        $r->msg = false;
        $r->errorMsg = false;
        $r->canceled = false;
        $r->code = false;

        try {
            $hermes = $this->getHermesService();
            if (!$hermes)
            {
                $r->errorMsg = TEXT_HERMES_CHECK_SETTINGS;
                $r->msg = TEXT_HERMES_CHECK_SETTINGS;
                return $r;
            }
            $loggedIn = $hermes->login(XT_HERMES_USER, XT_HERMES_PWD);
            if ($loggedIn)
            {
                $canceled = $hermes->cancelCollection($date);
                $r->canceled = $canceled;
            }
            else
            {
                $r->errorMsg = 'TEXT_HERMES_NOT_LOGGED_IN';
                return $r;
            }
        }
        catch (HermesException $he)
        {
            $r->errorMsg = $he->getHermesMessage();
            $r->msg = $he->getHermesMessage();
            $r->code = $he->getCode();
            return $r;
        }
        catch (Exception $e)
        {
            $r->errorMsg = $e->getMessage();
            $r->msg = $e->getMessage();
            $r->code = $e->getCode();
            $r;
        }

        $r->success = true;
        return $r;

    }



    private static function _buildParcelClassDesc(ProductWithPrice $pwp)
    {
        $desc = $pwp->productInfo->parcelFormat->parcelClass ? $pwp->productInfo->parcelFormat->parcelClass.' - ' : '';

        if (is_array($pwp->productInfo->deliveryDestinations->DeliveryDestination))
        {
            foreach($pwp->productInfo->deliveryDestinations->DeliveryDestination as $dd)
            {
                $desc .= $dd->countryCode.'-';
                $desc .= number_format($dd->weigthMaxKg,1,',','.').'kg ';
            }
        }
        else {
            $desc .= $pwp->productInfo->deliveryDestinations->DeliveryDestination->countryCode.'-';
            $desc .= number_format($pwp->productInfo->deliveryDestinations->DeliveryDestination->weigthMaxKg,1,',','.').'kg ';
        }
        return $desc;
    }

    public static function getHermesService()
    {

        $userToken = false;
        if ($_SESSION['hermes_user_token'])
        {
            $userToken = $_SESSION['hermes_user_token'];
        }

        try {
            $hermes = new Hermes(XT_HERMES_API_PARTNER_ID, XT_HERMES_API_PARTNER_PWD, $userToken, XT_HERMES_SANDBOX);
            $loggedIn = $hermes->login(XT_HERMES_USER, XT_HERMES_PWD);
            if (!$loggedIn)
            {
                return false;
            }
            else
            {
                $_SESSION['hermes_user_token'] = $hermes->getUserToken();
            }
        }
        catch (HermesException $he)
        {
            return false;
        }
        catch (Exception $e)
        {
            return false;
        }

        return $hermes;
    }


    /**
     *    SHIPCLOUD
     */


    public function quotesGet_shipcloud($reqData)
    {
        global $price;

        $r = new stdClass();
        $r->success = true;
        $r->msg = 'OK';

        try
        {
            $a = shipcloud_api::getInstance(XT_SHIPCLOUD_API_KEY)
                ->quotesGet($reqData);

            $r->price = $price->_StyleFormat($a->price());
        }
        catch (Exception $e)
        {
            $r->success = false;
            $msg = explode('|', $e->getMessage());
            $msg[0] = $msg[0] . '::' . $msg[1];
            unset($msg[1]);
            $r->msg = implode("<br/>", $msg);
        }

        return json_encode($r);
    }

        /**
     * @param $data  data from $_REQUEST
     * @param bool|true $cascadeTracking
     * @return mixed|string
     */
    public function saveOrder_shipcloud($data, $order = false)
    {
        global $store_handler, $price, $db;
/*
        $db->Execute('INSERT INTO '.TABLE_SHIPCLOUD_SETTINGS." values 
    ('".KEY_SHIPCLOUD_FROM_FIRSTNAME."'        ,''),
    ('".KEY_SHIPCLOUD_FROM_LASTNAME."'        ,''),
    ('".KEY_SHIPCLOUD_FROM_COMPANY."'        ,''),
    ('".KEY_SHIPCLOUD_FROM_CAREOF."'        ,''),
    ('".KEY_SHIPCLOUD_FROM_STREET."'        ,''),
    ('".KEY_SHIPCLOUD_FROM_HOUSENO."'       ,''),
    ('".KEY_SHIPCLOUD_FROM_CITY."'          ,''),
    ('".KEY_SHIPCLOUD_FROM_ZIP."'           ,''),
    ('".KEY_SHIPCLOUD_FROM_STATE."'         ,''),
    ('".KEY_SHIPCLOUD_FROM_COUNTRY."'       ,''),
    ('".KEY_SHIPCLOUD_FROM_PHONE."'         ,''),
    
    ('".KEY_SHIPCLOUD_DIFFERENT_RETOURE_ADDRESS."'         ,'0'),
    
    ('".KEY_SHIPCLOUD_RETOURE_FIRSTNAME."'        ,''),
    ('".KEY_SHIPCLOUD_RETOURE_LASTNAME."'        ,''),
    ('".KEY_SHIPCLOUD_RETOURE_COMPANY."'        ,''),
    ('".KEY_SHIPCLOUD_RETOURE_CAREOF."'        ,''),
    ('".KEY_SHIPCLOUD_RETOURE_STREET."'        ,''),
    ('".KEY_SHIPCLOUD_RETOURE_HOUSENO."'       ,''),
    ('".KEY_SHIPCLOUD_RETOURE_CITY."'          ,''),
    ('".KEY_SHIPCLOUD_RETOURE_ZIP."'           ,''),
    ('".KEY_SHIPCLOUD_RETOURE_STATE."'         ,''),
    ('".KEY_SHIPCLOUD_RETOURE_COUNTRY."'       ,''),
    ('".KEY_SHIPCLOUD_RETOURE_PHONE."'         ,'')
    ");
*/
        xt_shipcloud_settings::readSettings();

        $pickup = false;
        try
        {
            if ($data['orders_id'] == 0)
            {
                $r = new stdClass();
                $r->success = false;
                $r->msg = 'OK';
                $order_ids = explode(',', $data['order_ids']);
                $tmp_msg = '';
                foreach ($order_ids as $order_id)
                {
                    $o = new order($order_id, -1);
                    //$delivery_addr = $aoe->getOrderData()['order_data']['delivery'];

                    if (empty($data['to_street_no']))
                    {
                        $addr_street = self::splitAddress($o->order_data['delivery_street_address'], self::getSplit());
                    }

                    $data['orders_id'] = $order_id;
                    $data['shop_id'] = $o->order_data['shop_id'];

                    $data['to_first_name'] = $o->order_data['delivery_firstname'];
                    $data['to_last_name'] = $o->order_data['delivery_lastname'];
                    $data['to_company'] = $o->order_data['delivery_company'];
                    $data['to_care_of'] = '';
                    $data['to_street'] = $o->order_data['delivery_street_address'];
                    $data['to_street_no'] = '';
                    $data['to_zip_code'] = $o->order_data['delivery_postcode'];
                    $data['to_city'] = $o->order_data['delivery_city'];
                    $data['to_state'] = $o->order_data['delivery_federal_state_code'];
                    $data['to_country'] = $o->order_data['delivery_country_code'];
                    $data['to_phone'] = $o->order_data['delivery_mobile_phone'] ? $o->order_data['delivery_mobile_phone'] : $o->order_data['delivery_phone'];
                    $data['to_email'] = $o->order_data['customers_email_address'];

                    $r_tmp = $this->saveOrder_shipcloud($data, $o);
                    if ($data['quote'] == 1)
                    {
                        return $r_tmp;
                    }
                    $r_tmp = json_decode($r_tmp);
                    $tmp_msg .= TEXT_SHIPCLOUD_ORDER . '&nbsp;' . $order_id . ':&nbsp;';
                    if ($r_tmp->success == false)
                    {
                        $tmp_msg .= $r_tmp->msg;
                    }
                    else
                    {
                        $tmp_msg .= 'OK';
                    }
                    $tmp_msg .= '<br />';
                }
                if ($r->success == false)
                {
                    $r->msg = $tmp_msg;
                }
                return json_encode($r);
            }

            $r = new stdClass();
            $r->success = true;
            $r->msg = 'OK';

            if (empty($data['to_street_no']))
            {
                $addr_street = self::splitAddress($data['to_street'], self::getSplit());
            }
            else
            {
                $addr_street = array($data['to_street'], $data['to_street_no']);
            }

            if ($order == false)
            {
                $order = new order($data['orders_id'], -1);
                $r->refresh = true;
            }
            $store_name = $db->GetOne("SELECT shop_ssl_domain FROM " . TABLE_MANDANT_CONFIG . " where shop_id = ?", array((int)$data['shop_id']));
            $reference = $data['orders_id'].'#' . $data['orders_id'];
;
            $reference_cod = $store_name . ' - ' . TEXT_SHIPCLOUD_ORDER . ' ' . $data['orders_id'];

            if (
                (($data['shipcloud_carrier'] == 'ups' && $data['shipcloud_service'] == 'returns') ||
                    ($data['shipcloud_carrier'] == 'dhl' && $data['shipcloud_service'] == 'on_day'))
                && empty($data['description'])
            )
            {
                $p_names = array();
                foreach ($order->order_products as $k => $v)
                {
                    $p_names[] = $v['products_name'];
                }
                $desc = implode('|', $p_names);
            }
            else
            {
                $desc = $data['description'];
            }

            $to = Shipcloud\Address::create()
                ->setFirstName($data['to_first_name'])
                ->setLastName($data['to_last_name'])
                ->setCareOf($data['to_care_of'])
                ->setCompany($data['to_company'])
                ->setStreet($addr_street[0])
                ->setStreetNo($addr_street[1])
                ->setCity($data['to_city'])
                ->setZipCode($data['to_zip_code'])
                ->setCountry($data['to_country'])
                ->setPhone($data['to_phone'])
                ->setCareOf($data['to_care_of']);
            if(!empty($data['to_state']))
            {
                $to->setState($data['to_state']);
            }
            $package = Shipcloud\Package::create()
                ->setLength(str_replace(',', '.', $data['package_length']))
                ->setWidth(str_replace(',', '.', $data['package_width']))
                ->setHeight(str_replace(',', '.', $data['package_height']))
                ->setWeight(str_replace(',', '.', $data['package_weight']))
                ->setType($data['shipcloud_package_type'])->setDescription($desc);

            $reqData = Shipcloud\ShipmentCreateRequest::create()
                ->setCarrier($data['shipcloud_carrier'])
                ->setService($data['shipcloud_service']);

            // check for additional services
            switch ($data['shipcloud_carrier'])
            {
                case 'dhl':
                    if ($data['add_service_dhl'] == 'on')
                    {
                        $email = trim($data['add_service_dhl_advance_notice_email']);
                        if (!empty($email))
                        {
                            $reqData->addAdditonalService(Shipcloud\AdvanceNoticeEmail::create()
                                ->setEmail($email)
                                ->setLang($order->order_data['language_code']));
                        }
                        $amount = trim($data['add_service_dhl_cash_on_delivery']);
                        $amount = doubleval(abs(str_replace(',', '.', $amount)));
                        if ($amount)
                        {
                            $reqData->addAdditonalService(Shipcloud\CashOnDelivery::create()
                                ->setAmount($amount)
                                ->setBankAccountHolder(XT_SHIPCLOUD_BANK_ACCOUNT_HOLDER)
                                ->setBankAccountNumber(XT_SHIPCLOUD_BANK_ACCOUNT_NUMBER)
                                ->setBankCode(XT_SHIPCLOUD_BANK_CODE)
                                ->setBankName(XT_SHIPCLOUD_BANK_NAME)
                                ->setCurrency($order->order_data['currency_code'])
                                ->setReference1($reference_cod));
                        }
                        $declared_value = trim($data['add_service_dhl_declared_value']);
                        $declared_value = doubleval(abs(str_replace(',', '.', $declared_value)));
                        if ($declared_value)
                        {
                            $package->setDeclaredValue(Shipcloud\DeclaredValue::create()
                                ->setAmount($declared_value)
                                ->setCurrency($order->order_data['currency_code']));
                        }
                    }
                    break;
                case 'dpd':
                    if ($data['add_service_dpd'] == 'on')
                    {
                        $email = trim($data['add_service_dpd_advance_notice_email']);
                        if (!empty($email))
                        {
                            $reqData->addAdditonalService(Shipcloud\AdvanceNoticeEmail::create()
                                ->setEmail($email)
                                ->setLang($order->order_data['language_code']));
                        }
                        $sms = trim($data['add_service_dpd_advance_notice_sms']);
                        $sms = preg_replace('/[0-9]/', '', $sms);
                        if (!empty($sms))
                        {
                            $reqData->addAdditonalService(Shipcloud\AdvanceNoticeSms::create()
                                ->setSms('+' . $sms));
                        }
                        $msg = trim($data['add_service_dpd_drop_authorization_msg']);
                        if (!empty($msg))
                        {
                            $reqData->addAdditonalService(Shipcloud\DropAuthorization::create()
                                ->setMessage($msg));
                        }
                        if ($data['add_service_dpd_saturday_delivery'] == 'on')
                        {
                            $reqData->addAdditonalService(Shipcloud\SaturdayDelivery::create());
                        }
                    }
                    break;
            }

            $reqData->setPackage($package);

            $from = Shipcloud\Address::create();
            if (XT_SHIPCLOUD_DIFFERENT_RETOURE === "1" && $data['shipcloud_service'] == 'returns')
            {
                $from->setFirstName($data['retoure_first_name'])
                    ->setLastName($data['retoure_last_name'])
                    ->setCompany($data['retoure_company'])
                    ->setStreet($data['retoure_street'])
                    ->setStreetNo($data['retoure_street_no'])
                    ->setCity($data['retoure_city'])
                    ->setZipCode($data['retoure_zip_code'])
                    ->setCountry($data['retoure_country'])->setPhone($data['retoure_phone']);
                if(!empty($data['retoure_state']))
                {
                    $from->setState($data['retoure_state']);
                }
            }
            else
            {
                $from->setFirstName($data['from_first_name'])
                    ->setLastName($data['from_last_name'])
                    ->setCompany($data['from_company'])
                    ->setStreet($data['from_street'])
                    ->setStreetNo($data['from_street_no'])
                    ->setCity($data['from_city'])
                    ->setZipCode($data['from_zip_code'])
                    ->setCountry($data['from_country'])->setPhone($data['from_phone']);
                if(!empty($data['from_state']))
                {
                    $from->setState($data['from_state']);
                }
            }

            if ($data['shipcloud_service'] == 'returns')
            {
                $tmp = $from;
                $from = $to;
                $to = $tmp;
            }

            $reqData->setFrom($from)->setTo($to);

            // pickup
            $pickup = null;
            if ($data['pickup'] == 1 || $data['pickup'] == 'on' || $data['shipcloud_carrier'] == 'go')
            {
                switch ($data['shipcloud_carrier'])
                {
                    case 'dhl_express':
                    case 'go':
                        $data['pickup_latest_date'] = $data['pickup_earliest_date'];
                        $data['pickup_latest_time'] = $data['pickup_earliest_time'];
                        break;
                    default:

                }
                $earliestDateTime = DateTime::createFromFormat('d.m.Y H:i', $data['pickup_earliest_date'] . ' ' . $data['pickup_earliest_time']);
                $latestDateTime = DateTime::createFromFormat('d.m.Y H:i', $data['pickup_latest_date'] . ' ' . $data['pickup_latest_time']);
                $pickupTime = new stdClass();
                $pickupTime->earliest = $earliestDateTime->format(DATE_ATOM);
                $pickupTime->latest = $latestDateTime->format(DATE_ATOM);
                $pickup = new \Shipcloud\Pickup();
                $pickup->setCarrier($data['shipcloud_carrier'])
                    ->setPickupAddress($from)
                    ->setPickupTime($pickupTime);
            }

            if ($data['quote'] == 1)
            {
                return $this->quotesGet_shipcloud($reqData);
            }
        }
        catch(Exception $e)
        {
            $r->success = false;
            $msg =  $e->getMessage();
            $r->msg = $msg;
        }

        try
        {
            $reqData->setDescription($desc)
                ->setReferenceNumber($reference)
                ->setNotificationEmail($data['to_email'])
                ->setCreateShippingLabel(true)
                ->setLabel( Shipcloud\Label::create()->setSize('A5'));
            if($pickup)
            {
                $reqData->setPickup($pickup);
            }
            if($data['prepare_label'] == 1)
            {
                $reqData->setCreateShippingLabel(false);
            }

            $log_file = _SRV_WEBROOT._SRV_WEB_LOG."shipcloud_io.log";
            $aff_id = 'integration.xtcommerce.B7HjJWzu';

            $responseCreate = shipcloud_api::getInstance(XT_SHIPCLOUD_API_KEY, $aff_id, $log_file)
                ->shipmentCreate($reqData);
            $dateCreated = new DateTime();

            $r->price = $price->_StyleFormat($responseCreate->price());

            /*
            $responseRead = shipcloud_api::getInstance(XT_SHIPCLOUD_API_KEY)
                ->shipmentRead($responseCreate->id());


            $arr = array(
                COL_SHIPCLOUD_XT_ORDER_ID => $data['orders_id'],
                COL_SHIPCLOUD_LABEL_CARRIER => $responseRead->carrier(),
                COL_SHIPCLOUD_LABEL_CARRIER_TRACKING_NO => $responseRead->carrierTrackingNo(),
                COL_SHIPCLOUD_LABEL_CREATED_AT => $responseRead->createdAt(),
                COL_SHIPCLOUD_LABEL_FROM => serialize($responseRead->from()),
                COL_SHIPCLOUD_LABEL_ID => $responseRead->id(),
                COL_SHIPCLOUD_LABEL_LABEL_URL => $responseRead->labelUrl(),
                COL_SHIPCLOUD_LABEL_NOTIFICATION_EMAIL => $responseRead->notificationEmail(),
                COL_SHIPCLOUD_LABEL_PACKAGES => serialize($responseRead->packages()),
                COL_SHIPCLOUD_LABEL_PRICE => $responseRead->price(),
                COL_SHIPCLOUD_LABEL_REFERENCE_NUMBER => $responseRead->referenceNumber(),
                COL_SHIPCLOUD_LABEL_SERVICE => $responseRead->service(),
                COL_SHIPCLOUD_LABEL_SHIPPER_NOTIFICATION_EMAIL => $responseRead->shipperNotificationEmail(),
                COL_SHIPCLOUD_LABEL_TO =>  serialize($responseRead->to()),
                COL_SHIPCLOUD_LABEL_TRACKING_URL => $responseRead->trackingUrl()

            );
            */
            $arr = array(
                COL_SHIPCLOUD_XT_ORDER_ID => $data['orders_id'],
                COL_SHIPCLOUD_LABEL_CARRIER => $data['shipcloud_carrier'],
                COL_SHIPCLOUD_LABEL_CARRIER_TRACKING_NO => $responseCreate->carrierTrackingNo(),
                COL_SHIPCLOUD_LABEL_CREATED_AT => $dateCreated->format("Y-m-d H:i:s"),
                COL_SHIPCLOUD_LABEL_FROM => serialize($from),
                COL_SHIPCLOUD_LABEL_ID => $responseCreate->id(),
                COL_SHIPCLOUD_LABEL_LABEL_URL => $responseCreate->labelUrl(),
                COL_SHIPCLOUD_LABEL_NOTIFICATION_EMAIL => $data['to_email'],
                COL_SHIPCLOUD_LABEL_PACKAGES => serialize($package),
                COL_SHIPCLOUD_LABEL_PRICE => $responseCreate->price(),
                COL_SHIPCLOUD_LABEL_REFERENCE_NUMBER => $reference,
                COL_SHIPCLOUD_LABEL_SERVICE => $data['shipcloud_service'],
                COL_SHIPCLOUD_LABEL_SHIPPER_NOTIFICATION_EMAIL => '', // TODO ?
                COL_SHIPCLOUD_LABEL_TO =>  serialize($to),
                COL_SHIPCLOUD_LABEL_TRACKING_URL => $responseCreate->trackingUrl(),
                COL_SHIPCLOUD_LABEL_PICKUP => serialize($pickup)

            );
            $sats = new xt_ship_and_track_shipcloud();
            $sats->_set($arr, 'new');

            global $db;
            $shipperId = $db->GetOne(" SELECT `".COL_SHIPPER_ID_PK."` FROM ". TABLE_SHIPPER ." WHERE `".COL_SHIPPER_CODE."`=? ", array('shipcloud-'.$data['shipcloud_carrier']));
            if(!$shipperId)
            {
                $shipperCode =  'shipcloud-'.$data['shipcloud_carrier'];
                $inserData = array(
                    COL_SHIPPER_CODE => $shipperCode,
                    COL_SHIPPER_NAME => $shipperCode,
                    COL_SHIPPER_TRACKING_URL => 'https://track.shipcloud.io/[TRACKING_CODE]',
                );
                $inserData[COL_SHIPPER_API_ENABLED] = 1;

                try {
                    $db->AutoExecute(TABLE_SHIPPER ,$inserData);
                } catch (exception $e) {
                    return $e->msg;
                }
                $shipperId = $db->Insert_ID();
                /*
                $xts = new xt_shipper();
                $xts->_installStatusCodes($shipperId, $shipperCode, array('de'));
                */
            }

            $shipcloudShipperId = $db->GetOne("SELECT ".COL_SHIPPER_ID_PK." FROM ".TABLE_SHIPPER." WHERE ".COL_SHIPPER_CODE."='shipcloud'");
            $statusId = $db->GetOne('SELECT `'.COL_TRACKING_STATUS_ID_PK.'` FROM '.TABLE_TRACKING_STATUS.' WHERE `'.COL_TRACKING_STATUS_CODE."`=1 AND `".COL_TRACKING_SHIPPER_ID. "`=?", array($shipcloudShipperId) );
            $tracking = new xt_tracking();
            $data = array(
                'orders_id' => $data['orders_id'],
                'shipper' => $shipperId,
                'tracking_codes' => $responseCreate->id(),
                'send_email' => false,
                'status' => $statusId
            );
            $tracking->addTracking($data, false);
            //$tracking->setStatus(substr($responseRead->id(), 0, 10), 1);

            /**
            COL_SHIPCLOUD_XT_ORDER_ID
            COL_SHIPCLOUD_LABEL_CARRIER
            COL_SHIPCLOUD_LABEL_CARRIER_TRACKING_NO
            COL_SHIPCLOUD_LABEL_CREATED_AT
            COL_SHIPCLOUD_LABEL_FROM
            COL_SHIPCLOUD_LABEL_ID
            COL_SHIPCLOUD_LABEL_LABEL_URL
            COL_SHIPCLOUD_LABEL_NOTIFICATION_EMAIL
            COL_SHIPCLOUD_LABEL_PACKAGES
            COL_SHIPCLOUD_LABEL_PRICE
            COL_SHIPCLOUD_LABEL_REFERENCE_NUMBER
            COL_SHIPCLOUD_LABEL_SERVICE
            COL_SHIPCLOUD_LABEL_SHIPPER_NOTIFICATION_EMAIL
            COL_SHIPCLOUD_LABEL_TO
            COL_SHIPCLOUD_LABEL_TRACKING_URL
             */
        }
        catch(Exception $e)
        {
            $r->success = false;
            $msg = explode('|', $e->getMessage());
            $msg[0] = $msg[0].'::'.$msg[1];
            unset($msg[1]);
            $r->msg = implode("<br/>", $msg);
        }

        return json_encode($r);
    }



    /**
     *
     * @return array die verfügbaren store names für DropdownData
     */
    public static function getStores()
    {
        $data = array();

        global $store_handler;
        $_data = $store_handler->getStores();

        foreach ($_data as $sdata) {
            $data[] =  array('id' => $sdata['id'],
                'name' => $sdata['text']);
        }

        return $data;
    }

    /**
     *
     * @return array die verfügbaren hermes status codes für DropdownData
     */
    public static function getStatusCodes()
    {
        global $db;
        $shipperId = $db->GetOne(" SELECT `".COL_SHIPPER_ID_PK."` FROM ". TABLE_SHIPPER ." WHERE `".COL_SHIPPER_CODE."`='hermes'");

        $sql = "SELECT * FROM ".TABLE_TRACKING_STATUS." WHERE ".COL_TRACKING_SHIPPER_ID."=?";
        $rs = $db->Execute($sql, array($shipperId));

        $data = array();
        if ($rs->RecordCount()>0)
        {
            while (!$rs->EOF) {
                $data[] =  array('id' => $rs->fields[COL_TRACKING_STATUS_CODE],
                    'name' => __define('TEXT_HERMES_'.str_replace('-','_',$rs->fields[COL_TRACKING_STATUS_CODE]).'_SHORT'));
                $rs->MoveNext();
            }
        }
        $rs->Close();

        return $data;
    }


    /**
     * Split a string into an array consisting of Street, House Number and
     * House extension.
     *
     * @param string $address Address string to split
     *
     * @return array
     */
    public static function splitAddress($address)
    {
        /**
         *  ok, wir versuchen also aus irgend'ner zeile eine strasse, hausnummer und extension zu ermitteln ...
         *  alle begriffe daher in anführungszeichen ...
         */

        $address = trim($address);
        // ist die 'hausnummer' vorne...
        $hasMatchStart = preg_match('/^[0-9]+/', $address, $match);
        $hasMatchEnd = 0;
        if($hasMatchStart == 0)
        {
            // ... oder hinten
            $hasMatchEnd = preg_match('/[0-9]+.*$/', $address, $match);
            // aber nicht in der mitte
            $startsWithString = preg_match('/[0-9]+.*$/', $address, $match2);
            if($startsWithString)
                $hasMatchEnd = 1;
        }

        // weder noch, zurück mit eingabe
        if (!$hasMatchEnd && !$hasMatchStart) {
            return array($address, "", "");
        }

        if($hasMatchStart)
        {
        $addrArray = explode(" ", $address);

            // wenn vorne, ist 1stes element unsere 'hausnummer'
            $housenumber = trim($addrArray[0]);

            // den rest sehen wir als 'strasse'
            $street = trim(str_replace($housenumber, "", $address));

            // addressAddition lässt sich hier nicht sinnvoll ermitteln
            return array($street, $housenumber, '');
        }
        else {
            // 'hausnummer'
            $housenumber = trim($match[0]);

            // strasse
            $street = trim(str_replace($housenumber, "", $address));

            // hängt da noch eine extension an der hausnummer dran? puh
            $addrArray = explode(" ", $housenumber);
            if(count($addrArray)>1)
            {
                unset($addrArray[0]);
                $extension = trim(implode(" ", $addrArray));
                $housenumber = trim(str_replace($extension, "", $housenumber));
            }
            else {
                $extension = '';
            }

        return array($street, $housenumber, $extension);
    }
    }

    public static function getSplit($country = false)
    {
        return array('street', 'house_number', 'house_extension');
    }

    public static function formatShipcloudEvent($se, $glueLine = ' | ', $glueParam = ': ')
    {
        if(is_object($se))
        {
            $se = (array)$se;
        }
        $ts = new DateTime($se['timestamp']);
        $msgData = array(
            'Zeitstempel'.$glueParam . $ts->format('Y-m-d H:i:s'),
            'Ort'.$glueParam . $se['location'],
            'Details'.$glueParam . $se['details']
        );
        return implode($glueLine, $msgData);
    }

    public static function getXtStatusCodeFromShipcloudStatus($status = 'unknown')
    {
        $map = array(
           'label_created' => 1,
            'picked_up' => 2,
            'transit' => 3,
            'out_for_delivery' => 4,
            'delivered' => 5,
            'awaits_pickup_by_receiver' => 7,
            'canceled' => 8,
            'delayed' => 9,
            'exception' => 10,
            'not_delivered' => 11,
            'notification' => 12,
            'destroyed' => 13,
            'unknown'   => 14
        );
        if(array_key_exists($status, $map))
        {
            return $map[$status];
        }
        else return $map['unknown'];
    }

}