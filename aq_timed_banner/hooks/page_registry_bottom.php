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

    if ($_REQUEST['load_section'] == 'AqTimedBanner') {
        include_once _SRV_WEBROOT._SRV_WEB_PLUGINS.'aq_timed_banner/classes/class.aq_timed_banner_admin.php';
        
        $page = new aq_timed_banner_admin();
        
        if (isset($_REQUEST['new']) || isset($_REQUEST['edit_id'])) {
            $template = 'admin_timed_banners_edit.html';
            $data = isset($_REQUEST['edit_id']) ? $page->_get($_REQUEST['edit_id']) : array();
            $tpl_data = array(
                'data' => $data,
                'languages' => $language->_getLanguageList()
            );
        } else {
            $template = 'admin_timed_banners.html'; 
            $tpl_data = array(
                'banners' => $page->_getList()
            );
        }
        
        $admin_content = new Template();
        $admin_content->getTemplatePath($template, 'aq_timed_banner');
        $admin_content = $admin_content->getTemplate('admin', $template, $tpl_data);
    }
}
