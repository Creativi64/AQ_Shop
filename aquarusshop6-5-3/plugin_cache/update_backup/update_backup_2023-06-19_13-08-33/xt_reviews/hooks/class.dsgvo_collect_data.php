<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

/** @var $customers_id int */

unset($data_dsgvo);
$data_dsgvo = xt_reviews::getReviewsForCustomer($customers_id);
$data['xt_reviews'] = $data_dsgvo;