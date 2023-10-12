<?php
/* Smarty version 4.3.0, created on 2023-10-08 01:15:26
  from '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/templates/xt_responsive_AQ/includes/categorie_listing.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.0',
  'unifunc' => 'content_6521e68eecf161_33601147',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'e802431423f7266b6a6125f7252998a1ae1addd5' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/templates/xt_responsive_AQ/includes/categorie_listing.html',
      1 => 1691797583,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_6521e68eecf161_33601147 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/smarty/xt_plugins/function.img.php','function'=>'smarty_function_img',),));
if ($_smarty_tpl->tpl_vars['categorie_listing']->value) {?>
    <div class="listing categorie-listing">
        <?php if ($_smarty_tpl->tpl_vars['position']->value == 'index') {?>
            <p class="h2 index-heading"><?php echo $_smarty_tpl->tpl_vars['heading']->value;?>
</p>
        <?php } else { ?>
            <p class="h3"><?php echo $_smarty_tpl->tpl_vars['heading']->value;?>
</p>
        <?php }?>
        <div class="row">
            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['categorie_listing']->value, 'module_data');
$_smarty_tpl->tpl_vars['module_data']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['module_data']->value) {
$_smarty_tpl->tpl_vars['module_data']->do_else = false;
?>
                <div class="col col-xs-6 col-sm-6 col-md-4 col-lg-4">
                    <div class="section text-center">
                        <?php if ($_smarty_tpl->tpl_vars['module_data']->value['categories_image']) {?>
                            <p class="image">
                                <a href="<?php echo $_smarty_tpl->tpl_vars['module_data']->value['categories_link'];?>
" class="vertical-helper image-link"><?php echo smarty_function_img(array('img'=>$_smarty_tpl->tpl_vars['module_data']->value['categories_image'],'type'=>'m_category_thumb','class'=>"productImageBorder img-responsive",'alt'=>htmlspecialchars((string)$_smarty_tpl->tpl_vars['module_data']->value['categories_name'], ENT_QUOTES, 'UTF-8', true)),$_smarty_tpl);?>
</a>
                            </p>
                        <?php }?>
                        <p class="title text-word-wrap">
                            <a href="<?php echo $_smarty_tpl->tpl_vars['module_data']->value['categories_link'];?>
"><?php echo $_smarty_tpl->tpl_vars['module_data']->value['categories_name'];?>
</a>
                        </p>
                    </div>
                </div><!-- .col -->
            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
        </div>
    </div>
<?php }
}
}
