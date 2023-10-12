<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

if ($request['get']=='cleancache_types') {
	if(!isset($result)) $result = array();
	$result[] =  array('id' => 'all',
			'name' => 'All',
			'desc' => 'All');
	$result[] =  array('id' => 'feed',
			'name' => 'Feed',
			'desc' => 'Feed');
	$result[] =  array('id' => 'category',
			'name' => 'Category',
			'desc' => 'Category');
}
