<?php
/* Smarty version 4.3.2, created on 2024-04-21 11:50:18
  from '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages/edit_account.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_6624e15a770c24_47977211',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '64f512c5f3a55fec81e83d4a4b1735413e614f0e' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages/edit_account.html',
      1 => 1697144119,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_6624e15a770c24_47977211 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.txt.php','function'=>'smarty_function_txt',),1=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.form.php','function'=>'smarty_function_form',),2=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.hook.php','function'=>'smarty_function_hook',),));
?>
<div id="edit-account">
	<h1><?php echo smarty_function_txt(array('key'=>TEXT_EDIT_ACCOUNT),$_smarty_tpl);?>
</h1>
	<?php echo $_smarty_tpl->tpl_vars['message']->value;?>

	<?php echo smarty_function_form(array('type'=>'form','name'=>'edit_account','action'=>'dynamic','link_params'=>'page_action=edit_customer','method'=>'post','conn'=>'SSL'),$_smarty_tpl);?>

	<?php echo smarty_function_form(array('type'=>'hidden','name'=>'action','value'=>'edit_account'),$_smarty_tpl);?>

	<?php echo smarty_function_form(array('type'=>'hidden','name'=>'link_target','value'=>'customer'),$_smarty_tpl);?>

	<?php echo smarty_function_form(array('type'=>'hidden','name'=>'password_required','value'=>0),$_smarty_tpl);?>

	<?php echo smarty_function_form(array('type'=>'hidden','name'=>'privacy','value'=>1),$_smarty_tpl);?>


    <fieldset>
        <legend><?php echo smarty_function_txt(array('key'=>TEXT_ACCOUNT_DATA),$_smarty_tpl);?>
</legend>
        <div class="form-group">
            <label for="customers_email_address"><?php echo smarty_function_txt(array('key'=>TEXT_EMAIL),$_smarty_tpl);?>
*</label>
            <?php echo smarty_function_form(array('id'=>'customers_email_address','type'=>'email','name'=>'customers_email_address','value'=>$_smarty_tpl->tpl_vars['customers_email_address']->value,'readonly'=>true),$_smarty_tpl);?>

        </div>
        <div class="form-group">
            <!-- <label for="customers_email_address_confirm"><?php echo smarty_function_txt(array('key'=>TEXT_EMAIL_CONFIRM),$_smarty_tpl);?>
*</label> -->
            <?php echo smarty_function_form(array('id'=>'customers_email_address_confirm','type'=>'hidden','name'=>'customers_email_address_confirm','value'=>$_smarty_tpl->tpl_vars['customers_email_address_confirm']->value),$_smarty_tpl);?>

        </div>
        <?php if ($_smarty_tpl->tpl_vars['show_company']->value == 1) {?>
            <?php if ($_smarty_tpl->tpl_vars['show_vat']->value == 1) {?>
                <div class="form-group">
                    <label for="customers_vat_id"><?php echo smarty_function_txt(array('key'=>TEXT_VAT_ID),$_smarty_tpl);?>
</label>
                    <?php echo smarty_function_form(array('id'=>'customers_vat_id','type'=>'text','name'=>'customers_vat_id','value'=>$_smarty_tpl->tpl_vars['customers_vat_id']->value),$_smarty_tpl);?>

                </div>
            <?php }?>
        <?php }?>
        <div class="form-group">
            <label for="customers_default_language"><?php echo smarty_function_txt(array('key'=>TEXT_DEFAULT_LANGUAGE),$_smarty_tpl);?>
</label>
            <?php echo smarty_function_form(array('class'=>"form-control",'params'=>'id="customers_default_language"','type'=>'select','name'=>'customers_default_language','value'=>$_smarty_tpl->tpl_vars['lang_data']->value,'default'=>$_smarty_tpl->tpl_vars['selected_lang']->value),$_smarty_tpl);?>

        </div>
        <div class="form-group">
            <label for="customers_default_currency"><?php echo smarty_function_txt(array('key'=>TEXT_DEFAULT_CURRENCY),$_smarty_tpl);?>
</label>
            <?php echo smarty_function_form(array('class'=>"form-control",'params'=>'id="customers_default_currency"','type'=>'select','name'=>'customers_default_currency','value'=>$_smarty_tpl->tpl_vars['currency_data']->value,'default'=>$_smarty_tpl->tpl_vars['selected_curr']->value),$_smarty_tpl);?>

        </div>
    </fieldset>

    <fieldset>
        <legend><?php echo smarty_function_txt(array('key'=>TEXT_PASSWORD),$_smarty_tpl);?>
</legend>
        <p class="info-text alert alert-info"><?php echo smarty_function_txt(array('key'=>TEXT_PASSWORD_INFO),$_smarty_tpl);?>
</p>
        <div class="form-group">
            <label for="customers_password_current"><?php echo smarty_function_txt(array('key'=>TEXT_PASSWORD_CURRENT),$_smarty_tpl);?>
</label>
            <?php echo smarty_function_form(array('id'=>'customers_password_current','type'=>'password','name'=>'customers_password_current'),$_smarty_tpl);?>

        </div>
        <div class="form-group">
            <label for="customers_password"><?php echo smarty_function_txt(array('key'=>TEXT_PASSWORD_ENTRY),$_smarty_tpl);?>
</label>
            <?php echo smarty_function_form(array('id'=>'customers_password','type'=>'password','name'=>'customers_password','value'=>$_smarty_tpl->tpl_vars['customers_password']->value),$_smarty_tpl);?>

        </div>
        <div class="form-group">
            <label for="customers_password_confirm"><?php echo smarty_function_txt(array('key'=>TEXT_PASSWORD_ENTRY_CONFIRM),$_smarty_tpl);?>
</label>
            <?php echo smarty_function_form(array('id'=>'customers_password_confirm','type'=>'password','name'=>'customers_password_confirm','value'=>$_smarty_tpl->tpl_vars['customers_password_confirm']->value),$_smarty_tpl);?>

        </div>
    </fieldset>

    <?php echo smarty_function_hook(array('key'=>'edit_account_form'),$_smarty_tpl);?>

    <p class="required pull-left"><?php echo smarty_function_txt(array('key'=>TEXT_MUST),$_smarty_tpl);?>
</p>

    <div class="form-submit pull-right text-right">
        <p>
            <a href="javascript:history.back();" class="button"><?php echo smarty_function_txt(array('key'=>BUTTON_BACK),$_smarty_tpl);?>
</a>
            <button type="submit" class="btn btn-primary">
                <?php echo smarty_function_txt(array('key'=>BUTTON_NEXT),$_smarty_tpl);?>

            </button>
        </p>
    </div>

    <div class="clearfix"></div>

	<?php echo smarty_function_form(array('type'=>'formend'),$_smarty_tpl);?>


</div><!-- #edit-account --><?php }
}
