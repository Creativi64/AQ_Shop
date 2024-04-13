<?php
/* Smarty version 4.3.2, created on 2024-03-21 04:23:07
  from '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/templates/xt_responsive_AQ/xtCore/forms/contact.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_65fba81b8e34b8_37851505',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'd814d586d6f84cc194da0378d257de27386276bb' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/templates/xt_responsive_AQ/xtCore/forms/contact.html',
      1 => 1697144119,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:xtCore/pages/content.html' => 1,
  ),
),false)) {
function content_65fba81b8e34b8_37851505 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/smarty/xt_plugins/function.txt.php','function'=>'smarty_function_txt',),1=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/smarty/xt_plugins/function.form.php','function'=>'smarty_function_form',),2=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/vendor/smarty/smarty/libs/plugins/modifier.count.php','function'=>'smarty_modifier_count',),3=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/smarty/xt_plugins/function.content.php','function'=>'smarty_function_content',),));
?>
<div id="contact">
	<?php echo $_smarty_tpl->tpl_vars['message']->value;?>
 <?php if ($_smarty_tpl->tpl_vars['show_form']->value == 'true') {?>
	<p class="alert alert-info">
		<i class="fa fa-info-circle"></i> <?php echo smarty_function_txt(array('key'=>TEXT_CONTACT_INTRO),$_smarty_tpl);?>

	</p>
	<?php }?> <?php $_smarty_tpl->_subTemplateRender("file:xtCore/pages/content.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?> <?php if ($_smarty_tpl->tpl_vars['show_form']->value == 'true') {?> <?php echo smarty_function_form(array('class'=>"form-horizontal",'type'=>'form','name'=>'login','action'=>'dynamic','link_params'=>'getParams','method'=>'post','conn'=>'SSL'),$_smarty_tpl);?>
 <?php echo smarty_function_form(array('type'=>'hidden','name'=>'action','value'=>'send'),$_smarty_tpl);?>

	<fieldset>
		<legend><?php echo smarty_function_txt(array('key'=>TEXT_CONTACT_PAGE),$_smarty_tpl);?>
</legend>
		<div class="form-group">
			<label class="col-sm-3" for="email_address"><?php echo smarty_function_txt(array('key'=>TEXT_EMAIL),$_smarty_tpl);?>
*</label>
			<div class="col-sm-9"><?php echo smarty_function_form(array('id'=>'email_address','type'=>'text','name'=>'email_address','params'=>'maxlength="40"','value'=>$_smarty_tpl->tpl_vars['email_address']->value),$_smarty_tpl);?>
</div>
		</div>
		<?php if ($_smarty_tpl->tpl_vars['show_title']->value == 1) {?>
		<div class="form-group">
			<label for="default_address_customers_title" class="col-sm-3"><?php echo smarty_function_txt(array('key'=>TEXT_CUSTOMERS_TITLE),$_smarty_tpl);?>
</label>
			<div class="col-sm-9">
				<?php if (is_array($_smarty_tpl->tpl_vars['title_data']->value) && smarty_modifier_count($_smarty_tpl->tpl_vars['title_data']->value)) {?>
				<?php echo smarty_function_form(array('params'=>'id="default_address_customers_title"','type'=>'select','name'=>'title','value'=>$_smarty_tpl->tpl_vars['title_data']->value,'default'=>$_smarty_tpl->tpl_vars['title']->value,'class'=>"form-control"),$_smarty_tpl);?>

				<?php } else { ?>
				<?php echo smarty_function_form(array('params'=>'id="default_address_customers_title"','type'=>'text','name'=>'title','value'=>$_smarty_tpl->tpl_vars['title']->value,'default'=>$_smarty_tpl->tpl_vars['title']->value,'class'=>"form-control"),$_smarty_tpl);?>

				<?php }?>
			</div>
		</div>
		<?php }?>
		<div class="form-group">
			<label class="col-sm-3" for="firstname"><?php echo smarty_function_txt(array('key'=>TEXT_FIRSTNAME),$_smarty_tpl);?>
*</label>
			<div class="col-sm-9"><?php echo smarty_function_form(array('id'=>'firstname','type'=>'text','name'=>'firstname','params'=>'maxlength="40"','value'=>$_smarty_tpl->tpl_vars['firstname']->value),$_smarty_tpl);?>
</div>
		</div>
		<div class="form-group">
			<label class="col-sm-3" for="lastname"><?php echo smarty_function_txt(array('key'=>TEXT_LASTNAME),$_smarty_tpl);?>
*</label>
			<div class="col-sm-9"><?php echo smarty_function_form(array('id'=>'lastname','type'=>'text','name'=>'lastname','params'=>'maxlength="40"','value'=>$_smarty_tpl->tpl_vars['lastname']->value),$_smarty_tpl);?>
</div>
		</div>
		<div class="form-group">
			<label class="col-sm-3" for="company"><?php echo smarty_function_txt(array('key'=>TEXT_COMPANY),$_smarty_tpl);?>
</label>
			<div class="col-sm-9"><?php echo smarty_function_form(array('id'=>'company','type'=>'text','name'=>'company','params'=>'maxlength="40"','value'=>$_smarty_tpl->tpl_vars['company']->value),$_smarty_tpl);?>
</div>
		</div>
		<div class="form-group">
			<label class="col-sm-3" for="telefone"><?php echo smarty_function_txt(array('key'=>TEXT_PHONE),$_smarty_tpl);?>
</label>
			<div class="col-sm-9"><?php echo smarty_function_form(array('id'=>'telefone','type'=>'text','name'=>'telefone','params'=>'maxlength="32"','value'=>$_smarty_tpl->tpl_vars['telefone']->value),$_smarty_tpl);?>
</div>
		</div>
		<div class="form-group">
			<label class="col-sm-3" for="mobile_phone"><?php echo smarty_function_txt(array('key'=>TEXT_MOBILE_PHONE),$_smarty_tpl);?>
</label>
			<div class="col-sm-9"><?php echo smarty_function_form(array('id'=>'mobile_phone','type'=>'text','name'=>'mobile_phone','params'=>'maxlength="32"','value'=>$_smarty_tpl->tpl_vars['mobile_phone']->value),$_smarty_tpl);?>
</div>
		</div>
		<div class="form-group">
			<label class="col-sm-3" for="order_id"><?php echo smarty_function_txt(array('key'=>TEXT_ORDER_NUMBER),$_smarty_tpl);?>
</label>
			<div class="col-sm-9"><?php echo smarty_function_form(array('id'=>'order_id','type'=>'text','name'=>'order_id','params'=>'maxlength="40"','value'=>$_smarty_tpl->tpl_vars['order_id']->value),$_smarty_tpl);?>
</div>
		</div>
		<div class="form-group">
			<label class="col-sm-3" for="customer_message"><?php echo smarty_function_txt(array('key'=>TEXT_MESSAGE),$_smarty_tpl);?>
</label>
			<div class="col-sm-9"><?php echo smarty_function_form(array('class'=>"form-control",'id'=>'customer_message','type'=>'textarea','name'=>'customer_message','params'=>'cols="50" rows="15"','value'=>$_smarty_tpl->tpl_vars['customer_message']->value),$_smarty_tpl);?>
</div>
		</div>

		<div class="form-group">
			<label class="col-sm-3" for="customers_op_in"></label>
			<div class="col-sm-9">

				<div class="checkbox">
					<label> <?php echo smarty_function_form(array('type'=>'checkbox','name'=>'contact_opt_in','value'=>1),$_smarty_tpl);?>

						<?php echo smarty_function_txt(array('key'=>TEXT_CONTACT_OPT_IN),$_smarty_tpl);?>
 </label>
				</div>

			</div>
		</div>


			<?php if ((defined('_STORE_CAPTCHA') ? constant('_STORE_CAPTCHA') : null) == 'ReCaptcha') {?>
				<div class="row">
					<div class="form-group pull-right">
						<div class="col-sm-9 recaptcha-wrapper"></div>
					</div>
				</div>
			<?php } elseif ((defined('_STORE_CAPTCHA') ? constant('_STORE_CAPTCHA') : null) == 'Standard') {?>
		<div class="form-group">
			<label class="col-sm-3" for="captcha_code"><?php echo smarty_function_txt(array('key'=>TEXT_CAPTCHA),$_smarty_tpl);?>
*</label>
			<div class="col-sm-9">
				<p id="captcha-img">
					<img id="captchaImg" src="data:image/gif;base64,R0lGODlhAQABAID/AP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==" alt="<?php echo smarty_function_txt(array('key'=>TEXT_CAPTCHA),$_smarty_tpl);?>
"
						class="img-responsive img-thumbnail" />
				</p>
				<?php echo smarty_function_form(array('id'=>'captcha_code','type'=>'text','name'=>'captcha','params'=>'maxlength="30"'),$_smarty_tpl);?>

			</div>
		</div>
			<?php }?>


		<div class="form-submit pull-right text-right">
			<?php if ($_smarty_tpl->tpl_vars['show_privacy']->value == 1) {?> <?php echo smarty_function_content(array('cont_id'=>2,'is_id'=>'false'),$_smarty_tpl);?>
 <?php if ($_smarty_tpl->tpl_vars['show_privacy_type']->value == 1) {?>
			<div class="checkbox">
				<label>
					<?php echo smarty_function_form(array('type'=>'checkbox','name'=>'privacy','value'=>1),$_smarty_tpl);?>

					<?php echo smarty_function_txt(array('key'=>TEXT_PRIVACY_TEXT_INFO_1),$_smarty_tpl);?>
 <a href="<?php echo $_smarty_tpl->tpl_vars['privacy_link']->value;?>
" target="_blank"><?php echo smarty_function_txt(array('key'=>TEXT_PRIVACY_TEXT),$_smarty_tpl);?>
</a> <?php echo smarty_function_txt(array('key'=>TEXT_PRIVACY_TEXT_INFO_2),$_smarty_tpl);
echo smarty_function_txt(array('key'=>TEXT_DOT),$_smarty_tpl);?>

				</label>
			</div>
			<?php } else { ?>
				<p><?php echo smarty_function_txt(array('key'=>TEXT_PRIVACY_TEXT_INFO),$_smarty_tpl);?>
 <a href="<?php echo $_smarty_tpl->tpl_vars['privacy_link']->value;?>
" target="_blank"><?php echo smarty_function_txt(array('key'=>TEXT_PRIVACY_TEXT),$_smarty_tpl);?>
</a><?php echo smarty_function_txt(array('key'=>TEXT_DOT),$_smarty_tpl);?>
</p>
			<?php }?> <?php }?>
			<p>

				<button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-envelope"></span> <?php echo smarty_function_txt(array('key'=>BUTTON_SUBMIT),$_smarty_tpl);?>
</button>
			</p>
		</div>

		<div class="clearfix visible-xs"></div>

		<p class="required pull-right-xs"><?php echo smarty_function_txt(array('key'=>TEXT_MUST),$_smarty_tpl);?>
</p>
		<div class="clearfix"></div>


	</fieldset>
	<?php echo smarty_function_form(array('type'=>'formend'),$_smarty_tpl);?>
 <?php }?>
</div>
<!-- #contact -->
<?php echo '<script'; ?>
>

	document.addEventListener('DOMContentLoaded', function () {
		const captchaImg = document.getElementById('captchaImg');
		console.log(captchaImg);
		if(captchaImg)
		{
			setTimeout(function(captchaImg){ captchaImg.src = '<?php echo $_smarty_tpl->tpl_vars['captcha_link']->value;?>
'; }, 500, captchaImg);
		}
	});

<?php echo '</script'; ?>
><?php }
}
