<?php
/* Smarty version 4.3.2, created on 2024-08-02 22:56:57
  from '/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_recaptcha/templates/g-recaptcha-inv.tpl.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_66ad48198cd467_49017573',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '7dbedde66ff7ce8adcc8fbbdf7769cc47357018d' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_recaptcha/templates/g-recaptcha-inv.tpl.html',
      1 => 1697144057,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_66ad48198cd467_49017573 (Smarty_Internal_Template $_smarty_tpl) {
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
                    "sitekey": "<?php echo $_smarty_tpl->tpl_vars['public_key']->value;?>
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
 src="https://www.google.com/recaptcha/api.js?onload=recaptchaOnload&render=explicit&hl=<?php echo $_smarty_tpl->tpl_vars['language']->value;?>
" async defer><?php echo '</script'; ?>
><?php }
}
