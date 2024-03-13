<?php
defined('_VALID_CALL') or die('Direct Access is not allowed.');


global $db, $current_product_id, $language, $p_info;

// TODO
//  Not sure what this hook is for.  fixing shop misconfiguration?

if (false && XT_MASTER_SLAVE_ACTIVE == '1' && $current_product_id)
{
    $sql = "SELECT products_id from ".TABLE_PRODUCTS." WHERE products_model = (SELECT products_master_model FROM ".TABLE_PRODUCTS." WHERE products_id=? and products_master_model!='')";
    $master_id = $db->GetOne($sql, array($current_product_id));

    if($master_id)
    {
        $master_product = product::getProduct(0);
        $master_product->pID = $master_id;

        $master_product->sql_products->a_sql_where = '1';
        $master_product = $master_product->getProductData('default', $language->content_language);

        if(empty($master_product))
        {
            if (_SYSTEM_MOD_REWRITE_404 == 'true') header("HTTP/1.0 404 Not Found");
            $tmp_link  = $xtLink->_link(array('page'=>'404'));
            $xtLink->_redirect($tmp_link);
        }
        else if($master_product && !$p_info->is_product )
        {
            $link_array = array('page'=> 'product', 'type'=>'product', 'name'=>$master_product['products_name'], 'id'=>$master_product['products_id'],'seo_url'=>$master_product['url_text']);
            $tmp_link  = $xtLink->_link($link_array);
            $xtLink->_redirect($tmp_link, xt_master_slave_functions::getRedirectCode());
        }


    }
}
