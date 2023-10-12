<?php


defined('_VALID_CALL') or die('Direct Access is not allowed.');

if (XT_MASTER_SLAVE_ACTIVE == '1') {
    include_once _SRV_WEBROOT . _SRV_WEB_PLUGINS . 'xt_master_slave/classes/class.xt_master_slave_functions.php';

    $f = new xt_master_slave_functions();

    $options = $f->returnSelectedSlaveAttributes($params['pid']);

    if (!empty($options)){
        $tpl_data['xt_options'] = $options;
        $tpl = 'cart_xt_options.html';
        $plugin_template = new Template();
        $plugin_template->getTemplatePath($tpl, 'xt_master_slave', '', 'plugin');
        echo ($plugin_template->getTemplate('', $tpl, $tpl_data));
    }
}