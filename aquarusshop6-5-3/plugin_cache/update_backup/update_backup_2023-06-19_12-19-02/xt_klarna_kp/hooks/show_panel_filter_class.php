<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

if($_GET['load_section'] == 'xt_klarna_kp_payouts')
{
    $filterClass = get_class($this->class) . "Filter";
    {
        $file = _SRV_WEBROOT . _SRV_WEB_PLUGINS . "xt_klarna_kp/classes/class." . $filterClass . ".php";
        if (file_exists($file))
        {
            require_once $file;
            if (class_exists($filterClass))
            {
                $a = new $filterClass();
                $formFields = $a->formFields();
            }
        }
    }


    $extF = new ExtFunctions();
    $btnPanel = new PhpExt_Panel();

    $window = $extF->_RemoteWindow3("dummy",
        "",
        "adminHandler.php?plugin=xt_klarna_kp&load_section=xt_klarna_kp_payouts&pg=getSummary",
        '',
        array(), 500, 400, '');
    $window->setModal(true);
    $window->setTitle(TEXT_KLARNA_PAYOUTS_SUMMERY . ' ' . TEXT_KLARNA_PAYOUTS);
    $js_window = $window->getJavascript(false, "new_window") . ' new_window.show();';
    $js = "
    var ff = Ext.getCmp('" . $formId . "');
    ff.getForm().submit(
    {
        waitMsg: ' ...',
        failure: function(ff, action) {
            Ext.MessageBox.alert('Error Message', '');
        },
        success: function(ff, action)
        {
            " . $js_window . "
        }  
    });
    
    ";

    $getSummaryBtn = PhpExt_Button::createTextButton(TEXT_KLARNA_PAYOUTS_SUMMERY)
        ->setType(PhpExt_Button::BUTTON_TYPE_SUBMIT)
        ->setId('klarna_get_summary_btn')
        ->attachListener("click", new PhpExt_Listener(PhpExt_Javascript::functionDef(
            null, $js)))
        ->attachListener("render", new PhpExt_Listener(PhpExt_Javascript::functionDef(
            null, "$('#klarna_get_summary_btn').closest('.x-panel-btns-right').removeClass('x-panel-btns-right').addClass('x-panel-btns-center')")));
    $btnPanel->addButton($getSummaryBtn);

    $csrf_param = '&sec='. $_SESSION['admin_user']['admin_key'];

    $js = "
    var ff = Ext.getCmp('" . $formId . "');
    ff.getForm().submit(
    {
        waitMsg: ' ...',
        failure: function(ff, action) {
            Ext.MessageBox.alert('Error Message', '');
        },
        success: function(ff, action)
        {
            var params = $.param(ff.getValues());
            window.open('adminHandler.php?plugin=xt_klarna_kp&load_section=xt_klarna_kp_payouts".$csrf_param."&pg=getCsv&' + params,'_blank');
        }  
    });
    
    ";
    $getCsvBtn = PhpExt_Button::createTextButton(TEXT_KLARNA_EXPORT_CSV)
        ->setType(PhpExt_Button::BUTTON_TYPE_SUBMIT)
        ->attachListener("click", new PhpExt_Listener(PhpExt_Javascript::functionDef(
            null, $js)));

    $btnPanel->addButton($getCsvBtn);
    $btnPanel->setCssClass('x-panel-btns-center');


    $simple->addItem($btnPanel);
}




