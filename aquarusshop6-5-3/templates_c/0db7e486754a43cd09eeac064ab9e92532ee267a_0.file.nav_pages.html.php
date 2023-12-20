<?php
/* Smarty version 4.3.2, created on 2023-12-20 20:51:52
  from '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/templates/xt_responsive_AQ/xtCore/pages/navigation/nav_pages.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_658345d8b32917_95031921',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '0db7e486754a43cd09eeac064ab9e92532ee267a' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/templates/xt_responsive_AQ/xtCore/pages/navigation/nav_pages.html',
      1 => 1697144245,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_658345d8b32917_95031921 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/vendor/smarty/smarty/libs/plugins/modifier.count.php','function'=>'smarty_modifier_count',),1=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/smarty/xt_plugins/function.txt.php','function'=>'smarty_function_txt',),));
?>
<div class="nav-pages btn-group">
    <?php if ($_smarty_tpl->tpl_vars['pages']->value && smarty_modifier_count($_smarty_tpl->tpl_vars['pages']->value) > 1) {?>
        <?php $_smarty_tpl->_assignInScope('padding', 2);?>

        <?php if ($_smarty_tpl->tpl_vars['actual_page']->value == 1) {?>
            <button class="btn btn-default prev" disabled="disabled" type="button">
                <i class="fa fa-chevron-left"></i>
                <span class="sr-only">Prev</span>
            </button>
        <?php } else { ?>
            <a class="btn btn-default prev" role="button" href="<?php echo $_smarty_tpl->tpl_vars['prev']->value;?>
" rel="prev">
                <i class="fa fa-chevron-left"></i>
                <span class="sr-only">Prev</span>
            </a>
        <?php }?>

        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['pages']->value, 'page_link', false, 'pager_number');
$_smarty_tpl->tpl_vars['page_link']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['pager_number']->value => $_smarty_tpl->tpl_vars['page_link']->value) {
$_smarty_tpl->tpl_vars['page_link']->do_else = false;
?>        
            <?php if ($_smarty_tpl->tpl_vars['pager_number']->value == 1) {?>
                            <a class="btn btn-default<?php if ($_smarty_tpl->tpl_vars['pager_number']->value == $_smarty_tpl->tpl_vars['actual_page']->value) {?> active<?php }?>" role="button" href="<?php echo $_smarty_tpl->tpl_vars['page_link']->value;?>
"<?php if ($_smarty_tpl->tpl_vars['pager_number']->value == $_smarty_tpl->tpl_vars['actual_page']->value-1) {?> rel="prev"<?php }?>><?php echo $_smarty_tpl->tpl_vars['pager_number']->value;?>
</a>
            <?php }?>           
            <?php if ($_smarty_tpl->tpl_vars['actual_page']->value-$_smarty_tpl->tpl_vars['padding']->value == $_smarty_tpl->tpl_vars['pager_number']->value && $_smarty_tpl->tpl_vars['actual_page']->value-$_smarty_tpl->tpl_vars['padding']->value > 1 && $_smarty_tpl->tpl_vars['actual_page']->value-$_smarty_tpl->tpl_vars['padding']->value-1 > 1) {?>
                                        <button class="btn btn-default dots" disabled="disabled" type="button">...</button>
            <?php }?>
            <?php if ($_smarty_tpl->tpl_vars['pager_number']->value != 1 && $_smarty_tpl->tpl_vars['pager_number']->value != $_smarty_tpl->tpl_vars['last_page']->value && $_smarty_tpl->tpl_vars['pager_number']->value >= $_smarty_tpl->tpl_vars['actual_page']->value-$_smarty_tpl->tpl_vars['padding']->value && $_smarty_tpl->tpl_vars['pager_number']->value <= $_smarty_tpl->tpl_vars['actual_page']->value+$_smarty_tpl->tpl_vars['padding']->value) {?>
                <a class="btn btn-default<?php if ($_smarty_tpl->tpl_vars['pager_number']->value == $_smarty_tpl->tpl_vars['actual_page']->value) {?> active<?php }?>" role="button" href="<?php echo $_smarty_tpl->tpl_vars['page_link']->value;?>
"<?php if ($_smarty_tpl->tpl_vars['pager_number']->value == $_smarty_tpl->tpl_vars['actual_page']->value-1) {?> rel="prev"<?php } elseif ($_smarty_tpl->tpl_vars['pager_number']->value == $_smarty_tpl->tpl_vars['actual_page']->value+1) {?> rel="next"<?php }?>><?php echo $_smarty_tpl->tpl_vars['pager_number']->value;?>
</a>
            <?php }?>         
            <?php if ($_smarty_tpl->tpl_vars['actual_page']->value+$_smarty_tpl->tpl_vars['padding']->value == $_smarty_tpl->tpl_vars['pager_number']->value && $_smarty_tpl->tpl_vars['actual_page']->value+$_smarty_tpl->tpl_vars['padding']->value < $_smarty_tpl->tpl_vars['last_page']->value && $_smarty_tpl->tpl_vars['actual_page']->value+$_smarty_tpl->tpl_vars['padding']->value+1 < $_smarty_tpl->tpl_vars['last_page']->value) {?>
                            <button class="btn btn-default dots" disabled="disabled" type="button">...</button>
            <?php }?>        
            <?php if ($_smarty_tpl->tpl_vars['pager_number']->value == $_smarty_tpl->tpl_vars['last_page']->value) {?>
            <a class="btn btn-default<?php if ($_smarty_tpl->tpl_vars['pager_number']->value == $_smarty_tpl->tpl_vars['actual_page']->value) {?> active<?php }?>" role="button" href="<?php echo $_smarty_tpl->tpl_vars['page_link']->value;?>
"<?php if ($_smarty_tpl->tpl_vars['pager_number']->value == $_smarty_tpl->tpl_vars['actual_page']->value+1) {?> rel="next"<?php }?>><?php echo $_smarty_tpl->tpl_vars['pager_number']->value;?>
</a>
<?php }?>
        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>

        <?php if ($_smarty_tpl->tpl_vars['actual_page']->value == $_smarty_tpl->tpl_vars['last_page']->value) {?>
            <button class="btn btn-default next" disabled="disabled" type="button">
                <i class="fa fa-chevron-right"></i>
                <span class="sr-only">Next</span>
            </button>
        <?php } else { ?>
            <a class="btn btn-default next" role="button" href="<?php echo $_smarty_tpl->tpl_vars['next']->value;?>
" rel="next">
                <i class="fa fa-chevron-right"></i>
                <span class="sr-only">Next</span>
            </a>
        <?php }?>
    <?php } else { ?>
        <button class="btn btn-default one-page" disabled="disabled" type="button"><?php echo smarty_function_txt(array('key'=>TEXT_PAGINATION_TITLE),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['actual_page']->value;?>
 <?php echo smarty_function_txt(array('key'=>TEXT_PAGINATION_FROM),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['last_page']->value;?>
</button>
    <?php }?>
</div>
<?php }
}
