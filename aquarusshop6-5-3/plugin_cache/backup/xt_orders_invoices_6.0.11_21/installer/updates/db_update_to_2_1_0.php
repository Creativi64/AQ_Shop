<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

include_once _SRV_WEBROOT . _SRV_WEB_PLUGINS . 'xt_orders_invoices/classes/constants.php';
require_once _SRV_WEBROOT . 'xtFramework/admin/classes/class.adminDB_DataSave.php';

if(!$this->_FieldExists(COL_PRINT_BUTTONS_CODE,TABLE_PRINT_BUTTONS))
{
$db->Execute("ALTER TABLE `".TABLE_PRINT_BUTTONS."` ADD COLUMN `".COL_PRINT_BUTTONS_CODE."` varchar(64) ;");
}
   
$tpl = $db->GetArray("SELECT * FROM ".TABLE_PDF_MANAGER." WHERE template_type='invoice'");
if($tpl && is_array($tpl))
{
    // duplicate pdf manager
    $old_id = $tpl[0]['template_id'];
    unset($tpl[0]['template_id']);
    $tpl[0]['template_name'] .= ' backup';
    $tpl[0]['template_type'] .= '_backup';
    $o = new adminDB_DataSave(TABLE_PDF_MANAGER, $tpl[0], false, false);
    $result = $o->saveDataSet();
  
    // duplicate pdf manager content
    $tplId = $result->new_id;
    if($result->success && $tplId)
    {
        $tpl = $db->GetArray("SELECT * FROM ".TABLE_PDF_MANAGER_CONTENT." WHERE template_id=? AND language_code='de'", array($old_id));
        if($tpl && is_array($tpl))
        {
            $tpl[0]['template_id'] = $tplId;
            $o = new adminDB_DataSave(TABLE_PDF_MANAGER_CONTENT, $tpl[0], false, false);
            $result = $o->saveDataSet();
        }
        $tpl = $db->GetArray("SELECT * FROM ".TABLE_PDF_MANAGER_CONTENT." WHERE template_id=? AND language_code='en'", array($old_id));
        if($tpl && is_array($tpl))
        {
            $tpl[0]['template_id'] = $tplId;
            $o = new adminDB_DataSave(TABLE_PDF_MANAGER_CONTENT, $tpl[0], false, false);
            $result = $o->saveDataSet();
        }

        $tpl = file_get_contents(_SRV_WEBROOT._SRV_WEB_PLUGINS.'xt_orders_invoices/installer/tpl/tpl_invoice.html', true);
        if(empty($tpl)) $tpl = "";
        $db->Execute("UPDATE ".TABLE_PDF_MANAGER_CONTENT." SET template_body=? WHERE template_id=?", array($tpl, $old_id));
    }
}