

window.addEventListener('tracking_created', function(e){
    showCapturesForTracking(e.detail.xt_orders_id, e.detail.tracking_ids);
});

function syncRemoteWindowSizeKlarna(compId)
{
    var wnd = Ext.getCmp(compId);
    if(wnd)
    {
        console.log(wnd.id, wnd);
        wnd.syncSize();

        //wnd.container.setHeight("400px");

    }
    return wnd;
}

function klarnaDoCaptureMultiple()
{
    var ids = klarnaGetSelectedOrders();

    Ext.Msg.show({
        title: klarna_msg.CAPTURE_ALL_ASK,
        buttons: Ext.Msg.YESNO,
        fn: function(btn, text){
            if (btn == 'yes'){

                var total = ids.length;
                klarnaDoCaptureMultipleStep(ids, 0, total, []);
            }
        },
        icon: Ext.MessageBox.QUESTION
    });
}

function klarnaDoCaptureMultipleStep(xt_orders_ids, idx, total, msgs)
{
    if(xt_orders_ids.length > 0) {
        var mask = new Ext.LoadMask(Ext.getBody(), {msg: klarna_msg.CONNECTING + ' ' + (idx + 1) + '/' + total});
        mask.show();

        var xt_orders_id = xt_orders_ids[0];
        var conn = new Ext.data.Connection();
        conn.request({
            url: 'adminHandler.php?plugin=xt_klarna_kp&load_section=xt_klarna_kp&pg=capture&sec=' + csrf_key,
            method: 'POST',
            params: {orders_id: xt_orders_id},
            error: function (responseObject) {
                mask.hide();

                xt_orders_ids.shift();

                var jObj = JSON.parse(responseObject.responseText);
                Ext.Msg.alert(klarna_msg.TEXT_ALERT, klarna_msg.TEXT_ERROR + ': ' + jObj.msg, function(btn, text){
                    klarnaDoCaptureMultipleStep(xt_orders_ids, (idx+1), total, msgs);
                });
            },
            waitMsg: klarna_msg.CONNECTING,
            success: function (responseObject) {
                mask.hide();

                xt_orders_ids.shift();

                var jObj = JSON.parse(responseObject.responseText);
                if (jObj.success != true)
                {
                    msgs.push(xt_orders_id +': ' + jObj.msg + '</br>');
                }

                if (idx + 1 == total) {

                    var msg = klarna_msg.TEXT_SUCCESS;
                    if(msgs.length > 0)
                    {
                        msg = '';
                        msgs.forEach(function(item, idx)
                        {
                            msg += item;
                        });
                    }

                    Ext.Msg.alert(klarna_msg.TEXT_ALERT, msg, function(btn, text){
                        resetSelectedOrders();
                    });

                }
                else {
                    klarnaDoCaptureMultipleStep(xt_orders_ids, (idx+1), total, msgs)
                }
            }
        });
    }
}


function klarnaGetSelectedOrders($asString)
{
    var records = Ext.getCmp('ordergridForm').getStore().getModifiedRecords();
    var record_ids = [];
    for (var i = 0; i < records.length; i++) {
        if (records[i].get('selectedItem'))
            record_ids.push( records[i].get('orders_id'));
    }
    console.log(record_ids);

    if(typeof $asString != 'undefined') return record_ids.join('|');
    return record_ids;
}

function resetSelectedOrders()
{
    Ext.getCmp('ordergridForm').getStore().modified = [];
    Ext.getCmp('ordergridForm').getStore().reload(); //modified = [];
}

function klarnaApplyCaptureAmount(amount, orderId)
{
    var form = getKlarnaForm('doCapture_kp', orderId);
    if(typeof form != 'undefined')
    {
        form.findField('capture_amount').setValue(amount);
    }
}

function klarnaCheckCaptureAmount(orderId)
{
    var form = getKlarnaForm('doCapture_kp', orderId);
    if(typeof form != 'undefined')
    {
        var amount = parseFloat(form.findField('capture_amount').getValue().replace(',','.'));
        var maxAmount = parseFloat(form.findField('max_capture_amount').getValue().replace(',','.'));
        if(isNaN(amount) || isNaN(maxAmount) || amount<=0 || amount > maxAmount)
        {
            return false;
        }
        return true;
    }
    return false;
}

function klarnaApplyRefundAmount(amount, orderId)
{
    var form = getKlarnaForm('doRefund_kp', orderId);
    if(typeof form != 'undefined')
    {
        form.findField('refund_amount').setValue(amount);
    }
}

function klarnaCheckRefundAmount(orderId)
{
    var form = getKlarnaForm('doRefund_kp', orderId);
    if(typeof form != 'undefined')
    {
        var amount = parseFloat(form.findField('refund_amount').getValue().replace(',','.'));
        var maxAmount = parseFloat(form.findField('max_refund_amount').getValue().replace(',','.'));
        if(isNaN(amount) || isNaN(maxAmount) || amount<=0 || amount > maxAmount)
        {
            return false;
        }
        return true;
    }
    return false;
}

function klarnaCheckRefundDescription(orderId)
{
    var form = getKlarnaForm('doRefund_kp', orderId);
    if(form && typeof form != 'undefined')
    {
        var description = form.findField('refund_description').getValue();
        if(description.length > 250)
        {
            return false;
        }
        return true;
    }
    return false;
}

function klarnaCheckCaptureDescription(orderId)
{
    var form = getKlarnaForm('doCapture_kp', orderId);
    if(form && typeof form != 'undefined')
    {
        var description = form.findField('capture_description').getValue();
        if(description.length > 250)
        {
            return false;
        }
        return true;
    }
    return false;
}

function klarnaLinkCapture(orderId, mask)
{
    var form = getKlarnaForm('doLinkCapture_kp', orderId);

    if(typeof form != 'undefined')
    {
        if(form.isValid())
        {

            form.submit({
                waitMsg: klarna_msg.CONNECTING,
                success: function(form, action)
                {
                    try {
                        //mask.hide();
                        var r = action.result;
                        if (typeof r.success == 'undefined' || !r.success)
                        {
                            var msg = typeof r.msg != 'undefined' ? r.msg : 'Unknown error. Check server log';
                            Ext.Msg.alert(klarna_msg.TEXT_ALERT, msg);
                        }
                        else {
                            if(typeof r.refresh != 'undefined' && r.refresh == true)
                            {
                                //contentTabs.getActiveTab().getUpdater().refresh();
                            }
                            Ext.Msg.alert(klarna_msg.TEXT_ALERT, 'OK');
                            closeWindow('doLinkCapture_kp_wndRemoteWindow');
                            contentTabs.getActiveTab().getUpdater().refresh();contentTabs.getActiveTab().getUpdater().refresh();
                        }
                    }
                    catch(e)
                    {
                        console.error(e);
                    }
                },
                failure: function(form, action)
                {
                    //mask.hide();
                    var r = action.result;
                    var msg = typeof r.msg != 'undefined' ? r.msg : 'Unknown error. Check server log';
                    Ext.Msg.alert(klarna_msg.TEXT_ALERT, msg);
                }
            });
        }
    }
}

function klarnaCapture(orderId, release)
{
    if (typeof release == 'undefined') release = false;

    var form = getKlarnaForm('doCapture_kp', orderId);

    if(orderId==0 && false)
    {
        var records = Ext.getCmp('ordergridForm').getStore().getModifiedRecords();
        var record_ids = [];
        for (var i = 0; i < records.length; i++) {
            if (records[i].get('selectedItem'))
                record_ids.push( records[i].get('orders_id'));
        }
        if (record_ids.length == 0) return;
        form.findField('order_ids').setValue(record_ids);
    }

    if(typeof form != 'undefined')
    {
        if(form.isValid() && klarnaCheckCaptureAmount(orderId))
        {
            if(release == true) form.findField('release').setValue('1');

            form.submit({
                waitMsg: klarna_msg.CAPTURE_WAIT,
                success: function(form, action)
                {
                    try {
                        form.findField('release').setValue('0');

                        var r = action.result;
                        if (typeof r.success == 'undefined' || !r.success)
                        {
                            var msg = typeof r.msg != 'undefined' ? r.msg : 'Unknown error. Check server log';
                            Ext.Msg.alert(klarna_msg.TEXT_ALERT, msg);
                        }
                        else {
                            if(typeof r.refresh != 'undefined' && r.refresh == true)
                            {
                                contentTabs.getActiveTab().getUpdater().refresh();
                            }
                            closeWindow('doCapture_kp_wnd',orderId);
                            Ext.Msg.alert(klarna_msg.TEXT_ALERT, 'OK');
                        }
                    }
                    catch(e)
                    {
                        console.error(e);
                    }
                },
                failure: function(form, action)
                {
                    form.findField('release').setValue('0');
                    var r = action.result;
                    var msg = typeof r.msg != 'undefined' ? r.msg : 'Unknown error. Check server log';
                    Ext.Msg.alert(klarna_msg.TEXT_ALERT, msg);
                }
            });
        }
    }
}

function klarnaRefund(orderId)
{
    var form = getKlarnaForm('doRefund_kp', orderId);

    if(typeof form != 'undefined')
    {
        if(form.isValid() && klarnaCheckRefundAmount(orderId))
        {
            form.submit({
                waitMsg: klarna_msg.REFUND_WAIT,
                success: function(form, action)
                {
                    try {
                        var r = action.result;
                        if (typeof r.success == 'undefined' || !r.success)
                        {
                            var msg = typeof r.msg != 'undefined' ? r.msg : 'Unknown error. Check server log';
                            Ext.Msg.alert(klarna_msg.TEXT_ALERT, msg);
                        }
                        else {
                            if(typeof r.refresh != 'undefined' && r.refresh == true)
                            {
                                contentTabs.getActiveTab().getUpdater().refresh();
                            }
                            closeWindow('doRefund_kp_wnd',orderId);
                            Ext.Msg.alert(klarna_msg.TEXT_ALERT, 'OK');
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
                    Ext.Msg.alert(klarna_msg.TEXT_ALERT, msg);
                }
            });
        }
    }
}

function klarnaCancel(orderId)
{
    var form = getKlarnaForm('doCancel_kp', orderId);

    if(orderId==0)
    {
        var records = Ext.getCmp('ordergridForm').getStore().getModifiedRecords();
        var record_ids = [];
        for (var i = 0; i < records.length; i++) {
            if (records[i].get('selectedItem'))
                record_ids.push( records[i].get('orders_id'));
        }
        if (record_ids.length == 0) return;
        form.findField('order_ids').setValue(record_ids);
    }

    if(typeof form != 'undefined')
    {
        if(form.isValid())
        {
            form.submit({
                waitMsg: klarna_msg.CANCEL_WAIT,
                success: function(form, action)
                {
                    try {
                        var r = action.result;
                        if (typeof r.success == 'undefined' || !r.success)
                        {
                            var msg = typeof r.msg != 'undefined' ? r.msg : 'Unknown error. Check server log';
                            Ext.Msg.alert(klarna_msg.TEXT_ALERT, msg);
                        }
                        else {
                            if(typeof r.refresh != 'undefined' && r.refresh == true)
                            {
                                contentTabs.getActiveTab().getUpdater().refresh();
                            }
                            closeWindow('doCancel_kp_wnd',orderId);
                            Ext.Msg.alert(klarna_msg.TEXT_ALERT, 'OK');
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
                    Ext.Msg.alert(klarna_msg.TEXT_ALERT, msg);
                }
            });
        }
    }
}

function klarnaRelease(orderId)
{
    var form = getKlarnaForm('doRelease_kp', orderId);

    if(typeof form != 'undefined')
    {
        if(form.isValid())
        {
            form.submit({
                waitMsg: klarna_msg.RELEASE_WAIT,
                success: function(form, action)
                {
                    try {
                        var r = action.result;
                        if (typeof r.success == 'undefined' || !r.success)
                        {
                            var msg = typeof r.msg != 'undefined' ? r.msg : 'Unknown error. Check server log';
                            Ext.Msg.alert(klarna_msg.TEXT_ALERT, msg);
                        }
                        else {
                            if(typeof r.refresh != 'undefined' && r.refresh == true)
                            {
                                contentTabs.getActiveTab().getUpdater().refresh();
                            }
                            closeWindow('doCancel_kp_wnd',orderId);
                            Ext.Msg.alert(klarna_msg.TEXT_ALERT, 'OK');
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
                    Ext.Msg.alert(klarna_msg.TEXT_ALERT, msg);
                }
            });
        }
    }
}

function klarnaUpdateOrder(orderId)
{
    console.log(orderId);
    Ext.Msg.show({
        msg: klarna_msg.UPDATE_ORDER_ASK,
        buttons: Ext.Msg.YESNO,
        fn: function(btn, text){
            if (btn == 'yes'){
                var mask = new Ext.LoadMask(Ext.getBody(), {msg:klarna_msg.CONNECTING});
                mask.show();
                var conn = new Ext.data.Connection();
                conn.request({
                    url: 'adminHandler.php?plugin=xt_klarna_kp&load_section=xt_klarna_kp&pg=updateOrder&sec='+csrf_key,
                    method: 'POST',
                    params: {orders_id: orderId},
                    error: function(responseObject) {
                        mask.hide();
                        Ext.Msg.alert(klarna_msg.TEXT_ALERT, klarna_msg.TEXT_ERROR);
                    },
                    waitMsg: klarna_msg.CONNECTING,
                    success: function(responseObject) {
                        mask.hide();
                        var jObj = JSON.parse(responseObject.responseText);
                        if (jObj.success!=true) Ext.Msg.alert(klarna_msg.TEXT_ALERT, klarna_msg.TEXT_ERROR + ': ' + jObj.msg)
                        else {
                            Ext.Msg.alert(klarna_msg.TEXT_ALERT, klarna_msg.TEXT_SUCCESS);
                            contentTabs.getActiveTab().getUpdater().refresh();
                        }
                    }
                });
            }
        },
        icon: Ext.MessageBox.QUESTION
    });
}





function triggerResendAsk(kp_order_id, capture_id)
{
    Ext.Msg.show({
        title: klarna_msg.TRIGGER_RESEND_ASK,
        buttons: Ext.Msg.YESNO,
        fn: function(btn, text){
            if (btn == 'yes'){
                var mask = new Ext.LoadMask(Ext.getBody(), {msg:klarna_msg.CONNECTING});
                mask.show();
                var conn = new Ext.data.Connection();
                conn.request({
                    url: 'adminHandler.php?plugin=xt_klarna_kp&load_section=xt_klarna_kp&pg=triggerResend&sec='+csrf_key,
                    method: 'POST',
                    params: {kp_order_id: kp_order_id, capture_id: capture_id},
                    error: function(responseObject) {
                        mask.hide();
                        Ext.Msg.alert(klarna_msg.TEXT_ALERT, klarna_msg.TEXT_ERROR);
                    },
                    waitMsg: klarna_msg.CONNECTING,
                    success: function(responseObject) {
                        mask.hide();
                        var jObj = JSON.parse(responseObject.responseText);
                        if (jObj.success!=true) Ext.Msg.alert(klarna_msg.TEXT_ALERT, klarna_msg.TEXT_ERROR + ': ' + jObj.msg)
                        else Ext.Msg.alert(klarna_msg.TEXT_ALERT, klarna_msg.TEXT_SUCCESS);
                    }
                });
            }
        },
        icon: Ext.MessageBox.QUESTION
    });
}

function extendAuthAsk(kp_order_id)
{
    Ext.Msg.show({
        title: klarna_msg.EXTEND_AUTH_ASK,
        buttons: Ext.Msg.YESNO,
        fn: function(btn, text){
            if (btn == 'yes'){
                var mask = new Ext.LoadMask(Ext.getBody(), {msg:klarna_msg.CONNECTING});
                mask.show();
                var conn = new Ext.data.Connection();
                conn.request({
                    url: 'adminHandler.php?plugin=xt_klarna_kp&load_section=xt_klarna_kp&pg=extendAuth&sec='+csrf_key,
                    method: 'POST',
                    params: {kp_order_id: kp_order_id},
                    error: function(responseObject) {
                        mask.hide();
                        Ext.Msg.alert(klarna_msg.TEXT_ALERT, klarna_msg.TEXT_ERROR);
                    },
                    waitMsg: klarna_msg.CONNECTING,
                    success: function(responseObject) {
                        mask.hide();
                        var jObj = JSON.parse(responseObject.responseText);
                        if (jObj.success!=true) Ext.Msg.alert(klarna_msg.TEXT_ALERT, klarna_msg.TEXT_ERROR + ': ' + jObj.msg)
                        else
                        {
                            Ext.Msg.alert(klarna_msg.TEXT_ALERT, klarna_msg.TEXT_SUCCESS + '<br/>' + jObj.payload.expires_at, function(btn, text){
                                $('.'+jObj.payload.kp_ref+'_expires_at').html(jObj.payload.expires_at);
                            });
                        }
                    }
                });
            }
        },
        icon: Ext.MessageBox.QUESTION
    });
}






function getKlarnaForm(name, orderId)
{
    var comp = Ext.getCmp(name+orderId);
    if(typeof comp != 'undefined')
    {
        return comp.getForm();
    }
    return null;
}

function getKlarnaWindow(name, orderId)
{
    var comp = Ext.getCmp(name+orderId);
    if(typeof comp != 'undefined')
    {
        return comp;
    }
    return null;
}

function showKlarnaWindow(id, orderId)
{
    var comp = Ext.getCmp(id+orderId);
    if(typeof comp != 'undefined')
    {
        comp.show();
    }
}

function closeWindow(name, orderId)
{
    if(typeof orderId == 'undefined')
        orderId = '';
    var wnd = Ext.getCmp(name+orderId);
    if(typeof wnd != 'undefined')
    {
        wnd.close();
    }
}

function syncRemoteWindowSize______________(orderId)
{
    var wnd = Ext.getCmp('shipcloud_add_parcelRemoteWindow');
    wnd.syncSize();
    return wnd;
}

