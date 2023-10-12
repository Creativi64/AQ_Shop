
function init_ms_form(selectionItem)
{
    try {
        //console.log('ms init ' + selectionItem);
        $('#' + selectionItem + '.x-panel input[name=products_master_flag]').click(function () {
            //console.log('products_master_flag clicked', this);
            if ($(this).attr('checked')) {
                toogleMsSlaveAttributes($(this.form), false);
                toogleMsMasterAttributes($(this.form), true);
            }
            else {
                toogleMsMasterAttributes($(this.form), false);
            }
        });

        var compId = $('#' + selectionItem + '.x-panel input[name=products_master_model]').siblings('input.x-form-text.x-form-field').attr('id');
        if (typeof compId != 'undefined') {
            var comp = Ext.getCmp(compId);
            if (typeof comp != 'undefined') {
                comp.on('select', function () {
                    //console.log(this.getValue());
                    if (this.getValue() != '') {
                        toogleMsMasterAttributes($('#' + this.id).closest('form'), false);
                        toogleMsSlaveAttributes($('#' + this.id).closest('form'), true);
                    }
                    else {
                        toogleMsSlaveAttributes($('#' + this.id).closest('form'), false);
                    }
                });
            }
            else {
                console.warn('ext component not found for id ['+compId+']')
            }
        }
        else {
            //console.warn('ext component id not found item ['+selectionItem+']')
        }
    }
    catch(e)
    {
        console.error(e);
    }
}

function toogleMsSlaveAttributes(form, enable)
{
    try {
        if (enable) {
            form.find('input[name=products_master_flag]').removeAttr('checked');
            form.find('input[name=products_master_flag]').closest('div.x-form-item.x-tab-item').hide();
            form.find('input[name=products_master_slave_order]').closest('div.x-form-item.x-tab-item').show();
        }
        else {
            form.find('input[name=products_master_flag]').closest('div.x-form-item.x-tab-item').show();
            form.find('input[name=products_master_slave_order]').closest('div.x-form-item.x-tab-item').hide();
        }

        var elems = [

            'products_description_from_master',
            'products_short_description_from_master',
            'products_keywords_from_master',
            'ms_load_masters_main_img',
            'load_mains_imgs',
            'ms_load_masters_free_downloads',
            'products_master_slave_order',
            'products_image_from_master'
        ];
        $.each(elems, function (index, value) {
            var elem = form.find('input[name=' + value + ']').closest('div.x-form-item.x-tab-item');
            if (enable) elem.show();
            else elem.hide();
        });

    }
    catch(e)
    {
        console.error(e);
    }
}

function toogleMsMasterAttributes(form, enable)
{
    try {
        var elems = [
            'ms_open_first_slave',
            'ms_show_slave_list',
            'ms_filter_slave_list',
            'ms_filter_slave_list_hide_on_product',
            'products_description_from_master',
            'products_short_description_from_master',
            'products_keywords_from_master',
            'ms_load_masters_main_img',
            'load_mains_imgs',
            'ms_load_masters_free_downloads',
            'products_option_master_price',
            'products_image_from_master'
        ];
        $.each(elems, function( index, value ) {
            var elem = form.find('input[name='+value+']').closest('div.x-form-item.x-tab-item');
            if(enable) elem.show();
            else elem.hide();
        });
        if(enable) {
            form.find('input[name=products_master_slave_order]').closest('div.x-form-item.x-tab-item').hide();
            form.find('input[name=products_master_model]').closest('div.x-form-item.x-tab-item').hide();
            var compId = form.find('input[name=products_master_model]').siblings('input.x-form-text.x-form-field').attr('id');
            if(typeof compId != 'undefined')
            {
                var comp = Ext.getCmp(compId);
                if(typeof comp != 'undefined')
                {
                    comp.setValue('');
                }
                else {
                    console.warn('ext component not found for id ['+compId+']')
                }
            }
            else {
                //console.warn('ext component id not found item ['+selectionItem+']')
            }
        }
        else {
            form.find('input[name=products_master_slave_order]').closest('div.x-form-item.x-tab-item').hide();
            form.find('input[name=products_master_model]').closest('div.x-form-item.x-tab-item').show();
        }
    }
    catch(e)
    {
        console.error(e);
    }
}
