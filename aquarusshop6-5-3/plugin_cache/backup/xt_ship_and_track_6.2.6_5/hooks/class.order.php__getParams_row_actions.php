<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_ship_and_track/classes/class.xt_ship_and_track.php';

if(!empty(constant('XT_SHIPCLOUD_API_KEY')))
{
    $code = 'shipcloud_add_parcel';

    $window = xt_ship_and_track_layoutPart::getAddParcelWindowPanel_shipcloud(0);
    $remoteWindow = ExtFunctions::_RemoteWindow2(TEXT_SHIPCLOUD, $code, $window, 500, 725, 'window');
    $sandbox = XT_SHIPCLOUD_SANDBOX == 1 ? '   <span style="color:#c6080b">SANDBOX</span>' : '';
    $remoteWindow->setModal(true)->attachListener('show',
        new PhpExt_Listener(PhpExt_Javascript::functionDef(null, "
            var records = new Array();
            records = orderds.getModifiedRecords();
            var record_ids = [];
            for (var i = 0; i < records.length; i++) {
                if (records[i].get('selectedItem'))
                    record_ids.push( records[i].get('orders_id'));
            }
            //console.log(record_ids.join('|'), this);
            this.setTitle(this.title + ' - ' + record_ids.length + ' Bestellungen ausgewählt" . $sandbox . "');
            ")));

    $js_add_parcel = "
			var records = new Array();
            records = orderds.getModifiedRecords();
            var record_ids = [];
            for (var i = 0; i < records.length; i++) {
                if (records[i].get('selectedItem'))
                    record_ids.push( records[i].get('orders_id'));
            }
            if (record_ids.length == 0) return;
			";
    $js_add_parcel .= $remoteWindow->getJavascript(false, "new_window") . ' new_window.show();';

    $UserButtons[$code] = array('text' => 'TEXT_SHIPCLOUD', 'style' => $code, 'cls' => 'btn-wide-icon-30', 'icon' => '../../../plugins/xt_ship_and_track/images/icons/shipcloud16.png', 'acl' => '', 'stm' => $js_add_parcel);
    $params['display_' . $code . 'Btn'] = true;
    $params['UserButtons'] = $UserButtons;
}