<?php
defined('_VALID_CALL') or die('Direct Access is not allowed.');

if (defined('_SYSTEM_ADMIN')) {
    $admin_menu[] = array(
        'text' => 'Timed Banners',
        'link' => 'ejsadmin.php?plugin=aq_timed_banner&page=banners', 
        'image' => 'images/icons/clock.png',
        'node_group' => 'marketing'
    );
    
    if ($_REQUEST['plugin'] == 'aq_timed_banner') {
        include_once _SRV_WEBROOT._SRV_WEB_PLUGINS.'aq_timed_banner/classes/class.aq_timed_banner_admin.php';
        
        $banners = new aq_timed_banner_admin();
        $page = new Template();
        
        if (isset($_REQUEST['page']) && $_REQUEST['page'] == 'banners') {
            if (isset($_REQUEST['new']) || isset($_REQUEST['edit_id'])) {
                $page->getTemplatePath('admin_timed_banners_edit.html', 'aq_timed_banner');
                $obj = isset($_REQUEST['edit_id']) ? $banners->_get($_REQUEST['edit_id']) : array();
                $page_data = array(
                    'data' => $obj,
                    'languages' => $language->_getLanguageList()
                );
            } else {
                $page->getTemplatePath('admin_timed_banners.html', 'aq_timed_banner');
                $page_data = array(
                    'banners' => $banners->_getList()
                );
            }
            $admin_content = $page->getTemplate('admin', $page->template_file, $page_data);
        }
    }
}
