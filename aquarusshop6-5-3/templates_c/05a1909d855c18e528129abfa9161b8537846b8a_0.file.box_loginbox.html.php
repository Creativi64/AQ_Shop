<?php
/* Smarty version 4.3.0, created on 2023-06-19 13:53:20
  from '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/templates/xt_responsive/xtCore/boxes/box_loginbox.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.0',
  'unifunc' => 'content_649041b030dd30_48297743',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '05a1909d855c18e528129abfa9161b8537846b8a' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/templates/xt_responsive/xtCore/boxes/box_loginbox.html',
      1 => 1687006095,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_649041b030dd30_48297743 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/smarty/xt_plugins/function.form.php','function'=>'smarty_function_form',),1=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/smarty/xt_plugins/function.txt.php','function'=>'smarty_function_txt',),2=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/smarty/xt_plugins/function.link.php','function'=>'smarty_function_link',),3=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/smarty/xt_plugins/function.hook.php','function'=>'smarty_function_hook',),));
?>
<div id="box-loginbox" class="clearfix">
    <?php echo smarty_function_form(array('type'=>'form','name'=>'create_account','action'=>'customer','paction'=>'login','method'=>'post','conn'=>'SSL'),$_smarty_tpl);?>

        <?php echo smarty_function_form(array('type'=>'hidden','name'=>'action','value'=>'login'),$_smarty_tpl);?>

        <?php echo smarty_function_form(array('type'=>'hidden','name'=>'link_target','value'=>'index'),$_smarty_tpl);?>

        <div class="form-group">
            <label for="login_email"><?php echo smarty_function_txt(array('key'=>'text_email'),$_smarty_tpl);?>
*</label>
            <?php echo smarty_function_form(array('type'=>'email','id'=>"login_email",'name'=>'email','maxlength'=>'50'),$_smarty_tpl);?>

        </div>
        <div class="form-group">
            <label for="login_password"><?php echo smarty_function_txt(array('key'=>'text_password'),$_smarty_tpl);?>
*</label>
            <?php echo smarty_function_form(array('type'=>'password','id'=>"login_password",'name'=>'password','maxlength'=>'30'),$_smarty_tpl);?>

            <p class="help-block"><a href="<?php echo smarty_function_link(array('page'=>'customer','paction'=>'password_reset','conn'=>'SSL'),$_smarty_tpl);?>
"><i class="fa fa-envelope"></i> <?php echo smarty_function_txt(array('key'=>'text_link_lostpassword'),$_smarty_tpl);?>
</a></p>
        </div>
        
        <p class="required pull-left pull-none-xs"><?php echo smarty_function_txt(array('key'=>TEXT_MUST),$_smarty_tpl);?>
</p>
        
        <p class="pull-right pull-none-xs">
        <a href="<?php echo smarty_function_link(array('page'=>'customer','paction'=>'login','params'=>'form=register','conn'=>'SSL'),$_smarty_tpl);?>
" class="btn btn-default">
                <span class="hidden-xs"><?php echo smarty_function_txt(array('key'=>TEXT_NEW_CUSTOMER),$_smarty_tpl);?>
</span>
                <span class="visible-xs"><?php echo smarty_function_txt(array('key'=>BUTTON_REGISTER),$_smarty_tpl);?>
</span>
            </a>
            <button type="submit" class="btn btn-success">
                <i class="fa fa-sign-in"></i>
                <?php echo smarty_function_txt(array('key'=>BUTTON_LOGIN),$_smarty_tpl);?>

            </button>
        </p>
    <?php echo smarty_function_form(array('type'=>'formend'),$_smarty_tpl);?>

    <?php echo smarty_function_hook(array('key'=>'box_login_tpl'),$_smarty_tpl);?>

</div><!-- #box-loginbox -->
<?php }
}
