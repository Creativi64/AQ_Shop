<?php
/* Smarty version 4.3.0, created on 2023-10-09 17:40:52
  from '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/templates/xt_responsive_AQ/xtCore/forms/shipping.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.0',
  'unifunc' => 'content_65241f042637b3_06537257',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'a0e1abd7cacad8e5a5511ba2793b74e3059af063' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/templates/xt_responsive_AQ/xtCore/forms/shipping.html',
      1 => 1691797589,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_65241f042637b3_06537257 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/smarty/xt_plugins/function.img.php','function'=>'smarty_function_img',),1=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/smarty/xt_plugins/function.form.php','function'=>'smarty_function_form',),2=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/smarty/xt_plugins/function.txt.php','function'=>'smarty_function_txt',),3=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/vendor/smarty/smarty/libs/plugins/function.cycle.php','function'=>'smarty_function_cycle',),));
?>
<div id="shipping">
	<h1><?php echo $_smarty_tpl->tpl_vars['data']->value['content_heading'];?>
</h1>
	<?php if (trim($_smarty_tpl->tpl_vars['data']->value['content_image']) != '') {?>
		<div class="full-width-image content-image img-thumbnail">
			<?php echo smarty_function_img(array('img'=>$_smarty_tpl->tpl_vars['data']->value['content_image'],'type'=>'m_org','class'=>"img-responsive",'alt'=>htmlspecialchars((string)$_smarty_tpl->tpl_vars['data']->value['title'], ENT_QUOTES, 'UTF-8', true)),$_smarty_tpl);?>

		</div>
	<?php }?>
	<?php echo $_smarty_tpl->tpl_vars['message']->value;?>

	<?php if ($_smarty_tpl->tpl_vars['file']->value) {?><p><?php echo $_smarty_tpl->tpl_vars['file']->value;?>
</p><?php } else { ?><div class="textstyles"><?php echo $_smarty_tpl->tpl_vars['data']->value['content_body'];?>
</div><?php }?>
	<div class="well">
		<?php echo smarty_function_form(array('class'=>"form-inline",'type'=>'form','name'=>'shipping','action'=>'dynamic','link_params'=>'getParams','method'=>'post'),$_smarty_tpl);?>

		<?php echo smarty_function_form(array('type'=>'hidden','name'=>'action','value'=>'query'),$_smarty_tpl);?>

		<p><?php echo smarty_function_txt(array('key'=>TEXT_SHIPPING_INTRO),$_smarty_tpl);?>
</p>
        <div class="form-group">
		    <label><?php echo smarty_function_txt(array('key'=>TEXT_COUNTRY),$_smarty_tpl);?>
:&nbsp;</label>
		    <?php if ($_smarty_tpl->tpl_vars['sel_country']->value) {?>
		        <?php echo smarty_function_form(array('type'=>'select','name'=>'shipping_destination','value'=>$_smarty_tpl->tpl_vars['country_data']->value,'default'=>$_smarty_tpl->tpl_vars['sel_country']->value,'params'=>'onchange="this.form.submit();"'),$_smarty_tpl);?>

		    <?php } else { ?>
		        <?php echo smarty_function_form(array('type'=>'select','name'=>'shipping_destination','value'=>$_smarty_tpl->tpl_vars['country_data']->value,'default'=>(defined('_STORE_COUNTRY') ? constant('_STORE_COUNTRY') : null),'params'=>'onchange="this.form.submit();"'),$_smarty_tpl);?>

		    <?php }?>
        </div>
		<?php echo smarty_function_form(array('type'=>'formend'),$_smarty_tpl);?>

	</div><!-- .box -->

	<?php if ($_smarty_tpl->tpl_vars['shipping_data']->value) {?>
  		<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['shipping_data']->value, 'shipping_values');
$_smarty_tpl->tpl_vars['shipping_values']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['shipping_values']->value) {
$_smarty_tpl->tpl_vars['shipping_values']->do_else = false;
?>
		<p class="h3"><?php echo $_smarty_tpl->tpl_vars['shipping_values']->value['shipping_name'];?>
</p>
		<p><?php echo $_smarty_tpl->tpl_vars['shipping_values']->value['shipping_desc'];?>
</p>
		<table class="table table-responsive table-striped table-bordered">
			<thead>
				<tr>
      				<td><?php echo smarty_function_txt(array('key'=>TEXT_RANGE_STAFFEL),$_smarty_tpl);?>
</td>
      				<td class="right"><?php echo smarty_function_txt(array('key'=>TEXT_TOTAL_PRICE),$_smarty_tpl);?>
  <?php if ($_smarty_tpl->tpl_vars['shipping_values']->value['shipping_type'] == 'item') {
echo smarty_function_txt(array('key'=>TEXT_TYPE_PER_ITEM),$_smarty_tpl);
}?></td>
				</tr>
			</thead>
			<tbody>
				<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['shipping_values']->value['costs'], 'costs', false, NULL, 'shipping', array (
  'last' => true,
  'iteration' => true,
  'total' => true,
));
$_smarty_tpl->tpl_vars['costs']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['costs']->value) {
$_smarty_tpl->tpl_vars['costs']->do_else = false;
$_smarty_tpl->tpl_vars['__smarty_foreach_shipping']->value['iteration']++;
$_smarty_tpl->tpl_vars['__smarty_foreach_shipping']->value['last'] = $_smarty_tpl->tpl_vars['__smarty_foreach_shipping']->value['iteration'] === $_smarty_tpl->tpl_vars['__smarty_foreach_shipping']->value['total'];
?>
    			<tr class="<?php echo smarty_function_cycle(array('values'=>"odd,even"),$_smarty_tpl);?>
">
    				<td class="left">
    					<?php if ($_smarty_tpl->tpl_vars['costs']->value['shipping_type_value_from'] != '0') {?>
    					<?php echo smarty_function_txt(array('key'=>TEXT_RANGE_FROM),$_smarty_tpl);?>
 <?php echo trim(preg_replace('!<[^>]*?>!', ' ', $_smarty_tpl->tpl_vars['costs']->value['shipping_type_value_from'] ?: ''));?>

    					<?php }?>
    				<?php echo smarty_function_txt(array('key'=>TEXT_RANGE_TO),$_smarty_tpl);?>

   					<?php echo trim(preg_replace('!<[^>]*?>!', ' ', $_smarty_tpl->tpl_vars['costs']->value['shipping_type_value_to'] ?: ''));?>

    				<?php if ($_smarty_tpl->tpl_vars['shipping_values']->value['shipping_type'] == 'weight') {?>KG<?php }?>
    				<?php if ($_smarty_tpl->tpl_vars['shipping_values']->value['shipping_type'] == 'item') {
echo smarty_function_txt(array('key'=>TEXT_TYPE_ITEM),$_smarty_tpl);
}?>
   					</td>
    				<td class="right"><?php echo $_smarty_tpl->tpl_vars['costs']->value['shipping_price'];?>
</td>
  				</tr>
  				<?php if ((isset($_smarty_tpl->tpl_vars['__smarty_foreach_shipping']->value['last']) ? $_smarty_tpl->tpl_vars['__smarty_foreach_shipping']->value['last'] : null) == true) {?>
 				<tr class="<?php echo smarty_function_cycle(array('values'=>"odd,even"),$_smarty_tpl);?>
">
      				<td class="left">
    					<?php echo smarty_function_txt(array('key'=>TEXT_RANGE_FROM),$_smarty_tpl);?>

    					<?php echo trim(preg_replace('!<[^>]*?>!', ' ', $_smarty_tpl->tpl_vars['costs']->value['shipping_type_value_to'] ?: ''));?>

    					<?php if ($_smarty_tpl->tpl_vars['shipping_values']->value['shipping_type'] == 'weight') {?>KG<?php }?>
   			 			<?php if ($_smarty_tpl->tpl_vars['shipping_values']->value['shipping_type'] == 'item') {
echo smarty_function_txt(array('key'=>TEXT_TYPE_ITEM),$_smarty_tpl);
}?>
    				</td>
    				<td class="right"><?php echo smarty_function_txt(array('key'=>TEXT_INFO_NO_SHIPPING),$_smarty_tpl);?>
</td>
  				</tr>
 				<?php }?>
				<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
			</tbody>
  		</table>
 		<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
	<?php }?>
	<p><a href="javascript:history.back();" class="button"><?php echo smarty_function_txt(array('key'=>BUTTON_BACK),$_smarty_tpl);?>
</a></p>
</div><!-- #shipping --><?php }
}
