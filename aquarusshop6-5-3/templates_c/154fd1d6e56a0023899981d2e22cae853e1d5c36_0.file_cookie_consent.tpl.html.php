<?php
/* Smarty version 5.1.0, created on 2024-09-09 19:14:52
  from 'file:/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_cookie_consent/templates/cookie_consent.tpl.html' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.1.0',
  'unifunc' => 'content_66df2d0cafcdc5_02868315',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '154fd1d6e56a0023899981d2e22cae853e1d5c36' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_cookie_consent/templates/cookie_consent.tpl.html',
      1 => 1722634731,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_66df2d0cafcdc5_02868315 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/homepages/2/d41324517/htdocs/aquarus_shop/plugins/xt_cookie_consent/templates';
?>
    <div id="cookie-consent" class="coc-<?php echo $_smarty_tpl->getValue('theme_id');?>
" style="display:none">
        <div class="coc-wrapper">
            <div class="coc-title"><h2><?php echo $_smarty_tpl->getValue('content_title');?>
</h2></div>
            <div class="coc-body">
                <?php echo $_smarty_tpl->getValue('content_body');?>
<br /><span class="coc-accept-link button" onclick='javascript:xt_cookie_consent_accept(true, <?php echo $_smarty_tpl->getValue('lifetime');?>
, <?php echo $_smarty_tpl->getValue('secure');?>
)'><?php echo (defined('TEXT_XT_COC_ACCEPT') ? constant('TEXT_XT_COC_ACCEPT') : null);?>
</span><span class="coc-accept-link button" onclick='javascript:xt_cookie_consent_accept(false, <?php echo $_smarty_tpl->getValue('lifetime');?>
, <?php echo $_smarty_tpl->getValue('secure');?>
)'><?php echo (defined('TEXT_XT_COC_DENY') ? constant('TEXT_XT_COC_DENY') : null);?>
</span><span class="coc-more-link button"><?php echo $_smarty_tpl->getValue('more_link');?>
</span><div style="clear:both"></div>
            </div>
        </div>
    </div>

    <?php echo '<script'; ?>
>document.addEventListener("DOMContentLoaded", function () { try { xt_cookie_consent_init(<?php echo $_smarty_tpl->getValue('theme');?>
); } catch(e) { console.error(e) } } );<?php echo '</script'; ?>
>
    <?php if ($_smarty_tpl->getValue('open')) {
echo '<script'; ?>
>document.addEventListener("DOMContentLoaded", function () { try { xt_cookie_consent_show(); } catch(e) { console.error(e) } } );<?php echo '</script'; ?>
>
    <?php } else {
echo '<script'; ?>
>document.addEventListener("DOMContentLoaded", function () { try { processCookieConsentInitFunctions(); } catch(e) { console.error(e) } } );<?php echo '</script'; ?>
>
    <?php }
}
}
