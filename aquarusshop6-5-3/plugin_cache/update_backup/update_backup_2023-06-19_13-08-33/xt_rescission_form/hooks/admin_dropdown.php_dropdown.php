<?php

// ADMIN: config for search mode

defined('_VALID_CALL') or die('Direct Access is not allowed.');

if ($request['get'] == 'not_protected_content_blocks') {
	if (!isset($result)) $result = array();
	$result[] = array (
		'id' => '0',
		'text' => '- None -'
	);
	$query = "SELECT * FROM " . TABLE_CONTENT_BLOCK . " WHERE block_protected = '0'";
	$record = $db->Execute($query);
	if($record->RecordCount() > 0){
		while(!$record->EOF){
			$result[] = array (
				'id' => $record->fields['block_id'],
				'name' => $record->fields['block_tag'],
				'desc' => $record->fields['block_tag']
			);
			$record->MoveNext();
		}$record->Close();
	}
}
