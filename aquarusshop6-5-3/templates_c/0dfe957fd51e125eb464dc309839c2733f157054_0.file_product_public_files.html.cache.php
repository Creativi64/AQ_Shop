<?php
/* Smarty version 5.4.1, created on 2024-12-19 11:52:26
  from 'file:xtCore/pages/files/product_public_files.html' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.4.1',
  'unifunc' => 'content_6763faea463051_48372080',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '0dfe957fd51e125eb464dc309839c2733f157054' => 
    array (
      0 => 'xtCore/pages/files/product_public_files.html',
      1 => 1697144245,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_6763faea463051_48372080 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages/files';
$_smarty_tpl->getCompiled()->nocache_hash = '9587375036763fae9ed2e48_08080490';
?>
<div class="download-table div-table table-hover">
    <div class="row th hidden-xs">
        <div class="col col-sm-8"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_FILE), $_smarty_tpl);?>
</div>
        <div class="col col-sm-4 text-right text-left-xs"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_SIZE), $_smarty_tpl);?>
</div>
    </div>
    <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('media_files'), 'file_data', false, NULL, 'aussen', array (
));
$foreach0DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('file_data')->value) {
$foreach0DoElse = false;
?>
    <div class="row tr cursor-pointer" onclick="var win = window.open('<?php echo $_smarty_tpl->getValue('file_data')['download_url'];?>
', '_blank');win.focus();">
            <div class="col col-sm-8">
                <p class="title">
                <a href="<?php echo $_smarty_tpl->getValue('file_data')['download_url'];?>
" rel="nofollow" target="_blank" onclick="return false;" >

                    <?php if ($_smarty_tpl->getValue('file_data')['extension'] == 'pdf') {?>
                    <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                    <?php } elseif ($_smarty_tpl->getValue('file_data')['extension'] == 'zip' || $_smarty_tpl->getValue('file_data')['extension'] == 'rar') {?>
                    <i class="fa fa-file-archive-o" aria-hidden="true"></i>
                    <?php } else { ?>
                    <i class="fa fa-file-o" aria-hidden="true"></i>
                    <?php }?>
                    &nbsp;<span class="name"><?php if ($_smarty_tpl->getValue('file_data')['media_name']) {
echo $_smarty_tpl->getValue('file_data')['media_name'];
} else {
echo $_smarty_tpl->getValue('file_data')['file'];
}?></span>
                    </a>
                    <small class="visible-xs-inline">(<?php echo $_smarty_tpl->getSmarty()->getModifierCallback('fsize_format')($_smarty_tpl->getValue('file_data')['media_size'],"MB");?>
)</small>
                </p>
                <?php if ($_smarty_tpl->getValue('file_data')['media_description']) {?><p><?php echo $_smarty_tpl->getValue('file_data')['media_description'];?>
</p><?php }?>
            </div>
            <div class="col col-sm-4 text-right hidden-xs">
                <?php echo $_smarty_tpl->getSmarty()->getModifierCallback('fsize_format')($_smarty_tpl->getValue('file_data')['media_size'],"MB");?>

            </div>
        </div>
    <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
</div>
<?php }
}
