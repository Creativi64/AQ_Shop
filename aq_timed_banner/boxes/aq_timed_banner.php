<?php
defined('_VALID_CALL') or die('Direct Access is not allowed.');

if (AQ_TIMED_BANNER_STATUS == 1) {
    global $current_language_code;

    require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS . 'aq_timed_banner/classes/class.aq_timed_banner.php';

    $banner = new aq_timed_banner();
    $banners = $banner->_getList();

    if (!empty($banners)) {
        
        $tpl_data = array('banners' => $banners);

        $template = new Template();
        $template->getTemplatePath('timed_banners.html', 'aq_timed_banner');
        $output = $template->getTemplate('timed_banners', 'timed_banners.html', $tpl_data);

        // Add to content section
        global $page_data;
        $page_data['content'] .= $output;
    }
}
