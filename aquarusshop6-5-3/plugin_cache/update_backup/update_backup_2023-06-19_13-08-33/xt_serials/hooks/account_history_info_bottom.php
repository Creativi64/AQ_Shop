<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

$serials = new product_serials();
$serials->getSerialsFrontend((int)$_GET['oid']);
