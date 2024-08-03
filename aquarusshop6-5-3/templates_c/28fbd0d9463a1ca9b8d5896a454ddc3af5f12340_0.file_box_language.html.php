<?php
/* Smarty version 5.1.0, created on 2024-08-03 01:07:41
  from 'file:/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/boxes/box_language.html' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.1.0',
  'unifunc' => 'content_66ad66bd9cbf33_16126036',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '28fbd0d9463a1ca9b8d5896a454ddc3af5f12340' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/boxes/box_language.html',
      1 => 1697144118,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_66ad66bd9cbf33_16126036 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/boxes';
?><div class="language">
    <p class="headline"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_LANGUAGE), $_smarty_tpl);?>
</p>
    <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'form','name'=>'language','method'=>'post'), $_smarty_tpl);?>
    <select name="new_lang" onchange="location.href=this.options[this.selectedIndex].value" class="show-tick form-control">
        <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('lang_data'), 'box_data', false, NULL, 'aussen', array (
));
$foreach4DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('box_data')->value) {
$foreach4DoElse = false;
?>

            <?php $_smarty_tpl->assign('lang_switch_params', '?language=', false, NULL);?>
            <?php if ($_smarty_tpl->getSmarty()->getModifierCallback('strstr')($_smarty_tpl->getValue('box_data')['link'],'?')) {?>
                <?php $_smarty_tpl->assign('lang_switch_params', '&language=', false, NULL);?>
            <?php }?>

            <?php if (!$_smarty_tpl->getValue('selected_lang')) {?>                <option data-content="<span class='option-title'><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_LANGUAGE), $_smarty_tpl);?>
: </span><?php echo $_smarty_tpl->getValue('box_data')['name'];?>
" value="<?php echo $_smarty_tpl->getValue('box_data')['link'];
echo $_smarty_tpl->getValue('lang_switch_params');
echo $_smarty_tpl->getValue('box_data')['code'];?>
"<?php if ($_smarty_tpl->getValue('language') == $_smarty_tpl->getValue('box_data')['code']) {?> selected="selected"<?php }?>><?php echo $_smarty_tpl->getValue('box_data')['name'];?>
</option>
            <?php } else { ?>                <option data-content="<span class='option-title'><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_LANGUAGE), $_smarty_tpl);?>
: </span><?php echo $_smarty_tpl->getValue('box_data')['name'];?>
" value="<?php echo $_smarty_tpl->getValue('box_data')['link'];
echo $_smarty_tpl->getValue('lang_switch_params');
echo $_smarty_tpl->getValue('box_data')['code'];?>
"<?php if ($_smarty_tpl->getValue('selected_lang') == $_smarty_tpl->getValue('box_data')['code']) {?> selected="selected"<?php }?>><?php echo $_smarty_tpl->getValue('box_data')['name'];?>
</option>
            <?php }?>
        <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
    </select>
    <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'formend'), $_smarty_tpl);?>

</div><?php }
}
