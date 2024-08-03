<?php
/* Smarty version 5.1.0, created on 2024-08-03 01:07:42
  from 'file:/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_recaptcha/templates/g-recaptcha-inv.tpl.html' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.1.0',
  'unifunc' => 'content_66ad66be1b7115_23737746',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '9605c340871f79f65485ab6eddd69ed651d19d8e' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_recaptcha/templates/g-recaptcha-inv.tpl.html',
      1 => 1722634754,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_66ad66be1b7115_23737746 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_recaptcha/templates';
echo '<script'; ?>
>
    var recaptchaSubmit = function(token) {
        //console.log(token);
        return true;
    };
    var recaptchaExpired = function(data) {
        //console.log(data);
        return true;
    };
    var recaptchaError = function(data) {
        //console.log(data);
        return true;
    };


    function recaptchaOnload()
    {
        //console.log("recaptcha api loaded");
        $.each($(".recaptcha-wrapper"), function(key, value ) {
            //console.log( key , value );
            grecaptcha.render(
                value,
                {
                    "sitekey": "<?php echo $_smarty_tpl->getValue('public_key');?>
",
                    "callback":  recaptchaSubmit,
                    "expired-callback": recaptchaExpired,
                    "error-callback": recaptchaError,
                    "size": "invisible",
                    "badge": "bottomleft"
                }
            );
            grecaptcha.execute();
        });
    }
<?php echo '</script'; ?>
>

<?php echo '<script'; ?>
 src="https://www.google.com/recaptcha/api.js?onload=recaptchaOnload&render=explicit&hl=<?php echo $_smarty_tpl->getValue('language');?>
" async defer><?php echo '</script'; ?>
><?php }
}
