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

//define('LANGUAGE_CONTENT_TRANSLATED_FILTER','allow_edit');
//define('LANGUAGE_CONTENT_TRANSLATED_FILTER','language_status');

class language_content extends xt_backend_cls
{

    var $default_language = _STORE_LANGUAGE;

    protected $_table = TABLE_LANGUAGE_CONTENT;
    protected $_table_lang = null;
    protected $_table_seo = null;
    protected $_master_key = 'language_content_id';
    public $environment_language;

    private $_new_key_name = '';

    public function __construct()
    {
        if(!defined('LANGUAGE_CONTENT_TRANSLATED_FILTER'))
            define('LANGUAGE_CONTENT_TRANSLATED_FILTER','NO_FILTER');
    }

    function _buildData ($id)
    {
        global $db, $xtPlugin, $store_handler;

        ($plugin_code = $xtPlugin->PluginCode('class.language_content.php:_buildData_top')) ? eval($plugin_code) : false;
        if (isset($plugin_return_value))
            return $plugin_return_value;

        $record = $db->CacheExecute(
            _CACHETIME_LANGUAGE_CONTENT, "SELECT * FROM " . TABLE_LANGUAGE_CONTENT . " WHERE language_content_id = ?",
            array($id)
        );

        if ($record->RecordCount() > 0) {
            while (!$record->EOF) {
                $data = $record->fields;
                $record->MoveNext();
            }
            $record->Close();
            ($plugin_code = $xtPlugin->PluginCode('class.language_content.php:_buildData_bottom')) ? eval($plugin_code) : false;
            return $data;
        } else {
            return false;
        }
    }


    function _getLanguageContentList ($list_type = 'store')
    {
        global $db, $xtPlugin, $store_handler;
        $qry_where = '';
        ($plugin_code = $xtPlugin->PluginCode('class.language_content.php:_getLanguagelist_top')) ? eval($plugin_code) : false;
        if (isset($plugin_return_value))
            return $plugin_return_value;

        ($plugin_code = $xtPlugin->PluginCode('class.language_content.php:_getLanguagelist_qry')) ? eval($plugin_code) : false;

        $record = $db->CacheExecute("SELECT * FROM " . TABLE_LANGUAGE_CONTENT . " " . $qry_where . "");
        while (!$record->EOF) {
            $data[] = $record->fields;
            $record->MoveNext();
        }
        $record->Close();

        ($plugin_code = $xtPlugin->PluginCode('class.language_content.php:_getLanguagelist_bottom')) ? eval($plugin_code) : false;
        return $data;
    }

    function _getLanguageContent ($class)
    {
        global $db, $xtPlugin;

        ($plugin_code = $xtPlugin->PluginCode('class.language.php:_getLanguageContent_top')) ? eval($plugin_code) : false;
        if (isset($plugin_return_value))
            return $plugin_return_value;

        if(empty($class) || !in_array($class,array('both', 'store', 'admin'))) return;

        $input_arr = array($this->environment_language, 'both');
        if(in_array($class,array('store', 'admin')))
        {
            $input_arr[] = $class;
        }
        $place_holder = array_map(function($el){ return '?'; }, $input_arr);
        array_shift($place_holder);

        $orderDir = 'ASC';
        if($class == 'store')
        {
            $orderDir = 'DESC';
        }

        _buildDefine($db, TABLE_LANGUAGE_CONTENT, 'language_key', 'language_value', "language_key != '' and language_code != '' and language_code=? and class IN (" . implode(',', $place_holder) . ") ORDER BY class $orderDir", $input_arr);
    }

    function _importYML ($file, $code)
    {
        global $db;

        if (!file_exists($file)) return;

        $lines = file($file);

        $this->fixLanguageContent();

        $this->importYML_intern($lines, $code);
    }

    protected function fixLanguageContent($code = false)
    {
        global $db;

        $where = '';
        $params = [];
        if($code)
            $where = ' AND language_code = ? ';

        $db->Execute("UPDATE ".TABLE_LANGUAGE_CONTENT." set plugin_key = '' WHERE plugin_key LIKE '%.%' ". $where);
    }

    protected function importYML_intern ($lines, $code)
    {
        global $db;

        // load language definitions
        $definitions = array();
        $rs = $db->Execute(
            "SELECT `language_content_id`, `language_key`, `readonly`, `class` FROM " . TABLE_LANGUAGE_CONTENT . " WHERE language_code=?",
            array($code)
        );
        if ($rs->RecordCount() > 0) {
            while (!$rs->EOF) {
                $definitions[$rs->fields['language_key'] . $rs->fields['class']] = $rs->fields;
                $rs->MoveNext();
            }
        }

        foreach ($lines as $line_num => $line) {
            $delimiterPos = strpos($line, '=', 0);

            if ($delimiterPos === false) {
                continue;
            }
            $systemPart = substr($line, 0, $delimiterPos);
            $value = substr($line, $delimiterPos+1);
            $parts = explode('.', $systemPart);
            $force_override = false;
            if(count($parts) <=3)
                list($plugin, $class, $key) = $parts;
            else
            {
                list($plugin, $class, $key, $flags) = $parts;
                $flags = explode('&', $flags);
                $force_override = in_array('readonly', $flags); // ja, genau dann erlauben wir überschreiben
            }

            if($class == 'wizard') continue;

            $language_value = trim(str_replace("\n", '', $value));

            $insert_data = array();
            $insert_data['language_key'] = $key;
            $insert_data['language_code'] = $code;
            $insert_data['language_value'] = $language_value;
            $insert_data['class'] = $class;
            $insert_data['plugin_key'] = $plugin;
            $insert_data['translated'] = !empty($language_value) && $language_value != $key ? 1 : 0;
            $insert_data['readonly'] = $read_only = (int) $force_override;

            // If there is no translation or translation is not readonly
            if (!isset($definitions[$key . $class])) {
                // if it was not found because of changed class we first delete the old entry
                //$db->Execute("DELETE FROM ".TABLE_LANGUAGE_CONTENT." WHERE language_code=? AND language_key=?", array($code, $key));
                $db->AutoExecute(TABLE_LANGUAGE_CONTENT, $insert_data);
            }
            else if (empty($definitions[$key . $class]['language_content_id'])) {
                $db->AutoExecute(TABLE_LANGUAGE_CONTENT, $insert_data);
            }
            else if (!$definitions[$key . $class]['readonly'] || $force_override) {
                $update = array();
                $update['language_value'] = $insert_data['language_value'];
                $update['readonly'] = $insert_data['readonly'];
                $db->AutoExecute(TABLE_LANGUAGE_CONTENT, $update, "UPDATE", "`language_content_id`='" . (int)$definitions[$key . $class]['language_content_id']. "'");
            }
        }

        xt_cache::deleteCache('language_content');
    }

    /**
     * @throws Exception
     */
    function initEmptyKeys($code)
    {
        $lines = [];
        $rs = $this->getEmptyKeys($code);

        if ($rs->RecordCount() > 0)
        {
            while (!$rs->EOF)
            {
                $line = $this->getYmlLine($rs->fields, false);
                $lines[] = $line . $rs->fields['language_key']. "\n";
                $rs->MoveNext();
            }
        }
        $rs->Close();

        $lines = array_unique($lines);

        $this->importYML_intern($lines, $code);
    }

    /**
     * @throws Exception
     */
    function initMissingKeys($code)
    {
        $lines = [];
        $rs = $this->getMissingKeys($code);

        foreach ($rs as $key => $data)
        {
            $line = $this->getYmlLine($data, false);
            $lines[] = $line . "\n";
        }

        $lines = array_unique($lines);

        $this->importYML_intern($lines, $code);
    }

    function _importXML ($file, $code, $replace = false)
    {
        global $db;

        if (!file_exists($file)) return;

        $xml = file_get_contents($file);
        $xml_data = XML_unserialize($xml);
        //     debugbreak();

        // load language definitions
        $definitions = array();
        $rs = $db->Execute(
            "SELECT language_key FROM " . TABLE_LANGUAGE_CONTENT . " WHERE language_code=?",
            array($code)
        );
        if ($rs->RecordCount() > 0) {
            while (!$rs->EOF) {
                $definitions[$rs->fields['language_key']] = '1';
                $rs->MoveNext();
            }
        }

        foreach ($xml_data['xtcommerce_language']['phrase'] as $key => $val) {

            if ($val['class'] == 'wizard') continue;

            if (!isset($definitions[$val['language_key']])) { // key not existing
                $insert_data = array();
                $insert_data['language_key'] = $val['language_key'];
                $insert_data['language_code'] = $code;
                $insert_data['language_value'] = trim($val['language_value']);
                $insert_data['class'] = $val['class'];
                $insert_data['plugin_key'] = $val['plugin_key'];
                $insert_data['translated'] = '1';
                $db->AutoExecute(TABLE_LANGUAGE_CONTENT, $insert_data);
            }
        }

        // now get untranslated definitions and insert //TODO check if EN is existing  
        $sql = "SELECT * FROM " . TABLE_LANGUAGE_CONTENT . " a WHERE a.language_code='en' and a.language_key NOT IN (SELECT language_key FROM " . TABLE_LANGUAGE_CONTENT . " b WHERE b.language_code=?)";
        $rs = $db->Execute($sql, array($code));
        if ($rs->RecordCount() > 0) {
            while (!$rs->EOF) {
                $insert_data = array();
                $insert_data['language_key'] = $rs->fields['language_key'];
                $insert_data['language_code'] = $code;
                $insert_data['language_value'] = $rs->fields['language_value'];
                $insert_data['class'] = $rs->fields['class'];
                $insert_data['plugin_key'] = $rs->fields['plugin_key'];
                $insert_data['translated'] = '0';
                $db->AutoExecute(TABLE_LANGUAGE_CONTENT, $insert_data);
                $rs->MoveNext();
            }
        }
        xt_cache::deleteCache('language_content');

        return true;
    }

    function _exportYML ($id, $type = 'all')
    {
        global $db;

        $this->fixLanguageContent();

        $id = (int)$id;

        $sql = "SELECT `code` FROM " . TABLE_LANGUAGES . " WHERE languages_id = ?";
        $code = $db->GetOne($sql, array($id));

        $r = new stdClass();
        $r->success = true;

        if ($code) {

            $file = $code . '_content.yml';
            if ($type == 'untranslated' || $type == 'lang_class_problems') $file = $type.'_' . $file;
            $fp = fopen(_SRV_WEBROOT . 'export/' . $file, 'w');

            switch($type)
            {
                case 'all':
                    $sql = "SELECT * FROM " . TABLE_LANGUAGE_CONTENT . " WHERE class!='wizard' AND language_code = ?";
                    $rs = $db->Execute($sql, array($code));
                    if ($rs->RecordCount() > 0)
                    {
                        while (!$rs->EOF)
                        {
                            $line = $this->getYmlLine($rs->fields);
                            fputs($fp, $line);
                            $rs->MoveNext();
                        }
                    }
                    $rs->Close();
                    break;
                case 'untranslated':
                    /*
                     SELECT lc1.language_key, lc1.class, lc1.plugin_key
                    FROM
                    (SELECT language_key,class, plugin_key FROM ".$this->_table." GROUP BY language_key, class, plugin_key) lc1
                    LEFT JOIN ".$this->_table." lc2 ON lc1.language_key = lc2.language_key and lc1.class = lc2.class and lc2.language_code = 'fr'
                    WHERE lc2.language_key IS NULL ORDER BY lc1.class, lc1.language_key
                    ) missing_keys
                    UNION
                    SELECT language_key, class, plugin_key
                    FROM ".$this->_table."
                    WHERE language_code = 'fr' and language_value = ''
                    ORDER BY class, language_key;
                     */

                    // wir könnten alles auf einen schlag holen
                    // wollen aber kommentar zeilen im yml
                    // daher zwei separate abfragen

                    // empty/no translation
                    $sql = "SELECT language_key, class, plugin_key, readonly 
                        FROM ".$this->_table."
                        WHERE class!='wizard' and language_code = ? and (language_value = '' or language_value = language_key)
                        ORDER BY class, language_key";
                    $rs = $db->Execute($sql, array($code));

                    if ($rs->RecordCount() > 0)
                    {
                        fputs($fp, '## keys with empty / no translation for language '.$code."\n");
                        while (!$rs->EOF)
                        {
                            $line = $this->getYmlLine($rs->fields);
                            fputs($fp, $line);
                            $rs->MoveNext();
                        }
                    }
                    $rs->Close();

                    // missing keys
                    $rs = $this->getMissingKeys($code);

                    foreach ($rs as $key => $data)
                    {
                        $line = $this->getYmlLine($data);
                        fputs($fp, $line);
                    }

                    fputs($fp, '## missing keys for language '.$code."\n");
                    foreach ($rs as $key => $data)
                    {
                        $line = $this->getYmlLine($data);
                        fputs($fp, $line);
                    }

                    break;
                case 'lang_class_problems':
                    fputs($fp, '## Alle keys die Klasse both sind und darüber hinaus auch noch store oder admin haben. Oder sogar beides. Sprache: '.$code."\n");
                    fputs($fp, "## https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/917608/Sprachtexte\n");

                    $res = $db->GetArray("SELECT distinct lc1.language_key, lc2.class FROM ".TABLE_LANGUAGE_CONTENT." lc1
                        left join ".TABLE_LANGUAGE_CONTENT." lc2 ON lc1.language_key = lc2.language_key
                        WHERE lc1.class = 'both' and lc2.class != 'both'");

                    foreach ($res as $key)
                    {
                        fputs($fp, str_pad($key['language_key'],50)." .".$key['class']. "\n");
                    }
                    /*  alte variante
                    $keys = $db->GetArray("SELECT distinct language_key FROM ".TABLE_LANGUAGE_CONTENT." where language_code = ?", [$code]);
                    foreach ($keys as $key)
                    {
                        $classes = $db->GetArray("SELECT distinct class FROM ".TABLE_LANGUAGE_CONTENT." where language_key = ?", [$key['language_key']]);
                        if(count($classes) > 1)
                        {
                            $has_both = false;
                            foreach($classes as $class)
                            {
                                if($class['class'] == 'both')
                                {
                                    fputs($fp, $key['language_key']. "\n");
                                }
                            }
                        }
                    }
                    */
                    break;
                default:
                    fclose($fp);
                    $r->msg = "Nichts zu tun. Unbekannter oder fehlender export Typ [".$type."]";
                    return $r;
            }
            fclose($fp);
            $r->msg = __text('TEXT_LANG_EXPORT_SUCCESS') . ' > '.'export/' . $file;
            return $r;
        }

        $r->msg = "Nichts zu tun. Sprache nicht angegeben";
        return $r;
    }

    /**
     * vergleiche alle existierenden keys mit denen für gegebene Sprache
     * die in gegebener Sprache nicht vorhandenen keys werden zurückgegeben
     *
     * @param $code
     * @return array
     */
    function getMissingKeys($code, $throw_ex = false)
    {
        global $db;

        GLOBAL $ADODB_THROW_EXCEPTIONS;
        $ADODB_THROW_EXCEPTIONS = true;

        $all_keys_data = [];
        $all_keys = $db->GetArray("SELECT * FROM ".TABLE_LANGUAGE_CONTENT. " WHERE `class`!= 'wizard' GROUP BY language_key");
        foreach($all_keys as $key)
        {
            $all_keys_data[$key['language_key']] = $key;
        }

        $all_keys_lang_data = [];
        $all_keys_lang = $db->GetArray("SELECT * FROM ".TABLE_LANGUAGE_CONTENT. " WHERE language_code = ? and `class`!= 'wizard'  GROUP BY language_key", [$code]);
        foreach($all_keys_lang as $key)
        {
            $all_keys_lang_data[$key['language_key']] = $key;
        }

        $diff = [];
        foreach($all_keys_data as $key => $data)
        {
            if(!array_key_exists($key, $all_keys_lang_data))
            {
                $diff[$key] = $data;
            }
        }

        return $diff;
    }

    /**
     * @param $code
     * @return ADORecordSet|ADORecordSet_array|ADORecordSet_empty|bool|null
     * @throws Exception
     */
    function getEmptyKeys($code, $throw_ex = false, $ignore_read_only = false)
    {
        global $db;

        GLOBAL $ADODB_THROW_EXCEPTIONS;
        $ADODB_THROW_EXCEPTIONS = true;

        $sql = "SELECT lc.language_key, lc.class, lc.plugin_key
                FROM ".TABLE_LANGUAGE_CONTENT." lc
                WHERE lc.class != 'wizard' AND lc.language_code = ? AND TRIM(REPLACE(REPLACE(lc.language_value, '\r', ''), '\n', '')) IN ('', NULL)";

        if(!$ignore_read_only)
            $sql .= ' AND readonly = 0';

        $rs = new ADORecordSet_empty();
        try
        {
            $rs = $db->Execute($sql, array($code));
        }
        catch (Exception $e)
        {
            if($throw_ex) throw $e;
        }

        return $rs;
    }

    private function getYmlLine($data, $addNewLine = true)
    {
        $string = str_replace(chr(13), " ", $data['language_value'] ?: "");
        $string = preg_replace('/[\r\t\n]/', '', $string);
        $key = $data['plugin_key'];
        if ($key == 'NULL') $key = '';
        $readonly = array_key_exists('readonly', $data) && $data['readonly'] == 1 ? '.readonly' : '';
        $line = $key . '.' . $data['class'] . '.' . $data['language_key'] . $readonly . '=' . $string;
        if($addNewLine) $line .= "\n";

        return $line;
    }

    function _exportYML_OLD ($id, $type = 'all')
    {
        global $db;

        $id = (int)$id;

        $sql = "SELECT * FROM " . TABLE_LANGUAGES . " WHERE languages_id = ?";
        $rs = $db->Execute($sql, array($id));

        $code = $rs->fields['code'];

        if ($rs->RecordCount() == 1) {

            $file = $code . '_content.yml';
            if ($type == 'untranslated') $file = 'untranslated_' . $file;
            $fp = fopen(_SRV_WEBROOT . 'export/' . $file, 'w');

            // phrases
            $data = array();
            $sql = "SELECT * FROM " . TABLE_LANGUAGE_CONTENT . " WHERE language_code = ?";
            if ($type == 'untranslated') $sql .= " and translated='0'";
            $rs = $db->Execute($sql, array($code));
            if ($rs->RecordCount() > 0) {
                while (!$rs->EOF) {

                    $string = str_replace(chr(13), " ", $rs->fields['language_value']);
                    $string = preg_replace('/[\r\t\n]/', '', $string);
                    $key = $rs->fields['plugin_key'];
                    if ($key == 'NULL') $key = '';
                    $line = $key . '.' . $rs->fields['class'] . '.' . $rs->fields['language_key'] . '=' . $string . "\n";
                    fputs($fp, $line);

                    $rs->MoveNext();
                }
            }

            fclose($fp);
        } else {
            return;
        }
    }

    /**
     * export language definitions as xml files
     *
     * @param mixed $id
     * @param mixed $type  all,untranslated
     */
    function _exportXML ($id, $type = 'all')
    {
        global $db;

        include_once _SRV_WEBROOT . 'xtFramework/library/phpxml/xml.php';
        $id = (int)$id;

        $sql = "SELECT * FROM " . TABLE_LANGUAGES . " WHERE languages_id = ?";
        $rs = $db->Execute($sql, array($id));

        $code = $rs->fields['code'];

        if ($rs->RecordCount() == 1) {
            $data = array();
            $data['xtcommerce_language']['name'] = $rs->fields['name'];
            $data['xtcommerce_language']['code'] = $rs->fields['code'];
            $data['xtcommerce_language']['image'] = $rs->fields['image'];
            $data['xtcommerce_language']['sort_order'] = $rs->fields['sort_order'];
            $data['xtcommerce_language']['language_charset'] = $rs->fields['language_charset'];
            $data['xtcommerce_language']['default_currency'] = $rs->fields['default_currency'];
            $data['xtcommerce_language']['font'] = $rs->fields['font'];
            $data['xtcommerce_language']['font_size'] = $rs->fields['font_size'];
            $data['xtcommerce_language']['font_position'] = $rs->fields['font_position'];
            $data['xtcommerce_language']['setlocale'] = $rs->fields['setlocale'];
            $data['xtcommerce_language']['translated'] = $rs->fields['translated'];

            $xml = XML_serialize($data);
            $file = $code . '.xml';
            if ($type == 'untranslated') $file = 'untranslated_' . $file;
            $fp = fopen(_SRV_WEBROOT . 'export/' . $file, 'w');
            fputs($fp, $xml);
            fclose($fp);

            // phrases
            $data = array();
            $sql = "SELECT * FROM " . TABLE_LANGUAGE_CONTENT . " WHERE language_code = ?";
            if ($type == 'untranslated') $sql .= " and translated='0'";
            $rs = $db->Execute($sql, array($code));
            if ($rs->RecordCount() > 0) {
                while (!$rs->EOF) {
                    $data['xtcommerce_language']['phrase'][] = array('language_key' => $rs->fields['language_key'], 'language_value' => $rs->fields['language_value'], 'class' => $rs->fields['class'], 'plugin_key' => $rs->fields['plugin_key']);
                    $rs->MoveNext();
                }
            }

            $xml = XML_serialize($data);
            $file = $code . '_content.xml';
            if ($type == 'untranslated') $file = 'untranslated_' . $file;
            $fp = fopen(_SRV_WEBROOT . 'export/' . $file, 'w');
            fputs($fp, $xml);
            fclose($fp);
        } else {
            return;
        }

    }

    function _getParams ()
    {
        global $language;

        $params = array();

        $header['language_code'] = array(
            'type' => 'dropdown', // you can modyfy the auto type
            'url' => 'DropdownData.php?get=language_codes'
        );
        $header['class'] = array(
            'type' => 'dropdown', // you can modyfy the auto type
            'url' => 'DropdownData.php?get=language_classes'
        );

        $header['language_key'] = array('required' => true);

        $params['include'] = array('language_content_id', 'language_key', 'class', 'translated' );

        if(!empty($this->url_data['edit_id']) || $this->url_data["new"] == 'true')
        {
            foreach ($language->_getLanguageList() as $lng)
            {
                $k = 'language_value_' . $lng['code'];
                $header[$k] = array('type' => 'textarea');
                $params['include'][] = $k;

                $k = 'readonly_' . $lng['code'];
                $header[$k] = array('type' => 'status');
                $params['include'][] = $k;
            }
        }
        $header['translated'] = array('type' => 'status');

        $header['language_content_id'] = array('type' => 'hidden', 'readonly' => true);

        $params['header'] = $header;
        $params['master_key'] = $this->_master_key;
        $params['default_sort'] = 'language_key';
        $params['languageTab'] = 1;
        $params['PageSize'] = 50;

        $params['RemoteSort']   = true;
        $params['default_sort']   = 'language_key';

        $params['exclude'] = array('');
        $params['display_searchPanel'] = true;

        return $params;
    }

    function _getSearchIDs ($search_data)
    {
        global $filter;

        $sql_tablecols = array('language_key',
            'language_value'
        );

        foreach ($sql_tablecols as $tablecol) {
            $sql_where[] = "(" . $tablecol . " LIKE '%" . $filter->_filter($search_data) . "%')";
        }

        if (is_array($sql_where)) {
            $sql_data_array = " (" . implode(' or ', $sql_where) . ")";
        }

        return $sql_data_array;
    }


    function _get ($ID = 0)
    {
        global $xtPlugin, $db, $language;
		$obj = new stdClass;
        if ($this->position != 'admin') return false;

        $where = '';

        if ($ID === 'new') {
            $obj = $this->_set(array(), 'new');
            $ID = $obj->new_id;
        }

        $ID = (int)$ID;

        if ($this->url_data['query']) {
            $sql_where = $this->_getSearchIDs($this->url_data['query']);
            $where .= $sql_where;
        }

        if (!$ID && !isset($this->sql_limit)) {
            $this->sql_limit = "0,25";
        }

        $sort = 'language_key';
        if(!empty($this->url_data["sort"]))
        {
            $sort = $this->url_data["sort"];
            if(!empty($this->url_data["dir"]))
                $sort .= ' '.$this->url_data["dir"];
        }

        $group_sort = ' GROUP BY language_key, class ORDER BY '.$sort.' ,language_key ';
        $table_data = new adminDB_DataRead($this->_table, $this->_table_lang, $this->_table_seo, $this->_master_key, $where, $this->sql_limit, '', 'true', $group_sort);

        if ($this->url_data['get_data'])
        {
            $data = $table_data->getData();
            if (!$this->url_data['query']) {
                $table_data->_total_count = $db->GetOne("select count(*) FROM (select COUNT(*) from ".$this->_table."  GROUP BY language_key, class) lc");
            }
            foreach ($data as &$v)
            {
                $translations = $db->GetArray('SELECT * FROM '.$this->_table." WHERE language_key = ? AND class = ?", [$v['language_key'], $v['class']]);
                $translations_merge = [];
                foreach($translations as $t)
                {
                    $translations_merge['language_value_'.$t['language_code']] = $t['language_value'];
                }
                $translated = true;
                foreach($language->_getLanguageList() as $lng)
                {
                    // nur für im shop aktivierte sprachen prüfen
                    // oder besser nur für die mit Admin-Eingabe ?
                    // aktivierte Sprache: da sieht man bei vorbereitung einer sprache nicht, dass etwas fehlt. bis man live aktiviert. kann aber über shop gesteuert werden
                    // Admin-Eingabe aktiviert: bedeutet sprache wird gepflegt bzw eingerichtet
                    // oder eben alle
                    // entscheidung heute > aktivierte Sprache

                    $translation = trim($translations_merge['language_value_' . $lng['code']]);

                    if(constant('LANGUAGE_CONTENT_TRANSLATED_FILTER') == 'allow_edit')
                    {
                        if($lng['allow_edit'] == 1)
                        {
                            if (empty($translation) || $translation == $v['language_key'])
                            {
                                $translated = false;
                                break;
                            }
                        }
                    }
                    else if(constant('LANGUAGE_CONTENT_TRANSLATED_FILTER') == 'language_status')
                    {
                        if($lng['language_status'] == 1)
                        {
                            if (empty($translation) || $translation == $v['language_key'])
                            {
                                $translated = false;
                                break;
                            }
                        }
                    }
                    else
                    {
                        if (empty($translation) || $translation == $v['language_key'])
                        {
                            $translated = false;
                            break;
                        }
                    }
                }
                $v['translated'] = $translated;
            }
        }
        elseif ($ID)
        {
            $sql = "SELECT language_key, class FROM ".$this->_table." WHERE ".$this->_master_key." =?";
            $data = $db->GetArray($sql, [$ID]);

            $sql = "SELECT * FROM ".$this->_table."  WHERE language_key = ? AND class = ?";
            $rows = $db->GetArray($sql, [$data[0]['language_key'], $data[0]['class']]);

            foreach($rows as $k => $row)
            {
                $data[0]['language_value_'.$row['language_code']] = $row['language_value'];
                $data[0]['readonly_'.$row['language_code']] = $row['readonly'];
            }

            foreach($language->_getLanguageList() as $lng)
            {
                if(!array_key_exists('language_value_'.$lng['code'], $data[0]))
                {
                    $data[0]['language_value_'.$lng['code']] = '';
                    $data[0]['readonly_'.$lng['code']] = 0;
                }
            }
        }
        elseif ($this->url_data["new"] == 'true')
        {
            $data[0] = [];

            foreach($language->_getLanguageList() as $lng)
            {
                $data[0]['language_key'] = $this->_new_key_name; // ist nun ein leer-string
                $data[0]['class'] = 'store';
                $data[0]['language_value_'.$lng['code']] = '';
                $data[0]['readonly_'.$lng['code']] = 0;
            }
        }
        else {
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

    function _set ($data, $set_type = 'edit')
    {
        global $db, $language, $filter;

        /**
         *
        $data Array
        (
            [language_key] => __text('TEXT_DESC')
            [class] => admin
            [language_value_de] => Absteigend
            [language_value_en] => Descending
            [language_value_fr] =>
            [language_content_id] => 20329
        )
         französisch nicht übersetzt
         zB spanisch wurde nicht gesendet, da nicht als backend sprache aktiviert per language->allow_edit
         wichtig. hier im bsp ist language_content_id aus dem spanischen, da der einzige eintrag in db ! huh
         also vorsicht bei verwendung

        $this->url_data Array
        (
            [load_section] => language_content
            [parentNode] => node_language_content_20329
            [sec] => ae2bdf2a9ed42971604c2e05eb419ba4
            [edit_id] => 20329
            [gridHandle] => language_contentgridForm
            [save] => true
            [language_key] => __text('TEXT_DESC')
            [class] => admin
            [language_value_de] => Absteigend
            [language_value_en] => Descending
            [language_value_fr] =>
        )

         *
         */

        //error_log('set_type => '.$set_type);
        //error_log(print_r($data, true));
        //error_log(print_r($this->url_data, true));
        //die();

        // das wurde gemacht weil bei new immer leere keys angelegt worden sind
        // brauchts eigentlich nicht mehr, da wir bei new einfach rausspringen
        // bleibt als cleanup,
        $db->Execute("DELETE FROM " . $this->_table . " WHERE language_key = '' OR language_key IS NULL ");

        $obj = new stdClass();
        $obj->success = true;

        if($this->url_data["new"] == 'true' && (!array_key_exists("save", $this->url_data) || $this->url_data["save"] != 'true'))
        {
            return $obj;
        }

        // bisherige klasse merken both/store/admin
        $old_class = $db->GetOne('SELECT class FROM '.$this->_table.' WHERE language_content_id = ?', [$data['language_content_id']]);

        // kann sein, dass jemand den beim neu anlegen generierten key in einen existierenden ändert
        // also von _new_key_name auf zb BUTTON_SAVE
        // wir erkennen das erstmals-speichern-nach-klick-auf-new neuer keys an url_data.new und url_data.save
        $key_exists = $db->GetOne('SELECT 1 FROM '.$this->_table." WHERE language_key=? and `class` = ? ", [$data["language_key"], $data["class"]] );
        if($this->url_data["new"] == 'true' && $this->url_data["save"] == 'true')
        {
            // standard _new_key_name wollen wir nicht; nuun leer-string, wird auch in oberfläche abgefangen
            $key_exists_new = $data["language_key"] == $this->_new_key_name;
            if($key_exists_new)
            {
                $obj->success = false;
                $obj->error = true;
                $obj->error_message = $data["language_key"].'<br><br>Bitte geben Sie einen eindeutige Kombination Schlüssel/Klasse an<br> Please provide a new unique key/class combination';
                return $obj;
            }
            // schon vorhandene keys sollen nicht einfach überschrieben werdne
            if($key_exists)
            {
                $obj->success = false;
                $obj->error = true;
                $obj->error_message = $data["language_key"].'<br><br>Kombination Schlüssel/Klasse existiert.<br><br>Sollten Sie den Schlüssel soeben neu angelegt und per übernehmen gespeichert haben, schließen Sie bitte den Tab und öffnen erneut<br> Key exits. Please provide a new unique key/class combination';
                return $obj;
            }
        }
        else if($key_exists && $old_class != $data['class'])  // es wird versucht auf eine andere klasse umzustellen
        {
            $obj->success = false;
            $obj->error = true;
            $obj->error_message = $data["language_key"].'<br><br>Kombination Schlüssel/Klasse existiert.<br><br>Sollten Sie den Schlüssel soeben neu angelegt und per übernehmen gespeichert haben, schließen Sie bitte den Tab und öffnen erneut<br> Key exits. Please provide a new unique key/class combination';
            return $obj;
        }

        // erstmal davon ausgehen, dass alles übersetzt ist; wird später geprüft
        $translated = true;
        foreach($language->_getLanguageList('store') as $lng)
        {
            // nur für im shop aktivierte sprachen prüfen
            // oder besser nur für die mit Admin-Eingabe ?
            // aktivierte Sprache: da sieht man bei vorbereitung einer sprache nicht, dass etwas fehlt. bis man live aktiviert. kann aber über shop gesteert werden
            // Admin-Eingabe aktiviert: bedeutet sprache wird gepflegt bzw eingerichtet
            // entscheidung heute > aktivierte Sprache
            if($lng['language_status'] == 1)
            {
                if (empty(trim($data['language_value_' . $lng['code']])))
                {
                    $translated = false;
                    break;
                }
            }
        }

        // erst einmal nur die texte selbst
        // translated und class später
        foreach($language->_getLanguageList() as $lng)
        {
            $lngCode = $lng['code'];

            // prüfen, ob im backend sichtbare sprache allow_edit
            // wenn nicht, skippen wir
            // ansonsten würden wir mit '' überschreiben, da im request nix gefunden wird
            // aber, wenn neu, muss es angelegt werden

            //if($this->url_data["new"] != 'true' && $lng['allow_edit'] != 1)
              //  continue;

            // würden wir nicht brauchen, wenn HIER bei den sprachtexten selbst ALLE angezeigt werden würden
            // siehe vorbereitung in xtFramework/admin/classes/class.ExtEditForm.php:101
            // warum zeigen wir an dieser Stelle nicht alle Sprachen
            // bei Artikeln etc macht das Sinn unvollständige auszublenden, aber hier geht es um die Sprachen selbst
            // so machen es wir jetzt auch: alle sprachen sind sichtbar

            // filter_class zut prüfung welche einträge ggf ein update brauchen
            $filter_class = $old_class != $data['class'] ? $old_class : $data['class'];
            $key_exists = $db->GetOne('SELECT 1 FROM '.$this->_table." WHERE language_key=? AND language_code=? and `class` = ?", [$data["language_key"], $lngCode, $filter_class] );
            if($key_exists)
            {
                $sql = "UPDATE " . $this->_table . " SET language_value=?, translated=?, readonly=?, class=?
                WHERE language_code=? AND language_key=? AND class=?";

                $params = [
                    // update
                    $data['language_value_'.$lngCode] ?: '',
                    $translated ? 1 : 0,
                    empty($data['readonly_'.$lngCode]) ? 0 : 1,
                    empty($data['class']) ? 'store' : $data['class'],
                    // where ['language_value_'.$lng['code']],
                    $lngCode,
                    $data['language_key'],
                    $filter_class
                ];
            }
            else {
                $sql = "INSERT INTO " . $this->_table . " (language_code, language_key, class, language_value, translated, readonly) VALUES(?,?,?,?,?,?) 
                ON DUPLICATE KEY UPDATE language_value=?, translated=?, readonly=?, class=?";

                $params = [
                    // insert
                    $lngCode,
                    $data['language_key'],
                    empty($data['class']) ? 'store' : $data['class'],
                    $data['language_value_'.$lngCode] ?: '',
                    $translated ? 1 : 0,
                    empty($data['readonly_'.$lngCode]) ? 0 : 1,
                    // update
                    $data['language_value_'.$lngCode] ?: '',
                    $translated ? 1 : 0,
                    empty($data['readonly_'.$lngCode]) ? 0 : 1,
                    $data['class']
                ];
            }

            $db->Execute($sql, $params);
            $obj->new_id = $obj->last_id =$db->Insert_ID();
        }

        unset($_SESSION['debug'][$data['language_key']]);

        xt_cache::deleteCache('language_content');

        return $obj;
    }

    function _unset ($id = 0)
    {
        global $db;

        if ($id == 0) return false;
        if ($this->position != 'admin') return false;
        $id = (int)$id;
        if (!is_int($id)) return false;

        $key = $db->GetOne('SELECT language_key FROM '.$this->_table.' WHERE language_content_id = ?', [$id]);
        if($key !== false)
        {
            $class = $db->GetOne('SELECT class FROM '.$this->_table.' WHERE language_content_id = ?', [$id]);
            $db->Execute("DELETE FROM " . $this->_table . " WHERE language_key = ? AND `class` = ?", array($key, $class));
        }
        else $db->Execute("DELETE FROM " . $this->_table . " WHERE " . $this->_master_key . " = ?", array($id));

        xt_cache::deleteCache('language_content');
    }
}