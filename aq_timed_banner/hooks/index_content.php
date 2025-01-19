<?php
defined('_VALID_CALL') or die('Direct Access is not allowed.');

if (AQ_TIMED_BANNER_STATUS == 1) {
    global $current_language_code;
    
    require_once _SRV_WEBROOT._SRV_WEB_PLUGINS.'aq_timed_banner/classes/class.aq_timed_banner.php';
    
    $banner = new aq_timed_banner();
    $banners = $banner->_getList();
    
    if (!empty($banners)) {
        // Get language specific content for each banner
        foreach ($banners as &$banner) {
            // Get banner description for current language
            $sql = "SELECT banner_title, banner_description 
                   FROM ".DB_PREFIX."_aq_timed_banner_description 
                   WHERE banner_id = ? AND language_code = ?";
            $result = $db->Execute($sql, array($banner['banner_id'], $current_language_code));
            
            if ($result->RecordCount() > 0) {
                $banner['title'] = $result->fields['banner_title'];
                $banner['description'] = $result->fields['banner_description'];
            }
            
            $banner['image'] = _SRV_WEB_IMAGES.$banner['banner_image'];
        }
        
        $tpl_data = array('banners' => $banners);
        
        $template = new Template();
        $template->getTemplatePath('timed_banners.html', 'aq_timed_banner');
        $output = $template->getTemplate('timed_banners', 'timed_banners.html', $tpl_data);
        
        // Add to content section
        global $page_data;
        $page_data['content'] .= $output;
    }
}
