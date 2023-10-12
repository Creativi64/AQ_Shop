<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

$params = isset($data_in) ? json_decode(json_encode($data_in), true) : [];

if(isset($ret) && !empty($params))
{
    $subpage = new subpage($params);
    if($subpage->loaded_subpage != false){
        $page_data = '#page ['.$params['name'].'] computed in xtCore/connector/page.php#';

        global $page;
        $page_data = array('page' => $params['name']);
        $page = new page($page_data);

        include $subpage->loaded_subpage;
        $ret->callback = 'replaceDomElement';
        $ret->callback_args = $params['callback_args'];
        $ret->callback_args['html'] = $page_data;
        $ret->success = true;
    }else{
        $response_code = 200;
    }
}
else {
    die();
}

