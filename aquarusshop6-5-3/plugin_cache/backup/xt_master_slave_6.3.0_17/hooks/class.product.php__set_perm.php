<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

if ($data['products_master_flag'] == '1' && $data['products_model_old']!='') {
    if ($data['products_model'] != $data['products_model_old']) {
        $ms_data = array('products_master_model' => $data['products_model']);
        $db->AutoExecute($this->_table, $ms_data, 'UPDATE', "products_master_model=" . $db->Quote($data['products_model_old']));
    }
}
