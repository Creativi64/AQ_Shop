/*
 * Ext JS Library 2.0
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://extjs.com/license
 */

Ext.app.CheckboxField = Ext.extend(Ext.form.Checkbox, {
    initComponent : function(){
        Ext.app.CheckboxField.superclass.initComponent.call(this);
        this.checked = false;
    },

    onClick: function() {

        try {
            // id prefix is 'check_all_prod_'
            var id_prefix = 'check_all_prod_';

            var dridId = this.id;
            var new_var = dridId.split("node_");

            if (new_var.length>1 && !new_var[1].includes("generated_slaves"))
            {
                var gridTmp = Ext.getCmp(new_var[1] + 'gridForm');
            }
            else {
                var new_var = dridId.split("subcat_");
                if (new_var.length > 1) {
                    var new_var2 = new_var[1].split("_catst_");
                    if (new_var2.length>1){
                        var new_id = new_var2[0];
                    }else var new_id =new_var[1];
                    var gridTmp = Ext.getCmp('product'+new_id+'gridForm');
                }
                else {
                    var cmpId = dridId.replace(id_prefix, '');
                    var gridFormIdPrefix = cmpId.replace(new RegExp("_[0-9]+", "g"), "");
                    if(cmpId.includes("generated_slaves"))
                    {
                        gridFormIdPrefix = 'generated_slaves';
                    }

                    var gridTmp = Ext.getCmp(cmpId).getComponent(cmpId + 'Wrapper').getComponent(gridFormIdPrefix + "gridForm");
                }
                if (typeof gridTmp == 'undefined') //  payment/shipping price
                {

                    var gridTmp = Ext.getCmp('productgridForm');
                }
            }

            if (gridTmp) {
                if ($('#' + this.id).is(':checked')) {
                    var els=Ext.query(".x-grid3-check-col");
                    var state = true;
                }
                else {
                    var els=Ext.query(".x-grid3-check-col-on");
                    var state = false;
                }

                for (var i = 0; i < els.length; i++) {
                    var index = gridTmp.getView().findRowIndex(els[i]);
                    var record = gridTmp.getStore().getAt(index);
                    if (record)
                        record.set('selectedItem', state);
                }
            }
        }catch(e)
        {
            console.log(e);
        }
    }

});