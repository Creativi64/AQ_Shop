<?php
/* Smarty version 4.3.2, created on 2024-03-21 04:23:07
  from '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/templates/xt_responsive_AQ/xtCore/pages/content.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_65fba81b940d78_87332736',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '457062536f1b4ba2d82e9e1225ce5331c948ad62' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/templates/xt_responsive_AQ/xtCore/pages/content.html',
      1 => 1697144119,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_65fba81b940d78_87332736 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/smarty/xt_plugins/function.img.php','function'=>'smarty_function_img',),1=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/smarty/xt_plugins/function.txt.php','function'=>'smarty_function_txt',),));
if (!(isset($_smarty_tpl->tpl_vars['disable_content_container']->value))) {
if (trim($_smarty_tpl->tpl_vars['data']->value['content_image']) != '') {?>
    <div class="full-width-image content-image img-thumbnail">
        <?php echo smarty_function_img(array('img'=>$_smarty_tpl->tpl_vars['data']->value['content_image'],'type'=>'m_org','class'=>"img-responsive",'alt'=>htmlspecialchars((string)$_smarty_tpl->tpl_vars['data']->value['title'], ENT_QUOTES, 'UTF-8', true)),$_smarty_tpl);?>

    </div>
<?php }?>
<h1><?php echo $_smarty_tpl->tpl_vars['data']->value['title'];?>
</h1>
<?php if ($_smarty_tpl->tpl_vars['file']->value) {?>
    <?php echo $_smarty_tpl->tpl_vars['file']->value;?>

<?php } else { ?>
    <div class="textstyles">
        <?php echo $_smarty_tpl->tpl_vars['data']->value['content_body'];?>

    </div>
<?php }?>
<div class="clearfix"></div>
<?php if ($_smarty_tpl->tpl_vars['data']->value['subcontent']) {?>
    <hr />
    <p class="h4"><?php echo smarty_function_txt(array('key'=>HEADING_SUB_CONTENT),$_smarty_tpl);?>
</p>
    <ul class="list-inline">
        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['data']->value['subcontent'], 'module_data', false, NULL, 'aussen', array (
));
$_smarty_tpl->tpl_vars['module_data']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['module_data']->value) {
$_smarty_tpl->tpl_vars['module_data']->do_else = false;
?>
            <li>
                <a href="<?php echo $_smarty_tpl->tpl_vars['module_data']->value['link'];?>
" class="btn btn-secondary">
                    <i class="fa fa-info-circle"></i>
                    <?php echo trim($_smarty_tpl->tpl_vars['module_data']->value['title']);?>

                </a>
            </li>
        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
    </ul>
    <br />
<?php }
}
}
}
