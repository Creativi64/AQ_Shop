<?php
/*
 #########################################################################
 #                       xt:Commerce Shopsoftware
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # Copyright 2007-2018 xt:Commerce International Ltd. All Rights Reserved.
 # This file may not be redistributed in whole or significant part.
 # Content of this file is Protected By International Copyright Laws.
 #
 # ~~~~~~ xt:Commerce Shopsoftware IS NOT FREE SOFTWARE ~~~~~~~
 #
 # http://www.xt-commerce.com
 #
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # @copyright xt:Commerce International Ltd., www.xt-commerce.com
 #
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # xt:Commerce International Ltd., Kafkasou 9, Aglantzia, CY-2112 Nicosia
 #
 # office@xt-commerce.com
 #
 #########################################################################
 */

defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS . 'xt_it_recht_kanzlei/classes/class.xt_itrk.php';

if (ITRK_ACTIVATED)
{
    global $db;
    $itrk = null;
    try {

        // test mode must be set manually in db for store 1; test mode is for dev only
        // customers will use test features provided by it-rechts-kanzlei
        $sql = "SELECT `config_value` FROM ".TABLE_PLUGIN_CONFIGURATION." WHERE `config_key`='ITRK_TEST_MODE_ACTIVATED_STORE_INDEPENDENT' and `shop_id`='1'";// sqlI: no user input, fixed sql values only
        $test_mode = $db->GetOne($sql);

        $itrk =  new xt_itrk($test_mode);

        $post_xml = $_REQUEST['xml'];
        if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc())
        {
            $post_xml = stripslashes($post_xml);
        }

        // Catch errors - no data sent
        if ($itrk->test_with_local_xml_file !== true)
        {
            if (trim($post_xml) == '')
            {
                $itrk->return_error_xt('12', 'no xml data sent');
            }
        }

        // create xml object
        if ($itrk->test_with_local_xml_file !== true)
        {
            $xml = @simplexml_load_string($post_xml);
        }
        else
        {
            $xml = @simplexml_load_file(_SRV_WEBROOT . _SRV_WEB_PLUGINS . 'xt_it_recht_kanzlei/pages/beispiel.xml');  // used for testing with local xml-file
        }
        // Catch errors - error creating xml object
        if ($xml === false)
        {
            $itrk->return_error_xt('12', 'could not create xml object');
        }

        // Catch errors - action not supported
        if (($xml->action == '') OR (in_array($xml->action, $itrk->local_supported_actions) == false))
        {
            $itrk->return_error_xt('10', "action [$xml->action] not supported");
        }

        // Check api-version
        if ($xml->api_version != $itrk->local_api_version)
        {
            $itrk->return_error_xt('1', "wrong api version. remote version => [$xml->api_version]  local version => [$itrk->local_api_version]");
        }

        if(empty($xml->user_auth_token))
        {
            $itrk->return_error_xt('3', 'empty auth token');
        }
        if($xml->user_auth_token != $itrk->local_user_auth_token)
        {
            $itrk->return_error_xt('3', 'wrong auth token');
        }

        global $store_handler;
        $array_accountlist = $store_handler->getStores();

        // ---------- begin action 'push' ----------
        if ($xml->action == 'push')
        {
            // Catch errors - rechtstext_type
            if (($xml->rechtstext_type == '') OR (in_array($xml->rechtstext_type, $itrk->local_supported_rechtstext_types) == false))
            {
                $itrk->return_error_xt('4', "unsuported type [$xml->rechtstext_type]");
            }
            // Catch errors - rechtstext_text
            if (strlen($xml->rechtstext_text) < 50)
            {
                $itrk->return_error_xt('5', "rechtstext_text < 50");
            }
            // Catch errors - rechtstext_html
            if (strlen($xml->rechtstext_html) < 50)
            {
                $itrk->return_error_xt('6', "rechtstext_html < 50");
            }
            // Catch errors - rechtstext_language
            if (($xml->rechtstext_language == '') OR (in_array($xml->rechtstext_language, $itrk->local_supported_rechtstext_languages) == false))
            {
                $itrk->return_error_xt('9', "unsuported rechtstext_language [$xml->rechtstext_language]");
            }

            // check if 'user_account_id' is valid and belongs to this user or return error - for multishop systems
            if ($itrk->local_flag_multishop_system == true)
            {

                // Catch errors - no user_account_id transmitted
                if (trim($xml->user_account_id) == '')
                {
                    $itrk->return_error_xt('11', "no user_account_id (store id) transmitted");
                }

                $store_ids = array();
                foreach ($array_accountlist as $xt_store)
                {
                    $store_ids[] = $xt_store['id'];
                }
                if (!in_array($xml->user_account_id, $store_ids) )
                {
                    $itrk->return_error_xt('11', "invalid user_account_id (store id) [$xml->user_account_id]");
                }

                // set pdf required in live mode,. for test mode values are set in xt_itrk
                if ($itrk->test_with_local_xml_file !== true && $xml->rechtstext_type != 'impressum')
                {
                    switch ($xml->rechtstext_type)
                    {
                        case 'agb':
                            $plg_key = 'ITRK_SAVE_PDF_CONDITIONS';
                            break;
                        case 'datenschutz':
                            $plg_key = 'ITRK_SAVE_PDF_PRIVACY';
                            break;
                        case 'widerruf':
                            $plg_key = 'ITRK_SAVE_PDF_RESCISSION';
                            break;
                        case 'impressum':
                            $plg_key = 'ITRK_SAVE_PDF_IMPRINT';
                            break;
                    }
                    $sql = "SELECT `config_value` FROM ".TABLE_PLUGIN_CONFIGURATION." WHERE `config_key`=? and `shop_id`=?";
                    $req = $db->GetOne($sql, array( (string)$plg_key, (string)$xml->user_account_id ));
                    $itrk->local_rechtstext_pdf_required['' . $xml->rechtstext_type . ''] = $req ? true : false;
                }
            }

            if ($itrk->local_rechtstext_pdf_required['' . $xml->rechtstext_type . ''] === true)
            {

                // Catch errors - element 'rechtstext_pdf_url' empty or URL invalid
                if ($xml->rechtstext_pdf_url == '')
                {
                    $itrk->return_error_xt('7', 'element rechtstext_pdf_url is empty');
                }
                if ($itrk->url_valid($xml->rechtstext_pdf_url) !== true)
                {
                    $itrk->return_error_xt('7', 'rechtstext_pdf_url invalid');
                }

                switch ($xml->rechtstext_type)
                {
                    case 'agb':
                        if ($xml->rechtstext_language == 'de')
                            $file_name = 'AGB';
                        else
                            $file_name = 'Terms-of-Conditions';
                        break;
                    case 'datenschutz':
                        if ($xml->rechtstext_language == 'de')
                            $file_name = 'Datenschutzerklaerung';
                        else
                            $file_name = 'Privacy-notice';
                        break;
                        break;
                    case 'widerruf':
                        if ($xml->rechtstext_language == 'de')
                            $file_name = 'Widerrufsrecht';
                        else
                            $file_name = 'Right-of-rescission';
                        break;
                        break;
                    case 'impressum':
                        if ($xml->rechtstext_language == 'de')
                            $file_name = 'Impressum';
                        else
                            $file_name = 'Imprint';
                        break;
                        break;
                }


                // Download pdf file
                $file_pdf_targetfilename = $file_name . '_'.$xml->user_account_id.'_'.$xml->rechtstext_language.'.pdf'; // #### adapt the created filename to your needs, if required
                $file_pdf_target = $itrk->local_dir_for_pdf_storage . $file_pdf_targetfilename;

                $use_curl = function_exists('curl_init') ? true : false;
                $use_fopen = ini_get('allow_url_fopen') ? true : false;

                $file_pdf = @fopen($file_pdf_target, "w+");

                if ($file_pdf === false)
                {
                    $itrk->return_error_xt('7', "could not open [$file_pdf_target] for writing");
                } // catch errors

                if ($use_curl)
                {
                    $ch = curl_init();

                    curl_setopt($ch, CURLOPT_HEADER, 0);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_URL, $xml->rechtstext_pdf_url);

                    $retval = @fwrite($file_pdf, curl_exec($ch));
                    curl_close($ch);
                }
                else if ($use_fopen)
                {
                    $retval = @fwrite($file_pdf, @file_get_contents($xml->rechtstext_pdf_url));
                }
                else {
                    $itrk->return_error_xt('7', "neither curl nor allow_url_fopen found enabled");
                }

                if ($retval === false)
                {
                    $itrk->return_error_xt('7', "could not write to [$file_pdf_target] after opening it for writing");
                } // catch errors
                $retval = @fclose($file_pdf);
                if ($retval === false)
                {
                    $itrk->return_error_xt('7', "could not close file [$file_pdf_target] after writing");
                } // catch errors


                // Catch errors - downloaded file was not properly saved
                if (file_exists($file_pdf_target) !== true)
                {
                    $itrk->return_error_xt('7', "not found file [$file_pdf_target] after writing and closing");
                }

                // verify that file is a pdf
                if ($itrk->check_if_pdf_file($file_pdf_target) !== true)
                {
                    unlink($file_pdf_target);
                    $itrk->return_error_xt('7', "downloaded file $file_pdf_target is not an pdf. unlinking file");
                }

                // verify md5-hash, delete file if hash is not equal
                if (md5_file($file_pdf_target) != $xml->rechtstext_pdf_md5hash)
                {
                    unlink($file_pdf_target);
                    $itrk->return_error_xt('8', "downloaded file [$file_pdf_target] has wrong hash. unlinking file");
                }

                // create entry in xt medaia gallery 'free files'
                $sql = "SELECT `mg_id` FROM ".TABLE_MEDIA_GALLERY." WHERE `class`='files_free'";// sqlI: no user input, fixed sql values only
                $mg_id = $db->GetOne($sql);
                if ($mg_id)
                {
                    require_once(_SRV_WEBROOT._SRV_WEB_FRAMEWORK.'admin/classes/class.adminDB_DataSave.php');
                    $md = new MediaData();
                    $md->setMediaData(array('file' => $file_pdf_targetfilename, 'type' => 'files', 'class' => 'files_free', 'download_status'=>'free', 'mgID'=>$mg_id));
                }
                else {
                    $itrk->writeXtLog(0, "Media gallery 'files_free' not found.", 'warning');
                }
            }

            switch ($xml->rechtstext_type)
            {
                case 'agb':
                    $plg_key = 'ITRK_CONTENT_ID_CONDITIONS';
                    break;
                case 'datenschutz':
                    $plg_key = 'ITRK_CONTENT_ID_PRIVACY';
                    break;
                case 'widerruf':
                    $plg_key = 'ITRK_CONTENT_ID_RESCISSION';
                    break;
                case 'impressum':
                    $plg_key = 'ITRK_CONTENT_ID_IMPRINT';
                    break;
            }

            $sql = "SELECT `config_value` FROM ".TABLE_PLUGIN_CONFIGURATION." WHERE `config_key`=? and `shop_id`=?";
            $content_id = $db->GetOne($sql, array(
                (string) $plg_key,
                (string) ($xml->user_account_id)
            ));

            if (empty($content_id))
            {
                $itrk->return_error_xt('99', "no content_id found for type [$xml->rechtstext_type] and store [$xml->user_account_id]");
            }
            else
            {
                $ts = $ts_now = 0;
                // XT VERSION CHECK  different table defs since 4.2.00
                $vc = version_compare('4.2.00', _SYSTEM_VERSION);

                if (version_compare(_SYSTEM_VERSION, '4.2.00') >= 0)
                {
                    // XTC >= 4.2.00
                    // get latest timestamp
                    $sql = "SELECT `ts_last_updated` FROM ".TABLE_CONTENT_ELEMENTS." WHERE `content_id`=? AND `language_code`=? AND `content_store_id`=?";
                    $ts = $db->GetOne($sql, array(
                        $content_id,
                        (string) $xml->rechtstext_language,
                        (string) ($xml->user_account_id),
                    ));

                    // set new value in db
                    $sql = "
						INSERT INTO ".TABLE_CONTENT_ELEMENTS." (`content_id`, `language_code`, `content_store_id`, `content_body`) VALUES (?,?,?,?)
						ON DUPLICATE KEY UPDATE `content_body`=?";
                    $db->Execute($sql, array(
                        $content_id,
                        (string) $xml->rechtstext_language,
                        (string) ($xml->user_account_id),
                        (string) $xml->rechtstext_html,
                        (string) $xml->rechtstext_html
                    ));

                    $sql = "SELECT `ts_last_updated` FROM ".TABLE_CONTENT_ELEMENTS." WHERE `content_id`=? AND `language_code`=? AND `content_store_id`=?";
                    $ts_now = $db->GetOne($sql, array(
                        $content_id,
                        (string) $xml->rechtstext_language,
                        (string) ($xml->user_account_id),
                    ));
                }
                else
                {
                    // XTC < 4.2.00
                    // get latest timestamp
                    $sql = "SELECT `ts_last_updated` FROM ".TABLE_CONTENT_ELEMENTS." WHERE `content_id`=? AND `language_code`=?";
                    $ts = $db->GetOne($sql, array(
                        $content_id,
                        (string) $xml->rechtstext_language,
                    ));

                    // set new value in db
                    $sql = "
						INSERT INTO ".TABLE_CONTENT_ELEMENTS." (`content_id`, `language_code`,`content_body`) VALUES (?,?,?)
						ON DUPLICATE KEY UPDATE `content_body`=?";
                    $db->Execute($sql, array(
                        $content_id,
                        (string) $xml->rechtstext_language,
                        (string) $xml->rechtstext_html,
                        (string) $xml->rechtstext_html
                    ));

                    // get latest timestamp again
                    $sql = "SELECT `ts_last_updated` FROM ".TABLE_CONTENT_ELEMENTS." WHERE `content_id`=? AND `language_code`=?";
                    $ts_now = $db->GetOne($sql, array(
                        $content_id,
                        (string) $xml->rechtstext_language,
                    ));


                }
                if ($ts == $ts_now)
                {
                    $itrk->return_error_xt('99', 'text not saved to db (timestamp unchanged)');
                }
                else {
                    $itrk->return_success_xt($xml->rechtstext_type, $xml->user_account_id);
                }
            }

        } // ---------- end action 'push' ----------


        // ---------- begin action 'getaccountlist' ----------
        if ($xml->action == 'getaccountlist')
        {
            $itrk->return_account_list($array_accountlist);
        } // ---------- end action 'getaccountlist' ----------


        // return general error
        $itrk->return_error('99', 'general error');
    }
    catch(Exception $e)
    {
        if ($itrk) $itrk->return_error('99', $e->getMessage());
    }
    unset($itrk);
    exit();
}
