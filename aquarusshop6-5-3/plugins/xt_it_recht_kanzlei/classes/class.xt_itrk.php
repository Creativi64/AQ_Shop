<?php
/*
 #########################################################################
 #                       xt:Commerce Shopsoftware
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # Copyright 2021 xt:Commerce GmbH All Rights Reserved.
 # This file may not be redistributed in whole or significant part.
 # Content of this file is Protected By International Copyright Laws.
 #
 # ~~~~~~ xt:Commerce Shopsoftware IS NOT FREE SOFTWARE ~~~~~~~
 #
 # https://www.xt-commerce.com
 #
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # @copyright xt:Commerce GmbH, www.xt-commerce.com
 #
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # xt:Commerce GmbH, Maximilianstrasse 9, 6020 Innsbruck
 #
 # office@xt-commerce.com
 #
 #########################################################################
 */

defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS . 'xt_it_recht_kanzlei/classes/constants.php';
require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS . 'xt_it_recht_kanzlei/classes/class.it_recht_kanzlei.php';

class xt_itrk extends it_recht_kanzlei {

    public function __construct($local_test_mode = false)
    {
        global $db;

        if ($local_test_mode)
        {
            $this->local_user_auth_token = 'd485ab132e8330326cc973786e668401';
            $this->test_with_local_xml_file = true;
            $this->local_supported_rechtstext_types = array('agb');
        }
        else {
            // token is store independent, read always from store 1
            $sql = "SELECT `config_value` FROM ".TABLE_PLUGIN_CONFIGURATION." WHERE `config_key`='ITRK_API_TOKEN_STORE_INDEPENDENT' and `shop_id`='1'"; // sqlI: no user input, fixed sql values only
            $this->local_user_auth_token = $db->GetOne($sql);

            $this->test_with_local_xml_file = false;
            $this->local_supported_rechtstext_types = array('agb', 'datenschutz', 'widerruf', 'impressum');
        }
        $this->local_rechtstext_pdf_required = array('agb' => false, 'datenschutz' => false, 'widerruf' => false, 'impressum' => false);

        $this->local_flag_multishop_system = true;
        $this->local_dir_for_pdf_storage =  _SRV_WEBROOT._SRV_WEB_MEDIA_FILES;

        $this->shop_version = _SYSTEM_VERSION;
        $this->module_version = _ITRK_MODULE_VERSION;

        parent::__construct();
    }

    function url_valid($url, $limit_to_host = '')
    {
        // check for allowed URLs
        if (
            ($this->test_with_local_xml_file ===true && $this->local_limit_download_from_host == '')
        )
        {
            return true;
        }
        else
        {
            return parent::url_valid($url);
        }
    }

    public static function writeXtLog($error_code, $msg, $class)
    {
        global $logHandler;
        $logHandler->_addLog($class, 'xt_it_recht_kanzlei', 0, array('error_code' => $error_code, 'message' => $msg));
    }

    // return error and end script
    function return_error_xt($error_code, $msg = '')
    {
        error_log("xt_it_recht_kanzlei error [$error_code] ".$msg);

        parent::return_error($error_code, $msg);

        self::writeXtLog($error_code, $msg, 'error');

        exit();
    }

    // return success and end script
    function return_success_xt($text_type, $store_id)
    {
        parent::return_success();

        self::writeXtLog(0, "Imported [$text_type] for store [$store_id]", 'success');

        exit();
    }

}