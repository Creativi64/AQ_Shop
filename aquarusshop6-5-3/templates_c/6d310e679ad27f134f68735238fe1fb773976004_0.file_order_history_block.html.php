<?php
/* Smarty version 5.4.1, created on 2024-12-16 23:07:09
  from 'file:xtCore/pages/order_history_block.html' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.4.1',
  'unifunc' => 'content_6760a48d07db92_21079096',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '6d310e679ad27f134f68735238fe1fb773976004' => 
    array (
      0 => 'xtCore/pages/order_history_block.html',
      1 => 1697144119,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_6760a48d07db92_21079096 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages';
?><div class="table-responsive">
    <table class="table table-bordered table-striped">
	    <thead>
		    <tr>
      		    <th><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_ORDER_NUMBER), $_smarty_tpl);?>
</th>
      		    <th><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_ORDER_DATE), $_smarty_tpl);?>
</th>
      		    <th><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_ARTICLE), $_smarty_tpl);?>
</th>
      		    <th><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_TOTAL), $_smarty_tpl);?>
</th>
	  		    <th><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_ORDER_STATUS), $_smarty_tpl);?>
</th>
	 		    <th><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>BUTTON_SHOW), $_smarty_tpl);?>
</th>
    	    </tr>
        </thead>
        <tbody>
  		    <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('order_data'), 'data', false, NULL, 'aussen', array (
));
$foreach0DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('data')->value) {
$foreach0DoElse = false;
?>
   		    <tr class="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('cycle')->handle(array('values'=>"odd,even"), $_smarty_tpl);?>
 cursor-pointer" onclick="document.location.href='<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('link')->handle(array('page'=>'customer','paction'=>'order_info','params'=>'oid','params_value'=>$_smarty_tpl->getValue('data')['order_data']['orders_id'],'conn'=>'SSL'), $_smarty_tpl);?>
'">
			<td class="bold-undssserline"><u><strong><?php echo $_smarty_tpl->getValue('data')['order_data']['orders_id'];?>
</strong></u></td>
    		    <td><?php echo $_smarty_tpl->getValue('data')['order_data']['date_purchased'];?>
</td>
    		    <td><?php echo $_smarty_tpl->getValue('data')['order_count'];?>
</td>
    		    <td><?php echo $_smarty_tpl->getValue('data')['order_total']['total']['formated'];?>
</td>
			    <td><?php echo $_smarty_tpl->getValue('data')['order_data']['orders_status'];?>
</td>
			    <td>
                    <a href="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('link')->handle(array('page'=>'customer','paction'=>'order_info','params'=>'oid','params_value'=>$_smarty_tpl->getValue('data')['order_data']['orders_id'],'conn'=>'SSL'), $_smarty_tpl);?>
">
                        <span class="glyphicon glyphicon-info-sign"></span>
                        <span class="sr-only"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>BUTTON_SHOW), $_smarty_tpl);?>
</span>
                    </a>
                </td>
  		    </tr>
  		    <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
  	    </tbody>
    </table>
</div><?php }
}
