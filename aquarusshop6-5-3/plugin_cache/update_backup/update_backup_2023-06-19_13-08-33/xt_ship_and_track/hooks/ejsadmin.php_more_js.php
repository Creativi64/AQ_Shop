<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_ship_and_track/classes/class.xt_ship_and_track.php';

$sc = new xt_shipcloud_carriers();
$sc->setPosition('admin');
$sc->url_data['get_data'] = true;
$carriers = $sc->_get(0, -1); // get all no matter enabled or not

// todo um additional servcies anreichern oder besser schon beim fetch

$data = array();
foreach($carriers->data as $k => &$c)
{
    $decoded = json_decode($c[COL_SHIPCLOUD_CARRIER_DATA]);

    $services = array();
    foreach($decoded->services as $s)
    {
        $services[$s] = $s;
    }
    $package_types = array();
    $decoded->services = $services;

    foreach($decoded->package_types as $s)
    {
        $package_types[$s] = $s;
    }
    $decoded->package_types = $package_types;

    $data[$c['code']] = $decoded;
}
$carrier_json = json_encode($data);


$sc = new xt_shipcloud_packages();
$sc->setPosition('admin');
$sc->url_data['get_data'] = true;
$packages = $sc->_get(0, 1);

// todo um additional servcies anreichern oder besser schon beim fetch

$data = array();
foreach($packages->data as $k => &$c)
{
    $data[$c[COL_SHIPCLOUD_PACKAGE_CODE]] = $c;
}
$package_json = json_encode($data);

xt_shipcloud_settings::readSettings();
$dra = defined('XT_SHIPCLOUD_DIFFERENT_RETOURE') && constant('XT_SHIPCLOUD_DIFFERENT_RETOURE')==='1' ? 'true':'false';

echo '

<script type="text/javascript" charset="utf-8">
var shipcloud_carriers = '.$carrier_json.';
var shipcloud_packages = '.$package_json.';
var shipcloud_different_retoure_address = '.$dra.';
var shipcloud_msg =
{
    TEXT_SHIPCLOUD_CREATE_LABEL : "'.TEXT_SHIPCLOUD_CREATE_LABEL.'",
    TEXT_SHIP_AND_TRACK_WAIT : "'.TEXT_SHIP_AND_TRACK_WAIT.'",
    TEXT_ERROR_MSG : "'.TEXT_ERROR_MSG.'",
    TEXT_ALERT : "'.TEXT_ALERT.'",
    APPLY_CART_AMOUNT: "'.TEXT_SHIPCLOUD_APPLY_CART_AMOUNT.'",
    TEXT_SHIPCLOUD_RETOURE_TO: "'.TEXT_SHIPCLOUD_RETOURE_TO.'",
    TEXT_SHIPCLOUD_RETOURE_FROM: "'.TEXT_SHIPCLOUD_RETOURE_FROM.'",
    TEXT_SHIPCLOUD_EDIT_TO: "'.TEXT_SHIPCLOUD_EDIT_TO.'",
    TEXT_SHIPCLOUD_OVERRIDE_FROM: "'.TEXT_SHIPCLOUD_OVERRIDE_FROM.'"
}
</script>
<script type="text/javascript" src="../plugins/xt_ship_and_track/javascript/shipcloud_admin.js?'.time().'"></script>

';





echo "
<script type='text/javascript'>
	contentTabs =	new Ext.TabPanel({
	                    region:'center',
	                    deferredRender:false,
	                    activeTab:0,
						enableTabScroll:true,
	       				defaults: {autoScroll:true},
						plugins: new Ext.ux.TabCloseMenu(),
	                    id:'sites',
	                    activeItem:0,
	                    defaultType: 'iframepanel',items: [ {xtype: 'panel', title: 'Dashboard', autoLoad : {url : 'dashboard.php', params: { parentNode: 'dashboard' }, scripts : true}, id: 'dashboard', closable:false }],defaults:{
	                    closable:true,
	                    autoScroll:true,
				        loadMask:{msg:'Loading ...'},
	                    //required so nonIE (of all things) wont refresh the iframe object when hidden
	                    style:{position:(Ext.isIE?'relative':'absolute')},
	                    hideMode:(Ext.isIE?'display':'visibility')
                      }
				 });
	$(function(){
setTimeout( function(){
    //addTab('adminHandler.php?load_section=xt_shipcloud_settings&plugin=xt_ship_and_track&load_section=xt_shipcloud_settings&&edit_id=1&sec=61799d5819efe927cfda4ef35007bcbb','shipcloud Einstellungen');
    //addTab('adminHandler.php?load_section=xt_ship_and_track_shipcloud&plugin=xt_ship_and_track&pg=overview&sec=61799d5819efe927cfda4ef35007bcbb','shipcloud Sendungen');
    //addTab('adminHandler.php?load_section=order&pg=overview&parentNode=node_order&sec=7ada2f4e7ebcdec498ffb20827f39323','Bestellungen');
    //addTab('order_edit.php?pg=overview&parentNode=node_order&sec=f482e0c86d54a4713215e235a2ddaff0&edit_id=83&gridHandle=ordergridForm&sec=f482e0c86d54a4713215e235a2ddaff0','Bestellung 1');
    //addTab('adminHandler.php?load_section=product&pg=overview&parentNode=node_product&sec=df37301a6fe1b75dd5b043eaaff7b728&edit_id=5&gridHandle=productgridForm&sec=df37301a6fe1b75dd5b043eaaff7b728','artikel ms id5');
    //trees[0].collapse();trees[1].expandAll();trees[1].expand();
    }, 500 );
})
</script>
";

