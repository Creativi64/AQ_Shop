<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $klarna_trigger_warning;
if($klarna_trigger_warning != false)
{
    $obj = $klarna_trigger_warning;
}