<?php
/* Smarty version 4.3.2, created on 2024-04-05 17:56:56
  from '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/templates/__xtAdmin/xtCore/pages/license.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_66101f48ae0622_56124974',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '726e85dee9edfb18037c0f1007df89d927120947' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/templates/__xtAdmin/xtCore/pages/license.html',
      1 => 1697144119,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_66101f48ae0622_56124974 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/smarty/xt_plugins/function.txt.php','function'=>'smarty_function_txt',),));
?>
<div style="margin-top:20px; width:450px;margin-left:30px;">
	<p><?php echo smarty_function_txt(array('key'=>TEXT_LICENSE_INFO),$_smarty_tpl);?>
</p>
	<h1> </h1>
	<table width="450px;" >
		
		<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['lic_parms']->value, 'info', false, NULL, 'aussen', array (
));
$_smarty_tpl->tpl_vars['info']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['info']->value) {
$_smarty_tpl->tpl_vars['info']->do_else = false;
?>
		  <tr>
			<td width="30%" class="left"><?php echo smarty_function_txt(array('key'=>$_smarty_tpl->tpl_vars['info']->value['text']),$_smarty_tpl);?>
:</td>
			<td width="30%" class="left"><?php echo $_smarty_tpl->tpl_vars['info']->value['value'];?>
</td>
			<td></td>
		  </tr> 
		<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
	</table>
</div><?php }
}
