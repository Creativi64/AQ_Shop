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

require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_ship_and_track/classes/class.xt_ship_and_track.php';

class xt_ship_and_track_layoutPart extends xt_backend_cls {

    private $_master_key = 'id';

    function setPosition($position)
    {
        $this->position = $position;
    }

    function getdefaultParams(&$header = [], &$params = [])
    {
        $header[COL_HERMES_ID_PK] = array( 'readonly'=>true);
        $header[COL_HERMES_XT_ORDER_ID] = array('readonly'=>true);
        $header[COL_HERMES_ORDER_NO] = array('readonly'=>true);
        $header[COL_HERMES_SHIPPING_ID] = array('type' => 'textfield', 'readonly'=>true);
        $header[COL_HERMES_STATUS] = array('type' => 'textfield', 'readonly'=>true);
        $header[COL_HERMES_COLLECT_DATE] = array('type' => 'textfield', 'readonly'=>true);
        $header[COL_HERMES_AMOUNT_CASH_ON_DELIVERY] = array('type' => 'textfield', 'width' => 310);
        $header[COL_HERMES_BULK_GOOD ] = array('type' => 'status');
        $header[COL_HERMES_PARCEL_CLASS] = array('type'=>'dropdown', 'width' => 100, 'url'  => 'DropdownData.php?get=hermes_parcel_class&plugin_code=xt_ship_and_track','text'=>TEXT_PARCEL_CLASS);

        $params['header'] = $header;
        $params['master_key'] = $this->_master_key;
        $params['display_deleteBtn'] = false;
        $params['display_resetBtn'] = true;
        $params['display_editBtn'] = true;
        $params['display_newBtn'] = false;
        $params['display_searchPanel']  = true;

        $params['display_checkCol']  = true;
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

            $js = "Ext.MessageBox.show({
                   title:    'Druckposition wählen',
                   msg:      '<input checked=\"1\" value=\"1\" id=\"printPosition1\" name=\"printPosition\" type=\"radio\" /> oben links<br /><input value=\"2\" id=\"printPosition2\" name=\"printPosition\" type=\"radio\" /> oben rechts<br /><input value=\"3\" id=\"printPosition3\" name=\"printPosition\" type=\"radio\" /> unten links<br /><input value=\"4\" id=\"printPosition4\" name=\"printPosition\" type=\"radio\" /> unten rechts<br />',
                   buttons:  Ext.MessageBox.OKCANCEL,
                   fn: function(btn) {
                      if( btn == 'ok') {
                          var pos = $(\"input:radio[name ='printPosition']:checked\").val();
                          //alert(pos);
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
                        cascade: 1,
                        sec:            csrf_key
                    },
                    waitMsg: '".TEXT_SHIP_AND_TRACK_WAIT."',
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
                    tracking_code: 'refresh_all'
                    sec:            csrf_key
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

    function _get($ID = 0, $format = true)
    {
        if ($this->position != 'admin') return false;

        $where = '';
        if($this->url_data['query'])
        {
            $where = "(" .COL_HERMES_ORDER_NO." LIKE '%".$this->url_data['query']."%' OR ". COL_HERMES_SHIPPING_ID." LIKE '%".$this->url_data['query']."%' )";
        }

        if (!$ID && !isset($this->sql_limit)) {
            $this->sql_limit = "0,50";
        }

        $table_data = new adminDB_DataRead(TABLE_HERMES_ORDER, '', '', $this->_master_key, $where , '', '', '',  'ORDER BY '.COL_HERMES_ID_PK. ' DESC ');
        if ($this->url_data['get_data']) {
            $data = $table_data->getData();
            foreach($data as $k=>$d)
            {
                if ($format)
                {
                    $data[$k][COL_HERMES_STATUS] = __define('TEXT_HERMES_'.$d[COL_HERMES_STATUS].'_SHORT');
                    $data[$k][COL_HERMES_COLLECT_DATE] = $data[$k][COL_HERMES_COLLECT_DATE] ? date('Y-m-d', strtotime($data[$k][COL_HERMES_COLLECT_DATE])) : '';
                }
            }
        }

        elseif($ID) {
            $data = $table_data->getData($ID);
            $defaultOrder = array(
                COL_HERMES_ID_PK,
                COL_HERMES_XT_ORDER_ID,
                COL_HERMES_ORDER_NO,
                COL_HERMES_SHIPPING_ID,
                COL_HERMES_SHIPPING_ID,
                COL_HERMES_COLLECT_DATE,
                COL_HERMES_STATUS,
                COL_HERMES_AMOUNT_CASH_ON_DELIVERY,
                COL_HERMES_PARCEL_CLASS,
                COL_HERMES_BULK_GOOD
            );

            $orderedData = array();
            foreach ($defaultOrder as $key) {
                $orderedData[$key] = $data[0][$key];
            }
            if ($format) {
                $orderedData[COL_HERMES_STATUS] = __define('TEXT_HERMES_'.$orderedData[COL_HERMES_STATUS].'_SHORT');
                $orderedData[COL_HERMES_COLLECT_DATE] = $orderedData[COL_HERMES_COLLECT_DATE] ? date('Y-m-d', strtotime($orderedData[COL_HERMES_COLLECT_DATE])) : '';
            }
            $data = array($orderedData);

        } else {
            $data = $table_data->getHeader();
            $defaultOrder = array(
                COL_HERMES_ID_PK,
                COL_HERMES_XT_ORDER_ID,
                COL_HERMES_ORDER_NO,
                COL_HERMES_SHIPPING_ID,
                COL_HERMES_SHIPPING_ID,
                COL_HERMES_PARCEL_CLASS,
                COL_HERMES_STATUS,
                COL_HERMES_BULK_GOOD,
                COL_HERMES_AMOUNT_CASH_ON_DELIVERY,
                COL_HERMES_COLLECT_DATE
            );
            $orderedData = array();
            foreach ($defaultOrder as $key) {
                $orderedData[$key] = $data[0][$key];
            }
            $data = array($orderedData);

        }

        $obj = new stdClass;
        if ($table_data->_total_count != 0 || !$table_data->_total_count)
            $count_data = $table_data->_total_count;
        else
            $count_data = count($data);
        $obj->totalCount = $count_data;
        $obj->data = $data;
        return $obj;
    }

    function _set($data, $set_type = 'edit')
    {
        global $db;
        $sql = "SELECT `".COL_HERMES_XT_ORDER_ID."` FROM `".TABLE_HERMES_ORDER."` WHERE `".COL_HERMES_ID_PK."`=?";
        $xtOrderId = $db->GetOne($sql, array($_REQUEST['edit_id']));
        $data['orders_id'] = $xtOrderId;
        $sql = "SELECT `".COL_HERMES_ORDER_NO."` FROM `".TABLE_HERMES_ORDER."` WHERE `".COL_HERMES_ID_PK."`=?";
        $orderNo = $db->GetOne($sql, array($_REQUEST['edit_id']));
        $data['orderNo'] = $orderNo;
        $data['edit_id'] = $_REQUEST['edit_id'];
        $r = $this->saveOrder($data);

        return json_decode($r);
    }

    function _unset($id = 0)
    {
        return false;
    }

    public static function orderEdit_userButtonsWindows($orderId)
    {
        global $db;

        $sandbox = XT_SHIPCLOUD_SANDBOX == 1 ? '   <span style="color:#c6080b">SANDBOX</span>' : '';
        $infoText = ' ('.TEXT_SHIPCLOUD_ORDER.' '.$orderId. ')'.$sandbox;
        define("TEXT_SHIPCLOUD_CREATE_LABEL".$orderId , "shipcloud".$infoText);

        $extF_editAddress = new ExtFunctions();
        $code = 'shipcloud_add_parcel';
        $extF_editAddress->setCode($code);
        $panel = self::getAddParcelWindowPanel_shipcloud($orderId);
        $remoteWindow = ExtFunctions::_RemoteWindow2(TEXT_SHIPCLOUD .$infoText, $code, $panel, 700, 725, 'window');
        $remoteWindow->setModal(true)
            ->attachListener('render',
                new PhpExt_Listener(PhpExt_Javascript::functionDef(null, "
                this.setPosition(this.x, 20);
            "))
            );

        $js_add_parcel = "var orders_id = ".$orderId.";";
        $js_add_parcel.= $remoteWindow->getJavascript(false, "new_window").' new_window.show();';
        $UserButtons[$code] = array('text'=>TEXT_SHIPCLOUD, 'style'=>$code, 'icon'=>'../../../plugins/xt_ship_and_track/images/icons/shipcloud16.png', 'acl'=>'edit_address', 'stm' => $js_add_parcel);
        $params['display_'.$code.'Btn'] = true;


        // panel and buttons
        $pnl = new PhpExt_Panel();

        $pnl->getTopToolbar()->addTextItem('Shipcloud', '<span style="font-weight: bold">Shipcloud</span>');
        $pnl->getTopToolbar()->addSeparator('|');

        $pnl->getTopToolbar()->addButton(1, TEXT_SHIPCLOUD_CREATE_LABEL, null/*'/../plugins/xt_ship_and_track/images/icons/shipcloud16.png'*/, new PhpExt_Handler(PhpExt_Javascript::stm($js_add_parcel)));
        $pnl->setRenderTo(PhpExt_Javascript::variable("Ext.get('shipCloudMenubar'+".$orderId.")"));
/*
        $js = PhpExt_Ext::onReady(
            '$($("#memoContainer"+'.$orderId.').parent().find(\'div.text\')[0]).css(\'margin-top\', \'30px\');',
            '$("#memoContainer"+'.$orderId.').parent().find(\'#order-menubar'.$orderId.'\').append("<div id=\'shipCloudMenubar'.$orderId.'\'></div>");',
            $pnl->getJavascript(false, "shipCloudMenubar")
        );
*/
        return $pnl;
    }



    /**
     *     tabs and panels
     */

    /**
     * @param $orders_id
     * @return string
     */
    public static function orderEdit_displayAddParcel($orders_id)
    {
        $addParcelPanel = self::getAddParcelPanel($orders_id);

        $js= PhpExt_Ext::onReady(
            $addParcelPanel->getJavascript(false, "addTrackingPanel")
        );

        return $js;
    }

    static function getAddParcelPanel($orders_id)
    {
        $Panel = new PhpExt_Form_FormPanel('addParcelForm');
        $Panel->setId('addParcelForm'.$orders_id)
            ->setTitle(__define('TEXT_ADD_HERMES_PARCEL'))
            ->setAutoWidth(true)
            ->setAutoHeight(true)
            ->setBodyStyle('padding: 10px;')
            ->setUrl("adminHandler.php?plugin=xt_ship_and_track&load_section=xt_ship_and_track&pg=saveOrder&orders_id=".$orders_id.'&sec='.$_SESSION['admin_user']['admin_key']) ;

        $eF = new ExtFunctions();
        $combo = $eF->_comboBox('parcel_class', __define('TEXT_PARCEL_CLASS'), 'DropdownData.php?get=hermes_parcel_class&plugin_code=xt_ship_and_track', 500);
        $Panel->addItem($combo);

        $tf = PhpExt_Form_TextField::createTextField('hermes_amount_cash_on_delivery', __define('TEXT_HERMES_AMOUNT_CASH_ON_DELIVERY'));
        $tf->setWidth(150);
        $Panel->addItem($tf);
        $Panel->addItem(PhpExt_Form_Checkbox::createCheckbox('hermes_bulk_good', __define('TEXT_BULK_GOODS')));

        $submitBtn = PhpExt_Button::createTextButton(__define("BUTTON_SAVE"),
            new PhpExt_Handler(PhpExt_Javascript::stm("Ext.getCmp('addParcelForm".$orders_id."').getForm().submit({
												   waitMsg:'".__define('TEXT_HERMES_CREATING_ORDER')."',
												   success: function(form, action) {
												        var r = action.result;
												        //console.log(r);
                                                        if (!r.success)
                                                        {
                                                            Ext.Msg.alert('".__define('TEXT_ALERT')."', r.errorMsg);
                                                        }
                                                        else
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
                                                       contentTabs.getActiveTab().getUpdater().refresh()
                                                   },
                                                    failure: function(form, action)
                                                    {
                                                        var r = action.result;
                                                        //console.log(r);
                                                        Ext.Msg.alert('".__define('TEXT_ALERT')."', r.errorMsg);
                                                    }
												   })"))
        );

        $submitBtn->setType(PhpExt_Button::BUTTON_TYPE_SUBMIT);
        $Panel->addButton($submitBtn);

        return $Panel;
    }

    static function getAddParcelWindowPanel_shipcloud($orders_id)
    {
        global $admin_order_edit;
        if($orders_id)
        {
            $order_data = $admin_order_edit->getOrderData();
            $delivery_addr = $order_data['order_data']['delivery'];
        }
        else {
            $delivery_addr = array();
        }

        xt_shipcloud_settings::readSettings();

        $Panel = new PhpExt_Form_FormPanel('addParcelForm_shipcloud');
        $Panel->setId('addParcelForm_shipcloud'.$orders_id)
            ->setTitle(__define('TEXT_SHIPCLOUD_CREATE_LABEL'))
            ->setAutoWidth(true)
            ->setAutoHeight(true)
            ->setBodyStyle('padding: 10px;')
            ->setUrl("adminHandler.php?plugin=xt_ship_and_track&load_section=xt_ship_and_track&pg=saveOrder_shipcloud&orders_id=".$orders_id.'&sec='.$_SESSION['admin_user']['admin_key']);

        /**
         *   combo paket vorlagen
         */
        $eF = new ExtFunctions();
        $gridtype = $eF->getSetting('gridType');
        $eF->setSetting('gridType', 'EditGrid');
        $combo = $eF->_comboBox('shipcloud_package', __define('TEXT_XT_SHIPCLOUD_PACKAGE_TEMPLATE'), 'DropdownData.php?get=shipcloud_packages&plugin_code=xt_ship_and_track')
            ->setId('shipcloud_package'.$orders_id)
            ->setWidth(200)
            ->setLazyInit(false)
            ->attachListener('select',
                new PhpExt_Listener(PhpExt_Javascript::functionDef(null, "
                 setShipcloudPackageTemplate(this.getValue(), $orders_id, shipcloud_packages);
            ")))
            ->attachListener('change',
                new PhpExt_Listener(PhpExt_Javascript::functionDef(null, "
                //
            ")));
        $combo->getStore()->attachListener('load',
            new PhpExt_Listener(PhpExt_Javascript::functionDef(null, "
                setTimeout(function(){ initShipcloudCombo('shipcloud_package', $orders_id ) }, 500);
            ")));
        $Panel->addItem($combo);


        /**
         *   länge breite ...
         */
        $Panel->addItem(PhpExt_Form_TextField::createTextField('package_length', __define('TEXT_SHIPCLOUD_PACKAGE_LENGTH'))->setAllowBlank(false));
        $Panel->addItem(PhpExt_Form_TextField::createTextField('package_width', __define('TEXT_SHIPCLOUD_PACKAGE_WIDTH'))->setAllowBlank(false));
        $Panel->addItem(PhpExt_Form_TextField::createTextField('package_height', __define('TEXT_SHIPCLOUD_PACKAGE_HEIGHT'))->setAllowBlank(false));
        $Panel->addItem(PhpExt_Form_TextField::createTextField('package_weight', __define('TEXT_SHIPCLOUD_PACKAGE_WEIGHT'))->setAllowBlank(false));


        /**
         *   resize listener
         */
        $resize_listener = new PhpExt_Listener(PhpExt_Javascript::functionDef(null, "
                 syncRemoteWindowSize();
            "));


        /**
         *   combo versender
         */
        $eF = new ExtFunctions();
        $eF->setSetting('gridType', 'EditGrid');
        $combo = $eF->_comboBox('shipcloud_carrier', __define('TEXT_XT_SHIPPER'), 'DropdownData.php?get=shipcloud_carriers&plugin_code=xt_ship_and_track')
            ->setId('shipcloud_carrier'.$orders_id)
            ->setWidth(200)->setAllowBlank(false)
            ->setLazyInit(false)
            ->attachListener('select',
                new PhpExt_Listener(PhpExt_Javascript::functionDef(null, "
                 setShipcloudCarrierSelection(this.getValue(), $orders_id, shipcloud_carriers);
                 syncRemoteWindowSize();
            ")));
        $combo->getStore()->attachListener('load',
            new PhpExt_Listener(PhpExt_Javascript::functionDef(null, "
                setTimeout(function(){ initShipcloudCombo('shipcloud_carrier', $orders_id ); }, 500);
            ")));
        $Panel->addItem($combo);

        /**
         *   combo versender service
         */
        $combo = $eF->_comboBox('shipcloud_service', __define('TEXT_SHIPCLOUD_CARRIER_SERVICE'));
        $combo->setId('shipcloud_service'.$orders_id)
            ->setDisabled(true)->setAllowBlank(false)
            ->setValue( __define('TEXT_SHIPCLOUD_CHOOSE_CARRIER_FIRST'))
            ->setWidth(200)
            ->attachListener('select',
                new PhpExt_Listener(PhpExt_Javascript::functionDef(null, "
                 applyShipcloudServiceSelection(this.getValue(), $orders_id);
            ")));

        $Panel->addItem($combo);

        /**
         *   combo versender paket typ
         */
        $combo = $eF->_comboBox('shipcloud_package_type', __define('TEXT_SHIPCLOUD_CARRIER_PACKAGE_TYPE'))
            ->setId('shipcloud_package_type'.$orders_id)
            ->setDisabled(true)->setAllowBlank(false)
            ->setValue( __define('TEXT_SHIPCLOUD_CHOOSE_CARRIER_FIRST'))
            ->setWidth(200);
        $Panel->addItem($combo);


        /**
         *   sendungsbeschreibung
         */
        $ta = PhpExt_Form_TextArea::createTextArea('description',  __define('TEXT_SHIPCLOUD_DESCRIPTION'))
            ->setCssStyle('width:90%')->setHeight(50);

        if($orders_id && $order_data && is_array($order_data['order_products']))
        {
            $p_names = array();
            foreach($order_data['order_products'] as $k => $v)
            {
                $p_names[] = $v['products_name'];
            }
            $desc = implode(' | ', $p_names);
            $desc = str_replace("'",'', $desc);
            $ta->setValue($desc);
        }
        $Panel->addItem($ta);



        $extFnc = new ExtFunctions();
        $comboCountry_to = $extFnc->_comboBox( 'to_country', __define('TEXT_SHIPCLOUD_COUNTRY'),'DropdownData.php?get=countries')->setWidth(120);
        $comboCountry_to->setValue($delivery_addr['country_code']);
        $comboCountry_from = $extFnc->_comboBox('from_country', __define('TEXT_SHIPCLOUD_COUNTRY'),'DropdownData.php?get=countries')->setWidth(120);
        $comboCountry_from->setValue(XT_SHIPCLOUD_FROM_COUNTRY);
        $comboCountry_retoure = $extFnc->_comboBox('retoure_country', __define('TEXT_SHIPCLOUD_COUNTRY'),'DropdownData.php?get=countries')->setWidth(120);

        if($orders_id)
        {
            /**
             *    additional services DHL
             */
            $field_set_add_service_dhl = new PhpExt_Form_FieldSet();
            $field_set_add_service_dhl->setBorder(true)
                ->setId('group_add_service_dhl' . $orders_id)
                ->setBodyBorder(true)
                ->setCheckboxToggle(true)
                ->setCheckboxName('add_service_dhl')
                ->setTitle(TEXT_SHIPCLOUD_ADDITIONAL_SERVICES . ' DHL')
                ->setAutoHeight(true)
                ->setDefaults(new PhpExt_Config_ConfigObject(array("margin-top" => "100px")))
                ->setCollapsed(true)
                ->attachListener('expand', $resize_listener)
                ->attachListener('collapse', $resize_listener);

            $tf = PhpExt_Form_TextField::createTextField('add_service_dhl_advance_notice_email', __define('TEXT_SHIPCLOUD_ADVANCE_NOTICE_EMAIL'));
            $tf->setValue("");
            $field_set_add_service_dhl->addItem($tf);
            $tf = PhpExt_Form_TextField::createTextField('add_service_dhl_cash_on_delivery', __define('TEXT_SHIPCLOUD_CASH_ON_DELIVERY'));
            $tf->setValue("")->setId('add_service_dhl_cash_on_delivery' . $orders_id);
            $field_set_add_service_dhl->addItem($tf);
            $tf = PhpExt_Form_TextField::createTextField('add_service_dhl_declared_value', __define('TEXT_SHIPCLOUD_DECLARED_VALUE_DHL'));
            $tf->setValue("")->setId('add_service_dhl_declared_value' . $orders_id);
            $field_set_add_service_dhl->addItem($tf);
            $Panel->addItem($field_set_add_service_dhl);

            /**
             *    additional services DPD
             */
            $field_set_add_service_dpd = new PhpExt_Form_FieldSet();
            $field_set_add_service_dpd->setBorder(true)
                ->setId('group_add_service_dpd' . $orders_id)
                ->setBodyBorder(true)
                ->setCheckboxToggle(true)
                ->setCheckboxName('add_service_dpd')
                ->setTitle(TEXT_SHIPCLOUD_ADDITIONAL_SERVICES . ' DPD')
                ->setAutoHeight(true)
                ->setDefaults(new PhpExt_Config_ConfigObject(array("margin-top" => "100px")))
                ->setCollapsed(true)
                ->attachListener('expand', $resize_listener)
                ->attachListener('collapse', $resize_listener);

            $tf = PhpExt_Form_TextField::createTextField('add_service_dpd_advance_notice_email', __define('TEXT_SHIPCLOUD_ADVANCE_NOTICE_EMAIL'));
            $tf->setValue("");
            $field_set_add_service_dpd->addItem($tf);
            $tf = PhpExt_Form_TextField::createTextField('add_service_dpd_advance_notice_sms', __define('TEXT_SHIPCLOUD_ADVANCE_NOTICE_SMS'));
            $tf->setValue("+");
            $field_set_add_service_dpd->addItem($tf);
            $tf = PhpExt_Form_TextField::createTextField('add_service_dpd_drop_authorization_msg', __define('TEXT_SHIPCLOUD_DROP_AUTHORIZATION_MSG'));
            $tf->setValue("");
            $field_set_add_service_dpd->addItem($tf);
            $tf = PhpExt_Form_Checkbox::createCheckbox('add_service_dpd_saturday_delivery', __define('TEXT_SHIPCLOUD_SATURDAY_DELIVERY'));
            $tf->setValue("");
            $field_set_add_service_dpd->addItem($tf);
            $Panel->addItem($field_set_add_service_dpd);
        }


        /**
         *    pickup
         */
        $field_set_pickup = new PhpExt_Form_FieldSet();
        $field_set_pickup ->setBorder(true)
            ->setId('shipcloud_fieldset_pickup_'.$orders_id)
            ->setBodyBorder(true)
            ->setCheckboxToggle(true)
            ->setCheckboxName('pickup_fieldset')
            ->setTitle(TEXT_SHIPCLOUD_PICKUP)
            ->setAutoHeight(true)
            ->setDefaults(new PhpExt_Config_ConfigObject(array("margin-top"=>"80px")))
            ->setCollapsed(true);

        $columnLayout = new PhpExt_Layout_ColumnLayout();
        $columnPanel = new PhpExt_Panel();
        $columnPanel->setBorder(false)->setLayout($columnLayout);

        // left column
        $formLayout = new PhpExt_Layout_FormLayout();
        $firstColumn = new PhpExt_Panel();
        $firstColumn->setId('panel_pickup_earliest_'.$orders_id)
            ->setCssStyle('padding:5px;')->setBorder(false)->setLayout($formLayout);

        $d = new DateTime();
        $d->add(new DateInterval('P1D'));
        $tf = PhpExt_Form_DateField::createDateField('pickup_earliest_date',__define('TEXT_SHIPCLOUD_PICKUP_EARLIEST_DATE'));
        $tf->setFormat('d.m.Y')->setValue($d->format('d.m.Y'))->setWidth(85);
        $firstColumn->addItem($tf);
        $tf = PhpExt_Form_TimeField::createTimeField('pickup_earliest_time',__define('TEXT_SHIPCLOUD_PICKUP_EARLIEST_TIME'));
        $tf->setFormat('H:i')->setValue('15:00')->setWidth(85);
        $firstColumn->addItem($tf);
        $tf = PhpExt_Form_Checkbox::createCheckbox('pickup', __define('TEXT_PICKUP_ASK'));
        $tf->setValue("");
        $firstColumn->addItem($tf);
        $columnPanel->addItem($firstColumn, new PhpExt_Layout_ColumnLayoutData(0.5));

        // right column
        $secondColumn = new PhpExt_Panel();
        $secondColumn->setId('panel_pickup_latest_'.$orders_id)
            ->setCssStyle('padding:5px;')->setBorder(false)->setLayout($formLayout);

        $d = new DateTime();
        $d->add(new DateInterval('P1D'));
        $tf = PhpExt_Form_DateField::createDateField('pickup_latest_date',__define('TEXT_SHIPCLOUD_PICKUP_LATEST_DATE'));
        $tf->setFormat('d.m.Y')->setValue($d->format('d.m.Y'))->setWidth(85);
        $secondColumn->addItem($tf);
        $tf = PhpExt_Form_TimeField::createTimeField('pickup_latest_time',__define('TEXT_SHIPCLOUD_PICKUP_LATEST_TIME'));
        $tf->setFormat('H:i')->setValue('16:00')->setWidth(85);
        $secondColumn->addItem($tf);

        $columnPanel->addItem($secondColumn, new PhpExt_Layout_ColumnLayoutData(0.5));

        $field_set_pickup
            ->setCollapsed(true)->attachListener('expand', $resize_listener)
            ->setCollapsed(true)->attachListener('collapse',$resize_listener);

        $field_set_pickup->addItem($columnPanel);

        $Panel->addItem($field_set_pickup);



        $columnLayout = new PhpExt_Layout_ColumnLayout();
        $columnPanel = new PhpExt_Panel();
        $columnPanel->setBorder(false)->setLayout($columnLayout);

        $firstColumn = false;
        /**
         *   Empfänger
         */
        if($orders_id)
        {
            $firstColumn = new PhpExt_Panel();
            $firstColumn->setCssStyle('padding:5px;')->setBorder(false);

            $street_from = xt_ship_and_track::splitAddress($delivery_addr['street_address'], xt_ship_and_track::getSplit());

            $field_set_to = new PhpExt_Form_FieldSet();
            $field_set_to ->setBorder(true)
                ->setId('shipcloud_fieldset_to_'.$orders_id)
                ->setBodyBorder(true)
                ->setCheckboxToggle(true)
                ->setCheckboxName('override_default_to')
                ->setTitle(TEXT_SHIPCLOUD_EDIT_TO)
                ->setAutoHeight(true)
                ->setDefaults(new PhpExt_Config_ConfigObject(array("margin-top"=>"800px")))
                ->setCollapsed(true);

            $Panel->addItem(PhpExt_Form_Hidden::createHidden('to_email', $order_data['order_customer']['customers_email_address']));
            $tf = PhpExt_Form_TextField::createTextField('to_first_name', __define('TEXT_SHIPCLOUD_FIRST_NAME'));
            $tf->setValue($delivery_addr['firstname']);
            $field_set_to->addItem($tf);
            $tf = PhpExt_Form_TextField::createTextField('to_last_name', __define('TEXT_SHIPCLOUD_LAST_NAME'));
            $tf->setValue($delivery_addr['lastname']);
            $field_set_to->addItem($tf);
            $tf = PhpExt_Form_TextField::createTextField('to_company', __define('TEXT_SHIPCLOUD_COMPANY'));
            $tf->setValue($delivery_addr['company']);
            $field_set_to->addItem($tf);
            $tf = PhpExt_Form_TextField::createTextField('to_care_of', __define('TEXT_SHIPCLOUD_CARE_OF'));
            $tf->setValue($delivery_addr['address_addition']);
            $field_set_to->addItem($tf);
            $tf = PhpExt_Form_TextField::createTextField('to_street', __define('TEXT_SHIPCLOUD_STREET'));
            $tf->setValue($street_from[0]);
            $field_set_to->addItem($tf);
            $tf = PhpExt_Form_TextField::createTextField('to_street_no', __define('TEXT_SHIPCLOUD_STREET_NO'));
            $tf->setValue($street_from[1]);
            $field_set_to->addItem($tf);
            $tf = PhpExt_Form_TextField::createTextField('to_city', __define('TEXT_SHIPCLOUD_CITY'));
            $tf->setValue($delivery_addr['city']);
            $field_set_to->addItem($tf);
            $tf = PhpExt_Form_TextField::createTextField('to_zip_code', __define('TEXT_SHIPCLOUD_ZIP_CODE'));
            $tf->setValue($delivery_addr['postcode']);
            $field_set_to->addItem($tf);
            $tf =PhpExt_Form_TextField::createTextField('to_state', __define('TEXT_SHIPCLOUD_STATE'));
            $tf->setValue($delivery_addr['federal_state_code'] ? $delivery_addr['federal_state_code'] : '');
            $field_set_to->addItem($tf);
            /*
            $tf = PhpExt_Form_TextField::createTextField('to_country', __define('TEXT_SHIPCLOUD_COUNTRY'));
            $tf->setValue($delivery_addr['country_code']);
            */
            $field_set_to->addItem($comboCountry_to);
            $tf = PhpExt_Form_TextField::createTextField('to_phone', __define('TEXT_SHIPCLOUD_PHONE'));
            $tf->setValue($delivery_addr['mobile_phone'] ? $delivery_addr['mobile_phone'] : $delivery_addr['phone']);
            $field_set_to->addItem($tf)
                ->setCollapsed(true)->attachListener('expand', $resize_listener)
                ->setCollapsed(true)->attachListener('collapse',$resize_listener);
            $firstColumn->addItem($field_set_to);
        }

        /**
         *    Absender
         */

        $field_set_from = new PhpExt_Form_FieldSet();
        $field_set_from ->setBorder(true)
            ->setId('shipcloud_fieldset_from_'.$orders_id)
            ->setBodyBorder(true)
            ->setCheckboxToggle(true)
            ->setCheckboxName('override_default_from')
            ->setTitle(TEXT_SHIPCLOUD_OVERRIDE_FROM)
            ->setAutoHeight(true)
            ->setDefaults(new PhpExt_Config_ConfigObject(array("margin-top"=>"80px")))
            ->setCollapsed(true)->setHideMode(PhpExt_Component::HIDE_MODE_DISPLAY)->setHideParent(true);

        $tf = PhpExt_Form_TextField::createTextField('from_first_name', __define('TEXT_SHIPCLOUD_FIRST_NAME'));
        $tf->setValue(XT_SHIPCLOUD_FROM_FIRSTNAME);
        $field_set_from->addItem($tf);
        $tf = PhpExt_Form_TextField::createTextField('from_last_name', __define('TEXT_SHIPCLOUD_LAST_NAME'));
        $tf->setValue(XT_SHIPCLOUD_FROM_LASTNAME);
        $field_set_from->addItem($tf);
        $tf = PhpExt_Form_TextField::createTextField('from_company', __define('TEXT_SHIPCLOUD_COMPANY'));
        $tf->setValue(XT_SHIPCLOUD_FROM_COMPANY);
        $field_set_from->addItem($tf);
        $tf = PhpExt_Form_TextField::createTextField('from_care_of', __define('TEXT_SHIPCLOUD_CARE_OF'));
        $tf->setValue(XT_SHIPCLOUD_FROM_CAREOF);
        $field_set_from->addItem($tf);
        $tf = PhpExt_Form_TextField::createTextField('from_street', __define('TEXT_SHIPCLOUD_STREET'));
        $tf->setValue(XT_SHIPCLOUD_FROM_STREET);
        $field_set_from->addItem($tf);
        $tf = PhpExt_Form_TextField::createTextField('from_street_no', __define('TEXT_SHIPCLOUD_STREET_NO'));
        $tf->setValue(XT_SHIPCLOUD_FROM_HOUSENO);
        $field_set_from->addItem($tf);
        $tf = PhpExt_Form_TextField::createTextField('from_city', __define('TEXT_SHIPCLOUD_CITY'));
        $tf->setValue(XT_SHIPCLOUD_FROM_CITY);
        $field_set_from->addItem($tf);
        $tf = PhpExt_Form_TextField::createTextField('from_zip_code', __define('TEXT_SHIPCLOUD_ZIP_CODE'));
        $tf->setValue(XT_SHIPCLOUD_FROM_ZIP);
        $field_set_from->addItem($tf);
        $tf =PhpExt_Form_TextField::createTextField('from_state', __define('TEXT_SHIPCLOUD_STATE'));
        $tf->setValue(XT_SHIPCLOUD_FROM_STATE);
        $field_set_from->addItem($tf);
        /*
        $field_set_from->addItem($tf);
        $tf = PhpExt_Form_TextField::createTextField('from_country', __define('TEXT_SHIPCLOUD_COUNTRY'));
        $tf->setValue("");
        */
        $comboCountry_from->setValue(XT_SHIPCLOUD_FROM_COUNTRY);
        $field_set_from->addItem($comboCountry_from);
        $tf = PhpExt_Form_TextField::createTextField('from_phone', __define('TEXT_SHIPCLOUD_PHONE'));
        $tf->setValue(XT_SHIPCLOUD_FROM_PHONE);
        $field_set_from->addItem($tf);

        $field_set_from->setCollapsed(true)->attachListener('expand', $resize_listener)
            ->setCollapsed(true)->attachListener('collapse',$resize_listener);

        $secondColumn = new PhpExt_Panel();
        $secondColumn->setCssStyle('padding:5px;')->setBorder(false)->setId('shipcloud_panel_from_'.$orders_id)->setHideMode(PhpExt_Component::HIDE_MODE_DISPLAY);
        $secondColumn->addItem($field_set_from);


        /*
         *  retore addresse
         *
         */
        $thirdColumn = false;
        if(defined('XT_SHIPCLOUD_DIFFERENT_RETOURE') && XT_SHIPCLOUD_DIFFERENT_RETOURE==="1")
        {
            $field_set_retoure = new PhpExt_Form_FieldSet();
            $field_set_retoure->setBorder(true)
                ->setId('shipcloud_fieldset_retoure_' . $orders_id)
                ->setBodyBorder(true)
                ->setCheckboxToggle(true)
                ->setCheckboxName('override_default_from')
                ->setTitle(TEXT_SHIPCLOUD_RETOURE_TO)
                ->setAutoHeight(true)
                ->setDefaults(new PhpExt_Config_ConfigObject(array("margin-top" => "100px")))
                ->setCollapsed(true);

            $tf = PhpExt_Form_TextField::createTextField('retoure_first_name', __define('TEXT_SHIPCLOUD_FIRST_NAME'));
            $tf->setValue(XT_SHIPCLOUD_RETOURE_FIRSTNAME);
            $field_set_retoure->addItem($tf);
            $tf = PhpExt_Form_TextField::createTextField('retoure_last_name', __define('TEXT_SHIPCLOUD_LAST_NAME'));
            $tf->setValue(XT_SHIPCLOUD_RETOURE_LASTNAME);
            $field_set_retoure->addItem($tf);
            $tf = PhpExt_Form_TextField::createTextField('retoure_company', __define('TEXT_SHIPCLOUD_COMPANY'));
            $tf->setValue(XT_SHIPCLOUD_RETOURE_COMPANY);
            $field_set_retoure->addItem($tf);
            $tf = PhpExt_Form_TextField::createTextField('retoure_care_of', __define('TEXT_SHIPCLOUD_CARE_OF'));
            $tf->setValue(XT_SHIPCLOUD_RETOURE_CAREOF);
            $field_set_retoure->addItem($tf);
            $tf = PhpExt_Form_TextField::createTextField('retoure_street', __define('TEXT_SHIPCLOUD_STREET'));
            $tf->setValue(XT_SHIPCLOUD_RETOURE_STREET);
            $field_set_retoure->addItem($tf);
            $tf = PhpExt_Form_TextField::createTextField('retoure_street_no', __define('TEXT_SHIPCLOUD_STREET_NO'));
            $tf->setValue(XT_SHIPCLOUD_RETOURE_HOUSENO);
            $field_set_retoure->addItem($tf);
            $tf = PhpExt_Form_TextField::createTextField('retoure_city', __define('TEXT_SHIPCLOUD_CITY'));
            $tf->setValue(XT_SHIPCLOUD_RETOURE_CITY);
            $field_set_retoure->addItem($tf);
            $tf = PhpExt_Form_TextField::createTextField('retoure_zip_code', __define('TEXT_SHIPCLOUD_ZIP_CODE'));
            $tf->setValue(XT_SHIPCLOUD_RETOURE_ZIP);
            $field_set_retoure->addItem($tf);
            $tf = PhpExt_Form_TextField::createTextField('retoure_state', __define('TEXT_SHIPCLOUD_STATE'));
            $tf->setValue(XT_SHIPCLOUD_RETOURE_STATE);
            $field_set_retoure->addItem($tf);
            /*
            $field_set_from->addItem($tf);
            $tf = PhpExt_Form_TextField::createTextField('from_country', __define('TEXT_SHIPCLOUD_COUNTRY'));
            $tf->setValue("");
            */
            $comboCountry_retoure->setValue(XT_SHIPCLOUD_RETOURE_COUNTRY);
            $field_set_retoure->addItem($comboCountry_retoure);
            $tf = PhpExt_Form_TextField::createTextField('retoure_phone', __define('TEXT_SHIPCLOUD_PHONE'));
            $tf->setValue(XT_SHIPCLOUD_RETOURE_PHONE);
            $field_set_retoure->addItem($tf);

            $field_set_retoure->setCollapsed(true)->attachListener('expand', $resize_listener)
                ->setCollapsed(true)->attachListener('collapse', $resize_listener);

            $thirdColumn = new PhpExt_Panel();
            $thirdColumn->setCssStyle('padding:5px;')->setBorder(false);
            $thirdColumn->addItem($field_set_retoure);
        }

        if(is_object($firstColumn))
        {
            $columnPanel->addItem($firstColumn, new PhpExt_Layout_ColumnLayoutData(0.5));
            $columnPanel->addItem($secondColumn, new PhpExt_Layout_ColumnLayoutData(0.5));
            if(is_object($thirdColumn))
            {
                $columnPanel->addItem($thirdColumn, new PhpExt_Layout_ColumnLayoutData(0.5));
            }
        }
        else {
            $columnPanel->addItem($secondColumn, new PhpExt_Layout_ColumnLayoutData(1));
            if(is_object($thirdColumn))
            {
                $columnPanel->addItem($thirdColumn, new PhpExt_Layout_ColumnLayoutData(1));
            }
        }




        $Panel->addItem($columnPanel);

        /**
         *    hidden fields
         */
        $Panel->addItem(PhpExt_Form_Hidden::createHidden('quote', 0));
        $Panel->addItem(PhpExt_Form_Hidden::createHidden('prepare_label', 0));
        $Panel->addItem(PhpExt_Form_Hidden::createHidden('shop_id', $admin_order_edit->order_data['order_data']['shop_id']));
        if($orders_id)
        {
            $hidden = PhpExt_Form_Hidden::createHidden('order_total_price', number_Format($admin_order_edit->order_data['order_total']['total']['plain'], 2, ',', ''));
            $hidden->setId('order_total_price'.$orders_id);
            $Panel->addItem($hidden);
        }
        else {
            $Panel->addItem(PhpExt_Form_Hidden::createHidden('order_ids', 0));
        }

        /**
         *    Buttons
         */
        if($orders_id || true)
        {
            $quoteBtn = PhpExt_Button::createTextButton(__define("BUTTON_SHIPCLOUD_QUOTE"),
                new PhpExt_Handler(PhpExt_Javascript::stm("
            shipcloud_sendForm(" . $orders_id . ", 'quote');
            "))
            );
            $quoteBtn->setType(PhpExt_Button::BUTTON_TYPE_BUTTON);
            $quoteBtn->setId("BUTTON_SHIPCLOUD_QUOTE" . $orders_id);
            $Panel->addButton($quoteBtn);
        }

        /*
        if($orders_id)
        {
            $aveBtn = PhpExt_Button::createTextButton(__define("BUTTON_SHIPCLOUD_SAVE"),
                new PhpExt_Handler(PhpExt_Javascript::stm("
            shipcloud_sendForm(" . $orders_id . ", 'prepare_label');
            "))
            );
            $aveBtn->setType(PhpExt_Button::BUTTON_TYPE_SUBMIT);
            $aveBtn->setId("BUTTON_SHIPCLOUD_SAVE" . $orders_id);
            if ($orders_id)
            {
                $Panel->addButton($aveBtn);
            }
        }
        */

        $submitBtn = PhpExt_Button::createTextButton(__define("TEXT_SHIPCLOUD_CREATE_LABEL"),
            new PhpExt_Handler(PhpExt_Javascript::stm("
            shipcloud_sendForm(".$orders_id.");
            "))
        );

        $submitBtn->setType(PhpExt_Button::BUTTON_TYPE_SUBMIT);
        $submitBtn->setId("TEXT_SHIPCLOUD_CREATE_LABEL".$orders_id);
        $Panel->addButton($submitBtn);

        return $Panel;
    }


    /**
     *    ExtJs adds
     */



}