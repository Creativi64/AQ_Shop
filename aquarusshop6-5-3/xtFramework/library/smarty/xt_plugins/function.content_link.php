<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

function smarty_function_content_link($params, & $smarty)
{
    global $db, $xtLink, $language, $store_handler;

    $_content = new content();

    $query = "SELECT 
		                 c.*,
						 su.url_text,
						 su.language_code
						 FROM " . TABLE_CONTENT . " c
			 			 INNER JOIN " . TABLE_SEO_URL . " su  ON (su.link_id = c.content_id and su.link_type='3')
			 			 ".$_content->permission->_table."
						 WHERE 
						 c.content_id = ? and c.content_status = 1
						 and su.language_code = ?
						 and su.store_id = ?
						 " . $_content->permission->_where ;

    $record = $db->Execute($query, array((int)$params['cont_id'], $language->code, $store_handler->shop_id));

    $link_array = array(
        'page' => 'content',
        'type' => 'content',
        'name' => $record->fields['content_title'],
        'id' => $record->fields['content_id'],
        'seo_url' => $record->fields['url_text']
    );

    $url = $xtLink->_link($link_array);
    echo $url;

}