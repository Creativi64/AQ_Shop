<?php
/* Smarty version 4.3.0, created on 2023-10-08 01:15:17
  from '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/templates/xt_responsive_AQ/xtCore/boxes/box_search.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.0',
  'unifunc' => 'content_6521e685879c65_82130808',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'c4d065bf1dcf6df4ae43fd15e65d05e95779f8f9' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/templates/xt_responsive_AQ/xtCore/boxes/box_search.html',
      1 => 1691797588,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_6521e685879c65_82130808 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/smarty/xt_plugins/function.form.php','function'=>'smarty_function_form',),1=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/smarty/xt_plugins/function.txt.php','function'=>'smarty_function_txt',),));
?>
<div class="box-search">
    <?php echo smarty_function_form(array('class'=>"search-box-form",'type'=>'form','name'=>'search_box','action'=>'search','link_params'=>'getParams','method'=>'get','conn'=>'SSL'),$_smarty_tpl);?>

        <?php echo smarty_function_form(array('type'=>'hidden','name'=>'page','value'=>'search'),$_smarty_tpl);?>

        <?php echo smarty_function_form(array('type'=>'hidden','name'=>'page_action','value'=>'query'),$_smarty_tpl);?>

        <?php ob_start();
if ((defined('SEARCH_BOX_SEARCH_IN_DESC') ? constant('SEARCH_BOX_SEARCH_IN_DESC') : null)) {
echo "on";
}
$_prefixVariable1=ob_get_clean();
echo smarty_function_form(array('type'=>'hidden','name'=>'desc','value'=>$_prefixVariable1),$_smarty_tpl);?>

        <?php ob_start();
if ((defined('SEARCH_BOX_SEARCH_IN_SDESC') ? constant('SEARCH_BOX_SEARCH_IN_SDESC') : null)) {
echo "on";
}
$_prefixVariable2=ob_get_clean();
echo smarty_function_form(array('type'=>'hidden','name'=>'sdesc','value'=>$_prefixVariable2),$_smarty_tpl);?>

        <div class="input-group">
            <input type="text" name="keywords" class="form-control keywords" value="<?php echo preg_replace('!<[^>]*?>!', ' ', $_GET['keywords'] ?: '');?>
" placeholder="<?php echo smarty_function_txt(array('key'=>TEXT_ENTER_SEARCH_TERM),$_smarty_tpl);?>
" />
            <span class="input-group-btn">
                <button type="submit" class="submit-button btn btn-primary" title="<?php echo smarty_function_txt(array('key'=>BUTTON_SEARCH),$_smarty_tpl);?>
" data-toggle="tooltip" data-placement="auto">
                    <i class="fa fa-search"></i>
                    <span class="sr-only"><?php echo smarty_function_txt(array('key'=>BUTTON_SEARCH),$_smarty_tpl);?>
</span>
                </button>
            </span>
        </div>
    <?php echo smarty_function_form(array('type'=>'formend'),$_smarty_tpl);?>

</div><?php }
}
