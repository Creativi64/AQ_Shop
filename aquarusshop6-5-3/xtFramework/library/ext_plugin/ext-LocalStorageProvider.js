
Ext.state.LocalStorageProvider = Ext.extend(Ext.state.Provider, {

    constructor : function(config){
        Ext.state.LocalStorageProvider.superclass.constructor.call(this);
        Ext.apply(this, config);
        this.state = this.readStorage();
    },

    // private
    set : function(name, value){
        if(typeof value == "undefined" || value === null){
            this.clear(name);
            return;
        }
        localStorage.setItem('ys-' + name, JSON.stringify(value));
        Ext.state.LocalStorageProvider.superclass.set.call(this, name, value);
    },

    // private
    clear : function(name){
        localStorage.removeItem('ys-' + name);
        Ext.state.LocalStorageProvider.superclass.clear.call(this, name);
    },

    // private
    readStorage : function(){
        let storage = [];
        const items = { ...localStorage };
        const keys = Object.keys(localStorage);
        keys.forEach( (key) => {
            if(key && key.substring(0,3) === "ys-"){
                try
                {
                    storage[key.substring(3)] = JSON.parse(localStorage.getItem(key));
                }
                catch(e)
                {
                    console.log(key, e);
                }
            }
        });
        return storage;
    }

});