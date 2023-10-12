<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

if (ACTIVATE_XT_RESCISSION_FORM == 'true' && XT_RESCISSION_FORM_SHOW_IN_CONTENT_BLOCK != '' && XT_RESCISSION_FORM_SHOW_IN_CONTENT_BLOCK == $block) {
	$contentId = 'rescission_form';
		$conn = 'SSL';

	$link_array = array('page'=>'xt_rescission_form', 'conn'=>$conn);
	$url = $xtLink->_link($link_array);
	
	// Set link sort
	$length = count($array);
	$firstPart = array_slice($array, 0, XT_RESCISSION_FORM_LINK_SORT);
	$secondPart = array_slice($array, XT_RESCISSION_FORM_LINK_SORT, $length);
	
	$firstPart[] = array(
			'content_id' => $contentId,
			'language_code' => $language->code,
			'title' => XT_RESCISSION_FORM_LINK_TITILE,
			'link' => $url,
	);
	
	$array = array_merge($firstPart, $secondPart);
}
