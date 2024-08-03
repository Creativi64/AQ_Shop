<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

/** @var $request array */
if (isset($xtPlugin->active_modules['xt_master_slave']) && $request['get'] === 'xt_reviews_master_slave')
{
	$result = array(
		array('id' => 'default', 'name' => 'Default', 'desc' => 'Reviews displayed where they are written'),
		array('id' => 'master_only', 'name' => 'Master only', 'desc' => 'All reviews are displayed in master only'),
		array('id' => 'slave_only', 'name' => 'Slave only', 'desc' => 'Only slave products display reviews'),
		array('id' => 'master_all', 'name' => 'Master shows all (+slaves)', 'desc' => 'Master products display all reviews, while slaves only display their own')
	);
}

if ($request['get'] === 'xt_reviews_export_min_rating')
{
    $result = array(
        array('id' => '', 'name' => '', 'desc' => ''),
        array('id' => '1', 'name' => '*', 'desc' => '*'),
        array('id' => '2', 'name' => '* *', 'desc' => '* *'),
        array('id' => '3', 'name' => '* * *', 'desc' => '* * *'),
        array('id' => '4', 'name' => '* * * *', 'desc' => '* * * *'),
        array('id' => '5', 'name' => '* * * * *', 'desc' => '* * * * *'),
    );
}