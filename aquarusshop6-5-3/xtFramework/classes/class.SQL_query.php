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

class SQL_query {

    public $filterFunctions; // sql filter functions array
    public $a_sql_table;
    public $a_sql_where;
    public $a_sql_sort;
    public $a_sql_group;
    public $a_sql_limit;
    public $a_sql_cols;
    public $user_position;
    public $position;

	function __construct() {
      $this->user_position=USER_POSITION;
	}
    
    function setUserPosition($position='store') {
      $this->user_position=$position;  
    }

	function explodeArray(&$array) {
		$string = implode(",", $array);
		return $string;
	}

	//////////////
    /// common filters
    /** F_StoreID
     *
     * return part of string with store_id based on current store_id
     *
     * @param (string) ($table) - table to check for column into
     * @param (string) ($column) - column to be checked
     * @param (string) ($sql_colum) - column with prefix to be added in sql
     * @return string
     */
    function F_StoreID($table,$column,$sql_colum){
        global $db,$store_handler;

        if ($sql_colum=='') $sql_colum = $column;

        $store_id = $db->Quote($store_handler->shop_id);
        $add_to_sql =  " and ".$sql_colum."=$store_id ";
        return $add_to_sql;
    }

	//////////////////////////////////////////////////////
	// manage filter function ($values can be a string or array)
	function setFilter ($function, $values = '', $typ = 'and', $_vtype='string') {
	    global $store_handler;

	    if ($store_handler->store_count==1 && $function=='StoreCheck') return;
        
        // check if filter was allready set
     //   if (isset($this->filterFunctions[$function])) return;
        
		if (!is_array($values) && !empty($values))
			$_values[] = $values;
		else
			$_values = $values;

		if(!is_array($_values)) $_values = array();
		$_values['_vtype'] = $_vtype;

		$this->filterFunctions[$function] = $_values;
	}

	function getFilter () {
	    global $store_handler;
		if (is_array($this->filterFunctions) && count($this->filterFunctions) > 0)
            foreach($this->filterFunctions as $function => $values) {

		    if(is_array($values))
            {
                if ($values['_vtype'] == 'string' || empty($values['_vtype']) )
                {
                    unset($values['_vtype']);
                }

                $call_values = $values;
                if (isset($values['_vtype']) && $values['_vtype'] == 'array')
                {
                    unset($values['_vtype']);
                    $call_values = array($values);
                }

                call_user_func_array(array(&$this, 'F_' . $function), $call_values);
            }
		}
	}
	// manage filter function
	//////////////////////////////////////////////////////



	function setPosition ($position) {
		$this->position = $position;
	}
	function getPosition () {
		return $this->position;
	}

    function getSQL_query($sql_cols, $filter_type ='string') {

        $this->getFilter();
        $this->getHooks();

        $sql = 'SELECT '.$sql_cols.$this->a_sql_cols.' FROM '.$this->a_sql_table;
        if (is_data($this->a_sql_where))
            $sql.=' WHERE '.$this->a_sql_where;
        if (is_data($this->a_sql_group))
            $sql.=' GROUP BY '.$this->a_sql_group;
        if (is_data($this->a_sql_sort))
            $sql.=' ORDER BY '.$this->a_sql_sort;
        if (is_data($this->a_sql_limit))
            $sql.=' LIMIT '.$this->a_sql_limit;
        return $sql;
    }

	//////////////////////////////////////////////////////
	// sql arrays start
    private static function is_not_in_string($haystack, &$needle)
    {
        $needle = trim($needle);
        if(is_data($haystack) && stripos($haystack, $needle) !== false)
        {
            return false;
        }
        return $needle;
    }

	function setSQL_COLS ($string, $overwrite = false)
    {
		if (is_data($string))
        {
			if ($overwrite)
            {
				$this->a_sql_cols = ' '.$string;
            }
            else if(self::is_not_in_string($this->a_sql_cols ,$string))
            {
                $this->a_sql_cols .= ' ' . $string . ' ';
            }
        }
	}

	function setSQL_TABLE ($string, $overwrite = false)
    {
		if (is_data($string))
        {
            $string = trim($string);
			if ($overwrite)
            {
                $this->a_sql_table = ' ' . $string . ' ';
            }
            else if(self::is_not_in_string($this->a_sql_table ,$string))
            {
                $this->a_sql_table .= ' ' . $string . ' ';
            }
        }
	}

	function setSQL_WHERE ($string)
    {
        if(self::is_not_in_string($this->a_sql_where ,$string))
        {
            $this->a_sql_where.= ' '.$string . ' ';
        }
	}
	function setSQL_GROUP ($string)
    {
        if(self::is_not_in_string($this->a_sql_group ,$string))
        {
            $this->a_sql_group.= ' '.$string . ' ';
	}
	}
	function setSQL_SORT ($string)
    {
        if(self::is_not_in_string($this->a_sql_sort ,$string))
        {
            $this->a_sql_sort .= ' ' . $string . ' ';
        }
	}
	function setSQL_LIMIT ($string)
    {
        if(self::is_not_in_string($this->a_sql_limit ,$string))
        {
            $this->a_sql_limit= ' '.$string . ' ';
	}
	}

	// sql arrays end
	//////////////////////////////////////////////////////

	function reset(){

		$this->setSQL_TABLE('');
		$this->setPosition('');
		$this->setSQL_COLS('');
		$this->setSQL_WHERE('');
		$this->setSQL_SORT('');
		$this->setSQL_GROUP ('');
		$this->setSQL_LIMIT('');

	}

	//////////////////////////////////////////////////////
	// sql hook plugins start
	function getHooks () {
		if ($this->position == '') return;
		$this->getHookSQL_COLS();
		$this->getHookSQL_TABLE();
		$this->getHookSQL_WHERE();
		$this->getHookSQL_SORT();
		$this->getHookSQL_GROUP();
		$this->getHookSQL_LIMIT();
	}

	function getHookSQL_COLS () {
	 global $xtPlugin;
	 ($plugin_code = $xtPlugin->PluginCode($this->position.':cols')) ? eval($plugin_code) : false;
	 
	 if(isset($cols))
	 $this->setSQL_COLS($cols);
	}
	function getHookSQL_TABLE () {
	 global $xtPlugin;
	 ($plugin_code = $xtPlugin->PluginCode($this->position.':table')) ? eval($plugin_code) : false;
	 
	 if(isset($table))
	 $this->setSQL_TABLE($table);
	}
	function getHookSQL_WHERE () {
	 global $xtPlugin;
	 ($plugin_code = $xtPlugin->PluginCode($this->position.':where')) ? eval($plugin_code) : false;
	 
	 if(isset($where))
	 $this->setSQL_WHERE($where);
	}
	function getHookSQL_SORT () {
	 global $xtPlugin;
	 ($plugin_code = $xtPlugin->PluginCode($this->position.':sort')) ? eval($plugin_code) : false;
     
	 if(isset($sort))
	 $this->setSQL_SORT($sort);
	}
	function getHookSQL_GROUP () {
	 global $xtPlugin;
	 ($plugin_code = $xtPlugin->PluginCode($this->position.':group')) ? eval($plugin_code) : false;
	 
	 if(isset($group))
	 $this->setSQL_GROUP($group);
	}
	function getHookSQL_LIMIT () {
	 global $xtPlugin;
	 ($plugin_code = $xtPlugin->PluginCode($this->position.':limit')) ? eval($plugin_code) : false;
	 
	 if(isset($limit))
	 $this->setSQL_LIMIT($limit);
	}
	// sql hook plugins end
	//////////////////////////////////////////////////////
}
