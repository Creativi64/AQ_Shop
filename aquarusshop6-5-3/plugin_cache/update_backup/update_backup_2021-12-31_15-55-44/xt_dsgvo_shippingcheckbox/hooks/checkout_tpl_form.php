<?php
defined('_VALID_CALL') or die('Direct Access is not allowed.');
$shipping_method = $_SESSION['selected_shipping'];
if ($shipping_method!='') {
    global $db;
    $rs = $db->Execute("SELECT * FROM ".TABLE_SHIPPING." WHERE shipping_code=? LIMIT 0,1",array($shipping_method));
    if ($rs->RecordCount()==1 && $rs->fields['dsgvo_shipping_status']=='1') {
        $tpl = 'dsgvo_checkbox.html';
        $template = new Template();
        $template->getTemplatePath($tpl, 'xt_dsgvo_shippingcheckbox', '', 'plugin');
        $tpl_data= array('required'=>$rs->fields['dsgvo_shipping_required_status']);
        $tmp_data = $template->getTemplate('xt_dsgvo_shippingcheckbox_smarty', $tpl, $tpl_data);
        echo $tmp_data;
    }
}