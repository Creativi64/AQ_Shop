var settings;

function preUploadPopup(e) {
    var dialogHtml = '<div id="infoDiagFee" class="dialog2" title="Information"></div>';

    $('#infoDiagFee').remove();                           
    $('html').append(dialogHtml);
    $('#infoDiagFee').jDialog({
	buttons: [
	    {
		id: 'getarticlesfee-ok',
		text: settings.i18n.ok,
		click: function() {
		    $(this).dialog('close');
		    settings.addItems(e);
		}
	    },
	    {
		id: 'getarticlesfee-abort',
		text: settings.i18n.abort,
		click: function() {
		    $(this).dialog('close');
		}
	    }
	]
    });

    $('#infoDiagFee').html(settings.message);

}


$.fn.configureUploadPopup = function(options) {
    var defaults = {
	addItems: function() {
	    alert('Method addItems must be defined.');
	},
	method: 'preUploadPopup',
	message: null,
	i18n: {
	    ok: 'Ok',
	    abort: 'Abort'
	}
    };

    settings = $.extend({}, defaults, options);

    var e = this;

	preUploadPopup(e);


};
