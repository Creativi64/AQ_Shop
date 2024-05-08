<?php
/* Smarty version 4.3.2, created on 2024-05-04 18:29:46
  from '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages/address_book.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_6636627a541bd4_72759511',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'c75af8231da28dbd7bdf15423234addb93cb1c7e' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages/address_book.html',
      1 => 1697144119,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_6636627a541bd4_72759511 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.txt.php','function'=>'smarty_function_txt',),1=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.address_class.php','function'=>'smarty_function_address_class',),2=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.link.php','function'=>'smarty_function_link',),));
?>
<div id="adress-book">
	<h1><?php echo smarty_function_txt(array('key'=>TEXT_PAGE_TITLE_ADDRESS_BOOK),$_smarty_tpl);?>
</h1>
	<?php echo $_smarty_tpl->tpl_vars['message']->value;?>

	<p><?php echo smarty_function_txt(array('key'=>TEXT_ADDRESS_BOOK_INFO),$_smarty_tpl);?>
</p>

	<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['addresses_data']->value, 'addresses', false, NULL, 'aussen', array (
));
$_smarty_tpl->tpl_vars['addresses']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['addresses']->value) {
$_smarty_tpl->tpl_vars['addresses']->do_else = false;
?>
	    <div class="address well clearfix">
            <p class="pull-right btn-group">
                <span class="badge badge-lighter pull-right "><?php echo smarty_function_address_class(array('address'=>$_smarty_tpl->tpl_vars['addresses']->value),$_smarty_tpl);?>
</span>
                <span class="clear">&nbsp;</span>
            <?php if ($_smarty_tpl->tpl_vars['addresses']->value['allow_change'] == true) {?>
                    <a class="btn btn-default" href="<?php echo smarty_function_link(array('page'=>'customer','paction'=>'edit_address','params'=>'abID','params_value'=>$_smarty_tpl->tpl_vars['addresses']->value['address_book_id'],'conn'=>'SSL'),$_smarty_tpl);?>
">
                        <span class="glyphicon glyphicon-pencil"></span>
                        <?php echo smarty_function_txt(array('key'=>BUTTON_EDIT),$_smarty_tpl);?>

                    </a>
                    <a class="btn btn-danger" href="<?php echo smarty_function_link(array('page'=>'customer','paction'=>'delete_address','params'=>'abID','params_value'=>$_smarty_tpl->tpl_vars['addresses']->value['address_book_id'],'conn'=>'SSL'),$_smarty_tpl);?>
">
                        <span class="glyphicon glyphicon-trash"></span>
                        <?php echo smarty_function_txt(array('key'=>BUTTON_DELETE),$_smarty_tpl);?>

                    </a>
                <?php }?>
                </p>
		    <?php if ($_smarty_tpl->tpl_vars['addresses']->value['customers_company']) {?>
                <strong><?php echo $_smarty_tpl->tpl_vars['addresses']->value['customers_company'];?>
</strong><br />
            <?php }?>
            <?php if ($_smarty_tpl->tpl_vars['addresses']->value['customers_company_2']) {
echo $_smarty_tpl->tpl_vars['addresses']->value['customers_company_2'];?>
<br /><?php }?>
            <?php if ($_smarty_tpl->tpl_vars['addresses']->value['customers_company_3']) {
echo $_smarty_tpl->tpl_vars['addresses']->value['customers_company_3'];?>
<br /><?php }?>
		    <strong><?php if ($_smarty_tpl->tpl_vars['show_title']->value && $_smarty_tpl->tpl_vars['addresses']->value['customers_title']) {
echo $_smarty_tpl->tpl_vars['addresses']->value['customers_title'];?>
 <?php }
echo $_smarty_tpl->tpl_vars['addresses']->value['customers_firstname'];?>
 <?php echo $_smarty_tpl->tpl_vars['addresses']->value['customers_lastname'];?>
</strong><br />
		    <?php echo $_smarty_tpl->tpl_vars['addresses']->value['customers_street_address'];?>
<br />
            <?php if ($_smarty_tpl->tpl_vars['addresses']->value['customers_address_addition']) {
echo $_smarty_tpl->tpl_vars['addresses']->value['customers_address_addition'];?>
<br /><?php }?>
		    <?php if ($_smarty_tpl->tpl_vars['addresses']->value['customers_suburb']) {
echo $_smarty_tpl->tpl_vars['addresses']->value['customers_suburb'];?>
<br /><?php }?>
		    <?php echo $_smarty_tpl->tpl_vars['addresses']->value['customers_postcode'];?>
 <?php echo $_smarty_tpl->tpl_vars['addresses']->value['customers_city'];?>
<br />
		    <?php if ($_smarty_tpl->tpl_vars['addresses']->value['customers_zone']) {
echo $_smarty_tpl->tpl_vars['addresses']->value['customers_zone'];?>
<br /><?php }?>
		    <?php echo $_smarty_tpl->tpl_vars['addresses']->value['customers_country'];?>

	    </div>
	<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>

    <p class="pull-left text-muted"><?php echo $_smarty_tpl->tpl_vars['address_count']->value;?>
 / <?php echo $_smarty_tpl->tpl_vars['address_max_count']->value;?>
 <?php echo smarty_function_txt(array('key'=>TEXT_POSSIBLE_ENTRIES),$_smarty_tpl);?>
</p>

	<p class="pull-right">
        <a class="btn btn-default" href="<?php echo smarty_function_link(array('page'=>'customer','conn'=>'SSL'),$_smarty_tpl);?>
">
            <?php echo smarty_function_txt(array('key'=>BUTTON_BACK),$_smarty_tpl);?>

        </a>
		<?php if ($_smarty_tpl->tpl_vars['add_new_address']->value == 1) {?>
		    <a class="btn btn-success" href="<?php echo smarty_function_link(array('page'=>'customer','paction'=>'edit_address','conn'=>'SSL'),$_smarty_tpl);?>
">
                <span class="glyphicon glyphicon-plus"></span>
                <?php echo smarty_function_txt(array('key'=>BUTTON_ADD_ADDRESS),$_smarty_tpl);?>

            </a>
		<?php }?>
	</p>
    <div class="clearfix"></div>
</div><!-- #adress-book --><?php }
}
