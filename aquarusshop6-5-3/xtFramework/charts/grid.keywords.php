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



class grid_keywords {


	function getKeywordsStat()
	{
		return $this->_getKeywordsStat();
	}
	
	function getKeywordsNoResultStat()
	{
		return $this->_getKeywordsStat('result_count = 0', "request_count");
	}
	
	private function _getKeywordsStat($where_clause = '', $orderBy = "last_date") {

		global $store_handler, $filter;

        $stores = $store_handler->getStores();

        $shopname = array();
        foreach ($stores as $sdata) {
            $shopname[$sdata['id']] = $sdata['text'];
        }

        $data = array();

        $keywords = $this->_getKeywords($where_clause, $orderBy);

        $i = 0;
        foreach ($keywords as $tdata){
            $i++;
            $keywords = $filter->_filterXSS($tdata['keyword']);
            if(empty($keywords)) continue;
            $data[] = array(
                $i,
                $shopname[$tdata['shop_id']],
                $keywords,
                $tdata['result_count'],
                $tdata['request_count'],
                $tdata['last_date']
            );
		}

        return $data;

    }

    private function _getKeywords($where_clause = '', $orderBy = "last_date"){
        global $db,$store_handler;

         $rs = $db->Execute("SELECT shop_id, keyword, result_count, request_count, last_date
                             FROM " . TABLE_SEARCH . " 
                             WHERE 1 " . (!empty($where_clause) ? " AND keyword > '' AND " . $where_clause : "") . "
                             ORDER BY $orderBy DESC LIMIT 0, 100");

        $data = array();
		while (!$rs->EOF) {
			$data[] = array(
                "shop_id"   =>  $rs->fields['shop_id'],
                "keyword"     =>  $rs->fields['keyword'],
                "result_count"    =>  $rs->fields['result_count'],
                "request_count"    =>  $rs->fields['request_count'],
                "last_date" =>  $rs->fields['last_date']
             );

			$rs->MoveNext();
		}
		$rs->Close();

        return $data;
    }

}
?>