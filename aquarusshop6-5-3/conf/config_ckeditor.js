/**
 * @license Copyright (c) 2003-2018, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see https://ckeditor.com/legal/ckeditor-oss-license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here.
	// For complete reference see:
	// http://docs.ckeditor.com/#!/api/CKEDITOR.config

	// The toolbar groups arrangement, optimized for two toolbar rows.
	config.toolbarGroups = [
		{ name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
		{ name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },
		{ name: 'links' },
		{ name: 'insert' },
	//	{ name: 'forms' },
		{ name: 'tools' },
		{ name: 'document',	   groups: [ 'mode', 'document', 'doctools' ] },
		{ name: 'others' },
		'/',
		{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
		{ name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ] },
		{ name: 'styles' },
		{ name: 'colors' },
		{ name: 'about' }
	];

	// Remove some buttons provided by the standard plugins, which are
	// not needed in the Standard(s) toolbar.
	config.removeButtons = 'Underline,Subscript,Superscript,Forms,Flash';

	config.removePlugins = 'save';

	// Set the most common block elements.
	config.format_tags = 'p;h1;h2;h3;pre';

	// Simplify the dialog windows.
	config.removeDialogTabs = 'image:advanced;link:advanced;link:upload;image:Upload';

    config.extraAllowedContent = 'div(*); p(*)';

    config.enterMode = CKEDITOR.ENTER_BR;

    // set https to standard link protocol
    config.linkDefaultProtocol = 'https://';

    /** die Funktionen
     * addAdditionalCkeditorPlugins
     * addAdditionalCkeditorConfig
     * werden durch das xt-Plugin xt_ckeditor bereitgestellt
     */
    if (typeof addAdditionalCkeditorPlugins === "function") { addAdditionalCkeditorPlugins(config) };
    if (typeof addAdditionalCkeditorConfig === "function") { addAdditionalCkeditorConfig(config) };
	
};

CKEDITOR.on('instanceReady', function (ev) {
    var editor = ev.editor,
        dataProcessor = editor.dataProcessor,
        htmlFilter = dataProcessor && dataProcessor.htmlFilter;

    if(htmlFilter)
    {
        htmlFilter.addRules({
            elements: {
                $: function (element) {
                    try {
                    // for images add !important to height and width
                    if (element.name == 'img') {
                        var style = element.attributes.style;
                        if (style) {
                            // check !important added already
                            var match = /(?:^|\s)width\s*:\s*(\d+)px!important/i.exec(style);
                                if (match == null) {
                                match = /(?:^|\s)width\s*:\s*(\d+)px/i.exec(style);
                                    if (match) {
                                    style = style.replace(match[0], match[0]+'!important');
                                }
                            }

                            // same for height
                            var match = /(?:^|\s)height\s*:\s*(\d+)px!important/i.exec(style);
                                if (match == null) {
                                match = /(?:^|\s)height\s*:\s*(\d+)px/i.exec(style);
                                    if (match) {
                                    style = style.replace(match[0], match[0]+'!important');
                                }
                            }

                            element.attributes.style = style;
                        }
                    }
                }
                    catch(err)
                    {
                        alert(err.message);
                    }
                }
            }
        });
    }
    else {
        alert('ckeditor config_ckeditor.js : could not get instance of htmlFilter, img style did not get the !important rule applied')
    }
});
