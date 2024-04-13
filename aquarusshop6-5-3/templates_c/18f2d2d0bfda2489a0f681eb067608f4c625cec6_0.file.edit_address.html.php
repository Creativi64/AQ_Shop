<?php
/* Smarty version 4.3.2, created on 2024-03-20 20:18:43
  from '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/templates/xt_responsive_AQ/xtCore/pages/edit_address.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_65fb3693b9b472_48131546',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '18f2d2d0bfda2489a0f681eb067608f4c625cec6' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarusshop6-5-3/templates/xt_responsive_AQ/xtCore/pages/edit_address.html',
      1 => 1697144119,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_65fb3693b9b472_48131546 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/smarty/xt_plugins/function.txt.php','function'=>'smarty_function_txt',),1=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/smarty/xt_plugins/function.form.php','function'=>'smarty_function_form',),2=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/smarty/xt_plugins/function.hook.php','function'=>'smarty_function_hook',),3=>array('file'=>'/homepages/2/d41324517/htdocs/aquarusshop6-5-3/xtFramework/library/smarty/xt_plugins/function.link.php','function'=>'smarty_function_link',),));
?>
<div id="edit-adress">
	<h1><?php echo smarty_function_txt(array('key'=>TEXT_EDIT_ADDRESS),$_smarty_tpl);?>
</h1>
	<?php echo smarty_function_form(array('type'=>'form','name'=>'edit_address','action'=>'dynamic','link_params'=>'page_action=edit_address','method'=>'post','conn'=>'SSL'),$_smarty_tpl);?>

	<?php echo smarty_function_form(array('type'=>'hidden','name'=>'action','value'=>'edit_address'),$_smarty_tpl);?>

	<?php if ($_smarty_tpl->tpl_vars['address_book_id']->value) {
echo smarty_function_form(array('type'=>'hidden','name'=>'address_book_id','value'=>$_smarty_tpl->tpl_vars['address_book_id']->value),$_smarty_tpl);
}?>
	<?php echo $_smarty_tpl->tpl_vars['message']->value;?>

	<?php echo smarty_function_hook(array('key'=>'account_tpl_edit_address_center'),$_smarty_tpl);?>


    <fieldset>
        <legend><?php echo smarty_function_txt(array('key'=>TEXT_PERSONAL),$_smarty_tpl);?>
</legend>
        <?php if ($_smarty_tpl->tpl_vars['show_gender']->value == 1) {?>
        <div class="form-group">
            <label for="customers_gender"><?php echo smarty_function_txt(array('key'=>TEXT_GENDER),$_smarty_tpl);?>
*</label>
            <?php echo smarty_function_form(array('class'=>"form-control",'type'=>'select','name'=>'customers_gender','value'=>$_smarty_tpl->tpl_vars['gender_data']->value,'default'=>$_smarty_tpl->tpl_vars['customers_gender']->value),$_smarty_tpl);?>

        </div>
        <?php }?>
        <?php if ($_smarty_tpl->tpl_vars['show_company']->value == 1) {?>
        <div class="form-group">
            <label for="customers_company"><?php echo smarty_function_txt(array('key'=>TEXT_COMPANY_NAME),$_smarty_tpl);
if (_STORE_COMPANY_MIN_LENGTH > 0) {?>*<?php }?></label>
            <?php echo smarty_function_form(array('id'=>'customers_company','type'=>'text','name'=>'customers_company','value'=>$_smarty_tpl->tpl_vars['customers_company']->value),$_smarty_tpl);?>

        </div>
        <div class="form-group">
            <label for="customers_company_2"><?php echo smarty_function_txt(array('key'=>TEXT_COMPANY_NAME_2),$_smarty_tpl);?>
</label>
            <?php echo smarty_function_form(array('id'=>'customers_company_2','type'=>'text','name'=>'customers_company_2','value'=>$_smarty_tpl->tpl_vars['customers_company_2']->value),$_smarty_tpl);?>

        </div>
        <div class="form-group">
            <label for="customers_company_3"><?php echo smarty_function_txt(array('key'=>TEXT_COMPANY_NAME_3),$_smarty_tpl);?>
</label>
            <?php echo smarty_function_form(array('id'=>'customers_company_3','type'=>'text','name'=>'customers_company_3','value'=>$_smarty_tpl->tpl_vars['customers_company_3']->value),$_smarty_tpl);?>

        </div>
        <?php }?>


        <?php if ($_smarty_tpl->tpl_vars['show_title']->value == 1) {?>
        <!-- variante 1 mit Freifeld für Eingabe eines beliebigen Wertes -->
        <?php echo '<script'; ?>
>
            document.addEventListener('DOMContentLoaded', function () {
                $('.dropdown-menu a').click(function() {
                    console.log($(this).attr('data-value'));
                    $(this).closest('.dropdown').find('input#customers_title')
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

        <div class="form-group">
            <label for="customers_title"><?php echo smarty_function_txt(array('key'=>TEXT_CUSTOMERS_TITLE),$_smarty_tpl);
if ($_smarty_tpl->tpl_vars['title_required']->value) {?>*<?php }?></label>

            <div class="<?php if ($_smarty_tpl->tpl_vars['title_dd']->value) {?>input-group dropdown<?php }?>">
                <input type="text" name="customers_title" id="customers_title" class="form-control customers_title dropdown-toggle" value="<?php echo $_smarty_tpl->tpl_vars['customers_title']->value;?>
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

        <!-- variante 2 nur die im Backend eingestellten Werte können verwendet werden -->
        <?php if ($_smarty_tpl->tpl_vars['title_dd']->value) {?>
        <!-- div class="form-group">
            <label for="customers_title"><?php echo smarty_function_txt(array('key'=>TEXT_CUSTOMERS_TITLE),$_smarty_tpl);
if ($_smarty_tpl->tpl_vars['title_required']->value) {?>*<?php }?></label>
            <div>
                <?php echo smarty_function_form(array('params'=>'id="customers_title"','type'=>'select','name'=>'customers_title','value'=>$_smarty_tpl->tpl_vars['title_data']->value,'default'=>$_smarty_tpl->tpl_vars['customers_title']->value,'class'=>"form-control"),$_smarty_tpl);?>

            </div>
        </div-->
        <?php }?>

        <?php }?><!-- show_title -->


        <div class="form-group">
            <label for="customers_firstname"><?php echo smarty_function_txt(array('key'=>TEXT_FIRSTNAME),$_smarty_tpl);?>
*</label>
            <?php echo smarty_function_form(array('id'=>'customers_firstname','type'=>'text','name'=>'customers_firstname','value'=>$_smarty_tpl->tpl_vars['customers_firstname']->value),$_smarty_tpl);?>

        </div>
        <div class="form-group">
            <label for="customers_lastname"><?php echo smarty_function_txt(array('key'=>TEXT_LASTNAME),$_smarty_tpl);?>
*</label>
            <?php echo smarty_function_form(array('id'=>'customers_lastname','type'=>'text','name'=>'customers_lastname','value'=>$_smarty_tpl->tpl_vars['customers_lastname']->value),$_smarty_tpl);?>

        </div>
        <?php if ($_smarty_tpl->tpl_vars['show_birthdate']->value == 1) {?>
        <div class="form-group">
            <label for="customers_dob"><?php echo smarty_function_txt(array('key'=>TEXT_BIRTHDATE),$_smarty_tpl);?>
*</label>
            <?php echo smarty_function_form(array('id'=>'customers_dob','type'=>'text','name'=>'customers_dob','value'=>$_smarty_tpl->tpl_vars['customers_dob']->value),$_smarty_tpl);?>

        </div>
        <?php }?>
    </fieldset>

    <fieldset>
        <legend><?php echo smarty_function_txt(array('key'=>TEXT_ADDRESS),$_smarty_tpl);?>
</legend>
        <?php echo smarty_function_hook(array('key'=>'account_tpl_edit_address_address_top'),$_smarty_tpl);?>

        <div class="form-group">
            <label for="customers_street_address"><?php echo smarty_function_txt(array('key'=>TEXT_STREET),$_smarty_tpl);?>
*</label>
            <?php echo smarty_function_form(array('id'=>'customers_street_address','type'=>'text','name'=>'customers_street_address','value'=>$_smarty_tpl->tpl_vars['customers_street_address']->value),$_smarty_tpl);?>

        </div>
        <div class="form-group">
            <label for="customers_address_addition"><?php echo smarty_function_txt(array('key'=>TEXT_ADDRESS_ADDITION),$_smarty_tpl);?>
</label>
            <?php echo smarty_function_form(array('id'=>'customers_address_addition','type'=>'text','name'=>'customers_address_addition','value'=>$_smarty_tpl->tpl_vars['customers_address_addition']->value),$_smarty_tpl);?>

        </div>
        <?php if ($_smarty_tpl->tpl_vars['show_suburb']->value == 1) {?>
        <div class="form-group">
            <label for="customers_suburb"><?php echo smarty_function_txt(array('key'=>TEXT_SUBURB),$_smarty_tpl);?>
</label>
            <?php echo smarty_function_form(array('id'=>'customers_suburb','type'=>'text','name'=>'customers_suburb','value'=>$_smarty_tpl->tpl_vars['customers_suburb']->value),$_smarty_tpl);?>

        </div>
        <?php }?>
        <div class="form-group">
            <label for="customers_postcode"><?php echo smarty_function_txt(array('key'=>TEXT_CODE),$_smarty_tpl);?>
*</label>
            <?php echo smarty_function_form(array('id'=>'customers_postcode','type'=>'text','name'=>'customers_postcode','value'=>$_smarty_tpl->tpl_vars['customers_postcode']->value),$_smarty_tpl);?>

        </div>
        <div class="form-group">
            <label for="customers_city"><?php echo smarty_function_txt(array('key'=>TEXT_CITY),$_smarty_tpl);?>
*</label>
            <?php echo smarty_function_form(array('id'=>'customers_city','type'=>'text','name'=>'customers_city','value'=>$_smarty_tpl->tpl_vars['customers_city']->value),$_smarty_tpl);?>

        </div>
        <div class="form-group">
            <label for="customers_country_code"><?php echo smarty_function_txt(array('key'=>TEXT_COUNTRY),$_smarty_tpl);?>
*</label>
            <div id='countries'><?php echo smarty_function_form(array('type'=>'select','name'=>'customers_country_code','value'=>$_smarty_tpl->tpl_vars['country_data']->value,'default'=>$_smarty_tpl->tpl_vars['selected_country']->value),$_smarty_tpl);?>
</div><!-- #countries -->
        </div>
        <?php if ($_smarty_tpl->tpl_vars['show_federal_states']->value == 1) {?>
        <div id="federals" class="form-group" >
            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['country_data']->value, 'federal_states');
$_smarty_tpl->tpl_vars['federal_states']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['federal_states']->value) {
$_smarty_tpl->tpl_vars['federal_states']->do_else = false;
?>
            <?php if ($_smarty_tpl->tpl_vars['federal_states']->value['federal_states']) {?>
            <div class="federals-states <?php echo $_smarty_tpl->tpl_vars['federal_states']->value['id'];?>
" style="display:none">
                <label for="default_address_customers_federal_state_code"><?php echo smarty_function_txt(array('key'=>TEXT_FEDERAL_STATES),$_smarty_tpl);?>
*</label><br />
                <?php echo smarty_function_form(array('params'=>'id="customers_federal_state_code"','type'=>'select','name'=>'customers_federal_state_code','value'=>$_smarty_tpl->tpl_vars['federal_states']->value['federal_states'],'default'=>$_smarty_tpl->tpl_vars['customers_federal_state_code']->value),$_smarty_tpl);?>

            </div>
            <?php }?>
            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
        </div>
        <?php }?>
        <?php if ($_smarty_tpl->tpl_vars['show_state']->value == 1) {?>
        <div class="form-group">
            <label for="customers_zone_code"><?php echo smarty_function_txt(array('key'=>TEXT_STATE),$_smarty_tpl);?>
</label>
            <?php echo smarty_function_form(array('type'=>'select','name'=>'customers_zone_code','value'=>$_smarty_tpl->tpl_vars['customers_zone_code']->value,'default'=>$_smarty_tpl->tpl_vars['selected_zone']->value),$_smarty_tpl);?>

        </div>
        <?php }?>
        <?php if ($_smarty_tpl->tpl_vars['show_county']->value == 1) {?>
        <div class="form-group">
            <label for=""><?php echo smarty_function_txt(array('key'=>TEXT_COUNTY),$_smarty_tpl);?>
</label>
            <?php echo $_smarty_tpl->tpl_vars['INPUT_COUNTY']->value;?>

        </div>
        <?php }?>
        <?php echo smarty_function_hook(array('key'=>'account_tpl_edit_address_address_bottom'),$_smarty_tpl);?>

    </fieldset>

    <fieldset>
        <legend><?php echo smarty_function_txt(array('key'=>TEXT_CONTACT),$_smarty_tpl);?>
</legend>
        <div class="form-group">
            <label for="customers_phone"><?php echo smarty_function_txt(array('key'=>TEXT_PHONE),$_smarty_tpl);
if (_STORE_TELEPHONE_MIN_LENGTH > 0) {?>*<?php }?></label>
            <?php echo smarty_function_form(array('id'=>'customers_phone','type'=>'text','name'=>'customers_phone','value'=>$_smarty_tpl->tpl_vars['customers_phone']->value),$_smarty_tpl);?>

        </div>
        <div class="form-group">
            <label for="customers_mobile_phone"><?php echo smarty_function_txt(array('key'=>TEXT_MOBILE_PHONE),$_smarty_tpl);
if (_STORE_MOBILE_PHONE_MIN_LENGTH > 0) {?>*<?php }?></label>
            <?php echo smarty_function_form(array('id'=>'customers_mobile_phone','type'=>'text','name'=>'customers_mobile_phone','value'=>$_smarty_tpl->tpl_vars['customers_mobile_phone']->value),$_smarty_tpl);?>

        </div>
        <div class="form-group">
            <label for="customers_fax"><?php echo smarty_function_txt(array('key'=>TEXT_FAX),$_smarty_tpl);
if (_STORE_FAX_MIN_LENGTH > 0) {?>*<?php }?></label>
            <?php echo smarty_function_form(array('id'=>'customers_fax','type'=>'text','name'=>'customers_fax','value'=>$_smarty_tpl->tpl_vars['customers_fax']->value),$_smarty_tpl);?>

        </div>
    </fieldset>

	<?php if ($_smarty_tpl->tpl_vars['adType']->value) {?>
	    <?php echo smarty_function_form(array('type'=>'hidden','name'=>'address_class','value'=>$_smarty_tpl->tpl_vars['adType']->value),$_smarty_tpl);?>

	    <?php echo smarty_function_form(array('type'=>'hidden','name'=>'adType','value'=>$_smarty_tpl->tpl_vars['adType']->value),$_smarty_tpl);?>

	<?php } else { ?>
        <div class="well">
            <div class="form-group">
                <label for="old_address_class"><?php echo smarty_function_txt(array('key'=>TEXT_ADDRESS_TYPE),$_smarty_tpl);?>
</label>
                <?php echo smarty_function_form(array('id'=>'old_address_class','type'=>'hidden','name'=>'old_address_class','value'=>$_smarty_tpl->tpl_vars['old_address_class']->value),$_smarty_tpl);?>

                <?php echo smarty_function_form(array('class'=>"form-control",'type'=>'select','name'=>'address_class','value'=>$_smarty_tpl->tpl_vars['address_type']->value,'default'=>$_smarty_tpl->tpl_vars['address_class']->value),$_smarty_tpl);?>

            </div>
        </div>
	<?php }?>

    <p class="required pull-left"><?php echo smarty_function_txt(array('key'=>TEXT_MUST),$_smarty_tpl);?>
</p>

    <div class="form-submit pull-right text-right">
        <p>
            <?php if ($_smarty_tpl->tpl_vars['adType']->value == 'shipping') {?>
                <a href="<?php echo smarty_function_link(array('page'=>'checkout','paction'=>'shipping','conn'=>'SSL'),$_smarty_tpl);?>
" class="btn btn-default"><?php echo smarty_function_txt(array('key'=>BUTTON_BACK),$_smarty_tpl);?>
</a>
            <?php }?>
            <?php if ($_smarty_tpl->tpl_vars['adType']->value == 'payment') {?>
                <a href="<?php echo smarty_function_link(array('page'=>'checkout','paction'=>'payment','conn'=>'SSL'),$_smarty_tpl);?>
" class="btn btn-default"><?php echo smarty_function_txt(array('key'=>BUTTON_BACK),$_smarty_tpl);?>
</a>
            <?php }?>
            <?php if (!$_smarty_tpl->tpl_vars['adType']->value) {?>
                <a href="<?php echo smarty_function_link(array('page'=>'customer','paction'=>'address_overview','conn'=>'SSL'),$_smarty_tpl);?>
" class="btn btn-default"><?php echo smarty_function_txt(array('key'=>BUTTON_BACK),$_smarty_tpl);?>
</a>
            <?php }?>

            <button type="submit" class="btn btn-primary">
                <?php echo smarty_function_txt(array('key'=>BUTTON_NEXT),$_smarty_tpl);?>

            </button>
        </p>
    </div>

	<?php echo smarty_function_form(array('type'=>'formend'),$_smarty_tpl);?>


	
	<?php echo '<script'; ?>
 type="text/javascript">
        var dobClicked = false;
        var dobPreselect = new Date(<?php echo $_smarty_tpl->tpl_vars['dobPreselect']->value;?>
);
        document.addEventListener("DOMContentLoaded",function(event){
 			$('#countries').change(function(){
 				var selected_country = $('#countries option:selected').val();
                $('.federals-states').css({'display':'none'});
				if($('.'+selected_country).length != 0){
                    $('.federals-states.'+selected_country).css({'display':'block'});
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
            

            var dobInput = $('#customers_dob');
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
	
</div><!-- #edit-adress --><?php }
}
