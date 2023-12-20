<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $db;

/** @var $params array */

$header['variant_product_id_meta_hidden'] = ['type' => 'hidden'];
$header['products_master_model'] = ['type' => 'hidden'];
$header['products_master_flag'] = ['type' => 'hidden'];
if($this->url_data['pg']=='overview' && !$this->url_data['edit_id'] && $this->url_data['new'] != true){
    $params['include'] = array_merge($params['include'], ['products_master_model', 'products_master_flag', 'variant_product_id_meta_hidden']);
}

// ist das ein Hauptprodukt / Variantenprodukt ?
$is_variant_product = (bool) $db->GetOne("SELECT IFNULL( (SELECT 1 FROM ".TABLE_PRODUCTS." WHERE products_master_flag = 1 AND products_id = ?), 0) ", [$this->url_data['edit_id']]);
// oder ist das eine Produktvariante ? dabei id des Hauptprodukts ermitteln
$variant_product_id = $db->GetOne("SELECT IFNULL( (SELECT p2.products_id FROM ".TABLE_PRODUCTS." p1
        INNER JOIN ".TABLE_PRODUCTS." p2 ON p2.products_model = p1.products_master_model
        WHERE p2.products_master_model > '' AND p2.products_model > '' AND p1.products_id = ?), 0) ", [$this->url_data['edit_id']]);


if(!$is_variant_product)
{
    $params['rowActions'][] = array('iconCls' => 'products_to_attributes', 'qtipIndex' => 'qtip1', 'tooltip' => TEXT_PRODUCTS_TO_ATTRIBUTES);

    $extF = new ExtFunctions();
    $js .= $extF->_RemoteWindow("TEXT_PRODUCTS_TO_ATTRIBUTES", "TEXT_PRODUCTS", "adminHandler.php?plugin=xt_master_slave&load_section=product_to_attributes&pg=getTreePanel&products_id='+edit_id+'", '', array(), 800, 600) . ' new_window.show();';

    $params['rowActionsFunctions']['products_to_attributes'] = "
        //console.log('click button product 2 attributes', record);
        if(typeof record != 'undefined' && record.data.products_master_flag == '1')
        {
            Ext.Msg.alert('" . __define('TEXT_ERROR_MESSAGE') . "', '" . __define('TEXT_FUNCTION_NOT_ALLOWED_FOR') . " " . __define('TEXT_MAIN_PRODUCT') . "');
        }
        else {
            /*
            if(".($is_variant_product ? 'true' : 'false')." )
            {
                Ext.Msg.alert('" . __define('TEXT_ERROR_MESSAGE') . "', '" . __define('TEXT_FUNCTION_NOT_ALLOWED_FOR') . " " . __define('TEXT_MAIN_PRODUCT') . "');
                return;
            }
            */
            " . $js . "
        }";
}

$edit_id = $this->url_data['edit_id'];
if($variant_product_id) $edit_id = $variant_product_id;

$params['rowActions'][] = array('iconCls' => 'generate_slaves', 'qtipIndex' => 'qtip1', 'tooltip' => TEXT_MASTER_SLAVE_GENERATE_SLAVE);
if ($this->url_data['edit_id'])
    $sjs = "var edit_id = " . $edit_id . ";";
else
    $sjs = "var edit_id = record.id;";
$sjs .="var gh=Ext.getCmp('generate_slavesgridForm');if (gh) contentTabs.remove('node_generate_slaves');";
$sjs .= "addTab('adminHandler.php?type=generate_slaves&plugin=xt_master_slave&load_section=generate_slaves&pg=setStepOne&products_id='+edit_id+'&gridHandle=Step1','".TEXT_GENERATE_SLAVES_STEP_1."')";

$params['rowActionsFunctions']['generate_slaves'] = $sjs;

$params['rowActions'][] = array('iconCls' => 'generate_slaves_list', 'qtipIndex' => 'qtip1', 'tooltip' => TEXT_MASTER_SLAVE_GENERATE_SLAVE_LIST);
if ($this->url_data['edit_id'])
    $sjs = "var edit_id = " . $edit_id . ";";
else
    $sjs = "var edit_id = record.data.variant_product_id_meta_hidden > 0 ? record.data.variant_product_id_meta_hidden : record.id;";

$sjs .= "addTab('adminHandler.php?plugin=xt_master_slave&load_section=generated_slaves&pg=overview&products_id='+edit_id+'','".TEXT_MASTER_SLAVE_GENERATE_SLAVE_LIST." '+edit_id  , 'node_generated_slaves_'+edit_id )";

$params['rowActionsFunctions']['generate_slaves_list'] = $sjs;

if($is_variant_product && !empty($this->url_data['edit_id']))
{

    $params['rowActions'][] = array('iconCls' => 'apply_variantProduct_price', 'qtipIndex' => 'qtip1', 'tooltip' => __define('TEXT_APPLY_VARIANTPRODUCT_PRICE'));
    $js = "var edit_id = ".$this->url_data['edit_id'].";
        apply_variantProduct_price_confirm(edit_id);
    ";

    $params['rowActionsFunctions']['apply_variantProduct_price'] = $js;
}
