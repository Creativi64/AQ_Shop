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

class xtLink
{

    var $xtLink;
    var $params;
    var $GET_PARAMS = array();
    var $link_url;
    var $secure_link_url;
    var $_url_query_cache = array();

    function __construct ()
    {
        global $xtPlugin;

        $this->amp = '&amp;';
        $this->show_session_id = defined('_RMV_SESSION') ? constant('_RMV_SESSION') : false;

        ($plugin_code = $xtPlugin->PluginCode('class.link.php:link_top')) ? eval($plugin_code) : false;
        if (isset($plugin_return_value))
            return $plugin_return_value;

    }

    function showSessionID ($val)
    {
        $this->show_session_id = $val;
    }

    function setLinkURL ($url = '')
    {
        $this->link_url = $url;
    }

    function setSecureLinkURL ($surl = '')
    {
        $this->secure_link_url = $surl;
    }

    function unsetLinkURL ()
    {
        unset($this->link_url);
    }

    function unsetSecureLinkURL ()
    {
        unset($this->secure_link_url);
    }

    /**
     * generate index link
     *
     * @param string|array $remove_dir  directory which should be removed from link (str_replace)
     * @return string
     */
    function _index($remove_dir = '')
    {
        if(is_string($remove_dir)) $remove_dir = [$remove_dir];
        if(!in_array('xtAdmin', $remove_dir)) $remove_dir[] = '/xtAdmin';
        return $this->_link(['page' => 'index'], $remove_dir);
    }

    /**
     * generate link
     *
     * @param mixed $data    array with link data
     * @param string|array $remove_dir  directory which should be removed from link (str_replace)
     * @param boolean Deprecated $block_session  Not used in function. set to true if no session ID should be added to the generated link
     * @return string
     */
    function _link ($data, $remove_dir = '', $block_session = false)
    {
        global $xtPlugin, $page, $remove_session, $language;
        ($plugin_code = $xtPlugin->PluginCode('class.link.php:_link_top')) ? eval($plugin_code) : false;
        if (isset($plugin_return_value))
            return $plugin_return_value;

        $is_protocol_given = strpos($data['page'],'http',0) === 0;
        if($is_protocol_given) return $data['page'];

        if (empty($data['default_page'])) {
            $default_page = 'index.php';
        } else {
            $default_page = $data['default_page'];
        }

        if (empty($this->link_url)) {
            $system_http_link = _SYSTEM_BASE_HTTP;
        } else {
            $system_http_link = $this->link_url;
        }

        if (empty($this->secure_link_url)) {
            $system_https_link = _SYSTEM_BASE_HTTPS;
        } else {
            $system_https_link = $this->secure_link_url;
        }

        if (!isset($data['conn'])) $data['conn'] = 'NOSSL';
        if (($data['conn'] == 'SSL' && _SYSTEM_SSL == true) or (_SYSTEM_FULL_SSL == true))
        {
            $link_data = $system_https_link . _SRV_WEB;
        } else {
            $link_data = $system_http_link . _SRV_WEB;
        }

        if (!isset($data['seo_url']) || $data['seo_url']=='') $seo_url = $this->_getSeoUrl($data); // changed from ------ $seo_url = $this->_getSeoUrl($data);
        else $seo_url = $data['seo_url']; // this is to skip the double check for seo_url
        if ($seo_url != false && (!isset($data['seo_url']) || $data['seo_url'] == '')) {
            $data['seo_url'] = $seo_url;
        }
        // pagination links
        if (isset($data['params']) && stripos($data['params'], "next_page") !== false) {
            parse_str($data['params'], $output);
            if ($output['next_page']==1) {
                $toRemove = array('next_page');
                if (stripos($data['params'], "cat=") !== false && ($data['seo_url'] != '') && (_SYSTEM_MOD_REWRITE=='true') ) {
                    $toRemove[] = 'cat';
                }
                $data['params'] = $this->clean_url_qs($data['params'], $toRemove);

            }
        }

        if ($remove_dir != '')
        {
            if(!is_array($remove_dir))
                $remove_dir = [$remove_dir];
            foreach($remove_dir as $dir)
            {
                $link_data = str_replace($dir, '', $link_data);
            }
        }

        if ((_SYSTEM_MOD_REWRITE == 'true') && (isset($data['seo_url']) && $data['seo_url'] != '')) { // seo_url verwenden
            $link_data .= $data['seo_url'];

            if (_SYSTEM_SEO_FILE_TYPE != '' && ($default_page != 'index.php' || empty($data['default_page']))) {
                $link_data = $link_data . '.' . _SYSTEM_SEO_FILE_TYPE;
            }

            // page_action
            if ($data['page'] == 'dynamic' && !empty($page->page_action)) {
                    $link_data .= '/' . $page->page_action;
            } else if (!empty($data['paction'])){
                    $link_data .= '/' . $data['paction'];
            }

            // file type
            if ((empty($data['default_page'])) && (_SYSTEM_SEO_FILE_TYPE != '')) {
                if ($data['page']!='index' && $default_page != 'index.php')
                {
                    $link_data .= '.' . _SYSTEM_SEO_FILE_TYPE;
                }
            }

            // fix: params for seo links
            if (!empty($data['params']))
            {
                parse_str($data['params'],$paramsArr);
                $paramsArr =array_diff_key($paramsArr,array_flip(array('info', 'coID', 'cat')));
                if(count($paramsArr))
                {
                        if (!preg_match('/\?/', $link_data))
                        $link_data .= '?';
                        else
                        $link_data .= $this->amp;

                    $link_data .= http_build_query($paramsArr);
            }
            }

            return $link_data;

        } else if ((_SYSTEM_MOD_REWRITE == 'true') && $data['page'] != 'callback') { // default SEO-URL zusammenbauen
            if ((empty($data['dl_media'])) && (empty($data['default_page'])) && (_SYSTEM_SEO_URL_LANG_BASED == 'true')) {
                if (isset($data['lang_code']) && trim($data['lang_code']) != '') {
                    $link_data .= $data['lang_code'] . '/';
                } else {
                    $link_data .= $language->code . '/';
                }
            }

            // page
            if ($data['page'] == 'dynamic') {
                $link_data .= $page->page_name;
            } else {
                if ((empty($data['page'])) && (!empty($default_page))) {
                    $link_data .= $default_page;
                } else {
                    $link_data .= $data['page'];
                }
            }

            // page_action
            if ($data['page'] == 'dynamic') {
                if (!empty($page->page_action))
                    $link_data .= '/' . $page->page_action;
            } else {
                if (!empty($data['paction']))
                    $link_data .= '/' . $data['paction'];
            }
            if ((empty($data['default_page'])) && (_SYSTEM_SEO_FILE_TYPE != '')) {
                if ($data['page']!='index' && $default_page != 'index.php')
                {
                    $link_data .= '.' . _SYSTEM_SEO_FILE_TYPE;
                }
            }

            // wegen m?glicher weiterer Parameter
            $link_data .= '?';


//            }
        } else { // No SEO
            if (!empty($data['pos']))
                $link_data .= $data['pos'];

            // page
            if ($data['page'] == 'dynamic') {
                $link_data .= $default_page . '?page=' . $page->page_name;
            } else {
                $link_data .= $default_page . '?page=' . $data['page']. $this->amp . $this->_linkTypes($data['page'], isset($data['id'])? $data['id']: false );
            }

            // page_action
            if ($data['page'] == 'dynamic') {

                if (!empty($page->page_action))
                    $link_data .= $this->amp . 'page_action=' . $page->page_action;

            } else {

                if (!empty($data['paction']))
                    $link_data .= $this->amp . 'page_action=' . $data['paction'];

            }
        }

        $exclude_array = array();
        $data_exclude = array();

        if (!empty($data['params'])) {

            $data['params'] = str_replace($this->amp, '&', $data['params']);

            $data['params'] = str_replace('&', $this->amp, $data['params']);
            $data['params'] = $this->amp . $data['params'];

        }

        if (isset($data['params'])) $link_data .= $data['params'];

        ($plugin_code = $xtPlugin->PluginCode('class.link.php:_link_bottom')) ? eval($plugin_code) : false;
        $link_data = preg_replace('/' . $this->amp . $this->amp . '/', $this->amp, $link_data);

        // UBo++
        $link_data = str_replace('?' . $this->amp, '?', $link_data);
        //$link_data = rtrim($link_data, '?'.$this->amp);
        // check for last chars in url, if ?, & or $this->amp
        if (substr($link_data, -1) == '?' or substr($link_data, -1) == '&') {
            $link_data = substr($link_data, 0, -1);
        } elseif (substr($link_data, -4) == $this->amp) {
            $link_data = substr($link_data, 0, -4);
        }
        // UBo--

        if (!isset($data['keep_lang']) || $data['keep_lang'] !== true)
        {
            // content_language  '/index'
            if (_SYSTEM_SEO_URL_LANG_BASED == 'true' && substr($link_data, -8, 8) == $language->content_language . '/index')
            {
                return str_replace(substr($link_data, -8, 8), '', $link_data);
            }
            // '/index'
            if (_SYSTEM_SEO_URL_LANG_BASED != 'true' && substr($link_data, -6, 6) == '/index')
            {
                return str_replace(substr($link_data, -6, 6), '', $link_data);
            }
        }

        //language box
        if(stripos($link_data, $language->content_language.'/index?action=change_lang') !== false && _SYSTEM_SEO_URL_LANG_BASED != 'true')
        {
            return str_replace('/'.$language->content_language.'/index','/index',$link_data);
        }

        return $link_data;
    }

    function _storelink ($data)
    {
        global $xtPlugin;

        ($plugin_code = $xtPlugin->PluginCode('class.link.php:_adminlink_top')) ? eval($plugin_code) : false;
        if (isset($plugin_return_value))
            return $plugin_return_value;

        $data['pos'] = _SRV_WEB_ADMIN;

        return $this->_link($data);

    }

    function _adminlink ($data)
    {
        global $xtPlugin;

        ($plugin_code = $xtPlugin->PluginCode('class.link.php:_adminlink_top')) ? eval($plugin_code) : false;
        if (isset($plugin_return_value))
            return $plugin_return_value;

        $link = $this->_link($data, 'xtAdmin/');

        if (_SYSTEM_ADMIN_SSL) return str_replace("http://","https://",$link);
        else return $link;

    }


    function _redirect ($url,$http_response_code='')
    {
        global $xtPlugin;

        ($plugin_code = $xtPlugin->PluginCode('class.link.php:_redirect_top')) ? eval($plugin_code) : false;
        if (isset($plugin_return_value))
            return $plugin_return_value;


        $url = preg_replace('/[\r\n]+(.*)$/im', '', $url);

        $url = html_entity_decode($url);

        ($plugin_code = $xtPlugin->PluginCode('class.link.php:_redirect_bottom')) ? eval($plugin_code) : false;
        if ($http_response_code!='') header('Location: ' . $url,TRUE,$http_response_code);
        else header('Location: ' . $url);
        //session_write_close();
        exit;
        // close session and exit
        //session_close();
        //exit();

    }


    function _cleanData ($data)
    {
        global $xtPlugin;

        ($plugin_code = $xtPlugin->PluginCode('class.link.php:_cleanData_top')) ? eval($plugin_code) : false;
        if (isset($plugin_return_value))
            return $plugin_return_value;

        $search_array = array('ä', 'Ä', 'ö', 'Ö', 'ü', 'Ü', '&auml;', '&Auml;', '&ouml;', '&Ouml;', '&uuml;', '&Uuml;');
        $replace_array = array('ae', 'Ae', 'oe', 'Oe', 'ue', 'Ue', 'ae', 'Ae', 'oe', 'Oe', 'ue', 'Ue');

        ($plugin_code = $xtPlugin->PluginCode('class.link.php:_cleanData_arrays')) ? eval($plugin_code) : false;

        $data = str_replace($search_array, $replace_array, $data);

        $replace_param = '/[^a-zA-Z0-9]/';
        $data = preg_replace($replace_param, '-', $data);

        ($plugin_code = $xtPlugin->PluginCode('class.link.php:_cleanData_bottom')) ? eval($plugin_code) : false;
        return $data;

    }

    function _linkTypes ($type, $id)
    {
        global $xtPlugin;

        $link_data = '';

        ($plugin_code = $xtPlugin->PluginCode('class.link.php:_linkTypes_top')) ? eval($plugin_code) : false;
        if (isset($plugin_return_value))
            return $plugin_return_value;

        if($id)
        {
            if ($type == 'category' || $type == 'categorie')
            {
                $link_data = 'cat=' . $id;
            }

            if ($type == 'product')
            {
                $link_data = 'info=' . $id;
            }

            if ($type == 'manufacturer' || $type == 'manufacturers')
            {
                $link_data = 'mnf=' . $id;
            }

            if ($type == 'content')
            {
                $link_data = 'coID=' . $id;
            }
        }


        ($plugin_code = $xtPlugin->PluginCode('class.link.php:_linkTypes_bottom')) ? eval($plugin_code) : false;
        return $link_data;
    }

    function _getParams ($exclude = '', $include = '')
    {
        global $xtPlugin;

        $data_array = array();
        $data_array = $_GET;
        reset($data_array);

        ($plugin_code = $xtPlugin->PluginCode('class.link.php:_getParams_top')) ? eval($plugin_code) : false;
        if (isset($plugin_return_value))
            return $plugin_return_value;

        // added array $include to overwrite array $default_exclude
        if (!is_array($include)) $include = array();
        if (!is_array($exclude)) $exclude = array();
        $default_exclude = array();
        $default_exclude = array('page', 'x', 'y', 'next_page', 'page_action');
        ($plugin_code = $xtPlugin->PluginCode('class.link.php:_getParams_exclude')) ? eval($plugin_code) : false;

        $exclude = array_merge($exclude, $default_exclude);

        $url = array();
        if (is_array($data_array) && (sizeof($data_array) > 0)) {
            foreach($data_array as $key => $value) {

                if(is_array($value) && (!in_array($key, $exclude) || in_array($key, $include)))
                {
                    $this->_getParamsExtractArray($url, $key, $value); // we ignore excl/incl on sub arrays
                    continue;
                }
                else if (strlen($value) > 0 && (!in_array($key, $exclude) || in_array($key, $include))) {
                    $url[] = $key . '=' . urlencode($value);
                }
            }
        }

        $url = implode($this->amp, $url);

        ($plugin_code = $xtPlugin->PluginCode('class.link.php:_getParams_bottom')) ? eval($plugin_code) : false;
        return $url;
    }

    private function _getParamsExtractArray(&$url, $key, $value)
    {
        foreach($value as $key_v => $value_v)
        {
            if(is_array($value_v))
            {
                $this->_getParamsExtractArray($url, $key.'['.$key_v.']', $value_v);
            }
            else if (strlen($value_v) > 0) {
                $url[] = $key . '['.$key_v.']=' . urlencode($value_v);
            }
        }
    }


    function ClearUPFromDuplicate($org_page, $page_type)
    {
        global $xtLink,$seo,$db;
        if (stripos($org_page,"xtAdmin") || stripos($org_page,"cron")|| stripos($org_page,"xtInstaller") || stripos($org_page,"xtUpdater") ) return false;

        if ((_SYSTEM_MOD_REWRITE_NO_DUPLICATE_URLS=='true') && (_SYSTEM_MOD_REWRITE=='true'))
        {

            if (strpos($org_page,"sorting=")) return true; // exclude sorting option

            if ((strpos($org_page,"?")) || (strpos($org_page,"&")) )
            {
                $exp = explode("?",$org_page);
                $new_arrr= array();
                $new_ar = explode("&",$exp[1]);
                foreach($new_ar as $key=>$value)
                {
                    $second_exp= explode("=",$value);
                    if ($second_exp[0]=='page') $n=$second_exp[1];
                }

                $new_arrr= array('page'=>$n,'params'=>str_replace('page='.$n.'&',"",$exp[1]));

                $tmp_link  = $this->_getSeoUrl($new_arrr);

                if ($tmp_link!='')
                {
                    if (_SYSTEM_SEO_FILE_TYPE!='')
                    {
                        $exp = explode(".",$tmp_link);
                        if ($exp[count($exp)-1]!=_SYSTEM_SEO_FILE_TYPE) $tmp_link = $tmp_link.'.'._SYSTEM_SEO_FILE_TYPE;
                    }
                    $xtLink->_redirect($tmp_link, '301');
                }

            }
            else if (_SYSTEM_SEO_FILE_TYPE!='' && !empty($page_type) && in_array($page_type, ['product', 'categorie', 'content' ]))
            {
                $exp = explode(".",$org_page);
                $page_url_data = $seo->_cleanUpUrl($org_page);
                if (($exp[count($exp)-1]!=_SYSTEM_SEO_FILE_TYPE) && ($page_url_data["url_clean"]!='')) {
                    $xtLink->_redirect($org_page.'.'._SYSTEM_SEO_FILE_TYPE, '301');
                }
            }
            elseif (substr($org_page, -1)=='/')
            {
                $tmp_link = substr($org_page,0, -1);

                /* first check in seo table for original url with '/'. */
                $page_url_data = $seo->_cleanUpUrl($org_page);
                $clean_page = $page_url_data['url_clean'];
                $url = $seo->_UrlHash($clean_page);
                $query = "SELECT * FROM ".TABLE_SEO_URL." WHERE url_md5='".$url."' LIMIT 0,1";
                $rs = $db->CacheExecute($query);
                //if doesn't exists check for url without '/'. if exists redirect to it
                if ($rs->RecordCount()==0)
                {
                    $page_url_data = $seo->_cleanUpUrl($tmp_link);
                    $clean_page = $page_url_data['url_clean'];
                    $url = $seo->_UrlHash($clean_page);
                    $query = "SELECT * FROM ".TABLE_SEO_URL." WHERE url_md5='".$url."' LIMIT 0,1";

                    $rs = $db->CacheExecute($query);
                    if ($rs->RecordCount()>0)
                        $xtLink->_redirect($tmp_link, '301');
                }
            }

        }
    }

    public function _getSeoUrl($data)
    {
        global $db, $page, $xtPlugin, $filter, $store_handler,$language;

        $link_type = null;
        $params = array();
        if ( ! empty($data['params']))
        {
            parse_str( str_replace('&amp;', '&', $data['params']), $params);
        }

        switch ($data['page'])
        {
            case 'content':
                $link_type = 3;
                if(empty($data['id']))
                    $data['id'] = array_key_exists('coID', $params) ? $params['coID'] : 0;
                break;
            case 'product':
                $link_type = 1;
                if(empty($data['id']))
                    $data['id'] = $params['info'];
                break;
            case 'categorie':
                $link_type = 2;
                if(empty($data['id']))
                    $data['id'] = $params['cat'];
                break;
            case 'manufacturer':
            case 'manufacturers':
                if(empty($data['id']))
                    $data['id'] = $params['mnf'];
                $link_type = 4;
                break;
            default:
                ($plugin_code = $xtPlugin->PluginCode('class.link.php:_getSeoUrl')) ? eval($plugin_code) : false;
                break;
        }

        if ( ! isset($data['id'], $link_type))
        {
            if (isset($data['plugin']))
            {
                $data['id'] = $data['plugin'];
                $link_type  = 1000;
            }
            elseif (isset($data['page'])&& !isset($link_type))
            {

                if (array_key_exists($data['page'], $xtPlugin->active_modules_id)) {

                    $data['id'] = $xtPlugin->active_modules_id[$data['page']];
                    $link_type  = 1000;
                }
            }
        }

        if (empty($link_type) OR empty($data['id']))
            return false;

        $lang_code = (empty($data['lang_code']) OR ! ctype_alpha($data['lang_code']))
            ? $language->code
            : $data['lang_code'];


        $lookup_key = $link_type.'-'.$lang_code.'-'.$data['id'].'-'.$store_handler->shop_id;


        if (array_key_exists($lookup_key, $this->_url_query_cache)) {
            return $this->_url_query_cache[$lookup_key];
        } else {

            $record = $db->Execute('SELECT url_text FROM '.TABLE_SEO_URL." WHERE language_code = ? AND link_type = ? AND link_id = ? AND store_id = ?",

                array(
                    $filter->_char($lang_code),
                    $filter->_int($link_type),
                    $filter->_int($data['id']),
                    $store_handler->shop_id
                ));

            if ($record->RecordCount()) {
                $this->_url_query_cache[$lookup_key]=$record->fields['url_text'];
                return $record->fields['url_text'];
            } else {
                return false;
            }

        }

    }

    public function clean_url_qs ($url_params, $qs_key)
    {
        $params_str = '';

        $params = explode('&', $url_params);
        $out_params = array();

        foreach ($params as $key => &$param) {
            if(empty($param)) continue;
            $segments = explode('=', $param);
            if(count($segments)!=2) continue;
            list($name, $value) = explode('=', $param);
            if (in_array($name, $qs_key)) {
                unset($params[$key]);
            } else {
                $out_params[$name] = urldecode($value);
            }
        }
        $params_str = trim(http_build_query($out_params));
        // no change required
        return $params_str;
    }
    public function getCurrentUrl()
    {
        $pageURL = 'http';
        if ($_SERVER["HTTPS"] == "on") {
            $pageURL .= "s";
        }

        $pageURL .= "://";

        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
        }

        return $pageURL;
    }
}