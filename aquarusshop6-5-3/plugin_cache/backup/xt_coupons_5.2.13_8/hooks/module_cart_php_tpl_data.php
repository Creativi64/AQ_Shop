<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

if(isset($_SESSION['coupon_info']))
{
    unset($_SESSION['coupon_info']);
}

if(isset($_SESSION['coupon_info_type']))
{
    unset($_SESSION['coupon_info_type']);
}
