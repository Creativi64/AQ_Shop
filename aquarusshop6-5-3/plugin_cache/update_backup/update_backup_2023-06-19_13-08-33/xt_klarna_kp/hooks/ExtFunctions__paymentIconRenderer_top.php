<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

$add_renderer .= "
if (data == 'xt_klarna_kp') { return '<i class=\"pf pf-klarna\"></i>'; } 
";