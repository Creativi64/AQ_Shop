<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

$serials = new product_serials();
$extras .=$serials->getSerialsAdmin($this->oID);
