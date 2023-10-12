INSERT IGNORE INTO `##_config_**_REPLACE_STORE_ID_**` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_ACCOUNT_SALUTATION_PRESET', '', 5, 1, NULL, NULL);
INSERT IGNORE INTO `##_config_**_REPLACE_STORE_ID_**` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_ACCOUNT_DOB_PRESET', '', 5, 3, NULL, NULL);

UPDATE `##_config_**_REPLACE_STORE_ID_**` SET `config_value`='html5' WHERE `config_key`='_STORE_META_DOCTYPE_HTML';
UPDATE `##_config_**_REPLACE_STORE_ID_**` SET `config_value`='xt_responsive' WHERE `config_key`='_STORE_DEFAULT_TEMPLATE';

DELETE FROM `##_config_**_REPLACE_STORE_ID_**` WHERE `config_key`IN ('_STORE_JQUERY_VERSION','_STORE_JQUERY_CDN','_STORE_JQUERY_ACTIVATE','_STORE_TPL_LISTING_COLUMNS','_STORE_MOBILE_ACTIVATE','_STORE_TABLET_IS_MOBILE','_STORE_DEFAULT_MOBILE_TEMPLATE','_STORE_MOBILE_SWITCH_METHOD');
