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

function _getSingleValue($data)
{
		global $db;

	$regex = '/[^a-zA-Z0-9_]/';
	$data['value'] = preg_replace($regex, '', $data['value']);
	$data['table'] = preg_replace($regex, '', $data['table']);
	$data['key']   = preg_replace($regex, '', $data['key']);

	$params = array($data['key_val']);
	if(!empty($data['key_where_params']) && is_array($data['key_where_params']))
	{
		$params = array_merge($params, $data['key_where_params']);
	}

		$qry = "select ". $data['value'] . " from ". $data['table'] ." where ".$data['key']." = ? ".$data['key_where']."";
	$record = $db->Execute($qry, $params);

		if($record->RecordCount() > 0){
			return $record->fields[$data['value']];
		}
}

/*Url data will be copied in order to be redirected later on*/
function saveDeletedUrl($link_id,$link_type,$store_id=''){
	global $db, $filter;
	$qry = "select master_key  from ". TABLE_SEO_URL_REDIRECT ." ORDER BY master_key desc";
	$master_key = 1;
	
	$record2 = $db->Execute("select DISTINCT master_key  from ". TABLE_SEO_URL_REDIRECT ." ORDER BY master_key desc LIMIT 0,1");
	if($record2->RecordCount() > 0){
		$master_key = $record2->fields['master_key'];
	}
	$master_key++;

	$params =  array($link_id, $link_type);

	$add_to_url = ''; 
	if ($store_id!='') {
		$add_to_url = " and store_id = ? ";
		$params[] = $store_id;
	}
	$qry = "select *  from ". TABLE_SEO_URL ." where link_id = ?  and link_type = ? ".$add_to_url;
	$record = $db->Execute($qry, $params);

	if($record->RecordCount() > 0){
		while (!$record->EOF) {
			$insert_data = $record->fields;
			$insert_data['is_deleted'] = 1;
			$insert_data['master_key'] = $master_key;
			$db->AutoExecute(TABLE_SEO_URL_REDIRECT,$insert_data,'INSERT');
			$record->MoveNext();
			
		}
	}
	
}

/*Url data will be copied in order to be redirected later on*/
function save404Url($currnt_page,$store_id=''){
	global $db,$store_handler,$language,$filter;
	// dont pass 'null' to ADODB in save404Url, ADODB is converting it to NULL  NHO-645-76886
	// bot? or are there urls generated like www.xyz.de/null
	if ($currnt_page=='' || $currnt_page=='null') return true;
	if ((strpos($currnt_page,'xtAdmin/') !== false) || (strpos($currnt_page,'xtCore/') !== false) || 
        (strpos($currnt_page,'xtFramework/') !== false) || (strpos($currnt_page,'plugins/') !== false) || 
        (strpos($currnt_page,'cronjob.php') !== false) ||
        empty($language->code)){
        return true;
    }

    $url_parts = parse_url($currnt_page);
	$currnt_page = $url_parts['path'];

	if (empty($currnt_page)) return true;

	$currnt_page_md5 = md5($currnt_page);
	$master_key = 1;
	$total_count = 0;
	if ($store_id==''){
		 $store_id = (int) $store_handler->shop_id;
	}

	$qry = "select * from ". TABLE_SEO_URL_REDIRECT ." WHERE is_deleted = 0 and url_md5 = ?
					and store_id = ? and language_code = ?";
	
	$lng = $language->code;
	if(_SYSTEM_SEO_URL_LANG_BASED == 'true')
    {
        $store_langs = $language->_getLanguageList('store');
        foreach($store_langs as $k => $store_lang)
        {
            if(preg_match('/'.$k.'\//', $currnt_page) == 1 || preg_match('/\/'.$k.'\//', $currnt_page) == 1)
            {
                $lng = $k;
                break;
            }
        }
    }
	$record2 = $db->Execute($qry, array($currnt_page_md5, $store_id, $lng));
							
	if($record2->RecordCount() > 0){
		$total_count = $record2->fields['total_count'];
		$count_day_last_access = $record2->fields['count_day_last_access'];
		
		$insert_data['url_text'] = $currnt_page;
		$insert_data['url_md5'] = $currnt_page_md5;
		$insert_data['total_count'] = $total_count+1;

		$date = DateTime::createFromFormat('Y-m-d H:i:s', $record2->fields['last_access']);
		if($date)
			$date = $date->format('Ymd');

		if(date('Gis')<=235959 && date('Ymd') == $date)
		{
			$insert_data['count_day_last_access'] = $count_day_last_access + 1;
		}
		else {
			$insert_data['count_day_last_access'] = 1;
		}

		$db->AutoExecute(TABLE_SEO_URL_REDIRECT,$insert_data,'UPDATE', "url_md5='".$currnt_page_md5."' and store_id = '".(int)$store_id."'
		and is_deleted = 0 and language_code = '".$lng."' ");
	}
	else{
		$record2 = $db->Execute("select DISTINCT master_key  from ". TABLE_SEO_URL_REDIRECT ." ORDER BY master_key desc LIMIT 0,1");
		if($record2->RecordCount() > 0){
			$master_key = $record2->fields['master_key'];
		}
		$master_key++;

		if (empty($store_id)) $store_id = $store_handler->shop_id;

        // insert data
        $insert_data[] = $currnt_page;      //'url_text'
        $insert_data[] = $currnt_page_md5;  //'url_md5'
        $insert_data[] = $master_key;       //'master_key'
        $insert_data[] = $lng;              //'language_code'
        $insert_data[] = 0;                 //'is_deleted'
        $insert_data[] = 1;                 //'total_count'
        $insert_data[] = 1;                 //'count_day_last_access'
        $insert_data[] = $store_id;         //'store_id'
        // update data
        $now = date('Y-m-d H:i:s');
        $insert_data[] = $now;

        $sql = "INSERT INTO " . TABLE_SEO_URL_REDIRECT . " (url_text, url_md5, master_key, language_code, is_deleted, total_count, count_day_last_access, store_id) VALUES(?,?,?,?,?,?,?,?) 
                ON DUPLICATE KEY UPDATE total_count=total_count+1, count_day_last_access=count_day_last_access+1, last_access=?";
        $db->Execute($sql, $insert_data);
	}	
}
