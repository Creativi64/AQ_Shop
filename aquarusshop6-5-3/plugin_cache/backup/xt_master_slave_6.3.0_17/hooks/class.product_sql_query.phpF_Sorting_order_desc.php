<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

$check_pos = strpos($this->position,'plugin_ms_')===0;
$check_pos_s = strstr($this->position, 'getSearchData');
if (XT_MASTER_SLAVE_ACTIVE == 1)
{
//
    if (!$check_pos && !$check_pos_s && USER_POSITION != 'admin')
    {
        $this->setSQL_COLS(', p.products_model AS model ');
        $this->setSQL_COLS(", CASE
							WHEN p.products_master_flag=1 THEN (SELECT SUM(ps.products_ordered)FROM " . TABLE_PRODUCTS . " ps WHERE ps.products_master_model=model)
							ELSE p.products_ordered
						END AS sort_ordered ");

    }
}
