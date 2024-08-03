<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

if(array_key_exists('_SYSTEM_CKEDITOR_CONFIG', $header) && version_compare(_SYSTEM_VERSION, '6.2', '>='))
{
    $header['_SYSTEM_CKEDITOR_CONFIG']['type'] = 'textarea';
}
