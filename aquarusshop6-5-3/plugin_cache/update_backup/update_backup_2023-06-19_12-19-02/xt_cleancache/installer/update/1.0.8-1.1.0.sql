/*
-- Query: SELECT * FROM dev8_5.xt_clean_cache
LIMIT 0, 1000

-- Date: 2016-08-17 11:42
*/

ALTER TABLE `xt_clean_cache_logs` CHANGE COLUMN `type` `type` VARCHAR(64) NOT NULL ;

DROP IF EXISTS TABLE `xt_clean_cache`;

CREATE TABLE `xt_clean_cache` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(64) NOT NULL,
  `type_desc` varchar(512) DEFAULT NULL,
  `last_run` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `type_UNIQUE` (`type`)
);

INSERT INTO `xt_clean_cache` (`id`,`type`,`type_desc`,`last_run`) VALUES (1, 'all','All',NULL);
INSERT INTO `xt_clean_cache` (`id`,`type`,`type_desc`,`last_run`) VALUES (2, 'cache_feed','Feed',NULL);
INSERT INTO `xt_clean_cache` (`id`,`type`,`type_desc`,`last_run`) VALUES (3, 'cache_category','Category Cache',NULL);
INSERT INTO `xt_clean_cache` (`id`,`type`,`type_desc`,`last_run`) VALUES (4, 'cache_css','CSS',NULL);
INSERT INTO `xt_clean_cache` (`id`,`type`,`type_desc`,`last_run`) VALUES (5, 'cache_js','Javascript',NULL);
INSERT INTO `xt_clean_cache` (`id`,`type`,`type_desc`,`last_run`) VALUES (6, 'templates_c','Templates Cache',NULL);
INSERT INTO `xt_clean_cache` (`id`,`type`,`type_desc`,`last_run`) VALUES (7, 'cache_seo','SEO Optimization',NULL);
INSERT INTO `xt_clean_cache` (`id`,`type`,`type_desc`,`last_run`) VALUES (8, 'adodb_logsql','DB adodb_logsql',NULL);
INSERT INTO `xt_clean_cache` (`id`,`type`,`type_desc`,`last_run`) VALUES (9, 'system_log_cronjob','DB system_log cronjob',NULL);
INSERT INTO `xt_clean_cache` (`id`,`type`,`type_desc`,`last_run`) VALUES (10,'system_log_xt_export','DB system_log xt_export',NULL);
INSERT INTO `xt_clean_cache` (`id`,`type`,`type_desc`,`last_run`) VALUES (11,'system_log_xt_im_export','DB system_log xt_im_export',NULL);
INSERT INTO `xt_clean_cache` (`id`,`type`,`type_desc`,`last_run`) VALUES (12,'system_log_email','DB system_log email',NULL);
INSERT INTO `xt_clean_cache` (`id`,`type`,`type_desc`,`last_run`) VALUES (13,'system_log_ImageProcessing','DB system_log ImageProcessing',NULL);
INSERT INTO `xt_clean_cache` (`id`,`type`,`type_desc`,`last_run`) VALUES (14,'clean_cache_logs','DB clean_cache_log',NULL);
