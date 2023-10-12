<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

$key_fraud_status = array( 'kp_order_status_overview' => '' );
$keys = array_keys( $default_array );
$idx = array_search( 'payment', $keys );
$pos = false === $idx ? count( $default_array ) : $idx + 1;
$default_array = array_merge( array_slice( $default_array, 0, $pos ), $key_fraud_status, array_slice( $default_array, $pos ) );
