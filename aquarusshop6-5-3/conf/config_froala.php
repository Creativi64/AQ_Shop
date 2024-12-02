<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

// Größe des Editors
define('FROALA_HEIGHT_MIN', 200);
define('FROALA_HEIGHT_MAX', 400);

// FROALA_CDN_VERSION
// Version der js/css-Bibliothek
// bei Änderung muss Backend neu geladen werden
define('FROALA_CDN_VERSION', 'latest');


// FROALA_LOAD_ON_FOCUS_ONLY
// true  Editor wird erst geladen bei Klick ins Editor-Feld
// false Editor wird sofort geladen
// bei Änderung muss Backend neu geladen werden
define('FROALA_LOAD_ON_FOCUS_ONLY', false);


// Optionen für den Bild-Upload
$froala_imageUploadOptions = array(
    'validation' => array(
        'allowedExts' => array('gif', 'jpeg', 'jpg', 'png', 'webp'),
        'allowedMimeTypes' => array('image/gif', 'image/jpeg', 'image/pjpeg', 'image/x-png', 'image/png', 'image/webp')
    )
);

// Optionen für den Video-Upload
$froala_videoUploadOptions = array(
    'validation' => array(
        'allowedExts' => array('mp4', 'webm', 'ogg'),
        'allowedMimeTypes' => array('video/mp4','video/webm', 'video/ogg')
    )
);

// sollen Unterordner unterhalb media/u/images media/u/videos erzeugt werden
// product /content / xt_checkout_options etc, je nach xt-Klasse
$froala_useUploadSubFolders = true;

