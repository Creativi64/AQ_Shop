<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

if(empty($data_array['google_product_cat']) || $data_array['google_product_cat']==NULL || $data_array['google_product_cat']=='New'){
    
    //get standard categorie for multishop
    $c_rs=$db->Execute("SELECT config_value FROM ".TABLE_PLUGIN_CONFIGURATION." WHERE shop_id = '".$this->data['feed_store_id']."' and config_key ='XT_GOOGLE_PRODUCT_CATEGORIES_CAT'");
    if ($c_rs->RecordCount()==1) {
        $google_productcategory = $c_rs->fields['config_value'];
    }
    else{
        $google_productcategory = XT_GOOGLE_PRODUCT_CATEGORIES_CAT;
    }
    
    if ($rs->RecordCount()==1) {
        $g_rs=$db->Execute("SELECT google_product_cat FROM ".TABLE_CATEGORIES." WHERE google_product_cat <> 'New' AND google_product_cat <> '' and google_product_cat is not NULL and categories_id ='".$rs->fields['categories_id']."'");
        if ($g_rs->RecordCount()==1) {
            $data_array['google_productcategory'] = $g_rs->fields['google_product_cat'];
        }
        else{
            $data_array['google_productcategory'] = $google_productcategory;
        }
    } 
    else{
        $data_array['google_productcategory'] = $google_productcategory;
    }  
}
else{
    $data_array['google_productcategory'] = $data_array['google_product_cat'];
}
