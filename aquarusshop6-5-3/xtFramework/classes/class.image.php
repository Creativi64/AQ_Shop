<?php
/*
 #########################################################################
 #                       xt:Commerce Shopsoftware
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # Copyright 2021 xt:Commerce GmbH All Rights Reserved.
 # This file may not be redistributed in whole or significant part.
 # Content of this file is Protected By International Copyright Laws.
 #
 # ~~~~~~ xt:Commerce Shopsoftware IS NOT FREE SOFTWARE ~~~~~~~
 #
 # https://www.xt-commerce.com
 #
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # @copyright xt:Commerce GmbH, www.xt-commerce.com
 #
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # xt:Commerce GmbH, Maximilianstrasse 9, 6020 Innsbruck
 #
 # office@xt-commerce.com
 #
 #########################################################################
 */

defined('_VALID_CALL') or die('Direct Access is not allowed.');

class image extends MediaFileTypes{

	var $resource;
	var $target_dir;
	var $extension;
	var $target_name;

	var $max_height;
	var $max_width;
    
    var $compression = 75;
    var $quality_png = 8;
	
	var $crop;

	var $error;

	function __construct() {
		$this->type = 'images';
        $types = $this->getFileExt($this->type);
		$this->FileTypes 	= $types['FileTypes'];
		$this->UploadExt    = $types['UploadTypesArray'];
        
                
        if(constant('_SYSTEM_IMG_QUALITY')) {
            $this->compression = (int)_SYSTEM_IMG_QUALITY;
        }
	    
	    $this->crop = false;
	}

    /**
    * resize image to given size
    * 
    */
	function createThumbnail()
    {
        global $xtPlugin;

		if (!file_exists($this->resource)) {
			error_log('image not found: '.$this->resource);
			$this->error = __text('ERROR_IMAGE_NOT_EXISTS').': '.$this->resource;
			return false;
		}
		//error_log('image found: '.$this->resource);

		$this->extension = $this->_getExtension($this->resource);
		$this->filename = basename($this->resource);
		$this->image_name = str_replace($this->extension,'',$this->filename);

		// right extension ?
		$allowed = $this->UploadExt;
		if (!in_array($this->extension,$allowed)) {
			$this->error = '>'.$this->filename.'-'.$this->extension.'<'.__text('ERROR_IMAGE_EXTENSION_NOT_SUPPORTED');
			return false;
		}
		if (!function_exists('imagecreatefromgif') && $this->extension=='.gif') {
			$this->error = __text('ERROR_IMAGE_GIF_NOT_SUPPORTED');
			return false;
		}


		$exif_it = exif_imagetype($this->resource);

		$ext = 'jpg';
		if ($exif_it == IMAGETYPE_JPEG)
		{
			$image_source = imagecreatefromjpeg($this->resource);
			if (!$image_source)
			{
				$msg = "$this->filename : Expected file type not found [$ext].";
				$this->error = $msg;
				error_log($msg);
				return false;
			}
		}

		$ext = 'png';
		if ($exif_it == IMAGETYPE_PNG)
		{
			$image_source = imagecreatefrompng($this->resource);
			if (!$image_source)
			{
				$msg = "$this->filename : Expected file type not found [$ext].";
				$this->error = $msg;
				error_log($msg);
				return false;
			}
		}

		$ext = 'gif';
		if ($exif_it == IMAGETYPE_GIF)
		{
			$image_source = imagecreatefromgif($this->resource);
			if (!$image_source)
			{
				$msg = "$this->filename : Expected file type not found [$ext].";
				$this->error = $msg;
				error_log($msg);
				return false;
			}
		}
        $ext = 'webp';
        if ($exif_it == IMAGETYPE_WEBP)
        {
            $image_source = imagecreatefromwebp($this->resource);
            if (!$image_source)
            {
                $msg = "$this->filename : Expected file type not found [$ext].";
                $this->error = $msg;
                error_log($msg);
                return false;
            }
        }


		if (!$image_source)
		{
			$f = fopen($this->resource,'r');
			$s = fread($f,20);
			fclose($f);
			$msg = "$this->filename : Could not open file. Not jpg/png/gif. First 20 bytes [$s]";
			$this->error = $msg;
			error_log($msg);
			return false;
		}

		// get height/width
		$old_width =imagesx($image_source);
		$old_height = imagesy($image_source);


	    if ($this->crop) {
            $original_aspect_ratio = $old_width / $old_height;
            $thumb_aspect_ratio = $this->max_width / $this->max_height;

            if ($original_aspect_ratio >= $thumb_aspect_ratio) {
                // if image is wider than thumbnails (in terms of aspect ratio)
                $thumbnail_height = $this->max_height;
                $thumbnail_width = $old_width / ($old_height / $this->max_height);
            }
            else {
                // if thumbnail is wider than image (in terms of aspect ratio)
                $thumbnail_width = $this->max_width;
                $thumbnail_height = $old_height / ($old_width / $this->max_width);
            }
        }
        else {
            $ratio_width = $old_width / $this->max_width;
            $ratio_height = $old_height / $this->max_height;


            if ($ratio_width > $ratio_height) {
                $thumbnail_width = $this->max_width;
                $thumbnail_height = $old_height / $ratio_width;
            } else {
                $thumbnail_height = $this->max_height;
                $thumbnail_width = $old_width / $ratio_height;
            }
        }

		// check if we should create bigger thumb than resource
		if (_SYSTEM_IMG_SHRINK_ONLY=='true') {
			if ($thumbnail_width>$old_width or $thumbnail_height>$old_height) {
				$thumbnail_width = $old_width;
				$thumbnail_height = $old_height;
			}
		}

        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.':createThumbnail_before_create')) ? eval($plugin_code) : false;

		// create new image
	    if ($this->crop) {
            $image_thumbnail=imagecreatetruecolor($this->max_width,$this->max_height);
        }
        else {
            $image_thumbnail=imagecreatetruecolor($thumbnail_width,$thumbnail_height);
        }

        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.':createThumbnail_after_create_image')) ? eval($plugin_code) : false;
        
        if ($exif_it == IMAGETYPE_PNG) imagealphablending($image_thumbnail, false);
        
		// resize image
	    if ($this->crop) {
            // crop original image in the center of original image
            imagecopyresampled($image_thumbnail,
                $image_source,
                0 - ($thumbnail_width - $this->max_width) / 2, // center image horizontally
                0 - ($thumbnail_height - $this->max_height) / 2, // center image vertically
                0, 0,$thumbnail_width,$thumbnail_height,$old_width,$old_height);
        }
        else {
            // resize image wo cropping
            imagecopyresampled($image_thumbnail,$image_source,0,0,0,0,$thumbnail_width,$thumbnail_height,$old_width,$old_height);
        }

		// create image in destinaion dir
		if ($this->target_name!='') {
			$this->image_name = $this->target_name;
		}

        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.':createThumbnail_before_save_image')) ? eval($plugin_code) : false;

        if ($exif_it == IMAGETYPE_PNG) {
            imagesavealpha($image_thumbnail, true);
            imagepng($image_thumbnail,$this->target_dir.$this->image_name,$this->quality_png);
        }
		else if ($exif_it == IMAGETYPE_JPEG) {
            imagejpeg($image_thumbnail,$this->target_dir.$this->image_name,$this->compression);    
        }
		else if ($exif_it == IMAGETYPE_GIF) {
			imagegif($image_thumbnail,$this->target_dir.$this->image_name);
		}
        else if ($exif_it == IMAGETYPE_WEBP) {
            imagewebp($image_thumbnail,$this->target_dir.$this->image_name);
        }

        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.':createThumbnail_before_destroy')) ? eval($plugin_code) : false;

		imagedestroy($image_thumbnail);
		imagedestroy($image_source);

        $ret = true;

        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.':createThumbnail_bottom')) ? eval($plugin_code) : false;

		return $ret;
	}

	/**
	 * Extract extension from filename
	 *
	 * @param string $filename
	 * @return string extension
	 */
	function _getExtension($filename) {
		$extension = strtolower(strrchr($filename,"."));
		return $extension;
	}


	public static function getImgUrlOrTag($params, &$smarty = null)
    {
        global $language, $template, $mediaImages, $isEmailTemplate, $xtPlugin;

        $size  = $class = $imgPath = "";

        $type_array = explode("_", $params['type']);

        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.':'.__FUNCTION__.'_top')) ? eval($plugin_code) : false;

        if(_STORE_IMAGES_PATH_FULL == 'true'
            || (isset($isEmailTemplate) && $isEmailTemplate == true)
            || (isset($params['force_full_path']) && $params['force_full_path'] == 'true')){
            $path_base_url = _SYSTEM_BASE_URL;
        }else{
            $path_base_url = '';
        }

        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.':'.__FUNCTION__.'_path_base_url')) ? eval($plugin_code) : false;

        if(substr($params['img'],0,12)==='product:http')
        {
            $url = substr($params['img'],8);
            unset($params['img']);
        }
        else
        {
            if ($type_array[0] == 't')
            {
                $url = $path_base_url . _SRV_WEB . _SRV_WEB_TEMPLATES . $template->selected_template . '/';
                $imgPath = _SRV_WEBROOT. _SRV_WEB_TEMPLATES . $template->selected_template . '/';
            }
            elseif ($type_array[0] == 'm')
            {
                $url = $path_base_url . _SRV_WEB . _SRV_WEB_IMAGES;
                $imgPath = _SRV_WEBROOT. _SRV_WEB . _SRV_WEB_IMAGES;
            }
            elseif ($type_array[0] == 'mi')
            {
                $url = $path_base_url . _SRV_WEB . _SRV_WEB_ICONS;
                $imgPath = _SRV_WEBROOT. _SRV_WEB_ICONS;
            }
            elseif ($type_array[0] == 'w')
            {
                $url = $path_base_url . _SRV_WEB;
                $imgPath = _SRV_WEBROOT;
            }
            elseif ($type_array[0] == 'p')
            {
                $url = $path_base_url . _SRV_WEB. self::__getImagePath($params['img'], $params['plg'], $params['subdir']);
                $imgPath = _SRV_WEBROOT. self::__getImagePath($params['img'], $params['plg'], $params['subdir']);
            }
            else
            {
                $url = $params['url'];
            }

            $url = str_replace("/xtAdmin", '', $url);

            unset($type_array[0]);

            $file_type = self::__getFileType($params['img'], $type_array[1]);

            $params['img'] = $file_type['filename'];
            $url .= $file_type['main_dir'];

            if (is_data($type_array))
            {
                foreach ($type_array as $key => $value)
                {
                    $url .= $value . '/';
                }
            }

            $imgPath .= implode('/', $type_array).'/'.$params['img'];

            if(!file_exists($imgPath) && $template->selected_template != _SYSTEM_TEMPLATE)
            {
                // fallback to system template
                $imgPath = str_replace('/'.$template->selected_template.'/', '/'._SYSTEM_TEMPLATE.'/' , $imgPath);
                $url =     str_replace('/'.$template->selected_template.'/', '/'._SYSTEM_TEMPLATE.'/' , $url);
            }
        }

        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.':'.__FUNCTION__.'_url')) ? eval($plugin_code) : false;

        if (!empty($imgPath) && file_exists($imgPath))
        {
            $image_size = getimagesize($imgPath);
            $_height = $image_size[1];
            $_width = $image_size[0];

            if (!empty($params['width']) && $params['width'] != '0' && $params['width'] != 'auto' &&
                !empty($params['height']) && $params['height'] != '0' && $params['height'] != 'auto'
            )
            {
                $size = ' width="' . $params['width'] . '" height="' . $params['height'] . '"';
            }
            else if (!empty($params['width']) && $params['width'] != '0' && $params['width'] != 'auto')
            {
                $ratio_width = $_width / $params['width'];
                $height = $_height / $ratio_width;

                $size = ' width="' . $params['width'] . '" height="' . $height . '"';
            }
            else if (!empty($params['height']) && $params['height'] != '0' && $params['height'] != 'auto')
            {
                $ratio_height = $_height / $params['height'];
                $width = $_width / $ratio_height;

                $size = ' width="' . $width . '" height="' . $params['height'] . '"';
            }
            else if ($size == "")
            {
                $size = ' width="' . $_width . '" height="' . $_height . '"';
            }
        }
        else
        {
            if (!empty($params['width']))
            {
                $size = ' width="' . $params['width'] . '"';
            }
            if (!empty($params['height']))
            {
                $size = ' height="' . $params['height'] . '"';
            }
        }

        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.':'.__FUNCTION__.'_size')) ? eval($plugin_code) : false;

        if (!empty($params['class']))
            $class = ' class="' . $params['class'] . '"';

        if (!empty($params['alt'])) {
            $class .= ' alt="' . $params['alt'] . '"';
        } else {
            // add empty alt tag
            $class .= ' alt=""';
        }


        if (!empty($params['title']))
            $class .= ' title="' . $params['title'] . '"';

        if (!empty($params['itemprop']) && ($params['itemprop'] === true) )
            $class .= ' itemprop="image" ';

        $itemprop = '';
        if (!empty($params['itemprop'])) {
            $itemprop =  ' itemprop="'.$params['itemprop'] . '"';
        }

        $img = '<img src="' . $url . $params['img'] . '"' . $class . $size . $itemprop.' />';

        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.':'.__FUNCTION__.'_bottom')) ? eval($plugin_code) : false;

        if (isset($params['path_only'])) {
            if(isset($params['return'])) return $url . $params['img'];
            echo $url . $params['img'];
        } else {
            if(isset($params['return'])) return $img;
            echo $img;
        }
    }

    /**
     * return filepath or false
     * @global type $template
     * @param string $file
     * @param string $dir
     * @param string $subdir
     * @return string|boolean
     */
    public static function __getImagePath($file, $dir, $subdir = '') {
        global $template;

        if ($subdir)
            $subdir = $subdir . '/';

        $img_root_path = _SRV_WEBROOT . _SRV_WEB_TEMPLATES . $template->selected_template . '/' . _SRV_WEB_PLUGINS . $dir . '/images/' . $subdir;
        $img_path =  _SRV_WEB_TEMPLATES . $template->selected_template . '/' . _SRV_WEB_PLUGINS . $dir . '/images/' . $subdir;

        $img_root_plugin_path = _SRV_WEBROOT . _SRV_WEB_PLUGINS . $dir . '/images/' . $subdir;
        $img_plugin_path =  _SRV_WEB_PLUGINS . $dir . '/images/' . $subdir;

        if (file_exists($img_root_path . $file)) {
            return $img_path;
        } elseif (file_exists($img_root_plugin_path . $file)) {
            return $img_plugin_path;
        } else {
            return false;
        }
    }

    public static function __getFileType($img, $type) {

        require_once(_SRV_WEBROOT . _SRV_WEB_FRAMEWORK . 'classes/class.FileHandler.php');

        $tmp_img_data = explode(':', $img);
        $img_type = $tmp_img_data[0];
        $img_name = count($tmp_img_data)>1 ? $tmp_img_data[1] : null;

        $mf = new FileHandler();
        $mf->setParentDir(_SRV_WEB_IMAGES . $img_type . '/' . $type);
        $img_check = $mf->_checkFile($img_name);

        if ($img_check) {
            $img_array = array('main_dir' => $img_type . '/', 'filename' => $img_name);
        } else {

            if (preg_match('/:/', $img))
                $img = $img_name;

            $img_array = array('main_dir' => '', 'filename' => $img);
        }

        return $img_array;
    }

}