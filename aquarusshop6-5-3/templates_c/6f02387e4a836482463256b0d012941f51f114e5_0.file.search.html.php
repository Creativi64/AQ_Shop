<?php
/* Smarty version 4.3.2, created on 2024-05-08 20:07:28
  from '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages/search.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_663bbf60b259e1_07033163',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '6f02387e4a836482463256b0d012941f51f114e5' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages/search.html',
      1 => 1697144119,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_663bbf60b259e1_07033163 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.txt.php','function'=>'smarty_function_txt',),1=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/vendor/smarty/smarty/libs/plugins/modifier.truncate.php','function'=>'smarty_modifier_truncate',),2=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.form.php','function'=>'smarty_function_form',),3=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.hook.php','function'=>'smarty_function_hook',),4=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.link.php','function'=>'smarty_function_link',),5=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/vendor/smarty/smarty/libs/plugins/modifier.count.php','function'=>'smarty_modifier_count',),));
?>
<div id="search">
    <?php if ((isset($_GET['keywords']))) {?>
        <h1><?php echo smarty_function_txt(array('key'=>'text_search'),$_smarty_tpl);?>
 <small><?php echo smarty_modifier_truncate(preg_replace('!<[^>]*?>!', ' ', $_GET['keywords'] ?: ''),140,'...');?>
</small></h1>
        <?php echo $_smarty_tpl->tpl_vars['message']->value;?>

    <?php } else { ?>
        <h1><?php echo smarty_function_txt(array('key'=>'text_search'),$_smarty_tpl);?>
</h1>
    <?php }?>
    <?php echo smarty_function_form(array('class'=>"search-box-form",'type'=>'form','name'=>'search','action'=>'dynamic','link_params'=>'getParams','method'=>'get'),$_smarty_tpl);?>

    <?php echo smarty_function_form(array('type'=>'hidden','name'=>'page','value'=>'search'),$_smarty_tpl);?>

    <?php echo smarty_function_hook(array('key'=>'search_tpl'),$_smarty_tpl);?>

    <div class="form-group">
        <div class="input-group input-group-lg">
            <input class="form-control keywords" type="text" id="keywords" name="keywords" placeholder="<?php echo smarty_function_txt(array('key'=>TEXT_ENTER_SEARCH_TERM),$_smarty_tpl);?>
" value="<?php echo preg_replace('!<[^>]*?>!', ' ', $_GET['keywords'] ?: '');?>
" />
            <div class="input-group-btn">
                <button type="submit" class="submit-button btn btn-primary">
                    <i class="fa fa-search"></i>
                    <span class="sr-only"><?php echo smarty_function_txt(array('key'=>BUTTON_SEARCH),$_smarty_tpl);?>
</span>
                </button>
            </div>
        </div>
        <?php if (empty($_GET['advancedSearch'])) {?>
        <p class="help-block text-right"><a href="<?php echo smarty_function_link(array('page'=>'search'),$_smarty_tpl);?>
?advancedSearch=1&" onclick="href = href + 'keywords='+$('#keywords').val();"><i class="fa fa-chevron-circle-right"></i> <?php echo smarty_function_txt(array('key'=>TEXT_ADVANCED_SEARCH),$_smarty_tpl);?>
</a></p>
        <?php }?>
    </div>
    <?php if (!$_smarty_tpl->tpl_vars['included']->value) {?>
        <?php if ($_smarty_tpl->tpl_vars['use_stock_check']->value) {?>
        <div class="form-group">
            <?php if ($_smarty_tpl->tpl_vars['stock_check']->value) {?>
            <?php echo smarty_function_form(array('id'=>'stock_check','type'=>'checkbox','name'=>'stock_check','checked'=>true),$_smarty_tpl);?>

            <?php } else { ?>
            <?php echo smarty_function_form(array('id'=>'stock_check','type'=>'checkbox','name'=>'stock_check'),$_smarty_tpl);?>

            <?php }?>
            <label for="stock_check"><?php echo smarty_function_txt(array('key'=>TEXT_ONLY_PRODUCT_WITH_STOCK),$_smarty_tpl);?>
</label>
        </div>
    <?php }?>
        <div class="form-group">
            <?php if ($_smarty_tpl->tpl_vars['checked_desc']->value == 'checked') {?>
                <?php echo smarty_function_form(array('id'=>'desc','type'=>'checkbox','name'=>'desc','checked'=>$_smarty_tpl->tpl_vars['checked_desc']->value),$_smarty_tpl);?>

            <?php } else { ?>
                <?php echo smarty_function_form(array('id'=>'desc','type'=>'checkbox','name'=>'desc'),$_smarty_tpl);?>

            <?php }?>
            <label for="desc"><?php echo smarty_function_txt(array('key'=>'text_search_desc'),$_smarty_tpl);?>
</label>
        </div>
        <div class="form-group">
            <?php if ($_smarty_tpl->tpl_vars['checked_sdesc']->value == 'checked') {?>
                <?php echo smarty_function_form(array('id'=>'sdesc','type'=>'checkbox','name'=>'sdesc','checked'=>$_smarty_tpl->tpl_vars['checked_sdesc']->value),$_smarty_tpl);?>

            <?php } else { ?>
                <?php echo smarty_function_form(array('id'=>'sdesc','type'=>'checkbox','name'=>'sdesc'),$_smarty_tpl);?>

            <?php }?>
            <label for="sdesc"><?php echo smarty_function_txt(array('key'=>'text_search_sdesc'),$_smarty_tpl);?>
</label>
        </div>
        <?php if ($_smarty_tpl->tpl_vars['cat_data']->value) {?>
            <div class="well">
                <div class="form-group">
                    <label for="cat"><?php echo smarty_function_txt(array('key'=>'text_search_categories'),$_smarty_tpl);?>
</label>
                    <?php echo smarty_function_form(array('class'=>"form-control",'params'=>'id="cat"','type'=>'select','name'=>'cat','default'=>$_smarty_tpl->tpl_vars['default_cat']->value,'value'=>$_smarty_tpl->tpl_vars['cat_data']->value),$_smarty_tpl);?>

                </div>
                <div class="form-group">
                    <label>
                        <?php if ($_smarty_tpl->tpl_vars['checked_subcat']->value == 'checked') {?>
                        <?php echo smarty_function_form(array('id'=>'subkat','type'=>'checkbox','name'=>'subkat','checked'=>$_smarty_tpl->tpl_vars['checked_subcat']->value),$_smarty_tpl);?>
 <?php echo smarty_function_txt(array('key'=>'text_search_subcategories'),$_smarty_tpl);?>

                        <?php } else { ?>
                        <?php echo smarty_function_form(array('id'=>'subkat','type'=>'checkbox','name'=>'subkat'),$_smarty_tpl);?>
 <?php echo smarty_function_txt(array('key'=>'text_search_subcategories'),$_smarty_tpl);?>

                        <?php }?>
                    </label>
                </div>
            </div>
        <?php }?>
        <?php if ($_smarty_tpl->tpl_vars['mnf_data']->value && smarty_modifier_count($_smarty_tpl->tpl_vars['mnf_data']->value) > 1) {?>
            <div class="well">
                <div class="form-group">
                    <label for="mnf"><?php echo smarty_function_txt(array('key'=>'text_search_mnf'),$_smarty_tpl);?>
</label>
                    <?php echo smarty_function_form(array('class'=>"form-control",'params'=>'id="mnf"','type'=>'select','name'=>'mnf','default'=>$_smarty_tpl->tpl_vars['default_mnf']->value,'value'=>$_smarty_tpl->tpl_vars['mnf_data']->value),$_smarty_tpl);?>

                </div>
            </div>
        <?php }?>
        <div class="form-submit text-right">
            <a href="javascript:history.back();" class="button"><?php echo smarty_function_txt(array('key'=>BUTTON_BACK),$_smarty_tpl);?>
</a>
            <button type="submit" class="btn btn-primary">
                <span class="glyphicon glyphicon-search"></span>
                <?php echo smarty_function_txt(array('key'=>BUTTON_SEARCH),$_smarty_tpl);?>

            </button>
        </div>
    <?php }?>
    <?php echo smarty_function_form(array('type'=>'formend'),$_smarty_tpl);?>

</div>

<?php }
}
