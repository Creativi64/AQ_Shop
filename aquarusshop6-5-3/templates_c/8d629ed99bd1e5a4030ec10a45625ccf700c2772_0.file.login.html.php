<?php
/* Smarty version 4.3.2, created on 2024-07-17 21:26:06
  from '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages/login.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_66981ace87b265_15949245',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '8d629ed99bd1e5a4030ec10a45625ccf700c2772' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/templates/xt_responsive_AQ/xtCore/pages/login.html',
      1 => 1697144119,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_66981ace87b265_15949245 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.link.php','function'=>'smarty_function_link',),1=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.txt.php','function'=>'smarty_function_txt',),2=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.hook.php','function'=>'smarty_function_hook',),3=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.form.php','function'=>'smarty_function_form',),4=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.content.php','function'=>'smarty_function_content',),));
?>

<?php if ($_GET['form'] == "register" || $_POST['form'] == "register") {?>
    <a href="<?php echo smarty_function_link(array('page'=>'customer','paction'=>'login'),$_smarty_tpl);?>
" class="btn btn-success pull-right">
        <i class="fa fa-sign-in"></i> <?php echo smarty_function_txt(array('key'=>BUTTON_LOGIN),$_smarty_tpl);?>

    </a>
<?php }?>

<h1><?php echo smarty_function_txt(array('key'=>TEXT_PAGE_TITLE_LOGIN),$_smarty_tpl);?>
</h1>

<?php echo $_smarty_tpl->tpl_vars['message']->value;?>

<?php echo smarty_function_hook(array('key'=>'login_top'),$_smarty_tpl);?>


<div class="row">
    <?php if ($_GET['form'] != "register" && $_POST['form'] != "register") {?>

        <?php echo smarty_function_hook(array('key'=>'login_returning_top'),$_smarty_tpl);?>

        <div class="col col-md-6 col-md-push-6">
            <div class="panel panel-default">
                <p class="panel-heading"><strong><i class="fa fa-user"></i> <?php echo smarty_function_txt(array('key'=>TEXT_RETURNING),$_smarty_tpl);?>
</strong></p>
                <div id="loginbox" class="clearfix panel-body">
                    <p><?php echo smarty_function_txt(array('key'=>TEXT_TXT_RETURNING),$_smarty_tpl);?>
</p>
                    <?php echo smarty_function_form(array('type'=>'form','name'=>'login','action'=>'dynamic','link_params'=>'page_action=login','method'=>'post','conn'=>'SSL'),$_smarty_tpl);?>

                    <?php echo smarty_function_form(array('type'=>'hidden','name'=>'action','value'=>'login'),$_smarty_tpl);?>

                    <?php echo smarty_function_form(array('type'=>'hidden','name'=>'link_target','value'=>'index'),$_smarty_tpl);?>


                    <div class="form-group">
                        <label for="email"><?php echo smarty_function_txt(array('key'=>'text_email'),$_smarty_tpl);?>
*</label>
                        <?php echo smarty_function_form(array('id'=>'email','type'=>'text','name'=>'email','maxlength'=>'50'),$_smarty_tpl);?>

                    </div>

                    <div class="form-group">
                        <label for="password"><?php echo smarty_function_txt(array('key'=>'text_password'),$_smarty_tpl);?>
*</label>
                        <?php echo smarty_function_form(array('id'=>'password','type'=>'password','name'=>'password','maxlength'=>'30'),$_smarty_tpl);?>

                    </div>

                    <p class="required pull-left"><?php echo smarty_function_txt(array('key'=>TEXT_MUST),$_smarty_tpl);?>
</p>

                    <p class="pull-right">
                        <a href="<?php echo smarty_function_link(array('page'=>'customer','paction'=>'password_reset','conn'=>'SSL'),$_smarty_tpl);?>
" class="btn btn-default"><?php echo smarty_function_txt(array('key'=>'text_link_lostpassword'),$_smarty_tpl);?>
</a>
                        <button type="submit" class="btn btn-success">
                            <i class="fa fa-sign-in"></i> <?php echo smarty_function_txt(array('key'=>BUTTON_LOGIN),$_smarty_tpl);?>

                        </button>
                    </p>

                    <?php echo smarty_function_form(array('type'=>'formend'),$_smarty_tpl);?>

                </div><!-- #loginbox -->
            </div>
        </div><!-- .col -->
        <div class="col col-md-6 col-md-pull-6">
            <div class="panel panel-default">
                <p class="panel-heading"><strong><span class="glyphicon glyphicon-share-alt"></span> <?php echo smarty_function_txt(array('key'=>TEXT_NEW_CUSTOMER),$_smarty_tpl);?>
</strong></p>
                <div class="panel-body">
                    <p><?php echo smarty_function_txt(array('key'=>TEXT_TXT_NEW_CUSTOMER),$_smarty_tpl);
if ((defined('_STORE_ALLOW_GUEST_ORDERS') ? constant('_STORE_ALLOW_GUEST_ORDERS') : null) == 'true') {?><br /><?php echo smarty_function_txt(array('key'=>TEXT_TXT_GUEST),$_smarty_tpl);?>
<br /><?php echo smarty_function_txt(array('key'=>TEXT_NO_GUEST_ALLOWED_WITH_VAT),$_smarty_tpl);
}?></p>
                    <div class="register-short">
                        <form>
                            <div class="pull-right text-right">
                                <p>
                                    <a href="<?php echo smarty_function_link(array('page'=>'customer','paction'=>'login','params'=>'form=register','conn'=>'SSL'),$_smarty_tpl);?>
" class="btn btn-primary">
                                        <?php echo smarty_function_txt(array('key'=>BUTTON_NEXT),$_smarty_tpl);?>

                                    </a>
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php echo smarty_function_hook(array('key'=>'login_returning_bottom'),$_smarty_tpl);?>


    <?php }?>

    <?php if ($_GET['form'] == "register" || $_POST['form'] == "register") {?>
	<div class="col col-xs-12">

        <?php echo smarty_function_hook(array('key'=>'login_register_top'),$_smarty_tpl);?>


        <div class="panel panel-default">
            <p class="panel-heading"><strong><span class="glyphicon glyphicon-share-alt"></span> <?php echo smarty_function_txt(array('key'=>TEXT_NEW_CUSTOMER),$_smarty_tpl);?>
</strong></p>
            <div class="panel-body">
                <div id="registerbox">
                    <?php echo smarty_function_form(array('type'=>'form','name'=>'create_account','id'=>'create_account','action'=>'dynamic','link_params'=>'page_action=login','method'=>'post','conn'=>'SSL','role'=>"form"),$_smarty_tpl);?>

                        <?php echo smarty_function_form(array('type'=>'hidden','name'=>'action','value'=>'add_customer'),$_smarty_tpl);?>

                        <?php echo smarty_function_form(array('type'=>'hidden','name'=>'page','value'=>'customer'),$_smarty_tpl);?>

                        <?php echo smarty_function_form(array('type'=>'hidden','name'=>'form','value'=>'register'),$_smarty_tpl);?>

                        <?php echo smarty_function_form(array('type'=>'hidden','name'=>'default_address[address_class]','value'=>'default'),$_smarty_tpl);?>


                        <fieldset>
                        <legend><?php echo smarty_function_txt(array('key'=>BUTTON_REGISTER),$_smarty_tpl);?>
</legend>

                        <div class="row">
                            <!--div class="col col-md-3 bold"><?php echo smarty_function_txt(array('key'=>TEXT_ACCOUNT),$_smarty_tpl);
if ((defined('_STORE_ALLOW_GUEST_ORDERS') ? constant('_STORE_ALLOW_GUEST_ORDERS') : null) != 'true') {?><span class="required">*</span><?php }?></div-->
                            <div class="col col-md-12">

                                <div class="input-group">
                                    <!-- remember: wenn checkbox.name=guest-account nicht angehakt, wird der input nicht gesendet, also haben wir in customer niemals guest-account!=register, also auch niemals guest-account=guest -->
                                    <input type="checkbox" value="register" id="accountType" name="guest-account" class="guest-account checkbox-inline"style="vertical-align:text-top" <?php echo $_smarty_tpl->tpl_vars['account_type_checked']->value;?>
/>
                                    <label for="accountType" class="checkbox-inline" style="display:table-cell"><?php echo smarty_function_txt(array('key'=>TEXT_I_WANT_TO_REGISTER),$_smarty_tpl);
if ((defined('_STORE_ALLOW_GUEST_ORDERS') ? constant('_STORE_ALLOW_GUEST_ORDERS') : null) != 'true') {?>*<?php }?></label>
                                </div>

                                <p class="visible-register-account help-block" style="font-size:90%">
                                    <?php if ((defined('_STORE_ALLOW_GUEST_ORDERS') ? constant('_STORE_ALLOW_GUEST_ORDERS') : null) != 'true') {
echo smarty_function_txt(array('key'=>ERROR_ACCEPT_ACOUNT_CREATION),$_smarty_tpl);?>
<br /><?php }?>
                                    <?php echo smarty_function_txt(array('key'=>TEXT_TXT_NEW_CUSTOMER),$_smarty_tpl);?>

                                </p>
                                <?php if ((defined('_STORE_ALLOW_GUEST_ORDERS') ? constant('_STORE_ALLOW_GUEST_ORDERS') : null) == 'true') {?>
                                <p class="visible-register-guest help-block" style="font-size:90%"><?php echo smarty_function_txt(array('key'=>TEXT_TXT_GUEST),$_smarty_tpl);
if ((defined('_STORE_ACCOUNT_COMPANY_VAT_CHECK') ? constant('_STORE_ACCOUNT_COMPANY_VAT_CHECK') : null) == 'true') {?>&nbsp;<?php echo smarty_function_txt(array('key'=>TEXT_NO_VAT_ALLOWED_WITH_GUEST),$_smarty_tpl);
}?></p>
                                <?php }?>
                            </div>
                        </div>

                        <div class="form-group row visible-register-account">
                            <label for="cust_info_customers_password" class="col col-md-3"><?php echo smarty_function_txt(array('key'=>TEXT_PASSWORD_ENTRY),$_smarty_tpl);?>
*</label>
                            <div class="col col-md-9">
                                <?php echo smarty_function_form(array('id'=>'cust_info_customers_password','type'=>'password','name'=>'cust_info[customers_password]','value'=>$_smarty_tpl->tpl_vars['cust_info']->value['customers_password']),$_smarty_tpl);?>

                            </div>
                        </div>
                        <div class="form-group row visible-register-account">
                            <label for="cust_info_customers_password_confirm" class="col col-md-3"><?php echo smarty_function_txt(array('key'=>TEXT_PASSWORD_ENTRY_CONFIRM),$_smarty_tpl);?>
*</label>
                            <div class="col col-md-9">
                                <?php echo smarty_function_form(array('id'=>'cust_info_customers_password_confirm','type'=>'password','name'=>'cust_info[customers_password_confirm]','value'=>$_smarty_tpl->tpl_vars['cust_info']->value['customers_password_confirm']),$_smarty_tpl);?>

                            </div>
                        </div>
                    </fieldset>

                    <fieldset>
                        <p class="required"><?php echo smarty_function_txt(array('key'=>TEXT_MUST),$_smarty_tpl);?>
</p>
                    </fieldset>

                        <fieldset>
                            <legend><?php echo smarty_function_txt(array('key'=>TEXT_PERSONAL),$_smarty_tpl);?>
</legend>
                            <?php if ($_smarty_tpl->tpl_vars['show_gender']->value == 1) {?>
                                <div class="form-group row">
                                    <label for="default_address_customers_gender" class="col col-md-3"><?php echo smarty_function_txt(array('key'=>TEXT_GENDER),$_smarty_tpl);?>
*</label>
                                    <div class="col col-md-9">
                                        <?php echo smarty_function_form(array('params'=>'id="default_address_customers_gender"','type'=>'select','name'=>'default_address[customers_gender]','value'=>$_smarty_tpl->tpl_vars['gender_data']->value,'default'=>$_smarty_tpl->tpl_vars['default_address']->value['customers_gender'],'class'=>"form-control"),$_smarty_tpl);?>

                                    </div>
                                </div>
                            <?php }?>


                            <?php if ($_smarty_tpl->tpl_vars['show_title']->value == 1) {?>
                            <!-- variante 1 mit Freifeld für Eingabe eines beliebigen Wertes -->
                            <?php echo '<script'; ?>
>
                                document.addEventListener('DOMContentLoaded', function () {
                                    $('.dropdown-menu a').click(function() {
                                        console.log($(this).attr('data-value'));
                                        $(this).closest('.dropdown').find('input#default_address_customers_title')
                                            .val($(this).attr('data-value'));
                                        $(this).closest('.dropdown-menu').dropdown('toggle')
                                        return false;
                                    });
                                });
                            <?php echo '</script'; ?>
>

                            <?php $_smarty_tpl->_assignInScope('title_dd', false);?>
                            <?php if (is_array($_smarty_tpl->tpl_vars['title_data']->value) && count($_smarty_tpl->tpl_vars['title_data']->value) > 0) {
$_smarty_tpl->_assignInScope('title_dd', true);
}?>

                            <div class="form-group row">
                                <label for="default_address_customers_title" class="col col-md-3"><?php echo smarty_function_txt(array('key'=>TEXT_CUSTOMERS_TITLE),$_smarty_tpl);
if ($_smarty_tpl->tpl_vars['title_required']->value) {?>*<?php }?></label>
                                <div class="col-md-9">
                                    <div class="<?php if ($_smarty_tpl->tpl_vars['title_dd']->value) {?>input-group dropdown<?php }?>">
                                        <input type="text" name="default_address[customers_title]" id="default_address_customers_title" class="form-control default_address_customers_title dropdown-toggle" value="<?php echo $_smarty_tpl->tpl_vars['default_address']->value['customers_title'];?>
">
                                        <?php if ($_smarty_tpl->tpl_vars['title_dd']->value) {?>
                                        <ul class="dropdown-menu pull-right" style="width:100%">
                                            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['title_data']->value, 'title');
$_smarty_tpl->tpl_vars['title']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['title']->value) {
$_smarty_tpl->tpl_vars['title']->do_else = false;
?>
                                            <li><a href="#" data-value="<?php echo $_smarty_tpl->tpl_vars['title']->value['text'];?>
"><?php echo $_smarty_tpl->tpl_vars['title']->value['text'];?>
</a></li>
                                            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                                        </ul>
                                        <span role="button" class="input-group-addon dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span></span>
                                        <?php }?>
                                    </div>
                                </div>
                            </div>

                            <!-- variante 2 nur die im Backend eingestellten Werte können verwendet werden -->
                            <?php if ($_smarty_tpl->tpl_vars['title_dd']->value) {?>
                            <!--div class="form-group row">
                                <label for="default_address_customers_title" class="col col-md-3"><?php echo smarty_function_txt(array('key'=>TEXT_CUSTOMERS_TITLE),$_smarty_tpl);
if ($_smarty_tpl->tpl_vars['title_required']->value) {?>*<?php }?></label>
                                <div class="col col-md-9">
                                    <?php echo smarty_function_form(array('params'=>'id="default_address_customers_title"','type'=>'select','name'=>'default_address[customers_title]','value'=>$_smarty_tpl->tpl_vars['title_data']->value,'default'=>$_smarty_tpl->tpl_vars['default_address']->value['customers_title'],'class'=>"form-control"),$_smarty_tpl);?>

                                </div>
                            </div-->
                            <?php }?>

                            <?php }?><!-- show_title -->

                            <div class="form-group row">
                                <label for="default_address_customers_firstname" class="col col-md-3"><?php echo smarty_function_txt(array('key'=>TEXT_FIRSTNAME),$_smarty_tpl);?>
*</label>
                                <div class="col col-md-9">
                                    <?php echo smarty_function_form(array('id'=>'default_address_customers_firstname','type'=>'text','name'=>'default_address[customers_firstname]','value'=>$_smarty_tpl->tpl_vars['default_address']->value['customers_firstname']),$_smarty_tpl);?>

                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="default_address_customers_lastname" class="col col-md-3"><?php echo smarty_function_txt(array('key'=>TEXT_LASTNAME),$_smarty_tpl);?>
*</label>
                                <div class="col col-md-9">
                                    <?php echo smarty_function_form(array('id'=>'default_address_customers_lastname','type'=>'text','name'=>'default_address[customers_lastname]','value'=>$_smarty_tpl->tpl_vars['default_address']->value['customers_lastname']),$_smarty_tpl);?>

                                </div>
                            </div>
                            <?php if ($_smarty_tpl->tpl_vars['show_birthdate']->value == 1) {?>
                                <div class="form-group row">
                                    <label for="default_address_customers_dob" class="col col-md-3"><?php echo smarty_function_txt(array('key'=>TEXT_BIRTHDATE),$_smarty_tpl);?>
*</label>
                                    <div class="col col-md-9">
                                        <?php echo smarty_function_form(array('id'=>'default_address_customers_dob','type'=>'text','name'=>'default_address[customers_dob]','value'=>$_smarty_tpl->tpl_vars['default_address']->value['customers_dob']),$_smarty_tpl);?>

                                    </div>
                                </div>
                            <?php }?>
                            <div class="form-group row">
                                <label for="cust_info_customers_email_address" class="col col-md-3"><?php echo smarty_function_txt(array('key'=>TEXT_EMAIL),$_smarty_tpl);?>
*</label>
                                <div class="col col-md-9">
                                    <?php echo smarty_function_form(array('id'=>'cust_info_customers_email_address','type'=>'email','name'=>'cust_info[customers_email_address]','value'=>$_smarty_tpl->tpl_vars['cust_info']->value['customers_email_address']),$_smarty_tpl);?>

                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="cust_info_customers_email_address_confirm" class="col col-md-3"><?php echo smarty_function_txt(array('key'=>TEXT_EMAIL_CONFIRM),$_smarty_tpl);?>
*</label>
                                <div class="col col-md-9">
                                    <?php echo smarty_function_form(array('id'=>'cust_info_customers_email_address_confirm','type'=>'email','name'=>'cust_info[customers_email_address_confirm]','value'=>$_smarty_tpl->tpl_vars['cust_info']->value['customers_email_address_confirm']),$_smarty_tpl);?>

                                </div>
                            </div>
                        </fieldset>

                        <?php if ($_smarty_tpl->tpl_vars['show_company']->value == 1) {?>
                            <fieldset>
                                <legend><?php echo smarty_function_txt(array('key'=>TEXT_COMPANY),$_smarty_tpl);?>
</legend>
                                <div class="form-group row">
                                    <label for="default_address_customers_company" class="col col-md-3"><?php echo smarty_function_txt(array('key'=>TEXT_COMPANY_NAME),$_smarty_tpl);
if (_STORE_COMPANY_MIN_LENGTH > 0) {?>*<?php }?></label>
                                    <div class="col col-md-9">
                                        <?php echo smarty_function_form(array('id'=>'default_address_customers_company','type'=>'text','name'=>'default_address[customers_company]','value'=>$_smarty_tpl->tpl_vars['default_address']->value['customers_company']),$_smarty_tpl);?>

                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="default_address_customers_company_2" class="col col-md-3"><?php echo smarty_function_txt(array('key'=>TEXT_COMPANY_NAME_2),$_smarty_tpl);?>
</label>
                                    <div class="col col-md-9">
                                        <?php echo smarty_function_form(array('id'=>'default_address_customers_company_2','type'=>'text','name'=>'default_address[customers_company_2]','value'=>$_smarty_tpl->tpl_vars['default_address']->value['customers_company_2']),$_smarty_tpl);?>

                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="default_address_customers_company_3" class="col col-md-3"><?php echo smarty_function_txt(array('key'=>TEXT_COMPANY_NAME_3),$_smarty_tpl);?>
</label>
                                    <div class="col col-md-9">
                                        <?php echo smarty_function_form(array('id'=>'default_address_customers_company_3','type'=>'text','name'=>'default_address[customers_company_3]','value'=>$_smarty_tpl->tpl_vars['default_address']->value['customers_company_3']),$_smarty_tpl);?>

                                    </div>
                                </div>
                                <?php if ($_smarty_tpl->tpl_vars['show_vat']->value == 1) {?>
                                    <div class="form-group row">
                                        <label for="cust_info_customers_vat_id" class="col col-md-3"><?php echo smarty_function_txt(array('key'=>TEXT_VAT_ID),$_smarty_tpl);?>
</label>
                                        <div class="col col-md-9">
                                            <?php echo smarty_function_form(array('id'=>'cust_info_customers_vat_id','type'=>'text','name'=>'cust_info[customers_vat_id]','value'=>$_smarty_tpl->tpl_vars['cust_info']->value['customers_vat_id'],'class'=>'disabled-register-guest'),$_smarty_tpl);?>

                                            <?php if ((defined('_STORE_ALLOW_GUEST_ORDERS') ? constant('_STORE_ALLOW_GUEST_ORDERS') : null) == 'true') {?>
                                            <p class="visible-register-guest small help-block"><?php echo smarty_function_txt(array('key'=>TEXT_NO_VAT_ALLOWED_WITH_GUEST),$_smarty_tpl);?>
</p>
                                            <?php }?>
                                        </div>
                                    </div>
                                <?php }?>
                            </fieldset>
                        <?php }?>
                        <fieldset>
                            <legend><?php echo smarty_function_txt(array('key'=>TEXT_ADDRESS),$_smarty_tpl);?>
</legend>
                            <?php echo smarty_function_hook(array('key'=>'login_register_address_top'),$_smarty_tpl);?>

                            <div class="form-group row">
                                <label for="default_address_customers_street_address" class="col col-md-3"><?php echo smarty_function_txt(array('key'=>TEXT_STREET),$_smarty_tpl);?>
*</label>
                                <div class="col col-md-9">
                                    <?php echo smarty_function_form(array('id'=>'default_address_customers_street_address','type'=>'text','name'=>'default_address[customers_street_address]','value'=>$_smarty_tpl->tpl_vars['default_address']->value['customers_street_address']),$_smarty_tpl);?>

                                </div>
                            </div>
                        <?php if ($_smarty_tpl->tpl_vars['show_address_addition']->value == 1 || true) {?>
                            <div class="form-group row">
                                <label for="customers_address_addition" class="col col-md-3"><?php echo smarty_function_txt(array('key'=>TEXT_ADDRESS_ADDITION),$_smarty_tpl);?>
</label>
                                <div class="col col-md-9">
                                    <?php echo smarty_function_form(array('id'=>'default_customers_address_addition','type'=>'text','name'=>'default_address[customers_address_addition]','value'=>$_smarty_tpl->tpl_vars['default_address']->value['customers_address_addition']),$_smarty_tpl);?>

                                </div>
                            </div>
                        <?php }?>
                            <?php if ($_smarty_tpl->tpl_vars['show_suburb']->value == 1) {?>
                                <div class="form-group row">
                                    <label for="default_address_customers_suburb" class="col col-md-3"><?php echo smarty_function_txt(array('key'=>TEXT_SUBURB),$_smarty_tpl);?>
</label>
                                    <div class="col col-md-9">
                                        <?php echo smarty_function_form(array('id'=>'default_address_customers_suburb','type'=>'text','name'=>'default_address[customers_suburb]','value'=>$_smarty_tpl->tpl_vars['default_address']->value['customers_suburb']),$_smarty_tpl);?>

                                    </div>
                                </div>
                            <?php }?>
                            <div class="form-group row">
                                <label for="default_address_customers_postcode" class="col col-md-3"><?php echo smarty_function_txt(array('key'=>TEXT_CODE),$_smarty_tpl);?>
*</label>
                                <div class="col col-md-9">
                                    <?php echo smarty_function_form(array('id'=>'default_address_customers_postcode','type'=>'text','name'=>'default_address[customers_postcode]','value'=>$_smarty_tpl->tpl_vars['default_address']->value['customers_postcode']),$_smarty_tpl);?>

                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="default_address_customers_city" class="col col-md-3"><?php echo smarty_function_txt(array('key'=>TEXT_CITY),$_smarty_tpl);?>
*</label>
                                <div class="col col-md-9">
                                    <?php echo smarty_function_form(array('id'=>'default_address_customers_city','type'=>'text','name'=>'default_address[customers_city]','value'=>$_smarty_tpl->tpl_vars['default_address']->value['customers_city']),$_smarty_tpl);?>

                                </div>
                            </div>
                            <div id="countries" class="form-group row">
                                <label for="default_address_customers_country_code" class="col col-md-3"><?php echo smarty_function_txt(array('key'=>TEXT_COUNTRY),$_smarty_tpl);?>
*</label>
                                <div class="col col-md-9">
                                    <?php if ((isset($_smarty_tpl->tpl_vars['default_address']->value['customers_country_code']))) {?>
                                        <?php echo smarty_function_form(array('params'=>'id="default_address_customers_country_code"','class'=>"form-control",'type'=>'select','name'=>'default_address[customers_country_code]','value'=>$_smarty_tpl->tpl_vars['country_data']->value,'default'=>$_smarty_tpl->tpl_vars['default_address']->value['customers_country_code']),$_smarty_tpl);?>

                                    <?php } else { ?>
                                        <?php echo smarty_function_form(array('params'=>'id="default_address_customers_country_code"','class'=>"form-control",'type'=>'select','name'=>'default_address[customers_country_code]','value'=>$_smarty_tpl->tpl_vars['country_data']->value,'default'=>$_smarty_tpl->tpl_vars['default_country']->value),$_smarty_tpl);?>

                                    <?php }?>
                                </div>
                            </div>
                            <?php if ($_smarty_tpl->tpl_vars['show_federal_states']->value == 1) {?>
                                <div id="federals" class="form-group row">

                                    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['country_data']->value, 'federal_states');
$_smarty_tpl->tpl_vars['federal_states']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['federal_states']->value) {
$_smarty_tpl->tpl_vars['federal_states']->do_else = false;
?>
                                    <?php if ($_smarty_tpl->tpl_vars['federal_states']->value['federal_states']) {?>
                                    <div class="form-group federals-states <?php echo $_smarty_tpl->tpl_vars['federal_states']->value['id'];?>
" style="display:none">
                                        <label for="default_address_customers_federal_state_code" class="col col-md-3"><?php echo smarty_function_txt(array('key'=>TEXT_FEDERAL_STATES),$_smarty_tpl);?>
*</label>
                                        <div class="col col-md-9">
                                            <?php echo smarty_function_form(array('params'=>'id="default_address_customers_federal_state_code"','class'=>"form-control",'type'=>'select','name'=>'default_address[customers_federal_state_code]','value'=>$_smarty_tpl->tpl_vars['federal_states']->value['federal_states'],'default'=>$_smarty_tpl->tpl_vars['default_address']->value['customers_federal_state_code']),$_smarty_tpl);?>

                                        </div>
                                    </div>
                                    <?php }?>
                                    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>

                                </div>
                            <?php }?>

                            <?php echo smarty_function_hook(array('key'=>'login_register_address_bottom'),$_smarty_tpl);?>

                        </fieldset>

                        <fieldset class="inputs-with-prefix">
                            <legend><?php echo smarty_function_txt(array('key'=>TEXT_CONTACT),$_smarty_tpl);?>
</legend>
                            <div class="form-group row">
                                <label for="default_address_customers_phone" class="col col-md-3"><?php echo smarty_function_txt(array('key'=>TEXT_PHONE),$_smarty_tpl);
if (_STORE_TELEPHONE_MIN_LENGTH > 0) {?>*<?php }?></label>
                                <div class="col col-md-9">
                                    <?php if (_STORE_SHOW_PHONE_PREFIX == 'true') {?>
                                        <div class="row">
                                            <div class="col-xs-12 col-sm-5 col-md-4">
                                                <?php if ((isset($_smarty_tpl->tpl_vars['default_address']->value['customers_phone_prefix']))) {?>
                                                    <?php echo smarty_function_form(array('params'=>'id="default_address_customers_phone_prefix"','type'=>'select','name'=>'default_address[customers_phone_prefix]','value'=>$_smarty_tpl->tpl_vars['phone_prefix']->value,'default'=>$_smarty_tpl->tpl_vars['default_address']->value['customers_phone_prefix']),$_smarty_tpl);?>

                                                <?php } else { ?>
                                                    <?php echo smarty_function_form(array('params'=>'id="default_address_customers_phone_prefix"','type'=>'select','name'=>'default_address[customers_phone_prefix]','value'=>$_smarty_tpl->tpl_vars['phone_prefix']->value,'default'=>(defined('_STORE_PHONE_PREFIX') ? constant('_STORE_PHONE_PREFIX') : null)),$_smarty_tpl);?>

                                                <?php }?>
                                            </div>
                                            <div class="col-xs-12 col-sm-7 col-md-8">
                                                <?php echo smarty_function_form(array('id'=>'default_address_customers_phone','type'=>'text','name'=>'default_address[customers_phone]','value'=>$_smarty_tpl->tpl_vars['default_address']->value['customers_phone']),$_smarty_tpl);?>

                                            </div>
                                        </div>
                                    <?php } else { ?>
                                        <?php echo smarty_function_form(array('id'=>'default_address_customers_phone','type'=>'text','name'=>'default_address[customers_phone]','value'=>$_smarty_tpl->tpl_vars['default_address']->value['customers_phone']),$_smarty_tpl);?>

                                    <?php }?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="default_address_customers_mobile_phone" class="col col-md-3"><?php echo smarty_function_txt(array('key'=>TEXT_MOBILE_PHONE),$_smarty_tpl);
if (_STORE_MOBILE_PHONE_MIN_LENGTH > 0) {?>*<?php }?></label>
                                <div class="col col-md-9">
                                    <?php if (_STORE_SHOW_PHONE_PREFIX == 'true') {?>
                                        <div class="row">
                                            <div class="col-xs-12 col-sm-5 col-md-4">
                                                <?php if ((isset($_smarty_tpl->tpl_vars['default_address']->value['customers_mobile_phone_prefix']))) {?>
                                                    <?php echo smarty_function_form(array('params'=>'id="default_address_customers_mobile_phone_prefix"','type'=>'select','name'=>'default_address[customers_mobile_phone_prefix]','value'=>$_smarty_tpl->tpl_vars['phone_prefix']->value,'default'=>$_smarty_tpl->tpl_vars['default_address']->value['customers_mobile_phone_prefix']),$_smarty_tpl);?>

                                                <?php } else { ?>
                                                    <?php echo smarty_function_form(array('params'=>'id="default_address_customers_mobile_phone_prefix"','type'=>'select','name'=>'default_address[customers_mobile_phone_prefix]','value'=>$_smarty_tpl->tpl_vars['phone_prefix']->value,'default'=>(defined('_STORE_PHONE_PREFIX') ? constant('_STORE_PHONE_PREFIX') : null)),$_smarty_tpl);?>

                                                <?php }?>
                                            </div>
                                            <div class="col-xs-12 col-sm-7 col-md-8">
                                                <?php echo smarty_function_form(array('id'=>'default_address_customers_mobile_phone','type'=>'text','name'=>'default_address[customers_mobile_phone]','value'=>$_smarty_tpl->tpl_vars['default_address']->value['customers_mobile_phone']),$_smarty_tpl);?>

                                            </div>
                                        </div>
                                    <?php } else { ?>
                                        <?php echo smarty_function_form(array('id'=>'default_address_customers_mobile_phone','type'=>'text','name'=>'default_address[customers_mobile_phone]','value'=>$_smarty_tpl->tpl_vars['default_address']->value['customers_mobile_phone']),$_smarty_tpl);?>

                                    <?php }?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="default_address_customers_fax" class="col col-md-3"><?php echo smarty_function_txt(array('key'=>TEXT_FAX),$_smarty_tpl);?>
</label>
                                <div class="col col-md-9">
                                    <?php if (_STORE_SHOW_PHONE_PREFIX == 'true') {?>
                                        <div class="row">
                                            <div class="col-xs-12 col-sm-5 col-md-4">
                                                <?php if ((isset($_smarty_tpl->tpl_vars['default_address']->value['customers_fax_prefix']))) {?>
                                                    <?php echo smarty_function_form(array('params'=>'id="default_address_customers_fax_prefix"','type'=>'select','name'=>'default_address[customers_fax_prefix]','value'=>$_smarty_tpl->tpl_vars['phone_prefix']->value,'default'=>$_smarty_tpl->tpl_vars['default_address']->value['customers_fax_prefix']),$_smarty_tpl);?>

                                                <?php } else { ?>
                                                    <?php echo smarty_function_form(array('params'=>'id="default_address_customers_fax_prefix"','type'=>'select','name'=>'default_address[customers_fax_prefix]','value'=>$_smarty_tpl->tpl_vars['phone_prefix']->value,'default'=>(defined('_STORE_PHONE_PREFIX') ? constant('_STORE_PHONE_PREFIX') : null)),$_smarty_tpl);?>

                                                <?php }?>
                                            </div>
                                            <div class="col-xs-12 col-sm-7 col-md-8">
                                                <?php echo smarty_function_form(array('id'=>'default_address_customers_fax','type'=>'text','name'=>'default_address[customers_fax]','value'=>$_smarty_tpl->tpl_vars['default_address']->value['customers_fax']),$_smarty_tpl);?>

                                            </div>
                                        </div>
                                    <?php } else { ?>
                                        <?php echo smarty_function_form(array('id'=>'default_address_customers_fax','type'=>'text','name'=>'default_address[customers_fax]','value'=>$_smarty_tpl->tpl_vars['default_address']->value['customers_fax']),$_smarty_tpl);?>

                                    <?php }?>
                                </div>
                            </div>
                        </fieldset>

                        <div class="form-submit pull-right text-right">
                            <?php if ($_smarty_tpl->tpl_vars['show_privacy']->value == 1) {?>
                                <?php echo smarty_function_content(array('cont_id'=>2,'is_id'=>'false'),$_smarty_tpl);?>

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
                                <?php }?>
                            <?php }?>
                            <?php echo smarty_function_hook(array('key'=>'login_create_account_tpl'),$_smarty_tpl);?>

                            <p>
                                <a href="<?php echo smarty_function_link(array('page'=>'customer','paction'=>'login','conn'=>'SSL'),$_smarty_tpl);?>
" class="btn btn-default">
                                    <?php echo smarty_function_txt(array('key'=>BUTTON_BACK),$_smarty_tpl);?>

                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <?php echo smarty_function_txt(array('key'=>BUTTON_NEXT),$_smarty_tpl);?>

                                </button>
                            </p>
                        </div>

                        <div class="clearfix visible-xs"></div>

                        <p class="required pull-right-xs"><?php echo smarty_function_txt(array('key'=>TEXT_MUST),$_smarty_tpl);?>
</p>
                        <div class="clearfix"></div>

                    <?php echo smarty_function_form(array('type'=>'formend'),$_smarty_tpl);?>


                        <?php echo '<script'; ?>
>
                            const _STORE_ALLOW_GUEST_ORDERS = <?php if ((defined('_STORE_ALLOW_GUEST_ORDERS') ? constant('_STORE_ALLOW_GUEST_ORDERS') : null) == 'true') {?>true<?php } else { ?>false<?php }?>;
                        <?php echo '</script'; ?>
>

                        <?php echo '<script'; ?>
>
                            document.addEventListener("DOMContentLoaded",function(event)
                        {
                            const accountTypeChangeEvent = new Event('change');
                            const accountTypeInput = document.getElementById('accountType');
                            accountTypeInput.addEventListener('change', (e) =>
                            {
                                let display_register = 'none';
                                let display_guest = 'block';

                                if (e.target.checked)
                                {
                                    display_register = 'block';
                                    display_guest = 'none';

                                    // egal, ob _STORE_ALLOW_GUEST_ORDERS input.guest-account bekommt value 'register'
                                    this.value = "register";
                                    // evtl für gast disabled'te input wieder aktivieren
                                    document.querySelectorAll('input.disabled-register-guest').forEach((el) => { el.removeAttribute("disabled"); });
                                } else {
                                    // die nicht gecheckte checkbox wird nicht gesendet, pro forma aber auf value 'guest' gesetzt
                                    this.value = "guest";
                                    // für gast nicht erlaubte inputs leeren und deaktiveren
                                    // aber nur wenn gast erlaubt
                                    if(_STORE_ALLOW_GUEST_ORDERS)
                                        document.querySelectorAll('input.disabled-register-guest').forEach((el) => {
                                            el.value = "";
                                            el.setAttribute('disabled', 'true');
                                        });
                                }
                                // ein/ausblenden von gast/register spezifischen texten
                                document.querySelectorAll('.visible-register-account').forEach((el) => { el.style.display = display_register; });
                                document.querySelectorAll('.visible-register-guest').forEach((el) => { el.style.display = display_guest; });
                            });
                            accountTypeInput.dispatchEvent(accountTypeChangeEvent);



                            $('#countries').change(function() {
                                var selected_country = $('#countries option:selected').val();
                                $('.federals-states').css( { 'display':'none' } );
                                if($('.'+selected_country).length != 0) {
                                    $('.federals-states.'+selected_country).css({ 'display':'block' });
                                    var selectedFederalStateValue  = $('.federals-states.'+selected_country + ' select option[selected=selected]').val();
                                    if (typeof selectedFederalStateValue == 'undefined') selectedFederalStateValue = 1;
                                    $('.federals-states.'+selected_country + ' select').val( selectedFederalStateValue );
                                }
                                else {
                                    $('.federals-states select').val('');
                                }
                            });
                            //update bootstrap-select
                            $(".federals-states select, #countries select").attr('data-live-search', 'true').selectpicker('render');
                            // init federal states
                            $('#countries').trigger('change');

                        });
                        <?php echo '</script'; ?>
>


                        <?php echo '<script'; ?>
>

                        document.addEventListener("DOMContentLoaded",function(event)
                        {
                            var dobClicked = false;
                            var dobPreselect = new Date(<?php echo $_smarty_tpl->tpl_vars['dobPreselect']->value;?>
);

                            var dobInput = $('#default_address_customers_dob');
                            if (dobInput.length) {
                                dobInput.datetimepicker({
                                    minDate: new Date(<?php echo $_smarty_tpl->tpl_vars['min_date']->value;?>
),
                                    maxDate:  new Date(<?php echo $_smarty_tpl->tpl_vars['max_date']->value;?>
),
                                    useCurrent: false,
                                    format: '<?php echo mb_strtoupper((string) (defined('_STORE_ACCOUNT_DOB_FORMAT') ? constant('_STORE_ACCOUNT_DOB_FORMAT') : null) ?? '', 'UTF-8');?>
',
                                    locale: '<?php if ($_smarty_tpl->tpl_vars['language']->value == "de") {?>de<?php } else { ?>en<?php }?>'
                                }).on('dp.show', function()
                                {
                                    if (!dobClicked && dobPreselect!=false){
                                        dobInput.data("DateTimePicker").date( dobPreselect );// set
                                        <?php if ($_smarty_tpl->tpl_vars['is_new']->value) {?>dobInput.data("DateTimePicker").date( null ); // and unset to unselect and force user to click<?php }?>
                                        dobClicked = true;
                                    }
                                });
                            }
                        });
                        <?php echo '</script'; ?>
>

                </div><!-- #registerbox -->
            </div>
        </div>

        <?php echo smarty_function_hook(array('key'=>'login_register_bottom'),$_smarty_tpl);?>

	</div><!-- .col -->
    <?php }?>

</div><!-- .row -->

<?php echo smarty_function_hook(array('key'=>'login_bottom'),$_smarty_tpl);
}
}
