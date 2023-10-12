<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

$cb = PhpExt_Form_Checkbox::createCheckbox("filter_klarna_not_captured",constant('TEXT_KLARNA_NOT_COMPLETELY_CAPTURED'));
$cb ->setCssClass("checkBox");
$a[] = $cb;