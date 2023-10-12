<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

$rowActions[] = array('iconCls' => 'products_cross_selling', 'qtipIndex' => 'qtip1', 'tooltip' => TEXT_PRODUCTS_CROSS_SELLING);
if ($this->url_data['edit_id'])
    $js = "var edit_id = ".$this->url_data['edit_id'].";";
else
    $js = "var edit_id = record.id;";
$extF = new ExtFunctions();
$js.= $extF->_RemoteWindow("TEXT_PRODUCTS_CROSS_SELLING","TEXT_PRODUCTS","adminHandler.php?plugin=xt_cross_selling&load_section=cross_selling_products&noFilter=true&pg=overview&products_id='+edit_id+'", '', array(), 800, 600).' new_window.show();';

$rowActionsFunctions['products_cross_selling'] = $js;
