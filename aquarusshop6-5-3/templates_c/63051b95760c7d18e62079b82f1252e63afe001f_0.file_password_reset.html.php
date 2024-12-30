<?php
/* Smarty version 5.4.1, created on 2024-12-03 09:05:10
  from 'file:/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages/password_reset.html' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.4.1',
  'unifunc' => 'content_674ebbb65e4693_98832531',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '63051b95760c7d18e62079b82f1252e63afe001f' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages/password_reset.html',
      1 => 1697144119,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_674ebbb65e4693_98832531 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages';
?><div id="passwort-reset">
	<h1><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_PASSWORD_RESET_PAGE), $_smarty_tpl);?>
</h1>
	<?php echo $_smarty_tpl->getValue('message');?>

	<?php if ($_smarty_tpl->getValue('captcha_show') != 'false') {?>
	<div class="box">
		<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'form','name'=>'login','action'=>'dynamic','link_params'=>'page_action=password_reset','method'=>'post','conn'=>'SSL'), $_smarty_tpl);?>


		<?php if ($_smarty_tpl->getValue('captcha') == 'true') {?>
		<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'hidden','name'=>'action','value'=>'check_captcha'), $_smarty_tpl);?>

		<?php }?>

		<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'hidden','name'=>'link_target','value'=>'index'), $_smarty_tpl);?>


		<?php if ($_smarty_tpl->getValue('captcha') == 'true') {?>
			<p><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_PASSWORD_RESET), $_smarty_tpl);?>
</p>
		<?php } else { ?>
			<p><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_PASSWORD_RESET_WITHOUT_CAPTCHA), $_smarty_tpl);?>
</p>
		<?php }?>

		<?php if ($_smarty_tpl->getValue('captcha') == 'true') {?>
			<?php if ($_smarty_tpl->getValue('recaptcha') == 'true' || $_smarty_tpl->getValue('recaptcha') == true) {?>

			<div class="row">
				<div class="form-group">
					<div class="col-sm-9 recaptcha-wrapper"></div>
				</div>
			</div>

			<?php } else { ?>

			<p id="captcha-img"><img id="captchaImg" src="data:image/gif;base64,R0lGODlhAQABAID/AP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==" alt="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>'text_captcha'), $_smarty_tpl);?>
" class="img-responsive img-thumbnail" /></p>

			<div class="form-group">
				<label><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>'text_captcha'), $_smarty_tpl);?>
</label>
				<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'text','name'=>'captcha','maxlength'=>'30'), $_smarty_tpl);?>

			</div>
			<?php }?>
		<?php }?>

        <div class="form-group">
		    <label><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>'text_email'), $_smarty_tpl);?>
</label>
		    <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'text','name'=>'email','maxlength'=>'50'), $_smarty_tpl);?>

        </div>

		<p class="right">
            <button type="submit" class="btn btn-primary"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>BUTTON_NEXT), $_smarty_tpl);?>
</button>
        </p>

		<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'formend'), $_smarty_tpl);?>

	</div><!-- .box -->
	<?php }?>
</div><!-- #passwort-reset -->
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
