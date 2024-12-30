<?php
/* Smarty version 5.4.1, created on 2024-12-02 19:41:56
  from 'file:xtCore/pages/search.html' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.4.1',
  'unifunc' => 'content_674dff7423feb1_24371478',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '0081255e31b6aba5011d34749675d3c0b31b96dc' => 
    array (
      0 => 'xtCore/pages/search.html',
      1 => 1697144119,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_674dff7423feb1_24371478 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages';
?><div id="search">
    <?php if ((null !== ($_GET['keywords'] ?? null))) {?>
        <h1><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>'text_search'), $_smarty_tpl);?>
 <small><?php echo $_smarty_tpl->getSmarty()->getModifierCallback('truncate')(preg_replace('!<[^>]*?>!', ' ', (string) $_GET['keywords']),140,'...');?>
</small></h1>
        <?php echo $_smarty_tpl->getValue('message');?>

    <?php } else { ?>
        <h1><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>'text_search'), $_smarty_tpl);?>
</h1>
    <?php }?>
    <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('class'=>"search-box-form",'type'=>'form','name'=>'search','action'=>'dynamic','link_params'=>'getParams','method'=>'get'), $_smarty_tpl);?>

    <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'hidden','name'=>'page','value'=>'search'), $_smarty_tpl);?>

    <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>'search_tpl'), $_smarty_tpl);?>

    <div class="form-group">
        <div class="input-group input-group-lg">
            <input class="form-control keywords" type="text" id="keywords" name="keywords" placeholder="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_ENTER_SEARCH_TERM), $_smarty_tpl);?>
" value="<?php echo preg_replace('!<[^>]*?>!', ' ', (string) $_GET['keywords']);?>
" />
            <div class="input-group-btn">
                <button type="submit" class="submit-button btn btn-primary">
                    <i class="fa fa-search"></i>
                    <span class="sr-only"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>BUTTON_SEARCH), $_smarty_tpl);?>
</span>
                </button>
            </div>
        </div>
        <?php if (empty($_GET['advancedSearch'])) {?>
        <p class="help-block text-right"><a href="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('link')->handle(array('page'=>'search'), $_smarty_tpl);?>
?advancedSearch=1&" onclick="href = href + 'keywords='+$('#keywords').val();"><i class="fa fa-chevron-circle-right"></i> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_ADVANCED_SEARCH), $_smarty_tpl);?>
</a></p>
        <?php }?>
    </div>
    <?php if (!$_smarty_tpl->getValue('included')) {?>
        <?php if ($_smarty_tpl->getValue('use_stock_check')) {?>
        <div class="form-group">
            <?php if ($_smarty_tpl->getValue('stock_check')) {?>
            <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('id'=>'stock_check','type'=>'checkbox','name'=>'stock_check','checked'=>true), $_smarty_tpl);?>

            <?php } else { ?>
            <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('id'=>'stock_check','type'=>'checkbox','name'=>'stock_check'), $_smarty_tpl);?>

            <?php }?>
            <label for="stock_check"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_ONLY_PRODUCT_WITH_STOCK), $_smarty_tpl);?>
</label>
        </div>
    <?php }?>
        <div class="form-group">
            <?php if ($_smarty_tpl->getValue('checked_desc') == 'checked') {?>
                <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('id'=>'desc','type'=>'checkbox','name'=>'desc','checked'=>$_smarty_tpl->getValue('checked_desc')), $_smarty_tpl);?>

            <?php } else { ?>
                <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('id'=>'desc','type'=>'checkbox','name'=>'desc'), $_smarty_tpl);?>

            <?php }?>
            <label for="desc"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>'text_search_desc'), $_smarty_tpl);?>
</label>
        </div>
        <div class="form-group">
            <?php if ($_smarty_tpl->getValue('checked_sdesc') == 'checked') {?>
                <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('id'=>'sdesc','type'=>'checkbox','name'=>'sdesc','checked'=>$_smarty_tpl->getValue('checked_sdesc')), $_smarty_tpl);?>

            <?php } else { ?>
                <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('id'=>'sdesc','type'=>'checkbox','name'=>'sdesc'), $_smarty_tpl);?>

            <?php }?>
            <label for="sdesc"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>'text_search_sdesc'), $_smarty_tpl);?>
</label>
        </div>
        <?php if ($_smarty_tpl->getValue('cat_data')) {?>
            <div class="well">
                <div class="form-group">
                    <label for="cat"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>'text_search_categories'), $_smarty_tpl);?>
</label>
                    <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('class'=>"form-control",'params'=>'id="cat"','type'=>'select','name'=>'cat','default'=>$_smarty_tpl->getValue('default_cat'),'value'=>$_smarty_tpl->getValue('cat_data')), $_smarty_tpl);?>

                </div>
                <div class="form-group">
                    <label>
                        <?php if ($_smarty_tpl->getValue('checked_subcat') == 'checked') {?>
                        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('id'=>'subkat','type'=>'checkbox','name'=>'subkat','checked'=>$_smarty_tpl->getValue('checked_subcat')), $_smarty_tpl);?>
 <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>'text_search_subcategories'), $_smarty_tpl);?>

                        <?php } else { ?>
                        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('id'=>'subkat','type'=>'checkbox','name'=>'subkat'), $_smarty_tpl);?>
 <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>'text_search_subcategories'), $_smarty_tpl);?>

                        <?php }?>
                    </label>
                </div>
            </div>
        <?php }?>
        <?php if ($_smarty_tpl->getValue('mnf_data') && $_smarty_tpl->getSmarty()->getModifierCallback('count')($_smarty_tpl->getValue('mnf_data')) > 1) {?>
            <div class="well">
                <div class="form-group">
                    <label for="mnf"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>'text_search_mnf'), $_smarty_tpl);?>
</label>
                    <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('class'=>"form-control",'params'=>'id="mnf"','type'=>'select','name'=>'mnf','default'=>$_smarty_tpl->getValue('default_mnf'),'value'=>$_smarty_tpl->getValue('mnf_data')), $_smarty_tpl);?>

                </div>
            </div>
        <?php }?>
        <div class="form-submit text-right">
            <a href="javascript:history.back();" class="button"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>BUTTON_BACK), $_smarty_tpl);?>
</a>
            <button type="submit" class="btn btn-primary">
                <span class="glyphicon glyphicon-search"></span>
                <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>BUTTON_SEARCH), $_smarty_tpl);?>

            </button>
        </div>
    <?php }?>
    <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'formend'), $_smarty_tpl);?>

</div>

<?php }
}
