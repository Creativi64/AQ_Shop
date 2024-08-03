<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

if (strpos($_SERVER['REQUEST_URI'], '/ITRK')!==false && !empty($_REQUEST['xml']))
{
    $dontFilterAtAllKeys[] = 'xml';
}