<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

$rowActions[] = array('iconCls' => 'xt_payment_restriction', 'qtipIndex' => 'qtip1', 'tooltip' => TEXT_PAYMENT_RESTRICTION);
if ($this->url_data['edit_id'])
{
    $js = "var edit_id = ".$this->url_data['edit_id'].";";
    $js .= "var edit_name = " . $this->url_data['edit_id'] . ";";
}
else
{
    $js = "var edit_id = record.id;";
    $js .= "var edit_name = record.id;";
}
$js .= "addTab('adminHandler.php?plugin=xt_payment_restriction&load_section=payment_restriction&pg=overview&customers_status_id='+edit_id,'".TEXT_PAYMENT_RESTRICTION." '+edit_name,'paymentRestrictions_'+edit_id)";
$rowActionsFunctions['xt_payment_restriction'] = $js;

