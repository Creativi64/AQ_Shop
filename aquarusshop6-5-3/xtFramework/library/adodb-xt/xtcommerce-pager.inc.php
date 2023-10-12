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

class xtcommerce_Pager extends ADODB_Pager {
	
	/**
	 *
	 * @param unknown $db        	
	 * @param unknown $sql        	
	 * @param string $id        	
	 * @param string $param        	
	 * @param string $showPageLinks        	
	 * @param string $seo_link        	
	 */
	function __construct(&$db, $sql, $id = 'adodb', $param = '', $showPageLinks = false, $seo_link = 'true') {
		global $PHP_SELF;
		if ($id != '')
			$curr_page = $id . '_curr_page';
		else
			$curr_page = $id . 'curr_page';
		if (! empty ( $PHP_SELF ))
			$PHP_SELF = htmlspecialchars ( $_SERVER ['PHP_SELF'] ); // htmlspecialchars() to prevent XSS attacks
		
		$this->sql = $sql;
		$this->id = $id;
		$this->db = $db;
		$this->showPageLinks = $showPageLinks;
		$this->param = $param;
		$this->seo_link = $seo_link;
		
		if ($id != '')
			$next_page = $id . '_next_page';
		else
			$next_page = $id . 'next_page';
		
		if (isset ( $_GET [$next_page] )) {
			$_SESSION [$curr_page] = ( integer ) $_GET [$next_page];
		}
		if (empty ( $_SESSION [$curr_page] ))
			$_SESSION [$curr_page] = 1; // # at first page
		
		$this->curr_page = $_SESSION [$curr_page];
		unset ( $_SESSION [$curr_page] );
	}

	function getData($rows = 10, $inputarr = false) 
	{
		global $ADODB_COUNTRECS;
		
		$this->rows = $rows;
		
		if ($this->db->dataProvider == 'informix')
			$this->db->cursorType = IFX_SCROLL;
		
		$savec = $ADODB_COUNTRECS;
		if ($this->db->pageExecuteCountRows)
			$ADODB_COUNTRECS = true;
		if ($this->cache)
        {
            $rs = $this->db->CachePageExecute ( $this->cache, $this->sql, $rows, $this->curr_page, $inputarr);
        }
		else {
			$rs = $this->db->PageExecute ( $this->sql, $rows, $this->curr_page, $inputarr );
		}
		$ADODB_COUNTRECS = $savec;
	
		$this->rs = &$rs;
		
		$pages_array = array (
				'first' => $this->getFirst (),
				'last' => $this->getLast (),
				'pages' => $this->getPageLinksArray (),
				'next' => $this->getNext (),
				'prev' => $this->getPrevious () 
		);
		
		
		$xtc_data = array (
				'data' => $this->getSQLData (),
				'count' => $this->getPageCount (),
				'data_count' => $this->rs->_maxRecordCount,
				'pages' => $pages_array 
		);
				
		return $xtc_data;
	}
	function getFirst() {
		global $xtLink, $page;
		
		$link_array = array (
				'page' => $page->page_name,
				'params' => $this->param . $this->id . '&next_page=1',
				'conn' => _SYSTEM_CONNECTION 
		);
		$page_link = $xtLink->_link ( $link_array );
		
		return $page_link;
	}
	function getLast() {
		global $xtLink, $page;
		
		if (! $this->db->pageExecuteCountRows)
			return;
		
		$link_array = array (
				'page' => $page->page_name,
				'params' => $this->param . $this->id . '&next_page=' . $this->rs->LastPageNo (),
				'conn' => _SYSTEM_CONNECTION 
		);
		$page_link = $xtLink->_link ( $link_array );
		
		return $page_link;
	}
	function getNext() {
		global $xtLink, $page;
		
		$Page = $this->rs->AbsolutePage ();
		$new_page = $Page + 1;
		
		$link_array = array (
				'page' => $page->page_name,
				'params' => $this->param . $this->id . '&next_page=' . $new_page,
				'conn' => _SYSTEM_CONNECTION 
		);
		$page_link = $xtLink->_link ( $link_array );
		
		return $page_link;
	}
	function getPrevious() {
		global $xtLink, $page;
		
		if (! $this->db->pageExecuteCountRows)
			return;
		
		$Page = $this->rs->AbsolutePage ();
		$new_page = $Page - 1;
		
		$link_array = array (
				'page' => $page->page_name,
				'params' => $this->param . $this->id . '&next_page=' . $new_page,
				'conn' => _SYSTEM_CONNECTION 
		);
		$page_link = $xtLink->_link ( $link_array );
		
		return $page_link;
	}
	function getPageLinks() {
		global $xtLink, $page;
		
		$pages = $this->rs->LastPageNo ();
		$linksperpage = $this->linksPerPage ? $this->linksPerPage : $pages;
		for($i = 1; $i <= $pages; $i += $linksperpage) {
			if ($this->rs->AbsolutePage () >= $i) {
				$start = $i;
			}
		}
		$numbers = '';
		$end = $start + $linksperpage - 1;
		if ($end > $pages)
			$end = $pages;
		
		if ($this->startLinks && $start > 1) {
			$pos = $start - 1;
			
			$link_array = array (
					'page' => $page->page_name,
					'params' => $this->param . $this->id . '&next_page=' . $pos,
					'conn' => _SYSTEM_CONNECTION 
			);
			
			$page_link = $xtLink->_link ( $link_array );
			
			$numbers .= '<a class="navigation_link" href="' . $page_link . '">' . $this->startLinks . '</a>&nbsp;';
		}
		
		for($i = $start; $i <= $end; $i ++) {
			if ($this->rs->AbsolutePage () == $i) {
				$numbers .= '<span class="navigation_selected">' . $i . '</span>&nbsp;';
			} else {
				
				$link_array = array (
						'page' => $page->page_name,
						'params' => $this->param . $this->id . '&next_page=' . $i,
						'conn' => _SYSTEM_CONNECTION 
				);
				
				$page_link = $xtLink->_link ( $link_array );
				
				$numbers .= '<a class="navigation_link" href="' . $page_link . '">' . $i . '</a>&nbsp;';
			}
		}
		if ($this->moreLinks && $end < $pages) {
			
			$link_array = array (
					'page' => $page->page_name,
					'params' => $this->param . $this->id . '&next_page=' . $i,
					'conn' => _SYSTEM_CONNECTION 
			);
			$page_link = $xtLink->_link ( $link_array );
			
			$numbers .= '<a class="navigation_link" href="' . $page_link . '">' . $this->moreLinks . '</a>&nbsp;';
		}
		
		return $numbers;
	}
	function getPageLinksArray() {
		global $xtLink, $page;
		
		$pages = $this->rs->LastPageNo ();
		$start = 1;
		$end = $pages;

        $numbers = [];
		
		for($i = $start; $i <= $end; $i ++) {
			$link_array = array (
					'page' => $page->page_name,
					'params' => html_entity_decode ( $this->param ) . $this->id . '&next_page=' . $i,
					'conn' => _SYSTEM_CONNECTION 
			);
			
			$page_link = $xtLink->_link ( $link_array );
			$numbers [$i] = $page_link;
		}
		
		return $numbers;
	}

    function getSQLData()
    {
		$data = array ();
		
		while ( ! $this->rs->EOF ) {
			
			$data [] = $this->rs->fields;
			
			$this->rs->MoveNext ();
		}
		$this->rs->Close ();
		
		return $data;
	}
	function getPageCount() 
	{
		if (! $this->db->pageExecuteCountRows)
			return '';
		$lastPage = $this->rs->LastPageNo ();
		if ($lastPage == - 1)
			$lastPage = 1; // check for empty rs.
		if ($this->curr_page > $lastPage)
			$this->curr_page = 1;
		
		$data = array (
				'actual_page' => $this->curr_page,
				'last_page' => $lastPage 
		);
		
		return $data;
	}
}