<?php
/* Smarty version 4.3.2, created on 2024-04-21 11:50:15
  from '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages/order_history_block.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_6624e157963413_24836463',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '0f884f4dd6f46d0482ef422dd2edde329656bfcc' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages/order_history_block.html',
      1 => 1697144119,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_6624e157963413_24836463 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.txt.php','function'=>'smarty_function_txt',),1=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/vendor/smarty/smarty/libs/plugins/function.cycle.php','function'=>'smarty_function_cycle',),2=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.link.php','function'=>'smarty_function_link',),));
?>
<div class="table-responsive">
    <table class="table table-bordered table-striped">
	    <thead>
		    <tr>
      		    <th><?php echo smarty_function_txt(array('key'=>TEXT_ORDER_NUMBER),$_smarty_tpl);?>
</th>
      		    <th><?php echo smarty_function_txt(array('key'=>TEXT_ORDER_DATE),$_smarty_tpl);?>
</th>
      		    <th><?php echo smarty_function_txt(array('key'=>TEXT_ARTICLE),$_smarty_tpl);?>
</th>
      		    <th><?php echo smarty_function_txt(array('key'=>TEXT_TOTAL),$_smarty_tpl);?>
</th>
	  		    <th><?php echo smarty_function_txt(array('key'=>TEXT_ORDER_STATUS),$_smarty_tpl);?>
</th>
	 		    <th><?php echo smarty_function_txt(array('key'=>BUTTON_SHOW),$_smarty_tpl);?>
</th>
    	    </tr>
        </thead>
        <tbody>
  		    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['order_data']->value, 'data', false, NULL, 'aussen', array (
));
$_smarty_tpl->tpl_vars['data']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['data']->value) {
$_smarty_tpl->tpl_vars['data']->do_else = false;
?>
   		    <tr class="<?php echo smarty_function_cycle(array('values'=>"odd,even"),$_smarty_tpl);?>
 cursor-pointer" onclick="document.location.href='<?php echo smarty_function_link(array('page'=>'customer','paction'=>'order_info','params'=>'oid','params_value'=>$_smarty_tpl->tpl_vars['data']->value['order_data']['orders_id'],'conn'=>'SSL'),$_smarty_tpl);?>
'">
			<td class="bold-undssserline"><u><strong><?php echo $_smarty_tpl->tpl_vars['data']->value['order_data']['orders_id'];?>
</strong></u></td>
    		    <td><?php echo $_smarty_tpl->tpl_vars['data']->value['order_data']['date_purchased'];?>
</td>
    		    <td><?php echo $_smarty_tpl->tpl_vars['data']->value['order_count'];?>
</td>
    		    <td><?php echo $_smarty_tpl->tpl_vars['data']->value['order_total']['total']['formated'];?>
</td>
			    <td><?php echo $_smarty_tpl->tpl_vars['data']->value['order_data']['orders_status'];?>
</td>
			    <td>
                    <a href="<?php echo smarty_function_link(array('page'=>'customer','paction'=>'order_info','params'=>'oid','params_value'=>$_smarty_tpl->tpl_vars['data']->value['order_data']['orders_id'],'conn'=>'SSL'),$_smarty_tpl);?>
">
                        <span class="glyphicon glyphicon-info-sign"></span>
                        <span class="sr-only"><?php echo smarty_function_txt(array('key'=>BUTTON_SHOW),$_smarty_tpl);?>
</span>
                    </a>
                </td>
  		    </tr>
  		    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
  	    </tbody>
    </table>
</div><?php }
}
