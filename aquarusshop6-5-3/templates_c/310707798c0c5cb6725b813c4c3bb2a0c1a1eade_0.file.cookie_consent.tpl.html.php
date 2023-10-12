<?php
/* Smarty version 4.3.0, created on 2023-06-19 13:53:20
  from '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/plugins/xt_cookie_consent/templates/cookie_consent.tpl.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.0',
  'unifunc' => 'content_649041b03c5a56_42138567',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '310707798c0c5cb6725b813c4c3bb2a0c1a1eade' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/plugins/xt_cookie_consent/templates/cookie_consent.tpl.html',
      1 => 1687171152,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_649041b03c5a56_42138567 (Smarty_Internal_Template $_smarty_tpl) {
?>
    <div id="cookie-consent" class="coc-<?php echo $_smarty_tpl->tpl_vars['theme_id']->value;?>
" style="display:none">
        <div class="coc-wrapper">
            <div class="coc-title"><h2><?php echo $_smarty_tpl->tpl_vars['content_title']->value;?>
</h2></div>
            <div class="coc-body">
                <?php echo $_smarty_tpl->tpl_vars['content_body']->value;?>
<br /><span class="coc-accept-link button" onclick='javascript:xt_cookie_consent_accept(true, <?php echo $_smarty_tpl->tpl_vars['lifetime']->value;?>
, <?php echo $_smarty_tpl->tpl_vars['secure']->value;?>
)'><?php echo (defined('TEXT_XT_COC_ACCEPT') ? constant('TEXT_XT_COC_ACCEPT') : null);?>
</span><span class="coc-accept-link button" onclick='javascript:xt_cookie_consent_accept(false, <?php echo $_smarty_tpl->tpl_vars['lifetime']->value;?>
, <?php echo $_smarty_tpl->tpl_vars['secure']->value;?>
)'><?php echo (defined('TEXT_XT_COC_DENY') ? constant('TEXT_XT_COC_DENY') : null);?>
</span><span class="coc-more-link button"><?php echo $_smarty_tpl->tpl_vars['more_link']->value;?>
</span><div style="clear:both"></div>
            </div>
        </div>
    </div>

    <?php echo '<script'; ?>
>document.addEventListener("DOMContentLoaded", function () { try { xt_cookie_consent_init(<?php echo $_smarty_tpl->tpl_vars['theme']->value;?>
); } catch(e) { console.error(e) } } );<?php echo '</script'; ?>
>
    <?php if ($_smarty_tpl->tpl_vars['open']->value) {
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
