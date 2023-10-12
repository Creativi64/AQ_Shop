<?php
/* Smarty version 4.3.0, created on 2023-06-19 13:46:24
  from '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/templates/__xtAdmin/xtCore/pages/license.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.0',
  'unifunc' => 'content_64904010197aa1_83943867',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'fa8880ac54b9e544a7e6b16898450d3612a80ebd' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/templates/__xtAdmin/xtCore/pages/license.html',
      1 => 1687006096,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_64904010197aa1_83943867 (Smarty_Internal_Template $_smarty_tpl) {
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
