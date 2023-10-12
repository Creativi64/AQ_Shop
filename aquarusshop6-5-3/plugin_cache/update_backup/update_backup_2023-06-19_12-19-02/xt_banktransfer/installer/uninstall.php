<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

$db->Execute("DROP TABLE IF EXISTS ".DB_PREFIX."_plg_customer_bankaccount");
$emailTpls = $db->GetAll("SELECT `tpl_id` FROM ". TABLE_MAIL_TEMPLATES ." WHERE `tpl_type` LIKE 'sepa_mandat' ");
foreach($emailTpls as $tpl)
{
    $db->Execute("DELETE FROM ".TABLE_MAIL_TEMPLATES_CONTENT." WHERE `tpl_id` =? ",array($tpl['tpl_id']));
    $db->Execute("DELETE FROM ".TABLE_MAIL_TEMPLATES." WHERE `tpl_id` =? ",array($tpl['tpl_id']));
}
