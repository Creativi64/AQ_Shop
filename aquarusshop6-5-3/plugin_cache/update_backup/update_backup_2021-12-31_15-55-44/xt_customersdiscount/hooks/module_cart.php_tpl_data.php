<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

if (false && $_POST['action']!='update_product' && isset($_SESSION['cart']->discount)) {
    if ($_SESSION['cart']->discount != 'false') {
        $tpl_data = array_merge($tpl_data,array('discount'=>$_SESSION['cart']->discount));
    }
}
