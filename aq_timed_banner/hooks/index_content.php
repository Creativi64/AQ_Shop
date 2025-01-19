<?php
defined('_VALID_CALL') or die('Direct Access is not allowed.');

if (AQ_TIMED_BANNER_STATUS == 1) {
    require_once _SRV_WEBROOT._SRV_WEB_PLUGINS.'aq_timed_banner/classes/class.aq_timed_banner.php';
    
    $banner = new aq_timed_banner();
    $banners = $banner->_getList();
    
    if (!empty($banners)) {
        foreach ($banners as $banner) {
            $tpl_data['banners'][] = array(
                'id' => $banner['banner_id'],
                'title' => $banner['banner_title'],
                'description' => $banner['banner_description'],
                'image' => $banner['banner_image_url'],
                'link' => $banner['banner_link']
            );
        }
        
        $template = new Template();
        $template->getTemplatePath('timed_banners.html', 'aq_timed_banner');
        $output = $template->getTemplate('timed_banners', 'timed_banners.html', $tpl_data);
        
        echo $output;
    }
}
