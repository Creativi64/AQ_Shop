$(document).ready(function() {
    /**
     * example for overriding default variation matching js behavior 
     *
     * $.widget("ui.hitmeister_variation_matching", $.ui.ml_variation_matching, {
     *     _init: function() {
     *         this._super();
     *         myConsole.log('new init');
     *     }
     * });
     *
     * After this, starting widget should be done like this:
     * $(ml_vm_config.formName).hitmeister_variation_matching({...});
     */
    $(ml_vm_config.formName).ml_variation_matching({
        urlPostfix: '&kind=ajax&where=' + ml_vm_config.viewName,
        i18n: ml_vm_config.i18n,
        elements: {
            newGroupIdentifier: '#newGroupIdentifier',
            customVariationHeaderContainer: '#tbodyVariationConfigurationSelector',
            newCustomGroupContainer: '#newCustomGroup',
            mainSelectElement: '#PrimaryCategory',
            matchingHeadline: '#tbodyDynamicMatchingHeadline',
            matchingCustomHeadline: '#tbodyDynamicMatchingCustomHeadline',
            matchingOptionalHeadline: '#tbodyDynamicMatchingOptionalHeadline',
            matchingInput: '#tbodyDynamicMatchingInput',
            matchingCustomInput: '#tbodyDynamicMatchingCustomInput',
            matchingOptionalInput: '#tbodyDynamicMatchingOptionalInput',
            categoryInfo: '#categoryInfo'
        },
        shopVariations: ml_vm_config.shopVariations
    });
});
