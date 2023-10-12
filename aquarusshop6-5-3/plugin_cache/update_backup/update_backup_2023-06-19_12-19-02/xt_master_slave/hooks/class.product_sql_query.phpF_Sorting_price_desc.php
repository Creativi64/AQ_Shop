<?php


defined('_VALID_CALL') or die('Direct Access is not allowed.');

$check_pos = strpos($this->position,'plugin_ms_')===0;
$check_pos_s = strstr($this->position, 'getSearchData');
if (XT_MASTER_SLAVE_ACTIVE == 1)
{
    if (!$check_pos && !$check_pos_s && USER_POSITION != 'admin')
    {
        $this->setSQL_COLS(', p.products_model AS model ');

        $sqlCols = ", CASE
                     WHEN p.products_master_flag=1 THEN (SELECT MIN(ps.products_price)FROM " . TABLE_PRODUCTS . " ps WHERE ps.products_master_model=model)
                     ";
        if (isset($xtPlugin->active_modules['xt_special_products']))
        {
            $sqlCols .= "WHEN pps.specials_price>0 THEN pps.specials_price
						";
        }

        $sqlCols .= "ELSE p.products_price
				END AS sort_price ";

        $this->setSQL_COLS($sqlCols);
    }
}else{
    $this->setSQL_COLS(', IF(pps.specials_price>0,pps.specials_price,p.products_price) as sort_price');
}
