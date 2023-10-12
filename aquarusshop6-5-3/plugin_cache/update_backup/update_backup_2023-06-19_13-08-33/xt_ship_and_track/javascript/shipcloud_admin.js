$(function()
{

});

var shipcloud_pickups = {};
shipcloud_pickups.ups = {
    'enabled':true,
    'required':false,
    'latestSameEarliest':false
};
shipcloud_pickups.dhl_express = {
    'enabled':true,
    'required':false,
    'latestSameEarliest':true
};
shipcloud_pickups.hermes = {
    'enabled':true,
    'required':false,
    'latestSameEarliest':false
};
shipcloud_pickups.dpd = {
    'enabled':true,
    'required':false,
    'latestSameEarliest':false
};
shipcloud_pickups.tnt = {
    'enabled':true,
    'required':false,
    'latestSameEarliest':true
};
shipcloud_pickups.go = {
    'enabled':true,
    'required':true,
    'latestSameEarliest':true
};

function setShipcloudCarrierSelection(val, orderId, carriers)
{
    //console.log(val, orderId, carriers);
    try {
        var retoureFieldset = Ext.getCmp('shipcloud_fieldset_retoure_'+orderId);
        if (typeof retoureFieldset != 'undefined') retoureFieldset.getEl().hide();

        var carrier =  carriers[val];
        if(typeof carrier == 'undefined')
        {
            throw 'key ['+val+'] not found in carriers collection';
        }

        var comboPackages  = Ext.getCmp('shipcloud_package_type'+orderId);
        if(typeof comboPackages == 'undefined')
        {
            throw 'package types combo box not found for order ['+orderId+']';
        }

        var comboServices = Ext.getCmp('shipcloud_service'+orderId);
        if(typeof comboServices == 'undefined')
        {
            throw 'services combo box not found for order ['+orderId+']';
        }

        var comboData = initCombo(carrier.services);
        var store = initComboStore(comboData.data)
        comboServices.bindStore(store);
        comboServices.setValue(comboData.defaultValue);
        comboServices.setDisabled(false);

        var comboData = initCombo(carrier.package_types);
        var store = initComboStore(comboData.data)
        comboPackages.bindStore(store);
        comboPackages.setValue(comboData.defaultValue);
        comboPackages.setDisabled(false);

        enableAdditionalServices(val, orderId);
        enablePickup(val, orderId)
    }
    catch (e)
    {
        console.error(e);
    }
}

function setShipcloudPackageTemplate(val, orderId, sc_packages)
{
    try {
        var sc_package =  sc_packages[val];
        if(typeof sc_package == 'undefined')
        {
            throw 'key ['+val+'] not found in packages collection';
        }
        setShipcloudFormTextValue('package_length', sc_package.shipcloud_package_length, orderId);
        setShipcloudFormTextValue('package_width', sc_package.shipcloud_package_width, orderId);
        setShipcloudFormTextValue('package_height', sc_package.shipcloud_package_height, orderId);
        setShipcloudFormTextValue('package_weight', sc_package.shipcloud_package_weight,  orderId);
    }
    catch (e)
    {
        console.error(e);
    }
}

function setShipcloudFormTextValue(fieldName, val, orderId)
{
    var form = getShipcloudForm(orderId);
    var c = form.findField(fieldName);
    c.setValue(val)
}

function initCombo(obj)
{
    var arr =[];
    var defaultValue = false;
    for (var prop in obj) {
        // skip proto props
        if(!obj.hasOwnProperty(prop)) continue;
        arr.push( { "id" : prop, "name" : obj[prop], "desc" : obj[prop]});
        if(defaultValue===false)
        {
            defaultValue = prop;
        }
    }
    return { "data" : arr, "defaultValue" : defaultValue};
}

function initComboStore(data)
{
    return new Ext.data.JsonStore({
        fields: ['id', 'name', 'desc'],
        data : data
    });
}

function initShipcloudCombo(fieldName, orderId, defaultIndex)
{
    var form = getShipcloudForm(orderId);
    var c = form.findField(fieldName);

    var idx = 0;
    if (typeof defaultIndex != 'undefined') idx = defaultIndex;

    c.setValue(c.store.data.items[idx].data.id, true);
    c.fireEvent('select');
    c.setValue(c.store.data.items[idx].data.id, true);
}

/**
 *   api calls
 */
function shipcloud_sendForm(orderId, fieldToSet)
{
    var form = getShipcloudForm(orderId);

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
            var btn = Ext.getCmp('TEXT_SHIPCLOUD_CREATE_LABEL'+orderId);
            btn.setText(shipcloud_msg.TEXT_SHIPCLOUD_CREATE_LABEL);
            btn.removeClass('shipcloud-bg-green');

            if (typeof fieldToSet != 'undefined') form.findField(fieldToSet).setValue('1');
            form.submit({
                waitMsg: shipcloud_msg.TEXT_SHIP_AND_TRACK_WAIT,
                success: function(form, action)
                {
                    try {
                        if (typeof fieldToSet != 'undefined') form.findField(fieldToSet).setValue('0');

                        var r = action.result;
                        if (!r.success)
                        {
                            Ext.Msg.alert(shipcloud_msg.TEXT_ALERT, r.msg);
                        }
                        else {
                            if (typeof r.price != 'undefined') {
                                btn.setText(r.price + ' - ' + shipcloud_msg.TEXT_SHIPCLOUD_CREATE_LABEL);
                                btn.addClass('shipcloud-bg-green');
                            }
                            if(typeof r.refresh != 'undefined' && r.refresh == true)
                            {
                                contentTabs.getActiveTab().getUpdater().refresh();
                            }
                        }
                    }
                    catch(e)
                    {
                        console.error(e);
                    }
                },
                failure: function(form, action)
                {
                    if (typeof fieldToSet != 'undefined') form.findField(fieldToSet).setValue('0');
                    var r = action.result;
                    Ext.Msg.alert(shipcloud_msg.TEXT_ALERT, r.msg);
                }
            });
        }
        setTimeout(function(form){
            Ext.getCmp('addParcelForm_shipcloud'+orderId).getForm().findField('quote').setValue('0');
            Ext.getCmp('addParcelForm_shipcloud'+orderId).getForm().findField('prepare_label').setValue('0');
        }, 200);
        //setTimeout(
        //  (function(form){ form.findField('quote').setValue('0'); })(form), 200);
    }
}


function getSelectedOrders($asString)
{
    //var records = new Array();
    var records = window.sc_orders_modified;
    var record_ids = [];
    for (var i = 0; i < records.length; i++) {
        if (records[i].get('selectedItem'))
            record_ids.push( records[i].get('orderId'));
    }
    if(typeof $asString != 'undefined') return record_ids.join('|');
    return record_ids;
}

function getShipcloudForm(orderId)
{
    var form = Ext.getCmp('addParcelForm_shipcloud'+orderId).getForm();
    return form;
}

function syncRemoteWindowSize(orderId)
{
    var cmpId = "shipcloud_add_parcel"+"RemoteWindow2";
    var wnd = Ext.getCmp(cmpId);
    if(typeof  wnd !== "undefined")
        wnd.syncSize();
    else console.error("shipcloud syncRemoteWindowSize: cmp not found [" + cmpId + "]");
}

function enablePickup(carrier, orderId) {
    var fieldSet = Ext.getCmp('shipcloud_fieldset_pickup_' + orderId);
    if (typeof fieldSet != 'undefined') {
        if (shipcloud_pickups.hasOwnProperty(carrier)) {
            //console.log(shipcloud_pickups[carrier]);
            var panelLatest = Ext.getCmp('panel_pickup_latest_' + orderId);
            if (typeof fieldSet != 'undefined') {
                var pickupSettings = shipcloud_pickups[carrier];
                // earliest/latest pickup
                if (pickupSettings.latestSameEarliest == true) {
                    panelLatest.hide();
                }
                else {
                    panelLatest.show();
                }
                // pickup is required ?


                fieldSet.show();

                if (pickupSettings.required == true) {
                    fieldSet.expand(true);
                    $('#'+fieldSet.getId()).find('input[name=pickup_fieldset]').prop('disabled',true);
                    $('#'+fieldSet.getId()).find('input[name=pickup]').prop({'checked':true, 'disabled':true});
                }
                else {
                    fieldSet.collapse(true);
                    $('#'+fieldSet.getId()).find('input[name=pickup_fieldset]').prop('disabled',false);
                    $('#'+fieldSet.getId()).find('input[name=pickup]').prop({'checked':false, 'disabled':false});
                }
            }
        }
        else {
            //console.log('no pickup available for carrier [' + carrier + ']');
            fieldSet.hide();
            fieldSet.collapse(true);
        }
    }

}

function enableAdditionalServices(carrier, orderId)
{
    var arr = ['dhl','dpd'];
    arr.forEach(function(c){
        var fieldSet = Ext.getCmp('group_add_service_'+c + orderId);
        if(typeof fieldSet != 'undefined')
        {
            fieldSet.collapse(false);
            fieldSet.hide();
        }
    });

    // insert 'apply total from cart' button's if not already present
    // cod
    var p = $('#order_total_price'+orderId).val();
    var btn = $('#applyTotalFromCart_cod'+orderId);
    if(btn.length == 0)
    {
        var btnHtml = '<span>&nbsp;<img id="applyTotalFromCart_cod_delete'+orderId+'" src="images/icons/delete.png" /></span>';
        $(btnHtml).insertAfter('#add_service_dhl_cash_on_delivery'+orderId);
        $('#applyTotalFromCart_cod_delete'+orderId).click(function()
        {
            $('#add_service_dhl_cash_on_delivery'+orderId).val('');
        });
        var btnHtml = '<span>&nbsp;<img id="applyTotalFromCart_cod'+orderId+'" src="images/icons/basket_go_left.png" title="'+shipcloud_msg.APPLY_CART_AMOUNT+'" /></span>';
        $(btnHtml).insertAfter('#add_service_dhl_cash_on_delivery'+orderId);
        $('#applyTotalFromCart_cod'+orderId).click(function()
        {
            $('#add_service_dhl_cash_on_delivery'+orderId).val(p);
        });
    }
    // declared value
    var btn = $('#applyTotalFromCart_dv'+orderId);
    if(btn.length == 0)
    {
        var btnHtml = '<span>&nbsp;<img id="applyTotalFromCart_dv_delete'+orderId+'" src="images/icons/delete.png" /></span>';
        $(btnHtml).insertAfter('#add_service_dhl_declared_value'+orderId);
        $('#applyTotalFromCart_dv_delete'+orderId).click(function()
        {
            $('#add_service_dhl_declared_value'+orderId).val('');
        });
        var btnHtml = '<span>&nbsp;<img id="applyTotalFromCart_dv'+orderId+'" src="images/icons/basket_go_left.png" title="'+shipcloud_msg.APPLY_CART_AMOUNT+'" /></span>';
        $(btnHtml).insertAfter('#add_service_dhl_declared_value'+orderId);
        $('#applyTotalFromCart_dv'+orderId).click(function()
        {
            $('#add_service_dhl_declared_value'+orderId).val(p);
        });
    }


    var fieldSet = Ext.getCmp('group_add_service_'+carrier + orderId);
    if(typeof fieldSet != 'undefined')
    {
        fieldSet.show();
        //fieldSet.expand(true);
    }
}

function addApplyFromCartBtn(orderId, after, fieldToset, withDelete /* defaults to true*/)
{
    if(typeof withDelete == 'undefined')
    {
        withDelete = true;
    }

}

function isDescriptionRequired()
{}

function applyShipcloudServiceSelection(val, orders_id)
{
    if(val == 'returns')
    {
        if(shipcloud_different_retoure_address == true) {
            Ext.getCmp('shipcloud_panel_from_' + orders_id).getEl().hide();
            Ext.getCmp('shipcloud_panel_from_' + orders_id).addClass('sc-disable');
            $('#shipcloud_panel_from_' + orders_id).css({'display':'none'});
            Ext.getCmp('shipcloud_fieldset_retoure_' + orders_id).getEl().show();
        }
        // title
        Ext.getCmp('shipcloud_fieldset_to_'+orders_id).setTitle(shipcloud_msg.TEXT_SHIPCLOUD_RETOURE_FROM);
        Ext.getCmp('shipcloud_fieldset_from_'+orders_id).setTitle(shipcloud_msg.TEXT_SHIPCLOUD_RETOURE_TO);
    }
    else {
        if(shipcloud_different_retoure_address == true) {
            Ext.getCmp('shipcloud_fieldset_retoure_' + orders_id).getEl().hide();
            Ext.getCmp('shipcloud_panel_from_' + orders_id).getEl().show();
            Ext.getCmp('shipcloud_panel_from_' + orders_id).removeClass('sc-disable');
        }
        // title
        Ext.getCmp('shipcloud_fieldset_to_'+orders_id).setTitle(shipcloud_msg.TEXT_SHIPCLOUD_EDIT_TO);
        Ext.getCmp('shipcloud_fieldset_from_'+orders_id).setTitle(shipcloud_msg.TEXT_SHIPCLOUD_OVERRIDE_FROM);
    }

    syncRemoteWindowSize();
}

