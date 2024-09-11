<?php
/* Smarty version 5.1.0, created on 2024-09-10 11:50:10
  from 'file:/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/forms/shipping.html' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.1.0',
  'unifunc' => 'content_66e01652b3a426_76641311',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'af13e8e608a0bf4f17a2bf6e0ea0b0aa5a1f7f5d' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/forms/shipping.html',
      1 => 1697144119,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_66e01652b3a426_76641311 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/forms';
?><div id="shipping">
	<h1><?php echo $_smarty_tpl->getValue('data')['content_heading'];?>
</h1>
	<?php if ($_smarty_tpl->getSmarty()->getModifierCallback('trim')($_smarty_tpl->getValue('data')['content_image']) != '') {?>
		<div class="full-width-image content-image img-thumbnail">
			<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('img')->handle(array('img'=>$_smarty_tpl->getValue('data')['content_image'],'type'=>'m_org','class'=>"img-responsive",'alt'=>htmlspecialchars((string)$_smarty_tpl->getValue('data')['title'], ENT_QUOTES, 'UTF-8', true)), $_smarty_tpl);?>

		</div>
	<?php }?>
	<?php echo $_smarty_tpl->getValue('message');?>

	<?php if ($_smarty_tpl->getValue('file')) {?><p><?php echo $_smarty_tpl->getValue('file');?>
</p><?php } else { ?><div class="textstyles"><?php echo $_smarty_tpl->getValue('data')['content_body'];?>
</div><?php }?>
	<div class="well">
		<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('class'=>"form-inline",'type'=>'form','name'=>'shipping','action'=>'dynamic','link_params'=>'getParams','method'=>'post'), $_smarty_tpl);?>

		<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'hidden','name'=>'action','value'=>'query'), $_smarty_tpl);?>

		<p><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_SHIPPING_INTRO), $_smarty_tpl);?>
</p>
        <div class="form-group">
		    <label><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_COUNTRY), $_smarty_tpl);?>
:&nbsp;</label>
		    <?php if ($_smarty_tpl->getValue('sel_country')) {?>
		        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'select','name'=>'shipping_destination','value'=>$_smarty_tpl->getValue('country_data'),'default'=>$_smarty_tpl->getValue('sel_country'),'params'=>'onchange="this.form.submit();"'), $_smarty_tpl);?>

		    <?php } else { ?>
		        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'select','name'=>'shipping_destination','value'=>$_smarty_tpl->getValue('country_data'),'default'=>(defined('_STORE_COUNTRY') ? constant('_STORE_COUNTRY') : null),'params'=>'onchange="this.form.submit();"'), $_smarty_tpl);?>

		    <?php }?>
        </div>
		<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'formend'), $_smarty_tpl);?>

	</div><!-- .box -->

	<?php if ($_smarty_tpl->getValue('shipping_data')) {?>
  		<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('shipping_data'), 'shipping_values');
$foreach0DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('shipping_values')->value) {
$foreach0DoElse = false;
?>
		<p class="h3"><?php echo $_smarty_tpl->getValue('shipping_values')['shipping_name'];?>
</p>
		<p><?php echo $_smarty_tpl->getValue('shipping_values')['shipping_desc'];?>
</p>
		<table class="table table-responsive table-striped table-bordered">
			<thead>
				<tr>
      				<td><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_RANGE_STAFFEL), $_smarty_tpl);?>
</td>
      				<td class="right"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_TOTAL_PRICE), $_smarty_tpl);?>
  <?php if ($_smarty_tpl->getValue('shipping_values')['shipping_type'] == 'item') {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_TYPE_PER_ITEM), $_smarty_tpl);
}?></td>
				</tr>
			</thead>
			<tbody>
				<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('shipping_values')['costs'], 'costs', false, NULL, 'shipping', array (
  'last' => true,
  'iteration' => true,
  'total' => true,
));
$foreach1DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('costs')->value) {
$foreach1DoElse = false;
$_smarty_tpl->tpl_vars['__smarty_foreach_shipping']->value['iteration']++;
$_smarty_tpl->tpl_vars['__smarty_foreach_shipping']->value['last'] = $_smarty_tpl->tpl_vars['__smarty_foreach_shipping']->value['iteration'] === $_smarty_tpl->tpl_vars['__smarty_foreach_shipping']->value['total'];
?>
    			<tr class="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('cycle')->handle(array('values'=>"odd,even"), $_smarty_tpl);?>
">
    				<td class="left">
    					<?php if ($_smarty_tpl->getValue('costs')['shipping_type_value_from'] != '0') {?>
    					<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_RANGE_FROM), $_smarty_tpl);?>
 <?php echo $_smarty_tpl->getSmarty()->getModifierCallback('trim')(preg_replace('!<[^>]*?>!', ' ', (string) $_smarty_tpl->getValue('costs')['shipping_type_value_from']));?>

    					<?php }?>
    				<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_RANGE_TO), $_smarty_tpl);?>

   					<?php echo $_smarty_tpl->getSmarty()->getModifierCallback('trim')(preg_replace('!<[^>]*?>!', ' ', (string) $_smarty_tpl->getValue('costs')['shipping_type_value_to']));?>

    				<?php if ($_smarty_tpl->getValue('shipping_values')['shipping_type'] == 'weight') {?>KG<?php }?>
    				<?php if ($_smarty_tpl->getValue('shipping_values')['shipping_type'] == 'item') {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_TYPE_ITEM), $_smarty_tpl);
}?>
   					</td>
    				<td class="right"><?php echo $_smarty_tpl->getValue('costs')['shipping_price'];?>
</td>
  				</tr>
  				<?php if (($_smarty_tpl->getValue('__smarty_foreach_shipping')['last'] ?? null) == true) {?>
 				<tr class="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('cycle')->handle(array('values'=>"odd,even"), $_smarty_tpl);?>
">
      				<td class="left">
    					<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_RANGE_FROM), $_smarty_tpl);?>

    					<?php echo $_smarty_tpl->getSmarty()->getModifierCallback('trim')(preg_replace('!<[^>]*?>!', ' ', (string) $_smarty_tpl->getValue('costs')['shipping_type_value_to']));?>

    					<?php if ($_smarty_tpl->getValue('shipping_values')['shipping_type'] == 'weight') {?>KG<?php }?>
   			 			<?php if ($_smarty_tpl->getValue('shipping_values')['shipping_type'] == 'item') {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_TYPE_ITEM), $_smarty_tpl);
}?>
    				</td>
    				<td class="right"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_INFO_NO_SHIPPING), $_smarty_tpl);?>
</td>
  				</tr>
 				<?php }?>
				<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
			</tbody>
  		</table>
 		<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
	<?php }?>
	<p><a href="javascript:history.back();" class="button"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>BUTTON_BACK), $_smarty_tpl);?>
</a></p>
</div><!-- #shipping --><?php }
}
