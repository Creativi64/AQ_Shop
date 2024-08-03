<?php
/* Smarty version 5.1.0, created on 2024-08-03 01:15:25
  from 'file:/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/forms/contact.html' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.1.0',
  'unifunc' => 'content_66ad688d1947c7_69447531',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'a6e7eecaf40a0d263774d7e4863b8387eea262db' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/forms/contact.html',
      1 => 1697144119,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:xtCore/pages/content.html' => 1,
  ),
))) {
function content_66ad688d1947c7_69447531 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/forms';
?><div id="contact">
	<?php echo $_smarty_tpl->getValue('message');?>
 <?php if ($_smarty_tpl->getValue('show_form') == 'true') {?>
	<p class="alert alert-info">
		<i class="fa fa-info-circle"></i> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_CONTACT_INTRO), $_smarty_tpl);?>

	</p>
	<?php }?> <?php $_smarty_tpl->renderSubTemplate("file:xtCore/pages/content.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?> <?php if ($_smarty_tpl->getValue('show_form') == 'true') {?> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('class'=>"form-horizontal",'type'=>'form','name'=>'login','action'=>'dynamic','link_params'=>'getParams','method'=>'post','conn'=>'SSL'), $_smarty_tpl);?>
 <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'hidden','name'=>'action','value'=>'send'), $_smarty_tpl);?>

	<fieldset>
		<legend><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_CONTACT_PAGE), $_smarty_tpl);?>
</legend>
		<div class="form-group">
			<label class="col-sm-3" for="email_address"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_EMAIL), $_smarty_tpl);?>
*</label>
			<div class="col-sm-9"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('id'=>'email_address','type'=>'text','name'=>'email_address','params'=>'maxlength="40"','value'=>$_smarty_tpl->getValue('email_address')), $_smarty_tpl);?>
</div>
		</div>
		<?php if ($_smarty_tpl->getValue('show_title') == 1) {?>
		<div class="form-group">
			<label for="default_address_customers_title" class="col-sm-3"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_CUSTOMERS_TITLE), $_smarty_tpl);?>
</label>
			<div class="col-sm-9">
				<?php if (is_array($_smarty_tpl->getValue('title_data')) && $_smarty_tpl->getSmarty()->getModifierCallback('count')($_smarty_tpl->getValue('title_data'))) {?>
				<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('params'=>'id="default_address_customers_title"','type'=>'select','name'=>'title','value'=>$_smarty_tpl->getValue('title_data'),'default'=>$_smarty_tpl->getValue('title'),'class'=>"form-control"), $_smarty_tpl);?>

				<?php } else { ?>
				<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('params'=>'id="default_address_customers_title"','type'=>'text','name'=>'title','value'=>$_smarty_tpl->getValue('title'),'default'=>$_smarty_tpl->getValue('title'),'class'=>"form-control"), $_smarty_tpl);?>

				<?php }?>
			</div>
		</div>
		<?php }?>
		<div class="form-group">
			<label class="col-sm-3" for="firstname"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_FIRSTNAME), $_smarty_tpl);?>
*</label>
			<div class="col-sm-9"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('id'=>'firstname','type'=>'text','name'=>'firstname','params'=>'maxlength="40"','value'=>$_smarty_tpl->getValue('firstname')), $_smarty_tpl);?>
</div>
		</div>
		<div class="form-group">
			<label class="col-sm-3" for="lastname"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_LASTNAME), $_smarty_tpl);?>
*</label>
			<div class="col-sm-9"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('id'=>'lastname','type'=>'text','name'=>'lastname','params'=>'maxlength="40"','value'=>$_smarty_tpl->getValue('lastname')), $_smarty_tpl);?>
</div>
		</div>
		<div class="form-group">
			<label class="col-sm-3" for="company"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_COMPANY), $_smarty_tpl);?>
</label>
			<div class="col-sm-9"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('id'=>'company','type'=>'text','name'=>'company','params'=>'maxlength="40"','value'=>$_smarty_tpl->getValue('company')), $_smarty_tpl);?>
</div>
		</div>
		<div class="form-group">
			<label class="col-sm-3" for="telefone"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_PHONE), $_smarty_tpl);?>
</label>
			<div class="col-sm-9"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('id'=>'telefone','type'=>'text','name'=>'telefone','params'=>'maxlength="32"','value'=>$_smarty_tpl->getValue('telefone')), $_smarty_tpl);?>
</div>
		</div>
		<div class="form-group">
			<label class="col-sm-3" for="mobile_phone"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_MOBILE_PHONE), $_smarty_tpl);?>
</label>
			<div class="col-sm-9"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('id'=>'mobile_phone','type'=>'text','name'=>'mobile_phone','params'=>'maxlength="32"','value'=>$_smarty_tpl->getValue('mobile_phone')), $_smarty_tpl);?>
</div>
		</div>
		<div class="form-group">
			<label class="col-sm-3" for="order_id"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_ORDER_NUMBER), $_smarty_tpl);?>
</label>
			<div class="col-sm-9"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('id'=>'order_id','type'=>'text','name'=>'order_id','params'=>'maxlength="40"','value'=>$_smarty_tpl->getValue('order_id')), $_smarty_tpl);?>
</div>
		</div>
		<div class="form-group">
			<label class="col-sm-3" for="customer_message"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_MESSAGE), $_smarty_tpl);?>
</label>
			<div class="col-sm-9"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('class'=>"form-control",'id'=>'customer_message','type'=>'textarea','name'=>'customer_message','params'=>'cols="50" rows="15"','value'=>$_smarty_tpl->getValue('customer_message')), $_smarty_tpl);?>
</div>
		</div>

		<div class="form-group">
			<label class="col-sm-3" for="customers_op_in"></label>
			<div class="col-sm-9">

				<div class="checkbox">
					<label> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'checkbox','name'=>'contact_opt_in','value'=>1), $_smarty_tpl);?>

						<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_CONTACT_OPT_IN), $_smarty_tpl);?>
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
			<label class="col-sm-3" for="captcha_code"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_CAPTCHA), $_smarty_tpl);?>
*</label>
			<div class="col-sm-9">
				<p id="captcha-img">
					<img id="captchaImg" src="data:image/gif;base64,R0lGODlhAQABAID/AP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==" alt="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_CAPTCHA), $_smarty_tpl);?>
"
						class="img-responsive img-thumbnail" />
				</p>
				<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('id'=>'captcha_code','type'=>'text','name'=>'captcha','params'=>'maxlength="30"'), $_smarty_tpl);?>

			</div>
		</div>
			<?php }?>


		<div class="form-submit pull-right text-right">
			<?php if ($_smarty_tpl->getValue('show_privacy') == 1) {?> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('content')->handle(array('cont_id'=>2,'is_id'=>'false'), $_smarty_tpl);?>
 <?php if ($_smarty_tpl->getValue('show_privacy_type') == 1) {?>
			<div class="checkbox">
				<label>
					<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'checkbox','name'=>'privacy','value'=>1), $_smarty_tpl);?>

					<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_PRIVACY_TEXT_INFO_1), $_smarty_tpl);?>
 <a href="<?php echo $_smarty_tpl->getValue('privacy_link');?>
" target="_blank"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_PRIVACY_TEXT), $_smarty_tpl);?>
</a> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_PRIVACY_TEXT_INFO_2), $_smarty_tpl);
echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_DOT), $_smarty_tpl);?>

				</label>
			</div>
			<?php } else { ?>
				<p><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_PRIVACY_TEXT_INFO), $_smarty_tpl);?>
 <a href="<?php echo $_smarty_tpl->getValue('privacy_link');?>
" target="_blank"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_PRIVACY_TEXT), $_smarty_tpl);?>
</a><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_DOT), $_smarty_tpl);?>
</p>
			<?php }?> <?php }?>
			<p>

				<button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-envelope"></span> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>BUTTON_SUBMIT), $_smarty_tpl);?>
</button>
			</p>
		</div>

		<div class="clearfix visible-xs"></div>

		<p class="required pull-right-xs"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_MUST), $_smarty_tpl);?>
</p>
		<div class="clearfix"></div>


	</fieldset>
	<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'formend'), $_smarty_tpl);?>
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
			setTimeout(function(captchaImg){ captchaImg.src = '<?php echo $_smarty_tpl->getValue('captcha_link');?>
'; }, 500, captchaImg);
		}
	});

<?php echo '</script'; ?>
><?php }
}
