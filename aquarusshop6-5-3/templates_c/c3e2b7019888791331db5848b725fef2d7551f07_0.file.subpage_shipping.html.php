<?php
/* Smarty version 4.3.2, created on 2024-04-21 11:51:05
  from '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages/checkout/subpage_shipping.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_6624e189541f06_92880094',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'c3e2b7019888791331db5848b725fef2d7551f07' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages/checkout/subpage_shipping.html',
      1 => 1697144245,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_6624e189541f06_92880094 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.txt.php','function'=>'smarty_function_txt',),1=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.link.php','function'=>'smarty_function_link',),2=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/vendor/smarty/smarty/libs/plugins/modifier.count.php','function'=>'smarty_modifier_count',),3=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.form.php','function'=>'smarty_function_form',),4=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.hook.php','function'=>'smarty_function_hook',),5=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.box.php','function'=>'smarty_function_box',),));
?>
<div id="checkout-shipping" class="row">
	<div class="col col-sm-4 col-md-3">
		<div class="well shipping-address address">
            <p class="headline-underline clearfix">
                <?php echo smarty_function_txt(array('key'=>TEXT_SHIPPING_ADDRESS),$_smarty_tpl);?>

                <?php if ($_smarty_tpl->tpl_vars['shipping_address']->value['allow_change'] == true && $_smarty_tpl->tpl_vars['shipping_address']->value['address_class'] == 'shipping') {?>
                <a title="<?php echo smarty_function_txt(array('key'=>TEXT_EDIT),$_smarty_tpl);?>
" class="btn btn-xs btn-primary pull-right" href="<?php echo smarty_function_link(array('page'=>'customer','paction'=>'edit_address','params'=>'adType=shipping&abID','params_value'=>$_smarty_tpl->tpl_vars['shipping_address']->value['address_book_id'],'conn'=>'SSL'),$_smarty_tpl);?>
">
                    <span class="glyphicon glyphicon-pencil"></span>
                    <span class="sr-only"><?php echo smarty_function_txt(array('key'=>TEXT_EDIT),$_smarty_tpl);?>
</span>
                </a>
                <?php }?>
            </p>
			<?php if ($_smarty_tpl->tpl_vars['shipping_address']->value['customers_company']) {?><p><?php echo $_smarty_tpl->tpl_vars['shipping_address']->value['customers_company'];?>
</p><?php }?>
            <?php if ($_smarty_tpl->tpl_vars['shipping_address']->value['customers_company_2']) {?><p><?php echo $_smarty_tpl->tpl_vars['shipping_address']->value['customers_company_2'];?>
</p><?php }?>
            <?php if ($_smarty_tpl->tpl_vars['shipping_address']->value['customers_company_3']) {?><p><?php echo $_smarty_tpl->tpl_vars['shipping_address']->value['customers_company_3'];?>
</p><?php }?>
			<p><?php if ($_smarty_tpl->tpl_vars['shipping_address']->value['customers_title']) {
echo $_smarty_tpl->tpl_vars['shipping_address']->value['customers_title'];?>
 <?php }
echo $_smarty_tpl->tpl_vars['shipping_address']->value['customers_firstname'];?>
 <?php echo $_smarty_tpl->tpl_vars['shipping_address']->value['customers_lastname'];?>
</p>
			<p><?php echo $_smarty_tpl->tpl_vars['shipping_address']->value['customers_street_address'];?>
</p>
            <?php if ($_smarty_tpl->tpl_vars['shipping_address']->value['customers_address_addition']) {?></p><?php echo $_smarty_tpl->tpl_vars['shipping_address']->value['customers_address_addition'];?>
</p><?php }?>
			<?php if ($_smarty_tpl->tpl_vars['shipping_address']->value['customers_suburb']) {?></p><?php echo $_smarty_tpl->tpl_vars['shipping_address']->value['customers_suburb'];?>
</p><?php }?>
			<p><?php echo $_smarty_tpl->tpl_vars['shipping_address']->value['customers_postcode'];?>
 <?php echo $_smarty_tpl->tpl_vars['shipping_address']->value['customers_city'];?>
</p>
			<p><?php echo $_smarty_tpl->tpl_vars['shipping_address']->value['customers_country'];?>
</p>
			<p><br /></p>
			<?php if ($_smarty_tpl->tpl_vars['shipping_address']->value['allow_change'] == true) {?>
                <?php if (smarty_modifier_count($_smarty_tpl->tpl_vars['address_data']->value) > 2) {?>
                    <?php echo smarty_function_form(array('type'=>'form','role'=>"form",'name'=>'shipping_address','action'=>'dynamic','link_params'=>'page_action=shipping','method'=>'post','conn'=>'SSL'),$_smarty_tpl);?>

                    <div class="form-group">
                        <label for="address_data"><?php echo smarty_function_txt(array('key'=>TEXT_SELECT_SHIPPING_ADDRESS),$_smarty_tpl);?>
</label>
                        <?php echo smarty_function_form(array('type'=>'select','class'=>"form-control",'id'=>"address_data",'name'=>'adID','value'=>$_smarty_tpl->tpl_vars['address_data']->value,'default'=>$_SESSION['customer']->customer_shipping_address['address_book_id'],'params'=>'onchange="this.form.submit();" data-style="btn-secondary"'),$_smarty_tpl);?>

                    </div>
                    <?php echo smarty_function_form(array('type'=>'hidden','name'=>'adType','value'=>'shipping'),$_smarty_tpl);?>

                    <?php echo smarty_function_form(array('type'=>'hidden','name'=>'action','value'=>'select_address'),$_smarty_tpl);?>

                    <?php echo smarty_function_form(array('type'=>'formend'),$_smarty_tpl);?>

                <?php }?>
			    <?php if ($_smarty_tpl->tpl_vars['add_new_address']->value == 1) {?>
			        <!--<p><?php echo smarty_function_txt(array('key'=>TEXT_NEW_SHIPPING_ADDRESS),$_smarty_tpl);?>
</p>-->
			        <a class="btn btn-primary" href="<?php echo smarty_function_link(array('page'=>'customer','paction'=>'edit_address','params'=>'adType=shipping','conn'=>'SSL'),$_smarty_tpl);?>
">
                        <span class="glyphicon glyphicon-plus"></span>
                        <?php echo smarty_function_txt(array('key'=>BUTTON_ADD_ADDRESS),$_smarty_tpl);?>

                    </a>
			    <?php }?>
			<?php }?>
		</div>
        <?php echo smarty_function_hook(array('key'=>'tpl_checkout_shipping_info_boxes'),$_smarty_tpl);?>

	</div>
	<div class="col col-sm-8 col-md-9">
		<?php echo smarty_function_box(array('name'=>'xt_delivery_date','type'=>'user'),$_smarty_tpl);?>

		<h1><?php echo smarty_function_txt(array('key'=>TEXT_SELECT_SHIPPING_DESC),$_smarty_tpl);?>
</h1>
        <?php echo smarty_function_form(array('type'=>'form','name'=>'shipping','action'=>'checkout','method'=>'post','conn'=>'SSL'),$_smarty_tpl);?>

        <?php echo smarty_function_form(array('type'=>'hidden','name'=>'action','value'=>'shipping'),$_smarty_tpl);?>


        <ul class="list-group">
		    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['shipping_data']->value, 'sdata', false, 'kdata', 'aussen', array (
));
$_smarty_tpl->tpl_vars['sdata']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['kdata']->value => $_smarty_tpl->tpl_vars['sdata']->value) {
$_smarty_tpl->tpl_vars['sdata']->do_else = false;
?>
			    <li class="list-group-item clearfix list-group-item-<?php echo $_smarty_tpl->tpl_vars['kdata']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['sdata']->value['shipping'];?>
</li>
		    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
        </ul>

        <?php echo smarty_function_box(array('name'=>'xt_checkout_options','type'=>'user'),$_smarty_tpl);?>


        <div class="clearfix">
            <a href="<?php echo smarty_function_link(array('page'=>'cart','conn'=>'SSL'),$_smarty_tpl);?>
" class="btn btn-default pull-left">
                <i class="fa fa-chevron-left"></i>
                <?php echo smarty_function_txt(array('key'=>BUTTON_BACK),$_smarty_tpl);?>

            </a>
            <button id="<?php echo $_smarty_tpl->tpl_vars['page_action']->value;?>
-submit" type="submit" class="btn btn-success pull-right">
                <span class="glyphicon glyphicon-ok"></span>
                <?php echo smarty_function_txt(array('key'=>BUTTON_NEXT),$_smarty_tpl);?>

            </button>
        </div>

        <?php echo smarty_function_hook(array('key'=>'checkout_tpl_shipping'),$_smarty_tpl);?>

        <?php echo smarty_function_form(array('type'=>'formend'),$_smarty_tpl);?>

	</div>
</div>
<?php }
}
