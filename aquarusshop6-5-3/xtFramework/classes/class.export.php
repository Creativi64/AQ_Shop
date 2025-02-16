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

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\HandlerStack;

use Smarty\Smarty;

defined('_VALID_CALL') or die('Direct Access is not allowed.');

class export extends xt_backend_cls
{
    var $data;

    protected $_table = TABLE_FEED;
    protected $_table_lang = null;
    protected $_table_seo = null;
    protected $_master_key = 'feed_id';
    protected $_purifier = null;
    protected $logFile = _SRV_WEBROOT . 'xtLogs/export_manager.log';
    protected Smarty $Template;

    function __construct($feed_id = '')
    {
        global $db, $xtPlugin;

        ($plugin_code = $xtPlugin->PluginCode('class.export.php:_export_top')) ? eval($plugin_code) : false;
        if (isset($plugin_return_value))
            return $plugin_return_value;

        $feed_id = (int)$feed_id;

        if (!is_int($feed_id)) return false;

        $config = HTMLPurifier_Config::createDefault();
        $config->set('HTML.Allowed', '');
        $config->set('Cache.SerializerPath', _SRV_WEBROOT.'templates_c');
        $this->_purifier = new HTMLPurifier($config);

        $configBasic = HTMLPurifier_Config::createDefault();
        $configBasic->set('HTML.Allowed', 'p,hr,br,strong,ul,li,ol,em,i,table,td,tr');
        $configBasic->set('Cache.SerializerPath', _SRV_WEBROOT.'templates_c');
        $this->_purifierBasic = new HTMLPurifier($configBasic);

        $this->init($feed_id);
    }

    function init ($feed_id)
    {
        global $db, $xtPlugin;

        ($plugin_code = $xtPlugin->PluginCode('class.export.php:init_top')) ? eval($plugin_code) : false;
        if (isset($plugin_return_value))
            return $plugin_return_value;

        if ($feed_id != '') {
            $this->feed_id = (int)$feed_id;
            $rs = $db->Execute("SELECT * FROM " . TABLE_FEED . " WHERE feed_id=?", array($this->feed_id));

            ($plugin_code = $xtPlugin->PluginCode('class.export.php:init_getData')) ? eval($plugin_code) : false;
            if (isset($plugin_return_value))
                return $plugin_return_value;

            if ($rs->RecordCount() == 1) {
                $this->data = $rs->fields;
                //set default language
                if ($this->data['feed_language_code'] == 'Ne' || empty($this->data['feed_language_code'])) {
                    $query = "SELECT config_value FROM " . TABLE_CONFIGURATION . '_' . $this->data['feed_store_id'] . " WHERE config_key = '_STORE_LANGUAGE'";
                    $record = $db->Execute($query);

                    if ($record->RecordCount() == 1) {
                        $this->data['feed_language_code'] = $record->fields['config_value'];
                    }
                }
                // load shop data
                if ($this->data['feed_store_id'] > 0) {
                    $query = "SELECT * FROM " . TABLE_MANDANT_CONFIG . " where shop_id = ?";
                    $record = $db->Execute($query, array($this->data['feed_store_id']));

                    if ($record->RecordCount() == 1) {
                        $this->data['MANDANT'] = $record->fields;

                        if ($_GET['limit_lower'] && $_GET['limit_upper']) {
                            $this->limit_lower = $_GET['limit_lower'];
                            $this->limit_upper = $_GET['limit_upper'];
                        } else {
                            $this->limit_lower = 0;
                            $this->limit_upper = $this->data['feed_export_limit'];
                        }

                        if ($_GET['export_count'])
                            $this->export_count = $_GET['export_count'];

                        if ($_GET['timer_total'])
                            $this->timer_total = $_GET['timer_total'];

                        if ($_GET['cronjob'])
                            $this->cronjob = "true";
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
    }

    function setAdmin ()
    {
        $this->admin = true;
    }

    function _run ($cron_id = false)
    {
        global $db, $xtPlugin, $logHandler, $store_handler, $xtLink, $customers_status, $price;

        $obj = new stdClass;

        if ($this->url_data['edit_id']) {
            $feed_id = $this->url_data['edit_id'];
            $this->init($feed_id);

            if (!$this->feed_id || !is_array($this->data)) {
                $obj->error = 'No DATA';
                $obj->success = false;
                return $obj;
            }
        }

        $timer = new timer();
        $timer->_start();

        $xtLink->showSessionID(false);

        // get type query
        $selection_query_raw = $this->_getQuery($this->data['feed_type'], $this->data['feed_language_id']);
        if(empty($selection_query_raw))
        {
            echo json_encode(array('success'=>false));
            exit(0);
        }

        ($plugin_code = $xtPlugin->PluginCode('class.export.php:_run_getQuery')) ? eval($plugin_code) : false;
        if (isset($plugin_return_value))
            return $plugin_return_value;

        // ok first of all, write a file
        $rs = $db->Execute($selection_query_raw);

        $xt_template = new Template();
        $xt_template->getTemplate('export', '', []);
        $this->Template = $xt_template->content_smarty;

        //Template::addPluginsDir($this->Template, _SRV_WEBROOT.'xtFramework/library/smarty/xt_plugins');

        $this->Template->force_compile = true;

        $tpl_query = "SELECT * FROM " . TABLE_FEED . " WHERE feed_id=?";
        $rs_tpl = $db->Execute($tpl_query, array($this->feed_id));
        $tpl_source = $rs_tpl->fields['feed_body'];


        $this->localFile = 'export/' . $this->data['feed_filename'] . $this->data['feed_filetype'];
        $this->localFileRoot = _SRV_WEBROOT . $this->localFile;
        $this->file_name = $this->data['feed_filename'] . $this->data['feed_filetype'];

        ($plugin_code = $xtPlugin->PluginCode('class.export.php:_run_file')) ? eval($plugin_code) : false;
        if (isset($plugin_return_value))
            return $plugin_return_value;

        if ($this->limit_lower == 0)
            $fp = fopen($this->localFileRoot, "w+");
        else
            $fp = fopen($this->localFileRoot, "a");

        // write header
        if ($this->limit_lower == 0)
        {
            $this->log('starting export: '.$this->localFile, true);
            if ($this->data['feed_type'] == '3') // order products
            {
                $tpl_source_header = $rs_tpl->fields['feed_header'];
                $this->Template->assign('feed', $this->data);
                $line = $this->Template->fetch("eval:" . $tpl_source_header);

                if ($this->data['feed_linereturn_deactivated'] == 1)
                    $line = str_replace("\n", "", $line);

                if (strlen($line) > 0) {
                    if ($this->data['feed_encoding'] == "ISO-8859-1")
                        fputs($fp, mb_convert_encoding($this->replaceDelimiter($line), $this->data['feed_encoding'], "auto") . "\n");
                    else
                        fputs($fp, $this->replaceDelimiter(html_entity_decode($line)) . "\n");
                }
            }
            else fputs($fp, $this->replaceDelimiter($this->data['feed_header']) . "\n");
        }

        $customers_status = new customers_status($this->data['feed_p_customers_status']);

        // xt_customersdicount schreibt $_SESSION['customer_group_discount']
        // $_SESSION['customer_group_discount'] wird, wenn vorhanden benutzt
        // um $format_type = 'special'; zu ermitteln
        if (!empty($customers_status->customers_discount))
        {
            $total_cart = 0;

            if (strstr($customers_status->customers_discount,'#')) {
                $discounts = explode(';',$customers_status->customers_discount);

                $_SESSION['customer_group_discount'] = 0;
                $discounts = array_reverse(array_filter($discounts));
                for ($i=0;$i<sizeof($discounts);$i++)
                {
                    $dsc = explode('#', $discounts[$i]);
                    if (round($total_cart, 2) >= round($price->_calcCurrency($dsc[0]), 2))
                    {
                        $customer_group_discount = $dsc[1];
                        $_SESSION['customer_group_discount'] = $dsc[1];
                        break;
                    }
                }
            } else { // only single discount
                $_SESSION['customer_group_discount'] = floatval($customers_status->customers_discount);
            }
        }

        if ($this->data['feed_type'] == '1' || $this->data['feed_type'] == '3') { // products / order positions
            $price = new price($customers_status->customers_status_id, $customers_status->customers_status_master, $this->data['feed_p_currency_code']);
            $this->_forceLang = $this->data['feed_language_code'];
            // set link
            $xtLink->setLinkURL('http://'.$this->data['MANDANT']['shop_ssl_domain']);
            $xtLink->setSecureLinkURL('https://'.$this->data['MANDANT']['shop_ssl_domain']);

            // set shop id
            if ($this->data['feed_store_id'] > 0) $this->_setStore($this->data['feed_store_id']);
        }

        if ($this->export_count)
            $export_count = $this->export_count;
        else
            $export_count = 0;

        $log_data = array();

        // manufacturers
        $manufacturer = new manufacturer();
        $this->man_list = array();
        $_data = $manufacturer->getManufacturerList('admin');
        foreach ($_data as $mdata) {
            $this->man_list[$mdata['manufacturers_id']] = $mdata['manufacturers_name'];
        }

        $total_data = [];
        while (!$rs->EOF) {
            // get data
            $data = $this->_extractData($rs->fields, $this->data['feed_type']);

            if ($this->data['feed_type'] == '3') // order products
            {
                $data['products_quantity'] = (int) $rs->fields['products_quantity'];

                $sum_fields = ['products_weight'];
                ($plugin_code = $xtPlugin->PluginCode('class.export.php:_sum_fields')) ? eval($plugin_code) : false;
                foreach($sum_fields as $sf)
                {
                    $data[$sf] = $data[$sf] * $rs->fields['products_quantity'];
                    if (isset($total_data[$sf])) $total_data[$sf] = $total_data[$sf] + $data[$sf];
                    else $total_data[$sf] = $data[$sf];
                }
            }

            ($plugin_code = $xtPlugin->PluginCode('class.export.php:_run_data')) ? eval($plugin_code) : false;
            if (isset($plugin_return_value))
                return $plugin_return_value;

            // write body
            $this->Template->assign('data', $data);
            $line = $this->Template->fetch("eval:" . $tpl_source);

            if ($this->data['feed_linereturn_deactivated'] == 1)
                $line = str_replace("\n", "", $line);

            if (strlen($line) > 0) {
                if ($this->data['feed_encoding'] == "ISO-8859-1")
                    fputs($fp, mb_convert_encoding($this->replaceDelimiter($line), $this->data['feed_encoding'], "auto") . "\n");
                else
                    $a = fputs($fp, $this->replaceDelimiter(html_entity_decode($line)) . "\n");

                $export_count++;
            }
            $rs->MoveNext();
        }
        $rs->Close();

        if ($this->data['feed_type'] == '3') // order products
        {
            $tpl_source = $rs_tpl->fields['feed_footer'];
            $this->Template->assign('total', $total_data);
            $this->Template->assign('feed', $this->data);
            $line = $this->Template->fetch("eval:" . $tpl_source);

            if ($this->data['feed_linereturn_deactivated'] == 1)
                $line = str_replace("\n", "", $line);

            if (strlen($line) > 0) {
                if ($this->data['feed_encoding'] == "ISO-8859-1")
                    fputs($fp, mb_convert_encoding($this->replaceDelimiter($line), $this->data['feed_encoding'], "auto") . "\n");
                else
                    fputs($fp, $this->replaceDelimiter(html_entity_decode($line)) . "\n");
            }
        }

        if ($this->limit_upper < $this->total_count) {
            // redirect to next step
            global $xtLink;

            $timer->_stop();
            $t = $timer->timer_total;

            if ($this->timer_total)
                $t = $t + $this->timer_total;

            $limit = $this->limit_upper - $this->limit_lower;
            $limit_lower = $this->limit_upper;
            $limit_upper = $this->limit_upper + $limit;

            $params = [
                'limit_lower' => $limit_lower,
                'limit_upper' => $limit_upper,
                'export_count' => $export_count,
                'timer_total' => $t,
                'seckey' => _SYSTEM_SECURITY_KEY
            ];

            if ($_GET['feed_id'])
                $params['feed_id'] = $_GET['feed_id'];
            elseif ($_GET['feed_key'])
                $params['feed_key='] = $_GET['feed_key'];

            // security params ?
            if ($_GET['user'])
                $params['user'] = $_GET['user'];
            if ($_GET['pass'])
                $params['pass'] = $_GET['pass'];

            if ($this->cronjob == "true") {
                $params['cronjob'] = 1;
                $ch = curl_init($xtLink->_link(array('default_page' => 'cronjob.php')));
                curl_exec($ch);
                curl_close($ch);
            } elseif ($this->cronjob=='internal') {

                $r = new stdClass();
                $r->success = true;

                $params['executor'] = 'cron';
                $params['cron_id'] = $cron_id;
                $url = $xtLink->_adminlink(array('default_page' => 'cronstep.php'));

                $this->makeAsyncCall2($url, $params);
            }
            else {
                $params[] = ['executor' => 'admin'];
                //$xtLink->unsetLinkURL();
                //$xtLink->unsetSecureLinkURL();
                $params = http_build_query($params,'','&');
                $link = $xtLink->_adminlink(['default_page' => 'cronjob.php', 'params' => $params]);
                echo $this->_displayHTML($link, $limit_lower, $limit_upper, $this->total_count);
            }

            echo json_encode(array('success'=>true));
            exit(0);
        }

        $this->log('finishing export: '.$this->localFile, true);
        //write footer
        if ($this->data['feed_type'] != '3') // order products
        {
            fputs($fp, $this->data['feed_footer']);
        }
        fclose($fp);

        ($plugin_code = $xtPlugin->PluginCode('class.export.php:_file_finished')) ? eval($plugin_code) : false;
        if (isset($plugin_return_value))
          return $plugin_return_value;


        // ok send by mail ?
        if ($this->data['feed_mail_flag'] == 1) {
            $this->deliverTOmail();
        }
        // FTP Push ?
        if ($this->data['feed_ftp_flag'] == 1) {
            $ftp_timer = new timer();
            $ftp_timer->_start();
            $this->deliverTOftp();
            $ftp_timer->_stop();
            $log_data['runtime_ftp'] = $ftp_timer->timer_total;
        }

        // POST upload ?
        if ($this->data['feed_post_flag'] == 1) {
            $form_timer = new timer();
            $form_timer->_start();
            $this->deliverTOform();
            $form_timer->_stop();
            $log_data['runtime_form'] = $form_timer->timer_total;
        }

        // delete file ?
        if ($this->data['feed_save'] != 1) {
            unlink(_SRV_WEBROOT . 'export/' . $this->data['feed_filename'] . $this->data['feed_filetype']);
        }

        $timer->_stop();
        $timer_total = $timer->timer_total + $this->timer_total;

        $log_data['runtime_total'] = $timer_total;
        $log_data['count'] = $export_count;
        $logHandler->_addLog('success', 'xt_export', $this->data['feed_id'], $log_data);

        $xtLink->showSessionID(true);

        $this->_resetStore();

        $info = new info();

        if ($this->cronjob!='internal') {
        echo '  <style>
                            ul.success {border:solid 2px #4DAA30; background-color:#BDFFA9; padding:8px}
                            ul.success li {}
                            ul.success li {list-style:none; padding:5px 0px 2px 20px; background-image:url(xtAdmin/images/icons/accept.png); background-repeat:no-repeat; background-position:0px 4px; background-color:#BDFFA9}
					</style>
			';

            $export_file_web = '..' . str_replace('xtAdmin/', '', _SRV_WEB) . $this->localFile;
            $info->_addInfo("Export Finished"."<br />
            <a target='_blank' href='$export_file_web'>" . $this->localFile . "</a>",
                'success', 'admin');
        echo $info->info_content;
        } else {
            return true;
        }
    }

    protected function makeAsyncCall2($url, $params = [])
    {
        $r = new stdClass();
        $r->success = true;

        $timeout_ms = defined('EXPORT_CONNECT_TIMEOUT') ? (int)EXPORT_CONNECT_TIMEOUT : 5000;

        $verify = true;
        if(defined('DEV_CACERT_PEM'))
        {
            if(DEV_CACERT_PEM === false)
            {
                $verify = false;
                $this->log('makeAsyncCall | Guzzle configuring verify_peer using defined DEV_CACERT_PEM = false');
            }
            else
            {
                $cert_file = _SRV_WEBROOT . 'media/certificate/' . DEV_CACERT_PEM;
                if (file_exists($cert_file))
                {
                    $this->log('makeAsyncCall | Guzzle configuring verify_peer using defined DEV_CACERT_PEM ca-cert file ' . $cert_file);
                    $verify = $cert_file;
                }
                else {
                    $this->log('makeAsyncCall | Guzzle configuring verify_peer using not existing ca-cert file ' . $cert_file, true);
                }
            }
        }

        try
        {
            $curl = new \GuzzleHttp\Handler\CurlMultiHandler([
                CURLOPT_SSL_VERIFYHOST => $verify,
                CURLOPT_SSL_VERIFYPEER => $verify,
            ]);
            $handler = HandlerStack::create($curl);

            $client = new GuzzleHttp\Client([
                'debug' => defined('EXPORT_DEBUG_GUZZLE') && EXPORT_DEBUG_GUZZLE == true ? fopen($this->logFile, 'a+') : false,
                'timeout' => $timeout_ms / 1000,
                //'stream' => true,
                'exceptions' => true,
                'http_errors' => true,
                'verify' => $verify,

                'handler' => $handler,
                'curl' => [
                    CURLOPT_SSL_VERIFYHOST => $verify,
                    CURLOPT_SSL_VERIFYPEER => $verify,
                ],
            ]);


            $this->log('makeAsyncCall | sending request to '.$url.'?'.http_build_query($params), true);

            $promise = $client->requestAsync('GET', $url, ['query' => $params]);

            $now = time();
            while ($promise->getState() === 'pending')
            {
                if(time() > $now + 6000) throw new Exception('Export step took too long. Reduce step size in Export settings');
                $curl->tick();
            }
        }
        catch (RequestException $e)
        {
            $ctx = $e->getHandlerContext();
            if(isset($ctx['errno']) && $ctx['errno'] == 28 && strpos($e->getMessage(),'Operation timed out after'))
            {
                $this->log("makeAsyncCall | catched expected timeout RequestException after max {$timeout_ms}ms | " . $e->getMessage(), true);
            }
            else {
                $this->log('makeAsyncCall RequestException: ' . $e->getMessage(), true);
                $r->success = false;
                $r->msg = 'makeAsyncCall | RequestException: ' . $e->getMessage();
            }
        }
        catch (RuntimeException $e)
        {
            $this->log('makeAsyncCall RuntimeException: ' . $e->getMessage(), true);
            $r->success = false;
            $r->msg = 'makeAsyncCall | RuntimeException: ' . $e->getMessage();
        }
        catch (Exception $e)
        {
            $this->log('makeAsyncCall | Exception: ' . $e->getMessage(), true);
            $r->success = false;
            $r->msg = 'makeAsyncCall | Exception: ' . $e->getMessage();
        }

        $this->log('makeAsyncCall | exiting with r->success = '. ($r->success ?: '0'), true);
        return $r;
    }

    function log($data_string, $force = false)
    {
        if((defined('EXPORT_DEBUG') && EXPORT_DEBUG) || $force)
        {
            $f = fopen($this->logFile, 'a+');
            if ($f)
            {
                $d = date('[j-M-Y H:i:s e] ');
                fwrite($f, $d . $data_string . "\n");
                fclose($f);
            }
        }
    }

    function _setStore ($id)
    {
        global $store_handler;

        $this->old_store = $store_handler->shop_id;
        $store_handler->shop_id = $id;
    }

    function _resetStore ()
    {
        global $store_handler;

        if (isset($this->old_store)) {
            $store_handler->shop_id = $this->old_store;
            unset($this->old_store);
        }
    }

    function _extractData (& $data, $type)
    {
        global $price, $xtPlugin, $customers_status, $man_list, $xtLink, $db, $system_status, $mediaFiles,$mediaImages;

        $base_media = $xtLink->_link(array('default_page'=>'media', 'conn' => 'SSL'), '/xtAdmin');

        switch ($type) {
            // extract products data
            case 1 :
                $customers_status_show_price = $customers_status->customers_status_show_price;

                //fake customers_status_show_price for _getPrice in Product
                $customers_status->customers_status_show_price = 1;

                $product = product::getProduct($data['products_id'], 'export', 1, $this->_forceLang);
                if(USER_POSITION=='admin')
                {
                    $product->buildData('export', $this->_forceLang);
                }
                $data_array = $product->data;

                ($plugin_code = $xtPlugin->PluginCode('class.export.php:_extractData_data')) ? eval($plugin_code) : false;
                if (isset($plugin_return_value))
                    return $plugin_return_value;

                $data_array['products_description'] = str_replace(array("\r", "\n"), ' ', $data_array['products_description']);
                $data_array['products_short_description'] = str_replace(array("\r", "\n"), ' ', $data_array['products_short_description']);
                $data_array['products_id'] = $data['products_id'];
                $data_array['products_description_clean'] = $this->removeHTML($data_array['products_description']);
                $data_array['products_description_basic_html'] = base64_encode($this->removeBasicHTML($data_array['products_description']));
                $data_array['products_short_description_clean'] = $this->removeHTML($data_array['products_short_description']);

                if ($data_array['products_image'] != '') {
                    $tmp_img_data = explode(':', $data_array['products_image']);
                    $data_array['products_image'] = $tmp_img_data[1];
                    if(empty($data_array['products_image'])) $data_array['products_image'] = $tmp_img_data[0];
                }

                $data_array['products_image_thumb'] = $base_media . '/images/thumb/' . $data_array['products_image'];
                $data_array['products_image_info'] =  $base_media . '/images/info/' . $data_array['products_image'];
                $data_array['products_image_popup'] = $base_media . '/images/popup/' . $data_array['products_image'];
                $data_array['products_image_org'] =   $base_media . '/images/org/' . $data_array['products_image'];
                $data_array['currency'] = $this->data['feed_p_currency_code'];
                $data_array['manufacturers_name'] = '';

                if ($data_array['manufacturers_id'] > 0) {
                    $data_array['manufacturers_name'] = $this->man_list[$data_array['manufacturers_id']];
                }

				$add_sql='';
				if (isset($data_array["products_store_id"]) && $data_array["products_store_id"]>0){
					$add_sql = " and store_id = ".$data_array["products_store_id"];
				}

                // get category id
                $rs = $db->Execute(
                    "SELECT categories_id FROM " . TABLE_PRODUCTS_TO_CATEGORIES . " WHERE master_link=1 and products_id=?".$add_sql,
                    array($data['products_id'])
                );

                ($plugin_code = $xtPlugin->PluginCode('class.export.php:__extractData_category')) ? eval($plugin_code) : false;

                if ($rs->RecordCount() == 1) {
                	$nested_set = new nested_set();
                	$path = $nested_set->getCategoryPath($rs->fields['categories_id']);
                	$data_array['main_category'] = $this->getCategory($path[0]);
                    $data_array['category'] = $this->getCategory($rs->fields['categories_id']);
                    $data_array['category_tree'] = $this->buildCAT($rs->fields['categories_id']);
                    $data_array['category_tree'] = substr($data_array['category_tree'], 0, -4);
                }

                // link
                $link_array = array(
                    'page' => 'product',
                    'type' => 'product',
                    'name' => $data_array['products_name'],
                    'id' => $data_array['products_id'],
                    'seo_url' => $data_array['url_text']
                );
                $data_array['products_link'] = $xtLink->_link($link_array, '/xtAdmin', true);

                if ($this->data['feed_p_campaign'] != 0 && isset($system_status->values['campaign'][$this->data['feed_p_campaign']]['data']['ref_id'])) {
                    // attach campaigns
                    $campaign = $system_status->values['campaign'][$this->data['feed_p_campaign']]['data']['ref_id'];

                    if (substr($campaign, 0, 1) == '?' || substr($campaign, 0, 1) == '&') {
                        $campaign = substr($campaign, 1);
                    }

                    if (_SYSTEM_MOD_REWRITE == true) {
                        $data_array['products_link'] .= '?' . $campaign;
                    } else {
                        $data_array['products_link'] .= '&' . $campaign;
                    }
                }

                $customers_status->customers_status_show_price = $customers_status_show_price;

                $media_data = $mediaFiles->get_media_data($data['products_id'], 'product', 'product', 'info='.$data['products_id']);
                $data_array['free_dl'] = $product->_getPermittedMediaData($media_data['files']);
                if (null == $data_array['free_dl']) {
                	$data_array['free_dl'] = array();
                }

                $media_images = $mediaImages->get_media_images($data['products_id'], 'product');
                $data_array['products_aditional_image_org_1'] = '';
                $data_array['products_aditional_image_org_2'] = '';
                $data_array['products_aditional_image_org_3'] = '';
                $data_array['products_aditional_image_org_4'] = '';
                $data_array['products_aditional_image_org_5'] = '';
                if (is_countable($media_images) && count($media_images['images'])>0) {
                    $_n = 1;
                    foreach ($media_images['images'] as $img_id => $img_arr)  {
                        if ($_n==6) continue;
                        $data_array['products_aditional_image_org_'.$_n]=$base_media . '/images/org/' . $img_arr['data']['file'];
                        $_n++;

                    }
                }

                $data_array['url_aliases'] = array();

                $query = "SELECT url_text FROM " . TABLE_SEO_URL_REDIRECT . " WHERE link_type='1' AND link_id=? AND store_id=?";
                $urls = $db->Execute($query, array($data['products_id'], $this->data['MANDANT']['shop_id']));

                if ($urls->RecordCount() > 0) {
                	while (!$urls->EOF) {
                		if (!empty($urls->fields['url_text']))
                			$data_array['url_aliases'][] = $urls->fields['url_text'];
                		$urls->MoveNext();
                	}
                	$urls->Close();
                }

                ($plugin_code = $xtPlugin->PluginCode('class.export.php:_extractData_dataArray_bottom')) ? eval($plugin_code) : false;
                if (isset($plugin_return_value))
                    return $plugin_return_value;

                return $data_array;
                break;
            // extract order
            case '2' :
                $row_data = array();

                foreach ($data as $key => $val) {
                    $order = new order($data['orders_id'], $data['customers_id']);
                }

                $_order = array();
                $_order['order_customer'] = $order->order_customer;
                $_order['order_data'] = $order->order_data;
                $_order['order_products'] = $order->order_products;
                $_order['order_total_data'] = $order->order_total_data;
                $_order['order_total'] = $order->order_total;

                ($plugin_code = $xtPlugin->PluginCode('class.export.php:_extractData_order_bottom')) ? eval($plugin_code) : false;
                if (isset($plugin_return_value))
                    return $plugin_return_value;

                return $_order;
                break;

            // extract order position
            case '3' :
                $row_data = array();

                $customers_status_show_price = $customers_status->customers_status_show_price;

                //fake customers_status_show_price for _getPrice in Product
                $customers_status->customers_status_show_price = 1;

                $_product = product::getProduct($data['products_id'], 'export', 1, $this->_forceLang);
                if(USER_POSITION=='admin')
                {
                    $_product->buildData('export', $this->_forceLang);
                }

                ($plugin_code = $xtPlugin->PluginCode('class.export.php:_extractData_order_product_bottom')) ? eval($plugin_code) : false;
                if (isset($plugin_return_value))
                    return $plugin_return_value;

                return $_product->data;
                break;
        }
    }

    function makePlainString ($string)
    {
        global $xtPlugin;

        ($plugin_code = $xtPlugin->PluginCode('class.export.php:makePlainString_top')) ? eval($plugin_code) : false;
        if (isset($plugin_return_value))
            return $plugin_return_value;

        $string = str_replace("<br>", " ", $string);
        $string = str_replace("<br />", " ", $string);
        $string = str_replace(chr(13), " ", $string);
        $string = str_replace(";", ", ", $string);
        $string = str_replace("\"", "'", $string);
        $string = trim($string);
        $string = preg_replace('/[\r\t\n]/', '', $string);
        $string = str_replace(array("\r", "\n"), '', $string);

        ($plugin_code = $xtPlugin->PluginCode('class.export.php:makePlainString_bottom')) ? eval($plugin_code) : false;
        if (isset($plugin_return_value))
            return $plugin_return_value;

        return $string;
    }

    function removeHTML ($string)
    {
        global $xtPlugin;

        ($plugin_code = $xtPlugin->PluginCode('class.export.php:removeHTML_top')) ? eval($plugin_code) : false;
        if (isset($plugin_return_value))
            return $plugin_return_value;

        $string =  $this->_purifier->purify($string);
        $string = str_replace(chr(13), " ", $string);
        $string = str_replace(";", ", ", $string);
        $string = str_replace("\"", "'", $string);
        $string = trim($string);
        $string = preg_replace('/[\r\t\n]/', ' ', $string);
        $string = str_replace(array("\r", "\n"), ' ', $string);

        ($plugin_code = $xtPlugin->PluginCode('class.export.php:removeHTML_bottom')) ? eval($plugin_code) : false;
        if (isset($plugin_return_value))
            return $plugin_return_value;

        return $string;
    }

    function removeBasicHTML($string) {

      $string =  $this->_purifierBasic->purify($string);
      $string = str_replace(chr(13), " ", $string);
      $string = str_replace(";", ", ", $string);
      $string = str_replace("\"", "'", $string);
      $string = trim($string);
      $string = preg_replace('/[\r\t\n]/', '', $string);
      $string = str_replace(array("\r", "\n"), '', $string);
      return $string;

    }

    function replaceDelimiter ($string)
    {
        global $xtPlugin;

        ($plugin_code = $xtPlugin->PluginCode('class.export.php:replaceDelimiter_top')) ? eval($plugin_code) : false;
        if (isset($plugin_return_value))
            return $plugin_return_value;

        $string = str_replace('[<TAB>]', "\t", $string);

        ($plugin_code = $xtPlugin->PluginCode('class.export.php:replaceDelimiter_bottom')) ? eval($plugin_code) : false;
        if (isset($plugin_return_value))
            return $plugin_return_value;

        return $string;
    }

    function _getQuery ($type, $lang)
    {
        global $xtPlugin, $db;

        switch ($type) {
            case '1' : // products
                $group_check = '';
                $filter = '';
                $perm_where = '';
                $perm_and = '';

                // store permissions
                if (_SYSTEM_GROUP_CHECK == 'true' && $this->data['feed_store_id'] > 0) {
                    $perm_where = " left JOIN " . TABLE_PRODUCTS_PERMISSION . " shop ON (shop.pid = p.products_id and shop.pgroup = 'shop_" . $this->data['feed_store_id'] . "' )";

                    if (_SYSTEM_GROUP_PERMISSIONS == 'blacklist') {
                        $perm_and .= " and shop.permission IS NULL";
                    } else {
                        $perm_and .= " and shop.permission = 1";
                    }
                }

                if (_SYSTEM_GROUP_CHECK == 'true' && $this->data['feed_p_customers_status'] > 0) {
                    $perm_where .= " left JOIN " . TABLE_PRODUCTS_PERMISSION . " pgroup ON (pgroup.pid = p.products_id and pgroup.pgroup = 'group_permission_" . $this->data['feed_p_customers_status'] . "' )";

                    if (_SYSTEM_GROUP_PERMISSIONS == 'blacklist') {
                        $perm_and .= " and pgroup.permission IS NULL";
                    } else {
                        $perm_and .= " and pgroup.permission = 1";
                    }
                }

                if ($this->data['feed_p_slave'] == 0 && array_key_exists('xt_master_slave', $xtPlugin->active_modules)) {
                    $filter .= " and (p.products_master_model='' or p.products_master_model IS NULL) ";
                }

                if ($this->data['feed_p_price_min'] != '' && $this->data['feed_p_price_min'] != null) {
                    $filter .= " AND p.products_price >= '" . $this->data['feed_p_price_min'] . "'";
                }

                if ($this->data['feed_p_price_max'] != '' && $this->data['feed_p_price_max'] != null) {
                    $filter .= " AND p.products_price <= '" . $this->data['feed_p_price_max'] . "'";
                }

                if ($this->data['feed_p_quantity_min'] != '' && $this->data['feed_p_quantity_min'] != null) {
                    $filter .= " AND p.products_quantity >= '" . $this->data['feed_p_quantity_min'] . "'";
                }

                if ($this->data['feed_p_quantity_max'] != '' && $this->data['feed_p_quantity_max'] != null) {
                    $filter .= " AND p.products_quantity <= '" . $this->data['feed_p_quantity_max'] . "'";
                }

                if ($this->data['feed_p_model_min'] != '' && $this->data['feed_p_model_min'] != null) {
                    $filter .= " AND p.products_model >= '" . $this->data['feed_p_model_min'] . "'";
                }

                if ($this->data['feed_p_model_max'] != '' && $this->data['feed_p_model_max'] != null) {
                    $filter .= " AND p.products_model <= '" . $this->data['feed_p_model_max'] . "' OR p.products_model LIKE '" . $this->data['feed_p_model_max'] . "'";
                }

                if ($this->data['feed_manufacturers'] != "") {
                    $man_ids = unserialize($this->data['feed_manufacturers']);
                    if ($man_ids != false) {
                        $man_ids = array_map("intval", $man_ids);
                        $man_ids = implode(',', $man_ids);
                        $filter .= " and p.manufacturers_id IN(" . $man_ids . ")";
                    }
                }

                if ($this->data['feed_categories'] != "") {
                    $cat_ids = unserialize($this->data['feed_categories']);
                    if ($cat_ids != false) {
                        $cat_ids = array_map("intval", $cat_ids);
                        $cat_ids = implode(',', $cat_ids);
                        $perm_where .= " left JOIN " . TABLE_PRODUCTS_TO_CATEGORIES . " pcat ON (pcat.products_id = p.products_id and master_link=1)";
                        $perm_and .= " and pcat.categories_id IN(" . $cat_ids . ")";
                    }
                }

                if ($this->data['feed_p_deactivated_status'] == 1)
                    $products_status_where = " OR p.products_status=0";
                else
                    $products_status_where = "";

                ($plugin_code = $xtPlugin->PluginCode('class.export.php:_getQuery_before_products_query')) ? eval($plugin_code) : false;
                if (isset($plugin_return_value))
                    return $plugin_return_value;

                $query = "SELECT p.products_id FROM " . TABLE_PRODUCTS . " p " . $perm_where . " WHERE (p.products_status=1" . $products_status_where . ")" . $perm_and . $filter;

                ($plugin_code = $xtPlugin->PluginCode('class.export.php:_getQuery_after_products_query')) ? eval($plugin_code) : false;
                if (isset($plugin_return_value))
                    return $plugin_return_value;

                $rs = $db->Execute($query);
                $this->total_count = $rs->RecordCount();
                $limit = $this->limit_upper - $this->limit_lower;
                $query .= " ORDER BY p.products_id LIMIT " . (int)$this->limit_lower . "," . (int)$limit;

                return $query;
                break;
            case '2' : // orders
                $group_check = '';
                $status_check = '';
                $range_check = '';
                $store_check = '';

                if ($this->data['feed_store_id'] > 0) {
                    $store_check = " and shop_id = " . $this->data['feed_store_id'];
                }

                if ($this->data['feed_o_customers_status'] > 0) {
                    $group_check = " and customers_status = " . $this->data['feed_o_customers_status'];
                }

                if ($this->data['feed_o_orders_status'] > 0) {
                    $status_check = " and orders_status = " . $this->data['feed_o_orders_status'];
                }

                if ($this->data['feed_date_range_orders'] > 0) {
                    $calc_date = mktime(0 - $this->data['feed_date_range_orders'], 0, 0, date("m"), date("d"), date("Y"));
                    $calc_date = date('Y-m-d H:i:s', $calc_date);
                    $range_check = " and date_purchased >= '" . $calc_date . "'";
                }

                if ($this->data['feed_date_from_orders'] > 0) {
                    $range_check .= " AND date_purchased >= '" . $this->data['feed_date_from_orders'] . "'";
                }

                if ($this->data['feed_date_to_orders'] > 0) {
                    $range_check .= " AND date_purchased <= '" . $this->data['feed_date_to_orders'] . "'";
                }

                ($plugin_code = $xtPlugin->PluginCode('class.export.php:_getQuery_before_orders_query')) ? eval($plugin_code) : false;
                if (isset($plugin_return_value))
                    return $plugin_return_value;

                $query = "SELECT orders_id, customers_id FROM " . TABLE_ORDERS . " WHERE orders_id>0 " . $store_check . $group_check . $status_check . $range_check;

                ($plugin_code = $xtPlugin->PluginCode('class.export.php:_getQuery_after_orders_query')) ? eval($plugin_code) : false;
                if (isset($plugin_return_value))
                    return $plugin_return_value;

                $rs = $db->Execute($query);
                $this->total_count = $rs->RecordCount();
                $limit = $this->limit_upper - $this->limit_lower;
                $query .= " LIMIT " . (int)$this->limit_lower . "," . (int)$limit;

                return $query;
                break;

            case '3' : // order positions
                $group_check = '';
                $status_check = '';
                $range_check = '';
                $store_check = '';

                if ($this->data['feed_o_customers_status'] > 0) {
                    $group_check = " and customers_status = " . $this->data['feed_o_customers_status'];
                }

                if ($this->data['feed_o_orders_status'] > 0) {
                    $status_check = " and orders_status = " . $this->data['feed_o_orders_status'];
                }

                if ($this->data['feed_date_range_orders'] > 0) {
                    $calc_date = mktime(0 - $this->data['feed_date_range_orders'], 0, 0, date("m"), date("d"), date("Y"));
                    $calc_date = date('Y-m-d H:i:s', $calc_date);
                    $range_check = " and date_purchased >= '" . $calc_date . "'";
                }

                if ($this->data['feed_date_from_orders'] > 0) {
                    $range_check .= " AND date_purchased >= '" . $this->data['feed_date_from_orders'] . "'";
                }

                if ($this->data['feed_date_to_orders'] > 0) {
                    $range_check .= " AND date_purchased <= '" . $this->data['feed_date_to_orders'] . "'";
                }

                if($this->data['feed_store_id'])
                {
                    $store_check = " AND shop_id = ".$db->Quote($this->data['feed_store_id'])." ";
                }

                ($plugin_code = $xtPlugin->PluginCode('class.export.php:_getQuery_before_orders_accumulated_query')) ? eval($plugin_code) : false;
                if (isset($plugin_return_value))
                    return $plugin_return_value;

                $where_in = "SELECT orders_id FROM " . TABLE_ORDERS . " WHERE orders_id>0 " . $store_check. $group_check . $status_check . $range_check;
                $query = "SELECT distinct products_id, products_model, sum(products_quantity) as products_quantity FROM " . TABLE_ORDERS_PRODUCTS . "  WHERE orders_id IN({$where_in}) group by products_id";

                ($plugin_code = $xtPlugin->PluginCode('class.export.php:_getQuery_after_order_positions_query')) ? eval($plugin_code) : false;
                if (isset($plugin_return_value))
                    return $plugin_return_value;

                return $query;
                break;
        }
    }

    function _displayHTML ($next_target, $lower = 1, $upper = 0, $total = 0)
    {
        $process = $lower / $total * 100;

        if ($process > 100) $process = 100;

        $html = '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "https://www.w3.org/TR/html4/loose.dtd">
		           <html>
			           <head>
				           <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
					      <meta http-equiv="refresh" content="0; URL=' . $next_target . '" />
						   <title>Export</title>
		                   <style type="text/css"><!--
		                       .process_rating_light .process_rating_dark {
			                       background:#FF0000;
				                   height:15px;
					               position:relative;
						       }

							   .process_rating_light {
		                           height:15px;
		                           margin-right:5px;
			                       position:relative;
				                   width:150px;
					               border:1px solid;
						       }
		                   --></style>
		               </head>
			           <body>
				           <div class="process_rating_light"><div class="process_rating_dark" style="width:' . $process . '%">' . round($process, 0) . '%</div></div>
		                   Processing ' . $lower . ' to ' . $upper . ' of total ' . $total . '
		               </body>
			      </html>';

        return $html;
    }

    function getBody ()
    {
        return stripslashes($this->data['feed_body']);
    }

    /**
     * send file as mail attachment
     *
     */
    function deliverTOmail ()
    {
        global $xtPlugin;
        // send by mail
        // send mail to customer

        if (empty($this->data['feed_mail_body'])) {
        	$this->data['feed_mail_body'] = $this->data['feed_filename'] . $this->data['feed_filetype'];
        }
        $body_html = nl2br($this->data['feed_mail_body']);
        $attachment = array();
        $attachment[] = $this->localFileRoot;
        $exportMail = new xtMailer('none');
        $exportMail->_setFrom(_CORE_DEBUG_MAIL_ADDRESS,_CORE_DEBUG_MAIL_ADDRESS);
        if (strstr($this->data['feed_mail'],';')) {
            $emails = explode(';',$this->data['feed_mail']);
            foreach ($emails as $email) {
              $exportMail->_addReceiver($email, '');
            }
        } else {
            $exportMail->_addReceiver($this->data['feed_mail'], '');
        }
        $exportMail->_setSubject($this->data['feed_mail_header']);
        $exportMail->_setContent($body_html, $this->data['feed_mail_body']);
        $exportMail->_addAttachment($attachment);

        ($plugin_code = $xtPlugin->PluginCode('class.export.php:deliverTOmail_bottom')) ? eval($plugin_code) : false;
        if (isset($plugin_return_value))
            return $plugin_return_value;

        $exportMail->_sendMail();
    }

    /**
     * upload file to external FTP Server
     *
     */
    function deliverTOftp ()
    {
        global $logHandler;

        // user & pass given?
        if ($this->data['feed_ftp_user'] == '' or $this->data['feed_ftp_password'] == '' or $this->data['feed_ftp_server'] == '') {
            $logHandler->_addLog('error', 'xt_export', $this->data['feed_id'], array('message' => 'missing ftp login data. check export settings'));
            return;
        }

        try {
            if($this->data['feed_ftp_tls']) $connection_id = ftp_ssl_connect ($this->data['feed_ftp_server']);
            else $connection_id = ftp_connect ($this->data['feed_ftp_server']);
            if ($connection_id===false) throw new Exception('Connection to FTP Server failed');
            $login_result = @ ftp_login($connection_id, $this->data['feed_ftp_user'], $this->data['feed_ftp_password']);
            if ($login_result===false) throw new Exception('Login to FTP Server failed');


        } catch (\Exception $e) {

            $logHandler->_addLog('error', 'xt_export', $this->data['feed_id'], array('message' => $e->getMessage()));
            return;

        }


        if ($this->data['feed_ftp_passiv'] == 1)
            ftp_pasv($connection_id, 1);

        if ((!$connection_id) || (!$login_result)) {
            $logHandler->_addLog('error', 'xt_export', $this->data['feed_id'], array('message' => 'ftp login failed'));
            return;
        } else {
            // chdir if needed
            if ($this->data['feed_ftp_dir'] != '') {
                $chdir = @ ftp_chdir($connection_id, $this->data['feed_ftp_dir']);

                if ($chdir) {
                    // create and upload ftp file
                    $upload = @ ftp_put($connection_id, $this->file_name, $this->localFile, FTP_ASCII);

                    if (!$upload) {
                        $logHandler->_addLog('error', 'xt_export', $this->data['feed_id'], array('message' => 'ftp upload failed'));
                    }

                    ftp_close($connection_id);
                } else {
                    $logHandler->_addLog('error', 'xt_export', $this->data['feed_id'], array('message' => 'ftp chdir failed'));
                }
            } else {
                // just upload
                $upload = @ ftp_put($connection_id, $this->file_name, $this->localFile, FTP_ASCII);

                if (!$upload) {
                    $logHandler->_addLog('error', 'xt_export', $this->data['feed_id'], array('message' => 'ftp upload failed'));
                }

                ftp_close($connection_id);
            }
        }
    }

    /**
     * Upload file to normal formular via POST
     *
     */
    function deliverTOform ()
    {
        global $logHandler;

        $file = $this->localFile;

        $ch = curl_init();
        $data = array();
        $data[$this->data['feed_post_field']] = "@" . $file;
        curl_setopt($ch, CURLOPT_URL, $this->data['feed_post_server']);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $postResult = curl_exec($ch);

        if (curl_errno($ch)) {
            $logHandler->_addLog('error', 'xt_export', $this->data['feed_id'], array('message' => 'curl error'));
            exit ();
        }

        curl_close($ch);
    }

    function getCategory ($catID)
    {
        global $db, $xtPlugin;

        ($plugin_code = $xtPlugin->PluginCode('class.export.php:getCategory_top')) ? eval($plugin_code) : false;
        if (isset($plugin_return_value))
            return $plugin_return_value;

        if (isset($this->_CAT[$catID])) return $this->_CAT[$catID];

        $rs = $db->Execute(
            "SELECT categories_name FROM " . TABLE_CATEGORIES_DESCRIPTION . " WHERE categories_id=? and language_code=? AND categories_store_id=?",
            array($catID, $this->_forceLang, $this->data['MANDANT']['shop_id'])
        );

        if ($rs->RecordCount() == 1) {
            $this->_CAT[$catID] = $rs->fields['categories_name'];

            ($plugin_code = $xtPlugin->PluginCode('class.export.php:getCategory_bottom')) ? eval($plugin_code) : false;
            if (isset($plugin_return_value))
                return $plugin_return_value;

            return $this->_CAT[$catID];
        }
    }

    function getParent ($catID)
    {
        global $db, $xtPlugin;

        ($plugin_code = $xtPlugin->PluginCode('class.export.php:getParent_top')) ? eval($plugin_code) : false;
        if (isset($plugin_return_value))
            return $plugin_return_value;

        if (isset ($this->PARENT[$catID])) {
            return $this->PARENT[$catID];
        } else {
            $rs = $db->Execute("SELECT parent_id FROM " . TABLE_CATEGORIES . " WHERE categories_id=?", array($catID));
            $this->PARENT[$catID] = $rs->fields['parent_id'];

            ($plugin_code = $xtPlugin->PluginCode('class.export.php:getParent_bottom')) ? eval($plugin_code) : false;
            if (isset($plugin_return_value))
                return $plugin_return_value;

            return $rs->fields['parent_id'];
        }
    }

    /**
     * get Category tree
     *
     * @param mixed $catID
     * @return mixed
     */
    function buildCAT ($catID)
    {
        if (isset($this->CAT[$catID])) {
            return $this->CAT[$catID];
        } else {
            $cat = array();
            $tmpID = $catID;

            while ($this->_getParent($catID) != 0 || $catID != 0) {
                $cat[] = $this->getCategory($catID);
                $catID = $this->_getParent($catID);
            }

            $catStr = '';

            for ($i = count($cat); $i > 0; $i--) {
                $catStr .= $cat[$i - 1] . ' => ';
            }

            $this->CAT[$tmpID] = $catStr;

            return $this->CAT[$tmpID];
        }
    }

    function _getParent ($catID)
    {
        global $db, $xtPlugin;

        ($plugin_code = $xtPlugin->PluginCode('class.export.php:_getParent_top')) ? eval($plugin_code) : false;
        if (isset($plugin_return_value))
            return $plugin_return_value;

        if (isset($this->PARENT[$catID])) {
            return $this->PARENT[$catID];
        } else {
            $rs = $db->Execute("SELECT parent_id FROM " . TABLE_CATEGORIES . " WHERE categories_id=?", array($catID));
            $this->PARENT[$catID] = $rs->fields['parent_id'];

            ($plugin_code = $xtPlugin->PluginCode('class.export.php:_getParent_bottom')) ? eval($plugin_code) : false;
            if (isset($plugin_return_value))
                return $plugin_return_value;

            return $rs->fields['parent_id'];
        }
    }

    // Admin
    function _get ($ID = 0) {
        global $db, $logHandler;
		$obj = new stdClass;
        if ($this->position != 'admin') return false;

        if ($ID === 'new') {
            $obj = $this->_set(array(), 'new');
            $ID = $obj->new_id;
        }

        $ID = (int)$ID;

        if (!$ID && !isset($this->sql_limit)) {
            $this->sql_limit = "0,25";
        }

        $table_data = new adminDB_DataRead($this->_table, $this->_table_lang, $this->_table_seo, $this->_master_key, '', $this->sql_limit, $this->perm_array);

        if ($this->url_data['get_data']) {
            $data = $table_data->getData();

            if (count($data) > 0) {
                foreach ($data as $key => $arr) {
					$messages = $logHandler->getLogMessages('xt_export', $arr['feed_id'], " AND class='success'", 'LIMIT 0,1');

                    $records['last_runtime'] = ' - ';
                    $records['last_run'] = ' - ';
                    $records['last_count'] = ' - ';

                    if (!empty($messages)) {
                    	$logData = $messages[0];
                        $records['last_run'] = $logData['created'];
                        $runtime = unserialize($logData['data']);

                        $records['last_runtime'] = $runtime['runtime_total'];
                        $records['last_count'] = $runtime['count'];
                    }

                    $data[$key] = array_merge($arr, $records);
                }
            }
        } elseif ($ID) {
            $data = $table_data->getData($ID);
        } else {
            $data = $table_data->getHeader();
        }

        if ($table_data->_total_count != 0 || !$table_data->_total_count)
            $count_data = $table_data->_total_count;
        else
            $count_data = count($data);

        $obj->totalCount = $count_data;
        $obj->data = $data;

        return $obj;
    }

    function _set ($data='', $type='')
    {
        global $db, $language, $filter, $xtPlugin;

        if (!$this->url_data['edit_id'])
            $data['feed_key'] = md5(time());
        else
            $exclude_fields[] = 'feed_key';

        if($type=='new'){
            $data['feed_language_code'] = $_SESSION['selected_language'];
            $data['feed_store_id'] = 1;
            $data['feed_type'] = 1;
            $data['feed_save'] = 1;
        }

        $data['feed_p_price_min'] = str_replace(',', '.', $data['feed_p_price_min']);
        $data['feed_p_price_max'] = str_replace(',', '.', $data['feed_p_price_max']);
        $data['feed_p_quantity_min'] = str_replace(',', '.', $data['feed_p_quantity_min']);
        $data['feed_p_quantity_max'] = str_replace(',', '.', $data['feed_p_quantity_max']);

        if (!$data['feed_p_deactivated_status'])
            $data['feed_p_deactivated_status'] = 0;

        if (!$data['feed_linereturn_deactivated'])
            $data['feed_linereturn_deactivated'] = 0;


        ($plugin_code = $xtPlugin->PluginCode('class.export.php:_set')) ? eval($plugin_code) : false;
        if (isset($plugin_return_value))
            return $plugin_return_value;
        $obj = new stdClass;
        $oM = new adminDB_DataSave(TABLE_FEED, $data);
        $oM->setExcludeFields($exclude_fields);
        $obj = $oM->saveDataSet();

        return $obj;
    }

    function _getParams ()
    {
        global $language, $xtPlugin;

        $params = array();

        ($plugin_code = $xtPlugin->PluginCode('class.export.php:_getParams_top')) ? eval($plugin_code) : false;
        if (isset($plugin_return_value))
            return $plugin_return_value;

        $header['feed_id'] = array('type' => 'hidden');
        $header['feed_key'] = array('type' => 'hidden');
        $header['feed_categories'] = array('type' => 'hidden');
        $header['feed_manufacturers'] = array('type' => 'hidden');
        $header['feed_type'] = array('type' => 'dropdown',
            'url' => 'DropdownData.php?get=export_type',
        );

        $header['feed_body'] = array('type' => 'textarea', 'height' => '200', 'width' => '100%');
        $header['feed_header'] = array('type' => 'textarea', 'width' => '100%');
        $header['feed_footer'] = array('type' => 'textarea', 'width' => '100%');

        $header['feed_filename'] = array('type' => 'textfield', 'required' => true);
        $header['feed_filetype'] = array('type' => 'textfield', 'required' => true);

        $header['feed_mail_body'] = array('type' => 'textarea', 'height' => '200');

        $header['feed_mail'] = array('width' => '300');
        $header['feed_ftp_server'] = array('width' => '300');
        $header['feed_ftp_tls'] = array('type' => 'status');
        $header['feed_ftp_dir'] = array('width' => '300');

        $header['feed_date_range'] = array('type' => 'textfield');
        $header['feed_date_range_orders'] = array('type' => 'textfield');

        $header['feed_save'] = array('type' => 'status');
        $header['feed_export_limit'] = array('type' => 'textfield');
        $header['feed_linereturn_deactivated'] = array('type' => 'status');

        $header['feed_encoding'] = array('type' => 'hidden',
            'value' => 'UTF-8',
        );

        $header['feed_language_code'] = array(
            'type' => 'dropdown', // you can modyfy the auto type
            'url' => 'DropdownData.php?get=language_codes', 'text' => __text('TEXT_LANGUAGE_SELECT')
        );

        $header['feed_o_orders_status'] = array(
            'type' => 'dropdown', // you can modyfy the auto type
            'url' => 'DropdownData.php?systemstatus=order_status', 'text' => __text('TEXT_ORDERS_STATUS_SELECT')
        );

        $header['feed_p_customers_status'] = array(
            'type' => 'dropdown', // you can modyfy the auto type
            'url' => 'DropdownData.php?get=customers_status', 'text' => __text('TEXT_CUSTOMERS_STATUS_SELECT')
        );

        $header['feed_o_customers_status'] = array(
            'type' => 'dropdown', // you can modyfy the auto type
            'url' => 'DropdownData.php?get=customers_status', 'text' => __text('TEXT_CUSTOMERS_STATUS_SELECT')
        );

        $header['feed_p_currency_code'] = array(
            'type' => 'dropdown', // you can modyfy the auto type
            'url' => 'DropdownData.php?get=currencies', 'text' => __text('TEXT_CURRENCY_SELECT')
        );

        $header['feed_store_id'] = array(
            'type' => 'dropdown', // you can modyfy the auto type
            'required' => true,
            'url' => 'DropdownData.php?get=stores&skip_empty=true'
        );

        $groupingPosition = 'PRODUCTS';
        $grouping['feed_language_code'] = array('position' => $groupingPosition);
        $grouping['feed_p_currency_code'] = array('position' => $groupingPosition);
        $grouping['feed_p_customers_status'] = array('position' => $groupingPosition);
        $grouping['feed_p_campaign'] = array('position' => $groupingPosition);
        $grouping['feed_p_slave'] = array('position' => $groupingPosition);
        $grouping['feed_p_price_min'] = array('position' => $groupingPosition);
        $grouping['feed_p_price_max'] = array('position' => $groupingPosition);
        $grouping['feed_p_quantity_min'] = array('position' => $groupingPosition);
        $grouping['feed_p_quantity_max'] = array('position' => $groupingPosition);
        $grouping['feed_p_model_min'] = array('position' => $groupingPosition);
        $grouping['feed_p_model_max'] = array('position' => $groupingPosition);
        $grouping['feed_p_deactivated_status'] = array('position' => $groupingPosition);

        $header['feed_p_slave'] = array('type' => 'status');
        $header['feed_p_deactivated_status'] = array('type' => 'status');

        $header['feed_p_campaign'] = array(
            'type' => 'dropdown', // you can modyfy the auto type
            'url' => 'DropdownData.php?systemstatus=campaign&add_empty', 'text' => __text('TEXT_CAMPAIGN_SELECT')
        );
        $grouping['feed_manufacturer'] = array('position' => $groupingPosition);

        $groupingPosition = 'ORDERS';
        $grouping['feed_o_orders_status'] = array('position' => $groupingPosition);
        $grouping['feed_date_range_orders'] = array('position' => $groupingPosition);
        $grouping['feed_date_from_orders'] = array('position' => $groupingPosition);
        $grouping['feed_date_to_orders'] = array('position' => $groupingPosition);
        $grouping['feed_o_customers_status'] = array('position' => $groupingPosition);

        $groupingPosition = 'FTP';
        $grouping['feed_ftp_flag'] = array('position' => $groupingPosition);
        $grouping['feed_ftp_server'] = array('position' => $groupingPosition);
        $grouping['feed_ftp_tls'] = array('position' => $groupingPosition);
        $grouping['feed_ftp_user'] = array('position' => $groupingPosition);
        $grouping['feed_ftp_password'] = array('position' => $groupingPosition);
        $header['feed_ftp_password'] = array('type' => 'password');
        $grouping['feed_ftp_server'] = array('position' => $groupingPosition);
        $grouping['feed_ftp_dir'] = array('position' => $groupingPosition);
        $grouping['feed_ftp_passiv'] = array('position' => $groupingPosition);
        $header['feed_ftp_passiv'] = array('type' => 'status');

        $groupingPosition = 'MAIL';
        $grouping['feed_mail'] = array('position' => $groupingPosition);
        $grouping['feed_mail_flag'] = array('position' => $groupingPosition);
        $grouping['feed_mail_header'] = array('position' => $groupingPosition);
        $grouping['feed_mail_attachment'] = array('position' => $groupingPosition);
        $grouping['feed_mail_body'] = array('position' => $groupingPosition);

        $groupingPosition = 'POST';
        $grouping['feed_post_flag'] = array('position' => $groupingPosition);
        $grouping['feed_post_server'] = array('position' => $groupingPosition);
        $grouping['feed_post_field'] = array('position' => $groupingPosition);

        $groupingPosition = 'SECURITY';
        $grouping['feed_pw_flag'] = array('position' => $groupingPosition);
        $grouping['feed_pw_user'] = array('position' => $groupingPosition);
        $grouping['feed_pw_pass'] = array('position' => $groupingPosition);


        $panelSettings[] = array('position' => 'type', 'text' => __text('TEXT_EXPORT_TYPE'), 'groupingPosition' => array('PRODUCTS', 'ORDERS'));
        $panelSettings[] = array('position' => 'settings', 'text' => __text('TEXT_EXPORT_SETTINGS'), 'groupingPosition' => array('MAIL', 'FTP', 'POST'));
        $params['panelSettings'] = $panelSettings;

        //$grouping['feed_ftp_server'] = array('position' => $groupingPosition);
        if (!$this->url_data['edit_id'] && $this->url_data['new'] != true) {
            $params['include'] = array('feed_id', 'last_runtime', 'last_run', 'last_count', 'feed_title', 'feed_type', 'feed_filename', 'feed_filetype');
        } else {
            $params['exclude'] = array('last_runtime', 'last_run', 'last_count');
        }

        // Row Actions Run Export
        $rowActions[] = array('iconCls' => 'run_export', 'qtipIndex' => 'qtip1', 'tooltip' => __text('TEXT_RUN_EXPORT'));

        if ($this->url_data['edit_id'])
            $js = "var edit_id = " . $this->url_data['edit_id'] . ";";
        else
            $js = "var edit_id = record.id;";

        $js .= "Ext.Msg.confirm('" . __text('TEXT_START') . "','" . __text('TEXT_START_ASK') . "',function(btn){runEmport(edit_id,btn);})";

        $rowActionsFunctions['run_export'] = $js;

        // Row_Action Categories
        $rowActions[] = array('iconCls' => 'export_categories', 'qtipIndex' => 'qtip1', 'tooltip' => __text('TEXT_EXPORT_TO_CATEGORIES'));

        $js = '';
        if ($this->url_data['edit_id'])
            $js = "var edit_id = " . $this->url_data['edit_id'] . ";";
        else
            $js = "var edit_id = record.id;";

        $extF = new ExtFunctions();

        $js .= $extF->_RemoteWindow("TABTEXT_CATEGORY", "TABTEXT_CATEGORY", "adminHandler.php?load_section=export_categories&pg=getTreePanel&export_id='+edit_id+'", '', array(), 800, 600) . ' new_window.show();';

        $rowActionsFunctions['export_categories'] = $js;

        // Row_Action Manufacturers
        $rowActions[] = array('iconCls' => 'export_manufacturers', 'qtipIndex' => 'qtip1', 'tooltip' => __text('TEXT_EXPORT_TO_MANUFACTURERS'));

        $js = '';
        if ($this->url_data['edit_id'])
            $js = "var edit_id = " . $this->url_data['edit_id'] . ";";
        else
            $js = "var edit_id = record.id;";

        $extF = new ExtFunctions();

        $js .= $extF->_RemoteWindow("FEED_MANUFACTURER", "FEED_MANUFACTURER", "adminHandler.php?load_section=export_manufacturers&pg=getTreePanel&export_id='+edit_id+'", '', array(), 800, 600) . ' new_window.show();';

        $rowActionsFunctions['export_manufacturers'] = $js;

        $js = '';
        $js = "function runEmport(edit_id,btn){
	  		var edit_id = edit_id;
	  		if (btn == 'yes') {
				addTab('row_actions.php?type=export_manager&seckey="._SYSTEM_SECURITY_KEY."&feed_id='+edit_id,'" . __text('TEXT_START') . "');
			}
		};";

        $params['rowActionsJavascript'] = $js;
        $params['rowActions'] = $rowActions;

        if (!$this->url_data['edit_id'] && $this->url_data['new'] != true)
            $params['rowActionsFunctions'] = $rowActionsFunctions;

        $params['header'] = $header;
        $params['grouping'] = $grouping;
        $params['master_key'] = 'feed_id';
        $params['default_sort'] = 'feed_id';
		$params['display_checkItemsCheckbox']  = true;
		$params['display_checkCol']  = true;

		/*
        $extF = new ExtFunctions();
        $js = "addTab('adminHandler.php?load_section=export_tpls_import&new=true','" . __text('TEXT_EXPORT_TPLS_IMPORT') . "');";
        $UserButtons['options_add'] = array('text' => 'TEXT_EXPORT_TPLS_IMPORT', 'style' => 'options_add', 'icon' => 'add.png', 'acl' => 'edit', 'stm' => $js);

        $params['display_options_addBtn'] = true;
        $params['UserButtons'] = $UserButtons;
		*/

        ($plugin_code = $xtPlugin->PluginCode('class.export.php:_getParams_bottom')) ? eval($plugin_code) : false;
        if (isset($plugin_return_value))
            return $plugin_return_value;

        return $params;
    }

    function _unset ($id = 0) {
        global $db, $xtPlugin,$logHandler;

        ($plugin_code = $xtPlugin->PluginCode('class.export.php:_unset_top')) ? eval($plugin_code) : false;
        if (isset($plugin_return_value))
            return $plugin_return_value;

        if ($id == 0) return false;
        if ($this->position != 'admin') return false;
        $id = (int)$id;
        if (!is_int($id)) return false;

        $db->Execute("DELETE FROM " . TABLE_FEED . " WHERE feed_id = ?", array($id));
        $logHandler->clearLogMessages('xt_export', $id);

        ($plugin_code = $xtPlugin->PluginCode('class.export.php:_unset_bottom')) ? eval($plugin_code) : false;
    }

}
