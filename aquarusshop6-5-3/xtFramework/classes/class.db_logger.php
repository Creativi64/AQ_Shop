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

class db_logger {
	
	public $min_query_time = MIN_QUERYTIME_LOG;

	public $query_array;
    public $query_count;
    public $total_queries;
    public $total_query_time;
    public $query_tables;


    function __construct() {
        $this->query_array = array();
        $this->query_count = array();
        $this->total_queries = 0;
        $this->total_query_time = 0;
        
        $this->query_tables['tables'][TABLE_PRODUCTS]['count']=0;
        $this->query_tables['tables'][TABLE_PRODUCTS]['time']=0;
        
        $this->query_tables['tables'][TABLE_SEO_URL]['count']=0;
        $this->query_tables['tables'][TABLE_SEO_URL]['time']=0;
        
        $this->query_tables['tables'][TABLE_PLUGIN_PRODUCTS]['count']=0;
        $this->query_tables['tables'][TABLE_PLUGIN_PRODUCTS]['time']=0;
        
        $this->query_tables['tables'][TABLE_MEDIA]['count']=0;
        $this->query_tables['tables'][TABLE_MEDIA]['time']=0;
        
        $this->query_tables['tables'][TABLE_SEO_URL]['count']=0;
        $this->query_tables['tables'][TABLE_SEO_URL]['time']=0;

        if(defined('TABLE_PRODUCTS_REVIEWS'))
        {
            $this->query_tables['tables'][TABLE_PRODUCTS_REVIEWS]['count'] = 0;
            $this->query_tables['tables'][TABLE_PRODUCTS_REVIEWS]['time'] = 0;
        }

        if(defined('TABLE_PRODUCTS_TO_ATTRIBUTES'))
        {
            $this->query_tables['tables'][TABLE_PRODUCTS_TO_ATTRIBUTES]['count'] = 0;
            $this->query_tables['tables'][TABLE_PRODUCTS_TO_ATTRIBUTES]['time'] = 0;
        }
        
    }
    

    function addQuery($sql,$time) {

    //	if (strpos($sql,'19544'))
    // 			debug_print_backtrace();
    	
        $this->total_queries+=1;
        $this->total_query_time+=$time;

        if ($time<$this->min_query_time) return;
        
        $hash = md5($sql);
        
        if (strpos($sql,TABLE_PRODUCTS)) {
        	$this->query_tables['tables'][TABLE_PRODUCTS]['count']+=1;
        	$this->query_tables['tables'][TABLE_PRODUCTS]['time']+=$time;
        }
        
        if (strpos($sql,TABLE_PLUGIN_PRODUCTS)) {
        	$this->query_tables['tables'][TABLE_PLUGIN_PRODUCTS]['count']+=1;
        	$this->query_tables['tables'][TABLE_PLUGIN_PRODUCTS]['time']+=$time;
        }
        
        if (strpos($sql,TABLE_MEDIA)) {
        	$this->query_tables['tables'][TABLE_MEDIA]['count']+=1;
        	$this->query_tables['tables'][TABLE_MEDIA]['time']+=$time;
        }
        
        if (strpos($sql,TABLE_SEO_URL)) {
        	$this->query_tables['tables'][TABLE_SEO_URL]['count']+=1;
        	$this->query_tables['tables'][TABLE_SEO_URL]['time']+=$time;
        }
        
        if(defined('TABLE_PRODUCTS_REVIEWS'))
        {
            if (strpos($sql,TABLE_PRODUCTS_REVIEWS)) {
                $this->query_tables['tables'][TABLE_PRODUCTS_REVIEWS]['count']+=1;
                $this->query_tables['tables'][TABLE_PRODUCTS_REVIEWS]['time']+=$time;
            }
        }


        if(defined('TABLE_PRODUCTS_TO_ATTRIBUTES'))
        {
            if (strpos($sql, TABLE_PRODUCTS_TO_ATTRIBUTES))
            {
                $this->query_tables['tables'][TABLE_PRODUCTS_TO_ATTRIBUTES]['count'] += 1;
                $this->query_tables['tables'][TABLE_PRODUCTS_TO_ATTRIBUTES]['time'] += $time;
            }
        }
        
        $this->query_array[]=time().'|'.$time.'|'.$sql;
        
        
        
        if (isset($this->query_count[$hash])) {
            $this->query_count[$hash]['count']+=1;
            $this->query_count[$hash]['total_time']+=$time;
        } else {
            $this->query_count[$hash]=array('sql'=>$sql,'total_time'=>$time,'count'=>1);
        }
        
        // log different tables
        
        
    }
    
    function _showMultipleQueries($min_amount=2,$table_filter = '') {
    	
    	if ($min_amount>0) {
    	foreach ($this->query_count as $key => $val) {
    		if ($val['count']<$min_amount) unset($this->query_count[$key]);
    	}
    	}
    	
    	if ($table_filter!='') {
    		foreach ($this->query_array as $key => $val) {
    			if (!strpos($val,$table_filter)) unset($this->query_array[$key]);
    		}
    	}
    	
    	__debug($this->query_array);
    	__debug($this->query_tables);
    	
    }
}

