<?php
/* Smarty version 5.1.0, created on 2024-10-14 17:40:11
  from 'file:/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/boxes/box_search.html' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.1.0',
  'unifunc' => 'content_670d3b5b21cd90_56254824',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '00f12844ba75410e32d32dc523c6acb89f449c00' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/boxes/box_search.html',
      1 => 1697144118,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_670d3b5b21cd90_56254824 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/boxes';
?><div class="box-search">
    <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('class'=>"search-box-form",'type'=>'form','name'=>'search_box','action'=>'search','link_params'=>'getParams','method'=>'get','conn'=>'SSL'), $_smarty_tpl);?>

        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'hidden','name'=>'page','value'=>'search'), $_smarty_tpl);?>

        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'hidden','name'=>'page_action','value'=>'query'), $_smarty_tpl);?>

        <?php ob_start();
if ((defined('SEARCH_BOX_SEARCH_IN_DESC') ? constant('SEARCH_BOX_SEARCH_IN_DESC') : null)) {
echo "on";
}
$_prefixVariable1=ob_get_clean();
echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'hidden','name'=>'desc','value'=>$_prefixVariable1), $_smarty_tpl);?>

        <?php ob_start();
if ((defined('SEARCH_BOX_SEARCH_IN_SDESC') ? constant('SEARCH_BOX_SEARCH_IN_SDESC') : null)) {
echo "on";
}
$_prefixVariable2=ob_get_clean();
echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'hidden','name'=>'sdesc','value'=>$_prefixVariable2), $_smarty_tpl);?>

        <div class="input-group">
            <input type="text" name="keywords" class="form-control keywords" value="<?php echo preg_replace('!<[^>]*?>!', ' ', (string) $_GET['keywords']);?>
" placeholder="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_ENTER_SEARCH_TERM), $_smarty_tpl);?>
" />
            <span class="input-group-btn">
                <button type="submit" class="submit-button btn btn-primary" title="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>BUTTON_SEARCH), $_smarty_tpl);?>
" data-toggle="tooltip" data-placement="auto">
                    <i class="fa fa-search"></i>
                    <span class="sr-only"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>BUTTON_SEARCH), $_smarty_tpl);?>
</span>
                </button>
            </span>
        </div>
    <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'formend'), $_smarty_tpl);?>

</div><?php }
}
