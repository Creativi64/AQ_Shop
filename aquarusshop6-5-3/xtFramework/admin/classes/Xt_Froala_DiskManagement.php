<?php

use FroalaEditor\Utils\Utils;

class Xt_Froala_DiskManagement extends \FroalaEditor\Utils\DiskManagement
{
    public static function upload($fileRoute, $options)
    {

        $fieldname = $options['fieldname'];

        if (empty($fieldname) || empty($_FILES[$fieldname])) {
            throw new \Exception('Fieldname is not correct. It must be: ' . $fieldname);
        }

        if (
            isset($options['validation']) &&
            !Utils::isValid($options['validation'], $fieldname)
        ) {
            throw new \Exception('File does not meet the validation.');
        }

        // Get filename.
        $temp = explode(".", $_FILES[$fieldname]["name"]);

        // Get extension.
        $extension = end($temp);

        // Generate new random name.
        $name = $temp[0] . '_' . sha1(microtime()) . "." . $extension;

        $fullNamePath = $_SERVER['DOCUMENT_ROOT'] . $fileRoute . $name;

        $mimeType = Utils::getMimeType($_FILES[$fieldname]["tmp_name"]);

        if (isset($options['resize']) && $mimeType != 'image/svg+xml') {
            // Resize image.
            $resize = $options['resize'];

            // Parse the resize params.
            $columns = $resize['columns'];
            $rows = $resize['rows'];
            $filter = isset($resize['filter']) ? $resize['filter'] : \Imagick::FILTER_UNDEFINED;
            $blur = isset($resize['blur']) ? $resize['blur'] : 1;
            $bestfit = isset($resize['bestfit']) ? $resize['bestfit'] : false;

            $imagick = new \Imagick($_FILES[$fieldname]["tmp_name"]);

            $imagick->resizeImage($columns, $rows, $filter, $blur, $bestfit);
            $imagick->writeImage($fullNamePath);
            $imagick->destroy();
        } else {
            // Save file in the uploads folder.
            move_uploaded_file($_FILES[$fieldname]["tmp_name"], $fullNamePath);
        }

        // Generate response.
        $response = new \StdClass;
        $response->link = $fileRoute . $name;

        return $response;
    }
}