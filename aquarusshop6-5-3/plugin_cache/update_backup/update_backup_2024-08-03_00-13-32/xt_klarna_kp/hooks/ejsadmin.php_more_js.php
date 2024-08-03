<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_klarna_kp/classes/constants.php';
require_once _SRV_WEBROOT_ADMIN.'page_includes.php';

/**
 *    anhängen einer erstelleten sendungsnummer an ein vorhandenes capture
 */
$extF = new ExtFunctions();
$capturesWnd = $extF->_RemoteWindow3("TEXT_KLARNA_LINK_TRACKING_TO_CAPTURE","Captures","adminHandler.php?plugin=xt_klarna_kp&load_section=xt_klarna_kp&pg=getCapturesPanel&xt_orders_id='+xt_orders_id+'&tracking_id='+tracking_id+'" , '', array(), 400, 200, 'window', 'doLinkCapture_kp_wnd');
$btn = PhpExt_Button::createTextButton(__define("TEXT_KLARNA_KP_DO_LINK"),
    new PhpExt_Handler(PhpExt_Javascript::stm("
                            klarnaLinkCapture(xt_orders_id);
                        "))
)
    //->setId("TEXT_KLARNA_KP_CANCEL_LABEL" . $data['xt_orders_id'])
    ->setCssStyle('font-weight: bold')
    ->setCssClass('bold')
    ->setType(PhpExt_Button::BUTTON_TYPE_SUBMIT);
$capturesWnd->addButton($btn);
$js_capturesWnd = $capturesWnd->getJavascript(false, "new_window").PHP_EOL.' new_window.show();';


echo '
<script type="text/javascript" charset="utf-8">
var klarna_msg =
{
    TEXT_SUCCESS: "'.TEXT_SUCCESS.'",
    TEXT_ERROR: "'.TEXT_FAILURE.'",
   
    TEXT_ALERT: "'.TEXT_ALERT.'",
    CAPTURE_WAIT : "'.TEXT_KLARNA_KP_CAPTURE_WAIT.'",
    REFUND_WAIT : "'.TEXT_KLARNA_KP_REFUND_WAIT.'",
    CANCEL_WAIT : "'.TEXT_KLARNA_KP_CANCEL_WAIT.'",
    RELEASE_WAIT :"'.TEXT_KLARNA_KP_RELEASE_WAIT.'",
    
    TRIGGER_RESEND_ASK :"'.TEXT_KLARNA_TRIGGER_RESEND_ASK.'",
    UPDATE_ORDER_ASK:"'.TEXT_KLARNA_UPDATE_ORDER_ASK.'",
    
    CONNECTING :"'.TEXT_KLARNA_CONNECTING.'",
    
    EXTEND_AUTH_ASK :"'.TEXT_KLARNA_EXTEND_AUTH_ASK.'",
    
    CAPTURE_ALL_ASK: "'.TEXT_KLARNA_CAPTURE_ALL_ASK.'",
    
}

function openKlarnaRegisterPage() {
    	addITab(\'../plugins/xt_klarna_kp/klarna.php?page=register\',\'Jetzt bei Klarna anmelden\');	
}
function openKlarnaStatusPage() {
    	 addITab(\'../plugins/xt_klarna_kp/klarna.php\',\'Klarna\');	
}
    		

function showCapturesForTracking(xt_orders_id, tracking_id)
{
    var tracking_id = tracking_id; // TODO auf array umbauen, wenn nötig?
    '.$js_capturesWnd.'
}
</script>
<script type="text/javascript" src="../plugins/xt_klarna_kp/javascript/klarna_kp_admin.js?'.time().'"></script>
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
    addTab('adminHandler.php?load_section=order&pg=overview&parentNode=node_order&sec=".$_SESSION['admin_user']['admin_key']."','Bestellungen');
    //addTab('order_edit.php?pg=overview&parentNode=node_order&sec=f482e0c86d54a4713215e235a2ddaff0&edit_id=126&gridHandle=ordergridForm&sec=".$_SESSION['admin_user']['admin_key']."','Bestellung 126');
    contentTabs.setActiveTab(0);
    //addTab('adminHandler.php?load_section=product&pg=overview&parentNode=node_product&sec=df37301a6fe1b75dd5b043eaaff7b728&edit_id=5&gridHandle=productgridForm&sec=df37301a6fe1b75dd5b043eaaff7b728','artikel ms id5');
    //trees[0].collapse();trees[1].expandAll();trees[1].expand();
    }, 500 );
})
</script>
<style>
    div[name=_KLARNA_CONFIG_KP_PAYMENT_METHODS] { width:500px !important}
    div[name=_KLARNA_CONFIG_KP_B2B_GROUPS] { width:500px !important}
    #ordergridForm .x-grid3-row:last-of-type { padding-bottom: 22px; }
</style>
";

