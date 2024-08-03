<?php


defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $current_category_id, $page;

if (XT_MASTER_SLAVE_ACTIVE == '1')
{
    $check_pos = strpos($this->position,'plugin_ms_')===0;
    $check_pos_l = strpos($this->position,'product_listing')===0;
    $check_pos_s = strstr($this->position, 'getSearchData');

    /*
    if (!$check_pos_l && !$check_pos && !$check_pos_s && USER_POSITION != 'admin')
    {
        if (XT_MASTER_SLAVE_SHOP_SEARCH=='master')
            $this->setSQL_WHERE(" and ((p.products_master_flag = 1) || ((p.products_master_flag is NULL || p.products_master_flag=0) && (p.products_master_model is NULL || p.products_master_model='') )) ");
        else if (XT_MASTER_SLAVE_SHOP_SEARCH=='slave')
            $this->setSQL_WHERE(" and (p.products_master_flag = '' ||  p.products_master_flag is NULL)");
    }
    */

    if ($check_pos_l)
    {
        $this->setSQL_WHERE(" and ((p.products_master_flag = 1) || ((p.products_master_flag is NULL || p.products_master_flag=0) && (p.products_master_model is NULL || p.products_master_model='') )) ");
    }

}
