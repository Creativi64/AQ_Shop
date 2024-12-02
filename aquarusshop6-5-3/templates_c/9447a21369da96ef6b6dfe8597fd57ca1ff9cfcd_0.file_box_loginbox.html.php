<?php
/* Smarty version 5.1.0, created on 2024-12-02 18:49:39
  from 'file:/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/boxes/box_loginbox.html' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.1.0',
  'unifunc' => 'content_674df333326098_83162507',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '9447a21369da96ef6b6dfe8597fd57ca1ff9cfcd' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/boxes/box_loginbox.html',
      1 => 1697144118,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_674df333326098_83162507 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/boxes';
?><div id="box-loginbox" class="clearfix">
    <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'form','name'=>'create_account','action'=>'customer','paction'=>'login','method'=>'post','conn'=>'SSL'), $_smarty_tpl);?>

        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'hidden','name'=>'action','value'=>'login'), $_smarty_tpl);?>

        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'hidden','name'=>'link_target','value'=>'index'), $_smarty_tpl);?>

        <div class="form-group">
            <label for="login_email"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>'text_email'), $_smarty_tpl);?>
*</label>
            <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'email','id'=>"login_email",'name'=>'email','maxlength'=>'50'), $_smarty_tpl);?>

        </div>
        <div class="form-group">
            <label for="login_password"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>'text_password'), $_smarty_tpl);?>
*</label>
            <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'password','id'=>"login_password",'name'=>'password','maxlength'=>'30'), $_smarty_tpl);?>

            <p class="help-block"><a href="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('link')->handle(array('page'=>'customer','paction'=>'password_reset','conn'=>'SSL'), $_smarty_tpl);?>
"><i class="fa fa-envelope"></i> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>'text_link_lostpassword'), $_smarty_tpl);?>
</a></p>
        </div>
        
        <p class="required pull-left pull-none-xs"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_MUST), $_smarty_tpl);?>
</p>
        
        <p class="pull-right pull-none-xs">
        <a href="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('link')->handle(array('page'=>'customer','paction'=>'login','params'=>'form=register','conn'=>'SSL'), $_smarty_tpl);?>
" class="btn btn-default">
                <span class="hidden-xs"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>TEXT_NEW_CUSTOMER), $_smarty_tpl);?>
</span>
                <span class="visible-xs"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>BUTTON_REGISTER), $_smarty_tpl);?>
</span>
            </a>
            <button type="submit" class="btn btn-success">
                <i class="fa fa-sign-in"></i>
                <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('txt')->handle(array('key'=>BUTTON_LOGIN), $_smarty_tpl);?>

            </button>
        </p>
    <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('form')->handle(array('type'=>'formend'), $_smarty_tpl);?>

    <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>'box_login_tpl'), $_smarty_tpl);?>

</div><!-- #box-loginbox -->
<?php }
}
