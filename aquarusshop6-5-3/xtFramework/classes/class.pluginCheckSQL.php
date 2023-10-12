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


class pluginCheckSQL {
	
	private $version = null;
	public $mode = 'insert';
	
	function __construct($plugin) {
		$this->pluginSql = $plugin;
	}

	function check() {
		if ($this->mode != 'insert') return $this->pluginSql;
		
		$pat = '/\$db->Execute\s*\(\s*"(.*)"\s*\)\s*;/sU';
		$x = preg_match_all ($pat, $this->pluginSql, $res);
		$install = $res[1];

		$runSql = array();
		foreach ($install as $inst) {
 			if ($i = $this->checkStmt(trim($inst))) {
				$runSql[] =  '$db->Execute ("' . $i . ';");';
 			}
		}
		return implode(' ',$runSql);
	}

	function checkStmt ($s) {

		$s = $this->checkCreateStmt($s);
		$s = $this->checkDropTable($s);
		$s = $this->checkAdminNavStmt($s);

		return $s;
	}

	function checkDropTable ($s) {
		global $db;
		
		$pat = '/^drop\s+table\s+(if\s+exists\s+)/im';
		$pat2 = '/^DROP TABLE/i';
		$replace = 'DROP TABLE IF EXISTS %s ';
		if (!preg_match ($pat, $s, $res)) {
		    if(is_array($res) && count($res) > 1)
            {
                $rep = sprintf($replace, $res[1]);
                $s = preg_replace($pat2, $rep, $s);
            }
		}
		return $s;
	} // checkDropTable()	
	
	function checkCreateStmt ($s) {
		global $db;
		
		$pat = '/^create\s+table\s+(if\s+not\s+exists\s+)?/im';
		$pat2 = '/DB_PREFIX\s*\.\s*"\s*([^(]+)\s*\(/';
		$pat3 = '/^CREATE TABLE ([^(]+) \(/';
		$replace = 'CREATE TABLE IF NOT EXISTS %s (';
		if (preg_match ($pat, $s, $res)) {
			if (preg_match ($pat2, $s, $res)) {
				$orig_table = $res[1];
				
				$table =  trim(str_replace ('`', '', $orig_table));
				
				// check prefix
				if (DB_PREFIX != substr($table, strlen(DB_PREFIX)))
				    $table =  DB_PREFIX . $table;
				
				$checkTable = $this->checkTableExists($table);

				if ($checkTable) {
					$sql = 'show create table ' . $table;
					$result = $db->Execute($sql);
					$x = $result->fields;
	
					$this->copyTable($table, $x['Create Table']);
	
					$table =  str_replace ('`', '', $x['Create Table']);
					$s = preg_replace ($pat3, sprintf ($replace, $orig_table), $table);
				}
			}
		}
		return $s;
	} // checkCreateStmt()

	function checkTableExists($table){
		global $db;
		$result=$db->Execute("SHOW TABLES LIKE '".$table."'");
		if ($result->RecordCount()>0) return true;
		return false;
	}
	
	function copyTable($table,$createTableStmt) {
		global $db;

		$db->Execute("DROP TABLE IF EXISTS ".$table."_save");
		$create = str_replace($table, $table.'_save', $createTableStmt);
		
		$db->Execute($create);

		$sql = 'insert into '.$table.'_save select * from '.$table;
		$db->Execute($sql);
	}
	
	function checkAdminNavStmt(& $s) {
		
		$pat = '/^insert\s+into\s+?/im';
		$pat2 = '(TABLE_ADMIN_NAVIGATION|_alc_nav)';
		if ($this->mode == 'insert' && preg_match($pat,$s,$res)) {
			if (preg_match($pat2,$s,$res)) {
				return $s;
			}
			return $s;
		}
		return false;

	}
}