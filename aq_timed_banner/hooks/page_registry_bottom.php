<?php
defined('_VALID_CALL') or die('Direct Access is not allowed.');

if (defined('_SYSTEM_ADMIN')) {
    // Register admin menu item
    $admin_menu[] = array(
        'MenuText' => 'Timed Banners',
        'MenuLink' => 'adminHandler.php?plugin=aq_timed_banner&load_section=AqTimedBanner',
        'MenuIcon' => 'images/icons/clock.png',
        'MenuSection' => 'marketing'
    );
    
    // Register admin page handler
    if ($_GET['plugin'] == 'aq_timed_banner' && $_GET['load_section'] == 'AqTimedBanner') {
        require_once _SRV_WEBROOT._SRV_WEB_PLUGINS.'aq_timed_banner/classes/class.aq_timed_banner_admin.php';
        
        $banner = new aq_timed_banner_admin();
        
        if (isset($_GET['new'])) {
            $data = array();
        } else if (isset($_GET['edit_id'])) {
            $data = $banner->_get((int)$_GET['edit_id']);
        } else if (isset($_GET['save'])) {
            if ($_POST['banner_id']) {
                $banner->_set($_POST, 'edit');
            } else {
                $banner->_set($_POST, 'new');
            }
            header('Location: adminHandler.php?plugin=aq_timed_banner&load_section=AqTimedBanner');
            exit;
        } else if (isset($_GET['delete_id'])) {
            $banner->_delete((int)$_GET['delete_id']);
            header('Location: adminHandler.php?plugin=aq_timed_banner&load_section=AqTimedBanner');
            exit;
        }
        
        // Set template data
        global $language;
        $template = new Template();
        
        if (isset($_GET['new']) || isset($_GET['edit_id'])) {
            $template->getTemplatePath('/admin_timed_banners_edit.html', 'aq_timed_banner');
            $tpl_data = array(
                'data' => $data,
                'languages' => $language->_getLanguageList()
            );
        } else {
            $template->getTemplatePath('/admin_timed_banners.html', 'aq_timed_banner');
            $tpl_data = array(
                'banners' => $banner->_getList()
            );
        }
        
        $template_content = $template->getTemplate('admin', $template->template_file, $tpl_data);
        $page_data = array('content' => $template_content);
    }
}
