<?php
defined('_VALID_CALL') or die('Direct Access is not allowed.');

define('TABLE_BANNER', DB_PREFIX.'_aq_timed_banner');
define('TABLE_BANNER_DESCRIPTION', DB_PREFIX.'_aq_timed_banner_description');

if (defined('_SYSTEM_ADMIN')) {
    // Register admin navigation
  /*   $admin_navigation = array(
        'shop' => array(
            'children' => array(
                'timed_banners' => array(
                    'text' => 'Timed Banners',
                    'icon' => 'images/icons/clock.png',
                    'url' => 'adminHandler.php',
                    'url_parameters' => array(
                        'load_section' => 'aq_timed_banner',
                        'pg' => 'overview'
                    ),
                    'permission' => 'timed_banners'
                )
            )
        )
    );
    
    $navigation->_addAdminNavigation($admin_navigation);

    // Handle admin pages
    if ($_REQUEST['load_section'] == 'aq_timed_banner') {
        require_once _SRV_WEBROOT._SRV_WEB_PLUGINS.'aq_timed_banner/classes/class.aq_timed_banner_admin.php';
        $banners = new aq_timed_banner_admin();
        
        switch ($_REQUEST['pg']) {
            case 'overview':
                $template = 'admin_timed_banners.html';
                $data = array('banners' => $banners->_getList());
                break;
                
            case 'edit':
                $template = 'admin_timed_banners_edit.html';
                $data = array(
                    'data' => isset($_REQUEST['id']) ? $banners->_get($_REQUEST['id']) : array(),
                    'languages' => $language->_getLanguageList()
                );
                break;
                
            case 'new':
                $template = 'admin_timed_banners_edit.html';
                $data = array(
                    'data' => array(),
                    'languages' => $language->_getLanguageList()
                );
                break;
        }
        
        if (isset($template)) {
            $page = new Template();
            $page->getTemplatePath($template, 'aq_timed_banner');
            $admin_content = $page->getTemplate('admin', $page->template_file, $data);
        }
    } */
}
