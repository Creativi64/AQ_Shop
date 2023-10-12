function enablePayments_disabled() // läuft minimized im updater
{

    var conn = new Ext.data.Connection();
    conn.request({
        url: 'adminHandler.php',
        method:'GET',
        params: {
            plugin:             'xt_paypal_checkout',
            load_section:   'xt_paypal_checkout',
            pg:   'enablePayments',
            seckey:         paypal_checkout_constant.SYSTEM_SECURITY_KEY,
            //multiFlag_setStatus: 'true',
            //m_ids: '{$product_id}'
        },
        success: function(responseObject)
        {
            if(typeof(Ext.getCmp('plugin_installedgridForm')) != 'undefined') Ext.getCmp('plugin_installedgridForm').getStore().reload();
            var msg = JSON.parse(responseObject.responseText).statusText;
            Ext.MessageBox.alert('OK',msg);
        },
        failure: function(responseObject)
        {
            var title = 'Error';
            var msg = 'Fehler beim aktivieren des Plugins/Zahlungsweisen.';
            Ext.MessageBox.alert(title,msg);
        }
    });
}

function ppcp_updatePpOrderData(order_id){
    var mask = null;
    try {
        var mask = new Ext.LoadMask(Ext.getBody(), {msg: 'Moment...'});
        mask.show();
        var conn = new Ext.data.Connection();
        conn.request({
            url: 'adminHandler.php?plugin=xt_paypal_checkout&load_section=xt_paypal_checkout&pg=reloadPaypalOrder',
            method: 'POST',
            params: {'order_id': order_id, 'seckey': paypal_checkout_constant.SYSTEM_SECURITY_KEY},
            success: function (responseObject) {
                mask.hide();
                console.log(responseObject);
                var s = JSON.parse(responseObject.responseText);
                if(typeof s.errorMsg != 'undefined')
                {
                    Ext.MessageBox.alert('Message', paypal_checkout_constant.TEXT_ERROR + '<textarea cols=\'67\' rows=\'6\' style=\' overflow-y:scroll;padding:5px\' >' + s.errorMsg + '</textarea>');
                }
                else if (s && s.id) {
                    Ext.MessageBox.alert('Status: ' + s.status, paypal_checkout_constant.TEXT_ORDER_DATA + '<textarea cols=\'67\' rows=\'50\' style=\' overflow-y:scroll;padding:5px\' >' + JSON.stringify(s, undefined, 2) + '</textarea>');
                    // das machen wir seit 2.1.2 nicht mehr, da wir kein auto status-mapping machen
                    // es braucht im dialogfeld nochen eine button 'statusmapping ausführen'
                    //contentTabs.getActiveTab().getUpdater().refresh();
                } else Ext.MessageBox.alert('Message', paypal_checkout_constant.TEXT_ERROR);
            },
            failure: function (responseObject) {
                mask.hide();
                var title = 'Error';
                var msg = 'Fehler bei Aktualisierung.';
                Ext.MessageBox.alert(title, msg);
            }
        });
    }
    catch(e)
    {
        console.log(e)
    }
}

function ppcp_openRefundWindow(orders_id)
{
    var new_window = new Ext.Window({
        listeners: {
            'render': {
                fn: function () {
                    this.setPosition(this.x, 20);
                }
            }
        },
        items: {
            items: [{
                labelSeparator: '', name: 'refund_amount', fieldLabel: 'Betrag', validator: function () {
                    return ppcp_CheckRefundAmount(orders_id);
                    ;
                }, value: '', xtype: 'textfield'
            }, {
                labelSeparator: '',
                name: 'refund_description',
                fieldLabel: 'Kommentare(max 255)',
                validator: function () {
                    return ppcp_CheckRefundDescription(orders_id);
                    ;
                },
                value: '',
                height: 50,
                width: 240,
                xtype: 'textarea'
            }],
            id: 'doRefund_ppcp'+orders_id,
            title: paypal_checkout_constant.TEXT_DO_REFUND,
            autoWidth: true,
            autoHeight: true,
            bodyStyle: 'padding: 10px;',
            url: 'adminHandler.php?plugin=xt_paypal_checkout&load_section=xt_paypal_checkout&pg=refund&orders_id='+orders_id+'&seckey='+paypal_checkout_constant.SYSTEM_SECURITY_KEY,
            xtype: 'form'
        },
        buttons: [{
            text: 'Schliessen', handler: function () {
                if (new_window) {
                    new_window.destroy()
                } else {
                    this.destroy()
                }
                ;
            }, icon: 'images/icons/cancel.png', iconCls: 'x-btn-text', xtype: 'button'
        }, {
            listeners: {
                'render': {
                    fn: function () {

                        $('#' + this.id).find('button').addClass('bold');

                    }
                }
            }, text: paypal_checkout_constant.TEXT_DO_REFUND_LABEL, handler: function () {

                ppcp_refund(orders_id, false);
                ;
            }, id: 'TEXT_PPCP_REFUND_LABEL'+orders_id, style: 'font-weight: bold', cls: 'bold', type: 'submit', xtype: 'button'
        }],
        resizable: true,
        plain: true,
        bodyStyle: '',
        buttonAlign: 'center',
        title: 'Rückzahlung/Refund (Shop-Bestellung '+orders_id+')',
        width: 400,
        height: 325,
        autoHeight: true,
        id: 'doRefund_ppcp_wnd'+orders_id,
        modal: true,
        layout: 'fit'
    });
    new_window.show();
}

function ppcp_refund(orderId)
{
    var form = getPpcpForm('doRefund_ppcp', orderId);

    if(typeof form != 'undefined')
    {
        if(form.isValid() && ppcp_CheckRefundAmount(orderId))
        {
            form.submit({
                waitMsg: paypal_checkout_constant.TEXT_WAIT,
                success: function(form, action)
                {
                    try {
                        var r = action.result;
                        if (typeof r.success == 'undefined' || !r.success)
                        {
                            var msg = typeof r.msg != 'undefined' ? r.msg : 'Unknown error. Check server log';
                            Ext.Msg.alert(paypal_checkout_constant.TEXT_ERROR, msg);
                        }
                        else {
                            if(typeof r.refresh != 'undefined' && r.refresh == true)
                            {
                                contentTabs.getActiveTab().getUpdater().refresh();
                            }
                            ppcp_closeWindow('doRefund_ppcp_wnd',orderId);

                            if(typeof r.refresh != 'undefined' && r.refresh == true)
                            {
                                contentTabs.getActiveTab().getUpdater().refresh();
                            }

                            var msg = typeof r.msg != 'undefined' ? r.msg : '';
                            Ext.Msg.alert(paypal_checkout_constant.TEXT_SUCCESS, msg);
                        }
                    }
                    catch(e)
                    {
                        console.error(e);
                    }
                },
                failure: function(form, action)
                {
                    var r = action.result;
                    var msg = typeof r.msg != 'undefined' ? r.msg : 'Unknown error. Check server log';
                    Ext.Msg.alert(paypal_checkout_constant.TEXT_ERROR, msg);
                }
            });
        }
    }
}

function getPpcpForm(name, orderId)
{
    var comp = Ext.getCmp(name+orderId);
    if(typeof comp != 'undefined')
    {
        return comp.getForm();
    }
    return null;
}

let in_ppcp_CheckRefundAmount = false;
function ppcp_CheckRefundAmount(orderId)
{
    try {
        if(!in_ppcp_CheckRefundAmount) {
            let form = getPpcpForm('doRefund_ppcp', orderId);
            if (typeof form != 'undefined')
            {
                in_ppcp_CheckRefundAmount = true;
                let val = form.findField('refund_amount').getValue().replace(',', '.');
                form.findField('refund_amount').setValue(val);
                in_ppcp_CheckRefundAmount = false;

                let isNum = /^\d+\.?\d{0,2}$/.test(val);
                if (!isNum) return false;
                let amount = parseFloat(val);
                let maxAmount = 10000000; // TODO parseFloat(form.findField('max_refund_amount').getValue().replace(',','.'));

                if (isNaN(amount) || isNaN(maxAmount) || amount <= 0 || amount > maxAmount) {
                    return false;
                }
                return true;
            }
            return false;
        }
    }
    catch(e) { in_ppcp_CheckRefundAmount = false; console.log(e) }
}

function ppcp_CheckRefundDescription(orderId)
{
    var form = getPpcpForm('doRefund_ppcp', orderId);
    if(form && typeof form != 'undefined')
    {
        var description = form.findField('refund_description').getValue();
        if(description.length > 255)
        {
            return false;
        }
        return true;
    }
    return false;
}

function ppcp_closeWindow(name, orderId)
{
    if(typeof orderId == 'undefined')
        orderId = '';
    var wnd = Ext.getCmp(name+orderId);
    if(typeof wnd != 'undefined')
    {
        wnd.close();
    }
}

