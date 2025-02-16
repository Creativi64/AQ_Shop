<?php

use FroalaEditor\Image;
use FroalaEditor\Utils\DiskManagement;

require_once 'Xt_Froala_DiskManagement.php';

class Xt_Froala_Image extends \FroalaEditor\Image
{
    /**
     * @throws Exception
     */
    public static function upload($fileRoute, $options = NULL)
    {
        // Check if there are any options passed.
        if (is_null($options)) {
            $options = Image::$defaultUploadOptions;
        } else {
            $options = array_merge(Image::$defaultUploadOptions, $options);
        }

        // Upload image.
        return Xt_Froala_DiskManagement::upload($fileRoute, $options);
    }
}