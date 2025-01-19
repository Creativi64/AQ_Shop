<?php
defined('_VALID_CALL') or die('Direct Access is not allowed.');

if (defined('_SYSTEM_ADMIN'))
{
    $admin_menu[] = array(
        'MenuText' => 'Timed Banners',
        'MenuLink' => 'adminHandler.php?load_section=AqTimedBanner',
        'MenuIcon' => 'images/icons/clock.png',  
        'MenuSection' => 'marketing'
    );
}
