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

#[AllowDynamicProperties]
class language extends language_content{

	public $default_language = _STORE_LANGUAGE;

	protected $_table = TABLE_LANGUAGES;
	protected $_table_lang = null;
	protected $_table_seo = null;
	protected $_master_key = 'languages_id';

	public $content_language;
    /**
     * @var array|array[]
     */

    protected $setlocale;

    function __construct($code = ''){
		global $db, $xtPlugin;

        parent::__construct();

		($plugin_code = $xtPlugin->PluginCode('class.language.php:_language_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		$this->getPermission();
		$this->_getLanguage($code);

	}

	function getPermission(){
		global $store_handler, $customers_status, $xtPlugin;

		$this->perm_array = array(
			'shop_perm' => array(
				'type'=>'shop',
				'key'=>$this->_master_key,
				'value_type'=>'language',
				'pref'=>'l'
			)
		);
		($plugin_code = $xtPlugin->PluginCode(__CLASS__.':getPermission')) ? eval($plugin_code) : false;
		$this->permission = new item_permission($this->perm_array);
		return $this->perm_array;
	}

	function _getLanguage($code = ''){
		global $db, $xtPlugin,$filter;

		($plugin_code = $xtPlugin->PluginCode('class.language.php:_getLanguage_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		if ($code=='') {
			if(!empty($_SESSION['selected_language'])){
				$code = $_SESSION['selected_language'];
			}elseif(!empty($_SESSION['customer']->customer_info['customers_default_language'])){
				$code = $_SESSION['customer']->customer_info['customers_default_language'];
			}else{
				$code = $this->default_language;
			}
		}

		if(!$this->_checkStore($code, USER_POSITION)){
			$code = $this->default_language;
		}
        $code = $filter->_filter($code,'lng');
		$data = $this->_buildData($code);
        $data['environment_language']= $code;
        if ($data['content_language']!=$code) {
           $data['code']= $data['content_language'];
        }

		($plugin_code = $xtPlugin->PluginCode('class.language.php:_getLanguage_bottom')) ? eval($plugin_code) : false;
		foreach ($data as $key => $value) {
			$this->$key = $value;
		}
	}


	/**
	 * set locale value for language
	 *
	 */
	function _setLocale() {
		if ($this->setlocale!='') {
			$locale_array = explode(';',$this->setlocale);
			if (is_array($locale_array)) {
				@setlocale(LC_TIME,$locale_array);
			}
		}

	}

	function _buildData($lang){
		global $db, $xtPlugin, $store_handler;

		($plugin_code = $xtPlugin->PluginCode('class.language.php:_buildData_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		$record = $db->CacheExecute("SELECT * FROM " . TABLE_LANGUAGES . " WHERE code = ?", array($lang));

		if($record->RecordCount() > 0){
			while(!$record->EOF){
				$data = $record->fields;
				$record->MoveNext();
			}$record->Close();
			($plugin_code = $xtPlugin->PluginCode('class.language.php:_buildData_bottom')) ? eval($plugin_code) : false;
			return $data;
		}else{
			return false;
		}
	}

    function getLanguageSwitchLinks($languages = array(), $store_hreflang_def = '') {
        global $db,$page,$xtLink, $xtPlugin, $store_handler;

        $xt_langs = $this->_getLanguageList('store');

        $links = array();
        switch($page->page_name) {

            case 'content';
                    global $current_content_id,$shop_content_data;
                    $query = "SELECT * FROM ".TABLE_SEO_URL." WHERE link_type='3' and link_id=? and store_id=?";
                    $rs = $db->Execute($query, array((int)$current_content_id, $store_handler->shop_id));
                    while (!$rs->EOF) {
                        if(!array_key_exists($rs->fields['language_code'], $xt_langs))
                        {
                            $rs->MoveNext();
                            continue;
                        }
						$link_array = array(
							'page'=>'content',
							'type'=>'content',
							'name'=>$shop_content_data['content_title'],
							'id'=>$shop_content_data['content_id'],
							'seo_url' => $rs->fields['url_text']
						);
						$url = $xtLink->_link($link_array);
						$links[$rs->fields['language_code']]=$url;
						$rs->MoveNext();
                    }

                    break;
            case 'product':
            case 'reviews':
                global $current_product_id,$p_info;

                $query = "SELECT * FROM ".TABLE_SEO_URL." WHERE link_type='1' and link_id=? and store_id=?";

                $rs = $db->Execute($query, array($p_info->pID, $store_handler->shop_id));
                while (!$rs->EOF) {
                    if(!array_key_exists($rs->fields['language_code'], $xt_langs))
                    {
                        $rs->MoveNext();
                        continue;
                    }
                    $link_array = array(
						'page'=>'product',
						'type'=>'product',
						'name'=>$p_info->data['products_name'],
						'id'=>$p_info->pID,
						'seo_url' => $rs->fields['url_text']
					);
                    $url = $xtLink->_link($link_array);
                    $links[$rs->fields['language_code']]=$url;
                    $rs->MoveNext();
                }
                break;
           case 'categorie':
                global $category, $current_category_id;

                $query = "SELECT * FROM ".TABLE_SEO_URL." WHERE link_type='2' and link_id=? and store_id=?";

                $rs = $db->Execute($query, array($current_category_id, $store_handler->shop_id));
                while (!$rs->EOF) {
                    if(!array_key_exists($rs->fields['language_code'], $xt_langs))
                    {
                        $rs->MoveNext();
                        continue;
                    }
                    $link_array = array(
						'page'=>'categorie',
						'type'=>'categorie',
						'name'=>$category->data['categories_name'],
						'id'=>$current_category_id,
						'seo_url' => $rs->fields['url_text']
					);
                    $url = $xtLink->_link($link_array);

                    $links[$rs->fields['language_code']]=$url;
                    $rs->MoveNext();
                }
                break;
            case 'manufacturers':
                global $manufacturer, $current_manufacturer_id;
                $man = array('manufacturers_id' => $current_manufacturer_id);
                $man_data = $manufacturer->buildData($man);
                $query = "SELECT * FROM ".TABLE_SEO_URL." WHERE link_type='4' and link_id=? and store_id=?";
                $rs = $db->Execute($query, array($current_manufacturer_id, $store_handler->shop_id));
                while (!$rs->EOF) {
                    if(!array_key_exists($rs->fields['language_code'], $xt_langs))
                    {
                        $rs->MoveNext();
                        continue;
                    }
                    $link_array = array(
						'page'=>'manufacturers',
						'type'=>'manufacturers',
						'name'=>$man_data['manufacturers_name'],
						'id'=>$current_manufacturer_id,
						'seo_url' => $rs->fields['url_text']
					);
                    $url = $xtLink->_link($link_array);
                    $links[$rs->fields['language_code']]=$url;
                    $rs->MoveNext();
                }
                break;
            case 'index':


                /**
                 *
                 skype april 23
                 ums 100% richtig zu haben müsste man quasi in allen sprachen das gleiche haben, also

                auf EN Seite:

                <link rel="alternate" hreflang="de" href="https://www.xtc-shop.de/" />
                <link rel="alternate" hreflang="en" href="https://www.xtc-shop.de/en/index" />
                <link rel="alternate" hreflang="x-default" href="https://www.xtc-shop.de/" />

                auf DE Seite

                <link rel="alternate" hreflang="de" href="https://www.xtc-shop.de/" />
                <link rel="alternate" hreflang="en" href="https://www.xtc-shop.de/en/index" />
                <link rel="alternate" hreflang="x-default" href="https://www.xtc-shop.de/" />

                 *
                 */
                if(!is_array($languages) || count($languages)==0)
                {
                    $languages = array();
                    foreach($xt_langs as $l)
                    {
                        $languages[] = $l['code'];
                    }
                }
                foreach ($languages as $l)
                {
                    if($l == $store_hreflang_def)
                    {
                        $url = $xtLink->_index(['seo_url' => $l.'/index']);
                    }
                    else {
                        $link_array = array(
                            'page' => 'index',
                            'lang_code' => $l,
                            'keep_lang' => true,
                            'seo_url' => _SYSTEM_SEO_URL_LANG_BASED == 'true' ? $l.'/index' : ''
                        );
                        $url = $xtLink->_link($link_array);
                    }
                    if (_SYSTEM_SEO_URL_LANG_BASED != 'true' && $store_hreflang_def != $l)
                    {
                        $url .= '?language='.$l;
                    }
                    $links[$l]=$url;
                }
                break;
            default:

                if(!is_array($languages) || count($languages)==0)
                {
                    $languages = array();
                    foreach($xt_langs as $l)
                    {
                        $languages[] = $l['code'];
                    }
                }
                foreach ($languages as $l)
                {
                    $link_array = array(
                        'page' => $page->page_name,
                        'paction' => $page->page_action,
                        'lang_code' => $l,
                        'params' => 'language='.$l
                    );
                    $url = $xtLink->_link($link_array);

                    $links[$l]=$url;
                }
                ($plugin_code = $xtPlugin->PluginCode('class.language.php:getLanguageSwitchLinks_switch_default')) ? eval($plugin_code) : false;
        		if(isset($plugin_return_value))
            	return $plugin_return_value;
        }

                ($plugin_code = $xtPlugin->PluginCode('class.language.php:getLanguageSwitchLinks_bottom')) ? eval($plugin_code) : false;
                if(isset($plugin_return_value))
                return $plugin_return_value;

        return $links;
    }

	function _getLanguageList($list_type = '',$index='code'){
		global $db, $xtPlugin, $store_handler;

		static $cache = array();


		($plugin_code = $xtPlugin->PluginCode('class.language.php:_getLanguagelist_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

        $where = $table = "";
		if($list_type=='store' || USER_POSITION=='store'){
			$table = $this->permission->_table;
			$where = $this->permission->_where;
		}

		$qry_where = " where l.languages_id > 0 ".$where."";

       	if ($list_type=='store')
        	$qry_where .= " and l.language_status = '1'";

		($plugin_code = $xtPlugin->PluginCode('class.language.php:_getLanguagelist_qry')) ? eval($plugin_code) : false;

		$hash = $list_type.$index.$qry_where; //crc32($list_type.$qry_where);

		if(!array_key_exists($hash, $cache))
        {
            $data = array();
            $record = $db->CacheExecute("SELECT * FROM " . TABLE_LANGUAGES . " l " . $table . " " . $qry_where . " ORDER BY sort_order");
            while (!$record->EOF)
            {
                $record->fields['id'] = $record->fields['code'];
                $record->fields['text'] = $record->fields['name'];
                $record->fields['icon'] = $record->fields['image'];
                $record->fields['edit'] = $record->fields['allow_edit'];

                if ($index == '')
                {
                    $data[] = $record->fields;
                }
                if ($index == 'code')
                {
                    $data[$record->fields['code']] = $record->fields;
                }
                $record->MoveNext();
            }
            $record->Close();

            $cache[$hash] = $data;
        }
        $data = $cache[$hash]; // to be compatible for old code using the next hook

		($plugin_code = $xtPlugin->PluginCode('class.language.php:_getLanguagelist_bottom')) ? eval($plugin_code) : false;

		return $data;
	}


	function _checkStore($code, $list_type='store'){
		global $xtPlugin, $db, $filter;

		($plugin_code = $xtPlugin->PluginCode('class.language.php:_checkStore_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

        $where = $table = "";
		if($list_type=='store'){
			$table = $this->permission->_table;
			$where = $this->permission->_where;
		}

        if(!$this->_checkLanguageCode($code)){
           return false;
        }

        $record = $db->CacheExecute(
			"SELECT code,language_status FROM " . TABLE_LANGUAGES . " l ".$table." where code = ? ".$where,
			array($code)
		);
        if($record->RecordCount() > 0){
            if (USER_POSITION == 'store' && $record->fields['language_status']=='0') return false;
            return true;
        }else{
            return false;
        }
	}

    public function _checkLanguageCode($code){
        global $db;
        $lang_arr = $this->_getLanguageList(USER_POSITION);
        $iscode = false;
		if(is_array($lang_arr))
		{
			foreach ($lang_arr as $k => $v)
			{
				if ($v['code'] == $code)
				{
                $iscode = true;
            }
        }
		}
        return $iscode;
    }

	function _getParams() {
		global $xtPlugin;

		($plugin_code = $xtPlugin->PluginCode('class.language.php:_getParams_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		$params = array();

		$header['languages_id'] = array('type' => 'hidden');

		$header['image'] = array('type' => '');
		$header['code'] = array('max' => '2','min'=>'2');
		$header['default_currency'] = array(
			'type' => 'dropdown',
			'url'  => 'DropdownData.php?get=currencies','text'=>__text('TEXT_CURRENCY_SELECT')
		);

        $header['content_language'] = array(
			'type' => 'dropdown',
			'url'  => 'DropdownData.php?get=language_codes&skip_empty=true'
		);
        $header['allow_edit'] = array('type' => 'status');
		$params['header']         = $header;
		$params['master_key']     = $this->_master_key;
		$params['default_sort']   = 'sort_order';
		$params['languageTab']    = false;

		$params['display_checkCol']  = true;
		$params['display_adminActionStatus'] = false;
        $params['display_statusTrueBtn']  = true;
        $params['display_statusFalseBtn']  = true;

		$params['display_newBtn'] = false;

		if($this->url_data['pg']=='overview' && !$this->url_data['edit_id'] && $this->url_data['new'] != true){
			$params['include'] = array ('languages_id','language_status', 'code', 'name', 'sort_order', 'language_charset', 'default_currency','language_content');
		}

		$rowActions[] = array('iconCls' => 'export_language', 'qtipIndex' => 'qtip1', 'tooltip' => 'Export');
		if ($this->url_data['edit_id'])
		$js = "var edit_id = ".$this->url_data['edit_id'].";";
		else
		$js = "var edit_id = record.id";
		$js.= "
		alert('export_language_yml');
		         var conn = new Ext.data.Connection();
                 conn.request({
                 url: 'row_actions.php',
                 method:'GET',
                 params: {'language_id': edit_id,'type': 'export_language_yml'},
                 success: function(responseObject) {

                           var response = Ext.decode(responseObject.responseText);
                            var msg = '".__text('TEXT_SUCCESS')."';
                            if(typeof(response.msg) != 'undefined')
                            {
                                msg = response.msg;
                            }
                           Ext.MessageBox.alert('Message', msg);
                          },
                 });";
		$rowActionsFunctions['export_language'] = $js;

        $rowActions[] = array('iconCls' => 'export_nottranslated', 'qtipIndex' => 'qtip1', 'tooltip' => __text('TEXT_EXPORT_NOTTRANSLATED'));
        if ($this->url_data['edit_id'])
        $js = "var edit_id = ".$this->url_data['edit_id'].";";
        else
        $js = "var edit_id = record.id";
        $js.= "
                 var conn = new Ext.data.Connection();
                 conn.request({
                 url: 'row_actions.php',
                 method:'GET',
                 params: {'language_id': edit_id,'type': 'export_nottranslated'},
                 success: function(responseObject) {

                            var response = Ext.decode(responseObject.responseText);
                            var msg = '".__text('TEXT_SUCCESS')."';
                            if(typeof(response.msg) != 'undefined')
                            {
                                msg = response.msg;
                            }
                           Ext.MessageBox.alert('Message', msg);
                          },
                 });";
        $rowActionsFunctions['export_nottranslated'] = $js;

        $rowActions[] = array('iconCls' => 'export_lang_class_problems', 'qtipIndex' => 'qtip1', 'tooltip' => __text('TEXT_EXPORT_LANG_CLASS_PROBLEMS'));
        if ($this->url_data['edit_id'])
            $js = "var edit_id = ".$this->url_data['edit_id'].";";
        else
            $js = "var edit_id = record.id";
        $js.= "
                 var a = Ext.Msg.show({
                        title: 'Export',
                        msg: 'Das dauert einen Moment. Ok um zu starten.',
                        buttons: Ext.Msg.OKCANCEL,
                        fn: function (btn, text) {
                            console.log(btn);
                            if (btn == 'ok') {
                                var conn = new Ext.data.Connection();
                                conn.request({
                                    url: 'row_actions.php',
                                    method: 'GET',
                                    params: {'language_id': edit_id, 'type': 'export_lang_class_problems'},
                                    success: function (responseObject) 
                                    {
                                        var response = Ext.decode(responseObject.responseText);
                                        var msg = '".__text('TEXT_SUCCESS')."';
                                        if (typeof (response.msg) != 'undefined') {
                                            msg = response.msg;
                                        }
                                        Ext.MessageBox.alert('Message', msg);
                                    },
                                });
                            }
                        }
                    }
                );
                 ";
        $rowActionsFunctions['export_lang_class_problems'] = $js;

		($plugin_code = $xtPlugin->PluginCode('class.language.php:_getParams_row_actions')) ? eval($plugin_code) : false;

    $params['rowActions'] = $rowActions;
    $params['rowActionsFunctions'] = $rowActionsFunctions;

    $js = "addTab('adminHandler.php?load_section=language_sync&new=true','".__text('TEXT_DOWNLOAD_TRANSLATIONS')."');";
    $UserButtons['download_translations'] = array('text' => 'TEXT_DOWNLOAD_TRANSLATIONS', 'style' => 'download_translations', 'icon' => 'door_out.png', 'acl' => '', 'stm' => $js);
		$js = "addTab('adminHandler.php?load_section=language_import&new=true','".__text('TEXT_LANGUAGE_IMPORT')."');";
		$UserButtons['options_add'] = array('text'=>'TEXT_LANGUAGE_IMPORT', 'style'=>'options_add', 'icon'=>'add.png', 'acl'=>'edit', 'stm' => $js);

		$params['display_options_addBtn'] = true;
		$params['display_download_translationsBtn'] = true;
		$params['UserButtons']      = $UserButtons;


		return $params;
	}

	function _get($ID = 0) {
		global $xtPlugin, $db, $language;
		$obj = new stdClass;
		if ($this->position != 'admin') return false;

		if ($ID === 'new') {
			$obj = $this->_set(array(), 'new');
			$ID = $obj->new_id;
		}

		$table_data = new adminDB_DataRead($this->_table, $this->_table_lang, $this->_table_seo, $this->_master_key, '', '', $this->perm_array);

		if ($this->url_data['get_data']){
			$data = $table_data->getData();
            $total = $db->GetOne("select count(*) FROM (select COUNT(*) from ".TABLE_LANGUAGE_CONTENT." WHERE class!='wizard' GROUP BY language_key, class ) lc");
            foreach ($data as $key => $val) {


                $lng_keys = $db->GetOne(
					"SELECT count(*) FROM (select language_key, class from ".TABLE_LANGUAGE_CONTENT."  WHERE language_code = ? and class!='wizard'  GROUP BY language_key, class) lc",
					array($val['code'])
				);
                $missing_keys_count = $total - $lng_keys;

                $empty_value_count = $db->GetOne(
                    "SELECT count(*) FROM (select language_key, class from ".TABLE_LANGUAGE_CONTENT."  WHERE language_code = ? and (language_value='' OR language_value = language_key) ) lc",
                    array($val['code'])
                );


                $data[$key]['language_content']= "<span style='cursor: pointer;' qtip='{$missing_keys_count} missing keys<br>{$empty_value_count} empty translations<br>{$total} total'>".$missing_keys_count.' / '.$empty_value_count.' / '.$total.'</span>';
            }
		}elseif($ID){
			$data = $table_data->getData($ID);
			$data[0]['shop_permission_info']=_getPermissionInfo();
		}else{
			$data = $table_data->getHeader();
		}

		if($table_data->_total_count!=0 || !$table_data->_total_count)
			$count_data = $table_data->_total_count;
		else
			$count_data = count($data);

		$obj->totalCount = $count_data;
		$obj->data = $data;

		return $obj;
	}

	function _set($data, $set_type = 'edit') {
		global $db,$language,$filter;

		$obj = new stdClass;
		$o = new adminDB_DataSave($this->_table, $data, false, __CLASS__);
		$obj = $o->saveDataSet();

		if (($data['language_status']==1) && ($data['allow_edit']==1))
		{
			$this->addLanguageToConfiguration($data);
		}

		$set_perm = new item_permission($this->perm_array);
		$set_perm->_saveData($data, $data[$this->_master_key]);

		return $obj;
	}

	function addLanguageToConfiguration($data)
	{
		global $db, $language;
		$record = $db->Execute("SELECT * FROM " . TABLE_MANDANT_CONFIG );

		if($record->RecordCount() > 0) {
			while(!$record->EOF){
				if (!$data['shop_'.$record->fields['shop_id']]) {
					$record2 = $db->Execute("SELECT * FROM " . TABLE_CONFIGURATION_MULTI.$record->fields['shop_id']." WHERE config_key='_store_email_footer_txt_".$data['code']."' " );
                        if($record2->RecordCount() == 0)
                        {
						$db->Execute("INSERT INTO " . TABLE_CONFIGURATION_MULTI.$record->fields['shop_id']. " (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_store_email_footer_txt_".$data['code']."', 'DemoShop GmbH\nGeschäftsführer: Max Muster und Fritz Beispiel\n\nMax Muster Strasse 21-23\nD-0815 Musterhausen\nE-Mail: max.muster@muster.de\n\nHRB 123456\nAmtsgericht Musterhausen\nUStid-Nr. DE 000 111 222', 12, 8, 'textarea', '')");

                            $db->Execute("INSERT INTO " . TABLE_CONFIGURATION_MULTI.$record->fields['shop_id']. " (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_store_email_footer_html_".$data['code']."', 'DemoShop GmbH\nGeschäftsführer: Max Muster und Fritz Beispiel\n\nMax Muster Strasse 21-23\nD-0815 Musterhausen\nE-Mail: max.muster@muster.de\n\nHRB 123456\nAmtsgericht Musterhausen\nUStid-Nr. DE 000 111 222', 12, 8, 'textarea', '')");
                        }
                        $record2->Close();

				}

                $copy_keys = array('_STORE_NAME','_STORE_STORE_CLAIM', '_STORE_META_PUBLISHER', '_STORE_META_COMPANY', '_STORE_META_DESCRIPTION', '_STORE_META_KEYWORDS', '_STORE_META_FREE_META');
                foreach($copy_keys as $k)
                {
                    $key = $k;

                    $default_values = array('language_value' => '', 'group_id' => 1, 'sort_order' => 0);
                    $default_values_db = $db->GetArray("SELECT * FROM " . TABLE_CONFIGURATION_LANG_MULTI . " WHERE config_key=? AND language_code=? AND store_id=?", array($key, $language->default_language,$record->fields['shop_id']));
                    $default_values_db = $default_values_db[0];
                    if(!empty($default_values_db)){
                        $default_values = array_merge($default_values, $default_values_db);
                    }

                    $db->Execute("INSERT INTO " . TABLE_CONFIGURATION_LANG_MULTI . "
                    (`language_code`,
                    `config_key`,
                    `language_value`,
                    `store_id`,
                    `group_id`,
                    `sort_order`
                    )
                    VALUES
                    (?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE `language_value` = `language_value`;
                    ;", array($data['code'], $key, $default_values['language_value'], $record->fields['shop_id'], $default_values['group_id'], $default_values['sort_order']));
                }

				$record->MoveNext();
			}$record->Close();
		}

	}

    function _setStatus($id, $status) {
        global $db,$xtPlugin;

        $id = (int)$id;
        if (!is_int($id)) return false;

        $db->Execute(
			"update " . TABLE_LANGUAGES . " set language_status = ? where languages_id = ?",
			array($status, $id)
		);

    }

	function _seoCheck($lng_id) {
		global $xtLink;

		if (isset($_GET['limit_lower'])) {
			$this->limit_lower = (int)$_GET['limit_lower'];
		}

		if (isset($_GET['limit_upper'])) {
			$this->limit_upper = (int)$_GET['limit_upper'];
		}

		if (isset($_GET['counter'])) {
			$this->counter = (int)$_GET['counter'];
		}

		$this->counter+=10;

		if ($this->counter>50) {
			echo $this->_htmlHeader();
			echo '- export finished -<br />';
			echo '- exported datasets '.$this->count.'<br />';
			echo $this->_htmlFooter();
		} else {

			$params = 'type=api_seo_url_check&id='.$lng_id.'&sess_name='.session_name().'&sess_id='.session_id().
				'&limit_lower='.$this->limit_lower.
				'&limit_upper='.$this->limit_upper.
				'&counter='.$this->counter;
			echo $this->_displayHTML($xtLink->_adminlink(array('default_page'=>'xtAdmin/row_actions.php', 'params'=>$params)),$limit_lower,$limit_upper,$this->count);
		}
	}

	function _displayHTML($next_target,$lower=1,$upper=0,$total=0) {

		$process = $lower / $total * 100;
		if ($process>100) $process=100;

		$html='<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="refresh" content="5; URL='.$next_target.'" />
<title>..import / export..</title>
<style type="text/css">
<!--
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

-->
</style>
</head>
<body>
<div class="process_rating_light"><div class="process_rating_dark" style="width:'.$process.'%">'.round($process,0).'%</div></div>
Processing '.$lower.' to '.$upper.' of total '.$total.'
</body>
</html>';
		return $html;

	}


	function _htmlHeader() {
		$html='<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>..import / export..</title>
<style type="text/css">
<!--
ul.stack {padding:5px}
ul.stack li {}
ul.stack li.success {list-style:none; padding:5px 0px 2px 20px; background-image:url(xtAdmin/images/icons/accept.png); background-repeat:no-repeat; background-position:0px 4px;}
ul.stack li.error {list-style:none; padding:5px 0px 2px 20px; background-image:url(xtAdmin/images/icons/cross.png); background-repeat:no-repeat; background-position:0px 4px;}
-->
</style>
</head>
<body>';
		return $html;
	}

	function _htmlFooter() {
		$html ='</body></html>';
		return $html;
	}

	function _addLanguage() {
		global $db,$xtPlugin,$language;
	}

	function _unset($id = 0) {
		global $db,$xtPlugin, $store_handler;
		if ($id == 0) return false;
		$id = (int)$id;
		if (!is_int($id)) return false;
		$new_id = '';

		$query = "SELECT code FROM " . TABLE_LANGUAGES . " where languages_id =?";

		$record = $db->Execute($query, array($id));
		if($record->RecordCount() > 0){
			$current_code = $record->fields['code'];
		}else{
			return false;
		}

        // dont remove last language
        $query = "SELECT code FROM " . TABLE_LANGUAGES;
        $record = $db->Execute($query);
        if ($record->RecordCount()==1) return false;


		$set_perm = new item_permission($this->perm_array);
		$set_perm->_deleteData($id);

		$db->Execute("DELETE FROM ". TABLE_LANGUAGES ." WHERE code = ?", array($current_code));
		$db->Execute("DELETE FROM ". TABLE_LANGUAGE_CONTENT ." WHERE language_code = ?", array($current_code));

		$db->Execute("DELETE FROM ". TABLE_COUNTRIES_DESCRIPTION ." WHERE language_code = ?", array($current_code));
		$db->Execute("DELETE FROM ". TABLE_MAIL_TEMPLATES_CONTENT ." WHERE language_code = ?", array($current_code));
		$db->Execute("DELETE FROM ". TABLE_MANUFACTURERS_DESCRIPTION ." WHERE language_code = ?", array($current_code));
		$db->Execute("DELETE FROM ". TABLE_MEDIA_DESCRIPTION ." WHERE language_code = ?", array($current_code));

		$db->Execute("DELETE FROM ". TABLE_PAYMENT_DESCRIPTION ." WHERE language_code = ?", array($current_code));
		$db->Execute("DELETE FROM ". TABLE_SHIPPING_DESCRIPTION ." WHERE language_code = ?", array($current_code));

		$db->Execute("DELETE FROM ". TABLE_PRODUCTS_DESCRIPTION ." WHERE language_code = ?", array($current_code));
		$db->Execute("DELETE FROM ". TABLE_CATEGORIES_DESCRIPTION ." WHERE language_code = ?", array($current_code));
		$db->Execute("DELETE FROM ". TABLE_SEO_URL ." WHERE language_code = ?", array($current_code));

        $stores = $store_handler->getStores();
        foreach($stores as $store)
        {
            $db->Execute("DELETE FROM ". TABLE_CONFIGURATION_MULTI.$store['id'] ." WHERE config_key IN (?,?)", array('_store_email_footer_txt_'.$current_code, '_store_email_footer_html_'.$current_code));
        }
        $db->Execute("DELETE FROM ". TABLE_CONFIGURATION_LANG_MULTI ." WHERE language_code = ?", array($current_code));

		// update default values
		$query = "SELECT code FROM " . TABLE_LANGUAGES . " LIMIT 1";
		$rs = $db->Execute($query);
		$new_code=$rs->fields['code'];
		$db->Execute(
			"UPDATE ".TABLE_CUSTOMERS." SET customers_default_language=? WHERE customers_default_language=?",
			array($new_code, $current_code)
		);

		($plugin_code = $xtPlugin->PluginCode('class.language.php:_delete_bottom')) ? eval($plugin_code) : false;
	}
}
