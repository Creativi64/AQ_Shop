<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $db;

if (isset($ID) && $ID) {
    $data[0]['products_model_old'] = $data[0]['products_model'];
}
else if($this->url_data["get_data"] == 'true')
{
    foreach($data as $k => $p)
    {
        $variant_product_id = '';
        if($p['products_master_flag' != '0']) $variant_product_id = $p['products_id'];
        else if(!empty($p['products_master_model']))
        {
            $variant_product_id = $db->GetOne("SELECT IFNULL( (SELECT p2.products_id FROM ".TABLE_PRODUCTS." p1
                INNER JOIN ".TABLE_PRODUCTS." p2 ON p2.products_model = p1.products_master_model
                WHERE p1.products_master_model > '' AND p1.products_id = ?), 0) ", [$p['products_id']]);
        }
        $data[$k]['variant_product_id_meta_hidden'] = $variant_product_id;
    }
}
