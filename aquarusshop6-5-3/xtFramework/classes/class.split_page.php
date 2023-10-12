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

class split_page{
	var $split_data;

	/**
	 * return split page result
	 *
	 * @param string $sql sql query
	 * @param int $rows_per_page
	 * @param string $param
	 * @param int $cache_secs
	 * @return split_page
	 */
	function __construct($sql, $rows_per_page, $param='',$cache_secs = 0, $seo_link='true', $inputarr = false){
		global $db, $xtPlugin;

		$pager = new xtcommerce_Pager($db, $sql, '', $param, 'true', $seo_link);
		if ($cache_secs>0) {
			$pager->cache = $cache_secs;
		}

        ($plugin_code = $xtPlugin->PluginCode('class.split_page.php:_before_build')) ? eval($plugin_code) : false;

        $data = $pager->getData($rows_per_page, $inputarr);
        $this->split_data = array('actual_page' => $data['count']['actual_page'], 
                                  'last_page' => $data['count']['last_page'], 
		                          'data' => $data['data'],
								  'count' => $this->formatPagesCount($data['count']),
								  'pages' => $this->formatPages($data['pages'],$data['count']),
								  'total' => $data['data_count']
								 );
	}

	function formatPagesCount($data){
		global $xtPlugin, $db;

			$template = new Template();
			$tpl_data = $data;
            $tpl_data['page_name'] = $this->getPageName(); 
			$countData = $template->getTemplate('nav_count_smarty', '/'._SRV_WEB_CORE.'pages/navigation/nav_count.html',$tpl_data);

		return $countData;
	}
    
    function getPageName(){
        global $db, $language, $current_category_id;
        $page_name='';

        if (!empty($current_category_id)) {
            $record = $db->Execute("select categories_name from " . TABLE_CATEGORIES_DESCRIPTION . " where language_code = ? AND categories_id = ?", [$language->code,(int) $current_category_id]);
            if($record->RecordCount() > 0){
                return $record->fields['categories_name'];
            } else {
                return '';
            }
        }
        
        return $page_name;
    }
    
	function formatPages($data,$count){
		global $xtPlugin, $db;

			$template = new Template();
			$tpl_data = array_merge($data,$count);
			$pagesData = $template->getTemplate('nav_pages_smarty', '/'._SRV_WEB_CORE.'pages/navigation/nav_pages.html',$tpl_data);

		return $pagesData;
	}

}

?>