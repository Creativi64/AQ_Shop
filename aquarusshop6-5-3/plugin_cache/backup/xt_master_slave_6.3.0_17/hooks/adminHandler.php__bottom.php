<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

if($_REQUEST['load_section']=='product' && $_REQUEST['pg']=='overview' && $_REQUEST['edit_id'])
{
    global $db, $productGetParamsIsMainProduct, $productGetParamsIsVariant;
    $add_to_url = (isset($_SESSION['admin_user']['admin_key']))? ",sec:'".$_SESSION['admin_user']['admin_key']."'": "";
    $ms_js = '';
    if(!isset($productGetParamsIsMainProduct)) $productGetParamsIsMainProduct = $db->GetOne("SELECT 1 FROM ".TABLE_PRODUCTS." WHERE products_id=? AND products_master_flag=1", $_REQUEST['edit_id']) ? true : false;
    if(!isset($productGetParamsIsVariant)) $productGetParamsIsVariant = $db->GetOne("SELECT 1 FROM ".TABLE_PRODUCTS." WHERE products_id=? AND products_master_model IS NOT NULL AND products_master_model >''",  $_REQUEST['edit_id']) ? true : false;
    if($productGetParamsIsMainProduct)
    {
        $ms_js .= "
             var elem =  $('#product" . $form_grid->SelectionItem . "-grideditform.x-panel input[name=products_master_flag]')
             toogleMsMasterAttributes(elem.closest('form'), true);
            ";
    }
    else {
        $ms_js .= "
             var elem =  $('#product" . $form_grid->SelectionItem . "-grideditform.x-panel input[name=products_master_flag]')
             toogleMsMasterAttributes(elem.closest('form'), false);
            ";
    }
    if($productGetParamsIsVariant)
    {
        $ms_js .= "
             var elem =  $('#product" . $form_grid->SelectionItem . "-grideditform.x-panel input[name=products_master_flag]')
             toogleMsSlaveAttributes(elem.closest('form'), true);
            ";
    }
    else if(!$productGetParamsIsMainProduct){
        $ms_js .= "
             var elem =  $('#product" . $form_grid->SelectionItem . "-grideditform.x-panel input[name=products_master_flag]')
             toogleMsSlaveAttributes(elem.closest('form'), false);
            ";
    }

    echo '<script type="text/javascript">

            function apply_variantProduct_price_confirm(products_id)
            {
                Ext.MessageBox.confirm("", "'.__define('TEXT_APPLY_VARIANTPRODUCT_PRICE').'",function(btn){ if(btn == "yes") {apply_variantProduct_price(products_id);} });
            }
            
            function apply_variantProduct_price(products_id)
            {
                Ext.Ajax.request({
                    url: "adminHandler.php",
                    method: "POST",
                    params: { load_section:"xt_master_slave_functions", pg:"apply_variantProduct_price" ,products_id:products_id,sec:"'.$add_to_url.'"},
                    success: function(){
                        Ext.MessageBox.alert("Message", "Erfolg");
                    },
                    failure: function(){
                        Ext.MessageBox.alert("Message", "Kein Erfolg");
                    }

                });
            }
            
            setTimeout(function(){
            init_ms_form("product' . $form_grid->SelectionItem . '-grideditform");
            '.$ms_js.'
            }, 500);
 </script>
';
}
else if($_REQUEST['load_section']=='product' && $_REQUEST['pg']=='overview' && $form_grid->new)
{
    $ms_js .= "
             var elem =  $('#product" . $form_grid->SelectionItem . "-grideditform.x-panel input[name=products_master_flag]')
             toogleMsSlaveAttributes(elem.closest('form'), false);
             toogleMsMasterAttributes(elem.closest('form'), false);
            ";
    echo '<script type="text/javascript">
            setTimeout(function(){
            init_ms_form("product' . $form_grid->SelectionItem . '-grideditform");
            '.$ms_js.'
            }, 500);
 </script>
';
}
