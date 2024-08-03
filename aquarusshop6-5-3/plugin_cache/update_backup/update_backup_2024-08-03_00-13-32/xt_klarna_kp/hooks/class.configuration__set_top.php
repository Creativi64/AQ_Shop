<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

if(array_key_exists('_KLARNA_CONFIG_TRIGGER_CANCEL', $data) && array_key_exists('_KLARNA_CONFIG_TRIGGER_FULL_CAPTURE', $data))
{
    $cancel_triggers  = array_values(explode(',',$data['_KLARNA_CONFIG_TRIGGER_CANCEL']));
    $capture_triggers = array_values(explode(',',$data['_KLARNA_CONFIG_TRIGGER_FULL_CAPTURE']));

    /** überschneidungen in den triggern sind nicht erlaubt  */
    $no_conflict =  array_intersect($cancel_triggers, $capture_triggers);

    if(!empty($no_conflict))
    {
        $obj->succes = false;
        $obj->error_message = 'Cancel- und Capture-Trigger Überschneidung!<br />Cancel- and Capture-Triggers intersect!';
        return $obj;
    }
}
