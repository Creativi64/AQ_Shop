<?php

$functions = array();

function md_db_update_102() {
    if (!MagnaDB::gi()->columnExistsInTable('variation_theme', TABLE_MAGNA_AMAZON_APPLY)){
        MagnaDB::gi()->query('ALTER TABLE `' . TABLE_MAGNA_AMAZON_APPLY .
            '` ADD COLUMN `variation_theme` varchar(400) DEFAULT "{\"autodetect\":[]}" AFTER `PreparedTS`');
    }
}

$functions[] = 'md_db_update_102';
