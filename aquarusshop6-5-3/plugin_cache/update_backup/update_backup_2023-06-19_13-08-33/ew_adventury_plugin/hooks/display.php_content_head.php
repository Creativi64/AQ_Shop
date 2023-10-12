<?php defined('_VALID_CALL') or die('Direct Access is not allowed.');

/**
 * Manage html error reporting
 */

use ew_adventury\plugin as ew_adventury_plugin;

if (class_exists('ew_adventury\plugin') && ew_adventury_plugin::status()) {

    ?>

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <?php foreach (ew_adventury_plugin::getIE8js() as $file) : ?>
    <script type="text/javascript" src="<?php echo $file ?>"></script>
    <?php endforeach ?>
    <![endif]-->

    <?php

}
