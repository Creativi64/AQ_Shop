<?php
/* Smarty version 4.3.2, created on 2024-04-20 19:08:04
  from '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages/password_reset.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_6623f674ad5807_68782179',
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
),false)) {
function content_6623f674ad5807_68782179 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.txt.php','function'=>'smarty_function_txt',),1=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.form.php','function'=>'smarty_function_form',),));
?>
<div id="passwort-reset">
	<h1><?php echo smarty_function_txt(array('key'=>TEXT_PASSWORD_RESET_PAGE),$_smarty_tpl);?>
</h1>
	<?php echo $_smarty_tpl->tpl_vars['message']->value;?>

	<?php if ($_smarty_tpl->tpl_vars['captcha_show']->value != 'false') {?>
	<div class="box">
		<?php echo smarty_function_form(array('type'=>'form','name'=>'login','action'=>'dynamic','link_params'=>'page_action=password_reset','method'=>'post','conn'=>'SSL'),$_smarty_tpl);?>


		<?php if ($_smarty_tpl->tpl_vars['captcha']->value == 'true') {?>
		<?php echo smarty_function_form(array('type'=>'hidden','name'=>'action','value'=>'check_captcha'),$_smarty_tpl);?>

		<?php }?>

		<?php echo smarty_function_form(array('type'=>'hidden','name'=>'link_target','value'=>'index'),$_smarty_tpl);?>


		<?php if ($_smarty_tpl->tpl_vars['captcha']->value == 'true') {?>
			<p><?php echo smarty_function_txt(array('key'=>TEXT_PASSWORD_RESET),$_smarty_tpl);?>
</p>
		<?php } else { ?>
			<p><?php echo smarty_function_txt(array('key'=>TEXT_PASSWORD_RESET_WITHOUT_CAPTCHA),$_smarty_tpl);?>
</p>
		<?php }?>

		<?php if ($_smarty_tpl->tpl_vars['captcha']->value == 'true') {?>
			<?php if ($_smarty_tpl->tpl_vars['recaptcha']->value == 'true' || $_smarty_tpl->tpl_vars['recaptcha']->value == true) {?>

			<div class="row">
				<div class="form-group">
					<div class="col-sm-9 recaptcha-wrapper"></div>
				</div>
			</div>

			<?php } else { ?>

			<p id="captcha-img"><img id="captchaImg" src="data:image/gif;base64,R0lGODlhAQABAID/AP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==" alt="<?php echo smarty_function_txt(array('key'=>'text_captcha'),$_smarty_tpl);?>
" class="img-responsive img-thumbnail" /></p>

			<div class="form-group">
				<label><?php echo smarty_function_txt(array('key'=>'text_captcha'),$_smarty_tpl);?>
</label>
				<?php echo smarty_function_form(array('type'=>'text','name'=>'captcha','maxlength'=>'30'),$_smarty_tpl);?>

			</div>
			<?php }?>
		<?php }?>

        <div class="form-group">
		    <label><?php echo smarty_function_txt(array('key'=>'text_email'),$_smarty_tpl);?>
</label>
		    <?php echo smarty_function_form(array('type'=>'text','name'=>'email','maxlength'=>'50'),$_smarty_tpl);?>

        </div>

		<p class="right">
            <button type="submit" class="btn btn-primary"><?php echo smarty_function_txt(array('key'=>BUTTON_NEXT),$_smarty_tpl);?>
</button>
        </p>

		<?php echo smarty_function_form(array('type'=>'formend'),$_smarty_tpl);?>

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
			setTimeout(function(captchaImg){ captchaImg.src = '<?php echo $_smarty_tpl->tpl_vars['captcha_link']->value;?>
'; }, 500, captchaImg);
		}
	});

<?php echo '</script'; ?>
><?php }
}
