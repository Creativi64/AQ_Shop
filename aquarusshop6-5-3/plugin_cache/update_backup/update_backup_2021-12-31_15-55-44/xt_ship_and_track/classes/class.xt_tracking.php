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

require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_ship_and_track/classes/class.tracking.php';
require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_ship_and_track/classes/class.xt_ship_and_track.php';


/**
 * Funktionen zur Darstellung im Backend/Frontend
 */
class xt_tracking extends tracking
{
    private $_master_key = 'id';

    function setPosition($position)
    {
        $this->position = $position;
    }

    function _getParams()
    {
        $header = array();

        $params = array();
        $params['header'] = $header;
        $params['master_key'] = $this->_master_key;
        $params['display_deleteBtn'] = false;
        $params['display_resetBtn'] = false;
        $params['display_editBtn'] = false;
        $params['display_newBtn'] = false;
        $params['display_searchPanel']  = false;

        return $params;
    }

    public function addTracking($data, $cascadeShipper = true)
    {
        $trackinCodes = array();

        foreach(preg_split("/((\r?\n)|(\r\n?))| |,|;|(s\\s+)/", $data['tracking_codes']) as $code)
        {
            $code = trim($code);
            if (!empty($code))
            {
                $trackinCodes[] = $code;
            }
        }

        $trackinCodes = array_unique($trackinCodes);

        if(count($trackinCodes)==0 || empty($data['orders_id']) || empty($data['shipper']))
        {
            $result = new stdClass();
            $result->success = false;
            $result->msg = empty($data['shipper']) ? 'Versender/Shipper ?!' : (count($trackinCodes)==0 ? 'Sendungsnummer/Tracking code ?!' : 'no order id!');
            $result->errorMsg = empty($data['shipper']) ? 'Versender/Shipper ?!' : (count($trackinCodes)==0 ? 'Sendungsnummer/Tracking code ?!' : 'no order id!');
            return $result;
        }

        global $ADODB_THROW_EXCEPTIONS;
        $ADODB_THROW_EXCEPTIONS = true;

        $result = new stdClass();
        try
        {
        $result = parent::setTracking($data['orders_id'],$data['shipper'], $trackinCodes, $data['send_email'], $data['status']);

        global $db;
        $hermesShipperId = $db->GetOne(" SELECT `".COL_SHIPPER_ID_PK."` FROM ". TABLE_SHIPPER ." WHERE `".COL_SHIPPER_CODE."`='hermes'");
        if ($hermesShipperId==$data['shipper'] && $cascadeShipper)
        {
            $xt_hermes = new xt_ship_and_track();
            foreach($trackinCodes as $orderNo)
            {
                $hermesData = array(
                    'orders_id' => $data['orders_id'],
                    'orderNo' => $orderNo,
                );
                $xt_hermes->saveOrder($hermesData, false);
            }
        }
            //$result->msg = 'OK';
        }
        catch(Exception $e)
        {
            $result->success = false;
            $result->msg = "Error: ".$e->getMessage();
        }
        finally{
            $ADODB_THROW_EXCEPTIONS = false;
        }

        return $result;
    }

    public function deleteTracking($data)
    {
        $cascadeShipper = $data['cascade'];

        $r = new stdClass();
        $r->success = false;
        $r->msg = false;
        $r->errorMsg = false;

        if ($cascadeShipper || $data['cascadeShipper'])
        {
            $xtHermes = new xt_ship_and_track();
            $hResult = $xtHermes->deleteOrder($data);
            $hResult = json_decode($hResult);
            if ($hResult->code == '312311' || $hResult->success == false)
            {
                return json_encode($hResult);
            }
        }

        global $db;
        $db->Execute("DELETE FROM ".TABLE_TRACKING." where `".COL_TRACKING_CODE."` = ?", array($data[COL_TRACKING_CODE]));

        $r->success = true;
        $r->msg = __define('TEXT_SUCCESS');
        return json_encode($r);
    }

    public function setStatus($trackingCode, $statusCode, $shipperId = false)
    {
        global $db;
        if(!$shipperId)
        {
            $sql = "SELECT `" . COL_TRACKING_SHIPPER_ID . "` FROM `" . TABLE_TRACKING . "` WHERE `" . COL_TRACKING_CODE . "`=?";
            $shipperId = $db->GetOne($sql, array($trackingCode));
        }
        if (!$shipperId) return;
        $sql = "SELECT `".COL_TRACKING_STATUS_ID_PK ."` FROM `".TABLE_TRACKING_STATUS."` WHERE `".COL_TRACKING_STATUS_CODE."`=? AND `".COL_TRACKING_SHIPPER_ID."`=?";
        $statusId = $db->GetOne($sql, array($statusCode, $shipperId));
        $sql = "UPDATE ".TABLE_TRACKING." SET `".COL_TRACKING_STATUS_ID."`=? WHERE `".COL_TRACKING_CODE."`=?";
        $r = $db->Execute($sql, array($statusId, $trackingCode));
        return $r;
    }

    public function sendTrackingEmail($data)
    {
        global $xtPlugin;

        if ($data['tracking_code'] && $data['tracking_code']!='send_all')
        {
            $result = parent::sendTrackingMail($data['orders_id'], array($data['tracking_code']));
        }
        else{
            $result = parent::sendTrackingMail($data['orders_id']);
        }

        ($plugin_code = $xtPlugin->PluginCode('class.xt_tracking.php:_sendTrackingEmail_bottom')) ? eval($plugin_code) : false;

        return json_encode($result);
    }

    public static function orderEdit_displayTrackings($orders_id)
    {
        $htmlRows ='';
        $trackings = parent::getTrackingForOrder($orders_id);
        if (count($trackings)>0) {

            $apiCount = 0;
            $hermesCount = 0 ;
            foreach ($trackings as $t) {
                $api = 0;
                $shipper_code = $t['shipper_code'];
                if(strpos($shipper_code, 'shipcloud-') === 0)
                {
                    $shipper_code = 'shipcloud';
                }
                switch ($shipper_code)
                {
                    case 'hermes':
                        $hermesCount++;
                        break;
                }

                $keyStatus = 'TEXT_'.strtoupper($shipper_code).'_'.strtoupper(str_replace('-','_',$t[COL_TRACKING_STATUS_CODE])).'_SHORT';
                $btnRefresh = '';
                $btnPrint = '';
                if ($t[COL_SHIPPER_API_ENABLED])
                {
                    $api = true;
                    $apiCount++;
                    $showPositionDialog = false;
                    if($t[COL_SHIPPER_CODE] == 'hermes')
                    {
                        $showPositionDialog = true;
                    }
                    $btnRefresh = '&nbsp;<a id="'.$t[COL_TRACKING_CODE].'" onclick="refreshStatus'.$orders_id.'(this.id)" href="javascript:void(0)"><img alt="'.TEXT_HERMES_REFRESH.'" title="'.TEXT_HERMES_REFRESH.'" src="images/icons/arrow_refresh_small.png" /></a>';
                    $btnPrint = '&nbsp;<a id="'.$t[COL_TRACKING_CODE].'" onclick="printLabel'.$orders_id.'(this.id,'.$showPositionDialog.')" href="javascript:void(0)"><img  alt="'.TEXT_PRINT_LABEL.'" title="'.TEXT_PRINT_LABEL.'"  src="images/icons/printer.png" /></a>';
                }

                $trackingUrl = str_replace('[TRACKING_CODE]',$t[COL_TRACKING_CODE],$t[COL_SHIPPER_TRACKING_URL]);

                $htmlRows .='<tr>'.
                    '<td style="text-align:left;padding-left:5px" >'.$t[COL_TRACKING_CODE].'</td>'.
                    '<td style="text-align:left;">'.__define($keyStatus).'</td>'.
                    '<td style="text-align:left;">'.$t[COL_SHIPPER_NAME].'</td>'.
                    '<td style="text-align:left;">'.$t[COL_TRACKING_ADDED].'</td>'.
                    '<td style="text-align:left;padding-left:5px">'.
                        '<a target="_blank" href="'.$trackingUrl.'"><img alt="'.TEXT_SHIPPERS_PAGE.'" title="'.TEXT_SHIPPERS_PAGE.'" src="images/icons/package_go.png" /></a>'.
                        '&nbsp;<a id="'.$t[COL_TRACKING_CODE].'" onclick="sendTrackingMail'.$orders_id.'(this.id)" href="javascript:void(0)"><img alt="'.TEXT_SEND_MAIL.'" title="'.TEXT_SEND_MAIL.'" src="images/icons/email.png" /></a>'.
                        '&nbsp;<a id="'.$t[COL_TRACKING_CODE].'" onclick="deleteTracking'.$orders_id.'(this.id,'.$api.')" href="javascript:void(0)"><img  alt="'.TEXT_DELETE.'" title="'.TEXT_DELETE.'" src="images/icons/delete.png" /></a>'.
                        $btnRefresh.$btnPrint.
                    '</td>'.
                    '</tr>';
            }
            $refreshLink = '';
            $printLink = '';
            if ($apiCount>1)
            {
                $refreshLink = '&nbsp;<a id="refresh_order" onclick="refreshStatus'.$orders_id.'(this.id)" href="javascript:void(0)"><img alt="'.TEXT_HERMES_REFRESH.'" title="'.TEXT_HERMES_REFRESH.'" src="images/icons/arrow_refresh_small.png" /></a>';
                if ($hermesCount>1)
                    $printLink = '&nbsp;<a id="print_all" onclick="printLabels'.$orders_id.'()" href="javascript:void(0)"><img  alt="'.TEXT_PRINT_LABEL.'" title="'.TEXT_PRINT_LABEL_HERMES.'" src="../../../plugins/xt_ship_and_track/images/icons/printer_hermes.png" /></a>';

            }
            $btnEmail = '';
            if (sizeof($trackings)>1)
            {
                $btnEmail = '&nbsp;<a id="send_all" onclick="sendTrackingMail'.$orders_id.'(this.id)" href="javascript:void(0)"><img alt="'.TEXT_SEND_MAIL.'" title="'.TEXT_SEND_MAIL.'" src="images/icons/email.png" /></a>';
            }
            $htmlHead ='<table cellspacing="0" width="100%" class="table table-striped orderstatushistory">';
            $htmlHead .='<thead><tr>'.
                '<th class="">'.TEXT_TRACKING_CODE.'</th>'.
                '<th class="">'.TEXT_STATUS.'</th>'.
                '<th class="">'.TEXT_TRACKING_SHIPPER.'</th>'.
                '<th class="">'.TEXT_DATE_ADDED.'</th>'.
                '<th class="">'.TEXT_ACTION.$btnEmail.$refreshLink.$printLink.'</th>'.
                '</tr></thead>';
            $htmlHead .= '<tbody>';

            $html = $htmlHead.$htmlRows.'</tbody></table><br /><br />';
        }
        else{
            return false;
        }
        return $html;
    }


    public static function orderEdit_displayTrackingsJs($orders_id)
    {
        $add_to_url = (isset($_SESSION['admin_user']['admin_key']))? ",sec:'".$_SESSION['admin_user']['admin_key']."'": '';
        $add_to_url_abs = (isset($_SESSION['admin_user']['admin_key']))? '&sec='.$_SESSION['admin_user']['admin_key']: '';
        $js = "function sendTrackingMail".$orders_id."(tracking_code) {
          Ext.Msg.show({title:'".TEXT_CUSTOMERS_SEND."',
    				msg: '".TEXT_START_ASK."',
    				buttons: Ext.Msg.YESNO,
    				animEl: 'elId',
    				fn: function(btn){sendTrackingMail".$orders_id."Request(tracking_code,btn);},
    				icon: Ext.MessageBox.QUESTION
          })};";


        $js .= "
        function sendTrackingMail".$orders_id."Request(tracking_code,btn)
        {

            if (btn == 'yes') {

            var lm = new Ext.LoadMask(Ext.getBody(),{msg:'".__define('TEXT_SENDING_TRACKING_MAIL')."'});
            lm.show();

            var orders_id = ".$orders_id.";
            var conn = new Ext.data.Connection();
            conn.request({
                url: 'adminHandler.php',
                method:'GET',
                params: {
                    pg:             'sendTrackingEmail',
                    load_section:   'xt_tracking',
                    plugin:         'xt_ship_and_track',
                    tracking_code:      tracking_code,
                    orders_id: orders_id".$add_to_url."
                },

                success: function(responseObject)
                {
                    lm.hide();
                    var r = Ext.decode(responseObject.responseText);
                    //console.log('success');
                    //console.log(r);
                    if (!r.success)
                    {
                        Ext.MessageBox.alert('".__define('TEXT_ALERT')."',  r.errorMsg);
                    }
                    else
                    {
                        Ext.MessageBox.alert('".__define('TEXT_ALERT')."','".TEXT_TRACKING_MAIL_SENT."');
                    }
                },
                failure: function(responseObject)
                {
                lm.hide();
                var r = Ext.decode(responseObject.responseText);
                    //console.log('fail');
                    //console.log(r);
                    var title = responseObject.statusText ? 'Error '+responseObject.status : 'Error ';
                    var msg = responseObject.statusText ? responseObject.statusText : 'No Details available';
                    Ext.MessageBox.alert(title,msg);
                    //console.log(responseObject)
                }
            });

          }
        };\n";

        $js .= "
        function refreshStatus".$orders_id."(tracking_code)
        {
            var lm = new Ext.LoadMask(Ext.getBody(),{msg:'".__define('TEXT_HERMES_CHECKING_STATUS')."'});
            lm.show();

            var orders_id = ".$orders_id.";
            var conn = new Ext.data.Connection();
            conn.request({
                url: 'adminHandler.php',
                method:'GET',
                params: {
                    pg:             'updateStatus',
                    load_section:   'xt_ship_and_track', // TDOD v1.x load_section soll tracking provider entsprechen
                    plugin:         'xt_ship_and_track',
                    tracking_code:      tracking_code,
                    orders_id: orders_id".$add_to_url."
                },
                waitMsg: 'Laden',
                success: function(responseObject)
                {
                    var r = Ext.decode(responseObject.responseText);
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
        };\n";

        $js .= "function deleteTracking".$orders_id."(tracking_code,api) {
          Ext.Msg.show({title:'".TEXT_DELETE."',
            msg: '".TEXT_START_ASK."',
            buttons: Ext.Msg.YESNO,
            animEl: 'elId',
            fn: function(btn){deleteTracking".$orders_id."Request(tracking_code, api,btn);},
            icon: Ext.MessageBox.QUESTION
          })};";

        $js .= "
        function deleteTracking".$orders_id."Request(tracking_code, api, btn)
        {

            if (btn == 'yes') {
            var lm = new Ext.LoadMask(Ext.getBody(),{msg:'".__define('TEXT_DELETING_TRACKING')."'});
            lm.show();
            //console.log(api);

            var conn = new Ext.data.Connection();
            conn.request({
                url: 'adminHandler.php',
                method:'GET',
                params: {
                    pg:             'deleteTracking',
                    load_section:   'xt_tracking',
                    plugin:         'xt_ship_and_track',
                    tracking_code:      tracking_code,
                    orders_id:      '".$orders_id."',
                    cascade: api".$add_to_url."
                },
                success: function(responseObject)
                {
                    var r = Ext.decode(responseObject.responseText);
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
                    var title = responseObject.statusText ? '".__define('TEXT_ALERT')."'+responseObject.status : '".__define('TEXT_ALERT')."';
                    var msg = responseObject.statusText ? responseObject.statusText : 'No Details available';
                    Ext.MessageBox.alert(title,msg);
                }
            });
          }
        };\n";

        $js .= "
            function printLabel".$orders_id."(tracking_code, showPositionDialog)
        {
            if(!showPositionDialog)
            {
                window.open('adminHandler.php?plugin=xt_ship_and_track&load_section=xt_ship_and_track&pg=printLabel&type=pdf".$add_to_url_abs."&tracking_code='+tracking_code+'&orders_id=".$orders_id."','_blank');
                return;
            }

            var msg = '<input checked=\"1\" value=\"1\" id=\"printPosition1\" name=\"printPosition\" type=\"radio\" /> oben links<br /><input value=\"2\" id=\"printPosition2\" name=\"printPosition\" type=\"radio\" /> oben rechts<br /><input value=\"3\" id=\"printPosition3\" name=\"printPosition\" type=\"radio\" /> unten links<br /><input value=\"4\" id=\"printPosition4\" name=\"printPosition\" type=\"radio\" /> unten rechts<br />';
            Ext.MessageBox.show({
               title:    'Druckposition wählen',
               msg:     msg,
               buttons:  Ext.MessageBox.OKCANCEL,
               fn: function(btn) {
                  if( btn == 'ok') {
                      var pos = $(\"input:radio[name ='printPosition']:checked\").val();
                      //alert(pos);
                      window.open('adminHandler.php?plugin=xt_ship_and_track&load_section=xt_ship_and_track&pg=printLabel&type=pdf".$add_to_url_abs."&tracking_code='+tracking_code+'&pos='+pos+'&orders_id=".$orders_id."','_blank');
                  }
               }
            });
            }";

        $js .= "
            function printLabels".$orders_id."()
            {
                window.open('adminHandler.php?plugin=xt_ship_and_track&load_section=xt_ship_and_track&pg=printLabelsPdf".$add_to_url_abs."&orders_id=".$orders_id."','_blank');
            }";

        return $js;
    }

    public static function getTrackingPanel($orders_id)
    {
        $trackingHtml = self::orderEdit_displayTrackings($orders_id);
        if (!$trackingHtml)
            return false;
        $trackingTemplate = new PhpExt_XTemplate($trackingHtml);

        $trackingPanel = new PhpExt_Panel();
        $trackingPanel->setAutoScroll(true)
            ->setTitle(__define('TEXT_TRACKING'))
            ->setAutoWidth(true)
            ->setHtml($trackingTemplate)
            ->setAutoLoad(false);

        return $trackingPanel;
    }

    public static function orderEdit_displayAddTracking($orders_id)
    {
        $addTrackingPanel = self::getAddTrackingPanel($orders_id);

        $js= PhpExt_Ext::onReady(
            $addTrackingPanel->getJavascript(false, "addTrackingPanel")
        );

        return '';//$js;
    }


    static function getAddTrackingPanel($orders_id)
    {

        $add_to_url = (isset($_SESSION['admin_user']['admin_key']))? ",sec:'".$_SESSION['admin_user']['admin_key']."'": '';
        $add_to_url_abs = (isset($_SESSION['admin_user']['admin_key']))? '&sec='.$_SESSION['admin_user']['admin_key']: '';
        $Panel = new PhpExt_Form_FormPanel('addTrackingForm'.$orders_id);
        $Panel->setTitle(__define('TEXT_ADD_TRACKING'))
            ->setId('addTrackingForm'.$orders_id)
            ->setBodyStyle('padding: 5px 5px 0 5px;min-height:120px;')
            ->setAutoWidth(true)->setAutoHeight(true)
            ->setUrl("adminHandler.php?plugin=xt_ship_and_track&load_section=xt_tracking&pg=addTracking&orders_id=".$orders_id.$add_to_url_abs)
            ->addItem(PhpExt_Form_TextArea::createTextArea(
                'tracking_codes', __define('TEXT_TRACKING_CODE'))->setWidth(500)->setHeight(60)->setGrowMax(60)->setCssStyle('max-height:60px;max-width:500px'));


        $eF = new ExtFunctions();
        $combo = $eF->_comboBox('shipper', __define('TEXT_TRACKING_SHIPPER'), 'DropdownData.php?get=tracking_shippers&plugin_code=xt_ship_and_track');
        $combo->setAutoWidth(true);

        $Panel->addItem($combo);

        $Panel->addItem(PhpExt_Form_Checkbox::createCheckbox('send_email', __define('TEXT_SEND_MAIL')));

        $submitBtn = PhpExt_Button::createTextButton(__define("BUTTON_SAVE"),
            new PhpExt_Handler(PhpExt_Javascript::stm("Ext.getCmp('addTrackingForm".$orders_id."').getForm().submit({
												   waitMsg:'Saving Data...',
												   success: function(form, action) {
												        var r = action.result;
												        //console.log(r);
                                                        if (!r.success)
                                                        {
                                                        console.log(r);
                                                            Ext.Msg.alert('".__define('TEXT_ALERT')."', r.msg);
                                                        }
                                                        else
                                                        {
                                                            if(r.msg)
                                                            {
                                                            Ext.MessageBox.alert('".__define('TEXT_ALERT')."',r.msg, function(btn, text){
                                                                if(typeof r.event != 'undefined')
                                                                {
                                                                    var data = {};
                                                                    if(typeof r.eventData != 'undefined')
                                                                    {
                                                                        data = r.eventData;
                                                                    }
                                                                    var event = new CustomEvent(r.event, {detail: data});
                                                                    window.dispatchEvent(event);
                                                                }
                                                            });
                                                        }
                                                            else {
                                                                console.log('no msg');
                                                                if(typeof r.event != 'undefined')
                                                                    {
                                                                        var data = {};
                                                                        if(typeof r.eventData != 'undefined')
                                                                        {
                                                                            data = r.eventData;
                                                                        }
                                                                        var event = new CustomEvent(r.event, {detail: data});
                                                                        window.dispatchEvent(event);
                                                                    }
                                                            }
                                                        }
                                                        contentTabs.getActiveTab().getUpdater().refresh()
                                                   },
                                                    failure: function(form, action)
                                                    {
                                                        var r = action.result;
                                                        var msg = 'Es ist ein Fehler auftreten. Prüfen Sie die Logs<br />An error occured. Check logs';
                                                        if(r && r.msg)
                                                        {
                                                            msg = r.msg;
                                                        }
                                                        Ext.Msg.alert('".__define('TEXT_ALERT')."', msg);
                                                    }
												   })"))
        );

        $submitBtn->setType(PhpExt_Button::BUTTON_TYPE_SUBMIT);
        $Panel->addButton($submitBtn);

        return $Panel;
    }

    public static function getShippers($onlyActive = true)
    {
        $shippers = parent::getShippers($onlyActive);

        $data = array();
        foreach ($shippers as $sdata) {
            if ($sdata[COL_SHIPPER_CODE]=='hermes')
            {
            $data[] =  array('id' => $sdata['id'],
                'name' => $sdata['shipper_name']);
            }
        }

        foreach ($shippers as $sdata) {
            if ($sdata[COL_SHIPPER_CODE]!='hermes')
            {
                $data[] =  array('id' => $sdata['id'],
                    'name' => $sdata['shipper_name']);
            }
        }

        return $data;
    }
}
