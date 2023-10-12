<?php
defined('_VALID_CALL') or die('Direct Access is not allowed.');

if ($request['get'] == 'ppp_refunds_type') {
    $result = array();
    $result[] = array('id' => 'Full', 'name' => TEXT_PPP_REFUNDS_TYPE_FULL);
    $result[] = array('id' => 'Partial', 'name' => TEXT_PPP_REFUNDS_TYPE_PARTIAL);
}

?>