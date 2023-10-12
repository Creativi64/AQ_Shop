<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

$rowActions[] = array('iconCls' => 'products_to_attributes', 'qtipIndex' => 'qtip1', 'tooltip' => TEXT_PRODUCTS_TO_ATTRIBUTES);
if ($this->url_data['edit_id'])
    $js = "var edit_id = " . $this->url_data['edit_id'] . ";";
else
    $js = "var edit_id = record.id;";

$extF = new ExtFunctions();
$js .= $extF->_RemoteWindow("TEXT_PRODUCTS_TO_ATTRIBUTES", "TEXT_PRODUCTS", "adminHandler.php?plugin=xt_master_slave&load_section=product_to_attributes&pg=getTreePanel&products_id='+edit_id+'", '', array(), 800, 600) . ' new_window.show();';

$rowActionsFunctions['products_to_attributes'] = $js;
