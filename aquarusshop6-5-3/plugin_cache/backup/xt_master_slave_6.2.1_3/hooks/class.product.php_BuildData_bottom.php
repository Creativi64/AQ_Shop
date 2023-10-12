<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

if(!function_exists('checkImageExists'))
{
    function checkImageExists($s)
    {
        return !empty($s) && strpos($s, 'product:noimage') != false;
    }
}

global $ms_allow_add_cart;
if(XT_MASTER_SLAVE_ACTIVE==1)
{
    if (empty($this->data['products_master_model']) && isset($ms_allow_add_cart) && ($this->data['products_master_flag'] || $this->data['products_master_model']) && $this->data['allow_add_cart'] == true) // only override when true an not already disabled by some plg/core code
    {
        $this->data['allow_add_cart'] = $ms_allow_add_cart;
    }

	$master_model = false;
    $variant = null;
    if(!empty($this->data['products_master_model']))
    {
        $master_model = $this->data['products_master_model'];
        $variant = $this;
    }
    else if(!empty($this->data['products_master_flag']))
    {
        $master_model = $this->data['products_model'];
    }
    $m_data = false;
    if($master_model)
    {
        $m_data = xt_master_slave_functions::getMasterData($master_model);
    }

    if($m_data)
    {
        $loadMasterData = xt_master_slave_functions::getImagesOverrideSetting(XT_MASTER_SLAVE_LOAD_MASTER_IMAGE_IN_SLAVE, 'ms_load_masters_main_img', $m_data, $variant);
        if ($loadMasterData)
        {
            if($loadMasterData == 1)
            {
                if (is_array($this->data['more_images']) && checkImageExists($this->data['products_image']))
                {
                    array_unshift($this->data['more_images'], array('file' => $this->data['products_image']));
                }
                else if (checkImageExists($this->data['products_image']))
                {
                    $this->data['more_images'][] = ['file' => $this->data['products_image']];
                }
                $this->data['products_image'] = $m_data['products_image'];
            }
            else {
                $this->data['more_images'][] = ['file' => $m_data['products_image']];
            }
        }

        $loadMasterData = xt_master_slave_functions::getImagesOverrideSetting(LOAD_ALL_MAINS_IMAGES_IN_VARIANT, 'load_mains_imgs', $m_data, $variant);
        if ($loadMasterData)
        {
            global $mediaImages;
            $media_images = $mediaImages->get_media_images($m_data['products_id'], 'product');
            $more_images = $media_images['images'];

            if(count($more_images))
            {
                if ($loadMasterData == 1)
                {

                    if (is_array($this->data['more_images']))
                    {
                        $this->data['more_images'] = array_merge($more_images, $this->data['more_images']);
                    }
                    else
                    {
                        $this->data['more_images'] = $more_images;
                    }
                }
                else
                {
                    if(is_array($this->data['more_images']))
                        $this->data['more_images'] = array_merge($this->data['more_images'], $more_images);
                    else
                        $this->data['more_images'] = $more_images;
                }
            }
        }

        $loadMasterData = xt_master_slave_functions::getOverrideSetting(XT_MASTER_SLAVE_SHORT_DESCRIPTION_FROM_MASTER, 'products_short_description_from_master', $m_data);
        if ($loadMasterData)
        {
            $this->data['products_short_description'] = $m_data['products_short_description'];
        }

        $loadMasterData = xt_master_slave_functions::getOverrideSetting(XT_MASTER_SLAVE_DESCRIPTION_FROM_MASTER, 'products_description_from_master', $m_data);
        if ($loadMasterData)
        {
            $this->data['products_description'] = $m_data['products_description'];
        }

        $loadMasterData = xt_master_slave_functions::getOverrideSetting(XT_MASTER_SLAVE_KEYWORDS_FROM_MASTER, 'products_keyword_from_master', $m_data);
        if (XT_MASTER_SLAVE_KEYWORDS_FROM_MASTER == '1' || $this->data['products_keyword_from_master'] || $m_data['products_keyword_from_master'])
        {
            $this->data['products_keywords'] = $m_data['products_keywords'];
        }
    }


    $loadMasterData = xt_master_slave_functions::getOverrideSetting(XT_MASTER_SLAVE_USE_MASTER_FREE_FILES, 'ms_load_masters_free_downloads', $m_data);
    if ($size == 'full'
        && XT_MASTER_SLAVE_ACTIVE == '1'
        && $loadMasterData
        && $this->data['products_master_flag'] == 0 && !empty($this->data['products_master_model']))
    {
        global $db, $mediaImages, $mediaFiles;;
        $master_id = $db->GetOne("SELECT p.products_id FROM " . TABLE_PRODUCTS . " p WHERE products_model = (SELECT p2.products_master_model FROM " . TABLE_PRODUCTS . " p2 WHERE products_id=?)", array((int)$this->data['products_id']));

        $media_data_master = $mediaFiles->get_media_data($master_id, 'product', 'product', 'info=' . $master_id);
        $media_files_master = $this->_getPermittedMediaData($media_data_master['files']);

        if (is_array($media_files_master))
        {
            if (!is_array($this->data['media_files']))
            {
                $this->data['media_files'] = array();
            }
            $tmp_media_files = array_merge($this->data['media_files'], $media_files_master);

            $this->data['media_files'] = array();
            foreach ($tmp_media_files as $k => $v)
            {
                if (!array_key_exists($v['id'], $this->data['media_files']))
                {
                    $this->data['media_files'][$v['id']] = $v;
                }
            }
        }
    }
}
