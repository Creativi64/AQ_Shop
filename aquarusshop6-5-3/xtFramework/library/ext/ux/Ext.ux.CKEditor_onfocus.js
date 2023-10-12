Ext.namespace("Ext.ux.form");

Ext.ux.form.CKEditor = function(cfg){
    this.config = cfg;

    var href = window.location.href;
    var dir = href.substring(0, href.lastIndexOf('/')) + "/";
    dir = dir.replace("xtAdmin/","");
    
    this.config.CKConfig = Ext.apply({
        uiColor: '#D0D0D0',
        height: '300',
        customConfig: dir+'conf/config_ckeditor.js',
        defaultLanguage: typeof xt_language != 'undefined' && (xt_language=='en' || xt_language=='de') ? xt_language : 'en'
        
    }, this.config.CKConfig);

    Ext.ux.form.CKEditor.superclass.constructor.call(this, this.config);
};

Ext.extend(Ext.ux.form.CKEditor, Ext.form.TextArea,  {

    onFocus : function(ct, position){

        if (CKEDITOR.instances[this.id] != undefined) {
            delete CKEDITOR.instances[this.id];
        }

        if(!this.el){
            this.defaultAutoCreate = {
                tag: "textarea",
                autocomplete: "off"
            };
        }
        //Ext.form.TextArea.superclass.onRender.call(this, ct, position);
        Ext.form.TextArea.superclass.onFocus.call(this, ct, position);
        var self = this;

        window.setTimeout(function(){
        	CKEDITOR.replace(self.id, self.config.CKConfig);
        }, 500);
    },

    setValue : function(value){
        Ext.form.TextArea.superclass.setValue.call(this,[value]);
        var ckedit = CKEDITOR.instances[this.id];
        if (ckedit){
            ckedit.setData( value );
        }
    },

    getValue : function(){
        var ckedit = CKEDITOR.instances[this.id];
        if (ckedit){
            ckedit.updateElement();
        }
        return Ext.form.TextArea.superclass.getValue.call(this);
    },

    isDirty: function () {
        if (this.disabled || !this.rendered) {
            return false;
        }
        return String(this.getValue()) !== String(this.originalValue);
    },

    getRawValue : function(){
        var ckedit = CKEDITOR.instances[this.id];
        if (ckedit){
            ckedit.updateElement();
        }
        return Ext.form.TextArea.superclass.getRawValue.call(this);
    },


    destroyInstance: function(){
        var ckedit = CKEDITOR.instances[this.id];
        if (ckedit){
            delete ckedit;
        }
    }
});

Ext.reg('ckeditor', Ext.ux.form.CKEditor);